@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Suppliers</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Suppliers
                        <span class="tools pull-right">
                            <a href="{{ url('company/suppliers/create') }}" class="btn btn-info btn-sm" title="Add New Supplier">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add New
                            </a>
                         </span>
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Image</th>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Image</th>
                                <th>Code</th>
                                <th>Name</th>
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
        var datatable_url = "{{url('company/get-suppliers')}}";
        var datatable_columns = [
            {data: 'image', name: 'image',width: '15%'},
            {data: 'code', name: 'code'},
            {data: 'name', name: 'name'},
            {data: 'action', name: 'action', width: '10%',orderable: false, searchable: false}
            ];
            
            create_datatables(datatable_url,datatable_columns);
           
        var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );

        $('#datatable').on('click', '.btn-delete', function (e) { 
        e.preventDefault();
        var id = $(this).attr('id');
        var url= "{{ url('company/suppliers') }}"+'/'+id;
        var method = "delete";
        
        remove_record(url,reload_datatable,method);
        
        }); 
      });            
</script>
@endsection                            
