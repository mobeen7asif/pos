<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Store;
use App\Company;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class StoreController extends Controller
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
        return view('admin.stores.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getStores()
    {        
        $stores = Store::with(['company'])->get();                
        
        return Datatables::of($stores)
            ->addColumn('company_name', function ($store) {
                return $store->company->name;
            })
            ->addColumn('image', function ($store) {
                return '<img width="30" src="'.checkImage('stores/thumbs/'. $store->image).'" />';
            })
            ->addColumn('action', function ($store) {
                return '<a href="stores/'. Hashids::encode($store->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Store"><i class="fa fa-edit"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Store" id="'.Hashids::encode($store->id).'"><i class="fa fa-trash"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['image','company_name', 'action'])
            ->make(true);
            
    }    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        $companies = Company::pluck('name','id')->prepend('Select Company', ''); 
        return view('admin.stores.create', compact('companies'));                
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
            'company_id' => 'required',            
            'name' => 'required|max:255',            
            'address' => 'required',       
            'image' => 'required|mimes:jpeg,jpg,png',
        ]);   
        
       $requestData = $request->all();         
        
        $store = Store::create($requestData);
        
        //save logo image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/stores'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $store->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //update image record
            $store_image['image'] = $fileName;
            $store->update($store_image);
        }
        
        Session::flash('success', 'Store added!');
        
        return redirect('admin/stores');  
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
        
        $store = Store::findOrFail($id);       
        $companies = Company::pluck('name','id')->prepend('Select Company', ''); 

        return view('admin.stores.edit', compact('store','companies'));
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
            'company_id' => 'required',   
            'name' => 'required|max:255',            
            'address' => 'required',                                              
        ]);
        
        $requestData = $request->all();                   
        
        $store = Store::findOrFail($id);
        $store->update($requestData);        
        
        //save store image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/stores'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $company->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //remove old image
            File::delete($destinationPath . $company->image);
            
            //update image record
            $store_image['image'] = $fileName;
            $store->update($store_image);
        }                        
        
        Session::flash('success', 'Store updated!');
        
        return redirect('admin/stores');
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
        
        $store = Store::find($id);
        
        if($store){
            $store->delete();
            $response['success'] = 'Store deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Store not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }

}
