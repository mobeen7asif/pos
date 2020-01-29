

@extends('company.layouts.app')

@section('content')
    <section id="main-content" >
        <section class="wrapper">
            <div class="row">
                <div class="col-md-12">
                    <!--breadcrumbs start -->
                    <ul class="breadcrumb">
                        <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                        <li><a href="{{ url('company/tables').'/'.$floor_id }}">Tables</a></li>
                        <li class="active">Update</li>
                    </ul>
                    <!--breadcrumbs end -->
                </div>
            </div>

            {!! Form::open(['url' => 'company/tables/update_table', 'data-toggle' => 'validator', 'data-disable' => 'false', 'class' => 'form-horizontal', 'files' => true]) !!}

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        {{--<header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Floor</header>--}}
                        <div class="panel-body">
                            <div class="position-center">

                                <div class="form-group {{ $errors->has('waiter_id') ? 'has-error' : ''}}">
                                    {!! Form::label('store_id', 'Waiter', ['class' => 'col-md-3 control-label required-input']) !!}
                                    <div class="col-md-9">
                                        {!! Form::select('waiter_id', $waiters,$table->waiter_id, ['class' => 'form-control select2','required' => 'required']) !!}
                                        {!! $errors->first('waiter_id', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <input name="tableId" type="hidden" value="{{Hashids::encode($table->id)}}">
                                <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                                    {!! Form::label('name', 'Table Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                    <div class="col-lg-9">
                                        {!! Form::text('name', $table->name, ['class' => 'form-control','placeholder' => 'Table Name','required' => 'required']) !!}
                                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>

                                <div class="form-group {{ $errors->has('seats') ? 'has-error' : ''}}">
                                    {!! Form::label('seats', 'Seats', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                                    <div class="col-lg-9">
                                        {!! Form::number('seats', $table->seats, ['class' => 'form-control','placeholder' => 'Seats','required' => 'required' ,'oninput'=> 'this.value = Math.abs(this.value)', 'min' => 1]) !!}
                                        {!! $errors->first('seats', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>


                                <div class="form-group">
                                    <div class="col-lg-offset-2 col-lg-10">
                                        {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Update', ['class' => 'btn btn-info pull-right']) !!}
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

        });
    </script>
@endsection