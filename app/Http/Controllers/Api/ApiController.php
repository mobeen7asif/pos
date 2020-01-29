<?php

namespace App\Http\Controllers\api;

use App\Ad;
use Mail;
use Swift_SmtpTransport;
use Swift_Mailer;
use App\Categories;
use App\Category_products;
use App\Company;
use App\Currency;
use App\Customer;
use App\CustomerCard;
use App\CustomerSession;
use App\Discount;
use App\DiscountBogo;
use App\DiscountCategory;
use App\DiscountProduct;
use App\Email_template;
use App\Floor;
use App\FloorTable;
use App\Libs\CustomerAuth;
use App\MealType;
use App\Order;
use App\OrderPayment;
use App\Product;
use App\ProductOrder;
use App\Role;
use App\Shifts;
use App\Stock;
use App\Store;
use App\Store_products;
use App\StoreCustomer;
use App\Sync;
use App\Tax_rates;
use App\User_log;
use App\UserDevice;
use Carbon\CarbonPeriod;
use DateTime;
use function foo\func;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Helpers\LogActivity;
use App\Libs;
use App\User;
use App\Country;
use Illuminate\Support\Facades\View;
use Ingenico\Connect\Sdk\DeclinedPaymentException;
use function PHPSTORM_META\map;
use Response;
use Carbon\Carbon;
use Image;
use File;
use App\Attendance_status;

use Ingenico\Connect\Sdk\ApiException;

use Ingenico\Connect\Sdk\ValidationException;
use Ingenico\Connect\Sdk\Domain\Definitions\Address;
use Ingenico\Connect\Sdk\Domain\Definitions\AmountOfMoney;
use Ingenico\Connect\Sdk\Domain\Definitions\Card;

use Ingenico\Connect\Sdk\Domain\Payment\CreatePaymentRequest;
use Ingenico\Connect\Sdk\Domain\Payment\CreatePaymentResponse;

use Ingenico\Connect\Sdk\Domain\Payment\Definitions\CardPaymentMethodSpecificInput;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\ContactDetails;
use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Customer as CardCustomer;

use Ingenico\Connect\Sdk\Domain\Payment\Definitions\Order as PaymentOrder;

use Ingenico\Connect\Sdk\DefaultConnection;
use Ingenico\Connect\Sdk\CommunicatorConfiguration;
use Ingenico\Connect\Sdk\Communicator;
use Ingenico\Connect\Sdk\Client;


use Ingenico\Connect\Sdk\Domain\Token\CreateTokenRequest;
use Ingenico\Connect\Sdk\Domain\Token\Definitions\CustomerToken;



use Ingenico\Connect\Sdk\Domain\Definitions\CardWithoutCvv;
use Ingenico\Connect\Sdk\Domain\Token\Definitions\TokenCardData;
use Ingenico\Connect\Sdk\Domain\Token\Definitions\TokenCard;


class ApiController extends Controller
{
    public $successStatus = 200;
    public $badRequestStatus = 400;
    public $errorStatus = 401;
    public $notFoundStatus = 404;

    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        $input = $request->all();

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }

        $userdata['email'] = $input['email'];

        $user = User::firstOrNew($userdata);

        $user->name = $input['name'];
        $user->password = bcrypt($input['password']);
        $user->save();

        $response['user'] =  $user;
        $response['token'] =  $user->createToken('Skulocity')->accessToken;
        $response['success'] =  'You have successfully register.';

        return response()->json(['result'=>$response], $this->successStatus);
    }

    /**
     * login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'pin_code' => 'required',
            //'device_type' => 'required',
            //'device_token' => 'required'
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }

        $input = $request->all();
        $user = User::where(['pin_code' => $input['pin_code']])->first();
        if($user){
            Auth::login($user);

        //if(Auth::attempt(['email' => $input['email'], 'password' => $input['password']])){
            $user = Auth::user();
            $user->last_online_time = date("Y-m-d H:i:s");
            $user->update();

            if($user->status==0){
                $response['error'] = "You account is blocked please contact with store management.";
                return response()->json(['result'=>$response], $this->badRequestStatus);
            }

            LogActivity::addToUserLog($user->id,'success_login','Success Login');

            $response['user'] =  $this->getUserMap($user);

            $response['token'] =  $user->createToken('Skulocity')->accessToken;
            $response['success'] =  'You have successfully login.';

            $status = $this->successStatus;
           // $check_token = $this->registerDeviceToken($user,$request->all());

            return response()->json(['result'=>$response], $status);
        }
        else{
            LogActivity::addToUserLog($input['email'],'login_failed','Login Failed');
            //LogActivity::addToUserLog($input['email'],'login_failed','Login Failed');

            $response['error'] = "You have entered an invalid pin";
            $status = $this->errorStatus;

            return response()->json(['result'=>$response], $status);
        }

    }

    public function companyLogin(Request $request){
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }
        $input = $request->all();

        if(Auth::attempt(['email' => $input['email'], 'password' => $input['password']])){
            $store_admin = Auth::user();
            $store_admin->is_login = 1;
            $store_admin->save();
            $company = $store_admin->store->company;
            $model_has_role = DB::table('model_has_roles')->where(['model_id' => $store_admin->id,'model_type' => 'App\User'])->first();
            if($model_has_role){
                $role = DB::table('roles')->where('id',$model_has_role->role_id)->first();
                if($role->name == 'Store Admin'){
                    $employees = User::where('store_id',$store_admin->store_id)->get();
                    $employees->map(function ($employee) use($company) {
                        $model_role = DB::table('model_has_roles')->where('model_id',$employee->id)->where('model_type','App\User')->first();
                        if($model_role){
                            $role = DB::table('roles')->find($model_role->role_id);
                            if($role){
                                $employee->role = $role->name;
                            }
                        }

                        $employee->company_id = $company->id;
                        $employee->token = $employee->createToken('Skulocity')->accessToken;
                    });
                    $this->registerDeviceToken($store_admin,$request->all());
                    $response['employees'] =  $employees;
                    $company = $store_admin->store->company;
                    $response['ads'] = $company->ads;
                    $response['currency'] = $store_admin->store->currency;
                    $response['company_type'] = $company->company_type;
                    return response()->json(['result'=>$response], $this->successStatus);
                } else {
                    $response['error'] = "You are not authorize to login";
                    return response()->json(['result'=>$response], $this->errorStatus);
                }
            }
        } else {
            $response['error'] = "You have entered an invalid email or password";
            return response()->json(['result'=>$response], $this->errorStatus);
        }
//        $company = Company::where('email',$input['email'])->first();
//        if($company) {
//            $store_ids = Store::where('company_id',$company->id)->pluck('id');
//            if (Hash::check($input['password'], $company->password)) {
//                $employees = User::whereIn('store_id',$store_ids)->get();
//                $employees->map(function ($employee) use($company) {
//                    $model_role = DB::table('model_has_roles')->where('model_id',$employee->id)->where('model_type','App\User')->first();
//                    $role = Role::find($model_role->role_id);
//                    if($role){
//                        $employee->role = $role->name;
//                    }
//
//                    $employee->company_id = $company->id;
//                    $employee->token = $employee->createToken('Skulocity')->accessToken;
//                });
//                $response['employees'] =  $employees;
//                return response()->json(['result'=>$response], $this->successStatus);
//            }
//            else {
//                $response['error'] = "You have entered an invalid email or password";
//                return response()->json(['result'=>$response], $this->errorStatus);
//            }
//        } else {
//            $response['error'] = "You have entered an invalid email or password";
//            return response()->json(['result'=>$response], $this->errorStatus);
//        }
    }


    public function registerDeviceToken($user,$input){
        $check_device = UserDevice::where(['device_id' => $input['device_id'], 'user_type' => 'worker'])->first();
        if(!$check_device){
            if(isset($input['device_token']) && isset($input['device_type']) && isset($input['device_id'])){
                $user_device =  UserDevice::create([
                    'device_token' => $input['device_token'],
                    'device_type' => $input['device_type'],
                    'user_id' => $user->id,
                    'user_type' => 'worker',
                    'device_id' => $input['device_id']
                ]);
            }

        }
        else {
            if(isset($input['device_token'])){
                $check_device->device_token = $input['device_token'];
                $check_device->user_id = $user->id;
                $check_device->save();
//                $arr['device_token']= $input['device_token'];
//                $arr['updated_at']= date('Y-m-d H:i:s');
//                $check_device->update($arr);
            }

        }
    }

    /**
     * getProductsMap function
     *
     * @param  int  $user
     *
     * @return \Illuminate\Http\Response
     */

    private function getUserMap($user){


        $user_data = User::with(
            [
                'store.company.company_setting',
                'store.duty_setting',
//                'store.company.customers' => function ($query) {
//                    //$query->where('store_id', 0);
//                },
                'store.company.store',
                'store.company.currencies',
                'store.company.tax_rates',
                'store.company.shipping_options' => function ($query) {
                    //$query->orderBy('price','asc');
                },
                'store.company.customer_groups'
            ])->find($user->id);

        //$user_data['profile_thumbnail'] = checkImage('users/thumbs/'. $user->profile_image);
        $user_data['profile_image'] = checkImage('users/'. $user->profile_image);
        $user_data['company_type'] = $user->store->company->company_type;
        $user_data->store->company->stores = $user_data->store->company->store;
        if($user->store->background_image == null){
            $user_data->store->background_image = null;
        } else {
            $image_name = 'stores/'.$user->store->background_image;

            if (\File::exists(public_path('uploads/'.$image_name))){
                $store_background_image = asset('uploads/'.$image_name);
                $user_data->store->background_image = $store_background_image;
            }else {
                $user_data->store->background_image = null;
            }
        }
        if($user->store->receipt_logo == 'no_picture.jpg'){
            $user_data->store->receipt_logo = null;
        } else {
            $user_data->store->receipt_logo = checkImage('stores/receipt_logos/thumbs/'. $user->store->receipt_logo);
        }



        $store = $user->store;

        if($user->store->company->company_type == 1){
            $user_data['company_type'] = 1;
            $user_data['meal_types'] = MealType::where('store_id',$user->store_id)->get();
            $user_data['floors'] = Floor::where('store_id',$user->store_id)->get();
            $role = DB::table('roles')->where('name','Waiter')->where('company_id',$user->store->company->id)->first();
            $waiter_ids = DB::table('model_has_roles')->select('model_id')->where('role_id',$role->id)->get();
            $waiter_ids_array = [];
            foreach($waiter_ids as $id){
                $waiter_ids_array[] = $id->model_id;
            }
            $user_data['waiters'] = User::where('store_id',$user->store_id)->whereIn('id',$waiter_ids_array)->get();

            //getting floor tables
            $floor_ids = Floor::where('store_id',$store->id)->pluck('id');
            if($floor_ids){
                $floor_tables = FloorTable::whereIn('floor_id',$floor_ids)->get();
                $user_data['tables'] = $floor_tables;
            } else {
                $user_data['tables'] = null;
            }



        }

        $discount_categories = DiscountCategory::with('discount')->where('store_id',$store->id)->get();
        $user_data['discounts'] = $discount_categories;

//        $discount_products = DiscountProduct::with('discount')->where('store_id',$store->id)->get();
//        $user_data['discount_products'] = $discount_products;

        $discount_products = DiscountProduct::with('discount')->where('store_id',$store->id)->get();
        $user_data['discount_products'] = $discount_products;

        $discount_bogo = Discount::with('discountBogo')->where('store_id',$store->id)->get();
        $bogo_discount_array = [];
        foreach($discount_bogo as $bogo) {
            if(count($bogo->discountBogo) > 0){
                $bogo_discount_array[] = $bogo;
            }
        }
        $user_data['discount_bogo'] = $bogo_discount_array;





        $user_data->store->company->currencies->map( function ($currency) {

            $currency->default = ($currency->id == companySettingValueApi('currency_id') ? true : false);

            return $currency;
        });

        $user_data->store->company->tax_rates->map( function ($tax_rate) {

            $tax_rate->default = ($tax_rate->id == companySettingValueApi('tax_id') ? true : false);

            return $tax_rate;
        });

        $user_data->store->company->shipping_options->map( function ($shipping_option) {

            $shipping_option->default = ($shipping_option->id == companySettingValueApi('shipping_id') ? true : false);

            return $shipping_option;
        });

        unset($user_data->store->company->store);
        // return $products->all();

        //getting tax from store
        $store = $user_data->store;
        $store_tax = Tax_rates::find($store->tax_id);
        $user_data['store']['company']->tax_rates->map(function($tax) use ($store_tax) {
            if($tax->default == true){
                if(isset($store_tax)){
                    $tax->id = $store_tax->id;
                    $tax->company_id = $store_tax->company_id;
                    $tax->code = $store_tax->code;
                    $tax->name = $store_tax->name;
                    $tax->default = true;


                    //$tax = $store_tax;

                }

            }
            return $tax;
        });

        //dd($user_data->store->company->name);
        $store_customer_ids = StoreCustomer::where('store_id',$store->id)->pluck('customer_id');
        $customers = Customer::whereIn('id',$store_customer_ids)->get();
        $user_data->store->company->customers = $customers;
        return $user_data;
    }

    /**
     * profileDetails api
     *
     * @return \Illuminate\Http\Response
     */
    public function profileDetails()
    {
        $user = Auth::user();

        if ($user) {
            $user['profile_thumbnail'] = checkImage('users/thumbs/'. $user->profile_image);
            $user['profile_image'] = checkImage('users/'. $user->profile_image);
            $response['user'] = $user;
            $status = $this->successStatus;
        }else{
            $response['error'] = "Profile details not exist against this user";
            $status = $this->errorStatus;
        }

        return response()->json(['result' => $response], $status);
    }

    /**
     * updateProfile api
     *
     * @return \Illuminate\Http\Response
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validator = Validator::make($request->all(), [
            'name'          => 'required',
            'email'         => "email|unique:users,email,$user->id" ,
            'dob'    => 'required',
            'pin'     => 'digits:6',
            'blood_group'     => 'required',
            'profile_status'     => 'required',
        ]);
        //validate if user does not have image

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->errorStatus);
        }

        $input = $request->all();

        $record = User::findOrFail($user->id);

        $record->update($input);

        /*-----image manipulation-----*/
        if ($request->hasFile('profile_image'))
        {
            //validate if user has image
            $validator = Validator::make($request->all(), [
                'profile_image' => 'image|mimes:jpeg,png,jpg,gif'
            ]);
            if ($validator->fails()) {
                $response['error'] = $validator->errors();
                return response()->json(['result'=>$response], $this->errorStatus);
            }

            /*image make process*/
            $image = $request->file('profile_image');
            $image_name   = $record->id.'_'.str_random(10).'.'.$image->getClientOriginalExtension();

            $destinationPath = public_path('/uploads/users/thumbs');
            $img = Image::make($image->getRealPath());

            /*move image to thumbs folder*/
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$image_name);

            /*move image to folder*/
            $destinationPath = public_path('/uploads/users/');
            $image->move($destinationPath, $image_name);

            /*unlink old image*/
            @unlink(public_path("/uploads/users/$user->profile_image"));
            @unlink(public_path("/uploads/users/thumbs/$user->profile_image"));

            // save image to db
            $record->profile_image = $image_name;
            $record->save();
        }

        $response['success']    =  'Profile updated successfully';
        return response()->json(['result'=>$response], $this->successStatus);
    }

    /**
     * changePassword api
     *
     * @return \Illuminate\Http\Response
     */

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:6'
        ]);

        if ($validator->fails()){
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->errorStatus);
        }

        $user = Auth::guard('api')->user();

        //checking the old password first
        $check  = Auth::guard('web')->attempt([
            'email' => $user->email,
            'password' => $request->current_password
        ]);

        if($check) {
            $user->password = bcrypt($request->password);
            $user->token()->revoke();
            $token = $user->createToken('Bat App')->accessToken;

            //Changing the type
            $user->save();

            $response['user'] = $user;
            $response['token'] = $token;

            return response()->json(['result'=>$response], $this->successStatus);

        }else{

            $response['error']['current_password'] = 'Your current password is incorrect, please try again.';
            return response()->json(['result'=>$response], $this->errorStatus);

        }
    }

    /**
     * logout api
     *
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {

        if (Auth::check()) {

            Auth::user()->AauthAcessToken()->delete();
            $response['success'] = "You have successfully logout";
            $status = $this->successStatus;
        }else{
            $response['error'] = "Oops! some error occur not successfully logout. Please try again";
        }

        return response()->json(['result' => $response], $status);
    }
    public function adminLogout(Request $request){
        $validator = Validator::make($request->all(), [
            'user_id' => 'required',
            'device_token' => 'required',
            'device_id' => 'required'
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }
        UserDevice::where(['device_id' => $request->device_id])->delete();
        $response['success'] = "You have successfully logout";
        $status = $this->successStatus;
        return response()->json(['result' => $response], $status);
    }

    public function loginSync(Request $request){
        $logouts = json_decode($request->logouts);
        if(isset($logouts)){
            foreach($logouts as $logout){
                $logout = json_decode($logout);
                if($logout->type == 'login'){
                    $input['device_id'] = $logout->device_id;
                    $input['device_type'] = $logout->device_type;
                    $input['device_token'] = $logout->device_token;
                    $user = User::where('email',$logout->email)->first();
                    if($user){
                        $this->registerDeviceToken($user,$input);
                    }
                }
                else {
                    UserDevice::where(['device_id' => $logout->device_id])->delete();
                }
            }
        }
        $response['success'] = "Data synced successfully";
        $status = $this->successStatus;
        return response()->json(['result' => $response], $status);

    }

    /**
     * Forget password
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|exists:users'
        ],[
            'email.exists' => "We can't find a user with that e-mail address."
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }

        $headers =  'MIME-Version: 1.0' . "\r\n";
        $headers .= 'From: Your name <info@address.com>' . "\r\n";
        $headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

        mail($request->email,"Forget Password",'Forget Password',$headers);

        $response['success'] = 'Email successfully sent.';
        return response()->json(['result'=>$response], $this->successStatus);
    }



    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginWithPin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'pin_code' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }

        $input = $request->all();
        //$user_id = Auth::id();
        $user = User::where('pin_code',$input['pin_code'])->first();
        $user_id = $user->id;
        if($user){

            $user_id = $user->id;

            $user->last_online_time = date("Y-m-d H:i:s");

            $user->update();


            if(!empty($input['timestamp'])){
                $attData['attendance_time'] = $input['attendance_time'];
            }else{
                $attData['attendance_time'] = date("Y-m-d H:i:s");
            }

            $attData['user_id'] = $user_id;
            $attData['date'] = date('Y-m-d');
            $attData['status'] = 1;

            if(isset($input['type']) && ($input['type'] == 1)){
                Attendance_status::create($attData);

//                $attendance = Attendance_status::firstOrNew($attData);
//
//                $attendance->save();

                LogActivity::addToUserLog($user_id,'checkin_success','Success Check In');
                $user = User::find($user_id);
                $user->is_login = 1;
                $user->save();
                $check_token = $this->registerDeviceToken($user,$request->all());
                $status = $this->successStatus;
                $response['success'] =  'You have successfully check in.';

            }
            else if(isset($input['type']) && ($input['type'] == 2)){

                $attData['status'] = 2;
                Attendance_status::create($attData);

//                $check_in_status = Attendance_status::where($attData)->first();
//
//                if($check_in_status){
//                    $attData['status'] = 2;
//
//                    $attendance = Attendance_status::firstOrNew($attData);
//                    $attendance->save();
//

                //$this->logout();
                $user = User::find($user_id);
                $user->is_login = 0;
                $user->save();
                //delete device_token against this user
                UserDevice::where(['user_id' => $user->id, 'user_type' => 'worker'])->delete();

                LogActivity::addToUserLog($user_id,'checkout_success','Success Check Out');

                $user_check_in_time = User_log::where(['user_id' => $user_id, 'name' => 'checkin_success'])->orderBy('created_at','desc')->first();
                $last_checkout = User_log::where(['user_id' => $user_id, 'name' => 'checkout_success'])->orderBy('created_at','desc')->first();
                if($user_check_in_time){
                    $to_time = strtotime($last_checkout->created_at);
                    $from_time = strtotime($user_check_in_time->created_at);
                    $time_diff = round(abs($to_time - $from_time) / 60);
                } else {
                    $time_diff = 1;
                }




                $last_checkout->session_time = $time_diff;
                $last_checkout->save();

                $status = $this->successStatus;
                $response['success'] =  'You have successfully check out.';
//                }else{
//
//                    LogActivity::addToUserLog($user_id,'checkout_failed','Check Out Failed');
//
//                    $status = $this->errorStatus;
//                    $response['success'] =  'Please check in first.';
//                }
            }else{

                LogActivity::addToUserLog($user_id,'login_success_with_pin','Success Login with Pin Code');

                $status = $this->successStatus;
                $response['success'] =  'You have successfully login.';
            }



            return response()->json(['result'=>$response], $status);
        }
        else{

            LogActivity::addToUserLog(0,'login_failed_with_pin','Login Failed! Invalid pin code');

            $response['error'] = "You have entered an invalid pin code";

            return response()->json(['result'=>$response], $this->errorStatus);
        }
    }

    /**
     * Check internet status
     *
     * @return JSON
     */
    public function pingServer()
    {
        $user = Auth::user();
        $user->last_online_time = date("Y-m-d H:i:s");
        $user->update();

        $response['timestamp'] = date("d-m-Y h:i a");

        return response()->json(['result'=>$response], $this->successStatus);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function loginMultiWithPin(Request $request)
    {
        if(isset($request->attedence_data)){
            $attedence_data = json_decode($request->attedence_data,true);

            foreach($attedence_data as $attedence)
            {
                $user_id = $attedence['user_id'];
                $user = User::find($user_id);

                if($user){

                    $user->last_online_time = $attedence['attendance_time'];
                    $user->update();

                    $attData['created_at'] = $attedence['attendance_time'];
                    $attData['updated_at'] = $attedence['attendance_time'];
                    $attData['user_id'] = $user_id;
                    $attData['date'] = date('Y-m-d',strtotime($attedence['attendance_time']));
                    $attData['status'] = 1;

                    if(isset($attedence['type']) && ($attedence['type'] == 1)){
                        $user = User::find($user_id);
                        $user->is_login = 1;
                        $user->save();

                        $attendance = Attendance_status::create($attData);
                        LogActivity::addToUserLog($user_id,'checkin_success','Success Check In');
                        //updateSyncData('clock_in',$attendance->id);

                    }elseif(isset($attedence['type']) && ($attedence['type'] == 2)){
                        $user = User::find($user_id);
                        $user->is_login = 0;
                        $user->save();
                        $attData['status'] = 2;
                        $attendance = Attendance_status::create($attData);

                        LogActivity::addToUserLog($user_id,'checkout_success','Success Check Out');
                        //updateSyncData('clock_in',$attendance->id);
                        //add check out time


                        $user_check_in_time = User_log::where(['user_id' => $user_id, 'name' => 'checkin_success'])->orderBy('created_at','desc')->first();
                        $last_checkout = User_log::where(['user_id' => $user_id, 'name' => 'checkout_success'])->orderBy('created_at','desc')->first();
                        if($user_check_in_time){
                            $to_time = strtotime($last_checkout->created_at);
                            $from_time = strtotime($user_check_in_time->created_at);
                            $time_diff = round(abs($to_time - $from_time) / 60);
                        }
                        else {
                            $time_diff = 1;
                        }
                        $last_checkout->session_time = $time_diff;
                        $last_checkout->save();



                    }else{
                        LogActivity::addToUserLog($user_id,'login_success_with_pin','Success Login with Pin Code');
                    }

                }
            }
        }


        $response['timestamp'] = date("d-m-Y h:i a");

        return response()->json(['result'=>$response], $this->successStatus);
    }

    function getDiscounts(Request $request){
        $store = Auth::user()->store;
        $discount_categories = DiscountCategory::with('discount')->where('store_id',$store->id)->get();
        return response()->json(['result'=>$discount_categories], $this->successStatus);
    }
    function getAds(Request $request){

//        $headers = getallheaders();
//        dd($headers['Authorization']);
        $company = Auth::user()->store->company;
        $ads = Ad::where('company_id',$company->id)->orderBy('updated_at','desc')->get();
        return response()->json(['result'=>$ads], $this->successStatus);
    }


    //customer functions
    public function customerRegister(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|string',
            'last_name' => 'required|string',
            'email' => 'required|string|email|max:255|unique:customers',
            'password' => 'required|string|min:6',
            'device_id' => 'required',
            'device_type' => 'required',

        ]);

        $input = $request->all();
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }
        $userdata['password'] = bcrypt($input['password']);
        //$timestamp = strtotime(date('Y-m-d H:i:s'));
        $timestamp = randomString();
        $customer = new Customer();
        $customer->id = $timestamp;
        $customer->first_name = $input['first_name'];
        $customer->last_name = $input['last_name'];
        $customer->email = $input['email'];
        $customer->profile_image = 'user_placeholder.png';
        $customer->mobile = $request->input('mobile');
        $customer->dob = null;
        $customer->password = bcrypt($input['password']);
        $customer->save();
        //$customer = Customer::create($userdata);
        $customer_session = $this->saveCustomerSession($customer,$input);
        //saving data to customer session

        $response['customer'] =  Customer::find($customer->id);
        $response['token'] =  $customer_session->session_token;
        $response['success'] =  'You have successfully registered.';

        updateSyncData('customer',$customer->id);

        return response()->json(['result'=>$response], $this->successStatus);
    }

    public function customerLogin(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|string|email',
            'password' => 'required',
            'device_id' => 'required',
            'device_type' => 'required',
        ]);

        $input = $request->all();

        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }
        if(CustomerAuth::attempt($input)){
            $customer = Customer::where('email',$input['email'])->first();
            $customer_session = $this->saveCustomerSession($customer,$input);
            $response['customer'] =  $customer;
            $response['token'] =  $customer_session->session_token;
            $response['success'] =  'You have successfully login.';
            return response()->json(['result'=>$response], $this->successStatus);
        } else {
            $response['error'] = "You have entered an invalid email or password";
            $status = $this->errorStatus;

            return response()->json(['result'=>$response], $status);
        }
    }

    public function customerChangePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required|min:6'
        ]);

        if ($validator->fails()){
            $response['error'] = $validator->errors()->first();
            return response()->json(['result'=>$response], $this->errorStatus);
        } else {
            $new_pass = $request->input('new_password');
            $old_pass = $request->input('old_password');
            $customer = CustomerAuth::customer();
            if(Hash::check($old_pass,$customer->password)){
                Customer::where(['id' => $customer->id])->update(['password' => bcrypt($new_pass)]);

                $response['user'] = $customer;
                $response['token'] = $customer->session_token;
                return response()->json(['result'=>$response], $this->successStatus);
            } else {
                $response['error'] = 'Your old password is incorrect, please try again.';
                return response()->json(['result' => $response], $this->errorStatus);
            }
        }
    }

    public function updateCustomerProfile(Request $request)
    {
        //Log::info('here');
        $customer = CustomerAuth::customer();
//        $validator = Validator::make($request->all(), [
//            //'first_name' => 'required|max:255',
//            //'last_name' => 'required|max:255',
//            //'email' => "required|email|max:255|unique:customers,email,$customer->id",
//            //'country_id' => 'required',
//        ]);
//
//        if ($validator->fails()) {
//            $response['error'] = $validator->errors();
//            return response()->json(['result'=>$response], $this->badRequestStatus);
//        }
        $input = $request->all();

        $attrs = $this->purify($input);

        $record = Customer::findOrFail($customer->id);

        $record->update($attrs);

        /*-----image manipulation-----*/
        if ($request->hasFile('profile_image'))
        {
            //validate if user has image
            $validator = Validator::make($request->all(), [
                'profile_image' => 'image|mimes:jpeg,png,jpg,gif'
            ]);
            if ($validator->fails()) {
                $response['error'] = $validator->errors()->first();
                return response()->json(['result'=>$response], $this->errorStatus);
            }

            /*image make process*/
            $image = $request->file('profile_image');
            $image_name   = $record->id.'_'.str_random(10).'.'.$image->getClientOriginalExtension();

            $destinationPath = public_path('/uploads/customers/thumbs');
            $img = Image::make($image->getRealPath());

            /*move image to thumbs folder*/
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$image_name);

            /*move image to folder*/
            $destinationPath = public_path('/uploads/customers/');
            $image->move($destinationPath, $image_name);

            /*unlink old image*/
            @unlink(public_path("/uploads/users/$customer->profile_image"));
            @unlink(public_path("/uploads/users/thumbs/$customer->profile_image"));

            // save image to db
            $record->profile_image = $image_name;
            $record->save();
        }
        $customer = CustomerAuth::customer();
        //$customer->profile_image = checkImage('customers/'. $customer->profile_image);
        $response['success']    =  'Profile updated successfully';
        $response['customer'] = $customer;
        return response()->json(['result'=>$response], $this->successStatus);
    }

    function forgotPassword(Request $request) {

        $customer = Customer::where('email',$request->input('email'))->first();
        if ($customer != null) {
            $token = substr(uniqid(), 6, 6);
            $data['passwordToken'] = $token;
            $customer->token = $token;
            $customer->save();
            $code = url('api/reset_password').'/'.$token;
            $url_link       =   "<a href=" . $code . " >Click Here</a>";
            $email_data = Email_template::where('name','Forgot Password')->first();
            $email_to            = $customer->email;
            $email_from            = $email_data->from;
            $email_subject            = $email_data->subject;
            $email_body          = $email_data->content;
            $email_body = str_replace('{user_name}',$customer->first_name.' '.$customer->last_name,$email_body);
            $email_body = str_replace('{site_name}',settingValue('site_title'),$email_body);
            $email_body = str_replace('{reset_password_link}',$url_link,$email_body);
            $body = $email_body;
            //find customer store
            $store = StoreCustomer::where('customer_id',$customer->id)->first();
            if($store){
                Email_template::sendEmail($email_to,$email_data,$body,$store->id);
            }




            return response()->json(['message' => 'Email is sent your account'], $this->successStatus);
        }
        $response['error'] = 'This email does not exist';
        return response()->json(['result'=>$response], 404);
    }
    public function updatePassword(Request $request) {
        $this->validate($request,[
            'password' => 'required|confirmed'
        ]);
        $token = $request->input('token');
        Customer::where('token',$token)->update(['password' => bcrypt($request->input('password'))]);
        Customer::where('token',$token)->update(['token' => '']);
        return View::make('customer_password.password_change_verification_view');
    }
    public function getStoreCategories(Request $request){
        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }
        $categories = Categories::with('subcategories')->where('store_id',$request->input('store_id'))->get();
        $response['categories'] = $categories;
        $store = Store::find($request->input('store_id'));
        $response['store'] = $store;
        $company = $store->company;
        $currency = $store->company->currencies->map( function ($currency) use ($company) {

            $currency->default = ($currency->id == companySettingValueApi('currency_id',$company->id) ? true : false);

            return $currency;
        });
        $response['store']['currency'] = $currency[0];
        return response()->json(['result'=>$response], $this->successStatus);
    }
    public function getCategoryProducts(Request $request){
        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
            'category_id' => 'required'
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }
        //$user = User::with(['store.company'])->find(Auth::id());

        //$company_id = $user->store->company->id;
        $store_id = $request->input('store_id');
        $category_id = $request->input('category_id');
        $product_ids = Category_products::where('category_id',$category_id)->pluck('product_id');

        $products = Product::with(
            [
                'company',
                'tax_rate',
                'product_combos.product.product_images',
                'store_products' => function ($query) use ($store_id) {
                    $query->where('store_id', $store_id);
                },
                'category_products.category',
                'product_images',
                'product_tags',
                'product_attributes.variant',
                'product_modifiers.modifier',
                'products.tax_rate',
                'products.product_tags',
                'products.store_products' => function ($query) use ($store_id) {
                    $query->where('store_id', $store_id);
                },
                'products.product_images',
                'products.product_variants.product_attribute.variant'
            ])->whereIn('id',$product_ids);

        $products = $products->get();

        $response['products'] = $this->getProductsMap($products);


        $status = $this->successStatus;

        return response()->json(['result' => $response], $status);
    }

    public function productDetail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $product_id = $request->input('product_id');
        $products = Product::with(
            [
                'company',
                'tax_rate',
                'product_combos.product.product_images',
                'store_products' => function ($query) use ($product_id) {
                    $query->where('product_id', $product_id);
                },
                'category_products.category',
                'product_images',
                'product_tags',
                'product_attributes.variant',
                'product_modifiers.modifier',
                'products.tax_rate',
                'products.product_tags',
                'products.store_products' => function ($query) use ($product_id) {
                    $query->where('product_id', $product_id);
                },
                'products.product_images',
                'products.product_variants.product_attribute.variant'
            ])->where('id', $product_id);

        $products = $products->get();
        $products = $this->getProductsMap($products);
        $response['product'] = $products[0];
        $status = $this->successStatus;
        return response()->json(['result' => $response], $status);
    }
    public function productDetailBarcode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $product = Product::where('code',$request->input('code'))->first();
        if($product){
            $product_id = $product->id;
            $products = Product::with(
                [
                    'company',
                    'tax_rate',
                    'product_combos.product.product_images',
                    'store_products' => function ($query) use ($product_id) {
                        $query->where('product_id', $product_id);
                    },
                    'category_products.category',
                    'product_images',
                    'product_tags',
                    'product_attributes.variant',
                    'product_modifiers.modifier',
                    'products.tax_rate',
                    'products.product_tags',
                    'products.store_products' => function ($query) use ($product_id) {
                        $query->where('product_id', $product_id);
                    },
                    'products.product_images',
                    'products.product_variants.product_attribute.variant'
                ])->where('id', $product_id);

            $products = $products->get();
            $products = $this->getProductsMap($products);
            $response['product'] = $products[0];
            $status = $this->successStatus;
            return response()->json(['result' => $response], $status);
        }
        return response()->json(['message' => 'No product found'], $this->successStatus);

    }
    public function getOrders(Request $request){
        $validator = Validator::make($request->all(), [
            'order_status' => 'required',
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $order_status = $request->input('order_status');
        $customer = CustomerAuth::customer();
        $orders = Order::with(['store'])->where(['customer' => $customer->id,'order_status' => $order_status])->orderBy('updated_at','desc')->get();
        if($order_status == 0){
            $orders = Order::with(['store'])->where(['customer' => $customer->id])
                ->where(function ($query){
                    $query->where('order_status',0);
                    $query->orWhere('order_status',2);
                    $query->orWhere('order_status',3);
                })->orderBy('updated_at','desc')->get();
        }
        $orders->map(function ($order) {
            $order_payment = OrderPayment::where('order_id',$order->id)->first();
            if($order_payment){
                $order->card = $order_payment->card_name;
            } else {
                $order->card = '';
            }

            return $order;
        });
        $response['orders'] = $orders;

        $status = $this->successStatus;
        return response()->json(['result' => $response], $status);
    }
    public function getOrderDetail(Request $request){
        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $id = $request->input('order_id');
        $order = Order::with(['store'])->where('id',$id)->first();

        $store = Store::find($order->store_id);
        $company = $store->company;
        $order->store->email = $company->email;
        $order->store->phone = $company->phone;

        $response['order'] = $order;
        $response['order_payment'] = OrderPayment::where('order_id',$order->id)->first();
//        $card = CustomerCard::where('id',CustomerAuth::customer()->id)->where('is_default',1)->first();
//        if($card == null){
//            $card = CustomerCard::where('customer_id',CustomerAuth::customer()->id)->first();
//        }
//        $response['card'] = $card->type;
        $status = $this->successStatus;
        return response()->json(['result' => $response], $status);
    }

    public function saveFloorTables(Request $request){
        $tables = json_decode($request->tables);
        if(isset($tables)) {
            foreach ($tables as $table) {
                $floor_table = FloorTable::where('table_id', $table->table_id)->first();

                if($table->delete == 1){
                    $delete_status = FloorTable::where('table_id', $table->table_id)->delete();
                    //dd($delete_status);
                }
                else
                {
                    if ($floor_table == null) {
                        $floor_table = new FloorTable();
                    }
                    $floor_table->name = $table->name;
                    $floor_table->waiter_id = $table->waiter_id;
                    $floor_table->floor_id = $table->floor_id;
                    $floor_table->table_id = $table->table_id;
                    $floor_table->x1y1 = $table->x1y1;
                    $floor_table->x2y2 = $table->x2y2;
                    $floor_table->book_status = $table->book_status;
                    $floor_table->seats = $table->seats;
                    $floor_table->table_image = $table->table_image;
                    $floor_table->tag_id = $table->tag_id;
                    $floor_table->table_number = isset($table->table_number) ? $table->table_number : '';
                    $floor_table->save();
                }

            }

            $status = $this->successStatus;
            return response()->json(['message' => 'Tables stored successfully'], $status);
        }
    }
    public function saveCard(Request $request){
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'expiry' => 'required',
            'number' => 'required',
            'is_default' => 'required',
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        //saving token
        $expiry = $request->input('expiry');
        $expiry = str_replace("/","",$expiry);
        $expiry = trim($expiry);
        $number = $request->input('number');
        $token = $this->createToken($number,$expiry);
        if($token == false){
            $response['error'] = 'This card number is invalid';
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $customer = CustomerAuth::customer();
        $default_card = $request->input('is_default');
        if($default_card == 1){
            CustomerCard::where('customer_id',$customer->id)->update(['is_default' => 0]);
        }
        $attrs = $request->all();
        $attrs['customer_id'] = $customer->id;

        $attrs['token'] = $token;
        $attrs['type'] = getCardBrand($request['number']);
        $attrs['number'] = substr($request->input('number'), -4);
        $card = CustomerCard::create($attrs);

        $status = $this->successStatus;

        return response()->json(['message' => 'Card is saved'], $status);
    }
    public function deleteCard(Request $request){
        $validator = Validator::make($request->all(), [
            'card_id' => 'required',
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        CustomerCard::where('id',$request->input('card_id'))->delete();
        $status = $this->successStatus;

        return response()->json(['message' => 'Card is deleted'], $status);
    }

    public function getCards(Request $request){

        $customer = CustomerAuth::customer();
        $cards = CustomerCard::where('customer_id',$customer->id)->get();
        $response['cards'] = $cards;
        $status = $this->successStatus;

        return response()->json(['result' => $response], $status);
    }
    public function bookTable(Request$request){
        $validator = Validator::make($request->all(), [
            'table_id' => 'required',
            'old_table_id' => 'required'
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $table_id  = $request->input('table_id');
        $old_table_id  = $request->input('old_table_id');
        if($old_table_id != '0'){
            $table = FloorTable::where(['table_id'=>$table_id,'book_status'=>1])->first();
            if($table){
                $response['error'] = 'This table is already booked';
                return response()->json(['result' => $response], $this->errorStatus);
            }
            else {
                FloorTable::where(['table_id' => $old_table_id])->update(['book_status' => 0]);
                $table = FloorTable::where(['table_id'=>$table_id])->first();
                $table->book_status = 1;
                $table->is_mobile_order = 1;
                $table->save();
                $store = $table->floor->store;
                updateSyncData('table',$table->table_id,$store->id);
                updateSyncData('table',$old_table_id,$store->id);
                $waiter = User::where('id',$table->waiter_id)->first();
                $floor = Floor::where('id',$table->floor_id)->first();
                $response['table'] = $table;
                $response['waiter'] = $waiter;
                $response['floor'] = $floor;
                return response()->json(['result' => $response], $this->successStatus);
            }
        } else
            {

            $table = FloorTable::where(['table_id'=>$request->input('table_id'),'book_status'=>1])->first();
            if($table){
                $response['error'] = 'This table is already booked';
                return response()->json(['result' => $response], $this->errorStatus);
            } else {
                $table = FloorTable::where(['table_id'=>$request->input('table_id')])->first();
                $table->book_status = 1;
                $table->is_mobile_order = 1;
                $table->save();
                $store = $table->floor->store;
                updateSyncData('table',$table->table_id,$store->id);

                $waiter = User::where('id',$table->waiter_id)->first();
                $floor = Floor::where('id',$table->floor_id)->first();
                $response['table'] = $table;
                $response['waiter'] = $waiter;
                $response['floor'] = $floor;

                return response()->json(['result' => $response], $this->successStatus);
            }

        }

    }

    public function registerCustomerDevice(Request $request){
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required',
            'app_mode' => 'required'
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $request_data = $request->all();
        $request_data['user_id'] = CustomerAuth::customer()->id;
        $request_data['user_type'] = 'customer';
        $user_device = UserDevice::where(['user_id' => CustomerAuth::customer()->id, 'device_token' => $request_data['device_token']])->first();
        if(!$user_device){
            UserDevice::create($request_data);
            return response()->json(['message' => 'Device registered successfully'], $this->successStatus);
        } else {
            $user_device->device_token = $request_data['device_token'];
            $user_device->updated_at = date('Y-m-d H:i:s');
            $user_device->save();
        }

        return response()->json(['message' => 'This device is already added against this customer'], $this->successStatus);

    }

    public function registerWorkerDevice(Request $request){
        $validator = Validator::make($request->all(), [
            'device_token' => 'required',
            'device_type' => 'required',
            'app_mode' => 'required'
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $request_data = $request->all();
        $request_data['user_id'] = Auth::user()->id;
        $request_data['user_type'] = 'worker';
        $user_device = UserDevice::where(['user_id' => Auth::user()->id, 'device_token' => $request_data['device_token']])->first();
        if(!$user_device){
            UserDevice::create($request_data);
            return response()->json(['message' => 'Device registered successfully'], $this->successStatus);
        }
        return response()->json(['message' => 'This device is already added against this worker'], $this->successStatus);
    }
    public function approveOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'order_status' => 'required',
            'reference_id' => 'required'
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $order_status = $request->input('order_status');
        $order = Order::with(['store'])->where('reference',$request->input('reference_id'))->first();
        if($order->customer_order_status == 'rejected'){
            $response['error'] = 'This order is already rejected';
            return response()->json(['result' => $response], $this->errorStatus);
        } else if($order->customer_order_status == 'accepted'){
            $response['error'] = 'This order is already accepted';
            return response()->json(['result' => $response], $this->errorStatus);
        } else if($order->customer_order_status == 'process' && $order_status == 'process') {
            $response['error'] = 'This order is already in process';
            return response()->json(['result' => $response], $this->errorStatus);
        } else if ($order->customer_order_status == 'completed'){
            $response['error'] = 'This order is already in completed';
            return response()->json(['result' => $response], $this->errorStatus);
        }
        if($order_status == 'rejected'){
            $body['data']['order_status'] = 'rejected';
            $title = 'Order rejected';
            $message  = 'Sorry! Your order has been Cancelled.';
            $order->order_status = 2;
            $order->customer_order_status = 'rejected';
            //free bokk table
            //table free in case of hospitality
            $table = json_decode($order->table_data);
            if(isset($table)){
                $table_id = $table->table_info->id;
                $company = $order->store->company;
                if($company->company_type == 1){
                    if(isset($table_id)){
                        FloorTable::where('table_id',$table_id)->update(['book_status' => 0,'order_id' => 0,'is_mobile_order' => 0]);
                        //sync table
                        updateSyncData('table',$table_id,Auth::user()->store->id);
                    }
                }
            }



            //manage stock
            $this->orderUpdateStockManage($order->id,$order->order_items,$order->customer,$order->store_id);
            //$order_id , $order_items , $user_id = 0, $store_id = 0
        }
        else if($order_status == 'process') {
            $body = [];
            $order->order_status = 3;
            $order->customer_order_status = 'process';
            $body['data']['order_status'] = 'process';
            $title = 'Order in process';
            $message  = 'You order is in process';

            $body['data']['order_id'] = $order->id;

        }
        else {
            $body['data']['order_status'] = 'accepted';
            $title = 'Order approved';
            $message  = 'You order is approved';
            $order->customer_order_status = 'accepted';

            if($order->store->company->company_type == 1){
                $title = 'Order completed';
                $message  = 'You order is completed';
            }
        }

        $order->biller_detail = Auth::user()->toJson();
        updateSyncData('order',$order->id);
        $order->save();
        if($order){
            $devices = UserDevice::where('user_id',$order->customer)->get();
            foreach ($devices as $device){
                if($device->device_type == 'ios'){
                    $body['aps']['alert'] =  array('body'=>$message,"title"=>$title);
                    $body['aps']['sound'] =  "default";
                    ///$body['aps']['badge'] =  $total;
                    $body['data']['reference_id'] =  $order->reference;
                    $body['data']['store_type'] =  ($order->store->company->company_type == 1) ? 'Hospitality' : 'Retailer';
                    //Log::info($body);
                    sendIOSNotification($body,$device);
                }
            }

            return response()->json(['message' => 'Notification send to customer'], $this->successStatus);
        }
    }

    public function cancelOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'reference' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);
        }
        $order = Order::where('reference',$request->input('reference'))->first();
        if($order){
            $order->order_status = 4;
            $order->save();
            $this->orderUpdateStockManage($order->id,$order->order_items);
            return response()->json(['message' => 'Order cancelled successfully'], $this->successStatus);
        } else {
            return response()->json(['message' => 'Order no found'], $this->errorStatus);
        }

    }

    public function updateTip(Request $request){
        $tips = $request->all();
        $order_ids = [];
        $order_id = 0;
        if(isset($tips)){
            foreach ($tips as $tip){
                $order_payment = OrderPayment::where('transaction_id',$tip['transaction_id'])->first();
                if($order_payment){
                    if(isset($tip['tip'])){
                        $order_payment->tip = $tip['tip'];
                        $order_payment->order_total = $order_payment->order_total + $tip['tip'];
                        $order_payment->transaction_id = $tip['new_transaction_id'];
                        $transaction_detail = ['transaction_id' => $tip['new_transaction_id']];
                        $order_payment->transaction_detail = json_encode($transaction_detail);

                        $order = Order::find($order_payment->order_id);
                        $order_id = $order_payment->order_id;
                        $order->order_total = $order->order_total + $tip['tip'];
                        $order->save();
                        $order_payment->save();
                        $order_ids[] = $tip['orderID'];
                    }
                }
            }
            updateSyncData('order',$order_id);
        }
//        if(count($order_ids) > 0){
//            foreach (){
//
//            }
//        }
        return response()->json(['message' => 'Tips Updated'], $this->successStatus);
    }

    public function updateOrder(Request $request){
        $validator = Validator::make($request->all(), [
            'card_id' => 'required',
            'reference_id' => 'required',
            'cvv' => 'required'
        ]);
        if ($validator->fails()) {
            $response['error'] = $validator->errors()->first();
            return response()->json(['result' => $response], $this->badRequestStatus);
        }
        $card = CustomerCard::find($request->input('card_id'));
        $order = Order::where('reference',$request->input('reference_id'))->first();
        $store = Store::find($order->store_id);
        $company = $store->company;
        $currency = Currency::where('company_id',$company->id)->first();
        //make payment
        $result = $this->makePayment($order,$currency,CustomerAuth::customer(),$request->input('cvv'),$card);
        if($result == false){
            $response['response']['error'] = 'Payment has been failed. Please try again';
            return response()->json($response, $this->errorStatus);
        } else {
            $order->tip = (isset($request->tip) && is_numeric($request->tip)) ? $request->tip : 0;
            $order->order_status = 1;
            $order->payment_method = 2;
            $order->payment_status = 2;
            $order->customer_order_status = 'completed';
            $order->save();
            updateSyncData('order',$order->id);

            //save order payment
            $order_payment = new OrderPayment();
            $order_payment->order_id = $order->id;
            $order_payment->payment_method = 2;
            $order_payment->payment_status = 2;
            $order_payment->payment_type = 2;
            $order_payment->payment_received = $order->order_total;
            $order_payment->order_total = $order->order_total;
            $order_payment->payment_detail = $result;
            $order_payment->transaction_detail = $result;
            $order_payment->tip = (isset($request->tip) && is_numeric($request->tip)) ? $request->tip : 0;
            $order_payment->card_name = getCardBrand($card->number);
            $order_payment->save();

            //OrderPayment::where('order_id',$order->id)->update(['card_name' => getCardBrand($card->number),'transaction_detail' => $result]);

//        //table free in case of hospitality
            $table = json_decode($order->table_data);
            if(isset($table)){
                $table_id = $table->table_info->id;
                $company = $order->store->company;
                if($company->company_type == 1){
                    if(isset($table_id)){
                        FloorTable::where('table_id',$table_id)->update(['book_status' => 0,'order_id' => 0,'is_mobile_order' => 0]);
                        updateSyncData('table',$table_id,$store->id);
                    }
                }
            }

            $response['order_id'] = $order->id;

            return response()->json(['result' => $response], $this->successStatus);
        }
    }
    public function getBeacons(){
        $beacons = Store::select('id','uid','major','minor')->whereNotNull('uid')->get();
        $response['beacons'] = $beacons;
        return response()->json(['result' => $response], $this->successStatus);
    }

    public function searchStore(Request $request){
        $search = $request->input('search_key');
        if(isset($search)){
            $stores = Store::where('name', 'ilike' , '%'.$search.'%')->get();
        } else {
            $stores = Store::all();
        }
        $stores = $stores->map(function($store){
            $image_name = 'stores/thumbs/'.$store->image;
            $store->thumbnail = checkImage($image_name);
            $image_name = 'stores/'.$store->image;
            $store->fullImage = checkImage($image_name);
            return $store;
        });
        $response['stores'] = $stores;
        return response()->json(['result' => $response], $this->successStatus);
    }
    public function getCustomerName(Request $request){
        $email = $request->input('email');
        $customer = null;
        if(isset($email)){
            $customer = Customer::where('email',$email)->first();
            if($customer){
                $response['customer_name'] = $customer->first_name.' '.$customer->last_name;
            }
            else {
                $response['response']['error'] = 'Customer not found';
                return response()->json($response, $this->notFoundStatus);
            }
        }
        else {
            $response['response']['error'] = 'Customer not found';
            return response()->json($response, $this->notFoundStatus);
        }

        return response()->json(['result' => $response], $this->successStatus);
    }



    public function updateableAttributes()
    {
        $attrs = $this->purify($this->all());
        return $attrs;
    }
    public function purify($attributes)
    {
        $final_attributes = array('dob','email','first_name','last_name','country_id','mobile','ref_code','company_name','address','state','city','zip_code','note','current_billing_address','current_shipping_delivery_address','customer_group_id','company_id','store_id','password');
        $columns = [];
        foreach ($attributes as $attr => $value)
        {
            if(in_array($attr,$final_attributes))
            {
                $columns[$attr] = $value;
                if($attr == 'password'){
                    $columns[$attr] = bcrypt($value);
                }
            }
        }
        return $columns;
    }


    function saveCustomerSession($customer,$input){
        $data['customer_id'] = $customer->id;
        $data['device_id'] = $input['device_id'];
        $data['device_type'] = $input['device_type'];
        $data['session_token'] = bcrypt($customer->id);
        $customer_session = CustomerSession::create($data);
        return $customer_session;
    }
    public function getProductsMap($products)
    {

        $products->map(function ($product) {
            //$discount_array = $this->getProductDiscount($product);
            //unset($product->discount_type);
            //unset($product->discount);
//            $product->discount_type = $discount_array['discount_type'];
//            if($discount_array['discount_type'] == 'Percentage'){
//                $product->discount_amount = $discount_array['original_percentage'];
//            } else {
//                $product->discount_amount = $discount_array['amount'];
//            }

            if($product->is_modifier == 1){
                $product->product_modifiers->map(function ($modifier) {
                    $modifier->name = $modifier->modifier->name;
                    $modifier->price = $modifier->modifier->price;

                    unset($modifier->product_id);
                    unset($modifier->created_at);
                    unset($modifier->modifier);
                    return $modifier;
                });
            }
            if($product->product_images){
                $product->product_images->map(function ($product_image) {
                    $product_image->full_image = checkImage('products/'.$product_image->name);
                    $product_image->thumbnail = checkImage('products/thumbs/'.$product_image->name);
                    return $product_image;
                });
            }

            return $product;

        });

        return $products->all();
    }
    function createToken($card_number,$card_expiry){
        $communicatorConfiguration = new CommunicatorConfiguration(
            '6090ef9abe4ab299',
            'noOBPTHka36ZV5nZrVcdChCD5e/TFL9iNxrV9PGpK9I=',
            'https://eu.sandbox.api-ingenico.com',
            'Ingenico'
        );


        $connection = new DefaultConnection();
        $communicatorConfiguration = $communicatorConfiguration;
        $communicator = new Communicator($connection, $communicatorConfiguration);
        $client = new Client($communicator);

        $merchantId = '2631';

        $billingAddress = new Address();
        $billingAddress->countryCode = "US";

        $customer = new CustomerToken();
        $customer->billingAddress = $billingAddress;

        $cardWithoutCvv = new CardWithoutCvv();
        $cardWithoutCvv->cardNumber = $card_number;
        $cardWithoutCvv->expiryDate = $card_expiry;

        $data = new TokenCardData();
        $data->cardWithoutCvv = $cardWithoutCvv;

        $card = new TokenCard();
        $card->customer = $customer;
        $card->data = $data;

        $body = new CreateTokenRequest();
        $body->card = $card;
        $body->paymentProductId = 1;


        try {

            $response = $client->merchant($merchantId)->tokens()->create($body);
            return $response->token;
        } catch (\Exception $e) {
            //dd($e->getResponse());
            return false;

//            die();
        }




    }
    public function makePayment($customer_order,$currency,$order_customer,$cvv,$card){
        //dd($customer_order->order_total);
        $communicatorConfiguration = new CommunicatorConfiguration(
            '6090ef9abe4ab299',
            'noOBPTHka36ZV5nZrVcdChCD5e/TFL9iNxrV9PGpK9I=',
            'https://eu.sandbox.api-ingenico.com',
            'Ingenico'
        );


        $connection = new DefaultConnection();
        $communicatorConfiguration = $communicatorConfiguration;
        $communicator = new Communicator($connection, $communicatorConfiguration);
        $client = new Client($communicator);

        $merchantId = '2631';
        $createPaymentRequest = new CreatePaymentRequest();

        $order = new PaymentOrder();

        $amountOfMoney = new AmountOfMoney();
        $amountOfMoney->amount = round((($customer_order->order_total + $customer_order->tip)*100),2);
        $amountOfMoney->currencyCode = $currency->code;
        $order->amountOfMoney = $amountOfMoney;

        $customer = new CardCustomer();
//        $customer->merchantCustomerId = "1234";
        $customer->merchantCustomerId = $order_customer->id;



        $billingAddress = new Address();
        $billingAddress->countryCode = "US";
        $customer->billingAddress = $billingAddress;


        $contactDetails = new ContactDetails();
        $contactDetails->emailAddress = $order_customer->email;
        $contactDetails->emailMessageType = "html";
        $customer->contactDetails = $contactDetails;
        $order->customer = $customer;


        $createPaymentRequest->order = $order;

        $cardPaymentMethodSpecificInput = new CardPaymentMethodSpecificInput();
        $cardPaymentMethodSpecificInput->paymentProductId = 1;
        $cardPaymentMethodSpecificInput->skipAuthentication = false;
//        $cardPaymentMethodSpecificInput->token = 'ac0c5dae-2a69-40d8-b28b-6054319d83f4';
        $cardPaymentMethodSpecificInput->token = $card->token;
        $cardPaymentMethodSpecificInput->tokenize = true;

        $card = new Card();
//        $card->cvv = "123";
        $card->cvv = $cvv;
        // $card->cardNumber = "456735000042797";
        // $card->expiryDate = "1220";
        // $card->cardholderName = "Wile E. Coyote";
        $cardPaymentMethodSpecificInput->card = $card;

        $createPaymentRequest->cardPaymentMethodSpecificInput = $cardPaymentMethodSpecificInput;

        /** @var CreatePaymentResponse $createPaymentResponse */
        // $createPaymentResponse = $client->merchant($merchantId)->payments()->create($createPaymentRequest);

        try {
            //Log::info(['try' => 'here']);
            $createPaymentResponse = $client->merchant($merchantId)->payments()->create($createPaymentRequest);
            return $createPaymentResponse->payment->id;
            //echo $createPaymentResponse->payment->id;

        } catch (DeclinedPaymentException $e) {
            //Log::info(['DeclinedPaymentException' => $e->getResponse()]);
            //dd($e->getResponse());
            return false;

//            die();
        } catch (ApiException $e) {
            //Log::info(['ApiException' => $e->getErrors()]);
            //dd($e->getErrors());
            //print_r($e->getErrors());
            return false;
            //$this->handleApiErrors($e->getErrors());
        }catch (ValidationException $e) {
            //Log::info(['ApiException' => $e->getErrors()]);
            //dd($e->getResponse());

            return false;
            //print_r($e->getErrors());
            //$this->handleApiErrors($e->getErrors());
        }
    }

    function orderUpdateStockManage($order_id , $order_items , $user_id = 0, $store_id = 0){
    $order_items = json_decode($order_items);
        foreach($order_items as $key => $item) {
            //for customer login
            if($user_id == 0 || $store_id == 0){
                $store_id = Auth::user()->store->id;
                $user_id = Auth::user()->id;
            }
            $product_id = $item->item_id;
            $product_stock = Store_products::where('product_id',$product_id)->where('store_id',$store_id)->first();

            //check if order item is deleted
            $item->delete_status = 1;
            if($item->delete_status == 1){
                updateProductStockByData($product_id, $store_id, $item->quantity, 1, 3, $order_id, $user_id, 'Order product updated');
                updateSyncData('product', $product_id);
            } else {
                $add_quantity = 0;
                $remove_quantity = 0;
                //calculate item quantity against this order
                $old_quantity = $this->orderItemStock($order_id,$product_id);
                if($old_quantity == 'Not Found'){
                    updateProductStockByData($product_id, $store_id, $item->quantity, 2, 3, $order_id, $user_id, 'Order product updated');
                    updateSyncData('product', $product_id);
                } else {
                    $new_quantity = $item->quantity;
                    $diff  = $old_quantity - $new_quantity;
                    if($diff > 0){
                        $add_quantity = $diff;
                        updateProductStockByData($product_id, $store_id, $add_quantity, 1, 3, $order_id, $user_id, 'Order product updated');
                        updateSyncData('product', $product_id);
                    } else if($diff < 0 ) {
                        $remove_quantity = (-1 * $diff);
                        if($remove_quantity < $product_stock->quantity){
                            updateProductStockByData($product_id, $store_id, $remove_quantity, 2, 3, $order_id, $user_id, 'Order product updated');
                            updateSyncData('product', $product_id);
                        }
                    }
                }

            }
        }
    }
    function orderItemStock($order_id,$product_id){
        $stocks = Stock::where(['order_id' => $order_id , 'product_id' => $product_id])->get();
        if($stocks){
            $quantity = 0;
            foreach($stocks as $stock){
                if($stock->stock_type == 1)
                    $quantity = $quantity + $stock->quantity;
                elseif($stock->stock_type == 2)
                    $quantity = $quantity - $stock->quantity;
            }
            return abs($quantity);
        } else {
            return 'Not Found';
        }

    }
    public function test(Request $request){
        $device_token = $request->input('device_token');
        $device = UserDevice::where('device_token',$device_token)->first();
        //dd($device->device_token);
        $res = array();
        $res['notification']['title'] = 'New Order';
        $res['notification']['body'] = 'You have received a new order';
        $res['notification']['obj'] = 23;
        sendAndroidNotification($res,$device);
    }

    public function test1(){
        $logs = User_log::where('name','checkin_success')->orderBy('id','desc')->get();
        foreach ($logs as $log){
            $user_id = $log->user_id;
            $log_id = $log->id;
            $user_checkout_log = User_log::where('id' , '>', $log_id)->where('user_id',$user_id)->where('name','checkout_success')->orderBy('id','desc')->first();

            if($user_checkout_log){
                $user_check_in_time = $log->created_at;
                $check_out_time = $user_checkout_log->created_at;



                $to_time = strtotime($check_out_time);
                $from_time = strtotime($user_check_in_time);
                $time_diff = round(abs($to_time - $from_time) / 60);


                $user_checkout_log->session_time = $time_diff;
                $user_checkout_log->save();
                $log->session_time = 0;
                $log->save();
            }


        }
    }
    public function test3(){
        $order_payments = OrderPayment::all();
        foreach ($order_payments as $payment){
            $transaction_detail = json_decode($payment->transaction_detail,true);
            if(isset($transaction_detail)){
                foreach ($transaction_detail as $key => $value){
                    if($key == 'transaction_id'){
                        $payment->transaction_id = $value;
                        $payment->save();
                    }
                }
            }
        }
    }

    public function saveProductOrder(){

        $orders = Order::all();
        foreach ($orders as $order){
            $order_id = $order->id;
            $products = json_decode($order->order_items);
            if(isset($products)){
                $product_order_array = [];
                foreach ($products as $product){
                    $arr = [];
                    $arr['order_id'] = $order_id;
                    $arr['product_id'] = $product->item_id;
                    $arr['quantity'] = $product->quantity;
                    $arr['price'] = $product->unit_price;
                    $arr['created_at'] = date('Y-m-d H:i:s');
                    $arr['updated_at'] = date('Y-m-d H:i:s');
                    $arr['price'] = $product->unit_price;

                    $product_order_array[] = $arr;
                }
                ProductOrder::insert($product_order_array);
            }
        }

    }


    public function addShifts(Request $request){

        $request_data = $request->all();
        $shifts = $request_data['shifts'];
        $shifts = json_decode($shifts);
        $user = Auth::user();
        $store = $user->store;
        if(isset($shifts)) {
            foreach ($shifts as $shift) {
                $shift = json_decode($shift);
                $shift_insert = new Shifts();
                $shift_insert->user_id = $shift->user_id;
                $shift_insert->start_time = $shift->checkin_time;
                $shift_insert->end_time = $shift->checkout_time;
                $shift_insert->starting_balance = $shift->starting_balance;
                $shift_insert->total_balance = $shift->total_balance;
                $shift_insert->transaction = $shift->transaction;
                $shift_insert->logs = $shift->logs;
                //get store_id
                $user = User::find($shift->user_id);
                $shift_insert->store_id = $user->store->id;

                $to_time = strtotime($shift->checkin_time);
                $from_time = strtotime($shift->checkout_time);
                $time_diff = round(abs($to_time - $from_time) / 60);
                $shift_insert->minutes = $time_diff;
                $shift_insert->save();
            }
            $status = $this->successStatus;
            return response()->json(['message' => 'Shifts stored successfully'], $status);
        }
    }
    public function deleteData(Request $request){

        $transport = (new \Swift_SmtpTransport('smtp.gmail.com', '587'))
            ->setUsername('junaidbabar0223@gmail.com')
            ->setPassword('Mjunaid@223')
            ->setEncryption('tls');

        \Mail::setSwiftMailer(new \Swift_Mailer($transport));

// Mail::to('junaid.babar@elementarylogics.com')->send('sdsdsdsd');

        $email['email_message'] = 'ssdsds';

        Mail::send('welcome',$email, function ($message) {

            $message->from("junaid.babar@elementarylogics.com", $name = "Elementary a Logics");
            $message->to('mobeen7asif@gmail.com', 'junaid.babar@elementarylogics.com');


        });
        dd('success');




//        $order_payment = OrderPayment::where('order_id',70000)->sum('tip');
//        dd($order_payment);
//        if($order_payment){
//            $total_tip = $order_payment->sum('tip');
//            dd($total_tip);
           // $order->tip = $total_tip;
            //$order->save();
        //}

//        $collection = Sync::where('store_id',139)
//            ->take(500)->orderBy('id')->get();
//        $syncs = $collection->unique(function ($item) {
//            return $item['sync_id'].$item['sync_type'];
//        });
//
//        $syncs = collect($syncs->values()->all());
//        return response()->json($syncs);




        dd('success');
//        $discounts = DiscountBogo::all();
//        foreach ($discounts as $discount){
//            if($discount->type == 'category'){
//                if(!isset($discount->from_product_ids)){
//                    $required_product_ids = Category_products::where('category_id',$discount->from_id)->pluck('product_id')->toArray();
//                    $required_product_ids = implode(',',$required_product_ids);
//                    $discount->from_product_ids = $required_product_ids;
//
//                    $optional_product_ids = Category_products::where('category_id',$discount->to_id)->pluck('product_id')->toArray();
//                    $optional_product_ids = implode(',',$optional_product_ids);
//                    $discount->to_product_ids = $optional_product_ids;
//                    $discount->save();
//                }
//            }
//        }

        dd('here');
//        $users = User::all();
//        foreach ($users as $user){
//            $digits = 4;
//            $number = rand(pow(10, $digits-1), pow(10, $digits)-1);
//            $user->pin_code = $number;
//            $user->save();
//        }
//        $employees = User::all();
//        foreach($employees as $employee) {
//            $model_role = DB::table('model_has_roles')->where('model_id',$employee->id)->where('model_type','App\User')->first();
//            if($model_role){
//                $role = DB::table('roles')->find($model_role->role_id);
//                if($role){
//                    $employee->role_name = $role->name;
//                    $employee->save();
//                }
//            }
//
//        }
        dd('success');

//        $stores = Store::all();
//        foreach ($stores as $store){
//            $new_store = Store::find($store->id);
//            $new_store->name = $new_store->company->name;
//            $new_store->save();
//        }
//        dd('success');
//        $model = $request->input('model');
//        $model_namespace = '\\App\\'.$model;
//
//        $model_namespace::truncate();
//        dd('success');
//        $model::all()->delete();
//
//
//        $table = $request->input('table_name');
//        DB::table($table)->delete();
//        return 'success';
    }
    public function getStores(Request $request){
        $stores = Store::where('company_id',$request->get('company_id'))->get();
        return response()->json(['stores' => $stores], 200);
    }





}




