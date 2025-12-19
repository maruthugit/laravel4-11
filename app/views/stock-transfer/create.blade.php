@extends('layouts.master')
@section('title', 'Stock Transfer')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add New Stock Transfer</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/stock-transfer/store', 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Stock Transfer Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                <i class="fa"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                            </div>
                        @endif
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
                        <div class="form-group">
                           {{ Form::label('', '', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <input type="checkbox" class="" name="address_check" id="address_check" value="0"> New Address</input>
                              </div>
                        </div>
                        <div class="form-group required {{ $errors->first('seller_id', 'has-error') }}">
                            {{ Form::label('seller', 'Ship To ', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="seller_id" name="seller_id" value="{{Input::old('seller_id')}}">
                            <div class="col-lg-3">
                                <div class="input-group" id="seller_display">
                                <input type="text" id="seller" name="seller" class="form-control" value="{{Input::old('seller')}}" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectSellerBtn" id="selectSellerBtn"  type="button" href="/purchase-order/seller-list"><i class="fa fa-plus"></i> Seller</button>
                                </span>
                                </div><!-- /input-group -->
                                {{ $errors->first('seller_id', '<p class="help-block">:message</p>') }}
                            </div><!-- /.col-lg-6 -->
                        </div>
                        <div class="form-group required {{ $errors->first('warehouse_id', 'has-error') }}">
                            {{ Form::label('warehouse', 'Ship From ', array('class'=> 'col-lg-2 control-label')) }}
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
                        <div class='form-group'>
                            {{ Form::label('remark', 'Remark', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::textarea('remarks', '', array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            {{ $errors->first('remarks', '<p class="help-block">:message</p>') }}
                            </div>
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
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-2">SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-3">Product Name</th>
                                            <th class="cell-small col-sm-1">Quantity</th>
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
    });

    $(document).on("click", "#deleteItem", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
    });
       $(document).ready(function(){
   
   $(document).on("click", "#address_check", function() {
                var checked = $('#address_check').is(':checked');
       if(checked){
       $('#address_check').val("1");
          $('#seller_display').html('<b>Seller Name:</b><input type="text" class="form-control" name="new_seller_name" id="new_seller_name" style="margin-bottom: 15px" value=""><br><b>Address 1:</b><input type="text" class="form-control" name="address_1" style="margin-bottom: 15px" id="address_1" value=""><br><b>Address 2:</b><input type="text" class="form-control" name="address_2" style="margin-bottom: 15px" id="address_2" value=""><br><b>Postcode:</b><input type="text" class="form-control" name="postcode" style="margin-bottom: 15px" id="postcode" value=""><br><b>City:</b><input type="text" class="form-control" name="city" style="margin-bottom: 15px" id="city" value=""><br><b>State:</b><input type="text" class="form-control" name="state" style="margin-bottom: 15px" id="state" value=""><br><b>Tel Number:</b><input type="text" class="form-control" name="tel_num" style="margin-bottom: 15px" id="tel_num" value=""><br><b>Attn:</b><input type="text" class="form-control" name="attn" style="margin-bottom: 15px" id="attn" value="">');
            $('#seller_id').val("0");
        }else{
        $('#address_check').val("0");
          $('#seller_display').html('<input type="text" id="seller" name="seller" class="form-control" value="" readonly><span class="input-group-btn"><button class="btn btn-primary selectSellerBtn" id="selectSellerBtn"  type="button" href="/purchase-order/seller-list"><i class="fa fa-plus"></i> Seller</button></span>');
         
          }
        $('#selectSellerBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    });
   });

@stop