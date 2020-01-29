
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
                    'url' => ['/company/settings/update'],
                    'class' => 'form-horizontal',
                    'data-toggle' => 'validator',
                    'data-disable' => 'false',
                    'files' => true
                ]) !!}                            

                <div class="row">            
                    <div class="col-lg-12">
                        <section class="panel">
                        <header class="panel-heading">Settings</header>
                            <div class="panel-body">
                                <div class="position-center">

                                    <div class="form-group {{ $errors->has('currency_id') ? 'has-error' : ''}}">
                                        {!! Form::label('currency_id', 'Default Currency', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::select('currency_id', getCurrencyDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}
                                            {!! $errors->first('currency_id', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div> 

                                    <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                                        {!! Form::label('email', 'Default Email', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::email('email', null, ['class' => 'form-control','required' => 'required']) !!}
                                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                                        </div>
                                    </div>

                                     <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                                        {!! Form::label('store_id', 'Default Store', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::select('store_id', getStoresDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}
                                            {!! $errors->first('store_id', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div> 

                                    {{--<div class="form-group {{ $errors->has('tax_id') ? 'has-error' : ''}}">--}}
                                        {{--{!! Form::label('tax_id', 'Default Tax', ['class' => 'col-md-3 control-label required-input']) !!}--}}
                                        {{--<div class="col-md-9">--}}
                                            {{--{!! Form::select('tax_id', getTaxRatesDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}--}}
                                            {{--{!! $errors->first('tax_id', '<p class="help-block">:message</p>') !!}--}}
                                            {{--<div class="help-block with-errors"></div>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}
                                    
                                    <div class="form-group {{ $errors->has('shipping_id') ? 'has-error' : ''}}">
                                        {!! Form::label('shipping_id', 'Default Shipping', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            @php
                                             $shippings = \App\Shipping_option::where('company_id',Auth::id())->orderBy('cost','asc')->get();
                                            @endphp
                                            <select name="shipping_id" class="form-control select2" required>
                                                @foreach($shippings as $shipping)
                                                    <option value="{{$shipping->id}}" @if($setting->shipping_id == $shipping->id) selected @endif>{{$shipping->name.' ('.$shipping->cost.')'}}</option>
                                                @endforeach
                                            </select>



                                            {!! $errors->first('shipping_id', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group {{ $errors->has('sales_notifications') ? 'has-error' : ''}}">
                                        {!! Form::label('sales_notifications', 'Sales Notification', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::select('sales_notifications', ['1' => 'Yes', '0'=> 'No'],null, ['class' => 'form-control select2','required' => 'required']) !!}
                                            {!! $errors->first('sales_notifications', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    
                                    <div class="form-group {{ $errors->has('offline_mode') ? 'has-error' : ''}}">
                                        {!! Form::label('offline_mode', 'Offline Mode', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::select('offline_mode', ['1' => 'Enable', '0'=> 'Disable'],null, ['class' => 'form-control select2','required' => 'required']) !!}
                                            {!! $errors->first('offline_mode', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                        </div>
                                    </div>
                                    <div class="form-group {{ $errors->has('discount_status') ? 'has-error' : ''}}">
                                        {!! Form::label('discount_status', 'Discount Selection', ['class' => 'col-md-3 control-label required-input']) !!}
                                        <div class="col-md-9">
                                            {!! Form::select('discount_status', [ 'High'=> 'High Price','Low' => 'Low Price'],null, ['class' => 'form-control select2','required' => 'required']) !!}
                                            <span>If there are multiple discounts applying on same product, which discount should be chosen?</span>
                                            {!! $errors->first('discount_status', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
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
        
    });
</script>
@endsection

