<?php

class NinjavanController extends BaseController
{

	const NINJAVAN_URL_AUTHENTICATION = '/2.0/oauth/access_token';
	const NINJAVAN_URL_CREATE_ORDER = '/4.1/orders';
	const NINJAVAN_URL_GETWAYBILL = '/2.0/reports/waybill?tids=';
    const NINJAVAN_URL_DELETE_ORDER = '/2.2/orders/';

	public function Index()
    {
        //  $cust       = Customer::all();
        // $total      = count($cust);
        //  $cust       = Customer::count();
        
        // $total_cust 	= Customer::TotalCustomer();
        
// echo $cust;
        echo "Under development...";
    }


	public function getToken(){


		$data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";



        try {
        		ob_clean(); 

        		if(Config::get('constants.ENVIRONMENT') == 'live'){
        			$countryCode = 'MY';
        			$clientid 		= Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_ID');
        			$clientsecret	= Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_SECRET');
		            // $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.'/2.0/oauth/access_token'; 
		            $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.self::NINJAVAN_URL_AUTHENTICATION; 
		        }else{
		        	$countryCode = 'SG';
		        	$clientid 		= Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_ID');
        			$clientsecret	= Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_SECRET');
		            $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.self::NINJAVAN_URL_AUTHENTICATION; 
		            // $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.'/2.0/oauth/access_token'; 
		        }	

		        $post_fields = array(
		            "client_id" =>  $clientid,
		            "client_secret" => $clientsecret ,
		            "grant_type" => 'client_credentials',
		           
		        );
		        // echo '<pre>';
        		// print_r($post_fields); 
        		// echo $URL;
        		// echo '</pre>';

        		$post_fields = json_encode($post_fields);

        		
        		$ch = curl_init();

				curl_setopt($ch, CURLOPT_URL, $URL);
				
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
				curl_setopt($ch, CURLOPT_HEADER, FALSE);
				curl_setopt($ch, CURLOPT_POST, TRUE);
				curl_setopt($ch, CURLOPT_POSTFIELDS,$post_fields);
				// curl_setopt($ch, CURLOPT_POSTFIELDS, "{
				// 					  \"client_id\": \"$clientid\",
				// 					  \"client_secret\": \"$clientsecret\",
				// 					  \"grant_type\": \"client_credentials\"
				// 					}");
				curl_setopt($ch, CURLOPT_HTTPHEADER, array(
								  "Content-Type: application/json",
								  "Accept: application/json"
								));	
				$response = curl_exec($ch);


				curl_close($ch);

				$results = json_decode($response, TRUE);

				
        	 } catch (Exception $ex) {
        	 	echo $ex->getMessage();
                    } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        	}

        return $results;	

	}


	public function createOrder(){

			$data = array();
	        $RespStatus = 1; 
	        $message = "";
	        $errorCode = "";
	        $is_error = false;
	        $error_line = "";

	        try{
	        		DB::beginTransaction();

	        		// print_r(Input::all());

	        		$token = self::getToken();

	        	


	        		$trans_id   = Input::get('trans_id');
            		$logistic_id   = Input::get('logistic_id');
            		$parcelsize   = Input::get('parcelsize');
            		$parcelweight   = Input::get('parcelweight');
            		$parcellength   = Input::get('parcellength');
            		$parcelwidth   = Input::get('parcelwidth');
            		$parcelheight   = Input::get('parcelheight');
            		$logistic_log_id   = Input::get('logistic_log_id');
            		$quantity   = Input::get('quantity');

            		$courier_id = 5;
            	
		            $assignShipper = self::assignShipper($logistic_log_id, $quantity,$courier_id,$trans_id,$parcelsize,$parcelweight,$parcelwidth,$parcelheight);
		            $data['response'] = $assignShipper;


	        		 // print_r($assignShipper);


	         } catch (Exception $ex) {
           		echo  $ex->getMessage();
             } finally {
	            if ($is_error) {
	                DB::rollback();
	            } else {
	                DB::commit();
	            }
        	}


			$response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        	return $response;



	}

	public static function assignShipper($logistic_transaction_item_id,$quantity,$courier_id,$trans_id,$parcelsize,$parcelweight,$parcelwidth,$parcelheight) {


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
              
                case 'NINJA':
                    // FOR LINE CLEAR
                    $api_response = self::assignToNinjavan($ItemDetailsID,$quantity,$trans_id,$parcelsize,$parcelweight,$parcelwidth,$parcelheight);
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
                $data['tracking'] = $api_response['tracking_number'];
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


	public function assignToNinjavan($logistic_item_id,$quantity,$trans_id,$parcelsize,$parcelweight,$parcelwidth,$parcelheight) {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
    

        try {
            
            
            DB::beginTransaction();
            
            $Courier = Courier::getCourierByCode('NINJA');
            $LogisticTItem = LogisticTItem::find($logistic_item_id);

            $LogisticTransaction = LogisticTransaction::find($LogisticTItem->logistic_id);

            $ElevenStreetOrder = ElevenStreetOrder::where('transaction_id',$LogisticTransaction->transaction_id)->first();
            
            $doNo = $LogisticTransaction->do_no;
          
            // $AWB_No = str_replace('DO-','JCM',$LogisticTransaction->do_no).self::generateRandomString(2); // will be DO Number without '-'
           
            
            $wayBillOrderResponse = self::generateWaybillOrder($logistic_item_id,$quantity,$trans_id,$parcelsize,$parcelweight,$parcelwidth,$parcelheight);
   
            $wayBillOrder = $wayBillOrderResponse['response'];
           
            if($wayBillOrder['tracking_number'] != ''){
                // Add to shipper list
                $CourierOrder = new CourierOrder();
                $CourierOrder->courier_id = $Courier->id;
                $CourierOrder->transaction_item_logistic_id = $logistic_item_id;
                $CourierOrder->batch_id = '';
                $CourierOrder->response_message = 'API NINJAVAN';
                $CourierOrder->api_post =  json_encode($wayBillOrderResponse);
                $CourierOrder->reference_number = $trans_id;
                $CourierOrder->tracking_no = $wayBillOrder['tracking_number'];
                $CourierOrder->quantity = $quantity;
                $CourierOrder->remarks = '';
                $CourierOrder->created_by = Session::get('username');
                $CourierOrder->updated_by = Session::get('username');
                $CourierOrder->status = 1;
                $CourierOrder->save();

                $data['api_response'] = $wayBillOrderResponse['response'];
                $data['courier_order_id'] = $CourierOrder->id;
                $data['tracking_number'] = $wayBillOrder['tracking_number'];
                $data['api_post'] = '';
                $data['api_status'] = 1;
        
            }else{
                $data['api_response'] = $wayBillOrderResponse['response'];
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


	public static function generateWaybillOrder($logistic_item_id,$quantity,$trans_id,$parcelsize,$parcelweight,$parcelwidth,$parcelheight){

		$weight = 0;
		$frmflag = 0; 
        $data = "";

        $frmaddr1 = "";
        $frmaddr2 = "";
        $frmcity = ""; 
        $frmstate = ""; 
        $frmcountry = "";
        $frmPostal = "";

       
        $LogisticTItem = LogisticTItem::find($logistic_item_id);
        $LogisticTransaction = LogisticTransaction::find($LogisticTItem->logistic_id);
        $Price = Price::find($LogisticTItem->product_price_id);
        $quantity = $quantity; //$LogisticTItem->qty_order;

        $trans = Transaction::find($trans_id);

        $buyer = Customer::where('username', '=', $trans->buyer_username)->first();


        if(count($buyer)>0){

        		if(isset($buyer->postcode) && $buyer->postcode !="" && $buyer->address1 != "") {

        				$buycountry = "";
			  			if($buyer->country == ""){
			  				$buycountry = "Malaysia";
			  			}
			  			else {
			  				$buycountry = $buyer->country;
			  			}

			  			$frmflag = 1; 
			  			$frmaddr1 = $buyer->address1;
			  			$frmaddr2 = $buyer->address2;
			  			$frmPostal = $buyer->city;
			  			$frmPostal = $buyer->state;
				        $frmcountry = self::getCountrycode($buycountry);
				        $frmPostal = $buyer->postcode;
				        


        		}


        }

        if(isset($frmflag) && $frmflag == 0) {

        $fav_addr = DB::table('jocom_fav_address')
        				->where('user_id','=',$trans->buyer_id)
        				->where('default','=',1)
        				->first();
  		
	  		if(count($fav_addr)>0){
	  			$favcountry = "";
	  			if($fav_addr->country == ""){
	  				$favcountry = "Malaysia";
	  			}
	  			else {
	  				$favcountry = $fav_addr->country;
	  			}

	  			$frmflag = 1; 
	  			$frmaddr1 = $fav_addr->address1;
	  			$frmaddr2 = $fav_addr->address1;
		        $frmcountry = self::getCountrycode($favcountry);
		        $frmPostal = $fav_addr->postcode;


	  		}      

  		}

  		if(isset($frmflag) && $frmflag == 0) {

  			$frmbuyer = Customer::where('username', '=', $trans->buyer_username)->first();

  			if(count($frmbuyer)>0){



  				$frmflag = 1; 

	  			$frmaddr1 	= $frmbuyer->address1;
		        $frmcountry = $frmbuyer->country;
		        $frmPostal 	= $frmbuyer->postcode;
		        // $frmstate 	= 
		        // $frmcountry	=
		        // $frmPostal 	= 

  			}

  		}	


        

        if(isset($frmflag) && $frmflag == 0) {

        				$frmflag = 1; 
			  			$frmaddr1 = 'No.36A, Jalan Ipoh Batu 8,';
			  			$frmaddr2 = 'Kompleks Selayang, 68100 Batu Caves';
			  			$frmcity = 'Batu Caves';
			  			$frmstate = 'Selangor';
				        $frmcountry = 'MY';
				        $frmPostal = '68100';      	

        }
       



        $fromAddress = array('address1' => $frmaddr1, 
        					 'address2' => $frmaddr2, 
        					 'area' => '',
        					 'city' => $frmcity, 
        					 'state' => $frmstate, 
        					 'country' => $frmcountry, 
        					 'postcode' => $frmPostal, 

        					);



        $from = array('name' => $buyer->full_name,
        					 'phone_number' => $buyer->mobile_no,
        					 'email' => $buyer->email,
        					 'address' => $fromAddress,
        			   );


        	$tocountry = "";
	  			if($trans->delivery_country == ""){
	  				$tocountry = "Malaysia";
	  			}
	  			else {
	  				$tocountry = $trans->delivery_country;
	  			}

        $toAddress  = array('address1' => $trans->delivery_addr_1, 
        					'address2' => $trans->delivery_addr_2, 
        					'kelurahan' => '', 
        					'kecamatan' => '', 
        					'city' => $trans->delivery_city, 
        					'province' => $trans->delivery_state, 
        					'country' => self::getCountrycode($tocountry), 
        					'postcode' => $trans->delivery_postcode, 


        				);

        $to = array('name' => $trans->delivery_name,
        			'phone_number' => $trans->delivery_contact_no,
        			'email' => $trans->buyer_email,
        			'address' => $toAddress,

        			   );

        $pickupslot = array('start_time' => '09:00', 
        					'end_time' => '18:00', 
        					'timezone' => 'Asia/Kuala_Lumpur'
        					);

        


        $deliverytimezone = "";

        switch ($trans->delivery_country) {
        	case 'Malaysia':
        		 $deliverytimezone   = 'Asia/Kuala_Lumpur';
        		break;

        	case 'Singapore':
        		 $deliverytimezone   = 'Asia/Singapore';
        		break;

        	case 'Indonesia':
        		 $deliverytimezone   = 'Asia/Jakarta';
        		break;
        	
        	case 'Thailand':
        		 $deliverytimezone   = 'Asia/Bangkok';
        		break;

        	default:
        		$deliverytimezone   = 'Asia/Kuala_Lumpur';
        		break;
        }


         $deliveryslot = array('start_time' => '09:00', 
	        					'end_time' => '22:00', 
	        					'timezone' => $deliverytimezone
	        					);

         $dimensions = array('size' 	=> $parcelsize, 
         					 'weight' 	=> $parcelweight, 
         					 'length' 	=> $parcellength, 
         					 'width' 	=> $parcelwidth, 
         					 'height' 	=> $parcelheight

         					);


        $pickupaddress = array('name' => 'Asif', 
        					   'phone_number' => '+60163273545', 
        					   'email' => 'asif@jocom.my',

        					);

        $tmpDate = date('Y-m-d');
        $i = 1;
		$nextBusinessDay = date('Y-m-d', strtotime($tmpDate . ' +' . $i . ' Weekday'));

        $parceljob = array('is_pickup_required' => true,
        					'pickup_address_id' => self::Uniqids(),
        					'pickup_service_type' => 'Scheduled',
        					'pickup_service_level'  => 'Standard', 
        					'pickup_date' => $nextBusinessDay,
        					'pickup_timeslot' => $pickupslot,
        					'pickup_approx_volume'  => 'Less than 3 Parcels',
        					'pickup_instructions'  => 'Pickup with care!',
        					'delivery_instructions'  => $trans->special_msg, 
        					'delivery_start_date'  => $nextBusinessDay,
        					'delivery_timeslot' => $deliveryslot,
        					'dimensions' => $dimensions


        				 );

        $AWB_No = str_replace('DO-','SHIP-JCM',$trans->do_no).self::generateRandomString(2); // will be DO Number without '-'
        $reference = array('merchant_order_number' => $AWB_No );


        $data = array(
		            "service_type" =>  "Parcel",
		            "service_level" => "Standard",
		            "requested_tracking_number" => 'JOCOM-'.sprintf("%06d", $trans_id),
		            "reference" => $reference,
		            "from" => $from,
		            "to"   => $to,	
		            "parcel_job" => $parceljob
		           
		        );

        $data = json_encode($data);

        $token = self::getToken();


		if(Config::get('constants.ENVIRONMENT') == 'live'){
			$countryCode = 'MY';
			$clientid 		= Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_ID');
			$clientsecret	= Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_SECRET');
	        // $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.'/2.0/oauth/access_token'; 
	        $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.self::NINJAVAN_URL_CREATE_ORDER; 
	    }else{
	    	$countryCode = 'SG';
	    	$clientid 		= Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_ID');
			$clientsecret	= Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_SECRET');
	        $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.self::NINJAVAN_URL_CREATE_ORDER; 
	        // $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.'/2.0/oauth/access_token'; 
	    }	

	    ob_clean(); 

        $ch = curl_init();

		curl_setopt($ch, CURLOPT_URL, $URL);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);

		curl_setopt($ch, CURLOPT_POST, TRUE);

		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						  "Content-Type: application/json",
						  "Accept: application/json",
						  "Authorization: ".$token['token_type'].' '.$token['access_token']
						   // "Authorization: bearer C2Y6iFjnfvwagVG3lFD8vAbj4QpfGrjYhuUU0aUd"
						));	

		$response = curl_exec($ch);


		curl_close($ch);

		$results = json_decode($response, TRUE);


		return array("response"=>$results );

	}


	public function generateWaybill($tracking_no){

		$data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
    

        // try {
            
            
        //     DB::beginTransaction();
            $token = self::getToken();
             // print_r($token); 

            if(Config::get('constants.ENVIRONMENT') == 'live'){
			$countryCode = 'MY';
			$clientid 		= Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_ID');
			$clientsecret	= Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_SECRET');
		        // $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.'/2.0/oauth/access_token'; 
		        $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.self::NINJAVAN_URL_GETWAYBILL.$tracking_no.'&h=0'; 
		    }else{
		    	$countryCode = 'SG';
		    	$clientid 		= Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_ID');
				$clientsecret	= Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_SECRET');
		        $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.self::NINJAVAN_URL_GETWAYBILL.$tracking_no.'&h=0'; 
		        // $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.'/2.0/oauth/access_token'; 
		    }	

		    ob_clean(); 
            // echo $URL .'<br>';

            $ch = curl_init();

			curl_setopt($ch, CURLOPT_URL, $URL);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, FALSE);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
						  "Content-Type: application/json",
						  "Accept: application/json",
                         "cache-control: no-cache",
						 "Authorization: ".$token['token_type'].' '.$token['access_token']
						   // "Authorization: bearer C2Y6iFjnfvwagVG3lFD8vAbj4QpfGrjYhuUU0aUd"
						));	

			$response = curl_exec($ch);

			curl_close($ch);

            $filename=$tracking_no.date('mdyhis').'.pdf';

            header('Content-Description: File Transfer'); 
            header('Content-Type: application/pdf'); 
            header('Content-Disposition: attachment; filename="'.$filename.'"'); 
            header('Content-Transfer-Encoding: binary'); 
            header('Expires: 0'); 
            header('Cache-Control: must-revalidate, post-check=0, pre-check=0'); 
            header('Pragma: public'); 
            // header('Content-Length: ' . filesize($response)); 
            ob_clean(); 
            flush(); 
            readfile(dd($response));     

            exit();

	}


    public function cancelOrder($batch_id){


        $data = array();
        $respStatus = 0; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {

        $Batch = LogisticBatch::where('id','=',$batch_id)
                                ->orderby('assign_date','DESC')
                                ->first();

        if(count($Batch) > 0) {

            $tracking_no = $Batch->tracking_number;
            $token = self::getToken();

            if(Config::get('constants.ENVIRONMENT') == 'live'){
                $countryCode = 'MY';
                $clientid       = Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_ID');
                $clientsecret   = Config::get('constants.NINJAVAN_ENV_PRO_CLIENT_SECRET');
                // $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.'/2.0/oauth/access_token'; 
                $URL = Config::get('constants.NINJAVAN_ENV_PRO').'/'.$countryCode.self::NINJAVAN_URL_DELETE_ORDER.$tracking_no; 
            }else{
                $countryCode = 'SG';
                $clientid       = Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_ID');
                $clientsecret   = Config::get('constants.NINJAVAN_ENV_DEV_CLIENT_SECRET');
                $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.self::NINJAVAN_URL_DELETE_ORDER.$tracking_no; 
                // $URL = Config::get('constants.NINJAVAN_ENV_DEV').'/'.$countryCode.'/2.0/oauth/access_token'; 
            }


            $ch = curl_init();

            curl_setopt($ch, CURLOPT_URL, $URL);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");

            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
              "Content-Type: application/json",
              "Accept: application/json",
              "Authorization: ".$token['token_type'].' '.$token['access_token']
              // "Authorization: bearer C2Y6iFjnfvwagVG3lFD8vAbj4QpfGrjYhuUU0aUd"
            ));

            $response = curl_exec($ch);
            curl_close($ch);

            $respStatus = 1; 

        }







        } catch (Exception $ex) {
            
            } finally {
                if ($is_error) {
                    DB::rollback();
                } else {
                    DB::commit();
                }
        }


      return $respStatus;

    } 


	public static function Uniqids() {
		$chars = "113232313232123232123456789";
		srand((double)microtime()*1000000);
		$i = 0;
		$pass = '' ;
		while ($i <= 10) {
		$num = rand() % 33;
		$tmp = substr($chars, $num, 1);
		$pass = $pass . $tmp;
		$i++;
		}
		return $pass;
	}


	public static function rnd_number(){

        $random_id_length = 10; 
        //generate a random id and store it in $rnd_id 
        //$rnd_id = crypt(uniqid(rand(),1)); 
        $rnd_id = uniqid(rand(),1); 
        //to remove any slashes that might have come 
        $rnd_id = strip_tags(stripslashes($rnd_id)); 
        //Removing any . or / and reversing the string 
        $rnd_id = str_replace(".","",$rnd_id); 
        $rnd_id = strrev(str_replace("/","",$rnd_id)); 
        $rnd_id = strrev(str_replace("a","",$rnd_id)); 
        //finally I take the first 10 characters from the $rnd_id 
        $rnd_id = substr($rnd_id,0,$random_id_length); 
        
        return $rnd_id;

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


	public function getCountrycode($country) {

		$response = ""; 

		if(strtoupper($country) == 'MALAYSIA') {

			$response = 'MY'; 
		}
		else if(strtoupper($country) == 'SINGAPORE') {

			$response = 'SG'; 
		}
		else {

			$response = 'MY'; 
		}

		return $response;

	}




}