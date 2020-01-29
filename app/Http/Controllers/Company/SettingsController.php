<?php

namespace App\Http\Controllers\Company;

use App\DutySetting;
use App\Http\Requests;
use App\Http\Controllers\Controller;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Facades\Image;
use Session, Alert, DB;
use App\Admin;
use App\Company_setting;

class SettingsController extends Controller
{
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $setting = Company_setting::where('company_id',Auth::id())->first();
        
        return view('company.settings.settings', compact('setting'));
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
            'currency_id' => 'required',
            'email' => 'required|email',
            'store_id' => 'required',
            //'tax_id' => 'required',
            'shipping_id' => 'required',
            'discount_status' => 'required'
        ]);
        
        $requestData = $request->all();                   
        
        $company = Company_setting::where('company_id',$id)->first();
        if($company){
            $company->update($requestData);  
        }else{
            $requestData['company_id'] = $id;
            Company_setting::create($requestData);
        }
              
        Session::flash('success', 'Settings updated!');

        return redirect('company/settings');
    }

    public function dutyView()
    {
        $setting = DutySetting::where('company_id',Auth::id())->first();

        return view('company.settings.duty_settings', compact('setting'));
    }


    public function dutyUpdate(Request $request)
    {
        $id = Auth::id();
        $this->validate($request, [
            'store_id' => 'required',
            'ip' => 'required',
        ]);

        $requestData = $request->all();

        $company = DutySetting::where('company_id',$id)->first();
        if($company){
            //save logo
            if($request->hasFile('logo')){
                $destinationPath = 'uploads/duty_logos'; // upload path
                $image = $request->file('logo'); // file
                $extension = $image->getClientOriginalExtension(); // getting image extension
                $fileName = $company->id.'-'.str_random(10).'.'.$extension; // renameing image

                //make directory
                if (!file_exists(public_path('uploads/duty_logos/thumbs'))) {
                    mkdir(public_path('uploads/duty_logos/thumbs'), 0777, true);
                }

                $img = Image::make($image->getRealPath());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/thumbs/'.$fileName);

                $image->move($destinationPath, $fileName); // uploading file to given path

                //update image record
                $requestData['logo'] = $fileName;
            } else {
                $requestData['logo'] = 'no_picture.jpg';
            }
            $company->update($requestData);
        }else{
            //update logo
            $requestData['company_id'] = $id;
            $company = DutySetting::create($requestData);
            if($request->hasFile('logo')){
                $destinationPath = 'uploads/duty_logos'; // upload path
                $image = $request->file('logo'); // file
                $extension = $image->getClientOriginalExtension(); // getting image extension
                $fileName = $company->id.'-'.str_random(10).'.'.$extension; // renameing image
                //make directory
                if (!file_exists(public_path('uploads/duty_logos/thumbs'))) {
                    mkdir(public_path('uploads/duty_logos/thumbs'), 0777, true);
                }

                $img = Image::make($image->getRealPath());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/thumbs/'.$fileName);

                $image->move($destinationPath, $fileName); // uploading file to given path

                //update image record
                $requestData['logo'] = $fileName;
            } else {
                $requestData['logo'] = 'no_picture.jpg';
            }
            DutySetting::where(['company_id' => Auth::id()])->update(['logo' => $requestData['logo']]);
        }

        Session::flash('success', 'Settings updated!');

        return redirect('company/duty/settings');
    }




}
