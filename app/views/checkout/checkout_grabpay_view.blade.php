<?php
    $access_token = $APIResponse['access_token'];
    $token_type = $APIResponse['token_type'];
    $expires_in = $APIResponse['expires_in'];
    $id_token = $APIResponse['id_token'];
    
     $timezone  = -8; //(GMT -5:00) EST (U.S. & Canada)
 $timezone  = 'Asia/Kuala_Lumpur';
 $gmtdate = gmdate("D, d M Y H:i:s", time() + 3600*($timezone+date("I"))).' GMT';
    
    //  $access_token = '';
    // echo 'status-'.$status;
    // print_r($APIResponse);
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
        <div class="panel-body">
            <form id="grab_pay_checkout" action="https://api.tmgrocer.com/grabpay/grabcomplete" method="post">
                
                <input type="hidden" name="partnertxid" id="partnertxid" value="{{ $partnertxid }}">
                <input type="hidden" name="oauth2_token_otc" id="oauth2_token_otc" value="{{ $access_token }}">
                <input type="hidden" name="pop" id="pop">
                <input type="hidden" name="gmttime" id="gmttime">
                
                
            </form>
        </div>
        <div class="container-fluid" tyle="display:none;">
             Your Payment is processing... Please wait!.......  
        </div>
        
        <!--<script src="{{ url('js/jquery.js') }}"></script>-->
        <!--<script src="{{ url('js/bootstrap.min.js') }}"></script>-->
    </body>
    <script type="text/javascript">
            
                 
                 function getStoreAPIrequest(pop,gmTtimestamp){
                    var token ='<?php echo $access_token; ?>';
                    var partnertxid ='<?php echo $partnertxid; ?>';
                    
                    $.ajax({
                        method: "POST",
                        url: "/grabpay/grabcomplete",
                        dataType:'json',
                        data: {
                            'partnertxid':partnertxid, 
                            'oauth2_token_otc':token, 
                            'pop' :pop,
                            'gmttime' : gmTtimestamp 
                        },
                        beforeSend: function(){
                            
                        },
                        success: function(data) {
                            console.log(data);
                        }
                    });
                }
                
                function base64URLEncode(str) {
                	return str.toString(CryptoJS.enc.Base64).replace(/=/g, '').replace(/\+/g, '-').replace(/\//g, '_');
                }

                function generatePOPSignature(clientSecret, accessToken, timestamp) {
                    var timestampUnix = Math.round(timestamp.getTime() / 1000);
                    var message = timestampUnix.toString() + accessToken;
                    var signature = CryptoJS.enc.Base64.stringify(CryptoJS.HmacSHA256(message, clientSecret));
                    
                    var payload = {
                        "time_since_epoch": timestampUnix,
                        "sig": base64URLEncode(signature)
                    }
                    var payloadBytes = JSON.stringify(payload);
                    return base64URLEncode(btoa(payloadBytes));
                }
                
                var timestamp = new Date();
                // var timestamp = '<?php echo $gmtdate;?>';
                var clientSecret = '_Rjy97nQtq8obtfv';
                var oauthToken = '<?php echo $access_token;?>';
                
                var gmTtimestamp = timestamp.toGMTString();
                var pop_signature = generatePOPSignature(clientSecret, oauthToken, timestamp);  
                document.getElementById('pop').value=pop_signature;
                document.getElementById('gmttime').value=gmTtimestamp;
                // var pop = getStoreAPIrequest(pop_signature,gmTtimestamp);
                // alert('In');
                    console.log(pop_signature);
                document.getElementById('grab_pay_checkout').submit();
                   
          
         </script>
</html>
