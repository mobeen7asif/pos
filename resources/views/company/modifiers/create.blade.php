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
                    <li class="active">Add</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::open(['url' => 'company/modifiers', 'id' => 'modifier_form', 'data-toggle' => 'validator', 'class' => 'form-horizontal', 'files' => true]) !!}

                @include ('company.modifiers.form')

            {!! Form::close() !!}
            
    </section>
</section>


@endsection