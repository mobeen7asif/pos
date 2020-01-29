<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

use App\Categories;
use App\Regions;
use App\Category_sections;
use App\Section_questions;
use App\Question_answers;
use App\Items;
use App\Category_images;
use Illuminate\Http\Request;
use Session;
use Alert;
use Image;
use File;
use Hashids;
use Datatables;

class SubcategoriesController extends Controller
{
    public $successStatus = 200;
    public $errorStatus = 401;
    public $notFoundStatus = 404;
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $category_id = '';
        return view('admin.subcategories.index', compact('category_id'));
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    /*public function getItemOpponents($id)
    {      
        $records = Items::with(['user.country'])->where('category_id',$id)->groupBy('user_id')->get();                                   
        $records->map(function ($record) use ($id) {
            $user_score = Items::select('score')->where(['category_id' => $id, 'user_id' => $record->user->id])->pluck('score');
            
            $record->score = str_pad($record->score,7,0,STR_PAD_LEFT);
            $record['avg_score'] = str_pad(ceil($user_score->avg()),7,0,STR_PAD_LEFT);
            $record['max_score'] = str_pad($user_score->max(),7,0,STR_PAD_LEFT);
            $record['min_score'] = str_pad($user_score->min(),7,0,STR_PAD_LEFT);
            $record['user_score'] = str_pad($user_score->sum(),7,0,STR_PAD_LEFT);

            $record->user['profile_thumbnail'] = checkImage('users/thumbs/'. $record->user->profile_image);
            $record->user['profile_image'] = checkImage('users/'. $record->user->profile_image);
            $record->user['records_count'] = str_pad(Items::where(['category_id' => $id, 'user_id' => $record->user->id])->count(),7,0,STR_PAD_LEFT);
            $record->user->user_score = str_pad($record->user->user_score,7,0,STR_PAD_LEFT);


            return $record;
        });
        return $records;
    }*/

    /*public function edit($id)
    {   
        $url = url()->current();
        if(\Request::wantsJson()) 
        {  
            $item = Categories::with(['category', 'region', 'item_images','category_sections.section_questions.question_answers'])->findOrFail($id);
            
            $item_score = Items::select('score')->where('category_id',$item->id)->pluck('score');
            $item['share_url'] = \URL::to("/api/get-item/$item->id/records");
            $item['avg_score'] = str_pad(ceil($item_score->avg()),7,0,STR_PAD_LEFT);
            $item['max_score'] = str_pad($item_score->max(),7,0,STR_PAD_LEFT);
            $item['min_score'] = str_pad($item_score->min(),7,0,STR_PAD_LEFT);

            $item['item_name'] = $item->category_name;
            if($item->category->category_image != ""){
                $item->category->category_thumbnail = checkImage('categories/thumbs/'. $item->category->category_image);
                $item->category->category_image = checkImage('categories/'. $item->category->category_image);
            }

            $item['records_count'] = str_pad(Items::where('category_id',$item->id)->count(),7,0,STR_PAD_LEFT);
            if (strpos($url, "records")!==false){
                //$item['records'] = Items::with(['user.country', 'record_images'])->where('category_id',$item->id)->get();
                $item['records'] = $this->getItemRecords($item->id, $item['item_name'], $item->description, $item->region);
                
                
            }
            
            $item->item_images->map(function ($image) {
                $image['item_thumbnail'] = checkImage('items/thumbs/'. $image->name);
                $image['item_image'] = checkImage('items/'. $image->name);  

                return $image;
            });
                
            $item->category_sections->map(function ($section){
            
                $section->section_questions->map(function ($question){

                    $question->question_answers->map(function ($answer){

                        $answer['checked'] = false;

                        return $answer;
                    });

                    return $question;
                });

                return $section;
            });
            
            unset($item->category->description);
            unset($item->category_image);
            unset($item->category_name); 
            
            $response['item'] = $item;
            

            if (strpos($url, "opponents")!==false){
                $response['opponents'] = $this->getItemOpponents($id);
            }
            
            return response()->json(['result' => $response], $this->successStatus);
        }
        
        $id = Hashids::decode($id)[0];
        
        $form_type = 'Update';
        $images = Category_images::where('category_id',$id)->get();
        $categories = Categories::where('parent_id',0)->pluck('category_name','id')->prepend('Select League', '');        
        $regions = Regions::pluck('name','id')->prepend('Select Region', '');        
        
        $category = Categories::with(['category_sections.section_questions.question_answers'])->findOrFail($id);       
        $category['region_name'] = $category['region_id'];              
        $category['league_name'] = $category['parent_id'];              
        $category['item_name'] = $category['category_name'];
                
        return view('admin.subcategories.form',  compact('form_type','categories','regions','images','category','questions'));
    }*/

    public function getSubcategories($category_id="",$region_id="")
    {   //return Auth::user();
        $sub_categories = Categories::with('category', 'region', 'item_images','category_sections.section_questions.question_answers'); 
         
        if($category_id == ""){
            $sub_categories->where('parent_id','!=',0);            
        }else{
            if(\Request::ajax()){ 
                $category_id = Hashids::decode($category_id)[0];
                $sub_categories->where('parent_id',$category_id);
            }elseif(\Request::wantsJson()){              
                $sub_categories->where('parent_id',$category_id);                                
            }
        }
        
        if($region_id != ""){
            $sub_categories->where('region_id',$region_id);
        }
        
        if(\Request::ajax())
        {     
            $sub_categories = $sub_categories->get();
            return Datatables::of($sub_categories)
                ->addColumn('item_image', function ($sub_category) {
                    return '<img width="50" src="'.checkImage('items/thumbs/'. (($sub_category->item_images->count() > 0)?$sub_category->item_images[0]->name:'image')).'" />';                
                })
                ->addColumn('category_name', function ($sub_category) {
                    return  $sub_category->category->category_name;
                })
                ->addColumn('item_name', function ($sub_category) {
                    return  $sub_category->category_name;
                })
                ->addColumn('records_count', function ($sub_category) {
                    return  '<a href="'. url('admin/records/'.Hashids::encode($sub_category->category->id) .'/'.Hashids::encode($sub_category->id)) .'" >'.Items::where('category_id',$sub_category->id)->count().'</a>';
                })
                ->addColumn('action', function ($sub_category) {
                    return '<a href="items/'. Hashids::encode($sub_category->id).'/edit" class="btn btn-xs btn-primary"><i class="glyphicon glyphicon-edit"></i> Edit</a> 
                    <a href="javascript:void(0)" class="btn btn-xs btn-danger btn-delete" id="'.Hashids::encode($sub_category->id).'"><i class="glyphicon glyphicon-remove"></i> Delete</a>';
                })
                ->editColumn('id', 'ID: {{$id}}')
                ->rawColumns(['item_image','records_count', 'action'])
                ->make(true);
            
        }
        elseif(\Request::wantsJson()) 
        { 
            $sub_categories = $sub_categories->get();            
            if($sub_categories->isNotEmpty()){

                $response['category'] = $sub_categories[0]->category; 
                $response['category']['share_url'] = \URL::to('/api/get-items');
                $response['category']['category_thumbnail'] = checkImage('categories/thumbs/'. $sub_categories[0]->category->category_image);
                $response['category']['category_image'] = checkImage('categories/'. $sub_categories[0]->category->category_image);                
                $response['category']['items_count'] = str_pad(Categories::where('parent_id',$sub_categories[0]->category->id)->count(),7,0,STR_PAD_LEFT);
                $response['category']['records_count'] = str_pad(Items::whereIn('category_id',Categories::select('id')->where('parent_id' ,$sub_categories[0]->category->id)->pluck('id'))->count(),7,0,STR_PAD_LEFT);
                $response['category']['collectors_count'] = str_pad(Items::whereIn('category_id',Categories::select('id')->where('parent_id' ,$sub_categories[0]->category->id)->pluck('id'))->groupBy('user_id')->count(),7,0,STR_PAD_LEFT);
                $response['region'] = $sub_categories[0]->region;

                $sub_categories->map(function ($item,$key) use ($category_id) {
                    $key = $key+1;
                    $item_score = Items::select('score')->where(['category_id' => $item->id])->pluck('score');

                    $item['avg_score'] = str_pad(ceil($item_score->avg()),7,0,STR_PAD_LEFT);
                    $item['max_score'] = str_pad($item_score->max(),7,0,STR_PAD_LEFT);
                    $item['min_score'] = str_pad($item_score->min(),7,0,STR_PAD_LEFT);

                    if(Auth::id()){
                        $item['your_score'] = str_pad(Items::select('score')->where(['category_id' => $item->id, 'user_id' => Auth::id()])->pluck('score')->sum(),7,0,STR_PAD_LEFT);
                    }
                    $item['position_in_league'] = str_pad(($key),7,0,STR_PAD_LEFT);

                    $item['item_name'] = $item->category_name;                
                    $item['records_count'] = str_pad(Items::where('category_id',$item->id)->count(),7,0,STR_PAD_LEFT);
                    $item->item_images->map(function ($image) {
                        $image['item_thumbnail'] = checkImage('items/thumbs/'. $image->name);
                        $image['item_image'] = checkImage('items/'. $image->name);  

                        return $image;
                    });

                    $item->category_sections->map(function ($section){

                        $section->section_questions->map(function ($question){

                            $question->question_answers->map(function ($answer){

                                $answer['checked'] = false;

                                return $answer;
                            });

                            return $question;
                        });

                        return $section;
                    });

                    unset($item->category);
                    unset($item->region);
                    unset($item->category_image);
                    unset($item->category_name);
                    return $item;
                });                            
                        
            if(Auth::id()){
                $response['items'] = $sub_categories->sortByDesc('your_score')->values()->all();
            } else {
                $response['items'] = $sub_categories->all();
            }
            
            return response()->json(['result' => $response], $this->successStatus);
            }else{
              
              $response['items'] = $sub_categories->all();
              return response()->json(['result' => $response], $this->notFoundStatus);  
            }
            
        }                
            
    }    

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {   
        $form_type = 'Add';
        $images = [];
        $category = [];
        $questions = ['Case / Box','A Game','The Manual'];
        
        $categories = Categories::where('parent_id',0)->pluck('category_name','id')->prepend('Select League', '');        
        $regions = Regions::pluck('name','id')->prepend('Select Region', '');        
        
        return view('admin.subcategories.form', compact('form_type','categories','regions','images','category','questions'));                
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
            'region_name' => 'required',                                         
            'league_name' => 'required',                                         
            'item_name' => 'required',                                         
            'description' => 'required',                                         
        ]);
        
        $requestData = $request->all();              
        $requestData['region_id'] = $requestData['region_name'];              
        $requestData['parent_id'] = $requestData['league_name'];              
        $requestData['category_name'] = $requestData['item_name'];              
        
        $category = Categories::create($requestData);
        
        if(isset($requestData['image_ids'])){
          foreach($requestData['image_ids'] as $image_id){
                $category_images = Category_images::find($image_id);
                $category_images->category_id = $category->id;
                $category_images->update();
          }          
        }
        
        // save category section
        for($s = 1; $s <= $requestData['total_sections']; $s++){                       
           $section_data = [];
           $section_data['category_id'] = $category->id;
           $section_data['id'] = $requestData['section_id_'. $s];           

           $section = Category_sections::firstOrNew($section_data);
           $section->section_name = $requestData['section_name_'. $s];
           $section->save();
           
           // save section questions
           $total_section_questions = $requestData['total_section_'. $s .'_questions'];
           if($total_section_questions > 0){
               for($sq = 1; $sq <= $total_section_questions; $sq++){  
                    $section_question_data = [];
                    $section_question_data['section_id'] = $section->id;
                    $section_question_data['id'] = $requestData['question_id_'.$s.'_'. $sq];   
                    
                    $question = Section_questions::firstOrNew($section_question_data);
                    $question->question_name = $requestData['question_name_'.$s.'_'. $sq];
                    $question->answer_type = $requestData['answer_type_'.$s.'_'. $sq];
                    $question->save();           
                    
                    // save section question answers
                    $total_question_answers = $requestData['total_section_'. $s .'_question_'.$sq.'_answers'];
                    if($total_question_answers > 0){
                        for($sqa = 1; $sqa <= $total_question_answers; $sqa++){  
                            $question_answer_data = [];
                            $question_answer_data['question_id'] = $question->id;                             
                            $question_answer_data['id'] = $requestData['answer_id_'.$s.'_'. $sq.'_'. $sqa];    
                    
                            $answer = Question_answers::firstOrNew($question_answer_data);
                            $answer->answer_name = $requestData['answer_name_'.$s.'_'. $sq.'_'. $sqa];
                            $answer->score = ($requestData['score_'.$s.'_'. $sq.'_'. $sqa]==""?0:$requestData['score_'.$s.'_'. $sq.'_'. $sqa]);
                            $answer->save();                                                         
                        }
                    }
               }
           }
           
        }
        
        Alert::success('Success Message', 'Item added!');        

        return redirect('admin/items');  
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category_id = $id;
        return view('admin.subcategories.index', compact('category_id'));
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
        $url = url()->current();
        if(\Request::wantsJson()) 
        {  
            $item = Categories::with(['category', 'region', 'item_images','category_sections.section_questions.question_answers'])->findOrFail($id);
            
            $item_score = Items::select('score')->where('category_id',$item->id)->pluck('score');
            $item['share_url'] = \URL::to("/api/get-item/$item->id/records");
            $item['avg_score'] = str_pad(ceil($item_score->avg()),7,0,STR_PAD_LEFT);
            $item['max_score'] = str_pad($item_score->max(),7,0,STR_PAD_LEFT);
            $item['min_score'] = str_pad($item_score->min(),7,0,STR_PAD_LEFT);

            $item['item_name'] = $item->category_name;
            if($item->category->category_image != ""){
                $item->category->category_thumbnail = checkImage('categories/thumbs/'. $item->category->category_image);
                $item->category->category_image = checkImage('categories/'. $item->category->category_image);
            }

            $item['records_count'] = str_pad(Items::where('category_id',$item->id)->count(),7,0,STR_PAD_LEFT);
            if (strpos($url, "records")!==false){
                //$item['records'] = Items::with(['user.country', 'record_images'])->where('category_id',$item->id)->get();
                $item['records'] = $this->getItemRecords($item->id, $item['item_name'], $item->description, $item->region);
                
                /*$item->records->map(function ($user_image) {
                    $user_image->user['profile_thumbnail'] = checkImage('users/thumbs/'. $user_image->user->profile_image);
                    $user_image->user['profile_image'] = checkImage('users/'. $user_image->user->profile_image); 

                    $user_image->record_images->map(function ($record_image) {
                        $record_image['record_thumbnail'] = checkImage('items/thumbs/'. $record_image->name);
                        $record_image['record_image'] = checkImage('items/'. $record_image->name); 
                        return $record_image;
                    });

                    return $user_image;
                });*/
            }
            
            $item->item_images->map(function ($image) {
                $image['item_thumbnail'] = checkImage('items/thumbs/'. $image->name);
                $image['item_image'] = checkImage('items/'. $image->name);  

                return $image;
            });
                
            $item->category_sections->map(function ($section){
            
                $section->section_questions->map(function ($question){

                    $question->question_answers->map(function ($answer){

                        $answer['checked'] = false;

                        return $answer;
                    });

                    return $question;
                });

                return $section;
            });
            
            unset($item->category->description);
            unset($item->category_image);
            unset($item->category_name); 
            
            $response['item'] = $item;
            

            if (strpos($url, "opponents")!==false){
                $response['opponents'] = $this->getItemOpponents($id);
            }
            
            return response()->json(['result' => $response], $this->successStatus);
        }
        
        $id = Hashids::decode($id)[0];
        
        $form_type = 'Update';
        $images = Category_images::where('category_id',$id)->get();
        $categories = Categories::where('parent_id',0)->pluck('category_name','id')->prepend('Select League', '');        
        $regions = Regions::pluck('name','id')->prepend('Select Region', '');        
        
        $category = Categories::with(['category_sections.section_questions.question_answers'])->findOrFail($id);       
        $category['region_name'] = $category['region_id'];              
        $category['league_name'] = $category['parent_id'];              
        $category['item_name'] = $category['category_name'];
                
        return view('admin.subcategories.form',  compact('form_type','categories','regions','images','category','questions'));
    }

    public function getOpponents($id){
        if(\Request::wantsJson()) 
        {
            $opponents['opponents'] = $this->getItemOpponents($id);
            return response()->json(['result' => $opponents], $this->successStatus);
        }
    }

    // get opponents against item
    public function getItemOpponents($id)
    {      
        $records = Items::with(['user.country'])->where('category_id',$id)->groupBy('user_id')->get();                                   
        /*$item_ids = Categories::select('id')->where('parent_id' ,$id)->pluck('id');
        $records = Items::with(['user.country'])->whereIn('category_id',$item_ids)->groupBy('user_id')->get();*/
        $records->map(function ($record) use ($id) {
            $user_score = Items::select('score')->where(['category_id' => $id, 'user_id' => $record->user->id])->pluck('score');
            
            $record->score = str_pad($record->score,7,0,STR_PAD_LEFT);
            $record['avg_score'] = str_pad(ceil($user_score->avg()),7,0,STR_PAD_LEFT);
            $record['max_score'] = str_pad($user_score->max(),7,0,STR_PAD_LEFT);
            $record['min_score'] = str_pad($user_score->min(),7,0,STR_PAD_LEFT);
            $record['user_score'] = str_pad($user_score->sum(),7,0,STR_PAD_LEFT);

            $record->user['profile_thumbnail'] = checkImage('users/thumbs/'. $record->user->profile_image);
            $record->user['profile_image'] = checkImage('users/'. $record->user->profile_image);
            $record->user['records_count'] = str_pad(Items::where(['category_id' => $id, 'user_id' => $record->user->id])->count(),7,0,STR_PAD_LEFT);
            $record->user->user_score = str_pad($record->user->user_score,7,0,STR_PAD_LEFT);


            return $record;
        });
        return $records;
    }

    public function getOpponentsInLeague($id){
        if(\Request::wantsJson()) 
        {
            $opponents['opponents'] = $this->getLeagueOpponents($id);
            return response()->json(['result' => $opponents], $this->successStatus);
        }
    }

    // get opponents against item
    public function getLeagueOpponents($id)
    {      
        $item_ids = Categories::select('id')->where('parent_id' ,$id)->pluck('id');
        $records = Items::with(['user.country'])->whereIn('category_id',$item_ids)->groupBy('user_id')->get();
        $records->map(function ($record) use ($id,$item_ids) {
            $user_score = Items::select('score')->whereIn('category_id' , $item_ids)->where('user_id' , $record->user->id)->pluck('score');
            
            $record->score = str_pad($record->score,7,0,STR_PAD_LEFT);
            $record->avg_score = str_pad(ceil($user_score->avg()),7,0,STR_PAD_LEFT);
            $record->max_score = str_pad($user_score->max(),7,0,STR_PAD_LEFT);
            $record->min_score = str_pad($user_score->min(),7,0,STR_PAD_LEFT);
            $record->user_score = str_pad($user_score->sum(),7,0,STR_PAD_LEFT);

            $record->user->profile_thumbnail = checkImage('users/thumbs/'. $record->user->profile_image);
            $record->user->profile_image = checkImage('users/'. $record->user->profile_image);
            $record->user->records_count = str_pad($user_score->count(),7,0,STR_PAD_LEFT);
            $record->user->user_score = str_pad($record->user->user_score,7,0,STR_PAD_LEFT);


            return $record;
        });
        return $records;
    }

    public function getRecords($id){
        if(\Request::wantsJson()) 
        {   
            $item = Categories::with(['category', 'region'])->findOrFail($id);

            $result['records'] = $this->getItemRecords($id, $item->category_name, $item->description, $item->region);
            return response()->json(['result' => $result], $this->successStatus);
        }
    }

    public function getItemRecords($id, $item_name, $item_description, $region = ''){
        
        $records = Items::with(['user.country', 'record_images'])->where('category_id',$id)->get();
        /*$item_ids = Categories::select('id')->where('parent_id' ,$id)->pluck('id');
        $records = Items::with(['user.country', 'record_images'])->whereIn('category_id',$item_ids)->get();*/

        //$records['region'] = $region;
        $records->map(function ($user_image) use($item_name, $item_description, $region){
            $user_image['item_name'] = $item_name;
            $user_image['item_description'] = $item_description;
            $user_image['position_in_league'] = getPositionInLeagues($user_image->id);
            $user_image['region'] = $region;
            $user_image->score = str_pad($user_image->score,7,0,STR_PAD_LEFT);
            $user_image->user['profile_thumbnail'] = checkImage('users/thumbs/'. $user_image->user->profile_image);
            $user_image->user['profile_image'] = checkImage('users/'. $user_image->user->profile_image); 

            $user_image->user->user_score = str_pad($user_image->user->user_score,7,0,STR_PAD_LEFT);
            
            $user_image->record_images->map(function ($record_image) {
                $record_image['record_thumbnail'] = checkImage('items/thumbs/'. $record_image->name);
                $record_image['record_image'] = checkImage('items/'. $record_image->name); 
                return $record_image;
            });

            return $user_image;
        });

        return $records;
    }

    public function getRecordsInLeague($id){
        if(\Request::wantsJson()) 
        {               
            $item_ids = Categories::select('id')->where('parent_id' ,$id)->pluck('id');
            $records = Items::with(['category.region','user.country', 'record_images'])->whereIn('category_id',$item_ids)->get();

            $records->map(function ($record){
                
                $record['position_in_league'] = getPositionInLeagues($record->id);                
                $record->score = str_pad($record->score,7,0,STR_PAD_LEFT);
                $record->user['profile_thumbnail'] = checkImage('users/thumbs/'. $record->user->profile_image);
                $record->user['profile_image'] = checkImage('users/'. $record->user->profile_image); 

                $record->user->user_score = str_pad($record->user->user_score,7,0,STR_PAD_LEFT);

                $record->record_images->map(function ($record_image) {
                    $record_image['record_thumbnail'] = checkImage('items/thumbs/'. $record_image->name);
                    $record_image['record_image'] = checkImage('items/'. $record_image->name); 
                    return $record_image;
                });

                return $record;
            });
            
            $result['records'] = $records->all();
            return response()->json(['result' => $result], $this->successStatus);
        }
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
            'region_name' => 'required',                                         
            'league_name' => 'required',                                         
            'item_name' => 'required',                                         
            'description' => 'required',                                          
        ]);
        
        $requestData = $request->all();                   
        $requestData['region_id'] = $requestData['region_name'];              
        $requestData['parent_id'] = $requestData['league_name'];              
        $requestData['category_name'] = $requestData['item_name'];      
        
        $category = Categories::findOrFail($id);
        $category->update($requestData);        
        
        if(isset($requestData['image_ids'])){
          foreach($requestData['image_ids'] as $image_id){
                $category_images = Category_images::find($image_id);
                $category_images->category_id = $category->id;
                $category_images->update();
          }          
        }       
         
       // save category section
        for($s = 1; $s <= $requestData['total_sections']; $s++){                       
           $section_data = [];
           $section_data['category_id'] = $category->id;
           $section_data['id'] = $requestData['section_id_'. $s];           

           $section = Category_sections::firstOrNew($section_data);
           $section->section_name = $requestData['section_name_'. $s];
           $section->save();
           
           // save section questions
           $total_section_questions = $requestData['total_section_'. $s .'_questions'];
           if($total_section_questions > 0){
               for($sq = 1; $sq <= $total_section_questions; $sq++){  
                    $section_question_data = [];
                    $section_question_data['section_id'] = $section->id;
                    $section_question_data['id'] = $requestData['question_id_'.$s.'_'. $sq];   
                    
                    $question = Section_questions::firstOrNew($section_question_data);
                    $question->question_name = $requestData['question_name_'.$s.'_'. $sq];
                    $question->answer_type = $requestData['answer_type_'.$s.'_'. $sq];
                    $question->save();           
                    
                    // save section question answers
                    $total_question_answers = $requestData['total_section_'. $s .'_question_'.$sq.'_answers'];
                    if($total_question_answers > 0){
                        for($sqa = 1; $sqa <= $total_question_answers; $sqa++){  
                            $question_answer_data = [];
                            $question_answer_data['question_id'] = $question->id;                             
                            $question_answer_data['id'] = $requestData['answer_id_'.$s.'_'. $sq.'_'. $sqa];    
                    
                            $answer = Question_answers::firstOrNew($question_answer_data);
                            $answer->answer_name = $requestData['answer_name_'.$s.'_'. $sq.'_'. $sqa];
                            $answer->score = ($requestData['score_'.$s.'_'. $sq.'_'. $sqa]==""?0:$requestData['score_'.$s.'_'. $sq.'_'. $sqa]);
                            $answer->save();                                                         
                        }
                    }
               }
           }
           
        }
        
        Alert::success('Success Message', 'Item updated!');

        return redirect('admin/items');
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
        
        $categories = Categories::find($id);
        
        if($categories){
            $categories->delete();
            $response['success'] = 'Item deleted!';
            $status = $this->successStatus;  
        }else{
            $response['error'] = 'Item not exist against this id!';
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
    public function removeData($type, $id)
    {   
        if($type == 'section'){
            Category_sections::destroy($id);
        }elseif($type == 'question'){
            Section_questions::destroy($id);
        }elseif($type == 'answer'){
            Question_answers::destroy($id);
        }
        
        return response()->json(['success' => 1]);  
        
    }
    
    /**
    * Method : POST
    *
    * @return Post image and store in database
    */
    public function storeImage(Request $request)
    {
           
    	if($request->file('file') && $request->file('file')->isValid()){
                $destinationPath = 'uploads/items'; // upload path
                $image = $request->file('file'); // file
                $extension = $image->getClientOriginalExtension(); // getting image extension
                $fileName = str_random(12).'.'.$extension; // renameing image 
                
                $img = Image::make($image->getRealPath());
                $img->resize(100, 100, function ($constraint) {
                    $constraint->aspectRatio();
                })->save($destinationPath.'/thumbs/'.$fileName);
                
                $image->move($destinationPath, $fileName); // uploading file to given path
                
                //insert image record                
                $item_image['category_id'] = 0;
                $item_image['name'] = $fileName;
                
                $item_image = Category_images::create($item_image);  

    		if($item_image){
                    return response()->json(['id'=>$item_image->id,'name'=>$fileName]);
    		}else{
                    return response()->json(['message' => 'Error while saving image'],422);
    		}
    	}else{
    		return response()->json(['message' => 'Invalid image'],422);
    	}
    }
    
    /**
    * Method : DELETE
    *
    * @return delete images
    */
    public function deleteImage($id) 
    {
    	$image = Category_images::findOrFail($id);
        if ($image && count($image) > 0) {

            $file = public_path() . '/uploads/items/'.$image->name;
            $thumbFile = public_path() . '/uploads/items/thumbs/'.$image->name;
            if(is_file($file)){
                @unlink($file);
                @unlink($thumbFile);
            }

            $image->delete();
        }
                
        return response()->json(['success' => 1]);    
    }
    
    
    
}
