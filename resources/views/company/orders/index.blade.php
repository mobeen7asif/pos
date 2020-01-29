@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Sales</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Sales of {{$name}}
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Reference#</th>
                                <th>Store Name</th>
                                <th>Biller Name</th>
                                <th>Customer Name</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Type</th>
                                <th>Sub Total</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Date</th>
                                <th>Reference#</th>
                                <th>Store Name</th>
                                <th>Biller Name</th>
                                <th>Customer Name</th>
                                <th>Order Status</th>
                                <th>Payment Status</th>
                                <th>Type</th>
                                <th>Sub Total</th>
                                <th>Total</th>
                                <th>Action</th>
                            </tr>
                          </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        </div>   
                         
    </section>
</section>
      
@endsection


@section('scripts')
<script type="text/javascript">        
    var table;
$("document").ready(function () {
    var datatable_url = "{{url('company/get-sales')}}"+"/"+ <?=$id;?>+"/"+ <?=$type;?>;
    console.log(datatable_url);
    $('#datatable').DataTable({
            oLanguage: { sProcessing: '<img src="'+ base_url +'/images/bx_loader.gif">' },
            processing: true,
            serverSide: true,
            ajax: {
                url: datatable_url,
                data : function(d){                   
                    if($(".filter_by_store").val() != ''){
                        d.columns[2]['search']['value'] = $(".filter_by_store option:selected").text();                                                      
                    }                    
                    d.order_type = $(".filter_order_type option:selected").val();  
                    }
            }, 
            columns: [
                {data: 'date', name: 'date', width: "8%"},
                {data: 'reference', name: 'reference', width: "10%"},
                {data: 'store_name', name: 'store_name'},                 
                {data: 'biller_name', name: 'biller_name'},                 
                {data: 'customer', name: 'customer'},                 
                {data: 'order_status', name: 'order_status',width:'10%', className: 'text-center'}, 
                {data: 'payment_status', name: 'payment_status',width:'10%', className: 'text-center'}, 
                {data: 'order_type', name: 'order_type',width:'10%', className: 'text-center'}, 
                {data: 'sub_total', name: 'sub_total', width: "10%", className: 'text-center'},                 
                {data: 'order_total', name: 'order_total', width: "5%", className: 'text-center'},
                {data: 'action', name: 'action', width: "1%", orderable: false, searchable: false, className: 'text-center'}
            ],
            "order": []
        });   
        
        $("#datatable_length").append('{!! Form::select("type", getStoresFilterDropdown(), null, ["class" => "form-control input-sm filter_by_store","style"=>"margin-left: 20px;"]) !!}');                         
        $("#datatable_length").append('{!! Form::select("order_type", ["0"=>"Filter by type","1"=>"Sales","2"=>"Sales Return"], null, ["class" => "form-control input-sm filter_order_type","style"=>"margin-left: 20px;"]) !!}');                         
        
        var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
        
        $(document).on('change', '.filter_by_store', function (e) {
            reload_datatable.fnDraw();
        });  
        
        $(document).on('change', '.filter_order_type', function (e) {
            reload_datatable.fnDraw();
        });  
      });
</script>
@endsection                            
