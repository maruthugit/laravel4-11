<?php

$code = array();

$kkwprod = isset($kkwprod) ? $kkwprod : '';

switch (Session::get('lang')) {
    case 'CN':
        $code['001'] = '已经收到您的付款.<br />tmGrocer感谢您的光顾.';
        $code['002'] = '很遗憾您的订单已被取消.<br />tmGrocer感谢您的到访.';
        $code['003'] = '请到交易记录检视您的交易状态.';
        $code['004'] = '您的付款状态为"Pending".<br />tmGrocer感谢您的光顾.';
        $code['005'] = '很遗憾您的付款已被取消.<br />tmGrocer感谢您的到访.';
        $code['006'] = '已经收到您的付款.您的积分将会存入您的户口.';
        $code['101'] = '有误,请稍后再重试.';
        $code['102'] = '买家或密码有误.';
        $code['103'] = '请重选国家.';
        $code['104'] = '请重选州属.';
        $code['105'] = '请重选城市.';
        $code['106'] = '有误. (无该项产品.)';
        $code['107'] = '有误. (该项产品缺货当中.)';
        $code['108'] = '有误. (您的所在地并没提供该项产品.)';
        $code['109'] = '有误. (产品选项错误.)';
        $code['110'] = '抱歉, 您未能购买此产品: '.$kkwprod.'. 请从您的购物车删除此产品.';
        $code['111'] = '抱歉, 需达指定的最低消费才能享有优惠.';
        $code['112'] = '请重选城市.';
        $code['113'] = '抱歉, 需达指定的最低消费.';
        $code['114'] = '有误. (购物前请先激活您的帐号.)';
        $code['115'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 1 quantity.';
        break;
    case 'MY':
        $code['001'] = 'Transaksi anda telah berjaya.<br />Terima kasih kerana membeli-belah kat tmGrocer.';
        $code['002'] = 'Dukacitanya pesanan anda telah dibatal.<br />Terima kasih kerana mengunjungi tmGrocer';
        $code['003'] = 'Periksa status pesanan anda dalam Senarai Transaksi.';
        $code['004'] = 'Status bayaran anda adalah "Pending".<br />Terima kasih kerana beli-belah kat tmGrocer.';
        $code['005'] = 'Dukacitany bayaran anda gagal.<br />Terima kasih kerana mengunjungi tmGrocer';
        $code['006'] = 'Transaksi anda telah berjaya.<br>Point anda akan dikreditkan kepada akaun anda.';
        $code['101'] = 'Permintaan tidak sah. Sila cuba lagi.';
        $code['102'] = 'Pembeli atau katalaluan tidak sah';
        $code['103'] = 'Negara tidak sah.';
        $code['104'] = 'Negeri tidak sah.';
        $code['105'] = 'Lokasi tidak sah.';
        $code['106'] = 'Permintaan tidak sah. (Tiada produk.)';
        $code['107'] = 'Invalid request. (Produk yang anda beli dah habis stok.)';
        $code['108'] = 'Invalid request. (Produk tersebut tiada dalam lokasi anda.)';
        $code['109'] = 'Invalid request. (Pilihan harga tidak sah.)';
        $code['110'] = 'Oops, anda tidak dibenarkan membeli '.$kkwprod.'. Sila keluarkan barang tersebut dari troli anda.';
        $code['111'] = 'Oops, pembelian anda tidak memenuhi syarat pembelian minimum bagi harga istimewa.';
        $code['112'] = 'Bandar tidak sah.';
        $code['113'] = 'Oops, pembelian anda tidak memenuhi syarat pembelian minimum.';
        $code['114'] = 'Invalid request. (Sila aktifkan akaun anda sebelum membuat sebarang pembelian.)';
        $code['115'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 1 quantity.';
        break;
    case 'EN':
    default:
        $code['001'] = 'Your order has been successfully received.<br />Thank you for shopping at tmGrocer.';
        $code['002'] = 'Apologies for cancelling of your order.<br />Thank you for visiting tmGrocer.';
        $code['003'] = 'Check your order status in Transaction History.';
        $code['004'] = 'Your payment status was "Pending".'.' Transaction ID :'.$id. '. <br /> Thank you for shopping at tmGrocer.';
        $code['005'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting tmGrocer.';
        $code['006'] = 'Payment successful.<br>Your point will be credited directly to your account.';
        $code['101'] = 'Invalid request. Please try again.';
        $code['102'] = 'Invalid buyer or wrong password.';
        $code['103'] = 'Invalid country.';
        $code['104'] = 'Invalid state.';
        $code['105'] = 'Invalid location selected.';
        $code['106'] = 'Invalid request. (Product not found.)';
        $code['107'] = 'Invalid request. (The product your order was out of stock.)';
        $code['108'] = 'Invalid request. (Selected product is not available to your location)';
        $code['109'] = 'Invalid request. (Invalid price option selected.)';
        $code['110'] = 'Oops, you are not allow to purchase '.$kkwprod.'. Kindly remove it from your cart.';
        $code['111'] = 'Oops, you do not meet the minimum purchase requirement for special pricing.';
        $code['112'] = 'Invalid city.';
        $code['113'] = 'Oops, you do not meet the minimum purchase requirement.';
        $code['114'] = 'Invalid request. (Please activate your account before make any purchase.)';
        $code['115'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 1 quantity.';
        $code['117'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 5 quantity.';
        $code['118'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 4 quantity.';
        $code['119'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 2 quantity.';
        $code['120'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 3 quantity.';
        $code['121'] = 'Oops, you are not allow to purchase this item more than one time.';
        $code['122'] = 'Oops, you are not allow to purchase '.$kkwprod.' more than 12 quantity.';
        $code['123'] = 'Oops, you do not meet the minimum purchase value RM 30 for Flash sales/special Sales';
        $code['116'] = 'Item ' .$kkwprod. ' sold out.';
        break;
}

?>
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
    <body style="height:100vh;">
        <div id="loading" style="display: none;">
            <div id="loading-animation">
                <img src="{{ url('img/checkout/lightbox-ico-loading.gif') }}">
            </div>
        </div>
        <div class="container-fluid" >
            <div class="panel panel-default">
                <div class="panel-body">
                    {{ isset($message) ? $code[$message] : $code['101'] }}
                    <?php 
                        if($message == '107') {
                            ?><ul style="margin-top:10px;"><?php 
                            foreach ($dataCollection['outStockList'] as $key => $value) {
                                ?>
                                <li><?php echo $value['productSkU']." - ".$value['productName']; ?></li>
                        <?php    }?><ul><?php 
                        } 
                    ?>
                    @if ($message == '114')
                    	 <p> <a class="btn btn-primary btn-block" id="resentActivationLink">Resend Activation Link<a><p>
                    @endif
                    <span style="font-size: 0px">{{ isset($payment) ? $payment : '' }}</span>
                </div>
            </div>
            <div id="bcard-message" class="panel panel-default hide">
                <div class="panel-body"></div>
            </div>
            
            @if ( ! isset($rewardAmount) || (isset($rewardAmount) && $rewardAmount > 1))
                @if (isset($bcardStatus) && $bcardStatus == 1)
                    <?php
                    $deactivated = DB::table('point_deactivate_users')
                        ->where('point_type_id', '=', PointType::BCARD)
                        ->where('user_id', '=', $buyerId)
                        ->first();
                    ?>
                    @if ( ! $deactivated)
                        @if ($message == '001')
                            <!--<div id="bcard-reward" class="panel panel-default">-->
                            <!--    <div class="panel-body">-->
                            <!--        <div class="text-center">-->
                            <!--            <img src="{{ url('img/checkout/bcard.png') }}" alt="BCard" style="width:150px; margin-bottom:5px;">-->
                            <!--        </div>-->
                            <!--        <p>Enter your BCard number to earn loyalty points.</p>-->
                            <!--        <form id="bcardRewardForm">-->
                            <!--            <div class="input-group">-->
                            <!--                <input type="hidden" name="transactionId" value="{{ $id }}">-->
                            <!--                <input type="text" class="form-control" name="bcard" placeholder="BCard No." value="{{ $bcardNumber }}">-->
                            <!--                <span class="input-group-btn">-->
                            <!--                    <button class="btn btn-primary" type="submit">Claim</button>-->
                            <!--                </span>-->
                            <!--            </div>-->
                            <!--            <p class="help-block"></p>-->
                            <!--        </form>-->
                            <!--    </div>-->
                            <!--</div>-->
                        @endif
                    @endif
                @endif
            @endif
            <?php
            // Google Analytics
            if (isset($id))
            {
                //open connection
                $ch = curl_init();
                $url = asset('/') . "analytics/google/" . $id;

                //set the url, number of POST vars, POST data
                curl_setopt($ch,CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_HEADER, 0);

                // Timeout in seconds
                curl_setopt($ch, CURLOPT_TIMEOUT, 5);

                //execute post
                $result = curl_exec($ch);

                //close connection
                curl_close($ch);
            }
            ?>
            
            
        </div>
        <div class="modal fade" id="resentActivationMsg" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <h4 class="text-center">Activation link has been sent to {{$userinfo['userEmail']}}</h4>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" data-dismiss="modal">OK</button>
                    </div>
                </div>
            </div>
        </div> 
        <script src="{{ url('js/jquery.min.js') }}"></script>
        <script src="{{ url('js/bootstrap.min.js') }}"></script>
        <script>
        
            
        	
            function formDataParser(unindexedArray) {
                var indexedArray = {};

                $.map(unindexedArray, function(n, i) {
                    indexedArray[n['name']] = n['value'];
                });

                return indexedArray;
            }

            
              function resendActivationLink(username1){
              
                // Send Activation Link
                var username = username1;
                $.ajax({
                    url: '{{ url('api/user/resendactivation') }}',
                    data: {"username":username},
                    type: 'POST',
                    dataType: 'json',
                    success: function (response) {
                        if (response.responseStatus == '1') {
                            $("#resentActivationMsg").modal();
                        }
                    },
                    complete: function () {

                    }
                });
            }
            
            $("#resentActivationLink").click(function(){
                
                resendActivationLink('<?php echo $userinfo['username']; ?>');

            });
            
            
            $('#bcardRewardForm').submit(function () {
                $('#bcard-message').addClass('hide');
                var data = formDataParser($(this).serializeArray());

                if (data.bcard.length > 0) {
                    $('#bcardRewardForm .help-block').addClass('hide');
                    $('#loading').css('display', 'block');

                    $.ajax({
                        url: '{{ url('checkout/bcardreward') }}',
                        data: JSON.stringify(data),
                        type: 'POST',
                        contentType: 'application/json',
                        success: function (response) {
                            if (response == 'SUCCESS') {
                                response = 'You have successfully claimed your BPoints.';
                                $('#bcard-reward').addClass('hide');
                            }

                            $('#bcard-message').removeClass('hide');
                            $('#bcard-message .panel-body').html(response);
                        },
                        complete: function () {
                            $('#loading').css('display', 'none');
                        }
                    });
                } else {
                    $('#bcardRewardForm .help-block').html('BCard No. must not empty.');
                }

                return false;
            });
        </script>
        
    </body>
</html>