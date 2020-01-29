
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Ad</header>
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

                    <div class="form-group {{ $errors->has('media_type') ? 'has-error' : ''}}">
                        {!! Form::label('media_type', 'Media Type', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::select('media_type', getMediaDropdown(),null, ['class' => 'form-control select2 media_type','required' => 'required']) !!}
                            {!! $errors->first('media_type', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('time') ? 'has-error' : ''}} duration">
                        {!! Form::label('Duration (seconds)', 'Duration (seconds)', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::number('time', null, ['oninput'=> 'this.value = Math.abs(this.value)','class' => 'form-control duration','placeholder'=>'Duration','required' => 'required','step' => 'any']) !!}
                            {!! $errors->first('time', '<p class="help-block">:message</p>') !!}
                            {{--<div class="help-block with-errors"></div>--}}
                        </div>
                    </div>



                    <div class="form-group {{ $errors->has('media') ? 'has-error' : ''}}">
                        @if(isset($submitButtonText))
                            {!! Form::label('image', 'Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        @else
                            {!! Form::label('image', 'Image', ['class' => 'col-lg-3 col-sm-3 control-label required-input label_media']) !!}
                        @endif
                        <div class="col-md-9">
                            {{--<div class="fileupload fileupload-new" data-provides="fileupload">--}}
                                {{--<div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">--}}
                                    {{--@if(@$ad->image != '')--}}
                                        {{--<img class="image" src="{{ checkImage('stores/'. $ad->image) }}" alt="" />--}}
                                    {{--@else--}}
                                        {{--<img class="image" src="{{ asset('images/no-image.png') }}" alt="" />--}}
                                    {{--@endif--}}

                                {{--</div>--}}
                                {{--<div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>--}}
                                {{--<div>--}}
                                {{--<span class="btn btn-white btn-file">--}}
                                {{--<span class="fileupload-new select_media_text"><i class="fa fa-paper-clip"></i> Select image</span>--}}
                                {{--<span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>--}}
                                {{--<input type="file" class="default input_media" name="media" accept="image/*"  />--}}
                                {{--</span>--}}
                                    {{--<a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>--}}
                                {{--</div>--}}
                                {{--{!! $errors->first('image', '<p class="help-block">:message</p>') !!}--}}
                                {{--<div class="help-block with-errors"></div>--}}
                            {{--</div>--}}
                            <div class="custom-upload" style="position: relative;width: 40%;     border: 1px solid #aaa;">
                                <img src="{{url('/')}}/upload_icon.png" style="display: block;width: 100%">
                                <input id="image_check" name="media" class="input_media" accept="image/*" type="file" style="position: absolute; left: 0; top: 0; width: 100%;height: 100%;z-index: 1;opacity: 0">
                            </div>
                            <span id="file_name"></span>
                            <div class="help-block with-errors" style="color: #a94442" id="background_image_error"></div>
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
</div>


@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){

        var objectUrl;

        $(document).ready(function(){

            });

            $('#btn').click(function(){

                var check_video = $("#vid").prop('src');
// empty video check
                if(check_video){

                    var seconds = $("#vid")[0].duration;
                    var check_error = $("#vid")[0].error;


                }
    });

    $('.input_media').change(function () {
       var file_name = $(this).val().split('\\').pop();
       $('#file_name').html(file_name);
        $('#background_image_error').html('');
    });



    var selected_type = 'image';
    $('.media_type').change(function () {
        $('#background_image_error').html('');
        $('#file_name').html('');
       var media_type = $(this).val();
       if(media_type === 'video'){
           selected_type = 'video';
           $('.duration').hide();
           $('.select_media_text').html('Select video');
           $('.label_media').html('Video');
           $('.input_media').attr('accept','video/*');
           $('.duration').removeAttr('required');
           //$('.image').attr('src',"{{ asset('uploads/no_video.png') }}");
       } else {
           selected_type = 'image';
           $('.duration').show();
           $('.select_media_text').html('Select image');
           $('.label_media').html('Image');
           $('.input_media').attr('accept','image/*');
       }
    });

    function readURL(input) {
        validateFile(input.files[0].name);
        // if (input.files && input.files[0]) {
        //     var reader = new FileReader();
        //
        //     reader.onload = function (e) {
        //         $('#profile_image').attr('src', e.target.result);
        //     }
        //
        //     reader.readAsDataURL(input.files[0]);
        // }
    }

    // $(".input_media").change(function(){
    //     readURL(this);
    // });
    function validateFile(file_name) {
        //debugger;
        if(selected_type === 'image'){

            var validExtensions = ['jpg','png','jpeg','PNG','JPG','JPEG'];
        } else {
            var validExtensions = ['AVI','avi','FLV','flv','WMV','wmv','MOV','mov','MP4','mp4'];
        }
         //array of valid extensions
        var fileName = file_name;
        var fileNameExt = fileName.substr(fileName.lastIndexOf('.') + 1);
        if ($.inArray(fileNameExt, validExtensions) == -1) {
            // input.type = ''
            // input.type = 'file'
            // $('#user_img').attr('src',"");
            alert("Only these file types are accepted : "+validExtensions.join(', '));
            throw new Error("Only these file types are accepted : "+validExtensions.join(', '));
        }
    }



    $('#create_ad').submit(function() {
        var image = false;
        var imgpath = document.getElementById('image_check');
        if (!imgpath.value == ""){
            var img=imgpath.files[0].size;
            var imgsize=img/1024;
            if(imgsize > 2000){
                $('#background_image_error').html('Image size should be less than 2 MB');
                image = false;
            } else  {
                image = true;
            }
        }
        if(image == true){
            return true;
        } else {
            return false;
        }
        // DO STUFF...
        return false; // return false to cancel form action
    });
</script>
@endsection