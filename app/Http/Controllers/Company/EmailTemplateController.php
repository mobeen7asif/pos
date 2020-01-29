<?php

namespace App\Http\Controllers\Company;

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

    public function index(){

        return view('company.email-templates.index');
    }
    public function getEmailTemplates(){
        $emails = Email_template::where('company_id',Auth::id())->get();
        return Datatables::of($emails)
//            ->addColumn('store_name', function ($floor) {
//                return $floor->store->name;
//            })
            ->addColumn('action', function ($email) {
                return '
                <a href="email_templates/'. Hashids::encode($email->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Role"><i class="fa fa-edit"></i> </a>';
            })
            //->rawColumns(['action','store_name'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
    }
    public function editEmailTemplate($id)
    {
        $id = Hashids::decode($id)[0];
        $email_template = Email_template::where('id', $id)->first();
//        if (!$email_template) {
//            $email_template = Email_template::where('template_key', 'sale')->where('company_id', 0)->first();
//        }
        return view('company.email-templates.form', compact('email_template'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int $id
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'subject' => 'required',
            'from' => 'required',
            'content' => 'required',
        ]);

        $email_template = Email_template::find($request->input('template_id'));
        if ($email_template) {
            $email_template->name = $request->name;
            $email_template->subject = $request->subject;
            $email_template->from = $request->from;
            $email_template->content = $request->input('content');
            $email_template->save();
        }


//        $requestData = $request->all();
//
//        $templateData['company_id'] = Auth::id();
//        $templateData['template_key'] = 'sale';
//
//        $email_template = Email_template::where($templateData)->first();
//        if ($email_template) {
//            $email_template->name = $request->name;
//            $email_template->subject = $request->subject;
//            $email_template->from = $request->from;
//            $email_template->content = $request->content;
//            $email_template->save();
//        } else {
//            $requestData['company_id'] = Auth::id();
//            $requestData['template_key'] = 'sale';
//
//            Email_template::create($requestData);
//        }


        Session::flash('success', 'Template updated!');

        return redirect('company/email-template');
    }


}
