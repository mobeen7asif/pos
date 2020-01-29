@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/modifiers') }}">Modifiers</a></li>
                    <li class="active">Update</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::model($modifier, [
                'method' => 'PATCH',
                'url' => ['/company/modifiers', Hashids::encode($modifier->id)],
                'class' => 'form-horizontal',
                'files' => true,
                'data-toggle' => 'validator',
                'id' => 'modifier_form',
                ]) !!}
                
                @include ('company.modifiers.form', ['submitButtonText' => 'Update'])    

            {!! Form::close() !!}
            
    </section>
</section>


@endsection
