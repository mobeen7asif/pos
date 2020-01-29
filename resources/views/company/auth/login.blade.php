@extends('company.layouts.auth')

@section('content')
<a href="javascript:void(0)" class="logo">
    <img src="{{ asset('images/logo.png')}}" style="width: 326px;" alt="">
</a>
<form class="form-signin cmxform form-horizontal" method="POST" action="{{ url('company/login') }}">
      {{ csrf_field() }}
    <h2 class="form-signin-heading">sign in now</h2>
    <div class="login-wrap">
        <div class="user-login-info">
            <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                <input type="email" name="email" class="form-control" placeholder="Email" value="" autofocus>
                {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
            </div>
            <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                <input type="password" name="password" class="form-control" placeholder="Password">
                {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
            </div>

        </div>
        <label class="checkbox">
<!--            <input type="checkbox" value="remember-me"> Remember me-->
            <span class="pull-right">
                <a href="{{ url('company/password/reset') }}"> Forgot Password?</a>
            </span>
        </label>
        <button class="btn btn-lg btn-login btn-block" type="submit">Sign in</button>

    </div>
  </form>

@endsection
