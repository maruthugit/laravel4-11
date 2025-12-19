@extends('layouts.master')
@section('title', 'Purchase Order')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add New Purchase Order</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/purchase-order/store', 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Purchase Order Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                <i class="fa"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                            </div>
                        @endif
                        <div class="form-group required {{ $errors->first('po_date', 'has-error') }}">
                            {{ Form::label('po_date', 'PO Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_from">
                                    <input id="po_date" class="form-control" tabindex="1" name="po_date" type="text" value="{{ Input::old('po_date') }}">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                                {{ $errors->first('po_date', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('type', 'has-error') }}">
                            {{ Form::label('type', 'Type ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                 {{ Form::select('type', $po_type, false, ['id' => 'type', 'class' => 'form-control']) }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('payment_terms', 'has-error') }}">
                            {{ Form::label('payment_terms', 'Payment ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='payment_terms' id='payment_terms'>
                                    @foreach ($payment_terms as $payment_term)
                                    <option value='{{ $payment_term }}' <?php if ($payment_term == Input::old('payment_terms')) echo 'selected';
                                        else if ($payment_term == $payment_terms[0]) echo 'selected';?>>{{ $payment_term }} Days
                                    </option>
                                    @endforeach
                                </select>
                                {{ $errors->first('payment_terms', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('delivery_date', 'has-error') }}">
                            {{ Form::label('delivery_date', 'Delivery Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="deliverydate_from">
                                    <input id="delivery_date" class="form-control" tabindex="1" name="delivery_date" type="text" value="{{ Input::old('delivery_date') }}">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                                {{ $errors->first('delivery_date', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('from', 'has-error') }}">
                            {{ Form::label('from', 'From ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='from' id='from'>
                                    <option value='Tien Ming Distribution Sdn Bhd.'>Tien Ming Distribution Sdn Bhd.</option>
                                    <!--<option value='Jocom MShopping Sdn. Bhd.'>Jocom MShopping Sdn. Bhd.</option>-->
                                </select>
                                {{ $errors->first('from', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('seller', 'has-error') }}">
                            {{ Form::label('seller', 'Seller ', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="seller_id" name="seller_id" value="{{Input::old('seller_id')}}">
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="seller" name="seller" class="form-control" value="{{Input::old('seller')}}" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectSellerBtn" id="selectSellerBtn"  type="button" href="/purchase-order/seller-list"><i class="fa fa-plus"></i> Seller</button>
                                </span>
                                </div><!-- /input-group -->
                                {{ $errors->first('seller', '<p class="help-block">:message</p>') }}
                            </div><!-- /.col-lg-6 -->
                        </div>
                        <hr>
                        <div class="form-group required {{ $errors->first('warehouse', 'has-error') }}">
                            {{ Form::label('warehouse', 'Warehouse ', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{Input::old('warehouse_id')}}">
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="warehouse" name="warehouse" class="form-control" value="{{Input::old('warehouse')}}" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectWarehouseBtn" id="selectWarehouseBtn"  type="button" href="/purchase-order/warehouse-list"><i class="fa fa-plus"></i> Warehouse </button>
                                </span>
                                </div><!-- /input-group -->
                                {{ $errors->first('warehouse', '<p class="help-block">:message</p>') }}
                            </div><!-- /.col-lg-6 -->
                        </div>
                        <div class='form-group'>
                            {{ Form::label('specialmsg', 'Remark', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::textarea('specialmsg', Input::old('specialmsg'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            {{ $errors->first('specialmsg', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>   
                        <hr>
                        <div class="form-group required @if ($errors->has('lid')) has-error @endif">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/purchase-order/pbx/products"><i class="fa fa-plus"></i> Add Product</span>
                                    </div>
                                </div>
                                <br />
                            <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-2">Product Name &amp; SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-3">Label</th>
                                            <th class="hidden-xs hidden-sm col-sm-1">Price</th>
                                            <th class="cell-small col-sm-1">Quantity</th>
                                            <th class="cell-small col-sm-1">Sub-total</th>
                                            <th class="cell-small col-sm-1">SST (RM)</th>
                                            <th class="cell-small col-sm-1">Sub-total Inclusive SST</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                    </tbody>
                                    <input type="hidden" id="total_amount">
                                </table>{{ $errors->first('qrcode', '<p class="help-block" style="color: #a94442">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required">
                            {{ Form::label('discchk', 'Discount ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::checkbox('discchk', null, false, ['id' => 'discchk']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('discpercent', 'Discount %', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::number('discpercent', null, ['class' => 'form-control', 'readonly']) }}
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('disctotal', 'Discounted Total', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::text('disctotal', null, ['class' => 'form-control', 'readonly']) }}
                            </div>
                        </div>
                        <hr>
                        <div class="form-group required {{ $errors->first('manager', 'has-error') }}">
                            {{ Form::label('manager', 'Manager ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='manager' id='manager'>
                                    @foreach ($managers as $manager)
                                        <option value='{{ $manager }}' 
                                        <?php if ($manager == Input::old('manager')) echo 'selected';
                                            else if ($manager == $managers[0]) echo 'selected'; ?>>{{ $manager }}
                                        </option>
                                    @endforeach
                                </select>
                                {{ $errors->first('manager', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 29, 5, 'AND'))
    <div class='form-group' >
        <div class="col-lg-10" style="padding-bottom:10px;">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
            <!--Under Upgrading .. Wait for a moment.-->
        </div>
    </div>
    @endif
    {{ Form::close() }}
</div>
    
@stop

@section('script')
<?php if(isset($orderMappedInfo['order_number'])){ ?>

    var rowTotal = $('<tr id="grandTotal"><td class="text-right" colspan="5"><b>Total:</b></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_qty"></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_total"></td><td></td></tr>');
    parent.$('#ptb').append(rowTotal);
    calSubTotal();
    calculateTotal();
    
<?php  } ?>

    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('#deliverydate_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $(function () {
        $('[data-toggle="popover"]').popover({ trigger: "hover" });
    })


    $('#selectSellerBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#selectWarehouseBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#addProdBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed:function(){
            calSubTotal();
            calculateTotal();
        }
    });

    $('#discchk').change(function() {
        if (this.checked) {
            $('#discpercent').attr('readonly', false);
        } else {
            $('#discpercent').val('');
            $('#discpercent').attr('readonly', true);
            $('#disctotal').val('');
        }
    });

    $('#discpercent').change(function() {
        var total = $('#total_amount').val();
        $('#disctotal').val(currencyFormat(total - (total * this.value) / 100));
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
            
            prodprice   = $(cur + ' #price_local').val();
            promoprice  = $(cur + ' #price_promo_local').val();
            
            qty = $(cur + ' td.qty').text();

            var promoPrice = parseFloat(promoprice.replace(/,/g,''));
            subTotal = currencyFormat(qty * promoPrice);
            
            var subTotalHtml = '<div id="amt-value-main" amt-value-1="'+subTotal+'" >'+subTotal+ '</div>';
            $(cur + ' .subtotal').html(subTotalHtml);

            var sst = $(cur + ' td input.sst').val();
            var subtotalSst = currencyFormat(parseFloat(subTotal.replace(/[^\d\.\-]/g, "")) + parseFloat(sst));

            var subTotalSstHtml = '<div id="amt-value-main-sst" amt-value-2="'+subtotalSst+'" >'+subtotalSst+ '</div>';
            $(cur + ' .subtotal-sst').html(subTotalSstHtml);
        });
    }
    
    function calculateTotal() {
        var grandTotal1 = 0;
        var grandQty = 0;

        $('tr.product td#subtotal ').each(function(){
            subtotal1 = $(this).find( "#amt-value-main" ).attr("amt-value-1");
            grandTotal1 += parseFloat(subtotal1.replace(/,/g,''));;
        });

        $('.grand_total').html(currencyFormat(grandTotal1));
        $('#total_amount').val(grandTotal1);

        calculateSstTotal();
        calculateDiscount(grandTotal1);
    }

    function calculateSstTotal() {
        var grandTotal = 0.00;
        $('tr.product td#subtotal-sst').each(function(){
            subtotal = $(this).find( "#amt-value-main-sst" ).attr("amt-value-2");
            
            grandTotal += parseFloat(subtotal.replace(/,/g,''));;
            
        });

        $('.grand_total_sst').html(currencyFormat(grandTotal));console.log(grandTotal);
        $('#total_amount').val(grandTotal);
    }

    $(document).on('change', '.sst', function() {
        var sst = $(this).val();
        

        var subtotal = $(this).parent().parent().find('td.subtotal div#amt-value-main').attr('amt-value-1');
        var subtotalSst = (parseFloat(sst) + parseFloat(subtotal.replace(/,/g,''))).toFixed(2);
        
        $(this).parent().parent().find('td.subtotal-sst div#amt-value-main-sst').html(subtotalSst);
        $(this).parent().parent().find('td.subtotal-sst div#amt-value-main-sst').attr('amt-value-2', subtotalSst);
        calculateSstTotal();

    });

    function calculateDiscount(total) {
        let checkbox = document.getElementById('discchk');
        if(checkbox.checked) {
            $('#disctotal').val(currencyFormat(total - (total * $('#discpercent').val()) / 100));
        }
    }

    $(document).on("click", "#deleteItem", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        calculateTotal();
        if(!$('.product').length) {
            $('#emptyproduct').show();
            $('#grandTotal').remove();
        }
    });

@stop