<?php

    
    //  $access_token = '';
?>

<!DOCTYPE html>
<html lang="en">
    <head>
       
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no, maximum-scale=1">
        <link href="{{ url('css/bootstrap.min.css') }}" rel="stylesheet">
        <link href="{{ url('font-awesome/css/font-awesome.min.css') }}" rel="stylesheet">
        <link href="{{ url('css/checkout.css?v=2.3.0') }}" rel="stylesheet">
        <!--[if lt IE 9]>
            <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
            <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
        <![endif]-->
       <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-base64.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/hmac-sha256.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/enc-utf8.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.9-1/crypto-js.js"></script>
         <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
         
    </head>
    <body>
        <!--<div id="loading">-->
        <!--    <div id="loading-animation">-->
        <!--        <img src="{{ url('img/checkout/lightbox-ico-loading.gif') }}">-->
        <!--    </div>-->
        <!--</div>-->
        <div class="container-fluid" tyle="display:none;">
           
            <?php 
            
                if($status == 1) { ?>
            Your order has been successfully received.<br />Thank you for shopping at tmGrocer.
                    <div style="display:none;">JCSUCCESS</div>

             <?php } else {?>
             Your payment status was Pending. <br /> Thank you for shopping at tmGrocer.
             <?php } ?>
        </div>
        
        <!--<script src="{{ url('js/jquery.js') }}"></script>-->
        <!--<script src="{{ url('js/bootstrap.min.js') }}"></script>-->
    </body>
     
</html>
