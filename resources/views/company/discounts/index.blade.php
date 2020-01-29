@extends('company.layouts.app')

@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Discounts</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Discounts
                        <span class="tools pull-right">
                            <a href="{{ url('company/discounts/create').'/'.Hashids::encode($store->id) }}" class="btn btn-info btn-sm" title="Add New Discount">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add New
                            </a>
                         </span>

                        <span class="tools pull-right">
                            <a href="{{ url('company/discounts/bogo')}}" class="btn btn-info btn-sm" title="Add Bogo Discount">
                                <i class="fa fa-plus" aria-hidden="true"></i> Add Bogo Discount
                            </a>
                         </span>
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th>Time</th>
                                <th>Discount Detail</th>
                                <th>Discount Amount</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th>Time</th>
                                <th>Discount Detail</th>
                                <th>Discount Amount</th>
                                <th>Action</th>
                            </tr>
                          </tfoot>
                        </table>
                    </div>
                </section>
            </div>
        </div>   
            
    </section>
</section>
      
@endsection


@section('scripts')
<script type="text/javascript">
$("document").ready(function () {
        var datatable_url = "{{url('company/get-discounts')}}";
        var datatable_columns = [
            {data: 'date_time', name: 'date_time', width: '10%'},
            {data: 'discount_detail', name: 'categories', width: '40%'},
            {data: 'amount', name: 'amount', width: '10%',className: 'text-center'},
            {data: 'action', name: 'action', width: '10%', orderable: false, searchable: false}
            ];
        
        $('#datatable').DataTable({
            oLanguage: { sProcessing: '<img src="'+ base_url +'/images/bx_loader.gif">' },
            processing: true,
            serverSide: true,
            ajax: {
                url: datatable_url,
                data : function(d){
                    if($(".filter_by_store").val() != ''){
                        d.columns[2]['search']['value'] = $(".filter_by_store option:selected").text();
                    }                    
                    }
            }, 
            columns: datatable_columns,
            "order": []
        });
        
        //$("#datatable_length").append('{!! Form::select("type", getStoresFilterDropdown(), null, ["class" => "form-control input-sm filter_by_store","style"=>"margin-left: 20px;"]) !!}');
        
        var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
        
        $(document).on('change', '.filter_by_store', function (e) {
            reload_datatable.fnDraw();
        });
        
        $('#datatable').on('click', '.btn-delete', function (e) { 
        e.preventDefault();
        var id = $(this).attr('id');
        var url= "{{ url('company/discounts') }}"+'/'+id;
        var method = "delete";
        
        remove_record(url,reload_datatable,method);
        
        }); 
      });            
</script>
@endsection                            
