@extends('admin.layouts.app')

@section('content')

<div class="content-wrapper">
    <section class="content-header">
        <h1>Settings</h1>
        <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>           
            <li class="active">Site Settings</li>
        </ol>
    </section>

    <section class="content">
        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="box box-info">

                    {!! Form::open(['url' => '/admin/settings/update', 'class' => 'form-horizontal', 'files' => true]) !!}

                    <div class="box-header with-border">
                        <h3 class="box-title">Site Settings</h3>
                    </div><!-- /.box-header -->                
                    <div class="box-body">

                        <div class="form-group {{ $errors->has('site_title') ? 'has-error' : ''}}">
                            {!! Form::label('site_title', 'Site Title', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('site_title', settingValue('site_title'), ['class' => 'form-control']) !!}
                                {!! $errors->first('site_title', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                            {!! Form::label('email', 'Email Address', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::email('email', settingValue('email'), ['class' => 'form-control']) !!}
                                {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <!-- Social Links -->

                        <div class="form-group {{ $errors->has('facebook') ? 'has-error' : ''}}">
                            {!! Form::label('facebook', 'Facebook Link', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('facebook', settingValue('facebook'), ['class' => 'form-control']) !!}
                                {!! $errors->first('facebook', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('twitter') ? 'has-error' : ''}}">
                            {!! Form::label('twitter', 'Twitter Link', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('twitter', settingValue('twitter'), ['class' => 'form-control']) !!}
                                {!! $errors->first('twitter', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('youtube') ? 'has-error' : ''}}">
                            {!! Form::label('youtube', 'You Tube', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('youtube', settingValue('youtube'), ['class' => 'form-control']) !!}
                                {!! $errors->first('youtube', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>

                        <div class="form-group {{ $errors->has('instagram') ? 'has-error' : ''}}">
                            {!! Form::label('instagram', 'Istagram Link', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('instagram', settingValue('instagram'), ['class' => 'form-control']) !!}
                                {!! $errors->first('instagram', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
            
            
                    </div><!-- /.box-body -->
                    <div class="box-footer"> 
                        {!! Form::submit('Update Settings', ['class' => 'btn btn-info pull-right']) !!}
                    </div><!-- /.box-footer -->

                    {!! Form::close() !!}

                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
                <div class="box box-info">

                    {!! Form::open(['url' => '/admin/settings/update', 'class' => 'form-horizontal', 'files' => true]) !!}

                    <div class="box-header with-border">
                        <h3 class="box-title">SEO Settings</h3>
                    </div><!-- /.box-header -->                
                    <div class="box-body">

                        <div class="form-group {{ $errors->has('meta_title') ? 'has-error' : ''}}">
                            {!! Form::label('meta_title', 'Meta Title', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('meta_title', settingValue('meta_title'), ['class' => 'form-control']) !!}
                                {!! $errors->first('meta_title', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('meta_keyword') ? 'has-error' : ''}}">
                            {!! Form::label('meta_keyword', 'Meta Keyord', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-md-6">
                                {!! Form::text('meta_keyword', settingValue('meta_keyword'), ['class' => 'form-control']) !!}
                                {!! $errors->first('meta_keyword', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
                        <div class="form-group {{ $errors->has('meta_description') ? 'has-error' : ''}}">
                            {!! Form::label('meta_description', 'Meta Description', ['class' => 'col-md-4 control-label']) !!}
                            <div class="col-sm-6">
                              {{ Form::textarea('meta_description', settingValue('meta_description'), ['class' => 'form-control meta_description', 'rows' => 2, 'cols' => 40]) }}
                              {!! $errors->first('meta_description', '<p class="help-block">:message</p>') !!}
                            </div>
                        </div>
        
        
                    </div><!-- /.box-body -->
                    <div class="box-footer"> 
                        {!! Form::submit('Update Settings', ['class' => 'btn btn-info pull-right']) !!}
                    </div><!-- /.box-footer -->

                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </section>      
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $.uploadPreview({
        input_field: "#image-upload",   // Default: .image-upload
        preview_box: "#image-preview",  // Default: .image-preview
        label_field: "#image-label",    // Default: .image-label
        label_default: "Choose Logo",   // Default: Choose File
        label_selected: "Change Logo",  // Default: Change File
        no_label: false                 // Default: false
        });

    });
</script>
@endsection

