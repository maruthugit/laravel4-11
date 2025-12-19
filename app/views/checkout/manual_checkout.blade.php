<?php

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
        break;
}

$total_fees = $trans_row->delivery_charges + $trans_row->process_fees;
$mpaysku    = '';
$items      = array();
$key        = 0;
$group_product_price = array();
$group_product_gst   = array();

foreach($trans_detail_query as $row) {
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
$grand_amt = bcadd($grand_amt, ($trans_row->gst_total != '' ? $trans_row->gst_total : 0), 2);
$grand_amt = bcsub($grand_amt, ($total_trans_points ? $total_trans_points : 0), 2);
$grand_amt = abs($grand_amt);

?>

@extends('layouts.master')
@section('title', 'Transaction')
@section('content')

<div id="page-wrapper">
    <div class="row">
        <div class="col-lg-12">
            <h1 class="page-header">Checkout</h1>
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
                        <input type="hidden" name="devicetype" value="manual">
                        <div class="input-group">
                            <input type="text" class="form-control" id="coupon_code" name="coupon" placeholder="{{ $label_coupon }}">
                            <span class="input-group-btn">
                                <button class="btn btn-primary" type="submit">Enter Code</button>
                            </span>
                        </div>
                    </form>
                </div>
            </div>
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
                        <div class="@if (Session::get('devicetype') == 'android') col-sm-4 @else col-sm-6 @endif">
                            <input type="radio" name="payopt" value="cash" id="input-cash" checked>
                            <label class="label-payment-img" for="input-cash">
                                Cash
                            </label>
                        </div>
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
                    {{
                        implode('<br>', array_filter([
                            $trans_row->delivery_addr_1,
                            $trans_row->delivery_addr_2,
                            $trans_row->delivery_state,
                            $trans_row->delivery_postcode,
                            $trans_row->delivery_country,
                        ]))
                    }}
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
                        <div class="list-group-item item">
                            <span class="pull-right">{{ $currency }} {{ number_format($row->price * $row->unit, 2, '.', '') }}</span>
                            <h3 class="panel-title">@if ($row->gst_amount > 0) ** @else * @endif {{ $row->product_name }}</h3>
                            SKU: {{ $row->sku }}<br>
                            Unit Price: {{ $currency }} {{ number_format($row->price, 2, '.', '') }}<br>
                            Quantity: {{ $row->unit }}<br>
                            Delivery Time: <b>{{ empty($row->delivery_time) ? '24 hours' : $row->delivery_time }}</b><br>
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
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->delivery_charges, 2, '.', '') }}</span>
                        ** {{ $label_delivery }}<br>
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->process_fees, 2, '.', '') }}</span>
                        ** {{ $label_process }}<br>
                        <!-- @if ( ! empty($coupon_code))
                            <span class="pull-right">- {{ $currency }} {{ number_format($coupon_code_amount, 2, '.', '') }}</span>
                            {{ $label_coupon }}: {{ $coupon_code }}
                        @endif Old Invoice -->
                    </div>
                    <div class="list-group-item">
                        <span class="pull-right">{{ $currency }} {{ number_format($total_fees, 2, '.', '') }}</span> <!-- New Invoice -->
                        <!-- <span class="pull-right">{{ $currency }} {{ number_format($total_fees - ( ! empty($coupon_code) ? $coupon_code_amount : 0), 2, '.', '') }}</span> Old Invoice--> 
                        <h3 class="panel-title">{{ $label_subtotal }}</h3>
                    </div>
                    <div class="list-group-item">
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->gst_total, 2, '.', '') }}</span>
                        {{ $label_gst }}
                    </div>
                    @foreach ($trans_points as $point)
                        <div class="list-group-item">
                            <span class="pull-right">- {{ $currency }} {{ number_format($point->amount, 2, '.', '') }}</span>
                            {{ $point->type }} Redemption ({{ $point->point }} points)
                        </div>
                    @endforeach
                    <!-- New Invoice Start  -->
                     <div class="list-group-item">
                        <!-- <span class="pull-right">{{ $currency }} {{ number_format($trans_row->delivery_charges, 2, '.', '') }}</span>
                        ** {{ $label_delivery }}<br>
                        <span class="pull-right">{{ $currency }} {{ number_format($trans_row->process_fees, 2, '.', '') }}</span>
                        ** {{ $label_process }}<br> -->
                        @if ( ! empty($coupon_code))
                            <span class="pull-right">- {{ $currency }} {{ number_format($coupon_code_amount, 2, '.', '') }}</span>
                            {{ $label_coupon }}: {{ $coupon_code }}
                        @endif
                    </div>
                    <!-- New Invoice End -->
                    <div class="list-group-item">
                        <?php $total = $total_fees - ($coupon_code != '' ? $coupon_code_amount : 0) + ($trans_row->gst_total != '' ? $trans_row->gst_total : 0) - ($total_trans_points ? $total_trans_points : 0); ?>
                        <span class="pull-right"><b>{{ $currency }} {{ number_format(abs($total), 2, '.', '') }}</b></span>
                        <h3 class="panel-title">{{ $label_total }}</h3>
                    </div>
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
                        * GST 0%<br>
                        ** GST {{ $trans_row->gst_rate }}%
                    </div>
                </div>
            </div>
            @if ($grand_amt > 0)
                <?php $count = 0; ?>
                @foreach ($points as $point)
                    <?php $availablePoint = $point->point; ?>
                    <?php $pointType = strtolower($point->type); ?>
                    @foreach ($trans_points as $trans_point)
                        @if ($trans_point->point_type_id == $point->point_type_id)
                            <?php $availablePoint -= $trans_point->point; ?>
                        @endif
                    @endforeach
                    @if ($point->redeem_rate > 0 && $availablePoint > 0)
                        <div class="panel panel-default" id="redemption">
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
                                    <input type="checkbox" id="myCheck" name="test" checked>&nbsp;&nbsp;
                                </td>
                                <td>
                                    <label for="myCheck">
                                        {{ $label_tnc1 }}
                                        <a role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTNC" aria-expanded="true" aria-controls="collapseOne"> {{ $label_tnc2 }}</a>.
                                    </label>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div id="collapseTNC" class="panel-collapse collapse" role="tabpanel" aria-labelledby="headingTNC">
                        <div class="panel-body" style="overflow: auto; -webkit-overflow-scrolling: touch; height: 350px; width: 100%;">
                            <iframe src="{{ url('tnc') }}" width="100%" height="100%"></iframe>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <button type="button" id="btn-checkout" class="btn btn-lg btn-block btn-primary" onclick="return paypalSubmit();">Checkout</botton>
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

        ?>
        <script src="{{ url('js/bootstrap.min.js') }}"></script>
        <script type="text/javascript">
        function paypalSubmit() {
            if( ! document.getElementById('myCheck').checked) {
                alert('{{ $label_tnc4 }}');
                document.getElementById('myCheck').focus();
                return false;
            }

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
                else if($('[name="payopt"]:checked').val() == 'cash') {
                    document.getElementById('cash_checkout').submit();
                }
                else if($('[name="payopt"]:checked').val() == 'molpay') {
                    document.getElementById('molpay_checkout').submit();
                } else if($('[name="payopt"]:checked').val() == 'mpay') {
                    document.getElementById('mpay_checkout').submit();
                } else if($('[name="payopt"]').val() == "fullredeem") {
                    document.getElementById('fullredeem_checkout').submit();
                } else {
                    document.getElementById('paypal_checkout').submit();
                }
            }, 100);

            return false;
        }
        </script>
        <form id="cash_checkout" action="/transaction/edit/{{ $transaction_id }}" method="POST" style="display:none">
            <input TYPE="hidden" name="charset" value="utf-8">
            <input type="hidden" name="invoice" value="{{ $transaction_id }}">
            <input type="hidden" name="status" value="completed">
        </form>

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
            <input type="hidden" name="bill_desc" value="">
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
        <form id="jocom_checkout" action="http://dev.jocom.com.my/test" method="get" style="display: none;">
            <input type="hidden" name="MID" value="{{ $transaction_id }}">
        </form>
        </div>
    </div>
</div>
@stop