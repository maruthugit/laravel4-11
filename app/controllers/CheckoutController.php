<?php

class CheckoutController extends BaseController
{
    /**
     * Default index for apps feed
     * @return [type] [description]
     */
    public function anyIndex()
    {
        
//         //added by sairam
// $rules = array(

//               'delivercontactno'   => 'required|numeric|'

//               );
// $message = array(
        
//              'delivercontactno.required'=>'The Mobile Number Must be required and numeric',    
        
//               );

//  $validator = Validator::make(Input::all(),$rules,$message);

//  if ($validator->fails())
//                      {
//                               return Redirect::back()->withInput()->withErrors($validator);
//                      }
   //till here by sairam  
   
//   echo '<pre>';
//   print_r(Input::all());
//   echo '</pre>';
   
        
        try{
            
        $mycash = "";
        $mycash = Input::get('devicetype');
        
        $transaction_date = "";
      
        if(date("Y-m-d H:i:s") >= Config::get('constants.NEW_INVOICE_SST_START_DATE')){
            /* SST TEMPLATE */
            $checkout_view    = (Input::get('devicetype') == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
        }  else{
            /* GST TEMPLATE */
            $checkout_view    = (Input::get('devicetype') == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
        } 
        
        $transaction_date = "";


        if (Input::has('transaction_date')) {
            $transaction_date = (Input::get('transaction_date') != "") ? Input::get('transaction_date')." 00:00:00" : '';
       
            if($transaction_date >= Config::get('constants.NEW_INVOICE_SST_START_DATE')){
                /* SST TEMPLATE */
                $checkout_view    = (Input::get('devicetype') == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
            }  else{
                /* GST TEMPLATE */
                $checkout_view    = (Input::get('devicetype') == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';
            } 
        }
       
          $checkout_view    = (Input::get('devicetype') == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';

        /* WEB CHECKOUT */
        // Check on user
//        try{

        // $ApiLog = new ApiLog ;
        // $ApiLog->api = 'CHECKOUT';
        // $ApiLog->data = json_encode(Input::all());
        // $ApiLog->save();
            
        if(Input::get('devicetype') == "web" || Input::get('devicetype') == "web_others" || Input::get('devicetype') == "web_mycash" || Input::get('devicetype') == "webboost" || Input::get('devicetype') == "webasean" || Input::get('devicetype') == "efstore"){
            
            $web_checkout = true;
            $email_customer = Input::get('email');
            // Check is it existing user 
            $CustomerInfo = Customer::where('email', $email_customer)->first();
            
            if(count($CustomerInfo) > 0){
                // Use existing account
                $_POST["user"] = $CustomerInfo->username;
                $_POST["pass"] = $CustomerInfo->password;
            }else{
                // Create Customer basic account
                $pass       = $_POST["firstname"].$_POST["lastname"];
                $_POST["user"] = Input::get('email');
                $_POST["pass"] = Hash::make($pass);
                
                $Customer = new Customer();
                $Customer->username = $_POST["user"];  
                $Customer->password = $_POST["pass"];  
                $Customer->email = Input::get('email');  
                $Customer->firstname = $_POST["firstname"];  
                $Customer->lastname = $_POST["lastname"];  
                $Customer->full_name = $_POST["firstname"]." ".$_POST["lastname"];  
                $Customer->mobile_no = Input::get('mobile_no');  
                $Customer->address1 = Input::get('deliveradd1');
                $Customer->address2 = Input::get('deliveradd2');
                $Customer->postcode = Input::get('deliverpostcode');  
                $Customer->city_id = Input::get('city');    
                $Customer->state_id = Input::get('state');     
                $Customer->country_id = Input::get('delivercountry');    
                $Customer->city = "";  
                $Customer->state = Input::get('state');     
                $Customer->country = ""; 
                $Customer->active_status = 1;    
                
                
                // $ApiLog = new ApiLog ;
                // $ApiLog->api = 'CHECKOUT_CREATE_CUSTOMER';
                // $ApiLog->data = json_encode($Customer);
                // $ApiLog->save();
                
                $Customer->save(); 

                // SHOULD SEND EMAIL WELCOME //
                // Send Out Email to the member's
                $firstname  = $Customer->firstname;
                $username   = $Customer->lastname;
                

                $subject    = "[tmGrocer]: Welcome new member!";
                $user = array(
                            'email' => $Customer->email,
                            'name'  => $Customer->firstname,
                            'username'  => $Customer->username,
                            
                );
                
                // Get Email Activation Setup
                //$Setup = Fees::find(1);
                $data = array(
                            'name'      => $Customer->firstname,
                            'username'  => $Customer->username,
                            'password'  => $pass,
                            "email_activation" => 0,
                            'environment'  => Config::get('constants.ENVIRONMENT')
                            // 'subject'   => $subject,
                );
                if($mycash != 'web_mycash'){
                    Mail::send('emails.welcome', $data, function($message) use ($user,$subject)
                    {
                        $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                        $message->to($user['email'], $user['name'])->subject($subject);
                    });
                }
                // SHOULD SEND EMAIL WELCOME //
            }
              
            
        }
//        } catch (Exception $ex) {
//            echo $ex->getMessage();
//        }
        //die();
       
        /* WEB CHECKOUT */
        if(Input::get('devicetype') === "wavpay" && Input::get('WavPayUID') && Input::get('WavPaySID')){
				$web_checkout = true;
				$uID = json_encode(["userID" => preg_replace('/[^\w- ]+/', '', (Input::get('WavPayUID') ? Input::get('WavPayUID') : ''))]);

				$CustomerInfo = Customer::where('full_name', Input::get('user'))->where('email', Input::get('email'))->where('ref_info', $uID)->first();
				if(count($CustomerInfo) > 0){
					// Use existing account
					$_POST["user"] = $CustomerInfo->username;
					$_POST["pass"] = $CustomerInfo->password;
				}
			}
        
        
        $tax_rate  = Fees::get_tax_percent();
        
         
        $CustomerInfo = Customer::where('username', $_POST["user"])->first();
        
        $delivery_country_id = trim(Input::get('delivercountry'));
        
        // $countryInfo = DB::table('jocom_countries AS JC')
        //     ->select('JC.id', 'JC.currency','JC.business_currency')
        //     ->where('JC.id', '=', $delivery_country_id)->first();
          
           
        $countryInfo = DB::table('jocom_countries AS JC')
            ->select('JC.id', 'JC.currency','JC.business_currency')
            ->where('JC.id', '=', $CustomerInfo->country_id)->first();
        
        // Jocom APP only Accept MYR
        if(Input::get('devicetype') == 'android' || Input::get('devicetype') == 'ios'){
            $_POST["main_bussines_currency"] = 'MYR';
        }else{
            $_POST["main_bussines_currency"] = $countryInfo->business_currency;
        }
        
        
        // from cart to checkout, only with $_POST["user"], no return from PayPal
        if ( ! isset($_POST["txn_id"]) &&  ! isset($_POST["txn_type"]) && isset($_POST["user"])) {
            
            /* CURRENCY SET */
            $main_business_currency = isset($_POST["main_bussines_currency"]) ? $_POST["main_bussines_currency"] : 'MYR' ;
                
            switch ($main_business_currency) {

                case 'MYR':

                    $main_business_currency = 'MYR';
                    $base_currency = 'MYR';
                    $standard_currency = 'USD';
                    $foreign_country_currency = 'MYR';

                    $main_business_currency_data = ExchangeRate::getExchangeRate($base_currency , $main_business_currency);
                    $base_currency_rate_data = ExchangeRate::getExchangeRate($base_currency , $base_currency);
                    $standard_currency_rate_data = ExchangeRate::getExchangeRate($base_currency , $standard_currency);
                    $foreign_country_rate_data = ExchangeRate::getExchangeRate($base_currency , $foreign_country_currency);
                    
                    $base_currency_rate = $base_currency_rate_data->amount_to;
                    $standard_currency_rate = $standard_currency_rate_data->amount_to;
                    $foreign_country_rate = $foreign_country_rate_data->amount_to;
                    $main_business_currency_rate = $main_business_currency_data->amount_to;

                    break;
                
                case 'RMB':

                    $main_business_currency = 'RMB';
                    $base_currency = 'MYR';
                    $standard_currency = 'USD';

                    $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $base_currency);
                    $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $standard_currency);
                    $base_currency_rate = $base_currency_rate_data->amount_to;
                    $standard_currency_rate = $standard_currency_rate_data->amount_to;
                    
                case 'USD':

                    $main_business_currency = 'USD';
                    $base_currency = 'MYR';
                    $standard_currency = 'USD';
                    $foreign_country_currency = 'RMB';
                    
                    $main_business_currency_data = ExchangeRate::getExchangeRate($main_business_currency , $main_business_currency);
                    $base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $base_currency);
                    $standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $standard_currency);
                    $foreign_country_rate_data = ExchangeRate::getExchangeRate($main_business_currency , $foreign_country_currency);
                    
                    $base_currency_rate = $base_currency_rate_data->amount_to;
                    $standard_currency_rate = $standard_currency_rate_data->amount_to;
                    $foreign_country_rate = $foreign_country_rate_data->amount_to;
                    $main_business_currency_rate = $main_business_currency_data->amount_to;


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
            
            /* CURRENCY SET */
            
           
            $get = array(
                'user'                => trim($_POST["user"]),             // Buyer Username
                'pass'                => trim($_POST["pass"]),             // Buyer Password
                'delivery_name'       => trim(Input::get('delivername')),      // delivery name
                'delivery_contact_no' => trim(Input::get('delivercontactno')), // delivery contact no
                'special_msg'         => trim(Input::get('specialmsg')),       // special message
                'delivery_addr_1'     => trim(Input::get('deliveradd1')),
                'delivery_addr_2'     => trim(Input::get('deliveradd2')),
                'delivery_postcode'   => trim(Input::get('deliverpostcode')),
                'delivery_city'       => Input::has('city') ? trim(Input::get('city')) : '', // City ID
                'delivery_state'      => trim(Input::get('state')),                          // State ID
                'delivery_country'    => trim(Input::get('delivercountry')),                 // Country ID
                'delivery_charges'    => trim(Input::get('delivery_charges')),  
                'qrcode'              => Input::get('qrcode'),
                'price_option'        => Input::get('priceopt'), // Price Option
                'qty'                 => Input::get('qty'),
                'devicetype'          => Input::get('devicetype'),
                'uuid'                => Input::has('uuid') ? trim(Input::get('uuid')) : NULL, // City ID

                'lang'                => Input::has('lang') ? Input::get('lang') : 'EN',
                'ip_address'          => Input::has('ip') ? Input::get('ip') : Request::getClientIp(),
                'location'            => Input::has('location') ? Input::get('location') : '',
                'isPopbox'            => Input::has('isPopbox') ? Input::get('isPopbox') : '',
                'deliverPopbox'       => Input::has('deliverPopbox') ? Input::get('deliverPopbox') : '',
                'popaddresstext'      => Input::has('popaddresstext') ? Input::get('popaddresstext') : '',
                'transaction_date'    => $transaction_date,
                'is_self_collect'     => Input::has('is_self_collect') ? Input::get('is_self_collect') : 0,
                'create_by_user'     => Session::get('username') != '' ? Session::get('username') : '',

                'charity_id'          => Input::has('charity_id') ? Input::get('charity_id') : '',
                'external_ref_number' => Input::has('external_ref_number') ? Input::get('external_ref_number') : '',
                'selected_invoice_date' => Input::has('selected_invoice_date') ? Input::get('selected_invoice_date') : null,
                'invoice_to_address' => Input::has('invoice_to_address') ? Input::get('invoice_to_address') : 1,
                
                // CURRENCY //

                'invoice_bussines_currency' => $main_business_currency,
                'invoice_bussines_currency_rate' => $main_business_currency_rate,
                'standard_currency' => $standard_currency,
                'standard_currency_rate' => $standard_currency_rate,
                'base_currency' => $base_currency,
                'base_currency_rate' => $base_currency_rate,
                'foreign_country_currency' => $foreign_country_currency,
                'foreign_country_currency_rate' => $foreign_country_rate,
                
                // CURRENCY //
            );
            
              
            // $ApiLog = new ApiLog ;
            // $ApiLog->api = 'CHECKOUT_DATA_REQUEST';
            // $ApiLog->data = json_encode($get);
            // $ApiLog->save();

            /* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */
            if( (Input::get('transfer_order_id') != 0) && (Input::get('transfer_order_id_type') != 0) ){
                
                switch (Input::get('transfer_order_id_type')) {
                    case 1: // ELEVENSTREET 
                        $elevenStreetDeliveryCharges = 0;  
                        $OrderDataDetails = ElevenStreetOrderDetails::getByOrderID(Input::get('transfer_order_id'));
                        foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                            $APIData = json_decode($valueDetails->api_result_return, true);
                            $elevenStreetDeliveryCharges = $elevenStreetDeliveryCharges + ($APIData['dlvCst'] * (100/(100 + $tax_rate)));
                        }
                        
                        $get['elevenstreetDeliveryCharges'] = $elevenStreetDeliveryCharges;

                        break;
                    case 2: // LAZADA
                        
                        $lazadaDeliveryCharges = 0;  
                        $OrderDataDetails = LazadaOrderDetails::getByOrderID(Input::get('transfer_order_id'));
                        
                      
                        
                        foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                            $APIData = json_decode($valueDetails->order_items_details, true);
                            $lazadaDeliveryCharges = $lazadaDeliveryCharges + ($APIData['ShippingAmount'] * (100/(100 + $tax_rate)));
                        }

                        $get['lazadaDeliveryCharges'] = $lazadaDeliveryCharges;

                        break;
                    case 3: // Qoo10
                        
                        $qoo10DeliveryCharges = 0;  
                        $OrderDataDetails = QootenOrderDetails::getByOrderID(Input::get('transfer_order_id'));
                        
                        // Check Account
                        $QootenOrder = QootenOrder::where("id",Input::get('transfer_order_id'))->first();
                        // Check Account
                        if($QootenOrder->from_account == 2){
                                $ExchangeRate = ExchangeRate::getExchangeRate('SGD', 'MYR');
                                $ExchangeRateAmount = $ExchangeRate->amount_to;
                        }else{
                                $ExchangeRate = ExchangeRate::getExchangeRate('MYR', 'MYR');
                                $ExchangeRateAmount = $ExchangeRate->amount_to;
                        }
                          
                        foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                            $APIData = json_decode($valueDetails->api_result_return, true);
                            // $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($APIData['ShippingRate'] * (100/106));
                        }
                        $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($APIData['ShippingRate'] * (100/(100 + $tax_rate)));
                        
                        //$get['qoo10DeliveryCharges'] = $qoo10DeliveryCharges  * $ExchangeRateAmount;
                        $get['qoo10DeliveryCharges'] = $qoo10DeliveryCharges ;

                        break;
                    case 4: // Shopee
                        
                        $shopeeDeliveryCharges = 0;  
                        $OrderDataDetails = ShopeeOrderDetails::getByOrderID(Input::get('transfer_order_id'));
                     
                        foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                            $APIData = json_decode($valueDetails->api_result_return, true);
                            // $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($APIData['ShippingRate'] * (100/106));
                        }
                        $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($APIData['estimated_shipping_fee'] * (100/(100 + $tax_rate)));
                        
                        $get['shopeeDeliveryCharges'] = $shopeeDeliveryCharges;

                        break;
                    case 5: // PGMall
                        $pgmallDeliveryCharges = 0;  
                        $OrderDataDetails = PGMallOrderDetails::getByOrderID(Input::get('transfer_order_id'));
                     
                        foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                            $APIData = json_decode($valueDetails->api_result_return, true);
                        }
                        $pgmallDeliveryCharges = $pgmallDeliveryCharges + ($APIData['shipping_amount'] * (100/(100 + $tax_rate)));
                        
                        $get['pgmallDeliveryCharges'] = $pgmallDeliveryCharges;

                        break;
                    default:
                        break;
                }
                
                
            }
            /* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */            

            
            Session::put('lang', $get["lang"]);
            Session::put('devicetype', $get["devicetype"]);

            $signal_check = base64_encode(serialize($get));

            // **********************************
            // to remove later, for testing only
            //Session::put('checkout_signal_check', $signal_check);
            // echo '<pre>';
            // print_r($get);
            // echo '</pre>';
           
            $data = array();
             /*
            // if with posting and transaction ID stored in session
            if (Session::get('checkout_signal_check') && Session::get('checkout_transaction_id')) {
                if (Session::get('checkout_signal_check') == $signal_check) {
                    $data['transaction_id'] = Session::get('checkout_transaction_id');
                    $data['status']         = 'success';
                    $data['message']        = 'valid';
                } else {
                    Session::forget('checkout_signal_check');
                }
            }
            */
            // if no transaction ID
            if ( (!isset($data['transaction_id'])) ) {
                //$data = $this->checkout_m->checkout_transaction($get);
                $data = MCheckout::checkout_transaction($get);
                // print_r($data);
                //  die('End-4');
                 
                if( (Input::get('transfer_order_id') != 0) && (Input::get('transfer_order_id_type') != 0) ){
                
                    switch (Input::get('transfer_order_id_type')) {
                        case 1: // ELEVENSTREET 
                            // Update Status 11Street Order 
                            $ElevenStreetOrder = ElevenStreetOrder::find(Input::get('transfer_order_id'));
                            $ElevenStreetOrder->status = 2;
                            $ElevenStreetOrder->transaction_id = $data["transaction_id"];
                            $ElevenStreetOrder->save();
                            break;
                        case 2: // LAZADA

                            // Update Status 11Street Order 
                            $LazadaOrder = LazadaOrder::find(Input::get('transfer_order_id'));
                            $LazadaOrder->status = 2;
                            $LazadaOrder->transaction_id = $data["transaction_id"];
                            $LazadaOrder->save();

                            break;
                        case 3: // Qoo10

                            // Update Status Qoo10 Order 
                            $Qoo10Order = QootenOrder::find(Input::get('transfer_order_id'));
                            $Qoo10Order->status = 2;
                            $Qoo10Order->transaction_id = $data["transaction_id"];
                            $Qoo10Order->save();

                            break;
                        case 4: // Shopee

                            // Update Status ShopeeOrder Order 
                            $ShopeeOrder = ShopeeOrder::find(Input::get('transfer_order_id'));
                            $ShopeeOrder->status = 2;
                            $ShopeeOrder->transaction_id = $data["transaction_id"];
                            $ShopeeOrder->save();

                            break;
                        case 5: // PGMall  

                            // Update Status PGMallOrder Order 
                            $PGMallOrder = PGMallOrder::find(Input::get('transfer_order_id'));
                            $PGMallOrder->status = 2;
                            $PGMallOrder->transaction_id = $data["transaction_id"];
                            $PGMallOrder->save();

                            break;
                        default:
                            break;
                }

                }
                
                if($get['isPopbox'] == 1){
                    $popBoxReturn = PopboxController::savePopBox($data["transaction_id"],$get['deliverPopbox'],$get['popaddresstext']);
                    
                }
                $data['popbox'] = $popBoxReturn;

            }
            
            // $ApiLog = new ApiLog ;
            // $ApiLog->api = 'CHECKOUT_RESPONSE';
            // $ApiLog->data = json_encode($data);
            // $ApiLog->save();
            

            // succesfully checkout
            if (isset($data['status']) && $data['status'] == 'success') {
                Session::put('checkout_signal_check', $signal_check);
                Session::put('checkout_transaction_id', $data["transaction_id"]);
                Session::put('lang', $data["lang"]);
                Session::put('devicetype', $data["devicetype"]);
                Session::put('android_orderid', $data["transaction_id"]);
                $cashbackflag = $data["cashbackflag"];
                $cb_productid = $data["cashbacktext"];

                $tempdata = MCheckout::get_checkout_info($data["transaction_id"]);
                $data     = array_merge($data, $tempdata);
                
                if($web_checkout && Input::get('devicetype') == "web"){
                    $Transaction = Transaction::find($data["transaction_id"]);
                        $Transaction->checkout_source = 2; // Web Checkout
                        $Transaction->save();
                }
                
                if($web_checkout && Input::get('devicetype') == "web_others"){
                    $Transaction = Transaction::find($data["transaction_id"]);
                    $Transaction->checkout_source = 3; // Web Other Platforms Checkout //ie., Ecommunity
                    $Transaction->save();
                }
                
                if($web_checkout && Input::get('devicetype') == "web_mycash"){
                    $Transaction = Transaction::find($data["transaction_id"]);
                    $Transaction->checkout_source = 4; // Web Other Platforms Checkout //ie., MyCashOnline etc
                    $Transaction->save();
                }
                
                if($web_checkout && Input::get('devicetype') == "webboost"){
                    $Transaction = Transaction::find($data["transaction_id"]);
                        $Transaction->checkout_source = 5; // Web Checkout Boost Payment
                        $Transaction->save();
                }
                
                if($web_checkout && Input::get('devicetype') == "wavpay"){
						$Transaction = Transaction::find($data["transaction_id"]);
						$Transaction->checkout_source = 7; // Web Other Platforms Checkout //ie., Wavpay
						$Transaction->save();
				}
                
                if($web_checkout && Input::get('devicetype') == "webasean"){
                    $Transaction = Transaction::find($data["transaction_id"]);
                        $Transaction->checkout_source = 6; // Web Checkout Boost Payment
                        $Transaction->save();
                }
                
                if($web_checkout && Input::get('devicetype') == "efstore"){
                    $Transaction = Transaction::find($data["transaction_id"]);
                        $Transaction->checkout_source = 8; // Web Checkout efstore
                        $Transaction->save();
                }

                if (isset($data['trans_query'])) {
                    $buyerId        = $data['trans_query']->buyer_id;
                    $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                    $data['points'] = $points;
                    $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                    
                    //Start JCashback 
                    if(isset($cashbackflag) && $cashbackflag == 1) {

                            $trans_cashback = DB::table('jocom_transaction_jcashback')
                                 ->where('user_id','=',$buyerId)
                                 ->where('qrcode','=',$cb_productid)
                                 ->where('status','=',1)
                                 ->where('jcash_point_used','=',0)
                                 ->orderBy('id','ASC')
                                 ->first();

                            if(count($trans_cashback) > 0){

                                  $cback_array = array('id' => $trans_cashback->id,    
                                                       'sku' => $trans_cashback->sku,
                                                       'user_id' => $trans_cashback->user_id,
                                                       'product_name' => $trans_cashback->product_name,
                                                       'jcash_point' => $trans_cashback->jcash_point
                                                 );

                                 $tempdatacashback['trans_cashback'] = $cback_array;

                                 $data     = array_merge($data, $tempdatacashback);

                            }

                           


                    }
                    //End JCashback 
                    
                    if($web_checkout){
                       
                        return $data;
                    }else{

                    return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                    }
                    
                } else {
                    if ($data['message'] == '') {
                        $data['message'] = '101';
                    }
                    // $data['message'] = 'Invalid request. Please try again.';

                    /* WEB CHECKOUT CHANGES */
                    if($web_checkout){
                        return $data;
                    }else{
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('dataCollection',$data);
                }
                    /* WEB CHECKOUT CHANGES */
                    
                    
                }
            } else {
                Session::forget('checkout_signal_check');
                Session::forget('checkout_transaction_id');

                   if($web_checkout){
                        return $data;
                    }else{
                       if ($data['message'] == '') {
                           $data['message'] = '101';
                       }

                      // $data['message'] = 'Invalid request. Please try again.';
                      return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('kkwprod', $data['kkwprod'])
                        ->with('userinfo', $data['userinfo'])->with('dataCollection',$data);;
                    }

                
            }
        }
        // return post by PayPal
        else if ( ! empty($_POST)) {
            $transactionType = MCheckout::transaction_complete($_POST);

            if (Input::has('lang')) {
                Session::put('lang', Input::get('lang'));
            }

            switch ($transactionType) {
                case 'point':
                    $data['message'] = '006';
                    break;
                default:
                    $data['message'] = '001';
                    break;
            }

            $transaction = Transaction::find($_POST["invoice"]);
            $user        = Customer::find($transaction->buyer_id);
            $username    = $user->username;
            $bcard       = BcardM::where('username', '=', $username)->first();
            $bcardStatus = PointModule::getStatus('bcard_earn');

            $data['payment'] = 'JCSUCCESS successfully received';
            // $data['message'] = 'Your order has been successfully received.<br />Thank you for shopping at JOCOM.';
            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')
                ->with('message', $data['message'])
                ->with('payment', $data['payment'])
                ->with('id', $_POST["invoice"])
                ->with('bcardStatus', $bcardStatus)
                ->with('bcardNumber', object_get($bcard, 'bcard'))
                ->with('buyerId', $transaction->buyer_id);
        }
        // PayPal android only able to return id via url
        else if (Input::has('tran_id')) {
            if (empty($_POST)) {
                $transactionType = MCheckout::transaction_complete_android(Input::get('tran_id'));
            }

            if (Input::has('lang')) {
                Session::put('lang', Input::get('lang'));
            }

            $transaction = Transaction::find(Input::get('tran_id'));
            $user        = Customer::find($transaction->buyer_id);
            $username    = $user->username;
            $bcard       = BcardM::where('username', '=', $username)->first();

            switch ($transactionType) {
                case 'point':
                    $data['message'] = '006';
                    break;
                default:
                    $data['message'] = '001';
                    break;
            }

            $transaction = Transaction::find(Input::get('tran_id'));
            $bcardStatus = PointModule::getStatus('bcard_earn');

            $data['payment'] = 'JCSUCCESS successfully received';
            // $data['message'] = 'Your order has been successfully received.<br />Thank you for shopping at JOCOM.<br /><br />Order ID-: '.Input::get('tran_id');
            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')
                ->with('message', $data['message'])
                ->with('payment', $data['payment'])
                ->with('id', Input::get('tran_id'))
                ->with('bcardStatus', $bcardStatus)
                ->with('bcardNumber', object_get($bcard, 'bcard'))
                ->with('buyerId', $transaction->buyer_id);
        }
        // Error Page
        else {
            if (Input::has('lang')) {
                Session::put('lang', Input::get('lang'));
            }

            $data['message'] = '101';
            // $data['message'] = 'Invalid request. Please try again.';
            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
        }
        
        
        }catch(Exception $e) {
                // echo 'Message: ' .$e->getMessage();
                $ApiLog = new ApiLog ;
                $ApiLog->api = 'CHECKOUT_ERROR';
                $ApiLog->data = $e->getMessage() .'-'. $e->getLine();
                $ApiLog->save();
            }

    }

    public function anyPayment()
    {

        // require_once 'molpay/InpageMolpay.php';

        // $molpay = new MOLPay\distribution\InpageMolpay();

        // $html_code = $molpay->setMerchantID('test5620')         //Set the Merchant ID
        //         ->setOrderID('DEMO1045')                        //Set the Order ID
        //         ->setAmount(1.10)                               //Set the Transaction Amount
        //         ->setBuyerDetails(array(
        //             'bill_name' => 'MOLPay demo',
        //             'bill_email' => 'demo@molpay.com',
        //             'bill_mobile' => '0355218438',
        //             'bill_description' => 'testing by MOLPay',
        //         ))                                              //Define the buyer information
        //         ->setCountry('MY')                              //Set the country code
        //         ->setReturnURL('processing.php')                //Define the URL where shall we proceed after returning from MOLPay
        //         ->setVcode('0d72ceec9ee3848f4721697f5dca166e')  //Define the verification code
        //         ->setCurrency('MYR')                            //Set the currency used
        //         ->setLanguageCode('en')                         //Set the language code
        //         // ->setTemplate(true)                             //Set to true for Full Template Output
        //         ->trigger();                                    //Trigger the in-page payment

        // echo $html_code;

        $return_url = asset('/').'checkout';
        $cancel_url = asset('/').'checkout/cancelled';
        $notify_url = asset('/').'checkout/notify';

        if (isset($_POST)) {
            $str = array();
            foreach ($_POST as $k => $v) {
                $str[] = $k."=".urlencode($v);
            }

            // $cancel_url .= '?tran_id=' . $_POST['invoice'];
            // $return_url .= '?tran_id=' . $_POST['invoice'];
            $return_url .= '?tran_id='.Input::get('invoice');
            $cancel_url .= '?tran_id='.Input::get('invoice');

            $str[] = "return=".urlencode(stripslashes($return_url))."&";
            $str[] = "cancel_return=".urlencode(stripslashes($cancel_url))."&amp;";
            $str[] = "notify_url=".urlencode($notify_url);

            $fieldStr = "?".implode("&", $str);

            // PayPal Sandbox
            header('location:https://www.sandbox.paypal.com/cgi-bin/webscr'.$fieldStr);
            //header('location:https://www.paypal.com/cgi-bin/webscr' . $fieldStr);
            //
            exit();

        } else {
            echo 'Invalid request. Please try again.';
        }
    }

    public function anyNotify()
    {
        if ( ! empty($_POST)) {
            $temp_xml = MCheckout::transaction_complete($_POST);
            echo 'Transaction completed.';
        }
    }

    public function anyCancelled()
    {
        if (isset($_POST["tran_id"])) {
            $temp_xml = MCheckout::cancelled_transaction($_POST["tran_id"]);
        }

        $data['message'] = '002';
        $data['payment'] = 'JCFAIL failed';
        // $data['message'] = 'Apologies for cancelling of your order.<br />Thank you for visiting.';
        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);

    }

    public function anyCouponcode()
    {
        
try{
        $boost = 0;
        $lscheck = 0;
        $lscheckinf = 0;
        $razerpay = 0; 
        $tngpay = 0;
        $couponid = '';
        $wonda = 0;
        $restrict_jpoint=1;
        $data                   = MCheckout::get_checkout_info($_POST["transaction_id"]);
        $data['transaction_id'] = $_POST["transaction_id"];
        $data['coupon_msg'] = Session::get('coupon_msg');
        $alert=array();
        if (isset($_POST["transaction_id"])) {
            $transactionID = $_POST["transaction_id"];
                        		
            $Transaction = Transaction::find($transactionID);		
            $transactionDate = $Transaction->transaction_date;		
          		
            if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){		
            /* INCLUSIVE TEMPLATE */		
                $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';		
            }  else{		
                /* EXCLUSIVE TEMPLATE */		
                $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout' : '.checkout_view';		
            }		
            
            Session::put('lang', $_POST['lang']);
            Session::put('devicetype', $_POST['devicetype']);
            //voucher code restriction module  start
                    if($data['trans_detail_query']!=""){
            $data['boost'] = $boost;
            $data['razerpay'] = $razerpay;
            $data['tngpay'] = $tngpay;
            $product_details=$data['trans_detail_query'];
            
            foreach($product_details as $value){
                $vouchercode=DB::table('jocom_products')->select('name')->where('id','=',$value->product_id)->where('is_voucher_code','=',"1")->first();
                if(!empty($vouchercode)){
                $alert[]=$vouchercode->name;
                }
            }
            if(!empty($alert)){
               
                $hint="This Products Not allow using Voucher Code:<br>";
               foreach($alert as $mess){
                   $hint.=$mess."<br>";
               }
               Session::put('coupon_msg', "{$hint}");
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                
                if($_POST['devicetype'] == "web"){
                            // $data['coupon_msg'] = "To use bank card promotion with ".$products->name.",the minimum purchase amount is RM150";
                            $data['coupon_msg'] =  $hint;
                             return $data;
                        }else{
                         return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                //  return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
            }
           }
            //voucher code restriction module end
            //all voucher restrict
            $ProductCheck=TDetails::where('transaction_id', '=', $data['transaction_id'])->where('product_id','=','54361')->first();
            if($ProductCheck){
            $data['boost'] = $boost;
            $data['razerpay'] = $razerpay;
            $data['tngpay'] = $tngpay;
            
            //check coupon code
                    $coupon_c=strtoupper($_POST["coupon"]);
                    if($coupon_c!="JCMPMP100"){
                Session::put('coupon_msg', "Oops! You will not be able to use this voucher with jocom preferred member package.");
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                 if($_POST['devicetype'] == "web"){
                            $data['coupon_msg'] = "Oops! You will not be able to use this voucher with jocom preferred member package.";
                             return $data;
                        }else{
                return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                    }
            }
            //all voucher restrict end
            //temp coupon restrict
            $tempcheck=TDetails::where('transaction_id', '=', $data['transaction_id'])->where('product_id','=','54196')->first();
            
            if($tempcheck){
                $data['boost'] = $boost;
            $data['razerpay'] = $razerpay;
            $data['tngpay'] = $tngpay;
                $tdetails=TDetails::where('transaction_id', '=', $data['transaction_id'])->get();
           
            //check min purchase requirement
            $products=DB::table('jocom_products')->select('name')->where('id','=','54196')->first();
                    $sum_price    = TDetails::where('transaction_id', '=', $data['transaction_id'])->sum('total');
                    $sum_purchase = number_format($sum_price, 2);
                    $CouponAmountCheck = Coupon::where("coupon_code",$_POST["coupon"])->first();
                    $FinalCheckAmount=$sum_purchase-$CouponAmountCheck->amount;
                    if($FinalCheckAmount<150){
                Session::put('coupon_msg', "To use voucher code with ".$products->name.",the minimum purchase amount is RM150");
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                        if($_POST['devicetype'] == "web"){
                            $data['coupon_msg'] = "To use bank card promotion with ".$products->name.",the minimum purchase amount is RM150";
                             return $data;
                        }else{
                        return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                    }
            }
            //end coupon restrict
            
            
            // if($_POST["coupon"] == 'JOMSMO30') {
                
            //     $lscheck = Transaction::Checklivestreamexists($_POST["transaction_id"]);
            // }
            
            // if($lscheck == 0){
            //     $wonda = Transaction::Checkwondaproductexists($_POST["transaction_id"],$_POST["coupon"]);
            //     // echo $wonda;
            //     if($wonda == 7){
            //       $lscheck = 1;  
            //     }
                
            // }
            
            // if($lscheck == 0){
            //         $lscheck = Transaction::Checkproductexists($_POST["transaction_id"]);
            //          if($lscheck == 1){
            //              if(strtoupper($_POST["coupon"]) == 'JCMMCM800' || strtoupper($_POST["coupon"]) == 'JCMMCM500' || strtoupper($_POST["coupon"]) == 'JCMMCM300'){
            //                   $lscheck = 0;
            //              }
            //          }
            //     }
             if($lscheck == 0){
                    $lscheck = Transaction::Checkjcmeleven11($_POST["transaction_id"]);
                }
                // $yes='';
            
              if(strtoupper(trim($_POST["coupon"])) == 'SHARON50') {
                  
                  if($lscheck == 1){
                        $lscheck = 0;
                        // echo '3';
                    }
              }
              
              if($lscheck == 1){
                    $lscheck = Transaction::Checkcoupon(strtoupper(trim($_POST["coupon"])));
                    // echo $lscheck;
                    if($lscheck == 1){
                        $lscheck = 0;
                    }
                }
              
            
            
            // if($lscheck == 0){    
            // $lscheckinf = Transaction::Checkbinfiniteexists($_POST["transaction_id"]);
            // if($lscheckinf == 1){
            // if(strtoupper($_POST["coupon"]) == 'BINFJCMKUT' || strtoupper($_POST["coupon"]) == 'BINFJCMEWS' || strtoupper($_POST["coupon"]) == 'BINFJCMTRW' || strtoupper($_POST["coupon"]) == 'BINFJCMCSX' || strtoupper($_POST["coupon"]) == 'BINFJCMGXZ' || strtoupper($_POST["coupon"]) == 'BINFJCMMBV' || strtoupper($_POST["coupon"]) == 'BINFJCMFDZ') {
                    
            //          $yes='';
            //         if($lscheck == 1){
            //             $lscheck = 0;
            //         }
            //         else
            //         {
            //             $lscheck = 1;
            //         }
                    
            //     }
            //     else{
            //       $lscheck = 1; 
            //     }
            // }
            
            // }   
            
            // if($lscheck == 0){
            //      $lscheck = Transaction::Checkbinfiniteexists($_POST["transaction_id"]);
            //     // $lscheck = Transaction::Checkboostproductexists($_POST["transaction_id"],$_POST["coupon"]);
            // }
            
            // if($lscheck == 0){
            //     if($_POST["coupon"] == 'JOMSMO30') {
    
            //         $lscheck = Transaction::Checkpurchasevalidity($_POST["transaction_id"],'JOMSMO30');
            //     }
            //     if($_POST["coupon"] == 'JOMSMO20') {
    
            //         $lscheck = Transaction::Checkpurchasevalidity($_POST["transaction_id"],'JOMSMO20');
    
    
            //     }
                
            // }
            
            // if($_POST["coupon"] == 'JCMSUPERDEAL5') {
            //   $lscheck = 0; 
            // }
            
            if($lscheck == 0){
                 
                if(strtoupper($_POST["coupon"]) == 'NEW50') {
    
                    $lscheck = Transaction::Checkuserexists($_POST["transaction_id"]);
                }
                
            }
            
            if($lscheck == 0){
                 
                if(strtoupper($_POST["coupon"]) == 'NEW20') {
    
                    $lscheck = Transaction::Checkuserexists($_POST["transaction_id"]);
                }
                
            }
            
            if($lscheck == 0){
                 
                if(strtoupper($_POST["coupon"]) == 'NEWSUB5') {
    
                    $lscheck = Transaction::Checkuserexists($_POST["transaction_id"]);
                }
                
            }
            
            if($lscheck == 0){
                 
                if(strtoupper($_POST["coupon"]) == 'NEW50FEB') {
    
                    $lscheck = Transaction::Checkuserexists($_POST["transaction_id"]);
                }
                
            }
            
            
             
            // echo $lscheck;
             if($lscheck == 0){
                 if(strtoupper($_POST["coupon"]) == 'JCM16OFF') {
    
                  $lscheck = Transaction::Checkuserblockcontact($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper(trim($_POST["coupon"])) == 'JCM30OFF') {
    
                  $lscheck = Transaction::Checkuserblockcontact($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper(trim($_POST["coupon"])) == 'JCM30OFF') {
    
                  $lscheck = Transaction::Checkuserblockmobile($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper(trim($_POST["coupon"])) == 'JCM16OFF') {
    
                  $lscheck = Transaction::Checkuserblockmobile($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper(trim($_POST["coupon"])) == 'JCM30OFF') {
    
                  $lscheck = Transaction::Checkuserblockemail($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper(trim($_POST["coupon"])) == 'JCM16OFF') {
    
                  $lscheck = Transaction::Checkuserblockemail($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper(trim($_POST["coupon"])) == 'JCM30OFF') {
    
                  $lscheck = Transaction::Checkuserblockaddress($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper(trim($_POST["coupon"])) == 'JCM16OFF') {
    
                  $lscheck = Transaction::Checkuserblockaddress($_POST["transaction_id"]);
                //   echo $lscheck;
                }
             }
             
             
            // if($lscheck == 0){
            //      if(strtoupper($_POST["coupon"]) == 'JCM30OFFTEST') {
    
            //       $lscheck = Transaction::Checkuserblockcontact($_POST["transaction_id"]);
            //     //   echo $lscheck;
            //     }
            //  }
             
             
             
             if($lscheck == 0){
                 if(strtoupper($_POST["coupon"]) == 'JOMNL') {
    
                   $lscheck = Transaction::Checkuserblockcontact($_POST["transaction_id"]);
                }
             }
             
               //echo $lscheck;
             if($lscheck == 0){
                 if(strtoupper($_POST["coupon"]) == 'JOMSMO20') {
    
                //   $lscheck = Transaction::Checkuserblockcontactjom20($_POST["transaction_id"]);
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper($_POST["coupon"]) == 'JOMSMO30') {
    
                   $lscheck = Transaction::Checkuserblock($_POST["transaction_id"]);
                }
             }
            //   echo $lscheck;
            //  echo $lscheck.$_POST["transaction_id"];
             if($lscheck == 0){
                 if(strtoupper($_POST["coupon"]) == 'NEW50') {
    
                  $lscheck = Transaction::Checkuserblockcontactmoretimes($_POST["transaction_id"],'NEW50');
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper($_POST["coupon"]) == 'NEW20') {
    
                  $lscheck = Transaction::Checkuserblockcontactmoretimes($_POST["transaction_id"],'NEW20');
                //   echo $lscheck;
                }
             }
             
             if($lscheck == 0){
                 if(strtoupper($_POST["coupon"]) == 'NEW50FEB') {
    
                  $lscheck = Transaction::Checkuserblockcontactmoretimes($_POST["transaction_id"],'NEW50FEB');
                //   echo $lscheck;
                }
             }
             
            //   if($lscheck == 0){
            //      if(strtoupper($_POST["coupon"]) == 'EU30FEB') {
    
            //       $lscheck = Transaction::Checkuserblockcontactmoretimes($_POST["transaction_id"],'EU30FEB');
            //     //   echo $lscheck;
            //     }
            //  }
             
            //   if($lscheck == 0){
            //      if(strtoupper($_POST["coupon"]) == 'EU20FEB') {
    
            //       $lscheck = Transaction::Checkuserblockcontactmoretimes($_POST["transaction_id"],'EU20FEB');
            //     //   echo $lscheck;
            //     }
            //  }
            
                  ////quick coupon 
             $RegionChecks=Coupon::where("coupon_code",$_POST["coupon"])->first();
             if($RegionChecks->region!=null){
                 $regionchecker=0;
                 $state_id=$Transaction->delivery_state_id;
                 $statecheck=array('458004','458013','458015');
                 if(in_array($state_id,$statecheck)){
                    $regionchecker=1; 
                 }else{
                    $regionchecker=0;  
                 }
                 if($regionchecker==0){
                Session::put('coupon_msg','Invalid request.(Delivery region not match with coupon region.)');
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                    
                        if($_POST['devicetype'] == "web"){
                            $data['coupon_msg'] = 'Invalid coupon request.(Delivery region not match with coupon region.)';
                             return $data;
                        }else{
                        return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                    }
             }
            
            ///
                    /////coupon region check
               $RegionCheck=Coupon::where("coupon_code",$_POST["coupon"])->first();
               if($RegionCheck->region!=null){
                   $Iserror=false;
                   $buyer_zone=explode(',',$RegionCheck->region);
                   $transactionDetails = TDetails::where('transaction_id', '=',$data['transaction_id'])->get();
                    foreach($transactionDetails as $values){
                         $dl_row = DB::table('jocom_product_delivery')
                                  ->select('*')
                                  ->where('product_id', '=', $values->product_id)
                                  ->whereIn('zone_id', $buyer_zone)
                                  ->first();
                    if ($dl_row == null||$dl_row=="") { 
                       $Iserror = true;
                       $productname.='</br> '.$values->product_name.'';
                    }
                    }
                    if($Iserror==true){
                Session::put('coupon_msg','Invalid request.(Selected product region not match with coupon region.)'.$productname);
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                    
                        if($_POST['devicetype'] == "web"){
                            $data['coupon_msg'] = 'Invalid coupon request.(Selected product region not match with coupon region.)'.$productname;
                             return $data;
                        }else{
                        return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                    }
            
               }
            
            ////end region////
            ////member coupon start
            $member_coupon=strtoupper($_POST["coupon"]);
            if($member_coupon=='PMPFD10' ||$member_coupon=='PMP18'||$member_coupon=='PMP25'){
                 $data['boost'] = $boost;
                 $data['razerpay'] = $razerpay;
                 $data['tngpay'] = $tngpay;
                $CustomerInfo = Customer::where('username',$Transaction->buyer_username)->first();
                if($member_coupon=='PMPFD10'){
                 $available_count=$CustomerInfo->membership_delivery;
                }else if($member_coupon=='PMP18'){
                 $available_count=$CustomerInfo->member_disc_1;
                }else if($member_coupon=='PMP25'){
                  $available_count=$CustomerInfo->member_disc_2;
                }
             if($CustomerInfo->preferred_member==1 && $available_count==0){
                Session::put('coupon_msg','Limit finished for coupon code:'.$member_coupon);
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                        if($_POST['devicetype'] == "web"){
                            $data['coupon_msg'] = 'Limit finished for coupon code:'.$member_coupon;
                             return $data;
                        }else{
                        return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
             }else if($CustomerInfo->preferred_member==0)
             {
                   Session::put('coupon_msg','We are regret to inform you that you are not eligible to use it! Please Purchase Jocom preferred membership');
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                     if($_POST['devicetype'] == "web"){
                            $data['coupon_msg'] = 'We are regret to inform you that you are not eligible to use it! Please Purchase Jocom preferred membership';
                             return $data;
                        }else{
                        return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }  
             }
             
            }
            ///////member coupon end
            
            if($lscheck == 0){
                if (isset($_POST["coupon"])) {
                    $temp_xml = MCheckout::insert_coupon_code($_POST["transaction_id"], $_POST["coupon"]);
                    $this->pointBalance($_POST["transaction_id"]);
                    
                    $coupondata = Coupon::where("coupon_code",$_POST["coupon"])->first();
                    if(isset($coupondata->boost_payment) && $coupondata->boost_payment == 1){
    
                        $boost = 1; 
                    }
                    
                    if(isset($coupondata->razerpay_payment) && $coupondata->razerpay_payment == 1){
	                    $razerpay = 1;  
	                }
	                
	                if(isset($coupondata->tng_payment) && $coupondata->tng_payment == 1){
	                    $tngpay = 1; 
	                }
	                if($coupondata->is_jpoint=="0"){
	                    $restrict_jpoint="0";
	                }
	                
	                
	                $couponid = $_POST["coupon"];
                    if(isset($couponid) && $couponid != ''){
                        $freeitem  = MCheckout::FreecouponItemFoc($_POST["transaction_id"],$_POST["coupon"]);

                    }
                    
                      
                }
            }
            else {
                
                if($wonda == 7){
                    Session::put('coupon_msg', 'Oops! You will not be able to checkout this item with other products.');
                }
                else
                {
                Session::put('coupon_msg', 'We are regret to inform you that you are not eligible to use it. 我们很遗憾地通知您, 您不符合使用此条件。');
                }
            }
            
            //to continue
            $data                   = MCheckout::get_checkout_info($_POST["transaction_id"]);
            $data['transaction_id'] = $_POST["transaction_id"];
            $data['coupon_msg'] = Session::get('coupon_msg');
            $data['jpoint_restriction']=$restrict_jpoint;
            $data['boost'] = $boost;
            $data['razerpay'] = $razerpay;
            $data['tngpay'] = $tngpay;
            
            $JCash = self::getJcashbackdetails($_POST["transaction_id"]);

            if(isset($JCash)){
                $data['cashbackdetails_id'] = $JCash['trans_cashback']['id'];
                $data     = array_merge($data, $JCash);
            }
            
            if (isset($data['trans_query'])) {
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                // Valid Page
                
                if($_POST['devicetype'] == "web"){
                     return $data;
                }else{
                    return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                }
                
            } else {
                $data['message'] = '101';
                // $data['message'] = 'Invalid request. Please try again.';
                return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
            }
        } else {
            //header('location:{{asset('/')}}checkout');
            //header('location:https://www.paypal.com/cgi-bin/webscr' . $fieldStr);
            //exit();

            return Redirect::to('checkout');
            //return Response::view('checkout/checkout_view', $data);
        }
        
            
}catch(Exception $ex){
      //echo $ex->getTraceAsString();
    //   echo $ex->getFile();
    //   echo $ex->getLine();
    echo $ex->getMessage();
    
}
    }

    public function anyCouponpubliccode()
    {
        
    try{
        $lscheck = 0;
        $boost = 0;
        $razerpay = 0; 
        $ccard = 0;
        $publicbin = 1; 
        $couponid = '';
        $restrict_jpoint=1;
        if (isset($_POST["transaction_id"])) {
            $transactionID = $_POST["transaction_id"];
                                
            $Transaction = Transaction::find($transactionID);       
            $transactionDate = $Transaction->transaction_date;      
                
            if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){     
            /* INCLUSIVE TEMPLATE */        
                $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';       
            }  else{        
                /* EXCLUSIVE TEMPLATE */        
                $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout' : '.checkout_view';     
            } 

             //Temp.    
                $checkout_view    = ($_POST['devicetype'] == "manual") ? '.checkout_view_v2' : '.checkout_view_v2';        
            
            Session::put('lang', $_POST['lang']);
            Session::put('devicetype', $_POST['devicetype']);
            $data                   = MCheckout::get_checkout_info($_POST["transaction_id"]);
            $buyerId        = $data['trans_query']->buyer_id;
            $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
            $data['points'] = $points;
            $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
            $data['transaction_id'] = $_POST["transaction_id"];
            $data['coupon_msg'] = Session::get('coupon_msg');
            $alert=array();
            
        ///restriction module start
        if($data['trans_detail_query']!=""){
            $product_details=$data['trans_detail_query'];
            
            foreach($product_details as $value){
                $vouchercode=DB::table('jocom_products')->select('name')->where('id','=',$value->product_id)->where('is_voucher_code','=',"1")->first();
                if(!empty($vouchercode)){
                $alert[]=$vouchercode->name;
                }
            }
            if(!empty($alert)){
               
                $hint="This Products Not allow using Bank Card Promotions:<br>";
               foreach($alert as $mess){
                   $hint.=$mess."<br>";
               }
               Session::put('coupon_msg', "{$hint}");
            
            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
            }
           }
          
           ////restriction module end
           //all voucher restrict
            $ProductCheck=TDetails::where('transaction_id', '=', $data['transaction_id'])->where('product_id','=','54361')->first();
            if($ProductCheck){
            //check coupon code
                    $coupon_c=strtoupper($_POST["coupon_codepublic"]);
                    if($coupon_c!="JCMPMP100"){
                Session::put('coupon_msg', "Oops! You will not be able to use this bank card promo voucher with jocom preferred member package.");
                 if($_POST['devicetype'] == "web"){
                            $data['coupon_msg'] = "Oops! You will not be able to use this bank card promo voucher with jocom preferred member package.";
                             return $data;
                        }else{
                return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                    }
            }
            //all voucher restrict end
           //temp coupon restrict
            $tempcheck=TDetails::where('transaction_id', '=', $data['transaction_id'])->where('product_id','=','54196')->first();
            
            if($tempcheck){
               
            //check min purchase requirement
            $products=DB::table('jocom_products')->select('name')->where('id','=','54196')->first();
                    $sum_price    = TDetails::where('transaction_id', '=', $data['transaction_id'])->sum('total');
                    $sum_purchase = number_format($sum_price, 2);
                    $CouponAmountCheck = Coupon::where("coupon_code",$_POST["coupon_codepublic"])->first();
                    $FinalCheckAmount=$sum_purchase-$CouponAmountCheck->amount;
                    if($FinalCheckAmount<150){
                Session::put('coupon_msg', "To use bank card promotion with ".$products->name.",the minimum purchase amount is RM150");
                    if($_POST['devicetype'] == "web"){
                                $data['coupon_msg'] = "To use bank card promotion with ".$products->name.",the minimum purchase amount is RM150";
                             return $data;
                        }else{
                            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                    }
            }
            //end coupon restrict



            $lscheck = Transaction::Checkpublicbinexists($_POST["coupon_codepublic"],$_POST["public_bin"]);
            // echo $lscheck;
            // if($lscheck == 0){
            //         $lscheck = Transaction::Checkproductexists($_POST["transaction_id"]);
            //     }
            if($lscheck == 0){
                    $lscheck = Transaction::Checkjcmeleven11($_POST["transaction_id"]);
                }
            // if($lscheck == 0){
            //     $lscheck = Transaction::Checkboostproductexists($_POST["transaction_id"],$_POST["coupon_codepublic"]);
            // }
            if($lscheck == 0){
                if (isset($_POST["coupon_codepublic"])) {
                    $coupondata = Coupon::where("coupon_code",$_POST["coupon_codepublic"])->first();
                    $publicbin = $coupondata->is_bank_code;
                    $temp_xml = MCheckout::insert_couponpublic_code($_POST["transaction_id"], $_POST["coupon_codepublic"],$publicbin);
                    $this->pointBalance($_POST["transaction_id"]);
                    if($coupondata->is_jpoint=="0"){
	                    $restrict_jpoint="0";
	                }
                    
                    if(isset($coupondata->is_bank_code) && $coupondata->is_bank_code > 0){
    
                        $ccard = 1; 

                        // LOG Public Bank Bin Transaction  //
                        $PublicTrans = new CouponPublicTransaction ;
                        $PublicTrans->bin_number = $_POST["public_bin"];
                        $PublicTrans->transaction_id = $_POST["transaction_id"];
                        $PublicTrans->coupon_code = $_POST["coupon_codepublic"];
                        $PublicTrans->insert_by =  Session::get('username'); 
                        $PublicTrans->created_at =  date("Y-m-d H:i:s");
                        $PublicTrans->modify_by =  Session::get('username'); 
                        $PublicTrans->updated_at =  date("Y-m-d H:i:s");
                        $PublicTrans->save();

                    } 



                }
            }
            else {
                Session::put('coupon_msg', 'We regret to inform you that Invalid Bin Number / Coupon Code.');
            }
            
            //to continue
            $data                   = MCheckout::get_checkout_info($_POST["transaction_id"]);
            $data['transaction_id'] = $_POST["transaction_id"];
            $data['coupon_msg'] = Session::get('coupon_msg');
            $data['jpoint_restriction']=$restrict_jpoint;
            $data['boost'] = $boost;
            $data['razerpay'] = $razerpay;
            $data['ccard'] = $ccard;
            
            $JCash = self::getJcashbackdetails($_POST["transaction_id"]);

            if(isset($JCash)){
                $data['cashbackdetails_id'] = $JCash['trans_cashback']['id'];
                $data     = array_merge($data, $JCash);
            }
            
            if (isset($data['trans_query'])) {
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data["transaction_id"]);
                // Valid Page
                
                if($_POST['devicetype'] == "web"){
                     return $data;
                }else{
                    return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                }
                
            } else {
                $data['message'] = '101';
                // $data['message'] = 'Invalid request. Please try again.';
                return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
            }
        } else {
            //header('location:{{asset('/')}}checkout');
            //header('location:https://www.paypal.com/cgi-bin/webscr' . $fieldStr);
            //exit();

            return Redirect::to('checkout');
            //return Response::view('checkout/checkout_view', $data);
        }
        
                    
        }catch(Exception $ex){
              //echo $ex->getTraceAsString();
               echo $ex->getFile();
              echo $ex->getLine();
            echo $ex->getMessage();
            
        }
    }
    
     /*
     * @Desc : Redeem JCashback from jocom APP
     */

    public function anyJcashback()
    {
        
        try{
   

                $jcashmessage = '';
                $cashbackflag = 1; 
                $cashback = 1;

                // print_r(Input::all()); 

                // die();
                
                if (isset($_POST["transaction_id"])) {
                    $transactionID = $_POST["transaction_id"];
                                        
                    $Transaction = Transaction::find($transactionID);       
                    $transactionDate = $Transaction->transaction_date;      
                        
                    if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){     
                    /* INCLUSIVE TEMPLATE */        
                        $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';       
                    }  else{        
                        /* EXCLUSIVE TEMPLATE */        
                        $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout' : '.checkout_view';     
                    }       
                    
                    $checkout_view    = ($_POST['devicetype'] == "manual") ? '.checkout_view_v2' : '.checkout_view_v2';     

                    Session::put('lang', $_POST['lang']);
                    Session::put('devicetype', $_POST['devicetype']);
                    $jcashid = $_POST["jcashbackid"];
                    $userid = $_POST["jcashuserid"];
                    $sku = $_POST["jcashsku"];
                    $jcashpoints = $_POST["jcashpoints"];
                    $jcashmessage = $jcashpoints.' JCashback Points'; 

                    Session::put('jcashback_msg', $jcashmessage);

                    if(count($JCashValidate) == 0){
                        $cashdata = array(
                            "jcashback_id"      => $jcashid, 
                            "transaction_id"    => $transactionID, 
                            "user_id"           => $userid, 
                            "sku"               => $sku, 
                            "jcash_point"       => $jcashpoints,
                            "created_by"        => Session::get('username') ? Session::get('username'):'API_UPDATE', 
                            "created_at"        => date("Y-m-d h:i:sa"), 
                            "updated_by"        => Session::get('username') ? Session::get('username'):'API_UPDATE', 
                            "updated_at"        => date("Y-m-d h:i:sa") 
                        ); 
                        $insert_id_cash = DB::table('jocom_jcashback_transactiondetails')->insertGetId($cashdata);
                    }
                    else{

                        $insert_id_cash = $JCashValidate->id; 
                    }
                    
                   

                    //to continue
                    $data                   = MCheckout::get_checkout_info($_POST["transaction_id"]);
                    $data['transaction_id'] = $_POST["transaction_id"];
                    $data['cashback'] = $cashback;
                    $data['cashbackdetails_id'] = $insert_id_cash;
                    
                    
                    if (isset($data['trans_query'])) {
                        $buyerId        = $data['trans_query']->buyer_id;
                        $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                        $data['points'] = $points;
                        // Valid Page
                        

                        //Start JCashback 
                        if(isset($cashbackflag) && $cashbackflag == 1) {

                                $trans_cashback = DB::table('jocom_transaction_jcashback')
                                     ->where('id','=',$jcashid)
                                     ->first();

                                if(count($trans_cashback) > 0){

                                      $cback_array = array('id' => $trans_cashback->id,    
                                                           'sku' => $trans_cashback->sku,
                                                           'user_id' => $trans_cashback->user_id,
                                                           'product_name' => $trans_cashback->product_name,
                                                           'jcash_point' => $trans_cashback->jcash_point
                                                     );

                                     $tempdatacashback['trans_cashback'] = $cback_array;

                                     $data     = array_merge($data, $tempdatacashback);

                                }

                               


                        }
                        //End JCashback 

                        
                        if($_POST['devicetype'] == "web"){
                             return $data;
                        }else{
                            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                        }
                        
                    } else {
                        $data['message'] = '101';
                        // $data['message'] = 'Invalid request. Please try again.';
                        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                    }
                } else {
                
                    return Redirect::to('checkout');
                    //return Response::view('checkout/checkout_view', $data);
                }
                
                    
        }catch(Exception $ex){
              //echo $ex->getTraceAsString();
            //   echo $ex->getFile();
            //   echo $ex->getLine();
            echo $ex->getMessage();
            
        }
    }
    
    public static function getJcashbackdetails($transaction_id){

        $JcashTrans = DB::table('jocom_jcashback_transactiondetails')
                        ->where('transaction_id','=',$transaction_id)
                        ->first();

        if(count($JcashTrans) > 0){

            $cback_array = array('id' => $JcashTrans->id,    
                               'sku' => $JcashTrans->sku,
                               'user_id' => $JcashTrans->user_id,
                               'product_name' => $JcashTrans->product_name,
                               'jcash_point' => $JcashTrans->jcash_point
                         );

         $tempdatacashback['trans_cashback'] = $cback_array;

        

        }

        return $tempdatacashback;
    }

    /*
     * @Desc : Subscribe popbox service from jocom APP
     */
    public function anyPopbox()
    {
        //$checkout_view = ($_POST['devicetype'] == "manual") ? '.manual_checkout' : '.checkout_view';
        $checkout_view = '.checkout_view';
        $popboxLocation = Input::get('popboxLocation');
        $popboxAddress = Input::get('popboxAddress');
        $transactionID = Input::get('transaction_id');
        $max_popbox_weight = 10000;
        
        if ($transactionID != "") {
            Session::put('lang', $_POST['lang']);
            Session::put('devicetype', $_POST['devicetype']);
            
            $data = MCheckout::get_checkout_info($_POST["transaction_id"]);
            if($data['total_all_weight'] <= $max_popbox_weight){
                
                if ($popboxLocation != "" && $popboxAddress != "") {
                
                    $PopboxOrder = PopboxOrder::where("transaction_id",$transactionID)->first();
                    if(count($PopboxOrder) > 0){
                        $PopboxOrder->status = 0;
                        $PopboxOrder->save();
                    }

                    $popBoxReturn = PopboxController::savePopBox($transactionID,$popboxLocation,$popboxAddress);
                    if($popBoxReturn == 1){
                        $popboxsaved = true;
                        // Update Special Message 
                        $Transaction = Transaction::find($transactionID);
                        $Transaction->special_msg = $Transaction->special_msg." ".$popboxLocation." - ".$popboxAddress;
                        $Transaction->save();
                    }else{
                        $popboxsaved = false;
                    }
                }
            }else{
                Session::put('coupon_msg', 'We are sorry, total weight of purchase items cannot be more than 10KG for PopBox service');
            }
            
            //to continue
            
            $data['transaction_id'] = $_POST["transaction_id"];
            
            if($popboxsaved){
                $data['is_popbox'] = 1;
                $data['popbox_locker'] = $popboxLocation;
                $data['popbox_address'] = $popboxAddress;
            }else{
                $data['is_popbox'] = 0;
                $data['popbox_locker'] = $popboxLocation;
                $data['popbox_address'] = $popboxAddress;
            }
            
            if (isset($data['trans_query'])) {
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                
//                echo "<pre>";
//                print_r($data);
//                echo "</pre>";
//                die();
                // Valid Page
                return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                
            } else {
                $data['message'] = '101';
                // $data['message'] = 'Invalid request. Please try again.';
                return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
            }
        } else {
            //header('location:{{asset('/')}}checkout');
            //header('location:https://www.paypal.com/cgi-bin/webscr' . $fieldStr);
            //exit();
            
            return Redirect::to('checkout');
            //return Response::view('checkout/checkout_view', $data);
        }
    }

    public function postRedemption()
    {
        Session::put('lang', $_POST['lang']);
        Session::put('devicetype', $_POST['devicetype']);
        $lscheck = 0;
        $transactionId = Input::get('transaction_id');
        $data          = MCheckout::get_checkout_info($transactionId);
        $alert=array();
        $transaction   = $data['trans_query'];
        $Transaction = Transaction::find($transactionId);		
        $transactionDate = $Transaction->transaction_date;		
        		
        if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){		
            /* INCLUSIVE TEMPLATE */		
            $checkout_view    = (Input::get('devicetype') == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';		
        }  else{		
            /* EXCLUSIVE TEMPLATE */		
            $checkout_view    = (Input::get('devicetype') == "manual") ? '.manual_checkout' : '.checkout_view';		
        }

        if ( ! $transaction) {
            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
        }

        $redeemPoint            = Input::get('point');
        $type                   = PointType::findOrFail(Input::get('type'));
        $totalAmount            = $transaction->total_amount;
        $totalGst               = $transaction->gst_total;
        $transactionCoupon      = TCoupon::where('transaction_id', '=', $transactionId)->first();
        $transactionPoint       = TPoint::where('transaction_id', '=', $transactionId)->first();
        $buyerId                = $transaction->buyer_id;
        $point                  = PointUser::getPoint($buyerId, $type->id, PointUser::ACTIVE_ONLY);
        $points                 = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
        $data['points']         = $points;
        $data['transaction_id'] = $transactionId;
        $data['static_coupon']=self::getCheckoutcoupon($data['transaction_id']);
        
        $lscheck = Transaction::Checkjcmeleven11($transactionId);
        if($data['trans_detail_query']!=""){
            $product_details=$data['trans_detail_query'];
            foreach($product_details as $value){
                $newjpoints=DB::table('jocom_products')->select('name')->where('id','=',$value->product_id)->where('is_jpoint','=',"1")->first();
                if(!empty($newjpoints)){
                $alert[]=$newjpoints->name;
                }
            }
            if(!empty($alert)){
               
                $hint="This Products Not allow using JPoints:<br>";
               foreach($alert as $mess){
                   $hint.=$mess."<br>";
               }
               Session::put('pointMessage', "{$hint}");

            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
            }
            
            
        }
        //temp coupon restrict
            $tempcheck=TDetails::where('transaction_id', '=', $data['transaction_id'])->where('product_id','=','54196')->first();
            
            if($tempcheck){
               
            //check min purchase requirement
            $products=DB::table('jocom_products')->select('name')->where('id','=','54196')->first();
                    $sum_price    = TDetails::where('transaction_id', '=', $data['transaction_id'])->sum('total');
                    $sum_purchase = number_format($sum_price, 2);
                    $FinalCheckAmount=$sum_purchase-$redeemPoint/100;
                    if($FinalCheckAmount<150){
                Session::put('coupon_msg', "To use jpoint with ".$products->name.",the minimum purchase amount is RM150");
                
            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                    }
            }
            //end coupon restrict
            
        // if($lscheck == 0){
        //     $lscheck = Transaction::Checkvoucher($transactionId);
        // }
        
        if($lscheck == 0){
        if ( ! $point || empty($redeemPoint)) {
            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
        } elseif ($point->status == PointUser::INACTIVE || $point->status == PointUser::DELETED) {
            TPoint::void($transactionId, $type->id);

            // Refresh data
            $data                   = MCheckout::get_checkout_info($transactionId);
            $points                 = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
            $data['points']         = $points;
            $data['static_coupon']=self::getCheckoutcoupon($data['transaction_id']);
            $data['transaction_id'] = $transactionId;

            Session::put('pointMessage', "Invalid point redemption. Your points are not active.");

            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
        } elseif ( ! is_numeric($redeemPoint) || $redeemPoint <= 0 || $redeemPoint > $point->point || ($transactionPoint->point + $redeemPoint) > $point->point) {
            Session::put('pointMessage', "Invalid point redemption: {$redeemPoint}");

            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
        } else {
            $couponAmount  = $transactionCoupon ? $transactionCoupon->coupon_amount : 0;
            $pointAmount   = $transactionPoint ? $transactionPoint->amount : 0;
            $maxRedemption = ($totalAmount + $totalGst - $couponAmount - $pointAmount) / $type->redeem_rate;
            $redeemPoint   = ($redeemPoint > $maxRedemption) ? $maxRedemption : $redeemPoint;
            $pointUser     = PointUser::getPoint($buyerId, $type->id, true);

            if ($pointUser) {
                $pointTransaction = new PointTransaction($pointUser);
                $pointTransaction->redeem($transactionId, $redeemPoint);

                // Refresh data
                $data                   = MCheckout::get_checkout_info($transactionId);
                $points                 = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points']         = $points;
                $data['static_coupon']=self::getCheckoutcoupon($data['transaction_id']);
                $data['transaction_id'] = $transactionId;

                Session::put('pointMessage', "{$type->type} applied. You may proceed to checkout.");
            }
            
            // if($_POST['devicetype'] == "web"){
            //      return $data;
            // }else{
            //     return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
            // }

            return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
        }
        
        }else {
                 Session::put('pointMessage', 'We are regret to inform you that you are not eligible to use it');

                return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                
            }
        
    }
    
    // YEE HAO: Maruthu ask to do on these way
    // Duplicate function of the postRedemption
    // Added support for the website url
    public function postPointredemp(){
        Session::put('lang', $_POST['lang']);
        Session::put('devicetype', $_POST['devicetype']);

        $transactionId   = Input::get('transaction_id');
        $data            = MCheckout::get_checkout_info($transactionId);
        $transaction     = $data['trans_query'];
        $Transaction     = Transaction::find($transactionId);       
        $transactionDate = $Transaction->transaction_date;      
                
        $checkout_view    = ($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE') ? (Input::get('devicetype') == "manual" ? '.manual_checkout_v2' : '.checkout_view_v2' ) : (Input::get('devicetype') == "manual" ? '.manual_checkout' : '.checkout_view'));

        if (!$transaction) return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);

        $redeemPoint            = Input::get('point');
        $type                   = PointType::findOrFail(Input::get('type'));
        $totalAmount            = $transaction->total_amount;
        $totalGst               = $transaction->gst_total;
        $transactionCoupon      = TCoupon::where('transaction_id', '=', $transactionId)->first();
        $transactionPoint       = TPoint::where('transaction_id', '=', $transactionId)->first();
        $buyerId                = $transaction->buyer_id;
        $point                  = PointUser::getPoint($buyerId, $type->id, PointUser::ACTIVE_ONLY);
        $points                 = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
        $data['points']         = $points;
        $data['transaction_id'] = $transactionId;
        
        //temp coupon restrict
            $tempcheck=TDetails::where('transaction_id', '=', $data['transaction_id'])->where('product_id','=','54196')->first();
            
            if($tempcheck){
               
            //check min purchase requirement
            $products=DB::table('jocom_products')->select('name')->where('id','=','54196')->first();
                    $sum_price    = TDetails::where('transaction_id', '=', $data['transaction_id'])->sum('total');
                    $sum_purchase = number_format($sum_price, 2);
                    $FinalCheckAmount=$sum_purchase-$redeemPoint/100;
                    if($FinalCheckAmount<150){
                        $data['coupon_msg'] = "To use jpoint with ".$products->name.",the minimum purchase amount is RM150";
                // Session::put('coupon_msg', "To use jpoint with ".$products->name.",the minimum purchase amount is RM150");
                             return $data;
                    }
            }
            //end coupon restrict

        if ( ! $point || empty($redeemPoint)) {
            // do nothing just return the data
        } elseif ($point->status == PointUser::INACTIVE || $point->status == PointUser::DELETED) {
            TPoint::void($transactionId, $type->id);

            // Refresh data
            $data                   = MCheckout::get_checkout_info($transactionId);
            $points                 = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
            $data['points']         = $points;
            $data['transaction_id'] = $transactionId;

            Session::put('pointMessage', "Invalid point redemption. Your points are not active.");
        } elseif ( ! is_numeric($redeemPoint) || $redeemPoint <= 0 || $redeemPoint > $point->point || ($transactionPoint->point + $redeemPoint) > $point->point) {
            Session::put('pointMessage', "Invalid point redemption: {$redeemPoint}");
        } else {
            $couponAmount  = $transactionCoupon ? $transactionCoupon->coupon_amount : 0;
            $pointAmount   = $transactionPoint ? $transactionPoint->amount : 0;
            $maxRedemption = ($totalAmount + $totalGst - $couponAmount - $pointAmount) / $type->redeem_rate;
            $redeemPoint   = ($redeemPoint > $maxRedemption) ? $maxRedemption : $redeemPoint;
            $pointUser     = PointUser::getPoint($buyerId, $type->id, true);

            if ($pointUser) {
                $pointTransaction = new PointTransaction($pointUser);
                $pointTransaction->redeem($transactionId, $redeemPoint);

                // Refresh data
                $data                   = MCheckout::get_checkout_info($transactionId);
                $points                 = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points']         = $points;
                $data['transaction_id'] = $transactionId;

                Session::put('pointMessage', "{$type->type} applied. You may proceed to checkout.");
            }
        }
        $data['pointMessage'] = Session::get('pointMessage');

        if(in_array(Input::get('devicetype'), ["web", "web_others", "web_mycash", "webboost", "web_nuvending"])){
            return $data;
        }else{
            return Response::view(Config::get('constants.CHECKOUT_FOLDER') . $checkout_view, $data);
        }
    }

    public function anyPoint()
    {
        $success       = false;
        $webcheckout    = 0;
        $transactionId = Input::get('tran_id');
        $webcheckout = Input::get('devicetype');
        
        $transaction   = Transaction::findOrFail($transactionId);
        $totalAmount   = object_get($transaction, 'total_amount', 0);
        $gstTotal      = object_get($transaction, 'gst_total', 0);
        $grandTotal    = $totalAmount + $gstTotal;
        $couponAmount  = TCoupon::where('transaction_id', '=', $transactionId)->pluck('coupon_amount');
        $rewardAmount  = $grandTotal - $couponAmount;

        if ($couponAmount >= $grandTotal) {
            $success = true;
        } else {
            $transactionPoints = DB::table('jocom_transaction_point')
                ->select('jocom_transaction_point.point AS redeem_point', 'point_users.point AS user_point')
                ->join('jocom_transaction', 'jocom_transaction_point.transaction_id', '=', 'jocom_transaction.id')
                ->join('point_users', 'jocom_transaction.buyer_id', '=', 'point_users.user_id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('jocom_transaction_point.transaction_id', '=', $transactionId)
                ->where('jocom_transaction.buyer_id', '=', $transaction->getUserId())
                ->where('point_users.status', '=', 1)
                ->where('point_types.status', '=', 1)
                ->get();

            if ($transactionPoints) {
                $success = true;
            }

            foreach ($transactionPoints as $point) {
                if ($point->user_point < $point->redeem_point) {
                    $success = false;
                    break;
                }

                $rewardAmount = $rewardAmount - $point->redeem_point;
            }
        }

        if ($success) {
            MCheckout::point_transaction_complete($transactionId);

            $transaction = Transaction::find($transactionId);
            $user        = Customer::find($transaction->buyer_id);
            $username    = $user->username;
            // $bcard       = BcardM::where('username', '=', $username)->first();

            $data['message'] = '001';
            $data['payment'] = 'JCSUCCESS successfully received';

            $transaction = Transaction::find($transactionId);
            $bcardStatus = PointModule::getStatus('bcard_earn');
            
            if($webcheckout == 'web'){
                    return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=success');
            }
            else
            {

            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')
                ->with('message', $data['message'])
                ->with('payment', $data['payment'])
                ->with('id', $transactionId)
                ->with('bcardStatus', $bcardStatus)
                ->with('bcardNumber', object_get($bcard, 'bcard'))
                ->with('buyerId', $transaction->buyer_id)
                ->with('rewardAmount', $rewardAmount);
            }    
                
        } else {
            $data['message'] = '002';
            $data['payment'] = 'JCFAIL failed';

            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
        }
    }

    // Manage Pay Gateway
    public function anyMpayrtn()
    {
        if (Input::has('result')) {
            $result = Input::get('result');

            $data['resp']     = substr($result, 0, 1);
            $data['authCode'] = substr($result, 1, 6);
            $data['invno']    = ltrim(substr($result, 7, 20), 0);
            $data['card4']    = substr($result, 27, 4);
            $data['amt']      = substr($result, 31);

            $transactionType = MCheckout::mpay_transaction_complete($data);

            if ($data['resp'] == '0') {
                // Success
                switch ($transactionType) {
                    case 'point':
                        $data['message'] = '006';
                        break;
                    default:
                        $data['message'] = '001';
                        break;
                }

                $transaction = Transaction::find($data['invno']);
                $user        = Customer::find($transaction->buyer_id);
                $username    = $user->username;
                $bcard       = BcardM::where('username', '=', $username)->first();
                $bcardStatus = PointModule::getStatus('bcard_earn');

                $data['payment'] = 'JCSUCCESS successfully received';
                // $data['message'] = 'Your order has been successfully received.<br />Thank you for shopping at JOCOM.';
                return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')
                    ->with('message', $data['message'])
                    ->with('payment', $data['payment'])
                    ->with('id', $data['invno'])
                    ->with('bcardStatus', $bcardStatus)
                    ->with('bcardNumber', object_get($bcard, 'bcard'))
                    ->with('buyerId', $transaction->buyer_id);
            } else {
                // Error
                // Failed
                $temp_cancel     = MCheckout::cancelled_transaction(trim($data['invno']));
                $data            = array();
                $data['message'] = '002';
                $data['payment'] = 'JCFAIL failed';
                // $data['message'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting.';
                return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment']);
            }

        } else {
            $data = array();
            // $data['message'] = 'Invalid request. Please try again.';
            $data['message'] = '003';
            // $data['message'] = 'Check your order status in Transaction History.';
            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
        }
    }

    // MOLPay Gateway
    public function anyMolpayrtn()
    {
        
        $conf      = MCheckout::molpay_conf();
        $verifykey = $conf['molpay_verifykey'];

        $_POST['treq'] = 1;

        $tranID   = trim(Input::get('tranID'));
        $orderid  = str_replace('"', '', trim(Input::get('orderid')));
        $status   = trim(Input::get('status'));
        $domain   = trim(Input::get('domain'));
        $amount   = trim(Input::get('amount'));
        $currency = trim(Input::get('currency'));
        $appcode  = trim(Input::get('appcode'));
        $paydate  = trim(Input::get('paydate'));
        $skey     = trim(Input::get('skey'));
        $nbcb     = Input::has('nbcb') ? trim(Input::get('nbcb')) : 0;
        
        // LOG MOLPAY RESPONSE //
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'MOLPAY_RESPONSE';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

        
        // WEB CHECKOUT //

        $key0 = md5($tranID.$orderid.$status.$domain.$amount.$currency);
        $key1 = md5($paydate.$domain.$key0.$appcode.$verifykey);

        if ($skey != $key1) {
            $status = -1;
        }

        // WEB CHECKOUT //
        $Transaction = Transaction::find($orderid);
        $checkout_source = $Transaction->checkout_source;
        
        // WEB CHECKOUT //
        
        

        $transactionType = MCheckout::mol_transaction_complete(array_merge(Input::all(), $_POST));

        if ($status == "00" || $status == "22") {
            // Success OR Pending
            if ($status == "22") {
                $data            = array();
                $data['message'] = '004';
                // $data['message'] = 'Your payment status was "Pending".<br />Thank you for shopping.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                
                if($nbcb == 1){
                    // For MolPay Callback
                    echo "CBTOKEN:MPSTATOK"; exit;
                }else{
                    
                    if($checkout_source == 2){
                        return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=pending');
                    }
                    elseif($checkout_source == 3) {
                        return Redirect::to('https://ecommunity.jocom.com.my/shop/confirmation?tranID='.$orderid.'&status=pending');
                    }
                    elseif($checkout_source == 4) {
                        return Redirect::to('https://mycashonline.jocom.com.my/shop/confirmation?tranID='.$orderid.'&status=pending');
                    }
                    elseif($checkout_source == 6) {
                        return Redirect::to('https://crossborder.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=pending');
                    }
                    elseif($checkout_source == 8) {
                        return Redirect::to('https://efstore.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=pending');
                    }
                    else{
                        return Redirect::to('checkout/paymentstatus/?tranID='.$orderid.'&status=pending');
                    }
                    
                }


                
            } else {
                $data = array();

                switch ($transactionType) {
                    case 'point':
                        $data['message'] = '006';
                        break;
                    default:
                        $data['message'] = '001';
                        break;
                }

                // $data['message'] = 'Your order has been successfully received.<br />Thank you for shopping.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                if($nbcb == 1){
                    // For MolPay Callback
                    echo "CBTOKEN:MPSTATOK"; exit;
                }else{
                    if($checkout_source == 2){
                        return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=success');
                    }
                    elseif($checkout_source == 3) {
                        return Redirect::to('https://ecommunity.jocom.com.my/shop/confirmation?tranID='.$orderid.'&status=success');
                    }
                    elseif($checkout_source == 4) {
                        return Redirect::to('https://mycashonline.jocom.com.my/shop/confirmation?tranID='.$orderid.'&status=success');
                    }
                    elseif($checkout_source == 6) {
                        return Redirect::to('https://crossborder.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=success');
                    }
                    elseif($checkout_source == 8) {
                        return Redirect::to('https://efstore.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=success');
                    }
                    else{
                        return Redirect::to('checkout/paymentstatus/?tranID='.$orderid.'&status=success');
                    }
                }

            }
        } else {
            // Failed
            $temp_cancel = MCheckout::cancelled_transaction(trim($orderid));

            $data            = array();
            $data['message'] = '005';
            // $data['message'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting.';
            // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
            if($nbcb == 1){
                    // For MolPay Callback
                    echo "CBTOKEN:MPSTATOK"; exit;
            }else{
                if($checkout_source == 2){
                    return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=failed');
                }
                elseif($checkout_source == 3) {
                    return Redirect::to('https://ecommunity.jocom.com.my/shop/confirmation?tranID='.$orderid.'&status=failed');
                }
                elseif($checkout_source == 4) {
                    return Redirect::to('https://mycashonline.jocom.com.my/shop/confirmation?tranID='.$orderid.'&status=failed');
                }
                elseif($checkout_source == 6) {
                    return Redirect::to('https://crossborder.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=failed');
                }
                elseif($checkout_source == 8) {
                        return Redirect::to('https://efstore.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=failed');
                }
                else{
                    return Redirect::to('checkout/paymentstatus/?tranID='.$orderid.'&status=failed');
                }
            }

        }

    }
    
    // revPay Gateway
    public function anyRevpayrtn()
    {
        
        $conf      = MCheckout::revpay_conf();
        $verifykey = $conf['MERCHANT_REVPAY_KEY_LIVE'];

        $revpay_merchantid   = trim(Input::get('Revpay_Merchant_ID'));
        $paymentid  = trim(Input::get('Payment_ID'));
        $transactionid  = trim(Input::get('Transaction_ID'));
        $reference_number  = trim(substr(trim(Input::get('Reference_Number')),'2','20')); 
        $amount  = trim(Input::get('Amount'));
        $currency  = trim(Input::get('Currency'));
        $trans_description  = trim(Input::get('Transaction_Description'));
        $response_code  = trim(Input::get('Response_Code'));
        $error_description  = trim(Input::get('Error_Description'));
        $settle_amount  = trim(Input::get('Settlement_Amount'));
        $settlement_currency  = trim(Input::get('Settlement_Currency'));
        $orderid  = trim(Input::get('Settlement_FX_Rate'));
        $orderid  = trim(Input::get('Key_Index'));
        $orderid  = trim(Input::get('Signature'));
        $orderid  = trim(Input::get('Request_Datetime'));
        $orderid  = trim(Input::get('Response_Datetime'));
       
        
        // LOG MOLPAY RESPONSE //
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'REVPAY_RESPONSE';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

        
        // WEB CHECKOUT //


        if ($skey != $key1) {
            $status = -1;
        }

        // WEB CHECKOUT //
        $Transaction = Transaction::find($reference_number);
        $checkout_source = $Transaction->checkout_source;
        
        // WEB CHECKOUT //
        
        

        $transactionType = MCheckout::rev_transaction_complete(array_merge(Input::all(), $_POST));

        if ($response_code == "00" || $response_code == "09") {
            // Success OR Pending
            if ($response_code == "09") {
                $data            = array();
                $data['message'] = '004';
                // $data['message'] = 'Your payment status was "Pending".<br />Thank you for shopping.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                
                if($nbcb == 1){
                    // For MolPay Callback
                    echo "CBTOKEN:MPSTATOK"; exit;
                }else{
                    if($checkout_source == 2){
                        return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$reference_number.'&status=pending');
                    }
                    elseif($checkout_source == 6) {
                    return Redirect::to('https://crossborder.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=pending');
                    }
                    elseif($checkout_source == 8) {
                        return Redirect::to('https://efstore.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=pending');
                    }
                    else{
                        return Redirect::to('checkout/paymentstatus/?tranID='.$reference_number.'&status=pending');
                    }
                }


                
            } else {
                $data = array();

                switch ($transactionType) {
                    case 'point':
                        $data['message'] = '006';
                        break;
                    default:
                        $data['message'] = '001';
                        break;
                }

                // $data['message'] = 'Your order has been successfully received.<br />Thank you for shopping.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                if($nbcb == 1){
                    // For MolPay Callback
                    echo "CBTOKEN:MPSTATOK"; exit;
                }else{
                    if($checkout_source == 2){
                        return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$reference_number.'&status=success');
                    }
                    elseif($checkout_source == 6) {
                    return Redirect::to('https://crossborder.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=success');
                    }
                    elseif($checkout_source == 8) {
                        return Redirect::to('https://efstore.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=success');
                    }
                    else{
                        return Redirect::to('checkout/paymentstatus/?tranID='.$reference_number.'&status=success');
                    }
                }

            }
        } else {
            // Failed
            $temp_cancel = MCheckout::cancelled_transaction(trim($orderid));

            $data            = array();
            $data['message'] = '005';
            // $data['message'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting.';
            // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
            if($nbcb == 1){
                    // For MolPay Callback
                    echo "CBTOKEN:MPSTATOK"; exit;
            }else{
                if($checkout_source == 2){
                    return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$reference_number.'&status=failed');
                }
                elseif($checkout_source == 6) {
                    return Redirect::to('https://crossborder.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=failed');
                }
                elseif($checkout_source == 8) {
                        return Redirect::to('https://efstore.tmgrocer.com/p_respond.php?tranID='.$orderid.'&status=failed');
                    }
                else{
                    return Redirect::to('checkout/paymentstatus/?tranID='.$reference_number.'&status=failed');
                }
            }

        }

    }
    
    
    public function anyRevpaybackrtn()
    {
        echo 'OK';
        // exit();
        // $conf      = MCheckout::revpay_conf();
        // $verifykey = $conf['MERCHANT_REVPAY_KEY_LIVE'];

        // $revpay_merchantid   = trim(Input::get('Revpay_Merchant_ID'));
        // $paymentid  = trim(Input::get('Payment_ID'));
        // $transactionid  = trim(Input::get('Transaction_ID'));
        // $reference_number  = trim(substr(trim(Input::get('Reference_Number')),'2','20')); 
        // $amount  = trim(Input::get('Amount'));
        // $currency  = trim(Input::get('Currency'));
        // $trans_description  = trim(Input::get('Transaction_Description'));
        // $response_code  = trim(Input::get('Response_Code'));
        // $error_description  = trim(Input::get('Error_Description'));
        // $settle_amount  = trim(Input::get('Settlement_Amount'));
        // $settlement_currency  = trim(Input::get('Settlement_Currency'));
        // $orderid  = trim(Input::get('Settlement_FX_Rate'));
        // $orderid  = trim(Input::get('Key_Index'));
        // $orderid  = trim(Input::get('Signature'));
        // $orderid  = trim(Input::get('Request_Datetime'));
        // $orderid  = trim(Input::get('Response_Datetime'));
       
        
        // // LOG MOLPAY RESPONSE //
        // $ApiLog = new ApiLog ;
        // $ApiLog->api = 'REVPAY_RESPONSE_BACKEND';
        // $ApiLog->data = json_encode(Input::all());
        // $ApiLog->save();

        // echo 'OK';
        
        // // $transactionType = MCheckout::rev_transaction_complete(array_merge(Input::all(), $_POST));

        // if ($response_code == "00" || $response_code == "09") {
        //     // Success OR Pending
        //     if ($response_code == "09") {
        //         echo 'OK';
        //     } else {
               
        //         echo 'OK';
        //     }
        // } else {
        //     // Failed
        //     // $temp_cancel = MCheckout::cancelled_transaction(trim($orderid));
        //     echo 'Not ok';
        // }

    }

    // MOLPay Gateway XDK for Titanium
    public function anyMolpayrtn2()
    {

        $tranID   = trim(Input::get('txn_ID'));
        $orderid  = str_replace('"', '', trim(Input::get('order_id')));
        $status   = trim(Input::get('status_code'));
        $amount   = trim(Input::get('amount'));
        $paydate  = trim(Input::get('paydate'));

        $transactionType = MCheckout::mol_transaction_complete2(array_merge(Input::all(), $_POST));

        if ($status == "00" || $status == "22") {
            // Success OR Pending
            if ($status == "22") {
                $data            = array();
                $data['message'] = '004';
                // $data['message'] = 'Your payment status was "Pending".<br />Thank you for shopping.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                return Redirect::to('checkout/paymentstatus/?tranID='.$orderid.'&status=pending');
            } else {
                $data = array();

                switch ($transactionType) {
                    case 'point':
                        $data['message'] = '006';
                        break;
                    default:
                        $data['message'] = '001';
                        break;
                }

                // $data['message'] = 'Your order has been successfully received.<br />Thank you for shopping.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                return Redirect::to('checkout/paymentstatus/?tranID='.$orderid.'&status=success');
            }
        } else {
            // Failed
            $temp_cancel = MCheckout::cancelled_transaction($orderid);

            $data            = array();
            $data['message'] = '005';
            // $data['message'] = 'Apologies for payment transaction failed of your order.<br />Thank you for visiting.';
            // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
            return Redirect::to('checkout/paymentstatus/?tranID='.$orderid.'&status=failed');
        }

    }

    // Payment Gateway return
    public function anyPaymentstatus()
    {
        $tranID = trim(Input::get('tranID'));
        $status = trim(Input::get('status'));

        if ($status == "success") {
            $data['message'] = '001';
            $data['payment'] = 'JCSUCCESS successfully received';
            $bcardStatus     = PointModule::getStatus('bcard_earn');

            return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment'])->with('id', $tranID)->with('bcardStatus', $bcardStatus);
        } else {
            
            if($status == "pending"){
                
                $data['message'] = '004';
                $data['payment'] = 'JCPENDING pending';
                return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment'])->with('id', $tranID);
                
            }else{
                
                if($status == "pending"){
                    $data['message'] = '004';
                    $data['payment'] = 'JCFAIL Pending';
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment'])->with('id', $tranID);
                    
                }else{
                    if($status == "failed"){
                        return Redirect::to('http://tmgrocer.com/p_respond.php?tranID='.$tranID.'&status=failed');
                    }
                
                    $data['message'] = '005';
                    $data['payment'] = 'JCFAIL failed';
                    return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message'])->with('payment', $data['payment'])->with('id', $tranID);
                    
                }
            
            }
            
        }

    }

    public function anyMolxmlORI()
    {
        foreach ($_POST as $k => $v) {
            $tran_data[$k] = $v;
        }
 
        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_data_view')->with('data', json_encode($tran_data, JSON_UNESCAPED_SLASHES));

        
    }

    public function anyMolxml()
    {
        
        /*
         * ANDROID NATIVE CODE REQUIRE TO SEND TRANSACTION ID AND DEVICETYPE AS 'android'
         */
        if(isset($_POST['devicetype']) == 'android' ){ // ANDROID

            $conf      = MCheckout::molpay_conf();
            
            $molpay_verifykey = $conf['molpay_verifykey'];
            $molpay_merchant_id = $conf['molpay_merchant_id'];
            $molpay_url = $conf['molpay_url'];
            $molpay_returnurl = $conf['molpay_returnurl'];
            
            $transaction_id = $_POST['transaction_id'];
            
            $transactionAmount =self::getMolPayCheckoutDetails($transaction_id);
            $grand_amt = $transactionAmount['grandAmount'];
            $vcode = md5($grand_amt.$molpay_merchant_id.$transaction_id.(isset($molpay_verifykey) ? $molpay_verifykey : ''));
            
            $Transaction = Transaction::find($transaction_id);
            $UserInfo =  Customer::where("username",$Transaction->buyer_username)->first();;
            $bill_name = $UserInfo->full_name;    
            $bill_email = $UserInfo->email;      
            $bill_mobile = $UserInfo->mobile_no;   
            
            $tran_data =  array(
                "merchant_id"=>$molpay_merchant_id,
                "amount"=>$grand_amt,
                "orderid"=>$transaction_id,
                "country"=>"MY",
                "currency"=>"MYR",
                "returnurl"=>$molpay_returnurl,
                "cancelurl"=>$molpay_returnurl,
                "vcode"=>$vcode,
                "bill_name"=>$bill_name,
                "bill_email"=>$bill_email,
                "bill_mobile"=>$bill_mobile,
                "bill_desc"=>"Payment for JOCOM",
            );


        }else{ //IOS
            foreach ($_POST as $k => $v) {
                $tran_data[$k] = $v;
            }
        }
        
        
        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_data_view')->with('data', json_encode($tran_data, JSON_UNESCAPED_SLASHES));
    }

    
    
    public function anyMolxml2()
    {
        foreach ($_POST as $k => $v) {
            $tran_data[$k] = $v;
        }
       
       
        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_data_view')->with('data', json_encode($utfEncodedArray, JSON_UNESCAPED_SLASHES));
    }

    /**
     * For test email
     * @return [type] [description]
     */
    public function anyEmail($id = null)
    {
        if ($id != null) {
            //$row = Transaction::find(107);
            $row   = Transaction::find($id);
            $email = MCheckout::trans_complete_mailout($row);

            return Redirect::to('transaction/edit/'.$id)->with('success', 'Email sent!');
        }
    }

    /**
     * For clear checkout session, to test checkout
     * @return [type] [description]
     */
    public function anyClearsession()
    {
        //$row = Transaction::find(107);
        Session::forget('checkout_signal_check');
        Session::forget('checkout_transaction_id');
        Session::flush();

        return Redirect::to('test');
    }

    public function postBcardreward()
    {
        $data              = json_decode(json_encode(Input::all()));
        $bcard             = object_get($data, 'bcard');
        $transactionId     = object_get($data, 'transactionId');
        $transaction       = Transaction::find($transactionId);
        $coupon            = TCoupon::where('transaction_id', '=', $id)->first();
        $totalAmount       = $transaction->total_amount;
        $couponAmount      = $coupon->coupon_amount;
        $totalGst          = $transaction->gstTotal;
        $points            = TPoint::transactionAmount($transactionId);
        $totalPointsAmount = 0;

        $rewarded = BcardTransaction::where('bill_no', '=', 'T'.$transactionId)->first();

        if ( ! $rewarded) {
            foreach ($points as $point) {
                $totalPointsAmount += $point->amount;
            }

            $finalAmount = $totalAmount - $couponAmount + $totalGst - $totalPointsAmount;
            $earnRate    = object_get(PointType::find(PointType::BCARD), 'earn_rate', 1);
            $finalPoint  = floor($finalAmount * $earnRate);

            $config = array_merge(Config::get('points.bcard'), [
                'Card'        => $bcard,
                'TranxDate'   => date('Y-m-d\TH:i:s', strtotime($transaction->transaction_date)),
                'BillNo'      => 'T'.$transactionId,
                'TotalAmount' => $finalAmount,
                'TotalPoint'  => $finalPoint,
            ]);

            $response = Bcard::api('Reward', $config);

            if (object_get($response, 'status', 0) > 0) {
                $bcardTransaction            = new BcardTransaction;
                $bcardTransaction->action    = 'Earn';
                $bcardTransaction->api       = 'Reward';
                $bcardTransaction->point     = $finalPoint;
                $bcardTransaction->request   = json_encode($config);
                $bcardTransaction->response  = json_encode($response);
                $bcardTransaction->reward_id = object_get($response, 'RewardID');
                $bcardTransaction->bill_no   = 'T'.$transactionId;
                $bcardTransaction->save();

                return 'SUCCESS';
            } else {
                return object_get($response, 'ErrorMessage', 'Failed').'.';
            }
        }
    }

    private function pointBalance($transactionId)
    {
        $points = TPoint::where('transaction_id', '=', $transactionId)->get();

        if ( ! $points) {
            return;
        }

        $transaction = Transaction::findOrFail($transactionId);
        $coupon      = TCoupon::where('transaction_id', '=', $transactionId)->first();
        $total       = $transaction->total_amount + $transaction->gst_total - $coupon->coupon_amount;

        foreach ($points as $point) {
            $total -= $point->amount;
        }

        // User redeemed more than the amount they have to pay
        if ($total < 0) {
            // If user redeemed different types of point in transaction, release all
            if ($points->count() > 1) {
                foreach ($points as $point) {
                    $point->delete();
                }
            } else {
                // Release extra points
                // $type = PointType::findOrFail($point->point_type_id);

                // $point = $points->first();
                // $point->amount += $total;
                // $point->point += $total / $type->redeem_rate;
                // $point->save();
            }
        }
    }
    
    public function anyTestgetamount(){
        
        $transaction_id = 9355;
        $amountInfo = self::getMolPayCheckoutDetails($transaction_id);
                
        return $amountInfo;
        
    }
    
    private static function getMolPayCheckoutDetails($transaction_id){
        
        $transaction_id = $transaction_id;
        $transactionInfo = Transaction::find($transaction_id);

        $coupon_amount = 0;
        $point_amount = 0;
        $transactionCoupon = TCoupon::where("transaction_id",$transaction_id)->first();
        $transactionPoint = TPoint::where("transaction_id",$transaction_id)->first();
        
        if($transactionCoupon->coupon_code != ""){
            $coupon_amount = $transactionCoupon->coupon_amount;
        }
        
        if($transactionPoint->amount != ""){
            $point_amount = $transactionPoint->amount;
        }
        
        $grandAmount = (($transactionInfo->total_amount  + $transactionInfo->gst_total) - ($coupon_amount))- $point_amount;
        
        return array(
            "grandAmount" =>  number_format($grandAmount, 2, '.', ''),
            "processingFee" =>  number_format($transactionInfo->process_fees, 2, '.', ''),
            "deliveryCharges" =>  number_format($transactionInfo->delivery_charges, 2, '.', ''),
            "totalGST" =>  $transactionInfo->gst_total,
            "totalAmountPoint" =>  number_format($point_amount, 2, '.', ''),
            "totalAmountCoupon" =>  number_format($coupon_amount, 2, '.', ''),
        );
        
        
        
    } 
    
    
    public function anyBcardtest()
    {
        
        return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_bcard_test');

    }
    
    public function anyTest(){

        $return = self::getFlashcountupdate();
    }


    public function getFlashcountupdate()
    {

        $status = 'completed';    
        

        try{

        $result = DB::table('jocom_flashsale')
                        ->select('*')
                        ->where('status','=',1)
                        ->get();

            if(count($result)>0){

                 
                 foreach ($result as $row) {
                    $fld_id = $row->id;
                    $valid_from = $row->valid_from;
                    $valid_to = $row->valid_to;

                    $Prd_Result = DB::table('jocom_flashsale_products')
                                ->select('*')
                                ->where('fid','=',$fld_id)
                                ->get();

                    if(count($Prd_Result)>0){
                        foreach ($Prd_Result as $prd_row) {

                        $productid = $prd_row->product_id; 
                        $qtylimit =  $prd_row->limit_quantity; 
                        // echo 'In'.$productid.'-'.$qtylimit.'-'.$valid_from.'-'.$valid_to.'<br>';
                        // echo 'In';
                        $returnval= DB::table('jocom_transaction AS JT ')
                                ->select('JTD.product_id',DB::raw('SUM(JTD.unit) as TotalQuantity'))
                                ->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id', '=', 'JT.id')
                                ->where('JTD.product_id','=',$productid)
                                ->where('JT.transaction_date','>=',$valid_from)
                                ->where('JT.transaction_date','<=',$valid_to)
                                ->where('JT.status','=',$status)
                                ->groupby('JTD.product_id')
                                // ->distinct()
                                ->first();
                                // echo $returnval->TotalQuantity.'<br>';


                            if(count($returnval)>0) {
                                $fcount = 0;
                                $fcount = $fcount + $returnval->TotalQuantity;
                                // echo $fcount . '<br>';

                                if($fcount <= $qtylimit)
                                {
                                    $sql = DB::table('jocom_flashsale_products')
                                            ->where('fid','=',$fld_id)
                                            ->where('product_id','=',$productid)
                                            ->update(array('qty' => $fcount));
                                    // echo 'Updated';
                                }

                                
                            }  
                    }
                }
            }    
        }
        else{
            // echo 'No Flash sale found';
        }

        } catch (Exception $ex) {
            // echo $ex->getMessage();
        }

    }
    public function getCheckoutcoupon($transaction_id){
                    $today=date("Y-m-d");
                    $coupon_static=DB::table('jocom_static_coupon as sc')
                                   ->select('sc.id','sc.coupon_id','sc.coupon_code', 'sc.coupon_amount','sc.coupon_amount_type','sc.description','sc.status')
                                   ->join('jocom_coupon as jc ','sc.coupon_id','=','jc.id')
                                   ->where('jc.status','=','1')
                                   ->where('sc.status','=','1')
                                   ->whereDate('sc.from_date', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.to_date', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
                   $Transaction=Transaction::find($transaction_id);
                   $CustomerInfo = Customer::where('username',$Transaction->buyer_username)->first();
                  if($CustomerInfo->preferred_member==1){
                     $coupon=DB::table('jocom_coupon as sc')
                                  ->select('sc.id','sc.id as coupon_id','sc.coupon_code', 'sc.amount as coupon_amount','sc.amount_type as coupon_amount_type','sc.name as description','sc.status')
                                  ->where('sc.status','=','1')
                                  ->where('sc.is_preferred_member','=','1')
                                  ->where('sc.username','=','')
                                  ->whereDate('sc.valid_from', '<=', date_format(date_create($today), 'Y-m-d'))
                                  ->whereDate('sc.valid_to', '>=', date_format(date_create($today), 'Y-m-d'))
                                  ->orderBy('sc.id','DESC')
                                  ->get();
                    $usercoupons=DB::table('jocom_coupon as sc')
                                   ->select('sc.id','sc.id as coupon_id','sc.coupon_code', 'sc.amount as coupon_amount','sc.amount_type as coupon_amount_type','sc.name as description')
                                   ->where('sc.status','=','1')
                                   ->where('sc.is_preferred_member','=','1')
                                   ->where('sc.username','=',$Transaction->buyer_username)
                                   ->whereDate('sc.valid_from', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.valid_to', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
                                  
                                   if(!empty($usercoupons)){
                                       $users_totalcoupon=array_merge($coupon,$usercoupons);
                                       $coupon=$users_totalcoupon;
                                    }
                                  if(!empty($coupon)){
                                      foreach($coupon as $value){
                                          if($value->coupon_code=='PMPFD10'){
                                            $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                            $CustomerInfo = Customer::where('username',$Transaction->buyer_username)->first();
                                            $available_count='<br/>Minimum spend amount : RM'.$minmumcheck->min_purchase.'<br/>Coupon left: '.$CustomerInfo->membership_delivery;
                                          }else if($value->coupon_code=='PMP18'){
                                              $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                              $CustomerInfo = Customer::where('username',$Transaction->buyer_username)->first();
                                            $available_count='<br/>Minimum spend amount : RM'.$minmumcheck->min_purchase.'<br/> Coupon left: '.$CustomerInfo->member_disc_1; 
                                          }else if($value->coupon_code=='PMP25'){
                                              $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                             $CustomerInfo = Customer::where('username',$Transaction->buyer_username)->first();
                                            $available_count='<br/>Minimum spend amount : RM'.$minmumcheck->min_purchase.'<br/> Coupon left: '.$CustomerInfo->member_disc_2;   
                                          }else{
                                             $available_count='';  
                                          }
                                          $combained[]=array(
                                              'id'=>$value->id,
                                              'coupon_id'=>$value->coupon_id,
                                              'coupon_code'=>$value->coupon_code,
                                              'coupon_amount'=>$value->coupon_amount,
                                              'coupon_amount_type'=>$value->coupon_amount_type,
                                              'description'=>$value->description.$available_count,
                                              'status'=>$value->status,
                                              );
                                      }
                                     
                                      $coupon_merge=json_decode(json_encode($coupon_static), true);
                                      $checkoutcoupon=array_merge($coupon_merge,$combained);
                                      $result = array_map("unserialize", array_unique(array_map("serialize", $checkoutcoupon)));
                                      $coupon_static=(object) $result;
                                      $coupon_static=json_decode(json_encode($result), false);
                                  }
                    }
                    return $coupon_static; 
    }

    

}