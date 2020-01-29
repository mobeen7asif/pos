<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="images/favicon.png">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
        
    <title>Skulocity</title>

    <!--Core CSS -->
    <link href="{{ asset('bs3/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('css/bootstrap-reset.css') }}" rel="stylesheet">
    <link href="{{ asset('font-awesome/css/font-awesome.css') }}" rel="stylesheet" />

    <!-- Custom styles for this template -->
    <link href="{{ asset('css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('css/style-responsive.css') }}" rel="stylesheet" />
    

    <!-- Pace style -->
    <link rel="stylesheet" href="{{ asset('plugins/pace/pace.min.css') }}">
    
    @yield('css')
</head>
 <body class="login-body">
     <div class="container">
         
         @yield('content')      

    </div>                
    
     
    <!-- Scripts -->
<!--    <script type="text/javascript" src="{{ asset("js/jquery-1.11.1.min.js") }}"></script>-->
<!--    <script type="text/javascript" src="{{ asset("js/my_script.js") }}"></script>-->
    <!--Core js-->
    <script src="{{ asset("js/jquery.js") }}"></script>
    <script src="{{ asset("bs3/js/bootstrap.min.js") }}"></script> 
    <!-- PACE -->
    <script src="{{ asset("plugins/pace/pace.min.js") }}"></script>
    <!-- Common js-->
<!--    <script src="{{ asset('js/common.js') }}"></script>-->
    
    @include('sweet::alert')
    
    @yield('scripts')
</body>
</html>
