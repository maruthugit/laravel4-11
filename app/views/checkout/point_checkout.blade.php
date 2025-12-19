<?php

$transaction        = $trans_query;
$transactionDetails = $trans_detail_query;
$currency           = Fees::get_currency();

switch ($transaction->lang) {
    case 'CN':
        $label_tran     = "交易号";
        $label_coupon   = "优惠代码";
        $label_payment  = "付款方式";
        $label_add      = "收件地址";
        $label_contact  = "收件人电话号码";
        $label_msg      = "特别讯息";
        $label_process  = "处理费";
        $label_subtotal = "小计";
        $label_delivery = "运输费";
        $label_gst      = "消费税";
        $label_total    = "总额";
        $label_tnc1     = "本人同意所有的";
        $label_tnc2     = "服务条款";
        $label_tnc3     = "关闭";
        $label_tnc4     = "请同意服务条款才能继续";
        break;
    case 'MY':
        $label_tran     = "ID Transaksi";
        $label_coupon   = "Kod Kupon";
        $label_payment  = "Pilihan Pembayaran";
        $label_add      = "Alamat Penghantaran";
        $label_contact  = "Penerima";
        $label_msg      = "Mesej Khas";
        $label_process  = "Caj Pemprosesan";
        $label_subtotal = "Jumlah";
        $label_delivery = "Kos Penghantaran";
        $label_gst      = "GST";
        $label_total    = "Jumlah Keseluruhan";
        $label_tnc1     = "Saya setuju dengan";
        $label_tnc2     = "Terma & Syarat";
        $label_tnc3     = "Tutup";
        $label_tnc4     = "Sila akui Terma & Syarat untuk menerus.";
        break;
    case 'EN':
    default:
        $label_tran     = "Transaction ID";
        $label_coupon   = "Coupon Code";
        $label_payment  = "Payment Option";
        $label_add      = "Delivery Address";
        $label_contact  = "Recipient Contact";
        $label_msg      = "Special Message";
        $label_process  = "Processing Fees";
        $label_subtotal = "Subtotal";
        $label_delivery = "Postage and Packaging";
        $label_gst      = "GST";
        $label_total    = "Total";
        $label_tnc1     = "I hereby agree with the";
        $label_tnc2     = "Terms & Conditions";
        $label_tnc3     = "Close";
        $label_tnc4     = "To proceed, do accept our Terms & Conditions.";
        break;
}

$items        = [];
$managePaySku = '';
$totalAmount  = 0;

foreach ($transactionDetails as $transactionDetail) {
    $productId       = $transactionDetail->product_id;
    $pointTypeId     = array_get(Config::get('constants.POINTS'), $productId);
    $pointType       = PointType::find($pointTypeId);
    $conversionRate  = PointConversionRate::from(PointType::CASH)->to($pointType->id)->active()->first();
    $totalAmount    += $transactionDetail->unit / $conversionRate->rate;

    if ((strlen($managePaySku) + strlen($transactionDetail->product_name) + 1) < 1000) {
        $managePaySku = $managePaySku.$transactionDetail->product_name.'|';
    }
}

$sumCart = [
    'name'     => "Transaction ID: {$transaction->id}",
    'price'    => $totalAmount,
    'quantity' => (int) htmlentities(1, ENT_QUOTES),
    'shipping' => 0,
];

$environment = Config::get('constants.ENVIRONMENT');

if ($environment == 'test') {
    $paypalUrl = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
} else {
    $paypalUrl = 'https://www.paypal.com/cgi-bin/webscr';
}

$returnUrl = '?tran_id='.$transaction->id.'&lang='.$transaction->lang;
$cancelUrl = '?tran_id='.$transaction->id.'&lang='.$transaction->lang;

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
        <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ url('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/checkout.css?v=2.3.0') }}" rel="stylesheet">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
        <!-- jQuery is required by MOLPay -->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
        <script src="https://www.onlinepayment.com.my/MOLPay/API/seamless/js/MOLPay_seamless.deco.js"></script>
    </head>
    <body>
        <div id="loading" style="display: none;">
            <div id="loading-animation">
                <img src="{{ url('img/checkout/lightbox-ico-loading.gif') }}">
            </div>
        </div>
        <div class="container-fluid">
            <div class="panel panel-default">
                <div class="panel-body">
                    <h3 class="panel-title">{{ $label_tran }}: {{ $transaction->id }}</h3>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ $label_payment }}</h3>
                </div>
                <div class="panel-body">
                    <div class="@if (Session::get('devicetype') == 'android') col-sm-4 @else col-sm-6 @endif">
                        <input type="radio" name="payopt" value="palpay" id="input-palpay" checked>
                        <label class="label-payment-img" for="input-palpay">
                            <img src="{{ url('img/checkout/paypal.png') }}" alt="PayPal">
                        </label>
                    </div>
                    <div class="@if (Session::get('devicetype') == 'android') col-sm-4 @else col-sm-6 @endif">
                        <input type="radio" name="payopt" value="mpay" id="input-mpay">
                        <label class="label-payment-img" for="input-mpay">
                            <img src="{{ url('img/checkout/managepay.png') }}" alt="Manage Pay">
                        </label>
                    </div>
                    @if (Session::get('devicetype') == 'android')
                        <div class="col-sm-4">
                            <input type="radio" name="payopt" value="molpay" id="input-molpay">
                            <label class="label-payment-img" for="input-molpay">
                                <img src="{{ url('img/checkout/molpay-logo.png') }}" alt="MOLPay">
                            </label>
                        </div>
                    @endif
                </div>
            </div>
            <div class="panel panel-default">
                <div class="list-group">
                    @foreach ($transactionDetails as $transactionDetail)
                        <div class="list-group-item item">
                            <?php
                            $productId       = $transactionDetail->product_id;
                            $pointTypeId     = array_get(Config::get('constants.POINTS'), $productId);
                            $pointType       = PointType::find($pointTypeId);
                            $conversionRate  = PointConversionRate::from(PointType::CASH)->to($pointType->id)->active()->first();
                            ?>
                            <span class="pull-right">{{ $currency }} {{ number_format($transactionDetail->unit / $conversionRate->rate, 2, '.', '') }}</span>
                            <h3 class="panel-title">{{ $transactionDetail->product_name }}</h3>
                            <?php $point = PointType::find(array_get(Config::get('constants.POINTS'), $transactionDetail->product_id));?>
                            Rate: {{ $currency }} 1.00 = {{ (1 / $point->redeem_rate)." {$point->type}" }}<br>
                            Points: {{ number_format($transactionDetail->unit) }}<br>
                        </div>
                    @endforeach
                    <div class="list-group-item">
                        <span class="pull-right"><b>{{ $currency.' '.number_format($totalAmount, 2, '.', '') }}</b></span>
                        <h3 class="panel-title">{{ $label_total }}</h3>
                    </div>
                </div>
            </div>
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
        <button type="button" id="btn-checkout" class="btn btn-lg btn-block btn-primary" onclick="return paypalSubmit();">Checkout</button>
        <script src="{{ url('js/bootstrap.min.js') }}"></script>
        <script>
        function paypalSubmit() {
            if ( ! document.getElementById('myCheck').checked) {
                alert('{{ $label_tnc4 }}');
                document.getElementById('myCheck').focus();
                return false;
            }

            $('#loading').css('display', 'block');

            setTimeout(function () {
                if($('[name="payopt"]:checked').val() == 'molpay') {
                    document.getElementById('molpay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'mpay') {
                    document.getElementById('mpay_checkout').submit();
                } else {
                    document.getElementById('paypal_checkout').submit();
                }
            }, 100);

            return false;
        }
        </script>
        <form id="paypal_checkout" action="{{ $paypalUrl }}" method="POST" style="display: none;">
            <input TYPE="hidden" name="charset" value="utf-8">
            <input type="hidden" name="invoice" value="{{ $transaction->id }}">
            <input type="hidden" name="name" value="{{ $transaction->name }}">
            <input type="hidden" name="address1" value="{{ $transaction->delivery_addr_1 }}">
            <input type="hidden" name="address2" value="{{ $transaction->delivery_addr_2 }}">
            <input type="hidden" name="country" value="{{ $transaction->delivery_country }}">
            <input type="hidden" name="state" value="{{ $transaction->delivery_state }}">
            <input type="hidden" name="zip" value="{{ $transaction->delivery_postcode }}">
            <?php $settings = PayPal::get_setting();?>
            <?php echo '<input type="hidden" name="cmd" value="_cart"><input type="hidden" name="upload" value="1"><input type="hidden" name="no_note" value="0"><input type="hidden" name="bn" value="PP-BuyNowBF"><input type="hidden" name="tax" value="0"><input type="hidden" name="rm" value="2"><input type="hidden" name="business" value="'.$settings['business'].'"><input type="hidden" name="handling_cart" value="'.$settings['shipping'].'"><input type="hidden" name="currency_code" value="'.$settings['currency'].'"><input type="hidden" name="lc" value="'.$settings['location'].'"><input type="hidden" name="return" value="'.$settings['returnurl'].$returnUrl.'"><input type="hidden" name="cbt" value="'.$settings['returntxt'].'"><input type="hidden" name="cancel_return" value="'.$settings['cancelurl'].$cancelUrl.'"><input type="hidden" name="notify_url" value="'.$settings['notifyurl'].'"><input type="hidden" name="custom" value="'.$settings['custom'].'"><div id="item" class="itemwrap"><input type="hidden" name="item_name_1" value="'.array_get($sumCart, 'name').'"><input type="hidden" name="quantity_1" value="'.array_get($sumCart, 'quantity').'"><input type="hidden" name="amount_1" value="'.array_get($sumCart, 'price').'"><input type="hidden" name="shipping_1" value="'.array_get($sumCart, 'shipping').'"></div><input id="ppcheckoutbtn" type="submit" value="Checkout" class="button">'; ?>
        </form>
        <form id="molpay_checkout" action="{{ isset($molpay_url) ? $molpay_url : '' }}" method="POST" style="display: none;">
            <?php $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : ''));?>
            <input type="hidden" name="merchant_id" value="{{ isset($molpay_merchant_id) ? $molpay_merchant_id : '' }}">
            <input type="hidden" name="amount" value="{{ $totalAmount }}">
            <input type="hidden" name="orderid" value="{{ $transaction->id }}">
            <input type="hidden" name="country" value="MY">
            <input type="hidden" name="currency" value="MYR">
            <input type="hidden" name="returnurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="cancelurl" value="{{ isset($molpay_returnurl) ? $molpay_returnurl : '' }}">
            <input type="hidden" name="vcode" value="{{ $vcode }}">
            <input type="hidden" name="bill_name" value="{{ $transaction->name }}">
            <input type="hidden" name="bill_email" value="{{ $transaction->buyer_email }}">
            <input type="hidden" name="bill_mobile" value="{{ $transaction->delivery_contact_no }}">
            <input type="hidden" name="bill_desc" value="Payment for tmGrocer">
        </form>
        <form id="mpay_checkout" action="{{ isset($mpay_url) ? $mpay_url : '' }}" method="GET" style="display: none;">
            <input type="hidden" name="MID" value="{{ $mid }}">
            <input type="hidden" name="amt" value="{{ str_pad(number_format($grand_amt, 2, '', ''), 12, '0', STR_PAD_LEFT) }}">
            <input type="hidden" name="invNo" value="{{ $transaction_id }}">
            <input type="hidden" name="desc" value="{{ $mpaysku }}">
            <input type="hidden" name="postURL" value="{{ $mpay_returnurl }}">
            <input type="hidden" name="phone" value="{{ $transaction->delivery_contact_no }}">
            <input type="hidden" name="email" value="{{ $transaction->buyer_email }}">
        </form>
    </body>
</html>
