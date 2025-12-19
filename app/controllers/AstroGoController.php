<?php

class AstroGoController extends BaseController {

	public function index() {
		return View::make('astrogo.index');
	}

	public function orders() {
		$orders = DB::table('jocom_astrogoshop_order')
					->select('id', 'order_number', 'customer_name', 'status', 'transaction_id');
		return Datatables::of($orders)->make(true);
	}

	public function upload() {

		if (Input::hasFile('csv_file')) {

			$csv_file = Input::file('csv_file');

			$destinationPath = storage_path() . '/astrogo';
			$filename = $destinationPath . '/a.csv';
			$csv_file->move($destinationPath, 'a.csv');
			$file = fopen($filename, 'r');

			$orders = array();
			$products = array();

			// $order[10] == Order Number
			// $order[13] == Product Name
			// $order[17] == Product QR code
			// $order[18] == Quantity
			// $order[19] == Customer Name
			// $order[21] == Customer Mobile Phone
			// $order[22] == Recipient Name
			// $order[24] == Recipient Mobile Phone
			// $order[25] == Post Code
			// $order[26] == Recipient Address
			// $order[27] == Delivery Message
			$data = fgetcsv($file, 1400, ",");
			while (($data = fgetcsv($file, 1400, ",")) !== FALSE) {
				$order = array();
				$order['order_number'] = trim($data[10]);

				$exist = DB::table('jocom_astrogoshop_order')->where('order_number', '=', $order['order_number'])->count();
				if ($exist > 0) {
					continue;
				}
				$order['customer_name'] = $data[19];
				$order['customer_mobile'] = $data[21];
				$order['qrcode'] = trim($data[17]);
				$order['product_name'] = $data[13];
				$order['quantity'] = $data[18];
				$order['delivery_name'] = $data[22];
				$order['delivery_contact_no'] = $data[24];
				$order['postcode'] = $data[25];
				$order['delivery_address'] = $data[26];
				$order['delivery_message'] = $data[27];
				$json = [
					'Delivery Status' => $data[0],
					'CN No.' => $data[1],
					'Courier' => $data[2],
					'Cancel Reason' => $data[3],
					'DN Status' => $data[4],
					'DN Status Code' => $data[5],
					'DN Detail Status Code' => $data[6],
					'DN Detail Status' => $data[7],
					'DN Additional Status' => $data[8],
					'DN No.' => $data[9],
					'Order No.' => $data[10],
					'Order Type' => $data[11],
					'Product Code' => $data[12],
					'Product Name' => $data[13],
					'Attributes Products Code' => $data[14],
					'Attributes Name' => $data[15],
					'Product Type' => $data[16],
					'Vendor Products Code' => $data[17],
					'Order Qty' => $data[18],
					'Customer Name' => $data[19],
					'Customer Phone No.' => $data[20],
					'Customer Mobile Phone' => $data[21],
					'Recipient Name' => $data[22],
					'Recipient Phone No.' => $data[23],
					'Recipient Mobile Phone' => $data[24],
					'Post Code' => $data[25],
					'Recipient Address' => $data[26],
					'Delivery Message' => $data[27],
					'ordSeq' => $data[28],
					'rcpCllYn' => $data[29]
				];
				$order['json'] = json_encode($json);
				array_push($orders, $order);
				array_push($products, $order['qrcode']);

			}

			unlink($filename);

			try {
				if (count($orders) == 0) {
					throw new Exception("No new order found", 1);
				}

				$priceOptionsObj = DB::table('jocom_product_price as price_options')
									->join('jocom_products as products', 'price_options.product_id', '=', 'products.id')
									->whereIn('products.qrcode', $products)
									->where('price_options.default', '=', 1)
									->select('price_options.id as price_option', 'products.qrcode')
									->get();

				$priceOptions = array();

				foreach ($priceOptionsObj as $option) {
					$priceOptions[$option->qrcode] = $option->price_option;
				}

				usort($orders, function($a, $b) {
					if ($a['order_number'] == $b['order_number']) {
						return $a['qrcode'] > $b['qrcode'];
					}
					return $a['order_number'] > $b['order_number'];
				});

				$mergedOrders = array();
				$currentOrder = array();

				foreach ($orders as $order) {
					if (count($currentOrder) == 0) {
						$currentOrder = $order;
					} else {
						if ($order['order_number'] == $currentOrder['order_number'] && $order['qrcode'] == $currentOrder['qrcode']) {
							$currentOrder['quantity'] += $order['quantity'];
						} else {
							$stateCity = $this->getStateCityIdByPostcode($currentOrder['postcode']);
							$currentOrder['price_option'] = $priceOptions[$currentOrder['qrcode']];

							$transactionDetails = array(
								'user'                => 'Astro Go Shop',             	// Buyer Username
				                'pass'                => '',             				// Buyer Password
				                'delivery_name'       => $currentOrder['delivery_name'],      // delivery name
				                'delivery_contact_no' => $currentOrder['delivery_contact_no'], // delivery contact no
				                'special_msg'         => $currentOrder['delivery_message'],       // special message
				                'delivery_addr_1'     => $currentOrder['delivery_address'],
				                'delivery_addr_2'     => '',
				                'delivery_postcode'   => $currentOrder['postcode'],
				                'delivery_city'       => $stateCity->city_id, // City ID 
				                'delivery_state'      => $stateCity->state_id,                     // State ID
				                'delivery_country'    => $stateCity->country_id,                 // Country ID
				                'astrogoDeliveryCharges'	=> 0,  
				                'devicetype'          => 'cms',
				                'uuid'                => NULL, // City ID
				                'lang'                => 'EN',
				                'ip_address'          => Request::getClientIp(),
				                'location'            => '',
				                'transaction_date'    => date("Y-m-d H:i:s"),
				                'charity_id'          => '',
				                'external_ref_number' => $currentOrder['order_number'],
							);
							$currentOrder['transaction_details'] = $transactionDetails;
							array_push($mergedOrders, $currentOrder);
							$currentOrder = $order;
						}
					}
				}
				$stateCity = $this->getStateCityIdByPostcode($currentOrder['postcode']);
				$currentOrder['price_option'] = $priceOptions[$currentOrder['qrcode']];

				$transactionDetails = array(
								'user'                => 'Astro Go Shop',             	// Buyer Username
				                'pass'                => '',             				// Buyer Password
				                'delivery_name'       => $currentOrder['delivery_name'],      // delivery name
				                'delivery_contact_no' => $currentOrder['delivery_contact_no'], // delivery contact no
				                'special_msg'         => $currentOrder['delivery_message'],       // special message
				                'delivery_addr_1'     => $currentOrder['delivery_address'],
				                'delivery_addr_2'     => '',
				                'delivery_postcode'   => $currentOrder['postcode'],
				                'delivery_city'       => $stateCity->city_id, // City ID 
				                'delivery_state'      => $stateCity->state_id,                     // State ID
				                'delivery_country'    => $stateCity->country_id,                 // Country ID
				                'astrogoDeliveryCharges'	=> 0,  
				                'devicetype'          => 'cms',
				                'uuid'                => NULL, // City ID
				                'lang'                => 'EN',
				                'ip_address'          => Request::getClientIp(),
				                'location'            => '',
				                'transaction_date'    => date("Y-m-d H:i:s"),
				                'charity_id'          => '',
				                'external_ref_number' => $currentOrder['order_number'],
							);
				$currentOrder['transaction_details'] = $transactionDetails;
				array_push($mergedOrders, $currentOrder);

				$transactions = array();
				$currentTrasaction = array();
				$currentTransactionOrderNo = $mergedOrders[0]['order_number'];
				$transaction = $mergedOrders[0]['transaction_details'];
				unset($mergedOrders[0]['transaction_details']);
				$transaction['order_number'] = $mergedOrders[0]['order_number'];
				$transaction['customer_name'] = $mergedOrders[0]['customer_name'];
				$transaction['orders'][] = $mergedOrders[0];
				$transaction['qrcode'][] = $mergedOrders[0]['qrcode'];
				$transaction['price_option'][] = $mergedOrders[0]['price_option'];
				$transaction['qty'][] = intval($mergedOrders[0]['quantity']);
				

				for ($i = 1; $i < count($mergedOrders); $i++) { 
					if ($currentTransactionOrderNo != $mergedOrders[$i]['order_number']) {
						array_push($transactions, $transaction);
						$transaction = $mergedOrders[$i]['transaction_details'];
						$transaction['order_number'] = $mergedOrders[$i]['order_number'];
						$transaction['customer_name'] = $mergedOrders[$i]['customer_name'];
					} 
					unset($mergedOrders[$i]['transaction_details']);
					$transaction['orders'][] = $mergedOrders[$i];
					$transaction['qrcode'][] = $mergedOrders[$i]['qrcode'];
					$transaction['price_option'][] = $mergedOrders[$i]['price_option'];
					$transaction['qty'][] = intval($mergedOrders[$i]['quantity']);
					$currentTransactionOrderNo = $mergedOrders[$i]['order_number'];
				}
				array_push($transactions, $transaction);

				$isError = false;

	        
	            DB::beginTransaction();
    
    			$transferedProcessOrder = array();
    			$manualProcessOrder = array();

    			foreach ($transactions as $transaction) {
    				$orders = $transaction['orders'];

    				$astrogoOrder = new AstroGoShopOrder;
	            	$astrogoOrder->order_number = $orders[0]['order_number'];
	            	$astrogoOrder->customer_name = $orders[0]['customer_name'];
	            	$astrogoOrder->customer_mobile = $orders[0]['customer_mobile'];
	            	$astrogoOrder->transaction_id = 0;
	            	$astrogoOrder->json_data = $orders[0]['json'];
	            	$astrogoOrder->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
	            	$astrogoOrder->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
	            	$astrogoOrder->from_account = 1;
	            	$astrogoOrder->save();

    				foreach ($orders as $order) {
    					$astrogoOrderDetails = new AstroGoShopOrderDetails;
		            	$astrogoOrderDetails->order_id = $astrogoOrder->id;
		            	$astrogoOrderDetails->product_name = $order['product_name'];
		            	$astrogoOrderDetails->jc_code = $order['qrcode'];
		            	$astrogoOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
		            	$astrogoOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
		            	$astrogoOrderDetails->save();
    				}

    				unset($transaction['orders']);
    				$data = MCheckout::checkout_transaction($transaction);

	            	if($data['status'] == "success") {
                        $transaction_id = $data["transaction_id"];

                        // PUSH TO SUCCESS LIST 
                        array_push($transferedProcessOrder, array(
                            "order_number" => $transaction['order_number'],
                            "buyername" => $transaction['customer_name'],
                            "transactionID" => $transaction_id
                        )); 

                        
                        // SAVE AS COMPLETED TRANSACTION //
                        $trans = Transaction::find($transaction_id);
                        $trans->status = 'completed';
                        $trans->insert_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $trans->modify_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $trans->modify_date = date("Y-m-d h:i:sa");
                        $trans->save();
                        // SAVE AS COMPLETED TRANSACTION //

                        // CREATE INV
                        MCheckout::generateInv($transaction_id, true);
                        // CREATE PO
                        MCheckout::generatePO($transaction_id, true);
                        // CREATE DO
                        MCheckout::generateDO($transaction_id, true);

                        $astrogoOrder->transaction_id = $transaction_id;
                        $astrogoOrder->status = 1;
                        $astrogoOrder->is_completed = 1;
                        $astrogoOrder->save();

                        // AUTOMATED LOG TO LOGISTIC APP
                        LogisticTransaction::log_transaction($transaction_id);

                    } else {
                        array_push($manualProcessOrder, array(
                            "order_number" => $transaction['order_number'],
                            "buyername" => $transaction['customer_name']
                        ));
                        
                    }
    			}

	            // MANUAL PROCESS HANDLING
                // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
                $subject = "Astro Go Shop Migration Notification";
                $recipient = array(
                    "email"=>Config::get('constants.AstroGoShopManagerEmail'),
                );
                $data = array(
                        'execution_datetime'      => date("Y-m-d H:i:s"),
                        'total_records'  => count($transactions),
                        'manual_process'  => count($manualProcessOrder),
                        'manual_order_list'  => $manualProcessOrder,
                        'transfered_orders'  => $transferedProcessOrder,
                        'acc_name'  => 'JOCOM',
                );

                Mail::queue('emails.astrogouploadreport', $data, function ($message) use ($recipient,$subject) {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($recipient['email'], $recipient['name'])
                            ->cc(Config::get('constants.AstroGoShopManagerEmailCC'))
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

	        } catch(Exception $ex) {
	            $isError = true;
	        } finally {
	            if ($isError) {
	                DB::rollback();
	            	if (count($orders) == 0) {
						return Response::json(array('status' => 404, 'message' => 'No new order.'));
	            	}
	                return Response::json(array('status' => 400, 'message' => 'Error'));
	            } else {
	                DB::commit();
	                return Response::json(array('status' => 200, 'message' => 'Upload success'));
	            }
	        }

		}
	}

	public static function getStateCityIdByPostcode($postcode) {
		return DB::table('postcode')
				->join('jocom_cities as cities', 'postcode.post_office', '=', 'cities.name')
				->join('jocom_country_states as states', 'cities.state_id', '=', 'states.id')
				->select('cities.id as city_id', 'cities.name as city_name', 'cities.state_id as state_id', 'states.name as state_name', 'states.country_id')
				->where('postcode.postcode', '=', $postcode)
				->first();
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

}