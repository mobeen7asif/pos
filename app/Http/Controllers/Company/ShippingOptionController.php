<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Shipping_option;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class ShippingOptionController extends Controller
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
        return view('company.shipping-options.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getShippingOptions()
    {        
        $shipping_options = Shipping_option::where('company_id',Auth::id())->get();                
        
        return Datatables::of($shipping_options)
            ->addColumn('action', function ($shipping_option) {
                return '<a href="shipping-options/'. Hashids::encode($shipping_option->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Shipping Option"><i class="fa fa-edit"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Shipping Option" id="'.Hashids::encode($shipping_option->id).'"><i class="fa fa-trash"></i></a>';
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
        return view('company.shipping-options.create');                
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
            'name' => 'required|max:100',  
            'cost' => 'required|numeric',
        ]);   
        
       $requestData = $request->all();         
       $requestData['company_id'] = Auth::id();         
        
        Shipping_option::create($requestData);
        
        Session::flash('success', 'Shipping option added!');        

        return redirect('company/shipping-options');  
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
        
        $shipping_option = Shipping_option::findOrFail($id);       

        return view('company.shipping-options.edit', compact('shipping_option'));
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
            'name' => 'required|max:100',  
            'cost' => 'required|numeric',
        ]);
        
        $requestData = $request->all();                   
        
        $shipping_option = Shipping_option::findOrFail($id);
        $shipping_option->update($requestData);                               
        
        Session::flash('success', 'Shipping option updated!');

        return redirect('company/shipping-options'); 
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
        
        $shipping_option = Shipping_option::find($id);
        
        if($shipping_option){
            $shipping_option->delete();
            $response['success'] = 'Shipping option deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Shipping option not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    
    /**
     * Display a listing of the resource.
     *
     * @return json
     */
    public function getShippingOptionsApi()
    {   
        if(\Request::wantsJson()) 
        {
                    
            $shipping_options = Shipping_option::where('company_id', getComapnyIdByUser())->get();                
            
            $shipping_options->map( function ($shipping_option) {
                
                $shipping_option->default = ($shipping_option->id == companySettingValueApi('shipping_id') ? true : false);
                
                return $shipping_option;
            });
            
            $response['shipping_options'] = $shipping_options;
            $status = $this->successStatus;
            
            return response()->json(['result' => $response], $status);
        }
    }

}
