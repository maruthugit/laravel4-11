<?php


$disablepoint = false;
/*
// POP BOX //
/* GET STATES */
    //$api_token = '76ae16139e8b3f0dc4b3d6409f3b2b3967b450ce'; // UAT
  
    // $api_token = '2adc529a94a58a5d164aa9c079149d3f26bf1f15'; // LIVE
    // $data = array(
    //     "token" => $api_token, 
    //     "country" => "Malaysia"
    //     );
    
    // $data_string = json_encode($data);
    // //$ch = curl_init('http://api-dev.popbox.asia//locker/location'); // UAT
    // $ch = curl_init('https://partnerapi.popbox.asia/locker/location'); // API
    // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    // curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
    // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    //     'Content-Type: application/json',
    //     'Content-Length: ' . strlen($data_string))
    // );
    // curl_setopt($ch, CURLOPT_TIMEOUT, 300);
    // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

    // //execute post

    // $resultPopBox = curl_exec($ch);
    // $locker = json_decode($resultPopBox);



// POP BOX //
    
$disable_popbox = true;    
$coupon_code        = '';
$coupon_code_amount = 0;

if(isset($trans_coupon)) {
    $trans_coupon_row   = $trans_coupon;
    $coupon_code        = $trans_coupon_row->coupon_code;
    $coupon_code_amount = $trans_coupon_row->coupon_amount;
}

$currency  = Fees::get_currency();
$trans_row = $trans_query;

switch ($trans_row->lang) {
    case 'CN':
        $label_tran = "交易号";
        $label_coupon = "优惠代码";
        $label_payment = "付款方式";
        $label_add = "收件地址";
        $label_contact = "收件人电话号码";
        $label_msg = "特别讯息";
        $label_process = "处理费";
        $label_subtotal = "小计";
        $label_delivery = "运输费";
        $label_gst = "消费税";
        $label_total = "总额";
        $label_tnc1 = "本人同意所有的";
        $label_tnc2 = "服务条款";
        $label_tnc3 = "关闭";
        $label_tnc4 = "请同意服务条款才能继续";
        $lavel_diff_delivery = '您的商品将会在不同时段送出.确定继续?';
        $lavel_regroup = '您可依据商品的送件时间分次购买,以更快获取您的商品.';
        break;
    case 'MY':
        $label_tran = "ID Transaksi";
        $label_coupon = "Kod Kupon";
        $label_payment = "Pilihan Pembayaran";
        $label_add = "Alamat Penghantaran";
        $label_contact = "Penerima";
        $label_msg = "Mesej Khas";
        $label_process = "Caj Pemprosesan";
        $label_subtotal = "Jumlah";
        $label_delivery = "Kos Penghantaran";
        $label_gst = "GST";
        $label_total = "Jumlah Keseluruhan";
        $label_tnc1 = "Saya setuju dengan";
        $label_tnc2 = "Terma & Syarat";
        $label_tnc3 = "Tutup";
        $label_tnc4 = "Sila akui Terma & Syarat untuk menerus.";
        $lavel_diff_delivery = 'Barangan anda akan dihantar pada masa yang beza. Teruskan?';
        $lavel_regroup = 'Anda boleh beli dengan mengikut masa penghantaran untuk mempercepatkan penghantaran.';
        break;
    case 'EN':
    default:
        $label_tran = "Transaction ID";
        $label_coupon = "Coupon Code";
        $label_payment = "Payment Option";
        $label_add = "Delivery Address";
        $label_contact = "Recipient Contact";
        $label_msg = "Special Message";
        $label_process = "Processing Fees";
        $label_subtotal = "Subtotal";
        $label_delivery = "Postage and Packaging";
        $label_gst = "GST";
        $label_total = "Total";
        $label_tnc1 = "I hereby agree with the";
        $label_tnc2 = "Terms & Conditions";
        $label_tnc3 = "Close";
        $label_tnc4 = "To proceed, do accept our Terms & Conditions.";
        $lavel_diff_delivery = 'Your items will be delivered on different timing. Are you OK to proceed?';
        $lavel_regroup = 'You may need group your items based on delivery time for fast delivery.';
        break;
}

// NEW CHANGES //
$totalIncl = 0; 
$delivery_charges = $trans_row->delivery_charges;
$gst_status = Fees::get_gst_status();

if ($gst_status == '1') {
    if($trans_row->delivery_country == 'China'){
        $gst_delivery = 0;	
    }else{
        $temp_gst_rate     = Fees::get_gst();		
        $gst_delivery = round(($delivery_charges * $temp_gst_rate / 100), 2);	
    }
    	
}else{		
    $gst_delivery = 0;		
}		
		
$delivery_charges = $delivery_charges + $gst_delivery;		
		
   		
    		
// NEW CHANGES //

$total_fees = $trans_row->delivery_charges + $trans_row->process_fees;
$mpaysku    = '';
$items      = array();
$key        = 0;
$group_product_price = array();
$group_product_gst   = array();

$popboxSelection = false;
$popboxSelectionEnd = false;



foreach($trans_detail_query as $row) {
    
    if(($row->is_popbox_available == 1) && (!$popboxSelectionEnd)){
        $popboxSelection  = true;
    }else{
        $popboxSelection  = false;
        $popboxSelectionEnd = true;
    }
    
    $total_fees += ($row->price * $row->unit);

    if ($row->product_group != '') {
        if ( ! isset($group_product_price[$row->product_group])) {
            $group_product_price[$row->product_group] = 0;
        }

        $group_product_price[$row->product_group] += ($row->price * $row->unit);

        if ( ! isset($group_product_gst[$row->product_group])) {
            $group_product_gst[$row->product_group] = 0;
        }

        $group_product_gst[$row->product_group] += ($row->gst_amount);

        continue;
    }

    if ((strlen($mpaysku) + strlen($row->product_name) + 1) < 1000) {
        $mpaysku = $mpaysku . $row->product_name . "|";
    }
}

$grand_amt = bcsub($total_fees, ($coupon_code != '' ? $coupon_code_amount : 0), 2);
$grand_amt = bcadd($grand_amt, ($gst_delivery != '' ? $gst_delivery : 0), 2);
$grand_amt = bcsub($grand_amt, ($total_trans_points ? $total_trans_points : 0), 2);

$grand_amt = $grand_amt;

if($grand_amt < 0){
    $grand_amt = 0.00;
}



?>
<!DOCTYPE html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
        <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ url('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/checkout.css?v=2.3.0') }}" rel="stylesheet">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <style>
        .modal-dialog {
            height: calc(100% - 20px);
        }

        .modal-content {
            border-radius: 0;
            box-shadow: none;
            top: calc(50% - 45px - (139px / 2));
        }

        .modal-footer {
            border: 0;
            padding-top: 0;
            text-align: center;
        }

        .modal-footer button {
            margin: 0 10px;
            width: 60px;
        }

        input[name="payopt"] {
            margin-top: 20px;
            vertical-align: top;
        }

        .label-payment-img small {
            margin-left: 10px;
        }
        </style>
    </head>
    <body>
        <div id="loading" style="display: none;">
            <div id="loading-animation">
                <img src="{{ url('img/checkout/lightbox-ico-loading.gif') }}">
            </div>
        </div>
        <div class="container-fluid">
            @if (Session::has('pointMessage'))
                <?php
                if (strpos(Session::get('pointMessage'), 'applied. You may proceed to checkout.') !== false) {
                    $pointSuccess = true;
                }
                ?>
                <div class="alert @if ($pointSuccess) alert-success @else alert-danger @endif" role="alert">
                    <span class="sr-only">Error:</span>
                    {{ Session::get('pointMessage') }}
                    <?php Session::forget('pointMessage'); ?>
                </div>
            @endif
            @if (Session::has('coupon_msg'))
                <?php
                if (strpos(Session::get('coupon_msg'), 'applied. You may proceed to checkout.') !== false) {
                    $couponSuccess = true;
                }
                ?>
                <div class="alert @if ($couponSuccess) alert-success @else alert-danger @endif" role="alert">
                    <span class="sr-only">Error:</span>
                    {{ Session::get('coupon_msg') }}
                    <?php Session::forget('coupon_msg'); ?>
                </div>
            @endif
            <div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif">
                <div class="panel-body">
                    <form id="coupon_checkout" action="{{ url('checkout/couponcode') }}" method="post">
                        <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                        <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                        <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                        <div class="input-group">
                            <input type="text" class="form-control" id="coupon_code" name="coupon" placeholder="{{ $label_coupon }}">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Enter Code</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if(isset($is_popbox) && $is_popbox == 1) { ?>
                <div class="panel panel-default" id="popbox-panel">
                <div class="panel-body">
                    <h3 class="panel-title">Selected Popbox</h3><hr>
                    <p style="" id="slt-add">
                        <span style="font-weight:bold;"><?php echo $popbox_locker; ?></span><p><a class="fa fa-map-marker"></a> <?php echo $popbox_address; ?>
                    </p>
                </div>
                </div>
            <?php } else{ 
            if(!$disable_popbox) {
            ?>
                <div class="panel panel-default" id="popbox-panel">
                <div class="panel-body">
                    <h3 class="panel-title">PopBox Locations</h3>
                    <form method="post" action="{{ url('checkout/popbox') }}" id="popbox_checkout">
                        <div class="input-group" style="margin-top:5px;">
                            <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                            <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                            <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                            <input type="hidden" id="popboxLocation" name="popboxLocation" value="">
                            <input type="hidden" id="popboxAddress" name="popboxAddress" value="">
                            <select class="form-control" id="deliverPopbox" name="popboxLocker">
                                <option data-add=""> - Available PopBox - </option>
                                <!--<?php foreach ($locker->data as $key => $value) { ?> -->
                                <!--        <option data-box="<?php echo $value->name; ?>" data-add="<?php echo $value->address; ?>" data-operation="<?php echo $value->operational_hours; ?>" ><?php echo $value->name; ?></option>-->
                                <!--<?php } ?>-->
                            </select>
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">SUBMIT</button>
                            </span>
                        </div>
                        
                    </form>
                    <p style="margin-top:5px;" id="slt-add">
                    </p>
                </div>
                </div>
            
            <?php } } ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3 class="panel-title">{{ $label_tran }}: {{ $trans_row->id }}</h3>
                </div>
            </div>
            @if ($grand_amt > 0)
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ $label_payment }}</h3>
                    </div>
                    <div class="panel-body">
                        <!-- <div class="col-md-4">
                            <input type="radio" name="payopt" value="palpay" id="input-palpay" checked>
                            <label class="label-payment-img" for="input-palpay">
                                <img src="{{ url('img/checkout/paypal.png') }}" alt="PayPal">
                                <br><small>(Paypal Account & Credit Card)</small>
                            </label>
                        </div> -->
                        <div class="col-md-4">
                            <input type="radio" name="payopt" value="mpay" id="input-mpay"  <?php if($boost == 1){ echo 'disabled';} else { echo 'checked';}?>>
                            <label class="label-payment-img" for="input-mpay">
                                <img src="{{ url('img/checkout/managepay.png') }}" alt="Manage Pay">
                                <br><small>
                                    <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                    <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                              </small>
                            </label>
                        </div>
                        @if (Session::get('devicetype') == 'android')
                           
                            <?php if($trans_row->buyer_username == 'wiraizkandar' || $trans_row->buyer_username == 'maruthu') {?>   
                            <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay2" id="input-molpay">
                                    <label class="label-payment-img" for="input-molpay">
                                        <img src="{{ url('img/checkout/molpay-logo.png') }}" alt="MOLPay">
                                        <br><small>
                                            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/cimb-logo.png') }}" alt="cimb Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/maybank-logo.png') }}" alt="maybank Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/alipay-logo.png') }}" alt="alipay Pay"></span>
                                        </small>
                                    </label>
                                </div>
                            <?php }else{ ?>
                                <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay" id="input-molpay" <?php if($boost == 1){ echo 'disabled';}?>>
                                    <label class="label-payment-img" for="input-molpay">
                                        <img src="{{ url('img/checkout/payment-razerpay.png') }}" alt="Razer Pay">
                                        <br><small>
                                            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/cimb-logo.png') }}" alt="cimb Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/maybank-logo.png') }}" alt="maybank Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/alipay-logo.png') }}" alt="alipay Pay"></span>
                                        </small>
                                    </label>
                                </div> 
                            <?php } ?>
                        @else
                            <div class="col-md-4">
                                <input type="radio" name="payopt" value="molpay2" id="input-molpay" <?php if($boost == 1){ echo 'disabled'; }?>>
                                <label class="label-payment-img" for="input-molpay">
                                    <img src="{{ url('img/checkout/payment-razerpay.png') }}" alt="MOLPay">
                                    <br><small>
                                            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/cimb-logo.png') }}" alt="cimb Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/maybank-logo.png') }}" alt="maybank Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/alipay-logo.png') }}" alt="alipay Pay"></span>
                                        </small>
                                </label>
                            </div>
                            <!--<div class="col-sm-4">-->
                            <!--    <input type="radio" name="payopt" value="bpoints" id="input-molpay">-->
                            <!--    <label class="label-payment-img" for="input-molpay">-->
                            <!--        <img src="{{ url('img/checkout/bcard2.png') }}" alt="BPoints">-->
                            <!--        <br><small>(BPoints)</small>-->
                            <!--    </label>-->
                            <!--</div>-->
                        @endif
                        <?php // if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'agneshpchua' || $trans_row->buyer_username == 'tammyong' || $trans_row->buyer_username == 'wiraizkandar') {?>   
                        <div class="col-md-4">
                        <input type="radio" name="payopt" value="boost" id="input-boost" <?php if($boost == 1){ echo 'checked'; }?>>
                        <label class="label-payment-img" for="input-boost">
                            <img src="{{ url('img/checkout/Boost_Logo_White.png') }}" alt="Boost">
                            
                          </small>
                        </label>
                        </div>
                        <?php //}?>
                        
                        <?php // if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'agneshpchua' || $trans_row->buyer_username == 'tammyong' ) {?>   
                                <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay3" id="input-molpaytng">
                                    <label class="label-payment-img" for="input-molpaytng" align="left">
                                        <img src="{{ url('img/checkout/Touch-n-go-ewallet.png') }}" alt="Touch-n-Go">
                                    </label>
                                </div>
                                <!-- <div class="col-md-4">-->
                                <!--    <input type="radio" name="payopt" value="molpay4" id="input-molpay">-->
                                <!--    <label class="label-payment-img" for="input-molpay">-->
                                <!--        <img src="{{ url('img/checkout/razerpay-logo.png') }}" alt="RazerPay">-->
                                <!--        <br><small>-->
                                <!--            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>-->
                                <!--            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>-->
                                <!--            <span><img style="height:50px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>-->
                                <!--            <span><img style="height:50px;" src="{{ url('img/checkout/cimb-logo.png') }}" alt="cimb Pay"></span>-->
                                <!--            <span><img style="height:50px;" src="{{ url('img/checkout/maybank-logo.png') }}" alt="maybank Pay"></span>-->
                                <!--            <span><img style="height:50px;" src="{{ url('img/checkout/alipay-logo.png') }}" alt="alipay Pay"></span>-->
                                <!--        </small>-->
                                <!--    </label>-->
                                <!--</div>-->
                          <?php // }?>
                          
                          <?php  if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'wiraizkandar') {?>   
                                
                                <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay4" id="input-molpayrazer">
                                    <label class="label-payment-img" for="input-molpayrazer">
                                        <img src="{{ url('img/checkout/razerpay-logo.png') }}" alt="RazerPay">
                                        
                                    </label>
                                </div>
                                
                                
                            <?php echo $molpay_url4; }?>
                        
                    </div>
                </div>
            @else
                <input type="hidden" name="payopt" value="fullredeem">
            @endif
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $label_add }}</h3>
                </div>
                <div class="panel-body">
                    {{$trans_row->delivery_addr_1}}<br />
                    {{$trans_row->delivery_addr_2}}<br />
                    {{$trans_row->delivery_postcode}}, {{$trans_row->delivery_city}}<br />
                    {{$trans_row->delivery_state}}<br />
                    {{$trans_row->delivery_country}}<br />
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $label_contact }}</h3>
                </div>
                <div class="panel-body">
                    {{ $trans_row->delivery_name }}<br>{{ $trans_row->delivery_contact_no }}
                </div>
            </div>
            @if ( ! empty($trans_row->special_msg))
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h3 class="panel-title">{{ $label_msg }}</h3>
                    </div>
                    <div class="panel-body">
                        {{ $trans_row->special_msg }}
                    </div>
                </div>
            @endif
            <div class="panel panel-default">
                <div class="list-group">
                    @foreach ($trans_detail_query as $row)
                        <?php if ( ! empty($row->product_group)) continue; 
                         if($row->is_taxable == 2){
                            $totalIncl = $totalIncl + number_format($row->price* $row->unit, 2, '.', ''); 
                        }
                        ?>
                        <div class="list-group-item item">
                            <span class="pull-right">{{ $currency }} {{ number_format($row->price * $row->unit, 2, '.', '') }}</span>
                            <h3 class="panel-title">@if ($row->is_taxable == 2) ** @else * @endif {{ $row->product_name }}</h3>
                            SKU: {{ $row->sku }}<br>
                            Unit Price: {{ $currency }} {{ number_format($row->price, 2, '.', '') }}<br>
                            Quantity: {{ $row->unit }}<br>
                            <?php $deliveryTime = empty($row->delivery_time) ? '24 hours' : $row->delivery_time; $deliveryTimes[] = $deliveryTime; ?>
                            Delivery Time: <b>{{ $deliveryTime }}</b><br>
                        </div>
                    @endforeach
                   
                    <?php foreach ($trans_detail_group_query as $row): ?>
                        <?php if ((strlen($mpaysku) + strlen($row->product_name) + 1) < 1000) $mpaysku = $mpaysku.$row->product_name.'|'; ?>
                            <div class="list-group-item item">
                                <span class="pull-right">{{ $currency }} {{ number_format($group_product_price[$row->sku], 2, '.', '') }}</span>
                                <h3 class="panel-title">@if ($group_product_gst[$row->sku] > 0) ** @else * @endif {{ $row->product_name }}</h3>
                                SKU: {{ $row->sku }}<br>
                                Unit Price: {{ $currency }} {{ number_format($group_product_price[$row->sku] / $row->unit, 2, '.', '') }}<br>
                                Quantity: {{ $row->unit }}<br>
                            </div>
                    <?php endforeach; ?>
                    
                    <div class="list-group-item">
                        <span class="pull-right">{{ $currency }} {{ number_format($delivery_charges, 2, '.', '') }}</span>
                        ** {{ $label_delivery }}<br>
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->process_fees, 2, '.', '') }}</span>
                        ** {{ $label_process }}<br>
                        <!-- @if ( ! empty($coupon_code))
                            <span class="pull-right">- {{ $currency }} {{ number_format($coupon_code_amount, 2, '.', '') }}</span>
                            {{ $label_coupon }}: {{ $coupon_code }}
                        @endif Old Invoice  -->
                    </div>
                    
                    <div class="list-group-item">
                        <span class="pull-right">{{ $currency }} {{ number_format($total_fees +($gst_delivery), 2, '.', '') }}</span> <!-- New Invoice -->
                        <!-- <span class="pull-right">{{ $currency }} {{ number_format($total_fees - ( ! empty($coupon_code) ? $coupon_code_amount : 0), 2, '.', '') }}</span> Old Invoice--> 
                        <h3 class="panel-title">{{ $label_subtotal }}</h3>
                    </div>
                    	<!--
                    <div class="list-group-item">
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->gst_total, 2, '.', '') }}</span>
                        {{ $label_gst }}
                      </div>-->
                    
                    @foreach ($trans_points as $point)
                        <div class="list-group-item">
                            <span class="pull-right">- {{ $currency }} {{ number_format($point->amount, 2, '.', '') }}</span>
                            {{ $point->type }} Redemption ({{ $point->point }} points)
                        </div>
                    @endforeach
                    
                    <!-- New Invoice Start  -->
                    @if ( ! empty($coupon_code))
                    <div class="list-group-item">
                        <!-- <span class="pull-right">{{ $currency }} {{ number_format($trans_row->delivery_charges, 2, '.', '') }}</span>
                        ** {{ $label_delivery }}<br>
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->process_fees, 2, '.', '') }}</span>
                        ** {{ $label_process }}<br> -->
                     
                            <span class="pull-right">- {{ $currency }} {{ number_format($coupon_code_amount, 2, '.', '') }}</span>
                            {{ $label_coupon }}: {{ $coupon_code }}
                       
                    </div>
                     @endif 
                    <!-- New Invoice End  -->
                     <div class="list-group-item">
                        <?php 
                        if($delivery_charges > 0 ){
                            $totalIncl = $totalIncl + $delivery_charges;
                        }
                        $total = $total_fees - ($coupon_code != '' ? $coupon_code_amount : 0) + ($gst_delivery) - ($total_trans_points ? $total_trans_points : 0); 
                        if($total < 0){
                            $total = 0.00;
                        }
                        
                        ?>
                        <span class="pull-right"><b>{{ $currency }} {{ number_format($total, 2, '.', '') }}</b></span>
                        <h3 class="panel-title">{{ $label_total }}</h3>
                    </div>
                    <!-- <div class="list-group-item" style="text-align:center;">
                        <span class="">GST Summary</span>
                    </div>-->
<!--                    <div class="list-group-item">
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->gst_total, 2, '.', '') }}</span>
                        {{ $label_gst }}
                    </div>-->
                       
                   <!-- <div class="list-group-item">
                        <div class="row">
                            <div class="col-md-4 col-xs-4">Incl GST</div>
                            <div class="col-md-4 col-xs-4">Excl GST</div>
                            <div class="col-md-4 col-xs-4">GST Amt</div>
                        </div>
                        <div class="row">
                            <div class="col-md-4 col-xs-4"><?php echo number_format($totalIncl, 2, '.', ''); ?> </div>
                            <div class="col-md-4 col-xs-4"><?php echo number_format($totalIncl - $trans_row->gst_total, 2, '.', ''); ?></div>
                            <div class="col-md-4 col-xs-4"><?php echo number_format($trans_row->gst_total, 2, '.', ''); ?></div>
                        </div>
                    </div>-->
                  
                    <div class="list-group-item">
                        @if (count($pointsEarn) > 0)
                            @foreach ($pointsEarn as $pointType => $pointEarn)
                                @if ($pointEarn > 0)
                                    You can earn {{ $pointEarn }} {{ $pointType }} on success purchase.<br>
                                @endif
                            @endforeach
                        @endif
                        <br>
                        Remark:<br>
                       <!-- * GST 0%<br>
                        ** GST {{ $trans_row->gst_rate }}% included-->
                    </div>
                </div>
            </div>
            @if ($grand_amt > 0)
                <?php $count = 0; ?>
                @foreach ($points as $point)
                    
                    <?php //if($point->type != 'JPoint') { ?>
                    <?php //if($point->type == 'BCard' && $point->bcardUsername != '') { ?>
                    
                    <?php $availablePoint = $point->point; ?>
                    <?php $pointType = strtolower($point->type); ?>
                    @foreach ($trans_points as $trans_point)
                        @if ($trans_point->point_type_id == $point->point_type_id)
                            <?php $availablePoint -= $trans_point->point; ?>
                        @endif
                    @endforeach
                    @if ($point->redeem_rate > 0 && $availablePoint > 0 )
                        <?php 
                        // if($point->type == 'JPoint' || $point->type == 'BCard')
                        if($point->type == 'JPoint' || $point->type == 'BCard') { ?>
                        <div class="panel panel-default" id="redemption" style="<?php echo $point->bcardUsername == '' && $point->type == 'BCard' ? 'display:none;': '' ; ?>">
                            <div class="panel-body">
                                <p>You have {{ $availablePoint }} {{ $point->type }} available ({{ $currency }} {{ number_format($availablePoint * $point->redeem_rate, 2) }} on tmGrocer) to shop with points.</p>
                                <form method="post" action="{{ url('checkout/redemption') }}" id="point_redemption_{{ $count }}">
                                    <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
                                    <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                                    <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                                    <input type="hidden" name="type" value="{{ $point->point_type_id }}">
                                    <div class="input-group">
                                        <input type="text" class="form-control" id="point_amount_{{ $count }}" name="point" placeholder="Point">
                                        <span class="input-group-btn">
                                            <button class="btn btn-primary" type="submit">Redeem</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php } ?>
                        <?php // if($point->type == 'BCard'  && $point->bcardUsername != '') { ?>
                        <!--<div class="panel panel-default" id="redemption">-->
                        <!--    <div class="panel-body">-->
                        <!--        <p>You have {{ $availablePoint }} {{ $point->type }} available ({{ $currency }} {{ number_format($availablePoint * $point->redeem_rate, 2) }} on Jocom) to shop with points.</p>-->
                        <!--        <form method="post" action="{{ url('checkout/redemption') }}" id="point_redemption_{{ $count }}">-->
                        <!--            <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">-->
                        <!--            <input type="hidden" name="lang" value="{{ $trans_row->lang }}">-->
                        <!--            <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">-->
                        <!--            <input type="hidden" name="type" value="{{ $point->point_type_id }}">-->
                        <!--            <div class="input-group">-->
                        <!--                <input type="text" class="form-control" id="point_amount_{{ $count }}" name="point" placeholder="Point">-->
                        <!--                <span class="input-group-btn">-->
                        <!--                    <button class="btn btn-primary" type="submit">Redeem</button>-->
                        <!--                </span>-->
                        <!--            </div>-->
                        <!--        </form>-->
                        <!--    </div>-->
                        <!--</div>-->
                         <?php // } ?>
                        <?php $count++; ?>
                    @endif
                   
                @endforeach
            @endif
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-body" role="tab" id="headingTNC">
                        <table>
                            <tr>
                                <td>
                                    <input type="checkbox" id="myCheck" name="test" required>
                                </td>
                                <td>
                                    <label for="myCheck">
                                        {{ $label_tnc1 }}
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTNC" aria-expanded="true" aria-controls="collapseOne">{{ $label_tnc2 }}</a>.
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="collapseTNC" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTNC">
                        <div class="panel-body" style="overflow: auto; -webkit-overflow-scrolling: touch; height: 150px;">
                            <iframe src="{{ url('tnc') }}"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" id="btn-checkout" class="btn btn-lg btn-block btn-primary" onclick="return paypalSubmit();">Checkout</button> <br><br><br>
        <?php

        $sum_cart = array(
            'name'     => "Transaction ID: {$trans_row->id}",
            'price'    => $grand_amt,
            'quantity' => (int) htmlentities(1, ENT_QUOTES),
            'shipping' => 0,
        );

        $test = Config::get('constants.ENVIRONMENT');

        if ($test == 'test') {
            $paypal_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        } else {
            $paypal_url = 'https://www.paypal.com/cgi-bin/webscr';
        }

        $return_url2 = '?tran_id='.$trans_row->id.'&lang='.$trans_row->lang;
        $cancel_url2 = '?tran_id='.$trans_row->id.'&lang='.$trans_row->lang;

        $deliveryDifference = count(array_unique($deliveryTimes));

        ?>
        <script type="text/javascript">
            
        
        function paypalSubmit() {
            if( ! document.getElementById('myCheck').checked) {
                // alert('{{ $label_tnc4 }}');
                $('#agreeModal').modal();
                document.getElementById('myCheck').focus();
                return false;
            }

            // Check delivery time difference
            var deliveryDifference = $('#deliveryDifference').html();

            if (deliveryDifference > 1) {
                $('#deliveryDifferenceModal').modal({backdrop: 'static', keyboard: false});
            } else {
                checkoutContinue();
            }

            return false;
        }

        function checkoutContinue() {
            $('#loading').css('display', 'block');

            setTimeout(function () {
                if (document.getElementById('coupon_code').value != '') {
                    document.getElementById('coupon_checkout').submit();
                }
                <?php for($i = 0; $i < $count; $i++): ?>
                    else if(document.getElementById('point_amount_{{ $i }}').value != '') {
                        document.getElementById('point_redemption_{{ $i }}').submit();
                    }
                <?php endfor; ?>
                else if($('[name="payopt"]:checked').val() == 'molpay') {
                    document.getElementById('molpay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay2') {
                    document.getElementById('molpay_checkout2').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay3') {
                    document.getElementById('molpay_checkout3').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay4') {
                    document.getElementById('molpay_checkout4').submit();
                } else if($('[name="payopt"]:checked').val() == 'bpoints') {
                    document.getElementById('molpay_bpoints').submit();
                } else if($('[name="payopt"]:checked').val() == 'mpay') {
                    document.getElementById('mpay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'boost') {
                    document.getElementById('boost_checkout').submit();
                } else if($('[name="payopt"]').val() == "fullredeem") {
                    document.getElementById('fullredeem_checkout').submit();
                } else {
                    document.getElementById('paypal_checkout').submit();
                }
            }, 100);
        }
        </script>
        <span class="hide" id="deliveryDifference">{{ $deliveryDifference }}</span>
        <form id="paypal_checkout" action="{{ $paypal_url }}" method="POST" style="display:none">
            <input TYPE="hidden" name="charset" value="utf-8">
            <input type="hidden" name="invoice" value="{{ $transaction_id }}">
            <!-- <input type="hidden" name="business" value="joshua.sew@gmail.com"> -->
            <!-- <input type="hidden" name="business" value="eugene.lee@jocom.my"> -->
            <input type="hidden" name="name" value="{{ $trans_row->name }}">
            <input type="hidden" name="address1" value="{{ $trans_row->delivery_addr_1 }}">
            <input type="hidden" name="address2" value="{{ $trans_row->delivery_addr_2 }}">
            <input type="hidden" name="country" value="{{ $trans_row->delivery_country }}">
            <input type="hidden" name="state" value="{{ $trans_row->delivery_state }}">
            <input type="hidden" name="zip" value="{{ $trans_row->delivery_postcode }}">

            <?php

            $settings = PayPal::get_setting();
            $form='';

            $form .= '
            <input type="hidden" name="cmd" value="_cart">
            <input type="hidden" name="upload" value="1">
            <input type="hidden" name="no_note" value="0">
            <input type="hidden" name="bn" value="PP-BuyNowBF">
            <input type="hidden" name="tax" value="0">
            <input type="hidden" name="rm" value="2">';

            $form .= '
                <input type="hidden" name="business" value="'.$settings['business'].'">
                <input type="hidden" name="handling_cart" value="'.$settings['shipping'].'">
                <input type="hidden" name="currency_code" value="'.$settings['currency'].'">
                <input type="hidden" name="lc" value="'.$settings['location'].'">
                <input type="hidden" name="return" value="'.$settings['returnurl'].$return_url2.'">
                <input type="hidden" name="cbt" value="'.$settings['returntxt'].'">
                <input type="hidden" name="cancel_return" value="'.$settings['cancelurl'].$cancel_url2.'">
                <input type="hidden" name="notify_url" value="'.$settings['notifyurl'].'">
                <input type="hidden" name="custom" value="'.$settings['custom'].'">';
            $form .= '
                <div id="item" class="itemwrap">
                    <input type="hidden" name="item_name_1" value="'.$sum_cart['name'].'">
                    <input type="hidden" name="quantity_1" value="'.$sum_cart['quantity'].'">
                    <input type="hidden" name="amount_1" value="'.$sum_cart['price'].'">
                   <input type="hidden" name="shipping_1" value="'.$sum_cart['shipping'].'">
                </div>';

            $form .= '<input id="ppcheckoutbtn" type="submit" value="Checkout" class="button">';
            echo $form;

            ?>
        </form>
        <form id="molpay_checkout" action="{{ isset($molpay_url) ? $molpay_url : '' }}" method="post" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : '')); ?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="orderid" value="{{ $transaction_id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $trans_row->name }}">
            <input type="hidden" name="bill_email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer">
        </form>
        <form id="molpay_checkout2" action="{{asset('/')}}checkout/molxml" method="post" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : '')); ?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="orderid" value="{{ $transaction_id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $trans_row->name }}">
            <input type="hidden" name="bill_email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer">
        </form>
        <form id="molpay_bpoints" action="{{ isset($molpay_url2) ? $molpay_url2 : '' }}" method="post" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : '')); ?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="orderid" value="{{ $transaction_id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $trans_row->name }}">
            <input type="hidden" name="bill_email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer">
        </form>
        <form id="fullredeem_checkout" method="GET" action="{{ url('checkout/point') }}" style="display: none;">
            <input type="hidden" name="tran_id" value="{{ $transaction_id }}">
        </form>
        <form id="mpay_checkout" action="{{ isset($mpay_url) ? $mpay_url : '' }}" method="get" style="display: none;">
            <input type="hidden" name="MID" value="{{ $mid }}">
            <input type="hidden" name="amt" value="{{ str_pad(number_format($grand_amt, 2, '', ''), 12, '0', STR_PAD_LEFT) }}">
            <input type="hidden" name="invNo" value="{{ $transaction_id }}">
            <input type="hidden" name="desc" value="{{ $mpaysku }}">
            <input type="hidden" name="postURL" value="{{ $mpay_returnurl }}">
            <input type="hidden" name="phone" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="email" value="{{ $trans_row->buyer_email }}">
        </form>
        <form id="boost_checkout" action="{{ isset($boost_url) ? $boost_url : '' }}" method="post" style="display: none;">
            <input type="hidden" name="boost_merchant_id" value="{{ $boost_merchant_id }}">
            <input type="hidden" name="amt" value="{{ $grand_amt }}">
            <input type="hidden" name="transid" value="{{ $transaction_id }}">
            <input type="hidden" name="desc" value="{{ $mpaysku }}">
            <input type="hidden" name="phone" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="username" value="{{ $trans_row->buyer_username }}">
            <input type="hidden" name="email" value="{{ $trans_row->buyer_email }}">
        </form>
        <form id="molpay_checkout3" action="{{ isset($molpay_url3) ? $molpay_url3 : '' }}" method="post" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : '')); ?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="orderid" value="{{ $transaction_id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $trans_row->name }}">
            <input type="hidden" name="bill_email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer TNG eWallet">
        </form>
        <form id="molpay_checkout4" action="{{ isset($molpay_url4) ? $molpay_url4 : '' }}" method="post" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : '')); ?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="orderid" value="{{ $transaction_id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $trans_row->name }}">
            <input type="hidden" name="bill_email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer Razer Pay">
            
        </form>
        <form id="jocom_checkout" action="http://dev.jocom.com.my/test" method="get" style="display: none;">
            <input type="hidden" name="MID" value="{{ $transaction_id }}">
        </form>
        <div class="modal fade" id="deliveryDifferenceModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $lavel_diff_delivery }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="checkoutContinue();">Yes</button>
                        <button type="button" class="btn btn-primary" data-dismiss="modal" onclick="$('#separateCheckoutModal').modal({backdrop: 'static', keyboard: false});">No</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="separateCheckoutModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $lavel_regroup }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="agreeModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $label_tnc4 }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <script src="{{ url('js/jquery.js') }}"></script>
        <script src="{{ url('js/bootstrap.min.js') }}"></script>
        <script>
       
        $( document ).ready(function() {
            function loadPopbox(){
        
            var location = $("#deliverPopbox option:selected" ).attr("data-add");
            var box = $("#deliverPopbox option:selected" ).attr("data-box");
            console.log(location);
            if(location != ''){
                $("#slt-add").html('<span style="font-weight:bold;">'+box+'</span><p><a class="fa fa-map-marker"></a> ' + location);
                $("#popaddresstext").val(location);
                $("#popboxLocation").val(box);
                $("#popboxAddress").val(location);
                
            }else{
                $("#slt-add").html('');
                $("#popaddresstext").val('');
                $("#popboxLocation").val('');
                $("#popboxAddress").val('');
            }
            
        
        }
        $('#deliverPopbox').change(function(){
            loadPopbox();
        });
        });
        
        </script>
        
    </body>
</html>