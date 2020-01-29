<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\MealType;
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

class MealController extends Controller{
    
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('company.meals.index');
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getMeals()
    {
        $meals = MealType::with('store')->where('company_id',Auth::id())->get();


        
        return Datatables::of($meals)
            ->addColumn('store_name', function ($meal) {
                return $meal->store->name;
            })
            ->addColumn('action', function ($meal) {
                return '
                <a href="meals/'. Hashids::encode($meal->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Meal Type"><i class="fa fa-edit action-padding"></i> </a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Meal" id="'.Hashids::encode($meal->id).'"><i class="fa fa-trash action-padding"></i></a>';
            })
            ->rawColumns(['action','store_name'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
            
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
       return view('company.meals.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){

        $max = MealType::where('store_id',$request->input('store_id'))->count();

        $this->validate($request,[
            'meal_type' => 'required',
            'store_id' => 'required',
            'color' => 'required',
            'sort_id' => 'required|numeric|max:'.$max.'|min:1'
        ],['sort_id.max' => 'Sort number must not be greater than total meal types',
            'sort_id.min' => 'Sort number must be start from 1'
            ]);
        
        $requestData = $request->all();
        $requestData['company_id'] = Auth::id();
        MealType::create($requestData);
        
        Session::flash('success', 'Meal added!');
        
        return redirect('company/meal_types');
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
        
        $meal = MealType::findOrFail($id);
        
        return view('company.meals.edit',compact('meal'));
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
        $max = MealType::where('store_id',$request->input('store_id'))->count();
        $this->validate($request, [
            'store_id' => 'required',
            'meal_type' => 'required',
            'color' => 'required',
            'sort_id' => 'required|numeric|max:'.$max.'|min:1'
        ],['sort_id.max' => 'Sort number must not be greater than total meal types',
            'sort_id.min' => 'Sort number must be start from 1'
        ]);
        
        $requestData = $request->all();                   
        
        $meal = MealType::findOrFail($id);
        
        $meal->update($requestData);
        
        Session::flash('success', 'Meal updated!');
        
        return redirect('company/meal_types');
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
        
        $meal = MealType::find($id);
        
        if($meal){
            $meal->delete();
            $response['success'] = 'Meal deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Meal not exist against this id!';
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
