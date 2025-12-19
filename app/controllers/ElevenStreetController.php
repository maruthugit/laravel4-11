<?php

class ElevenStreetController extends BaseController
{
    const DATE_FORMAT_ymdhhmm = "1";
    
    /*
     * @Desc    : This function is to call API from 11Street to return list of confirmed order from 11Street
     * @param   : fromDate (string : DDMMYYYYHHMM) // Base on confirmation date
     * @param   : toDate (string : DDMMYYYYHHMM)// Base on confirmation date
     * @Note    : API only application for 1 week period only.
     */
    public function getOrders($fromDate,$toDate,$listType = 1,$accountType = 1){
        
        // Get API Key
        switch ($accountType) {
            case 1:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_1');  
                $from_account = 1;
                break;
            case 2:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_2');  
                $from_account = 2;

                break;
            case 3:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_FN');  
                $from_account = 3;

                break;
                
            case 4: // COCA COLA
         
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_COCACOLA');  
                $from_account = 4;

                break;
                
            case 5: // SPRITZER
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_SPRITZER');  
                $from_account = 5;

                break;
            
            case 6: // CACTUS
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_CACTUS');  
                $from_account = 6;

                break;
                
            case 7: // F&N CREAMERIES
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_FNCREAMERIES');  
                $from_account = 7;
                break;
                
            case 8: // Starbuck
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_STARBUCK');  
                $from_account = 8;

                break;
                
            case 9: // POKKA
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_POKKA');  
                $from_account = 9;

                break;
                
            case 10: // YEOS
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_YEOS');  
                $from_account = 10;
                break;
                
            case 11: // ORIENTAL
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_ORIENTAL');  
                $from_account = 11;
                break;
                
            case 12: // KAWAN FOOD
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_KAWANFOOD');  
                $from_account = 12;
                break;
            
            case 13: // NIKUDO SEA FOOD
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_NIKUDO');  
                $from_account = 13;
                break;
            
            case 14: // ETIKA
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_ETIKA');  
                $from_account = 14;
                break;
            
            default:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_1');  
                $from_account = 1;
                break;
        }
        
        //echo $ElevenStreetAPIKEY;
        //echo "FROM :".$from_account;
        //die();
//        
        $orderCollection = [];
        $weekNumber = 0;
        $weeks = array();
     
        $start = new DateTime((string)$fromDate);
        
        $end = new DateTime((string)$toDate);
  
        $interval = new DateInterval('P1D');
        $dateRange = new DatePeriod($start, $interval, $end);
        
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
        
        // Create collection based on weekly period
        foreach ($weeks as $key => $value){
          
            if($key==0){
                $startDateTime = $this->ConvertDateTimeFormat($fromDate, self::DATE_FORMAT_ymdhhmm);
            }else{
                $date = array_shift($weeks[$key])." 00:00";
                $startDateTime = $this->ConvertDateTimeFormat($date, self::DATE_FORMAT_ymdhhmm);
            }
            
            $endDateTime = $this->ConvertDateTimeFormat(array_pop($weeks[$key])." 23:59", self::DATE_FORMAT_ymdhhmm);
            
            // Call 11Street API
            if($listType == 1){
            /* API URL : SHIPPING IN PROGRESS  */
            //  $URL = "https://api.11street.my/rest/ordservices/dlvcompleted/".$startDateTime."/".$endDateTime; 
            //Amended dated: 17-06-2019
            $URL = "https://api.prestomall.com/rest/ordservices/dlvcompleted/".$startDateTime."/".$endDateTime; 
            }else{
                /* API URL : CONFIRMED ORDERS API URL */
               //  $URL = "https://api.11street.my/rest/ordservices/packaging/".$startDateTime."/".$endDateTime; 
                //Amended dated: 17-06-2019
                $URL = "https://api.prestomall.com/rest/ordservices/packaging/".$startDateTime."/".$endDateTime; 
            }
        //     echo $URL;
        //   echo $ElevenStreetAPIKEY;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                'Contenttype: application/xml',
                'openapikey: '.$ElevenStreetAPIKEY
            ));

            curl_setopt($ch,CURLOPT_URL,$URL );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 1);
            $output = curl_exec($ch);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            /*
            echo "<pre>";
            print_r($httpcode);
            echo "</pre>";
            echo "<pre>";
            print_r(curl_getinfo($ch));
            echo "</pre>";
            */
            
            // curl_close($ch);
            // echo "<pre>";
            // print_r($output);
            // echo "</pre>";
            // echo "<pre>";
            // print_r($URL);
            // echo "</pre>";
          
            // Load returned result and get namespaces
            $xml = simplexml_load_string($output);
            $namespaces = $xml->getNamespaces(true);

            // Create collection
            $holderSubline = array();
            $index = 0;
            
            foreach($xml->children($namespaces['ns2']) as $child) {
                
                $index++; 
                $SubOrder = get_object_vars($child->children());
                //echo "<pre>";
                //print_r($SubOrder);
                //echo "</pre>";
                $orderNum = $SubOrder['ordNo'];

                //$OrderRecord  = ElevenStreetOrder::findByOrderNumber($SubOrder['ordNo']);
                $OrderRecord  = ElevenStreetOrder::where('order_number', $SubOrder['ordNo'])
                                ->where('from_account', $from_account)
                                ->where('activation', 1)
                                ->first();

                if($OrderRecord->id > 0){
            //echo $SubOrder['ordNo']."</br>";
                    // Update email address of the user 
//                    $OrderRecord->customer_email = $SubOrder['ordId'];
//                    $OrderRecord->save();

                }else{
                    
                if((count($holderSubline) > 0 ) && ($holderSubline['order_number'] !== $orderNum)){
                    array_push($orderCollection, $holderSubline);
                    $holderSubline = array();
                    $orderNum = $SubOrder['ordNo'];
                }
                
                if(!array_search($SubOrder['ordNo'],$holderSubline)){
                        // Check if the order already in database 

                    $holderSubline['order_number'] = $SubOrder['ordNo'];
                            $holderSubline['migrate_from'] = $listType;
                    $holderSubline['customer_name'] = $SubOrder['ordNm'];
                    
                    if(!empty($SubOrder['ordId'])){
                        $holderSubline['customer_email'] = $SubOrder['ordId'];
                    }else{
                        $holderSubline['customer_email'] = "";
                    }
                    $holderSubline['product_info'][] = $SubOrder;
                    

                }else{
                    if($SubOrder['ordNo'] == $orderNum){
                        $holderSubline['product_info'][] = $SubOrder;
                    }
                }
                
                if($index == count($xml->children($namespaces['ns2']))){
                    array_push($orderCollection, $holderSubline);
                }
                
            }

        }
        
        }
        
        // return collection

        return $orderCollection;
        
    }
    
    /*
     * @Desc    : View list of confirmed orders
     */
    public function index(){
        
        $isError = 0;
        $message = "";
        
        try{
            // Added by Maruthu    
            if ( ! file_exists(Config::get('constants.XML_FILE_PATH').'state.xml')) {
                $statedb = ElevenStreetOrder::getState();
                $xml = new XMLWriter();
                $xml->openURI(Config::get('constants.XML_FILE_PATH').'state.xml');
                $xml->startDocument('1.0');
                $xml->startElement('statedb');

                foreach ($statedb as $value) {
                    $xml->startElement('value');
                    $xml->writeElement('id', $value->id);
                    $xml->writeElement('state_code', $value->state_code);
                    $xml->writeElement('state_name', $value->state_name);
                    $xml->endElement();
                }

                $xml->endElement();
                $xml->endDocument();
                $xml->flush();

                // Session::flash('success', 'success.');
            }   


            if ( ! file_exists(Config::get('constants.XML_FILE_PATH').'postcode.xml')) {
                $postcode = ElevenStreetOrder::getPostcode();
                $xml = new XMLWriter();
                $xml->openURI(Config::get('constants.XML_FILE_PATH').'postcode.xml');
                $xml->startDocument('1.0');
                $xml->startElement('postcode');

                foreach ($postcode as $value) {
                    $xml->startElement('value');
                    $xml->writeElement('id', $value->id);
                    $xml->writeElement('postcode', $value->postcode);
                    $xml->writeElement('area', $value->area);
                    $xml->writeElement('post_office', $value->post_office);
                    $xml->writeElement('state_code', $value->state_code);
                    $xml->endElement();
                }

                $xml->endElement();
                $xml->endDocument();
                $xml->flush();

                // Session::flash('success', 'success.');
            } 
            
        } catch (Exception $ex) {
            $isError = 1;
        }finally{
            return View::make('elevenstreet.index')->with(
                    [
                    "error" => $isError,
                    "message" => $message
                    ]
                );
        }
        
        
    }
    
    
    /*
     * @Desc    : To list out list of confirmed orders
     * @Param   : None
     * @Return  : (DATATABLE) format
     */
    public function Orders() {
        
        // Get Orders
        
        $orders = DB::table('jocom_elevenstreet_order')->select(array(
                        'jocom_elevenstreet_order.id','jocom_elevenstreet_order.order_number','jocom_elevenstreet_order.customer_name','jocom_elevenstreet_order.customer_email','jocom_elevenstreet_order.status','jocom_elevenstreet_order.transaction_id','jocom_elevenstreet_order_details.api_result_return'
                        ))
                    ->leftJoin('jocom_elevenstreet_order_details', 'jocom_elevenstreet_order_details.order_id', '=', 'jocom_elevenstreet_order.id')
                    ->where('jocom_elevenstreet_order.activation',1)
                    ->groupBy('jocom_elevenstreet_order.id')
                    ->orderBy('jocom_elevenstreet_order.status','asc');
                    
        return Datatables::of($orders)
        ->add_column('recepient', function($orders){
            $api = json_decode($orders->api_result_return, true);
            return $api['rcvrNm'];
        })
        ->make(true);
        
    }
    
    /*
     * @desc    : To migrate orders in 11Street to jocom CMS
     * @return  : Json
     */
    public function migrateOrders(){
        
        $isError = 0;
        $OrderCollection = array();
        $message = "";
        
        try{
            
            $listType = Input::get('list_type');
            $accountType = Input::get('account_type');
        $toDate = DATE("Y-m-d")." 23:59";
            $fromDate = date("Y-m-d", strtotime("-2 week"))." 00:00";

            // Check latest recorded order
        
            $LatestRecord = ElevenStreetOrder::findLastByType($listType);
        /*     
        if(count($LatestRecord) > 0){
            // Add 1 minute
            $time = new DateTime($LatestRecord->created_at);
            $time->add(new DateInterval('PT1M'));
            $fromDate  = $time->format('Y-m-d H:i');
        }else{
            // get last created date time
            $fromDate = date("Y-m-d", strtotime("-20 week"))." 00:00";
        }

*/
            $OrderCollection = $this->getOrders($fromDate,$toDate,$listType,$accountType);
          
           
        if(count($OrderCollection) > 0 ){
            // Save new records
            $isSaved = $this->saveNewOrder($OrderCollection,$accountType);
        }
        
        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getTraceAsString();
        }
      
        finally {
            return array(
                "response" => $isError,
                "totalRecords" => count($OrderCollection),
                //"LatestRecord" => $OrderCollection,
                "message"=>$message,
                "charges"=>$isSaved
            );
        }
        
      
        
        
        
    }
    
    /*
     * @Desc    : This function use to import 11Street order in csv to saved in database
     * @Return  : boolean
     */
    public function importOrderByCSV(){
        
        $isError = 0;

        try{
            
            
            // Begin Transaction
            DB::beginTransaction();
            $orderCollection = [];
            // Get CSV file
            if (Input::hasFile('fileCSV'))
            {
                $CSVfile = Input::file('fileCSV');
                
                // Read file  and checking required column information
                if($_FILES["fileCSV"]["type"] == "text/csv"){
                    
                    $holderSubline = array();
                    $counterExisting = 0;
                    //Convert CSV into array type
                    $csvAsArray = array_map('str_getcsv', file($_FILES['fileCSV']['tmp_name']));
     
                    for ($index = 1;  $index < count($csvAsArray); $index++){
                        
                        $SubOrder = $csvAsArray[$index];
                        $order_number = $SubOrder[1];
                        $orderNum = $SubOrder[1];
                        
                        // check order number already exist or not 
                        $OrderInfo = ElevenStreetOrder::findByOrderNumber($order_number);
                        
                            $productInfo = array(
                                "ordAmt" => $SubOrder[19],   
                                "ordId" => "",  // Because exported excel file from 11Street do not provide this information
                                "ordNm" => $SubOrder[25],  
                                "ordNo" => $SubOrder[1],  
                                "ordPrdSeq" => $SubOrder[2],  
                                "ordPrtblTel" => $SubOrder[28],  
                                "ordStlEndDt" => $SubOrder[6],  
                                "prdNm" => $SubOrder[13],  
                                "prdNo" => $SubOrder[4],  
                               "ordQty" => $SubOrder[14],
                                "prdStckNo" => "",  
                                "rcvrBaseAddr" => $SubOrder[40],
                                "rcvrDtlsAddr" => "", // Because exported excel file from 11Street do not provide this information
                                "rcvrMailNo" => "", // Because exported excel file from 11Street do not provide this information
                                "rcvrNm" => $SubOrder[27],
                                "rcvrTlphn" => $SubOrder[29],
                                "selPrc" => $SubOrder[15],
                                "sellerPrdCd" => $SubOrder[3]
                            );
                        if(count($OrderInfo) <= 0 ){
                            
                            if((count($holderSubline) > 0 ) && ($holderSubline['order_number'] !== $order_number)){
                                array_push($orderCollection, $holderSubline);
                                $holderSubline = array();
                            }
                            
//                            $productInfo = array(
//                                "ordAmt" => $SubOrder[19],   
//                                "ordId" => "",  // Because exported excel file from 11Street do not provide this information
//                                "ordNm" => $SubOrder[25],  
//                                "ordNo" => $SubOrder[1],  
//                                "ordPrdSeq" => $SubOrder[2],  
//                                "ordPrtblTel" => $SubOrder[28],  
//                                "ordStlEndDt" => $SubOrder[6],  
//                                "prdNm" => $SubOrder[13],  
//                                "prdNo" => $SubOrder[4], 
//                                "ordQty" => $SubOrder[14],
//                                "prdStckNo" => "",  
//                                "rcvrBaseAddr" => $SubOrder[40],
//                                "rcvrDtlsAddr" => "", // Because exported excel file from 11Street do not provide this information
//                                "rcvrMailNo" => "", // Because exported excel file from 11Street do not provide this information
//                                "rcvrNm" => $SubOrder[27],
//                                "rcvrTlphn" => $SubOrder[29],
//                                "selPrc" => $SubOrder[15],
//                                "sellerPrdCd" => $SubOrder[3]
//                            );
                            
                            if(!array_search($order_number,$holderSubline)){
                                $holderSubline['order_number'] = $SubOrder[1];
                                $holderSubline['migrate_from'] = 3;
                                $holderSubline['customer_name'] = $SubOrder[25];
                                $holderSubline['customer_email'] = ""; // Because exported excel file from 11Street do not provide this information

                                $holderSubline['product_info'][] = $productInfo;
                            }else{
                                if($SubOrder['ordNo'] == $orderNum){
                                    $holderSubline['product_info'][] = $productInfo;
                                }
                            }
                            
                        }else{
                            $ElevenStreetOrderDetails = ElevenStreetOrderDetails::findElevenProductDetails($OrderInfo->id,$productInfo['prdNm']);
                            
                            if(count($ElevenStreetOrderDetails) > 0){
                                $ElevenStreetOrderDetails->api_result_return = json_encode($productInfo);
                                $ElevenStreetOrderDetails->save();
                            }
                            
                            $counterExisting++;
                        }
                        
                    }
                   // $isSaved = $this->saveNewOrder($orderCollection);
                }else{
                    throw new Exception("Uploaded file is invalid file type");
                }
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally{
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "response" =>  $isError ? 0 : 1,
                "data" => array(
                    "totalCSVRecord" => count($csvAsArray),
                    "totalRecord" => count($orderCollection),
                    "totalExisting" => count($csvAsArray) - count($orderCollection)
                    
                )
            );
        }
    }
    
    
    public function importOrderByCSV2(){
        
        $isError = 0;

        try{
            
            $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY'); 
            // Begin Transaction
            DB::beginTransaction();
            $orderCollection = [];
            $orderCollectionPos = array();
            (int)$counterRow = 0;
            // Get CSV file
            if (Input::hasFile('fileCSV'))
            {
                $CSVfile = Input::file('fileCSV');
//                print_r($CSVfile);
                // Read file  and checking required column information
                if($_FILES["fileCSV"]["type"] == "text/csv"){
                    
                    $holderSubline = array();
                    $counterExisting = 0;
                    //Convert CSV into array type
                    $csvAsArray = array_map('str_getcsv', file($_FILES['fileCSV']['tmp_name']));

     
                    for ($index = 0;  $index < count($csvAsArray); $index++){
                        
                        $SubOrder = $csvAsArray[$index];
                        $order_number = $SubOrder[1];
                        $product_number = $SubOrder[4];
                        $product_code = $SubOrder[3];
                        $orderNum = $SubOrder[1];

                        $OrderRecord1st  = ElevenStreetOrder::specialForce($order_number);
                       
                        if(count($OrderRecord1st) > 0 ){
                                
                                $ElevenStreetOrder = ElevenStreetOrder::find($OrderRecord1st->id);
                                $ElevenStreetOrder->activation = 0;
                                $ElevenStreetOrder->remarks = 'Deactivated : CSV Importation Replacement';
                                $ElevenStreetOrder->save();
                               
                        }

                        //var_dump(array_search($order_number, array_keys($orderCollectionPos)));
                        
                        $key = array_search($order_number, array_keys($orderCollectionPos));
                       
                        
                        if(array_search($order_number, array_keys($orderCollectionPos))){
                       
                            $key = array_search($order_number, array_keys($orderCollectionPos));
      
                            foreach ($orderCollection[$key]['product_info'] as $keyRep => $valueRep) {
                                if($valueRep['prdNo'] == $product_number ){
                                    $valueRep['sellerPrdCd'] = $product_code;
                                    $orderCollection[$key]['product_info'][$keyRep]['sellerPrdCd']= $product_code;
                                }
                            }
                            
                        }else{
                        
                        // Call 11Street API for order info 
                       // $URL = "http://api.11street.my/rest/ordservices/complete/".$order_number;
                       //Amended dated : 17-06-2019
                       $URL = "https://api.prestomall.com/rest/ordservices/complete/".$order_number;
                       
                        //echo $URL;
          
                        //echo $URL;
                        $ch = curl_init();
                        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                            'Contenttype: application/xml',
                            'AcceptCharset: utf8',
                            'openapikey: '.$ElevenStreetAPIKEY
                        ));

                        curl_setopt($ch,CURLOPT_URL,$URL );
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                        $output = curl_exec($ch);
                        curl_close($ch);

                        // Load returned result and get namespaces
                        $xml=simplexml_load_string($output);
                        $namespaces = $xml->getNamespaces(true);
                        
                        $counter = 0;
                        foreach($xml->children($namespaces['ns2']) as $child) {
                            
                            $counter++;
                            $SubOrder = get_object_vars($child->children());
                            
                            if($SubOrder['prdNo'] == $product_number){
                                 $SubOrder['sellerPrdCd'] = $product_code;
                            }
                           
                            $orderNum = $SubOrder['ordNo'];
                            
//                            print_r($SubOrder);
                            
                            $OrderRecord  = ElevenStreetOrder::findByOrderNumber($SubOrder['ordNo']);
                            
                            if($OrderRecord->id > 1 ){
                                
                               
                               
                            }else{
                                
                                
                                if((count($holderSubline) > 0 ) && ($holderSubline['order_number'] !== $orderNum) ){
                                   
                                    array_push($orderCollection, $holderSubline);
                                    $orderCollectionPos[$holderSubline['order_number']] = $holderSubline;
                                    $holderSubline = array();
                                    $orderNum = $SubOrder['ordNo'];
                                }
                                
                                if(array_search($SubOrder['ordNo'],$holderSubline)){
                                    $holderSubline['product_info'][] = $SubOrder;
                                    $counterRow++;
                                }

                                if(!array_search($SubOrder['ordNo'],$holderSubline)){
                                   
                                    // Check if the order already in database 
                                    $holderSubline['order_number'] = $SubOrder['ordNo'];
                                    $holderSubline['migrate_from'] = 3;
                                    $holderSubline['customer_name'] = $SubOrder['ordNm'];
                                    $holderSubline['customer_email'] = "";
                                    $holderSubline['product_info'][] = $SubOrder;
                                    $counterRow++;

                                }

                                if($counter == count($xml->children($namespaces['ns2']))){
                                    array_push($orderCollection, $holderSubline);
                                    $orderCollectionPos[$holderSubline['order_number']] = $holderSubline;
                                    $holderSubline = array();
                                }

                            }
                            
                            //echo "Counter: ".$counterRow;
                        }
                       } 
                    }
                    $isSaved = $this->saveNewOrder($orderCollection);
                }else{
                    throw new Exception("Uploaded file is invalid file type");
                }
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
            echo $message;
        }finally{
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "response" =>  $isError ? 0 : 1,
                "data" => array(
                    "totalCSVRecord" => count($csvAsArray),
                    "totalRecord" => $counterRow,
                    "totalExisting" => count($csvAsArray) - $counterRow
//                    "record" => $orderCollection
                )
            );
        }
    }
    
    
    public function revertOrderStatus(){
        
        $isError = 0;
        
        try{
            $order_id = Input::get('order_id');
            $ElevenStreetOrder = ElevenStreetOrder::find($order_id);
            $ElevenStreetOrder->status =  1;
            $ElevenStreetOrder->is_completed =  1;
            $ElevenStreetOrder->save();
            
        }catch (Exception $ex) {
            $isError = 1;
        }
        finally {
            return array(
                "response" =>  $isError,
                "data" => array(
                    "order_id" => $order_id
                )
            );
        }
        
    }
    
    private function saveNewOrder($OrderCollection,$from_account){
        
        $counter = 0;
        $isSaved = true;
        $manualProcess = false;
        $manualProcessOrder = array();
        $transferedProcessOrder = array();
        $elevenStreetDeliveryCharges = 0;
        set_time_limit(0);

        try{
            
            $tax_rate = Fees::get_tax_percent();
            
            foreach ($OrderCollection as $key => $value) {
            
                // Assign Value
                $order_number = $value['order_number'];
                $customer_name = $value['customer_name'];
                $customer_email = $value['customer_email'];
                $migrate_from = $value['migrate_from'];

                // saving order information 
                $ElevenStreetOrder = new ElevenStreetOrder;
                $ElevenStreetOrder->order_number = $order_number;
                $ElevenStreetOrder->customer_name = $customer_name;
                $ElevenStreetOrder->customer_email = $customer_email;
                $ElevenStreetOrder->transaction_id = 0;
                $ElevenStreetOrder->migrate_from = $migrate_from;
                $ElevenStreetOrder->is_completed = 1;
                $ElevenStreetOrder->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $ElevenStreetOrder->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                $ElevenStreetOrder->from_account = $from_account;
                $ElevenStreetOrder->save();
                
                $OrderID = $ElevenStreetOrder->id;
                // Save Product Details
                foreach ($value['product_info'] as $keySub => $valSub) {
                    
                    $delivery_postcode = $valSub['rcvrMailNo'];
                    $delivery_addr_1 = $valSub['rcvrDtlsAddr'];
                    $delivery_addr_2 = "" ; //$valSub['rcvrBaseAddr'];
                    $delivery_contact_no = $valSub['rcvrTlphn'];
                    $delivery_name = $valSub['rcvrNm'];
                    
                    $ElevenStreetOrderDetails = new ElevenStreetOrderDetails;
                    $ElevenStreetOrderDetails->order_id = $OrderID;
                    $ElevenStreetOrderDetails->product_name = $valSub['prdNm'];
                    $ElevenStreetOrderDetails->api_result_return = json_encode($valSub);
                    $ElevenStreetOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                    $ElevenStreetOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                    $ElevenStreetOrderDetails->save();
                
                }
                /**
                /* AUTOMATION TRANSACTION */
                /*
                 * 1. Create transaction
                 * 2. Create DO , Invoice, PO
                 * 3. Create Logistic Transaction
                 * 4. Update 11Street Order status to failed
                 * 4. Send Email for failed automation
                 */
            
                /*
                
                
                /* THIS LOCATION ID GET FROM MARUTHU'S PUZLLE */
                
                //$username_11street = '11Street';
                $username_11street = 'prestomall';
                $password_11street = '';
                
                // GET XML //
                // Added by Maruthu    
                if ( ! file_exists(Config::get('constants.XML_FILE_PATH').'state.xml')) {
                    $statedb = ElevenStreetOrder::getState();
                    $xml = new XMLWriter();
                    $xml->openURI(Config::get('constants.XML_FILE_PATH').'state.xml');
                    $xml->startDocument('1.0');
                    $xml->startElement('statedb');

                    foreach ($statedb as $value) {
                        $xml->startElement('value');
                        $xml->writeElement('id', $value->id);
                        $xml->writeElement('state_code', $value->state_code);
                        $xml->writeElement('state_name', $value->state_name);
                        $xml->endElement();
            } 

                    $xml->endElement();
                    $xml->endDocument();
                    $xml->flush();

                    // Session::flash('success', 'success.');
                }   


                if ( ! file_exists(Config::get('constants.XML_FILE_PATH').'postcode.xml')) {
                    $postcode = ElevenStreetOrder::getPostcode();
                    $xml = new XMLWriter();
                    $xml->openURI(Config::get('constants.XML_FILE_PATH').'postcode.xml');
                    $xml->startDocument('1.0');
                    $xml->startElement('postcode');

                    foreach ($postcode as $value) {
                        $xml->startElement('value');
                        $xml->writeElement('id', $value->id);
                        $xml->writeElement('postcode', $value->postcode);
                        $xml->writeElement('area', $value->area);
                        $xml->writeElement('post_office', $value->post_office);
                        $xml->writeElement('state_code', $value->state_code);
                        $xml->endElement();
                    }

                    $xml->endElement();
                    $xml->endDocument();
                    $xml->flush();

                    // Session::flash('success', 'success.');
                } 
                
                
                $getpostcode = Transaction::getXMLpostcode($delivery_postcode);

                if($getpostcode['status']==1){
                    $st_code = $getpostcode['st_code'];
                    $post_office = $getpostcode['post_office'];
                    $statename   = $getpostcode['state'];        
                    $status      = $getpostcode['status']; 
                }

                $stateinfo = Transaction::getStateID($statename);
                $state_id = $stateinfo->id;

                $city_idcityid="";
                $cityinfo = Delivery::getCityList($state_id);

                $city_info = Transaction::getCityID($post_office,$state_id);
                $city_id   = $city_info->id;
                // GET XML //
       
                $country_id = 458;
                
                $OrderDataDetails = ElevenStreetOrderDetails::getByOrderID($OrderID);
                
                $qrcode = array();
                $priceopt = array();
                $qty = array();

                $zeroDelivery = 0;
                
                 // ADDED BY WIRA ON 15092016 //
                foreach ($OrderDataDetails as $keyDetailsCheck => $valueDetailsCheck) {
                    $APIDataCheck = json_decode($valueDetailsCheck->api_result_return, true);
                    if($APIDataCheck['dlvCst'] == 0){
                        $zeroDelivery = 1;
                    }
                }
              $elevenStreetDeliveryCharges = 0;  

            //if((count($OrderDataDetails) > 1) || ($getpostcode['status']==0) || ($delivery_postcode == "")){   
            if(($getpostcode['status']==0) || ($delivery_postcode == "")){
                    // THROW TO MANUAL PROCESS
                   
                    $manualProcess = true;
                    array_push($manualProcessOrder, array(
                        "order_number"=>$order_number,
                        "buyername"=>$customer_name
                    ));
                    
                }else{
                    $delivery = array();
                    $is_skip = 0;
                    $extra_message = "";
                    
                    foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
                        $APIData = json_decode($valueDetails->api_result_return, true);
                       
                        if(count($APIData['sellerPrdCd']) > 0 ){

                            if(count($APIData['ordDlvReqCont']) > 0 ){
                                $extra_message = $APIData['ordDlvReqCont'];
                            }
                            
                        $ProductInformation = Product::findProductInfoByQRCODE($APIData['sellerPrdCd']);
                        
                            if(count($ProductInformation) >  0){

                        $qrcode[] = $ProductInformation->qrcode;
                                
                                // CHECK DEFINE LABEL PRICE CODE //
                                $optionName = $APIData['slctPrdOptNm'];
                                
                                
                                if(count($optionName) > 0 ){
                                     
                                    // OPEN : OLD CODE : Option id Take from option name  
                                    
                                    // if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                                    //     // TAKE DEFINED LABEL ID //
                                    //     $selectedPriceOptionID = substr($optionName,strpos($optionName,"[") + 1,strpos($optionName,"]") -1);
                                    //     $PriceOption = Price::find($selectedPriceOptionID);

                                    //     if(count($PriceOption)>0){
                                    //         $priceopt[] = $selectedPriceOptionID;       
                                    //     }else{
                                    //         $priceopt[] = $ProductInformation->ProductPriceID;
                                    //     }
                                    // }else{
                                    //     $priceopt[] = $ProductInformation->ProductPriceID;
                                    // }
                                    // CLOSE OLD CODE : Option id Take from option name  
                                    
                                    
                                    if(isset($APIData['partCode']) && $APIData['partCode'] != '' && count($APIData['partCode']) > 0 ){
                                        // TAKE DEFINED LABEL ID //
                                        $selectedPriceOptionID = $APIData['partCode'];
                                        $PriceOption = Price::find($selectedPriceOptionID);
                
                                        if(count($PriceOption)>0){
                                            $priceopt[] = $selectedPriceOptionID;       
                                        }else{
                                            $priceopt[] = $ProductInformation->ProductPriceID;
                                        }
                                        
                                    }else{
                                        
                                        if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                                            
                                            $selectedPriceOptionID = $selectedPriceOptionID = substr($optionName,strpos($optionName,"[") + 1,strpos($optionName,"]") -1);
                                            $PriceOption = Price::find($selectedPriceOptionID);
                                            
                                            if(count($PriceOption)>0){
                                                $priceopt[] = $selectedPriceOptionID;       
                                            }else{
                                                $priceopt[] = $ProductInformation->ProductPriceID;
                                            }
                                        }else{
                                            $priceopt[] = $ProductInformation->ProductPriceID;
                                        } 
                                    }
                                    
                                }else{
                                    $priceopt[] = $ProductInformation->ProductPriceID;
                                }
                                // CHECK DEFINE LABEL PRICE CODE //
                                
                        $qty[] = $APIData['ordQty'];
                        
                            // Exclusive Amount = Inclusive Tax * 100 / 106
                                $elevenStreetDeliveryCharges = $elevenStreetDeliveryCharges + ($APIData['dlvCst'] * (100/(100 + $tax_rate)));
                                $delivery[] = $elevenStreetDeliveryCharges;
                            
                        }else{
                           
                            $is_skip = 1;
                            $manualProcess = true;
                            array_push($manualProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name
                            ));

                    }
                        }else{
                    
                            $is_skip = 1;
                            $manualProcess = true;
                            array_push($manualProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name
                            ));
                       
                    }
                        
                         
                    }
                    if($is_skip == 0){
                        
                    $get = array(
                        'user'                => $username_11street,             // Buyer Username
                        'pass'                => $password_11street,             // Buyer Password
                        'delivery_name'       => $delivery_name,      // delivery name
                        'delivery_contact_no' => $delivery_contact_no, // delivery contact no
                            'special_msg'         => 'Transaction transfer from Prestomall ( Order Number : '.$order_number.' )'. " ".$extra_message,       // special message
                        'delivery_addr_1'     => $delivery_addr_1,
                        'delivery_addr_2'     => $delivery_addr_2,
                        'delivery_postcode'   => $delivery_postcode,
                        'delivery_city'       => $city_id, // City ID 
                        'delivery_state'      => $state_id,                          // State ID
                        'delivery_country'    => $country_id,                 // Country ID
                        'elevenstreetDeliveryCharges'                    => $elevenStreetDeliveryCharges,  
                        'qrcode'              => $qrcode,
                        'price_option'        => $priceopt, // Price Option
                        'qty'                 => $qty,
                        'devicetype'          => 'cms',
                        'uuid'                => NULL, // City ID
                        'lang'                => 'EN',
                        'ip_address'          => Request::getClientIp(),
                        'location'            => '',
                        'transaction_date'    => date("Y-m-d H:i:s"),
                        'charity_id'          => '',
                    );
                    
                
                    // Create Transaction //
                    $data = MCheckout::checkout_transaction($get);

                   

                    if($data['status'] == "success"){

                        $transaction_id = $data["transaction_id"];
                            
                        // DEDUCT STOCK //
                        $details = TDetails::where('transaction_id', '=', $transaction_id)->get();
                        foreach ($details as $detail){
                            $product = Price::find($detail->p_option_id);
                            if (isset($product->id))
                            {
                                $product->qty -= $detail->unit;
                                $product->stock -= $detail->unit;
                                $product->save();
                            }
                            
                        }
                        // DEDUCT STOCK //    
                            
                            // PUSH TO SUCCESS LIST 
                            array_push($transferedProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name,
                                "transactionID"=>$transaction_id
                            )); 

                            
                            
                        // SAVE AS COMPLETED TRANSACTION //
                        $trans = Transaction::find($transaction_id);
                        $trans->status = 'completed';
                        $trans->modify_by = 'API';
                        $trans->modify_date = date("Y-m-d h:i:sa");
                        $trans->save();
                        // SAVE AS COMPLETED TRANSACTION //

                        // CREATE INV
                        //$tempInv = MCheckout::generateInv($transaction_id, true);
                        // CREATE PO
                        //$tempPO = MCheckout::generatePO($transaction_id, true);
                        // CREATE DO
                        //$tempDO = MCheckout::generateDO($transaction_id, true);

                        // Update Status 11Street Order as transfered
                        if($OrderID != 0){
                            $ElevenStreetOrder = ElevenStreetOrder::find($OrderID);
                            $ElevenStreetOrder->status = 2;
                            $ElevenStreetOrder->transaction_id = $transaction_id;
                            $ElevenStreetOrder->save();
                        }

                    }else{
                            $manualProcess = true;
                            array_push($manualProcessOrder, array(
                                "order_number"=>$order_number,
                                "buyername"=>$customer_name
                            ));
                        // THROW ERROR FAILED TO CREATE TRANSACTION
                    }
                        
                }
                
            } 

            } 
            switch ($from_account) {
                case 1:
                    $acc_name = "11Street Acc 1";  
                    break;
                case 2:
                    $acc_name = "11Street Acc 2";  
                    break;
                case 3:
                    $acc_name = "11Street F&N";  
                    break;            
                case 4:
                    $acc_name = "Coca Cola";  
                    break;            
                case 5: // SPRITZER               
                   $acc_name = "Spritzer";  
                    break;            
                case 6: // CACTUS               
                    $acc_name = "Cactus";  
                    break;            
                case 7: // F&N CREAMERIES               
                    $acc_name = "F&N Creamer";  
                    break;            
                case 8: // Starbuck               
                    $acc_name = "Starbuck";  
                    break;  
                case 9: // POKKA               
                    $acc_name = "POKKA";  
                    break;   
                case 10: // Yeos               
                    $acc_name = "Yeos";  
                    break;  
                case 11: // Oriental               
                    $acc_name = "Oriental";  
                    break;    
                case 12: // KawanFood               
                    $acc_name = "KawanFood";  
                    break; 
                case 13: // Nikudo               
                    $acc_name = "Nikudo";  
                    break; 
                case 14: // ETIKA               
                    $acc_name = "Etika";  
                    break; 
                default:
                   $acc_name = "11Street Acc 1";  
                    break;
            }
            // MANUAL PROCESS HANDLING
            //if($manualProcess){
                // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
                $subject = "Migration Notification";
                $recipient = array(
                    "email"=>Config::get('constants.ElevenStreetManagerEmail'),
                    //"name"=> "Wira Izkandar"
                );
                $data = array(
                        'execution_datetime'      => date("Y-m-d H:i:s"),
                        'total_records'  => count($OrderCollection),
                        'manual_process'  => count($manualProcessOrder),
                        'manual_order_list'  => $manualProcessOrder,
                        'transfered_orders'  => $transferedProcessOrder,
                        'acc_name'  => $acc_name,
                );

                Mail::send('emails.elevenmigratereport', $data, function($message) use ($recipient,$subject)
                {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($recipient['email'], $recipient['name'])
                            ->cc(['asif@jocom.my', 'mint@jocom.my', 'september@jocom.my'])
                            ->subject($subject);
                });
                
            
                
                $running_number = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'batch_no')->first();
                
                $batchNo = str_pad($running_number->counter + 1,10,"0",STR_PAD_LEFT);
                $NewRunner = Running::find($running_number->id);
                $NewRunner->counter = $running_number->counter + 1;
                $NewRunner->save();
                
                //self::maruthu($transferedProcessOrder);
                //self::transactionDeliverytime24h($batchNo, $transferedProcessOrder);
                
                self::transactionDeliverytime24h($batchNo, $transferedProcessOrder);
                
            //}
            
            /* AUTOMATION TRANSACTION */
            
        } catch (Exception $ex) {
            $isSaved = false;
            //echo $ex->getTraceAsString();
        }
        
        finally{
            return $delivery;
    }
    
    }
    
    
    public function batchGenerate(){
    
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
        DB::beginTransaction();
        try{
           
            $batch = ElevenStreetOrder::getBatch();
            //return $batch;
            if(count($batch) > 0 ){
                
                 set_time_limit(0);
                foreach ($batch as $key => $value) {
                    
                    $transaction_id = $value->transaction_id;
                    $Transaction = Transaction::find($transaction_id);
                    if($Transaction->status == 'completed'){
                    $order_id = $value->id;
                    $ElevenStreetOrder = ElevenStreetOrder::find($order_id);
                    $TransactionInfo = Transaction::find($transaction_id);
                    
                    if($TransactionInfo->do_no == ""){
                        // CREATE INV
                        $tempInv = MCheckout::generateInv($transaction_id, true);
                        // CREATE PO
                        $tempPO = MCheckout::generatePO($transaction_id, true);
                        // CREATE DO
                        $tempDO = MCheckout::generateDO($transaction_id, true);
                    }
                    
                    $ElevenStreetOrder->status = "2";
                    $ElevenStreetOrder->is_completed = "2";
                    $ElevenStreetOrder->updated_by = "api_system";
                    $ElevenStreetOrder->save();
                        // AUTOMATED LOG TO LOGISTIC APP
                        LogisticTransaction::log_transaction($transaction_id);
                    }
                    
                }
                
                 //echo "END: ".date("Y-m-d H:i:s");
                
            }
            
        } catch (Exception $ex) {
            $isError = 1;
            DB::rollBack();
            $errorMessage = $ex->getMessage();
        }
        finally{
            if($isError == 0){
                DB::commit();
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }
        
    }
    
    
    
    private function ConvertDateTimeFormat($datetime,$format){
    
        switch ($format) {
            case self::DATE_FORMAT_ymdhhmm:
                $old_date_timestamp = strtotime($datetime);
                $new_format_date = date('dmYHi', $old_date_timestamp); 
                break;
        }
        
        
        
        return $new_format_date;
        
    }
    
   
     public static function transactionDeliverytime24h($batchno,$transactioncollections = array()){
    
            $arr = array();
    
            foreach ($transactioncollections as $key => $value) {
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

                          
            // foreach ($returnval as $rvalue) {
            //     $trnsArray  = array();  
            //     $transactionlist = DB::table('jocom_transaction_group')->select('transaction_id')  
            //                             ->where('batch_no',$batchno)
            //                             ->where('sku',$rvalue->sku)
            //                             ->get();
            //                     $str = "";
            //                     foreach ($transactionlist as $key3 => $tranvalue) {
                                            
            //                                 $arr_val = array('transaction_id' => $tranvalue->transaction_id);
            //                                 if($str == ""){
            //                                     $str = $tranvalue->transaction_id;
            //                                 }else{
            //                                     $str = $str.",".$tranvalue->transaction_id;
            //                                 }
                                           
            //                             // array_push($trnsArray, $arr_val);   
            //                           }      
            //                       // $str = implode(",", $arr_val->transaction_id);
            //     $array =  array('batchno' => $batchno,
            //                     'sku' => $rvalue->sku,
            //                     'product_name' => $rvalue->name,
            //                     'price_label' => $rvalue->price_label,
            //                     'quantity' => $rvalue->quantity,
            //                     'transaction_id' => $str,

            //      );
            //     array_push($arr, $array);
                
               
            // }
            
            //self::createCSV($batchno,$arr);
            
            return $arr;

         

    }
    
    public static function createCSV($batch_number,$record){
        
        
        $fileName = 'purchase_product_'.date("Ymd his").'_'.$batch_number.".csv";
        $path = Config::get('constants.CSV_FILE_PATH');
        $file = fopen($path.'/'.$fileName, 'w');

        fputcsv($file, ['Batch Number', 'Product SKU', 'Product Name','Product Label', 'Product Quantity','Transaction ID']);

        foreach ($record as $row)
        {   
//            $transaction_id = $row['transactionID'];
//            $recordItem = DB::table('jocom_transaction_details' )
//                            ->leftJoin('jocom_products','jocom_transaction_details.sku', '=', 'jocom_products.sku')
//                            ->where('jocom_transaction_details.transaction_id',$transaction_id)->get();
//                           
//            foreach ($recordItem as $key1 => $value1) {
                fputcsv($file, [
                    "BP".$row['batchno'],
                    $row['sku'],
                    $row['product_name'],
                    $row['price_label'],
                    $row['quantity'],
                    $row['transaction_id']
                ]);
            //}        
            
        }
        
        fclose($file);
        
        $test = Config::get('constants.ENVIRONMENT');

        if ($test == 'test')
            $mail = ['wira.izkandar@jocom.my'];
        else
            $mail = ['sri@jocom.my', 'johnny.lin@jocom.my','joshua.sew@jocom.my'];
        $subject = "Fresh Purchase : " . $fileName;
        $attach = $path . "/" . $fileName;

        $body = array('title' => 'Fresh Product To Purchase');

        Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
            {
                $message->from('payment@jocom.my', 'JOCOM');
                $message->to($mail, '')->subject($subject);
                $message->attach($attach);
            }
        );
        
        
    }

    public function createFreshreporting(){

        if ((int)date('H') >= 12) {
            $from_date = date("Y-m-d").' 00:00:00';
            $to_date = date("Y-m-d").' 12:00:00';
        }else{
            $from_date = date('Y-m-d',strtotime("-1 days")).' 12:00:01';
            $to_date = date('Y-m-d',strtotime("-1 days")).' 23:59:59';
        }

        $record = DB::table('jocom_transaction_group AS JTG')
                ->leftJoin('jocom_products AS JP','JTG.sku', '=', 'JP.sku')
                ->select('JTG.batch_no', 'JTG.sku', 'JP.name', 'JTG.price_label', 'JTG.unit', 'JTG.transaction_id', 'JTG.created_at')
                ->where('JTG.created_at', ">=",$from_date)
                ->where('JTG.created_at', "<=",$to_date)
                ->orderBy('created_at', Desc)
                ->get();
     
        $fileName = 'fresh_product_'.$from_date.'-'.$to_date.".csv";
        $path = Config::get('constants.CSV_FILE_PATH');
        $file = fopen($path.'/'.$fileName, 'w');

        fputcsv($file, ['Batch Number', 'Product SKU', 'Product Name','Product Label', 'Product Quantity','Transaction ID', 'Created At']);

        foreach ($record as $row)
        {   

                fputcsv($file, [
                    "BP".$row->batch_no,
                    $row->sku,
                    $row->name,
                    $row->price_label,
                    $row->unit,
                    $row->transaction_id,
                    $row->created_at
                ]);
      
            
        }
        
        fclose($file);
        
        $test = Config::get('constants.ENVIRONMENT');

        if ($test == 'test')
            $mail = ['wira.izkandar@jocom.my'];
        else
            $mail = ['sri@jocom.my', 'johnny.lin@jocom.my','joshua.sew@jocom.my'];
            $subject = "Fresh Purchase : " . $fileName;
            $attach = $path . "/" . $fileName;

        $body = array('title' => 'Fresh Product To Purchase');

        Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
            {
                $message->from('payment@jocom.my', 'JOCOM');
                $message->to($mail, '')->subject($subject);
                $message->attach($attach);
            }
        );


    }

    public function migrateSingleOrder() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            DB::beginTransaction();
            
            $accountType = Input::get('account_type');
            // Get API Key
            switch ($accountType) {
            case 1:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_1');  
                $from_account = 1;
                break;
            case 2:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_2');  
                $from_account = 2;

                break;
            case 3:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_FN');  
                $from_account = 3;

                break;
                
            case 4: // COCA COLA
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_COCACOLA');  
                $from_account = 4;

                break;
                
            case 5: // SPRITZER
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_SPRITZER');  
                $from_account = 5;

                break;
            
            case 6: // CACTUS
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_CACTUS');  
                $from_account = 6;

                break;
                
            case 7: // F&N CREAMERIES
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_FNCREAMERIES');  
                $from_account = 7;
                
                break;
                
            case 8: // Starbuck
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_STARBUCK');  
                $from_account = 8;

                break;
                
            case 9: // POKKA
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_POKKA');  
                $from_account = 9;

                break;
                
            case 10: // YEOS
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_YEOS');  
                $from_account = 10;
                break;
                
             case 11: // ORIENTAL
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_ORIENTAL');  
                $from_account = 11;
                break;
                
             case 12: // KAWAN FOOD
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_KAWANFOOD');  
                $from_account = 12;
                break;
            
            case 13: // NIKUDO SEA FOOD
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_NIKUDO');  
                $from_account = 13;
                break; 
            
            case 14: // ETIKA
               
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_ETIKA');  
                $from_account = 14;
                break; 
            
            default:
                $ElevenStreetAPIKEY = Config::get('constants.ElevenStreetAPIKEY_ACCT_1');  
                $from_account = 1;
                break;
            }
            
            $holderSubline = array();
            $orderCollection = [];
            
            $order_number = Input::get('order_number');
            
            
            if($order_number == ''){
                $message = 'No order found';
            }else{
                
            
//          $ElevenStreetOrderData = ElevenStreetOrder::findByOrderNumber($order_number);
            $ElevenStreetOrderData  = DB::table('jocom_elevenstreet_order')
                                ->where('order_number', $order_number)
                                ->where('from_account', $from_account)
                                ->where('activation', 1)
                                ->first();
            
            if(count($ElevenStreetOrderData) > 0){
                $message = "Order number: ".$order_number." already exist in the system";
            }else{
                //$URL = "http://api.11street.my/rest/ordservices/complete/".$order_number;
                
                // Amended dated: 17-06-2019
                $URL = "https://api.prestomall.com/rest/ordservices/complete/".$order_number;
                
                $ch = curl_init();
                curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                    'Contenttype: application/xml',
                    'AcceptCharset: utf8',
                    'openapikey: '.$ElevenStreetAPIKEY
                ));

                curl_setopt($ch,CURLOPT_URL,$URL );
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                $output = curl_exec($ch);
               
                curl_close($ch);

                // Load returned result and get namespaces
                $xml=simplexml_load_string($output);
                $resultResponse = get_object_vars($xml);
                $namespaces = $xml->getNamespaces(true);
                $index = 0;
               
                if($resultResponse['result_code'] == 0){
                    $message = 'No order found';
                }
                 
                foreach($xml->children($namespaces['ns2']) as $child) {
               
                    $SubOrder = get_object_vars($child->children());
                    $orderNum = $SubOrder['ordNo'];
                
                    $index++;
                    if((count($holderSubline) > 0 ) && ($holderSubline['order_number'] !== $orderNum)){
                        array_push($orderCollection, $holderSubline);
                        $holderSubline = array();
                        $orderNum = $SubOrder['ordNo'];
                    }

                    if(!array_search($SubOrder['ordNo'],$holderSubline)){
                        // Check if the order already in database 

                            $holderSubline['order_number'] = $SubOrder['ordNo'];
                            $holderSubline['migrate_from'] = $listType;
                            $holderSubline['customer_name'] = $SubOrder['ordNm'];

                            if(!empty($SubOrder['ordId'])){
                                $holderSubline['customer_email'] = $SubOrder['ordId'];
                            }else{
                                $holderSubline['customer_email'] = "";
                            }
                            $holderSubline['product_info'][] = $SubOrder;


                    }else{
                        if($SubOrder['ordNo'] == $orderNum){
                            $holderSubline['product_info'][] = $SubOrder;
                        }
                    }
                    if($index == count($xml->children($namespaces['ns2']))){
                        array_push($orderCollection, $holderSubline);
                    }
                   
                
                }
                
            }
           
            if(count($orderCollection) > 0 ){
                // Save new records
                $isSaved = $this->saveNewOrder($orderCollection,$accountType);
                $message = "Order migrated";
            }
            
            }
            
        
        } catch (Exception $ex) {
              
            $is_error = true;
            $message = $ex->getMessage();
            
        } finally {
            
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
            
        }

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }

    public function createpdf(){
        
        
        $isError = 0;
        
        try{
            
            
            // Begin Transaction
            DB::beginTransaction();
            set_time_limit(0);
            $scheduleList = DB::table('jocom_generate_invoice_schedule')->where("is_generated",0)->limit(100)->get();
            
            if(count($scheduleList) > 0 ){
                
            
                foreach ($scheduleList as $key => $value) {

                    $trans = Transaction::find($value->transaction_id); 
                    $file_name = $trans->invoice_date."_".$trans->invoice_no.".pdf";
                    $file_path = Config::get('constants.INVOICE_PDF_FILE_PATH_2017');

                    include app_path('library/html2pdf/html2pdf.class.php');

                    $INVView = TransactionController::createINVView($trans);
                    // BIG REMINDER AFTER 1/04/2017 PLEASE FOLLOW NEW INVOICE VIEW
                    $response = View::make('checkout.invoice_view')
                            ->with('display_details', $INVView['general'])
                            ->with('display_trans', $INVView['trans'])
                            ->with('display_issuer',$INVView['issuer'])
                            ->with('display_seller', $INVView['paypal'])
                            ->with('display_product', $INVView['product'])
                            ->with('display_group', $INVView['group'])
                            ->with('display_coupon', $INVView['coupon'])
                            ->with('display_points', $INVView['points'])
                            ->with('display_earns', $INVView['earnedPoints'])
                            ->with('toCustomer', $INVView['toCustomer']);

                    $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');

                    $html2pdf->setDefaultFont('arialunicid0');

                    $html2pdf->WriteHTML($response);

                    // $html2pdf->Output($file_name, $display_details, $headers);

                    $html2pdf->Output($file_path."/".$file_name, 'F');

                    // UPDATE 
                    $InvoiceSchedule = InvoiceSchedule::find($value->id);
                    $InvoiceSchedule->is_generated = 1;
                    $InvoiceSchedule->filename = $file_name;
                    $InvoiceSchedule->generated_on = date("Y-m-d h:i:s");
                    $InvoiceSchedule->save();

                }
            }
        
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally{
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "response" =>  $isError ? 0 : 1,
            );
        }

    }

    public function createpdfeinv(){
        
        
        $isError = 0;
        
        try{
            
            
            // Begin Transaction
            DB::beginTransaction();
         set_time_limit(0);
            $scheduleList = DB::table('jocom_generate_einvoice_schedule')->where("is_generated",0)->limit(30)->get();
        
                foreach ($scheduleList as $key => $value) {

                    $trans = Transaction::find($value->transaction_id); 
//echo "<pre>";
                   //  echo print_r($trans);
//echo "</pre>";
                    $eINV_no =  DB::table('jocom_transaction_parent_invoice')->where("transaction_id",$value->transaction_id)->first();
                    
                    if(count($eINV_no) > 0){

                    $file_name = $trans->invoice_date."_".$eINV_no->parent_inv.".pdf";
                    $file_path = Config::get('constants.EINVOICE_PDF_FILE_PATH_2016');

                    include app_path('library/html2pdf/html2pdf.class.php');

                    $ENVView = TransactionController::createEINVView($trans,$eINV_no->parent_inv);

                    $response = View::make('checkout.invoice_view')
                            ->with('display_details', $ENVView['general'])
                            ->with('display_trans', $ENVView['trans'])
                            ->with('display_issuer',$ENVView['issuer'])
                            ->with('display_seller', $ENVView['paypal'])
                            ->with('display_product', $ENVView['product'])
                            ->with('display_coupon', $INVView['coupon'])
                            ->with('display_group', $ENVView['group'])
                            ->with('display_points', $ENVView['points'])
                            ->with('display_earns', $ENVView['earnedPoints'])
                            ->with('toCustomer', $ENVView['toCustomer']);

                    $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');

                    $html2pdf->setDefaultFont('arialunicid0');

                    $html2pdf->WriteHTML($response);

                    // $html2pdf->Output($file_name, $display_details, $headers);

                    $html2pdf->Output($file_path."/".$file_name, 'F');

                    // UPDATE 
                    $EInvoiceSchedule = EInvoiceSchedule::find($value->id);
                    $EInvoiceSchedule->is_generated = 1;
                    $EInvoiceSchedule->filename = $file_name;
                    $EInvoiceSchedule->generated_on = date("Y-m-d h:i:s");
                    $EInvoiceSchedule->save();

                    }else{
                          // UPDATE 
                        $EInvoiceSchedule = EInvoiceSchedule::find($value->id);
                        $EInvoiceSchedule->is_generated = 1;
                        $EInvoiceSchedule->filename = 'Delivery Service';
                        $EInvoiceSchedule->generated_on = date("Y-m-d h:i:s");
                        $EInvoiceSchedule->save();
                    }


            }
        
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            echo $message;
            $isError = 1;
        }finally{
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "response" =>  $isError ? 0 : 1,
            );
        }

    }
    
    
    public function topWinner() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            // echo date("Y-m-d", strtotime("yesterday"))." 14:00:01";
            // die();
            
            DB::beginTransaction();
            
            // $fromDatetime = "2018-01-05 14:00:01";
            // $toDatetime = "2018-01-07 23:59:59";
            
            // $fromDatetimeName = "2018-01-05 14:00:01";
            // $toDatetimeName = "2018-01-07 23:59:59";
            
            $fromDatetime = date("Y-m-d", strtotime("yesterday"))." 14:00:01";
            $toDatetime = date("Y-m-d")." 23:59:59";
            
            $fromDatetimeName = date("Ymd", strtotime("yesterday"))." 14:00:01";
            $toDatetimeName = date("Ymd")." 23:59:59";
            
            //  echo $fromDatetime;
            //   echo $toDatetime;
            // die();
            $productList =  array(
                'JC10481' , 
                'JC10482',
                'JC10483',
                'JC10486',
                'JC10488',
                'JC10489',
                'JC10490',
                'JC10491',
                'JC8242',
                'JC7301',
                'JC8243',
                'JC4066',
                'JC8244',
                'JC8245',
                'JC4067',
                'JC8246',
                'JC8248',
                'JC8249',
                'JC8263',
                'JC8264',
                'JC8265',
                'JC8266',
                'JC7299',
                'JC8268',
                'JC6568',
                'JC3243',
                'JC3244',
                'JC8269',
                'JC8270',
                'JC8272',
                'JC8273',
                'JC8271',
                'JC10182',
                'JC10496',
                'JC10498',
                'JC10524',
                'JC8744',
                'JC8550',
                'JC8454',
                'JC8302',
                'JC8299',
                'JC8303',
                'JC8301',
                'JC8300',
                'JC8455',
                'JC8106',
                'JC10522',
                'JC10568',
                'JC10569',
                'JC10570',
                'JC10571',
                'JC10572',
                'JC10573',
                'JC10574',
                'JC10575',
                'JC10576',
                'JC10577',
                'JC10592',
                'JC10593',
                'JC10594',
                'JC10597',
                'JC10598',
                'JC10599',
                'JC10600',
                'JC10601',
                'JC10602',
                'JC10579',
                'JC10584',
                'JC10586',
                'JC10588',
                'JC10589',
                'JC10590',
                'JC10591',
                'JC10596',
                'JC10578',
                'JC10580',
                'JC10582',
                'JC10583',
                'JC10585',
                'JC10587',
                'JC10581',
                'JC10594',
                'JC5489',
                'JC10595',
                'JC5488',
                'JC10341',
                'JC10342'
            );
            
            $listSales = DB::table('jocom_transaction_details AS JTD')
                ->leftJoin('jocom_transaction AS JT','JT.id', '=', 'JTD.transaction_id')
                ->leftJoin('jocom_products AS JP','JP.sku', '=', 'JTD.sku')
                ->leftJoin('jocom_elevenstreet_order AS JEO','JEO.transaction_id', '=', 'JT.id')
                ->select('JT.transaction_date', 'JT.id', 'JEO.order_number', 'JEO.customer_name', 'JP.qrcode', 'JP.name', 'JTD.price_label', 'JP.sku', 'JTD.price', 'JTD.unit AS Quantity', DB::raw('JTD.unit * JTD.price  as TotalAmount') )
                ->where('JT.status', "=",'completed')
                ->where('JT.invoice_no', "<>",'')
                ->where('JT.buyer_username', "=",'11Street')
                ->where('JT.transaction_date', ">=",$fromDatetime)
                ->where('JT.transaction_date', "<=",$toDatetime)
                ->whereIn('JP.qrcode', $productList)
                ->get();
            //     echo $listSales;
            // die();
            //print_r($listSales);
            
            
            /** @var LaravelExcelWriter $file */

//        $file = Excel::create("Waiting unvalidated Accounts -".Carbon::now()->getTimestamp(),
//            function (LaravelExcelWriter $excel) use ($summary) {
//                $excel->sheet('bookings', function (LaravelExcelWorksheet $sheet) use ($summary) {
//                    $sheet->fromArray($summary);
//                });
//            });
            
        $finalData[] = array("Transaction Date","id","order_number","customer_name","qrcode","name","price_label","sku","price","unit","total amount");
        
        foreach ($listSales as $key => $value) {
            
         
            $sub_array = [
                     $value->transaction_date,
                     $value->id,
                     $value->order_number,
                     $value->customer_name,
                     $value->qrcode,
                     $value->name,
                     $value->price_label,
                    $value->sku,
                    $value->price,
                    $value->Quantity,
                     $value->TotalAmount
                    ];
            
            $finalData[] = $sub_array;
            
        }    
        
        $Filename = "11STREET_TOP_WINNER_".$fromDatetimeName."_".$toDatetimeName;
        $file =  Excel::create($Filename, function($excel) use($finalData) {
            $excel->sheet('Sheetname', function($sheet) use($finalData) {
                $sheet->fromArray($finalData);
            });
        });
  
        $subject = "11Street TOP SPENDER : ". date("Y-m-d") ;
        $body = array('title' => 'Stock Alert');

        
         Mail::send([], [], function ($message) use ($subject, $mail, $file){
            $message->from('wira.izkandar@jocom.my', 'JOCOM 11Street TOP WINNER');
            $message->to(['asif@jocom.my', 'humairah@jocom.my']);
            $message->cc('wira.izkandar@jocom.my');
            $message->subject($subject);
            $message->setBody('Dear Asif and Ira<br><br>', 'text/html');
            $message->attach($file->store("xls",false,true)['full']);
        });
            
            
        
        } catch (Exception $ex) {
           echo $ex->getMessage();    
            echo $ex->getFile();  
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


//        /* Return Response */
//        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
//        return $response;

    
    }
    
    
     public function updateEmail(){
        
        $listSales = DB::table('jocom_elevenstreet_order_details AS JEOD')
                    ->select(array(
                        'JEO.id','JEO.order_number','JEOD.api_result_return'
                        ))
                    ->leftJoin('jocom_elevenstreet_order AS JEO','JEO.id', '=', 'JEOD.order_id')
                    ->where('JEO.customer_email', "=",'')
                    ->orderBy('id','ASC')
                    ->groupBy('JEO.order_number')
                    ->take(2000)->get();
                    
        // echo "<pre>";
        //     print_r($listSales);
        //     echo "</pre>";
        
        
        foreach ($listSales as $key => $value) {
            
            $id = $value->id;
            $collection = json_decode($value->api_result_return,true);
            
            // echo "<pre>";
            // print_r($id);
            // echo "</pre>";
            
            if( isset($collection['ordEmail']) ){
                echo $id;
                echo $collection['ordEmail']."<br>";
                
                DB::table('jocom_elevenstreet_order')
                    ->where('id', $id)
                    ->update(['customer_email' => $collection['ordEmail']]);
            }
            
        }
          
    }
    
    public function testpartcode(){
        
        $testorder = '201809081622730';
        $OrderID = 70428;
        // $OrderID = 70489;
       
        
        $OrderDataDetails = ElevenStreetOrderDetails::getByOrderID($OrderID);
        
        
        foreach ($OrderDataDetails as $keyDetails => $valueDetails) {
            
            $APIData = json_decode($valueDetails->api_result_return, true);
            $ProductInformation = Product::findProductInfoByQRCODE($APIData['sellerPrdCd']);
            
            
            echo "<pre>";
            print_r($APIData);
            echo "</pre>";
            
            echo "<pre>";
            print_r($ProductInformation);
            echo "</pre>";
            
            
            if(count($ProductInformation) >  0){

                $qrcode[] = $ProductInformation->qrcode;
                
                // CHECK DEFINE LABEL PRICE CODE //
                $optionName = $APIData['slctPrdOptNm'];
                
                
                if(count($optionName) > 0 ){
                    
                    
                    if(isset($APIData['partCode']) && $APIData['partCode'] != '' && count($APIData['partCode']) > 0 ){
                        // TAKE DEFINED LABEL ID //
                        $selectedPriceOptionID = $APIData['partCode'];
                        $PriceOption = Price::find($selectedPriceOptionID);

                        if(count($PriceOption)>0){
                            $priceopt[] = $selectedPriceOptionID;       
                        }else{
                            $priceopt[] = $ProductInformation->ProductPriceID;
                        }
                        
                    }else{
                        
                        if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false)) {
                            
                            $selectedPriceOptionID = $selectedPriceOptionID = substr($optionName,strpos($optionName,"[") + 1,strpos($optionName,"]") -1);
                            $PriceOption = Price::find($selectedPriceOptionID);
                            
                            if(count($PriceOption)>0){
                                $priceopt[] = $selectedPriceOptionID;       
                            }else{
                                $priceopt[] = $ProductInformation->ProductPriceID;
                            }
                        }else{
                            $priceopt[] = $ProductInformation->ProductPriceID;
                        } 
                    }
                    
                }else{
                    $priceopt[] = $ProductInformation->ProductPriceID;
                }
                // CHECK DEFINE LABEL PRICE CODE //
                
                $qty[] = $APIData['ordQty'];
                
                echo "<pre>";
                print_r($priceopt);
                print_r($qty);
                echo "</pre>";
            }
        }
        
        
       
        
        
        
        
    }
  
    
}