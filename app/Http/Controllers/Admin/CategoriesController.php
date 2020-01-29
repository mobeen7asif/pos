<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Categories;
use App\Category_sections;
use App\Section_questions;
use App\Question_answers;
use App\Items;
use App\Regions;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

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
        if(\Request::wantsJson()) 
        {
            $categories = Categories::where('parent_id',0)->get();               
          
            $categories->map(function ($category) {
                $item_ids = Categories::select('id')->where('parent_id' ,$category->id)->pluck('id');
                $league_items = Categories::with('item_images')->where('parent_id',$category->id);
                $league_records = Items::with(['user.country','record_images'])->whereIn('category_id',$item_ids);
                
                /*$category['items'] = $league_items->get();
                $category['records'] = $league_records->get();*/
                $category['items_count'] = str_pad($league_items->count(),7,0,STR_PAD_LEFT);
                $category['records_count'] = str_pad($league_records->count(),7,0,STR_PAD_LEFT);
                $category['collectors_count'] = str_pad(Items::whereIn('category_id',$item_ids)->groupBy('user_id')->count(),7,0,STR_PAD_LEFT);

                $category['category_thumbnail'] = '';
                if($category->category_image != ""){
                    $category['category_thumbnail'] = checkImage('categories/thumbs/'. $category->category_image);
                    $category['category_image'] = checkImage('categories/'. $category->category_image);  
                }  
                return $category;
            });          
           
            $response['categories'] = $categories->all();
                  
            return response()->json(['result' => $response], $this->successStatus);
        }
        

        return view('admin.categories.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function get_categories()
    {        
        $categories = Categories::where('parent_id',0)->get();                
        
        return Datatables::of($categories)
            ->addColumn('category_image', function ($category) {
                return '<img width="50" src="'.checkImage('categories/thumbs/'. $category->category_image).'" />';
            })
            ->addColumn('items_count', function ($category) {
                return '<a href="'. url('admin/items/'.Hashids::encode($category->id)) .'" >'.Categories::where('parent_id',$category->id)->count().'</a>';
            })
            ->addColumn('records_count', function ($category) {
                return '<a href="'. url('admin/records/'.Hashids::encode($category->id)) .'" >'.Items::whereIn('category_id',Categories::select('id')->where('parent_id' ,$category->id)->pluck('id'))->count().'</a>';
            })
            ->addColumn('action', function ($category) {
                return '<a href="categories/'. Hashids::encode($category['id']).'/edit" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a> 
                <a href="javascript:void(0)" class="btn btn-xs btn-danger btn-delete" id="'.Hashids::encode($category->id).'"><i class="glyphicon glyphicon-remove"></i> Delete</a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['category_image', 'items_count', 'records_count', 'action'])
            ->make(true);
            
    }    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        return view('admin.categories.create');                
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
            'category_name' => 'required',
            'description' => 'required'                                         
        ]);
        
        $requestData = $request->all();              
        $requestData['parent_id'] = 0;              
        
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
        }
        
        Alert::success('Success Message', 'League added!');        

        return redirect('admin/categories');  
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
                
        if(\Request::wantsJson()) 
        {  
            $category = Categories::findOrFail($id);
            
            $category['items_count'] = Categories::where('parent_id',$category->id)->count();
            
            $category['category_thumbnail'] = '';
            if($category->category_image != ""){
                $category['category_thumbnail'] = checkImage('categories/thumbs/'. $category->category_image);
                $category['category_image'] = checkImage('categories/'. $category->category_image);
            }                                    
             
            $response['category'] = $category;
            return response()->json(['result' => $response], $this->successStatus);
        }
        
        $id = Hashids::decode($id)[0];
        
        $category = Categories::findOrFail($id);       
        
        return view('admin.categories.edit', compact('category'));
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
            'category_name' => 'required', 
            'description' => 'required'                                       
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
        
        Alert::success('Success Message', 'League updated!');

        return redirect('admin/categories');
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
        
        $categories = Categories::find($id);
        
        if($categories){
            $categories->delete();
            $response['success'] = 'League deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'League not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
        
    /**
     * get Leagues And Regions.
     *
     * @return \Illuminate\View\View
     */
    public function getLeaguesAndRegions()
    {
        if(\Request::wantsJson()) 
        {
            $categories = Categories::where('parent_id',0)->get();               
            $regions = Regions::get();               
          
            $categories->map(function ($category) {
                $item_ids = Categories::select('id')->where('parent_id' ,$category->id)->pluck('id');
                $league_items = Categories::with('item_images')->where('parent_id',$category->id);
                $league_records = Items::with(['user.country','record_images'])->whereIn('category_id',$item_ids);
                
                /*$category['items'] = $league_items->get();
                $category['records'] = $league_records->get();*/
                $category['items_count'] = str_pad($league_items->count(),7,0,STR_PAD_LEFT);
                $category['records_count'] = str_pad($league_records->count(),7,0,STR_PAD_LEFT);
                $category['collectors_count'] = str_pad(Items::whereIn('category_id',$item_ids)->groupBy('user_id')->count(),7,0,STR_PAD_LEFT);

                $category['category_thumbnail'] = '';
                if($category->category_image != ""){
                    $category['category_thumbnail'] = checkImage('categories/thumbs/'. $category->category_image);
                    $category['category_image'] = checkImage('categories/'. $category->category_image);  
                }  
                return $category;
            });          
           
            $response['leagues'] = $categories->all();
            $response['regions'] = $regions->all();
                  
            return response()->json(['result' => $response], $this->successStatus);
        }

    }
}
