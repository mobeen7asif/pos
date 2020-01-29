<style type="text/css">
  .btn-signup-newsletter {
    display: inline-block;
    height: 36px;
    background: #961818;
    border: solid 0.5px #751010;
    padding: 4px 20px;
    line-height: 25px;
    color: #f9f9f9;
    cursor: pointer;
    font-size: 16px;
    letter-spacing: -0.7px;
    border-radius: 2px !important;
    box-shadow: 0px 16px 2px rgba(255,255,255,0.10) inset;
    font-family: 'Arial-BoldMT';
    transition: all 0.3s ease;
    -moz-transition: all 0.3s ease;
    -ms-transition: all 0.3s ease;
    -o-transition: all 0.3s ease;
    -webkit-transition: all 0.3s ease;
    float: left;
    margin-left: 10px;
}
.btn-signup-newsletter {
    border-radius: 0px;
    -webkit-appearance: none;
    appearance: none;
    outline: none;
}

</style>

<footer id="footer" class="footerOut">
<div class="footerInn_innr">
  <div class="footerTopOuter">
    <div class="autoContent">
      <div class="footer_autoContent">
        <div class="footerTopinner clearfix">
          <div class="rigisterUser">
            <h5>{{registeredUsers()}}</h5>
            <p>Registered Users</p>
          </div>
          <div class="totalRecord">
            <h5>{{allRecords()}} </h5>
            <p>Total Records</p>
          </div>
          <div class="storsOuter clearfix">
            <ul>
              <li><a class="appleStore" href="#"></a></li>
              <li><a class="googleStore"  href="#"></a></li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
  <div class="footerMiddleOuter">
    <div class="autoContent">
      <div class="footer_autoContent">
        <div class="footerMiddleInner clearfix">
          <div class="getIntuchSocialLink clearfix">
            <div class="getIntuch">
              <h5>Get in Touch</h5>
              <ul>
                <li><a href="{{ url('pages/get-support') }}">Get Support</a></li>
                <li><a href="{{ url('contact-us') }}"> Contact Us</a></li>
              </ul>
            </div>
            <div class="collBat">
              <h5>CollBatt</h5>
              <ul>
                <li><a href="{{ url('pages/privacy-policy') }}">Privacy Policy</a></li>
                <li><a href="{{ url('pages/terms-and-conditions') }}"> Terms & Conditions</a></li>
              </ul>
            </div>
            <div class="collBat">
              <h5>About</h5>
              <ul>
                <li><a href="{{ url('take-tour') }}">Take the Tour</a></li>
              </ul>
            </div>
            <div class="sociallinkOuter clearfix">
              <ul>
                <li><a href="#"><i class="fa fa-facebook" aria-hidden="true"></i></a></li>
                <li><a href="#"><i class="fa fa-twitter" aria-hidden="true"></i></a></li>
                <li><a href="#"><i class="fa fa-youtube-play" aria-hidden="true"></i></a></li>
                <li><a href="#"><i class="fa fa-instagram" aria-hidden="true"></i></a></li>
              </ul>
            </div>
          </div>
          
          {!! Form::open(['class' => 'form-horizontal', 'files' => true , 'id' => 'formId']) !!}
            <div class="newlatterOuter clearfix">
              <div class="newlatterhead">
                <h2>Sign Up for the Newsletter</h2>
              </div>
              <div class="signUpNewslatter signUpNewslatter_email clearfix">
                
                
                {{ Form::email('subscriber_email', null ,[
                'placeholder' => 'Enter Your Email Address Here',
                'class'       => 'form-control',
                'id'          => 'subscriber_email',
                'required'    => 'required',
                'autofocus'   => ($errors->has('subscriber_email') ? 'autofocus' : null)
                ]) }}
                @if ($errors->has('subscriber_email'))
                  <p class="help-block error">{{{ $errors->first('subscriber_email') }}}</p>
                @endif
                <label style="display: none;" id="subscriber_email_error" class="error" for="subscriber_email">This field is required.</label>
                
                <!-- <input type="submit" value="Sign Up"> -->
                <a href="javascript:void(0)" class="btn-signup-newsletter">Sign Up</a>
              </div>
            </div>
          {!! Form::close() !!}
        </div>
      </div>
    </div>
  </div>
</div>
<div class="footerBottomouter">
  <div class="autoContent">
    <div class="footer_autoContent">
      <div class="footerBottomInner clearfix">
        <div class="copyrightBat">
          <p>© 2017 CollBatt </p>
        </div>
        <div class="mainscrollBtn"> </div>
      </div>
    </div>
  </div>
</div>
</footer>

