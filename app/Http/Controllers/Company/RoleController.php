<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;
use DB;

class RoleController extends Controller{
    
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        
        return view('company.roles.index');
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getRoles()
    {        
        $roles = Role::get();                
        
        return Datatables::of($roles)
            ->addColumn('action', function ($role) {
                return '<a href="roles/permissions/'. Hashids::encode($role->id).'" class="text-primary" data-toggle="tooltip" title="Change Permission"><i class="fa fa-key"></i> </a>
                        <a href="roles/'. Hashids::encode($role->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Role"><i class="fa fa-edit"></i> </a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Role" id="'.Hashids::encode($role->id).'"><i class="fa fa-trash"></i></a>';
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
        
       return view('company.roles.create'); 
       
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
        $requestData['guard_name'] = 'company';
                        
        Role::create($requestData);
        
        Session::flash('success', 'Role added!');   
        
        return redirect('company/roles');
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
        
        $role = Role::findOrFail($id);
        
        return view('company.roles.edit',compact('role')); 
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
        
        $role = Role::findOrFail($id);
        
        $role->update($requestData);
        
        Session::flash('success', 'Role updated!');   
        
        return redirect('company/roles');
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
        
        $role = Role::find($id);
        
        if($role){
            $role->delete();
            $response['success'] = 'Role deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Role not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    
    
    /**
     * Get all permissions.
     * 
     * @param  int  $role_id   
     *  
     * @return \Illuminate\Http\Response
     */
     public function getRolePermissions($role_id){        
         
         $id = Hashids::decode($role_id)[0];
         
        $role = Role::find($id);
        
        $permissions = Permission::all();
        
        return view('company.roles.permissions',compact('role','permissions')); 
    }
    
    
    public function updateRolePermission($role_id, Request $request){
        
        $id = Hashids::decode($role_id)[0];
        
        $permissions = $request->permissions;
        
        $role = Role::findById($id);
        
        DB::table('role_has_permissions')->where('role_id', $id)->delete();
        
        $role->syncPermissions($permissions);
        
        Session::flash('success', 'Permission updated!');   
        
       return redirect('company/roles');
        
    }
}
