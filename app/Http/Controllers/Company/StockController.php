<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Validator;
use Datatables;
use App\Store;
use App\Stock;
use App\Product;
use App\Store_products;

class StockController extends Controller
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

//        $store_ids = Store::where('company_id',Auth::id())->pluck('id');
//
//        $stocks = Stock::with(['store','product'])->whereIn('store_id',$store_ids)->orderBy('id','desc')->get();
//
//        return $stocks;
        return view('company.stocks.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getStocks()
    {        
        $store_ids = Store::where('company_id',Auth::id())->pluck('id');
        
        $stocks = Stock::with(['store','product'])->whereIn('store_id',$store_ids)->orderBy('id','desc')->get();
             
        
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
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {   

        return view('company.stocks.create');                
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
            'store_id' => 'required',
            'product_id' => 'required',
            'stock_type' => 'required',
            'quantity' => 'required|numeric',
        ]);
        
        
        
        if($request->stock_type == 2){
            
           $store_product = Store_products::where('product_id',$request->product_id)->where('store_id',$request->store_id)->first();
           
           $rules['quantity'] = 'required|numeric|max:'.$store_product->quantity;
           $this->validate($request, $rules);
        }           
        
        
        
        $requestData = $request->all();              
        $requestData['origin'] = 5;              
        
        $stock = Stock::create($requestData);
        
        if($stock)
            updateProductStock($stock->product_id, $stock->store_id);
             updateSyncData('product', $stock->product_id);
            
        Session::flash('success', 'Stock Adjustment added!');        

        return redirect('company/manage-stocks');  
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
        return redirect('company/manage-stocks/create');  
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
        
        return redirect('company/manage-stocks/create');  
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
    }    
    
    
    /**
     * getStoreProducts function
     *      
     * @param  int  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function getStoreProducts($store_id, Request $request)
    {      
        $product_ids = Store_products::where('store_id',$store_id)->pluck('product_id');                                    
       
        $products = Product::with([
                'store_products' => function ($query) use ($store_id) {
                        $query->where('store_id', $store_id);
                    }
            ])->whereIn('id',$product_ids);                                                                
        
        if(!empty($request->q)){
            $products->where('name', 'ilike', '%'.$request->q.'%')
                    ->orWhere('code', 'ilike', '%'.$request->q.'%')
                    ->orWhere('sku', 'ilike', '%'.$request->q.'%');
        }        

        $products = $products->limit(10)->get(); 

        $products->map(function ($product) { 
            
            $quantity = 0; 
            if($product->store_products->count()>0)
               $quantity =  $product->store_products[0]->quantity;
            
            $product['text'] = $product->name . '  ('. $quantity .')';

            return $product;
        });
        
        $products = $products->all(); 
        
        $status = $this->successStatus;

        return response()->json(['results' => $products], $status);

    }
        
    
    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function transferStock(Request $request)
    {          

        $validator = Validator::make($request->all(), [
            'store_id' => 'required',
            'product_id' => 'required',
            'quantity' => 'required|numeric',
        ]);
             
        $store_id = Auth::user()->store_id;
        $store_product = Store_products::where('product_id',$request->product_id)->where('store_id',$store_id)->first();
        
        $rules['quantity'] = 'required|numeric|max:'.$store_product->quantity;
        $validator = Validator::make($request->all(), $rules);           
                           
        if ($validator->fails()) {
            $response['error'] = $validator->errors();
            return response()->json(['result'=>$response], $this->badRequestStatus);                 
        }
        
        
        $requestData = $request->all();   
        
        $outStock['product_id'] = $request->product_id;
        $outStock['store_id'] = $store_id;
        $outStock['user_id'] = Auth::id();
        $outStock['quantity'] = $request->quantity;
        $outStock['stock_type'] = 2;              
        $outStock['origin'] = 5;              
        $outStock['note'] = 'Stock Transfer';              
        
        $stockOut = Stock::create($outStock);
        
        if($stockOut){
            updateProductStock($stockOut->product_id, $stockOut->store_id);
            updateSyncData('product', $stockOut->product_id);
        }
        
        $inStock['product_id'] = $request->product_id;
        $inStock['store_id'] = $request->store_id;
        $inStock['user_id'] = Auth::id();
        $inStock['quantity'] = $request->quantity;
        $inStock['stock_type'] = 1;              
        $inStock['origin'] = 5;              
        $inStock['note'] = 'Stock Transfer';              
        
        $stockIn = Stock::create($inStock);
        
        if($stockIn){
            updateProductStock($stockIn->product_id, $stockIn->store_id);
            updateSyncData('product', $stockIn->product_id);
        }

            
        $response['success'] =  'Stock successfully transfer!';

        return response()->json(['result'=>$response], $this->successStatus);
    }
    
}
