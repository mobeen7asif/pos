<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Supplier;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class SuppliersController extends Controller
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
        return view('company.suppliers.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getSuppliers()
    {        
        $suppliers = Supplier::get();                
        
        return Datatables::of($suppliers)
            ->addColumn('image', function ($supplier) {
                return '<img width="30" src="'.checkImage('suppliers/thumbs/'. $supplier->image).'" />';
            })
            ->addColumn('action', function ($supplier) {
                return '<a href="suppliers/'. Hashids::encode($supplier->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Supplier"><i class="fa fa-edit action-padding"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Supplier" id="'.Hashids::encode($supplier->id).'"><i class="fa fa-trash action-padding"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['image', 'action'])
            ->make(true);
            
    }    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        return view('company.suppliers.create');                
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
            'code' => 'required|numeric',            
            'name' => 'required|max:255',                           
            'image' => 'required|mimes:jpeg,jpg,png',
            'email' => 'email'
        ]);   
        
       $requestData = $request->all();         
       $requestData['company_id'] = Auth::id();         
        
        $supplier = Supplier::create($requestData);
        
        //save image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/suppliers'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $supplier->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //update image record
            $supplier_image['image'] = $fileName;
            $supplier->update($supplier_image);
        }
        
        Session::flash('success', 'Supplier added!');        

        return redirect('company/suppliers');  
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
        
        $supplier = Supplier::findOrFail($id);       

        return view('company.suppliers.edit', compact('supplier'));
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
            'code' => 'required|numeric',            
            'name' => 'required|max:255',
            'email' => 'email|unique:suppliers,email,'.$id
        ]);
        
        $requestData = $request->all();                   
        
        $supplier = Supplier::findOrFail($id);
        $supplier->update($requestData);        
        
        //save image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/suppliers'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $supplier->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //remove old image
            File::delete($destinationPath . $supplier->image);
            
            //update image record
            $supplier_image['image'] = $fileName;
            $supplier->update($supplier_image);
        }                        
        
        Session::flash('success', 'Supplier updated!');

        return redirect('company/suppliers');
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
        
        $supplier = Supplier::find($id);
        
        if($supplier){
            $supplier->delete();
            $response['success'] = 'Supplier deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Supplier not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }

}
