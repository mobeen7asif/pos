<?php

namespace App\Http\Controllers\Admin;

use App\Company_setting;
use App\Currency;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\MealType;
use App\Role;
use App\Shipping_option;
use App\StoreCustomer;
use App\Tax_rates;
use App\User;
use Exception;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Country;
use App\Store;
use App\Customer;
use App\Email_template;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;
use DateTime;

class CompanyController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {                
        return view('admin.companies.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getCompanies()
    {        
        $companies = Company::with(['store'])->get();                
        
        return Datatables::of($companies)
            ->addColumn('logo', function ($company) {
                $logo = $company->logo;
                if(empty($company->logo))
                    $logo = 'no_image.png';
                
                return '<img width="30" src="'.checkImage('companies/thumbs/'. $logo).'" />';
            })
            ->addColumn('total_stores', function ($company) {
                return @$company->store->count();
            })
            ->addColumn('action', function ($company) {
                return '<a href="companies/'. Hashids::encode($company->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Company"><i class="fa fa-edit"></i></a> 
                        <a href="companies/company-login/'. Hashids::encode($company->id).'" class="text-success" target="_blank" data-toggle="tooltip" title="Company Login"><i class="fa fa-sign-in"></i></a>';
            })           
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['logo', 'total_stores', 'action'])
            ->make(true);
            
    }    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        return view('admin.companies.create');                
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {

        $this->validate($request, [ 
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:companies',
            'password' => 'required|min:6|confirmed',
            'country' => 'required',
            'company_type' => 'required'
//            'state' => 'required',
//            'city' => 'required',
//            'zip' => 'required',
//            'address' => 'required',       
//            'logo' => 'required|mimes:jpeg,jpg,png',
        ]);   
        
       $requestData = $request->all();  
       $password = $requestData['password'];
       $requestData['password'] = bcrypt($requestData['password']);
        
        $company = Company::create($requestData);
        if($company){

            //create no tax rate
            $tax_rate['name'] = 'Tax Free';
            $tax_rate['code'] = 'NT';
            $tax_rate['rate'] = 0;
            $tax_rate['company_id'] = $company->id;

            $tax_rate= Tax_rates::create($tax_rate);

            //create currency
            $currency['company_id'] = $company->id;
            $currency['code'] = 'USD';
            $currency['name'] = 'Dollar';
            $currency['symbol'] = '$';
            $currency['direction'] = 1;

            $currency = Currency::create($currency);
            
            $email_data = Email_template::where('name','Company Register')->first();

            $site_link = '<a href="'.url('company/login').'">Clck here for login</a>';

            $email_to            = $company->email;
            $email_from            = $email_data->from;
            $email_subject            = $email_data->subject;
            $email_body          = $email_data->content;

            $email_body = str_replace('{client_name}',$company->name,$email_body);
            $email_body = str_replace('{site_link}',$site_link,$email_body);
            $email_body = str_replace('{email}',$company->email,$email_body);
            $email_body = str_replace('{password}',$password,$email_body);
            $email_body = str_replace('{site_name}',settingValue('site_title'),$email_body);
            $body = $email_body;
            Email_template::sendEmail($email_to,$email_data,$body);
//
            $store['company_id'] = $company->id;
            $store['name'] = $company->name;
            $store['address'] = $company->address .' '.$company->city;
            $store['image'] = 'default.jpg';
            $store['tax_id'] = $tax_rate->id;
            $saved_store = Store::create($store);

            //create store admin role and assign it to the new employee
            $role = [];
            $role['company_id'] = $company->id;
            $role['name'] = 'Store Admin';
            $role['guard_name'] = 'company';
            $role_status = Role::create($role);
            if($role_status){
                $new_user = [];
                $new_user['name'] = 'Store Admin';
                $new_user['email'] = $company->email;
                $rand_pass = rand(pow(10, 7-1), pow(10, 7)-1);
                $new_user['password'] = bcrypt($rand_pass);
                $new_user['is_subscribed'] = 1;
                $new_user['pin_code'] = rand(pow(10, 4-1), pow(10, 4)-1);
                $new_user['gender'] = 1;
                $new_user['status'] = 1;
                $new_user['store_id'] = $saved_store->id;
                $new_user['role_name'] = $role_status->name;
                $new_user = User::create($new_user);
                if($new_user){
                    $model_has_role['role_id'] = $role_status->id;
                    $model_has_role['model_id'] = $new_user->id;
                    $model_has_role['model_type'] = 'App\User';
                    DB::table('model_has_roles')->insert($model_has_role);
                    //send password email to new admin
                    $body = "Your password is $rand_pass";
                    $res['final_content'] = $body;
                    $email_to = $company->email;
                    try {
                        Mail::send('emails.email_body',$res, function ($message) use ($email_to) {
                            $message->from('info@skulocity.com', 'New Store Admin');
                            $message->to($email_to, $email_to)
                                ->subject('Store Login Password');
                        });
                    }catch (Exception $e) {
                        echo $e->getMessage();
                    }
                }

            }

            //create email template
            Email_template::create(
                ['name' => 'Low Stock','subject' => 'Low Stock Notification','from' => 'Admin','template_key' => 'low_stock',
                    'company_id' => $company->id, 'content' => getEmailHtml()
                ]
            );

            //saving shipping option
            $shipping_options['name'] = 'Free Shipping';
            $shipping_options['cost'] = 0;
            $shipping_options['company_id'] = $company->id;
            $shipping_options = Shipping_option::create($shipping_options);
            //saving meal types
            if($company->company_type == 1){
                $meal_types_array = ['Drink','Starter','Main Course','Deserts'];
                $meal_types_colors = ['4de2c0','ffc30c','bd10e0','fd3d50'];
                $data = [];
                for($i = 0; $i < 4; $i++){
                    $temp = [];
                    $temp['store_id'] = $saved_store->id;
                    $temp['company_id'] = $company->id;
                    $temp['meal_type'] = $meal_types_array[$i];
                    $temp['color'] = $meal_types_colors[$i];
                    $temp['created_at'] = date('Y-m-d H:i:s');
                    $temp['updated_at'] = date('Y-m-d H:i:s');
                    $data[] = $temp;
                }
                MealType::insert($data);
            }
            $timestamp = strtotime(date('Y-m-d H:i:s'));
            $customer['id'] = $company->id.$timestamp;
            $customer['first_name'] = 'Walk-in';
            $customer['last_name'] = 'Customer';
            $customer['email'] = $request->email;
            //$customer['company_id'] = $company->id;
            $customer['country_id'] = $request->country;
            $customer['address'] = $request->address;
            $customer['city'] = $request->city;
            $customer['state'] = $request->state;
            $customer['zip_code'] = $request->zip;
            //$customer['store_id'] = $saved_store->id;
            $customer['dob'] = null;
            $customer['password'] = bcrypt(123456);
            $customer_data = Customer::create($customer);

            //save store customer
            $store_customer['customer_id'] = $customer_data->id;
            $store_customer['store_id'] = $saved_store->id;
            $store_customer['company_id'] = $company->id;
            StoreCustomer::create($store_customer);
            
             //sync customers
            updateSyncData('customer',$customer_data->id);

            //save system settings
            $system_settings['currency_id'] = $currency->id;
            $system_settings['email'] = $company->email;
            $system_settings['store_id'] = $saved_store->id;
            $system_settings['tax_id'] = $tax_rate->id;
            $system_settings['shipping_id'] = $shipping_options->id;
            $system_settings['discount_status'] = 'High';
            $system_settings['sales_notifications'] = 1;
            $system_settings['offline_mode'] = 1;
            $system_settings['company_id'] = $company->id;
            Company_setting::create($system_settings);
        }
        
        //save logo image
        if($request->hasFile('logo')){
            $destinationPath = 'uploads/companies'; // upload path
            $image = $request->file('logo'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $company->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //update image record
            $company_image['logo'] = $fileName;
            $company->update($company_image);
        }



        Session::flash('success', 'Company added!');

        return redirect('admin/companies');  
    }
    
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id)
    {
                        
        $id = Hashids::decode($id)[0];
        
        $company = Company::findOrFail($id);       
        
        return view('admin.companies.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update($id, Request $request)
    {        
        $id = Hashids::decode($id)[0];
        
        $this->validate($request, [ 
            'name' => 'required|max:255',
            'country' => 'required',
//            'state' => 'required',
//            'city' => 'required',
//            'zip' => 'required',
//            'address' => 'required',                                         
        ]);
        
        $requestData = $request->all();
        $company = Company::findOrFail($id);
        $company->update($requestData);
        $company->company_type = $requestData['company_type'];
        $company->save();
        
        //save category image
        if($request->hasFile('logo')){
            $destinationPath = 'uploads/companies'; // upload path
            $image = $request->file('logo'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $company->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //remove old image
            File::delete($destinationPath . $company->logo);
            
            //update image record
            $company_image['logo'] = $fileName;
            $company->update($company_image);
        }                        
        
        Session::flash('success', 'Company updated!');  

        return redirect('admin/companies');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id)
    {   
        $id = Hashids::decode($id)[0];
        
        $company = Company::find($id);
        
        if($company){
            $company->delete();
            $response['success'] = 'Company deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Company not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    
    
    /**
     * Company Login.
     *
     * @param \Illuminate\Http\Request $company_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function companyLogin($company_id)
    {            
        $id = Hashids::decode($company_id)[0];
        
        Auth::guard('company')->loginUsingId($id);
        
        return redirect('company/dashboard');
    }
    
}
