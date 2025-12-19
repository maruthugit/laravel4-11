<?php

class ShopeeManualController extends BaseController {

	public function index() {
		return View::make('shopeemanual.index');
	}

	public function orders() {
		$orders = DB::table('jocom_shopee_order')
					->select('id', 'ordersn', 'name', 'status', 'transaction_id')->where('manual','=',1);
		return Datatables::of($orders)->make(true);
	}

	public function upload() {

		if (Input::hasFile('csv_file')) {

			// echo '<pre>';
			// print_r(Input::all());
			// echo '</pre>';
			// die();
			$from_account = Input::get('account_type');
			$csv_file = Input::file('csv_file');

			$destinationPath = storage_path() . '/shopeemanual';
			$filename = $destinationPath . '/a.csv';
			$csv_file->move($destinationPath, 'a.csv');
			$file = fopen($filename, 'r');

			$orders = array();
			$products = array();
			$tax_rate = Fees::get_tax_percent();
			
			$data = fgetcsv($file, 1400, ",");

					

			while (($data = fgetcsv($file, 1400, ",")) !== FALSE) {
				
				$order = array();
				$order['ordersn'] = trim($data[0]);

				// $exist = DB::table('jocom_shopee_order')->where('ordersn', '=', $order['ordersn'])->count();
				// if ($exist > 0) {
				// 	continue;
				// }
				$order['name'] = $data[43];
				$order['phone'] = $data[44];
				$order['qrcode'] = trim($data[12]);
				$order['product_name'] = $data[11];
				$order['quantity'] = $data[16];
				$order['delivery_name'] = $data[43];
				$order['delivery_contact_no'] = $data[44];
				$order['postcode'] = $data[51];
				$order['delivery_address'] = $data[45];
				$order['delivery_message'] = $data[52];
				$order['estimated_shipping_fee'] = $data[41];


				$json = [
					'Order ID' => $data[0],
					'Order Status' => $data[1],
					'Return / Refund Status' => $data[2],
					'Tracking Number' => $data[3],
					'Shipping Option' => $data[4],
					'Shipment Method' => $data[5],
					'Estimated Ship Out Date' => $data[6],
					'Ship Time' => $data[7],
					'Order Creation Date' => $data[8],
					'Order Paid Time' => $data[9],
					'Parent SKU Reference No.' => $data[10],
					'Product Name' => $data[11],
					'SKU Reference No.' => $data[12],
					'Variation Name' => $data[13],
					'Original Price' => $data[14],
					'Deal Price' => $data[15],
					'Quantity' => $data[16],
					'Product Subtotal' => $data[17],
					'Seller Rebate' => $data[18],
					'Seller Discount' => $data[19],
					'Shopee Rebate' => $data[20],
					'SKU Total Weight' => $data[21],
					'No of product in order' => $data[22],
					'Order Total Weight' => $data[23],
					'Voucher Code' => $data[24],
					'Seller Voucher' => $data[25],
					'Seller Absorbed Coin Cashback' => $data[26],
					'Shopee Voucher' => $data[27],
					'Bundle Deal Indicator' => $data[28],
					'Shopee Bundle Discount' => $data[29],
					'Seller Bundle Discount' => $data[30],
					'Shopee Coins Offset' => $data[31],
					'Credit Card Discount Total' => $data[32],
					'Total Amount' => $data[33],
					'Buyer Paid Shipping Fee' => $data[34],
					'Shipping Rebate Estimate' => $data[35],
					'Reverse Shipping Fee' => $data[36],
					'Transaction Fee' => $data[37],
					'Commission Fee' => $data[38],
					'Service Fee' => $data[39],
					'Grand Total' => $data[40],
					'Estimated Shipping Fee' => $data[41],
					'Username (Buyer)' => $data[42],
					'Receiver Name' => $data[43],
					'Phone Number' => $data[44],
					'Delivery Address' => $data[45],
					'Town' => $data[46],
					'District' => $data[47],
					'Area' => $data[48],
					'State' => $data[49],
					'Country' => $data[50],
					'Zip Code' => $data[51],
					'Remark from buyer' => $data[52],
					'Order Complete Time' => $data[53],
					'Note' => $data[54]



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
					if ($a['ordersn'] == $b['ordersn']) {
						return $a['qrcode'] > $b['qrcode'];
					}
					return $a['ordersn'] > $b['ordersn'];
				});

				// echo '<pre>';
				// print_r($orders);
				// echo '</pre>';
				// die();


				$mergedOrders = array();
				$currentOrder = array();

				foreach ($orders as $order) {
					
					$zeroDelivery = 0;

					if($order['estimated_shipping_fee'] == 0){
                        $zeroDelivery = 1;
                    }

                    $shopeeDeliveryCharges = 0;  

                    $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($order['estimated_shipping_fee'] * (100/(100 + $tax_rate)));

					if (count($currentOrder) == 0) {
						$currentOrder = $order;
					} else {
						if ($order['ordersn'] == $currentOrder['ordersn'] && $order['qrcode'] == $currentOrder['qrcode']) {
							$currentOrder['quantity'] += $order['quantity'];
						} else {
							$stateCity = $this->getStateCityIdByPostcode($currentOrder['postcode']);
							$currentOrder['price_option'] = $priceOptions[$currentOrder['qrcode']];

							$transactionDetails = array(
								'user'                => 'shopee',             	// Buyer Username
				                'pass'                => '',             				// Buyer Password
				                'delivery_name'       => $currentOrder['delivery_name'],      // delivery name
				                'delivery_contact_no' => $currentOrder['delivery_contact_no'], // delivery contact no
				                'special_msg'         => 'Transaction transfer from Shopee Manual ( Order Number : '.$currentOrder['ordersn'].' )'. " ".$currentOrder['delivery_message'], // special message
				                'delivery_addr_1'     => $currentOrder['delivery_address'],
				                'delivery_addr_2'     => '',
				                'delivery_postcode'   => $currentOrder['postcode'],
				                'delivery_city'       => $stateCity->city_id, // City ID 
				                'delivery_state'      => $stateCity->state_id,                     // State ID
				                'delivery_country'    => $stateCity->country_id,                 // Country ID
				                'shopeeDeliveryCharges'=> $shopeeDeliveryCharges,  
				                'devicetype'          => 'cms',
				                'uuid'                => NULL, // City ID
				                'lang'                => 'EN',
				                'ip_address'          => Request::getClientIp(),
				                'location'            => '',
				                'transaction_date'    => '2022-01-25 00:00:00',//date("Y-m-d H:i:s"),
				                'charity_id'          => '',
				                'external_ref_number' => $currentOrder['ordersn'],
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
								'user'                => 'shopee',             	// Buyer Username
				                'pass'                => '',             				// Buyer Password
				                'delivery_name'       => $currentOrder['delivery_name'],      // delivery name
				                'delivery_contact_no' => $currentOrder['delivery_contact_no'], // delivery contact no
				                'special_msg'         => 'Transaction transfer from Shopee Manual ( Order Number : '.$currentOrder['ordersn'].' )'. " ".$currentOrder['delivery_message'], // special message
				                'delivery_addr_1'     => $currentOrder['delivery_address'],
				                'delivery_addr_2'     => '',
				                'delivery_postcode'   => $currentOrder['postcode'],
				                'delivery_city'       => $stateCity->city_id, // City ID 
				                'delivery_state'      => $stateCity->state_id,                     // State ID
				                'delivery_country'    => $stateCity->country_id,                 // Country ID
				                'shopeeDeliveryCharges'=> $shopeeDeliveryCharges,  
				                'devicetype'          => 'cms',
				                'uuid'                => NULL, // City ID
				                'lang'                => 'EN',
				                'ip_address'          => Request::getClientIp(),
				                'location'            => '',
				                'transaction_date'    => '2022-01-25 00:00:00',//date("Y-m-d H:i:s"),
				                'charity_id'          => '',
				                'external_ref_number' => $currentOrder['ordersn'],
							);
				$currentOrder['transaction_details'] = $transactionDetails;
				array_push($mergedOrders, $currentOrder);


				// echo '<pre>';
				// print_r($mergedOrders);
				// echo '</pre>';
				// die();

				$transactions = array();
				$currentTrasaction = array();
				$currentTransactionOrderNo = $mergedOrders[0]['ordersn'];
				$transaction = $mergedOrders[0]['transaction_details'];
				unset($mergedOrders[0]['transaction_details']);
				$transaction['ordersn'] = $mergedOrders[0]['ordersn'];
				$transaction['customer_name'] = $mergedOrders[0]['name'];
				$transaction['orders'][] = $mergedOrders[0];
				$transaction['qrcode'][] = $mergedOrders[0]['qrcode'];
				$transaction['price_option'][] = $mergedOrders[0]['price_option'];
				$transaction['qty'][] = intval($mergedOrders[0]['quantity']);
				

				for ($i = 1; $i < count($mergedOrders); $i++) { 
					if ($currentTransactionOrderNo != $mergedOrders[$i]['ordersn']) {
						array_push($transactions, $transaction);
						$transaction = $mergedOrders[$i]['transaction_details'];
						$transaction['ordersn'] = $mergedOrders[$i]['ordersn'];
						$transaction['customer_name'] = $mergedOrders[$i]['name'];
					} 
					unset($mergedOrders[$i]['transaction_details']);
					$transaction['orders'][] = $mergedOrders[$i];
					$transaction['qrcode'][] = $mergedOrders[$i]['qrcode'];
					$transaction['price_option'][] = $mergedOrders[$i]['price_option'];
					$transaction['qty'][] = intval($mergedOrders[$i]['quantity']);
					$currentTransactionOrderNo = $mergedOrders[$i]['ordersn'];
				}
				array_push($transactions, $transaction);

				$isError = false;


				// echo '<pre>';
				// print_r($transactions);
				// echo '</pre>';
				// die();

	        
	            // DB::beginTransaction();
    
    			$transferedProcessOrder = array();
    			$manualProcessOrder = array();

    			foreach ($transactions as $transaction) {
    				$orders = $transaction['orders'];
    	// 			echo '<pre>';
					// print_r($orders);
					// echo '</pre>';

    				
    				// echo $orders[0]['ordersn'] . '<br>';
    				$orderschecheck = ShopeeOrder::where('ordersn','=', $orders[0]['ordersn'])->first();
    				if (empty($orderschecheck)) 
                	{

                	$ordersn_1 = $orders[0]['ordersn'];	

                	// echo $ordersn_1 .$orders[0]['name'].'<br>';
                		
                	$Shopee = new ShopeeOrder;
                    $Shopee->ordersn = $orders[0]['ordersn'];
                    $Shopee->name = $orders[0]['name'];
                    $Shopee->phone = $orders[0]['phone'];
                    $Shopee->transaction_id = 0;
                    $Shopee->migrate_from = "Shopee";
                    $Shopee->is_completed = 1;
                    $Shopee->from_account = $from_account;
                    $Shopee->manual = 1;
                    $Shopee->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                    $Shopee->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";

                    $Shopee->save();

                    $OrderID = $Shopee->id;

                    foreach ($orders as $order) {
                    
                        $ShopeeOrderDetails = new ShopeeOrderDetails;
                        $ShopeeOrderDetails->order_id = $OrderID;
                        $ShopeeOrderDetails->ordersn = $ordersn_1;
                        $ShopeeOrderDetails->item_name = $order['product_name'];
                        $ShopeeOrderDetails->item_sku = $order['item_sku'];
                        $ShopeeOrderDetails->api_result_return = json_encode($order['json']);
                        $ShopeeOrderDetails->api_result_full = json_encode($order['json']);
                        $ShopeeOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $ShopeeOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $ShopeeOrderDetails->save();

                    }


    				unset($transaction['orders']);

    				// echo '<pre>New';
    				// print_r($transaction); 
    				// echo '</pre>';

    				

    				$data = MCheckout::checkout_transaction($transaction);

    				//                 echo '<pre>';
    				// print_r($data); 
    				// echo '</pre>';

	            	if($data['status'] == "success") {
                        $transaction_id = $data["transaction_id"];

                        // PUSH TO SUCCESS LIST 
                        array_push($transferedProcessOrder, array(
                            "ordersn" => $transaction['	'],
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

                        $Shopee->transaction_id = $transaction_id;
                        $Shopee->status = 1;
                        $Shopee->is_completed = 1;
                        $Shopee->save();

                        // AUTOMATED LOG TO LOGISTIC APP
                        LogisticTransaction::log_transaction($transaction_id);

                        $logtrans = LogisticTransaction::where('transaction_id','=',$transaction_id)->first();
                        $logtrans->status = 5;
                        $logtrans->save();

        //                 echo '<pre>New';
    				// print_r($data); 
    				// echo '</pre>';


                    } else {
                        array_push($manualProcessOrder, array(
                            "ordersn" => $transaction['ordersn'],
                            "buyername" => $transaction['customer_name']
                        ));
                        
                    }

                	}
    			}

    			// die();

	            // MANUAL PROCESS HANDLING
                // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
                $subject = "Shopee Manual Migration Notification";
                $recipient = array(
                    "email"=>Config::get('constants.ShopeeManagerEmail'),
                );
                $data = array(
                        'execution_datetime'      => date("Y-m-d H:i:s"),
                        'total_records'  => count($transactions),
                        'manual_process'  => count($manualProcessOrder),
                        'manual_order_list'  => $manualProcessOrder,
                        'transfered_orders'  => $transferedProcessOrder,
                        'acc_name'  => 'JOCOM',0
                );

                // Mail::queue('emails.astrogouploadreport', $data, function ($message) use ($recipient,$subject) {
                //     $message->from('payment@jocom.my', 'JOCOM');
                //     $message->to($recipient['email'], $recipient['name'])
                //             ->cc(Config::get('constants.AstroGoShopManagerEmailCC'))
                //             ->subject($subject);
                // });
                
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