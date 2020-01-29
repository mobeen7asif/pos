@extends('company.layouts.app')

@section('css')
<style>
    td span.details-control {
        background: url(../images/details_open.png) no-repeat center center;
        cursor: pointer;
        width: 18px;
        padding: 12px;
    }
    tr.shown td span.details-control {
        background: url(../images/details_close.png) no-repeat center center;
    }
    td {vertical-align: middle !important; }
</style>
@endsection
@section('content')
<section id="main-content" >
    <section class="wrapper">
        <div class="row">
            <div class="col-md-12">
                <!--breadcrumbs start -->
                <ul class="breadcrumb">
                    <li><a href="{{ url('company/dashboard') }}"><i class="fa fa-home"></i> Dashboard</a></li>
                    <li class="active">Products</li>
                </ul>
                <!--breadcrumbs end -->
            </div>
        </div>                
        
         <div class="row">
            <div class="col-sm-12">
                <section class="panel">
                    <header class="panel-heading">
                        Products
                        <span class="pull-right">
                            <a href="{{ url('company/import/products') }}" class="btn btn-info btn-sm" title="Import Products">
                                <i class="fa fa-plus" aria-hidden="true"></i> Import Products
                            </a>
                            <a href="javascript:void(0)" class="btn btn-success btn-sm refresh_products" data-toggle="tooltip" title="Refresh Products"><i class="fa fa-refresh" aria-hidden="true"></i></a>
                            <a href="{{ url('company/products/create') }}" class="btn btn-info btn-sm" data-toggle="tooltip" title="Add New Product"><i class="fa fa-plus" aria-hidden="true"></i></a>                            
                         </span>
                    </header>
                    <div class="panel-body">
                        <table id="datatable" class="table table-bordered table-striped">
                            <thead>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Code</th>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Supplier</th>
                                <th>Cost</th>
                                <th>Price</th>
                                <th>Action</th>
                            </tr>
                            </thead>
                            <tbody>
                                
                            </tbody>
                            <tfoot>
                            <tr>
                                <th></th>
                                <th>ID</th>
                                <th>Image</th>
                                <th>Code</th>
                                <th>SKU</th>
                                <th>Name</th>
                                <th>Supplier</th>
                                <th>Cost</th>
                                <th>Price</th>
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
    var upload_url = '{{ asset("uploads") }}';
    
$(document).ready(function () {
    
    var table = $('#datatable').DataTable({
      oLanguage: { sProcessing: '<img src="'+ base_url +'/images/bx_loader.gif">' },
      processing: true,
      serverSide: true,
      ordering: true,
      responsive: true,
      ajax: "{{url('company/get-products')}}",
      columns: [
            {data: 'is_variants', orderable:false, searchable: false},
            {data: 'id', name: 'id'},
            {data: 'product_image', name: 'product_image', width: "10%", className: 'text-center'},
            {data: 'code', name: 'code', className: 'text-center'},
            {data: 'sku', name: 'sku', className: 'text-center'},
            {data: 'name', name: 'name'},                 
            {data: 'supplier', name: 'supplier', className: 'text-center'},                
            {data: 'cost', name: 'cost', className: 'text-center'},                 
            {data: 'price', name: 'price', className: 'text-center'},                      
            {data: 'action', name: 'action', width: "10%", orderable: false, searchable: false, className: 'text-center'}
        ],
      order: [],
      drawCallback: function( settings ) {
        var api = this.api();
 
        // Output the data for the visible rows to the browser's console
        console.log( api.rows( {page:'current'} ).data() );
    }
    });
        
        $('#datatable tbody').on('click', 'td span.details-control', function () 
        {
            var tr = $(this).closest('tr');
            var row = table.row( tr );

            if ( row.child.isShown() ) {
                row.child.hide();
                tr.removeClass('shown');
            }
            else {                
                row.child( format(row.data()) ).show();
                tr.addClass('shown');
            }
        } );
        
        var reload_datatable = $("#datatable").dataTable( { bRetrieve : true } );
        
        $(document).on('click', '.refresh_products', function (e) { 
            reload_datatable.fnDraw(); 
        });        
        
        $('#datatable').on('click', '.btn-delete', function (e) { 
        e.preventDefault();
        var id = $(this).attr('id');
        var url= "{{ url('company/products') }}"+'/'+id;
        var method = "delete";
        
        remove_record(url,reload_datatable,method);
        
        }); 
      });
      
    function format ( rowData ) {
        var div = $('<div/>').addClass( 'loading' ).text( 'Loading...' );
        var products = rowData.products;
        
        var product_html = '<table class="table table-bordered table-striped">\
                            <thead><tr>\
                                <th class="text-center">Image</th>\
                                <th class="text-center">Code</th>\
                                <th class="text-center">SKU</th>\
                                <th>Name</th>\
                                <th class="text-center">Cost</th>\
                                <th class="text-center">Price</th>\
                                <th class="text-center">Action</th>\
                            </tr></thead><tbody>';
                                
        if(products.length>0){
            console.log(products);
    
            $.each(products,function(index, product){                
                
                var image = '<img width="30" src="'+ upload_url +'/no_image.png" />';
                if(product.product_images.length>0){
                    image = '<img width="30" src="' + upload_url +'/products/thumbs/'+ product.product_images[0].name +'" />'
                }
                
                var cost = '-';
                var price = '-';
                if(product.is_main_price==0){
                    cost = product.cost;
                    price = product.price;
                }                
                
                product_html += '<tr>\
                                    <td class="text-center" width="10%">'+ image +'</td>\
                                    <td class="text-center" width="10%">'+ product.code +'</td>\
                                    <td class="text-center">'+ product.sku +'</td>\
                                    <td>'+ product.name +'</td>\
                                    <td class="text-center" width="10%">'+ cost +'</td>\
                                    <td class="text-center" width="10%">'+ price +'</td>\
                                    <td class="text-center" width="10%"><a href="products/edit/' + product.encoded_id +'" class="text-primary" data-toggle="tooltip" title="Edit Product"><i class="fa fa-edit"></i></a>\
                                        <a href="product-stocks/' + product.encoded_id +'" class="text-success" data-toggle="tooltip" title="Stock History"><i class="fa fa-history"></i></a>\
                                        <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Product" id="'+ product.id +'"><i class="fa fa-trash"></i></a>\
                                    </td>\
                                </tr>';

            });
        }else{
                        
            product_html += '<tr><td colspan="7">Record not found</td></tr> ';
        }
        
        
        
        
        product_html += '</tbody></table>';
        
        div.html( product_html ).removeClass( 'loading' );
    
//        $.ajax( {
//        url: '/api/staff/details',
//        data: {
//            name: rowData.name
//        },
//        dataType: 'json',
//        success: function ( json ) {
//            div
//                .html( json.html )
//                .removeClass( 'loading' );
//        }
//    } );
 
    return div;
}  
</script>
@endsection                            
