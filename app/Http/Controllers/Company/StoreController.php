<?php

namespace App\Http\Controllers\Company;

use App\DutySetting;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\MealType;
use DateTime;
use DateTimeZone;
use Illuminate\Support\Facades\Auth;

use App\Store;
use App\User;
use App\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class StoreController extends Controller
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
        //dd(getTimeZones());
        return view('company.stores.index');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getStores()
    {
        $stores = Store::with(['currency'])->where('company_id',Auth::id())->orderBy('id','desc')->get();

        return Datatables::of($stores)
            ->addColumn('image', function ($store) {
                return '<img width="30" src="'.checkImage('stores/thumbs/'. $store->image).'" />';
            })
            ->addColumn('Store Admin', function ($store) {
                $admins = User::where('store_id',$store->id)->where('role_name','Store Admin')->get();
                $anchor_html = '';
                $i = 0;
                $size = count($admins);
                foreach ($admins as $admin){
                    if($i == $size - 1){
                        $anchor_html .= '<a href="users/'. Hashids::encode($admin->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Admin">'.$admin->name.'</a>';
                    } else {
                        $anchor_html .= '<a href="users/'. Hashids::encode($admin->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Admin">'.$admin->name.'</a>, ';
                    }

                    $i++;
                }
                return $anchor_html;
            })
            ->addColumn('currency_id', function ($store) {
                return @$store->currency->name;
            })
            ->addColumn('action', function ($store) {
                return '<a href="stores/'. Hashids::encode($store->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Store"><i class="fa fa-edit action-padding"></i></a>
<a href="store/'. Hashids::encode($store->id).'/beacon" class="text-primary" data-toggle="tooltip" title="Add Beacon">Beacon</a>';
            })
            ->editColumn('id', 'ID: {{$id}}')
            ->rawColumns(['image','company_name', 'action','Store Admin'])
            ->make(true);

        //<a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Store" id="'.Hashids::encode($store->id).'"><i class="fa fa-trash"></i></a>

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('company.stores.create');
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
            'currency_id' => 'required',
            'image' => 'required|mimes:jpeg,jpg,png',
            'background_image' => 'required|mimes:jpeg,jpg,png',
            'logo' => 'mimes:jpeg,jpg,png',
            'receipt_logo' => 'mimes:jpeg,jpg,png',
            'tax_id' => 'required',
            'time_zone' => 'required',
        ]);
        $this->validate($request, [
            'name' => ['required' , function($attribute, $value, $fail) {
                $store = Store::where('name',$value)->first();
                if($store){
                    $fail($attribute.' is already taken.');
                }
            },
            ],
        ]);

        $requestData = $request->all();
        if(!isset($requestData['set_break_time'])){
            $requestData['break_time'] = null;
            $requestData['set_break_time'] = 0;
        }
        $requestData['company_id'] = Auth::id();

        $store = Store::create($requestData);

        //saving meal types
        if(Auth::user()->company_type == 1){
            $meal_types_array = ['Drink','Starter','Main Course','Deserts'];
            $meal_types_colors = ['4de2c0','ffc30c','bd10e0','fd3d50'];
            $data = [];
            for($i = 0; $i < 4; $i++){
                $temp = [];
                $temp['store_id'] = $store->id;
                $temp['company_id'] = Auth::user()->id;
                $temp['meal_type'] = $meal_types_array[$i];
                $temp['color'] = $meal_types_colors[$i];
                $temp['created_at'] = date('Y-m-d H:i:s');
                $temp['updated_at'] = date('Y-m-d H:i:s');
                $data[] = $temp;
            }
            MealType::insert($data);
        }

        //save logo image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/stores'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $store->id.'-'.str_random(10).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //update image record
            $store_image['image'] = $fileName;
            $store->update($store_image);
        }

        if($request->hasFile('background_image')){
            $destinationPath = 'uploads/stores'; // upload path
            $image = $request->file('background_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $store->id.'-'.str_random(10).'.'.$extension; // renameing image

//            $img = Image::make($image->getRealPath());
//            $img->resize(100, 100, function ($constraint) {
//                $constraint->aspectRatio();
//            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //update image record
            $store_image['background_image'] = $fileName;
            $store->update($store_image);
        }

        if($request->hasFile('receipt_logo')){
            $destinationPath = 'uploads/stores/receipt_logos'; // upload path
            $image = $request->file('receipt_logo'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $store->id.'-'.str_random(10).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //update image record
            $store_image['receipt_logo'] = $fileName;
            $store->update($store_image);
        }

        //save duty settings
        $this->addDutySettings($requestData,$store->id);

        Session::flash('success', 'Store added!');

        return redirect('company/stores');
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

        $store = Store::findOrFail($id);
        $setting = DutySetting::where('store_id',$store->id)->first();
        return view('company.stores.edit', compact('store','setting'));
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
            'currency_id' => 'required',
            'tax_id' => 'required',
            'time_zone' => 'required',
            'image' => 'mimes:jpeg,jpg,png',
            'background_image' => 'smimes:jpeg,jpg,png',
            'logo' => 'mimes:jpeg,jpg,png',
            'receipt_logo' => 'mimes:jpeg,jpg,png',
        ]);
        $this->validate($request, [
            'name' => ['required' , function($attribute, $value, $fail) use($id) {
                $store = Store::where('name',$value)->where('id','!=',$id)->first();
                if($store){
                    $fail($attribute.' is already taken.');
                }
            },
            ],
        ]);
        $requestData = $request->all();
        if(!isset($requestData['set_break_time'])){
            $requestData['break_time'] = null;
            $requestData['set_break_time'] = 0;
        }
        $store = Store::findOrFail($id);
        $store->update($requestData);

        //save store image
        if($request->hasFile('image')){
            $destinationPath = 'uploads/stores'; // upload path
            $image = $request->file('image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $store->id.'-'.str_random(10).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //remove old image
            File::delete($destinationPath . $store->image);
            File::delete($destinationPath .'/thumbs/'. $store->image);

            //update image record
            $store_image['image'] = $fileName;
            $store->update($store_image);
        }

        if($request->hasFile('background_image')){
            $destinationPath = 'uploads/stores'; // upload path
            $image = $request->file('background_image'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $store->id.'-'.str_random(10).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(100, 100, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //remove old image
            File::delete($destinationPath . $store->image);
            File::delete($destinationPath .'/thumbs/'. $store->image);

            //update image record
            $store_image['background_image'] = $fileName;
            $store->update($store_image);
        }
        if($request->hasFile('receipt_logo')){
            $destinationPath = 'uploads/stores/receipt_logos'; // upload path
            $image = $request->file('receipt_logo'); // file
            $extension = $image->getClientOriginalExtension(); // getting image extension
            $fileName = $store->id.'-'.str_random(10).'.'.$extension; // renameing image

            $img = Image::make($image->getRealPath());
            $img->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            })->save($destinationPath.'/thumbs/'.$fileName);

            $image->move($destinationPath, $fileName); // uploading file to given path

            //update image record
            $store_image['receipt_logo'] = $fileName;
            $store->update($store_image);
        }
        //update duty settings
        $this->addDutySettings($requestData,$id);

        Session::flash('success', 'Store updated!');

        return redirect('company/stores');
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

        $store = Store::find($id);

        if($store){

            $store->delete();
            $response['success'] = 'Store deleted!';
            $status = $this->successStatus;
        }else{
            $response['error'] = 'Store not exist against this id!';
            $status = $this->errorStatus;
        }

        return response()->json(['result'=>$response], $status);

    }
    public function beaconView($store_id){
        $store_id = Hashids::decode($store_id)[0];
        $store = Store::find($store_id);
        return view('company.stores.beacon',['store' => $store]);
    }
    public function addBeacon(Request $request,$store_id){
        $store_id = Hashids::decode($store_id)[0];
        $this->validate($request,[
            'uid' => 'required',
            'major' => 'required',
            'minor' => 'required',
        ]);
        $uid = $request->input('uid');
        $major = $request->input('major');
        $minor = $request->input('minor');
        $check_duplicate = Store::where('id','<>',$store_id)->where('uid',$uid)->where('major',$major)->where('minor',$minor)->first();
        if($check_duplicate){
            Session::flash('error', 'These keys are already taken');
            return redirect()->back()->withInput();
        }
        $store = Store::find($store_id);
        $store->uid = $uid;
        $store->major = $major;
        $store->minor = $minor;

        $store->save();
        Session::flash('success', 'Beacon Updated');

        return redirect('company/stores');

    }


    public function createStoreView(){
        return view('company.stores.create');
    }
    public function deleteImage($table,$id,$column){
        DB::table($table)->where('id',$id)->update([$column => 'no_picture.jpg']);
        Session::flash('success', 'Image Deleted');

        return redirect()->back();
    }

    public function addDutySettings($request,$store_id)
    {
        $requestData = $request;
        $store_duty_setting = DutySetting::where('store_id',$store_id)->first();
        if($store_duty_setting){
            //save logo
            if(isset($requestData['logo'])){
                $destinationPath = 'uploads/duty_logos'; // upload path
                $image = $requestData['logo']; // file
                $extension = $image->getClientOriginalExtension(); // getting image extension
                $fileName = $store_duty_setting->id.'-'.str_random(10).'.'.$extension; // renameing image

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
            }
            $store_duty_setting->update($requestData);
        }
        else{
            //update logo
            $requestData['company_id'] = Auth::id();
            $requestData['store_id'] = $store_id;
            $store_duty_setting = DutySetting::create($requestData);
            if(isset($requestData['logo'])){
                $destinationPath = 'uploads/duty_logos'; // upload path
                $image = $requestData['logo']; // file
                $extension = $image->getClientOriginalExtension(); // getting image extension
                $fileName = $store_duty_setting->id.'-'.str_random(10).'.'.$extension; // renameing image
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
            DutySetting::where(['store_id' => $store_id])->update(['logo' => $requestData['logo']]);
        }

        Session::flash('success', 'Settings updated!');

        return redirect('company/duty/settings');
    }

}
