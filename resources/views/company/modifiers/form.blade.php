
<div class="row">            
    <div class="col-lg-12">
        <section class="panel">
            <header class="panel-heading">{{ isset($submitButtonText) ? $submitButtonText : 'Create' }} Modifier</header>
            <div class="panel-body">
                <div class="position-center">                                                                                                                                                             
                    
                    <div class="form-group {{ $errors->has('name') ? 'has-error' : ''}}">
                        {!! Form::label('name', 'Name', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::text('name', null, ['class' => 'form-control','placeholder' => 'Name','required' => 'required']) !!}
                            {!! $errors->first('name', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>                                                                                                                                                                                                                                           
                    
                    <div class="form-group {{ $errors->has('max_options') ? 'has-error' : ''}}">
                        {!! Form::label('max_options', 'Max Options', ['class' => 'col-lg-3 col-sm-3 control-label required-input']) !!}
                        <div class="col-lg-9">
                            {!! Form::number('max_options', null, ['class' => 'form-control','placeholder' => 'Max Options','required' => 'required','min' => '0']) !!}
                            {!! $errors->first('max_options', '<p class="help-block">:message</p>') !!}
                            <div class="help-block with-errors"></div>
                        </div>
                    </div>                                                                                                                                                                                                                                           
                
            <div class="row">                    
                <div class="col-sm-12">
                <section class="panel">
                    <div class="panel-body">
                        <table class="table general-table" style="margin-left: 40px;" id="options_table">
                            <thead>
                            <tr>
                                <th>Ordering</th>
                                <th>Option Name</th>
                                <th>Cost Price</th>
                                <th>Sale Price</th>
                                <th>Option SKU</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            
                            @if(isset($modifier))
                            
                            <input type="hidden" name="total_options" id="total_options" value="{{ $modifier->modifier_options->count() }}" />
                            
                                @foreach($modifier->modifier_options as $option_key => $option_value)                
                                @php($option_key = $option_key+1) 
                                    <tr data-id="{{ $option_key }}">
                                        <td>
                                            <div class="form-group">
                                                <input class="form-control option_ordering" name="ordering_{{ $option_key }}" type="number" min="0" value="{{ $option_value['ordering'] }}" style="width: 70px;" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input class="form-control option_name" name="name_{{ $option_key }}" placeholder="Name" type="text" value="{{ $option_value['name'] }}" style="width: 120px;" required>                                        
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input class="form-control option_cost" name="cost_{{ $option_key }}" type="number" min="0" value="{{ $option_value['cost'] }}" style="width: 70px;" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input class="form-control option_price" name="price_{{ $option_key }}" type="number"  min="0" value="{{ $option_value['price'] }}" style="width: 70px;" required>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-group">
                                                <input class="form-control option_sku" name="sku_{{ $option_key }}" type="text"  min="0" value="{{ $option_value['sku'] }}" style="width: 100px;" required>
                                            </div>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-info btn-xs remove_opiton" data-toggle="tooltip" title="Remove Option" style="margin-top: 6px;"><i class="fa fa-minus" ></i></button>
                                        </td>
                                    <input type="hidden" name="option_id_{{ $option_key }}" class="option_id"  value="{{ $option_value['id'] }}" />
                                    </tr>
                                @endforeach
                            
                            @else
                            
                                <input type="hidden" name="total_options" id="total_options" value="1" />
                                <tr data-id="1">
                                    <td>
                                        <div class="form-group">
                                            <input class="form-control option_ordering" name="ordering_1" type="number" min="0" style="width: 70px;" required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input class="form-control option_name" name="name_1" placeholder="Name" type="text" style="width: 120px;" required>                                        
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input class="form-control option_cost" name="cost_1" type="number" min="0" style="width: 70px;" required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input class="form-control option_price" name="price_1" type="number"  min="0" style="width: 70px;" required>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="form-group">
                                            <input class="form-control option_sku" name="sku_1" type="text"  min="0" style="width: 100px;" required>
                                        </div>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-info btn-xs remove_opiton" data-toggle="tooltip" title="Remove Option" style="margin-top: 6px;"><i class="fa fa-minus" ></i></button>
                                    </td>
                                <input type="hidden" name="option_id_1" class="option_id"  value="0" />
                                </tr>
                            @endif    
                            </tbody>
                            
                            <tfoot>
                            <tr>
                                <td colspan="6">
                                    <button type="button" id="add_option" class="btn btn-primary btn-xs pull-right" data-toggle="tooltip" title="Add Option"><i class="fa fa-plus"  ></i></button>
                                </td>
                            </tr>

                            </tfoot>
                        </table>
                    </div>
                </section>
            </div>
            </div>    
                    
                <div class="form-group">
                    <div class="col-lg-offset-2 col-lg-10">
                        {!! Form::submit(isset($submitButtonText) ? $submitButtonText : 'Create', ['class' => 'btn btn-info pull-right']) !!}
                    </div>
                </div>
                </div>
            </div>
        </section>

    </div>
</div>


@section('scripts')
<script type="text/javascript">
$(document).ready(function(){
    $("#direction").select2();
        
        
    // add new option
    $(document).on("click", "#add_option", function () 
    {
        var opitons_table = $("#options_table");
        var opitons_body = opitons_table.find("tbody");
        var id = opitons_body.find('tr').length+1;

        opitons_body.append('\
            <tr data-id="'+id+'">\
                <td><div class="form-group"><input class="form-control option_ordering" name="ordering_'+id+'" type="number" min="0" style="width: 70px;" required></div></td>\
                <td><div class="form-group"><input class="form-control option_name" name="name_'+id+'" placeholder="Name" type="text" style="width: 120px;" required></div></td>\
                <td><div class="form-group"><input class="form-control option_cost" name="cost_'+id+'" type="number" min="0" style="width: 70px;" required></div></td>\
                <td><div class="form-group"><input class="form-control option_price" name="price_'+id+'" type="number" min="0" style="width: 70px;" required></div></td>\
                <td><div class="form-group"><input class="form-control option_sku" name="sku_'+id+'" type="text" style="width: 100px;" required></div></td>\
                <td><button type="button" class="btn btn-info btn-xs remove_opiton" data-toggle="tooltip" title="Remove Option" style="margin-top: 6px;"><i class="fa fa-minus" ></i></button></td>\
            <input type="hidden" name="option_id_'+id+'" class="option_id"  value="0" /></tr>\
        ');
                                                    
        refine_html();                                    
    });
    
    // remove option
    $(document).on("click", ".remove_opiton", function ()
    {   
        var parent_el = $(this).parents("tr");
        var option_id = parent_el.find('.option_id').val();
        
        if(option_id>0){
           $.ajax({
            url: "{{ url('company/modifiers/remove-option') }}"+'/'+option_id,
            type: 'get',
            success: function (result) {
                parent_el.remove();     
                refine_html();
            }
            }); 
        }else{
            parent_el.remove();     
            refine_html();
        }
        
        
        return false;   
    });
        
});

function refine_html()
{
    var opitons_table = $("#options_table");
    var opitons_body = opitons_table.find("tbody");
    var total_options = opitons_body.find("tr").length;
    
    $("#total_options").val(total_options);    
    
    //sections
    opitons_body.find("tr").each(function(index) { 
        var option_id = index+1;
        var option_html = $(this);        
        option_html.attr("data-id", option_id);
                       
        option_html.find(".option_ordering").attr("name","ordering_"+option_id);             
        option_html.find(".option_name").attr("name","name_"+option_id);
        option_html.find(".option_cost").attr("name","cost_"+option_id);
        option_html.find(".option_price").attr("name","price_"+option_id);
        option_html.find(".option_sku").attr("name","sku_"+option_id);          
        option_html.find(".option_id").attr("name","option_id_"+option_id);          
        
        
    });  
    
    $('#modifier_form').validator('update');
} 
</script>
@endsection