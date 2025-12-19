@extends('layouts.master')
@section('title', 'Transaction')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h4 class="page-header"><i class="fa fa-file-o"></i> Add New Transaction</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            {{ Form::open(array('url'=>'/checkout', 'class' => 'form-horizontal')) }}
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><i class="fa fa-pencil"></i> Transaction Details</h3>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <input type="hidden" id="devicetype" name="devicetype" value="manual">
                        <?php //echo "<pre>"; print_r($orderMappedInfo); echo "</pre>"; ?>
                        <?php if(isset($orderMappedInfo['order_number'])){ ?>
                        <input name="transfer_order_id" type="hidden" value='<?php echo $orderMappedInfo['id']; ?>' >
                        <input name="transfer_order_id_type" type="hidden" value='<?php echo $orderMappedInfo['transfer_type']; ?>' >
                         <div class='form-group'>
                            <div class="alert alert-info">
                                <p><strong>Order Information</strong>
                                <p>Transfer From Order Number : <?php echo $orderMappedInfo['order_number']; ?>
                                <p>Customer Name : <?php echo $orderMappedInfo['customer_order']; ?>
                            </div>
                        </div>
                        <?php } ?>
                         <div class='form-group'>
                            {{ Form::label('transaction_date', 'Transaction Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_from">
                                        <input id="transaction_from" class="form-control" tabindex="1" name="transaction_date" type="text">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                            <!--{{ Form::text('transaction_date', $orderMappedInfo['transaction_date'], ['placeholder' => '', 'class' => 'form-control', 'id' => 'datepicker']) }}-->
                            </div>
                        </div>
                        <?php if(Session::get('username') == 'quenny' || Session::get('username') == 'sclim' || Session::get('username') == 'maruthu' || Session::get('username') == 'winnie'){ ?>
                        <div class='form-group'>
                            
                            <!-- Only For Queeny -->
                            {{ Form::label('transaction_date', 'Invoice Date', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <div class="input-group" id="datetimepicker_from">
                                        <input id="transaction_from" class="form-control" tabindex="1" name="selected_invoice_date" type="text">
                                        <span class="input-group-btn">
                                            <button type="button" class="btn btn-default"><span class="glyphicon glyphicon-calendar"></span></button>
                                        </span>
                                    </div>
                            </div>
                            <!-- Only For Queeny -->
                            
                        </div>
                        <?php } ?>
                        <div class='form-group'>
                            <label class="col-lg-2 control-label" for="">Billing Type</label>
                            <div class="col-md-3">
                                <div class="btn-group btn-group-sm btn-group-justified " role="group" aria-label="...">
                                    <div class="btn-group " role="group">
                                        <button type="button" class="btn btn-default active" id="is_local_order"><i class="fa fa-check"></i> Local Order</button>
                                    </div>
                                    <div class="btn-group" role="group">
                                      <button type="button" class="btn btn-default" id="is_foreign_order">Foreign Order</button>
                                    </div>
                                  </div>
                                <input id="selected_type" type="hidden" value="1">

                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('user')) has-error @endif">
                            {{ Form::label('user', 'Bill To *', array('class'=> 'col-lg-2 control-label')) }}
                            <input type="hidden" id="user_id" name="user_id">
                            <div class="col-lg-3">
                                <div class="input-group">
                                <input type="text" id="user" name="user" class="form-control" placeholder="" readonly>
                                <span class="input-group-btn">
                                    <button class="btn btn-primary selectUserBtn" id="selectUserBtn"  type="button" href="/transaction/ajaxcustomer"><i class="fa fa-plus"></i> Select Buyer</button>
                                </span>
                                </div><!-- /input-group -->
                            </div><!-- /.col-lg-6 -->
                        </div>
                        <div class="form-group @if ($errors->has('user')) has-error @endif">
                            {{ Form::label('user', 'Self Collect', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4" style="padding:10px;">
                                <input type="checkbox" name="is_self_collect" value="1"> Self Collect <i class="fa fa-question-circle" data-container="body" data-toggle="popover" data-placement="top" data-content="By choose this option this transaction will automatically deduct stock from inventory once transaction generate invoice."></i>
                            </div>
                        </div>
                        <hr>
                        <div class='form-group'>
                            {{ Form::label('invoice_to_address', '', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4" style="padding:10px;">
                                <input type="radio" id="invoice_to_address_delivery" name ="invoice_to_address" class="" value="1" checked> Invoice to delivery address<br>
                                <input type="radio" id="invoice_to_address_buyer" name ="invoice_to_address" class="" value="2" > Invoice to buyer address
                                <p>
                                <div id="invoice-address-box" style="display:none;width:100%;min-height:130px;border:solid 1px #ddd;    background-color: #f9f9f9;padding:10px;">
                                        Celcom Planet Sdn Bhd (11street)<br>
                                        Unit 9-1 Level 9, Tower 3, Avenue 3,Bangsar South No. 8, ,<br>
                                        Jalan Kerinchi, 59200 Kuala Lumpur,<br>                59200 KUALA LUMPUR,<br>
                                        Kuala Lumpur, Malaysia.<br>
                                </div>
                            </div>
                        </div>
                        <div class='form-group'>
                            {{ Form::label('external_ref_number', 'External Platform Order Number', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <input type="text" id="external_ref_number" name ="external_ref_number" class="form-control">
                                <small id="emailHelp" class="form-text text-muted">External platform order number. Put only order number</small>
                            </div>
                        </div>
                        <div class="form-group @if ($errors->has('lid')) has-error @endif">
                            <?php $count = 1; ?>
                            <label class="col-lg-2 control-label" for="price_option">Products * </label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <div class="input-group-btn">
                                        <span class="pull-left"><button id="addProdBtn" name="addProdBtn" class="btn btn-primary addProdBtn" data-toggle="tooltip" href="/transaction/ajaxproduct/1"><i class="fa fa-plus"></i> Add Product</span>
                                    </div>
                                </div>
                                <br />
                            <div class="clearfix">{{ $errors->first('lid', '<p class="help-block">:message</p>') }}</div>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th class="col-sm-2">Product Name &amp; SKU</th>
                                            <th class="hidden-xs hidden-sm col-sm-3">Label</th>
                                            <th class="hidden-xs hidden-sm col-sm-1">Actual Price</th>
                                            <th class="hidden-xs hidden-sm col-sm-1">Promotion Price</th>
                                            <th class="cell-small col-sm-1">Quantity</th>
                                            <th class="cell-small col-sm-1">Sub-total</th>
                                            <th class="cell-small text-center col-sm-1">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody id="ptb">
                                        <?php if(isset($orderMappedInfo['order_number'])){ 
                                            foreach ($orderMappedInfo['product'] as $key => $value) {
                                             
                                                
                                               $price = $value['promotion_price'] != '' ? $value['promotion_price'] : $value['actual_price'];
                                               
                                               $subtotal = $price * $value['quantity'];
                                            ?>
                                        
                                        <tr id="<?php echo $value['product_id']; ?>" class="product">
                                            <input type="hidden" id="priceopt[]" name="priceopt[]" value="<?php echo $value['option_price_id']; ?>">
                                            <input type="hidden" id="qrcode[]" name="qrcode[]" value="<?php echo $value['qrcode']; ?>">
                                            <input type="hidden" id="qty[]" name="qty[]" value="<?php echo $value['quantity']; ?>">
                                            <input type="hidden" id="price_local" name="price_local[]" value="<?php echo number_format($value['actual_price'], 2);  ?>">
                                            <input type="hidden" id="price_promo_local" name="price_promo_local[]" value="<?php echo number_format($value['promotion_price'], 2);  ?>">
                                            <input type="hidden" id="price_foreign" name="price_promo_foreign[]" value="<?php echo number_format(0, 2);  ?>">
                                            <input type="hidden" id="price_promo_foreign" name="price_local[]" value="<?php echo number_format(0, 2);  ?>">
                                            <td> <b> <?php echo $value['product_name']; ?></b> <br><i class="fa fa-tag"></i> <?php echo $value['sku']; ?> <br></td>
                                            <td class="hidden-xs hidden-sm"><?php echo $value['label']; ?></td>
                                            <td class="hidden-xs hidden-sm col-xs-1 text-right p_price">MYR <?php echo number_format($value['actual_price'], 2);  ?></td>
                                            <td class="hidden-xs hidden-sm col-xs-1 text-right promo_price">MYR <?php echo number_format($value['promotion_price'], 2); ?></td>
                                            <td class="hidden-xs hidden-sm col-xs-1 text-right qty"><?php echo $value['quantity']; ?></td>
                                            <td id="subtotal" class="hidden-xs hidden-sm col-xs-1 text-right subtotal">
                                                <div id="amt-value-main" amt-value-1="<?php echo $subtotal; ?>"><?php echo $subtotal; ?></div>
                                                </td>
                                            <td class="text-center col-xs-1">
                                                <div class="btn-group">
                                                    <a class="btn btn-xs btn-danger" id="deleteItem" data-toggle="tooltip" href="javascript:void(0)" data-original-title="Delete"><i class="fa fa-times"></i> Remove</a>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php }} ?>
                                        <?php if(!isset($orderMappedInfo['order_number'])){ ?>
                                        <tr id="emptyproduct">
                                            <td colspan="6">No product added.</td>
                                        </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        
                    </div>
                </div>
                <!-- /.panel-body -->
            </div>
            <!-- /.panel -->

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Delivery Address</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <?php if($orderMappedInfo['delivery_address_1']['status']=='0') {?>
                        <div class="alert alert-warning">
                             <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Warning!</strong> API Address does not match in the location directory.
                       </div>  
                        <?php } ?> 
                        <div class='form-group'>
                            {{ Form::label('deliveradd1', 'Street Address *', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            <?php if($orderMappedInfo['delivery_address']['street_address_1']=='') {?>
                            {{ Form::text('deliveradd1', $orderMappedInfo['delivery_address_1']['street_address_1_1'], ['placeholder' => 'Street Address', 'class' => 'form-control']) }}
                            <?php }else{?>     
                           {{ Form::text('deliveradd1', $orderMappedInfo['delivery_address']['street_address_1'], ['placeholder' => 'Street Address', 'class' => 'form-control']) }}
                            <?php } ?>
                            {{ $errors->first('deliveradd1', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class='form-group'>
                            {{ Form::label('deliveradd2', 'Street Address 2', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            <?php if($orderMappedInfo['delivery_address']['street_address_1']=='') {?>
                            {{ Form::text('deliveradd2', $orderMappedInfo['delivery_address_1']['street_address_2_1'], ['placeholder' => 'Street Address 2', 'class' => 'form-control']) }}
                            <?php }else{?>     
                            {{ Form::text('deliveradd2', $orderMappedInfo['delivery_address']['street_address_2'], ['placeholder' => 'Street Address 2', 'class' => 'form-control']) }}
                            <?php } ?>
                            {{ $errors->first('deliveradd2', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class='form-group'>
                            {{ Form::label('deliverpostcode', 'Postcode *', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                            <?php if($orderMappedInfo['delivery_address']['postcode']=='') {?>
                            {{ Form::text('deliverpostcode', $orderMappedInfo['delivery_address_1']['postcode_1'], ['placeholder' => 'Postcode', 'class' => 'form-control']) }}
                            <?php }else{?>    
                            {{ Form::text('deliverpostcode', $orderMappedInfo['delivery_address']['postcode'], ['placeholder' => 'Postcode', 'class' => 'form-control']) }}
                            <?php } ?>
                            {{ $errors->first('deliverpostcode', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class='form-group'>
                            {{ Form::label('delivercountry', 'Country *', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                                <select class='form-control' name='delivercountry' id='delivercountry'>
                                    <option value=""> - </option>
                                    @foreach ($countries as $country)
                                        <option value='{{ $country->id }}' <?php if ($country->id == $orderMappedInfo['delivery_address']['country']) echo 'selected="selected"'?>>{{ $country->name }}</option>
                                    @endforeach
                                </select>
                                {{ $errors->first('country', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class='form-group'>
                            {{ Form::label('state', 'State *', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-sm-3">
                                <select class='form-control' name='state' id='deliverstate'>
                                    <option value=""> - </option>
                                    <?php if (count($orderMappedInfo)>0){  ?>
                                    @foreach ($orderMappedInfo['statelist'] as $state)
                                     <option value='{{ $state->id }}' <?php if ($state->id == $orderMappedInfo['delivery_address_1']['state']) echo 'selected="selected"'?>>{{ $state->name }}</option>
                                    @endforeach
                                    <?php } ?>
                                </select>
                                {{ $errors->first('state', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <div class='form-group'>
                            {{ Form::label('city', 'City/Town *', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-sm-3">
                                <select class='form-control' name='city' id='city'>
                                    <option value=""> - </option>
                                    <?php if (count($orderMappedInfo)>0){  ?>
                                    @foreach ($orderMappedInfo['citylist'] as $city)
                                     <option value='{{ $city->id }}' <?php if ($city->id == $orderMappedInfo['delivery_address_1']['city']) echo 'selected="selected"'?>>{{ $city->name }}</option>
                                    @endforeach
                                    <?php } ?>
                                </select>
                            </div>
                        </div>
                        <?php if(in_array(Session::get('username'), Config::get('constants.ACCOUNT_ADMIN'))) { ?>
                        <div class='form-group'>
                            {{ Form::label('delivery_charges', 'Delivery Charges (RM)', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-3">
                            {{ Form::text('delivery_charges', $orderMappedInfo['shipping_fee'], ['placeholder' => '(RM) Before GST ', 'class' => 'form-control']) }}
                                <span class="text-danger">* Delivery charges is only applicable for offline sales!</span>
                            </div>
                        </div>
                        <?php } ?>
                        </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h2 class="panel-title"><i class="fa fa-truck"></i> Receiver Information</h2>
                </div>
                <div class="panel-body">
                    <div class="col-lg-12">
                        <div class='form-group'>
                            {{ Form::label('delivername', 'Recipient Name *', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('delivername', $orderMappedInfo['receiver_name'], ['placeholder' => 'Recipient Name', 'class' => 'form-control']) }}
                            {{ $errors->first('delivername', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>

                        <!--<div class='form-group'>-->
                        <!--    {{ Form::label('delivercontactno', 'Mobile No *', array('class' => 'col-lg-2 control-label')) }}-->
                        <!--    <div class="col-lg-4">-->
                        <!--    {{ Form::text('delivercontactno', $orderMappedInfo['mobile_no'], ['placeholder' => 'Mobile No', 'class' => 'form-control']) }}-->
                        <!--    {{ $errors->first('delivercontactno', '<p class="help-block">:message</p>') }}-->
                        <!--    </div>-->
                        <!--</div>-->
                         <div class="form-group required {{ $errors->first('mobile_no', 'has-error') }}">
                            {{ Form::label('delivercontactno', 'Mobile No ', array('class' => 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::text('delivercontactno', $orderMappedInfo['mobile_no'], ['placeholder' => 'Mobile No', 'class' => 'form-control', 'maxlength'=>"26"]) }}
                            {{ $errors->first('delivercontactno', '<p class="help-block" style="color:red;" >:message</p>') }}
                            </div>
                        </div>

                         <div class='form-group'>
                            {{ Form::label('specialmsg', 'Special Message', array('class'=> 'col-lg-2 control-label')) }}
                            <div class="col-lg-4">
                            {{ Form::textarea('specialmsg', $orderMappedInfo['special_message']!= "" ? $orderMappedInfo['special_message'] : Input::old('special_msg'), array('class'=> 'form-control', 'autofocus' => 'autofocus')) }}
                            {{ $errors->first('specialmsg', '<p class="help-block">:message</p>') }}
                            </div>
                        </div>                       
                    </div>
                </div>
            </div>
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
<?php if(isset($orderMappedInfo['order_number'])){ ?>

    var rowTotal = $('<tr id="grandTotal"><td class="text-right" colspan="4"><b>Total:</b></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_qty"></td><td class="hidden-xs hidden-sm col-xs-1 text-right grand_total"></td><td></td></tr>');
    parent.$('#ptb').append(rowTotal);
    calSubTotal();
    calculateTotal();
    
<?php  } ?>

    $('#datetimepicker_from, #datetimepicker_to').datetimepicker({
        format: 'YYYY-MM-DD'
    });
    
    $(function () {
        $('[data-toggle="popover"]').popover({ trigger: "hover" });
    })

    $(document).on("click", "#is_local_order", function(e) {
    
        $("#is_foreign_order").removeClass( "active" );
        $("#is_foreign_order").html('Foreign Order');
        $("#is_local_order").addClass( "active" );
        $("#is_local_order").html( '<i class="fa fa-check"></i> Local Order');
        
        $("#addProdBtn").attr('href','/transaction/ajaxproduct/1');
        $("#selected_type").val(1);
        
    });
    
    $(document).on("click", "#is_foreign_order", function(e) {
    
        $("#is_local_order").removeClass( "active" );
        $("#is_local_order").html('Local Order');
        $("#is_foreign_order").addClass( "active" );
        $("#is_foreign_order").html( '<i class="fa fa-check"></i> Foreign Order');
        $("#addProdBtn").attr('href','/transaction/ajaxproduct/2');
        $("#selected_type").val(2);
        
    });
    
    $(document).on("click", "#invoice_to_address_buyer", function(e) {

        if($("#user").val() === ''){
            $('#invoice_to_address_delivery').prop('checked', true);
            alert('Please select buyer first');
        }else{

            $.ajax({
                type: 'GET',
                url: '/transaction/getuser/'+$("#user").val(),
                data: {},
                dataType: "json",
                success: function(resultData) { 
                    $("#invoice-address-box").show();
                    var address =   resultData.full_name+'<br>'
                                    +resultData.address1+'<br>'
                                    +resultData.address2+'<br>'    
                                    +resultData.postcode+' '+resultData.city+'<br>'  
                                    +resultData.state+', '+resultData.country+'<br>';
                    $("#invoice-address-box").html(address);

                }
            });
        }
    });

    $(document).on("click", "#invoice_to_address_delivery", function(e) {
        $("#invoice-address-box").hide();
    });
    
    
    $('#datepicker').datepicker({ dateFormat: "yy-mm-dd" }).val();

    $('#selectUserBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed: function() {
            localStorage.clear();
        }
    });

    $('#addProdBtn').colorbox({
        iframe:true, width:"90%", height:"90%",
        onClosed:function(){
            calSubTotal();
            calculateTotal();
        }
    });

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
//           prodprice   = $(cur + ' td.p_price').text();
//            promoprice  = $(cur + ' td.promo_price').text();
            
            
            prodprice   = $(cur + ' #price_local').val();
            promoprice  = $(cur + ' #price_promo_local').val();
            
            prodForeignPrice   = $(cur + ' #price_foreign').val();
            promoForeignPrice  = $(cur + ' #price_promo_foreign').val();
            
            
            qty         = $(cur + ' td.qty').text();
            var price   = prodprice;
            var priceForeign   = prodForeignPrice;
            
            if(parseFloat(promoprice.replace(/,/g,''))  > 0) {
                //alert('Promo Price: '+promoprice);
                price = promoprice;
                priceForeign   = promoForeignPrice;
            }
            var selected_type = $("#selected_type").val();
            if(selected_type == 2 ){
                subTotal  = '<div id="amt-value-main" amt-value-1="'+currencyFormat((qty * parseFloat(priceForeign.replace(/,/g,''))))+'" > USD '+currencyFormat((qty * parseFloat(priceForeign.replace(/,/g,'')))) + '</div><div id="amt-value-option" amt-value-2="'+currencyFormat(qty * parseFloat(price.replace(/,/g,'')))+'" style="font-size:10px;"> MYR '+ currencyFormat(qty * parseFloat(price.replace(/,/g,'')))+'</div>';
            }else{
                subTotal  = '<div id="amt-value-main" amt-value-1="'+currencyFormat((qty * parseFloat(price.replace(/,/g,''))))+'" > MYR '+currencyFormat((qty * parseFloat(price.replace(/,/g,'')))) + '</div>';
            }
            
            //subTotal  = qty * parseFloat(price.replace(/,/g,''));

            //alert('Product price: '+prodprice+'\nQty: '+qty+'\nSub-Total: '+subTotal);
            $(cur + ' .subtotal').html(subTotal);
            //$(cur + ' .subtotal').html(currencyFormat(subTotal));

        });
    }

    function calSubTotalOLD() {
        var subTotal    = 0;
        var subPromo    = 0;
        var qty         = 0;
        var prodprice   = 0;
        var promoprice  = 0;

        $('tr.product').each(function(){
            var cur     = '#'+this.id;
            prodprice   = $(cur + ' td.p_price').text();
            promoprice  = $(cur + ' td.promo_price').text();
            qty         = $(cur + ' td.qty').text();
            var price   = prodprice;
           
            if(promoprice > 0) {
                //alert('Promo Price: '+promoprice);
                price = promoprice;
            }
            
            

            subTotal  = qty * parseFloat(price.replace(/,/g,''));

            //alert('Product price: '+prodprice+'\nQty: '+qty+'\nSub-Total: '+subTotal);
            $(cur + ' .subtotal').html(currencyFormat(subTotal));

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

    function calculateTotalOLD() {
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