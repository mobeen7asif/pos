<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
    <!--Core CSS -->
    <link href="{{ asset('bs3/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('js/jquery-ui/jquery-ui-1.10.1.custom.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-reset.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet">
    <link href="{{ asset('js/jvector-map/jquery-jvectormap-1.2.2.css') }}" rel="stylesheet">
    <link href="{{ asset('css/clndr.css') }}" rel="stylesheet">
    <!--clock css-->
    <link href="{{ asset('js/css3clock/css/style.css') }}" rel="stylesheet">
    <!--Morris Chart CSS -->
    <link rel="{{ asset('stylesheet" href="js/morris-chart/morris.css') }}">   
    
    <link rel="stylesheet" href="{{ asset('css/bootstrap-switch.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/bootstrap-fileupload/bootstrap-fileupload.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/bootstrap-wysihtml5/bootstrap-wysihtml5.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/bootstrap-datepicker/css/datepicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/bootstrap-timepicker/compiled/timepicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/bootstrap-colorpicker/css/colorpicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/bootstrap-daterangepicker/daterangepicker-bs3.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/bootstrap-datetimepicker/css/datetimepicker.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/jquery-multi-select/css/multi-select.css') }}" />
    <link rel="stylesheet" type="text/css" href="{{ asset('js/jquery-tags-input/jquery.tagsinput.css') }}" />
    
    <!-- select2 -->
    <link rel="stylesheet" type="text/css" href="{{ asset('js/select2/select2.css') }}" />
    
    
    <!-- Custom styles for this template -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style-responsive.css') }}" rel="stylesheet"/> 
    <!-- Just for debugging purposes. Don't actually copy this line! -->
    <!--[if lt IE 9]>
    <script src="js/ie8-responsive-file-warning.js"></script><![endif]-->
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/libs/html5shiv/3.7.0/html5shiv.js"></script>
    <script src="https://oss.maxcdn.com/libs/respond.js/1.3.0/respond.min.js"></script>
    <![endif]-->
    
    
    <!-- Bootstrap 3.3.5 -->
<!--    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">-->
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">
    <!-- DataTables -->
    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">  
    <!-- jvectormap -->
    <link rel="stylesheet" href="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">
     <!-- Styles -->
<!--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
     <!-- Sweet Alert -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
    <!-- Choose a skin from the css/skins  -->
<!--    <link rel="stylesheet" href="{{ asset('css/skins/_all-skins.min.css') }}">-->
    <!-- Pace style -->
    <link rel="stylesheet" href="{{ asset('plugins/pace/pace.min.css') }}">
    <!-- Bootstrap datatables -->
    <link href="{{ asset('css/datatables.bootstrap.css') }}" rel="stylesheet">   
     
    @yield('css')
    
    <title>{{ settingValue('site_title') }}</title>
   
    <script>
        var base_url = '{{ url("") }}';
        var panel_url = '{{ url("admin") }}';
    </script>
    
</head>
<body>
    
    <section id="container">
       
        <!-- Admin Header -->
        @include('admin.sections.header')
        <!-- Admin Sidebar -->
        @include('admin.sections.sidebar')
        
        @yield('content')
        
        <!-- Admin Footer -->
        @include('admin.sections.footer')
        
    </section>
    <!-- Scripts -->
     <!--Core js-->
    <script src="{{ asset('js/jquery.js') }}"></script>
    <script src="{{ asset('js/jquery-1.8.3.min.js') }}"></script>
    <script src="{{ asset('js/jquery-ui/jquery-ui-1.10.1.custom.min.js') }}"></script>
    <script src="{{ asset('bs3/js/bootstrap.min.js') }}"></script>
    <script src="{{ asset('js/jquery.dcjqaccordion.2.7.js') }}"></script>
    <script src="{{ asset('js/jquery.scrollTo.min.js') }}"></script>
    <script src="{{ asset('js/jQuery-slimScroll-1.3.0/jquery.slimscroll.js') }}"></script>
    <script src="{{ asset('js/jquery.nicescroll.js') }}"></script>
    <script src="{{ asset('js/bootstrap-switch.js') }}"></script>
    <!--[if lte IE 8]><script language="javascript" type="text/javascript" src="js/flot-chart/excanvas.min.js"></script><![endif]-->
    <script src="{{ asset('js/skycons/skycons.js') }}"></script>
    <script src="{{ asset('js/jquery.scrollTo/jquery.scrollTo.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jquery-easing/1.3/jquery.easing.min.js"></script>
    <script src="{{ asset('js/calendar/clndr.js') }}"></script>
    <script src="http://cdnjs.cloudflare.com/ajax/libs/underscore.js/1.5.2/underscore-min.js"></script>
    <script src="{{ asset('js/calendar/moment-2.2.1.js') }}"></script>
    <script src="{{ asset('js/evnt.calendar.init.js') }}"></script>
    <script src="{{ asset('js/jvector-map/jquery-jvectormap-1.2.2.min.js') }}"></script>
    <script src="{{ asset('js/jvector-map/jquery-jvectormap-us-lcc-en.js') }}"></script>
    <!--clock init-->
    <script src="{{ asset('js/css3clock/js/css3clock.js') }}"></script>
    <!--Easy Pie Chart-->
    <script src="{{ asset('js/easypiechart/jquery.easypiechart.js') }}"></script>
    <!--Sparkline Chart-->
    <script src="{{ asset('js/sparkline/jquery.sparkline.js') }}"></script>
    <!--Morris Chart-->
    <script src="{{ asset('js/morris-chart/raphael-min.js') }}"></script>

    <script src="{{ asset('js/jquery.customSelect.min.js') }}" ></script>
    <!--common script init for all pages-->
    <script src="{{ asset('js/scripts.js') }}"></script>
    <!--script for this page-->
<!--    <script src="{{ asset('js/app.js') }}"></script>-->
    <!-- jQuery 2.1.4 -->
<!--    <script src="{{ asset('plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>-->
    <!-- Bootstrap 3.3.5 -->
<!--    <script src="{{ asset('js/bootstrap.min.js') }}"></script>-->
    <!-- Sweet Alert -->
    <script src="{{ asset("js/sweetalert.min.js") }}"></script>
    <!-- DataTables -->
    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>
    <!-- AdminLTE App -->
    <script src="{{ asset('js/app.min.js') }}"></script>
    <!-- datetimepicker-->
    <script src="{{ asset("js/moment.min.js") }}"></script>    
    <!-- PACE -->
    <script src="{{ asset("plugins/pace/pace.min.js") }}"></script>
    <!-- Common js-->
    <script src="{{ asset('js/common.js') }}"></script>
    <!-- Bootstrap Validator-->
    <script src="{{ asset('js/validator.min.js') }}"></script>       
    
    <script type="text/javascript" src="{{ asset('js/fuelux/js/spinner.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-fileupload/bootstrap-fileupload.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-wysihtml5/wysihtml5-0.3.0.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-wysihtml5/bootstrap-wysihtml5.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datepicker/js/bootstrap-datepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-datetimepicker/js/bootstrap-datetimepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-daterangepicker/moment.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-daterangepicker/daterangepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-colorpicker/js/bootstrap-colorpicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-timepicker/js/bootstrap-timepicker.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-multi-select/js/jquery.multi-select.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/jquery-multi-select/js/jquery.quicksearch.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/bootstrap-inputmask/bootstrap-inputmask.min.js') }}"></script>
    <script src="{{ asset('js/jquery-tags-input/jquery.tagsinput.js') }}"></script>

    <!-- Select 2-->
    <script type="text/javascript" src="{{ asset('js/select2/select2.js') }}"></script>
    
    @include('company.sections.notification')
    
    @include('sweet::alert')
    
    @yield('scripts')
    
    
    
</body>
</html>
