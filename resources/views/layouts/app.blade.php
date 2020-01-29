<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="{{settingValue('meta_title')}}" content="{{settingValue('meta_description')}}">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="apple-touch-icon" sizes="180x180" href="images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="images/favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="images/favicon-16x16.png">
    <link rel="manifest" href="images/manifest.json">
    <link rel="mask-icon" href="images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">
        
    <title>{{ settingValue('site_title') }}</title>

    <!-- Styles -->
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/style-dev.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/responsive.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/font-awesome.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/animations.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/jquery.bxslider.css') }}">
    <link rel="stylesheet" type="text/css" href="{{ asset('css/owl.carousel.css') }}">
     <link rel="stylesheet" type="text/css" href="{{ asset('css/owl.theme.css') }}">
    <!-- Bootstrap 3.3.5 -->
<!--    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">-->
<!--     Font Awesome -->
<!--    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css">-->
<!--     Ionicons -->
<!--    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css">-->
<!--     DataTables -->
<!--    <link rel="stylesheet" href="{{ asset('plugins/datatables/dataTables.bootstrap.css') }}">-->
<!--     jvectormap -->
<!--    <link rel="stylesheet" href="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.css') }}">-->
<!--      Styles -->
<!--    <link href="{{ asset('css/app.css') }}" rel="stylesheet">-->
<!--      Sweet Alert -->
    <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
<!--     Choose a skin from the css/skins  -->
<!--    <link rel="stylesheet" href="{{ asset('css/skins/_all-skins.min.css') }}">-->
<!--     Pace style -->
    <link rel="stylesheet" href="{{ asset('plugins/pace/pace.min.css') }}">
<!--     Bootstrap datatables -->
<!--    <link href="{{ asset('css/datatables.bootstrap.css') }}" rel="stylesheet">-->
    
    @yield('css')
</head>
<body>
    <div id="wrapper">
       
        <!-- Header -->
        @include('sections.header')

       <div class="contant">
           @yield('content')
       </div>
       
        <!-- Footer -->
        @include('sections.footer')
        
    </div>

    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset("js/jquery-1.11.1.min.js") }}"></script>    
    <script type="text/javascript" src="{{ asset("js/css3-animate-it.js") }}"></script> 
    <script type="text/javascript" src="{{ asset("js/animations-ie-fix.js") }}"></script> 
    <script type="text/javascript" src="{{ asset("js/paralex_custom.js") }}"></script> 
    <script type="text/javascript" src="{{ asset("js/jquery.bxslider.js") }}"></script>
    <script type="text/javascript" src="{{ asset("js/owl.carousel.min.js") }}"></script>
        
    <!-- overlay-->
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/src/loadingoverlay.min.js"></script>
    <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/gasparesganga-jquery-loading-overlay@1.5.4/extras/loadingoverlay_progress/loadingoverlay_progress.min.js"></script>    
    
    <script type="text/javascript" src="{{ asset("js/my_script.js") }}"></script>
    <!-- jQuery 2.1.4 -->
<!--    <script src="{{ asset('plugins/jQuery/jQuery-2.1.4.min.js') }}"></script>-->
    <!-- Bootstrap 3.3.5 -->
<!--    <script src="{{ asset('js/bootstrap.min.js') }}"></script>-->
    <!-- Sweet Alert -->
    <script src="{{ asset("js/sweetalert.min.js") }}"></script>
    <!-- DataTables -->
<!--    <script src="{{ asset('plugins/datatables/jquery.dataTables.min.js') }}"></script>-->
<!--    <script src="{{ asset('plugins/datatables/dataTables.bootstrap.min.js') }}"></script>-->
    <!-- FastClick -->
<!--    <script src="{{ asset('plugins/fastclick/fastclick.min.js') }}"></script>-->
    <!-- AdminLTE App -->
<!--    <script src="{{ asset('js/app.min.js') }}"></script>-->
    <!-- Sparkline -->
<!--    <script src="{{ asset('plugins/sparkline/jquery.sparkline.min.js') }}"></script>-->
    <!-- jvectormap -->
<!--    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-1.2.2.min.js') }}"></script>-->
<!--    <script src="{{ asset('plugins/jvectormap/jquery-jvectormap-world-mill-en.js') }}"></script>-->
    <!-- SlimScroll 1.3.0 -->
<!--    <script src="{{ asset('plugins/slimScroll/jquery.slimscroll.min.js') }}"></script>-->
    <!-- tokenize2 -->
    <script src="{{ asset('plugins/tokenize2/tokenize2.min.js') }}"></script>
    <!-- AdminLTE for demo purposes -->
    <script src="{{ asset('js/demo.js') }}"></script>    
    <script src="{{ asset("js/jquery.uploadPreview.min.js") }}"></script> 
    <!-- PACE -->
    <script> window.paceOptions = { ajax: false, restartOnRequestAfter: false, }; </script>
    <script src="{{ asset("plugins/pace/pace.min.js") }}"></script>
    <!-- Common js-->
    <script src="{{ asset('js/common.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.17.0/jquery.validate.js"></script>
    
    @include('sweet::alert')
    
    @yield('scripts')
    <script type="text/javascript">
    
        function validateEmail(email) {
            var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
            return re.test(email);
        }

        $(document).ready(function(){
        
            $("#formId").validate();

            $("body").on("click",".btn-signup-newsletter", function(){
            
            var url= "{{url('newsletter-subscribed')}}";
            var subscriber_email = $("#subscriber_email").val();
            if(subscriber_email == ''){
                $('#subscriber_email_error').show();return false;
            } else {
                $('#subscriber_email_error').hide();
            }
            
            if (validateEmail(subscriber_email)) {
                $.ajax({
                    url:url,
                    type:"post",
                    data:{subscriber_email:subscriber_email},
                    beforeSend: function(){
                        $("#subscriber_email").LoadingOverlay("show");
                    },
                    complete:function (res) {
                        $("#subscriber_email").LoadingOverlay("hide");
                        if(res.status == 422){
                            $('#subscriber_email_error').text('Sorry! This email has already been subscribed with us').show();
                        } else {
                            swal({title: "You have been subscribed successfully.",
                                type: "success",
                            });
                            $('#subscriber_email').val('');
                        }
                    }//.... end of success.
                });//..... end of ajax() .....//
            } else {
                $('#subscriber_email_error').text('Please enter a valid email address.').show();
            }
            

            


            });
        });
    </script>
</body>
</html>
