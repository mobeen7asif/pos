@extends('admin.layouts.app')

@section('css')
    <link rel="stylesheet" href="{{ asset('plugins/zabuto-calendar/zabuto_calendar.min.css') }}">
    
<style>
.present { background-color: #7ED321; }
.leave { background-color: #D0021B; }
.holidays { background-color: #4A90E2; }
/*div.zabuto_calendar tr.calendar-dow-header th, div.zabuto_calendar tr.calendar-dow td{border: 1px solid #dddddd;}*/
div.zabuto_calendar .table tr.calendar-dow-header th{background-color: #3c8dbc;color: white;}
.progress-bar-green { background-color: #7ED321; }
.progress-bar-aqua { background-color: #4A90E2; }
.progress-bar-red { background-color: #D0021B; }
</style>
@endsection

@section('content')
<!-- image style -->

<!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        Attendance
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
        <li><a href="{{ url('admin/users') }}"> User</a></li>
        <li class="active">Attendance</li>
      </ol>
    </section>

    <!-- Main content -->
    <section class="content">
        <a href="{{ url('admin/attendance/create/'.Request::segment(3)) }}" style="margin-bottom: 10px;" class="btn btn-success btn-sm pull-right"><i class="fa fa-plus" aria-hidden="true"></i> Add New</a> 
      <!-- row -->
      <div class="row">
        <div class="col-md-12">
            <div id="my-calendar"></div>
        </div>
       </div>
       
      <div class="row" style="margin-top: 60px;">
          
        <div class="col-md-4"></div>
         <div class="col-md-4">

            <div class="progress-group">
              <span class="progress-text">Present</span>
              <span class="progress-number"><b id="present">0</b></span>

              <div class="progress sm">
                <div class="progress-bar progress-bar-green" style="width: 0%"></div>
              </div>
            </div>
            <!-- /.progress-group -->
            <div class="progress-group">
              <span class="progress-text">Leaves</span>
              <span class="progress-number"><b id="leaves">0</b></span>

              <div class="progress sm">
                <div class="progress-bar progress-bar-red" style="width: 0%"></div>
              </div>
            </div>
            <!-- /.progress-group -->
            <div class="progress-group">
              <span class="progress-text">Holidays</span>
              <span class="progress-number"><b id="holidays">0</b></span>

              <div class="progress sm">
                <div class="progress-bar progress-bar-aqua" style="width: 0%"></div>
              </div>
            </div>            
            <!-- /.progress-group -->
          </div> 
        <!-- /.col -->
      </div>

    </section>
    <!-- /.content -->
  </div>
<!-- /.content-wrapper -->
    
@endsection


@section('scripts')
<script src="{{ asset('plugins/zabuto-calendar/zabuto_calendar.min.js') }}" ></script>
<script>
      
$("document").ready(function () {
    
    $("#my-calendar").zabuto_calendar({ 
        cell_border: true,        
        ajax:{
            url:"{{url('admin/get-attendance')}}" + '/' + "{{Request::segment(3)}}",
            modal: true
        },
        action_nav: function () {
            return navClicked();
        },
    });   
    
    navClicked();
    
}); 

    function navClicked(){
        setTimeout(function(){ 
            
            if($(".zabuto_calendar .event-styled .present").hasClass("holidays")){
                $(".zabuto_calendar .event-styled .present").removeClass("holidays")
            }
            
            var total_days_in_month = $(".zabuto_calendar .day").length;

            var total_days = $(".zabuto_calendar .event-styled").length;
            var present = $(".zabuto_calendar .event-styled .present").length;
            var leaves = $(".zabuto_calendar .event-styled .leave").length;
            var holidays = $(".zabuto_calendar .event-styled .holidays").length;

            $("#present").html(present);
            $("#leaves").html(leaves);
            $("#holidays").html(holidays);

            var present_percentage = present/total_days_in_month*100;
            var leaves_percentage = leaves/total_days_in_month*100;
            var holidays_percentage = holidays/total_days_in_month*100;

            $("#present").parents(".progress-group").find(".progress-bar").css("width",present_percentage+"%");
            $("#leaves").parents(".progress-group").find(".progress-bar").css("width",leaves_percentage+"%");
            $("#holidays").parents(".progress-group").find(".progress-bar").css("width",holidays_percentage+"%");

        }, 500);
    }
</script>

@endsection       