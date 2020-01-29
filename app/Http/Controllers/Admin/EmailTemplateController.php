<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Email_template;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class EmailTemplateController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index($id)
    {          
        $id = Hashids::decode($id)[0];
        
        $email_template = Email_template::find($id);
        
        return view('admin.email-templates.form', compact('email_template'));
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
        $template_id = Hashids::decode($id)[0];
        
        $this->validate($request, [
            'name' => 'required',   
            'subject' => 'required',            
            'from' => 'required',                                              
            'content' => 'required',                                              
        ]);                           
        
        $email_template = Email_template::findOrFail($template_id);
        $email_template->update($request->all());                              
        
        Session::flash('success', 'Template updated!');  
        
        return redirect('admin/email-templates/'.$id);
    }



}
