<?php 

use Helper\ImageHelper as Image;


class ReportTemplateController extends BaseController {
   
   public static function anyIndex(){

    $Seller = Seller::where("active_status",1)->orderBy("company_name","asc")->get();

    // $transID = DB::table('jocom_transaction')->where('status','completed')->orderby('transaction_date','DESC')->limit(50)->get();

    // $products = DB::table('jocom_products')->where('status','1')->orderby('id', 'desc')->limit(50)->get();
    // print_r($transID );die();

    $category = DB::table('jocom_products_category')->orderby('category_name','ASC')->get();

    return View::make('report.report_template')->with("seller",$Seller)->with("transaction_id",$transID)->with("products",$products)->with('category',$category);
   }

   public static function anyGenerate(){

        try{

            $type_report = Input::get('type_report');
            $is_checked = Input::get('is_checked');
            $from = Input::get('created_from')." 00:00:00";
            $to = Input::get('created_to')." 23:59:59";
            $supplier = Input::get('supplier');
            $transaction_id = Input::get('transID');
            $category = Input::get('category');
            $productID = Input::get('product');

            if (!empty($transaction_id)) {
                $transID = explode(",", $transaction_id);
            }

            if (!empty($productID)) {
                $product = explode(",", $productID);
            }
            
            switch ($type_report) {

                case '1':

                    $ElevenStreet = self::getPlatformrecord('11Street',$from,$to,$supplier,$category,$product,$transID); 
                    $lazada = self::getPlatformrecord('lazada',$from,$to,$supplier,$category,$product,$transID); 
                    $shopee = self::getPlatformrecord('shopee',$from,$to,$supplier,$category,$product,$transID); 
                    $Qoo10 = self::getPlatformrecord('Qoo10',$from,$to,$supplier,$category,$product,$transID); 
                    $Jocom = self::getPlatformrecord('Jocom',$from,$to,$supplier,$category,$product,$transID); 
                    $Astro = self::getPlatformrecord('Astro Go Shop',$from,$to,$supplier,$category,$product,$transID); 

                    $data['platform'] = array(
                        "ElevenStreet" => round($ElevenStreet->total_order + $ElevenStreet->gst_total, 2), 
                        "lazada" => round($lazada->total_order + $lazada->gst_total,2),
                        "Jocom" => round($Jocom->total_order + $Jocom->gst_total,2),
                        "Shopee" => round($shopee->total_order + $shopee->gst_total,2),
                        "Qoo10" => round($Qoo10->total_order + $Qoo10->gst_total,2),
                        "Astro" => round($Astro->total_order + $Astro->gst_total,2),
                    );

                    $data['daytoday'] =  self::getDayrecord($from,$to,$supplier,$category,$product,$transID);   

                    $data['state'] =  self::getStaterecord($from,$to,$supplier,$category,$product,$transID); 

                    $data['postcode'] = self::getPostcoderecord($from,$to,$supplier,$category,$product,$transID);


                    //logistic status 
                    $sent = self::getStatusrecord('5',$from,$to,$supplier,$category,$product,$transID); 
                    $cancelled = self::getStatusrecord('6',$from,$to,$supplier,$category,$product,$transID);
                   
                    $data['status'] = array(
                        "cancelled" => round($cancelled->total_order,2),
                        "delivered" => round($sent->total_order,2),
                    );

                    //products
                    $data['products'] = self::getProductrecord($from,$to,$supplier,$category,$product,$transID);

                    //region
                    $data['region'] = self::getRegionrecord($from,$to,$supplier,$category,$product,$transID);

                    $data['monthly'] =  self::getMonthlyrecord($from,$to,$supplier,$category,$product,$transID);

                    $data['quaterly'] =  self::getQuaterlyrecord($from,$to,$supplier,$category,$product,$transID);

                    break;
                case '2':

                    $ElevenStreet = self::getPlatformrecordproduct('11Street',$from,$to,$supplier,$category,$product); 
                    $lazada = self::getPlatformrecordproduct('lazada',$from,$to,$supplier,$category,$product); 
                    $shopee = self::getPlatformrecordproduct('shopee',$from,$to,$supplier,$category,$product); 
                    $Qoo10 = self::getPlatformrecordproduct('Qoo10',$from,$to,$supplier,$category,$product); 
                    $Jocom = self::getPlatformrecordproduct('Jocom',$from,$to,$supplier,$category,$product); 
                    $Astro = self::getPlatformrecordproduct('Astro Go Shop',$from,$to,$supplier,$category,$product); 

                    $data['platform'] = array(
                        "ElevenStreet" => round($ElevenStreet->total_order + $ElevenStreet->gst_total, 2), 
                        "lazada" => round($lazada->total_order + $lazada->gst_total,2),
                        "Jocom" => round($Jocom->total_order + $Jocom->gst_total,2),
                        "Shopee" => round($shopee->total_order + $shopee->gst_total,2),
                        "Qoo10" => round($Qoo10->total_order + $Qoo10->gst_total,2),
                        "Astro" => round($Astro->total_order + $Astro->gst_total,2),
                    );

                    $data['daytoday'] =  self::getDayrecordproduct($from,$to,$supplier,$category,$product);   

                    $data['state'] =  self::getStaterecordproduct($from,$to,$supplier,$category,$product); 

                    $data['postcode'] = self::getPostcoderecordproduct($from,$to,$supplier,$category,$product);


                    //logistic status 
                    $sent = self::getStatusrecordproduct('5',$from,$to,$supplier,$category,$product); 
                    $cancelled = self::getStatusrecordproduct('6',$from,$to,$supplier,$category,$product);
                   
                    $data['status'] = array(
                        "cancelled" => round($cancelled->total_order,2),
                        "delivered" => round($sent->total_order,2),
                    );

                    //products
                    $data['products'] = self::getProductrecordproduct($from,$to,$supplier,$category,$product);

                    //region
                    $data['region'] = self::getRegionrecordproduct($from,$to,$supplier,$category,$product);

                    $data['monthly'] =  self::getMonthlyrecordproduct($from,$to,$supplier,$category,$product);

                    $data['quaterly'] =  self::getQuaterlyrecordproduct($from,$to,$supplier,$category,$product,$transID);
                    # code...
                    break;
                default:
                    # code...
                    break;
            }

            return $data;

        }catch(Exception $ex){
            echo $ex;
            return $ex->getMessage();
        }

   }


   public static function getPlatformrecord($platform, $from,$to,$supplier,$category,$product,$transID){
        
       
        try{
            
            switch ($platform) {

                case 'Jocom':
                    $platforms = DB::table('jocom_transaction AS JT')
                        ->whereNotIn('JT.buyer_username', ['lazada','11Street', 'shopee','Qoo10','Astro Go Shop'])
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);
                        
                        if (!empty($supplier)) {
                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JTD.seller_id','=',$supplier);

                        }elseif (!empty($category)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JP.category','LIKE', "%".$category."%")
                                        ->groupBy('JTD.product_id')
                                        ->orderby('JTD.product_id', 'DESC');

                        }elseif (!empty($product)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->whereIn('JTD.product_id',$product);

                        }elseif (!empty($transID)) {

                            $platforms = $platforms->select(
                                            DB::raw("SUM(JT.total_amount) as total_order"), 
                                            DB::raw("SUM(JT.gst_total) as gst_total"), 'JT.buyer_username')
                                        ->whereIn('JT.id',$transID);
                        }else{

                            $platforms = $platforms->select(
                                            DB::raw("SUM(JT.total_amount) as total_order"), 
                                            DB::raw("SUM(JT.gst_total) as gst_total"), 'JT.buyer_username');
                        }

                    $platforms = $platforms->first();

                    break;
                
                default:

                    $platforms = DB::table('jocom_transaction AS JT')
                        ->where('JT.buyer_username','LIKE', "%".$platform."%")
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);
                        
                        if (!empty($supplier)) {
                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JTD.seller_id','=',$supplier);

                        }elseif (!empty($category)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JP.category','LIKE', "%".$category."%")
                                        ->groupBy('JTD.product_id')
                                        ->orderby('JTD.product_id', 'DESC');

                        }elseif (!empty($product)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->whereIn('JTD.product_id',$product);
                        }elseif (!empty($transID)) {

                            $platforms = $platforms->select(
                                            DB::raw("SUM(JT.total_amount) as total_order"), 
                                            DB::raw("SUM(JT.gst_total) as gst_total"), 'JT.buyer_username')
                                        ->whereIn('JT.id',$transID);
                        }else{

                            $platforms = $platforms->select(
                                            DB::raw("SUM(JT.total_amount) as total_order"), 
                                            DB::raw("SUM(JT.gst_total) as gst_total"), 'JT.buyer_username');
                        }

                    $platforms = $platforms->first();

                    break;
            }
            
            return $platforms;

      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public static function getPlatformrecordproduct($platform, $from,$to,$supplier,$category,$product){
        
       
        try{
            
            switch ($platform) {

                case 'Jocom':
                    $platforms = DB::table('jocom_transaction AS JT')
                        ->whereNotIn('JT.buyer_username', ['lazada','11Street', 'shopee','Qoo10','Astro Go Shop'])
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);
                        
                        if (!empty($supplier)) {
                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JTD.seller_id','=',$supplier);

                        }elseif (!empty($category)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JP.category','LIKE', "%".$category."%")
                                        ->groupBy('JP.id')
                                        ->orderby('JP.id', 'DESC');

                        }elseif (!empty($product)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->whereIn('JP.id',$product);

                        }else{

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username');
                        }

                    $platforms = $platforms->first();

                    break;
                
                default:

                    $platforms = DB::table('jocom_transaction AS JT')
                        ->where('JT.buyer_username','LIKE', "%".$platform."%")
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);
                        
                        if (!empty($supplier)) {
                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JTD.seller_id','=',$supplier);

                        }elseif (!empty($category)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->where('JP.category','LIKE', "%".$category."%")
                                        ->groupBy('JP.id')
                                        ->orderby('JP.id', 'DESC');

                        }elseif (!empty($product)) {

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username')
                                        ->whereIn('JP.id',$product);
                        }else{

                            $platforms = $platforms->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                                        ->select(
                                            DB::raw("SUM(JTD.total) as total_order"), 
                                            DB::raw("SUM(JTD.gst_amount) as gst_total"), 'JT.buyer_username');
                        }

                    $platforms = $platforms->first();

                    break;
            }
            
            return $platforms;

      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public static function getDayrecord($from,$to,$supplier,$category,$product,$transID){
        
       
        try{
            
            // day to day
        $array = array();
        $daytoday = array();

        $interval = new DateInterval('P1D');
        $format = 'Y-m-d';
        $realEnd = new DateTime($to);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($from), $interval, $realEnd);
        
        foreach($period as $date) { 
            $array[] = $date->format($format); 
        }
        array_pop($array);
        // print_r($array);die();
        foreach ($array as $key => $value) {

            $fr = $value." 00:00:00";
            $end = $value." 23:59:59";

            $days = DB::table('jocom_transaction AS JT')
                    ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                    ->where('JT.invoice_no','!=','')
                    ->where('JT.transaction_date','>=',$fr)
                    ->where('JT.transaction_date','<=',$end);

            if (!empty($supplier)) {

                $days = $days->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    ->select(
                        DB::raw("SUM(JTD.total) as total_order"), 
                        DB::raw("SUM(JTD.gst_amount) as gst_total"))
                    ->where('JTD.seller_id','=',$supplier);
               
            }elseif (!empty($category)) {
                
                $days = $days->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                    ->select(
                        DB::raw("SUM(JTD.total) as total_order"), 
                        DB::raw("SUM(JTD.gst_amount) as gst_total"))
                    ->where('JP.category','LIKE', "%".$category."%")
                    ->groupBy('JTD.product_id')
                    ->orderby('JTD.product_id', 'DESC');
                    
                    
            }elseif (!empty($product)) {
                $days = $days->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                    ->select(
                        DB::raw("SUM(JTD.total) as total_order"), 
                        DB::raw("SUM(JTD.gst_amount) as gst_total"))
                    ->whereIn('JTD.product_id',$product);
            
            }elseif(!empty($transID)){

                $days = $days->select(
                        DB::raw("SUM(JT.total_amount) as total_order"), 
                        DB::raw("SUM(JT.gst_total) as gst_total"))->whereIn('JT.id',$transID);

            }else{

                $days = $days->select(
                        DB::raw("SUM(JT.total_amount) as total_order"), 
                        DB::raw("SUM(JT.gst_total) as gst_total"));
                    
      
            }
            
            $days = $days->first();

            $daytoday[] = array(
                "date" => $value,
                "total" => round($days->total_order + $days->gst_total,2),
                );

        }

        foreach($daytoday as $key=>$val){
           $result[substr($val['date'],-2)] += $val['total'];
        }

        return $result;

      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public static function getDayrecordproduct($from,$to,$supplier,$category,$product){
        
       
        try{
            
            // day to day
        $array = array();
        $daytoday = array();

        $interval = new DateInterval('P1D');
        $format = 'Y-m-d';
        $realEnd = new DateTime($to);
        $realEnd->add($interval);

        $period = new DatePeriod(new DateTime($from), $interval, $realEnd);
        
        foreach($period as $date) { 
            $array[] = $date->format($format); 
        }

        foreach ($array as $key => $value) {

            $fr = $value." 00:00:00";
            $end = $value." 23:59:59";

            $days = DB::table('jocom_transaction AS JT')
                    ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                    ->where('JT.invoice_no','!=','')
                    ->where('JT.transaction_date','>=',$fr)
                    ->where('JT.transaction_date','<=',$end);

            if (!empty($supplier)) {

                $days = $days->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                    ->select(
                        DB::raw("SUM(JTD.total) as total_order"), 
                        DB::raw("SUM(JTD.gst_amount) as gst_total"))
                    ->where('JTD.seller_id','=',$supplier);
               
            }elseif (!empty($category)) {
                
                $days = $days->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                    ->select(
                        DB::raw("SUM(JTD.total) as total_order"), 
                        DB::raw("SUM(JTD.gst_amount) as gst_total"))
                    ->where('JP.category','LIKE', "%".$category."%")
                    ->groupBy('JP.id')
                    ->orderby('JP.id', 'DESC');
                    
                    
            }elseif (!empty($product)) {
                $days = $days->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                    ->select(
                        DB::raw("SUM(JTD.total) as total_order"), 
                        DB::raw("SUM(JTD.gst_amount) as gst_total"))
                    ->whereIn('JP.id',$product);
            
            }else{

                $days = $days->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                    ->select(
                        DB::raw("SUM(JTD.total) as total_order"), 
                        DB::raw("SUM(JTD.gst_amount) as gst_total"));            
      
            }
            
            $days = $days->first();

            $daytoday[] = array(
                "date" => $value,
                "total" => round($days->total_order + $days->gst_total,2),
                );

        }

        foreach($daytoday as $key=>$val){
           $result[substr($val['date'],-2)] += $val['total'];
        }

        return $result;

      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public static function getStaterecord($from,$to,$supplier,$category,$product,$transID){
        
       
        try{
            
            $arr = array();

            $states = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);

            if (!empty($supplier)) {

                $states =  $states->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_state')
                            ->where('JTD.seller_id','=',$supplier);

            }elseif (!empty($category)) {

                $states =  $states->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_state')
                            ->where('JP.category','LIKE', "%".$category."%")
                            ->groupBy('JTD.product_id')
                            ->orderby('JTD.product_id', 'DESC');

            }elseif (!empty($product)) {
                
                $states = $states->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_state')
                            ->whereIn('JTD.product_id',$product);

            }elseif(!empty($transID)){

                $states = $states->select(DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"), 'JT.delivery_state')->whereIn('JT.id',$transID);

            }else{

                $states = $states->select(DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"), 'JT.delivery_state');

            }

            $states = $states->groupBy('JT.delivery_state')
                            ->orderby('JT.delivery_state', 'DESC')
                            ->get();

            return $states;

        }catch(Exception $ex){

          return $ex->getMessage();
        }
        
        
    }

    public static function getStaterecordproduct($from,$to,$supplier,$category,$product){
        
       
        try{
            
            $arr = array();

            $states = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);

            if (!empty($supplier)) {

                $states =  $states->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_state')
                            ->where('JTD.seller_id','=',$supplier);

            }elseif (!empty($category)) {

                $states =  $states->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_state')
                            ->where('JP.category','LIKE', "%".$category."%")
                            ->groupBy('JP.id')
                            ->orderby('JP.id', 'DESC');

            }elseif (!empty($product)) {
                
                $states = $states->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_state')
                            ->whereIn('JP.id',$product);

            }else{

                $states = $states->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_state');

            }

            $states = $states->groupBy('JT.delivery_state')
                            ->orderby('JT.delivery_state', 'DESC')
                            ->get();

            return $states;

        }catch(Exception $ex){

          return $ex->getMessage();
        }
        
        
    }

    public static function getStatusrecord($status,$from,$to,$supplier,$category,$product,$transID){
        
       
        try{

                $statuses = DB::table('jocom_transaction AS JT')
                        ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id')
                        ->where('JT.invoice_no','!=','')
                        ->where('LT.status','=', $status)
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);

                if (!empty($supplier)) {

                    $statuses = $statuses->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'LT.status')
                        ->where('JTD.seller_id','=',$supplier);

                }elseif (!empty($category)) {

                    $statuses = $statuses->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'LT.status')
                        ->where('JP.category','LIKE', "%".$category."%")
                        ->groupBy('JTD.product_id')
                        ->orderby('JTD.product_id', 'DESC');

                }elseif (!empty($product)) {

                    $statuses = $statuses->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'LT.status')
                        ->whereIn('JTD.product_id',$product);

                }elseif(!empty($transID)){

                    $statuses = $statuses->select(DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"), 'LT.status')->whereIn('JT.id',$transID);

                }else{

                    $statuses = $statuses->select(DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"), 'LT.status');
                }

                $statuses = $statuses->first();
                
                return $statuses;

        }catch(Exception $ex){

          return $ex->getMessage();
        }
        
        
    }

    public static function getStatusrecordproduct($status,$from,$to,$supplier,$category,$product){
        
       
        try{

                $statuses = DB::table('jocom_transaction AS JT')
                        ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id')
                        ->where('JT.invoice_no','!=','')
                        ->where('LT.status','=', $status)
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to);

                if (!empty($supplier)) {

                    $statuses = $statuses->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'LT.status')
                        ->where('JTD.seller_id','=',$supplier);

                }elseif (!empty($category)) {

                    $statuses = $statuses->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'LT.status')
                        ->where('JP.category','LIKE', "%".$category."%")
                        ->groupBy('JP.id')
                        ->orderby('JP.id', 'DESC');

                }elseif (!empty($product)) {

                    $statuses = $statuses->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'LT.status')
                        ->whereIn('JP.id',$product);

                }else{

                    $statuses = $statuses->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'LT.status');
                }

                $statuses = $statuses->first();
                
                return $statuses;

        }catch(Exception $ex){

          return $ex->getMessage();
        }
        
        
    }

    public static function getPostcoderecord($from,$to,$supplier,$category,$product,$transID){
        
       
        try{    

                $postcodes = DB::table('jocom_transaction AS JT')
                            ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                            ->where('JT.invoice_no','!=','')
                            ->where('JT.transaction_date','>=',$from)
                            ->where('JT.transaction_date','<=',$to);

                if (!empty($supplier)) {

                    $postcodes = $postcodes->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')                            
                            ->where('JTD.seller_id','=',$supplier)                           
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_postcode')
                            ->groupBy('JTD.product_id')
                            ->orderby('JTD.product_id', 'DESC');     

                }elseif (!empty($category)) {

                    $postcodes = $postcodes->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')                           
                            ->where('JP.category','LIKE', "%".$category."%")                 
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_postcode')
                            ->groupBy('JTD.product_id')
                            ->orderby('JTD.product_id', 'DESC');                            

                }elseif (!empty($product)) {

                    $postcodes = $postcodes->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')                           
                            ->whereIn('JTD.product_id',$product)       
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_postcode');
                           
                }elseif(!empty($transID)){

                    $postcodes = $postcodes->select(DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"), 'JT.delivery_postcode')->whereIn('JT.id',$transID)       ;
                }else{

                    $postcodes = $postcodes->select(DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"), 'JT.delivery_postcode');
                }

                $postcodes = $postcodes->groupBy('JT.delivery_postcode')
                                    ->orderby('JT.delivery_postcode', 'DESC')
                                    ->get();
                return $postcodes;

      }catch(Exception $ex){

          return $ex->getMessage();
      }    
        
    }

    public static function getPostcoderecordproduct($from,$to,$supplier,$category,$product){
        
       
        try{    

                $postcodes = DB::table('jocom_transaction AS JT')
                            ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                            ->where('JT.invoice_no','!=','')
                            ->where('JT.transaction_date','>=',$from)
                            ->where('JT.transaction_date','<=',$to);

                if (!empty($supplier)) {

                    $postcodes = $postcodes->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')                            
                            ->where('JTD.seller_id','=',$supplier)                           
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_postcode')
                            ->groupBy('JP.id')
                            ->orderby('JP.id', 'DESC');   

                }elseif (!empty($category)) {

                    $postcodes = $postcodes->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')                           
                            ->where('JP.category','LIKE', "%".$category."%")                   
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_postcode')
                            ->groupBy('JP.id')
                            ->orderby('JP.id', 'DESC');
                            

                }elseif (!empty($product)) {

                    $postcodes = $postcodes->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')                           
                            ->whereIn('JP.id',$product)       
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_postcode');
                           
                }else{

                    $postcodes = $postcodes->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id') 
                            ->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JT.delivery_postcode');
                }

                $postcodes = $postcodes->groupBy('JT.delivery_postcode')
                                    ->orderby('JT.delivery_postcode', 'DESC')
                                    ->get();
                return $postcodes;

      }catch(Exception $ex){

          return $ex->getMessage();
      }    
        
    }

    public static function getProductrecord($from,$to,$supplier,$category,$product,$transID){
        
       
        try{
                $products = DB::table('jocom_transaction AS JT')
                            ->leftJoin('jocom_transaction_details AS JTD','JTD.transaction_id','=','JT.id')
                            ->leftJoin('jocom_products AS JP','JP.id','=','JTD.product_id')
                            ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                            ->where('JT.invoice_no','!=','')
                            ->where('JT.transaction_date','>=',$from)
                            ->where('JT.transaction_date','<=',$to);

                if (!empty($supplier)) {

                    $products = $products->where('JTD.seller_id','=',$supplier)->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 

                }elseif (!empty($category)) {

                    $products = $products->where('JP.category','LIKE', "%".$category."%")->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 
                    
                }elseif (!empty($product)) {

                    $products = $products->whereIn('JTD.product_id',$product)->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 

                }elseif(!empty($transID)){

                    $products = $products->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name')->whereIn('JT.id',$transID); 

                }else{

                    $products = $products->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 

                }  

                $products = $products->groupBy('JTD.product_id')
                            ->orderby('JTD.product_id', 'DESC')
                            ->get();

                return $products;      

      }catch(Exception $ex){

          return $ex->getMessage();
      }
              
    }

    public static function getProductrecordproduct($from,$to,$supplier,$category,$product){
        
       
        try{
                $products = DB::table('jocom_transaction AS JT')
                            ->leftJoin('jocom_transaction_details AS JTD','JTD.transaction_id','=','JT.id')
                            ->leftJoin('jocom_products AS JP','JP.id','=','JTD.product_id')
                            ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                            ->where('JT.invoice_no','!=','')
                            ->where('JT.transaction_date','>=',$from)
                            ->where('JT.transaction_date','<=',$to);

                if (!empty($supplier)) {

                    $products = $products->where('JTD.seller_id','=',$supplier)->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 

                }elseif (!empty($category)) {

                    $products = $products->where('JP.category','LIKE', "%".$category."%")->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 
                    
                }elseif (!empty($product)) {

                    $products = $products->whereIn('JP.id',$product)->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 

                }else{

                    $products = $products->select(DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"), 'JP.name'); 

                }  

                $products = $products->groupBy('JP.id')
                            ->orderby('JP.id', 'DESC')
                            ->get();

                return $products;      

      }catch(Exception $ex){

          return $ex->getMessage();
      }
              
    }

    public static function getMonthlyrecord($from,$to,$supplier,$category,$product,$transID){
        
       
        try{

            $months = array();

            while (strtotime($from) <= strtotime($to)) {
                $months[] = array(               
                    'from_date' => date('Y-m-d', strtotime($from)),
                    'end_date' => date('Y-m-t', strtotime($from)),
                    'month_name' => date('M-Y', strtotime($from)),
                );

                $from = date('d M Y', strtotime($from. '+ 1 month'));
            }
        // return $months;

            $monthlylist = array();

            foreach ($months as $key => $value) {

                $fr = $value['from_date']." 00:00:00";
                $end = $value['end_date']." 23:59:59";
                
                $month = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->wherebetween('JT.transaction_date', [$fr, $end]);

                if (!empty($supplier)) {

                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JTD.seller_id','=',$supplier);
                   
                }elseif (!empty($category)) {
                    
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JP.category','LIKE', "%".$category."%")
                        ->groupBy('JTD.product_id')
                        ->orderby('JTD.product_id', 'DESC');                   
                        
                }elseif (!empty($product)) {
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->whereIn('JTD.product_id',$product);
                
                }elseif(!empty($transID)){

                    $month = $month->select(
                            DB::raw("SUM(JT.total_amount) as total_order"), 
                            DB::raw("SUM(JT.gst_total) as gst_total"))->whereIn('JT.id',$transID);

                }else{

                    $month = $month->select(
                            DB::raw("SUM(JT.total_amount) as total_order"), 
                            DB::raw("SUM(JT.gst_total) as gst_total"));
                        
          
                }
                
                $month = $month->first();
                
                $monthlylist[] = array(
                    "from_date" => $fr,
                    "end_date" => $end,
                    "month_name" => $value['month_name'],
                    "total_order" => round($month->total_order + $month->gst_total,2),
                    );

            }

        return $monthlylist;

      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public static function getMonthlyrecordproduct($from,$to,$supplier,$category,$product){
        
       
        try{

            $months = array();

            while (strtotime($from) <= strtotime($to)) {
                $months[] = array(               
                    'from_date' => date('Y-m-d', strtotime($from)),
                    'end_date' => date('Y-m-t', strtotime($from)),
                    'month_name' => date('M-Y', strtotime($from)),
                );

                $from = date('d M Y', strtotime($from. '+ 1 month'));
            }
        // return $months;

            $monthlylist = array();

            foreach ($months as $key => $value) {

                $fr = $value['from_date']." 00:00:00";
                $end = $value['end_date']." 23:59:59";
                
                $month = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->wherebetween('JT.transaction_date', [$fr, $end]);

                if (!empty($supplier)) {

                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JTD.seller_id','=',$supplier);
                   
                }elseif (!empty($category)) {
                    
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JP.category','LIKE', "%".$category."%")
                        ->groupBy('JP.id')
                        ->orderby('JP.id', 'DESC');  
                        
                        
                }elseif (!empty($product)) {
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->whereIn('JP.id',$product);
                
                }else{

                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"));
                        
          
                }
                
                $month = $month->first();
                
                $monthlylist[] = array(
                    "from_date" => $fr,
                    "end_date" => $end,
                    "month_name" => $value['month_name'],
                    "total_order" => round($month->total_order + $month->gst_total,2),
                    );

            }

        return $monthlylist;

      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public static function getRegionrecord($from,$to,$supplier,$category,$product,$transID){
        
       
        try{
                $c_states = DB::table('jocom_country_states')->where('status','1')->get();
                
                
                // $c_states = DB::table('jocom_country_states AS JCS')
                // ->leftJoin('jocom_region AS JR', 'JCS.id','=', 'JTD.product_id')->where('JCS.status','1')
                // ->where('JCS.status','1')->get();
             
                $list = array();
                foreach ($c_states as $key => $value) {

                    $month = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled','refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to)
                        ->where('JT.delivery_state','LIKE',"%".$value->name."%");

                    if (!empty($supplier)) {

                        $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->select(
                                DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"),'JT.delivery_state'
                            )
                            ->where('JTD.seller_id','=',$supplier);
                       
                    }elseif (!empty($category)) {
                        
                        $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(
                                DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"),'JT.delivery_state'
                            )
                            ->where('JP.category','LIKE', "%".$category."%")
                            ->groupBy('JTD.product_id')
                            ->orderby('JTD.product_id', 'DESC'); 
                            
                            
                    }elseif (!empty($product)) {
                        $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(
                                DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"),'JT.delivery_state'
                            )
                            ->whereIn('JTD.product_id',$product);
                    
                    }elseif(!empty($transID)){

                        $month = $month->select(
                                DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"),'JT.delivery_state'
                            )
                        ->whereIn('JT.id',$transID);

                    }else{

                        $month = $month->select(
                                    DB::raw("ROUND(SUM(JT.total_amount) + SUM(JT.gst_total),2) AS total_order"),'JT.delivery_state'
                                );                     
              
                    }
                    
                    $month = $month->groupBy('JT.delivery_state')->first();

                    if (isset($month)) {
                        

                        $name = DB::table('jocom_region')->where('id', $value->region_id)->first();
              
                        array_push($list, array(
                            "total_order"=>$month->total_order,
                            "state"=>$month->delivery_state,
                            "region" =>$value->region_id,
                            "region_name"=>$name->region
                            ));
                       
                    }
                    
                }

                $newArray = array();

                foreach ($list as $key=>$v ) 
                {
                    if ( !isset($newArray[$v['region']]) ) {

                        $newArray[$v['region']]['region_name'] = $v['region_name'];

                    }

                    $newArray[$v['region']]['total_order'] += $v['total_order'];

                }
            
               return $newArray;
        

      }catch(Exception $ex){

          return $ex->getMessage();
      }
              
    }

    public static function getRegionrecordproduct($from,$to,$supplier,$category,$product){
        
       
        try{
                $c_states = DB::table('jocom_country_states')->where('status','1')->get();
                $list = array();
                foreach ($c_states as $key => $value) {

                    $month = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->where('JT.transaction_date','>=',$from)
                        ->where('JT.transaction_date','<=',$to)
                        ->where('JT.delivery_state','LIKE',"%".$value->name."%");

                    if (!empty($supplier)) {

                        $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(
                                DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"),'JT.delivery_state'
                            )
                            ->where('JTD.seller_id','=',$supplier);
                       
                    }elseif (!empty($category)) {
                        
                        $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(
                                DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"),'JT.delivery_state'
                            )
                            ->where('JP.category','LIKE', "%".$category."%")
                            ->groupBy('JP.id')
                            ->orderby('JP.id', 'DESC'); 
                            
                            
                    }elseif (!empty($product)) {
                        $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(
                                DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"),'JT.delivery_state'
                            )
                            ->whereIn('JP.id',$product);
                    
                    }else{

                        $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->select(
                                DB::raw("ROUND(SUM(JTD.total) + SUM(JTD.gst_amount),2) AS total_order"),'JT.delivery_state'
                            );         
              
                    }
                    
                    $month = $month->groupBy('JT.delivery_state')->first();

                    if (isset($month)) {

                        $name = DB::table('jocom_region')->where('id', $value->region_id)->first();

                        array_push($list, array(
                            "total_order"=>$month->total_order,
                            "state"=>$month->delivery_state,
                            "region" =>$value->region_id,
                            "region_name"=>$name->region
                            ));
                    }
                    
                }

                $newArray = array();

                foreach ($list as $key=>$v ) 
                {
                    if ( !isset($newArray[$v['region']]) ) {

                        $newArray[$v['region']]['region_name'] = $v['region_name'];

                    }

                    $newArray[$v['region']]['total_order'] += $v['total_order'];

                }

               return $newArray;
        

      }catch(Exception $ex){

          return $ex->getMessage();
      }
              
    }

    public static function getQuaterlyrecord($from,$to,$supplier,$category,$product,$transID){
        
       
        try{

            $months = array();

            while (strtotime($from) <= strtotime($to)) {
                $months[] = array(               
                    'from_date' => date('Y-m-d', strtotime($from)),
                    'end_date' => date('Y-m-t', strtotime($from)),
                    'month_name' => date('M-Y', strtotime($from)),
                );

                $from = date('d M Y', strtotime($from. '+ 1 month'));
            }
        // return $months;
        
        // echo "<pre>";
        // print_r($months);
        // echo "</pre>";
        // die();

            $monthlylist = array();

            foreach ($months as $key => $value) {

                $fr = $value['from_date']." 00:00:00";
                $end = $value['end_date']." 23:59:59";
                
                $month = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->wherebetween('JT.transaction_date', [$fr, $end]);

                if (!empty($supplier)) {

                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JTD.seller_id','=',$supplier);
                   
                }elseif (!empty($category)) {
                    
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JP.category','LIKE', "%".$category."%")
                        ->groupBy('JP.id')
                        ->orderby('JP.id', 'DESC');  
                        
                        
                }elseif (!empty($product)) {
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->whereIn('JP.id',$product);
                
                }else{

                    // $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                    //     ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                    //     ->select(
                    //         DB::raw("SUM(JTD.total) as total_order"), 
                    //         DB::raw("SUM(JTD.gst_amount) as gst_total"));
                            
                             $month = $month->select(
                            DB::raw("SUM(JT.total_amount) as total_order"), 
                            DB::raw("SUM(JT.gst_total) as gst_total"));
                        
          
                }
                
                $month = $month->first();
                
                $monthlylist[] = array(
                    "from_date" => $fr,
                    "end_date" => $end,
                    "month_name" => $value['month_name'],
                    "total_order" => round($month->total_order + $month->gst_total,2),
                    );

            }

            $quater = array_chunk($monthlylist, 3);
            $quater_name = array("First Quater","Second Quater","Third Quater","Forth Quater");

            $result = array();
            foreach($quater as $key=>$value)
            {
                $res_total = array_sum(array_column($value,'total_order'));
            
                $res_name = array_column($value,'month_name');

                array_push($result, array(
                    "total_order"=>$res_total,
                     "month_name"=>$quater_name[$key],
                ));

            }

        return $result;
     

        }catch(Exception $ex){

          return $ex->getMessage();
        }       
    }

    public static function getQuaterlyrecordproduct($from,$to,$supplier,$category,$product){
        
       
        try{

            $months = array();

            while (strtotime($from) <= strtotime($to)) {
                $months[] = array(               
                    'from_date' => date('Y-m-d', strtotime($from)),
                    'end_date' => date('Y-m-t', strtotime($from)),
                    'month_name' => date('M-Y', strtotime($from)),
                );

                $from = date('d M Y', strtotime($from. '+ 1 month'));
            }
        // return $months;

            $monthlylist = array();

            foreach ($months as $key => $value) {

                $fr = $value['from_date']." 00:00:00";
                $end = $value['end_date']." 23:59:59";
                
                $month = DB::table('jocom_transaction AS JT')
                        ->whereIn('JT.status', ['completed', 'cancelled', 'refund'])
                        ->where('JT.invoice_no','!=','')
                        ->wherebetween('JT.transaction_date', [$fr, $end]);

                if (!empty($supplier)) {

                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JTD.seller_id','=',$supplier);
                   
                }elseif (!empty($category)) {
                    
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->where('JP.category','LIKE', "%".$category."%")
                        ->groupBy('JP.id')
                        ->orderby('JP.id', 'DESC');  
                        
                        
                }elseif (!empty($product)) {
                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"))
                        ->whereIn('JP.id',$product);
                
                }else{

                    $month = $month->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=', 'JT.id')
                        ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                        ->select(
                            DB::raw("SUM(JTD.total) as total_order"), 
                            DB::raw("SUM(JTD.gst_amount) as gst_total"));
                        
          
                }
                
                $month = $month->first();
                
                $monthlylist[] = array(
                    "from_date" => $fr,
                    "end_date" => $end,
                    "month_name" => $value['month_name'],
                    "total_order" => round($month->total_order + $month->gst_total,2),
                    );

            }

            $quater = array_chunk($monthlylist, 3);
            $quater_name = array("First Quater","Second Quater","Third Quater","Forth Quater");

            $result = array();

                foreach($quater as $key=>$value)
                {
                    $res_total = array_sum(array_column($value,'total_order'));
                
                    $res_name = array_column($value,'month_name');

                    array_push($result, array(
                        "total_order"=>$res_total,
                        "month_name"=>$quater_name[$key],
                    ));

                }

            return $result;

        }catch(Exception $ex){

          return $ex->getMessage();
        }    
    }
}
?>