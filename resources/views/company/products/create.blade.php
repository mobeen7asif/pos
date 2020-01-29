@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/products') }}">Products</a></li>
                    <li class="active">Add</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::open(['url' => 'company/products', 'data-toggle' => 'validator', 'data-disable' => 'false', 'id' => 'product_form', 'files' => true]) !!}

                @include ('company.products.form')

            {!! Form::close() !!}
            
    </section>
</section>

@endsection