<?php

class DashboardController extends BaseController {
    
    const PLATFORMS=['11Street'=>'jocom_elevenstreet_order',
                              'lazada'=>'jocom_lazada_order',
                              'shopee'=>'jocom_shopee_order',
                              'Qoo10'=>'jocom_qoo10_order',
                               'pgmall'=>'jocom_pgmall_order',
                               'AstroGoShop'=>'jocom_astrogoshop_order'];
    const PLATFORMSDETAILS=['11Street'=>'jocom_elevenstreet_order_details',
                              'lazada'=>'jocom_lazada_order_items',
                              'shopee'=>'jocom_shopee_order_details',
                              'Qoo10'=>'jocom_qoo10_order_details',
                               'pgmall'=>'jocom_pgmall_order_details',
                               'AstroGoShop'=>'jocom_astrogoshop_order_details'];
    const NAMESCOUNT=['11Street'=>'customer_name',
                              'lazada'=>'customer_name',
                              'shopee'=>'name',
                              'Qoo10'=>'buyer',
                               'pgmall'=>'name',
                               'AstroGoShop'=>'customer_name'];
    const SKUCOUNT=['11Street'=>'product_name',
                              'lazada'=>'sku',
                              'shopee'=>'item_sku',
                              'Qoo10'=>'sellerItemCode',
                               'pgmall'=>'item_sku',
                               'AstroGoShop'=>'jc_code'];
    const SKIPUSERS=['11street', 'lazada', 'shopee', 'qoo10','pgmall'];
    
    const STATICTOPBOARD=['joshua', 'joshua01'];
    const TOTALSALESBOARD=['agnes', 'maruthu', 'adminjohor','boobalan'];
    const BARCHARTTOTAL=['joshua', 'agnes', 'wira', 'maruthu', 'adminjohor', 'william', 'kean','quenny','ryanloh','joshua01','gerald','boobalan'];
    const PERCENTACHECIVED=['agnes', 'maruthu', 'wira', 'owen', 'annix','tammy','ira','boobalan'];
    const DAYMONTHYEAR=['joshua', 'agnes', 'maruthu','joshua01', 'william', 'kean','quenny','gerald','ryanloh','boobalan'];
    const LOGISTICSBAR=['joshua', 'agnes', 'maruthu', 'william', 'kean','quenny','joshua01','gerald','ryanloh','boobalan'];
    const LOGISTICSSPCL=['ira','asif','wendy','nuratiqah','toby','maruthu','grace','jamilah','kaijie','boobalan'];
    const PLATFORMUSERS=['agnes', 'maruthu', 'adminjohor','boobalan'];

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

        return View::make('dashboard.index2', [   
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
        
        return View::make('dashboard.index', [   
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
            
        $currentAmount = (double)$CurrentSalesAmount->total_sales  + (double)$CurrentSalesAmountDeliveryChargesDateSales->DeliveryAmount;
        
        $currentAmount2019 = (double)$CurrentSalesAmount2019->total_sales  + (double)$CurrentSalesAmountDeliveryChargesDateSales2019->DeliveryAmount;
                
        $percentageAchieved = ROUND(($currentAmount / $NextTargetAmount->amount) * 100,2);  
        
        $percentageAchieved2019 = ROUND(($currentAmount2019 / 10000000.00) * 100,2);   
        $platformslist=DB::table('jocom_plaforms_details')->select('id','platform_name','platform_username')->where('status','=',1)->get();
        $topstatic_privilage=(in_array(Session::get('username'),self::STATICTOPBOARD)) ? '1':'0';
        $totalsale_privilage=(in_array(Session::get('username'),self::TOTALSALESBOARD)) ? '1':'0';
        $barchart_privilage=(in_array(Session::get('username'),self::BARCHARTTOTAL)) ? '1':'0';
        $percentage_previlage=(in_array(Session::get('username'),self::PERCENTACHECIVED)) ? '1':'0';
        $dmy_privilage=(in_array(Session::get('username'),self::DAYMONTHYEAR)) ? '1':'0';
        $logistics_previlage=(in_array(Session::get('username'),self::LOGISTICSBAR)) ? '1':'0';
        $logistics_spcl=(in_array(Session::get('username'),self::LOGISTICSSPCL)) ? '1':'0';
        $platform_previlage=(in_array(Session::get('username'),self::PLATFORMUSERS)) ? '1':'0';
        
        return View::make('dashboard.index', [   
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
                                'platformslist'=>$platformslist,
                                'topstatic_privilage'=>$topstatic_privilage,
                                'totalsale_privilage'=>$totalsale_privilage,
                                'barchart_privilage'=>$barchart_privilage,
                                'percentage_previlage'=>$percentage_previlage,
                                'dmy_privilage'=>$dmy_privilage,
                                'logistics_previlage'=>$logistics_previlage,
                                'logistics_spcl'=>$logistics_spcl,
                                'platform_previlage'=>$platform_previlage,
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
        return View::make('dashboard.profile')->with(array('user' => $user));
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
                return Redirect::to('dashboard/profile/'.$id);
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
           
            $navigate = Input::get('navigation');
            $period_type = Input::get('rangeType');
            $start_date = Input::get('startDate');
            $to_date = Input::get('toDate');
            $cumumulative_type = Input::get('cumumulative_type');
            $platform_id=Input::get('platform_id');
            $platform=Input::get('platform');
            $store=Input::get('store_id');
            $dynamic_platform=Input::get('platform');
           if($dynamic_platform=='Astro Go Shop'){
               $dynamic_platform='AstroGoShop';  
             }
           $dynamic_table=self::PLATFORMS[$dynamic_platform];
            
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
                        $sumInformation = self::sumvalue($startDate ,$endDate,$UserRegionIdList,$platform_id,$platform,$store,$dynamic_table);
                        $sumInformation2 = self::sumvaluetwo($startDate ,$endDate,$UserRegionIdList,$platform_id,$platform,$store,$dynamic_table);
                        
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
                        $sumInformation = self::sumvalue($startDate ,$endDate,$UserRegionIdList,$platform_id,$platform,$store,$dynamic_table);
                        $sumInformation2 = self::sumvaluetwo($startDate ,$endDate,$UserRegionIdList,$platform_id,$platform,$store,$dynamic_table);
                       
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
                        $sumInformation = self::sumvalue($vWeek[0]. "00:00:00",$vWeek[count($vWeek)-1]. "23:59:59",$UserRegionIdList,$platform_id,$platform,$store,$dynamic_table);
                        $sumInformation2 = self::sumvaluetwo($vWeek[0]. "00:00:00",$vWeek[count($vWeek)-1]. "23:59:59",$UserRegionIdList,$platform_id,$platform,$store,$dynamic_table);
                        
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
            $platform_id=Input::get('platform_id');
            $platform=Input::get('platform');
            $store=Input::get('store_id');
            $dynamic_platform=Input::get('platform');
           if($dynamic_platform=='Astro Go Shop'){
            $dynamic_platform='AstroGoShop';  
             }
           $dynamic_table=self::PLATFORMS[$dynamic_platform];
           $skipusers=self::SKIPUSERS;
            switch ($type) {
                case 'DAYCOM':
                    if($platform_id==1){
                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-d",strtotime("-1 day", $date_user_entered));
                    $nextDate = date("Y-m-d",strtotime("+1 day", $date_user_entered));
                   if($store=='0'){
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->first(); 
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->first();   
                        
                    $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->first(); 
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->first();   
                   }else{
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
                   }
                    $previousAmount = (double)$PreviousDateSales->total_sales + (double)$PreviousDeliveryChargesDateSales->DeliveryAmount;
                    $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
                    
                    $previousDateDisplay = date("d, F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("d, F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        $labelData[0][] = date("d F",strtotime("-".$i." day ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." day ", $date_user_entered));
                        if($store=='0'){
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->first(); 
                        }else{
                          $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first();   
                        }
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                    
                    for($i=5; $i>=0; $i--){
                        
                        $labelData[1][]  = date("d F",strtotime("-".$i." day", strtotime($date_one_year_ago)));
                        $naviDate= date("Y-m-d",strtotime("-".$i." day",strtotime($date_one_year_ago)));
                        if($store=='0'){
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first(); 
                        }else{
                          $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first();   
                        }
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                                   
                    $StatusCompare = $this->getDateStatusCompare($currentDate,$store,$platform,$platform_id,$dynamic_table,$skipusers);
                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
                    }else{
                            if($store!='all'){
                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-d",strtotime("-1 day", $date_user_entered));
                    $nextDate = date("Y-m-d",strtotime("+1 day", $date_user_entered));
                   
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                        ->first(); 
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                        ->first();   
                        
                    $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->first(); 
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->first();   
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales + (double)$PreviousDeliveryChargesDateSales->DeliveryAmount;
                    $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
                    
                    $previousDateDisplay = date("d, F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("d, F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        $labelData[0][] = date("d F",strtotime("-".$i." day ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." day ", $date_user_entered));
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first(); 
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                    
                    for($i=5; $i>=0; $i--){
                        
                        $labelData[1][]  = date("d F",strtotime("-".$i." day", strtotime($date_one_year_ago)));
                        $naviDate= date("Y-m-d",strtotime("-".$i." day",strtotime($date_one_year_ago)));
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first(); 
                        
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                  
                    $StatusCompare = $this->getDateStatusCompare($currentDate,$store,$platform,$platform_id,$dynamic_table,$skipusers);
   
                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
                            }else{
                   
                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-d",strtotime("-1 day", $date_user_entered));
                    $nextDate = date("Y-m-d",strtotime("+1 day", $date_user_entered));
                    
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.buyer_username','=',$platform)
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                       ->first(); 
                   
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->where('JT.buyer_username','=',$platform)
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$currentDate 00:00:00")
                        ->where("JT.transaction_date","<=","$currentDate 23:59:59")
                        ->first();   
                        
                    $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.buyer_username','=',$platform)
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->first(); 
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$date_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$date_one_year_ago 23:59:59")
                        ->first();   
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales + (double)$PreviousDeliveryChargesDateSales->DeliveryAmount;
                    $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
                    
                    $previousDateDisplay = date("d, F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("d, F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        $labelData[0][] = date("d F",strtotime("-".$i." day ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." day ", $date_user_entered));
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first(); 
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                    
                    for($i=5; $i>=0; $i--){
                        
                        $labelData[1][]  = date("d F",strtotime("-".$i." day", strtotime($date_one_year_ago)));
                        $naviDate= date("Y-m-d",strtotime("-".$i." day",strtotime($date_one_year_ago)));
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$naviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$naviDate 23:59:59")
                        ->first(); 
                        
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? $PreviousDateSales->total_sales : 0;

                    }
                    
                    $StatusCompare = $this->getDateStatusCompare($currentDate,$store,$platform,$platform_id,$dynamic_table,$skipusers);
                    
                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
                            
                            }
                        
                    }

                    break;
                    
                case 'MONTHCOM':
                    
                   if($platform_id==1){
                     
                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-01",strtotime("-1 month", $date_user_entered));
                    $nextDate = date("Y-m-01",strtotime("+1 month", $date_user_entered));
                    
                    $firstDateIntheMonthCurrentYear = date("Y-m-01",$date_user_entered);
                    $lastDateIntheMonthCurrentYear = date("Y-m-t",$date_user_entered);

                    $firstDateIntheMonthLastYear = date("Y-m-01",strtotime("-1 year", $date_user_entered));
                    $lastDateIntheMonthLastYear = date("Y-m-t",strtotime("-1 year", $date_user_entered));
                     if($store=='0'){
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();   
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();

                     $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                     }else{
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
                     }
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales;
                    $currentAmount = (double)$selectedDateSales->total_sales  ;
                    

                    $previousDateDisplay = date("F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        
                        $labelData[0][] = date("M",strtotime("-".$i." month ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." month ", $date_user_entered));
                        
                        $firstDateIntheMonthNaviDate = date("Y-m-01",strtotime($naviDate));
                        $lastDateIntheMonthNaviDate = date("Y-m-t",strtotime($naviDate));
                        if($store=='0'){
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        }else{
                          $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();  
                        }
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    for($i=5; $i>=0; $i--){
                        
                        $labelData[1][]  = date("M",strtotime("-".$i." month", strtotime($date_one_year_ago)));
                        $naviDate= date("Y-m-d",strtotime("-".$i." month",strtotime($date_one_year_ago)));
                        
                        $firstDateIntheMonthNaviDate = date("Y-m-01",strtotime($naviDate));
                        $lastDateIntheMonthNaviDate = date("Y-m-t",strtotime($naviDate));
                        
                        if($store=='0'){
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();  
                        }else{
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
                        }
                        
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $month = date("m",$date_user_entered);
                    $year = date("Y",$date_user_entered);
                    $StatusCompare = $this->getMonthStatusCompare($year,$month,$store,$platform,$platform_id,$dynamic_table,$skipusers);
                   

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
            }else{
            
                if($store!='all'){
                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-01",strtotime("-1 month", $date_user_entered));
                    $nextDate = date("Y-m-01",strtotime("+1 month", $date_user_entered));
                    
                    $firstDateIntheMonthCurrentYear = date("Y-m-01",$date_user_entered);
                    $lastDateIntheMonthCurrentYear = date("Y-m-t",$date_user_entered);

                    $firstDateIntheMonthLastYear = date("Y-m-01",strtotime("-1 year", $date_user_entered));
                    $lastDateIntheMonthLastYear = date("Y-m-t",strtotime("-1 year", $date_user_entered));

                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();   
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();

                     $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales;
                    $currentAmount = (double)$selectedDateSales->total_sales  ;
                    

                    $previousDateDisplay = date("F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        
                        $labelData[0][] = date("M",strtotime("-".$i." month ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." month ", $date_user_entered));
                        
                        $firstDateIntheMonthNaviDate = date("Y-m-01",strtotime($naviDate));
                        $lastDateIntheMonthNaviDate = date("Y-m-t",strtotime($naviDate));
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
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

                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $month = date("m",$date_user_entered);
                    $year = date("Y",$date_user_entered);
                    $StatusCompare = $this->getMonthStatusCompare($year, $month,$store,$platform,$platform_id,$dynamic_table,$skipusers);
                   

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
                    
                }else{
                    $date_user_entered = strtotime($currentDate);
                    $date_one_year_ago = date("Y-m-d",strtotime("-1 year", $date_user_entered));
                    
                    $previousDate = date("Y-m-01",strtotime("-1 month", $date_user_entered));
                    $nextDate = date("Y-m-01",strtotime("+1 month", $date_user_entered));
                    
                    $firstDateIntheMonthCurrentYear = date("Y-m-01",$date_user_entered);
                    $lastDateIntheMonthCurrentYear = date("Y-m-t",$date_user_entered);

                    $firstDateIntheMonthLastYear = date("Y-m-01",strtotime("-1 year", $date_user_entered));
                    $lastDateIntheMonthLastYear = date("Y-m-t",strtotime("-1 year", $date_user_entered));

                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();   
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthCurrentYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthCurrentYear 23:59:59")
                        ->first();

                     $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                        
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthLastYear 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthLastYear 23:59:59")
                        ->first();  
                        
                
                    
                    $previousAmount = (double)$PreviousDateSales->total_sales;
                    $currentAmount = (double)$selectedDateSales->total_sales  ;
                    

                    $previousDateDisplay = date("F Y",strtotime("-1 year", $date_user_entered));
                    $currentDateDisplay = date("F Y",strtotime($currentDate));
                    
                    $labelData = [];
                    
                    for($i=5; $i>=0; $i--){

                        
                        $labelData[0][] = date("M",strtotime("-".$i." month ", $date_user_entered));
                        $naviDate = date("Y-m-d",strtotime("-".$i." month ", $date_user_entered));
                        
                        $firstDateIntheMonthNaviDate = date("Y-m-01",strtotime($naviDate));
                        $lastDateIntheMonthNaviDate = date("Y-m-t",strtotime($naviDate));
                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
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

                        
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        
                        $valueData[1][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $month = date("m",$date_user_entered);
                    $year = date("Y",$date_user_entered);
                    $StatusCompare = $this->getMonthStatusCompare($year, $month,$store,$platform,$platform_id,$dynamic_table,$skipusers);
                   

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    ); 
                }
            }


                    break;
                    
                case 'YEARCOM':
                    if($platform_id==1){
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
                   if($store=='0'){
                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();     
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();   
                    
                 
                    $PreviousDateSales =    DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                        ->first(); 
                
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                        ->first();   
                   }else{
                     $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();     
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();   
                    
                 
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
                   }
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
                        
                      if($store=='0'){
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
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        ->where("JT.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        ->first();
                        
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first(); 
                      }else{
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
                        
                      }
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $year = date("Y",$date_user_entered);
                    $StatusCompare = $this->getYearStatusCompare($year,$store,$platform,$platform_id,$dynamic_table,$skipusers);
                    
                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
                   }else{
                       if($store!='all'){
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

                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();     
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();   
                    
                 
                    $PreviousDateSales =    DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                        ->first(); 
                
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
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
                        
                            
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
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
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        ->where("JT.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        ->first();
                        
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $year = date("Y",$date_user_entered);
                
                    $StatusCompare = $this->getYearStatusCompare($year,$store,$platform,$platform_id,$dynamic_table,$skipusers);

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
                       }else{
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

                    $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();     
                        
                    $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges ) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$first_date_of_selected_year 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_selected_year 23:59:59")
                        ->first();   
                    
                 
                    $PreviousDateSales =    DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_price_gst_amount + JTD.actual_total_amount) AS total_sales"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$first_date_of_one_year_ago 00:00:00")
                        ->where("JT.transaction_date","<=","$last_date_of_one_year_ago 23:59:59")
                        ->first(); 
                
                    $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
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
                        
                            
                        $PreviousDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
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
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=",$firstDateIntheMonthNaviDate." 00:00:00")
                        ->where("JT.transaction_date","<=",$lastDateIntheMonthNaviDate." 23:59:59")
                        ->first();
                        
                        
                        $PreviousDeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                         ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$firstDateIntheMonthNaviDate 00:00:00")
                        ->where("JT.transaction_date","<=","$lastDateIntheMonthNaviDate 23:59:59")
                        ->first();   
                        
                        $valueData[0][] = $PreviousDateSales->total_sales > 0 ? (double)($PreviousDateSales->total_sales + $PreviousDeliveryChargesDateSales->DeliveryAmount) : 0;

                    }
                    
                    $year = date("Y",$date_user_entered);
                
                    $StatusCompare = $this->getYearStatusCompare($year,$store,$platform,$platform_id,$dynamic_table,$skipusers);

                    $last5cycle = array(
                        "labelData" => $labelData,
                        "valueData" => $valueData,
                        "StatusCompare" => $StatusCompare
                    );
                       }
                   }

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
    
    public function getYearStatusCompare($year,$store,$platform,$platform_id,$dynamic_table,$skipusers){

       
        if($platform_id==1){
             if($store=='0'){
                 $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->whereNotIn('JT.buyer_username',$skipusers)
            ->where('JT.status','cancelled')->count();
            
        $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->whereNotIn('JT.buyer_username',$skipusers)
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->whereNotIn('JT.buyer_username',$skipusers)
            ->where('JT.status',  'refund')->count();
             }else{
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
             }
        }else{
            if($store!='all'){
               $totalOrderCancelled = DB::table('jocom_transaction AS JT')
               ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where(''.$dynamic_table.'.from_account','=',$store)
            ->where('JT.status','cancelled')->count();
            
        $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where(''.$dynamic_table.'.from_account','=',$store)
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where(''.$dynamic_table.'.from_account','=',$store)
            ->where('JT.status',  'refund')->count(); 
            }else{
                $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where('JT.status','cancelled')->count();
            
        $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-01-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-12-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where('JT.status',  'refund')->count();
            }
        }
        
       
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

    public function getMonthStatusCompare($year,$month,$store,$platform,$platform_id,$dynamic_table,$skipusers){
       
        if($platform_id==1){
            
            if($store=='0'){
                $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->whereNotIn('JT.buyer_username',$skipusers)
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->whereNotIn('JT.buyer_username',$skipusers)
            ->where('JT.status',  'refund')->count();
            
        $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->whereNotIn('JT.buyer_username',$skipusers)
            ->where('JT.status',  'cancelled')->count(); 
            }else{
              $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.status',  'refund')->count();
            
        $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.status',  'cancelled')->count();   
            }
        }else{
          if($store!='all'){
              $totalOrderCompleted = DB::table('jocom_transaction AS JT')
             ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where(''.$dynamic_table.'.from_account','=',$store)
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
           ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
             ->where('JT.buyer_username','=',$platform)
            ->where(''.$dynamic_table.'.from_account','=',$store)
            ->where('JT.status',  'refund')->count();
            
        $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where(''.$dynamic_table.'.from_account','=',$store)
            ->where('JT.status',  'cancelled')->count();
          }else{
            $totalOrderCompleted = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where('JT.status',  'completed')->count();
            
        $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where('JT.status',  'refund')->count();
            
        $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
            ->select(DB::raw('count(*) as TotalTransaction'))
            ->where('JT.invoice_no', '<>', "")
            ->where('JT.transaction_date', '>=', "$year-$month-01 00:00:00")
            ->where('JT.transaction_date', '<=', "$year-$month-31 23:59:59")
            ->where('JT.buyer_username','=',$platform)
            ->where('JT.status',  'cancelled')->count();  
          }  
        }
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


    public function getDateStatusCompare($date,$store,$platform,$platform_id,$dynamic_table,$skipusers){

        try{
         
          if($platform_id==1){
             if($store=='0'){
            $totalOrderCompleted = DB::table('jocom_transaction AS JT')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")
                ->whereNotIn('JT.buyer_username',$skipusers)
                ->where('JT.status',  'completed')->count();
                
            $totalOrderRefund = DB::table('jocom_transaction AS JT')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")
                ->whereNotIn('JT.buyer_username',$skipusers)
                ->where('JT.status',  'refund')->count();
                
            $totalOrderCancelled = DB::table('jocom_transaction AS JT')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")
                ->whereNotIn('JT.buyer_username',$skipusers)
                ->where('JT.status',  'cancelled')->count();
             }else{
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
             }
          }else{
           if($store!='all'){
              $totalOrderCompleted = DB::table('jocom_transaction AS JT')
                ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'completed')
                ->where('JT.buyer_username','=',$platform)
                ->where(''.$dynamic_table.'.from_account','=',$store)
                ->count();
                
            $totalOrderRefund = DB::table('jocom_transaction AS JT')
                ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'refund')
                ->where('JT.buyer_username','=',$platform)
                ->where(''.$dynamic_table.'.from_account','=',$store)
                ->count();
                
            $totalOrderCancelled = DB::table('jocom_transaction AS JT')
                ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'cancelled')
                ->where('JT.buyer_username','=',$platform)
                ->where(''.$dynamic_table.'.from_account','=',$store)
                ->count(); 
           }else{
               $totalOrderCompleted = DB::table('jocom_transaction AS JT')
               ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'completed')
                ->where('JT.buyer_username','=',$platform)
                ->count();
                
            $totalOrderRefund = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'refund')
                ->where('JT.buyer_username','=',$platform)
                ->count();
                
            $totalOrderCancelled = DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id')
                ->select(DB::raw('count(*) as TotalTransaction'))
                ->where('JT.invoice_no', '<>', "")
                ->where('JT.transaction_date', '>=', "$date 00:00:00")
                ->where('JT.transaction_date', '<=', "$date 23:59:59")->where('JT.status',  'cancelled')
                ->where('JT.buyer_username','=',$platform)
                ->count();
           }   
          }
            
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
    public function anyPlatformlisting(){
        
        $platform=Input::get('platform');
        $store=Input::get('store');
        $status=Input::get('platformstatus');
        $from_date=Input::get('from_date');
        $to_date=Input::get('to_date');
        $ctime=date("Y-m-d",strtotime($from_date));
        $ltime=date("Y-m-d",strtotime($to_date));
        $dynamic_table=self::PLATFORMS[$platform];
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }
       
        $trans = Transaction::select([
            'jocom_transaction.id',
            'jocom_transaction.transaction_date',
            'jocom_transaction.buyer_username',
            'jocom_transaction.total_amount',
            'jocom_transaction.status',
            'jocom_transaction.gst_total',
            'jocom_transaction.external_ref_number',
            'jocom_transaction_coupon.coupon_code',
            'jocom_transaction_coupon.coupon_amount',
            'jocom_transaction_point.amount AS point_amount',
            'jocom_transaction.delivery_state AS delivery_state',
            'jocom_transaction.delivery_area_type',
        ])
            ->leftJoin('jocom_transaction_coupon', 'jocom_transaction.id', '=', 'jocom_transaction_coupon.transaction_id')
            ->leftJoin('jocom_transaction_point', 'jocom_transaction.id', '=', 'jocom_transaction_point.transaction_id')
            ->leftJoin($dynamic_table, 'jocom_transaction.id', '=', ''.$dynamic_table.'.transaction_id');
            
            if($store!='all'){
              $trans->where(''.$dynamic_table.'.from_account','=',$store);
             }
         if($status!=""){
            $trans->where('jocom_transaction.status','=',$status);
         }
         if($from_date!=''&& $to_date=!''){
            $trans->where('jocom_transaction.transaction_date', '>=', "$ctime 00:00:00")
                ->where('jocom_transaction.transaction_date', '<=', "$ltime 23:59:59"); 
         }
            $trans->where('jocom_transaction.buyer_username','=',$platform);
            $trans->groupBy('jocom_transaction.id');

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="transaction/edit/{{$id}}"><i class="fa fa-pencil"></i></a>';
        return Datatables::of($trans)
            ->add_column('total', '{{number_format(abs($total_amount - $coupon_amount + $gst_total - $point_amount), 2)}}')
            ->edit_column('status',function($row){
                      if($row->status=='completed'){
                          return '<a class="btn btn-success" title="Status" data-toggle="tooltip">'.ucwords($row->status).'</a>';
                      }else{
                       return '<a class="btn btn-warning" title="Status" data-toggle="tooltip">'.ucwords($row->status).'</a>';   
                      }
                      
            })
                 ->add_column('order_number', function($row){     
                switch ($row->buyer_username) {
                    case '11Street':

                        $OrderInfo = DB::table('jocom_elevenstreet_order AS JEO' )
                            ->select('JEO.order_number')
                            ->where("JEO.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->order_number != '' ? $OrderInfo->order_number : $row->external_ref_number;
                        break;
                    
                    case 'lazada':
                        $OrderInfo = DB::table('jocom_lazada_order AS JLZD' )
                            ->select('JLZD.order_number')
                            ->where("JLZD.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->order_number != '' ? $OrderInfo->order_number : $row->external_ref_number;
                        break;
                    
                    case 'shopee':
                        $OrderInfo = DB::table('jocom_shopee_order AS JSPE' )
                            ->select('JSPE.ordersn')
                            ->where("JSPE.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->ordersn != '' ? $OrderInfo->ordersn : $row->external_ref_number;
                        break;
                    case 'pgmall':
                        $OrderInfo = DB::table('jocom_pgmall_order AS JPO' )
                            ->select('JPO.ordersn')
                            ->where("JPO.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->ordersn != '' ? $OrderInfo->ordersn : $row->external_ref_number;
                        break;
                    
                    case 'Qoo10':
                        $OrderInfo = DB::table('jocom_qoo10_order AS JQUO' )
                            ->select('JQUO.packNo')
                            ->where("JQUO.transaction_id",$row->id)->first();
                        
                        return $OrderInfo->packNo != '' ? $OrderInfo->packNo : $row->external_ref_number;
                        break;

                    default:
                        
                        return  $row->external_ref_number;
                        break;
                }
                })
            ->add_column('Action', $actionBar)
            ->make(true);
    }
    public function anyDownloadexcel($type)
    {
        $platform=Input::get('platform');
        $store=Input::get('store');
        $status=Input::get('platformstatus');
        $from_date=Input::get('from_date');
        $to_date=Input::get('to_date');
        $ctime=date("Y-m-d",strtotime($from_date));
        $ltime=date("Y-m-d",strtotime($to_date));
        $dynamic_table=self::PLATFORMS[$platform];
    
         if($platform!=""){
             $data = Transaction::select([
            'jocom_transaction.id',
            'jocom_transaction.buyer_username',
            'jocom_transaction.transaction_date',
            'jocom_transaction.external_ref_number',
            'jocom_transaction_details.sku',
            'jocom_transaction_details.product_name',
            'jocom_transaction_details.price_label',
            'jocom_transaction_details.price',
            'jocom_transaction_details.unit',
            'jocom_transaction_details.total',
            'jocom_transaction.delivery_state',
            'jocom_transaction.delivery_charges',
            'jocom_transaction.total_amount',
            'jocom_transaction.status',
            'jocom_transaction_coupon.coupon_amount',
            'jocom_transaction_point.amount AS point_amount',
            'jocom_transaction.gst_total'
        ])
            ->leftJoin('jocom_transaction_details', 'jocom_transaction.id', '=', 'jocom_transaction_details.transaction_id')
            ->leftJoin('jocom_transaction_point', 'jocom_transaction.id', '=', 'jocom_transaction_point.transaction_id')
            ->leftJoin('jocom_transaction_coupon', 'jocom_transaction.id', '=', 'jocom_transaction_coupon.transaction_id');
            $data->leftJoin($dynamic_table, 'jocom_transaction.id', '=', ''.$dynamic_table.'.transaction_id');
            
             if($store!='all'){
              $data->where(''.$dynamic_table.'.from_account','=',$store);
             }
         if($status!=""){
            $data->where('jocom_transaction.status','=',$status);
         }
         if($from_date!=''&& $to_date=!''){
            $data->where('jocom_transaction.transaction_date', '>=', "$ctime 00:00:00")
                ->where('jocom_transaction.transaction_date', '<=', "$ltime 23:59:59"); 
         }
         $data->where('jocom_transaction.buyer_username','=',$platform);
         $data=$data->get();

        $data_array[] = array('TRANSACTION ID','BUYER USERNAME','TRANSACTION DATE','EXTERNAL REF NUMBER','PRODUCT SKU','PRODUCT NAME','PRODUCT LABEL','PRICE','UNIT','ITEM TOTAL AMOUNT','DELIVERY STATE','DELIVERY CHARGES','TOTAL AMOUNT','STATUS');
            
        foreach ($data as $key => $value) {
            $totals=number_format(abs($value->total_amount - $value->coupon_amount + $value->gst_total - $value->point_amount), 2);
            switch ($value->buyer_username) {
                    case '11Street':

                        $OrderInfo = DB::table('jocom_elevenstreet_order AS JEO' )
                            ->select('JEO.order_number')
                            ->where("JEO.transaction_id",$value->id)->first();
                        
                        $externalnumber=$OrderInfo->order_number != '' ? $OrderInfo->order_number : $row->external_ref_number;
                        break;
                    
                    case 'lazada':
                        $OrderInfo = DB::table('jocom_lazada_order AS JLZD' )
                            ->select('JLZD.order_number')
                            ->where("JLZD.transaction_id",$value->id)->first();
                        
                        $externalnumber=$OrderInfo->order_number != '' ? $OrderInfo->order_number : $value->external_ref_number;
                        break;
                    
                    case 'shopee':
                        $OrderInfo = DB::table('jocom_shopee_order AS JSPE' )
                            ->select('JSPE.ordersn')
                            ->where("JSPE.transaction_id",$value->id)->first();
                        
                        $externalnumber=$OrderInfo->ordersn != '' ? $OrderInfo->ordersn : $value->external_ref_number;
                        break;
                    case 'pgmall':
                        $OrderInfo = DB::table('jocom_pgmall_order AS JPO' )
                            ->select('JPO.ordersn')
                            ->where("JPO.transaction_id",$value->id)->first();
                        
                        $externalnumber=$OrderInfo->ordersn != '' ? $OrderInfo->ordersn : $value->external_ref_number;
                        break;
                    
                    case 'Qoo10':
                        $OrderInfo = DB::table('jocom_qoo10_order AS JQUO' )
                            ->select('JQUO.packNo')
                            ->where("JQUO.transaction_id",$value->id)->first();
                        
                        $externalnumber=$OrderInfo->packNo != '' ? $OrderInfo->packNo : $value->external_ref_number;
                        break;

                    default:
                        
                        $externalnumber=$value->external_ref_number;
                        break;
                }
            
          $data_array[] = array(
            'TRANSACTION ID' =>$value->id,
            'BUYER USERNAME' =>$value->buyer_username,
            'DATE ' =>$value->transaction_date,
            'EXTERNAL REF NUMBER' =>$externalnumber,
            'PRODUCT SKU' =>$value->sku,
            'PRODUCT NAME' =>$value->product_name,
            'PRODUCT LABEL' =>$value->price_label,
            'PRICE' =>$value->price,
            'UNIT' =>$value->unit,
            'ITEM TOTAL' =>$value->total,
            'DELIVERY STATE' =>$value->delivery_state,
            'DELIVERY CHANRGES' =>$value->delivery_charges,
            'TOTAL AMOUNT' =>$totals,
            'STATUS' =>$value->status,
          );
        }
         }
          
        return Excel::create('Transaction Details', function($excel) use ($data_array) {

            $excel->sheet('Transaction Details', function($sheet) use ($data_array)
            {
                // $sheet->cell('A1', function($cell) {$cell->setValue('Transaction -Details');   });
                // $sheet->cell('A1', function($cell) {$cell->setFont(array(
                //   'size' => '25',
                //   'bold' => true
                // ));   });
                // $sheet->cell('A3', function($cell) {$cell->setFont(array(
                //   'bold' => true
                // ));   });
                // $sheet->cell('A4', function($cell) {$cell->setFont(array(
                //   'bold' => true
                // ));   });

                // $sheet->mergeCells('A1:T2');
                // $sheet->setCellValue('A3', 'From Date - ');
                // $sheet->setCellValue('B3','');
                // $sheet->setCellValue('A4', 'To Date - ');
                // $sheet->setCellValue('B4','');
                $sheet->rows($data_array);
                
                $sheet->row(1, function($row) {
                    $row->setBackground('#D9D9D9');
                });
                
                $sheet->setHeight(1,29);
                $sheet->cell('A1:N1',function($cell){
                  $cell->setValignment('center');
                  $cell->setAlignment('center');
                  $cell->setFontWeight('bold');
                });
                
                $count =count($data_array);
                $range = "A1:N".$count;

                $sheet->cells('A1:N1', function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                $sheet->cells($range, function($cells) {
                    $cells->setBorder('thin', 'thin', 'thin', 'thin');
                });
                
            });
        })->setFilename('Transaction-report'.Carbon\Carbon::now()->timestamp)
        ->download($type);
    }
        public function anyDashboardtotal(){
        
        $platform_id=Input::get('platform_id');
        $platform=Input::get('platform');
        $store=Input::get('store_id');
        $dynamic_platform=Input::get('platform');
        if($dynamic_platform=='Astro Go Shop'){
          $dynamic_platform='AstroGoShop';  
        }
        $dynamic_table=self::PLATFORMS[$dynamic_platform];
        $dynamic_details_table=self::PLATFORMSDETAILS[$dynamic_platform];
        $table_sku=self::SKUCOUNT[$dynamic_platform];
        $names=self::NAMESCOUNT[$dynamic_platform];
        $skipusers=self::SKIPUSERS;
        if($platform_id=='1'){
            
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed','cancelled']);
        
         $selectedDateSales =  DB::table('jocom_transaction AS JT')
            ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
            ->select(DB::raw("SUM(JTD.actual_total_amount) AS total_sales"))
            ->whereIn('JT.status', ['completed','cancelled']);
        if($store=='0'){
           $selectedDateSales=$selectedDateSales->whereNotIn('JT.buyer_username',$skipusers)->first();
           $DeliveryChargesDateSales=$DeliveryChargesDateSales->whereNotIn('JT.buyer_username',$skipusers)->first();
           $total_completed_transaction = Transaction::where('status', '=', 'completed')->whereNotIn('jocom_transaction.buyer_username',$skipusers)->count();
           $total_pending_transaction = LogisticTransaction::leftJoin('jocom_transaction', 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')->where('logistic_transaction.status', '=', '0')->whereNotIn('jocom_transaction.buyer_username',$skipusers)->count();
           $total_cancelled_transaction = Transaction::where('status', '=', 'cancelled')->whereNotIn('jocom_transaction.buyer_username',$skipusers)->count();
           $total_refund_transaction = Transaction::where('status', '=', 'refund')->whereNotIn('jocom_transaction.buyer_username',$skipusers)->count();
        }else{
           $selectedDateSales=$selectedDateSales->first();
           $DeliveryChargesDateSales=$DeliveryChargesDateSales->first(); 
           $total_completed_transaction = Transaction::where('status', '=', 'completed')->count();
           $total_pending_transaction = LogisticTransaction::where('status', '=', '0')->count();
           $total_cancelled_transaction = Transaction::where('status', '=', 'cancelled')->count();
           $total_refund_transaction = Transaction::where('status', '=', 'refund')->count();
        }
        $totalproduct =  DB::table('jocom_products')
                ->count();
        $customer_total= Customer::count();
        $total_completed_transaction = $total_completed_transaction;
        $total_pending_transaction = $total_pending_transaction;
        $total_cancelled_transaction = $total_cancelled_transaction;
        $total_refund_transaction = $total_refund_transaction;
        $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
        $totalvalue=number_format($currentAmount,2);
        $total_product=$totalproduct;
        
        }else{
            
            $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed','cancelled']);
            
            $selectedDateSales =  DB::table('jocom_transaction AS JT')
            ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
            ->select(DB::raw("SUM(JTD.actual_total_amount) AS total_sales"))
            ->whereIn('JT.status', ['completed','cancelled']);
            
            if($store!='all'){
            $selectedDateSales->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id');
            $DeliveryChargesDateSales->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id');
            $selectedDateSales->where(''.$dynamic_table.'.from_account','=',$store);
            $DeliveryChargesDateSales->where(''.$dynamic_table.'.from_account','=',$store);
            $total_product =  DB::table($dynamic_table)
                ->join($dynamic_details_table,''.$dynamic_details_table.'.order_id','=',''.$dynamic_table.'.id')
                ->where(''.$dynamic_table.'.from_account','=',$store)
                ->distinct()
                ->count($table_sku);
            $customer_total =  DB::table($dynamic_table)
                ->where(''.$dynamic_table.'.from_account','=',$store)
                ->distinct()
                ->count($names);
           $total_completed_transaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->where('jocom_transaction.buyer_username','=',$platform)->where(''.$dynamic_table.'.from_account','=',$store)->where('jocom_transaction.status', '=', 'completed')->count();
           $total_pending_transaction = LogisticTransaction::leftJoin($dynamic_table, 'logistic_transaction.transaction_id', '=', ''.$dynamic_table.'.transaction_id')->leftJoin('jocom_transaction', 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')->where('logistic_transaction.status', '=', '0')->where('jocom_transaction.buyer_username','=',$platform)->where(''.$dynamic_table.'.from_account','=',$store)->count();
          $total_cancelled_transaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->where('jocom_transaction.buyer_username','=',$platform)->where(''.$dynamic_table.'.from_account','=',$store)->where('jocom_transaction.status', '=', 'cancelled')->count();
          $total_refund_transaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->where('jocom_transaction.buyer_username','=',$platform)->where(''.$dynamic_table.'.from_account','=',$store)->where('jocom_transaction.status', '=', 'refund')->count();

            }else{
            $selectedDateSales->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id');
            $DeliveryChargesDateSales->leftJoin($dynamic_table, 'JT.id', '=', ''.$dynamic_table.'.transaction_id');
            
            $total_product =  DB::table($dynamic_details_table)
                ->distinct()
                ->count($table_sku);
            $customer_total= DB::table($dynamic_table)
                ->distinct()
                ->count($names);
        $total_completed_transaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->where('jocom_transaction.buyer_username','=',$platform)->where('jocom_transaction.status', '=', 'completed')->count();
        $total_pending_transaction = LogisticTransaction::leftJoin($dynamic_table, 'logistic_transaction.transaction_id', '=', ''.$dynamic_table.'.transaction_id')->leftJoin('jocom_transaction', 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')->where('logistic_transaction.status', '=', '0')->where('jocom_transaction.buyer_username','=',$platform)->count();
        $total_cancelled_transaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->where('jocom_transaction.buyer_username','=',$platform)->where('jocom_transaction.status', '=', 'cancelled')->count();
        $total_refund_transaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->where('jocom_transaction.buyer_username','=',$platform)->where('jocom_transaction.status', '=', 'refund')->count();
            }
 
           
           $sales=$selectedDateSales->where('JT.buyer_username','=',$platform)->first();
           $delivery=$DeliveryChargesDateSales->where('JT.buyer_username','=',$platform)->first();
           $currentAmount = (double)$sales->total_sales  + (double)$delivery->DeliveryAmount;
           $totalvalue=number_format($currentAmount,2);
            
            
        }
        
        $data=array('master_total'=>$totalvalue,
        'total_customer'=>number_format($customer_total),
        'total_products'=>number_format($total_product),
        'total_completed_transaction'=>number_format($total_completed_transaction),
        'total_pending_transaction'=>number_format($total_pending_transaction),
        'total_cancelled_transaction'=>number_format($total_cancelled_transaction),
        'total_refund_transaction'=>number_format($total_refund_transaction)
        );
        return $data;
    }
    
    public function anyDashboardlatesttransaction()
    { 
        $platform_id=Input::get('platform_id');
        $platform=Input::get('platform');
        $store=Input::get('store_id');
        $dynamic_platform=Input::get('platform');
        if($dynamic_platform=='Astro Go Shop'){
          $dynamic_platform='AstroGoShop';  
        }
        $dynamic_table=self::PLATFORMS[$dynamic_platform];
         $skipusers=self::SKIPUSERS;
        if($platform_id==1){
        $latestTransaction = Transaction::select('jocom_transaction.transaction_date','jocom_transaction.id','jocom_transaction.total_amount','jocom_transaction.buyer_username','jocom_transaction.delivery_city')
                                        ->where("jocom_transaction.status","=","completed");
            if($store=='0'){
            
                $latestTransaction=$latestTransaction->where("jocom_transaction.invoice_no","<>","")->whereNotIn('jocom_transaction.buyer_username',$skipusers)->orderBy("jocom_transaction.id","DESC")->limit(10);
            }else{
                $latestTransaction=$latestTransaction->where("jocom_transaction.invoice_no","<>","")->orderBy("jocom_transaction.id","DESC")->limit(10);
            }
        $latestTransaction=$latestTransaction;
        
        }else{
                if($store!='all'){
                   $latestTransaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')
                                        ->select('jocom_transaction.transaction_date','jocom_transaction.id','jocom_transaction.total_amount','jocom_transaction.buyer_username','jocom_transaction.delivery_city')
                                        ->where("jocom_transaction.status","=","completed")
                                        ->where("jocom_transaction.invoice_no","<>","")
                                        ->where('jocom_transaction.buyer_username','=',$platform)
                                        ->where(''.$dynamic_table.'.from_account','=',$store)
                                        ->orderBy("jocom_transaction.id","DESC")
                                        ->limit(10); 
                }else{
                    $latestTransaction = Transaction::leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')
                                        ->select('jocom_transaction.transaction_date','jocom_transaction.id','jocom_transaction.total_amount','jocom_transaction.buyer_username','jocom_transaction.delivery_city')
                                        ->where("jocom_transaction.status","=","completed")
                                        ->where("jocom_transaction.invoice_no","<>","")
                                        ->where('jocom_transaction.buyer_username','=',$platform)
                                        ->orderBy("jocom_transaction.id","DESC")
                                        ->limit(10);
                }
             
             
        }
        return Datatables::of($latestTransaction)
                        ->edit_column('transaction_date',function($row){
                         $date=date_format(date_create($row->transaction_date),"d/m/Y H:i A");     
                          return $date;  
                        })
                        ->edit_column('total_amount',function($row){
                         $total=number_format($row->total_amount, 2, '.', '');     
                          return $total;  
                        })
                           ->make();
    }
    public function anyChartone(){
        $platform_id=Input::get('platform_id');
        $platform=Input::get('platform');
        $store=Input::get('store_id');
        $currentdate = date("Y-m-d")." 23:59:59";
        if($platform_id==1){
        $TotalPendingAll = self::skipuserrecordbystatus(0,$currentdate,$store,$platform);
        $TotalUndeliveredAll = self::skipuserrecordbystatus(1,$currentdate,$store,$platform);
        $TotalPartialAll = self::skipuserrecordbystatus(2,$currentdate,$store,$platform);
        $TotalReturnedAll = self::skipuserrecordbystatus(3,$currentdate,$store,$platform);
        $TotalSendingAll = self::skipuserrecordbystatus(4,$currentdate,$store,$platform);
        $TotalSentAll = self::skipuserrecordbystatus(5,$currentdate,$store,$platform);
        $TotalCancelledAll = self::skipuserrecordbystatus(6,$currentdate,$store,$platform);
        }else{
            
        $TotalPendingAll = self::shopeerecordbystatus(0,$currentdate,$store,$platform);
        $TotalUndeliveredAll = self::shopeerecordbystatus(1,$currentdate,$store,$platform);
        $TotalPartialAll = self::shopeerecordbystatus(2,$currentdate,$store,$platform);
        $TotalReturnedAll = self::shopeerecordbystatus(3,$currentdate,$store,$platform);
        $TotalSendingAll = self::shopeerecordbystatus(4,$currentdate,$store,$platform);
        $TotalSentAll = self::shopeerecordbystatus(5,$currentdate,$store,$platform);
        $TotalCancelledAll = self::shopeerecordbystatus(6,$currentdate,$store,$platform);
                
            
        }

        
        $result=array(
                    "TotalPendingAll"=>$TotalPendingAll,
                    "TotalUndeliveredAll"=>$TotalUndeliveredAll,
                    "TotalPartialAll"=>$TotalPartialAll,
                    "TotalReturnedAll"=>$TotalReturnedAll,
                    "TotalSendingAll"=>$TotalSendingAll,
                    "TotalSentAll"=>$TotalSentAll
                );
        return $result;
    }
    public static function shopeerecordbystatus($status,$date,$store,$platform){
        $dynamic_platform=$platform;
        if($dynamic_platform=='Astro Go Shop'){
          $dynamic_platform='AstroGoShop';  
        }
        $dynamic_table=self::PLATFORMS[$dynamic_platform];
    
        $result = LogisticTransaction::leftJoin($dynamic_table, 'logistic_transaction.transaction_id', '=',''.$dynamic_table.'.transaction_id')
                ->leftJoin('jocom_transaction', 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id');
                if($store!='all'){
                  $result->where('jocom_transaction.buyer_username','=',$platform)->where(''.$dynamic_table.'.from_account','=',$store);
                }else{
                  $result->where('jocom_transaction.buyer_username','=',$platform);  
                }
                $result->where("logistic_transaction.status","=",$status);
                $result->where("logistic_transaction.insert_date","<=",$date);
                $count=$result->count();
        return $count;
    }
    public static function skipuserrecordbystatus($status,$date,$store,$platform){
            $skipusers=self::SKIPUSERS;
            if($store=='0'){
                $result = LogisticTransaction::leftJoin('jocom_transaction', 'logistic_transaction.transaction_id', '=', 'jocom_transaction.id')
                        ->where("logistic_transaction.status","=",$status)
                        ->where("logistic_transaction.insert_date","<=",$date)
                        ->whereNotIn('jocom_transaction.buyer_username',$skipusers)
                        ->count();
                }else{
                $result = LogisticTransaction::where("status","=",$status)
                ->where("insert_date","<=",$date)
                ->count();
                }
               
                $count=$result;
        return $count;
    }
    
    public function anyCharttwo(){
        $platform_id=Input::get('platform_id');
        $platform=Input::get('platform');
        $store=Input::get('store_id');
        $dynamic_platform=Input::get('platform');
        if($dynamic_platform=='Astro Go Shop'){
          $dynamic_platform='AstroGoShop';  
        }
        $dynamic_table=self::PLATFORMS[$dynamic_platform];
        $skipusers=self::SKIPUSERS;
        if($platform_id==1){
            if($store=='0'){
                
         $totalProductQty = DB::select(DB::raw('SELECT LTI.name,SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id
                                LEFT JOIN jocom_transaction AS JT ON JT.id = LT.transaction_id
                                WHERE LT.status IN (0)  AND JT.buyer_username NOT IN("'.$skipusers.'")
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 50'));
               
            }else{
             $totalProductQty = DB::select(DB::raw('SELECT LTI.name,SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id
                                WHERE LT.status IN (0)
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 50'));   
            }
        }else{
             
            if($store!='all'){
                
            $totalProductQty = DB::select(DB::raw('SELECT LTI.name,SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id 
                                LEFT JOIN jocom_transaction AS JT ON JT.id = LT.transaction_id
                                LEFT JOIN '.$dynamic_table.' AS JSO ON JSO.transaction_id = LT.transaction_id
                                WHERE JT.buyer_username="'.$platform.'" AND JSO.from_account="'.$store.'" AND LT.status IN (0)
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 50'));
                
                }else{
                    
                   $totalProductQty = DB::select(DB::raw('SELECT LTI.name,SUM(LTI.qty_order) as total_qty from logistic_transaction AS LT
                                LEFT JOIN logistic_transaction_item AS LTI ON LTI.logistic_id = LT.id 
                                LEFT JOIN '.$dynamic_table.' AS JSO ON JSO.transaction_id = LT.transaction_id
                                LEFT JOIN jocom_transaction AS JT ON JT.id = LT.transaction_id
                                WHERE JT.buyer_username="'.$platform.'" AND LT.status IN (0)
                                GROUP BY LTI.name ORDER BY total_qty DESC LIMIT 50'));  
            
                }
            
            
        }
        
        return $totalProductQty;
    }
    public static function sumvalue($startDate,$endDate,$regionID = array(),$platform_id,$platform,$store,$dynamic_table){
        
        $skipusers=self::SKIPUSERS;
        if(count($regionID) > 0){
            
            $searchValue = array();
            foreach ($regionID as $id) {
                
                // take all region 
                if(in_array($id, Config::get('constants.REGION_SPECIAL_CODE'))){
                    
                    $RegionCode = DB::table('jocom_region_refer')
                                ->where('code', $id)
                                ->where('status', 1)
                                ->first();
                  
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('country_id', $RegionCode->country_id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                        
                    
                }else{
                    
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('region_id', $id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                    
                }
            }
            
            $searchValue = array_unique($searchValue);
            if($platform_id==1){
                if($store=='0'){
            $resultQuery =  DB::table('jocom_transaction')->select(
                    DB::raw("SUM(total_amount) as total_order"), 
                    DB::raw("SUM(gst_total) as gst_total"))
                    // ->whereIn('status', ['completed', 'cancelled', 'refund'])
                    ->whereIn('status', ['completed'])
                    ->whereIn('delivery_state', $searchValue)
                    //->where('status','completed')
                    ->where('invoice_no','<>','')
                    ->whereNotIn('jocom_transaction.buyer_username',$skipusers)
                    ->where('transaction_date','>=',$startDate)
                    ->where('transaction_date','<=',$endDate)
                    ->first();
                }else{
                   $resultQuery =  DB::table('jocom_transaction')->select(
                    DB::raw("SUM(total_amount) as total_order"), 
                    DB::raw("SUM(gst_total) as gst_total"))
                    // ->whereIn('status', ['completed', 'cancelled', 'refund'])
                    ->whereIn('status', ['completed'])
                    ->whereIn('delivery_state', $searchValue)
                    //->where('status','completed')
                    ->where('invoice_no','<>','')
                    ->where('transaction_date','>=',$startDate)
                    ->where('transaction_date','<=',$endDate)
                    ->first(); 
                }
            }else{
                if($store!='all'){
               $resultQuery =  DB::table('jocom_transaction')->leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')
               ->select(DB::raw("SUM(jocom_transaction.total_amount) as total_order"), 
                    DB::raw("SUM(jocom_transaction.gst_total) as gst_total"))
                    // ->whereIn('status', ['completed', 'cancelled', 'refund'])
                    ->whereIn('jocom_transaction.status', ['completed'])
                    ->whereIn('jocom_transaction.delivery_state', $searchValue)
                    ->where('jocom_transaction.buyer_username','=',$platform)
                    ->where(''.$dynamic_table.'.from_account','=',$store)
                    ->where('jocom_transaction.invoice_no','<>','')
                    ->where('jocom_transaction.transaction_date','>=',$startDate)
                    ->where('jocom_transaction.transaction_date','<=',$endDate)
                    ->first(); 
                }else{
                   $resultQuery =  DB::table('jocom_transaction')->leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->select(
                    DB::raw("SUM(jocom_transaction.total_amount) as total_order"), 
                    DB::raw("SUM(jocom_transaction.gst_total) as gst_total"))
                    // ->whereIn('status', ['completed', 'cancelled', 'refund'])
                    ->whereIn('jocom_transaction.status', ['completed'])
                    ->whereIn('jocom_transaction.delivery_state', $searchValue)
                     ->where('jocom_transaction.buyer_username','=',$platform)
                    ->where('jocom_transaction.invoice_no','<>','')
                    ->where('jocom_transaction.transaction_date','>=',$startDate)
                    ->where('jocom_transaction.transaction_date','<=',$endDate)
                    ->first();  
                }
            }
            
        }else{
            if($platform_id==1){
                if($store=='0'){
           $resultQuery =  DB::table('jocom_transaction')->select(
                    DB::raw("SUM(total_amount) as total_order"), 
                    DB::raw("SUM(gst_total) as gst_total"))
                    ->whereIn('status', ['completed'])
                    ->where('invoice_no','<>','')
                    ->whereNotIn('jocom_transaction.buyer_username',$skipusers)
                    ->where('transaction_date','>=',$startDate)
                    ->where('transaction_date','<=',$endDate)
                    ->first();
                }else{
                  $resultQuery =  DB::table('jocom_transaction')->select(
                    DB::raw("SUM(total_amount) as total_order"), 
                    DB::raw("SUM(gst_total) as gst_total"))
                    ->whereIn('status', ['completed'])
                    ->where('invoice_no','<>','')
                    ->where('transaction_date','>=',$startDate)
                    ->where('transaction_date','<=',$endDate)
                    ->first();  
                }
            }else{
                if($store!='all'){
                    $resultQuery =  DB::table('jocom_transaction')->leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->select(
                    DB::raw("SUM(jocom_transaction.total_amount) as total_order"), 
                    DB::raw("SUM(jocom_transaction.gst_total) as gst_total"))
                    ->whereIn('jocom_transaction.status', ['completed'])
                    ->where('jocom_transaction.invoice_no','<>','')
                    ->where('jocom_transaction.buyer_username','=',$platform)
                    ->where(''.$dynamic_table.'.from_account','=',$store)
                    ->where('jocom_transaction.transaction_date','>=',$startDate)
                    ->where('jocom_transaction.transaction_date','<=',$endDate)
                    ->first();
                }else{
                    $resultQuery =  DB::table('jocom_transaction')->leftJoin($dynamic_table, 'jocom_transaction.id', '=',''.$dynamic_table.'.transaction_id')->select(
                    DB::raw("SUM(jocom_transaction.total_amount) as total_order"), 
                    DB::raw("SUM(jocom_transaction.gst_total) as gst_total"))
                    ->whereIn('jocom_transaction.status', ['completed'])
                    ->where('jocom_transaction.invoice_no','<>','')
                    ->where('jocom_transaction.buyer_username','=',$platform)
                    ->where('jocom_transaction.transaction_date','>=',$startDate)
                    ->where('jocom_transaction.transaction_date','<=',$endDate)
                    ->first();
                }
            }
                    

        }
       
        return $resultQuery ;
        
    }
    
    public static function sumvaluetwo($startDate,$endDate,$regionID = array(),$platform_id,$platform,$store,$dynamic_table){
        $skipusers=self::SKIPUSERS;
        
        if(count($regionID) > 0){
            
            $searchValue = array();
            foreach ($regionID as $id) {
                
                // take all region 
                if(in_array($id, Config::get('constants.REGION_SPECIAL_CODE'))){
                    
                    $RegionCode = DB::table('jocom_region_refer')
                                ->where('code', $id)
                                ->where('status', 1)
                                ->first();
                  
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('country_id', $RegionCode->country_id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                        
                    
                }else{
                    
                    $RegionState  = DB::table('jocom_country_states')
                                ->where('region_id', $id)
                                ->where('status', 1)
                                ->get();
                    
                    foreach ($RegionState as $key => $value) {
                        $searchValue[] = $value->name;
                    }
                    
                }
            }
            
            $searchValue = array_unique($searchValue);
            
            
            if($platform_id==1){
               if($store=='0'){
                    
                  $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->whereIn('JT.delivery_state', $searchValue)
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();   
                        
            $resultQuery =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first(); 
               }else{
                  
                   $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->whereIn('JT.delivery_state', $searchValue)
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();   
                        
            $resultQuery =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();
               }   
            }else{
              if($store!='all'){
                  $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->whereIn('JT.delivery_state', $searchValue)
                        ->where('JT.invoice_no','<>','')
                         ->where('JT.buyer_username','=',$platform)
                         ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();   
                        
            $resultQuery =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                         ->where('JT.buyer_username','=',$platform)
                         ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();
              }else{
                  $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->whereIn('JT.delivery_state', $searchValue)
                        ->where('JT.invoice_no','<>','')
                         ->where('JT.buyer_username','=',$platform)
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();   
                        
            $resultQuery =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                         ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();
              }   
            }
            $dataRecord = array(
                    "total_order" => (double)($resultQuery->total_order ),
                    "gst_total" => (double)($resultQuery->gst_total )
                )   ;

      
      
        }else{
            
             if($platform_id==1){
                     if($store=='0'){
                        $resultQuery = DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                ->select(
                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END AS total_order"), 
                                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(
                        CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        ), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    END AS gst_total"))
                ->whereIn('JT.status', ['completed'])
                ->where('JT.invoice_no','<>','')
                ->whereNotIn('JT.buyer_username',$skipusers)
                ->where('JT.transaction_date','>=',$startDate)
                ->where('JT.transaction_date','<=',$endDate)
                ->first();
                
            $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();  
                        
                        
            // 
            
            $a =  date('2015-01-01 00:00:00');
            $b =  date('2018-11-22 23:59:59');
            
            $a =  $startDate;
            $b =  $endDate;
            
            $selectedDateSales =  DB::table('jocom_transaction AS JT')
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
        ->whereNotIn('JT.buyer_username',$skipusers)
        ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
        ->first();
        
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->whereNotIn('JT.buyer_username',$skipusers)
            ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
            ->first();
            
            
             $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->whereNotIn('JT.buyer_username',$skipusers)
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();   
                        
                        
            $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount; 
                     }else{
                         $resultQuery = DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                ->select(
                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END AS total_order"), 
                                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(
                        CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        ), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    END AS gst_total"))
                ->whereIn('JT.status', ['completed'])
                ->where('JT.invoice_no','<>','')
                ->where('JT.transaction_date','>=',$startDate)
                ->where('JT.transaction_date','<=',$endDate)
                ->first();
                
            $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();  
                        
                        
            // 
            
            $a =  date('2015-01-01 00:00:00');
            $b =  date('2018-11-22 23:59:59');
            
            $a =  $startDate;
            $b =  $endDate;
            
            $selectedDateSales =  DB::table('jocom_transaction AS JT')
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
        ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
        ->first();
        
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
            ->first();
            
            
             $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();   
                        
                        
            $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;
                     }     
             }else{
                 if($store!='all'){
                         $resultQuery = DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                ->select(
                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END AS total_order"), 
                                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(
                        CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        ), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    END AS gst_total"))
                ->whereIn('JT.status', ['completed'])
                ->where('JT.invoice_no','<>','')
                ->where('JT.buyer_username','=',$platform)
                ->where(''.$dynamic_table.'.from_account','=',$store)
                ->where('JT.transaction_date','>=',$startDate)
                ->where('JT.transaction_date','<=',$endDate)
                ->first();
                
            $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();  
                        
                        
            // 
            
            $a =  date('2015-01-01 00:00:00');
            $b =  date('2018-11-22 23:59:59');
            
            $a =  $startDate;
            $b =  $endDate;
            
            $selectedDateSales =  DB::table('jocom_transaction AS JT')
        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
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
        ->where('JT.buyer_username','=',$platform)
        ->where(''.$dynamic_table.'.from_account','=',$store)
        ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
        ->first();
        
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
            ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where('JT.buyer_username','=',$platform)
             ->where(''.$dynamic_table.'.from_account','=',$store)
            ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
            ->first();
            
            
             $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where(''.$dynamic_table.'.from_account','=',$store)
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();   
                        
                        
            $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount;  
                 }else{
                    $resultQuery = DB::table('jocom_transaction AS JT')
                ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                ->select(
                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                        		ROUND(SUM(CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price ELSE JTD.price END  * JTD.unit ) , 2)
                            ELSE 
                        		ROUND(SUM(CASE WHEN JTD.ori_price IS NULL THEN JTD.price ELSE JTD.ori_price END  * JTD.unit ) , 2)  
                            END AS total_order"), 
                                    DB::raw("CASE WHEN YEAR(JT.transaction_date) = 2017 THEN 
                		ROUND(SUM(
                        CASE WHEN YEAR(JT.transaction_date) = 2017 THEN JTD.actual_price_gst_amount ELSE 0 END 
                        ), 2)
                    ELSE 
                		ROUND(SUM(CASE WHEN JTD.gst_ori IS NULL THEN JTD.gst_amount ELSE JTD.gst_ori * JTD.unit END   ) , 2)
                    END AS gst_total"))
                ->whereIn('JT.status', ['completed'])
                ->where('JT.invoice_no','<>','')
                ->where('JT.buyer_username','=',$platform)
                ->where('JT.transaction_date','>=',$startDate)
                ->where('JT.transaction_date','<=',$endDate)
                ->first();
                
            $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->select(DB::raw("SUM(JT.delivery_charges) AS DeliveryAmount , SUM(JT.gst_delivery) AS GSTDeliveryAmount"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where('JT.transaction_date','>=',$startDate)
                        ->where('JT.transaction_date','<=',$endDate)
                        ->first();  
                        
                        
            // 
            
            $a =  date('2015-01-01 00:00:00');
            $b =  date('2018-11-22 23:59:59');
            
            $a =  $startDate;
            $b =  $endDate;
            
            $selectedDateSales =  DB::table('jocom_transaction AS JT')
        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
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
        ->where('JT.buyer_username','=',$platform)
        ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
        ->first();
        
        $DeliveryChargesDateSales =  DB::table('jocom_transaction AS JT')
            ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
            ->select(DB::raw("SUM(JT.delivery_charges + JT.gst_delivery) AS DeliveryAmount"))
            ->whereIn('JT.status', ['completed'])
            ->where('JT.invoice_no','<>','')
            ->where('JT.buyer_username','=',$platform)
            ->where("JT.transaction_date",">=",$a)
            ->where("JT.transaction_date","<=",$b)
            ->first();
            
            
             $selectedDateSales =  DB::table('jocom_transaction AS JT')
                        ->leftJoin($dynamic_table, 'JT.id', '=',''.$dynamic_table.'.transaction_id')
                        ->leftjoin('jocom_transaction_details AS JTD', 'JT.id','=','JTD.transaction_id')
                        ->select(
                            DB::raw("SUM(JTD.actual_total_amount) AS total_order , SUM(JTD.actual_price_gst_amount ) AS gst_total"))
                        ->whereIn('JT.status', ['completed'])
                        ->where('JT.invoice_no','<>','')
                        ->where('JT.buyer_username','=',$platform)
                        ->where("JT.transaction_date",">=","$startDate 00:00:00")
                        ->where("JT.transaction_date","<=","$endDate 23:59:59")
                        ->first();   
                        
                        
            $currentAmount = (double)$selectedDateSales->total_sales  + (double)$DeliveryChargesDateSales->DeliveryAmount; 
                 }
             }
              
                
            $dataRecord = array(
                    "total_order" => (double)$selectedDateSales->total_order  , //$currentAmount ,
                    "gst_total" => (double)$selectedDateSales->gst_total  //0
                )  ;


        }
        
        
        return (object)$dataRecord ;
        
    }
    
   
}
