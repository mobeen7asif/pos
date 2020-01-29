<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <?php if(isset($submitButtonText)){
                $update = 1;
            } else {
                $update = 0;
                $user = (object) [
                    'printer_type' => 'wifi' ,
                ];
            } ?>
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Employee</header>
            <div class="panel-body">
                <div class="position-center">                    
                                       
                  <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                    {!! Form::label('store_id', 'Store', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::select('store_id', getStoresDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}
                        {!! $errors->first('store_id', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>
                    
                  <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                    {!! Form::label('name', 'Name', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Name','required' => 'required']) !!}
                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>

                  <div class="form-group {{ $errors->has('gender') ? 'has-error' : ''}}">
                    {!! Form::label('gender', 'Gender', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::select('gender', [''=>'Select Gender','1'=>'Male','2'=>'Female'],null, ['class' => 'form-control select2','required' => 'required']) !!}
                        {!! $errors->first('gender', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>



                  <div class="form-group {{ $errors->has('phone') ? 'has-error' : ''}}">
                    {!! Form::label('phone', 'Phone', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::text('phone', null, ['class' => 'form-control','maxlength' => 25 ]) !!}
                        {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>

                    <div class="form-group {{ $errors->has('printer_type') ? 'has-error' : ''}}">
                        {!! Form::label('name', "Printer's Type", ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9 radio_class">
                            <div>
                            <input @if(@$user->printer_type == 'wifi') checked @endif class="printer_type" type="radio" name="printer_type" value="wifi"> Wifi Printer
                            </div>
                            <div>
                            <input @if(@$user->printer_type == 'bluetooth') checked @endif class="printer_type" type="radio" name="printer_type" value="bluetooth"> Bluetooth Printer
                            </div>
                            {!! $errors->first('printer_type', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('ip') ? 'has-error' : ''}}">
                        {!! Form::label('name', "Printer IP Address", ['class' => 'col-lg-3 col-sm-3 control-label printer_label']) !!}
                        <div class="col-lg-9">
                            {{--<input type="text" value="" class="form-control printer_placeholder" placeholder="Enter Printer IP Address">--}}
                            {!! Form::text('ip', null, ['class' => 'form-control printer_placeholder','placeholder' => 'Enter Printer IP Address','id' => 'ip_address']) !!}
                            {!! $errors->first('ip', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    
                 <div class="form-group {{ $errors->has('profile_image') ? 'has-error' : ''}}">
                     @if(isset($submitButtonText))
                        {!! Form::label('profile_image', 'Profile Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}                        
                    @else
                        {!! Form::label('profile_image', 'Profile Image', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}                        
                    @endif
                    <div class="col-md-9">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                @if(@$user->profile_image != '')
                                    <img src="{{ checkImage('users/'. $user->profile_image) }}" alt="" />
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" alt="" />
                                @endif

                            </div>
                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                            <div>
                                <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                <input type="file" class="default" name="profile_image" accept="image/*" />
                                </span>
                                <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                            </div>
                            {!! $errors->first('profile_image', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>                        
                    </div>
                </div>    
                    
                  <div class="form-group {{ $errors->has('role') ? 'has-error' : ''}}">
                    {!! Form::label('role', 'Role', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {{ Form::select('role',  $roles, null,['class' => 'form-control select2 select_role']) }}
                        {!! $errors->first('role', '<p class="help-block">:message</p>') !!}
                    </div>
                  </div>                                                                                 

                  <div class="form-group {{ $errors->has('status') ? 'has-error' : ''}}">
                    {!! Form::label('status', 'Status', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {{ Form::select('status',  ['1'=>'Active','0'=>'Inactive'], null,['class' => 'form-control select2']) }}
                        {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
                    </div>
                  </div>                                                                                 

                </div>
            </div>
        </section>

    </div>
    {{--*(If you need to reset the password for this user--}}
    @if(@$user)
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Login Information</header>
            <div class="panel-body">
                <div class="position-center">
                    
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                        {!! Form::label('email', 'Email', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::email('email', null, ['class' => 'form-control','placeholder' => 'Email','required' => 'required']) !!}
                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                        <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}} password_div">
                            {!! Form::label('password', 'Password', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                            <div class="col-lg-9">
                                {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password']) !!}
                                {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    {{--<div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : ''}}">--}}
                        {{--{!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}--}}
                        {{--<div class="col-lg-9">--}}
                            {{--{!! Form::password('password_confirmation', ['class' => 'form-control','placeholder' => 'Confirm Password','data-match'=>'#password']) !!}--}}
                            {{--{!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}--}}
                            {{--<div class="help-block with-errors"></div>--}}
                        {{--</div>--}}
                    {{--</div>  --}}
                    <div class="form-group {{ $errors->has('pin_code') ? 'has-error' : ''}}">
                        {!! Form::label('pin_code', 'Pin Code', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::text('pin_code', null, ['class' => 'form-control','required' => 'required', 'id' => 'pin_code' ]) !!}
                            {!! $errors->first('pin_code', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </section>

    </div>
    @else

    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Login Information </header>
            <div class="panel-body">
                <div class="position-center">
                    
                    <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                        {!! Form::label('email', 'Email', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::email('email', null, ['class' => 'form-control','placeholder' => 'Email','required' => 'required']) !!}
                            {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}} password_div" >
                        {!! Form::label('password', 'Password', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password','required' => 'required','data-minlength' => 6]) !!}
                            {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    {{--<div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : ''}}">--}}
                        {{--{!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}--}}
                        {{--<div class="col-lg-9">--}}
                            {{--{!! Form::password('password_confirmation', ['class' => 'form-control','placeholder' => 'Confirm Password','required' => 'required','data-match'=>'#password']) !!}--}}
                            {{--{!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}--}}
                            {{--<div class="help-block with-errors"></div>--}}
                        {{--</div>--}}
                    {{--</div>  --}}
                    <div class="form-group {{ $errors->has('pin_code') ? 'has-error' : ''}}">
                        {!! Form::label('pin_code', 'Pin Code', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::text('pin_code', null, ['class' => 'form-control','required' => 'required', 'id' => 'pin_code' ]) !!}
                            {!! $errors->first('pin_code', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
                        </div>
                    </div>


                </div>
            </div>
        </section>

    </div>
    @endif
</div>

@section('scripts')
    <script type="text/javascript" src="{{ asset('plugins/jquery_ip_mask/jquery.mask.js') }}"></script>
<script type="text/javascript">

    //$('#ip_address').mask('099.099.099.099');
    $('#pin_code').mask('9999');
    $(document).ready(function(){
        if("{{$update}}" == 1){
            var printer_type = "{{$user->printer_type}}";
            if(printer_type == 'wifi'){
                $('.printer_placeholder').attr('placeholder','Enter Printer IP Address');
                $('.printer_label').html('Printer IP Address');
            } else {
                $('.printer_placeholder').attr('placeholder','Enter Printer MAC Address');
                $('.printer_label').html('Printer MAC Address');
            }
        } else {
            $('.printer_placeholder').attr('placeholder','Enter Printer IP Address');
            $('.printer_label').html('Printer IP Address');
        }

        if("{{$update}}" == 1){
            var role = "{{@$user->role_name}}";
            if(role === 'Store Admin'){
                $('.password_div').show();
            } else {
                $('.password_div').hide();
            }
        } else {
            $('.password_div').hide();
        }

    });
    $('.printer_type').change(function () {
       var printer_type = $('input[name=printer_type]:checked').val();
       if(printer_type == 'wifi'){
           $('.printer_placeholder').attr('placeholder','Enter Printer IP Address');
           $('.printer_label').html('Printer IP Address');
       } else {
           $('.printer_placeholder').attr('placeholder','Enter Printer MAC Address');
           $('.printer_label').html('Printer MAC Address');
       }
    });
    $('.select_role').change(function () {
        var role = $(".select_role option:selected").text();
        if(role === 'Store Admin'){
            $('.password_div').show();
        } else {
            $('.password_div').hide();
        }
    });
</script>
@endsection
