<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Tax_rates;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class TaxRatesController extends Controller
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
        return view('company.tax-rates.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getTaxRates()
    {        
        $tax_rates = Tax_rates::where('company_id',Auth::user()->id)->get();
        
        return Datatables::of($tax_rates)
            ->addColumn('action', function ($tax_rate) {
                return '<a href="tax-rates/'. Hashids::encode($tax_rate->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Tax Rate"><i class="fa fa-edit"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Tax Rate" id="'.Hashids::encode($tax_rate->id).'"><i class="fa fa-trash"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['action'])
            ->make(true);
            
    }    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        return view('company.tax-rates.create');                
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
            'name' => 'required|max:100',  
            'code' => 'required|max:50',            
            'rate' => 'required|numeric',
        ]);   
        
       $requestData = $request->all();         
       $requestData['company_id'] = Auth::id();         
        
        Tax_rates::create($requestData);
        
        Session::flash('success', 'Tax rate added!');        

        return redirect('company/tax-rates');  
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
        
        $tax_rate = Tax_rates::findOrFail($id);       

        return view('company.tax-rates.edit', compact('tax_rate'));
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
            'name' => 'required|max:100',  
            'code' => 'required|max:50',            
            'rate' => 'required|numeric',
        ]);
        
        $requestData = $request->all();                   
        
        $tax_rate = Tax_rates::findOrFail($id);
        $tax_rate->update($requestData);                               
        
        Session::flash('success', 'Tax rate updated!');

        return redirect('company/tax-rates');
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
        
        $tax_rate = Tax_rates::find($id);
        
        if($tax_rate){
            $tax_rate->delete();
            $response['success'] = 'Tax rate deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Tax rate not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    
    /**
     * Display a listing of the resource.
     *
     * @return json
     */
    public function getTaxRatesApi()
    {   
        if(\Request::wantsJson()) 
        {
                    
            $tax_rates = Tax_rates::where('company_id', getComapnyIdByUser())->get();                
            
            $tax_rates->map( function ($tax_rate) {
                
                $tax_rate->default = ($tax_rate->id == companySettingValueApi('tax_id') ? true : false);
                
                return $tax_rate;
            });
            
            $response['tax_rates'] = $tax_rates;
            $status = $this->successStatus;
            
            return response()->json(['result' => $response], $status);
        }
    }

}
