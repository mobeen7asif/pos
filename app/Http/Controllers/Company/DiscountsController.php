<?php

namespace App\Http\Controllers\Company;

use App\Discount;
use App\DiscountBogo;
use App\DiscountCategory;
use App\DiscountProduct;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Product;
use App\Store_products;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;
use App\Categories;
use App\Category_sections;
use App\Section_questions;
use App\Question_answers;
use App\Items;
use App\Store;
use App\Category_products;

class DiscountsController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
     
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function showDiscounts()
    {

        $store = Store::where('company_id',Auth::user()->id)->first();
        return view('company.discounts.index',['store' => $store]);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getDiscounts()
    {
//        $store_ids = Store::where('company_id',Auth::id())->pluck('id');
//        $discount_category_ids = DiscountCategory::whereIn('store_id',$store_ids)->pluck('discount_id');
//        $discount_ids = Discount::whereIn('id',$discount_category_ids)->pluck('id');
        $discounts = Discount::with('discountCategories','discountBogo')->where('company_id',Auth::user()->id)->get();
        return Datatables::of($discounts)
            ->addColumn('date_time', function ($discount) {
                if(!isset($discount->date_time) || $discount->date_time == null){
                    return 'No Time Selection';
                } else {
                    return $discount->date_time;
                }
            })
            ->addColumn('discount_detail', function ($discount) {
                if(count($discount->discountCategories) > 0){
                    $discount_categories  = $discount->discountCategories;
                    $temp = [];
                    foreach($discount_categories as $discount_category){
                        $cat = $discount_category->category;
                        $temp[] = $cat->category_name;
                    }
                    return implode(', ',$temp);
                } else {
                    if($discount->discountBogo->first()->type == 'category'){
                        $temp_category = [];
                        $temp_category[] = Categories::find($discount->discountBogo->first()->from_id);
                        foreach($discount->discountBogo as $discountBogo){
                            $temp_category[] = Categories::find($discountBogo->to_id);
                        }
                        $temp = [];
                        foreach($temp_category as $category){
                            $cat = $category;
                            $temp[] = $cat->category_name;
                        }
                        return implode(', ',$temp);
                    } else {
                        $temp_product = [];
                        $temp_product[] = Product::find($discount->discountBogo->first()->from_id);
                        foreach($discount->discountBogo as $discountBogo){
                            $temp_product[] = Product::find($discountBogo->to_id);
                        }
                        $temp = [];
                        foreach($temp_product as $product){
                            $temp[] = $product->name;
                        }
                        return implode(', ',$temp);
                    }
                }


            })
            ->addColumn('discount_amount', function ($discount) {
                if($discount->discount_type == 1){
                    return $discount->amount.'%';
                } else {
                    return $discount->amount;
                }

            })
            ->addColumn('action', function ($discount) {
                if(count($discount->discountCategories) > 0){
                    $edit = '<a href="discounts/'. Hashids::encode($discount->id).'/'.Hashids::encode($discount->discountCategories->first()->store_id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Discount"><i class="fa fa-edit action-padding"></i></a>';
                } else {
                    $edit = '<a href="discounts_bogo/'. Hashids::encode($discount->id).'/'.Hashids::encode($discount->discountBogo->first()->store_id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Discount"><i class="fa fa-edit action-padding"></i></a>';
                }
                return $edit.'
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Discount" id="'.Hashids::encode($discount->id).'"><i class="fa fa-trash action-padding"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['categories', 'discount_type','discount_amount', 'action'])
            ->make(true);
            
    }

    public function editView($id,$store_id)
    {
        $id = Hashids::decode($id)[0];
        $discount = Discount::with(['discountCategories','discountProducts'])->where('id',$id)->first();
        //get categories
        $store_id = Hashids::decode($store_id)[0];
        $all_categories = Categories::with(['store'])->whereIn('store_id',[$store_id])->get();
        $categories = [];
        $this->getCategoriesRecursive($all_categories, $categories);
        $modified_categories = [];
        foreach ($categories as $cat) {
            $modified_categories[] = (object)$cat;
        }
        $categories = collect($modified_categories);
        $discount_type = ['2' => 'Flat' , '1' => 'Percentage'];
        $discount_type = collect($discount_type);
        //get company stores
        $stores = Store::where('company_id',Auth::id())->get();
        $product_ids = DiscountProduct::where('discount_id',$id)->pluck('product_id');
        $cat_ids = DiscountCategory::where('discount_id',$id)->pluck('cat_id');
        $product_ids = Category_products::whereIn('category_id',$cat_ids)->pluck('product_id');
        $products = Product::whereIn('id',$product_ids)->get();
        return view('company.discounts.edit',['stores' => $stores,'products' => $products, 'discount' => $discount,'categories' => $categories,'discount_type' => $discount_type]);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function createDiscountView($store_id)
    {
        $store_id = request()->route()->parameter('store_id');
        $store_id = Hashids::decode($store_id)[0];
        //get categories
        $all_categories = Categories::with(['store'])->whereIn('store_id',[$store_id])->get();
        $categories = [];
        $this->getCategoriesRecursive($all_categories, $categories);
        $modified_categories = [];
        foreach($categories as $cat){
            $modified_categories[] = (object)$cat;
        }
        $categories = collect($modified_categories);
        $discount_type = ['2' => 'Flat' , '1' => 'Percentage'];
        $discount_type = collect($discount_type);
        //get company stores
        $stores = Store::where('company_id',Auth::id())->get();
        return view('company.discounts.create', compact('categories','discount_type','stores'));
    }
    public function createDiscount(Request $request){
        $request_data = $request->all();
        if(!isset($request_data['date_time'])){
            $date_time = null;
            $start_time = null;
            $end_time = null;
        } else {
            $date_time = $request_data['date_time'];
            $input_date_time = $request_data['date_time'];
            $exploded_date = explode(' - ',$input_date_time);
            $date = new \DateTime($exploded_date[0]);
            $start_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['start_time'] = $start_time;

            $date = new \DateTime($exploded_date[1]);
            $end_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['end_time'] = $end_time;

        }
        $store_id = Hashids::decode($request_data['store_id'])[0];
        $discount = Discount::create([
            'company_id' => Auth::user()->id,
            'amount' => $request_data['discount_amount'],
            'discount_type' => $request_data['discount_type'],
            'date_time' => $date_time,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'name' => $request_data['name'],
            'store_id' => $store_id,
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        $discount_categories = [];

        foreach ($request_data['category'] as $category_id){
            $temp = [
                'discount_id' => $discount->id,
                'cat_id' => $category_id,
                'store_id' => $store_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $discount_categories[] = $temp;
            updateSyncData('discount',$discount->id,$store_id);
        }
        DiscountCategory::insert($discount_categories);

        //insert product discounts
        $check_products = $request->input('check_products');
        if(isset($check_products)){
//            $product_ids = Category_products::whereIn('category_id',$request_data['category'])->pluck('product_id');
//            $products = Product::whereIn('id',$product_ids)->get();
//            $discount_products = [];
//            foreach ($products as $product){
//                $temp = [
//                    'discount_id' => $discount->id,
//                    'product_id' => $product->id,
//                    'store_id' => $store_id,
//                    'created_at' => date('Y-m-d H:i:s'),
//                    'updated_at' => date('Y-m-d H:i:s')
//                ];
//                $discount_products[] = $temp;
//                //updateSyncData('discount',$discount->id,$store_id);
//            }
//            DiscountProduct::insert($discount_products);
        } else {
            $discount_products = [];
            if(isset($request_data['products'])){
                foreach ($request_data['products'] as $product_id){
                    $temp = [
                        'discount_id' => $discount->id,
                        'product_id' => $product_id,
                        'store_id' => $store_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $discount_products[] = $temp;
                    updateSyncData('discount',$discount->id,$store_id);
                }
                DiscountProduct::insert($discount_products);
            }

        }

        Session::flash('success', 'Discount Added');

        return redirect('company/discounts');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */


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
        
        $category = Categories::with(['category'])->find($id); 
        
        if ($category->category) {
            $categories = ['0' => 'Root Category', $category->category->id => $category->category->category_name];
        } else {
            $categories = ['0' => 'Root Category'];
        }
        
        return view('company.categories.edit', compact('category','categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateDiscount($id, Request $request)
    {
        $request_data = $request->all();
        if(!isset($request_data['date_time'])){
            $date_time = null;
            $start_time = null;
            $end_time = null;
        } else {
            $date_time = $request_data['date_time'];
            $input_date_time = $request_data['date_time'];
            $exploded_date = explode(' - ',$input_date_time);
            $date = new \DateTime($exploded_date[0]);
            $start_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['start_time'] = $start_time;
            $date = new \DateTime($exploded_date[1]);
            $end_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['end_time'] = $end_time;

        }
        $store_id = Hashids::decode($request_data['store_id'])[0];
        $id = Hashids::decode($id)[0];
        $discount = Discount::find($id);
        $discount->date_time = $date_time;
        $discount->start_time = $start_time;
        $discount->end_time = $end_time;
        $discount->discount_type = $request->input('discount_type');
        $discount->amount = $request->input('discount_amount');
        $discount->name = $request->input('name');
        $discount->store_id = $store_id;
        $discount->save();
        DiscountCategory::where('discount_id',$id)->delete();
        $discount_categories = [];
        foreach ($request->input('category') as $category_id){
            $temp = [
                'discount_id' => $discount->id,
                'cat_id' => $category_id,
                'store_id' => $store_id,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ];
            $discount_categories[] = $temp;
            updateSyncData('discount',$discount->id,$store_id);
        }
        DiscountCategory::insert($discount_categories);

        DiscountProduct::where('discount_id',$id)->delete();
        //insert product discounts
        $check_products = $request->input('check_products');
        if(isset($check_products)){
//            $product_ids = Category_products::whereIn('category_id',$request_data['category'])->pluck('product_id');
//            $products = Product::whereIn('id',$product_ids)->get();
//            $discount_products = [];
//            foreach ($products as $product){
//                $temp = [
//                    'discount_id' => $discount->id,
//                    'product_id' => $product->id,
//                    'store_id' => $store_id,
//                    'created_at' => date('Y-m-d H:i:s'),
//                    'updated_at' => date('Y-m-d H:i:s')
//                ];
//                $discount_products[] = $temp;
//                //updateSyncData('discount',$discount->id,$store_id);
//            }
//            DiscountProduct::insert($discount_products);
        } else {
            $discount_products = [];
            if(isset($request_data['products'])) {
                foreach ($request_data['products'] as $product_id) {
                    $temp = [
                        'discount_id' => $discount->id,
                        'product_id' => $product_id,
                        'store_id' => $store_id,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];
                    $discount_products[] = $temp;
                    updateSyncData('discount',$discount->id,$store_id);
                }
                DiscountProduct::insert($discount_products);
            }
        }


        Session::flash('success', 'Discount updated!');

        return redirect('company/discounts');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function deleteDiscount($id)
    {
        $id = Hashids::decode($id)[0];
        $discount = Discount::find($id);
        if($discount){
            $discount_category = DiscountCategory::where('discount_id',$discount->id)->first();
            updateSyncData('discount_delete',$discount->id,$discount_category->store_id);
            $discount->delete();
            $response['success'] = 'Discount deleted!';
            $status = $this->successStatus;
        }else{
            $response['error'] = 'Discount not exist against this id!';
            $status = $this->errorStatus;
        }
        
        return response()->json(['result'=>$response], $status);

    }

    function getCategoriesRecursive($all_categories, &$categories, $parent_id = 0, $depth = 0)
    {
        $cats = $all_categories->filter(function ($item) use ($parent_id) {
            return $item->parent_id == $parent_id;
        });

        foreach ($cats as $cat)
        {
            $categories[$cat->id] = array(
                "id" => $cat->id,
                "category_image" => $cat->category_image,
                "parent_id" => $cat->parent_id,
                "category_name" => str_repeat(' -', $depth) .' '. $cat->category_name,
                "store_name" => @$cat->store->name,
                "created_at" => $cat->created_at,
            );

            $this->getCategoriesRecursive($all_categories, $categories, $cat->id, $depth + 1);
        }
    }
    function getStoreCategories(Request $request){
        $store_id = $request->get('id');
        $all_categories = Categories::with(['store'])->where('store_id',$store_id)->get();
        $categories = [];
        $this->getCategoriesRecursive($all_categories, $categories);
        $modified_categories = [];
        foreach($categories as $cat){
            $modified_categories[] = (object)$cat;
        }
        $categories = collect($modified_categories);
        return response()->json($categories);
    }

    public function addBogoView()
    {
        $stores = Store::where('company_id',Auth::user()->id)->get();
        $discount_type = ['2' => 'Flat' , '1' => 'Percentage'];
        $discount_type = collect($discount_type);
        return view('company.discounts.add_bogo',['stores' => $stores,'discount_type' => $discount_type]);
    }
    function getProductsAjax(Request $request){
        $store_id = $request->get('store_id');
        $product_ids = Store_products::where('store_id',$store_id)->pluck('product_id');
        $products = Product::whereIn('id',$product_ids)->get();
        return response()->json($products);
    }
    function getCategoriesAjax(Request $request){
        $store_id = $request->get('store_id');
        $categories = Categories::where('store_id',$store_id)->get();
        return response()->json($categories);
    }
    public function addBogoDiscount(Request $request){
        $request_data = $request->all();
        $bogo_type = $request->input('bogo_type');
        //validation
        if($bogo_type == 'product'){
            $required_product = $request->input('required_product');
            $optional_products = $request->input('optional_products');
            if(!isset($required_product)){
                return redirect()->back()->withInput()->with(['error' => 'Required Product field is required']);
            }
            if(!isset($optional_products)){
                return redirect()->back()->withInput()->with(['error' => 'Optional Products field is required']);
            }
        } else {
            $required_category = $request->input('required_category');
            $optional_categories = $request->input('optional_categories');
            if(!isset($required_category)){
                return redirect()->back()->withInput()->with(['error' => 'Required Category field is required']);
            }
            if(!isset($optional_categories)){
                return redirect()->back()->withInput()->with(['error' => 'Optional Categories field is required']);
            }
        }
        if(!isset($request_data['date_time'])){
            $date_time = null;
            $start_time = null;
            $end_time = null;
        } else {
            $date_time = $request_data['date_time'];
            $input_date_time = $request_data['date_time'];
            $exploded_date = explode(' - ',$input_date_time);
            $date = new \DateTime($exploded_date[0]);
            $start_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['start_time'] = $start_time;

            $date = new \DateTime($exploded_date[1]);
            $end_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['end_time'] = $end_time;

        }
        $discount = Discount::create([
            'company_id' => Auth::user()->id,
            'amount' => $request_data['discount_amount'],
            'discount_type' => $request_data['discount_type'],
            'date_time' => $date_time,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'store_id' => $request->input('store_id'),
            'name' => $request_data['name'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if($discount){
            if(isset($bogo_type)){
                if($bogo_type == 'product'){
                    $required_product = $request->input('required_product');
                    $optional_products = $request->input('optional_products');
                    $bogo_discount = [];
                    if(isset($optional_products) && count($optional_products) > 0){
                        foreach ($optional_products as $optional_product){
                            $temp = [];
                            $temp['discount_id'] = $discount->id;
                            $temp['store_id'] = $request->input('store_id');
                            $temp['from_id'] = $required_product;
                            $temp['to_id'] = $optional_product;
                            $temp['type'] = $request->input('bogo_type');
                            $temp['created_at'] = date('Y-m-d H:i:s');
                            $temp['updated_at'] = date('Y-m-d H:i:s');
                            $bogo_discount[] = $temp;
                            //updateSyncData('discount',$discount->id,$request->input('store_id'));
                        }
                        DiscountBogo::insert($bogo_discount);
                    }

                }
                else {
                    $required_category = $request->input('required_category');
                    $optional_categories = $request->input('optional_categories');
                    $bogo_discount = [];
                    if(isset($optional_categories) && count($optional_categories) > 0){
                        foreach ($optional_categories as $optional_category){
                            $temp = [];
                            $temp['discount_id'] = $discount->id;
                            $temp['store_id'] = $request->input('store_id');
                            $temp['from_id'] = $required_category;
                            $temp['to_id'] = $optional_category;
                            $temp['type'] = $request->input('bogo_type');
                            $temp['created_at'] = date('Y-m-d H:i:s');
                            $temp['updated_at'] = date('Y-m-d H:i:s');
                            $required_product_ids = Category_products::where('category_id',$required_category)->pluck('product_id')->toArray();
                            $required_product_ids = implode(',',$required_product_ids);
                            $temp['from_product_ids'] = $required_product_ids;

                            $optional_product_ids = Category_products::where('category_id',$optional_category)->pluck('product_id')->toArray();
                            $optional_product_ids = implode(',',$optional_product_ids);
                            $temp['to_product_ids'] = $optional_product_ids;


                            $bogo_discount[] = $temp;
//                            updateSyncData('discount',$discount->id,$request->input('store_id'));
                        }
                        DiscountBogo::insert($bogo_discount);
                    }

                }
            }
        }
        updateSyncData('discount_bogo',$discount->id,$request->input('store_id'));
        Session::flash('success', 'Bogo Discount Added');

        return redirect('company/discounts');
    }
    public function editBogoView($discount_id,$store_id){
        $discount_id = Hashids::decode($discount_id)[0];
        $store_id = Hashids::decode($store_id)[0];
        $discount = Discount::with('discountBogo')->where('id',$discount_id)->first();
        $bogo_type = $discount->discountBogo->first()->type;

        //get products against store_id
        $product_ids = Store_products::where('store_id',$store_id)->pluck('product_id');
        $products = Product::whereIn('id',$product_ids)->get();

        //get categories against store_id
        $categories = Categories::where('store_id',$store_id)->get();

        $discount_type = ['2' => 'Flat' , '1' => 'Percentage'];
        $discount_type = collect($discount_type);
        $stores = Store::where('company_id',Auth::user()->id)->get();
        return view('company.discounts.update_bogo',['store_id' => $store_id,
            'discount' => $discount,
            'bogo_type' => $bogo_type,
            'products' => $products,
            'categories' => $categories,
            'discount_type' => $discount_type,
            'stores' => $stores
        ]);

    }
    public function updateBogoDiscount(Request $request){
        $request_data = $request->all();
        //validation
        $bogo_type = $request->input('bogo_type');
        if($bogo_type == 'product'){
            $required_product = $request->input('required_product');
            $optional_products = $request->input('optional_products');
            if(!isset($required_product)){
                return redirect()->back()->withInput()->with(['error' => 'Required Product field is required']);
            }
            if(!isset($optional_products)){
                return redirect()->back()->withInput()->with(['error' => 'Optional Products field is required']);
            }
        } else {
            $required_category = $request->input('required_category');
            $optional_categories = $request->input('optional_categories');
            if(!isset($required_category)){
                return redirect()->back()->withInput()->with(['error' => 'Required Category field is required']);
            }
            if(!isset($optional_categories)){
                return redirect()->back()->withInput()->with(['error' => 'Optional Categories field is required']);
            }
        }
        if(!isset($request_data['date_time'])){
            $date_time = null;
            $start_time = null;
            $end_time = null;
        } else {
            $date_time = $request_data['date_time'];
            $input_date_time = $request_data['date_time'];
            $exploded_date = explode(' - ',$input_date_time);
            $date = new \DateTime($exploded_date[0]);
            $start_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['start_time'] = $start_time;

            $date = new \DateTime($exploded_date[1]);
            $end_time = date_format($date, 'Y-m-d H:i:s');
            $request_data['end_time'] = $end_time;

        }
        $discount = Discount::find($request_data['discount_id']);
        $discount_attr = [
            'company_id' => Auth::user()->id,
            'amount' => $request_data['discount_amount'],
            'discount_type' => $request_data['discount_type'],
            'date_time' => $date_time,
            'start_time' => $start_time,
            'end_time' => $end_time,
            'store_id' => $request->input('store_id'),
            'name' => $request_data['name'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s')
        ];
        $discount->update($discount_attr);
        $discount = Discount::find($request_data['discount_id']);
        DiscountBogo::where('discount_id',$discount->id)->delete();
        if($discount){
            $bogo_type = $request->input('bogo_type');
            if(isset($bogo_type)){
                if($bogo_type == 'product'){
                    $required_product = $request->input('required_product');
                    $optional_products = $request->input('optional_products');
                    $bogo_discount = [];
                    if(isset($optional_products) && count($optional_products) > 0){
                        foreach ($optional_products as $optional_product){
                            $temp = [];
                            $temp['discount_id'] = $discount->id;
                            $temp['store_id'] = $request->input('store_id');
                            $temp['from_id'] = $required_product;
                            $temp['to_id'] = $optional_product;
                            $temp['type'] = $request->input('bogo_type');
                            $temp['created_at'] = date('Y-m-d H:i:s');
                            $temp['updated_at'] = date('Y-m-d H:i:s');
                            $bogo_discount[] = $temp;
                        }
                        DiscountBogo::insert($bogo_discount);
                    }

                }
                else {
                    $required_category = $request->input('required_category');
                    $optional_categories = $request->input('optional_categories');
                    $bogo_discount = [];
                    if(isset($optional_categories) && count($optional_categories) > 0){
                        foreach ($optional_categories as $optional_category){
                            $temp = [];
                            $temp['discount_id'] = $discount->id;
                            $temp['store_id'] = $request->input('store_id');
                            $temp['from_id'] = $required_category;
                            $temp['to_id'] = $optional_category;
                            $temp['type'] = $request->input('bogo_type');
                            $temp['created_at'] = date('Y-m-d H:i:s');
                            $temp['updated_at'] = date('Y-m-d H:i:s');

                            $required_product_ids = Category_products::where('category_id',$required_category)->pluck('product_id')->toArray();
                            $required_product_ids = implode(',',$required_product_ids);
                            $temp['from_product_ids'] = $required_product_ids;

                            $optional_product_ids = Category_products::where('category_id',$optional_category)->pluck('product_id')->toArray();
                            $optional_product_ids = implode(',',$optional_product_ids);
                            $temp['to_product_ids'] = $optional_product_ids;
                            $bogo_discount[] = $temp;
                        }
                        DiscountBogo::insert($bogo_discount);
                    }

                }
            }
        }
        updateSyncData('discount_bogo',$discount->id,$request->input('store_id'));
        Session::flash('success', 'Bogo Discount Updated');

        return redirect('company/discounts');
    }




}
