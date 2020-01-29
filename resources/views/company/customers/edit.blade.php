@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/customers') }}">Customers</a></li>
                    <li class="active">Update Customer</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::model($customer, [
                    'method' => 'PATCH',
                    'url' => ['/company/customers', base64_encode($customer->id)],
                    'class' => 'form-horizontal',
                    'data-toggle' => 'validator',
                    'data-disable' => 'false',
                    'files' => true
                ]) !!}

                @include ('company.customers.form', ['submitButtonText' => 'Update'])

                {!! Form::close() !!}
            
    </section>
</section>


@endsection

