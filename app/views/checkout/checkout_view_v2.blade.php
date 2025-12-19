<?php


$disablepoint = false;
$kolflag = 0;
$voucherflag = 0;
$voucherflag1 = 0;
$bflag = 0;
$jpointflag = 0;
$jpointsku ="";
$jpfilter = 0;
$jpfilter1 = 0;

$flowboost = 0;
$flowuniq = 0;
$flowmulti = 0;

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
$jpoint_restrict=1;

if(isset($trans_coupon)) {
    $trans_coupon_row   = $trans_coupon;
    $coupon_code        = $trans_coupon_row->coupon_code;
    $coupon_code_amount = $trans_coupon_row->coupon_amount;
}
if(isset($jpoint_restriction)) {
    $jpoint_restrict   = $jpoint_restriction;
}

if(isset($trans_cashback)){
    $trans_cashback_row = $trans_cashback;
    $validity = '2021-04-25';
    $cbackid        = $trans_cashback_row['id']; 
    $jcashuserid    = $trans_cashback_row['user_id']; 
    $jcashsku       = $trans_cashback_row['sku'];
    $cashbackpoints = $trans_cashback_row['jcash_point']; 
    $cashbackrm     = $trans_cashback_row['jcash_point'] / 100; 
    $cashbacksku    = $trans_cashback_row['sku']; 
    $cashbackname   = $trans_cashback_row['product_name']; 
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
        $label_jcashback = "JCashback Points";
        $label_coupon1 = "First 6 digit of card";
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
        // $label_mcm = "Please add MCM Voucher code or MCM SKU";
        $label_mcm = "Oops! You only can checkout using Voucher code!";
        $label_bst = "Please choose other payment channel.e-voucher cannot be checkout with Boost wallet";
        $label_jpoint = "Oops! You only can redeem using Jpoints!";
        $label_flash = "Oops! You can't redeem flash sale products using Jpoints!";
        $label_voucher = "Oops! You can't redeem Voucher Code using Jpoints!";
        $lavel_diff_delivery = 'Your items will be delivered on different timing. Are you OK to proceed?';
        $lavel_regroup = 'You may need group your items based on delivery time for fast delivery.';
        $checkout_sku = 'You will not be able to checkout this SKU with other items.';
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

 $flowuniq_2 = 0;

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
    
    // Boost Start 
//     if($row->sku == 'JC-0000000034055') {
//       $flowboost = 1;  
//     } else if ($row->sku == 'JC-0000000034056') {
//       $flowboost = 1;  
//     } else if ($row->sku == 'JC-0000000034059') {
//       $flowboost = 1;  
//     } 
    
    
//     // Boost End 
    
  if($row->sku == 'JC--0000000029627') {
      $flowuniq = 1;
      $flowuniq_2 = 1;
    }
    else 
    {
         $flowuniq = $flowuniq + 1;
    }
    
    if($row->sku == 'JC-0000000048152') {
      $flowuniq3 = 1;
      $flowuniq_4 = 1;
    } else 
    {
         $flowuniq3 = $flowuniq3 + 1;
    }
    
    
    if ($row->sku == 'JC-0000000047776') {
        $flowuniq4 = 1;
        $flowuniq_5 = 1;
    } else 
    {
         $flowuniq4 = $flowuniq4 + 1;
    }
    
    if ($row->sku == 'JC-0000000047774') {
        $flowuniq5 = 1;
        $flowuniq_6 = 1;
    } else 
    {
         $flowuniq5 = $flowuniq5 + 1;
    }
    
    if ($row->sku == 'JC-0000000046535') {
        $flowuniq6 = 1;
        $flowuniq_7 = 1;
    } else 
    {
         $flowuniq6 = $flowuniq6 + 1;
    }
    if ($row->sku == 'JC-0000000046533') {
        $flowuniq7 = 1;
        $flowuniq_8 = 1;
    } else 
    {
         $flowuniq7 = $flowuniq7 + 1;
    }
    if ($row->sku == 'JC-0000000046532') {
        $flowuniq8 = 1;
        $flowuniq_9 = 1;
    } else 
    {
         $flowuniq8 = $flowuniq8 + 1;
    }
    if ($row->sku == 'JC-0000000046531') {
        $flowuniq9 = 1;
        $flowuniq_10 = 1;
    } else 
    {
         $flowuniq9 = $flowuniq9 + 1;
    }
    if ($row->sku == 'JC-0000000046530') {
        $flowuniq10 = 1;
        $flowuniq_11 = 1;
    } else 
    {
         $flowuniq10 = $flowuniq10 + 1;
    }
    if ($row->sku == 'JC-0000000046527') {
        $flowuniq11 = 1;
        $flowuniq_12 = 1;
    } else 
    {
         $flowuniq11 = $flowuniq11 + 1;
    }
    if ($row->sku == 'JC-0000000046526') {
        $flowuniq12 = 1;
        $flowuniq_13 = 1;
    } else 
    {
         $flowuniq12 = $flowuniq12 + 1;
    }
    if ($row->sku == 'JC-0000000046525') {
        $flowuniq13 = 1;
        $flowuniq_14 = 1;
    } else 
    {
         $flowuniq13 = $flowuniq13 + 1;
    }
    if ($row->sku == 'JC-0000000046524') {
        $flowuniq14 = 1;
        $flowuniq_15 = 1;
    } else 
    {
         $flowuniq14 = $flowuniq14 + 1;
    }
    if ($row->sku == 'JC-0000000046523') {
        $flowuniq15 = 1;
        $flowuniq_16 = 1;
    } else 
    {
         $flowuniq15 = $flowuniq15 + 1;
    }
    if ($row->sku == 'JC-0000000046522') {
        $flowuniq16 = 1;
        $flowuniq_17 = 1;
    } else 
    {
         $flowuniq16 = $flowuniq16 + 1;
    }
    if ($row->sku == 'JC-0000000046521') {
        $flowuniq17 = 1;
        $flowuniq_18 = 1;
    } else 
    {
         $flowuniq17 = $flowuniq17 + 1;
    }
    if ($row->sku == 'JC-0000000045517') {
        $flowuniq18 = 1;
        $flowuniq_19 = 1;
    } else 
    {
         $flowuniq18 = $flowuniq18 + 1;
    }
    if ($row->sku == 'JC-0000000045516') {
        $flowuniq19 = 1;
        $flowuniq_20 = 1;
    }
    else 
    {
         $flowuniq19 = $flowuniq19 + 1;
    }
    
//     if($row->sku == 'JC-0000000037588') {
//       $flowuniq = 1;
//     }
   
   
    
}


$grand_amt = bcsub($total_fees, ($coupon_code != '' ? $coupon_code_amount : 0), 2);
$grand_amt = bcadd($grand_amt, ($gst_delivery != '' ? $gst_delivery : 0), 2);
$grand_amt = bcsub($grand_amt, ($total_trans_points ? $total_trans_points : 0), 2);

 if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'maruthujocom') {
    $grand_amt = bcsub($grand_amt, ($cashbackrm != '' ? $cashbackrm : 0), 2);
}

$grand_amt = $grand_amt;

if($grand_amt < 0){
    $grand_amt = 0.00;
}

$signature = $revpay_verifykey.$revpay_merchant_id.$transaction_id.$grand_amt.'MYR';
$valueSign = hash('sha512', $signature);
// echo $valueSign.'<br>';

 $ref_numer = 'JC'.$transaction_id;
            $response_code = '00';
            $signature = $revpay_verifykey.$revpay_merchant_id.$ref_numer.$grand_amt.'MYR';
            // echo $signature.'<br>';
            $valueSign = hash('sha512', $signature);

    //GrabPay 
    $grabformat = number_format($grand_amt, 2);
    
    $parts = explode('.', (string) $grabformat);
    
    $string = str_replace(',', '', $parts[0]);
    $whole = (int)$string; 
    $decimal = $parts[1]; 
    $totnumber = (int)($whole.$decimal);
    
    // echo $whole.'-'.$decimal.'-'.$totnumber;
    
    
     $timezone  = -8; //(GMT -5:00) EST (U.S. & Canada)
     $timezone  = 'Asia/Kuala_Lumpur';
     $gmtdate = gmdate("D, d M Y H:i:s", time() + 3600*($timezone+date("I"))).' GMT';
     
     $partertxtID = 'JCM'.$transaction_id;  //c782659e8b544c06be23d1c3167fdf73
    //  $partertxtID = 'c782659e8b544c06be23d1c3167fdf78';
     $params = json_encode(array(
              "partnerGroupTxID"=>$partertxtID,
              "partnerTxID"=>$partertxtID,
              "currency"=>"MYR",
              "amount"=>$totnumber,
              "description"=>"Jocom Payment",
              "merchantID"=>"9a80a2b8-eed3-4e70-a3f3-5355a8fe308f"
        ));
        
    // GrabPay

?>
<!DOCTYPE html>
<html lang="en">
    <head><meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        
        
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
        <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ url('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet">
        <link href="{{ url('css/checkout.css?v=2.3.0') }}" rel="stylesheet">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-base64.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/hmac-sha256.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-utf8.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
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
        .fancy-collapse-panel .panel-default > .panel-heading {
padding: 0;

}
.fancy-collapse-panel .panel-heading a {
padding: 12px 35px 12px 15px;
display: inline-block;
width: 74%;
background-color: #ffffff;
color: #054c06;
position: relative;
text-decoration: none;
font-weight: bold;
}
.fancy-collapse-panel .panel-heading a:after {
font-family: "FontAwesome";
content: "\f147";
position: absolute;
right: 20px;
font-size: 20px;
font-weight: 400;
top: 50%;
line-height: 1;
margin-top: -10px;
}
.fancy-collapse-panel .panel-heading a.collapsed:after {
content: "\f196";
}
.btn-customs{
color:#ffffff;
background-color: #024747;
border-color: #024747;
border-top-left-radius: 40px !important;
border-top-right-radius: 40px;
border-bottom-right-radius: 40px;
border-bottom-left-radius: 40px !important;
margin-left:1px !important;
}
.btn-customs:hover{
color:#ffffff !important;
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
                if (strpos(Session::get('coupon_msg'), 'applied. You may proceed to checkout') !== false) {
                    $couponSuccess = true;
                }
                
                
                ?>
                <div class="alert @if ($couponSuccess) alert-success @else alert-danger @endif" role="alert">
                    <span class="sr-only">Error:</span>
                    {{ Session::get('coupon_msg') }}<?php //if($razerpay == 1){ echo 'with ShopeePay';}?>
                    <?php if($razerpay == 1 && $couponSuccess ==true){ echo 'with ShopeePay';} Session::forget('coupon_msg'); ?>.
                </div>
            @endif
            @if (Session::has('jcashback_msg'))
                <?php

                if ($cashbackdetails_id !== false) {
                    $JCashbackSuccess = true;
                }
                ?>
                <div class="alert @if ($JCashbackSuccess) alert-success @else alert-danger @endif" role="alert">
                    <span class="sr-only">Error:</span>
                    {{ Session::get('jcashback_msg') }} applied. You may proceed to checkout.
                    <?php Session::forget('jcashback_msg'); ?>
                </div>
            @endif
            <!--<div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif">-->
            <!--    <div class="panel-body">-->
            <!--        <form id="coupon_checkout" action="{{ url('checkout/couponcode') }}" method="post">-->
            <!--            <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">-->
            <!--            <input type="hidden" name="lang" value="{{ $trans_row->lang }}">-->
            <!--            <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">-->
            <!--            <div class="input-group">-->
            <!--                <input type="text" class="form-control" id="coupon_code" name="coupon" placeholder="{{ $label_coupon }}">-->
            <!--                <span class="input-group-btn">-->
            <!--                    <button class="btn btn-primary" type="submit">Enter Code</button>-->
            <!--                </span>-->
            <!--            </div>-->
            <!--        </form>-->
            <!--    </div>-->
            <!--</div>-->
            <?php  if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'maruthujocom') {?>   
            <!-- Start JCashBack-->
            <div class="panel panel-default  @if ( empty($cbackid)) hide @endif" id="jocom-panel">
            <div class="panel-body">
            <h3 class="panel-title"><b>JCashback</b></h3><hr>
                <div class="panel panel-default @if ( !empty($cashbackdetails_id)) hide @endif">
                      <div class="panel-body">
                        <form id="couponjcash_checkout" action="{{ url('checkout/jcashback') }}" method="post">
                        <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                        <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                        <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                        <input type="hidden" name="jcashbackid" value="{{$cbackid}}">
                        <input type="hidden" name="jcashuserid" value="{{$jcashuserid}}">
                        <input type="hidden" name="jcashsku" value="{{$jcashsku}}">
                        <input type="hidden" name="jcashpoints" value="{{$cashbackpoints}}">
                            <div  class="form-group row">
                                <div class="col-sm-8">
                                    <p>Your have {{$cashbackpoints}}(RM {{$cashbackrm}}) <b>JCashback</b> points for {{$cashbackname}} <br> {{$cashbacksku}} <br><sub>Validity Till: {{$validity}}</sub></p>
                                    <hr>
                                </div>
                                <div class="col-sm-4">
                                    <span class="input-group-btn float-right">
                                        <button class="btn btn-primary " type="submit">Redeem</button>
                                    </span>
                                </div>
                            </div>
                         </form>
                        
                          
                      </div>  
                </div>
            </div>
            </div>
            <!-- End JCashBack-->
            <?php } ?>
            <!-- Start Coupon-->
            <!-- Jocom start -->
            @if (count($trans_points) == 0)
            <div class="panel panel-default" id="jocom-panel">
            <div class="panel-body">
            <h3 class="panel-title"><b>tmGrocer Coupon Code</b></h3><hr>
                <div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif">
                        <div class="panel-body">
                            <form id="coupon_checkout" action="{{ url('checkout/couponcode') }}" method="post">
                                <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                                <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                                <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                                <div class="input-group">
                                    <input type="text" class="form-control maxlength" id="coupon_code" name="coupon" placeholder="{{ $label_coupon }}">
                                    <span class="input-group-btn">
                                        <button class="btn btn-primary" id="jcoupon" type="submit">Enter Code</button>
                                    </span>
                                </div>
                            </form>
                            <form id="coupon_autocheckout" action="{{ url('checkout/couponcode') }}" method="post" style="display:none;">
                                <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                                <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                                <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                                <div class="input-group cus-div">
                                    <input type="text" class="form-control maxlength cus-input" id="coupon_autocode" name="coupon" placeholder="{{ $label_coupon }}">
                                </div>
                            </form>
                        </div>
                    </div>
                    @if(!empty($static_coupon))
                    <p class="@if( ! empty($coupon_code)) hide @endif">
  <button class="btn input-block-level form-control" style="background-color:#d2f042; color:black;border-radius:26px" type="button" data-toggle="collapse" data-target=".multi-collapse" aria-expanded="false" aria-controls="justCollapse">View Coupon Code</button>
</p>
<div class="row" style="background-color: #fffed9;margin-right: -3px;margin-left: 1px;">
  <div class="col">
    <div class="collapse multi-collapse" id="justCollapse">
        <div class="row">
            <div class="">
                <div class="fancy-collapse-panel">
                    <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="margin-left: 9px;margin-right: 5px;">
                        
                        <?php $s=1; $stype = gettype($static_coupon);  $stypes = array('array','object'); if(in_array($stype, $stypes)) { ?>
                        @foreach($static_coupon as $coupon_static)
                        <div class="">
                            <div class="panel-heading" role="tab" id="headingOne">
                                <h5 class="panel-title" style="font-size:13px">
                                    <a data-toggle="collapse" class="collapsed" data-parent="#accordion" href="#collapse{{$s}}" aria-expanded="false" aria-controls="collapseOne" style="color:#ff1e90">{{$coupon_static->coupon_code}} <span>&nbsp;&nbsp;&nbsp;</span></a>
                                    <button class="btn btn-customs autocoupon" data-value="{{$coupon_static->coupon_code}}">Apply</button>
                                </h5>
                            </div>
                            <div id="collapse{{$s}}" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
                                <div class="panel-body" style="padding-left:25px;font-weight:bold;border:unset;color:#024747;">
                                   {{$coupon_static->description}} 
                                </div>
                            </div>
                        </div>
                    <?php $s++;?>
                        @endforeach
                        
                        <?php }?>
                       
                    </div>
                </div>
            </div>
    </div>
  </div>
</div>
</div>
@endif
        </div>
        </div>
        <!-- Jocom end -->
        <!-- Public Start -->
        <div class="panel panel-default" id="public-panel">
            <div class="panel-body">
                <h3 class="panel-title"><b>Credit/Debit Card Coupon Code</b></h3><hr>
                   <div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif">
                    <div lass="panel-body">
                        <form id="coupon_checkout_public" action="{{ url('checkout/couponpubliccode') }}" method="post">
                            <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                            <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                            <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                            <div class="form-group">
                                <input type="text" class="form-control" required="" id="public_bin" name="public_bin" placeholder="{{ $label_coupon1 }}">
                            </div>
                            <div class="form-group">
                                <input type="text" class="form-control" required="" id="coupon_codepublic" name="coupon_codepublic" placeholder="{{ $label_coupon }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-primary" id="pcoupon" type="submit">Enter Code</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
        </div>
        @endif
        <!-- Public End -->

        <!-- End Coupon-->
            
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
            <div class="panel-body">
                <form id="grab_pay_checkout" action="https://api.tmgrocer.com/grabpay/generate" method="post">
                    
                    <input type="hidden" name="transaction_id" id="transaction_id" value="{{ $trans_row->id }}">
                    <input type="hidden" name="hmacs" id="hmacs">
                    <input type="hidden" name="gmtdate" id="gmtdate">
                    <input type="hidden" name="params" id="params">
                    
                </form>
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
                        
                         <?php // if ($ccard == 1) { ?>   
                        <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay6" id="input-molpayrazer" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
                                    <label class="label-payment-img" for="input-molpayrazer">
                                        <b>Credit Card / Debit Card</b>
                                        <br><small>
                                        <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                        <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                                        </small>
                                    </label>
                                </div>
                         <?php // }else{ ?>
                        <!-- <div class="col-md-4">-->
                        <!--    <input type="radio" name="payopt" value="mpay" id="input-mpay"  <?php // if($boost == 1 || $razerpay == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>-->
                        <!--    <label class="label-payment-img" for="input-mpay">-->
                        <!--        <b>Credit Card / Debit Card</b>-->
                                <!--<img src="{{ url('img/checkout/managepay.png') }}" alt="Manage Pay">-->
                        <!--        <br><small>-->
                        <!--            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>-->
                        <!--            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>-->
                        <!--      </small>-->
                        <!--    </label>-->
                        <!--</div>-->
                         <?php // } ?>        
                        @if (Session::get('devicetype') == 'android')
                           
                            <?php if($trans_row->buyer_username == 'wiraizkandar' || $trans_row->buyer_username == 'maruthu') {?>   
                            <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay2" id="input-molpay">
                                    <label lass="label-payment-img" for="input-molpay">
                                        <!--<img src="{{ url('img/checkout/molpay-logo.png') }}" alt="MOLPay">-->
                                        <b>Online Banking/Credit Card/e-Wallets</b>
                                        <br><small>
                                            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/cimb-logo.png') }}" alt="cimb Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/maybank-logo.png') }}" alt="maybank Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/razerpay-logo.png') }}" alt="alipay Pay"></span>
                                        </small>
                                    </label>
                                </div>
                            <?php }else{ ?>
                                <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay" id="input-molpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'disabled';}  else { echo 'checked';} ?>>
                                    <label class="label-payment-img" for="input-molpay">
                                        <!--<img src="{{ url('img/checkout/razerpay-logo.png') }}" height="70" alt="Razer Pay">-->
                                        <b>Online Banking/Credit Card/e-Wallets</b>
                                        
                                    </label>
                                       <br><small>
                                            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/cimb-logo.png') }}" alt="cimb Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/maybank-logo.png') }}" alt="maybank Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/razerpay-logo.png') }}" alt="razer pay"></span>
                                        </small> 
                                </div> 
                            <?php } ?>
                        @else
                            <div class="col-md-4">
                                <input type="radio" name="payopt" value="molpay2" id="input-molpayrazer" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled'; } else if ($ccard == 1) { echo 'disabled';} else { echo 'checked';}?>>
                                <label lass="label-payment-img" for="input-molpay">
                                    <!--<img src="{{ url('img/checkout/razerpay-logo.png') }}" alt="MOLPay">-->
                                    <b>Online Banking/Credit Card/e-Wallets</b>
                                    <br><small>
                                            <span><img style="height:20px;" src="{{ url('img/checkout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                            <span><img style="height:40px;" src="{{ url('img/checkout/visa-logo.png') }}" alt="visa Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/cimb-logo.png') }}" alt="cimb Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/maybank-logo.png') }}" alt="maybank Pay"></span>
                                            <span><img style="height:50px;" src="{{ url('img/checkout/razerpay-logo.png') }}" alt="razer pay"></span>
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
                        <input type="radio" name="payopt" value="boost" id="input-boost" <?php if($boost == 1 || $flowboost == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if ($tngpay == 1) { echo 'disabled';} else if($razerpay == 1){ echo 'disabled'; } ?>>
                        <label class="label-payment-img" for="input-boost">
                            <img src="{{ url('img/checkout/Boost_Logo_White.png') }}" alt="Boost">
                            
                          </small>
                        </label>
                        </div>
                                <!--<div class="col-md-4">-->
                                <!--    <input type="radio" name="payopt" value="molpay5" id="input-molpayrazer" <?php if($boost == 1){ echo 'checked'; } if($razerpay == 1){ echo 'disabled'; } ?>>-->
                                <!--    <label class="label-payment-img" for="input-molpayrazer">-->
                                <!--        <img src="{{ url('img/checkout/Boost_Logo_White.png') }}" alt="Boost">-->
                                        
                                <!--    </label>-->
                                <!--</div>-->
                        <?php //}?>
                        
                        <?php // if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'agneshpchua' || $trans_row->buyer_username == 'tammyong' ) {?>   
                                <!--<div class="col-md-4">-->
                                <!--    <input type="radio" name="payopt" value="molpay3" id="input-molpaytng" <?php if($boost == 1 || $razerpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'disabled';}?>>-->
                                <!--    <label class="label-payment-img" for="input-molpaytng" align="left">-->
                                <!--        <img src="{{ url('img/checkout/Touch-n-go-ewallet.png') }}" alt="Touch-n-Go">-->
                                <!--    </label>-->
                                <!--</div>-->
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
                          <?php // if($trans_row->buyer_username == 'joshuadaboss' || $trans_row->buyer_username == 'maruthujocom') {?>   
                            <div class="col-md-4">
                                    <input type="radio" name="payopt" value="revpay" id="input-revpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled'; } else if ($ccard == 1) { echo 'disabled';}?>>
                                    <label class="label-payment-img" for="input-molpay">
                                        <!--<img src="{{ url('img/checkout/revPay_logo.png') }}" alt="revPay">-->
                                        <img src="{{ url('img/checkout/log-revpay-50px.png') }}" eight="35px;" alt="revPay">
                                        <br><small>
                                            <!--<span><img style="height:35px;" src="{{ url('img/checkout/FPX-logo.png') }}" alt="FPX Pay"></span>-->
                                            <!--<span><img style="height:35px;" src="{{ url('img/checkout/logo-payment-touchngo.png') }}" alt="touchngo Pay"></span>-->
                                            <!--<span><img style="height:35px;" src="{{ url('img/checkout/logo-payment-grabpay.png') }}" alt="grab Pay"></span>-->
                                            <span><img style="height:35px;" src="{{ url('img/checkout/logo-payment-unionpay.png') }}" alt="Union Pay"></span>
                                            <span><img style="height:35px;" src="{{ url('img/checkout/alipay-logo.png') }}" alt="alipay Pay"></span>
                                        </small>
                                    </label>
                                </div> 
                                <!--<div class="col-md-4">-->
                                <!--    <input type="radio" name="payopt" value="touchngo" id="input-touchngo" <?php  if($tngpay == 1){ echo 'checked'; } else if ($boost == 1 || $razerpay == 1 || $flowboost == 1){ echo 'disabled'; } else if ($ccard == 1) { echo 'disabled';}?>>-->
                                <!--    <label class="label-payment-img" for="input-mpay">-->
                                <!--        <img src="{{ url('img/checkout/logo-payment-touchngo.png') }}" alt="Touchngo">-->
                                        
                                <!--      </small>-->
                                <!--    </label>-->
                                <!--</div>-->
                          <?php // }?>
                            <div class="col-md-4">
                                <input type="radio" name="payopt" value="shopeepay" id="input-shopeepay" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1 || $tngpay == 1){ echo 'disabled'; } ?>>
                                <label class="label-payment-img" for="input-shopeepay">
                                    <img src="{{ url('img/checkout/logo-shopeepay-v2.png') }}" alt="ShopeePay">
                                    
                                  </small>
                                </label>
                                </input>
                            </div>
                            <div class="col-md-4">
                                <input type="radio" name="payopt" value="grab_pay" id="input-grabpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1){ echo 'disabled';}?>
                                <label class="label-payment-img" for="input-grabpay">
                                    <img src="{{ url('img/checkout/grabpay.png') }}" height="35px;" alt="GrabPay">
                                    <small>
                                        <span><img style="height:65px;" src="{{ url('img/checkout/grabpaylater.png') }}" alt="Grab PayLater"></span>
                                    </small>
                                </label>
                            </div>
                            <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay8" id="input-molpayrazer" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1){ echo 'disabled'; } else if($tngpay == 1){ echo 'disabled'; } ?>>
                                    <label class="label-payment-img" for="input-atome">
                                    <img src="{{ url('img/checkout/atome-payment-getway-app.png') }}" alt="atome">
                                    
                                    </label>
                                    </input>
                                </div>
                            <!--<div class="col-md-4">-->
                            <!--        <input type="radio" name="payopt" value="pacepay" id="input-pacepay" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1){ echo 'disabled'; } else if($tngpay == 1){ echo 'disabled'; }  ?>>-->
                            <!--        <label class="label-payment-img" for="input-paceepay">-->
                            <!--        <img src="{{ url('img/checkout/pace.png') }}" height="45px;" alt="Pace Pay">-->
                            <!--        </label>-->
                            <!--    </div>-->
                                
                          <?php  if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'maruthujocom') {?>   
                            
                                <div class="col-md-4">
                                    <input type="radio" name="payopt" value="favepay" id="input-favepay" <?php if($boost == 1 || $razerpay == 1){ echo 'disabled';}?>
                                    <label class="label-payment-img" for="input-favepay">
                                        <img src="{{ url('img/checkout/fave.png') }}" height="35px;" alt="Fave Pay">
                                        <!--<small>-->
                                        <!--    <span><img style="height:65px;" src="{{ url('img/checkout/grabpaylater.png') }}" alt="Grab PayLater"></span>-->
                                        <!--</small>-->
                                    </label>
                                </div>
                                
                                <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay4" id="input-molpayrazer">
                                    <label class="label-payment-img" for="input-molpayrazer">
                                        <b>Credit Card / Debit Card</b>
                                        <img src="{{ url('img/checkout/razerpay-logo.png') }}" alt="RazerPay">
                                        
                                    </label>
                                    {{$molpay_url4}}
                                </div>
                                <div class="col-md-4">
                                    <input type="radio" name="payopt" value="molpay5" id="input-molpayrazer">
                                    <label class="label-payment-img" for="input-molpayrazer">
                                        <img src="{{ url('img/checkout/Boost_Logo_White.png') }}" alt="Boost">
                                        
                                    </label>
                                </div>
                                
                                
                                
                                
                            <?php //echo $molpay_url4; 
                            // $flowboost = 1;
                            //   echo '<pre>';
                            //   print_r($trans_detail_query);
                            //   echo '</pre>';
                            }
                            ?>
                        
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
                        if($row->sku == 'JC-0000000029757' || $row->sku == 'JC-0000000029885' || $row->sku == 'JC-0000000029886' || $row->sku == 'JC-0000000031266' || $row->sku == 'JC-0000000032612' || $row->sku == 'JC-0000000032610' || $row->sku == 'JC-0000000032606' || $row->sku == 'JC-0000000032605' || $row->sku == 'JC-0000000032602' || $row->sku == 'JC-0000000032598' || $row->sku == 'JC-0000000032592') {
                            $voucherflag = 1;
                            $bflag = 1;
                        }
                        if($row->sku == 'JC-0000000026553') {
                            $kolflag = 1;   
                        }
                        
                        if($row->sku == 'JC-0000000029635' || $row->sku == 'JC-0000000029636' || $row->sku == 'JC-0000000029637') {
                            $bflag = 1;
                        }
                        
                      
                        
                        // 01 December - 31 December
                    //     if($row->sku == 'JC-0000000031128') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031128'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031129') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031129'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031130') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031130'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031131') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031131'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031133') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031133'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031134') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031134'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000030428') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000030428'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031135') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031135'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031136') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031136'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000031137') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000031137'.'<br>';
                    // 	} 	
                        
                        // 1 January 2021 - 31 January 2021
                    //     if($row->sku == 'JC-0000000032469') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032469'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032472') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032472'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032473') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032473'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032475') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032475'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032477') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032477'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032478') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032478'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032480') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032480'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032481') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032481'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032483') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032483'.'<br>';
                    // 	} else if ($row->sku == 'JC-0000000032486') {
                    // 	  $jpointflag = 2;  
                    // 	  $jpointsku .= 'JC-0000000032486'.'<br>';
                    // 	}
                        
                        
                        // 11 January - 17 January
                        // if($row->sku == 'JC-0000000032947') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032947'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032946') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032946'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032945') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032945'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032944') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032944'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032943') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032943'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032942') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032942'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032710') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032710'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032941') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032941'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032940') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032940'.'<br>';
                        // } else if ($row->sku == 'JC-0000000032939') {
                        //   $jpointflag = 2;  
                        //   $jpointsku .= 'JC-0000000032939'.'<br>';
                        // } 
                
                        
                        // if($row->sku == 'JC-0000000028384' || $row->sku == 'JC-0000000029905' || $row->sku == 'JC-0000000029926' || $row->sku == 'JC-0000000029927' || $row->sku == 'JC-0000000030177' || $row->sku == 'JC-0000000017594' || $row->sku == 'JC-0000000030161' || $row->sku == 'JC-0000000030176' || $row->sku == 'JC-0000000029186' || $row->sku == 'JC-0000000030162' || $row->sku == 'JC-0000000029188' || $row->sku == 'JC-0000000029398' || $row->sku == 'JC-0000000029192' || $row->sku == 'JC-0000000029193' || $row->sku == 'JC-0000000029943' || $row->sku == 'JC-0000000029198' || $row->sku == 'JC-0000000029969' || $row->sku == 'JC-0000000029400' || $row->sku == 'JC-0000000029560' || $row->sku == 'JC-0000000029562' || $row->sku == 'JC-0000000029567' || $row->sku == 'JC-0000000029568' || $row->sku == 'JC-0000000030163' || $row->sku == 'JC-0000000030164' || $row->sku == 'JC-0000000029573' || $row->sku == 'JC-0000000030165' || $row->sku == 'JC-0000000030166' || $row->sku == 'JC-0000000030000' || $row->sku == 'JC-0000000030174' || $row->sku == 'JC-0000000030167' || $row->sku == 'JC-0000000030169' || $row->sku == 'JC-0000000030170' || $row->sku == 'JC-0000000030171' || $row->sku == 'JC-0000000030172' || $row->sku == 'JC-0000000030173') {
                        //   $jpfilter = 2;  
                        // }
                        
                        //   if($row->sku == 'JC-0000000029635' || $row->sku == 'JC-0000000029636' || $row->sku == 'JC-0000000029637' || $row->sku == 'JC-0000000029886' || $row->sku == 'JC-0000000029885' || $row->sku == 'JC-0000000029757'){
                        //      $jpfilter1 = 2;    
                        //   }
                        
                        ?>
                        <div class="list-group-item item">
                            <span class="pull-right">{{ $currency }} {{ number_format($row->price * $row->unit, 2, '.', '') }}</span>
                            <h3 class="panel-title">@if ($row->is_taxable == 2) ** @else * @endif {{ $row->product_name }}</h3>
                            SKU: {{ $row->sku }}<br>
                            Unit Price: {{ $currency }} {{ number_format($row->price, 2, '.', '') }}<br>
                            Quantity: {{ $row->unit }}<br>
                            <?php $deliveryTime = empty($row->delivery_time) ? '24 hours' : $row->delivery_time; $deliveryTimes[] = $deliveryTime; ?>
                            Delivery Time: <b>{{ $deliveryTime }}</b><br>
                            <input type="hidden" name="kolflag" id="kolflag" value="{{ $kolflag }}">
                            <input type="hidden" name="voucherflag" id="voucherflag" value="{{ $voucherflag }}">
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
                        <?php 
                            $c_verify ='';
                            if($row->sku == 'JC-0000000031266'){
                                $c_verify = substr($coupon_code, 0, 3);
                            }
                            
                            
                            
                            if($coupon_code == 'JCMMCM300' || $coupon_code == 'JCMMCM500' || $coupon_code == 'JCMMCM800' || $c_verify =='BST' || $coupon_code == 'BINF41CHVQ' || $coupon_code == 'BINFBR3EPY' || $coupon_code == 'BINF97B669' || $coupon_code == 'BINFUL3HR4' || $coupon_code == 'BINFYNADG8' || $coupon_code == 'BINFGJ9RZ5' || $coupon_code == 'BINFHAWJOL') {
                                $voucherflag1 = 1;
                            }
                             if($trans_row->buyer_username == 'maruthujocom')
                             {
                                 echo $c_verify;
                             }
                        ?>
                            <input type="hidden" name="voucherflag1" id="voucherflag1" value="{{ $voucherflag1 }}">
                            <span class="pull-right">- {{ $currency }} {{ number_format($coupon_code_amount, 2, '.', '') }}</span>
                            {{ $label_coupon }}: {{ $coupon_code }}
                       
                    </div>
                     @endif 
                    <!-- New Invoice End  -->
                    <!-- New JCashback Start  -->
                     @if ( ! empty($cashbackdetails_id))
                    <div class="list-group-item">
                    
                        <?php 
                           
                        ?>
                            <input type="hidden" name="jcashdetails_id" id="jcashdetails_id" value="{{ $cashbackdetails_id }}">
                            <span class="pull-right">- {{ $currency }} {{ number_format($cashbackrm, 2, '.', '') }}</span>
                            {{ $label_jcashback }}: {{ $cashbackpoints }}
                       
                    </div>
                     @endif 
                    <!-- New JCashback End  -->
                     <div class="list-group-item">
                        <?php 
                        if($point->point >0){
                            
                            if($jpfilter == 2) {
                                $jpfilter = 1;  
                            }
                            
                            if($jpfilter1 == 2) {
                                $jpfilter1 = 1;  
                            }
                            
                            
                            if($jpointflag == 2){
                                $jpointflag = 1;
                            }
                            
                        }
                        // echo $voucherflag1;
                        if($delivery_charges > 0 ){
                            $totalIncl = $totalIncl + $delivery_charges;
                        }
                        $total = $total_fees - ($coupon_code != '' ? $coupon_code_amount : 0) + ($gst_delivery) - ($total_trans_points ? $total_trans_points : 0)- ($cashbackdetails_id != '' ? $cashbackrm : 0); 
                        if($total < 0){
                            $total = 0.00;
                        }
                        
                        
                        if($jpointflag == 1) {
                            if($total > 0){
                                $jpointflag = 2;
                            }
                            
                            if($total == 0) {
                                $jpointflag = 1;
                                
                            }
                            
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
                     <!-- *    @if (count($pointsEarn) > 0)
                            @foreach ($pointsEarn as $pointType => $pointEarn)
                                @if ($pointEarn > 0)
                                    You can earn {{ $pointEarn }} {{ $pointType }} on success purchase.<br>
                                @endif
                            @endforeach
                        @endif -->
                        <br>
                        Remark:<br>
                       <!-- * GST 0%<br>
                        ** GST {{ $trans_row->gst_rate }}% included-->
                    </div>
                </div>
            </div>
            @if ($grand_amt > 0)
            @if ( empty($coupon_code)||$jpoint_restrict=="0")
                <?php $count = 0;
                     // if($trans_row->buyer_username == 'maruthujocom' || $trans_row->buyer_username == 'maruthu') {
                        
                ?>
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
                        //if($point->type == 'JPoint' || $point->type == 'BCard') Apr 20,20
                        // if($point->type == 'JPoint' || $point->type == 'BCard') { 
                        //  if($trans_row->buyer_username == 'maruthujocom' || $trans_row->buyer_username == 'Twiggy') {
                        if($point->type == 'TPoint'){
                            // echo $count
                        ?>
                       
                        <div class="panel panel-default" id="redemption" style="<?php // echo $point->bcardUsername == '' && $point->type == 'BCard' ? 'display:none;': '' ; ?>">
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
                                            <button class="btn btn-primary" type="submit" id="idredemption">Redeem</button>
                                        </span>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <?php  } ?>
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
                <?php // } ?>
            @endif
            @endif
            <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
                <div class="panel panel-default">
                    <div class="panel-body" role="tab" id="headingTNC">
                        <table>
                            <!--<tr>-->
                            <!--    <td>-->
                            <!--        <input type="checkbox" id="mocCheck" name="mocCheck" required>-->
                            <!--    </td>-->
                            <!--    <td>-->
                            <!--        <label for="mocCheck">-->
                            <!--            I hearby agree with orders to be send after April 1st due to disrupted roadblocks.-->
                            <!--        </label>-->
                            <!--    </td>-->
                            <!--</tr>-->
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
        <button type="button" id="btn-checkout" class="btn btn-lg btn-block btn-primary" onclick="return paypalSubmit();">Checkout</button> <br><br><br><br><br><br>
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
        <!--<script> window.jQuery = window.$ = require('jquery'); </script>-->
        <script type="text/javascript">
            
        
        function paypalSubmit() {
            if( ! document.getElementById('myCheck').checked) {
                // alert('{{ $label_tnc4 }}');
                $('#agreeModal').modal();
                document.getElementById('myCheck').focus();
                return false;
            }
            if (document.getElementById('voucherflag').value == 1) {
                    // $('#mcmModal').modal();
                    // return false;
            }
            
            
            
            // Check delivery time difference
            var deliveryDifference = $('#deliveryDifference').html();
            <?php  if( $voucherflag == 1 && $voucherflag1 == 0 ) {?>  
                 $('#mcmModal').modal();
                    return false;
            <?php } else if( $voucherflag == 0 && $voucherflag1 == 1 ) {  ?>
                 $('#mcmModal').modal();
                    return false;
            <?php } ?>
            
            <?php  if($bflag == 1) {?>  
                if($('[name="payopt"]:checked').val() == 'boost')
                 {
                 $('#boostModal').modal();
                    return false;
                 }
            <?php } ?>
            <?php  if($jpointflag == 2) {?> 
                $('#JpointModal').modal();
                    return false;
            <?php } ?>
            <?php  if($jpfilter == 1) {?> 
                $('#JpointflashModal').modal();
                    return false;
            <?php } ?>
            <?php  if($jpfilter1 == 1) {?> 
                $('#JpointVoucherModal').modal();
                    return false;
            <?php } ?>
            <?php 
            // echo $flowuniq;
            if($flowuniq_2 == 1){
            if($flowuniq >= 2) {?> 
                $('#singleModal').modal();
                    return false;
            <?php }} ?>
            
            <?php 
            echo $flowuniq3;
            if($flowuniq_4 == 1){
            if($flowuniq3 >= 2) {?> 
                // alert($flowuniq3);
                $('#singleModalmooncake').modal();
                    return false;
            <?php }} ?>
            
            
            
            
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
                // alert('Proceed');
                var myEleValue = '';
                 var element = document.getElementById('coupon_code');
                  if(element){
                        myEleValue= element.value;
                    }
               
                
                // if (document.getElementById('coupon_code').value != '') {
                 if (myEleValue != '') {
                    document.getElementById('coupon_checkout').submit();
                }
              
                
                
                
                <?php for($i = 0; $i < $count; $i++): ?>
                    if(document.getElementById('point_amount_{{ $i }}').value != '') {
                        document.getElementById('point_redemption_{{ $i }}').submit();
                    }
                <?php endfor; ?>
                //  alert('Proceed');
                else if($('[name="payopt"]:checked').val() == 'molpay') {
                    // alert('Proceed');
                    document.getElementById('molpay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay2') {
                    document.getElementById('molpay_checkout2').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay3') {
                    document.getElementById('molpay_checkout3').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay4') {
                    document.getElementById('molpay_checkout4').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay5') {
                    document.getElementById('molpay_checkout5').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay6') {
                    document.getElementById('molpay_checkout6').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay8') {
                    document.getElementById('molpay_checkout8').submit();
                } else if($('[name="payopt"]:checked').val() == 'bpoints') {
                    // document.getElementById('molpay_bpoints').submit();
                } else if($('[name="payopt"]:checked').val() == 'mpay') {
                    document.getElementById('mpay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'boost') {
                    document.getElementById('boost_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'revpay') {
                    document.getElementById('revpay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'touchngo') {
                    document.getElementById('touchngo_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'shopeepay') {
                    document.getElementById('shopeepay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'favepay') {
                    document.getElementById('favepay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'pacepay') {
                    document.getElementById('pacepay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'grab_pay') {
                    checkoutgrab();
                } else if($('[name="payopt"]').val() == "fullredeem") {
                    // alert('Please proceed');
                    document.getElementById('fullredeem_checkout').submit();
                } else {
                    document.getElementById('paypal_checkout').submit();
                }
            }, 100);
        }
        
        // GrabPay
        
       
        
    // Start Web URL Parameters 
    
    function getStoreAPIrequest(state,code_verifier,request,partnertxid){
        var transID = "<?php echo $transaction_id; ?>"
        $.ajax({
            method: "POST",
            url: "/grabpay/codeverifier",
            dataType:'json',
            data: {
                'transaction_id':transID, 
                'state' :state,
                'code_verifier' : code_verifier,
                'request' : request, 
                'partnertxid' : partnertxid, 
            },
            beforeSend: function(){
                
            },
            success: function(data) {
                console.log(data);
            }
        });
    }
    
    function sendURL(url){
        
        $.ajax({
            method: "POST",
            url: "/grabpay/sendurl",
            dataType:'json',
            data: {
                'url':url
            },
            
            beforeSend: function(){
                
            },
            success: function(data) {
                // console.log(data);
                
            }
        });
        
    }
   
   
    
    function getAuthorizeLink(request,partnertxid) 
    {
    var scope = ['openid', 'payment.one_time_charge'];
    var response_type = 'code';
    var redirect_uri = 'https://api.tmgrocer.com/grabpay/redirect';
    var nonce = generateRandomString(16);
    var state = generateRandomString(7);
    var code_challenge_method = 'S256';
    //var code_verifier = generateCodeVerifier(64);
    //var code_challenge = generateCodeChallenge(code_verifier);
    var code_verifier = base64URLEncode(generateRandomString(64));
    var code_challenge = base64URLEncode(CryptoJS.SHA256(code_verifier));
    var countryCode = "MY";
    var currency = "MYR";
    console.log('state='+state);
    console.log('code_challenge='+code_challenge);
    console.log('code_verifier='+code_verifier);
    var params = {
        client_id: '7e7d07f655b64fcca6257ffb8d5f3faa',
        scope: scope.join(' '),
        response_type: response_type,
        redirect_uri: redirect_uri,
        nonce: nonce,
        state: state,
        code_challenge_method: code_challenge_method,
        code_challenge: code_challenge,
        request: request,
        acr_values: "consent_ctx:countryCode=" + countryCode + ",currency="+currency
    };
    var str = $.param(params);
    
    var returnvalue = getStoreAPIrequest(state,code_verifier,request,partnertxid);
    
    // You should get this URL from service discovery
    var url ='https://partner-api.grab.com/grabid/v1/oauth2/authorize?' + str;
    return url;
}

function generateRandomString(length) {
    var text = '';
    var possible ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
    for (var i = 0; i < length; i++) {
        text += possible.charAt(Math.floor(Math.random() * possible.length));
    }
    return text;
}

function base64URLEncode(str) {
    return str.toString(CryptoJS.enc.Base64).replace(/=/g,'').replace(/\+/g, '-').replace(/\//g, '_');
}
// var nonce = generateRandomString(16);
// var state = generateRandomString(7);
// var codeVerifier = base64URLEncode(generateRandomString(64));
// var codeChallenge = base64URLEncode(CryptoJS.SHA256(codeVerifier));

// End Web URL Parameters 
        
    function getPath(url) {
        var pathRegex = /.+?\:\/\/.+?(\/.+?)(?:#|\?|$)/;
        var result = url.match(pathRegex);
        if(!result){
            pathRegex = /\/.*/;
            result = url.match(pathRegex);
            return result && result.length == 1 ? result[0] : "";
        }
        return result && result.length > 1 ? result[1] : "";
    }

    function getQueryString(url) {
        var arrSplit = url.split("?");
        return arrSplit.length > 1 ? url.substring(url.indexOf("?")+1) : "";
    }

   
    function generateHMACSignature(partnerID, partnerSecret, httpMethod, requestURL, contentType, requestBody, timestamp) {
    var requestPath = getPath(requestURL);
    if (httpMethod == 'GET' || !requestBody) {
        requestBody = '';
    }
   
     var crt = CryptoJS.SHA256(requestBody);
    //  console.log("New"+ CryptoJS.enc.Base64.stringify(crt));

    var hashedPayload = CryptoJS.enc.Base64.stringify(CryptoJS.SHA256(requestBody));

    var requestData = [[httpMethod, contentType, timestamp, requestPath, hashedPayload].join('\n'), '\n'].join('');
    var hmacDigest = CryptoJS.enc.Base64.stringify(CryptoJS.HmacSHA256(requestData, partnerSecret));
    var authHeader = partnerID + ':' + hmacDigest;
    // console.log(hmacDigest);
    return authHeader;
}
   
   
    function checkoutgrab(){
    //   alert('Ol');
    var partnerID = "8ebf940e-92c1-493c-a517-4693099e5657";
    var partnerSecret = "bUfHoS8kNPUxSbC_";
    // var timestampString = new Date().toGMTString();
    var timestampString = "<?php echo $gmtdate; ?>";
    //console.log(timestampString);
     var initReqBody = '<?php echo $params; ?>';
   

    var requestPath = "https://partner-api.grab.com/grabpay/partner/v2/charge/init";
   
    var hmacSign = generateHMACSignature(
        partnerID,
        partnerSecret,
        "POST",
        requestPath,
        "application/json",
        initReqBody,
        timestampString
    );
    
        
            var productID = 1001;
            var gmtdate = '<?php echo $gmtdate; ?>';
            var params = '<?php echo $params; ?>';
           
            document.getElementById('hmacs').value=hmacSign;
            document.getElementById('gmtdate').value=gmtdate;
            document.getElementById('params').value=params;
            document.getElementById('grab_pay_checkout').submit();
            
            return false;
           
            
       
       
   }

    // GrabPay
        
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
        <form id="molpay_checkout2" action="{{ isset($molpay_url) ? $molpay_url : '' }}" method="post" style="display: none;">
        <!--<form id="molpay_checkout2" action="{{asset('/')}}checkout/molxml" method="post" style="display: none;">-->
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
            <input type="hidden" name="channel" value="RazerPay">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer Razer Pay">
            
        </form>
        <form id="molpay_checkout5" action="{{ isset($molpay_url5) ? $molpay_url5 : '' }}" method="post" style="display: none;">
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
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer Boost">
            
        </form>
        <form id="molpay_checkout6" action="{{ isset($molpay_url6) ? $molpay_url6 : '' }}" method="post" style="display: none;">
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
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer Visa / MasterCard">
            
        </form>
        <form id="shopeepay_checkout" action="{{ isset($molpay_url7) ? $molpay_url7 : '' }}" method="post" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : '')); ?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="orderid" value="{{ $transaction_id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="callbackurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="AppDeeplink" value="https://jocomapp.page.link?apn=com.jocomit.twenty37&ibi=com.jocomit.jocom&link=http%3A%2F%2Fdeeplink.jocom.my%2F%3Fpayment%3Dpayment">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $trans_row->name }}">
            <input type="hidden" name="bill_email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer ShopeePay">
            
        </form>
        <form id="molpay_checkout8" action="{{ isset($molpay_url8) ? $molpay_url8 : '' }}" method="post" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : '')); ?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="orderid" value="{{ $transaction_id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="AppDeeplink" value="https://jocomapp.page.link?apn=com.jocomit.twenty37&ibi=com.jocomit.jocom&link=http%3A%2F%2Fdeeplink.jocom.my%2F%3Fpayment%3Dpaymentatome">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $trans_row->name }}">
            <input type="hidden" name="bill_email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer Atome">
            
        </form>
        <form id="revpay_checkout" action="{{ isset($revpay_url) ? $revpay_url : '' }}" method="post" style="display: none;" accept-charset="UTF-8">
            
            <input type="hidden" name="Revpay_Merchant_ID" value="{{ isset($revpay_merchant_id) ? $revpay_merchant_id : '' }}">
            <input type="hidden" name="Reference_Number" value="{{ $ref_numer }}">
            <input type="hidden" name="Amount" value="{{ $grand_amt }}">
            <input type="hidden" name="Key_Index" value="1">
            <input type="hidden" name="Currency" value="MYR">
            <input type="hidden" name="Installment_Plan" value="">
            <input type="hidden" name="Installment_Term" value="">
            <input type="hidden" name="Signature" value="{{ $valueSign }}">
            <input type="hidden" name="Return_URL" value="{{ isset($revpay_returnurl) ? $revpay_returnurl : '' }}">
            <input type="hidden" name="Customer_Name" value="{{ $trans_row->name }}">
            <input type="hidden" name="Customer_Email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="Customer_Contact" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="Transaction_Description" value="Payment for tmGrocer">
            <input type="hidden" name="Payment_ID" value="3">
            <input type="hidden" name="Bank_Code" value="">
            
        </form>
        <form id="touchngo_checkout" action="{{ isset($revpay_url) ? $revpay_url : '' }}" method="post" style="display: none;">
            <input type="hidden" name="Revpay_Merchant_ID" value="{{ isset($revpay_merchant_id) ? $revpay_merchant_id : '' }}">
            <input type="hidden" name="Reference_Number" value="{{ $ref_numer }}">
            <input type="hidden" name="Amount" value="{{ $grand_amt }}">
            <input type="hidden" name="Key_Index" value="1">
            <input type="hidden" name="Currency" value="MYR">
            <input type="hidden" name="Installment_Plan" value="">
            <input type="hidden" name="Installment_Term" value="">
            <input type="hidden" name="Signature" value="{{ $valueSign }}">
            <input type="hidden" name="Return_URL" value="{{ isset($revpay_returnurl) ? $revpay_returnurl : '' }}">
            <input type="hidden" name="Customer_Name" value="{{ $trans_row->name }}">
            <input type="hidden" name="Customer_Email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="Customer_Contact" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="Transaction_Description" value="Payment for tmGrocer">
            <input type="hidden" name="Payment_ID" value="28">
            <input type="hidden" name="Bank_Code" value="">
        </form>
        
        <form id="favepay_checkout" action="https://api.tmgrocer.com/favepay/paymentqrcode" method="post" style="display: none;" accept-charset="UTF-8">
            
            <input type="hidden" name="trans_id" value="{{ $transaction_id }}">
            <input type="hidden" name="Amount" value="{{ $grand_amt }}">
            <input type="hidden" name="Currency" value="MYR">
            <input type="hidden" name="Customer_Name" value="{{ $trans_row->name }}">
            <input type="hidden" name="Customer_Email" value="{{ $trans_row->buyer_email }}">
            <input type="hidden" name="Customer_Contact" value="{{ $trans_row->delivery_contact_no }}">
            <input type="hidden" name="Location" value="{{ $trans_row->delivery_addr_1 }}, {{ $trans_row->delivery_addr_2 }}">
            <input type="hidden" name="Transaction_Description" value="Payment for tmGrocer">
           
            
        </form>
        <form id="pacepay_checkout" action="{{ url('pacepay/transaction') }}" method="post" style="display: none;" accept-charset="UTF-8">
            
            <input type="hidden" name="referenceID" value="{{ $transaction_id }}">
            <input type="hidden" name="amount" value="{{ $grand_amt }}">
            <input type="hidden" name="currency" value="MYR">
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
        <div class="modal fade" id="mcmModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $label_mcm }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="boostModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $label_bst }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="JpointModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $label_jpoint }}</h4>
                        <h5 class="text-center">{{ $jpointsku }}</h5>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="JpointflashModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $label_flash }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="JpointVoucherModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $label_voucher }}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="singleModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $checkout_sku }}</h4>
                        <h4 class="text-left">Popi LaLaLa 3ply Super Tissues 1 Carton (40 Pack x 40sheets)</h4>
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="singleModalmooncake" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">{{ $checkout_sku }}</h4>
                        <!--<h4 class="text-left">Duria Signature Musang King Snowy Skin Mooncake 经典猫山王榴莲冰皮月饼 (6PCS /SET) + FOC Pokka Houjicha (2 x 500ml)</h4>-->
                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        
        
        <div class="modal fade" id="mcoModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">Due to MCO, more orders had been stuck and not able to deliver due to massive roadblock, we will be back when we got our permission</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="kolModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">
                            <!-- Due to MCO, we only able to deliver out those products after 14th April. Thank you 
                            CONGRATS! Your freebies will be deliver to you from 14th March 2020 onwards. Thank you! -->
                            Your Non-Contact Infrared Thermometer will be deliver to you from 20th April 2020 onwards. Thank you!
                            </h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div>
        <!--<script src="{{ url('js/jquery.js') }}"></script>-->
        <script src="https://api.tmgrocer.com/js/jquery.js"></script>
        <!--<script src="{{ url('js/bootstrap.min.js') }}"></script>-->
        <script src="https://api.tmgrocer.com/js/bootstrap.min.js"></script>
        <script>
       
        $( document ).ready(function() {
            // var kol = $('#kolflag').val();
            // if(kol == 0){
            //     $('#mcoModal').modal();
            // }
            // else 
            // {
            //      $('#kolModal').modal();
            //     //  $('#btn-checkout').prop('disabled', false);
            // }
            
            // $('#mocCheck').change(function() {
            //     if(this.checked) {
            //         $('#btn-checkout').prop('disabled', false);
            //     } else {
            //         $('#btn-checkout').prop('disabled', true);
            //     }
            // });
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
        $("#coupon_code").change(function(){
            // alert('s');
             var len=$('#coupon_code').val().length;
            if(len > 0){
                $('#pcoupon').addClass('disabled')
                // $('#idredemption').addClass('disabled')
                // $('#point_amount_1').attr('readonly');
            } else{
                 $('#pcoupon').removeClass('disabled')
                //  $('#idredemption').removeClass('disabled')
                //  $('#point_amount_1').removeClass('readonly');
            }
        });

        $("#public_bin").change(function(){
            // alert('s');
             var len=$('#public_bin').val().length;
             var len1=$('#coupon_codepublic').val().length;
            if(len > 0 || len1 > 0){
                $('#jcoupon').addClass('disabled')
            } else{
                 $('#jcoupon').removeClass('disabled')
            }
        });

        $("#coupon_codepublic").change(function(){
            // alert('s');
             var len=$('#public_bin').val().length;
             var len1=$('#coupon_codepublic').val().length;
            if(len > 0 || len1 > 0){
                $('#jcoupon').addClass('disabled')
            } else{
                 $('#jcoupon').removeClass('disabled')
            }
        });
              $('.autocoupon').on('click',function(){
     var coupon_code=$(this).data('value');
     $('#coupon_autocode').val(coupon_code);
     document.getElementById('coupon_autocheckout').submit();
        });
        
        });
        
        </script>
        
    </body>
</html>