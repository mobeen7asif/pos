




@extends('company.layouts.auth')

@section('content')
    <a href="javascript:void(0)" class="logo">
        <img src="{{ asset('images/logo.png')}}" style="width: 326px;" alt="">
    </a>
    <form id="passwordForm" class="form-signin cmxform form-horizontal" method="POST" action="{{ url('/api/customer-update-password') }}">
        <input type="hidden" value="<?php echo $token; ?>" name="token">
        {{ csrf_field() }}
        <h2 class="form-signin-heading">Forgot Password</h2>
        <div class="login-wrap">
            <div class="user-login-info">
                <div class="form-group">

                    @if(isset($errors)) {{dd($errors)}} @endif
                    <input type="password" name="password" id="password" autocomplete="off" class="form-control" placeholder="Password">
                    @if(isset($errors)) @if($errors->any()){!! $errors->first('password', '<p class="help-block">:message</p>') !!} @endif @endif
                </div>
                <div class="form-group">
                    <input type="password" name="password_confirmation" id="confirm_password" autocomplete="off" class="form-control" placeholder="Confirm Password">
                    @if(isset($errors)) @if($errors->any()){!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!} @endif @endif
                </div>
            </div>
            <button class="btn btn-lg btn-login btn-block" type="submit">Send</button>

        </div>
    </form>


    <script src="https://code.jquery.com/jquery-3.2.1.min.js" integrity="sha256-hwg4gsxgFZhOsEEamdOYGBf13FyQuiTwlAQgxVSNgt4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.16.0/jquery.validate.js"></script>
@endsection
