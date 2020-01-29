@extends('company.layouts.app')

@section('content')

<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li><a href="{{ url('company/ads') }}">Ads</a></li>
                    <li class="active">Update</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
            {!! Form::model($ad, [
                'method' => 'post',
                'url' => ['/company/ad_update', Hashids::encode($ad->id)],
                'class' => 'form-horizontal',
                'files' => true,
                'data-toggle' => 'validator',
                'data-disable' => 'false',
                'id' => 'update_ad'
                ]) !!}
                
                @include ('company.ads.form', ['submitButtonText' => 'Update'])

            {!! Form::close() !!}
            
    </section>
</section>


@endsection
