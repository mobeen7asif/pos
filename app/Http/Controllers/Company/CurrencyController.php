<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Currency;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class CurrencyController extends Controller
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
        return view('company.currencies.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getCurrencies()
    {        
        $currencies = Currency::where('company_id',Auth::id())->get();
        $currency_count = $currencies->count();
        
        return Datatables::of($currencies)
            ->addColumn('direction', function ($currency) {
                if($currency->direction == 1)
                    return 'Left';
                elseif($currency->direction == 2)
                    return 'Right';
            })
            ->addColumn('action', function ($currency) use ($currency_count) {
                if($currency_count > 1){
                    return '<a href="currencies/'. Hashids::encode($currency->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Currency"><i class="fa fa-edit"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Currency" id="'.Hashids::encode($currency->id).'"><i class="fa fa-trash"></i></a>';
                } else {
                    return '<a href="currencies/'. Hashids::encode($currency->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Currency"><i class="fa fa-edit"></i></a>';
                }

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
        return view('company.currencies.create');                
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
            'code' => 'required',            
            'name' => 'required|max:255',                           
            'symbol' => 'required',
            'direction' => 'required',
        ]);   
        
       $requestData = $request->all();         
       $requestData['company_id'] = Auth::id();         
        
        Currency::create($requestData);
        
        Session::flash('success', 'Currency added!');        

        return redirect('company/currencies');  
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
        
        $currency = Currency::findOrFail($id);       

        return view('company.currencies.edit', compact('currency'));
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
            'code' => 'required',            
            'name' => 'required|max:255',                           
            'symbol' => 'required',    
            'direction' => 'required',
        ]);
        
        $requestData = $request->all();                   
        
        $currency = Currency::findOrFail($id);
        $currency->update($requestData);                               
        
        Session::flash('success', 'Currency updated!');

        return redirect('company/currencies');
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
        
        $currency = Currency::find($id);
        
        if($currency){
            $currency->delete();
            $response['success'] = 'Currency deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Currency not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }

}
