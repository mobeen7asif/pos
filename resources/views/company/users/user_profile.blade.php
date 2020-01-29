@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/users') }}"><i class="fa fa-home"></i> Employees</a></li>
                    <li class="active">Profile</li>
                </ul>
                <!--breadcrumbs end -->
                
                <div class="row">
            <div class="col-md-12">
                <section class="panel">
                    <div class="panel-body profile-information">
                       <div class="col-md-3">
                           <div class="profile-pic text-center">
                               <img src="{{ checkImage('users/'. $user->profile_image) }}" alt="{{ $user->name }}"/>
                           </div>
                       </div>
                       <div class="col-md-6">
                           <div class="profile-desk">
                               <h1>{{ $user->name }}</h1>
                               <span class="text-muted">{{$user->email}}</span>
                               <p>
                                   Lorem ipsum dolor sit amet, consectetur adipiscing elit. Aenean porttitor vestibulum imperdiet. Ut auctor accumsan erat, a vulputate metus tristique non. Aliquam aliquam vel orci quis sagittis.
                               </p>
                           </div>
                       </div>
                       <div class="col-md-3">
                           <div class="profile-statistics">
                               <h1>1240</h1>
                               <p>This Week Sales</p>
                               <h1>$5,61,240</h1>
                               <p>This Week Earn</p>
                               <ul>
                                   <li>
                                       <a href="#">
                                           <i class="fa fa-facebook"></i>
                                       </a>
                                   </li>
                                   <li class="active">
                                       <a href="#">
                                           <i class="fa fa-twitter"></i>
                                       </a>
                                   </li>
                                   <li>
                                       <a href="#">
                                           <i class="fa fa-google-plus"></i>
                                       </a>
                                   </li>
                               </ul>
                           </div>
                       </div>
                    </div>
                </section>
            </div>
            </div>
        </div>
    </div>
        
             
    </section>
</section>       
        

Employees
@endsection
