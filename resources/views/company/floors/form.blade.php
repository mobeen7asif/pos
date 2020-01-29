
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Floor</header>
            <div class="panel-body">
                <div class="position-center">

                    <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                        {!! Form::label('store_id', 'Store', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::select('store_id', getStoresDropdown(),$floor->store_id, ['class' => 'form-control select2','required' => 'required']) !!}
                            {!! $errors->first('store_id', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                        {!! Form::label('name', 'Floor Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('name', $floor->name, ['class' => 'form-control','placeholder' => 'Floor Name','required' => 'required']) !!}
                            {!! $errors->first('meal_type', '<p class="help-block">:message</p>') !!}
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
                                    @if(@$floor->image != '')
                                        <img src="{{ checkImage('floors/'. $floor->image) }}" alt="" />
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="" />
                                    @endif

                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                <div>
                                <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                <input type="file" class="default" name="image" accept="image/*" />
                                </span>
                                    <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                </div>
                                {!! $errors->first('image', '<p class="help-block">:message</p>') !!}
                                <div class="help-block with-errors"></div>
                            </div>
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
            
    });
</script>
@endsection