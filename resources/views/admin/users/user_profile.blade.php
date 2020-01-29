@extends('admin.layouts.app')

@section('content')

<div class="content-wrapper">
        <section class="content-header">
          <h1>User Profile</h1>
            <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ url('admin/users') }}">Users</a></li>
            <li class="active">User Profile</li>
          </ol>
        </section>

        <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">
              <img src="{{ checkImage('users/'. $user->profile_image) }}" class="profile-user-img img-responsive img-circle" alt="User Image">
              
              <h3 class="profile-username text-center">{{$user->name}}</h3>

              <p class="text-muted text-center">{{$user->email}}</p>

<!--              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Records</b> <a class="pull-right">{{count($user->items)}}</a>
                </li>
                <li class="list-group-item">
                  <b>Score</b> <a class="pull-right">{{$user->user_score}}</a>
                </li>
                
              </ul>-->

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab" aria-expanded="true">Profile Detail</a></li>
            </ul>
            <div class="tab-content">
              
              <!-- /.tab-pane -->
              <!-- /.tab-pane -->

              <div class="tab-pane active" id="settings">
                <form class="form-horizontal">
                    <div class="form-group">
                    {!! Form::label('Name', 'Name: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$user->name}}</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                    {!! Form::label('Email', 'Email: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$user->email}}</span>
                        </div>
                    </div>
                    
                    <div class="form-group">
                    {!! Form::label('', 'Date of birth: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$user->dob}}</span>
                        </div>
                    </div>

                    <div class="form-group">
                    {!! Form::label('', 'Blood Group: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$user->blood_group}}</span>
                        </div>
                    </div>

                    

                    <div class="form-group">
                    {!! Form::label('', 'Status: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$user->profile_status}}</span>
                        </div>
                    </div>

                    


                    <div class="form-group">
                        <div class="col-sm-offset-2 col-sm-10">
                            <a href="{{ url()->previous() }}" class="btn btn-primary">Back</a>
                        </div>
                    </div>                                    
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>  
    </div>

@endsection
