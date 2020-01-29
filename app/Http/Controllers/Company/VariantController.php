<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Variant;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class VariantController extends Controller
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
        return view('company.variants.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getVariants()
    {        
        $variants = Variant::with(['company'])->company(Auth::id())->get();                
        
        return Datatables::of($variants)
            ->addColumn('action', function ($variant) {
                return '<a href="variants/'. Hashids::encode($variant->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Attribute"><i class="fa fa-edit"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Attribute" id="'.Hashids::encode($variant->id).'"><i class="fa fa-trash"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['action'])
            ->make(true);
            
    }    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        return view('company.variants.create');                
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
            'name' => 'required|max:255',                           
        ]);   
        
       $requestData = $request->all();         
       $requestData['company_id'] = Auth::id();         
        
        Variant::create($requestData);
        
        Session::flash('success', 'Attribute added!');

        return redirect('company/variants');  
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
        
        $variant = Variant::findOrFail($id);       

        return view('company.variants.edit', compact('variant'));
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
            'name' => 'required|max:255',                           
        ]);
        
        $requestData = $request->all();                   
        
        $variant = Variant::findOrFail($id);
        $variant->update($requestData);                               
        
        Session::flash('success', 'Attribute updated!');

        return redirect('company/variants');
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
        
        $variant = Variant::find($id);
        
        if($variant){
            $variant->delete();
            $response['success'] = 'Attribute deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Attribute not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }

}
