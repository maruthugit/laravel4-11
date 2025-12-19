@extends('layouts.master')
@section('title', 'Goods Defect')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Edit Goods Defect Form</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/gdf/update/'.$gdf->id,'files'=>true,'class' => 'form-horizontal')) }}
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
                        <div class="form-group">
                            {{ Form::label('gdf_no', 'GDF No', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <p class="form-control-static"><?php

                                        $path = Config::get('constants.GDF_PDF_FILE_PATH') . '/' . urlencode($gdf->gdf_no) . '.pdf';
                                        $file = ($gdf->id)."#".($gdf->gdf_no)."#". $path;
                                        $encrypted = Crypt::encrypt($file);
                                        $encrypted = urlencode(base64_encode($encrypted));

                                        ?>
                                        {{ HTML::link('gdf/files/'.$encrypted, $gdf->gdf_no, array('target'=>'_blank')) }}
                                </p>{{Form::input('hidden', 'id', $gdf->id)}}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('type', 'has-error') }}">
                            {{ Form::label('type', 'Please select one of the option ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='type' id='type'>
                                    <option value="Exchanged" <?php if ($gdf->type == 'Exchanged') echo 'selected' ?>>Exchanged</option>
                                    <option value="Returned To Vendor" <?php if ($gdf->type == 'Returned To Vendor') echo 'selected' ?>>Returned To Vendor</option>
                                    <option value="Issue Credit Note" <?php if ($gdf->type == 'Issue Credit Note') echo 'selected' ?>>Issue Credit Note</option>
                                    <option value="Expired / Write Off" <?php if ($gdf->type == 'Expired / Write Off') echo 'selected' ?>>Expired / Write Off</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group seller-toggle">
                            {{ Form::label('seller', 'Seller ', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="seller_id" name="seller_id" value="{{$gdf->seller_id}}">
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="seller" name="seller" class="form-control" value="{{$seller_name}}" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectSellerBtn" id="selectSellerBtn"  type="button" href="/purchase-order/seller-list"><i class="fa fa-plus"></i> Seller </button>
                                </span>
                                </div><!-- /input-group -->
                                {{ $errors->first('warehouse_id', '<p class="help-block">:message</p>') }}
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
                                        @foreach ($details as $detail)
                                            <tr row-type="edit">
                                                <td>{{$detail->sku}}</td>
                                                <td>{{$detail->name}}</td>
                                                <td><input type="number" name="quantity[]" class="quantity" id="quantity" value="{{$detail->quantity}}"></td>
                                                <td><input type="text" name="remark[]" value="{{$detail->remark}}"></td>
                                                <td class="text-center col-xs-1">
                                                    <div class="btn-group">
                                                        <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>
                                                    </div>
                                                </td>
                                                <input type="hidden" name="product_id[]" value="{{$detail->product_id}}">
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>{{ $errors->first('product_id', '<p class="help-block" style="color: #a94442">:message</p>') }}
                            </div>
                        </div>
                        <hr>
                        <div class="form-group required {{ $errors->first('warehouse_id', 'has-error') }}">
                            {{ Form::label('warehouse', 'Warehouse ', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="warehouse_id" name="warehouse_id" value="{{$gdf->warehouse_id}}">
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="warehouse" name="warehouse" class="form-control" value="{{$gdf->warehouse_name}}" readonly>
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
                                {{ Form::text('reason', $gdf->reason, ['class' => 'form-control']) }}
                                {{ $errors->first('reason', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>
                        <div class="form-group required {{ $errors->first('type', 'has-error') }}">
                            {{ Form::label('current_status', 'Status', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='current_status' id='current_status'>
                                    <option value="Pending" <?php if ($gdf->current_status == 'Pending') echo 'selected' ?>>Pending</option>
                                    <option value="Completed" <?php if ($gdf->current_status == 'Completed') echo 'selected' ?>>Completed</option>
                                </select>
                            </div>
                        </div>
                         @if($gdf->gdf_image_path!=null)
                        <div class="form-group">
                            {{ Form::label('Images', 'Images', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3" style="width:50% !important">
                                    <?php 
                                      
                                     $path=$gdf->gdf_image_path;
                                      $result=json_decode($path);
                                    ?>
                                   <ol>
               @foreach($result as $results)
               <?php $name = substr($results, strrpos($results, '_') + 1);
 ?>
                          <li><a href="../../public/public/gdf-img/upload/{{$results}}" style="margin-right:10px;margin-bottom:3px " download="{{$results}}">{{$name}}</a>    <i class="fa fa-trash btn btn-danger gdfimg" aria-hidden="true" style="margin-bottom:8px" data-id="{{$gdf->id}}" data-value="{{$results}}"></i></li>
                   @endforeach
                              </ol>
                                </div>
                            </div>
                            @endif
                                <div class="form-group  {{ $errors->first('image', 'has-error') }}">
                            {{ Form::label('Upload Image', 'Upload Image', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <input type="file" name="image[]" class="form-control" multiple>
                                {{ $errors->first('image', '<p class="help-block">:message</p>') }}
                            </div>
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

$('#addProdBtn').colorbox({
    iframe:true, width:"90%", height:"90%",
    onClosed:function(){

    }
});

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

    var tr = $(this).parent().parent().parent();
    var rowType = tr.attr('row-type');

    $(this).closest("tr").remove();

});
$(document).on("click", ".gdfimg", function(e) {
        var link = $(this).attr("data-value");
        var id=$(this).attr("data-id");
        var path='/gdf/deleteimage';
        e.preventDefault();
        bootbox.confirm({
            title: "Delete",
            message: "Are you sure want to  Delete this Image",
            callback: function(result) {
                if (result === true) {
                 window.location=window.location.origin+path+'/'+link+'/'+id;
                } else {
                    console.log("IGNORE");
                }
            } 
        });
    });

@stop