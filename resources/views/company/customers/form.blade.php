
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Customer</header>
            <div class="panel-body">
                <div class="position-center">                    
                                       
                  {{--<div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">--}}
                    {{--{!! Form::label('store_id', 'Store', ['class' => 'col-md-3 control-label required-input']) !!}--}}
                    {{--<div class="col-md-9">--}}
                        {{--{!! Form::select('store_id', getStoresDropdown(),@$store->id, ['class' => 'form-control select2','required' => 'required']) !!}--}}
                        {{--{!! $errors->first('store_id', '<p class="help-block">:message</p>') !!}--}}
                        {{--<div class="help-block with-errors"></div>--}}
                    {{--</div>--}}
                  {{--</div>--}}
                    
                  <div class="form-group {{ $errors->has('first_name') ? 'has-error' : ''}}">
                    {!! Form::label('first_name', 'First Name', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::text('first_name', null, ['class' => 'form-control','placeholder'=>'First Name','required' => 'required']) !!}
                        {!! $errors->first('first_name', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>

                  <div class="form-group {{ $errors->has('last_name') ? 'has-error' : ''}}">
                    {!! Form::label('last_name', 'Last Name', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::text('last_name', null, ['class' => 'form-control','placeholder' => 'Last Name','required' => 'required']) !!}
                        {!! $errors->first('last_name', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>

                  <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
                    {!! Form::label('email', 'Email', ['class' => 'col-md-3 control-label required-input']) !!}
                    <div class="col-md-9">
                        {!! Form::email('email', null, ['class' => 'form-control','required' => 'required' ]) !!}
                        {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>                                        
                  
                  <div class="form-group {{ $errors->has('image') ? 'has-error' : ''}}">
                        @if(isset($submitButtonText))
                            {!! Form::label('profile_image', 'Profile Image', ['class' => 'col-lg-3 col-sm-3 control-label']) !!}                        
                        @else
                            {!! Form::label('profile_image', 'Profile Image', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}                        
                        @endif
                        <div class="col-md-9">
                            <div class="fileupload fileupload-new" data-provides="fileupload">
                                <div class="fileupload-new thumbnail" style="width: 200px; height: 150px;">
                                    @if(@$customer->image != '')
                                        <img src="{{ checkImage('customers/'. $customer->image) }}" alt="" />
                                    @else
                                        <img src="{{ asset('images/no-image.png') }}" alt="" />
                                    @endif

                                </div>
                                <div class="fileupload-preview fileupload-exists thumbnail" style="max-width: 200px; max-height: 150px; line-height: 20px;"></div>
                                <div>
                                    <span class="btn btn-white btn-file">
                                    <span class="fileupload-new"><i class="fa fa-paper-clip"></i> Select image</span>
                                    <span class="fileupload-exists"><i class="fa fa-undo"></i> Change</span>
                                    <input type="file" class="default" {{ isset($submitButtonText)?'':'required' }} name="profile_image" accept="image/*" />
                                    </span>
                                    <a href="#" class="btn btn-info fileupload-exists" data-dismiss="fileupload"><i class="fa fa-trash"></i> Remove</a>
                                </div>
                                {!! $errors->first('profile_image', '<p class="help-block">:message</p>') !!}
                                <div class="help-block with-errors"></div>
                            </div>                        
                        </div>
                    </div>  
                    
                  <div class="form-group {{ $errors->has('mobile') ? 'has-error' : ''}}">
                    {!! Form::label('mobile', 'Mobile Number', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::text('mobile', null, ['class' => 'form-control' ]) !!}
                        {!! $errors->first('mobile', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>  
                   
                  <div class="form-group {{ $errors->has('ref_code') ? 'has-error' : ''}}">
                    {!! Form::label('ref_code', 'Ref code', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::number('ref_code', null, ['class' => 'form-control','placeholder' => 'Ref code' ]) !!}
                        {!! $errors->first('ref_code', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>  
                    
                  <div class="form-group {{ $errors->has('customer_group_id') ? 'has-error' : ''}}">
                    {!! Form::label('customer_group_id', 'Customer Group', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {{ Form::select('customer_group_id',  $groups, null,['class' => 'form-control select2']) }}
                        {!! $errors->first('customer_group_id', '<p class="help-block">:message</p>') !!}
                    </div>
                  </div>                                                                                                                                                                  
                    
                   <div class="form-group {{ $errors->has('address') ? 'has-error' : ''}}">
                        {!! Form::label('address', 'Address', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::textarea('address', null, ['class' => 'form-control','placeholder' => 'Address','rows' => '2' ]) !!}
                            {!! $errors->first('address', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div> 
                    
                    <div class="form-group {{ $errors->has('country_id') ? 'has-error' : ''}}">
                        {!! Form::label('country_id', 'Country', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {{ Form::select('country_id',  getCountries(), null,['class' => 'form-control select2','required' => 'required']) }}
                            {!! $errors->first('country_id', '<p class="help-block">:message</p>') !!}
                        </div>
                      </div>
                    
                    <div class="form-group {{ $errors->has('state') ? 'has-error' : ''}}">
                    {!! Form::label('state', 'State', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::text('state', null, ['class' => 'form-control','placeholder' => 'State' ]) !!}
                        {!! $errors->first('state', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>
                    
                   <div class="form-group {{ $errors->has('city') ? 'has-error' : ''}}">
                    {!! Form::label('city', 'City', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::text('city', null, ['class' => 'form-control','placeholder' => 'City' ]) !!}
                        {!! $errors->first('city', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>
                    
                    <div class="form-group {{ $errors->has('zip_code') ? 'has-error' : ''}}">
                    {!! Form::label('zip_code', 'Zip code', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        {!! Form::number('zip_code', null, ['class' => 'form-control','placeholder' => 'Zip code' ]) !!}
                        {!! $errors->first('zip_code', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                    </div>
                  </div>
                    
                   <div class="form-group {{ $errors->has('note') ? 'has-error' : ''}}">
                        {!! Form::label('note', 'Note', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            {!! Form::textarea('note', null, ['class' => 'form-control','placeholder' => 'Note','rows' => '3' ]) !!}
                            {!! $errors->first('note', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div> 
                    
                   <div class="form-group {{ $errors->has('current_billing_address') ? 'has-error' : ''}}">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                        {{ Form::checkbox('current_billing_address', 1,null) }}
                        {!! Form::label('current_billing_address', 'Current Billing Address', ['class' => 'control-label']) !!}
                        {!! $errors->first('current_billing_address', '<p class="help-block">:message</p>') !!}
                    </div>
                  </div>
                    
                    <div class="form-group {{ $errors->has('current_shipping_delivery_address') ? 'has-error' : ''}}">
                    <div class="col-md-3"></div>
                    <div class="col-md-9">
                        {{ Form::checkbox('current_shipping_delivery_address', 1,null) }}
                        {!! Form::label('current_shipping_delivery_address', 'Current Shipping/Delivery Address', ['class' => 'control-label']) !!}                    
                        {!! $errors->first('status', '<p class="help-block">:message</p>') !!}
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
