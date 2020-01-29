<?php

namespace App\Http\Controllers\Company;

use App\Ad;
use App\Floor;
use App\Http\Controllers\Controller;
use App\MealType;
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

class AdsController extends Controller{
    
    public $successStatus = 200;
    public $errorStatus = 401;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(){
        return view('company.ads.index');
    }
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function getAds()
    {
        $ads = Ad::with('store')->where('company_id',Auth::id())->get();
        return Datatables::of($ads)
            ->addColumn('store_name', function ($ad) {
                return $ad->store->name;
            })
            ->addColumn('time', function ($ad) {
                return $ad->time.' seconds';
            })
            ->addColumn('preview', function ($ad) {
                if($ad->media_type == 'image') {
                    return '<img width="100" src="'.checkImage('ads/'. $ad->media).'" /> <span style="padding-left: 30px">(Image)</span>';
                }
                else {
                    //return '<embed src="'.checkImage('ads/'. $ad->media).'" autostart="false" height="30" width="50" />';
                    return '<video width="100" ><source src="'.checkImage('ads/'. $ad->media).'" type="video/mp4"></video> <span style="padding-left: 30px">(Video)</span>';
                }
            })
            ->addColumn('action', function ($ad) {
                return '
                <a href="ads/'. Hashids::encode($ad->id).'/edit" class="text-primary" data-toggle="tooltip" title="Edit Ad"><i class="fa fa-edit action-padding"></i> </a> 
                <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Ad" id="'.Hashids::encode($ad->id).'"><i class="fa fa-trash action-padding"></i></a>';
            })
            ->rawColumns(['action','store_name','preview'])
            ->editColumn('id', 'ID: {{$id}}')
            ->make(true);
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(){
       return view('company.ads.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $media_type = $request->input('media_type');
        if($media_type == 'image'){
            $this->validate($request,[
                'store_id' => 'required',
                'time' => 'required',
                'media_type' => 'required',
                'media' => 'required|mimes:jpeg,jpg,png,max:40240',
            ]);
        } else {
            $this->validate($request,[
                'store_id' => 'required',
                'media_type' => 'required',
                'media' => 'required|mimes:mp4,mov,ogg,qt,wmv,flv,avi,max:40240',
            ]);
        }
        $requestData = $request->all();
        $requestData['company_id'] = Auth::id();
        if($media_type == 'video'){
            $requestData['time'] = 0;
        }
        unset($requestData['media']);
        $ad = Ad::create($requestData);

        //save  image
        if($request->hasFile('media')){
            $destinationPath = 'uploads/ads'; // upload path
            $media = $request->file('media'); // file
            $extension = $media->getClientOriginalExtension(); // getting image extension
            $fileName = $ad->id.'-'.str_random(10).'.'.$extension; // renameing image


            //create directory if not exists
            if (!file_exists($destinationPath.'/thumbs')) {
                mkdir($destinationPath.'/thumbs', 0777, true);
            }
//            $img = Image::make($image->getRealPath());
//            $img->resize(100, 100, function ($constraint) {
//                $constraint->aspectRatio();
//            })->save($destinationPath.'/thumbs/'.$fileName);
            $media->move($destinationPath, $fileName); // uploading file to given path

            //update image record
            $ad_image['media'] = $fileName;
            $ad->update($ad_image);
        }
        updateSyncData('ad',$ad->id,$requestData['store_id']);
        Session::flash('success', 'Advertisement added!');
        
        return redirect('company/ads');
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
        
        $ad = Ad::findOrFail($id);
        
        return view('company.ads.edit',compact('ad'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function updateAd($id, Request $request)
    {
        //dd($request->all());
        $id = Hashids::decode($id)[0];

        $media_type = $request->input('media_type');
        if($media_type == 'image'){
            $this->validate($request,[
                'store_id' => 'required',
                'time' => 'required',
                'media_type' => 'required',
                'media' => 'mimes:jpeg,jpg,png,max:40240',
            ]);
        } else {
            $this->validate($request,[
                'store_id' => 'required',
                'media_type' => 'required',
                'media' => 'mimes:mp4,mov,ogg,qt,wmv,flv,avi,max:40240',
            ]);
        }
        
        $requestData = $request->all();                   
        
        $ad = Ad::findOrFail($id);

        $ad->update($requestData);

        //save  image
        if($request->hasFile('media')){
            $destinationPath = 'uploads/ads'; // upload path
            $media = $request->file('media'); // file
            $extension = $media->getClientOriginalExtension(); // getting image extension
            $fileName = $ad->id.'-'.str_random(10).'.'.$extension; // renameing image


            //create directory if not exists
            if (!file_exists($destinationPath.'/thumbs')) {
                mkdir($destinationPath.'/thumbs', 0777, true);
            }
//            $img = Image::make($image->getRealPath());
//            $img->resize(100, 100, function ($constraint) {
//                $constraint->aspectRatio();
//            })->save($destinationPath.'/thumbs/'.$fileName);
            $media->move($destinationPath, $fileName); // uploading file to given path

            //remove old image
            File::delete($destinationPath . $ad->media);
            //File::delete($destinationPath .'/thumbs/'. $ad->media);

            //update image record
            $ad_image['media'] = $fileName;
            $ad->update($ad_image);
        }
        updateSyncData('ad',$ad->id,$requestData['store_id']);
        Session::flash('success', 'Ad updated!');
        
        return redirect('company/ads');
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
        
        $ad = Ad::find($id);
        
        if($ad){
            updateSyncData('ad_delete',$ad->id,$ad->store_id);
            $ad->delete();
            $response['success'] = 'Ad deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Ad not exist against this id!';
            $status = $this->errorStatus;  
        }
        return response()->json(['result'=>$response], $status);

    }


}
