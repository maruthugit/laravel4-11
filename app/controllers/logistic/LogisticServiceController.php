<?php

class LogisticServiceController extends BaseController {
        
    
    
    public function saveOrderDelivery(){
        
        /*
         * 1. Save order 
         * 2. Save order item deescription 
         * 3. Create transaction with product service delivery 
         * 4. Generate DO 
         * 5. View DO and Invoice 
         */
               
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        $messageCode = '';
            
        try{
            
            DB::beginTransaction();
            
            $reference_number   = Input::get('reference_number');
            $receiver_name      = Input::get('receiver_name');
            $receiver_address   = Input::get('receiver_address');
            $receiver_postcode  = Input::get('receiver_postcode');
            $receiver_city      = Input::get('receiver_city');
            $receiver_state     = Input::get('receiver_state');
            $receiver_country   = Input::get('receiver_country');
            $receiver_phone   = Input::get('receiver_phone');
            //
            
            $ship_out_date   = Input::get('ship_out_date');
            $prefered_delivery_date   = Input::get('prefered_delivery_date');
            
            $item_sku   = Input::get('item_sku');
            $item_description   = Input::get('item_description');
            $item_label  = Input::get('item_label');
            $quantity   = Input::get('quantity');
            $item_total_weight   = Input::get('item_total_weight');
            
            
            $shipper_id   = Input::get('shipper_id');
            $shipper_username   = Input::get('shipper_username');
            
            /* Save Delivery Order  */
            $DeliveryOrder = new DeliveryOrder;
            $DeliveryOrder->reference_number = $reference_number;
            $DeliveryOrder->transaction_id = '';
            $DeliveryOrder->logistic_id = '';
            $DeliveryOrder->receiver_name = $receiver_name;
            $DeliveryOrder->receiver_address = $receiver_address;
            $DeliveryOrder->receiver_postcode = $receiver_postcode;
            $DeliveryOrder->receiver_city = $receiver_city;
            $DeliveryOrder->receiver_state = $receiver_state;
            $DeliveryOrder->receiver_country = $receiver_country;
            $DeliveryOrder->receiver_phone = $receiver_phone;
            $DeliveryOrder->ship_out_date = date("Y-m-d", strtotime(str_replace('/', '-', $ship_out_date)));
            $DeliveryOrder->prefered_delivery_date = date("Y-m-d", strtotime(str_replace('/', '-', $prefered_delivery_date)));
            $DeliveryOrder->shipper_id = $shipper_id;
            $DeliveryOrder->created_by = $shipper_username;
            $DeliveryOrder->updated_by = $shipper_username;
            $DeliveryOrder->activation = 1;
            $DeliveryOrder->status = 1;
            $DeliveryOrder->save();
            
            $DeliveryOrderID = $DeliveryOrder->id;
            
            /* Save Delivery Order Items */
            
            $DeliveryOrderItems = new DeliveryOrderItems;
            $DeliveryOrderItems->service_order_id = $DeliveryOrderID;
            $DeliveryOrderItems->item_sku = $item_sku;
            $DeliveryOrderItems->item_description = $item_description;
            $DeliveryOrderItems->item_label = $item_label;
            $DeliveryOrderItems->quantity = $quantity;
            $DeliveryOrderItems->item_total_weight = $item_total_weight;
            $DeliveryOrderItems->created_by = $shipper_username;
            $DeliveryOrderItems->updated_by = $shipper_username;
            $DeliveryOrderItems->status = 1;
            $DeliveryOrderItems->activation = 1;
            $DeliveryOrderItems->save();
            
            /* CREATE TRANSACTION */
            $transaction_date = date("Y-m-d h:i:s");
            
            // Get Shipper Information //
            
            $env = Config::get('constants.ENVIRONMENT');
            if ($env == 'live'){
                $qrcode = array('JC7110');
                $priceopt = array('12155');
            }else{
                $qrcode = array('JC5570');
                $priceopt = array('10396');
            }

            $qty = array('1');
            $username = $shipper_username;
            
            $get = array(
                'user'                => $username,             // Buyer Username
                'delivery_name'       => $receiver_name,      // delivery name
                'delivery_contact_no' => $receiver_phone, // delivery contact no
                'special_msg'         => "Delivery services",       // special message
                'delivery_addr_1'     => $receiver_address,
                'delivery_addr_2'     => "",
                'delivery_postcode'   => $receiver_postcode,
                'delivery_city'       => $receiver_city, // City ID 
                'delivery_state'      => $receiver_state,                          // State ID
                'delivery_country'    => $receiver_country,                 // Country ID
                'qrcode'              => $qrcode,
                'price_option'        => $priceopt, // Price Option
                'qty'                 => $qty,
                'devicetype'          => "web",
                'isDelivery'          => "1",
                'uuid'                => NULL, // City ID
                'lang'                => 'EN',
                'ip_address'          => Request::getClientIp(),
                'location'            => '',
                'transaction_date'    => $transaction_date,
                'charity_id'          => '',
            );
            
            $dataCheckout = MCheckout::checkout_transaction($get);
            $data['Checkout'] = $dataCheckout;
            if($dataCheckout['status'] == 'success'){
                $transaction_id = $dataCheckout['transaction_id'];
                // Update Order 
                $DeliveryOrder = DeliveryOrder::find($DeliveryOrderID);
                $DeliveryOrder->transaction_id = $transaction_id;
                $DeliveryOrder->save();
                
                // Create status transaction as completed //
                
                $transaction_id = $dataCheckout['transaction_id'];
                $Transaction = Transaction::find($transaction_id);
                $Transaction->checkout_source = 3;
                $Transaction->modify_by = $username;
                $Transaction->modify_date = date("Y-m-d h:i:s");
                $Transaction->status = 'completed';
                $Transaction->save();
                
                // Insert scheduler to create DO and Invoice
                // GENERATE INVOICE
                $tempInv = MCheckout::generateInv($transaction_id, true,false);
                $tempDO = MCheckout::generateDO($transaction_id, true,true);
                LogisticTransaction::log_transaction($transaction_id);
                // GENERATE DO 
                    
            }else{
                throw new Exception("Failed to create transaction", '901');
            }
            /* CREATE DELIVERY ORDER */
            
            
            
        }catch (Exception $ex) {
            
            $is_error = true;
            $error_line = $ex->getLine();
            $errorCode = $ex->getCode();
            $messageCode = '101';
            
            
        }finally{
            if($is_error){
                DB::rollback();
            }else{
                DB::commit();
            }
             $messageCode = '102';
        }
        
        /* Return Response */
        $response = array("RespStatus"=> $RespStatus,"error"=> $is_error,"error_code" => $errorCode,"error_line" => $error_line,"message" => $message,"messageCode"=>$messageCode, "data"=> $data);
        return $response; 
        
        
    }
    
    public function getListOrderService() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        $shipper_id   = Input::get('shipper_id');
        $api_verification   = Input::get('api_verification');
        
        try {
            
            $orders = DB::table('jocom_delivery_order')
                    ->leftJoin('jocom_transaction', 'jocom_delivery_order.transaction_id', '=', 'jocom_transaction.id')
                    ->leftJoin('logistic_transaction', 'logistic_transaction.transaction_id', '=', 'jocom_delivery_order.transaction_id')
                    ->where('jocom_delivery_order.shipper_id', '=', $shipper_id)
                    ->select('jocom_delivery_order.*','jocom_transaction.status','logistic_transaction.status AS LogisticStatus')
                    ->orderBy('jocom_delivery_order.id',"DESC")
                    ->paginate(30);
            
            $data['order'] = $orders;
            
            
        }catch (Exception $ex) {
            return $ex->getMessage();
        }finally{
            /* Return Response */
            return $orders;
        }

        

    
    }
    
    
    public function checkDeliveryStatus() {

        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            
            $shipper_id   = Input::get('shipper_id');
            $api_verification   = Input::get('api_verification');
            $tracking_number   = Input::get('tracking_number');
            
            $DeliveryOrder = DeliveryOrder::find($tracking_number);
            
            $data['DeliveryOrder'] = $DeliveryOrder;
            
            
            
        
        } catch (Exception $ex) {
            
        } finally {
            /* Return Response */
            $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
            return $response;
        }

    }
    
    public function getShipperInformation() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            
            $shipper_id   = Input::get('shipper_id');
            
            $Customer = Customer::find($shipper_id);
            
            $profile = array(
                "name" => $Customer->full_name,
                "phone" => $Customer->mobile_no,
                "address" => $Customer->address1,
                "postcode" => $Customer->postcode,
                "city_id" => $Customer->city_id,
                "state_id" => $Customer->state_id,
                "country_id" => $Customer->country_id,
                "email" => $Customer->email
            );
            
            $data['profile'] = $profile;
            
        
        } catch (Exception $ex) {
            
        } finally {
           
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    public function updateShipper(){
        
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $messageCode = "";
        $error_line = "";

        try {
            
            DB::beginTransaction();
            
            
            $shipper_id   = Input::get('shipper_id');
            $mobile_no   = Input::get('mobile_no');
            $email   = Input::get('email');
            $address   = Input::get('address');
            $postcode   = Input::get('postcode');
            $state_id   = Input::get('state_id');
            $city_id   = Input::get('city_id');
            $country_id   = Input::get('country_id');
            
            $State = State::find($state_id);
            $City = City::find($city_id);
            $Country = Country::find($country_id);
            
            $Customer = Customer::find($shipper_id);
            $Customer->mobile_no = $mobile_no ;
            $Customer->email = $email;
            $Customer->address1 = $address;
            $Customer->state_id = $state_id;
            $Customer->state = $State->name;
            $Customer->postcode = $postcode;
            $Customer->city_id = $city_id;
            $Customer->city = $City->name;
            $Customer->country_id = $country_id;
            $Customer->country = $Country->name;
            $Customer->save();
            
        
        } catch (Exception $ex) {
            $is_error = true;
            $message = $ex->getMessage();
            
        } finally {
            if($is_error){
                DB::rollback();
                $messageCode = 103;
            }else{
                DB::commit();
                $messageCode = 102;
            }
        }

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "messageCode"=>$messageCode,"error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

        
    }
    
    
    public function searchService() {

        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            
            $keyword = Input::get('keyword');
            $shipper_id = Input::get('shipper_id');
            
            
            $orders = DB::table('jocom_delivery_order')
                    ->leftJoin('jocom_transaction', 'jocom_delivery_order.transaction_id', '=', 'jocom_transaction.id')
                    ->leftJoin('logistic_transaction', 'logistic_transaction.transaction_id', '=', 'jocom_delivery_order.transaction_id')
                   
                    // ->where(function ($query) {
                    //     $query->where('jocom_delivery_order.reference_number', 'like', "%$keyword%")
                    //           ->orWhere('jocom_delivery_order.transaction_id', 'like', "%$keyword%");
                    // })
                    ->where('jocom_delivery_order.reference_number', 'like', "%$keyword%")
                    ->where('jocom_delivery_order.shipper_id', '=', $shipper_id)
                    ->select('jocom_delivery_order.*','jocom_transaction.status','logistic_transaction.status AS LogisticStatus')
                    ->orderBy('jocom_delivery_order.id',"DESC")
                    ->get();
                    
            $data['orders'] = $orders;
            
        } catch (Exception $ex) {
            
        } finally {
            
        }

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    public function updateShipperPassword(){
        
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            
            $shipper_id = Input::get('shipper_id');
            $password = Input::get('password');
            
            $hashed_password = Hash::make(Input::get('password'));
            $username = $Customer->username;
            $Customer = Customer::find($shipper_id);
            $Customer->password = $hashed_password;
            $Customer->modify_by = $username;
            $Customer->modify_date = date("Y-m-d h:i:s");
            $Customer->save();
            
           
        } catch (Exception $ex) {
            
        } finally {
            
        }

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;
        
        
    }
    
    
    
    public function download($id)
    {
       
        $DeliveryOrder = DeliveryOrder::find($id);
        $transaction_id = $DeliveryOrder->transaction_id;
        $Transaction = Transaction::find($transaction_id);
        
        $file = Config::get('constants.DO_PDF_FILE_PATH') . '/' . $Transaction->do_no . '.pdf';

        //echo $loc;
        if (file_exists($file)) {
            $headers = array(
              'Content-Type: application/pdf',
            );
            return Response::download($file, $Transaction->do_no.'.pdf', $headers);
            exit;
        } else {
            
            $file_path = Config::get('constants.DO_PDF_FILE_PATH');
            $file_name = $Transaction->do_no.".pdf";
                include app_path('library/html2pdf/html2pdf.class.php');

                $DOView = TransactionController::createDOView($Transaction);

                $response =  View::make('checkout.do_view')
                            ->with('display_details', $DOView['general'])
                            ->with('display_trans', $DOView['trans'])
                            ->with('display_seller', $DOView['paypal'])
                            ->with('display_product', $DOView['product'])
                            ->with('display_group', $DOView['group'])
                            ->with('deliveryservice', $DOView['deliveryservice'])
                            ->with("display_delivery_service_items",$DOView['DeliveryOrderItems']);

                $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            
                $html2pdf->setDefaultFont('arialunicid0');
                
                $html2pdf->WriteHTML($response);
              
                $html2pdf->Output($file_path."/".$file_name, 'F');

                return Response::download($file_path."/".$file_name);
                exit;
                echo "<script>window.close();</script>";
        }
    }
    
    /*
     * International Delivery 
     */
    
    
    public function saveOrderDeliveryInternational(){
        
        /*
         * 1. Save order 
         * 2. Save order item deescription 
         * 3. Create transaction with product service delivery 
         * 4. Generate DO 
         * 5. View DO and Invoice 
         */
               
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        $messageCode = '';
            
        try{
            
            DB::beginTransaction();
            
            
            /*
            "currency_code"=>$currency_code,
            "amount_value"=>$amount_value,
            "recipient_id"=>$receiver_id_number,
            "from_country"=>$from_country,
        
        */
            
            $reference_number   = Input::get('reference_number');
            $receiver_name      = Input::get('receiver_name');
            $receiver_address   = Input::get('receiver_address');
            $receiver_postcode  = Input::get('receiver_postcode');
            $receiver_city      = Input::get('receiver_city');
            $receiver_state     = Input::get('receiver_state');
            $receiver_country   = Input::get('receiver_country');
            $receiver_phone   = Input::get('receiver_phone');
            $from_country   = Input::get('from_country');
            
            //
            
            $ship_out_date   = DATE("Y-m-d h:i:s");
            $prefered_delivery_date   = DATE("Y-m-d h:i:s");
            
            $item_sku   = Input::get('item_sku');
            $item_description   = Input::get('item_description');
            $item_label  = Input::get('item_label');
            $quantity   = Input::get('quantity');
            $item_total_weight   = Input::get('item_total_weight');
            
            // New For Oversea delivery
            
            $currency_code  = Input::get('currency_code');
            $amount_value  = Input::get('amount_value');
            $recipient_id  = Input::get('recipient_id');
            
            
            $shipper_id   = Input::get('shipper_id');
            $shipper_username   = Input::get('shipper_username');
            
         
            
            /* Save Delivery Order  */
            $DeliveryOrder = new DeliveryOrder;
            $DeliveryOrder->reference_number = $reference_number;
            $DeliveryOrder->transaction_id = '';
            $DeliveryOrder->logistic_id = '';
            $DeliveryOrder->receiver_name = $receiver_name;
            $DeliveryOrder->receiver_address = $receiver_address;
            $DeliveryOrder->receiver_postcode = $receiver_postcode;
            $DeliveryOrder->receiver_city = $receiver_city;
            $DeliveryOrder->receiver_state = $receiver_state;
            $DeliveryOrder->receiver_country = $receiver_country;
            $DeliveryOrder->receiver_phone = $receiver_phone;
            $DeliveryOrder->ship_out_date = $ship_out_date;
            $DeliveryOrder->prefered_delivery_date = $prefered_delivery_date;
            
            // NEW coloum //
            $DeliveryOrder->shipper_id = $shipper_id;
            $DeliveryOrder->recipient_id = $recipient_id;
            $DeliveryOrder->currency_code = $currency_code;
            $DeliveryOrder->from_country = $from_country;
            // NEW coloum //
            
            $DeliveryOrder->created_by = $shipper_username;
            $DeliveryOrder->updated_by = $shipper_username;
            $DeliveryOrder->activation = 1;
            $DeliveryOrder->status = 1;
            $DeliveryOrder->save();
            
            $DeliveryOrderID = $DeliveryOrder->id;
            
            /* Save Delivery Order Items */
            
            $DeliveryOrderItems = new DeliveryOrderItems;
            $DeliveryOrderItems->service_order_id = $DeliveryOrderID;
            $DeliveryOrderItems->item_sku = $item_sku;
            $DeliveryOrderItems->item_description = $item_description;
            $DeliveryOrderItems->item_label = $item_label;
            $DeliveryOrderItems->quantity = $quantity;
            $DeliveryOrderItems->item_total_weight = $item_total_weight;
            $DeliveryOrderItems->amount_value = $amount_value;
            $DeliveryOrderItems->created_by = $shipper_username;
            $DeliveryOrderItems->updated_by = $shipper_username;
            $DeliveryOrderItems->status = 1;
            $DeliveryOrderItems->activation = 1;
            $DeliveryOrderItems->save();
            
            /* CREATE TRANSACTION */
            $transaction_date = date("Y-m-d h:i:s");
            
            // Get Shipper Information //
            $env = Config::get('constants.ENVIRONMENT');
           
            if ($env == 'live'){
                
                if($from_country == 156){
                    
                    $qrcode = array('JC15071');
                    $priceopt = array('21376');
                    
                }else{
                    
                    $qrcode = array('JC15072');
                    $priceopt = array('21377');
                    
                }
                
            }else{
                
                $qrcode = array('JC5905');
                $priceopt = array('16372');
            }
            
            $qty = array('1');
            $username = $shipper_username;
            
            $main_business_currency = isset($_POST["main_bussines_currency"]) ? $_POST["main_bussines_currency"] : 'MYR' ;
                
                switch ($main_business_currency) {
                    
                    case 'MYR':
                        
                        $main_business_currency = 'MYR';
                        $base_currency = 'MYR';
                        $standard_currency = 'USD';
                        
                        $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $base_currency);
                        $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $standard_currency);
                        $base_currency_rate = $base_currency_rate_data->amount_to;
                        $standard_currency_rate = $standard_currency_rate_data->amount_to;
                        
                        break;
                    case 'RMB':
                        
                        $main_business_currency = 'RMB';
                        $base_currency = 'MYR';
                        $standard_currency = 'USD';
                        
                        $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $base_currency);
                        $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $standard_currency);
                        $base_currency_rate = $base_currency_rate_data->amount_to;
                        $standard_currency_rate = $standard_currency_rate_data->amount_to;
                        
                        
                        break;
                  
                    default:
                        
                        $main_business_currency = 'MYR';
                        $base_currency = 'MYR';
                        $standard_currency = 'USD';
                        
                        $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $base_currency);
                        $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $standard_currency);
                        $base_currency_rate = $base_currency_rate_data->amount_to;
                        $standard_currency_rate = $standard_currency_rate_data->amount_to;
                       
                 }
            
            $get = array(
                'user'                => $username,             // Buyer Username
                'delivery_name'       => $receiver_name,  
                'delivery_contact_no' => $receiver_phone, // delivery contact no
                'special_msg'         => "Delivery services",       // special message
                'delivery_addr_1'     => $receiver_address,
                'delivery_addr_2'     => "",
                'delivery_postcode'   => $receiver_postcode,
                'delivery_city'       => $receiver_city, // City ID 
                'delivery_state'      => $receiver_state,                          // State ID
                'delivery_country'    => $receiver_country,                 // Country ID
                'qrcode'              => $qrcode,
                'price_option'        => $priceopt, // Price Option
                'qty'                 => $qty,
                'devicetype'          => "web",
                'isDelivery'          => "1",
                'uuid'                => NULL, // City ID
                'lang'                => 'EN',
                'ip_address'          => Request::getClientIp(),
                'location'            => '',
                'transaction_date'    => $transaction_date,
                'delivery_identity_number'  => $recipient_id,
                'charity_id'          => '',
                
                // CURRENCY //

                'invoice_bussines_currency' => $main_business_currency,
                'invoice_bussines_currency_rate' => 1.0,
                'standard_currency' => $standard_currency,
                'standard_currency_rate' => $standard_currency_rate,
                'base_currency' => $base_currency,
                'base_currency_rate' => $base_currency_rate,
                
                // CURRENCY //
                
            );
           
    
            $dataCheckout = MCheckout::checkout_transaction($get);
            $data['Checkout'] = $dataCheckout;
            if($dataCheckout['status'] == 'success'){
                $transaction_id = $dataCheckout['transaction_id'];
                // Update Order 
                $DeliveryOrder = DeliveryOrder::find($DeliveryOrderID);
                $DeliveryOrder->transaction_id = $transaction_id;
                $DeliveryOrder->save();
                
                // Create status transaction as completed //
                
                $transaction_id = $dataCheckout['transaction_id'];
                $Transaction = Transaction::find($transaction_id);
                $Transaction->checkout_source = 3;
                $Transaction->modify_by = $username;
                $Transaction->modify_date = date("Y-m-d h:i:s");
                $Transaction->status = 'completed';
                $Transaction->save();
                
                // Insert scheduler to create DO and Invoice
                // GENERATE INVOICE
                $tempInv = MCheckout::generateInv($transaction_id, true,true);
                $tempDO = MCheckout::generateDO($transaction_id, true,true);
                LogisticTransaction::log_transaction($transaction_id);
                // GENERATE DO 
                    
            }else{
                throw new Exception("Failed to create transaction", '901');
            }
            /* CREATE DELIVERY ORDER */
    
        }catch (Exception $ex) {
            
            $is_error = true;
            $error_line = $ex->getLine();
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            $messageCode = '101';
            
            
            
        }finally{
            if($is_error){
                DB::rollback();
            }else{
                DB::commit();
            }
             $messageCode = '102';
        }
        
     
        // /* Return Response */
        $response = array("RespStatus"=> $RespStatus,"error"=> $is_error,"error_code" => $errorCode,"error_line" => $error_line,"message" => $message,"messageCode"=>$messageCode, "data"=> $data);
        return $response; 
        
        
    }
    
    
    public function getListManifestService() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        $shipper_id   = Input::get('shipper_id');
        $api_verification   = Input::get('api_verification');
        
        try {
            
            $orders = DB::table('jocom_international_logistic_manifest AS JILM')
                    ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JILM.country_id')
                    ->where('JILM.status', '=', 1)
                    ->select('JILM.*','JC.name')
                    ->orderBy('JILM.id',"DESC")
                    ->paginate(30);
            
            $data['order'] = $orders;
            
            
        }catch (Exception $ex) {
            return $ex->getMessage();
        }finally{
            /* Return Response */
            return $orders;
        }

        

    
    }
    
    public function getDownloadmanifestbyid($manifestNumber){
        
        
        $collectionData = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.*',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JT.delivery_name',
                    'JT.delivery_contact_no',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
                    'JILI.*' 
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->leftJoin('jocom_international_logistic_item AS JILI', 'JILI.jocom_international_logistic_id', '=', 'JIL.id')
                ->where('JIL.manifest_id', '=',$manifestNumber)->get();
//        
//        echo "<pre>";
//        print_r($collectionData);
//        echo "</pre>";
//      
        $currentRefNumber = 0;
        $indexCounter = 0;
        
        $DataList = array();
        
        foreach ($collectionData as $key => $value) {
        
            // New Item 
            if($value->reference_number !== $currentRefNumber){
            
                $currentRefNumber = $value->reference_number;
                $indexCounter++;
                
                $sublineArray = array(
                    "index" => $indexCounter,
                    "reference_number" => $value->reference_number,
                    "description" => $value->product_name,
                    "brand" => '',
                    "specification" => $value->product_label,
                    "model" => $value->Model,
                    "no_of_pcs" => $value->no_of_pcs,
                    "content_pcs" => $value->content_of_pcs,
                    "unit" => $value->quantity,
                    "weight" => $value->weight,
                    "value" => $value->value,
                    "recipient_name" => $value->delivery_name,
                    "recipient_id_number" => $value->recipient_id,
                    "delivery_address" => $value->FullAddress,
                    "contact_number" => $value->delivery_contact_no
                );
                
            }else{
                
                $sublineArray = array(
                    "index" => '',
                    "reference_number" => '',
                    "description" => $value->product_name,
                    "brand" => '',
                    "specification" => $value->product_label,
                    "model" => $value->Model,
                    "no_of_pcs" => $value->no_of_pcs,
                    "content_pcs" => $value->content_of_pcs,
                    "unit" => $value->quantity,
                    "weight" => $value->weight,
                    "value" => $value->value,
                    "recipient_name" => '',
                    "recipient_id_number" => '',
                    "delivery_address" => '',
                    "contact_number" => '',
                );
                
            }
            
            array_push($DataList, $sublineArray);
            
        }

        
        return Excel::create($manifestNumber, function($excel) use ($DataList) {
                    
            $excel->sheet('MANIFEST', function($sheet) use ($DataList)
            {
                $sheet->loadView('manifest.CHN', array('data' =>$DataList));
            });
            
        })->download('xls');
        

        
    }
    
    public function getStatisticmanifest(){
        try{
            
        
        $jan = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-01-01 00:00:00")->where("JIL.created_at","<=","2018-01-31 23:59:59")->count();
        $feb = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-02-01 00:00:00")->where("JIL.created_at","<=","2018-02-31 23:59:59")->count();
        $mac = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-03-01 00:00:00")->where("JIL.created_at","<=","2018-03-31 23:59:59")->count();
        $apr = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-04-01 00:00:00")->where("JIL.created_at","<=","2018-04-31 23:59:59")->count();
        $may = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-05-01 00:00:00")->where("JIL.created_at","<=","2018-05-31 23:59:59")->count();
        $jun = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-06-01 00:00:00")->where("JIL.created_at","<=","2018-06-31 23:59:59")->count();
        $jul = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-07-01 00:00:00")->where("JIL.created_at","<=","2018-07-31 23:59:59")->count();
        $aug = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-08-01 00:00:00")->where("JIL.created_at","<=","2018-08-31 23:59:59")->count();
        $sep = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-09-01 00:00:00")->where("JIL.created_at","<=","2018-09-31 23:59:59")->count();
        $oct = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-10-01 00:00:00")->where("JIL.created_at","<=","2018-10-31 23:59:59")->count();
        $nov = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-11-01 00:00:00")->where("JIL.created_at","<=","2018-11-31 23:59:59")->count();
        $dec = DB::table('jocom_international_logistic AS JIL')->where("JIL.created_at",">=","2018-12-01 00:00:00")->where("JIL.created_at","<=","2018-12-31 23:59:59")->count();
        
        
        return array(
            $jan,$feb,$mac,$apr,$may,$jun,$jul,$aug,$sep,$oct,$nov,$dec
            );
            
        }catch(Exception $ex){
            echo $ex->getMessage();
            
        }
        
        
    }
    
    
    
    /*
     * International Delivery 
     */
    
   
}
?>