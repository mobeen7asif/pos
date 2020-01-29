
<style>
    .ms-drop ul > li label span em {
        font-weight: bold;
        float: right;
    }
    .ms-drop ul > li:hover {
        background-color: #f1f2f7;
    }
</style>

@extends('company.layouts.app')

@section('content')
    <section id="main-content" >
        <section class="wrapper">
            <div class="row">
                <div class="col-md-12">
                    <!--breadcrumbs start -->
                    <ul class="breadcrumb">
                        <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                        <li><a href="{{ url('company/tables').'/'.Hashids::encode($floor_id) }}">Tables</a></li>
                        <li class="active">Waiters</li>
                    </ul>
                    <!--breadcrumbs end -->
                </div>
            </div>

            {!! Form::open(['url' => 'company/tables/update_waiter', 'data-toggle' => 'validator', 'data-disable' => 'false', 'class' => 'form-horizontal', 'files' => true]) !!}

            <div class="row">
                <div class="col-lg-12">
                    <section class="panel">
                        {{--<header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Floor</header>--}}
                        <div class="panel-body">
                            <div class="position-center">

                                <div class="form-group {{ $errors->has('waiter_id') ? 'has-error' : ''}}">
                                    {!! Form::label('store_id', 'Waiter', ['class' => 'col-md-3 control-label required-input']) !!}
                                    <div class="col-md-9">
                                        <select name="waiter_id" class="form-control select2" required>
                                        @foreach($waiters as $waiter)
                                            <option value="{{$waiter->id}}">{{$waiter->name}}</option>
                                            @endforeach
                                        </select>
                                        {{--{!! Form::select('waiter_id', $waiters, null, ['class' => 'form-control select2','required' => 'required']) !!}--}}
                                        {!! $errors->first('waiter_id', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                                <input name="floor_id" type="hidden" value="{{$floor_id}}">
                                <div class="form-group {{ $errors->has('table_ids') ? 'has-error' : ''}}">
                                    {!! Form::label('category', 'Tables', ['class' => 'col-md-3 control-label required-input']) !!}
                                    <div class="col-md-9">
                                        <select name="table_ids[]" multiple="multiple" class="form-control required tables">
                                            @foreach($tables as $table)

                                                <option value="{{$table->id}}">{{$table->name}} <em>{{$table->waiter->name}}</em></option>
                                            @endforeach
                                        </select>
                                        {!! $errors->first('table_ids', '<p class="help-block">:message</p>') !!}

                                    </div>
                                </div>

                                @php
                                    $table_waiters = [];
                                    foreach ($tables as $table){
                                        $temp = [];
                                        $temp['table_name'] = $table->name;
                                        $temp['waiter_name'] = $table->waiter->name;
                                        $table_waiters[] = $temp;
                                    }
                                @endphp

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
    <script type="text/javascript" src="{{asset('plugins/multi-transfer/js/jquery.multi-select.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            var tables = <?php echo json_encode($table_waiters,true); ?>;

            $('.tables').multipleSelect();
            var i = 0;
            setTimeout(function(){
                $(".ms-drop ul li").each(function(){
                    if($(this).hasClass('ms-select-all') || $(this).hasClass('ms-no-results')){
                        //continue;
                    }else{
                        var table_data = tables[i];
                        var html = table_data.table_name+'<em>'+table_data.waiter_name+'</em>';
                        $(this).find('span').html(html);
                        i = i+1;
                    }

                })
            },500)

            console.log(tables);


        });
    </script>
@endsection