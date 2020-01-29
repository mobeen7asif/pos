@extends('company.layouts.app')

@section('css')
<style>
    .dataTables_length{float: left;}
    .dt-buttons{float: right; margin: 14px 0 0 0px;}
    div.dataTables_processing{top:55%;}
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
                    <li class="active">Shifts Report</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Shifts Report {{$user_name}}
                        
                        <span class="pull-right">
                            <div id="reportrange" class="pull-right report-range">
                                <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>&nbsp;
                                <span></span> <b class="caret"></b>
                            </div>
                        </span>
                        
                        {!! getStoreDropdownHtml() !!}
                    </header>
                    <div class="row">
                        <div class="col-sm-12">
                            <section class="panel">
                                <div class="panel-body">                                                
                                    <div id="stock_chart" ></div>
                                </div>
                            </section>
                        </div>
                    </div>  
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Transactions</th>
                                <th>Total</th>
                                <th>Expected</th>                                
                                <th>Variance</th>
                                <th>Working Hours</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Date</th>
                                <th>Name</th>
                                <th>Transactions</th>
                                <th>Total</th>
                                <th>Expected</th>                                
                                <th>Variance</th>
                                <th>Working Hours</th>
                                <th>Clock In</th>
                                <th>Clock Out</th>
                            </tr>
                          </tfoot>
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
<script type="text/javascript">
    var upload_url = '{{ asset("uploads") }}';
    
$(document).ready(function () {
    
    var start = moment().subtract(6, 'days');
    var end = moment();
    var user_id = {{$user_id}};
    var table = $('#datatable').DataTable({
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
        ordering: true,
        responsive: true,
        ajax: {
                  url: "{{url('company/reports/get-shift-report')}}",
                  data : function(d){
                          d.store_id = $("#store_reports option:selected").val(); 
                          d.from_date= start.format('YYYY/MM/DD');
                          d.to_date= end.format('YYYY/MM/DD');
                          d.user_id= user_id;                           
                      }
              },
        columns: [
              {data: 'date', name: 'date'},
              {data: 'name', name: 'name'},            
              {data: 'transaction_balance', name: 'transaction_balance', className: 'text-center'},
              {data: 'total_balances', name: 'total_balances', className: 'text-center'},
              {data: 'expected', name: 'expected', className: 'text-center'},
              {data: 'variance', name: 'variance', className: 'text-center'},
              {data: 'working_hours', name: 'working_hours', className: 'text-center'}, 
              {data: 'checkin_time', name: 'checkin_time', className: 'text-center'},
              {data: 'checkout_time', name: 'checkout_time', className: 'text-center'},                  
          ],
        order: [],
       /* footerCallback: function ( row, data, start, end, display ) {
            var api = this.api(), data;
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ? i.replace(/[\$,]/g, '')*1 : typeof i === 'number' ? i : 0;
            };
  
            salesTotal = api.column( 2, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            saleAmountTotal = api.column( 3, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            discountTotal = api.column( 4, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            tipTotalCash = api.column( 5, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            tipTotalCard = api.column( 6, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );
            taxTotal = api.column( 7, { page: 'current'} ).data().reduce( function (a, b) { return intVal(a) + intVal(b); }, 0 );


            durations = api.column( 8, { page: 'current'} ).data().reduce( function (a, b) { 
                if(a===0){
                    a='0:0';
                }
               
                var hour=0;
                var minute=0;
        
                var splitTime1= a.toString().split(':');
                var splitTime2= b.toString().split(':');
        
                hour = parseInt(splitTime1[0])+parseInt(splitTime2[0]);
                minute = parseInt(splitTime1[1])+parseInt(splitTime2[1]);
                hour = hour + minute/60;
                minute = minute%60;
                second = 0;
                minute = minute + second/60;
                second = second%60;

                return Math.floor(hour)+':'+Math.floor(minute);

             }, 0 );
 
            // Update footer
            $( api.column( 2 ).footer() ).html( '<b>'+salesTotal +'</b>' );
            $( api.column( 3 ).footer() ).html( '<b>'+saleAmountTotal +'</b>' );
            $( api.column( 4 ).footer() ).html( '<b>'+discountTotal +'</b>' );
            $( api.column( 5 ).footer() ).html( '<b>'+tipTotalCash +'</b>' );
            $( api.column( 6 ).footer() ).html( '<b>'+tipTotalCard +'</b>' );
            $( api.column( 7 ).footer() ).html( '<b>'+taxTotal.toFixed(2) +'</b>' );
            $( api.column( 8 ).footer() ).html( '<b>'+durations +'</b>' );
        }*/
      });        
        
    var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
                
        
    
    $(document).on('change', '#store_reports', function(){
        cb(start, end);
    });
    
    function cb(from_date, end_date) {
        $(".mini-stat").LoadingOverlay("show");
        start = from_date;
        end = end_date;        
        //graph(start,end);
        $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));        
        reload_datatable.fnDraw();
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
                          
