@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Employees</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Employees
                        <span class="tools pull-right">
                            <a href="{{ url('company/users/create') }}" class="btn btn-info btn-sm" title="Add New Employee">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add New
                            </a>
                         </span>
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Store Name</th>
                                <th>Email</th>
                                <th>Signup Date</th>                       
                                <th>Status</th>                       
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Image</th>
                                <th>Name</th>
                                <th>Store Name</th>
                                <th>Email</th>
                                <th>Signup Date</th>  
                                <th>Status</th>       
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

<script>

    var table;
    $("document").ready(function () {
        $('#datatable').DataTable({
            oLanguage: { sProcessing: '<img src="'+ base_url +'/images/bx_loader.gif">' },
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{url('company/get-users')}}",
                data : function(d){
                    if($(".filter_by_store").val() != ''){
                        d.columns[2]['search']['value'] = $(".filter_by_store option:selected").text();
                    }                    
                    }
            }, 
            columns: [
                {data: 'profile_image', name: 'profile_image', className: "my_class"},
                {data: 'name', name: 'name'},
                {data: 'store_name', name: 'store_name'},
                {data: 'email', name: 'email'},
                {data: "created_at", name: 'created_at'},
                {data: 'status', name: 'status', width: '10%', orderable: false, searchable: false},
                {data: 'action', name: 'action', width: '10%', orderable: false, searchable: false}
            ],
            "order": [[ 4, "asc"]]
        });   
        
        $("#datatable_length").append('{!! Form::select("type", getStoresFilterDropdown(), null, ["class" => "form-control input-sm filter_by_store","style"=>"margin-left: 20px;"]) !!}');
        
        var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
        
        $(document).on('change', '.filter_by_store', function (e) {
            reload_datatable.fnDraw();
        });
        
        $('#datatable').on('click', '.btn-delete', function (e) { 
            e.preventDefault();

            var id = $(this).attr('id');
            var url= "{{url('company/users')}}"+'/'+id;
            var method = "delete";
        
            remove_record(url,reload_datatable,method);
        }); //..... end of btn-delete .....//
    }); //..... end of ready() .....//
</script>

@endsection