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
        $label_jpoint = "Oops! You only can redeem using Tpoints!";
        $label_flash = "Oops! You can't redeem flash sale products using Tpoints!";
        $label_voucher = "Oops! You can't redeem Voucher Code using Tpoints!";
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
        body{
            color:#024747 !important;
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
        .panel-title{
            color:#024747;
        }

        .label-payment-img small {
            margin-left: 10px;
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
        .cus-input{
            box-shadow:unset !important;
            border:unset !important;
            border-top-left-radius: 40px !important;
            border-top-right-radius: 40px !important;
            border-bottom-right-radius: 40px !important;
            border-bottom-left-radius: 40px !important;
        }
        .cus-input-xs{
            border:1px solid !important;
            border-top-left-radius: 40px !important;
            border-top-right-radius: 40px !important;
            border-bottom-right-radius: 40px !important;
            border-bottom-left-radius: 40px !important;
        }
        .cus-div{
            border:1px solid #024747 !important;
            border-top-left-radius: 40px !important;
            border-top-right-radius: 40px !important;
            border-bottom-right-radius: 40px !important;
            border-bottom-left-radius: 40px !important;
        }
        .btn-place-order{
            background-color:#024747;
            border-color:#024747;
            color:#ffffff;
        }
        .btn-place-order:hover{
            color:#ffffff;
        }
        #redemption{
            padding:9px;
        }
        .redemption{
            border: 2px solid #024747;
            border-radius: 17px;
            padding: 22px;
        }
        p{
            color:;#024747;
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
.address-group {
    background-color: #fff;
    display: block;
    margin: 10px 0;
    position: relative;
}
 .address-group >label{
      padding: 12px 30px;
      width: 96%;
      display: block;
      text-align: left;
      color: #3C454C;
      cursor: pointer;
      position: relative;
      z-index: 2;
      transition: color 200ms ease-in;
      overflow: hidden;
      margin:7px;
      border-radius: 24px;
}
      .crosslabel:before {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        content: '';
        background-color: #024747;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%) scale3d(1, 1, 1);
        transition: all 300ms cubic-bezier(0.4, 0.0, 0.2, 1);
        opacity: 0;
        z-index: -1;
      }
      .crosslabel:after {
        width: 32px;
        height: 32px;
        content: '';
        border: 3px solid #024747;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3Csvg width='32' height='32' viewBox='0 0 32 32' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
        background-repeat: no-repeat;
        background-position: 2px 3px;
        border-radius: 50%;
        z-index: 2;
        position: absolute;
        right: 31px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        transition: all 200ms ease-in;
      }
    .address-group > input:checked ~ label {
      color: #fff;
}
      .address-group > input:checked ~ label:before {
        transform: translate(-50%, -50%) scale3d(56, 56, 1);
        opacity: 1;
      }

      .address-group > input:checked ~ label:after {
        background-color: #54E0C7;
        border-color: #54E0C7;
      }
    

    .address-group > input {
      width: 32px;
      height: 32px;
      order: 1;
      z-index: 2;
      position: absolute;
      right: 37px;
      top: 14%;
      transform: translateY(-50%);
      cursor: pointer;
      visibility: visible;
    }
    .addform {
  padding: 0 16px;
  max-width: 550px;
  margin: 50px auto;
  font-size: 18px;
  font-weight: 400;
  line-height: 19px;
}
.panel-groups .panels {
    border-radius: 0;
    box-shadow: none;
    border-color: #EEEEEE;
  }

  .panel-defaults > .panel-headings {
    padding: 0;
    border-radius: 0;
    color: #212121;
    background-color: #FAFAFA;
    border-color: #EEEEEE;
  }

  .panel-titles {
    font-size: 14px;
  }

  .panel-titles > a {
    display: block;
    padding: 3px;
    text-decoration: none;
  }

  .more-less {
    float: right;
    color: #024747;
  }

  .panel-defaults > .panel-headings + .panel-collapses > .panel-bodys {
    border-top-color: #EEEEEE;
  }
  .label-payment-img{
      margin-left: 4px;
   }
   

  .inputGroup-payment{
    display: block;
    margin: 10px 0;
    position: relative;
}
.inputGroup-payment > label {
      padding: 9px 30px;
      width: 94%;
      display: block;
      text-align: left;
      color: #3C454C;
      cursor: pointer;
      position: relative;
      z-index: 2;
      transition: color 200ms ease-in;
      overflow: hidden;
      margin-left: 10px;
      border-radius:4px;
      background-color: #ffff;
}
   .cors-label:before {
        width: 10px;
        height: 10px;
        border-radius: 50%;
        content: '';
        background-color: #e7e7e78c;
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%) scale3d(1, 1, 1);
        transition: all 300ms cubic-bezier(0.4, 0.0, 0.2, 1);
        opacity: 0;
        z-index: -1;
      }
.cors-label:after {
        width: 21px;
        height: 21px;
        content: '';
        border: 2px solid #D1D7DC;
        background-color: #fff;
        background-image: url("data:image/svg+xml,%3Csvg width='29' height='25' viewBox='0 0 51 46' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M5.414 11L4 12.414l5.414 5.414L20.828 6.414 19.414 5l-10 10z' fill='%23fff' fill-rule='nonzero'/%3E%3C/svg%3E ");
        background-repeat: no-repeat;
        background-position: 2px 3px;
        border-radius: 50%;
        z-index: 2;
        position: absolute;
        right: 30px;
        top: 50%;
        transform: translateY(-50%);
        cursor: pointer;
        transition: all 200ms ease-in;
      }

    .inputGroup-payment > input:checked ~ label {
      border:1px solid #024747;
}
      .inputGroup-payment > input:checked ~ :before {
        transform: translate(-50%, -50%) scale3d(56, 56, 1);
        opacity: 1;
      }

      .inputGroup-payment > input:checked ~ :after {
        background-color: #5d9771;
        border-color: #b6ff55;
      }


    .inputGroup-payment > input {
      width: 21px;
      height: 21px;
      order: 1;
      z-index: 2;
      position: absolute;
      right: 39px;
      top: 39%;
      transform: translateY(-50%);
      cursor: pointer;
      visibility: hidden;
    }
    .a-link{
        background-color:#ffff;
    }
    .online-image{
        position: absolute;
        margin-left:-12px;
    }
    .onlinebank-text{
        margin: 25px;
    }
    .sidebar-logo{
        display:none;
    }
ol {
  list-style-type: none;
  counter-reset: item;
  margin: 0;
  padding: 0;
}

ol > li {
  display: table;
  counter-increment: item;
  margin-bottom: 10px;
}

ol > li:before {
  content: counters(item, ".") ". ";
  display: table-cell;
  padding-right: 0.6em;
}

li ol > li {
  margin: 0;
  margin-bottom: 10px;
}

li ol > li:before {
  content: counters(item, ".") " ";
}

ul > li {
  list-style-type: none;
  margin-left: -40px;
  margin-bottom: 10px;
}
b {
  display: inline-block;
  font-size: 16px;
  margin-bottom: 10px;
}
div#dynamic_address {
    margin-top: -14px !important;
}

div#deivery_recept {
    margin-top: -16px !important;
}
        </style>
    </head>
    <body>
        <div id="loading" style="display: none;">
            <div id="loading-animation">
                <img src="{{ url('img/newcheckout/lightbox-ico-loading.gif') }}">
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
            @if (Session::has('addressmsg'))
                <?php
                if (strpos(Session::get('addressmsg'), 'Delivery Address Changed Successfully!') !== false) {
                    $addressmsgSuccess = true;
                }
                ?>
                <div class="alert @if ($addressmsgSuccess) alert-success @else alert-danger @endif" role="alert">
                    <span class="sr-only">Error:</span>
                    {{ Session::get('addressmsg') }}
                    <?php Session::forget('addressmsg'); ?>
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
            <!--        <form id="coupon_checkout" action="{{ url('v2/checkout/couponcode') }}" method="post">-->
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
                        <form id="couponjcash_checkout" action="{{ url('v2/checkout/jcashback') }}" method="post">
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
                        <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><b>{{ $label_add }} @if($fav_address!="")<span class="fa fa-edit pull-right" data-toggle="collapse" data-target=".address-collapse" aria-expanded="false" aria-controls="justCollapses" style="font-size: 20px"></span>@endif</b></h3>
                </div>
                <div class="panel-body" id="dynamic_address">
                    {{$trans_row->delivery_addr_1}}<br />
                    {{$trans_row->delivery_addr_2}}<br />
                    {{$trans_row->delivery_postcode}}, {{$trans_row->delivery_city}}<br />
                    {{$trans_row->delivery_state}}<br />
                    {{$trans_row->delivery_country}}<br />
                </div>
                <form id="fav_address_form" action="{{ url('v2/checkout/favaddressupdate') }}" method="post" style="display:none">
                            <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                            <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                            <input type="hidden" name="id" id="fav_id_address" value="">
                            <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                </form>
                @if($fav_address!="")
                   <div class="collapse address-collapse" id="justCollapses">
        <div class="row">
            <div class="">
       <form class="addform">
        <?php $k=1; ?>
        @foreach($fav_address as $address_static)
                <div class="address-group">
                    @if($trans_row->delivery_postcode==$address_static['deliverpostcode']&&
$trans_row->delivery_name==$address_static['delivername']&&$trans_row->delivery_contact_no==$address_static['delivercontactno'])
    <input id="addinput{{$k}}" class="saveaddress" name="radio" type="radio" value="{{$address_static['addr_id']}}" checked="checked"/ id="primary_radio{{$k}}" data-value="{{$k}}">
    @else
        <input id="addinput{{$k}}" class="saveaddress" name="radio" type="radio" value="{{$address_static['addr_id']}}" id="primary_radio{{$k}}" data-value="{{$k}}"/>
    @endif
    <input type="hidden" value="{{$address_static['delivername']}}" id="delivernameid{{$k}}">
    <input type="hidden" value="{{$address_static['delivercontactno']}}" id="delivercontactnoid{{$k}}">
    <input type="hidden" value="{{$address_static['deliveradd1']}}" id="deliveradd1id{{$k}}">
    <input type="hidden" value="{{$address_static['deliveradd2']}}" id="deliveradd2id{{$k}}">
     <input type="hidden" value="{{$address_static['deliverpostcode']}}" id="deliverpostcodeid{{$k}}">
     <input type="hidden" value="{{$address_static['city_name']}}" id="city_nameid{{$k}}">
     <input type="hidden" value="{{$address_static['state_name']}}" id="state_nameid{{$k}}">
     <input type="hidden" value="{{$address_static['country_name']}}" id="country_nameid{{$k}}">
    <label for="addinput{{$k}}" class="crosslabel">{{ucfirst($address_static['delivername'])}}</label>
                    <p style="font-weight:normal;font-size:14px;line-height:19px;margin-left:28px;color:#024747">{{ucfirst($address_static['deliveradd1'])}}
                    {{$address_static['deliveryadd2']}}<br/>
                    {{$address_static['deliverpostcode']}}, {{$address_static['city_name']}}<br />
                    {{$address_static['state_name']}}<br />
                    {{$address_static['country_name']}}<br /></p>
  </div>
  <hr>
   <?php $k++;?>
          @endforeach
  </form>
            </div>
    </div>
  </div>
  @endif
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title"><b>{{ $label_contact }}</b></h3>
                </div>
                <div class="panel-body" id="deivery_recept">
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
            @if (count($trans_points) == 0)
            <div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif" id="jocom-panel">
            <div class="panel-body">
            <h3 class="panel-title"><b>tmGrocer Coupon Code</b></h3><hr style="margin-top: -3px !important;margin-bottom: 2px !important;">
                <div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif">
                        <div class="panel-body">
                            <form id="coupon_checkout" action="{{ url('v2/checkout/couponcode') }}" method="post">
                                <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                                <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                                <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                                <div class="input-group cus-div">
                                    <input type="text" class="form-control maxlength cus-input" id="coupon_code" name="coupon" placeholder="{{ $label_coupon }}">
                                    <span class="input-group-btn">
                                        <button class="btn btn-customs" id="jcoupon" type="submit">Enter Code</button>
                                    </span>
                                </div>
                            </form>
                             <form id="coupon_autocheckout" action="{{ url('v2/checkout/couponcode') }}" method="post" style="display:none;">
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
        <div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif" id="public-panel">
            <div class="panel-body">
                <h3 class="panel-title"><b>Credit/Debit Card Coupon Code</b></h3><hr style="margin-top:0px !important;margin-bottom: 13px !important">
                   <div class="panel panel-default @if ( ! empty($coupon_code)) hide @endif">
                    <div lass="panel-body">
                        <form id="coupon_checkout_public" action="{{ url('v2/checkout/couponpubliccode') }}" method="post">
                            <input type="hidden" name="transaction_id" value="{{ $trans_row->id }}">
                            <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                            <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                            <div class="form-group">
                                <input type="text" class="form-control cus-input-xs" required="" id="public_bin" name="public_bin" placeholder="{{ $label_coupon1 }}">
                            </div>
                            <div class="input-group cus-div">
                                <input type="text" class="form-control cus-input" required="" id="coupon_codepublic" name="coupon_codepublic" placeholder="{{ $label_coupon }}">
                                <span class="input-group-btn">
                                    <button class="btn btn-customs" id="pcoupon" type="submit">Enter Code</button>
                                </span>
                            </div>
                        </form>
                    </div>
                </div>
        </div>
        </div>
        </hr>
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
                    <form method="post" action="{{ url('v2/checkout/popbox') }}" id="popbox_checkout">
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
            <div class="panel panel-default" style="margin-bottom: -14px !important;">
                <div class="panel-body">
                    <p>
  <button class="btn input-block-level form-control" style="background-color:#024747; color:black;border-radius:26px;color:#ffffff" type="button"><b>{{ $label_tran }}: {{ $trans_row->id }}</b></button>
</p>
                    <h3 class="panel-title"></h3>
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
            
                 <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true" style="padding: 5px;background-color:#ffffff70;">
                     <div class="panel-heading">
                        <h3 class="panel-title"><b>{{ $label_payment }}</b></h3>
                    </div>
            
<div class="panels panel-default">
      <div class="panel-headings" role="tab" id="headingOne">
        <h4 class="panel-titles">
          <a role="button" data-toggle="collapse" class="a-link" data-parent="#accordion" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="color:#024747;padding:8px;">
              <small style="margin-right: 4px;margin-left: 6px;" class="sidebar-logo">
                                        <span><img style="height:16px;" src="https://api.tmgrocer.com/img/newcheckout/credit-card.png" alt="Credit Card/Debit Card"></span>
                                        </small>
            <i class="more-less fa fa-angle-right" style="margin-top:6px;"></i>
            <b>Credit Card / Debit Card</b><small>
                                        <span style="margin-left: 29px;"><img style="height:16px;" src="https://api.tmgrocer.com/img/newcheckout/mastercard-logo.png" alt="mastercard Pay"></span>
                                        <span><img style="height:25px;" src="https://api.tmgrocer.com/img/newcheckout/visa-logo.png" alt="visa Pay"></span>
                                        </small>
          </a>
          
        </h4>
      </div>
      <div id="collapseOne" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingOne">
        <div class="panel-body" style="padding:0px !important">
              <div class="inputGroup-payment">
    <input type="radio" name="payopt" value="razer_credit" id="input-molpayrazer" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
    <label for="input-molpayrazer" class="cors-label">
                                        <span><img style="height:20px;" src="{{ url('img/newcheckout/mastercard-logo.png') }}" alt="mastercard Pay"></span>
                                        <span><img style="height:20px;" src="{{ url('img/newcheckout/visa-logo.png') }}" alt="visa Pay"></span>
                                        </label>
  </div>
        </div>
      </div>
    </div>
    <div class="panels panel-default">
      <div class="panel-headings" role="tab" id="headingTwo">
        <h4 class="panel-titles">
          <a role="button" data-toggle="collapse" class="a-link" data-parent="#accordion" href="#collapseTwo" aria-expanded="true" aria-controls="collapseTwo" style="color:#024747;padding:8px;">
              <small style="margin-right: 4px;margin-left: 4px;" class="sidebar-logo">
                                        <span><img style="height:16px;" src="https://api.tmgrocer.com/img/newcheckout/online-banking.png" alt="Credit Card/Debit Card"></span>
                                        </small>
            <i class="more-less fa fa-angle-right" style="margin-top:6px;"></i>
            <b>Online Banking</b><small>
                                        <span style="margin-left: 82px;"><img style="height:25px;" src="https://api.tmgrocer.com/img/newcheckout/FPX-logo.png" alt="FPX Pay"></span>
                                        <span><img style="height:25px;" src="https://api.tmgrocer.com/img/newcheckout/razerpay-logo.png" alt="razer pay"></span>
                                        <span><img src="https://api.tmgrocer.com/img/newcheckout/log-revpay-50px.png" height="10px;" alt="revPay"></span>
                                        </small>
          </a>
          
        </h4>
      </div>
      <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTwo">
        <div class="panel-body" style="padding:0px !important"> 
             
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_mb2u" id="input-maybank2ufpx" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-maybank2ufpx" class="cors-label">
        <span class="online-image"><img style="height:20px;" src="{{ url('img/newcheckout/maybank2u.png') }}" alt="Maybank2U(FPX)"></span>
        <span class="onlinebank-text">Maybank2U</span>
        </label>
        </div>
        
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_cimbclicks" id="input-cimbclicksfpx" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-cimbclicksfpx" class="cors-label">
        <span class="online-image"><img style="height:20px;" src="{{ url('img/newcheckout/cimb-clicks.png') }}" alt="CIMB Clicks(FPX)"></span>
        <span class="onlinebank-text">CIMB Clicks</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_pbb" id="input-publicbank" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-publicbank" class="cors-label">
        <span class="online-image"><img style="height:20px;" src="{{ url('img/newcheckout/public-bank.png') }}" alt="Public Bank"></span>
        <span class="onlinebank-text">Public Bank</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_hlbconnect" id="input-hongleongonlinefpx" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-hongleongonlinefpx" class="cors-label">
        <span class="online-image"><img style="height:20px;" src="{{ url('img/newcheckout/hong-leong.png') }}" alt="Hong Leong Online(FPX)"></span>
        <span class="onlinebank-text">Hong Leong Online</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_rhbnow" id="input-rhbnowfpx" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-rhbnowfpx" class="cors-label">
        <span class="online-image"><img style="height:9px;" src="{{ url('img/newcheckout/rhb-now.png') }}" alt="RHB Now(FPX)"></span>
        <span class="onlinebank-text">RHB Now</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_bimb" id="input-bankislam" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-bankislam" class="cors-label">
        <span class="online-image"><img style="height:19px;" src="{{ url('img/newcheckout/bank-islam.png') }}" alt="Bank Islam"></span>
        <span class="onlinebank-text">Bank Islam</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_bankrakyat" id="input-bankkerjasama" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-bankkerjasama" class="cors-label">
        <span class="online-image"><img style="height:25px;" src="{{ url('img/newcheckout/bank-kerjasama.png') }}" alt="Kerjasama Bank"></span>
        <span class="onlinebank-text">Bank Kerjasama Rakyat<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Malaysia</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_bankmuamalat" id="input-bankmuamalat" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-bankmuamalat" class="cors-label">
        <span class="online-image"><img style="height:19px;" src="{{ url('img/newcheckout/bank-muamalat.png') }}" alt="Bank Muamalat"></span>
        <span class="onlinebank-text">Bank Muamalat</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_bsn" id="input-bsnbank" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-bsnbank" class="cors-label">
        <span class="online-image"><img style="height:19px;" src="{{ url('img/newcheckout/bsn.png') }}" alt="Bank Simpanan Nasional"></span>
        <span class="onlinebank-text">Bank Simpanan<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nasional</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_abb" id="input-affinbankfpx" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-affinbankfpx" class="cors-label">
        <span class="online-image"><img style="height:19px;" src="{{ url('img/newcheckout/affin-bank.png') }}" alt="Affin Online(FPX)"></span>
        <span class="onlinebank-text">Affin Online</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_abmb" id="input-alliancebank" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-alliancebank" class="cors-label">
        <span class="online-image"><img style="height:19px;" src="{{ url('img/newcheckout/alliance-bank.png') }}" alt="Alliance bank"></span>
        <span class="onlinebank-text">Alliance Bank<br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Malaysia Berhad</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_amonline" id="input-amonlinefbx" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-amonlinefbx" class="cors-label">
        <span class="online-image"><img style="height:19px;" src="{{ url('img/newcheckout/amonline-bank.png') }}" alt="Am Online(FPX)"></span>
        <span class="onlinebank-text">AmOnline</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_hsbc" id="input-hsbc" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-hsbc" class="cors-label">
        <span class="online-image"><img style="height:14px;" src="{{ url('img/newcheckout/hsbc-bank.png') }}" alt="HSBC Bank"></span>
        <span class="onlinebank-text">HSBC Bank</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_kfh" id="input-kuwaitfinancehouse" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-kuwaitfinancehouse" class="cors-label">
        <span class="online-image"><img style="height:18px;" src="{{ url('img/newcheckout/kfh-bank.png') }}" alt="Kuwait Finance House"></span>
        <span class="onlinebank-text">Kuwait Finance House</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_ocbc" id="input-ocbcbank" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-ocbcbank" class="cors-label">
        <span class="online-image"><img style="height:22px;" src="{{ url('img/newcheckout/ocbc-bank.png') }}" alt="OCBC bank"></span>
        <span class="onlinebank-text">OCBC Bank</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_scb" id="input-standardchartered" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-standardchartered" class="cors-label">
        <span class="online-image"><img style="height:22px;" src="{{ url('img/newcheckout/standard-chartered.png') }}" alt="Standard Chartered Bank"></span>
        <span class="onlinebank-text">Standard Chartered</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_uob" id="input-uobbank" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-uobbank" class="cors-label">
        <span class="online-image"><img style="height:20px;margin-left:-6px !important" src="{{ url('img/newcheckout/uob-bank.png') }}" alt="UOB Bank"></span>
        <span class="onlinebank-text">United Ovesea Bank</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_fpx_agrobank" id="input-agrobank" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-agrobank" class="cors-label">
        <span class="online-image"><img style="height:26px;margin-left: -3px !important;" src="{{ url('img/newcheckout/agro-bank.png') }}" alt="Agro Bank"></span>
        <span class="onlinebank-text">Agro Bank</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="revpay" id="input-unionpayraz" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-unionpayraz" class="cors-label">
        <span class="online-image"><img style="height:14px;" src="https://api.tmgrocer.com/img/newcheckout/logo-payment-unionpay.png" alt="Union Pay Raz"></span>
        <span class="onlinebank-text">Unionpay (Revpay)</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="revpay" id="input-revpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>
        <label for="input-revpay" class="cors-label">
        <span class="online-image"><img src="https://api.tmgrocer.com/img/newcheckout/FPX-logo.png" height="27px;" alt="revPay"></span>
        <span class="onlinebank-text">FPX (Revpay)</span>
        </label>
        </div>
        <!--<div class="inputGroup-payment">-->
        <!--<input type="radio" name="payopt" value="unionpay" id="input-unionpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>-->
        <!--<label for="input-unionpay" class="cors-label">-->
        <!--<span class="online-image"><img style="height:14px;" src="https://api.jocom.com.my/img/newcheckout/logo-payment-unionpay.png" alt="Union Pay"></span>-->
        <!--<span class="onlinebank-text">Unionpay(Rev Pay)</span>-->
        <!--</label>-->
        <!--</div>-->
        <!--<div class="inputGroup-payment">-->
        <!--<input type="radio" name="payopt" value="alipayravpay" id="input-alipayravpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>-->
        <!--<label for="input-alipayravpay" class="cors-label">-->
        <!--<span class="online-image"><img style="height:27px;" src="https://api.jocom.com.my/img/newcheckout/alipay-logo.png" alt="Alipay (Ravpay)"></span>-->
        <!--<span class="onlinebank-text">Alipay (RevPay)</span>-->
        <!--</label>-->
        <!--</div>-->
        
        
        
        
        
        
        
           <!--@if (Session::get('devicetype') == 'android')-->
                           
           <!--                 <?php if($trans_row->buyer_username == 'wiraizkandar' || $trans_row->buyer_username == 'maruthu') {?>   -->
           <!--                 <div class="col-md-4">-->
           <!--                         <input type="radio" name="payopt" value="molpay2" id="input-molpay">-->
           <!--                         <label lass="label-payment-img" for="input-molpay">-->
                                        <!--<img src="{{ url('img/newcheckout/molpay-logo.png') }}" alt="MOLPay">-->
           <!--                             <small style="margin-left: 6px;">-->
           <!--                                 <span><img style="height:20px;" src="{{ url('img/newcheckout/mastercard-logo.png') }}" alt="mastercard Pay"></span>-->
           <!--                                 <span><img style="height:40px;" src="{{ url('img/newcheckout/visa-logo.png') }}" alt="visa Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/FPX-logo.png') }}" alt="FPX Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/cimb-logo.png') }}" alt="cimb Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/maybank-logo.png') }}" alt="maybank Pay"></span>-->
           <!--                                 <span><img style="height:30px;" src="{{ url('img/newcheckout/razerpay-logo.png') }}" alt="alipay Pay"></span>-->
           <!--                             </small>-->
           <!--                         </label>-->
           <!--                     </div>-->
           <!--                 <?php }else{ ?>-->
           <!--                     <div class="col-md-4">-->
           <!--                         <input type="radio" name="payopt" value="molpay" id="input-molpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'disabled';}  else { echo 'checked';} ?>>-->
           <!--                         <label class="label-payment-img" for="input-molpay">-->
                                        <!--<img src="{{ url('img/newcheckout/razerpay-logo.png') }}" height="70" alt="Razer Pay">-->
                                        
                                        
           <!--                         </label>-->
           <!--                            <small style="margin-left: 6px;">-->
           <!--                                 <span><img style="height:20px;" src="{{ url('img/newcheckout/mastercard-logo.png') }}" alt="mastercard Pay"></span>-->
           <!--                                 <span><img style="height:40px;" src="{{ url('img/newcheckout/visa-logo.png') }}" alt="visa Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/FPX-logo.png') }}" alt="FPX Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/cimb-logo.png') }}" alt="cimb Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/maybank-logo.png') }}" alt="maybank Pay"></span>-->
           <!--                                 <span><img style="height:30px;" src="{{ url('img/newcheckout/razerpay-logo.png') }}" alt="razer pay"></span>-->
           <!--                             </small> -->
           <!--                     </div> -->
           <!--                 <?php } ?>-->
           <!--             @else-->
           <!--                 <div class="col-md-4">-->
           <!--                     <input type="radio" name="payopt" value="molpay2" id="input-molpayrazer" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled'; } else if ($ccard == 1) { echo 'disabled';} else { echo 'checked';}?>>-->
           <!--                     <label lass="label-payment-img" for="input-molpay">-->
                                    <!--<img src="{{ url('img/newcheckout/razerpay-logo.png') }}" alt="MOLPay">-->
           <!--                         <small style="margin-left: 6px;">-->
           <!--                                 <span><img style="height:20px;" src="{{ url('img/newcheckout/mastercard-logo.png') }}" alt="mastercard Pay"></span>-->
           <!--                                 <span><img style="height:40px;" src="{{ url('img/newcheckout/visa-logo.png') }}" alt="visa Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/FPX-logo.png') }}" alt="FPX Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/cimb-logo.png') }}" alt="cimb Pay"></span>-->
           <!--                                 <span><img style="height:50px;" src="{{ url('img/newcheckout/maybank-logo.png') }}" alt="maybank Pay"></span>-->
           <!--                                 <span><img style="height:30px;" src="{{ url('img/newcheckout/razerpay-logo.png') }}" alt="razer pay"></span>-->
           <!--                             </small>-->
           <!--                     </label>-->
           <!--                 </div>-->
                            <!--<div class="col-sm-4">-->
                            <!--    <input type="radio" name="payopt" value="bpoints" id="input-molpay">-->
                            <!--    <label class="label-payment-img" for="input-molpay">-->
                            <!--        <img src="{{ url('img/newcheckout/bcard2.png') }}" alt="BPoints">-->
                            <!--        <br><small>(BPoints)</small>-->
                            <!--    </label>-->
                            <!--</div>-->
           <!--             @endif-->
        </div>
      </div>
    </div>
    
    <div class="panels panel-default">
      <div class="panel-headings" role="tab" id="headingThree">
        <h4 class="panel-titles">
          <a role="button" data-toggle="collapse" class="a-link" data-parent="#accordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree" style="color:#024747;padding:10px;">
              <small style="margin-right: 4px;margin-left: 6px;" class="sidebar-logo">
                                        <span><img style="height:16px;" src="https://api.tmgrocer.com/img/newcheckout/pay-later.png" alt="Pay-later"></span>
                                        </small>
            <i class="more-less fa fa-angle-right" style="margin-top:2px;"></i>
            <b>Buy Now Pay Later</b>
          </a>
          
        </h4>
      </div>
      <div id="collapseThree" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingThree">
        <div class="panel-body" style="padding:0px !important">
                        
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_atome" id="input-atome" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1){ echo 'disabled'; } else if($tngpay == 1){ echo 'disabled'; } ?>>
            <label class="cors-label" for="input-atome">
        <span class="online-image"><img style="height:22px;" src="{{ url('img/newcheckout/atome-pay.png') }}" alt="atome"></span>
        <span class="onlinebank-text">Atome</span>
        </label>
        </div>
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="grab_pay" id="input-grabpay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1){ echo 'disabled';}?>>
            <label class="cors-label" for="input-grabpay">
        <span class="online-image"><img style="height:22px;" src="{{ url('img/newcheckout/grab-pay.png') }}" alt="Grab Pay"></span>
        <span class="onlinebank-text">GrabPay</span>
        </label>
        </div>
        <div class="inputGroup-payment">
            <input type="radio" name="payopt" value="pacepay" id="input-pacepay" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1){ echo 'disabled'; } else if($tngpay == 1){ echo 'disabled'; }  ?>>
            <label class="cors-label" for="input-pacepay">
        <span class="online-image"><img style="height:22px;" src="{{ url('img/newcheckout/pace-pay.png') }}" alt="Pace Pay"></span>
        <span class="onlinebank-text">Pace Pay</span>
        </label>
        </div>
                        
                          <?php  if($trans_row->buyer_username == 'maruthu' || $trans_row->buyer_username == 'maruthujocoms') {?>   
                            
                                <div class="inputGroup-payment">
                                    <input type="radio" name="payopt" value="favepay" id="input-favepay" <?php if($boost == 1 || $razerpay == 1){ echo 'disabled';}?>>
                                    <label class="cors-label" for="input-favepay">
                                        <span class="online-image"><img src="{{ url('img/newcheckout/fave.png') }}" height="35px;" alt="Fave Pay"></span>
                                        <span class="onlinebank-text">Fave Pay</span>
                                        
                                    </label>
                                </div>
                                
                                <div class="inputGroup-payment">
                                    <input type="radio" name="payopt" value="molpay4" id="input-molpayrazer">
                                    <label class="cors-label" for="input-molpayrazer">
                                       <span class="online-image"> <img src="{{ url('img/newcheckout/razerpay-logo.png') }}" alt="RazerPay"></span>
                                        <span class="onlinebank-text">Credit Card / Debit Card</span>
                                        
                                    </label>
                                    {{$molpay_url4}}
                                </div>
                                <div class="inputGroup-payment">
                                    <input type="radio" name="payopt" value="molpay5" id="input-molpayrazer">
                                    <label class="cors-label" for="input-molpayrazer">
                                        <span class="online-image"><img src="{{ url('img/newcheckout/Boost_Logo_White.png') }}" alt="Boost"></span>
                                        <span class="onlinebank-text">Boost Pay</span>
                                    </label>
                                </div>
                                <?php }?>
                                
        </div>
      </div>
    </div>
    
    <div class="panels panel-default">
      <div class="panel-headings" role="tab" id="headingFour">
        <h4 class="panel-titles">
          <a role="button" data-toggle="collapse" class="a-link" data-parent="#accordion" href="#collapseFour" aria-expanded="true" aria-controls="collapseFour" style="color:#024747;padding:12px;">
              <small style="margin-right: 4px;margin-left: 6px;" class="sidebar-logo">
                                        <span><img style="height:16px;" src="https://api.tmgrocer.com/img/newcheckout/e-wallet.png" alt="E-Wallet"></span>
                                        </small>
            <i class="more-less fa fa-angle-right" style="margin-top:1px;"></i>
            <b>e-Wallet</b>
          </a>
          
        </h4>
      </div>
      <div id="collapseFour" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingFour">
        <div class="panel-body" style="padding:0px !important">
                           <div class="inputGroup-payment">
                                    <input type="radio" name="payopt" value="touchngo" id="input-touchngo" <?php  if($tngpay == 1){ echo 'checked'; } else if ($boost == 1 || $razerpay == 1 || $flowboost == 1){ echo 'disabled'; } else if ($ccard == 1) { echo 'disabled';}?>>
                                    <label class="cors-label" for="input-touchngo">
                                        <span class="online-image"><img src="https://api.tmgrocer.com/img/newcheckout/logo-payment-touchngo.png" alt="Touchngo" height="22px;"></span>
                                      <span class="onlinebank-text">Touchn Go ( Revpay )</span>
                                    </label>
                                </div>
                            <div class="inputGroup-payment">
                                    <input type="radio" name="payopt" value="razer_tng_ewallet" id="input-touchngo-razer" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1 || $tngpay == 1){ echo 'disabled'; } ?>>
                                    <label class="cors-label" for="input-touchngo-razer">
                                        <span class="online-image"><img src="https://api.tmgrocer.com/img/newcheckout/logo-payment-touchngo.png" alt="Touchngo" height="22px;"></span>
                                      <span class="onlinebank-text">Touchn Go</span>
                                    </label>
                                </div>
                        
                        <div class="inputGroup-payment">
                                <input type="radio" name="payopt" value="grab_pay" id="input-grabpays" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1){ echo 'disabled';}?>>
                                <label class="cors-label" for="input-grabpays">
                                
                                    <span class="online-image"><img src="https://api.tmgrocer.com/img/newcheckout/grab-pay.png" height="22px;" alt="GrabPay"></span>
                                    <span class="onlinebank-text">Grab Pay ( Direct )</span>
                                </label>
                        </div>
                        <div class="inputGroup-payment">
                                <input type="radio" name="payopt" value="razer_grabpay" id="input-grabpay-razer" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1){ echo 'disabled';}?>>
                                <label class="cors-label" for="input-grabpay-razer">
                                
                                    <span class="online-image"><img src="https://api.tmgrocer.com/img/newcheckout/grab-pay.png" height="22px;" alt="GrabPay"></span>
                                    <span class="onlinebank-text">Grab Pay ( Razer )</span>
                                </label>
                        </div>
                        
                        <div class="inputGroup-payment">
                                <input type="radio" name="payopt" value="razer_shopeepay" id="input-shopeepay" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1 || $tngpay == 1){ echo 'disabled'; } ?>>
                                <label class="cors-label" for="input-shopeepay">
                                    <span class="online-image">
                                    <img src="https://api.tmgrocer.com/img/newcheckout/shopee-pay.png" alt="ShopeePay" style="height: 28px;"></span>
                             <span class="onlinebank-text">Shopee Pay</span>
                                </label>
                                </input>
                            </div>
                        <div class="inputGroup-payment">
                        <input type="radio" name="payopt" value="boost" id="input-boost" <?php if($boost == 1 || $flowboost == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';}else if($razerpay == 1){ echo 'disabled'; } ?>>
                        <label class="cors-label" for="input-boost">
                            <span class="online-image"><img src="{{ url('img/newcheckout/boost-pay.png') }}" alt="Boost" style="height: 24px;"></span>
                            <span class="onlinebank-text">Boost</span>
                         
                        </label>
                        </div>
        <!--                <div class="inputGroup-payment">-->
        <!--                <input type="radio" name="payopt" value="wechat" id="input-wechat" <?php if($boost == 1 || $flowboost == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if ($tngpay == 1) { echo 'disabled';} else if($razerpay == 1){ echo 'disabled'; } ?>>-->
        <!--                <label class="cors-label" for="input-wechat">-->
        <!--                    <span class="online-image"><img src="{{ url('img/newcheckout/wechat-pay.png') }}" alt="Wechat Pay" style="height: 20px;"></span>-->
        <!--                    <span class="onlinebank-text">Wechat Pay</span>-->
                         
        <!--                </label>-->
        <!--                </div>-->
        <!--                <div class="inputGroup-payment">-->
        <!--<input type="radio" name="payopt" value="alipay" id="input-alipay" <?php if($boost == 1 || $razerpay == 1 || $tngpay == 1 || $flowboost == 1){ echo 'disabled';} else if ($ccard == 1) { echo 'checked';} else { echo 'checked';}?>>-->
        <!--<label for="input-alipay" class="cors-label">-->
        <!--<span class="online-image"><img style="height:27px;" src="https://api.jocom.com.my/img/newcheckout/alipay-logo.png" alt="alipay Pay"></span>-->
        <!--<span class="onlinebank-text">Alipay</span>-->
        <!--</label>-->
        <!--</div>-->
        <div class="inputGroup-payment">
        <input type="radio" name="payopt" value="razer_mb2u_qrpay_push" id="input-maeewallet" <?php if($razerpay == 1){ echo 'checked'; } else if ($ccard == 1) { echo 'disabled';} else if($boost == 1 || $tngpay == 1){ echo 'disabled'; } ?>>
        <label for="input-maeewallet" class="cors-label">
        <span class="online-image"><img style="height:17px;" src="{{ url('img/newcheckout/m2u-wallet.png') }}" alt="M2U Bank E-Wallet"></span>
        <span class="onlinebank-text">Maybank2u</span>
        </label>
        </div>
                        
                    
        </div>
      </div>
    </div>
    
    
    
  </div>
   @else
                <input type="hidden" name="payopt" value="fullredeem">
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
                   
                    <?php if($trans_detail_group_query!=""){foreach ($trans_detail_group_query as $row): ?>
                        <?php if ((strlen($mpaysku) + strlen($row->product_name) + 1) < 1000) $mpaysku = $mpaysku.$row->product_name.'|'; ?>
                            <div class="list-group-item item">
                                <span class="pull-right">{{ $currency }} {{ number_format($group_product_price[$row->sku], 2, '.', '') }}</span>
                                <h3 class="panel-title">@if ($group_product_gst[$row->sku] > 0) ** @else * @endif {{ $row->product_name }}</h3>
                                SKU: {{ $row->sku }}<br>
                                Unit Price: {{ $currency }} {{ number_format($group_product_price[$row->sku] / $row->unit, 2, '.', '') }}<br>
                                Quantity: {{ $row->unit }}<br>
                            </div>
                    <?php endforeach;} ?>
                    
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
            @if (empty($coupon_code)||$jpoint_restrict=="0")
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
                            <div class="panel-body redemption">
                                <p>You have {{ $availablePoint }} {{ $point->type }} available ({{ $currency }} {{ number_format($availablePoint * $point->redeem_rate, 2) }} on tmGrocer) to shop with points.</p>
                                <form method="post" action="{{ url('v2/checkout/redemption') }}" id="point_redemption_{{ $count }}">
                                    <input type="hidden" name="transaction_id" value="{{ $transaction_id }}">
                                    <input type="hidden" name="lang" value="{{ $trans_row->lang }}">
                                    <input type="hidden" name="devicetype" value="{{ Session::get('devicetype') }}">
                                    <input type="hidden" name="type" value="{{ $point->point_type_id }}">
                                    <div class="input-group cus-div">
                                        <input type="text" class="form-control cus-input" id="point_amount_{{ $count }}" name="point" placeholder="Point">
                                        <span class="input-group-btn">
                                            <button class="btn btn-customs" type="submit" id="idredemption" >Redeem</button>
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
                        <!--        <form method="post" action="{{ url('v2/checkout/redemption') }}" id="point_redemption_{{ $count }}">-->
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
                                    <label for="myCheck" style="color:#024747">
                                        {{ $label_tnc1 }}
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTNC" aria-expanded="true" aria-controls="collapseOne">{{ $label_tnc2 }}</a>.
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="collapseTNC" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTNC">
                        <div class="panel-body" style="overflow: auto; -webkit-overflow-scrolling: touch; height: 150px;">
                            <article class="container" style=" height: 100%;width: 100%;padding: 0;margin: 0;font-family: arial;font-size: 12px;color: #7a7b7b;">
      <h2>Terms and Conditions</h2>
      <section>
        <h2>Introduction</h2>
        <p>Welcome to the tmGrocer mobile commerce Platform (the "Site"). These terms and conditions ("Terms and Conditions") apply to the Site, Tien Ming Distribution Sdn Bhd (1537285-T), and all of its divisions, subsidiaries, and affiliate operated Internet sites which reference these Terms and Conditions. “tmGrocer” means Tien Ming Distribution Sdn Bhd, a company incorporated in Malaysia under registration number 1537285-T and having its registered address at 10, Jalan Str 1, Saujana Teknologi Park, Rawang, 48000 Rawang, Malaysia.</p>
        <p>By accessing the Site, you confirm your understanding of the Terms and Conditions. If you do not agree to these Terms and Conditions of use, you shall not use this website. The Site reserves the right, to change, modify, add, or remove portions of these Terms and Conditions of use at any time. Changes will be effective when posted on the Site with no other notice provided. Please check these Terms and Conditions of use regularly for updates. Your continued use of the Site following the posting of changes to these Terms and Conditions of use constitutes your acceptance of those changes.</p>
      </section>
      <section>
        <h2>Use of the Site</h2>
        <p>We grant you a non-transferable and revocable license to use the Site, under the Terms and Conditions described, for the purpose of shopping for personal items sold on the Site. Commercial use or use on behalf of any third party is prohibited, except as explicitly permitted by us in advance. Any breach of these Terms and Conditions shall result in the immediate revocation of the license granted in this paragraph without notice to you.</p>
        <p>Content provided on this site is solely for informational purposes. Product representations expressed on this Site are those of the vendor and are not made by us. Submissions or opinions expressed on this Site are those of the individual posting such content and may not reflect our opinions.</p>
        <p>Certain services and related features that may be made available on the Site may require registration or subscription. Should you choose to register or subscribe for any such services or related features, you agree to provide accurate and current information about yourself, and to promptly update such information if there are any changes. Every user of the Site is solely responsible for keeping passwords and other account identifiers safe and secure. The account owner is entirely responsible for all activities that occur under such password or account. Furthermore, you must notify us of any unauthorized use of your password or account. The Site shall not be responsible or liable, directly or indirectly, in any way for any loss or damage of any kind incurred as a result of, or in connection with, your failure to comply with this section.</p>
        <p>During the registration process you agree to receive promotional emails from the Site. You can subsequently opt out of receiving such promotional e-mails by clicking on the link at the bottom of any promotional email.</p>
      </section>
      <section>
        <h2>User Submissions</h2>
        <p>Anything that you submit to the Site and/or provide to us, including but not limited to, questions, reviews, comments, and suggestions (collectively, "Submissions") will become our sole and exclusive property and shall not be returned to you. In addition to the rights applicable to any Submission, when you post comments or reviews to the Site, you also grant us the right to use the name that you submit, in connection with such review, comment, or other content. You shall not use a false e-mail address, pretend to be someone other than yourself or otherwise mislead us or third parties as to the origin of any Submissions. We may, but shall not be obligated to, remove or edit any Submissions.</p>
      </section>
      <section>
        <h2>Order Acceptance and Pricing</h2>
        <p>Please note that there are cases when an order cannot be processed for various reasons. The Site reserves the right to refuse or cancel any order for any reason at any given time. You may be asked to provide additional verifications or information, including but not limited to phone number and address, before we accept the order.</p>
        <p>We are determined to provide the most accurate pricing information on the Site to our users; however, errors may still occur, such as cases when the price of an item is not displayed correctly on the website. As such, we reserve the right to refuse or cancel any order. In the event that an item is mispriced, we may, at our own discretion, either contact you for instructions or cancel your order and notify you of such cancellation. We shall have the right to refuse or cancel any such orders whether or not the order has been confirmed and your credit card or bank account charged.</p>
      </section>
      <section>
        <h2>Trademarks and Copyrights</h2>
        <p>All intellectual property rights, whether registered or unregistered, in the Site, information content on the Site and all the website design, including, but not limited to, text, graphics, software, photos, video, music, sound, and their selection and arrangement, and all software compilations, underlying source code and software shall remain our property. The entire contents of the Site also are protected by copyright as a collective work under Malaysia copyright laws and international conventions. All rights are reserved.</p>
      </section>
      <section>
        <h2>Applicable Law and Jurisdiction</h2>
        <p>These Terms and Conditions shall be interpreted and governed by the laws in force in Malaysia. Subject to the Arbitration section below, each party hereby agrees to submit to the jurisdiction of the courts of Government of Malaysia to waive any objections based upon venue.</p>
      </section>
      <section>
        <h2>Arbitration</h2>
        <p>Any controversy, claim or dispute arising out of or relating to these Terms and Conditions will be referred to and finally settled by private and confidential binding arbitration before a single arbitrator held in Malaysia in English and governed by Malaysian law. The arbitrator shall be a person who is legally trained and who has experience in the information technology field in Malaysia and is independent of either party. Notwithstanding the foregoing, the Site reserves the right to pursue the protection of intellectual property rights and confidential information through injunctive or other equitable relief through the courts.</p>
      </section>
      <section>
        <h2>Termination</h2>
        <p>In addition to any other legal or equitable remedies, we may, without prior notice to you, immediately terminate the Terms and Conditions or revoke any or all of your rights granted under the Terms and Conditions. Upon any termination of this Agreement, you shall immediately cease all access to and use of the Site and we shall, in addition to any other legal or equitable remedies, immediately revoke all password(s) and account identification issued to you and deny your access to and use of this Site in whole or in part. Any termination of this agreement shall not affect the respective rights and obligations (including without limitation, payment obligations) of the parties arising before the date of termination. You furthermore agree that the Site shall not be liable to you or to any other person as a result of any such suspension or termination. If you are dissatisfied with the Site or with any terms, conditions, rules, policies, guidelines, or practices of Tien Ming Distribution Sdn Bhd (1537285-T), in operating the Site, your sole and exclusive remedy is to discontinue using the Site.</p>
      </section>
      <section>
        <h2>Terms of Use</h2>
        <ol>
          <li>
            <b>Interpretation</b>
            <ol>
              <li>In these Conditions:
                <ul>
                  <li>"Buyer" means the person who accepts a quotation of tmGrocer for the supply of Goods or who otherwise enters into a contract for the supply of Goods with tmGrocer;</li>
                  <li>"Conditions" mean the general terms and conditions set out in this document and (unless the context otherwise requires) any special terms and conditions agreed in writing between the Buyer and tmGrocer;</li>
                  <li>"Contract" means the contract for the purchase and sale of Goods, howsoever formed or concluded;</li>
                  <li>"Goods" means the goods (including any installment of the goods or any parts for them) which tmGrocer is to supply in accordance with a Contract;</li>
                  <li>"Writing" includes electronic mail facsimile transmission and any comparable means of communication.</li>
                  <li>“tmGrocer” means Tien Ming Distribution Sdn Bhd, a company incorporated in Malaysia under registration number (1537285-T) and having its registered address at 10, Jalan Str 1, Saujana Teknologi Park, Rawang, 48000 Rawang, Malaysia.</li>
                </ul>
              </li>
              <li>Any reference in these Conditions to any provision of a statute shall be construed as a reference to that provision as amended re-enacted or extended at the relevant time.</li>
              <li>The headings in these Conditions are for convenience only and shall not affect the interpretation of any parties.</li>
            </ol>
          </li>
          <li>
            <b>Basis of the Contract</b>
            <ol>
              <li>The supply of Goods by tmGrocer to the Buyer under any Contract shall be subjected to these Conditions which shall govern the Contract to the exclusion of any other terms and conditions contained or referred to in any documentation submitted by the Buyer or in correspondence or elsewhere or implied by trade custom practice or course of dealing.</li>
              <li>Any information made available in tmGrocer’s mobile commerce Platform connection with the supply of Goods, including photographs, drawings, data about the extent of the delivery, appearance, performance, dimensions, weight, consumption of operating materials, operating costs, are not binding and for information purposes only. In entering into the Contract the Buyer acknowledges that it does not rely on and waives any claim based on any such representations or information not so confirmed.</li>
              <li>No variation to these Conditions shall be binding unless agreed in writing between the authorised representatives of the Buyer and tmGrocer.</li>
              <li>Any typographical clerical or other error or omission in any quotation, invoice or other document or information issued by tmGrocer in its mobile commerce Platform shall be subjected to correction without any liability on the part of tmGrocer.</li>
            </ol>
          </li>
          <li>
            <b>Orders and Specifications</b>
            <ol>
              <li>Order acceptance and completion of the contract between the Buyer and tmGrocer will only be completed upon tmGrocer issuing a confirmation of dispatch of the Goods to the Buyer. For the avoidance of doubt, tmGrocer shall be entitled to refuse or cancel any order without giving any reasons for the same to the Buyer prior to issue of the confirmation of dispatch. tmGrocer shall furthermore be entitled to require the Buyer to furnish tmGrocer with contact and other verification information, including but not limited to address, contact numbers prior to issuing a confirmation of dispatch.</li>
              <li>No concluded Contract may be modified or cancelled by the Buyer except with the agreement in writing of tmGrocer and on terms that the Buyer shall indemnify tmGrocer in full against all loss (including loss of profit) costs (including the cost of all labour and materials used) damages charges and expenses incurred by tmGrocer as a result of the modification or cancellation, as the case may be.</li>
            </ol>
          </li>
          <li>
            <b>Price</b>
            <ul>
              <li>The price of the Goods and/or Services shall be the price stated in tmGrocer’s mobile commerce Platform at the time which the Buyer makes its offer purchase to tmGrocer. The price excludes the cost of packaging and delivery charges, any applicable goods and services tax, value added tax or similar tax which the Buyer shall be liable to pay to tmGrocer in addition to the price.</li>
            </ul>
          </li>
          <li>
            <b>Terms of Payment</b>
            <ol>
              <li>The Buyer shall be entitled to make payment for the Goods pursuant to the various payment methods set out in tmGrocer’s mobile commerce Platform. The terms and conditions applicable to each type of payment, as contained in tmGrocer's mobile commerce Platform, shall be applicable to the Contract.</li>
              <li>In addition to any additional terms contained in tmGrocer’s mobile commerce Platform, the following terms shall also apply to the following types of payment:
                <ol>
                  <li>Credit Card
                    <ul>
                      <li>Credit Card payment is possible for all Buyers. When the Buyer places an order with Credit Card on the tmGrocer mobile commerce Platform, the transaction is handled by PayPal. This system is certified and allows tmGrocer to accept payments such as Visa and MasterCard. All credit card numbers shall be protected by means of industry-leading encryption standards.</li>
                    </ul>
                  </li>
                </ol>
              </li>
              <li>If the Buyer fails to make any payment pursuant to the terms and conditions of the payment method elected, then without prejudice to any other right or remedy available to tmGrocer, tmGrocer shall be entitled to:
                <ol>
                  <li>cancel the Contract or suspend deliveries of the Goods until payment is made in full; and/or</li>
                  <li>charge the Buyer interest (both before and after any judgement) on the amount unpaid at the rate of one per cent (1.0%) per month until payment in full is made (a part of a month being treated as a full month for the purposes of calculating interest).</li>
                </ol>
              </li>
            </ol>
          </li>
          <li>
            <b>Delivery/Performance</b>
            <ol>
              <li>Delivery of the Goods shall be made to the address specified by the Buyer in its order.</li>
              <li>tmGrocer has the right at any time to sub-contract all or any of its obligations for the sale/delivery of the Goods to any other party as it may from time to time decide without giving notice of the same to the Buyer.</li>
              <li>Any dates quoted for delivery of the Goods are approximate only. The time for delivery/performance shall not be of the essence, and tmGrocer shall not be liable for any delay in delivery or performance howsoever caused.</li>
              <li>If tmGrocer has failed to deliver the Goods in accordance with the Contract or within a reasonable time, the Buyer shall be entitled, by serving written notice on tmGrocer, to demand performance within a specified time thereafter, which shall be at least 14 days. If tmGrocer fails to do so within the specified time, the Buyer shall be entitled to terminate the Contract in respect of the undelivered Goods and claim compensation for actual loss and expense sustained as a result of tmGrocer’s non-performance, which was foreseeable at the time of conclusion of the Contract and resulting from the usual course of events, subject always to the limitations set out in Condition 12.4.</li>
              <li>If the Buyer fails to take delivery of the Goods (otherwise than by reason of any cause beyond the Buyer's reasonable control or by reason of tmGrocer's fault) then without prejudice to any other right or remedy available to tmGrocer tmGrocer may:
                <ol>
                  <li>sell the Goods at the best price readily obtainable and (after deducting all reasonable storage and selling expenses) account to the Buyer for the excess over the price under the Contract provided the price has been paid in cleared funds in full or charge the Buyer for any shortfall below the price under the Contract; or</li>
                  <li>terminate the Contract and claim damages.</li>
                </ol>
              </li>
            </ol>
          </li>
          <li>
            <b>Risk and property of the Goods</b>
            <ol>
              <li>Risk of damage to or loss of the Goods shall pass to the Buyer at the time of delivery or if the Buyer wrongfully fails to take delivery of the Goods, the time when tmGrocer has tendered delivery of the Goods.</li>
              <li>Notwithstanding delivery and the passing of risk in the Goods or any other provision of these Conditions the property in the Goods shall not pass to the Buyer until tmGrocer has received cleared funds payment in full of the price of the Goods and all other goods agreed to be sold by tmGrocer to the Buyer for which payment is then due.</li>
              <li>Until such time as the property in the Goods passes to the Buyer, the Buyer shall hold the Goods as tmGrocer's fiduciary agent and bailee and shall keep the Goods separate from those of the Buyer.</li>
              <li>The Buyer agrees with tmGrocer that the Buyer shall immediately notify tmGrocer of any matter from time to time affecting tmGrocer’s title to the Goods and the Buyer shall provide tmGrocer with any in-formation relating to the Goods as tmGrocer may require from time to time.</li>
              <li>Until such time as the property in the Goods passes to the Buyer (and provided the Goods are still in existence and have not been resold) tmGrocer shall be entitled at any time to demand the Buyer to deliver up the Goods to tmGrocer and in the event of non-compliance tmGrocer reserves it’s right to take legal action against the Buyer for the delivery up the Goods and also reserves its right to seek damages and all other costs including but not limited to legal fees against the Buyer.</li>
              <li>The Buyer shall not be entitled to pledge or in any way charge by way of security for any indebtedness any of the Goods which remain the property of tmGrocer but if the Buyer does so all moneys owing by the Buyer to tmGrocer shall (without prejudice to any other right or remedy of tmGrocer) forthwith become due and payable.</li>
              <li>If the provisions in this Condition 7 are not effective according to the law of the country in which the Goods are located, the legal concept closest in nature to retention of title in that country shall be deemed to apply mutatis mutandis to give effect to the underlying intent expressed in this condition, and the Buyer shall take all steps necessary to give effect to the same.</li>
              <li>The Buyer shall indemnify tmGrocer against all loss damages costs expenses and legal fees incurred by the Buyer in connection with the assertion and enforcement of tmGrocer's rights under this condition.</li>
            </ol>
          </li>
          <li>
            <b>Warranties and Remedies</b>
            <ol>
              <li>Subject as expressly provided in these Conditions all other warranties conditions or terms, including those implied by statute or common law, are excluded to the fullest extent permitted by law.</li>
              <li>Subject to this Condition 8, tmGrocer warrants that the Goods will correspond with their specification at the time of delivery, and agrees to remedy any non-conformity therein for a period of 12 months commencing from the date on which the Goods are delivered or deemed to be delivered ("Warranty Period"). Where the Buyer is dealing as a consumer (within the meaning of the Sale of Goods Act and the Consumer Protection Act), tmGrocer further gives to the Buyer such implied warranties as cannot be excluded by law.
                <ol>
                  <li>tmGrocer’s above warranty concerning the Goods is given subject to the following conditions:
                    <ol>
                      <li>No condition is made or to be implied nor is any warranty given or to be implied as to the life or wear of the Goods supplied or that they will be suitable for any particular purpose or use under any specific conditions, notwithstanding that such purpose or conditions may be known or made known to tmGrocer.</li>
                      <li>Any description given of the Goods is given by way of identification only and the use of such description shall not constitute a sale by description.</li>
                      <li>tmGrocer binds itself only to deliver Goods in accordance with the general description under which they were sold, whether or not any special or particular description shall have been given or shall be implied by law. Any such special or particular description shall be taken only as the expression of tmGrocer's opinion in that behalf. tmGrocer does not give any warranty as to the quality state condition or fitness of the Goods.</li>
                      <li>tmGrocer shall be under no liability for the following measures and actions taken by the Buyer or third parties and the consequences thereof: improper remedy of defects, alteration of the Goods without the prior agreement of tmGrocer, addition and insertion of parts, in particular of spare parts which do not come from tmGrocer.</li>
                      <li>tmGrocer shall be under no liability in respect of any defect arising from unsuitable or improper use, defective installation or commissioning by the Buyer or third parties, fair wear and tear, wilful damage, negligence, abnormal working conditions, defective or negligent handling, improper maintenance, excessive load, unsuitable operating materials and replacement materials, poor work, unsuitable foundation, chemical, electro-technical/electronic or electric influences, failure to follow tmGrocer's instructions (whether oral or in writing) misuse or alteration or repair of the Goods without tmGrocer's approval.</li>
                      <li>tmGrocer is not liable for any loss damage or liability of any kind suffered by any third party directly or indirectly caused by repairs or remedial work carried out without tmGrocer’s prior written approval and the Buyer shall indemnify tmGrocer against each loss liability and cost arising out of such claims.</li>
                      <li>tmGrocer shall be under no liability under the above warranty (or any other warranty condition or guarantee) if the total price for the Goods has not been paid in cleared funds by the due date for payment.</li>
                      <li>tmGrocer shall be under no liability whatsoever in respect of any defect in the Goods arising after the expiry of the Warranty Period.</li>
                    </ol>
                  </li>
                  <li>Any claim by the Buyer which is based on any defect in the quality or condition of the Goods or their failure to correspond with specification shall be notified to tmGrocer within seven days from the date of receipt of the Goods or (where the defect or failure was not apparent on reasonable inspection) within a reasonable time after discovery of the defect or failure. During use, the Goods shall be monitored constantly with regard to safety and defects. If there are even slight reservations concerning the suitability for use or the slightest reservations concerning safety, the Goods must not be used. tmGrocer shall be given written notification immediately, specifying the reservations or the defect. However in no event shall the Buyer be entitled to reject the Goods on the basis of any defect or failure, except where the failure is such that the Goods delivered are of a fundamentally different nature than those which tmGrocer had contracted to deliver.</li>
                  <li>If the Buyer does not give due notification to tmGrocer in accordance with the Condition 8.2.2, tmGrocer shall have no liability for any defect or failure or for any consequences resulting therefrom. Where any valid claim in respect of any of the Goods which is based on any defect in the quality or condition of the Goods or their failure to meet a specification is notified to tmGrocer in accordance with Condition 8.2.2, the non-conforming Goods (or part thereof) will be repaired or replaced free of charge as originally ordered. Where the Goods have not been repaired or replaced within a reasonable time, despite a written warning from the Buyer, the Buyer shall be entitled to a reduction of the price in proportion to the reduced value of the Goods, provided that under no circumstance shall such reduction exceed 15% of the price of the affected Goods. In lieu of repair or replacement, tmGrocer may, at its sole discretion, grant such a reduction to the Buyer. Upon a repair, replacement or price reduction being made as aforesaid, the Buyer shall have no further claim against tmGrocer.</li>
                  <li>When tmGrocer has provided replacement Goods or given the Buyer a refund, the non-conforming Goods or parts thereof shall become tmGrocer’s property.</li>
                </ol>
              </li>
              <li></li>
            </ol>
          </li>
          <li>
            <b>Force Majeure</b>
            <ol>
              <li>tmGrocer shall not be liable to the Buyer or be deemed to be in breach of the Contract by reason of any delay in performing or any failure to perform any of tmGrocer's obligations if the delay or failure was due to any cause beyond tmGrocer's reasonable control. Without prejudice to the generality of the foregoing the following shall be regarded as causes beyond tmGrocer's reasonable control:
                <ol>
                  <li>Act of God, explosion, flood, tempest, fire or accident;</li>
                  <li>war or threat of war, sabotage, insurrection, civil disturbance or requisition;</li>
                  <li>acts of restrictions, regulations, bye-laws, prohibitions or measures of any kind on the part of any governmental parliamentary or local authority;</li>
                  <li>import or export regulations or embargoes;</li>
                  <li>interruption of traffic, strikes, lock-outs, other industrial actions or trade disputes (whether involving employees of tmGrocer or of a third party);</li>
                  <li>interruption of production or operation, difficulties in obtaining raw materials labour fuel parts or machinery;</li>
                  <li>power failure or breakdown in machinery.</li>
                </ol>
              </li>
              <li>Upon the happening of any one of the events set out in Condition 9.1 tmGrocer may at its option:
                <ol>
                  <li>fully or partially suspend delivery/performance while such event or circumstances continues;</li>
                  <li>terminate any Contract so affected with immediate effect by written notice to the Buyer and tmGrocer shall not be liable for any loss or damage suffered by the Buyer as a result thereof.</li>
                </ol>
              </li>
            </ol>
          </li>
          <li>
            <b>Insolvency of Buyer</b>
            <ol>
              <li>This condition applies if:
                <ol>
                  <li>the Buyer makes any voluntary arrangement with its creditors or becomes subject to an administration order or (being an individual or firm) becomes bankrupt or (being a company) goes into liquidation (otherwise than for the purposes of amalgamation or reconstruction); or</li>
                  <li>an encumbrancer takes possession or a receiver is appointed of any of the property or assets of the Buyer; or</li>
                  <li>the Buyer ceases - or threatens to cease - to carry on business; or</li>
                  <li>tmGrocer reasonably apprehends that any of the events mentioned above is about to occur in relation to the Buyer and notifies the Buyer accordingly.</li>
                </ol>
              </li>
              <li>If this condition applies then without prejudice to any other right or remedy available to tmGrocer, tmGrocer shall be entitled to cancel the Contract or suspend any further delivery/performance under the Contract without any liability to the Buyer and if Goods have been delivered but not paid for the price shall become immediately due and payable notwithstanding any previous agreement or arrangement to the contrary.</li>
            </ol>
          </li>
          <li>
            <b>Notices</b>
            <ul>
              <li>Any notice required or permitted to be given by either party to the other under these Conditions shall be in writing addressed, if to tmGrocer, to its registered office or principal place of business and if to the Buyer, to the address stipulated in the relevant offer to purchase.</li>
            </ul>
          </li>
          <li>
            <b>Liability</b>
            <ol>
              <li>tmGrocer shall accept liability to the Buyer for death or injury resulting from its own or that of its employees' negligence. Save as aforesaid, tmGrocer’s liability under or in connection with the Contract shall be subject to the limitations set out in this Condition 12.</li>
              <li>tmGrocer shall be under no liability whatsoever where this arises from a reason beyond its reasonable control as provided in Condition 9 or from an act or default of the Buyer.</li>
              <li>In no event shall tmGrocer be liable for loss of profit or goodwill, loss of production or revenue or any type of special indirect or consequential loss whatsoever (including loss or damage suffered by the Buyer as a result of an action brought by a third party) even if such loss were reasonably foreseeable or tmGrocer had been advised of the possibility of the Buyer incurring the same.</li>
              <li>Where time of performance has been agreed by tmGrocer becomes the essence of the Contract by means of notice by the Buyer to tmGrocer, as provided for in Clause 6.4, and tmGrocer fails to comply with its obligations in due time, so that the Buyer becomes entitled to compensation in accordance with Condition 6.4, tmGrocer’s liability shall be limited to an amount of ½% for each full week of delay, in total to a maximum cumulative amount of 5%, of the value of the delayed Goods.</li>
              <li>The remedies set out in Condition 8 are the Buyer’s sole and exclusive remedies for non-conformity of or defects in the Goods or Services and tmGrocer’s liability for the same shall be limited in the manner specified in Condition 8.</li>
              <li>Without prejudice to the sub-limits of liability applicable under this Condition 12 or elsewhere in these Conditions, tmGrocer’s maximum and cumulative total liability (including any liability for acts and omissions of its employees agents and sub-contractors) in respect of any and all claims for defective performance, breach of contract, compensation, indemnity, tort, misrepresentation, negligence at law or equity and any other damages or losses which may arise in connection with its performance or non-performance under the Contract, shall not exceed the total Contract price.</li>
              <li>If a number of events give rise substantially to the same loss they shall be regarded as giving rise to only one claim under these Conditions.</li>
              <li>No action shall be brought by tmGrocer later than 12 months after the date it became aware of the circumstances giving rise to a claim or the date when it ought reasonably to have become aware, and in any event, no later than 12 months after the end of the Warranty Period.</li>
            </ol>
          </li>
          <li>
            <b>Termination</b>
            <ol>
              <li>On or at any time after the occurrence of any of the events in condition 13.2 tmGrocer may stop any Goods in transit, suspend further deliveries to the Buyer and exercise its rights under Condition 7 and/or terminate the Contract with the Buyer with immediate effect by written notice to the Buyer.</li>
              <li>
                The events are:
                <ol>
                  <li>the Buyer being in breach of an obligation under the Contract;</li>
                  <li>the Buyer passing a resolution for its winding up or a court of competent jurisdiction making an order for the Buyer’s winding up or dissolution;</li>
                  <li>the making of an administration order in relation to the Buyer or the appointment of a receiver over or an encumbrancer taking possession of or selling any of the Buyer’s assets;</li>
                </ol>
              </li>
              <li>the Buyer making an arrangement or composition with its creditors generally or applying to a Court of competent jurisdiction for protection from its creditors.</li>
            </ol>
          </li>
          <li>
            <b>Returns Of Goods</b>
            <ol>
              <li>tmGrocer will accept returns or refunds of Goods on the following cases:
                <ol>
                  <li>Faulty Goods</li>
                  <li>Damaged Goods</li>
                  <li>Incorrect products</li>
                  <li>Customer’s convenience (as long as it is within tmGrocer's return policy)</li>
                </ol>
              </li>
              <li>There are three types of Goods return:
                <ol>
                  <li>For delivery failures</li>
                  <li>Unopened returns - For items in whose categories tmGrocer offers a return policy and for items with visible damages</li>
                  <li>Opened returns - for defective, void and expired, damages and for categories where tmGrocer offers an opened returns policy</li>
                </ol>
              </li>
              <li>The return or refund should be within period of time on the following cases:
                <ol>
                  <li>Perishable goods (e.g. fruits, vegetables) - Hand back to the driver at the time of delivery</li>
                  <li>a.  Non-perishable goods (e.g. kitchen, laundry products) - Hand back to the driver at the time of delivery or within 48 hours.</li>
                </ol>
              </li>
              <li>Due to the reason of refund condition the refund has to be made within 7 days or the next bill with tmGrocer.</li>
              <li>Shipping costs must be borne by the tmGrocer should the reason of the return be in the case of faulty, damaged, or incorrect goods. </li>
              <li>Shipping costs must be borne by the Customer should the reason of the return be in the case of customer’s convenience (as long as it is within tmGrocer’s refund & return policy), the return shipping costs shall be borne by the Customer.</li>
              <li>tmGrocer agrees to release, defend, protect, indemnify and hold customer harmless from and against any costs, expenses, fines, penalties, losses, damages, and liabilities arising from any above mentioned situations.</li>
            </ol>
          </li>
          <li>
            <b>General</b>
            <ol>
              <li>Unless the context otherwise requires, any term or expression which is defined in or given a particular meaning by the provisions of Incoterms shall have the same meaning in these Conditions but if there is any conflict between the provisions of Incoterms and these Conditions, the latter shall prevail.</li>
              <li>No waiver by tmGrocer of any breach of the Contract by the Buyer shall be considered as a waiver of any subsequent breach of the same or any other provision.</li>
              <li>If any provision of these Conditions is held by any competent authority to be invalid or unenforceable in whole or in part the validity of the other provisions of these Conditions and the remainder of the provision in question shall not be affected thereby.</li>
              <li>No person who is not a party to this Contract (including any employee officer agent representative or sub-contractor of either party) shall have any right under the Contracts (Rights of Third Parties) Act to enforce any terms of this Contract which expressly or by implication confers a benefit on that person without the express prior agreement in writing of the parties, which the agreement must refer to Condition 3.2.</li>
              <li>The Contract shall be governed by the laws of Malaysia and the Buyer agrees to submit to the non-exclusive jurisdiction of the Courts in Malaysia, as provided for in Clause 15.7.</li>
              <li>Except as provided for in Clause 15.7, any dispute, controversy or claim arising out of or relating to this contract, or the breach, termination or invalidity thereof shall be settled by arbitration in accordance with the Rules for Arbitration of the Kuala Lumpur Regional Centre for Arbitration (KLR-CA). The arbitral tribunal shall consist of a sole arbitrator, to be appointed by the Chairman of the KLRCA. The place of arbitration shall be Kuala Lumpur. Any award by the arbitration tribunal shall be final and binding upon the parties.</li>
              <li>Notwithstanding Clause 15.6, tmGrocer shall be entitled to commence court legal proceedings for the purposes of protecting its intellectual property rights and confidential information by means of injunctive or other equitable relief.</li>
              <li>The United Nations Convention on Contracts for the International Sale of Goods shall not apply to any Contract for the sale of Goods.</li>
              <li>tmGrocer reserves their right to these terms and conditions of sale at any time.</li>
            </ol>
          </li>
        </ol>
      </section>
      <section>
          <h2>TPoint</h2>
          <ol>
              <li>In these Terms and Conditions, unless the context otherwise requires, the following words and expressions shall have the following meanings: “TPoint” or “TPoint (s)” means the points awarded to Members for purchases and redemption of Rewards at participating Merchants’ outlets at tmGrocer platform; “Rewards” means the products, services, rewards, gifts or other benefits made available by tmGrocer may be redeemed by Members;</li>
              <li>These Terms and Conditions (including the Policy on Privacy and Data Protection) govern the award and use of Points by Members, and set out the terms of the agreement between tmGrocer and each Member with regards to the Reward. A person can sign up and register at tmGrocer can then earn Points on various purchases at tmGrocer platform. By applying to register with tmGrocer, a Member is deemed to have accepted these Terms and Conditions. tmGrocer may, in its sole discretion decide and without the need to assign any reason, refuse an application made by any person to be a Member.</li>
              <li>tmGrocer reserves the right to amend these Terms and Conditions at any time and from time to time as it deems fit at its absolute discretion and without prior notification to Members. Earning or redeeming Points by Members will constitute acceptance of the amended Terms and Conditions. Failure to observe the Terms and Conditions stated herein by a Member may result in termination of Membership.</li>
              <li>tmGrocer reserves the right to, at any time, vary or terminate the Reward or any privileges by withdrawing the TPoint(s) from use without prior notification to Members and without being liable in any way to Members. tmGrocer may, at its sole discretion, remove any or all Members from the Program at any time.</li>
              <li>The Program is operated by:
                  <ul>
                      <li>Tien Ming Distribution Sdn Bhd (1537285-T)<br>
                      10, Jalan Str 1, Saujana Teknologi Park, <br>
                      Rawang, 48000 Rawang, Malaysia.<br>
                      Email: <a href="mailto:enquiries@tmgrocer.com">enquiries@tmgrocer.com</a><br>
                      Call Centre: <a href="tel:0367348744">03 6734 8744</a> </li>
                  </ul>
              </li>
              <li>Membership is open to individuals who are 18 years of age and above. Residents and non-residents of Malaysia may apply for membership.</li>
              <li>A Member may use TPoint(s) only in tmGrocer platform or at such places or on such items as tmGrocer may specify from time to time.</li>
              <li>No annual fee will be charged for the Membership.</li>
              <li>POINT ACCUMULATION WILL BEGIN FROM NIL. Members are advised to keep all online or printed invoice for at least 6 months of each qualifying period in the event of discrepancies in the accumulated Points.</li>
              <li>Points expire 36 months after issuance on a first in first out basis. Points are not transferable.</li>
              <li>Points will be awarded at the rate agreed by tmGrocer when a Member purchases goods at tmGrocer platform. tmGrocer may alter the method and rate at which Points are awarded at its discretion from time to time. Each Point is equivalent to the value of RM0.01 (1 Sen) or such other value as tmGrocer may revise from time to time.</li>
              <li>A Member may redeem some or all his Points for Rewards subject to the Member complying with the procedures for redemption. Points may be redeemed at the tmGrocer platform, through the tmGrocer Application. Redemption of points will be processed after the application details for redemption has been received by tmGrocer. Once redemption has been accepted by tmGrocer, it cannot be cancelled, exchanged or returned. Points cannot be exchanged for cash and can only be used for redemption of Rewards. Rewards may be redeemed by a Member using points or a combination of points plus cash/ vouchers. No points will be issued if redemption is made by a Member online through the Website. On confirmation of redemption, points redeemed will be deducted from the Member’s account and if applicable, additional point’s equivalent to the value of Rewards will be added to the Member’s account. All Rewards are subject to availability and further subject to all applicable legal rules and the terms and conditions (including booking requirements, cancellation restrictions, return conditions, warranties and limitations of liability) as imposed by tmGrocer. tmGrocer makes no representation or warranty of any kind (whether express or implied) with regards to the condition, fitness for purposes, merchantable quality or otherwise of any Rewards redeemed. tmGrocer shall not however be responsible for any failure or delay by a third party to supply such Reward, or loss or damage to such Reward during delivery. Points cannot be redeemed until they are credited into the Membership account of the Member. Points will be recorded in the Member’s account only after the tmGrocer has notified the details of the relevant transaction which Points are issued.</li>
              <li>tmGrocer may, at its discretion, replace or substitute any advertised Reward with a similar Reward.</li>
              <li>A Member may check his Points online at the tmGrocer Application.</li>
              <li>In the event of a failure or breakdown of any equipment or system used in connection with the Reward, tmGrocer may refuse request for redemption or to award Points on any transaction. tmGrocer shall not be responsible or liable in any manner in the event Points are not awarded or redemption cannot be made or a Member is unable to check his Points, due to any failure in the equipment or system used in connection with the Reward.</li>
              <li>The use of the tmGrocer Application is at the Members’ risk. Members are responsible for the security of their user login and password. tmGrocer accepts no liability for the disclosure of the user login or password by the Member to a third party, whether intentionally or not. tmGrocer reserves the right to block a Member from accessing his account online if tmGrocer has reasonable grounds to suspect that fraud or misconduct has been committed by the Member. While tmGrocer uses reasonable efforts to include up to date information in the tmGrocer Application and in all its publications, tmGrocer makes no warranties or representations as to their accuracy, reliability, completeness or otherwise. The contents, materials, products or other services available in tmGrocer’s publications or accessible through the tmGrocer Application are on “as is” and “as available” basis. tmGrocer disclaims all warranties (express or implied) including but not limited to, fitness for purpose and non-infringement, in relation to the contents, materials, products or other services published in any of its publications or available on the tmGrocer Application. tmGrocer does not warrant that the tmGrocer Application will be error-free, free of viruses, bugs or other harmful components or access to the Application will be uninterrupted. Members are responsible to implement security measures in their device before accessing the Application. tmGrocer shall not be liable in any way for any direct, indirect, punitive, incidental, consequential or other damages howsoever arising out of (i) the use of, or access to, the Application; or (ii) delay or inability to use or access the Application; or (iii) for any content, information, material, products or services published in, posted on, advertised in or obtained through tmGrocer’s publications or the Application.</li>
              <li>The conditions of use stated on the reverse of the TPoint form part of the Terms and Conditions herein and in the event of any conflict, the Terms and Conditions contained herein shall prevail.</li>
              <li>The Points balance at the time the email was blocked may (at the sole discretion of tmGrocer) be transferred to the new tmGrocer account.  Customer may call tmGrocer customer service/technical support for assistance.</li>
              <li>Notification of any matter in relation to the reward shall be deemed given to Members if it is made via any one of the methods below:
                  <ol>
                      <li>by posting on the Website; or</li>
                      <li>by sending an email to Members who have provided email address to tmGrocer; or</li>
                      <li>by publication in a newspaper; or</li>
                      <li>sending by ordinary post to the last known address of Members appearing in tmGrocer’s records.</li>
                      <li>by notification through tmGrocer Application</li>
                  </ol>
              </li>
              <li>If tmGrocer sells or transfers the Program to another party, tmGrocer may transfer all of tmGrocer’s rights and obligations under these Terms and Conditions without any consent from any Member. tmGrocer may further disclose and transfer all and any information and data which tmGrocer holds or which resides in the system of tmGrocer in relation to the Members and all transactions made by Members, including purchase and redemption transactions (“Information”) to the new transferee, or disclose any such Information to a prospective new buyer. The Member hereby unconditionally and irrevocably agrees to such transfer and disclosure of the Information to the new transferee or the prospective new buyer.</li>
              <li>tmGrocer will only be liable to a Member (and not any other third party) who suffers loss in connection with the Reward arising from Points being wrongly deducted or non-credit of Points entitled by a Member and in such a case, tmGrocer’s sole liability will be limited to crediting to the relevant Member’s account such Points which have been wrongly deducted or should have been credited but were not. tmGrocer shall not be responsible where: (i) there is no breach of a legal duty of care owed to such Member by tmGrocer or by any of tmGrocer’s employees, staffs, authorized personnel or agents; or (ii) such loss or damage is not a reasonably foreseeable result of any such breach at the time tmGrocer enters into this agreement with such Member; or (iii) any increase in loss or damage resulting from breach by such Member of the Reward.</li>
              <li>tmGrocer is not responsible or liable to the Members for indirect, consequential or economic losses, loss of profits, loss of opportunity or punitive damages of any kind.</li>
              <li>tmGrocer may further establish rules, procedures and policies in relation to any matter regarding the Reward, all of which shall form part of the Terms and Conditions. These Terms and Conditions as set out herein shall prevail in the event of any conflict or inconsistency with any other documents, statements, rules, procedures, policies or communications, issued by tmGrocer, including FAQ, advertising or promotional materials.</li>
              <li>These Terms and Conditions are governed by the laws of Malaysia and Members shall submit to the exclusive jurisdiction of the courts of Malaysia</li>
              <li>tmGrocer maintains a call centre for enquiries from Members. If you have any enquiries please contact the call centre number notified on the Website from time to time.</li>
              <li>Some materials on the Website, tmGrocer Application and other tmGrocer promotional materials are the intellectual property of tmGrocer, or other third parties. Members have no right to use such intellectual property.</li>
              <li>Each exclusion or limitation of liability in these Terms and Conditions shall also apply for the benefit of each of the tmGrocer and their employees or agents.</li>
          </ol>
      </section>
    </article>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" id="btn-checkout" class="btn btn-lg btn-block btn-place-order" onclick="return paypalSubmit();">Place Order</button> <br><br><br><br><br><br>
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
                }
                else if($('[name="payopt"]:checked').val() == 'molpay2') {
                    document.getElementById('molpay_checkout2').submit();
                }else if($('[name="payopt"]:checked').val() == 'razer_credit') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_credit}}';
                    if(gateway=='razer_credit'){
                    $("#channel").val('credit');
                    $('#razer_desc').val('Payment for tmGrocer Visa / MasterCard');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_mb2u') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_mb2u}}';
                    if(gateway=='razer_mb2u'){
                    $("#channel").val('MB2U');
                    $('#razer_desc').val('Payment for tmGrocer From FPX MB2U');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_cimbclicks') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_cimbclicks}}';
                    if(gateway=='razer_cimbclicks'){
                    $("#channel").val('CIMBCLICKS');
                    $('#razer_desc').val('Payment for tmGrocer From FPX CIMBCLICKS');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_pbb') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_pbb}}';
                    if(gateway=='razer_pbb'){
                    $("#channel").val('PBB');
                    $('#razer_desc').val('Payment for tmGrocer From FPX PBB');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_hlbconnect') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_hlbconnect}}';
                    if(gateway=='razer_hlbconnect'){
                    $("#channel").val('HLBConnect');
                    $('#razer_desc').val('Payment for tmGrocer From FPX HLBConnect');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_rhbnow') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_rhbnow}}';
                    if(gateway=='razer_rhbnow'){
                    $("#channel").val('RHBNow');
                    $('#razer_desc').val('Payment for tmGrocer From FPX RHBNow');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_bimb') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_bimb}}';
                    if(gateway=='razer_bimb'){
                    $("#channel").val('BIMB');
                    $('#razer_desc').val('Payment for tmGrocer From FPX BIMB');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_bankrakyat') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_bankrakyat}}';
                    if(gateway=='razer_bankrakyat'){
                    $("#channel").val('bankrakyat');
                    $('#razer_desc').val('Payment for tmGrocer From FPX Bankrakyat');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_bankmuamalat') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_bankmuamalat}}';
                    if(gateway=='razer_bankmuamalat'){
                    $("#channel").val('bankmuamalat');
                    $('#razer_desc').val('Payment for tmGrocer From FPX bankmuamalat');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_bsn') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_bsn}}';
                    if(gateway=='razer_fpx_bsn'){
                    $("#channel").val('FPX_BSN');
                    $('#razer_desc').val('Payment for tmGrocer From FPX BSN');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_abb') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_abb}}';
                    if(gateway=='razer_fpx_abb'){
                    $("#channel").val('FPX_ABB');
                    $('#razer_desc').val('Payment for tmGrocer From FPX ABB');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_abmb') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_abmb}}';
                    if(gateway=='razer_fpx_abmb'){
                    $("#channel").val('FPX_ABMB');
                    $('#razer_desc').val('Payment for tmGrocer From FPX ABMB');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_amonline') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_amonline}}';
                    if(gateway=='razer_amonline'){
                    $("#channel").val('AMOnline');
                    $('#razer_desc').val('Payment for tmGrocer From FPX AMOnline');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_hsbc') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_hsbc}}';
                    if(gateway=='razer_fpx_hsbc'){
                    $("#channel").val('FPX_HSBC');
                    $('#razer_desc').val('Payment for tmGrocer From FPX HSBC');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_kfh') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_kfh}}';
                    if(gateway=='razer_fpx_kfh'){
                    $("#channel").val('FPX_KFH');
                    $('#razer_desc').val('Payment for tmGrocer From FPX KFH');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_ocbc') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_ocbc}}';
                    if(gateway=='razer_fpx_ocbc'){
                    $("#channel").val('FPX_OCBC');
                    $('#razer_desc').val('Payment for tmGrocer From FPX OCBC');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_scb') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_scb}}';
                    if(gateway=='razer_fpx_scb'){
                    $("#channel").val('FPX_SCB');
                    $('#razer_desc').val('Payment for tmGrocer From FPX SCB');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_uob') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_uob}}';
                    if(gateway=='razer_fpx_uob'){
                    $("#channel").val('FPX_UOB');
                    $('#razer_desc').val('Payment for tmGrocer From FPX UOB');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_fpx_agrobank') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_fpx_agrobank}}';
                    if(gateway=='razer_fpx_agrobank'){
                    $("#channel").val('FPX_AGROBANK');
                    $('#razer_desc').val('Payment for tmGrocer From FPX AGROBANK');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_tng_ewallet') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_tng_ewallet}}';
                    if(gateway=='razer_tng_ewallet'){
                    $("#channel").val('TNG-EWALLET');
                    $('#razer_desc').val('Payment for tmGrocer From FPX TNG-EWALLET');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_grabpay') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_grabpay}}';
                    if(gateway=='razer_grabpay'){
                    $("#channel").val('GrabPay');
                    $('#razer_desc').val('Payment for tmGrocer From FPX GrabPay');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'razer_mb2u_qrpay_push') {
                    var gateway=$('[name="payopt"]:checked').val();
                    var action='{{$razer_mb2u_qrpay_push}}';
                    if(gateway=='razer_mb2u_qrpay_push'){
                    $("#channel").val('MB2U_QRPay-Push');
                    $('#razer_desc').val('Payment for tmGrocer From FPX MB2U_QRPay');
                    $("#razer_checkout").attr("action", action);
                     document.getElementById('razer_checkout').submit();   
                    }
                }else if($('[name="payopt"]:checked').val() == 'molpay3') {
                    document.getElementById('molpay_checkout3').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay4') {
                    document.getElementById('molpay_checkout4').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay5') {
                    document.getElementById('molpay_checkout5').submit();
                } else if($('[name="payopt"]:checked').val() == 'molpay6') {
                    document.getElementById('molpay_checkout6').submit();
                } else if($('[name="payopt"]:checked').val() == 'razer_atome') {
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
                } else if($('[name="payopt"]:checked').val() == 'razer_shopeepay') {
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
        <form id="razer_checkout" action="" method="post" style="display: none;">
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
            <input type="hidden" name="bill_desc" id="razer_desc" value="">
            <input type="hidden" name="channel" value="" id="channel">
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
        <form id="fullredeem_checkout" method="GET" action="{{ url('v2/checkout/point') }}" style="display: none;">
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
        <form id="shopeepay_checkout" action="{{ isset($razer_shopeepay) ? $razer_shopeepay : '' }}" method="post" style="display: none;">
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
            <input type="hidden" name="channel" value="ShopeePay">
            
        </form>
        <form id="molpay_checkout8" action="{{ isset($razer_atome) ? $razer_atome : '' }}" method="post" style="display: none;">
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
            <input type="hidden" name="channel" value="Atome">
            
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
        
        <form id="jocom_checkout" action="https://dev.jocom.com.my/test" method="get" style="display: none;">
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
  <script type="text/javascript">
  
       function checkboxChecked(jsonResponse) {
        $('#dynamic_address').html(''+jsonResponse.deliveradd1+'<br>'+jsonResponse.deliveradd2+'<br>'+jsonResponse.deliverpostcode+','+jsonResponse.city_name+'<br>'+jsonResponse.country_name+'');
        $('#deivery_recept').html(''+jsonResponse.delivername+'<br>'+jsonResponse.delivercontactno+'');
        
        }
        function toggleIcon(e) {
        $(e.target)
            .prev('.panel-headings')
            .find(".more-less")
            .toggleClass('fa fa-angle-right fa fa-angle-down');
    }
    $('.panel-group').on('hidden.bs.collapse', toggleIcon);
    $('.panel-group').on('shown.bs.collapse', toggleIcon);
        $(document ).ready(function() {

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
$('.saveaddress').on('click',function(){
     var addr_id=$(this).val();  
     var main_id=$(this).data('value');
     var trns_id={{ $transaction_id }};
     var deliveradd1=$('#deliveradd1id'+main_id).val();
     var deliveradd2=$('#deliveradd2id'+main_id).val();
     var deliverpostcode=$('#deliverpostcodeid'+main_id).val();
     var city_name=$('#city_nameid'+main_id).val();
     var country_name=$('#country_nameid'+main_id).val();
     var delivername=$('#delivernameid'+main_id).val();
     var delivercontactno=$('#delivercontactnoid'+main_id).val();
     $('#fav_id_address').val(addr_id);
     document.getElementById('fav_address_form').submit();
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