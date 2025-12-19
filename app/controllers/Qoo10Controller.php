<?php

class Qoo10Controller extends BaseController
{
    const DATE_FORMAT_ymdhhmm = "1";

    public function anyIndex(){

        return View::make('qoo10.index');
    }

    public function anyOrderslisting() {
        
        // Get Orders
        $orders = DB::table('jocom_qoo10_order')->select(
                    array(
                        'jocom_qoo10_order.id',
                        'jocom_qoo10_order.packNo',
                        // 'jocom_qoo10_order.orderNo',
                        'jocom_qoo10_order.buyer',
                        'jocom_qoo10_order.buyerEmail',
                        'jocom_qoo10_order.transaction_id',
                        'jocom_qoo10_order.status'
                        ))
                    ->leftJoin('jocom_qoo10_order_details', 'jocom_qoo10_order_details.order_id', '=', 'jocom_qoo10_order.id')
                    ->where('jocom_qoo10_order_details.activation',1)
                    ->groupby('packNo');
 
        return Datatables::of($orders)->make(true);
        
    }


    public function anyMigrate(){

        $isError = 0;
        $OrderCollection = array();
        $message = "Success";  

        try{
            
            $listType = Input::get('list_type');
            
            $search_Sdate = date("Ymd", strtotime("-12 week"))."000001";
            $search_Edate = DATE("Ymd")."235959";
            $ShippingStat = 2;
            $key          = self::AuthorizationKey($listType);
       
            $OrderCollection = $this->getOrders($listType,$search_Sdate,$search_Edate,$ShippingStat,$key);
            if(count($OrderCollection) > 0 ){
                // Save new records
                 $isSaved = $this->saveNewOrder($OrderCollection);
            }

        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getMessage();
            

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

    public function getOrders($listType,$search_Sdate,$search_Edate,$ShippingStat,$key){

        $orderCollection = [];

        //$url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.OpenApiService/ShippingBasicService.api/GetShippingInfo?key=".$key."&ShippingStat=".$ShippingStat."&search_Sdate=".$search_Sdate."&search_Edate=".$search_Edate."";
        //$url = Config::get('constants.Qoo10_SINGAPORE_URL')."/GMKT.INC.Front.OpenApiService/ShippingBasicService.api/GetShippingInfo?key=".$key."&ShippingStat=".$ShippingStat."&search_Sdate=".$search_Sdate."&search_Edate=".$search_Edate."";
        
        // 1 - Malaysia Qoo10
        // 2 - Singapore Qoo10
        
        switch ($listType) {
            case 1: // Malaysia Qoo10
              
                $url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.OpenApiService/ShippingBasicService.api/GetShippingInfo?key=".$key."&ShippingStat=".$ShippingStat."&search_Sdate=".$search_Sdate."&search_Edate=".$search_Edate."";
                // $ExchangeRate = ExchangeRate::getExchangeRate('MYR', 'MYR');
                // $ExchangeRateAmount = $ExchangeRate->amount_to;
                break;
            
            case 2: // Singapore Qoo10
    
                $url = Config::get('constants.Qoo10_SINGAPORE_URL')."/GMKT.INC.Front.OpenApiService/ShippingBasicService.api/GetShippingInfo?key=".$key."&ShippingStat=".$ShippingStat."&search_Sdate=".$search_Sdate."&search_Edate=".$search_Edate."";
                // $ExchangeRate = ExchangeRate::getExchangeRate('SGD', 'MYR');
                // $ExchangeRateAmount = $ExchangeRate->amount_to;
                break;
                
            case 3: // Qoo10 F&N Malaysia
                $url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.QAPIService/Giosis.qapi/ShippingBasic.GetShippingInfo?key=".$key."&ShippingStat=".$ShippingStat."&search_Sdate=".$search_Sdate."&search_Edate=".$search_Edate."&returnType=json";
                // $ExchangeRate = ExchangeRate::getExchangeRate('MYR', 'MYR');
                // $ExchangeRateAmount = $ExchangeRate->amount_to;
                break;

            default: // Malaysia Qoo10
                $url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.OpenApiService/ShippingBasicService.api/GetShippingInfo?key=".$key."&ShippingStat=".$ShippingStat."&search_Sdate=".$search_Sdate."&search_Edate=".$search_Edate."";
                // $ExchangeRate = ExchangeRate::getExchangeRate('MYR', 'MYR'); // No Longer needed for now
                // $ExchangeRateAmount = $ExchangeRate->amount_to;
                break;
        }
        
        $ch = curl_init();

        curl_setopt($ch,CURLOPT_HTTPHEADER,array(
            'Contenttype: application/xml',
            'AcceptCharset: utf8',
            'openapikey: '.$key
        ));

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $output = curl_exec($ch);
        
        // echo "<pre>";
        // // echo $url;
        // print_r($output);
        // echo "</pre>";
        // die();
       
  
        curl_close($ch);
        
        switch ($listType) {
            case 1: // Malaysia Qoo10
                $collection = self::qoo10APIv1Parser($output,$listType,$ExchangeRateAmount);
                break;
            
            case 2: // Singapore Qoo10
                $collection = self::qoo10APIv1Parser($output,$listType,$ExchangeRateAmount);
                break;
                
            case 3: // Qoo10 F&N Malaysia
                $collection = self::qoo10APIv2Parser($output,$listType,$ExchangeRateAmount);
                break;

            default: // Malaysia Qoo10
                $collection = self::qoo10APIv1Parser($output,$listType,$ExchangeRateAmount);
                break;
        }
        
        
        
        return $collection;
       

    }
    
    /*
        XML Format
    */
    public static function qoo10APIv1Parser($output,$listType,$ExchangeRateAmount){
        
        $orderCollection = [];
        $xml = simplexml_load_string($output);
        $namespaces = $xml->getNamespaces(true);
        $totalorder = (int)$xml->TotalOrder[0];
       
           
           
        for ($i=1; $i<=$totalorder; $i++) 
        { 

            $order = 'Order'.$i;

            $OrderRecord  = DB::table('jocom_qoo10_order')
                                ->where('packNo', (string)$xml->$order->packNo)
                                ->where('activation', 1)
                                ->where('from_account', $listType)
                                ->get();
       
            if (empty($OrderRecord)) 
            {
                $ExchangeRate = ExchangeRate::getExchangeRate((string)$xml->$order->Currency, 'MYR');
                $ExchangeRateAmount = $ExchangeRate->amount_to;
                
                
                array_push($orderCollection, (object)array(
                            "packNo" => (string)$xml->$order->packNo,
                            "buyer" =>(string)$xml->$order->buyer,
                            "buyerTel" => (string)$xml->$order->buyerTel,
                            "buyerMobile" => (string)$xml->$order->buyerMobile,
                            "buyerEmail" => (string)$xml->$order->buyerEmail,
                            "orderNo" => (string)$xml->$order->orderNo,
                            "sellerItemCode" => (string)$xml->$order->sellerItemCode,
                            "itemTitle" => (string)$xml->$order->itemTitle,
                            "orderPrice" => (string)$xml->$order->orderPrice * $ExchangeRateAmount,
                            "orderQty" => (string)$xml->$order->orderQty,
                            "discount" => (string)$xml->$order->discount,
                            "total" => ((string)$xml->$order->orderPrice * $ExchangeRateAmount ) * (string)$xml->$order->orderQty, //(string)$xml->$order->total ,
                            "receiver" => (string)$xml->$order->receiver,
                            "receiverTel" => (string)$xml->$order->receiverTel,
                            "receiverMobile" => (string)$xml->$order->receiverMobile,
                            "shippingCountry" => (string)$xml->$order->shippingCountry,
                            "zipCode" => (string)$xml->$order->zipCode,
                            "shippingAddr" => (string)$xml->$order->shippingAddr,
                            "Addr1" => (string)$xml->$order->Addr1,
                            "Addr2" => (string)$xml->$order->Addr2,
                            "ShippingRate" => (string)$xml->$order->ShippingRate * $ExchangeRateAmount,
                            "ShippingActualRate" => (string)$xml->$order->ShippingRate ,
                            "shipping" => (string)$xml->$order->ShippingRate,
                            "ShippingMsg" => (string)$xml->$order->ShippingMsg,
                            "option" => (string)$xml->$order->option,
                            "created_by" => Session::get('username'),
                            "created_at" => date('Y-m-d H:i:s'),
                            "from_account" =>$listType,
                            "currency" =>(string)$xml->$order->Currency,
                            "exchange_rate" => $ExchangeRateAmount,
                            ));

            }
            // else{

            //     $orderCollection = [];
            // }
            

        }
          
        $newArray = array();

        foreach ($orderCollection as $v ) 
        {
            if ( !isset($newArray[$v->packNo]) ) {

                $newArray[$v->packNo] = array(
                        'packNo' => $v->packNo, 
                        'orderNo' => $v->orderNo,
                        "buyer" =>$v->buyer,
                        "buyerTel" => $v->buyerTel,
                        "buyerMobile" => $v->buyerMobile,
                        "buyerEmail" => $v->buyerEmail,
                        "receiver" => $v->receiver,
                        "receiverTel" => $v->receiverTel,
                        "receiverMobile" =>$v->receiverMobile,
                        "shippingCountry" => $v->shippingCountry,
                        "zipCode" => $v->zipCode,
                        "shippingAddr" => $v->shippingAddr,
                        "Addr1" => $v->Addr1,
                        "Addr2" => $v->Addr2,
                        "shipping" => $v->ShippingRate,
                        "created_by" => Session::get('username'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "from_account" => $listType,
                        "currency" => $v->currency,
                        "exchange_rate" => $v->exchange_rate,
                        'product' => array()
                    );
            }

            $newArray[$v->packNo]['product'][] = array(
                    'sellerItemCode' => $v->sellerItemCode, 
                    'orderNo' => $v->orderNo,
                    'itemTitle' => $v->itemTitle,
                    "orderPrice" => $v->orderPrice,
                    "orderQty" => $v->orderQty,
                    "discount" => $v->discount,
                    "total" => $v->total,
                    "ShippingRate" => $v->ShippingRate,
                    "ShippingActualRate" => $v->ShippingActualRate,
                    "ShippingMsg" => $v->ShippingMsg, 
                    "shippingCountry" => $v->shippingCountry,
                    "option" => $v->option,
                    "receiver" => $v->receiver,
                    "receiverTel" => $v->receiverTel,
                    "receiverMobile" =>$v->receiverMobile,
                    "buyer" =>$v->buyer,
                    "buyerTel" => $v->buyerTel,
                    "buyerMobile" => $v->buyerMobile,
                    "buyerEmail" => $v->buyerEmail,
                    "Addr1" => $v->Addr1,
                    "Addr2" => $v->Addr2,
                    "zipCode" => $v->zipCode,
                    "currency" => $v->currency,
                    "exchange_rate" => $v->exchange_rate,
                );
        }
        
        

        return $newArray;
        
    }
    
    /*
        JSON Format
    */
    public static function qoo10APIv2Parser($output,$listType,$ExchangeRateAmount){
        
        $orderCollection = [];
        
        $orders = json_decode($output,true);
    
           
        foreach ($orders['ResultObject'] as $key => $order){
        { 

          
            $order = (object)$order;

            $OrderRecord  = DB::table('jocom_qoo10_order')
                                ->where('packNo', $order->packNo)
                                ->where('activation', 1)
                                ->where('from_account', $listType)
                                ->get();
        
           
            if (empty($OrderRecord)) 
            {
                $ExchangeRate = ExchangeRate::getExchangeRate($order->Currency, 'MYR');
                $ExchangeRateAmount = $ExchangeRate->amount_to;
                
                array_push($orderCollection, (object)array(
                    "packNo" => $order->packNo,
                    "buyer" =>$order->buyer,
                    "buyerTel" => $order->buyerTel,
                    "buyerMobile" => $order->buyerMobile,
                    "buyerEmail" => $order->buyerEmail,
                    "orderNo" => $order->orderNo,
                    "sellerItemCode" => $order->sellerItemCode,
                    "itemTitle" => $order->itemTitle,
                    "orderPrice" => $order->orderPrice * $ExchangeRateAmount,
                    "orderQty" => $order->orderQty,
                    "discount" => $order->discount,
                    "total" => ($order->orderPrice * $ExchangeRateAmount ) * $order->orderQty, //(string)$xml->$order->total ,
                    "receiver" => $order->receiver,
                    "receiverTel" => $order->receiverTel,
                    "receiverMobile" => $order->receiverMobile,
                    "shippingCountry" => $order->shippingCountry,
                    "zipCode" => $order->zipCode,
                    "shippingAddr" => $order->shippingAddr,
                    "Addr1" => $order->Addr1,
                    "Addr2" => $order->Addr2,
                    "ShippingRate" => $order->ShippingRate * $ExchangeRateAmount,
                    "ShippingActualRate" => $order->ShippingRate,
                    "shipping" => $order->ShippingRate,
                    "ShippingMsg" => $order->ShippingMsg,
                    "option" => $order->option,
                    "created_by" => Session::get('username'),
                    "created_at" => date('Y-m-d H:i:s'),
                    "from_account" =>$listType,
                    "currency" =>$order->Currency,
                    "exchange_rate" => $ExchangeRateAmount,
                    ));

            }
            // else{

            //     $orderCollection = [];
            // }
            

        }
        
        // print_r($orderCollection);
          
        $newArray = array();

        foreach ($orderCollection as $v ) 
        {
            if ( !isset($newArray[$v->packNo]) ) {

                $newArray[$v->packNo] = array(
                        'packNo' => $v->packNo, 
                        'orderNo' => $v->orderNo,
                        "buyer" =>$v->buyer,
                        "buyerTel" => $v->buyerTel,
                        "buyerMobile" => $v->buyerMobile,
                        "buyerEmail" => $v->buyerEmail,
                        "receiver" => $v->receiver,
                        "receiverTel" => $v->receiverTel,
                        "receiverMobile" =>$v->receiverMobile,
                        "shippingCountry" => $v->shippingCountry,
                        "zipCode" => $v->zipCode,
                        "shippingAddr" => $v->shippingAddr,
                        "Addr1" => $v->Addr1,
                        "Addr2" => $v->Addr2,
                        "shipping" => $v->ShippingRate,
                        "created_by" => Session::get('username'),
                        "created_at" => date('Y-m-d H:i:s'),
                        "from_account" => $listType,
                        "currency" => $v->currency,
                        "exchange_rate" => $v->exchange_rate,
                        'product' => array()
                    );
            }

            $newArray[$v->packNo]['product'][] = array(
                    'sellerItemCode' => $v->sellerItemCode, 
                    'orderNo' => $v->orderNo,
                    'itemTitle' => $v->itemTitle,
                    "orderPrice" => $v->orderPrice,
                    "orderQty" => $v->orderQty,
                    "discount" => $v->discount,
                    "total" => $v->total,
                    "ShippingRate" => $v->ShippingRate,
                    "ShippingActualRate" => $v->ShippingActualRate,
                    "ShippingMsg" => $v->ShippingMsg,
                    "shippingCountry" => $v->shippingCountry,
                    "option" => $v->option,
                    "receiver" => $v->receiver,
                    "receiverTel" => $v->receiverTel,
                    "receiverMobile" =>$v->receiverMobile,
                    "buyer" =>$v->buyer,
                    "buyerTel" => $v->buyerTel,
                    "buyerMobile" => $v->buyerMobile,
                    "buyerEmail" => $v->buyerEmail,
                    "Addr1" => $v->Addr1,
                    "Addr2" => $v->Addr2,
                    "zipCode" => $v->zipCode,
                    "currency" => $v->currency,
                    "exchange_rate" => $v->exchange_rate,
                );
        }

       
        
    }
    
     return $newArray;
        
    }

    public function anyRevert(){
        
        $isError = 0;
        
        try{
            $order_id = Input::get('order_id');
            $Qoo10Order = QootenOrder::find($order_id);
            $Qoo10Order->status =  1;
            $Qoo10Order->is_completed =  1;
            $Qoo10Order->save();
            
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

    private function saveNewOrder($OrderCollection){

        $counter = 0;
        $isSaved = true;
        $manualProcess = false;
        $manualProcessOrder = array();
        $transferedProcessOrder = array();
        $qoo10DeliveryCharges = 0;
        set_time_limit(0);

        try{
            
            $tax_rate = Fees::get_tax_percent();
     
            foreach ($OrderCollection as $key => $value) 
            {
                $packNo         = $value['packNo']; 
                $orderNo        = $value['orderNo'];
                $buyer          = $value['buyer'];
                $buyerEmail     = $value['buyerEmail'];
                $buyerTel       = $value['buyerTel'];
                $buyerMobile    = $value['buyerMobile'];
                $receiver       = $value['receiver'];
                $receiverTel    = $value['receiverTel'];
                $receiverMobile = strlen((string)$value['receiverMobile']) > 8 ? $value['receiverMobile'] : $receiverTel;
                $Addr1          = $value['Addr1'];
                $Addr2          = $value['Addr2'];
                $zipCode        = $value['zipCode'];
                $shipping       = $value['shipping'];
                $from_account   = $value['from_account'];
                // Check if the order already in database 
                $orders = QootenOrder::where('packNo','=', $packNo)->where('from_account','=', $from_account)->first();
   
                if (empty($orders)) 
                {
                    $Qoo10 = new QootenOrder;
                    $Qoo10->packNo = $packNo;
                    $Qoo10->orderNo = $orderNo;
                    $Qoo10->buyer = $buyer;
                    $Qoo10->buyerEmail = $buyerEmail;
                    $Qoo10->buyerTel = $buyerTel;
                    $Qoo10->buyerMobile = $buyerMobile;
                    $Qoo10->transaction_id = 0;
                    $Qoo10->migrate_from = "Qoo10";
                    $Qoo10->is_completed = 1;
                    $Qoo10->from_account = $from_account;
                    $Qoo10->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                    $Qoo10->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";

                    $Qoo10->save();

                    $OrderID = $Qoo10->id;

                     // Save Product Details
                    foreach ($value['product'] as $key => $val) {
                    
                        $QootenOrderDetails = new QootenOrderDetails;
                        $QootenOrderDetails->order_id = $OrderID;
                        $QootenOrderDetails->orderNo = $val['orderNo'];
                        $QootenOrderDetails->itemTitle = $val['itemTitle'];
                        $QootenOrderDetails->sellerItemCode = $val['sellerItemCode'];
                        $QootenOrderDetails->api_result_return = json_encode($val);
                        $QootenOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $QootenOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $QootenOrderDetails->save();

                    }
                }

                $username_Qoo10 = 'Qoo10';
                $password_Qoo10 = '';
                // $city = DB::table('postcode')->where('postcode', $zipCode)->first();
                // $city_id = DB::table('jocom_cities')->where('name', $city->post_office)->first();
                //  print_r($value);
                $zipcode = $value['zipCode'];
                 
                switch ($value['from_account']) {
                    case 1:
                        
                        if($value['shippingCountry'] == 'MY'){
                            $country_id = 458; // Malaysia
                            $city = DB::table('postcode')->where('postcode', $zipCode)->first();
                            $city_id = DB::table('jocom_cities')->where('name', $city->post_office)->first();
                            $state_id = $city_id->state_id;
                        }else{
                            $country_id = 702; // Singapore
                            $postal_code = substr($zipcode,0,2);
                            $city = DB::table('jocom_singapore_district_code')->where('districts_code', $postal_code)->first();
                            print_r($city);
                            $city_id = $city->city_id;
                            
                            $state = DB::table('jocom_cities')->where('id', $city_id)->first();
                            $state_id = $state->state_id;
                        }
                        
                        break;
                    case 2:
                        if($value['shippingCountry'] == 'MY'){
                            $country_id = 458; // Malaysia
                            $city = DB::table('postcode')->where('postcode', $zipCode)->first();
                            $city_id = DB::table('jocom_cities')->where('name', $city->post_office)->first();
                            $state_id = $city_id->state_id;
                        }else{
                            $country_id = 702; // Singapore
                            $postal_code = substr($zipcode,0,2);
                            $city = DB::table('jocom_singapore_district_code')->where('districts_code', $postal_code)->first();
                            print_r($city);
                            $city_id = $city->city_id;
                            
                            $state = DB::table('jocom_cities')->where('id', $city_id)->first();
                            $state_id = $state->state_id;
                        }
                        
                        break;

                    default:
                        $country_id = 458; // Malaysia
                        $city = DB::table('postcode')->where('postcode', $zipCode)->first();
                        $city_id = DB::table('jocom_cities')->where('name', $city->post_office)->first();
                        $state_id = $city_id->state_id;
                        break;
                }
                
        //                 echo "<pre>";
        // print_r($city_id);
        // echo "</pre>";
        // die();

                $OrderDataDetails = QootenOrderDetails::getByOrderID($OrderID);

                $qrcode = array();
                $priceopt = array();
                $qty = array();

                $zeroDelivery = 0;
            
                foreach ($OrderDataDetails as $keyDetailsCheck => $valueDetailsCheck) {
                    $APIDataCheck = json_decode($valueDetailsCheck->api_result_return, true);
                    if($APIDataCheck['ShippingRate'] == 0){
                        $zeroDelivery = 1;
                    }
                }

                $qoo10DeliveryCharges = 0;  
                    
                $delivery = array();
                $is_skip = 0;
                $extra_message = "";
                $testarr = array();   
                foreach ($OrderDataDetails as $keyDetails => $valueDetails) 
                {
                    $APIData = json_decode($valueDetails->api_result_return, true);
                        
                    if(count($APIData['sellerItemCode']) > 0 )
                    { 
                        if(count($APIData['ShippingMsg']) > 0 ){
                            $extra_message = $APIData['ShippingMsg'];
                        }

                        //find by Qrcode
                        $ProductInformation = Product::findProductInfoByQRCODE($APIData['sellerItemCode']);

                            if(count($ProductInformation) >  0)
                            {
                                $qrcode[] = $ProductInformation->qrcode;
                                
                                //CHECK DEFINE LABEL PRICE CODE //
                                // $optionName = $APIData['option'];
                                $optionnew = $APIData['option'];
                                $optionName = substr($optionnew, ($pos = strpos($optionnew, ':')) !== false ? $pos + 1 : 0);
                                                                
                                if(count($optionName) > 0 )
                                {                                  
                                    
                                    if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false))
                                    {
                                        // TAKE DEFINED LABEL ID //
                                        $selectedPriceOptionID = substr($optionName,strpos($optionName,"[") + 1,strpos($optionName,"]") -1);
                                        $PriceOption = Price::find($selectedPriceOptionID);

                                        if(count($PriceOption)>0)
                                        {
                                            $priceopt[] = $selectedPriceOptionID;    
                                            array_push($testarr, array(
                                                "price_option"=>$selectedPriceOptionID,
                                                "qty"=>$APIData['orderQty'],
                                                "qprice"=>$APIData['total'],
                                                "qrcode"=>$ProductInformation->qrcode
                                            ));  
                                        }else{
                                            $priceopt[] = $ProductInformation->ProductPriceID;
                                            array_push($testarr, array(
                                                "price_option"=>$ProductInformation->ProductPriceID,
                                                "qty"=>$APIData['orderQty'],
                                                "qprice"=>$APIData['total'],
                                                "qrcode"=>$ProductInformation->qrcode
                                            )); 
                                        }

                                    }else{
                                        $priceopt[] = $ProductInformation->ProductPriceID;
                                        array_push($testarr, array(
                                                "price_option"=>$ProductInformation->ProductPriceID,
                                                "qty"=>$APIData['orderQty'],
                                                "qprice"=>$APIData['total'],
                                                "qrcode"=>$ProductInformation->qrcode
                                            )); 
                                    }
                                    
                                }else{
                                    $priceopt[] = $ProductInformation->ProductPriceID;
                                    array_push($testarr, array(
                                                "price_option"=>$ProductInformation->ProductPriceID,
                                                "qty"=>$APIData['orderQty'],
                                                "qprice"=>$APIData['total'],
                                                "qrcode"=>$ProductInformation->qrcode
                                            )); 
                                     // $priceopt[] = $APIData['total'];
                                }
                                // CHECK DEFINE LABEL PRICE CODE //
                                
                                $qty[] = $APIData['orderQty'];        
                                
                                // Exclusive Amount = Inclusive Tax * 100 / 106
                                // $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($shipping * (100/106));
                                // $delivery[] = $qoo10DeliveryCharges;

                            }else{

                                $is_skip = 1;
                                $manualProcess = true;
                                array_push($manualProcessOrder, array(
                                    "order_number"=>$orderNo,
                                    "buyername"=>$buyer 
                                ));

                            }
                    }else{
                            
                        $is_skip = 1;
                        $manualProcess = true;
                        array_push($manualProcessOrder, array(
                            "order_number"=>$orderNo,
                            "buyername"=>$buyer 
                        ));
                    }                                          
                }
                
                $result = array(); 
                    //Your minimized array
                foreach($testarr as $value){
                    $userid = $value['price_option'];
                    if(isset($result[$userid]))
                        $index = ((count($result[$userid]) - 1) / 2) + 1;
                    else
                        $index = 1;

                    $result[$userid]['price_option'] = $userid;
                    $result[$userid]['qty'] += $value['qty'];
                    $result[$userid]['qrcode'] = $value['qrcode'];
                    $result[$userid]['qprice'] = $value['qprice'];
                                             
                }   
                $result = array_values($result); 

                $priceoption = array();
                $quantity = array();
                $qrcode1 = array();
                $qprice = array();

                foreach ($result as $key => $value) {
                    array_push($priceoption, $value['price_option']);
                    array_push($quantity, $value['qty']);
                    array_push($qrcode1, $value['qrcode']);
                    array_push($qprice, $value['qprice']);
                }   

                $qoo10DeliveryCharges = $qoo10DeliveryCharges + ($shipping * (100/(100 + $tax_rate)));
                $delivery[] = $qoo10DeliveryCharges;

                if($is_skip == 0)
                {
                    $get = array(
                        'user'                => $username_Qoo10,             // Buyer Username
                        'pass'                => $password_Qoo10,             // Buyer Password
                        'delivery_name'       => $receiver,      // delivery name
                        'delivery_contact_no' => $receiverMobile, // delivery contact no
                        'special_msg'         => 'Transaction transfer from Qoo10 ( Pack Number : '.$packNo.' )'. " ".$extra_message,       // special message
                        'delivery_addr_1'     => $Addr2,
                        'delivery_addr_2'     => $Addr1,
                        'delivery_postcode'   => $zipCode,
                        'delivery_city'       => $city_id->id, // City ID 
                        'delivery_state'      => $state_id,                          // State ID
                        'delivery_country'    => $country_id,                 // Country ID
                        'qoo10DeliveryCharges'=> $qoo10DeliveryCharges ,
                        'qrcode'              => $qrcode1,
                        'price_option'        => $priceoption, // Price Option
                        'qty'                 => $quantity, 
                        'devicetype'          => 'cms',
                        'uuid'                => NULL, // City ID
                        'lang'                => 'EN',
                        'ip_address'          => Request::getClientIp(),
                        'location'            => '',
                        'transaction_date'    => date("Y-m-d H:i:s"),
                        'charity_id'          => '',
                    );
                    
                    $ApiLog = new ApiLog ;
                    $ApiLog->api = 'QOO10_MIGRATION_DATA';
                    $ApiLog->data = json_encode($get);
                    $ApiLog->save();
                    
                    $data = MCheckout::checkout_transaction($get);
                    
                    $ApiLog = new ApiLog ;
                    $ApiLog->api = 'QOO10_MIGRATION_DATA_CHECKOUT';
                    $ApiLog->data = json_encode($data);
                    $ApiLog->save();
                    
                    // echo "<pre>";
                    // print_r($get);
                    // print_r($data);
                    // echo "<pre>";
                    // die();
                   
                    $get_qoo10 = array(
                        'user'                => $username_Qoo10,             // Buyer Username
                        'pass'                => $password_Qoo10,             // Buyer Password
                        'delivery_name'       => $receiver,      // delivery name
                        'delivery_contact_no' => $receiverMobile, // delivery contact no
                        'special_msg'         => 'Transaction transfer from Qoo10 ( Pack Number : '.$packNo.' )'. " ".$extra_message,       // special message
                        'delivery_addr_1'     => $Addr2,
                        'delivery_addr_2'     => $Addr1,
                        'delivery_postcode'   => $zipCode,
                        'delivery_city'       => $city_id->id, // City ID 
                        'delivery_state'      => $state_id,                          // State ID
                        'delivery_country'    => $country_id,                 // Country ID
                        'qoo10DeliveryCharges'=> $qoo10DeliveryCharges,  
                        'qrcode'              => $qrcode1,
                        'price_option'        => $priceoption, // Price Option
                        'qty'                 => $quantity, 
                        'qprice'              => $qprice,  
                        'devicetype'          => 'cms',
                        'uuid'                => NULL, // City ID
                        'lang'                => 'EN',
                        'ip_address'          => Request::getClientIp(),
                        'location'            => '',
                        'transaction_date'    => date("Y-m-d H:i:s"),
                        'charity_id'          => '',
                        'transaction_id'      => $data["transaction_id"],
                    );

                    $data_qoo10 = MCheckout::checkout_transaction_qoo10($get_qoo10);
                    
                    // echo "<pre>";
                    // print_r($data);
                    // echo "</pre>";
                   
                    if($data['status'] == "success")
                    {
                        $transaction_id = $data["transaction_id"];
                            
                            // PUSH TO SUCCESS LIST 
                        array_push($transferedProcessOrder, array(
                             "order_number"=>$orderNo,
                            "buyername"=>$buyer,
                            "transactionID"=>$transaction_id
                        ));                            
                            
                        // SAVE AS COMPLETED TRANSACTION //
                        $trans = Transaction::find($transaction_id);
                        $trans->status = 'completed';
                        $trans->modify_by = 'API';
                        $trans->modify_date = date("Y-m-d h:i:sa");
                        $trans->save();
                        // SAVE AS COMPLETED TRANSACTION //
                        DB::table('jocom_transaction_qoo10')
                            ->where('id','=',$transaction_id)
                            ->update(array('status'=>'completed', 'modify_by'=>'API', 'modify_date'=>date("Y-m-d h:i:sa")));
                        // Update Status Qoo10 Order as transfered
                        if($OrderID != 0)
                        {
                            $Qoo10Order = QootenOrder::find($OrderID);
                            $Qoo10Order->status = 2;
                            $Qoo10Order->transaction_id = $transaction_id;
                            $Qoo10Order->save();
                        }

                    }else{

                        $manualProcess = true;
                        array_push($manualProcessOrder, array(
                            "order_number"=>$orderNo,
                            "buyername"=>$buyer
                        ));
                            // THROW ERROR FAILED TO CREATE TRANSACTION
                    }
                }
            }
            switch ($from_account) {
                case 1:
                    $acc_name = "Jocom";  
                    break;
                default:
                   $acc_name = "Jocom";  
                    break;
            }
            // MANUAL PROCESS HANDLING
            //if($manualProcess){
                // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
                $subject = "Migration Notification";
                $recipient = array(
                    "email"=>Config::get('constants.Qoo10ManagerEmail'),
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

                Mail::send('emails.qoo10migratereport', $data, function($message) use ($recipient,$subject)
                {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($recipient['email'], $recipient['name'])
                            ->cc(Config::get('constants.Qoo10ManagerEmailCC'))
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
                
                self::transactionDeliverytime24h($batchNo, $transferedProcessOrder);
                
            //}
            
            /* AUTOMATION TRANSACTION */

        }catch (Exception $ex) {
            $isSaved = false;
            // echo $ex;
        }
        
        finally{
            return $delivery;
        }
        
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

    public function anyBatchgenerate(){
    
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
         DB::beginTransaction();
        try{
           
            $batch = QootenOrder::getBatch();

            //return $batch;
            if(count($batch) > 0 ){
                
                set_time_limit(0);
                foreach ($batch as $key => $value) {

                    $transaction_id = $value->transaction_id;
                    $Transaction = Transaction::find($transaction_id);
                    if($Transaction->status == 'completed'){
                        $order_id = $value->id;
                        $Qoo10Order = QootenOrder::find($order_id);
                        $TransactionInfo = Transaction::find($transaction_id);

                        if($TransactionInfo->do_no == ""){
                            // CREATE INV
                            $tempInv = MCheckout::generateInv($transaction_id, true);
                            $tempQ0010Inv = MCheckout::generateQoo10Inv($transaction_id, true);
                            // CREATE PO
                            $tempPO = MCheckout::generatePO($transaction_id, true);
                            // CREATE DO
                            $tempDO = MCheckout::generateDO($transaction_id, true);
                        }

                        $Qoo10Order->status = "2";
                        $Qoo10Order->is_completed = "2";
                        $Qoo10Order->updated_by = "api_system";
                        $Qoo10Order->save();
                        // AUTOMATED LOG TO LOGISTIC APP
                        LogisticTransaction::log_transaction($transaction_id);
                    }
                     
                }                
                
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
    
    
    public function anyBatchgenerate2(){
    
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
         DB::beginTransaction();
        try{
            $transaction_id = 53564;
            $tempQ0010Inv = MCheckout::generateQoo10Inv($transaction_id, true);
           
            
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

    public static function AuthorizationKey($type = ''){


// $url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.OpenApiService/Certification.api/CreateCertificationKey?user_id=".$Qoo10_user_id."&pwd=".$Qoo10_pwd."&key=".$Qoo10_apiKey;

        switch ($type) {
            case 1:
            case 2:
                $Qoo10_apiKey = Config::get('constants.Qoo10_apiKey');  
                $Qoo10_user_id = Config::get('constants.Qoo10_user_id');  
                $Qoo10_pwd = Config::get('constants.Qoo10_pwd');  
                $url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.QAPIService/Giosis.qapi/CertificationAPI.CreateCertificationKey?user_id=".$Qoo10_user_id."&pwd=".$Qoo10_pwd."&key=".$Qoo10_apiKey."&returnType=xml";
                //$url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.OpenApiService/Certification.api/CreateCertificationKey?user_id=".$Qoo10_user_id."&pwd=".$Qoo10_pwd."&key=".$Qoo10_apiKey;
                break;
                
            case 3:
                $Qoo10_apiKey = Config::get('constants.Qoo10_fn_apiKey');  
                $Qoo10_user_id = Config::get('constants.Qoo10_fn_user_id');  
                $Qoo10_pwd = Config::get('constants.Qoo10_fn_pwd');  
                $url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.QAPIService/Giosis.qapi/CertificationAPI.CreateCertificationKey?user_id=".$Qoo10_user_id."&pwd=".$Qoo10_pwd."&key=".$Qoo10_apiKey."&returnType=xml";
                break;
       
            default:
                $Qoo10_apiKey = Config::get('constants.Qoo10_apiKey');  
                $Qoo10_user_id = Config::get('constants.Qoo10_user_id');  
                $Qoo10_pwd = Config::get('constants.Qoo10_pwd');  
                $url = Config::get('constants.Qoo10_URL')."/GMKT.INC.Front.OpenApiService/Certification.api/CreateCertificationKey?user_id=".$Qoo10_user_id."&pwd=".$Qoo10_pwd."&key=".$Qoo10_apiKey;
        }

        $ch = curl_init();
            curl_setopt($ch,CURLOPT_HTTPHEADER,array(
                'Contenttype: application/xml',
                'AcceptCharset: utf8',
                'openapikey: '.$Qoo10_apiKey
            ));
            
          

        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $output = curl_exec($ch);
        
        //   echo "<pre>";
        // print_r($url);
        // echo "</pre>";
        //      echo "<pre>";
        // print_r($output);
        // echo "</pre>";
        //     echo "<pre>";
        // print_r($httpcode);
        // echo "</pre>";
        // die();
        
        
       
        curl_close($ch);
        
        

        $xml=simplexml_load_string($output);
        
    
        
        $authorization_key = $xml->ResultObject;

        return $authorization_key;
    }
  
    
}
