<?php

namespace App\Http\Controllers\Company;

use App\Company;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Helpers\LogActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;


use App\User;
use App\Role;
use App\Store;
use App\Attendance_status;
use Illuminate\Support\Collection;

class UsersController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    public $notFoundStatus = 404;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        
        Collection::macro('permute', function () {
            if ($this->isEmpty()) {
                return new static([[]]);
            }

            return $this->flatMap(function ($value, $index) {
                return (new static($this))
                    ->forget($index)
                    ->values()
                    ->permute()
                    ->map(function ($item) use ($value) {
                        return (new static($item))->prepend($value);
                    });
            });
        });
//        $store_ids = Store::where('company_id',Auth::id())->pluck('id');
//        $users = User::with(['store'])->whereIn('store_id',$store_ids)->get();
//        return $users;
        
        //$permute = collect([1 => ['Black', 'Red', 'White'], 2 => ['Large', 'Mediam', 'Small']])->permute();
        //dd($permute->toArray());
        return view('company.users.index');
    }

    /**
     * get users
     *
     * 
     */
    public function getUsers(){
        
        $store_ids = Store::where('company_id',Auth::id())->pluck('id');
        
        $users = User::with(['store'])->whereIn('store_id',$store_ids)->get();
        
        return Datatables::of($users)
            ->addColumn('profile_image', function ($user_image) {
                return "<img style='width:30px;' src=".checkImage('users/thumbs/'. $user_image->profile_image).">";               
            })
            ->addColumn('name', function ($user) {
                $name = '<p>'.$user->name.'</p> <span style="font-weight: bold">'.$user->role_name.'</span>';
                return $name;
            })
            ->addColumn('store_name', function ($user) {
                return @$user->store->name;               
            })
            ->addColumn('created_at', function ($user) {
                return date('d M, Y',strtotime($user->created_at));               
            })
            ->addColumn('status', function ($user) {
                if($user->status == 1){
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-success" data-toggle="tooltip" title="Active"><i class="fa fa-check"></i> Active</a>';
                }else{
                    return '<a href="javascript:void(0)" class="btn btn-xs btn-danger" data-toggle="tooltip" title="Inactive"><i class="fa fa-times"></i> Inactive</a>';
                }               
            })
            ->addColumn('action', function ($user) {
                if($user->role_name == 'Store Admin'){
                    return '<a href="users/'.Hashids::encode($user->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Employee"><i class="fa fa-edit action-padding"></i></a> 
                        <a href="users/get-logs/'.Hashids::encode($user->id).'" class="text-success" data-toggle="tooltip" title="Employee Logs"><i class="fa fa-history action-padding"></i></a>
                        <a href="reports/shift-report?employee_id='.Hashids::encode($user->id).'" class="text-primary" data-toggle="tooltip"  title="Employee Shifts" ><i class="fa fa-bar-chart"></i></a>
                          ';
                } else {
                    return '<a href="users/'.Hashids::encode($user->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Employee"><i class="fa fa-edit action-padding"></i></a> 
                        <a href="users/get-logs/'.Hashids::encode($user->id).'" class="text-success" data-toggle="tooltip" title="Employee Logs"><i class="fa fa-history action-padding"></i></a>
                        <a href="reports/shift-report?employee_id='.Hashids::encode($user->id).'" class="text-primary" data-toggle="tooltip"  title="Employee Shifts" ><i class="fa fa-bar-chart"></i></a>
                        <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Employee" id="'.Hashids::encode($user->id).'"><i class="fa fa-trash"></i></a>
                          ';
                }

            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['profile_image', 'store_name', 'status','action','name'])
            ->make(true);
 
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $roles = Role::where('company_id',Auth::id())->pluck('name','id');
        
        return view('company.users.create', compact('roles'));
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
            'store_id' => 'required',
            //'email' => 'required|email|max:255|unique:users',
            //'password' => 'required|min:6|confirmed',
            'gender' => 'required',
            'pin_code' => 'required',
            'role' => 'required',
            'status' => 'required',
        ]);
        $this->validate($request, [
            'email' => ['required' , function($attribute, $value, $fail) {
                $store_ids = Store::where('company_id',Auth::id())->pluck('id');
                $user = User::where('email' , $value)->whereIn('store_id',$store_ids)->first();
                if($user){
                    $fail($attribute.' is already taken.');
                }
            },
            ],
        ]);
        $this->validate($request, [
            'pin_code' => ['required' , function($attribute, $value, $fail) {
                $company = Company::where('id',Auth::id())->first();
                $store_ids = Store::where('company_id',$company->id)->pluck('id');
                $check = User::whereIn('store_id',$store_ids)->where('pin_code',$value)->first();
                if($check){
                    $fail($attribute.' already exists.');
                }
            },
            ],
        ]);
        $requestData = $request->all();

        //$requestData['password'] = bcrypt($requestData['password']);
        $temp_role = DB::table('roles')->where('id',$requestData['role'])->first();
        $requestData['role_name'] = $temp_role->name;
        if($requestData['password']){
            $requestData['password'] = bcrypt($requestData['password']);
        }else{
            unset($requestData['password']);
        }
        $user = User::create($requestData);

        if($user){
            $role['role_id'] = $requestData['role'];
            $role['model_id'] = $user->id;
            $role['model_type'] = 'App\User';

            DB::table('model_has_roles')->insert($role);
        }

        //save profile image
        if($request->hasFile('profile_image')){
            $destinationPath = 'uploads/users'; // upload path
            $image = $request->file('profile_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $user->id.'-'.str_random(10).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //update image record
            $user_image['profile_image'] = $fileName;
            $user->update($user_image);
        }

        Session::flash('success', 'Employee added!');

        return redirect('company/users');
    }
    
    /**
     * Show the detail of user.
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $id = Hashids::decode($id)[0];
        
        $user = User::with(['store.company.company_setting'])->findOrFail($id);
        
        return view('company.users.user_profile', compact('user'));
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
        
        $roles = Role::where('company_id',Auth::id())->pluck('name','id');
        $role = DB::table('model_has_roles')->where('model_id',$id)->first();
        
        $user = User::with(['store.company.company_setting'])->findOrFail($id);
        if($role)
            $user['role'] = $role->role_id;
        
        return view('company.users.edit', compact('user', 'roles'));
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
            'store_id' => 'required',
            //'email' => "required|email|max:255|unique:users,email,$id",
            'gender' => 'required',
            'pin_code' => 'required',
            'role' => 'required',
            'status' => 'required',
        ]);
        $this->validate($request, [
            'pin_code' => ['required' , function($attribute, $value, $fail) use($id) {
                $company = Company::where('id',Auth::id())->first();
                $store_ids = Store::where('company_id',$company->id)->pluck('id');
                $check = User::whereIn('store_id',$store_ids)->where('id','!=',$id)->where('pin_code',$value)->first();
                if($check){
                    $fail($attribute.' already exists.');
                }
            },
            ],
        ]);
        $this->validate($request, [
            'email' => ['required' , function($attribute, $value, $fail) use ($id) {
                $store_ids = Store::where('company_id',Auth::id())->pluck('id');
                $user = User::where('email' , $value)->whereIn('store_id',$store_ids)->first();
                if($user){
                    $exist_user = User::find($id);
                    if($exist_user){
                        if($user->id != $exist_user->id){
                            $fail($attribute.' is already taken.');
                        }
                    }
                }
            },
            ],
        ]);

//        if($request->password){
//            $this->validate($request, [
//                'password' => 'required|min:6|confirmed',
//            ]);
//        }

        $user = User::findOrFail($id);


        $requestData = $request->all();

        if($user){
            DB::table('model_has_roles')->where('model_id',$user->id)->update(['role_id'=>$requestData['role']]);
        }

        if($requestData['password']){
            $requestData['password'] = bcrypt($requestData['password']);
        }else{
            unset($requestData['password']);
        }
        $temp_role = DB::table('roles')->where('id',$requestData['role'])->first();
        $requestData['role_name'] = $temp_role->name;
        $user->update($requestData);

        /*-----image manipulation-----*/
        if ($request->hasFile('profile_image'))
        {
            $image = $request->file('profile_image');
            $image_name = $user->id.'_'.rand(11111,99999).'.'.$image->getClientOriginalExtension();
            $destinationPath = public_path('/uploads/users/thumbs');
            $img = Image::make($image->getRealPath());
            /*save image thumbnamil*/
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/'.$image_name);

            /*save original image*/
            $destinationPath = public_path('/uploads/users');
            $image->move($destinationPath, $image_name);

            /*unlink old image*/
            @unlink(public_path("/uploads/users/$user->profile_image"));
            @unlink(public_path("/uploads/users/thumbs/$user->profile_image"));

            $user->profile_image = $image_name;
            $user->save();
        }

        Session::flash('success', 'Employee updated!');

        return redirect('company/users');
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

        $user = User::find($id);

        if($user){
            $user->delete();
            $response['success'] = 'User deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'User not exist against this id!';
            $status = $this->notFoundStatus;  
        }
        
        return response()->json(['result'=>$response], $status);
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @param  int  $user_id
     * 
     * @return \Illuminate\View\View
     */
    
    public function timeline($user_id){
        $user_id = Hashids::decode($user_id)[0];
        $timelines = Timeline::where('user_id',$user_id)->orderBy('date', 'desc')->get();
        
        //dd($timelines->toArray());
        
        return view('admin.users.timeline', compact('timelines'));
    }
 
    /**
     * Display a listing of the resource.
     *
     * @param  int  $user_id
     * 
     * @return \Illuminate\View\View
     */
    
    public function attendance($user_id){
        
        return view('admin.users.attendance');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param  int  $user_id
     * 
     * @return JSON
     */
    
    public function getAttendance($user_id, Request $request){
        
        $user_id = Hashids::decode($user_id)[0];
        
        $holidays = Holidays::where('date','<=', date("Y-m-d"))->whereMonth('date',$request->month)->whereYear('date',$request->year)->get();
        $attendance = Attendance_status::where('user_id',$user_id)->where('date','<=', date("Y-m-d"))->whereMonth('date',$request->month)->whereYear('date',$request->year)->where('status',1)->get();
        
        $holidays->map(function ($holiday) {           
            $holiday['classname'] = 'holidays';
            $holiday['badge'] = false;
            $holiday['body'] =  $holiday->note;
            
            return $holiday;
         });
        
        $attendance->map(function ($att) {
            switch ($att->type) {
                case 1:
                    $att['title'] = 'Present';     
                    $att['classname'] = 'present';
                    break;
                case 2:
                    $att['title'] = 'Leave';
                    $att['classname'] = 'leave';  
                    break;
                default:
                    $att['title'] = 'Present';     
                    $att['classname'] = 'present';
            }

            $att['badge'] = false;
            $att['body'] =  $att['title'];
            
            return $att;
         });
        
        $holidaysArray = $holidays->toArray();
        $attendanceArray = $attendance->toArray();

        $holidaysCollection = collect( $holidaysArray );

        $merged = $holidaysCollection->merge($attendanceArray);
        
        return response()->json($merged, 200);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param  int  $user_id
     * 
     * @return JSON
     */
    function getUserLogs($user_id){
        
        $user_id = Hashids::decode($user_id)[0];                
        
        return view('company.users.logs', compact('user_id'));
        
    }
    
    /**
     * Display a listing of the resource.
     *
     * @param  int  $user_id
     * 
     * @return JSON
     */
    function getUserAjaxLogs($user_id){       
        
        $logs = LogActivity::getUserLogs($user_id);        
        
        return Datatables::of($logs)
            ->addColumn('created_at', function ($user) {
                return date('d M, Y h:i a',strtotime($user->created_at));               
            })
            ->make(true);
            
        
    }
    
}
