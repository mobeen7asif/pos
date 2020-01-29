@extends('admin.layouts.app')

@section('content')

<div class="content-wrapper">
        <section class="content-header">
          <h1>Record Details</h1>
            <ol class="breadcrumb">
            <li><a href="{{ url('admin/dashboard') }}"><i class="fa fa-dashboard"></i> Dashboar</a></li>
            <li><a href="{{ url('admin/items') }}">User Records</a></li>
            <li class="active">Record Details</li>
          </ol>
        </section>

        <section class="content">

      <div class="row">
        <div class="col-md-3">

          <!-- Profile Image -->
          <div class="box box-primary">
            <div class="box-body box-profile">

              <h3 class="profile-username text-center">{{$item->title}}</h3>

              <p class="text-muted text-center">{{$item->description}}</p>

              <ul class="list-group list-group-unbordered">
                <li class="list-group-item">
                  <b>Total Comments</b> <a class="pull-right">{{$item->total_comments}}</a>
                </li>
                
              </ul>

            </div>
            <!-- /.box-body -->
          </div>
          <!-- /.box -->

          <!-- About Me Box -->
          
          <!-- /.box -->
        </div>
        <!-- /.col -->
        <div class="col-md-9">
          <div class="nav-tabs-custom">
            <ul class="nav nav-tabs">
              <li class="active"><a href="#settings" data-toggle="tab" aria-expanded="true">Details</a></li>
            </ul>
            <div class="tab-content">
              
              <!-- /.tab-pane -->
              <!-- /.tab-pane -->

              <div class="tab-pane active" id="settings">
                <form class="form-horizontal">
                    <div class="form-group">
                    {!! Form::label('title', 'Title: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$item->title}}</span>
                        </div>
                    </div>

                    <div class="form-group">
                    {!! Form::label('category_name', 'Category: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$category->category_name}}</span>
                        </div>
                    </div>

                    <div class="form-group">
                    {!! Form::label('total_comments', 'Comments: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$item->total_comments}}</span>
                        </div>
                    </div>

                    <div class="form-group">
                    {!! Form::label('description', 'Description: ', ['class' => 'col-sm-2 control-label']) !!}
                        <div class="col-sm-10 align-text">
                            <span>{{$item->description}}</span>
                        </div>
                    </div>

                  <!-- <div class="form-group">
                    <label for="inputExperience" class="col-sm-2 control-label">Experience</label>
                  
                    <div class="col-sm-10">
                      <textarea class="form-control" id="inputExperience" placeholder="Experience"></textarea>
                    </div>
                  </div> -->

                  <!-- <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <div class="checkbox">
                        <label>
                          <input type="checkbox"> I agree to the <a href="#">terms and conditions</a>
                        </label>
                      </div>
                    </div>
                  </div>
                  <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                      <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                  </div> -->
                </form>
              </div>
              <!-- /.tab-pane -->
            </div>
            <!-- /.tab-content -->
          </div>
          <!-- /.nav-tabs-custom -->
        </div>
        <!-- /.col -->
      </div>
      <!-- /.row -->

    </section>  
    </div>

@endsection
