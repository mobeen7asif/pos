@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/users') }}">Employees</a></li>
                    <li class="active">Update Employee</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::model($user, [
                    'method' => 'PATCH',
                    'url' => ['/company/users', Hashids::encode($user->id)],
                    'class' => 'form-horizontal',
                    'data-toggle' => 'validator',
                    'data-disable' => 'false',
                    'files' => true
                ]) !!}

                @include ('company.users.form', ['submitButtonText' => 'Update'])

                {!! Form::close() !!}
            
    </section>
</section>


@endsection

