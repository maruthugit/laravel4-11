<?php
use Helper\ImageHelper as Image;
// use DB;

class FacebookController extends BaseController
{
    protected $appSecret = '46f88efe2b0dca5630e8315911771bbd';
    protected $verifyToken = 'jocom@99';
	protected $token = 'EAARH5MqOAq0BACLeKj4moq4iN4FUfgdmGxaYaRQgMj0emJQF0qoQMEPhL19w8e0FTHVBpMmBHrHfQb6GBfyYLSpIZAz4pVRobNtwXi87XtTUZCvOOLbNYrnsoQLGk2B0XrnsT2WZCIVXank5VQYzdhWRHjhJt0JoTSMQGytlwZDZD';
    
    public function getWebhook()
    {
        $mode = Input::get('hub_mode');
        $verify_token = Input::get('hub_verify_token');
        $challenge = Input::get('hub_challenge');

        if ($mode === 'subscribe' && $verify_token === $this->verifyToken) {
            return Response::make($challenge, 200);
        } else {
            return Response::make('', 403);
        }
    }
    
    public function postWebhook()
    {
        DB::table('test_message')->insert(['message' => 1]);
        return Response::make('', 200);
    }
    
    public function postPayload()
    {
        $psid = Input::get('psid');
        $payload = Input::get('payload');
        
        $exist = DB::table('facebook_message')->where('psid', '=', $psid)->first();
        if ($exist != null) {
            DB::table('facebook_message')->where('psid', '=', $psid)
              ->update(['last_payload' => $payload]);
        } else {
            DB::table('facebook_message')->insert(['psid' => $psid, 'last_payload' => $payload]);
        }
        
        return Response::make('', 200);
    }
    
    public function getPayload($psid)
    {
        return DB::table('facebook_message')->where('psid', '=', $psid)->first()->last_payload;
    }
    
    public function getJocomorderdetails($id) 
    {
        $order = DB::table('jocom_transaction')
                   ->where('id', '=', $id)
                   ->where('status', '=', 'completed')
                   ->first();
                   
        if ($order == null) {
            return Response::make("Order not found.", 200);
        }
        
        $logistic = DB::table('logistic_transaction')
                      ->where('logistic_transaction.transaction_id', '=', $order->id)
                      ->select('logistic_transaction.transaction_date', 'logistic_transaction.status')
                      ->first();


        $message = '';
        
        switch ($logistic->status) {
            case 0:
                $delivery_times = DB::table('jocom_transaction_details')->select('delivery_time')->where('transaction_id', '=', $id)->get();

                $transaction_date = date_create($logistic->transaction_date);
                  
                $dtime = '';
                foreach ($delivery_times as $time) {
                    if ($time == '24 hours') {
                        $dtime = $time;
                        break;
                    } else {
                        if ($dtime == '') {
                            $dtime = $time;
                        } else if ($time < $dtime) {
                            $dtime = $time;
                        }
                    }
                }

                $duration = '';
                if ($dtime->delivery_time == '24 hours') {
                    $duration = '1 day';
                } else if ($dtime->delivery_time == '1-2 business days') {
                    $duration = '2 days';
                } else if ($dtime->delivery_time == "2-3 business days") {
                    $duration = '3 days';
                } else if ($dtime->delivery_time == '3-7 business days') {
                    $duration = '7 days';
                } else if ($dtime->delivery_time == '14 business days') {
                    $duration = '14 days';
                } else if ($dtime->delivery_time == '15-21 business days') {
                    $duration = '21 days';
                }

                $ddate = date_add($transaction_date, date_interval_create_from_date_string($duration));
                $date = date_format($ddate, 'j M Y');
                                   
                $message = "Your order will be delivered by " . $date . ".";
                break;
            case 2:
                $message = "Your order is partially sent.";
                break;
            case 3:
                $message = "Your order is returned. Maybe you just missed it.";
                break;
            case 4: 
                $message = "Your item is being delivered.";
                break;
            case 5:
                $message = "Your order has been delivered.";
                break;
            case 6:
                $message = "Your order has been cancelled.";
                break;
        }

        return Response::make($message, 200);
    }
    
    public function getLazadaorderdetails($order_number) 
    {
        $order = DB::table('jocom_lazada_order')
                   ->where('order_number', '=', $order_number)
                   ->first();
                   
        $express = DB::table('jocom_lazada_order_items')
                    ->join('jocom_lazada_order', 'jocom_lazada_order_items.order_id', '=', 'jocom_lazada_order.id')
                    ->where('jocom_lazada_order_items.shipping_provider_type', '=', 'express')
                    ->where('jocom_lazada_order.order_number', '=', $order_number)
                    ->count();

        $is_express = ($express > 0) ? true : false;
                     
        return $this->response($order, $is_express);
    }
    
    public function getShopeeorderdetails($order_number)
    {
        $order = DB::table('jocom_shopee_order')
                   ->where('ordersn', '=', $order_number)
                   ->first();
                     
        return $this->response($order);
    }
    
    public function getAgsorderdetails($order_number)
    {
        $order = DB::table('jocom_astrogoshop_order')
                   ->where('order_number', '=', $order_number)
                   ->first();
                     
        return $this->response($order);
    }
    
    public function getQoo10orderdetails($order_number)
    {
        $order = DB::table('jocom_qoo10_order')
                   ->where('packNo', '=', $order_number)
                   ->first();
                     
        return $this->response($order);
    }
    
    public function getPrestomallorderdetails($order_number)
    {
        $order = DB::table('jocom_elevenstreet_order')
                   ->where('order_number', '=', $order_number)
                   ->first();

        return $this->response($order);
    }
    
    private function response($order, $is_express = false)
    {
        if ($order == null) {
            return Response::make("Order not found.", 200);
        }
        
        $logistic = DB::table('logistic_transaction')
                      ->where('logistic_transaction.transaction_id', '=', $order->transaction_id)
                      ->select('logistic_transaction.transaction_date', 'logistic_transaction.status')
                      ->first();
        
        $message = '';
        
        switch ($logistic->status) {
            case 0:
                $delivery_times = DB::table('jocom_transaction_details')->select('delivery_time')->where('transaction_id', '=', $order->transaction_id)->get();

                $transaction_date = date_create($logistic->transaction_date);
                                   
                $dtime = '';
                foreach ($delivery_times as $time) {
                    if ($time == '24 hours') {
                        $dtime = $time;
                        break;
                    } else {
                        if ($dtime == '') {
                            $dtime = $time;
                        } else if ($time < $dtime) {
                            $dtime = $time;
                        }
                    }
                }
                
                $duration = '';
                if ($dtime->delivery_time == '24 hours') {
                    $duration = '1 day';
                } else if ($is_express) {
                    $duration = '1 day';
                } else if ($dtime->delivery_time == '1-2 business days') {
                    $duration = '2 days';
                } else if ($dtime->delivery_time == '2-3 business days') {
                    $duration = '3 days';
                } else if ($dtime->delivery_time == '3-7 business days') {
                    $duration = '7 days';
                } else if ($dtime->delivery_time == '14 business days') {
                    $duration = '14 days';
                } else if ($dtime->delivery_time == '15-21 business days') {
                    $duration = '21 days';
                }

                $ddate = date_add($transaction_date, date_interval_create_from_date_string($duration));
                $date = date_format($ddate, 'j M Y');
                                   
                $message = "Your order will be delivered by " . $date . ".";
                break;
            case 2:
                $message = "Your order is partially sent.";
                break;
            case 3:
                $message = "Your order is returned. Maybe you just missed it.";
                break;
            case 4: 
                $message = "Your item is being delivered.";
                break;
            case 5:
                $message = "Your order has been delivered.";
                break;
            case 6:
                $message = "Your order has been cancelled.";
                break;
        }

        return Response::make($message, 200);
    }
    

}
