@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Email Templates</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Email Templates
                        {{--<span class="tools pull-right">--}}
                            {{--<a href="{{ url('company/floors/create') }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Add New Floor">--}}
                                {{--<i class="fa fa-plus" aria-hidden="true"></i> Add New--}}
                            {{--</a>--}}
                         {{--</span>--}}
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Email Name</th>
                                <th>Subject</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>                                
                                <th>Email Name</th>
                                <th>Subject</th>
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
        var datatable_url = "{{url('company/get-email-templates')}}";
        var datatable_columns = [
            {data: 'name', name: 'name'},
            {data: 'subject', name: 'subject'},
            {data: 'action', name: 'action', width: '10%', orderable: false, searchable: false}
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


    ///$("#datatable_length").append('{!! Form::select("type", getStoresFilterDropdown(), null, ["class" => "form-control input-sm filter_by_store","style"=>"margin-left: 20px;"]) !!}');

    var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );

    $(document).on('change', '.filter_by_store', function (e) {
        reload_datatable.fnDraw();
    });
});


</script>
@endsection                            
