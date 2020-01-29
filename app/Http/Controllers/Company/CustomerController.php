<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\LogActivity;
use App\StoreCustomer;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;
use Auth, DB, Validator;
use App\Customer;
use App\Customer_group;
use App\Store;
use App\User;
use App\Attendance_status;
use Illuminate\Support\Collection;
use App\ProductOrder;
use App\Order;
use App\Product;
use App\Store_products;
class CustomerController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    public $notFoundStatus = 404;
    public $badRequestStatus = 400;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {

//        $customers = StoreCustomer::with(['customer','store'])->where('company_id',Auth::id())->orderBy('id','desc')->get();
//        return $customers->first()->store;
        return view('company.customers.index');
    }

    /**
     * get users
     *
     * 
     */
    public function getCustomers(){


        $customers = StoreCustomer::with(['customer','store'])->where('company_id',Auth::id())->orderBy('id','desc')->get();
       // return $customers;
        //$customers = Customer::with(['store'])->whereIn('company_id',$company_ids)->orderBy('id','desc')->get();
        
        return Datatables::of($customers)
            ->addColumn('profile_image', function ($customer) {
                return '<img width="30" src="'.checkImage('customers/thumbs/'. $customer->customer->profile_image).'" />';
            })
            ->addColumn('name', function ($customer) {
                return $customer->customer->first_name .' '.$customer->customer->last_name;
            })
            ->addColumn('email', function ($customer) {
                return $customer->customer->email;
            })
            ->addColumn('mobile', function ($customer) {
                return $customer->customer->mobile;
            })
            ->addColumn('store_name', function ($customer) {
                return @$customer->store->name;
            })
            ->addColumn('action', function ($customer) {
                     return '<a href="sales?customer_id='.base64_encode($customer->customer_id).'" class="text-primary action-padding" data-toggle="tooltip" title="Customer Sales"><i class="fa fa-bar-chart"></i></a>
                        <a href="favorite-products/'.base64_encode($customer->customer_id).'" class="text-primary action-padding" data-toggle="tooltip" title="Favorite Products"><i class="fa fa-heart" style="color:red;"></i></a>
                        <a href="customer_detail/'. base64_encode($customer->customer_id).'" class="text-success btn-order" data-toggle="tooltip" title="View Customer" id="'.$customer->customer_id.'"><i class="fa fa-eye"></i></a>';
//                else{
//                     return '
//                        <a href="sales?customer_id='.base64_encode($customer->customer_id).'" class="text-primary action-padding" data-toggle="tooltip" title="Customer Sales"><i class="fa fa-bar-chart"></i></a>
//                        <a href="favorite-products/'.base64_encode($customer->customer_id).'" class="text-primary action-padding" data-toggle="tooltip" title="Favorite Products"><i class="fa fa-heart" style="color:red;"></i></a>';
//                }
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['profile_image','store_name','action','email','mobile'])
            ->make(true);

//        <a href="javascript:void(0)" class="text-danger btn-delete action-padding" data-toggle="tooltip" title="Delete Customer" id="'.Hashids::encode($customer->customer_id).'"><i class="fa fa-trash"></i></a>
//        <a href="customers'.'/'.base64_encode($customer->customer_id).'/edit" class="text-primary action-padding" data-toggle="tooltip" title="Edit Customer"><i class="fa fa-edit"></i></a>
 
    }
    
        //search items
    public function searchCustomers(Request $request)
    {      
        if(\Request::wantsJson()) 
        {
            
            $customers = Customer::with(['orders'])->where("company_id",getComapnyIdByUser());                              
            
            if(!empty($request->q)){
                $name = $request->q;
                $customers->where(function($q) use ($name) {
                        $q->where('first_name', 'ilike', "$name%")->orWhere('last_name', 'ilike', "$name%");
                  });
                   
                  $customers->where('email', 'ilike', '%'.$name.'%');
            }           
            
            
            if(!empty($request->limit)){
                if($request->limit=='all')
                    $customers = $customers->paginate($customers->count());
                else
                    $customers = $customers->paginate($request->limit);
            }else
                $customers = $customers->paginate(10);                            
            
            $customers->setCollection(
                $customers->getCollection()
                    ->map(function($customer, $key)
                    {
                        $customer->name = $customer->first_name .' '.$customer->last_name;
                        
                        if($customer->orders->count()>0){
                            $customer->total_sales = number_format($customer->orders->sum('order_total'),2);
                            $customer->total_visits = $customer->orders->count();
                            $customer->last_visit = $customer->orders->last()->created_at->format('d/m/Y');
                        }else{
                            $customer->total_sales = 0;
                            $customer->total_visits = 0;
                            $customer->last_visit = '';
                        }
                        
                        unset($customer->orders);
                        return $customer;
                    })
                    //->sortBy('store_id')
            );
            
//            $customers->map(function ($customer) { 
//                
//                $customer['name'] = $customer['first_name'] .' '.$customer['last_name'];
//                
//                return $customer;
//            });
            
            $response['customers'] = $customers;  
            
            
            $status = $this->successStatus;
            
            return response()->json(['result' => $response], $status);
        }
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $groups = Customer_group::where('company_id',Auth::id())->pluck('name','id')->prepend('Select Customer Group','');
        
        return view('company.customers.create', compact('groups'));
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
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:customers', 
            'store_id' => 'required',            
            'profile_image' => 'required|mimes:jpeg,jpg,png',
        ]);   
        
        $requestData = $request->all();         
        $requestData['company_id'] = Auth::id();
        
        $customer = Customer::create($requestData);        
        
        //save profile image
        if($request->hasFile('profile_image')){
            $destinationPath = 'uploads/customers'; // upload path
            $image = $request->file('profile_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $customer->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //update image record
            $customer_image['profile_image'] = $fileName;
            $customer->update($customer_image);
        }
        
        // sync customers
        updateSyncData('customer',$customer->id);  
        
        Session::flash('success', 'Customer added!');        

        return redirect('company/customers');  
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JSON
     */
    public function storeApi(Request $request)
    {
        if(isset($request->customers)){
            $customers = json_decode($request->customers);

            foreach($customers as $customer_request)
            {
                $customer_exist = Customer::where('email',$customer_request->email)->first();
                if(!$customer_exist) {

                    $customer_data['id'] = $customer_request->id;
                    $customer = Customer::firstOrNew($customer_data);

                    $customer->id = $customer_request->id;
                    $customer->first_name = $customer_request->first_name;
                    $customer->last_name = $customer_request->last_name;
                    $customer->email = $customer_request->email;
                    $customer->country_id = $customer_request->country_id;
                    $customer->mobile = $customer_request->mobile;
                    $customer->ref_code = $customer_request->ref_code;
                    $customer->company_name = $customer_request->company_name;
                    $customer->address = $customer_request->address;
                    $customer->state = $customer_request->state;
                    $customer->city = $customer_request->city;
                    $customer->zip_code = $customer_request->zip_code;
                    $customer->note = $customer_request->note;
                    $customer->current_billing_address = $customer_request->current_billing_address;
                    $customer->current_shipping_delivery_address = $customer_request->current_shipping_delivery_address;
                    $customer->customer_group_id = isset($customer_request->customer_group_id) ? $customer_request->customer_group_id : 0;
                    //$customer->company_id = getComapnyIdByUser();
                    //$customer->store_id = Auth::user()->store_id;
                    $customer->profile_image = 'default.png';
                    $customer->created_at = $customer_request->created_at;
                    $customer->updated_at = $customer_request->created_at;
                    $customer->save();

                    // sync customer
                    updateSyncData('customer',$customer->id);

                    //save store customer
                    $store_customer['customer_id'] = $customer->id;
                    $store_customer['store_id'] = Auth::user()->store_id;
                    $store_customer['company_id'] = Auth::user()->store->company->id;
                    StoreCustomer::create($store_customer);
                }
                else {

                    $customer_exist->id = $customer_request->id;
                    $customer_exist->first_name = $customer_request->first_name;
                    $customer_exist->last_name = $customer_request->last_name;
                    $customer_exist->email = $customer_request->email;
                    $customer_exist->country_id = $customer_request->country_id;
                    $customer_exist->mobile = $customer_request->mobile;
                    $customer_exist->ref_code = $customer_request->ref_code;
                    $customer_exist->company_name = $customer_request->company_name;
                    $customer_exist->address = $customer_request->address;
                    $customer_exist->state = $customer_request->state;
                    $customer_exist->city = $customer_request->city;
                    $customer_exist->zip_code = $customer_request->zip_code;
                    $customer_exist->note = $customer_request->note;
                    $customer_exist->current_billing_address = $customer_request->current_billing_address;
                    $customer_exist->current_shipping_delivery_address = $customer_request->current_shipping_delivery_address;
                    $customer_exist->customer_group_id = isset($customer_request->customer_group_id) ? $customer_request->customer_group_id : 0;
                    //$customer->company_id = getComapnyIdByUser();
                    //$customer->store_id = Auth::user()->store_id;
                    $customer_exist->profile_image = 'default.png';
                    $customer_exist->created_at = $customer_request->created_at;
                    $customer_exist->updated_at = $customer_request->created_at;
                    $customer_exist->save();
                    updateSyncData('customer',$customer_exist->id);

                    $store_customer = StoreCustomer::where(['customer_id' => $customer_exist->id,'store_id' => Auth::user()->store_id])->first();
                    if(!$store_customer){
                        //save store customer
                        $store_customer['customer_id'] = $customer_exist->id;
                        $store_customer['store_id'] = Auth::user()->store_id;
                        $store_customer['company_id'] = Auth::user()->store->company->id;
                        StoreCustomer::create($store_customer);
                    }
                    else {
                        $store_customer->store_id = Auth::user()->store_id;
                        $store_customer['company_id'] = Auth::user()->store->company->id;
                        $store_customer->save();

                    }
                }
            }



            $response['success'] =  'Customer added!';

            return response()->json(['result'=>$response], $this->successStatus);
        }else{
            $response['error'] =  'customers key is required';

            return response()->json(['result'=>$response], $this->badRequestStatus);
        }


    }

    /**
     * Show the detail of user.
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $id = Hashids::decode($id)[0];
        
        $groups = Customer_group::where('company_id',Auth::id())->pluck('name','id')->prepend('Select Customer Group','');
        
        $customer = Customer::with(['store'])->findOrFail($id);
        
        return view('company.users.user_profile', compact('user'));
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
        $id = base64_decode($id);
        //$id = Hashids::decode($id)[0];
        
        $groups = Customer_group::where('company_id',Auth::id())->pluck('name','id')->prepend('Select Customer Group','');

        $store = StoreCustomer::where('customer_id',$id)->first();

        $customer = Customer::with(['store'])->findOrFail($id);

        return view('company.customers.edit', compact('customer', 'groups','store'));
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

        $id = base64_decode($id);
        //$id = Hashids::decode($id)[0];
       
        $this->validate($request, [ 
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',      
            'email' => "required|email|max:255|unique:users,email,$id",
            //'store_id' => 'required',
        ]);                  
        
        $customer = Customer::findOrFail($id);
        
        $requestData = $request->all(); 
        
        $customer->update($requestData);

        $customer = Customer::find($id);
//        if($customer){
//            $customer = StoreCustomer::where(['customer_id' => $customer->id,'store_id' => $request->input('store_id')])->first();
//            if(!$customer){
//                $store_customer['customer_id'] = $customer->id;
//                $store_customer['store_id'] = $request->input('store_id');
//                $store_customer['company_id'] = Auth::user()->id;
//                StoreCustomer::create($store_customer);
//            }
//            else {
//                $customer->store_id = $request->input('store_id');
//                $customer->save();
//            }
//        }
        
        //save profile image
        if($request->hasFile('profile_image')){
            $destinationPath = 'uploads/customers'; // upload path
            $image = $request->file('profile_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $customer->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //remove old image
            File::delete($destinationPath . $customer->profile_image);
            File::delete($destinationPath .'/thumbs/'. $customer->profile_image);
            
            //update image record
            $customer_image['profile_image'] = $fileName;
            $customer->update($customer_image);
        }
        
        // sync customers
        updateSyncData('customer',$customer->id);  
        
        Session::flash('success', 'Customer updated!');

        return redirect('company/customers');
    }

    public function getCustomerDetail($id, Request $request){
        $id = base64_decode($id);
        $customer = Customer::find($id);
        if(!$customer){
            return redirect()->back()->with('error','Customer not exist');
        }
        return view('company.customers.customer_detail',['customer' => $customer]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return JOSN
     */
    public function updateApi($id, Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|max:255',
            'last_name' => 'required|max:255',
            'email' => "required|email|max:255|unique:users,email,$id", 
            'country_id' => 'required',              
        ]);   
        
        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);                 
        }
        
        $customer = Customer::findOrFail($id);
        
        $requestData = $request->all(); 
        
        $customer->update($requestData);               
        
        // sync customers
        updateSyncData('customer',$customer->id);  
        
        $response['customer'] =  $customer;
        $response['success'] =  'Customer updated!';

        return response()->json(['result'=>$response], $this->successStatus);                                    
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

        $customer = Customer::find($id);
        
        if($customer){
            // sync customers
            updateSyncData('delete_customer',$customer->id,[$customer->store_id]);  
        
            $customer->delete();
            $response['success'] = 'Customer deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Customer not exist against this id!';
            $status = $this->notFoundStatus;  
        }
        
        return response()->json(['result'=>$response], $status);
    }   

    public function favoriteProducts($customer_id){

        $customer_id = base64_decode($customer_id) ;
        return view('company.customers.favorite',compact('customer_id'));
    }  

    public function getFavoriteProducts($customer_id){

        $orders = Order::where('customer',$customer_id)->whereIn('store_id',getStoreIds())->pluck('id');


        $product_order = ProductOrder::with(['product'])->whereIn('order_id',$orders)->get();


        $product_order->map(function ($product) use($orders){


                $product_orders = ProductOrder::whereIn('order_id',$orders)->where('product_id',$product->product_id)->get();

                $product['reorder_point'] =$product_orders->sum('quantity');

                $product['reorder_amount'] =$product_orders->sum(function ($product) {
                                                return $product->quantity * $product->price;
                                            });
                return $product;            
        });

        $product_order = $product_order->unique('product_id');

        return Datatables::of($product_order)
            ->addColumn('name', function ($product) {
               return $product->product['name'];
            })
            ->addColumn('code', function ($product) {
               return $product->product['code'];
            })
            ->addColumn('sku', function ($product) {
               return $product->product['sku'];
            })
            ->addColumn('reorder_point', function ($product) {
               return $product->reorder_point;
            })
            ->addColumn('reorder_amount', function ($product) {
               return $product->reorder_amount;
            })            
            ->rawColumns(['name','code','sku','reorder_point','reorder_amount'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
        }       
    
}
