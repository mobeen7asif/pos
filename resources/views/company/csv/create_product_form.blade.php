
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Import' }} Products
                <span class="tools pull-right">
                            <a href="{{ asset('/download/file/products.csv') }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Download Sample File" download>
                                <i class="" aria-hidden="true"></i> Download Sample file
                            </a>
                </span>
            </header>
            <div class="panel-body">
                <div class="position-center" id="form">

                    <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                        {!! Form::label('store_id', 'Store', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::select('store_id', getStoresDropdown(),null, ['class' => 'form-control select2','required' => 'required']) !!}
                            {!! $errors->first('store_id', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('csv_file') ? 'has-error' : ''}}">
                        {!! Form::label('image', 'CSV', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            <div class="custom-upload" style="position: relative;width: 17%;     border: 1px solid #aaa; cursor: pointer">
                                <img src="{{url('/')}}/upload_icon.png" style="display: block;width: 100%">
                                <input id="image_check" sty name="csv_file" class="input_media" type="file" style="position: absolute; left: 0; top: 0; width: 100%;height: 100%;z-index: 1;opacity: 0;cursor: pointer">
                            </div>
                            <span id="file_name"></span>
                            <div class="help-block with-errors" style="color: #a94442" id="csv_file_error">{{ $errors->has('csv_file') ? $errors->first() : ''}}</div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            {!! Form::submit('Upload', ['class' => 'btn btn-info pull-right', ($pending_csv_file >= 1) ? 'disabled' : '']) !!}
                        </div>
                    </div>

                </div>
                <div class="position-center" id="form">
                    <span id="stats" style="text-align: center; display: block; font-weight: bold; font-size: 15px">{!! $stats !!}</span>
                </div>
            </div>
        </section>

    </div>
</div>


@section('scripts')
    <script type="text/javascript">
        var token = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function () {
            if("{{$pending_csv_file}}" > 0) {
                //$(".panel-body").LoadingOverlay("show");
                $('#form').hide();
                $('#stats').show();
                $('.panel-heading').text('Uploading CSV');
                checkPendingFile();
            }
        });
        //$(".panel-body").LoadingOverlay("hide");

        function checkPendingFile() {
            $.ajax({
                url : "{{url('/company')}}" + '/insert-products-file',
                type: 'post',
                data: {},
                success: function (result) {
                    console.log(result);
                    if(result.complete_status === 0){
                        $('#stats').html(result.stats);
                        $('.panel-heading').text('Uploading CSV');
                        setTimeout(checkPendingFile, 5000);
                    } else {
                        $('#stats').html(result.stats);
                        $('.panel-heading').html('CSV Uploaded');
                        toastr.success("Products Added");
                    }
                }
            });
        }
        $('input[type=file]').change(function(e){
            $('#file_name').html(e.target.files[0].name);
        });
    </script>
@endsection