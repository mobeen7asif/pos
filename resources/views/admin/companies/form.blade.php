
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">Basic Information</header>
            <div class="panel-body">
                <div class="position-center">
                    
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                        {!! Form::label('name', 'Company Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Company Name','required' => 'required']) !!}
                            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>


                    <div class="form-group {{ $errors->has('company_type') ? 'has-error' : ''}}">
                        {!! Form::label('company_type', 'Company Type', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::select('company_type', getCompanyTypes(),null, ['class' => 'form-control','required' => 'required']) !!}
                            {!! $errors->first('company_type', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>


                    <div class="form-group {{ $errors->has('country') ? 'has-error' : ''}}">
                        {!! Form::label('country', 'Country', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::select('country', getCountries(),null, ['class' => 'form-control','required' => 'required']) !!}
                            {!! $errors->first('country', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group {{ $errors->has('state') ? 'has-error' : ''}}">
                        {!! Form::label('state', 'State/Province', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('state', null, ['class' => 'form-control','placeholder' => 'State/Province Name']) !!}
                            {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
                        {!! Form::label('city', 'City', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('city', null, ['class' => 'form-control','placeholder' => 'City Name']) !!}
                            {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group {{ $errors->has('zip') ? 'has-error' : ''}}">
                        {!! Form::label('zip', 'Zip Code', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('zip', null, ['class' => 'form-control','placeholder' => 'Zip Code']) !!}
                            {!! $errors->first('zip', '<p class="help-block">:message</p>') !!}
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
                    
                    <div class="form-group {{ $errors->has('phone') ? 'has-error' : ''}}">
                        {!! Form::label('phone', 'Phone #', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('phone', null, ['class' => 'form-control','placeholder' => 'Phone #']) !!}
                            {!! $errors->first('phone', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group {{ $errors->has('mobile') ? 'has-error' : ''}}">
                        {!! Form::label('mobile', 'Mobile #', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('mobile', null, ['class' => 'form-control','placeholder' => 'Mobile #']) !!}
                            {!! $errors->first('mobile', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>
                    
                    <div class="form-group last {{ $errors->has('logo') ? 'has-error' : ''}}">
                    {!! Form::label('logo', 'Company Logo', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}                        
                    <div class="col-md-9">
                        <div class="fileupload fileupload-new" data-provides="fileupload">
                            <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                @if(@$company->logo != '')
                                    <img src="{{ checkImage('companies/'. $company->logo) }}" alt="" />
                                @else
                                    <img src="{{ asset('images/no-image.png') }}" alt="" />
                                @endif
                                
                            </div>
                            <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                            <div>
                                <span class="btn btn-white btn-file">
                                <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                <input type="file" class="default" name="logo" accept="image/*" />
                                </span>
                                <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                            </div>
                            {!! $errors->first('logo', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>                        
                    </div>
                </div>

                @if(@$company)
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
                    </div>
                </div>
                @endif
                </div>
            </div>
        </section>

    </div>
    
    @if(@!$company)
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
                    
                    <div class="form-group {{ $errors->has('password') ? 'has-error' : ''}}">
                        {!! Form::label('password', 'Password', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::password('password', ['class' => 'form-control','placeholder' => 'Password','required' => 'required','data-minlength' => 6]) !!}
                            {!! $errors->first('password', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>  
                    
                    <div class="form-group {{ $errors->has('password_confirmation') ? 'has-error' : ''}}">
                        {!! Form::label('password_confirmation', 'Confirm Password', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::password('password_confirmation', ['class' => 'form-control','placeholder' => 'Confirm Password','required' => 'required','data-match'=>'#password']) !!}
                            {!! $errors->first('password_confirmation', '<p class="help-block">:message</p>') !!}
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
<script type="text/javascript">
    $(document).ready(function(){
        $("#country").select2();
        $("#company_type").select2({
            placeholder: "Select Company Type",
        });
    });
</script>
@endsection