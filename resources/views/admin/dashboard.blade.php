@extends('admin.layouts.app')

@section('content')

<!--main content start-->
<section id="main-content">
<section class="wrapper">

<!--mini statistics start-->

<!--mini statistics end-->

<!--mini statistics start-->
<div class="row">
    <div class="col-md-3">
        <div class="mini-stat clearfix">
            <span class="mini-stat-icon orange"><i class="fa fa-home"></i></span>
            <div class="mini-stat-info">
                <span>{{$total_comapny}}</span>
                Companies
            </div>
        </div>
    </div>
</div>



</section>
</section>
<!--main content end-->

@endsection

@section('scripts')
    <script src="{{ asset('js/gauge/gauge.js') }}"></script>

    <script src="{{ asset('js/morris-chart/morris.js') }}"></script>

    <!--jQuery Flot Chart-->
    <script src="{{ asset('js/flot-chart/jquery.flot.js') }}"></script>
    <script src="{{ asset('js/flot-chart/jquery.flot.tooltip.min.js') }}"></script>
    <script src="{{ asset('js/flot-chart/jquery.flot.resize.js') }}"></script>
    <script src="{{ asset('js/flot-chart/jquery.flot.pie.resize.js') }}"></script>
    <script src="{{ asset('js/flot-chart/jquery.flot.animator.min.js') }}"></script>
    <script src="{{ asset('js/flot-chart/jquery.flot.growraf.js') }}"></script>
    <script src="{{ asset('js/dashboard.js') }}"></script>
@endsection
