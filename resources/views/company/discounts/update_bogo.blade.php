@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/discounts') }}">Discounts</a></li>
                    <li class="active">Add</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::open(['id' => 'submit_form','url' => 'company/discounts/update/bogo', 'data-toggle' => 'validator','data-disable' => 'false', 'class' => 'form-horizontal', 'files' => true]) !!}

                @include ('company.discounts.update_bogo_form')

            {!! Form::close() !!}
            
    </section>
</section>

@endsection