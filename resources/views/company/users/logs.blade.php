@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Employee Logs</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading"> Employee Logs </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Ip Address</th>
                                <th>Agent</th>
                                <th>Time</th>                       
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Ip Address</th>
                                <th>Agent</th>
                                <th>Time</th> 
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

<script>

    var table;
    $("document").ready(function () {
        var datatable_url = "{{ url('company/get-user-logs') }}"+ '/'+{{ $user_id }};
        var datatable_columns = [
            {data: 'name', name: 'name'},                 
            {data: 'description', name: 'description'},                 
            {data: 'ip', name: 'ip',width: '10%'},                 
            {data: 'agent', name: 'agent'},                                  
            {data: 'created_at', name: 'created_at',width: '17%',  orderable: false, searchable: false}
        ];
            
        create_datatables(datatable_url,datatable_columns);  
        
       var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
        
        $('#datatable').on('click', '.btn-delete', function (e) { 
            e.preventDefault();
            var id = $(this).attr('id');
            var url= "{{ url('company/products') }}"+'/'+id;
            var method = "delete";

            remove_record(url,reload_datatable,method);

            });
    }); //..... end of ready() .....//
</script>

@endsection