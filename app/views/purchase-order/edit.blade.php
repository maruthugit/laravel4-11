@extends('layouts.master')
@section('title', 'Purchase Order')
@section('content')



<?php 
// $tempKey = 'aDmIn';
// Crypt::setKey($tempKey);

$encrypted = Crypt::encrypt($po->id);
$encrypted = urlencode(base64_encode($encrypted));

$g_po_inv = true;
$tempcoupon = 0;
$tempamount = 0;
$tempcount = 1;
$tempid = $po->id;

$currency = Config::get('constants.CURRENCY');
$newinvdate = Config::get('constants.NEW_INVOICE_START_DATE');

?>
<style type="text/css">
    .proend:not(:empty) {
   background-color:#5cb85c;
   color:white;
}
</style>
<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Purchase Order Management
                <!-- <span class="pull-right"><a class="btn btn-default" title="" data-toggle="tooltip" href='{{asset('/')}}transaction'}}><i class="fa fa-reply"></i></a></span> -->
            </h1>
        </div>
        <!-- /.col-lg-12 -->
    </div>
    <!-- /.row -->
    <div class="row">
        <div class="col-lg-12">
        @if (Session::has('message'))
            <div class="alert alert-danger">
                <i class="fa fa-exclamation"></i> {{ Session::get('message') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
        @if (Session::has('success'))
            <div class="alert alert-success">
                <i class="fa fa-thumbs-up"></i> {{ Session::get('success') }}<button data-dismiss="alert" class="close" type="button">×</button>
            </div>
        @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Edit Purchase Order</h3>
                </div>
                <!-- /.panel-heading -->
                <div class="panel-body">
                    <div class="col-lg-12">
                        {{ Form::open(array('url'=>'purchase-order/update/'.$po->id, 'class' => 'form-horizontal')) }}
                            <div class="form-group">
                            {{ Form::label('id', 'PO No', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static"><?php

                                        $path = Config::get('constants.PO_PDF_FILE_PATH') . '/' . urlencode($po->po_no) . '.pdf';
                                        $file = ($po->id)."#".($po->po_no)."#". $path;
                                        $encrypted = Crypt::encrypt($file);
                                        $encrypted = urlencode(base64_encode($encrypted));

                                        ?>
                                        {{ HTML::link('purchase-order/files/'.$encrypted, $po->po_no, array('target'=>'_blank')) }}
                                        <?php
                                                                        
                                    ?></p>{{Form::input('hidden', 'id', $po->id)}}

                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('type', 'Type', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                    <p class="form-control-static">{{ (in_array((int)$po->type, array_keys($po_type)) ? $po_type[(int)$po->type] : 'PURCHASE ORDER') }}</p>
                                </div>
                            </div>
                            <div class="form-group">
                            {{ Form::label('type', 'Status', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-5">
                                     <p class="form-control-static">
                                    @if ($po->status == 1)
                                        Active
                                    @endif
                                    @if ($po->status == 4)
                                        Revised
                                    @endif
                                        
                                     </p>

                                </div>
                            </div>

                            <div class="form-group" >
                            {{ Form::label('transaction_date', 'PO Date', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-5">
                                    <p class="form-control-static">
                                    <?php 
                                        $po_date = date_create($po->po_date);
                                        echo date_format($po_date, 'Y-m-d');
                                    ?>   
                                    </p>{{Form::input('hidden', 'transaction_date', $po->transaction_date)}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('payment_terms', 'Payment', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    <p class="form-control-static">{{$po->payment_terms}} Days</p>{{Form::input('hidden', 'buyer_username', $po->buyer_username)}}
                                </div>
                            </div>
                            <?php 
                                        $delivery_date = date_create($po->delivery_date);
                                        $deldate=date_format($delivery_date, 'Y-m-d');
                                    ?>
                               
                            <div class="form-group required {{ $errors->first('delivery_date', 'has-error') }}">
                            {{ Form::label('delivery_date', 'Delivery Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_del">
                                    <input id="delivery_date" class="form-control" tabindex="1" name="delivery_date" type="text" value="{{ $deldate }}">
                                    <span class="input-group-btn">
                                        <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                    </span>
                                </div>
                                {{ $errors->first('delivery_date', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                            <div class="form-group">
                            {{ Form::label('from', 'From', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    <select class='form-control' name='from' id='from'>
                                        <option value='Tien Ming Distribution Sdn Bhd.' 
                                        <?php if ($po->from == 'Tien Ming Distribution Sdn Bhd.') echo ' selected'; ?>>
                                        Tien Ming Distribution Sdn Bhd.</option>
                                        <!--<option value='Jocom MShopping Sdn. Bhd.'-->
                                        <?php //if ($po->from == 'Jocom MShopping Sdn. Bhd.') echo ' selected'; ?>
                                        <!-->Jocom MShopping Sdn. Bhd.</option>-->
                                    </select>
                                </div>
                            </div>
                            
                            <div class="form-group">
                            {{ Form::label('seller', 'Seller', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    <p class="form-control-static">{{$po->company_name}}</p>{{Form::input('hidden', 'seller', $po->buyer_username)}}
                                </div>
                            </div>

                            <div class="form-group">
                            {{ Form::label('warehouse', 'Warehouse', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    <p class="form-control-static">{{$po->warehouse_name}}</p>{{Form::input('hidden', 'warehouse', $po->lang)}}
                                </div>
                            </div>

                            <div class='form-group'>
                                {{ Form::label('specialmsg', 'Special Message', array('class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-4">
                                {{ Form::textarea('specialmsg', $po->remark, array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                                {{ $errors->first('specialmsg', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>  

                            <hr />

                            <div class="form-group">
                            {{ Form::label('total', "Total Amount ($currency)", array('disabled','disabled','class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    {{ Form::text('total', number_format(abs($total), 2, '.', ''), array('required'=>'required','disabled'=>'disabled', 'class'=> 'form-control')) }}
                                  
                                </div>
                            </div>

                            <hr>

                            <div class="form-group @if ($errors->has('total_amount')) has-error @endif">
                            {{ Form::label('total_amount', "Created By", array('disabled','disabled','class'=> 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('total_amount', $po->created_by, array('required'=>'required','disabled'=>'disabled', 'class'=> 'form-control')) }}
                                </div>
                            </div>

                            <div class="form-group">
                                {{ Form::label('manager', 'Manager', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3">
                                    <select class='form-control' name='manager' id='manager'>
                                    @foreach ($managers as $manager)
                                        <option value='{{ $manager }}' 
                                        <?php if ($manager == $po->manager) echo 'selected'; ?>>{{ $manager }}
                                        </option>
                                    @endforeach
                                    </select>
                                </div>
                            </div>
                             <hr />

                             <div class="row">
                                <div class="col-lg-12">
                                    <div class="panel panel-default">
                                        <div class="panel-heading">
                                            Details
                                        </div>
                                        <!-- /.panel-heading -->
                                        <div class="panel-body">
                                            <div class="input-group">
                                                <div class="input-group-btn">
                                                    <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/purchase-order/pbx/editproducts"><i class="fa fa-plus"></i> Add Product</span>
                                                </div>
                                            </div>
                                            <br>
                                            <div class="dataTable_wrapper">
                                                <table class="table table-striped table-bordered table-hover" id="dataTables-details">
                                                    <thead>
                                                       <tr>
                                                            <th>Product Name</th>
                                                            <th>Label</th>
                                                            <th>SKU</th>
                                                            <th>Base Unit</th>
                                                            <th>Packing Factor</th>
                                                            <th>Qty</th>
                                                            <th>Price</th>
                                                            <th>Total Exclusive of SST</th>
                                                            <th>SST (RM)</th>
                                                            <th>Total Inclusive of SST</th>
                                                            <th class="text-center">Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="ptb">
                                                        @foreach($po_details as $trans_details)
                                                            <tr class="odd gradeX product" row-type="edit">
                                                                <td>{{$trans_details->product_name}}</td>
                                                                <td>{{$trans_details->price_label}}</td>                     
                                                                <td>{{$trans_details->sku}}<br>{{$trans_details->seller_sku}}</td>
                                                                <td>{{$trans_details->base_unit}}</td>
                                                                <td>{{$trans_details->packing_factor}}</td>
                                                                <td><input type="number" name="quantity[]" class="quantity" id="quantity" value="{{$trans_details->quantity}}"></td>
                                                                <td><input type="number" name="price[]" class="price" id="price" value="{{$trans_details->price}}"></td>
                                                                <td class="subtotal">{{number_format($trans_details->total,2)}}</td>
                                                                <td><input type="number" name="sst[]" class="sst" id="sst" value="{{number_format($trans_details->sst,2)}}"></td>
                                                                <td class="subtotal-sst">{{number_format($trans_details->total+$trans_details->sst,2)}}</td>
                                                                <input type="hidden" class="subtotal" name="subtotal[]" value="{{number_format($trans_details->total,2)}}">
                                                                <input type="hidden" name="edit_id[]" value="{{$trans_details->id}}">
                                                                <input type="hidden" class="edit_quantity" name="edit_quantity[]" value="{{$trans_details->quantity}}">
                                                                <input type="hidden" name="edit_price[]" value="{{$trans_details->price}}">
                                                                <input type="hidden" name="edit_total[]" value="{{$trans_details->total}}">
                                                                <input type="hidden" name="edit_sst[]" value="{{$trans_details->sst}}">
                                                                <input type="hidden" name="delete_id[]" value="">
                                                                <td class="text-center col-xs-1">
                                                                    <div class="btn-group">
                                                                        <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                            <?php $tempcount++; ?>
                                                        @endforeach

                                                    </tbody>
                                                </table>
                                            </div>
                                            <!-- /.table-responsive -->

                                        </div>
                                        <!-- /.panel-body -->
                                    </div>
                                    <!-- /.panel -->
                                </div>
                                <!-- /.col-lg-12 -->
                            </div>
                            <!-- /.row -->
                            <hr />
                            <div class="form-group required">
                                {{ Form::label('discchk', 'Discount ', array('class' => 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::checkbox('discchk', null, $po->discount_percent > 0, ['id' => 'discchk']) }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('discpercent', 'Discount %', array('class' => 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::number('discpercent', $po->discount_percent, ['class' => 'form-control', (($po->discount_percent> 0) ? '' : 'readonly')]) }}
                                </div>
                            </div>
                            <div class="form-group">
                                {{ Form::label('disctotal', 'Discounted Total', array('class' => 'col-lg-2 control-label')) }}
                                <div class="col-lg-3">
                                    {{ Form::text('disctotal', $po->discount_total, ['class' => 'form-control', 'readonly']) }}
                                </div>
                            </div>
                            <hr>
                            
                           @if($po->sign_po_path!=null)
                            <div class="form-group">
                            {{ Form::label('Signed PO', 'Signed PO', array('class'=> 'col-lg-2 control-label')) }}
                                 <div class="col-lg-3" style="width:50% !important">
                                    <?php 
                                      
                                     $path=$po->sign_po_path;
                                      $result=json_decode($path);
                                    ?>
                                   <ol>
               @foreach($result as $results)
               <?php $name = substr($results, strrpos($results, '_') + 1);
 ?>
                          <li><a href="/purchase-order/{{$results}}" style="margin-right:10px;margin-bottom:3px ">{{$name}}</a>    <i class="fa fa-trash btn btn-danger signedpo" aria-hidden="true" style="margin-bottom:8px" data-id="{{$po->id}}" data-value="{{$results}}"></i></li>
                   @endforeach
                              </ol>
                                </div>
                            </div>
                            <hr>
                            @endif
                            <div class="form-group">
                                 {{ Form::label('Upload', 'Upload', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                               <a class="btn btn-warning" id="signedpo" title="Upload Signed PO" data-toggle="modal" data-target="#myModal"  data-value="{{$po->id}}"><i class="fa fa-upload" aria-hidden="true"> Signed PO</i></a> 
                            </div>
                        </div> 
                        @if($po->einv_id != null && $po->einv_status == 1)
                        <div class="form-group">
                                 {{ Form::label('Logs', 'Logs', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                               <a class="btn btn-danger" id="logpo" title="View Logs" data-toggle="modal" data-target="#logModal"  data-value="{{$po->id}}"><i class="fa fa-eye" aria-hidden="true"> View Logs</i></a> 
                            </div>
                        </div><hr>
                             
                        <div id="logModal" class="modal fade" role="dialog">
                        <div class="modal-dialog" style="width:93%">
                        <!-- Modal content-->
                        <div class="modal-content">
                        <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">PO Revised Logs</h4>
                        </div>
                        <div class="modal-body">
                        <table class="table table-striped" id="revisedtable">
                        <thead>
                        <tr>
<!--                         <th scope="col">S.No</th>
 -->                        <th scope="col">PO</th>
                        <th scope="col">From</th>
                        <th scope="col">Special Message</th>
                        <th scope="col">Delivery Date</th>
                        <th scope="col">Manager</th>
                        <th scope="col">Discount </th>
                        <th scope="col">Product</th>
                        <th scope="col">Deleted Product</th>
                        <th scope="col">Added Product</th>
                        <th scope="col">User</th>
                        <th scope="col">Date</th>


                        </tr>
                        </thead>
                        <tbody>
                        <?php $i=1;?>
                            @foreach($po_logs as $result) 
                                               
                        <tr>
<!--                         <th scope="row"><b>{{$i}}</b></th>
 -->                        <td><b>{{$result->po_id}}</b></td>
                        @if($result->from_data!=$result->from_data_old)
                        <td style="color:white;background:#5cb85c;">
                            Old: {{$result->from_data_old}} <br><br>
                            New:<b>{{$result->from_data}}</b>
                        </td>
                        @else
                        <td></td>
                        @endif
                         @if($result->remark!=$result->remark_old)
                        <td style="color:white;background:#5cb85c;">
                            Old: {{$result->remark_old}} <br><br>
                            New:<b>{{$result->remark}}</b>
                        </td>
                        @else
                        <td></td>
                        @endif
                         @if($result->delivery_date!=$result->delivery_date_old)
                        <td style="color:white;background:#5cb85c;">
                            Old: {{$result->delivery_date_old}} <br><br>
                            New:<b>{{$result->delivery_date}}</b>
                        </td>
                        @else
                        <td></td>
                        @endif
                        @if($result->manager!=$result->manager_old)
                        <td style="color:white;background:#5cb85c;">
                            Old: {{$result->manager_old}} <br><br>
                            New:<b>{{$result->manager}}</b>
                        </td>
                        @else
                        <td></td>
                        @endif
                         @if($result->discount_percent!=$result->discount_percent_old)
                        <td style="color:white;background:#5cb85c;">
                            Old: {{$result->discount_percent_old}} <br><br>
                            @if($result->discount_percent=="")
                            New:<b>0</b>
                            @else
                            New:<b>{{$result->discount_percent}}</b>
                            @endif
                        </td>
                        @else
                        <td></td>
                        @endif 
                        <?php 
                        $product=json_decode($result->product);
                        $product_old=json_decode($result->product_old);
                        $partial=array();
                        foreach ((array)$product as $key => $value) {
                               if($value->quantity!=$product_old[$key]->quantity){
                               $partial[$key]['sku']=$value->sku;
                                $partial[$key]['quantity']=$value->quantity;
                               }
                               else{
                                 $partial[$key]['sku']=$value->sku;
                              $partial[$key]['quantity']="No Update";       
                               }
                               if($value->price!=$product_old[$key]->price){
                                $partial[$key]['price']=$value->price;
                               }
                               else{
                              $partial[$key]['price']="No Update";

                               }
                             if($value->sst!=$product_old[$key]->sst){
                                $partial[$key]['sst']=$value->sst;
                               }
                               else{
                              $partial[$key]['sst']="No Update";

                               }
                               
                        }

                        ?>
                      
                           <td class="proend">@foreach($partial as $final_product) @if($final_product['quantity']=="No Update" && $final_product['price']=="No Update" && $final_product['sst']=="No Update")@endif @if($final_product['quantity']!="No Update" || $final_product['price']!="No Update" || $final_product['sst']!="No Update")<p>SKU:{{$final_product['sku']}}</p>@endif
                            @if($final_product['quantity']!="No Update" && $final_product['quantity']!="")<p>QTY:{{$final_product['quantity']}}</p>@endif @if($final_product['price']!="No Update" && $final_product['price']!="")<p>PRICE:{{$final_product['price']}}</p>@endif @if($final_product['sst']!="No Update" && $final_product['sst']!="")<p>SST:{{$final_product['sst'] }}</p>@endif @endforeach</td>
                            @if($result->product_delete=="") 
                          <td></td>
                          @else
                          <?php $del=json_decode($result->product_delete);?>
                          <td style="color:white;background:#5cb85c;">
                            <b>
                              @foreach($del as $del_result)
                                <p>Product ID: {{$del_result}}</p>
                              @endforeach
                            </b>
                           
                          </td>
                          @endif 
                        @if($result->product_add=="") 
                          <td></td>
                          @else
                          <?php $add=json_decode($result->product_add);?>
                          <td style="color:white;background:#5cb85c;">
                            <b>
                              @foreach($add as $add_result)
                                <p>Product ID: {{$add_result->product_id}}</p>
                                <p>Quantity: {{$add_result->quantity}}</p>
                              @endforeach
                            </b>
                           
                          </td>
                          @endif                         
                        <td>                         
                        <b>{{$result->updated_by}}</b>
                        </td>
                       
                        <td><b>{{$result->insert_date}}</b></td>                    
                        </tr>
                         <?php $i++;?>
                        @endforeach
                        </tbody>
                        </table>
                        </div>

                        </div>
                        </div>

                        </div>
                        

                            @endif
                      
                             @if ( Permission::CheckAccessLevel(Session::get('role_id'), 29, 3, 'AND'))
                            <div class="form-group">
                                <div class="col-lg-12">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::button('Save', array('class'=>'btn btn-primary', 'data-toggle'=>'tooltip', 'onclick'=>'submit()'))}}
                                </div>
                            </div>
                            @endif
                         
                        {{ Form::close() }}
   <div id="myModal" class="modal fade" role="dialog">
  <div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Upload Signed PO</h4>
      </div>
      <div class="modal-body">
        <form method="post" enctype="multipart/form-data"  action="/purchase-order/uploadsignpo">
            <div class="form-group">
           <input type="file" name="signedpo[]" class="form-control" accept=".pdf" multiple required>
           <input type="hidden" name="signpoid" value="" id="signpoid">

       </div>
       
      </div>

      <div class="modal-footer">
       <button type="submit" class="btn btn-primary" style="float: right;">Submit</button>
      </div> 

        </form>
    </div>

  </div>
</div> 
                    </div>

                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

        </div>
        <!-- /.col-lg-12 -->
    </div>
    </div>

@stop

@section('script')
$('#datetimepicker_del').datetimepicker({
        format: 'YYYY-MM-DD'
    });
$('#addProdBtn').colorbox({
    iframe:true, width:"90%", height:"90%",
    onClosed:function(){
        calculateSubTotal();
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
    var total = 0.00;
    $('.subtotal-sst').each(function() {
        total += parseFloat($(this).html().replace(/,/g, ''));
    });
    $('#disctotal').val(currencyFormat(total - (total * this.value) / 100));
});

function currencyFormat(num) {
    return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
}

$(document).on("change", 'input[name="quantity[]"], input[name="price[]"], .sst', function() {
console.log(3);
    var rowType = $(this).parent().parent().attr('row-type');
    
    calculateSubTotal(this, rowType);
});

function calculateSubTotal(input, rowType) {

    var total = 0;

    $('tr.product').each(function() {
        var quantity = $(this).find('#quantity').val();
        var price = $(this).find('#price').val();

        var subtotal = quantity * price;
        $(this).find('.subtotal').html(currencyFormat(subtotal));
        $(this).find('input[name="subtotal[]"]').val(currencyFormat(subtotal));

        var sst = $(this).find('.sst').val();
        $(this).find('.subtotal-sst').html(currencyFormat(subtotal + parseFloat(sst)));

        total = total + subtotal + parseFloat(sst);
    });

    $('#total').val(currencyFormat(total));

    var tr = $(input).parent().parent();
    if (rowType == 'edit') {
        
        tr.find('input[name="edit_quantity[]"]').val(tr.find('td input.quantity').val());
        tr.find('input[name="edit_price[]"]').val(tr.find('td input.price').val());
        tr.find('input[name="edit_total[]"]').val(tr.find('input.subtotal').val());
        tr.find('input[name="edit_sst[]"]').val(tr.find('input.sst').val());
    } else if (rowType == 'add') {
        tr.find('input[name="add_quantity[]"]').val(tr.find('td input.quantity').val());
        tr.find('input[name="add_price[]"]').val(tr.find('td input.price').val());
        tr.find('input[name="add_total[]"]').val(tr.find('input.subtotal').val());
        tr.find('input[name="add_sst[]"]').val(tr.find('input.sst').val());
    }

    calculateDiscount(total);

}

function calculateDiscount(total) {
    var discountPercen = $('#discpercent').val();

    if (discountPercen != 0) {
        $('#disctotal').val(currencyFormat(total - (total * $('#discpercent').val()) / 100));
    }
    
}

$(document).on("click", "#deleteItem", function(e) {

    var tr = $(this).parent().parent().parent();
    var rowType = tr.attr('row-type');

    if (rowType == "add") {
        $(this).closest("tr").remove();
    } else if (rowType == "edit") {
        var editId = tr.find('input[name="edit_id[]"]').val();
        tr.find('input[name="quantity[]"]').val('');
        tr.find('input[name="price[]"]').val('');
        tr.find('input[name="subtotal[]"]').val('');
        tr.find('input[name="sst[]"]').val('');
        tr.find('input[name="delete_id[]"]').val(editId);
        tr.hide();
    }
    
    calculateSubTotal($(this).parent(), rowType);

});
$(document).on("click", "#signedpo", function(e) {
        var link = $(this).attr("href");
        var status=$(this).attr("data-value");
        e.preventDefault();
        $('#signpoid').val(status);


    });
    $(document).ready(function(){
  $('.proend').each(function(){
  if($(this).html()=="                                                                  " || $(this).html=="                                 "){
  $(this).html("");
}

 if($(this).html()=="                                 "){
  $(this).html("");
}
});
});
$(document).ready(function() {
    $('#revisedtable').DataTable({
        "pageLength": 4,
            "ordering": false,
"bLengthChange": false,
   "searching": false

});
} );


    $(document).on("click", ".signedpo", function(e) {
        var link = $(this).attr("data-value");
        var id=$(this).attr("data-id");
        var path='/purchase-order/signedpo';
        e.preventDefault();
        bootbox.confirm({
            title: "Delete Signed PO",
            message: "Are you sure want to  Delete this Signed PO Document of  - " + $(this).attr("data-id") + " ?",
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