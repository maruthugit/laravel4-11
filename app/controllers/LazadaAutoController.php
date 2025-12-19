<?php

include_once app_path('library/LazopSdk.php');


class LazadaAutoController extends BaseController{
    
    /* NEW LAZADA API V2 */
    
    
    public function getLazadaV2AuthToken(){
        
        /*
         * 1. Check token availability
         *    (Avai)
         *         return token 
         *    ELSE
         *        Generate Auth Code
         *        Call create new token
         */
        
        $isError = false;
        $message = '';
        $responseCode = 1;
        
        //  echo 'In_new';
        //  die();
        try{
           
            DB::beginTransaction();
            
            $validToken = '';
           
            
            $accountType = Input::get("account");
         
            if(strtolower(Config::get('constants.ENVIRONMENT')) == "live"){
                
                switch ($accountType) {
                    case 1:
                        $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'FNN';
                        break;
                    case 2:
                        $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'JOC';
                        break;
                    case 3:
                        $app_key = Config::get('constants.LAZADA_ETIKA_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_ETIKA_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'ETI';
                        break;
                    case 4:
                        $app_key = Config::get('constants.LAZADA_YEOS_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_YEOS_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'YEO';
                        break;
                    case 5:
                        $app_key = Config::get('constants.LAZADA_STARBUCKS_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_STARBUCKS_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'STR';
                        break;
                    case 6:
                        $app_key = Config::get('constants.LAZADA_POKKA_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_POKKA_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'POK';
                        break;
                    case 7:
                        $app_key = Config::get('constants.LAZADA_EBFROZEN_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_EBFROZEN_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'EBF';
                        break;
                    case 8:
                        $app_key = Config::get('constants.LAZADA_EVERBEST_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_EVERBEST_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'EVE';
                        break;
                    case 9:
                        $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'JEX';
                        break;
                        
                    default:
                        
                        $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'FNN';
                        break;
                }

                $app_auth_url = Config::get('constants.LAZADA_V2_URL_PATH');
                $URLAuthTokenService = Config::get('constants.LAZADA_V2_AUTH_TOKEN_URL');
                
            }else{
                //echo "DEV";
                
                switch ($accountType) {
                    case 1:
                        $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'FNN';
                        break;
                    case 2:
                        $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'JOC';
                        break;
                    case 3:
                        $app_key = Config::get('constants.LAZADA_ETIKA_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_ETIKA_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'ETI';
                        break;
                    case 4:
                        $app_key = Config::get('constants.LAZADA_YEOS_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_YEOS_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'YEO';
                        break;
                    case 5:
                        $app_key = Config::get('constants.LAZADA_STARBUCKS_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_STARBUCKS_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'STR';
                        break;
                    case 6:
                        $app_key = Config::get('constants.LAZADA_POKKA_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_POKKA_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'POK';
                        break;
                    case 7:
                        $app_key = Config::get('constants.LAZADA_EBFROZEN_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_EBFROZEN_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'EBF';
                        break;
                    case 8:
                        $app_key = Config::get('constants.LAZADA_EVERBEST_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_EVERBEST_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'EVE';
                        break;
                    case 9:
                        $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'JEX';
                        break;
                    default:
                        
                        $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                        $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                        $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                        $appCode = 'FNN';
                        break;
                }

                $app_auth_url = Config::get('constants.LAZADA_V2_URL_PATH');
                $URLAuthTokenService = Config::get('constants.LAZADA_V2_AUTH_TOKEN_URL');
            }
            // print_r(array("api_key" => $app_key, "api_secret" => $app_secret_id));
            // echo $appCode;
            // die();
            $AuthCodeData = LazadaAuthCode::where("status",1)->where("app_code",$appCode)->first();
            // echo "<pre>";
            // print_r($AuthCodeData);
            // echo "</pre>";
            //  die();

            if($AuthCodeData){
                // check token expiry
                $AuthTokenData = LazadaAuthToken::where("activation",1)->where("app_code",$appCode)->where("auth_code",$AuthCodeData->code)->where("access_token","<>","")->first();
                //   echo "<pre>";
                //   echo $appCode;
                //   echo $AuthCodeData->code;
                // print_r($AuthTokenData);
                // echo "</pre>";
                // die();
      
                if($AuthTokenData){
                    
                    $validRefreshToken = $AuthTokenData->refresh_token;
            
                    
                    // DB::table('jocom_lazada_auth_token')
                    //     ->where('activation', 1)
                    //     ->update(['activation' => 0]);
                    
                    $AuthCode = $AuthCodeData->code;
                    $AccessTokenClient = new LazopClient($URLAuthTokenService, $app_key, $app_secret_id);
                    
           
                    
                    $request = new LazopRequest('/auth/token/refresh','GET');
                    $request->addApiParam('refresh_token',(string)$validRefreshToken);
                   
                 
                    $responseAccessToken = $AccessTokenClient->execute($request);
                    
                    // echo "<pre>";
                    // die($responseAccessToken);
                    // echo "</pre>";
                    // die();
                   
                    
                    $responseAccessTokenArray = json_decode($responseAccessToken,true);
           
                   
                   
                    $LazadaAuthToken= new LazadaAuthToken();
                    $LazadaAuthToken->auth_code = $AuthCode;
                    $LazadaAuthToken->access_token = $responseAccessTokenArray['access_token'];
                    $LazadaAuthToken->refresh_token = $responseAccessTokenArray['refresh_token'];
                    $LazadaAuthToken->country = $responseAccessTokenArray['country'];
                    $LazadaAuthToken->refresh_expires_in = $responseAccessTokenArray['refresh_expires_in'];
                    $LazadaAuthToken->auth_code = $AuthCode;
                    $LazadaAuthToken->app_code = $appCode;
                    $LazadaAuthToken->save();
               
                    $validToken = $responseAccessTokenArray['access_token'];
                    
                    
                }else{
                    
                   
                    $AuthCode = $AuthCodeData->code;
                  
                    $AccessTokenClient = new LazopClient($URLAuthTokenService, $app_key, $app_secret_id);
                    $request = new LazopRequest('/auth/token/create','GET');
                    $request->addApiParam('code',(string)$AuthCode);
         
                    $responseAccessToken = $AccessTokenClient->execute($request);
                  
                    $responseAccessTokenArray = json_decode($responseAccessToken,true);
                    
                    // echo "<pre>";
                    // die($responseAccessToken);
                    // echo "</pre>";
                    // die();
                    
                    $LazadaAuthToken= new LazadaAuthToken();
                    $LazadaAuthToken->auth_code = $AuthCode;
                    $LazadaAuthToken->access_token = $responseAccessTokenArray['access_token'];
                    $LazadaAuthToken->refresh_token = $responseAccessTokenArray['refresh_token'];
                    $LazadaAuthToken->country = $responseAccessTokenArray['country'];
                    $LazadaAuthToken->refresh_expires_in = $responseAccessTokenArray['refresh_expires_in'];
                    $LazadaAuthToken->auth_code = $AuthCode;
                    $LazadaAuthToken->app_code = $appCode;
                    $LazadaAuthToken->save();
             
                    
                    $validToken = $responseAccessTokenArray['access_token'];
                    
                }
                
                
            }else{
                
                $needAuthentication = 1;
                if(strtolower(Config::get('constants.ENVIRONMENT')) == "live"){
                    $callbackURL = 'https://api.tmgrocer.com/lazada/authcallback/'.$appCode;
                }else{
                    $callbackURL = 'https://uat.all.jocom.com.my/lazada/authcallback/'.$appCode;
                }
                $authenticationURL = 'https://auth.lazada.com/oauth/authorize?response_type=code&force_auth=true&redirect_uri='.$callbackURL.'&client_id='.$app_key;
                 
                throw new Exception("Auth Code Expired / Invalid . Email has been sent to authorized user to grant new access");
                
               
            }
         
            
            
        } catch (Exception $ex) {
            
            $message = $ex->getMessage();
            echo $message;
            $responseCode = 0;
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }

        }  finally {
            
            return array(
                "responseCode" => $responseCode,
                "message" => $message,
                "data"=>array( 
                    "valid_token" => $validToken,
                    "need_authentication" => $needAuthentication,
                    "authentication_url" => $authenticationURL,
                )
            ); 
            
        }
        
       
    
    }
    
    public function authcallback($appcode){
        
        $isError = false;
        $message = '';
        $responseCode = 1;
         
        try{
            
            $code = $_GET["code"];
            
            $LazadaAuthCodeStr = $code;
            
            if(strlen($LazadaAuthCodeStr) > 0 ){
            
                // $OldAuthCode = LazadaAuthCode::where("status",1)->first();
                // if($OldAuthCode){
                //     $OldAuthCode->status = 0;
                //     $OldAuthCode->save();
                // }
               
                $LazadaAuthCode = new LazadaAuthCode;
                $LazadaAuthCode->code = $LazadaAuthCodeStr;
                $LazadaAuthCode->generate_datetime = '';
                $LazadaAuthCode->expired_datetime = '';
                $LazadaAuthCode->app_code = $appcode;
                $LazadaAuthCode->status = 1;
               
                $LazadaAuthCode->save();
                
                
                
                
            }else{
                 echo "NO";
            }
        
        }catch (exception $ex){
            
            $isError = true;
            $message = $ex->getMessage();
            $responseCode = 0;
            
        }finally{
            
            if($isError){
                DB::rollBack();
                return array(
                    "responseCode"=>$responseCode,
                    "message"=>$message,
                    "data"=>array( "auth_code" => $LazadaAuthCodeStr)
                );
            }else{
                DB::commit();
                return Redirect::to('/lazada');
            }
        }
        
    }
    
    public function anyMigrateapiorders(){
      
        $isError = 0;
        $OrderCollection = array();
        $message = "";
        $isNeedAuthentication = 0;
        $authenticationURL = '';
        
        // $host = 'api.tmgrocer.com';
        // $port = 443;
        // $timeout = 10; // seconds
        
        // $fp = @fsockopen($host, $port, $errno, $errstr, $timeout);
        
        // if ($fp) {
        //     echo "✅ Connected to $host on port $port\n";
        //     fclose($fp);
        // } else {
        //     echo "❌ Connection failed: $errstr ($errno)\n";
        // }
        // die();
    //   die('Disabled Temporary');
        try{
            
        set_time_limit(0);
        ini_set('memory_limit', '-1');
            $accounts=array('2','3','4','5','6','7','8','9');
            foreach($accounts as $value){
                $accountType=$value;
            $OrderResponse = self::getOrdersV2($accountType);
            // echo '<pre>';
            // print_r($OrderResponse);
            // echo '</pre>';
            // die('D');
            if(count($OrderResponse["data"]["orders"]) > 0 ){
                // Save new records // 
                $OrderCollection = $OrderResponse["data"]["orders"];
                
               $isSaved = self::saveNewOrderV2($OrderCollection,$accountType);
            }else{
                if($OrderResponse["tokenInfo"]["responseCode"] == 0){
                    $isNeedAuthentication = $OrderResponse["data"]["tokenInfo"]["data"]["need_authentication"];
                    $authenticationURL = $OrderResponse["data"]["tokenInfo"]["data"]["authentication_url"];
                }
            }
            }
            
        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getMessage();
            
        } finally {
            return array(
                "response" => $isError,
                "totalRecords" => count($OrderCollection),
                "need_authentication" => $isNeedAuthentication,
                "authentication_url" => $authenticationURL,
                "message"=>$message,
                "charges"=>$isSaved
            );
        }
         
    }
    
    public function anyManualmigrateapiorders(){
      
        $isError = 0;
        $OrderCollection = array();
        $message = "";
        $isNeedAuthentication = 0;
        $authenticationURL = '';
    
       
        try{
            
        set_time_limit(0);
        ini_set('memory_limit', '-1');
            $accounts= 4; //array('2','3','4','5','6','7','8','9');
            // foreach($accounts as $value){
                $accountType=$accounts;
            $OrderResponse = self::getOrdersV3($accountType);
            // echo 'In';
            echo '<pre>';
            print_r($OrderResponse);
            echo '</pre>';
            
            if(count($OrderResponse["data"]["orders"]) > 0 ){
                // Save new records // 
                $OrderCollection = $OrderResponse["data"]["orders"];
                
               $isSaved = self::saveNewOrderV2($OrderCollection,$accountType);
            }else{
                if($OrderResponse["tokenInfo"]["responseCode"] == 0){
                    $isNeedAuthentication = $OrderResponse["data"]["tokenInfo"]["data"]["need_authentication"];
                    $authenticationURL = $OrderResponse["data"]["tokenInfo"]["data"]["authentication_url"];
                }
            }
            // }
            
        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getMessage();
            
        } finally {
            return array(
                "response" => $isError,
                "totalRecords" => count($OrderCollection),
                "need_authentication" => $isNeedAuthentication,
                "authentication_url" => $authenticationURL,
                "message"=>$message,
                "charges"=>$isSaved
            );
        }
         
    }
    
    public static function callAPIGetPendingOrders($urlClient,$app_key,$app_secret_id,$valid_token,$createAfter){
        
        $listResult = array();
        
        $search = true;
        $totalRecords = 0;
        $listOrders = [];
        
        while($search) {
            
            $c = new LazopClient($urlClient,$app_key,$app_secret_id);
            $request = new LazopRequest('/orders/get','GET');
            // $request->addApiParam('status','pending');
            $request->addApiParam('status','shipped');
            // $request->addApiParam('sort_direction','DESC');
            $request->addApiParam('created_after',$createAfter);
            $request->addApiParam('sort_direction','ASC');
            
            //$request->addApiParam('offset','0');
           
            $requestResponse = $c->execute($request,$valid_token);
            $requestResponseArray = json_decode($requestResponse,true);
        
            // 100 Because Lazada max return orders is 100
            // echo "data count".$requestResponseArray["data"]["count"];
            if(isset($requestResponseArray["data"]["count"]) && $requestResponseArray["data"]["count"] == 100 ){
                
               // Example substr("2019-05-16 17:54:07 +0800",0,19);
               $lastDate =  substr($requestResponseArray["data"]["orders"][99]['created_at'],0,19);
               $datetime = new DateTime($lastDate);
               $createAfter =  $datetime->format(DateTime::ATOM);
            }else{
                $search = false;
            }
            
           
           // print_r($requestResponseArray["data"]["orders"]);
            $totalRecords += $requestResponseArray["data"]["count"];
            //array_merge($listOrders, $requestResponseArray["data"]["orders"]);
            $listOrders[] = $requestResponseArray["data"]["orders"];
           
            
            $listResult = array(
                "data" => array(
                    "count" => $totalRecords,
                    "orders" => $listOrders
                    )
                );
            
            // 
           
        } 
      print_r($listResult);
        return $listResult;
        
        
        
    
    }
    
    public static function getOrdersV2($accountType){
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
            // echo 'In';
            // die();
        try{
            
            set_time_limit(0);
            $masterList = array();
            $data = array();
            $dataReturn = array();
            // Get last insert orders
            $latestOrder = DB::table('jocom_lazada_pre_order')->orderBy('id', 'desc')->first();
            if(count($latestOrder) > 0 ){
                $datetime = new DateTime($latestOrder->order_datetime);
                $createAfter =  $datetime->format(DateTime::ATOM);
            }else{
                $datetime = new DateTime('2017-04-01 00:00:00');
                $createAfter =  $datetime->format(DateTime::ATOM);
            }
            
            $datetime = new DateTime('2017-04-01 00:00:00');
            // $datetime = new DateTime('2021-10-04 00:00:00');
                $createAfter =  $datetime->format(DateTime::ATOM);

            // Define Credential key for lazada API
            switch ($accountType) {
                case 1:
                    $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 2:
                    $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 3:
                    $app_key = Config::get('constants.LAZADA_ETIKA_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_ETIKA_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 4:
                    $app_key = Config::get('constants.LAZADA_YEOS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_YEOS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 5:
                    $app_key = Config::get('constants.LAZADA_STARBUCKS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_STARBUCKS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 6:
                    $app_key = Config::get('constants.LAZADA_POKKA_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_POKKA_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 7:
                    $app_key = Config::get('constants.LAZADA_EBFROZEN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_EBFROZEN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 8:
                    $app_key = Config::get('constants.LAZADA_EVERBEST_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_EVERBEST_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 9:
                    $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                    
                default:

                    $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                    break;
            }

            // Get LAZADA Authentication Token
            if(strtolower(Config::get('constants.ENVIRONMENT')) == "live"){
                $url = 'https://api.tmgrocer.com/lazada/getauthtoken';
            }else{
                $url = 'https://uat.all.jocom.com.my/lazada/getauthtoken';
            }
            
            // echo 'In_1';
            // print_r($fields_string);
            // // die();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // Save response to the variable $data
            $fields_string = array("account"=>$accountType);
           
            curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $data = curl_exec($ch);
   
            // Check the return value of curl_exec(), too
            if ($data === false) {dd(curl_error($ch));
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
           
            // print_r($data);
            // dd(curl_errno($ch));
            // print_r($fields_string);
            // die();
            $responseAccessTokenArray = json_decode($data,true);
        
            // print_r($responseAccessTokenArray);
        //   die();
       
            // Get LAZADA Authentication Token
            $dataReturn["orders"] = $masterList;
         
        
            if( isset($responseAccessTokenArray["responseCode"]) && $responseAccessTokenArray["responseCode"] == '1'){

                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                $request = new LazopRequest('/orders/get','GET');
                $request->addApiParam('status','pending');
                //$request->addApiParam('status','shipped');
                $request->addApiParam('created_after',$createAfter);
                // $request->addApiParam('limit',10);
                $request->addApiParam('sort_direction','ASC');
                // $request->addApiParam('sort_direction','DESC');
                $request->addApiParam('offset','0');
          

                $requestResponse = $c->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
                $requestResponseArray = json_decode($requestResponse,true);
                
                // print_r($requestResponseArray);
            
                //$requestResponseArray = self::callAPIGetPendingOrders($urlClient,$app_key,$app_secret_id,$responseAccessTokenArray["data"]["valid_token"],$createAfter);

                if(isset($requestResponseArray["data"]["orders"])   && count($requestResponseArray["data"]["orders"]) > 0 ){
                    $orders = $requestResponseArray["data"]["orders"];
                }
                $dataReturn["tokenInfo"] = $responseAccessTokenArray;

            }else{
                $dataReturn["tokenInfo"] = $responseAccessTokenArray;
                throw new exception($responseAccessTokenArray["message"]);
            }
            if(count($orders) > 0 ){
                
                // foreach ($orders as $k => $val){
                    
                    
                    foreach ($orders as $keyOrder => $valueOrder) {

                        $order_number = $valueOrder['order_number'];
                        
                        $order = LazadaPreOrder::where("order_number","=",$order_number)->where("from_account","=",$accountType)->get();
                        
                        // Order number already exist
                        // if($order > 0 ){
                        if(count($order) > 0 ){
                            // remove from list
                            unset($orders[$keyOrder]);
                            //echo "OUT";
                        }else{
                            //echo "IN";
                            $LazadaAPIClient = new LazopClient($urlClient,$app_key,$app_secret_id);
                            $request = new LazopRequest('/order/items/get','GET');
                            $request->addApiParam('order_id',$valueOrder['order_id']);
                            $requestResponseDetails = $LazadaAPIClient->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
    
                            $requestResponseDetailsArray = json_decode($requestResponseDetails,true);
    
                            $valueOrder['orderItems'] = $requestResponseDetailsArray['data'];
                            array_push($masterList, $valueOrder);
                        }
                        sleep(1);
    
                    }
                    
                    
                // }

                
                
                $dataReturn["orders"] = $masterList;
            }
            
            
        
        } catch (Exception $ex) {
            $messsage = $ex->getMessage();
            $isError = 1;

        } finally {
            return array(
                "response" => $isError,
                "message" => $messsage,
                "data" => $dataReturn
            );
        }
        
    }
    
    public static function getOrdersV3($accountType){
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
            echo 'In2';
            // die();
        try{
            
            set_time_limit(0);
            $masterList = array();
            $data = array();
            $dataReturn = array();
            // Get last insert orders
            $latestOrder = DB::table('jocom_lazada_pre_order')->orderBy('id', 'desc')->first();
            if(count($latestOrder) > 0 ){
                $datetime = new DateTime($latestOrder->order_datetime);
                $createAfter =  $datetime->format(DateTime::ATOM);
            }else{
                $datetime = new DateTime('2017-04-01 00:00:00');
                $createAfter =  $datetime->format(DateTime::ATOM);
            }
            
            $datetime = new DateTime('2017-04-01 00:00:00');
            // $datetime = new DateTime('2021-10-04 00:00:00');
                $createAfter =  $datetime->format(DateTime::ATOM);

            // Define Credential key for lazada API
            switch ($accountType) {
                case 1:
                    $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 2:
                    $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 3:
                    $app_key = Config::get('constants.LAZADA_ETIKA_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_ETIKA_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 4:
                    $app_key = Config::get('constants.LAZADA_YEOS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_YEOS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 5:
                    $app_key = Config::get('constants.LAZADA_STARBUCKS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_STARBUCKS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 6:
                    $app_key = Config::get('constants.LAZADA_POKKA_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_POKKA_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 7:
                    $app_key = Config::get('constants.LAZADA_EBFROZEN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_EBFROZEN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 8:
                    $app_key = Config::get('constants.LAZADA_EVERBEST_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_EVERBEST_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 9:
                    $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                    
                default:

                    $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                    break;
            }

            // Get LAZADA Authentication Token
            if(strtolower(Config::get('constants.ENVIRONMENT')) == "live"){
                $url = 'https://api.tmgrocer.com/lazada/getauthtoken';
            }else{
                $url = 'https://uat.all.jocom.com.my/lazada/getauthtoken';
            }
            
            // echo 'In_1';
            print_r($url);
            // die();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // Save response to the variable $data
            $fields_string = array("account"=>$accountType);
           
            curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $data = curl_exec($ch);
   
            // Check the return value of curl_exec(), too
            if ($data === false) {dd(curl_error($ch));
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
           
            print_r($data);
            // dd(curl_errno($ch));
            print_r($fields_string);
            
            die();
            $responseAccessTokenArray = json_decode($data,true);
        
        //     print_r($responseAccessTokenArray);
        //   die();
       
            // Get LAZADA Authentication Token
            $dataReturn["orders"] = $masterList;
         
        
            if( isset($responseAccessTokenArray["responseCode"]) && $responseAccessTokenArray["responseCode"] == '1'){

                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                $request = new LazopRequest('/orders/get','GET');
                $request->addApiParam('status','pending');
                //$request->addApiParam('status','shipped');
                $request->addApiParam('created_after',$createAfter);
                // $request->addApiParam('limit',10);
                $request->addApiParam('sort_direction','ASC');
                // $request->addApiParam('sort_direction','DESC');
                $request->addApiParam('offset','0');
          

                $requestResponse = $c->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
                $requestResponseArray = json_decode($requestResponse,true);
                
                // print_r($requestResponseArray);
            
                //$requestResponseArray = self::callAPIGetPendingOrders($urlClient,$app_key,$app_secret_id,$responseAccessTokenArray["data"]["valid_token"],$createAfter);

                if(isset($requestResponseArray["data"]["orders"])   && count($requestResponseArray["data"]["orders"]) > 0 ){
                    $orders = $requestResponseArray["data"]["orders"];
                }
                $dataReturn["tokenInfo"] = $responseAccessTokenArray;

            }else{
                $dataReturn["tokenInfo"] = $responseAccessTokenArray;
                throw new exception($responseAccessTokenArray["message"]);
            }
            if(count($orders) > 0 ){
                
                // foreach ($orders as $k => $val){
                    
                    
                    foreach ($orders as $keyOrder => $valueOrder) {

                        $order_number = $valueOrder['order_number'];
                        
                        $order = LazadaPreOrder::where("order_number","=",$order_number)->where("from_account","=",$accountType)->get();
                        
                        // Order number already exist
                        // if($order > 0 ){
                        if(count($order) > 0 ){
                            // remove from list
                            unset($orders[$keyOrder]);
                            //echo "OUT";
                        }else{
                            //echo "IN";
                            $LazadaAPIClient = new LazopClient($urlClient,$app_key,$app_secret_id);
                            $request = new LazopRequest('/order/items/get','GET');
                            $request->addApiParam('order_id',$valueOrder['order_id']);
                            $requestResponseDetails = $LazadaAPIClient->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
    
                            $requestResponseDetailsArray = json_decode($requestResponseDetails,true);
    
                            $valueOrder['orderItems'] = $requestResponseDetailsArray['data'];
                            array_push($masterList, $valueOrder);
                        }
                        sleep(1);
    
                    }
                    
                    
                // }

                
                
                $dataReturn["orders"] = $masterList;
            }
            
            
        
        } catch (Exception $ex) {
            $messsage = $ex->getMessage();
            $isError = 1;

        } finally {
            return array(
                "response" => $isError,
                "message" => $messsage,
                "data" => $dataReturn
            );
        }
        
    }
    
    public static function saveNewOrderV2($OrderCollection,$accountType){
        
        /*
         * 1. Save Lazada Order
         * 2. Save New Transaction ID
         * 3. Sent Email to person in charge (Successful/Failed Order)
         * 4. 
         */
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $counter = 0;
        $isSaved = true;
        $manualProcess = false;
        $manualProcessOrder = array();
        $errorLog = array();
        $transferedProcessOrder = array();
        $lazadaDeliveryCharges = 0;
        // set_time_limit(0);

        try{
            
            foreach ($OrderCollection as $key => $value) {

                // Assign Value
                $order_number = $value['order_number'];
                $customer_name = $value['customer_first_name']." ".$value['customer_last_name'];
                $customer_email = '';
                $migrate_from = 1;
                $delivery_information = $value['delivery_info'];
                $delivery_postcode = $value['address_shipping']['post_code'];
                $delivery_addr_1 = $value['address_shipping']['address1'];
                $delivery_addr_2 = "" ; 
                $delivery_contact_no = $value['address_shipping']['phone'];
                $delivery_name = $value['address_shipping']['first_name']." ".$value['address_shipping']['last_name'];
                
                
                $tempData = $value;
                unset($tempData['orderItems']);
                
                $order = LazadaPreOrder::where("order_number","=",$order_number)->where("from_account","=",$accountType)->first();
                
                if(!$order && $order==''){
                // saving order information 
                $LazadaOrder = new LazadaPreOrder();
                $LazadaOrder->order_number = $order_number;
                $LazadaOrder->order_id = $order_number;
                $LazadaOrder->customer_name = $customer_name;
                $LazadaOrder->migrate_from = $accountType;
                $LazadaOrder->transaction_id = 0;
                $LazadaOrder->order_datetime = $value['created_at'];
                $LazadaOrder->payment_method = $value['payment_method'];
                $LazadaOrder->remarks = $value['remarks'];
                $LazadaOrder->gift_message = $value['gift_message'];
                $LazadaOrder->delivery_info = $delivery_information;
                $LazadaOrder->delivery_postcode = $delivery_postcode;
                $LazadaOrder->delivery_addr = $delivery_addr_1;
                $LazadaOrder->delivery_contact_no = $delivery_contact_no;
                $LazadaOrder->delivery_name = $delivery_name;
                $LazadaOrder->from_account = $accountType;
                $LazadaOrder->is_completed = 0;
                $LazadaOrder->internal_status= 0;
                $LazadaOrder->api_data_return = json_encode($tempData); 
                $LazadaOrder->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->save();
                
                $OrderID = $LazadaOrder->id;
                // Save Product Details
                if(count($value['orderItems']) > 0){
                    
                    foreach ($value['orderItems'] as $keySub => $valSub) {

                        $LazadaOrderDetails = new LazadaPreOrderDetails();
                        $LazadaOrderDetails->order_id = $OrderID;
                        $LazadaOrderDetails->product_id = $OrderID;
                        $LazadaOrderDetails->product_name = $valSub['name'];
                        $LazadaOrderDetails->sku = $valSub['sku'];
                        $LazadaOrderDetails->variation = $valSub['variation'];
                        $LazadaOrderDetails->shipping_provider_type = $valSub['shipping_provider_type'];
                        $LazadaOrderDetails->order_items_details = json_encode($valSub);
                        $LazadaOrderDetails->status = 1;
                        $LazadaOrderDetails->internal_status= 0;
                        $LazadaOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $LazadaOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $LazadaOrderDetails->save();

                    }
                
                }
                
                }
            }   
            
        } catch (Exception $ex) {
            $isSaved = false;
            echo $ex->getMessage();
        }
        
        finally{
            return 1;
        }
        
        
    }
    
        public function anyManualapisave(){
        
        /*
         * 1. Save Lazada Order
         * 2. Save New Transaction ID
         * 3. Sent Email to person in charge (Successful/Failed Order)
         * 4. 
         */
        $accountType = Input::get('account_type');
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $counter = 0;
        $isSaved = true;
        $manualProcess = false;
        $manualProcessOrder = array();
        $manualsavecount = array();
        $errorLog = array();
        $transferedProcessOrder = array();
        $lazadaDeliveryCharges = 0;
        $isError = 0;
        $message = "";
        // set_time_limit(0);

        try{
            
            $tax_rate = Fees::get_tax_percent();
            $get_orders=LazadaPreOrder::where('from_account','=',$accountType)
                                       ->where('internal_status','=','0')
                                       ->get();
          if($get_orders>0){
            foreach ($get_orders as $key => $value) {

                $order_number = $value->order_number;
                $customer_name = $value->customer_name;
                $customer_email = '';
                $migrate_from = 1;
                $delivery_information = $value->delivery_info;
                $delivery_postcode = $value->delivery_postcode;
                $delivery_addr_1 = $value->delivery_addr;
                $delivery_addr_2 = "" ; 
                $delivery_contact_no = $value->delivery_contact_no;
                $delivery_name = $value->delivery_name;
                
            
                $existOrder =LazadaOrder::where('order_number',$value->order_number)->where('from_account','=',$accountType)->first();
               if(empty($existOrder)){
                // saving order information 
                $LazadaOrder = new LazadaOrder();
                $LazadaOrder->order_number = $order_number;
                $LazadaOrder->order_id = $order_number;
                $LazadaOrder->customer_name = $customer_name;
                $LazadaOrder->migrate_from = $accountType;
                $LazadaOrder->transaction_id = 0;
                $LazadaOrder->order_datetime = $value->order_datetime;
                $LazadaOrder->payment_method = $value->payment_method;
                $LazadaOrder->remarks = $value->remarks;
                $LazadaOrder->gift_message = $value->gift_message;
                $LazadaOrder->from_account = $accountType;
                $LazadaOrder->is_completed = 0;
                $LazadaOrder->api_data_return = $value->api_data_return; 
                $LazadaOrder->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->save();
                
                $OrderID = $LazadaOrder->id;
                $manualsavecount[]=array('order_id'=>$OrderID);
                $local_orders= LazadaPreOrder::where('id','=',$value->id)
                                                  ->where('order_number','=',$value->order_number)
                                                  ->update(['internal_status'=>'1']);
                                                  
            $details=LazadaPreOrderDetails::where('order_id','=',$value->id)
                                                   ->where('internal_status','=','0')
                                                   ->get();
           
                // Save Product Details
                if(count($details) > 0){

                    foreach ($details as $keySub => $valSub) {
                        $LazadaOrderDetails = new LazadaOrderDetails();
                        $LazadaOrderDetails->order_id = $OrderID;
                        $LazadaOrderDetails->product_id = $OrderID;
                        $LazadaOrderDetails->product_name = $valSub->product_name;
                        $LazadaOrderDetails->sku = $valSub->sku;
                        $LazadaOrderDetails->variation = $valSub->variation;
                        $LazadaOrderDetails->shipping_provider_type = $valSub->shipping_provider_type;
                        $LazadaOrderDetails->order_items_details =$valSub->order_items_details;
                        $LazadaOrderDetails->status = 1;
                        $LazadaOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $LazadaOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        if($LazadaOrderDetails->save()){
                            $local_details=LazadaPreOrderDetails::where('order_id','=',$value->id)
                                      ->where('sku','=',$valSub->sku)
                                      ->update(['internal_status'=>'1']);
                        }


                    }
                
                }
            
                
            $username_lazada = 'lazada';
            $password_lazada = '';
            
            
            if ( ! file_exists(Config::get('constants.XML_FILE_PATH').'state.xml')) {
                    $statedb = ElevenStreetOrder::getState();
                    $xml = new XMLWriter();
                    $xml->openURI(Config::get('constants.XML_FILE_PATH').'state.xml');
                    $xml->startDocument('1.0');
                    $xml->startElement('statedb');

                    foreach ($statedb as $value) {
                        $xml->startElement('value');
                        $xml->writeElement('id', $value->id);
                        $xml->writeElement('state_code', $value->state_code);
                        $xml->writeElement('state_name', $value->state_name);
                        $xml->endElement();
                    } 

                    $xml->endElement();
                    $xml->endDocument();
                    $xml->flush();

                    // Session::flash('success', 'success.');
            }
            
            if ( ! file_exists(Config::get('constants.XML_FILE_PATH').'postcode.xml')) {
                    $postcode = ElevenStreetOrder::getPostcode();
                    $xml = new XMLWriter();
                    $xml->openURI(Config::get('constants.XML_FILE_PATH').'postcode.xml');
                    $xml->startDocument('1.0');
                    $xml->startElement('postcode');

                    foreach ($postcode as $value) {
                        $xml->startElement('value');
                        $xml->writeElement('id', $value->id);
                        $xml->writeElement('postcode', $value->postcode);
                        $xml->writeElement('area', $value->area);
                        $xml->writeElement('post_office', $value->post_office);
                        $xml->writeElement('state_code', $value->state_code);
                        $xml->endElement();
                    }

                    $xml->endElement();
                    $xml->endDocument();
                    $xml->flush();

                    // Session::flash('success', 'success.');
            }
            $getpostcode = Transaction::getXMLpostcode($delivery_postcode);

            
            if($getpostcode['status']==1){
                $st_code = $getpostcode['st_code'];
                $post_office = $getpostcode['post_office'];
                $statename   = $getpostcode['state'];        
                $status      = $getpostcode['status']; 
            }
            
            $stateinfo = Transaction::getStateID($statename);
            $state_id = $stateinfo->id;

            $city_idcityid="";
            $cityinfo = Delivery::getCityList($state_id);

            $city_info = Transaction::getCityID($post_office,$state_id);
            $city_id   = $city_info->id;
            // GET XML //

            $country_id = 458;
            
            
            $OrderDataDetails = LazadaOrderDetails::getByOrderID($OrderID);
                
            $qrcode = array();
            $priceopt = array();
            $qty = array();
            $lazadaoriginalpirce = array();
            $lazada_platform_originalpirce = array();
            
            $zeroDelivery = 0;
            
            foreach ($OrderDataDetails as $keyDetailsCheck => $valueDetailsCheck) {
                $APIDataCheck = json_decode($valueDetailsCheck->order_items_details, true);
                if($APIDataCheck['ShippingAmount'] == 0){
                    $zeroDelivery = 1;
                }
            }
            $lazadaDeliveryCharges = 0; 
            
            if(($getpostcode['status']==0) || ($delivery_postcode == "")){
                    // THROW TO MANUAL PROCESS
                   
                    $manualProcess = true;
                    array_push($manualProcessOrder, array(
                        "order_number"=>$order_number,
                        "buyername"=>$customer_name
                    ));

                    $errors = "empty postcode";
                    array_push($errorLog, array(
                        "order_number"=>$order_number,
                        "buyername"=>$customer_name,
                        "error"=>$errors
                    ));
                //echo "-------------------------------------------------MANUAL-------------------------------------------------------------";     
            }else{
                
                $delivery = array();
                $is_skip = 0;
                $extra_message = "";
                if(count($OrderDataDetails) > 0 ){
                foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                        
                    $APIData = json_decode($valueDetails->order_items_details, true);
                    
                           
                    if(count($APIData['sku']) > 0 ){
                        
                        $product_jc_code = $APIData['sku'];
                        //echo "SKU: ".$product_jc_code;
                        if($delivery_information != "" ){
                            $extra_message = $delivery_information;
                        }

                        $ProductInformation = Product::findProductInfoByQRCODE($product_jc_code);
//                        echo "<pre>";
//                            print_r($ProductInformation);
//                        echo "<pre>";
                        if(count($ProductInformation) >  0){
                            //echo "auto";   
                            $qrcode[] = $ProductInformation->qrcode;

                            // CHECK DEFINE LABEL PRICE CODE //
                            $optionName = $APIData['variation'];


                            if(count($optionName) > 0 ){
                                 // TEST CASE 10752
                                //$optionName = '[10753]'.' '.$optionName;

                                if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                                    // TAKE DEFINED LABEL ID //
                                    $selectedPriceOptionID = substr($optionName,strpos($string,"[") + 1,strpos($optionName,"]") -1);
                                    $PriceOption = Price::find($selectedPriceOptionID);

                                    if(count($PriceOption)>0){
                                        $priceopt[] = $selectedPriceOptionID;       
                                    }else{
                                        $priceopt[] = $ProductInformation->ProductPriceID;
                                    }
                                }else{
                                    $priceopt[] = $ProductInformation->ProductPriceID;
                                }

                            }else{
                                $priceopt[] = $ProductInformation->ProductPriceID;
                            }
                            // CHECK DEFINE LABEL PRICE CODE //

                            $qty[] = 1; //$APIData['ordQty'];

                            // Exclusive Amount = Inclusive Tax * 100 / 106
                            $lazadaDeliveryCharges = $lazadaDeliveryCharges + ($APIData['shipping_amount'] * (100/(100 + $tax_rate)));
                            $delivery[] = $lazadaDeliveryCharges;
                            $lazadaoriginalpirce[] = $APIData['item_price'];
                            $lazada_platform_originalpirce[] = $APIData['item_price'];

                        }else{
                            //echo "-------------------------------------------------MANUAL-------------------------------------------------------------"; 
                            $is_skip = 1;
                            $manualProcess = true;
                            array_push($manualProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name
                            ));

                            $errors = $APIData['sku']. " not found";
                            array_push($errorLog, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name,
                                "error"=>$errors,
                            ));

                        }
                    }else{

                        $is_skip = 1;
                        $manualProcess = true;
                        array_push($manualProcessOrder, array(
                            "order_number"=>$order_number,
                            "buyername"=>$customer_name
                        ));

                        $errors = "sku null";
                        array_push($errorLog, array(
                            "order_number"=>$order_number,
                            "buyername"=>$customer_name,
                            "error"=>$errors
                        ));
                        //echo "-------------------------------------------------MANUAL-------------------------------------------------------------"; 
                    }


                }
                }else{
                    $is_skip = 1;
                    $manualProcess = true;
                    array_push($manualProcessOrder, array(
                        "order_number"=>$order_number,
                        "buyername"=>$customer_name
                    ));
                    //echo "-------------------------------------------------MANUAL-------------------------------------------------------------"; 
                }   
                if($is_skip == 0){
                       $lazadasplmeg='';
                       if($accountType == 9){
                           $lazadasplmeg='Lazada Jocom Express';
                       }
                       else{
                           $lazadasplmeg='Lazada';
                       }
                       
                        $get = array(
                            'user'                => $username_lazada,             // Buyer Username
                            'pass'                => $password_lazada,             // Buyer Password
                            'delivery_name'       => $delivery_name,      // delivery name
                            'delivery_contact_no' => $delivery_contact_no, // delivery contact no
                            'special_msg'         => 'Transaction transfer from '.$lazadasplmeg.' ( Order Number : '.$order_number.' )'. " ".$extra_message,       // special message
                            'delivery_addr_1'     => $delivery_addr_1,
                            'delivery_addr_2'     => $delivery_addr_2,
                            'delivery_postcode'   => $delivery_postcode,
                            'delivery_city'       => $city_id, // City ID 
                            'delivery_state'      => $state_id,                          // State ID
                            'delivery_country'    => $country_id,                 // Country ID
                            'lazadaDeliveryCharges'                    => $lazadaDeliveryCharges,  
                            'qrcode'              => $qrcode,
                            'price_option'        => $priceopt, // Price Option
                            'qty'                 => $qty,
                            'lazadaoriginalpirce' => $lazadaoriginalpirce,
                            'lazada_platform_originalpirce' => $lazada_platform_originalpirce,
                            'devicetype'          => 'cms',
                            'uuid'                => NULL, // City ID
                            'lang'                => 'EN',
                            'ip_address'          => Request::getClientIp(),
                            'location'            => '',
                            'transaction_date'    => date("Y-m-d H:i:s"),
                            'charity_id'          => '',
                        );
                        
                
                        // Create Transaction //
                        $data = MCheckout::checkout_transaction($get);
                       
                                
                        if($data['status'] == "success"){

                            $transaction_id = $data["transaction_id"];
                            
                            
                            
                            // PUSH TO SUCCESS LIST 
                            array_push($transferedProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name,
                                "transactionID"=>$transaction_id
                            )); 
                            
                            
                            
                            // SAVE AS COMPLETED TRANSACTION //
                            $trans = Transaction::find($transaction_id);
                            $trans->status = 'completed';
                            $trans->modify_by = 'API';
                            $trans->modify_date = date("Y-m-d h:i:sa");
                            $trans->save();
                            // SAVE AS COMPLETED TRANSACTION //

                            // CREATE INV
                            //$tempInv = MCheckout::generateInv($transaction_id, true);
                            // CREATE PO
                            //$tempPO = MCheckout::generatePO($transaction_id, true);
                            // CREATE DO
                            //$tempDO = MCheckout::generateDO($transaction_id, true);

                            // Update Status 11Street Order as transfered
                            if($OrderID != 0){
                                $LazadaOrder = LazadaOrder::find($OrderID);
                                $LazadaOrder->status = 2;
                                $LazadaOrder->transaction_id = $transaction_id;
                                $LazadaOrder->save();
                            }

                        }else{
                            $manualProcess = true;
                            array_push($manualProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name
                            ));

                            $errors = $data['message'];
                            array_push($errorLog, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name,
                                "error"=>$errors
                            ));
                            // THROW ERROR FAILED TO CREATE TRANSACTION
                        }
                        
                    }
                    
            }
            }   

            }   
            
            switch ($accountType) {
                case 1:
                    $acc_name = "F&N";  
                    break;
                case 2:
                    $acc_name = "JOCOM";  
                    break;
                case 3:
                    $acc_name = "ETIKA";  
                    break;
                case 4:
                    $acc_name = "YEOS";  
                    break;
                case 5:
                    $acc_name = "STARBUCKS";  
                    break;
                case 6:
                    $acc_name = "POKKA";  
                    break;
                case 7:
                    $acc_name = "EBFROZEN";  
                    break;
                case 8:
                    $acc_name = "EVERBEST";  
                    break;
                case 9:
                    $acc_name = "JCMEXPRESS";  
                    break;
                default:
                   $acc_name = "F&N";  
                    break;
            }
            
            // MANUAL PROCESS HANDLING
            //if($manualProcess){
                // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
                $subject = "Migration Notification";
                $recipient = array(
                    "email"=>Config::get('constants.ElevenStreetManagerEmail'),
                    //"name"=> "Wira Izkandar"
                );
                $data = array(
                        'execution_datetime'      => date("Y-m-d H:i:s"),
                        'total_records'  => count($manualsavecount),
                        'manual_process'  => count($manualProcessOrder),
                        'manual_order_list'  => $manualProcessOrder,
                        'transfered_orders'  => $transferedProcessOrder,
                        'acc_name'  => $acc_name,
                );

                Mail::send('emails.lazadamigratereport', $data, function($message) use ($recipient,$subject)
                {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($recipient['email'], $recipient['name'])
                            // ->cc(Config::get('constants.ElevenStreetManagerEmailCC'))
                            ->cc(['quenny@jocom.my', 'barrylwm@jocom.my'])
                            ->subject($subject);
                });
                
                
                
                $running_number = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'batch_no')->first();
                
                $batchNo = str_pad($running_number->counter + 1,10,"0",STR_PAD_LEFT);
                $NewRunner = Running::find($running_number->id);
                $NewRunner->counter = $running_number->counter + 1;
                $NewRunner->save();
                
                //self::maruthu($transferedProcessOrder);
                //self::transactionDeliverytime24h($batchNo, $transferedProcessOrder);
                
                self::transactionDeliverytime24h($batchNo, $transferedProcessOrder);
                
            //}
                /*
                foreach ($errorLog as $key => $value) {

                    $log = DB::table('jocom_thirdparty_log')->where('order_number','=',$value['order_number'])->first();

                    if (empty($log)) {

                        DB::table('jocom_thirdparty_log')->insert(array(
                                    'platform'=>Config::get('constants.lazada_code'),  
                                    'order_number'=>$value['order_number'],
                                    'buyername'=>$value['buyername'],
                                    'response'=>$value['error'], 
                                    'insert_date'=>date("Y-m-d H:i:s")
                                    )
                                );
                    }
                    
                } */

            }
        } catch (Exception $ex) {
            $isSaved = false;
            $isError = 1;
            $message = $ex->getMessage();
        }
       finally {
            return array(
                "response" => $isError,
                "totalRecords" =>count($manualsavecount),
                "message"=>$message,
                "charges"=>$isSaved
            );
        }
        
        
    }
    public static function transactionDeliverytime24h($batchno,$transactioncollections = array()){

            $arr = array();

            foreach ($transactioncollections as $key => $value) {
                $transactionlabels= DB::table('jocom_transaction_details')
                                        ->select('sku','price_label','unit','transaction_id')
                                        ->where('transaction_id',$value['transactionID'])
                                        ->where('delivery_time','24 hours')
                                        ->get();

                    foreach ($transactionlabels as $key2 => $val) {

                            DB::table('jocom_transaction_group')->insert(array(
                                    'sku'  => $val->sku,
                                    'price_label' => $val->price_label,
                                    'unit' => $val->unit,
                                    'transaction_id' => $val->transaction_id,
                                    'batch_no' => $batchno,
                                    'created_at'=>date("Y-m-d H:i:s"),

                                    )
                                );
                        }                    
            }

            $returnval= DB::table('jocom_transaction_group AS JTG ')
                            ->select('JTG.batch_no','JTG.sku','JP.name','JTG.price_label',DB::raw('SUM(JTG.unit) as quantity'))
                            ->leftJoin('jocom_products AS JP', 'JTG.sku', '=', 'JP.sku')
                            ->where('JTG.batch_no',$batchno)
                            ->groupby('JTG.sku')
                            ->get();

                          
            // foreach ($returnval as $rvalue) {
            //     $trnsArray  = array();  
            //     $transactionlist = DB::table('jocom_transaction_group')->select('transaction_id')  
            //                             ->where('batch_no',$batchno)
            //                             ->where('sku',$rvalue->sku)
            //                             ->get();
            //                     $str = "";
            //                     foreach ($transactionlist as $key3 => $tranvalue) {
                                            
            //                                 $arr_val = array('transaction_id' => $tranvalue->transaction_id);
            //                                 if($str == ""){
            //                                     $str = $tranvalue->transaction_id;
            //                                 }else{
            //                                     $str = $str.",".$tranvalue->transaction_id;
            //                                 }
                                           
            //                             // array_push($trnsArray, $arr_val);   
            //                           }      
            //                       // $str = implode(",", $arr_val->transaction_id);
            //     $array =  array('batchno' => $batchno,
            //                     'sku' => $rvalue->sku,
            //                     'product_name' => $rvalue->name,
            //                     'price_label' => $rvalue->price_label,
            //                     'quantity' => $rvalue->quantity,
            //                     'transaction_id' => $str,

            //      );
            //     array_push($arr, $array);
                
               
            // }
            
            // self::createCSV($batchno,$arr);
            
            return $arr;

         

    }
    
    
    
    
}