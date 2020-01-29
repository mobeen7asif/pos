
<style>
    em {
        font-weight: bold;
        float: right;
    }

</style>@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                   <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li class="active">Settings</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
            
        {!! Form::model($setting, [
                    'method' => 'POST',
                    'url' => ['/company/duty/settings/update'],
                    'class' => 'form-horizontal',
                    'data-toggle' => 'validator',
                    'data-disable' => 'false',
                    'files' => true,
                    'id' => 'update_setting'
                ]) !!}                            

                <div class="row">            
                    <div class="col-lg-12">
                        <section class="panel">
                        <header class="panel-heading">Duty Settings</header>
                            <div class="panel-body">
                                <div class="position-center">

                                     <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                                        {!! Form::label('store_id', 'Default Store', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::select('store_id', getStoresDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}
                                            {!! $errors->first('store_id', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('ip') ? 'has-error' : ''}}">
                                        {!! Form::label('ip', 'Printer IP', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::text('ip', null, ['class' => 'form-control','placeholder'=>'IP','required' => 'required']) !!}
                                            {!! $errors->first('ip', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('logo') ? 'has-error' : ''}}">
                                        @if(isset($submitButtonText))
                                            {!! Form::label('logo', 'Logo', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                                        @else
                                            {!! Form::label('logo', 'Logo', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                                        @endif
                                        <div class="col-md-9">
                                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                                    @if (isset($setting)) @if(@$setting->logo != 'no_picture.jpg')  <a href="{{url('company/delete_image/duty_settings/'.@$setting->id).'/logo'}}" title="Delete Image"><i class="fa fa-trash action-padding"></i></a> @endif
                                                    @if(@$setting->logo != '')
                                                        <img src="{{ checkImage('duty_logos/'. @$setting->logo) }}" alt="" />
                                                    @else
                                                        <img src="{{ asset('uploads/no_image.png') }}" alt="" />
                                                    @endif
                                                        @else
                                                        <img src="{{ asset('uploads/no_image.png') }}" alt="" />
                                                    @endif
                                                </div>
                                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                                <div>

                                    <span class="btn btn-white btn-file">
                                    <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                    <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                    <input id="logo" type="file" class="default" name="logo" accept="image/*" />
                                    </span>
                                                    <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                                </div>
                                                {!! $errors->first('logo', '<p class="help-block">:message</p>') !!}
                                                <div class="help-block with-errors"></div>
                                                <div class="help-block with-errors" style="color: #a94442" id="background_image_error"></div>
                                            </div>
                                        </div>
                                    </div>

                                        <div class="form-group col-md-6 {{ $errors->has('detail') ? 'has-error' : ''}}">
                                            {!! Form::label('detail', 'Product Details', ['class' => 'control-label']) !!}
                                            {!! Form::textarea('detail', null, ['class' => 'form-control']) !!}
                                            {!! $errors->first('detail', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>

                                    <div class="form-group">
                                        <div class="col-lg-offset-2 col-lg-10">
                                            {!! Form::submit('Update', ['class' => 'btn btn-info pull-right']) !!}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </section>
                    </div>
                </div>

            {!! Form::close() !!}
            
    </section>
</section>

@endsection

@section('scripts')
    <!--<script type="text/javascript" src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>-->
    <script src="//cdn.ckeditor.com/4.8.0/full/ckeditor.js"></script>
<script type="text/javascript">
    $(document).ready(function(){
        CKEDITOR.replace( 'detail',{
            removePlugins: 'elementspath,magicline',
            resize_enabled: false,
            allowedContent: true,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            toolbar: [
                [ 'Bold','-','Italic','-','Underline'],
            ],
        });
    });

    $('#update_setting').submit(function() {
        var image = false;
        var back_image = false;
        var imgpath = document.getElementById('logo');
        if (!imgpath.value == ""){
            var img=imgpath.files[0].size;
            var imgsize=img/1024;
            if(imgsize > 2000){
                $('#background_image_error').html('Image size should be less than 2 MB');
                back_image = false;
            } else  {
                back_image = true;
            }
        } else {
            back_image = true;
        }
        if(back_image == true){
            return true;
        } else {
            return false;
        }
        // DO STUFF...
        //return false; // return false to cancel form action
    });

</script>
@endsection

