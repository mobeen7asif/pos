@extends('admin.layouts.app')

@section('content')
<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
        <h1>User Records</h1>
        <ol class="breadcrumb">
          <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
          <li class="active">User Records</li>
      </ol>
  </section>

  <!-- Main content -->
  <section class="content">
    <div class="row">
      <div class="col-xs-12">

        <div class="box">
          <div class="box-body">
            <table id="datatable" class="table table-bordered table-striped">
              <thead>
                <tr>
                  <th>Title</th>
                  <th>Total Comments</th>
                  <th>Details</th>
              </tr>
          </thead>
          <tbody>

          </tbody>
          <tfoot>
            <tr>
              <th>Title</th>
              <th>Total Comments</th>
              <th>Details</th>
          </tr>
      </tfoot>
  </table>
</div><!-- /.box-body -->
</div><!-- /.box -->
</div><!-- /.col -->
</div><!-- /.row -->
</section><!-- /.content -->
</div>
<!-- /.content-wrapper -->

@endsection


@section('scripts')

<script>

    var table;
    $("document").ready(function () {
        $('#datatable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{url('admin/get-user-items/'.$id.'')}}",
            columns: [
            {data: 'title', name: 'title'},
            {data: 'total_comments', name: 'total_comments'},
            {data: 'action', name: 'action', orderable: false, searchable: false}
            ]
        });   
        
        var $reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
       /* $('#datatable').on('click', '.item-detail', function (e) { 
            e.preventDefault();
            $.ajaxSetup({
                headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var id = $(this).attr('id');
            var url= "{{url('admin/users')}}"+'/'+id;

            // confirm then
            swal({
                title: "Are you sure!",
                type: "error",
                confirmButtonClass: "btn-danger",
                confirmButtonText: "Yes!",
                showCancelButton: true,
                },
                function() {
                    $.ajax({
                        type: "delete",
                        url: url,
                        success:function (res) {
                            if(res=="true"){
                              swal({title: "'User deleted succefully",
                              type: "success",
                              });
                              $reload_datatable.fnDraw();
                            }
                        }       
                    });
                }
            );  //..... end of swal.
        });*/ //..... end of btn-delete .....//
    }); //..... end of ready() .....//
</script>

@endsection