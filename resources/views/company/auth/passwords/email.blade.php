@extends('company.layouts.auth')

<!-- Main Content -->
@section('content')
<a href="javascript:void(0)" class="logo">
    <img src="{{ asset('images/logo.png')}}" style="width: 326px;" alt="">
</a>
<form class="form-signin cmxform form-horizontal" method="POST" action="{{ url('company/password/email') }}">
      {{ csrf_field() }}
    <h2 class="form-signin-heading">Reset Password</h2>
    <div class="login-wrap">
        
        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif
        
        <div class="user-login-info">
            <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" autofocus>
                {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
            </div>            
            
        </div>
        <button class="btn btn-lg btn-login btn-block" type="submit">Send Password Reset Link</button>

    </div>
  </form>
@endsection
