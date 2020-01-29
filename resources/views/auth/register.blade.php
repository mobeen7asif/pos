@extends('auth.layouts.app')

@section('title', 'Register')

@section('content')
<div class="registerLoginDetail clearfix">
    <div class="registrationForm clearfix">
     <form class="form-horizontal" method="POST" action="{{ url('register') }}">
      {{ csrf_field() }}
      
      <ul>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('first_name') ? ' has-error' : '' }} clearfix"> <small>First Name</small>
            <div class="customField">
              <input id="first_name" type="text" name="first_name" value="{{ old('first_name') }}" autofocus>
              {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('email') ? ' has-error' : '' }} clearfix"> <small>Email</small>
            <div class="customField">
              <input id="email" type="email" name="email" value="{{ old('email') }}" >
              {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('last_name') ? ' has-error' : '' }} clearfix"> <small>Last Name</small>
            <div class="customField">
              <input id="last_name" type="text" class="form-control" name="last_name" value="{{ old('last_name') }}" autofocus>
            {!! $errors->first('last_name', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('name') ? ' has-error' : '' }} clearfix"> <small>User Name</small>
            <div class="customField">
               <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}" autofocus>
               {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('location') ? ' has-error' : '' }} clearfix"> <small>Location</small>
            <div class="customFieldSelect"> <span>Select Country</span>
              {!! Form::select('location', getCountries(),null, ['class' => '']) !!}                                              
            </div>
            {!! $errors->first('location', '<p class="help-block">:message</p>') !!}
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('location_visible') ? ' has-error' : '' }} clearfix"> <small>Location Visible </small>
            <div class="customCheckBox clearfix">   
              <div class="checkBoxouter">
                <label class="select">
                  {!! Form::radio('location_visible',true, true) !!}
                  <strong>Yes</strong></label>
              </div>
              <div class="checkBoxouter">
                <label>
                  {!! Form::radio('location_visible',false, false) !!}
                  <strong>No</strong></label>
              </div>
                {!! $errors->first('location_visible', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter{{ $errors->has('password') ? ' has-error' : '' }} clearfix"> <small>Password</small>
            <div class="customField">
              <input id="password" type="password" class="form-control" name="password" >
              {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
            </div>
          </div>
        </li>
        <li>
          <div class="customTextfieldOuter clearfix"> <small>Repeat Password</small>
            <div class="customField">
              <input id="password-confirm" type="password" class="form-control" name="password_confirmation" >
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
              <input type="submit" value="Register">
            </div>
          </div>
        </li>
      </ul>
      
     </form>
    </div>
    <div class="orSection"> <span>OR</span> </div>
    <div class="regiterWith">
      <div class="regiterWithFb">
        <h5>Register With Facebook</h5>
        <a href="{{ url("auth/facebook") }}"><strong><i class="fa fa-facebook" aria-hidden="true"></i></strong><b>Register With facebook</b></a> </div>
    </div>
  </div>
@endsection
