@extends('layouts.master')
@section('title', 'Stock Requisition')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Update Stock Requisition</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/stock-requisition/update/'.$stock_transfer->id, 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Stock Requisition Form</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        @if (Session::has('message'))
                            <div class="alert alert-success">
                                <i class="fa"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
                            </div>
                        @endif
                        <div class="form-group">
                            {{ Form::label('st_no', 'Stock Requisition No', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static"><?php

                                        $path = Config::get('constants.STOCK_TRANSFER_PDF_FILE_PATH') . '/' . urlencode($stock_transfer->st_no) . '.pdf';
                                        $file = ($stock_transfer->id)."#".($stock_transfer->st_no)."#". $path;
                                        $encrypted = Crypt::encrypt($file);
                                        $encrypted = urlencode(base64_encode($encrypted));

                                        ?>
                                        {{ HTML::link('stock-requisition/files/'.$encrypted, $stock_transfer->st_no, array('target'=>'_blank')) }}
                                </p>{{Form::input('hidden', 'id', $stock_transfer->id)}}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('delivery_date', 'has-error') }}">
                            {{ Form::label('delivery_date', 'Campaign From Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="deliverydate_from">
                                    <input id="delivery_date" class="form-control" tabindex="1" name="delivery_date" type="text" value="{{ $stock_transfer->delivery_date }}">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                                {{ $errors->first('delivery_date', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        
                        <div class="form-group required {{ $errors->first('campaign_end', 'has-error') }}">
                            {{ Form::label('campaign_end', 'Campaign End Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_to">
                                    <input id="campaign_end" class="form-control" tabindex="1" name="campaign_end" type="text" value="{{ $stock_transfer->campaign_end }}">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                                {{ $errors->first('campaign_end', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        
                        <div class="form-group required {{ $errors->first('platform_id', 'has-error') }}">
                            {{ Form::label('platform', 'Platform', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3" id="platform_display">
                               <select class="form-control" name="platform">
                                   @if($platforms) @foreach($platforms as $platform)
                        <option value="{{$platform->platform_name}}" @if($stock_transfer->platform==$platform->platform_name) selected="selected" @endif>{{$platform->platform_name}}</option> 
                                   @endforeach 
                                   @endif
                                   </select>
                            </div><!-- /.col-lg-6 -->
                             {{ $errors->first('platform', '<p class="help-block">:message</p>') }}
                        </div>
                        <div class="form-group required {{ $errors->first('warehouse_id', 'has-error') }}">
                            {{ Form::label('warehouse', 'Warehouse', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$stock_transfer->warehouse_id}}">
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="warehouse" name="warehouse" class="form-control" value="{{$stock_transfer->warehouse_name}}" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectWarehouseBtn" id="selectWarehouseBtn"  type="button" href="/purchase-order/warehouse-list"><i class="fa fa-plus"></i> Warehouse </button>
                                </span>
                                </div><!-- /input-group -->
                                {{ $errors->first('warehouse_id', '<p class="help-block">:message</p>') }}
                            </div><!-- /.col-lg-6 -->
                        </div>
                    
                        <div class='form-group'>
                            {{ Form::label('total_remarks', 'Remark', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                <textarea class="form-control" autofocus='autofocus' name="total_remarks">{{$stock_transfer->remark}}</textarea>
                            {{ $errors->first('total_remarks', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>   
                        <hr>
                        <div class="form-group required @if ($errors->has('product_id')) has-error @endif">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/stock-requisition/products"><i class="fa fa-plus"></i> Add Product</span>
                                    </div>
                                </div>
                                <br />
                                <table class="table table-bordered ">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-2">SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-3">Product Name</th>
                                            <th class="cell-small col-sm-1">Quantity</th>
                                            <th class="cell-small col-sm-4">Expiry Date</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                        @foreach ($details as $detail)
                                            <tr row-type="edit">
                                                <td>{{$detail->sku}}</td>
                                                <td>{{$detail->name}}</td>
                                                <td><input type="number" name="quantity[]" class="quantity" id="quantity" value="{{$detail->quantity}}"></td>
                                                <td><div class="input-group expiry_dates"><input id="expiry_date" class="expiry_date form-control" tabindex="1" name="expiry_date[]" type="text" value="{{$detail->expiry_date}}"><span class="input-group-btn"><button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button></span></div></td>
                                                <td class="text-center col-xs-1">
                                                    <div class="btn-group">
                                                        <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>
                                                    </div>
                                                </td>
                                                <input type="hidden" name="product_id[]" value="{{$detail->product_id}}">
                                            </tr>
                                        @endforeach
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
    @endif
    {{ Form::close() }}
</div>
    
@stop

@section('script')

    $('#deliverydate_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    $('.expiry_dates').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $(function () {
        $('[data-toggle="popover"]').popover({ trigger: "hover" });
    })


    $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
             var user_id=$('#user_id').val();
             load_address(user_id);
        },
       
        
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
         $('.expiry_dates').datetimepicker({
        format: 'YYYY-MM-DD'
    });
        }
    });

    $(document).on("click", "#deleteItem", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
    });
    function load_address(user_id){
          var id=user_id;
          $('#address_1').val("");
             $('#address_2').val("");
             $('#postcode').val("");
             $('#city').val("");
             $('#state').val("");
             $('#tel_num').val("");
          $.ajax({
            type: 'GET',
            url: "../address",
            data:{'id':id},
            success:function(data){
             $('#address_1').val(data.address1);
             $('#address_2').val(data.address2);
             $('#postcode').val(data.postcode);
             $('#city').val(data.city);
             $('#state').val(data.state);
             $('#tel_num').val(data.mobile_no);
            }
        });
          
    }
 $(document).ready(function(){
       var sdefault=<?php echo $customer;?>;
        if(sdefault=='1'){
        $('#old_customer').prop('checked', true);
        }else{
        $('#new_customer').prop('checked', true);
        }
       var ischeck=$("input[name='customer']:checked").val();
        if(ischeck=="1"){
          $('#customer_select').html('<input type="text" id="user" name="customer_name" class="form-control" value="{{$stock_transfer->customer_name}}" readonly> <span class="input-group-btn"><button class="btn btn-primary selectUserBtn" id="selectUserBtn"  type="button" href="/transaction/ajaxcustomer"><i class="fa fa-plus"></i> Customer</button></span>');
        }else{
          $('#customer_select').html('<input type="text" id="user" name="customer_name" class="form-control" value="{{$stock_transfer->customer_name}}">');
          }
    $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
             var user_id=$('#user_id').val();
             load_address(user_id);
        },
       
        
    });
    $(document).on("click", "input[name='customer']", function() {
        
         var ischeck=$("input[name='customer']:checked").val();
       if(ischeck=="1"){
          $('#customer_select').html('<input type="text" id="user" name="customer_name" class="form-control" value="{{$stock_transfer->customer_name}}" readonly> <span class="input-group-btn"><button class="btn btn-primary selectUserBtn" id="selectUserBtn"  type="button" href="/transaction/ajaxcustomer"><i class="fa fa-plus"></i> Customer</button></span>');
        }else{
          $('#customer_select').html('<input type="text" id="user" name="customer_name" class="form-control" value="{{$stock_transfer->customer_name}}">');
          }
     $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
             var user_id=$('#user_id').val();
             load_address(user_id);
        },
       
        
    });
    });
     <!--var platformdefault=<?php echo $is_platform;?>;-->
     <!--   if(platformdefault=='1'){-->
     <!--   $('#old_platform').prop('checked', true);-->
     <!--   }else{-->
     <!--   $('#new_platform').prop('checked', true);-->
     <!--   }-->
     <!--  var ischecked=$("input[name='platfrom_name']:checked").val();-->
     <!--   if(ischecked=="1"){-->
     <!--     $('#platform_display').html('<select class="form-control" name="platform">@if($platforms) @foreach($platforms as $platform)<option value="{{$platform->platform_name}}" @if($stock_transfer->platform==$platform->platform_name) selected="selected" @endif >{{$platform->platform_name}}</option> @endforeach @endif</select>');-->
     <!--   }else{-->
     <!--     $('#platform_display').html('<input type="text" id="platforms" name="platform" class="form-control" value="{{$stock_transfer->platform}}">');-->
     <!--     }-->
    $(document).on("click", "input[name='platfrom_name']", function() {
        
         var ischecks=$("input[name='platfrom_name']:checked").val();
       if(ischecks=="1"){
          $('#platform_display').html('<select class="form-control" name="platform">@if($platforms) @foreach($platforms as $platform)<option value="{{$platform->platform_name}}" @if($stock_transfer->platform==$platform->platform_name) selected="selected" @endif >{{$platform->platform_name}}</option> @endforeach @endif</select>');
        }else{
          $('#platform_display').html('<input type="text" id="platforms" name="platform" class="form-control" value="{{$stock_transfer->platform}}">');
          }

    });
    });
    
@stop