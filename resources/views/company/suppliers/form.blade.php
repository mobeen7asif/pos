
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Supplier</header>
            <div class="panel-body">
                <div class="position-center">                    
                    
                    <div class="form-group {{ $errors->has('code') ? 'has-error' : ''}}">
                        {!! Form::label('code', 'Code', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::number('code', null, ['class' => 'form-control','placeholder' => 'Code','required' => 'required','min' => '0']) !!}
                            {!! $errors->first('code', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>                                                                                                                      
                    
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                        {!! Form::label('name', 'Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Name','required' => 'required']) !!}
                            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                        {!! Form::label('email', 'Email', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('email', null, ['class' => 'form-control','placeholder' => 'Email']) !!}
                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
                    @if(isset($submitButtonText))
                        {!! Form::label('image', 'Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}                        
                    @else
                        {!! Form::label('image', 'Image', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}                        
                    @endif
                    <div class="col-md-9">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                @if(@$supplier->image != '')
                                    <img src="{{ checkImage('suppliers/'. @$supplier->image) }}" alt="" />
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" alt="" />
                                @endif
                                
                            </div>
                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                            <div>
                                <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                <input id="image" type="file" class="default" name="image" accept="image/*" />
                                </span>
                                <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                            </div>
                            {!! $errors->first('image', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors" style="color: #a94442" id="background_image_error"></div>
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
    $(document).ready(function(){
        
    });
    $('#submit_supplier').submit(function() {
        var image = false;
        var imgpath = document.getElementById('image');
        if (!imgpath.value == ""){
            var img=imgpath.files[0].size;
            var imgsize=img/1024;
            if(imgsize > 2000){
                $('#background_image_error').html('Image size should be less than 2 MB');
                image = false;
            } else  {
                image = true;
            }
        } else {
            image = true;
        }
        if(image == true){
            return true;
        } else {
            return false;
        }
        // DO STUFF...
        return false; // return false to cancel form action
    });
</script>
@endsection