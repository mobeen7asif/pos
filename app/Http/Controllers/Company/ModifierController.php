<?php

namespace App\Http\Controllers\Company;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Company;
use App\Modifier;
use App\Modifier_option;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class ModifierController extends Controller
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
        return view('company.modifiers.index');
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getModifiers()
    {        
        $modifiers = Modifier::with(['modifier_options'])->get();                
        
        return Datatables::of($modifiers)
            ->addColumn('options', function ($modifier) {
                return @$modifier->modifier_options->count();
            })
            ->addColumn('action', function ($modifier) {
                return '<a href="modifiers/'. Hashids::encode($modifier->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Modifier"><i class="fa fa-edit"></i></a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Modifier" id="'.Hashids::encode($modifier->id).'"><i class="fa fa-trash"></i></a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['action','options'])
            ->make(true);
            
    }    
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {               
        return view('company.modifiers.create');                
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
            'max_options' => 'required|numeric',                           
        ]);   
        
       $requestData = $request->all();         
       $requestData['company_id'] = Auth::id();         
        
        $modifier = Modifier::create($requestData);
        
        if($modifier){
            // save modifier opitons
            for($s = 1; $s <= $requestData['total_options']; $s++){                       
               $option_data = [];
               $option_data['modifier_id'] = $modifier->id;
               $option_data['id'] = $requestData['option_id_'. $s];           

               $option = Modifier_option::firstOrNew($option_data);
               $option->name = $requestData['name_'. $s];
               $option->cost = $requestData['cost_'. $s];
               $option->price = $requestData['price_'. $s];
               $option->sku = $requestData['sku_'. $s];
               $option->ordering = $requestData['ordering_'. $s];
               $option->save();
            }
            
        }
        
        Session::flash('success', 'Modifier added!');        

        return redirect('company/modifiers');  
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
        
        $modifier = Modifier::with(['modifier_options' => function ($query) {
            $query->orderBy('id', 'asc');
        }])->findOrFail($id);       

        return view('company.modifiers.edit', compact('modifier'));
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
            'max_options' => 'required|numeric',  
        ]);
        
        $requestData = $request->all();                   
        
        $modifier = Modifier::findOrFail($id);
        $modifier->update($requestData);                               
        
        // update modifier opitons
        for($s = 1; $s <= $requestData['total_options']; $s++){                       
           $option_data = [];
           $option_data['modifier_id'] = $modifier->id;
           $option_data['id'] = $requestData['option_id_'. $s];           

           $option = Modifier_option::firstOrNew($option_data);
           $option->name = $requestData['name_'. $s];
           $option->cost = $requestData['cost_'. $s];
           $option->price = $requestData['price_'. $s];
           $option->sku = $requestData['sku_'. $s];
           $option->ordering = $requestData['ordering_'. $s];
           $option->save();
        }
        
        Session::flash('success', 'Modifier updated!');

        return redirect('company/modifiers');
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
        
        $modifier = Modifier::find($id);
        
        if($modifier){
            $modifier->delete();
            $response['success'] = 'Modifier deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Modifier not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function removeModifierOption($id)
    {   

        Modifier_option::destroy($id);

        return response()->json(['success' => 1]);  
        
    }

}
