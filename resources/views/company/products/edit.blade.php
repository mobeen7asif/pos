
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
                    <li class="active">Update</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
            
        @php($current_tab = app('request')->input('tab'))
        @switch($current_tab)
            @case(1)
                @php($url = '/company/products/'.Hashids::encode($product->id))
                @break

            @case(2)
                @php($url = '/company/products/update-store/'.Hashids::encode($product->id))
                @break

            @case(3)
                @php($url = '/company/products/update-combo-products/'.Hashids::encode($product->id))
                @break

            @default
                @php($url = '/company/products/'.Hashids::encode($product->id))
        @endswitch 
        
        @if($current_tab == 4)
        
            @if($product->is_variants == 1)
                @include ('company.products.variant_form')
            @elseif($product->is_modifier == 1)
                @include ('company.products.modifier_form')
            @endif
            
        @else  
            
            {!! Form::model($product, [
                'method' => 'PATCH',
                'url' => $url,
                'files' => true,
                'data-toggle' => 'validator',
                'data-disable' => 'false',
                'id' =>'product_form'
                ]) !!}
                
                @include ('company.products.form', ['submitButtonText' => 'Update'])

            {!! Form::close() !!}
            
        @endif
    </section>
</section>

@endsection