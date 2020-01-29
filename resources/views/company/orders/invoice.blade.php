@extends('company.layouts.app')

@section('css')
    <style>
        td.invoice ul li span{float: right;}
    </style>
@endsection

@section('content')

    @php
        $total_text = 'Due';
        if($order['payment_status'] == 2){
            $total_text = 'Paid';
        }
    @endphp

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
                                <div class="col-md-4 col-sm-4 pull-left">
                                    <h4>Invoice To:</h4>
                                    <h2>{{$order['store']['name']}}</h2>
                                    <p>
                                        {{$order['customers']['first_name']}} {{$order['customers']['last_name']}}<br>
                                        Address: {{$order['customers']['address']}} {{$order['customers']['city']}} {{$order['customers']['state']}}<br>
                                        Phone: {{$order['customers']['mobile']}}<br>
                                        Email : {{$order['customers']['email']}}
                                    </p>
                                </div>
                                <div class="col-md-4 col-sm-5 pull-right">
                                    <div class="row">
                                        <div class="col-md-3 col-sm-4 inv-label">Invoice #</div>
                                        <div class="col-md-8 col-sm-7">{{$order['reference']}}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-3 col-sm-4 inv-label">Date #</div>
                                        <div class="col-md-8 col-sm-7">{{ date('d-m-Y h:i a', strtotime($order['created_at'])) }}</div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12 inv-label">
                                            <h3>Total {{$total_text}}
                                                <span class="amnt-value">{{$order['store']['currency']['symbol']}}{{number_format($order['order_total'],2)}}</span>
                                            </h3>
                                        </div>
                                    </div>


                                </div>
                            </div>
                            <table class="table table-invoice" >
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Item Description</th>
                                    <th class="text-center">Unit Price</th>
                                    <th class="text-center">Quantity</th>
                                    <th class="text-center">Discount</th>
                                    <th class="text-center">Total</th>
                                </tr>
                                </thead>
                                <tbody>

                                @php
                                    //$jsonData = stripslashes(html_entity_decode($order['order_items']));
                                    $item_array = json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $order['order_items']), true );
                                    //$item_array = json_decode($jsonData,true);

                                if($item_array == null){
                                    $jsonData = stripslashes(html_entity_decode($order['order_items']));
                                    $item_array = json_decode($jsonData,true);

                                    if($item_array == null){
                                        $item_array = array();
                                    }
                                }
                                @endphp

                                @foreach($item_array as $single_item)
                                    @php
                                        $unit_price = $single_item['unit_price'];

                                        $item_modifiers = "";
                                        if(isset($single_item['item_modifiers'])){
                                            $item_modifiers = $single_item['item_modifiers'];
                                            $modifiers = json_decode($item_modifiers);
                                            foreach($modifiers as $modifier){
                                                $unit_price = $unit_price+$modifier->price;
                                            }
                                        }

                                        $item_sub_total = $unit_price * $single_item['quantity'];
                                        $item_discount = $single_item['item_discount'];
                                        //$item_sub_total = $item_sub_total - $item_discount;
                                        $item_combos = "";

                                        if(isset($single_item['item_combos']))
                                            $item_combos = $single_item['item_combos'];
                                    @endphp
                                    <tr>
                                        <td>{{ $loop->iteration }}</td>
                                        <td class="invoice">
                                            @if(isset($single_item['meal_type'])) <span style="background-color: <?php echo '#'.$single_item['meal_type']['color']; ?> ; font-size: 90%; float: right; font-weight: 700;line-height:1;white-space:nowrap;vertical-align:baseline;text-align: center;padding: 5px;border-radius: 3px;color: white; text-shadow: 2px 2px grey">{{$single_item['meal_type']['meal_type']}}</span> @endif
                                            <h5 class="amnt-value">{{$single_item['item_name']}}</h5>
                                            {!! getProductTagline($single_item['item_id'],$item_modifiers,$item_combos) !!}
                                        </td>
                                        <td class="text-center">{{$order['store']['currency']['symbol']}}{{number_format($unit_price,2)}}</td>
                                        <td class="text-center">{{$single_item['quantity']}}</td>
                                        <td class="text-center">{{$order['store']['currency']['symbol']}}{{number_format($item_discount,2)}}</td>
                                        <td class="text-center">{{$order['store']['currency']['symbol']}}{{number_format($item_sub_total,2)}}</td>
                                    </tr>

                                @endforeach


                                </tbody>
                            </table>
                            <div class="row">
                                <div class="col-md-8 col-xs-7 payment-method">
                                    <h4>Order Payments</h4>
                                    @php
                                        $payment_method = 'Cash';
                                        $payment_status = 'Pending';
                                        $payment_class = 'label-danger';
                                        if($order['payment_method'] == 2){
                                            $payment_method = 'Card';
                                        }
                                        if($order['payment_status'] == 1){
                                            $payment_status = 'Partial';
                                            $payment_class = 'label-info';
                                        }else if($order['payment_status'] == 2){
                                             $payment_status = 'Paid';
                                             $payment_class = 'label-success';
                                        }

                                        $order_type = '';
                                        $order_type_class = '';
                                        if($order['order_id'] == 0){
                                            $order_type = 'Sales';
                                            $order_type_class = 'label-success';
                                        }elseif($order['order_id'] != 0){
                                            $order_type = 'Sales Return';
                                            $order_type_class = 'label-danger';
                                        }

                                        $shipping_detail = json_decode($order['shipping_detail']);
                                    @endphp

                                    {{--<p>Payment Method : {{$payment_method}}</p>--}}
                                    {{--<p>Order Status : <span class="label {{$payment_class}}">{{$payment_status}}</span></p>--}}
                                    {{--<br/>--}}
                                    {{--<p>Order Type : <span class="label {{$order_type_class}}">{{$order_type}}</span></p>--}}

                                    <table class="table table-invoice" >
                                        <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Payment Method</th>
                                            <th class="text-center">Payment Amount</th>
                                            <th class="text-center">Tip</th>
                                            <th class="text-center">Payment Detail</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @php  $total_tip = 0; @endphp
                                        @foreach($order_payments as $payment)
                                            @php
                                                if($payment->payment_method == 1){
                                                    $payment->payment_method = 'Cash';
                                                } else {
                                                    $payment->payment_method = 'Card';
                                                }
                                                if($payment->payment_type == 1){
                                                    $payment->payment_type = 'Partial';
                                                } else {
                                                $payment->payment_type = 'Full';
                                                }

                                                $transaction_detail = json_decode($payment->transaction_detail,true);
                                           $total_tip = $total_tip + $payment->tip;
                                            @endphp

                                            <tr>
                                                <td class="text-center">{{$loop->iteration}}</td>
                                                <td class="text-center">{{$payment->payment_method}} {{'( '.$payment->payment_type.' )'}}</td>
                                                <td class="text-center">{{number_format($payment->payment_received,2)}}</td>
                                                <td class="text-center">{{number_format($payment->tip,2)}}</td>
                                                <td class="text-center">
                                                    @if(isset($transaction_detail))
                                                        @foreach($transaction_detail as $key => $value)
                                                            {{$key.': '}}  {{$value}}
                                                        @endforeach
                                                    @else
                                                        {{''}}
                                                    @endif

                                                </td>
                                            </tr>

                                        @endforeach


                                        </tbody>
                                    </table>

                                    <br/>
                                    <h4>Biller Detail</h4>
                                    @php
                                        //$biller_detail = json_decode($order['biller_detail'],true);
                                        if(isset($order['biller_detail'])){
                                                $biller = json_decode($order['biller_detail'],true);
                                                if(isset($biller['name'])){
                                                    $biller_name = $biller['name'];
                                                } else {
                                                    $biller_name = $biller['first_name'];
                                                }
                                                $biller_email = $biller['email'];
                                            } else {
                                                $biller_name = '';
                                                $biller_email = '';
                                            }
                                    @endphp

                                    <p>Name : {{$biller_name}}</p>
                                    <p>Email : {{$biller_email}}</p>

                                    <br>
                                    @php($table_information = json_decode($order['table_data'],true))

                                    <?php
                                    if(isset($table_information)){
                                    $waiter = \App\User::find($table_information['waiter_id']);
                                    if(!isset($waiter)){$waiter_name = "";} else {$waiter_name = $waiter->name;}
                                    ?>
                                    <h4>Table Information</h4>
                                    <p>Name : {{$table_information['table_info']['name']}}</p>
                                    <p>Waiter : {{$waiter_name}}</p>
                                    <p>Sitting Capacity : {{$table_information['table_info']['sit_cap']}}</p>

                                    <br>
                                    <?php } ?>


                                    @if(!empty($order['order_note']))
                                        <h4>Order Notes</h4>

                                        <div class="alert alert-danger" style="display: inline-block;">
                                            {{nl2br($order['order_note'])}}
                                        </div>
                                    @endif
                                </div>
                                <div class="col-md-4 col-xs-5 invoice-block pull-right">
                                    <ul class="unstyled amounts">
                                        <li>Sub Total : {{$order['store']['currency']['symbol']}}{{number_format($order['sub_total'],2)}}</li>
                                        <li>Shipping Cost : {{$order['store']['currency']['symbol']}}{{number_format(@$shipping_detail->cost,2)}} </li>
                                        <li>Service Fee : {{$order['store']['currency']['symbol']}}{{number_format($order['service_fee'],2)}} </li>
                                        <li>Discount : {{$order['store']['currency']['symbol']}}{{number_format($order['global_discount'],2)}} </li>
                                        <li>Tax : {{$order['store']['currency']['symbol']}}{{number_format($order['order_tax'],2)}} </li>
                                        @if($total_tip > 0) <li>Tip : {{number_format($total_tip,2)}} </li> @endif
                                        <li class="grand-total">Grand Total : {{$order['store']['currency']['symbol']}}{{number_format($order['order_total'],2)}}</li>
                                    </ul>
                                </div>
                            </div>



                        </div>
                    </section>
                </div>
            </div>



        </section>
    </section>

@endsection





