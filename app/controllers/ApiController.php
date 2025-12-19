<?php
use Helper\ImageHelper as Image;
class ApiController extends BaseController
{
    private static $month_data = [ 'JAN' => 1, 'FEB' => 2, 'MAR' => 3, 'APR' => 4, 'MAY' => 5, 'JUN' => 6, 'JUL' => 7, 'AUG' => 8, 'SEP' => 9, 'OCT' => 10, 'NOV' => 11, 'DEC' => 12 ];
    private static $week_day = ['SUN' => 'Sunday', 'MON' => 'Monday', 'TUE' => 'Tuesday', 'WED' => 'Wednesday', 'THU' => 'Thursday', 'FRI' => 'Friday', 'SAT' => 'Saturday'];
    
    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyMember()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

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
            case 'wavpay_register':
				$api  = new Api;
				$data = array_merge($data, $api->RegisterWavPayMember(Input::all()));
				break;
            default:
                $tmpdata = array(
                    'status'     => '0',
                    'status_msg' => '#805',
                );
                $data = array_merge($data, array('xml_data' => $tmpdata));
                break;
        }

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }

    public function anyForgot()
    {
        $api         = new Api;
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, $api->MemberForgot(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }
    
    
    public function getComment() {
        $where = 'WHERE ' . (Input::get('product_id') && ctype_digit(Input::get('product_id')) ? 'com.product_id = ' . Input::get('product_id') . ' AND ' : '') . (Input::get('lang') && in_array(Input::get('lang'), ['CN', 'MY', 'EN']) ? 'com.lang = "' . Input::get('lang') . '" AND ' : '') . 'com.status = 1';
        $limit = (Input::get('limit') && ctype_digit(Input::get('limit')) ? 'LIMIT ' . Input::get('limit') . (Input::get('page') && ctype_digit(Input::get('page')) ? ' OFFSET ' . ((int)Input::get('limit') * (int)Input::get('page')) : '') : '');
        $query = DB::select("
            SELECT com.id, user.full_name AS full_name, user.username AS user_name, com.product_id, com.comment_date, com.comment, com.image, com.rating, com.lang 
            FROM jocom_comments AS com
            LEFT JOIN jocom_user AS user ON user.id = com.user_id
            $where
            ORDER BY comment_date DESC
            $limit
        ");

        return Response::json([
            'timestamp' => (count($query) ? $query[0]->comment_date : date('Y-m-d h:i:s')),
            'record' => count($query),
            'tot_record' => count($query),
            'image_path' => url('/') . '/' . Config::get('constants.COMMENT_IMG_PATH'),
            'item' => $query,
        ]);
    }
    
    public function postComment() {
        $username = trim(Input::get('username')); // Buyer Username
        $product_id = trim(Input::get('product_id')); // Product SKU
        if(!$username || !$product_id) return Response::json(['status' => '0', 'status_msg' => '#301']);

        $user = DB::table('jocom_user')->select('id')->where('username', '=', $username)->first();
        $product = DB::table('jocom_products')->select('id')->where('id', '=', $product_id)->first();

        if(!$user) return Response::json(['status' => '0', 'status_msg' => '#301']);
        if(!$product) return Response::json(['status' => '0', 'status_msg' => '#302']);
        $lang = (in_array(Input::get('lang'), ['CN', 'MY', 'EN']) ? Input::get('lang') : 'EN');
        $rating = (ctype_digit(Input::get('rating')) ? (int)trim(Input::get('rating')) : 0);

        $id = DB::table('jocom_comments')->insertGetId([
            'comment_date'  => date('Y-m-d H:i:s', time()),
            'user_id'       => $user->id,
            'product_id'    => $product->id,
            'comment'       => trim(htmlentities(strip_tags(Input::get('comment')), ENT_QUOTES)), // Comment
            'rating'        => $rating <= 5 && $rating >= 0 ? $rating : 0, // Comment Rating (0-5) default 0
            'lang'          => $lang,
            'insert_by'     => 'CMS',
            'insert_date'   => date('Y-m-d H:i:s'),
            'modify_date'   => date('Y-m-d H:i:s'),
            'status'        => 0,
        ]);

        if(count(Input::file('image')) && isset(Input::file('image')[0]) && Input::file('image')[0]){
            $img = [];
            if(!file_exists('./' . Config::get('constants.COMMENT_IMG_PATH'))) mkdir('./' . Config::get('constants.COMMENT_IMG_PATH'), 0755, true);
            foreach (Input::file('image') as $image) {
                if(!in_array(strtolower($image->getClientOriginalExtension()), ['png', 'jpg', 'gif', 'jpeg']) || $image->getSize() > 1000000) continue;
                $filename = $id . '_' . md5(openssl_random_pseudo_bytes(4)) . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(Config::get('constants.COMMENT_IMG_PATH'), $filename);
                $img[] = $filename;
            }

            DB::table('jocom_comments')->where('id', $id)->update(['image' => json_encode($img)]);
        }

        // clear these product cache prevent issue
        Cache::forget('prode_JC' . $product_id . '_' . $lang);
        return Response::json(['status' => '1', 'status_msg' => '#303']);
    }
    
    // public function anyComments()
    // {
    //     $data    = array();
    //     $xmldata = array();
    //     $get     = array();
    //     $enc     = 'UTF-8';

    //     if (Input::get('enc')) {
    //         $enc = trim(Input::get('enc'));
    //     }

    //     $data['enc'] = 'UTF-8';

    //     $username = trim(Input::get('username')); // Buyer Username
    //     $sku      = trim(Input::get('sku')); // Product SKU

    //     $user = DB::table('jocom_user')->select('id')
    //         ->where('username', '=', $username)
    //         ->first();

    //     $product = DB::table('jocom_products')->select('id')
    //         ->where('sku', '=', $sku)
    //         ->first();

    //     $data['user_id']    = $user->id;
    //     $data['product_id'] = $product->id;

    //     $data['comment']   = trim(Input::get('comment')); // Comment
    //     $data['rating']    = trim(Input::get('rating')); // Comment Rating (0-5) default 0
    //     $data["insert_by"] = "phone_app";

    //     if (!is_numeric($data['rating']) && $data['rating'] < 1 && $data['rating'] > 5) {
    //         $data['rating'] = 0;
    //     }

    //     if ($data['user_id'] === false) {
    //         $xmldata['status']     = '0';
    //         $xmldata['status_msg'] = '#301';
    //     } elseif ($data['product_id'] === false) {
    //         $xmldata['status']     = '0';
    //         $xmldata['status_msg'] = '#302';
    //     } else {
    //         $id = DB::table('jocom_comments')->insertGetId(array(
    //             'comment_date' => date('Y-m-d H:i:s', time()),
    //             'user_id'      => $data['user_id'],
    //             'product_id'   => $data['product_id'],
    //             'comment'      => $data['comment'],
    //             'rating'       => $data['rating'],
    //             'insert_by'    => $data["insert_by"],
    //             'insert_date'  => date('Y-m-d H:i:s'),
    //             'modify_date'  => date('Y-m-d H:i:s'))
    //         );

    //         $xmldata['status']     = '1';
    //         $xmldata['status_msg'] = '#303';
    //     }

    //     return Response::view('xml_v', array('enc' => $enc, 'xml_data' => $xmldata))
    //         ->header('Content-Type', 'text/xml')
    //         ->header('Pragma', 'public')
    //         ->header('Cache-control', 'private')
    //         ->header('Expires', '-1');
    // }

    public function anyUpdateprofile()
    {
        $api         = new Api;
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, $api->updateprofile(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }

    public function anyCategory()
    {
        $limit  = Input::get('count', 50);
        $offset = Input::get('from', 0);

        $data = ['enc' => 'UTF-8'];
        $data = array_merge($data, ApiProduct::fetch_category($limit, $offset, Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
        
    }

    public function anyProduct()
    {
        $limit  = Input::get('count', 250);
        $offset = Input::get('from', 0);

        $data = ['enc' => 'UTF-8'];
        $data = array_merge($data, ApiProduct::fetch_product($limit, $offset, Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }
    
    public function anyProductios()
    {
        $limit  = Input::get('count', 250);
        $offset = Input::get('from', 0);

        $data = ['enc' => 'UTF-8'];
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'API_PRODUCT_IOS_New';
        $ApiLog->data = print_r(Input::all());
        $ApiLog->save();
        
        $data = array_merge($data, ApiProduct::fetch_product_ios($limit, $offset, Input::all()));
        
        // return json_encode($data);
        
        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }
    
    public function anyProductsku()
    {
        $limit  = Input::get('count', 250);
        $offset = Input::get('from', 0);

        $data = ['enc' => 'UTF-8'];
        $data = array_merge($data, ApiProduct::fetch_product($limit, $offset, Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
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
                    ->whereRaw('LOCATE(1347, jp.category) = 0') // exclude wavpay product result
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

        $data = ['enc' => 'UTF-8'];
        $data = array_merge($data, Feedback::fetch_feedback($limit, $offset, Input::all()));
        
        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

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
        // print_r(Input::all());
        $from = Input::get('from');
        // $to = Input::get('to');
        // die();
        
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
        
        // echo "<pre>";
        // print_r($flash);
        // echo "</pre>";

        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if (!isset($newArray[$v->id]) ) {

                $newArray[$v->id] = array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
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

            $newArray[$v->id]['item'][] = array(
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
        }
        
        $data['xml_data']['batch'] = $newArray;
        $data['enc'] = 'UTF-8';
        // print_r($newArray);
        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }
    
    

    public function anyExclusivecorner(){
        // print_r(Input::all());
        $from = Input::get('from');
        // $to = Input::get('to');
        // die();
        
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
        
        // echo "<pre>";
        // print_r($flash);
        // echo "</pre>";

        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if (!isset($newArray[$v->id]) ) {

                $newArray[$v->id] = array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
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

            $newArray[$v->id]['item'][] = array(
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
        }
        
        $data['xml_data']['batch'] = $newArray;
        $data['enc'] = 'UTF-8';

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }


    public function anyCombodeals(){
        // print_r(Input::all());
        $from = Input::get('from');
        // $to = Input::get('to');
        // die();
        
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
        
        // echo "<pre>";
        // print_r($flash);
        // echo "</pre>";

        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if (!isset($newArray[$v->id]) ) {

                $newArray[$v->id] = array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
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

            $newArray[$v->id]['item'][] = array(
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
        }
        
        $data['xml_data']['batch'] = $newArray;
        $data['enc'] = 'UTF-8';

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }
    
    public function anyDynamicsale(){
        // print_r(Input::all());
        $from = Input::get('from');
        // $to = Input::get('to');
        // die();
        
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
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
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
                    'JF.rule_name',
                    'JF.valid_from',
                    'JF.valid_to',
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
        
        // echo "<pre>";
        // print_r($flash);
        // echo "</pre>";

        foreach ($flash as $v) 
        {
            $newArray2 = array();

            if (!isset($newArray[$v->id]) ) {

                $newArray[$v->id] = array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
                        'rule_name' =>$v->rule_name,
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

            $newArray[$v->id]['item'][] = array(
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
        }
        
        $data['xml_data']['batch'] = $newArray;
        $data['enc'] = 'UTF-8';

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

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

                $newArray[$v->id] = array(
                        'valid_from' => $v->valid_from, 
                        'valid_to' => $v->valid_to,
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

            $newArray[$v->id]['item'][] = array(
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
        $data['enc'] = 'UTF-8';

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

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

         $data['enc'] = 'UTF-8';

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

         return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }
    
    public function anyRewardreferrer(){
        $data        = array();
        $userid = 0;
        $username = '';
        $refercode = '';
        $referrerurl = '';



        $data['enc'] = 'UTF-8';
       

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

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');


    }
    
    public function anyReferrersettings(){
        $data        = array();
        $points = 0;
        $rtype = '';
        $status = 1;
        
        $data['enc'] = 'UTF-8';

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

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }
    
    public function anyReferrergenerate()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }


        $data = array_merge($data, self::createReferrer(Input::all()));
        
        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
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
    
    /**
     * Comment: Optimise Version that reduce DB load + check content if DB 
     *          have record else using php file read method (php file 
     *          read is ineffective, when come to big file)
     *
     * @api TRUE
     * @author ???
     * @since ???
     * @param string tag_data ['data_only', 'full_data'], For get the the banner type of flash sales
     * @version 1.2
     * @return JSON data
     *
     * Last Update: 17 FEB 2023
     */
    public function anyPosts($page = 1, $limit = 10){
        $tag_d = Input::get('tag_data', 'full_data');
        $page = ((int)$page > 0 ? (int)$page : 1) - 1;
        $limit = ((int)$limit > 0 ? (int)$limit : 10);
        $offset = 0;
        $dataCollection = [];
        $max_length = 500;
        
        try{
            $totalPage = ceil(DB::table('jocom_blog_posts')->where('jocom_blog_posts.status', 1)->where('jocom_blog_posts.activation', 1)->count() / $limit);
            $currentPage = $page + 1;
            $nextPage = (($currentPage + 1) > (int)$totalPage ? 0 : $currentPage + 1);
            $previousPage = $currentPage - 1;
            
            $post = DB::table('jocom_blog_posts')
                ->select('jocom_blog_posts.id', 'jocom_blog_posts.title', 'jocom_blog_posts.category_id', 'jocom_blog_category.category', 'jocom_blog_posts.is_publish', 'jocom_blog_posts.published_date', 'jocom_blog_posts.status', 'jocom_blog_posts.author', 'jocom_blog_posts.is_pinned_post', 'jocom_blog_posts.created_at', 'jocom_blog_posts.created_by', 'jocom_blog_posts.main_image_id')
                ->leftJoin('jocom_blog_category', 'jocom_blog_category.id', '=', 'jocom_blog_posts.category_id')
                ->where('jocom_blog_posts.status', 1)
                ->where('jocom_blog_posts.activation', 1)
                ->orderBy('jocom_blog_posts.published_date', 'desc')
                ->offset($page * $limit)->take($limit)->get();
            $pids = array_column(json_decode(json_encode($post), true), 'id');
            $tags = DB::table('jocom_blog_tag')->select('post_id', 'tag')->whereIn("post_id", $pids)->where('activation', 1)->get();
            $tags = json_decode(json_encode($tags), true);
            
            foreach ($post as $key => $value){
                $post_id = $value->id;
                if(!$post->content){
                    $htmlFile = fopen(Config::get('constants.HTML_CONTENT_BLOG_PATH') . "/" . $post_id . ".txt", "r") or die("Unable to open file!");
                    $htmlString = fread($htmlFile, filesize(Config::get('constants.HTML_CONTENT_BLOG_PATH') . "/" . $post_id . ".txt"));
                    fclose($htmlFile);
                }else{
                    $htmlString = urldecode(html_entity_decode($post->content));
                }
                $htmlString = trim(preg_replace('/([\t]|[ ]{2,})/i', '', strip_tags($htmlString)));

                if (strlen($htmlString) > $max_length) {
                    $offset = ($max_length - 3) - strlen($htmlString);
                    $htmlString = substr($htmlString, 0, strrpos($htmlString, ' ', $offset)) . '...';
                }
                $tdata = array_filter($tags, function($v) use ($post_id) { return $v['post_id'] === $post_id; });
                    
                $dataCollection[] = [
                    "id" => $post_id,
                    "title" => $value->title,
                    "category" => $value->category,
                    "category_id" => $value->category_id,
                    "is_publish" => $value->is_publish,
                    "published_date" => date_format(date_create($value->published_date), "F d, Y") , //Sept 16th, 2012
                    "status" => $value->status,
                    "author" => $value->author,
                    "is_pinned_post" => $value->is_pinned_post,
                    "created_at" => $value->created_at,
                    "created_by" => $value->created_by,
                    "main_image_id" => $value->main_image_id,
                    "tags" => ($tag_d === 'data_only' ? array_column($tdata, 'tag') : $tdata),
                    "content" => $htmlString
                ];
            }
        } catch (Exception $ex) { die("Error : " . $ex->getMessage() . " " . $ex->getLine()); }

        return Response::json([
            "totalPages" => $totalPage,
            "currentPageNumber" => $currentPage,
            "nextPage" => $nextPage,
            "previousPage" => $previousPage,
            "totalPageDisplay" => $limit,
            "data" => $dataCollection
        ]);
    }

    
    /**
     * Comment: Optimise Version that reduce DB load + check content if DB 
     *          have record else using php file read method (php file 
     *          read is ineffective, when come to big file)
     *
     * @api TRUE
     * @author ???
     * @since ???
     * @param string tag_data ['data_only', 'full_data'], For get the the banner type of flash sales
     * @version 1.2
     * @return JSON data
     *
     * Last Update: 17 FEB 2023
     */
    public function anyPost($post_id = 1){
        $post_id = ((int)$post_id > 0 ? (int)$post_id : 1);
        
        try{
            $post = DB::table('jocom_blog_posts')
                ->select('jocom_blog_posts.id', 'jocom_blog_posts.total_viewed', 'jocom_blog_posts.title', 'jocom_blog_posts.category_id', 'jocom_blog_posts.published_date', 'jocom_blog_posts.is_pinned_post', 'jocom_blog_category.category', 'jocom_blog_posts.author', 'jocom_blog_posts.created_at', 'jocom_blog_posts.created_by', 'jocom_blog_posts.status', 'jocom_blog_posts.is_publish', 'jocom_blog_posts.main_image_id')
                ->leftJoin('jocom_blog_category', 'jocom_blog_category.id', '=', 'jocom_blog_posts.category_id')
                ->where('jocom_blog_posts.status', 1)
                ->where("jocom_blog_posts.id", $post_id)->first();

            if(!$post) return ["data" => []];  // if post data not found return empty instead
            $postViewed = BlogPosts::where('id', $post_id)->update(['total_viewed' => $post->total_viewed + 1]);

            if(!$post->content){
                $htmlFile = fopen(Config::get('constants.HTML_CONTENT_BLOG_PATH') . "/" . $post->id . ".txt", "r") or die("Unable to open file!");
                $htmlString = fread($htmlFile, filesize(Config::get('constants.HTML_CONTENT_BLOG_PATH') . "/" . $post->id . ".txt"));
                fclose($htmlFile);
            }else{
                $htmlString = urldecode(html_entity_decode($post->content));
            }
            $htmlString = trim(preg_replace('/([\t]|[ ]{2,})/i', '', $htmlString));
                
            return Response::json([
                "id" => $post->id,
                "title" => $post->title,
                "category" => $post->category,
                "category_id" => $post->category_id,
                "is_publish" => $value->is_publish,
                "published_date" => date_format(date_create($post->published_date),"F d, Y") , //Sept 16th, 2012
                "status" => $post->status,
                "author" => $post->author,
                "is_pinned_post" => $post->is_pinned_post,
                "created_at" => $post->created_at,
                "created_by" => $post->created_by,
                "main_image_id" => $post->main_image_id,
                "tags" => DB::table('jocom_blog_tag')->where("post_id", $post->id)->where('activation', 1)->lists('tag'),
                "content" => $htmlString,
            ]);
        } catch (Exception $ex) { die("Error : " . $ex->getMessage() . " " . $ex->getLine()); }
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
        
        $data = ['enc' => 'UTF-8'];
        
        
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
        
        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
        
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
        die('Disabled Temporary');
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
    public function anyCatfirsttier() {
        $method = Input::get('method', 'json');
        $category_id = (int)Input::get('cat_id', 588);
        $this_cat_info = Input::get('get_this', false);
        $parent_cat_info = Input::get('get_parent', false);

        $categories = DB::select("
            SELECT category.id, category.category_name, category.category_img, category.category_img_banner, category.category_web_banner, category.charity, category.category_parent 
            FROM (SELECT id FROM jocom_products_category WHERE permission = 0 AND status = 1 AND id > 0 AND (id = $category_id OR category_parent = $category_id)) AS cat_ids
            LEFT JOIN jocom_products_category AS category ON category.category_parent = cat_ids.id
            WHERE category.id IS NOT NULL
            ORDER BY category.weight DESC, category.category_name ASC
        ");
        $categories = json_decode(json_encode($categories), true); // convert into array format

        $data = ['cat_id' => $category_id, 'item' => []];
        if($this_cat_info){
            $this_cat = Category::where('id', '=', $category_id)->select('id', 'category_name', 'category_name_cn', 'category_name_my', 'category_descriptions', 'category_parent', 'category_img', 'category_img_banner', 'category_web_banner', 'status', 'charity')->first()->toArray();
            $data = array_merge($data, $this_cat);
        }

        if($parent_cat_info){
            if(!$this_cat_info) $this_cat = Category::where('id', '=', $category_id)->select('id', 'category_name', 'category_name_cn', 'category_name_my', 'category_descriptions', 'category_parent', 'category_img', 'category_img_banner', 'category_web_banner', 'status', 'charity')->first()->toArray();
            $data['parent'] = Category::where('id', '=', $this_cat['category_parent'])->select('id', 'category_name', 'category_name_cn', 'category_name_my', 'category_descriptions', 'category_parent', 'category_img', 'category_img_banner', 'category_web_banner', 'status', 'charity')->first();
        }

        foreach ($categories as $value) {
            if((int)$value['id'] === $category_id) continue;
            $value_data = [
                'id'         => $value['id'],
                'cat_name'   => $value['category_name'],
                'cat_icon'   => (!empty($value['category_img']) ? Image::link("images/category/" . (file_exists("images/category/thumbs/" . $value['category_img']) ? 'thumbs/' : '') . $value['category_img']) : ''),
                'cat_banner' => (!empty($value['category_img_banner']) ? Image::link("images/category/" . (file_exists("images/category/" . $value['category_img_banner']) ? '' : 'thumbs/') . $value['category_img_banner']) : ''),
                'web_banner' => (!empty($value['category_web_banner']) ? Image::link("images/category/" . (file_exists("images/category/" . $value['category_web_banner']) ? '' : 'web/') . $value['category_web_banner']) : ''),
                'charity'    => $value['charity'],
            ];
            if(!$value_data['web_banner']) $value_data['web_banner'] = $value_data['cat_banner'];

            if((int)$value['category_parent'] === $category_id){ // sub cat
                if(isset($data['item'][$value['id']])){
                    $child = (isset($data['item'][$value['id']]['children']) ? $data['item'][$value['id']]['children'] : false);
                    $data['item'][$value['id']] = $value_data;
                    if($child) $data['item'][$value['id']]['children'] = $child;
                }else{
                    $data['item'][$value['id']] = $value_data;
                }
            }else{ // last cat
                if(isset($data['item'][$value['category_parent']]))
                    $data['item'][$value['category_parent']]['children'][$value['id']] = $value_data;
                else
                    $data['item'][$value['category_parent']] = [
                        'children' => [$value['id'] => $value_data]
                    ];
            }
        }

        if($method === 'json'){
            return Response::json($data);
        }else if($method === 'xml'){
            $xml_data = [
                'enc' => 'UTF-8',
                'cat_id' => $data['cat_id'],
                'item' => $data['item']
            ];
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
        $category_id = Input::get('cat_id',588);
        $query = Category::where('permission', '=', 0)->where('status', '=', 1)->where('id', '>', 0);


        // get only grocery main cat result
        $where = '';
        $cat_table = "jocom_categories";
        if(Input::get('only_grocery')){
            $temp_query = clone $query;
            $cat_ids = $temp_query->where('category_parent', '=', $category_id)->lists('id');
            $str_id = implode(",",$cat_ids);
            $where = "WHERE sales_total.category_id IN(" . $str_id . ")";
            $cat_table = "(SELECT id, product_id, category_id FROM jocom_categories WHERE category_id IN (" . $str_id . ") )";
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
                    SUM(sales_total.total) AS total,
                    cat.category_name as category_name,
                    cat.category_img as image,
                    cat.category_img_banner as banner
                FROM (
                    SELECT 
                        b.product_id AS product_id, 
                        d.category_id AS category_id,
                        ROUND( SUM( b.total ) / COUNT(d.category_id), 2) AS total
                    FROM $cat_table AS d
                    JOIN jocom_transaction_details AS b
                        ON d.product_id = b.product_id
                    JOIN (SELECT id FROM jocom_transaction WHERE status = 'completed') AS a
                        ON a.id = b.transaction_id
                    WHERE
                        b.product_id <> 0 
                    GROUP BY
                        b.p_option_id
                ) AS sales_total
                LEFT JOIN jocom_products_category AS cat ON sales_total.category_id = cat.id
                $where
                GROUP BY sales_total.category_id
                ORDER BY total DESC
            ")
        ); // category_parent


        $data = [
            'img_link' => Image::link("images/category/"),
            'cat' => $cat_data,
            'sort' => $result,
        ];
        if($method === 'json'){
            return json_encode($data);
        }else if($method === 'xml'){
            $data['enc'] = 'UTF-8';
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
            $flash = defined('FLASHSALES_IGNORE_STATUS') && in_array($flashtype, ['Exclusive', 'ComboDeal', 'Dynamic']) ? $flash->where('JFP.activation', 1) : $flash->where('JF.status', 1)->where('JFP.activation', 1);
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
            'enc' => '',
        ];
        $data['xml_data']['batch'] = $batch;
        $data['enc'] = 'UTF-8';

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }
    
    public function anyFlashsalesweb()
    {
        define('FLASHSALES_IGNORE_STATUS', 1);
        return $this->anyFlashsales();
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
    
    private function bannerimg_callback($dir, &$filename){
        if(!$filename || !$dir) return false; // stop futher proceed if filename is not valid
        $filename_webp = preg_replace('/[.](png|jpg|jpeg)$/i', '.webp', $filename);
        $filename = (file_exists($dir . $filename_webp) ? $filename_webp : $filename);
    }

    private function bannerimg_check(&$banner_data, $idx, $is_webp){
        if($is_webp){
            if(is_array($banner_data[$idx])){
                if($banner_data[$idx]['allow_multilang']){
                    $banner_data[$idx]['image'] = json_decode($banner_data[$idx]['image'], true);
                    $banner_data[$idx]['image_m'] = json_decode($banner_data[$idx]['image_m'], true);
                    foreach (explode(',', $banner_data[$idx]['lang']) as $lang) {
                        $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]['image'][$lang]);
                        $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]['image_m'][$lang]);
                    }
                }else{
                    $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]['image']);
                    $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]['image_m']);
                }
            }else{
                if($banner_data[$idx]->allow_multilang){
                    $banner_data[$idx]->image = json_decode($banner_data[$idx]->image, true);
                    $banner_data[$idx]->image_m = json_decode($banner_data[$idx]->image_m, true);
                    foreach (explode(',', $banner_data[$idx]->lang) as $lang) {
                        $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]->image[$lang]);
                        $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]->image_m[$lang]);
                    }
                }else{
                    $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]->image);
                    $this->bannerimg_callback(Config::get('constants.JOCOMMY_BANNER_PATH'), $banner_data[$idx]->image_m);
                }
            }
        }
    }

    private function Callback_EndDuration($year = null, $month = null, $day = null, $hour = null, $minute = null, $second = null){
        $year = (!is_null($year) ? $year : date('Y'));
        $month = (!is_null($month) ? $month : date('m'));
        $day = (!is_null($day) ? $day : date('d'));
        $hour = (!is_null($hour) ? $hour : date('H'));
        $minute = str_pad($minute, 2, '0', STR_PAD_LEFT);
        $second = str_pad($second, 2, '0', STR_PAD_LEFT);
        return "$year-$month-$day $hour:$minute:$second";
    }

    private function generate_ScheduleEndDuration($format = [], $duration = 0){
        $data = false;
        if($format[0] === 'DAY') $date = $this->Callback_EndDuration(date('Y'), date('m'), date('d'), $format[1], $format[2], $format[3]);
        if($format[0] === 'WEEK') $date = self::$week_day[$format[1]] . " this week " . $format[2] . ":" . str_pad($format[3], 2, '0', STR_PAD_LEFT) . ":" . str_pad($format[4], 2, '0', STR_PAD_LEFT);
        if($format[0] === 'MONTH') $date = $this->Callback_EndDuration(date('Y'), date('m'), $format[1], $format[2], $format[3], $format[4]);
        if($format[0] === 'YEAR') $date = $this->Callback_EndDuration(date('Y'), str_pad(self::$month_data[$format[1]], 2, '0', STR_PAD_LEFT), $format[2], $format[3], $format[4], $format[5]);
        return ($date ? date('Y-m-d H:i:s', strtotime($date) + $duration) : false);
    }

    /**
     * Comment: Banner include its content
     *
     * @api TRUE
     * @author YEE HAO
     * @since 25 AUG 2021
     * @param method
     * @version 1.6
     * @method GET, POST, any request method
     * @return JSON | XML
     * @used-by jocom.my home page
     * @used-by jocom.my flash sales page
     * @used-by jocom.my banner page
     *
     * Last Update: 7 SEP 2022
     */
    public function anyJocommybanner(){
        $method = Input::get('method', 'json');
        $type = Input::get('type');
        $id = Input::get('id', 0);
        $event_id = Input::get('EventType', 0); // default always treat to fetch jocom.my
        $fetch = Input::get('content_fetch', 0); // 0 - dont fetch, 1 - convert jocommy_banner_manage content type is template from json into array format, 2 - futher force convert content type is template into template content "Inaddition, remove all banner that not template content", 3 - Json Decode all multilang content before it convert to XML or JSON format
        $region = Input::get('region', 0); // All pick the banner support all region, 
        $lang = Input::get('lang', 'EN'); // default always EN first, Refer ISO 639-2 codes
        $platform = mb_strtoupper(Input::get('platform', 'ALL'), 'UTF-8'); // ALL - all platform, WEB - website only, MOBILE - both andriod and IOS, IOS - for ios phone, ANDRIOD - for andriod phone
        $status = (in_array(Input::get('status'), [0, 1, 2, 'ALL']) ? Input::get('status', 1) : 1); // ALL - ignore all banner status, 0 - inactive, 1 - active, 2 - softdelete
        $img_format = Input::get('img_format', 'ignore'); // pick specified image format if found otherwise ignore use back the DB configure
        $is_webp = ($img_format === 'webp' ? true : false);
        
        $type_ref = [
            0 => [
                'hero' => 'banner_hero',
                'brand' => 'banner_brand',
                'special' => 'banner_special',
                'partner' => 'banner_partner',
                'category' => 'banner_category',
                'ewallet' => 'banner_ewallet',
                'arrive' => 'banner_arrive',
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
        
        $query = DB::table('jocommy_banner_manage')->select('id', 'type', 'content_type', 'content_data', 'image', 'image_m', 'title', 'status', 'position', 'logic_operation', 'begin_at', 'duration', 'allow_multilang', 'lang', 'region');
        if($id) $query->where('id', $id);
        $query->whereIn('region', ($region ? [$region, 0] : [0]));
        if($lang && in_array($lang, ['EN', 'CN', 'MY'])) $query->whereRaw("LOCATE('" . strtolower($lang) . "', lang)");
        if($event_id === 0 || $event_id) $query->where('event_id', $event_id);
        if(isset($type_ref[$event_id][$type])) $query->where('type', $type_ref[$event_id][$type]);
        $platform_range = in_array($platform, ['IOS', 'ANDROID']) ? [$platform, 'MOBILE', 'ALL'] : [$platform, 'ALL'];
        if(in_array($platform, ['WEB', 'MOBILE', 'IOS', 'ANDROID'])) $query->whereIn('platform', $platform_range);
        if($status !== 'ALL') $query->where('status', $status);
        $result = $query->orderBy('position', 'ASC')->get();

        if($fetch){
            if($fetch == 1){
                foreach ($result as $k => $v) {
                    $this->bannerimg_check($result, $k, $is_webp);
                    $result[$k]->end_at = ((int)$result[$k]->logic_operation === 1 ? date('Y-m-d H:i:s', strtotime($result[$k]->begin_at) + $result[$k]->duration) : ((int)$result[$k]->logic_operation === 2 ? $this->generate_ScheduleEndDuration(explode(' ', $result[$k]->begin_at), $result[$k]->duration) : "0"));
                    if($v->content_type !== 'template') continue;
                    $temp_v = json_decode($v->content_data, true);
                    if(json_last_error() === 0) $result[$k]->content_data = $temp_v;
                }
            }
            if($fetch == 2){
                $new = json_decode(json_encode($result), true);
                $temp = [];
                foreach ($new as $k => $v) {
                    $this->bannerimg_check($result, $k, $is_webp);
                    if($v['content_type'] !== 'template'){
                        unset($result[$k]); // remove the non template banner
                        continue;
                    }
                    $result[$k]->end_at = ((int)$result[$k]->logic_operation === 1 ? date('Y-m-d H:i:s', strtotime($result[$k]->begin_at) + $result[$k]->duration) : ((int)$result[$k]->logic_operation === 2 ? $this->generate_ScheduleEndDuration(explode(' ', $result[$k]->begin_at), $result[$k]->duration) : "0"));
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
                    foreach ($ref_1 as $index => $banner_id) $result[$index]->content_data = (in_array(strtolower(Input::get('device')), ['android', 'ios']) ? preg_replace('/(href="(.*?)")/i', 'href="JavaScript:Void(0);" onclick="event.preventDefault();"', $template_data[$ref_0[$banner_id]]) : $template_data[$ref_0[$banner_id]]);
                }
            }
            if($fetch == 3){
                foreach ($result as $k => $v) {
                    self::bannerimg_check($result, $k, $is_webp);
                    if(!(in_array($v->content_type, ['template', 'searchname']) || $v->allow_multilang)) continue;
                    $result[$k]->content_data = json_decode($v->content_data, true);
                    $result[$k]->image = json_decode($v->image, true);
                    $result[$k]->image_m = json_decode($v->image_m, true);
                    $result[$k]->title = json_decode($v->title, true);
                }
            }
        }else if($is_webp){
            foreach ($result as $k => $v) self::bannerimg_check($result, $k, $is_webp);
        }

        $data = [
            'img_path' => url('/') . '/' . Config::get('constants.JOCOMMY_BANNER_PATH'),
            'banner' => $result
        ];

        if($method === 'json'){
            return Response::json($data);
        }else if($method === 'xml'){
            $data['enc'] = 'UTF-8';
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
        $data = [];

        try{
            // Start transaction
            DB::beginTransaction();

            $jccode = explode(",", Input::get('code'));
            if(count($jccode)){
                $pro_data = Product::whereIn('qrcode', $jccode)->select('qrcode', 'name', 'category')->get();
                if(count($pro_data)){
                    $pro_data = $pro_data->toArray();
                    $cat_list = array_column($pro_data, 'category'); // grasp the category field only
                    $cat_list = array_unique(explode(",", implode(",", $cat_list)));

                    $cat_data = Category::whereIn('id', $cat_list)->lists('category_name', 'id');
                    foreach ($pro_data as $pro) {
                        $pro_cat = explode(',', $pro['category']);
                        $cat_name = [];
                        foreach ($cat_data as $k => $v) if(in_array($k, $pro_cat)) $cat_name[] = str_replace('+', '-', urlencode($v));
                        $data[] = [
                            'code' => $pro['qrcode'],
                            'name' => str_replace('+', '-', urlencode($pro['name'])),
                            'category_id'  => $pro_cat,
                            'category_name' => $cat_name,
                        ];
                    }
                }
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
        return Response::json(["RespStatus" => $RespStatus, "error"=> $isError, "errorCode"=> $errorCode, "message" => $message, "data"=> $data]);
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
    
    public function anyGearUp(){      
        $dest_receipt_pdf      = Config::get('constants.ATTACHMENT_GEARUP_RECEIPT');
        
        $receipt_id     = Input::get('receipt_id');
        $amount         = Input::get('amount');
        $name           = Input::get('name');
        $ic             = Input::get('ic');
        $email          = Input::get('email');
        $mobile_num     = Input::get('mobile_num');
        $remark         = Input::get('remark');
        $date_created   = Input::get('date_created');
        $status         = Input::get('status');

        $image          = Input::file('image');
        $file_ext       = $image->getClientOriginalExtension();

        $receipt_name  = $receipt_id.'_upload_'.date('Ymd_his').'.'.$file_ext;
        $upload_receipt_attachment  = $image->move($dest_receipt_pdf, $receipt_name);

        
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';

        $data = DB::table('jocom_gear_up')
                    ->insert([
                        'receipt_id'    => $receipt_id,
                        'amount'        => $amount,
                        'name'          => $name,
                        'ic'            => $ic,
                        'email'         => $email,
                        'mobile_num'    => $mobile_num,
                        'remark'        => $remark,
                        'date_created'  => $date_created,
                        'status'        => $status,
                        'receipt_name'  => $receipt_name
                    ]);

        return;
    }
    
    public function anyClaimredeemrequest(){
      
      $data=array();
      $transaction_id=Input::get('transaction_id');
      $user_id=Input::get('user_id');
      $password=Input::get('password');
      
      $checklist=DB::table('jocom_redeem')->select('id','spending_amout')->where('user_id', $user_id)->where('transaction_id', $transaction_id)
                 ->where('status','=',0)
                 ->first();
                
      if($checklist!=""){
          
      if($password=='JCM22GU'){
         $spentamount=$checklist->spending_amout;
         if(($spentamount) >='200' && ($spentamount) < '300'){
            $amount="200"; 
         }else  if(($spentamount) >='300' && ($spentamount) < '500'){
             $amount="300";
         }else  if(($spentamount) >='500'){
             $amount="500";
         }
         
         $data=[
           'user_id'=>$user_id,
           'transaction_id'=>$transaction_id,
           'amount'=>$amount,
           'status'=>'success',
           'message'=>''
           ];
    
      }else{
          $data=[
              'status'=>'error',
              'message'=>'Password is incorrect'
              ];
      }
    }else{
       $data=[
              'status'=>'error',
              'message'=>'E-Voucher Not Found or Already Used!'
              ]; 
    }
      
      return json_encode($data);
      
  }
  
  public function anyClaimredeempost(){
      
      $data=array();
      $transaction_id=Input::get('transaction_id');
      $user_id=Input::get('user_id');
      $redeem_hamper=Input::get('redeem_hamper');
      $password=Input::get('password');

      $checklist=DB::table('jocom_redeem')->select('id','spending_amout')->where('user_id', $user_id)->where('transaction_id', $transaction_id)
                 ->where('status','=',0)
                 ->first();
                
      if($checklist!=""){
      if($password=='JCM22GU'){
      $update = DB::table('jocom_redeem')
                ->where('user_id', $user_id)
                ->where('transaction_id', $transaction_id)
                ->update([
                        'redeem_hamper'    => $redeem_hamper,
                        'date_redeem'   => date('Y-m-d H:i:s'),
                        'status'        => '1'
                    ]);
        if($update){
          $data=[
              'status'=>'Success',
              'message'=>'E-Voucher Redeem Successfully'
              ]; 
        }
        else{
          $data=[
              'status'=>'error',
              'message'=>'Invaild Request !Please Try Again'
              ];  
        }
      }else{
          $data=[
              'status'=>'error',
              'message'=>'Password is incorrect'
              ];
      }
      
    }else{
       $data=[
              'status'=>'error',
              'message'=>'E-Voucher Not Found or Already Used!'
              ]; 
    }
      
      return json_encode($data);
      
  }
  
  public function anyWavpaylogin(){
		$data = [ 'enc' => 'UTF-8' ];
		
		$ApiLog = new ApiLog;
		$ApiLog->api = 'WAVPAY APP LOGIN';
		$ApiLog->data = json_encode(Input::all());
		$ApiLog->save();

		$data = array_merge($data, ApiUser::GetWavpayDetails());

		return Response::view('xml_v', $data)
			->header('Content-Type', 'text/xml')
			->header('Pragma', 'public')
			->header('Cache-control', 'private')
			->header('Expires', '-1');
	}
	
    /**
     * Comment: Optimize version of Feed Transaction History
     *
     * @api TRUE
     * @author YEE HAO
     * @since 22 FEB 2023
     * @param method
     * @version 1.0
     * @method GET, POST, any request method
     * @return JSON
     * @used-by jocom.my Orderlist Page
     * @used-by jocom.my Order Details Page
     *
     * Last Update: 22 FEB 2023
     */
    public function anyTransactionhistory(){
        try{
            $error = true;
            if(Input::get('buyer')){
                $buyer = Customer::where('username', '=', Input::get('buyer'))->first();
                if (!empty($buyer)) $error = false;
            }

            if($error === true) return ['xml_data' => ['status_msg' => '#401']];

            $limit = "";
            if(Input::get('count') !== false && is_numeric(Input::get('count'))) {
                $limit = " LIMIT " . Input::get('count');
                if(Input::get('from') !== false && is_numeric(Input::get('from'))) $limit = " LIMIT " . Input::get('from') . ", " . Input::get('count');
            }else{
                $limit = " LIMIT 50 ";
            }

            $where = " WHERE status in ('completed', 'refund') ";
            if(Input::get('buyer')) $where .= " AND `buyer_username` = " . General::escape(Input::get('buyer'));

            $trans_result = DB::select('SELECT * from jocom_transaction' . $where . ' ORDER BY id DESC' . $limit);
            $trans_result = json_decode(json_encode($trans_result), true);
            $trans_id_list = array_column($trans_result, 'id');

            $data = [
                // is the modify date more than the insert date
                'record' => count($trans_result),
                'total_record' => count(DB::select('select id from jocom_transaction' . $where)),
                'item' => [],
            ];

            $trans_details = DB::table('jocom_transaction_details AS JTD')
                ->select('JTD.*', 'JP.*')
                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JTD.product_id')    
                ->whereIn('JTD.transaction_id', $trans_id_list)
                ->get();

            foreach($trans_details as $row) {
                if($row->product_group != '') { // if is Package product skip assign into Details
                    if(!isset($group_product_price[$row->product_group])) $group_product_price[$row->product_group] = 0;
                    $group_product_price[$row->product_group] += ($row->price * $row->unit);

                    if(!isset($group_product_gst[$row->product_group])) $group_product_gst[$row->product_group] = 0;
                    $group_product_gst[$row->product_group] += ($row->gst_amount);
                    continue;
                }

                $details[$row->transaction_id][] = [
                    'id' => $row->id,
                    'name'  => $row->name,
                    'img_1' => (!empty($row->img_1) ? Image::link("images/data/thumbs/{$row->img_1}") : ''),
                    'img_2' => (!empty($row->img_2) ? Image::link("images/data/thumbs/{$row->img_2}") : ''),
                    'img_3' => (!empty($row->img_3) ? Image::link("images/data/thumbs/{$row->img_3}") : ''),
                    'sku' => $row->sku,
                    'qrcode' => $row->qrcode,
                    'price' => number_format($row->price, 2, '.', ''),
                    'unit' => $row->unit,
                    'gst_amount' => number_format($row->gst_amount, 2, '.', ''),
                    'delivery_time' => $row->delivery_time,
                    'total' => number_format($row->total, 2, '.', '')
                ];
            }

            // Get The Transaction Product For Package
            $trans_detail_group_query = DB::select('SELECT a.*, b.delivery_time, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name, b.qrcode FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_and_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` IN (' . implode(',', $trans_id_list) . ')');
            foreach($trans_detail_group_query as $pack_row) {
                $details[$pack_row->transaction_id][] = [
                    'id' => $pack_row->id,
                    'sku' => $pack_row->sku,
                    'qrcode' => $pack_row->qrcode,
                    'price' => number_format($group_product_price[$pack_row->sku] / $pack_row->unit, 2, '.', ''),
                    'unit' => $pack_row->unit,
                    'gst_amount' => number_format($group_product_gst[$pack_row->sku], 2, '.', ''),
                    'delivery_time' => $pack_row->delivery_time,
                    'total' => number_format($group_product_price[$pack_row->sku], 2, '.', '')
                ];
            }

            // Get The Coupon Applied
            $coupon_applied = array_fill_keys($trans_id_list, []);
            $coupon_tot_amt = array_fill_keys($trans_id_list, 0);
            $grandTotal = array_fill_keys($trans_id_list, 0);
            $coupon_query = TCoupon::whereIn('transaction_id', $trans_id_list)->get();
            foreach($coupon_query as $coupon_row) {
                $coupon_applied[$coupon_row->transaction_id][] = $coupon_row->coupon_code;
                $coupon_tot_amt[$coupon_row->transaction_id] += $coupon_row->coupon_amount;
            }

            // Point Redeem
            $redeemed = array_fill_keys($trans_id_list, []);
            $redeemedPoints = TPoint::join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')
                ->whereIn('jocom_transaction_point.transaction_id', $trans_id_list)
                ->where('jocom_transaction_point.status', '=', 1)
                ->get();
            foreach ($redeemedPoints as $point) {
                $redeemed[$point->transaction_id][] = [
                    'point_type_id' => $point->point_type_id,
                    'point_type' => $point->type,
                    'point' => $point->point,
                    'amount' => $point->amount,
                ];

                $grandTotal[$point->transaction_id] -= $point->amount;
            }

            // Point Earn
            $earned = array_fill_keys($trans_id_list, []);
            $earnedPoints = DB::table('point_transactions')
                ->select('point_users.point_type_id', 'point_types.type', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->whereIn('point_transactions.transaction_id', $trans_id_list)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->groupBy('point_transactions.transaction_id')
                ->get();
            foreach ($earnedPoints as $point) {
                $earned[$point->transaction_id][] = [
                    'point_type_id' => $point->point_type_id,
                    'point_type' => $point->type,
                    'point' => $point->point,
                ];
            }
            
            $refund         = array_fill_keys($trans_id_list, []);
            $arr_refunds    = DB::table('jocom_refund as refund')
                ->select('refund.id', 'refund.trans_id', 'type.refund_type', 'type.amount', 'type.amount_type', 'refund.created_date')
                ->leftJoin('jocom_refund_types as type', 'type.refund_id', '=', 'refund.id')
                ->whereIn('refund.trans_id', $trans_id_list)
                ->whereNotNull('type.refund_type')
                ->get();

            foreach($arr_refunds as $refunds) {
                $amount = $refunds->amount;
                $cash_amount    = 0;
                $point_amount   = 0;

                if($refund_id == "" || $refund_id != $refunds->id) { 
                    $refund_id = $refunds->id;
                }
                
                if(is_numeric($amount) && $amount > 0) {
                    if($refunds->refund_type == "Cash") {
                        $cash_amount    =  ($cash_amount == "") ? $amount : $cash_amount + $amount;
                    }
                        
                    if($refunds->refund_type == "JoPoint") {
                        $point          = ($refunds->amount_type == "deduct" ? "" : "+" . $amount);
                        $point_amount   = ($point_amount == "") ? $point : $point_amount  + $point;
                    }
                }

                if ($cash_amount > 0 || $point_amount > 0) {
                    $refund[$refunds->trans_id][] = [
                        'refund_cash'   => Config::get('constants.CURRENCY') . number_format($cash_amount, 2, '.', ''),
                        'refund_point'  => ($point_amount > 0) ? $point_amount : "",
                        'refund_date'   => $refunds->created_date,
                    ];
                }
            }


            // Get delivery status
            $logisticStatus = LogisticTransaction::whereIn('transaction_id', $trans_id_list)->lists('status', 'transaction_id');
            foreach ($trans_result as $trans_record) {
                $grandTotal[$trans_record['id']] = $grandTotal[$trans_record['id']] + $trans_record['total_amount'] - $coupon_tot_amt[$trans_record['id']] + $trans_record['gst_total'];
                
                $data['item'][] = [
                    'id' => $trans_record['id'],
                    'transaction_date' => date("d/m/Y", strtotime($trans_record['transaction_date'])),
                    'buyer' => $trans_record['buyer_username'],
                    'delivery_charges' => number_format($trans_record['delivery_charges'], 2,'.',''),
                    'processing_fees' => number_format($trans_record['process_fees'], 2,'.',''),
                    'coupon_code' => implode(", ", $coupon_applied[$trans_record['id']]),
                    'coupon_amount' => number_format($coupon_tot_amt[$trans_record['id']], 2,'.',''),
                    'point_redeem' => $redeemed[$trans_record['id']],
                    'point_earn' => $earned[$trans_record['id']],
                    'gst_rate' => number_format($trans_record['gst_rate'], 0,'.',''),
                    'gst_total' => number_format($trans_record['gst_total'], 2,'.',''),
                    // amount customer paid
                    'grand_total' => number_format($grandTotal[$trans_record['id']], 2, '.', ''),
                    'status' => ucwords($trans_record['status']),
                    'refund' => $refund[$trans_record['id']],
                    'extra' => $details[$trans_record['id']],
                    'delivery_status' => ($logisticStatus[$trans_record['id']] ? LogisticTransaction::get_status($logisticStatus[$trans_record['id']]) : '-'),
                    'delivery_completed' => ($logisticStatus[$trans_record['id']] == 5 ? 'true' : 'false'),
                    'parcel_status' => $trans_record['parcel_status'],
                    'parcel_status_option' => ($trans_record['parcel_status'] == Parcel::Sending ? Parcel::Received : '')
                ];
            }

            return Response::json($data);
        }catch(exception $ex){
            echo $ex->getMessage();
        }
    }
    
    public function anyTotalcompletetrans(){
        $code = Input::get('code', NULL);
        $username = Input::get('username', NULL);
        if (!is_string($code) || !$code || !$username) return ['status' => 'error'];
        $keyname = 'TransRecord_' . $code . '_' . $username;
        if (Cache::has($keyname)) return Response::json(Cache::get($keyname));
        
        $trans_rec = 0;
        if($username){ // When username is define treta as logion user
            $proID = (int)str_replace('JC', '', $code);
            $trans_rec = DB::select("
                SELECT count(JT.id) AS num_record
                FROM (SELECT transaction_id FROM jocom_transaction_details WHERE product_id = $proID) AS JTD
                JOIN (SELECT id FROM jocom_transaction WHERE buyer_username = '$username' AND status = 'completed') AS JT
                    ON JT.id = JTD.transaction_id
            ")[0]->num_record;
        }
        Cache::add($keyname, [ 'trans_rec' => $trans_rec ], Carbon\Carbon::now()->addMinutes(10));
        return Response::json([ 'trans_rec' => $trans_rec ]);
    }
    public function anyTestlazada(){
        $ordernumber=Input::get('ordernumber');
        $accounttype=Input::get('accounttype');
        $order = LazadaPreOrder::where("order_number","=",$ordernumber)->where("from_account","=",$accounttype)->first();
            
                if(!$order && $order==''){ 
                    print_r('working');
                    exit;
                }
    } 
    
    public function anyUpdategobarakah(){
        $isError = 0;
        $RespStatus = 1;
        $message = "";
       
        $data = array();

        try{

            DB::beginTransaction();

                $transactionId      = Input::get('transaction_id');
                $gobaravoucher      = Input::get('gobarakah_voucher');
                $transaction        = Transaction::find($transactionId);

                if ($transaction) {
                $transaction->gobarakah_voucher = $gobaravoucher;
                $transaction->save();

                    $data = array('transID' => $transactionId,
                                  'status' => 1
                        );
                    $message = "success";
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
    

    
}
