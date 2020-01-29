
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Meal</header>
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
                        {!! Form::label('name', 'Meal Type', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('meal_type', null, ['class' => 'form-control','placeholder' => 'Meal Type','required' => 'required']) !!}
                            {!! $errors->first('meal_type', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('color') ? 'has-error' : ''}}">
                        {!! Form::label('color', 'Color', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('color', null, ['class' => 'form-control jscolor','placeholder' => 'Meal Type','required' => 'required']) !!}
                            {!! $errors->first('meal_type', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('sort_id') ? 'has-error' : ''}}">
                        {!! Form::label('sort_id', 'Sort Number', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::number('sort_id', null, ['oninput'=> 'this.value = Math.abs(this.value)','class' => 'form-control','placeholder'=>'Sort Number','required' => 'required','min' => 1]) !!}
                            {!! $errors->first('sort_id', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('printer_type') ? 'has-error' : ''}}">
                        {!! Form::label('name', "Printer's Type", ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9 radio_class">
                            <div>
                                <input  checked class="printer_type" type="radio" name="printer_type" value="wifi"> Wifi Printer
                            </div>
                            <div>
                                <input   class="printer_type" type="radio" name="printer_type" value="bluetooth"> Bluetooth Printer
                            </div>

                            {!! $errors->first('printer_type', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('ip') ? 'has-error' : ''}}">
                        {!! Form::label('name', "Printer IP Address", ['class' => 'col-lg-3 col-sm-3 control-label printer_label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('ip', null, ['class' => 'form-control printer_placeholder','placeholder' => 'Enter Printer IP Address','id' => 'ip_address']) !!}
                            {!! $errors->first('ip', '<p class="help-block">:message</p>') !!}
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
</div>


@section('scripts')
    <script type="text/javascript" src="{{ asset('plugins/jquery_ip_mask/jquery.mask.js') }}"></script>
<script type="text/javascript">
    //$('#ip_address').mask('099.099.099.099');
    $(document).ready(function(){
            
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
</script>
@endsection