<!--header start-->
<header class="header fixed-top clearfix">
<!--logo start-->
<div class="brand">

    <a href="{{ url('admin/dashboard') }}" class="logo">
        <img src="{{ asset('images/logo.png')}}" style="width: 210px;margin: -80px 0px 0px -394px;" alt="">
    </a>
    <div class="sidebar-toggle-box">
        <div class="fa fa-bars"></div>
    </div>
</div>
<!--logo end-->

<div class="top-nav clearfix">
    <!--search & user info start-->
    <ul class="nav pull-right top-menu">
<!--        <li>
            <input type="text" class="form-control search" placeholder=" Search">
        </li>-->
        <!-- user login dropdown start-->
        <li class="dropdown">
            <a data-toggle="dropdown" class="dropdown-toggle" href="#">
                <img alt="{{ Auth::user()->name }}" src="{{ checkImage('admin_avatar/'.Auth::user()->profile_image) }}">
                <span class="username">{{ Auth::user()->name }}</span>
                <b class="caret"></b>
            </a>
            <ul class="dropdown-menu extended logout">
                
                <li><a href="{{ url('admin/change-password') }}"><i class=" fa fa-key"></i>Change Password</a></li>
          
                <li><a href="{{ url('admin/logout') }}"><i class="fa fa-key"></i> Log Out</a></li>
            </ul>
        </li>
        <!-- user login dropdown end -->
    </ul>
    <!--search & user info end-->
</div>
</header>
<!--header end-->
