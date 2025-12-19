@extends('layouts.master')
@section('title', 'Goods Received Note GRN')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Edit GRN</h1>
              <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('stock') }}"><i class="fa fa-reply"></i></a>
                </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
                  
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Goods Received Note</h3>
                </div>
                <div class="panel-body">

                      {{ Form::open(['url' => 'grn/update', 'method' => 'post']) }}
                 <div class="form-horizontal">    

                    <div class="form-group">
                    {{ Form::label('id', 'GRN No', array('class'=> 'col-lg-2 control-label')) }}
                         <div class="col-lg-5">
                             <p class="form-control-static"><?php

                                $path = Config::get('constants.GRN_PDF_FILE_PATH') . '/' . urlencode($grn->grn_no) . '.pdf';
                                $file = ($grn->id)."#".($grn->grn_no)."#". $path;
                                $encrypted = Crypt::encrypt($file);
                                $encrypted = urlencode(base64_encode($encrypted));

                                ?>
                                {{ HTML::link('grn/files/'.$encrypted, $grn->grn_no, array('target'=>'_blank')) }}
                                <?php
                                                                
                            ?></p>{{Form::input('hidden', 'grn_id', $grn->id)}}
                            {{ $errors->first('grn_id', '<p class="help-block">:message</p>') }}

                        </div>
                    </div>

                    <div class="form-group">
                        <label  class="col-lg-2 control-label">GRN Date</label>
                        <div class="col-lg-2">
                            <p class="form-control-static">{{$grn->grn_date}}</p>
                        </div>
                    </div>

                    <div class="form-group">
                        {{ Form::label('po_no', 'PO Number', array('class'=> 'col-lg-2 control-label')) }}
                        <div class="col-lg-4">
                            {{ Form::text('po_no', $grn->po_no, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                        </div>
                    </div>


                        <div class="form-group required">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products</label>
                            <div class="col-sm-10">

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"/></th>
                                            <th class="col-sm-1">#</th>
                                            <th class="hidden-xs hidden-sm col-sm-2">Description</th>
                                            <th class="hidden-xs hidden-sm col-sm-2">Label</th>
                                            <th class="cell-small col-sm-2">Quantity</th>
                                            <th class="cell-small col-sm-2">StockIn Qty</th>
                                            <th class="cell-small col-sm-1" title="Stock In History">StockIn & History</th>
                                            <th class="hidden-xs hidden-sm col-sm-1">Base unit</th>
                                            <th class="hidden-xs hidden-sm col-sm-1">Packing Factor</th>
                                            <th class="hidden-xs hidden-sm col-sm-1">UOM</th>
                                            <th class="cell-small col-sm-1">FOC Quantity</th>
                                            <th class="cell-small col-sm-1">FOC UOM</th>
                                            <th class="hidden-xs hidden-sm col-sm-1">REMARK</th>

                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                        <?php $tempcount = 1; ?>
                                        @foreach($grn_details as $trans_details)
                                        @if($trans_details->status=="2")
                                        <input type="hidden" value="{{$trans_details->id}}" class="bid" name="sin_id[]">
                                        @endif
                                        <tr class="odd gradeX">
                                             @if($trans_details->status=="2")
                                            <td><input type="checkbox" class="input-check" value="2"id="checkbox{{$tempcount}}" onclick="checkboxChecked(this.id);"/><input type="hidden" value="2" name="sin_status[]" id="scheckbox{{$tempcount}}"/></td>
                                            @else
                                            <td></td>
                                            @endif
                                            <td>{{$tempcount++}}</td>
                                            <td>{{$trans_details->product_name}}</td>
                                            <td>{{$trans_details->price_label}}</td>                     
                                             @if($trans_details->status=="2") 
                                            <td><input type="number" class="form-control text-center" name="qty[]" value="{{$trans_details->quantity}}"></td>
                                            @else
                                            <td class="text-center"> {{$trans_details->quantity}}</td>
                                            @endif
                                            @if($trans_details->status=="2") 
                                            <td><input type="number" class="form-control text-center stockin_qty" name="stockin_qty[]" value="0"></td>
                                            @else
                                            <td class="text-center"> {{$trans_details->stockin_qty}}</td>
                                            @endif
                                            <td class="text-center QD" data-value='{{$trans_details->stockin_qty}}'><button type="button" class="btn btn-success" data-toggle="modal" data-target="#recordview" onclick="viewrecord('{{$trans_details->sku}}','{{$grn->id}}')">{{$trans_details->stockin_qty}} & <i class="fa fa-history"></i></button></td>
                                            <td>{{$trans_details->base_unit}}</td>
                                            <td>{{$trans_details->packing_factor}}</td>
                                            <td>{{$trans_details->uom}}</td>
                                            @if($trans_details->status=="2") 
                                            <td><input type="text" class="form-control text-center" name="foc_qty[]" value="{{$trans_details->foc_qty}}"></td>
                                            @else
                                            <td>{{$trans_details->foc_qty}}</td>
                                            @endif
                                            @if($trans_details->status=="2") 
                                            <td><input type="text" class="form-control text-center" name="foc_uom[]" value="{{$trans_details->foc_uom}}"></td>
                                            @else
                                            <td>{{$trans_details->foc_uom}}</td>
                                            @endif
                                            @if($trans_details->status=="2") 
                                            <td><input type="text" class="form-control text-center" name="remark[]" value="{{$trans_details->remarks}}"></td>
                                            @else
                                            <td>{{$trans_details->remarks}}</td>
                                            @endif

                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                               
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            {{ Form::label('whloc_id', 'Warehouse Location ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_id', $grn->warehouse_loc_id, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_name', 'Warehouse Location Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_name', $grn->warehouse_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_address1', 'Address 1', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_address1', $grn->address_1, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_address2', 'Address 2', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_address2', $grn->address_2, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_contact', 'Contact No.', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_contact', $grn->pic_contact, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_pic', 'Person In Charge', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_pic', $grn->pic_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>


                        <hr>

                        <div class="form-group">
                            {{ Form::label('seller_id', 'Supplier/Seller ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('seller_id', $grn->seller_id, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(seller_name)) has-error @endif">
                            {{ Form::label('seller_name', 'Supplier/Seller Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('seller_name', $grn->company_name, array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                             <div class="form-group required {{ $errors->first('seller_do_no', 'has-error') }}">
                                <label class="col-lg-2 control-label">Supplier/Seller DO No.</label>
                                <div class="col-lg-4">
                                    {{ Form::text('seller_do_no', $grn->seller_do_no, ['class' => 'form-control']) }}
                                    {{ $errors->first('seller_do_no', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('seller_driver_name', 'has-error') }}">
                                <label class="col-lg-2 control-label">Delivery Person/Driver Name</label>
                                <div class="col-lg-4">
                                    {{ Form::text('seller_driver_name', $grn->seller_driver_name, ['class' => 'form-control']) }}
                                    {{ $errors->first('seller_driver_name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group {{ $errors->first('remarks', 'has-error') }}">
                                <label class="col-lg-2 control-label">Remarks / Note</label>
                                <div class="col-lg-4">
                                    {{ Form::textarea('remarks', $grn->remarks, ['class' => 'form-control']) }}
                                    {{ $errors->first('remarks', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('deliverby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Deliver By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('deliverby', $grn->deliver_by, ['class' => 'form-control']) }}
                                    {{ $errors->first('deliverby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('receivedby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Recieved By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('receivedby', $grn->received_by, ['class' => 'form-control']) }}
                                    {{ $errors->first('receivedby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                             <div class="form-group required {{ $errors->first('verifiedby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Verified By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('verifiedby', $grn->verified_by, ['class' => 'form-control']) }}
                                    {{ $errors->first('verifiedby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            <input type="hidden" id="checknumber" name="product_count" value="0">
                            <hr>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <input class="btn btn-default" data-toggle="tooltip" type="reset" value="Reset">
                                @if ( Permission::CheckAccessLevel(Session::get('role_id'), 2, 3, 'AND'))
                                    <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save</button>
                                @endif
                            </div>
                        </div>
<!-- Modal -->
<div class="modal fade" id="recordview" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="width: max-content;">
    <div class="modal-content">
      <div class="modal-header float-right">
        <h5>Stock In History</h5>
        <div class="text-right">
          <i data-dismiss="modal" aria-label="Close" class="fa fa-close"></i>
        </div>
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th scope="col">GRD ID</th>
                    <th scope="col">GRD NO</th>
                    <th scope="col">SKU</th>
                    <th scope="col">PRODUCT NAME</th>
                    <th scope="col">STOCK IN QTY</th>
                    <th scope="col">Remarks</th>
                    <th scope="col">UPDATED BY</th>
                    <th scope="col">UPDATED AT</th>
                </tr>
           </thead>
           <tbody id="tables">
               </tbody>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>

                            
                           <!--  <div class='form-group'>
                                <div class="col-lg-10">
                                    {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
                                    {{ Form::submit('Save', array('class' => 'btn btn-large btn-primary', 'id' => 'Save')) }}


                                </div>
                            </div> -->

                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

        

    


    {{ Form::close() }}
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
  <script type="text/javascript">
    function checkboxChecked(id) {

   if($("#"+id).is(':checked')){
    $("#s"+id).val("1");
    $("#"+id).val("1");
   }else{

    $("#s"+id).val("2");
    $("#"+id).val("2");
   }
   var numberNotChecked = $('.input-check:checkbox:checked').length;
   $("#checknumber").val(numberNotChecked);
}
$(".stockin_qty").on("change paste keyup", function() {
   var total_qty=$(this).parent().prev('td').find("input[type=number]").val();
   var stockin_qty=$(this).parent().next('td').data('value');
   var current_qty=$(this).val();
   var total=(+current_qty) + (+stockin_qty);
    var total_remaining=(+total_qty) -(+stockin_qty);
    var c= parseInt(total);
   var t= parseInt(total_qty);
   if(c>t){
       alert("Remaining quantity is "+total_remaining+" only !Please Check Stock In History");
       $(this).val(total_remaining)
       return false;
   }
   
});
function viewrecord(sku,id){
    $('#tables').html();
    $.ajax({
            method: "POST",
            url: "/grn/viewrecord",
            dataType:'json',
            data: {'id':id,'sku':sku},
            success: function(data) {
                 if(data!=""){
                      var result='';
                      $.each(data, function( key, value ) {
                   result+='<tr><td>'+value['grn_id']+'</td><td>'+value['grn_no']+'</td><td>'+value['sku']+'</td><td>'+value['product_name']+'</td><td>'+value['stock_in_qty']+'</td><td>'+value['remarks']+'</td><td>'+value['updated_user']+'</td><td>'+value['updated_date']+'</td></tr>';
                       });
                       $('#tables').html(result);
                     }else{
                       result+='<tr><td>No data Available</td></tr>'; 
                        $('#tables').html(result);
                     }
            }
    })
}
</script>
@stop

@section('script')

$(document).ready(function() {

    $(document).on("click", "#deleteItem", function(e) {
        e.preventDefault();
        $(this).closest("tr").remove();
        calculateTotal();
        if(!$('.product').length) {
            $('#emptyproduct').show();
            $('#grandTotal').remove();
        }
    });
$('#selectAll').click(function () {
   if($('#selectAll').is(':checked')){
    $(this).closest('table').find('td input:checkbox').prop('checked', this.checked).val("1");
    $(this).closest('table').find('td input:hidden').val("1");
    var numberNotChecked = $('.input-check:checkbox:checked').length;
   $("#checknumber").val(numberNotChecked);
}else{
  $(this).closest('table').find('td input:checkbox').prop('checked',false).val("2"); 
      $(this).closest('table').find('td input:hidden').val("2");
      var numberNotChecked = $('.input-check:checkbox:checked').length;
   $("#checknumber").val(numberNotChecked);
 
}
});
});

@stop