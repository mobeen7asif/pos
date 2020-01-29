<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\User;
use App\Attendance_status;
use App\Holidays;
use App\Timeline;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
//use datatables
use Datatables;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        //$countries = Country::all();
        return view('admin.users.index');
    }

    /**
     * get users
     *
     * 
     */
    public function get_users(){
        $users = User::get();
        return Datatables::of($users)
            ->addColumn('profile_image', function ($user_image) {
                return "<img style='width:50px;' src=".checkImage('users/thumbs/'. $user_image->profile_image).">";
                /*if($user_image->profile_image !='')
                    return "<img style='max-height:100px; max-width:100px;' src='../uploads/users/".$user_image->profile_image."' /> ";
                else
                    return "<img style='max-height:100px; max-width:100px;' src='../uploads/users/default.png' /> ";*/
            })
            ->addColumn('action', function ($user) {
                return '<a href="users/'.Hashids::encode($user->id).'/edit" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a> 
                        <a href="users/'.Hashids::encode($user->id).'" class="btn btn-xs btn-success"><i class="glyphicon glyphicon-user"></i> Profile</a>
                        <a href="user-timeline/'.Hashids::encode($user->id).'" class="btn btn-xs btn-info"><i class="fa fa-sitemap"></i> Timeline</a>
                        <a href="user-attendance/'.Hashids::encode($user->id).'" class="btn btn-xs btn-success"><i class="fa fa-calendar"></i> Attendance</a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['profile_image', 'action'])
            ->make(true);
            
            
//                        <a href="javascript:void(0)" class="btn btn-xs btn-danger btn-delete" id="'.Hashids::encode($user->id).'"><i class="glyphicon glyphicon-remove"></i> Delete</a>
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return redirect('admin/users');
    }

    /**
     * Show the detail of user.
     *
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        $id = Hashids::decode($id)[0];
        $user = User::with(['country', 'items'])->findOrFail($id);

        return view('admin.users.user_profile', compact('user'));
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
        $user = User::findOrFail($id);

        return view('admin.users.edit', compact('user', 'countries'));
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
            'name'          => 'required',
            'email'         => "required|email|unique:users,email,$id" ,
            'dob'    => 'required',
            'pin'     => 'required|digits:6',
            'blood_group'     => 'required',
            'profile_status'     => 'required',
              
        ]);
        
        $user = User::findOrFail($id);

        $input = $request->all();
        
        $user->update($input);
              
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
        
        Alert::success('Success Message', 'User updated!');

        return redirect('admin/users');
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

        User::destroy($id);

        Alert::success('Success Message', 'User deleted!');

        return 'true';
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
}
