<?php
use Helper\ImageHelper as Image;
class ApiV2Controller extends BaseController
{
    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyMember()
    {
        $data        = array();
        $get         = array();
        
        $req   = trim(Input::get('req'));
        $count = Input::get('count');
        $from  = Input::get('from');

        if ($count === false || !is_numeric($count)) {
            $count = 50;
        }
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'REGISTER_API';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

        switch ($req) {
            case 'register':
                $api  = new Api;
                $data = array_merge($data, $api->RegisterMember(Input::all()));
                break;
            case 'fb_register':
                $api  = new Api;
                $data = array_merge($data, $api->RegisterFbMember(Input::all()));
                break;
            case 'google_register':
                $api  = new Api;
                $data = array_merge($data, $api->RegisterGoogleMember(Input::all()));
                break;
            case 'apple_register':
                $api  = new Api;
                $data = array_merge($data, $api->RegisterAppleMember(Input::all()));
                break;
            default:
                $tmpdata = array(
                    'status'     => '0',
                    'status_msg' => '#805',
                );
                $data = array_merge($data, array('xml_data' => $tmpdata));
                break;
        }

        // return Response::json($data);
        return json_encode($data);
    }

    public function anyForgot()
    {
        $api         = new Api;
        $data        = array();
        $get         = array();
        
        $data = $api->MemberForgot(Input::all());

        // return Response::json($data);
        return json_encode($data);

    }

    public function anyComments()
    {
        $data    = array();
        $xmldata = array();
        $get     = array();

        $username = trim(Input::get('username')); // Buyer Username
        $sku      = trim(Input::get('sku')); // Product SKU

        $user = DB::table('jocom_user')->select('id')
            ->where('username', '=', $username)
            ->first();

        $product = DB::table('jocom_products')->select('id')
            ->where('sku', '=', $sku)
            ->first();

        $data['user_id']    = $user->id;
        $data['product_id'] = $product->id;

        $data['comment']   = trim(Input::get('comment')); // Comment
        $data['rating']    = trim(Input::get('rating')); // Comment Rating (0-5) default 0
        $data["insert_by"] = "phone_app";

        if (!is_numeric($data['rating']) && $data['rating'] < 1 && $data['rating'] > 5) {
            $data['rating'] = 0;
        }

        if ($data['user_id'] === false) {
            $xmldata['status']     = '0';
            $xmldata['status_msg'] = '#301';
        } elseif ($data['product_id'] === false) {
            $xmldata['status']     = '0';
            $xmldata['status_msg'] = '#302';
        } else {
            $id = DB::table('jocom_comments')->insertGetId(array(
                'comment_date' => date('Y-m-d H:i:s', time()),
                'user_id'      => $data['user_id'],
                'product_id'   => $data['product_id'],
                'comment'      => $data['comment'],
                'rating'       => $data['rating'],
                'insert_by'    => $data["insert_by"],
                'insert_date'  => date('Y-m-d H:i:s'),
                'modify_date'  => date('Y-m-d H:i:s'))
            );

            $xmldata['status']     = '1';
            $xmldata['status_msg'] = '#303';
        }

        // return Response::json($xmldata);
        return json_encode($xmldata);
    }

    public function anyUpdateprofile()
    {
        $api         = new Api;
        $get         = array();

        $data =$api->updateprofile(Input::all());

        // return Response::json($data);
        return json_encode($data);
    }

    public function anyCategory()
    {
        $limit  = Input::get('count', 50);
        $offset = Input::get('from', 0);

        $data = ApiProduct::fetch_category($limit, $offset, Input::all());

        //  return Response::json($data);
        return json_encode($data);
        
    }

    public function anyProduct()
    {
        $limit  = Input::get('count', 250);
        $offset = Input::get('from', 0);

        $data = ApiProduct::fetch_product($limit, $offset, Input::all());

        // return Response::json($data);
        return json_encode($data);
    }
    
    public function anyProductios()
    {
        $limit  = Input::get('count', 250);
        $offset = Input::get('from', 0);
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'API_PRODUCT_IOS_New';
        $ApiLog->data = print_r(Input::all());
        $ApiLog->save();
        
        $data = ApiProduct::fetch_product_ios($limit, $offset, Input::all());
        
        return json_encode($data);
        
    }
    
    public function anyProductsku()
    {
        $limit  = Input::get('count', 250);
        $offset = Input::get('from', 0);

        $data = ApiProduct::fetch_product($limit, $offset, Input::all());

        // return Response::json($data);
        return json_encode($data);
    }
    
    /**
     * Send notification on queue list
     * @return status (string) 'Success' on execution completed
     */
    public function push()
    {
        $batch        = Config::get('push.batch');
        $android      = PushQueue::operatingSystem('Android')->take($batch)->get();
        $iphone       = PushQueue::operatingSystem('iOS')->take($batch)->get();
       // $ipad         = PushQueue::operatingSystem('iPad')->take($batch)->get();
        $androidCount = $android->count();
        $iphoneCount  = $iphone->count();
       // $ipadCount    = $ipad->count();
       
    //   echo "<pre>";
    //     // print_r( $android);
    //   echo "count:". $androidCount;
    //   echo "</pre>";

        if ($androidCount > 0) {
            // Push::GCM()->push($android);
            Push::FCM()->push($android);
        }

        if ($iphoneCount > 0) {
            // Push::APNS()->push($iphone, 'iphone');
            Push::FCM()->push($iphone);
        }

       // if ($ipadCount > 0) {
        //    Push::APNS()->push($ipad, 'ipad');
        //}

        $inQueue = PushMessage::inQueue()->get();

        // Update message status to 'Completed' if the message already sent to all devices
        foreach ($inQueue as $message) {
            $queueCount = PushQueue::where('push_message_id', '=', $message->id)
                ->where('begin', '<=', date('Y-m-d H:i:s'))
                ->whereNull('sent_at')
                ->count();

            if ($queueCount == 0) {
                $message->status = 'Completed';
                $message->save();
            }
        }

        return json_encode([
            'status' => 'Success',
            'sent'   => $androidCount + $iphoneCount + $ipadCount,
        ]);
    }

    /**
     * Process of storing new device or updating existing device details
     * Intended to be accessible from device application
     * This will register the device application token with the system
     * @param POST os       (string) Device operating system
     *             token    (string) Application token
     *             push     (int) Enable push notification. 1 = Enabled; 0 = Disabled.
     * @return              JSON
     *             status   (string) 'Success' or 'Failed'
     */
    public function registerDevice()
    {
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'TOKEN_PUSH';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();
        
        $data   = Input::all();
        $device = new Device;
        $status = $device->register($data) ? 'Success' : 'Failed';

        return json_encode(['status' => $status]);
    }

    public function anyCategorysearch()
    {
        $keyword = Input::get('keyword');
        $limit   = Input::get('limit', 10);

        if (is_numeric($keyword)) {
            return Category::where('id', '=', $keyword)->limit($limit)->groupBy('category_name')->get()->toJson();
        } else {
            $keys      = ['id', 'category_name'];
            $relevants = [];
            $results   = Category::where('category_name', 'LIKE', "%{$keyword}%")
                ->orWhere('category_name_cn', 'LIKE', "%{$keyword}%")
                ->orWhere('category_name_my', 'LIKE', "%{$keyword}%")
                ->groupBy('category_name')
                ->get();

            foreach ($results as $result) {
                $rate             = floor((strlen($keyword) / strlen($result->category_name)) * 10000);
                $relevants[$rate] = "{$result->id}:{$result->category_name}";
            }

            krsort($relevants);

            foreach (array_slice($relevants, 0, 5) as $relevant) {
                $values    = explode(':', $relevant);
                $objects[] = array_combine($keys, $values);
            }

            return json_encode($objects);
        }
    }

    public function getMolpaystatus()
    {
        $transactionId = Input::get('tranID');
        $transaction   = Transaction::find($transactionId);
        $status        = 0;

        if ($transaction) {
            switch (object_get($transaction, 'status')) {
                case 'completed':
                    $status = 1;
                    break;
                case 'failed':
                    $status = 2;
                    break;
                default:
                    $status = 0;
                    break;
            }
        }

        return $status;
    }

    public function postMolpaystatus()
    {
        $transactionId = Input::get('tranID');
        $status        = Input::get('status');
        $transaction   = Transaction::find($transactionId);

        if ($transaction) {
            if ($status == 'success') {
                $transaction->status = 'completed';
                $transaction->save();

                return Redirect::to('checkout/paymentstatus/?tranID='.$transactionId.'&status=success');
            }

            if ($status == 'failed') {
                $transaction->status = 'failed';
                $transaction->save();
            }
        }

        return Redirect::to('checkout/paymentstatus/?tranID='.$transactionId.'&status=failed');
    }

    public function anyProductname(){
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $data=json_decode($json);
            
            if(isset($data->results[0])) {
                $response = array();
                foreach($data->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                    
                    if(in_array('postal_code', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];
            // print_r($region_name);die();
            $postcode = DB::table('postcode')->where('postcode', '=',  $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=',  $postcode->state_code)->first();

            if ($statecode->state_name== "Kuala Lumpur") {
                $name2 = "WP-".$statecode->state_name;
            }
            else{
                $name2 = $statecode->state_name;

            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
            }else{
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }

        }else{
            if(Input::get('stateid') != ''){

                $id = Input::get('stateid');
                    if($id == RegionController::KLANGVALLEYSTATEID){
                        $regions_id = RegionController::KLANGVALLEYREGIONID;
                    }else{
                $regions = DB::table('jocom_country_states')->where("id",$id)->select(DB::raw('region_id'))->first(); 
                $regions_id = $regions->region_id;
                $allRegion=0;
            }
        
            }else{

            }
            
        }
        
        $name= Input::get('name');
        // print_r($name);
        // echo $regions_id.'-'.$allRegion;
        // $products = DB::table('jocom_product_tags AS jpt')
        //             ->leftJoin('jocom_products AS jp', 'jp.id', '=', 'jpt.product_id')
        //             ->where('jpt.tag_name', 'LIKE', '%'.$name.'%')
        //             ->where('jp.status', '=', '1')
        //             ->select(DB::raw('jpt.tag_name, jp.name, jp.qrcode'))
        //             ->groupBy('jp.qrcode')
        //             ->limit(10)
        //             ->get();
        $regions_id = 1 ;
        $allRegion=0;
        // $products = DB::table('jocom_products AS jp')
        //             ->leftJoin('jocom_product_tags AS jpt', 'jp.id', '=', 'jpt.product_id')
        //             ->where('jp.status', '=', '1')
        //             ->whereIn('jp.region_id', [$regions_id,$allRegion])
        //             ->where('jp.name', 'LIKE', '%'.$name.'%')
        //             // ->orWhere('jpt.tag_name', 'LIKE', '%'.$name.'%')
        //             ->select(DB::raw('jp.name as tag_name, jp.name, jp.qrcode,jp.region_id'))
        //             ->orderBy('jp.region_id','DESC');
        
        $products = DB::table('jocom_products AS jp')
            ->leftJoin('jocom_product_tags AS jpt', 'jp.id', '=', 'jpt.product_id')
            ->where('jp.status', '=', '1')
            ->whereIn('jp.region_id', [1, 0])
            ->whereRaw('LOCATE(1347, jp.category) = 0') // exclude wavpay product result
            ->where('jp.name', 'LIKE', '%' . Input::get('name') . '%')
            ->select(DB::raw('jp.name as tag_name, jp.name, jp.qrcode, jp.region_id'))
            ->orderBy('jp.region_id','DESC')->limit((int)Input::get('count', 50))->get();
                    
       // $products = $products->groupBy('jp.qrcode');
        // $products = $products->limit(50)->get();
        return array("search" => $products);

    }

    
    public function anySearchproduct(){
        
        try{

            if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $data=json_decode($json);
            
            if(isset($data->results[0])) {
                $response = array();
                foreach($data->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                    
                    if(in_array('postal_code', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];
            // print_r($region_name);die();
            $postcode = DB::table('postcode')->where('postcode', '=',  $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=',  $postcode->state_code)->first();

            if ($statecode->state_name== "Kuala Lumpur") {
                $name2 = "WP-".$statecode->state_name;
            }
            else{
                $name2 = $statecode->state_name;

            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
            }else{
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }

        }else{
            if(Input::get('stateid') != ''){

                $id = Input::get('stateid');
                    if($id == RegionController::KLANGVALLEYSTATEID){
                        $regions_id = RegionController::KLANGVALLEYREGIONID;
                    }else{
                $regions = DB::table('jocom_country_states')->where("id",$id)->select(DB::raw('region_id'))->first(); 
                $regions_id = $regions->region_id;
            }
        
            }else{

            }
            
        } 
        
        $keyword= Input::get('keyword');

        $products = DB::table('jocom_products AS JP')
                    ->whereIn('JP.region_id',[$regions_id,$allRegion])
                    ->where('JP.name', 'LIKE', '%'.$keyword.'%')
                    ->where('JP.status', '=', 1)
                    ->orWhere('JP.sku', 'LIKE', '%'.$keyword.'%')
                    ->select(DB::raw('JP.id, JP.name, JP.qrcode, JP.sku, JP.status,JP.region_id'))
                    ->orderBy('JP.region_id','DESC')
                    ->limit(3)
                    ->get();
        
        

        return array("search" => $products);

        }catch (Exception $ex){
            echo $ex->getMessage();
        }

    }
    
    public function anySearchbaseproduct(){
        try{


            $keyword= Input::get('keyword');
    
            $products = DB::table('jocom_products AS JP')
                        ->where('JP.is_base_product', 1)
                        ->Where(function($query) use ($keyword)
                        {
                            $query->where('JP.name', 'LIKE', '%'.$keyword.'%')
                                  ->orWhere('JP.sku', 'LIKE', '%'.$keyword.'%');
                        })
                        ->select(DB::raw('JP.id, JP.name, JP.qrcode, JP.sku, JP.status'))
                        ->limit(10)
                        ->get();
                        
                        // dd(DB::getQueryLog());
    
            return json_encode(['search' => $products]);
    
        }catch (Exception $ex){
            echo $ex->getMessage();
        }

    }
    

    public function anyFeedback(){

        $limit  = Input::get('count', 300);
        $offset = Input::get('from', 0);

        $data =Feedback::fetch_feedback($limit, $offset, Input::all());
        
        // return Response::json($data);
        return json_encode($data);

    }
    
    public function anyJocommy(){

        $bannermasters = DB::table('jocommy_banners AS JB')
                        ->leftJoin('jocommy_banners_images as JBI','JB.id','=','JBI.banner_id')
                        ->where('JB.type',1)
                        ->where('JB.active_status',1)
                        ->select('JB.id','JB.type','JB.seq','JBI.file_name','JBI.heading','JBI.sub_heading')
                        ->orderBy('JB.seq','asc')
                        ->get();
        $banners = array();

        foreach ($bannermasters as $key => $value) {

            $file_name = Config::get('constants.NEW_JOCOMMY_BANNER_PATH').$value->file_name;

            array_push($banners, array(
                'file_name' => Image::link($file_name),
                'heading'    => $value->heading,
                'sub_heading' => $value->sub_heading,
                'seq' => $value->seq,
                ));
        }
        return $banners;

        
    }
    public function anyJocommyfestive(){

        $bannermasters = DB::table('jocommy_banners AS JB')
                        ->leftJoin('jocommy_banners_images as JBI','JB.id','=','JBI.banner_id')
                        ->where('JB.type',2)
                        ->where('JB.active_status',1)
                        ->select('JB.id','JB.type','JB.seq','JBI.file_name','JBI.heading','JBI.sub_heading')
                        ->orderBy('JB.seq','asc')
                        ->get();
        $banners = array();

        foreach ($bannermasters as $key => $value) {

            $file_name = Config::get('constants.NEW_JOCOMMY_BANNER_PATH').$value->file_name;

            array_push($banners, array(
                'file_name' => Image::link($file_name),
                'seq' => $value->seq,
                'key'=>$key,
                ));
        }
        return $banners;
        
    }
    
    public function anyCrossborder(){

        $bannermasters = DB::table('jocommy_banners AS JB')
                        ->leftJoin('jocommy_banners_images as JBI','JB.id','=','JBI.banner_id')
                        ->where('JB.type',3)
                        ->where('JB.active_status',1)
                        ->select('JB.id','JB.type','JB.seq','JBI.file_name','JBI.heading','JBI.sub_heading')
                        ->orderBy('JB.seq','asc')
                        ->get();
        $banners = array();

        foreach ($bannermasters as $key => $value) {

            $file_name = Config::get('constants.NEW_JOCOMMY_BANNER_PATH').$value->file_name;

            array_push($banners, array(
                'file_name' => Image::link($file_name),
                'seq' => $value->seq,
                'key'=>$key,
                ));
        }
        return $banners;
        
    }
    
    public function anyJocomvoucher(){

        $bannermasters = DB::table('jocommy_banners AS JB')
                        ->leftJoin('jocommy_banners_images as JBI','JB.id','=','JBI.banner_id')
                        ->where('JB.type',4)
                        ->where('JB.active_status',1)
                        ->select('JB.id','JB.type','JB.seq','JBI.file_name','JBI.heading','JBI.sub_heading')
                        ->orderBy('JB.seq','asc')
                        ->get();
        $banners = array();

        foreach ($bannermasters as $key => $value) {

            $file_name = Config::get('constants.NEW_JOCOMMY_BANNER_PATH').$value->file_name;

            array_push($banners, array(
                'file_name' => Image::link($file_name),
                'seq' => $value->seq,
                'key'=>$key,
                ));
        }
        return $banners;
        
    }
    
    public function anyFlashsale(){
        
        $from = Input::get('from');
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $test = json_decode($json);

            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                    
                    if(in_array('postal_code', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];
            
            $postcode = DB::table('postcode')->where('postcode', '=',  $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=',  $postcode->state_code)->first();
            // print_r($statecode->state_name);die();
            if ($statecode->state_name== "Kuala Lumpur") {
                $name2 = "WP-".$statecode->state_name;
            }
            else{
                $name2 = $statecode->state_name;

            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
               
            }else{
       
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }

        }
    
        
        $inputStateId = Input::get('stateid');
        // fast fix
        $inputStateId = "";
        
       // if(Input::get('id') != "" || Input::get('stateid') != ""){
           if(Input::get('id') != "" || $inputStateId != ""){    
            if(Input::get('id') != ""){
                 $stateid = strtoupper(Input::get('id'));
            }else{
                 $stateid = strtoupper(Input::get('stateid'));
            }
            //$stateid = strtoupper(Input::get('id'));
            $stateidInfo = State::find($stateid);
            $regions_id = $stateidInfo->region_id;
            
        } 

    //   print_r($regions_id);die();
       if ($regions_id !='') { 
        $flash = DB::table('jocom_flashsale AS JF')
                ->leftJoin('jocom_flashsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JP.region_id',$regions_id)
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }else{
          
           $flash = DB::table('jocom_flashsale AS JF')
                ->leftJoin('jocom_flashsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                //  ->whereDate('JF.valid_from', '=', $from)
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }
       
        $newArray = array();
        
       $i="1";
        foreach ($flash as $v) 
        {
            $newArray2 = array();
             
            if (!isset($newArray[$v->id]) && $i==1) {

                $newArray= array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
                        'rule_name' =>$v->rule_name,
                        'type' =>$v->type,
                    );
            }

            $wt = DB::table('jocom_product_price')->where('id',$v->label_id)->first();
            $pro = DB::table('jocom_products')->where('id',$v->product_id)->first();

            if ($pro->gst == 2) {
                    $tax_rate = Fees::get_tax_percent();
                    $after_gst = $v->actual_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori = $after_gst - $v->actual_price;
                    $ori_price = $v->actual_price + $gst_ori;

                    $after_gst2 = $v->promo_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori2 = $after_gst2 - $v->promo_price;
                    $promo_price = $v->promo_price + $gst_ori2;

                }else{        
                    $gst_ori = 0;
                    $ori_price = $v->actual_price;

                    $gst_ori2 = 0;
                    $promo_price = $v->promo_price;
                }

            $discPrice =  ApiProduct::hidePricing($username, $promo_price);  

            if ($discPrice != "0") {
                $discpercent = (($ori_price - $promo_price)*100) /$ori_price ; 
                $percent = number_format($discpercent, 0).'%';
            }else{
                $percent = '';
            }

            $total = DB::table('jocom_flashsale_stock')->where('fpid','=',$v->fid)->first();

            if (isset($total)) {
                //$total_sold = $total->stock - $v->qty;
                $total_sold = $v->qty;
            }else{
                $total_sold = 0;
            }

            $zones = [];
            $deliveryZones  = Delivery::getZonesByProduct($v->proId);

            foreach ($deliveryZones as $deliveryZone)
            {
                $zones[] = [
                    'zone' => $deliveryZone->zone_id,
                    'zone_name' => $deliveryZone->zone_name,
                ];
            }

            $points  = Comment::scopeCommentsRating($v->proId);
            
            $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
            $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
            $multiply = 1;

            if ($discPrice > 0) {
                $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiply;
            }else{
                $pointsJpoint = ($v->actual_price) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($v->actual_price) * $Bpoint->earn_rate * $multiply;
            }
            
            if ($pro->freshness_days !='' && $pro->freshness_days !=0) {
                $freshness_tag = $pro->freshness_days. ' days freshness.';
            }else{
                $freshness_tag = '';
            }

            $newArray2[] = array(
                        'label' => $v->label,
                        'label_id' => $v->label_id,
                        'priceopt' => "FS".$v->fid."[".$v->label_id."]",   
                        'price' => round($ori_price, 2),  
                        'promo_price' => $promo_price,  
                        'stock' => $v->limit_quantity,
                        'default'=> ($wt->default == 1) ? 'TRUE' : 'FALSE',
                        'p_weight' => $wt->p_weight,
                        'discount_percent' => $percent,
                        'jpoint'        => floor($pointsJpoint),
                        'bpoint'        => floor($pointsBpoint),
                    );

            $newArray['item'][] = array(
                    'sku' => $v->sku,
                    'qrcode' => $v->qrcode,
                    'name' => $v->name,
                    'description' => $v->description,
                    'delivery_time' => $v->delivery_time,
                    'min_qty'           => empty($v->min_qty) ? '' : $v->min_qty,
                    'max_qty'           => empty($v->max_qty) ? '' : $v->max_qty,
                    'img_1' => ( ! empty($v->img_1)) ? Image::link("images/data/".$v->img_1) : '',
                    'img_2' => ( ! empty($v->img_2)) ? Image::link("images/data/".$v->img_2) : '',
                    'img_3' => ( ! empty($v->img_3)) ? Image::link("images/data/".$v->img_3) : '',
                    'thumb_1' => ( ! empty($v->img_1)) ? Image::link("images/data/thumbs/".$v->img_1) : '',
                    'thumb_2' => ( ! empty($v->img_2)) ? Image::link("images/data/thumbs/".$v->img_2) : '',
                    'thumb_3' => ( ! empty($v->img_3)) ? Image::link("images/data/thumbs/".$v->img_3) : '',
                    'vid_1' => ( $v->vid_1 != "") ? $v->vid_1 : '',
                    'total_sold'=> $total_sold,
                    'zone_records'      => count($zones),
                    'delivery_zones'    => [$zones],
                    'option' => $newArray2,
                    'freshness'         => $pro->freshness, 
                    'freshness_days'    => $freshness_tag,
                    'bulk'              => empty($pro->bulk) ? '0' : $pro->bulk,
                    'halal'          => empty($pro->halal) ? '0' : $pro->halal,
                    'overall_rating' => empty($points) ? '' : $points,
                );
        $i++;
        }
        
        $data['xml_data']['batch'] = $newArray;
        
        return json_encode($data);
        

    }
    
    

    public function anyExclusivecorner(){
       
        $from = Input::get('from');
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $test = json_decode($json);

            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                    
                    if(in_array('postal_code', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];
            
            $postcode = DB::table('postcode')->where('postcode', '=',  $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=',  $postcode->state_code)->first();
            // print_r($statecode->state_name);die();
            if ($statecode->state_name== "Kuala Lumpur") {
                $name2 = "WP-".$statecode->state_name;
            }
            else{
                $name2 = $statecode->state_name;

            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
               
            }else{
       
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }

        }
    
        
        $inputStateId = Input::get('stateid');
        // fast fix
        $inputStateId = "";
        
       // if(Input::get('id') != "" || Input::get('stateid') != ""){
           if(Input::get('id') != "" || $inputStateId != ""){    
            if(Input::get('id') != ""){
                 $stateid = strtoupper(Input::get('id'));
            }else{
                 $stateid = strtoupper(Input::get('stateid'));
            }
            //$stateid = strtoupper(Input::get('id'));
            $stateidInfo = State::find($stateid);
            $regions_id = $stateidInfo->region_id;
            
        } 

    //   print_r($regions_id);die();
       if ($regions_id !='') { 
        $flash = DB::table('jocom_jocomexcorner AS JF')
                ->leftJoin('jocom_jocomexcorner_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JP.region_id',$regions_id)
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.main_title',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JF.banner_filename',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }else{
          
           $flash = DB::table('jocom_jocomexcorner AS JF')
                ->leftJoin('jocom_jocomexcorner_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                //  ->whereDate('JF.valid_from', '=', $from)
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.main_title',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JF.banner_filename',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }
       
        $newArray = array();
        $final = array();
        
      $i=1;
        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if ($i==1) {

                $newArray= array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
                        'main_title' =>$v->main_title,
                        'banner'=>'https://api.jocom.com.my/exclusivecorners/banner/'.$v->banner_filename,
                        'rule_name' =>$v->rule_name,
                        'type' =>$v->type,
                        'item' => array(),
                    );
            }

            $wt = DB::table('jocom_product_price')->where('id',$v->label_id)->first();
            $pro = DB::table('jocom_products')->where('id',$v->product_id)->first();

            if ($pro->gst == 2) {
                    $tax_rate = Fees::get_tax_percent();
                    $after_gst = $v->actual_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori = $after_gst - $v->actual_price;
                    $ori_price = $v->actual_price + $gst_ori;

                    $after_gst2 = $v->promo_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori2 = $after_gst2 - $v->promo_price;
                    $promo_price = $v->promo_price + $gst_ori2;

                }else{        
                    $gst_ori = 0;
                    $ori_price = $v->actual_price;

                    $gst_ori2 = 0;
                    $promo_price = $v->promo_price;
                }

            $discPrice =  ApiProduct::hidePricing($username, $promo_price);  

            if ($discPrice != "0") {
                $discpercent = (($ori_price - $promo_price)*100) /$ori_price ; 
                $percent = number_format($discpercent, 0).'%';
            }else{
                $percent = '';
            }

            $total = DB::table('jocom_jocomexcorner_stock')->where('fpid','=',$v->fid)->first();

            if (isset($total)) {
                //$total_sold = $total->stock - $v->qty;
                $total_sold = $v->qty;
            }else{
                $total_sold = 0;
            }

            $zones = [];
            $deliveryZones  = Delivery::getZonesByProduct($v->proId);

            foreach ($deliveryZones as $deliveryZone)
            {
                $zones[] = [
                    'zone' => $deliveryZone->zone_id,
                    'zone_name' => $deliveryZone->zone_name,
                ];
            }

            $points  = Comment::scopeCommentsRating($v->proId);
            
            $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
            $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
            $multiply = 1;

            if ($discPrice > 0) {
                $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiply;
            }else{
                $pointsJpoint = ($v->actual_price) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($v->actual_price) * $Bpoint->earn_rate * $multiply;
            }
            
            if ($pro->freshness_days !='' && $pro->freshness_days !=0) {
                $freshness_tag = $pro->freshness_days. ' days freshness.';
            }else{
                $freshness_tag = '';
            }
            $newArray2[] = array(
                        'label' => $v->label,
                        'label_id' => $v->label_id,
                        'priceopt' => "EC".$v->fid."[".$v->label_id."]",   
                        'price' => round($ori_price, 2),  
                        'promo_price' => $promo_price,  
                        'stock' => $v->limit_quantity,
                        'default'=> ($wt->default == 1) ? 'TRUE' : 'FALSE',
                        'p_weight' => $wt->p_weight,
                        'discount_percent' => $percent,
                        'jpoint'        => floor($pointsJpoint),
                        'bpoint'        => floor($pointsBpoint),
                    );

            $newArray['item'][]= array(
                    'sku' => $v->sku,
                    'qrcode' => $v->qrcode,
                    'name' => $v->name,
                    'description' => $v->description,
                    'delivery_time' => $v->delivery_time,
                    'min_qty'           => empty($v->min_qty) ? '' : $v->min_qty,
                    'max_qty'           => empty($v->max_qty) ? '' : $v->max_qty,
                    'img_1' => ( ! empty($v->img_1)) ? Image::link("images/data/".$v->img_1) : '',
                    'img_2' => ( ! empty($v->img_2)) ? Image::link("images/data/".$v->img_2) : '',
                    'img_3' => ( ! empty($v->img_3)) ? Image::link("images/data/".$v->img_3) : '',
                    'thumb_1' => ( ! empty($v->img_1)) ? Image::link("images/data/thumbs/".$v->img_1) : '',
                    'thumb_2' => ( ! empty($v->img_2)) ? Image::link("images/data/thumbs/".$v->img_2) : '',
                    'thumb_3' => ( ! empty($v->img_3)) ? Image::link("images/data/thumbs/".$v->img_3) : '',
                    'vid_1' => ( $v->vid_1 != "") ? $v->vid_1 : '',
                    'total_sold'=> $total_sold,
                    'zone_records'      => count($zones),
                    'delivery_zones'    => [$zones],
                    'option' => $newArray2,
                    'freshness'         => $pro->freshness, 
                    'freshness_days'    => $freshness_tag,
                    'bulk'              => empty($pro->bulk) ? '0' : $pro->bulk,
                    'halal'          => empty($pro->halal) ? '0' : $pro->halal,
                    'overall_rating' => empty($points) ? '' : $points,
                );
                
        $final[]=$newArray;
        $i++;
        }
      
        $data['xml_data']['batch'] = end($final);
        if(empty($data['xml_data']['batch'])){
            $oVal = new stdClass();
          $data['xml_data']['batch']=$oVal;
          return json_encode($data);
        }else{
            return json_encode($data);  
        }
           

    }


    public function anyCombodeals(){
       
        $from = Input::get('from');
       
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $test = json_decode($json);

            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                    
                    if(in_array('postal_code', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];
            
            $postcode = DB::table('postcode')->where('postcode', '=',  $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=',  $postcode->state_code)->first();
            // print_r($statecode->state_name);die();
            if ($statecode->state_name== "Kuala Lumpur") {
                $name2 = "WP-".$statecode->state_name;
            }
            else{
                $name2 = $statecode->state_name;

            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
               
            }else{
       
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }

        }
    
        
        $inputStateId = Input::get('stateid');
        // fast fix
        $inputStateId = "";
        
       // if(Input::get('id') != "" || Input::get('stateid') != ""){
           if(Input::get('id') != "" || $inputStateId != ""){    
            if(Input::get('id') != ""){
                 $stateid = strtoupper(Input::get('id'));
            }else{
                 $stateid = strtoupper(Input::get('stateid'));
            }
            //$stateid = strtoupper(Input::get('id'));
            $stateidInfo = State::find($stateid);
            $regions_id = $stateidInfo->region_id;
            
        } 

    //   print_r($regions_id);die();
       if ($regions_id !='') { 
        $flash = DB::table('jocom_combodeals AS JF')
                ->leftJoin('jocom_combodeals_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JP.region_id',$regions_id)
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.main_title',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JF.banner_filename',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }else{
          
           $flash = DB::table('jocom_combodeals AS JF')
                ->leftJoin('jocom_combodeals_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                //  ->whereDate('JF.valid_from', '=', $from)
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.main_title',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JF.banner_filename',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }
       
        $newArray = array();
        $final = array();
        $i=1;

        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if ($i==1) {

                $newArray= array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
                        'main_title' =>$v->main_title,
                        'banner'=>'https://api.jocom.com.my/combodeals/banner/'.$v->banner_filename,
                        'rule_name' =>$v->rule_name,
                        'type' =>$v->type,
                        'item' => array(),
                    );
            }

            $wt = DB::table('jocom_product_price')->where('id',$v->label_id)->first();
            $pro = DB::table('jocom_products')->where('id',$v->product_id)->first();

            if ($pro->gst == 2) {
                    $tax_rate = Fees::get_tax_percent();
                    $after_gst = $v->actual_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori = $after_gst - $v->actual_price;
                    $ori_price = $v->actual_price + $gst_ori;

                    $after_gst2 = $v->promo_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori2 = $after_gst2 - $v->promo_price;
                    $promo_price = $v->promo_price + $gst_ori2;

                }else{        
                    $gst_ori = 0;
                    $ori_price = $v->actual_price;

                    $gst_ori2 = 0;
                    $promo_price = $v->promo_price;
                }

            $discPrice =  ApiProduct::hidePricing($username, $promo_price);  

            if ($discPrice != "0") {
                $discpercent = (($ori_price - $promo_price)*100) /$ori_price ; 
                $percent = number_format($discpercent, 0).'%';
            }else{
                $percent = '';
            }

            $total = DB::table('jocom_combodeals_stock')->where('fpid','=',$v->fid)->first();

            if (isset($total)) {
                //$total_sold = $total->stock - $v->qty;
                $total_sold = $v->qty;
            }else{
                $total_sold = 0;
            }

            $zones = [];
            $deliveryZones  = Delivery::getZonesByProduct($v->proId);

            foreach ($deliveryZones as $deliveryZone)
            {
                $zones[] = [
                    'zone' => $deliveryZone->zone_id,
                    'zone_name' => $deliveryZone->zone_name,
                ];
            }

            $points  = Comment::scopeCommentsRating($v->proId);
            
            $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
            $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
            $multiply = 1;

            if ($discPrice > 0) {
                $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiply;
            }else{
                $pointsJpoint = ($v->actual_price) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($v->actual_price) * $Bpoint->earn_rate * $multiply;
            }
            
            if ($pro->freshness_days !='' && $pro->freshness_days !=0) {
                $freshness_tag = $pro->freshness_days. ' days freshness.';
            }else{
                $freshness_tag = '';
            }

            $newArray2[] = array(
                        'label' => $v->label,
                        'label_id' => $v->label_id,
                        'priceopt' => "CD".$v->fid."[".$v->label_id."]",   
                        'price' => round($ori_price, 2),  
                        'promo_price' => $promo_price,  
                        'stock' => $v->limit_quantity,
                        'default'=> ($wt->default == 1) ? 'TRUE' : 'FALSE',
                        'p_weight' => $wt->p_weight,
                        'discount_percent' => $percent,
                        'jpoint'        => floor($pointsJpoint),
                        'bpoint'        => floor($pointsBpoint),
                    );

            $newArray['item'][] = array(
                    'sku' => $v->sku,
                    'qrcode' => $v->qrcode,
                    'name' => $v->name,
                    'description' => $v->description,
                    'delivery_time' => $v->delivery_time,
                    'min_qty'           => empty($v->min_qty) ? '' : $v->min_qty,
                    'max_qty'           => empty($v->max_qty) ? '' : $v->max_qty,
                    'img_1' => ( ! empty($v->img_1)) ? Image::link("images/data/".$v->img_1) : '',
                    'img_2' => ( ! empty($v->img_2)) ? Image::link("images/data/".$v->img_2) : '',
                    'img_3' => ( ! empty($v->img_3)) ? Image::link("images/data/".$v->img_3) : '',
                    'thumb_1' => ( ! empty($v->img_1)) ? Image::link("images/data/thumbs/".$v->img_1) : '',
                    'thumb_2' => ( ! empty($v->img_2)) ? Image::link("images/data/thumbs/".$v->img_2) : '',
                    'thumb_3' => ( ! empty($v->img_3)) ? Image::link("images/data/thumbs/".$v->img_3) : '',
                    'vid_1' => ( $v->vid_1 != "") ? $v->vid_1 : '',
                    'total_sold'=> $total_sold,
                    'zone_records'      => count($zones),
                    'delivery_zones'    => [$zones],
                    'option' => $newArray2,
                    'freshness'         => $pro->freshness, 
                    'freshness_days'    => $freshness_tag,
                    'bulk'              => empty($pro->bulk) ? '0' : $pro->bulk,
                    'halal'          => empty($pro->halal) ? '0' : $pro->halal,
                    'overall_rating' => empty($points) ? '' : $points,
                );
        $final[]=$newArray;
        $i++;
        }
        
        $data['xml_data']['batch'] = end($final);
      
        if(empty($data['xml_data']['batch'])){
            $oVal = new stdClass();
          $data['xml_data']['batch']=$oVal;
          return json_encode($data);
        }else{
            return json_encode($data);  
        }

    }
    
    public function anyDynamicsale(){
       
        $from = Input::get('from');
       
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $test = json_decode($json);

            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                    
                    if(in_array('postal_code', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];
            
            $postcode = DB::table('postcode')->where('postcode', '=',  $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=',  $postcode->state_code)->first();
            // print_r($statecode->state_name);die();
            if ($statecode->state_name== "Kuala Lumpur") {
                $name2 = "WP-".$statecode->state_name;
            }
            else{
                $name2 = $statecode->state_name;

            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
               
            }else{
       
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }

        }
    
        
        $inputStateId = Input::get('stateid');
        // fast fix
        $inputStateId = "";
        
       // if(Input::get('id') != "" || Input::get('stateid') != ""){
           if(Input::get('id') != "" || $inputStateId != ""){    
            if(Input::get('id') != ""){
                 $stateid = strtoupper(Input::get('id'));
            }else{
                 $stateid = strtoupper(Input::get('stateid'));
            }
            //$stateid = strtoupper(Input::get('id'));
            $stateidInfo = State::find($stateid);
            $regions_id = $stateidInfo->region_id;
            
        } 

    //   print_r($regions_id);die();
       if ($regions_id !='') { 
        $flash = DB::table('jocom_dynamic_sale AS JF')
                ->leftJoin('jocom_dynamicsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JP.region_id',$regions_id)
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.main_title',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JF.banner_filename',
                    'JF.title_filename',
                    'JF.title_mime',
                    'JF.banner_filename',
                    'JF.banner_mime',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }else{
          
           $flash = DB::table('jocom_dynamic_sale AS JF')
                ->leftJoin('jocom_dynamicsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'))
                //  ->whereDate('JF.valid_from', '=', $from)
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.main_title',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JF.banner_filename',
                    'JF.title_filename',
                    'JF.title_mime',
                    'JF.banner_filename',
                    'JF.banner_mime',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.min_qty',
                    'JP.max_qty',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JFP.seq')
                ->get();
       }
       
        $newArray = array();
        $final = array();
        $i=1;

        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if($i==1){

                $newArray= array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
                        'main_title' =>$v->main_title,
                        'rule_name' =>$v->rule_name,
                        'banner'=>'https://api.jocom.com.my/dynamic/images/'.$v->banner_filename,
                        'type' =>$v->type,
                        'title_filename' => ( ! empty($v->title_filename)) ? Image::link("dynamic/images/".$v->title_filename) : '',
                        'title_mime' => $v->title_mime,
                        'banner_filename' => ( ! empty($v->banner_filename)) ? Image::link("dynamic/images/".$v->banner_filename) : '',
                        'banner_mime' => $v->banner_mime,
                        'item' => array(),
                    );
            }

            $wt = DB::table('jocom_product_price')->where('id',$v->label_id)->first();
            $pro = DB::table('jocom_products')->where('id',$v->product_id)->first();

            if ($pro->gst == 2) {
                    $tax_rate = Fees::get_tax_percent();
                    $after_gst = $v->actual_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori = $after_gst - $v->actual_price;
                    $ori_price = $v->actual_price + $gst_ori;

                    $after_gst2 = $v->promo_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori2 = $after_gst2 - $v->promo_price;
                    $promo_price = $v->promo_price + $gst_ori2;

                }else{        
                    $gst_ori = 0;
                    $ori_price = $v->actual_price;

                    $gst_ori2 = 0;
                    $promo_price = $v->promo_price;
                }

            $discPrice =  ApiProduct::hidePricing($username, $promo_price);  

            if ($discPrice != "0") {
                $discpercent = (($ori_price - $promo_price)*100) /$ori_price ; 
                $percent = number_format($discpercent, 0).'%';
            }else{
                $percent = '';
            }

            $total = DB::table('jocom_dynamicsale_stock')->where('fpid','=',$v->fid)->first();

            if (isset($total)) {
                //$total_sold = $total->stock - $v->qty;
                $total_sold = $v->qty;
            }else{
                $total_sold = 0;
            }

            $zones = [];
            $deliveryZones  = Delivery::getZonesByProduct($v->proId);

            foreach ($deliveryZones as $deliveryZone)
            {
                $zones[] = [
                    'zone' => $deliveryZone->zone_id,
                    'zone_name' => $deliveryZone->zone_name,
                ];
            }

            $points  = Comment::scopeCommentsRating($v->proId);
            
            $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
            $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
            $multiply = 1;

            if ($discPrice > 0) {
                $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiply;
            }else{
                $pointsJpoint = ($v->actual_price) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($v->actual_price) * $Bpoint->earn_rate * $multiply;
            }
            
            if ($pro->freshness_days !='' && $pro->freshness_days !=0) {
                $freshness_tag = $pro->freshness_days. ' days freshness.';
            }else{
                $freshness_tag = '';
            }

            $newArray2[] = array(
                        'label' => $v->label,
                        'label_id' => $v->label_id,
                        'priceopt' => "DY".$v->fid."[".$v->label_id."]",   
                        'price' => round($ori_price, 2),  
                        'promo_price' => $promo_price,  
                        'stock' => $v->limit_quantity,
                        'default'=> ($wt->default == 1) ? 'TRUE' : 'FALSE',
                        'p_weight' => $wt->p_weight,
                        'discount_percent' => $percent,
                        'jpoint'        => floor($pointsJpoint),
                        'bpoint'        => floor($pointsBpoint),
                    );

            $newArray['item'][] = array(
                    'sku' => $v->sku,
                    'qrcode' => $v->qrcode,
                    'name' => $v->name,
                    'description' => $v->description,
                    'delivery_time' => $v->delivery_time,
                    'min_qty'           => empty($v->min_qty) ? '' : $v->min_qty,
                    'max_qty'           => empty($v->max_qty) ? '' : $v->max_qty,
                    'img_1' => ( ! empty($v->img_1)) ? Image::link("images/data/".$v->img_1) : '',
                    'img_2' => ( ! empty($v->img_2)) ? Image::link("images/data/".$v->img_2) : '',
                    'img_3' => ( ! empty($v->img_3)) ? Image::link("images/data/".$v->img_3) : '',
                    'thumb_1' => ( ! empty($v->img_1)) ? Image::link("images/data/thumbs/".$v->img_1) : '',
                    'thumb_2' => ( ! empty($v->img_2)) ? Image::link("images/data/thumbs/".$v->img_2) : '',
                    'thumb_3' => ( ! empty($v->img_3)) ? Image::link("images/data/thumbs/".$v->img_3) : '',
                    'vid_1' => ( $v->vid_1 != "") ? $v->vid_1 : '',
                    'total_sold'=> $total_sold,
                    'zone_records'      => count($zones),
                    'delivery_zones'    => [$zones],
                    'option' => $newArray2,
                    'freshness'         => $pro->freshness, 
                    'freshness_days'    => $freshness_tag,
                    'bulk'              => empty($pro->bulk) ? '0' : $pro->bulk,
                    'halal'          => empty($pro->halal) ? '0' : $pro->halal,
                    'overall_rating' => empty($points) ? '' : $points,
                );
        $final[]=$newArray;
        $i++;
        }
        
        $data['xml_data']['batch'] = end($final);

        if(empty($data['xml_data']['batch'])){
         $oVal = new stdClass();
          $data['xml_data']['batch']=$oVal;
          return json_encode($data);
        }else{
            return json_encode($data);  
        }

    }
    
    public function anyFlashsalenew(){
        // print_r(Input::all());
        $from = Input::get('from');
        // $to = Input::get('to');
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $test = json_decode($json);

            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                    
                    if(in_array('postal_code', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];
            
            $postcode = DB::table('postcode')->where('postcode', '=',  $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=',  $postcode->state_code)->first();
            // print_r($statecode->state_name);die();
            if ($statecode->state_name== "Kuala Lumpur") {
                $name2 = "WP-".$statecode->state_name;
            }
            else{
                $name2 = $statecode->state_name;

            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
               
            }else{
       
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }

        }
    
        if(Input::get('id') != "" || Input::get('stateid') != ""){
            
            if(Input::get('id') != ""){
                 $stateid = strtoupper(Input::get('id'));
            }else{
                 $stateid = strtoupper(Input::get('stateid'));
            }
            //$stateid = strtoupper(Input::get('id'));
            $stateidInfo = State::find($stateid);
            $regions_id = $stateidInfo->region_id;
            
        } 
    //   print_r($regions_id);die();
       if ($regions_id !='') { 
        $flash = DB::table('jocom_flashsale AS JF')
                ->leftJoin('jocom_flashsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JP.region_id',$regions_id)
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                ->whereDate('JF.valid_from', '=', $from)
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JF.id','desc')
                ->get();
       }else{
          
           $flash = DB::table('jocom_flashsale AS JF')
                ->leftJoin('jocom_flashsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id')
                ->where('JF.status',1)
                ->where('JFP.activation',1)
                 ->where('JF.valid_from', '=', $from)
                //  ->whereDate('JF.valid_from', '=', $from)
                ->where('JFP.qty','>=',0)
                ->select(
                    'JF.id',
                    'JF.type',
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
                    'JFP.id as fid',
                    'JFP.label_id',
                    'JFP.label',
                    'JFP.product_id',
                    'JP.sku',
                    'JP.delivery_time',
                    'JP.name',
                    'JP.id as proId',
                    'JP.description',
                    'JP.img_1',
                    'JP.img_2',
                    'JP.img_3',
                    'JP.vid_1',
                    'JP.qrcode',
                    'JFP.actual_price',
                    'JFP.promo_price',
                    'JFP.limit_quantity',
                    DB::raw("
                        (CASE
                            WHEN JFP.qty <= 0 THEN '0' ELSE JFP.qty
                        END) AS qty
                    ")
                )
                ->orderBy('JF.id','desc')
                ->get();
       }
       
        $newArray = array();
        
        // echo "<pre>";
        // print_r($flash);
        // echo "</pre>";

        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if (!isset($newArray[$v->id]) ) {

                $newArray = array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
                        'rule_name' =>$v->rule_name,
                        'type' =>$v->type,
                    );
            }

            $wt = DB::table('jocom_product_price')->where('id',$v->label_id)->first();
            $pro = DB::table('jocom_products')->where('id',$v->product_id)->first();

            if ($pro->gst == 2) {
                    $tax_rate = Fees::get_tax_percent();
                    $after_gst = $v->actual_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori = $after_gst - $v->actual_price;
                    $ori_price = $v->actual_price + $gst_ori;

                    $after_gst2 = $v->promo_price * (( 100 + ($tax_rate)) / 100) ;
                    $gst_ori2 = $after_gst2 - $v->promo_price;
                    $promo_price = $v->promo_price + $gst_ori2;

                }else{        
                    $gst_ori = 0;
                    $ori_price = $v->actual_price;

                    $gst_ori2 = 0;
                    $promo_price = $v->promo_price;
                }

            $discPrice =  ApiProduct::hidePricing($username, $promo_price);  

            if ($discPrice != "0") {
                $discpercent = (($ori_price - $promo_price)*100) /$ori_price ; 
                $percent = number_format($discpercent, 0).'%';
            }else{
                $percent = '';
            }

            $total = DB::table('jocom_flashsale_stock')->where('fpid','=',$v->fid)->first();

            if (isset($total)) {
                //$total_sold = $total->stock - $v->qty;
                $total_sold = $v->qty;
            }else{
                $total_sold = 0;
            }

            $zones = [];
            $deliveryZones  = Delivery::getZonesByProduct($v->proId);

            foreach ($deliveryZones as $deliveryZone)
            {
                $zones[] = [
                    'zone' => $deliveryZone->zone_id,
                    'zone_name' => $deliveryZone->zone_name,
                ];
            }

            $points  = Comment::scopeCommentsRating($v->proId);
            
            $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
            $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
            $multiply = 1;

            if ($discPrice > 0) {
                $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiply;
            }else{
                $pointsJpoint = ($v->actual_price) * $Jpoint->earn_rate * $multiply;
                $pointsBpoint = ($v->actual_price) * $Bpoint->earn_rate * $multiply;
            }
            
            if ($pro->freshness_days !='' && $pro->freshness_days !=0) {
                $freshness_tag = $pro->freshness_days. ' days freshness.';
            }else{
                $freshness_tag = '';
            }

            $newArray2[] = array(
                        'label' => $v->label,
                        'label_id' => $v->label_id,
                        'priceopt' => "FS".$v->fid."[".$v->label_id."]",   
                        'price' => round($ori_price, 2),  
                        'promo_price' => $promo_price,  
                        'stock' => $v->limit_quantity,
                        'default'=> ($wt->default == 1) ? 'TRUE' : 'FALSE',
                        'p_weight' => $wt->p_weight,
                        'discount_percent' => $percent,
                        'jpoint'        => floor($pointsJpoint),
                        'bpoint'        => floor($pointsBpoint),
                    );

            $newArray['item'][] = array(
                    'sku' => $v->sku,
                    'qrcode' => $v->qrcode,
                    'name' => $v->name,
                    'description' => $v->description,
                    'delivery_time' => $v->delivery_time,
                    'img_1' => ( ! empty($v->img_1)) ? Image::link("images/data/".$v->img_1) : '',
                    'img_2' => ( ! empty($v->img_2)) ? Image::link("images/data/".$v->img_2) : '',
                    'img_3' => ( ! empty($v->img_3)) ? Image::link("images/data/".$v->img_3) : '',
                    'thumb_1' => ( ! empty($v->img_1)) ? Image::link("images/data/thumbs/".$v->img_1) : '',
                    'thumb_2' => ( ! empty($v->img_2)) ? Image::link("images/data/thumbs/".$v->img_2) : '',
                    'thumb_3' => ( ! empty($v->img_3)) ? Image::link("images/data/thumbs/".$v->img_3) : '',
                    'vid_1' => ( $v->vid_1 != "") ? $v->vid_1 : '',
                    'total_sold'=> $total_sold,
                    'zone_records'      => count($zones),
                    'delivery_zones'    => [$zones],
                    'option' => $newArray2,
                    'freshness'         => $pro->freshness, 
                    'freshness_days'    => $freshness_tag,
                    'bulk'              => empty($pro->bulk) ? '0' : $pro->bulk,
                    'halal'          => empty($pro->halal) ? '0' : $pro->halal,
                    'overall_rating' => empty($points) ? '' : $points,
                );
        }
        
        $data['xml_data']['batch'] = $newArray;

        return Response::json($data);

    }
    
    public function anyRewardbuymore() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            $username = Input::get("username");

            $CustomerInfo = Customer::where("username",$username)->first();
            
            
            if($CustomerInfo == null ){
                
                // return error
                throw new Exception ("No user record found!");
                
            }else{
                
                $user_id = $CustomerInfo->id;
                
                $RewardBMGMTracking = RewardController::createAccountBMGM($user_id);
                
                //create API data
                
              
                
                $orders = DB::table('jocom_reward_bmgm_transaction AS RBGM')
                    ->select(DB::raw('SUM(RBGM.amount) as total_amount'))
                    ->leftJoin('jocom_transaction as JT','JT.id','=','RBGM.transaction_id')
                    ->where('JT.buyer_username',$username)
                    ->where('JT.status','completed')
                    //->whereIn('JT.status','completed')
                    ->where('RBGM.tracking_id',$RewardBMGMTracking->id)
                    ->groupBy('RBGM.tracking_id')
                    ->first();
                    
               
                
                // $VoucherStage1 = '';
                // $VoucherStage2 = '';
                // $VoucherStage3 = '';
                
                if($RewardBMGMTracking->stage_1_voucher_id > 0){
                    $VoucherStage1 = DB::table('jocom_coupon')
                    ->select('jocom_coupon.*')
                    ->where("jocom_coupon.id",$RewardBMGMTracking->stage_1_voucher_id)
                    ->first();
                    
                    // echo "<pre>";
                    // print_r($VoucherStage1);
                    // echo "</pre>";
                    
                    if($VoucherStage1->status == 1 && $VoucherStage1->qty > 0 ){
                        $voucher_status_1 = 1;
                    }else{
                        $voucher_status_1 = 0;
                        $RewardBMGMTracking->stage_1_used = 1;
                    }
                    
                    $VoucherStage1 = array(
                        "coupon_code" => $VoucherStage1->coupon_code,
                        "amount" => $VoucherStage1->amount,
                        "amount_type" => $VoucherStage1->amount_type,
                        "min_purchase" => $VoucherStage1->min_purchase,
                        "valid_from" => $VoucherStage1->valid_from,
                        "valid_to" => $VoucherStage1->valid_to,
                        "status" => $voucher_status_1,
                    );
                    
                    
                }
                if($RewardBMGMTracking->stage_2_voucher_id > 0){
                    $VoucherStage2 = DB::table('jocom_coupon')
                    ->select('jocom_coupon.*')
                    ->where("jocom_coupon.id",$RewardBMGMTracking->stage_2_voucher_id)
                    ->first();
                    
                    if($VoucherStage2->status == 1 && $VoucherStage2->qty > 0 ){
                        $voucher_status_2 = 1;
                    }else{
                        $voucher_status_2 = 0;
                        $RewardBMGMTracking->stage_2_used = 1;
                    }
                    
                    $VoucherStage2 = array(
                        "coupon_code" => $VoucherStage2->coupon_code,
                        "amount" => $VoucherStage2->amount,
                        "amount_type" => $VoucherStage2->amount_type,
                        "min_purchase" => $VoucherStage2->min_purchase,
                        "valid_from" => $VoucherStage2->valid_from,
                        "valid_to" => $VoucherStage2->valid_to,
                        "status" => $voucher_status_2,
                    );
                    
                    
                }
                
                if($RewardBMGMTracking->stage_3_voucher_id > 0){
                    $VoucherStage3 = DB::table('jocom_coupon')
                    ->select('jocom_coupon.*')
                    ->where("jocom_coupon.id",$RewardBMGMTracking->stage_3_voucher_id)
                    ->first();
                    
                    if($VoucherStage3->status == 1 && $VoucherStage3->qty > 0 ){
                        $voucher_status_3 = 1;
                    }else{
                        $voucher_status_3 = 0;
                        $RewardBMGMTracking->stage_3_used = 1;
                        
                    }
                    
                    $VoucherStage3 = array(
                        "coupon_code" => $VoucherStage3->coupon_code,
                        "amount" => $VoucherStage3->amount,
                        "amount_type" => $VoucherStage3->amount_type,
                        "min_purchase" => $VoucherStage3->min_purchase,
                        "valid_from" => $VoucherStage3->valid_from,
                        "valid_to" => $VoucherStage3->valid_to,
                        "status" => $voucher_status_3,
                    );
                    
                }
                
                $firstStage = DB::table('jocom_reward_bmgm_stage')
                   ->where('code', '=', 'S1')
                   ->first();
                
                $SecondStage = DB::table('jocom_reward_bmgm_stage')
                   ->where('code', '=', 'S2')
                   ->first();
                
                $thirdStage = DB::table('jocom_reward_bmgm_stage')
                   ->where('code', '=', 'S3')
                   ->first();
                
                
                $info = array(
                    "activation" => 1,
                    "stage_1_amount" => $firstStage->amount,
                    "stage_1" => $RewardBMGMTracking->stage_1,
                    "stage_1_voucher_is_used" => (string)$RewardBMGMTracking->stage_1_used,
                    "stage_1_voucher" => $VoucherStage1,
                    "stage_2_amount" => $SecondStage->amount,
                    "stage_2" => $RewardBMGMTracking->stage_2,
                    "stage_2_voucher_is_used" => (string)$RewardBMGMTracking->stage_2_used,
                    "stage_2_voucher" => $VoucherStage2,
                    "stage_3_amount" => $thirdStage->amount,
                    "stage_3" => $RewardBMGMTracking->stage_3,
                    "stage_3_voucher_is_used" => (string)$RewardBMGMTracking->stage_3_used,
                    "stage_3_voucher" => $VoucherStage3,
                    "current_total" => $orders->total_amount > 0 ? $orders->total_amount : "0.00"
                );
                
                $data = $info ;
                
                
                
            }
            
        
        } catch (Exception $ex) {
              
            $message = $ex->getMessage();
            $RespStatus = 0;
            
        } finally {
            
            if ($is_error) {
               
            } else {
               
            }
            
        }


        /* Return Response */
        $response = array("status" => $RespStatus, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    /*
     * @Desc: API for Referrer Reward module 
     */
    
    public function anyRewardmodulestatus(){
         $data        = array();
         $rewardstatus = 0;
         $statusmsg = "Inactive";

         $rewardcode = Input::get("rewardcode");

             $RewardModule = RewardModule::where("reward_type_code",$rewardcode)->first();

             if(count($RewardModule)>0){
                $rewardstatus = $RewardModule->activation;
                if(isset($rewardstatus) && $rewardstatus == 1){
                    $statusmsg = "Active";
                }
                
             }

             $temp = array('rewardstatus' => $rewardstatus,
                            'status_msg'  => $statusmsg
                             );

             $data['xml_data'] = $temp;

         return Response::json($data);

    }
    
    public function anyRewardreferrer(){
        $data        = array();
        $userid = 0;
        $username = '';
        $refercode = '';
        $referrerurl = '';

        $username = Input::get("username");


        $userresult = DB::table('jocom_user')
                        ->where('username',$username)
                        ->first();


        if(count($userresult)>0){
            $userid =  $userresult->id; 
            $username = $userresult->username; 
            $refercode = $userresult->referrer_code; 
            $referrerurl = 'http://reward.jocom.my/?'.$refercode;
            
        }


        $datanew = array('user_id' => $userid, 
                      'username' => $username, 
                      'referrercode' => $refercode,   
                      'referrerurl' => $referrerurl  

                     );
        // $data = array_merge($data, $datanew);

         $data['xml_data'] = $datanew;

         /* Return Response */

        return Response::json($data);


    }
    
    public function anyReferrersettings(){
        $data        = array();
        $points = 0;
        $rtype = '';
        $status = 1;
        
        $RewardSettings= RewardRFRRSetting::where("activation",$status)->first();

        if(count($RewardSettings)>0){
            $rtype =  'JPoint'; 
            $points = (int)$RewardSettings->point; 
            $description = $RewardSettings->description; 
        }


        $datanew = array('rtype' => $rtype, 
                      'points' => $points,
                      'description' => $description

                     );

        $data['xml_data'] = $datanew;

         /* Return Response */

        return Response::json($data);

    }
    
    public function anyReferrergenerate()
    {
        $data        = array();
        $get         = array();

        $data = self::createReferrer(Input::all());
        
        return Response::json($data);
    }


    public static function createReferrer($input=array()){

        $data = array();

        $refuserid = 0;
        $newuserid = 0;
        $rndcode = '';
        $isUpdate = 0;
        $status_msg = '';

        // print_r(Input::all());

        // die();

        $refercode = Input::get('referrercode');
        $new_user  = Input::get('username');
        
        $rndcode = RewardRFRRSetting::generateCode();

        
        $rndcoderesult = DB::table('jocom_user')
                        ->where('referrer_code',$rndcode)
                        ->first();

        if(count($rndcoderesult)>0){
            $rndcode = RewardRFRRSetting::generateCode();

            $l2_rndcoderesult = DB::table('jocom_user')
                                ->where('referrer_code',$rndcode)
                                ->first();
             if(count($l2_rndcoderesult)>0){
                $rndcode = RewardRFRRSetting::generateCode();

             }
        }


        $refuserresult = Customer::where('referrer_code','=',$refercode)
                        ->first();

        if(count($refuserresult)>0){

            $refuserid = $refuserresult->id; 

            $newuserresult = DB::table('jocom_user')
                        ->where('username','=',$new_user)
                        ->whereNull('referrer_code')
                        ->first();


            if(count($newuserresult)>0){
                $newuserid = $newuserresult->id; 

                if($refuserid == $newuserid){

                    $status_msg = 'Error: Invalid Referrer';

                }
                else{

                    $uniqueresult = DB::table('jocom_reward_referrer_tracking')
                                    ->where('referrer_from','=',$refuserid)
                                    ->where('referrer_to','=',$newuserid)
                                    ->first();
                                    
                    if(count($uniqueresult) == 0){

                        $uniqueresult_sub = DB::table('jocom_reward_referrer_tracking')
                                    ->where('referrer_from','=',$newuserid)
                                    ->where('referrer_to','=',$refuserid)
                                    ->first();

                        if(count($uniqueresult_sub) == 0){

                                $userupdate = Customer::find($newuserid);
                                $userupdate->referrer_code = $rndcode;
                                $userupdate->save();

                                $api                = new RewardRFRRTracking;
                                $api->referrer_from = $refuserid;
                                $api->referrer_to   = $newuserid;
                                $api->created_by     = 'API_UPDATE';
                                $api->save();

                                $isUpdate = 1;

                                $status_msg = 'Sucessfully Updated';

                        }
                        else{
                            $status_msg = 'Error: Invalid Referrer';  

                        }

                    }
                    else{
                        
                        $status_msg = 'Error: Invalid Referrer';  

                    }

                }

            }
            else{
                $status_msg = 'Error: Permission denied';

            }

        }

        if($isUpdate == 1){
            
            $data  = array('status_ins'     => '1', 
                           'status_msg'     => $status_msg,

                );
        } 
        else 
        {
            $data  = array('status_ins'     => '0', 
                           'status_msg'     => $status_msg, 
                );  
        }
        return array('xml_data' => $data);

    }
    
    public function anyShare(){
        
        $qrcode = Input::get('qrcode');
        $productInfo = DB::table('jocom_products AS JP')
                        ->where('JP.status',1)
                        ->where('JP.qrcode',$qrcode)
                        ->select('JP.qrcode','JP.img_1','JP.name','JP.description')
                        ->first();
        
        $file_name =  Image::link("images/data/$productInfo->img_1") ;
        
        return array(
            "name" => $productInfo->name,
            "description" => $productInfo->description,
            "image" => Image::link($file_name)
            );
         
    }
    
    /* 
    * Birthday API
    */
    public function anyBirthday(){

        $date = Input::get('date');
        $username = Input::get('username');

        if (isset($date)) {
           $user = DB::table('jocom_user')->where('dob','!=', '')->where('dob','=', $date)->where('active_status',1)->get();
        
            $arr = array();

            foreach ($user as $key => $value) {
            
                $today = new DateTime($date);
                $current = $today->format('m-d');

                $date2 = new DateTime($value->dob);
                $user_dob = $date2->format('m-d');

                if ($current == $user_dob) {

                    $arr[] = array(
                        "username" => $value->username,
                        "wishes" => "Happy Birthday ". $value->full_name . ". Have an amazing birthday! Enjoy your special day.",
                    );
                }
               
            }
        }

        if (isset($username)) {
            $user = DB::table('jocom_user')->where('username',$username)->where('dob','!=', '')->where('active_status','1')->first();
        
            $current = date('m-d');
            $date2 = new DateTime($user->dob);
            $user_dob = $date2->format('m-d');
            //echo $user_dob; echo $current;

            $arr = array();
            if($user->dob == '0000-00-00'){
                 $data["birthdays"] = $arr;
            }else{
                
                
                if ($current == $user_dob) {
                    $arr[] = array(
                        // "current" => $current,
                        // "user_dob" => $user->dob,
                        "username" => $user->username,
                        "wishes" => "Happy Birthday ". $user->full_name . ". Have an amazing birthday! Enjoy your special day.",
                    );
                    $data["birthdays"] = $arr;
                }else{
                    $data["birthdays"] = $arr;
                }
                
            }
    
            

        }
        
        $data["birthdays"] = $arr;

        return $data;
    }
    
    public function anyPosts($pageRequest = 1){
        
        $limit = 5;
        $offset = 0;
        $page = $pageRequest - 1;
        $dataCollection = array();
        $max_length = 500;
        
        try{
            
            $totalPage = ceil(DB::table('jocom_blog_posts')->where('jocom_blog_posts.status',1)
                ->where('jocom_blog_posts.activation',1)->count() / $limit );
            $currentPage = $page + 1;
            $nextPage = $currentPage + 1;
            if($nextPage  > (int)$totalPage) 
            {   
                $nextPage =  0; 
            } else { 
                $nextPage = $currentPage + 1;
            }
            
            $previousPage = $currentPage - 1;
            
            $post = DB::table('jocom_blog_posts')
                ->select(array(
                    'jocom_blog_posts.id',
                    'jocom_blog_posts.title',
                    'jocom_blog_posts.category_id',
                    'jocom_blog_posts.published_date',
                    'jocom_blog_posts.is_pinned_post',
                    'jocom_blog_category.category',
                    'jocom_blog_posts.author',
                    'jocom_blog_posts.created_at',
                    'jocom_blog_posts.created_by',
                    'jocom_blog_posts.status',
                    'jocom_blog_posts.is_publish',
                    'jocom_blog_posts.main_image_id',
                    ))
                ->leftJoin('jocom_blog_category', 'jocom_blog_category.id', '=', 'jocom_blog_posts.category_id')
                ->where('jocom_blog_posts.status',1)
                ->where('jocom_blog_posts.activation',1)
                ->orderBy('jocom_blog_posts.published_date', 'desc')
                ->offset($page*$limit)->take($limit)->get();
               
            
            foreach ($post as $key => $value){
                $date=date_create($value->published_date);
               // echo date_format($date,"Y/m/d H:i:s");
                $subLine = array(
                    "id" => $value->id,
                    "title" => $value->title,
                    "category" => $value->category,
                    "category_id" => $value->category_id,
                    "is_publish" => $value->is_publish,
                    "published_date" => date_format($date,"F d, Y") , //Sept 16th, 2012
                    "status" => $value->status,
                    "author" => $value->author,
                    "is_pinned_post" => $value->is_pinned_post,
                    "created_at" => $value->created_at,
                    "created_by" => $value->created_by,
                    "main_image_id" => $value->main_image_id,
                    );
                
                $tags = DB::table('jocom_blog_tag')
                    ->where("post_id",$value->id)
                    ->get();  
                    
                $file_path = Config::get('constants.HTML_CONTENT_BLOG_PATH');
                
                $htmlFile = fopen($file_path."/".$value->id.".txt", "r") or die("Unable to open file!");
                $htmlString =  fread($htmlFile,filesize($file_path."/".$value->id.".txt"));
                fclose($htmlFile);
                
                $htmlString = strip_tags($htmlString, '<p>');
                
                if (strlen($htmlString) > $max_length)
                {
                    $offset = ($max_length - 3) - strlen($htmlString);
                    $htmlString = substr($htmlString, 0, strrpos($htmlString, ' ', $offset)) . '...';
                }
                        
                $subLine["tags"] = $tags;
                $subLine["content"] = $htmlString;
                    
                array_push($dataCollection,$subLine);
                
            }
            
            
        } catch (Exception $ex) {
            
            
        } finally {
          
            return array(
                "totalPages" => $totalPage,
                "currentPageNumber" => $currentPage,
                "nextPage" => $nextPage,
                "previousPage" => $previousPage,
                "totalPageDisplay" =>2,
                "data" => $dataCollection
            );
            
            
        }
        
        
    }
    
    public function anyPost($post_id = 1){
        
        $limit = 1;
        $dataCollection = array();
        
        try{
      
            $post = DB::table('jocom_blog_posts')
                ->select(array(
                    'jocom_blog_posts.id',
                    'jocom_blog_posts.total_viewed',
                    'jocom_blog_posts.title',
                    'jocom_blog_posts.category_id',
                    'jocom_blog_posts.published_date',
                    'jocom_blog_posts.is_pinned_post',
                    'jocom_blog_category.category',
                    'jocom_blog_posts.author',
                    'jocom_blog_posts.created_at',
                    'jocom_blog_posts.created_by',
                    'jocom_blog_posts.status',
                    'jocom_blog_posts.is_publish',
                    'jocom_blog_posts.main_image_id',
                    ))
                ->leftJoin('jocom_blog_category', 'jocom_blog_category.id', '=', 'jocom_blog_posts.category_id')
                ->where('jocom_blog_posts.status',1)
                ->where("jocom_blog_posts.id",$post_id)->first();
                
            $postViewed = BlogPosts::where('id', $post->id)->update(['total_viewed' => $post->total_viewed + 1]);
          
        
            $date=date_create($post->published_date);
           // echo date_format($date,"Y/m/d H:i:s");
            $subLine = array(
                "id" => $post->id,
                "title" => $post->title,
                "category" => $post->category,
                "category_id" => $post->category_id,
                "is_publish" => $value->is_publish,
                "published_date" => date_format($date,"F d, Y") , //Sept 16th, 2012
                "status" => $post->status,
                "author" => $post->author,
                "is_pinned_post" => $post->is_pinned_post,
                "created_at" => $post->created_at,
                "created_by" => $post->created_by,
                "main_image_id" => $post->main_image_id,
                );
            
            $tags = DB::table('jocom_blog_tag')
                ->where("post_id",$post->id)
                ->get();  
                
            $file_path = Config::get('constants.HTML_CONTENT_BLOG_PATH');
            
            $htmlFile = fopen($file_path."/".$post->id.".txt", "r") or die("Unable to open file!");
            $htmlString =  fread($htmlFile,filesize($file_path."/".$post->id.".txt"));
            fclose($htmlFile);
                    
            $subLine["tags"] = $tags;
            $subLine["content"] = $htmlString;
                
            array_push($dataCollection,$subLine);
                
           
            
            
        } catch (Exception $ex) {
            
            
        } finally {
          
            return array(
                "data" => $dataCollection
            );
            
            
        }
        
        
    }
    
    
    public function anyPopularpost(){
        
        $limit = 1;
        $dataCollection = array();
        
        try{
      
            $post = DB::table('jocom_blog_posts')
                ->select(array(
                    'jocom_blog_posts.id',
                    'jocom_blog_posts.total_viewed',
                    'jocom_blog_posts.title',
                    'jocom_blog_posts.category_id',
                    'jocom_blog_posts.published_date',
                    'jocom_blog_posts.is_pinned_post',
                    'jocom_blog_category.category',
                    'jocom_blog_posts.author',
                    'jocom_blog_posts.created_at',
                    'jocom_blog_posts.created_by',
                    'jocom_blog_posts.status',
                    'jocom_blog_posts.is_publish',
                    ))
                ->leftJoin('jocom_blog_category', 'jocom_blog_category.id', '=', 'jocom_blog_posts.category_id')
                ->where('jocom_blog_posts.status',1)
                ->where('jocom_blog_posts.activation',1)
                ->orderBy("jocom_blog_posts.total_viewed","DESC")->take(3)->get();
          
        
            foreach ($post as $key => $value){
                $date=date_create($value->published_date);
               // echo date_format($date,"Y/m/d H:i:s");
                $subLine = array(
                    "id" => $value->id,
                    "title" => $value->title,
                    "month" => date_format($date,"F") ,
                    "day" => date_format($date,"d") ,
                    "category" => $value->category,
                    "category_id" => $value->category_id,
                    "is_publish" => $value->is_publish,
                    "published_date" => date_format($date,"F d, Y") , //Sept 16th, 2012
                    "status" => $value->status,
                    "author" => $value->author,
                    "is_pinned_post" => $value->is_pinned_post,
                    "created_at" => $value->created_at,
                    "created_by" => $value->created_by,
                    "total_viewed" => $value->total_viewed,
                    );
                
                $tags = DB::table('jocom_blog_tag')
                    ->where("post_id",$value->id)
                    ->get();  
                    
                $subLine["tags"] = $tags;
                    
                array_push($dataCollection,$subLine);
                
            }
              
        } catch (Exception $ex) {
            
            
        } finally {
          
            return array(
                "data" => $dataCollection
            );
            
            
        }
        
        
    }
    
    /*
     * App Tutorial API
     */
    
    public function anyAppguide(){
        
        
        $wt = DB::table('jocom_app_guide')->orderBy('sequence', 'ASC')->get();
        $appGuideData = array();
        
        foreach ($wt as $key => $value) {
            
            $subLine = array(
                "intro_title" => $value->title,
                "intro_desc" => $value->description,
                "intro_img" => Image::link('/images/tutorial/'. $value->image)
            );
            
            array_push($appGuideData, $subLine);
        }
       
        $data['xml_data']['record'] = DB::table('jocom_app_guide')->count();
        $data['xml_data']['total_record'] = DB::table('jocom_app_guide')->count();
        $data['xml_data']['item'] = $appGuideData;
        
        return Response::json($data);
        
    }
    
    
    public function anyRunautomatefims(){

        $isError = false;
        $message = 'Success';
        
        try{
            
            DB::beginTransaction();

            $AccountController = new AccountController();
            $AccountController->automateInvoice();

        }catch(exception $ex){
            $isError = true;
            $message = 'Error : '.$ex->getMessage()." ".$ex->getLine();
           
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            return array(
                "is_error" => $isError,
                "message" => $message
            );
        }
    }
    
    public function anyVisitor(){
        
            $currentdate = date('Y-m-d');
            $vistormaster = DB::table('jocom_visitor_details AS VD')
                             ->where('VD.visitor_datetime', 'LIKE', '%'.$currentdate.'%')
                             ->where('VD.status', '=', 1)
                             ->select('VD.name','VD.ic','VD.visitor_datetime','VD.visitor_purpose')
                             ->orderBy('VD.id','asc')
                             ->get();

             $vistor = array();  

             foreach ($vistormaster as $key => $value) {

                    array_push($vistor, array(
                        'vistor_name' => $value->name,
                        'vistor_ic' => $value->ic,
                        'vistor_datetime' => $value->visitor_datetime,
                        'visitor_purpose' => $value->visitor_purpose,
                        ));
                }

         return $vistor;       

    }
    
    /**
     * Logistics Truck Driver Values
     * @param int driver_id
     * @param start_date
     * @param end_date
     * 
     * @return mixed
     * 
    **/
    public function logisticsTruckDriverValues() {
        $driverId = Input::get('driver_id');
        $assignedStartDate = Input::get('start_date');
        $assignedEndDate = Input::get('end_date');
        
        if(!empty($driverId) && !empty($assignedStartDate) && !empty($assignedEndDate)){
            $data = [];
            $startDate = $assignedStartDate." 00:00:00";
            $endDate = $assignedEndDate." 23:59:59";
            
            $truckJourneyDetails = LogisticBatch::select(
                                            'logistic_batch.driver_id as id',
                                            'logistic_driver.name as driver_name',
                                            'logistic_batch.assign_date'
                                            // DB::raw('MAX(jocom_trackingsignal.created_date)  as latestdate'),
                                            // DB::raw('MIN(jocom_trackingsignal.created_date)  as startjourney')
                                        )
                                        ->leftjoin('logistic_driver','logistic_driver.id','=','logistic_batch.driver_id')
                                        // ->leftjoin('jocom_trackingsignal','jocom_trackingsignal.driverid','=','logistic_driver.id')
                                        ->where('logistic_batch.driver_id', $driverId)
                                        ->where('logistic_batch.assign_date', '>=', $startDate)
                                        ->where('logistic_batch.assign_date', '<=', $endDate)
                                        // ->groupBy('logistic_batch.driver_id')
                                        ->first();
            
            $data['total_assigned'] = (string) DB::table('logistic_batch')->where('accept_date', '>=', $startDate)->where('accept_date', '<=', $endDate)->where('driver_id', $driverId)->count();
            $data['total_completed'] = (string) DB::table('logistic_batch')->where('accept_date', '>=', $startDate)->where('accept_date', '<=', $endDate)->where('status', '=', 4)->where('driver_id', $driverId)->count();
            $total_price = 0;
            
            $batches = DB::table('logistic_batch')->where('accept_date', '>=', $startDate)->where('accept_date', '<=', $endDate)->where('driver_id', $driverId)->get();
            
            foreach($batches as $key => $batch) {
                $logistics = DB::table('logistic_transaction')->select('transaction_id')->where('id','=', $batch->logistic_id)->get();
                foreach($logistics as $logistic) {
                    $data['transactions'][$key]['total_price'] = DB::table('jocom_transaction_details')->select(DB::raw('SUM(unit*price) as unit_price_sum'))->where('transaction_id', '=', $logistic->transaction_id)->first();
                    $data['transactions'][$key]['details'] = DB::table('jocom_transaction_details')->select('transaction_id', 'product_id', 'unit', 'price')->where('transaction_id', $logistic->transaction_id)->get();
                }
            }
            
            $totalPrice = [];
            if(!empty($data['transactions'])) {
                foreach($data['transactions'] as $k => $value) {
                    $totalPrice[$k] = $value['total_price']->unit_price_sum;
                }
            }
            $data['total_transactions_price'] = (string) array_sum($totalPrice);
            
            
            return Response::json([
                'truck_journey_details' => $truckJourneyDetails,
                'logistics_trancaction_details' => $data
                
            ]);
        
        } else {
            return Response::json(['message' => 'Invalid details']); 
        }
    }
    
    /// Get Ecom Banner API
    public function anyGetbanner() {
        $banners = DB::table('jocom_ecom_banners')
                    ->select('file_name', 'product_id')
                    ->get();
        return $banners;
    }
    
    public function updatePriceCost() {
        $today = Carbon\Carbon::now()->startOfDay();

        $schedule_price = ProductPriceSchedule::where('date', '=', $today)->get();

        $isError = false;

        try{
            
            DB::beginTransaction();

            foreach ($schedule_price as $schedule) {
                $price = Price::find($schedule->product_price_id);
                if ($schedule->price != null) {
                    $price->price = $schedule->price;
                    $price->save();
                }
                if ($schedule->price_promo != null) {
                    $price->price_promo = $schedule->price_promo;
                    $price->save();
                }

                if ($schedule->cost != null) {
                    $cost = ProductPriceSeller::where('product_price_id', '=', $schedule->product_price_id)->first();
                    $cost->cost_price = $schedule->cost;
                    $cost->save();
                }
                
                $schedule->completed = 1;
                $schedule->save();
            }

        }catch(exception $ex){
            $isError = true;
            $message = 'Error : '.$ex->getMessage();
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            return array(
                "is_error" => $isError,
                "message" => $message
            );
        }
    }
    
    public function generateDriverTimeSheet() {

        $drivers = DB::table('logistic_driver')
                    ->where('status', '=', 1)
                    ->where('is_logistic_dashboard', '=', 1)
                    ->get();

        $driver_ids = array();
        $driver_list = array();
        foreach ($drivers as $driver) {
            array_push($driver_ids, $driver->id);
            $driver_list[$driver->id][0] = 0;
            $driver_list[$driver->id][1] = 0;
            $driver_list[$driver->id][2] = $driver->name;
        }
        $driver_ids_string = implode(',', $driver_ids);
        $delivery_list = DB::select('select b.driver_id, t.delivery_addr_1, t.delivery_addr_2 from (
                                        select a.logistic_id,a.driver_id from logistic_batch as a
                                        where a.driver_id in ('.$driver_ids_string.') and (a.status=0 or a.status=1)) as b
                                    join logistic_transaction as t on b.logistic_id=t.id
                                    where t.status =0 or t.status = 4
                                    order by b.driver_id, t.delivery_addr_1');

        $previous_addr_1 = '';

        foreach ($driver_list as $driver_id => $count) {
            foreach ($delivery_list as $do) {
                if ($do->driver_id == $driver_id) {
                    $driver_list[$driver_id][0]++; 
                }
                if ($do->driver_id == $driver_id) {
                    if ($previous_addr_1 != strtoupper(substr($do->delivery_addr_1, 0, 22))) {
                        $driver_list[$driver_id][1]++;
                        $previous_addr_1 = strtoupper(substr($do->delivery_addr_1, 0, 22));
                    }
                }
                
            }
            $previous_addr_1 = '';
        }

        $result = array();
        foreach ($driver_list as $driver) {
            array_push($result, ['driver_name' => $driver[2], 'do_qty' => $driver[0], 'roads' => $driver[1]]);
        }

        $json_encoded = json_encode($result);

        DB::table('logistic_driver_time_sheet')->insert(['data' => $json_encoded]);

        $files = glob(Config::get('constants.DRIVER_TIME_SHEET') . '/*');
        foreach($files as $file){
            if(is_file($file))
                unlink($file);
        }
        
        return 1;
    }
    
    public function postCreatecontestant() {
        $contest = Input::get('contest');
        $name = Input::get('name');
        $email = Input::get('email');
        $contact = Input::get('contact');
        $survey1_answer = Input::get('survey1_answer');
        $survey1_why = Input::get('survey1_why');
        $survey2_answer = Input::get('survey2_answer');
        $survey2_why = Input::get('survey2_why');
        $transaction_id = Input::get('transaction_id');

        $unique = time();
        $image = Input::file('invoice_img');

        $imgFilename = $contest . "-" . $unique . '.' . $image->getClientOriginalExtension();
        $image->move('./images/contestant/', $imgFilename);

        $result = DB::table('jocom_contestant')->insert(
        [
            'contest' => $contest,
            'name' => $name,
            'email' => $email,
            'contact' => $contact,
            'invoice_img' => $imgFilename,
            'survey1_answer' => $survey1_answer,
            'survey1_why' => $survey1_why,
            'survey2_answer' => $survey2_answer,
            'survey2_why' => $survey2_why,
            'transaction_id' => $transaction_id
        ]);

        return Redirect::to('https://www.jocom.my/');
    }

    public function getDuplicatetransaction($transaction_id) {
        $duplicate = DB::table('jocom_contestant')->where('transaction_id', '=', $transaction_id)->count();
        
        return $duplicate;
    }

    public function getAPIFlashsaleProduct(){
        
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        $now = date_format(date_create(), 'Y-m-d H:i:s');
        
        try{
            // Start transaction
            DB::beginTransaction();
            
            $campaign_id = 1;
            $Product = CampaignProducts::getAPICampaignProduct($campaign_id);

            $Product = DB::table('jocom_flashsale_products AS JFP')
                    ->select('JFP.*','JFP.id as fid','JFP.qty as sold','JF.valid_to',
                            'JP.id AS ProductID','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                            'JPP.*')
                    ->join('jocom_flashsale as JF', 'JFP.fid', '=', 'JF.id')
                    ->join('jocom_products AS JP', 'JP.id', '=', 'JFP.product_id')
                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->where('JF.valid_from', '<=', $now)
                    ->where('JF.valid_to', '>=', $now)
                    ->where('JF.status', 1)
                    ->where('JFP.activation', 1)
                    ->where('JP.status', 1)
                    ->where('JPP.default', 1)
                    ->orderBy('JFP.seq')
                    ->get();
            
            if(count($Product) > 0){
                
                $ProductCollection = array();
                $PriceOptionCollection = array();
                
                foreach ($Product as $key => $value) {
                    
                    $delivery_zones = array();
                    $PriceOptionCollection = array();
                    
                    $DeliveryOption = Delivery::getZonesByProduct($value->ProductID);
     
                    foreach ($DeliveryOption as $keyZone => $valueZone) {
                        $line_delivery_zones = array(
                            "zone_id" => $valueZone->zone_id,
                            "zone_price" => number_format( $valueZone->price, 2, '.', ''),
                            "zone_name" => $valueZone->zone_name,
                        );
                        
                        array_push($delivery_zones, $line_delivery_zones);
                    }
                        
                    
                    array_push($PriceOptionCollection, [
                        "id" => $value->label_id,
                        "label" => $value->label, //number_format( $valueZone->price, 2, '.', ''),
                        "price" => number_format($value->actual_price, 2, '.', ''),
                        "promo_price" => number_format($value->promo_price, 2, '.', ''),
                    ]);
                    
                    $lineProduct = array(
                        
                        "ProductID" => $value->ProductID,
                        "ProductName" => $value->name,
                        "ProductSKU" => $value->sku,
//                        "ProductActualPrice" => number_format( $value->price, 2, '.', ''),
//                        "ProductPromoPrice" => number_format( $value->price_promo, 2, '.', ''),
//                        "ProductActualPriceFinal" => number_format($final_price, 2, '.', ''),
//                        "ProductPromoPriceFinal" =>number_format( $final_price_promo, 2, '.', ''),
                        "ProductDescription" => $value->ProductDescription,
                        "delivery_time" => $value->delivery_time,
                        "delivery_zone" => $delivery_zones,
                        "ProductIMG1" => ( ! empty($value->img_1)) ? Image::link("images/data/{$value->img_1}") : '',
                        "ProductIMG2" => ( ! empty($value->img_2)) ? Image::link("images/data/{$value->img_2}") : '',
                        "ProductIMG3" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductThumb1" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductThumb2" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductThumb3" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductQRCODE" => $value->qrcode,
                        "ProductWeight" => $value->weight,
                        "isGST" => $value->gst,
                        "ProductLabel" => $value->label,
                        "PriceOption" => $PriceOptionCollection,
                        "freshness" => $value->freshness,
                        "bulk" => "",
                        "halal" => empty($value->halal) ? '0' : $value->halal,
                        "fid" => $value->fid,
                        "sold" => $value->sold,
                        "limit_quantity" => $value->limit_quantity,
                        "valid_to" => $value->valid_to
                    );
                    
                    array_push($ProductCollection, $lineProduct);
                    
                }
                
            }else{
                throw new Exception('Failed: Product not found', 10);
            }
            
            $data['dataProduct'] = $ProductCollection;
            
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);

        return json_encode($response);
        
    }

    public function getAPIFlashsaleProductInfo(){
    
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            // Start transaction
            DB::beginTransaction();

            if (Input::has('fid')) {
                $fid = Input::get('fid');

                $Product = DB::table('jocom_flashsale_products as JFP')
                        ->select('JFP.*', 'JP.id AS ProductID','JFP.qty as sold','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                                'JPP.*')
                        ->join('jocom_products AS JP', 'JP.id', '=', 'JFP.product_id')
                        ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                        ->where('JFP.id', '=', $fid)
                        ->where('JFP.activation', 1)
                        ->where('JP.status', 1)
                        ->first();
            } else {
                $product_id = Input::get('product_id');

                $Product = DB::table('jocom_flashsale_products as JFP')
                        ->select('JFP.*', 'JP.id AS ProductID','JFP.qty as sold','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                                'JPP.*')
                        ->join('jocom_products AS JP', 'JP.id', '=', 'JFP.product_id')
                        ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                        ->where('JFP.product_id', '=', $product_id)
                        ->where('JFP.activation', 1)
                        ->where('JP.status', 1)
                        ->first();
            }
            

            if(count($Product) > 0){
                
                $ProductCollection = array(); 
                $PriceOptionCollection = array();
                
                    
                    $delivery_zones = array();
                    $PriceOptionCollection = array();
                    
                    $DeliveryOption = Delivery::getZonesByProduct($Product->ProductID);
     
                    foreach ($DeliveryOption as $keyZone => $valueZone) {
                        $line_delivery_zones = array(
                            "zone_id" => $valueZone->zone_id,
                            "zone_price" => number_format( $valueZone->price, 2, '.', ''),
                            "zone_name" => $valueZone->zone_name,
                        );
                        
                        array_push($delivery_zones, $line_delivery_zones);
                    }
                    
                    array_push($PriceOptionCollection, [
                        "id" => $Product->label_id,
                        "label" => $Product->label, //number_format( $valueZone->price, 2, '.', ''),
                        "price" => number_format($Product->actual_price, 2, '.', ''),
                        "promo_price" => number_format($Product->promo_price, 2, '.', ''),
                    ]);
                  
                    
                    $ProductCollection = array(
                        
                        "ProductID" => $Product->ProductID,
                        "ProductName" => $Product->name,
                        "ProductSKU" => $Product->sku,
                        "ProductDescription" => $Product->ProductDescription,
                        "delivery_time" => $Product->delivery_time,
                        "delivery_zone" => $delivery_zones,
                        "ProductIMG1" => ( ! empty($Product->img_1)) ? Image::link("images/data/{$Product->img_1}") : '',
                        "ProductIMG2" => ( ! empty($Product->img_2)) ? Image::link("images/data/{$Product->img_2}") : '',
                        "ProductIMG3" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb1" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb2" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb3" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductQRCODE" => $Product->qrcode,
                        "ProductWeight" => $Product->weight,
                        "isGST" => $Product->gst,
                        "ProductLabel" => $Product->label,
                        "PriceOption" => $PriceOptionCollection,
                        "freshness" => $Product->freshness,
                        "bulk" => "",
                        "halal" => empty($Product->halal) ? '0' : $Product->halal,
                        "sold" => $Product->sold,
                        "limit_quantity" => $Product->limit_quantity
                        
                    );
                    
                    
                
            }else{
                throw new Exception('Failed: Product not found', 10);
            }
            
            $data['dataProduct'] = $ProductCollection;
            
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        
        return json_encode($response);
        
    }
    
    public function getAPIProductInfo(){
    
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            // Start transaction
            DB::beginTransaction();
            
            $campaign_id = 1;
            $productID = Input::get('product_id');
            $Products = DB::table('jocom_products AS JP')
                    ->select('JP.id AS ProductID','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                            'JPP.*')
                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->where('JP.id', '=', $productID)
                    ->where('JP.status', 1)
                    // ->where('JPP.default', 1)
                    ->get();

            if(count($Products) > 0){
                foreach ($Products as $Product){
                $ProductCollection = array(); 
                $PriceOptionCollection = array();
                
                    
                    $delivery_zones = array();
                    $PriceOptionCollection = array();
                    
                    $DeliveryOption = Delivery::getZonesByProduct($Product->ProductID);

                    foreach ($DeliveryOption as $keyZone => $valueZone) {
                        $line_delivery_zones = array(
                            "zone_id" => $valueZone->zone_id,
                            "zone_price" => number_format( $valueZone->price, 2, '.', ''),
                            "zone_name" => $valueZone->zone_name,
                        );
                        
                        array_push($delivery_zones, $line_delivery_zones);
                    }
                    
                    
                    $PriceOption = Price::getActivePrices($Product->ProductID);
                    foreach ($PriceOption as $keyPrice => $valuePrice) {
                        
                        if($Product->gst == 2){ // 0=Exempted, 1=ZeroRated, 2=Taxable //Inclusive Tax : Exclusive Amount * 106 / 100
                        $final_price = $valuePrice->price * ((100 + $Product->gst_value) / 100);
                        $final_price_promo = $valuePrice->price_promo * ((100 + $Product->gst_value) / 100);
                        
                        }else{
                            $final_price = $valuePrice->price ;
                            $final_price_promo = $valuePrice->price_promo ;
                        }
                        
                        $line_price_option = array(
                            "id" => $valuePrice->id,
                            "label" => $valuePrice->label, //number_format( $valueZone->price, 2, '.', ''),
                            "price" => number_format($final_price, 2, '.', ''),
                            "promo_price" => number_format($final_price_promo, 2, '.', ''),
                            "qty" => $valuePrice->qty,
                            "stock" => $valuePrice->stock,
                            "stock_unit" => $valuePrice->stock_unit,
                            "default" => $valuePrice->default,
                            "p_weight" => $valuePrice->p_weight,
                            "p_weight_unit" => "g",
                        );
                        
                        array_push($PriceOptionCollection, $line_price_option);
                    }
                  
                    
                    $ProductCollection = array(
                        
                        "ProductID" => $Product->ProductID,
                        "ProductName" => $Product->name,
                        "ProductSKU" => $Product->sku,
                        "ProductDescription" => $Product->ProductDescription,
                        "delivery_time" => $Product->delivery_time,
                        "delivery_zone" => $delivery_zones,
                        "ProductIMG1" => ( ! empty($Product->img_1)) ? Image::link("images/data/{$Product->img_1}") : '',
                        "ProductIMG2" => ( ! empty($Product->img_2)) ? Image::link("images/data/{$Product->img_2}") : '',
                        "ProductIMG3" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb1" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb2" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb3" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductQRCODE" => $Product->qrcode,
                        "ProductWeight" => $Product->weight,
                        "isGST" => $Product->gst,
                        "ProductLabel" => $Product->label,
                        "PriceOption" => $PriceOptionCollection,
                        "freshness" => $Product->freshness,
                        "bulk" => "",
                        "halal" => empty($Product->halal) ? '0' : $Product->halal
                        
                    );
                    
                    }
                
            }else{
                throw new Exception('Failed: Product not found', 10);
            }
            
            $data['dataProduct'] = $ProductCollection;
            
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        return json_encode($response);
        
    }
    
   /**
     * Comment: Get the category and with it subcategory
     *
     * @api TRUE
     * @author YEE HAO
     * @since 16 APR 2021
     * @param method
     * @version 1.1
     * @method GET, POST, any request method
     * @return JSON | XML
     * @used-by revamp.jocom.com.my home page
     * @used-by revamp.jocom.com.my Sales Banner/Category/Sub Category/Camping page
     *
     * Last Update: 16 APR 2021
     */
    public function anyCatfirsttier()
    {
        $method = Input::get('method', 'json');
        $category_id = Input::get('cat_id', 588);
        $this_cat_info = Input::get('get_this', false);
        $parent_cat_info = Input::get('get_parent', false);

        $query = Category::
                where('permission', '=', 0)
                ->where('status', '=', 1)
                ->where('id', '>', 0);

        $temp_query = clone $query;
        $parent_cat_ids = $temp_query
                        ->where('category_parent', '=', $category_id)
                        ->lists('id');

        $temp_query = clone $query;
        array_push($parent_cat_ids, $category_id);
        $categories = $temp_query
                    ->whereIn('category_parent', $parent_cat_ids)
                    ->orderBy('weight', 'desc')
                    ->orderBy('category_name', 'asc')
                    ->get()->toArray();

        if($this_cat_info){
            $this_cat = Category::
                    where('id', '=', $category_id)
                    ->first()->toArray();
            $data = $this_cat;
            $data['cat_id'] = $category_id;
            $data['item'] = [];
        }else{
            $data = [
                'cat_id' => $category_id,
                'item' => []
            ];
        }

        if($parent_cat_info){
            if($this_cat_info){
                $parent_cat = Category::where('id', '=', $this_cat['category_parent'])->first()->toArray();
            }else{
                // retrive this cat info after that retrive the parent Cat
                $this_cat = Category::where('id', '=', $category_id)->first()->toArray();
                $parent_cat = Category::where('id', '=', $this_cat['category_parent'])->first()->toArray();
            }
            $data['parent'] = $parent_cat;
        }

        foreach ($categories as $value) {
            $categoryImage = ( ! empty($value['category_img'])) ? Image::link("images/category/{$value->category_img}") : '';
            $categoryImagebanner = ( ! empty($value['category_img_banner'])) ? Image::link("images/category/{$value->category_img_banner}") : '';
            $value_data = [
                'id'         => $value['id'],
                'cat_name'   => $value['category_name'],
                'cat_icon'   => $categoryImage,
                'cat_banner' => $categoryImagebanner,
                'sub_cat'    => (count($childCategories)) ? 1 : '',
                'charity'    => $value['charity'],
            ];


            if(in_array($value['id'], $parent_cat_ids)){
                if(isset($data['item'][$value['id']])){
                    $temp_child = $data['item'][$value['id']]['children'];
                    $data['item'][$value['id']] = $value_data;
                    $data['item'][$value['id']]['children'] = $temp_child;
                }else{
                    $data['item'][$value['id']] = $value_data;
                }
            }else{
                if(!isset($data['item'][$value['category_parent']])){
                    $data['item'][$value['category_parent']] = ['children' => []];
                }
                if($method === 'xml'){
                    $data['item'][$value['category_parent']]['children'][] = $value_data;
                }else{
                    $data['item'][$value['category_parent']]['children'][$value['id']] = $value_data;
                }
            }
        }

        if($method === 'json'){
            return json_encode($data);
        }else if($method === 'xml'){
            $enc = 'UTF-8';
            $xml_data = ['enc' => $enc];
            $xml_data['cat_id'] = $data['cat_id'];
            $xml_data['item'] = array_values($data['item']);
            return Response::view('xml_v', compact('xml_data', 'enc'))
                ->header('Content-Type', 'text/xml')
                ->header('Pragma', 'public')
                ->header('Cache-control', 'private')
                ->header('Expires', '-1');
        }
    }

    /**
     * Comment: Return category sort according most popular sales
     *
     * @api TRUE
     * @author YEE HAO
     * @since 16 APR 2021
     * @param method
     * @version 1.1
     * @method GET, POST, any request method
     * @return JSON | XML
     * @used-by revamp.jocom.com.my home page
     *
     * Last Update: 16 APR 2021
     */
    public function anyCatorderbysales(){
        $method = Input::get('method', 'json');
        $category_id = Input::get('cat_id', 1071);
        
        $query = Category::
                where('permission', '=', 0)
                ->where('status', '=', 1)
                ->where('id', '>', 0);



        // get only grocery main cat result
        if(Input::get('only_grocery')){
            $temp_query = clone $query;
            $cat_ids = $temp_query
                    ->where('category_parent', '=', $category_id)
                    ->lists('id');
        }

        $temp_query = clone $query;
        $cat_data = $temp_query
                    ->where('category_parent', '=', $category_id)
                    ->select('id AS category_id', 'category_name', 'category_img AS image', 'category_img_banner AS banner')
                    ->get();
        

        $result = DB::select(
            DB::raw("
                SELECT
                    sales_total.category_id as category_id,
                    sales_total.category_name as category_name,
                    sales_total.image as image,
                    sales_total.banner as banner,
                    SUM( sales_total.total) AS total
                FROM (
                    SELECT 
                        b.product_id AS product_id, 
                        e.id as category_id,
                        e.category_name as category_name,
                        e.category_img as image,
                        e.category_img_banner as banner,
                        ROUND( SUM( b.total ) / COUNT(e.category_name), 2) AS total
                    FROM jocom_transaction AS a
                    LEFT JOIN jocom_transaction_details AS b 
                        ON a.id = b.transaction_id
                    LEFT JOIN jocom_products AS c 
                        ON b.product_id = c.id
                    LEFT JOIN jocom_categories AS d 
                        ON c.id = d.product_id
                    LEFT JOIN jocom_products_category AS e 
                        ON d.category_id = e.id
                    WHERE
                        a.status = 'completed' AND
                        b.product_id <> 0 AND
                        c.name IS NOT NULL
                    GROUP BY
                        b.p_option_id
                ) AS sales_total

                " . (Input::get('only_grocery') ? "WHERE sales_total.category_id IN(" . implode(", ", $cat_ids) . ")" : "") . "
                
                GROUP BY sales_total.category_id
                
                ORDER BY total DESC
            ")
        );


        $data = [
            'img_link' => Image::link("images/category/"),
            'cat' => $cat_data,
            'sort' => $result,
        ];
        if($method === 'json'){
            return json_encode($data);
        }else if($method === 'xml'){
            $enc = 'UTF-8';
            $data['enc'] = $enc;
            $xml_data = json_encode($data);
            $xml_data = json_decode($xml_data, TRUE);
            return Response::view('xml_v', compact('xml_data', 'enc'))
                ->header('Content-Type', 'text/xml')
                ->header('Pragma', 'public')
                ->header('Cache-control', 'private')
                ->header('Expires', '-1');
        }return $result;
    }
    
    /*
     * @Desc    : Get checkout cart items
     * @Param   : cartsession_id, qrcode, price_option_id, qty
     * @Method  : POST
     * @return  : JSON
     */
     
    private function updateManagecart($qrcode, $cartsession_id, $priceoption, $qty){
        foreach ($qrcode as $key => $value) {
            $cart                   = new ManageCart;
            $cart->cartsession_id   = $cartsession_id;
            $cart->qrcode           = $qrcode[$key];
            $cart->price_option_id  = $priceoption[$key];
            $cart->qty              = $qty[$key];
            $cart->status           = 1;
            $cart->created_at       = date('Y-m-d H:i:s');
            $cart->created_by       = 'API_UPDATE';
            $cart->updated_at       = date('Y-m-d H:i:s');
            $cart->updated_by       = 'API_UPDATE';
            $cart->save();
        }

        return $cart;
    }

     public function postCartitems(){
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $cartsession_id="";
        $flag = 0;
        $data = array();
        $qrcode = array();
        $priceoption = array();
        $qty = array();
        
        try{
            // Start transaction
            DB::beginTransaction();

            $cartsession_id = Input::get('cartsession_id'); 
            $qrcode = Input::get('qrcode'); 
            $priceoption = Input::get('price_option_id'); 
            $qty = Input::get('qty'); 

            $UserResult = DB::table('jocom_managecart_items')
                                ->where('cartsession_id','=', $cartsession_id)
                                ->where('paymentstatus','=', 0)
                                ->get(); 
            
            if(count($UserResult) > 0){
                $res = ManageCart::where('cartsession_id',$cartsession_id)->where('paymentstatus', 0)->delete();
            } else {
                $rndID              = ManageCart::rnd_number();
                $gmtdatestring      = $rndID.gmdate("D, d M Y H:i:s", time() + 3600*($timezone+date("I")));
                $cartsession_id     = md5($gmtdatestring);
            }
            $this->updateManagecart($qrcode, $cartsession_id, $priceoption, $qty);
            $flag = 1;

            if($flag == 1){
                $CartResults = DB::table('jocom_managecart_items')
                            ->where('cartsession_id', '=', $cartsession_id)
                            ->where('paymentstatus',0)
                            ->get();
                                    
                if(count($CartResults) > 0){

                    foreach ($CartResults as $value) {
                        $qty = 0; 
                        $qty = $value->qty; 

                        // check the qr code strt with the JC
                        // substr($value->qrcode, 0, 2)
                        $QRcode = substr($value->qrcode, 0, 2);
                        $QR_int = (int)substr($value->qrcode, 2);

                        $opt_id = is_numeric($value->price_option_id); // check is numeric if numeric then consider normal product
                        
                        $flash_check = explode('[', $value->price_option_id);
                        if(count($flash_check) == 2){
                            $f_p_id = substr($flash_check[0], 0, 2);
                            $f_p_id = (is_numeric($f_p_id) ? $f_p_id : false);
                        }

                        if($QRcode === "JC" && $QR_int && $opt_id){
                            // if start with JC and all end is string
                            $datares = DB::table('jocom_products as JP')
                                    ->select('JP.qrcode','JP.sku','JP.name','JP.img_1','JPP.id','JPP.price','JPP.price_promo')  
                                    ->leftjoin('jocom_product_price as JPP','JPP.product_id','=','JP.id')
                                    ->where('JP.qrcode', $value->qrcode)
                                    ->where('JPP.id',$value->price_option_id)
                                    ->where('JPP.status',1)
                                    ->first();
                        }else if($f_p_id){
                            // gonna check the format if valid then do the stuff
                            // if start with JC and all end is string
                            $datares = DB::table('jocom_flashsale_products as JFP')
                                ->select('JP.sku', 'JP.name', 'JP.qrcode', 'JP.img_1', 'JFP.label_id', 'JP.gst',
                                        'JFP.actual_price AS price', 'JFP.promo_price AS price_promo')
                                ->join('jocom_products AS JP', 'JP.id', '=', 'JFP.product_id')
                                ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                                ->where('JFP.id', '=', $f_p_id)
                                ->where('JFP.activation', 1)
                                ->where('JP.status', 1)
                                ->first();
                        }

                        if(count($datares) > 0){
                            // these store is the product just 1 qty price - it wont according the qty number for increase the price
                            $temparray = array(
                                'qrcode'            => $datares->qrcode, 
                                'sku'               => $datares->sku, 
                                'name'              => $datares->name,
                                'img_thumb_1'       => (!empty($datares->img_1)) ? Image::link("images/data/thumbs/" . $datares->img_1) : '', 
                                'price_option_id'   => $datares->id, 
                                'price'             => $datares->price,         
                                'price_promo'       => $datares->price_promo, 
                                'qty'               => $qty, 
                            );
                            array_push($data, $temparray);
                        }
                    }
                }
            }

        } catch (Exception $ex) {
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
        } finally {
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }

        $response = array("RespStatus"=> $RespStatus, "error"=> $isError, "errorCode"=> $errorCode, "message" => $message, "cartsession_id" => $cartsession_id, "data"=> $data);
       
        return Response::json($response);
    }
    
   /**
     * Comment: Callback of anyFlashsales API return data
     * Purpose: Arrange the Data according the need
     *
     * @api FALSE
     * @author YEE HAO
     * @since 6 OCT 2021
     * @param string flashtype ['Exclusive', 'JP', 'Dynamic', 'Flash', 'All'], For get the the banner type of flash sales
     * @param object product_data, it generate according the table join on anyFlashsales
     * @version 1.0
     * @method Pass the Parameter
     * @return XML product data
     *
     * Last Update: 7 OCT 2021
     */
    private function flashsales_formatarrange_callback($flashtype, $v){
        $flashtype = ($flashtype ? $flashtype : Input::get('flashtype'));
        $wt = DB::table('jocom_product_price')->where('id',$v->label_id)->first();

        if ($v->gst == 2) {
            $tax_rate = Fees::get_tax_percent();
            $after_gst = $v->actual_price * (( 100 + ($tax_rate)) / 100);
            $gst_ori = $after_gst - $v->actual_price;
            $ori_price = $v->actual_price + $gst_ori;

            $after_gst2 = $v->promo_price * (( 100 + ($tax_rate)) / 100);
            $gst_ori2 = $after_gst2 - $v->promo_price;
            $promo_price = $v->promo_price + $gst_ori2;

        }else{        
            $gst_ori = 0;
            $ori_price = $v->actual_price;

            $gst_ori2 = 0;
            $promo_price = $v->promo_price;
        }

        $discPrice =  ApiProduct::hidePricing('', $promo_price);  

        if ($discPrice != "0") {
            $discpercent = (($ori_price - $promo_price) * 100) / $ori_price; 
            $percent = number_format($discpercent, 0) . '%';
        }else{
            $percent = '';
        }

        if($flashtype === 'Exclusive'){
            $total = DB::table('jocom_jocomexcorner_stock')->where('fpid','=',$v->fid)->first();
        } else if($flashtype === 'ComboDeal') {
            $total = DB::table('jocom_combodeals_stock')->where('fpid','=',$v->fid)->first();
        } else if($flashtype === 'Dynamic') {
            $total = DB::table('jocom_dynamicsale_stock')->where('fpid','=',$v->fid)->first();
        } else {
            $total = DB::table('jocom_flashsale_stock')->where('fpid','=',$v->fid)->first();
        }

        if (isset($total)) {
            $total_sold = $v->qty;
        }else{
            $total_sold = 0;
        }

        $zones = [];
        $deliveryZones  = Delivery::getZonesByProduct($v->proId);

        foreach ($deliveryZones as $deliveryZone){
            $zones[] = [
                'zone' => $deliveryZone->zone_id,
                'zone_name' => $deliveryZone->zone_name,
            ];
        }

        $points  = Comment::scopeCommentsRating($v->proId);
        
        $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
        $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
        $multiply = 1;

        if ($discPrice > 0) {
            $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
            $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiply;
        }else{
            $pointsJpoint = ($v->actual_price) * $Jpoint->earn_rate * $multiply;
            $pointsBpoint = ($v->actual_price) * $Bpoint->earn_rate * $multiply;
        }
        
        if ($v->freshness_days !='' && $v->freshness_days !=0) {
            $freshness_tag = $v->freshness_days . ' days freshness.';
        }else{
            $freshness_tag = '';
        }

        $temp_data = [
            'sku' => $v->sku,
            'qrcode' => $v->qrcode,
            'name' => $v->name,
            'description' => $v->description,
            'delivery_time' => $v->delivery_time,
            'img_1' => ( ! empty($v->img_1)) ? Image::link("images/data/".$v->img_1) : '',
            'img_2' => ( ! empty($v->img_2)) ? Image::link("images/data/".$v->img_2) : '',
            'img_3' => ( ! empty($v->img_3)) ? Image::link("images/data/".$v->img_3) : '',
            'thumb_1' => ( ! empty($v->img_1)) ? Image::link("images/data/thumbs/".$v->img_1) : '',
            'thumb_2' => ( ! empty($v->img_2)) ? Image::link("images/data/thumbs/".$v->img_2) : '',
            'thumb_3' => ( ! empty($v->img_3)) ? Image::link("images/data/thumbs/".$v->img_3) : '',
            'vid_1' => ( $v->vid_1 != "") ? $v->vid_1 : '',
            'total_sold'=> $total_sold,
            'zone_records'      => count($zones),
            'delivery_zones'    => [$zones],
            'option' => [
                [
                    'label' => $v->label,
                    'label_id' => $v->label_id,
                    'priceopt' => (
                        $flashtype === 'Exclusive' ? "EC" : 
                        ($flashtype === 'ComboDeal' ? "CD" : 
                        ($flashtype === 'Dynamic' ? "DY" : "FS"
                    ))) . $v->fid . "[" . $v->label_id . "]",   
                    'price' => round($ori_price, 2),  
                    'promo_price' => $promo_price,
                    'stock' => $v->limit_quantity,
                    'default'=> ($wt->default == 1) ? 'TRUE' : 'FALSE',
                    'p_weight' => $wt->p_weight,
                    'discount_percent' => $percent,
                    'jpoint'        => floor($pointsJpoint),
                    'bpoint'        => floor($pointsBpoint),
                ],
            ],
            'fpid_ref'           => $v->fid,
            'freshness'         => $v->freshness, 
            'freshness_days'    => $freshness_tag,
            'bulk'              => empty($v->bulk) ? '0' : $v->bulk,
            'halal'          => empty($v->halal) ? '0' : $v->halal,
            'overall_rating' => empty($points) ? '' : $points,
            'min_qty'       => (empty($v->min_qty) ? '' : $v->min_qty),
            'max_qty'       => (empty($v->max_qty) ? '' : $v->max_qty),
        ];

        return $temp_data;
    }

    /**
     * Comment: 
     *
     * @api TRUE
     * @author YEE HAO
     * @since 3 OCT 2021
     * @param string flashtype ['Exclusive', 'JP', 'Dynamic', 'Flash', 'All'], For get the the banner type of flash sales
     * @param object product_data, it generate according the table join on anyFlashsales
     * @param string return_format ['banner', 'product'], modify the return format of the flash sales
     * @version 1.0
     * @method Pass the Parameter
     * @return XML product data || Status Message
     *
     * Last Update: 7 OCT 2021
     */
    private function flashsales_formatarrange($flashtype, $flash, $return, $newArray = []){
        if($return === 'banner'){
            $banner_setup = false;
            $index = count($newArray);

            foreach ($flash as $v){
                if (!isset($newArray[$index]) ) {
                    $newArray[$index] = array(
                        'flash_id' => $v->id, 
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
                        'rule_name' => $v->rule_name,
                        'type' => $v->type,
                        'item' => array(),
                    );

                    if($flashtype === 'Dynamic'){
                        $newArray[$index]['title_filename'] = (!empty($v->title_filename) ? Image::link("dynamic/images/". $v->title_filename) : '');
                        $newArray[$index]['title_mime'] = $v->title_mime;
                        $newArray[$index]['banner_filename'] = (!empty($v->banner_filename) ? Image::link("dynamic/images/". $v->banner_filename) : '');
                        $newArray[$index]['banner_mime'] = $v->banner_mime;
                    }
                }

                $newArray[$index]['item'][] = $this->flashsales_formatarrange_callback($flashtype, $v);
            }
        }else{
            foreach ($flash as $v){
                $temp_data = $this->flashsales_formatarrange_callback($flashtype, $v);
                $temp_data['flash_id'] = $v->id;
                $temp_data['valid_from'] = $v->valid_from;
                $temp_data['valid_to'] = $v->valid_to;
                $temp_data['rule_name'] = $v->rule_name;
                $temp_data['type'] = $v->type;

                if($flashtype === 'Dynamic'){
                    $temp_data['title_filename'] = (!empty($v->title_filename) ? Image::link("dynamic/images/".$v->title_filename) : '');
                    $temp_data['title_mime'] = $v->title_mime;
                    $temp_data['banner_filename'] = (!empty($v->banner_filename) ? Image::link("dynamic/images/".$v->banner_filename) : '');
                    $temp_data['banner_mime'] = $v->banner_mime;
                }

                $newArray[] = $temp_data;
            }
        }

        return $newArray;
    }

    /**
     * Comment: Callback of anyFlashsales API return DB fetch data
     * Purpose: 
     *
     * @api FALSE
     * @author YEE HAO
     * @since 6 OCT 2021
     * @param string flashtype, it generate according the table join on anyFlashsales
     * @param integer regions_id, not use anymore, dunno why still need it
     * @param string from, 
     * @param integer flash_id,
     * @param array flash_product,
     * @version 1.0
     * @method Pass the Parameter
     * @return Laravel DB fetch result Object
     *
     * Last Update: 7 OCT 2021
     */
    private function flashsales_datallocate($flashtype, $regions_id, $from, $flash_id, $flash_product){
        if($flashtype === 'Exclusive'){
            $flash = DB::table('jocom_jocomexcorner AS JF')
                ->leftJoin('jocom_jocomexcorner_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id');
        } else if($flashtype === 'ComboDeal') {
            $flash = DB::table('jocom_combodeals AS JF')
                ->leftJoin('jocom_combodeals_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id');
        } else if($flashtype === 'Dynamic') {
            $flash = DB::table('jocom_dynamic_sale AS JF')
                ->leftJoin('jocom_dynamicsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id');
        } else { // Treat as Flash Sales
            $flash = DB::table('jocom_flashsale AS JF')
                ->leftJoin('jocom_flashsale_products AS JFP','JFP.fid','=','JF.id')
                ->leftJoin('jocom_products AS JP','JP.id','=','JFP.product_id');
        }

        if ($regions_id != ''){
            $flash = $flash->where('JP.region_id', $regions_id);
        }

        if($from != ''){
            $flash = $flash->whereDate('JF.valid_from', '<=', date_format(date_create($from), 'Y-m-d H:i:s'))
                    ->whereDate('JF.valid_to', '>=', date_format(date_create($from), 'Y-m-d H:i:s'));
        }

        if($flash_id != ''){
            $flash = $flash->where('JF.id', $flash_id);
        }

        if($flash_product != '' && is_array($flash_product)){
            $flash = $flash->whereIn('JFP.id', $flash_product);
        }else if($flash_product != ''){
            $flash = $flash->where('JFP.id', $flash_product);
        }

        if(empty($flash_id) && empty($flash_product)){
            $flash = $flash->where('JF.status',1)
            ->where('JFP.activation',1);
        }

        if($flashtype === 'Exclusive' || $flashtype === 'ComboDeal'){
            $flash = $flash->where('JFP.qty','>=',0)
                ->select(
                    'JF.id', 'JF.type', 'JF.rule_name',
                    'JF.valid_from', 'JF.valid_to',
                    // it dont have 'JF.title_filename', 'JF.title_mime', 'JF.banner_filename', 'JF.banner_mime'
                    'JFP.id as fid', 'JFP.label_id',
                    'JFP.label', 'JFP.product_id',
                    'JP.sku', 'JP.delivery_time',
                    'JP.min_qty', 'JP.max_qty',
                    'JP.name', 'JP.id as proId',
                    'JP.description',
                    'JP.img_1', 'JP.img_2', 'JP.img_3',
                    'JP.vid_1', 'JP.qrcode',
                    'JP.gst', 'JP.freshness',
                    'JP.freshness_days',
                    'JP.bulk', 'JP.halal',
                    'JFP.actual_price', 'JFP.promo_price',
                    'JFP.limit_quantity', 'JFP.qty'
                )
                ->orderBy('JFP.seq')
                ->get();
        } else if($flashtype === 'Dynamic') {
            $flash = $flash->where('JFP.qty','>=',0)
                ->select(
                    'JF.id', 'JF.type', 'JF.rule_name',
                    'JF.valid_from', 'JF.valid_to',
                    'JF.title_filename', 'JF.title_mime', 'JF.banner_filename', 'JF.banner_mime',
                    'JFP.id as fid', 'JFP.label_id',
                    'JFP.label', 'JFP.product_id',
                    'JP.sku', 'JP.delivery_time',
                    // it dont have JP.min_qty and JP.max_qty
                    'JP.min_qty', 'JP.max_qty',
                    'JP.name', 'JP.id as proId',
                    'JP.description',
                    'JP.img_1', 'JP.img_2', 'JP.img_3',
                    'JP.vid_1', 'JP.qrcode',
                    'JP.gst', 'JP.freshness',
                    'JP.freshness_days',
                    'JP.bulk', 'JP.halal',
                    'JFP.actual_price', 'JFP.promo_price',
                    'JFP.limit_quantity', 'JFP.qty'
                )
                ->orderBy('JFP.seq')
                ->get();
        }else{
            $flash = $flash->where('JFP.qty','>=',0)
                ->select(
                    'JF.id', 'JF.type', 'JF.rule_name',
                    'JF.valid_from', 'JF.valid_to',
                    // it dont have 'JF.title_filename', 'JF.title_mime', 'JF.banner_filename', 'JF.banner_mime'
                    'JFP.id as fid', 'JFP.label_id',
                    'JFP.label', 'JFP.product_id',
                    'JP.sku', 'JP.delivery_time',
                    'JFP.min_qty', 'JFP.max_qty', 
                    'JP.name', 'JP.id as proId',
                    'JP.description',
                    'JP.img_1', 'JP.img_2', 'JP.img_3',
                    'JP.vid_1', 'JP.qrcode',
                    'JP.gst', 'JP.freshness',
                    'JP.freshness_days',
                    'JP.bulk', 'JP.halal',
                    'JFP.actual_price', 'JFP.promo_price',
                    'JFP.limit_quantity', 'JFP.qty'
                )
                ->orderBy('JFP.seq')
                ->get();
        }

        return $flash;
    }
    
    /**
     * Comment: Combine API of the Flash Sales/Exclusive Sales/Combo Sales/Dynamic Sales
     *
     * @api TRUE
     * @author YEE HAO
     * @since 3 OCT 2021
     * @param string from, the start duration of the flash sales
     * @param string latitude, For goolge api use to retrive location
     * @param string longitude, For goolge api use to retrive location
     * @param integer id, State ID
     * @param integer stateid, State ID
     * @param string flashtype ['Exclusive', 'JP', 'Dynamic', 'Flash', 'All'], For get the the banner type of flash sales
     * @param string return_format ['banner', 'product'], modify the return format of the flash sales
     * @version 1.5
     * @method Pass the Parameter
     * @return XML product data || Status Message
     * @used-by jocom.my product module
     * @used-by jocom.my product details
     * @used-by jocom.my cart module
     *
     * Last Update: 6 OCT 2021
     */
    public function anyFlashsales(){

        $flash_id = Input::get('flash_id');
        $flash_product = Input::get('flash_product');
        $return = Input::get('return_format', 'banner');
        $from = Input::get('from');

        // Check latitude and longitude
        if(Input::get('latitude') != '' && Input::get('longitude') != ''){
            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'http://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
            $json = file_get_contents($url);
            $test = json_decode($json);

            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                        $response[] = $addressComponet->long_name;
                    }
                    if(in_array('country', $addressComponet->types)) {
                        $response[] = $addressComponet->long_name;
                    }

                    if(in_array('postal_code', $addressComponet->types)) {
                        $response[] = $addressComponet->long_name;
                    }
                }
            }

            $region_name = $response[0];
            $country_name = $response[1];
            $postcodenum = $response[2];

            $postcode = DB::table('postcode')->where('postcode', '=', $postcodenum)->first();
            $statecode = DB::table('state')->where('state_code', '=', $postcode->state_code)->first();
            if ($statecode->state_name == "Kuala Lumpur") {
                $name2 = "WP-" . $statecode->state_name;
            } else {
                $name2 = $statecode->state_name;
            }

            $country = DB::table('jocom_countries')->where('name', '=', $country_name)->first();

            if ($country_name !='Malaysia') {
                $regions = DB::table('jocom_country_states')->where('country_id','=','458')->where('name','LIKE','%Kuala Lumpur%')->first();
                $regions_id = $regions->region_id;
            }else{
                $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
                $regions_id = $regions->region_id;
                $allRegion = 0;
            }
        }
    
        if(Input::get('id') != "" || Input::get('stateid') != ""){
            if(Input::get('id') != ""){
                $stateid = strtoupper(Input::get('id'));
            }else{
                $stateid = strtoupper(Input::get('stateid'));
            }

            if($stateid == 458999){
                $regions_id = 1;
            }else{
                $stateidInfo = State::find($stateid);
                $regions_id = $stateidInfo->region_id;
            }
        }

        if(Input::get('flashtype') !== 'All'){
            $flash = $this->flashsales_datallocate(Input::get('flashtype', 'Flash'), $regions_id, $from, $flash_id, $flash_product);
            $batch = $this->flashsales_formatarrange(Input::get('flashtype', 'Flash'), $flash, $return);
        }else{
            $flash_type = [
                'Flash',
                'Exclusive',
                'ComboDeal',
                'Dynamic'
            ];
            $batch = [];
            foreach ($flash_type as $type) {
                $flash = $this->flashsales_datallocate($type, $regions_id, $from, $flash_id, $flash_product);
                $batch = $this->flashsales_formatarrange($type, $flash, $return, $batch);
            }
        }

        $data = [
            'xml_data' => [
                'batch' => [],
            ],
        ];
        $data['xml_data']['batch'] = $batch;

        return Response::json($data);
    }
    
    
    public function anyGrabsettings(){
        $status = 1;
        
        $dataSettings = array(
                            'envurl' => Config::get('constants.GRABPAY_ENV_PRO_URL'), 
                            'partnerid' => Config::get('constants.GRABPAY_ENV_PRO_PARTNER_ID'), 
                            'partnersecret' => Config::get('constants.GRABPAY_ENV_PRO_PARTNER_SECRET'), 
                            'clientid' => Config::get('constants.GRABPAY_ENV_PRO_CLIENT_ID'), 
                            'clientsecret' => Config::get('constants.GRABPAY_ENV_PRO_CLIENT_SECRET'), 
                            'mid' => Config::get('constants.GRABPAY_ENV_PRO_MID'), 
                            'urlredirect' => Config::get('constants.GRABPAY_ENV_PRO_REDIRECT'), 
                            'urlgenerate' => 'https://api.jocom.com.my/grabpay/generate', 
                            
                        );
         return Response::json($dataSettings);
         
    }
    
    /**
     * Comment: Banner include its content
     *
     * @api TRUE
     * @author YEE HAO
     * @since 25 AUG 2021
     * @param method
     * @version 1.4
     * @method GET, POST, any request method
     * @return JSON | XML
     * @used-by jocom.my home page
     * @used-by jocom.my flash sales page
     * @used-by jocom.my banner page
     *
     * Last Update: 10 MAY 2022
     */
      public function anyJocommybanner()
    {
        $method = Input::get('method', 'json');
        $type = Input::get('type');
        $id = Input::get('id', 0);
        $event_id = (Input::get('EventType') ? Input::get('EventType') : 0);
        $from = Input::get('from', 0);
        $limit = Input::get('limit', 0);
        $fetch_template = Input::get('template_fetch', false);
        
        $type_ref = [
            0 => [
                'hero' => 'banner_hero',
                'brand' => 'banner_brand',
                'special' => 'banner_special',
                'partner' => 'banner_partner',
                'category' => 'banner_category',
                'flashsales' => 'banner_flashsales',
            ]
        ];

        $result = DB::table('jocommy_banner_event_format')->select('event_id', 'type', 'banner_type')->get();
        foreach ($result as $event_method) {
            $name = str_replace("banner_", "", $event_method->type);
            if(isset($type_ref[$event_method->event_id]))
                $type_ref[$event_method->event_id][$name] = $event_method->banner_type;
            else
                $type_ref[$event_method->event_id] = [ $name => $event_method->banner_type ];
        }
        
        $query = DB::table('jocommy_banner_manage')->select('id', 'type', 'content_type', 'content_data', 'image', 'image_m', 'title', 'status', 'position', 'logic_operation', 'begin_at', 'duration');
        if($id) $query->where('id', $id);
        if($event_id == 0 || $event_id) $query->where('event_id', $event_id);
        if(isset($type_ref[$event_id][$type])) $query->where('type', $type_ref[$event_id][$type]);
        $result = $query->where('status', 1)->orderBy('position', 'ASC')->get();

        if($fetch_template){
            $new = json_decode(json_encode($result), true);
            $temp = [];
            foreach ($new as $k => $v) {
                if($v['content_type'] !== 'template') continue;
                $temp_v = json_decode($v['content_data'], true);
                if(json_last_error() === 0) $v['content_data'] = (isset($temp_v['id']) ? $temp_v['id'] : $temp_v);
                $temp[$k] = $v;
            }
            if(count($temp) > 0){
                $ref_0 = array_combine(array_column($temp, 'id'), array_column($temp, 'content_data'));
                $ref_1 = array_combine(array_keys($temp), array_column($temp, 'id'));
                DB::connection()->setFetchMode(PDO::FETCH_ASSOC); // YH: no longer supported as of Laravel 5.4
                $template_data = DB::table('jocommy_template')->select('id', 'name', 'banner_image', 'content_html', 'status', 'modify_at')->whereIn('id', array_unique($ref_0))->get();
                $template_data = array_combine(array_column($template_data, 'id'), $template_data);
                foreach ($ref_1 as $index => $banner_id) $result[$index]->content_data = $template_data[$ref_0[$banner_id]];
            }
        }

        $data = [
            'img_path' => url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH'),
            'banner' => $result
        ];

        if($method === 'json'){
            return json_encode($data);
        }else if($method === 'xml'){
            $enc = 'UTF-8';
            $data['enc'] = $enc;
            $xml_data = json_encode($data);
            $xml_data = json_decode($xml_data, TRUE);
            return Response::view('xml_v', compact('xml_data', 'enc'))
                ->header('Content-Type', 'text/xml')
                ->header('Pragma', 'public')
                ->header('Cache-control', 'private')
                ->header('Expires', '-1');
        }
        return $result;
    }

    /**
     * Comment: Banner include its content
     *
     * @api TRUE
     * @author YEE HAO
     * @since 25 AUG 2021
     * @param method
     * @version 1.0
     * @method GET, POST, any request method
     * @return JSON | XML
     * @used-by jocom.my home page
     * @used-by jocom.my flash sales page
     * @used-by jocom.my banner page
     *
     * Last Update: 26 MAY 2022
     */
    public function anyJocommytemplate()
    {
        $id = Input::get('id', 0);
        $method = Input::get('method', 'json');
        if(!$id) return false;
        $data = [
            'img_path' => url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH'),
            'template' => DB::table('jocommy_template')->select('id', 'name', 'banner_image', 'content_html', 'status', 'modify_at')->where('id', $id)->first()
        ];

        if($method === 'json'){
            return json_encode($data);
        }else if($method === 'xml'){
            $enc = 'UTF-8';
            $data['enc'] = $enc;
            $xml_data = json_encode($data);
            $xml_data = json_decode($xml_data, TRUE);
            return Response::view('xml_v', compact('xml_data', 'enc'))
                ->header('Content-Type', 'text/xml')
                ->header('Pragma', 'public')
                ->header('Cache-control', 'private')
                ->header('Expires', '-1');
        }
        return $data;
    }

    public function postProducttext(){

        $isError = 0;
        $RespStatus = 1;
        $message = "";
        
        $data = array();

        try{
            // Start transaction
            DB::beginTransaction();

            $jccode = explode(",",Input::get('code'));

            foreach ($jccode as $key => $codevalue) {
               $codevalue = str_replace(' ','',$codevalue);
                $result = Product::where('qrcode','=',$codevalue)->first(); 

                $category =   explode(",",$result->category);
                $cat_text = array();
                
                foreach ($category as $key => $value) {
                    # code...
                    // echo $value.'-';
                     $cateresult = Category::where('id','=',$value)->first();
                     array_push($cat_text, str_replace('+','-',urlencode($cateresult->category_name)));
                }

               

                $tempdata = array('code' => $codevalue, 
                              'name' => str_replace('+','-',urlencode($result->name)),
                              'category_id'  => array($result->category),
                              'category_name' => $cat_text, // str_replace('+','-',urlencode($cat_text)) ,
                        );

                array_push($data, $tempdata);
            }

            


        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);

        return Response::json($response);

    }
    
    public function anyTopspender(){


         $start='2022-07-15 00:00:00';
         $end='2022-07-17 23:59:59';
         $leaderborad=DB::table('jocom_transaction AS JT')
                ->join('jocom_user AS JU','JT.buyer_id','=','JU.id')
                ->select(DB::raw("concat(JU.firstname,' ',JU.lastname) AS name"),DB::raw("sum(JT.total_amount) as revenue"))
                ->whereNotIn('JT.buyer_username',['11Street','lazada','Qoo10','shopee','Astro Go Shop','vettons','tiktokshop','pgmall','Lamboplace','wong_kinhing'])
                ->where('JT.device_platform','<>','manual')
                ->whereBetween('JT.transaction_date',[$start, $end])
                ->where('JT.status','=','completed')
                ->groupBy('JT.buyer_username')
                ->orderBy(DB::raw("sum(JT.total_amount)"),'DESC')
                ->limit('10')
                ->get();
          $i=1;
          foreach ($leaderborad as  $value) {
                $response[]=array('rank'=>$i,
                                  'name'=>$value->name,
                                  'revenue'=>$value->revenue);
            $i++;
          }
           
    
          return Response::json($response);

    }
    
    public function anyCategorylatest()
    {
        $limit  = Input::get('count', 50);
        $offset = Input::get('from', 0);
        
        $cat_id = Input::get('product_cat');
        $custom=Input::get('custom');
        $username=Input::get('username');
        $charity=Input::get('charity');
        $device=Input::get('device');
        $city=Input::get('city');
        

        $category   = empty($cat_id) ? '0' : $cat_id;
        $permission = empty($custom) ? '0' : $custom;
        $username   = empty($username) ? NULL : $username;
        $charity    = empty($charity) ? '0' : $charity;
        // $lang       = array_get($params, 'lang', 'en');
        $device     = empty($device) ? 'phone' : $device;
        $city       = empty($city) ? '0' : $city;   // Added by Maruthu
        
        if (isset($username) && ! empty($username) && $category == 0)
        {
            $userCategories = DB::table('jocom_user_category')
                ->select('category_id')
                ->where('username', '=', $username)
                ->get();

            foreach ($userCategories as $userCategory)
            {
                $categoryList[] = $userCategory->category_id;
            }
        }
        $count =DB::table('jocom_products_category')
                ->where('category_parent', '=', $category)
                ->where('permission', '=', $permission)
                ->where('status', '=', 1)
                ->where('id', '>', 0)
                ->count();
                
        $categories =Category::where('category_parent', '=', $category)
                ->where('permission', '=', $permission)
                ->where('status', '=', 1)
                ->where('id', '>', 0); 
                
         if (is_numeric($limit) && $limit > 0)
        {
            $categories = $categories->take($limit);
        }

        if (is_numeric($offset) && $offset >= 0)
        {
            $categories = $categories->skip($offset);
        }

        if (isset($categoryList) && ! empty($categoryList))
        {
            $categories = $categories->whereIn('id', $categoryList);
        }

        $data['xml_data'] = [
            'record'        => $categories->get()->count(), 
            'cat_parent'    => $category,
            'total_record'  => $count,
        ];
        $categories = $categories->orderBy('weight', 'desc')->orderBy('category_name', 'asc')->get();
        
        foreach ($categories as $category)
        {
            switch (input::get('lang'))
            {
                case 'CN':
                case 'cn':
                    $language = '_cn';
                    break;
                case 'MY':
                case 'my':
                    $language = '_my';
                    break;
                default:
                    $language = '';
                    break;
            }

            if ( ! empty($category->{"category_name{$language}"}))
            {
                $categoryName = $category->{"category_name{$language}"};
            }
            else
            {
                $categoryName = $category->category_name;
            }

        if ( ! isset($categoryName))
            {
                $categoryName = $category->category_name;
            }
        $childCategories = ApiProduct::fetchCategoryTree($category->id);
        $regex = $category->id;

            foreach ($childCategories as $child)
            {
                $regex = "{$regex}|{$child['id']}";
            }
        if (isset($city) && ! empty($city))
            {
                $productsCount = Product::join('jocom_product_delivery', 'jocom_product_delivery.product_id', '=', 'jocom_product_and_package.id')
                ->join('jocom_zone_cities', 'jocom_zone_cities.zone_id', '=', 'jocom_product_delivery.zone_id')
                ->where('status', '=', 1)
                ->where(function($categories) use ($regex)
                {
                    $categories->where('category', 'REGEXP', "(^|,)({$regex})(,|$)");
                })
                ->where('jocom_zone_cities.city_id', '=', $city)
                ->count();
            }
            else 
            {
                $productsCount = Product::where('status', '=', 1)
                ->where(function($categories) use ($regex)
                {
                    $categories->where('category', 'REGEXP', "(^|,)({$regex})(,|$)");
                })
                ->count();
            }

            $categoryImage = ( ! empty($category->category_img)) ?
            url("images/category/{$category->category_img}") : '';
            
            $categoryImagebanner = ( ! empty($category->category_img_banner)) ?
                url("images/category/{$category->category_img_banner}") : '';

            $data['xml_data']['item'][] = [
                        'id'        => $category->id,
                        'cat_name'  => $categoryName,
                        'cat_icon'  => $categoryImage,
                        'cat_banner'  => $categoryImagebanner,
                        'p_count'   => $productsCount,
                        'sub_cat'   => (count($childCategories)) ? 1 : 0,
                        ];
        }
         
          return json_encode($data);
        
    }
    
    public function anyCategoryv2()
    {
        $limit  = Input::get('count', 50);
        $offset = Input::get('from', 0);
        
        $cat_id = Input::get('product_cat');
        $custom=Input::get('custom');
        $username=Input::get('username');
        $charity=Input::get('charity');
        $device=Input::get('device');
        $city=Input::get('city');
        

        $category   = empty($cat_id) ? '0' : $cat_id;
        $permission = empty($custom) ? '0' : $custom;
        $username   = empty($username) ? NULL : $username;
        $charity    = empty($charity) ? '0' : $charity;
        // $lang       = array_get($params, 'lang', 'en');
        $device     = empty($device) ? 'phone' : $device;
        $city       = empty($city) ? '0' : $city;   // Added by Maruthu
        
        if (isset($username) && ! empty($username) && $category == 0)
        {
            $userCategories = DB::table('jocom_user_category')
                ->select('category_id')
                ->where('username', '=', $username)
                ->get();

            foreach ($userCategories as $userCategory)
            {
                $categoryList[] = $userCategory->category_id;
            }
        }
        $count =DB::table('jocom_products_category')
                ->where('category_parent', '=', $category)
                ->where('permission', '=', $permission)
                ->where('status', '=', 1)
                ->where('id', '>', 0)
                ->count();
                
        $categories =Category::where('category_parent', '=', $category)
                ->where('permission', '=', $permission)
                ->where('status', '=', 1)
                ->where('id', '>', 0); 
                
         if (is_numeric($limit) && $limit > 0)
        {
            $categories = $categories->take($limit);
        }

        if (is_numeric($offset) && $offset >= 0)
        {
            $categories = $categories->skip($offset);
        }

        if (isset($categoryList) && ! empty($categoryList))
        {
            $categories = $categories->whereIn('id', $categoryList);
        }

        $data['xml_data'] = [
            'record'        => $categories->get()->count(), 
            'cat_parent'    => $category,
            'total_record'  => $count,
        ];
        $categories = $categories->orderBy('weight', 'desc')->orderBy('category_name', 'asc')->get();
        
        foreach ($categories as $category)
        {
            switch (input::get('lang'))
            {
                case 'CN':
                case 'cn':
                    $language = '_cn';
                    break;
                case 'MY':
                case 'my':
                    $language = '_my';
                    break;
                default:
                    $language = '';
                    break;
            }

            if ( ! empty($category->{"category_name{$language}"}))
            {
                $categoryName = $category->{"category_name{$language}"};
            }
            else
            {
                $categoryName = $category->category_name;
            }

        if ( ! isset($categoryName))
            {
                $categoryName = $category->category_name;
            }
        $childCategories = ApiProduct::fetchCategoryTree($category->id);
        $regex = $category->id;

            foreach ($childCategories as $child)
            {
                $regex = "{$regex}|{$child['id']}";
            }
        if (isset($city) && ! empty($city))
            {
                $productsCount = Product::join('jocom_product_delivery', 'jocom_product_delivery.product_id', '=', 'jocom_product_and_package.id')
                ->join('jocom_zone_cities', 'jocom_zone_cities.zone_id', '=', 'jocom_product_delivery.zone_id')
                ->where('status', '=', 1)
                ->where(function($categories) use ($regex)
                {
                    $categories->where('category', 'REGEXP', "(^|,)({$regex})(,|$)");
                })
                ->where('jocom_zone_cities.city_id', '=', $city)
                ->count();
            }
            else 
            {
                $productsCount = Product::where('status', '=', 1)
                ->where(function($categories) use ($regex)
                {
                    $categories->where('category', 'REGEXP', "(^|,)({$regex})(,|$)");
                })
                ->count();
            }

            $categoryImage = ( ! empty($category->category_img)) ?
            url("images/category/{$category->category_img}") : '';
            
            $categoryImagebanner = ( ! empty($category->category_img_banner)) ?
                url("images/category/{$category->category_img_banner}") : '';

            $data['xml_data']['item'][] = [
                        'id'        => $category->id,
                        'cat_name'  => $categoryName,
                        'cat_icon'  => $categoryImage,
                        'cat_banner'  => $categoryImagebanner,
                        'p_count'   => $productsCount,
                        'sub_cat'   => (count($childCategories)) ? 1 : 0,
                        ];
        }
         
          return json_encode($data);
        
    }
    
    public function anyProductlite(){
        $limit  = Input::get('count', 250);
        $offset = Input::get('from', 0);
        $apitype = Input::get('api_type', 'lite');

        if($apitype === 'original')
            $data = ApiProduct::fetch_product($limit, $offset, Input::all());
        elseif($apitype === 'details')
            $data = ApiProduct::fetch_ProductDetails(Input::all());
        else
            $data = ApiProduct::fetch_product_lite($limit, $offset, Input::all());

        return Response::json($data);
    }
    
    public function anyHelpcenter(){
        
          $data=array();
          $validator = Validator::make(Input::all(), ApiHelpCenter::$apirule, ApiHelpCenter::$apimessage);
         if ($validator->passes())
         {
          $username=Input::get('username');
          $order_id=Input::get('order_id');
          $query_topic=Input::get('query_topic');
          $description=Input::get('description');
          $email=Input::get('email');
          $contact_number=Input::get('contact_number');
          $image_url="";
             if (Input::hasFile("image")) {
                $unique = time();
                $image = Input::file("image");
                $destinationPath = 'public/media/images/helpcenter';
                $imgFilename= $order_id. "-helpcenter-" . $unique . '.' . $image->getClientOriginalExtension();
                $image->move($destinationPath, $imgFilename);
                $image_url=$destinationPath.'/'.$imgFilename;
              }
            $helpcenterdata=new ApiHelpCenter;
            $helpcenterdata->username=$username;
            $helpcenterdata->order_id=$order_id;
            $helpcenterdata->query_topic=$query_topic;
            $helpcenterdata->description=$description;
            $helpcenterdata->email=$email;
            $helpcenterdata->contact_number=$contact_number;
            $helpcenterdata->image_attached=$image_url;
            $helpcenterdata->status="1";
            $helpcenterdata->updated_by="API Update";
            $helpcenterdata->created_at=date('Y-m-d H:i:s');
            
            if($helpcenterdata->save()){
                
                $user = array(
                            'email' => 'enquiries@tmgrocer.com', //$email,
                            'name'  => 'App Feedback',
                        );
                $body_data=array(
                    'username' => $username,
                    'regarding'=>$query_topic,
                    'order_id'=>$order_id,
                    'email'=>$email,
                    'contact_number'=>$contact_number,
                    'description'=>$description,
                    'view'=>url('/helpcenter/view/'.$helpcenterdata->id),
                    );
                  Mail::send('emails.helpcenter',$body_data, function($message) use ($user)
                        {
                            $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                            $message->to($user['email'], $user['name'])->subject('[tmGrocer APP]:Regarding tmGrocer App Help Center Request');
                        });
            if( count(Mail::failures()) > 0) {
                 $data['status']="failed";
                 $data['code']  ="3";
                 $data['message']="Mail submission Failed";  
            }else{
                 $data['status']="success";
                 $data['code']  ="1";
                 $data['message']="Sucessfully Submitted";  
            }

                
            }else{
                 $data['status']="failed";
                 $data['code']  ="4";
                 $data['message']="Invaild Request !Please Try Again";
            }
            
         }
         else{
             $data['status']="failed";
             $data['code']  ="0";
             $data['message']=$validator->errors();
             
          }
          return json_encode($data);
    }
    public function anyCheckoutcoupon(){
        $data=array();
        $today=date("Y-m-d");
        $coupon_static=DB::table('jocom_static_coupon as sc')
                                   ->select('sc.id','sc.coupon_id','sc.coupon_code', 'sc.coupon_amount','sc.coupon_amount_type','sc.description','sc.status')
                                   ->join('jocom_coupon as jc ','sc.coupon_id','=','jc.id')
                                   ->where('jc.status','=','1')
                                   ->where('sc.status','=','1')
                                   ->whereDate('sc.from_date', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.to_date', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
        if($coupon_static){
            foreach($coupon_static as $value){
              $coupon[]=array('coupon_code'=>$value->coupon_code,'coupon_amount'=>$value->coupon_amount,'coupon_description'=>$value->description);
            }
            $data['response']=$coupon;
            $data['status']='1';
            $data['message']='success';
        }else{
            $data['response']='';
            $data['status']='0';
            $data['message']='No Coupons Available';
        }
        return json_encode($data);
    }
    public function anyUpdatfaveaddress()
    {
        $transaction_id=Input::get('transaction_id');
        $address_id=Input::get('id');
        $addr = FavouriteAddress::where('id', '=',$address_id)->first();
        $returnData=array();
        if ($addr->city != null or $addr->city != '') {
            $error = false;
            // Get Country
                $country_row =Country::select('id','name')
                    ->where('id', '=', $addr->delivercountry)->first();

                // Get State
                $state_row = CountryState::select('*')
                            ->where('id', '=',$addr->state)
                             ->where('country_id', '=',$addr->delivercountry)->first();
                // Get Zone
                $check_zone = [];
                $city_name  = '';
                $city_id    = 0;
                    // Get Cities
                    $city_row = City::select('id','name')
                        ->where('id', '=', $addr->city)
                        ->where('state_id', '=', $addr->state)->first();
                  
                    if (count($city_row) > 0) {
                        $city_name = $city_row->name;
                        $city_id   = $city_row->id;
                    }

                    // Get Zone
                    $zone = DB::table('jocom_zone_cities')
                        ->select('id','zone_id')
                        ->where('city_id', '=',$addr->city)->get();
                } else {
                    // Get Cities
                    $city_row = '1';

                    // Get Zone
                    $zone = DB::table('jocom_zone_states')
                        ->select('id','zone_id')
                        ->where('states_id', '=', $addr->state)->get();
                }

                if ($zone == null) {
                    $error = true;
                    $zone_query == null;
                    $returnData['message'] = 'Invalid location selected.';
        
                } else {
                     foreach ($zone as $zone_row) {
                        $check_zone[] = $zone_row->zone_id;
                    }
                    if (sizeof($check_zone) == 0) {
                        $check_zone[] = 0;
                    }
                    $zone_query = DB::table('jocom_zones')
                        ->select('id')
                        ->where('country_id', '=', $addr->delivercountry)
                        ->whereIn('id', $check_zone)
                        ->get();
                }

                if ($country_row == null) {
                    $error                 = true;
                    $returnData['message'] = 'Invalid country.';
                } elseif ($state_row == null) {
                    $error                 = true;
                    $returnData['message'] = 'Invalid state.';
                } elseif ($city_row == null) {
                    $error                 = true;
                    $returnData['message'] = 'Invalid city.';
                } elseif ($zone_query == null) {
                    $error                 = true;
                    $returnData['message'] ='Invalid location selected.';
                }
                if($error===false){
                    $buyer_zone = [];
                    foreach ($zone_query as $zone_row) {
                        $buyer_zone[] = $zone_row->id;
                    }
                    $transaction_details = TDetails::where('transaction_id', '=',$transaction_id)->get();
                    foreach($transaction_details as $values){
                         $dl_row = DB::table('jocom_product_delivery')
                                  ->select('*')
                                  ->where('product_id', '=', $values->product_id)
                                  ->whereIn('zone_id', $buyer_zone)
                                  ->first();
                    if ($dl_row == null||$dl_row=="") {
                       $error = true;
                       $productname.='</br> '.$values->product_name.'';
                    }
                    }
                    $returnData['message']='Invalid request.(Selected product is not available to your location.)'.$productname;
                }
                
                   
        if (count($addr) > 0 && $error===false)
        {
                $city_name = "";
                $city_name = City::select('name')->find($addr->city);

                $state_name = "";
                $state_name = State::select('name')->find($addr->state);

                $country_name = "";
                $country_name = Country::select('name')->find($addr->delivercountry);

             $transaction = Transaction::find($transaction_id);
             $transaction->delivery_name=$addr->delivername;
             $transaction->delivery_contact_no=$addr->delivercontactno;
             $transaction->special_msg=$addr->specialmsg;
             $transaction->delivery_addr_1=$addr->deliveradd1;
             $transaction->delivery_addr_2=$addr->deliveradd2;
             $transaction->delivery_postcode=$addr->deliverpostcode;
             $transaction->delivery_city=$city_name->name;
             $transaction->delivery_city_id=$addr->city;
             $transaction->delivery_state=$state_name->name;
             $transaction->delivery_state_id=$addr->state;
             $transaction->delivery_country=$country_name->name;
             
             if($transaction->save()){
                  $data = [
                    'addr_id'           => $addr->id,
                    'delivername'       => $addr->delivername,
                    'delivercontactno'  => $addr->delivercontactno,
                    'specialmsg'        => $addr->specialmsg,
                    'deliveradd1'       => $addr->deliveradd1,
                    'deliveradd2'       => $addr->deliveradd2,
                    'deliverpostcode'   => $addr->deliverpostcode,
                    'city'              => $addr->city,
                    'city_name'         => $city_name->name,
                    'state'             => $addr->state,
                    'state_name'        => $state_name->name,
                    'delivercountry'    => $addr->delivercountry,
                    'country_name'      => $country_name->name,
                    'default_list'      => $addr->default_list,
                    'message'           =>'',
                    'error'             =>'0',
                ];
                return Response::json($data);
             }
             else
             {
              $data = ['addr_id'=>0];
            
               return Response::json($data);
             }
             
           }else{
            $data = ['message'=>$returnData['message'],'error'=>'1'];
            return Response::json($data);
           }
       
    }

    public function anyWebcheckoutcoupon(){
        
        $username=Input::get('username');
        $data=array();
        $today=date("Y-m-d");
        $coupon_static=DB::table('jocom_static_coupon as sc')
                                   ->select('sc.id','sc.coupon_id','sc.coupon_code', 'sc.coupon_amount','sc.coupon_amount_type','sc.description')
                                   ->join('jocom_coupon as jc ','sc.coupon_id','=','jc.id')
                                   ->where('jc.status','=','1')
                                   ->where('sc.status','=','1')
                                   ->whereDate('sc.from_date', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.to_date', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
                    $CustomerInfo = Customer::where('username',$username)->first();
                   if($CustomerInfo->preferred_member==1){
                     $coupons=DB::table('jocom_coupon as sc')
                                   ->select('sc.id','sc.id as coupon_id','sc.coupon_code', 'sc.amount as coupon_amount','sc.amount_type as coupon_amount_type','sc.name as description')
                                   ->where('sc.status','=','1')
                                   ->where('sc.is_preferred_member','=','1')
                                   ->whereDate('sc.valid_from', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.valid_to', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
                                  
                                   if(!empty($coupons)){
                                       $checkoutcoupon=array_merge($coupon_static,$coupons); 
                                       $coupon_static=$checkoutcoupon;
                                    }
                    }
                    
        if($coupon_static!=""){
            foreach($coupon_static as $value){
                 if($value->coupon_code=='PMPFD10'){
                                            $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                            $CustomerInfo = Customer::where('username',$username)->first();
                                            $available_count='<br/>Minimum spend amount: RM'.$minmumcheck->min_purchase.'<br/>Coupon left:'.$CustomerInfo->membership_delivery;
                                           }else if($value->coupon_code=='PMP18'){
                                              $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                              $CustomerInfo = Customer::where('username',$username)->first();
                                            $available_count='<br/>Minimum spend amount: RM'.$minmumcheck->min_purchase.'<br/>Coupon left:'.$CustomerInfo->member_disc_1; 
                                           }else if($value->coupon_code=='PMP25'){
                                             $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                             $CustomerInfo = Customer::where('username',$username)->first();
                                            $available_count='<br/>Minimum spend amount: RM'.$minmumcheck->min_purchase.'<br/>Coupon left:'.$CustomerInfo->member_disc_2;   
                                           }else{
                                             $available_count='';  
                                           }
                                           
              $coupon[]=array('coupon_code'=>$value->coupon_code,'coupon_amount'=>$value->coupon_amount,'coupon_description'=>$value->description.$available_count);
            }
            $data['response']=$coupon;
            $data['status']='1';
            $data['message']='success';
        }else{
            $data['response']='';
            $data['status']='0';
            $data['message']='No Coupons Available';
        }
        return json_encode($data);
    }
    
    public function anyCoupontest(){
        $username=Input::get('username');
        $data=array();
        $today=date("Y-m-d");
        $coupon_static=DB::table('jocom_static_coupon as sc')
                                   ->select('sc.id','sc.coupon_id','sc.coupon_code', 'sc.coupon_amount','sc.coupon_amount_type','sc.description')
                                   ->join('jocom_coupon as jc ','sc.coupon_id','=','jc.id')
                                   ->where('jc.status','=','1')
                                   ->where('sc.status','=','1')
                                   ->whereDate('sc.from_date', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.to_date', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
                    $CustomerInfo = Customer::where('username',$username)->first();
                   if($CustomerInfo->preferred_member==1){
                     $coupons=DB::table('jocom_coupon as sc')
                                   ->select('sc.id','sc.id as coupon_id','sc.coupon_code', 'sc.amount as coupon_amount','sc.amount_type as coupon_amount_type','sc.name as description')
                                   ->where('sc.status','=','1')
                                   ->where('sc.is_preferred_member','=','1')
                                   ->where('sc.username','=','')
                                   ->whereDate('sc.valid_from', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.valid_to', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
                    $usercoupons=DB::table('jocom_coupon as sc')
                                   ->select('sc.id','sc.id as coupon_id','sc.coupon_code', 'sc.amount as coupon_amount','sc.amount_type as coupon_amount_type','sc.name as description')
                                   ->where('sc.status','=','1')
                                   ->where('sc.is_preferred_member','=','1')
                                   ->where('sc.username','=',$username)
                                   ->whereDate('sc.valid_from', '<=', date_format(date_create($today), 'Y-m-d'))
                                   ->whereDate('sc.valid_to', '>=', date_format(date_create($today), 'Y-m-d'))
                                   ->orderBy('sc.id','DESC')
                                   ->get();
                                  $users_totalcoupon=array_merge($coupons,$usercoupons);
                                   if(!empty($users_totalcoupon)){
                                       $checkoutcoupon=array_merge($coupon_static,$users_totalcoupon); 
                                       $coupon_static=$checkoutcoupon;
                                    }
                    }
                    
        if($coupon_static!=""){
            foreach($coupon_static as $value){
                 if($value->coupon_code=='PMPFD10'){
                                            $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                            $CustomerInfo = Customer::where('username',$username)->first();
                                            $available_count='<br/>Minimum spend amount: RM'.$minmumcheck->min_purchase.'<br/>Coupon left:'.$CustomerInfo->membership_delivery;
                                           }else if($value->coupon_code=='PMP18'){
                                              $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                              $CustomerInfo = Customer::where('username',$username)->first();
                                            $available_count='<br/>Minimum spend amount: RM'.$minmumcheck->min_purchase.'<br/>Coupon left:'.$CustomerInfo->member_disc_1; 
                                           }else if($value->coupon_code=='PMP25'){
                                             $minmumcheck=Coupon::where("coupon_code",$value->coupon_code)->first();
                                             $CustomerInfo = Customer::where('username',$username)->first();
                                            $available_count='<br/>Minimum spend amount: RM'.$minmumcheck->min_purchase.'<br/>Coupon left:'.$CustomerInfo->member_disc_2;   
                                           }else{
                                             $available_count='';  
                                           }
                                           
              $coupon[]=array('coupon_code'=>$value->coupon_code,'coupon_amount'=>$value->coupon_amount,'coupon_description'=>$value->description.$available_count);
            }
            $data['response']=$coupon;
            $data['status']='1';
            $data['message']='success';
        }else{
            $data['response']='';
            $data['status']='0';
            $data['message']='No Coupons Available';
        }
        return json_encode($data);
    }
    public function anyBirthdayreward()
   {
    $TUser=DB::table('jocom_user')->select('username','dob','email','firstname','lastname')->where('preferred_member','=',1)->where(DB::raw('MONTH(`dob`)= MONTH(NOW()) and DAY(`dob`) = DAY(NOW());'))->get();
    $enddate=date('Y-m-d', strtotime(date('Y-m-d'). ' + 29 days'));

    if($TUser){
        foreach($TUser as $value){
            
        $user=DB::table('jocom_user')
        ->select('id','email','full_name')
        ->where('username', '=',$value->username)
        ->where('preferred_member','=',1)
        ->first();
        
        $users_detail = array(
        'email' =>$user->email,
        'name' => $user->full_name,
        );

        $existing=DB::table('jocom_coupon')->select('coupon_code')->where('username','=',$value->username)->where('name','=','Jocom Preferred Member RM 88 Off Birthday Reward Voucher')->where('status','=',1)->first();
            if($user && !$existing){
            $code_r=str_random(6);
            $code_u=strtoupper($code_r);
            $couponCode =$code_u;
            $couponname='Jocom Preferred Member RM 88 Off Birthday Reward Voucher';
            $coupon = new Coupon;
            $coupon->coupon_code =$couponCode;
            $coupon->name = $couponname;
            $coupon->username =$value->username;
            $coupon->amount =88;
            $coupon->amount_type ='Nett';
            $coupon->status ='1';
            $coupon->min_purchase =350;
            $coupon->max_purchase = '0';
            $coupon->valid_from =date("Y-m-d");
            $coupon->valid_to =$enddate;
            $coupon->type ='all';
            $coupon->qty ='1';
            $coupon->q_limit ='Yes';
            $coupon->cqty ='1';
            $coupon->c_limit ='Yes';
            $coupon->free_delivery ='0';
            $coupon->free_process ='0';
            $coupon->boost_payment ='0';
            $coupon->is_jpoint='1';
            $coupon->is_preferred_member='1';
            $coupon->region=null;
            $coupon->razerpay_payment ='0';
            $coupon->insert_by ='API CREATE';
            $coupon->insert_date = date("Y-m-d h:i:sa");
            
            $coupon->modify_by ='';
            $coupon->modify_date ='';
            
            if($coupon->save()){
            $body_data=array(
            'username' => $user->full_name,
            'coupon_code' => $coupon->coupon_code);
            
            if(!empty($coupon->coupon_code)){
            Mail::send('emails.preferred_member_birthday',$body_data, function($message) use ($users_detail)
            {
            $message->from('customersupport@tmgrocer.com', 'tmGrocer');
            $message->to($users_detail['email'],$users_detail['name'])->subject('[tmGrocer]:Congratulations! You have Received Birthday
            Reward Voucher worth to RM88.');
            });
            }
            
            }
         }
      }
    }
}

    public function anyPendingorders(){
        $data=array();
        
        $transactionId = Input::get('transID');
        if($transactionId){
            $trans = Transaction::where('id',$transactionId)
                            ->where('status','pending')
                            ->where('gobarakah_mailstatus',0)
                            ->first();
                            
            if (isset($trans) && ! empty($trans)){
                
            //Order details Start 
                
                    $body    = "
                    <u>Your order details:</u><br>
                    Name: {$trans->delivery_name}<br>
                    Contact No: {$trans->delivery_contact_no}<br>
                    Address 1: {$trans->delivery_addr_1}<br>
                    Address 2: {$trans->delivery_addr_2}<br>
                    State: {$trans->delivery_state}<br>
                    Postcode: {$trans->delivery_postcode}<br>
                    Country: {$trans->delivery_country}<br><br>
                    Special Message: {$trans->special_msg}<br><br>
                    Buyer Email: {$trans->buyer_email}<br><br>
                    <table border=1 class=item>
                    <tr>
                        <td class=item><b>SKU</b></td>
                        <td class=item><b>Item Name</b></td>
                        <td class=item><b>Label</b></td>
                        <td class=item><b>Price</b></td>
                        <td class=item><b>QTY</b></td>
                        <td class=item><b>Delivery Time</b></td>
                    </tr>";
        
                $pro_body        = [];
                $seller_username = [];
                $seller_po       = [];
                $seller_popath   = [];   
                $seller_inv      = [];
                $dquery          = TDetails::where('transaction_id', '=', $trans->id)->get();
        
                foreach ($dquery as $drow) {
                    $prow = Product::where('sku', '=', $drow->sku)->first();
                    $temp_price = number_format($drow->price, 2, '.', '')+number_format($drow->price*$drow->gst_rate_item/100, 2, '.', '');
        
                    if ( ! isset($pro_body[$prow->sell_id])) {
                        $pro_body[$prow->sell_id] = '';
                    }
                    
                    // Convert Qty Decimal
                    if( is_numeric( $drow->unit ) && floor( $drow->unit ) != $drow->unit){  
                        $qty = $drow->unit; 
                    } else { 
                        $qty = (integer)$drow->unit; 
                    } 
                    // Convert Qty Decimal
        
                    if ($row->no_shipping == 1) {                
                        $pro_body[$prow->sell_id] .= "
                            <tr>
                                <td class=item>{$drow->sku}</td>
                                <td class=item>".(isset($prow->name) ? $prow->name : '')."</td>
                                <td class=item>".(isset($drow->price_label) ? $drow->price_label : '')."</td>
                                <td class=item>".$temp_price."</td>
                                <td class=item>{$qty}</td>
                            </tr>";
                    } else {
                        $pro_body[$prow->sell_id] .= "
                            <tr>
                                <td class=item>{$drow->sku}</td>
                                <td class=item>".(isset($prow->name) ? $prow->name : '')."</td>
                                <td class=item>".(isset($drow->price_label) ? $drow->price_label : '')."</td>
                                <td class=item>".$temp_price."</td>
                                <td class=item>{$qty}</td>
                                <td class=item>{$drow->delivery_time}</td>
                            </tr>";
                    }
        
                    if ($drow->parent_po != '') {
                        $drow_po = $drow->parent_po;
                        $popath  = Config::get('constants.PO_PARENT_PDF_FILE_PATH').'/';
                    } else {
                        $drow_po = $drow->po_no;
                        $popath  = Config::get('constants.PO_PDF_FILE_PATH').'/';
                    }
        
                    $sell_id[]                      = $prow->sell_id;
                    $seller_po[$prow->sell_id]      = $drow_po;
                    $seller_popath[$prow->sell_id]  = $popath;
                    $seller_inv[$prow->sell_id]     = $drow->s_inv_no;
                    foreach(DB::table('language')->lists('code') AS $lang) Cache::forget('prode_JC' . $drow->product_id . '_' . strtoupper($lang)); // Purge API product details cache
                }
                //Added by Maruthu --- Begin
                
                
               
                
                $totalamount=number_format(($trans->gst_total+$trans->total_amount)-($couponcode+$jcashback), 2, '.', '');        
                // $amountintext=MCheckout::convert_number_to_words($totalamount);
                $total_amount_text="Total Amount  :<strong> RM {$totalamount}</strong><br>";
                    
                   
        
                if ($row->no_shipping == 1) {
                    $seller_mail_body1 = "
                    Buyer Email: {$row->buyer_email}<br><br>
                    <table border=1 class=item>
                    <tr>
                        <td class=item><b>SKU</b></td>
                        <td class=item><b>Item Name</b></td>
                        <td class=item><b>Label</b></td>
                        <td class=item><b>Price</b></td>
                        <td class=item><b>QTY</b></td>
                    </tr>";
                } else {
                    $seller_mail_body1 = $body;
                }
        
                if ($trans->no_shipping == 1) {
                    $seller_mail_body2 = "
                        </table>
                        <br><br>
                        The transaction ID will be {$trans->id} for references.<br>
                        <br>";
                } else {
                    $seller_mail_body2 = "
                        </table>
                        <br><br>
                        {$total_amount_text}
                        <br>
                        The transaction ID will be <strong>{$trans->id}</strong> for references.<br>
                        <br>";
                }
        
                $notify_body = $seller_mail_body1.implode(' ', $pro_body).$seller_mail_body2;
                $data        = ['notify_body' => $notify_body,
                                'delivery_name' => $trans->delivery_name,
                                'gobarakah_voucher' => $trans->gobarakah_voucher
                                ];
                
                // array_push($trans,$data);
                
                //Order details End
                
                
                // $mail = ['maruthu@jocom.my', 'maruthujocom@jocom.my'];
                $subject ='Pending Order Notification : Txn No '.$trans->id;
               
                Mail::send('emails.gobarakah_orderpending', $data, function($message) use ($trans,$subject)
                    {
                        $message->from('payment@tmgrocer.com', 'tmGrocer');
                        $message->to($trans->buyer_email)->cc('tmgrocer_order@gobarakah.com')->cc('gobarakahorders@tmgrocer.com')
                        ->subject($subject);
                    });
                
                $transaction = Transaction::find($trans->id);
                $transaction->gobarakah_mailstatus =1;
                $transaction->save();
                
            }
            
            
        }
     
         
        
    }
    
}
