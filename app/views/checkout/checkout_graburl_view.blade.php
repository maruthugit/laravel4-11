<?php
// echo $transaction_id;
//  echo $request;
//  echo $partnertxid;
    
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
        <div class="panel-body">
            <form id="grab_pay_checkout" action="https://api.tmgrocer.com/grabpay/codeverifier" method="post">
                
                <input type="hidden" name="transaction_id" id="transaction_id" value="{{ $transaction_id }}">
                <input type="hidden" name="state" id="state">
                <input type="hidden" name="code_verifier" id="code_verifier">
                <input type="hidden" name="request" id="request">
                <input type="hidden" name="partnertxid" id="partnertxid">
                <input type="hidden" name="getcode" id="getcode">
                
            </form>
        </div>
        <div class="container-fluid" tyle="display:none;">
            <?php if($flag==1){ ?>
            Your Payment is processing... Please wait!.  
            <?php }else{ ?>
            Apologies for payment transaction failed of your order
            <?php } ?>
        </div>
        
        <!--<script src="{{ url('js/jquery.js') }}"></script>-->
        <!--<script src="{{ url('js/bootstrap.min.js') }}"></script>-->
    </body>
     <script type="text/javascript">
                //  alert('In');
                 
                
                 
                //  function getStoreAPIrequest(state,code_verifier,request,partnertxid){
                //         var transID = "<?php echo $transaction_id; ?>"
                //         $.ajax({
                //             method: "POST",
                //             url: "/grabpay/codeverifier",
                //             dataType:'json',
                //             data: {
                //                 'transaction_id':transID, 
                //                 'state' :state,
                //                 'code_verifier' : code_verifier,
                //                 'request' : request, 
                //                 'partnertxid' : partnertxid, 
                //             },
                //             beforeSend: function(){
                                
                //             },
                //             success: function(data) {
                //                 console.log(data);
                //             }
                //         });
                //     }
                
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
                    
                 function getAuthorizeLink(request,partnertxid) 
                    {
                        // alert('In');
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
                    
                    document.getElementById('state').value=state;
                    document.getElementById('code_verifier').value=code_verifier;
                    document.getElementById('request').value=request;
                    document.getElementById('partnertxid').value=partnertxid;
                    // var returnvalue = getStoreAPIrequest(state,code_verifier,request,partnertxid);
                    
                    // You should get this URL from service discovery
                    var url ='https://partner-api.grab.com/grabid/v1/oauth2/authorize?' + str;
                    return url;
                }
                <?php if($flag==1){ ?>
                
                var request = '<?php echo $request; ?>';
                 var partnerid = '<?php echo $partnertxid; ?>';
                 
                var getcode = getAuthorizeLink(request,partnerid);
                //  document.write(getcode);
                // window.open(getcode, '_self');
                // alert(getcode);
               
            document.getElementById('getcode').value=getcode;
           
            document.getElementById('grab_pay_checkout').submit();
                <?php } ?>
                // window.location.href=getcode;
                 
                
         </script>
</html>
