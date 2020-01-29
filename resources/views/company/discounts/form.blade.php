<div class="row">
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Discount</header>
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
                 
                {{--<div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}}">--}}
                    {{--{!! Form::label('category', 'Categories', ['class' => 'col-md-3 control-label required-input']) !!}--}}
                    {{--<div class="col-md-9">                                                 --}}
                        {{--{!! Form::select('category[]', $categories, null, ['class' => 'form-control categories','multiple' => 'multiple','required' => 'required']) !!}--}}
                        {{--{!! $errors->first('category', '<p class="help-block">:message</p>') !!}--}}
                        {{--<div class="help-block with-errors"></div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}}">
                        {!! Form::label('category', 'Stores', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            <select name="store_id" class="form-control required select_store">
                                @foreach($stores as $store)
                                    <option @if(Hashids::encode($store->id) == request()->route()->parameter('store_id')) selected @endif value={{Hashids::encode($store->id)}}>{{$store->name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('category', '<p class="help-block">:message</p>') !!}

                        </div>
                    </div>
                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}}">
                        {!! Form::label('category', 'Categories', ['class' => 'col-md-3 control-label required-input']) !!}
                        <div class="col-md-9">
                            <select name="category[]" multiple="multiple" class="form-control required categories" id="cat">
                                @foreach($categories as $category)
                                    <option value={{$category->id}}>{{$category->category_name}}</option>
                                @endforeach
                            </select>
                            {!! $errors->first('category', '<p class="help-block">:message</p>') !!}
                        </div>
                    </div>


                    <div class="form-group {{ $errors->has('store_id') ? 'has-error' : ''}}">
                        {!! Form::label('product_check', 'Products', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9">
                            <div class="checkDate_parent">
                                <label class="date_checkBox"><input name="check_products" class="check_products" type="checkbox" checked disabled="disabled"></label>
                                {!! Form::label('product_check', 'All products', ['class' => 'control-label']) !!}
                                {!! $errors->first('date_time', '<p class="help-block">:message</p>') !!}
                                <div class="help-block with-errors"></div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group {{ $errors->has('parent_id') ? 'has-error' : ''}} products_div">
                        {!! Form::label('category', 'Select Products', ['class' => 'col-md-3 control-label']) !!}
                        <div class="col-md-9" id="products_select" >
                            {!! $errors->first('category', '<p class="help-block">:message</p>') !!}
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
                    $cat_array = $categories->toArray();
                @endphp
                <input id="store" type="hidden" value="">
            </div>
        </section>

    </div>
    
</div>

@section('scripts')
    <script type="text/javascript" src="{{asset('plugins/multi-transfer/js/jquery.multi-select.js')}}"></script>
    <script>
        var check_category = [];
        $(function() {
            var selected_array =  [];
            var selected_cat_ids = [];
            $(document).on("click",".ms-drop ul li label input", function(){
                var view = $(this);

                for(var i = 0; i < cat_array.length; i++){
                    if(parseInt(view.val()) === parseInt(cat_array[i].parent_id)) {
                        //if (check(view.value)) {
                        var id = cat_array[i].id;
                        $('.ms-drop ul li input').filter(sprintf('[value="%s"]', id)).prop('checked', view.prop('checked'));
                        $('.categories option').filter(sprintf('[value="%s"]', id)).prop('selected', view.prop('checked'));

                        // console.log(selected);
                        //}
                    }
                }

                setTimeout(function(){
                    var selected = '';
                    var arr = [];
                    var ids = [];

                    $('.categories option').each(function (index, value) {
                        var el = $(value);
                        if (el.attr('selected') == 'selected') {
                            //console.log(el.text());
                            selected_array.push(el.text() + ', ');
                            selected += el.text() + ', ';

                            //console.log(el.val());
                            ids.push(el.val());
                            check_category.push(el.val());
                        }

                        // if (el.val() == id) {
                        //     if(view.checked === false){
                        //         el.removeAttr('selected');
                        //         selected.replace(el.text()+',','');
                        //         var length = $('.categories option:selected').length;
                        //         if(length <= 0){
                        //             selected = '';
                        //         }
                        //     } else {
                        //     selected += el.text() + ', ';
                        //     el.attr('selected', 'selected');
                        //     }
                        //
                        // }
                        // $(".categories button span").text(selected);
                    });
                    //console.log(ids);
                    var id_string = ids.join(',');

                    arr = selected.split(",");
                    arr.pop();
                    if(arr.length > 3){
                        //var count = cat_array.length - arr.length;
                        $(".categories button span").text(arr.length+' of '+cat_array.length+' selected');
                    } else {
                        $(".categories button span").text(selected);
                    }
                    if(ids.length > 0){
                        $('.check_products').prop('disabled', false);
                    } else {
                        $('.check_products').prop('disabled', true);
                    }
                    $(".position-center").LoadingOverlay("show");

                    $('.products_select').html('');
                    $.ajax({
                        type: "get",
                        url: "{{ url('company/get-category-products-ajax') }}",
                        data: {id_string:id_string},
                        success:function (result) {
                            var html = '<select name="products[]" multiple="multiple" class="form-control required searchable" id="products">';
                            $.each(result, function(key, value) {
                                html += '<option value="'+value.id+'">'+value.name+'</option>'

                            });
                            html += '</select>';

                            $("#products_select").html(html);
                            $('.searchable').multiSelect({
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
                                });
                        }
                    });
                    $(".position-center").LoadingOverlay("hide");

                }, 50);


                var arr1 = [];
                $('input.categories:checkbox:checked').each(function () {
                    arr1.push($(this).val());
                });
//console.log(arr1);
            });

            $('input[name="date_time"]').daterangepicker({
                timePicker: true,
                startDate: moment().startOf('hour'),
                endDate: moment().startOf('hour').add(32, 'hour'),
                locale: {
                    format: 'YYYY-MM-DD hh:mm A'
                }
            });
        });



        var cat_array = <?php echo json_encode($cat_array); ?>;
        $(document).ready(function() {

            $('.datetime').prop('disabled', true);
            $('.categories').multipleSelect();

            $('.products_div').hide();

            $('.discount_type').select2();
            $('.select_store').select2();

        });

        function check(check_id) {
            var found = false;
            for(var i = 0; i < cat_array.length; i++){
                if(cat_array[i].id == check_id){
                    if(cat_array[i].parent_id == 0){
                        found = true;
                    }
                }
            }
            return found;
        }
        function sprintf(str) {
            var args = arguments,
                flag = true,
                i = 1;

            str = str.replace(/%s/g, function () {
                var arg = args[i++];

                if (typeof arg === 'undefined') {
                    flag = false;
                    return '';
                }
                return arg;
            });
            return flag ? str : '';
        };

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


        $("#submit_form").submit(function(e) {
            var selected = $('.categories option:selected').length;
            e.preventDefault();
            if(selected <= 0){
                $('#cat_error').html('Please select category');
            }
            else if($('#discount').val() === ''){
                $('#discount_error').html('Please fill out this field.');
            } else if($('#name').val() === '') {
                $('#name_error').html('Please fill out this field.');
            }
            else {
                $(this).unbind('submit').submit();
            }
        });

        $('.select_store').change(function () {
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



