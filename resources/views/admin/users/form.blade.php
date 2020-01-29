
<div class="box-header with-border">
    <h3 class="box-title">Update Profile</h3>
</div><!-- /.box-header -->                
<div class="box-body">
  <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
    {!! Form::label('name', 'Name', ['class' => 'col-md-4 control-label']) !!}
    <div class="col-md-6">
        {!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Name']) !!}
        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
    </div>
  </div>
    
  <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
    {!! Form::label('email', 'Email', ['class' => 'col-md-4 control-label']) !!}
    <div class="col-md-6">
        {!! Form::text('email', null, ['class' => 'form-control','placeholder'=>'Email']) !!}
        {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
    </div>
  </div>

  <div class="form-group {{ $errors->has('dob') ? 'has-error' : ''}}">
    {!! Form::label('dob', 'Date of birth', ['class' => 'col-md-4 control-label']) !!}
    <div class="col-md-6">
        {!! Form::text('dob', null, ['class' => 'form-control date']) !!}
        {!! $errors->first('dob', '<p class="help-block">:message</p>') !!}
    </div>
  </div>
  
  <div class="form-group {{ $errors->has('pin') ? 'has-error' : ''}}">
    {!! Form::label('pin', 'Pin', ['class' => 'col-md-4 control-label']) !!}
    <div class="col-md-6">
        {!! Form::text('pin', null, ['class' => 'form-control pin' ]) !!}
        {!! $errors->first('pin', '<p class="help-block">:message</p>') !!}
    </div>
  </div>  
    
  <div class="form-group {{ $errors->has('blood_group') ? 'has-error' : ''}}">
    {!! Form::label('blood_group', 'Blood Group', ['class' => 'col-md-4 control-label']) !!}
    <div class="col-md-6">
        {{ Form::select('blood_group',  [''=>'Select blood group','A+'=>'A+','A-'=>'A-','B+'=>'B+','B-'=>'B-','O+'=>'O+','O-'=>'O-','AB+'=>'AB+','AB-'=>'AB-'], $user->blood_group,['class' => 'form-control']) }}
        {!! $errors->first('blood_group', '<p class="help-block">:message</p>') !!}
    </div>
  </div>      
    
    <div class="form-group {{ $errors->has('profile_status') ? 'has-error' : ''}}">
        {!! Form::label('profile_status', 'Status', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-6">
            {!! Form::text('profile_status', null, ['class' => 'form-control','placeholder'=>"Tell your friends what you're upto"]) !!}
            {!! $errors->first('profile_status', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    
    <div class="form-group {{ $errors->has('active') ? 'has-error' : ''}}">
        {!! Form::label('active', 'Active Status', ['class' => 'col-md-4 control-label']) !!}
        <div class="col-md-6">
            {!! Form::radio('active', 1, null) !!} Active
            {!! Form::radio('active', 0, null) !!} Inactive
            {!! $errors->first('active', '<p class="help-block">:message</p>') !!}
        </div>
      </div>
    
    <div class="form-group {{ $errors->has('profile_image') ? 'has-error' : ''}}">
        {!! Form::label('profile_image', 'Profile Image: ', ['class' => 'col-sm-4 control-label']) !!}
        <div class="col-sm-6">
            <div id="image-preview" style="background-image: url('{{ checkImage('users/'. $user->profile_image) }}');background-size: cover;background-position: center center;">
                <label for="image-upload" id="image-label">Choose File</label>
                <input type="file" name="profile_image" id="image-upload" />                       
            </div>
            {!! $errors->first('profile_image', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
</div><!-- /.box-body -->
<div class="box-footer"> 
  {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
</div><!-- /.box-footer -->

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        
        $('.pin').inputmask("999999");
        $('.date').inputmask('dd/mm/yyyy', { 'placeholder': 'dd/mm/yyyy' });
        
        $.uploadPreview({
          input_field: "#image-upload",   // Default: .image-upload
          preview_box: "#image-preview",  // Default: .image-preview
          label_field: "#image-label",    // Default: .image-label
          label_default: "Choose File",   // Default: Choose File
          label_selected: "Change File",  // Default: Change File
          no_label: false                 // Default: false
        });
        
    });
</script>
@endsection
