<?php

use Helper\ImageHelper as Image;

class MpayPrepaidController extends BaseController
{
    
    
    const  MPAY_ACCT_REGISTER_WEBURL = 'mpay/tpwalletapi/account/registeracc';
    const  MPAY_ACCT_CHANGE_PIN_WEBURL = 'account/PINChange';
    const  MPAY_ACCT_VIRTUALCARD_WEBURL = 'account/getVirtualCard';
    const  MPAY_ACCT_DOPAYMENT_WEBURL = 'account/dopayment';
    const  MPAY_ACCT_TOPUP_WEBURL = 'account/topupaccount';
    const  MPAY_ACCT_ENROLLMASTERCARD_WEBURL = 'account/addCard';
    const  MPAY_ACCT_GETACCTINFO_WEBURL = 'account/getaccountinfo';
    const  MPAY_ACCT_RESUBMMIT_WEBURL = 'account/reuploadoc';
    const  MPAY_ACCT_TOPUP_FPX = 1;
    const  MPAY_BALANCE_CODE = 'MBLN';
    const  MPAY_MASTERCARD_CODE = 'MMTR';

    /*
     * Desc : View list of registered MPay Account
     */
    public function index(){
        
        return View::make('mpay.index');
        
    }
    
    
    /*
     * @Desc    : To list out list of confirmed orders
     * @Param   : None
     * @Return  : (DATATABLE) format
     */
    public function cards() {
        
        // Get Orders
        
        $cards= DB::table('jocom_mpay_card')->select(array(
                        'jocom_mpay_card.id',
                        'jocom_mpay_card.user_id',
                        'jocom_mpay_card.login_id',
                        'jocom_mpay_card.name',
                        'jocom_mpay_card.mobileno',
                        'jocom_mpay_card.idno',
                        'jocom_mpay_card.email',
                        'jocom_mpay_card.card_type',
                        'jocom_mpay_card.mail_tracking',
                        'jocom_mpay_card.card_status'
                        ))
                    ->orderBy('jocom_mpay_card.id','asc');

//        return Datatables::of($cards)->make(true);
        return Datatables::of($cards)
        ->add_column('jocom_username', function($orders){
            $id = $orders->user_id;
            $Customer = Customer::find($id);
            return $Customer->username;
        })
        ->make(true);
        
    }
    
    public function updateMailStatus() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            $id = Input::get('card_id');
            $card_status = Input::get('card_status');
            $tracking_number = Input::get('tracking_number');
            $updateBy = Session::get('username');
            
            $MpayCard = MpayCard::find($id);
            $MpayCard->card_status = $card_status;
            $MpayCard->mail_tracking = $tracking_number;
            $MpayCard->updated_by = $updateBy;
            $MpayCard->save();
            
            $data = array(
                "save" => 1,
                "message" => 'Save Successfully'
            );
            
        
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

    /*
     * @Description : To encrypt authentication and message body in SHA-256 before
     * @Param : Partner ID
     * @Param : Partner Key
     * @Param : Concatenate the PID + PartnerKey + Timestamp + MessageBody
     */
    private static function generateAuthToken($timestamp, $dataSet){
        
        try{
            
            $authToken = "";
            $isError = 0;
            
            // Set Partner ID and Partner Key
            if(Config::get('constants.ENVIRONMENT') == 'live'){
                //$partnerID = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERID_DEV');
                //$partnerKey = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERKEY_DEV');
                $partnerID = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERID_PRO');
                $partnerKey = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERKEY_PRO');
            }else{
                $partnerID = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERID_DEV');
                $partnerKey = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERKEY_DEV');

                
            }
            //unset($dataSet['PID']);
            $sortedParam = self::sorterAlphabetOrder($dataSet);
            $messageBody2 = '';
            foreach($sortedParam as $key => $value){
                $messageBody2 = $messageBody2.$value;
               
            }
         
            //create Message body 
            $comma_separated = implode($sortedParam);

            $messageBody = (string)$comma_separated;
    
            $ConcatenateString = $partnerID.$partnerKey.$timestamp.$messageBody2;

            $hash =  strtoupper(hash('SHA256', $ConcatenateString)) ;
        
            $authToken = $hash;
            return $authToken;

        } catch (Exception $ex) {
            $isError = 1;
            
        }  finally {
            
            return array(
                "authToken" => $authToken,
                "isError" => $isError,
                "sortedParameter" =>$sortedParam
            );
            
        }

    }
    
    
    private static function setLRCDelimterMessageChecker($data,$type = false){
        
        $lrcString = "";
        $counter = 0;
        $delimeter2 = chr(30);
        
        if($type == true){
            
            foreach ($data as $key => $value) {
                if($counter == 0){
                    $delimeter = $delimeter2;
                    $lrcString = $delimeter2.$value;
                }else{
                    $lrcString = $lrcString.$delimeter.$value;
                }
                $counter++;
            }
            
        }else{
            
            foreach ($data as $key => $value) {
                if($counter == 0){
                    $delimeter = $delimeter2;
                    $lrcString = $delimeter2.$value;
                }else{
                    $lrcString = $lrcString.$delimeter.$value;
                }
                $counter++;
            }
            
        }
       

        return (string)$lrcString;
       
        
    }
    
     private static function setLRCDelimterMessageCheckerEnroll($data,$type = false){
        
        $lrcString = "";
        $counter = 0;
        $delimeter2 = chr(30);
        
        if($type == true){
            
            foreach ($data as $key => $value) {
                if($counter == 0){
                    $delimeter = $delimeter2;
                    $lrcString = $value;
                }else{
                    $lrcString = $lrcString.$delimeter.$value;
                }
                $counter++;
            }
            
        }else{
            
            foreach ($data as $key => $value) {
                if($counter == 0){
                    $delimeter = $delimeter2;
                    $lrcString = $value;
                }else{
                    $lrcString = $lrcString.$delimeter.$value;
                }
                $counter++;
            }
            
        }
       
        $lrcString =  $lrcString.$delimeter2;

        return (string)$lrcString;
       
        
    }
    
    
    /*
     * Desc : Check LRC checksum on MessageData
     */
    private static function getLRC($data,$type = false){
        
        if($type == true){
            $string = self::setLRCDelimterMessageCheckerEnroll($data,$type);
        }else{
            $string = self::setLRCDelimterMessageChecker($data,$type);
        }
         
        $lrc = "";
        $ReturnHexCode = "";
        $CharStore= [];
        $CharStore = str_split($string);
        $lrc = $CharStore[0];
        $hex = '';
        
        for ($x = 1; $x < count($CharStore); $x++){
          
            $lrc = $lrc^$CharStore[$x];
           
        }
        $ReturnHexCode = strtoupper(bin2hex($lrc));
        
        if(strlen($ReturnHexCode) == 1){
            $ReturnHexCode = "0".$ReturnHexCode;
        }
        
        return $ReturnHexCode;
        
    }
    
    /*
     * Desc : Sort Array key in alphabet order
     */
    private static function sorterAlphabetOrder($dataSet){
        
        $Params = $dataSet;
        ksort($Params);
        return $Params;
        
    }
    
    /*
     * Desc : Create MPay MasterCard account
     */
    public function createAccount() {

        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            DB::beginTransaction();

            
            $PartnerInfo = self::getPartnerInfo();
            
            $phoneNumber = Input::get("mobileno");
            $phoneNumber = str_replace("+6","",$phoneNumber);
            $phoneNumber = str_replace("+","",$phoneNumber);
            $phoneNumber = str_replace(""," ",$phoneNumber);
            
            if($phoneNumber[0] == '0'){
               $phoneNumber =  '6'.$phoneNumber;
            }
           
            $image_file = Input::file("image_user");
            $handle = fopen($image_file, "rb");
            $HexedContents = bin2hex( fread( $handle, filesize($image_file) ) );
            fclose($handle);
            
            // Mpay use base64 string
            $imagedata = file_get_contents($image_file);
            $base64 = base64_encode($imagedata);
            
            $authtoken = "";
            $timestamp = date("Ymdhis");
            $PID = $PartnerInfo['partnerID'];
            $registertype = 1;// Basic registration
            $username = Input::get("username");
            $name = Input::get("name");
            $nationality = Input::get("nationality");
            $idno = Input::get("idno");
            $email = Input::get("email");
            $mobileno = $phoneNumber;
            $dob = Input::get("dob");
            $loginid = Input::get("email"); // Customer Email address
            $address  = Input::get("address");
            $state = Input::get("state");
            $city = Input::get("city");
            $postalcode = Input::get("postcode");
            $mothermaidenname = Input::get("mothermaidenname");
            $useridimagefilename = $idno.".jpg";
            $useridimagestring = $base64; //$HexedContents;
            $agreementflag = 1;
            $pdpaFlag = 1;
            $marketingflag = 1;
            $lrc = 0;
            
            $Customer = Customer::where('username',$username)->first();
            

//            // Generate AuthToken 
//          
            $dob = date("Ymd", strtotime($dob));
            
            $user_id = $Customer->id ;
            $MpayCardUserExist = MpayCard::where("user_id",$user_id)->first();
            $MpayCardUserEmailExist = MpayCard::where("email",$email)->first();
            $MpayCardUserPhoneExist = MpayCard::where("mobileno",$mobileno)->first();
            
            if(count($Customer) <= 0 ){
                $existingRecord = true;    
                $validateErrorMessage = "No user found";
            }else{
                
                if(count($MpayCardUserExist) > 0 ){
                    $existingRecord = true;
                    $validateErrorMessage = "You account has been registered before";
                }
                
                if(count($MpayCardUserEmailExist)> 0 ){
                    $existingRecord = true;
                    $validateErrorMessage = "You email has been registered before";
                }
                
                if(count($MpayCardUserPhoneExist)> 0 ){
                    $existingRecord = true;
                    $validateErrorMessage = "You has mobile number been registered before";
                }
                
            }

            $param = array(
                "pid"=> $PID,
                "registerType"=> '1',
                "name"=> $name,
                "nationality"=> $nationality,
                "idno"=> $idno,
                "email"=> $email,
                "mobileno"=> $mobileno,
                "dob"=> $dob,
                "loginid"=> $loginid,
                "address"=> $address,
                "state"=> $state,
                "city"=> $city,
                "postalcode"=> $postalcode,
                "mothermaidenname"=> $mothermaidenname,
                "useridimagefilename"=> $useridimagefilename,
                "useridimagestring"=> $useridimagestring,
                "agreementflag"=> '1',
                "pdpaFlag"=> '1',
                "marketingFlag"=> 1,
                "parentname"=> '',
                "parentemail"=> '',
                "parentmobileno"=> '',
                "parentidno"=> '',
                "parentidimagefilename"=> '',
                "parentidimagestring"=> ''
                
            );
            
            

            if($existingRecord){
                
                $data = array(
                    "status_code" =>'error',
                    "login_id" =>'',
                    "idno" =>'',
                    "message" => 'Failed: '.$validateErrorMessage
                );
                
            }else{
                
            $authToken = self::generateAuthToken($timestamp, $param);
            
            $profile = array(
                "authtoken" => $authToken['authToken'],
                "timestamp"=>$timestamp,
                "PID"=>(string)$PID
                );
                
            
                
                
            $sortedParam = self::sorterAlphabetOrder($param);  
            $paramWithToken =   array_merge($profile,$sortedParam);

            $lrc = self::getLRC($paramWithToken);   
            $full = $paramWithToken;
            $full['lrc'] =  $lrc;
            
          

            // $file = Input::file("image_user");
            //     $file_ext = $file->getClientOriginalExtension();
                
            //     echo $file_ext;
                
            //     $dest_path = Config::get('constants.MPAY_IMAGE_PATH');
            //       echo $dest_path;
            //     $file_name = $Customer->id."_".$userAccInfo['uid'].".".$file_ext;
           
            $ApiMpayCreateAccResponse = self::APICaller('account/registeracc',$full);
           //  echo "<pre>";
            //    print_r($ApiMpayCreateAccResponse);
            //   echo "</pre>";
            
            if($ApiMpayCreateAccResponse['Header']['status'] == '00'){
                // success
                
                $userAccInfo = $ApiMpayCreateAccResponse['Body']['useracc_info'];
                $cardAccInfo = $ApiMpayCreateAccResponse['Body']['cardinfo'][0];
                
                $file = Input::file("image_user");
                $file_ext = $file->getClientOriginalExtension();
                
                $dest_path = Config::get('constants.MPAY_IMAGE_PATH');
                $file_name = $Customer->id."_".$userAccInfo['uid'].".".$file_ext;
                
                $upload_image = $image_file->move($dest_path, $file_name);
               
                // Save Record //
                $MpayCard = new MpayCard();
                $MpayCard->user_id = $Customer->id;
                $MpayCard->uid = $userAccInfo['uid'];
                $MpayCard->login_id = $userAccInfo['login_id'];
                $MpayCard->name = $userAccInfo['name'];
                $MpayCard->mobileno = $userAccInfo['mobileno'];
                $MpayCard->idno = $userAccInfo['idno'];
                $MpayCard->email = $userAccInfo['email'];
                $MpayCard->cardtoken = $cardAccInfo['cardtoken'];
                $MpayCard->card_vault_status = $cardAccInfo['card_vault_status'];
                $MpayCard->card_type = $cardAccInfo['cardtype'];
                $MpayCard->card_group = $cardAccInfo['cardGroup'];
                $MpayCard->mask_cardno = $cardAccInfo['mask_cardno'];
                $MpayCard->status = $cardAccInfo['status'];
                $MpayCard->api_response = json_encode($ApiMpayCreateAccResponse);
                $MpayCard->image_profile_filename = $file_name;
                $MpayCard->save();
                
                $MpayCardId = $MpayCard->id;
                
                //UPDATE REGISTERED CARD INFO
                foreach ($ApiMpayCreateAccResponse['Body']['cardinfo'] as $kCI => $vCI) {
                    
                    if($vCI['cardGroup'] == 1){
                        $cardType = 'MBLN';
                    }
                    
                    if($vCI['cardGroup'] == 2){
                        $cardType = 'MMTR';
                        $cardType = 'MMTR';
                        $maskNum = $vCI['mask_cardno'];
                        $cardTempPin = $vCI['card_temporary_pin'];
                    }
                    
                    $MpayRegisteredCardCard = new MpayRegisteredCard();
                    $MpayRegisteredCardCard->register_id = $MpayCardId;
                    $MpayRegisteredCardCard->card_number = $vCI['mask_cardno'];
                    $MpayRegisteredCardCard->cardtoken = $vCI['cardtoken'];
                    $MpayRegisteredCardCard->cardtype = $cardType;
                    $MpayRegisteredCardCard->card_temporary_pin = $vCI['card_temporary_pin'];
                    $MpayRegisteredCardCard->current_pin = $vCI['card_temporary_pin'];
                    $MpayRegisteredCardCard->cardGroup = $vCI['cardGroup'];
                    $MpayRegisteredCardCard->mask_cardno = $vCI['mask_cardno'];
                    $MpayRegisteredCardCard->save();
                    
                }
                //UPDATE REGISTERED CARD INFO
                
                
                $messageResponse = $ApiMpayCreateAccResponse['Header']['message'];
                $data = array(
                    "status_code" =>'success',
                    "message" =>$messageResponse,
                    "login_id" =>$userAccInfo['login_id'],
                    "idno" =>$userAccInfo['idno'],
                    "cardReference" => $ApiMpayCreateAccResponse['Body']['cardinfo']
                );
                
                // $subject = 'JOCOM Mpay MasterCard';
                // $mail = $Customer->email;
                // $dataMail = array(
                //         'username'      => $username,
                //         'masked_number'  => $maskNum,
                //         'temporary_pin'  => $cardTempPin
                // );
                
                // Mail::send('emails.mpay_register', $dataMail, function($message) use ($subject, $mail)
                //     {
                //         $message->from('payment@jocom.my', 'JOCOM');
                //         $message->to($mail, '')->subject($subject);
                      
                //     }
                // );
                
                
            }else{
                // Failed to register 
                $messageResponse = $ApiMpayCreateAccResponse['Header']['message'];
                $data = array(
                    "status_code" =>'error',
                    "message" => $messageResponse,
                    "login_id" =>'',
                    "idno" =>'',
                  //  "message" =>'Saving Failed',
                );
                
            }
            }
        } catch (Exception $ex) {
            
            
        } finally {
            if ($is_error) {
               // DB::rollback();
            } else {
                DB::commit();
            }
            
        }

        /* Return Response */
        return $data;

    
    }
    
    /*
     * Desc : Get Card Balance 
     */
    
    public static function checkBalance($jocom_username,$cardtype = 2){
        

            //$username = Input::get('username');
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];


            $username = $jocom_username;
            if($cardtype == 2){
                $cardCode = self::MPAY_MASTERCARD_CODE;
            }else{
                $cardCode = self::MPAY_BALANCE_CODE;
            }

        
            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)
            ->orderBy('id', 'desc')->first();
    
            
            $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                    ->where("cardtype",$cardCode)
                    ->orderBy('id', 'desc')->first();
           
            $uid = $MpayCardData->uid;
            $cardtoken = $MpayRegisteredCard->cardtoken;
           
            $dataSet = array(
                "uid" => $uid,
                "cardtoken" => $cardtoken,
            );
            
            $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
            $AuthToken = self::generateAuthToken($timestamp, $dataSet);
           
            $profile = array(
                    "authtoken" => $AuthToken['authToken'],
                    "timestamp"=>$timestamp,
                    "PID"=>(string)$PID
                    );
                    
            $sortedParam = self::sorterAlphabetOrder($dataSet);  
            $paramWithToken =   array_merge($profile,$sortedParam);
    
            $lrc = self::getLRC($paramWithToken);   
            $fullParam = $paramWithToken;
            $fullParam['lrc'] =  $lrc;
            
            // Call API to get latest Balance 
            $endpoint = 'account/getcardbalance';
            $APICallResponse = self::APICaller($endpoint, $fullParam);
          
            if($APICallResponse['Header']['status'] == '00'){
                $response = array(
                    "status_code" => 'success',
                    "balance" => number_format($APICallResponse['Body']['cardlist']['balance'], 2, '.', '') 
                );
            }else{
                $response = array(
                    "status_code" => 'error',
                    "balance" => 0.00
                );
            }
    
            /* Return Response */
            return $response;

    }
    
    public function getBalance() {


        $response = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        //$username = Input::get('username');
        $PartnerInfo = self::getPartnerInfo();
        $PID = $PartnerInfo['partnerID'];
        
     
        $username = Input::get("username");
        $card_type = Input::get("card_type");
        $cardCode = self::getCardCode($card_type);
        
        $Customer = Customer::where("username",$username)->first();
        $MpayCardData = MpayCard::where("user_id",$Customer->id)
        ->orderBy('id', 'desc')->first();
        
        if(count($MpayCardData) > 0 ){
            
        

        
        $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                ->where("cardtype",$cardCode)
                ->orderBy('id', 'desc')->first();
       
        $uid = $MpayCardData->uid;
        $cardtoken = $MpayRegisteredCard->cardtoken;
       
        $dataSet = array(
            "uid" => $uid,
            "cardtoken" => $cardtoken,
        );
        
        $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
        $AuthToken = self::generateAuthToken($timestamp, $dataSet);
       
        $profile = array(
                "authtoken" => $AuthToken['authToken'],
                "timestamp"=>$timestamp,
                "PID"=>(string)$PID
                );
                
        $sortedParam = self::sorterAlphabetOrder($dataSet);  
        $paramWithToken =   array_merge($profile,$sortedParam);

        $lrc = self::getLRC($paramWithToken);   
        $fullParam = $paramWithToken;
        $fullParam['lrc'] =  $lrc;
        
        // Call API to get latest Balance 
        $endpoint = 'account/getcardbalance';
        $APICallResponse = self::APICaller($endpoint, $fullParam);
      
        if($APICallResponse['Header']['status'] == '00'){
            $response = array(
                "status_code" => 'success',
                "balance" => number_format($APICallResponse['Body']['cardlist']['balance'], 2, '.', '') 
            );
        }else{
            $response = array(
                "status_code" => 'error',
                "message" => 'Failed to get balance',
                "balance" => 0.00
            );
        }
        
        }else{
            
            $response = array(
                "status_code" => 'error',
                "message" => 'No valid user found',
                "balance" => 0.00
            );
            
        }

        /* Return Response */
        return $response;

    
    }
    
    /*
     *  Allow user to change PIN through JOCOM APP
     */
    public function setChangePIN(){
        
        $is_error = false;
        
        try{
             
            DB::beginTransaction(); 
  
            $username = Input::get("username");
            $newPin = Input::get("newPin");
            $oldPin = Input::get("oldPin");
            $card_type = Input::get("card_type");
            $cardCode = self::getCardCode($card_type);
         
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];

       
            $Customer = Customer::where("username",$username)->first();
            
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->orderBy('id', 'desc')->first();
           
            $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                ->where("cardtype",$cardCode)
                ->where("current_pin",$oldPin)
                ->orderBy('id', 'desc')
                ->first();
            

            if(count($MpayRegisteredCard) > 0){
                
                $MpayRegisteredCardID = $MpayRegisteredCard->id;
                $uid = $MpayCardData->uid;
                $cardtoken = $MpayRegisteredCard->cardtoken;
                $currentPIN =  $MpayRegisteredCard->current_pin;

                $dataSet = array(
                    "uid" => $uid,
                    "oldPin" => $currentPIN,
                    "newPin" => $newPin,
                    "cardtoken" => $cardtoken,
                );

                $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                $AuthToken = self::generateAuthToken($timestamp, $dataSet);

                $profile = array(
                        "authtoken" => $AuthToken['authToken'],
                        "timestamp"=>$timestamp,
                        "PID"=>(string)$PID
                        );

                $sortedParam = self::sorterAlphabetOrder($dataSet);  
                $paramWithToken =   array_merge($profile,$sortedParam);


                $lrc = self::getLRC($paramWithToken,true);   
                $fullParam = $paramWithToken;
                $fullParam['lrc'] =  $lrc;
                
                $APICallResponse = self::APICaller(self::MPAY_ACCT_CHANGE_PIN_WEBURL, $fullParam);
                
                if($APICallResponse['Body']['pin_change_status'] == '00'){
                    
                    // UPDATE PIN
                    $MpayRegisteredCard = MpayRegisteredCard::find($MpayRegisteredCardID);
                    $MpayRegisteredCard->old_pin = $currentPIN;
                    $MpayRegisteredCard->current_pin = $newPin;
                    $MpayRegisteredCard->save();
                    
                    $response = array(
                        "status_code" => 'success',
                        "message" => 'PIN successfully changed',
                    );
                }else{
                    $is_error = true;
                    $response = array(
                        "status_code" => 'error',
                        "message" => 'PIN failed to change',
                    );
                }
                
            }else{
                $is_error = true;
                $response = array(
                    "status_code" => 'error',
                    "message" => 'PIN failed to change',
                );
                
                
            } 
            
        } catch (Exception $ex) {
            
             $is_error = true;

        }  finally {
            
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
            
            return $response;
            
        }
        
        
    }
    
    public function resubmitDoc(){
        
        $is_error = false;
        
        try{
            
            DB::beginTransaction(); 
             
            $username = Input::get("username");
          
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];

            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->orderBy('id', 'desc')->first();
            
            if(count($MpayCardData) > 0){
                
                $image_file = Input::file("image_user");
                $imagedata = file_get_contents($image_file);
                $base64Image = base64_encode($imagedata);
                
                $uid = $MpayCardData->uid;
               
                $dataSet = array(
                    "uid" => $uid,
                    "useridimagefilename" => $uid.".jpg",
                    "useridimagestring" => $base64Image,
                    "parentidimagefilename" => '',
                    "parentidimagestring" => ''
                );

                $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                $AuthToken = self::generateAuthToken($timestamp, $dataSet);

                $profile = array(
                        "authtoken" => $AuthToken['authToken'],
                        "timestamp"=>$timestamp,
                        "PID"=>(string)$PID
                        );

                $sortedParam = self::sorterAlphabetOrder($dataSet);  
                $paramWithToken =   array_merge($profile,$sortedParam);

                $lrc = self::getLRC($paramWithToken,true);   
                $fullParam = $paramWithToken;
                $fullParam['lrc'] =  $lrc;
                
               
                
                $APICallResponse = self::APICaller(self::MPAY_ACCT_RESUBMMIT_WEBURL, $fullParam);
                
                if($APICallResponse['Header']['status'] == '00'){
                   
                    $response = array(
                        "status_code" => 'success',
                        "message" => 'Resubmit Document Successful',
                    );
                }else{
                    $is_error = true;
                    $response = array(
                        "status_code" => 'error',
                        "message" => 'Resubmit Document Failed',
                    );
                }
                
            }else{
                $is_error = true;
                $response = array(
                    "status_code" => 'error',
                    "message" => 'No Record Found',
                );
                
            }
            
        
        } catch (Exception $ex) {
            
            $is_error = true;

        }  finally {
            
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
            
            return $response;
            
        }
        
        
    }
    
    public function enroll(){
        
        $isError = false;
        
        try{
            
            // Begin Transaction
            DB::beginTransaction();
            
            // Define parameter
            $username = Input::get("username");
            $pin = Input::get("pin");
            $digitCard = Input::get("digitCard");
            $secureCode = Input::get("secureCode");
            $forTest = Input::get("for_test");
            
            // Get Partner Info
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];
            
            // Get customer information
            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->first();
            $MpayAccountID = $MpayCardData->id;
            
            
            if(count($MpayCardData) > 0 ){
                
                $uid = $MpayCardData->uid;

                $dataSet = array(
                    "uid" => $uid,
                    "card_no_last4" => $digitCard,
                    "serial_no" => $secureCode,
                    "pin" => $pin,
                    "old_pin" =>''
                );

                $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                $AuthToken = self::generateAuthToken($timestamp, $dataSet);

                $profile = array(
                        "authtoken" => $AuthToken['authToken'],
                        "timestamp"=>$timestamp,
                        "PID"=>(string)$PID
                        );
                
                
                $profileLRC = array(
                        "authtoken" => $AuthToken['authToken']
                        );

                $sortedParam = self::sorterAlphabetOrder($dataSet);  
                $paramWithToken =   array_merge($profile,$sortedParam);
                
                $paramWithTokenForLRC =   array_merge($profile,$sortedParam);
                
//                echo "PARAM TOKEN LRC:";
//                echo "<pre>";
//                print_r($paramWithToken);
//                echo "</pre>";
                
                
                $lrc = self::getLRC($paramWithTokenForLRC,true);   
                $fullParam = $paramWithToken;
                $fullParam['lrc'] =  $lrc;
                 $APICallResponse = self::APICaller(self::MPAY_ACCT_ENROLLMASTERCARD_WEBURL, $fullParam);
               /*
                if($forTest > 0){
                    
                    if($forTest == 1){
                        $APICallResponse = array(
                            "Header" => array(
                                "message" => '',
                                "status" => '00'
                            ),
                            "Body" => array(
                                "uid" => 94,
                                "pin_change_status" => '00',
                                "card_token" => '525318lr63rY0618',
                                "name" => 'Muhammad WIra Izkandar',
                                "masked_card_no" => '525318xxxxxx0618'
                            )
                        );
                    }
                    
                    if($forTest == 2){
                        $APICallResponse = array(
                            "Header" => array(
                                "message" => 'Card Enrolment Failed',
                                "status" => '01'
                            ),
                            "Body" => array()
                        );
                    }
                }else{
                    // COMMENT FIRST
                    $APICallResponse = self::APICaller(self::MPAY_ACCT_ENROLLMASTERCARD_WEBURL, $fullParam);
                    
                    //echo "<pre>";
                   // print_r($APICallResponse);
                   // echo "</pre>";
                    
                }
                */
               
             
                if($APICallResponse['Header']['status'] == '00'){

//                    // UPDATE PIN
//                    $MpayRegisteredCard = MpayRegisteredCard::find($MpayRegisteredCardID);
//                    $MpayRegisteredCard->old_pin = $currentPIN;
//                    $MpayRegisteredCard->current_pin = $newPin;
//                    $MpayRegisteredCard->save();
                    
                    // Register the card under user account
                    
                    if(isset($APICallResponse['Header']['Body']['pin_change_status'])  && $APICallResponse['Header']['Body']['pin_change_status'] == '01'){
                        $SavedPin = '';
                        $remarks = 'ENROLLMENT PIN CHANGE FAILED';
                    }else{
                        $SavedPin = $pin;
                        $remarks = '';
                    }
                    
                    $MpayRegisteredCardCard = new MpayRegisteredCard();
                    $MpayRegisteredCardCard->register_id = $MpayAccountID;
                    $MpayRegisteredCardCard->card_number = $APICallResponse['Body']['masked_card_no'];
                    $MpayRegisteredCardCard->cardtoken = $APICallResponse['Body']['card_token'];
                    $MpayRegisteredCardCard->cardtype = 'MMTR';
                    $MpayRegisteredCardCard->card_temporary_pin = '';
                    $MpayRegisteredCardCard->current_pin = $SavedPin;
                    $MpayRegisteredCardCard->cardGroup = 2;
                    $MpayRegisteredCardCard->remarks = $remarks;
                    $MpayRegisteredCardCard->mask_cardno = $APICallResponse['Body']['masked_card_no'];
                    $MpayRegisteredCardCard->save();

                    $response = array(
                        "status_code" => 'success',
                        "message" => $APICallResponse['Header']['message'],
                        "maskcardnumber" => $APICallResponse['Body']['masked_card_no']
                    );
                }else{
                    $response = array(
                        "status_code" => 'error',
                        "message" => $APICallResponse['Header']['message']
                    );
                }
   
            }else{
                
                $response = array(
                    "status_code" => 'error',
                    "message" => 'No user record found!',
                );
                
            }
    
                   
        } catch (Exception $ex) {
            
            $isError = true;
            $response = array(
                "status_code" => 'error',
                "message" => 'Record failed to save',
            );

        }  finally {
            
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return $response;
            
        }
        
        
    }
    
    
    /*
     *  This is master function . To make payment 
     */
    public function doPayment(){
        
        
        
         try{
            
            $username = Input::get("username");
            $transactionID = Input::get("transactionID");
            $cardpin = Input::get("CID");
            
            // Get Total Amount to PAY //
            
            //$username = Input::get('username');
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];

            $username = Input::get("username");
            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->first();
            
            $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                ->where("cardtype",self::MPAY_MASTERCARD_CODE)
                ->where("current_pin",$oldPin)->first();
            
            
//            echo "<pre>";
//            print_r($MpayRegisteredCard);
//            echo "</pre>";
//            die();
            if(count($MpayRegisteredCard) > 0){
                
                $MpayRegisteredCardID = $MpayRegisteredCard->id;
                $uid = $MpayCardData->uid;
                $cardtoken = $MpayRegisteredCard->cardtoken;
                $currentPIN =  $MpayRegisteredCard->current_pin;

                $dataSet = array(
                    "uid" => $uid,
                    "amount" => $newPin,
                    "productdescription" => $transactionID,
                    "referenceno" => $transactionID, // TRANSACTION ID
                    "cardpin" => $cardpin,
                    "cardtoken" => $cardtoken,
                );

                $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                $AuthToken = self::generateAuthToken($timestamp, $dataSet);

                $profile = array(
                        "authtoken" => $AuthToken['authToken'],
                        "timestamp"=>$timestamp,
                        "PID"=>(string)$PID
                        );

                $sortedParam = self::sorterAlphabetOrder($dataSet);  
                $paramWithToken =   array_merge($profile,$sortedParam);

                //        echo "<pre>";
                //        print_r($paramWithToken);
                //        echo "</pre>";

                $lrc = self::getLRC($paramWithToken,true);   
                $fullParam = $paramWithToken;
                $fullParam['lrc'] =  $lrc;
                
                $APICallResponse = self::APICaller(self::MPAY_ACCT_CHANGE_PIN_WEBURL, $fullParam);
                
                if($APICallResponse['Header']['status'] == '00'){
                    
                    // UPDATE PIN
                    $MpayRegisteredCard = MpayRegisteredCard::find($MpayRegisteredCardID);
                    $MpayRegisteredCard->old_pin = $currentPIN;
                    $MpayRegisteredCard->current_pin = $newPin;
                    $MpayRegisteredCard->save();
                    
                    $response = array(
                        "status_code" => 'success',
                    );
                }else{
                    $response = array(
                        "status_code" => 'error',
                    );
                }

//                echo "<pre>";
//                print_r($fullParam);
//                print_r($APICallResponse);
//                print_r($response);
//                echo "</pre>";
//                die();
                
            }else{
                
                
                
            }
               

            
//            $endpoint = 'account/getcardbalance';
//            $APICallResponse = self::APICaller($endpoint, $fullParam);
            
                   
        } catch (Exception $ex) {

        }  finally {
            
        }
        
        
    }
    
    /*
     *  Allow user to change PIN through JOCOM APP
     */
    public function getAccountInformation(){
        
       
        
    try{
            
        
        $username = Input::get("username");
            
            //$username = Input::get('username');
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];


            $username = Input::get("username");
            $Customer = Customer::where("username",$username)->first();
            if(count($Customer) > 0){
            
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->first();
            
            $uid = $MpayCardData->uid;
          
            $dataSet = array(
                "uid" => $uid
            );
            
            if(count($MpayCardData) > 0 && $uid != ''){
                
                
            

            $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
            $AuthToken = self::generateAuthToken($timestamp, $dataSet);

            $profile = array(
                    "authtoken" => $AuthToken['authToken'],
                    "timestamp"=>$timestamp,
                    "PID"=>(string)$PID
                    );

            $sortedParam = self::sorterAlphabetOrder($dataSet);  
            $paramWithToken =   array_merge($profile,$sortedParam);

            //        echo "<pre>";
            //        print_r($paramWithToken);
            //        echo "</pre>";

            $lrc = self::getLRC($paramWithToken,true);   
            $fullParam = $paramWithToken;
            $fullParam['lrc'] =  $lrc;
             
            //$endpoint = 'account/getcardbalance';
            $APICallResponse = self::APICaller(self::MPAY_ACCT_GETACCTINFO_WEBURL, $fullParam);
            
            if($APICallResponse['Header']['status'] == '00'){
                    
                    
                    $accountInfo = $APICallResponse['Body']['accountinfo'];
                    
                    $docStatusData = DB::table('jocom_mpay_doc_status')
                        ->where("status_code",$accountInfo['docstatuslookup_id'])
                        ->first();
                    
                    $userStatusData = DB::table('jocom_mpay_user_status')
                        ->where("status_code",$accountInfo['userstatuslookup_id'])
                        ->first();
                    
                    $kysStatusData = DB::table('jocom_mpay_kyc_status')
                        ->where("status_code",$accountInfo['kycstatuslookup_id'])
                        ->first();
                    
                    $countryData = DB::table('jocom_mpay_country')
                        ->where("code_id",$accountInfo['countrylookup_id'])
                        ->first();
                        
                    $isResubmit = 0;
                    
                    $docstatus = $docStatusData->status;
                    $userstatus = $userStatusData->status;
                    $kycstatus = $kysStatusData->status;
                    $country = $countryData->description;
                    
                    $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)->get();
                    $cardInfo = array();
                    foreach ($MpayRegisteredCard as $keyCard => $valueCard) {
                        
                        if($valueCard->cardtype == self::MPAY_BALANCE_CODE){
                            
                            $checkBalance = self::checkBalance($username,1);
                            if( isset($checkBalance['status_code']) && $checkBalance['status_code'] == 'success'){
                                array_push($cardInfo, array(
                                    "maskcardnumber"=> $valueCard->card_number,
                                    "cardtype"=> $valueCard->cardtype,
                                    "balance"=> $checkBalance['balance'],
                                ));
                            }
                        }
                        
                        if($valueCard->cardtype == self::MPAY_MASTERCARD_CODE){
                            $checkBalance = self::checkBalance($username,2);
                            if( isset($checkBalance['status_code']) && $checkBalance['status_code'] == 'success'){
                                array_push($cardInfo, array(
                                    "maskcardnumber"=> $valueCard->card_number,
                                    "cardtype"=> $valueCard->cardtype,
                                    "balance"=> $checkBalance['balance'],
                                ));
                            }
                        }
                        
                    }
                    
                    $dest_path = Config::get('constants.MPAY_IMAGE_PATH');
                    $file_path= $dest_path.$MpayCardData->image_profile_filename;
                    
                    $imageLink = Image::link($file_path).'?u='.uniqid();
                    if($MpayCardData->image_profile_filename != ""){
                        $imageLink = Image::link($file_path).'?u='.uniqid();
                    }else{
                        $imageLink = '';
                    }
                    
                    switch ($accountInfo['kycstatuslookup_id']) {
                        case 0:
                            if (in_array($accountInfo['userstatuslookup_id'], array(1,2))) {
                                $approved = 0;
                                if($accountInfo['docstatuslookup_id'] == 2){
                                    $isResubmit = 1;
                                    $approved_msg = 'Your application need to resubmit image of IC/Passport due to image is not clear. Please re-submit the image in full size and clear.';
                                }else{
                                    $approved_msg = 'Your application still in processing. Kind contact us at enquiries@jocom.my for further information';
                                }
                            }else{
                                $approved = 0;
                                $approved_msg = 'Your application has been rejected. Status : '.$userstatus. ". Kind contact us at enquiries@jocom.my for further information.";
                            }
                            
                            break;
                        case 1:
                            $approved = 1;
                            $approved_msg = 'Your application has been approved';
                            break;
                        case 2:
                            
                            if (in_array($accountInfo['userstatuslookup_id'], array(1,2))) {
                                $approved = 2;
                                $approved_msg = 'Your application has been rejected. Kind contact us at enquiries@jocom.my for further information';
                            }else{
                                $approved = 0;
                                $approved_msg = 'Your application has been rejected. Status : '.$userstatus. ". Kind contact us at enquiries@jocom.my for further information";
                            }
                            
                            break;
                
                    }
                   
                    return array(
                        "status_code" => 'success',
                        "accountinfo" => array(
                            "uid" => $uid,
                            "userid" => $MpayCardData->user_id,
                            "id_no" => $accountInfo['id_no'],
                            "doc_status" => $docstatus,
                            "dob" => $accountInfo['dob'],
                            "mobile_no" => $accountInfo['mobile_no'],
                            "name" => $accountInfo['name'],
                            "country" => $country,
                            "user_status" => $userstatus,
                            "kyc_status" => $kycstatus,
                            "profileImage" => $imageLink,
                            "approved" => $approved,
                            "approval_message" => $approved_msg,
                            "resubmit" =>$isResubmit,
                            "cardInfo" => $cardInfo,
                        ),
                    );
            }else{
                 return array(
                        "status_code" => 'error',
                        "status_msg" => 'Request Error',
                        "accountinfo" => json_decode ("{}"),
                    );
            }
            
            }else{
                return array(
                        "status_code" => 'error',
                        "status_msg" => 'No record found',
                        "accountinfo" => json_decode ("{}"),
                    );
            }
            
            }else{
                
                 return array(
                        "status_code" => 'error',
                        "status_msg" => 'No valid user found',
                        "accountinfo" => json_decode ("{}"),
                    );
                
            }
            
            } catch (Exception $ex) {
                
                echo $ex->getMessage();
                
                return array(
                        "status_code" => 'error',
                        "status_msg" => 'System Error',
                        "accountinfo" => json_decode ("{}"),
                );

        }
        
    }
    
    /*
     *  Allow user to topup card
     */
    public function getTopup(){

        $is_error = false;
        
        try{
            
        
        $username = Input::get("username");
        $amount = Input::get("amount");
            

            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];
            $card_type = Input::get("card_type");
            $cardCode = self::getCardCode($card_type);
            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->orderBy('id', 'desc')->first();
                   
           
            $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                ->where("cardtype",$cardCode)
                ->orderBy('id', 'desc')
                ->first();
         
            if(count($MpayRegisteredCard) > 0 ){   

                $uid = $MpayCardData->uid;
                $cardtoken = $MpayRegisteredCard->cardtoken;
                $current_pin = $MpayRegisteredCard->current_pin;
    
                $dataSet = array(
                    "uid" => $uid,
                    "cardtoken" => $cardtoken,
                    "amount" => $amount * 100, // in cents
                    "channel" => self::MPAY_ACCT_TOPUP_FPX,
                );
    
                $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                $AuthToken = self::generateAuthToken($timestamp, $dataSet);
                //print_r($dataSet);
                $profile = array(
                        "authtoken" => $AuthToken['authToken'],
                        "timestamp"=>$timestamp,
                        "PID"=>(string)$PID
                        );
    
                $sortedParam = self::sorterAlphabetOrder($dataSet);  
                $paramWithToken =   array_merge($profile,$sortedParam);
    
                $lrc = self::getLRC($paramWithToken,true);   
                $fullParam = $paramWithToken;
                $fullParam['lrc'] =  $lrc;
                
               
                 
                $APICallResponse = self::APICaller(self::MPAY_ACCT_TOPUP_WEBURL, $fullParam);
    
                if($APICallResponse['Header']['status'] == '00'){
                        
                        $topupURL = $APICallResponse['Body']['topupinfo']['topupurl'];
    
                        // save in topup_request_transaction
                        $parts = parse_url($topupURL);
                        parse_str($parts['query'], $query);
    
                        $MpayTopupTransaction = new MpayTopupTransaction();
                        $MpayTopupTransaction->mas_txn_id = $query['masTxnID'];
                        $MpayTopupTransaction->amount = $amount;
                        $MpayTopupTransaction->created_by = $username;
                        $MpayTopupTransaction->status = 0;
                        $MpayTopupTransaction->save();
    
                        $response = array(
                            "status_code" => 'success',
                            "topupURL" => $topupURL,
                        );
                       
                       
                }else{
                    $is_error = true;
                    $response = array(
                        "status_code" => 'error',
                        "message" => "Request Failed",
                        "topupURL" => '',
                    );
                }
            
            }else{
                $is_error = true;
                $response = array(
                    "status_code" => 'error',
                    "message" => "No valid card",
                    "topupURL" => '',
                    );
            }
            
            } catch (Exception $ex) {
                $is_error = true;
            } finally {
                
                if ($is_error) {
                    DB::rollback();
                } else {
                    DB::commit();
                }
                
                return $response;
            }
        
    }
    
    public function updateTopupStatus(){
        
        
        $isError = false;
        $status_code = "success";
        $status_msg = "Topup Success!";
        
        try{
            
            // Get variables
            $masTxnID = Input::get("masTxnID");
            $message = Input::get("message");
            $status = Input::get("status");
            
            // Get transaction
            $MpayTopupTransaction = MpayTopupTransaction::where("mas_txn_id",$masTxnID)->first();
            
            // Check transaction
            if(count($MpayTopupTransaction) > 0 ){
                
                // Update transaction
                $tranID = $MpayTopupTransaction->id;
                $MpayTopupTransaction = MpayTopupTransaction::find($tranID);
                $MpayTopupTransaction->status = $status;
                $MpayTopupTransaction->resp_msg = $message;
                $MpayTopupTransaction->updated_by = 'MPAY_RESPONSE';
                $MpayTopupTransaction->is_success = 1;
                $MpayTopupTransaction->save();
                
            }else{
                $status_code = 'error';
                $status_msg = 'User record not found!';
            }
               
        } catch (Exception $ex) {
            
            $isError = true;
            $status_code = 'error';
            $status_msg = 'System failed to update';
            
        } finally {
            
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "status_code" => $status_code,
                "message" => $status_msg,
            );
              
        }
             
    }
    
    
    /*
     *  Allow user to change PIN through JOCOM APP
     */
    public function getVirtualCardNumber(){
        
        
        try{
            
        
        
            $username = Input::get("username");
            $cardpin = Input::get("cardpin");
            
            $card_type = Input::get("card_type");
            $cardCode = self::getCardCode($card_type);
            
            //$username = Input::get('username');
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];

            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->orderBy('id', 'desc')->first();
            
           
            $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                ->where("cardtype",$cardCode)
                ->orderBy('id', 'desc')
                ->first();
                
            if(count($MpayRegisteredCard) > 0 ){
                
                $uid = $MpayCardData->uid;
                $cardtoken = $MpayRegisteredCard->cardtoken;
                $current_pin = $MpayRegisteredCard->current_pin;
    
                $dataSet = array(
                    "uid" => $uid,
                    "cardtoken" => $cardtoken,
                    "cardpin" => $cardpin,
                );
    
                $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                $AuthToken = self::generateAuthToken($timestamp, $dataSet);
    
                $profile = array(
                        "authtoken" => $AuthToken['authToken'],
                        "timestamp"=>$timestamp,
                        "PID"=>(string)$PID
                        );
    
                $sortedParam = self::sorterAlphabetOrder($dataSet);  
                $paramWithToken =   array_merge($profile,$sortedParam);
    
                $lrc = self::getLRC($paramWithToken,true);   
                $fullParam = $paramWithToken;
                $fullParam['lrc'] =  $lrc;
                      
                $APICallResponse = self::APICaller(self::MPAY_ACCT_VIRTUALCARD_WEBURL, $fullParam);

                if($APICallResponse['Header']['status'] == '00'){
                        
                       return array(
                            "status_code" => 'success',
                            "message" => 'success',
                            "cardVirtualURL" => $APICallResponse['Body']['virtual_card_link'],
                            "cardno" => $APICallResponse['Body']['cardno']
                        );
                }else{
                     return array(
                            "status_code" => 'error',
                            "message" => isset($APICallResponse['Header']['message']) ? $APICallResponse['Header']['message'] : 'Failed to get response',
                            "cardVirtualURL" => '',
                            "cardno" => ''
                        );
                }
            
            }else{
                return array(
                        "status_code" => 'error',
                        "message" => 'error',
                        "cardVirtualURL" => '',
                        "cardno" => ''
                    );
                
            }
            
            
            
            } catch (Exception $ex) {
                
                return array(
                "status_code" => 'error',
                "cardVirtualURL" => '',
                "cardno" => ''
            );

        }
         
    }
    
    /*
     *  Get MPay Card information
     */
    public function getCardInformation(){
        
        
        
        
        try{
            
            $username = Input::get("username");
            $card_type = Input::get("card_type");
            $cardCode = self::getCardCode($card_type);
            $PartnerInfo = self::getPartnerInfo();
            $PID = $PartnerInfo['partnerID'];



            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->first();
            
            $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                ->where("cardtype",$cardCode)
                ->orderBy('id', 'desc')
                ->first();  
            $uid = $MpayCardData->uid;
            
            if(count($MpayRegisteredCard) > 0 ){
                
                $cardtoken = $MpayRegisteredCard->cardtoken;

                $dataSet = array(
                    "uid" => $uid,
                    "cardtoken" => $cardtoken,
                );
    
                $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                $AuthToken = self::generateAuthToken($timestamp, $dataSet);
    
                $profile = array(
                        "authtoken" => $AuthToken['authToken'],
                        "timestamp"=>$timestamp,
                        "PID"=>(string)$PID
                        );
    
                $sortedParam = self::sorterAlphabetOrder($dataSet);  
                $paramWithToken =   array_merge($profile,$sortedParam);
    
                $lrc = self::getLRC($paramWithToken,true);   
                $fullParam = $paramWithToken;
                $fullParam['lrc'] =  $lrc;

                $endpoint = 'account/getcardbalance';
                $APICallResponse = self::APICaller($endpoint, $fullParam);
                
                if(isset($APICallResponse['Header']['status']) && $APICallResponse['Header']['status'] == '00' ){
                    
                    $response = array(
                        "status_code" => 'success',
                        "message" => 'success',
                        "cardInfo" => $APICallResponse['Body']['cardlist']
                        );
                    
                }else{
                    
                    $response = array(
                        "status_code" => 'error',
                        "message" => 'Failed to get response',
                        "cardInfo" => array()
                        );

                }
   
            }else{
                $response = array(
                    "status_code" => 'error',
                    "message" => 'No Record Found'
                    );
            }
 
            
        } catch (Exception $ex) {
            
            $response = array(
                    "status_code" => 'error',
                    "message" => 'System error'
                    );

        }  finally {
            return $response;
        }
         
    }
    
    public function getMpayCountry(){
        
        $list = array();
        
        $countryList = DB::table('jocom_mpay_country')
                ->where("status",1)
                ->get();
        
        foreach ($countryList as $key => $value) {
            array_push($list, array(
                "id" => $value->code_id,
                "description" => $value->description,
                "status" => $value->status == 1 ? 'Active':'Inactive'
            ));
        }
        
        return $list;
        
    }
    
    public function getMpayState($country_code_id = 130){
        
        $list = array();
        
        $stateList = DB::table('jocom_mpay_state')
                ->where("country_code_id",$country_code_id)
                ->where("status",1)
                ->get();
        
        foreach ($stateList as $key => $value) {
            array_push($list, array(
                "id" => $value->code_id,
                "country_id" => $value->country_code_id,
                "description" => $value->description,
                "status" => $value->status == 1 ? 'Active':'Inactive'
            ));
        }
        
        return $list;
        
    }
    
    public function getUserCards(){
        
        
        $status = "success";
        $message = "";
        $response = array();
         
        try{
            
            $username = Input::get("username");
            $Customer = Customer::where("username",$username)->first();
            $MpayCardData = MpayCard::where("user_id",$Customer->id)->orderBy('id', 'desc')->first();
           
            $cardList = array();

            if(count($MpayCardData) > 0 ){

                $MpayRegisteredCard = MpayRegisteredCard::where("register_id",$MpayCardData->id)
                    ->orderBy('id', 'asc')
                    ->get();

                foreach ($MpayRegisteredCard as $key => $value) {

                    $subLineData = array(
                        "card_type"=> $value->cardtype,
                        "mask_cardno"=> $value->mask_cardno,
                        "activation"=> $value->activation,
                    );

                    array_push($cardList, $subLineData);

                }

            }else{
                $status = "error";
                $message = "No record found!";
            }
        
        } catch (Exception $ex) {
            $status = "error";
            $message = $ex->getMessage();
        }
       
        $response = array(
            "status_code" => $status,
            "message" => $message,
            "total_records" => count($cardList),
            "cards_info" =>$cardList
        );
        
        return $response;
        
    }




    public static function getPartnerInfo(){
        
        if(Config::get('constants.ENVIRONMENT') == 'live'){
            
            //$partnerID = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERID_DEV');
            //$partnerKey = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERKEY_DEV');
            $partnerID = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERID_PRO');
            $partnerKey = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERKEY_PRO');
        }else{
             
            $partnerID = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERID_DEV');
            $partnerKey = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERKEY_DEV');

            //$partnerID = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERID_PRO');
            //$partnerKey = Config::get('constants.MPAY_PPEMASTERCARD_PARTNERKEY_PRO');
        }
        
        return array(
            "partnerID" => $partnerID,
            "partnerKey" => $partnerKey,
        );
    }
    
    
    // Get card code
    private static function getCardCode($card_type){
        
        $code = '';
        
        switch ($card_type) {
            case 1 :
                $code = self::MPAY_BALANCE_CODE;
                break;
            
            case 2 :
                $code = self::MPAY_MASTERCARD_CODE;
                break;
        }
        
        return $code;
        
    }
    
    


    private static function APICaller($endpoint,$param){
     
        if (Config::get('constants.ENVIRONMENT') == 'live') {
            //$env = Config::get('constants.MPAY_PPEMASTERCARD_PARTNER_DEV_WEBSERVICE');
            $env = Config::get('constants.MPAY_PPEMASTERCARD_PARTNER_PRO_WEBSERVICE');
        }else{
            $env = Config::get('constants.MPAY_PPEMASTERCARD_PARTNER_DEV_WEBSERVICE');
            
        }

        $data_string = $param;
        $string = "";
        
        foreach ($data_string as $key => $value){
            if($string == ""){
                $string = $key."=".$value;
            }else{
                $string = $string."&".$key."=".$value;
            }

        }

       
        $post_fields = http_build_query($data_string) ;
       
        $headers= array(
                'Content-Type: application/x-www-form-urlencoded;',
            );
   
        $ch = curl_init($env.$endpoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $string);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch,CURLOPT_HTTPHEADER,$headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //execute post
        $resultMpay = curl_exec($ch);

        $result = json_decode($resultMpay, true);
        
        $MpayAPILog = new MpayAPILog();
        $MpayAPILog->username = Session::get('username') != '' ? Session::get('username') : '';
        $MpayAPILog->request = json_encode($data_string); 
        $MpayAPILog->response = $resultMpay;
        $MpayAPILog->url = $env.$endpoint;
        $MpayAPILog->save();
        
//        $jsonSample = '{"Header":{"message":"Registration Successful.","status":"00"},"Body":{"useracc_info":{"uid":"64","login_id":"weraw@gmail.com","name":"Muhammad Wira Izkandar","mobileno":"0172560994","idno":"880824055540","email":"weraw@gmail.com"},"cardinfo":[{"cardtoken":"636840YBYVK44242","card_vault_status":"3","cardtype":"MPay Balance","cardGroup":"1","mask_cardno":"636840xxxxxx4242","status":"1"}]}}';
//        $result = json_decode($jsonSample, true);
//        
        //echo "<pre>";
        //echo $env;
        //print_r($result);
        //echo "</pre>";
        return $result;
        
    }
    
    public function updateStatus() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            $AllCards = DB::table('jocom_mpay_card AS JMC')
                            ->select('JMC.*','JU.username')
                            ->leftJoin('jocom_user AS JU', 'JU.id', '=', 'JMC.user_id')
                            ->where('JMC.kyc_status',0)
                            ->where('JMC.activation',1)
                            ->get();
            
            
            foreach ($AllCards as $key => $value) {
                
                $username = $value->username;
             
                //$username = Input::get('username');
                $PartnerInfo = self::getPartnerInfo();
                $PID = $PartnerInfo['partnerID'];
                $Customer = Customer::where("username",$username)->first();
                
               
                if(count($Customer) > 0){

                    $MpayCardData = MpayCard::where("user_id",$Customer->id)->first();
                      
                    $uid = $MpayCardData->uid;
                    $dataSet = array(
                        "uid" => $uid
                    );
                    
                     

                    $timestamp = date("Ymdhis"); //'20170531040757'; //date("Ymdhis");
                    $AuthToken = self::generateAuthToken($timestamp, $dataSet);

                    $profile = array(
                            "authtoken" => $AuthToken['authToken'],
                            "timestamp"=>$timestamp,
                            "PID"=>(string)$PID
                            );

                    $sortedParam = self::sorterAlphabetOrder($dataSet);  
                    $paramWithToken =   array_merge($profile,$sortedParam);

                    $lrc = self::getLRC($paramWithToken,true);   
                    $fullParam = $paramWithToken;
                    $fullParam['lrc'] =  $lrc;

                    //$endpoint = 'account/getcardbalance';
                    $APICallResponse = self::APICaller(self::MPAY_ACCT_GETACCTINFO_WEBURL, $fullParam);
                    echo "<pre>";
                    print_r($APICallResponse);
                    echo "</pre>";
                    if($APICallResponse['Header']['status'] == '00'){

                        $accountInfo = $APICallResponse['Body']['accountinfo'];

                        $MpayCardProfile = MpayCard::find($MpayCardData->id);
                        $MpayCardProfile->kyc_status = $accountInfo['kycstatuslookup_id'];
                        $MpayCardProfile->doc_status = $accountInfo['docstatuslookup_id'];
                        $MpayCardProfile->user_status = $accountInfo['userstatuslookup_id'];
                        $MpayCardProfile->save();

                    }
                }
            
            }
        
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
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => '');
        return $response;

    
    }
    
}
