<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="ThemeBucket">
    <link rel="shortcut icon" href="images/favicon.png">
    <meta name="{{settingValue('meta_title')}}" content="{{settingValue('meta_description')}}">
    
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    
        
    <title>{{ settingValue('site_title') }}</title>

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
 <div id="wrapper">
  <div class="contant">
    <div class="registerLoginOuter clearfix">
      <div class="autoContent">
        <div class="registerLoginMain">
          <div class="registerLoginHead">
            <div class="logoOuter clearfix"> <a href="#"><img src="{{ asset("images/logo.png") }}" alt="#"></a> </div>
            <h1>@yield('title')</h1>
          </div>
          <div class="registerLoginInner clearfix">
            @yield('content')
         </div>
        </div>
      </div>
    </div>
  </div>
<footer id="footer" class="footerOut">
    <div class="footerInn_innr">
      <div class="footerTopOuter">
        <div class="autoContent">
          <div class="footer_autoContent">
            <div class="footerTopinner clearfix">
              <div class="rigisterUser">
                <h5>{{registeredUsers()}} </h5>
                <p>Registered Users</p>
              </div>
              <div class="totalRecord">
                <h5>{{allRecords()}} </h5>
                <p>Total Records</p>
              </div>
              <div class="storsOuter clearfix">
                <ul>
                  <li><a class="appleStore" href="#"></a></li>
                  <li><a class="googleStore"  href="#"></a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="footerMiddleOuter">
        <div class="autoContent">
          <div class="footer_autoContent">
            <div class="footerMiddleInner clearfix">
              <div class="getIntuchSocialLink clearfix">
                <div class="getIntuch">
                  <h5>Get in Touch</h5>
                  <ul>
                    <li><a href="{{ url('pages/get-support') }}">Get Support</a></li>
                    <li><a href="{{ url('contact-us') }}"> Contact Us</a></li>
                  </ul>
                </div>
                <div class="collBat">
                  <h5>CollBatt</h5>
                  <ul>
                    <li><a href="{{ url('pages/privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ url('pages/terms-and-conditions') }}"> Terms & Conditions</a></li>
                  </ul>
                </div>
                <div class="collBat">
                  <h5>About</h5>
                  <ul>
                    <li><a href="{{ url('take-tour') }}">Take the Tour</a></li>
                  </ul>
                </div>
                <div class="sociallinkOuter clearfix">
                  <ul>
                    <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
                    <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
                  </ul>
                </div>
              </div>
              {!! Form::open(['url' => '/newsletter-subscribed', 'class' => 'form-horizontal', 'files' => true , 'id' => 'formId']) !!}
              <div class="newlatterOuter clearfix">
                <div class="newlatterhead">
                  <h2>Sign Up for the Newsletter</h2>
                </div>
                <div class="signUpNewslatter clearfix">
                  <input type="text" name="subscriber_email" placeholder="Enter Your Email Address Here">
                  {!! $errors->first('subscriber_email', '<p class="help-block">:message</p>') !!}
                  <input type="submit" value="Sign Up">
                </div>
              </div>
              {!! Form::close() !!}
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="footerBottomouter">
      <div class="autoContent">
        <div class="footer_autoContent">
          <div class="footerBottomInner clearfix">
            <div class="copyrightBat">
              <p>© 2017 CollBatt </p>
            </div>
            <div class="mainscrollBtn"> </div>
          </div>
        </div>
      </div>
    </div>
  </footer>
     
    <!-- Scripts -->
    <script type="text/javascript" src="{{ asset("js/jquery-1.11.1.min.js") }}"></script>
    <script type="text/javascript" src="{{ asset("js/my_script.js") }}"></script>
    <!--Core js-->
    <script src="{{ asset("js/jquery.js") }}"></script>
    <script src="{{ asset("bs3/js/bootstrap.min.js"></script> 
    <!-- PACE -->
    <script src="{{ asset("plugins/pace/pace.min.js") }}"></script>
    <!-- Common js-->
    <script src="{{ asset('js/common.js') }}"></script>
    
    @include('sweet::alert')
    
    @yield('scripts')
</body>
</html>
