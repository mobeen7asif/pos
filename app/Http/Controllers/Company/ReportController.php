<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Categories;
use App\StoreCustomer;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Response;
use Image;
use File;
use Session;
use Alert;
use Datatables;
use Hashids;
use App\User;
use App\Product;
use App\Store;
use App\Store_products;
use App\Stock;
use App\Order;
use App\Customer;
use DB;
use App\Helpers\LogActivity;
use App\ProductOrder;
use App\OrderPayment;
use App\Shifts;

class ReportController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    public $notFoundStatus = 404;
    
    /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function index()
    {              
        return view('company.reports.index');
    }        
    
    public function getRetailsDashboard(Request $request)
    {

        $orders = Order::whereIn('store_id',getStoreIds());
        
        if(!empty($request->store_id)){
            $store_id = Hashids::decode($request->store_id)[0];  
            $orders->where('store_id', $store_id);
        }
        
        if(!empty($request->from_date)){
            $orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00');
        }

        if(!empty($request->to_date)){
            $orders->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');
        }
        
        $orders->get();        
        $total_orders = $orders->get();
        //print_r($orders->get()->toArray());
        

        $order_total = $orders->sum('order_total');
        $cost_of_goods = $orders->sum('cost_of_goods');
        $tip_total = $orders->sum('tip');
        
        $report['total_income'] = number_format($order_total,2);
        $report['total_tip'] = number_format($tip_total,2);
        $report['total_sales'] = $orders->count();
        if($order_total>0)
            $report['total_profit'] = number_format($order_total-$cost_of_goods,2); 
        else
            $report['total_profit'] = number_format($order_total+$cost_of_goods,2);     
            
        $report['total_discount'] = number_format($orders->sum('discount'),2); 
        $report['discount_percentage'] = 0;        
        
        if($orders->sum('discount')>0 )
            $report['discount_percentage'] = number_format($orders->sum('sub_total')/$orders->sum('discount'),2); 
        
        $report['basket_value'] = number_format($orders->avg('order_total'),2);
        $report['basket_size'] = number_format($orders->avg('basket_size'),2);           
        $report['cash_sales'] = number_format($total_orders->where('payment_method',1)->sum('order_total'),2);
        $report['card_sales'] = number_format($total_orders->where('payment_method',2)->sum('order_total'),2);
        $report['total_customers'] = $total_orders->groupBy('customer')->count();                   
               
        $start = strtotime($request->from_date);
        $stop = strtotime($request->to_date);
        $dates =[];

        $dates_revenue = [];
        $dates_profit = [];
        $dates_discounts=[];
        $dates_tips=[];

        $t_total_orders=[];
        for ($seconds=$start; $seconds<=$stop; $seconds+=86400)
        {
            $dates[] = date("Y-m-d", $seconds);
            $t_orders = Order::whereIn('store_id',getStoreIds());
        
            if(!empty($request->store_id)){
                $store_id = Hashids::decode($request->store_id)[0];  
                $t_orders->where('store_id', $store_id);
            }
        
            if(!empty($request->from_date)){
                $t_orders->where('created_at', '>=' , date("Y-m-d", $seconds).' 00:00:00')->where('created_at', '<=' , date("Y-m-d", $seconds).' 23:59:59');
            }
        
            $t_total_orders[] = $t_orders->get(); 
        }
        for($i=0;$i<count($t_total_orders);$i++){

            $order_total = $t_total_orders[$i]->sum('order_total');
            $cost_of_goods = $t_total_orders[$i]->sum('cost_of_goods');

            $dates_revenue[] = round($order_total,2);

            if($order_total>0)
            $dates_profit[] = round($order_total-$cost_of_goods,2); 
            else
            $dates_profit[] = round($order_total+$cost_of_goods,2);

            $dates_discounts[] = round($t_total_orders[$i]->sum('discount'),2); 

            $dates_tips[] = round($t_total_orders[$i]->sum('tip'),2); 
        }

       $report['dates'] = $dates;
       $report['dates_revenue'] = $dates_revenue;
       $report['dates_profit'] = $dates_profit;
       $report['dates_discounts'] = $dates_discounts;
       $report['dates_tips'] = $dates_tips;
       $report['t_total_orders'] = $t_total_orders;
       $report['exact_total_orders'] = $total_orders;
       $report['from_date']=$request->from_date;
       $report['to_date']   =$request->to_date;
       $report['store_id']   =     $request->store_id;         

        $status = $this->successStatus;
            
        return response()->json(['result' => $report], $status);
    }

    public function getStoreStocksChart($id = '')
    {
        
        if(empty($id)){
          $product_ids = Store_products::pluck('product_id');  
        }else{
            $store_id = Hashids::decode($id)[0];  
            $product_ids = Store_products::where('store_id',$store_id)->pluck('product_id');  
        }
        
        
        $products = Product::with(['store_products'])->whereIn('id',$product_ids)->get();
          
        $total_products = $products->count();
        $total_product_quantity = Store_products::whereIn('product_id',$product_ids)->sum('quantity');
        
        $total_product_cost=0;
        $total_product_price=0;
        
        foreach($products as $product){
            $total_product_cost = $total_product_cost + ($product->cost * $product->store_products->sum('quantity'));
            $total_product_price = $total_product_price + ($product->price * $product->store_products->sum('quantity'));
        }
        
        $profit_estimate = $total_product_price - $total_product_cost;
        
        $report['total_products'] = number_format($total_products,2);
        $report['total_product_quantity'] = number_format($total_product_quantity,2);
        $report['total_product_cost'] = $total_product_cost;
        $report['total_product_price'] = $total_product_price;
        $report['profit_estimate'] = $profit_estimate;        
        
        return view('company.reports.stores_stock', compact('report'));
            
    }


    /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function salesReport()
    {                     
        
        $orders = Order::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(sub_total) as revenue'),
                            DB::raw('SUM(cost_of_goods) as cost_of_goods'),
                            DB::raw('SUM(order_tax) as order_tax')
                )->whereIn('store_id',getStoreIds());                                           
           
        $orders->where('store_id',2);
        
        $orders = $orders->groupBy('date')->orderBy('date', 'desc')->get();
                
        //dd($orders->toArray());
        
        return view('company.reports.sales');
    } 


        /**
     * Display a listing of the resource.
     *
     * @return JSON
     */
    public function getSalesReport(Request $request)
    {

        $orders = Order::select(
                            DB::raw('DATE(created_at) as date'),
                            DB::raw('SUM(order_total) as revenue'),
                            DB::raw('SUM(cost_of_goods) as cost_of_goods'),
                            DB::raw('SUM(order_tax) as order_tax')
                )->whereIn('store_id',getStoreIds());     
        
        if(!empty($request->store_id)){
            $store_id = Hashids::decode($request->store_id)[0];  
            $orders->where('store_id',$store_id);
        }
        
        if(!empty($request->from_date)){
            $orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00');
        }

        if(!empty($request->to_date)){
            $orders->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');
        }
        
        $orders = $orders->groupBy('date')->orderBy('date', 'desc')->get();                       
        

       

        return Datatables::of($orders)
            ->addColumn('date', function ($order) {
                return date('d/m/Y', strtotime($order->date));                
            })
            ->addColumn('revenue', function ($order) {
                return number_format($order->revenue, 2, '.', '');
            })
            ->addColumn('cost_of_goods', function ($order) {
                return number_format($order->cost_of_goods, 2, '.', '');
            })
            ->addColumn('gross_profit', function ($order) {
                if($order->revenue>0)
                    return number_format($order->revenue - $order->cost_of_goods, 2, '.', ''); 
                else
                    return number_format($order->revenue + $order->cost_of_goods, 2, '.', ''); 
            })
            ->addColumn('margin', function ($order) {
                return number_format(($order->cost_of_goods / $order->revenue), 2, '.', '');
            })
            ->addColumn('order_tax', function ($order) {
                return number_format($order->order_tax, 2, '.', '');
            })
            ->rawColumns(['gross_profit', 'margin'])
            ->make(true);
            
    }



    public function getSalesGraph(Request $request){

            $start = strtotime($request->from_date);
            $stop = strtotime($request->to_date);
            $dates =[];

            $dates_revenue = [];
            $dates_profit = [];
            $dates_cost_goods=[];

            $t_total_orders=[];
            for ($seconds=$start; $seconds<=$stop; $seconds+=86400)
            {
                $dates[] = date("Y-m-d", $seconds);
                $t_orders =  Order::whereIn('store_id',getStoreIds());    
            
                if(!empty($request->store_id)){
                    $store_id = Hashids::decode($request->store_id)[0];  
                    $t_orders->where('store_id', $store_id);
                }
            
                if(!empty($request->from_date)){
                    $t_orders->where('created_at', '>=' , date("Y-m-d", $seconds).' 00:00:00')->where('created_at', '<=' , date("Y-m-d", $seconds).' 23:59:59');
                }
            
                $t_total_orders[] = $t_orders->get(); 
            }

            for($i=0;$i<count($t_total_orders);$i++){
           // foreach($t_total_orders as $row){

                $order_total = $t_total_orders[$i]->sum('order_total');
                $cost_of_goods = $t_total_orders[$i]->sum('cost_of_goods');

                $dates_revenue[] = round($order_total,2);

                $dates_cost_goods[] = round($cost_of_goods,2);

                if($order_total>0)
                $dates_profit[] = round($order_total-$cost_of_goods,2); 
                else
                $dates_profit[] = round($order_total+$cost_of_goods,2);

            }


           $report['dates'] = $dates;
           $report['dates_revenue'] = $dates_revenue;
           $report['dates_profit'] = $dates_profit;
           $report['dates_cost_goods'] = $dates_cost_goods;
           $report['from_date']=$request->from_date;
           $report['to_date']   =$request->to_date;
           $report['store_id']   =     $request->store_id;  

           $status = $this->successStatus;
                
           return response()->json(['result' => $report], $status);

    }



        /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function productsReport(Request $request)
    {    
        $products = Store_products::with(['product.store_products'])->whereIn('store_id',getStoreIds());
        
        if(!empty($request->store_id)){
            $store_id = Hashids::decode($request->store_id)[0];  
            $products->where('store_id',$store_id);
        }
        
        $products = $products->orderBy('id','asc')->get();
        
        $products->map(function ($product) {                                    
            $product['current_stock'] = $product->product->store_products->where('store_id',2)->sum('quantity');
            $product['item_value'] = $product->product->price;
            $product['stock_value'] = $product->product->store_products->sum('quantity')*$product->product->price;
            
            $orders = Order::whereIn('store_id',getStoreIds())->pluck('id');      
            
           // $reorder_point = 0;
           // $reorder_amount = 0;


            $product_orders = ProductOrder::whereIn('order_id',$orders)->where('product_id',$product->product->id)->get();

            $product['reorder_point'] =$product_orders->sum('quantity');

            $product['reorder_amount'] =number_format($product_orders->sum(function ($product) {
                                return $product->quantity * $product->price;
                            }), 2, '.', '');
           /* foreach($orders as $order){
                
                $order_products = json_decode($order->order_items);
                if(isset($order_products)){
                    if(count($order_products)>0){

                        $product_collection = collect($order_products);
                        $filtered = $product_collection->where('item_id', $product->product->id);
                        $filtered->all();

                        if($filtered->count() > 0){
                            $reorder_point = $reorder_point + 1;

                            if(isset($filtered[0]))
                                $reorder_amount = $reorder_amount + $filtered[0]->unit_price;

                        }

                    }
                }

            }*/
            
           /* $product['reorder_point'] = $reorder_point;
            $product['reorder_amount'] = $reorder_amount;*/
            
            return $product;
       });
        
        //dd($products->unique('product_id')->toArray());
       
        return view('company.reports.products');
    } 


    /**
     * Display a listing of the resource.
     *
     * @return JSON
     */
    public function getProductsReport(Request $request)
    {                    
        $products = Store_products::with(['product.store_products'])->whereIn('store_id',getStoreIds());
        
        if(!empty($request->store_id)){
            $store_id = Hashids::decode($request->store_id)[0];  
            $products->where('store_id',$store_id);
        }
        
        $products = $products->orderBy('id','asc')->get();
        
        $products->map(function ($product) use ($request) { 

            if(!empty($request->store_id)){
                $store_id = Hashids::decode($request->store_id)[0];  
                $quantity = $product->product->store_products->where('store_id',$store_id)->sum('quantity');
            }else{
                $quantity = $product->product->store_products->sum('quantity');
            }                                                    
            
            $product['code'] = $product->product->code;
            $product['sku'] = $product->product->sku;
            $product['current_stock'] = $quantity;
            $product['item_value'] = number_format(($product->product->price), 2, '.', '');
            $product['stock_value'] = number_format(($quantity * $product->product->price), 2, '.', '');
            
            $orders = Order::whereIn('store_id',getStoreIds());
            
            if(!empty($request->from_date)){
                $orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00');
            }

            if(!empty($request->to_date)){
                $orders->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');
            }
            
           // $orders = $orders->get();     

           /* $reorder_point = 0;
            $reorder_amount = 0;
            foreach($orders as $order){
                
                $order_products = json_decode($order->order_items);
                if(count($order_products)>0){  
                    
                    $product_collection = collect($order_products);                    
                    $filtered = $product_collection->where('item_id', $product->product->id);
                    $filtered->all();
                    
                    if($filtered->count() > 0){ 
                        $reorder_point = $reorder_point + 1;  
                       
                        if(isset($filtered[0]))
                            $reorder_amount = $reorder_amount + $filtered[0]->unit_price;                       
                                               
                    }
                                                          
                }
            }
            
            $product['reorder_point'] = $reorder_point;
            $product['reorder_amount'] = $reorder_amount;*/
            $orders = $orders->pluck('id');
            $product_orders = ProductOrder::whereIn('order_id',$orders)->where('product_id',$product->product->id)->get();
            $product['reorder_point'] =$product_orders->sum('quantity');

            $product['reorder_amount'] = number_format($product_orders->sum(function ($product) {
                                            return $product->quantity * $product->price;
                                        }), 2, '.', '');
            return $product;
        });
        
        $products = $products->unique('product_id');            
       
        return Datatables::of($products)
            ->addColumn('name', function ($product) {
                if($product->product->is_variants)
                    return '<a href="'. url('company/products/'. Hashids::encode($product->product->id).'/edit') .'" class="text-info" target="_blank">'. $product->product->name .'</a>';
                else    
                    return '<a href="'. url('company/products/edit/'. Hashids::encode($product->product->id)) .'" class="text-info" target="_blank">'. $product->product->name .'</a>';
                    
            })           
            ->rawColumns(['name'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);          
    }
    


    public function getProductsGraph(Request $request){


            $start = strtotime($request->from_date);
            $stop = strtotime($request->to_date);

            $dates =[];
            $dates_pro = [];
            $dates_reorder = [];

            $t_total_orders=[];

            $products = Store_products::with(['product.store_products'])->whereIn('store_id',getStoreIds());
        
            if(!empty($request->store_id)){
                    $store_id = Hashids::decode($request->store_id)[0];  
                    $products->where('store_id',$store_id);
            }
        
            $products = $products->orderBy('id','asc')->get();




            $products->map(function ($product) use ($request,$start,$stop) { 
                $reorder_point = 0;
                for ($seconds=$start; $seconds<=$stop; $seconds+=86400)
                {
                    $orders = Order::whereIn('store_id',getStoreIds());
                    
                    if(!empty($request->from_date)){
                        $orders->where('created_at', '>=' , date("Y-m-d", $seconds).' 00:00:00')->where('created_at', '<=' , date("Y-m-d", $seconds).' 23:59:59');
                    }

                    $orders = $orders->pluck('id');

                    $product_orders = ProductOrder::whereIn('order_id',$orders)->where('product_id',$product->product->id)->get();
                    $reorder_point =$reorder_point+$product_orders->sum('quantity');
                }
                $product['reorder_point'] = $reorder_point;
                return $product;
            });


            $prose =[];
            foreach($products as $pro){

               $prose[$pro->product->name] =  $pro->reorder_point;
            }

            arsort($prose);
            $largest = array_slice($prose, 0, 10);

            foreach($largest as $key=>$value){
                $dates_pro[] = [$key,$value,false,false];
            }
            $report['dates'] = $dates;
            $report['products'] = $dates_pro;
            $status = $this->successStatus;
                
            return response()->json(['result' => $report], $status);

    }


        /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function customersReport()
    {              
        return view('company.reports.customers');
    } 
    
    /**
     * Display a listing of the resource.
     *
     * @return JSON
     */
    public function getCustomersReport(Request $request)
    {
        $store_customer_ids = StoreCustomer::where('company_id',Auth::id())->pluck('customer_id');

        $customers = Customer::with(['store','orders'])->whereIn('id', $store_customer_ids);
        
        if(!empty($request->store_id)){
            $store_id = Hashids::decode($request->store_id)[0];  
            $customers->where('store_id',$store_id);
        }
        
        $customers = $customers->orderBy('id','asc')->get();
        
        $customers->map(function ($customer) use ($request) { 
                
                $customer_orders = $customer->orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00')->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');                
            
                $customer['total_sales'] = $customer_orders->count('id');
                $customer['total_sale_amount'] = number_format($customer_orders->sum('order_total'), 2, '.', '');
                $customer['total_paid'] = number_format($customer_orders->sum('payment_received'), 2, '.', '');          
             
            unset($customer->orders);    
            return $customer;
        });
        
        $customers = $customers->filter(function($item) {
            return $item->total_sales != 0;
        });
       
        
        $customers = $customers->all();                         
        
        return Datatables::of($customers)
            ->addColumn('name', function ($customer) {
                return $customer->first_name .' '.$customer->last_name;               
            })
            ->addColumn('store_name', function ($customer) {
                return @$customer->store->name;                    
            })                              
            ->rawColumns(['name'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);            
    }


     public function getCustomersGraph(Request $request)
    {
        $store_customer_ids = StoreCustomer::where('company_id',Auth::id())->pluck('customer_id');

        $customers = Customer::with(['store','orders'])->whereIn('id', $store_customer_ids);
        
        if(!empty($request->store_id)){
            $store_id = Hashids::decode($request->store_id)[0];  
            $customers->where('store_id',$store_id);
        }
        
        $customers = $customers->orderBy('id','asc')->get();
        
        $customers->map(function ($customer) use ($request) { 
                
                $customer_orders = $customer->orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00')->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');                
            
                $customer['total_sales'] = $customer_orders->count('id');
                $customer['total_sale_amount'] = round($customer_orders->sum('order_total'),2);
                $customer['total_paid'] = round($customer_orders->sum('payment_received'),2);          
             
            unset($customer->orders);    
            return $customer;
        });
        
        $customers = $customers->filter(function($item) {
            return $item->total_sales != 0;
        });
       
        
        $customers = $customers->all();                         
        
        $dates_customer = [];
        $dates_sale_amount = [];
        $dates_paid_amount = [];

       foreach($customers as $row){

            $dates_customer[] = $row->first_name.' '.$row->last_name;
            $dates_sale_amount[] = $row->total_sale_amount;
            $dates_paid_amount[] = $row->total_paid;
       }      
       $report['dates_customer'] = $dates_customer;
       $report['dates_sale_amount'] = $dates_sale_amount;
       $report['dates_paid_amount'] = $dates_paid_amount; 

       $status = $this->successStatus;
                
        return response()->json(['result' => $report], $status); 
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function staffReport()
    {              
        return view('company.reports.staff');
    } 
    
    /**
     * Display a listing of the resource.
     *
     * @return JSON
     */
    public function getStaffReport(Request $request)
    {          
        $users = User::with(['orders','logs'])->whereIn('store_id', getStoreIds())->limit(1);          
        
        if(!empty($request->store_id)){

            $store_id = Hashids::decode($request->store_id)[0]; 

            $users->where('store_id',$store_id);
        }
        
        $users = $users->orderBy('id','asc')->get();
        $users->map(function ($user) use ($request) { 

                $total = 0;

                $user_orders = $user->orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00')->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');


                 $user_logs = $user->logs->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00')->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59'); 
            
                $user['total_sales'] = $user_orders->count('id');
                $user['total_sale_amount'] = number_format($user_orders->sum('order_total'), 2, '.', '');
                $user['total_discount'] = number_format($user_orders->sum('discount'), 2, '.', '');              
                $staff_orders = $user_orders->pluck('id');
                $user['total_tax'] = number_format($user_orders->sum('order_tax'), 2, '.', ''); 

                $user['user_orders'] = $user_orders;

                $tip_by_cash = 0;
                $tip_by_card = 0;

                for($i=0;$i<count($staff_orders);$i++){

                    $order_payment = OrderPayment::join('orders','order_payments.order_id','=','orders.id')->where('order_payments.order_id',$staff_orders[$i])->get();
                    if($order_payment){
                        if(count($order_payment)>1){
                            foreach($order_payment as $row){
                                $divided_tip =  $row->tip/count($order_payment);
                                if($row->payment_method==2){
                                    $tip_by_card = $tip_by_card+$divided_tip;
                                }else{
                                    $tip_by_cash = $tip_by_cash+$divided_tip;
                                }
                            }

                        }else{
                            foreach($order_payment as $row){
                               if($row->payment_method==2){
                                    $tip_by_card = $tip_by_card+$row->tip;
                                }else{
                                    $tip_by_cash = $tip_by_cash+$row->tip;
                                }
                            }
                            
                        }

                    }
                }
                
                
                $user['total_tip'] = number_format($user_orders->sum('tip'), 2, '.', '');   
                $user['total_tip_cash'] = number_format($tip_by_cash, 2, '.', '');   
                $user['total_tip_card'] = number_format($tip_by_card, 2, '.', '');   
                $total = $user_logs->sum('session_time');
                $hours = floor($total / 60);
                $minutes = $total % 60;
                $user['duration'] =  $hours.':'.$minutes;

            unset($user->orders);  
            unset($user->logs); 
            return $user;
        });
        

        
        $users = $users->all(); 

        return Datatables::of($users)
            ->addColumn('action', function ($user) {
                return '<a href="'. url('/company/reports/history/'.Hashids::encode($user->id)).'" class="text-success" data-toggle="tooltip" title="Staff History"><i class="fa fa-history"></i></a>';
            })                            
            ->rawColumns(['action'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);         

    }
    

    public function getStaffGraph(Request $request){

        $users = User::with(['orders','logs'])->whereIn('store_id', getStoreIds())->limit(1);          
        
        if(!empty($request->store_id)){

            $store_id = Hashids::decode($request->store_id)[0]; 

            $users->where('store_id',$store_id);
        }
        
        $users = $users->orderBy('id','asc')->get();
        $users->map(function ($user) use ($request) { 

                $total = 0;

                $user_orders = $user->orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00')->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');


                 $user_logs = $user->logs->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00')->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59'); 
            

                $user['total_sale_amount'] = round($user_orders->sum('order_total'), 2);

                $user['total_discount'] = round($user_orders->sum('discount'), 2);  

                $staff_orders = $user_orders->pluck('id');

                $user['user_orders'] = $user_orders;

                $user['total_tip'] = round($user_orders->sum('tip'), 2);   
                
            return $user;
        });
                
        $users = $users->all();


        $dates_staff = [];
        $dates_sale_amount = [];
        $dates_discounts = [];
        $dates_tip =[];

       foreach($users as $row){

            $dates_staff[] = $row->name;

            $dates_sale_amount[] = $row->total_sale_amount;

            $dates_tip[] = $row->total_tip;

            $dates_discounts[] = $row->total_discount;
       }      
       $report['dates_staff'] = $dates_staff;
       $report['dates_sale_amount'] = $dates_sale_amount;
       $report['dates_tip'] = $dates_tip;
       $report['dates_discounts'] = $dates_discounts;
       $status = $this->successStatus;
                
        return response()->json(['result' => $report], $status); 
    } 

    /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function productStocks($product_id)
    {    
        $product_id = Hashids::decode($product_id)[0];  
        
        $product = Product::find($product_id);
        
        return view('company.reports.stores_stock', compact('product'));
    }  
    
    /**
     * Display a listing of the resource.
     *
     * @return JSON
     */
    public function getReportsGraphApi(Request $request)
    {
        if(\Request::wantsJson()) 
        {
            
            $orders = Order::where('store_id',Auth::user()->store_id);
            
            if(empty($request->from_date) && empty($request->to_date)){
                $orders->where('created_at', '>=' , date('Y-m-d', strtotime('-1 years')).' 00:00:00');
                $orders->where('created_at', '<=' , date('Y-m-d').' 23:59:59');
            }else{
                if(!empty($request->from_date)){
                    $orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00');
                }

                if(!empty($request->to_date)){
                    $orders->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');
                }
            }
            
            $orders->groupBy('x')->orderBy('x', 'asc');
            
            switch ($request->type) {
                case 1: //Total Sales
                    $total_sales = $orders->get(array( DB::raw('Date(created_at) as x'), DB::raw('SUM(order_total) as y')));     
                    $total_transactions = $orders->get(array( DB::raw('Date(created_at) as x'), DB::raw('COUNT(id) as y')));                     
                    break;
                case 2: //Cash Sales    
                    $total_sales = $orders->where('payment_method',1)->get(array( DB::raw('Date(created_at) as x'), DB::raw('SUM(order_total) as y')));     
                    $total_transactions = $orders->where('payment_method',1)->get(array( DB::raw('Date(created_at) as x'), DB::raw('COUNT(id) as y')));     
                    break;
                case 3: //Card Sales   
                    $total_sales = $orders->where('payment_method',2)->get(array( DB::raw('Date(created_at) as x'), DB::raw('SUM(order_total) as y')));     
                    $total_transactions = $orders->where('payment_method',2)->get(array( DB::raw('Date(created_at) as x'), DB::raw('COUNT(id) as y')));     
                    break;
                case 4: // Avg Basket Size 
                    $total_sales = [];     
                    $total_transactions = $orders->get(array( DB::raw('Date(created_at) as x'), DB::raw('AVG(basket_size) as y')));     
                    break;
                case 5: // Avg Basket Value   
                    $total_sales = $orders->get(array( DB::raw('Date(created_at) as x'), DB::raw('AVG(order_total) as y')));     
                    $total_transactions = [];   
                    break;
                case 6: // Discount 
                    $total_sales = $orders->get(array( DB::raw('Date(created_at) as x'), DB::raw('SUM(discount) as y')));     
                    $total_transactions = $orders->get(array( DB::raw('Date(created_at) as x'), DB::raw('COUNT(id) as y')));     
                    break;                
                default:
                    $total_sales = [];
                    $total_transactions = [];
            }
            
            $report['total_sales'] = $total_sales;
            $report['total_transactions'] = $total_transactions;
            
            $status = $this->successStatus;

            return response()->json(['result' => $report], $status);
        }    
    }   



    public function getStaffHistory($user_id){

        $user_id = Hashids::decode($user_id)[0];                
        
        return view('company.reports.history', compact('user_id'));
    }  




    public function getStaffAjaxHistory($user_id){       
        
        $logs = LogActivity::getUserHistory($user_id);        
        
        return Datatables::of($logs)
            ->addColumn('name', function ($user) {
                if($user->name=='checkin_success'){
                    return 'Check In';
                }else{
                    return 'Check Out';
                }
            })
            ->addColumn('duration', function ($user) {
                if($user->name=='checkin_success'){
                    return '';
                }else{
                     $total = $user->session_time;
                     $hours = floor($total/60);
                     $minutes = $total % 60;

                     return $hours.':'.$minutes; 
                }
                
              // return $total;              
            })
            ->addColumn('created_at', function ($user) {
                return date('d M, Y h:i a',strtotime($user->created_at));               
            })
            ->make(true);        
    }   



    public function shiftReport(Request $request){


        $user_shifts = Shifts::with(['user'])->get();
        //return $user_shifts;

        $user_id=0;
        $user_name = '';

        if(!empty($request->employee_id)){

            $user_id = Hashids::decode($request->employee_id)[0];   
            $user_name = User::select('name')->where('id',$user_id)->first()->name;
        }
        
        return view('company.reports.shifts', compact('user_id','user_name'));
    }


    
    public function getShiftReport(Request $request){

       $user_shifts = Shifts::with(['user']);

       $store_ids = Store::where('company_id',Auth::id())->pluck('id')->toArray();
       $user_shifts->whereIn('store_id',$store_ids);
        
       if(!empty($request->store_id)){

            $store_id = Hashids::decode($request->store_id)[0];

            $user_shifts->where('store_id',$store_id);
        }

        if(!empty($request->user_id)){

            if($request->user_id!=0){

              $user_shifts->where('user_id',$request->user_id);   
            }
            
        }

//        if(!empty($request->from_date)){
//
//           $user_shifts->where('start_time', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00');
//        }
//
//        if(!empty($request->to_date)){
//
//            $user_shifts->where('start_time', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');
//        }

        $user_shifts = $user_shifts->orderBy('id','desc')->get();

        $user_shifts->map(function ($user) use ($request) { 

                $total = 0;

                $user['date'] = date('Y-m-d', strtotime($user->start_time));

                $user['name'] = $user->user->name;

                $user['transaction_balance'] = number_format(($user->transaction), 2, '.', '');

                $user['total_balances'] = number_format(($user->total_balance), 2, '.', '');

                $user['expected'] = number_format(($user->starting_balance+$user->transaction), 2, '.', '');

                $user['variance'] = number_format((($user->starting_balance+$user->transaction)-$user->total_balance), 2, '.', '');            


                $store_break_time = $user->user->store->break_time;
                if(isset($store_break_time)){
                    $arr = explode('-',$store_break_time);

                    $break_start_time = date("H:i:s", strtotime($arr[0]));
                    $break_end_time = date("H:i:s", strtotime($arr[1]));


                    //check if employee clocked out after break time

                    $shift_start_time = date("H:i:s",strtotime($user->start_time));
                    $shift_end_time = date("H:i:s",strtotime($user->end_time));

                    if(isset($shift_start_time) && isset($shift_end_time)){
                        $shift_start_time = Carbon::createFromFormat('H:i:s', $shift_start_time);
                        $shift_end_time = Carbon::createFromFormat('H:i:s', $shift_end_time);
                        $break_start_time = Carbon::createFromFormat('H:i:s', $break_start_time);
                        $break_end_time = Carbon::createFromFormat('H:i:s', $break_end_time);
                        $this->checkBreakTime($break_start_time,$break_end_time,$shift_start_time,$shift_end_time,$user);
                    }


                }

                $total = $user->minutes;


                $hours = floor($total / 60);

                $minutes = $total % 60;

                $user['working_hours'] =  $hours.':'.$minutes;

                $decoded_log = json_decode($user->logs);

                $user['checkin_time'] = isset($decoded_log->checkin_time)?$decoded_log->checkin_time:'';
                $user['checkout_time'] = isset($decoded_log->checkout_time)?$decoded_log->checkout_time:'';
               unset($user->user);

               return $user;
        });
        

        
        $user_shifts = $user_shifts->all(); 

        return Datatables::of($user_shifts)
            ->addColumn('action', function ($shift) {
                return '<a href="'. url('/company/reports/shift-log/'.Hashids::encode($shift->id)).'" class="text-success" data-toggle="tooltip" title="Shift Logs"><i class="fa fa-history"></i></a>';
            })                            
            ->rawColumns(['action'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);        
    }


    public function getShiftLogs($shift_id){

        $shift_id = Hashids::decode($shift_id)[0];                
        
        return view('company.reports.shift_logs', compact('shift_id'));
    }


    public  function getShiftAjaxLogs($shift_id){       
        
        $logs = LogActivity::getShiftLogs($shift_id);        
        


        $logs->map(function ($log){ 

            $decoded_log = json_decode($log->logs);

            $checkin_arr = [];
            $checkout_arr = [];

            foreach($decoded_log as $decode){
                $checkin_arr[] = $decode['checkin_time'];
                $checkout_arr[] = $decode['checkout_time'];
            }
            $log['checkin_arr'] = $checkin_arr;
            $log['checkout_arr'] = $checkout_arr;
            return $log;
        });


        return Datatables::of($logs)
            ->addColumn('chec', function ($log) {
                if($log->decoded_log=='checkin_success'){
                    return 'Check In';
                }else{
                    return 'Check Out';
                }
            })
            ->addColumn('duration', function ($user) {
                if($user->name=='checkin_success'){
                    return '';
                }else{
                     $total = $user->session_time;
                     $hours = floor($total/60);
                     $minutes = $total % 60;

                     return $hours.':'.$minutes; 
                }
                
              // return $total;              
            })
            ->addColumn('created_at', function ($user) {
                return date('d M, Y h:i a',strtotime($user->created_at));               
            })
            ->make(true);    
    }   





    public function checkBreakTime($break_start_time,$break_end_time,$shift_start_time,$shift_end_time, &$shift){

        //shift start time check before break

        if($shift_start_time < $break_start_time && $shift_end_time > $break_end_time){
            $minutes = $break_start_time->diffInMinutes($break_end_time);
            $shift->minutes = abs($shift->minutes - $minutes);
        }

        //shift start time check after break
        if($shift_start_time > $break_end_time){
            //do nothing
        }

        //shift start time lies in break time
        if(($shift_start_time >= $break_start_time && $shift_start_time <= $break_end_time) && ($shift_end_time >= $break_end_time)){
            $minutes = $shift_start_time->diffInMinutes($break_end_time);
            $diff = $shift->minutes - $minutes;
            $shift->minutes = abs($diff);
        }
        //shift end time lies in break time
        if($shift_end_time >= $break_start_time && $shift_end_time <= $break_end_time){
            //if shift start and end time both lies in break time
            if(($shift_start_time >= $break_start_time && $shift_start_time <= $break_end_time) && ($shift_end_time >= $break_start_time && $shift_end_time <= $break_end_time)){
                $minutes = $shift_start_time->diffInMinutes($shift_end_time);
                $diff = $shift->minutes - $minutes;
                $shift->minutes = abs($diff);
            } else
                $minutes = $break_start_time->diffInMinutes($shift_end_time);
                $diff = $shift->minutes - $minutes;
                $shift->minutes = abs($diff);
            }

        }




}
