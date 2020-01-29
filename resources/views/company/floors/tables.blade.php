@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/floors') }}">Floors</a></li>
                    <li class="active">Floor Tables</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Floor Tables
                        <span class="tools pull-right">
                            <a href="{{ url('company/floors/waiter_assign').'/'.request()->route()->parameter('id') }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Assign Waiters">
                                 Mass Assign Waiters
                            </a>
                         </span>
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Table ID</th>
                                <th>Table Name</th>
                                <th>Table Number</th>
                                <th>Waiter</th>
                                <th>Seats</th>
                                <th>Floor Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Table ID</th>
                                <th>Table Name</th>
                                <th>Table Number</th>
                                <th>Waiter</th>
                                <th>Seats</th>
                                <th>Floor Name</th>
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
$("document").ready(function () {
    var floor_id = "{{$floor_id}}";
        var datatable_url = "{{url('company/get-tables')}}"+'/'+floor_id;
        var datatable_columns = [
            {data: 'table_id', name: 'table_id'},
            {data: 'name', name: 'name'},
            {data: 'table_number', name: 'table_number'},
            {data: 'waiter', name: 'waiter'},
            {data: 'seats', name: 'seats',width : '10%'},
            {data: 'floor_name', name: 'floor_name'},
            {data: 'action', name: 'action', width: '15%', orderable: false, searchable: false}
            ];

    $('#datatable').DataTable({
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

    var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );

        $('#datatable').on('click', '.btn-delete', function (e) {
        e.preventDefault();
        var id = $(this).attr('id');
        var url= "{{ url('company/floors') }}"+'/'+id;
        var method = "delete";

        remove_record(url,reload_datatable,method);

        });


    //$("#datatable_length").append('{!! Form::select("type", getStoresFilterDropdown(), null, ["class" => "form-control input-sm filter_by_store","style"=>"margin-left: 20px;"]) !!}');

    var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );

    $(document).on('change', '.filter_by_store', function (e) {
        reload_datatable.fnDraw();
    });
});


</script>
@endsection                            
