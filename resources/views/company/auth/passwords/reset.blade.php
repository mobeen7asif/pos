@extends('company.layouts.auth')

@section('content')
<a href="javascript:void(0)" class="logo">
    <img src="{{ asset('images/logo.png')}}" style="width: 326px;" alt="">
</a>

<form class="form-signin cmxform form-horizontal" method="POST" action="{{ url('company/password/reset') }}">
     
      {{ csrf_field() }}
      <input type="hidden" name="token" value="{{ $token }}">
      
    <h2 class="form-signin-heading">Reset Password</h2>
    <div class="login-wrap">                
        <div class="user-login-info">
            <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                <input type="email" name="email" class="form-control" placeholder="Email" value="{{ old('email') }}" autofocus>
                {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
            </div>            
            
            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                <input id="password" type="password" placeholder="Password" class="form-control" name="password">
                {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
            </div>
            
            <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                <input id="password-confirm" type="password" placeholder="Confirm Password" class="form-control" name="password_confirmation">
                {!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}
            </div>
            
        </div>
        <button class="btn btn-lg btn-login btn-block" type="submit">Reset Password</button>

    </div>
  </form>

@endsection
