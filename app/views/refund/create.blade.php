@extends('layouts.master')
@section('title', 'Refund')
@section('content')
<?php

$currency = Config::get('constants.CURRENCY');
?>

<style><style>
    .specialadjust.form-group{marginx; margin-bottom: 15px;}
    .specialadjust.form-group .max-width-adj{max-width: 500px;}
    .inlineblock-middle{display: inline-block; vertical-align: top;}
    #clone-base{display: none;}
</style>

<div id="page-wrapper">
	<div class="row">
		<div class="col-lg-12">
			<h1 class="page-header">Refund Management</h1>
		</div>
	</div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/refund/store', 'class' => 'form-horizontal', 'files' => true)) }}
            <div class="panel @if ($errors->has('trans_id')) panel-danger @else panel-default @endif ">
                {{ $errors->first('trans_id', '<p class="text-danger">&nbsp; :message</p>') }}
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Transaction Details *</h3>
                </div>
                <div class="panel-body ">
                    <div class="col-lg-12">
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="selectTransBtn" class="btn btn-primary selectTransBtn" href="/refund/ajaxtrans"><i class="fa fa-plus"></i> Select Transaction</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('trans_id', 'Transaction ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('trans_id', Input::old('trans_id'), array('class'=> 'trans-id form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('trans_date', 'Transaction Date', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('trans_date', Input::old('trans_date'), array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        {{-- <div class="form-group @if ($errors->has('trans_amount')) has-error @endif">
                            {{ Form::label('trans_amount', "Transaction Amount ($currency)", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('trans_amount', Input::old('trans_amount'), array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div> --}}

                        <input type="hidden" name="buyer_id" id="buyer_id">
						<div class="form-group @if ($errors->has('username')) has-error @endif">
                            {{ Form::label('buyer', 'Buyer', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('buyer', Input::old('buyer'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                            {{ Form::label('email', "Email address", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('email', $refund->email, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has('address')) has-error @endif">
                            {{ Form::label('address', "Address", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('address', $refund->address, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                {{-- {{ Form::text('address', Input::old('buyer'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }} --}}
                            </div>
                            {{ Form::label('postcode', "Postcode", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('postcode', $refund->address, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div>

                        {{-- <div class="form-group @if ($errors->has('postcode')) has-error @endif">
                            {{ Form::label('postcode', "Postcode", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('postcode', $refund->address, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div> --}}

                        <div class="form-group @if ($errors->has('ic_no')) has-error @endif">
                            {{ Form::label('ic_no', "I/C Number", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('ic_no', $refund->bank_name, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                            {{ Form::label('phone', "Phone Number", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('phone', $refund->hp_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div>

                        {{-- <div class="form-group @if ($errors->has('phone')) has-error @endif">
                            {{ Form::label('phone', "Phone Number", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('phone', $refund->hp_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div> --}}
                        
                        <div class="form-group @if ($errors->has('bank_name')) has-error @endif">
                            {{ Form::label('bank_name', "Bank name", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('bank_name', $refund->bank_name, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                            {{ Form::label('bank_acc_no', "Bank account no", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('bank_acc_no', $refund->bank_account_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div>

						{{-- <div class="form-group @if ($errors->has('bank_acc_no')) has-error @endif">
                            {{ Form::label('bank_acc_no', "Bank account no", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('bank_acc_no', $refund->bank_account_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                        </div> --}}

                        <div class="form-group @if ($errors->has('email')) has-error @endif">
                            {{ Form::label('order_no', "Order Number", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('order_no', $refund->order_no, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            </div>
                            {{ Form::label('platform_store', "Platform Store", array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{-- {{ Form::text('platform_store', $refund->platform_store, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }} --}}
                                {{ Form::select('platform_store', ['Jocom' => 'Jocom', 'Lazada Express' => 'Lazada Express',  'L-Redbull' => 'L-Redbull',
                                        'L-Starbucks' => 'L-Starbucks', 'L-Jocom' => 'L-Jocom','L-Everbest' => 'L-Everbest', 
                                        'L-EB' => 'L-EB', 'L-Pokka' => 'L-Pokka', 'L-Yeos' => 'L-Yeos', 'L-Etika' => 'L-Etika', 
                                        'S-Starbucks' => 'S-Starbucks', 'S-Jocom' => 'S-Jocom', 'S-Everbest' => 'S-Everbest',
                                        'S-EB' => 'S-EB', 'S-Pokka' => 'S-Pokka', 'S-Yeos' => 'S-Yeos', 'S-Etika' => 'S-Etika', 'S-Redbull' => 'S-Redbull', 'TikTok' => 'TikTok',
                                        'PG Mall' => 'PG Mall', 'SRC' => 'SRC'], 
                                        $refund->platform_store, ['class' => 'form-control', 'tabindex' => 4]) }}                               
                            </div>
                        </div>

                        <div class="specialadjust form-group">
                            {{-- {{ Form::label('file', 'Upload file', array('class' => 'col-lg-2 control-label')) }} --}}
                            <label class="col-lg-2 control-label" for="remark_doc[]">Support Document</label>
                            <div class="col-lg-6">
                                <div class="max-width-adj inlineblock-middle">
                                    <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                                        <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                                        <span class="input-group-addon btn btn-default btn-file">
                                            <span class="fileinput-new">Select file</span>
                                            <span class="fileinput-exists">Change</span>
                                            <input type="hidden"><input type="file" name="remark_doc[]">
                                        </span>
                                        <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                                    </div>
                                </div>
                                <div class="input-group-btn inlineblock-middle">
                                    <button class="btn btn-success" type="button"><i class="glyphicon glyphicon-plus"></i>Add</button>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <!-- /.panel-body -->
                
            </div>
            <!-- /.panel -->

            <div class="panel @if ($errors->has('grand_total')) panel-danger @else panel-default @endif">
                {{ $errors->first('grand_total', '<p class="text-danger">&nbsp; :message</p>') }}
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Refund Item *</h2>
                </div>
                <div class="panel-body">
                    <div class="form-group">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="product_option">Products</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip"><i class="fa fa-plus"></i> Add Product</span>
                                        <div id="product_btn"></div>
                                    </div>
                                </div>
                                <br />
                                <div class="clearfix"></div>
                                <table class="table table-bordered" id="product_list">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-2 text-center">Product Name &amp; SKU</th>
                                            <th class="col-sm-2 text-center">Item Name & Label</th>
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Price ({{$currency}})</th>
                                            {{-- <th class="hidden-xs hidden-sm col-sm-1 text-center">GST (%)</th>
                                            <th class="cell-small col-sm-1 text-center">Quantity</th>
                                            <th class="cell-small col-sm-1 text-center">Discount ({{$currency}})</th>
                                            <th class="cell-small col-sm-1 text-center">Sub-total ({{$currency}})</th> --}}
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Refund Quantity</th>
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Refund Price ({{$currency}})</th>
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Total Refund ({{$currency}})</th>
                                            <th class="cell-small col-sm-1 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                        <tr id="emptyproduct">
                                            <td colspan="11">No product added.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <label class="col-lg-2 control-label" for="other_option">Others</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addOtherBtn" name="addOtherBtn" class="btn btn-primary addOtherBtn" data-toggle="tooltip"><i class="fa fa-plus"></i> Add Other</span>
                                    </div>
                                </div>
                                <br />
                                <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-5 text-center">Item Name</th>
                                            <th class="cell-small col-sm-1 text-center">Quantity</th>
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">Amount ({{Config::get("constants.CURRENCY")}})</th>
                                            <th class="hidden-xs hidden-sm col-sm-1 text-center">GST (%)</th>
                                            <th class="cell-small col-sm-1 text-center">Sub-total ({{Config::get("constants.CURRENCY")}})</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="otb">
                                        <tr id="emptyother">
                                            <td colspan="6">No other item added.</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <label class="col-lg-2 control-label" for=""></label>
                            <div class="col-sm-10">
                                <table class="table table-bordered">
                                    <tbody id="ttb">
                                        {{-- <tr id="emptytotal">
                                            <input type="hidden" name="grand_total">
                                            <td class="text-right col-sm-8"><b>TOTAL ({{Config::get("constants.CURRENCY")}}):</b></td>
                                            <td id="grandTotal" name="grandTotal" class="text-right col-sm-1 grand_total"></td>
                                        </tr> --}}
                                        <tr>
                                            <input type="hidden" name="grand_total_refund">
                                            <td class="text-right col-sm-5"><b>TOTAL REFUND ({{Config::get("constants.CURRENCY")}}):</b></td>
                                            <td id="totalRefund" name="totalRefund" class="text-right col-sm-1 grand_total_refund"></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>

                            <div class="clearfix"></div>
                            <div class="specialadjust">
                                <label class="col-lg-2 control-label" for="remark">Remark</label>
                                <div class="col-sm-10">
                                    {{-- <input type="text" name="remark" class="form-control max-width-adj"> --}}
                                    {{ Form::textarea('remark', $refund->remarks, ['class' => 'form-control', 'rows' => '5']) }}
                                </div>
                            </div>
                    </div>
                </div>            
            </div>
            @if ( Permission::CheckAccessLevel(Session::get('role_id'), 5, 5, 'AND'))
            <div class='form-group'>
                <div class="col-lg-10">
                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                    {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
                </div>
            </div>
	        @endif
        </div>
    </div>
	{{ Form::close() }}
</div>

<div id="clone-base">
    <div class="specialadjust form-group">
        <label class="col-lg-2 control-label" for="remark_doc[]"></label>
        <div class="col-lg-10">
            <div class="max-width-adj inlineblock-middle">
                <div class="fileinput fileinput-new input-group" data-provides="fileinput">
                    <div class="form-control" data-trigger="fileinput"><i class="glyphicon glyphicon-file fileinput-exists"></i> <span class="fileinput-filename"></span></div>
                    <span class="input-group-addon btn btn-default btn-file">
                        <span class="fileinput-new">Select file</span>
                        <span class="fileinput-exists">Change</span>
                        <input type="hidden"><input type="file" name="remark_doc[]">
                    </span>
                    <a href="#" class="input-group-addon btn btn-default fileinput-exists" data-dismiss="fileinput">Remove</a>
                </div>
            </div>
            <div class="input-group-btn inlineblock-middle">
                <button class="btn btn-danger" type="button"><i class="glyphicon glyphicon-remove"></i> Remove</button>
            </div>
        </div>
    </div>
</div>

@stop


@section('script')


    {{-- Add supporting document --}}
    $(document).on('click', ".btn-success", function(){ 
        var target = $(this).parents('.specialadjust.form-group');
        var html = $('#clone-base').html();
        target.after(html);
    });

    $(document).on('click', ".btn-danger", function(){ 
        var target = $(this).parents('.specialadjust.form-group');
        target.remove();
    });

    $('#addProdBtn').prop('disabled', true);
    //$('#addOtherBtn').prop('disabled', true);
    localStorage.clear();

    if(localStorage.data0) {
        enableBtn();
    }

    $('#selectTransBtn').click(function() {
        localStorage.clear();
        $('.product').remove();
        $('#emptyproduct').show();
        //$('#grandTotal').remove();
        
        //$('#ptb tr').remove();
    });

    $('#selectTransBtn').colorbox({
        iframe:true, width:"70%", height:"90%",
        onClosed:function(){
            //$('#ptb').append('<tr id="emptyproduct"><td colspan="6">No product added.</td></tr>');
            enableBtn();
        }
    });

    $('#addProdBtn').click(function(e) {
        e.preventDefault();
        var trans_id = $(".trans-id").val();

        $.colorbox({
            iframe:true, 
            width:"90%", 
            height:"90%",
            onClosed:function(){
                calculateTotal();
            },
            href:"/refund/ajaxproduct/" + trans_id,
        });
    });

    function enableBtn() {
        $('#addProdBtn').prop('disabled', false);
        $('#addOtherBtn').prop('disabled', false);
    }

    $('#addOtherBtn').click(function(e) {
        e.preventDefault();
        $('#emptyother').hide();

        bootbox.dialog({
            title: "Add Refund Item",
            message: '<div class="row">  ' +
                        '<div class="col-md-12"> ' + 
                        '<form class="form-horizontal"> ' +
                            '<div class="form-group">' +
                                '<label class="col-md-3 control-label" for="name">Item name *</label>' +
                                '<div class="col-md-8">' +
                                    '<input id="name" type="text" name="name" class="form-control" placeholder="Item name">' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label class="col-md-3 control-label" for="amount">Unit Amount ({{Config::get("constants.CURRENCY")}})*<br>(exclude GST)</label>' +
                                '<div class="col-md-6">' +
                                    '<input id="amount" type="text" name="amount" class="form-control" placeholder="Amount">' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label class="col-md-3 control-label" for="gst_rate">GST Rate(%)</label>' +
                                '<div class="col-md-6">' +
                                    '<input id="gst_rate" type="text" name="gst_rate" class="form-control" placeholder="GST">' +
                                '</div>' +
                            '</div>' +
                            '<div class="form-group">' +
                                '<label class="col-md-3 control-label" for="qty">Quantity</label>' +
                                '<div class="col-md-6">' +
                                    '<input id="qty" type="text" name="qty" class="form-control" value="1">' +
                                '</div>' +
                            '</div>' +
                        '</form></div></div>',
            size: "large",
            buttons: {
                success: {
                    label: "Add",
                    className: "btn-success add-other",
                    callback: function() {
                        var name        = $('#name').val();
                        var amount      = parseFloat($('#amount').val()).toFixed(2);
                        var gst_rate    = parseFloat($('#gst_rate').val());
                        var qty         = $('#qty').val();
                        var total;
                        //var total       = parseFloat($('#total').val()).toFixed(2);

                        if (gst_rate > 0) {
                            var gst_value = amount * gst_rate / 100;
                            total       = parseFloat(amount) + parseFloat(gst_value);
                            subtotal    = parseFloat(total) * qty;
                            //alert('[gst_rate: '+ gst_rate +'] [gst_value: '+ gst_value +'] [qty: '+ qty +'] [total: ' + total +'] [subtotal: ' + subtotal +']');
                        } else {
                        	subtotal    = amount;
                        }

                        var rowTd   = '<input type="hidden" name="other[][name]" value="'+ name +'">\
                                        <input type="hidden" name="other[][price]" value="'+ amount +'">\
                                        <input type="hidden" name="other[][gst_rate]" value="'+ gst_rate +'">\
                                        <input type="hidden" name="other[][unit]" value="'+ qty +'">\
                                        <input type="hidden" name="other[][total]" value="'+ parseFloat(subtotal).toFixed(2) +'">\
                                        <td>' + name + '</td><td class="text-center col-xs-1">' + qty + '</td><td class="text-center col-xs-1">'+ currencyFormat(parseFloat(amount)) + '</td>\
                                        <td class="text-center col-xs-1">'+ gst_rate  + '</td><td class="text-right col-xs-1 subtotal">'+ currencyFormat(parseFloat(subtotal)) +'</td>\
                                        <td class="text-center col-xs-1"><div class="btn-group">\
                                        {{-- <a class="btn btn-xs btn-danger" id="deleteOther" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\ --}}
                                        <a class="btn btn-primary btn-danger" id="deleteOther" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-trash-o"></i></a>\
                                        </div></td>';

                        parent.$('#otb').append('<tr id="other" class="other">'+rowTd+'</tr>');
                        calculateTotal();
                    }
                }
            }
        });

    });

   

    function currencyFormat(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,");
    }

    function calculateTotal() {

        var grandTotal              = 0; //total origila product's price
        var productTotal            = 0;
        var otherTotal              = 0; 
        var refundTotal             = 0; //total refund
        var productTotalRefund      = 0;
        var transTotal;

        transTotal = $('input[name="trans_amount"]').val();

        if(transTotal == "") {
            transTotal      = parseFloat(0).toFixed(2);
        } else {
        	{{-- transTotal = transTotal.replace(/,/g,''); --}}
        }

        $('tr.product td.subtotal').each(function(){
            p_subtotal = $(this).text();
            productTotal += parseFloat(p_subtotal.replace(/,/g,''));
        });
        
        $('tr.product td.total_refund').each(function(){
            p_subtotal_refund = $(this).text();
            productTotalRefund += parseFloat(p_subtotal_refund.replace(/,/g,''));
        });
        
        $('tr.other td.subtotal').each(function(){
            o_subtotal = $(this).text();
            otherTotal += parseFloat(o_subtotal.replace(/,/g,''));
        });

        grandTotal = productTotal + otherTotal;
        grandTotal = currencyFormat(grandTotal);
        totalRefund = productTotalRefund + otherTotal;
        totalRefund = currencyFormat(totalRefund);

        $('.grand_total').html(grandTotal);
        $('input[name="grand_total"]').val(grandTotal);
        $('.grand_total_refund').html(totalRefund);
        $('input[name="grand_total_refund"]').val(totalRefund);

        {{-- if (totalRefund > parseFloat(transTotal)) {
            bootbox.alert({
                title: "Warning!",
                message: "The <b>Refund Amount ( " + totalRefund + ")</b> is more than the <b>Transaction Amount ( " + transTotal +")</b>."
            });
        } --}}
    }

    $('#add_remark').click(function (event) {
        $('#remark_div').append('<div class="form-group" id="remark"><div class="col-lg-8 col-lg-offset-2"><textarea name="remark" class="form-control" rows="5"></textarea></div><div class="col-lg-offset-2"><button type="button" class="btn btn-danger delete-remark"><i class="fa fa-minus"></i> Delete Remark</button></div></div>');
    });

    $(document).on('click', '.delete-remark', function() {
        $('#remark').remove();
    });

    $(document).on("click", "#deleteItem", function(e) {
        e.preventDefault();

        var id          = $(this).closest("tr").index() - 1;
        var rowCount    = parent.$('#ptb tr').length - 1;

        $(this).closest("tr").remove();
        calculateTotal();
        if(!$('.product').length) {
            $('#emptyproduct').show();
            //$('#grandTotal').remove();
        }

        var trans = 0
        for (var j=0; j < rowCount; j++) {
            for (var i=0; i < localStorage.length; i++) {
                var key = "transId" + j;

                if(localStorage.key(i) == key) {
                    //alert('[DELETE] [i: '+ i +'] [Length: '+ localStorage.length +'] ['+ localStorage.key(i) +'] [Key: '+key+'] [value: '+ localStorage.getItem(key) +']  Total Trans: ' + trans);
                    localStorage.removeItem(key);
                    trans++;
                }
            }
        }

        var count = 0;
        $('input[type=hidden][name="trans[][trans_detail_id]"]').each(function() {
            localStorage.setItem("transId"+count, $(this).val());
            count++;
        });

    });

    $(document).on("click", "#deleteOther", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        calculateTotal();
        if(!$('.other').length) {
            $('#emptyother').show();
            //$('#grandTotal').remove();
        }
    });

    $(document).on("click", "#deleteType", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        if(!$('.other').length) {
            $('#emptytype').show();
        }
    });

    // Delete product option
    $(document).on('click', '#delete_product_option', function(e) {
        e.preventDefault();
        localStorage.clear(e); //clear data inside local
        if ($('.product').length != 0) {
            $(this).closest('tr').remove();

            $('#ptb tr').each(function (index) {
                $(this).children().first().html(index + 1);
            });

            if (!$('input[name="price[][default]"]:checked').val()) {
                $('input[name="price[][default]"]:nth(0)').prop('checked', true);
            }
        } else {
            bootbox.alert({
                title: 'Error',
                message: 'Please insert at least one (1) price option.',
            });

            $('input[name="price[][default]"]:nth(0)').prop('checked', true);
        }

        calculateTotal();
    });


@stop

<!-- 21/03/2022 -  change code (add product's details direct to refund_details table) -->