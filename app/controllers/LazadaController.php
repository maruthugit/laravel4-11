<?php

include_once app_path('library/LazopSdk.php');


class LazadaController extends BaseController{
    
    
    public function index(){
        
        $isError = 0;
        $message = "";

        try{
             // Added by Maruthu    
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

        } catch (Exception $ex) {
            $isError = 1;
        }finally{
            return View::make('lazada.index')->with(
                    [
                    "error" => $isError,
                    "message" => $message
                    ]
                );
        }
        
    }
    
    public function Orders() {
        // ini_set('memory_limit', '-1');
        // ini_set('max_execution_time', 3600);
        // Get Orders
        try{
            // $orders = LazadaOrder::select(array(
            $orders = DB::table('jocom_lazada_order')->select(array(
                'jocom_lazada_order.id',
                'jocom_lazada_order.order_number',
                'jocom_lazada_order.customer_name',
                'jocom_lazada_order.status',
                'jocom_lazada_order.transaction_id'
                        ))
                  //  ->leftJoin('jocom_lazada_order_items', 'jocom_lazada_order_items.order_id', '=', 'jocom_lazada_order.id')
                    ->where('jocom_lazada_order.activation',1)
                   // ->groupBy('jocom_lazada_order.order_number')
                    ->orderBy('jocom_lazada_order.id','desc')
                    ;
                return Datatables::of($orders)->make(true);
                
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        

        //return LazadaOrder::all();
    }
    
    /*
     * @desc    : To migrate orders in 11Street to jocom CMS
     * @return  : Json $listType
     */
    public function migrateOrders(){
        
        $isError = 0;
        $OrderCollection = array();
        $message = "";
        
        try{
            
            
            $accountType = Input::get('account_type');
            $OrderCollection = self::getOrders($accountType);
            
            if(count($OrderCollection) > 0 ){
                // Save new records
                $isSaved = $savedOrders = self::saveNewOrder($OrderCollection,$accountType);
            }
            
        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getMessage();
        }
        
        finally {
            return array(
                "response" => $isError,
                "totalRecords" => count($OrderCollection),
                //"LatestRecord" => $OrderCollection,
                "message"=>$message,
                "charges"=>$isSaved
            );
        }
        
        
        
    }
    
    
    public static function getOrders($accountType){
        
        set_time_limit(0);
        $masterList = array();
        
        
        $latestOrder = DB::table('jocom_lazada_order')->orderBy('id', 'desc')->first();
        if(count($latestOrder) > 0 ){
            $datetime = new DateTime($latestOrder->order_datetime);
            $createAfter =  $datetime->format(DateTime::ATOM);
        }else{
            $datetime = new DateTime('2017-04-01 00:00:00');
            $createAfter =  $datetime->format(DateTime::ATOM);
        }
        
        //ready_to_ship
        //$result = self::callLazadaAction('GetOrders',array("SortBy"=>"created_at","SortDirection"=>"ASC","CreatedAfter"=>$createAfter,"Limit"=>10));
        $result = self::callLazadaAction('GetOrders',array("Status"=>'pending',"SortDirection"=>"ASC"),$accountType);
        $decodeResult = json_decode($result);
        echo "<pre>";
        print_r($decodeResult);
        echo "</pre>";
        die();
        $arrayGetOrders = json_decode(json_encode($decodeResult), True);
        $orders = $arrayGetOrders['SuccessResponse']['Body']['Orders'];
           
        foreach ($orders as $keyOrder => $valueOrder) {
            if(count($order) > 0 ){
                // remove from list
                unset($orders[$keyOrder]);
                
            }
        }
        
        foreach ($orders as $keyOrder => $valueOrder) {
           
            $order_number = $valueOrder['OrderNumber'];
            //echo "Order Number :".$order_number;
            $order = LazadaOrder::where("order_number","=",$order_number)
            ->where("from_account","=",$accountType)
            ->first();
            
            // Order number already exist
            if(count($order) > 0 ){
                // remove from list
                unset($orders[$keyOrder]);
                
            }else{
                
                $resultOrderitems = self::callLazadaAction('GetOrderItems',array("OrderId"=>$valueOrder['OrderId']),$accountType);
                // Order tiems
                $decodeOrderItemsResult = json_decode($resultOrderitems);
                $arrayGetOrderItems = json_decode(json_encode($decodeOrderItemsResult), True);
                $orderItems = $arrayGetOrderItems['SuccessResponse']['Body']['OrderItems'];
//                echo "<pre>";
//                print_r($arrayGetOrderItems);
//                echo "</pre>"; 
                $valueOrder['orderItems'] = $orderItems;
                array_push($masterList, $valueOrder);
            }
            sleep(1);
            
        }
//        echo "---------------------------------";
//        echo "<pre>";
//        print_r($masterList);
//        echo "</pre>";
        return $masterList;
        
        
    }
    
    public static function saveNewOrder($OrderCollection,$accountType){
        
        /*
         * 1. Save Lazada Order
         * 2. Save New Transaction ID
         * 3. Sent Email to person in charge (Successful/Failed Order)
         * 4. 
         */
        
        
        $counter = 0;
        $isSaved = true;
        $manualProcess = false;
        $manualProcessOrder = array();
        $transferedProcessOrder = array();
        $lazadaDeliveryCharges = 0;
        set_time_limit(0);
        
        $tax_rate = Fees::get_tax_percent();

        try{
            foreach ($OrderCollection as $key => $value) {
//                        echo "<pre>";
//                        print_r($value);
//                        echo "</pre>";
                // Assign Value
                $order_number = $value['OrderNumber'];
                $customer_name = $value['CustomerFirstName']." ".$value['CustomerLastName'];
                $customer_email = '';
                $migrate_from = 1;
                $delivery_information = $value['AddressShipping']['DeliveryInfo'];
                $delivery_postcode = $value['AddressShipping']['PostCode'];
                $delivery_addr_1 = $value['AddressShipping']['Address1'];
                $delivery_addr_2 = "" ; 
                $delivery_contact_no = $value['AddressShipping']['Phone'];
                $delivery_name = $value['AddressShipping']['FirstName']." ".$value['AddressShipping']['LastName'];
                
                
                $tempData = $value;
                unset($tempData['orderItems']);
                // saving order information 
                $LazadaOrder = new LazadaOrder();
                $LazadaOrder->order_number = $order_number;
                $LazadaOrder->order_id = $order_number;
                $LazadaOrder->customer_name = $customer_name;
                $LazadaOrder->migrate_from = 1;
                $LazadaOrder->transaction_id = 0;
                $LazadaOrder->order_datetime = $value['CreatedAt'];
                $LazadaOrder->payment_method = $value['PaymentMethod'];
                $LazadaOrder->remarks = $value['Remarks'];
                $LazadaOrder->gift_message = $value['GiftMessage'];
                $LazadaOrder->from_account = $accountType;
                $LazadaOrder->is_completed = 0;
                $LazadaOrder->api_data_return = json_encode($tempData); 
                $LazadaOrder->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->save();
                
                $OrderID = $LazadaOrder->id;
                // Save Product Details
                if(count($value['orderItems']) > 0){
                    
                    foreach ($value['orderItems'] as $keySub => $valSub) {


                        $LazadaOrderDetails = new LazadaOrderDetails();
                        $LazadaOrderDetails->order_id = $OrderID;
                        $LazadaOrderDetails->product_id = $OrderID;
                        $LazadaOrderDetails->product_name = $valSub['Name'];
                        $LazadaOrderDetails->sku = $valSub['Sku'];
                        $LazadaOrderDetails->variation = $valSub['Variation'];
                        $LazadaOrderDetails->shipping_provider_type = $valSub['shipping_provider_type'];
                        $LazadaOrderDetails->order_items_details = json_encode($valSub);
                        $LazadaOrderDetails->status = 1;
                        $LazadaOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $LazadaOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $LazadaOrderDetails->save();

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
//            echo "Postcode: ".$delivery_postcode;
            $getpostcode = Transaction::getXMLpostcode($delivery_postcode);
//            echo "<pre>";
//            print_r($getpostcode);
//            echo "</pre>";
            
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
                //echo "-------------------------------------------------MANUAL-------------------------------------------------------------";     
            }else{
                
                $delivery = array();
                $is_skip = 0;
                $extra_message = "";
                if(count($OrderDataDetails) > 0 ){
                foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                        
                    $APIData = json_decode($valueDetails->order_items_details, true);
                    
                           
                    if(count($APIData['Sku']) > 0 ){
                        
                        $product_jc_code = $APIData['Sku'];
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
                            $optionName = $APIData['Variation'];


                            if(count($optionName) > 0 ){
                                 // TEST CASE 10752
                                //$optionName = '[10753]'.' '.$optionName;

                                if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                                    // TAKE DEFINED LABEL ID //
                                    $selectedPriceOptionID = substr($optionName,strpos($optionName,"[") + 1,strpos($optionName,"]") -1);
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
                            $lazadaDeliveryCharges = $lazadaDeliveryCharges + ($APIData['ShippingAmount'] * (100/(100 + $tax_rate)));
                            $delivery[] = $lazadaDeliveryCharges;

                        }else{
                            //echo "-------------------------------------------------MANUAL-------------------------------------------------------------"; 
                            $is_skip = 1;
                            $manualProcess = true;
                            array_push($manualProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name
                            ));

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
                       
                        $get = array(
                            'user'                => $username_lazada,             // Buyer Username
                            'pass'                => $password_lazada,             // Buyer Password
                            'delivery_name'       => $delivery_name,      // delivery name
                            'delivery_contact_no' => $delivery_contact_no, // delivery contact no
                            'special_msg'         => 'Transaction transfer from Lazada ( Order Number : '.$order_number.' )'. " ".$extra_message,       // special message
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
                            // THROW ERROR FAILED TO CREATE TRANSACTION
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
                        'total_records'  => count($OrderCollection),
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
                            ->cc(['quenny@jocom.my', 'william@jocom.my', 'barrylwm@jocom.my', 'kokhou@jocom.my'])
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
            
        } catch (Exception $ex) {
            $isSaved = false;
            echo $ex->getMessage();
        }
        
        finally{
            return $delivery;
        }
        
        
    }
    
    
    public function batchGenerate(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
        DB::beginTransaction();
        try{
            
            $batch = LazadaOrder::getBatch();
           
            // echo '<pre>';
            // print_r($batch);
            // echo '</pre>';
           
            // die();
            //return $batch;
            if(count($batch) > 0 ){
                // echo 'UY';
                set_time_limit(0);
                ini_set('memory_limit', '-1');
                foreach ($batch as $key => $value) {

                    $transaction_id = $value->transaction_id;
                    // echo $transaction_id;
                    $Transaction = Transaction::find($transaction_id);
                    if($Transaction->status == 'completed'){
                        $order_id = $value->id;
                        $LazadaOrder = LazadaOrder::find($order_id);
                        $TransactionInfo = Transaction::find($transaction_id);
                        // echo 'In';

                        if($TransactionInfo->do_no == ""){
                            
                            echo "GENERATE DO";
                            // CREATE INV
                            $tempInv = MCheckout::generateInv($transaction_id, true);
                            // CREATE PO
                            $tempPO = MCheckout::generatePO($transaction_id, true);
                            // CREATE DO
                            $tempDO = MCheckout::generateDO($transaction_id, true);
                        }

                        $LazadaOrder->status = "2";
                        $LazadaOrder->is_completed = "1";
                        $LazadaOrder->updated_by = "SYSTEM";
                        $LazadaOrder->save();
                        // AUTOMATED LOG TO LOGISTIC APP
                        $logisticcheck = LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        if(count($logisticcheck)==0){
                            LogisticTransaction::log_transaction($transaction_id);
                        }
                        
                         $logisticData= LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        
                        // Insert transaction into schedule
                        $LazadaPushStatus = new LazadaPushStatus();
                        $LazadaPushStatus->lazada_order_number = $value->order_number;
                        $LazadaPushStatus->transaction_id = $value->transaction_id;
                        $LazadaPushStatus->logistic_id = $logisticData->id;
                        $LazadaPushStatus->is_completed = 0;
                        $LazadaPushStatus->current_logistic_status = 0;
                        $LazadaPushStatus->created_by = Session::get('username') != "" ? Session::get('username') : "SYSTEM";
                        $LazadaPushStatus->save();
                        // Insert transaction into schedule
                    }
                     
                }
                
                //  echo "END: ".date("Y-m-d H:i:s");
                
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
   
    
    private static function callLazadaAction($action,$extraParameter = array(),$accountType){
        
        $enviroment = Config::get('constants.ENVIRONMENT');

        if ($enviroment == 'test'){
            // The API key for the user as generated in the Seller Center GUI.
            // Must be an API key associated with the UserID parameter.
            $api_user_id = Config::get('constants.LAZADA_API_USER_ID_DEVELOPMENT');
            $api_key = Config::get('constants.LAZADA_API_KEY_DEVELOPMENT');
        }else{
        
            // Get API Key
        switch ($accountType) {    
            case 1:
                $api_key = Config::get('constants.LAZADA_API_KEY_PRODUCTION');
                $api_user_id = Config::get('constants.LAZADA_API_USER_ID_PRODUCTION');
                break;
            case 2:
                $api_key = Config::get('constants.LAZADA_JOCOM_API_KEY');
                $api_user_id = Config::get('constants.LAZADA_JOCOM_API_USER_ID');

                break;

            default:
                $api_key = Config::get('constants.LAZADA_API_KEY_PRODUCTION');
                $api_user_id = Config::get('constants.LAZADA_API_USER_ID_PRODUCTION');
                break;
            }
        }
        
        // Pay no attention to this statement.
        // It's only needed if timezone in php.ini is not set correctly.
        date_default_timezone_set("UTC");

        // The current time. Needed to create the Timestamp parameter below.
        $now = new DateTime();

        // The parameters for our GET request. These will get signed.
        $parameters = array(
            // The user ID for which we are making the call.
            'UserID' => $api_user_id,

            // The API version. Currently must be 1.0
            'Version' => '1.0',

            // The API method to call.
            'Action' => $action,

            // The format of the result.
            'Format' => 'JSON',

            // The current time formatted as ISO8601
            'Timestamp' => $now->format(DateTime::ISO8601)
        );
        
        //echo $now->format(DateTime::ISO8601);
        $parameters = array_merge($parameters, $extraParameter);
        // Sort parameters by name.
        ksort($parameters);

        // URL encode the parameters.
        $encoded = array();
        foreach ($parameters as $name => $value) {
            $encoded[] = rawurlencode($name) . '=' . rawurlencode($value);
        }

        // Concatenate the sorted and URL encoded parameters into a string.
        $concatenated = implode('&', $encoded);

        // Compute signature and add it to the parameters.
        $parameters['Signature'] = rawurlencode(hash_hmac('sha256', $concatenated, $api_key, false));
       
        $result = self::apiCaller($parameters);
        return $result;
    }
    
    
    
    public static function apiCaller($parameters){
        
        // Replace with the URL of your API host.
        
        
        if ($test == 'test'){
            $url = Config::get('constants.LAZADA_API_ENV_DEVELOPMENT');
        }else{
            $url = Config::get('constants.LAZADA_API_ENV_PRODUCTION');
        }

        // Build Query String
        $queryString = http_build_query($parameters, '', '&', PHP_QUERY_RFC3986);
//echo $queryString;
        // Open cURL connection
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url."?".$queryString);

        // Save response to the variable $data
        curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $data = curl_exec($ch);

        // Close Curl connection
        curl_close($ch);
        
        return $data;
        
        
    }
    
    /*
     * @Desc: To push status of lazada order to Lazada Seller Centre
     */
    
    public function LazadaStatus(){
        
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
            
            // Get Incomplete lazada orders
            $incompleteOrders = DB::table('jocom_lazada_push_status AS JLPS ')
                    ->where("JLPS.is_completed","0")
                    ->get();
                    
            
            
           
            foreach ($incompleteOrders as $key => $value) {

                $lazadaScheduleID = $value->id;
                $LogisticTransaction = LogisticTransaction::find($value->logistic_id);
                $transactionID = $LogisticTransaction->transaction_id;
//
                $StatusLog = DB::table('jocom_lazada_push_status_log AS JLPSL ')
                    ->where("JLPSL.push_order_id",$value->id)
                    ->where("JLPSL.status",1)
                    ->get();
                
            
                // Lazada Tracking Number
                $lazadaTrackingNumber = Config::get('constants.LAZADA_PREFIX_TRACKING_NUMBER').$LogisticTransaction->transaction_id;
                // Lazada Order Number
                $lazadaOrderNumber = $value->lazada_order_number;
//                
                $statusData = UtilitiesController::getLogisticStatusInfo($LogisticTransaction->status);
//                
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
                            "tracking_number" => $lazadaTrackingNumber,
                            "order_number" => $lazadaOrderNumber,
                            "logistic_status_code" => $LogisticTransaction->status,
                            "logistic_status" => $logistic_status,
                            "datetime" => $dateTime,
                            "remark" => '',
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
                            "tracking_number" => $lazadaTrackingNumber,
                            "order_number" => $lazadaOrderNumber,
                            "logistic_status_code" => $LogisticTransaction->status,
                            "logistic_status" => $logistic_status,
                            "datetime" => $dateTime,
                            "remark" => '',
                        );
                    
                    array_push($pushStatusList, $setData);
                    
                }

            }
            
       
            // Start push status to lazada API
            foreach ($pushStatusList as $kPush => $vPush) {

                $setDataPush = array(
                    "tracking_number" => $vPush['tracking_number'],
                    "order_number" => $vPush['order_number'],
                    "logistic_status" => $vPush['logistic_status'],
                    "datetime" => $vPush['datetime'],
                    "remark" => $vPush['remark'],
                );
                $pushDatetime = date("Y-m-d h:i:s");
                // Data in array will convert to json in API caller
                $ApiPushCaller = self::LazadaPushStatusApiCaller($setDataPush);
                
                $successHttpCode = array('200','202');
                
                // If response in acceptable http code
                var_dump($ApiPushCaller);
                if( in_array($ApiPushCaller['httpcode'], $successHttpCode) ){
                    
                    array_push($pushStatusSuccessList, array_merge($vPush,array("APIHttpCode" => json_encode($ApiPushCaller))));
              
                    // Insert into LOG
                    $LazadaPushStatusLog = new LazadaPushStatusLog();
                    $LazadaPushStatusLog->push_order_id = $vPush['push_order_id'];
                    $LazadaPushStatusLog->push_data = json_encode($setDataPush);
                    $LazadaPushStatusLog->push_at = $pushDatetime;
                    $LazadaPushStatusLog->response_data = json_encode($ApiPushCaller);
                    $LazadaPushStatusLog->response_at = date("Y-m-d h:i:s");
                    $LazadaPushStatusLog->push_status = $vPush['logistic_status'];
                    $LazadaPushStatusLog->status = 1;
                    $LazadaPushStatusLog->save();
                    
                     
                    $LazadaPushStatus = LazadaPushStatus::find($vPush['push_order_id']);
                    $LazadaPushStatus->current_logistic_status = $vPush['logistic_status_code'];
                    $LazadaPushStatus->updated_by = 'SYSTEM';
                    if($vPush['logistic_status_code'] == 5){ // 5 = Sent
                        $LazadaPushStatus->is_completed = 1;
                    }
                    $LazadaPushStatus->save(); 
                    
                }else{
                    array_push($pushStatusFailedList, array_merge($vPush,array("APIHttpCode" => json_encode($ApiPushCaller))));
                    
                    $LazadaPushStatusLog = new LazadaPushStatusLog();
                    $LazadaPushStatusLog->push_order_id = $vPush['push_order_id'];
                    $LazadaPushStatusLog->push_data = json_encode($setDataPush);
                    $LazadaPushStatusLog->push_at = $pushDatetime;
                    $LazadaPushStatusLog->response_data = json_encode($ApiPushCaller);
                    $LazadaPushStatusLog->response_at = date("Y-m-d h:i:s");
                    $LazadaPushStatusLog->push_status = $vPush['logistic_status'];
                    $LazadaPushStatusLog->status = 0;
                    $LazadaPushStatusLog->save();
                }
                
            }
//            
//            // Send Email Notification 
            $recipient = array(
                "name" => "Maruthu",
                "email" => "maruthu@jocom.my"
            );
            $subject = "LAZADA PUSH STATUS REPORT ".date("Y-m-d H:i:s");
            
            $data = array(
                    'execution_datetime'      => date("Y-m-d H:i:s"),
                    'total_records'  => count($pushStatusFailedList) + count($pushStatusSuccessList),
                    'manual_process'  => count($pushStatusFailedList),
                    'failed_list'  => $pushStatusFailedList,
                    'success_list'  => $pushStatusSuccessList
            );
//            
            Mail::send('emails.lazadapushstatusreport', $data, function($message) use ($recipient,$subject)
                {
                    $message->from('payment@jocom.my', 'JOCOM LAZADA PUSH STATUS');
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
    
    public static function LazadaPushStatusApiCaller($param){
        
        
        if (Config::get('constants.ENVIRONMENT') == 'live') {
            $lazada_push_env = Config::get('constants.LAZADA_API_ENV_PUSH_STATUS');
        }else{
            $lazada_push_env = Config::get('constants.LAZADA_API_ENV_PUSH_STATUS');
        }
       
        $data_string = json_encode($param);
        // ENABLE IT WHEN IT IS READY
        $ch = curl_init($lazada_push_env);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
     
        $resultPush = curl_exec($ch);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $info = curl_getinfo($ch);

        $response = array(
            "httpcode" => (string)$httpcode,
            "responseData" => $resultPush,
        );
        
        //$response = array(
        //    "httpcode" => 200,
        //    "responseData" => array("data"=>'Yes Zah'),
        //);
        
        return $response;
       
    }
    
    
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
    
    public function migrateOrdersV2(){
      
        $isError = 0;
        $OrderCollection = array();
        $message = "";
        $isNeedAuthentication = 0;
        $authenticationURL = '';
    
       
        try{
            // print_r(Input::all());
            // die();
            $accountType = Input::get('account_type');
            $OrderResponse = self::getOrdersV2($accountType);
            // echo "<pre>";
            //     print_r($OrderResponse);
            //     echo "</pre>";
            //     die(); 
           
            if(count($OrderResponse["data"]["orders"]) > 0 ){
                // Save new records // 
                $OrderCollection = $OrderResponse["data"]["orders"];
                
                // echo "<pre>";
                // print_r($OrderCollection);
                // echo "</pre>";
                // die(); 
                
                $isSaved = $savedOrders = self::saveNewOrderV2($OrderCollection,$accountType);
            }else{
                if($OrderResponse["tokenInfo"]["responseCode"] == 0){
                    $isNeedAuthentication = $OrderResponse["data"]["tokenInfo"]["data"]["need_authentication"];
                    $authenticationURL = $OrderResponse["data"]["tokenInfo"]["data"]["authentication_url"];
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
            $latestOrder = DB::table('jocom_lazada_order')->orderBy('id', 'desc')->first();
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
                $url = 'https://api.jocom.com.my/lazada/getauthtoken';
            }else{
                $url = 'https://uat.all.jocom.com.my/lazada/getauthtoken';
            }
            
            // echo 'In_1';
            // print_r($fields_string);
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
           
            // print_r($data);
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
                        //$order = LazadaOrder::where("order_number","=",$order_number)->where("from_account","=",$accountType)->count();
                        $order = LazadaOrder::where("order_number","=",$order_number)->where("from_account","=",$accountType)->get();
                        
                        // $order = LazadaOrder::where(function($query) {
                        //             $query->where('order_number', '=', $order_number)
                        //                   ->where('from_account', '=', $accountType);
                        //         })->get();
                        
                        // $order = LazadaOrder::where("order_number","=",$order_number)->first();
                        // echo "<pre>";
                        //print_r($order);
                        // echo "</pre>";
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
//        echo "---------------------------------";
//        echo "<pre>";
//        print_r($masterList);
//        echo "</pre>";
     
        
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
            
            $tax_rate = Fees::get_tax_percent();
            
            foreach ($OrderCollection as $key => $value) {
//                        echo "<pre>";
//                        print_r($value);
//                        echo "</pre>";
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
                // saving order information 
                $LazadaOrder = new LazadaOrder();
                $LazadaOrder->order_number = $order_number;
                $LazadaOrder->order_id = $order_number;
                $LazadaOrder->customer_name = $customer_name;
                $LazadaOrder->migrate_from = $accountType;
                $LazadaOrder->transaction_id = 0;
                $LazadaOrder->order_datetime = $value['created_at'];
                $LazadaOrder->payment_method = $value['payment_method'];
                $LazadaOrder->remarks = $value['remarks'];
                $LazadaOrder->gift_message = $value['gift_message'];
                $LazadaOrder->from_account = $accountType;
                $LazadaOrder->is_completed = 0;
                $LazadaOrder->api_data_return = json_encode($tempData); 
                $LazadaOrder->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $LazadaOrder->save();
                
                $OrderID = $LazadaOrder->id;
                // Save Product Details
                if(count($value['orderItems']) > 0){
                    
                    foreach ($value['orderItems'] as $keySub => $valSub) {

                        $LazadaOrderDetails = new LazadaOrderDetails();
                        $LazadaOrderDetails->order_id = $OrderID;
                        $LazadaOrderDetails->product_id = $OrderID;
                        $LazadaOrderDetails->product_name = $valSub['name'];
                        $LazadaOrderDetails->sku = $valSub['sku'];
                        $LazadaOrderDetails->variation = $valSub['variation'];
                        $LazadaOrderDetails->shipping_provider_type = $valSub['shipping_provider_type'];
                        $LazadaOrderDetails->order_items_details = json_encode($valSub);
                        $LazadaOrderDetails->status = 1;
                        $LazadaOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $LazadaOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $LazadaOrderDetails->save();

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
//            echo "Postcode: ".$delivery_postcode;
            $getpostcode = Transaction::getXMLpostcode($delivery_postcode);
//            echo "<pre>";
//            print_r($getpostcode);
//            echo "</pre>";
            
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
                        'total_records'  => count($OrderCollection),
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
                            ->cc(['quenny@jocom.my', 'william@jocom.my', 'barrylwm@jocom.my', 'kokhou@jocom.my'])
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
            
        } catch (Exception $ex) {
            $isSaved = false;
            echo $ex->getMessage();
        }
        
        finally{
            return $delivery;
        }
        
        
    }
    
    
    /* NEW LAZADA API V2 */
    
    public function unmasktest(){
        
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        
        $c = new LazopClient('https://api.lazada.com.my/rest','105651','sRLa3HuaByWZkwFPZUBDZwzZsWJssB6T');
        $request = new LazopRequest('/datamoat/login');
        $request->addApiParam('time',$timestamp);
        $request->addApiParam('appName','STARBUCKS');
        $request->addApiParam('userId','starbucks@jocom.my');
        $request->addApiParam('tid','starbucks@jocom.my');
        $request->addApiParam('userIp','175.139.129.27');
        $request->addApiParam('ati','2626120510051');
        $request->addApiParam('loginResult','fail');
        $request->addApiParam('loginMessage','password is not corret');
        var_dump($c->execute($request));
    }
    
    public function computerisk(){
    
    
        $date = new DateTime();
        $timestamp = $date->getTimestamp();
        $c = new LazopClient('https://api.lazada.com.my/rest','105651','sRLa3HuaByWZkwFPZUBDZwzZsWJssB6T');
        $request = new LazopRequest('/datamoat/compute_risk');
        $request->addApiParam('time',$timestamp);
        $request->addApiParam('appName','STARBUCKS');
        $request->addApiParam('userId','starbucks@jocom.my');
        $request->addApiParam('userIp','60.49.105.160');
        $request->addApiParam('ati','2626120510051');
        var_dump($c->execute($request));
    
    }
    
    public function newgetrisk()
    {
        $accessToken="50000501514aIuuenxhyogbkBS13fe78c69OrXHhgNoxiUTEl5DvsdwxxFc8p";    
         $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
            $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
            $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');        
            $c = new LazopClient($urlClient,$app_key,$app_secret_id);
            $request = new LazopRequest('/orders/get','GET');
            $request->addApiParam('sort_direction','DESC');
            $request->addApiParam('offset','0');
            $request->addApiParam('limit','10');
            $request->addApiParam('sort_by','updated_at');
            $request->addApiParam('created_after','2017-02-10T09:00:00+08:00');
            $request->addApiParam('status','pending');
            var_dump($c->execute($request, $accessToken));
    }
    
    public function sofdelivered($logistic_id){
        
        $data = array();
        $order_item_id = array(); 
        
        $isError = false;
        // echo 'In';

        try{
            
        DB::beginTransaction();

        if($logistic_id > 0){
            
            $logisticdata = DB::table('logistic_transaction')->selectRaw('status,transaction_id')
                                ->where('buyer_email','=','lazada@tmgrocer.com')
                                ->where('status','=',5)
                                ->where('id','=',$logistic_id)
                                ->first();
            
                                
            if(count($logisticdata)>0){
                
                
                $orderdata = DB::table('jocom_lazada_order')->selectRaw('order_number,from_account')->where('transaction_id','=',$logisticdata->transaction_id)->first();
                
                if(count($orderdata)>0){
                    
                    $ordernumber = $orderdata->order_number;
                    $accountType = $orderdata->from_account;
                    // LAZADA DBS Start 
                        $lazadaapires = DB::table('jocom_lazada_order_items AS LOI')
                            ->select('LOI.order_items_details')
                            ->leftjoin('jocom_lazada_order as LO','LO.id','=','LOI.order_id')
                            ->where('LO.order_number','=',$ordernumber)
                            ->get();
        
        
                        foreach($lazadaapires as $value){
                            //$data[] = array('order_items_details' => $row['order_items_details']);
                            $data= json_decode($value->order_items_details);
                           
                            array_push($order_item_id,$data->order_item_id);
                        }
                        
                        //  print_r($order_item_id);
                        //  echo json_encode($order_item_id);
                        //   die("ok");
                        // $accountType = 2;
                        
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
                                // case 9:
                                //     $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                                //     $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                                //     $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                //     break;
                                    
                                default:
                
                                    $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
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
                           
                            // print_r($data);
                            // print_r($fields_string);
                            // die();
                            $responseAccessTokenArray = json_decode($data,true);
                        
                        //     print_r($responseAccessTokenArray);
                        //   die();
                       
                            // Get LAZADA Authentication Token
                            // $dataReturn["orders"] = $masterList;
                         
                        
                            if( isset($responseAccessTokenArray["responseCode"]) && $responseAccessTokenArray["responseCode"] == '1'){
                        
                                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                                $request = new LazopRequest('/order/sof/delivered');
                                $request->addApiParam('order_item_ids',json_encode($order_item_id));
                                $c->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
                                
                                // var_dump($c->execute($request, $responseAccessTokenArray["data"]["valid_token"]));
                            }
                  
                    
                    // LAZADA DBS End 
                    
                }
                
                
            }
            
            
            
        }
        
        }catch(exception $ex){
            $messsage = $ex->getMessage();
            $isError = true;

        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "isError" => $messsage
            );
        }
        // $ordernumber = '317663517587665';
        
        
    }
    
    
    public function dbsdelivered($logistic_id){
        
        $data = array();
        $order_item_id = array(); 
        
        $isError = false;

        try{
            
        DB::beginTransaction();

        if($logistic_id > 0){
            
            $logisticdata = DB::table('logistic_transaction')->selectRaw('status,transaction_id')
                                ->where('buyer_email','=','lazada@jocom.my')
                                ->where('status','=',5)
                                ->where('id','=',$logistic_id)
                                ->first();
            
                                
            if(count($logisticdata)>0){
                
                
                $orderdata = DB::table('jocom_lazada_order')->selectRaw('order_number,from_account')->where('transaction_id','=',$logisticdata->transaction_id)->first();
                
                if(count($orderdata)>0){
                    
                    $ordernumber = $orderdata->order_number;
                    $accountType = $orderdata->from_account;
                    // LAZADA DBS Start 
                        $lazadaapires = DB::table('jocom_lazada_order_items AS LOI')
                            ->select('LOI.order_items_details')
                            ->leftjoin('jocom_lazada_order as LO','LO.id','=','LOI.order_id')
                            ->where('LO.order_number','=',$ordernumber)
                            ->get();
        
        
                        foreach($lazadaapires as $value){
                            //$data[] = array('order_items_details' => $row['order_items_details']);
                            $data= json_decode($value->order_items_details);
                           
                            array_push($order_item_id,$data->order_item_id);
                        }
                        
                        //  print_r($order_item_id);
                        //  echo json_encode($order_item_id);
                        //   die("ok");
                        // $accountType = 2;
                        
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
                                // case 9:
                                //     $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                                //     $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                                //     $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                //     break;
                                    
                                default:
                
                                    $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                                    break;
                            }
                        
                        
                         // Get LAZADA Authentication Token
                            if(strtolower(Config::get('constants.ENVIRONMENT')) == "live"){
                                $url = 'https://api.jocom.com.my/lazada/getauthtoken';
                            }else{
                                $url = 'https://uat.all.jocom.com.my/lazada/getauthtoken';
                            }
                            
                            // echo 'In_1';
                            // print_r($fields_string);
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
                           
                            // print_r($data);
                            // print_r($fields_string);
                            // die();
                            $responseAccessTokenArray = json_decode($data,true);
                       
                       
                            // Get LAZADA Authentication Token
                            // $dataReturn["orders"] = $masterList;
                         
                        
                            if( isset($responseAccessTokenArray["responseCode"]) && $responseAccessTokenArray["responseCode"] == '1'){
                                
                                 $LazadaAPIClient = new LazopClient($urlClient,$app_key,$app_secret_id);
                                $request = new LazopRequest('/order/items/get','GET');
                                $request->addApiParam('order_id',$ordernumber);
                                $requestResponseDetails = $LazadaAPIClient->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
                
                                $requestResponseDetailsArray = json_decode($requestResponseDetails,true);
                                
                                // echo '<pre>';
                                // print_r($requestResponseDetailsArray);
                                // echo '</pre>';
                                
                                // echo $ordernumber.'---'.$requestResponseDetailsArray["data"][0]["package_id"].'Ins<br>';
                                
                                
                                
                                $package_id = 0;
                                $package_id = $requestResponseDetailsArray["data"][0]["package_id"];
                                
                                $package = array('packages' => [array('package_id' => $package_id)]);
                                
                                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                                $request = new LazopRequest('/order/package/sof/delivered');
                                $request->addApiParam('dbsDeliveryReq',json_encode($package));
                                $c->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
                                // var_dump($c->execute($request, $responseAccessTokenArray["data"]["valid_token"]));
                                /*
                                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                                $request = new LazopRequest('/order/sof/delivered');
                                $request->addApiParam('order_item_ids',json_encode($order_item_id));
                                $c->execute($request, $responseAccessTokenArray["data"]["valid_token"]); 
                                */
                                
                                // var_dump($c->execute($request, $responseAccessTokenArray["data"]["valid_token"]));
                            }
                            
                            
                  
                    
                    // LAZADA DBS End 
                    
                }
                
                
            }
            
            
            
        }
        
        }catch(exception $ex){

            $isError = true;

        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "isError" => $isError
            );
        }
        // $ordernumber = '317663517587665';
        
        
    }
    
    public function dbslazada(){
        die('Disabled Temporary');
        $dateyday= date("Y-m-d", strtotime("yesterday")); 
        
        $SuccesTransList = DB::table('logistic_transaction')->selectRaw('id')
                                ->where('buyer_email','=','lazada@jocom.my')
                                ->where('status','=',5)
                                ->where('modify_date','LIKE','%'.$dateyday.'%')
                                ->get();
                
        //  print_r($SuccesTransList);      
        foreach ($SuccesTransList as $key => $value) {        
    
        $logisticid = $value->id; 
        $logistcstatus = 5;
        $response = LazadaController::dbsdelivered($logisticid);
      
        }
        // print_r($lazada_arr);
        
        

        // echo 'Done'; 
        

    }
    
    public function expireLazadaToken(){

        $isError = false;

        try{
            
            DB::beginTransaction();
            
            $LazadaAuthCode = LazadaAuthCode::where("status",1)->get();
            if($LazadaAuthCode){
                foreach ($LazadaAuthCode as $key => $value) {
                    $LazadaAuthCodeSub = LazadaAuthCode::find($value->id);
                    $LazadaAuthCodeSub->status = 0;
                    $LazadaAuthCodeSub->save();
                }
            }

        }catch(exception $ex){

            $isError = true;

        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "isError" => $isError
            );
        }

    }
    
    public function lazadanewpriceupdate(){
        die('done');
        echo 'InLazada'.'<br>';
        $lazadatrans = DB::table('jocom_lazada_order')
                              ->select('id','order_number','transaction_id')
                              ->where('created_at','<','2024-10-26 00:00:00')
                              ->get();
        if(count($lazadatrans) > 0){
            foreach ($lazadatrans as $trans) {
                echo $trans->order_number .'-'.$trans->transaction_id.'<br>';
                $trans_id=$trans->transaction_id;
                
                $lazadaitems = DB::table('jocom_lazada_order_items')
                                ->where('order_id',$trans->id)
                                ->get();
                 if(count($lazadaitems) > 0){
                    foreach ($lazadaitems as $transitem) {
                        $APIdata = json_decode($transitem->order_items_details,true);
                        $sku=0;
                        $sku=$APIdata['sku'];
                        $item_price = $APIdata['item_price'];
                        echo $trans_id.'-'.$APIdata['sku'].'-'.$item_price.'<br>';
                        $productsku = Product::where('qrcode',$sku)->first();
                        
                        $trasdetails = DB::table('jocom_transaction_details')->where('transaction_id','=',$trans_id)
                                                                             ->where('sku',$productsku->sku)
                                                                             ->get();
                        if(count($trasdetails)>0){
                            DB::table('jocom_transaction_details')
                                ->where("transaction_id","=",$trans_id)
                                ->where("sku","=",$productsku->sku)
                                ->update(
                                        ['price' => $item_price]
                                );
                                
                             foreach ($trasdetails as $transitem_details) {
                                 $total = 0;
                                 $total = $transitem_details->unit * $item_price;
                                 $totalfrmt = number_format($total,2);
                                 $price = number_format($item_price,2);
                                 echo $transitem_details->id .'-'.$transitem_details->price.'-'.$transitem_details->unit.'-'.$price.'-'.$totalfrmt.'-'.$productsku->sku.'<br>';
                                 
                                //  $d_total = round(($d_price * $d_unit),2);


                                    DB::table('jocom_transaction_details')
                                        ->where("id","=",$transitem_details->id)
                                        ->update(
                                                ['total' => $totalfrmt]
                                        );
                                    # code...
                                 
                             }
                             
                             
                        }
                        
                    }
                    
                     
                 }
                 
              
              // total update
              if($trans_id>0){
                 $trasdetails_1 = DB::table('jocom_transaction_details')
                                    ->select(DB::raw('SUM(total) as totalamount'))
                                    ->where('transaction_id','=',$trans_id)->first();
            echo $trans_id;
            // print_r($trasdetails_1);
            //     if($trans_id == 653)
            //     {
            //         die('In');
            //     }
            
                     if(count($trasdetails_1)>0){

                        // echo '<br>'.$trans_id .'-'.$trasdetails->totalamount.'<br>';
                        // die($trans_id);
                        $transactions = Transaction::find($trans_id);
                        $transactions->total_amount = $trasdetails_1->delivery_charges + $trasdetails_1->totalamount;
                        $transactions->save();
                        echo $trans_id;
                        // die('Out');
                     }
              
              // total update 
                  
              }
              
                
            }
            
        }
                              
        
    }
    
    public function lazadadiscupdate(){
        die('done');
         $lazadatrans = DB::table('jocom_lazada_order')
                              ->select('id','order_number','transaction_id')
                              //->where('created_at','<','2024-10-26 00:00:00')
                              ->get();
        
        // die();
        
            
        if(count($lazadatrans) > 0){
             foreach ($lazadatrans as $trans) {
                echo $trans->order_number .'-'.$trans->transaction_id.'<br>';
                $trans_id=$trans->transaction_id;
                
                $trasdetails_1 = DB::table('jocom_transaction_details')
                                    ->select(DB::raw('SUM(total) as totalamount'))
                                    ->where('transaction_id','=',$trans_id)->first();
                                    
                if(count($trasdetails_1)>0){
                        // $totalamt = ($transactions->delivery_charges + $trasdetails_1->totalamount);
                        // echo '<br>'.$trans_id .'-'.$trasdetails->totalamount.'<br>';
                        // die($trans_id);
                        if($trasdetails_1->totalamount > 0){
                        $transactions = Transaction::find($trans_id);
                        $transactions->total_amount = $transactions->delivery_charges + $trasdetails_1->totalamount;
                        $transactions->save();
                        echo $trans_id . '-'. $transactions->delivery_charges .'<br>';
                        // die('Out');
                        }
                     }
                     
                // $trasdetails = DB::table('jocom_transaction_details')
                //                     ->where('transaction_id','=',$trans_id)
                //                     ->get();
                // foreach ($trasdetails as $transitem_details) {
                //     $new_disc = 0;
                //     $new_per_disc = 0;
                //     $new_disc = $transitem_details->original_price - $transitem_details->price;
                //     $new_per_disc = number_format($new_disc,2);
                //     if($new_per_disc != $transitem_details->disc_per_unit) {
                //     echo $transitem_details->id .'-'.$transitem_details->price.'-<b>'.$transitem_details->disc_per_unit.'</b>-'.$transitem_details->original_price.'-<b>'.$new_per_disc.'</b><br>';
                    
                //     DB::table('jocom_transaction_details')
                //                         ->where("id","=",$transitem_details->id)
                //                         ->update(
                //                                 ['disc_per_unit' => $new_per_disc]
                //                         );
                //     }
                // }
             }
             
             
        }
        
    }
    
    public function lazadapriceupdate(){
        
         die('Done');

            $lazadaPrice = DB::table('jocom_lazada_price')
                              ->get();

            if(count($lazadaPrice) > 0){

                foreach ($lazadaPrice as $value) {
                    
                        echo $value->transaction_id .'==' . $value->qr_code .'<br>';
                        $trans_id = 0;
                        $product_id = 0;
                        $price = 0; 
                        $trans_id = (int) $value->transaction_id;
                        $product_id = (int) $value->qr_code;
                        $price = number_format($value->price,2);

                         DB::table('jocom_transaction_details')
                                ->where("transaction_id","=",$trans_id)
                                ->where("product_id","=",$product_id)
                                ->update(
                                        ['platform_original_price' => $price]
                                );
                    // die('In');
                }
            }

            echo 'Value Updated'.'<br>';
            
            die();

            $lazadatrans = DB::table('jocom_lazada_price')
                              ->select('transaction_id')
                              ->distinct()->get();

            if(count($lazadatrans) > 0){

                foreach ($lazadatrans as $value) {
                    echo $value->transaction_id .'<br>';

                    $t_id = 0;
                    $t_id = $value->transaction_id;

                     $trasdetails = DB::table('jocom_transaction_details')->where('transaction_id','=',$t_id)->get();

                     if(count($trasdetails)>0){
                            foreach ($trasdetails as $value2) {
                                $d_id = 0; 
                                $d_price = 0; 
                                $d_unit = 0; 
                                $d_total = 0;
                                $d_id = $value2->id;
                                $d_price = round($value2->price,2);
                                $d_unit = (int)$value2->unit;

                                $d_total = round(($d_price * $d_unit),2);


                                DB::table('jocom_transaction_details')
                                    ->where("id","=",$d_id)
                                    ->update(
                                            ['total' => $d_total]
                                    );
                                # code...
                            }

                     }

                     $trasdetails = DB::table('jocom_transaction_details')
                                    ->select(DB::raw('SUM(total) as total_amount'))
                                    ->where('transaction_id','=',$t_id)->first();

                     if(count($trasdetails)>0){

                        echo $value->transaction_id .'-'.$trasdetails->total_amount.'<br>';

                        $transactions = Transaction::find($t_id);
                        $transactions->total_amount = $transactions->delivery_charges + $trasdetails->total_amount;
                        $transactions->save();

                     }               



                }

                // 

            }



    }
    
    
    public function priceupdate(){
    
        die('Done');
            $maindata = array(); 

            $date1 = date('Y-m-d').' 00:00:00';
            $date2 = date('Y-m-d').' 23:59:59';
            $date1 = '2022-08-30 00:00:00';

            $lazadaJLO = DB::table('jocom_lazada_order as JLO')
                             ->whereBetween('JLO.created_at',array($date1, $date2))
                             ->where('JLO.transaction_id','!=',0)
                             ->get();
        // die('Done');
            if(count($lazadaJLO) > 0){
                
                
                foreach ($lazadaJLO as $valueJLO) {

                    $orderid = $valueJLO->id;
                    $ordernumber = $valueJLO->order_number;
                    echo $orderid .'-'. $valueJLO->transaction_id.'<br>'; 

                    $tempdata = array(); 
                    $collectdata = array(); 
                    $lazadaLOI = DB::table('jocom_lazada_order_items as LOT')
                                ->where('LOT.order_id','=',$orderid)
                                ->get();
                    
                    
                    foreach ($lazadaLOI as $valueLOI) {

                            $APIData = json_decode($valueLOI->order_items_details, true);
                            
                            array_push($collectdata, array(
                                "order_number"=>$ordernumber,
                                "transaction_id"=>$valueJLO->transaction_id,
                                "sku"=>$APIData['sku'],
                                "paid_price"=>$APIData['item_price']
                            ));
                            
                            // echo '<pre>';
                            //     print_r(array(
                            //     "sku"=>$APIData['sku'],
                            //     "paid_price"=>$APIData['paid_price']
                            // ));
                            //     echo $APIData['sku'] .'<br>';
                            //     echo $APIData['paid_price'] .'<br>'; 
                            // echo '</pre>';
                    // die();
                    }
                    // echo '<pre>';
                    // print_r($collectdata); 
                    // echo '</pre>';
                    
                    array_push($maindata, array(
                                "order_id" => $orderid,
                                "order_number"=>$ordernumber,
                                "transaction_id"=>$valueJLO->transaction_id,
                                "itemdata" => $collectdata,
                     ));

                }
                
                 
                 

            }
                             
                    echo '<pre>';
                    print_r($maindata); 
                    echo '</pre>';                 

            die('End');
           

            $lazadaPrice = DB::table('jocom_lazada_price')
                              ->get();

            if(count($lazadaPrice) > 0){

                foreach ($lazadaPrice as $value) {
                    
                        echo $value->transaction_id .'==' . $value->qr_code .'<br>';
                        $trans_id = 0;
                        $product_id = 0;
                        $price = 0; 
                        $trans_id = (int) $value->transaction_id;
                        $product_id = (int) $value->qr_code;
                        $price = number_format($value->price,2);

                         DB::table('jocom_transaction_details')
                                ->where("transaction_id","=",$trans_id)
                                ->where("product_id","=",$product_id)
                                ->update(
                                        ['price' => $price]
                                );

                }
            }

            echo 'Value Updated'.'<br>';

            $lazadatrans = DB::table('jocom_lazada_price')
                              ->select('transaction_id')
                              ->distinct()->get();

            if(count($lazadatrans) > 0){

                foreach ($lazadatrans as $value) {
                    echo $value->transaction_id .'<br>';

                    $t_id = 0;
                    $t_id = $value->transaction_id;

                     $trasdetails = DB::table('jocom_transaction_details')->where('transaction_id','=',$t_id)->get();

                     if(count($trasdetails)>0){
                            foreach ($trasdetails as $value2) {
                                $d_id = 0; 
                                $d_price = 0; 
                                $d_unit = 0; 
                                $d_total = 0;
                                $d_id = $value2->id;
                                $d_price = round($value2->price,2);
                                $d_unit = (int)$value2->unit;

                                $d_total = round(($d_price * $d_unit),2);


                                DB::table('jocom_transaction_details')
                                    ->where("id","=",$d_id)
                                    ->update(
                                            ['total' => $d_total]
                                    );
                                # code...
                            }

                     }

                     $trasdetails = DB::table('jocom_transaction_details')
                                    ->select(DB::raw('SUM(total) as total_amount'))
                                    ->where('transaction_id','=',$t_id)->first();

                     if(count($trasdetails)>0){

                        echo $value->transaction_id .'-'.$trasdetails->total_amount.'<br>';

                        $transactions = Transaction::find($t_id);
                        $transactions->total_amount = $transactions->delivery_charges + $trasdetails->total_amount;
                        $transactions->save();

                     }               



                }

                // 

            }



    }
    
    
    
}