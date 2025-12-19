<?php 

class GrabPayController extends BaseController {

	const GRABPAY_HMAC_SIGNATURE  = '/grabpay/partner/v2/charge/init'; 
	const GRABPAY_OAUTH_TOKEN 	  = '/grabid/v1/oauth2/token'; 
	const GRABPAY_OAUTH_AUTHORIZE = '/grabid/v1/oauth2/authorize'; 


	public function Index()
    {
        
        echo "Page not found.";
        return 0;
         
    }


public function generate(){
    $flag = 0;
    
    // print_r(Input::all());
    // die();
    $hmacsig = Input::get('hmacs'); 
    $gmtdate = Input::get('gmtdate'); 
    $params = Input::get('params'); 
    
    // $hmacsig = Input::get('hmacs'); 
    // $gmtdate = Input::get('date'); 
    // $params = Input::get('params'); 
    $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => 'https://partner-api.grab.com/grabpay/partner/v2/charge/init',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => $params,
          CURLOPT_HTTPHEADER => array(
            'Authorization:'. $hmacsig,
            'Date: '.$gmtdate,
            'Content-Type: application/json'
            ),
        ));
    
    $resp = curl_exec($curl);
    
    curl_close($curl);
    
    $APIResponse = json_decode($resp,true);    
    $requeststatus = $APIResponse['request'];
    $partnertxid = $APIResponse['partnerTxID'];
    // print_r($APIResponse);
    if(isset($requeststatus) && $requeststatus <>''){
        $flag = 1;
    }
    
    // return array(
    //         "response"=>json_decode($resp),
    //         "status"=> $flag,
    //     );
      return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_graburl_view')->with('flag', $flag)->with('transaction_id', Input::get('transaction_id'))->with('request', $requeststatus)->with('partnertxid', $partnertxid);
   
}

public function codeverifier(){
    $flag = 0; 
    
    // print_r(Input::all());
    
    $transaction_id = Input::get('transaction_id'); 
    $state = Input::get('state'); 
    $code_verifier = Input::get('code_verifier'); 
    $request = Input::get('request'); 
    $partnertxid = Input::get('partnertxid'); 
    $getcode = Input::get('getcode'); 
    
    $TransResult = DB::table('jocom_grabpay_code')->where('transaction_id','=',$transaction_id)->first();
    if(count($TransResult) > 0){
        
        $sql = DB::table('jocom_grabpay_code')
                    ->where('transaction_id', $transaction_id)
                    ->update(array('state_grs' => $state,
                                    'code_verifier' => $code_verifier,
                                    'modify_by' => 'API_UPDATE',
                                    'updated_at' => date("Y-m-d H:i:s")));
        $flag = 1; 
    }
    else {
        $callbackid =  DB::table('jocom_grabpay_code')->insertGetId(array(
                                    'transaction_id' => $transaction_id,
                                    'state_grs'      => $state,
                                    'code_verifier'  => $code_verifier,
                                    'partnertxid'    => $partnertxid,
                                    'request'        => $request,
                                    'status'         => 0,
                                    'insert_by'      => 'API_APDATE',
                                    'created_at'     => date("Y-m-d H:i:s"),
                                    'modify_by'      => 'API_APDATE',
                                    'updated_at'     => date("Y-m-d H:i:s")
                                    )
                            );
        $flag = 1;
    }

    return Redirect::to($getcode);
    // return $flag;
    
}

public function grabcomplete(){
    $flag = 0; 
    $transid = 0;
    
    $oauth2_token_otc = Input::get('oauth2_token_otc'); 
    $pop = Input::get('pop'); 
    $gmttime = Input::get('gmttime'); 
    $partnertxid = Input::get('partnertxid');

    $params = json_encode(array(
                          "partnerTxID"=>$partnertxid
                    ));
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://partner-api.grab.com/grabpay/partner/v2/charge/complete?currency=MYR',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS => $params,
      CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer '.$oauth2_token_otc,
        'X-GID-AUX-POP: '.$pop,
        'Content-Type: application/json',
        'Date: '.$gmttime
      ),
    ));
    
    $response = curl_exec($curl);
    
    curl_close($curl);
    // echo $response;
    
    $APIResponse = json_decode($response,true);    
    $grabstatus  = $APIResponse['status'];
    
    if(isset($grabstatus) && $grabstatus =='success'){
        $flag = 1; 
        
        $TransResult = DB::table('jocom_grabpay_code')->where('partnertxid','=',$partnertxid)->first();
        if(count($TransResult) > 0){
            $transid = $TransResult->transaction_id; 
        }
            
       $data['transaction_id']  = $transid; 
       $data['paymentstatus'] = $APIResponse['status'];
        
        $insertres =  DB::table('jocom_grabpay_transaction')->insertGetId(array(
                                    'transaction_id' => $transid,
                                    'partnertxid'    => $partnertxid,
                                    'description'    => $APIResponse['description'],
                                    'msgid'          => $APIResponse['msgID'],
                                    'paymentmethod'  => $APIResponse['paymentMethod'],
                                    'reason'         => $APIResponse['reason'],
                                    'status'         => $APIResponse['status'],
                                    'txid'           => $APIResponse['txID'],
                                    'txstatus'       => $APIResponse['txStatus'],
                                    'api_response'   => json_encode($APIResponse),
                                    'insert_by'      => 'API_APDATE',
                                    'created_at'     => date("Y-m-d H:i:s"),
                                    'modify_by'      => 'API_APDATE',
                                    'updated_at'     => date("Y-m-d H:i:s")
                                    )
                            );
        
        $transactionType = MCheckout::Grab_transaction_complete($data);
        
                $transaction = Transaction::find($transid);
                $checkout_source = $transaction->checkout_source;
               
                if($checkout_source == 2){
                    return Redirect::to('https://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=success');
                     exit;
                 }
                 else{
                   return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_grabmsg_view')->with('status', $flag);
                 }
        
    }
    else
    {
         $flag = 0; 
        $insertres =  DB::table('jocom_grabpay_transaction')->insertGetId(array(
                                    'transaction_id' => $transid,
                                    'partnertxid'    => $partnertxid,
                                    'description'    => $APIResponse['description'],
                                    'msgid'          => $APIResponse['msgID'],
                                    'paymentmethod'  => $APIResponse['paymentMethod'],
                                    'reason'         => $APIResponse['reason'],
                                    'status'         => $APIResponse['status'],
                                    'txid'           => $APIResponse['txID'],
                                    'txstatus'       => $APIResponse['txStatus'],
                                    'api_response'   => json_encode($APIResponse),
                                    'insert_by'      => 'API_APDATE',
                                    'created_at'     => date("Y-m-d H:i:s"),
                                    'modify_by'      => 'API_APDATE',
                                    'updated_at'     => date("Y-m-d H:i:s")
                                    )
                            );
        
        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_grabmsg_view')->with('status', $flag);
    }
    
    
    

    
}

public function sendurl(){
    
    $url = Input::get('url'); 
    
     $callbackid =  DB::table('jocom_grabpay_oauth')->insertGetId(array(
                                    'graburl' => $url,
                                    'insert_by'      => 'API_APDATE',
                                    'created_at'     => date("Y-m-d H:i:s"),
                                    'modify_by'      => 'API_APDATE',
                                    'updated_at'     => date("Y-m-d H:i:s")
                                    )
                            );
                            
    
    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_graburl_view')->with('url', $url);
    // header('Location: '.$url, true, 301);
    
    exit;
}

public static function genaratecode(){
    
    $hmacsig = Input::get('hmacs'); 
    $gmtdate = Input::get('date'); 
    
    $curl = curl_init();

    curl_setopt_array($curl, array(
      CURLOPT_URL => 'https://partner-api.grab.com/grabpay/partner/v2/charge/init',
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_ENCODING => '',
      CURLOPT_MAXREDIRS => 10,
      CURLOPT_TIMEOUT => 0,
      CURLOPT_FOLLOWLOCATION => true,
      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
      CURLOPT_CUSTOMREQUEST => 'POST',
      CURLOPT_POSTFIELDS =>'{"partnerGroupTxID":"TestPayment0001","partnerTxID":"c782659e8b544c06be23d1c3167fdc86","currency":"MYR","amount":127,"description":"test charge","merchantID":"3d846079-d36c-41cc-98bc-92624cbe840a"}',
      CURLOPT_HTTPHEADER => array(
        'Authorization:'. $hmacsig,
        'Date: '.$gmtdate,
        'Content-Type: application/json'
        ),
    ));

$respo = curl_exec($curl);


curl_close($curl);

return $respo;

}
    
public function encode($data) {
    return str_replace(['+', '/'], ['-', '_'], base64_encode($data));
}

public function decode($data) {
    return base64_decode(str_replace(['-', '_'], ['+', '/'], $data));
}




    public static function authenticate(){

    	 if (Config::get('constants.ENVIRONMENT') == 'live') {
            $apiClientID = Config::get('constants.GRAB_EXPRESS_CLIENTID_LIVE');
            $apiSecret = Config::get('constants.GRAB_EXPRESS_CLIENT_SECRET_LIVE');
        }else{
            $apiClientID = Config::get('constants.GRAB_EXPRESS_CLIENTID_DEV');
            $apiSecret = Config::get('constants.GRAB_EXPRESS_CLIENT_SECRET_DEV');
        }

        $request = array(
            "client_id" => $apiClientID,
            "client_secret" => $apiSecret,
            "grant_type" => 'client_credentials',
            "scope" => 'grab_express.partner_deliveries',
        );


    	$APIResponse = self::ApiCaller(self::GRAB_EXPRESS_ACCESS_TOKEN	, $request,true);

    	$APIResponseJSON = json_decode($APIResponse,true); 

    	$APIAccessToken = $APIResponseJSON['access_token']; 
    	$APITokenType 	= $APIResponseJSON['token_type']; 
    	$APIExpiresIn 	= $APIResponseJSON['expires_in']; 


    	$response = array('access_token' => $APIAccessToken, 
    					  'token_type' 	 => $APITokenType,
    					  'expires_in'   => $APIExpiresIn,
    				);

    	return $response;
    }
    
    public function anyTesting(){
        
    }
    
    public  function hmacsignatures(){

    		$timezone  = 8; //(GMT -8:00) EST (Kuala Lumpur)
			$gmtdate = gmdate("Y/m/j H:i:s", time() + 3600*($timezone+date("I")));

			$partnerID = Config::get('constants.GRABPAY_ENV_DEV_PARTNER_ID');
         	$partnerSecret = Config::get('constants.GRABPAY_ENV_DEV_PARTNER_SECRET');

         	$PayloadtoSign = '{\"partnerTxID\":\"jc913123\",\"partnerGroupTxID\":\"jc913123\",\"amount\":100,\"currency\":\"SGD\",\"merchantID\":\"3d846079-d36c-41cc-98bc-92624cbe840a\",\"description\":\"Order payment\"}';

         	$hmac = base64_encode(hash_hmac('sha256', $PayloadtoSign, $apiSecret, true));

         	return $hmac;


    }
    
    public function redirect(){
        $data = array(); 
        
        $code   = $_GET['code']; 
        $state  = $_GET['state']; 
        
        if(isset($code)) {
        $result = DB::table('jocom_grabpay_code')
                    ->where('state_grs','=',$state)
                    ->update(array('code' => $code,
                                    'modify_by' => 'API_UPDATE',
                                    'updated_at' => date("Y-m-d H:i:s")));
                                    
        $getresult = DB::table('jocom_grabpay_code')
                        ->where('code','=',$code)
                        ->first();
                        
        
        if(count($getresult)>0){
           
            
            $code_verifier  = $getresult->code_verifier;
            $transaction_id = $getresult->transaction_id;
            $partnertxid    = $getresult->partnertxid;
            
            $redirect_uri = 'https://api.jocom.com.my/grabpay/redirect';

                $params = json_encode(array(
                          "grant_type"=>"authorization_code",
                          "client_id"=>"7e7d07f655b64fcca6257ffb8d5f3faa",
                          "client_secret"=>"_Rjy97nQtq8obtfv",
                          "code_verifier"=>$code_verifier,
                          "redirect_uri"=>"https://api.jocom.com.my/grabpay/redirect",
                          "code"=>$code
                    ));
            
            
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://partner-api.grab.com/grabid/v1/oauth2/token',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>$params,
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                
                // print_r($response);
                
                $APIResponse = json_decode($response,true); 
                
                $callbackid =  DB::table('jocom_grabpay_oauth_token')->insertGetId(array(
                                    'transaction_id' => $transaction_id,
                                    'partnertxid'    => $partnertxid,
                                    'access_token'   => $APIResponse['access_token'],
                                    'token_type'     => $APIResponse['token_type'],
                                    'expires_in'     => $APIResponse['expires_in'],
                                    'api_response'   => json_encode($response),
                                    'insert_by'      => 'API_APDATE',
                                    'created_at'     => date("Y-m-d H:i:s"),
                                    'modify_by'      => 'API_APDATE',
                                    'updated_at'     => date("Y-m-d H:i:s")
                                    )
                            );
                
              return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_grabpay_view')->with('transaction_id', $transaction_id)->with('partnertxid', $partnertxid)->with('APIResponse',$APIResponse);
                
        }
                                    
        // 
        exit;
        }
        else{
             $flag=0;
             $partnertxid = 0;
             $trans = 0;
             $gresult = DB::table('jocom_grabpay_code')
                        ->where('state_grs','=',$state)
                        ->first();
             if(count($gresult)>0){
                $transaction_id = $gresult->transaction_id;
                $transaction = Transaction::find($transaction_id);
                $checkout_source = $transaction->checkout_source;
               
                if($checkout_source == 2){
                    return Redirect::to('https://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=failed');
                     exit;
                 }
                 else{
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_graburl_view')->with('flag', $flag)->with('transaction_id', $trans)->with('request', $requeststatus)->with('partnertxid', $partnertxid);
                     exit;
                 }
                
             }
             
             return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_graburl_view')->with('flag', $flag)->with('transaction_id', $trans)->with('request', $requeststatus)->with('partnertxid', $partnertxid);
        }
        // return $code;
        
    }
    
    public function webhook(){
        $data = array(); 
        
        $code   = $_GET['code']; 
        $state  = $_GET['state']; 
        
        if(isset($code)) {
        $result = DB::table('jocom_grabpay_code')
                    ->where('state_grs','=',$state)
                    ->update(array('code' => $code,
                                    'modify_by' => 'API_UPDATE',
                                    'updated_at' => date("Y-m-d H:i:s")));
                                    
        $getresult = DB::table('jocom_grabpay_code')
                        ->where('code','=',$code)
                        ->first();
                        
        
        if(count($getresult)>0){
           
            
            $code_verifier  = $getresult->code_verifier;
            $transaction_id = $getresult->transaction_id;
            $partnertxid    = $getresult->partnertxid;
            
            $redirect_uri = 'https://api.jocom.com.my/grabpay/redirect';

                $params = json_encode(array(
                          "grant_type"=>"authorization_code",
                          "client_id"=>"7e7d07f655b64fcca6257ffb8d5f3faa",
                          "client_secret"=>"_Rjy97nQtq8obtfv",
                          "code_verifier"=>$code_verifier,
                          "redirect_uri"=>"https://api.jocom.com.my/grabpay/redirect",
                          "code"=>$code
                    ));
            
            
                $curl = curl_init();
    
                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://partner-api.grab.com/grabid/v1/oauth2/token',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>$params,
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                  ),
                ));
                
                $response = curl_exec($curl);
                
                curl_close($curl);
                
                // print_r($response);
                
                $APIResponse = json_decode($response,true); 
                
                $callbackid =  DB::table('jocom_grabpay_oauth_token')->insertGetId(array(
                                    'transaction_id' => $transaction_id,
                                    'partnertxid'    => $partnertxid,
                                    'access_token'   => $APIResponse['access_token'],
                                    'token_type'     => $APIResponse['token_type'],
                                    'expires_in'     => $APIResponse['expires_in'],
                                    'api_response'   => $APIResponse,
                                    'insert_by'      => 'API_APDATE',
                                    'created_at'     => date("Y-m-d H:i:s"),
                                    'modify_by'      => 'API_APDATE',
                                    'updated_at'     => date("Y-m-d H:i:s")
                                    )
                            );
                
               return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_grabpay_view')->with('transaction_id', $transaction_id)->with('partnertxid', $partnertxid)->with('APIResponse',$APIResponse);
                
        }
                                    
        // 
        exit;
        }
        else{
             $flag=0;
             $partnertxid = 0;
             $trans = 0;
             return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_graburl_view')->with('flag', $flag)->with('transaction_id', $trans)->with('request', $requeststatus)->with('partnertxid', $partnertxid);
        }
    }
    



}