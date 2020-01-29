<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Product;
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

class CategoriesController extends Controller
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

        return view('company.categories.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getCategories()
    {        
        $store_ids = Store::where('company_id',Auth::id())->pluck('id');
        
        $all_categories = Categories::with(['store'])->whereIn('store_id',$store_ids)->get();

        $categories = [];
        $this->getCategoriesRecursive($all_categories, $categories);              
        
        return Datatables::of($categories)
            ->addColumn('category_image', function ($category) {
                return '<img width="50" src="'.checkImage('categories/thumbs/'. $category['category_image']).'" />';
            })
            ->addColumn('store_id', function ($category) {
                return @$category['store_name'];
            })
            ->addColumn('products_count', function ($category) {
                return Category_products::where('category_id',$category['id'])->count();
            })
            ->addColumn('action', function ($category) {
                return '<a href="categories/'. Hashids::encode($category['id']).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Category"><i class="fa fa-edit action-padding"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Category" id="'.Hashids::encode($category['id']).'"><i class="fa fa-trash action-padding"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['category_image', 'products_count', 'action'])
            ->make(true);                          
            
    }    
    
    // utility method to build the categories tree
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
                  "category_name" => str_repeat('-', $depth) .' '. $cat->category_name, 
                  "store_name" => @$cat->store->name,
                  "created_at" => $cat->created_at, 
                );

            $this->getCategoriesRecursive($all_categories, $categories, $cat->id, $depth + 1);
        }
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        $store_ids = Store::where('company_id',Auth::id())->pluck('id');
        
        $categories = ['0' => 'Root Category'];       
        
        return view('company.categories.create', compact('categories'));                
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
            'category_name' => 'required',
            'category_image' => 'mimes:jpeg,jpg,png',
        ]);
        
        $requestData = $request->all();              
        
        $category = Categories::create($requestData);
        
        //save category image
        if($request->hasFile('category_image')){
            $destinationPath = 'uploads/categories'; // upload path
            $image = $request->file('category_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $category->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //update image record
            $category_image['category_image'] = $fileName;
            $category->update($category_image);
        } else {
            $category_image['category_image'] = 'no_picture.jpg';
            $category->update($category_image);
        }
        
        // sync products
        updateSyncData('category',$category->id);
        
        Session::flash('success', 'Category added!');        

        return redirect('company/categories');  
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
        
        $category = Categories::with(['category'])->find($id); 
        
        if($category->category){
            $categories = ['0' => 'Root Category', $category->category->id => $category->category->category_name];
        }else{
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
    public function update($id, Request $request)
    {
        $id = Hashids::decode($id)[0];
        
        $this->validate($request, [ 
//            'store_id' => 'required',
            'category_name' => 'required', 
        ]);
        
        $requestData = $request->all();                   
        
        $category = Categories::findOrFail($id);
        $category->update($requestData);        
        
        //save category image
        if($request->hasFile('category_image')){
            $destinationPath = 'uploads/categories'; // upload path
            $image = $request->file('category_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $category->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //remove old image
            File::delete($destinationPath . $category->category_image);
            
            //update image record
            $category_image['category_image'] = $fileName;
            $category->update($category_image);
        }

        
        // sync products
        updateSyncData('category',$category->id);
        
        Session::flash('success', 'Category updated!');

        return redirect('company/categories');
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
        
        $category = Categories::find($id);
        
        if($category){
            // sync products
            updateSyncData('delete_category',$category->id,[$category->store_id]);

            $category->delete();
            $response['success'] = 'Categories deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Categories not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getCategoriesApi()
    {   
        if(\Request::wantsJson()) 
        { 
            $store_id = Auth::user()->store_id;

            $categories = Categories::with(['subcategories'])->where('store_id',$store_id)->where('parent_id',0)->get();
            
            $response['categories'] = $categories;

            return response()->json(['result' => $response], $this->successStatus);
        }       
                          
            
    }
    
    /**
     * getStoreCategories function
     *      
     * @param  int  $request
     * 
     * @return \Illuminate\Http\Response
     */
    public function getStoreCategories($store_id, Request $request)
    {      
        $categories = Categories::select(\DB::raw("id, category_name as text"))->where('parent_id',0)->where('store_id',$store_id)->get()->prepend([ 'id' => 0, 'text' => 'Root Category']);                                                 
        
        $categories = $categories->all(); 
        
        $status = $this->successStatus;

        return response()->json(['results' => $categories], $status);

    }

    public function getProducts(Request $request){
        $cat_ids = $request->input('id_string');
        if(isset($cat_ids)){
            $cat_ids = explode(',',$cat_ids);
            $product_ids = Category_products::whereIn('category_id',$cat_ids)->pluck('product_id');
            $products = Product::whereIn('id',$product_ids)->get();
            return response()->json($products);
        } else {
            $products = [];
            $products = collect($products);
            return response()->json($products);
        }

    }


}
