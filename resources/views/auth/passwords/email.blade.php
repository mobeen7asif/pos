@extends('auth.layouts.app')

@section('title', 'Forgot Password')

@section('content')
@if (session('status'))
    <div class="alert alert-success">
        {{ session('status') }}
    </div>
@endif

<div class="registerLoginDetail clearfix">    
    <div class="registrationForm clearfix"> 
    <form class="form-horizontal" method="POST" action="{{ url('password/email') }}">
        {{ csrf_field() }}
      <ul>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('email') ? ' has-error' : '' }} clearfix"> <small>Enter Your Email Address</small>
            <div class="customField">
              <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" >
              {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('g-recaptcha-response') ? ' has-error' : '' }} clearfix">
            <div class="customField"> 
                {!! Form::captcha() !!} 
                {!! $errors->first('g-recaptcha-response', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter clearfix">
            <div class="customsubmit">
              <input type="submit" value="Reset">
            </div>
          </div>
        </li>
      </ul>
    </form>
    </div>
  </div>
@endsection
