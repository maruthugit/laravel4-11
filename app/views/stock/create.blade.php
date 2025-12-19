@extends('layouts.master')
@section('title', 'Stock')
@section('content')

<!-- <script src="https://code.jquery.com/jquery-2.1.1.min.js"
    type="text/javascript"></script>
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script> -->

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Add Stock Transfer</h1>
              <span class="pull-right">
                    <a class="btn btn-default" href="{{ url('stock') }}"><i class="fa fa-reply"></i></a>
                </span>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
                  
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i>Stock Transfer Inputs</h3>
                </div>
                <div class="panel-body">

                      {{ Form::open(['url' => 'stock', 'method' => 'post']) }}
                 <div class="form-horizontal">
                        
                          <div class="form-group required {{ $errors->first('st_no', 'has-error') }}">
                                <label class="col-lg-2 control-label">ST No</label>
                                <div class="col-lg-2">
                                    {{ Form::text('st_no', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('st_no', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group {{ $errors->first('st_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">ST Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker1'>
                                      
                                        <input type='text' class="form-control" name="st_date" value="<?php echo (Input::get('st_date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                 </div>
                                </div>
                            </div>

                             <div class="form-group {{ $errors->first('transfer_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">Transfer Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker2'>
                                      
                                        <input type='text' class="form-control" name="transfer_date" value="<?php echo (Input::get('transfer_date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                 </div>
                                </div>
                            </div>

                       

                         

                      

                        <div class="form-group @if ($errors->has('lid')) has-error @endif">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products * </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/stock/wareproducts"><i class="fa fa-plus"></i> Add Product</span>
                                    </div>
                                </div>
                                <br />
                            <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>

                              <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                           
                                            <th class="col-sm-2">Product Name </th>
                                       
                                       
                                         
                                            <th class="cell-small col-sm-1">Quantity</th>
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


<hr>

                          <div class="form-group {{ $errors->first('expired_date', 'has-error') }}">
                                     <label  class="col-lg-2 control-label">Expiry Date</label>
                                     <div class="col-lg-2">
                                    <div class='input-group date' id='datetimepicker3'>
                                      
                                        <input type='text' class="form-control" name="expired_date" value="<?php echo (Input::get('expired_date')); ?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                 </div>
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('deliver_from', 'has-error') }}">
                                <label class="col-lg-2 control-label">Delivery From</label>
                                <div class="col-lg-4">
                                    {{ Form::text('deliver_from', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('deliver_from', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('deliver_to', 'has-error') }}">
                                <label class="col-lg-2 control-label">Delivery To</label>
                                <div class="col-lg-4">
                                    {{ Form::text('deliver_to', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('deliver_to', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                            <div class="form-group {{ $errors->first('purposeoftransfer', 'has-error') }}">
                                <label class="col-lg-2 control-label">Purpose of Transfer</label>
                                <div class="col-lg-4">
                                    {{ Form::textarea('purposeoftransfer', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('purposeoftransfer', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group required {{ $errors->first('sendby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Send By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('sendby', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('sendby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>

                             <div class="form-group">
                                <label class="col-lg-2 control-label">Recieved By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('receivedby', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('receivedby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                             <div class="form-group required {{ $errors->first('approvedby', 'has-error') }}">
                                <label class="col-lg-2 control-label">Approved By</label>
                                <div class="col-lg-4">
                                    {{ Form::text('approvedby', null, ['class' => 'form-control']) }}
                                    {{ $errors->first('approvedby', '<p class="help-block">:message</p>') }}
                                </div>
                            </div>
                            

                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

        

    <div class='form-group'>
        <div class="col-lg-10">
            {{ Form::reset('Reset', array('class'=>'btn btn-default', 'data-toggle'=>'tooltip')) }}
            {{ Form::submit('Save', array('class' => 'btn btn-large btn-primary', 'id' => 'Save')) }}


        </div>
    </div>


    {{ Form::close() }}
</div>
    
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
    
});
    
    
    $('#datepicker').datepicker({ dateFormat: "yy-mm-dd" }).val();
  
    $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#addProdBtn').colorbox({
        iframe:true, width:"40%", height:"60%",
        onClosed:function(){
            calSubTotal();
            calculateTotal();
        }
    });
        jQuery('#cboxOverlay').remove();
     


    function currencyFormat(num) {
        return num.toFixed(2).replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,")
    }

   

    function calculateTotal() {
        var grandTotal = 0;
        var grandQty = 0;

        $('tr.product td.subtotal').each(function(){
            subtotal = $(this).text();
            grandTotal += parseFloat(subtotal.replace(/,/g,''));
        });

        $('tr.product td.qty').each(function(){
            qty = $(this).text();
            grandQty += parseFloat(qty.replace(/,/g,''));;
        });
        
        $('.grand_total').html(currencyFormat(grandTotal));
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