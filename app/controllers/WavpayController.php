<?php
use Illuminate\Support\Facades\Response;

class WavpayController extends BaseController{
	private static $ch = false;
	private static $codelist = [
		200 => 'Successful',
		500 => 'Error'
	];
	private static $status_trans = [
		'SUCCESS' => 'completed',
		'PENDING' => 'pending',
		'FAILED' => 'failed',
		'REFUNDED' => 'refund'
	];
	public static $input = [];
	public static $result = [];

	private function Curl($options){
		if(!self::$ch) self::$ch = curl_init();
		curl_setopt_array(self::$ch, $options);
		return curl_exec(self::$ch);
	}

	public function anyInitial($post){
		try{
			// need jocom.my pass the userID + sessionID of wavpay
			$env = (Config::get('constants.ENVIRONMENT') === 'live' ? 'PRO' : 'DEV');
			$cfg = Config::get('constants.WAVPAY_' . $env);
			$field = [
				"callbackUrl" => url('/') . '/wavpay/callback',
				"ctbMerchantAccountIdentifier" => $cfg['id'],
				"ctbPaymentReference" => 'JT-' . $post['TID'], // use jocom transaction id
				"orderDescription" => "Jocom Transaction on WAVPAY",
				"payableAmount" => number_format((float)$post['amount'], 2, '.', ''),
				"paymentCurrency" => $post['currency'],
				"primaryCustomerEmail" => $post['user']->email, // 'developer@wavpay.net', 
				"primaryCustomerPhoneNumber" => (isset($post['user']->mobile_no) && preg_match('/^[0-9]{10,}+$/', $post['user']->mobile_no) ? $post['user']->mobile_no : $post['delivery_contact_no']), // '601000000007', 
				"redirectionUrl" => url('/') . '/wavpay/redirect',
				"sessionId" => $post['Wavpay_SID'],
				"userId" => $post['Wavpay_UID']
			];
			$hashfield = $field['callbackUrl'] . '|' . $field['ctbMerchantAccountIdentifier'] . '|' . $field['ctbPaymentReference'] . '|' . 
				$field['orderDescription'] . '|' . $field['payableAmount'] . '|' . $field['paymentCurrency'] . '|' . $field['primaryCustomerEmail'] . '|' . 
				$field['primaryCustomerPhoneNumber'] . '|' . $field['redirectionUrl'] . '|' . $field['sessionId'] . '|' . $field['userId'] . '|' . $cfg['key'];
			$field["secureHash"] = hash("sha256", $hashfield);

			$ApiLog = new ApiLog;
			$ApiLog->api = 'WAVPAY_INITIAL_PAYMENT';
			$ApiLog->data = stripslashes(json_encode($field));
			$ApiLog->save();
			self::$result = self::Curl([
				CURLOPT_URL => $cfg['url'] . '/api/merchant_web/payment_initial',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => stripslashes(json_encode($field)),
				CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
			]);
			self::$result = json_decode(self::$result, true);
			if((int)self::$result['Code'] == 200 || (int)self::$result['code'] == 200){
				DB::table('jocom_wavpay_transaction')->insert([
					'trans_id' => self::$result['data']['gatewayReference'],
					'user_id' => $post['Wavpay_UID'],
					'trans_status' => 'PENDING',
					'transaction_id' => $post['TID'],
					'trans_data' => json_encode(Input::all()),
				]);
			}
			return self::$result;
		}catch(Exception $e) { }
	}

	private function Brinkas($cfg, $transaction){
		self::$result = self::Curl([
			CURLOPT_URL => $cfg['brikas']['url'] . '/auth/login',
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING => '',
			CURLOPT_MAXREDIRS => 10,
			CURLOPT_TIMEOUT => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_CUSTOMREQUEST => 'POST',
			CURLOPT_POST => true,
			CURLOPT_POSTFIELDS => stripslashes(json_encode([
				"email" => $cfg['brikas']['email'],
				"password" => $cfg['brikas']['pass'],
			])),
			CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
		]);
		self::$result = json_decode(self::$result, true);
		if((int)self::$result['code'] === 200){
			$td = DB::table('jocom_transaction_details AS trans_details')->leftJoin('jocom_products_category AS cat', 'cat.id', '=', 'trans_details.category_1')->where('trans_details.transaction_id', $transaction->id)->select('trans_details.product_name AS name', 'trans_details.price AS price', 'trans_details.unit AS quantity', 'cat.category_name AS category_name', 'cat.category_descriptions AS category_descriptions')->get();
			$td = json_encode($td);
			$td = json_decode($td, true);
			self::$result = self::Curl([
				CURLOPT_URL => $cfg['brikas']['url'] . '/BINGKAS/bingkas_store_transaction',
				CURLOPT_RETURNTRANSFER => true,
				CURLOPT_ENCODING => '',
				CURLOPT_MAXREDIRS => 10,
				CURLOPT_TIMEOUT => 0,
				CURLOPT_FOLLOWLOCATION => true,
				CURLOPT_CUSTOMREQUEST => 'POST',
				CURLOPT_POST => true,
				CURLOPT_POSTFIELDS => stripslashes(json_encode([
					"selangkah_id" => null,
					"transaction_id" => Input::get('orderId'),
					"merchant_id" => $cfg['id'],
					"source" => "Jocom Transaction on WAVPAY",
					"transaction_ts" => date("Y-m-d H:i:s"),
					"data" => $td,
				])),
				CURLOPT_HTTPHEADER => ['Authorization: Bearer ' . self::$result['data'], 'Content-Type: application/json'],
			]);
		}
	}

	public function anyRedirect(){
		// redirectionUrl For WavPay to redirect Wallet user mobile app back to Merchant
		$env = (Config::get('constants.ENVIRONMENT') === 'live' ? 'PRO' : 'DEV');
		$cfg = Config::get('constants.WAVPAY_' . $env);

		// <postMerchantId>|<postOrderId>|<postStatus>|<postUserId>|<merchant secret key>
		if(strtolower(Input::get('postSecureHash')) === hash('sha256', Input::get('postMerchantId') . '|' . Input::get('postOrderId') . '|' . Input::get('postStatus') . '|' . Input::get('postUserId') . '|' . $cfg['key'])){ // hash is match
			$uID = json_encode(["userID" => preg_replace('/[^\w- ]+/', '', (Input::get('postUserId') ? Input::get('postUserId') : ''))]);
			$CustomerInfo = Customer::where('ref_info', '=', $uID)->first();
			$transaction = Transaction::where('status', '!=', 'completed')->where('external_ref_number', '=', Input::get('postOrderId'))->where('checkout_source', 7)->where('buyer_id', $CustomerInfo->id)->first();
			if($transaction){
				DB::table('jocom_wavpay_transaction')->where('trans_id', Input::get('postOrderId'))->where('transaction_id', $transaction->id)->update([
					'trans_data' => json_encode(Input::all()),
					'trans_status' => Input::get('postStatus'),
					'trans_method' => Input::get('postPaymentMethod'),
				]);
				$transaction->status = (Input::get('postStatus') === 'SUCCESS' ? 'completed' : strtolower(Input::get('postStatus')));
				if(Input::get('postStatus') === 'SUCCESS'){
					$transaction->invoice_date = date('Y-m-d');
					$transaction->save();

					// Maruthu: befoore run through these function, the transaction query/result must be complete. If save query after these action will cause fail instead
					MCheckout::afterTransactionUpdate($transaction->id);
					MCheckout::generateInv($transaction->id);
					MCheckout::generatePO($transaction->id);
					MCheckout::generateDO($transaction->id);
					MCheckout::trans_complete_mailout($transaction);
					self::Brinkas($cfg, $transaction);
				}else{
					$transaction->save();
				}
			}
		}

		return Redirect::to('https://jocom.my/p_respond.php?tranID=' . $transaction->id . '&status=' . strtolower(Input::get('postStatus')));
	}

	public function anyCallback(){
		$env = (Config::get('constants.ENVIRONMENT') === 'live' ? 'PRO' : 'DEV');
		$cfg = Config::get('constants.WAVPAY_' . $env);

		// <merchantId>|<paymentMethod>|<orderId>|<status>|userId|<merchant secret key>
		if(Input::get('secureHash') === hash('sha256', Input::get('merchantId') . '|' . Input::get('paymentMethod') . '|' . Input::get('orderId') . '|' . Input::get('status') . '|' . Input::get('userId') . '|' . $cfg['key'])){ // hash is match
			$uID = json_encode(["userID" => preg_replace('/[^\w- ]+/', '', (Input::get('userId') ? Input::get('userId') : ''))]);
			$CustomerInfo = Customer::where('ref_info', '=', $uID)->first();
			$transaction = Transaction::where('status', '!=', 'completed')->where('external_ref_number', '=', Input::get('orderId'))->where('checkout_source', 7)->where('buyer_id', $CustomerInfo->id)->first();
			if($transaction){
				DB::table('jocom_wavpay_transaction')->where('trans_id', Input::get('orderId'))->where('transaction_id', $transaction->id)->update([
					'trans_data' => json_encode(Input::all()),
					'trans_status' => Input::get('status'),
					'trans_method' => Input::get('paymentMethod'),
				]);
				$transaction->status = self::$status_trans[Input::get('status')];
				if($codelist[Input::get('status')] === 'SUCCESS'){
					$transaction->invoice_date = date('Y-m-d');
					$transaction->save();

					// Maruthu: befoore run through these function, the transaction query/result must be complete. If save query after these action will cause fail instead
					MCheckout::afterTransactionUpdate($transaction->id);
					MCheckout::generateInv($transaction->id);
					MCheckout::generatePO($transaction->id);
					MCheckout::generateDO($transaction->id);
					MCheckout::trans_complete_mailout($transaction);
					self::Brinkas($cfg, $transaction);
				}else{
					$transaction->save();
				}
			}
		}

		return 'OK';
	}

	public function anyQuery($tran_id){
		$env = (Config::get('constants.ENVIRONMENT') === 'live' ? 'PRO' : 'DEV');
		$cfg = Config::get('constants.WAVPAY_' . $env);

		$trans = Transaction::where('status', '!=', 'completed')->where('id', '=', $tran_id)->where('checkout_source', 7)->first();
		if($trans){
			$logi_tran = LogisticTransaction::where('transaction_id', '=', $tran_id)->first();
			$logi_batch = ($logi_tran ? LogisticBatch::where('logistic_id', '=', $logi_tran->id)->where('status', '!=', 0)->get() : false);

			// check logistic batch got status or not
			if(!$logi_batch){
				$hash = hash('sha256', $cfg['id'] . '|' . $trans->external_ref_number . '|' . $cfg['key']);

				self::$result = self::Curl([
					CURLOPT_URL => $cfg['url'] . '/api/merchant_web/query/' . $cfg['id'] . '/' . $trans->external_ref_number . '/' . $hash,
					CURLOPT_SSL_VERIFYPEER => 0,
					CURLOPT_RETURNTRANSFER => 1,
					CURLOPT_CONNECTTIMEOUT => 0,
					CURLOPT_HTTPHEADER => ['Content-Type:application/json'],
					CURLOPT_TIMEOUT => 30
				]);

				$ApiLog = new ApiLog;
				$ApiLog->api = 'WAVPAY_CHECK_PAYMENT';
				$ApiLog->data = json_encode([
					'request_data' => [
						'tran_id' => $tran_id,
						'm_id' => $cfg['id'],
						'm_ref_num' => $trans->external_ref_number,
						'hash' => $tran_id,
					],
					'respond_data' => self::$result,
				]);
				$ApiLog->save();

				$trans->status = self::$status[self::$result['data']['Status']];
				$transaction->save();
			}
		}
	}

	public function anyBingkas(){
		if (Request::isMethod('post') && Input::get('token') === 'iRbJZCpRjBMsTC7M4MX6H7Rtel7UdTNLA9n8cKvKei') {
			$env = (Config::get('constants.ENVIRONMENT') === 'live' ? 'PRO' : 'DEV');
			$cfg = Config::get('constants.WAVPAY_' . $env);
			$d = date('Y-m-d', strtotime("-1 day")); // pick yesterday record instead

			// get yesterday record
			$transaction = Transaction::leftJoin('jocom_wavpay_transaction AS wav', 'wav.transaction_id', '=', 'jocom_transaction.id')->leftJoin('jocom_transaction_details AS details', 'details.transaction_id', '=', 'jocom_transaction.id')->leftJoin('jocom_products_category AS category', 'details.category_1', '=', 'category.id')->where('jocom_transaction.checkout_source', 7)->where('wav.trans_method', '=', 'BINGKAS')->where('jocom_transaction.status', '=', 'completed')->where('jocom_transaction.transaction_date', '>=', $d . ' 00:00:00')->where('jocom_transaction.transaction_date', '<=', $d . ' 23:59:59')->select('wav.trans_method AS trans_method', 'wav.trans_id AS trans_id', 'details.sku AS sku', 'details.product_name AS product_name', 'category.category_name AS category_name', 'details.unit AS unit_qty', 'details.price AS unit_price', 'jocom_transaction.total_amount AS transaction_total', 'jocom_transaction.transaction_date AS transaction_date')->get()->toArray();
			return Response::json($transaction);
		}
	}
}