@extends('layouts.master')

@section('title') Package @stop

@section('content')
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Product Package</h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Add Package</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => 'product/package/store', 'class' => 'form-horizontal', 'files'=> true)) }}

                            <div class="form-group @if ($errors->has('prod_name')) has-error @endif">
                            {{ Form::label('prod_name', 'Package Name *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('prod_name', Input::old('prod_name'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('prod_name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('product_name_cn')) has-error @endif">
                            {{ Form::label('product_name_cn', 'Package Name (Chinese)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('product_name_cn', Input::old('product_name_cn'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_name_cn', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('product_name_my')) has-error @endif">
                            {{ Form::label('product_name_my', 'Package Name (Bahasa)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('product_name_my', Input::old('product_name_my'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_name_my', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product_desc')) has-error @endif">
                            {{ Form::label('product_desc', 'Description *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('product_desc', Input::old('product_desc'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_desc', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('product_desc_cn')) has-error @endif">
                            {{ Form::label('product_desc_cn', 'Description (Chinese)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('product_name_my', Input::old('product_desc_cn'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_name_my', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('product_desc_my')) has-error @endif">
                            {{ Form::label('product_desc_my', 'Description (Bahasa)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('product_desc_my', Input::old('product_desc_my'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_desc_my', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product_category')) has-error @endif">
                                {{ Form::label('product_category', 'Category', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::select('product_category', ['' => 'Select Category'] + $categoriesMainOptions, null, ['id' => 'product_category', 'class' => 'form-control']) }}
                                    {{ $errors->first('product_category', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('lid')) has-error @endif">
                                <?php $count = 1; ?>
                                <label class="col-lg-2 control-label" for="price_option">Package Products</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="../ajaxproduct"><i class="fa fa-plus"></i> Add Product</span>
                                        </div>
                                    </div>
                                    <br />
                                <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                    <table class="table table-bordered">
                                        <thead>
                                            <tr>
                                                <th class="col-sm-2">Product Name &amp; SKU</th>
                                                <th class="hidden-xs hidden-sm col-sm-3">Label</th>
                                                <th class="hidden-xs hidden-sm col-sm-1">Actual Price ({{Config::get("constants.CURRENCY")}})</th>
                                                <th class="hidden-xs hidden-sm col-sm-1">Promotion Price ({{Config::get("constants.CURRENCY")}})</th>
                                                <th class="cell-small col-sm-1">Quantity in Package</th>
                                                <th class="cell-small text-center col-sm-1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ptb">
                                            <tr id="emptyproduct">
                                                <td colspan="6">No product added.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('delivery_time')) has-error @endif">
                                {{ Form::label('delivery_time', 'Delivery Time', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::select('delivery_time', array('' => 'Select Delivery Time', '24 hours' => '24 hours', '1-2 business days' => '1-2 business days', '2-3 business days' => '2-3 business days', '3-7 business days' => '3-7 business days', '14 business days' => '14 business days'), null, ['class' => 'form-control']) }}
                                    {{ $errors->first('delivery_time', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('related_product')) has-error @endif">
                            {{ Form::label('related_product', 'Related Products (QR Code)', array('class' => 'col-lg-2 control-label')) }}
                                <div class="col-lg-6">
                                {{ Form::text('related_product', null, array('placeholder' => 'e.g. JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
                                {{ $errors->first('related_product', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />
                            <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Package Status', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::select('status', array('0' => 'Inactive', '1' => 'Active'), null, array('class'=> 'form-control')) }}
                                    {{ $errors->first('status', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <!-- <a class="btn btn-default" href="/product"><i class="fa fa-reply"></i> Cancel</a> -->
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    <button id="buttonSave" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                </div>
                            </div>
                        {{ Form::close() }}
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
@stop

@section('inputjs')
<!-- File Input JavaScript -->
<script src="../../../js/fileinput.min.js"></script>
@stop

@section('script')
    $(document).ready(function(){
        localStorage.clear()
    });

    $('#addProdBtn').colorbox({
        iframe:true, width:"80%", height:"80%",
        onClosed:function(){
            calculateTotal();
        }
    });

    function currencyFormat(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }

    function calculateTotal() {
        var grandTotal = 0;
        var grandPromo = 0;
        $('tr.product td.p_price').each(function(){
            prodprice = $(this).text();
            grandTotal += parseFloat(prodprice.replace(/,/g,''));
        });

        $('tr.product td.promo_price').each(function(){
            promoprice = $(this).text();
            grandPromo += parseFloat(promoprice.replace(/,/g,''));
        });

        $('.p_price_total').html(currencyFormat(grandTotal));
        $('.p_promo_total').html(currencyFormat(grandPromo));
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
