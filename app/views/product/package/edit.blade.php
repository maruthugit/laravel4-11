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
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Package</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url' => array('product/package/update', $package->id), 'method'=> 'PUT', 'class' => 'form-horizontal', 'files' => true)) }}
                            <div class="form-group">
                            {{ Form::label('id_number', 'ID Number', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$package->id}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group">
                            {{ Form::label('sku_number', 'SKU Number', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <p class="form-control-static">{{$package->sku}}</p>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('prod_name')) has-error @endif">
                            {{ Form::label('prod_name', 'Package Name *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('prod_name', $package->name, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('prod_name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('prod_name_cn')) has-error @endif">
                            {{ Form::label('prod_name_cn', 'Package Name (Chinese)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('prod_name_cn', $package->name_cn, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('prod_name_cn', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('prod_name_my')) has-error @endif">
                            {{ Form::label('prod_name_my', 'Package Name (Bahasa)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('prod_name_my', $package->name_my, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('prod_name_my', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product_desc')) has-error @endif">
                            {{ Form::label('product_desc', 'Description *', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('product_desc', $package->description, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_desc', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('product_desc_cn')) has-error @endif">
                            {{ Form::label('product_desc_cn', 'Description (Chinese)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('product_desc_cn', $package->description_cn, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_desc_cn', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group @if ($errors->has('product_desc_my')) has-error @endif">
                            {{ Form::label('product_desc_my', 'Description (Bahasa)', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-10">
                                    {{ Form::textarea('product_desc_my', $package->description_my, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                    {{ $errors->first('product_desc_my', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product_category')) has-error @endif">
                                {{ Form::label('product_category', 'Category', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::select('product_category', ['' => 'Select Category'] + $categoriesMainOptions, $mainCategory, ['id' => 'product_category', 'class' => 'form-control']) }}
                                    {{ $errors->first('product_category', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('lid')) has-error @endif">
                                <label class="col-lg-2 control-label" for="price_option">Package Products</label>
                                <div class="col-sm-10">
                                    <div class="input-group">
                                        <div class="input-group-btn">
                                            <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="../../ajaxproduct"><i class="fa fa-plus"></i> Add Product</span>
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
                                                <th class="cell-small col-sm-1">Quantity in Package</th>
                                                <th class="cell-small text-center col-sm-1">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody id="ptb">
                                            @if (($product_package) && sizeof($product_package) > 0)
                                            @foreach ($product_package as $pkg)
                                            <tr class="product">
                                                <input type="hidden" value="{{$pkg->product_opt}}" name="label" id="label[]">
                                                <input type="hidden" value="{{$pkg->product_opt}}" name="lid[]" id="lid[]">
                                                <td><b>{{ $pkg->name }}</b><br><i class="fa fa-tag"></i> {{$pkg->sku}}</td>
                                                <td class="hidden-xs hidden-sm">{{$pkg->label}}</td>
                                                <td class="hidden-xs hidden-sm col-xs-1 text-right p_price">{{number_format((float)$pkg->price, 2, '.', '')}}</td>
                                                <td class="hidden-xs hidden-sm col-xs-1 text-right promo_price">{{number_format((float)$pkg->price_promo, 2, '.', '')}}</td>
                                                <td class="hidden-xs hidden-sm col-xs-1"><input type="text" value="{{$pkg->qty}}" name="qty[]" autofocus="autofocus" class="form-control col-xs-2"></td>
                                                <td class="text-center col-xs-1">
                                                    <div class="btn-group">
                                                        <a data-original-title="Delete" href="javascript:void(0)" data-toggle="tooltip" id="deleteItem" class="btn btn-xs btn-danger"><i class="fa fa-times"></i> Remove</a>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                            @else
                                            <tr id="emptyproduct">
                                                <td colspan="6">No product added.</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('product_video')) has-error @endif">
                                {{ Form::label('qr_code', 'QR Code', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    @if ($package->qrcode_file)
                                    {{ HTML::image('images/qrcode/'.$package->qrcode_file)}}
                                    @else
                                    {{ HTML::image('images/qrcode/noqrcode.png')}}
                                    @endif
                                    <p class="clearfix"></p>
                                    <h4><span class="label label-danger">{{ $package->qrcode }}</span></h4>
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('delivery_time')) has-error @endif">
                                {{ Form::label('delivery_time', 'Delivery Time', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::select('delivery_time', array('' => 'Select Delivery Time', '24 hours' => '24 hours', '1-2 business days' => '1-2 business days', '2-3 business days' => '2-3 business days', '3-7 business days' => '3-7 business days', '14 business days' => '14 business days'), $package->delivery_time, ['class' => 'form-control']) }}
                                    {{ $errors->first('delivery_time', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            
                            <hr/>
                            <div class="form-group @if ($errors->has('related_product')) has-error @endif">
                            {{ Form::label('related_product', 'Related Products (QR Code)', array('class' => 'col-lg-2 control-label')) }}
                                <div class="col-lg-6">
                                {{ Form::text('related_product', $package->related_product, array('placeholder' => 'e.g. JC2110 or JC1000, JC2000, JC3000', 'class' => 'form-control') ) }}
                                {{ $errors->first('related_product', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />

                            <div class="form-group @if ($errors->has('status')) has-error @endif">
                            {{ Form::label('status', 'Product Status', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::select('status', array('0' => 'Inactive', '1' => 'Active'), $package->status, array('class'=> 'form-control')) }}
                                    {{ $errors->first('status', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <hr />
                            <div class="form-group">
                                <div class="col-lg-10 col-lg-offset-2">
                                    <!-- <a class="btn btn-default" href="/product/package"><i class="fa fa-reply"></i> Cancel</a> -->
                                    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND'))
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    <button id="buttonSave" type="submit" value="Save" class="btn btn-primary"><i class="fa fa-save"></i> Save</button>
                                    @endif
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
        var rowCount = $('#ptb tr').length - 1;
        var label = document.getElementsByName("label");

        //alert('rowCount: '+rowCount + ' [label] ' + label.length);
        for (var i=0; i < label.length; i++) {
            localStorage.setItem("trid"+ i, label[i].value);
            //alert('rowCount: '+i + ' [label] ' + label[i].value);

        }
    });

    var rowTotal = $('<tr id="grandTotal"><td colspan="2"></td><td class="hidden-xs hidden-sm col-xs-1 text-right p_price_total"></td><td class="hidden-xs hidden-sm col-xs-1 text-right p_promo_total"></td><td colspan="2"></td></tr>');
    $('#ptb').append(rowTotal);
    calculateTotal();

    $('#emptyproduct').hide();

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
        var emptyRow = $('<tr id="emptyproduct"><td colspan="6">No product added.</td></tr>');
        e.preventDefault();
        $(this).closest("tr").remove();
        calculateTotal();
        if($('.product').length == '0') {
            $('#ptb').append(emptyRow);
            $('#grandTotal').remove();
        }
    });

    $("#image1").fileinput({
        initialPreview: [
            <?php $noimg = 'noimage.png'; ?>
            '<img src="/images/data/thumbs/<?php echo ($package->img_1 != '' ? "$package->img_1" : "$noimg") ?>" class="file-preview-image">'
        ],
        allowedFileExtensions: ["jpg", "png"],
        maxFileSize: 500,
        showUpload: false
    });

    $("#image2").fileinput({
        <?php if($package->img_2 != '') { ?>
        initialPreview: [
            '<img src="/images/data/thumbs/<?php echo $package->img_2 ?>" class="file-preview-image">'
        ], <?php } ?>
        allowedFileExtensions: ["jpg", "png"],
        maxFileSize: 500,
        showUpload: false
    });

    $("#image3").fileinput({
        <?php if($package->img_3 != '') { ?>
        initialPreview: [
            '<img src="/images/data/thumbs/<?php echo $package->img_3 ?>" class="file-preview-image">'
        ], <?php } ?>
        allowedFileExtensions: ["jpg", "png"],
        maxFileSize: 500,
        showUpload: false
    });

    $('#image1, #image2, #image3').on('fileerror', function(event, file, previewId, index, reader) {
        $('#buttonSave').attr('disabled', 'disabled')
    });

    $('#image1, #image2, #image3').on('filebrowse', function(event) {
        $('#buttonSave').removeAttr('disabled');
    });

    $('#image1').on('filecleared', function(event) {
        $("#currentimage1").val("");
    });

    $('#image2').on('filecleared', function(event) {
        $("#currentimage2").val("");
    });

    $('#image3').on('filecleared', function(event) {
        $("#currentimage3").val("");
    });
@stop
