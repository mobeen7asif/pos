@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                   <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li class="active">Profile</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
            
        {!! Form::model($profile, [
                    'method' => 'POST',
                    'url' => ['/company/profile/update'],
                    'class' => 'form-horizontal',
                    'data-toggle' => 'validator',
                    'data-disable' => 'false',
                    'files' => true
                ]) !!}                            

                <div class="row">            
                    <div class="col-lg-12">
                        <section class="panel">
                        <header class="panel-heading">Profile</header>
                            <div class="panel-body">
                                <div class="position-center">

                                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                                        {!! Form::label('name', 'Company Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Company Name','required' => 'required']) !!}
                                            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group">
                                        {!! Form::label('name', 'Company Email', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('', @$profile->email, ['class' => 'form-control','placeholder' => 'Company Email','disabled' => 'disabled']) !!}
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('country') ? 'has-error' : ''}}">
                                        {!! Form::label('country', 'Country', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::select('country', getCountries(),null, ['class' => 'form-control','required' => 'required']) !!}
                                            {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('state') ? 'has-error' : ''}}">
                                        {!! Form::label('state', 'State/Province', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('state', null, ['class' => 'form-control','placeholder' => 'State/Province Name','required' => 'required']) !!}
                                            {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
                                        {!! Form::label('city', 'City', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('city', null, ['class' => 'form-control','placeholder' => 'City Name','required' => 'required']) !!}
                                            {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('zip') ? 'has-error' : ''}}">
                                        {!! Form::label('zip', 'Zip Code', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::text('zip', null, ['class' => 'form-control','placeholder' => 'Zip Code','required' => 'required']) !!}
                                            {!! $errors->first('zip', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('address') ? 'has-error' : ''}}">
                                        {!! Form::label('address', 'Address', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::textarea('address', null, ['class' => 'form-control','placeholder' => 'Address','required' => 'required','rows'=>2]) !!}
                                            {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : ''}}">
                                        {!! Form::label('phone', 'Phone #', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::number('phone', null, ['class' => 'form-control','placeholder' => 'Phone #']) !!}
                                            {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group {{ $errors->has('mobile') ? 'has-error' : ''}}">
                                        {!! Form::label('mobile', 'Mobile #', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                                        <div class="col-lg-9">
                                            {!! Form::number('mobile', null, ['class' => 'form-control','placeholder' => 'Mobile #']) !!}
                                            {!! $errors->first('mobile', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>

                                    <div class="form-group last">
                                    {!! Form::label('logo', 'Company Logo', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}                        
                                    <div class="col-md-9">
                                        <div class="fileupload fileupload-new" data-provides="fileupload">
                                            <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                                @if(@$profile->logo != '')
                                                    <img src="{{ checkImage('companies/'. $profile->logo) }}" alt="" />
                                                @else
                                                    <img src="{{ asset('images/no-image.png') }}" alt="" />
                                                @endif

                                            </div>
                                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                            <div>
                                                <span class="btn btn-white btn-file">
                                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                                <input type="file" class="default" name="logo" accept="image/*" />
                                                </span>
                                                <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                            </div>
                                            {!! $errors->first('logo', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>                        
                                        </div>
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
<script type="text/javascript">
    $(document).ready(function(){
        $("#country").select2();        
    });
</script>
@endsection

