<?php

class HomeController extends BaseController {

	public function __construct()
    {
        $this->beforeFilter('auth');
    }

	/*
	|--------------------------------------------------------------------------
	| Default Home Controller
	|--------------------------------------------------------------------------
	|
	| You may wish to use controllers instead of, or in addition to, Closure
	| based routes. That's great! Here is an example controller method to
	| get you started. To route to this controller, just add the route:
	|
	|	Route::get('/', 'HomeController@showWelcome');
	|
	*/
	
	
	public static function datediffInWeeks($date1, $date2){
        
        if($date1 > $date2) return datediffInWeeks($date2, $date1);
        $first = DateTime::createFromFormat('m/d/Y', $date1);
        $second = DateTime::createFromFormat('m/d/Y', $date2);
        return floor($first->diff($second)->days/7);
        
    }
	
	public function anyIndex2()
    {
    	$user 			= User::find(Auth::id());
    	$date 			= $user->last_login;
    	$last_login		= date("d-M-Y H:i:s A", strtotime($date));
        $total_cust 	= Customer::TotalCustomer();
        $total_value 	= Transaction::dashboard_total();
	$latest_trans 	= Transaction::dashboard_latest_transaction();	//i.e. id, total_amount, transaction_date
        $total_product  = Product::dashboard_totalProducts();

        $latestTransaction = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(10)
                                        ->get();
        
        $latestTransaction2 = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(1)
                                        ->first();
                                        
        $total_completed_transaction = Transaction::where('status', '=', 'completed')->count();
        // $total_pending_transaction = Transaction::where('status', '=', 'pending')->count();
        $total_pending_transaction = LogisticTransaction::where('status', '=', '0')->count();
        $total_cancelled_transaction = Transaction::where('status', '=', 'cancelled')->count();
        $total_refund_transaction = Transaction::where('status', '=', 'refund')->count();
                                        
        $totalOrder = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->count();
        
        
        $totalDeliveryOrder = LogisticBatch::where("status",4)->count();
        $total_of_week = self::datediffInWeeks('09/5/2014','10/5/2017');
        $totalOrderPerWeek = (int)$totalOrder/$total_of_week;
        
        $totalDeliveryPerWeek = (int)$totalDeliveryOrder/$total_of_week;
        
        $currentdate = date("Y-m-d")." 23:59:59";
        $TotalPendingAll = LogisticTransaction::getAllTotalRecordByStatus(0,$currentdate);
        $TotalUndeliveredAll = LogisticTransaction::getAllTotalRecordByStatus(1,$currentdate);
        $TotalPartialAll = LogisticTransaction::getAllTotalRecordByStatus(2,$currentdate);
        $TotalReturnedAll = LogisticTransaction::getAllTotalRecordByStatus(3,$currentdate);
        $TotalSendingAll = LogisticTransaction::getAllTotalRecordByStatus(4,$currentdate);
        $TotalSentAll = LogisticTransaction::getAllTotalRecordByStatus(5,$currentdate);
        $TotalCancelledAll = LogisticTransaction::getAllTotalRecordByStatus(6,$currentdate);
        
        
        $totalProductQty = DB::select(DB::raw('SELECT LTI.name,SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id
                                WHERE LT.status IN (0)
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 10'));
//        echo "<pre>";
//        print_r($totalProduct);
//        echo "</pre>";
//        

        return View::make('home.index2', [   
                                'last_login'    => $last_login,
                                'total_order_per_week'    => $totalOrderPerWeek,
                                'total_delivery_per_week'    => $totalDeliveryPerWeek,
                                'total_value'	=> number_format($total_value,2), 
                                'total_cust'    => $total_cust,
                                'total_completed_transaction' => $total_completed_transaction,
                                'total_pending_transaction' => $total_pending_transaction,
                                'total_cancelled_transaction' => $total_cancelled_transaction,
                                'total_refund_transaction' => $total_refund_transaction,
                                'total_products' => $total_product,
                                'latestTransaction' => $latestTransaction,
                                'totalProductQty' => $totalProductQty,
                                'summaryLogisticStatus' => array(
                                    "TotalPendingAll"=>$TotalPendingAll,
                                    "TotalUndeliveredAll"=>$TotalUndeliveredAll,
                                    "TotalPartialAll"=>$TotalPartialAll,
                                    "TotalReturnedAll"=>$TotalReturnedAll,
                                    "TotalSendingAll"=>$TotalSendingAll,
                                    "TotalSentAll"=>$TotalSentAll
                                )
        ], $latest_trans);	

    }

	 /**
     * Display the dashboard.
     *
     * @return Response
     */
    
    public function anyIndex_11()
    { 
        try{
            
    	$user 			= User::find(Auth::id());
    	$date 			= $user->last_login;
    	$last_login		= date("d-M-Y H:i:s A", strtotime($date));
    	
        $total_cust 	= Customer::TotalCustomer();
        // $total_cust = 11357;
        // dd($total_cust);
        $total_value 	= Transaction::dashboard_total();
	    $latest_trans 	= Transaction::dashboard_latest_transaction();	//i.e. id, total_amount, transaction_date
        $total_product  = Product::dashboard_totalProducts();

        $latestTransaction = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(10)
                                        ->get();
        
        $latestTransaction2 = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(1)
                                        ->first();
                                        
        $total_completed_transaction = Transaction::where('status', '=', 'completed')->count();
        $total_pending_transaction = Transaction::where('status', '=', 'pending')->count();
        $total_cancelled_transaction = Transaction::where('status', '=', 'cancelled')->count();
        $total_refund_transaction = Transaction::where('status', '=', 'refund')->count();
        
        $currentdate = date("Y-m-d")." 23:59:59";
        $TotalPendingAll = LogisticTransaction::getAllTotalRecordByStatus(0,$currentdate);
        $TotalUndeliveredAll = LogisticTransaction::getAllTotalRecordByStatus(1,$currentdate);
        $TotalPartialAll = LogisticTransaction::getAllTotalRecordByStatus(2,$currentdate);
        $TotalReturnedAll = LogisticTransaction::getAllTotalRecordByStatus(3,$currentdate);
        $TotalSendingAll = LogisticTransaction::getAllTotalRecordByStatus(4,$currentdate);
        $TotalSentAll = LogisticTransaction::getAllTotalRecordByStatus(5,$currentdate);
        $TotalCancelledAll = LogisticTransaction::getAllTotalRecordByStatus(6,$currentdate);
        
        
        $totalProductQty = DB::select(DB::raw('SELECT LTI.name,SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id
                                WHERE LT.status IN (0)
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 50'));
        
        $NextTargetAmount = DB::table('jocom_target')->where('year', date("Y"))->first();
        
        // $CurrentSalesAmount = DB::table('jocom_transaction')
        //         ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
        //         ->where("jocom_transaction.status","completed")
        //         ->where("jocom_transaction.invoice_no","<>","")
        //         ->where("jocom_transaction.transaction_date",">=",date("Y")."-01-01 00:00:00")
        //         ->where("jocom_transaction.transaction_date","<=",date("Y")."-12-31 23:59:59")
        //         ->first();
        
       
         $CurrentSalesAmount =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(
                                CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                                ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                            END AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                         ->where("JT.transaction_date",">=",date("Y")."-01-01 00:00:00")
                        ->where("JT.transaction_date","<=",date("Y")."-12-31 23:59:59")
                        ->first();
                        
        $CurrentSalesAmount2019 =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                                ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                                ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                                ROUND(SUM(
                                CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                                ) , 2)
                            ELSE 
                                ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                            END AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                         ->where("JT.transaction_date",">=","2019-01-01 00:00:00")
                        ->where("JT.transaction_date","<=", "2019-12-31 23:59:59")
                        ->first();

                        
        $CurrentSalesAmountDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=",date("Y")."-01-01 00:00:00")
            ->where("JT.transaction_date","<=",date("Y")."-12-31 23:59:59")
            ->first();  
        
        $CurrentSalesAmountDeliveryChargesDateSales2019 =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=", "2019-01-01 00:00:00")
            ->where("JT.transaction_date","<=", "2019-12-31 23:59:59")
            ->first(); 
            
        // $currentAmount = (double)$CurrentSalesAmount->total_sales  + (double)$CurrentSalesAmountDeliveryChargesDateSales->DeliveryAmount;
        
        // $currentAmount2019 = (double)$CurrentSalesAmount2019->total_sales  + (double)$CurrentSalesAmountDeliveryChargesDateSales2019->DeliveryAmount;
                
        // $CurrentSalesAmount =  DB::table('jocom_transaction AS JT')
        //                 ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
        //                 ->select(
        //                     DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
        //                 		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)
        //                     ELSE 
        //                 		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)  
        //                     END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
        //                 		ROUND(SUM(
        //                         CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
        //                         ) + SUM(JT.gst_delivery), 2)
        //                     ELSE 
        //                 		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) +  SUM(JT.gst_delivery), 2)
        //                     END AS total_sales"))
        //                 ->whereIn('JT.status', ['completed'])
        //                 ->where('JT.invoice_no','<>','')
        //                  ->where("JT.transaction_date",">=",date("Y")."-01-01 00:00:00")
        //                 ->where("JT.transaction_date","<=",date("Y")."-12-31 23:59:59")
        //                 ->first();
                
    //   die($NextTargetAmount->amount);
        $percentageAchieved = ROUND(($currentAmount / $NextTargetAmount->amount) * 100,2);  
        
        $percentageAchieved2019 = ROUND(($currentAmount2019 / 10000000.00) * 100,2);   
        
        return View::make('home.index', [   
                                'last_login'    => $last_login,
                                'total_value'	=> number_format($total_value,2), 
                                'total_cust'    => $total_cust,
                                'total_completed_transaction' => $total_completed_transaction,
                                'total_pending_transaction' => $total_pending_transaction,
                                'total_cancelled_transaction' => $total_cancelled_transaction,
                                'total_refund_transaction' => $total_refund_transaction,
                                'total_products' => $total_product,
                                'latestTransaction' => $latestTransaction,
                                'totalProductQty' => $totalProductQty,
                                'NextTargetAmount' => $NextTargetAmount,
                                'percentageAchieved' => $percentageAchieved,
                                'CurrentSalesAmount' => $currentAmount,
                                'percentageAchieved2019' => $percentageAchieved2019,
                                'CurrentSalesAmount2019' => $currentAmount2019,
                                'summaryLogisticStatus' => array(
                                    "TotalPendingAll"=>$TotalPendingAll,
                                    "TotalUndeliveredAll"=>$TotalUndeliveredAll,
                                    "TotalPartialAll"=>$TotalPartialAll,
                                    "TotalReturnedAll"=>$TotalReturnedAll,
                                    "TotalSendingAll"=>$TotalSendingAll,
                                    "TotalSentAll"=>$TotalSentAll
                                )
        ], $latest_trans);	
            
        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
              
        }     
            
    }
     
    public function anyIndex()
    { 
        try{
    	$user 			= User::find(Auth::id());
    	$date 			= $user->last_login;
    	$last_login		= date("d-M-Y H:i:s A", strtotime($date));
        $total_cust 	= Customer::TotalCustomer();
        $total_value 	= Transaction::dashboard_total();
	    $latest_trans 	= Transaction::dashboard_latest_transaction();	//i.e. id, total_amount, transaction_date
        $total_product  = Product::dashboard_totalProducts();

        $latestTransaction = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(10)
                                        ->get();
        
        $latestTransaction2 = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(1)
                                        ->first();
                                        
        $total_completed_transaction = Transaction::where('status', '=', 'completed')->count();
        // $total_pending_transaction = Transaction::where('status', '=', 'pending')->count();
        $total_pending_transaction = LogisticTransaction::where('status', '=', '0')->count();
        $total_cancelled_transaction = Transaction::where('status', '=', 'cancelled')->count();
        $total_refund_transaction = Transaction::where('status', '=', 'refund')->count();
        
        $currentdate = date("Y-m-d")." 23:59:59";
        $TotalPendingAll = LogisticTransaction::getAllTotalRecordByStatus(0,$currentdate);
        $TotalUndeliveredAll = LogisticTransaction::getAllTotalRecordByStatus(1,$currentdate);
        $TotalPartialAll = LogisticTransaction::getAllTotalRecordByStatus(2,$currentdate);
        $TotalReturnedAll = LogisticTransaction::getAllTotalRecordByStatus(3,$currentdate);
        $TotalSendingAll = LogisticTransaction::getAllTotalRecordByStatus(4,$currentdate);
        $TotalSentAll = LogisticTransaction::getAllTotalRecordByStatus(5,$currentdate);
        $TotalCancelledAll = LogisticTransaction::getAllTotalRecordByStatus(6,$currentdate);
        
        
        $totalProductQty = DB::select(DB::raw('SELECT LTI.name,SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id
                                WHERE LT.status IN (0)
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 50'));
        
        $NextTargetAmount = DB::table('jocom_target')->where('year', date("Y"))->first();
        
        // $CurrentSalesAmount = DB::table('jocom_transaction')
        //         ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
        //         ->where("jocom_transaction.status","completed")
        //         ->where("jocom_transaction.invoice_no","<>","")
        //         ->where("jocom_transaction.transaction_date",">=",date("Y")."-01-01 00:00:00")
        //         ->where("jocom_transaction.transaction_date","<=",date("Y")."-12-31 23:59:59")
        //         ->first();
        
        
         $CurrentSalesAmount =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(
                                CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                                ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                            END AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                         ->where("JT.transaction_date",">=",date("Y")."-01-01 00:00:00")
                        ->where("JT.transaction_date","<=",date("Y")."-12-31 23:59:59")
                        ->first();
                        
        $CurrentSalesAmount2019 =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                                ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                                ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                                ROUND(SUM(
                                CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                                ) , 2)
                            ELSE 
                                ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                            END AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                         ->where("JT.transaction_date",">=","2024-01-01 00:00:00")
                        ->where("JT.transaction_date","<=", "2024-12-31 23:59:59")
                        ->first();

                        
        $CurrentSalesAmountDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=",date("Y")."-01-01 00:00:00")
            ->where("JT.transaction_date","<=",date("Y")."-12-31 23:59:59")
            ->first();  
        
        $CurrentSalesAmountDeliveryChargesDateSales2019 =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=", "2024-01-01 00:00:00")
            ->where("JT.transaction_date","<=", "2024-12-31 23:59:59")
            ->first(); 
            
        $currentAmount = (double)$CurrentSalesAmount->total_sales  + (double)$CurrentSalesAmountDeliveryChargesDateSales->DeliveryAmount;
        
        $currentAmount2019 = (double)$CurrentSalesAmount2019->total_sales  + (double)$CurrentSalesAmountDeliveryChargesDateSales2019->DeliveryAmount;
                
        // $CurrentSalesAmount =  DB::table('jocom_transaction AS JT')
        //                 ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
        //                 ->select(
        //                     DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
        //                 		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)
        //                     ELSE 
        //                 		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)  
        //                     END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
        //                 		ROUND(SUM(
        //                         CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
        //                         ) + SUM(JT.gst_delivery), 2)
        //                     ELSE 
        //                 		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) +  SUM(JT.gst_delivery), 2)
        //                     END AS total_sales"))
        //                 ->whereIn('JT.status', ['completed'])
        //                 ->where('JT.invoice_no','<>','')
        //                  ->where("JT.transaction_date",">=",date("Y")."-01-01 00:00:00")
        //                 ->where("JT.transaction_date","<=",date("Y")."-12-31 23:59:59")
        //                 ->first();
                
       
        $percentageAchieved = ROUND(($currentAmount / $NextTargetAmount->amount) * 100,2);  
        
        $percentageAchieved2019 = ROUND(($currentAmount2019 / 10000000.00) * 100,2);   
        
        return View::make('home.index', [   
                                'last_login'    => $last_login,
                                'total_value'	=> number_format($total_value,2), 
                                'total_cust'    => $total_cust,
                                'total_completed_transaction' => $total_completed_transaction,
                                'total_pending_transaction' => $total_pending_transaction,
                                'total_cancelled_transaction' => $total_cancelled_transaction,
                                'total_refund_transaction' => $total_refund_transaction,
                                'total_products' => $total_product,
                                'latestTransaction' => $latestTransaction,
                                'totalProductQty' => $totalProductQty,
                                'NextTargetAmount' => $NextTargetAmount,
                                'percentageAchieved' => $percentageAchieved,
                                'CurrentSalesAmount' => $currentAmount,
                                'percentageAchieved2019' => $percentageAchieved2019,
                                'CurrentSalesAmount2019' => $currentAmount2019,
                                'summaryLogisticStatus' => array(
                                    "TotalPendingAll"=>$TotalPendingAll,
                                    "TotalUndeliveredAll"=>$TotalUndeliveredAll,
                                    "TotalPartialAll"=>$TotalPartialAll,
                                    "TotalReturnedAll"=>$TotalReturnedAll,
                                    "TotalSendingAll"=>$TotalSendingAll,
                                    "TotalSentAll"=>$TotalSentAll
                                )
        ], $latest_trans);	
            
        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
              
        }     
            
    }
    
    public function anyProfile($id)
    {
        $user  = User::getUserRole($id);
        return View::make('home.profile')->with(array('user' => $user));
    }

	public function anyUpdate($id)
    {
        $arr_input_all = Input::all();

        foreach($arr_input_all as $key => $value) {
            if(isset($value)) {

                $arr_input[$key] = $value;
             
                switch($key) {
                    case 'password'     :   
                                $arr_validate[$key] = "alpha_num|between:5,12|confirmed";
                                break;

                    case 'password_confirmation' :   
                                $arr_validate[$key] = "alpha_num|between:5,12";
                                break;
                }
               
            }
            
        }
      
        $validator = Validator::make($arr_input, $arr_validate);

        if ($validator->passes()) {
            // $user = new User;
            $udata = array();

            foreach($arr_input as $key => $value) {
                switch ($key) {
                    case 'password':
                        $udata['password'] = Hash::make($value);
                        break;
                }
            }

            if(User::updateUser($id, $udata)){
                Session::flash('success', 'Profile setting has been successfully save!');
                return Redirect::to('home/profile/'.$id);
            } 
        
        }else {
            // echo "<br> ERROR records updated! ";
            return Redirect::back()
                        ->withInput()
                        ->withErrors($validator)->withInput();
        }
    }
   
    
    public function anyDashboarddata() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            /*
             *  'navigation':navigation,
                    'rangeType':type,
                    'startDate':startDate,
                    'toDate':toDate
             */
            $navigate = Input::get('navigation');
            $period_type = Input::get('rangeType');
            $start_date = Input::get('startDate');
            $to_date = Input::get('toDate');
            $cumumulative_type = Input::get('cumumulative_type');
            
            $UserRegionIdList = [];
            $sysAdminInfo = User::where("username",Session::get('username'))->first();
           
            $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->where("status",1)->get();
            
            foreach ($SysAdminRegion as $key => $value) {
                if($value->region_id != 0){
                    $UserRegionIdList[] = $value->region_id;
                }
            }
            
            /*
             * Use morris.js data format
             */
            $cumulative_amount = 0;
            $cumulative_gst_amount = 0;
            
            $cumulative_amount2 = 0;
            $cumulative_gst_amount2 = 0;
            
            $dataChartCollection = array();
            
            switch ($period_type) {
                
                case 3: // Year
                
                    if($navigate == 1){ // LEFT
                        $startDate = date('Y-01-01', strtotime(date($start_date).' -1 year', time()))." 00:00:00";
                        $toDate = date('Y-12-t', strtotime(date($start_date).' -1 year', time()))." 00:00:00";
                    }else if($navigate == 2){ // RIGHT
                        $startDate = date('Y-01-01', strtotime(date($start_date).' +1 year', time()))." 00:00:00";
                        $toDate = date('Y-12-t', strtotime(date($start_date).' +1 year', time()))." 00:00:00";
                    }else{
                        $startDate = date('2015-01-01', strtotime(date($start_date), time()))." 00:00:00";
                        $toDate = date('Y-12-t', strtotime(date($start_date), time()))." 23:59:59";
}
                    // create 12 month 
                    $dataChartCollection = array();
                  //   echo $startDate;
                    $year = date('Y',strtotime($startDate));
                    $displayChartTitle = $year;
                    
                    $current_year = date("Y");
                    $current_month = date("m");
                    if($current_year == $year){
                        $endMonth = $current_month;
                    }else{
                        $endMonth = 12;
                    }
                    
                    $displayStartDate = date("d M Y", strtotime($startDate));
                    $displayEndDate = date("d M Y", strtotime($toDate));
                    $year_now = date("Y");

                    for($x=2015;$x<=$year_now;$x++){
                        
                        $startDate = date('Y-01-01 00:00:00',strtotime($x.'-'.'01'));
                        $endDate = date('Y-12-t 23:59:59',strtotime($x.'-'.'12'));
                        
                        
                        $startDate = date("Y-m-d H:i:s", strtotime($startDate)); //date('Y-01-01 00:00:00',strtotime($x.'-'.'01'));
                        $endDate = date("Y-m-d H:i:s", strtotime($endDate)); //date('Y-12-t 23:59:59',strtotime($x.'-'.'12'));
                        
                        $monthDesc= $x;
                        $sumInformation = Transaction::sumValue($startDate ,$endDate,$UserRegionIdList);
                        $sumInformation2 = Transaction::sumValueTwo($startDate ,$endDate,$UserRegionIdList);
                        
                        if($x <= $year_now){
                            if($cumumulative_type == 1){
                                $cumulative_amount = $cumulative_amount + $sumInformation->total_order;
                                $cumulative_gst_amount = $cumulative_gst_amount + $sumInformation->gst_total;
                                
                                $cumulative_amount2 = $cumulative_amount2 + $sumInformation2->total_order;
                                $cumulative_gst_amount2 = $cumulative_gst_amount2 + $sumInformation2->gst_total;
                            }else{
                                $cumulative_amount = $sumInformation->total_order;
                                $cumulative_gst_amount = $sumInformation->gst_total;
                                
                                $cumulative_amount2 = $sumInformation2->total_order;
                                $cumulative_gst_amount2 = $sumInformation2->gst_total;
                            }
                        }else{
                                $cumulative_amount = 0;
                                $cumulative_gst_amount = 0;
                                
                                $cumulative_amount2 = 0;
                                $cumulative_gst_amount2 = 0;
                        }
                        
//                        $cumulative_amount = $cumulative_amount + $sumInformation->total_order;
//                        $cumulative_gst_amount = $cumulative_gst_amount + $sumInformation->gst_total;
                        
                        $subData = array(
                            "x_description"=>$monthDesc,
                            "startDate"=>$startDate,
                            "endDate"=>$endDate,
                            "total_amount"=>$cumulative_amount,
                            "gst_amount"=>$cumulative_gst_amount,
                            "total_amount2"=>$cumulative_amount2,
                            "gst_amount2"=>$cumulative_gst_amount2,
                        );
                        
                        array_push($dataChartCollection, $subData);
                    }
                    break;
                case 1: // Year
                    
                    
                    
                    if($navigate == 1){ // LEFT
                        $startDate = date('Y-01-01', strtotime(date($start_date).' -1 year', time()))." 00:00:00";
                        $toDate = date('Y-12-t', strtotime(date($start_date).' -1 year', time()))." 00:00:00";
                    }else if($navigate == 2){ // RIGHT
                        $startDate = date('Y-01-01', strtotime(date($start_date).' +1 year', time()))." 00:00:00";
                        $toDate = date('Y-12-t', strtotime(date($start_date).' +1 year', time()))." 00:00:00";
                    }else{
                        $startDate = date('Y-01-01', strtotime(date($start_date), time()))." 00:00:00";
                        $toDate = date('Y-12-t', strtotime(date($start_date), time()))." 23:59:59";
}
                    // create 12 month 
                    $dataChartCollection = array();
                  //   echo $startDate;
                    $year = date('Y',strtotime($startDate));
                    $displayChartTitle = $year;
                    
                    $current_year = date("Y");
                    $current_month = date("m");
                    if($current_year == $year){
                        $endMonth = $current_month;
                    }else{
                        $endMonth = 12;
                    }
                    
                    $displayStartDate = date("d M Y", strtotime($startDate));
                    $displayEndDate = date("d M Y", strtotime($toDate));
                    
                    
                    for($x=1;$x<=12;$x++){
                        
                        $startDate = date('Y-m-01 00:00:00',strtotime($year.'-'.$x));
                        $endDate = date('Y-m-t 23:59:59',strtotime($year.'-'.$x));

                        $monthDesc= date('M', strtotime($startDate));
                        $sumInformation = Transaction::sumValue($startDate ,$endDate,$UserRegionIdList);
                        $sumInformation2 = Transaction::sumValueTwo($startDate ,$endDate,$UserRegionIdList);
                        
                        if($x <= $endMonth){
                            if($cumumulative_type == 1){
                                $cumulative_amount = $cumulative_amount + $sumInformation->total_order;
                                $cumulative_gst_amount = $cumulative_gst_amount + $sumInformation->gst_total;
                                
                                $cumulative_amount2 = $cumulative_amount2 + $sumInformation2->total_order;
                                $cumulative_gst_amount2 = $cumulative_gst_amount2 + $sumInformation2->gst_total;
                            }else{
                                $cumulative_amount = $sumInformation->total_order;
                                $cumulative_gst_amount = $sumInformation->gst_total;
                                
                                $cumulative_amount2 = $sumInformation2->total_order;
                                $cumulative_gst_amount2 = $sumInformation2->gst_total;
                            }
                        }else{
                                $cumulative_amount = 0;
                                $cumulative_gst_amount = 0;
                                
                                $cumulative_amount2 = 0;
                                $cumulative_gst_amount2 = 0;
                        }
                        
//                        $cumulative_amount = $cumulative_amount + $sumInformation->total_order;
//                        $cumulative_gst_amount = $cumulative_gst_amount + $sumInformation->gst_total;
                        
                        $subData = array(
                            "x_description"=>$monthDesc,
                            "total_amount"=>$cumulative_amount,
                            "gst_amount"=>$cumulative_gst_amount,
                            "total_amount2"=>$cumulative_amount2,
                            "gst_amount2"=>$cumulative_gst_amount2,
                        );
                        
                        array_push($dataChartCollection, $subData);
                    }
                       
                    break;
                    
                case 2: // Weekly
                    
                    
                    
                    if($navigate == 1){ // LEFT
                        $startDate = date('Y-m-01', strtotime(date($start_date).' -1 month', time()))." 00:00:00";
                        $toDate = date('Y-m-t', strtotime(date($start_date).' -1 month', time()))." 00:00:00";
                    }else if($navigate == 2){ // RIGHT
                        $startDate = date('Y-m-01', strtotime(date($start_date).' +1 month', time()))." 00:00:00";
                        $toDate = date('Y-m-t', strtotime(date($start_date).' +1 month', time()))." 00:00:00";
                    }else{
                        $startDate = date('Y-m-01', strtotime(date($start_date), time()))." 00:00:00";
                        $toDate = date('Y-m-t', strtotime(date($start_date), time()))." 23:59:59";
                    }
                    
                    $year = date('M Y',strtotime($startDate));
                    $displayChartTitle = $year;
                    $displayStartDate = date("d M Y", strtotime($startDate));
                    $displayEndDate = date("d M Y", strtotime($toDate));
                    
//                    echo $displayChartTitle;
                    
                    $start = new DateTime((string)$startDate);
                    $end = new DateTime((string)$toDate);

                    $interval = new DateInterval('P1D');
                    $dateRange = new DatePeriod($start, $interval, $end);
                    
                    $current_date = date("Y-m-d");
                    $current_month = date("m");
                    if($current_year == $year){
                        $endMonth = $current_month;
                    }else{
                        $endMonth = 12;
                    }

                    // Seperate date range in weekly period this is because 11Street API support within 7 days range of date only 
                    foreach ($dateRange as $date) {
                        $weeks[$weekNumber][] = $date->format('Y-m-d');
                        if ($date->format('w') == 6) {
                            if(count($weeks[$weekNumber]) == 1){
                                $weeks[$weekNumber][] = $date->format('Y-m-d');
                            }
                            $weekNumber++;
                        }
                    }
                    
                    foreach ($weeks as $kWeek => $vWeek) {
                        $sumInformation = Transaction::sumValue($vWeek[0]. "00:00:00",$vWeek[count($vWeek)-1]. "23:59:59",$UserRegionIdList);
                        $sumInformation2 = Transaction::sumValueTwo($vWeek[0]. "00:00:00",$vWeek[count($vWeek)-1]. "23:59:59",$UserRegionIdList);
                        
                        if($vWeek[0] < $current_date){
                            if($cumumulative_type == 1){
                                $cumulative_amount = $cumulative_amount + $sumInformation->total_order;
                                $cumulative_gst_amount = $cumulative_gst_amount + $sumInformation->gst_total;
                                
                                $cumulative_amount2 = $cumulative_amount2 + $sumInformation2->total_order;
                                $cumulative_gst_amount2 = $cumulative_gst_amount2 + $sumInformation2->gst_total;
                            }else{
                                $cumulative_amount = $sumInformation->total_order;
                                $cumulative_gst_amount = $sumInformation->gst_total;
                                
                                $cumulative_amount2 = $sumInformation2->total_order;
                                $cumulative_gst_amount2 = $sumInformation2->gst_total;
                            }
                            
                        }else{
                            $cumulative_amount = 0;
                            $cumulative_gst_amount = 0;
                            
                            $cumulative_amount2 = 0;
                            $cumulative_gst_amount2 = 0;
                        }
                        
                        $subData = array(
                            "x_description"=> date_format(date_create($vWeek[0]),"d/m/Y")." - ".date_format(date_create($vWeek[count($vWeek)-1]),"d/m/Y"),
                            "total_amount"=>$cumulative_amount, //number_format($cumulative_amount, 2, '.', '') ,
                            "gst_amount"=>$cumulative_gst_amount, //number_format($cumulative_gst_amount, 2, '.', '') ,
                            "total_amount2"=>$cumulative_amount2,
                            "gst_amount2"=>$cumulative_gst_amount2,
                        );
                        
                        array_push($dataChartCollection, $subData);
                        
                    }
  
                    break;
                case 3: // Monthly
                    break;

                default:
                    break;
                
            }
            
            
            $currentdate = date("Y-m-d")." 23:59:59";
            $data['DateSelection'] = array(
                "startDate" => $startDate,
                "toDate" => $toDate,
                "displayStartDate" => $displayStartDate,
                "displayChartTitle" => $displayChartTitle,
                "displayEndDate" => $displayEndDate,
                "today" => date("Y-m-d"),
                "WeeklyStartDate" =>  date("Y-m-d", strtotime('monday this week')), 
                "WeeklyEndDate" =>  date("Y-m-d",  strtotime('sunday this week')),
                "MonthStartDate" => date('Y-m-1'),
                "MonthEndDate" => date('Y-m-t')
            );
            
            $data['BarChartData'] = $dataChartCollection;
            
        
        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
           
           
        } 

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    public function anyDashboarddriver() {

        $isError = 0;
        $respStatus = 1;
        $data = array();
        try {
        $rangeType = Input::get("rangeType");
        $startDate = Input::get("startDate");
        $toDate = Input::get("toDate");
        $navigate = Input::get("navigation");
        
        // default weekly
        if ($startDate == '' && $toDate == '') {
            $startDate = date("Y-m-d", strtotime('monday this week'));
            $toDate = date("Y-m-d",  strtotime('sunday this week'));
        }

        switch ($rangeType) {
            case 1: // Daily
                if($navigate == 1){ // LEFT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' -1 days', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                }else if($navigate == 2){ // RIGHT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' +1 days', time()))." 00:00:00";;
                    $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                }else{
                    $startDate = date('Y-m-d', strtotime(date($startDate), time()))." 00:00:00";;
                    $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                }


                break;
            case 2: // Weekly
                
                $day = 7;
                if($navigate == 1){ // LEFT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' -'.$day.' days', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($toDate).' -'.($day).' days', time()))." 23:23:59";
                }else if($navigate == 2){ // RIGHT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' +'.$day.' days', time()))." 00:00:00";;
                    $toDate = date('Y-m-d', strtotime(date($toDate).' +'.$day.' days', time()))." 23:23:59";
                }

                break;
            case 3: // Monthly
        
                if($navigate == 1){ // LEFT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' first day of last month', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($toDate).' last day of last month', time()))." 23:23:59";
                }else if($navigate == 2){ // RIGHT
                    $startDate = date('Y-m-d', strtotime(date($startDate).' first day of next month', time()))." 00:00:00";
                    $toDate = date('Y-m-d', strtotime(date($toDate).' last day of next month', time()))." 23:23:59";
                }

                break;

            default:
                break;
        }

        $drivers_ = DB::table('logistic_driver')
            ->where('status', '=', '1')
            ->where('is_logistic_dashboard', '=', 1)
            ->select('id', 'name')
            ->get();

        $drivers_id = array();
        $driver_status = array();
        foreach ($drivers_ as $driver) {
            array_push($drivers_id, $driver->id);
            $driver_status[$driver->id]['name'] = $driver->name;
            $driver_status[$driver->id]['sent'] = 0;
            $driver_status[$driver->id]['return'] = 0;
            $driver_status[$driver->id]['total'] = 0;
        }

        $logistic_driver = DB::table('logistic_batch')
                            ->whereIn('driver_id', $drivers_id)
                            ->where(function($query) use ($startDate, $toDate) {
                                $query->where(function($query) use ($startDate, $toDate) {
                                    $query->where('batch_date', '>=', $startDate)
                                      ->where('batch_date', '<=', $toDate);
                                })
                                ->orWhere(function($query) use ($startDate, $toDate) {
                                    $query->where('modify_date', '>=', $startDate)
                                          ->where('modify_date', '<=', $toDate);
                                })
                                ->orWhere(function($query) use ($startDate, $toDate) {
                                    $query->where('accept_date', '>=', $startDate)
                                          ->where('accept_date', '<=', $toDate);
                                });
                            })
                            ->select('driver_id', 'logistic_batch.status')
                            ->orderBy('driver_id')
                            ->orderBy('logistic_batch.status')
                            ->get();
        
        $total_batch = 0;
        foreach ($logistic_driver as $driver) {
            if ($driver->status == 2) {
                $driver_status[$driver->driver_id]['return']++;
            } else if ($driver->status == 4) {
                $driver_status[$driver->driver_id]['sent']++;
            } 

            $driver_status[$driver->driver_id]['total']++;
            $total_batch++;
        }

        // convert to normal array
        $driver_array = array();
        foreach ($driver_status as $driver) {
            if ($total_batch > 0) {
                $driver['sent'] = $driver['sent'] . ' (' . number_format(($driver['sent'] / $total_batch)* 100, 2) . '%)';
                $driver['sent_percent'] = number_format(($driver['sent'] / $total_batch)* 100, 2);
                $driver['return'] = $driver['return'] . ' (' . number_format(($driver['return'] / $total_batch) * 100, 2) . '%)';
                $driver['return_percent'] = number_format(($driver['return'] / $total_batch)* 100, 2);
                $driver['total'] = $driver['total'] . ' (' . number_format(($driver['total'] / $total_batch) * 100, 2) . '%)';
                $driver['total_percent'] = number_format(($driver['total'] / $total_batch)* 100, 2);
            } 
            
            array_push($driver_array, $driver);
        }
        
        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
           
           
        } 
        return array(
            "weekly_start_date" =>  date("Y-m-d", strtotime('monday this week')), 
            "weekly_end_date" =>  date("Y-m-d",  strtotime('sunday this week')),
            "monthly_start_date" => date('Y-m-1'),
            "monthly_end_date" => date('Y-m-t'),
            'start_date' => $startDate,
            'end_date' => $toDate,
            'start_date_display' => date_format(date_create($startDate),"d M Y"),
            'end_date_display' => date_format(date_create($toDate),"d M Y"),
            'driver' => $driver_array
        );
        
        
    }
    
    public function anyDaystatistic(){
        
        try{
            
            $type = Input::get("typeData");
            $currentDate = Input::get("currentDate");
//            
//            $type = 'YEARCOM';
//            $currentDate = '2018-06-29';
            
            switch ($type) {
                case 'DAYCOM':

                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-d",strtotime("-1 day", $date_user_entered));
                    $nextDate = date("Y-m-d",strtotime("+1 day", $date_user_entered));
                   

                    // $selectedDateSales = DB::table('jocom_transaction')
                    //     ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                    //     ->where("jocom_transaction.status","completed")
                    //     ->where('jocom_transaction.invoice_no','<>','')
                    //     ->where("jocom_transaction.transaction_date",">=","$currentDate 00:00:00")
                    //     ->where("jocom_transaction.transaction_date","<=","$currentDate 23:59:59")
                    //     ->first();
                        
                        
                    // $selectedDateSales =  DB::table('jocom_transaction AS JT')
                    //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                    //     ->select(
                    //         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                    //         END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(
                    //             CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                    //             ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    //         END AS total_sales"))
                    //     ->whereIn('JT.status', ['completed'])
                    //     ->where('JT.invoice_no','<>','')
                    //     ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                    //     ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                    //     ->first(); 
                        
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                        ->first(); 
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                        ->first();   
            

                    // $PreviousDateSales = DB::table('jocom_transaction')
                    //     ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                    //     ->where("jocom_transaction.status","completed")
                    //      ->where('jocom_transaction.invoice_no','<>','')
                    //     ->where("transaction_date",">=",$date_one_year_ago." 00:00:00")
                    //     ->where("transaction_date","<=",$date_one_year_ago." 23:59:59")
                    //     ->first();
                        
                        
                    // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                    //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                    //     ->select(
                    //         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                    //         END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(
                    //             CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                    //             ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    //         END AS total_sales"))
                    //     ->whereIn('JT.status', ['completed'])
                    //     ->where('JT.invoice_no','<>','')
                    //     ->where("JT.transaction_date",">=",$date_one_year_ago." 00:00:00")
                    //     ->where("JT.transaction_date","<=",$date_one_year_ago." 23:59:59")
                    //     ->first(); 
                        
                    $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->first(); 
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->first();   
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales + (double)$PreviousDeliveryChargesDateSales->DeliveryAmount;
                    $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
                    
                    $previousDateDisplay = date("d, F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("d, F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        //echo "<br>".date("d, F Y",strtotime("-".$i." day", $date_user_entered));
                        $labelData[0][] = date("d F",strtotime("-".$i." day ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." day ", $date_user_entered));
                        
                        
                        // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        // ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        // ->select(
                        //     DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)  
                        //     END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(
                        //         CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        //         ) + SUM(JT.gst_delivery), 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) +  SUM(JT.gst_delivery), 2)
                        //     END AS total_sales"))
                        // ->whereIn('JT.status', ['completed'])
                        // ->where('JT.invoice_no','<>','')
                        // ->where("JT.transaction_date",">=",$naviDate." 00:00:00")
                        // ->where("JT.transaction_date","<=",$naviDate." 23:59:59")
                        // ->first();
                        
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first(); 
                        
                        
                        // $PreviousDateSales = DB::table('jocom_transaction')
                        // ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                        // ->where("jocom_transaction.status","completed")
                        // ->where("jocom_transaction.transaction_date",">=",$naviDate." 00:00:00")
                        // ->where("jocom_transaction.transaction_date","<=",$naviDate." 23:59:59")
                        // ->first();
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                    
                    for($i=5; $i>=0; $i--){
                        
                        $labelData[1][]  = date("d F",strtotime("-".$i." day", strtotime($date_one_year_ago)));
                        $naviDate= date("Y-m-d",strtotime("-".$i." day",strtotime($date_one_year_ago)));

                        // $PreviousDateSales = DB::table('jocom_transaction')
                        // ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                        // ->where("jocom_transaction.status","completed")
                        // ->where("jocom_transaction.transaction_date",">=",$naviDate." 00:00:00")
                        // ->where("jocom_transaction.transaction_date","<=",$naviDate." 23:59:59")
                        // ->first();
                        
                        // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        // ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        // ->select(
                        //     DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) + SUM(JT.delivery_charges) + SUM(JT.gst_delivery), 2)  
                        //     END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(
                        //         CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        //         ) + SUM(JT.gst_delivery), 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) +  SUM(JT.gst_delivery), 2)
                        //     END AS total_sales"))
                        // ->whereIn('JT.status', ['completed'])
                        // ->where('JT.invoice_no','<>','')
                        // ->where("JT.transaction_date",">=",$naviDate." 00:00:00")
                        // ->where("JT.transaction_date","<=",$naviDate." 23:59:59")
                        // ->first();
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first(); 
                        
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                    
                    $StatusCompare = $this->getDateStatusCompare($currentDate);
                    // echo "<pre>";
                    // print_r($StatusCompare);
                    // echo "</pre>";

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );


                    break;
                    
                case 'MONTHCOM':

                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-01",strtotime("-1 month", $date_user_entered));
                    $nextDate = date("Y-m-01",strtotime("+1 month", $date_user_entered));
                    
                    $firstDateIntheMonthCurrentYear = date("Y-m-01",$date_user_entered);
                    $lastDateIntheMonthCurrentYear = date("Y-m-t",$date_user_entered);

                    $firstDateIntheMonthLastYear = date("Y-m-01",strtotime("-1 year", $date_user_entered));
                    $lastDateIntheMonthLastYear = date("Y-m-t",strtotime("-1 year", $date_user_entered));

                    // $selectedDateSales = DB::table('jocom_transaction')
                    //     ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                    //     ->where("jocom_transaction.status","completed")
                    //     ->where('jocom_transaction.invoice_no','<>','')
                    //     ->where("jocom_transaction.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                    //     ->where("jocom_transaction.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                    //     ->first();
                        
                    // $selectedDateSales =  DB::table('jocom_transaction AS JT')
                    //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                    //     ->select(
                    //         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                    //         END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(
                    //             CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                    //             ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    //         END AS total_sales"))
                    //     ->whereIn('JT.status', ['completed'])
                    //     ->where('JT.invoice_no','<>','')
                    //     ->where("JT.transaction_date",">=",$firstDateIntheMonthCurrentYear." 00:00:00")
                    //     ->where("JT.transaction_date","<=",$lastDateIntheMonthCurrentYear." 23:59:59")
                    //     ->first();
                        
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();   
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();

                    // $PreviousDateSales = DB::table('jocom_transaction')
                    //     ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                    //     ->where("jocom_transaction.status","completed")
                    //     ->where('jocom_transaction.invoice_no','<>','')
                    //     ->where("jocom_transaction.transaction_date",">=",$firstDateIntheMonthLastYear." 00:00:00")
                    //     ->where("jocom_transaction.transaction_date","<=",$lastDateIntheMonthLastYear." 23:59:59")
                    //     ->first();
                        
                    // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                    //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                    //     ->select(
                    //         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                    //         END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(
                    //             CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                    //             ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    //         END AS total_sales"))
                    //     ->whereIn('JT.status', ['completed'])
                    //     ->where('JT.invoice_no','<>','')
                    //     ->where("JT.transaction_date",">=",$firstDateIntheMonthLastYear." 00:00:00")
                    //     ->where("JT.transaction_date","<=",$lastDateIntheMonthLastYear." 23:59:59")
                    //     ->first();
                        
                     $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales;
                    $currentAmount = (double)$selectedDateSales->total_sales  ;
                    
                    // $previousAmount = (double)$PreviousDateSales->total_sales + (double)$PreviousDeliveryChargesDateSales->DeliveryAmount;
                    // $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;

                    $previousDateDisplay = date("F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        //echo "<br>".date("d, F Y",strtotime("-".$i." day", $date_user_entered));
                        $labelData[0][] = date("M",strtotime("-".$i." month ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." month ", $date_user_entered));
                        
                        $firstDateIntheMonthNaviDate = date("Y-m-01",strtotime($naviDate));
                        $lastDateIntheMonthNaviDate = date("Y-m-t",strtotime($naviDate));
                        
                        // $PreviousDateSales = DB::table('jocom_transaction')
                        // ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                        // ->where("jocom_transaction.status","completed")
                        // ->where('jocom_transaction.invoice_no','<>','')
                        // ->where("jocom_transaction.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        // ->where("jocom_transaction.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        // ->first();
                        
                        // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        // ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        // ->select(
                        //     DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                        //     END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(
                        //         CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        //         ) , 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                        //     END AS total_sales"))
                        // ->whereIn('JT.status', ['completed'])
                        // ->where('JT.invoice_no','<>','')
                        // ->where("JT.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        // ->where("JT.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        // ->first();
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    for($i=5; $i>=0; $i--){
                        
                        $labelData[1][]  = date("M",strtotime("-".$i." month", strtotime($date_one_year_ago)));
                        $naviDate= date("Y-m-d",strtotime("-".$i." month",strtotime($date_one_year_ago)));
                        
                        $firstDateIntheMonthNaviDate = date("Y-m-01",strtotime($naviDate));
                        $lastDateIntheMonthNaviDate = date("Y-m-t",strtotime($naviDate));

                        // $PreviousDateSales = DB::table('jocom_transaction')
                        // ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                        // ->where("jocom_transaction.status","completed")
                        // ->where('jocom_transaction.invoice_no','<>','')
                        // ->where("jocom_transaction.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        // ->where("jocom_transaction.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        // ->first();
                        
                        // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        // ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        // ->select(
                        //     DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                        //     END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        // 		ROUND(SUM(
                        //         CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        //         ) , 2)
                        //     ELSE 
                        // 		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                        //     END AS total_sales"))
                        // ->whereIn('JT.status', ['completed'])
                        // ->where('JT.invoice_no','<>','')
                        // ->where("JT.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        // ->where("JT.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        // ->first();
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $month = date("m",$date_user_entered);
                    $year = date("Y",$date_user_entered);
                    $StatusCompare = $this->getMonthStatusCompare($year, $month);
                   

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );


                    break;
                    
                case 'YEARCOM':

                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $first_date_of_selected_year = date("Y-01-01",$date_user_entered);
                    $last_date_of_selected_year = date("Y-12-31",$date_user_entered);
                    
                    $first_date_of_one_year_ago = date("Y-01-01",strtotime($date_one_year_ago));
                    $last_date_of_one_year_ago = date("Y-12-31",strtotime($date_one_year_ago));
             
                    $previousDate = date("Y-01-01",strtotime("-1 year", $date_user_entered));
                    $nextDate = date("Y-01-01",strtotime("+1 year", $date_user_entered));
                    
                    $firstDateIntheMonthCurrentYear = date("Y-m-01",$date_user_entered);
                    $lastDateIntheMonthCurrentYear = date("Y-m-t",$date_user_entered);

                    $firstDateIntheMonthLastYear = date("Y-m-01",strtotime("-1 year", $date_user_entered));
                    $lastDateIntheMonthLastYear = date("Y-m-t",strtotime("-1 year", $date_user_entered));

                    // $selectedDateSales = DB::table('jocom_transaction')
                    //     ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                    //     ->where("jocom_transaction.status","completed")
                    //     ->where('jocom_transaction.invoice_no','<>','')
                    //     ->where("jocom_transaction.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                    //     ->where("jocom_transaction.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                    //     ->first();
                        
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();     
                        
                    //  $selectedDateSales =  DB::table('jocom_transaction AS JT')
                    //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                    //     ->select(
                    //         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                    //         END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(
                    //             CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                    //             ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    //         END AS total_sales"))
                    //     ->whereIn('JT.status', ['completed'])
                    //     ->where('JT.invoice_no','<>','')
                    //     ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                    //     ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                    //     ->first(); 
                        
                   
                        
                    // echo "<pre>";
                    // print_r($selectedDateSales);
                    // echo "</pre>";
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();   
                    
                    // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                    //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                    //     ->select(
                    //         DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                    //         END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                    //     		ROUND(SUM(
                    //             CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                    //             ) , 2)
                    //         ELSE 
                    //     		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    //         END AS total_sales"))
                    //     ->whereIn('JT.status', ['completed'])
                    //     ->where('JT.invoice_no','<>','')
                    //     ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                    //     ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                    //     ->first();
                        
                        
                    
                    // $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                    //     ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                    //     ->select(
                    //         DB::raw("CASE
                    //             WHEN
                    //                 YEAR(JT.transaction_date) >= 2017
                    //             THEN
                    //                 ROUND(SUM(JTD.actual_total_amount),2)
                    //             ELSE ROUND(SUM(CASE
                    //                         WHEN JTD.ori_price IS NULL THEN JTD.price
                    //                         ELSE JTD.ori_price
                    //                     END * JTD.unit),
                    //                     2)
                    //         END AS total_sales"))
                    //     ->whereIn('JT.status', ['completed'])
                    //     ->where('JT.invoice_no','<>','')
                    //     ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                    //     ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                    //     ->first();
                 
                    $PreviousDateSales =    DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                        ->first(); 
                
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                        ->first();   
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales ;//+ (double)$PreviousDeliveryChargesDateSales->DeliveryAmount;
                    $currentAmount = (double)$selectedDateSales->total_sales  ; //+ (double)$DeliveryChargesDateSales->DeliveryAmount;

                    $previousDateDisplay = date("Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        $labelData[0][] = date("Y",strtotime("-".$i." year ", $date_user_entered));
                        $naviDate = date("Y-01-01",strtotime("-".$i." year ", $date_user_entered));
                        
                        $firstDateIntheMonthNaviDate = date("Y-01-01",strtotime($naviDate));
                        $lastDateIntheMonthNaviDate = date("Y-12-t",strtotime($naviDate));
                        
                        // $PreviousDateSales = DB::table('jocom_transaction')
                        //     ->select(DB::raw('SUM(jocom_transaction.total_amount + jocom_transaction.gst_total) as total_sales'))
                        //     ->where("jocom_transaction.status","completed")
                        //     ->where('jocom_transaction.invoice_no','<>','')
                        //     ->where("jocom_transaction.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        //     ->where("jocom_transaction.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        //     ->first();
                            
                            
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END + CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(
                                CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                                ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                            END AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        ->where("JT.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        ->first();
                        
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $year = date("Y",$date_user_entered);
                
                    $StatusCompare = $this->getYearStatusCompare($year);

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );


                    break;

                default:
                    break;
            }
        
        } catch (Exception $ex) {
            

        } finally {
            
            return array(
                "previousDate" => $previousDate,
                "nextDate" => $nextDate,
                "currentDate" => $currentDate,
                "currentAmount" => number_format($currentAmount,2,".",","),
                "previousAmount" => number_format($previousAmount,2,".",","),
                "previousDateDisplay" => $previousDateDisplay,
                "currentDateDisplay" => $currentDateDisplay,
                "last5cycle" => $last5cycle,
            );
            
        }
        
        
    }
    
    /**
     * Results for Top Transactions Products in detail - @json
     * updated by Niyaz at 16 Oct 2K18 4.35pm
     * Again requested for exportable Excel Data - Updated at 17 Oct 2k18 9.45am
    */
     
    public function getTopTransactionsProducts()
    {
        $data = DB::select(DB::raw('SELECT LTI.name, LTI.sku, SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id
                                WHERE LT.status IN (0)
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 50'));
        
        // return Response::json(['data' => array_merge($data)]);
        
        // Export as Excell
        Excel::create('Top-Pending-Products-J-'.Config::get('constants.ENVIRONMENT').'-'.date('d-m-Y'), function($excel) use($data) {
            $excel->sheet('Top Products', function($sheet) use($data) {
                $sheetArray = array();
                $sheetArray[] = array('Product Name', 'SKU', 'Total Quantity');
                $sheetArray[] = array(); // Add an empty row
                $sheetArray[] = array(); // Add an empty row
                foreach($data as $row){
                    $sheetArray[] = array($row->name, $row->sku, $row->total_qty);
                }
                $sheet->fromArray($sheetArray, null, 'A1', false, false);
            });
        })->export('xls');
    }
    
    // Display new sales dashboard starts

    public function getV3(){

        $total_products  = Product::dashboard_totalProducts();

        $total_customers     = Customer::TotalCustomer();

        $total_value    = Transaction::dashboard_total();
        
        $total_transactions = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(1)
                                        ->first();

         // Today total transactions count
        $total_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                        ->where('status', '=', 'completed')
                                        ->count();
        // yesterday total transactions count
        $total_sales_yesterday = Transaction::whereBetween('transaction_date', [
                                            \Carbon\Carbon::parse('yesterday')->startOfDay()->toDateTimeString(),
                                            \Carbon\Carbon::parse('yesterday')->endOfDay()->toDateTimeString()
                                         ])
                                        ->where('status', '=', 'completed')
                                        ->count();                                   

        // Total transactions of current week
        $total_sales_of_current_week = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->startOfWeek()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();
       
        // Total transactions of previous week
        $total_sales_of_previous_week = Transaction::whereBetween('transaction_date', [
                                                \Carbon\Carbon::now()->subWeeks(2)->startOfWeek()->toDateTimeString(),
                                                \Carbon\Carbon::now()->subWeeks(2)->endOfWeek()->toDateTimeString()
                                            ])
                                            ->where('status', '=', 'completed')
                                            ->count();
        
        // Monthly sales || last 30 days records
        $total_sales_of_month = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();

        return View::make('dashboards.index-v3', [
                        'total_products' => $total_products,
                        'total_customers' => $total_customers,
                        'total_transactions' => $total_transactions->id,
                        'total_sales_current_week' => $total_sales_of_current_week,
                        'total_sales_previous_week' => $total_sales_of_previous_week,
                        'total_sales_of_month' => $total_sales_of_month,
                        'total_sales_today' => $total_sales_today,
                        'total_sales_yesterday' => $total_sales_yesterday
                    ]);
    }
    
    // Display new sales dashboard starts

    public function getV2(){

        $total_products  = Product::dashboard_totalProducts();

        $total_customers     = Customer::TotalCustomer();

        $total_value    = Transaction::dashboard_total();
        
        // $total_transactions = Transaction::where("status","=","completed")
        //                                 ->where("invoice_no","<>","")
        //                                 ->orderBy("id","DESC")
        //                                 ->limit(1)
        //                                 ->first();

        $total_transactions = Transaction::select('id')
                                        ->count();

         // Today total transactions count
        $total_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                        ->where('status', '=', 'completed')
                                        ->count();
        // yesterday total transactions count
        $total_sales_yesterday = Transaction::whereBetween('transaction_date', [
                                            \Carbon\Carbon::parse('yesterday')->startOfDay()->toDateTimeString(),
                                            \Carbon\Carbon::parse('yesterday')->endOfDay()->toDateTimeString()
                                         ])
                                        ->where('status', '=', 'completed')
                                        ->count();                                   

        // Total transactions of current week
        $total_sales_of_current_week = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->startOfWeek()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();
       
        // Total transactions of previous week
        $total_sales_of_previous_week = Transaction::whereBetween('transaction_date', [
                                                \Carbon\Carbon::now()->subWeeks(2)->startOfWeek()->toDateTimeString(),
                                                \Carbon\Carbon::now()->subWeeks(2)->endOfWeek()->toDateTimeString()
                                            ])
                                            ->where('status', '=', 'completed')
                                            ->count();
        
        // Monthly sales || last 30 days records
        $total_sales_of_month = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();

        return View::make('dashboards.index-v2', [
                        'total_products' => $total_products,
                        'total_customers' => $total_customers,
                        // 'total_transactions' => $total_transactions->id,
                        'total_transactions' => $total_transactions,
                        'total_sales_current_week' => $total_sales_of_current_week,
                        'total_sales_previous_week' => $total_sales_of_previous_week,
                        'total_sales_of_month' => $total_sales_of_month,
                        'total_sales_today' => $total_sales_today,
                        'total_sales_yesterday' => $total_sales_yesterday
                    ]);
    }
    
    public function getV4(){

        $total_products  = Product::dashboard_totalProducts();

        $total_customers     = Customer::TotalCustomer();

        $total_value    = Transaction::dashboard_total();
        
        $total_transactions = Transaction::where("status","=","completed")
                                        ->where("invoice_no","<>","")
                                        ->orderBy("id","DESC")
                                        ->limit(1)
                                        ->first();

         // Today total transactions count
        $total_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                        ->where('status', '=', 'completed')
                                        ->count();
        // yesterday total transactions count
        $total_sales_yesterday = Transaction::whereBetween('transaction_date', [
                                            \Carbon\Carbon::parse('yesterday')->startOfDay()->toDateTimeString(),
                                            \Carbon\Carbon::parse('yesterday')->endOfDay()->toDateTimeString()
                                         ])
                                        ->where('status', '=', 'completed')
                                        ->count();                                   

        // Total transactions of current week
        $total_sales_of_current_week = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->startOfWeek()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();
       
        // Total transactions of previous week
        $total_sales_of_previous_week = Transaction::whereBetween('transaction_date', [
                                                \Carbon\Carbon::now()->subWeeks(2)->startOfWeek()->toDateTimeString(),
                                                \Carbon\Carbon::now()->subWeeks(2)->endOfWeek()->toDateTimeString()
                                            ])
                                            ->where('status', '=', 'completed')
                                            ->count();
        
        // Monthly sales || last 30 days records
        $total_sales_of_month = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();

        return View::make('dashboards.index-v4', [
                        'total_products' => $total_products,
                        'total_customers' => $total_customers,
                        'total_transactions' => $total_transactions->id,
                        'total_sales_current_week' => $total_sales_of_current_week,
                        'total_sales_previous_week' => $total_sales_of_previous_week,
                        'total_sales_of_month' => $total_sales_of_month,
                        'total_sales_today' => $total_sales_today,
                        'total_sales_yesterday' => $total_sales_yesterday
                    ]);
    }
    
    public function getV5(){

        $total_products  = Product::dashboard_totalProducts();

        $total_customers     = Customer::TotalCustomer();

        $total_value    = Transaction::dashboard_total();
        
        $total_transactions = Transaction::where("status","=","completed")->count();

        $total_transactions_processed_today = 
            SortTransaction::where('created_at', '>=', \Carbon\Carbon::today()->toDateTimeString())
                            ->where(function($query) {
                                $query->where('generated', '=', 1);
                                $query->orWhere('regenarate', '=', 1);
                            })
                            ->count();

         // Today total transactions count
        $total_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                        ->where('status', '=', 'completed')
                                        ->count();
        // yesterday total transactions count
        $total_sales_yesterday = Transaction::whereBetween('transaction_date', [
                                            \Carbon\Carbon::parse('yesterday')->startOfDay()->toDateTimeString(),
                                            \Carbon\Carbon::parse('yesterday')->endOfDay()->toDateTimeString()
                                         ])
                                        ->where('status', '=', 'completed')
                                        ->count();                                   

        // Total transactions of current week
        $total_sales_of_current_week = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->startOfWeek()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();
       
        // Total transactions of previous week
        $total_sales_of_previous_week = Transaction::whereBetween('transaction_date', [
                                                \Carbon\Carbon::now()->subWeeks(2)->startOfWeek()->toDateTimeString(),
                                                \Carbon\Carbon::now()->subWeeks(2)->endOfWeek()->toDateTimeString()
                                            ])
                                            ->where('status', '=', 'completed')
                                            ->count();
        
        // Monthly sales || last 30 days records
        $total_sales_of_month = Transaction::where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->count();
                                            
        $total_lazada_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                               ->where('buyer_username', '=', 'lazada')
                                               ->where('status', '=', 'completed')
                                               ->count();

        $total_shopee_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                               ->where('buyer_username', '=', 'shopee')
                                               ->where('status', '=', 'completed')
                                               ->count();


        $total_pgmall_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                               ->where('buyer_username', '=', 'pgmall')
                                               ->where('status', '=', 'completed')
                                               ->count();
                                               
        $total_lamboplace_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
                                               ->where('buyer_username', '=', 'lamboplace')
                                               ->where('status', '=', 'completed')
                                               ->count();
                                               
        // $total_astrogo_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
        //                                       ->where('buyer_username', '=', 'Astro Go Shop')
        //                                       ->where('status', '=', 'completed')
        //                                       ->count();

        // $total_prestomall_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
        //                                       ->where('buyer_username', '=', 'prestomall')
        //                                       ->where('status', '=', 'completed')
        //                                       ->count();
                                               
        // $total_qoo10_sales_today = Transaction::where('transaction_date', '>=', \Carbon\Carbon::today()->toDateTimeString())
        //                                       ->where('buyer_username', '=', 'Qoo10')
        //                                       ->where('status', '=', 'completed')
        //                                       ->count();

        return View::make('dashboards.index-v5', [
                        'total_products' => $total_products,
                        'total_customers' => $total_customers,
                        'total_transactions' => $total_transactions,
                        'total_sales_current_week' => $total_sales_of_current_week,
                        'total_sales_previous_week' => $total_sales_of_previous_week,
                        'total_sales_of_month' => $total_sales_of_month,
                        'total_sales_today' => $total_sales_today,
                        'total_sales_yesterday' => $total_sales_yesterday,
                        'total_transactions_processed_today' => $total_transactions_processed_today,
                        'total_lazada_sales_today' => $total_lazada_sales_today,
                        'total_shopee_sales_today' => $total_shopee_sales_today,
                        'total_pgmall_sales_today' => $total_pgmall_sales_today,
                        'total_lamboplace_sales_today' => $total_lamboplace_sales_today
                        // 'total_astrogo_sales_today' => $total_astrogo_sales_today,
                        // 'total_prestomall_sales_today' => $total_prestomall_sales_today,
                        // 'total_qoo10_sales_today' => $total_qoo10_sales_today
                    ]);
    }

    // Get Daily Transaction for Graph
    public function getDailyTransactions(){
        // Daily transactions for Graphing
        $data = Transaction::select([
                            // This aggregates the data and makes available a 'count' attribute
                            DB::raw('count(id) as `count`'), 
                            // This throws away the timestamp portion of the date
                            DB::raw('DATE(transaction_date) as day')
                            // Group these records according to that day
                            ])->groupBy('day')
                            // ->where('transaction_date', '>=', \Carbon\Carbon::parse('last sunday'))
                            // And restrict these results to only those created in the last week
                            ->where('transaction_date', '>=', \Carbon\Carbon::now()->subWeeks(1)->toDateTimeString())
                            ->where('status', '=', 'completed')
                            ->get();

        $output = [];
        foreach($data as $entry) {
        // $output[$entry->day] = $entry->count;
        $output[date(l, strtotime($entry->day))] = $entry->count;
        // $output[]=$entry->count;
        }

        return Response::json(array('date'=>array_keys($output), 'count'=>array_values($output)));
    }

    // Get Monthly Transaction for Graph
    public function getMonthlyTransactions(){
        // Monthly Transactions for graphing
        $monthly_transactions =  Transaction::select([
                                                DB::raw('WEEK(transaction_date) AS week'),
                                                DB::raw('COUNT(id) AS count'),
                                            ])
                                            ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth()->toDateTimeString())
                                            ->where('status', '=', 'completed')
                                            ->groupBy('week')
                                            ->orderBy('transaction_date', 'desc')
                                            ->get();
        return Response::json(array('monthly_transactions'=>$monthly_transactions));
    }

    // Get platform transaction data for chart
    public function getPlatformSales(){
        
        // Platform Sales

        if (Request::ajax())
        {

            // $elevenStreet = Transaction::where('buyer_username', 'like', '11street')
            //                             ->where('status', '=', 'completed')
            //                             ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
            //                             ->count();

            // $prestomall = Transaction::where('buyer_username', 'like', 'prestomall')
            //                         ->where('status', '=', 'completed')
            //                         ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
            //                         ->count();

            $lazada = Transaction::where('buyer_username', 'like', 'lazada')
                                    ->where('status', '=', 'completed')
                                    ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
                                    ->count();
            
            $shopee = Transaction::where('buyer_username', 'like', 'shopee')
                                    ->where('status', '=', 'completed')
                                    ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
                                    ->count();

            $pgmall = Transaction::where('buyer_username', 'like', 'pgmall')
                                    ->where('status', '=', 'completed')
                                    ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
                                    ->count();

            $lamboplace = Transaction::where('buyer_username', 'like', 'Lamboplace')
                                    ->where('status', '=', 'completed')
                                    ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
                                    ->count();

            // $qoo10 = Transaction::where('buyer_username', 'like', 'qoo10')
            //                         ->where('status', '=', 'completed')
            //                         ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
            //                         ->count();

            $offline = Transaction::whereNotIn('buyer_username', array('11street', 'lazada', 'shopee', 'qoo10'))
                                    ->where('status', '=', 'completed')
                                    ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
                                    ->count();
                                    
            $apps = Transaction::whereIn('device_platform', array('ios', 'android'))
                                    ->where('status', '=', 'completed')
                                    ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
                                    ->count();

            // $astrogoShop = Transaction::where('buyer_username', 'like', 'Astro Go Shop')
            //                         ->where('status', '=', 'completed')
            //                         ->where('transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
            //                         ->count();
                
            // return Response::json(array('prestomall'=>$prestomall, 'Lazada' => $lazada, 
            //                 'Shopee' => $shopee, 'Qoo10' => $qoo10, 'Offline'=>$offline, 'Apps'=>$appsm, 'astrogoShop'=>$astrogoShop));

            return Response::json(array('PGMall'=>$pgmall, 'Lazada' => $lazada, 
                            'Shopee' => $shopee, 'Lamboplace' => $lamboplace, 'Offline'=>$offline, 'Apps'=>$apps));
        }

    }

    // Get Platform data for buliding comparison graph chart
    public function getPlatformsCompareChartData(){
        // Platform Sales Comparison Chart

        if(Request::ajax())
        {
            $month = date_format(date_create(\Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString()), 'M Y');
            $elevenStreetData = Transaction::select([
                        DB::raw('WEEK(transaction_date) AS week'),
                        DB::raw('COUNT(id) AS count'),
                    ])
                    ->where('buyer_username', 'like', '11street')
                    ->whereBetween('transaction_date', [
                        \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                        \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                    ])
                    ->where('status', '=', 'completed')
                    ->groupBy('week')
                    ->orderBy('transaction_date', 'asc')
                    ->get();

            // $prestomallData = Transaction::select([
            //         DB::raw('WEEK(transaction_date) AS week'),
            //         DB::raw('COUNT(id) AS count'),
            //     ])
            //     ->where('buyer_username', 'like', 'prestomall')
            //     ->whereBetween('transaction_date', [
            //         \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
            //         \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
            //     ])
            //     ->where('status', '=', 'completed')
            //     ->groupBy('week')
            //     ->orderBy('transaction_date', 'asc')
            //     ->get();

            $lazadaData = Transaction::select([
                    DB::raw('WEEK(transaction_date) AS week'),
                    DB::raw('COUNT(id) AS count'),
                ])
                ->where('buyer_username', 'like', 'lazada')
                ->whereBetween('transaction_date', [
                    \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                    \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                ])
                ->where('status', '=', 'completed')
                ->groupBy('week')
                ->orderBy('transaction_date', 'asc')
                ->get();

            $shopeeData = Transaction::select([
                    DB::raw('WEEK(transaction_date) AS week'),
                    DB::raw('COUNT(id) AS count'),
                ])
                ->where('buyer_username', 'like', 'shopee')
                ->whereBetween('transaction_date', [
                    \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                    \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                ])
                ->where('status', '=', 'completed')
                ->groupBy('week')
                ->orderBy('transaction_date', 'asc')
                ->get();

            $pgmalllData = Transaction::select([
                    DB::raw('WEEK(transaction_date) AS week'),
                    DB::raw('COUNT(id) AS count'),
                ])
                ->where('buyer_username', 'like', 'pgmall')
                ->whereBetween('transaction_date', [
                    \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                    \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                ])
                ->where('status', '=', 'completed')
                ->groupBy('week')
                ->orderBy('transaction_date', 'asc')
                ->get();

            $lamboplaceData = Transaction::select([
                    DB::raw('WEEK(transaction_date) AS week'),
                    DB::raw('COUNT(id) AS count'),
                ])
                ->where('buyer_username', 'like', 'Lamboplace')
                ->whereBetween('transaction_date', [
                    \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                    \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                ])
                ->where('status', '=', 'completed')
                ->groupBy('week')
                ->orderBy('transaction_date', 'asc')
                ->get();
            // $qoo10Data = Transaction::select([
            //         DB::raw('WEEK(transaction_date) AS week'),
            //         DB::raw('COUNT(id) AS count'),
            //     ])
            //     ->where('buyer_username', 'like', 'qoo10')
            //     ->whereBetween('transaction_date', [
            //         \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
            //         \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
            //     ])
            //     ->where('status', '=', 'completed')
            //     ->groupBy('week')
            //     ->orderBy('transaction_date', 'asc')
            //     ->get();


            $offlineData = Transaction::select([
                        DB::raw('WEEK(transaction_date) as week'),
                        DB::raw('COUNT(id) as count')
                    ])
                    ->where('status', '=', 'completed')
                    ->whereBetween('transaction_date', [
                        \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                        \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                    ])
                    ->whereNotIn('buyer_username',
                            ['11Street','11street', 'Lazada', 'lazada', 'Qoo10', 'qoo10', 'Shopee', 'shopee', 'Astro Go Shop'])
                    ->whereNotIn('device_platform', ['ios','android'])
                    ->groupBy('week')
                    ->orderBy('transaction_date', 'asc')
                    ->get();

            $appData = Transaction::select([
                        DB::raw('WEEK(transaction_date) as week'),
                        DB::raw('COUNT(id) as count')
                    ])
                    ->where('status', '=', 'completed')
                    ->whereBetween('transaction_date', [
                        \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                        \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                    ])
                    ->whereIn('device_platform', ['ios','android'])
                    ->groupBy('week')
                    ->orderBy('transaction_date', 'asc')
                    ->get();

            // $astrogoShopData = Transaction::select([
            //         DB::raw('WEEK(transaction_date) AS week'),
            //         DB::raw('COUNT(id) AS count'),
            //     ])
            //     ->where('buyer_username', 'like', 'Astro Go Shop')
            //     ->whereBetween('transaction_date', [
            //         \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
            //         \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
            //     ])
            //     ->where('status', '=', 'completed')
            //     ->groupBy('week')
            //     ->orderBy('transaction_date', 'asc')
            //     ->get();

            // return Response::json(array('month'=>$month,'prestomall'=>$prestomallData, 'lazada'=>$lazadaData,'shopee'=>$shopeeData, 
            //                                         'qoo10'=>$qoo10Data, 'offline'=>$offlineData, 'app'=>$appData, 'astrogoShop'=>$astrogoShopData));

            return Response::json(array('month'=>$month,'pgmall'=>$pgmalllData, 'lazada'=>$lazadaData,'shopee'=>$shopeeData, 
                                                    'lamboplace'=>$lamboplaceData, 'offline'=>$offlineData, 'app'=>$appData));
        }
    }

    // Get platforms completed transactions 
    // percent range for last 2 weeks
    public function getPlatformSalesPercent(){
        
        // Percent Calculations for platforms 
        // based on current week against previous weeks
        // Note : variable name conventions are like monthly
        
        try {

        $currentWeek_start = \Carbon\Carbon::now()->subWeeks(1)->toDateTimeString();
        $lastWeek_start =  \Carbon\Carbon::now()->subWeeks(2)->toDateTimeString();
        Log::info($currentWeek_start);
        Log::info($lastWeek_start);

        // $curMonth_11street = Transaction::where('transaction_date', '>=', $currentWeek_start)
        //                                 ->where('buyer_username', 'like', '11street')
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();
        
        // $preMonth_11street = Transaction::where('buyer_username', 'like', '%11street%')
        //                                 ->whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();

        // $curMonth_prestomall = Transaction::where('transaction_date', '>=', $currentWeek_start)
        //                                 ->where('buyer_username', 'like', 'prestomall')
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();
        
        // $preMonth_prestomall = Transaction::where('buyer_username', 'like', 'prestomall')
        //                                 ->whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();

        $curMonth_lazada = Transaction::where('transaction_date', '>=', $currentWeek_start)
                                        ->where('buyer_username', 'like', 'lazada')
                                        ->where('status', '=', 'completed')
                                        ->count();

        
        $preMonth_lazada = Transaction::whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
                                        ->where('buyer_username', 'like', 'lazada')
                                        ->where('status', '=', 'completed')
                                        ->count();

        
        $curMonth_shopee = Transaction::where('transaction_date', '>=', $currentWeek_start)
                                        ->where('buyer_username', 'like', 'shopee')
                                        ->where('status', '=', 'completed')
                                        ->count();

        
        $preMonth_shopee = Transaction::whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
                                        ->where('buyer_username', 'like', 'shopee')
                                        ->where('status', '=', 'completed')
                                        ->count();

        $curMonth_pgmall = Transaction::where('transaction_date', '>=', $currentWeek_start)
                                        ->where('buyer_username', 'like', 'pgmall')
                                        ->where('status', '=', 'completed')
                                        ->count();

        
        $preMonth_pgmall = Transaction::whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
                                        ->where('buyer_username', 'like', 'pgmall')
                                        ->where('status', '=', 'completed')
                                        ->count();

        $curMonth_lamboplace = Transaction::where('transaction_date', '>=', $currentWeek_start)
                                        ->where('buyer_username', 'like', 'Lamboplace')
                                        ->where('status', '=', 'completed')
                                        ->count();


        $preMonth_lamboplace = Transaction::whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
                                        ->where('buyer_username', 'like', 'Lamboplace')
                                        ->where('status', '=', 'completed')
                                        ->count();
        // $curMonth_qoo10 = Transaction::where('transaction_date', '>=', $currentWeek_start)
        //                                 ->where('buyer_username', 'like', 'qoo10')
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();

        
        // $preMonth_qoo10 = Transaction::whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
        //                                 ->where('buyer_username', 'like', 'qoo10')
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();
                                        
        $curMonth_offline = Transaction::where('transaction_date', '>=', $currentWeek_start)
                                        ->whereNotIn('buyer_username', ['11Street','11street', 'prestomall', 'Lazada', 'lazada', 'Qoo10', 'qoo10', 'Shopee', 'shopee', 'Astro Go Shop'])
                                        ->where('status', '=', 'completed')
                                        ->count();

        $preMonth_offline = Transaction::whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
                                        ->whereNotIn('buyer_username', ['11Street','11street', 'prestomall', 'Lazada', 'lazada', 'Qoo10', 'qoo10', 'Shopee', 'shopee', 'Astro Go Shop'])
                                        ->where('status', '=', 'completed')
                                        ->count();

        $curMonth_app = Transaction::where('transaction_date', '>=', $currentWeek_start)
                                        ->whereIn('device_platform', ['ios','android'])
                                        ->where('status', '=', 'completed')
                                        ->count();

        $preMonth_app = Transaction::whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
                                        ->whereIn('device_platform', ['ios','android'])
                                        ->where('status', '=', 'completed')
                                        ->count();

        // $curMonth_astrogoShop = Transaction::where('transaction_date', '>=', $currentWeek_start)
        //                                 ->where('buyer_username', 'like', 'Astro Go Shop')
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();
        
        // $preMonth_astrogoShop = Transaction::where('buyer_username', 'like', 'Astro Go Shop')
        //                                 ->whereBetween('transaction_date', array($lastWeek_start, $currentWeek_start))
        //                                 ->where('status', '=', 'completed')
        //                                 ->count();

        // $Eleven_street_comparison_percent = $this->percent_calc($preMonth_11street, $curMonth_11street);
        // $prestomall_comparison_percent = $this->percent_calc($preMonth_prestomall, $curMonth_prestomall);
        $Lazada_comparison_percent = $this->percent_calc($preMonth_lazada, $curMonth_lazada);
        $shopee_comparison_percent = $this->percent_calc($preMonth_shopee, $curMonth_shopee);
        $pgmall_comparison_percent = $this->percent_calc($preMonth_pgmall, $curMonth_pgmall);
        $lamboplace_comparison_percent = $this->percent_calc($preMonth_lamboplace, $curMonth_lamboplace);
        // $qoo10_comparison_percent = $this->percent_calc($preMonth_qoo10, $curMonth_qoo10);
        $offline_comparison_percent = $this->percent_calc($preMonth_offline, $curMonth_offline);
        $app_comparison_percent = $this->percent_calc($preMonth_app, $curMonth_app);
        // $astrogoshop_comparison_percent = $this->percent_calc($preMonth_astrogoshop, $curMonth_astrogoshop);
        
        

        // return Response::json(array('prestomall'=>$prestomall_comparison_percent, 'lazada'=>$Lazada_comparison_percent, 
        //                             'shopee'=>$shopee_comparison_percent, 'qoo10'=>$qoo10_comparison_percent, 'offline'=>$offline_comparison_percent, 'app'=>$app_comparison_percent, 'astrogoShop'=>$astrogoshop_comparison_percent));

        return Response::json(array('pgmall'=>$pgmall_comparison_percent, 'lazada'=>$Lazada_comparison_percent, 
                                    'shopee'=>$shopee_comparison_percent, 'lamboplace'=>$lamboplace_comparison_percent, 'offline'=>$offline_comparison_percent, 'app'=>$app_comparison_percent));

        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
           
           
        } 
            
    }

    // Top 10 Undelivered Items
    public function getTopUndeliveredItems(){
        $top_10_undelivered = DB::table('logistic_transaction as trans')
                                ->join('logistic_transaction_item as item', 'trans.id', '=', 'item.logistic_id')
                                ->select(DB::raw('count(*) as item_count'), 'item.name')
                                ->where('trans.transaction_date', '>=', \Carbon\Carbon::now()->startOfMonth()->toDateTimeString())
                                ->where('trans.status', '=', 1)
                                ->groupBy('item.product_id')
                                ->orderBy('item_count', 'desc')
                                ->take(10)
                                ->get();

        return Response::json(array('topUndelivered'=>$top_10_undelivered));
    }

    // Top 10 Undelivered 0 stock items
    public function getTopUndeliveredZeroStockItems(){
        $top_10_undelivered = DB::table('logistic_transaction as trans')
                                ->join('logistic_transaction_item as item', 'trans.id', '=', 'item.logistic_id')
                                ->join('jocom_warehouse_products as prod', 'item.product_id', '=', 'prod.product_id')
                                ->select(DB::raw('count(*) as item_count'), 'item.name')
                                ->where('trans.transaction_date', '>=', \Carbon\Carbon::now()->startOfMonth()->toDateTimeString())
                                ->where('trans.status', '=', 1)
                                ->where('stockin_hand', '=', 0)
                                ->groupBy('item.product_id')
                                ->orderBy('item_count', 'desc')
                                ->take(10)
                                ->get();

        return Response::json(array('topUndelivered'=>$top_10_undelivered));
    }

    // Top 5 Products & Categories
    public function getTopProductsCategories(){
        // Top 5 products and categories
        
        $start_week = \Carbon\Carbon::now()->subWeek()->addDay()->toDateTimeString();
        $end_week = \Carbon\Carbon::now()->toDateTimeString();

        // $top_5_products = DB::table('jocom_transaction_details')
        //                                 ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
        //                                 ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
        //                                 ->select(DB::raw('count(*) as product_count'), 'jocom_products.name', 'jocom_products.id')
        //                                 ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
        //                                 ->groupBy('jocom_transaction_details.product_id')
        //                                 ->orderBy('product_count', 'desc')
        //                                 ->take(5)
        //                                 ->get();

        // $top_5_products_categories = DB::table('jocom_transaction_details')
        //                                 ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
        //                                 ->join('jocom_products_category', DB::raw('FIND_IN_SET(jocom_products_category.id, jocom_products.category)'), '>', DB::raw('0'))
        //                                 ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
        //                                 ->select(DB::raw('count(*) as product_count'), 'jocom_products.name', 'jocom_products_category.category_name')
        //                                 ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
        //                                 ->groupBy('jocom_transaction_details.product_id')
        //                                 ->orderBy('product_count', 'desc')
        //                                 ->take(5)
        //                                 ->get();

        // $top_10_products = DB::table('jocom_transaction_details')
        //                                 ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
        //                                 ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
        //                                 ->select(DB::raw('count(*) as product_count'), 'jocom_products.name', 'jocom_products.id')
        //                                 ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
        //                                 ->groupBy('jocom_transaction_details.product_id')
        //                                 ->orderBy('product_count', 'desc')
        //                                 ->take(10)
        //                                 ->get();

        $top_10_products_categories = DB::table('jocom_transaction_details')
                                        ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
                                        ->join('jocom_products_category', DB::raw('FIND_IN_SET(jocom_products_category.id, jocom_products.category)'), '>', DB::raw('0'))
                                        ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
                                        ->select(DB::raw('count(*) as product_count'), 'jocom_products.name', 'jocom_products_category.category_name')
                                        ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
                                        ->groupBy('jocom_transaction_details.product_id')
                                        ->orderBy('product_count', 'desc')
                                        ->take(10)
                                        ->get();
                                        
        $week = date_format(date_create($start_week), 'd/m/Y') . '-' . date_format(date_create($end_week), 'd/m/Y');

        // return Response::json(array('week'=>$week,'topProducts'=>$top_5_products,'topProductsCategories'=>$top_5_products_categories));
        // return Response::json(array('week'=>$week,'topProducts'=>$top_10_products,'topProductsCategories'=>$top_10_products_categories));
        return Response::json(array('week'=>$week,'topProductsCategories'=>$top_10_products_categories));
    }
    
    public function getTopproducts() {
        $start_week = \Carbon\Carbon::now()->subWeek()->addDay()->toDateTimeString();
        $end_week = \Carbon\Carbon::now()->toDateTimeString();

        $top_5_products = DB::table('jocom_transaction_details')
                                        ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
                                        ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
                                        ->select(DB::raw('count(*) as product_count'), 'jocom_products.name', 'jocom_products.id', 'jocom_products.img_1', 'jocom_products.qrcode')
                                        ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
                                        ->groupBy('jocom_transaction_details.product_id')
                                        ->orderBy('product_count', 'desc')
                                        ->take(5)
                                        ->get();

        $week = date_format(date_create($start_week), 'd/m/Y') . '-' . date_format(date_create($end_week), 'd/m/Y');

        return Response::json(array('week'=>$week,'topProducts'=>$top_5_products));
    }

    public function getTopcategories() {
        $start_week = \Carbon\Carbon::now()->subWeek()->addDay()->toDateTimeString();
        $end_week = \Carbon\Carbon::now()->toDateTimeString();
        
        $top_5_products_categories = DB::table('jocom_transaction_details')
                                        ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
                                        ->join('jocom_products_category', DB::raw('FIND_IN_SET(jocom_products_category.id, jocom_products.category)'), '>', DB::raw('0'))
                                        ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
                                        ->select(DB::raw('count(*) as product_count'), 'jocom_products.name', 'jocom_products_category.category_name')
                                        ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
                                        ->groupBy('jocom_transaction_details.product_id')
                                        ->orderBy('product_count', 'desc')
                                        ->take(5)
                                        ->get();

        return Response::json(array('topProductsCategories'=>$top_5_products_categories));
    }

    // Top 5 Products for last week
    public function getTopProductsLastWeek(){
        // Top 5 products

        $start_week = \Carbon\Carbon::now()->subWeeks(2)->addDay()->toDateTimeString();
        $end_week = \Carbon\Carbon::now()->subWeek()->toDateTimeString();

        // $top_5_products = DB::table('jocom_transaction_details')
        //                                 ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
        //                                 ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
        //                                 ->select(DB::raw('count(*) as product_count'), 'jocom_transaction_details.product_name', 'jocom_transaction_details.product_id', 'jocom_products.img_1', 'jocom_products.qrcode')
        //                                 ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
        //                                 ->groupBy('jocom_transaction_details.product_id')
        //                                 ->orderBy('product_count', 'desc')
        //                                 ->take(5)
        //                                 ->get();

        $top_10_products = DB::table('jocom_transaction_details')
                                        ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
                                        ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
                                        ->select(DB::raw('count(*) as product_count'), 'jocom_transaction_details.product_name', 'jocom_transaction_details.product_id', 'jocom_products.img_1', 'jocom_products.qrcode')
                                        ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
                                        ->groupBy('jocom_transaction_details.product_id')
                                        ->orderBy('product_count', 'desc')
                                        ->take(10)
                                        ->get();

        $week = date_format(date_create($start_week), 'd/m/Y') . '-' . date_format(date_create($end_week), 'd/m/Y');
        // return Response::json(array('week'=>$week,'topProducts'=>$top_5_products));
        return Response::json(array('week'=>$week,'topProducts'=>$top_10_products));
    }

    // Top 10 Products for this week
    public function getTopProductsThisWeek(){
        // Top 10 products

        $start_week = \Carbon\Carbon::now()->subWeek()->addDay()->toDateTimeString();
        $end_week = \Carbon\Carbon::now()->toDateTimeString();

        $top_10_products = DB::table('jocom_transaction_details')
                                        ->join('jocom_products', 'jocom_transaction_details.product_id', '=', 'jocom_products.id')
                                        ->join('jocom_transaction', 'jocom_transaction_details.transaction_id', '=', 'jocom_transaction.id')
                                        ->select(DB::raw('count(*) as product_count'), 'jocom_products.name', 'jocom_products.id')
                                        ->whereBetween('jocom_transaction.transaction_date', [$start_week, $end_week])
                                        ->groupBy('jocom_transaction_details.product_id')
                                        ->orderBy('product_count', 'desc')
                                        ->take(10)
                                        ->get();

        $week = date_format(date_create($start_week), 'd/m/Y') . '-' . date_format(date_create($end_week), 'd/m/Y');
        // return Response::json(array('week'=>$week,'topProducts'=>$top_5_products));
        return Response::json(array('week'=>$week,'topProducts'=>$top_10_products));
    }

    // Get Mobile Platforms Data
    public function getMobilePlatformValues(){
        // // App Platform Transactions
        // $android = Transaction::whereBetween('transaction_date', [
        //     \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
        //     \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
        //     ])
        //     ->where('device_platform', 'like', 'android')
        //     ->where('status', '=', 'completed')
        //     ->count();
                
        // $ios = Transaction::whereBetween('transaction_date', [
        //                 \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
        //                 \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
        //             ])
        //             ->where('device_platform', 'like', 'ios')
        //             ->where('status', '=', 'completed')
        //             ->count();

        $web = Transaction::whereBetween('transaction_date', [
                        \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                        \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                    ])
                    ->where('device_platform', 'web')
                    ->where('status', '=', 'completed')
                    ->count();

        $app = Transaction::whereBetween('transaction_date', [
                        \Carbon\Carbon::now()->startOfMonth()->subMonth(1)->toDateTimeString(), 
                        \Carbon\Carbon::now()->startOfMonth()->toDateTimeString()
                    ])
                    ->whereIn('device_platform', ['ios','android'])
                    ->where('status', '=', 'completed')
                    ->count();

        // $app_transactions = ['android'=>$android, 'ios'=>$ios];
        $device_transactions = ['web'=>$web, 'app'=>$app];

        // return Response::json(array('appTransactions'=>$app_transactions));
        return Response::json(array('deviceTransactions'=>$device_transactions));

    }

    // Get Top regions Data
    public function getTopRegions(){
        // Region Check
        // $transaction_regions = DB::table('jocom_transaction')
        //                         ->join('jocom_country_states', 'jocom_transaction.delivery_state_id', '=', 'jocom_country_states.id')
        //                         ->join('jocom_region', 'jocom_country_states.region_id', '=', 'jocom_region.id')
        //                         ->select('jocom_transaction.delivery_state_id', 
        //                                     DB::raw('count(*) as Count'), 'jocom_country_states.name', 'jocom_country_states.iso_code', 'jocom_region.region')
        //                         ->where('jocom_transaction.transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
        //                         ->groupBy('jocom_transaction.delivery_state_id')
        //                         ->orderBy('Count', 'desc')
        //                         ->take(5)
        //                         ->get();

        $transaction_regions = DB::table('jocom_transaction')
                                ->join('jocom_country_states', 'jocom_transaction.delivery_state_id', '=', 'jocom_country_states.id')
                                ->join('jocom_region', 'jocom_country_states.region_id', '=', 'jocom_region.id')
                                ->select('jocom_transaction.delivery_state_id', 
                                            DB::raw('count(*) as Count'), 'jocom_country_states.name', 'jocom_country_states.iso_code', 'jocom_region.region')
                                ->where('jocom_transaction.transaction_date', '>=', \Carbon\Carbon::now()->subMonth(1)->toDateTimeString())
                                ->groupBy('jocom_transaction.delivery_state_id')
                                ->orderBy('Count', 'desc')
                                ->take(10)
                                ->get();
                                
        return Response::json(array('regions'=>$transaction_regions));
    }

    // Percent Calculation bw 2 values
    private function percent_calc($oldValue, $newValue) {


        if ($oldValue !== null && $oldValue !== 0) {

            $percentChange = number_format(($newValue - $oldValue) / $oldValue * 100, 2);


        } else if ($oldValue !== null && $oldValue !== 0) {
            
            $percentChange = number_format(($newValue - $oldValue) / $oldValue * 100, 2);

        }
        else {
            
            $percentChange = 0;

        }
        
        return $percentChange;
    }
    
    // Get list of product stock 
    public function getStocks() {

        $product_ids = [2948, 13005, 2885, 8296, 9780];

        $stock_list = DB::table('jocom_warehouse_products as warehouse')
                        ->join('jocom_products as products', 'warehouse.product_id', '=', 'products.id')
                        ->join('jocom_product_price as price', 'products.id', '=', 'price.product_id')
                        ->whereIn('warehouse.product_id', $product_ids)
                        ->select('qrcode', 'label', 'stockin_hand', 'products.img_1', 'products.name')
                        ->get();

        return Response::json(array('stocks'=>$stock_list));
    }
    
    public function getYearStatusCompare($year){

       
        
        $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.status','cancelled')->count();
            
        $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.status',  'refund')->count();
        
        
       
        $totalAllOrder = $totalOrderCompleted + $totalOrderRefund  + $totalOrderCancelled;


        return array(
            "completed" =>  array(
                "total" => $totalOrderCompleted,
                "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderCompleted / ($totalAllOrder)) * 100, 2, ".", "") : 0 
            ),
            "refund" =>  array(
                "total" => $totalOrderRefund,
                "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderRefund / ($totalAllOrder)) * 100, 2, ".", "") : 0 
            ),
            "cancelled" =>  array(
                "total" => $totalOrderCancelled,
                "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderCancelled / ($totalAllOrder)) * 100, 2, ".", "") : 0 
            ),
        );

    }

    public function getMonthStatusCompare($year, $month){

     
        $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")->where('JT.status',  'refund')->count();
            
        $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")->where('JT.status',  'cancelled')->count();
            
        $totalAllOrder = $totalOrderCompleted + $totalOrderRefund  + $totalOrderCancelled;


        return array(
            "completed" =>  array(
                "total" => $totalOrderCompleted,
                "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderCompleted / ($totalAllOrder)) * 100, 2, ".", "") : 0 
            ),
            "refund" =>  array(
                "total" => $totalOrderRefund,
                "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderRefund / ($totalAllOrder)) * 100, 2, ".", "") : 0 
            ),
            "cancelled" =>  array(
                "total" => $totalOrderCancelled,
                "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderCancelled / ($totalAllOrder)) * 100, 2, ".", "") : 0 
            ),
        );

    }


    public function getDateStatusCompare($date){

        try{

          

            $totalOrderCompleted = DB::table('jocom_transaction AS JT')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'completed')->count();
                
            $totalOrderRefund = DB::table('jocom_transaction AS JT')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'refund')->count();
                
            $totalOrderCancelled = DB::table('jocom_transaction AS JT')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'cancelled')->count();
            $totalAllOrder = $totalOrderCompleted + $totalOrderRefund  + $totalOrderCancelled;

            return array(
                "completed" =>  array(
                    "total" => $totalOrderCompleted,
                    "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderCompleted / ($totalAllOrder)) * 100, 2, ".", "") : 0 
                ),
                "refund" =>  array(
                    "total" => $totalOrderRefund,
                    "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderRefund / ($totalAllOrder)) * 100, 2, ".", "") : 0 
                ),
                "cancelled" =>  array(
                    "total" => $totalOrderCancelled,
                    "percentage" =>  $totalAllOrder != 0 ? number_format(($totalOrderCancelled / ($totalAllOrder)) * 100, 2, ".", "") : 0 
                ),
            );

        }catch(exception $ex){
            echo $ex->getMessage();
        }

    }
   
}
