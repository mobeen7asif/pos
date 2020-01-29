@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    {{--<li><a href="{{ url('company/ads') }}">Ads</a></li>--}}
                    {{--<li class="active">Add</li>--}}
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::open(['url' => 'company/upload/products_csv', 'data-toggle' => 'validator', 'data-disable' => 'false', 'class' => 'form-horizontal', 'files' => true,'id' => 'create_ad']) !!}

                @include ('company.csv.create_product_form')

            {!! Form::close() !!}
            
    </section>
</section>
@endsection