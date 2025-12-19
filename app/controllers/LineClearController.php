<?php

class LineClearController extends BaseController
{
    
    public function createOrder() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
        
            
            $xl= "";
           // self::ApiCaller($xl);
            $response = self::ApiCallerTracking('060306200310529');
            print_r($response);
            
        
        } catch (Exception $ex) {
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
    
    public function getDeliveryStatus(){
        
        
        
        
    }
    
    public function trackOrder() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
     
        $courier_id = 2; // LINE CLEAR ID
        
        try {
            
            DB::beginTransaction();
            
            $dataResponse = array();
            
            $CourierOrders = CourierOrder::getCourierOrders($courier_id);
          
            foreach ($CourierOrders as $key => $value) {
                
                $tracking_no = $value->tracking_no;
                
                if($tracking_no != ""){
                    
                    $output = self::ApiCallerTracking($tracking_no);
                
                    $courierStatusCode = '';
                    $courierStatus = $output;
                    array_push($dataResponse, array(
                        "trackingNumber" => $tracking_no,
                        "courierStatusCode" => '',
                        "courierStatus" => $output
                    ));
                    
                    $CourierOrderInfo = CourierOrder::findByTrackingNo($tracking_no);
                    
                    $CourierOrder = CourierOrder::find($CourierOrderInfo->id);
                    $CourierOrder->courier_status_code = $courierStatusCode;
                    $CourierOrder->courier_status = $courierStatus;
                    $CourierOrder->save();
                    
                    if($output == 'RTO Delivered'){
                        LogisticBatch::UpdateBatch($CourierOrder->batch_id, array("status"=>4));
                    }
                    
                }
                
                
            }
            
            $data['trackingResponse'] = $dataResponse;
            
        
        } catch (Exception $ex) {
                    
            $is_error = true;
            
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
    
    
    // 
    
    
    
    
    public static function generateWaybillOrder($orderno, $logistic_transaction_item_id,$awb_no,$quantity){
        
        
       
        $weight = 0;
        $xmlData = "";
    //   echo $quantity;
        $LogisticTItem = LogisticTItem::find($logistic_transaction_item_id);
        $LogisticTransaction = LogisticTransaction::find($LogisticTItem->logistic_id);
        $Price = Price::find($LogisticTItem->product_price_id);
        $quantity = $quantity; //$LogisticTItem->qty_order;
        $weight = ($Price->p_weight) / 1000 * ($quantity);
        
        if(Config::get('constants.ENVIRONMENT') == 'live'){
            $AGENT_ID = Config::get('constants.LINE_CLEAR_AGENTID_PRO'); 
        }else{
            $AGENT_ID = Config::get('constants.LINE_CLEAR_AGENTID_DEV'); 
        }
        
        if($weight > 0){
            
            // VERY IMPORTANT !! : BECAUSE OF THE MODE IS PREPAID THE TOTAL AMOUNT AND COLLECTIABLE AMOUNT SHOUL BE 0 //
            
            $Order_No = $orderno;
            $Product_Code = $LogisticTItem->sku;
            $Item_Name = $LogisticTItem->name."(".$LogisticTItem->label.")";
            //$AWB_No = "1".str_pad(str_replace('DO-','',$LogisticTItem->id),8,"0",STR_PAD_LEFT);  // OLD STRUCTURE
            $AWB_No = $awb_no; // will be DO Number without '-'
            $N0_of_Pieces = $quantity; //$LogisticTItem->qty_order;
            $Customer_Name = $LogisticTransaction->delivery_name;
            $Shipping_Add1 = $LogisticTransaction->delivery_addr_1;
            $Shipping_Add2 = $LogisticTransaction->delivery_addr_2;
            $Shipping_City = $LogisticTransaction->delivery_city;
            $Shipping_State = $LogisticTransaction->delivery_state;
            $Shipping_Zip = $LogisticTransaction->delivery_postcode;
            $Shipping_TeleNo = '';
            $Shipping_MobileNo = str_replace(['-', '\\', '/', '+'],"",$LogisticTransaction->delivery_contact_no);
            $Shipping_TeleNo2 = '';
            $Total_Amt = 1;
            $Mode = 'Prepaid'; // Prepaid
            $Collectable_amount = 0;
            $Weight = $weight;
            $UOM = 'Per KG';
            $Type_of_Service = 'Express';
            $N0_of_Pieces = 1; 
            // <N0_of_Pieces>'.$N0_of_Pieces.'</N0_of_Pieces>
            $xmlData = '<NewDataSet>
            <Docket>
                    <Order_No>'.$Order_No.'</Order_No>
                    <AGENT_ID>'.$AGENT_ID.'</AGENT_ID>
                    <Product_Code>'.$Product_Code.'</Product_Code>
                    <Item_Name>'.$Item_Name.'</Item_Name>
                    <AWB_No>'.$AWB_No.'</AWB_No> 
                    <Num_of_pieces>'.$N0_of_Pieces.'</Num_of_pieces>
                    <Customer_Name>'.$Customer_Name.'</Customer_Name>
                    <Shipping_Add1>'.$Shipping_Add1.'</Shipping_Add1>
                    <Shipping_Add2>'.$Shipping_Add2.'</Shipping_Add2>
                    <Shipping_City>'.$Shipping_City.'</Shipping_City>
                    <Shipping_State>'.$Shipping_State.'</Shipping_State>
                    <Shipping_Zip>'.$Shipping_Zip.'</Shipping_Zip>
                    <Shipping_TeleNo>'.$Shipping_TeleNo.'</Shipping_TeleNo>
                    <Shipping_MobileNo>'.$Shipping_MobileNo.'</Shipping_MobileNo>
                    <Shipping_TeleNo2>'.$Shipping_TeleNo2.'</Shipping_TeleNo2>
                    <Total_Amt>'.$Total_Amt.'</Total_Amt>
                    <Mode>P</Mode>
                    <Collectable_amount>0</Collectable_amount>
                    <Weight>'.$Weight.'</Weight>
                    <UOM>'.$UOM.'</UOM>
                    <Type_of_Service>'.$Type_of_Service.'</Type_of_Service>
            </Docket>
    </NewDataSet>';
    
     // XPEN FOR PENANG // ZSHM FOR SHAH ALAM

    if($Shipping_State == 'Pulau Pinang' || $Shipping_State == 'Perak' || $Shipping_State == 'Kedah' ){
        $locationCode = 'XBTW';
        $ShipperPostcode = '14100';
        $ShipperAddress = 'Bukit Mertajam';
        
    }elseif($Shipping_State == 'Melaka' ){
        $locationCode = 'XMKZ';
        $ShipperPostcode = '75350';
        $ShipperAddress = 'Batu Berendam';
        
    }elseif($Shipping_State == 'Johor' ){
        $locationCode = 'XJHB';
        $ShipperPostcode = '81750';
        $ShipperAddress = 'Masai';
        
    }elseif($Shipping_State == 'Negeri Sembilan' ){
        $locationCode = 'XSBN';
        $ShipperPostcode = '70300';
        $ShipperAddress = 'Seremban';
    
    }elseif($Shipping_State == 'Perlis' ){
        $locationCode = 'XKGR';
        $ShipperPostcode = '02600';
        $ShipperAddress = 'Arau';
        
    }elseif($Shipping_State == 'Pahang' ){

        //$locationCode = 'TPPM';
        $locationCode = 'XKUA';
        $ShipperPostcode = '25150';
        $ShipperAddress = 'Kuantan';
    
        
    }elseif($Shipping_State == 'Terengganu' ){
        $locationCode = 'OKMN';
        $ShipperPostcode = '24100';
        $ShipperAddress = 'Kijal';
        
    }else{
        $locationCode = 'ZSHM';
        $ShipperPostcode = '59200';
        $ShipperAddress = 'Shah Alam';
    }
    

      
            //Number of pieces not match with number of MPS
            $newBatchXML = '<NewDataSet><DocketDetail>
            		<Order_No>'.$Order_No.'</Order_No>
            		<ShipperID>'.$AGENT_ID.'</ShipperID>
            		<ShipperName>JOCOM MSHOPPING SDN BHD</ShipperName>
            		<AccountCode>'.$AGENT_ID.'</AccountCode>
            		<ShipperPostCode>'.$ShipperPostcode.'</ShipperPostCode>
            		<LocationCode>'.$locationCode.'</LocationCode>
            		<ShipperContactPerson>Asif</ShipperContactPerson>
            		<ShipperContactNumber>0322416637</ShipperContactNumber>
            		<ShipperAddress>Bukit Mertajam</ShipperAddress>
            		<Content>'.$Item_Name.'</Content>
            		<Waybill_No>'.$AWB_No.'</Waybill_No>
            		<Num_of_pieces>'.$N0_of_Pieces.'</Num_of_pieces>
            		<ConsigneeCompanyName>'.$Customer_Name.'</ConsigneeCompanyName>
            		<Consignee_Add1>'.$Shipping_Add1.'</Consignee_Add1>
            		<Consignee_City>'.$Shipping_City.'</Consignee_City>
            		<Consignee_State>'.$Shipping_State.'</Consignee_State>
            		<Post_Code>'.$Shipping_Zip.'</Post_Code>
            		<ContactPerson>'.$Customer_Name.'</ContactPerson>
            		<Consignee_TeleNo>'.$Shipping_MobileNo.'</Consignee_TeleNo>
            		<Consignee_MobileNo>'.$Shipping_MobileNo.'</Consignee_MobileNo>
            		<OTPApplicable>N</OTPApplicable>
            		<Consignee_TeleNo2>'.$Shipping_MobileNo.'</Consignee_TeleNo2>
            		<Total_Amt>0</Total_Amt>
            		<Payment_Mode>'.$Mode.'</Payment_Mode>
            		<Collectable_amount>0</Collectable_amount>
            		<Weight>'.$Weight.'</Weight>
            		<Length>0</Length>
            		<Width>0</Width>
            		<Height>0</Height>
            		<Unit_of_Measure>'.$UOM.'</Unit_of_Measure>
            		<Type_of_Service>'.$Type_of_Service.'</Type_of_Service>
            		<VAS_Type>1</VAS_Type>
            		<Shipment_Type>Parcel</Shipment_Type>
            		<Shipper_reference_number>'.$Order_No.'AT'.$AWB_No.'</Shipper_reference_number>
                    <ShipperContactName>Asif</ShipperContactName> 
                    <ShipperCity>'.$Shipping_City.'</ShipperCity> 
                    <ShipperState>'.$Shipping_State.'</ShipperState> 
            	</DocketDetail>
            </NewDataSet>';
            /*
            // =========== Amended on Nov 6, 2019=============
            $newBatchXML = '<NewDataSet><DocketDetail>
            		<Order_No>'.$Order_No.'</Order_No>
            		<ShipperID>'.$AGENT_ID.'</ShipperID>
            		<ShipperName>JOCOM MSHOPPING SDN BHD</ShipperName>
            		<AccountCode>'.$AGENT_ID.'</AccountCode>
            		<ShipperPostCode>'.$ShipperPostcode.'</ShipperPostCode>
            		<LocationCode>'.$locationCode.'</LocationCode>
            		<ShipperContactPerson>Asif</ShipperContactPerson>
            		<ShipperContactNumber>0322416637</ShipperContactNumber>
            		<ShipperAddress>Bukit Mertajam</ShipperAddress>
            		<Content>'.$Item_Name.'</Content>
            		<Waybill_No>'.$AWB_No.'</Waybill_No>
            		<Num_of_pieces>'.$N0_of_Pieces.'</Num_of_pieces>
            		<ConsigneeCompanyName>'.$Customer_Name.'</ConsigneeCompanyName>
            		<Consignee_Add1>'.$Shipping_Add1.'</Consignee_Add1>
            		<Consignee_City>'.$Shipping_City.'</Consignee_City>
            		<Consignee_State>'.$Shipping_State.'</Consignee_State>
            		<Post_Code>'.$Shipping_Zip.'</Post_Code>
            		<ContactPerson>'.$Customer_Name.'</ContactPerson>
            		<Consignee_TeleNo>'.$Shipping_MobileNo.'</Consignee_TeleNo>
            		<Consignee_MobileNo>'.$Shipping_MobileNo.'</Consignee_MobileNo>
            		<OTPApplicable>N</OTPApplicable>
            		<Consignee_TeleNo2>'.$Shipping_MobileNo.'</Consignee_TeleNo2>
            		<Total_Amt>0</Total_Amt>
            		<Payment_Mode>'.$Mode.'</Payment_Mode>
            		<Collectable_amount>0</Collectable_amount>
            		<Weight>'.$Weight.'</Weight>
            		<Length>0</Length>
            		<Width>0</Width>
            		<Height>0</Height>
            		<Unit_of_Measure>'.$UOM.'</Unit_of_Measure>
            		<Type_of_Service>'.$Type_of_Service.'</Type_of_Service>
            		<VAS_Type>1</VAS_Type>
            		<Shipment_Type>Parcel</Shipment_Type>
            		<Shipper_reference_number>'.$Order_No.'AT'.$AWB_No.'</Shipper_reference_number>
            	</DocketDetail>
            </NewDataSet>';
            
             
           
             $newBatchXML = '<NewDataSet><DocketDetail>
            		<Order_No>'.$Order_No.'</Order_No>
            		<ShipperID>'.$AGENT_ID.'</ShipperID>
            		<ShipperName>JOCOM MSHOPPING SDN BHD</ShipperName>
            		<AccountCode>'.$AGENT_ID.'</AccountCode>
            		<ShipperPostCode>59200</ShipperPostCode>
            		<LocationCode>ZSHM</LocationCode>
            		<ShipperContactPerson>Asif</ShipperContactPerson>
            		<ShipperContactNumber>0322416637</ShipperContactNumber>
            		<ShipperAddress>Kuala Lumpur</ShipperAddress>
            		<Content>'.$Item_Name.'</Content>
            		<Waybill_No>'.$AWB_No.'</Waybill_No>
            		<Num_of_pieces>'.$N0_of_Pieces.'</Num_of_pieces>
            		<ConsigneeCompanyName>'.$Customer_Name.'</ConsigneeCompanyName>
            		<Consignee_Add1>'.$Shipping_Add1.'</Consignee_Add1>
            		<Consignee_City>'.$Shipping_City.'</Consignee_City>
            		<Consignee_State>'.$Shipping_State.'</Consignee_State>
            		<Post_Code>'.$Shipping_Zip.'</Post_Code>
            		<ContactPerson>'.$Customer_Name.'</ContactPerson>
            		<Consignee_TeleNo>'.$Shipping_MobileNo.'</Consignee_TeleNo>
            		<Consignee_MobileNo>'.$Shipping_MobileNo.'</Consignee_MobileNo>
            		<OTPApplicable>N</OTPApplicable>
            		<Consignee_TeleNo2>'.$Shipping_MobileNo.'</Consignee_TeleNo2>
            		<Total_Amt>0</Total_Amt>
            		<Payment_Mode>'.$Mode.'</Payment_Mode>
            		<Collectable_amount>0</Collectable_amount>
            		<Weight>'.$Weight.'</Weight>
            		<Length>0</Length>
            		<Width>0</Width>
            		<Height>0</Height>
            		<Unit_of_Measure>'.$UOM.'</Unit_of_Measure>
            		<Type_of_Service>'.$Type_of_Service.'</Type_of_Service>
            		<VAS_Type>1</VAS_Type>
            		<Shipment_Type>Parcel</Shipment_Type>
            		<Shipper_reference_number>'.$Order_No.'AT'.$AWB_No.'</Shipper_reference_number>
            	</DocketDetail>
            </NewDataSet>';
          */
        // echo "<pre>";
        // print_r($newBatchXML);
        // echo "</pre>";
        // die();
            

        //$result = self::ApiCaller($newBatchXML);
        $result = self::ApiCallerNew($newBatchXML);
        //   print_r($result);   
        }else{
            $result = array(
                "Success" => 'No',
                "Reason" => 'No product Weight' 
            );
            
        }
        
       
        return array(
            "response"=>$result,
            "dataPost"=> (string)$newBatchXML
        );
        
    }
    
    
    public static function ApiCaller($xmlData){

    try{
        
   
        $clientID = Config::get('constants.LINE_CLEAR_CLIENTID_DEV');
        $userName = Config::get('constants.LINE_CLEAR_USERNAME_DEV');
        $password = Config::get('constants.LINE_CLEAR_PASSWORD_DEV');
        $xmlBatch = $xmlData;
    
        $post_fields = array(
            "userName" =>$userName,
            "password" =>$password,
            "clientId" =>$clientID,
            "xmlBatch" =>$xmlBatch
        );
       
        $header = array('Content-Type: application/x-www-form-urlencoded');

        if(Config::get('constants.ENVIRONMENT') == 'live'){
            
            $URL_ENV = Config::get('constants.LINE_CLEAR_WEBSERVICE_PRO');
                     
            //$ch = curl_init("http://13.67.50.144/lineclear_live/services/cust_ws_ver2.asmx/PushOrderData_New"); 
        }else{
            
            $URL_ENV = Config::get('constants.LINE_CLEAR_WEBSERVICE_DEV')."/PushOrderData_New";
            //$URL_ENV = Config::get('constants.LINE_CLEAR_WEBSERVICE_PRO')."/PushOrderData_New";
        }
        
        echo $URL_ENV;
        die();
        
        // echo "<pre>";
        // print_r($post_fields);
        // echo "</pre>";
        // echo $URL_ENV;
        // die();
        
        $ch = curl_init($URL_ENV);
        
        $post_fields = http_build_query($post_fields) ;
        
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //execute post
        $response = curl_exec($ch);
    
        // Check if any error occurred

 header('Content-Type: text/xml');
        echo $response;
        die();
//        $xml=simplexml_load_string($response);
        $ob = simplexml_load_string($response);
        $json  = json_encode($ob);
        
        
        
        $configData = json_decode($json, true);

        $ob2= simplexml_load_string($configData[0]);
        $json2  = json_encode($ob2);
        $configData2 = json_decode($json2, true);
       
       
        //print_r($configData2['Docket']);
        
        if(isset($configData2['Docket']['Succeed'])){
            
            return array(
                "Success"=>$configData2['Docket']['Succeed'],
                "DockNo"=>$configData2['Docket']['DockNo'],
                "Reason"=>$configData2['Docket']['Reason'],
            );
//             echo (count($configData2['Docket']));
        }else{
//            echo (count($configData2['Docket']));
            
            $reason = "";
            $counter = 1;
            foreach ($configData2['Docket'] as $key => $value) {
                $reason = $reason.$counter.": ".$value['Reason']."<p>";
                $result = array(
                    "Succeed" => $value['Succeed'],
                    "DockNo" => $value['DockNo'],
                    "Reason" => $reason,
                );
                
                $counter++;
            }
             return $result;
        }
        
    }catch (Exception $ex){
        
        echo $ex->getMessage();
    }
        
      
    }
    
    
    public static function ApiCallerNew($xmlData){

        try{
            
       
            $clientID = Config::get('constants.LINE_CLEAR_CLIENTID_DEV');
            $userName = Config::get('constants.LINE_CLEAR_USERNAME_DEV');
            $password = Config::get('constants.LINE_CLEAR_PASSWORD_DEV');
            $xmlBatch = $xmlData;
        
            $post_fields = array(
                "userName" =>$userName,
                "password" =>$password,
                "clientId" =>$clientID,
                "xmlBatch" =>$xmlBatch
            );
            
            // echo "<pre>";
            // print_r($xmlBatch);
            // echo "</pre>";


         	$soapUrl = 'http://lineclear.southeastasia.cloudapp.azure.com/LineClear/services/cust_ws_ver2.asmx?wsdl';
            $client = new SoapClient($soapUrl);
                
            $result = $client->PushOrderData_New(array('userName' => $userName, 'password' => $password, 'clientId' => $clientID, 'xmlBatch' => $xmlBatch))->PushOrderData_NewResult;
            //   var_dump($result);
            // exit();
            (string)$xml = new SimpleXMLElement((string)$result);
           
            // echo $xml->Docket->Succeed;
            
            
            $resultFinal = array(
                    "Success" => (string)$xml->Docket->Succeed,
                    "DockNo" => (string)$xml->Docket->WayBillNo,
                    "Reason" => (string)$xml->Docket->Reason,
                );
                
            return $resultFinal;
           
        }catch (Exception $ex){
            
            echo $ex->getMessage();
        }
        
    }
    
    
     public static function ApiCallerTracking($tracking_no){
        
        
        ob_clean(); 
        if(Config::get('constants.ENVIRONMENT') == 'live'){
            $clientID = Config::get('constants.LINE_CLEAR_CLIENTID_PRO');
            $userName = Config::get('constants.LINE_CLEAR_USERNAME_PRO');
            $password = Config::get('constants.LINE_CLEAR_PASSWORD_PRO');
        }else{
            $clientID = Config::get('constants.LINE_CLEAR_CLIENTID_DEV');
            $userName = Config::get('constants.LINE_CLEAR_USERNAME_DEV');
            $password = Config::get('constants.LINE_CLEAR_PASSWORD_DEV');
        }
        $xmlBatch = $xmlData;

        $post_fields = array(
            "userName" =>$userName,
            "password" =>$password,
            "clientId" =>$clientID,
            "DOCNO" =>$tracking_no
        );

        $post_fields = http_build_query($post_fields) . "\n";
      
    
        $post = "userName=".$userName."&password=".$password."&clientId=".$clientID."&DOCNO=".$tracking_no;
       
        $header = array('Content-Type: application/x-www-form-urlencoded');
        
        if(Config::get('constants.ENVIRONMENT') == 'live'){
            $ch = curl_init("http://13.67.50.144/lineclear_live/services/cust_ws_ver2.asmx/ConsignmentTrackEvents_Details_New");
        }else{
            $ch = curl_init("http://13.67.50.144:81/LineClear_Quality/services/cust_ws_ver2.asmx/ConsignmentTrackEvents_Details_New");
        }
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header );
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //execute post
       $response = curl_exec($ch);
        
//        $response = '<ArrayOfConsignmentTrack xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://tempuri.org/"><ConsignmentTrack><ERROR/><DOCKNO>131220164</DOCKNO><TRANSIT_LOCATION>JOHOR BAHRU, Johor
//Bahru</TRANSIT_LOCATION><ACTIVITY>Picked up and Booking processed</ACTIVITY><EVENTDATE>13 Dec 2016</EVENTDATE><EVENTTIME>18:32:50</EVENTTIME><NEXT_LOCATION/><TRACKING_CODE>B</TRACKING_CODE></ConsignmentTrack></ArrayOfConsignmentTrack>';
//        
      
       
       
//       
        $xml = simplexml_load_string($response, "SimpleXMLElement", LIBXML_NOCDATA);
        $json = json_encode($xml);
        $arrayCollection = json_decode($json,TRUE);
   
        if(isset($arrayCollection['ConsignmentTrack']['ACTIVITY'])){
           $activityResponse =  $arrayCollection['ConsignmentTrack']['ACTIVITY'];
            
        }else{
           $activityResponse =  $arrayCollection['ConsignmentTrack'][0]['ACTIVITY'];
        
        }
        
        return $activityResponse;
      
        
    }
    
    public static function xml2array ( $xmlObject, $out = array () )
    {
        foreach ( (array) $xmlObject as $index => $node ){
            $out[$index] = ( is_object ( $node ) ) ? self::xml2array ( $node ) : $node;
        }
            
        return $out;
    }
    
    
    public function tracktest(){
        
        
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
     
        $courier_id = 2; // LINE CLEAR ID
        
        try {
            
            DB::beginTransaction();
            
            $totalDelivered = 0;
            $CourierOrders = CourierOrder::getCourierOrders($courier_id);
            // echo "<pre>";
            // print_r($CourierOrders);
            // echo "</pre>";
            // die();
          
            foreach ($CourierOrders as $key => $value) {
                
                $tracking_no = substr($value->tracking_no, 0, -2);
        
                $soapUrl = 'http://lineclear.southeastasia.cloudapp.azure.com/LineClear/services/cust_ws_ver2.asmx?wsdl';
                $client = new SoapClient($soapUrl);
                
                $result = $client->TrackConsignment_Header_New(array('userName' => 'Lineclear', 'password' => 'Lineclear@2017', 'clientId' => 'Lineclear2017', 'DOCNO' => $tracking_no))->TrackConsignment_Header_NewResult->Consignment;
                // $xml = new SimpleXMLElement((string)$result);
                
              
                
                if($result->TRACKING_CODE != '' && $result->CURRENT_STATUS != ''){
                    
                    $CourierOrderInfo = CourierOrder::findByTrackingNo($value->tracking_no);
                    
                    $courierStatusCode = $result->TRACKING_CODE;
                    $courierStatus = $result->CURRENT_STATUS;
                    
                    // echo "<pre>";
                    // echo "[".$result->TRACKING_CODE."]";
                    // echo "[".$tracking_no."]";
                    // echo "[".$result->CURRENT_STATUS."]";
                    // echo "</pre>";
                    
                    $CourierOrder = CourierOrder::find($CourierOrderInfo->id);
                    $CourierOrder->courier_status_code = $courierStatusCode;
                    $CourierOrder->courier_status = $courierStatus;
                    $CourierOrder->save();
                    
                    if($result->CURRENT_STATUS == 'Delivered'){
                        //echo 'UPDATE';
                        $totalDelivered++;
                        //echo $CourierOrder->batch_id;
                        LogisticBatch::UpdateBatchLineClear($CourierOrder->batch_id, array("status"=>4));
                    }
                }
            }
            
            
        } catch (Exception $ex) {
            //echo "is_error";
            $is_error = true;
            //echo $ex->getMessage();
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
            
            return array(
                "TotalRecords"=>count($CourierOrders),
                "Delivered"=>$totalDelivered
                );
        }
        
    }
    
    
    
    
    
    
    
}