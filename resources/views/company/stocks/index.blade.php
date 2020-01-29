@extends('company.layouts.app')

@section('css')
    <style>
        table tbody tr{cursor: pointer;}
        .modal { text-align: center; padding: 0!important; }
        .modal:before { content: ''; display: inline-block; height: 100%; vertical-align: middle; margin-right: -4px; }
        .modal-dialog { display: inline-block; text-align: left; vertical-align: middle; }
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
                    <li class="active">Stock Adjustments</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Stock Adjustments
                        <span class="tools pull-right">
                            <a href="{{ url('company/manage-stocks/create') }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Add New Adjustment">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Adjustment
                            </a>
                         </span>
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Store Name</th>
                                <th>Product Name</th>
                                <th>Type</th>
                                <th>Task</th>
                                <th>Stock Quantity</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Date</th>
                                <th>Store Name</th>
                                <th>Product Name</th>
                                <th>Type</th>
                                <th>Task</th>
                                <th>Stock Quantity</th>
                            </tr>
                          </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        </div>   
            
    </section>
</section>
   
<div aria-hidden="true" aria-labelledby="myModalLabel" role="dialog" tabindex="-1" id="stock_model" class="modal fade">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button aria-hidden="true" data-dismiss="modal" class="close" type="button">×</button>
                <h4 class="modal-title">Stock Adjustment</h4>
            </div>
            <div class="modal-body">

                <form class="form-horizontal" role="form">
                    <div class="form-group">
                        <label class="col-lg-4 col-sm-4 control-label">Date:</label>
                        <div class="col-lg-8">
                            <p class="form-control-static" id="stock_date"></p>
                        </div>
                    </div>                           
                    <div class="form-group">
                        <label class="col-lg-4 col-sm-4 control-label">Store Name:</label>
                        <div class="col-lg-8">
                            <p class="form-control-static" id="stock_store"></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-4 col-sm-4 control-label">Product Name:</label>
                        <div class="col-lg-8">
                            <p class="form-control-static" id="product_name"></p>
                        </div>
                    </div>                           
                    <div class="form-group">
                        <label class="col-lg-4 col-sm-4 control-label">Type:</label>
                        <div class="col-lg-8">
                            <p class="form-control-static" id="stock_type"></p>
                        </div>
                    </div>                           
                    <div class="form-group">
                        <label class="col-lg-4 col-sm-4 control-label">Task:</label>
                        <div class="col-lg-8">
                            <p class="form-control-static" id="stock_task"></p>
                        </div>
                    </div>                           
                    <div class="form-group">
                        <label class="col-lg-4 col-sm-4 control-label">Stock Quantity:</label>
                        <div class="col-lg-8">
                            <p class="form-control-static" id="stock_quantity"></p>
                        </div>
                    </div>              
                    <div class="form-group">
                        <label class="col-lg-4 col-sm-4 control-label">Note:</label>
                        <div class="col-lg-8">
                            <p class="form-control-static" id="stock_note"></p>
                        </div>
                    </div>                                                      

                </form>

            </div>

        </div>
    </div>
</div>

@endsection


@section('scripts')
<script type="text/javascript">
$("document").ready(function () {
    var datatable_url = "{{url('company/get-stocks')}}";
    var datatable_columns = [
        {data: 'created_at', name: 'created_at', width: '15%'},
        {data: 'store_name', name: 'store_name'},
        {data: 'product_name', name: 'product_name'},
        {data: 'stock_type', name: 'stock_type', width: '10%'},
        {data: 'origin', name: 'origin', width: '15%'},
        {data: 'quantity', name: 'quantity', width: '15%'},
//            {data: 'action', name: 'action', width: '10%', orderable: false, searchable: false}
        ];
            
    var table = $('#datatable').DataTable({
            oLanguage: { sProcessing: '<img src="'+ base_url +'/images/bx_loader.gif">' },
            processing: true,
            serverSide: true,
            ajax: {
                url: datatable_url,
                data : function(d){
                    if($(".filter_by_store").val() != ''){
                        d.columns[1]['search']['value'] = $(".filter_by_store option:selected").text();
                    }                    
                    }
            }, 
            columns: datatable_columns,
            "order": []
        });   
        
    $('#datatable tbody').on('click', 'tr', function () 
    {
        var tr = $(this);
        var row = table.row( tr );
        var stock = row.data();
        
        console.log(stock);
        
        $("#stock_date").text(stock.created_at);
        $("#stock_store").text(stock.store_name);
        $("#product_name").text(stock.product_name);
        $("#stock_type").text(stock.stock_type);
        $("#stock_task").text(stock.origin);
        $("#stock_quantity").text(stock.quantity);
        
        if(stock.note){
            $("#stock_note").parents('.form-group').show();
            $("#stock_note").text(stock.note);
        }else
            $("#stock_note").parents('.form-group').hide();
        
        $('#stock_model').modal({show:true});        
        
    });
        
        $("#datatable_length").append('{!! Form::select("type", getStoresFilterDropdown(), null, ["class" => "form-control input-sm filter_by_store","style"=>"margin-left: 20px;"]) !!}');
        
        var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
        
        $(document).on('change', '.filter_by_store', function (e) {
            reload_datatable.fnDraw();
        });
        
           
      });            
</script>
@endsection                            
