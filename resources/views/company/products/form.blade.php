
@section('css')
    <link href="{{ asset('css/dropzone.css') }}" rel="stylesheet">
    <link href="{{ asset('css/gijgo.min.css') }}" rel="stylesheet">
    <style>
        .cke_inner{border: 1px solid #e2e2e4 !important;border-radius: 4px !important;}
    </style>
@endsection

@php($current_tab = app('request')->input('tab'))
@switch($current_tab)
    @case(1)
        @php($tab1 = 'active')
        @php($tab2 = '')
        @php($tab3 = '')
        @break

    @case(2)
        @php($tab1 = '')
        @php($tab2 = 'active')
        @php($tab3 = '')
        @break
    
    @case(3)
        @php($tab1 = '')
        @php($tab2 = '')
        @php($tab3 = 'active')
        @break

    @default
        @php($tab1 = 'active')
        @php($tab2 = '')
        @php($tab3 = '')
@endswitch

<ul class="nav nav-tabs">
    @if(!isset($submitButtonText))
        <li class="{{ $tab1 }}"><a href="{{ url('company/products/create') }}">Product Information</a></li>
    @else
        <li class="{{ $tab1 }}"><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=1') }}">Product Information</a></li>
        @if($product->type == 1 || $product->type == 2)
            <li class="{{ $tab2 }}"><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=2') }}">Store & Categories</a></li>        
        @endif
        @if($product->type == 3)
            <li class="{{ $tab2 }}"><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=2') }}">Store Stock</a></li>        
        @endif
        @if($product->type == 2)
            <li class="{{ $tab3 }}" id="combo_product_tab"><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=3') }}">Combo Products</a></li>
        @endif
        
        @if($product->is_variants == 1)
            <li><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=4') }}">Variants</a></li>
        @endif
        @if($product->is_modifier == 1)
            <li><a href="{{ url('company/products/'. Hashids::encode($product->id) .'/edit?tab=4') }}">Modifiers</a></li>
        @endif
    @endif    
</ul>

<div class="tab-content">
    @if(!empty($tab1))
        <div id="home" class="tab-pane fade in {{ $tab1 }}"> 
            <div class="row">            
                <div class="col-lg-12">
                    <section class="panel">
                        <div class="panel-body">                                                      

                            <div class="row">
                                <div class="form-group col-md-4 {{ $errors->has('type') ? 'has-error' : ''}}">
                                    {!! Form::label('type', 'Product Type', ['class' => 'control-label required-input']) !!}
                                        {!! Form::select('type', ['1'=>'Standard','2'=>'Combo','3'=>'Modifier'],null, ['id' => 'product_type','class' => 'form-control select2','required' => 'required']) !!}
                                        {!! $errors->first('type', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>        
                                </div>                                 

                                <div class="form-group col-md-4 {{ $errors->has('code') ? 'has-error' : ''}}">
                                    {!! Form::label('code', 'Product Barcode', ['class' => 'control-label required-input']) !!}
                                        <div class="input-group">
                                            {!! Form::text('code', null, ['class' => 'form-control','placeholder'=>'Product Code','required' => 'required']) !!}
                                            <span class="input-group-addon pointer" id="genrate_random_number"><i class="fa fa-random"></i></span>
                                        </div>
            <!--                            <span class="help-block">You can scan your barcode and select the correct symbology below.</span>-->
                                        {!! $errors->first('code', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group col-md-4 {{ $errors->has('name') ? 'has-error' : ''}}">
                                    {!! Form::label('name', 'Product Name', ['class' => 'control-label required-input']) !!}
                                        {!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Product Name','required' => 'required']) !!}
                                        {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>
                            </div>             
                            
                            <div class="row modifier_select">
                                <div class="form-group col-md-3" style="margin-left: 18px;" id="is_variants_text">
                                    {!! Form::checkbox('is_variants', 1, null,['id'=>'is_variants']) !!} <b>This product has variants</b>                    
                                </div>
                                
                                <div class="form-group col-md-3" id="is_modifier_text">
                                    {!! Form::checkbox('is_modifier', 1, null,['id'=>'is_modifier']) !!} <b>This product has modifiers</b>                    
                                </div>
                            </div>
                            
                            <div class="row modifier_select">        
                                {{--<div class="form-group col-md-4 {{ $errors->has('barcode_symbology') ? 'has-error' : ''}}">--}}
                                    {{--{!! Form::label('barcode_symbology', 'Barcode Symbology', ['class' => 'control-label required-input']) !!}--}}
                                        {{--{!! Form::select('barcode_symbology', ['code25'=>'Code25','code39'=>'Code39','code128'=>'Code128','ean8'=>'EAN8','ean13'=>'EAN13','upca'=>'UPC-A','upce'=>'UPC-E'],null, ['class' => 'form-control select2','required' => 'required']) !!}--}}
                                        {{--{!! $errors->first('barcode_symbology', '<p class="help-block">:message</p>') !!}--}}
                                        {{--<div class="help-block with-errors"></div>--}}
                                {{--</div>--}}




                                <div class="form-group variant_checked col-md-4 {{ $errors->has('sku') ? 'has-error' : ''}}">
                                    {!! Form::label('sku', 'SKU', ['class' => 'control-label required-input']) !!}
                                    {!! Form::text('sku', null, ['class' => 'form-control','placeholder'=>'SKU','required' => 'required']) !!}
                                    {!! $errors->first('sku', '<p class="help-block">:message</p>') !!}
                                    <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group  col-md-4 {{ $errors->has('tax_rate_id') ? 'has-error' : ''}}">
                                    {!! Form::label('tax_rate_id', 'Product Tax', ['class' => 'control-label']) !!}
                                        @if(isset($product))
                                            {!! Form::select('tax_rate_id', getTaxRatesDropdown(),null, ['class' => 'form-control select2']) !!}
                                        @else
                                            {!! Form::select('tax_rate_id', getTaxRatesDropdown(),companySettingValue('tax_id'), ['class' => 'form-control select2']) !!}
                                        @endif
                                        {!! $errors->first('tax_rate_id', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>

                                <div class="form-group  col-md-4 {{ $errors->has('tax_rate_id') ? 'has-error' : ''}}">
                                    {!! Form::label('tax_rate_id', 'This product has duty tax', ['class' => 'control-label']) !!}
                                    {!! Form::checkbox('is_duty', 1, null) !!}
                                    <div class="help-block with-errors"></div>
                                </div>


                                
                            </div>
                            
                            <div class="row variant_checked">

                                <div class="form-group col-md-4 {{ $errors->has('supplier_id') ? 'has-error' : ''}}">
                                    {!! Form::label('supplier_id', 'Supplier Name', ['class' => 'control-label']) !!}
                                    {!! Form::select('supplier_id', getSuppliersDropdown(),null, ['class' => 'form-control select2']) !!}
                                    {!! $errors->first('supplier_id', '<p class="help-block">:message</p>') !!}
                                    <div class="help-block with-errors"></div>
                                </div>


                                
                                <div class="form-group col-md-4 {{ $errors->has('discount_type') ? 'has-error' : ''}}">
                                    {!! Form::label('discount_type', 'Discount Type', ['class' => 'control-label']) !!}
                                        {!! Form::select('discount_type', ['0'=>'Select Discount Type','1'=>'Percentage','2'=>'Fixed'],null, ['class' => 'form-control select2']) !!}
                                        {!! $errors->first('discount_type', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>
                                
                                <div class="form-group col-md-4 {{ $errors->has('discount') ? 'has-error' : ''}}">
                                    {!! Form::label('discount', 'Max Discount', ['class' => 'control-label']) !!}
                                        {!! Form::number('discount', null, ['class' => 'form-control','placeholder'=>'Max Discount','min' => '0']) !!}
                                        {!! $errors->first('discount', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>                                                                
                                                                
                            </div>                                    

                            <div class="row">                                 
                                <div class="form-group col-md-4 hide_cost {{ $errors->has('cost') ? 'has-error' : ''}}">
                                    {!! Form::label('cost', 'Product Cost', ['class' => 'control-label required-input']) !!}
                                        {!! Form::number('cost', null, ['class' => 'form-control','required' => 'required','min' => '0','placeholder'=>'Product Cost','step' => 'any']) !!}
                                        {!! $errors->first('cost', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>
                                
                                <div class="form-group col-md-4 hide_price {{ $errors->has('price') ? 'has-error' : ''}}">
                                    {!! Form::label('price', 'Product Price', ['class' => 'control-label required-input']) !!}
                                        {!! Form::number('price', null, ['class' => 'form-control','placeholder'=>'Product Price','min' => '0','required' => 'required','step' => 'any']) !!}
                                        {!! $errors->first('price', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>
                                
                                <div class="form-group col-md-4 modifier_select {{ $errors->has('tags') ? 'has-error' : ''}}">
                                    {!! Form::label('tags', 'Tags', ['class' => 'control-label ']) !!}
                                        {!! Form::textarea('tags', (isset($product)?$product->product_tags->pluck('name')->implode(','):'') , ['class' => 'form-control']) !!}
                                        {!! $errors->first('tags', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                </div>
                               
                            </div>                         

                            <div class="row modifier_select" > 
                                <div class="form-group col-md-12">
                                  {!! Form::label('images', 'Product Images', ['class' => 'control-label']) !!}                  
                                      <div class="dropzone {{ isset($product)?'dz-started':'' }}" id="my-awesome-dropzone">        
                                            @include('company.products.imagelist')
                                      </div>
                                      <input id="total_images" name="product_images" value="{{ isset($product)?$product->product_images->count():'' }}" style="display:none;" type="text">
                                      <div class="help-block with-errors"style="margin-left: 10px;"></div>
                                </div>
                            </div>

                            <div class="row modifier_select">                    
                                <div class="form-group col-md-6 {{ $errors->has('detail') ? 'has-error' : ''}}">
                                    {!! Form::label('detail', 'Product Details', ['class' => 'control-label']) !!}
                                        {!! Form::textarea('detail', null, ['class' => 'form-control']) !!}
                                        {!! $errors->first('detail', '<p class="help-block">:message</p>') !!}
                                        <div class="help-block with-errors"></div>
                                  </div>

                                  {{--<div class="form-group col-md-6 {{ $errors->has('invoice_detail') ? 'has-error' : ''}}">--}}
                                    {{--{!! Form::label('invoice_detail', 'Product Details for Invoice', ['class' => 'control-label']) !!}--}}
                                        {{--{!! Form::textarea('invoice_detail', null, ['class' => 'form-control']) !!}--}}
                                        {{--{!! $errors->first('invoice_detail', '<p class="help-block">:message</p>') !!}--}}
                                        {{--<div class="help-block with-errors"></div>--}}
                                  {{--</div> --}}
                            </div>

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    {!! Form::submit(isset($product) ? 'Save' : 'Save & Next', ['class' => 'btn btn-info pull-right']) !!}
                                </div>
                            </div>  
                    </div>
                    </section>
                </div>
            </div>
        </div>  
    @endif
    
    @if(isset($submitButtonText))
        @if(!empty($tab2))
            <!-- Store and Categories-->
            <div class="tab-pane fade in {{ $tab2 }}"> 
            <div class="row">            
                <div class="col-lg-12">
                    <section class="panel">
                        <div class="panel-body">                                                      

                            <div class="row">
                                <div class="form-group col-md-6 {{ $errors->has('store_category_ids') ? 'has-error' : ''}}"> 
                                    <section class="panel">
                                        <header class="panel-heading">Stores & Categories</header>
                                        <div class="panel-body">
                                            <div id="store_category_tree"></div>
                                            <input type="text" name="store_category_ids" id="checkedIds" required style="display:none;" />
                                            {!! $errors->first('store_category_ids', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>                                            
                                        </div>
                                    </section> 
                                </div> 
                                
                                <div class="form-group col-md-6"> 
                                    <section class="panel">
                                        <header class="panel-heading">Stores Quantity</header>
                                        <div class="panel-body">
                                            <div id="store_quantity" /> </div>                                           
                                        </div>
                                    </section> 
                                </div>                                                                    
                            </div>                                                                                                       

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    {!! Form::submit('Save', ['class' => 'btn btn-info pull-right']) !!}
                                </div>
                            </div>  
                        </div>
                    </section>
                </div>
            </div>
        </div>   
        @elseif(!empty($tab3) && $product->type==2)    
            <!-- Combo Products -->
            <div class="tab-pane fade in {{ $tab3 }}"> 
            <div class="row">            
                <div class="col-lg-12">
                    <section class="panel">
                        <div class="panel-body">                                                      

                            <div class="row" id="combo_panel">
                                <div class="col-md-6">    
                                    
                                    <table class="table general-table" id="combos_table">
                                        <thead>                            
                                        </thead>
                                        <tbody> 
                                            @if($product->product_combos->count() > 0)

                                            <input type="hidden" name="total_combos" id="total_combos" value="{{ $product->product_combos->count() }}" />

                                            @foreach($product->product_combos as $combo_key => $combo_value)
                                            @php($combo_key = $combo_key+1) 
                                                <tr data-id="{{ $combo_key }}">
                                                    <td>
                                                        <div class="form-group">
                                                            {!! Form::select('product_id_'.$combo_key, getSelectedProduct($combo_value['product_id']),$combo_value['product_id'], ['class' => 'form-control combo_name select2','placeholder'=>'Product Name','required'=>'required','style'=>'width: 300px;']) !!}
                                                            <div class="help-block with-errors"></div>   
                                                        </div>
                                                    </td>
                                                    <td width='10px'>
                                                        <button type="button" class="btn btn-info btn-xs remove_combo" data-toggle="tooltip" title="Remove Combo Product" style="margin-top: 6px;"><i class="fa fa-minus" ></i></button>
                                                    </td>
                                                    <input type="hidden" name="combo_id_{{ $combo_key }}" class="combo_id"  value="{{ $combo_value['id'] }}" />
                                                </tr>                                    
                                                @endforeach
                                            @else
                                            <input type="hidden" name="total_combos" id="total_combos" value="1" />
                                            <tr data-id="1">
                                                <td>
                                                    <div class="form-group">
                                                        {!! Form::select('product_id_1', ["" => "Select Product"],null, ['class' => 'form-control combo_name select2','style'=>'width: 300px;']) !!}
                                                        <div class="help-block with-errors"></div>   
                                                    </div>
                                                </td>
                                                <td width='10px'>
                                                    <button type="button" class="btn btn-info btn-xs remove_combo" data-toggle="tooltip" title="Remove Combo Product" style="margin-top: 6px;"><i class="fa fa-minus" ></i></button>
                                                </td>
                                                <input type="hidden" name="combo_id_1" class="variant_id"  value="0" />
                                            </tr> 
                                          @endif  
                                        </tbody>

                                        <tfoot>
                                            <tr>
                                                <td colspan="2">                                    
                                                    <button type="button" id="add_combo" class="btn btn-primary btn-xs pull-right" data-toggle="tooltip" title="Add Combo Product" style="margin-top: 6px;"><i class="fa fa-plus"  ></i></button>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                          
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group variant_checked col-md-12 {{ $errors->has('cost') ? 'has-error' : ''}}">
                                        {!! Form::label('cost', 'Total Products Cost', ['class' => 'control-label required-input']) !!}
                                            {!! Form::number('cost', null, ['class' => 'form-control','min' => '0','placeholder'=>'Cost','required' => 'required']) !!}
                                            {!! $errors->first('cost', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                    </div>
                                    <div class="form-group variant_checked col-md-12 {{ $errors->has('price') ? 'has-error' : ''}}">
                                        {!! Form::label('price', 'Total Products Price', ['class' => 'control-label required-input']) !!}
                                            {!! Form::number('price', null, ['class' => 'form-control','placeholder'=>'Price','min' => '0','required' => 'required']) !!}
                                            {!! $errors->first('price', '<p class="help-block">:message</p>') !!}
                                            <div class="help-block with-errors"></div>
                                    </div>
                                </div>
                            </div>
                            
                            

                            <div class="form-group">
                                <div class="col-lg-offset-2 col-lg-10">
                                    {!! Form::submit('Save', ['class' => 'btn btn-info pull-right']) !!}
                                </div>
                            </div>  
                        </div>
                    </section>
                </div>
            </div>
        </div>                   
        @endif    
    @endif    
</div>

@section('scripts')
<!--<script type="text/javascript" src="{{ asset('plugins/ckeditor/ckeditor.js') }}"></script>-->
<script src="//cdn.ckeditor.com/4.8.0/full/ckeditor.js"></script>
@if(!empty($tab1))
    <script type="text/javascript" src="{{ asset('js/dropzone.js') }}"></script>
@endif
<script type="text/javascript" src="{{ asset('js/gijgo.min.js') }}"></script>
<script type="text/javascript">
    var token = $('meta[name="csrf-token"]').attr('content');
    var baseUrl = "{{url('company/products')}}"; 
    var category_select = $('#category_id');
    
    $(document).ready(function(){         
        
        $('#discount_type').change();
        
        @if(@$product)
            var url = "{{ url('company/get-all-store-categories') }}"+'/'+{{ $product->id }}
        @else
            var url = "{{ url('company/get-all-store-categories') }}";
        @endif                 
       
       @if(@$product && !empty($tab1))
            show_hide_fields(2);            
       @else
            show_hide_fields(1); 
       @endif
   
       @if(@$product && !empty($tab2))
        var tree = $('#store_category_tree').tree({
            primaryKey: 'data_id',
            uiLibrary: 'bootstrap',
            dataSource: url,
            icons: {
                expand: '<i class="glyphicon glyphicon-circle-arrow-right"></i>',
                collapse: '<i class="glyphicon glyphicon-circle-arrow-down"></i>'
            },
            checkboxes: true,
            border: true,            
            showIcon: true,
        });                
        
        tree.on('checkboxChange', function (e, $node, record, state) {
            $("#store_quantity").LoadingOverlay("show");
            setTimeout(function(){
                var checkedIds = tree.getCheckedNodes();
                $("#checkedIds").val(checkedIds);
                $("#store_quantity").html('');
                $.each(checkedIds,function(index, value){
                    var id_type = value.split('-');
                    if(id_type[0] == 'store'){
                        var data = tree.getDataById(value);

                        if(data.name){
                            $("#store_quantity").append('<div class="form-group">\
                        <label class="control-label required-input">'+ data.text +'</label>\
                        <input type="number" name="store_quantity_'+ data.data_id +'" class="form-control" min="0" required />\
                        <div class="help-block with-errors"></div>\
                       </div>\
                       <div class="form-group clearfix low_stock">\
                        <label class="control-label">Low Stock Notification</label>\
                        \<input class="low_stock" type="checkbox" data-type="checkbox" data-guid="236f2159-1db8-82b9-779d-90474ad7a919" data-checkbox="true">\
                        <input style="display: none" oninput="this.value = Math.abs(this.value)" type="number" name="low_stock_'+ data.data_id +'" class="form-control" min="0" value="0" />\
                        <input type="hidden" value="0" name="low_status_'+ data.data_id +'">\
                        <div class="help-block with-errors"></div>\
                       </div>\
                       </br>');


                            // $("#store_quantity").append('\<label class="control-label required-input">Low Stock Notification</label>\
                            // <input class="low_stock" type="checkbox" data-type="checkbox" data-guid="236f2159-1db8-82b9-779d-90474ad7a919" data-checkbox="true">\
                            // <div style="display: none" class="form-group">\
                            //  <input type="number" name="low_stock_'+ data.data_id +'" class="form-control" min="0" required />\
                            //  <div class="help-block with-errors"></div>\
                            // </div>');
                            $('#product_form').validator('update');
                            set_store_products(tree,50);
                        }

                    }
                });
                $("#store_quantity").LoadingOverlay("hide");
            },500);
        });

        $(document).on('click' , '.low_stock', function () {
            var element = $(this).next();
           if($(this).is(":checked")){
               element.next().val(1);
               element.show();
           } else {
               element.val(0);
               element.next().val(0);
               element.hide();
           }
        });

        
        //set_store_products(tree,2000);
    @endif    
        
//        $("#product_form").validator({
//            disable: true,
//            custom: {
//                "checkedIds": function(el) {
//                    if(el.val() != ""){
//                        var ids = el.val().split(',');
//                        var test = 0;
//                        $.each(ids,function(index, value){
//                           var id_type = value.split('-');
//                           if(id_type[0] == 'category' || id_type[0] == 'subcategory'){
//                               test = 1;
//                           }                       
//                        });
//                        
//                        setTimeout(function(){ 
//                            if(test == 0){
//                                return 'Please select store and atleast one category.';
//                            } 
//                        }, 1000);
//                    }
//                }
//            },            
//        });
        
        $('#tags').tagsInput({width:'auto'});       
        
        $("#genrate_random_number").click(function(){
            var random_number = generateRandomNumber(8);
            $("#code").val(random_number);
            $("#code").blur();
            return false;
        });

        
        $('#discount_type').change(function(){
            var text = "Max Discount";
            if(this.value == 1){
                text = "Max Discount (%)";
            }else if(this.value == 2){
                text = "Max Discount (Fixed)";
            }
            
            $("#discount").parent(".form-group").find("label").text(text);
            $("#discount").attr("placeholder",text);
        });
        
        // --------------------- combos code --------------
            $(document).on('change','#product_type',function() { 
               var val = $(this).val();
               
                if(val==1){
                    show_hide_fields(1);
                   $("#combo_product_tab").hide();
                    //$("#product_price").prop('required',true);
                    //$("#product_cost").prop('required',true);
                }else if(val==2){
                   show_hide_fields(2);
                   $("#combo_product_tab").show();
                   //$("#product_price").removeAttr('required');
                    //$("#product_cost").removeAttr('required');
                }else{
                    show_hide_fields(2);
                    $("#combo_product_tab").hide();
                    //$("#product_price").prop('required',true);
                    //$("#product_cost").prop('required',true);
                }
            }); 
        
        // add new combo
        $(document).on("click", "#add_combo", function () 
        {
            var combos_table = $("#combos_table");
            var combos_body = combos_table.find("tbody");
            var id = combos_body.find('tr').length+1;

            combos_body.append('\
                <tr data-id="'+id+'">\
                    <td><div class="form-group">{!! Form::select("product_id", ["" => "Select Product"], null, ["class" => "form-control combo_name select2","required" => "required","style"=>"width: 300px;"]) !!}<div class="help-block with-errors"></div></div></td>\
                    <td><button type="button" class="btn btn-info btn-xs remove_combo" data-toggle="tooltip" title="Remove Combo Product" style="margin-top: 6px;"><i class="fa fa-minus" ></i></button></td>\
                <input type="hidden" name="combo_id_'+id+'" class="combo_id"  value="0" /></tr>\
            ');

            $('.combo_name').select2({
                ajax: {
                  url: "{{url('company/get-combo-products')}}",
                  dataType: 'json'
                  // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
                }
              });
            refine_combo_html();                                    
        });
        
        $('.combo_name').select2({
            ajax: {
              url: "{{url('company/get-combo-products')}}",
              dataType: 'json'
              // Additional AJAX parameters go here; see the end of this chapter for the full code of this example
            }
          });
        
        // remove combo
        $(document).on("click", ".remove_combo", function ()
        {   
            var parent_el = $(this).parents("tr");
            var combo_id = parent_el.find('.combo_id').val();
            
            swal({
                title: "Are you sure?",
                text: "You will not be able to recover this variant!",
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#DD6B55",
                confirmButtonText: "Yes, delete it!",
                cancelButtonText: "No, cancel!",
                closeOnConfirm: false,
                closeOnCancel: false
              },
              function(isConfirm){
                if (isConfirm) {
                    if(combo_id>0){
                       $.ajax({
                        url: "{{ url('company/products/remove-combo') }}"+'/'+combo_id,
                        type: 'delete',
                        success: function (result) {
                            parent_el.remove(); 
                            swal.close();
                            refine_combo_html();
                        }
                        }); 
                    }else{
                        swal.close();
                        parent_el.remove(); 
                        refine_combo_html();
                    }

                } else {
                swal.close();
                toastr.info("Your record is safe :)");
              }
            });    
  
        });
        
        // --------------------- variants code --------------
        
        $(document).on('click','#is_variants',function() { 
            if ($(this).is(':checked')) {
                $("#is_modifier").prop('checked',false);
                show_hide_fields(2);                      
            }else{
                show_hide_fields(1);
            } 
        }); 
        
        $(document).on('click','#is_modifier',function() { 
            if ($(this).is(':checked')) {
                $("#is_variants").prop('checked',false);
                show_hide_fields(1);                      
            } 
        }); 
                                  
        
        @if(!empty($tab1))
            Dropzone.autoDiscover = false;         
            var myDropzone = new Dropzone("div#my-awesome-dropzone", {
                url: baseUrl+"/store-image",
                paramName: "file",
                maxFilesize: 2,
                init: function () {
                var self = this;
                // config
                self.options.addRemoveLinks = true;
                self.options.dictRemoveFile = "Remove";
                // bind events

                /*
                * Success file upload
                */
                self.on("success", function (file, response) {  
                   if (response){            
                       $('#my-awesome-dropzone').append('<input type="hidden" name="image_ids[]" class="image_ids" id="img_' + response.id + '" value="' + response.id + '"/>');
                       file.previewElement.classList.add("dz-"+response.id);

                       $('.dz-'+response.id).append('<span class="default-image"><input class="default_image" id="default_image_'+ response.id +'" data-id="'+ response.id +'" type="checkbox" data-toggle="tooltip" title="Set image as default"></span>');
                   }


                    file.serverId = response.id;

                    var total_images = $("#total_images").val();

                    if(total_images == "")
                        $("#total_images").val(1);
                    else
                        $("#total_images").val( parseInt(total_images)+1 );
               });

                /*
                * On delete file
                */
               self.on("removedfile", function (file) {
                    $.ajax({
                        url : baseUrl + '/delete-image/'+file.serverId,
                        type: 'get',
                        data: {'_token': token},
                        success: function (result) {                     
                           var total_images = $("#total_images").val();
                           if(total_images == 1)
                               $("#total_images").val("");
                           else
                               $("#total_images").val( parseInt(total_images)-1 );
                        }
                    });
                });      
               },
               params: {
                  _token: token
                }
                });
                 

            $(document).on('click','.default_image',function() {                               
            
            var image_id = $(this).data('id');
            var image_ids = [];
            
            $("#my-awesome-dropzone .dz-preview").each(function () {
                image_ids.push($(this).find('.default-image input').data('id'));
            });            
            
            if ($(this).is(':checked')) {
                $('.default_image').prop('checked', false);     
                $(this).prop('checked', true);  
                var checked = 1;
            }else{
                $('.default_image').prop('checked', false); 
                var checked = 0;  
            }
            
            $.ajax({
                 url : baseUrl + '/set-default-image',
                 type: 'post',
                 data: {'_token': token,'image_id':image_id,'image_ids':image_ids,'checked':checked},
                 success: function (result) {                     
                   
                 }
                 
             });
            
          });
          
        @endif
        
        });  
        
    function show_hide_fields(type){
        if(type==1){ // Show
            
            $(".modifier_select").show();
            if($("#is_variants").is(':checked')){
                $(".variant_checked").hide();                            
                $("#sku").removeAttr("required","required");   
            }else{
                $(".variant_checked").show();  
                $("#sku").attr("required","required");                                    
            }
            
            if ($("#product_type option:selected").val() == 1 || $("#product_type option:selected").val() == 3) {
                $("#cost").attr("required","required");  
                $("#price").attr("required","required"); 
                $("#cost").parent('.hide_cost').show();  
                $("#price").parent('.hide_price').show();
                        
                if($("#product_type option:selected").val() == 3){
                    $(".modifier_select").hide();
                }
            }else if($("#product_type option:selected").val() == 2){
                $("#cost").removeAttr("required","required");  
                $("#price").removeAttr("required","required");  
                $("#cost").parent('.hide_cost').hide();  
                $("#price").parent('.hide_price').hide();        
        }
            
        }else if(type==2){ // Hide
            $(".modifier_select").show();                        
            $(".variant_checked").hide();                             
            $("#sku").removeAttr("required","required");
            $("#cost").removeAttr("required","required");
            $("#price").removeAttr("required","required");
            
            
            if ($("#product_type option:selected").val() == 1 || $("#product_type option:selected").val() == 3) {
                $(".variant_checked").show(); 
                $("#cost").removeAttr("required","required");  
                $("#price").removeAttr("required","required"); 
                
                if($("#product_type option:selected").val() == 3){
                    $(".variant_checked").hide(); 
                    $(".modifier_select").hide();
                    $("#cost").attr("required","required");  
                    $("#price").attr("required","required"); 
                    $("#cost").parent('.hide_cost').show();  
                    $("#price").parent('.hide_price').show();
                    $("#is_variants").prop('checked',false);
                    $("#is_modifier").prop('checked',false);
                }
                
            }else if($("#product_type option:selected").val() == 2){
                if($("#is_variants").is(':checked')){
                    $(".variant_checked").hide();
                }else{
                    $(".variant_checked").show();                    
                }
                
                $("#cost").parents('.hide_cost').hide();  
                $("#price").parents('.hide_price').hide();
            }
        }        
    }    
     
    @if(@$product && !empty($tab2)) 
        function set_store_products(tree, timeout){
            //alert(timeout);
            setTimeout(function(){ 
                @foreach($product->store_products as $store)
                    @if($store->quantity>0)
                        //alert(tree.getNodeById('store-{{$store->store_id}}'));
                        tree.expand(tree.getNodeById('store-{{$store->store_id}}'));   
                        $("input[name=store_quantity_store-{{ $store->store_id }}]").val({{ $store->quantity }});
                    @endif

                    var temp_node_check = $("input[name=low_stock_store-{{ $store->store_id }}]");
                    temp_node_check.val({{ $store->low_stock }});
                    @if($store->low_stock_status == 1)
                    temp_node_check.show();
                    temp_node_check.prev().prop('checked',true);
                    temp_node_check.next().val(1);
                    @endif
                @endforeach  

                @foreach($product->category_products as $category)
                    @if($category->category->parent_id == 0)
                        tree.expand(tree.getNodeById('category-{{$category->category_id}}'));
                    @else
                        tree.expand(tree.getNodeById('subcategory-{{$category->category_id}}'));
                    @endif    
                @endforeach 

            }, timeout);
        } 
    @endif
    
    function refine_combo_html()
    {
        var combos_table = $("#combos_table");
        var combos_body = combos_table.find("tbody");
        var total_combos = combos_body.find('tr').length;            

        $("#total_combos").val(total_combos);    

        //sections
        combos_body.find("tr").each(function(index) { 
            var combo_id = index+1;
            var combo_html = $(this);        
            combo_html.attr("data-id", combo_id);
            
//            if ($("#product_type option:selected").val() == 2) {
//                combo_html.find(".combo_name").attr("required","required");     
//            }else{
//                combo_html.find(".combo_name").removeAttr("required","required");
//            }
            
            combo_html.find(".combo_name").attr("name","product_id_"+combo_id);
            combo_html.find(".combo_id").attr("name","combo_id_"+combo_id);          
        });  
        
        $('#product_form').validator('update');
    }
    
    function remove_uploaded_file(imageId){
        $.ajax({
             url: baseUrl + '/delete-image/'+imageId,
             type: 'get',
             success: function (result) {
                $('.dz-'+imageId).remove();
                
                var total_images = $("#total_images").val();
                if(total_images == 1)
                    $("#total_images").val("");
                else
                    $("#total_images").val( parseInt(total_images)-1 );
             }
         });
     }
     
    @if(!empty($tab1)) 
     $(document).ready(function(){ 
        CKEDITOR.replace( 'detail',{            
            removePlugins: 'elementspath,magicline',
            resize_enabled: false,
            allowedContent: true,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            toolbar: [
                [ 'Bold','-','Italic','-','Underline'],
            ],
        });
        
        CKEDITOR.replace( 'invoice_detail',{            
            removePlugins: 'elementspath,magicline',
            resize_enabled: false,
            allowedContent: true,
            enterMode: CKEDITOR.ENTER_BR,
            shiftEnterMode: CKEDITOR.ENTER_BR,
            toolbar: [
                [ 'Bold','-','Italic','-','Underline'],
            ],
        });
    });
    
    @endif
</script>
@endsection



