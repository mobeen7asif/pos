@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Email Template</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::model($email_template, [
                'method' => 'POST',
                'url' => ['/company/update-email-template'],
                'class' => 'form-horizontal',
                'files' => true,
                'data-toggle' => 'validator',
                'data-disable' => 'false',
                ]) !!}

                <div class="row">            
                    <div class="col-lg-12">
                        <section class="panel">
                            <header class="panel-heading">{{ @$email_template->name }}</header>
                            <div class="panel-body">
                                <div class="position-center"> 
                                    <input type="hidden" name="template_id" value="{{$email_template->id}}">
                                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                                        {!! Form::label('name', 'Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Name','required' => 'required']) !!}
                                            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>                                                                               
                                    
                                    <div class="form-group {{ $errors->has('subject') ? 'has-error' : ''}}">
                                        {!! Form::label('subject', 'Subject', ['class' => 'col-lg-3 col-sm-3 control-label  required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('subject', null, ['class' => 'form-control','placeholder' => 'Subject','required' => 'required']) !!}
                                            {!! $errors->first('subject', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>                                                                               
                                    
                                    <div class="form-group {{ $errors->has('from') ? 'has-error' : ''}}">
                                        {!! Form::label('from', 'From', ['class' => 'col-lg-3 col-sm-3 control-label  required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('from', null, ['class' => 'form-control','placeholder' => 'From','required' => 'required']) !!}
                                            {!! $errors->first('from', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>                                                                               
                            </div>
                                    <div class="form-group {{ $errors->has('content') ? 'has-error' : ''}}">
                                        {!! Form::label('content', 'Content', ['class' => 'col-lg-4 col-sm-3 control-label  required-input']) !!}
                                        <div class="col-lg-8">
                                            {!! Form::textarea('content', null, ['class' => 'form-control','placeholder' => 'Content','required' => 'required','rows'=>2]) !!}
                                            {!! $errors->first('content', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>                    

                                <div class="form-group">
                                    <div class="col-lg-offset-2 col-lg-8">
                                        {!! Form::submit( 'Update', ['class' => 'btn btn-info pull-right']) !!}
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
<script type="text/javascript" src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>
<script type="text/javascript">
    $(document).ready(function(){
        
        CKEDITOR.replace( 'content',{            
            removePlugins: 'elementspath,magicline',
            resize_enabled: false,
            allowedContent: true,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
        });
    });
</script>
@endsection