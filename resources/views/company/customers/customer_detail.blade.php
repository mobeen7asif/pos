@extends('company.layouts.app')

@section('css')
    <style>
        td.invoice ul li span{float: right;}
    </style>
@endsection

@section('content')

    <section id="main-content" >
        <section class="wrapper">
            <div class="row">
                <div class="col-md-12">
                    <!--breadcrumbs start -->
                    <ul class="breadcrumb">
                        <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                        <li><a href="{{ url('company/sales') }}"> Sales</a></li>
                        <li class="active">Invoice</li>
                    </ul>
                    <!--breadcrumbs end -->
                </div>
            </div>

            <div class="row">
                <div class="col-md-12">
                    <section class="panel">
                        <div class="panel-body invoice">

                            <div class="row invoice-to">
                                {{--<div class="col-md-4 col-sm-4 pull-left">--}}
                                    {{--<h4>Invoice To:</h4>--}}
                                    {{--<h2>{{$order['store']['name']}}</h2>--}}
                                    {{--<p>--}}
                                        {{--{{$order['customers']['first_name']}} {{$order['customers']['last_name']}}<br>--}}
                                        {{--Address: {{$order['customers']['address']}} {{$order['customers']['city']}} {{$order['customers']['state']}}<br>--}}
                                        {{--Phone: {{$order['customers']['mobile']}}<br>--}}
                                        {{--Email : {{$order['customers']['email']}}--}}
                                    {{--</p>--}}
                                {{--</div>--}}
                                {{--<div class="col-md-4 col-sm-5 pull-right">--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-md-3 col-sm-4 inv-label">Invoice #</div>--}}
                                        {{--<div class="col-md-8 col-sm-7">{{$order['reference']}}</div>--}}
                                    {{--</div>--}}
                                    {{--<br>--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-md-3 col-sm-4 inv-label">Date #</div>--}}
                                        {{--<div class="col-md-8 col-sm-7">{{ date('d-m-Y h:i a', strtotime($order['created_at'])) }}</div>--}}
                                    {{--</div>--}}
                                    {{--<br>--}}
                                    {{--<div class="row">--}}
                                        {{--<div class="col-md-12 inv-label">--}}
                                            {{--<h3>Total {{$total_text}}--}}
                                                {{--<span class="amnt-value">{{$order['store']['currency']['symbol']}}{{number_format($order['order_total'],2)}}</span>--}}
                                            {{--</h3>--}}
                                        {{--</div>--}}
                                    {{--</div>--}}


                                {{--</div>--}}
                            </div>
                            <table class="table table-invoice" >
                                <thead>
                                <tr>
                                    <th>Name</th>
                                    <th class="text-center">Email</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Total</th>
                                </tr>
                                </thead>
                                <tbody>

                                    <tr>
                                        <td class="invoice">saf</td>
                                        <td class="text-center">{{}}</td>
                                        <td class="text-center">{{}}</td>
                                        <td class="text-center">{{}}</td>
                                        <td class="text-center">{{}}</td>
                                    </tr>



                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>
            </div>



        </section>
    </section>

@endsection





