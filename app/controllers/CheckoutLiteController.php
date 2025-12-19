<?php
// YH: Fees Models have DB query issue like keep repick and loop data -> Fees::find(1);
// YH: Fees Model GetTotalDelivery() these query should move out from loop DB::table('jocom_zones')->select('*')->where('id', '=', $zone)->first();
class CheckoutLiteController extends BaseController {
	private static $gstTotalComputeTax = true; // This is define the tax calculation is based on per item of per total amount.!
	private static $gst_status = false; // Fees::get_gst_status()
	private static $tax_rate = 0;
	private static $web_checkout = false;
	private static $buyer_zone = false;
	private static $flash_product_only = true;
	private static $flash_controller = ['FS' => 'FlashSaleController', 'EC' => 'JocomExcCornerController', 'CD' => 'JocomComboDealsController', 'DY' => 'DynamicSaleController'];
	private static $flash_table = ['FS' => 'jocom_flashsale_products', 'EC' => 'jocom_jocomexcorner_products', 'CD' => 'jocom_combodeals_products', 'DY' => 'jocom_dynamicsale_products'];

	// checkout src
	private static $c_src = [
		"web" => 2,         // Web Checkout
		"web_others" => 3,  // Web Other Platforms Checkout //ie., Ecommunity
		"web_mycash" => 4,  // Web Other Platforms Checkout //ie., MyCashOnline etc
		"webboost" => 5,    // Web Checkout Boost Payment
		"webasean" => 6,    // Web Checkout ASEAN/crossborder Payment
		"wavpay" => 7,      // Web Other Platforms Checkout //ie., Wavpay
		"efstore" => 8,     // Web Checkout efstore
	];

	// Plateform Order Model
	private static $plateform_order_model = [
		1 => 'ElevenStreetOrder',
		2 => 'LazadaOrder',
		3 => 'QootenOrder',
		4 => 'ShopeeOrder',
		5 => 'PGMallOrder',
	];

	// Plateform Order Details Model get by order ID
	private static $plateform_details_model = [
		1 => [
			'name' => 'ElevenStreetOrderDetails',
			'dc_calc_in_loop' => true,
			'return_name' => 'dlvCst',
			'post_name' => 'elevenstreetDeliveryCharges',
		],
		2 => [
			'name' => 'LazadaOrderDetails',
			'dc_calc_in_loop' => true,
			'return_name' => 'ShippingAmount',
			'post_name' => 'lazadaDeliveryCharges',
		],
		3 => [
			'name' => 'QootenOrderDetails',
			'dc_calc_in_loop' => false,
			'return_name' => 'ShippingRate',
			'post_name' => 'qoo10DeliveryCharges',
		],
		4 => [
			'name' => 'ShopeeOrderDetails',
			'dc_calc_in_loop' => false,
			'return_name' => 'estimated_shipping_fee',
			'post_name' => 'shopeeDeliveryCharges',
		],
		5 => [
			'name' => 'PGMallOrderDetails',
			'dc_calc_in_loop' => false,
			'return_name' => 'shipping_amount',
			'post_name' => 'pgmallDeliveryCharges',
		],
	];

	// SP customer Group checker
	private static $spgroup_id = false;
	private function spgroup_check($buyer_username = ''){
		if(self::$spgroup_id === false){
			$spGroup = DB::table('jocom_user AS user')
				->leftJoin('jocom_sp_customer AS customer', 'user.id', '=', 'customer.user_id')
				->leftJoin('jocom_sp_customer_group AS group', 'customer.id', '=', 'group.sp_cust_id')
				->where('user.username', '=', $buyer_username)
				->select('group.sp_group_id')
				->first();
			self::$spgroup_id = (isset($spGroup->sp_group_id) ? $spGroup->sp_group_id : 0);
		}
		return self::$spgroup_id;
	}


	/**
	 * Lite Version of api/checkout
	 * SRC: /CheckoutController.php function anyIndex()
	 * Version: 1.05
	 */
	public function anyIndex(){
		try{
			self::$web_checkout = false;

			if(in_array(Input::get('devicetype'), ["web", "web_others", "web_mycash", "webboost", "webasean", "efstore"])){
				self::$web_checkout = true;
				$CustomerInfo = Customer::where('email', Input::get('email'))->first();
			
				if(count($CustomerInfo) > 0){ // Account is exist
					$_POST["user"] = $CustomerInfo->username;
					$_POST["pass"] = $CustomerInfo->password;
				}else{ // Account not exist, proceed to create Customer account
					$pass       = $_POST["firstname"] . $_POST["lastname"];
					$_POST["user"] = Input::get('email');
					$_POST["pass"] = Hash::make($pass);
					
					$Customer = new Customer();
					$Customer->username = $_POST["user"];  
					$Customer->password = $_POST["pass"];  
					$Customer->email = Input::get('email');  
					$Customer->firstname = $_POST["firstname"];  
					$Customer->lastname = $_POST["lastname"];  
					$Customer->full_name = $_POST["firstname"] . " " . $_POST["lastname"];  
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
					$Customer->save(); 

					// Welcome Email
					$user = [
						'email' => $Customer->email,
						'name'  => $Customer->firstname,
						'username'  => $Customer->username,
					];
					$data = [
						'name'      => $Customer->firstname,
						'username'  => $Customer->username,
						'password'  => $pass,
						"email_activation" => 0,
						'environment'  => Config::get('constants.ENVIRONMENT')
					];
					if(Input::get('devicetype') != 'web_mycash'){
						Mail::send('emails.welcome', $data, function($message) use ($user) {
							$message->from('payment@tmgrocer.com', 'tmGrocer');
							$message->to($user['email'], $user['name'])->subject("[tmGrocer]: Welcome new member!");
						});
					}
				}
			}

			if(Input::get('devicetype') === "wavpay" && Input::get('WavPayUID') && Input::get('WavPaySID')){
				self::$web_checkout = true;
				$uID = json_encode(["userID" => preg_replace('/[^\w- ]+/', '', (Input::get('WavPayUID') ? Input::get('WavPayUID') : ''))]);

				$CustomerInfo = Customer::where('full_name', Input::get('user'))->where('email', Input::get('email'))->where('ref_info', $uID)->first();
				if(count($CustomerInfo) > 0){
					// Use existing account
					$_POST["user"] = $CustomerInfo->username;
					$_POST["pass"] = $CustomerInfo->password;
				}
			}

			self::$tax_rate = Fees::get_tax_percent();
			if(!isset($CustomerInfo)) $CustomerInfo = Customer::where('username', $_POST["user"])->first();

			if(in_array(Input::get('devicetype'), ['android', 'ios'])){
				$_POST["main_bussines_currency"] = 'MYR';
			}else{
				$countryInfo = DB::table('jocom_countries AS JC')->where('JC.id', '=', $CustomerInfo->country_id)->select('JC.id', 'JC.currency','JC.business_currency')->first();
				$_POST["main_bussines_currency"] = $countryInfo->business_currency;
			}

			// from cart to checkout, only with $_POST["user"], no return from PayPal
			if (!isset($_POST["txn_id"]) && !isset($_POST["txn_type"]) && isset($_POST["user"])) {
				$main_business_currency = isset($_POST["main_bussines_currency"]) ? $_POST["main_bussines_currency"] : 'MYR'; // CURRENCY SET

				// YH: Possible to optimise these code need think careful before move on
				switch ($main_business_currency) {
					case 'MYR':
						$main_business_currency = 'MYR';
						$base_currency = 'MYR';
						$standard_currency = 'USD';
						$foreign_country_currency = 'MYR';

						$main_business_currency_data = ExchangeRate::getExchangeRate($base_currency, $main_business_currency);
						$base_currency_rate_data = ExchangeRate::getExchangeRate($base_currency, $base_currency);
						$standard_currency_rate_data = ExchangeRate::getExchangeRate($base_currency, $standard_currency);
						$foreign_country_rate_data = ExchangeRate::getExchangeRate($base_currency, $foreign_country_currency);
						
						$base_currency_rate = $base_currency_rate_data->amount_to;
						$standard_currency_rate = $standard_currency_rate_data->amount_to;
						$foreign_country_rate = $foreign_country_rate_data->amount_to;
						$main_business_currency_rate = $main_business_currency_data->amount_to;

						break;
					
					case 'RMB':
						$main_business_currency = 'RMB';
						$base_currency = 'MYR';
						$standard_currency = 'USD';

						$base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
						$standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
						$base_currency_rate = $base_currency_rate_data->amount_to;
						$standard_currency_rate = $standard_currency_rate_data->amount_to;
						
					case 'USD':
						$main_business_currency = 'USD';
						$base_currency = 'MYR';
						$standard_currency = 'USD';
						$foreign_country_currency = 'RMB';
						
						$main_business_currency_data = ExchangeRate::getExchangeRate($main_business_currency, $main_business_currency);
						$base_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $base_currency);
						$standard_currency_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $standard_currency);
						$foreign_country_rate_data = ExchangeRate::getExchangeRate($main_business_currency, $foreign_country_currency);
						
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
				$get = [
					'user'                  => trim($_POST["user"]),                                    // Buyer Username
					'pass'                  => trim($_POST["pass"]),                                    // Buyer Password
					'urow'                  => $CustomerInfo,
					'delivery_name'         => trim(Input::get('delivername')),                         // delivery name
					'delivery_contact_no'   => trim(Input::get('delivercontactno')),                    // delivery contact no
					'special_msg'           => trim(Input::get('specialmsg')),                          // special message
					'delivery_addr_1'       => trim(Input::get('deliveradd1')),
					'delivery_addr_2'       => trim(Input::get('deliveradd2')),
					'delivery_postcode'     => trim(Input::get('deliverpostcode')),
					'delivery_city'         => Input::has('city') ? trim(Input::get('city')) : '',      // City ID
					'delivery_state'        => trim(Input::get('state')),                               // State ID
					'delivery_country'      => trim(Input::get('delivercountry')),                      // Country ID
					'delivery_charges'      => trim(Input::get('delivery_charges')),  
					'qrcode'                => Input::get('qrcode'),
					'price_option'          => Input::get('priceopt'),                                  // Price Option
					'qty'                   => Input::get('qty'),
					'devicetype'            => Input::get('devicetype'),
					'uuid'                  => Input::has('uuid') ? trim(Input::get('uuid')) : NULL,    // City ID

					'lang'                  => Input::get('lang', 'EN'),
					'ip_address'            => Input::get('ip', Request::getClientIp()),
					'location'              => Input::get('location', ''),
					'isPopbox'              => Input::get('isPopbox', ''),
					'deliverPopbox'         => Input::get('deliverPopbox', ''),
					'popaddresstext'        => Input::get('popaddresstext', ''),
					'transaction_date'      => (Input::has('transaction_date') && Input::get('transaction_date') != "" ? Input::get('transaction_date') . " 00:00:00" : ''),
					'is_self_collect'       => Input::get('is_self_collect', 0),
					'create_by_user'        => Session::get('username') != '' ? Session::get('username') : '',

					'charity_id'            => Input::get('charity_id', ''),
					'external_ref_number'   => Input::get('external_ref_number', ''),
					'selected_invoice_date' => Input::get('selected_invoice_date', null),
					'invoice_to_address'    => Input::get('invoice_to_address', 1),
					
					// CURRENCY //
					'invoice_bussines_currency'         => $main_business_currency,
					'invoice_bussines_currency_rate'    => $main_business_currency_rate,
					'standard_currency'                 => $standard_currency,
					'standard_currency_rate'            => $standard_currency_rate,
					'base_currency'                     => $base_currency,
					'base_currency_rate'                => $base_currency_rate,
					'foreign_country_currency'          => $foreign_country_currency,
					'foreign_country_currency_rate'     => $foreign_country_rate,
				];

				/* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */
				if((Input::get('transfer_order_id') != 0) && isset(self::$plateform_details_model[Input::get('transfer_order_id_type')])){
					$platfom_DC = 0; // plateform Deliver Charge
					$modelname = self::$plateform_details_model[Input::get('transfer_order_id_type')]['name'];
					$OrderDataDetails = $modelname::getByOrderID(Input::get('transfer_order_id'));
					if(self::$plateform_details_model[Input::get('transfer_order_id_type')]['dc_calc_in_loop']){
						foreach ($OrderDataDetails as $valueDetails) {
							$APIData = json_decode($valueDetails->api_result_return, true);
							$platfom_DC += ($APIData[self::$plateform_details_model[Input::get('transfer_order_id_type')]['return_name']] * (100 / (100 + self::$tax_rate)));
						}
					}else{
						$lastkey = end($OrderDataDetails);
						$APIData = json_decode($OrderDataDetails[$lastkey]->api_result_return, true);
						$platfom_DC += ($APIData[self::$plateform_details_model[Input::get('transfer_order_id_type')]['return_name']] * (100 / (100 + self::$tax_rate)));
					}
					$get[self::$plateform_details_model[Input::get('transfer_order_id_type')]['post_name']] = $platfom_DC;
				}
				/* FIX MANUAL TRANSFER DELIVERY CHARGES TO FOLLOW 11STREET DELIVERY CHARGES */            

				Session::put('lang', $get["lang"]);
				Session::put('devicetype', $get["devicetype"]);
				$signal_check = base64_encode(serialize($get));

				$data = [];
				if (!isset($data['transaction_id'])) { // if transaction ID not set, do create the transaction data
					$data = self::CREATE_transaction($get);
					if(Input::get('transfer_order_id') != 0 && Input::get('transfer_order_id_type') != 0 && isset(self::$plateform_order_model[Input::get('transfer_order_id_type')])){
						$modelname = self::$plateform_order_model[Input::get('transfer_order_id_type')];
						$order = $modelname::find(Input::get('transfer_order_id'));
						$order->status = 2;
						$order->transaction_id = $data["transaction_id"];
						$order->save();
					}
					if($get['isPopbox'] == 1) $data['popbox'] = PopboxController::savePopBox($data["transaction_id"], $get['deliverPopbox'], $get['popaddresstext']);
					
				}

				if (isset($data['status']) && $data['status'] == 'success') { // succesfully checkout
				    
					Session::put('checkout_signal_check', $signal_check);
					Session::put('checkout_transaction_id', $data["transaction_id"]);
					Session::put('lang', $data["lang"]);
					Session::put('devicetype', $data["devicetype"]);
					Session::put('android_orderid', $data["transaction_id"]);
					if(!isset($data['trans_query']) && !isset($data['trans_detail_query'])) $data = array_merge($data, MCheckout::get_checkout_info($data["transaction_id"])); // if trans_query trans_detail_query not set do pick the data again

					if (isset($data['trans_query'])) {
						$data['points'] = PointUser::getPoints($data['trans_query']['buyer_id'], PointUser::ACTIVE_ONLY);

						// if trans_cashback is not set and cashbackflag is true, do grasp data like OLD API
						if(isset($data["cashbackflag"]) && $data["cashbackflag"] == 1 && !isset($data['trans_cashback'])) {
							$trans_cashback = DB::table('jocom_transaction_jcashback')->where('user_id', '=', $data['trans_query']['buyer_id'])->where('qrcode', '=', $data["cashbacktext"])->where('status', '=', 1)->where('jcash_point_used', '=', 0)->orderBy('id', 'ASC')->first();

							if(count($trans_cashback) > 0){
								$data = array_merge($data, [
									'trans_cashback' => [
										'id' => $trans_cashback->id,
										'sku' => $trans_cashback->sku,
										'user_id' => $trans_cashback->user_id,
										'product_name' => $trans_cashback->product_name,
										'jcash_point' => $trans_cashback->jcash_point
									]
								]);
							}
						}

						return (self::$web_checkout ? $data : Response::view(Config::get('constants.CHECKOUT_FOLDER') . (Input::get('devicetype') == "manual" ? '.lite_manual' : '.lite_view'), $data));
					}
					if ($data['message'] == '') $data['message'] = '101';
					return (self::$web_checkout ? $data : View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('dataCollection',$data));
				}

				// Fail to checkout
				Session::forget('checkout_signal_check');
				Session::forget('checkout_transaction_id');
				if ($data['message'] == '' && !self::$web_checkout) $data['message'] = '101';
				return (self::$web_checkout ? $data : View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message'])->with('kkwprod', $data['kkwprod'])->with('userinfo', $data['userinfo'])->with('dataCollection', $data));
			} else if (!empty($_POST) || Input::has('tran_id')) { // "$_POST" return post by PayPal, "Input::get('tran_id')" PayPal android only able to return id via url
				// YH: DOES NOT NEED OPTIMISE FIRST
				$transactionType = (!empty($_POST) ? MCheckout::transaction_complete($_POST) : MCheckout::transaction_complete_android(Input::get('tran_id')));
				if (Input::has('lang')) Session::put('lang', Input::get('lang'));

				$transaction = Transaction::find((!empty($_POST) ? $_POST["invoice"] : Input::get('tran_id')));
				$user        = Customer::find($transaction->buyer_id);
				$bcard       = BcardM::where('username', '=', $user->username)->first();
				$bcardStatus = PointModule::getStatus('bcard_earn');

				$data['message'] = ($transactionType === 'point' ? '006' : '001');
				$data['payment'] = 'JCSUCCESS successfully received';
				return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')
					->with('message', $data['message'])
					->with('payment', $data['payment'])
					->with('id', (!empty($_POST) ? $_POST["invoice"] : Input::get('tran_id')))
					->with('bcardStatus', $bcardStatus)
					->with('bcardNumber', object_get($bcard, 'bcard'))
					->with('buyerId', $transaction->buyer_id);
			}
			// Error Page
			if (Input::has('lang')) Session::put('lang', Input::get('lang'));
			$data['message'] = '101';
			return View::make(Config::get('constants.CHECKOUT_FOLDER') . '.checkout_msg_view')->with('message', $data['message']);
		} catch (Exception $e) {
			$ApiLog = new ApiLog;
			$ApiLog->api = 'CHECKOUT_ERROR';
			$ApiLog->data = $e->getMessage() .'-'. $e->getLine();
			$ApiLog->save();
		}
	}


	/**
	 * Lite Version of Model MCheckout function
	 * SRC: models/MCheckout.php function scopeCheckout_transaction()
	 * Version: 1.06
	 */
	private function CREATE_transaction($post = []){
		$returnData = [ 'status'  => 'error', 'message' => '101' ];

		if(!isset($post['qrcode']) || !is_array($post['qrcode'])) return $returnData; // QRcode not set return false
		if (is_null($post['urow'])) return [ 'status'  => 'error', 'message' => '102' ]; // Invalid Buyer/Customer

		// to define total or unit items bought
		$total_unit_items = 0;
		$fl_sale = 0;
		$off_sale = 0;
		$error = false;
		
		$accountStatus = $post['urow']->active_status;
		$returnData['userinfo'] = [ "userEmail" => $post['urow']->email, "username" => $post['urow']->username ];

		$country_row = DB::table('jocom_countries')->where('id', '=', $post['delivery_country'])->select('name')->first();
		$state_row = DB::table('jocom_country_states')->where('id', '=', $post['delivery_state'])->where('country_id', '=', $post['delivery_country'])->select('name', 'id')->first();

		// Get Zone
		$city_name  = '';
		$city_id    = 0;
		$delivery_countryID = $post['delivery_country'];
		
		// YH HARDCODE: FREESHIPPING
		$freeshipping = (in_array(date('Y-m-d'), ['2023-10-10', '2023-10-11', '2023-10-12', '2023-10-13']) && in_array($post['delivery_state'], ['458004', '458013', '458015']) ? 1 : 0);

		if ($post['delivery_city'] != null || $post['delivery_city'] != '') {
			$city_row = DB::table('jocom_cities')->where('id', '=', $post['delivery_city'])->where('state_id', '=', $post['delivery_state'])->select('name', 'id')->first();
			if (count($city_row) > 0) {
				$city_name = $city_row->name;
				$city_id   = $city_row->id;
			}

			$zoneIDlist = DB::table('jocom_zone_cities')->where('city_id', '=', $post['delivery_city'])->lists('zone_id');
		} else {
			$city_row = '1';
			$zoneIDlist = DB::table('jocom_zone_states')->where('states_id', '=', $post['delivery_state'])->lists('zone_id');
		}

		if (count($zoneIDlist) == 0) {
			$error = true;
			self::$buyer_zone = null;
			$returnData['message'] = '105'; // 'Invalid location selected.';
		} else {
			self::$buyer_zone = DB::table('jocom_zones')->where('country_id', '=', $post['delivery_country'])->whereIn('id', $zoneIDlist)->lists('id');
		}

		// Error checking, check the location data is missing or not
		if($error === false && ($country_row == null || $state_row == null || $city_row == null || count(self::$buyer_zone) == 0 || $accountStatus != 1)){ 
			$error = true;
			$returnData['message'] = (
				$country_row == null
				? '103' // 'Invalid country'
				: ( $state_row == null 
					? '104' // 'Invalid state'
					: ( $city_row == null 
						? '112' // 'Invalid city'
						: ( count(self::$buyer_zone) == 0
							? '105' // 'Invalid location selected'
							: '114' // 'Account not activate'
						)
					)
				)
			);
		}

		if ($error === false) { // No Error on location proceed to generate the transaction data
			$transac_data = [
				"transaction_date"                  => ($post['transaction_date'] == "" ? date('Y-m-d H:i:s') : $post['transaction_date']),
				"status"                            => "pending",
				"buyer_id"                          => $post['urow']->id,
				"buyer_username"                    => $post['urow']->username,
				"delivery_name"                     => $post['delivery_name'],
				"delivery_contact_no"               => $post['delivery_contact_no'],
				"special_msg"                       => $post['special_msg'],
				"third_party"                       => isset($post['elevenstreetDeliveryCharges']) ? 1 : 0,
				"third_party_lazada"                => isset($post['lazadaDeliveryCharges']) ? 1 : 0,
				"third_party_qoo10"                 => isset($post['qoo10DeliveryCharges']) ? 1 : 0,
				"third_party_shopee"                => isset($post['shopeeDeliveryCharges']) ? 1 : 0,
				"third_party_astrogo"               => isset($post['astrogoDeliveryCharges']) ? 1 : 0,
				"third_party_pgmall"                => isset($post['pgmallDeliveryCharges']) ? 1 : 0,
				"buyer_email"                       => $post['urow']->email,
				"delivery_addr_1"                   => $post['delivery_addr_1'],
				"delivery_addr_2"                   => $post['delivery_addr_2'],
				"delivery_postcode"                 => $post['delivery_postcode'],
				"delivery_city"                     => $city_name,
				"delivery_city_id"                  => $city_id,
				"delivery_state"                    => $state_row->name,
				"delivery_state_id"                 => $state_row->id,    //Added new field - 12-01-2018
				"delivery_country"                  => $country_row->name,
				"invoice_to_address"                => $post['invoice_to_address'],
				"device_platform"                   => $post['devicetype'],
				"delivery_condition"                => '',
				"total_amount"                      => 0,
				"insert_by"                         => $post['urow']->username,
				"insert_date"                       => date('Y-m-d H:i:s'),
				"modify_by"                         => $post['urow']->username,
				"modify_date"                       => date('Y-m-d H:i:s'),
				"lang"                              => $post['lang'],
				"ip_address"                        => $post['ip_address'],
				"location"                          => $post['location'],
				'agent_id'                          => $post['urow']->agent_id,
				"charity_id"                        => $post['charity_id'],
				"external_ref_number"               => $post['external_ref_number'],
				"selected_invoice_date"             => $post['selected_invoice_date'],
				"is_self_collect"                   => $post['is_self_collect'],
				"create_by_user"                    => $post['create_by_user'],
				
				"delivery_identity_number"          => isset($post['delivery_identity_number']) ? $post['delivery_identity_number'] : '',
				'invoice_bussines_currency'         => isset($post['invoice_bussines_currency']) ? $post['invoice_bussines_currency'] : '',
				'invoice_bussines_currency_rate'    => isset($post['invoice_bussines_currency_rate']) ? $post['invoice_bussines_currency_rate'] : 0, 
				'standard_currency'                 => isset($post['standard_currency']) ? $post['standard_currency'] : '',
				'standard_currency_rate'            => isset($post['standard_currency_rate']) ? $post['standard_currency_rate'] : 0, 
				'base_currency'                     => isset($post['base_currency']) ? $post['base_currency'] : '',
				'base_currency_rate'                => isset($post['base_currency_rate']) ? $post['base_currency_rate'] : 0, 
				'foreign_country_currency'          => isset($post['foreign_country_currency']) ? $post['foreign_country_currency'] : '',
				'foreign_country_currency_rate'     => isset($post['foreign_country_currency_rate']) ? $post['foreign_country_currency_rate'] : 0, 
				'flash_sale_product'                => []
			];

			$transac_data_detail    = [];
			$transac_data_group     = [];
			$cback                  = 0;
			$cbacktext              = "";
			$durian                 = 0;
			$i_eleven               = 0;
			$is_add                 = 0;
			$is_restrict            = 0;
			


			if (in_array('JC2995', $post['qrcode']) && $post['urow']->username != 'kkwoodypavilion') {
				$returnData = [
					'status'  => 'error',
					'message' => '110',
					'kkwprod' => 'Pancake',
				];
				$error = true;
			}
            
            if ($post['urow']->delivery_contact_no == '0174378393') {
                            $returnData = [
                                'status'  => 'error',
                                'message' => '101',
                            ];
                            $error = true;
                        }
            
            if ($post['urow']->delivery_contact_no == '60174378393') {
                            $returnData = [
                                'status'  => 'error',
                                'message' => '101',
                            ];
                            $error = true;
                        }

			$match = array_intersect($post['qrcode'], ['JC2454400']);
			if (count($match) > 0) { 
				$firstkey = key($match);
				if ($post['qty'][$firstkey] >= 1) {
					$returnData = [
						'status'  => 'error',
						'message' => '116',
						'kkwprod' => Product::where("qrcode", $post['qrcode'][$firstkey])->select('name')->first()->name,
					];
					$error = true;
				}
			}


			$qtycheck = DB::table('jocom_productrestrict')->whereIn('product_id', $post['qrcode'])->lists('qty', 'product_id');
			if (count($qtycheck) > 0) {
				$qr_match = array_intersect($post['qrcode'], array_keys($qtycheck));
				$match = array_filter($post['qty'], function($v, $i) use ($qr_match, $qtycheck){
					if(isset($qtycheck[$qr_match[$i]])) return $v > $qtycheck[$qr_match[$i]];
				}, ARRAY_FILTER_USE_BOTH);
				if (count($match) > 0) {
					$firstkey = key($match);
					$returnData = [
						'status'  => 'error',
						'message' => '117',
						'kkwprod' => Product::where("qrcode", $post['qrcode'][$firstkey])->select('name')->first()->name,
					];
					$error = true;
				}
			}


			$QR_qty_check = [
				1 => [
					'qr' => ['JC50347', 'JC50348', 'JC50349', 'JC50934', 'JC51556', 'JC51697', 'JC51698', 'JC51699', 'JC51700', 'JC51701', 'JC51702'],
					'err' => '115',
				],
				2 => [
					'qr' => ['JC47165', 'JC48800', 'JC48801', 'JC48799', 'JC48798'],
					'err' => '119',
				],
				3 => [
					'qr' => ['JC48700', 'JC43991'],
					'err' => '120',
				],
				4 => [
					'qr' => ['JC48650', 'JC48652', 'JC41234', 'JC48655', 'JC48656', 'JC48657', 'JC48658', 'JC48659', 'JC48660', 'JC48662'],
					'err' => '118',
				],
				5 => [
					'qr' => ['JC48580', 'JC41097', 'JC41389', 'JC41821', 'JC41822', 'JC41855', 'JC41874', 'JC41855', 'JC41903', 'JC41932', 'JC41944', 'JC43306', 'JC43306', 'JC27858', 'JC27857', 'JC49717', 'JC37391', 'JC48584', 'JC48576', 'JC43549', 'JC41152', 'JC39079', 'JC39077', 'JC39074', 'JC39073', 'JC39072', 'JC29887', 'JC29595', 'JC41026', 'JC43356', 'JC42330', 'JC41043', 'JC13819'],
					'err' => '117',
				]
			];


			$qty_type = false;
			for ($i = 1; $i <= 5; $i++) { 
				$match = array_intersect($post['qrcode'], $QR_qty_check[$i]['qr']);
				if(count($match) > 0){
					$qty_exceed = array_filter($post['qty'], function($v, $k) use ($match, $i) { if(isset($match[$k])) return $v > $i; }, ARRAY_FILTER_USE_BOTH);
					if(count($qty_exceed) > 0) $qty_type = $QR_qty_check[$i]['err'];
				}
				if($qty_type) break;
			}

			if($qty_type){
				$firstkey = key($qty_exceed);
				$returnData = [
					'status'  => 'error',
					'message' => $qty_type,
					'kkwprod' => Product::where("qrcode", $post['qrcode'][$firstkey])->select('name')->first()->name,
				];
				$error = true;
			}

			// if not error proceed loop and gerenate the data
			if($error === false){
				self::$gst_status = Fees::get_gst_status();

				if (in_array('JC34288', $post['qrcode'])){
					$cback = 1; 
					$cbacktext = "JC34288";
				}

				// according product set the $delivery_charges
				if(count(array_intersect($post['qrcode'], ['JC37588', 'JC37836'])) > 0) $durian = 1;
				if(in_array('JC32982', $post['qrcode'])) $off_sale = 1;

				// 11.11 Freeshipping Start....
				$totalSKU = Product::where("status", 1)->whereIn("qrcode", $post['qrcode'])->where('category', 'LIKE', '%1060%')->count();
				if($totalSKU > 0) $i_eleven = 1;

				// Pick view table product and package and join the product table that related to qrcode for data reuse
				$product_data_row = DB::table('jocom_product_and_package AS JPP')->leftJoin('jocom_products AS JP', 'JP.qrcode', '=', 'JPP.qrcode')->whereIn('JPP.qrcode', $post['qrcode'])->select('JPP.qrcode', 'JPP.id', 'JPP.name', 'JP.name_cn', 'JP.name_my', 'JPP.sku', 'JPP.delivery_time', 'JP.is_popbox_available', 'JP.gst AS is_taxable')->get();
				if(count($product_data_row) > 0){
					$product_data_row = json_decode(json_encode($product_data_row), true);
					$product_data_row = array_combine(array_column($product_data_row, 'qrcode'), $product_data_row);
				}else{
					$product_data_row = [];
				}

				$group_total = [];
				foreach ($post['qrcode'] as $k => $v) {
					if ($error === true) break; // If error stop all loop process

					$platform_original_price = 0;
					$shopee_poriginal_price = 0;
					$lazada_poriginal_price = 0;

					$platform_price = 0;
					$lazadapirce = 0;
					$shopeepirce = 0;
					$pgmallpirce = 0;
					$qrcode       = $post['qrcode'][$k];
					$qty          = $post['qty'][$k];
					$price_option = $post['price_option'][$k];
					$lazadapirce  = $post['lazadaoriginalpirce'][$k] ? $post['lazadaoriginalpirce'][$k] : 0;
					$shopeepirce  = $post['shopee_original_price'][$k] ? $post['shopee_original_price'][$k] : 0;
					$pgmallpirce  = $post['pgmall_original_price'][$k] ? $post['pgmall_original_price'][$k] : 0;

					$shopee_poriginal_price  = $post['shopee_platform_original_price'][$k] ? $post['shopee_platform_original_price'][$k] : 0;
					$lazada_poriginal_price  = $post['lazada_platform_originalpirce'][$k] ? $post['lazada_platform_originalpirce'][$k] : 0;

					if(is_numeric($lazadapirce) && $lazadapirce > 0) $platform_price = $lazadapirce;
					if(is_numeric($shopeepirce) && $shopeepirce > 0) $platform_price = $shopeepirce;
					if(is_numeric($pgmallpirce) && $pgmallpirce > 0) $platform_price = $pgmallpirce;
					if(is_numeric($shopee_poriginal_price) && $shopee_poriginal_price > 0) $platform_original_price = $shopee_poriginal_price;
					if(is_numeric($lazada_poriginal_price) && $lazada_poriginal_price > 0) $platform_original_price = $lazada_poriginal_price;

					if ($qrcode != '' && is_numeric($qty) && $qty > 0) { // is valid qrcode and qty in numeric
						if (isset($product_data_row[$qrcode])) {
							if (substr($product_data_row[$qrcode]['id'], 0, 1) != 'P') { // not package Product
								$tmp_return_data = self::ADD_trans_detail($product_data_row[$qrcode], $platform_price, $platform_original_price, $price_option, $qty, $returnData, $transac_data, $transac_data_detail, $error, "", $post['urow']->username);
								$total_unit_items += (int)$returnData['item_quantity']; // Sum up total items
							} else {
								// Get Package Products
								$get_pro_query = DB::table('jocom_product_package_product AS JPPP')
									->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'JPPP.product_opt')
									->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JPP.product_id')
									->where('JPPP.package_id', '=', substr($product_data_row[$qrcode]['id'], 1))
									->select('JPPP.package_id', 'JPPP.product_opt', 'JPPP.qty', 'JP.qrcode', 'JPP.product_id AS id', 'JP.name', 'JP.sku', 'JP.delivery_time', 'JP.is_popbox_available', 'JP.gst AS is_taxable')->get();

								// for each package
								foreach ($get_pro_query as $get_pro_row) {
									if ($get_pro_row->id && !$error) { // Product is avaliable, and previous package product does not have issue
										$prow = json_decode(json_encode($get_pro_row), true);
										$tmp_return_data = self::ADD_trans_detail($prow, $platform_price, $platform_original_price, $get_pro_row->product_opt, $get_pro_row->qty * $qty, $returnData, $transac_data, $transac_data_detail, $error, $product_data_row[$qrcode]['sku'], "");
										$total_unit_items += (int)$returnData['item_quantity']; // Sum up total items
									} else { // either product ID not found or previous package product have issue
										$error = true;
										$returnData['message'] = '106';
										break;
									}
								}

								if (isset($transac_data_group[$product_data_row[$qrcode]['sku']])) {
									$transac_data_group[$product_data_row[$qrcode]['sku']]["unit"] += $qty;
								} else {
									$transac_data_group[$product_data_row[$qrcode]['sku']] = [
										"sku"  => $product_data_row[$qrcode]['sku'],
										"unit" => $qty,
										"qrcode" => $qrcode,
										"product_name" => (strtolower($post['lang']) === 'cn' ? $product_data_row[$qrcode]['name_cn'] : (strtolower($post['lang']) === 'my' ? $product_data_row[$qrcode]['name_my'] : $product_data_row[$qrcode]['name'])),
									];
								}
							} // end not package
						} else { // Product data not found
							$error = true;
							$returnData['message'] = '106';
							break;
						}
					}
				}

				if($error === false){
					// Check special pricing meet minimum purchase requirement.
					foreach ($transac_data_detail as $key => $trow) {
						if ($trow['sp_group_id'] != 0) {
							if (!isset($group_total[$trow['sp_group_id']])) $group_total[$trow['sp_group_id']] = 0;
							$group_total[$trow['sp_group_id']] += $trow['total'];
						}
					}

					// Special min purchase checker loop
					if(count($group_total) > 0){
						$groupmin = DB::table('jocom_sp_group')->whereIn('id', array_keys($group_total))->lists('min_purchase', 'id');
						foreach ($group_total as $key => $value) {
							if ($group_total[$key] < $groupmin[$key]) {
								$error                 = true;
								$returnData['message'] = '111';
								break;
							}
						}
					}
				}
			}
		}

		if ($error === false) { // no error on product, proceed to checkout
			// Start: Calculate delivery fees ----------------
			$temp_weight = [];
			$temp_zone = [];

			$delivery_charges = Fees::GetTotalDelivery($transac_data_detail);
			$process_fees = Fees::get_process_fees();
			$temp_gst_process  = 0;
			$temp_gst_delivery = 0;
			$temp_gst_rate     = 0;

			if (self::$gst_status == '1') {
				$temp_gst_rate     = Fees::get_gst();
				$temp_gst_process  = round(($process_fees * $temp_gst_rate / 100), 2);
				$temp_gst_delivery = round(($delivery_charges * $temp_gst_rate / 100), 2);
			}

			if($transac_data['invoice_bussines_currency'] == 'USD'){
				$LocalExchangeRate = ExchangeRate::getExchangeRate('USD', 'MYR');
				$local_delivery_charges = $delivery_charges * $LocalExchangeRate->amount_to;
				$transac_data['delivery_charges'] = $local_delivery_charges;
				$transac_data['foreign_delivery_charges'] = $delivery_charges;
			}else{
				$transac_data['delivery_charges'] = $delivery_charges;
			}
			
			$transac_data['process_fees']       = $process_fees;
			$transac_data['gst_rate']           = $temp_gst_rate;
			$transac_data['gst_process']        = $temp_gst_process;
			$transac_data['gst_delivery']       = $temp_gst_delivery;
			$transac_data['gst_total']          += $temp_gst_process + $temp_gst_delivery;
			$transac_data['delivery_condition'] = "Delivery fees is set in the item";
			// End: Calculate delivery fees ----------------

			// Dynamic get all product zone ID, and price related zone
			// $z_id = array_column($transac_data_detail, 'zone_id');
			// $zp = DB::table('jocom_zones')->whereIn('id', $z_id)->select('id', 'init_price', 'add_price')->get();
			// $z_nozerofee = array_filter($zp, function($v) { return (float)$v->init_price > 0 || (float)$v->add_price > 0; });
			
			
            // flash product Min purchase Check
				$flash_deliverfee = false;
				if(count($transac_data_detail) > 0){
					$min_val = DB::table('jocom_minspendvalue')->whereIn('product_id', array_column($transac_data_detail, 'qrcode'))->where('status', 1)->orderBy('minvalue', 'ASC')->lists('minvalue');
					if(isset($min_val[0]) && (float)$transac_data['total_amount'] < $min_val[0]) $flash_deliverfee = true;
				}
				
				
			$z_id = array_column($transac_data_detail, 'zone_id');
			$z_nozerofee = array_filter($z_id, function($v) { return !in_array((int)$v, [5, 10, 20, 21, 28, 34, 43, 62, 76, 82]); }); // Hard code Free shipping zone id due to unable retrive the change
			
			if(count($z_nozerofee) == 0 && $freeshipping == 0 && !$flash_deliverfee){ // is freeshipping item only in checkout
				$freeshipping = 1;
				$d_cy = 0; $d_c0 = 1;
			}else{
			    
				
				// YH: Hardcode, Flat rate overwrite deliver fee
				// YH: Klang Valley Deliver Charge RM10 when purchase more than RM 120 do Free Deliver
				// YH: West Malaysia Deliver Charge RM18 when purchase more than RM 200 do Free Deliver
				$is_klang = DB::table('jocom_zone_states')->where('states_id', '=', $post['delivery_state'])->where('zone_id', '=', 3)->first();
				$is_west = DB::table('jocom_zone_states')->where('states_id', '=', $post['delivery_state'])->where('zone_id', '=', 9)->first();
				$d_cy = ($post['isDelivery'] == "1" || $fl_sale == 1 || $off_sale == 1 || ($is_klang && (float)$transac_data['total_amount'] < 120) || ($is_west && (float)$transac_data['total_amount'] < 200) ? 1 : 0);
				$d_c0 = ($firsttimer == 1 || $durian == 1 || $i_eleven == 1 || $freeshipping == 1 || (self::$flash_product_only && !$flash_deliverfee) || ($is_klang && (float)$transac_data['total_amount'] >= 120) || ($is_west && (float)$transac_data['total_amount'] >= 200) ? 1 : 0); // is Freeshipping condition
			}

			if($d_cy || $d_c0){
				$delivery_charges = ($d_c0 ? 0 : ($is_klang ? 15 : ($is_west ? 24 : 15)));
				$process_fees = 0;
				$transac_data['gst_delivery'] = 0;
				$transac_data['gst_total'] -= $temp_gst_delivery;
				$transac_data['delivery_charges'] = $delivery_charges;
			}

			/** FREE DELIVERY AND PROCESSINF FOR MYCYBERSALE2016 WEB CHECKOUT **/
			$post_keylist = array_keys($post);
			$match = array_intersect(array_keys($post), ['elevenstreetDeliveryCharges', 'lazadaDeliveryCharges', 'qoo10DeliveryCharges', 'shopeeDeliveryCharges', 'pgmallDeliveryCharges', 'delivery_charges']);
			if(count($match) > 0){
				$firstkey = key($match);
				if(($post_keylist[$firstkey] === 'delivery_charges' && !empty($post[$post_keylist[$firstkey]])) || $post_keylist[$firstkey] !== 'delivery_charges'){
					$d_value = ($post[$post_keylist[$firstkey]] ? $post[$post_keylist[$firstkey]] : 0);
					$delivery_charges = number_format($d_value, 2, '.', '');
					$process_fees = 0;

					$gst_delivery = number_format(($d_value * self::$tax_rate) / 100, 2, '.', '');
					$transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
					$transac_data['gst_total'] -= $temp_gst_delivery;
					$transac_data['gst_total'] += $gst_delivery;
					$transac_data['delivery_charges'] = $delivery_charges;
				}
			}
			
			$transac_data['delivery_charges'] = $delivery_charges;
			$transac_data['process_fees'] = $process_fees;
			$transac_data['total_amount'] += $transac_data['delivery_charges'] + $transac_data['process_fees'];
			if($transac_data['invoice_bussines_currency'] == 'USD') $transac_data['foreign_total_amount'] += $transac_data['foreign_delivery_charges'];

			if($delivery_countryID == 156){
				$transac_data['gst_delivery'] = 0;
				$transac_data['gst_total'] = 0;
			}

			unset($transac_data['third_party']);
			unset($transac_data['third_party_lazada']);
			unset($transac_data['third_party_qoo10']);
			unset($transac_data['third_party_shopee']);
			unset($transac_data['third_party_astrogo']);
			unset($transac_data['third_party_pgmall']);
			unset($transac_data['invoice_to_address']);
			unset($transac_data['flash_sale_product']);

			// Set Delivery Area Type
			$collection = DB::table('jocom_keywords')->where('type', '=', 'office')->lists('title');
			$transac_data['delivery_area_type'] = (preg_match('/(' . str_replace('\|', '|', preg_quote(implode('|', $collection), '/')) . ')/i', $transac_data['delivery_addr_1'] . ' ' . $transac_data['delivery_addr_2']) ? 'office' : 'house'); // using preg_match do check > using the SQL LIKE string check

			// Set Checkout Source
			if(self::$web_checkout && in_array(Input::get('devicetype'), array_keys(self::$c_src))) 
				$transac_data['checkout_source'] = self::$c_src[Input::get('devicetype')];


			$trans_id = DB::table('jocom_transaction')->insertGetId($transac_data);
			$transac_data['id'] = $trans_id;
             
			// Insert invoice address information
			TransactionInvoiceAddress::saveAddress($trans_id, $transac_data['invoice_to_address']);

			// Start: Update Transaction Information when is webcheckout ----------------
			$update_sql = "";
			if($post['devicetype'] === 'wavpay'){
				$result = WavpayController::anyInitial([
					'user' => $post['urow'],
					'TID' => $trans_id,
					'amount' => $transac_data['total_amount'],
					'currency' => $transac_data['base_currency'],
					'delivery_contact_no' => $transac_data['delivery_contact_no'],
					'Wavpay_SID' => Input::get('WavPaySID'), // Session ID
					'Wavpay_UID' => Input::get('WavPayUID'), // User ID
				]);
				$update_sql .= " external_ref_number = '" . $result['data']['gatewayReference'] . "'";
			}
			if(strlen($update_sql) > 0) DB::update("UPDATE jocom_transaction SET $update_sql WHERE id = $trans_id");
			// Ends: Update Transaction Information when is webcheckout ----------------


			if(count($transac_data_detail) > 0){
				$is_klang = DB::table('jocom_zone_states')->where('states_id', '=', $post['delivery_state'])->where('zone_id', '=', 3)->first();
				if(in_array(date('Y-m-d'), ['2023-04-14', '2023-04-15', '2023-04-16', '2023-04-17', '2023-04-18', '2023-04-19', '2023-04-20']) && (float)$transac_data['total_amount'] >= 150 && $is_klang) $this->FreeProductItem(54196, $transac_data, $transac_data_detail);

				$db_transac_detail = $transac_data_detail;
				array_walk($db_transac_detail, function(&$arg) use ($trans_id){ 
					$arg['transaction_id'] = $trans_id;
					unset($arg['qrcode']);
					unset($arg['is_popbox_available']);
					unset($arg['is_taxable']);
				});
				if(count($db_transac_detail) == 1) $db_transac_detail = $db_transac_detail[0];
				TDetails::insert($db_transac_detail);
				
			}

			if(count($transac_data_group) > 0){
				$db_transac_group = $transac_data_group;
				array_walk($db_transac_group, function(&$arg) use ($trans_id){
					$arg['transaction_id'] = $trans_id;
					unset($arg['qrcode']);
					unset($arg['product_name']);
				});
				DB::table('jocom_transaction_details_group')->insert($db_transac_group);
			}

			if(count($transac_data['flash_sale_product']) > 0){
				array_walk($transac_data['flash_sale_product'], function(&$arg) use ($trans_id){ $arg['transaction_id'] = $trans_id; });
				DB::table('jocom_flashsale_transaction_product')->insert($transac_data['flash_sale_product']);
			}
			
           

			// Start JCashback
			$returnData['cashbackflag'] = 0;
			if($cback == 1) {
				$cashrev = JCashBack::where('user_id', $post['urow']->id)->where('qrcode', $cbacktext)->where('status', 1)->count();
				if($cashrev < 5) {
					if(isset($product_data_row[$cbacktext])){
						$cashback_data = [
							"transaction_id"    => $trans_id,
							"user_id"           => $post['urow']->id,
							"qrcode"            => $cbacktext,
							"sku"               => $product_data_row[$cbacktext]['sku'],
							"product_name"      => $product_data_row[$cbacktext]['name'],
							"jcash_point"       => 800,
							"jcash_point_used"  => 0,
							"created_by"        => $post['urow']->username ? $post['urow']->username : 'API_UPDATE',
							"created_at"        => date("Y-m-d h:i:sa"),
							"updated_by"        => $post['urow']->username ? $post['urow']->username : 'API_UPDATE',
							"updated_at"        => date("Y-m-d h:i:sa")
						];
						$cashback_id = DB::table('jocom_transaction_jcashback')->insertGetId($cashback_data);
						$cashback_data['id'] = $cashback_id;
						$returnData['trans_cashback'] = $cashback_data; // pass JCashback
					}
					$returnData['cashbackflag'] = 1;
				}
			}
			//End JCashback
           
			 // Start Black Friday 24/11/2023 
            //  $tempTrans = Transaction::find('id', $trans_id)->first();
                
             if($post['urow']->username ==='maruthu'){
                //  die('Done');
                    // $transactionvalue = Transaction::Blackfriday($trans_id);
            //       $sum_price    = TDetails::where('transaction_id', '=', $trans_id)->sum('total');
            //       $sum_purchase = number_format($sum_price, 2);
            //      if($sum_purchase>=70 && $sum_purchase<=149){
            //      $returnData['coupon_value'] = MCheckout::Couponcodetempweb($trans_id,'BLACKFRIDAY5');
            //     }else if($sum_purchase>=150){
            // //         //  $lscheck = MCheckout::Couponcodetemp($trans_id,'BLACKFRIDAY10');
            //     }
                 
             }
            
            // End Black Friday 30/11/2023

			if($post['devicetype'] === 'wavpay') $returnData['Wavpay'] = WavpayController::$result;
			$returnData['transaction_id']           = $trans_id;
			$returnData['status']                   = 'success';
			$returnData['message']                  = 'valid';
			$returnData['devicetype']               = $post['devicetype'];
			$returnData['lang']                     = $post['lang'];
			$returnData['cashbacktext']             = $cbacktext;
			// Adedd by YH, reuse previous declare data.
			$returnData['trans_query']              = $transac_data;
			$returnData['trans_detail_query']       = $transac_data_detail;
			$returnData['trans_detail_group_query'] = $transac_data_group;
			$returnData['total_all_weight']         = array_sum(array_column($transac_data_detail, 'total_weight'));
			$returnData['trans_coupon']             = [];
			$returnData['trans_points']             = [];
			$returnData['total_trans_points']       = [];

			$pointTypes = PointType::getActive();
			$earn = [];
			foreach ($pointTypes as $pointType) {
				$pointUser                            = PointUser::getOrCreate($post['urow']->id, $pointType->id, true);
				$pointTransaction                     = new PointTransaction($pointUser);
				$earn['pointsEarn'][$pointType->type] = $pointTransaction->getTransactionPoint($trans_id);
			}

			// Merge all payment gatway url/config data
			$returnData = array_merge($returnData, MCheckout::molpay_conf(), MCheckout::mpay_conf(), MCheckout::Boost_conf(), MCheckout::Revpay_conf(), $earn);

			// POPBOX //
			$PopboxOrder = PopboxOrder::where("transaction_id", $trans_id)->where("status", 1)->first();
			$returnData['is_popbox'] = (count($PopboxOrder) > 0 ? 1 : 0);
			$returnData['popbox_locker'] = (count($PopboxOrder) > 0 ? $PopboxOrder->popbox_locker : '');
			$returnData['popbox_address'] = (count($PopboxOrder) > 0 ? $PopboxOrder->popbox_address : '');
		}

		return $returnData;
	}


	/**
	 * Add details to transaction
	 * @param  Object  $prow                [description]
	 * @param  string  $price_option        [description]
	 * @param  int     $qty                 [description]
	 * @param  array   $returnData          [description]
	 * @param  array   $transac_data        [description]
	 * @param  array   $transac_data_detail [description]
	 * @param  boolean $error               [description]
	 * @param  string  $type                [description]
	 * @return string  $buyer_username      [description]
	 * 
	 * Lite Version of Model MCheckout function
	 * SRC: models/MCheckout.php function scopeAdd_transaction_detail()
	 * Version: 1.05
	 */
	private function ADD_trans_detail($prow, $platform_price, $platform_original_price, $price_option, $qty, &$returnData = [], &$transac_data = [], &$transac_data_detail = [], &$error = false, $type = "", $buyer_username = "") {
		$sp_ind         = 0;
		$sp_cus_grp     = self::spgroup_check($buyer_username);

		if (substr($price_option, 0, 4) == 'SPCL' || $sp_cus_grp > 0) { // Special Customer Product Price
			self::$flash_product_only = false;
			$price_row = DB::table('jocom_sp_product_price AS a')
				->select('a.label_id AS id', 'a.sp_group_id', 'b.label', 'b.label_cn', 'b.label_my', 'b.seller_sku', 'b.p_weight', 'a.price', 'a.price_promo', 'a.qty', 'a.p_referral_fees', 'a.p_referral_fees_type','a.disc_amount', 'a.disc_type', 'a.default', 'a.product_id', 'a.status', 'c.gst', 'c.gst_value')
				->leftjoin('jocom_product_price AS b', 'b.id', '=', 'a.label_id')
				->leftJoin('jocom_products AS c', 'a.product_id', '=', 'c.id')
				->where('c.status', '=', 1)
				->where('a.id', '=', $price_option)
				->where('a.product_id', '=', $prow['id'])
				->first();

			if (count($price_row) <= 0) {
				// if unable retrive the special price for customer rollback to normal/standard price for all user type
				$price_row = DB::table('jocom_product_price AS a')
					->select('a.*', 'b.gst', 'b.gst_value')
					->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
					->where('b.status', '=', 1)
					->where('a.id', '=', $price_option)
					->where('a.product_id', '=', $prow['id'])
					->first();
			} else {
				$sp_ind = $price_row->sp_group_id;
				$price_row->qty = $qty + 1;
				
				if ($price_row->disc_type === '%') {
					$discount = 1 - ($price_row->disc_amount / 100);
					$price_row->price = number_format($price_row->price * $discount, 2);
				} elseif ($price_row->disc_type === 'N'){
					$price_row->price = number_format($price_row->price - $price_row->disc_amount, 2);
				}
			}
		} elseif (preg_match('/(FS|EC|CD|DY)/i', $price_option, $flash_match)) {
			$prefix = substr($price_option, 0, 2);
			$arr = explode("[", substr($price_option, 2), 2);
			$fpid = $arr[0]; // flashsales product id
			$option_id = explode("]", $arr[1])[0];

			$products = DB::table(self::$flash_table[$prefix])->where('id','=', $fpid)->first();
			$fid = $products->fid; // flashsales ID
			$price_row = DB::table('jocom_product_price AS a')
				->select('a.*', 'b.gst', 'b.gst_value')
				->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
				->where('a.id', '=', $option_id)
				->where('a.product_id', '=', $products->product_id)
				->first();
			$price_row->price_promo = $products->promo_price;

			$fc = self::$flash_controller[$prefix]; // flash sales controller name
			$f_stock = $fc::check_flashsales_stock($fid, $option_id, $qty);
			if (!$f_stock) {
				$returnData['message'] = '111';
				$error = true;
			} else {
				$transac_data['flash_sale_product'][] = [
					"flash_sales_id" => $fid,
					"option_id" => $option_id,
					"quantity" => $qty,
					"created_at" => date("Y-m-d h:i:s")
				];
			}
		}else {
			self::$flash_product_only = false;
			$price_row = DB::table('jocom_product_price AS a')->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id');
			if(in_array($transac_data['device_platform'], array("ios","android"))) $price_row = $price_row->where('b.status', '=', 1);
			$price_row = $price_row->select('a.*', 'b.gst', 'b.gst_value')->where('a.id', '=', $price_option)->where('a.product_id', '=', $prow['id'])->first();
		}

		// YH: Really Hard to OPT these cuz it involve the package product single product checking
		$dl_row = DB::table('jocom_product_delivery')
			->where('product_id', '=', $prow['id'])
			->whereIn('zone_id', self::$buyer_zone)
			->select('zone_id', 'price')
			->first();
        

		if ($dl_row != null && $price_row != null && !$error) { // Product Price and Delivery fees is avaliable proceed to create the Transaction Details Data
			$foreign_currency = '';
			if($transac_data['invoice_bussines_currency'] == 'USD'){ // Overide Price If Business in Foreign Currecy
				$price_row->price = CurrencyController::getRate($transac_data['invoice_bussines_currency'], 'MYR', $price_row->foreign_price);
				$price_row->price_promo = CurrencyController::getRate($transac_data['invoice_bussines_currency'], 'MYR', $price_row->foreign_price_promo);
				$foreign_currency = $transac_data['invoice_bussines_currency'];
			}

			// Get product Category
			$cat_ids = DB::table('jocom_categories')->where('product_id', '=', $prow['id'])->lists('category_id', 'main');
			$cat1 = (count($cat_ids) > 0 && isset($cat_ids[1]) ? $cat_ids[1] : '');
			$cat2 = (count($cat_ids) > 0 && isset($cat_ids[0]) ? $cat_ids[0] : '');
			$cat3 = (count($cat_ids) > 0 ? implode(', ', $cat_ids) : '');


			// DEFINE SELLER //
			if(isset($transac_data['delivery_state_id'])){
				$StateID = $transac_data['delivery_state_id'];
			}else{
				$City = City::find($transac_data['delivery_city_id']);
				$StateID = $City->state_id;
			}
			$DeliveryState = State::find($StateID);

			$pid = $prow['id'];
			$srow = DB::table('jocom_product_seller AS JPS') // if have record pick the latest one
				->leftJoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
				->leftJoin('jocom_country_states AS JCS', 'JCS.id', '=', 'JS.state')
				->where("JPS.product_id", $pid)
				->where("JPS.activation", 1)
				->where("JCS.region_id", $DeliveryState->region_id)
				->select('JPS.seller_id', 'JCS.region_id', 'JS.gst_reg_num', 'JS.parent_seller', 'JS.id', 'JS.username')
				->orderBy('JPS.seller_id', 'desc')
				->first();

			if($srow === null){ // Pick older and ignore region id, AS default seller for product
				$srow = DB::select("
					SELECT JS.id, JS.gst_reg_num, JS.parent_seller, JS.username, JPS.seller_id
					FROM (
						SELECT JPS.seller_id FROM jocom_product_seller AS JPS
						WHERE JPS.activation = 1 AND JPS.product_id = $pid LIMIT 1
					) AS JPS
					LEFT JOIN jocom_seller AS JS ON JS.id = JPS.seller_id
					LIMIT 1
				");
				if(isset($srow[0])) $srow = $srow[0];
			}
			// DEFINE SELLER //

			if($platform_price != 0) $price_row->price_promo = (float)$platform_price;
			$tempitem  = (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price);
			$temptotal = $tempitem * $qty;
			$original_price = (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price : 0);

			$temp_gst_rate   = 0;
			$temp_gst_amount = 0;
			$parent_seller     = 0;
			$parent_gst_amount = 0;
			$item_selling_price = $tempitem;
			$is_pricerowgst = (isset($price_row->gst) && $price_row->gst == 2 ? 1 : 0);

			if (self::$gst_status == '1') {
				// Calculate tax on total amount before tax
				if(self::$gstTotalComputeTax){
					$item_selling_price = round(($is_pricerowgst ? $tempitem * (($price_row->gst_value + 100) / 100) : $tempitem), 2);
					// WITH GST CALCULATION
					$temp_gst_rate   = ($is_pricerowgst ? $price_row->gst_value : 0); // Price before GST (Exclusive) = Price After GST  / 1.06
					
					$temp_before_gst_amount = round(($is_pricerowgst ? ($item_selling_price * $qty) / (($price_row->gst_value + 100) / 100) : ($tempitem * $qty)), 2);
					$temp_gst_amount = round(($is_pricerowgst ? ($item_selling_price * $qty) - $temp_before_gst_amount : 0), 2);
					
					$transac_data['total_amount'] += $temp_before_gst_amount;

					if($price_row->gst == 2) $temptotal = $temp_before_gst_amount;
					
					$NotPromoPrice = round(($is_pricerowgst ? $price_row->price * (($price_row->gst_value + 100) / 100): 0), 2);
					$PromoPrice = round(($is_pricerowgst ? $price_row->price_promo * (($price_row->gst_value + 100) / 100): 0), 2);
					$original_price = (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $NotPromoPrice : $PromoPrice);
				}else{
					$transac_data['total_amount'] += ((isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $qty);
					
					$temp_gst_rate   = ($is_pricerowgst ? $price_row->gst_value : 0);
					$temp_gst_amount = round(($is_pricerowgst ? $price_row->gst_value / 100 * $tempitem : 0), 2) * $qty;
				}
				$transac_data['gst_total'] += $temp_gst_amount;
			}


			if (trim($srow->gst_reg_num) != "") {
				$tempprice = isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price;
				// Calculate tax on total amount before tax
				$fee_price = (isset($price_row->p_referral_fees) ? (isset($price_row->p_referral_fees_type) && $price_row->p_referral_fees_type == 'N' ? $price_row->p_referral_fees : ($price_row->p_referral_fees * ($tempprice) / 100)) : 0);
				$tempgstseller = (self::$gstTotalComputeTax ? $temptotal - $fee_price * $qty : $tempprice - $fee_price);
				// calculate seller gst/input tax regardless gst is inactive
				$temp_gst_sell = ($is_pricerowgst ? $price_row->gst_value : 0);
				$tempgstseller = round($tempgstseller * $temp_gst_sell / 100, 2);
				$tempgstseller = (self::$gstTotalComputeTax ? $tempgstseller : $tempgstseller * $qty);
			} else {
				$tempgstseller = 0;
			}

			// Start: calculate gst for seller parent ----------------
			if ($srow->parent_seller != 0) {
				$parent_seller = $srow->parent_seller;
				$parent_row = DB::table('jocom_seller')->select('*')->where('id', '=', $srow->parent_seller)->first();

				if (trim($parent_row->gst_reg_num) != "") {
					// Calculate tax on total amount before tax
					$parent_gst_rate   = ($is_pricerowgst ? $price_row->gst_value : 0);
					$parent_gst_amount = ($is_pricerowgst ? $price_row->gst_value / 100 * (self::$gstTotalComputeTax ? $temptotal : $tempitem) : 0);
					$parent_gst_amount = round($parent_gst_amount, 2);
					if(!self::$gstTotalComputeTax) $parent_gst_amount = $parent_gst_amount * $qty;
				}
			}
			// End: calculate gst for seller parent ----------------


			if ($price_row->id == '7750' && $buyer_username == 'kkwoodypavilion') { // if KKW product
				$transac_data_detail[] = [
					"product_id"            => $prow['id'],
					"product_name"          => $prow['name'],
					"sku"                   => $prow['sku'],
					"qrcode"                => $prow['qrcode'],
					"category_1"            => $cat1,
					"category_2"            => $cat2,
					"category_3"            => $cat3,
					"price_label"           => $price_row->label,
					"seller_sku"            => $price_row->seller_sku,
					"price"                 => 0,
					"unit"                  => $qty,
					"p_referral_fees"       => $price_row->p_referral_fees,
					"p_referral_fees_type"  => $price_row->p_referral_fees_type,
					"delivery_time"         => ($prow['delivery_time'] == '' ? '24 hours' : $prow['delivery_time']),
					"delivery_fees"         => $dl_row->price,
					"seller_id"             => $srow->id,
					"seller_username"       => (isset($srow->username) ? $srow->username : $srow->seller_id),
					"gst_rate_item"         => 0,
					"gst_amount"            => 0,
					"gst_seller"            => 0,
					"total"                 => 0,
					"transaction_id"        => "",
					"p_option_id"           => $price_row->id,
					"product_group"         => $type,
					"sp_group_id"           => $sp_ind,
					"parent_seller"         => $parent_seller,
					"parent_gst_amount"     => $parent_gst_amount,
					"zone_id"               => $dl_row->zone_id,
					"total_weight"          => ($price_row->p_weight * $qty),
					"is_popbox_available"   => $prow['is_popbox_available'],
					"is_taxable"            => $prow['is_taxable'],
					"action_type"           => null,
				];

				$transac_data['total_amount'] -= ((isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $qty);
				$transac_data['gst_total'] -= $temp_gst_amount;
			} else { // not KWW product
				$gst_ori = 0; // set as 0
				if ($price_row->gst == 2) { // if GST enable set it become value
					$after_gst = $price_row->price * (isset($price_row->gst) && $price_row->gst == 2 ? ($price_row->gst_value + 100) / 100 : 0);
					$gst_ori = (double)$after_gst - (double)$price_row->price;
				}
				$ori_price = $price_row->price;


				/* Foreign Math */
				$foreign_unit_price = ($price_row->foreign_price_promo != 0 ? $price_row->foreign_price_promo : $price_row->foreign_price);
				$foreign_total_amount = $foreign_unit_price * $qty;
				$foreign_actual_price = $price_row->foreign_price;
				$transac_data['foreign_total_amount'] += $foreign_total_amount;
				/* Foreign Math */

				
				/* Add Cost per Item */
				$cost_price = DB::table('jocom_product_price_seller AS JPPS')
					->where('JPPS.seller_id', '=', $srow->id)
					->where('JPPS.activation', '=', 1)
					->where('JPPS.product_price_id', '=', $price_row->id)
					->select('JPPS.*')
					->first();

				$unit_cost = ($cost_price ? $cost_price->cost_price : 0.00);
				$item_price = ($platform_price > 0 ? (float)$platform_price : $item_selling_price);
				/* Add Cost per Item */
				
				$transac_data_detail[] = [
					"product_id"                => $prow['id'],
					"product_name"              => $prow['name'],
					"sku"                       => $prow['sku'],
					"qrcode"                    => $prow['qrcode'],
					"category_1"                => $cat1,
					"category_2"                => $cat2,
					"category_3"                => $cat3,    
					"price_label"               => $price_row->label,
					"seller_sku"                => $price_row->seller_sku,
					"price"                     => $item_price,
					"unit"                      => $qty,
					"p_referral_fees"           => $price_row->p_referral_fees,
					"p_referral_fees_type"      => $price_row->p_referral_fees_type,
					"delivery_time"             => ($prow['delivery_time'] == '' ? '24 hours' : $prow['delivery_time']),
					"delivery_fees"             => $dl_row->price,
					"seller_id"                 => $srow->id,
					"seller_username"           => (isset($srow->username) ? $srow->username : $srow->seller_id),
					"gst_rate_item"             => $temp_gst_rate,
					"gst_amount"                => $temp_gst_amount, //$GstAmount, //$temp_gst_amount,
					"gst_seller"                => $tempgstseller,
					"total"                     => ($platform_price ? $item_price * $qty : $temptotal),
					"transaction_id"            => "",
					"p_option_id"               => $price_row->id,
					"product_group"             => $type,
					"sp_group_id"               => $sp_ind,
					"parent_seller"             => $parent_seller,
					"parent_gst_amount"         => $parent_gst_amount,
					"zone_id"                   => $dl_row->zone_id,
					"total_weight"              => ($price_row->p_weight * $qty),
					"original_price"            => (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price : 0),//$original_price,
					"ori_price"                 => round($ori_price, 2),
					"gst_ori"                   => round($gst_ori, 2),
					"actual_price"              => round($ori_price, 2),
					"actual_total_amount"       => round($ori_price, 2) * $qty,
					"actual_price_gst_amount"   => round($gst_ori, 2),
					"foreign_price"             => $foreign_unit_price,
					"foreign_total"             => $foreign_total_amount,
					"foreign_actual_price"      => $foreign_actual_price,
					"foreign_currency"          => $foreign_currency,
					"cost_unit_amount"          => $unit_cost,
					"cost_amount"               => $unit_cost * $qty,
					"disc_per_unit"             => ($platform_price ? number_format((float)($ori_price - $item_price), 2, '.', '') : number_format((float)($ori_price - $item_selling_price), 2, '.', '')), 
					"disc"                      => 0,
					"platform_original_price"   => ($platform_original_price > 0 ? (float)$platform_original_price : 0.00),
					"is_popbox_available"       => $prow['is_popbox_available'],
					"is_taxable"                => $prow['is_taxable'],
					"action_type"               => null,
				];
			}

			$returnData['item_quantity'] = $qty; // Total item quantity
			
			// allow to checkout even no quantity for 11Street and Lazada and Qoo10
			if ($transac_data['third_party'] != 1 && $transac_data['third_party_lazada'] != 1 && $transac_data['third_party_qoo10'] != 1 && $transac_data['third_party_shopee'] != 1  && $transac_data['third_party_astrogo'] != 1 && $transac_data['third_party_pgmall'] != 1 && (int)$price_row->qty < (int)$qty) {
				$error                 = true;
				$returnData['message'] = '107';
				$returnData['outStockList'][] = [
					"productSkU" => $prow['sku'],
					"productLabel" => $price_row->label,
					"productName" => $prow['name'],
					"productID" => $prow['id'],
				];
			}
		} else {
			$error = true;
			$returnData['message'] = ($price_row !== null && $returnData['message'] !== '111' ? '108' : '109');
		} // end of with product price and delivery fees

		return ["returnData" => $returnData, "transac_data" => $transac_data, "transac_data_detail" => $transac_data_detail, "error" => $error];
	}
	
	private function FreeProductItem($free_product_id, $transac_data, &$transac_data_detail){
		$parent_seller = [54196 => 69, 29149 => 69];
		$price_row = DB::table('jocom_product_price AS a')->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
            ->where('a.status', '=', 1)
            ->where('a.default','=', 1)
            ->where('a.product_id', '=', $free_product_id)
            ->select('a.*', 'b.name', 'b.sku','b.sell_id','delivery_time')
            ->first();

		// Get product Category
		$cat_ids = DB::table('jocom_categories')->where('product_id', '=', $free_product_id)->lists('category_id', 'main');
		$cat1 = (count($cat_ids) > 0 && isset($cat_ids[1]) ? $cat_ids[1] : '');
		$cat2 = (count($cat_ids) > 0 && isset($cat_ids[0]) ? $cat_ids[0] : '');
		$cat3 = (count($cat_ids) > 0 ? implode(', ', $cat_ids) : '');

		$zoneInfo = DB::table('jocom_product_delivery')->where("product_id", $free_product_id)->first();
		$sellerInfo = DB::table('jocom_seller')->where("id", $price_row->sell_id)->first();

		$transac_data_detail[] = [
			"product_id"                => $free_product_id,
			"product_name"              => $price_row->name,
			"sku"                       => $price_row->sku,
			"qrcode"                    => 'JC' . $free_product_id, 
			"category_1"                => $cat1,
			"category_2"                => $cat2,
			"category_3"                => $cat3,
			"price_label"               => $price_row->label,
			"seller_sku"                => $price_row->seller_sku,
			"price"                     => 0,
			"unit"                      => 1,
			"p_referral_fees"           => 0,
			"p_referral_fees_type"      => $price_row->p_referral_fees_type,
			"delivery_time"             => $price_row->delivery_time,
			"delivery_fees"             => 0,
			"seller_id"                 => $sellerInfo->id,
			"seller_username"           => $sellerInfo->username,
			"gst_rate_item"             => 0,
			"gst_amount"                => 0,
			"gst_seller"                => 0,
			"total"                     => 0,
			"transaction_id"            => $transac_data['id'],
			"p_option_id"               => $price_row->id,
			"product_group"             => null,
			"sp_group_id"               => 0,
			"parent_gst_amount"         => 0,
			"parent_seller"             => $parent_seller[$free_product_id],
			"zone_id"                   => $zoneInfo->zone_id,
			"total_weight"              => $price_row->p_weight,
			"original_price"            => 0,
			"ori_price"                 => 0,
			"gst_ori"                   => 0,
			"actual_price"              => 0,
			"actual_total_amount"       => 0,
			"actual_price_gst_amount"   => 0,
			"foreign_price"             => 0,
			"foreign_total"             => 0,
			"foreign_actual_price"      => 0,
			"foreign_currency"          => 0,
			"cost_unit_amount"          => 0,
			"cost_amount"               => 0,
			"disc_per_unit"             => 0,
			"disc"                      => 0,
			"platform_original_price"   => 0,
			"action_type"               => 'FOC',
		];
	}
	
}