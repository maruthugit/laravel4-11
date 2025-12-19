@extends('layouts.master')

@section('title') Flash Sale Management @stop

@section('content')

<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">

    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/css/bootstrap-select.min.css">

<!-- Latest compiled and minified JavaScript -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.1/js/bootstrap-select.min.js"></script>

<div id="page-wrapper">
    @if ($errors->any())
        {{ implode('', $errors->all('<div class=\'bg-danger alert\'>:message</div>')) }}
    @endif

    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Flash Sale Management
            <span class="pull-right">
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="{{asset('/')}}flashsale/edit/<?php echo $flash->id; ?>"><i class="fa fa-refresh"></i></a>
                <a class="btn btn-default" data-toggle="tooltip" href="{{asset('/')}}flashsale"><i class="fa fa-reply"></i></a>
            </span>
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>

    {{ Form::open(array('url'=>'flashsale/update/'.$flash->id , 'class' => 'form-horizontal form-submit', 'files' => true)) }}

    <div class="panel panel-default">
        <div class="panel-heading">
            <h2 class="panel-title"><i class="fa fa-pencil"></i> Details </h2>
        </div>
        <div class="panel-body">
            <div class="col-lg-12">
                <div class='form-group'>
                {{ Form::label('rule_name', 'Rule Name', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                    {{ Form::text('rule_name', $flash->rule_name, array('class' => 'form-control') ) }}
                    </div>
                </div>  
                <div class="form-group">
                    {{ Form::label('Date', 'Valid From/Until', array('class'=> 'col-lg-2 control-label')) }}
                    <div class="col-lg-3">
                        <div class="input-group" id="datetimepicker_from">
                            {{ Form::text('valid_from', $flash->valid_from, ['id' => 'valid_from', 'class' => 'valid_from form-control', 'tabindex' => 1]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                    <div class="col-lg-3">
                        <div class="input-group" id="datetimepicker_to">
                            {{ Form::text('valid_to', $flash->valid_to, ['id' => 'valid_to', 'class' => 'valid_to form-control', 'tabindex' => 2]) }}
                            <span class="input-group-btn">
                                <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                            </span>
                        </div>
                    </div>
                </div>
                <hr/>
                <div class="form-group @if ($errors->has('lid')) has-error @endif">
                                <label class="col-lg-2 control-label" for="price_option">Products</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/flashsale/ajaxproduct?editpage=1"><i class="fa fa-plus"></i> Add Product</span>
                                        </div>
                                    </div>
                                    <br />
                                    <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-2">Product &amp; SKU</th>
                                                <th class="hidden-xs hidden-sm col-sm-3">Label</th>
                                                <th class="hidden-xs hidden-sm col-sm-1">Actual Price ({{Config::get("constants.CURRENCY")}})</th>
                                                <th class="hidden-xs hidden-sm col-sm-1">Promotion Price ({{Config::get("constants.CURRENCY")}})</th>
                                                <th class="cell-small col-sm-1">Total Sold</th>
                                                <th class="cell-small col-sm-1">Stock in Hand</th>
                                                <th class="cell-small col-sm-1">Per User Min Purchase Qty</th>
                                                <th class="cell-small col-sm-1">Per User Max Purchase Qty</th>
                                                <th class="cell-small col-sm-1 text-center">Seq</th>
                                                <th class="cell-small text-center col-sm-1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ptb">
                                            @if (($flash_products) && sizeof($flash_products) > 0)
                                            @foreach ($flash_products as $pkg)
                                            <tr class="product">
                                                <input type="hidden" name="label_id[]" value="{{$pkg->label_id}}">
                                                <input type="hidden" name="product_id[]" value="{{$pkg->product_id}}">
                                                <input type="hidden" name="label[]" value="{{$pkg->label}}">
                                                <input type="hidden" name="price[]" value="{{$pkg->actual_price}}">
                                                <td><b>{{$pkg->name}}</b><br><i class="fa fa-tag"></i> {{$pkg->sku}}</td><td>{{$pkg->label}}</td>
                                                <td class="text-center col-xs-1">{{$pkg->actual_price}}</td>
                                                <td class="text-center col-xs-1"><input type="text" name="promo_price[]" value="{{$pkg->promo_price}}" class="form-control"></td>
                                                <td class="text-center col-xs-1">{{$pkg->qty}}</td>
                                                <td class="text-center col-xs-1"><input type="text" name="qty[]" value="{{$pkg->limit_quantity}}" class="form-control"></td>
                                                <td class="text-center col-xs-1"><input type="text" name="min[]" value="{{$pkg->min_qty}}" class="form-control"></td>
                                                <td class="text-center col-xs-1"><input type="text" name="limit[]" value="{{$pkg->max_qty}}" class="form-control"></td>
                                                <td class="text-center col-xs-1"><input type="text" name="seq[]" value="{{$pkg->seq}}" class="form-control seq" required></td>
                                                <td class="text-center col-xs-1"><div class="btn-group">
                                                <a class="btn btn-xs btn-danger" id="deleteProduct" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>
                                                </div></td>
                                                <!-- <input type="hidden" value="{{$pkg->label}}" name="label" id="label[]">
                                                <input type="hidden" value="{{$pkg->product_opt}}" name="lid[]" id="lid[]">
                                                <td><b>{{ $pkg->name }}</b><br><i class="fa fa-tag"></i> {{$pkg->sku}}</td>
                                                <td class="hidden-xs hidden-sm">{{$pkg->label}}</td>
                                                <td class="hidden-xs hidden-sm col-xs-1 text-right price">{{$pkg->actual_price}}</td>
                                                <td class="hidden-xs hidden-sm col-xs-1 text-right promo_price"><input type="text" name="promo_price[]" value="{{number_format((float)$pkg->promo_price, 2, '.', '')}}"></td>
                                                <td class="hidden-xs hidden-sm col-xs-1"><input type="text" value="{{$pkg->qty}}" name="qty[]" autofocus="autofocus" class="form-control col-xs-2"></td>
                                                <td class="text-center col-xs-1">
                                                    <div class="btn-group">
                                                        <a data-original-title="Delete" href="javascript:void(0)" data-toggle="tooltip" id="deleteProduct" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> Remove</a>
                                                    </div>
                                                </td> -->
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr id="emptyproduct">
                                                <td colspan="10">No product added.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                <div class='form-group'>
                {{ Form::label('status', 'Status', array('class' => 'col-lg-2 control-label')) }}
                    <div class="col-lg-6">
                        <select class="form-control" name="status">
                            <?php
                                foreach ($status as $key => $valstat){
                                                                
                                    echo '<option value="' . htmlspecialchars( $key) .'"';
                                    if( $key == $flash->status){
                                        echo ' selected="selected"';
                                    }
                                    echo '>' . htmlspecialchars( $valstat) . '</option>';
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <hr/>
            </div>

            <div class='form-group'>
                <div class="col-lg-10">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                </div>
            </div>
        </div>
    </div>
    {{ Form::close() }}

    

</div>
<script>
    $(function() {
        $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
            format: 'YYYY-MM-DD HH:mm:ss'
        });
    });

      $('body').on('click', '.remove-seller', function() {
       
        if($("#multipleSellerTable").children().length <= 1){
            alert('Must has minimum 1 Product');
        }else{
             var seller_id = $(this).attr("seller-id");
             $(".seller-cost-"+seller_id).remove();
            console.log($(this).parent().parent().remove());
        }
        
    });
   
   $("#add-seller-btn").click(function(){
       
        console.log($("#seller_name2").val());
       
        var seller_id = $("#seller_name2").val();
        var str = '<tr><td>'+seller_id+'<input name="product_sku[]" value="'+seller_id+'" type="hidden"></td> <td><input type="text" name="product_price[]"></td><td><input type="text" name="product_quantity[]"></td> <td><a seller-id="'+seller_id+'" class="btn btn-danger btn-xs remove-seller"><i class="fa fa-trash-o"></i> Remove</a></td></tr>';
                   $("#multipleSellerTable").append(str);

   });


</script>

@stop

@section('script')
    localStorage.clear();
    $('#addProdBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed:function(){
            calSubTotal();
            calculateTotal();
        }
    });

    function currencyFormat(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }

    function calSubTotal() {
        var subTotal    = 0;
        var subPromo    = 0;
        var qty         = 0;
        var prodprice   = 0;
        var promoprice  = 0;

        $('tr.product').each(function(){
            var cur     = '#'+this.id;
            prodprice   = $(cur + ' td.p_price').text();
            promoprice  = $(cur + ' td.promo_price').text();
            qty         = $(cur + ' td.qty').text();
            var price   = prodprice;

            if(promoprice > 0) {
                //alert('Promo Price: '+promoprice);
                price = promoprice;
            }

            subTotal  = qty * parseFloat(price.replace(/,/g,''));

            //alert('Product price: '+prodprice+'\nQty: '+qty+'\nSub-Total: '+subTotal);
            $(cur + ' .subtotal').html(currencyFormat(subTotal));

        });
    }

    function calculateTotal() {
        var grandTotal = 0;
        var grandQty = 0;

        $('tr.product td.subtotal').each(function(){
            subtotal = $(this).text();
            grandTotal += parseFloat(subtotal.replace(/,/g,''));
        });

        $('tr.product td.qty').each(function(){
            qty = $(this).text();
            grandQty += parseFloat(qty.replace(/,/g,''));;
        });
        
        $('.grand_total').html(currencyFormat(grandTotal));
        $('.grand_qty').html(grandQty);

    }

    $(document).on("click", "#deleteProduct", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        if(!$('.disc_product').length) {
            $('#emptyproduct').show();
        }
    });
    
    $('form.form-submit').on('submit', function(e) {
    
        var position = [];

        $('.seq').each(function() {
            position.push(this.value);
        });

        if (duplicateExists(position)) {
            e.preventDefault();
            alert('There is a duplicate seq. Please check');
            return;
        }
    });

    function duplicateExists(w){
        return new Set(w).size !== w.length 
    }
@stop