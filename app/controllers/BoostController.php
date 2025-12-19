<?php 

class BoostController extends BaseController {
    
    const BOOST_URL_AUTHENTICATION = '/online/authentication/';
    const BOOST_URL_PAYMENT_QRCODE = '/api/v1.0/online/transaction/payment/qrcode';
    const BOOST_URL_ONLINE_REFER_NO = "/api/v1.0/online/transaction/ref/";
    const BOOST_URL_SINGLE_TRANSACTION = "/api/v1.0/online/transaction/";
    const BOOST_URL_PAYMENT = '/boost/payment/';
    const BOOST_REDIRECT_URL = '/';
    
    
    public function boostTest(){
        
         //$response = self::authetication();
        // $response = self::getPaymentURL();
        echo $response;
//        self::getPaymentURL();
        
    }
    

    /*
     * @desc : To generate API Token. API token will be expired 30minutes from last API request
     */

    public static function validatepayment(){

       
        // echo '<pre>';
        // print_r(Input::all());
        // echo '</pre>';

         $username = Input::get('username');
         $amount = Input::get('amt');
         $transid = Input::get('transid');
         $desc = substr(Input::get('desc'),0,19);
         $email = Input::get('email');
         $desc = preg_replace('/[^A-Za-z0-9\-]/', '', $desc);
         if($desc == ""){
             $desc = $transid;
         }
         
         if (Config::get('constants.ENVIRONMENT') == 'live') {
                    $apiKey = Config::get('constants.BOOST_API_KEY_PRO');
                    $apiSecret = Config::get('constants.BOOST_SECRET_KEY_PRO');
                    $apiredirecturl = Config::get('constants.BOOST_ENV_PRO_REDIRECT_URL');
                    $apicancelurl = Config::get('constants.BOOST_ENV_PRO_CANCEL_URL');

                }else{
                    $apiKey = Config::get('constants.BOOST_API_KEY_DEV');
                    $apiSecret = Config::get('constants.BOOST_SECRET_KEY_DEV');
                    $apiredirecturl = Config::get('constants.BOOST_ENV_DEV_REDIRECT_URL');
                    $apicancelurl = Config::get('constants.BOOST_ENV_DEV_REDIRECT_URL');
                }

        $request = array(
            "apiKey" => $apiKey,
            "apiSecret" => $apiSecret
        );

        // echo $apiSecret;
        $api_user = ApiUser::where('username',$username)
                        ->select('id')
                        ->first();
        $userid = $api_user->id;


        $apiToken=self::authetication($userid);
        // $apiToken = '26daf695-c972-4eb2-92b3-8958b5c94c75';

        $rndnum = self::rnd_number();

             $res = array(
                "merchantId" => Config::get('constants.BOOST_MERCHANT_ID'),
                "onlineRefNum" =>  $rndnum,
                "amount" => $amount,
                "remark" => $desc,
                "redirectURL" => $apiredirecturl.$rndnum,
                "cancelURL" => $apicancelurl.$rndnum,
                "merchantInfo" => array(
                    "id" => Config::get('constants.BOOST_MERCHANT_ID'),
                    "merchantName" => 'online-JOCOM-M',
                ),
                "checksum" => '',
                "userid"  => $userid,
                "apitoken" => $apiToken
            );

             $checkSumString = $res['merchantId'].$res['onlineRefNum'].$res['amount'].$res['remark'].$res['redirectURL'];
            // echo '---'.$checkSumString.'---';
            // $checkSum = self::getTDESChecksum($checkSumString,$apiSecret);
            $checkSum = self::encryptnew($checkSumString,$apiSecret);
            $res['checksum'] = $checkSum;
        

        // $checkSumDecrypt = self::getDecrypt($checkSum,$apiSecret);
        
        // echo 'DeCrypt-'.$checkSumDecrypt.'-DeCrypt';
        // die();
        $APIResponse = self::ApiCaller(self::BOOST_URL_PAYMENT_QRCODE, $res,false);

        // print_r($APIResponse);

        $arrayResponse = json_decode($APIResponse,true);


        $apitokenURL = $arrayResponse['checkoutURI'];


        $BoostApiTokenResponse = new BoostApiTokenResponse();
        $BoostApiTokenResponse->userid = $userid;
        $BoostApiTokenResponse->onlinerefnum =$rndnum;
        $BoostApiTokenResponse->transaction_id = $transid;
        $BoostApiTokenResponse->userid = $userid;
        $BoostApiTokenResponse->token_id = $apiToken;
        $BoostApiTokenResponse->transaction_token = $arrayResponse['transactionToken'];
        $BoostApiTokenResponse->checkout_url = $arrayResponse['checkoutURI'];
        $BoostApiTokenResponse->base64image_qrcode = $arrayResponse['checkoutURI'];
        $BoostApiTokenResponse->checksum = $checkSum;
        $BoostApiTokenResponse->created_at = date("Y-m-d H:i:s");
        $BoostApiTokenResponse->save();

       // Boost Sandbox
        header('location:'.$apitokenURL);
        //
        exit();

        // return $APIResponse;


    }  


    public function response($onlineref){
        
        // echo $onlineref;
        $checkout_source = 0;
        $t_id = 0;
        $jsondata = "";
        $BoostApiOnlineRef = BoostApiTokenResponse::getBoostAPIResponse($onlineref);
         if(count($BoostApiOnlineRef)>0){

                $onlineRefNum = $BoostApiOnlineRef->onlinerefnum;
                $tokenid = $BoostApiOnlineRef->token_id;
                $checksum = $BoostApiOnlineRef->checksum;
                $userid = $BoostApiOnlineRef->userid;
                $transaction_id = $BoostApiOnlineRef->transaction_id;
                $t_id = $BoostApiOnlineRef->transaction_id;

                $res = array(
                "merchantId" => Config::get('constants.BOOST_MERCHANT_ID'),
                "onlineRefNum" =>  $onlineRefNum,
                "apitoken" => $apiToken
            );
                
                $APIResponse = self::ApiCallerGet(self::BOOST_URL_ONLINE_REFER_NO, $res,false);
                $jsondata = $APIResponse;
                $arrayResponse = json_decode($APIResponse,true);
                
                if($arrayResponse['error'] =='invalid_token'){
                    // echo 'ok';
                    $apiToken=self::authetication($userid);
                    // echo $apiToken;
                     $res['apitoken'] = $apiToken;
                    $APIResponse_att = self::ApiCallerGet(self::BOOST_URL_ONLINE_REFER_NO, $res,false);
                    $arrayResponse = json_decode($APIResponse_att,true);
                    // var_dump($APIResponse_att);
                    $jsondata = $APIResponse_att;
                }

         }
       
       
         $transaction          = Transaction::incomplete($transaction_id)->first();
        //  print_r($transaction);
        // die();
        //  print_r($arrayResponse['transactionType']);
        if ($arrayResponse['transactionType'] != "" && $transaction != "") {
       
            $data['merchantId'] = $arrayResponse['merchantId'];
            $data['onlineRefNum'] = $arrayResponse['onlineRefNum'];
            $data['transactionType'] = $arrayResponse['transactionType'];
            $data['amount'] = $arrayResponse['amount'];
            $data['transactionTime'] = $arrayResponse['transactionTime'];
            $data['customerLast4DigitMSISDN'] = $arrayResponse['customerLast4DigitMSISDN'];
            $data['transactionStatus'] = $arrayResponse['transactionStatus'];
            $data['boostRefNum'] = $arrayResponse['boostRefNum'];
            $data['checksum'] = $checksum;
            $data['jsondata'] = $jsondata;
               

            $BoostApiTokenResponse = BoostApiTokenResponse::getBoostAPIResponse($data['onlineRefNum']);

            if(count($BoostApiTokenResponse) > 0){

                $data['transid'] = $BoostApiTokenResponse->transaction_id;
                $data['userid'] = $BoostApiTokenResponse->userid;
            }

            $BoostPaymentResponse = new BoostTransaction();
            $BoostPaymentResponse->userid = $data['userid'];
            $BoostPaymentResponse->onlinerefnum = $data['onlineRefNum'];
            $BoostPaymentResponse->transaction_id = $data['transid'];
            $BoostPaymentResponse->merchant_Id = $data['merchantId'];
            $BoostPaymentResponse->transaction_type = $data['transactionType'];
            $BoostPaymentResponse->amount = $data['amount'];
            $BoostPaymentResponse->transaction_time = $data['transactionTime'];
            $BoostPaymentResponse->customer_last4digit_msisdn = $data['customerLast4DigitMSISDN'];
            $BoostPaymentResponse->transaction_status = $data['transactionStatus'];
            $BoostPaymentResponse->boost_refnum = $data['boostRefNum'];
            $BoostPaymentResponse->checksum = $data['checksum'];
            $BoostPaymentResponse->json_data = $data['jsondata'];
            $BoostPaymentResponse->created_at = date("Y-m-d H:i:s");
            $BoostPaymentResponse->save();

            if($data['transactionStatus'] == 'completed'){

                $transactionType = MCheckout::boost_transaction_complete($data);

                // Success
                switch ($transactionType) {
                    case 'point':
                        $data['message'] = '006';
                        break;
                    default:
                        $data['message'] = '001';
                        break;
                }

                $transaction = Transaction::find($data['transid']);
                $user        = Customer::find($transaction->buyer_id);
                $username    = $user->username;
                $bcard       = BcardM::where('username', '=', $username)->first();
                $bcardStatus = PointModule::getStatus('bcard_earn');
                $checkout_source = $transaction->checkout_source;
                // print_r($user);
                if($transaction->transaction_date >= Config::get('constants.BOOST_MONDAY_CAMPAIGN_START_DATE') && $transaction->transaction_date <= Config::get('constants.BOOST_MONDAY_CAMPAIGN_END_DATE')  ){
                    
                    // Reward Double JPoint for purchase with boost on every monday
                    // From 22 April 2019 - 30 Jun 2019
                    $timestamp = strtotime($transaction->transaction_date);
                    $day = date('D', (string)$timestamp);
                    $isPayWithBoost = DB::table('jocom_boost_transaction')->where('transaction_id', '=', $transaction->id)->count();
                    if($isPayWithBoost > 0 && $day == 'Mon'){
                        $PointController = new PointController;
                        $PointController->rewardDouble($transaction->id,$user->id);
                    }
                    // Reward Double JPoint for purchase with boost on every monday
                }
                
                // Reward 2000 JPoints when puchase above RM100
                if($transaction->transaction_date >= Config::get('constants.BOOST_BIGPOINT_CAMPAIGN_START_DATE') && $transaction->transaction_date <= Config::get('constants.BOOST_BIGPOINT_CAMPAIGN_END_DATE')  ){
                    
                    $isPayWithBoost = DB::table('jocom_boost_transaction')->where('transaction_id', '=', $transaction->id)->count();
                    if($isPayWithBoost > 0 ){
                        $PointController = new PointController;
                        $PointController->rewardBigPoint($transaction->id,$user->id,2000,100);
                    }
                }
                // Reward 2000 JPoints when puchase above RM100
               
                $data['payment'] = 'JCSUCCESS successfully received';
                // $data['message'] = 'Your order has been successfully received.<br />Thank you for shopping at JOCOM.';
                if($checkout_source == 5){
                    return Redirect::to('http://jocom.my/boost/verify?tranID='.$transaction->id.'&status=success');
                 }
                 else{
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')
                        ->with('message', $data['message'])
                        ->with('payment', $data['payment'])
                        ->with('id', $data['transid'])
                        ->with('bcardStatus', $bcardStatus)
                        ->with('bcardNumber', object_get($bcard, 'bcard'))
                        ->with('buyerId', $transaction->buyer_id);
                 }

            } else if ($data['transactionStatus'] != 'completed-ack' || $data['transactionStatus'] != 'completed' ){
                // Error
                // Failed
                $temp_cancel     = MCheckout::cancelled_transaction(trim($data['transid']));
                $data            = array();
                $data['message'] = '002';
                $data['payment'] = 'JCFAIL failed';
                $transaction = Transaction::find($t_id);
                $checkout_source = $transaction->checkout_source;
                if($checkout_source == 5 && $data['transactionStatus'] != 'completed'){
                    return Redirect::to('http://jocom.my/boost/verify?tranID='.$transaction->id.'&status=failed');
                 }
                 else
                 {
                     return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
                 }
                // $data['message'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting.';
                
            }

        } else {
            $data = array();
            // $data['message'] = 'Invalid request. Please try again.';
            // if ($transaction == "completed") {
               $transaction = Transaction::find($t_id);
                $checkout_source = $transaction->checkout_source;
                if($checkout_source == 5 && $transaction->status == 'completed'){
                    return Redirect::to('http://jocom.my/boost/verify?tranID='.$transaction->id.'&status=success');
                 }
                 else{
                    $data['payment'] = 'JCSUCCESS successfully received';
                    $data['message'] = '001';
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
                 }
            // }
            // else {
            //     $data['message'] = '003';
            //     return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
            // }
        }

    }


    public function cancel($onlineref){
        
        $checkout_source = 0;
        $data['onlineRefNum'] = $onlineref;
        $BoostApiTokenResponse = BoostApiTokenResponse::getBoostAPIResponse($data['onlineRefNum']);

            if(count($BoostApiTokenResponse) > 0){

                $data['transid'] = $BoostApiTokenResponse->transaction_id;
                $data['userid'] = $BoostApiTokenResponse->userid;
            }

        if (isset($data['transid'])) {
            $temp_cancel = MCheckout::cancelled_transaction($data['transid']);
            $transaction = Transaction::find($data['transid']);
            $checkout_source = $transaction->checkout_source;
        }

        $data['message'] = '002';
        $data['payment'] = 'JCFAIL failed';
        // $data['message'] = 'Apologies for cancelling of your order.<br />Thank you for visiting.';
        if($checkout_source == 5){
                    return Redirect::to('http://jocom.my/boost/verify?tranID='.$transaction->id.'&status=failed');
         }
         else{
            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
         }
    }

    public function callback(){
        try
        {
       
            
        $onlinrefno = '';    
        $data = file_get_contents('php://input');
        
        $calldata = json_decode($data,true);    
        
        $onlinrefno =   $calldata['onlineRefNum'];
       
        $callbackid =  DB::table('jocom_boost_api_callbacklogs')->insertGetId(array(
                                    'onlinerefnum'     =>  $onlinrefno,
                                    'api_log'          =>  $data,
                                    'created_at'    =>  date("Y-m-d H:i:s")
                                    
                                    )
                            );
            
        
        $BoostApiOnlineRef = BoostApiTokenResponse::getBoostAPIResponse($onlinrefno);
         if(count($BoostApiOnlineRef)>0){

                $onlineRefNum = $BoostApiOnlineRef->onlinerefnum;
                $tokenid = $BoostApiOnlineRef->token_id;
                $checksum = $BoostApiOnlineRef->checksum;
                $userid = $BoostApiOnlineRef->userid;
                $transaction_id = $BoostApiOnlineRef->transaction_id;

        }

        $transaction  = Transaction::incomplete($transaction_id)->first();
   

        if ($calldata['transactionType'] != "" && $transaction != "") {
    //  echo $transaction['status'].'ok';
 
            $data_cal['merchantId'] = $calldata['merchantId'];
            $data_cal['onlineRefNum'] = $calldata['onlineRefNum'];
            $data_cal['transactionType'] = $calldata['transactionType'];
            $data_cal['amount'] = $calldata['amount'];
            $data_cal['transactionTime'] = $calldata['transactionTime'];
            $data_cal['customerLast4DigitMSISDN'] = $calldata['customerLast4DigitMSISDN'];
            $data_cal['transactionStatus'] = $calldata['transactionStatus'];
            $data_cal['boostRefNum'] = $calldata['boostRefNum'];
            $data_cal['checksum'] = $checksum;
            $data_cal['transid'] = $transaction_id;     
            $data_cal['userid'] = $userid;
          
            $BoostPaymentResponse = new BoostTransaction();
            $BoostPaymentResponse->userid = $data_cal['userid'];
            $BoostPaymentResponse->onlinerefnum = $data_cal['onlineRefNum'];
            $BoostPaymentResponse->transaction_id = $data_cal['transid'];
            $BoostPaymentResponse->merchant_Id = $data_cal['merchantId'];
            $BoostPaymentResponse->transaction_type = $data_cal['transactionType'];
            $BoostPaymentResponse->amount = $data_cal['amount'];
            $BoostPaymentResponse->transaction_time = $data_cal['transactionTime'];
            $BoostPaymentResponse->customer_last4digit_msisdn = $data_cal['customerLast4DigitMSISDN'];
            $BoostPaymentResponse->transaction_status = $data_cal['transactionStatus'];
            $BoostPaymentResponse->boost_refnum = $data_cal['boostRefNum'];
            $BoostPaymentResponse->checksum = $data_cal['checksum'];
            $BoostPaymentResponse->json_data = $data;
            $BoostPaymentResponse->created_at = date("Y-m-d H:i:s");
            $BoostPaymentResponse->save();
            
            if($data_cal['transactionStatus'] == 'completed'){

                $transactionType = MCheckout::boost_transaction_complete($data_cal);
                // Success
                switch ($transactionType) {
                    case 'point':
                        $data_cal['message'] = '006';
                        break;
                    default:
                        $data_cal['message'] = '001';
                        break;
                }
                $transaction = Transaction::find($data_cal['transid']);
                $user        = Customer::find($transaction->buyer_id);
                $username    = $user->username;
                $bcard       = BcardM::where('username', '=', $username)->first();
                $bcardStatus = PointModule::getStatus('bcard_earn');
                
                if($transaction->transaction_date >= Config::get('constants.BOOST_MONDAY_CAMPAIGN_START_DATE') && $transaction->transaction_date <= Config::get('constants.BOOST_MONDAY_CAMPAIGN_END_DATE')  ){
                    
                    // Reward Double JPoint for purchase with boost on every monday
                    // From 22 April 2019 - 30 Jun 2019
                    $timestamp = strtotime($transaction->transaction_date);
                    $day = date('D', (string)$timestamp);
                    $isPayWithBoost = DB::table('jocom_boost_transaction')->where('transaction_id', '=', $transaction->id)->count();
                    if($isPayWithBoost > 0 && $day == 'Mon'){
                        $PointController = new PointController;
                        $PointController->rewardDouble($transaction->id,$user->id);
                    }
                    // Reward Double JPoint for purchase with boost on every monday
                }
                
                // Reward 2000 JPoints when puchase above RM100
                if($transaction->transaction_date >= Config::get('constants.BOOST_BIGPOINT_CAMPAIGN_START_DATE') && $transaction->transaction_date <= Config::get('constants.BOOST_BIGPOINT_CAMPAIGN_END_DATE')  ){
                    
                    $isPayWithBoost = DB::table('jocom_boost_transaction')->where('transaction_id', '=', $transaction->id)->count();
                    if($isPayWithBoost > 0 ){
                        $PointController = new PointController;
                        $PointController->rewardBigPoint($transaction->id,$user->id,2000,100);
                    }
                }
                // Reward 2000 JPoints when puchase above RM100
          
                $CMSResponse = BoostCallback::find($callbackid);
                $CMSResponse->c_transactiontype = $transactionType;
                $CMSResponse->c_transid = $data_cal['transid'];
                $CMSResponse->c_bcardstatus = $bcardStatus;
                $CMSResponse->c_bcardnumber = $bcard;
                $CMSResponse->c_buyerid = $transaction->buyer_id;
                $CMSResponse->message = $data_cal['message'];
                
                $CMSResponse->save();
               

            } 

        } 
        // print_r($data);
        
        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
           
           
        } 
    
    }
    
    public static function authetication($userid){
        // echo 'ok';
        // die();
        try{
            // $userid = 0;
            $isNewToken = false;

            $latestToken = DB::table('jocom_boost_api_token')
                                    ->where('status', 1)
                                    ->where('userid',$userid)
                                     ->whereNotNull('api_token')
                                    ->orderBy('jocom_boost_api_token.id','desc')
                                    ->first();
                                    
                // $latestToken = $latestToken + 35;
            if(count($latestToken) > 0 ){

                $updatedDatetime = $latestToken->updated_at;
                $lastTokenID = $latestToken->id;

                $to_time = strtotime($updatedDatetime);
                $from_time = strtotime("now");
                // echo $updatedDatetime.'-'.$lastTokenID;
                $totalMinutes =  round(abs($to_time - $from_time) / 60,2);

                // $totalMinutes = $totalMinutes + 35;
           // echo '<br>'.$totalMinutes;
                // check over 30 minutes

                if($totalMinutes > 30){
                    // Update old token //
                      
                    $BoostApiToken = BoostApiToken::find($lastTokenID);
                    $BoostApiToken->userid = $userid;
                    $BoostApiToken->status = 2;
                    $BoostApiToken->save();
                    // echo 'More';
                    $isNewToken = true;
                }else{
                    $BoostApiToken = BoostApiToken::find($lastTokenID);
                    $BoostApiToken->userid = $userid;
                    $BoostApiToken->updated_at = date("Y-m-d H:i:s");
                    $BoostApiToken->save();
                    $isNewToken = false;
                }
               
            }else{
                $isNewToken = true;
            }
            // die();

            if($isNewToken){

                if (Config::get('constants.ENVIRONMENT') == 'live') {
                    $apiKey = Config::get('constants.BOOST_API_KEY_PRO');
                    $apiSecret = Config::get('constants.BOOST_SECRET_KEY_PRO');
                }else{
                    $apiKey = Config::get('constants.BOOST_API_KEY_DEV');
                    $apiSecret = Config::get('constants.BOOST_SECRET_KEY_DEV');
                }

                $request = array(
                    "apiKey" => $apiKey,
                    "apiSecret" => $apiSecret
                );

                // print_r($request);

                $APIResponse = self::ApiCaller(self::BOOST_URL_AUTHENTICATION, $request,true);
                 // $APIResponse = self::ApiAuthentication(self::BOOST_URL_AUTHENTICATION, $request,true);


                // print_r($APIResponse);



                $arrayResponse = json_decode($APIResponse,true);

                $api_token = $arrayResponse['apiToken'];
                // echo $api_token.'New';
                $BoostApiToken = new BoostApiToken();
                $BoostApiToken->userid = $userid;
                $BoostApiToken->api_token = $api_token;
                $BoostApiToken->save();

            }else{
                $api_token = $latestToken->api_token;
            }

            return $api_token;
        
        } catch (Exception $ex) {
            

        }
      
    }




    public static function rnd_number(){

        $random_id_length = 10; 
        //generate a random id and store it in $rnd_id 
        //$rnd_id = crypt(uniqid(rand(),1)); 
        $rnd_id = uniqid(rand(),1); 
        //to remove any slashes that might have come 
        $rnd_id = strip_tags(stripslashes($rnd_id)); 
        //Removing any . or / and reversing the string 
        $rnd_id = str_replace(".","",$rnd_id); 
        $rnd_id = strrev(str_replace("/","",$rnd_id)); 
        //finally I take the first 10 characters from the $rnd_id 
        $rnd_id = substr($rnd_id,0,$random_id_length); 
        
        return $rnd_id;

    }
    

    public static function encrypt($input,$secret){//Data encryption
        
         $key=base64_encode($secret);
         $size = mcrypt_get_block_size(MCRYPT_RIJNDAEL_128,MCRYPT_MODE_ECB);
         $input = self::pkcs5_pad($input, $size);
         //$key = str_pad($this->key,24,'0');
         $key = str_pad($key,24,'0');


         $td = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', MCRYPT_MODE_ECB, '');
        // $iv = @mcrypt_create_iv (mcrypt_enc_get_iv_size($td), MCRYPT_RAND);//The initialization vector
        $iv = mcrypt_create_iv(mcrypt_get_iv_size(MCRYPT_3DES,MCRYPT_MODE_ECB), MCRYPT_RAND);
         @mcrypt_generic_init($td, $key, $iv);
         $data = mcrypt_generic($td, $input);
         mcrypt_generic_deinit($td);
         mcrypt_module_close($td);
            $data = base64_encode(self::PaddingPKCS7($data));
         // $data = base64_encode($data);
         return  $data;
    }

    public function encryptnew($data, $key)
    {
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_ECB, '');
         $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        // $iv = base64_decode($iv);
        $data = self::PaddingPKCS7($data);
        // $key = base64_decode($key);
        @mcrypt_generic_init($td, $key, $iv);
        
        $ret = base64_encode(mcrypt_generic($td, $data));
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $ret;
    }

    public function pkcs5_pad ($text, $blocksize) {
     $pad = $blocksize - (strlen($text) % $blocksize);
     return $text . str_repeat(chr($pad), $pad);
     }

    public static function PaddingPKCS7($data)
        {
        $block_size = mcrypt_get_block_size('tripledes', 'ecb');
        $padding_char = $block_size - strlen($data) % $block_size;
        $data .= str_repeat(chr($padding_char), $padding_char);
        return $data;
    }
    
    public static function getDecrypt($data, $key){
        // echo '<br>DECRYPT'.'<br>'.$data;
        $td = mcrypt_module_open(MCRYPT_3DES, '', MCRYPT_MODE_CBC, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        // $iv = base64_decode(iv);
        // $key = base64_decode(key);
        @mcrypt_generic_init($td, $key, $iv);
        $ret = trim(mdecrypt_generic($td, base64_decode($data)));

        echo $ret;
        $ret = self::UnPaddingPKCS7($ret);

        // echo $ret;

        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        // echo $ret;
        return $ret;
    }

    public static function UnPaddingPKCS7($text){

       $pad = ord($text{strlen($text) - 1});
        if ($pad > strlen($text)) {
            return false;
        }
        echo strspn($text, chr($pad), strlen($text) - $pad);
        if (strspn($text, chr($pad), strlen($text) - $pad) != $pad) {
            return false;
        }
        echo $text;
        return substr($text, 0, -1 * $pad);




    }


    public static function getTDESChecksum($data,$secret){
        
        $encrypted = '';

        // echo 'In'.$data;
        
        //Generate a key from a hash
        $key = md5(utf8_encode($secret), true);

        //Take first 8 bytes of $key and append them to the end of $key.
        $key .= substr($key, 0, 8);

        //Pad for PKCS7
        $blockSize = mcrypt_get_block_size('tripledes', 'ecb');

      
        $len = strlen($data);
        $pad = $blockSize - ($len % $blockSize);
        $data .= str_repeat(chr($pad), $pad);

        //Encrypt data
        $encData = mcrypt_encrypt('tripledes', $key, $data, 'ecb');

        return base64_encode($encData);

        return $encrypted;
    }
    
    public static function  checksum($mprhase, $crypt = 'encrypt') {
        
        $MASTERKEY = Config::get('constants.BOOST_SECRET_KEY_DEV');
        $td = mcrypt_module_open('tripledes', '', 'ecb', '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td), MCRYPT_RAND);
        mcrypt_generic_init($td, $MASTERKEY, $iv);
        if ($crypt == 'encrypt')
        {
            $return_value = base64_encode(mcrypt_generic($td, $mprhase));
        }
        else
        {
            $return_value = mdecrypt_generic($td, base64_decode($mprhase));
        }
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);
        return $return_value;
} 
    
    
   
    private static function ApiCallerGet($endPoint,$param,$isAuthentication = false){

        if (Config::get('constants.ENVIRONMENT') == 'live') {
            $env = Config::get('constants.BOOST_ENV_PRO');
            $envAuthenticate = Config::get('constants.BOOST_ENV_PRO_AUTHENTICATE');
        }else{
            $env = Config::get('constants.BOOST_ENV_DEV');
            $envAuthenticate = Config::get('constants.BOOST_ENV_DEV_AUTHENTICATE');
        }
         $data = json_encode($param);

         $apiToken=$param['apitoken'];
         $merchantId = $param['merchantId'];
         $onlineRefNum = $param['onlineRefNum'];

        if($isAuthentication){
            $URL = $envAuthenticate.$endPoint;
            $header = array(
                'Content-Type: application/json'

                );
        }else{
            $URL = $env.$endPoint.'/'.$merchantId.'/'.$onlineRefNum;
            // $apiToken = self::authetication();
            // echo 'API'.$URL;
            $header = array(
                'Content-Type: application/json',
                'Authorization: '. 'Bearer '.$apiToken
            );
        }
       
       

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        // curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
       
        $result = curl_exec($ch);
        
        
        return $result;


    }  
    
   
    private static function ApiCaller($endPoint,$param,$isAuthentication = false){
            // echo $isAuthentication;
        // die();
        if (Config::get('constants.ENVIRONMENT') == 'live') {
            $env = Config::get('constants.BOOST_ENV_PRO');
            $envAuthenticate = Config::get('constants.BOOST_ENV_PRO_AUTHENTICATE');
        }else{
            $env = Config::get('constants.BOOST_ENV_DEV');
            $envAuthenticate = Config::get('constants.BOOST_ENV_DEV_AUTHENTICATE');
        }


        $data = json_encode($param);

        // echo "uuuu<pre>"; 
        // print_r($data);
        //  echo "</pre>uuuu";
        //  echo $param['apitoken'];
         // $apiToken = self::ApiAuthentication();

          // echo 'Token :'. $apiToken;    
          $apiToken=$param['apitoken'];


        if($isAuthentication){
            $URL = $envAuthenticate.$endPoint;
            $header = array(
                'Content-Type: application/json'
                // 'Authorization: '. 'Bearer '.$apiToken
                );
        }else{
            $URL = $env.$endPoint;
            // $apiToken = self::authetication();
            $header = array(
                'Content-Type: application/json',
                'Authorization: '. 'Bearer '.$apiToken
            );
        }
        
        // echo "sss<pre>";
        // print_r($data);
        // echo "</pre>sss";
        // echo "<pre>";
        // print_r($header);
        // echo "</pre>";
        // echo $URL;

        // die();

        $ch = curl_init($URL);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
     
        //execute post
        $result = curl_exec($ch);
        
        
          
    

        //  die('Dee');
        return $result;
       
    }
    




    
    
    
}


?>