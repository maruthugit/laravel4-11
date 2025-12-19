<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Helper\ImageHelper as Image;
class RegionV2Controller extends BaseController
{
    
    const KLANGVALLEYSTATEID = 458999;
    const KLANGVALLEYSTATE = ['458004','458013','458015'];
    const KLANGVALLEYREGIONID = 1;


    public function index(){
        
        return View::make('sysadmin.region.index');
        
    }
    
    public function createRegion(){
        
        
        $state =  DB::table('state')->get();
        $countries = Country::getActiveCountry();
       
        return View::make('sysadmin.region.create')->with("countries",$countries);
        
    }
    
    public function getRegionByCountry() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            
            $sysAdminInfo = User::where("username",Session::get('username'))->first();
            $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();
            
            $country_id = Input::get('country_id');
            
            if($SysAdminRegion->region_id != 0){
                
                $region = Region::where("country_id",$country_id)
                    ->where("activation",1)
                    ->where("id",$SysAdminRegion->region_id)->get();
                
            }else{
                $region = Region::where("country_id",$country_id)
                    ->where("activation",1)->get();
            }
            
            $data['region'] = $region;
        
        } catch (Exception $ex) {
            $is_error = true;
            $message = $ex->getMessage();
        } 


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    
    public function editRegion($id){
        
        $Region = Region::getRegionInfo($id);
        $States = State::getCountryStatesRegion($Region->country_id);
//        echo "<pre>";
//        print_r($States);
//        echo "</pre>";
        
        return View::make('sysadmin.region.edit')
                ->with("region",$Region)
                ->with("states",$States);
        
    }
    
    public function getStateByCountry() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        
        try {
            
            
            $country_id = Input::get('country_id');
            $state = State::getCountryStatesRegion($country_id);
            $data['states'] = $state;
        
        } catch (Exception $ex) {
                    
        
        } finally {
            
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    public function saveRegion() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
//            echo "<pre>";
//            print_r(Input::all());
//            echo "</pre>";
//           
            
            if(Input::get('region_id') != ""){
                
                $img_thumb = Input::file('img_thumb');
                $region_id = Input::get('region_id');
                $region = Region::find(Input::get('region_id'));
                $region->region = Input::get('region_name');
                $region->email_pic = Input::get('region_email');
                $region->region_code = Input::get('region_code');
                $region->updated_by = Session::get('username');
                $region->activation = Input::get('status');
                
                
                $unique = time();
                $path = Config::get('constants.REGION_IMG_THUMB');

                if ($img_thumb!='') {

                    $ext = $img_thumb->getClientOriginalExtension();

                    if ($ext=='png') {
                        $image = $region_id . '-' . $unique . '.' . $img_thumb->getClientOriginalExtension();      
                        $img_thumb->move($path, $image);

                        // Image::make(sprintf('images/region/%s', $image))->resize(512, 512, function($constraint) { $constraint->aspectRatio(); })->save();

                        $region->img_thumb = $image;

                    }else{
                        return Redirect::back()->with('message','The valid filetype is PNG.');
                    }
                }

                $region->save();
                
                $ExistState = State::where("region_id",$region_id)->get();
                $StateList = Input::get('region_states');
                
                if(Input::get('status') == 0){
                    
                    foreach ($ExistState as $key => $value) {
                        $state = State::find($value->id);
                        $state->region_id = 0;
                        $state->save();
                    }
                    
                }else{
                    
                    if(count($StateList) > 0){
                        foreach ($ExistState as $key => $value) {
                            if (!in_array($value->id, $StateList)){
                                $state = State::find($value->id);
                                $state->region_id = 0;
                                $state->save();
                            } else{
                                if (($key = array_search($value->id, $StateList)) !== false) {
                                    unset($array[$key]);
                                }
                            }
                        }
                    }else{
                        foreach ($ExistState as $key => $value) {
                            $state = State::find($value->id);
                            $state->region_id = 0;
                            $state->save();
                        }
                    }
                    
                    
                    foreach ($StateList as $valueState) {

                        $Country = State::find($valueState);
                        $Country->region_id = $region_id;
                        $Country->save();

                    }
                    
                }
                  
                
            }else{
                
                $img_thumb = Input::file('img_thumb');
                $region = new Region;
                $region->region = Input::get('region_name');
                $region->region_code = Input::get('region_code');
                $region->email_pic = Input::get('region_email');
                $region->country_id = Input::get('region_country');
                $region->created_by = Session::get('username');
                $region->updated_by = Session::get('username');
                $region->status = 1;
                $region->activation = 1;
                    
                $region->save();

                $region_id = $region->id;

                if ($img_thumb!='') {

                    $ext = $img_thumb->getClientOriginalExtension();
                    $unique = time();
                    $path = Config::get('constants.REGION_IMG_THUMB');
                 
                    if ($ext=='png') {
                      
                        $image = $region_id . '-' . $unique . '.' . $img_thumb->getClientOriginalExtension();      
                        $img_thumb->move($path, $image);

                        // Image::make(sprintf('images/region/%s', $image))->resize(512, 512, function($constraint) { $constraint->aspectRatio(); })->save();

                        $region->img_thumb = $image;
                        $region->save();

                    }else{
                        return Redirect::back()->with('message','The valid filetype is PNG.');
                    }
                }
                $region->save();

                $region_id = $region->id;

                foreach (Input::get('region_states') as $value) {

                    $Country = State::find($value);
                    $Country->region_id = $region_id;
                    $Country->save();

                }
                
            }
           
            
            
            DB::beginTransaction();
        
        } catch (Exception $ex) {
                    } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }
        
        return Redirect::to('/region');

    
    }
    
    
    public function regionList() {

        // Get Orders
        $regions = DB::table('jocom_region')->select(array(
                        'jocom_region.id','jocom_region.region','jocom_region.activation','jocom_region.region_code','jocom_countries.name'
                        ))
                ->leftJoin('jocom_countries', 'jocom_countries.id', '=', 'jocom_region.country_id')
                ->orderBy('jocom_region.id','asc');
        
        

        return Datatables::of($regions)
                ->add_column('states', function($row){      // Added by Maruthu
                        return State::where('region_id',$row->id)->get();
                })
                ->make(true);
    
    }

    
     public function transactionregion(){
        
        
        
        $Region = Region::where('country_id',458)
                ->where('activation',1)->get();
      
     
        foreach ($Region as $key => $value) {
            
            $region_id = $value->id;
            $region_email_pic = $value->email_pic;
            $stateList = array();
            $State = State::getStateByRegion($region_id);
            
            foreach ($State as $keyS => $valueS) {
                $stateList[] = $valueS->id;
            };
            
            $from_date = date('Y-m-d',strtotime("-1 day")).' 12:30:00';
            $to_date = date('Y-m-d').' 12:29:59';
            
            /*
            
            if ((int)date('H') >= 12) {
                $from_date = date("Y-m-d").' 00:00:00';
                $to_date = date("Y-m-d").' 12:00:00';
            }else{
                $from_date = date('Y-m-d',strtotime("-1 days")).' 12:00:01';
                $to_date = date('Y-m-d',strtotime("-1 days")).' 23:59:59';
            }
           
            */
            
            $transactions = DB::table('jocom_transaction AS JT')
                ->leftJoin('jocom_cities AS JC', 'JC.id', '=', 'JT.delivery_city_id') 
                ->select('JT.*','JC.name','JC.state_id')        
                ->where('JT.insert_date', ">=",$from_date)
                ->where('JT.insert_date', "<=",$to_date)
                ->where('JT.status', "=",'completed')
                ->whereIn('JC.state_id', $stateList)
                ->get();
            
            
            // CSV SEND OUT 
            
            $fileName = "(".$value->region.")order_transaction_".date("Ymd his")."_".$from_date."_".$to_date.".csv";
            $path = Config::get('constants.CSV_FILE_PATH');
            $file = fopen($path.'/'.$fileName, 'w');

            fputcsv($file, ['Transaction ID','Transaction Date', 'Invoice Date','Invoice Number','DO Number', 
                'Product SKU','Product Name','Product Label', 'Product Quantity',
                'Reference Number','Buyer Name','Recipient Name','Recipient Contact Number',
                'Delivery Address 1','Delivery Address 2','Delivery Postcode','Delivery City','Delivery State','Delivery Country']);

            foreach ($transactions  as $keyT => $valueT)
            {   
                
                $transaction_id = $valueT->id;
                $transaction_date = $valueT->transaction_date;
                $transaction_invoice_date = $valueT->invoice_date;
                $transaction_invoice_no = $valueT->invoice_no;
                $transaction_do_number = $valueT->do_no;
                $transaction_reference_number = $valueT->id;
                
                $ElevenStreetOrder = ElevenStreetOrder::where("transaction_id",$transaction_id)->first();
                
                if(count($ElevenStreetOrder) > 0 ){
                    $reference_number = $ElevenStreetOrder->order_number;
                }else{
                    $reference_number = $valueT->id;
                }
                
                $buyer_name = $valueT->buyer_username;
                $recipient_name = $valueT->delivery_name;
                $recipient_contact_number = $valueT->delivery_contact_no;
                
                $delivery_address_1 = $valueT->delivery_addr_1;
                $delivery_address_2 = $valueT->delivery_addr_2;
                $delivery_postcode = $valueT->delivery_postcode;
                $delivery_city= $valueT->delivery_city;
                $delivery_state = $valueT->delivery_state;
                $delivery_country = $valueT->delivery_country;
                
                $TDetails = TDetails::where("transaction_id",$transaction_id)->get();
                
                foreach ($TDetails as $keyTD => $valueTD) {
                    
                    $Product = Product::where("sku",$valueTD->sku)->first();
                    
                    fputcsv($file, [
                        $transaction_id,
                        $transaction_date,
                        $transaction_invoice_date,
                        $transaction_invoice_no,
                        $transaction_do_number,
                        $valueTD->sku,
                        $Product->name,
                        $valueTD->price_label,
                        $valueTD->unit,
                        $reference_number,
                        $buyer_name,
                        $recipient_name,
                        $recipient_contact_number,
                        $delivery_address_1,
                        $delivery_address_2,
                        $delivery_postcode,
                        $delivery_city,
                        $delivery_state,
                        $delivery_country
                        
                    ]);
                }
                
                
            }

            fclose($file);

            $test = Config::get('constants.ENVIRONMENT');


            if ($test == 'test'){
                // $emails = 'wira.izkandar@jocom.my,weraw.hayek@gmail.com';
                $emails = 'maruthu@jocom.my,maruthujocom@gmail.com';
                $mail = explode(',', $emails);
                
            }else{
                //  $mail = $region_email_pic;
                $mail = explode(',', $region_email_pic);
            }
            $subject = "(".$value->region.") Order Transactions : " .$from_date." to ".$to_date;
            $attach = $path . "/" . $fileName;

            $body = array('title' => $subject);
            Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
                {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($mail, '')->subject($subject);
                    if ($test == 'test'){
                    $message->cc(["maruthu13@gmail.com", "maruthujocom@gmail.com"]);
                    }else{
                        $message->cc(["asif@jocom.my", "joshua.sew@jocom.my","agnes.chua@jocom.my"]);
                    }
                    
                    $message->attach($attach);
                }
            );
            
            // CSV SEND OUT
            
        }
        
      
        
        // Send CSV to selected region 
        
        
        
        
        
    }
    
    /*
     * Desc : API to get available regions base on APP platform
     */
    public function getAvailableRegionOLD(){
        
        $setData     = array();
        
        $platform_code = Input::get('platform_code');
        
        /** WHEN JUEPIN IS READY **/
        //$platform = Platform::where("platform_code",$platform_code)->first();
        /** WHEN JUEPIN IS READY **/
        
        $country_id = 458;
        $state = State::getCountryStatesRegion($country_id);

        $stateSet = array();
        
        // Defined Custom Area : Klang Valley
        $klangValley = ['458004','458013','458015'];
        
        foreach ($state as $key => $value) {
            
            /** WHEN JUEPIN IS READY **/
            /*
            switch ($platform_code) {
                case 'JOC':
                    $status = $value->region_id > 0 ? 'Active':'Inactive';
                    break;
                case 'JUE':
                    $status = $value->juepin_region_id > 0 ? 'Active':'Inactive';
                    break;

                default:
                    $status = $value->region_id > 0 ? 'Active':'Inactive';
                    break;
            }
            */
            /** WHEN JUEPIN IS READY **/
            
            if(!in_array($value->id, $klangValley)){
            array_push($stateSet, array(
                    "id"=>$value->id,
                    "name"=>$value->name,
                    "activation"=>$status ,
                ));
        }
        
        }
        
        
        
        array_unshift($stateSet, array(
                    "id"=>458999,
                    "name"=>'Klang Valley',
                    "activation"=>'Active' 
        ));
        
        $stateSet2 = array(
            array(
                    "id"=>458999,
                    "name"=>'Klang Valley',
                    "activation"=>'Active' 
            ),
            array(
                    "id"=>458001,
                    "name"=>'Johor',
                    "activation"=>'Active' 
            ),
            array(
                    "id"=>458010,
                    "name"=>'Pulau Pinang',
                    "activation"=>'Active' 
            )
        );
        // WHEN JUEPIN IS READY //
//        $setData['xml_data']['app_code'] = $platform->platform_code;
//        $setData['xml_data']['app_status'] = $platform->activation == 1 ? 'Active':'Inactive';
//        $setData['xml_data']['states']['state'] = $stateSet;
        // WHEN JUEPIN IS READY //
        
        $setData['xml_data']['app_code'] = 'JOC';
        $setData['xml_data']['app_status'] = 'Active';
        $setData['xml_data']['states']['state'] = $stateSet2;

        $data = $setData;

        return Response::json($data);
        
    }
    
    
    public function getAvailableRegion(){
        
        $setData     = array();
        $platform_code = Input::get('platform_code');
        $country_id = Input::get('country_id');
        $stateCollection = array();
        
        /** WHEN JUEPIN IS READY **/
        //$platform = Platform::where("platform_code",$platform_code)->first();
        /** WHEN JUEPIN IS READY **/
        
        if($country_id != "" || $country_id != null){
            
            
            if($country_id == 156){
                $app_code = 'B2D';
            }else{
                $app_code = 'JOC';
            }
            
            $regions = DB::table('jocom_region')->where('country_id', $country_id)->get();
            
            $country_id = $country_id;
            $stateSet = array(); 
            
            foreach ($regions as $key => $value) {
               if($value->region_code =='HQ') { //17 Mar 2020
                array_push($stateSet, array(
                    "id"=>$value->id,
                    "name"=>$value->region,
                    "activation"=>$value->activation ,
                ));
               } //17 Mar 2020
            }
            
            foreach ($regions as $key => $val) {
                if($val->region_code =='HQ') { //17 Mar 2020
                $resultStates = DB::table('jocom_country_states')->where('region_id', $val->id)->first();
          
                $region  = Region::find($val->id);
    
                if ($region->img_thumb!='') {
                    $file_name = Config::get('constants.REGION_IMG_THUMB').$region->img_thumb;
                    $image = Image::link($file_name);
                }else{
                    $image = '';
                }
                
                $statesID = DB::table('jocom_country_states')->where('region_id', $val->id)->get();
                $listState = [];
                
                foreach($statesID as $keyS => $valueS){
                    $listState[] = $valueS->id;
                }
                
               $statesID =  implode(",", $listState);
    
                array_push($stateCollection, array(
                    //'id'=>$statesID,
                    'id'=>$resultStates->id,
                    'name'=>$region->region,
                    'img_thumb'=> $image,
                    'activation'=> $region->activation
                    ));
                } //17 Mar 2020
            }
            
         
            
        }else{
            $app_code = 'JOC';
            $country_id = 458;
            $state = State::getCountryStatesRegion($country_id);

            $stateSet = array();
            
            // Defined Custom Area : Klang Valley
            $klangValley = ['458004','458013','458015'];
            
            foreach ($state as $key => $value) {
               
                if(!in_array($value->id, $klangValley)){
                    array_push($stateSet, array(
                        "id"=>$value->id,
                        "name"=>$value->name,
                        "activation"=>$status ,
                    ));
                }
               
            }
            
            array_unshift($stateSet, array(
                        "id"=>458999,
                        "name"=>'Klang Valley',
                        "activation"=>'Active' 
            ));
            
            $stateSet2 = array(
                // array(
                //         "id"=>458999,
                //         "name"=>'Klang Valley',
                //         "activation"=>'Active' 
                // ),
                // array(
                //         "id"=>458001,
                //         "name"=>'Johor',
                //         "activation"=>'Active' 
                // ),
                array(
                        "id"=>458010,
                        "name"=>'Penang',
                        "activation"=>'Active' 
                )
            );
            
            $stateCollection = array();
    
            //KLANG VALLEY
            $result2 = DB::table('jocom_country_states')->whereIn('id', $klangValley)->first();
           
            $region2 = Region::find($result2->region_id);
           
            if ($region2->img_thumb!='') {
                $file_name2 = Config::get('constants.REGION_IMG_THUMB').$region2->img_thumb;
                $image2 = Image::link($file_name2);
            }else{
                $image2 = '';
            }
    
            array_push($stateCollection, array(
                    'id'=>458999,
                    'name'=>"Klang Valley",
                    'img_thumb'=> $image2,
                    'activation'=> 'Active'
                    ));
             // END KLANG VALLEY
    
             //JOHOR & PENANG
            foreach ($stateSet2 as $key => $val) {
    
                $result = DB::table('jocom_country_states')->where('id', $val['id'])->first();
    
                $region  = Region::find($result->region_id);
    
                if ($region->img_thumb!='') {
                    $file_name = Config::get('constants.REGION_IMG_THUMB').$region->img_thumb;
                    $image = Image::link($file_name);
                }else{
                    $image = '';
                }
    
                array_push($stateCollection, array(
                    'id'=>$result->id,
                    'name'=>$val['name'],
                    'img_thumb'=> $image,
                    'activation'=> 'Active'
                    ));
     
            }
            
        }
        
        //Australia 
        // Mar 17 , 2020
        //   array_push($stateCollection, array(
        //             'id'=> "764078",
        //             'name'=> "Australia AU",
        //             'img_thumb'=> "https://api.jocom.com.my/images/region/5-1565149121.png",
        //             'activation'=> '1'
        //             ));
        
       
        

        // END JOHOR & PENANG
        
        // WHEN JUEPIN IS READY //
//        $setData['xml_data']['app_code'] = $platform->platform_code;
//        $setData['xml_data']['app_status'] = $platform->activation == 1 ? 'Active':'Inactive';
//        $setData['xml_data']['states']['state'] = $stateSet;
        // WHEN JUEPIN IS READY //
        
        $setData['xml_data']['app_code'] = $app_code;
        $setData['xml_data']['app_status'] = 'Active';
        $setData['xml_data']['states']['state'] = $stateCollection;

        $data = $setData;

        return Response::json($data);
        
    }
    
    
    /**
    * API TO GET REGIONS BASED ON COUTRIES COLLECTION
    * @param string app_id
    * @return mixed
    */

    public function getCoutryBasedRegionsApi() {
        
        $apps = ['JOC', 'B2D'];
        
        $app_id = Input::get('app_id');
        
        if(!empty($app_id) && in_array($app_id, $apps, TRUE)) {
            
            $data = [];
            
            $selectedCountries = [458, 156, 36];
            
            $data = DB::table('jocom_countries')->select('id', 'name')->whereIn('id', $selectedCountries)->get();

            foreach($data as $key => $country) {
                
                $data[$key]->regions = DB::table('jocom_region')->select('id', 'region', 'region_code', 'img_thumb', 'activation')->where('country_id', $country->id)->get();
                
                foreach($data[$key]->regions as $k => $value) {
                    
                    $region_data = DB::table('jocom_country_states')->where('region_id', $value->id)->first();
                    
                    $value->id = $region_data->id;
                    
                    if ($value->img_thumb!='') {
                        $value->img_thumb = Config::get('constants.REGION_IMG_THUMB').$value->img_thumb;
                        $value->img_thumb = Image::link($value->img_thumb);
                    }else{
                        $value->img_thumb = '';
                    }
                    
                    $value->activation = (!empty($value->activation == 1)) ? "true" : "false";
                }
                
                
                // Hard coded values for Australia on APP developer's request - 26/06/2019 11am
                // if($country->name == "Australia") {
                //     $data[0]->regions[] = [
                //         "id" => "764078",
                //         "region" =>  "Australia HQ",
                //         "region_code" => "AZHQ",
                //         "img_thumb" => "https://au.api.jocom.com.my/images/region/5-1561693386.png",
                //         "activation" => "true"
                //     ];
                // }
            }

            return Response::json($data, 200);


        } else {
            return Response::json(['message' => "Invalid Details"], 422);
        }
    }
}

?>
