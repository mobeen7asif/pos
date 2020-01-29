
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Category</header>
            <div class="panel-body">
                <div class="position-center">

                    @if(!isset($submitButtonText))
                 <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                    {!! Form::label('store_id', 'Store Name', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::select('store_id', getStoresDropdown(),null, ['class' => 'form-control','required' => 'required']) !!}
                        {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                    @endif
                 
                <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}}">
                    {!! Form::label('parent_id', 'Root Category', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">                                                 
                        {!! Form::select('parent_id', $categories, null, ['class' => 'form-control']) !!}                        
                        {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                </div>
                    
                  <div class="form-group {{ $errors->has('category_name') ? 'has-error' : ''}}">
                    {!! Form::label('category_name', 'Name', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::text('category_name', null, ['class' => 'form-control','placeholder'=>'Name','required' => 'required']) !!}
                        {!! $errors->first('category_name', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>

                  <div class="form-group {{ $errors->has('category_image') ? 'has-error' : ''}}">
                        @if(isset($submitButtonText))
                            {!! Form::label('category_image', 'Category Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}                        
                        @else
                            {!! Form::label('category_image', 'Category Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        @endif
                        <div class="col-md-9">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    @if(isset($submitButtonText)) @if($category->category_image != 'no_picture.jpg') <a href="{{url('company/delete_image/categories/'.$category->id).'/category_image'}}" title="Delete Image"><i class="fa fa-trash action-padding"></i></a> @endif @endif
                                    @if(@$category->category_image != '')
                                        <img src="{{ checkImage('categories/'. $category->category_image) }}" alt="" />
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="" />
                                    @endif

                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                <div>

                                    <span class="btn btn-white btn-file">
                                    <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                    <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                    <input type="file" class="default" name="category_image" accept="image/*" />
                                    </span>
                                    <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                </div>
                                {!! $errors->first('category_image', '<p class="help-block">:message</p>') !!}
                                <div class="help-block with-errors"></div>
                            </div>                        
                        </div>
                    </div>                                                                                
                   
                  <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
                        </div>
                    </div>  
                    
                </div>
            </div>
        </section>

    </div>
    
</div>

@section('scripts')
<script type="text/javascript">
    
    var category_select = $('#parent_id');  
    var default_data = [{ id: 0, text: 'Root Category' }];
    
    $(document).ready(function(){                
        
        var store_select = $('#store_id');          
        
        store_select.select2();
        
        category_select.select2();
         
        store_select.change(function(){            
            get_store_categories(this.value);
        }); 
        
        @if(@$category)
            store_select.change();            
        @endif    
        
    });
  
    function get_store_categories(store_id=""){

        if(store_id == ''){           
               category_select.select2('destroy').empty().select2({ data:default_data  });
        }else{

             category_select.select2({  
                data:default_data,  
                ajax: {
                  url: "{{url('company/get-store-categories')}}"+'/'+store_id,
                  dataType: 'json',
                  processResults: function (result) {
                    return {
                        results: result.results
                      };                                
              }
                }          
              });          
         }
    }
</script>
@endsection



