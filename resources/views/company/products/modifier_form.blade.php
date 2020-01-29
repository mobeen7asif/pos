
@section('css')
    <style>
        
    </style>    
@endsection


<ul class="nav nav-tabs">
        <li><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=1') }}">Product Information</a></li>
        <li><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=2') }}">Store & Categories</a></li>        
    @if($product->type == 2)
        <li><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=3') }}">Combo Products</a></li>
    @endif
        <li class="active"><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=4') }}">Modifiers</a></li>
</ul>

<div class="tab-content">
    <!-- Vantiants -->
    <div class="tab-pane fade in active"> 
        <div class="row">            
            <div class="col-lg-12">
                <section class="panel">
                    <div class="panel-body">                                                                             

                        <div class="row"> 
                            
                            <div class="col-sm-6">   
                                <section class="panel" id="modifiers_panel">
                                    <header class="panel-heading">
                                        Product Modifiers                                        
                                    </header>
                                    <div class="panel-body">
                                        <table class="table  table-hover general-table" id="modifiers_table">
                                            <thead>
                                            <tr>
                                                <th></th>
                                                <th>Name</th>
                                                <th>Price</th>
                                            </tr>
                                            </thead>

                                            <tbody> 
                                                <tr>
                                                    <td colspan="3"> Record not found</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </section>
                            </div>

                            <div class="col-sm-6" id="modifier">
                                <section class="panel" id="modifiers_panel">
                                    <header class="panel-heading">
                                        Number of Modifiers
                                    </header>
                                    <div class="panel-body">
                                        <div style="color: red" class="help-block with-errors"></div>
                                        <div class="form-group">
                                            <input id="min_check" type="checkbox" data-type="checkbox" data-guid="236f2159-1db8-82b9-779d-90474ad7a919" data-checkbox="true">
                                            <label class="control-label">Min Modifier <b></b></label>
                                            <input disabled oninput="this.value = Math.abs(this.value)" id="min_modifier" value="{{$product->min_modifier}}" type="number" name="store_quantity_store-2" class="form-control" min="0">
                                        </div>
                                        <div class="form-group">
                                            <input id="max_check" type="checkbox" data-type="checkbox" data-guid="236f2159-1db8-82b9-779d-90474ad7a919" data-checkbox="true">
                                            <label class="control-label">Max Modifier <b></b></label>
                                            <input disabled oninput="this.value = Math.abs(this.value)" id="max_modifier" value="{{$product->max_modifier}}" type="number" name="store_quantity_store-2" class="form-control" min="0" >
                                        </div>
                                        <input class="btn btn-info pull-right saveModifier" value="Save">
                                    </div>
                                </section>
                            </div>

                        </div>                                                                                                       
                    </div>
                </section>
            </div>
        </div>
    </div>   
  
</div>

@section('scripts')

<script type="text/javascript">

    $(document).ready(function(){

        get_product_modifiers();
  
        //set default
        $(document).on('click','.is_checked',function() { 
            
            $("#modifiers_table").LoadingOverlay("show");
            
             var product_id = {{ $product->id }};
            var modifier_id = $(this).val();
            var value = 0;
            
            if ($(this).is(':checked')) {
                value = 1;    
            }
            
            $.ajax({
                url : "{{url('company/products/set-product-modifier')}}",
                type: 'post',
                data: {'product_id':product_id,'modifier_id':modifier_id,'value':value},
                success: function (result) {
                    $("#modifiers_table").LoadingOverlay("hide");
                }

               });
        }); 
                                                          
        });  
    
    function get_product_modifiers()
    {
        $("#modifiers_table").LoadingOverlay("show");
        $("#modifiers_table tbody").html('<tr><td colspan="3"> Record not found</td></tr>');
        $.ajax({
            url : "{{url('company/products/get-product-modifiers/'.$product->id)}}",
            type: 'get',
            success: function (result) {                  
               
              var product_modifiers = result.product_modifiers;
              
              if(product_modifiers.length > 0)
                  $("#modifiers_table tbody").html('');
               
              $.each(product_modifiers,function(index, value){
                  var checked="";
                  if(value.is_checked)
                      checked="checked"; 
                  
                  $("#modifiers_table tbody").append('\
                    <tr>\
                        <td><input type="checkbox" class="is_checked" value="'+ value.id +'" '+ checked +' /></td>\
                        <td width="620px">'+ value.name +'</b></td>\
                        <td>'+ value.price +'</td>\
                    </tr>\
                    ');
              });                            
              
              $("#modifiers_table").LoadingOverlay("hide");
            }

           });
    }
    var min_check = 0;
    var max_check = 0;
    $('#min_check').click(function () {
        if($(this).is(":checked")){
            $('#min_modifier').prop('disabled', false);
            min_check = 1;
        } else {
            $('#min_modifier').prop('disabled', true);
            min_check = 0;
        }
    });
    $('#max_check').click(function () {
        if($(this).is(":checked")){
            $('#max_modifier').prop('disabled', false);
            max_check = 1;
        } else {
            $('#max_modifier').prop('disabled', true);
            min_check = 0;
        }
    });
    $('.saveModifier').click(function () {
        var min = $('#min_modifier').val();
        var max = $('#max_modifier').val();
        if(min_check === 0){
            min = 0;
        }
        if(max_check === 0){
            max = 0;
        }
        // if(min.length === 0 || max.length === 0){
        //     $('.with-errors').html('Please select value')
        // } else {
            if(max < min){
                $('.with-errors').html('Max value should be greater than Min value')
            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                //console.log('min---'+min+'------'+max);
                var url = "{{url('company/products/save-modifier-numbers')}}";
                var data_object = {min:min,max:max,product_id:"{{ $product->id }}"};
                $("#modifier").LoadingOverlay("show");
                $.ajax({
                    url:url,
                    type:"post",
                    data:data_object,
                    success:function (result) {
                        $('.with-errors').html('')
                        $("#modifier").LoadingOverlay("hide");
                    }//.... end of success.
                });//..... end of ajax() .....//
            }
        //}

    });
    
</script>
@endsection


<!--<a href="javascript:void(0)" class="text-info btn-edit" data-toggle="tooltip" title="Edit Product Attribute" data-id="'+ value.id +'" data-variant_id="'+ value.variant_id +'"><i class="fa fa-pencil"></i></a>\
                            <a href="javascript:void(0)" class="text-danger btn-delete" data-toggle="tooltip" title="Delete Product Attribute" data-id="'+ value.id +'"><i class="fa fa-trash"></i></a>-->
