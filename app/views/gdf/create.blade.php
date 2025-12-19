@extends('layouts.master')
@section('title', 'Goods Defect Form')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add New Goods Defect Form</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/gdf/store','files'=>true,'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Goods Defect Form Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                <i class="fa"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                            </div>
                        @endif
                        <div class="form-group required {{ $errors->first('type', 'has-error') }}">
                            {{ Form::label('type', 'Please select one of the option ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='type' id='type'>
                                    <option value="Exchanged">Exchanged</option>
                                    <option value="Returned To Vendor">Returned To Vendor</option>
                                    <option value="Issue Credit Note">Issue Credit Note</option>
                                    <option value="Expired / Write Off">Expired / Write Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="seller-toggle form-group">
                            {{ Form::label('seller', 'Seller ', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="seller_id" name="seller_id" value=<?php echo (Input::old('seller_id') != null) ? Input::old('seller_id') : 0; ?>>
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="seller" name="seller" class="form-control" value="{{Input::old('seller')}}" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectSellerBtn" id="selectSellerBtn"  type="button" href="/purchase-order/seller-list"><i class="fa fa-plus"></i> Seller </button>
                                </span>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                        </div>
                        <hr>
                        <div class="form-group required @if ($errors->has('product_id')) has-error @endif">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/gdf/products"><i class="fa fa-plus"></i> Add Product</span>
                                    </div>
                                </div>
                                <br />
                            <div class="clearfix">{{ $errors->first('', '<p class="help-block">:message</p>') }}</div>
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-2">SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-3">Product Name</th>
                                            <th class="cell-small col-sm-1">Quantity</th>
                                            <th class="cell-small col-sm-1">Remark</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                    </tbody>
                                    <input type="hidden" id="total_amount">
                                </table>{{ $errors->first('product_id', '<p class="help-block" style="color: #a94442">:message</p>') }}{{ $errors->first('quantity.*', '<p class="help-block" style="color: #a94442">:message</p>') }}
                            </div>
                        </div>
                        <hr>
                        <div class="form-group required {{ $errors->first('warehouse_id', 'has-error') }}">
                            {{ Form::label('warehouse', 'Warehouse ', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{Input::old('warehouse_id')}}">
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="warehouse" name="warehouse" class="form-control" value="{{Input::old('warehouse')}}" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectWarehouseBtn" id="selectWarehouseBtn"  type="button" href="/purchase-order/warehouse-list"><i class="fa fa-plus"></i> Warehouse </button>
                                </span>
                                </div><!-- /input-group -->
                                {{ $errors->first('warehouse_id', '<p class="help-block">:message</p>') }}
                            </div><!-- /.col-lg-6 -->
                        </div>

                        <div class="form-group required {{ $errors->first('reason', 'has-error') }}">
                            {{ Form::label('reason', 'Reasons', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                {{ Form::text('reason', null, ['class' => 'form-control']) }}
                                {{ $errors->first('reason', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('type', 'has-error') }}">
                            {{ Form::label('current_status', 'Status', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='current_status' id='current_status'>
                                    <option value="Pending">Pending</option>
                                    <option value="Completed">Completed</option>
                                
                                </select>
                            </div>
                        </div>
                        <div class="form-group  {{ $errors->first('image', 'has-error') }}">
                            {{ Form::label('Upload Image', 'Upload Image', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <input type="file" name="image[]" class="form-control" multiple>
                                {{ $errors->first('image', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->
        </div>
    @if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 5, 'AND'))
    <div class='form-group' >
        <div class="col-lg-10" style="padding-bottom:10px;">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', ['class' => 'btn btn-large btn-primary']) }}
            <!--Under Upgrading .. Wait for a moment.-->
        </div>
    </div>
    @else 
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

$(function () {
    $('[data-toggle="popover"]').popover({ trigger: "hover" });
})

$('#selectWarehouseBtn').colorbox({
    iframe:true, width:"90%", height:"90%",
    onClosed: function() {
        localStorage.clear();
    }
});

$('#selectSellerBtn').colorbox({
    iframe:true, width:"90%", height:"90%",
    onClosed: function() {
        localStorage.clear();
    }
});

$('#addProdBtn').colorbox({
    iframe:true, width:"90%", height:"90%",
    onClosed:function(){ 
}
   
});

$('#type_').change(function() {

    if ($(this).val() == 'Returned To Vendor') {
        $('.seller-toggle').show();
    } else {
        $('.seller-toggle').hide();
        $('#seller_id').val(0);
        $('#seller').val('');
    }
    
});

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