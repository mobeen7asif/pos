<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Session, Alert, DB, Image, File;
use App\Company;

class ProfileController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $profile = Auth::user();
       
        return view('company.profile.index', compact('profile'));
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
        $id = Auth::id();
        
        $this->validate($request, [ 
            'name' => 'required|max:255',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address' => 'required',                                         
            'phone' => 'numeric',                                         
            'mobile' => 'numeric',                                         
        ]);
        
        $requestData = $request->all();                   
        
        $company = Company::findOrFail($id);
        $company->update($requestData);        
        
        //save company logo
        if($request->hasFile('logo')){
            $destinationPath = 'uploads/companies'; // upload path
            $image = $request->file('logo'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $company->id.'-'.str_random(10).'.'.$extension; // renameing image
            
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path
            
            //remove old image
            File::delete($destinationPath . $company->logo);
            
            //update image record
            $company_image['logo'] = $fileName;
            $company->update($company_image);
        }  

        Session::flash('success', 'Profile updated!');

        return redirect('company/profile');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function changePasswordView()
    {
        $data = Company::find(Auth::id());
        return view('company.profile.change-password', compact('data'));
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
            $user = Auth::guard('company')->user();

            //checking the old password first
            $check  = Auth::guard('company')->attempt([
                'email' => $user->email,
                'password' => $request->current_password
            ]);

         if(!$check) {           
                $validator->errors()->add('current_password','Your current password is incorrect, please try again.');            
            }
        });
        
        if ($validator->fails()){
            return redirect('company/change-password')->withErrors($validator);
        }
        
        $user = Auth::guard('company')->user();
        
        $user->password = bcrypt($request->password);
        $user->save();
        
        Session::flash('success', 'Password updated!');

        return redirect('company/change-password');
    }
}
