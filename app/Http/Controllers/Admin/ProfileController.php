<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session, Alert, DB;
use App\Admin;
use Session;

class ProfileController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $profile = Admin::find(Auth::id());
        return view('admin.profile.index', compact('profile'));
    }    

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $requestData = $request->all();
        unset($requestData['_token']);
        
         
        Admin::where('id',Auth::id())->update($requestData); 
        
        if($request->hasFile('profile_image')){            
            $destinationPath = 'uploads/admin_avatar'; // upload path
            $image = $request->file('profile_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = 'profile_image.'.$extension; // renameing image

            $image->move($destinationPath, $fileName); // uploading file to given path

            //insert image record            
            DB::table('admins')->where('id',Auth::id())->update(['profile_image' => $fileName]); 
        }

        Session::flash('success', 'Profile updated!');  
        
        return redirect('admin/profile');
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function changePasswordView()
    {
        $data = Admin::find(Auth::id());
        return view('admin.profile.change-password', compact('data'));
    }  
    
    /**
     * Change password.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required', 
            'password' => 'required|confirmed'            
        ]);
        
        $requestData = $request->all();
        $validator->after(function($validator) use ($request) {
            $user = Auth::guard('admin')->user();

            //checking the old password first
            $check  = Auth::guard('admin')->attempt([
                'email' => $user->email,
                'password' => $request->current_password
            ]);

         if(!$check) {           
                $validator->errors()->add('current_password','Your current password is incorrect, please try again.');            
            }
        });
        
        if ($validator->fails()){
            return redirect('admin/change-password')->withErrors($validator);
        }
        
        $user = Auth::guard('admin')->user();
        
        $user->password = bcrypt($request->password);
        $user->save();
        
        Session::flash('success', 'Password updated!');  
        
        return redirect('admin/change-password');
    }
}
