@extends('admin.layouts.app')

@section('content')
<div class="content-wrapper">
    <section class="content-header">
      <h1>Edit Profile</h1>
      <ol class="breadcrumb">
        <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>           
        <li class="active">Edit Profile</li>
    </ol>
</section>

<section class="content">
    <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
      <div class="box box-info">

        {!! Form::open(['url' => '/admin/profile/update', 'class' => 'form-horizontal', 'files' => true]) !!}

        <div class="box-header with-border">
            <h3 class="box-title">Edit Profile</h3>
        </div><!-- /.box-header -->                
        <div class="box-body">

        <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
            {!! Form::label('name', 'Name', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-6">
                {!! Form::text('name', $profile->name, ['class' => 'form-control']) !!}
                {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        <div class="form-group {{ $errors->has('email') ? 'has-error' : ''}}">
            {!! Form::label('email', 'Email Address', ['class' => 'col-md-4 control-label']) !!}
            <div class="col-md-6">
                {!! Form::email('email', $profile->email, ['class' => 'form-control']) !!}
                {!! $errors->first('email', '<p class="help-block">:message</p>') !!}
            </div>
        </div>
        
        <div class="form-group {{ $errors->has('profile_image') ? 'has-error' : ''}}">
            {!! Form::label('profile_image', 'Logo ', ['class' => 'col-sm-4 control-label']) !!}
            <div class="col-sm-6">
                <div id="image-preview"
                @if ($profile->profile_image != '')
                    style="background-image: url('{{ asset('uploads/admin_avatar/'.$profile->profile_image) }}');background-size: cover;background-position: center center;"
                @else
                    style="background-image: url('{{ asset('uploads/admin_avatar/admin.jpg') }}');background-size: cover;background-position: center center;"
                @endif>
                <label for="image-upload" id="image-label">Choose Logo</label>
                <input type="file" name="profile_image" id="image-upload" />                       
            </div>
            {!! $errors->first('profile_image', '<p class="help-block">:message</p>') !!}
        </div>
    </div>
    </div><!-- /.box-body -->
    <div class="box-footer"> 
    {!! Form::submit('Update Profile', ['class' => 'btn btn-info pull-right']) !!}
    </div><!-- /.box-footer -->

    {!! Form::close() !!}

    </div>
    </div>
    </div>

</section>      
</div>

@endsection

@section('scripts')
<script type="text/javascript">
    $(document).ready(function(){
        $.uploadPreview({
        input_field: "#image-upload",   // Default: .image-upload
        preview_box: "#image-preview",  // Default: .image-preview
        label_field: "#image-label",    // Default: .image-label
        label_default: "Choose Logo",   // Default: Choose File
        label_selected: "Change Logo",  // Default: Change File
        no_label: false                 // Default: false
        });

    });
</script>
@endsection

