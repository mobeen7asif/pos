@extends('admin.layouts.app')

@section('content')

<div class="content-wrapper">
        <section class="content-header">
          <h1>Users</h1>
            <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
            <li><a href="{{ url('admin/users') }}">Users</a></li>
            <li class="active">Update Profile</li>
          </ol>
        </section>

        <section class="content">
          <div class="row">
            <div class="col-md-2"></div>
            <div class="col-md-8">
              <div class="box box-info">
                  
                {!! Form::model($user, [
                    'method' => 'PATCH',
                    'url' => ['/admin/users', Hashids::encode($user->id)],
                    'class' => 'form-horizontal',
                    'files' => true
                ]) !!}

                @include ('admin.users.form', ['submitButtonText' => 'Update'])

                {!! Form::close() !!}
                
              </div>
            </div>
          </div>
        </section>      
    </div>

@endsection

