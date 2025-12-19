@extends('layouts.master')
@section('title', 'Goods Received Note GRN')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add New Admin GRN</h1>
              <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('stock') }}"><i class="fa fa-reply"></i></a>
                </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
                  
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Admin Goods Received Note</h3>
                </div>
                <div class="panel-body">

                      {{ Form::open(['url' => 'admingrn/store', 'method' => 'post']) }}
                 <div class="form-horizontal">
                        
                         

                    <div class="form-group {{ $errors->first('grn_date', 'has-error') }}">
                        <label  class="col-lg-2 control-label">GRN Date</label>
                        <div class="col-lg-2">
                            <div class='input-group date' id='datetimepicker1'>
                                <input type='text' class="form-control" name="grn_date" value="<?php echo (Input::get('grn_date')); ?>"/>
                                <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                            {{ $errors->first('grn_date', '<p class="help-block">:message</p>') }}
                        </div>
                    </div>
                    <div class="form-group required {{ $errors->first('po_no', 'has-error') }}">
                            {{ Form::label('po_no', 'PO Number ', array('class'=> 'col-lg-2 control-label')) }}
                        <input type="hidden" id="po_id" name="po_id" value="{{Input::old('po_id')}}">
                        <div class="col-lg-4">
                            <div class="input-group">
                            <input type="text" id="po_no" name="po_no" class="form-control" value="{{Input::old('po_no')}}" readonly>
                            <span class="input-group-btn">
                                <button class="btn btn-primary selectPOBtn" id="selectPOBtn"  type="button" href="/purchase-order/po-list"><i class="fa fa-plus"></i> Select PO Number</button>
                            </span>
                            </div><!-- /input-group -->
                            {{ $errors->first('po_no', '<p class="help-block">:message</p>') }}
                        </div><!-- /.col-lg-6 -->
                    </div>


                        <div class="form-group required">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products</label>
                            <div class="col-sm-10">

                               
                            <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>

                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th><input type="checkbox" id="selectAll"/></th>
                                            <th class="col-sm-3">Product Name &amp; SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-4">Label</th>
                                            <th class="cell-small col-sm-1 text-center">Quantity</th>
                                            <th class="cell-small col-sm-1 text-center">UOM</th>
                                            <th class="cell-small col-sm-1">FOC Quantity</th>
                                            <th class="cell-small col-sm-1">FOC UOM</th>
                                            <th class="cell-small col-sm-1">Remark</th>
                                            <th class="cell-small col-sm-1 text-center">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">

                                         <tr id="emptyproduct">
                                            <td colspan="8">No product added.</td>
                                        </tr>
                                       
                                    </tbody>
                                </table>
                               
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="selectWarehouseBtn" class="btn btn-primary selectWarehouseBtn" href="/purchase-order/warehouse-list"><i class="fa fa-plus"></i> Select Warehouse Location</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('whloc_id', 'Warehouse Location ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_id', Input::old('whloc_id'), array('class'=> 'form-control','id'=>'warehouse_id', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_name', 'Warehouse Location Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_name', Input::old('whloc_name'), array('class'=> 'form-control','id'=>'warehouse_name', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_address1', 'Address 1', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_address1', Input::old('whloc_address1'), array('class'=> 'form-control','id'=>'warehouse_add1', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_address2', 'Address 2', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_address2', Input::old('whloc_address2'), array('class'=> 'form-control','id'=>'warehouse_add2', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_contact', 'Contact No.', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_contact', Input::old('whloc_contact'), array('class'=> 'form-control','id'=>'warehouse_contact', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(trans_date)) has-error @endif">
                            {{ Form::label('whloc_pic', 'Person In Charge', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('whloc_pic', Input::old('whloc_pic'), array('class'=> 'form-control','id'=>'warehouse_pname', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>


                        <hr>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="selectSellerBtn" class="btn btn-primary selectSellerBtn" href="/purchase-order/seller-list"><i class="fa fa-plus"></i> Select Supplier/Seller Name</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {{ Form::label('seller_id', 'Supplier/Seller ID', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('seller_id', Input::old('seller_id'), array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                        <div class="form-group @if ($errors->has(seller_name)) has-error @endif">
                            {{ Form::label('seller_name', 'Supplier/Seller Name', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                                {{ Form::text('seller_name', Input::old('seller_name'), array('class'=> 'form-control', 'autofocus' => 'autofocus', 'readonly' => 'readonly')) }}
                            </div>
                        </div>

                             <div class="form-group required {{ $errors->first('seller_do_no', 'has-error') }}">
                                <label class="col-lg-2 control-label">Supplier/Seller DO No.</label>
                                <div class="col-lg-4">
                                    {{ Form::text('seller_do_no', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('seller_do_no', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('seller_driver_name', 'has-error') }}">
                                <label class="col-lg-2 control-label">Delivery Person/Driver Name</label>
                                <div class="col-lg-4">
                                    {{ Form::text('seller_driver_name', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('seller_driver_name', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group {{ $errors->first('remarks', 'has-error') }}">
                                <label class="col-lg-2 control-label">Remarks / Note</label>
                                <div class="col-lg-4">
                                    {{ Form::textarea('remarks', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('remarks', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('deliverby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Deliver By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('deliverby', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('deliverby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('receivedby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Recieved By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('receivedby', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('receivedby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                             <div class="form-group required {{ $errors->first('verifiedby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Verified By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('verifiedby', null, ['class' => 'form-control']) }}
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
</script>  
@stop

@section('script')
<?php if(isset($orderMappedInfo['order_number'])){ ?>

    var rowTotal = $('<tr id="grandTotal"><td class="hidden-xs hidden-sm col-xs-1 text-right grand_total"></td><td></td></tr>');
    parent.$('#ptb').append(rowTotal);
    calSubTotal();
    calculateTotal();
    
<?php  } ?>

$(document).ready(function() {
    
   $('#datetimepicker1').datetimepicker({
        format: 'YYYY-MM-DD'
    });

   $('#datetimepicker2').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $('#datetimepicker3').datetimepicker({
        format: 'YYYY-MM-DD'
    });

    $('#selectPOBtn').click(function() {
        localStorage.clear();
    });

    $('#selectPOBtn').colorbox({
        iframe:true, width:"70%", height:"90%",
        onClosed:function(){

            if (localStorage.data0 != undefined) {
            $.ajax({
                url:'/purchase-order/fetch-po-products/' + localStorage.data0,
                type:'get',
                processData: false,
                contentType: false,
                success:function(response) {
                    $('.product').remove();
                    for (i = 0; i < response.length; i++) {
                        $('#ptb').append('<tr id="'+i+'" class="product">\
                                <input type="hidden" id="product_name[]" name="product_name[]" value="'+response[i].product_name+'">\
                                <input type="hidden" id="price_label[]" name="price_label[]" value="'+response[i].price_label+'">\
                                <input type="hidden" id="uom[]" name="uom[]" value="'+response[i].uom+'">\
                                <input type="hidden" id="sku[]" name="sku[]" value="'+response[i].sku+'">\
                                <input type="hidden" id="price[]" name="price[]" value="'+response[i].price+'">\\n\
                                <input type="hidden" id="total[]" name="total[]" value="'+response[i].total+'">\\n\
                                <input type="hidden" id="base_unit[]" name="base_unit[]" value="'+response[i].base_unit+'">\\n\
                                <input type="hidden" id="packing_factor[]" name="packing_factor[]" value="'+response[i].packing_factor+'">\\n\
                                <input type="hidden" id="sst[]" name="sst[]" value="'+response[i].sst+'">\\n\
                                <td><input type="checkbox" class="input-check" value="2"id="checkbox'+i+'" onclick="checkboxChecked(this.id);"/><input type="hidden" value="2" name="sin_status[]" id="scheckbox'+i+'"/></td>\
                                <td> <b>'+response[i].product_name+'</b> <br><i class="fa fa-tag"></i> '+response[i].sku+'</td>\
                                <td class="hidden-xs hidden-sm">'+response[i].price_label+'</td>\
                                <td class="hidden-xs hidden-sm col-xs-2"><input type="number" class="form-control text-center" name="qty[]" value="'+Math.round(response[i].quantity)+'"></td>\
                                <td class="hidden-xs hidden-sm col-xs-1 text-center" name="uom[]">'+response[i].uom+'</td>\
                                <td class="hidden-xs hidden-sm col-xs-1"><input type="number" class="form-control text-center" id="" name="foc_qty[]"></td>\
                                <td class="hidden-xs hidden-sm col-xs-1 text-center"><input type="text" class="form-control text-center" name="foc_uom[]"></td>\
                                <td class="hidden-xs hidden-sm col-xs-1 text-center"><input type="text" class="form-control text-center" name="remark[]"></td>\
                                <td class="text-center col-xs-1">\
                                    <div class="btn-group">\
                                        <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>\
                                    </div>\
                                </td>\
                            </tr>');
                    }
                }
            });
             $.ajax({
                url:'/admingrn/ajaxfetchseller/' + localStorage.data0,
                type:'get',
                processData: false,
                contentType: false,
                success:function(response) {

                $('#seller_id').val(response.id);
                $('#seller_name').val(response.company_name);
                

            }
             });
             $.ajax({
                url:'/admingrn/ajaxfetchwarehouse/' + localStorage.data0,
                type:'get',
                processData: false,
                contentType: false,
                success:function(response) {
               console.log(localStorage.data0);
                $('#warehouse_id').val(response.id);
                $('#warehouse_name').val(response.name);
                 $('#warehouse_add1').val(response.address_1);
                  $('#warehouse_add2').val(response.address_2);
                   $('#warehouse_contact').val(response.pic_contact);
                    $('#warehouse_pname').val(response.pic_name);
                
            }
             });
            }
                
        }
    });

    $('#selectWarehouseBtn').click(function() {
        localStorage.clear();
    });

    $('#selectWarehouseBtn').colorbox({
        iframe:true, width:"70%", height:"90%",
        onClosed:function(){
            //$('#ptb').append('<tr id="emptyproduct"><td colspan="6">No product added.</td></tr>');
            enableBtn();
        }
    });

    $('#selectSellerBtn').click(function() {
        localStorage.clear();
    });

    $('#selectSellerBtn').colorbox({
        iframe:true, width:"70%", height:"90%",
        onClosed:function(){
            //$('#ptb').append('<tr id="emptyproduct"><td colspan="6">No product added.</td></tr>');
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
  
    $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#addProdBtn').colorbox({
        iframe:true, width:"80%", height:"70%",
        onClosed:function(){
            calSubTotal();
            calculateTotal();
        }
    });
        jQuery('#cboxOverlay').remove();
     


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
            var price = parseFloat(prodprice.replace(/,/g,''));
            var promoPrice = parseFloat(promoprice.replace(/,/g,''));

            if (promoPrice == 0) {
                subTotal = currencyFormat(qty * price);
            } else {
                subTotal = currencyFormat(qty * promoPrice);
            }
            
            var subTotalHtml = '<div id="amt-value-main" amt-value-1="'+subTotal+'" > MYR '+subTotal+ '</div>';
            
            $(cur + ' .subtotal').html(subTotalHtml);
        });
    }

    function calculateTotal() {
        var grandTotal1 = 0;
        var grandTotal2 = 0;
        var grandQty = 0;

        $('tr.product td#subtotal ').each(function(){
            subtotal1 = $(this).find( "#amt-value-main" ).attr("amt-value-1");
            subtotal2 = $(this).find( "#amt-value-option" ).attr("amt-value-2");
            
            grandTotal1 += parseFloat(subtotal1.replace(/,/g,''));;
            if(selected_type == 2 ){
                grandTotal2 += parseFloat(subtotal2.replace(/,/g,''));;
            }
            
        });

        $('tr.product td.qty').each(function(){
            qty = $(this).text();
            grandQty += parseFloat(qty.replace(/,/g,''));
        });
        
        var selected_type = $("#selected_type").val();
        if(selected_type == 2 ){
            $('.grand_total').html('USD ' +currencyFormat(grandTotal1)+'<br> <div style="font-size:10px;">MYR '+ currencyFormat(grandTotal2))+'</div>';
        }else{
            $('.grand_total').html('MYR ' +currencyFormat(grandTotal1));
        }
        
        $('.grand_qty').html(grandQty);

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