<?php

namespace App\Http\Controllers\Company;
use App\Http\Controllers\Controller;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;



class PermissionController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('company.permissions.index');
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getPermissions()
    {        
        $permissions = Permission::get();                
        
        return Datatables::of($permissions)
            ->addColumn('action', function ($permission) {
                return '<a href="permissions/'. Hashids::encode($permission->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Permission"><i class="fa fa-edit"></i> </a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Permission" id="'.Hashids::encode($permission->id).'"><i class="fa fa-trash"></i></a>';
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
        
       return view('company.permissions.create'); 
       
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required|unique:permissions,name',
        ]);
        
        $requestData = $request->all();
        $requestData['guard_name'] = 'company';
        
        Permission::create($requestData);
        
        Session::flash('success', 'Permission added!');   
        
        return redirect('company/permissions');
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
        
        $permission = Permission::findOrFail($id);
        
        return view('company.permissions.edit',compact('permission')); 
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
            'name' => 'required|unique:permissions,name,'.$id,
        ]);
        
        $permission = Permission::findOrFail($id);
        
        $permission->update($request->all());
        
        Session::flash('success', 'Permission updated!');   
        
        return redirect('company/permissions');
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
        
        $permission = Permission::find($id);
        
        if($permission){
            $permission->delete();
            $response['success'] = 'Permission deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Permission not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
           
    
}
