<?php
require_once app_path('library/barcodemaster/src/Milon/Barcode/DNS1D.php');
use \Milon\Barcode\DNS1D;
use Helper\ImageHelper as Image;
class ApiLogisticDashboardController extends BaseController {
	private $states = false;
	private $t_color = [
		0 => '#8A2BE2',
		1 => '#000000', 
		2 => '#ED2536',
		4 => '#458224',
		'dafault' => '#ED2536',
	];

	private static function seek($array, $key, $needle){ 
		$seek = 0; 
		foreach($array as $k => $v) if(array_key_exists($key, $v) && in_array($needle, $v)) $seek = $array[$k];
		return $seek; 
	}

	private static function arr_unique($arr) {
		sort($arr);
		$curr = $arr[0];
		$uni_arr[] = $arr[0];
		for($i = 0; $i < count($arr); $i++){
			if($curr != $arr[$i]) {
				$uni_arr[] = $arr[$i];
				$curr = $arr[$i];
			}
		}
		return $uni_arr;
	}

	private function generate_TagEnd($diff, $transID, $logic = '$diff > 3'){
		$tagend = "</span>";
		$tagstart = "<span style=color:#ED2536>";
		if($diff == 1){
			$transID = $transID . $tagstart . ' *' . $tagend;
		} elseif ($diff == 2) {
			$transID = $transID . $tagstart . ' **' . $tagend;
		} elseif ($diff == 3) {
			$transID = $transID . $tagstart . ' ***' . $tagend;
		}else{
			if(($logic === '$diff > 3' && $diff > 3) || ($logic === '$diff >= 60' && $diff >= 60))
				$transID = $transID . $tagstart . ' [' . $diff . ']' . $tagend;
		}
		return $transID;
	}

	private function getStates(){
		if(!$this->states){
			$user = DB::table('jocom_sys_admin')->where('username','=', Session::get('username'))->first();
			$region = DB::table('jocom_sys_admin_region AS JSR')
				->select('JSR.*')
				->where('JSR.sys_admin_id', $user->id)
				->where('JSR.status', 1)
				->first();
			$this->states = DB::table('jocom_country_states')->where('region_id', $region->region_id)->lists('name');
		}
		return $this->states;
	}

	// YH: need optimise, terrible 17s load
	/**
	 * Comment: clone function of LogisticController anyDashboardstatistic()
	 *
	 * @api TRUE
	 * @author YEE HAO
	 * @since 30 MAY 2023
	 * @param rangeType Integer
	 * @param startDate String
	 * @param toDate String
	 * @param navigation Integer
	 * @version 1.0
	 * @method GET, POST, any request method
	 * @return JSON ONLY
	 * @used-by ?
	 *
	 * Last Update: 30 MAY 2023
	 */
	public function anyStatistic(){
		$isError = 0;
		$respStatus = 1;
		$data = [];
		
		try{
			$rangeType = Input::get("rangeType");
			$startDate = Input::get("startDate");
			$toDate = Input::get("toDate");
			$navigate = Input::get("navigation");
	 
			switch ($rangeType) {
				case 1: // Daily
					if($navigate == 1){ // LEFT
						$startDate = date('Y-m-d', strtotime(date($startDate) . ' -1 days', time())) . " 00:00:00";
						$toDate = date('Y-m-d', strtotime(date($startDate), time())) . " 23:23:59";
					}else if($navigate == 2){ // RIGHT
						$startDate = date('Y-m-d', strtotime(date($startDate) . ' +1 days', time())) . " 00:00:00";
						$toDate = date('Y-m-d', strtotime(date($startDate), time())) . " 23:23:59";
					}else{
						$startDate = date('Y-m-d', strtotime(date($startDate), time())) . " 00:00:00";
						$toDate = date('Y-m-d', strtotime(date($startDate), time())) . " 23:23:59";
					}
					break;

				case 2: // Weekly
					$day = 7;
					if($navigate == 1){ // LEFT
						$startDate = date('Y-m-d', strtotime(date($startDate) . ' -' . $day . ' days', time())) . " 00:00:00";
						$toDate = date('Y-m-d', strtotime(date($toDate) . ' -' . $day . ' days', time())) . " 23:23:59";
					}else if($navigate == 2){ // RIGHT
						$startDate = date('Y-m-d', strtotime(date($startDate) . ' +' . $day . ' days', time())) . " 00:00:00";
						$toDate = date('Y-m-d', strtotime(date($toDate) . ' +' . $day . ' days', time())) . " 23:23:59";
					}
					break;

				case 3: // Monthly
					if($navigate == 1){ // LEFT
						$startDate = date('Y-m-d', strtotime(date($startDate) . ' first day of last month', time())) . " 00:00:00";
						$toDate = date('Y-m-d', strtotime(date($toDate) . ' last day of last month', time())) . " 23:23:59";
					}else if($navigate == 2){ // RIGHT
						$startDate = date('Y-m-d', strtotime(date($startDate) . ' first day of next month', time())) . " 00:00:00";
						$toDate = date('Y-m-d', strtotime(date($toDate) . ' last day of next month', time())) . " 23:23:59";
					}
					break;

				default:
					break;
			}
			
			$displayStartDate = date("d M Y", strtotime($startDate));
			$displayEndDate = date("d M Y", strtotime($toDate));
			
			$currentdate = date("Y-m-d") . " 23:59:59";
			
			$data['DateSelection'] = [
				"startDate" => $startDate,
				"toDate" => $toDate,
				"displayStartDate" => $displayStartDate,
				"displayEndDate" => $displayEndDate,
				"today" => date("Y-m-d"),
				"WeeklyStartDate" =>  date("Y-m-d", strtotime('monday this week')), 
				"WeeklyEndDate" =>  date("Y-m-d",  strtotime('sunday this week')),
				"MonthStartDate" => date('Y-m-1'),
				"MonthEndDate" => date('Y-m-t')
			];
			
			$TotalPending = LogisticTransaction::getTotalRecordByStatus(0, $startDate, $toDate);
			$TotalUndelivered = LogisticTransaction::getTotalRecordByStatus(1, $startDate, $toDate);
			$TotalPartial = LogisticTransaction::getTotalRecordByStatus(2, $startDate, $toDate);
			$TotalReturned = LogisticTransaction::getTotalRecordByStatus(3, $startDate, $toDate);
			$TotalSending = LogisticTransaction::getTotalRecordByStatus(4, $startDate, $toDate);
			$TotalSent = LogisticTransaction::getTotalRecordByStatus(5, $startDate ,$toDate);
			$TotalCancelled = LogisticTransaction::getTotalRecordByStatus(6, $startDate, $toDate);
			
			$TotalPendingAll = LogisticTransaction::getAllTotalRecordByStatus(0, $currentdate);
			$TotalUndeliveredAll = LogisticTransaction::getAllTotalRecordByStatus(1, $currentdate);
			$TotalPartialAll = LogisticTransaction::getAllTotalRecordByStatus(2, $currentdate);
			$TotalReturnedAll = LogisticTransaction::getAllTotalRecordByStatus(3, $currentdate);
			$TotalSendingAll = LogisticTransaction::getAllTotalRecordByStatus(4, $currentdate);
			$TotalSentAll = LogisticTransaction::getAllTotalRecordByStatus(5, $currentdate);
			$TotalCancelledAll = LogisticTransaction::getAllTotalRecordByStatus(6, $currentdate);

			$totalPendingDelay1Month = LogisticTransaction::getAllTotalDelaybyMonth(0, 1);
			$totalPendingDelay2Month = LogisticTransaction::getAllTotalDelaybyMonth(0, 2);
			$totalPendingDelay3Month = LogisticTransaction::getAllTotalDelaybyMonth(0, 3);

			$totalReturnedDelay1Month = LogisticTransaction::getAllTotalDelaybyMonth(3, 1);
			$totalReturnedDelay2Month = LogisticTransaction::getAllTotalDelaybyMonth(3, 2);
			$totalReturnedDelay3Month = LogisticTransaction::getAllTotalDelaybyMonth(3, 3);
			
			$totalSendingDelay1Month = LogisticTransaction::getAllTotalDelaybyMonth(4, 1);
			$totalSendingDelay2Month = LogisticTransaction::getAllTotalDelaybyMonth(4, 2);
			$totalSendingDelay3Month = LogisticTransaction::getAllTotalDelaybyMonth(4, 3);

			$totalCancelDelay1Month = LogisticTransaction::getAllTotalDelaybyMonth(6, 4);
			$totalCancelDelay2Month = LogisticTransaction::getAllTotalDelaybyMonth(6, 5);
			$totalCancelDelay3Month = LogisticTransaction::getAllTotalDelaybyMonth(6, 6);
			
			$TotalBatchPending = LogisticBatch::getTotalRecordByStatus(0, $startDate, $toDate);
			$TotalBatchSending = LogisticBatch::getTotalRecordByStatus(1, $startDate, $toDate);
			$TotalBatchReturned = LogisticBatch::getTotalRecordByStatus(2, $startDate, $toDate);
			$TotalBatchUndelivered = LogisticBatch::getTotalRecordByStatus(3, $startDate, $toDate);
			$TotalBatchSent = LogisticBatch::getTotalRecordByStatus(4, $startDate, $toDate);
			$TotalBatchCancelled = LogisticBatch::getTotalRecordByStatus(5, $startDate, $toDate);
			
			// REGION UPDATE
			// Region 
			$TotalPendingRegion = LogisticTransaction::getTotalRecordByStatusRegion(0, $startDate, $toDate);
			$TotalUndeliveredRegion = LogisticTransaction::getTotalRecordByStatusRegion(1, $startDate, $toDate);
			$TotalPartialRegion = LogisticTransaction::getTotalRecordByStatusRegion(2, $startDate, $toDate);
			$TotalReturnedRegion = LogisticTransaction::getTotalRecordByStatusRegion(3, $startDate, $toDate);
			$TotalSendingRegion = LogisticTransaction::getTotalRecordByStatusRegion(4, $startDate, $toDate);
			$TotalSentRegion = LogisticTransaction::getTotalRecordByStatusRegion(5, $startDate, $toDate);
			$TotalCancelledRegion = LogisticTransaction::getTotalRecordByStatusRegion(6, $startDate, $toDate);

			$totalPendingDelay1MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(0, 1); 
			$totalPendingDelay2MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(0, 2);
			$totalPendingDelay3MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(0, 3);
			$totalReturnedDelay1MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(3, 1);
			$totalReturnedDelay2MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(3, 2);
			$totalReturnedDelay3MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(3, 3);

			$totalSendingDelay1MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(4, 1);
			$totalSendingDelay2MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(4, 2);
			$totalSendingDelay3MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(4, 3);
			$TotalPendingAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(0, $currentdate);
			$TotalUndeliveredAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(1, $currentdate);
			$TotalPartialAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(2, $currentdate);
			$TotalReturnedAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(3, $currentdate);
			$TotalSendingAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(4, $currentdate);
			$TotalSentAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(5, $currentdate);
			$TotalCancelledAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(6, $currentdate);
			$TotalBatchPendingRegion = LogisticBatch::getTotalRecordByStatusRegion(0, $startDate, $toDate);
			$TotalBatchSendingRegion = LogisticBatch::getTotalRecordByStatusRegion(1, $startDate, $toDate);
			$TotalBatchReturnedRegion = LogisticBatch::getTotalRecordByStatusRegion(2, $startDate, $toDate);
			$TotalBatchUndeliveredRegion = LogisticBatch::getTotalRecordByStatusRegion(3, $startDate, $toDate);
			$TotalBatchSentRegion = LogisticBatch::getTotalRecordByStatusRegion(4, $startDate, $toDate);
			$TotalBatchCancelledRegion = LogisticBatch::getTotalRecordByStatusRegion(5, $startDate, $toDate);
			$username = Input::get('username');
			$states = LogisticTransaction::getStates($username);
			// REGION UPDATE


			$TotalPendingAllplace = LogisticTransaction::select(
				DB::raw("count(id) as total_order"), 
				DB::raw("delivery_city"), 
				DB::raw("delivery_state"), 
				DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location")
			)
			->where('status', 0)
			->groupBy('location')
			->get();
			
			$TotalSendingAllplace = LogisticTransaction::select(
				DB::raw("count(id) as total_order"), 
				DB::raw("delivery_city"), 
				DB::raw("delivery_state"), 
				DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location")
			)
			->where('status', 4)
			->groupBy('location')
			->get();


			$TotalPendingAllplaceRegion = LogisticTransaction::select(                  
				DB::raw("count(id) as total_order"),        
				DB::raw("delivery_city"),       
				DB::raw("delivery_state"),      
				DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location")
			)       
			->where('status', 0)
			->whereIn('delivery_state',$states)
			->groupBy('location')
			->get();        
			$TotalSendingAllplaceRegion = LogisticTransaction::select(      
				DB::raw("count(id) as total_order"),        
				DB::raw("delivery_city"),       
				DB::raw("delivery_state"),      
				DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location")
			)       
			->where('status', 4)
			->whereIn('delivery_state',$states)
			->groupBy('location')
			->get();
			
			
			
			$TotalBatchSentGrouping = LogisticBatch::getTotalRecordDriverSent(4, $startDate, $toDate);
			$collectionDriver = [];
			$LogisticDriver = LogisticDriver::where("is_logistic_dashboard", 1)->whereNotIn("username", ['admin','joshua'])->get();
				
			foreach ($LogisticDriver as $keyDriver => $valueDriver) {
				$totalSent = 0;
				$totalAssign = 0;
				$totalReturn = 0;
				
				$totalSent = LogisticBatch::getTotalStatusByDriver(4,$valueDriver->id,$startDate,$toDate);
				$totalReturn = LogisticBatch::getTotalStatusByDriver(2,$valueDriver->id,$startDate,$toDate);
				$totalAssign = LogisticBatch::getTotalDriverAssigned($valueDriver->id,$startDate,$toDate);
				
				$collectionDriver[] = [
					"driver"	=> $valueDriver->name,
					"assign"	=> $totalAssign,
					"sent"		=> $totalSent,
					"return"	=> $totalReturn,
				];
			}

			// UPDATE REGION
			$admin_username = Input::get('username');       
			$user = DB::table('jocom_sys_admin')->where('username', '=', $admin_username)->first();       
			$region = DB::table('jocom_sys_admin_region AS JSR')        
				->select('JSR.*')               
				->where('JSR.sys_admin_id', $user->id)      
				->where('JSR.status', 1)        
				->first();      
			//Region        
			$TotalBatchSentGroupingRegion = LogisticBatch::getTotalRecordDriverSentRegion(4,$startDate,$toDate);        
			$collectionDriverRegion = [];
			$LogisticDriverRegion = LogisticDriver::where("status", 1)->whereNotIn("username", ['admin','joshua'])->where('region_id', '=', $region->region_id)->get();      
			foreach ($LogisticDriverRegion as $keyDriverRegion => $valueDriverRegion) {     
				$totalSent = 0;     
				$totalAssign = 0;       
				$totalReturn = 0;       
				$totalSent = LogisticBatch::getTotalStatusByDriverRegion(4,$valueDriverRegion->id,$startDate,$toDate);      
				$totalReturn = LogisticBatch::getTotalStatusByDriverRegion(2,$valueDriverRegion->id,$startDate,$toDate);        
				$totalAssign = LogisticBatch::getTotalDriverAssignedRegion($valueDriverRegion->id,$startDate,$toDate);      
						
				$collectionDriverRegion[] = [
					"driver"	=> $valueDriverRegion->name,
					"assign"	=> $totalAssign,
					"sent"		=> $totalSent,
					"return"	=> $totalReturn,
				];
			}
			// UPDATE REGION
			
			
			$returnCollection = [
				"TransactionLogistic" => [
					"pending"		=> $TotalPending,
					"Undelivered"	=> $TotalUndelivered,
					"Partial"		=> $TotalPartial,
					"Returned"		=> $TotalReturned,
					"sending"		=> $TotalSending,
					"Sent"			=> $TotalSent,
					"Cancelled"		=> $TotalCancelled,
				],
				"TransactionLogisticRegion" => [
					"pending"		=> $TotalPendingRegion,
					"Undelivered"	=> $TotalUndeliveredRegion,
					"Partial"		=> $TotalPartialRegion,
					"Returned"		=> $TotalReturnedRegion,
					"sending"		=> $TotalSendingRegion,
					"Sent"			=> $TotalSentRegion,
					"Cancelled"		=> $TotalCancelledRegion,
				],
				"TransactionLogisticAll" => [
					"pendingPlace"		=> $TotalPendingAllplace,
					"sendingPlace"		=> $TotalSendingAllplace,
					"pending"			=> $TotalPendingAll,
					"Undelivered"		=> $TotalUndeliveredAll,
					"Partial"			=> $TotalPartialAll,
					"Returned"			=> $TotalReturnedAll,
					"sending"			=> $TotalSendingAll,
					"Sent"				=> $TotalSentAll,
					"Cancelled"			=> $TotalCancelledAll,
					"Pending1Month"		=> $totalPendingDelay1Month,
					"Pending2Month"		=> $totalPendingDelay2Month,
					"Pending3Month"		=> $totalPendingDelay3Month,
					"Returned1Month"	=> $totalReturnedDelay1Month,
					"Returned2Month"	=> $totalReturnedDelay2Month,
					"Returned3Month"	=> $totalReturnedDelay3Month,
					"Sending1Month"		=> $totalSendingDelay1Month,
					"Sending2Month"		=> $totalSendingDelay2Month,
					"Sending3Month"		=> $totalSendingDelay3Month,
					"Cancel1Month"		=> $totalCancelDelay1Month,
					"Cancel2Month"		=> $totalCancelDelay2Month,
					"Cancel3Month"		=> $totalCancelDelay3Month
				],
				"TransactionLogisticAllRegion" => [
					"pendingPlaceRegion"	=> $TotalPendingAllplaceRegion,
					"sendingPlaceRegion"	=> $TotalSendingAllplaceRegion,
					"pending"				=> $TotalPendingAllRegion,
					"Undelivered"			=> $TotalUndeliveredAllRegion,
					"Partial"				=> $TotalPartialAllRegion,
					"Returned"				=> $TotalReturnedAllRegion,
					"sending"				=> $TotalSendingAllRegion,
					"Sent"					=> $TotalSentAllRegion,
					"Cancelled"				=> $TotalCancelledAllRegion,
					"Pending1Month"			=> $totalPendingDelay1MonthRegion,
					"Pending2Month"			=> $totalPendingDelay2MonthRegion,
					"Pending3Month"			=> $totalPendingDelay3MonthRegion,
					"Returned1Month"		=> $totalReturnedDelay1MonthRegion,
					"Returned2Month"		=> $totalReturnedDelay2MonthRegion,
					"Returned3Month"		=> $totalReturnedDelay3MonthRegion,
					"Sending1Month"			=> $totalSendingDelay1MonthRegion,
					"Sending2Month"			=> $totalSendingDelay2MonthRegion,
					"Sending3Month"			=> $totalSendingDelay3MonthRegion,
				],
				"batchLogistic" => [
					"sending"=>$TotalBatchPending,
					"processing"=>$TotalBatchSending + $TotalBatchReturned + $TotalBatchUndelivered,
					"completed"=>$TotalBatchSent,
				],
				"batchLogisticRegion" => [
					"sending" => $TotalBatchPendingRegion,
					"processing" => $TotalBatchSendingRegion + $TotalBatchReturnedRegion + $TotalBatchUndeliveredRegion,      
					"completed" => $TotalBatchSentRegion,
				],
				"driverBatch" => [
					"TotalBatchSentGrouping" => $collectionDriver,
				],
				"driverBatchRegion" => [
					"TotalBatchSentGroupingRegion" => $collectionDriverRegion,
				],
			];

			$data['TotalStatistic'] = $returnCollection;
		}catch (Exception $ex) {
			$message = $ex->getMessage();
			$isError = 1;
		}

		return Response::json([
			"respStatus" => $respStatus,
			"isError" => $isError,
			"message" => $message,
			"data" => $data
		]);
	}

	/**
	 * Comment: clone function of LogisticController anyDashboardlist()
	 *
	 * @api TRUE
	 * @author YEE HAO
	 * @since 30 MAY 2023
	 * @param status Integer
	 * @version 1.0
	 * @method GET, POST, any request method
	 * @return JSON ONLY
	 * @used-by ?
	 *
	 * Last Update: 30 MAY 2023
	 */
	public function anyList(){
		$status = Input::get("status");
		
		if((int)$status == 4){
			$result = DB::table('logistic_transaction')
				->leftJoin("logistic_batch", 'logistic_batch.logistic_id', '=', 'logistic_transaction.id')
				->leftJoin("jocom_transaction", 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')
				->leftJoin("logistic_driver", 'logistic_batch.driver_id', '=', 'logistic_driver.id')
				->select("logistic_transaction.*", "jocom_transaction.buyer_username as plateform", "logistic_driver.name as assign")
				->where("logistic_transaction.status", $status)
				->groupBy('logistic_transaction.id')
				->get();
		}else if((int)$status == 0){
			$result = DB::table('logistic_transaction')
				->leftJoin("jocom_transaction", 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')
				->select("logistic_transaction.*", "jocom_transaction.buyer_username as plateform")
				->where("logistic_transaction.status", $status)
				->get();
		}else{
			$result = DB::table('logistic_transaction')
				->where("status", $status)
				->get();
		}

		return Response::json($result);
	}

	/**
	 * Comment: clone function of LogisticController anyDashboardlistregion()
	 *
	 * @api TRUE
	 * @author YEE HAO
	 * @since 30 MAY 2023
	 * @param status Integer
	 * @version 1.0
	 * @method GET, POST, any request method
	 * @return JSON ONLY
	 * @used-by ?
	 *
	 * Last Update: 30 MAY 2023
	 */
	public function anyListregion(){
		return Response::json(
			DB::table('logistic_transaction')
				->where("status", Input::get("status"))
				->whereIn('delivery_state', LogisticTransaction::getStates(Session::get('username')))
				->orderby('transaction_id')
				->get()
		);
	}

	/**
	 * Comment: clone function of LogisticController anyDashboardlistdelay()
	 *
	 * @api TRUE
	 * @author YEE HAO
	 * @since 30 MAY 2023
	 * @param status Integer
	 * @param month Integer
	 * @version 1.0
	 * @method GET, POST, any request method
	 * @return JSON ONLY
	 * @used-by ?
	 *
	 * Last Update: 30 MAY 2023
	 */
	public function anyListdelay(){
		return Response::json(LogisticTransaction::getAllListDelay(Input::get("status"), Input::get("month")));
	}

	/**
	 * Comment: clone function of LogisticController anyDashboardlistdelayregion()
	 *
	 * @api TRUE
	 * @author YEE HAO
	 * @since 30 MAY 2023
	 * @param status Integer
	 * @param month Integer
	 * @version 1.0
	 * @method GET, POST, any request method
	 * @return JSON ONLY
	 * @used-by ?
	 *
	 * Last Update: 30 MAY 2023
	 */
	public function anyListdelayregion(){
		return Response::json(LogisticTransaction::getAllListDelayRegion(Input::get("status"), Input::get("month")));       
	}

	/**
	 * Comment: clone function of LogisticController anyDashboardlistdate()
	 *
	 * @api TRUE
	 * @author YEE HAO
	 * @since 30 MAY 2023
	 * @param status Integer
	 * @param startDate String
	 * @param endDate String
	 * @version 1.0
	 * @method GET, POST, any request method
	 * @return JSON ONLY
	 * @used-by ?
	 *
	 * Last Update: 30 MAY 2023
	 */
	public function anyListdate() {
		return Response::json(
			DB::table('logistic_transaction')
				->where("status", Input::get("status"))
				->whereBetween('insert_date', [Input::get("startDate"), Input::get("endDate")])
				->get()
		);
	}

	/**
	 * Comment: clone function of LogisticController anyDashboardlistdateregion()
	 *
	 * @api TRUE
	 * @author YEE HAO
	 * @since 30 MAY 2023
	 * @param status Integer
	 * @param startDate String
	 * @param endDate String
	 * @param username String
	 * @version 1.0
	 * @method GET, POST, any request method
	 * @return JSON ONLY
	 * @used-by ?
	 *
	 * Last Update: 30 MAY 2023
	 */
	public function anyListdateregion() {
		return Response::json(
			DB::table('logistic_transaction')     
				->where("status", Input::get("status"))
				->whereBetween('insert_date', [Input::get("startDate"), Input::get("endDate")])
				->whereIn('delivery_state', LogisticTransaction::getStates(Session::get('username')))
				->orderby('transaction_id')
				->get()
		);     
	}


	// Local BKP Copy, optimise organize code from LogisticController Dashboard function
	// public function anyDashboard(){
	// 	$admin_username = Session::get('username');
	// 	$user = DB::table('jocom_sys_admin')->where('username', '=', $admin_username)->first();
	// 	$region = DB::table('jocom_sys_admin_region AS JSR')
	// 		->select('JSR.*')
	// 		->where('JSR.sys_admin_id', $user->id)
	// 		->where('JSR.status', 1)
	// 		->first();
	// 	$regionName = DB::table('jocom_region')->where('id', '=', $region->region_id)->first();

	// 	return View::make('logistic.logistic_dashboard', ['region_id'=> $region->region_id, 'region_name' => $regionName->region]);
	// }

	// public function anyDashboardwh(){
	// 	$admin_username = Session::get('username');
	// 	$user = DB::table('jocom_sys_admin')->where('username', '=', $admin_username)->first();
	// 	$region = DB::table('jocom_sys_admin_region AS JSR')
	// 		->select('JSR.*')
	// 		->where('JSR.sys_admin_id', $user->id)
	// 		->where('JSR.status', 1)
	// 		->first();
	// 	$regionName = DB::table('jocom_region')->where('id', '=', $region->region_id)->first();

	// 	return View::make('logistic.logistic_dashboardwh', ['region_id'=> $region->region_id, 'region_name' => $regionName->region]);
	// }

	// public function anyDashboardcsv(){
	// 	$status = $_GET['status'];
	// 	$startDate = $_GET['startDate'];
	// 	$endDate = $_GET['endDate'];
	// 	$period = $_GET['month'];

	// 	//status, period
	// 	if ($period!='' && $status!='') {
	// 		$list = LogisticTransaction::getAllListDelay($status, $period);
	// 		$data = json_decode(json_encode($list), true);
	// 		switch ($status){
	// 			case '0':
	// 				$status = 'Pending';
	// 				break;
	// 			case '1':
	// 				$status = 'Undelivered';
	// 				break;
	// 			case '2':
	// 				$status = 'Partial Sent';
	// 				break;
	// 			case '3':
	// 				$status = 'Returned';
	// 				break;
	// 			case '4':
	// 				$status = 'Sending';
	// 				break;
	// 			case '5':
	// 				$status = 'Sent';
	// 				break;
	// 		}

	// 		$ldate = date('Y-m-d')."-".$status.'-'.$period;
	// 		$path = Config::get('constants.CSV_FILE_PATH');
	// 		Excel::create($ldate, function($excel) use($data) {
	// 			$excel->sheet('Sheet 1', function($sheet) use($data) {
	// 				$sheet->fromArray($data);
	// 			});
	// 		})->download('xls');
	// 	}

		

	// 	//status, startdate, enddate
	//    if($status != '' && $startDate != '' && $endDate != '') {
	// 		$result = DB::table('logistic_transaction')
	// 			->where("status", $status)
	// 			->whereBetween('insert_date', array($startDate, $endDate))
	// 			->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')->get();
	// 		$data = json_decode(json_encode($result), true);
	// 		switch ($status) {
	// 			case '0':
	// 				$status = 'Pending';
	// 				break;
	// 			case '1':
	// 				$status = 'Undelivered';
	// 				break;
	// 			case '2':
	// 				$status = 'Partial Sent';
	// 				break;
	// 			case '3':
	// 				$status = 'Returned';
	// 				break;
	// 			case '4':
	// 				$status = 'Sending';
	// 				break;
	// 			case '5':
	// 				$status = 'Sent';
	// 				break;
	// 		}

	// 		$start = explode(" ", $startDate);
	// 		$startDate = $start[0]; 
	// 		$end = explode(" ", $endDate);
	// 		$endDate= $end[0]; 
	// 		$ldate = $status.'('.$startDate.' '.$endDate.')';
	// 		$path = Config::get('constants.CSV_FILE_PATH');

	// 		Excel::create($ldate, function($excel) use($data) {
	// 			$excel->sheet('Sheet 1', function($sheet) use($data) {
	// 				$sheet->fromArray($data);
	// 			});
	// 		})->download('csv');
	// 	}

	// 	//status
	// 	if ($status!='') {
	// 		if((int)$status == 4){
	// 			$result = DB::table('logistic_transaction')
	// 				->leftJoin("logistic_batch", 'logistic_batch.logistic_id', '=', 'logistic_transaction.id')
	// 				->leftJoin("jocom_transaction", 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')
	// 				->leftJoin("logistic_driver", 'logistic_batch.driver_id', '=', 'logistic_driver.id')
	// 				->where("logistic_transaction.status", $status)
	// 				->select('logistic_transaction.transaction_id as transaction_id', 'logistic_transaction.insert_date as insert_date', 'jocom_transaction.buyer_username as plateform', 'logistic_transaction.delivery_state as delivery_state', 'logistic_driver.name as assign')
	// 				->groupBy('logistic_transaction.id')
	// 				->get();
	// 		}else if((int)$status == 0){
	// 			$result = DB::table('logistic_transaction')
	// 				->leftJoin("jocom_transaction", 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')
	// 				->where("logistic_transaction.status", $status)
	// 				->select('logistic_transaction.transaction_id as transaction_id', 'logistic_transaction.insert_date as insert_date', 'jocom_transaction.buyer_username as plateform', 'logistic_transaction.delivery_state as delivery_state', 'logistic_transaction.delivery_city as delivery_city')
	// 				->get();
	// 		}else{
	// 			$result = DB::table('logistic_transaction')
	// 				->where("status", $status)
	// 				->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')
	// 				->get();
	// 		}

	// 		$data = json_decode(json_encode($result), true);

	// 		switch ($status) {
	// 			case '0':
	// 				$status = 'Pending';
	// 				break;
	// 			case '1':
	// 				$status = 'Undelivered';
	// 				break;
	// 			case '2':
	// 				$status = 'Partial Sent';
	// 				break;
	// 			case '3':
	// 				$status = 'Returned';
	// 				break;
	// 			case '4':
	// 				$status = 'Sending';
	// 				break;
	// 			case '5':
	// 				$status = 'Sent';
	// 				break;
	// 		}

	// 		$ldate = date('Y-m-d') . "-" . $status;
	// 		$path = Config::get('constants.CSV_FILE_PATH');
	// 		Excel::create($ldate, function($excel) use($data) {
	// 			$excel->sheet('Sheet 1', function($sheet) use($data) {
	// 				$sheet->fromArray($data);
	// 			});
	// 		})->download('csv');
	// 	}
	// }

	// public function anyDashboardcsvregion() {
	// 	$status = $_GET['status'];
	// 	$startDate = $_GET['startDate'];
	// 	$endDate = $_GET['endDate'];
	// 	$period = $_GET['month'];


	// 	//status, period
	// 	if ($period!='' && $status!='') {
	// 		$list = LogisticTransaction::getAllListDelayRegion($status, $period);
	// 		$data = json_decode(json_encode($list), true);
	// 		switch ($status){
	// 			case '0':
	// 				$status = 'Pending';
	// 				break;
	// 			case '1':
	// 				$status = 'Undelivered';
	// 				break;
	// 			case '2':
	// 				$status = 'Partial Sent';
	// 				break;
	// 			case '3':
	// 				$status = 'Returned';
	// 				break;
	// 			case '4':
	// 				$status = 'Sending';
	// 				break;
	// 			case '5':
	// 				$status = 'Sent';
	// 				break;
	// 		}

	// 		$ldate = date('Y-m-d')."-".$status.'-'.$period;
	// 		$path = Config::get('constants.CSV_FILE_PATH');
	// 		Excel::create($ldate, function($excel) use($data) {
	// 			$excel->sheet('Sheet 1', function($sheet) use($data) {
	// 				$sheet->fromArray($data);
	// 			});
	// 		})->download('csv');
	// 	}

	// 	//status, startdate, enddate
	// 	if($status!='' && $startDate!='' && $endDate!='') {
	// 		$username = Session::get('username'); 
	// 		$states = LogisticTransaction::getStates($username);
	// 		$result = DB::table('logistic_transaction')
	// 			->where("status", $status)
	// 			->whereBetween('insert_date', array($startDate, $endDate))
	// 			->whereIn("delivery_state", $states)
	// 			->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')
	// 			->orderby('transaction_id')
	// 			->get();

	// 		$data = json_decode(json_encode($result), true);

	// 		switch ($status) {
	// 			case '0':
	// 				$status = 'Pending';
	// 				break;
	// 			case '1':
	// 				$status = 'Undelivered';
	// 				break;
	// 			case '2':
	// 				$status = 'Partial Sent';
	// 				break;
	// 			case '3':
	// 				$status = 'Returned';
	// 				break;
	// 			case '4':
	// 				$status = 'Sending';
	// 				break;
	// 			case '5':
	// 				$status = 'Sent';
	// 				break;
	// 		}

	// 		$start = explode(" ", $startDate);
	// 		$startDate = $start[0]; 
	// 		$end = explode(" ", $endDate);
	// 		$endDate = $end[0]; 
	// 		$ldate = $status.'('.$startDate.' '.$endDate.')';
	// 		$path = Config::get('constants.CSV_FILE_PATH');
	// 		Excel::create($ldate, function($excel) use($data) {
	// 			$excel->sheet('Sheet 1', function($sheet) use($data) {
	// 				$sheet->fromArray($data);
	// 			});
	// 		})->download('csv');
	// 	}

	// 	//status
	// 	if ($status != '') {
	// 		$username = Session::get('username'); 
	// 		$states = LogisticTransaction::getStates($username);
	// 		$result = DB::table('logistic_transaction')
	// 			->where("status", $status)
	// 			->whereIn("delivery_state", $states)
	// 			->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')
	// 			->orderby('transaction_id')
	// 			->get();
	// 		$data = json_decode(json_encode($result), true);

	// 		switch ($status) {
	// 			case '0':
	// 				$status = 'Pending';
	// 				break;
	// 			case '1':
	// 				$status = 'Undelivered';
	// 				break;
	// 			case '2':
	// 				$status = 'Partial Sent';
	// 				break;
	// 			case '3':
	// 				$status = 'Returned';
	// 				break;
	// 			case '4':
	// 				$status = 'Sending';
	// 				break;
	// 			case '5':
	// 				$status = 'Sent';
	// 				break;
	// 		}

	// 		$ldate = date('Y-m-d')."-".$status;
	// 		$path = Config::get('constants.CSV_FILE_PATH');
	// 		Excel::create($ldate, function($excel) use($data) {
	// 			$excel->sheet('Sheet 1', function($sheet) use($data) {
	// 				$sheet->fromArray($data);
	// 			});
	// 		})->download('xls');
	// 	}
	// }

	// public function anyDashboarddayview(){
	// 	return View::make('logistic.logistic_dashboard_dayview');
	// }

	// public function anyDashboardstatisticdayview(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$data = array(); 
	// 	$status = 1;


	// 	try{
	// 		$TotalPending = LogisticTransaction::getTotaltranslistingbystatus(0);
	// 		$TotalSending = LogisticTransaction::getTotaltranslistingbystatus(4);
	// 		$TotalBatchPending1day = LogisticTransaction::getTotalbatchPending(1);
	// 		$TotalBatchPending2day = LogisticTransaction::getTotalbatchPending(2);
	// 		$TotalBatchPending3day = LogisticTransaction::getTotalbatchPending(3);

	// 		$TotalBatchtoday    = LogisticTransaction::getTotalbatchtoday();
	// 		$TotalBatchPending  = LogisticTransaction::getTotalbatchbystatus(0);
	// 		$TotalBatchSent     = LogisticTransaction::getTotalbatchbystatus(4);
	// 		$TotalUndelivered   = $TotalBatchtoday - $TotalBatchSent;
	// 		$TotalBatchPending  = $TotalBatchtoday;

	// 		$driverresult = DB::select('select LDT.driverid,LDT.team_sequence,LDT.team_bg_colorcode,LDT.seq_bg_colorcode,LD.name from logistic_driver_team LDT LEFT JOIN logistic_driver LD ON LD.id = LDT.driverid where LDT.status=1 order by LDT.team_sequence ASC');
	// 		$callarray = array();
	// 		$calltransactions = array();
	// 		$startdate = Date('Y-m-d', strtotime(date('l') == 'Monday' ? "-3 days" : "-2 days")) . " 23:59:59";
			
			 
	// 		foreach ($driverresult  as  $value) {
	// 			$callarray[] = [
	// 				'driverid'   => $value->driverid,
	// 				'teamseque'  => $value->team_sequence,
	// 				'drivername' => $value->name,
	// 				'colorlight' => $value->team_bg_colorcode,
	// 				'colordark'  => $value->seq_bg_colorcode,
	// 			];
				
	// 			$driverid = $value->driverid;

	// 			$result = LogisticTransaction::getBatchdetails($value->driverid);

	// 			foreach ($result as  $row) {
	// 				$tstatus = 0;
	// 				$Transactionresult = LogisticTransaction::getTransactionID($row->logistic_id);
	// 				$result_driver = LogisticTransaction::getBatchIDStatus($row->logistic_id);
	// 				$tstatus = $result_driver->status;

	// 				$calltransactions[] = [
	// 					'driver_id'		=> $driverid,
	// 					'transactionid'	=> $Transactionresult->transaction_id,
	// 					'transcolor'	=> $this->t_color[(in_array($tstatus, [0, 1, 2, 4]) ? $tstatus : 'default')],
	// 					'status'		=> $tstatus,
	// 				];
	// 			}
	// 			$resultpending = LogisticTransaction::getBatchdetailsPendingDay($value->driverid);

	// 			$icount = 0; 

	// 			foreach ($resultpending as  $rowpending) {
	// 				$diff = 0;
	// 				$transID = 0;
	// 				$tstatus_01 = 0;
	// 				$assigndate = "";

	// 				$Transactionresult_01 = LogisticTransaction::getTransactionID($rowpending->logistic_id);
	// 				$assigndate = $rowpending->assign_date;
	// 				$transID = $Transactionresult_01->transaction_id;
	// 				$result_driver_01 = LogisticTransaction::getBatchIDStatus($rowpending->logistic_id);
	// 				$tstatus_01 = $result_driver_01->status;


	// 				$first_date = strtotime($startdate);
	// 				$second_date = strtotime($assigndate);
	// 				$offset = $second_date - $first_date; 
	// 				$diff = abs(floor($offset / 60 / 60 / 24));
	// 				if($diff == 0) $diff = 1;

	// 				$calltransactions[] = [
	// 					'driver_id'		=> $driverid,
	// 					'transactionid'	=> $this->generate_TagEnd($diff, $transID),
	// 					'transcolor'	=> $this->t_color[(in_array($tstatus_01, [0, 1, 2, 4]) ? $tstatus_01 : 'default')],
	// 					'status'		=> $tstatus_01,
	// 				];
	// 			}
	// 		}

	// 		$data['TransactionLogistic'] = [
	// 			"TotalBatchPending1day" => $TotalBatchPending1day,
	// 			"TotalBatchPending2day" => $TotalBatchPending2day,
	// 			"TotalBatchPending3day" => $TotalBatchPending3day,
	// 			"TotalPending" => $TotalPending,
	// 			"TotalSending" => $TotalSending,
	// 			"TotalBatchPending" =>  $TotalBatchPending,
	// 			"TotalBatchSent" => $TotalBatchSent,
	// 			"TotalUndelivered" => abs($TotalUndelivered),
	// 			"DriverDetails" => $callarray,
	// 			"BatchDetails"  => $calltransactions
	// 		];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return [
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"data" => $data
	// 		];
	// 	}
	// }
	
	// public function anyDashboardregion(){
	// 	return View::make('logistic.logistic_dashboard_region');
	// }

	// public function anyDashboardstatisticregion(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$data = []; 
	// 	$status = 1;


	// 	$MalaysiaCountryID = 458;
	// 	$regionid = 0;
	// 	$region = "";
		
	// 	$SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	// 	$stateName = [];

	// 	foreach ($SysAdminRegion as  $value) {
	// 		$regionid = $value;
	// 		$State = State::getStateByRegion($value);
	// 		foreach ($State as $keyS => $valueS) $stateName[] = $valueS->name;
	// 	}


	// 	if (isset($regionid) && $regionid ==0){
	// 		$region = "All Region";
	// 	} else{
	// 		$resultregion = LogisticTransaction::getDriverRegionName($regionid);
	// 		$region = $resultregion->region;
	// 	}


	// 	try{
	// 		$TotalPending = LogisticTransaction::getTotaltranslistingbystatusRegion(0, $regionid, $stateName);
	// 		$TotalSending = LogisticTransaction::getTotaltranslistingbystatusRegion(4, $regionid, $stateName);
	// 		$TotalBatchPending1day = LogisticTransaction::getTotalbatchPendingRegion(1, $regionid, $stateName);
	// 		$TotalBatchPending2day = LogisticTransaction::getTotalbatchPendingRegion(2, $regionid, $stateName);
	// 		$TotalBatchPending3day = LogisticTransaction::getTotalbatchPendingRegion(3, $regionid, $stateName);

	// 		$TotalBatchtoday    = LogisticTransaction::getTotalbatchtodayRegion($regionid, $stateName);
	// 		$TotalBatchPending  = LogisticTransaction::getTotalbatchbystatusRegion(0, $regionid, $stateName);
	// 		$TotalBatchSent     = LogisticTransaction::getTotalbatchbystatusRegion(4, $regionid, $stateName);
	// 		$TotalUndelivered   = $TotalBatchtoday - $TotalBatchSent;
	// 		$TotalBatchPending  = $TotalBatchtoday;

	// 		if (isset($regionid) && $regionid == 0){
	// 			$driverresult = DB::select('select LDT.driverid,LDT.team_sequence,LDT.team_bg_colorcode,LDT.seq_bg_colorcode,LD.name from logistic_driver_team LDT LEFT JOIN logistic_driver LD ON LD.id = LDT.driverid where LDT.status=1 order by LDT.region_id,LDT.team_sequence,LD.id ASC');
	// 		} else {
	// 			$driverresult = DB::select('select LDT.driverid,LDT.team_sequence,LDT.team_bg_colorcode,LDT.seq_bg_colorcode,LD.name from logistic_driver_team LDT LEFT JOIN logistic_driver LD ON LD.id = LDT.driverid where LDT.status=1 and LD.region_id=' . $regionid . ' order by LDT.region_id,LDT.team_sequence,LD.id ASC');
	// 		}
					 
	// 		$callarray = array();
	// 		$calltransactions = array();

	// 		$startdate = Date('Y-m-d', strtotime(date('l') == 'Monday' ? "-3 days" : "-2 days")) . " 23:59:59";

			 
	// 		foreach ($driverresult  as  $value) {
	// 			$callarray[] = [
	// 				'driverid'   => $value->driverid,
	// 				'teamseque'  => $value->team_sequence,
	// 				'drivername' => $value->name,
	// 				'regionname' => $region,
	// 				'colorlight' => $value->team_bg_colorcode,
	// 				'colordark'  => $value->seq_bg_colorcode,
	// 			];
				
	// 			$driverid = $value->driverid;

	// 			$result = LogisticTransaction::getBatchdetails($value->driverid);

	// 			foreach ($result as $row) {
	// 				$tstatus = 0;
	// 				$Transactionresult = LogisticTransaction::getTransactionID($row->logistic_id);
	// 				$result_driver = LogisticTransaction::getBatchIDStatus($row->logistic_id);
	// 				$tstatus = $result_driver->status;

	// 				$calltransactions[] = [
	// 					'driver_id'			=> $driverid,
	// 					'transactionid'		=> $Transactionresult->transaction_id,
	// 					'transvalid'		=> $Transactionresult->transaction_id,
	// 					'transcolor'		=> $this->t_color[(in_array($tstatus, [0, 1, 2, 4]) ? $tstatus : 'default')],
	// 					'status'			=> $tstatus,
	// 				];
	// 			}

	// 			// Current date delivery
	// 			$resultCurrent = "";
	// 			$resultCurrent = LogisticTransaction::getBatchdetailsDeliveryDay($value->driverid);
	// 			$icount_1 = 0;
	// 			foreach ($resultCurrent as $rowCurrent) {
	// 				$diff_01 = 0;
	// 				$transID_1 = 0;
	// 				$tstatus_02 = 0;
	// 				$assigndate_01 = "";

	// 				$Transactionresult_02 = LogisticTransaction::getTransactionID($rowCurrent->logistic_id);
	// 				$assigndate_01 = $rowCurrent->assign_date;
	// 				$transID_1 = $Transactionresult_02->transaction_id;
	// 				$transID_1_01 = $Transactionresult_02->transaction_id;
	// 				$result_driver_02 = LogisticTransaction::getBatchIDStatus($rowCurrent->logistic_id);
	// 				$tstatus_02 = $result_driver_02->status;


	// 				$first_date_01 = strtotime($startdate);
	// 				$second_date_01 = strtotime($assigndate_01);
	// 				$offset_01 = $second_date_01-$first_date_01; 
	// 				$diff_01 = abs(floor($offset_01/60/60/24));
	// 				if($diff_01 == 0) $diff_01 = 1;

	// 				if(!is_array(self::seek($calltransactions, 'transvalid', $transID_1_01))) 
	// 					$calltransactions[] = [
	// 						'driver_id'			=> $driverid,
	// 						'transactionid'		=> $this->generate_TagEnd($diff_01, $transID_1),
	// 						'transvalid'		=> $transID_1_01,
	// 						'transcolor'		=> $this->t_color[(in_array($tstatus_02, [0, 1, 2, 4]) ? $tstatus_02 : 'default')],
	// 						'status'			=> $tstatus_02,
	// 					];
	// 			}
				 
	// 			// Returned Batch but Pending
	// 			$resultReturned = DB::table('logistic_transaction AS LT')
	// 				->select('LT.id','LT.transaction_id','LB.assign_date')
	// 				->leftJoin('logistic_batch AS LB','LB.logistic_id','=','LT.id')
	// 				->where('LB.driver_id', '=', $value->driverid)
	// 				->where('LT.status', '=', 3)
	// 				->where('LB.status', '=', 2)
	// 				->orderby('LT.insert_date','DESC')
	// 				->get();

	// 			$icount_3 = 0;


	// 			foreach ($resultReturned as  $rowReturned) {
	// 				$diff_03 = 0;
	// 				$transID_3 = 0;
	// 				$tstatus_03_01 = 0;
	// 				$assigndate_02 = "";

	// 				$validResult = LogisticTransaction::getValidTransaction($rowReturned->transaction_id);

	// 				if($validResult == 0){
	// 					$assigndate_03 = $rowReturned->assign_date;
	// 					$transID_3 = $rowReturned->transaction_id;
	// 					$transID_1_03 = $rowReturned->transaction_id;
	// 					$result_driver_03 = LogisticTransaction::getBatchIDStatus($rowReturned->id);
	// 					$tstatus_03_01 = $result_driver_03->status;

	// 					$first_date_03 = strtotime($startdate);
	// 					$second_date_03 = strtotime($assigndate_03);
	// 					$offset_03 = $second_date_03-$first_date_03; 
	// 					$diff_03 = abs(floor($offset_03/60/60/24));
	// 					if($diff_03 == 0) $diff_03 = 1;

	// 					if(!is_array(self::seek($calltransactions, 'transvalid', $transID_1_03)))
	// 						$calltransactions[] = [
	// 							'driver_id'			=> $driverid,
	// 							'transactionid'		=> $this->generate_TagEnd($diff_03, $transID_3),
	// 							'transvalid'		=> $transID_1_03,
	// 							'transcolor'		=> $this->t_color[(in_array($tstatus_03_01, [0, 1, 2, 4]) ? $tstatus_03_01 : 'default')],
	// 							'status'			=> $tstatus_03_01,
	// 						];
	// 				}
	// 			}

	// 			// Pending Order..... 
	// 			$resultpending = "";
	// 			$resultpending = LogisticTransaction::getBatchdetailsPendingDay($value->driverid);
	// 			$icount = 0; 
	// 			foreach ($resultpending as  $rowpending) {
	// 				$diff = 0;
	// 				$transID = 0;
	// 				$tstatus_01 = 0;
	// 				$assigndate = "";

	// 				$Transactionresult_01 = LogisticTransaction::getTransactionID($rowpending->logistic_id);
	// 				$assigndate = $rowpending->assign_date;
	// 				$transID = $Transactionresult_01->transaction_id;
	// 				$transIDpending = $Transactionresult_01->transaction_id;
	// 				$result_driver_01 = LogisticTransaction::getBatchIDStatus($rowpending->logistic_id);
	// 				$tstatus_01 = $result_driver_01->status;


	// 				$first_date = strtotime($startdate);
	// 				$second_date = strtotime($assigndate);
	// 				$offset = $second_date - $first_date;  
	// 				$diff = abs(floor($offset / 60 / 60 / 24));
	// 				if($diff == 0) $diff = 1;


	// 				$calltransactions[] = [
	// 					'driver_id'		=> $driverid,
	// 					'transactionid'	=> $this->generate_TagEnd($diff, $transID),
	// 					'transvalid'	=> $transIDpending,
	// 					'transcolor'	=> $this->t_color[(in_array($tstatus_01, [0, 1, 2, 4]) ? $tstatus_01 : 'default')],
	// 					'status'		=> $tstatus_01,
	// 				];
	// 			}
	// 		}

	// 		$data['TransactionLogistic'] = [
	// 			"TotalBatchPending1day" => $TotalBatchPending1day,
	// 			"TotalBatchPending2day" => $TotalBatchPending2day,
	// 			"TotalBatchPending3day" => $TotalBatchPending3day,
	// 			"TotalPending" => $TotalPending,
	// 			"TotalSending" => $TotalSending,
	// 			"TotalBatchPending" =>  $TotalBatchPending,
	// 			"TotalBatchSent" => $TotalBatchSent,
	// 			"TotalUndelivered" => abs($TotalUndelivered),
	// 			"DriverDetails" => $callarray,
	// 			"BatchDetails"  => $calltransactions
	// 		];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return [
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"data" => $data
	// 		];
	// 	}
	// }
	
	// public function anyCouriertransaction(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$data = array(); 
	// 	$calltransactions = []; 
	// 	$status = 1;

	// 	$MalaysiaCountryID = 458;
	// 	$regionid = 0;
	// 	$region = "";
		
	// 	$SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	// 	$stateName = [];

	// 	foreach ($SysAdminRegion as $value) {
	// 		$regionid = $value;
	// 		$State = State::getStateByRegion($value);
	// 		foreach ($State as $keyS => $valueS) $stateName[] = $valueS->name;
	// 	}


	// 	if (isset($regionid) && $regionid == 0) $region = 1;
	// 	$startdate = Date('Y-m-d', strtotime(date('l') == 'Monday' ? "-3 days" : "-2 days")) . " 23:59:59";


	// 	// Ta Q BIN & LineClear Start
	// 	try{
	// 		$countpending = 0;
	// 		$resultLineclear = "";
	// 		$resultLineclear = LogisticTransaction::getBatchdetailsTaqbinlineclear();
	// 		$countpending = count($resultLineclear);
	// 		$icount = 0;

	// 		foreach ($resultLineclear as $rowLineclear) {
	// 			$diff = 0;
	// 			$transID = 0;
	// 			$tstatus_01 = 0;
	// 			$assigndate = "";
	// 			$courier = "";

	// 			$Transactionresult_01 = LogisticTransaction::getTransactionID($rowLineclear->logistic_id);

	// 			$assigndate = $rowLineclear->assign_date;
	// 			$courier = $rowLineclear->courier_id;
	// 			$transID = $Transactionresult_01->transaction_id;
	// 			$transIDpending = $Transactionresult_01->transaction_id;
				

	// 			$result_driver_01 = LogisticTransaction::getBatchIDStatus($rowLineclear->logistic_id);
	// 			$tstatus_01 = $result_driver_01->status;


	// 			$first_date = strtotime($startdate);
	// 			$second_date = strtotime($assigndate);
	// 			$offset = $second_date - $first_date; 
	// 			$diff = abs(floor($offset / 60 / 60 / 24));
	// 			if($diff == 0) $diff = 1;

	// 			$calltransactions[] = [
	// 				'courier'		=> $courier,
	// 				'transactionid'	=> $this->generate_TagEnd($diff, $transID),
	// 				'transvalid'	=> $transIDpending,
	// 				'transcolor'	=> $this->t_color[(in_array($tstatus_01, [0, 1, 2, 4]) ? $tstatus_01 : 'default')],
	// 				'status'		=> $tstatus_01,
	// 			];
	// 		}

	// 		$data['TransactionLineclear'] = [
	// 			"TalineDetails"	=> self::arr_unique($calltransactions),
	// 			"cpending"		=> $countpending,
	// 			"regionid"		=> $region
	// 		];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return [
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"data" => $data
	// 		];
	// 	}
	// 	// Ta Q BIN & LineClear End 
	// }
	
	//  // Over 60 days  
	// public function anyOver60daystransaction(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$data = []; 
	// 	$calltransactions = [];
	// 	$status = 1;

	// 	$MalaysiaCountryID = 458;
	// 	$regionid = 0;
	// 	$region = "";
		
	// 	$SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	// 	$stateName = [];

	// 	foreach ($SysAdminRegion as $value) {
	// 		$regionid = $value;
	// 		$State = State::getStateByRegion($value);
	// 		foreach ($State as $keyS => $valueS) $stateName[] = $valueS->name;
	// 	}


	// 	if (isset($regionid) && $regionid == 0) $region = 1;
	// 	$startdate = Date('Y-m-d', strtotime(date('l') == 'Monday' ? "-3 days" : "-2 days")) . " 23:59:59";

	// 	// Over 60 days Start 
	// 	try{
	// 		$countpending = 0;
	// 		$resultover60Days = "";
	// 		$resultover60Days = LogisticTransaction::getBatchdetailsover60Days();

	// 		$countpending = count($resultover60Days);
	// 		echo '<pre>';
	// 		print_r($resultover60Days);
	// 		echo '</pre>';
	// 		$icount = 0; 

	// 		foreach ($resultover60Days as  $rowover60Days) {
	// 			$diff = 0;
	// 			$transID = 0;
	// 			$tstatus_01 = 0;
	// 			$assigndate = "";
	// 			$courier = "";

	// 			$Transactionresult_01 = LogisticTransaction::getTransactionID($rowover60Days->logistic_id);
	// 			$assigndate = $rowover60Days->assign_date;
	// 			$transID = $Transactionresult_01->transaction_id;
	// 			$transIDpending = $Transactionresult_01->transaction_id;
	// 			$result_driver_01 = LogisticTransaction::getBatchIDStatus($rowover60Days->logistic_id);
	// 			$tstatus_01 = $result_driver_01->status;


	// 			$first_date = strtotime($startdate);
	// 			$second_date = strtotime($assigndate);
	// 			$offset = $second_date-$first_date; 
	// 			$diff = abs(floor($offset / 60 / 60 / 24));

	// 			if($diff == 0) $diff = 1;

	// 			if ($diff >= 60) {
	// 				$calltransactions[] = [
	// 					'courier'		=> $courier,
	// 					'transactionid'	=> $this->generate_TagEnd($diff, $transID, '$diff >= 60'),
	// 					'transvalid'	=> $transIDpending,
	// 					'transcolor'	=> $this->t_color[(in_array($tstatus_01, [0, 1, 2, 4]) ? $tstatus_01 : 'default')],
	// 					'status'		=> $tstatus_01,
	// 				];
	// 			}
	// 		}

	// 		$data['TransactionLineclear'] = [
	// 			"Over60daysDetails"	=> self::arr_unique($calltransactions),
	// 			"cpending60"		=> $countpending,
	// 			"regionid"			=> $region
	// 		];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return [
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"data" => $data
	// 		];
	// 	}
	// 	// Over 60 days End 
	// }
	
	// public function anyReturnedtransaction(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$data = array(); 
	// 	$calltransactions = []; 
	// 	$status = 1;


	// 	$MalaysiaCountryID = 458;
	// 	$regionid = 0;
	// 	$region = "";

	// 	$SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	// 	$stateName = [];

	// 	foreach ($SysAdminRegion as  $value) {
	// 		$regionid = $value;
	// 		$State = State::getStateByRegion($value);
	// 		foreach ($State as $keyS => $valueS) $stateName[] = $valueS->name;
	// 	}


	// 	if (isset($regionid) && $regionid == 0) $region = 1;
	// 	$startdate = Date('Y-m-d') . " 23:59:59";
			 
	// 	// Over 60 days Start 
	// 	try{
	// 		$countreturned = 0;
	// 		$resultoReturned = "";
	// 		$resultoReturned = LogisticTransaction::getTransactionreturned(Session::get('username'));
	// 		$countreturned = count($resultoReturned);
	// 		$icount = 0; 

	// 		foreach ($resultoReturned as  $rowreturned) {
	// 			$diff = 0;
	// 			$transID = 0;
	// 			$assigndate = "";

	// 			$assigndate = $rowreturned->insert_date;
	// 			$transID = $rowreturned->transaction_id;

	// 			$first_date = strtotime($startdate);
	// 			$second_date = strtotime($assigndate);
	// 			$offset = $second_date-$first_date; 
	// 			$diff = abs(floor($offset / 60 / 60 / 24));
	// 			if($diff == 0) $diff = 1;
				
	// 			$calltransactions[] = [
	// 				'transactionid'		=> $this->generate_TagEnd($diff, $transID),
	// 				'transcolor'		=> '#FF0000',
	// 			];
	// 		}

	// 		$data['TransactionReturned'] = [
	// 			"ReturnedDetails"	=> self::arr_unique($calltransactions),
	// 			"creturned"			=> $countreturned,
	// 			"regionid"			=> $region
	// 		];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return [
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"data" => $data
	// 		];
	// 	}
	// }
	
	// public function anyPartialsenttransaction(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$data = array(); 
	// 	$calltransactions = array(); 
	// 	$status = 1;


	// 	$MalaysiaCountryID = 458;
	// 	$regionid = 0;
	// 	$region = "";
		
	// 	$SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	// 	$stateName = [];

	// 	foreach ($SysAdminRegion as  $value) {
	// 		$regionid = $value;
	// 		$State = State::getStateByRegion($value);
	// 		foreach ($State as $keyS => $valueS) $stateName[] = $valueS->name;
	// 	}


	// 	if (isset($regionid) && $regionid == 0) $region = 1;
	// 	$startdate = Date('Y-m-d') . " 23:59:59";
			 
	// 	// Over 60 days Start 
	// 	try{
	// 		$countpartialsent = 0;
	// 		$resultoPartialSent = "";
	// 		$resultoPartialSent = LogisticTransaction::getTransactionpartialsent(Session::get('username'));
	// 		$countpartialsent = count($resultoPartialSent);

	// 		$icount = 0; 

	// 		foreach ($resultoPartialSent as $rowpartialsent) {
	// 			$diff = 0;
	// 			$transID = 0;
	// 			$assigndate = "";

	// 			$assigndate = $rowpartialsent->insert_date;
	// 			$transID = $rowpartialsent->transaction_id;

	// 			$first_date = strtotime($startdate);
	// 			$second_date = strtotime($assigndate);
	// 			$offset = $second_date - $first_date; 
	// 			$diff = abs(floor($offset / 60 / 60 / 24));
	// 			if($diff == 0) $diff = 1;

	// 			$calltransactions[] = [
	// 				'transactionid'	=> $this->generate_TagEnd($diff, $transID),
	// 				'transcolor'	=> '#FF0000',
	// 			];
	// 		}

	// 		$data['TransactionPartialSent'] = [
	// 			"PartialSentDetails"	=> self::arr_unique($calltransactions),
	// 			"cpartialsent"			=> $countpartialsent,
	// 			"regionid"				=> $region
	// 		];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return array(
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"data" => $data
	// 		);
	// 	}
	// }

	// private function Unassignedtransaction_callback($region_id, $stateName, &$transdata, &$rdata){
	// 	$resultregion = LogisticTransaction::getDriverRegionName($region_id);
	// 	$region = $resultregion->region;

	// 	$countpending = 0;
	// 	$resultpending = LogisticTransaction::getTransactionpending($stateName);
	// 	$countpending = count($resultpending);
	// 	foreach ($resultpending as $tvalue) {
	// 		$transdata[] = [
	// 			'transactionid'	=> $tvalue->transaction_id,
	// 			'regionid'		=> $resultregion->id,
	// 		];
	// 	}

	// 	$rdata[] = [
	// 		'regionname'	=> $region,
	// 		'countpending'	=> $countpending,
	// 		'regionid'		=> $resultregion->id,
	// 	];
	// }
	
	// public function anyUnassignedtransaction(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$transdata = []; 
	// 	$rdata     = [];  
	// 	$tdata     = [];

	// 	$status = 1;

	// 	$MalaysiaCountryID = 458;
	// 	$regionid = 0;
	// 	$region = "";

	// 	$SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
	// 	$stateName = [];

	// 	foreach ($SysAdminRegion as  $value) {
	// 		$regionid = $value;
	// 		$State = (isset($regionid) && $regionid == 0 ? State::getStateByCountry($MalaysiaCountryID) : State::getStateByRegion($value));
	// 		foreach ($State as $keyS => $valueS) $stateName[] = $valueS->name;
	// 	}


	// 	try{
	// 		if(in_array($regionid, ['1', '2', '3'])){
	// 			$this->Unassignedtransaction_callback($regionid, $stateName, $transdata, $rdata);
	// 		}else{
	// 			$rowarray = [];
	// 			$result = DB::table('jocom_region')->orderby('id','ASC')->get();

	// 			foreach ($result as $rvalue) {
	// 				$region = $rvalue->region;
	// 				$rid    = $rvalue->id;
	// 				unset($stateName);
	// 				$State = State::getStateByRegion($rid);
	// 				foreach ($State as $keyS => $valueS) $stateName[] = $valueS->name;
	// 				$countpending = 0;
	// 				unset($temparray);
	// 				$resultpending = LogisticTransaction::getTransactionpending($stateName);
	// 				$countpending = count($resultpending);
	// 				foreach ($resultpending as $tvalue) {
	// 					$transdata[] = [
	// 						'transactionid'	=> $tvalue->transaction_id,
	// 						'regionid'		=> $rid,
	// 					];
	// 				}

	// 				$rdata[] = [
	// 					'regionname'	=> $region,
	// 					'countpending'	=> $countpending,
	// 					'regionid'		=> $rid,
	// 				];
	// 			}
	// 		}
	// 		$tdata["Transactionpending"] = [
	// 			'Regionlist'       => $rdata,
	// 			'Transactinlist'   => $transdata
	// 		];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return [
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"tdata" => $tdata
	// 		];
	// 	}
	// }

	// public function anyDashboarddriverbatch(){
	// 	$isError = 0;
	// 	$respStatus = 1;
	// 	$driverdata = []; 
	// 	$status = 1;


	// 	try{
	// 		$driverid = Input::get($driverid);
	// 		$calltransactions = [];

	// 		$result = LogisticTransaction::getBatchdetails($driverid);

	// 		foreach ($result as  $row) {
	// 			$tstatus = 0;
	// 			$Transactionresult = LogisticTransaction::getTransactionID($row->logistic_id);
	// 			$tstatus = $row->status;

	// 			$calltransactions[] = [
	// 				'driver_id'		=> $row->driver_id,
	// 				'transactionid'	=> $Transactionresult->transaction_id,
	// 				'transcolor'	=> $this->t_color[(in_array($tstatus, [0, 1, 4]) ? $tstatus : 'default')],
	// 			];
	// 		}

	// 		$driverdata['TransactionLogisticdriver'] = ["BatchDetailsNew"  => $calltransactions];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return array(
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"driverdata" => $driverdata
	// 		);
	// 	}

	// }
	
	// /* GENERATE ASSIGN REPORT : OPEN */
	// public function anyAssigned(){
	// 	$sysAdminInfo = User::where("username", Session::get('username'))->first();
	// 	$SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->where("status",1)->first();

	// 	if($SysAdminRegion->region_id == 0){
	// 		$driver = DB::table('logistic_driver')->where('status', 1)->select('id', 'name')->get();
	// 	}else{
	// 		$driver = DB::table('logistic_driver')->where('region_id', $SysAdminRegion->region_id)->where('status', 1)->select('id', 'name')->get();   
	// 	}

	// 	return View::make('report.assigned_report')->with('driver', $driver);
	// }
	
	// public function anyAssignedreport(){
	// 	try{
	// 		$transaction_from = Input::get('transaction_from');
	// 		$transaction_to = Input::get('transaction_to');
	// 		$driver = Input::get('driver');

	// 		$driver_details = LogisticDriver::find($driver);

	// 		$collectionData = [];

	// 		$logistic2 = DB::table('logistic_batch AS LB')
	// 			->leftJoin('logistic_batch_item AS LBI','LB.id','=','LBI.batch_id')
	// 			->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
	// 			->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
	// 			->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
	// 			->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'LTI.product_price_id')
	// 			->where('LB.driver_id','=', $driver)
	// 			->where('LB.assign_date', '>=', $transaction_from)
	// 			->where('LB.assign_date','<=', $transaction_to)
	// 			->whereIn('LB.status',[0, 1])
	// 			->select("JP.sku",DB::raw("JP.shortname,JP.name,JP.id,LTI.label,LTI.product_price_id, GROUP_CONCAT(LBI.qty_assign SEPARATOR ',') as 'qty_assign',GROUP_CONCAT(LT.transaction_id SEPARATOR ',') as 'transaction_id',SUM(LBI.qty_assign) as 'total', Count(LT.transaction_id) as 'id_count'"))
	// 			->groupBy('LTI.product_price_id')
	// 			->orderBy('id_count','Desc') //do not modify this
	// 			->get();

	// 		foreach ($logistic2 as $key => $value) {

	// 			$base = DB::table('jocom_product_base_item')
	// 				->where("product_id",$value->id)
	// 				->where("price_option_id",$value->product_price_id) // ADDED: WIRA
	// 				->where("status",1);

	// 			$baseList = $base;
	// 			$baseListData = $base->get();

	// 			$baseListId = $baseList->lists('product_base_id');

	// 			$product = DB::table('jocom_products')->whereIn('id', $baseListId)->select('shortname', 'name', 'id', 'sku')->get(); // amended by wira add quantity
				
	// 			if (count($product) > 0) {
					
	// 				$baseCounter = 0;
	// 				foreach ($product as $value3) {

	// 					foreach ($baseListData as $kbd => $vbd) if($vbd->product_base_id == $value3->id) $baseTotalQty = $vbd->quantity;

	// 					$productPrice = DB::table('jocom_product_price')
	// 						->where('product_id', $value3->id)
	// 						->where('default', 1)
	// 						->first(); // amended by wira add quantity

	// 					$name = $value3->shortname != '' ? $value3->shortname : $value3->name;
	// 					$label_name = $productPrice->alternative_label_name != '' ? $productPrice->alternative_label_name : $productPrice->label;
						
	// 					$assignCollection = explode(",",$value->qty_assign);
	// 					$assignQTY = '';
						
	// 					foreach ($assignCollection as $assign) $assignQTY[] = $assign * $baseTotalQty;
						
	// 					$assignQTY =  implode(",", $assignQTY);
 
	// 					if (array_key_exists($value3->sku, $collectionData)) {
	// 						$collectionData[$value3->sku]['qty_assign'] = $collectionData[$value3->sku]['qty_assign'] . "," . $assignQTY;
	// 						$collectionData[$value3->sku]['qty'] = $collectionData[$value3->sku]['qty'] . "," . $value->qty_assign;
	// 						$collectionData[$value3->sku]['transaction_id'] = $collectionData[$value3->sku]['transaction_id'] . "," . $value->transaction_id;
	// 						$collectionData[$value3->sku]['total'] = $collectionData[$value3->sku]['total'] + $value->total;
	// 						$collectionData[$value3->sku]['id_count'] = $collectionData[$value3->sku]['id_count'] + count(explode(",", $value->transaction_id));
	// 					}else{
	// 						$collectionData[$value3->sku] = [
	// 							"product_sku" => $value3->sku,
	// 							"qty" => $value->qty_assign,
	// 							"qty_assign" => $assignQTY,
	// 							"label_name" => $label_name,
	// 							"transaction_id" => $value->transaction_id,
	// 							"total" => $value->total,
	// 							"id_count" => $value->id_count,
	// 							"base_product" => $name,
	// 							"base_quantity" => $baseTotalQty,
	// 						];
	// 					}

	// 					$baseCounter++;
	// 				}  
	// 			}else{
	// 				$productPrice = DB::table('jocom_product_price')->where("id",$value->product_price_id)->first(); // amended by wira add quantity

	// 				$name = ($value->shortname != '' ? $value->shortname : $value->name);
	// 				$label_name = ($productPrice->alternative_label_name != '' ? $productPrice->alternative_label_name : $productPrice->label);
					
	// 				if (array_key_exists($value->sku, $collectionData)) {
	// 					$collectionData[$value->sku]['qty_assign'] = $collectionData[$value->sku]['qty_assign'].",".$value->qty_assign;
	// 					$collectionData[$value->sku]['transaction_id'] = $collectionData[$value->sku]['transaction_id'].",".$value->transaction_id;
	// 					$collectionData[$value->sku]['total'] = $collectionData[$value->sku]['total'] + $value->total;
	// 					$collectionData[$value->sku]['id_count'] = $collectionData[$value->sku]['id_count'] + count(explode(",",$value->transaction_id));
	// 				}else{
	// 					$collectionData[$value->sku] = array(
	// 						"is_not_base" => true,
	// 						"shortname" => $name,
	// 						"label_name" => $label_name,
	// 						"qty_assign" => $value->qty_assign,
	// 						"transaction_id" => $value->transaction_id,
	// 						"total" => $value->total,
	// 						"id_count" => $value->id_count,
	// 					);
	// 				}
	// 			}
	// 		}

	// 		$data = array(
	// 			'logistic2' => $collectionData,
	// 			'transaction_from' => $transaction_from,
	// 			'transaction_to' => $transaction_to,
	// 			'driver_name' => $driver_details->name,
	// 		);

	// 		if (!empty($logistic2)) {
	// 			return Excel::create('assigned(' . $transaction_from . '- ' . $transaction_to . ')', function($excel) use ($data) {
	// 				$excel->sheet('Batch Item', function($sheet) use ($data) {   
	// 					$sheet->loadView('report.assignedtable3', array('data'=>$data));
	// 					$sheet->setOrientation('landscape');
	// 				});
	// 			})->download('xls');
	// 		}else{
	// 			return Redirect::to('jlogistic/assigned')->with('message', 'Sorry. No data found!');
	// 		}
	// 	}catch(Exception $ex){
	// 		echo $ex->getMessage();
	// 	}

	// }

	// public function anyAssignedtable(){
	// 	return View::make('report.assignedtable');
	// }

	// public function anyAssignedtable2(){
	// 	return View::make('report.assignedtable2');
	// }
	
	// /* GENERATE ASSIGN REPORT : CLOSE */
	// public function anyTracking(){
	// 	return View::make('logistic.logistic_tracking_dashboard');
	// }

	// public function anyActivedrivers(){
	// 	$isError = 0;
	// 	$respStatus = 1;

	// 	$data = [];
	// 	$calldata = [];

	// 	$MalaysiaCountryID = 458;
	// 	$regionid = 0;
	// 	$region = "";

	// 	try{
	// 		$stateName = DB::table('jocom_country_states AS')->whereIn('region_id', UserController::getSysRegionList(Session::get('username')))->pluck('name');

	// 		if (isset($regionid) && $regionid ==0){
	// 			$DriversResult = LogisticDriver::where('is_logistic_dashboard', '=', 1)
	// 				->where('status','=',1)
	// 				->orderBy('username','ASC')
	// 				->get();
	// 		} else{
	// 			$DriversResult = LogisticDriver::where('is_logistic_dashboard', '=', 1)
	// 				->where('status','=',1)
	// 				->where('region_id','=', $regionid)
	// 				->orderBy('username','ASC')
	// 				->get();
	// 		}


	// 		foreach ($DriversResult as $key => $value) {
	// 			$totalbatch = 0;
	// 			$totalsent = 0;
	// 			$totalpending = 0; 

	// 			$totalbatch = LogisticTransaction::getTotaldailybatch($value->id);
	// 			$totalsent  = LogisticTransaction::getTotaldailybatchsent($value->id);

	// 			if($totalbatch != 0){
	// 				$totalpending = $totalbatch - $totalsent;
	// 				$calldata[] = [
	// 					'driver_id'  	=> $value->id, 
	// 					'name'       	=> $value->name,
	// 					'totalbatch' 	=> $totalbatch,
	// 					'totalsent'  	=> $totalsent,
	// 					'totalpending'  => $totalpending,
	// 				];
	// 			}
	// 		}
	// 		$data['driverdata'] =  ['driverdetails' => $calldata];
	// 	}catch (Exception $ex) {
	// 		$message = $ex->getMessage();
	// 		$isError = 1;
	// 	}finally {
	// 		return array(
	// 			"respStatus" => $respStatus,
	// 			"isError" => $isError,
	// 			"message" => $message,
	// 			"data" => $data
	// 		);
	// 	}
	// }

	// /*
	//  * OPEN
	//  * Desc : International Logistic Listing
	//  */
	// public function anyInternationallogistic(){
	// 	return View::make('logistic.logistic_international_listing');
	// }
	
	// public function anyManifests() {
	// 	// Get Orders
	// 	$tasks = DB::table('jocom_international_logistic_manifest AS JILM')->select(['JILM.id', 'JILM.created_at', 'JILM.manifest_id', 'JILM.country_id', 'JC.name'])
	// 		->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JILM.country_id')
	// 		->where('JILM.activation', '=', 1)
	// 		->orderBy('JILM.id','ASC');

	// 	return Datatables::of($tasks)->make(true);
	// }
	
	// public function anyInternationallogisticlist($type) {
	// 	// Get Orders
	// 	$user_id = Session::get("user_id");
		
	// 	switch ($type) {
	// 		case 1:
	// 			$tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
	// 				'JIL.id',
	// 				'JIL.reference_number',
	// 				'JIL.manifest_id',
	// 				'JT.buyer_username',
	// 				'JT.transaction_date',
	// 				'JIL.transaction_id',
	// 				'JT.delivery_name',
	// 				'JT.delivery_addr_1',
	// 				'JT.delivery_addr_2',
	// 				'JT.delivery_city',
	// 				'JT.delivery_state',
	// 				'JT.delivery_postcode',
	// 				'JT.delivery_country',
	// 				DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
	// 				'JIL.status',
	// 			))
	// 			->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
	// 			->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
	// 			->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
	// 			->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
	// 			->where('JIL.status', '=',1)
	// 			->orderBy('JIL.id','ASC');

	// 			break;
			
	// 		case 2:
				
	// 			 $tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
	// 				'JIL.id',
	// 				'JIL.reference_number',
	// 				'JIL.manifest_id',
	// 				'JT.buyer_username',
	// 				'JT.transaction_date',
	// 				'JIL.transaction_id',
	// 				'JT.delivery_name',
	// 				'JT.delivery_addr_1',
	// 				'JT.delivery_addr_2',
	// 				'JT.delivery_city',
	// 				'JT.delivery_state',
	// 				'JT.delivery_postcode',
	// 				'JT.delivery_country',
	// 				DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
	// 				'JIL.status',
	// 			))
	// 			->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
	// 			->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
	// 			->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
	// 			->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
	// 			->where('JIL.status', '=',2)
	// 			->orderBy('JIL.id','ASC');

	// 			break;
			
	// 		case 3:
				
	// 			$tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
	// 				'JIL.id',
	// 				'JIL.reference_number',
	// 				'JIL.manifest_id',
	// 				'JT.buyer_username',
	// 				'JT.transaction_date',
	// 				'JIL.transaction_id',
	// 				'JT.delivery_name',
	// 				'JT.delivery_addr_1',
	// 				'JT.delivery_addr_2',
	// 				'JT.delivery_city',
	// 				'JT.delivery_state',
	// 				'JT.delivery_postcode',
	// 				'JT.delivery_country',
	// 				DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
	// 				'JIL.weight',
	// 				'JIL.status',
					 
	// 			))
	// 			->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
	// 			->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
	// 			->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
	// 			->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
	// 			->where('JIL.status', '=',3)
	// 			->orderBy('JIL.id','ASC');

	// 			break;
			
	// 		case 4:
				
	// 			$tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
	// 				'JIL.id',
	// 				'JIL.reference_number',
	// 				'JIL.manifest_id',
	// 				'JT.buyer_username',
	// 				'JT.transaction_date',
	// 				'JIL.transaction_id',
	// 				'JT.delivery_name',
	// 				'JT.delivery_addr_1',
	// 				'JT.delivery_addr_2',
	// 				'JT.delivery_city',
	// 				'JT.delivery_state',
	// 				'JT.delivery_postcode',
	// 				'JT.delivery_country',
	// 				DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
	// 				'JIL.weight',
	// 				'JIL.status',
					 
	// 			))
	// 			->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
	// 			->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
	// 			->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
	// 			->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
	// 			->whereIn('JIL.status', [4,5])
	// 			->orderBy('JIL.id','ASC');

	// 			break;
	  
	// 		default:
	// 			break;
	// 	}

	// 	return Datatables::of($tasks)->make(true);
	// }

	// /*
	//  * Desc : To verify/confirmed delivery for new international delivery
	//  */
	// public function anyVerifiedlogistic() {
	// 	$data = [];
	// 	$RespStatus = 1; 
	// 	$message = "Updated!";
	// 	$errorCode = "";
	// 	$is_error = false;
	// 	$error_line = "";

	// 	try {
	// 		DB::beginTransaction();

	// 		$selectedIDs = Input::get("items");
	// 		$action = Input::get("action");

	// 		switch ($action) {
	// 			case 2: // Verify New Order
	// 				$updateStatus = self::verifyInternationDelivery($selectedIDs, $action);
	// 				break;
	// 			case 4: // Set as shipped
	// 				$updateStatus = self::verifyInternationDelivery($selectedIDs, $action);
	// 				break;
	// 			case 5: // Set as delivered
	// 				$updateStatus = self::verifyInternationDelivery($selectedIDs, $action);
	// 				break;
	// 			default:
	// 				break;
	// 		}
			
	// 		if(!$updateStatus){
	// 			throw new exception("Ape lancau xupdate doh!");
	// 		}
	// 	} catch (Exception $ex) {
	// 		$is_error = true;
	// 		$message = $ex->getMessage();			
	// 	} finally {
	// 		if ($is_error) {
	// 			DB::rollback();
	// 		} else {
	// 			DB::commit();
	// 		}
	// 	}


	// 	/* Return Response */
	// 	$response = ["RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data];
	// 	return $response;
	// }
	
	// private static function verifyInternationDelivery($selectedIDs,$action){
	// 	if(count($selectedIDs) > 0){
	// 		if($action == 5){
	// 			foreach($selectedIDs as $value){
	// 				$InternationalLogistic = InternationalLogistic::find($value);
	// 				$InternationalLogistic->status = $action;
	// 				$InternationalLogistic->updated_by = Session::get("username");
	// 				$InternationalLogistic->save();
	// 				LogisticBatch::UpdateBatch($InternationalLogistic->batch_id, array("status"=>4));
	// 			}
	// 		}else{
	// 			DB::table('jocom_international_logistic')
	// 				->whereIn("id", $selectedIDs)
	// 				->update([
	// 					'status' => $action,
	// 					'updated_at' => date("Y-m-d h:i:s"),
	// 					'updated_by' => Session::get("username")
	// 				]);
	// 		}
	// 		return true;
	// 	} else{
	// 		return false;
	// 	}
	// }
	
	// public function anyConfirmedweight() {
	// 	$data = array();
	// 	$RespStatus = 1; 
	// 	$message = "Weight Updated!";
	// 	$errorCode = "";
	// 	$is_error = false;
	// 	$error_line = "";

	// 	try {
	// 		DB::beginTransaction();

	// 		$itemID = Input::get("item");
	// 		$total = Input::get("total");

	// 		$running_number = DB::table('jocom_running')
	// 			->select('*')
	// 			->where('value_key', '=', 'inter_label')->first();
			
	// 		$ReferenceNo = "JCM".str_pad($running_number->counter + 1,9,"0",STR_PAD_LEFT);
	// 		$NewRunner = Running::find($running_number->id);
	// 		$NewRunner->counter = $running_number->counter + 1;
	// 		$NewRunner->save();
			
	// 		$updateWeight = DB::table('jocom_international_logistic')->where("id", $itemID)->update([
	// 			'weight' => $total,
	// 			'reference_number' => $ReferenceNo,
	// 			'status' => 3,
	// 			'updated_at' => DATE("Y-m-d h:i:s"),
	// 			'updated_by' => Session::get("username")
	// 		]);
	// 	} catch (Exception $ex) {
	// 		$is_error = true;
	// 		$message = $ex->getMessage();
	// 	} finally {
	// 		if ($is_error) {
	// 			DB::rollback();
	// 		} else {
	// 			DB::commit();
	// 		}
	// 	}


	// 	/* Return Response */
	// 	$response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
	// 	return $response;
	// }
	
	// /*
	//  * Desc : View label from
	//  */
	// public function anyViewlabel($deliveryID){
	// 	$isError = 0;
	// 	$message = "";
	// 	$code = $code;
	// 	$loopEmpty = 7;

	// 	$d = new DNS1D();
	// 	$d->setStorPath(__DIR__ . "/cache/");

	// 	$collectionData = DB::table('jocom_international_logistic AS JIL')->select([
	// 		'JIL.*',
	// 		'JT.buyer_username',
	// 		'JT.transaction_date',
	// 		'JT.delivery_name',
	// 		'JT.delivery_contact_no',
	// 		'JT.delivery_addr_1',
	// 		'JT.delivery_addr_2',
	// 		'JT.delivery_city',
	// 		'JT.delivery_state',
	// 		'JT.delivery_postcode',
	// 		'JT.delivery_country',
	// 		DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress'),
	// 	])
	// 	->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
	// 	->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
	// 	->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
	// 	->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
	// 	->where('JIL.id', '=', $deliveryID)->first();
		
	// 	$collectionTotalData = DB::table('jocom_international_logistic_item')->where("jocom_international_logistic_id",$collectionData->id)->count();
	// 	$collectionItems = DB::table('jocom_international_logistic_item')->where("jocom_international_logistic_id",$collectionData->id)->get();
		
	// 	try{
	// 		$general = [
	// 			"barcode"=> '<img src="data:image/png;base64,' . $d->getBarcodePNG(strtoupper($collectionData->reference_number), "C39E", 1.8, 70) . '" alt="barcode" />', 
	// 			"data_header"=> $collectionData, 
	// 			"data_items"=> $collectionItems, 
	// 		];
	// 		$loopEmpty = $loopEmpty - $collectionTotalData;
	// 	} catch (Exception $ex) {
	// 		$isError = 1;
	// 	}finally{
	// 		$data = [
	// 			"error" => $isError,
	// 			"data" => $general,
	// 			"message" => $message,
	// 			"totalLoopEmpty" => $loopEmpty
	// 		];
	// 		$pdf = PDF::loadView('emails.international_logistic_lable', $data);
	// 		return $pdf->stream('invoice.pdf');
	// 	} 
	// }
	
	// public function anyDownloadmanifestbyid($manifestNumber){
	// 	$collectionData = DB::table('jocom_international_logistic AS JIL')
	// 		->select([
	// 			'JIL.*',
	// 			'JT.buyer_username',
	// 			'JT.transaction_date',
	// 			'JT.delivery_name',
	// 			'JT.delivery_contact_no',
	// 			'JT.delivery_identity_number',
	// 			'JT.delivery_addr_1',
	// 			'JT.delivery_addr_2',
	// 			'JT.delivery_city',
	// 			'JT.delivery_state',
	// 			'JT.delivery_postcode',
	// 			'JT.delivery_country',
	// 			 DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress'),
	// 			'JILI.*' 
	// 		])
	// 		->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
	// 		->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
	// 		->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
	// 		->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
	// 		->leftJoin('jocom_international_logistic_item AS JILI', 'JILI.jocom_international_logistic_id', '=', 'JIL.id')
	// 		->where('JIL.manifest_id', '=', $manifestNumber)->get();

	// 	$currentRefNumber = 0;
	// 	$indexCounter = 0;
		
	// 	$DataList = [];
		
	// 	foreach ($collectionData as $key => $value) {
	// 		if($value->reference_number !== $currentRefNumber){ // New Item 
	// 			$currentRefNumber = $value->reference_number;
	// 			$indexCounter++;
				
	// 			$DataList[] = [
	// 				"index" => $indexCounter,
	// 				"reference_number" => $value->reference_number,
	// 				"description" => $value->product_name,
	// 				"brand" => '',
	// 				"specification" => $value->product_label,
	// 				"model" => $value->Model,
	// 				"no_of_pcs" => $value->no_of_pcs,
	// 				"content_pcs" => $value->content_of_pcs,
	// 				"unit" => $value->quantity,
	// 				"weight" => $value->weight,
	// 				"value" => $value->value,
	// 				"recipient_name" => $value->delivery_name,
	// 				"recipient_id_number" => $value->delivery_identity_number,
	// 				"delivery_address" => $value->FullAddress,
	// 				"contact_number" => $value->delivery_contact_no
	// 			];
	// 		}else{
	// 			$DataList[] = [
	// 				"index" => '',
	// 				"reference_number" => '',
	// 				"description" => $value->product_name,
	// 				"brand" => '',
	// 				"specification" => $value->product_label,
	// 				"model" => $value->Model,
	// 				"no_of_pcs" => $value->no_of_pcs,
	// 				"content_pcs" => $value->content_of_pcs,
	// 				"unit" => $value->quantity,
	// 				"weight" => $value->weight,
	// 				"value" => $value->value,
	// 				"recipient_name" => '',
	// 				"recipient_id_number" => '',
	// 				"delivery_address" => '',
	// 				"contact_number" => '',
	// 			];
	// 		}
	// 	}

		
	// 	return Excel::create($manifestNumber, function($excel) use ($DataList) {
	// 		$excel->sheet('MANIFEST', function($sheet) use ($DataList){
	// 			$sheet->loadView('manifest.CHN', array('data' =>$DataList));
	// 		});
	// 	})->download('xls');
	// }
	
	// public function anyViewdetailslogistic($id){
	// 	try{
	// 		$collectionData = DB::table('jocom_international_logistic AS JIL')
	// 			->select([
	// 				'JIL.*',
	// 				'JT.buyer_username',
	// 				'JT.transaction_date',
	// 				'JT.delivery_name',
	// 				'JT.delivery_contact_no',
	// 				'JT.delivery_addr_1',
	// 				'JT.delivery_addr_2',
	// 				'JT.delivery_city',
	// 				'JT.delivery_state',
	// 				'JT.delivery_postcode',
	// 				'JT.delivery_country',
	// 				 DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' )
	// 			])
	// 			->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
	// 			->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
	// 			->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
	// 			->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
	// 			->where('JIL.id', '=',$id)->first();
				
				
	// 		$collectionItems = DB::table('jocom_international_logistic_item')->where("jocom_international_logistic_id",$collectionData->id)->get();
			
	// 		$data = [
	// 			"headerInfo" => $collectionData,
	// 			"itemsInfo" => $collectionItems
	// 		];
				
	// 		echo "<pre>";
	// 		print_r($data);
	// 		echo "</pre>";
	// 	}catch(Exception $e) {
	// 		echo 'Message: ' .$e->getMessage();
	// 	}
	// }

	// public function anyMacrolinkdomesticlabel($id){
	// 	$isError = 0;
	// 	$message = "";
	// 	$code = $code;
	// 	$loopEmpty = 5;
		
		
	// 	$d = new DNS1D();
	// 	$d->setStorPath(__DIR__."/cache/");
		
	// 	$collectionData = array();
	// 	$collectionItems = array();
		
	// 	$courierOrderData = DB::table('jocom_courier_orders AS JCO')
	// 		->where("JCO.id",$id)
	// 		->first();
		
	// 	$batchInfo = DB::table('logistic_batch AS LB')
	// 		->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
	// 		->where("LB.id",$courierOrderData->batch_id)
	// 		->first();

	// 	$collectionData = DB::table('jocom_transaction AS JT')
	// 		->where("JT.id",$batchInfo->transaction_id)
	// 		->first();
		
	// 	$collectionItems = DB::table('logistic_transaction_item AS LTI')
	// 		->select("JTD.*")
	// 		->leftJoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'LTI.transaction_item_id')
	// 		->where("LTI.id",$courierOrderData->transaction_item_logistic_id)
	// 		->get();

	// 	$collectionTotalData = DB::table('logistic_transaction_item AS LTI')
	// 		->leftJoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'LTI.transaction_item_id')
	// 		->where("LTI.id",$courierOrderData->transaction_item_logistic_id)
	// 		->count();
  
	// 	$reference_number = "JDM".str_pad($courierOrderData->transaction_item_logistic_id,9,"0",STR_PAD_LEFT);
	   
	// 	try{
	// 		$general = [
	// 			"barcode"=> '<img src="data:image/png;base64,' . $d->getBarcodePNG(strtoupper($reference_number), "C39E",1.8,70) . '" alt="barcode"   />', 
	// 			"data_header"=> $collectionData, 
	// 			"data_items"=> $collectionItems, 
	// 			"reference_number"=> $reference_number, 
	// 		];

	// 		$loopEmpty = $loopEmpty - $collectionTotalData;
	// 	} catch (Exception $ex) {
	// 		$isError = 1;
	// 	}finally{
	// 		$data = [
	// 			"error" => $isError,
	// 			"data" => $general,
	// 			"message" => $message,
	// 			"totalLoopEmpty" => $loopEmpty
	// 		];

	// 		$pdf = PDF::loadView('emails.domestic_logistic_label', $data);
	// 		return $pdf->stream('invoice.pdf');
	// 	}  
	// }
}
?>