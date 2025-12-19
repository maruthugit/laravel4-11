@extends('layouts.master')
@section('title', 'Stock Requisition')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add New Stock Requisition</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/stock-requisition/store', 'class' => 'form-horizontal')) }}
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
                        <div class="form-group required {{ $errors->first('delivery_date', 'has-error') }}">
                            {{ Form::label('delivery_date', 'Campaign From Date', array('class' => 'col-lg-2 control-label')) }}
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
                        <div class="form-group required {{ $errors->first('campaign_end', 'has-error') }}">
                            {{ Form::label('campaign_end', 'Campaign End Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_to">
                                    <input id="campaign_end" class="form-control" tabindex="1" name="campaign_end" type="text" value="{{ Input::old('campaign_end') }}">
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
                                <select class="form-control" name="platform" >
                                    @if($platforms)
                                    @foreach($platforms as $platform)
                                    <option value="{{$platform->platform_name}}">{{$platform->platform_name}}</option>
                                    @endforeach
                                    @endif
                                </select>
                                
                            </div><!-- /.col-lg-6 -->
                            {{ $errors->first('platform', '<p class="help-block">:message</p>') }}
                        </div>
                        <div class="form-group required {{ $errors->first('warehouse_id', 'has-error') }}">
                            {{ Form::label('warehouse', 'To Warehouse', array('class'=> 'col-lg-2 control-label')) }}
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
                            {{ Form::label('total_remarks', 'Remark', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::textarea('total_remarks', '', array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
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
    $('#old_customer').prop('checked', true);
     $('#old_platform').prop('checked', true);
    $(function () {
        $('[data-toggle="popover"]').popover({ trigger: "hover" });
    })
    $('.expiry_dates').datetimepicker({
        format: 'YYYY-MM-DD'
    });


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
            url: "address",
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
    $(document).on("click", "input[name='customer']", function() {
        
         var ischeck=$("input[name='customer']:checked").val();
       if(ischeck=="1"){
          $('#customer_select').html('<input type="text" id="user" name="customer_name" class="form-control" value="" readonly> <span class="input-group-btn"><button class="btn btn-primary selectUserBtn" id="selectUserBtn"  type="button" href="/transaction/ajaxcustomer"><i class="fa fa-plus"></i> Customer</button></span>');
        }else{
          $('#customer_select').html('<input type="text" id="user" name="customer_name" class="form-control" value="">');
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
        $(document).on("click", "input[name='platfrom_name']", function() {
        
         var ischecks=$("input[name='platfrom_name']:checked").val();
       if(ischecks=="1"){
          $('#platform_display').html('<select class="form-control" name="platform">@if($platforms) @foreach($platforms as $platform)<option value="{{$platform->platform_name}}">{{$platform->platform_name}}</option> @endforeach @endif</select>');
        }else{
          $('#platform_display').html('<input type="text" id="platforms" name="platform" class="form-control" value="">');
          }

    })
@stop