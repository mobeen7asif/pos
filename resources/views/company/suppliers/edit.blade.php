@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/suppliers') }}">Suppliers</a></li>
                    <li class="active">Update</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::model($supplier, [
                'method' => 'PATCH',
                'url' => ['/company/suppliers', Hashids::encode($supplier->id)],
                'class' => 'form-horizontal',
                'files' => true,
                'data-toggle' => 'validator',
                'data-disable' => 'false',
                'id' => 'submit_supplier'
                ]) !!}
                
                @include ('company.suppliers.form', ['submitButtonText' => 'Update'])    

            {!! Form::close() !!}
            
    </section>
</section>


@endsection
