<?php

//Nadzri 10/12/2021 - Create Processor Dashboard
class ProcessorDashboardController extends BaseController
{

    public function anyProcessor(){
        //link to view
        return View::make('admin.processor_dashboard');
    }
    
    public function anyDashboardprocessor(){
        //automatically process data base on the selected date type (Daily, Weekly, Monthly)

        $data = array();

        $rangeType = Input::get("rangeType");
        $startDate = Input::get("startDate");
        $toDate = Input::get("toDate");
        $navigate = Input::get("navigation");
        
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
            case 4: // Custom
        
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
        
        //display date
        $displayStartDate = date("d M Y", strtotime($startDate)); 
        $displayEndDate = date("d M Y", strtotime($toDate));

        $currentdate = date("Y-m-d")." 23:59:59";
            
        $data['DateSelection'] = array(
            "startDate" => $startDate,
            "toDate" => $toDate,
            "displayStartDate" => $displayStartDate,
            "displayEndDate" => $displayEndDate,
            "today" => date("Y-m-d"),
            "WeeklyStartDate" =>  date("Y-m-d", strtotime('monday this week')), 
            "WeeklyEndDate" =>  date("Y-m-d",  strtotime('sunday this week')),
            "MonthStartDate" => date('Y-m-1'),
            "MonthEndDate" => date('Y-m-t')
        );        
        
        //package query
        $allProduct = DB::table("jocom_transaction as jt")
            ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
            ->leftjoin('jocom_seller as js', 'js.id', '=', 'jtd.seller_id')
            ->select(DB::raw("jtd.product_id as prodID, js.company_name as companyName, 
                    jtd.sku as sku, jtd.product_name as productName, jtd.price_label as label, 
                    round(sum(jtd.unit), 0) as unitsSold, round(sum(jtd.total), 2) as revenue"))
            ->whereBetween('jt.transaction_date', [$startDate, $toDate])
            ->where("jt.status", "completed")
            ->where("jt.invoice_no", "<>", "")
            ->groupBy("jtd.product_id")
            ->orderBy("unitsSold", "desc")
            ->get();
    
    //   $coupon_code = ['VVIP16', 'VVIP24', 'VVIP36','VVIP54', 'VVIP65', 'VVIP84','HARIHARI20'];        
    //   $allProduct = DB::table("jocom_transaction as jt")
    //         ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
    //         ->leftjoin('jocom_seller as js', 'js.id', '=', 'jtd.seller_id')
    //         ->leftjoin('jocom_transaction_coupon as jtc','js.id', '=', 'jtc.transaction_id')
    //         ->select(DB::raw("jtd.product_id as prodID, js.company_name as companyName, 
    //                 jtd.sku as sku, jtd.product_name as productName, jtd.price_label as label, 
    //                 round(sum(jtd.unit), 0) as unitsSold, round(sum(jtd.total), 2) as revenue"))
    //         ->whereBetween('jt.transaction_date', [$startDate, $toDate])
    //         ->where("jt.status", "completed")
    //         ->where("jt.invoice_no", "<>", "")
           
    //         ->whereNotIn('jtc.coupon_code', $coupon_code)
    //          ->orwhere('jtc.coupon_code','=',"")
    //         ->groupBy("jtd.product_id")
    //         ->orderBy("unitsSold", "desc")
    //         ->get();            

        // //to list the transactions and its item product
        // $products = DB::table("jocom_transaction as jt")
        //     ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
        //     ->select(DB::raw("jt.id as transID, jtd.product_id as prodID, jtd.price_label as label, 
        //             sum(jtd.unit) as totalQuantity, sum(jtd.total) as price"))
        //     ->whereBetween('jt.transaction_date', [$startDate, $toDate])
        //     ->where("jt.status", "completed")
        //     ->where("jt.invoice_no", "<>", "")
        //     ->groupBy("jtd.product_id")
        //     ->get();


        // $allProduct = [];
        // foreach($products as $product){ //loop using transactions list
        //     $productID = $product->prodID;
        //     $transactionID = $product->transID;

        //     //determine the product is base product or not
        //     $isProductBase = DB::table("jocom_products as jp")
        //         ->where("jp.id", "=", $productID)
        //         ->select(DB::raw("DISTINCT(jp.id) as prodID, jp.is_base_product as prodBase"))
        //         ->first();

        //     if($isProductBase->prodBase == 1){ //not product base
        //         $productSold = DB::table("jocom_transaction as jt")
        //                 ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
        //                 ->leftjoin('jocom_seller as js', 'js.id', '=', 'jtd.seller_id')
        //                 ->select(DB::raw("jt.transaction_date as date, jtd.product_id as prodID, jtd.sku as sku, js.company_name as companyName, 
        //                         jtd.product_name as productName, jtd.price_label as label, round(sum(jtd.unit), 0) as unitsSold, 
        //                         sum(jtd.total) as revenue"))
        //                 ->where("jt.id", $transactionID)
        //                 ->where("jtd.product_id", $productID)
        //                 ->groupBy("jtd.product_id")
        //                 ->get();
        //     }else{ //product base
        //         $unitsSold = $product->totalQuantity;
                
        //         $productSold = DB::table("jocom_transaction as jt")
        //             ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
        //             ->leftjoin('jocom_seller as js', 'js.id', '=', 'jtd.seller_id')
        //             ->leftjoin('jocom_product_base_item as jpbi', 'jpbi.product_id', '=', 'jtd.product_id')
        //             ->leftjoin('jocom_products as jp', 'jp.id', '=', 'jpbi.product_base_id')
        //             ->leftjoin('jocom_product_price as jpp', 'jpp.product_id', '=', 'jpbi.product_base_id')
        //             ->select(DB::raw("jtd.product_id as prodID, jpbi.product_base_id as prodBaseID, 
        //                     jp.sku as sku, js.company_name as companyName, jp.name as productName, jpp.label as label, 
        //                     jpbi.quantity*'$unitsSold' as unitsSold, round(jtd.price*'$unitsSold', 2) as revenue"))
        //             ->where("jt.id", $transactionID)
        //             ->where("jtd.product_id", $productID)
        //             ->where("jpbi.status", 1)
        //             ->groupBy(DB::raw("IFNULL(jpbi.product_base_id, jtd.product_id)"))
        //             ->get();
        //     }      

        //     //if data(array) > 0. To save all array inside single square bracket
        //     if (sizeof($productSold) > 0) {
        //         foreach($productSold as $p){
        //             array_push($allProduct, $p); //collect array from $productSolc
        //         }
        //     }
        // }
        // usort($allProduct, function($a, $b) { //Sort the array
        //     return $a->unitsSold < $b->unitsSold  ? 1 : -1; //Compare the scores
        // }); 

        $arrayTotalProductSold =  json_encode($allProduct);

        // total of quantity sold from arrray 
        $sumQuantity = array_reduce($allProduct, function($carry, $item)
        {
            return $carry + $item->unitsSold;
        });

        //total of price from arrray 
        $sumPrice= array_reduce($allProduct, function($carry, $item)
        {
            return number_format((float)($carry + $item->revenue), 2, '.', '');
        });
        
        $returnCollection = array(
            "TotalOrderAndRevenue"=>array(
                "totalOrder" =>  $sumQuantity." Units",
                "totalRevenue" =>  "RM ".$sumPrice
            ),
            "ProcessorDashboard"=>array(
                "allProductSold" => $arrayTotalProductSold,
            )
        );

        $data['TotalProcessor'] = $returnCollection;

        return array(
            "data" => $data,
        );
    }

    public function anyGenerateprocessorexcel(){
        //To generate and print excel

        $date = Input::get("selected-date");
        $startDate = Input::get("start-date-select");
        $toDate = Input::get("to-date-select");
        $customStartDate = Input::get("transaction_from")." 00:00:00";
        $customToDate = Input::get("transaction_to")." 23:23:59";
        $rangeType = Input::get("type-select");

        //package query
        $allProduct = DB::table("jocom_transaction as jt")
            ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
            ->leftjoin('jocom_seller as js', 'js.id', '=', 'jtd.seller_id')
            ->select(DB::raw("jtd.product_id as prodID, js.company_name as companyName, 
                    jtd.sku as sku, jtd.product_name as productName, jtd.price_label as label, 
                    round(sum(jtd.unit), 0) as unitsSold, round(sum(jtd.total), 2) as revenue"))
            ->whereBetween('jt.transaction_date', [$startDate, $toDate])
            ->where("jt.status", "completed")
            ->where("jt.invoice_no", "<>", "")
            ->groupBy("jtd.product_id")
            ->orderBy("unitsSold", "desc")
            ->get();

        // //to list the transactions and its item product
        // $products = DB::table("jocom_transaction as jt")
        //     ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
        //     ->select(DB::raw("jt.id as transID, jtd.product_id as prodID, jtd.price_label as label, 
        //             sum(jtd.unit) as totalQuantity, sum(jtd.total) as price"))
        //     ->whereBetween('jt.transaction_date', [$startDate, $toDate])
        //     ->where("jt.status", "completed")
        //     ->where("jt.invoice_no", "<>", "")
        //     ->groupBy("jtd.product_id")
        //     ->get();

        //     $allProduct = [];
        //     foreach($products as $product){ //loop using transactions list
        //         $productID = $product->prodID;
        //         $transactionID = $product->transID;

        //         //determine the product is base product or not
        //         $isProductBase = DB::table("jocom_products as jp")
        //             ->where("jp.id", "=", $productID)
        //             ->select(DB::raw("DISTINCT(jp.id) as prodID, jp.is_base_product as prodBase"))
        //             ->first();

        //         if($isProductBase->prodBase == 1){ //not product base

        //             $productSold = DB::table("jocom_transaction as jt")
        //                 ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
        //                 ->leftjoin('jocom_seller as js', 'js.id', '=', 'jtd.seller_id')
        //                 ->select(DB::raw("jtd.product_id as prodID, jtd.sku as sku, js.company_name as companyName, 
        //                         jtd.product_name as productName, jtd.price_label as label, round(sum(jtd.unit), 0) as unitsSold, 
        //                         sum(jtd.total) as revenue"))
        //                 ->where("jt.id", $transactionID)
        //                 ->where("jtd.product_id", $productID)
        //                 ->get();
        //         }else{ //product base
        //             $unitsSold = $product->totalQuantity;
                    
        //             $productSold = DB::table("jocom_transaction as jt")
        //                 ->leftjoin('jocom_transaction_details as jtd', 'jtd.transaction_id', '=', 'jt.id')
        //                 ->leftjoin('jocom_seller as js', 'js.id', '=', 'jtd.seller_id')
        //                 ->leftjoin('jocom_product_base_item as jpbi', 'jpbi.product_id', '=', 'jtd.product_id')
        //                 ->leftjoin('jocom_products as jp', 'jp.id', '=', 'jpbi.product_base_id')
        //                 ->leftjoin('jocom_product_price as jpp', 'jpp.product_id', '=', 'jpbi.product_base_id')
        //                 ->select(DB::raw("jtd.product_id as prodID , jp.sku as sku, js.company_name as companyName, 
        //                         jp.name as productName, jpp.label as label, jpbi.quantity*'$unitsSold' as unitsSold, 
        //                         round(jtd.price*'$unitsSold', 2) as revenue"))
        //                 ->where("jt.id", $transactionID)
        //                 ->where("jtd.product_id", $productID)
        //                 ->where("jpbi.status", 1)
        //                 ->groupBy(DB::raw("IFNULL(jpbi.product_base_id, jtd.product_id)"))
        //                 ->get();
        //         }      

        //         //if data(array) > 0. To save all array inside single square bracket
        //         if (sizeof($productSold) > 0) {
        //             foreach($productSold as $p){
        //                 array_push($allProduct, $p); //collect array from $productSolc
        //             }
        //         }
        //     }

        //     usort($allProduct, function($a, $b) { //Sort the array
        //         return $a->unitsSold < $b->unitsSold  ? 1 : -1; //Compare the scores
        //     }); 
            

            $arrayTotalProductSold =  json_decode(json_encode($allProduct));

            // total of quantity from arrray 
            $sumQuantity = array_reduce($allProduct, function($carry, $item)
            {
                return $carry + $item->unitsSold;
            });

            //total of price from arrray 
            $sumPrice= array_reduce($allProduct, function($carry, $item)
            {
                return number_format((float)($carry + $item->revenue), 2, '.', '');
            });

            $repname = 'Processor Report';
    
            $data = array(
                "startDate" => $startDate,
                "toDate" => $toDate,  
                "sumQuantity" => $sumQuantity,  
                "sumPrice" => $sumPrice,  
                "totalProductSold" => $arrayTotalProductSold          
            );
    
    
            return Excel::create($repname.'_'.date("dmyHis"), function($excel) use ($data) {
                $excel->sheet('Processor', function($sheet) use ($data)
                {   
                    $sheet->loadView('admin.processor_excel', array('data' =>$data));
                    
                });
            })->download('xls');
    
    }

}