<?php

class PacepayController extends BaseController
{

    const PACEPAY_API_PATH = '/v1/';
    const PACEPAY_GET_TRANSACTION = '/v1/checkouts/';

    public function index()
    {
        echo "Page not found.";
        return  0;

    }
    /*
     * @desc : Genarate Encoded Token for Basic Auth
    */
    public static function authentication()
    {

        if (Config::get('constants.ENVIRONMENT') == 'live')
        {
            $merchantappid = Config::get('constants.PACEPAY_ENV_PRO_CLIENT_ID');
            $apisuperkey = Config::get('constants.PACEPAY_ENV_PRO_CLIENT_SECRET');

        }
        else
        {
            $merchantappid = Config::get('constants.PACEPAY_ENV_DEV_CLIENT_ID');
            $apisuperkey = Config::get('constants.PACEPAY_ENV_DEV_CLIENT_SECRET');

        }
        $authtoken = base64_encode($merchantappid . ':' . $apisuperkey);

        return $authtoken;

    }
    /*
     * @desc : Create Transaction API using basic auth
    */
    public function createTransaction()

    {
        try
        {

            if (Config::get('constants.ENVIRONMENT') == 'live')
            {

                $envAuthenticate = Config::get('constants.PACEPAY_ENV_PRO');
                $redirectUrls = array(
                    'success' => Config::get('constants.PACEPAY_ENV_PRO_SUCCESS_URL') ,
                    'failed' => Config::get('constants.PACEPAY_ENV_PRO_FAILED_URL')
                );

            }
            else
            {
                $envAuthenticate = Config::get('constants.PACEPAY_ENV_DEV');
                $redirectUrls = array(
                    'success' => Config::get('constants.PACEPAY_ENV_DEV_SUCCESS_URL') ,
                    'failed' => Config::get('constants.PACEPAY_ENV_DEV_FAILED_URL')
                );

            }
            $totlamt = Input::get('amount');
            $pace_amount = number_format($totlamt, 2, '', '');
            $expire=$date=date('Y-m-d h:m:s',strtotime('+10 day'));

            $source = ['amount' => (int)$pace_amount, 'currency' => Input::get('currency') , 'expiringAt' =>$expire, 'referenceID' => Input::get('referenceID') , 'redirectUrls' => $redirectUrls, 'webhookUrl' => self::Webhookcallback() , ];

            $apitoken = self::authentication();
            $headers = array(
                'Content-Type:application/json',
                'Authorization: Basic ' . $apitoken
            );
            $endpoint = $envAuthenticate . '/v1/checkouts';

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => $endpoint,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($source) ,
                CURLOPT_HTTPHEADER => $headers,
            ));

            $response = curl_exec($curl);
            if (curl_error($curl))
            {
                echo 'Error:' . curl_error($curl);
            }
            curl_close($curl);
            $final = json_decode($response, JSON_UNESCAPED_SLASHES);
            $encoded = ['pace_transaction_id' => $final['transactionID'], 'transaction_id' => $final['referenceID'], 'merchant_id' => $final['merchantID'], 'amount' => json_encode($final['amount']) , 'callback' => self::Webhookcallback() , ];
            self::addPaceTrasaction($final, $encoded);
            return Redirect::to($final['paymentLink']);
        }
        catch(Exception $ex)
        {
            $exp = $ex->getMessage();
            $expline = $ex->getLine();

        }

    }
    public static function addPaceTrasaction($final, $encoded)
    {
        try
        {

            $pacetransaction = array();
            $pacetransaction['pace_transaction_id'] = $final['transactionID'];
            $pacetransaction['token'] = $final['token'];
            $pacetransaction['transaction_id'] = $final['referenceID'];
            $pacetransaction['merchant_id'] = $final['merchantID'];
            $pacetransaction['amount'] = $final['amount']['actualValue'];
            $pacetransaction['currency'] = $final['amount']['currency'];
            $pacetransaction['value'] = $final['amount']['value'];
            $pacetransaction['expiry_date'] = $final['expiryDate'];
            $pacetransaction['creation_date'] = $final['creationDate'];
            $pacetransaction['update_date'] = $final['updateDate'];
            $pacetransaction['status'] = $final['status'];
            $pacetransaction['payment_link'] = $final['paymentLink'];
            $pacetransaction['json_data'] = json_encode($encoded);
            DB::table('jocom_pacepay_transaction')->insert($pacetransaction);
        }
        catch(Exception $ex)
        {

            $exp = $ex->getMessage();
            $expline = $ex->getLine();

        }
    }
    /*
     * @desc : Redirect URL response
    */

    public function response()
    {
        
        $order_id = Input::get('transactionId');
        $trans_id = Input::get('merchantReferenceId');
        $status = Input::get('status');

        if (isset($status) && $status == 'success')
        {
            if ($status == 'success')
            {
                $status_success = 'successful';
            }
            else
            {
                $status_success = 'failed';
            }
            $sql = DB::table('jocom_pacepay_transaction')->where('pace_transaction_id', '=', $order_id)->update(array(
                'status' => $status_success
            ));

            $pacedata['transid'] = $trans_id;
            $pacedata['transactionStatus'] = $status_success;

            $transaction = Transaction::incomplete($trans_id)->first();
             
            if ($order_id != "" && $transaction != "")
            {

                $transactionType = MCheckout::pacepay_transaction_complete($pacedata);

                // Success
                switch ($transactionType)
                {
                    case 'point':
                        $data['message'] = '006';
                    break;
                    default:
                        $data['message'] = '001';
                    break;
                }

                $transaction = Transaction::find($trans_id);
                $user = Customer::find($transaction->buyer_id);
                $username = $user->username;
                $checkout_source = $transaction->checkout_source;

                $data['payment'] = 'JCSUCCESS successfully received';
                if ($checkout_source == 2)
                {
                    // return Redirect::to('http://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=success');
                    
                }
                else
                {

                    return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment'])->with('id', $data['transid'])->with('buyerId', $transaction->buyer_id);
                }

            }
            
            $transaction_confirm= Transaction::find($trans_id);
            
            if($transaction_confirm->status=="completed"){
            switch ($transactionType)
                {
                    case 'point':
                        $data['message'] = '006';
                    break;
                    default:
                        $data['message'] = '001';
                    break;
                }
                $user = Customer::find($transaction_confirm->buyer_id);
                $username = $user->username;
                $checkout_source = $transaction_confirm->checkout_source;

                $data['payment'] = 'JCSUCCESS successfully received';
                if ($checkout_source == 2)
                {
                    // return Redirect::to('http://jocom.my/p_respond.php?tranID='.$transaction->id.'&status=success');
                    
                }
                else
                {

                    return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment'])->with('id', $data['transid'])->with('buyerId', $transaction_confirm->buyer_id);
                }
            }
            

        }
        else
        {

            $temp_cancel = MCheckout::cancelled_transaction(trim($trans_id));
            $data = array();
            $data['message'] = '002';
            $data['payment'] = 'JCFAIL failed';

            $transaction = Transaction::find($trans_id);
            $checkout_source = $transaction->checkout_source;
            if ($checkout_source == 2 && $status != 'successful')
            {
                return Redirect::to('http://jocom.my/p_respond.php?tranID=' . $transaction->id . '&status=failed');
            }
            else
            {
                return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')
                    ->with('message', $data['message'])->with('payment', $data['payment']);
            }
        }

    }
    public function Webhookcallback()
    {
        if (Config::get('constants.ENVIRONMENT') == 'live')
        {
            $merchantappid = Config::get('constants.PACEPAY_ENV_PRO_CLIENT_ID');
            $apisuperkey = Config::get('constants.PACEPAY_ENV_PRO_CLIENT_SECRET');
            $base="https://api.jocom.com.my";
            
        }
        else
        {
            $merchantappid = Config::get('constants.PACEPAY_ENV_DEV_CLIENT_ID');
            $apisuperkey = Config::get('constants.PACEPAY_ENV_DEV_CLIENT_SECRET');
            $base = "https://uat.all.jocom.com.my";
        }
        $user = array(
            'user_name' => $merchantappid,
            'password' => $apisuperkey
        );
        $nonce = md5('jocom_pace_webhooks');
        $url = $base . "/pacepay/webhook_callback";
        $url = preg_replace("/([http|https]:\/\/)/", "$1$user[user_name]:$user[password]@", $url);
        return $url;
    }

    public function webhook_callback()
    {

        $payload = json_decode(file_get_contents('php://input') , true);
        
        if(empty($payload)){
            echo 'Invalid order';
           
        }
        else 
        {
            $pacecallback = array();
            $pacecallback['pace_transaction_id'] = $payload['transactionID'];
            $pacecallback['transaction_id'] = $payload['referenceID'];
            $pacecallback['event'] = $payload['event'];
            $pacecallback['status'] = $payload['status'];
            $pacecallback['amount'] = $payload['amount']['actualValue'];
            $pacecallback['currency'] = $payload['amount']['currency'];
            $pacecallback['refund_id'] = $payload['refund_id'];
            $pacecallback['refund_type'] = $payload['refund_type'];
            $pacecallback['details'] = $payload['details'];
            $pacecallback['json'] = json_encode($payload);
            DB::table('jocom_pace_callback')->insert($pacecallback);
            
        }
        

        try
        {
            if (empty($payload['referenceID']))
            {
                throw new Exception('order id missing');
            }
            $transaction = Transaction::find($payload['referenceID']);
            $pace_token = DB::table('jocom_pacepay_transaction')->select('status', 'amount')
                ->where('pace_transaction_id', '=', $payload['transactionID'])->where('transaction_id', '=', $payload['referenceID'])->first();

            if (empty($transaction))
            {
                throw new Exception('Invalid order');
            }
            if ($transaction->total_amount != $payload['amount']['actualValue'])
            {
                throw new Exception('Order Amount is Invaild');
            }

            if ($payload['event'] == 'approved' && $payload['status'] == 'success')
            {

                if ($transaction->status != 'completed' && $pace_token->status != 'successful')
                {

                    if ($payload['status'] == 'success')
                    {
                        $status_success = 'successful';
                    }
                    else
                    {
                        $status_success = 'failed';
                    }

                    $sql = DB::table('jocom_pacepay_transaction')->where('pace_transaction_id', '=', $payload['transactionID'])->update(array(
                        'status' => $status_success
                    ));

                    $pacedata['transid'] = $payload['referenceID'];
                    $pacedata['transactionStatus'] = $status_success;

                    $transaction = Transaction::incomplete($payload['referenceID'])->first();

                    if ($transaction->id != "" && $transaction != "")
                    {

                        $transactionType = MCheckout::pacepay_transaction_complete($pacedata);

                    }

                }

            }
            if ($payload['event'] == 'cancelled' && $payload['status'] == 'fail')
            {
                if ($payload['status'] == 'fail')
                {
                    $status_success = 'failed';
                }
                $sql = DB::table('jocom_pacepay_transaction')->where('pace_transaction_id', '=', $payload['transactionID'])->update(array(
                    'status' => $status_success
                ));

                $temp_cancel = MCheckout::cancelled_transaction(trim($payload['referenceID']));

            }
            if ($payload['event'] == 'cancelled' && $payload['status'] == 'success')
            {
                if ($payload['status'] == 'success')
                {
                    $status_success = 'Cancelled';
                }
                $sql = DB::table('jocom_pacepay_transaction')->where('pace_transaction_id', '=', $payload['transactionID'])->update(array(
                    'status' => $status_success
                ));

                $temp_cancel = MCheckout::cancelled_transaction(trim($payload['referenceID']));

            }
            if ($payload['event'] == 'expired')
            {
                if ($payload['event'] == 'expired')
                {
                    $status_success = 'expired';
                }
                $sql = DB::table('jocom_pacepay_transaction')->where('pace_transaction_id', '=', $payload['transactionID'])->update(array(
                    'status' => $status_success
                ));

            }

        }
        catch(Exception $ex)
        {

            $exp = $ex->getMessage();
            $expline = $ex->getLine();

        }

    }
    /*
     * @desc : Get Transaction API using basic auth 
     */
    public function getTransaction($transaction_id)
    
     {
     try
        { 

        if (Config::get('constants.ENVIRONMENT') == 'live') {

        $envAuthenticate = Config::get('constants.PACEPAY_ENV_PRO');
        $redirectUrls=array('success'=>Config::get('constants.PACEPAY_ENV_PRO_SUCCESS_URL'),
        'failed'=>Config::get('constants.PACEPAY_ENV_PRO_FAILED_URL'));

        }else{
        $envAuthenticate = Config::get('constants.PACEPAY_ENV_DEV');
        $redirectUrls=array('success'=>Config::get('constants.PACEPAY_ENV_DEV_SUCCESS_URL'),
        'failed'=>Config::get('constants.PACEPAY_ENV_DEV_FAILED_URL'));

        }
        
        
      $pace_id= DB::table('jocom_pacepay_transaction')
                                    ->select('pace_transaction_id')
                                    ->where('transaction_id','=',$transaction_id)
                                    ->first();
       if(empty($pace_id)){
           return "Transaction ID Not Exist";
       }         
      $apitoken=self::authentication();
      $headers = array(
      'Content-Type:application/json',
      'Authorization: Basic '. $apitoken);
      $endpoint = $envAuthenticate . '/v1/checkouts/'.$pace_id->pace_transaction_id;
          

      $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => $endpoint,
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_SSL_VERIFYPEER=>false,
                  CURLOPT_ENCODING => '',
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT =>30,
                  CURLOPT_FOLLOWLOCATION => true,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => 'GET',
                  CURLOPT_HTTPHEADER =>$headers,
                ));

                $response = curl_exec($curl);
                if (curl_error($curl)) {
                echo 'Error:' . curl_error($curl);
                }     
                curl_close($curl);
               $final = json_decode($response,JSON_UNESCAPED_SLASHES);
              
             return $final;
            }
             catch (Exception $ex) {
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
           
        } 

  } 

}

?>
