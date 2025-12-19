<?php

require_once app_path('library/barcodemaster/src/Milon/Barcode/DNS1D.php');
use \Milon\Barcode\DNS1D;


class TaQBinController extends BaseController
{
    
    public function courier(){
        
        
        return View::make('courier.index')->with(
                    [
                    "error" => $isError,
                    "message" => $message
                    ]);
        
        
    }
    
    public function taqbinslip($order_id){

        $orderInfo =  self::getCollectionInfoTAQWayBil($order_id);
        
        include app_path('library/html2pdf/html2pdf.class.php');

        $response = View::make('courier.taq_bin_order_slip')
                ->with('order_info', $orderInfo);

        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
        $html2pdf->setDefaultFont('arial');
        $html2pdf->WriteHTML($response);
        $html2pdf->Output('wira.pdf');
            
//            return response($html2pdf)
//                  ->header('Content-Type', 'application/pdf')
//                  ->header('Content-Length', strlen($pdf))
//                  ->header('Content-Disposition', 'inline; filename="example.pdf"');
       
//        return View::make('courier.taq_bin_order_slip')->with('order_info',$orderInfo);
        
        
    }
    
    public function courierList(){
        
        $orders = CourierOrder::select([
            DB::raw("CONCAT(logistic_transaction_item.name, ' <p> <strong>Label</strong> : ', logistic_transaction_item.label) AS product_name"),
            'jocom_courier_orders.courier_id',
            'jocom_courier_orders.batch_id',
            'jocom_courier_orders.reference_number',
            'jocom_courier_orders.transaction_item_logistic_id',
            'jocom_courier_orders.tracking_no',
            'jocom_courier_orders.product_id',
            'jocom_courier.courier_name',
            'jocom_courier_orders.quantity',
            'jocom_courier_orders.remarks',
            'logistic_transaction.transaction_id',
            'jocom_courier_orders.id',
            'logistic_transaction.status AS LogisticStatus',
            'logistic_batch.status AS BatchStatus',
            'jocom_courier_orders.courier_status',
          // 'logistic_transaction_item.name AS product_name',
             
        ])
        ->leftJoin('logistic_batch', 'logistic_batch.id', '=', 'jocom_courier_orders.batch_id')
        ->leftJoin('jocom_courier', 'jocom_courier_orders.courier_id', '=', 'jocom_courier.id')
        ->leftJoin('logistic_transaction_item', 'logistic_transaction_item.id', '=', 'jocom_courier_orders.transaction_item_logistic_id')
        ->leftJoin('logistic_transaction', 'logistic_transaction_item.logistic_id', '=', 'logistic_transaction.id')->orderBy('jocom_courier_orders.id','desc');
        
        return Datatables::of($orders)->make(true);
        
        
        
    }
    
    
    public function createWayBill() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
                   
            DB::beginTransaction();
            
            $TA_Q_BIN_API_KEY = Config::get('constants.TA_Q_BIN_API_KEY');
            if(Config::get('constants.ENVIRONMENT') == 'live'){
                $TA_Q_BIN_API_URL = Config::get('constants.TA_Q_BIN_API_URL_LIVE');
            }else{
                $TA_Q_BIN_API_URL = Config::get('constants.TA_Q_BIN_API_URL_TEST');
            }
            
            // Collect transaction Information 
            $transaction_id     = Input::get('transaction_id');
//            $transaction_id = 11996;
            $TransactionInfo    = Transaction::find($transaction_id);
            $reservedNumber     = TAQBINReservedNumber::getNextNumber();
            
            $TDetails = TDetails::where("transaction_id",$transaction_id)->get();
            
            foreach ($TDetails as $key => $value) {
                
                $Product = Product::getBySKU($value->sku);
                $ProductLabel = Price::find(1);
                
                $fields = array(
                    "key"=> $TA_Q_BIN_API_KEY,
                    "trackingno"=> $reservedNumber->reserved_number,
                    "orderno"=> $TransactionInfo->id, 
                    "shipoutdate"=> date("Ymd"),
                    "deliverydate"=> date('Ymd', strtotime(' +3 day')),
                    "timezone"=> '0000',
                    "consigneephone"=> $TransactionInfo->delivery_contact_no,
                    "consigneepostcode"=> $TransactionInfo->delivery_postcode,
                    "consigneeadd1"=> $TransactionInfo->delivery_addr_1,
                    "consigneeadd2"=> $TransactionInfo->delivery_addr_2,
                    "consigneeadd3"=> '',
                    "consigneeadd4"=> '',
                    "consigneeadd5"=> '',
                    "consigneename"=> $TransactionInfo->delivery_name,
                    "shipperphone"=> '0322416637',
                    "shipperpostcode"=> '43500',
                    "shipperadd1"=> 'Unit 9-1 , Level 9 , Tower 3 , ',
                    "shipperadd2"=> 'Avenue 3 , Bangsar South , No 8 ',
                    "shipperadd3"=> '',
                    "shippername"=> '11S-Jocom',
                    "itemname1"=> substr($LogisticTItem->name,0,32),
                    "itemname2"=> '',
                    "cod"=> '',
                    "weight"=> ''
                );

                $postedData = json_encode($fields);
                //echo $postedData;

                // Call Ta Q bin API 

                $output = self::ApiCaller($fields, $TA_Q_BIN_API_URL);
               
                if($output == Config::get('constants.TA_Q_BIN_SUCCESS_CODE')){
                    // success
                    $TAQBINReservedNumber = TAQBINReservedNumber::find($reservedNumber->id);
                    $TAQBINReservedNumber->transaction_id = $transaction_id;
                    $TAQBINReservedNumber->is_use = 1;
                    $TAQBINReservedNumber->updated_by = Session::get('username');
                    $TAQBINReservedNumber->save();
                    
                }else{
                    // not success
                    $TAQBINReservedNumber = TAQBINReservedNumber::find($reservedNumber->id);
                    $TAQBINReservedNumber->transaction_id = $transaction_id;
                    $TAQBINReservedNumber->is_use = 1;
                    $TAQBINReservedNumber->is_failed = 1;
                    $TAQBINReservedNumber->updated_by = Session::get('username');
                    $TAQBINReservedNumber->save();
                }
            }
            
            // Updated Reserved Number with transaction id and save posted data
             
        
        } catch (Exception $ex) {
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
      //  return $response;

    
    }
    
    public function assignShiping() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            DB::beginTransaction();
            
            $logistic_transaction_item_id = Input::get('logistic_item_id');
            $quantity   = Input::get('quantity');
            $courier_id   = Input::get('shipper');
            
            $assignShipper = self::assignShipper($logistic_transaction_item_id, $quantity,$courier_id);
            $data['response'] = $assignShipper;
            
        } catch (Exception $ex) {
        
            echo $ex->getLine();
            echo $ex->getMessage();
            $is_error = true;
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    
    public static function assignShipper($logistic_transaction_item_id,$quantity,$courier_id) {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            /*
             * 1. Create batch 
             * 2. Create batch item 
             * 3. Update logistic transaction item quantity
             */
            
            $ItemDetailsID = $logistic_transaction_item_id;
            $LogisticTItem = LogisticTItem::find($ItemDetailsID);
            
            $Courier = Courier::find($courier_id);
            
            switch ($Courier->courier_code) {
                case 'TAQB':
                    $api_response = self::assignToTaQBin($ItemDetailsID,$quantity);
                    break;
                
                case 'LCLEAR':
                    // FOR LINE CLEAR
                    $api_response = self::assignToLineClearNew($ItemDetailsID,$quantity);
                    
                    
                    break;
                
                default:
                    break;
            }
//            echo "<pre>";
//            print_r($api_response);
//            echo "</pre>";
            if($api_response['api_status'] == 1){
                
                $logisticID = $LogisticTItem->logistic_id;
                
                $LogisticBatch = new LogisticBatch();
                $LogisticBatch->logistic_id = $logisticID;
                $LogisticBatch->batch_date = date("Y-m-d h:i:s");
                $LogisticBatch->driver_id = 0;
                $LogisticBatch->shipping_method = $Courier->id;
                $LogisticBatch->tracking_number = $api_response['tracking_number'];
                $LogisticBatch->do_no = '';
                $LogisticBatch->status = 1;
                $LogisticBatch->assign_by = Session::get('username');
                $LogisticBatch->assign_date = date("Y-m-d h:i:s");
                $LogisticBatch->save();

                $BatchID = $LogisticBatch->id;
                
                $CourierOrder = CourierOrder::find($api_response['courier_order_id']);
                $CourierOrder->batch_id = $BatchID;
                $CourierOrder->save();

                $LogisticBatchItem = new LogisticBatchItem();
                $LogisticBatchItem->batch_id = $BatchID;
                $LogisticBatchItem->transaction_item_id = $LogisticTItem->id;
                $LogisticBatchItem->qty_assign = $quantity;
                $LogisticBatchItem->qty_pickup = $quantity;
                $LogisticBatchItem->qty_sent = '';
                $LogisticBatchItem->remark = '';
                $LogisticBatchItem->save();

                $LogisticTItem = LogisticTItem::find($ItemDetailsID);
                $LogisticTItem->qty_to_assign = $LogisticTItem->qty_to_assign - ($quantity);
                $LogisticTItem->save();   

                $LogisticTransaction = LogisticTransaction::find($logisticID);
                $LogisticTransaction->status = 4;
                $LogisticTransaction->save();
                
                $data['is_assign'] = 1;
                $data['courier_code'] = $Courier->courier_code;
                $data['result'] = $api_response;                
            }else{
                $data['is_assign'] = 0;
                $data['result'] = $api_response; 
            }
            
        
        } catch (Exception $ex) {
           echo  $ex->getMessage();
                    } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        return $data;

    
    }
    
    public function assignToLineClear($logistic_item_id,$quantity) {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
    

        try {
            DB::beginTransaction();
            
            $Courier = Courier::getCourierByCode('LCLEAR');
            $LogisticTItem = LogisticTItem::find($logistic_item_id);

            $LogisticTransaction = LogisticTransaction::find($LogisticTItem->logistic_id);

            $ElevenStreetOrder = ElevenStreetOrder::where('transaction_id',$LogisticTransaction->transaction_id)->first();
             
            if(count($ElevenStreetOrder) > 0){
                $orderno  = $ElevenStreetOrder->order_number;
            }else{
                $orderno  = $LogisticTransaction->transaction_id;
            }
            
            // Add to shipper list
            $CourierOrder = new CourierOrder();
            $CourierOrder->courier_id = $Courier->id;
            $CourierOrder->transaction_item_logistic_id = $logistic_item_id;
            $CourierOrder->batch_id = '';
            $CourierOrder->response_message = '';
            $CourierOrder->api_post = '';
            $CourierOrder->reference_number = $orderno;
            $CourierOrder->tracking_no = '';
            $CourierOrder->quantity = $quantity;
            $CourierOrder->remarks = '';
            $CourierOrder->created_by = Session::get('username');
            $CourierOrder->updated_by = Session::get('username');
            $CourierOrder->status = 1;
            $CourierOrder->save();

            $data['api_response'] = 1;
            $data['courier_order_id'] = $CourierOrder->id;
            $data['tracking_number'] = '';
            $data['api_post'] = '';
            $data['api_status'] = 1;
        
        } catch (Exception $ex) {
            
            echo $ex->getMessage();
            
            } finally {
                if ($is_error) {
                    DB::rollback();
                } else {
                    DB::commit();
                }
        }

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        
//        echo "<pre>";
//        print_r($response);
//        echo "</pre>";
//      
        
        return $data;

    
    }
    
    function generateRandomString($length = 2) {
        
        $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return strtoupper($randomString);
    }
    
    // WHEN GET OFFICIAL AGENT ID
    public function assignToLineClearNew($logistic_item_id,$quantity) {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
    

        try {
            
            
            DB::beginTransaction();
            
            $Courier = Courier::getCourierByCode('LCLEAR');
            $LogisticTItem = LogisticTItem::find($logistic_item_id);

            $LogisticTransaction = LogisticTransaction::find($LogisticTItem->logistic_id);

            $ElevenStreetOrder = ElevenStreetOrder::where('transaction_id',$LogisticTransaction->transaction_id)->first();
            
             // DO NUMBER ACCORDING TO LINECLEAR FORMAT
            $doNo = $LogisticTransaction->do_no;
            // DO NUMBER ACCORDING TO LINECLEAR FORMAT
          
            $AWB_No = str_replace('DO-','JCM',$LogisticTransaction->do_no).self::generateRandomString(2); // will be DO Number without '-'
             
            if(count($ElevenStreetOrder) > 0){
                $orderno  = $ElevenStreetOrder->order_number;
            }else{
                $orderno  = $LogisticTransaction->transaction_id;
            }
            
            $wayBillOrderResponse = LineClearController::generateWaybillOrder($orderno,$logistic_item_id,$AWB_No,$quantity);
   
            $wayBillOrder = $wayBillOrderResponse['response'];
            $output = $wayBillOrder['Reason'];
           
            if($wayBillOrder['Success'] == 'Yes'){
                // Add to shipper list
                $CourierOrder = new CourierOrder();
                $CourierOrder->courier_id = $Courier->id;
                $CourierOrder->transaction_item_logistic_id = $logistic_item_id;
                $CourierOrder->batch_id = '';
                $CourierOrder->response_message = $output;
                $CourierOrder->api_post = $wayBillOrderResponse['dataPost'];
                $CourierOrder->reference_number = $orderno;
                $CourierOrder->tracking_no = $AWB_No;
                $CourierOrder->quantity = $quantity;
                $CourierOrder->remarks = '';
                $CourierOrder->created_by = Session::get('username');
                $CourierOrder->updated_by = Session::get('username');
                $CourierOrder->status = 1;
                $CourierOrder->save();

                $data['api_response'] = $output;
                $data['courier_order_id'] = $CourierOrder->id;
                $data['tracking_number'] = $wayBillOrder['DockNo'];
                $data['api_post'] = '';
                $data['api_status'] = 1;
        
            }else{
                $data['api_response'] = $output;
                $data['tracking_number'] = '';
                $data['api_status'] = 0;
            }
            
        
        } catch (Exception $ex) {
            
            } finally {
                if ($is_error) {
                    DB::rollback();
                } else {
                    DB::commit();
                }
        }

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        
//        echo "<pre>";
//        print_r($response);
//        echo "</pre>";
//      
        
        return $data;

    
    }
    
    public static function assignToTaQBin($logistic_item_id,$quantity){
        
        
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        
        try{
            
            $TA_Q_BIN_API_KEY = Config::get('constants.TA_Q_BIN_API_KEY');

            if(Config::get('constants.ENVIRONMENT') == 'live'){
                $TA_Q_BIN_API_URL = Config::get('constants.TA_Q_BIN_API_URL_LIVE');
            }else{
                $TA_Q_BIN_API_URL = Config::get('constants.TA_Q_BIN_API_URL_TEST');
            }
            
            $Courier = Courier::getCourierByCode('TAQB');

            $reservedNumber = TAQBINReservedNumber::getNextNumber();
            $tracking_number = $reservedNumber->reserved_number;
            $LogisticTItem = LogisticTItem::find($logistic_item_id);

            $LogisticTransaction = LogisticTransaction::find($LogisticTItem->logistic_id);

            $ElevenStreetOrder = ElevenStreetOrder::where('transaction_id',$LogisticTransaction->transaction_id)->first();
             
            if(count($ElevenStreetOrder) > 0){
                $orderno  = $ElevenStreetOrder->order_number;
            }else{
                $orderno  = $LogisticTransaction->transaction_id;
            }

            $Price = Price::find($LogisticTItem->product_price_id);
            $weight = ($Price->p_weight) / 1000 * ($quantity);

            if($weight > 0){

                $fields = array(
                        "key"=> $TA_Q_BIN_API_KEY,
                        "trackingno"=> $tracking_number,
                        "orderno"=> $orderno, 
                        "shipoutdate"=> date("dmY"),
                        "deliverydate"=> date('dmY', strtotime(' +3 day')),
                        "timezone"=> '0000',
                        "consigneephone"=> $LogisticTransaction->delivery_contact_no,
                        "consigneepostcode"=> $LogisticTransaction->delivery_postcode,
                        "consigneeadd1"=> $LogisticTransaction->delivery_addr_1,
                        "consigneeadd2"=> $LogisticTransaction->delivery_addr_2,
                        "consigneeadd3"=> '',
                        "consigneeadd4"=> '',
                        "consigneeadd5"=> '',
                        "consigneename"=> $LogisticTransaction->delivery_name,
                        "shipperphone"=> '0322416637',
                        "shipperpostcode"=> '43500',
                        "shipperadd1"=> 'Unit 9-1 , Level 9 , Tower 3 , ',
                        "shipperadd2"=> 'Avenue 3 , Bangsar South , No 8 ',
                        "shipperadd3"=> '',
                        "shippername"=> '11S-Jocom',
                        "itemname1"=> substr($LogisticTItem->name,0,32),
                        "itemname2"=> '',
                        "cod"=> '',
                        "weight"=> $weight
                );

                $output = self::ApiCaller($fields, $TA_Q_BIN_API_URL);

                if($output == Config::get('constants.TA_Q_BIN_SUCCESS_CODE')){
                    // success
                    $TAQBINReservedNumber = TAQBINReservedNumber::find($reservedNumber->id);
                    $TAQBINReservedNumber->transaction_item_logistic_id = $logistic_item_id;
                    $TAQBINReservedNumber->is_use = 1;
                    $TAQBINReservedNumber->updated_by = Session::get('username');
                    $TAQBINReservedNumber->save();

                    // Add to shipper list
                    $CourierOrder = new CourierOrder();
                    $CourierOrder->courier_id = $Courier->id;
                    $CourierOrder->transaction_item_logistic_id = $logistic_item_id;
                    $CourierOrder->batch_id = '';
                    $CourierOrder->response_message = $output;
                    $CourierOrder->api_post = json_encode($fields);
                    $CourierOrder->reference_number = $orderno;
                    $CourierOrder->tracking_no = $tracking_number;
                    $CourierOrder->quantity = $quantity;
                    $CourierOrder->remarks = '';
                    $CourierOrder->created_by = Session::get('username');
                    $CourierOrder->updated_by = Session::get('username');
                    $CourierOrder->status = 1;
                    $CourierOrder->save();
                    
                    $data['api_response'] = $output;
                    $data['courier_order_id'] = $CourierOrder->id;
                    $data['tracking_number'] = $tracking_number;
                    $data['api_post'] = json_encode($fields);
                    $data['api_status'] = 1;

                }else{
                    $data['api_response'] = $output;
                    $data['tracking_number'] = '';
                    $data['api_status'] = 0;
                }

            }else{
                // Send Error
                $data['api_response'] = 'No product weight';
                $data['tracking_number'] = '';
                $data['api_status'] = 0;
            }
        
        } catch (Exception $ex) {
            
            // Send Error
            $data['api_response'] = $ex->getMessage();
            $data['api_status'] = 0;

        }
        
        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $data;
        
    }
    
    public static function getCollectionInfoTAQWayBil($order_id){
        
        $CourierOrder  = CourierOrder::getOrderInfo($order_id);
//        echo "<pre>";
//        print_r($CourierOrder);
//        echo "</pre>";
//        die();
//        echo "<pre>";
//        
//         
//         print_r($API_post);
//        echo "</pre>";
//        die();
        
       
        $timezone = array(
            "0000"=>'No time zone delivery',
            "0812"=>'8:00 AM to 12:00 PM delivery',
            "1215"=>'12:00 PM to 3:00 PM delivery',
            "1518"=>'3:00 PM to 6:00 PM delivery',
            "1821"=>'6:00 PM to 9:00 PM delivery'
        );
        
        $d = new DNS1D();
        $d->setStorPath(__DIR__."/cache/");
        
        $sortingcode = TAQBINSortingCode::where("postcode",$CourierOrder->delivery_postcode)->first();
        $taqbin_sortcode = substr($sortingcode->sorting_code,0,3)."-".substr($sortingcode->sorting_code,3,2)."-".substr($sortingcode->sorting_code,5,2);
        
        $API_post = json_decode($CourierOrder->api_post);

        $cossignee_info = array(
            "consigneed_name" => $CourierOrder->delivery_name,
            "address_1" => $CourierOrder->delivery_addr_1,
            "address_2" => $CourierOrder->delivery_addr_2,
            "city" => $CourierOrder->delivery_city,
            "postcode" => $CourierOrder->delivery_postcode,
            "state" => $CourierOrder->delivery_state,
            "country" => $CourierOrder->delivery_country,
            "phone" => $CourierOrder->delivery_contact_no,
            "tracking_no" => $CourierOrder->tracking_no,
            "taq_bin_zone_code" => $taqbin_sortcode,
            "customer_ref_code" => $CourierOrder->reference_number,
            "barcode" => '<img src="data:image/png;base64,' . $d->getBarcodePNG($CourierOrder->tracking_no, "CODABAR",2,50) . '" alt="barcode"   />' ,
        );
        
        $shipper_info = array(
            "from" => 'Jocom MShopping Sdn Bhd',
            "address_1" => 'Unit 9-1, Level 9, Tower 3, Avenue 3',
            "address_2" => 'Bangsar South, No. 8 Jalan Kerinchi',
            "city" => 'Kuala Lumpur',
            "postcode" => '59200',
            "state" => '',
            "country" => 'Malaysia',
            "phone" => '03-2241 6637',
            "customer_code" => '0322416637',
        );
        
        $shipment_schedule = array(
            "shipment_schedule_date" => substr($API_post->shipoutdate,0,2)."-".substr($API_post->shipoutdate,2,2)."-".substr($API_post->shipoutdate,4,7),
            "prefered_delivery_date" => substr($API_post->deliverydate,0,2)."-".substr($API_post->deliverydate,2,2)."-".substr($API_post->deliverydate,4,7), 
            "timezone_delivery_date" => $timezone[$API_post->timezone]
        );
        
        $item_schedule = array(
            "item_description" => substr($CourierOrder->name,0,32)." - ".$CourierOrder->label ." <br> QTY: ".$CourierOrder->quantity,
            "item_weight" => $API_post->weight." Kg",
        );
        
        
        return array(
            "cossignee_info" => $cossignee_info,
            "shipper_info" => $shipper_info,
            "shipment_schedule" => $shipment_schedule,
            "item_schedule" => $item_schedule
        );
        
        
    }
    
    
    public static function trackingSearch() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            DB::beginTransaction();
        
        } catch (Exception $ex) {
                    } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    
    public static function ApiCaller($fields,$url){
        
        $ch = curl_init();  

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, count($fields));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $output=curl_exec($ch);
        curl_close($ch);
        
        return $output;
        
    }
    
    
    public function trackOrder() {
    

        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        $user_id = Config::get('constants.TA_Q_BIN_TRACKING_USERID');
        $user_password = Config::get('constants.TA_Q_BIN_TRACKING_PASSWORD');
        $URL = Config::get('constants.TA_Q_BIN_TRACKING_URL');
        $courier_id = 1; // TA Q BIN ID

        try {
            
            DB::beginTransaction();
            
            $dataResponse = array();
            
            $CourierOrders = CourierOrder::getCourierOrders($courier_id);
           echo "<pre>";
           print_r($CourierOrders);
           echo "</pre>";
            foreach ($CourierOrders as $key => $value) {
                
                $fields = array(
                    "id"=> $user_id,
                    "pwd"=> $user_password,
                    "no"=> $value->tracking_no, 
                    "dtKbn"=> 1,
                    "dtOrder"=> 1
                );
            
                $output = self::ApiCaller($fields, $URL);
              
                //print_r($output);
                $Resultlines = explode("\n",$output);
                // print_r(explode(",",$Resultlines[1]));
                $responseData = explode(",",$Resultlines[1]);
                
                $trackingNumber = str_replace('"',"",$responseData[0]);
                $courierStatus = str_replace('"',"",$responseData[9]);
                $courierStatusCode = str_replace('"',"",$responseData[8]);
                
                
                array_push($dataResponse, array(
                    "trackingNumber" => $trackingNumber,
                    "courierStatusCode" => $courierStatusCode,
                    "courierStatus" => $courierStatus
                ));
                
                $CourierOrderInfo = CourierOrder::findByTrackingNo($trackingNumber);
                
                $CourierOrder = CourierOrder::find($CourierOrderInfo->id);
                $CourierOrder->courier_status_code = $courierStatusCode;
                $CourierOrder->courier_status = $courierStatus;
                $CourierOrder->save();
                
                if($courierStatusCode == '90'){
                    LogisticBatch::UpdateBatch($CourierOrder->batch_id, array("status"=>4));
}

            }

            $data['trackingResponse'] = $dataResponse;
            
        
        } catch (Exception $ex) {
                    
            $is_error = true;
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        } 

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    
    
}


?>