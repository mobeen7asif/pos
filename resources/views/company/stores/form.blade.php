<style>
    .cke_inner{border: 1px solid #e2e2e4 !important;border-radius: 4px !important;}
</style>
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Store</header>
            <div class="panel-body">
                <div class="position-center">                    
                    
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                        {!! Form::label('name', 'Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Name','required' => 'required']) !!}
                            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>                                                                               
                    
                    <div class="form-group {{ $errors->has('currency_id') ? 'has-error' : ''}}">
                        {!! Form::label('currency_id', 'Currency', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            @if(isset($store))
                                {!! Form::select('currency_id', getCurrencyDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}
                            @else
                                {!! Form::select('currency_id', getCurrencyDropdown() ,companySettingValue('currency_id'), ['class' => 'form-control select2','required' => 'required']) !!}
                            @endif
                            
                            {!! $errors->first('currency_id', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>


                    {{--<div class="form-group {{ $errors->has('tax') ? 'has-error' : ''}}">--}}
                        {{--{!! Form::label('tax', 'Tax', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}--}}
                        {{--<div class="col-lg-9">--}}
                            {{--{!! Form::text('tax', null, ['class' => 'form-control','placeholder' => 'Tax','required' => 'required']) !!}--}}
                            {{--{!! $errors->first('tax', '<p class="help-block">:message</p>') !!}--}}
                            {{--<div class="help-block with-errors"></div>--}}
                        {{--</div>--}}
                    {{--</div>--}}


                    <div class="form-group {{ $errors->has('tax_id') ? 'has-error' : ''}}">
                        {!! Form::label('tax_id', 'Default Tax', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::select('tax_id', getTaxRatesDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}
                            {!! $errors->first('tax_id', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>


                    <div class="form-group {{ $errors->has('time_zone') ? 'has-error' : ''}}">
                        {!! Form::label('time_zone', 'Timezone', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            <?php $timezones = getTimeZones(); ?>
                            <select name="time_zone" class="form-control select2" required>
                                @foreach($timezones as $timezone)
                                    <option @if($store->time_zone == $timezone['value']) selected @endif value="{{$timezone['value']}}">{{$timezone['name']}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('time_zone', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    @php
                    if(isset($store->break_time)){
                        $arr = explode('-',$store->break_time);
                    } else {
                        $arr[0] = '10:00 AM';
                        $arr[1] = '12:00 PM';
                    }
                    @endphp
                    <div class="form-group {{ $errors->has('break_time') ? 'has-error' : ''}}">
                        {!! Form::label('break_time', 'Break Time', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            <input name="set_break_time" type="checkbox" id="set_break_time" @if($store->set_break_time == 1) value="1" checked @else value="0" @endif>
                            <div id="time-range" @if($store->set_break_time == 1) style="display: block" @else style="display: none" @endif>
                                <p>Time Range: <span class="slider-time">{{$arr[0]}}</span> - <span class="slider-time2">{{$arr[1]}}</span>

                                </p>
                                <div class="sliders_step1">
                                    <div id="slider-range"></div>
                                </div>
                            </div>
                            {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                        <input type="hidden" value="{{$arr[0]}}-{{$arr[1]}}" name="break_time" id="break_time">
                    </div>

                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : ''}}">
                        {!! Form::label('phone', 'Phone', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('phone', null, ['class' => 'form-control','placeholder' => 'Phone']) !!}
                            {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('manager') ? 'has-error' : ''}}">
                        {!! Form::label('manager', 'Manager', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('manager', null, ['class' => 'form-control','placeholder' => 'Manager']) !!}
                            {!! $errors->first('manager', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>


                    
                    <div class="form-group {{ $errors->has('address') ? 'has-error' : ''}}">
                        {!! Form::label('address', 'Address', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::textarea('address', null, ['class' => 'form-control','placeholder' => 'Address','rows'=>2]) !!}
                            {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>                    
                    
                    <div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
                    @if(isset($submitButtonText))
                        {!! Form::label('image', 'Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}                        
                    @else
                        {!! Form::label('image', 'Image', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}                        
                    @endif
                    <div class="col-md-9">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                @if(isset($submitButtonText)) @if($store->image != 'no_picture.jpg') <a href="{{url('company/delete_image/stores/'.$store->id).'/image'}}" title="Delete Image"><i class="fa fa-trash action-padding"></i></a> @endif @endif
                                @if(@$store->image != 'no_picture.jpg')
                                    <img src="{{ checkImage('stores/'. $store->image) }}" alt="" />
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" alt="" />
                                @endif
                                
                            </div>
                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                            <div>
                                <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                <input id="image" type="file" class="default" name="image" accept="image/*" />
                                </span>
                                <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                            </div>
                            {!! $errors->first('image', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors" style="color: #a94442" id="image_error"></div>
                        </div>                        
                    </div>
                </div>




                    <div class="form-group {{ $errors->has('background_image') ? 'has-error' : ''}}">
                        @if(isset($submitButtonText))
                            {!! Form::label('background_image', 'APP Background Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        @else
                            {!! Form::label('background_image', 'APP Background Image', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        @endif
                        <div class="col-md-9">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    @if(isset($submitButtonText)) @if($store->background_image != 'no_picture.jpg') <a href="{{url('company/delete_image/stores/'.$store->id).'/background_image'}}" title="Delete Image"><i class="fa fa-trash action-padding"></i></a> @endif @endif
                                    @if(@$store->background_image != 'no_picture.jpg')
                                        <img src="{{ checkImage('stores/'. $store->background_image) }}" alt="" />
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="" />
                                    @endif

                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                <div>
                                <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                <input id="background_image" type="file" class="default" name="background_image" accept="image/*" />
                                </span>
                                    <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                </div>
                                {!! $errors->first('background_image', '<p class="help-block">:message</p>') !!}
                                <div class="help-block with-errors" style="color: #a94442" id="background_image_error"></div>
                            </div>
                        </div>
                    </div>

                {{--<div class="form-group">--}}
                    {{--<div class="col-lg-offset-2 col-lg-10">--}}
                        {{--{!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}--}}
                    {{--</div>--}}
                {{--</div>--}}
                </div>
            </div>
        </section>

    </div>
</div>

<div class="col-lg-12">
    <section class="panel">
        <header class="panel-heading">Recept configurations</header>
        <div class="panel-body">
            <div class="position-center ck_editor">
                <div class="row modifier_select">
                    <div class="form-group col-md-6 {{ $errors->has('header_content') ? 'has-error' : ''}}">
                        {!! Form::label('header', 'Header Text', ['class' => 'control-label']) !!}
                        {!! Form::textarea('header_content', null, ['class' => 'form-control']) !!}
                        {!! $errors->first('header_content', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>

                    <div class="form-group col-md-6 {{ $errors->has('footer_content') ? 'has-error' : ''}}">
                        {!! Form::label('footer', 'Footer Text', ['class' => 'control-label']) !!}
                        {!! Form::textarea('footer_content', null, ['class' => 'form-control']) !!}
                        {!! $errors->first('footer_content', '<p class="help-block">:message</p>') !!}
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


@section('scripts')
    <script src="//cdn.ckeditor.com/4.8.0/full/ckeditor.js"></script>
<script type="text/javascript">

    $(document).ready(function(){
        //$('#slider-range').trigger("change");
        CKEDITOR.replace( 'header_content',{
            removePlugins: 'elementspath,magicline',
            resize_enabled: false,
            allowedContent: true,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            toolbar: [
                [ 'Bold','-','Italic','-','Underline'],
            ],
        });

        CKEDITOR.replace( 'footer_content',{
            removePlugins: 'elementspath,magicline',
            resize_enabled: false,
            allowedContent: true,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            toolbar: [
                [ 'Bold','-','Italic','-','Underline'],
            ],
        });

        $("#slider-range").slider({
            range: true,
            min: 0,
            max: 1440,
            step: 15,
            values: [600, 720],
            slide: function (e, ui) {
                var hours1 = Math.floor(ui.values[0] / 60);
                var minutes1 = ui.values[0] - (hours1 * 60);

                if (hours1.length == 1) hours1 = '0' + hours1;
                if (minutes1.length == 1) minutes1 = '0' + minutes1;
                if (minutes1 == 0) minutes1 = '00';
                if (hours1 >= 12) {
                    if (hours1 == 12) {
                        hours1 = hours1;
                        minutes1 = minutes1 + " PM";
                    } else {
                        hours1 = hours1 - 12;
                        minutes1 = minutes1 + " PM";
                    }
                } else {
                    hours1 = hours1;
                    minutes1 = minutes1 + " AM";
                }
                if (hours1 == 0) {
                    hours1 = 12;
                    minutes1 = minutes1;
                }



                $('.slider-time').html(hours1 + ':' + minutes1);

                var hours2 = Math.floor(ui.values[1] / 60);
                var minutes2 = ui.values[1] - (hours2 * 60);

                if (hours2.length == 1) hours2 = '0' + hours2;
                if (minutes2.length == 1) minutes2 = '0' + minutes2;
                if (minutes2 == 0) minutes2 = '00';
                if (hours2 >= 12) {
                    if (hours2 == 12) {
                        hours2 = hours2;
                        minutes2 = minutes2 + " PM";
                    } else if (hours2 == 24) {
                        hours2 = 11;
                        minutes2 = "59 PM";
                    } else {
                        hours2 = hours2 - 12;
                        minutes2 = minutes2 + " PM";
                    }
                } else {
                    hours2 = hours2;
                    minutes2 = minutes2 + " AM";
                }

                $('.slider-time2').html(hours2 + ':' + minutes2);

                var break_time = hours1 + ':' + minutes1 + '-' + hours2 + ':' + minutes2;
                $('#break_time').val(break_time);
            }
        });

    });
    $('#update_store').submit(function() {
        var image = false;
        var back_image = false;
        var imgpath = document.getElementById('background_image');
        if (!imgpath.value == ""){
            var img=imgpath.files[0].size;
            var imgsize=img/1024;
            if(imgsize > 2000){
                $('#background_image_error').html('Image size should be less than 2 MB');
                back_image = false;
            } else  {
                back_image = true;
            }
        } else {
            back_image = true;
        }

        var imgpath = document.getElementById('image');
        if (!imgpath.value == ""){
            var img=imgpath.files[0].size;
            var imgsize=img/1024;
            if(imgsize > 2000){
                $('#image_error').html('Image size should be less than 2 MB');
                image = false;
            } else  {
                image = true;
            }
        } else {
            image = true;
            //image = true;
        }

        if(back_image == true && image == true){
            return true;
        } else {
            return false;
        }
        // DO STUFF...
        //return false; // return false to cancel form action
    });


    $('#set_break_time').change(function () {
        if($(this).is(':checked')){
            $('#time-range').show();
            $(this).val(1);
        } else {
            $(this).val(0);
            $('#time-range').hide();
        }
    });
</script>
@endsection