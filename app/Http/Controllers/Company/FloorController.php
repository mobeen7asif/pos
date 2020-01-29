<?php

namespace App\Http\Controllers\Company;

use App\Floor;
use App\FloorTable;
use App\Http\Controllers\Controller;
use App\MealType;
use App\Store;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Role;
use Image;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Session;
use Alert;
use File;
use Hashids;
use Datatables;
use DB;
use LaravelQRCode\Facades\QRCode;

class FloorController extends Controller{
    
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('company.floors.index');
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getFloors()
    {
        $floors = Floor::with('store')->where('company_id',Auth::id())->get();
        return Datatables::of($floors)
            ->addColumn('store_name', function ($floor) {
                return $floor->store->name;
            })
            ->addColumn('action', function ($floor) {
                return '
                <a href="floors/'. Hashids::encode($floor->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Floor"><i class="fa fa-edit action-padding"></i> </a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Floor" id="'.Hashids::encode($floor->id).'"><i class="fa fa-trash action-padding"></i></a>
                <a href="tables/'. Hashids::encode($floor->id).'" class="text-success btn-order" data-toggle="tooltip" title="View Tables" id="'.$floor->id.'"><i class="fa fa-cutlery action-padding"></i></a>';
            })

            ->rawColumns(['action','store_name'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
       return view('company.floors.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $this->validate($request,[
            'name' => 'required',
            'store_id' => 'required',
            //'image' => 'required|mimes:jpeg,jpg,png,max:60000',
        ]);
        
        $requestData = $request->all();
        $requestData['company_id'] = Auth::id();
        $floor = Floor::create($requestData);

        //save  image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/floors'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $floor->id.'-'.str_random(10).'.'.$extension; // renameing image


            //create directory if not exists
            if (!file_exists($destinationPath.'/thumbs')) {
                mkdir($destinationPath.'/thumbs', 0777, true);
            }
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);
            $image->move($destinationPath, $fileName); // uploading file to given path

            //update image record
            $store_image['image'] = $fileName;
            $floor->update($store_image);
        }

        Session::flash('success', 'Floor added!');
        
        return redirect('company/floors');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        
        $id = Hashids::decode($id)[0];
        
        $floor = Floor::findOrFail($id);
        
        return view('company.floors.edit',compact('floor'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateFloor($id, Request $request)
    {
        $id = Hashids::decode($id)[0];

        $this->validate($request,[
            'name' => 'required',
            'store_id' => 'required',
            //'image' => 'mimes:jpeg,jpg,png,max:10240',
        ]);
        
        $requestData = $request->all();                   
        
        $floor = Floor::findOrFail($id);
        
        $floor->update($requestData);

        //save  image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/floors'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $floor->id.'-'.str_random(10).'.'.$extension; // renameing image


            //create directory if not exists
            if (!file_exists($destinationPath.'/thumbs')) {
                mkdir($destinationPath.'/thumbs', 0777, true);
            }
            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);
            $image->move($destinationPath, $fileName); // uploading file to given path


            //remove old image
            @File::delete($destinationPath . $floor->image);
            @File::delete($destinationPath .'/thumbs/'. $floor->image);

            //update image record
            $store_image['image'] = $fileName;
            $floor->update($store_image);
        }


        Session::flash('success', 'Floor updated!');
        
        return redirect('company/floors');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {   
        $id = Hashids::decode($id)[0];
        
        $floor = Floor::find($id);
        
        if($floor){
            $floor->delete();
            $response['success'] = 'Floor deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Floor not exist against this id!';
            $status = $this->errorStatus;  
        }
        
        return response()->json(['result'=>$response], $status);

    }
    public function getTablesView($id){
        return view('company.floors.tables',['floor_id' => $id]);
    }
    public function getTables(Request $request,$id)
    {
        $floor_id = Hashids::decode($id)[0];
        $tables = FloorTable::where('floor_id',$floor_id)->orderBy('updated_at','desc')->get();
        return Datatables::of($tables)
            ->addColumn('table_id', function ($table) {
                return $table->table_id;
            })
            ->addColumn('name', function ($table) {
                return $table->name;
            })
            ->addColumn('waiter', function ($table) {
                return $table->waiter->name;
            })
            ->addColumn('seats', function ($table) {
                return $table->seats;
            })
            ->addColumn('floor_name', function ($table) {
                return $table->floor->name;
            })
            ->addColumn('action', function ($table) {
                if($table->book_status == 1){
                    return '
                <a href="'. Hashids::encode($table->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Table"><i class="fa fa-edit action-padding"></i> </a>
                <a target="_blank" href="'. Hashids::encode($table->id).'/qr" class="text-primary" data-toggle="tooltip" title="Generate QR"><i class="fa fa-qrcode action-padding"></i>  </a>
                <a href="'. Hashids::encode($table->id).'/free_table" class="text-primary" data-toggle="tooltip" title="Free Table" class="action-padding"> Free Table </a>';
                } else {
                    return '
                <a href="'. Hashids::encode($table->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Table"><i class="fa fa-edit action-padding"></i> </a>
                <a target="_blank" href="'. Hashids::encode($table->id).'/qr" class="text-primary" data-toggle="tooltip" title="Generate QR"><i class="fa fa-qrcode action-padding"></i>  </a>';
                }

            })
            ->rawColumns(['action','table_id', 'table_name', 'floor_name'])
            //->editColumn('id', 'ID: {{$id}}')
            ->make(true);

    }
    public function editTableView($id){
        $id = Hashids::decode($id)[0];
        $table = FloorTable::findOrFail($id);
        $floor = Floor::where('id',$table->floor_id)->first();
        $waiters =  User::where('store_id',$floor->store->id)->where('role_name','Waiter')->pluck('name','id');
        return view('company.floors.edit_table',['table' => $table,'waiters' => $waiters,'floor_id' => Hashids::encode($table->floor->id)]);
    }

    public function freeTable($id){
        $id = Hashids::decode($id)[0];
        $table = FloorTable::findOrFail($id);
        $table->book_status = 0;
        $table->is_mobile_order = 0;
        $table->order_id = 0;
        $table->save();
        updateSyncData('table',$table->table_id,$table->floor->store->id);
        return redirect()->back()->with('success','Table freed successfully');
    }

    public function updateTable(Request $request)
    {
        $id = $request->input('tableId');
        $id = Hashids::decode($id)[0];

        $this->validate($request,[
            'name' => 'required',
            'seats' => 'required',
        ]);

        $requestData = $request->all();

        $table = FloorTable::findOrFail($id);

        $table->update($requestData);

        $table = FloorTable::findOrFail($id);

        updateSyncData('table',$table->table_id,$table->floor->store->id);

        Session::flash('success', 'Table updated!');

        return redirect('company/tables/'.Hashids::encode($table->floor->id));
    }

    public function waiterAssignView($id){
        $floor_id = Hashids::decode($id)[0];
        $tables = FloorTable::where('floor_id',$floor_id)->get();
        $floor = Floor::where('id',$floor_id)->first();
        $waiters =  User::where('store_id',$floor->store->id)->where('role_name','Waiter')->select('name','id','profile_image')->orderBy('name','asc')->get();
        return view('company.floors.assign_waiters',['tables' => $tables,'waiters' => $waiters, 'floor_id' => $floor_id]);
    }

    public function updateWaiter(Request $request)
    {

        $this->validate($request,[
            'waiter_id' => 'required',
            'table_ids' => 'required'
        ]);
        $waiter_id = $request->input('waiter_id');
        $table_ids = $request->input('table_ids');
        $floor_id = $request->input('floor_id');
        if($table_ids && count($table_ids) > 0){
            foreach ($table_ids as $table_id){
                $table = FloorTable::find($table_id);
                FloorTable::where('id',$table_id)->update(['waiter_id' => $waiter_id]);
                updateSyncData('table',$table->table_id,$table->floor->store->id);
            }
        }

        Session::flash('success', 'Waiters Assigned Successfully');

        return redirect('company/tables/'.Hashids::encode($floor_id));
    }
    public function getQr($id){
        $id = Hashids::decode($id)[0];
        $table = FloorTable::find($id);
        if($table){
            $qr_code['app_name'] = 'autonomous';
            $qr_code['type'] = 'table';
            $qr_code['table_id'] = $table->table_id;
            $qr_code = json_encode($qr_code);


            $fname = $table->table_id.'.png';
            $url = 'https://chart.googleapis.com/chart?';
            $chs = 'chs=300x300';
            $cht = 'cht=qr';


            $chl = 'chl='.$qr_code;

            $qstring = $url ."&". $chs ."&". $cht ."&". $chl;


            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'http://chart.apis.google.com/chart');
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, "chs=150x150&cht=qr&chl=" . urlencode($qr_code));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HEADER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 30);
            $img = curl_exec($ch);
            curl_close($ch);
            $f = fopen(public_path('uploads/qr/'.$fname), 'w');
            fwrite($f, $img);
            fclose($f);
            return redirect(asset('public/uploads/qr/'.$fname));
        }
        else {
               return redirect()->back()->with('error','Table not found');
        }


    }



}
