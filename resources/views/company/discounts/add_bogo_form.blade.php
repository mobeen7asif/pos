@if($errors->any())

    @endif
<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Bogo Discount</header>
            <div class="panel-body">
                <div class="position-center">

                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                        {!! Form::label('Discount Name', 'Discount Name', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::text('name', null, ['class' => 'form-control','placeholder'=>'Name','required' => 'required','id' => 'name']) !!}
                            {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
                            <div id="name_error" style="color: #a94442" class="help-block with-errors"></div>
                            {{--<div class="help-block with-errors"></div>--}}
                        </div>
                    </div>


                 <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                    {!! Form::label('store_id', 'Time Period', ['class' => 'col-md-3 control-label']) !!}
                    <div class="col-md-9">
                        <div class="checkDate_parent">
                        <label class="date_checkBox"><input class="check_date" type="checkbox"></label>
                        {!! Form::text('date_time', null, ['class' => 'form-control datetime','placeholder'=>'Date']) !!}
                        {!! $errors->first('date_time', '<p class="help-block">:message</p>') !!}
                        <div class="help-block with-errors"></div>
                        </div>
                    </div>
                </div>

                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}}">
                        {!! Form::label('store_id', 'Stores', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            <select name="store_id" class="form-control required select_store">
                                @foreach($stores as $store)
                                    <option value={{$store->id}}>{{$store->name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('category', '<p class="help-block">:message</p>') !!}

                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('bogo_type') ? 'has-error' : ''}}">
                        {!! Form::label('bogo_type', "Apply To", ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9 radio_class">
                            <div>
                                <input @if(old('bogo_type') == 'product') checked @endif class="bogo_type" type="radio" required name="bogo_type" value="product"> Products
                            </div>
                            <div>
                                <input @if(old('bogo_type') == 'category') checked @endif class="bogo_type" type="radio" required name="bogo_type" value="category"> Categories
                            </div>
                            {!! $errors->first('bogo_type', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('required_product') ? 'has-error' : ''}} required_product_div">
                        {!! Form::label('required_product', 'Product With', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9" id="select_required_product" >
                            {!! $errors->first('required_product', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('optional_products') ? 'has-error' : ''}} optional_products_div">
                        {!! Form::label('optional_products', 'Product To', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9" id="select_optional_products" >
                            {!! $errors->first('optional_products', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('required_category') ? 'has-error' : ''}} required_category_div">
                        {!! Form::label('required_category', 'Category With', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9" id="select_required_category" >
                            {!! $errors->first('required_category', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('optional_categories') ? 'has-error' : ''}} optional_categories_div">
                        {!! Form::label('optional_categories', 'Categorie To', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9" id="select_optional_categories" >
                            {!! $errors->first('optional_categories', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>





                    <div class="form-group {{ $errors->has('discount_type') ? 'has-error' : ''}}">
                        {!! Form::label('discount_type', 'Discount Type', ['class' => 'col-md-3 control-label required-input' ,'required' => 'required']) !!}
                        <div class="col-md-9">
                            {!! Form::select('discount_type', $discount_type, null, ['class' => 'form-control discount_type']) !!}
                            {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
                            <div id="cat_error" style="color: #a94442" class="help-block with-errors"></div>
                            {{--<div class="help-block with-errors"></div>--}}
                        </div>
                    </div>

                    <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                        {!! Form::label('Discount Amount', 'Discount Amount', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            {!! Form::number('discount_amount', null, ['oninput'=> 'this.value = Math.abs(this.value)','class' => 'form-control','placeholder'=>'Amount','required' => 'required','step' => 'any','id' => 'discount']) !!}
                            {!! $errors->first('parent_id', '<p class="help-block">:message</p>') !!}
                            <div id="discount_error" style="color: #a94442" class="help-block with-errors"></div>
                            {{--<div class="help-block with-errors"></div>--}}
                        </div>
                    </div>





                  <div class="form-group">
                        <div class="col-lg-offset-2 col-lg-10">
                            {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
                        </div>
                    </div>  
                    
                </div>
                @php
                    //$cat_array = $categories->toArray();
                @endphp
                <input id="store" type="hidden" value="">
            </div>
        </section>

    </div>
    
</div>

@section('scripts')
    <script type="text/javascript" src="{{asset('plugins/multi-transfer/js/jquery.multi-select.js')}}"></script>
    <script>
        var store_id = 0;
        $(document).ready(function () {
            $('.select_store').select2();
          store_id = $( ".select_store option:selected" ).val();
            $('.required_product_div').hide();
            $('.optional_products_div').hide();
            $('.required_category_div').hide();
            $('.optional_categories_div').hide();


            $('input[name="date_time"]').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'YYYY-MM-DD hh:mm A'
                }
            });
        });
        $('.select_store').change(function () {
           store_id = $(this).val();
            $(".position-center").LoadingOverlay("show");
            $('input[type="radio"]').prop('checked', false);
            $('.required_product_div').hide();
            $('.optional_products_div').hide();
            $('.required_category_div').hide();
            $('.optional_categories_div').hide();
            setTimeout(function () {
                $(".position-center").LoadingOverlay("hide");
            },500);
        });

        $('.bogo_type').change(function () {
            $(".position-center").LoadingOverlay("show");
           var bogo_type = $(this).val();

           if(bogo_type === 'product'){
               //get products
               $.ajax({
                   type: "get",
                   url: "{{ url('company/get-products-ajax') }}",
                   data: {store_id:store_id},
                   success:function (result) {
                       var html = '<select name="required_product" class="form-control required required_product qqsqqqssss">';
                       $.each(result, function(key, value) {
                           html += '<option value="'+value.id+'">'+value.name+'</option>'

                       });
                       html += '</select>';
                       $("#select_required_product").html(html);


                       //show optional products;
                       var html = '<select name="optional_products[]" multiple class="form-control required optional_products ">';
                       $.each(result, function(key, value) {
                           html += '<option value="'+value.id+'">'+value.name+'</option>'

                       });
                       html += '</select>';
                       $("#select_optional_products").html(html);


                       $('.optional_products').multiSelect({
                           selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search'>",
                           selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search'>",
                           afterInit: function(ms){
                               var that = this,
                                   $selectableSearch = that.$selectableUl.prev(),
                                   $selectionSearch = that.$selectionUl.prev(),
                                   selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                                   selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                               that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                                   .on('keydown', function(e){
                                       if (e.which === 40){
                                           that.$selectableUl.focus();
                                           return false;
                                       }
                                   });

                               that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                                   .on('keydown', function(e){
                                       if (e.which == 40){
                                           that.$selectionUl.focus();
                                           return false;
                                       }
                                   });
                           },
                           afterSelect: function(){
                               this.qs1.cache();
                               this.qs2.cache();
                           },
                           afterDeselect: function(){
                               this.qs1.cache();
                               this.qs2.cache();
                           }
                       })
                       $('.required_product').select2();
                       $('.required_product_div').show();
                       $('.optional_products_div').show();

                       $('.required_category_div').hide();
                       $('.optional_categories_div').hide();
                       $(".position-center").LoadingOverlay("hide");
                   }
               });
           }
           else {
               $.ajax({
                   type: "get",
                   url: "{{ url('company/get-categories-ajax') }}",
                   data: {store_id:store_id},
                   success:function (result) {
                       var html = '<select name="required_category" class="form-control required required_category ">';
                       $.each(result, function(key, value) {
                           html += '<option value="'+value.id+'">'+value.category_name+'</option>'

                       });
                       html += '</select>';
                       $("#select_required_category").html(html);


                       //show optional categories;
                       var html = '<select name="optional_categories[]" multiple class="form-control required optional_categories ">';
                       $.each(result, function(key, value) {
                           html += '<option value="'+value.id+'">'+value.category_name+'</option>'

                       });
                       html += '</select>';
                       $("#select_optional_categories").html(html);


                       $('.optional_categories').multiSelect({
                           selectableHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search'>",
                           selectionHeader: "<input type='text' class='search-input' autocomplete='off' placeholder='Search'>",
                           afterInit: function(ms){
                               var that = this,
                                   $selectableSearch = that.$selectableUl.prev(),
                                   $selectionSearch = that.$selectionUl.prev(),
                                   selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                                   selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                               that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                                   .on('keydown', function(e){
                                       if (e.which === 40){
                                           that.$selectableUl.focus();
                                           return false;
                                       }
                                   });

                               that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                                   .on('keydown', function(e){
                                       if (e.which == 40){
                                           that.$selectionUl.focus();
                                           return false;
                                       }
                                   });
                           },
                           afterSelect: function(){
                               this.qs1.cache();
                               this.qs2.cache();
                           },
                           afterDeselect: function(){
                               this.qs1.cache();
                               this.qs2.cache();
                           }
                       })
                       $('.required_category').select2();
                       $('.required_category_div').show();
                       $('.optional_categories_div').show();

                       $('.required_product_div').hide();
                       $('.optional_products_div').hide();

                       $(".position-center").LoadingOverlay("hide");
                   }
               });
           }
        });

        var check_category = [];

        $(document).ready(function() {

            $('.datetime').prop('disabled', true);
            // $('.categories').multipleSelect();

            $('.products_div').hide();

            $('.discount_type').select2();


        });

        $('.check_date').change(function () {
            if ($(this).is(':checked')) {
                $('.datetime').prop('disabled', false);
            } else {
                $('.datetime').prop('disabled', true);
            }
        });


        $('.check_products').change(function () {
            if ($(this).is(':checked')) {
                $('.products_div').hide();
            } else {
                $('.products_div').show();
            }
        });



        $('.select_store1').change(function () {
            var store_id = $(this).val();
            var url= "{{url('company/get_store_categories')}}";
            //var data_object = {id:store_id};
            var redirect_url = "{{ url('company/discounts/create') }}";
            redirect_url = redirect_url+'/'+store_id;
            window.location.replace(redirect_url);
            // $.ajax({
            //     url:url,
            //     type:"get",
            //     data:data_object,
            //     success:function (result) {
            //         if (result) {
            //             var selected_array =  [];
            //             if (result) {
            //                 $('.categories option').remove();
            //
            //
            //                 for (var i = 0; i < result.length; ++i) {
            //                     $('.categories').append(new Option(result[i].category_name, result[i].id));
            //                 }
            //
            //                 setTimeout(function () {
            //                     $('.categories').multipleSelect('refresh')
            //
            //
            //                     $('.ms-parent').each(function(){
            //                         if ( $(this).css('display') == 'none')
            //                             $(this).remove();
            //                     })
            //                 ,100});
            //
            //                 // setTimeout(function () {
            //                 //     $('.categories').multipleSelect({
            //                 //         onClick: function (view) {
            //                 //             for (var i = 0; i < cat_array.length; i++) {
            //                 //                 if (parseInt(view.value) === parseInt(cat_array[i].parent_id)) {
            //                 //                     //if (check(view.value)) {
            //                 //                     var id = cat_array[i].id;
            //                 //
            //                 //                     $('.ms-drop ul li input').filter(sprintf('[value="%s"]', id)).prop('checked', view.checked);
            //                 //                     $('.categories option').filter(sprintf('[value="%s"]', id)).prop('selected', view.checked);
            //                 //
            //                 //                     // console.log(selected);
            //                 //                     //}
            //                 //                 }
            //                 //             }
            //                 //
            //                 //             setTimeout(function () {
            //                 //                 var selected = '';
            //                 //                 var arr = [];
            //                 //                 $('.categories option').each(function (index, value) {
            //                 //
            //                 //                     var el = $(value);
            //                 //                     if (el.attr('selected') == 'selected') {
            //                 //                         //console.log(el.text());
            //                 //                         selected_array.push(el.text() + ', ');
            //                 //                         selected += el.text() + ', ';
            //                 //                     }
            //                 //
            //                 //                     // if (el.val() == id) {
            //                 //                     //     if(view.checked === false){
            //                 //                     //         el.removeAttr('selected');
            //                 //                     //         selected.replace(el.text()+',','');
            //                 //                     //         var length = $('.categories option:selected').length;
            //                 //                     //         if(length <= 0){
            //                 //                     //             selected = '';
            //                 //                     //         }
            //                 //                     //     } else {
            //                 //                     //     selected += el.text() + ', ';
            //                 //                     //     el.attr('selected', 'selected');
            //                 //                     //     }
            //                 //                     //
            //                 //                     // }
            //                 //                     // $(".categories button span").text(selected);
            //                 //                 });
            //                 //                 arr = selected.split(",");
            //                 //                 arr.pop();
            //                 //                 if (arr.length > 3) {
            //                 //                     //var count = cat_array.length - arr.length;
            //                 //                     $(".categories button span").text(arr.length + ' of ' + cat_array.length + ' selected');
            //                 //                 } else {
            //                 //                     $(".categories button span").text(selected);
            //                 //                 }
            //                 //             }, 50);
            //                 //         },
            //                 //     })
            //                 // , 100 });
            //
            //                 //$('.categories').multipleSelect('refresh');
            //                 if(result.length <= 0){
            //                     $('.ms-select-all label').html('No categories found');
            //
            //                 }
            //             }
            //         }
            //         // var divs = $('.ms-parent');
            //         // divs.each(function () {
            //         //     if ( $(this).css('display') == 'none')
            //         //     {
            //         //         $(this).remove();
            //         //     }
            //         // });
            //         console.log(cat);
            //     }//.... end of success.
            // });//..... end of ajax() .....//
        });
    </script>




@endsection



