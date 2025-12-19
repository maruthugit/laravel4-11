<?php

class Warehouse extends Eloquent  {

    protected $table = 'jocom_warehouse_products';


    public static function isExists($value='')
    {
    	$response = 0;
    	
    	$result = Warehouse::where('product_id','=',$value)
    			  ->get();
    	if(count($result) >0){
    		$response = 1;
    	}
    	return $response;
    }

    public static function isExistsBase($value=''){
    	$response = 0;

    	$result = DB::table("jocom_warehouse_productslinks")
    				  ->where('parent_product_id','=',$value)
    				  ->get();
    		if(count($result)>0){
    			$response = 1;
    		}

    	return $response;

    }

    public static function isExistsBaselink($value=''){
        $response = 0;

        $result = DB::table("jocom_warehouse_products_baselinks")
                      ->where('product_id','=',$value)
                      ->get();
            if(count($result)>0){
                $response = 1;
            }

        return $response;

    }


    public static function isExistsBasic($value=''){
    	$response = 0;

    	$result = DB::table("jocom_warehouse_productslinks")
    				  ->where('product_id','=',$value)
    				  ->get();
    		if(count($result)>0){
    			$response = 1;
    		}

    	return $response;

    }

    public static function isExistsproductbase($value=''){
        $response = 0;

        $result = DB::table("jocom_warehouse_products_baselinks")
                      ->where('variant_product_id','=',$value)
                      ->where('product_id','<>',$value)
                      ->where('status','=',1)
                      ->get();
            if(count($result)>0){
                $response = 1;
            }
            
            if($response == 0){

                $result01 = DB::table("jocom_warehouse_productslinks")
                      ->where('product_id','=',$value)
                      ->get();

                 if(count($result01)>0){
                    $response = 1;
                 }     
            }

        return $response;

    }
    
    public static function getProductID($warehouseid){

      $productid = 0; 
      $refno = 0; 
      $active = 0; 

      $result = DB::table("jocom_warehouse_products")
                    ->where('id','=',$warehouseid)
                    ->first();
        if(count($result) > 0){

          $productid = $result->product_id; 
          $refno = $result->ref_no; 
          $active = $result->active; 
          $stinhand = $result->stockin_hand; 

        }

        return  array('productid' => $productid,
                      'refno'     => $refno,
                      'stockinhand'   => $stinhand,
                      'active'    => $active

                       );

    }

    public static function getProductlinkstock($productid){

    	$parentstock = 0; 
    	$basicstock = 0;
    	$quantity = 0;


    	$result = DB::table('jocom_warehouse_productslinks AS JWP')
    				    ->select('parent_product_id','quantity','product_id')
                                    ->where('JWP.parent_product_id','=',$productid)
                                    ->first();	

    			$parentid = $result->parent_product_id;
                $basicid = $result->product_id;
    			$quantity = $result->quantity;

    			$parentstock = self::getStockinhand($parentid);
    			$basicstock	 = self::getStockinhand($basicid);


    	return array('basicstock' 	        => $basicstock,
                     'basicStockProductID' 	=> $basicid,
                     'parentstock'	        => $parentstock,
                     'parentStockProductID'	=> $parentid,
                     'quantity'		        => $quantity,
                    );		

    }

    public static function getStockinhand($productid){

    	$stockinhand = 0;

    	$result = Warehouse::where('product_id','=',$productid)
    			  	->select('stockin_hand')
    			  	->first();

    	if(count($result)>0){
    		$stockinhand = $result->stockin_hand;
    	}

    	return $stockinhand;

    }
    
    public static function getStockinhandReserved($productid){

        $stockinhand = 0;
        $reservedstockinhand = 0;

        $result = Warehouse::where('product_id','=',$productid)
                    ->first();

        if(count($result)>0){
            $stockinhand            = $result->stockin_hand;
            $reservedstockinhand    = $result->reserved_in_hand;
            $wareID                 = $result->id;
        }
     
    //  echo '<pre>';
    //  print_r($result);
    //  echo '</pre>';

        return array('stockinhand'          => $stockinhand,
                     'reservedstockinhand'  => $reservedstockinhand,
                     'warehouseID'          => $wareID,

                     );



    }
    
    public static function getRefernumber($productid){

        $refno = 0; 

        $result = DB::table('jocom_warehouse_productslinks')
                      ->where('product_id','=',$productid)
                      ->first();

        if(count($result)>0){

            $refno = $result->ref_no;
        } 
        else
        {
           $result_01 = DB::table('jocom_warehouse_products_baselinks')
                          ->where('variant_product_id','=',$productid)
                          ->first(); 
            if(count($result_01)>0){

                $refno = $result_01->ref_no;

            }
        }

       return $refno; 
    }
    
    /*
     * Sorter Function 
     */

    public static function getStockavailable($productid,$quantityOrder,$type){
        $availableStock = 0;
        $is_Exists = 0; 
        $stockExist = 0;
        $stockEnough = 0;
        $totalStockInHandAvailable = 0;
        $totalstock = 0;
        $totalRequired = 0;
        $quantity = 0;
        $basicrefarray = array(); 


        $refno = 0;
        $basic = array();
        $base = array();

        
        // print_r($productid);
        $is_Exists = self::isExists($productid);
        
        // print_r($is_Exists);
        
        $reservedData = self::getBasicreservedvalue($productid);
        // echo $productid;
        // echo "<pre>";
        // print_r($reservedData);
        // echo "</pre>";
        
        if($is_Exists > 0){

             $productresult = Warehouse::where('product_id','=',$productid)
                                        ->first();
             if(count($productresult)>0){

                $refno       = $productresult->ref_no; 
                $stockinhand = $productresult->stockin_hand; 

             //   $totalstock = $stockinhand;

                // $temparr = array('warehouseID'      => $productresult->id,
                //                  'productid'        => $productid,
                //                  'stockinhand'      => $stockinhand,
                //                  'reservedstock'    => $productresult->reserved_in_hand,
                //                  );
                // array_push($basic,$temparr);
                // echo 'RefNo'.$refno;
            
                $tempcarray = array('productid' => $productid, );                   
                array_push($basicrefarray, $tempcarray);
                //If REF Number YES
                if($refno >0){
                    //If REF Number YES - Start 
                    $LinkBasicProductData = DB::table('jocom_warehouse_productslinks AS JWP')
                                                ->select('parent_product_id','quantity','product_id')
                                                ->where('JWP.parent_product_id','=',$productid)
                                                ->first();  
                    
                    // if(empty($LinkBasicProductData)){
                    //   $LinkBasicProductData = DB::table('jocom_warehouse_productslinks AS JWP')
                    //                             ->select('parent_product_id','quantity','product_id')
                    //                             ->where('JWP.product_id','=',$productid)
                    //                             ->first();  
                      
                    // }
                    
                   // $LinkBasicProductData  = self::getProductlinkstock($productid);
                    $basicProdData = self::getStockinhandReserved($LinkBasicProductData->product_id);
                    $baseProdData = self::getStockinhandReserved($LinkBasicProductData->parent_product_id); 
                    
                    
                    // echo "<pre>";
                    // echo "BASIC";
                    // print_r($LinkBasicProductData);
                    // print_r($basicProdData);
                    // print_r($baseProdData);
                    // echo "</pre>";

                    $refproductID = $LinkBasicProductData->product_id;

                    $quantity = $LinkBasicProductData->quantity;

                    $totalStockInHandAvailable = $basicProdData['stockinhand'] + ( $baseProdData['stockinhand'] * $LinkBasicProductData->quantity );
                    
                    
                    
                    
                    $totalAvailable =  $totalStockInHandAvailable - $basicProdData['reservedstockinhand']; 
                    $totalstock = $totalstock + $totalAvailable;
                    // echo "quantityOrder 1 :". $quantityOrder;
                    // echo "<pre>";
                    // print_r($LinkBasicProductData);
                    // echo "</pre>";
                    
                    
                    
                    $totalRequired = $quantityOrder * $LinkBasicProductData->quantity;
                   
                    // echo "totalStockInHandAvailable 1 :".$totalStockInHandAvailable;
                    // echo "totalRequired 1 :". $totalRequired;
                    
                    if($LinkBasicProductData->quantity == 0 ){
                       $tempstock = $totalAvailable;

                    }
                    else
                    {
                      $tempstock = round($totalAvailable/$LinkBasicProductData->quantity);
                    }
                    
                    $temparr = array('productid'     => $LinkBasicProductData->product_id,
                                     'baseproductid' => $LinkBasicProductData->parent_product_id,
                                     'stockinhand' => $tempstock, 
                                     'reservedstock'  => $basicProdData['reservedstockinhand'], 
                                 );
                    array_push($basic,$temparr);

                    if($totalStockInHandAvailable >= $totalRequired){
                        
                            // echo "totalStockInHandAvailable 2:".$totalStockInHandAvailable;
                            // echo "totalRequired 2:".$totalRequired;


                            $stockExist = 1;
                            $stockEnough = 1;

                            $result = array(
                                "products"           => $basic,    
                                "stockExist"         => $stockExist,
                                "stockEnough"        => $stockEnough,
                                "refno"              => $refno,
                                "stockRequired"      => $totalRequired,
                                "stockAvailable"     => $totalAvailable,
                                "reservedData"  => $reservedData
                             );

                    }
                    else{

                            
                            $baserefarray = array(); 
                            $full = 0; 
                            $stockExist = 1;

                            $basicRefdata = DB::table('jocom_warehouse_productslinks')
                                                ->where('ref_no','=',$refno)
                                                ->where('product_id','!=',$refproductID)
                                                ->get(); 
                           
                                                    
                            foreach ($basicRefdata as $basicvalue) {

                                 $basicStockInHandAvailable = 0;
                                 $totalAvailablebasic = 0; 
                                
                                //echo 'ProD'.$basicvalue->product_id;
                                $tempbasicarray = array('productid' => $basicvalue->parent_product_id, );                   
                                array_push($basicrefarray, $tempbasicarray);

                                $basicData = self::getStockinhandReserved($basicvalue->product_id);
                                $baseLinksData = self::getStockinhandReserved($basicvalue->parent_product_id); 

                                $basicStockInHandAvailable = $basicData['stockinhand'] + ( $baseLinksData['stockinhand'] * $basicvalue->quantity );

                                $totalAvailablebasic =  $basicStockInHandAvailable - $basicData['reservedstockinhand']; 
                                
                                $totalstock = $totalstock + $totalAvailablebasic; 
                                
                                if($basicvalue->quantity == 0 ){
                                   $tempstockbasic = $totalAvailablebasic;
            
                                }
                                else
                                {
                                  $tempstockbasic = round($totalAvailablebasic/$basicvalue->quantity);
                                }

                                $temparr = array(
                                     'productid'   => $basicvalue->product_id,
                                     'baseproductid' => $basicvalue->parent_product_id,
                                     'stockinhand' => $tempstockbasic,  
                                     'reservedstock'  => $basicData['reservedstockinhand'], 
                                 );
                                array_push($basic,$temparr);

                                if($totalstock>=$totalRequired){
                                        $stockEnough = 1;
                                         $result = array(
                                            "products"           => $basic,    
                                            "stockExist"         => $stockExist,
                                            "stockEnough"        => $stockEnough,
                                            "refno"              => $refno,
                                            "stockRequired"      => $totalRequired, //round($totalRequired/$quantity,1),
                                            "stockAvailable"     => $totalstock, //round($totalstock/$quantity,2),
                                            "reservedData"  => $reservedData
                                         );
                                    break;
                                }

                              //  $totalRequired = $totalRequired + $totalAvailablebasic;
                    
                            }   

                            if($stockEnough == 0){

                                $baseRefdata = DB::table('jocom_warehouse_products_baselinks')
                                                ->where('ref_no','=',$refno)
                                                ->whereNotIn('variant_product_id',$basicrefarray)
                                                ->get(); 

                                foreach ($baseRefdata as  $basevalue) {
                                   $baseStockInHandAvailable = 0;
                                   $totalAvailablebase = 0; 
                                   
                                   $baseLinksData_01 = self::getStockinhandReserved($basevalue->variant_product_id);     


                                  // $baseStockInHandAvailable =  ( $baseLinksData_01['stockinhand'] * $quantity );
                                  // $totalAvailablebase =  $baseStockInHandAvailable - ($baseLinksData_01['reservedstockinhand'] * $quantity);
                                   $baseStockInHandAvailable =  ( $baseLinksData_01['stockinhand'] );
                                   $totalAvailablebase =  $baseStockInHandAvailable - ($baseLinksData_01['reservedstockinhand']);  
                                
                                    $totalstock = $totalstock + $totalAvailablebase; 

                                    $temparr = array(
                                         'productid'   => $basevalue->variant_product_id,
                                         'baseproductid' => $basevalue->product_id,
                                         'stockinhand' => $totalAvailablebase, 
                                         'reservedstock'  => $baseLinksData_01['reservedstockinhand'],  
                                     );                 
                                     array_push($basic,$temparr);    

                                     if($totalstock>=$totalRequired){
                                        
                                        $stockEnough = 1;
                                         $result = array(
                                            "products"           => $basic,    
                                            "stockExist"         => $stockExist,
                                            "stockEnough"        => $stockEnough,
                                            "refno"              => $refno,
                                            "stockRequired"      => $totalRequired,
                                            "stockAvailable"     => $totalstock,
                                            "reservedData"  => $reservedData );
                                            break;
                                        }

                                } 

                            }

                            if($stockEnough == 0){

                                $result = array(
                                        "products"           => $basic,    
                                        "stockExist"         => $stockExist,
                                        "stockEnough"        => $stockEnough,
                                        "refno"              => $refno,
                                        "stockRequired"      => $totalRequired, //round($totalRequired/$quantity,1),
                                        "stockAvailable"     => $totalstock, //round($totalstock/$quantity,2),
                                        "reservedData"  => $reservedData
                                    );

                            }

                    }

                    //If REF Number YES - End 

                }
                else{
                    //If REF Number NO - Start 
                      
                     $stock_inHand = 0; 
                     $stockinhand = $productresult->stockin_hand; 
                     $ProdData = self::getStockinhandReserved($productid);
                     $stock_inHand = $stockinhand - $ProdData['reservedstockinhand'];
                     $temparr = array(
                                     'productid'   => $productid,
                                     'baseproductid' => 0,
                                     'stockinhand' => $stock_inHand,  
                                     'reservedstock'  => $ProdData['reservedstockinhand'],  
                                 );
                    array_push($basic,$temparr);

                     if($stock_inHand>=$quantityOrder){

                            $stockExist = 1;
                            $stockEnough = 1;

                            $result = array(
                                "products"           => $basic,    
                                "stockExist"         => $stockExist,
                                "stockEnough"        => $stockEnough,
                                "refno"              => $refno,
                                "stockRequired"      => $quantityOrder,
                                "stockAvailable"     => $stock_inHand,
                                "reservedData"  => $reservedData
                             );


                    }
                    else{

                            $stockExist = 1;

                            $result = array(
                                "products"           => $basic,    
                                "stockExist"         => $stockExist,
                                "stockEnough"        => $stockEnough,
                                "refno"              => $refno,
                                "stockRequired"      => $quantityOrder,
                                "stockAvailable"     => $stock_inHand,
                                "reservedData"  => $reservedData
                             );
                    }

                    //If REF Number NO - End 
                }

 
             }     

        }
        else{
            // NO PRODUCT FOUND 

            $temparr = array(
                             'productid'   => 0,
                             'baseproductid' => 0,
                             'stockinhand' => 0, 
                             'reservedstock'  => 0,   
                         );
            array_push($basic,$temparr);     

            $result = array(
                "products"           => $basic,    
                "stockExist"         => $stockExist,
                "stockEnough"        => $stockEnough,
                "refno"              => $refno,
                "stockRequired"      => $quantityOrder,
                "stockAvailable"     => $stockinhand,
                "reservedData"  => $reservedData
             );

        }

        // echo '<pre>';
        // print_r($result);
        // echo '</pre>';

       return $result;

    }
    
    /*
     * Sorter & Deducation Functions 
     */
     
     public static function getSorterdeducationOLD(){

        $product_warehouse_id = 0; 
        $totalreserved = 0; 
        $reservedid = 0; 
        

        $data = array(); 

        $startdate = Date('Y-m-d')." 00:00:00";
        $enddate   = Date('Y-m-d')." 23:59:59";

        $WarehouseReserved = DB::table('jocom_warehouse_product_reserved')
                          ->where('created_at','>=',$startdate)
                          ->where('created_at','<=',$enddate)
                          ->where('is_completed','=',0)
                          ->get(); 

          

          if(count($WarehouseReserved) > 0){
              
              foreach ($WarehouseReserved as $key => $value) {

                  $refno = 0; 
                  $currentstock = 0; 
                  $basicStockInHandAvailable = 0; 
                  $actualstock = 0;
                  $wholestock  = 0;
                  $decimal = 0;
                  $deduction = 0; 


                  $product_warehouse_id = $value->product_warehouse_id;
                  $totalreserved        = $value->total_reserved;
                  $reservedid           = $value->id;
                  $transaction_id = $value->transaction_id ;
                  
                //  echo $product_warehouse_id.'-'.$totalreserved.'-'.$reservedid.'<br>';

                  $data = self::getProductID($product_warehouse_id); 


                  $refno = $data['refno'];
                  $productid = $data['productid'];
                  $stinhand = $data['stockinhand'];
                  
                  /*
                    IF REFERENCE NO FOUND , THERE IS LINKED PRODUCT 
                  
                  */
                  if($refno > 0){
                    //If REF Number YES - Start
                    $LinkBasicProductData = DB::table('jocom_warehouse_productslinks AS JWP')
                                            ->select('parent_product_id','quantity','product_id')
                                            ->where('JWP.parent_product_id','=',$productid)
                                            ->first();

                    if(count($LinkBasicProductData)>0){
                      
                        $basicProdData = self::getStockinhandReserved($LinkBasicProductData->product_id);
                        $baseProdData = self::getStockinhandReserved($LinkBasicProductData->parent_product_id); 

                    }
                    else
                    {
                      
                        $LinkBasicProductData = DB::table('jocom_warehouse_productslinks')
                                                ->select('parent_product_id','quantity','product_id')
                                                ->where('product_id','=',$productid)
                                                ->first();

                        $basicProdData = self::getStockinhandReserved($LinkBasicProductData->product_id);
                        $baseProdData = self::getStockinhandReserved($LinkBasicProductData->parent_product_id); 

                    }
               
                    $refproductID = $LinkBasicProductData->product_id;

           
                    if(count($LinkBasicProductData)>0){

                        
                        $refbaseproductid = $LinkBasicProductData->parent_product_id;
                        $quantity = $LinkBasicProductData->quantity;
                       
                        if($quantity>0){
                            
                            $currentstock = $totalreserved;
                            //$basicStockInHandAvailable = $basicProdData['stockinhand'] + ( $baseProdData['stockinhand'] * $LinkBasicProductData->quantity);
                              $basicStockInHandAvailable = $baseProdData['stockinhand'];

                                  $deduction = 1;
                                   $deductQuantity = $currentstock/$LinkBasicProductData->quantity;
                                   $actualstock =  $basicStockInHandAvailable-($currentstock/$LinkBasicProductData->quantity);
                                    
                                   $base_update_Stock = Warehouse::where('id','=',$baseProdData['warehouseID'])->first();
                                   $base_update_Stock->stockin_hand = $actualstock;
                                   $base_update_Stock->save();
                                   
                                   // LOG DEDUCTION HERE //
                                   
                                   // WarehouseProductLog::saveRecord($base_update_Stock->id,'STOCKDEDUCT',$deductQuantity,$reference_id);
                                  
                                   // LOG DEDUCTION HERE //
                                  

                        }
                        
                    }
                    else
                    {
                             // Active REF Links Start 

                              $collectarr = array();

                              $basicRefdata = DB::table('jocom_warehouse_products')
                                                ->where('ref_no','=',$refno)
                                                ->where('active','=',1)
                                                //->where('product_id','!=',$refproductID)
                                                ->get(); 

                              if(count($basicRefdata)>0){
                                  $reftrue = 0;

                                  foreach ($basicRefdata as $key => $value) {
                                      
                                      $temparray = array('productid' => $value->product_id, );

                                      array_push($collectarr, $temparray);

                                  }

                                  if(count($collectarr)>0){
                                     foreach ($collectarr as $key => $value) {
                                              $basicStockInHandAvailable_01 = 0; 
                                              $currentstock_1 = 0; 
                                              $wholestock_1 = 0;
                                              $actualstock_1 = 0;
                                              $decimal_1 = 0;

                                              $LinkProductData = DB::table('jocom_warehouse_productslinks')
                                                                  ->select('parent_product_id','quantity','product_id')
                                                                  ->where('product_id','=',$value['productid'])
                                                                  ->first();
                                               $quantity_1 = $LinkProductData->quantity;
                                                                  
                                              if(count($LinkProductData)>0){
                                                  
                                                  if($quantity_1>0){
                                                    
                                                      $basicLinkProdData = self::getStockinhandReserved($LinkProductData->product_id);
                                                      $baseLinkProdData = self::getStockinhandReserved($LinkProductData->parent_product_id);

                                                      $currentstock_1 = $totalreserved;

                                                      //$basicStockInHandAvailable_01 = $basicLinkProdData['stockinhand'] + ( $baseLinkProdData['stockinhand'] * $LinkProductData->quantity);
                                                      $basicStockInHandAvailable_01 =  $baseLinkProdData['stockinhand'];

                                                        $actualstock_1 = $basicStockInHandAvailable_01-($currentstock_1/$LinkProductData->quantity);
                                                       // list($wholestock_1, $decimal_1) = explode('.', $actualstock_1);
                                                       
                                                        $baselink_update_Stock = Warehouse::where('id','=',$baseLinkProdData['warehouseID'])
                                                                                            ->first();
                                                        $baselink_update_Stock->stockin_hand = $actualstock_1;
                                                        $baselink_update_Stock->save();
                                                        
                                                        // LOG DEDUCTION HERE //
                                  
                                                        // LOG DEDUCTION HERE //
                                                        
                                                       

                                                        $reftrue = 1;
                                                        $deduction = 1;  
                                                        // echo $wholestock_1.'-'.$decimal_1;

                                                         break;

                                                  }  

                                                  


                                              }                    
                                              


                                      } 
                                  }

                                  // Active REF Base Links Start 
                                  if($reftrue == 0){
                                  
                                        if(count($collectarr)>0){
                                           
                                            foreach ($collectarr as $key => $value) {
                                                $baseStockInHandAvailable = 0; 
                                                
                                                $BaseProductData = DB::table('jocom_warehouse_products_baselinks')
                                                                  ->select('variant_product_id','product_id')
                                                                  ->where('variant_product_id','=',$value['productid'])
                                                                  ->where('default','=',1)
                                                                  ->first();
                                                    
                                                if(count($BaseProductData)>0){
                                                      $baseitemProdData = self::getStockinhandReserved($BaseProductData->variant_product_id);
                                                       
                                                    //  if($baseitemProdData['stockinhand']>0){

                                                          $actualstock_base =  $baseitemProdData['stockinhand'] - $totalreserved;

                                                           $baseitem_update_Stock = Warehouse::where('id','=',$baseitemProdData['warehouseID'])
                                                                                                ->first();
                                                           $baseitem_update_Stock->stockin_hand = $actualstock_base;
                                                           $baseitem_update_Stock->save();
                                                           
                                                            // LOG DEDUCTION HERE //
                                  
                                                            // LOG DEDUCTION HERE //

                                                        //   echo 'Base-'.$actualstock_base;
                                                           $deduction = 1;
                                                           
                            
                                                    //  }
                                                    break;        
                                                }


                                            }
                                        }

                                  }
                                  // Active REF Base Links End 

                              } 

                             // Active REF Links End    

                    }
                      

                    //If REF Number YES - End

                  }
                  else 
                  {
                    //If REF Number No - Start
                     $actcurrentstock = 0;
                     $actcurrentstock = $stinhand - $totalreserved;
                        //echo $product_warehouse_id;
                       $act_update_Stock = Warehouse::where('id','=',$product_warehouse_id)
                                                    ->first();
                       $act_update_Stock->stockin_hand = $actcurrentstock;
                       $act_update_Stock->save();
                       
                        // LOG DEDUCTION HERE //
                                  
                        // LOG DEDUCTION HERE //

                    $deduction = 1;        
                    //If REF Number No - End


                  }

                  if($deduction == 1){


                        $reserved_update = WarehouseProductReserved::where('id','=',$reservedid)
                                                                    ->first();
                        $reserved_update->is_completed = 1;
                        $reserved_update->save(); 

                  }

              }

          }


    }

    public static function getSorterdeducation(){

        $product_warehouse_id = 0; 
        $totalreserved = 0; 
        $reservedid = 0; 
        

        $data = array(); 

        $startdate = Date('Y-m-d')." 00:00:00";
        $enddate   = Date('Y-m-d')." 23:59:59";
         try{
        $WarehouseReserved = DB::table('jocom_warehouse_product_reserved')
                          ->where('created_at','>=',$startdate)
                          ->where('created_at','<=',$enddate)
                          ->where('is_completed','=',0)
                          ->get(); 

          

          if(count($WarehouseReserved) > 0){
              
              foreach ($WarehouseReserved as $key => $value) {
                  
                //   echo "<pre>";
                //   print_r($value);
                //   echo "</pre>";

                  $refno = 0; 
                  $currentstock = 0; 
                  $basicStockInHandAvailable = 0; 
                  $actualstock = 0;
                  $wholestock  = 0;
                  $decimal = 0;
                  $deduction = 0; 


                  $product_warehouse_id = $value->product_warehouse_id;
                  $totalreserved        = $value->total_reserved;
                  $reservedid           = $value->id;
                  $transaction_id       = $value->transaction_id ;
                //   echo "PWID:".$product_warehouse_id;
                //   echo $product_warehouse_id.'-'.$totalreserved.'-'.$reservedid.'<br>';

                  $data = self::getProductID($product_warehouse_id); 
                    // echo "<pre>";
                    // print_r($data );
                    // echo "</pre>";

                  $refno = $data['refno'];
                  $productid = $data['productid'];
                //   echo "PRODUCT ID:".$productid;
                  $stinhand = $data['stockinhand'];
                  
                  /*
                    IF REFERENCE NO FOUND , THERE IS LINKED PRODUCT 
                  
                  */
                  if($refno > 0){
                    //If REF Number YES - Start
                    $LinkBasicProductData = DB::table('jocom_warehouse_productslinks AS JWP')
                                            ->select('parent_product_id','quantity','product_id')
                                            ->where('JWP.parent_product_id','=',$productid)
                                            ->first();

                    if(count($LinkBasicProductData)>0){
                      
                        $basicProdData = self::getStockinhandReserved($LinkBasicProductData->product_id);
                        $baseProdData = self::getStockinhandReserved($LinkBasicProductData->parent_product_id); 

                    }
                    else
                    {
                      
                        $LinkBasicProductData = DB::table('jocom_warehouse_productslinks')
                                                ->select('parent_product_id','quantity','product_id')
                                                ->where('product_id','=',$productid)
                                                ->first();

                        $basicProdData = self::getStockinhandReserved($LinkBasicProductData->product_id);
                        $baseProdData = self::getStockinhandReserved($LinkBasicProductData->parent_product_id); 

                    }
               
                    $refproductID = $LinkBasicProductData->product_id;
                    
                    //  echo "Transaction ID :" .$transaction_id;
           
                    if(count($LinkBasicProductData)>0){

                        
                        $refbaseproductid = $LinkBasicProductData->parent_product_id;
                        $quantity = $LinkBasicProductData->quantity;
                        $g_basestock = 0; 
                        $g_basicstock = 0; 
                        $g_st_total = 0;
                        $g_st_total_final = 0;
                        $g_wholestock_1 = 0;
                        $g_decimal_1 = 0; 
                        $g_s_basestock = 0; 
                        $g_s_basicstock = 0; 
                       
                        if($quantity>0){
                            
                            $currentstock = $totalreserved;
                            //$basicStockInHandAvailable = $basicProdData['stockinhand'] + ( $baseProdData['stockinhand'] * $LinkBasicProductData->quantity);
                              $basicStockInHandAvailable = $baseProdData['stockinhand'];
                              
                              if($totalreserved >= $quantity)
                                   {
                                        $deduction = 1;
                                       $deductQuantity = $currentstock/$LinkBasicProductData->quantity;
                                       $actualstock =  $basicStockInHandAvailable- (int)($currentstock/$LinkBasicProductData->quantity);
                                        
                                       $base_update_Stock = Warehouse::where('id','=',$baseProdData['warehouseID'])->first();
                                       $base_update_Stock->stockin_hand = $actualstock;
                                      
                                       $base_update_Stock->save();     
                                       
                                       // LOG DEDUCTION HERE //
                                     //  WarehouseProductLog::saveRecord($base_update_Stock->id,'STOCKDEDUCT',$deductQuantity,$transaction_id,'REMARK1');
                                            
                                   
                                   }
                                   else
                                   {
                                        $deduction = 1;
                                        $g_basestock = $basicStockInHandAvailable * $quantity; 
                                        $g_basicstock = $basicProdData['stockinhand'];  
                                        $g_st_total = (int)($g_basestock + $g_basicstock) - $totalreserved; 
                                        $g_st_total_final = $g_st_total / $quantity ; 
                                        
                                        list($g_wholestock_1, $g_decimal_1) = explode('.', $g_st_total_final);
                                        
                                        $g_s_basestock = $g_wholestock_1;
                                        $g_s_basicstock = $g_st_total - (int)($g_wholestock_1 * $quantity);
                                        
                                        $g_base_update_Stock = Warehouse::where('id','=',$baseProdData['warehouseID'])->first();
                                        $g_base_update_Stock->stockin_hand = $g_wholestock_1;
                                        $g_base_update_Stock->save(); 
                                        
                                        $g_base_update_Stock = Warehouse::where('id','=',$basicProdData['warehouseID'])->first();
                                        $g_base_update_Stock->stockin_hand = $g_s_basicstock;
                                        $g_base_update_Stock->save(); 
                                       
                                       // LOG DEDUCTION HERE //
                                     //  WarehouseProductLog::saveRecord($g_base_update_Stock->id,'STOCKDEDUCT',$g_st_total_final,$transaction_id,'REMARK2');
                                   }

                                //   $deduction = 1;
                                //   $deductQuantity = $currentstock/$LinkBasicProductData->quantity;
                                //   $actualstock =  $basicStockInHandAvailable-($currentstock/$LinkBasicProductData->quantity);
                                    
                                //   $base_update_Stock = Warehouse::where('id','=',$baseProdData['warehouseID'])->first();
                                //   $base_update_Stock->stockin_hand = $actualstock;
                                //   $base_update_Stock->save();
                                
                                
                                
                                   
                                   // LOG DEDUCTION HERE //
                                   
                                   // WarehouseProductLog::saveRecord($base_update_Stock->id,'STOCKDEDUCT',$deductQuantity,$transaction_id);
                                  
                                   // LOG DEDUCTION HERE //
                                  

                        }
                        
                    }
                    else
                    {
                             // Active REF Links Start 

                              $collectarr = array();

                              $basicRefdata = DB::table('jocom_warehouse_products')
                                                ->where('ref_no','=',$refno)
                                                ->where('active','=',1)
                                                //->where('product_id','!=',$refproductID)
                                                ->get(); 

                              if(count($basicRefdata)>0){
                                  $reftrue = 0;

                                  foreach ($basicRefdata as $key => $value) {
                                      
                                      $temparray = array('productid' => $value->product_id, );

                                      array_push($collectarr, $temparray);

                                  }

                                  if(count($collectarr)>0){
                                     foreach ($collectarr as $key => $value) {
                                              $basicStockInHandAvailable_01 = 0; 
                                              $currentstock_1 = 0; 
                                              $wholestock_1 = 0;
                                              $actualstock_1 = 0;
                                              $decimal_1 = 0;

                                              $LinkProductData = DB::table('jocom_warehouse_productslinks')
                                                                  ->select('parent_product_id','quantity','product_id')
                                                                  ->where('product_id','=',$value['productid'])
                                                                  ->first();
                                               $quantity_1 = $LinkProductData->quantity;
                                                                  
                                              if(count($LinkProductData)>0){
                                                  
                                                  if($quantity_1>0){
                                                    
                                                      $basicLinkProdData = self::getStockinhandReserved($LinkProductData->product_id);
                                                      $baseLinkProdData = self::getStockinhandReserved($LinkProductData->parent_product_id);

                                                      $currentstock_1 = $totalreserved;

                                                      //$basicStockInHandAvailable_01 = $basicLinkProdData['stockinhand'] + ( $baseLinkProdData['stockinhand'] * $LinkProductData->quantity);
                                                      $basicStockInHandAvailable_01 =  $baseLinkProdData['stockinhand'];
                                                        $deductQuantity_1 = $currentstock_1/$LinkProductData->quantity;
                                                        $actualstock_1 = $basicStockInHandAvailable_01-($currentstock_1/$LinkProductData->quantity);
                                                       // list($wholestock_1, $decimal_1) = explode('.', $actualstock_1);
                                                       
                                                        $baselink_update_Stock = Warehouse::where('id','=',$baseLinkProdData['warehouseID'])
                                                                                            ->first();
                                                        $baselink_update_Stock->stockin_hand = $actualstock_1;
                                                        $baselink_update_Stock->save();
                                                        
                                                        // LOG DEDUCTION HERE //
                                                        
                                                        // WarehouseProductLog::saveRecord($baselink_update_Stock->id,'STOCKDEDUCT',$deductQuantity_1,$transaction_id);
                                  
                                                        // LOG DEDUCTION HERE //
                                                        
                                                       

                                                        $reftrue = 1;
                                                        $deduction = 1;  
                                                        // echo $wholestock_1.'-'.$decimal_1;

                                                         break;

                                                  }  

                                                  


                                              }                    
                                              


                                      } 
                                  }

                                  // Active REF Base Links Start 
                                  if($reftrue == 0){
                                  
                                        if(count($collectarr)>0){
                                           
                                            foreach ($collectarr as $key => $value) {
                                                $baseStockInHandAvailable = 0; 
                                                
                                                $BaseProductData = DB::table('jocom_warehouse_products_baselinks')
                                                                  ->select('variant_product_id','product_id')
                                                                  ->where('variant_product_id','=',$value['productid'])
                                                                  ->where('default','=',1)
                                                                  ->first();
                                                    
                                                if(count($BaseProductData)>0){
                                                      $baseitemProdData = self::getStockinhandReserved($BaseProductData->variant_product_id);
                                                       
                                                    //  if($baseitemProdData['stockinhand']>0){

                                                          $actualstock_base =  $baseitemProdData['stockinhand'] - $totalreserved;

                                                           $baseitem_update_Stock = Warehouse::where('id','=',$baseitemProdData['warehouseID'])
                                                                                                ->first();
                                                           $baseitem_update_Stock->stockin_hand = $actualstock_base;
                                                           $baseitem_update_Stock->save();
                                                           
                                                            // LOG DEDUCTION HERE //
                                                            
                                                            // WarehouseProductLog::saveRecord($baseitem_update_Stock->id,'STOCKDEDUCT',$totalreserved,$transaction_id);
                                  
                                                            // LOG DEDUCTION HERE //

                                                        //   echo 'Base-'.$actualstock_base;
                                                           $deduction = 1;
                                                           
                            
                                                    //  }
                                                    break;        
                                                }


                                            }
                                        }

                                  }
                                  // Active REF Base Links End 

                              } 

                             // Active REF Links End    

                    }
                      

                    //If REF Number YES - End

                  }
                  else 
                  {
                    //If REF Number No - Start
                     $actcurrentstock = 0;
                     $actcurrentstock = $stinhand - $totalreserved;
                      // echo "THE ID:".$product_warehouse_id;
                       $act_update_Stock = Warehouse::where('id','=',$product_warehouse_id)
                                                    ->first();
                       $act_update_Stock->stockin_hand = $actcurrentstock;
                       $act_update_Stock->save();
                       
                        // LOG DEDUCTION HERE //
                        
                        // WarehouseProductLog::saveRecord($act_update_Stock->id,'STOCKDEDUCT',$totalreserved,$transaction_id); 
                        
                        // LOG DEDUCTION HERE //

                    $deduction = 1;        
                    //If REF Number No - End


                  }

                  if($deduction == 1){


                        $reserved_update = WarehouseProductReserved::where('id','=',$reservedid)
                                                                    ->first();
                        $reserved_update->is_completed = 1;
                        $reserved_update->save(); 

                  }

              }

          }
         } catch (Exception $ex) {
            //   echo $ex->getMessage();
            //   echo $ex->getline();
                // echo "PRODUCT ID:".$productid;
         }	

    }
    
    public static function getUpdateCronParameters($productid,$quantityOrder,$type,$logitem_id,$isexists){

        $data = array('product_id' => $productid,
                      'quantity'   => $quantityOrder,
                      'type'       => $type,
                      'logitem'    => $logitem_id,
                      'isexists'   => $isexists,

                      );

          $result =  DB::table('jocom_cronparameters')
                        ->insert($data);
            // print_r($data);
          return $result;
    }
    
    /*
     * Deduction Function 
     */

    public static function getCronDeduction($productid,$quantityOrder,$type,$logitem_id = 0){
        $availableStock = 0;
        $is_Exists = 0; 
        $stockExist = 0;
        $stockEnough = 0;
        $totalStockInHandAvailable = 0;
        $totalstock = 0;
        $totalRequired = 0;
        $quantity = 0;
        $basicrefarray = array(); 
        $cronresult = array();

        $deduction = 0;
        $resv_stock = 0; 
        $resv_carry_stock = 0; 
        $cron_requiredstock = 0;
        $basiclinks = 0;

        $refno = 0;
        $basic = array();
        $base = array();
       

        $is_Exists = self::isExists($productid);
        
        $tempresult = self::getUpdateCronParameters($productid,$quantityOrder,$type,$logitem_id,$is_Exists);
        
        $reservedData = self::getBasicreservedvalue($productid);
        
        if($is_Exists > 0){

             $productresult = Warehouse::where('product_id','=',$productid)->first();
             
             if(count($productresult)>0){

                $refno       = $productresult->ref_no; 
                $stockinhand = $productresult->stockin_hand; 

             //   $totalstock = $stockinhand;

                // $temparr = array('warehouseID'      => $productresult->id,
                //                  'productid'        => $productid,
                //                  'stockinhand'      => $stockinhand,
                //                  'reservedstock'    => $productresult->reserved_in_hand,
                //                  );
                // array_push($basic,$temparr);

                $tempcarray = array('productid' => $productid, );                   
                array_push($basicrefarray, $tempcarray);
                //If REF Number YES
                if($refno >0){ 
                    //If REF Number YES - Start 
                    $LinkBasicProductData = DB::table('jocom_warehouse_productslinks AS JWP')
                                                ->select('parent_product_id','quantity','product_id')
                                                ->where('JWP.parent_product_id','=',$productid)
                                                ->first();  
                     
                    if(count($LinkBasicProductData)>0){
                        $basiclinks = 1;
                        // $LinkBasicProductData  = self::getProductlinkstock($productid);
                        $basicProdData = self::getStockinhandReserved($LinkBasicProductData->product_id);
                        $baseProdData = self::getStockinhandReserved($LinkBasicProductData->parent_product_id); 

                        $refproductID = $LinkBasicProductData->product_id;
                        $refparent_productID = $LinkBasicProductData->parent_product_id;

                        $quantity = $LinkBasicProductData->quantity;
                        $cron_requiredstock = $quantityOrder;

                        $totalStockInHandAvailable = $basicProdData['stockinhand'] + ( $baseProdData['stockinhand'] * $LinkBasicProductData->quantity );
                        $totalAvailable =  $totalStockInHandAvailable - $basicProdData['reservedstockinhand']; 
                        $totalstock = $totalstock + $totalAvailable;
                        $totalRequired = $quantityOrder * $LinkBasicProductData->quantity;

                        if($LinkBasicProductData->quantity == 0 ){
                           $tempstock = $totalAvailable;

                        }
                        else
                        {
                          $tempstock = round($totalAvailable/$LinkBasicProductData->quantity,2);
                        }

                        

                        $temparr = array('productid'         => $LinkBasicProductData->product_id,
                                         'baseproductid'     => $LinkBasicProductData->parent_product_id,
                                         'stockinhand'       => $tempstock, 
                                         'reservedstock'     => $basicProdData['reservedstockinhand'], 
                                         'warehouseid'       => $basicProdData['warehouseID'],
                                         'warehouseidbase'   => $baseProdData['warehouseID'],
                                     );
                        array_push($basic,$temparr);

                    }
                    else{
                      $totalRequired = $quantityOrder;
                    }        

                    if($totalStockInHandAvailable>=$totalRequired){

                            $stockExist = 1;
                            $stockEnough = 1;

                            $result = array(
                                "products"           => $basic,    
                                "stockExist"         => $stockExist,
                                "stockEnough"        => $stockEnough,
                                "refno"              => $refno,
                                "stockRequired"      => $totalRequired,
                                "stockAvailable"     => $totalAvailable,
                                "reservedData"  => $reservedData
                             );

                            //CRON JOB START

                            if($type == 'CRON'){
                              $finalstock = 0; 
                              $reserved = 0; 
                              $restk = 0;
                              $resvwareid = 0;
                              $st_base_deduct = 0;
                              $st_basic_deduct = 0;


                              // RESERVED STOCK START
                              $resvwareid = $basicProdData['warehouseID'];
                              $revresult = DB::table('jocom_warehouse_product_reserved')
                                               ->where('product_warehouse_id','=',$resvwareid) 
                                               ->where('logistic_item_id','=',$logitem_id)
                                               ->where('is_completed','=',0)
                                               ->first(); 

                              if(count($revresult)>0){
                                $restk = $revresult->total_reserved;

                                  if($basicProdData['reservedstockinhand'] != 0){
                                      $reserved = $basicProdData['reservedstockinhand'] - $restk;
                                      $cresrv = 0;
                                      if($reserved>=0){
                                        $cresrv = $reserved;
                                      }

                                       $resvStock = Warehouse::where('id','=',$resvwareid)
                                                               ->first();
                                       $resvStock->reserved_in_hand = $cresrv;
                                       $resvStock->save();

                                       $whresvprod = WarehouseProductReserved::find($revresult->id);//->first();
                                       $whresvprod->is_completed = 1; 
                                       $whresvprod->save();       

                                  }

                              }   
                              // RESERVED STOCK END

                              // DEDUCTION START 
                              $wholestock = 0;
                              $decimal = 0;
                              $basicstock =0;
                             // echo $LinkBasicProductData->quantity.'-00000';
                              if($LinkBasicProductData->quantity == 0 ){  
                                //  $finalstock = round(round($totalAvailable/$LinkBasicProductData->quantity) - $quantityOrder,2);
                                  $tempstock_1 = $totalAvailable;          
                              }   
                              else    
                              {   
                                  $tempstock_1 = round($totalAvailable/$LinkBasicProductData->quantity,2);    
                              }   
                                
                              $tot = round($tempstock_1,2);
                             // $tot = round($totalAvailable/$LinkBasicProductData->quantity,2);
                              $finalstock = round(round($tempstock_1) - $quantityOrder,2);
                               
                              if($finalstock != 0){

                                  if($finalstock<0){
                                    $finalstock = 0;
                                  }

                                  list($wholestock, $decimal) = explode('.', $finalstock);
                                
                                  if(isset($wholestock) && $wholestock != 0){
                                      $deduction = 1;
                                      $base_update_Stock = Warehouse::where('product_id','=',$LinkBasicProductData->parent_product_id)
                                                                  ->first();
                                      $base_update_Stock->stockin_hand = $wholestock;
                                      $base_update_Stock->save();


                                      if($decimal != 0){
                                        $basicstock =$decimal;
                                        $deduction = 1;
                                      }
                                      
                                      $basic_update_Stock = Warehouse::where('product_id','=',$LinkBasicProductData->product_id)
                                                                  ->first();
                                      $basic_update_Stock->stockin_hand = $basicstock;
                                      $basic_update_Stock->save();

                                      $tempcron =  array('warehouseid'        => $basicProdData['warehouseID'], 
                                                         'warehouseidbase'    => $baseProdData['warehouseID'], 
                                                         'stockdeductbasic'   => $quantityOrder,
                                                         'stockdeductbase'   => 0

                                                    );
                                      array_push($cronresult,$tempcron);
                                   
                                  }

                              }
                              else if ($tot == $quantityOrder) 
                              {
                                      $deduction = 1;
                                      $basic_update_Stock = Warehouse::where('product_id','=',$LinkBasicProductData->product_id)
                                                                  ->first();
                                      $basic_update_Stock->stockin_hand = 0;
                                      $basic_update_Stock->save();

                                      $tempcron =  array('warehouseid'      => $basicProdData['warehouseID'], 
                                                         'warehouseidbase'  => $baseProdData['warehouseID'], 
                                                         'stockdeductbasic'   => $quantityOrder,
                                                         'stockdeductbase'   => 0

                                                    );
                                      array_push($cronresult,$tempcron);
                              }
                              // DEDUCTION END 

                              $resultcronjobs = array('deduction'  => $deduction,
                                                      'cronresult' => $cronresult
                                                     );

                                //$cronresult = array('' => , ); 

                            }
                             //CRON JOB END


                    }
                    else{
                          
                            $baserefarray = array(); 
                            $full = 0; 
                            $stockExist = 1;
                            
                            if(!isset($refproductID)){
                              $refproductID = 0;
                            }

                            $basicRefdata = DB::table('jocom_warehouse_productslinks')
                                                ->where('ref_no','=',$refno)
                                                ->where('product_id','!=',$refproductID)
                                                ->get(); 
                            // echo '<pre>';                     
                            // print_r($basicRefdata);
                            // echo '</pre>';
                              //die($refno.'-'.$refproductID.'-'.$totalStockInHandAvailable.'-'.$totalRequired.'ok');   
                           if(count($basicRefdata)>0){
                                $basiclinks = 1;    
                              foreach ($basicRefdata as $basicvalue) {

                                 $basicStockInHandAvailable = 0;
                                 $totalAvailablebasic = 0; 
                                
                                // echo 'ProD'.$basicvalue->product_id;
                                $tempbasicarray = array('productid' => $basicvalue->parent_product_id, );                   
                                array_push($basicrefarray, $tempbasicarray);

                                $basicData = self::getStockinhandReserved($basicvalue->product_id);
                                $baseLinksData = self::getStockinhandReserved($basicvalue->parent_product_id); 

                                if($refparent_productID == $basicvalue->parent_product_id){
                                  $basicStockInHandAvailable = $basicData['stockinhand'];
                                }
                                else{

                                $basicStockInHandAvailable = $basicData['stockinhand'] + ( $baseLinksData['stockinhand'] * $basicvalue->quantity );
                                }

                                $totalAvailablebasic =  $basicStockInHandAvailable - $basicData['reservedstockinhand']; 
                                
                                $totalstock = $totalstock + $totalAvailablebasic; 

                                if($basicvalue->quantity == 0 ){
                                   $tempstockbasic = $totalAvailablebasic;
            
                                }
                                else
                                {
                                  $tempstockbasic = round($totalAvailablebasic/$basicvalue->quantity,2);
                                }
                               // echo $totalAvailablebasic.'-'.$basicvalue->quantity;
                               // echo $quantity;
                                if($quantity == 0){
                                    $quantity = $basicvalue->quantity;
                                }
                               // die();
                                $temparr = array(
                                     'productid'   => $basicvalue->product_id,
                                     'baseproductid' => $basicvalue->parent_product_id,
                                     'stockinhand' => $tempstockbasic,  
                                     'reservedstock'  => $basicData['reservedstockinhand'], 
                                     'warehouseid'   => $basicData['warehouseID'],
                                     'warehouseidbase'   => $baseLinksData['warehouseID'],

                                 );
                                array_push($basic,$temparr);

                                if($totalstock>=$totalRequired){
                                        $stockEnough = 1;
                                         $result = array(
                                            "products"           => $basic,    
                                            "stockExist"         => $stockExist,
                                            "stockEnough"        => $stockEnough,
                                            "refno"              => $refno,
                                            "stockRequired"      => round($totalRequired/$quantity,1),
                                            "stockAvailable"     => round($totalstock/$quantity,2),
                                            "reservedData"       => $reservedData
                                         );
                                       
                                        // echo '<pre>'.count($basic);
                                        // print_r($basic);
                                        // echo '<pre>';

                                        //CRON JOB START

                                        if($type == 'CRON'){


                                            foreach ($basic as $key => $value) {
                                              $reserved = 0; 
                                              $restk = 0;
                                              $wareid  = 0;
                                              # code...
                                              // echo $value['warehouseid'].'Basic<br>';
                                              // echo $value['warehouseidbase'].'Base<br>';
                                              // echo $value['stockinhand'].'stock<br>';
                                              // echo $value['warehouseid'].'WareID<br>';

                                              $wareid = $value['warehouseid'];

                                              // RESERVED STOCK START
                                              $resvwareid = 0;
                                              $resvwareid = $value['productid'];
                                              $revresult = DB::table('jocom_warehouse_product_reserved')
                                                               ->where('product_warehouse_id','=',$wareid) 
                                                               ->where('logistic_item_id','=',$logitem_id)
                                                               ->where('is_completed','=',0)
                                                               ->first(); 

                                              if(count($revresult)>0){
                                                $restk = $revresult->total_reserved;

                                                if($value['reservedstock'] != 0){
                                                    $reserved = $value['reservedstock'] - $restk;
                                                    $cresrv = 0;
                                                    if($reserved>=0){
                                                      $cresrv = $reserved;
                                                    }

                                                     $resvStock = Warehouse::where('id','=',$value['warehouseid'])
                                                                                ->first();
                                                     $resvStock->reserved_in_hand = $cresrv;
                                                     $resvStock->save();


                                                     $whresvprod = WarehouseProductReserved::find($revresult->id);//->first();
                                                     $whresvprod->is_completed = 1; 
                                                     $whresvprod->save();   

                                                }

                                              }
                                              // RESERVED STOCK END

                                              // DEDUCTION START 
                                                $wholestock = 0;
                                                $decimal = 0;
                                                $basicstock =0;
                                                $stkrecurr = 0;
                                                
                                                if($cron_requiredstock == 0){
                                                 $cron_requiredstock = $quantityOrder;   
                                                }
                                                $cron_requiredstock = $value['stockinhand'] - $cron_requiredstock; 
                                                
                                                if($cron_requiredstock == 0){
                                                        
                                                   // list($wholestock, $decimal) = explode('.', $cron_requiredstock);
                                                    
                                                    $deduction = 1;
                                                        
                                                        $base_update_Stock = Warehouse::where('id','=',$value['warehouseidbase'])
                                                                                    ->first();
                                                        $base_update_Stock->stockin_hand = 0;
                                                        $base_update_Stock->save();

                                                        
                                                    
                                                        
                                                        $basic_update_Stock = Warehouse::where('id','=',$value['warehouseid'])
                                                                                    ->first();
                                                        $basic_update_Stock->stockin_hand = 0;
                                                        $basic_update_Stock->save();

                                                        $tempcron =  array('warehouseid'      => $value['warehouseid'], 
                                                                           'warehouseidbase'  => $value['warehouseidbase'], 
                                                                           'stockdeductbasic'   => $quantityOrder,
                                                                           'stockdeductbase'   => 0
                                                                      );
                                                        array_push($cronresult,$tempcron);


                                                }
                                                else if($cron_requiredstock != 0)
                                                {
                                                   list($wholestock, $decimal) = explode('.', abs($cron_requiredstock));
                                                    

                                                    if(isset($wholestock)){
                                                        $deduction = 1;
                                                        $base_update_Stock = Warehouse::where('id','=',$value['warehouseidbase'])
                                                                                    ->first();
                                                        $base_update_Stock->stockin_hand = $wholestock;
                                                        $base_update_Stock->save();

                                                        if($decimal != 0){
                                                          $basicstock =$decimal;
                                                          $deduction = 1;
                                                        }
                                                        
                                                        $basic_update_Stock = Warehouse::where('id','=',$value['warehouseid'])
                                                                                    ->first();
                                                        $basic_update_Stock->stockin_hand = $basicstock;
                                                        $basic_update_Stock->save();

                                                        $tempcron =  array('warehouseid'      => $value['warehouseid'], 
                                                                           'warehouseidbase'  => $value['warehouseidbase'], 
                                                                           'stockdeductbasic'   => $quantityOrder,
                                                                           'stockdeductbase'   => 0
                                                                           
                                                                      );
                                                        array_push($cronresult,$tempcron);

                                                     
                                                    }

                                                }
                                                // DEDUCTION END 
                                               

                                            }
                                             $resultcronjobs = array('deduction'  => $deduction,
                                                                      'cronresult' => $cronresult
                                                                     );

                                        }
                                                                                
                                         //CRON JOB END

                                    break;
                                }

                              //  $totalRequired = $totalRequired + $totalAvailablebasic;
                    
                            } 

                           }
                                                    
                            



                            if($stockEnough == 0){
                                 
                                if($basiclinks == 1){
                                    $baseRefdata = DB::table('jocom_warehouse_products_baselinks')
                                                ->where('ref_no','=',$refno)
                                                ->whereNotIn('variant_product_id',$basicrefarray)
                                                ->get(); 
                                }
                                else 
                                {
                                  $baseRefdata = DB::table('jocom_warehouse_products_baselinks')
                                                ->where('ref_no','=',$refno)
                                                //->whereNotIn('variant_product_id',$basicrefarray)
                                                ->get();   
                                }
                                
                                if((int)$totalRequired == 0) {
                                     $totalRequired = $quantityOrder;
                                }
                                
                                
                                
                                // echo '<pre>'; 
                                // print_r($baseRefdata);
                                // echo '</pre>';
                                
                                if(count($baseRefdata)>0){
                                   // echo 'TOT'.$totalstock;
                                  foreach ($baseRefdata as  $basevalue) {
                                   $baseStockInHandAvailable = 0;
                                   $totalAvailablebase = 0; 
                                   
                                   $baseLinksData_01 = self::getStockinhandReserved($basevalue->variant_product_id);     


                                  // $baseStockInHandAvailable =  ( $baseLinksData_01['stockinhand'] * $quantity );
                                  // $totalAvailablebase =  $baseStockInHandAvailable - ($baseLinksData_01['reservedstockinhand'] * $quantity);
                                   //$baseStockInHandAvailable =  ($baseLinksData_01['stockinhand'] );
                                   $baseStockInHandAvailable =  self::getStockinhand($basevalue->variant_product_id);
                                   //$totalAvailablebase =  $baseStockInHandAvailable - ($baseLinksData_01['reservedstockinhand']);  
                                   $totalAvailablebase = $baseStockInHandAvailable; 
                                   
                                    $totalstock = $totalstock + $totalAvailablebase; 
                                   $totalstock_stk =0; 
                                   $totalstock_stk =0;
                                   if($quantity == 0){
                                    $totalstock_stk = $totalstock;
                                   }
                                   else{
                                      $totalRequired_stk = $totalRequired / $quantity; 
                                      $totalstock_stk = $totalstock / $quantity;
                                   }

                                    $temparr = array(
                                         'productid'   => $basevalue->variant_product_id,
                                         'baseproductid' => $basevalue->product_id,
                                         'stockinhand' => self::getStockinhand($basevalue->variant_product_id), 
                                         'reservedstock'  => $baseLinksData_01['reservedstockinhand'],  
                                         'warehouseid'   => 0,
                                         'warehouseidbase'   => $baseLinksData_01['warehouseID'],
                                     );                 
                                     array_push($basic,$temparr);    
                                     
                                    // echo '77'.$baseLinksData_01['stockinhand'].'-'.$totalstock.'_'.$basevalue->variant_product_id.'-'.$totalRequired.'==';
                                     
                                     if($totalstock>=$totalRequired){
                                        
                                        $stockEnough = 1;
                                         $result = array(
                                            "products"           => $basic,    
                                            "stockExist"         => $stockExist,
                                            "stockEnough"        => $stockEnough,
                                            "refno"              => $refno,
                                            "stockRequired"      => $totalRequired_stk,
                                            "stockAvailable"     => $totalstock_stk,
                                            "reservedData"  => $reservedData
                                                 );


                                            //CRON JOB START

                                            if($type == 'CRON'){

                                              //  $cron_requiredstock = $quantityOrder * $quantity;

                                                foreach ($basic as $key => $value) {
                                                  $reserved = 0; 
                                                  $restk = 0;
                                                  $wareid = 0;
                                                  $cresrv = 0;
                                                  
                                                  // RESERVED STOCK START
                                                  $wareid = $value['warehouseid'];
                                                  $resvwareid = 0;
                                                  $resvwareid = $value['productid'];
                                                  $revresult = DB::table('jocom_warehouse_product_reserved')
                                                                   ->where('product_warehouse_id','=',$wareid) 
                                                                   ->where('logistic_item_id','=',$logitem_id)
                                                                   ->where('is_completed','=',0)
                                                                   ->first(); 


                                                  if(count($revresult)>0){
                                                    $restk = $revresult->total_reserved;

                                                    if($value['reservedstock'] != 0){
                                                        $reserved = $value['reservedstock'] - $restk;
                                                        
                                                        if($reserved>=0){
                                                          $cresrv = $reserved;
                                                        }


                                                         $resvStock = Warehouse::where('id','=',$wareid)
                                                                                    ->first();
                                                         $resvStock->reserved_in_hand = $cresrv;
                                                         $resvStock->save();

                                                         $whresvprod = WarehouseProductReserved::find($revresult->id);
                                                                                   // ->first();
                                                         $whresvprod->is_completed = 1; 
                                                         $whresvprod->save(); 


                                                    }

                                                  }
                                                  // RESERVED STOCK END
                                                       // echo 'LI';
                                                  // DEDUCTION START 
                                                  $wholestock = 0;
                                                  $decimal = 0;
                                                  $basicstock =0;
                                                  $stkrecurr = 0;
                                                  
                                                  $cron_requiredstock = $value['stockinhand'] - $cron_requiredstock  ; 

                                                  if($cron_requiredstock>0){

                                                      list($wholestock, $decimal) = explode('.', $cron_requiredstock);
                                                      
                                                      if(isset($wholestock) && $wholestock != 0){
                                                          $deduction = 1;
                                                          $base_update_Stock = Warehouse::where('id','=',$value['warehouseidbase'])
                                                                                      ->first();
                                                          $base_update_Stock->stockin_hand = 0;
                                                          $base_update_Stock->save();


                                                          if($decimal != 0){
                                                            $basicstock =$decimal;
                                                            $deduction = 1;
                                                          }
                                                          if($value['warehouseid'] != 0){
                                                            $basic_update_Stock = Warehouse::where('id','=',$value['warehouseid'])
                                                                                      ->first();
                                                            $basic_update_Stock->stockin_hand = 0;
                                                            $basic_update_Stock->save();

                                                          }

                                                          

                                                          $tempcron =  array('warehouseid'      => $value['warehouseid'], 
                                                                             'warehouseidbase'  => $value['warehouseidbase'], 
                                                                             'stockdeductbasic'   => $quantityOrder,
                                                                             'stockdeductbase'   => 0
                                                                        );
                                                          array_push($cronresult,$tempcron);

                                                       
                                                      }

                                                  }
                                                  else if($cron_requiredstock != 0)
                                                  {
                                                     list($wholestock, $decimal) = explode('.', abs($cron_requiredstock));
                                                      
                                                     // die($value['warehouseid'].$cron_requiredstock);
                                                      if(isset($wholestock)){
                                                          $deduction = 1;
                                                          $base_update_Stock = Warehouse::where('id','=',$value['warehouseidbase'])
                                                                                      ->first();
                                                          $base_update_Stock->stockin_hand = $wholestock;
                                                          $base_update_Stock->save();

                                                          if($decimal != 0){
                                                            $basicstock =$decimal;
                                                            $deduction = 1;
                                                          }
                                                          
                                                          if($value['warehouseid'] != 0){
                                                            $basic_update_Stock = Warehouse::where('id','=',$value['warehouseid'])
                                                                                      ->first();
                                                            $basic_update_Stock->stockin_hand = $basicstock;
                                                            $basic_update_Stock->save();

                                                          }

                                                          

                                                          $tempcron =  array('warehouseid'      => $value['warehouseid'], 
                                                                             'warehouseidbase'  => $value['warehouseidbase'], 
                                                                             'stockdeductbasic'   => $totalRequired,
                                                                             'stockdeductbase'   => 0
                                                                        );
                                                          array_push($cronresult,$tempcron);

                                                       
                                                      }

                                                  }
                                                  // DEDUCTION END 



                                                }

                                                $resultcronjobs = array('deduction'  => $deduction,
                                                                    'cronresult' => $cronresult
                                                                   );

                                            }
                                                                                    
                                             //CRON JOB END    


                                            break;
                                        }

                                } 

                                }

                                

                            }

                            if($stockEnough == 0){
                                
                                $totReq = 0;
                                $stavai = 0;
                                if((int)$quantity == 0){
                                   
                                    $totReq = $totalRequired;
                                    $stavai = $totalstock;
                                }
                                else
                                {
                                    $totReq = round($totalRequired/$quantity,1);
                                    $stavai = round($totalstock/$quantity,2);
                                }   
                                
                                // echo '<pre>';
                                // print_r($basic);
                                // echo '</pre>';
                                
                                  
                                $result = array(
                                        "products"           => $basic,    
                                        "stockExist"         => $stockExist,
                                        "stockEnough"        => $stockEnough,
                                        "refno"              => $refno,
                                        "stockRequired"      => $totReq,
                                        "stockAvailable"     => $stavai,
                                        "reservedData"   => $reservedData
                                    );

                            }

                    }

                    //If REF Number YES - End 

                }
                else{
                    
                    //If REF Number NO - Start 
                     $stock_inHand = 0; 
                     $stockinhand = $productresult->stockin_hand; 
                     $ProdData = self::getStockinhandReserved($productid);
                     $stock_inHand = $stockinhand - $ProdData['reservedstockinhand'];
                     $temparr = array(
                                     'productid'   => $productid,
                                     'baseproductid' => 0,
                                     'stockinhand' => $stock_inHand,  
                                     'reservedstock'  => $ProdData['reservedstockinhand'],  
                                     'warehouseid'   => $ProdData['warehouseID'],
                                     'warehouseidbase'   => 0,
                                 );
                    array_push($basic,$temparr);

                     if($stockinhand>=$quantityOrder){

                            $stockExist = 1;
                            $stockEnough = 1;

                            $result = array(
                                "products"           => $basic,    
                                "stockExist"         => $stockExist,
                                "stockEnough"        => $stockEnough,
                                "refno"              => $refno,
                                "stockRequired"      => $quantityOrder,
                                "stockAvailable"     => $stock_inHand,
                                "reservedData"       => $reservedData
                             );

                            //CRON JOB START

                            if($type == 'CRON'){
                              $finalstock = 0; 
                              $reserved = 0; 
                              $restk = 0;
                              // RESERVED STOCK START
                              $resvwareid = $ProdData['warehouseID'];
                              $revresult = DB::table('jocom_warehouse_product_reserved')
                                               ->where('product_warehouse_id','=',$resvwareid) 
                                               ->where('logistic_item_id','=',$logitem_id)
                                               ->where('is_completed','=',0)
                                               ->first(); 

                              if(count($revresult)>0){
                                $restk = $revresult->total_reserved;

                                  if($ProdData['reservedstockinhand'] != 0){
                                    $reserved = $ProdData['reservedstockinhand'] - $restk;
                                    $cresrv = 0;
                                    if($reserved>=0){
                                      $cresrv = $reserved;
                                    }

                                     $resvStock = Warehouse::where('id','=',$resvwareid)
                                                             ->first();
                                     $resvStock->reserved_in_hand = $cresrv;
                                     $resvStock->save();
                                  }

                              }   

                              // RESERVED STOCK END

                              // DEDUCTION START 

                               
                                $basicstock =0;
                                $tot = 0;
                                $tot = $stockinhand;
                                $finalstock = $stockinhand - $quantityOrder;

                                if($finalstock != 0){
                                  
                                  
                                    if(isset($finalstock) && $finalstock != 0){

                                      if($finalstock < 0){
                                       // $finalstock = 0;
                                      }

                                        $deduction = 1;
                                        $base_update_Stock = Warehouse::where('product_id','=',$productid)
                                                                    ->first();
                                        $base_update_Stock->stockin_hand = $finalstock;
                                        $base_update_Stock->save();


                                        $tempcron =  array('warehouseid'      => $ProdData['warehouseID'], 
                                                           'warehouseidbase'  => 0, 
                                                           'stockdeductbasic'   => 0,
                                                           'stockdeductbase'   => $quantityOrder


                                                      );
                                        array_push($cronresult,$tempcron);
                                     
                                    }

                                }
                                else if ($tot == $quantityOrder) 
                                {

                                        $noref_update_Stock = Warehouse::where('product_id','=',$productid)
                                                                    ->first();
                                        $noref_update_Stock->stockin_hand = 0;
                                        $noref_update_Stock->save();

                                        $tempcron =  array('warehouseid'      => $ProdData['warehouseID'], 
                                                           'warehouseidbase'  => 0, 
                                                           'stockdeductbasic'   => 0,
                                                           'stockdeductbase'   => $quantityOrder

                                                      );
                                        array_push($cronresult,$tempcron);
                                }
                                // DEDUCTION END  
                             

                                //$cronresult = array('' => , ); 

                                $resultcronjobs = array('deduction'  => $deduction,
                                                      'cronresult' => $cronresult
                                                     );

                            }
                             //CRON JOB END


                    }
                    else{

                            $stockExist = 1;

                            $result = array(
                                "products"           => $basic,    
                                "stockExist"         => $stockExist,
                                "stockEnough"        => $stockEnough,
                                "refno"              => $refno,
                                "stockRequired"      => $quantityOrder,
                                "stockAvailable"     => $stock_inHand,
                                "reservedData"       => $reservedData
                             );
                    }

                    //If REF Number NO - End 
                }

 
             }     

        }
        else{
            // NO PRODUCT FOUND 

            $tempcron =  array('warehouseid'      => 0, 
                               'warehouseidbase'  => 0, 
                               'stockdeductbasic'   => $quantityOrder,
                               'stockdeductbase'   => 0

                          );
                          array_push($cronresult,$tempcron);

            $resultcronjobs = array('deduction'  => $deduction,
                                    'cronresult' => $cronresult
                                   );              

      

        }

        // echo '<pre>';
        // print_r($resultcronjobs);
        // echo '</pre>';


      return $resultcronjobs;

    }
    
    public static function getBasicreservedvalue($baseid){
        $response = 0; 
        $productid = 0; 
        $quantity = 0; 
        $base_id = 0; 

        $baseresult = DB::table('jocom_warehouse_products_baselinks')
                            ->where('variant_product_id','=',$baseid)
                            ->first();  
            
        if(count($baseresult)>0){
             $base_id = $baseresult->variant_product_id; 
             $root_base_id = $baseresult->product_id; 

             $linksresult = DB::table('jocom_warehouse_productslinks')
                            ->where('parent_product_id','=',$baseid)
                            ->first(); 

             if(count($linksresult)>0){

                $response = 1; 
                $productid = $linksresult->product_id;
                $quantity = $linksresult->quantity;
             } 
             else{
                $base_id = $baseresult->product_id; 

                $linksres = DB::table('jocom_warehouse_productslinks')
                            ->where('parent_product_id','=',$base_id)
                            ->first(); 
                 if(count($linksres)>0){
                     $response = 1; 
                     $productid = $linksres->product_id;
                     $quantity = $linksres->quantity;

                 }           


             }        
             
        }else{
            
            $basicresult = DB::table('jocom_warehouse_productslinks')
                            ->where('product_id','=',$baseid)
                            ->first(); 
                if(count($basicresult)>0){
                     $response = 1; 
                     $productid = $basicresult->product_id;
                     //$quantity = $basicresult->quantity;
                     $quantity = 1;
                }

        }

        if($productid == 0){
          $whresult = Warehouse::where("product_id",$baseid)->first();
          
                if(count($whresult)>0){
                     $response = 1; 
                     $productid = $whresult->product_id;
                     $quantity = 1;

                }

        }
        
        // find warehouseid 
        $Warehouse = Warehouse::where("product_id",$productid)->first();
       
        // find warehouseid 
        
        return array('response' => $response ,
                     'productid' => $productid,
                     'quantity' => $quantity,  
                     'ProductWareHouseID' => $Warehouse->id
                     );

    }
    
    /*
     * This is damn so pening . becareful on the flow guys
     */
    public static function getAvailableStock($productid,$quantityOrder){

    	$availableStock = 0;
        
        $isExistsBase = self::isExistsBase($productid);
        
        
        // Have basic product
        if($isExistsBase == 1){
            
           // echo "TEE";
            
            // will have information (basicstock,parentstock,quantity)
            $LinkBasicProductData  = self::getProductlinkstock($productid);
            $totalStockInHandAvailable = $LinkBasicProductData['basicstock'] + ( $LinkBasicProductData['parentstock'] * $LinkBasicProductData['quantity'] );
            
            $basicProductID = $LinkBasicProductData['basicStockProductID'];
            $parentProductID = $LinkBasicProductData['parentStockProductID'];
            
            
            $basicProductInfo  = Warehouse::where('product_id','=',$basicProductID)
    			  	->first();
            
            $parentProductInfo  = Warehouse::where('product_id','=',$parentProductID)
    			  	->first();
            
            $basicWareHouseID = $basicProductInfo->id;
            $parentWareHouseID = $parentProductInfo->id;
            
            $totalAvailable = $totalStockInHandAvailable - $basicProductInfo->reserved_in_hand;
            $totalRequired = $quantityOrder * $LinkBasicProductData['quantity'];
            
            if($totalRequired <=  $totalAvailable) {
                
                $result = array(
                    
                    "stockExist" => 1,
                    "stockEnough" => 1,
                    "productWarehouseID" => $basicWareHouseID,
                    "productID" => $basicProductID,
                    "stockInHand" => $totalStockInHandAvailable,
                    "stockReserved" => $basicProductInfo->reserved_in_hand,
                    "stockRequired" => $totalRequired,
                    "stockAvailable" => $totalAvailable,
                );
                
                
                
            }else{
                
                $result = array(
                    "stockExist" => 1,
                    "stockEnough" => 0,
                    "productWarehouseID" => $basicWareHouseID,
                    "stockInHand" => $totalStockInHandAvailable,
                    "stockReserved" => $getTotalReservedBasic->reserved_in_hand,
                    "stockAvailable" => $totalAvailable,
                );
                
            }
             
        }else{
            
           // echo "WIRA";
            // NO basic product
            
            $wareProducts = Warehouse::where('product_id','=',$productid)
                            ->select('id','stockin_hand','reserved_in_hand')
                            ->first();
            
            //Return available stock for delivery
            if(count($wareProducts)>0){
            
                $totalAvailable = $wareProducts->stockin_hand -  $wareProducts->reserved_in_hand;
                
                if($quantityOrder <= $totalAvailable){
                    
                    $result = array(
                        "stockExist" => 1,
                        "stockEnough" => 1,
                        "productWarehouseID" => $wareProducts->id,
                        "productID" => $productid,
                        "stockInHand" => $wareProducts->stockin_hand,
                        "stockReserved" => $wareProducts->reserved_in_hand,
                        "stockRequired" => $quantityOrder,
                        "stockAvailable" => $totalAvailable,
                    );
                    
                }else{
                    
                    $result = array(
                        "stockExist" => 1,
                        "stockEnough" => 0,
                        "productWarehouseID" => $wareProducts->id,
                        "stockInHand" => $wareProducts->stockin_hand,
                        "stockReserved" => $wareProducts->reserved_in_hand,
                        "stockRequired" => $quantityOrder,
                        "stockAvailable" => $totalAvailable,
                    );
                    
                }
                
                
            }
        }

    	$wareProducts = Warehouse::where('product_id','=',$productid)
    			  	->select('id','stockin_hand','reserved_in_hand')
    			  	->first();
        
        
        // Return available stock for delivery
//    	if(count($wareProducts)>0){
//            
//            $availableStock = $wareProducts->stockin_hand -  $wareProducts->reserved_in_hand;
//            $result = array(
//                "stockExist" => 1,
//                "productWarehouseID" => $wareProducts->id,
//                "stockInHand" => $wareProducts->stockin_hand,
//                "stockReserved" => $wareProducts->reserved_in_hand,
//                "stockAvailable" => $availableStock,
//            );
//            
//            
//    	}else{
//            
//            $stockinhand = $wareProducts->stockin_hand;
//            $result = array(
//                "stockExist" => 0,
//                "productWarehouseID" => 0,
//                "stockInHand" => 0,
//                "stockReserved" => 0,
//                "stockAvailable" => 0,
//            );
//        }
        
//        echo "<pre>";
//        print_r($result);
//        echo "</pre>";

    	return $result;

    }

    public static function getInventoryoldstock($productid){

    	$oldstock = 0;

    	$result = DB::table('jocom_warehouse_inventoryadjustments')
    				->select('oldstock')	
    				->orderby('created_at','DESC')
    				->where('product_id','=',$productid)
    				->first();
    	if(count($result)==0){

    		$resultpm = Warehouse::where('product_id','=',$productid)
	    			  	->select('stockin_hand')
	    			  	->first();
    		$oldstock = $resultpm->stockin_hand; 	  	
    	}
    	else{
    		$oldstock = $result->oldstock;
    	}

    	return $oldstock;


    }

    public static function getInventoryNewstock($productid){

    	$newstock = 0;

    	$result = DB::table('jocom_warehouse_inventoryadjustments')
    				->select('newstock')	
    				->orderby('created_at','DESC')
    				->where('product_id','=',$productid)
    				->first();
    	if(count($result)>0){
  			$newstock = $result->newstock;
    	}
    	

    	return $newstock;


    }


    public static function getProductslinksup($productid){

    	// $result = DB::table('jocom_warehouse_productslinks')
    	// 			->select('product_id','parent_product_id','nodelevel','quantity')
    	// 			->where('parent_product_id','=',$productid)
    	// 			->where('status','!=','2')
    	// 			->get();
    	// return $result;

    	$result = DB::table('jocom_warehouse_productslinks AS WP')
	    				->select("WP.product_id AS ProductID",
	    						 "JP.name AS ProductName",
	    						 "JPP.label AS Label",
	    						 "WP.parent_product_id AS ParentProductId",
                                 "WP.base_product_id AS BaseProductId",
	    						 "WP.nodeposition AS NodePosition",
	    						 "WP.quantity AS Quantity",
	    						 "WP.status AS Status",
                                 "WP.ref_no AS Refno"
	    						
	    					)
	    				->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.product_id')
	    				//->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
	    				->leftJoin('jocom_product_price AS JPP', function($join)
                                    {
                                        $join->on('JPP.product_id', '=', 'WP.product_id')
                                        ->where('JPP.status', '!=', 2);
                                    })
	    				->where('WP.base_product_id','=',$productid)
                        ->orwhere('WP.parent_product_id','=',$productid)
                        // ->where(function ($query){
                        //         $query->where('WP.base_product_id',$productid)
                        //               ->orwhere('WP.parent_product_id',$productid);
                        //        })
	    			   
	    				->where('WP.status','!=','2')
	    				->orderby('WP.nodeposition','ASC')
	    				->get();

	    				// print_r($result);

	    return $result;				
	    
    }

    public static function getProductsbaselinksup($productid){

        // $result = DB::table('jocom_warehouse_productslinks')
        //          ->select('product_id','parent_product_id','nodelevel','quantity')
        //          ->where('parent_product_id','=',$productid)
        //          ->where('status','!=','2')
        //          ->get();
        // return $result;


        $result = DB::table('jocom_warehouse_products_baselinks AS WP')
                        ->select("WP.variant_product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WP.default AS Default",
                                 "WP.status AS Status",
                                 "WP.ref_no AS Refno"
                                
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.variant_product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.variant_product_id')
                        ->where('WP.product_id','=',$productid)
                         ->where('JPP.status','!=','2')
                        ->where('WP.status','!=','2')
                        ->orderby('WP.default','DESC')
                        ->get();

                        // print_r($result);

        return $result;             
        
    }


    public static function getInventoryActualstock($productid){

    	$actualstock = 0;

    	$result = DB::table('jocom_warehouse_inventoryadjustments')
    				->select('newstock')	
    				->orderby('created_at','DESC')
    				->where('product_id','=',$productid)
    				->first();
    	if(count($result)==0){

    		$resultpm = Warehouse::where('product_id','=',$productid)
	    			  	->select('stockin_hand')
	    			  	->first();
    		$actualstock = $resultpm->stockin_hand; 	  	
    	}
    	else{
    		$actualstock = $result->newstock;
    	}

    	return $actualstock;


    }

    public static function getInventorydetails(){

    	$MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
           
        }
        if (isset($regionid) && $regionid ==0){
	    	$result = DB::table('jocom_warehouse_products AS WP')
	    				->select("WP.product_id AS ProductID",
	    						 "JP.name AS ProductName",
	    						 "JPP.label AS Label",
	    						 "WP.stockin_hand AS StockInHand",
	    						 "JPP.stock_unit AS Measurement",
	    						 "WP.insert_by AS CreatedBy",
	    						 "WP.created_at AS CreatedAt",
	    						 "WP.modify_by AS ModifiedBy",
	    						 "WP.updated_at AS UpdatedAt"
	    					)
	    				->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.product_id')
	    				->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
	    				->orderby('WP.product_id','DESC')
	    				->get();
	    }
	    else if (isset($regionid) && $regionid ==1){
	    	$result = DB::table('jocom_warehouse_products AS WP')
	    				->select("WP.product_id AS ProductID",
	    						 "JP.name AS ProductName",
	    						 "JPP.label AS Label",
	    						 "WP.stockin_hand AS StockInHand",
	    						 "JPP.stock_unit AS Measurement",
	    						 "WP.insert_by AS CreatedBy",
	    						 "WP.created_at AS CreatedAt",
	    						 "WP.modify_by AS ModifiedBy",
	    						 "WP.updated_at AS UpdatedAt"
	    					)
	    				->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.product_id')
	    				->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
	    				// ->where('WP.region_id','=',$regionid)
	    				->where(function ($query){
                                $query->where('WP.region_id','=',0)
                                      ->orwhere('WP.region_id','=',1);
                               })
	    				->orderby('WP.product_id','DESC')
	    				->get();
	    }
	    else{

	    	$result = DB::table('jocom_warehouse_products AS WP')
	    				->select("WP.product_id AS ProductID",
	    						 "JP.name AS ProductName",
	    						 "JPP.label AS Label",
	    						 "WP.stockin_hand AS StockInHand",
	    						 "JPP.stock_unit AS Measurement",
	    						 "WP.insert_by AS CreatedBy",
	    						 "WP.created_at AS CreatedAt",
	    						 "WP.modify_by AS ModifiedBy",
	    						 "WP.updated_at AS UpdatedAt"
	    					)
	    				->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.product_id')
	    				->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
	    				->where('WP.region_id','=',$regionid)
	    				->orderby('WP.product_id','DESC')
	    				->get();

	    }				

   		return $result; 				
    }

    public static function getStockinDetails($fromdate,$todate,$reptype){

    	$MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
           
        }

        if(isset($reptype) && $reptype == 2){
            //STOCKIN 
            if (isset($regionid) && $regionid ==0){

            $result = DB::table('jocom_warehouse_stockin AS WSI')
                        ->select("WSI.product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WSI.unit AS Qty",
                                 "WSI.unitprice AS UnitPrice",
                                 "WSI.total AS Total",
                                 "WSI.remark AS Remark",
                                 "WSI.expiry_date AS Expirydate",
                                 "WSI.insert_by AS CreatedBy",
                                 "WSI.created_at AS CreatedAt"
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSI.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSI.product_id')
                        ->where('WSI.created_at','>=',$fromdate)
                        ->where('WSI.created_at','<=',$todate)
                        ->where('JPP.status','=',1)
                        // ->where('WSI.region_id','=',$regionid)
                        ->orderby('WSI.created_at','DESC')
                        ->get();



             }
             else if (isset($regionid) && $regionid ==1){
                $result = DB::table('jocom_warehouse_stockin AS WSI')
                            ->select("WSI.product_id AS ProductID",
                                     "JP.name AS ProductName",
                                     "JPP.label AS Label",
                                     "WSI.unit AS Qty",
                                     "WSI.unitprice AS UnitPrice",
                                     "WSI.total AS Total",
                                     "WSI.remark AS Remark",
                                     "WSI.expiry_date AS Expirydate",
                                     "WSI.insert_by AS CreatedBy",
                                     "WSI.created_at AS CreatedAt"
                                )
                            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSI.product_id')
                            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSI.product_id')
                            ->where('WSI.created_at','>=',$fromdate)
                            ->where('WSI.created_at','<=',$todate)
                            ->where('JPP.status','=',1)
                            ->where(function ($query){
                                    $query->where('WSI.region_id','=',0)
                                          ->orwhere('WSI.region_id','=',1);
                                   })
                            // ->where('WSI.region_id','=',$regionid)
                            ->orderby('WSI.created_at','DESC')
                            ->get();

             }
             else {

                $result = DB::table('jocom_warehouse_stockin AS WSI')
                            ->select("WSI.product_id AS ProductID",
                                     "JP.name AS ProductName",
                                     "JPP.label AS Label",
                                     "WSI.unit AS Qty",
                                     "WSI.unitprice AS UnitPrice",
                                     "WSI.total AS Total",
                                     "WSI.remark AS Remark",
                                     "WSI.expiry_date AS Expirydate",
                                     "WSI.insert_by AS CreatedBy",
                                     "WSI.created_at AS CreatedAt"
                                )
                            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSI.product_id')
                            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSI.product_id')
                            ->where('WSI.created_at','>=',$fromdate)
                            ->where('WSI.created_at','<=',$todate)
                            ->where('WSI.region_id','=',$regionid)
                            ->where('JPP.status','=',1)
                            ->orderby('WSI.created_at','DESC')
                            ->get();

             }
         //END


        }else if(isset($reptype) && $reptype == 3){
            //STOCKOUT
            if (isset($regionid) && $regionid ==0){

            $result = DB::table('jocom_warehouse_stockout AS WSO')
                        ->select("WSO.product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WSO.stockout_unit AS StockOutUnit",
                                 "LD.name AS DriverName",
                                 "WSO.remark AS Remark",
                                 "WSO.insert_by AS CreatedBy",
                                 "WSO.created_at AS CreatedAt"
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSO.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSO.product_id')
                        ->leftjoin('logistic_driver AS LD','LD.id','=','WSO.driver_id')
                        ->where('WSO.created_at','>=',$fromdate)
                        ->where('WSO.created_at','<=',$todate)
                        ->where('JPP.status','=',1)
                        // ->where('WSI.region_id','=',$regionid)
                        ->orderby('WSO.created_at','DESC')
                        ->get();



             }
             else if (isset($regionid) && $regionid ==1){
                $result = DB::table('jocom_warehouse_stockout AS WSO')
                        ->select("WSO.product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WSO.stockout_unit AS StockOutUnit",
                                 "LD.name AS DriverName",
                                 "WSO.remark AS Remark",
                                 "WSO.insert_by AS CreatedBy",
                                 "WSO.created_at AS CreatedAt"
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSO.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSO.product_id')
                        ->leftjoin('logistic_driver AS LD','LD.id','=','WSO.driver_id')
                        ->where('WSO.created_at','>=',$fromdate)
                        ->where('WSO.created_at','<=',$todate)
                        ->where('JPP.status','=',1)
                        ->where(function ($query){
                                $query->where('WSO.region_id','=',0)
                                      ->orwhere('WSO.region_id','=',1);
                               })
                        // ->where('WSI.region_id','=',$regionid)
                        ->orderby('WSO.created_at','DESC')
                        ->get();

             }
             else {

                $result = DB::table('jocom_warehouse_stockout AS WSO')
                        ->select("WSO.product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WSO.stockout_unit AS StockOutUnit",
                                 "LD.name AS DriverName",
                                 "WSO.remark AS Remark",
                                 "WSO.insert_by AS CreatedBy",
                                 "WSO.created_at AS CreatedAt"
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSO.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSO.product_id')
                        ->leftjoin('logistic_driver AS LD','LD.id','=','WSO.driver_id')
                        ->where('WSO.created_at','>=',$fromdate)
                        ->where('WSO.created_at','<=',$todate)
                        ->where('WSO.region_id','=',$regionid)
                        ->where('JPP.status','=',1)
                        ->orderby('WSO.created_at','DESC')
                        ->get();

             }

            //END

        }else if(isset($reptype) && $reptype == 4){

            //STOCKRETURN
            if (isset($regionid) && $regionid ==0){

            $result = DB::table('jocom_warehouse_stockreturn AS WSR')
                        ->select("WSR.product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WSR.stockreturn_unit AS StockReturnUnit",
                                 "LD.name AS DriverName",
                                 "WSR.remark AS Remark",
                                 "WSR.insert_by AS CreatedBy",
                                 "WSR.created_at AS CreatedAt"
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSR.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSR.product_id')
                        ->leftjoin('logistic_driver AS LD','LD.id','=','WSR.driver_id')
                        ->where('WSR.created_at','>=',$fromdate)
                        ->where('WSR.created_at','<=',$todate)
                        ->where('JPP.status','=',1)
                        // ->where('WSI.region_id','=',$regionid)
                        ->orderby('WSR.created_at','DESC')
                        ->get();



             }
             else if (isset($regionid) && $regionid ==1){
                $result = DB::table('jocom_warehouse_stockreturn AS WSR')
                        ->select("WSR.product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WSR.stockreturn_unit AS StockReturnUnit",
                                 "LD.name AS DriverName",
                                 "WSR.remark AS Remark",
                                 "WSR.insert_by AS CreatedBy",
                                 "WSR.created_at AS CreatedAt"
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSR.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSR.product_id')
                        ->leftjoin('logistic_driver AS LD','LD.id','=','WSR.driver_id')
                        ->where('WSR.created_at','>=',$fromdate)
                        ->where('WSR.created_at','<=',$todate)
                        ->where('JPP.status','=',1)
                        ->where(function ($query){
                                $query->where('WSR.region_id','=',0)
                                      ->orwhere('WSR.region_id','=',1);
                               })
                        // ->where('WSI.region_id','=',$regionid)
                        ->orderby('WSR.created_at','DESC')
                        ->get();

             }
             else {

                $result = DB::table('jocom_warehouse_stockreturn AS WSR')
                        ->select("WSR.product_id AS ProductID",
                                 "JP.name AS ProductName",
                                 "JPP.label AS Label",
                                 "WSR.stockreturn_unit AS StockReturnUnit",
                                 "LD.name AS DriverName",
                                 "WSR.remark AS Remark",
                                 "WSR.insert_by AS CreatedBy",
                                 "WSR.created_at AS CreatedAt"
                            )
                        ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WSR.product_id')
                        ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WSR.product_id')
                        ->leftjoin('logistic_driver AS LD','LD.id','=','WSR.driver_id')
                        ->where('WSR.created_at','>=',$fromdate)
                        ->where('WSR.created_at','<=',$todate)
                        ->where('WSR.region_id','=',$regionid)
                        ->where('JPP.status','=',1)
                        ->orderby('WSR.created_at','DESC')
                        ->get();

             }

            //END

        }


        return $result;
         

    }

    public static function genReferenceno(){

            $i_counter = 0;

            $running = DB::table('jocom_warehouse_running')
                        ->select('*')
                        ->where('value_key', '=', 'wh_ref_no')->first();

            $i_counter = $running->counter + 1;
            $sql = DB::table('jocom_warehouse_running')
                        ->where('value_key', 'wh_ref_no')
                        ->update(array('counter' => $i_counter));
                    
            return $i_counter;            
    }  

    public static function getBaseLinks($productID){
        $data = array(); 
        $refno      = 0;
        $default    = 1;

        // echo $productID.'Ok1';

        $result = DB::table('jocom_warehouse_products_baselinks')
                    ->where('product_id','=',$productID)   
                    ->get();

        if(count($result)==0){
            $refno = self::genReferenceno();
            DB::table('jocom_warehouse_products_baselinks')->insert(array(
                            'variant_product_id'    => $productID,
                            'product_id'            => $productID,
                            'ref_no'                => $refno,
                            'default'               => $default, 
                            'insert_by'             => Session::get('username'),
                            'created_at'            => date('Y-m-d H:i:s'),
                            'modify_by'             => Session::get('username'),
                            'updated_at'            => date('Y-m-d H:i:s')
                            )
                    );

        }


        $resultfinal = DB::table('jocom_warehouse_products_baselinks')
                    ->where('product_id','=',$productID)   
                    ->get();

        if(count($resultfinal)>0){

            foreach ($resultfinal as $value) {
                $arraytemp = array('product_id' => $value->variant_product_id , );
                 array_push($data, $arraytemp);
            }

           

        }

        return $data;


    } 
    
    public static function getRootID($productID){
        $rootno = 0;
        $baseresult = DB::table('jocom_warehouse_products_baselinks')
                            ->where('variant_product_id','=',$productID)
                            ->first();  
        if(count($baseresult)>0){
          $rootno = $baseresult->product_id;
        }
        else{
          $basicresult = DB::table('jocom_warehouse_productslinks')
                            ->where('product_id','=',$productID)
                            ->first();  
              if(count($basicresult)>0){
                 $rootno = $basicresult->base_product_id;
              }
        }
        return $rootno;

    }
    
    public static function getStocklevelupdate($productid,$stock=0,$type){

        $stockinhand = 0; 
        $warehouseid = 0;

        $whole = 0; 
        $decimal = 0; 

        $result = Warehouse::where('product_id','=',$productid)
                             ->where('reorderrule','=',0)
                             ->where('status','=',1)
                             ->where('active','=',1)
                             ->first();

        if(count($result)>0){
            $warehouseid = $result->id;

            if($type == 1){
              list($whole,$decimal)  = explode('.', ($stock * 0.2));  
            }
            elseif ($type == 2) {

               $links = DB::table('jocom_warehouse_products_baselinks')
                    ->where('product_id','=',$productID)   
                    ->first();

                if(count($links)>0){
                    $baseid = $links->parent_product_id;

                    $resultlinks = Warehouse::where('product_id','=',$baseid)
                                   ->where('reorderrule','=',0)
                                   ->where('status','=',1)
                                   ->where('active','=',1)
                                   ->first();

                    if(count($resultlinks)>0){
                      $warehouseid = $resultlinks->id;
                      list($whole,$decimal)  = explode('.', ($stock * 0.2));  

                    }


                }

            }

            

            if($whole > 0){
                $warehouse = Warehouse::find($warehouseid);
                $warehouse->reorderrule = 0; 
                $warehouse->stocklevel = $whole; 
                $warehouse->save();
            }

        }


    }
    
    public static function Stocklevelnotification(){
      try{

        $regionid = 0;
          $regionhq = 0;
          $region = "";
          
          $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
         
          foreach ($SysAdminRegion as  $value) {
              $regionid = $value;
             
          }
          


          if (isset($regionid) && $regionid ==0){

            $products = DB::table('jocom_warehouse_products AS WP')
                          ->select("WP.product_id",
                                   "JP.sku",
                                   "JP.name",
                                   "JPP.label",
                                   "WP.stockin_hand"
                              )
                          ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.product_id')
                // ->leftjoin('jocom_warehouse_product_reorderlevel AS PRL', 'WP.product_id', '=', 'PRL.product_id')
                ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
                //->whereRaw('WP.stockin_hand', '<', 'WP.stocklevel')
                ->whereRaw('WP.stockin_hand < WP.stocklevel')
                ->where('WP.status','=',1)
                ->where('WP.reorderrule','=',0)
                ->get();

           $productsreordlevel = ProductsReorderLevel::select('jocom_warehouse_product_reorderlevel.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand')
                    ->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->leftjoin('jocom_warehouse_products', 'jocom_warehouse_products.product_id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->where('jocom_warehouse_products.stockin_hand', '<', 'jocom_warehouse_product_reorderlevel.level')
                    ->where('jocom_warehouse_products.reorderrule','=',1)
                    ->get();
                    
                    
        } else if (isset($regionid) && $regionid ==1){
          $products = DB::table('jocom_warehouse_products AS WP')
                          ->select("WP.product_id",
                                   "JP.sku",
                                   "JP.name",
                                   "JPP.label",
                                   "WP.stockin_hand"
                              )
                          ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.product_id')
                // ->leftjoin('jocom_warehouse_product_reorderlevel AS PRL', 'WP.product_id', '=', 'PRL.product_id')
                ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
                ->whereRaw('WP.stockin_hand < WP.stocklevel')
                ->where(function ($query){
                                    $query->where('WP.region_id','=',0)
                                          ->orwhere('WP.region_id','=',1);
                                   })
                ->where('WP.status','=',1)
                ->where('WP.reorderrule','=',0)
                ->get();

                $productsreordlevel = ProductsReorderLevel::select('jocom_warehouse_product_reorderlevel.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand')
                    ->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->leftjoin('jocom_warehouse_products', 'jocom_warehouse_products.product_id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->where('jocom_warehouse_products.stockin_hand', '<', 'jocom_warehouse_product_reorderlevel.level')
                    ->where(function ($query){
                                    $query->where('jocom_warehouse_product_reorderlevel.region_id','=',0)
                                          ->orwhere('jocom_warehouse_product_reorderlevel.region_id','=',1);
                                   })
                    ->where('jocom_warehouse_products.reorderrule','=',1)
                    ->get();

           
        }else{
          $products = DB::table('jocom_warehouse_products AS WP')
                          ->select("WP.product_id",
                                   "JP.sku",
                                   "JP.name",
                                   "JPP.label",
                                   "WP.stockin_hand"
                              )
                          ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'WP.product_id')
                // ->leftjoin('jocom_warehouse_product_reorderlevel AS PRL', 'WP.product_id', '=', 'PRL.product_id')
                ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'WP.product_id')
                ->whereRaw('WP.stockin_hand < WP.stocklevel')
                ->where('WP.region_id','=',$regionid)
                ->where('WP.status','=',1)
                ->where('WP.reorderrule','=',0)
                ->get();

          $productsreordlevel = ProductsReorderLevel::select('jocom_warehouse_product_reorderlevel.product_id','jocom_products.sku','jocom_products.name','jocom_product_price.label','jocom_warehouse_products.stockin_hand')
                    ->leftjoin('jocom_products', 'jocom_products.id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->leftjoin('jocom_warehouse_products', 'jocom_warehouse_products.product_id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->leftjoin('jocom_product_price', 'jocom_product_price.product_id', '=', 'jocom_warehouse_product_reorderlevel.product_id')
                    ->where('jocom_warehouse_products.stockin_hand', '<', 'jocom_warehouse_product_reorderlevel.level')
                    ->where('jocom_warehouse_product_reorderlevel.region_id','=',$regionid)
                    ->where('jocom_warehouse_products.reorderrule','=',1)
                    ->get();

        }

       

        $fileName = 'stocklevel_notification'.date('Y-m-d H:i:s').".csv";
          $path = Config::get('constants.CSV_FILE_PATH');
          $file = fopen($path.'/'.$fileName, 'w');

          fputcsv($file, ['product_id', 'sku','name','label', 'stockin_hand']);

          foreach ($products as $row)
          {   
                  fputcsv($file, [
                    $row->product_id,
                      $row->sku,
                      $row->name,
                      $row->label,
                      $row->stockin_hand                     
                  ]);  
              
          }

          if(count($productsreordlevel)> 0) {

            foreach ($productsreordlevel as $row)
            {   
                    fputcsv($file, [
                      $row->product_id,
                        $row->sku,
                        $row->name,
                        $row->label,
                        $row->stockin_hand                     
                    ]);  
                
            } 
          }

          


          
          fclose($file);
          
          $test = Config::get('constants.ENVIRONMENT');
          if ($test == 'test')
              $mail = ['maruthu@tmgrocer.com'];
          else
              $mail = ['quenny.leong@tmgrocer.com'];

          $subject = "Stock Level Notification : " . $fileName;
          $attach = $path . "/" . $fileName;
          $body = array('title' => 'Stock Level');
          
          Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
              {
                  $message->from('notification@tmgrocer.com', 'tmGrocer');
                  $message->to($mail, '')->subject($subject);
                  $message->attach($attach);
              }
          );





      } catch (Exception $ex) {
             //  echo $ex->getMessage();
        }

   }
   
   // $product_id can be either stock product or package product
    // $action can be either 'increase' or 'decrease'
    public static function manageProductstock($product_id, $price_id, $quantity, $action, $username = '') {

        $base_items = DB::table('jocom_product_base_item AS bi')
                        ->where("bi.product_id", $product_id)
                        ->where("bi.price_option_id", $price_id)
                        ->where("bi.status", 1)
                        ->select('bi.product_base_id', 'bi.quantity')
                        ->get();

        if (count($base_items) > 0) {
            foreach ($base_items as $base_item) {
                Warehouse::manageWarehouse($base_item->product_base_id, $quantity * $base_item->quantity, $action, $username);
            }
        } else {
            Warehouse::manageWarehouse($product_id, $quantity, $action, $username);
        }

    }

    // $product_id is stock product id
    // $action can be either 'increase' or 'decrease'
    public static function manageWarehouse($product_id, $quantity, $action, $username) {
        $product_link = DB::table('jocom_warehouse_productslinks')
                            ->where('product_id', '=', $product_id)
                            ->select('parent_product_id', 'quantity')
                            ->first();

        if ($product_link != null) {

            $current_stock = DB::table('jocom_warehouse_products')
                              ->where('product_id', '=', $product_id)
                              ->select('stockin_hand')
                              ->first();

            $product_stock = $current_stock->stockin_hand;
            $parent_product_id = $product_link->parent_product_id;

            $carton = intval($quantity / $product_link->quantity);
            $loosed = $quantity % $product_link->quantity;

            if ($action == 'increase') {
                // DeActivated on 22/12/2021
                // Warehouse::increaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $product_link->quantity);
                // Warehouse::stockinLog($product_id, $quantity, $username);
            } else {
                // DeActivated on 16/06/2021
                // Warehouse::decreaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $product_link->quantity);
                // Warehouse::stockoutLog($product_id, $quantity, $username);
            }
        
        } else {
            $query = DB::table('jocom_warehouse_products')
                      ->where('product_id', '=', $product_id);

            if ($action == 'increase') {
                // DeActivated on 22/12/2021
                // $query->increment('stockin_hand', $quantity);
                // Warehouse::stockinLog($product_id, $quantity, $username);
            } else {
                // DeActivated on 16/06/2021
                // $query->decrement('stockin_hand', $quantity);
                // Warehouse::stockoutLog($product_id, $quantity, $username);
                
            }
        }
    }

    private function increaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $carton_size) {
        if ($loosed > 0) {

            $loose = $loosed + $product_stock;
            if ($loose >= $carton_size) {
                $loose = $loose % $carton_size;
                $carton = $carton + 1;
            }

            DB::table('jocom_warehouse_products')
              ->where('product_id', '=', $product_id)
              ->update(['stockin_hand' => $loose]);
        } 

        DB::table('jocom_warehouse_products')
            ->where('product_id', '=', $parent_product_id)
            ->increment('stockin_hand', $carton);
    }

    private function decreaseLooseItem($product_id, $quantity, $product_stock, $parent_product_id, $carton, $loosed, $carton_size) {
        if ($loosed > 0) {
            if ($loosed > $product_stock) {
                $loose = $carton_size - $loosed + $product_stock;
                $carton = $carton + 1;
            } else {
                $loose = $product_stock - $loosed;
            }

            DB::table('jocom_warehouse_products')
              ->where('product_id', '=', $product_id)
              ->update(['stockin_hand' => $loose]);
        } 

        DB::table('jocom_warehouse_products')
            ->where('product_id', '=', $parent_product_id)
            ->decrement('stockin_hand', $carton);
    }

    public static function stockinLog($product_id, $quantity, $username, $unit_price = 0, $total = 0, $remark = 'returned,undelivered,cancelled') {
        $regionid = 0;
        if ($username == '') {
          $username = Session::get('username');
        }
        $SysAdminRegion = UserController::getSysRegionList($username);

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
        }

        DB::table('jocom_warehouse_stockin')->insert([
          'product_id' => $product_id,
          'unit' => $quantity,
          'expiry_date' => date('Y-m-d H:i:s'),
          'unitprice' => $unit_price,
          'total' => $total,
          'remark' => $remark,
          'region_country_id' => 458,  
          'region_id' => $regionid,
          'insert_by' => $username,
          'created_at' => date('Y-m-d H:i:s'), 
        ]);
    }

    public static function stockoutLog($product_id, $quantity, $username, $driver_id = 1, $remark = '') {
        $regionid = 0;
        if ($username == '') {
          $username = Session::get('username');
        }
        $SysAdminRegion = UserController::getSysRegionList($username);

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
        }

        DB::table('jocom_warehouse_stockout')->insert([
          'product_id' => $product_id,
          'stockout_unit' => $quantity,
          'driver_id' => $driver_id,
          'remark' => $remark,
          'region_country_id' => 458,  
          'region_id' => $regionid,
          'insert_by' => $username,
          'created_at' => date('Y-m-d H:i:s'),
        ]);
    }

}
?>