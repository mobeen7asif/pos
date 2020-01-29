@extends('admin.layouts.app')

@section('content')
<!-- image style -->

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Timeline
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{ url('admin/users') }}"> User</a></li>
        <li class="active">Timeline</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">

      <!-- row -->
      <div class="row">
          <a href="{{ url('admin/timeline/create/'.Request::segment(3)) }}" style="margin-right: 40px;" class="btn btn-success btn-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Create Timeline</a> 

        <div class="col-md-12">
          <!-- The time line -->
          <ul class="timeline">
              
            @php($current_year = 0)
            
            @forelse($timelines as $timeline)
            
            
            @php($date = explode('-',$timeline->date))
            
            
            <!-- timeline time label -->
            @if($current_year != $date[0])
            <li class="time-label">
                  <span class="bg-green">{{ $date[0] }}</span>
            </li>
            @php($current_year = $date[0])
            @endif
            <!-- /.timeline-label -->
            <!-- timeline item -->
            <li>
              @if($timeline->image == "")  
                    <i class="fa fa-comments bg-yellow"></i>
              @else
                    <i class="fa fa-camera bg-purple"></i>
              @endif
              
              <div class="timeline-item">
                  <span class="time"> {{ strtoupper(date('F Y', strtotime($timeline->date))) }} 
                        <a href="{{ url('admin/timeline/'.Hashids::encode($timeline->id).'/edit') }}"><i class="fa fa-pencil"></i></a> 
                        <a href="javascript:void(0)"><i class="fa fa-times btn-delete text-danger" data-id="{{ Hashids::encode($timeline->id) }}"></i></a>
                  </span>

                <h3 class="timeline-header"><a href="javascript:void(0)">{{ strtoupper($timeline->title) }}</a></h3>
                
                @if($timeline->image != "")  
                    <div class="timeline-body"> 
                        <img src="{{ checkImage('timeline/'. $timeline->image) }}" alt="..." class="margin"> 
                    </div>
                @endif
                
                
              </div>
            </li>
            <!-- END timeline item -->
            @empty
            <div style="margin-left: 35px;" class="alert alert-info">No timeline found</div>
            @endforelse 
          </ul>
        </div>
        <!-- /.col -->
      </div>

    </section>
    <!-- /.content -->
  </div>
<!-- /.content-wrapper -->

@endsection


@section('scripts')
<script>
$("document").ready(function () {
    $(document).on('click', '.btn-delete', function (e) { 
        e.preventDefault();

        var id = $(this).data('id');
        var url= "{{url('admin/timeline')}}"+'/'+id;

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
                          swal({title: "Timeline deleted succefully",
                          type: "success",
                          });
                          
                          location.reload();
                        }
                    }       
                });
            }
        );  //..... end of swal.
    }); //..... end of btn-delete .....//
}); 
</script>

@endsection