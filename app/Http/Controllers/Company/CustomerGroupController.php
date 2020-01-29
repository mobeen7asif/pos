<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Customer_group;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;
use DB;

class CustomerGroupController extends Controller{
    
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('company.customer-groups.index');
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getCustomerGroups()
    {        
        $groups = Customer_group::where('company_id',Auth::id())->get();             
        
        return Datatables::of($groups)
            ->addColumn('action', function ($group) {
                return '<a href="customer-groups/'. Hashids::encode($group->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Group"><i class="fa fa-edit"></i> </a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Group" id="'.Hashids::encode($group->id).'"><i class="fa fa-trash"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
            
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
        
       return view('company.customer-groups.create'); 
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required',
        ]);
        
        $requestData = $request->all();
        $requestData['company_id'] = Auth::id();
                        
        Customer_group::create($requestData);
        
        Session::flash('success', 'Customer group added!');   
        
        return redirect('company/customer-groups');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $id = Hashids::decode($id)[0];
        
        $group = Customer_group::findOrFail($id);
        
        return view('company.customer-groups.edit',compact('group')); 
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        
        $id = Hashids::decode($id)[0];
        
        $this->validate($request, [ 
            'name' => 'required',
        ]);
        
        $requestData = $request->all();                   
        
        $group = Customer_group::findOrFail($id);
        
        $group->update($requestData);
        
        Session::flash('success', 'Company group updated!');   
        
        return redirect('company/customer-groups');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $id = Hashids::decode($id)[0];
        
        $group = Customer_group::find($id);
        
        if($group){
            $group->delete();
            $response['success'] = 'Customer group deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Customer group not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
        
}
