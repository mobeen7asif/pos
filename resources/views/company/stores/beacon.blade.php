
@extends('company.layouts.app')

@section('content')

    @php
    if(isset($store->uid) || isset($store->uid) || isset($store->uid)){
        $submitButtonText = 'Update';
    } else {
    $submitButtonText = null;
    }
    @endphp

    <section id="main-content" >
        <section class="wrapper">
            <div class="row">
                <div class="col-md-12">
                    <!--breadcrumbs start -->
                    <ul class="breadcrumb">
                        <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                        <li><a href="{{ url('company/stores') }}">Stores</a></li>
                        <li class="active">Beacon</li>
                        <li class="active">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }}</li>
                    </ul>
                    <!--breadcrumbs end -->
                </div>
            </div>


            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Beacon</header>
                        <div class="panel-body">
                            <div class="position-center">
                                {!! Form::open(['url' => 'company/store/beacon/'.request()->route()->parameter('store_id'), 'data-toggle' => 'validator', 'data-disable' => 'false', 'class' => 'form-horizontal', 'files' => true]) !!}
                                <div class="form-group {{ $errors->has('uid') ? 'has-error' : ''}}">
                                    {!! Form::label('uid', 'Beacon UID', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                    <div class="col-lg-9">
                                        {!! Form::text('uid', $store->uid, ['class' => 'form-control','placeholder' => 'Beacon UID','required' => 'required']) !!}
                                        {!! $errors->first('uid', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <div class="form-group {{ $errors->has('major') ? 'has-error' : ''}}">
                                    {!! Form::label('major', 'Beacon Major', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                    <div class="col-lg-9">
                                        {!! Form::text('major', $store->major, ['class' => 'form-control','placeholder' => 'Beacon Major','required' => 'required']) !!}
                                        {!! $errors->first('major', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('minor') ? 'has-error' : ''}}">
                                    {!! Form::label('minor', 'Beacon Minor', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                    <div class="col-lg-9">
                                        {!! Form::text('minor', $store->minor, ['class' => 'form-control','placeholder' => 'Beacon Minor','required' => 'required']) !!}
                                        {!! $errors->first('minor', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-lg-offset-2 col-lg-10">
                                        {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </section>

                </div>
            </div>


            @section('scripts')
                <script type="text/javascript">
                    $(document).ready(function(){

                    });
                </script>
            @endsection



        </section>
    </section>


@endsection