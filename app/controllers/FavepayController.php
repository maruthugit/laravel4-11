<?php 

class FavepayController extends BaseController {

    const FAVEPAY_API_PATH = '/api/fpo/v1/'; 
    const FAVEPAY_QR_CODE_API = '/qr_codes';
    const FAVEPAY_GET_TRANSACTION = '/transactions'; 
    const FAVEPAY_ACK_TRANSACTION = '/transactions/'; 


    public function index()
    {
        echo "Page not found.";
        return  0;
    }



    /*
     * @desc : Signature Generation, An API request signature (sign) generated using the HMAC-SHA256 algorithm
     */


    public static function callsignature($trans_id,$email,$contact,$amount,$location)
    {

            if (Config::get('constants.ENVIRONMENT') == 'live') {
                $apiAppId = Config::get('constants.FAVEPAY_ENV_PRO_APP_ID');
                $apiOutletID = Config::get('constants.FAVEPAY_ENV_PRO_OUTLET_ID');
                $apiResponse = Config::get('constants.FAVEPAY_ENV_PRO_REDIRECT_URL');
                $apiCallback = Config::get('constants.FAVEPAY_ENV_PRO_CALLBACK_URL');
                $prefix = Config::get('constants.FAVEPAY_ENV_PRO_PREFIX');

            }else{
                $apiAppId = Config::get('constants.FAVEPAY_ENV_DEV_APP_ID');
                $apiOutletID = Config::get('constants.FAVEPAY_ENV_DEV_OUTLET_ID');
                $apiResponse = Config::get('constants.FAVEPAY_ENV_DEV_REDIRECT_URL');
                $apiCallback = Config::get('constants.FAVEPAY_ENV_DEV_CALLBACK_URL');
                $prefix = Config::get('constants.FAVEPAY_ENV_DEV_PREFIX');  
            }

   
         $omni = $prefix.'-'.$trans_id;
         $totlamt = $amount;
         $app_id = $apiAppId; 
         $outlet_id = $apiOutletID;
    //   $email = urlencode('shopper_details[email]').'='. urlencode($email); 
    //   $phone = urlencode('shopper_details[phone]').'='.urlencode($contact); 
    //   $location =urlencode('shopper_details[location]').'='.preg_replace('/\s+/', '+', urlencode($location)); 
         $format = 'web_url'; 
         $redirect_url = urlencode($apiResponse); 
         $callback_url = urlencode($apiCallback); 

    //    $checkSumString = 'omni_reference='.$trans_id.'&total_amount_cents='.$totlamt.'&app_id='.$app_id.'&outlet_id='.$outlet_id.'&'.$email.'&'.$phone.'&'.$location.'&redirect_url='.$redirect_url.'&callback_url='.$callback_url.'&format='.$format.'&test=true&client_integration=jocom'; 
          $checkSumString = 'omni_reference='.$omni.'&total_amount_cents='.$totlamt.'&app_id='.$app_id.'&outlet_id='.$outlet_id.'&'.$email.'&'.$contact.'&'.$location.'&redirect_url='.$redirect_url.'&callback_url='.$callback_url.'&format='.$format.'&test=false&client_integration=jocom'; 
    //   echo '<pre>Sign===>>>>';
    //   echo $checkSumString;
    //   echo '</pre>';
        $hash = hash_hmac('sha256',$checkSumString,Config::get('constants.FAVEPAY_ENV_PRO_PRIVATE_API_KEY'));
        
        $last_number = substr($totlamt, -2);
        $first_number = substr($totlamt, -10,-2);
        $wholeamount = (int)($first_number.'.'.$last_number);

        self::updateApitoken($trans_id,$wholeamount,json_encode($checkSumString),$hash);

        return $hash;

    }


    public static function updateApitoken($trans_id,$amount,$json,$hash){

        try
        {

            $FaveApiToken = new FavepayApitoken();
            $FaveApiToken->transaction_id = $trans_id;
            $FaveApiToken->amount = $amount;
            $FaveApiToken->jsoninput = $json;
            $FaveApiToken->checksum = $hash;           
            $FaveApiToken->created_at = date("Y-m-d H:i:s");
            $FaveApiToken->save();

        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
           
           
        } 


    }

    /*
     * @desc : Payment QR Code Submission...  
     */

    public function paymentqrcode(){
        // print_r(Input::all());
        
        
       
        
            if (Config::get('constants.ENVIRONMENT') == 'live') {
                $apiAppId = Config::get('constants.FAVEPAY_ENV_PRO_APP_ID');
                $apiOutletID = Config::get('constants.FAVEPAY_ENV_PRO_OUTLET_ID');
                $apiResponse = Config::get('constants.FAVEPAY_ENV_PRO_REDIRECT_URL');
                $apiCallback = Config::get('constants.FAVEPAY_ENV_PRO_CALLBACK_URL');
                $prefix = Config::get('constants.FAVEPAY_ENV_PRO_PREFIX');

            }else{
                $apiAppId = Config::get('constants.FAVEPAY_ENV_DEV_APP_ID');
                $apiOutletID = Config::get('constants.FAVEPAY_ENV_DEV_OUTLET_ID');
                $apiResponse = Config::get('constants.FAVEPAY_ENV_DEV_REDIRECT_URL');
                $apiCallback = Config::get('constants.FAVEPAY_ENV_DEV_CALLBACK_URL');
                $prefix = Config::get('constants.FAVEPAY_ENV_DEV_PREFIX');  
            }
        
            $trans_id = Input::get('trans_id'); 
            $email = Input::get('Customer_Email');
            $contact = Input::get('Customer_Contact'); 
            $amount = Input::get('Amount'); 
            $location = Input::get('Location');
            
            $grabformat = number_format($amount, 2);
    
            $parts = explode('.', (string) $grabformat);
            
            $whole = (int)$parts[0]; 
            $decimal = $parts[1]; 
            $totnumber = (int)($whole.$decimal);

            $shopper = array(
                            "email" => $email,
                            "phone" => $contact,
                            "location" => $location
                             );
                             
             $omni = $prefix.'-'.$trans_id;
             $amount = $totnumber;
             $app_id = $apiAppId; 
             $outlet_id = $apiOutletID;
             $email = urlencode('shopper_details[email]').'='. urlencode($email); 
             $phone = urlencode('shopper_details[phone]').'='.urlencode($contact); 
             $location = urlencode('shopper_details[location]').'='.preg_replace('/\+/', '+', urlencode($location)); 
             $format = 'web_url'; 
             $redirect_url = urlencode($apiResponse); 
             $callback_url = urlencode($apiCallback); 
            // $request = 'omni_reference='.$omni.'&total_amount_cents='.$totlamt.'&app_id='.$app_id.'&outlet_id='.$outlet_id.'&'.$email.'&'.$phone.'&'.$location.'&redirect_url='.$redirect_url.'&callback_url='.$callback_url.'&format='.$format.'&test=true&client_integration=jocom&sign='.self::callsignature($omni,$email,$phone,$amount,$location);
            $request = json_encode(array(   
                "omni_reference" => $omni,
                "total_amount_cents" => $amount,                
                "app_id" => $apiAppId,
                "outlet_id" => $apiOutletID,
                "shopper_details" => $shopper,  
                "redirect_url" => $apiResponse,
                "callback_url" => $apiCallback, 
                "format" => "web_url",
                "test" => false,
                "client_integration" => "jocom",
                "sign" => self::callsignature($trans_id,$email,$phone,$amount,$location)
            ));

            // echo '<pre>';
            // print_r($request);
            // echo '</pre>';
            
            //  die();

            $url = self::FAVEPAY_API_PATH.Config::get('constants.FAVEPAY_ENV_PRO_COUNTRY').self::FAVEPAY_QR_CODE_API;   
            // print_r($request);

            $APIResponse = self::ApiCaller($url, $request,true);

            // var_dump($APIResponse);

            $arrayResponse = json_decode($APIResponse,true);
            
            $omni_url =   $arrayResponse['code'];
            
            
            
            
          
                
                
            
        //     print_r($response);
            
        //     echo $omni_url;
            
        // die();
            if(isset($omni_url) && $omni_url !=''){
                return Redirect::to($omni_url);
            }
            else 
            {
                    $data            = array();
                    $data['message'] = '002';
                    $data['payment'] = 'Oops. Something went wrong. Please try again later.';
                 return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
            }
            

            // print_r($arrayResponse);

    }


    /*
     * @desc : Redirect URL response 
     */

    public function response(){
   
    //  $data = json_decode(file_get_contents("php://input"), true);
        
        //  $json = file_get_contents('php://input');
        // // Converts it into a PHP object 
        // $data = json_decode($json, true);
        
        $omni_reference = Input::get('omni_reference');
        $receipt_id = Input::get('receipt_id');
        $sign = Input::get('sign');
        $status = Input::get('status');
        $total_amount_cents = Input::get('total_amount_cents');
        // print_r(Input::all());
        // die();
        
        $last_number = substr($total_amount_cents, -2);
        $first_number = substr($total_amount_cents, -10,-2);
        $wholeamount = (int)($first_number.'.'.$last_number);

        if(isset($omni_reference) && $omni_reference != ''){

            $tran_split = explode('-',$omni_reference); 
            $trans_id = array_pop($tran_split);

            $data =  array('omni_reference' => $omni_reference,
                           'receipt_id' => $receipt_id, 
                           'sign' => $sign, 
                           'status' => $status, 
                           'total_amount_cents' => $wholeamount, 

                     );
        //      echo 'In';

            $FaveTransaction = new FavepayTransaction();
            $FaveTransaction->transaction_id = $trans_id;
            $FaveTransaction->omni_reference = $omni_reference;
            $FaveTransaction->receipt_id = $receipt_id;
            $FaveTransaction->sign = $sign;  
            $FaveTransaction->amount = $wholeamount;
            $FaveTransaction->status = $status;
            $FaveTransaction->json_data = json_encode($data);                    
            $FaveTransaction->created_at = date("Y-m-d H:i:s");
            $FaveTransaction->updated_at = date("Y-m-d H:i:s");
            $FaveTransaction->save();

            if(isset($status) && $status == 'successful'){

                $favedata['transid'] = $trans_id;
                $favedata['transactionStatus'] = $status;

                // $tran_split = explode('-',$omni_reference); 
                // $trans_id = array_pop($tran_split);
                
                $sql = DB::table('jocom_favepay_apitoken')
                                    ->where('transaction_id','=',$trans_id)
                                    ->update(array('receipt_id' => $receipt_id));



                $transaction = Transaction::incomplete($trans_id)->first();

                if ($omni_reference != "" && $transaction != "") {


                    $transactionType = MCheckout::favepay_transaction_complete($favedata);



                // Success
                switch ($transactionType) {
                    case 'point':
                        $data['message'] = '006';
                        break;
                    default:
                        $data['message'] = '001';
                        break;
                }

                $transaction = Transaction::find($trans_id);
                $user        = Customer::find($transaction->buyer_id);
                $username    = $user->username;
                // $bcard       = BcardM::where('username', '=', $username)->first();
                // $bcardStatus = PointModule::getStatus('bcard_earn');
                $checkout_source = $transaction->checkout_source;
                
                


                $data['payment'] = 'JCSUCCESS successfully received';
                 if($checkout_source == 2){
                    return Redirect::to('http://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=success');
                 }
                 else{
                    // return Redirect::to('https://jocomapp.page.link?apn=com.jocomit.twenty37&ibi=com.jocomit.jocom&link=http%3A%2F%2Fdeeplink.jocom.my%2F%3Fpayment%3Dpaymentfavesuccess'); 
                     
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')
                    ->with('message', $data['message'])
                    ->with('payment', $data['payment'])
                    ->with('id', $data['transid'])
                    ->with('buyerId', $transaction->buyer_id);
                 }

                }

            }
            else {

                    $temp_cancel     = MCheckout::cancelled_transaction(trim($trans_id));
                    $data            = array();
                    $data['message'] = '002';
                    $data['payment'] = 'JCFAIL failed';

                    $transaction = Transaction::find($trans_id);
                    $checkout_source = $transaction->checkout_source;
                    if($checkout_source == 2 && $status != 'successful'){
                        return Redirect::to('http://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=failed');
                     }
                     else
                     {
                        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
                     }
            }

        }

       


        
        

       




        // echo $omni_reference;
        


        // $data1 = file_get_contents('php://input');
        
     //    $calldata1 = json_decode($data,true);   



        

    }


    /*
     * @desc : Redirect URL response 
     */

    public function callback(){
        
    //   $data = file_get_contents('php://V L.;\  input');
        
    //     $calldata = json_decode($data,true); 
        
        $omni_reference = Input::get('omni_reference');
        $receipt_id = Input::get('receipt_id');
        $sign = Input::get('sign');
        $status = Input::get('status');
        $total_amount_cents = Input::get('total_amount_cents');
        // print_r(Input::all());
        die();

        if(isset($omni_reference) && $omni_reference != ''){

            $tran_split = explode('-',$omni_reference); 
            $trans_id = array_pop($tran_split);

            $data =  array('omni_reference' => $omni_reference,
                           'receipt_id' => $receipt_id, 
                           'sign' => $sign, 
                           'status' => $status, 
                           'total_amount_cents' => $total_amount_cents, 

                     );

            $FaveTransaction = new FavepayTransaction();
            $FaveTransaction->transaction_id = $trans_id;
            $FaveTransaction->omni_reference = $omni_reference;
            $FaveTransaction->receipt_id = $receipt_id;
            $FaveTransaction->sign = $sign;  
            $FaveTransaction->amount = $amount;
            $FaveTransaction->status = $status;
            $FaveTransaction->json_data = json_encode($data);                    
            $FaveTransaction->created_at = date("Y-m-d H:i:s");
            $FaveTransaction->save();

            if(isset($status) && $status == 'successful'){

                $favedata['transid'] = $trans_id;
                $favedata['transactionStatus'] = $status;

                // $tran_split = explode('-',$omni_reference); 
                // $trans_id = array_pop($tran_split);



                $transaction = Transaction::incomplete($trans_id)->first();

                if ($omni_reference != "" && $transaction != "") {


                    $transactionType = MCheckout::Favepay_transaction_complete($favedata);



                // Success
                switch ($transactionType) {
                    case 'point':
                        $data['message'] = '006';
                        break;
                    default:
                        $data['message'] = '001';
                        break;
                }

                $transaction = Transaction::find($trans_id);
                $user        = Customer::find($transaction->buyer_id);
                $username    = $user->username;
                // $bcard       = BcardM::where('username', '=', $username)->first();
                // $bcardStatus = PointModule::getStatus('bcard_earn');
                $checkout_source = $transaction->checkout_source;
                
                


                $data['payment'] = 'JCSUCCESS successfully received';
                 if($checkout_source == 2){
                    return Redirect::to('http://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=success');
                 }
                 else{
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')
                    ->with('message', $data['message'])
                    ->with('payment', $data['payment'])
                    ->with('id', $data['transid'])
                    // ->with('bcardStatus', $bcardStatus)
                    // ->with('bcardNumber', object_get($bcard, 'bcard'))
                    ->with('buyerId', $transaction->buyer_id);
                 }

                }

            }
            else {

                    $temp_cancel     = MCheckout::cancelled_transaction(trim($trans_id));
                    $data            = array();
                    $data['message'] = '002';
                    $data['payment'] = 'JCFAIL failed';

                    $transaction = Transaction::find($trans_id);
                    $checkout_source = $transaction->checkout_source;
                    if($checkout_source == 2 && $status != 'successful'){
                        return Redirect::to('http://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=failed');
                     }
                     else
                     {
                        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
                     }
            }

        }


    }



    private static function ApiCaller($endPoint,$param,$isAuthentication = false){
            // echo $isAuthentication;
        // die();
        if (Config::get('constants.ENVIRONMENT') == 'live') {        
            $envAuthenticate = Config::get('constants.FAVEPAY_ENV_PRO');
        }else{
            $envAuthenticate = Config::get('constants.FAVEPAY_ENV_DEV');
        }


        // $data = json_encode($param);

        //  echo "<pre>"; 
        // print_r($data);
        //  echo "</pre>";
        // //  echo $param['apitoken'];
         // $apiToken = self::ApiAuthentication();

          // echo 'Token :'. $apiToken;    

         $URL = $envAuthenticate.$endPoint;
         $header = array(
                'Content-Type: application/json'
                // 'Authorization: '. 'Bearer '.$apiToken
                );


        // echo "sss<pre>";
        // print_r($param);
        // echo "</pre>sss";
        // echo "<pre>";
        // print_r($header);
        // echo "</pre>";
        // // echo $URL.'<BR>';

        $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => 'https://omni.myfave.com/api/fpo/v1/my/qr_codes',
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 0,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'POST',
                  CURLOPT_POSTFIELDS =>$param,
                  CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                  ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);
                // echo $response;
                
                

        return $response;
       
    }









    
}

?>