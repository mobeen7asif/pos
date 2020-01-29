<?php

namespace App\Http\Controllers\Company;

use App\FloorTable;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Libs\CustomerAuth;
use App\OrderPayment;
use App\ProductOrder;
use App\StoreCustomer;
use App\UserDevice;
use Illuminate\Support\Facades\Log;
use App\Categories;
use App\MealType;
use App\Stock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Response;
use Image;
use File;
use Session;
use Alert;
use Datatables;
use Hashids;
use App\User;
use App\Product;
use App\Product_images;
use App\Store;
use App\Store_products;
use App\Category_products;
use App\Product_variant;
use App\Product_tag;
use App\Product_stock;
use App\Variant;
use App\Order;
use App\Shipping_option;
use App\Tax_rates;
use App\Customer;
use App\Supplier;
class OrderController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    public $notFoundStatus = 404;
    public $validationStatus = 422;

    /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function index(Request $request)
    {

//        $orders = Order::with(['store','customers'])->whereIn('store_id',getStoreIds())->where('customer',1536169909)->get();
//        dd($orders->count());


    	$id = 0;
    	$name = '';
        $type=0;
        //Order::whereIn('reference',[620180504183744,620180504185929,920180504123918,620180504183053,620180509174703,620180509175151])->delete();
        if($request->customer_id){

        	$id = base64_decode($request->customer_id);
        	$customer_detail = Customer::where('id',$id)->first();
        	if($customer_detail){
        		$name = $customer_detail->first_name.' '.$customer_detail->last_name;
        	}
            $type=1;

        }
         if($request->supplier_id){

            $id = Hashids::decode($request->supplier_id)[0];
            $supplier_detail = Supplier::where('id',$id)->first();
            if($supplier_detail){
                $name = $supplier_detail->name;
            }
            $type=2;

        }

       
        return view('company.orders.index',compact('id','name','type'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getOrders(Request $request,$customer_id,$type)
    {
        //$store_ids = Store::where('company_id',Auth::id())->pluck('id');
        $product_orders_det = [];

       if($type==2){

            $product_orders_det = Product::join('product_orders','products.id','=','product_orders.product_id')->where('products.supplier_id',$id)->pluck('product_orders.order_id');
       }

       $orders = Order::with(['store','customers'])->whereIn('store_id',getStoreIds());

        if($request->order_type==1){
            $orders->where('order_id',0);

        }elseif($request->order_type==2){
            $orders->where('order_id','!=',0);
        }
        if($type==1){
        	$orders->where('customer',$customer_id);

        }else if($type==2){
            $orders->whereIn('id',$product_orders_det);
        }

        $orders = $orders->orderBy('updated_at','desc')->get();

        return Datatables::of($orders)
            ->addColumn('date', function ($order) {
                return date('d/m/Y', strtotime($order->created_at));
            })
            ->addColumn('store_name', function ($order) {
                return $order->store->name;
            })
            ->addColumn('biller_name', function ($order) {
                if(isset($order->biller_detail)){
                    $biller = json_decode($order->biller_detail);
                    if(isset($biller->name)){
                        return $biller->name;
                    } else {
                        return $biller->first_name;
                    }
                } else {
                    return '';
                }
            })
            ->addColumn('customer', function ($order) {
                if($order->customers){
                    return $order->customers->first_name.' '.$order->customers->last_name;
                } return '';

            })
            ->addColumn('order_status', function ($order) {
                if($order->order_status == 1){
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-success" data-toggle="tooltip" title="Completed">Completed</a>';
                }else if($order->order_status == 0) {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Pending">Pending</a>';
                } else {
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Pending">Rejected</a>';
                }
            })
            ->addColumn('payment_status', function ($order) {
                if($order->order_status == 1){
                    $status = 'Paid';
                    $class = 'btn btn-xs btn-success';
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-success" data-toggle="tooltip" title="Payment Status">'.$status.'</a>';
                }else {
                    $status = 'Pending';
                    $class = 'btn btn-xs btn-danger';
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Payment Status">'.$status.'</a>';
                }
//                else {
//                    $order_payment = OrderPayment::where('order_id',$order->id)->first();
//                    if($order_payment){
//                        if($order_payment->payment_status == 1){
//                            $status = 'Partial';
//                        } else if($order_payment->payment_status == 2){
//                            $status = 'Full';
//                        } else {
//                            $status = 'Pending';
//                        }
//                    }
//                }
//
//                    if($order->order_status == 2) {
//                    $status = 'Partial';
//                } else {
//                    $status = 'Pending';
//                }
////                $order_payment = OrderPayment::where('order_id',$order->id)->first();
////                if($order_payment){
//                    if($order_payment->payment_status == 1){
//                        $status = 'Partial';
//                    } else if($order_payment->payment_status == 2){
//                        $status = 'Full';
//                    } else {
//                        $status = 'Pending';
//                    }
////                } else {
////                    $status = 'Not available';
////
            })
            ->addColumn('order_type', function ($order) {
                if($order->order_id == 0){
                    return '<a href="javascript:void(0)" class= data-toggle="tooltip" title="Sales">Sales</a>';
                }else if($order->order_id != 0){
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Sales Return">Return</a>';
                }
            })
            ->addColumn('sub_total', function ($order) {
                return number_format($order->sub_total, 2);
            })
            ->addColumn('order_total', function ($order) {
                return number_format($order->order_total, 2);
            })
//            ->addColumn('deliver_status', function ($order) {
//                if($order->deliver_status == null){
//                    return '<a href="javascript:void(0)" class="btn btn-xs btn-success" data-toggle="tooltip" title="Deliver Status">Not Deliver</a>';
//                }else{
//                    return '<a href="javascript:void(0)" class="btn btn-xs btn-info" data-toggle="tooltip" title="Deliver Status">$order->deliver_status</a>';
//                }
//            })
            ->addColumn('action', function ($order) {
                return '<a href="invoice/'. Hashids::encode($order->id).'" class="text-success btn-order" data-toggle="tooltip" title="View Order" id="'.$order->id.'"><i class="fa fa-eye"></i></a>';
            })
            ->rawColumns(['date', 'store_name', 'biller_name', 'order_status', 'payment_status', 'order_type', 'action'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);

    }

    //search orders
    public function searchOrders(Request $request)
    {
        //  Order::destroy([563]);
        if(\Request::wantsJson())
        {
            //Order::where('reference',220180503144816)->delete();

            $store_id = Auth::user()->store_id;
            if(isset($request->customer)){
                $orders = Order::with(['store','customers.orders','shipping_option'])->where('store_id',$store_id)
                    ->where('customer',$request->customer);
            }
            else {
                $orders = Order::with(['store','customers.orders','shipping_option'])->where('store_id',$store_id);
            }


            if(!empty($request->order_type)){
                if($request->order_type == 'sales'){
                    $orders->where('order_id',0);
                }elseif($request->order_type == 'returns'){
                    $orders->where('order_id', '!=', 0);
                }

            }

            if(!empty($request->q)){

                if(is_numeric($request->q))
                    $orders->whereRaw('reference::TEXT LIKE '."'%$request->q%'");
                else
                    $orders->customer($request->q);

                // $test = $orders->whereRaw('reference::TEXT LIKE '."'$request->q%'");
                // if(!$test->first())

            }

            if(!empty($request->reference)){
                $orders->whereRaw('reference::TEXT LIKE '."'%$request->reference%'");
            }

            if(!empty($request->customer)){
                $orders->where('customer',$request->customer);
            }

            if(!empty($request->from_date)){
                $orders->where('created_at', '>=' , date('Y-m-d', strtotime($request->from_date)).' 00:00:00');
            }

            if(!empty($request->to_date)){
                $orders->where('created_at', '<=' , date('Y-m-d', strtotime($request->to_date)).' 23:59:59');
            }

            $orders->orderBy('updated_at','desc');

            // dd($orders->get()->toArray());

            if(!empty($request->limit)){
                if($request->limit=='all')
                    $orders = $orders->paginate($orders->count());
                else
                    $orders = $orders->paginate($request->limit);
            }else
                $orders = $orders->paginate(10);

            $orders->setCollection(
                $orders->getCollection()->map(function ($order) {

                    if($order->biller_detail != "")
                        $order['biller_detail'] = json_decode($order->biller_detail);

                    if($order->shipping_detail != "")
                        $order['shipping_detail'] = json_decode($order->shipping_detail);
                    else
                        $order['shipping_detail'] = NULL;

                    if($order->order_items != ""){
                        $order_items = json_decode($order->order_items);

                        foreach($order_items as $order_item){

                            if(isset($order_item->tax_details))
                                $order_item->tax_details = json_decode($order_item->tax_details);
                            if(isset($order_item->item_combos))
                                $order_item->item_combos = json_decode($order_item->item_combos);
                            if(isset($order_item->item_modifiers))
                                $order_item->item_modifiers = json_decode($order_item->item_modifiers);
                        }

                        $order['order_items'] = $order_items;
                    }

                    if($order->return_ids != "")
                        $order['return_orders'] = json_decode($order->return_ids);
                    else
                        $order['return_orders'] = NULL;

                    unset($order->return_ids);

                    if($order->customers->orders->count()>0){
                        $order->customers->total_sales = number_format($order->customers->orders->sum('order_total'),2);
                        $order->customers->total_visits = $order->customers->orders->count();
                        $order->customers->last_visit = $order->customers->orders->last()->created_at->format('d/m/Y');
                    }else{
                        $order->customers->total_sales = 0;
                        $order->customers->total_visits = 0;
                        $order->customers->last_visit = '';
                    }
                    unset($order->customers->orders);

                    return $order;

                })
            );

            $response['orders'] = $orders;

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
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JSON Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'discount' => 'required',
            'order_status' => 'required',
            'payment_status' => 'required',
            'sub_total' => 'required',
            'service_fee' => 'required',
            'order_total' => 'required',
            'order_items' => 'required',
            'customer' => 'required',
            'shipping_id' => 'required',
            'reference' => 'required',
            'payment_received' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->validationStatus);
        }
        $customer_route = $request->route()->getName();
        $requestData = $request->all();

        $user = User::select(['id','name','profile_image','email','phone','store_id'])->find(Auth::id());
        $order_data['reference'] = $request->reference;

        //check if its new or order or old order
        $check = false;
        $check_order = Order::where('reference', $request->reference)->first();
        if($check_order){
            $check = true;
        }
        if($check){
            //call order update stock function
            if(isset($customer_route)){
                $customer = CustomerAuth::customer();
                $this->orderUpdateStockManage($check_order->id, json_decode($request->order_items),$customer->id,$request->input('store_id'));
            } else {
                $this->orderUpdateStockManage($check_order->id, json_decode($request->order_items));
            }

        }
        $order = Order::firstOrNew($order_data);
        if(isset($customer_route)){
            $store = Store::find($request->input('store_id'));
            $company = $store->company;


            $customer = CustomerAuth::customer();
            $order->biller_id = $customer->id;
            //$order->biller_detail = $customer->toJson();
            $order->store_id = $request->input('store_id');
            $order->is_mobile_order = 1;

        } else {
            $company = Auth::user()->store->company;

            $order->biller_id = $user->id;
            $order->biller_detail = $user->toJson();
            $order->store_id = $user->store_id;
        }


        $order->order_id = 0;
        $order->discount = $request->discount;
        $order->order_status = $request->order_status;
        $order->payment_status = $request->payment_status;
        $order->payment_method = $request->payment_method;
        $order->payment_detail = $request->payment_detail;
        $order->payment_received = $request->payment_received;
        $order->order_note = $request->order_note;
        $order->staff_note = $request->staff_note;
        $order->order_total = $request->order_total;
        $order->service_fee = $request->service_fee;
        $order->sub_total = $request->sub_total;
        $order->order_tax = isset($request->vat_total) ? $request->vat_total : 0;
        $order->customer = $request->customer;
        $order->shipping_id = $request->shipping_id;
        if(isset($request->tip)){
            $order->tip = $request->tip;
        }
        $shipping_option = Shipping_option::select(['id','name','cost','company_id'])->find($request->shipping_id);
        if($shipping_option)
            $order->shipping_detail = $shipping_option->toJson();

        // save order items
        if(!empty($request->order_items))
        {
            $items_data = [];
            $basket_size = 0;
            $cost_of_goods  = 0;

            $order_items = json_decode($request->order_items);
            if($customer_route){
                //Log::info(['order_items' => $order_items]);
                foreach($order_items as $key => $item){
                    if(isset($item->item_id)){
                        //saving meal object in items_data
                        if($company->company_type == 1){
                            if($item->delete_status == 0) {
                                if(isset($item->item_meal_id)){
                                    $meal_type = MealType::find($item->item_meal_id);
                                    $items_data[$key]['meal_type'] = $meal_type;
                                }
                            }
                        }
                        $product_id = $item->item_id;

                        $product = Product::with(['product','product_combos.product'])->find($product_id);
                        if($item->delete_status == 0) {
                            if ($product) {
                                $items_data[$key]['item_id'] = $product->id;
                                $items_data[$key]['item_image'] = getProductDefaultImage($product->id);
                                $items_data[$key]['item_name'] = $product->name;
                                if ($product->product_id == 0) {
                                    $items_data[$key]['unit_cost'] = $product->cost;
                                    $items_data[$key]['unit_price'] = $product->price;
                                } else {
                                    if ($product->is_main_price == 0) {
                                        $items_data[$key]['unit_cost'] = $product->cost;
                                        $items_data[$key]['unit_price'] = $product->price;
                                    } elseif ($product->is_main_price == 1) {
                                        $items_data[$key]['unit_cost'] = $product->product->cost;
                                        $items_data[$key]['unit_price'] = $product->product->price;
                                    }
                                }

                                $items_data[$key]['item_discount'] = $item->item_discount;
                                $items_data[$key]['quantity'] = $item->quantity;
                                if(isset($item->item_tagline)){
                                    $items_data[$key]['item_tagline'] = $item->item_tagline;
                                } else {
                                    $items_data[$key]['item_tagline'] = '';
                                }
                                if(isset($item->item_note)){
                                    $items_data[$key]['item_note'] = $item->item_note;
                                }
                                else {
                                    $items_data[$key]['item_note'] = '';
                                }


                                if ($product->product_id == 0) {
                                    $tax_rate_id = $product->tax_rate_id;
                                } else {
                                    if ($product->is_main_tax == 0) {
                                        $tax_rate_id = $product->tax_rate_id;
                                    } elseif ($product->is_main_tax == 1) {
                                        $tax_rate_id = $product->product->tax_rate_id;
                                    }
                                }

                                $tax = Tax_rates::select(['id', 'code', 'name', 'rate'])->find($tax_rate_id);
                                if ($tax) {
                                    $items_data[$key]['tax_details'] = $tax->toJson();
                                }

                                if ($product->type == 2) {
                                    $combo_collection = $product->product_combos->map(function ($item) {
                                        return ['id' => $item->product->id, 'name' => $item->product->name, 'code' => $item->product->code, 'sku' => $item->product->sku];
                                    });

                                    $items_data[$key]['item_combos'] = $combo_collection->toJson();
                                }

                                if ($product->is_modifier == 1) {
                                    if (!empty($item->item_modifiers)) {
                                        $item_modifiers = explode(',', $item->item_modifiers);
                                        $modifier_data_collection = collect([]);
                                        foreach ($item_modifiers as $modifier_id) {
                                            $modifier = Product::find($modifier_id);
                                            if ($modifier) {
                                                $modifier_data['id'] = $modifier->id;
                                                $modifier_data['name'] = $modifier->name;
                                                $modifier_data['cost'] = $modifier->cost;
                                                $modifier_data['price'] = $modifier->price;

                                                $cost_of_goods = $cost_of_goods + $modifier->cost;

                                                $modifier_data_collection->push($modifier_data);
                                            }

                                        }

                                        $items_data[$key]['item_modifiers'] = json_encode($modifier_data_collection);
                                    }
                                }

                                $basket_size = $basket_size + $item->quantity;
                                $cost_of_goods = $cost_of_goods + ($product->cost * $item->quantity);

                                // sync products
                                if($check == false){
                                    updateSyncData('product', $product->id);
                                }
                            }
                        }
                    }

                }
                $order->basket_size = $basket_size;
                $order->cost_of_goods = $cost_of_goods;
                $order->order_items = json_encode($items_data,true);
            }
            else {
                $items_data = [];
                $cost_of_goods = 0;
                $basket_size = 0;
                foreach($order_items as $key => $item){
                    if(isset($item->item_id)){
                        //saving meal object in items_data
                        if($company->company_type == 1){
                            if($item->delete_status == 0) {
                                if (isset($item->item_meal_id)) {
                                    $meal_type = MealType::find($item->item_meal_id);
                                    $items_data[$key]['meal_type'] = $meal_type;
                                }
                            }
                        }
                        $product_id = $item->item_id;

                        $product = Product::with(['product','product_combos.product'])->find($product_id);
                        if($item->delete_status == 0) {
                            if ($product) {
                                $items_data[$key]['item_id'] = $product->id;
                                $items_data[$key]['item_image'] = getProductDefaultImage($product->id);
                                $items_data[$key]['item_name'] = $product->name;
                                $items_data[$key]['check_id'] = $item->check_id;
                                if ($product->product_id == 0) {
                                    if($item->split_item == 0){
                                        $items_data[$key]['unit_price'] = $product->price;
                                    } else {
                                        $items_data[$key]['unit_price'] = $item->unit_price;
                                    }
                                    $items_data[$key]['unit_cost'] = $product->cost;

                                } else {
                                    if ($product->is_main_price == 0) {
                                        if($item->split_item == 0){
                                            $items_data[$key]['unit_price'] = $product->price;
                                        } else {
                                            $items_data[$key]['unit_price'] = $item->unit_price;
                                        }
                                        $items_data[$key]['unit_cost'] = $product->cost;
                                        //$items_data[$key]['unit_price'] = $product->price;
                                    } elseif ($product->is_main_price == 1) {
                                        if($item->split_item == 0){
                                            $items_data[$key]['unit_price'] = $product->price;
                                        } else {
                                            $items_data[$key]['unit_price'] = $item->unit_price;
                                        }
                                        $items_data[$key]['unit_cost'] = $product->product->cost;
                                        //$items_data[$key]['unit_price'] = $product->product->price;
                                    }
                                }

                                $items_data[$key]['item_discount'] = $item->item_discount;
                                $items_data[$key]['quantity'] = $item->quantity;
                                if(isset($item->item_tagline)){
                                    $items_data[$key]['item_tagline'] = $item->item_tagline;
                                } else {
                                    $items_data[$key]['item_tagline'] = '';
                                }
                                if(isset($item->item_note)){
                                    $items_data[$key]['item_note'] = $item->item_note;
                                }
                                else {
                                    $items_data[$key]['item_note'] = '';
                                }


                                if ($product->product_id == 0) {
                                    $tax_rate_id = $product->tax_rate_id;
                                } else {
                                    if ($product->is_main_tax == 0) {
                                        $tax_rate_id = $product->tax_rate_id;
                                    } elseif ($product->is_main_tax == 1) {
                                        $tax_rate_id = $product->product->tax_rate_id;
                                    }
                                }

                                $tax = Tax_rates::select(['id', 'code', 'name', 'rate'])->find($tax_rate_id);
                                if ($tax) {
                                    $items_data[$key]['tax_details'] = $tax->toJson();
                                }

                                if ($product->type == 2) {
                                    $combo_collection = $product->product_combos->map(function ($item) {
                                        return ['id' => $item->product->id, 'name' => $item->product->name, 'code' => $item->product->code, 'sku' => $item->product->sku];
                                    });

                                    $items_data[$key]['item_combos'] = $combo_collection->toJson();
                                }

                                if ($product->is_modifier == 1) {
                                    if (!empty($item->item_modifiers)) {
                                        $item_modifiers = explode(',', $item->item_modifiers);
                                        $modifier_data_collection = collect([]);
                                        foreach ($item_modifiers as $modifier_id) {
                                            $modifier = Product::find($modifier_id);
                                            if ($modifier) {
                                                $modifier_data['id'] = $modifier->id;
                                                $modifier_data['name'] = $modifier->name;
                                                $modifier_data['cost'] = $modifier->cost;
                                                $modifier_data['price'] = $modifier->price;

                                                $cost_of_goods = $cost_of_goods + $modifier->cost;

                                                $modifier_data_collection->push($modifier_data);
                                            }

                                        }

                                        $items_data[$key]['item_modifiers'] = json_encode($modifier_data_collection);
                                    }
                                }

                                $basket_size = $basket_size + $item->quantity;
                                $cost_of_goods = $cost_of_goods + ($product->cost * $item->quantity);

                                // sync products
                                if($check == false){
                                    updateSyncData('product', $product->id);
                                }
                            }
                        }
                    }

                }
                //merge items in case of check orders
                if($check && $order->is_mobile_order == 0){
                    $prev_items_data = json_decode($order->order_items,true);
                    $old_items_size = count($prev_items_data);
                    $items_size = count($items_data);
                    $new_items_array = [];


                    for($i = 0; $i < $old_items_size; $i++){
                        for($c = 0; $c < $items_size; $c++){
                            if(isset($prev_items_data[$i]['check_id']) && isset($items_data[$c]['check_id'])){
                                if($prev_items_data[$i]['check_id'] == $items_data[$c]['check_id']){
                                    unset($prev_items_data[$i]);
                                }
                            }
                        }
                    }

                    foreach($prev_items_data as $prev_data){
                        $new_items_array[] = $prev_data;
                    }
                    foreach ($items_data as $item_data){
                        $new_items_array[] = $item_data;
                    }
                    //Log::info(['old  items' => $prev_items_data]);
                    //Log::info(['new  items' => $items_data]);
                    //Log::info(['all items' => $new_items_array]);
//                    $order->basket_size = $basket_size + $order->basket_size;
//                    $order->cost_of_goods = $cost_of_goods + $order->cost_of_goods;
                    $order->order_items = json_encode($new_items_array,true);
                }
                else {
//                    $order->basket_size = $basket_size;
//                    $order->cost_of_goods = $cost_of_goods;
                    $order->order_items = json_encode($items_data,true);
                }

            }

        }

        $order->dine_option = $request->dine_option;
        if($company->company_type == 1){
            if($request->dine_option == 'yes'){
                $order->table_data = $request->table;
                $table_data = json_decode($request->table);
                //check if table is booked
//                if($check){
//                    //check if table is changed
//                    $update = $this->addTable($table_data,$order);
//                    if(!$update){
//                        $response['error'] = 'This table is already booked';
//                        return response()->json(['result'=>$response], $this->errorStatus);
//                    }
//                }
//                else {
//                    $check_table = FloorTable::where('table_id',$table_data->table_info->id)->where('book_status',1)->first();
//                    if($check_table){
//                        $response['error'] = 'This table is already booked';
//                        return response()->json(['result'=>$response], $this->errorStatus);
//                    }
//                    else {
//                        //update order_id in floor_tables
//                        $table = FloorTable::where('table_id',$table_data->table_info->id)->first();
//                        $table->order_id = $request->reference;
//                        $table->book_status = 1;
//                        $table->save();
//                        updateSyncData('table',$table_data->table_info->id,Auth::user()->store->id);
//                    }
//                }
                $table = FloorTable::where('table_id',$table_data->table_info->id)->first();
                $table->order_id = $request->reference;
                $table->book_status = 1;
                $table->save();
                updateSyncData('table',$table_data->table_info->id,Auth::user()->store->id);
            }
        }

        if(!$customer_route){
            $global_discount = $request->input('global_discount');
            if(isset($global_discount)){
                $order->global_discount = $global_discount;
            } else {
                $order->global_discount = 0;
            }
        }

        $order->save();


        //saving payments of  worker side
        if(isset($request->payment)){
            $order_payment = new OrderPayment();
            $order_payment->order_id = $order->id;
            $order_payment->payment_method = $request->payment_method;
            $order_payment->payment_status = $request->payment_status;
            $order_payment->payment_type = $request->payment_status;
            $order_payment->payment_received = $request->payment_received;
            $order_payment->order_total = $request->order_total;
            $order_payment->payment_detail = $request->payment_detail;
            $order_payment->transaction_detail = $request->transaction_detail;
            $order_payment->check_id = $request->check_id;

            //saving transaction_id in a seperate column
            if(isset($request->transaction_detail)) {
                $details = json_decode($request->transaction_detail,true);
                foreach($details as $key => $value){
                    if($key == 'transaction_id'){
                        $order_payment->transaction_id = $value;
                    }
                }
            }
            if(isset($request->tip)){
                $order_payment->tip = $request->tip;
            } else {
                $order_payment->tip = 0;
            }
            $order_payment->save();

            if($company->company_type == 1){
                if($request->dine_option == 'yes'){
                    if($request->order_status == 1){
                        //$order->table_data = $request->table;
                        $table_data = json_decode($order->table_data);
                        $table = FloorTable::where('table_id',$table_data->table_info->id)->first();
                        $table->order_id = 0;
                        $table->book_status = 0;
                        $table->is_mobile_order = 0;
                        $table->save();
                        updateSyncData('table',$table_data->table_info->id,Auth::user()->store->id);
                    }

                }
            }
        }
        if($order){

            // sync order
            updateSyncData('order',$order->id);


            //sendOrderEmail($order->id);
            //call this syncer when order is created
            if(isset($customer_route)){
                if($check == false){
                    updateOrderProductsStock($order->id,$customer->id);
                }
            } else {
                if($check == false){
                    updateOrderProductsStock($order->id);
                }
            }


            //saving customer in store_customers table
            if(isset($customer_route)){
                $check_customer = StoreCustomer::where(['store_id' => $store->id,'company_id' => $store->company->id,'customer_id' => $customer->id])->first();
                if($check_customer == null){
                    $check_customer = new StoreCustomer();
                }
                $check_customer->customer_id = $customer->id;
                $check_customer->company_id = $store->company->id;
                $check_customer->store_id = $store->id;
                $check_customer->save();

            }

            $response['reference'] = $order->reference;
            $status = $this->successStatus;
            $response['success'] =  'You have successfully create order.';
        }else{
            $status = $this->errorStatus;
            $response['record_id'] = 0;
            $response['error'] =  'Order not successfully created.';
        }
        if(!isset($customer_route)){
            $this->calculateCostOfGoods($order);
        }
        if(isset($customer_route)){
            if($company->company_type == 1){
                //saving table data in case of customer order
                $table = FloorTable::where('table_id',$request->input('table_id'))->first();

                $table_data['waiter_id'] = $table->waiter_id;
                $table_data['floor_id'] = $table->floor_id;
                $table_data['table_info']['sit_cap'] = $table->seats;
                $table_data['table_info']['name'] = $table->name;
                $table_data['table_info']['id'] = $table->table_id;
                $table_data['table_info']['table_number'] = $table->table_number;
                $table_data = json_encode($table_data);
                $order->table_data = $table_data;
                $order->save();

                $table->order_id = $order->reference;
                $table->save();
                updateSyncData('table',$table->id,$store->id);

            }
        }

        //save order products in product_order table
        $this->saveProductOrder($order->id,$order->order_items);

        //send notification to workers in case of customer order
        if(isset($customer_route)){
            $this->sendNotifications($order,$store,$request->input('table_id'));
        }
        updateSyncData('customer',$order->customer);
        //update tip in order table
        $total_tip = OrderPayment::where('order_id',$order->id)->sum('tip');
        $order->tip = $total_tip;
        $order->save();
        return response()->json(['result'=>$response], $status);
    }
    public function calculateCostOfGoods(&$order){
        if(isset($order->order_items)){
            $basket_size = 0;
            $cost_of_goods = 0;
            $order_items = json_decode($order->order_items);
            foreach ($order_items as $item){
                $product = Product::find($item->item_id);
                if ($product->is_modifier == 1) {
                    if (!empty($item->item_modifiers)) {
                        //$item_modifiers = explode(',', json_decode($item->item_modifiers));
                        $item_modifiers = json_decode($item->item_modifiers);
                        $modifier_data_collection = collect([]);
                        foreach ($item_modifiers as $modifier) {
                            //$modifier = Product::find($modifier_id);
                            if ($modifier) {
                                $modifier_data['id'] = $modifier->id;
                                $modifier_data['name'] = $modifier->name;
                                $modifier_data['cost'] = $modifier->cost;
                                $modifier_data['price'] = $modifier->price;

                                $cost_of_goods = $cost_of_goods + $modifier->cost;

                                $modifier_data_collection->push($modifier_data);
                            }

                        }
                    }
                }
                $basket_size = $basket_size + $item->quantity;
                $cost_of_goods = $cost_of_goods + ($product->cost * $item->quantity);
            }
            $order->cost_of_goods = $cost_of_goods;
            $order->basket_size = $basket_size;
            $order->save();
        }
    }

    public function addTable($table_data,$order){
        $old_table_id  = $table_data->table_info->old_table_id;
        $table_id = $table_data->table_info->id;
        if($old_table_id != 0){
            $check_table = FloorTable::where('table_id',$table_data->table_info->id)->where('book_status',1)->first();
            if($check_table){
                return false;
            } else {
                $table = FloorTable::where(['table_id' => $old_table_id])->update(['book_status' => 0,'order_id' => 0]);
                $table = FloorTable::where(['table_id'=>$table_id])->first();
                $table->book_status = 1;
                $table->save();
                $store = $table->floor->store;
                updateSyncData('table',$table->table_id,$store->id);
            }
        }
        return true;
    }

    public function sendNotifications($order,$store,$table_id = 0){
        $company = $store->company;
        //if($company->company_type == 1){
        //$table = FloorTable::where('table_id',$table_id)->first();
        //if($table){
            $store_admins = User::where('store_id',$store->id)->where('role_name','Store Admin')->get();
            foreach ($store_admins as $admin) {
                $devices = UserDevice::where('user_id', $admin->id)->get();
                foreach ($devices as $device) {
                    //Log::info(['device' => $device->device_token]);
                    $res = array();
                    $res['notification']['title'] = 'New Order';
                    $res['notification']['body'] = 'You have received a new order';
                    $res['notification']['obj'] = $order->reference;
                    $res['notification']['order'] = $order;
                    sendAndroidNotification($res, $device);
                }
            }
            //}
            //$waiter = User::where(['id' => $table->waiter_id, 'is_login' => 1])->first();
//            if($waiter){
//                $devices = UserDevice::where('user_id',$table->waiter_id)->get();
//                foreach ($devices as $device){
//                    $res = array();
//                    $res['notification']['title'] = 'New Order';
//                    $res['notification']['body'] = 'You have received a new order';
//                    $res['notification']['obj'] = $order->reference;
//                    $res['notification']['order'] = $order;
//                    sendAndroidNotification($res,$device);
//                }
//


        //}
//        else {
//            $all_users = User::where('store_id',$store->id)->where('is_login',1)->get();
//            foreach ($all_users as $user){
//                $devices = UserDevice::where('user_id',$user->id)->get();
//                foreach ($devices as $device){
//                    $res = array();
//                    $res['notification']['title'] = 'New Order';
//                    $res['notification']['body'] = 'You have received a new order';
//                    $res['notification']['obj'] = $order->reference;
//                    $res['notification']['order'] = $order;
//                    sendAndroidNotification($res,$device);
//                }
//            }
//
//        }
    }

    public function saveProductOrder($order_id,$products){
        if(isset($products)){
            ProductOrder::where('order_id',$order_id)->delete();
            $products = json_decode($products);
            $product_order_array = [];
            foreach ($products as $product){
                $arr = [];
                $arr['order_id'] = $order_id;
                $arr['product_id'] = $product->item_id;
                $arr['quantity'] = $product->quantity;
                $arr['price'] = $product->unit_price;
                $arr['created_at'] = date('Y-m-d H:i:s');
                $arr['updated_at'] = date('Y-m-d H:i:s');

                $product_order_array[] = $arr;
            }
            ProductOrder::insert($product_order_array);
        }

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JSON Response
     */
    public function salesReturn(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'order_id' => 'required',
            'sub_total' => 'required',
            'order_total' => 'required',
            'order_items' => 'required',
            'customer' => 'required',
            'reference' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->validationStatus);
        }

        $requestData = $request->all();
        $user = User::select(['id','name','email','phone','store_id','profile_image','status','gender'])->find(Auth::id());

        $order_data['reference'] = $request->reference;
        $order = Order::firstOrNew($order_data);

        $order->biller_id = $user->id;
        $order->biller_detail = $user->toJson();
        $order->store_id = $user->store_id;
        $order->order_id = $request->order_id;;
        $order->discount = 0;
        $order->order_status = 1;
        $order->payment_status = 2;
        $order->payment_method = 1;
        $order->payment_detail = 'Sale Return';
        $order->payment_received = 0;
        $order->order_note = $request->order_note;
        $order->staff_note = $request->staff_note;
        $order->order_total = $request->order_total;
        $order->service_fee = 0;
        $order->sub_total = $request->sub_total;
        $order->order_tax = isset($request->vat_total) ? $request->vat_total : 0;
        $order->customer = $request->customer;
        $order->shipping_id = 0;
        $order->shipping_detail = '';

        $order->save();

        $order = Order::find($order->id);

        if(!empty($request->order_items)){
            $items_data = [];
            $basket_size = 0;
            $cost_of_goods  = 0;

            $order_items = json_decode($request->order_items);
            foreach($order_items as $key => $item){
                if(isset($item->item_id)){
                    $product_id = $item->item_id;

                    $items_data[$key]['item_id'] = $product_id;
                    if(!empty($item->item_image))
                        $items_data[$key]['item_image'] = $item->item_image;

                    $items_data[$key]['item_name'] = $item->item_name;
                    $items_data[$key]['unit_cost'] = $item->unit_cost;
                    $items_data[$key]['unit_price'] = $item->unit_price;
                    $items_data[$key]['total_price'] = $item->total_price;
                    $items_data[$key]['item_discount'] = $item->item_discount;
                    $items_data[$key]['quantity'] = $item->quantity;
                    $items_data[$key]['tax_details'] = json_encode($item->tax_details);

                    if(!empty($item->item_modifiers))
                        $items_data[$key]['item_modifiers'] = json_encode($item->item_modifiers);

                    $basket_size = $basket_size + $item->quantity;
                    $cost_of_goods = $cost_of_goods + ($item->unit_cost*$item->quantity);

                    if(isset($item->item_modifiers)){
                        $item_modifiers = $item->item_modifiers;
                        foreach($item_modifiers as $item_modifier){
                            $cost_of_goods = $cost_of_goods+ $item_modifier->cost;
                        }
                    }

                    $product = Product::find($product_id);
                    if($product){
                        if(isset($item->inventory) && $item->inventory==1){
                            updateProductStockByData($product->id, $order->store_id, $item->quantity, 1, 4, $order->id, Auth::id(), $order->order_note);
                        }
                        // sync products
                        updateSyncData('product',$product->id);
                    }
                }

            }

            $order->basket_size = $basket_size;
            $order->cost_of_goods = $cost_of_goods;
            $order->order_items = json_encode($items_data);
        }

        $order->save();

        if($order){

            if($order->order_id>0){
                $this->saveReturnOrders($order->order_id,$order->id);
            }

            // sync order
            updateSyncData('order',$order->order_id);
            updateSyncData('order',$order->id);

            $response['order_id'] = $order->id;
            $response['reference'] = $order->reference;
            $status = $this->successStatus;
            $response['success'] =  'You have successfully create order.';

            //update order payment table

            $order_payments = $request->input('order_payments');
            if($order_payments){
                foreach ($order_payments as $payment){
                    $order_payment = new OrderPayment();
                    $order_payment->order_id = $order->id;
                    $order_payment->payment_method = $payment->payment_method;
                    $order_payment->payment_status = $payment->payment_status;
                    $order_payment->payment_type = $payment->payment_status;
                    $order_payment->payment_received = $payment->payment_received;
                    $order_payment->order_total = $payment->order_total;
                    $order_payment->payment_detail = $payment->payment_detail;
                    $order_payment->transaction_detail = $payment->transaction_id;

                    //saving transaction_id in a seperate column
                    if(isset($payment->transaction_id)) {
                        $details = json_decode($payment->transaction_id,true);
                        foreach($details as $key => $value){
                            if($key == 'transaction_id'){
                                $order_payment->transaction_id = $value;
                            }
                        }
                    }
                    if(isset($payment->tip)){
                        $order_payment->tip = $payment->tip;
                    } else {
                        $order_payment->tip = 0;
                    }

                    $order_payment->save();
                }
            }

        }else{
            $status = $this->errorStatus;
            $response['record_id'] = 0;
            $response['reference'] = 0;
            $response['error'] =  'Order not successfully created.';
        }

        //save order products in product_order table
        $this->saveProductOrder($order->id,$order->order_items);

        return response()->json(['result'=>$response], $status);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return JSON Response
     */
    public function storeBulkOrders(Request $request)
    {

        if(isset($request->orders)){

            $orders = json_decode($request->orders);

            foreach($orders as $order_request)
            {
                //$requestData = $order_request;
                $biller_id = $order_request->biller_id;

                $user = User::select(['id','profile_image','name','email','phone','store_id'])->find($biller_id);

                $order_data['reference'] = $order_request->reference;

                $check = false;
                $check_order = Order::where('reference', $order_request->reference)->first();
                if($check_order){
                    $check = true;
                }
                if($check && $order_request->order_id==0){
                    //call order update stock function
                    //if($request->order_status == 0){

                    $this->orderUpdateStockManage($check_order->id, json_decode($order_request->order_items));
                    //}
                }

                $order = Order::firstOrNew($order_data);
                $company = Auth::user()->store->company;


                $order->biller_id = $biller_id;
                $order->biller_detail = $user->toJson();
                $order->store_id = $user->store_id;
                $order->discount = $order_request->discount;
                $order->order_status = $order_request->order_status;
                $order->payment_status = $order_request->payment_status;
                $order->payment_method = $order_request->payment_method;
                $order->payment_detail = $order_request->payment_detail;
                $order->payment_received = $order_request->payment_received;
                $order->order_note = $order_request->order_note;
                $order->staff_note = $order_request->staff_note;
                $order->order_total = $order_request->order_total;
                $order->service_fee = $order_request->service_fee;
                $order->sub_total = $order_request->sub_total;
                $order->order_tax = isset($order_request->vat_total) ? $order_request->vat_total : 0;
                $order->customer = $order_request->customer;
                $order->shipping_id = $order_request->shipping_id;
                if(isset($request->tip)){
                    $order->tip = $request->tip;
                }

                $shipping_option = Shipping_option::select(['id','name','cost','company_id'])->find($order_request->shipping_id);
                if($shipping_option)
                    $order->shipping_detail = $shipping_option->toJson();

                $order->save();

                $order = Order::find($order->id);
                if(!empty($order_request->order_items))
                {
                    $items_data = [];
                    $basket_size = 0;
                    $cost_of_goods  = 0;

                    $order_items = json_decode($order_request->order_items);

                    // sales return
                    if(isset($order_request->order_id) && $order_request->order_id>0)
                    {
                        foreach($order_items as $key => $item){
                            if(isset($item->item_id)){

                                //saving meal object in items_data
                                if($company->company_type == 1){
                                    if($item->delete_status == 0) {
                                        if (isset($item->item_meal_id)) {
                                            $meal_type = MealType::find($item->item_meal_id);
                                            $items_data[$key]['meal_type'] = $meal_type;
                                        }
                                    }
                                }

                                $product_id = $item->item_id;

                                $items_data[$key]['item_id'] = $product_id;
                                $items_data[$key]['item_image'] = $item->item_image;
                                $items_data[$key]['item_name'] = $item->item_name;
                                $items_data[$key]['unit_cost'] = $item->unit_cost;
                                $items_data[$key]['unit_price'] = $item->unit_price;
                                $items_data[$key]['total_price'] = $item->total_price;
                                $items_data[$key]['item_discount'] = $item->item_discount;
                                $items_data[$key]['quantity'] = $item->quantity;
                                $items_data[$key]['tax_details'] = json_encode($item->tax_details);

                                $basket_size = $basket_size + $item->quantity;
                                $cost_of_goods = $cost_of_goods + ($item->unit_cost*$item->quantity);

                                if(isset($item->item_modifiers)){
                                    $items_data[$key]['item_modifiers'] = json_encode($item->item_modifiers);
                                    $item_modifiers = $item->item_modifiers;
                                    foreach($item_modifiers as $item_modifier){
                                        $cost_of_goods = $cost_of_goods+ $item_modifier->cost;
                                    }
                                }
                                try {
                                    $product = Product::find($product_id);

                                } catch (\Exception $e) {

                                }

                                if($product){
                                    if(isset($item->inventory) && $item->inventory==1){
                                        updateProductStockByData($product->id, $order->store_id, $item->quantity, 1, 4, $order->id, $biller_id, $order->order_note);
                                    }
                                    // sync products
                                    updateSyncData('product',$product->id);
                                }
                            }

                        }
                        $order->order_id = $order_request->order_id;

                        $this->saveReturnOrders($order_request->order_id,$order->id);
                        $order->order_items = json_encode($items_data);

                    }
                    else
                    { // sales

                        foreach($order_items as $key => $item){
                            if(isset($item->item_id)){
                                //saving meal object in items_data
                                if($company->company_type == 1){
                                    if(isset($item->item_meal_id)) {
                                        if ($item->delete_status == 0) {
                                            $meal_type = MealType::find($item->item_meal_id);
                                            $items_data[$key]['meal_type'] = $meal_type;
                                        }
                                    }
                                }
                                $product_id = $item->item_id;
                                try {
                                $product = Product::with(['product','product_combos.product'])->find($product_id);
                            } catch (\Exception $e) {
                               // Log::info(['product id ' => $product_id]);
                            }
                                if($item->delete_status == 0) {
                                    if ($product) {
                                        $items_data[$key]['item_id'] = $product->id;
                                        $items_data[$key]['item_image'] = getProductDefaultImage($product->id);
                                        $items_data[$key]['item_name'] = $product->name;
                                        $items_data[$key]['check_id'] = $item->check_id;
                                        if ($product->product_id == 0) {
                                            if($item->split_item == 0){
                                                $items_data[$key]['unit_price'] = $product->price;
                                            } else {
                                                $items_data[$key]['unit_price'] = $item->unit_price;
                                            }
                                            $items_data[$key]['unit_cost'] = $product->cost;

                                        } else {
                                            if ($product->is_main_price == 0) {
                                                if($item->split_item == 0){
                                                    $items_data[$key]['unit_price'] = $product->price;
                                                } else {
                                                    $items_data[$key]['unit_price'] = $item->unit_price;
                                                }
                                                $items_data[$key]['unit_cost'] = $product->cost;
                                                //$items_data[$key]['unit_price'] = $product->price;
                                            } elseif ($product->is_main_price == 1) {
                                                if($item->split_item == 0){
                                                    $items_data[$key]['unit_price'] = $product->price;
                                                } else {
                                                    $items_data[$key]['unit_price'] = $item->unit_price;
                                                }
                                                $items_data[$key]['unit_cost'] = $product->product->cost;
                                                //$items_data[$key]['unit_price'] = $product->product->price;
                                            }
                                        }

                                        $items_data[$key]['item_discount'] = $item->item_discount;
                                        $items_data[$key]['quantity'] = $item->quantity;
                                        if(isset($item->item_tagline)){
                                            $items_data[$key]['item_tagline'] = $item->item_tagline;
                                        } else {
                                            $items_data[$key]['item_tagline'] = '';
                                        }
                                        if(isset($item->item_note)){
                                            $items_data[$key]['item_note'] = $item->item_note;
                                        }
                                        else {
                                            $items_data[$key]['item_note'] = '';
                                        }


                                        if ($product->product_id == 0) {
                                            $tax_rate_id = $product->tax_rate_id;
                                        } else {
                                            if ($product->is_main_tax == 0) {
                                                $tax_rate_id = $product->tax_rate_id;
                                            } elseif ($product->is_main_tax == 1) {
                                                $tax_rate_id = $product->product->tax_rate_id;
                                            }
                                        }

                                        $tax = Tax_rates::select(['id', 'code', 'name', 'rate'])->find($tax_rate_id);
                                        if ($tax) {
                                            $items_data[$key]['tax_details'] = $tax->toJson();
                                        }

                                        if ($product->type == 2) {
                                            $combo_collection = $product->product_combos->map(function ($item) {
                                                return ['id' => $item->product->id, 'name' => $item->product->name, 'code' => $item->product->code, 'sku' => $item->product->sku];
                                            });

                                            $items_data[$key]['item_combos'] = $combo_collection->toJson();
                                        }

                                        if ($product->is_modifier == 1) {
                                            if (!empty($item->item_modifiers)) {
                                                $item_modifiers = explode(',', $item->item_modifiers);
                                                $modifier_data_collection = collect([]);
                                                foreach ($item_modifiers as $modifier_id) {
                                                    $modifier = Product::find($modifier_id);
                                                    if ($modifier) {
                                                        $modifier_data['id'] = $modifier->id;
                                                        $modifier_data['name'] = $modifier->name;
                                                        $modifier_data['cost'] = $modifier->cost;
                                                        $modifier_data['price'] = $modifier->price;

                                                        $cost_of_goods = $cost_of_goods + $modifier->cost;

                                                        $modifier_data_collection->push($modifier_data);
                                                    }

                                                }

                                                $items_data[$key]['item_modifiers'] = json_encode($modifier_data_collection);
                                            }
                                        }
                                        $basket_size = $basket_size + $item->quantity;
                                        $cost_of_goods = $cost_of_goods + ($product->cost * $item->quantity);

                                        // sync products
                                        if($check == false){
                                            updateSyncData('product', $product->id);
                                        }
                                    }
                                }
                            }

                        }

                        //merge items in case of check orders
                        if($check){
                            $prev_items_data = json_decode($order->order_items,true);
                            $old_items_size = count($prev_items_data);
                            $items_size = count($items_data);
                            $new_items_array = [];


                            for($i = 0; $i < $old_items_size; $i++){
                                for($c = 0; $c < $items_size; $c++){
                                    if(isset($prev_items_data[$i]['check_id']) && isset($items_data[$c]['check_id'])){
                                        if($prev_items_data[$i]['check_id'] == $items_data[$c]['check_id']){
                                            unset($prev_items_data[$i]);
                                        }
                                    }
                                }
                            }

                            foreach($prev_items_data as $prev_data){
                                $new_items_array[] = $prev_data;
                            }
                            foreach ($items_data as $item_data){
                                $new_items_array[] = $item_data;
                            }
//                    $order->basket_size = $basket_size + $order->basket_size;
//                    $order->cost_of_goods = $cost_of_goods + $order->cost_of_goods;
                            $order->order_items = json_encode($new_items_array,true);
                        }
                        else {
//                    $order->basket_size = $basket_size;
//                    $order->cost_of_goods = $cost_of_goods;
                            $order->order_items = json_encode($items_data,true);
                        }

                    }

//                    $order->basket_size = $basket_size;
//                    $order->cost_of_goods = $cost_of_goods;

                }
                $order->dine_option = $order_request->dine_option;
                if($company->company_type == 1){
                    if($order_request->dine_option == 'yes'){
                        $order->table_data = $order_request->table;
                    }
                }
                $global_discount = $order_request->global_discount;
                if(isset($global_discount)){
                    $order->global_discount = $global_discount;
                } else {
                    $order->global_discount = 0;
                }
                if(isset($order_request->payment)){
                    //Log::info(['payment' => 'yes']);
                    $order_payment = new OrderPayment();
                    $order_payment->order_id = $order->id;
                    $order_payment->payment_method = $order_request->payment_method;
                    $order_payment->payment_status = $order_request->payment_status;
                    $order_payment->payment_type = $order_request->payment_status;
                    $order_payment->payment_received = $order_request->payment_received;
                    $order_payment->order_total = $order_request->order_total;
                    $order_payment->payment_detail = $order_request->payment_detail;
                    //$order_payment->transaction_detail = $order_request->transaction_detail;

                    //saving transaction_id in a seperate column
                    if(isset($order_request->transaction_detail)) {
                        $details = json_decode($order_request->transaction_detail,true);
                        if(isset($details)){
                            foreach($details as $key => $value){
                                if($key == 'transaction_id'){
                                    $order_payment->transaction_id = $value;
                                }
                            }
                        }

                    }
                    if(isset($order_request->tip)){
                        $order_payment->tip = $order_request->tip;
                    } else {
                        $order_payment->tip = 0;
                    }

                    $order_payment->save();
                }
                $order->save();
                if($order){

                    // sync order
                    if($order->order_id>0)
                        updateSyncData('order',$order->order_id);

                    updateSyncData('order',$order->id);
                    //sendOrderEmail($order->id);
//                    if($order->order_status==1){
//                        updateOrderProductsStock($order->id,$biller_id);
//                    }


                    if($check == false){
                        updateOrderProductsStock($order->id);
                    }
                }

                //save order products in product_order table
                $this->saveProductOrder($order->id,$order->order_items);


                $this->calculateCostOfGoods($order);
                updateSyncData('customer',$order->customer);

            }

            $status = $this->successStatus;
            $response['success'] =  'You have successfully create orders.';

            return response()->json(['result'=>$response], $status);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($category_id)
    {
        $subcategory_id = '';
        return view('admin.items.index', compact('category_id','subcategory_id'));
    }


    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return JS0N Response
     */
    public function edit($id)
    {
        $order = Order::with(['store','customers'])->find($id);

        $order->date = date('d/m/Y h:j a', strtotime($order->created_at));
        $order->sub_total = number_format($order->sub_total,2);
        $order->order_total = number_format($order->order_total,2);

        if(!empty($order->biller_detail))
            $order->biller_detail = json_decode($order->biller_detail);

        if(!empty($order->order_items))
            $order->order_items = json_decode($order->order_items);
        if(!empty($order->order_items))
            $order->order_items = json_decode($order->order_items);

        if($order->shipping_id > 0)
            $order->shipping_detail = json_decode($order->shipping_detail);



        if($order){
            $response['order'] = $order;
            $status = $this->successStatus;
        }else{
            $status = $this->notFoundStatus;
            $response['record_id'] = $id;
            $response['error'] =  'Order not exist against this id.';
        }

        return response()->json(['result'=>$response], $status);
    }

    /**
     * Update the specified resource in storage.
     *
     * @return JS0N Response
     */
    public function update(Request $request)
    {

        $validator = Validator::make($request->all(), [
            // 'order_id' => 'required',
            'payment_received' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->validationStatus);
        }

        $order = Order::find($request->order_id);

        $requestData = $request->all();

        if($order){
            $requestData['order_id'] = 0;
            $requestData['payment_status'] = 2;
            $requestData['order_status'] = 1;
            $order->update($requestData);

            //sendOrderEmail($order->id);
            updateOrderProductsStock($order->id);

            $response['reference'] = $order->reference;
            $status = $this->successStatus;
            $response['success'] =  'Order successfully updated.';
        }else{
            $status = $this->notFoundStatus;
            $response['record_id'] = $request->order_id;
            $response['error'] =  'Order not exist against this id.';
        }

        return response()->json(['result'=>$response], $status);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return JS0N Response
     */
    public function destroy($id)
    {
        if(\Request::ajax())
        {
            $id = Hashids::decode($id)[0];
        }

        $product = Product::find($id);

        if($product){
            $product->delete();
            $response['success'] = 'Product deleted!';
            $status = $this->successStatus;
        }else{
            $response['error'] = 'Product not exist against this id!';
            $status = $this->errorStatus;
        }

        return response()->json(['result'=>$response], $status);
    }


    /**
     * Method : DELETE
     *
     * @return delete images
     */
    public function removeVariant($id)
    {
        $variant = Product_variant::findOrFail($id);
        if ($variant) {
            $variant->delete();
        }

        return response()->json(['success' => 1]);
    }



    public function orderInvoice($id) {
        $id = Hashids::decode($id)[0];
        $order = Order::with(['store.currency','customers'])->where('id',$id)->first();
        $data['order'] =  $order->toArray();
        $data['order_payments'] = OrderPayment::where('order_id',$order->id)->get();
        return view('company.orders.invoice', $data);

    }

    /**
     * getOrdersMap function
     *
     * @param  int  $orders
     *
     * @return \Illuminate\Http\Response
     */
    public function getOrdersMap($orders)
    {

        // return $orders->all();
    }

    /**
     * sendOrderEmail function
     *
     * @param  int  $order_id
     *
     * @return \Illuminate\Http\Response
     */
    public function sendOrderEmail($order_id)
    {
        sendOrderEmail($order_id);

        $status = $this->successStatus;
        $response['success'] =  'Email successfully sent.';

        return response()->json(['result'=>$response], $status);

    }

    /**
     * sendOrderEmailApi function
     *
     * @param  int  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function sendOrderEmailApi(Request $request)
    {

        $order = Order::with(['store'])->where('reference',$request->reference)->first();

        if (!$order) {
            $response['error'] = 'Order not exists against this id.';
            return response()->json(['result'=>$response], $this->notFoundStatus);
        }
        $customer_name = null;
        $first_name = '';
        $last_name = '';
        if($request->customer_name){
            $customer_name = $request->customer_name;
            $names = explode(" ", $customer_name);
            if(count($names) >= 2)
            {
                $first_name = $names[0];
                (array_shift($names));
                $last_names = implode(" " , $names);
                $last_name = $last_names;
            } else if(count($names) == 1) {
                $first_name = $names[0];
                $last_name = '';
            } else {
                $first_name = '';
                $last_name = '';
            }
        }
        if(isset($request->email)){
            $email = $request->email;
            $customer = Customer::where('email',$email)->first();
            if(!$customer){
                $customerData['id'] = Auth::id().date('Ymdhis');
                $customerData['first_name'] = (isset($customer_name)) ? $first_name : '';
                $customerData['last_name'] = (isset($customer_name)) ? $last_name : '';
                $customerData['email'] = $email;
                $customerData['company_id'] = $order->store->company_id;
                $customerData['store_id'] = $order->store_id;
                $customerData['currency_id'] = companySettingValueApi('currency_id');
                $customerData['profile_image'] = 'default.png';

                $customer = Customer::create($customerData);

                $store_customer['customer_id'] = $customer->id;
                $store_customer['store_id'] = $order->store_id;
                $store_customer['company_id'] = $order->store->company_id;
                StoreCustomer::create($store_customer);
                updateSyncData('customer',$customer->id);

                $order_data['customer'] = $customer->id;
                $order->update($order_data);
                $order = Order::with(['store'])->where('reference',$request->reference)->first();
                updateSyncData('order',$order->id);
            }
            else {
                $customer->first_name = (isset($customer_name)) ? $first_name : $customer->first_name;
                $customer->last_name = (isset($customer_name)) ? $last_name : $customer->last_name;
                $customer->save();
                $order_data['customer'] = $customer->id;
                $order->update($order_data);
            }
        }


        $order = Order::with(['store'])->where('reference',$request->reference)->first();
        sendOrderEmail($order->id);


        $status = $this->successStatus;
        $response['success'] =  'Email successfully sent.';

        return response()->json(['result'=>$response], $status);

    }

    function saveReturnOrders($parent_order_id,$order_id)
    {
        $main_order = Order::find($parent_order_id);
        $return_order = Order::find($order_id);
        $return_ids = $main_order->return_ids;
        if($return_ids!=""){
            $return_ids = json_decode($return_ids,true);
            if(!find_key_value($return_ids, 'order_id', $order_id)){
                $count = COUNT($return_ids);
                $return_ids[$count]['order_id'] = $order_id;
                $return_ids[$count]['reference'] = $return_order->reference;
                $main_order->return_ids = json_encode($return_ids);
                $main_order->save();
            }
        }else{
            $return_data = [];
            $return_data[0]['order_id'] = $order_id;
            $return_data[0]['reference'] = $return_order->reference;
            $main_order->return_ids = json_encode($return_data);
            $main_order->save();
        }
    }

    function orderUpdateStockManage($order_id , $order_items , $user_id = 0, $store_id = 0){

        foreach($order_items as $key => $item) {
            //for customer login
            if($user_id == 0 || $store_id == 0){
                $store_id = Auth::user()->store->id;
                $user_id = Auth::user()->id;
            }
            $product_id = $item->item_id;
            $product_stock = Store_products::where('product_id',$product_id)->where('store_id',$store_id)->first();

            //check if order item is deleted

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
}
