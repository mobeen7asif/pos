@extends('auth.layouts.app')

@section('title', 'Login')

@section('content')
<div class="registerLoginDetail clearfix">
    <div class="registrationForm clearfix">
     <form class="form-horizontal" method="POST" action="{{ url('login') }}">
         {{ csrf_field() }}
      <ul>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('email') ? ' has-error' : '' }} clearfix"> <small>Email</small>
            <div class="customField">
              <input id="email" type="email" name="email" value="{{ old('email') }}" autofocus>
              {!! $errors->first('email', '<p class="help-block">:message</p>') !!}              
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('password') ? ' has-error' : '' }} clearfix"> <small>Password</small>
            <div class="customField">
              <input id="password" type="password" name="password" >
              {!! $errors->first('password', '<p class="help-block">:message</p>') !!} 
            </div>
            <a href="{{ url('password/reset') }}">Forgot Password</a> </div>
        </li>
        <li>
          <div class="customTextfieldOuter clearfix">
            <div class="customsubmit">
                  <input type="submit" value="Login">
            </div>
          </div>
        </li>
      </ul>
      </form>   
    </div>
    <div class="orSection"> <span>OR</span> </div>
    <div class="regiterWith">
      <div class="yetNotRegiter">
        <p>If you are not registered click here to </p>
        <a href="{{ url('register') }}">REGISTER</a> </div>
      <div class="regiterWithFb">
        <h5>Register With Facebook</h5>
        <a href="{{ url("auth/facebook") }}"><strong><i class="fa fa-facebook" aria-hidden="true"></i></strong><b>Register With facebook</b></a> </div>
    </div>
  </div>
@endsection
