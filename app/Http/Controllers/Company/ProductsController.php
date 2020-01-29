<?php

namespace App\Http\Controllers\Company;

use App\Ad;
use App\Attendance_status;
use App\Discount;
use App\DiscountBogo;
use App\DiscountCategory;
use App\DiscountProduct;
use App\FloorTable;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use App\Categories;
use App\OrderPayment;
use DateTime;
use function foo\func;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
use App\Product_attribute;
use App\Product_tag;
use App\Variant;
use App\Product_combos;
use App\Product_variant;
use App\Stock;
use App\Product_modifier;
use App\Sync;
use App\Customer;
use App\Order;
use App\ProductOrder;

class ProductsController extends Controller
{
    public $successStatus = 200;
    public $badRequestStatus = 400;
    public $errorStatus = 401;
    public $notFoundStatus = 404;

    /**
     * Display a listing of the resource.
     *
     * @return JS0N Response
     */
    public function index()
    {

        return view('company.products.index');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getProducts()
    {

        $products = Product::with([
            'products' => function ($query) {
                $query->orderBy('id', 'asc');
            },
            'products.product_images',
            'products.store_products',
            'company',
            'store_products.store',
            'category_products.category',
            'product_images'
        ])->where('product_id',0)->company(Auth::id())->orderBy('id','desc')->get();

        $products->map(function ($product) {

            $product->products->map(function ($product) {
                $product['encoded_id'] = Hashids::encode($product->id);

                return $product;
            });

            return $product;
        });

        return Datatables::of($products)
            ->addColumn('id', function ($product) {
                    return $product->id;
            })
            ->addColumn('is_variants', function ($product) {
                if($product->is_variants)
                    return '<span class="details-control"></span>';
                else
                    return '';
            })
            ->addColumn('sku', function ($product) {
                if(($product->is_variants) || ($product->type==3))
                    return '-';
                else
                    return $product->sku;
            })
            ->addColumn('name', function ($product) {
                $product_name = $product->name;
                if($product->type == 2)
                    $product_name = $product_name .' <b>(Combo)</b>';
                elseif($product->type == 3)
                    $product_name = $product_name .' <b>(Modifier)</b>';

                return $product_name;
            })
            ->addColumn('product_image', function ($product) {
                if($product->is_variants)
                    return '<i class="fa fa-sitemap fa-2x"></i>';
                else
                    return '<img width="50" src="'. getProductDefaultImage($product->id) .'" />';
            })
            ->addColumn('supplier', function ($product) {
                if($product->type == 3)
                    return '-';
                else
                    return @$product->supplier->name;
            })
            ->addColumn('action', function ($product) {
                return '<a href="products/'. Hashids::encode($product->id).'/edit" class="text-primary action-padding" data-toggle="tooltip" title="Edit Product"><i class="fa fa-edit"></i></a> 
                        <a href="product-stocks/'. Hashids::encode($product->id).'" class="text-success action-padding" data-toggle="tooltip" title="Stock History"><i class="fa fa-line-chart"></i></a> 
                        <a href="product-sale-history/'. Hashids::encode($product->id).'" class="text-success action-padding" data-toggle="tooltip" title="Sale History"><i class="fa fa-bar-chart-o"></i></a> 
                        <a href="javascript:void(0)" class="text-danger btn-delete action-padding" data-toggle="tooltip" title="Delete Product" id="'.Hashids::encode($product->id).'"><i class="fa fa-trash"></i></a>';
            })
            ->rawColumns(['is_variants','name','product_image', 'supplier', 'store', 'quantity', 'action'])
            ->editColumn('id', '{{$id}}')
            ->make(true);

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

        return view('company.products.stock_history', compact('product'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getProductStocks($product_id)
    {

        $store_ids = Store::where('company_id',Auth::id())->pluck('id');

        $stocks = Stock::with(['store','product'])->where('product_id',$product_id)->whereIn('store_id',$store_ids)->orderBy('id','desc')->get();


        return Datatables::of($stocks)
            ->addColumn('created_at', function ($stock) {
                return date('d-m-Y h:i a', strtotime($stock->created_at));
            })
            ->addColumn('store_name', function ($stock) {
                return $stock->store->name;
            })
            ->addColumn('product_name', function ($stock) {
                return $stock->product->name;
            })
            ->addColumn('stock_type', function ($stock) {
                if($stock->stock_type==1)
                    return "IN";
                elseif($stock->stock_type==2)
                    return "OUT";
            })
            ->addColumn('origin', function ($stock) {
                switch ($stock->origin) {
                    case 1:
                        return "Add Product";
                        break;
                    case 2:
                        return "Update Product";
                        break;
                    case 3:
                        return "Sale";
                        break;
                    case 4:
                        return "Sale Return";
                        break;
                    case 5:
                        return "Adjustment";
                        break;
                    default:
                        return "Add Product";
                }
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['store_name', 'product_name'])
            ->make(true);

    }

    //search items
    public function searchProducts(Request $request)
    {
        if(\Request::wantsJson())
        {
            $user = User::with(['store.company'])->find(Auth::id());

            $company_id = $user->store->company->id;
            $store_id = $user->store->id;
            $product_ids = Store_products::where('store_id',$store_id)->pluck('product_id');
            $products = Product::with(
                [
                    'company',
                    'tax_rate',
                    'product_combos.product.product_images',
                    'store_products' => function ($query) use ($user) {
                        $query->where('store_id', $user->store->id);
                    },
                    'category_products.category',
                    'product_images',
                    'product_tags',
                    'product_attributes.variant',
                    'product_modifiers.modifier',
                    'products.tax_rate',
                    'products.product_tags',
                    'products.store_products' => function ($query) use ($user) {
                        $query->where('store_id', $user->store->id);
                    },
                    'products.product_images',
                    'products.product_variants.product_attribute.variant'
                ])->where('product_id',0)->company($company_id)->whereIn('id',$product_ids);

            if(!empty($request->q)){
                $q = $request->q;
                $products->where('name', 'ilike', '%'.$q.'%')
                    ->orWhere('code', 'ilike', '%'.$q.'%')
                    ->orWhere('sku', 'ilike', '%'.$q.'%')
                    ->orWhere(function($query) use($q){
                        $query->whereHas('product_tags',function($query) use($q){
                            $query->where('name', 'ilike', '%'.$q.'%');
                        });
                    });
            }

            //if(!empty($request->store_ids)){

            //$store_ids = json_decode($request->store_ids);

            //$products->stores([$user->store->id]);

            //}

            if(!empty($request->category_ids)){

                $category_ids = json_decode($request->category_ids);

                    $products->categories($category_ids);

            }
            if(!empty($request->detail)){
                $products->where('detail', 'like', '%'.$request->detail.'%');
            }
            if(!empty($request->price_range)){
                $range  = explode("-",$request->price_range);
                $from   = $range[0];
                $to     = $range[1];
                $products->whereBetween('price', array($from, $to));
            }
            if(!empty($request->sorting)){
                if($request->sorting == 'high_price'){
                    $products->orderBy('price', 'desc');
                }
                if($request->sorting == 'low_price'){
                    $products->orderBy('price', 'asc');
                }
                if($request->sorting == 'new'){
                    $products->orderBy('id', 'asc');
                }
            }


            $products = $products->get();

            $response['products'] = $this->getProductsMap($products);


            $status = $this->successStatus;

            return response()->json(['result' => $response], $status);
        }
    }

    //sync data
    public function syncData(Request $request)
    {
        if(\Request::wantsJson())
        {
            $validator = Validator::make($request->all(), [
                'store_id' => 'required|numeric'
            ]);

            $input = $request->all();

            if ($validator->fails()) {
                $response['error'] = $validator->errors();
                return response()->json(['result'=>$response], $this->badRequestStatus);
            }

            $offset = 0;
            $limit = 500;

            if(!empty($request->offset)){
                $offset = $request->offset;
            }
            if(!empty($request->limit)){
                $limit = $request->limit;
            }

            if($request->store_id>0){
                $collection = Sync::where('id','>',$offset)
                    ->where('store_id',$request->store_id)
                    ->take($limit)->get();

                if($collection->count()>0){
                    $last_id = $collection->last()->id;

                    $syncs = $collection->unique(function ($item) {
                        return $item['sync_id'].$item['sync_type'];
                    });

                    $syncs = collect($syncs->values()->all());

                    $syncs->map(function ($sync) {

                        if ($sync->sync_type == 'product') {

                            $product = Product::with(
                                [
                                    'company',
                                    'tax_rate',
                                    'product_combos.product.product_images',
                                    'store_products' => function ($query) use ($sync) {
                                        $query->where('store_id', $sync->store_id);
                                    },
                                    'category_products.category',
                                    'product_images',
                                    'product_tags',
                                    'product_attributes.variant',
                                    'product_modifiers.modifier',
                                    'products.tax_rate',
                                    'products.product_tags',
                                    'products.store_products' => function ($query) use ($sync) {
                                        $query->where('store_id', $sync->store_id);
                                    },
                                    'products.product_images',
                                    'products.product_variants.product_attribute.variant'
                                ])->find($sync->sync_id);
                            if ($product) {
                                if ($product->is_modifier == 1) {
                                    $product->product_modifiers->map(function ($modifier) {
                                        $modifier->name = $modifier->modifier->name;
                                        $modifier->price = $modifier->modifier->price;

                                        unset($modifier->product_id);
                                        unset($modifier->created_at);
                                        unset($modifier->modifier);
                                        return $modifier;
                                    });
                                }

                                $sync['sync_data'] = $product;
                            } else {
                                $sync['sync_data'] = '';
                            }
                        }
                        elseif ($sync->sync_type == 'category') {

                            $category = Categories::with(['subcategories'])->where('store_id', $sync->store_id)->find($sync->sync_id);

                            if ($category) {
                                $sync['sync_data'] = $category;
                            } else {
                                $sync['sync_data'] = '';
                            }
                        }
                        elseif ($sync->sync_type == 'customer') {

                            $customer = Customer::with(['orders'])->find($sync->sync_id);

                            if ($customer) {
                                $customer->name = $customer->first_name . ' ' . $customer->last_name;

                                if ($customer->orders->count() > 0) {
                                    $customer->total_sales = number_format($customer->orders->sum('order_total'), 2);
                                    $customer->total_visits = $customer->orders->count();
                                    $customer->last_visit = $customer->orders->last()->created_at->format('d/m/Y');
                                } else {
                                    $customer->total_sales = 0;
                                    $customer->total_visits = 0;
                                    $customer->last_visit = '';
                                }

                                unset($customer->orders);

                                $sync['sync_data'] = $customer;
                            } else {
                                $sync['sync_data'] = '';
                            }
                        }
                        elseif ($sync->sync_type == 'discount') {
                            $discount_data = [];
                            $discount = Discount::with('discountCategories','discountProducts')->find($sync->sync_id);
                            if($discount) {
                                if (count($discount->discountCategories) > 0) {
                                    $discount_data = DiscountCategory::with('discount')->where('discount_id', $sync->sync_id)->first();
                                }
                                if (count($discount->discountProducts) > 0) {
                                    $discount_data = DiscountProduct::with('discount')->where('discount_id', $sync->sync_id)->first();
                                }
                            }
                            $sync['sync_data'] = $discount_data;
                        }
//                        else if ($sync->sync_type == 'discount_delete') {
//
//                            $discount = Discount::with('discountCategories','discountProducts')->find($sync->sync_id);
//                            if($discount){
//                                if(count($discount->discountCategories) > 0){
//                                    $discount->type = 'category';
//                                }
//                                if(count($discount->discountProducts) > 0){
//                                    $discount->type = 'product';
//                                }
//                            }
//                            $sync['sync_data'] = $discount;
//                        }
                        elseif ($sync->sync_type == 'discount_bogo') {
                            $discount_bogo = Discount::with('discountBogo')->where('id',$sync->sync_id)->get();
                            $sync['discount_bogo'] = $discount_bogo;
                        }
                        elseif ($sync->sync_type == 'ad') {
                            $ad = Ad::find($sync->sync_id);
                            $sync['sync_data'] = $ad;
                        }
                        elseif ($sync->sync_type == 'table') {
                            $table = FloorTable::where('table_id',$sync->sync_id)->first();
                            $sync['sync_data'] = $table;
                        }
                        elseif ($sync->sync_type == 'clock_in') {
                            $attendance = Attendance_status::where('id',$sync->sync_id)->first();
                            $sync['sync_data'] = $attendance;
                        }
                        elseif ($sync->sync_type == 'clock_out') {
                            $attendance = Attendance_status::where('id',$sync->sync_id)->first();
                            $sync['sync_data'] = $attendance;
                        }
                    elseif($sync->sync_type=='order'){

                        $order = Order::with(['store','customers.orders','shipping_option'])->find($sync->sync_id);
                        if($order){
                            //setting order time
//                            $store = Store::where('id',$order->store_id)->first();
//                            if($store){
//                                $time_zone =  $store->time_zone;
//                                if($time_zone && $time_zone != null){
//                                    $offset = explode(',',$time_zone);
//                                    $order_time = $order->created_at->toDateTimeString();
//                                    if(count($offset) > 0){
//                                        if($offset[1][0] == '+'){
//                                            //add time
//                                            $time = str_replace('+','',$offset[1]);
//                                            $time = explode(':',$time);
//                                            $hours = $time[0];
//                                            $new_time = Carbon::parse($order_time)->addHour($hours);
//                                            $order->created_at = $new_time->toDateTimeString();
//                                        } else {
//                                            //subtract time
//                                            $time = str_replace('-','',$offset[1]);
//                                            $time = explode(':',$time);
//                                            $hours = $time[0];
//                                            $new_time = Carbon::parse($order_time)->subHour($hours);
//                                            $order->created_at = $new_time->toDateTimeString();
//                                        }
//                                    }
//
//                                }
//                            }


                            if($order->biller_detail != "")
                                $order['biller_detail'] = json_decode($order->biller_detail);

                            $order_payments = OrderPayment::where('order_id',$order->id)->get();
                            if($order_payments)
                            {
                                $arr = $order_payments;
//                                foreach ($order_payments as $payment){
//                                    $transaction = json_decode($payment->transaction_detail);
//                                    if(isset($transaction)){
//                                        $payment->transaction_id = $transaction->transaction_id;
//                                        $arr[] = $payment;
//                                    }
//                                }
                                $tip_sum = OrderPayment::where('order_id',$order->id)->sum('tip');
                                if(isset($tip_sum)){
                                    $order->tip = $tip_sum;
                                } else {
                                    $order->tip = 0;
                                }

                                $order['transaction_detail'] = $arr;
                            }
                            if($order->shipping_detail != "")
                                $order['shipping_detail'] = json_decode($order->shipping_detail);
                            else
                                $order['shipping_detail'] = NULL;

                            if($order->table_data != "")
                                $order['table_data'] = json_decode($order->table_data);
                            else
                                $order['table_data'] = NULL;

                            if($order->order_items != ""){
                                $order_items = json_decode($order->order_items);
                                foreach($order_items as $order_item){
                                    if(isset($order_item->tax_details))
                                        $order_item->tax_details = json_decode($order_item->tax_details);
                                    if(isset($order_item->item_combos))
                                        $order_item->item_combos = json_decode($order_item->item_combos);
                                    if(isset($order_item->item_modifiers))
                                        $order_item->item_modifiers = json_decode($order_item->item_modifiers);
//                                        if(isset($order_item->meal_type)){
//                                            $order_item->meal_type = json_decode($order_item->meal_type);
//                                        }

                                }

                                $order['order_items'] = $order_items;
                                $order['customer'] = (string)$order->customer;
                            }
                            if($order->customers){
                                if($order->customers->orders->count()>0){
                                    $order->customers->total_sales = number_format($order->customers->orders->sum('order_total'),2);
                                    $order->customers->total_visits = $order->customers->orders->count();
                                    $order->customers->last_visit = $order->customers->orders->last()->created_at->format('d/m/Y');
                                }

                            }else{
                                if($order->customers){
                                    $order->customers->total_sales = 0;
                                    $order->customers->total_visits = 0;
                                    $order->customers->last_visit = '';
                                }
                            }
                            if($order->customers){
                                unset($order->customers->orders);
                            }

                            if($order->return_ids != "")
                                $order['return_orders'] = json_decode($order->return_ids);
                            else
                                $order['return_orders'] = NULL;

                            unset($order->return_ids);


                            $sync['sync_data'] = $order;
                        }else{
                            $sync['sync_data'] = '';
                        }
                    }
                    else{
                            $sync['sync_data'] = '';
                        }

                        return $sync;
                    });
                    //check if further record exist after current offset
                    $more_record_count = Sync::where('id','>',$last_id)
                        ->where('store_id',$request->store_id)->count();
                    $response['more_records'] = ($more_record_count > 0) ? true : false;
                    $response['offset'] = $last_id;
                    $response['sync_data'] = $syncs->all();
                    $status = $this->successStatus;
                }else{
                    $response['offset'] = $offset;
                    $status = $this->notFoundStatus;
                }

                return response()->json(['result' => $response], $status);
            }

        }
    }

    // get sync data
    public function getSyncData(Request $request)
    {
        if(\Request::wantsJson())
        {
            $validator = Validator::make($request->all(), [
                'sync_type' => 'required|alpha',
                'sync_id' => 'required|numeric',
                'store_id' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                $response['error'] = $validator->errors();
                return response()->json(['result'=>$response], $this->badRequestStatus);
            }

            if($request->sync_type=='product'){

                $product = Product::with(
                    [
                        'company',
                        'tax_rate',
                        'product_combos.product.product_images',
                        'store_products' => function ($query) use ($request) {
                            $query->where('store_id', $request->store_id);
                        },
                        'category_products.category',
                        'product_images',
                        'product_tags',
                        'product_attributes.variant',
                        'product_modifiers.modifier',
                        'products.tax_rate',
                        'products.product_tags',
                        'products.store_products' => function ($query) use ($request) {
                            $query->where('store_id', $request->store_id);
                        },
                        'products.product_images',
                        'products.product_variants.product_attribute.variant'
                    ])->find($request->sync_id);
                if($product){
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

                    $response['sync_data'] = $product;
                    $status = $this->successStatus;
                }else{
                    $response['sync_data'] = '';
                    $status = $this->notFoundStatus;
                }

            }
            elseif($request->sync_type=='category'){

                $category = Categories::with(['subcategories'])->where('store_id',$request->store_id)->find($request->sync_id);

                if($category){
                    $response['sync_data'] = $category;
                    $status = $this->successStatus;
                }else{
                    $response['sync_data'] = '';
                    $status = $this->notFoundStatus;
                }
            }
            elseif($request->sync_type=='customer'){

                $customer = Customer::with(['orders'])->find($request->sync_id);

                if($customer){
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

                    $response['sync_data'] = $customer;
                    $status = $this->successStatus;
                }else{
                    $response['sync_data'] = '';
                    $status = $this->notFoundStatus;
                }
            }
            elseif($request->sync_type=='order'){

                $order = Order::with(['store','customers.orders','shipping_option'])->find($request->sync_id);

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

                if($order){
                    $response['sync_data'] = $order;
                    $status = $this->successStatus;
                }else{
                    $response['sync_data'] = '';
                    $status = $this->notFoundStatus;
                }
            }

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
        $images = [];

        return view('company.products.create', compact('images'));
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
        $rules['type'] = 'required|numeric';
        $rules['code'] = 'required|unique:products';
        $rules['name'] = 'required';
        //$rules['tax_rate_id'] = 'required';
        //$rules['barcode_symbology'] = 'required';

        if(empty($request->is_variants) && $request->type != 3){
            $rules['sku'] = 'required|unique:products';
        }

        if($request->type == 1 || $request->type == 3){
            $rules['cost'] = 'required|numeric';
            $rules['price'] = 'required|numeric';
        }

        $this->validate($request, $rules);

        $requestData = $request->all();

        $requestData['company_id'] = Auth::id();
        $requestData['sku'] = empty($requestData['sku']) ? 0 : $requestData['sku'];
        $requestData['cost'] = empty($requestData['cost']) ? 0 : $requestData['cost'];
        $requestData['price'] = empty($requestData['price']) ? 0 : $requestData['price'];
        $requestData['discount_type'] = empty($requestData['discount_type']) ? 0 : $requestData['discount_type'];
        $requestData['discount'] = empty($requestData['discount']) ? 0 : $requestData['discount'];
        $requestData['supplier_id'] = empty($requestData['supplier_id']) ? 0 : $requestData['supplier_id'];
        $requestData['tax_rate_id'] = empty($requestData['tax_rate_id']) ? 0 : $requestData['tax_rate_id'];

        $product = Product::create($requestData);

        if($product){

            if(!empty($request->tags)){
                $tags = explode(',', $request->tags);
                foreach($tags as $tag){
                    $tag_data['product_id'] = $product->id;
                    $tag_data['name'] = $tag;
                    Product_tag::create($tag_data);
                }
            }

            if(isset($requestData['image_ids'])){
                foreach($requestData['image_ids'] as $image_id){
                    $product_images = Product_images::find($image_id);
                    $product_images->product_id = $product->id;
                    $product_images->update();
                }
            }

            $store_product = new Store_products();
            //$store_product->store_id =

            Session::flash('success', 'Product successfully added!');
        }else{
            Session::flash('error', 'Product not successfully added!');
        }

        return redirect('company/products/'. Hashids::encode($product->id) .'/edit?tab=2');
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
        $id = Hashids::decode($id)[0];

        $product = Product::with(
            [
                'company',
                'product_tags'=> function ($query) {
                    $query->orderBy('id', 'asc');
                },
                'product_combos' => function ($query) {
                    $query->orderBy('id', 'asc');
                },
                'store_products.store',
                'category_products.category',
                'product_images'
            ]
        )->findOrFail($id);

        //dd($product->toArray());

        $images = $product->product_images->sortBy('id');

        return view('company.products.edit', compact('product','images'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return JS0N Response
     */
    public function update($id, Request $request)
    {
        $id = Hashids::decode($id)[0];

        $rules['type'] = 'required|numeric';
        $rules['code'] = 'required|unique:products,code,'.$id;
        $rules['name'] = 'required';
        //$rules['tax_rate_id'] = 'required';
        //$rules['barcode_symbology'] = 'required';
        $rules['product_images'] = 'required|numeric';


        if(empty($request->is_variants) && $request->type != 3){
            $rules['sku'] = 'required|unique:products,sku,'.$id;
        }

        if($request->type == 1 || $request->type == 3){
            $rules['cost'] = 'required|numeric';
            $rules['price'] = 'required|numeric';
        }

        $this->validate($request, $rules);

        $product = Product::findOrFail($id);

        $requestData = $request->all();
        $requestData['is_duty'] = empty($requestData['is_duty']) ? 0 : $requestData['is_duty'];
        $requestData['sku'] = empty($requestData['sku']) ? 0 : $requestData['sku'];
        $requestData['cost'] = empty($requestData['cost']) ? 0 : $requestData['cost'];
        $requestData['price'] = empty($requestData['price']) ? 0 : $requestData['price'];
        $requestData['discount_type'] = empty($requestData['discount_type']) ? 0 : $requestData['discount_type'];
        $requestData['discount'] = empty($requestData['discount']) ? 0 : $requestData['discount'];
        $requestData['supplier_id'] = empty($requestData['supplier_id']) ? 0 : $requestData['supplier_id'];
        $requestData['tax_rate_id'] = empty($requestData['tax_rate_id']) ? 0 : $requestData['tax_rate_id'];


        $product->update($requestData);

        // remove product tags
        Product_tag::where('product_id',$product->id)->delete();
        if($request->type == 1)
            Product_combos::where('product_id',$product->id)->delete();

        if(!empty($request->tags)){
            $tags = explode(',', $request->tags);
            foreach($tags as $tag){
                $tag_data['product_id'] = $product->id;
                $tag_data['name'] = $tag;
                Product_tag::create($tag_data);
            }
        }

        if(isset($requestData['image_ids'])){
            foreach($requestData['image_ids'] as $image_id){
                $product_images = Product_images::find($image_id);
                $product_images->product_id = $product->id;
                $product_images->update();
            }
        }

        // sync products
        updateSyncData('product',$product->id);

        Session::flash('success', 'Product successfully updated!');

        return redirect('company/products/'. Hashids::encode($product->id) .'/edit?tab=1');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return JS0N Response
     */
    public function updateStore($id, Request $request)
    {
        //dd($request->all());
        $id = Hashids::decode($id)[0];

        $this->validate($request, [
            'store_category_ids' => 'required',
        ]);

        $product = Product::findOrFail($id);

        $requestData = $request->all();

        $product->update($requestData);

        // remove store products and category products and product stock and product tags
        Category_products::where('product_id',$product->id)->delete();

        $store_category_ids = explode(',', $request->store_category_ids);

        $old_store_ids = Store_products::select('store_id','quantity')->where('product_id',$product->id)->get();
        $new_store_ids = [];

        foreach($store_category_ids as $store_category_id){
            $store_category = explode('-', $store_category_id);

            if($store_category[0] == "store"){

                $new_quantity = empty($requestData['store_quantity_'.$store_category_id]) ? 0 : $requestData['store_quantity_'.$store_category_id];
                $store_id = $store_category[1];


                // Check store product exist or not
                $store_product_data = Store_products::where('product_id',$product->id)->where('store_id',$store_id)->first();

                //get low stock value for store
                $low_stock = empty($requestData['low_stock_'.$store_category_id]) ? 0 : $requestData['low_stock_'.$store_category_id];
                //get lowstock status
                $low_stock_status = $requestData['low_status_'.$store_category_id];

                if($store_product_data){

                    //save low stock
                    $store_product_data->low_stock = $low_stock;
                    $store_product_data->low_stock_status = $low_stock_status;
                    $store_product_data->save();

                    if($new_quantity > $store_product_data->quantity){
                        // stock add
                        $add_quantity = $new_quantity - $store_product_data->quantity;
                        updateProductStockByData($product->id, $store_id, $add_quantity, 1, 2, 0, 0, 'Edit Product');
                    }elseif($new_quantity < $store_product_data->quantity){
                        // stock remove
                        $remove_quantity = $store_product_data->quantity - $new_quantity;
                        updateProductStockByData($product->id, $store_id, $remove_quantity, 2, 2, 0, 0, 'Edit Product');
                    }
                }else{
                    // save new store product
                    $store_product['product_id'] = $product->id;
                    $store_product['store_id'] = $store_id;
                    $store_product['quantity'] = $new_quantity;
                    $store_product['low_stock'] = $low_stock;
                    $store_product['low_stock_status'] = $low_stock_status;
                    $store_products = Store_products::create($store_product);

                    updateProductStockByData($product->id, $store_id, $new_quantity, 1, 1, 0, 0, 'Add Product');
                }

                $new_store_product['store_id'] = $store_id;
                $new_store_product['quantity'] = empty($requestData['store_quantity_'.$store_category_id]) ? 0 : $requestData['store_quantity_'.$store_category_id];

                array_push($new_store_ids, $new_store_product);


            }

            if($store_category[0] == "category" || $store_category[0] == "subcategory"){
                $category_id =  $store_category[1];

                if($store_category[0] == "category"){
                    // save store product
                    $store_id = Categories::find($category_id)->store_id;

                    $store_product['product_id'] = $product->id;
                    $store_product['store_id'] = $store_id;

                    $store_products = Store_products::firstOrNew($store_product);
                    $store_products->save();
                }

                if($store_category[0] == "subcategory"){

                    $parent_id = Categories::find($category_id)->parent_id;

                    $store_id = Categories::find($parent_id)->store_id;

                    // save store product
                    $store_product['product_id'] = $product->id;
                    $store_product['store_id'] = $store_id;

                    $store_products = Store_products::firstOrNew($store_product);
                    $store_products->save();

                    // save category products
                    $category_product['product_id'] = $product->id;
                    $category_product['category_id'] = $parent_id;

                    $category_products = Category_products::firstOrNew($category_product);
                    $category_products->save();
                }

                // save category products
                $category_product['product_id'] = $product->id;
                $category_product['category_id'] = $category_id;

                $category_products = Category_products::firstOrNew($category_product);
                $category_products->save();
            }
        }

        foreach($old_store_ids as $old_store_id){
            if(find_key_value($new_store_ids,'store_id',$old_store_id['store_id'])){

            }else{
                // Check store product exist or not
                $store_product_data = Store_products::where('product_id',$product->id)->where('store_id',$old_store_id['store_id'])->first();

                if($store_product_data){
                    // stock remove
                    $remove_quantity = $store_product_data->quantity;
                    updateProductStockByData($product->id, $old_store_id['store_id'], $remove_quantity, 2, 2, 0, 0, 'Edit Product');
                }
            }
        }

        // sync products
        updateSyncData('product',$product->id);

        Session::flash('success', 'Product successfully updated!');

        return redirect('company/products/'. Hashids::encode($product->id) .'/edit?tab=2');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return JS0N Response
     */
    public function updateComboProducts($id, Request $request)
    {
        $id = Hashids::decode($id)[0];

        $this->validate($request, [
            'price' => 'required',
        ]);

        $product = Product::findOrFail($id);

        $requestData = $request->all();
        $requestData['cost'] = empty($requestData['cost']) ? 0 : $requestData['cost'];
        $requestData['price'] = empty($requestData['price']) ? 0 : $requestData['price'];

        $product->update($requestData);

        // save combo products
        for($c = 1; $c <= $requestData['total_combos']; $c++){

            $combo_data['id'] = $requestData['combo_id_'. $c];

            $combo = Product_combos::firstOrNew($combo_data);
            $combo->combo_product_id = $product->id;
            $combo->product_id = $requestData['product_id_'. $c];
            $combo->save();
        }

        // sync products
        updateSyncData('product',$product->id);

        Session::flash('success', 'Product successfully updated!');

        return redirect('company/products/'. Hashids::encode($product->id) .'/edit?tab=3');
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
            // sync products
            $product_store_ids = Store_products::where('product_id',$product->id)->pluck('store_id');
            updateSyncData('delete_product',$product->id,$product_store_ids);

            $product->delete();

            $response['success'] = 'Product deleted!';
            $status = $this->successStatus;
        }else{
            $response['error'] = 'Product not exist against this id!';
            $status = $this->notFoundStatus;
        }

        return response()->json(['result'=>$response], $status);
    }

    /**
     * getAllStoreCategories function
     *
     * @return \Illuminate\Http\Response
     */

    public function getAllStoreCategories($product_id = 0){

        $product_type = 0;
        if($product_id>0)
            $product_type = Product::find($product_id)->type;

        $store_categories = Store::with(
            [
                'categories' => function ($query) {
                    $query->where('parent_id', 0);
                },
                'store_products'
            ]
        )->where('company_id',Auth::id())->get();

        $store_categories->map(function ($store) use ($product_id, $product_type){

            $store['data_id'] = 'store-'. $store->id;
            $store['text'] = $store->name .' <b>(Store)</b>';
            $store['imageCssClass'] = 'glyphicon glyphicon-home';

            if($product_id>0){
                $store['checked'] = $store->store_products->where('quantity','>',0)->contains('product_id', $product_id);
            }else{
                if($store->id == companySettingValue('store_id'))
                    $store['checked'] = true;
            }

            if($product_type != 3){
                if($store->categories->count() > 0){
                    $categories = $store->categories->map(function ($category) use ($product_id) {

                        $category['data_id'] = 'category-'. $category->id;
                        $category['text'] = $category->category_name;
                        $category['imageCssClass'] = 'glyphicon glyphicon-list';
                        $category['checked'] = $category->category_products->contains('product_id', $product_id);

                        if($category->subcategories->count() > 0){
                            $subcategories = $category->subcategories->map(function ($subcategory) use ($product_id) {
                                $subcategory['data_id'] = 'subcategory-'. $subcategory->id;
                                $subcategory['text'] = $subcategory->category_name;
                                $subcategory['imageCssClass'] = 'glyphicon glyphicon-list';
                                $subcategory['checked'] = $subcategory->category_products->contains('product_id', $product_id);

                                return $subcategory;
                            });

                            $category['children'] =  $subcategories;
                        }

                        return $category;
                    });

                    $store['children'] =  $categories;
                }
            }

            unset($store['company_id']);
            unset($store['currency_id']);
            unset($store['image']);
            unset($store['address']);
            unset($store['created_at']);
            unset($store['updated_at']);
            return $store;
        });

        // dd($store_categories->toJson());

        return response()->json($store_categories);
    }

    /**
     * getStoreCategories function
     *
     * @param  int  $store_id
     *
     * @return \Illuminate\Http\Response
     */

    public function getStoreCategories($store_id){


        $all_categories = Categories::where('store_id',$store_id)->get();

        $categories = [];
        if($all_categories){
            $status = true;
            // $categories[0] = ['id'=>'','text'=>'Select Categories'];
            $this->getCategoriesRecursive($all_categories, $categories);
        }else{
            $status = false;
            //$categories[0] = ['id'=>'','text'=>'Please select other store'];
        }


        return response()->json(['status' => $status,'categories' => $categories]);
    }

    // utility method to build the categories tree
    function getCategoriesRecursive($all_categories, &$categories, $parent_id = 0, $depth = 0)
    {
        $cats = $all_categories->filter(function ($item) use ($parent_id) {
            return $item->parent_id == $parent_id;
        });

        foreach ($cats as $key => $cat)
        {
            $categories[$key] = array(
                "id" => $cat->id,
                "text" => str_repeat('-', $depth) .' '. $cat->category_name,
            );

            $this->getCategoriesRecursive($all_categories, $categories, $cat->id, $depth + 1);
        }
    }

    /**
     * getProductsMap function
     *
     * @param  int  $products
     *
     * @return \Illuminate\Http\Response
     */
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

            return $product;

        });

        return $products->all();
    }
    function getProductDiscount($product){
        $cat_ids = $product->category_products->pluck('category_id')->toArray();
        $discount_ids = DiscountCategory::whereIn('cat_id',$cat_ids)->pluck('discount_id')->toArray();
        $discounts = Discount::whereIn('id',$discount_ids)

            ->where(function ($query){
                $query->where('start_time' ,'<=',date('Y-m-d H:i:s'));
                $query->where('end_time' ,'>=',date('Y-m-d H:i:s'));
                $query->orWhere('start_time',null);
            })
            ->get();
        $discount_array = [];
        if($product->discount_type == 1){
            $discount_amount = ($product->discount/100) * $product->price;
            $discount_array[0]['amount'] = $discount_amount;
            $discount_array[0]['discount_type'] = 'Percentage';
            $discount_array[0]['original_percentage'] = $product->discount;
        } else {
            $discount_array[0]['amount'] = $product->discount;
            $discount_array[0]['discount_type'] = 'Flat';
            $discount_array[0]['original_percentage'] = 0;
        }
        $i = 1;
        foreach($discounts as $discount){
            if($discount->discount_type == 'Percentage'){
                $discount_amount = ($discount->discount_amount/100) * $product->price;
                $discount_array[$i]['amount'] = $discount_amount;
                $discount_array[$i]['discount_type'] = 'Percentage';
                $discount_array[$i]['original_percentage'] = $discount->discount_amount;
                //dd($discount_amount,$product->price,$discount->discount_amount);
            } else {
                $discount_array[$i]['amount'] = $discount->discount_amount;
                $discount_array[$i]['discount_type'] = 'Flat';
                $discount_array[$i]['original_percentage'] = 0;
            }
            $i++;
        }
        $data = $this->findMaxDiscount($discount_array);
        return $data;
    }
    function findMaxDiscount($discount_array){
        $setting = DB::table('company_settings')->first();
        if($setting->discount_status == 'High'){
            $max = 0;
            $discount = [];
            $discount['amount'] = $discount_array[0]['amount'];
            $discount['discount_type'] = $discount_array[0]['discount_type'];
            $discount['original_percentage'] = $discount_array[0]['original_percentage'];
            foreach($discount_array as $key => $value){
                if($value['amount'] > $max){
                    $max = $value['amount'];
                    $discount['amount'] = $value['amount'];
                    $discount['discount_type'] = $value['discount_type'];
                    $discount['original_percentage'] = $value['original_percentage'];
                }
            }
            return $discount;
        }
        else {
            $min = $discount_array[0]['amount'];
            $discount = [];
            $discount['amount'] = $discount_array[0]['amount'];
            $discount['discount_type'] = $discount_array[0]['discount_type'];
            foreach($discount_array as $key => $value){
                if($value['amount'] < $min){
                    $min = $value['amount'];
                    $discount['amount'] = $value['amount'];
                    $discount['discount_type'] = $value['discount_type'];
                    $discount['original_percentage'] = $value['original_percentage'];
                }
            }
            return $discount;
        }

    }

    /**
     * Method : POST
     *
     * @param \Illuminate\Http\Request $request
     *
     *  @return image id and name
     */
    public function storeImage(Request $request)
    {

        if($request->file('file') && $request->file('file')->isValid()){
            $destinationPath = 'uploads/products'; // upload path
            $image = $request->file('file'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = str_random(12).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //insert image record
            $product_image['product_id'] = 0;
            $product_image['name'] = $fileName;
            $product_image = Product_images::create($product_image);

            if($product_image){
                return response()->json(['id'=>$product_image->id,'name'=>$fileName]);
            }else{
                return response()->json(['message' => 'Error while saving image'],422);
            }
        }else{
            return response()->json(['message' => 'Invalid image'],422);
        }
    }

    /**
     * Method : DELETE
     *
     * @return delete images
     */
    public function deleteImage($id)
    {
        $image = Product_images::findOrFail($id);
        if ($image && count($image) > 0) {

            $file = public_path() . '/uploads/products/'.$image->name;
            $thumbFile = public_path() . '/uploads/products/thumbs/'.$image->name;
            if(is_file($file)){
                @unlink($file);
                @unlink($thumbFile);
            }

            // sync products
            updateSyncData('product',$image->product_id);

            $image->delete();
        }

        return response()->json(['success' => 1]);
    }

    /**
     * Method : POST
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return message
     */
    public function setDefaultImage(Request $request)
    {

        Product_images::whereIn('id',$request->image_ids)->update(['default'=>0]);

        if($request->checked == 1){
            $product_image = Product_images::find($request->image_id);
            $product_image->default = 1;
            $product_image->update();

            // sync products
            updateSyncData('product',$product_image->product_id);
        }

    }

    /**
     * getComboProducts function
     *
     * @param  int  $request
     *
     * @return \Illuminate\Http\Response
     */
    public function getComboProducts(Request $request)
    {

        $products = Product::where('product_id',0)->where('type',1)->company(getComapnyIdByUser());

        if(!empty($request->q)){
            $products->where('name', 'ilike', '%'.$request->q.'%')
                ->orWhere('code', 'ilike', '%'.$request->q.'%')
                ->orWhere('sku', 'ilike', '%'.$request->q.'%');
        }

        $products = $products->limit(10)->get();

        $products->map(function ($product) {

            $product['text'] = $product->name .' (Cost: '. $product->cost .'| Price: '. $product->cost .')';

            return $product;
        });

        $products = $products->all();

        $status = $this->successStatus;

        return response()->json(['results' => $products], $status);

    }

    /**
     * getProductAttributes function
     *
     * @param  int  $product_id
     *
     * @return \Illuminate\Http\Response
     */

    public function getProductAttributes($product_id){


        $product_attributes = Product_attribute::with(['variant'])->where('product_id',$product_id)->orderBy('id','asc')->get();


        return response()->json(['product_attributes' => $product_attributes]);
    }

    /**
     * Method : POST
     *
     * @param \Illuminate\Http\Request $request
     *
     *  @return response
     */
    public function createProductAttribute(Request $request)
    {
        if($request->attribute_id == 0){
            $product_variant['product_id'] =  $request->product_id;
            $product_variant['variant_id'] =  $request->attribute_id;

            $product_variants = Product_attribute::firstOrNew($product_variant);
            $product_variants->save();
        }elseif($request->attribute_id > 0){
            $product_variant['product_id'] =  $request->product_id;
            $product_variant['variant_id'] =  $request->attribute_id;

            $product_variants = Product_attribute::updateOrCreate($product_variant);
            $product_variants->save();
        }

        // sync products
        updateSyncData('product',$request->product_id);

    }

    /**
     * Method : DELETE
     *
     * @return delete product attribute
     */
    public function removeProductAttribute($id)
    {
        $attribute = Product_attribute::findOrFail($id);
        if ($attribute) {

            // sync products
            updateSyncData('product',$attribute->product_id);

            $attribute->delete();
        }

        return response()->json(['success' => 1]);
    }

    /**
     * getProductVariants function
     *
     * @param  int  $product_id
     *
     * @return \Illuminate\Http\Response
     */

    public function getProductVariants($product_id){

        $product_variants = Product::where('product_id',$product_id)->orderBy('id','asc')->get();

        $product_variants->map(function ($variant) {

            $variant['encoded_id'] = Hashids::encode($variant['id']);

            return $variant;
        });

        return response()->json(['product_variants' => $product_variants]);
    }

    /**
     * Method : POST
     *
     * @param \Illuminate\Http\Request $request
     *
     *  @return response
     */
    public function createProductVariant(Request $request)
    {
        $product = Product::with('store_products')->find($request->product_id);
        $post_data = array();
        parse_str($request->attribute_data, $post_data);

        $variant_name = [];
        for($i=1; $i<=(int)$post_data['total_attributes']; $i++){
            $variant_name[] = $post_data['attribute-'.$i];
        }

        $requestData['company_id'] = Auth::id();
        $requestData['product_id'] = $product->id;
        $requestData['name'] = $product->name .' - '. implode(', ', $variant_name);
        $requestData['cost'] = empty($post_data['cost']) ? 0 : $post_data['cost'];
        $requestData['price'] = empty($post_data['price']) ? 0 : $post_data['price'];
        $requestData['code'] = 0;
        $requestData['sku'] = 0;
        $requestData['discount_type'] = 0;
        $requestData['discount'] = 0;
        $requestData['supplier_id'] = 0;
        $requestData['tax_rate_id'] = companySettingValue('tax_id');
        if(isset($post_data['is_main_price'])){
            $requestData['is_main_price'] = $post_data['is_main_price'];
        } else {
            $requestData['is_main_price'] = 0;
        }


        $variant_product = Product::create($requestData);

        if($variant_product){
            for($i=1; $i<=(int)$post_data['total_attributes']; $i++){
                $variant_data['product_attribute_id'] = $post_data['attribute-id-'.$i];
                $variant_data['variant_product_id'] = $variant_product->id;

                $product_variant = Product_variant::firstOrNew($variant_data);
                $product_variant->name= $post_data['attribute-'.$i];
                $product_variant->save();
            }

            //set first record as default
            $this->setFirstProductAsDefault($variant_product->id);

            $product->store_products->map(function($store_product) use ($variant_product) {

                $variant_store_product = [];
                $variant_store_product['store_id'] =  $store_product->store_id;
                $variant_store_product['product_id'] =  $variant_product->id;
                $variant_store_product['quantity'] =  0;

                Store_products::create($variant_store_product);

                return $store_product;
            });

            // sync products
            updateSyncData('product',$variant_product->product_id);
            //updateSyncData('product',$variant_product->id);
        }


    }

    /**
     * Method : POST
     *
     * @param \Illuminate\Http\Request $request
     *
     *  @return response
     */
    public function setProductAsDefault(Request $request)
    {
        $product = Product::find($request->product_id);

        Product::where('product_id',$product->product_id)->update(['is_default' => 0]);

        $product->is_default = $request->value;
        $product->save();

        //set first record as default
        $this->setFirstProductAsDefault($product->id);

        // sync products
        updateSyncData('product',$product->product_id);
    }

    /**
     *
     * @param $product_id
     *
     */
    private function setFirstProductAsDefault($product_id)
    {
        $product = Product::find($product_id);

        $total_default_products = Product::where('product_id',$product->product_id)->where('is_default' , 1)->count();

        if($total_default_products==0){
            $first_product = Product::where('product_id',$product->product_id)->orderBy('id','asc')->first();
            $first_product->is_default = 1;
            $first_product->save();

            // sync products
            updateSyncData('product',$product->product_id);
        }

    }

    /**
     * Method : DELETE
     *
     * @return delete variant
     */
    public function removeVariant($id)
    {
        $variant = Product_attribute::findOrFail($id);
        if ($variant) {

            // sync products
            updateSyncData('product',$variant->product_id);
            $variant->delete();
        }

        return response()->json(['success' => 1]);
    }


    /**
     * Method : DELETE
     *
     * @return delete combo
     */
    public function removeCombo($id)
    {
        $combo = Product_combos::findOrFail($id);
        if ($combo) {

            // sync products
            updateSyncData('product',$combo->product_id);
            $combo->delete();
        }

        return response()->json(['success' => 1]);
    }


    /**
     * getProductModifiers function
     *
     * @param  int  $product_id
     *
     * @return \Illuminate\Http\Response
     */

    public function getProductModifiers($product_id)
    {

        $product_store_ids = Store_products::where('product_id',$product_id)->pluck('store_id');

        $modifier_ids = Product::where('type',3)->pluck('id');

        $modifer_store_ids = Store_products::whereIn('product_id',$modifier_ids)->pluck('store_id');

        $merged = $product_store_ids->merge($modifer_store_ids);

        $store_unique_ids = $merged->unique();

        $product_ids = Store_products::whereIn('store_id',$store_unique_ids)->pluck('product_id');

        $product_modifiers = Product::where('type',3)->whereIn('id',$product_ids)->get();

        $product_modifiers->map(function($product_modifier) use($product_id){

            $product_modifier['is_checked'] = false;

            $modifier['product_id'] = $product_id;
            $modifier['modifier_id'] = $product_modifier->id;

            $is_modifier = Product_modifier::where($modifier)->first();
            if($is_modifier)
                $product_modifier['is_checked'] = true;

            return $product_modifier;
        });

        return response()->json(['product_modifiers' => $product_modifiers]);
    }

    /**
     * Method : POST
     *
     * @param \Illuminate\Http\Request $request
     *
     *  @return response
     */
    public function setProductModifier(Request $request)
    {
        $product_modifier['product_id'] = $request->product_id;
        $product_modifier['modifier_id'] = $request->modifier_id;

        if($request->value==1){
            $product_modifiers = Product_modifier::firstOrNew($product_modifier);
            $product_modifiers->save();
        }else{
            Product_modifier::where($product_modifier)->delete();
        }

        // sync products
        updateSyncData('product',$request->product_id);

    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return JS0N Response
     */
    public function editVariant($id)
    {
        $id = Hashids::decode($id)[0];

        $product = Product::with(
            [
                'company',
                'product_tags' => function ($query) {
                    $query->orderBy('id', 'asc');
                },
                'product_variants.product_attribute.variant',
                'product.store_products.store',
                'product.product_attributes.variant',
                'category_products.category',
                'product_images'
            ]
        )->findOrFail($id);

        //dd($product->toArray());
        if($product->product_id == 0)
            return redirect('company/products/'. Hashids::encode($product->id) .'/edit');

        return view('company.products.edit-variant', compact('product'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     *
     * @return JS0N Response
     */
    public function updateVariantProduct($id, Request $request)
    {
        //dd($request->all());
        $id = Hashids::decode($id)[0];

        $requestData = $request->all();

        $rules['code'] = 'required|unique:products,code,'.$id;
        $rules['name'] = 'required|max:100';
        $rules['sku'] = 'required|unique:products,sku,'.$id;
        $rules['tax_rate_id'] = 'required';

        if(empty($request->is_main_price)){
            $requestData['is_main_price'] =  0;
            $rules['cost'] = 'required|numeric';
            $rules['price'] = 'required|numeric';
        }else{
            $requestData['cost'] = 0;
            $requestData['price'] = 0;
        }

        $requestData['is_main_tax'] = (empty($requestData['is_main_tax']))? 0 : $requestData['is_main_tax'] ;

        $this->validate($request, $rules);


        $product = Product::with([
            'product_images',
            'store_products',
            'product.store_products.store',
            'product.product_attributes.variant',
            'product_variants'
        ])->findOrFail($id);

        $product->update($requestData);

        // remove product tags
        Product_tag::where('product_id',$product->id)->delete();

        if(!empty($request->tags)){
            $tags = explode(',', $request->tags);
            foreach($tags as $tag){
                $tag_data['product_id'] = $product->id;
                $tag_data['name'] = $tag;
                Product_tag::create($tag_data);
            }
        }

        //save product image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/products'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $product->id.'-'.str_random(10).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            /*unlink old image*/
            if($product->product_images){
                @unlink(public_path("/uploads/products/".$product->product_images[0]->name));
                @unlink(public_path("/uploads/products/thumbs/".$product->product_images[0]->name));
            }

            //update image record
            $product_image['product_id'] = $product->id;
            $product_images = Product_images::firstOrNew($product_image);
            $product_images->default = 1;
            $product_images->name = $fileName;
            $product_images->save();
        }

        // update store stock
        if($product->product->store_products){
            $product->product->store_products->map(function($store_product) use ($product, $requestData) {

                $new_quantity = empty($requestData['store-product-'.$store_product->id]) ? 0 : $requestData['store-product-'.$store_product->id];
                $store_id = $store_product->store->id;

                // Check store product exist or not
                $store_product_data = Store_products::where('product_id',$product->id)->where('store_id',$store_id)->first();

                if($store_product_data){

                    if($new_quantity > $store_product_data->quantity){
                        // stock add
                        $add_quantity = $new_quantity - $store_product_data->quantity;
                        updateProductStockByData($product->id, $store_id, $add_quantity, 1, 2, 0, 0, 'Edit Product');
                    }elseif($new_quantity < $store_product_data->quantity){
                        // stock remove
                        $remove_quantity = $store_product_data->quantity - $new_quantity;
                        updateProductStockByData($product->id, $store_id, $remove_quantity, 2, 2, 0, 0, 'Edit Product');
                    }
                }else{
                    // save new store product
                    $store_product_create['product_id'] = $product->id;
                    $store_product_create['store_id'] = $store_id;
                    $store_product_create['quantity'] = $new_quantity;

                    $store_products = Store_products::create($store_product_create);

                    updateProductStockByData($product->id, $store_id, $new_quantity, 1, 1, 0, 0, 'Add Product');
                }

                return $store_product;
            });
        }

        // product variants
        if($product->product->product_variants){
            $product->product->product_attributes->map(function($product_attribute) use ($product, $requestData) {

                $product_variant = [];
                $product_variant['product_attribute_id'] = $product_attribute->id;
                $product_variant['variant_product_id'] = $product->id;

                $product_variants = Product_variant::firstOrNew($product_variant);
                $product_variants->name= $requestData['variant-name-'.$product_attribute->id];
                $product_variants->save();

                return $product_variant;

            });
        }


        // sync products
        updateSyncData('product',$product->product_id);

        Session::flash('success', 'Product successfully updated!');

        return redirect('company/products');
    }
    public function saveModifierNumber(Request $request){
        Product::where('id',$request->input('product_id'))->update([
            'min_modifier' => $request->input('min'),
            'max_modifier' => $request->input('max')]);

        updateSyncData('product',$request->input('product_id'));
        return response()->json('success');
    }



    public function productSaleHistory($product_id){
        $product_id = Hashids::decode($product_id)[0];
        return view('company.products.sale',compact('product_id'));

    }

   public function getProductSaleHistory($product_id){


        $product_orders = ProductOrder::where('product_id',$product_id)->pluck('order_id');

        $orders =Order::with(['store','customers'])->whereIn('store_id',getStoreIds())->whereIn('id',$product_orders);

        /*if($request->order_type==1){
            $orders->where('order_id',0);
        }elseif($request->order_type==2){
            $orders->where('order_id','!=',0);
        }
        if($customer_id!=0){
            
             $orders =$orders->where('customer',$customer_id);
        }*/


        $orders = $orders->orderBy('updated_at','desc')->get();

        $orders->map(function ($single_order) use($product_id) { 

            $product_order_det = ProductOrder::where('product_id',$product_id)->where('order_id',$single_order->id)->first();
            
            $single_order['reference'] = $single_order->reference;
            $single_order['date'] = date('d/m/Y', strtotime($single_order->created_at));

            $single_order['store_name'] = $single_order->store->name;

            if($single_order->customers){

                $single_order['customer']=$single_order->customers->first_name.' '.$single_order->customers->last_name;

            }else{
                $single_order['customer']='';
            }

            if($single_order->order_id == 0){

                $single_order['order_type'] = '<a href="javascript:void(0)" class= data-toggle="tooltip" title="Sales">Sales</a>';

            }else if($single_order->order_id != 0){

                $single_order['order_type'] = '<a href="javascript:void(0)" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Sales Return">Return</a>';

            }

            $single_order['quantity'] = $product_order_det->quantity;
            $single_order['order_total'] = number_format($product_order_det->quantity*$product_order_det->price,2);

            return $single_order;

        });

       // print_r($orders);
        return Datatables::of($orders)
            ->addColumn('date', function ($order) {
                return $order->date;
            })
            ->addColumn('reference', function($order){
                return $order->reference;
            })
            ->addColumn('store_name', function ($order) {
                return $order->store_name;
            })
            ->addColumn('customer', function ($order) {
                return $order->customer;
            })
            ->addColumn('order_type', function ($order) {
                return $order->order_type;
            })
            ->addColumn('qunatity', function ($order) {

                return number_format($order->qunatity, 2);
            })
            ->addColumn('order_total', function ($order) {
                return number_format($order->order_total, 2);
            })
            
            ->addColumn('action', function ($order) {
                return '<a href="'. url('/company/invoice/'.Hashids::encode($order->id)).'" class="text-success btn-order" data-toggle="tooltip" title="View Order" id="'.$order->id.'"><i class="fa fa-eye"></i></a>';
               
            })
            ->rawColumns(['date', 'reference', 'store_name', 'customer','order_type', 'qunatity','order_total', 'action'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
    }
    public function importUsers(Request $request)
    {
        $this->validate(request(), [
            'users' => 'required',
        ], ['users.required' => 'Select File']);

        $user = $request->file('users');
        $file_type = $user->getClientOriginalExtension();
        if ($file_type != 'csv') {
            return redirect()->back()->with('error', 'Upload csv file');
        }
        //dd($user);
        $public_path = '/users_data';
        $destinationPath = public_path($public_path);
        $filename = $user->getClientOriginalName();
        $user->move($destinationPath, $filename);

        $base_path = env('CSV', 'public/users_data//');
        Excel::load($base_path . $filename, function ($reader) {
            $users = array();
            $results = $reader->get();
            $sort_id = DB::table('users')->max('sort_id');
            if (!isset($sort_id)) {
                $sort_id = 0;
            }
            $user1 = DB::table('users')->where('user_type', 0)->inRandomOrder()->first();
            if ($user1 != null) {
                $user_pass = $user1->user_pass;
            } else {
                $user_pass = 123456789;
            }
            foreach ($results as $result) {
                if (isset($result['email'])) {
                    $sort_id = $sort_id + 1;
                    $users[] = [
                        'user_name' => $result['user_name'],
                        'email' => $result['email'],
                        'password' => bcrypt($user_pass),
                        'user_pass' => $user_pass,
                        'phone' => $result['phone'],
                        'first_name' => $result['first_name'],
                        'last_name' => $result['last_name'],
                        'user_type' => 0,
                        'sort_id' => $sort_id
                    ];
                }
            }
            $users = $this->unique_multidim_array($users, 'email');
            $this->usersRepo->insertMultiple($users);

        });
    }


}
