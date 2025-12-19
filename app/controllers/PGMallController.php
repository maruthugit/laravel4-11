<?php

class PGMallController extends BaseController
{
    const DATE_FORMAT_ymdhhmm = "1";
    
    public function anyIndex(){

        return View::make('pgmall.index');
    }

    public function anyOrderslisting() {
        
        // Get Orders
        $orders = DB::table('jocom_pgmall_order')->select(
                    array(
                        'jocom_pgmall_order.id',
                        'jocom_pgmall_order.ordersn',
                        'jocom_pgmall_order.name',
                        'jocom_pgmall_order.phone',
                        'jocom_pgmall_order.transaction_id',
                        'jocom_pgmall_order.status'
                        ))
                    ->leftJoin('jocom_pgmall_order_details', 'jocom_pgmall_order_details.order_id', '=', 'jocom_pgmall_order.id')
                    ->where('jocom_pgmall_order_details.activation',1)
                    ->groupby('jocom_pgmall_order.ordersn');
 
        return Datatables::of($orders)->make(true);
        
    }

    // Nadzri (22/03/2022) - Migrate function
    public function anyMigrate(){

        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
        $OrderCollection = array();
        $message = "";
        

        try{
            $offset = 0;
            $listType = Input::get('list_type');
            $date_start = Input::get('date_start');
            $date_end = Input::get('date_end');
            $auth = Config::get('constants.PGMallJocomAuth');

            $vars = array(
                'date_start'        => $date_start, 
                'date_end'          => $date_end, 
                'order_status_id'   => 20,
            );
            $curl = curl_init(); // HTTP requests. Initialize a new session and return a cURL handle

            // curl_setopt - set a cURL transfer options.
            curl_setopt($curl, CURLOPT_URL,"https://api.pgmall.my/index.php?route=api/seller_api/getOrderProduct"); // fetch url
            curl_setopt($curl, CURLOPT_POST, 1); // sends a normal POST request
            curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($vars));  //Post Fields 
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); // Returns the information obtained in the form of file stream, instead of being output.	

            $headers = [
                'Cache-Control: no-cache',
                'Content-Type: application/x-www-form-urlencoded; charset=utf-8',
                'authkey: '. $auth
            ];

            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); // To set a array of HTTP header fields
            //execute post
            $result = curl_exec($curl);
            // print_r($vars);
            // dd($result);

            $results = json_decode($result, TRUE);
            
            // echo 'Auth token: '.$auth;
            // echo '<pre>Resiult:';
            // print_r($results);
            // echo '</pre>';
            // die();
     
            $array = array();

            if (!empty($results)) {
                foreach ($results['data'] as $key => $value) {
                    $ordernum = $value['order_no'];

                    array_push($array, $ordernum);
                }   
            }

            $chunked_arr = array_chunk($array,50);
            $total = array();

            foreach ($chunked_arr as $key => $value) {
                $list = json_encode($value);

                $OrderCollection = $this->getOrders($listType,$list,$results);

                array_push($total, count($OrderCollection));
                
                // echo '<pre>';
                // print_r($OrderCollection);
                // echo '</pre>';
                // die();

                if(count($OrderCollection) > 0 ){
                    // Save new records
                    $isSaved = $this->saveNewOrder($OrderCollection);
                }
            }
  
        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getMessage();
        }

        finally {
            return array(
                "response" => $isError,
                "totalRecords" => array_sum($total),
                //"LatestRecord" => $OrderCollection,
                "message"=>$message,
                "charges"=>$isSaved
            );
        }

    }

    // Nadzri (22/03/2022)
    public function getOrders($listType,$ordernum,$results){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $orderCollection = [];
        $date = new DateTime();
        $currentdate = $date->getTimestamp();
        $isTrue = 0; 

        if($isTrue == 0){  
            foreach ($results['data'] as $value) 
            {

                $OrderRecord  = DB::table('jocom_pgmall_order')
                                    ->where('ordersn', $value['order_no'])
                                    ->where('activation', 1)
                                    ->get();
                
                if (empty($OrderRecord)) {

                    if (!isset($orderCollection[$value['order_no']])){
    
                        $orderCollection[$value['order_no']] = array(
                                'ordersn'                   => $value['order_no'],
                                "name"                      => $value['customer_info']['shipping_firstname'],
                                "phone"                     => $value['customer_info']['shipping_telephone'],
                                "email"                     => $value['customer_info']['shipping_email'],
                                "full_address"              => $value['customer_info']['shipping_address_1'].' '.$value['customer_info']['shipping_address_2'],
                                "shipping_postcode"         => $value['customer_info']['shipping_postcode'],
                                // "message_to_seller"         => $value['recipient_address']['message_to_seller'],
                                "shipping_amount"           => $value['shipping_amount'],
                                "from_account"              => $listType,
                                "created_by"                => Session::get('username'),
                                "created_at"                => date('Y-m-d H:i:s'),
                                "json"                      => $value,
                                'product'                   => array()
                            );
                    }
    
                    foreach ($value['order_product'] as $va) 
                    {
    
                        $orderCollection[$value['order_no']]['product'][] = array(
                            'item_name'                     => $va['product_name'], 
                            'item_sku'                      => $va['attribute_sku'], 
                            // 'variation_sku'                 => $va['variation_sku'], 
                            // 'variation_name'                => $va['variation_name'], 
                            'order_quantity'                => $va['order_quantity'], 
                            // 'variation_discounted_price'    => $va['variation_discounted_price'], 
                            'variation_original_price'      => $va['total_price'], 
                            // "message_to_seller"             => $value['recipient_address']['message_to_seller'],
                            "name"                          => $value['customer_info']['shipping_firstname'],
                            "phone"                         => $value['customer_info']['shipping_telephone'],
                            "email"                         => $value['customer_info']['shipping_email'],
                            "shipping_postcode"             => $value['customer_info']['shipping_postcode'],
                            "full_address"                  => $value['customer_info']['shipping_address_1'].' '.$value['customer_info']['shipping_address_2'],
                            "shipping_amount"               => $value['shipping_amount'],
                            "from_account"                  => $listType,
                            // 'ordersn'=> $value['ordersn'],
                        );
                    }
                }
            
            }
        }
        return $orderCollection;

    }

    // Nadzri (22/03/2022) - Revert Order
    public function anyRevert(){
        
        $isError = 0;
        
        try{
            $order_id = Input::get('order_id');
            $PGMallOrder = PGMallOrder::find($order_id);
            $PGMallOrder->status =  1;
            $PGMallOrder->is_completed =  1;
            $PGMallOrder->save();
            
        }catch (Exception $ex) {
            $isError = 1;
        }
        finally {
            return array(
                "response" =>  $isError,
                "data" => array(
                    "order_id" => $order_id
                )
            );
        }
        
    }

    // Nadzri (22/03/2022) - Save new order
    private function saveNewOrder($OrderCollection){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $counter = 0;
        $isSaved = true;
        $manualProcess = false;
        $manualProcessOrder = array();
        $transferedProcessOrder = array();
        $pgmallDeliveryCharges = 0;
        set_time_limit(0);

        try{
            
            $tax_rate = Fees::get_tax_percent();
            
            // Save order to DB
            foreach ($OrderCollection as $key => $value) 
            {
                $ordersn                = $value['ordersn'];
                $name                   = $value['name'];
                $phone                  = $value['phone'];
                $email                  = $value['email'];
                $receiver               = $value['name'];
                $receiverTel            = $value['phone'];
                $full_address           = $value['full_address'];
                $shipping_postcode      = trim($value['shipping_postcode']);
                $shipping_amount        = $value['shipping_amount'];
                $from_account           = $value['from_account'];
                // Check if the order already in database 
                $orders = PGMallOrder::where('ordersn','=', $ordersn)->first();
                
               
                   
                if (empty($orders)) 
                {
                    $PGMall = new PGMallOrder;
                    $PGMall->ordersn = $ordersn;
                    $PGMall->name = $name;
                    $PGMall->phone = $phone;
                    $PGMall->transaction_id = 0;
                    $PGMall->migrate_from = "PGMall";
                    $PGMall->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                    $PGMall->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                    $PGMall->is_completed = 1;
                    $PGMall->from_account = $from_account;

                    $PGMall->save();

                    $OrderID = $PGMall->id;

                     // Save Product Details
                    foreach ($value['product'] as $key => $val) {
                    
                        $PGMallOrderDetails = new PGMallOrderDetails;
                        $PGMallOrderDetails->order_id = $OrderID;
                        $PGMallOrderDetails->ordersn = $ordersn;
                        $PGMallOrderDetails->item_name = $val['item_name'];
                        $PGMallOrderDetails->item_sku = $val['item_sku'];
                        $PGMallOrderDetails->api_result_return = json_encode($val);
                        $PGMallOrderDetails->api_result_full = json_encode($value['json']);
                        $PGMallOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $PGMallOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $PGMallOrderDetails->save();
                    }
                }

                $username_PGMall = 'pgmall'; 
                $password_PGMall = '';
                $city = DB::table('postcode')->where('postcode', $shipping_postcode)->first();
                $city_id = DB::table('jocom_cities')->where('name', $city->post_office)->first();
                $country_id = 458;
                

                $OrderDataDetails = PGMallOrderDetails::getByOrderID($OrderID);

                $qrcode = array();
                $priceopt = array();
                $qty = array();
                $pgmall_original_price = array();

                $zeroDelivery = 0;
            
                foreach ($OrderDataDetails as $keyDetailsCheck => $valueDetailsCheck) {
                    $APIDataCheck = json_decode($valueDetailsCheck->api_result_return, true);
                    if($APIDataCheck['shipping_amount'] == 0){
                        $zeroDelivery = 1;
                    }
                }

                $pgmallDeliveryCharges = 0;  
                    
                $delivery = array();
                $is_skip = 0;
                $extra_message = "";
                    
                foreach ($OrderDataDetails as $keyDetails => $valueDetails) 
                {
                    $APIData = json_decode($valueDetails->api_result_return, true); //Get api_result_return
                    
                    
                    if(count($APIData['item_sku']) > 0 ) //If total item_sku more than 0
                    { 
                        // if(count($APIData['message_to_seller']) > 0 ){
                        //     $extra_message = $APIData['message_to_seller'];
                        // }

                        //find by Qrcode
                        $ProductInformation = Product::findProductInfoByQRCODE(trim($APIData['item_sku'])); //first - Automation add transaction after migrate
                        // echo '<pre>Enter';
                        // print_r($ProductInformation);
                        // echo '</pre>';

                            if(count($ProductInformation) >  0)
                            {
                                $qrcode[] = $ProductInformation->qrcode;
                                
                                // $optionId = $APIData['variation_sku'];

                                $priceopt[] = $ProductInformation->ProductPriceID;
                                $qty[] = $APIData['order_quantity'];  
                                $pgmall_original_price[] = $APIData['variation_original_price']; 
                                
                            }else{

                                $is_skip = 1;
                                $manualProcess = true;
                                array_push($manualProcessOrder, array(
                                    "order_number"=>$ordersn,
                                    "buyername"=>$name 
                                ));


                            }
                            
                        //     print_r($ProductInformation);
                        // die();
                    }else{                                     
                        $is_skip = 1;
                        $manualProcess = true;
                        array_push($manualProcessOrder, array(
                            "order_number"=>$ordersn,
                            "buyername"=>$name 
                        ));
                    }                                          
                }

                $pgmallDeliveryCharges = $pgmallDeliveryCharges + ($shipping_amount * (100/(100 + $tax_rate)));
                $delivery[] = $pgmallDeliveryCharges;
             
                if($is_skip == 0)
                {

                    $get = array(
                        'user'                  => $username_PGMall,             // Buyer Username
                        'pass'                  => $password_PGMall,             // Buyer Password
                        'delivery_name'         => $name,      // delivery name
                        'delivery_contact_no'   => $phone, // delivery contact no
                        // 'special_msg'           => 'Transaction transfer from PGMall ( Order Number : '.$ordersn.' )'. " ".$extra_message,       // special message
                        'special_msg'           => 'Transaction transfer from PGMall ( Order Number : '.$ordersn.' )',       // special message
                        'delivery_addr_1'       => $full_address,
                        'delivery_addr_2'       => '',
                        'delivery_postcode'     => $shipping_postcode,
                        'delivery_city'         => $city_id->id, // City ID 
                        'delivery_state'        => $city_id->state_id,                          // State ID
                        'delivery_country'      => $country_id,                 // Country ID
                        'pgmallDeliveryCharges' => $pgmallDeliveryCharges,  
                        'qrcode'                => $qrcode,
                        'price_option'          => $priceopt, // Price Option
                        'qty'                   => $qty,
                        'pgmall_original_price' => $pgmall_original_price,
                        'devicetype'            => 'cms',
                        'uuid'                  => NULL, // City ID
                        'lang'                  => 'EN',
                        'ip_address'            => Request::getClientIp(),
                        'location'              => '',
                        'transaction_date'      => date("Y-m-d H:i:s"),
                        'charity_id'            => '',
                    );
                    
                    $data = MCheckout::checkout_transaction($get);   
                    
                    // print_r($data);

                    if($data['status'] == "success")
                    {
                        $transaction_id = $data["transaction_id"];
                            
                        // PUSH TO SUCCESS LIST 
                        array_push($transferedProcessOrder, array(
                            "order_number"=>$ordersn,
                            "buyername"=>$name,
                            "transactionID"=>$transaction_id
                        ));
                            
                        // SAVE AS COMPLETED TRANSACTION //
                        $trans = Transaction::find($transaction_id);
                        $trans->status = 'completed';
                        $trans->modify_by = 'API';
                        $trans->modify_date = date("Y-m-d h:i:sa");
                        $trans->save();
                        // SAVE AS COMPLETED TRANSACTION //

                        // Update Status PGMall Order as transfered
                        if($OrderID != 0)
                        {
                            $PGMallOrder = PGMallOrder::find($OrderID);
                            $PGMallOrder->status = 2;
                            $PGMallOrder->transaction_id = $transaction_id;
                            $PGMallOrder->save();
                        }

                    }else{

                        $manualProcess = true;
                        array_push($manualProcessOrder, array(
                            "order_number"=>$ordersn,
                            "buyername"=>$name
                        ));
                            // THROW ERROR FAILED TO CREATE TRANSACTION
                    }
                }
            }
            switch ($from_account) {
                case 1:
                    $acc_name = "PGMall Jocom";  
                    break;
                default:
                   $acc_name = "PGMall Jocom";  
                    break;
            }
            // MANUAL PROCESS HANDLING
            // if($manualProcess){
                // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
                $subject = "Migration Notification";
                $recipient = array(
                    "email"=>Config::get('constants.PGMallManagerEmail'),
                    //"name"=> "Wira Izkandar"
                );
                $data = array(
                        'execution_datetime'      => date("Y-m-d H:i:s"),
                        'total_records'  => count($OrderCollection),
                        'manual_process'  => count($manualProcessOrder),
                        'manual_order_list'  => $manualProcessOrder,
                        'transfered_orders'  => $transferedProcessOrder,
                        'acc_name'  => $acc_name,
                );

                Mail::send('emails.pgmallmigratereport', $data, function($message) use ($recipient,$subject)
                {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($recipient['email'], $recipient['name'])
                            // ->cc(Config::get('constants.PGMallManagerEmailCC'))
                            ->cc(['ryanloh@jocom.my', 'fooyau@jocom.my'])
                            ->subject($subject);
                });
                       
                $running_number = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'batch_no')->first();
                
                $batchNo = str_pad($running_number->counter + 1,10,"0",STR_PAD_LEFT);
                $NewRunner = Running::find($running_number->id);
                $NewRunner->counter = $running_number->counter + 1;
                $NewRunner->save();
                
                self::transactionDeliverytime24h($batchNo, $transferedProcessOrder);
                
            // }

            
            /* AUTOMATION TRANSACTION */

        }catch (Exception $ex) {
            $isSaved = false;
            // echo $ex;
        }
        
        finally{
            return $delivery;
        }
        
    }

    public static function transactionDeliverytime24h($batchno,$transactioncollections = array()){

        $arr = array();

        foreach ($transactioncollections as $key => $value) 
        {
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
            
        return $arr;         

    }

    public function anyBatchgenerate(){
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
        DB::beginTransaction();
        die('Disabled Temporary');
        try{
            
            $batch = PGMallOrder::getBatch();

            //return $batch;
            if(count($batch) > 0 ){
                
                set_time_limit(0);
                foreach ($batch as $key => $value) {

                    $transaction_id = $value->transaction_id;
                    $Transaction = Transaction::find($transaction_id);
                    if($Transaction->status == 'completed'){
                        $order_id = $value->id;
                        $PGMallOrder = PGMallOrder::find($order_id);
                        $TransactionInfo = Transaction::find($transaction_id);

                        if($TransactionInfo->do_no == ""){
                            // CREATE INV
                            $tempInv = MCheckout::generateInv($transaction_id, true);
                            // CREATE PO
                            $tempPO = MCheckout::generatePO($transaction_id, true);
                            // CREATE DO
                            $tempDO = MCheckout::generateDO($transaction_id, true);
                        }

                        $PGMallOrder->status = "2";
                        $PGMallOrder->is_completed = "2";
                        $PGMallOrder->updated_by = "api_system";
                        $PGMallOrder->save();
                        // AUTOMATED LOG TO LOGISTIC APP
                        $logisticcheck = LogisticTransaction::where("transaction_id",'=', $transaction_id)->get();
                        if(count($logisticcheck)==0){
                           LogisticTransaction::log_transaction($transaction_id); 
                        }
                        

                        $logisticData= LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        
                        
                    }
                     
                }                
                
            }
            
        } catch (Exception $ex) {
            $isError = 1;
            DB::rollBack();
            $errorMessage = $ex->getMessage();
        }
        finally{
            if($isError == 0){
                DB::commit();
            }else{
                DB::rollBack();
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }
        
    }


    public function anyPGMallstatus(){
        
        /*
         * 1. Insert into schedule list when mark sent/ update sent
         * 2. Run crob job to every 0900 , 1300, 1700, 2100
         * 3. Save response into log and update try out
         */
        
        
        /*
         * Get all incomplete order
         */
        
        $isError = 0;
        $successResponse = array();
        $pushStatusList = array();
        $pushStatusSuccessList = array();
        $pushStatusFailedList = array();
        $message = "";
        
        
        try{
            
            // Begin Transaction
            DB::beginTransaction();
            
            // Get Incomplete pgmall orders
            $incompleteOrders = DB::table('jocom_pgmall_push_status AS JPPS ')
                    ->where("JPPS.is_completed","0")
                    ->get();
           
            foreach ($incompleteOrders as $key => $value) {

                $PGMallScheduleID = $value->id;
                $LogisticTransaction = LogisticTransaction::find($value->logistic_id);
                $transactionID = $LogisticTransaction->transaction_id;

                $StatusLog = DB::table('jocom_pgmall_push_status_log AS JPPSL ')
                    ->where("JPPSL.push_order_id",$value->id)
                    ->where("JPPSL.status",1)
                    ->get();

                // PGMall Order Number
                $pgmallOrderNumber = $value->pgmall_order_number;
                
                $statusData = UtilitiesController::getLogisticStatusInfo($LogisticTransaction->status);

                switch ($value->from_account) {
                    case 1:
                        $shopid     = Config::get('constants.PGMallJocomShopid');
                        $partner_id = Config::get('constants.PGMallJocomPartnerID');
                        $secret     = Config::get('constants.PGMallJocomSecret');  
                        break;
                    case 2:
                    default:
                        $shopid     = Config::get('constants.PGMallJocomShopid');
                        $partner_id = Config::get('constants.PGMallJocomPartnerID');
                        $secret     = Config::get('constants.PGMallJocomSecret');  
                        break;
                }

       
                if(!$statusData){
                    $logistic_status = "No Status";
                }else{
                    $logistic_status = $statusData['status_description'];
                }
                
                // Have Record
                if(count($StatusLog) > 0 ){
                    
                    if($LogisticTransaction->status == 0){
                        $dateTime = $LogisticTransaction->insert_date; 
                    }else{
                        $dateTime = $LogisticTransaction->modify_date; 
                    }

                    if($value->current_logistic_status != $LogisticTransaction->status){
                        // Insert to push latest status List 
                        $setData = array(
                            "push_order_id" => $value->id,
                            "transaction_ID" => $transactionID,
                            "order_number" => $pgmallOrderNumber,
                            "logistic_status_code" => $LogisticTransaction->status,
                            "logistic_status" => $logistic_status,
                            "datetime" => $dateTime,
                            "remark" => '',
                            "shopid" => $shopid,
                            "partner_id" => $partner_id,
                            "secret" => $secret,
                        );
                        
                        array_push($pushStatusList, $setData);

                    }
                }else{
                    
                    if($LogisticTransaction->status == 0){
                        $dateTime = $LogisticTransaction->insert_date; 
                    }else{
                        $dateTime = $LogisticTransaction->modify_date; 
                    }
                   
                    // Insert to push latest status List 
                    $setData = array(
                            "push_order_id" => $value->id,
                            "transaction_ID" => $transactionID,
                            "order_number" => $pgmallOrderNumber,
                            "logistic_status_code" => $LogisticTransaction->status,
                            "logistic_status" => $logistic_status,
                            "datetime" => $dateTime,
                            "remark" => '',
                            "shopid" => $shopid,
                            "partner_id" => $partner_id,
                            "secret" => $secret,
                        );
                    
                    array_push($pushStatusList, $setData);
                    
                }

            }
       
            // Start push status to pgmall API only when logistic status is SENT
            foreach ($pushStatusList as $kPush => $vPush) {

                if ($vPush['logistic_status'] === "Sent") 
                {
                    $setDataPush = array(
                    "ordersn" => $vPush['order_number'],
                    "timestamp" => $vPush['datetime'],
                    "shopid" => $vPush['shopid'],
                    "partner_id" => $vPush['partner_id'],
                    );

                    $shopid      = $vPush['shopid'];
                    $partner_id  = $vPush['partner_id'];
                    $date        = new DateTime();
                    $currentdate = $date->getTimestamp();
                    $ordersn     = $vPush['order_number'];
                    $secret      = $vPush['secret'];
             
                    $string = 'https://seller-staging.pgmall.my/api/v1/logistics/offline/set|{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "ordersn":"'.$ordersn.'"}';

                    $sig = hash_hmac('sha256', $string, $secret);
                    $post = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "ordersn":"'.$ordersn.'"}';
                    $header = array('Content-Type: application/json', 'Authorization: '. $sig);

                    $ch = curl_init('https://seller-staging.pgmall.my/api/v1/logistics/offline/set');
                                                                                    
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

                    //execute post
                    $result = curl_exec($ch);

                    $pushDatetime = date("Y-m-d h:i:s");
     
                    if($result === "{}"){
                        
                        array_push($pushStatusSuccessList, array_merge($vPush,array("APIHttpCode" => json_encode($result))));
                  
                        // Insert into LOG
                        $PGMallPushStatusLog = new PGMallPushStatusLog();
                        $PGMallPushStatusLog->push_order_id = $vPush['push_order_id'];
                        $PGMallPushStatusLog->push_data = json_encode($setDataPush);
                        $PGMallPushStatusLog->push_at = $pushDatetime;
                        $PGMallPushStatusLog->response_data = json_encode($result);
                        $PGMallPushStatusLog->response_at = date("Y-m-d h:i:s");
                        $PGMallPushStatusLog->push_status = $vPush['logistic_status'];
                        $PGMallPushStatusLog->status = 1;
                        $PGMallPushStatusLog->save();
                        
                         
                        $PGMallPushStatus = PGMallPushStatus::find($vPush['push_order_id']);
                        $PGMallPushStatus->current_logistic_status = $vPush['logistic_status_code'];
                        $PGMallPushStatus->updated_by = 'SYSTEM';
                        if($vPush['logistic_status_code'] == 5){ // 5 = Sent
                            $PGMallPushStatus->is_completed = 1;
                        }
                        $PGMallPushStatus->save(); 
                        
                    }else{
                        array_push($pushStatusFailedList, array_merge($vPush,array("APIHttpCode" => json_encode($result))));
                        
                        $PGMallPushStatusLog = new PGMallPushStatusLog();
                        $PGMallPushStatusLog->push_order_id = $vPush['push_order_id'];
                        $PGMallPushStatusLog->push_data = json_encode($setDataPush);
                        $PGMallPushStatusLog->push_at = $pushDatetime;
                        $PGMallPushStatusLog->response_data = json_encode($result);
                        $PGMallPushStatusLog->response_at = date("Y-m-d h:i:s");
                        $PGMallPushStatusLog->push_status = $vPush['logistic_status'];
                        $PGMallPushStatusLog->status = 0;
                        $PGMallPushStatusLog->save();
                    }
                }
            }
         
           // Send Email Notification 
            $recipient = array(
                "name" => "",
                "email" => Config::get('constants.PGMallManagerEmail')
            );
            $subject = "PGMALL PUSH STATUS REPORT ".date("Y-m-d H:i:s");
            
            $data = array(
                    'execution_datetime'      => date("Y-m-d H:i:s"),
                    'total_records'  => count($pushStatusFailedList) + count($pushStatusSuccessList),
                    'manual_process'  => count($pushStatusFailedList),
                    'failed_list'  => $pushStatusFailedList,
                    'success_list'  => $pushStatusSuccessList
            );
            
            Mail::send('emails.pgmallpushstatusreport', $data, function($message) use ($recipient,$subject)
            {
                $message->from('payment@jocom.my', 'JOCOM PGMALL PUSH STATUS');
                $message->to($recipient['email'], $recipient['name'])->subject($subject);
            });
                
        
        } catch (Exception $ex) {
            
            $message = $ex->getMessage();
            $isError = 1;
            echo $ex->getLine();
            
        }  finally {
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "response" => $isError,
                "response_message" => $message
            );
                
        }
        
        
    }

}