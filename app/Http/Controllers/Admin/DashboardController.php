<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Country;
use App\Store;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class DashboardController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {     
       
        $data['total_comapny'] = Company::count();       
        
        return view('admin.dashboard', $data);
       
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getCompanies()
    {        
        $companies = Company::with(['store'])->get();                
        
        return Datatables::of($companies)
            ->addColumn('logo', function ($company) {
                return '<img width="30" src="'.checkImage('companies/thumbs/'. $company->logo).'" />';
            })
            ->addColumn('total_stores', function ($company) {
                return @$company->store->count();
            })
            ->addColumn('action', function ($company) {
                return '<a href="companies/'. Hashids::encode($company->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Company"><i class="fa fa-edit"></i></a> 
                        <a href="companies/company-login/'. Hashids::encode($company->id).'" class="text-success" target="_blank" data-toggle="tooltip" title="Company Login"><i class="fa fa-sign-in"></i></a>
                        <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Comapny" id="'.Hashids::encode($company->id).'"><i class="fa fa-trash"></i></a>';
            })           
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['logo', 'total_stores', 'action'])
            ->make(true);
            
    }    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        return view('admin.companies.create');                
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
            'email' => 'required|email|max:255|unique:companies',
            'password' => 'required|min:6|confirmed',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address' => 'required',       
            'logo' => 'required|mimes:jpeg,jpg,png',
        ]);   
        
       $requestData = $request->all();  
       
        $requestData['password'] = bcrypt($requestData['password']);
        
        $company = Company::create($requestData);
        
        if($company){
            $store['company_id'] = $company->id;
            $store['name'] = 'Default';
            $store['address'] = $company->address .' '.$company->city;
            $store['image'] = 'default.jpg';
            
            Store::create($store);
        }
        
        //save logo image
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
            
            //update image record
            $company_image['logo'] = $fileName;
            $company->update($company_image);
        }
        
        Alert::success('Success Message', 'Company added!');        

        return redirect('admin/companies');  
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
        
        $company = Company::findOrFail($id);       
        
        return view('admin.companies.edit', compact('company'));
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
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required',
            'address' => 'required',                                         
        ]);
        
        $requestData = $request->all();                   
        
        $company = Company::findOrFail($id);
        $company->update($requestData);        
        
        //save category image
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
        
        Alert::success('Success Message', 'Company updated!');

        return redirect('admin/companies');
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
        
        $company = Company::find($id);
        
        if($company){
            $company->delete();
            $response['success'] = 'Company deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Company not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    
    
    /**
     * Company Login.
     *
     * @param \Illuminate\Http\Request $company_id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function companyLogin($company_id)
    {            
        $id = Hashids::decode($company_id)[0];
        
        Auth::guard('company')->loginUsingId($id);
        
        return redirect('company/dashboard');
    }
    
}
