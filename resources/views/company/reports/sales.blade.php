@extends('company.layouts.app')

@section('css')
<style>
    .dataTables_length{float: left;}
    .dt-buttons{float: right; margin: 14px 0 0 0px;}
     div.dataTables_processing{top:55%;}
    .mini-stat{background: #f7f7f7;}
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
                    <li class="active">Sales Report</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Sales Report
                        
                        <span class="pull-right">
                            <div id="reportrange" class="pull-right report-range">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </span>
                        
                        {!! getStoreDropdownHtml() !!}
                    </header>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="mini-stat clearfix bg-aqua" data-toggle="tooltip" title="Total Revenue">
                                    <div class="mini-stat-info">
                                        <span id="revenue">0</span>
                                        REVENUE
                                    </div>
                                </div>
                            </div>  
                            <div class="col-md-2">
                                <div class="mini-stat clearfix">
                                    <div class="mini-stat-info" data-toggle="tooltip" title="" data-original-title="Number of sales in this time period">
                                        <span id="cost_of_goods">0</span>
                                        COST OF GOODS
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="mini-stat clearfix">
                                    <div class="mini-stat-info" data-toggle="tooltip" title="Number of unique registered customers served in a time period">
                                        <span id="gross_profit">0</span>
                                        GROSS PROFIT
                                    </div>
                                </div>
                            </div>                           
                            <div class="col-md-2">
                                <div class="mini-stat clearfix">
                                    <div class="mini-stat-info" data-toggle="tooltip" title="Total revenue less the cost of goods sold">
                                        <span id="margin">0</span>
                                        MARGIN %
                                    </div>
                                </div>
                            </div>                            
                            <div class="col-md-2">
                                <div class="mini-stat clearfix">
                                    <div class="mini-stat-info" data-toggle="tooltip" title="Total amount discounted for this time period">
                                        <span id="tax">0.00</span>
                                        TAX
                                    </div>
                                </div>
                            </div>                              
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <section class="panel">
                                    <div class="panel-body">                                                
                                        <div id="stock_chart" ></div>
                                    </div>
                                </section>
                            </div>
                        </div>  
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Revenue</th>
                                <th>Cost of Goods</th>
                                <th>Gross Profit</th>
                                <th>Margin %</th>
                                <th>Tax</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th class="text-right">Total</th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
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
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/dataTables.buttons.min.js" ></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.html5.min.js" ></script>
<script type="text/javascript" src="https://cdn.datatables.net/buttons/1.5.1/js/buttons.print.min.js" ></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script type="text/javascript">
    var table;
$("document").ready(function () {
    
    var start = moment().subtract(6, 'days');
    var end = moment();

    var datatable_url = "{{url('company/reports/get-sales-report')}}";

    $('#datatable').DataTable({
            dom: 'lBfrtip',
            buttons: [{
                text: '<span data-toggle="tooltip" title="Export CSV"><i class="fa fa-lg fa-file-text-o"></i> CSV</span>',
                extend: 'csv',
                className: 'btn btn-sm btn-round btn-success',
                title: 'Export CSV',
                extension: '.csv'
            },{
                text: '<span data-toggle="tooltip" title="Print"><i class="fa fa-lg fa-print"></i> Print</span>',
                extend: 'print',
                className: 'btn btn-sm btn-round btn-info',
            }],
            oLanguage: { sProcessing: '<img src="'+ base_url +'/images/bx_loader.gif">',sEmptyTable: 'No matching records found' },
            processing: true,
            serverSide: true,
            ajax: {
                url: datatable_url,
                data : function(d){
                    d.store_id = $("#store_reports option:selected").val();                       
                    d.from_date= start.format('YYYY/MM/DD');
                    d.to_date= end.format('YYYY/MM/DD');
                    }
            }, 
            columns: [
                {data: 'date', name: 'date', className: 'text-center'},
                {data: 'revenue', name: 'revenue', className: 'text-center'},             
                {data: 'cost_of_goods', name: 'cost_of_goods', className: 'text-center'},                 
                {data: 'gross_profit', name: 'gross_profit', className: 'text-center'},                 
                {data: 'margin', name: 'margin', className: 'text-center'}, 
                {data: 'order_tax', name: 'order_tax', className: 'text-center'},                 
            ],
            "order": [],
            footerCallback: function ( row, data, start, end, display ) {
                var api = this.api(), data;

                // Remove the formatting to get integer data for summation
                var intVal = function ( i ) {
                    return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
                };

                revenueTotal = api.column( 1, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
                cgTotal = api.column( 2, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
                gpTotal = api.column( 3, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
                marginTotal = api.column( 4, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
                taxTotal = api.column( 5, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );

                // Update footer
                $( api.column( 1 ).footer() ).html( '<b>'+revenueTotal +'</b>' );
                $( api.column( 2 ).footer() ).html( '<b>'+cgTotal +'</b>' );
                $( api.column( 3 ).footer() ).html( '<b>'+gpTotal +'</b>' );
                $( api.column( 4 ).footer() ).html( '<b>'+marginTotal.toFixed(2) +' %</b>' );
                $( api.column( 5 ).footer() ).html( '<b>'+taxTotal.toFixed(2) +'</b>' );
                
                $("#revenue").text(revenueTotal.toFixed(2));
                $("#cost_of_goods").text(cgTotal.toFixed(2));
                $("#gross_profit").text(gpTotal.toFixed(2));
                $("#margin").text(marginTotal.toFixed(2)+' %');
                $("#tax").text(taxTotal.toFixed(2));
                
                $(".mini-stat").LoadingOverlay("hide");
            }
        });   
        
    var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
                              
    
    $(document).on('change', '#store_reports', function(){
        cb(start, end);
    });
    
    function cb(from_date, end_date) {
        $(".mini-stat").LoadingOverlay("show");
        start = from_date;
        end = end_date;        
        graph(from_date,end_date);
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));        
        reload_datatable.fnDraw();
    } 


    function graph(from_date,end_date){
        start = from_date;
        end = end_date;
        var store_id = $('#store_reports option:selected').val();
        
        $.ajax({
            url :  '{{ url("company/reports/get-sale-graph") }}',
            type: 'post',
            data: 'store_id='+store_id+'&from_date='+start.format('YYYY/MM/DD')+'&to_date='+end.format('YYYY/MM/DD'),
            //data: {'from_date':start, 'to_date': end},
            success: function (result) { 
                console.log(result);
                var data = result.result;

                Highcharts.setOptions({ lang: { thousandsSep: ',' } });
                Highcharts.chart('stock_chart', {
                    title: {
                        text: 'Sales Graph'
                    },
                    credits: {enabled: false},
                    chart: {
                        type: 'column'
                    },
                    xAxis: {
                        categories:data.dates
                    },
                    yAxis: {
                        title: {
                            text: 'Amount'
                        }
                    },
                     legend: {
                        layout: 'vertical',
                        align: 'right',
                        verticalAlign: 'middle'
                    },

                    plotOptions: {
                        line: {
                            dataLabels: {
                                enabled: true
                            },
                        }
                    },
                    series: [{
                        name: 'Revenue',
                        data: data.dates_revenue
                    }, {
                        name: 'Profit',
                        data: data.dates_profit
                    }, {
                        name: 'Cost',
                        data: data.dates_cost_goods
                    }]
                });
        
                $(".retail-dashboard").LoadingOverlay("hide");
               
            }
        });
    }
    
    $('#reportrange').daterangepicker({
        startDate: start,
        endDate: end,
        ranges: {
           'Today': [moment(), moment()],
           'Yesterday': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
           'Last 7 Days': [moment().subtract(6, 'days'), moment()],
           'Last 30 Days': [moment().subtract(29, 'days'), moment()],
           'This Month': [moment().startOf('month'), moment().endOf('month')],
           'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
        }
    }, cb);

    cb(start, end);         
    
      });
      
    
</script>
@endsection                            
