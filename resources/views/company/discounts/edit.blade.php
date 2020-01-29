
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
                    <li class="active">Update</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>
            {!! Form::model($discount, [
                'method' => 'post',
                'url' => ['/company/discounts', Hashids::encode($discount->id)],
                'class' => 'form-horizontal',
                'files' => true,
                'data-toggle' => 'validator',
                'data-disable' => 'false',
                'id' =>'category_form'
                ]) !!}
                
                @include ('company.discounts.edit_form', ['submitButtonText' => 'Update'])

            {!! Form::close() !!}
            
    </section>
</section>

@endsection