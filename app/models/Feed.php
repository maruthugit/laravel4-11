<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Helper\ImageHelper as Image;

class Feed extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $fillable = ['code'];

    /**
     *  Fetch user details.
     */
    public function scopeFetch_user($query, $username, $pass) {
        $data       = array();
        $valid      = 0;
        $crypt_pass = md5($pass);

        $cust       = DB::table('jocom_user')
                        ->where('username', '=', $username)
                        ->where('password', '=', $crypt_pass)
                        ->first();

        if($cust) {
            $valid = 1;
            $udata                  = array();
            $udata['password']      = Hash::make($pass);
            $udata['modify_date']   = date("Y-m-d H:i:s");
            $udata['modify_by']     = "admin";

            $customer = DB::table('jocom_user')
                            ->where('username', '=', $username)
                            ->update($udata);

        } else {

            $cust   = DB::table('jocom_user as u')
                        ->select('u.*', 'c.name as country2', 's.name as state2')
                        ->leftjoin('jocom_countries as c', 'c.id', '=', 'u.country_id')
                        ->leftjoin('jocom_country_states as s', 's.id', '=', 'u.state_id')
                        ->where('username', '=', $username)
                        ->first();

            if($cust) {
                if(Hash::check($pass, $cust->password)) $valid = 1;
            }
        }

        if($valid == 1) {

            $data['username']       = $cust->username;
            $data['full_name']      = $cust->full_name;
            $data['ic_no']          = $cust->ic_no;
            $data['address1']       = str_replace("\n", " ", $cust->address1);
            $data['address2']       = str_replace("\n", " ", $cust->address2);
            $data['postcode']       = $cust->postcode;
            $data['state']          = $cust->state2;
            $data['city']           = $cust->city;
            $data['dob']            = $cust->dob;
            $data['country']        = $cust->country2;
            $data['email']          = $cust->email;
            $data['mobile_no']      = $cust->mobile_no;
            $data['firstname']      = $cust->firstname;
            $data['lastname']       = $cust->lastname;
            $data['modify_date']    = $cust->modify_date;
            $data['created_date']   = $cust->created_date;
        } else {

            $data['status_msg']  = '#806';
            // $data['error_message']  = 'Access denied';
        }

        return array('xml_data' => $data);
    }


    /**
     * Display country feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    public function scopeCountry_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";

        if( isset($get['code']) ) {
            return $this->state_feed($param1, $count, $from, $get);
        } else {
            $insert_date = DB::table('jocom_countries')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')->first();

            $modify_date = DB::table('jocom_countries')
                                ->select('modify_date')
                                ->orderBy('modify_date', 'DESC')->first();

            $timestamp_insert = $insert_date->insert_date;
            $timestamp_modify = $modify_date->modify_date;

            $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
            $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

            if ($modify_date > $insert_date) {
                $timestamp = $modify_date ;
            } else {
                $timestamp = $insert_date ;
            }

            $countries = DB::table('jocom_countries')
                                ->select('*')
                                ->orderBy('name', 'ASC')->get();

            $data['timestamp'] = $timestamp;
            $data['record'] = count($countries);
            $data['state'] = 'NO';
            $data['item'] = array();

            foreach ($countries as $country) {
                $data['item'][] = array(
                    'id' => $country->id,
                    'name' => $country->name,
                    // 'insert_by' => $country->insert_by,
                    // 'insert_date' => $country->insert_date,
                    // 'modify_by' => $country->modify_by,
                    // 'modify_date' => $country->modify_date,
                );
            }
        }
        // var_dump($data);
        return array('xml_data' => $data);
    }

    /**
     * Display state feed based on country.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    public function scopeState_feed($test, $param1, $count='', $from='', $get=array()) {
        $data       = array();
        $limit      = "";
        // $country_id = $get['code'];
        // echo "<br>[param1: ".$param1."]";
        $states = DB::table('jocom_country_states')
                        ->select('*')
                        ->orderBy('name', 'ASC')
                        ->where('country_id', '=', $get['code'])
                        ->get();


            // echo "<br>get[code]: ".$get['code'] . "<br>country_id: ".$country_id . "<br>";
            // echo "<br>[state_feed] : ";
            // var_dump($states);

        if(count($states) > 0) {
            $insert_date = DB::table('jocom_country_states')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')
                                ->where('country_id', '=', $get['code'])->first();

            $modify_date = DB::table('jocom_country_states')
                                ->select('modify_date')
                                ->orderBy('modify_date', 'DESC')
                                ->where('country_id', '=', $get['code'])->first();

            $timestamp_insert = $insert_date->insert_date;
            $timestamp_modify = $modify_date->modify_date;

            $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
            $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

            if ($modify_date > $insert_date) {
                $timestamp = $modify_date ;
            } else {
                $timestamp = $insert_date ;
            }

            $data['timestamp'] = $timestamp;
            $data['record'] = count($states);
            $data['state'] = 'YES';
            $data['item'] = array();

            foreach ($states as $state) {
                $data['item'][] = array(
                    'id' => $state->id,
                    'name' => $state->name,
                    // 'country_id' => $state->country_id,
                    // 'insert_by' => $state->insert_by,
                    // 'insert_date' => $state->insert_date,
                    // 'modify_by' => $state->modify_by,
                    // 'modify_date' => $state->modify_date,
                );
            }
        } else $data['status_msg'] = '#101';
        // else $data['error'] = 'Sorry. No data found!';

        return array('xml_data' => $data);
    }


    /**
     * Display city feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */

    public function scopeCity_feed($query, $param1, $count='', $from='', $get=array()) {

        $data   = array();
        $limit  = "";

        if( isset($get['code']) ) {
            $cities = DB::table('cities')
                                ->select('*')
                                ->orderBy('name', 'ASC')
                                ->where('state_id', '=', $get['code'])->get();

            if(count($cities) > 0) {
                $insert_date = DB::table('cities')
                                    ->select('insert_date')
                                    ->orderBy('insert_date', 'DESC')
                                    ->where('state_id', '=', $get['code'])->first();

                $modify_date = DB::table('cities')
                                    ->select('modify_date')
                                    ->orderBy('modify_date', 'DESC')
                                    ->where('state_id', '=', $get['code'])->first();

                $timestamp_insert = $insert_date->insert_date;
                $timestamp_modify = $modify_date->modify_date;

                $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
                $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

                if ($modify_date > $insert_date) {
                    $timestamp = $modify_date ;
                } else {
                    $timestamp = $insert_date ;
                }

                $data['timestamp'] = $timestamp;
                $data['record'] = count($cities);
                $data['city'] = 'YES';
                $data['item'] = array();

                foreach ($cities as $city) {
                    $data['item'][] = array(
                        'id'    => $city->id,
                        'name'  => $city->name,
                        // 'country_id' => $state->country_id,
                        // 'insert_by' => $state->insert_by,
                        // 'insert_date' => $state->insert_date,
                        // 'modify_by' => $state->modify_by,
                        // 'modify_date' => $state->modify_date,
                    );
                }
            } else $data['status_msg'] = '#101';
            // else $data['error'] = 'Sorry. No data found!';
        } else {
            $insert_date = DB::table('cities')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')->first();

            $modify_date = DB::table('cities')
                                ->select('modify_date')
                                ->orderBy('modify_date', 'DESC')->first();

            $timestamp_insert = $insert_date->insert_date;
            $timestamp_modify = $modify_date->modify_date;

            $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
            $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

            if ($modify_date > $insert_date) {
                $timestamp = $modify_date ;
            } else {
                $timestamp = $insert_date ;
            }

            $cities = DB::table('cities')
                                ->select('*')
                                ->orderBy('name', 'ASC')->get();

            $data['timestamp'] = $timestamp;
            $data['record'] = count($cities);
            $data['item'] = array();

            foreach ($cities as $city) {
                $data['item'][] = array(
                    'id' => $city->id,
                    'name' => $city->name,
                );
            }
        }
        // var_dump($data);
        return array('xml_data' => $data);
    }

     /**
     * Display banner feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */

    public function scopeBanner_feed($query, $param1, $count='', $from='', $get=array(), $region_id='')
    {
        $lang               = "en";
        $device             = "phone";
        if($get['lang'] != "")      $lang   = strtolower($get['lang']) ;
        if($get['device'] != "")    $device = strtolower($get['device']);

        $data               = array();
        $limit              = "";
        $timestamp          = "";
        $thumb_name         = "";
        $image_path         = "";
        $thumb_path         = "";
        $thumb_width        = 640;
        $thumb_height       = 400;

        if($get['platform'] != "") {
            $platform = strtoupper($get['platform']);
        }else{
            $platform = Platform::JOCOM_APP_CODE;
        }
        
        
        $v3App = false;
        
        // App checking version
        if($get['vapp'] != ""){
            $vApp = $get['vapp'];
            if($vApp >= 3.0){
                $v3App = true;
            }
        }
        
        $region_id = "";
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyBPcKtMmHfljZFsJSHy4wuzp5vO7NNwGVo';
            
            $json = file_get_contents($url);
          
            $test=json_decode($json);
          

            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];

            $country_name = $response[1];

            if ($region_name== "Wilayah Persekutuan Kuala Lumpur" OR $region_name== "Wilayah Persekutuan Putrajaya" or  $region_name== "Wilayah Persekutuan Labuan") {
                
                $string = explode (' ', $region_name, 3);
                $name2 = "WP-".$string[2];
            }
            else{
                $name2 = $region_name;
            }
            
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();
            
            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();

            $region_id = $regions->region_id;
           
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
            
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
            if($stateid == RegionController::KLANGVALLEYSTATEID){
                $region_id = RegionController::KLANGVALLEYREGIONID;
            }else{
                $stateidInfo = State::find($stateid);
                $region_id = $stateidInfo->region_id;
            }
        }
        
       
        switch ($device) {
            case 'phone':
                $image_path         = Config::get('constants.BANNER_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BANNER_THUMB_FILE_PATH');
                break;
            
            case 'tablet':
                $image_path         = Config::get('constants.BANNER_TAB_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BANNER_TAB_THUMB_FILE_PATH');
                break;
        }

        if($count !== false && is_numeric($count)) {
            $limit  = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }
        $where = "";

        // FOR NEW APP
        if($v3App){
            $banners = DB::table('jocom_banners')->select('jocom_banners.id', 'jocom_banners.pos', 'jocom_banners.url_link', 'jocom_banners.qrcode','jocom_banners.is_page')
//                            ->leftjoin('jocom_banners_images', 'jocom_banners_images.banner_id', '=', 'jocom_banners.id')
//                            ->where('jocom_banner_images.language', '=', 'en')
                ->where('jocom_banners.category_id', '=', 0)
                ->where('jocom_banners.active_status', '=', 1);
                /* WHEN JUEPIN IS READY */
                            /*
                            switch ($platform) {
                                case Platform::JOCOM_APP_CODE:
                                    $banners->where('jocom_banners.is_jocom_app',"=",1);
                                    break;
                                case Platform::JUEPIN_APP_CODE:
                                    $banners->where('jocom_banners.is_juepin_app',"=",1);
                                    break;
                                default:
                                    $banners->where('jocom_banners.is_jocom_app',"=",1);
                                    break;
                            }
                            /* WHEN JUEPIN IS READY */
                if($region_id == 2 || $region_id == 3 || $region_id == 5){
                    $banners = $banners->whereIn('jocom_banners.region_id',[$region_id]);
                }else{
                    $banners = $banners->whereIn('jocom_banners.region_id',[$region_id,0]);
                }
                
                
                
               
        }else{
        // FOR OLD APP    
            $banners = DB::table('jocom_banners')->select('jocom_banners.id', 'jocom_banners.pos', 'jocom_banners.url_link', 'jocom_banners.qrcode','jocom_banners.is_page')
//                            ->leftjoin('jocom_banners_images', 'jocom_banners_images.banner_id', '=', 'jocom_banners.id')
//                            ->where('jocom_banner_images.language', '=', 'en')
                ->where('jocom_banners.category_id', '=', 0);
        }

        $banners = $banners->groupby('jocom_banners.id');
        $banners = $banners->orderBy('jocom_banners.pos', 'desc')->get();

        $time_in = DB::table('jocom_banners')
                        ->orderBy('insert_date', 'desc')
                        ->first();

        $time_mo = DB::table('jocom_banners')
                        ->orderBy('modify_date', 'desc')
                        ->first();

        $timestamp_insert = $time_in->insert_date;
        $timestamp_modify = $time_mo->modify_date;

        $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
        $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        if(count($banners) > 0) {
            $data['timestamp']  = $timestamp;

            $data['item']       = array();
            $args               = array();
            $args['req']        = "banner";
            $count              = 0;

            foreach ($banners as $banner) {

                $image_name = "";
                $file_name  = "";
                $thumb_name = "";
                $images     = Banner::getImagesByLanguage($banner->id, $device, $lang);

                if(count($images) > 0) {
                    $image_name = $images->file_name;
                    $file_name  = $image_path . $lang . "/" . $images->file_name;
                    $thumb_name = $thumb_path . $lang . "/" . $images->thumb_name;
                }
                else {
                    $default_images = Banner::getImagesByLanguage($banner->id, "phone", "en");

                    if(count($default_images) > 0) {
                        $image_name     = $default_images->file_name;
                        $file_name      = Config::get('constants.BANNER_FILE_PATH') . "en/" . $default_images->file_name;
                        $thumb_name     = Config::get('constants.BANNER_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                    }
                }

                if ($file_name != "" && file_exists('./' . $file_name)) {
                    if(!file_exists('./' . $thumb_name)) {
                        // echo "<br> NOT EXISTS! - ". '/public/' . $thumb_name;
                        create_thumbnail($image_name, $thumb_width, $thumb_height, './' . $image_path . "en", './' . $thumb_path . $lang . "/");
                    }

                    $data['item'][] = array(
                        'id'        => $banner->id,
                        'img'       => Image::link($file_name).'?'.$banner->modify_date, // .'?'.uniqid(),
                        'thumbnail' => Image::link($thumb_name).'?'.$banner->modify_date, //.'?'.uniqid(),
                        'url'       => $banner->url_link,
                        'qrcode'    => $banner->qrcode,
                        'is_page'   => $banner->is_page
                    );
                    $count++;
                }
            }

            $data['record']     = $count;
        } else $data['status_msg'] = '#501';
        
        return array('xml_data' => $data);
    }

    function scopeBannertemplate_feed($query, $param1, $count='', $from='', $get=array()){
        
        $app = Input::get('app');
        // print_r($param1);

        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyBPcKtMmHfljZFsJSHy4wuzp5vO7NNwGVo';
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
                }
            
              }
              
            $region_name = $response[0];

            $country_name = $response[1];

            if ($region_name== "Wilayah Persekutuan Kuala Lumpur") {
                $string = explode (' ', $region_name, 3);
                $name2 = $string[2];
            }elseif ($region_name== "Wilayah Persekutuan Putrajaya" or  $region_name== "Wilayah Persekutuan Labuan") {
                
                $string = explode (' ', $region_name, 3);
                $name2 = "WP-".$string[2];
            }elseif ($region_name== "Pulau Pinang") {
                $name2 = 'Pulau Pinang';
            }
            else{
                $name2 = $region_name;
            }

          
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();

            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();

            $region_id = $regions->region_id;


        }
    
        if(Input::get('id') != "" || Input::get('stateid') != ""){
            
            if(Input::get('id') != ""){
                 $stateid = strtoupper(Input::get('id'));
            }else{
                 $stateid = strtoupper(Input::get('stateid'));
            }
            //$stateid = strtoupper(Input::get('id'));
            $stateidInfo = State::find($stateid);
            $region_id = $stateidInfo->region_id;
            
        }
        

        if ($region_id !='') {

           $bannermasters = DB::table('jocom_managebanners')
                        ->select('id','type','device')
                        ->where('region_id',$region_id)
                        ->where('active_status','=',1)
                        ->orderBy('type','asc')
                        ->get();
        }else{
            $bannermasters = DB::table('jocom_managebanners')
                        ->select('id','type','device')
                        ->where('region_id',1)
                        ->where('active_status','=',1)
                        ->orderBy('type','asc')
                        ->get();
        }        

            foreach ($bannermasters as $bannermaster) {
                
                if($app == 'android' || $app == ''){
                    $banners = DB::table('jocom_managebanners_images AS JMI')
                        ->leftjoin('jocom_managebanners AS JM', 'JM.id','=', 'JMI.banner_id')
                        ->where('JMI.banner_id','=',$bannermaster->id)
                        ->where('JMI.active_status','=',1)
                        ->select('JMI.file_name','JMI.qrcode','JMI.max_width','JMI.max_height','JMI.banner_seq','JMI.language','JM.type')
                        ->orderBy('JMI.banner_seq','asc')
                        ->get();
                }else{
                    $banners = DB::table('jocom_managebanners_images AS JMI')
                        ->leftjoin('jocom_managebanners AS JM', 'JM.id','=', 'JMI.banner_id')
                        ->where('JMI.banner_id','=',$bannermaster->id)
                        ->select('JMI.file_name','JMI.qrcode','JMI.max_width','JMI.max_height','JMI.banner_seq','JMI.language')
                        ->orderBy('JMI.banner_seq','asc')
                        ->get();
                }
                
                $array_banners['banner'] ="";     
                $Bcounter = 0;
                foreach ($banners as $banner) {

                    $file_name = Config::get('constants.NEW_BANNER_FILE_PATH').$banner->file_name;
                    
                    if($banner->type == 'B002'){
                        $Bcounter++;
                        if ($file_name!='') {
    
                            $array_banners['banner'][] = array(
                                'file_name' => Image::link($file_name),
                                'max_width' => $banner->max_width,
                                'max_height'=> $banner->max_height,
                                'banner_seq'=> $Bcounter,
                                'qrcode'    => $banner->qrcode,
                                
                            );

                        }
                        
                    }else{
                        
                        if ($file_name!='') {
    
                            $array_banners['banner'][] = array(
                                'file_name' => Image::link($file_name),
                                'max_width' => $banner->max_width,
                                'max_height'=> $banner->max_height,
                                'banner_seq'=> $banner->banner_seq,
                                'qrcode'    => $banner->qrcode,
                                
                            );

                        }
                        
                    }
                    
                }
                    $data['item'][] = array(
                        'id'        => $bannermaster->id,
                        'type'      => $bannermaster->type,
                        'device'    => $bannermaster->device,
                        'layout'    => array($array_banners),
                     );   
                    
                }

        return array('xml_data' => $data);
    }
 
    /**
     * Display sellers feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeSeller_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";
        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $data['timestamp'] = '';
        $data['record'] = 0;
        $data['category'] = 'NO';
        $data['item'] = array();

        if( isset($get['code']) ) {
            $sellers = DB::table('jocom_product_and_package')
                                ->select('*')
                                ->orderBy('modify_date', 'DESC')
                                ->where('sell_id', '=', $get['code'])
                                // ->take($limit)
                                ->get();

            $insert_date = DB::table('jocom_product_and_package')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')->first();

            $modify_date = DB::table('jocom_product_and_package')
                                ->select('modify_date')
                                ->orderBy('modify_date', 'DESC')->first();

            $timestamp_insert = $insert_date->insert_date;
            $timestamp_modify = $modify_date->modify_date;

            $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
            $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

            if ($modify_date > $insert_date) {
                $timestamp = $modify_date ;
            } else {
                $timestamp = $insert_date ;
            }

            $data['timestamp'] = $timestamp;
            $data['record'] = $query->num_rows();
            $data['category'] = 'NO';
            $data['item'] = array();

            $args = array();
            $args['req'] = 'seller_products';

            foreach($query as $row) {
                $qrcode = '';

                for($i = 1; $i < 4; $i++) {
                    $img{$i} = '';
                    $thumb{$i} = '';
                    if($row->{'img_' . $i} != '' && file_exists(asset('/').'images/data/' . $row->{'img_' . $i})) {
                        $img{$i} = asset('/').'images/data/' .  $row->{'img_' . $i};
                        $thumb{$i} = asset('/').'images/data/thumbs' .  $row->{'img_' . $i};
                    }
                }

                $vid1 = '';
                if($row->vid_1 != '' && file_exists(asset('/').'media/videos/files/' . $row->vid_1)) {
                    $vid1 = asset('/').'media/videos/files/' . $row->vid_1;
                } else if($row->vid_1 != '')
                    $vid1 = $row->vid_1;

                $args['code'] = $row->qrcode;

                $exist_zone = array();
                $exist_price = array();
                if(substr($row->id, 0, 1) != 'P') {
                    // For Product
                    $delivery_query = DB::table('jocom_product_delivery')
                            ->select('*')
                            ->where('product_id', '=', $row->id)->get();

                    foreach($delivery_query as $delivery_row) {
                        $exist_zone[] = array(
                            'zone' => $delivery_row->zone_id
                        );
                    }

                    $price_query = DB::table('jocom_product_price')
                            ->select('*')
                            ->orderBy('default', 'DESC')
                            ->orderBy('price', 'ASC')
                            ->where('status', '=', 1)
                            ->where('product_id', '=', $row->id)->get();

                    foreach($price_query as $price_row) {
                        $exist_price[] = array(
                            'id' => $price_row->id,
                            'label' => $price_row->label,
                            'price' => $price_row->price,
                            'promo_price' => $price_row->price_promo,
                            'qty' => $price_row->qty,
                            'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                        );
                    }

                    $seller = DB::table('jocom_seller')
                                ->select('*')
                                ->where('id', '=', $row->sell_id)->first();

                    $product_cat = DB::table('jocom_products_category')
                                ->select('category_name')
                                ->where('id', '=', $row->category)->first();

                    $seller_file = '';
                    $seller_file_thumb = '';
                    if(isset($seller->file_name) && $seller->file_name != '' && file_exists(asset('/').'images/seller/' . $seller->file_name)) {
                        $seller_file = asset('/').'images/seller/' . $seller->file_name;
                        $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                    }

                    $data['item'][] = array(
                        'sku' => $row->sku,
                        'seller' => $seller->company_name,
                        'seller_logo' => $seller_file,
                        'seller_logo_thumb' => $seller_file_thumb,
                        'name' => $row->name,
                        'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                        'description' => $row->description,
                        'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                        'img_1' => $img{'1'},
                        'thumb_1' => $thumb{'1'},
                        'img_2' => $img{'2'},
                        'thumb_2' => $thumb{'2'},
                        'img_3' => $img{'3'},
                        'thumb_3' => $thumb{'3'},
                        'vid_1' => $vid1,
                        'qr_code' => $row->qrcode,
                        'zone_records' => $delivery_query->num_rows(),
                        'delivery_zones' => array($exist_zone),
                        'price_records' => sizeof($exist_price),
                        'price_options' => array('option' => $exist_price)
                    );

                } else {
                    // For Package
                    $pro_qty_cal = array();

                    $delivery_query = DB::table('jocom_package_delivery')
                            ->select('*')
                            ->where('package_id', '=', substr($row->id, 1))->get();

                    $tmp = array();
                    foreach($delivery_query as $k => $delivery_row) {
                        if($k == 0)
                            $tmp = explode(",", $delivery_row->zone);
                        $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));

                    }

                    foreach($tmp as $zone_id) {
                        $exist_zone[] = array(
                            'zone' => $zone_id
                        );
                    }

                    $get_pro_query = DB::table('jocom_product_package_product')
                            ->select('*')
                            ->where('package_id', '=', substr($row->id, 1))->get();

                    $pro_opt_id = array();
                    foreach($get_pro_query as $get_pro_row) {
                        $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                        $pro_qty_cal[$get_pro_row->product_opt] = 0;
                    }

                    // Get Total Price
                    $promo_price = 0;
                    $actual_price = 0;
                    $price_query = DB::select( DB::raw("SELECT * FROM `jocom_product_price` WHERE `status` = 1 AND `id` IN (" . implode(", ", array_keys($pro_opt_id)) . ")") );
                    foreach($price_query as $price_row) {
                        $actual_price += ($price_row->price * $pro_opt_id[$price_row->id]);
                        $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id]);
                        $pro_qty_cal[$price_row->id] = $price_row->qty;
                    }

                    $max_qty = false;
                    foreach($pro_qty_cal as $m_qty) {
                        if($max_qty === false)
                            $max_qty = $m_qty;
                        if($max_qty > $m_qty)
                            $max_qty = $m_qty;
                    }

                    if($actual_price == $promo_price)
                        $promo_price = 0;

                    $exist_price[] = array(
                            'id' => "Null",
                            'label' => "Null",
                            'price' => $actual_price,
                            'promo_price' => $promo_price,
                            'qty' => $max_qty,
                            'default' => 'TRUE',
                        );

                    $data['item'][] = array(
                        'sku' => $row->sku,
                        'seller' => 'Null',
                        'seller_logo' => 'Null',
                        'seller_logo_thumb' => 'Null',
                        'name' => $row->name,
                        'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                        'description' => $row->description,
                        'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                        'img_1' => $img{'1'},
                        'thumb_1' => $thumb{'1'},
                        'img_2' => $img{'2'},
                        'thumb_2' => $thumb{'2'},
                        'img_3' => $img{'3'},
                        'thumb_3' => $thumb{'3'},
                        'vid_1' => $vid1,
                        'qr_code' => $row->qrcode,
                        'zone_records' => sizeof($exist_zone),
                        'delivery_zones' => array($exist_zone),
                        'price_records' => sizeof($exist_price),
                        'price_options' => array('option' => $exist_price)
                    );
                }
            }
        }
        return array('xml_data' => $data);
    }

    /**
     * Listing for transaction
     * @return [type] [description]
     */
    public function scopeTransaction_feed($query, $req, $count='', $from='', $get=array())
    {
        
        try{
            
       
        $error = false;
        if( !isset($get['buyer']) )
        {
            $error = true;
        }
        else
        {
            $buyer = Customer::where('username', '=', $get['buyer'])->first();

            if ($buyer == null)
            {
                $error = true;
            }
        }

        if($error === true)
            return array('xml_data' => array('status_msg' => '#401'));
         // return array('xml_data' => array('error_message' => 'Invalid user.'));

        $data = array();
        $limit = "";
        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $where = " WHERE status in ('completed', 'refund') ";
        if( isset($get['buyer']) ) {
            $where .= " AND `buyer_username` = " . General::escape($get['buyer']);
        }

        $query = DB::select('select * from jocom_transaction' . $where . ' ORDER BY id DESC' . $limit);
        

        $totalquery = DB::select('select id from jocom_transaction' . $where);
        
       
        // calculate latest date
        $sql_in = DB::table('jocom_transaction')
        ->select('insert_date')
        ->orderBy('insert_date', 'Desc')
        ->first();
    //   echo "1";
//  print_r($sql_in);
        $query_mo = DB::table('jocom_transaction')
        ->select('modify_date')
        ->orderBy('modify_date', 'Desc')
        ->first();
        // print_r($query_mo);
        // echo "2";
         
        $in_date = (isset($sql_in->insert_date) ? $sql_in->insert_date : '');
        $mo_date = (isset($query_mo->modify_date) ? $query_mo->modify_date : '');
        
        // $in_date = (isset($sql_in[0]->insert_date) ? $sql_in[0]->insert_date : '');
        // $mo_date = (isset($query_mo[0]->modify_date) ? $query_mo[0]->modify_date : '');

        // is the modify date more than the insert date?
        if ($mo_date > $in_date) {
            $timestamp = $mo_date;
        } else {
            $timestamp = $in_date;
        }
        
        $data['timestamp'] = $timestamp;
        $data['record'] = count($query);
        $data['total_record'] = count($totalquery);
        $data['item'] = array();

        // $args = array();
        // $args['req'] = 'trans';

        foreach($query as $row)
        {
            // For the package
            $group_product_price = array();

            $details = array();
            //            $query_2 = TDetails::where('transaction_id', '=', $row->id)
//                    ->leftJoin("jocom_products")->get();
            
            $query_2 = DB::table('jocom_transaction_details AS JTD')
                ->select('JTD.*','JP.*')
                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JTD.product_id')    
                ->where('JTD.transaction_id','=',$row->id)
                ->get();

            foreach($query_2 as $row_2)
            {
                if($row_2->product_group != '')
                {
                    if(!isset($group_product_price[$row_2->product_group]))
                        $group_product_price[$row_2->product_group] = 0;
                    $group_product_price[$row_2->product_group] += ($row_2->price * $row_2->unit);

                    if(!isset($group_product_gst[$row_2->product_group]))
                        $group_product_gst[$row_2->product_group] = 0;
                    $group_product_gst[$row_2->product_group] += ($row_2->gst_amount);
                    continue;
                }
                
                $image1 = ( ! empty($row_2->img_1)) ? Image::link("images/data/thumbs/{$row_2->img_1}") : '';
                $image2 = ( ! empty($row_2->img_2)) ? Image::link("images/data/thumbs/{$row_2->img_2}") : '';
                $image3 = ( ! empty($row_2->img_3)) ? Image::link("images/data/thumbs/{$row_2->img_3}") : '';
                
                $details[] = array(
                    'id' => $row_2->id,
                    'name'  => $row_2->name,
                    'img_1' => $image1,
                    'img_2' => $image2,
                    'img_3' => $image3,
                    'sku' => $row_2->sku,
                    'qrcode' => $row_2->qrcode,
                    'price' => number_format($row_2->price,2,'.',''),
                    'unit' => $row_2->unit,
                    'gst_amount' => number_format($row_2->gst_amount,2,'.',''),
                    //'delivery_fees' => 0,
                    //'delivery_fees' => $row_2->delivery_fees,
                    //remove temporary for steven feed generate total without delivery fees
                    'delivery_time' => $row_2->delivery_time,
                    'total' => number_format($row_2->total,2,'.','')
                );
            }

            // Get The Transaction Product For Package
            $trans_detail_group_query = DB::select('SELECT a.*, b.delivery_time, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_and_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ' . $row->id);
            foreach($trans_detail_group_query as $pack_row)
            {
                $details[] = array(
                    'id' => $pack_row->id,
                    'sku' => $pack_row->sku,
                    'qrcode' => $row_2->qrcode,
                    'price' => number_format($group_product_price[$pack_row->sku] / $pack_row->unit,2,'.',''),
                    'unit' => $pack_row->unit,
                    'gst_amount' => number_format($group_product_gst[$pack_row->sku],2,'.',''),
                    //'delivery_fees' => 0,
                    'delivery_time' => $pack_row->delivery_time,
                    'total' => number_format($group_product_price[$pack_row->sku],2,'.','')
                );
            }

            // Get The Coupon Applied
            $coupon_applied = array();
            $coupon_tot_amt = 0;
            $coupon_query = TCoupon::where('transaction_id', '=', $row->id)->get();
            foreach($coupon_query as $coupon_row) {
                $coupon_applied[] = $coupon_row->coupon_code;
                $coupon_tot_amt += $coupon_row->coupon_amount;
            }
                
            //JCashback Start  
            $jcashback = 0;
            $jcashback_applied = array();

            $queryjcashback = DB::table('jocom_jcashback_transactiondetails')
                            ->where("transaction_id",$row->id)
                            ->where("status",'=',1)
                            ->first();

            if(count($queryjcashback) >0){

                $jcashbackrm = $queryjcashback->jcash_point;
                $jcashback = number_format(($queryjcashback->jcash_point/100),2, '.', '');

                $jcashback_applied[] = [
                    'jcashback_points_rm' => $jcashback,
                    'jcashback_points' => $jcashbackrm,
                ];

            }

            //JCashback End 
            
            $redeemedPoints = TPoint::join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')
                ->where('jocom_transaction_point.transaction_id', '=', $row->id)
                ->where('jocom_transaction_point.status', '=', 1)
                ->get();

            $grandTotal = $row->total_amount - ($coupon_tot_amt + $row->gst_total+ $jcashback);
            $redeemed = array();
            $earned = array();

            foreach ($redeemedPoints as $point)
            {
                $redeemed[] = [
                    'point_type_id' => $point->point_type_id,
                    'point_type' => $point->type,
                    'point' => $point->point,
                    'amount' => $point->amount,
                ];

                $grandTotal -= $point->amount;
            }

            $earnedPoints = DB::table('point_transactions')
                ->select('point_users.point_type_id', 'point_types.type', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $row->id)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->groupBy('point_transactions.transaction_id')
                ->get();

            foreach ($earnedPoints as $point)
            {
                $earned[] = [
                    'point_type_id' => $point->point_type_id,
                    'point_type' => $point->type,
                    'point' => $point->point,
                ];
            }
            
            $refund      = "";
            $arr_refunds    = Refund::get_refund_history($row->id); 
            $cash_amount    = 0;
            $point_amount   = "";
            
            foreach($arr_refunds as $refunds) {
                $amount         = $refunds->amount;

                if($refund_id == "" || $refund_id != $refunds->id) { 
                    $refund_id = $refunds->id;
                }
                
                if(is_numeric($amount) && $amount > 0) {
                    if($refunds->refund_type == "Cash") {
                        $cash_amount    =  ($cash_amount == "") ? $amount : $cash_amount + $amount;
                    }
                        
                    if($refunds->refund_type == "JoPoint") {
                        $point          = ($refunds->amount_type == "deduct") ? "" : "+".$amount;
                        $point_amount   = ($point_amount == "") ? $point : $point_amount  + $point;
                    }
                }
            }
            
            if ($cash_amount > 0 || $point_amount > 0) {
                $refund[] = array(
                                //'refund_id'       => $refunds->id,
                                'refund_cash'   => Config::get('constants.CURRENCY') . number_format($cash_amount,2,'.',''),
                                'refund_point'  => ($point_amount > 0) ? $point_amount : "",
                                'refund_date'   => $refunds->created_date,
                            );
            }

            // Get delivery status
            $logisticStatus = LogisticTransaction::where('transaction_id', '=', $row->id)->pluck('status');

            if ($logisticStatus) {
                $deliveryStatus = LogisticTransaction::get_status($logisticStatus);
            } else {
                $deliveryStatus = '-';
            }
            
            if($logisticStatus == 5){
                $delivery_mark = 'true'; 
            }else{
                $delivery_mark = 'false'; 
            }

            if ($row->parcel_status == Parcel::Sending){
                $parcel = Parcel::Received;
            } else {
                $parcel = '';
            }
            
            // Logistic Status //
            
            
                
            $data['item'][] = array(
                'id' => $row->id,
                'transaction_date' => date("d/m/Y", strtotime($row->transaction_date)),
                'buyer' => $row->buyer_username,
                'delivery_charges' => number_format($row->delivery_charges, 2,'.',''),
                'processing_fees' => number_format($row->process_fees, 2,'.',''),
                'coupon_code' => implode(", ", $coupon_applied),
                'jcashback' => $jcashback_applied,
                'coupon_amount' => number_format($coupon_tot_amt, 2,'.',''),
                'point_redeem' => $redeemed,
                'point_earn' => $earned,
                'gst_rate' => number_format($row->gst_rate, 0,'.',''),
                'gst_total' => number_format($row->gst_total, 2,'.',''),
                // amount customer paid
                'grand_total' => number_format($grandTotal, 2, '.', ''),
                'status' => ucwords($row->status),
                'refund' => $refund,
//                'refund_id' => $refund->id,
//                'refund_type' => $refund->refund_type,
//                'refund_amount' => ($refund->refund_type == "Cash") ? number_format($refund->amount,2, '.', '') : $refund->amount,
//                'refund_date' => $refund->created_date,
                'extra' => $details,
                'delivery_status' => $deliveryStatus,
                'delivery_completed' => $delivery_mark,
                'parcel_status' => $row->parcel_status,
                'parcel_status_option' => $parcel
            );

        }

        //return $data['record'];
        //return var_dump($error);
        //return $in_date . " / " . $mo_date . " / " . $timestamp;
        //return DB::getQueryLog();

        // $data['name'] = "eugene lee";

        return array('xml_data' => $data);
        
        }catch(exception $ex){
            echo $ex->getMessage();
        }
    }

    // public function scopeGen_xml_data($query, $data, $t_count=0, $array_pre="", $array_post="")
    // {
    //     return "Hello";
    // }

//added by maryanne
    
    public function scopeParcel_update_feed($query, $get=array())
    {  
        $data['status']     = '0';
        $data['status_msg'] = '#802';
        $transaction        = Transaction::find(3741);

        if (count($transaction)>0)
        {
            if ($get['parcel_status']=='Received')
            {
                $transaction->parcel_status = $get['parcel_status'];

                if($transaction->save()){ 
                    $data['status']     = '1';
                    $data['status_msg'] = '#818';
                }
            }

        } 
        return array('xml_data' => $data);
        
    }

    public function scopeFees_feed($query, $req, $count='', $from='', $get=array())
    {
        $data = array();

        $delivery_charges = Fees::get_delivery_charges();
        $process_fees = Fees::get_process_fees();

        // $feesrow = Fees::find(1);

        // $delivery_charges = $feesrow->delivery_charges;
        // $process_fees = $feesrow->process_fees;
        $gst_status = Fees::get_gst_status();
        $gst_rate = 1;
        if ($gst_status == '1')
        {
            $temp_gst_rate = Fees::get_gst();
            $gst_rate += $temp_gst_rate/100;
        }

        $data['delivery_charges'] = number_format($delivery_charges*$gst_rate, 2, '.', '');
        $data['process_fees'] = number_format($process_fees*$gst_rate, 2, '.', '');

        $data['currency'] = Config::get('constants.CURRENCY');

        return array('xml_data' => $data);
    }

    public function scopeProducts_name_custom($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";

        if(trim($get['lang']) == 'CN') $lang = '_cn';
        elseif(trim($get['lang'] == 'MY')) $lang = '_my';
        else $lang = '';

        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $search_product_key = "#None#";
        if( isset($get['code']) && strlen($get['code']) >= 3 ) {
            $search_product_key = $get['code'];
        }

        // $sql = DB::table('jocom_product_and_package')
        //                         ->select('*')
        //                         ->orderBy('name', 'ASC')
        //                         ->take($limit)
        //                         ->where('status', '=', 1)
        //                         ->where('name', 'LIKE', "%".$search_product_key."%")->get();

        // $sql = DB::table('jocom_product_and_package')
      //                           ->select('*')
      //                           ->orderBy('name', 'ASC')
      //                           ->take($limit)
      //                           ->where('status', '=', 1)
      //                           ->where('name', 'LIKE', "%".$search_product_key."%")
      //                           ->orWhere(function($query)
                     //            {
                     //                $query->where('status', '=', 1)
                     //                      ->where('name_cn', 'LIKE', "%".$search_product_key."%");
                     //            })
                     //            ->orWhere(function($query)
                     //            {
                     //                $query->where('status', '=', 1)
                     //                      ->where('name_my', 'LIKE', "%".$search_product_key."%");
                     //            })->get();

        function fetchCategoryTree($parent = 0, $user_tree_array = '')
        {

            if (!is_array($user_tree_array))
                $user_tree_array = array();

            $query = DB::table('jocom_products_category')
                        ->select('jocom_products_category.id')
                        ->orderBy('category_name', 'ASC')
                        ->where('jocom_products_category.category_parent', '=', $parent)
                        ->where('jocom_products_category.status', '=', '1')
                        ->get();

            if (count($query) > 0) {

                foreach($query as $row) {
                    $user_tree_array[] = array("id" => $row->id);
                    $user_tree_array = fetchCategoryTree($row->id, $user_tree_array);
                }
            }

            return $user_tree_array;
        }

        $categoryList = fetchCategoryTree($get['products_cat']);
        $temparray = "";
        $count = 0;

        foreach ($categoryList as $list) {
            if ($count==0)
            {
                $temparray = $list['id'];
                $count++;
            }
            else
            $temparray = $temparray . '|' . $list['id'];
        }

        $sql = DB::table('jocom_product_and_package AS a')
                                ->select('a.*')
                                ->orderBy('a.name', 'ASC')
                                ->take($limit)
                                ->where('a.status', '=', 1)
                                ->where(function($query) use ($search_product_key)
                                {
                                    $query->where('a.name', 'LIKE', '%'.$search_product_key.'%')
                                          ->orWhere('a.name_cn', 'LIKE', '%'.$search_product_key.'%')
                                          ->orWhere('a.name_my', 'LIKE', '%'.$search_product_key.'%');
                                })
                                ->where(function($query) use ($temparray)
                                {
                                    // $query->where('a.category', 'REGEXP',  '(^|,)(216|140)(,|$)');
                                    $query->where('a.category', 'REGEXP',  '(^|,)('.$temparray.')(,|$)');
                                            // ->orWhereIn('c.category', $categoryList);
                                })
                                ->get();

                                // SELECT * from fiberbox where field REGEXP '1740|1938|1940';

        // calculate latest date
        $insert_date = DB::table('jocom_product_and_package')
                        ->select('insert_date')
                        ->orderBy('insert_date', 'DESC')
                        ->where('status', '=', 1)
                        ->where('name', 'LIKE', "%".$search_product_key."%")->first();

        $modify_date = DB::table('jocom_product_and_package')
                            ->select('modify_date')
                            ->orderBy('modify_date', 'DESC')
                            ->where('status', '=', 1)
                            ->where('name', 'LIKE', "%".$search_product_key."%")->first();

        $timestamp_insert = $insert_date->insert_date;
        $timestamp_modify = $modify_date->modify_date;

        $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
        $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        $data['timestamp'] = $timestamp;
        $data['tot_record'] = count($sql);
        $data['record'] = count($sql);
        $data['category'] = 'NO';
        $data['item'] = array();

        $args = array();

        foreach($sql as $row) {
            $qrcode = '';
            for($i = 1; $i < 4; $i++) {
                $img{$i} = '';
                $thumb{$i} = '';
                if($row->{'img_' . $i} != '') {
                    $img{$i} = asset('/').'images/data/' . $row->{'img_' . $i};
                    $thumb{$i} = asset('/').'images/data/thumbs/' . $row->{'img_' . $i};
                }
            }

            $vid1 = '';
            if($row->vid_1 != '' && file_exists(asset('/').'media/videos/files/' . $row->vid_1)) {
                $vid1 = asset('/').'media/videos/files/' . $row->vid_1;
            } else if($row->vid_1 != '')
                $vid1 = $row->vid_1;

            $args['code'] = $row->qrcode;

            $exist_zone = array();
            $exist_price = array();
            if(substr($row->id, 0, 1) != 'P') {
                // For Product

                $delivery_query = DB::table('jocom_product_delivery')
                                ->select('*')
                                ->where('product_id', '=', $row->id)->get();

                foreach($delivery_query as $delivery_row) {
                    $exist_zone[] = array(
                        'zone' => $delivery_row->zone_id
                    );
                }

                $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->where('product_id', '=', $row->id)->get();

                foreach($price_query as $price_row) {
                    if($price_row->{'label'.$lang} == NULL) {
                        $price_label = $price_row->label;
                    } else $price_label = $price_row->{'label'.$lang};

                    $tempgst = Product::getGstValue($price_row->id);
                    if($price_row->price_promo!=0)
                        $price_promo = number_format(($price_row->price_promo * $tempgst),2);
                    else
                        $price_promo = 0;
                    $exist_price[] = array(
                        'id' => $price_row->id,
                        'label' => $price_label,
                        'price' => number_format(($price_row->price * $tempgst),2),
                        'promo_price' => $price_promo,
                        'qty' => number_format($price_row->qty),
                        'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                    );
                }

                $seller = DB::table('jocom_seller')
                            ->select('*')
                            ->where('id', '=', $row->sell_id)->first();

                $product_cat = DB::table('jocom_products_category')
                            ->select('category_name')
                            ->where('id', '=', $row->category)->first();

                $seller_file = '';
                $seller_file_thumb = '';
                if(isset($seller->file_name) && $seller->file_name != '' && file_exists(asset('/').'images/seller/' . $seller->file_name)) {
                    $seller_file = asset('/').'images/seller/' . $seller->file_name;
                    $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                }

                if($row->{"name".$lang} == NULL) {
                    $product_name = $row->name;
                } else $product_name = $row->{"name".$lang};

                if($row->{"description".$lang} == NULL) {
                    $product_desc = $row->description;
                } else $product_desc = $row->{"description".$lang};

                $data['item'][] = array(
                    'sku' => $row->sku,
                    'seller' => $seller->company_name,
                    'seller_logo' => $seller_file,
                    'seller_logo_thumb' => $seller_file_thumb,
                    'name' => $product_name,
                    'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                    'description' => $product_desc,
                    'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                    'img_1' => $img{'1'},
                    'thumb_1' => $thumb{'1'},
                    'img_2' => $img{'2'},
                    'thumb_2' => $thumb{'2'},
                    'img_3' => $img{'3'},
                    'thumb_3' => $thumb{'3'},
                    'vid_1' => $vid1,
                    'qr_code' => $row->qrcode,
                    'zone_records' => count($delivery_query),
                    'delivery_zones' => array($exist_zone),
                    'price_records' => sizeof($exist_price),
                    'price_options' => array('option' => $exist_price)
                );
            } else {
                // For Package
                $pro_qty_cal = array();

                $delivery_query = DB::table('jocom_package_delivery')
                            ->select('*')
                            ->where('package_id', '=', substr($row->id, 1))->get();

                $tmp = array();
                foreach($delivery_query as $k => $delivery_row) {
                    if($k == 0)
                        $tmp = explode(",", $delivery_row->zone);
                    $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));

                }

                foreach($tmp as $zone_id) {
                    $exist_zone[] = array(
                        'zone' => $zone_id
                    );
                }

                $get_pro_query = DB::table('jocom_product_package_product')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                $pro_opt_id = array();
                foreach($get_pro_query as $get_pro_row) {
                    $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                    $pro_qty_cal[$get_pro_row->product_opt] = 0;
                }

                // Get Total Price
                $promo_price = 0;
                $actual_price = 0;


                $price_query = DB::select( DB::raw("SELECT * FROM `jocom_product_price` WHERE `status` = 1 AND `id` IN (" . implode(", ", array_keys($pro_opt_id)) . ")") );

                foreach($price_query as $price_row) {
                    $tempgst = Product::getGstValue($price_row->id);

                    $actual_price += ($price_row->price * $pro_opt_id[$price_row->id] * $tempgst);
                    $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id] * $tempgst);
                    $pro_qty_cal[$price_row->id] = $price_row->qty;
                }

                $max_qty = false;
                foreach($pro_qty_cal as $m_qty) {
                    if($max_qty === false)
                        $max_qty = $m_qty;
                    if($max_qty > $m_qty)
                        $max_qty = $m_qty;
                }

                if($actual_price == $promo_price)
                    $promo_price = 0;
                else
                    $promo_price = number_format($promo_price,2);


                $exist_price[] = array(
                        'id' => "Null",
                        'label' => "Null",
                        'price' => number_format($actual_price,2),
                        'promo_price' => $promo_price,
                        'qty' => number_format($max_qty),
                        'default' => 'TRUE',
                    );

                if($row->{"name".$lang} == NULL) {
                    $package_name = $row->name;
                } else $package_name = $row->{"name".$lang};

                if($row->{"description".$lang} == NULL) {
                    $package_desc = $row->description;
                } else $package_desc = $row->{"description".$lang};

                $data['item'][] = array(
                    'sku' => $row->sku,
                    'seller' => 'Null',
                    'seller_logo' => 'Null',
                    'seller_logo_thumb' => 'Null',
                    'name' => $package_name,
                    'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                    'description' => $package_desc,
                    'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                    'img_1' => $img{'1'},
                    'thumb_1' => $thumb{'1'},
                    'img_2' => $img{'2'},
                    'thumb_2' => $thumb{'2'},
                    'img_3' => $img{'3'},
                    'thumb_3' => $thumb{'3'},
                    'vid_1' => $vid1,
                    'qr_code' => $row->qrcode,
                    'zone_records' => sizeof($exist_zone),
                    'delivery_zones' => array($exist_zone),
                    'price_records' => sizeof($exist_price),
                    'price_options' => array('option' => $exist_price)
                );

            }
        }

        // echo '<pre>';
        // dd(DB::getQueryLog());
        // // var_dump($product_package);
        // echo '</pre>';
        // die();

        return array('xml_data' => $data);

    }

    //
    //
    // KHAIRUL's
    //
    //
    /**
     * Display products name feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeProducts_name_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";

        if(trim($get['lang']) == 'CN') $lang = '_cn';
        elseif(trim($get['lang'] == 'MY')) $lang = '_my';
        else $lang = '';

        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $search_product_key = "#None#";
        if( isset($get['code']) && strlen($get['code']) >= 3 ) {
            $search_product_key = $get['code'];
        }

        function fetchCategoryTree($parent = 0, $user_tree_array = '')
        {
            if (!is_array($user_tree_array))
                $user_tree_array = array();

            $query = DB::table('jocom_products_category')
                        ->select('id')
                        ->orderBy('category_name', 'ASC')
                        ->where('category_parent', '=', $parent)
                        ->where('status', '=', '1')
                        ->where('permission', '=', '0')
                        ->get();

            if (count($query) > 0) {

                foreach($query as $row) {
                    $user_tree_array[] = array("id" => $row->id);
                    $user_tree_array = fetchCategoryTree($row->id, $user_tree_array);
                }
            }

            return $user_tree_array;
        }

        $categoryList = fetchCategoryTree('0');
        $temparray = "";
        $count = 0;

        foreach ($categoryList as $list) {
            if ($count==0)
            {
                $temparray = $list['id'];
                $count++;
            }
            else
            $temparray = $temparray . '|' . $list['id'];
        }

        $sql = DB::table('jocom_product_and_package AS a')
                                ->select('a.*')
                                ->orderBy('a.name', 'ASC')
                                ->take($limit)
                                ->where('a.status', '=', 1)
                                ->where(function($query) use ($search_product_key)
                                {
                                    $query->where('a.name', 'LIKE', '%'.$search_product_key.'%')
                                          ->orWhere('a.name_cn', 'LIKE', '%'.$search_product_key.'%')
                                          ->orWhere('a.name_my', 'LIKE', '%'.$search_product_key.'%');
                                })
                                ->where(function($query) use ($temparray)
                                {
                                    // $query->where('a.category', 'REGEXP',  '(^|,)(216|140)(,|$)');
                                    $query->where('a.category', 'REGEXP',  '(^|,)('.$temparray.')(,|$)');
                                            // ->orWhereIn('c.category', $categoryList);
                                })
                                ->get();

        // calculate latest date
        $insert_date = DB::table('jocom_product_and_package')
                        ->select('insert_date')
                        ->orderBy('insert_date', 'DESC')
                        ->where('status', '=', 1)
                        ->where('name', 'LIKE', "%".$search_product_key."%")->first();

        $modify_date = DB::table('jocom_product_and_package')
                            ->select('modify_date')
                            ->orderBy('modify_date', 'DESC')
                            ->where('status', '=', 1)
                            ->where('name', 'LIKE', "%".$search_product_key."%")->first();

        $timestamp_insert = $insert_date->insert_date;
        $timestamp_modify = $modify_date->modify_date;

        $insert_date = (isset($timestamp_insert)? $timestamp_insert : '');
        $modify_date = (isset($timestamp_modify)? $timestamp_modify : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        $data['timestamp'] = $timestamp;
        $data['tot_record'] = count($sql);
        $data['record'] = count($sql);
        $data['category'] = 'NO';
        $data['item'] = array();

        $args = array();

        foreach($sql as $row) {
            $qrcode = '';
            for($i = 1; $i < 4; $i++) {
                $img{$i} = '';
                $thumb{$i} = '';
                if($row->{'img_' . $i} != '') {
                    $img{$i} = asset('/').'images/data/' . $row->{'img_' . $i};
                    $thumb{$i} = asset('/').'images/data/thumbs/' . $row->{'img_' . $i};
                }
            }

            $vid1 = '';
            if($row->vid_1 != '' && file_exists(asset('/').'media/videos/files/' . $row->vid_1)) {
                $vid1 = asset('/').'media/videos/files/' . $row->vid_1;
            } else if($row->vid_1 != '')
                $vid1 = $row->vid_1;

            $args['code'] = $row->qrcode;

            $exist_zone = array();
            $exist_price = array();
            if(substr($row->id, 0, 1) != 'P') {
                // For Product

                $delivery_query = DB::table('jocom_product_delivery')
                                ->select('*')
                                ->where('product_id', '=', $row->id)->get();

                foreach($delivery_query as $delivery_row) {
                    $exist_zone[] = array(
                        'zone' => $delivery_row->zone_id
                    );
                }

                $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->where('product_id', '=', $row->id)->get();

                foreach($price_query as $price_row) {
                    if($price_row->{'label'.$lang} == NULL) {
                        $price_label = $price_row->label;
                    } else $price_label = $price_row->{'label'.$lang};

                    $tempgst = Product::getGstValue($price_row->id);
                    if($price_row->price_promo!=0)
                        $price_promo = number_format(($price_row->price_promo * $tempgst),2);
                    else
                        $price_promo = 0;
                    $exist_price[] = array(
                        'id' => $price_row->id,
                        'label' => $price_label,
                        'price' => number_format(($price_row->price * $tempgst),2),
                        'promo_price' => $price_promo,
                        'qty' => number_format($price_row->qty),
                        'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                    );
                }

                $seller = DB::table('jocom_seller')
                            ->select('*')
                            ->where('id', '=', $row->sell_id)->first();

                $product_cat = DB::table('jocom_products_category')
                            ->select('category_name')
                            ->where('id', '=', $row->category)->first();

                $seller_file = '';
                $seller_file_thumb = '';
                if(isset($seller->file_name) && $seller->file_name != '' && file_exists(asset('/').'images/seller/' . $seller->file_name)) {
                    $seller_file = asset('/').'images/seller/' . $seller->file_name;
                    $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                }

                if($row->{"name".$lang} == NULL) {
                    $product_name = $row->name;
                } else $product_name = $row->{"name".$lang};

                if($row->{"description".$lang} == NULL) {
                    $product_desc = $row->description;
                } else $product_desc = $row->{"description".$lang};

                $data['item'][] = array(
                    'sku' => $row->sku,
                    'seller' => $seller->company_name,
                    'seller_logo' => $seller_file,
                    'seller_logo_thumb' => $seller_file_thumb,
                    'name' => $product_name,
                    'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                    'description' => $product_desc,
                    'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                    'img_1' => $img{'1'},
                    'thumb_1' => $thumb{'1'},
                    'img_2' => $img{'2'},
                    'thumb_2' => $thumb{'2'},
                    'img_3' => $img{'3'},
                    'thumb_3' => $thumb{'3'},
                    'vid_1' => $vid1,
                    'qr_code' => $row->qrcode,
                    'zone_records' => count($delivery_query),
                    'delivery_zones' => array($exist_zone),
                    'price_records' => sizeof($exist_price),
                    'price_options' => array('option' => $exist_price)
                );
            } else {
                // For Package
                $pro_qty_cal = array();

                $delivery_query = DB::table('jocom_package_delivery')
                            ->select('*')
                            ->where('package_id', '=', substr($row->id, 1))->get();

                $tmp = array();
                foreach($delivery_query as $k => $delivery_row) {
                    if($k == 0)
                        $tmp = explode(",", $delivery_row->zone);
                    $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));

                }

                foreach($tmp as $zone_id) {
                    $exist_zone[] = array(
                        'zone' => $zone_id
                    );
                }

                $get_pro_query = DB::table('jocom_product_package_product')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                $pro_opt_id = array();
                foreach($get_pro_query as $get_pro_row) {
                    $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                    $pro_qty_cal[$get_pro_row->product_opt] = 0;
                }

                // Get Total Price
                $promo_price = 0;
                $actual_price = 0;

                $price_query = DB::select( DB::raw("SELECT * FROM `jocom_product_price` WHERE `status` = 1 AND `id` IN (" . implode(", ", array_keys($pro_opt_id)) . ")") );

                foreach($price_query as $price_row) {
                    $tempgst = Product::getGstValue($price_row->id);

                    $actual_price += ($price_row->price * $pro_opt_id[$price_row->id] * $tempgst);
                    $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id] * $tempgst);
                    $pro_qty_cal[$price_row->id] = $price_row->qty;
                }

                $max_qty = false;
                foreach($pro_qty_cal as $m_qty) {
                    if($max_qty === false)
                        $max_qty = $m_qty;
                    if($max_qty > $m_qty)
                        $max_qty = $m_qty;
                }

                if($actual_price == $promo_price)
                    $promo_price = 0;
                else
                    $promo_price = number_format($promo_price,2);


                $exist_price[] = array(
                        'id' => "Null",
                        'label' => "Null",
                        'price' => number_format($actual_price,2),
                        'promo_price' => $promo_price,
                        'qty' => number_format($max_qty),
                        'default' => 'TRUE',
                    );

                if($row->{"name".$lang} == NULL) {
                    $package_name = $row->name;
                } else $package_name = $row->{"name".$lang};

                if($row->{"description".$lang} == NULL) {
                    $package_desc = $row->description;
                } else $package_desc = $row->{"description".$lang};

                $data['item'][] = array(
                    'sku' => $row->sku,
                    'seller' => 'Null',
                    'seller_logo' => 'Null',
                    'seller_logo_thumb' => 'Null',
                    'name' => $package_name,
                    'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                    'description' => $package_desc,
                    'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                    'img_1' => $img{'1'},
                    'thumb_1' => $thumb{'1'},
                    'img_2' => $img{'2'},
                    'thumb_2' => $thumb{'2'},
                    'img_3' => $img{'3'},
                    'thumb_3' => $thumb{'3'},
                    'vid_1' => $vid1,
                    'qr_code' => $row->qrcode,
                    'zone_records' => sizeof($exist_zone),
                    'delivery_zones' => array($exist_zone),
                    'price_records' => sizeof($exist_price),
                    'price_options' => array('option' => $exist_price)
                );

            }
        }
        return array('xml_data' => $data);
    }

    /**
     * Display products feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeProducts_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";

        if(trim($get['lang']) == 'CN') $lang = '_cn';
        elseif(trim($get['lang'] == 'MY')) $lang = '_my';
        else $lang = '';

        if( isset($get['code']) ) {

            $qrcode_clause = trim($get['code']);
            $prodpackage = DB::table("jocom_product_and_package")
                                ->select('*')
                                ->orderBy('name', 'ASC')
                                ->where('status', '=', 1)
                                ->where('qrcode', '=', $get['code'])->get();

            if(count($prodpackage) > 0) {
                $insert_date = DB::table("jocom_product_and_package")
                                    ->select('insert_date')
                                    ->orderBy('insert_date', 'DESC')
                                    ->where('qrcode', '=', $qrcode_clause)->first();

                $modify_date = DB::table("jocom_product_and_package")
                                    ->select('modify_date')
                                    ->orderBy('modify_date', 'DESC')
                                    ->where('qrcode', '=', $qrcode_clause)->first();

                $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                if ($modify_date > $insert_date) {
                    $timestamp = $modify_date ;
                } else {
                    $timestamp = $insert_date ;
                }

                $data['timestamp'] = $timestamp;
                $data['record'] = count($prodpackage);
                $data['category'] = 'NO';
                $data['item'] = array();

                $args = array();
                $args['req'] = 'products';

                foreach ($prodpackage as $prodpkg) {
                    $qrcode = '';

                    for($i = 1; $i < 4; $i++) {
                        $img{$i} = '';
                        $thumb{$i} = '';
                        if(file_exists("./".'images/data/' . $prodpkg->{'img_' . $i}) && $prodpkg->{'img_' . $i} != '') {
                            $img{$i} = asset('/').'images/data/' . $prodpkg->{'img_' . $i};
                            $thumb{$i} = asset('/').'images/data/thumbs/' . $prodpkg->{'img_' . $i};
                        }
                    }

                    $vid1 = '';
                    if($prodpkg->vid_1 != '' && file_exists(asset('/').'media/videos/files/' . $prodpkg->vid_1)) {
                        $vid1 = asset('/').'media/videos/files/' . $prodpkg->vid_1;
                    } else if($prodpkg->vid_1 != '')
                        $vid1 = $prodpkg->vid_1;

                    $args['code'] = $prodpkg->qrcode;

                    $exist_zone = array();
                    $exist_price = array();
                    if(substr($prodpkg->id, 0, 1) != 'P') {

                        $delivery_query = DB::table('jocom_product_delivery')
                                ->select('*')
                                ->where('product_id', '=', $prodpkg->id)->get();

                        foreach ($delivery_query as $delivery_row) {
                            $exist_zone[] = array(
                                'zone' => $delivery_row->zone_id
                            );
                        }

                        $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->where('product_id', '=', $prodpkg->id)->get();

                        foreach ($price_query as $price_row) {
                            if($price_row->{'label'.$lang} == NULL) {
                                $price_label = $price_row->label;
                            } else $price_label = $price_row->{'label'.$lang};

                            $tempgst = Product::getGstValue($price_row->id);
                            if($price_row->price_promo!=0)
                                $price_promo = number_format(($price_row->price_promo * $tempgst),2);
                            else
                                $price_promo = 0;
                            $exist_price[] = array(
                                'id' => $price_row->id,
                                'label' => $price_label,
                                'price' => number_format(($price_row->price * $tempgst),2),
                                'promo_price' => $price_promo,
                                'qty' => number_format($price_row->qty),
                                'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                            );
                        }

                        $seller = DB::table('jocom_seller')
                                ->select('*')
                                ->where('id', '=', $prodpkg->sell_id)->first();

                        $product_cat = DB::table('jocom_products_category')
                                ->select("category_name")
                                ->where('id', '=', $prodpkg->category)->first();

                        $seller_file = '';
                        $seller_file_thumb = '';
                        if(isset($seller->file_name) && $seller->file_name != '' && file_exists(asset('/').'images/seller/' . $seller->file_name)) {
                            $seller_file = asset('/').'images/seller/' . $seller->file_name;
                            $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                        }

                        if($prodpkg->{"name".$lang} == NULL) {
                            $product_name = $prodpkg->name;
                        } else $product_name = $prodpkg->{"name".$lang};

                        if($prodpkg->{"description".$lang} == NULL) {
                            $product_desc = $prodpkg->description;
                        } else $product_desc = $prodpkg->{"description".$lang};

                        $data['item'][] = array(
                            'sku' => $prodpkg->sku,
                            'seller' => $seller->company_name,
                            'seller_logo' => $seller_file,
                            'seller_logo_thumb' => $seller_file_thumb,
                            'name' => $product_name,
                            'products_cat' => $prodpkg->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                            'description' => $product_desc,
                            'delivery_time' => ($prodpkg->delivery_time == '' ? '24 hours' : $prodpkg->delivery_time),
                            'img_1' => $img{'1'},
                            'thumb_1' => $thumb{'1'},
                            'img_2' => $img{'2'},
                            'thumb_2' => $thumb{'2'},
                            'img_3' => $img{'3'},
                            'thumb_3' => $thumb{'3'},
                            'vid_1' => $vid1,
                            'qr_code' => $prodpkg->qrcode,
                            'zone_records' => count($delivery_query),
                            'delivery_zones' => array($exist_zone),
                            'price_records' => sizeof($exist_price),
                            'price_options' => array('option' => $exist_price)
                        );
                    } else {
                        // For Package
                        $pro_qty_cal = array();

                        $delivery_query = DB::table('jocom_package_delivery')
                                ->select('*')
                                ->where('package_id', '=', substr($prodpkg->id, 1))->get();

                        $tmp = array();

                        foreach($delivery_query as $k => $delivery_row) {
                            if($k == 0)
                                $tmp = explode(",", $delivery_row->zone);
                            $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));

                        }

                        foreach($tmp as $zone_id) {
                            $exist_zone[] = array(
                                'zone' => $zone_id
                            );
                        }

                        $get_pro_query = DB::table('jocom_product_package_product')
                                ->select('*')
                                ->where('package_id', '=', substr($prodpkg->id, 1))->get();

                        $pro_opt_id = array();
                        foreach($get_pro_query as $get_pro_row) {
                            $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                            $pro_qty_cal[$get_pro_row->product_opt] = 0;
                        }

                        // Get Total Price
                        $promo_price = 0;
                        $actual_price = 0;

                        $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->whereIn('id', array_keys($pro_opt_id))->get();

                        foreach($price_query as $price_row) {
                            $tempgst = Product::getGstValue($price_row->id);

                            $actual_price += ($price_row->price * $pro_opt_id[$price_row->id] * $tempgst);
                            $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id] * $tempgst);
                            $pro_qty_cal[$price_row->id] = $price_row->qty;
                        }

                        $max_qty = false;
                        foreach($pro_qty_cal as $m_qty) {
                            if($max_qty === false)
                                $max_qty = $m_qty;
                            if($max_qty > $m_qty)
                                $max_qty = $m_qty;
                        }

                        if($actual_price == $promo_price)
                            $promo_price = 0;
                        else
                            $promo_price = number_format($promo_price,2);


                        $exist_price[] = array(
                                'id' => "Null",
                                'label' => "Null",
                                'price' => number_format($actual_price,2),
                                'promo_price' => $promo_price,
                                'qty' => number_format($max_qty),
                                'default' => 'TRUE',
                        );

                        if($prodpkg->{"name".$lang} == NULL) {
                            $package_name = $prodpkg->name;
                        } else $package_name = $prodpkg->{"name".$lang};

                        if($prodpkg->{"description".$lang} == NULL) {
                            $package_desc = $prodpkg->description;
                        } else $package_desc = $prodpkg->{"description".$lang};

                        $data['item'][] = array(
                            'sku' => $prodpkg->sku,
                            'seller' => 'Null',
                            'seller_logo' => 'Null',
                            'seller_logo_thumb' => 'Null',
                            'name' => $package_name,
                            'products_cat' => $prodpkg->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                            'description' => $package_desc,
                            'delivery_time' => ($prodpkg->delivery_time == '' ? '24 hours' : $prodpkg->delivery_time),
                            'img_1' => $img{'1'},
                            'thumb_1' => $thumb{'1'},
                            'img_2' => $img{'2'},
                            'thumb_2' => $thumb{'2'},
                            'img_3' => $img{'3'},
                            'thumb_3' => $thumb{'3'},
                            'vid_1' => $vid1,
                            'qr_code' => $prodpkg->qrcode,
                            'zone_records' => sizeof($exist_zone),
                            'delivery_zones' => array($exist_zone),
                            'price_records' => sizeof($exist_price),
                            'price_options' => array('option' => $exist_price)
                        );
                    }
                }
            }
        } else {
            if( isset($get['products_cat']) ) {

                $query = DB::table('jocom_products_category')
                                ->select('*')
                                ->where('category_parent', '=', $get['products_cat'])
                                ->where('status', '=', 1)
                                ->where('permission', '=', 0)
                                ->get();

                if(count($query) == 0) {
                    $products_cat = trim($get['products_cat']);

                    $query = DB::select( DB::raw("SELECT * FROM jocom_product_and_package WHERE category REGEXP '(^|,)($products_cat)(,|$)' AND status = 1") );

                    // $query = DB::table('jocom_product_and_package')
                    //             ->select('*')
                    //             ->where('category', '=', $get['products_cat'])->get();

                    // $insert_date = DB::select( DB::raw("SELECT insert_date FROM jocom_product_and_package WHERE category REGEXP '(^|,)($products_cat)(,|$)' ORDER BY insert_date DESC LIMIT 1") );
                    $insert_date = DB::table("jocom_product_and_package")
                                    ->select('insert_date')
                                    ->orderBy('insert_date', 'DESC')
                                    ->where('category', '=', $get['products_cat'])->first();

                    // $modify_date = DB::select( DB::raw("SELECT modify_date FROM jocom_product_and_package WHERE category REGEXP '(^|,)($products_cat)(,|$)' ORDER BY modify_date DESC LIMIT 1") );
                    $modify_date = DB::table("jocom_product_and_package")
                                        ->select('modify_date')
                                        ->orderBy('modify_date', 'DESC')
                                        ->where('category', '=', $get['products_cat'])->first();

                    // $timestamp_insert = $insert_date->insert_date;
                    // $timestamp_modify = $modify_date->modify_date;

                    $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                    $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                    if ($modify_date > $insert_date) {
                        $timestamp = $modify_date ;
                    } else {
                        $timestamp = $insert_date ;
                    }

                    $data['timestamp'] = $timestamp;
                    $data['record'] = count($query);
                    $data['category'] = 'NO';
                    $data['item'] = array();

                    $args = array();
                    $args['req'] = 'products';

                    foreach($query as $row) {
                        $qrcode = '';

                        for($i = 1; $i < 4; $i++) {
                            $img{$i} = '';
                            $thumb{$i} = '';
                            if($row->{'img_' . $i} != '') {
                                $img{$i} = asset('/').'images/data/' . $row->{'img_' . $i};
                                $thumb{$i} = asset('/').'images/data/thumbs/' . $row->{'img_' . $i};
                            }
                        }

                        $vid1 = '';
                        if($row->vid_1 != '' && file_exists(asset('/').'media/videos/files/' . $row->vid_1)) {
                            $vid1 = asset('/').'media/videos/files/' . $row->vid_1;
                        } else if($row->vid_1 != '')
                            $vid1 = $row->vid_1;

                        $args['code'] = $row->qrcode;

                        $exist_zone = array();
                        $exist_price = array();
                        if(substr($row->id, 0, 1) != 'P') {
                            // For Product
                            $delivery_query = DB::table('jocom_product_delivery')
                                ->select('*')
                                ->where('product_id', '=', $row->id)->get();

                            foreach($delivery_query as $delivery_row) {
                                $exist_zone[] = array(
                                    'zone' => $delivery_row->zone_id
                                );
                            }

                            $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->where('product_id', '=', $row->id)->get();

                            foreach($price_query as $price_row) {
                                if($price_row->{'label'.$lang} == NULL) {
                                    $price_label = $price_row->label;
                                } else $price_label = $price_row->{'label'.$lang};

                                $tempgst = Product::getGstValue($price_row->id);
                                if($price_row->price_promo!=0)
                                    $price_promo = number_format(($price_row->price_promo * $tempgst),2);
                                else
                                    $price_promo = 0;
                                $exist_price[] = array(
                                    'id' => $price_row->id,
                                    'label' => $price_label,
                                    'price' => number_format(($price_row->price * $tempgst),2),
                                    'promo_price' => $price_promo,
                                    'qty' => number_format($price_row->qty),
                                    'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                                );
                            }

                            $seller = DB::table('jocom_seller')
                                ->select('*')
                                ->where('id', '=', $row->sell_id)->first();

                            $product_cat = DB::table('jocom_products_category')
                                ->select('category_name')
                                ->where('id', '=', $row->category)->first();

                            $seller_file = '';
                            $seller_file_thumb = '';
                            if(isset($seller->file_name) && $seller->file_name != '' && file_exists(asset('/').'images/seller/' . $seller->file_name)) {
                                $seller_file = asset('/').'images/seller/' . $seller->file_name;
                                $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                            }

                            if($row->{"name".$lang} == NULL) {
                                $product_name = $row->name;
                            } else $product_name = $row->{"name".$lang};

                            if($row->{"description".$lang} == NULL) {
                                $product_desc = $row->description;
                            } else $product_desc = $row->{"description".$lang};

                            $data['item'][] = array(
                                'sku' => $row->sku,
                                'seller' => ($seller != NULL) ? $seller->company_name : '',
                                'seller_logo' => $seller_file,
                                'seller_logo_thumb' => $seller_file_thumb,
                                'name' => $product_name,
                                'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                                'description' => $product_desc,
                                'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                                'img_1' => $img{'1'},
                                'thumb_1' => $thumb{'1'},
                                'img_2' => $img{'2'},
                                'thumb_2' => $thumb{'2'},
                                'img_3' => $img{'3'},
                                'thumb_3' => $thumb{'3'},
                                'vid_1' => $vid1,
                                'qr_code' => $row->qrcode,
                                'url' => 'feed/?' . http_build_query($args),
                                'zone_records' => count($delivery_query),
                                'delivery_zones' => array($exist_zone),
                                'price_records' => sizeof($exist_price),
                                'price_option' => array('option' => $exist_price)
                            );
                        } else {
                            // For Package
                            $pro_qty_cal = array();

                            $delivery_query = DB::table('jocom_package_delivery')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                            $tmp = array();
                            foreach($delivery_query as $k => $delivery_row) {
                                if($k == 0)
                                    $tmp = explode(",", $delivery_row->zone);
                                $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));

                            }

                            foreach($tmp as $zone_id) {
                                $exist_zone[] = array(
                                    'zone' => $zone_id
                                );
                            }

                            $get_pro_query = DB::table('jocom_product_package_product')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                            $pro_opt_id = array();
                            foreach($get_pro_query as $get_pro_row) {
                                $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                                $pro_qty_cal[$get_pro_row->product_opt] = 0;
                            }

                            // Get Total Price
                            $promo_price = 0;
                            $actual_price = 0;

                            $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->where('status', '=', 1)
                                ->whereIn('id', array_keys($pro_opt_id))->get();

                            foreach($price_query as $price_row) {
                                $tempgst = Product::getGstValue($price_row->id);

                                $actual_price += ($price_row->price * $pro_opt_id[$price_row->id] * $tempgst);
                                $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id] * $tempgst);
                                $pro_qty_cal[$price_row->id] = $price_row->qty;
                            }

                            $max_qty = false;
                            foreach($pro_qty_cal as $m_qty) {
                                if($max_qty === false)
                                    $max_qty = $m_qty;
                                if($max_qty > $m_qty)
                                    $max_qty = $m_qty;
                            }

                            if($actual_price == $promo_price)
                                $promo_price = 0;
                            else
                                $promo_price = number_format($promo_price,2);


                            $exist_price[] = array(
                                    'id' => "Null",
                                    'label' => "Null",
                                    'price' => number_format($actual_price,2),
                                    'promo_price' => $promo_price,
                                    'qty' => number_format($max_qty),
                                    'default' => 'TRUE',
                                );

                            $data['item'][] = array(
                                'sku' => $row->sku,
                                'seller' => 'Null',
                                'seller_logo' => 'Null',
                                'seller_logo_thumb' => 'Null',
                                'name' => $row->name,
                                'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                                'description' => $row->description,
                                'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                                'img_1' => $img{'1'},
                                'thumb_1' => $thumb{'1'},
                                'img_2' => $img{'2'},
                                'thumb_2' => $thumb{'2'},
                                'img_3' => $img{'3'},
                                'thumb_3' => $thumb{'3'},
                                'vid_1' => $vid1,
                                'qr_code' => $row->qrcode,
                                'zone_records' => sizeof($exist_zone),
                                'delivery_zones' => array($exist_zone),
                                'price_records' => sizeof($exist_price),
                                'price_options' => array('option' => $exist_price)
                            );
                        }
                    }
                } else {
                    $query = DB::table('jocom_products_category')
                                ->select('*')
                                ->where('category_parent', '=', $get['products_cat'])
                                ->where('status', '=', 1)
                                ->where('permission', '=', 0)
                                ->get();

                    // calculate latest date
                    $insert_date = DB::table('jocom_products_category')
                                    ->select('insert_date')
                                    ->orderBy('insert_date', 'DESC')
                                    ->where('category_parent', '=', $get['products_cat'])->first();

                    $modify_date = DB::table('jocom_products_category')
                                        ->select('modify_date')
                                        ->orderBy('modify_date', 'DESC')
                                        ->where('category_parent', '=', $get['products_cat'])->first();

                    // $timestamp_insert = $insert_date->insert_date;
                    // $timestamp_modify = $modify_date->modify_date;

                    $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                    $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                    if ($modify_date > $insert_date) {
                        $timestamp = $modify_date ;
                    } else {
                        $timestamp = $insert_date ;
                    }

                    $data['timestamp'] = $timestamp;
                    $data['record'] = count($query);
                    $data['category'] = 'YES';
                    $data['item'] = array();

                    $args = array();
                    $args['req'] = 'products';

                    foreach($query as $row) {
                        $category_parent = DB::table('jocom_products_category')
                                        ->select("category_name$lang", "category_name")
                                        ->where('id', '=', $row->category_parent)->first();

                        // for multi-level category, added by eugene
                        $sub_cat = DB::table('jocom_products_category')
                                            ->select('id')
                                            ->where('category_parent', '=', $row->id)->get();

                        $temp_subcat = "";
                        if (count($sub_cat) > 0)
                        {
                            $temp_subcat = "1";
                        }

                        // $temp_subcat = "";
                        // $counter = 0;
                        // foreach($sub_cat as $sub)
                        // {
                        //     if($counter == 0)
                        //     {
                        //         $temp_subcat = $temp_subcat . $sub->id ;
                        //         $counter++;
                        //     }
                        //     else
                        //     {
                        //         $temp_subcat = $temp_subcat . "," . $sub->id;
                        //     }
                        // }
                        // end for multi-level category, added by eugene

                        $cat_name_lang = $row->{"category_name".$lang};
                        if($row->{"category_name".$lang} == NULL) {
                            $cat_name_lang = $row->category_name;
                        }

                        $cat_parent_name = $category_parent->{"category_name".$lang};
                        if($category_parent->{"category_name".$lang} == NULL) {
                            $cat_parent_name = $category_parent->category_name;
                        }

                        // number of product, added by eugene
                        $rowcount = 0;
                        if ($temp_subcat == "")
                        {
                            $rowproduct = DB::select( DB::raw("SELECT * FROM jocom_product_and_package WHERE category REGEXP '(^|,)($row->id)(,|$)' AND status = 1") );
                            $rowcount = count($rowproduct);
                        } // end number of product

                        $cat_icon = '';
                        if($row->category_img != '') {
                            $cat_icon = asset('/').'images/category/' . $row->category_img;
                        }

                        $args['products_cat'] = $row->id;
                        $data['item'][] = array(
                            'id' => $row->id,
                            'cat_name' => $cat_name_lang,
                            'cat_name_sort' => $row->category_name,
                            // 'cat_description' => $row->category_descriptions,
                            'cat_parent' => $row->category_parent,
                            'cat_parent_name' => $cat_parent_name,
                            'p_count' => $rowcount,
                            'cat_icon' => $cat_icon,
                            // for multi-level category, added by eugene
                            'sub_cat' => $temp_subcat,
                            // end for multi-level category, added by eugene
                            // 'url' => 'feed/?' . http_build_query($args)
                        );
                    }

                }
            } else {
                $query = DB::table('jocom_products_category')->where('status', '=', 1)->where('permission', '=', 0)->get();

                // calculate latest date
                $insert_date = DB::table('jocom_products_category')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')->first();

                $modify_date = DB::table('jocom_products_category')
                                    ->select('modify_date')
                                    ->orderBy('modify_date', 'DESC')->first();

                $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                if ($modify_date > $insert_date) {
                    $timestamp = $modify_date ;
                } else {
                    $timestamp = $insert_date ;
                }

                $data['timestamp'] = $timestamp;
                $data['record'] = count($query);
                $data['category'] = 'YES';
                $data['item'] = array();

                $args = array();
                $args['req'] = 'products';

                foreach($query as $row) {
                    $category_parent = DB::table('jocom_products_category')
                                        ->select("category_name$lang", "category_name")
                                        ->where('id', '=', $row->category_parent)->first();

                    // for multi-level category, added by eugene
                    $sub_cat = DB::table('jocom_products_category')
                                        ->select('id')
                                        ->where('category_parent', '=', $row->id)->first();

                    $temp_subcat = "";
                    if (count($sub_cat) > 0)
                    {
                        $temp_subcat = "1";
                    }

                    // $temp_subcat = "";
                    // $counter = 0;
                    // foreach($sub_cat as $sub)
                    // {
                    //     if($counter == 0)
                    //     {
                    //         $temp_subcat = $temp_subcat . $sub->id ;
                    //         $counter++;
                    //     }
                    //     else
                    //     {
                    //         $temp_subcat = $temp_subcat . "," . $sub->id;
                    //     }
                    // }
                    // end for multi-level category, added by eugene

                    $cat_name_lang = $row->{"category_name".$lang};
                    if($row->{"category_name".$lang} == NULL) {
                        $cat_name_lang = $row->category_name;
                    }

                    $cat_parent_name = $category_parent->{"category_name".$lang};
                    if($category_parent->{"category_name".$lang} == NULL) {
                        $cat_parent_name = $category_parent->category_name;
                    }

                    // number of product, added by eugene
                    $rowcount = 0;
                    if ($temp_subcat == "")
                    {
                        $rowproduct = DB::select( DB::raw("SELECT id FROM jocom_product_and_package WHERE category REGEXP '(^|,)($row->id)(,|$)' AND status = 1") );
                        $rowcount = count($rowproduct);
                    } // end number of product

                    $cat_icon = '';
                    if($row->category_img != '') {
                        $cat_icon = asset('/').'images/category/' . $row->category_img;
                    }

                    $args['products_cat'] = $row->id;
                    $data['item'][] = array(
                        'id' => $row->id,
                        'cat_name' => $cat_name_lang,
                        'cat_name_sort' => $row->category_name,
                        // 'cat_description' => $row->category_descriptions,
                        'cat_parent' => $row->category_parent,
                        'cat_parent_name' => $cat_parent_name,
                        'p_count' => $rowcount,
                        'cat_icon' => $cat_icon,
                        // for multi-level category, added by eugene
                        'sub_cat' => $temp_subcat,
                        // end for multi-level category, added by eugene
                        // 'url' => 'feed/?' . http_build_query($args)
                    );
                }
            }
        }

        return array('xml_data' => $data);
    }

    public function scopeProducts_custom($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";

        if(trim($get['lang']) == 'CN') $lang = '_cn';
        elseif(trim($get['lang'] == 'MY')) $lang = '_my';
        else $lang = '';

        if( isset($get['code']) ) {

            $qrcode_clause = trim($get['code']);
            $prodpackage = DB::table("jocom_product_and_package")
                                ->select('*')
                                ->orderBy('name', 'ASC')
                                ->where('status', '=', 1)
                                ->where('qrcode', '=', $get['code'])->get();

            if(count($prodpackage) > 0) {
                $insert_date = DB::table("jocom_product_and_package")
                                    ->select('insert_date')
                                    ->orderBy('insert_date', 'DESC')
                                    ->where('qrcode', '=', $qrcode_clause)->first();

                $modify_date = DB::table("jocom_product_and_package")
                                    ->select('modify_date')
                                    ->orderBy('modify_date', 'DESC')
                                    ->where('qrcode', '=', $qrcode_clause)->first();

                $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                if ($modify_date > $insert_date) {
                    $timestamp = $modify_date ;
                } else {
                    $timestamp = $insert_date ;
                }

                $data['timestamp'] = $timestamp;
                $data['record'] = count($prodpackage);
                $data['category'] = 'NO';
                $data['item'] = array();

                $args = array();
                $args['req'] = 'products';

                foreach ($prodpackage as $prodpkg) {
                    $qrcode = '';

                    for($i = 1; $i < 4; $i++) {
                        $img{$i} = '';
                        $thumb{$i} = '';
                        if(file_exists("./".'images/data/' . $prodpkg->{'img_' . $i}) && $prodpkg->{'img_' . $i} != '') {
                            $img{$i} = asset('/').'images/data/' . $prodpkg->{'img_' . $i};
                            $thumb{$i} = asset('/').'images/data/thumbs/' . $prodpkg->{'img_' . $i};
                        }
                    }

                    $vid1 = '';
                    if($prodpkg->vid_1 != '' && file_exists(asset('/').'media/videos/files/' . $prodpkg->vid_1)) {
                        $vid1 = asset('/').'media/videos/files/' . $prodpkg->vid_1;
                    } else if($prodpkg->vid_1 != '')
                        $vid1 = $prodpkg->vid_1;

                    $args['code'] = $prodpkg->qrcode;

                    $exist_zone = array();
                    $exist_price = array();
                    if(substr($prodpkg->id, 0, 1) != 'P') {

                        $delivery_query = DB::table('jocom_product_delivery')
                                ->select('*')
                                ->where('product_id', '=', $prodpkg->id)->get();

                        foreach ($delivery_query as $delivery_row) {
                            $exist_zone[] = array(
                                'zone' => $delivery_row->zone_id
                            );
                        }

                        $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->where('product_id', '=', $prodpkg->id)->get();

                        foreach ($price_query as $price_row) {
                            if($price_row->{'label'.$lang} == NULL) {
                                $price_label = $price_row->label;
                            } else $price_label = $price_row->{'label'.$lang};

                            $tempgst = Product::getGstValue($price_row->id);
                            if($price_row->price_promo!=0)
                                $price_promo = number_format(($price_row->price_promo * $tempgst),2);
                            else
                                $price_promo = 0;
                            $exist_price[] = array(
                                'id' => $price_row->id,
                                'label' => $price_label,
                                'price' => number_format(($price_row->price * $tempgst),2),
                                'promo_price' => $price_promo,
                                'qty' => number_format($price_row->qty),
                                'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                            );
                        }

                        $seller = DB::table('jocom_seller')
                                ->select('*')
                                ->where('id', '=', $prodpkg->sell_id)->first();

                        $product_cat = DB::table('jocom_products_category')
                                ->select("category_name")
                                ->where('id', '=', $prodpkg->category)->first();

                        $seller_file = '';
                        $seller_file_thumb = '';
                        if(isset($seller->file_name) && $seller->file_name != '' && file_exists(asset('/').'images/seller/' . $seller->file_name)) {
                            $seller_file = asset('/').'images/seller/' . $seller->file_name;
                            $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                        }

                        if($prodpkg->{"name".$lang} == NULL) {
                            $product_name = $prodpkg->name;
                        } else $product_name = $prodpkg->{"name".$lang};

                        if($prodpkg->{"description".$lang} == NULL) {
                            $product_desc = $prodpkg->description;
                        } else $product_desc = $prodpkg->{"description".$lang};

                        $data['item'][] = array(
                            'sku' => $prodpkg->sku,
                            'seller' => $seller->company_name,
                            'seller_logo' => $seller_file,
                            'seller_logo_thumb' => $seller_file_thumb,
                            'name' => $product_name,
                            'products_cat' => $prodpkg->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                            'description' => $product_desc,
                            'delivery_time' => ($prodpkg->delivery_time == '' ? '24 hours' : $prodpkg->delivery_time),
                            'img_1' => $img{'1'},
                            'thumb_1' => $thumb{'1'},
                            'img_2' => $img{'2'},
                            'thumb_2' => $thumb{'2'},
                            'img_3' => $img{'3'},
                            'thumb_3' => $thumb{'3'},
                            'vid_1' => $vid1,
                            'qr_code' => $prodpkg->qrcode,
                            'zone_records' => count($delivery_query),
                            'delivery_zones' => array($exist_zone),
                            'price_records' => sizeof($exist_price),
                            'price_options' => array('option' => $exist_price)
                        );
                    } else {
                        // For Package
                        $pro_qty_cal = array();

                        $delivery_query = DB::table('jocom_package_delivery')
                                ->select('*')
                                ->where('package_id', '=', substr($prodpkg->id, 1))->get();

                        $tmp = array();

                        foreach($delivery_query as $k => $delivery_row) {
                            if($k == 0)
                                $tmp = explode(",", $delivery_row->zone);
                            $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));
                        }

                        foreach($tmp as $zone_id) {
                            $exist_zone[] = array(
                                'zone' => $zone_id
                            );
                        }

                        $get_pro_query = DB::table('jocom_product_package_product')
                                ->select('*')
                                ->where('package_id', '=', substr($prodpkg->id, 1))->get();

                        $pro_opt_id = array();
                        foreach($get_pro_query as $get_pro_row) {
                            $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                            $pro_qty_cal[$get_pro_row->product_opt] = 0;
                        }

                        // Get Total Price
                        $promo_price = 0;
                        $actual_price = 0;

                        $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->whereIn('id', array_keys($pro_opt_id))->get();

                        foreach($price_query as $price_row) {
                            $tempgst = Product::getGstValue($price_row->id);

                            $actual_price += ($price_row->price * $pro_opt_id[$price_row->id] * $tempgst);
                            $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id] * $tempgst);
                            $pro_qty_cal[$price_row->id] = $price_row->qty;
                        }

                        $max_qty = false;
                        foreach($pro_qty_cal as $m_qty) {
                            if($max_qty === false)
                                $max_qty = $m_qty;
                            if($max_qty > $m_qty)
                                $max_qty = $m_qty;
                        }

                        if($actual_price == $promo_price)
                            $promo_price = 0;
                        else
                            $promo_price = number_format($promo_price,2);


                        $exist_price[] = array(
                                'id' => "Null",
                                'label' => "Null",
                                'price' => number_format($actual_price,2),
                                'promo_price' => $promo_price,
                                'qty' => number_format($max_qty),
                                'default' => 'TRUE',
                            );

                        if($prodpkg->{"name".$lang} == NULL) {
                            $package_name = $prodpkg->name;
                        } else $package_name = $prodpkg->{"name".$lang};

                        if($prodpkg->{"description".$lang} == NULL) {
                            $package_desc = $prodpkg->description;
                        } else $package_desc = $prodpkg->{"description".$lang};

                        $data['item'][] = array(
                            'sku' => $prodpkg->sku,
                            'seller' => 'Null',
                            'seller_logo' => 'Null',
                            'seller_logo_thumb' => 'Null',
                            'name' => $package_name,
                            'products_cat' => $prodpkg->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                            'description' => $package_desc,
                            'delivery_time' => ($prodpkg->delivery_time == '' ? '24 hours' : $prodpkg->delivery_time),
                            'img_1' => $img{'1'},
                            'thumb_1' => $thumb{'1'},
                            'img_2' => $img{'2'},
                            'thumb_2' => $thumb{'2'},
                            'img_3' => $img{'3'},
                            'thumb_3' => $thumb{'3'},
                            'vid_1' => $vid1,
                            'qr_code' => $prodpkg->qrcode,
                            'zone_records' => sizeof($exist_zone),
                            'delivery_zones' => array($exist_zone),
                            'price_records' => sizeof($exist_price),
                            'price_options' => array('option' => $exist_price)
                        );
                    }
                }
            }
        } else {
            if( isset($get['products_cat']) ) {

                $query = DB::table('jocom_products_category')
                                ->select('*')
                                ->where('category_parent', '=', $get['products_cat'])
                                ->where('status', '=', 1)
                                ->where('permission', '=', 1)
                                ->get();

                if(count($query) == 0) {
                    $products_cat = trim($get['products_cat']);

                    $query = DB::select( DB::raw("SELECT * FROM jocom_product_and_package WHERE category REGEXP '(^|,)($products_cat)(,|$)' AND status = 1") );

                    // $query = DB::table('jocom_product_and_package')
                    //             ->select('*')
                    //             ->where('category', '=', $get['products_cat'])->get();

                    // $insert_date = DB::select( DB::raw("SELECT insert_date FROM jocom_product_and_package WHERE category REGEXP '(^|,)($products_cat)(,|$)' ORDER BY insert_date DESC LIMIT 1") );
                    $insert_date = DB::table("jocom_product_and_package")
                                    ->select('insert_date')
                                    ->orderBy('insert_date', 'DESC')
                                    ->where('category', '=', $get['products_cat'])->first();

                    // $modify_date = DB::select( DB::raw("SELECT modify_date FROM jocom_product_and_package WHERE category REGEXP '(^|,)($products_cat)(,|$)' ORDER BY modify_date DESC LIMIT 1") );
                    $modify_date = DB::table("jocom_product_and_package")
                                        ->select('modify_date')
                                        ->orderBy('modify_date', 'DESC')
                                        ->where('category', '=', $get['products_cat'])->first();

                    // $timestamp_insert = $insert_date->insert_date;
                    // $timestamp_modify = $modify_date->modify_date;

                    $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                    $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                    if ($modify_date > $insert_date) {
                        $timestamp = $modify_date ;
                    } else {
                        $timestamp = $insert_date ;
                    }

                    $data['timestamp'] = $timestamp;
                    $data['record'] = count($query);
                    $data['category'] = 'NO';
                    $data['item'] = array();

                    $args = array();
                    $args['req'] = 'products';

                    foreach($query as $row) {
                        $qrcode = '';

                        for($i = 1; $i < 4; $i++) {
                            $img{$i} = '';
                            $thumb{$i} = '';
                            if($row->{'img_' . $i} != '') {
                                $img{$i} = asset('/').'images/data/' . $row->{'img_' . $i};
                                $thumb{$i} = asset('/').'images/data/thumbs/' . $row->{'img_' . $i};
                            }
                        }

                        $vid1 = '';
                        if($row->vid_1 != '' && file_exists(asset('/').'media/videos/files/' . $row->vid_1)) {
                            $vid1 = asset('/').'media/videos/files/' . $row->vid_1;
                        } else if($row->vid_1 != '')
                            $vid1 = $row->vid_1;

                        $args['code'] = $row->qrcode;

                        $exist_zone = array();
                        $exist_price = array();
                        if(substr($row->id, 0, 1) != 'P') {
                            // For Product
                            $delivery_query = DB::table('jocom_product_delivery')
                                ->select('*')
                                ->where('product_id', '=', $row->id)->get();

                            foreach($delivery_query as $delivery_row) {
                                $exist_zone[] = array(
                                    'zone' => $delivery_row->zone_id
                                );
                            }

                            $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->where('product_id', '=', $row->id)->get();

                            foreach($price_query as $price_row) {
                                if($price_row->{'label'.$lang} == NULL) {
                                    $price_label = $price_row->label;
                                } else $price_label = $price_row->{'label'.$lang};

                                $tempgst = Product::getGstValue($price_row->id);
                                if($price_row->price_promo!=0)
                                    $price_promo = number_format(($price_row->price_promo * $tempgst),2);
                                else
                                    $price_promo = 0;
                                $exist_price[] = array(
                                    'id' => $price_row->id,
                                    'label' => $price_label,
                                    'price' => number_format(($price_row->price * $tempgst),2),
                                    'promo_price' => $price_promo,
                                    'qty' => number_format($price_row->qty),
                                    'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                                );
                            }

                            $seller = DB::table('jocom_seller')
                                ->select('*')
                                ->where('id', '=', $row->sell_id)->first();

                            $product_cat = DB::table('jocom_products_category')
                                ->select('category_name')
                                ->where('id', '=', $row->category)->first();

                            $seller_file = '';
                            $seller_file_thumb = '';
                            if(isset($seller->file_name) && $seller->file_name != '' && file_exists(asset('/').'images/seller/' . $seller->file_name)) {
                                $seller_file = asset('/').'images/seller/' . $seller->file_name;
                                $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                            }

                            if($row->{"name".$lang} == NULL) {
                                $product_name = $row->name;
                            } else $product_name = $row->{"name".$lang};

                            if($row->{"description".$lang} == NULL) {
                                $product_desc = $row->description;
                            } else $product_desc = $row->{"description".$lang};

                            $data['item'][] = array(
                                'sku' => $row->sku,
                                'seller' => ($seller != NULL) ? $seller->company_name : '',
                                'seller_logo' => $seller_file,
                                'seller_logo_thumb' => $seller_file_thumb,
                                'name' => $product_name,
                                'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                                'description' => $product_desc,
                                'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                                'img_1' => $img{'1'},
                                'thumb_1' => $thumb{'1'},
                                'img_2' => $img{'2'},
                                'thumb_2' => $thumb{'2'},
                                'img_3' => $img{'3'},
                                'thumb_3' => $thumb{'3'},
                                'vid_1' => $vid1,
                                'qr_code' => $row->qrcode,
                                'url' => 'feed/?' . http_build_query($args),
                                'zone_records' => count($delivery_query),
                                'delivery_zones' => array($exist_zone),
                                'price_records' => sizeof($exist_price),
                                'price_option' => array('option' => $exist_price)
                            );
                        } else {
                            // For Package
                            $pro_qty_cal = array();

                            $delivery_query = DB::table('jocom_package_delivery')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                            $tmp = array();
                            foreach($delivery_query as $k => $delivery_row) {
                                if($k == 0)
                                    $tmp = explode(",", $delivery_row->zone);
                                $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));

                            }

                            foreach($tmp as $zone_id) {
                                $exist_zone[] = array(
                                    'zone' => $zone_id
                                );
                            }

                            $get_pro_query = DB::table('jocom_product_package_product')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                            $pro_opt_id = array();
                            foreach($get_pro_query as $get_pro_row) {
                                $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                                $pro_qty_cal[$get_pro_row->product_opt] = 0;
                            }

                            // Get Total Price
                            $promo_price = 0;
                            $actual_price = 0;

                            $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->where('status', '=', 1)
                                ->whereIn('id', array_keys($pro_opt_id))->get();

                            foreach($price_query as $price_row) {
                                $tempgst = Product::getGstValue($price_row->id);

                                $actual_price += ($price_row->price * $pro_opt_id[$price_row->id] * $tempgst);
                                $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id] * $tempgst);
                                $pro_qty_cal[$price_row->id] = $price_row->qty;
                            }

                            $max_qty = false;
                            foreach($pro_qty_cal as $m_qty) {
                                if($max_qty === false)
                                    $max_qty = $m_qty;
                                if($max_qty > $m_qty)
                                    $max_qty = $m_qty;
                            }

                            if($actual_price == $promo_price)
                                $promo_price = 0;
                            else
                                $promo_price = number_format($promo_price,2);


                            $exist_price[] = array(
                                    'id' => "Null",
                                    'label' => "Null",
                                    'price' => number_format($actual_price,2),
                                    'promo_price' => $promo_price,
                                    'qty' => number_format($max_qty),
                                    'default' => 'TRUE',
                                );

                            $data['item'][] = array(
                                'sku' => $row->sku,
                                'seller' => 'Null',
                                'seller_logo' => 'Null',
                                'seller_logo_thumb' => 'Null',
                                'name' => $row->name,
                                'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                                'description' => $row->description,
                                'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                                'img_1' => $img{'1'},
                                'thumb_1' => $thumb{'1'},
                                'img_2' => $img{'2'},
                                'thumb_2' => $thumb{'2'},
                                'img_3' => $img{'3'},
                                'thumb_3' => $thumb{'3'},
                                'vid_1' => $vid1,
                                'qr_code' => $row->qrcode,
                                'zone_records' => sizeof($exist_zone),
                                'delivery_zones' => array($exist_zone),
                                'price_records' => sizeof($exist_price),
                                'price_options' => array('option' => $exist_price)
                            );
                        }
                    }
                } else {
                    $query = DB::table('jocom_products_category')
                                ->select('*')
                                ->where('category_parent', '=', $get['products_cat'])
                                ->where('status', '=', 1)
                                ->where('permission', '=', 1)
                                ->get();

                    // calculate latest date
                    $insert_date = DB::table('jocom_products_category')
                                    ->select('insert_date')
                                    ->orderBy('insert_date', 'DESC')
                                    ->where('category_parent', '=', $get['products_cat'])->first();

                    $modify_date = DB::table('jocom_products_category')
                                        ->select('modify_date')
                                        ->orderBy('modify_date', 'DESC')
                                        ->where('category_parent', '=', $get['products_cat'])->first();

                    // $timestamp_insert = $insert_date->insert_date;
                    // $timestamp_modify = $modify_date->modify_date;

                    $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                    $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                    if ($modify_date > $insert_date) {
                        $timestamp = $modify_date ;
                    } else {
                        $timestamp = $insert_date ;
                    }

                    $data['timestamp'] = $timestamp;
                    $data['record'] = count($query);
                    $data['category'] = 'YES';
                    $data['item'] = array();

                    $args = array();
                    $args['req'] = 'products';

                    foreach($query as $row) {
                        $category_parent = DB::table('jocom_products_category')
                                        ->select("category_name$lang", "category_name")
                                        ->where('id', '=', $row->category_parent)->first();

                        // for multi-level category, added by eugene
                        $sub_cat = DB::table('jocom_products_category')
                                            ->select('id')
                                            ->where('category_parent', '=', $row->id)->get();

                        $temp_subcat = "";
                        if (count($sub_cat) > 0)
                        {
                            $temp_subcat = "1";
                        }

                        // $temp_subcat = "";
                        // $counter = 0;
                        // foreach($sub_cat as $sub)
                        // {
                        //     if($counter == 0)
                        //     {
                        //         $temp_subcat = $temp_subcat . $sub->id ;
                        //         $counter++;
                        //     }
                        //     else
                        //     {
                        //         $temp_subcat = $temp_subcat . "," . $sub->id;
                        //     }
                        // }
                        // end for multi-level category, added by eugene

                        $cat_name_lang = $row->{"category_name".$lang};
                        if($row->{"category_name".$lang} == NULL) {
                            $cat_name_lang = $row->category_name;
                        }

                        $cat_parent_name = $category_parent->{"category_name".$lang};
                        if($category_parent->{"category_name".$lang} == NULL) {
                            $cat_parent_name = $category_parent->category_name;
                        }

                        // number of product, added by eugene
                        $rowcount = 0;
                        if ($temp_subcat == "")
                        {
                            $rowproduct = DB::select( DB::raw("SELECT * FROM jocom_product_and_package WHERE category REGEXP '(^|,)($row->id)(,|$)' AND status = 1") );
                            $rowcount = count($rowproduct);
                        } // end number of product

                        $cat_icon = '';
                        if($row->category_img != '') {
                            $cat_icon = asset('/').'images/category/' . $row->category_img;
                        }

                        $args['products_cat'] = $row->id;
                        $data['item'][] = array(
                            'id' => $row->id,
                            'cat_name' => $cat_name_lang,
                            'cat_name_sort' => $row->category_name,
                            // 'cat_description' => $row->category_descriptions,
                            'cat_parent' => $row->category_parent,
                            'cat_parent_name' => $cat_parent_name,
                            'p_count' => $rowcount,
                            'cat_icon' => $cat_icon,
                            // for multi-level category, added by eugene
                            'sub_cat' => $temp_subcat,
                            // end for multi-level category, added by eugene
                            // 'url' => 'feed/?' . http_build_query($args)
                        );
                    }

                }
            } else {
                $query = DB::table('jocom_products_category')->where('status', '=', 1)->where('permission', '=', 1)->get();

                // calculate latest date
                $insert_date = DB::table('jocom_products_category')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')->first();

                $modify_date = DB::table('jocom_products_category')
                                    ->select('modify_date')
                                    ->orderBy('modify_date', 'DESC')->first();

                $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                if ($modify_date > $insert_date) {
                    $timestamp = $modify_date ;
                } else {
                    $timestamp = $insert_date ;
                }

                $data['timestamp'] = $timestamp;
                $data['record'] = count($query);
                $data['category'] = 'YES';
                $data['item'] = array();

                $args = array();
                $args['req'] = 'products';

                foreach($query as $row) {
                    $category_parent = DB::table('jocom_products_category')
                                        ->select("category_name$lang", "category_name")
                                        ->where('id', '=', $row->category_parent)->first();

                    // for multi-level category, added by eugene
                    $sub_cat = DB::table('jocom_products_category')
                                        ->select('id')
                                        ->where('category_parent', '=', $row->id)->first();

                    $temp_subcat = "";
                    if (count($sub_cat) > 0)
                    {
                        $temp_subcat = "1";
                    }

                    // $temp_subcat = "";
                    // $counter = 0;
                    // foreach($sub_cat as $sub)
                    // {
                    //     if($counter == 0)
                    //     {
                    //         $temp_subcat = $temp_subcat . $sub->id ;
                    //         $counter++;
                    //     }
                    //     else
                    //     {
                    //         $temp_subcat = $temp_subcat . "," . $sub->id;
                    //     }
                    // }
                    // end for multi-level category, added by eugene

                    $cat_name_lang = $row->{"category_name".$lang};
                    if($row->{"category_name".$lang} == NULL) {
                        $cat_name_lang = $row->category_name;
                    }

                    $cat_parent_name = $category_parent->{"category_name".$lang};
                    if($category_parent->{"category_name".$lang} == NULL) {
                        $cat_parent_name = $category_parent->category_name;
                    }

                    // number of product, added by eugene
                    $rowcount = 0;
                    if ($temp_subcat == "")
                    {
                        $rowproduct = DB::select( DB::raw("SELECT * FROM jocom_product_and_package WHERE category REGEXP '(^|,)($row->id)(,|$)' AND status = 1") );
                        $rowcount = count($rowproduct);
                    } // end number of product

                    $cat_icon = '';
                    if($row->category_img != '') {
                        $cat_icon = asset('/').'images/category/' . $row->category_img;
                    }

                    $args['products_cat'] = $row->id;
                    $data['item'][] = array(
                        'id' => $row->id,
                        'cat_name' => $cat_name_lang,
                        'cat_name_sort' => $row->category_name,
                        // 'cat_description' => $row->category_descriptions,
                        'cat_parent' => $row->category_parent,
                        'cat_parent_name' => $cat_parent_name,
                        'p_count' => $rowcount,
                        'cat_icon' => $cat_icon,
                        // for multi-level category, added by eugene
                        'sub_cat' => $temp_subcat,
                        // end for multi-level category, added by eugene
                        // 'url' => 'feed/?' . http_build_query($args)
                    );
                }
            }
        }

        return array('xml_data' => $data);
    }

    /**
     * Display sellers feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeSeller_products_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";
        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $data['timestamp'] = '';
        $data['record'] = 0;
        $data['category'] = 'NO';
        $data['item'] = array();

        if(isset($get['code'])) {
            $query = DB::table('jocom_product_and_package')
                                ->select('*')
                                ->orderBy('modify_date', 'DESC')
                                ->where('sell_id', '=', $get['code'])
                                ->take($limit)
                                ->get();

            // calculate latest date
            $insert_date = DB::table('jocom_product_and_package')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')
                                ->where('sell_id', '=', $get['code'])->first();

            $modify_date = DB::table('jocom_product_and_package')
                                ->select('modify_date')
                                ->orderBy('modify_date', 'DESC')
                                ->where('sell_id', '=', $get['code'])->first();

            // $timestamp_insert = $insert_date->insert_date;
            // $timestamp_modify = $modify_date->modify_date;

            $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
            $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

            if ($modify_date > $insert_date) {
                $timestamp = $modify_date ;
            } else {
                $timestamp = $insert_date ;
            }

            $data['timestamp'] = $timestamp;
            $data['record'] = count($query);
            $data['category'] = 'NO';
            $data['item'] = array();

            $args = array();
            $args['req'] = 'seller_products';

            foreach($query as $row) {
                $qrcode = '';

                for($i = 1; $i < 4; $i++) {
                    $img{$i} = '';
                    $thumb{$i} = '';
                    if($row->{'img_' . $i} != '') {
                        $img{$i} = asset('/').'images/data/' . $row->{'img_' . $i};
                        $thumb{$i} = asset('/').'images/data/thumbs/' . $row->{'img_' . $i};
                    }
                }

                $vid1 = '';
                if($row->vid_1 != '') {
                    $vid1 = $row->vid_1;
                } else if($row->vid_1 != '')
                    $vid1 = $row->vid_1;

                $args['code'] = $row->qrcode;

                $exist_zone = array();
                $exist_price = array();
                if(substr($row->id, 0, 1) != 'P') {
                    // For Product

                    $delivery_query = DB::table('jocom_product_delivery')
                                ->select('*')
                                ->where('product_id', '=', $row->id)
                                ->get();

                    foreach($delivery_query as $delivery_row) {
                        $exist_zone[] = array(
                            'zone' => $delivery_row->zone_id
                        );
                    }

                    $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->where('product_id', '=', $row->id)->get();

                    foreach($price_query as $price_row) {
                        $exist_price[] = array(
                            'id' => $price_row->id,
                            'label' => $price_row->{'label'.$lang},
                            'price' => $price_row->price,
                            'promo_price' => $price_row->price_promo,
                            'qty' => number_format($price_row->qty),
                            'default' => ($price_row->default == 1 ? 'TRUE' : 'FALSE'),
                        );
                    }

                    $seller = DB::table('jocom_seller')
                                ->select('*')
                                ->where('id', '=', $row->sell_id)->first();

                    $product_cat = DB::table('jocom_products_category')
                                ->select('category_name')
                                ->where('id', '=', $row->category)->first();

                    $seller_file = '';
                    $seller_file_thumb = '';
                    if(isset($seller->file_name) && $seller->file_name != '') {
                        $seller_file = asset('/').'images/seller/' . $seller->file_name;
                        $seller_file_thumb = asset('/').'images/seller/thumbs/' . $seller->file_name;
                    }

                    $data['item'][] = array(
                        'sku' => $row->sku,
                        'seller' => $seller->company_name,
                        'seller_logo' => $seller_file,
                        'seller_logo_thumb' => $seller_file_thumb,
                        'name' => $row->name,
                        'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                        'description' => $row->description,
                        'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                        'img_1' => $img{'1'},
                        'thumb_1' => $thumb{'1'},
                        'img_2' => $img{'2'},
                        'thumb_2' => $thumb{'2'},
                        'img_3' => $img{'3'},
                        'thumb_3' => $thumb{'3'},
                        'vid_1' => $vid1,
                        'qr_code' => $row->qrcode,
                        'zone_records' => count($delivery_query),
                        'delivery_zones' => array($exist_zone),
                        'price_records' => sizeof($exist_price),
                        'price_options' => array('option' => $exist_price)
                    );

                } else {
                    // For Package
                    $pro_qty_cal = array();

                    $delivery_query = DB::table('jocom_package_delivery')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                    $tmp = array();
                    foreach($delivery_query as $k => $delivery_row) {
                        if($k == 0)
                            $tmp = explode(",", $delivery_row->zone);
                        $tmp = array_intersect($tmp, explode(",", $delivery_row->zone));

                    }

                    foreach($tmp as $zone_id) {
                        $exist_zone[] = array(
                            'zone' => $zone_id
                        );
                    }

                    $get_pro_query = DB::table('jocom_product_package_product')
                                ->select('*')
                                ->where('package_id', '=', substr($row->id, 1))->get();

                    $pro_opt_id = array();
                    foreach($get_pro_query->result() as $get_pro_row) {
                        $pro_opt_id[$get_pro_row->product_opt] = $get_pro_row->qty;
                        $pro_qty_cal[$get_pro_row->product_opt] = 0;
                    }

                    // Get Total Price
                    $promo_price = 0;
                    $actual_price = 0;

                    $price_query = DB::table('jocom_product_price')
                                ->select('*')
                                ->orderBy('default', 'DESC')
                                ->orderBy('price', 'ASC')
                                ->where('status', '=', 1)
                                ->whereIn('id', $pro_opt_id)->get();

                    $price_query = $this->db->query($sql);
                    foreach($price_query->result() as $price_row) {
                        $actual_price += ($price_row->price * $pro_opt_id[$price_row->id]);
                        $promo_price += (($price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $pro_opt_id[$price_row->id]);
                        $pro_qty_cal[$price_row->id] = $price_row->qty;
                    }

                    $max_qty = false;
                    foreach($pro_qty_cal as $m_qty) {
                        if($max_qty === false)
                            $max_qty = $m_qty;
                        if($max_qty > $m_qty)
                            $max_qty = $m_qty;
                    }

                    if($actual_price == $promo_price)
                        $promo_price = 0;

                    $exist_price[] = array(
                            'id' => "Null",
                            'label' => "Null",
                            'price' => $actual_price,
                            'promo_price' => $promo_price,
                            'qty' => number_format($max_qty),
                            'default' => 'TRUE',
                        );


                    $data['item'][] = array(
                        'sku' => $row->sku,
                        'seller' => 'Null',
                        'seller_logo' => 'Null',
                        'seller_logo_thumb' => 'Null',
                        'name' => $row->name,
                        'products_cat' => $row->category, //($product_cat != NULL) ? $product_cat->category_name : '',
                        'description' => $row->description,
                        'delivery_time' => ($row->delivery_time == '' ? '24 hours' : $row->delivery_time),
                        'img_1' => $img{'1'},
                        'thumb_1' => $thumb{'1'},
                        'img_2' => $img{'2'},
                        'thumb_2' => $thumb{'2'},
                        'img_3' => $img{'3'},
                        'thumb_3' => $thumb{'3'},
                        'vid_1' => $vid1,
                        'qr_code' => $row->qrcode,
                        'zone_records' => sizeof($exist_zone),
                        'delivery_zones' => array($exist_zone),
                        'price_records' => sizeof($exist_price),
                        'price_options' => array('option' => $exist_price)
                    );
                }
            }
        }
        return array('xml_data' => $data);
    }

    /**
     * Display news feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */

    public function scopeNews_feed($query, $param1, $count='', $from='', $get=array())
    {
        $lang   = 'en';
        $device = 'phone';
        $data   = array();
        $limit  = "";
        $count  = 250;

        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $where = "";

        if ($get['lang'] !== "") $lang = strtolower($get['lang']);
        if ($get['device'] !== "") $device = strtolower($get['device']);
        if($get['platform'] != "") {
            $platform = strtoupper($get['platform']);
        }else{
            $platform = Platform::JOCOM_APP_CODE;
        }

        $query = DB::table('jocom_latest_news')->select('jocom_latest_news.id', 'jocom_latest_news.pos', 'jocom_latest_news.url_link', 'jocom_latest_news.qrcode','jocom_latest_news.title','jocom_latest_news.description');
                                //->leftjoin('jocom_hot_item_images', 'jocom_hot_item_images.hot_id', '=', 'jocom_hot_items.id')
                                //->where('jocom_hot_item_images.language', '=', 'en')

                                // WHEN JUEPIN APP IS READY //
                                /*
                                switch ($platform) {
                                    case Platform::JOCOM_APP_CODE:
                                        $query->where('jocom_latest_news.is_jocom_app',"=",1);
                                        break;
                                    case Platform::JUEPIN_APP_CODE:
                                        $query->where('jocom_latest_news.is_juepin_app',"=",1);
                                        break;
                                    default:
                                        $query->where('jocom_latest_news.is_jocom_app',"=",1);
                                        break;
                                }
                                */
                                // WHEN JUEPIN APP IS READY //
        
        
                                $query = $query->groupby('jocom_latest_news.id');
                                $query = $query->orderBy('jocom_latest_news.pos', 'DESC')->get();
        
        $insert_date = DB::table('jocom_latest_news')
                            ->select('insert_date')
                            ->orderBy('insert_date', 'DESC')->first();

        $modify_date = DB::table('jocom_latest_news')
                            ->select('modify_date')
                            ->orderBy('modify_date', 'DESC')->first();

        $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
        $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        $data['timestamp']  = $timestamp;

        $data['item']       = array();
        $args               = array();
        $args['req']        = 'news';
        $image_path         = "";
        $thumb_path         = "";
        $thumb_width        = 320;
        $thumb_height       = 220;

        switch ($device) {
            case 'phone':
                $image_path         = Config::get('constants.LATESTNEWS_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.LATESTNEWS_THUMB_FILE_PATH');  //'./public/images/banner/thumbs/';
                break;
            
            case 'tablet':
                $image_path         = Config::get('constants.LATESTNEWS_TAB_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.LATESTNEWS_TAB_THUMB_FILE_PATH');  //'./public/images/banner/thumbs/';
                break;
        }

        if(count($query) > 0) {
            foreach ($query as $row) {
                $file_name  = "";
                $thumb_name = "";
                $images     = Latestnews::getImagesByLanguage($row->id, $device, $lang);

                if(count($images) > 0) {
                    $file_name  = $image_path . $lang . "/" . $images->file_name;
                    $thumb_name = $thumb_path . $lang . "/" . $images->thumb_name;
                }
                else {
                    $default_images = Latestnews::getImagesByLanguage($row->id, "phone", "en");

                    if(count($default_images) > 0) {
                        $file_name      = Config::get('constants.LATESTNEWS_FILE_PATH') . "en/" . $default_images->file_name;
                        $thumb_name     = Config::get('constants.LATESTNEWS_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                        
                    }
                }

                if ($file_name != "" && file_exists('./' . $file_name)) {
                    if(!file_exists('./' . $thumb_name)) {
                        create_thumbnail($file_name, $thumb_width, $thumb_height, './' . $image_path, './' . $thumb_path);
                    }

                    $data['item'][] = array(
                        'id'        => $row->id,
                        'img'       => Image::link($file_name),
                        'thumbnail' => Image::link($thumb_name),
                        'url'       => $row->url_link,
                        'qrcode'    => $row->qrcode,
                        'title'     => $row->title,
                        'description' => $row->description,
                    );
                    $count++;
                }
            }
             $data['record']     = $count;
        } else $data['status_msg'] = '#601';

        return array('xml_data' => $data);
    }

    /**
     * Display zone feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeZone_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";
        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        if( isset($get['code']) ) {
            $zone_id = trim($get['code']);

            $query = DB::table('jocom_zones')
                                ->select('*')
                                ->take($limit)
                                ->where('id', '=', $zone_id)->get();

            if(count($query) > 0) {
                foreach($query as $row) {
                    $insert_date = DB::table('jocom_zones')
                                        ->select('insert_date')
                                        ->orderBy('insert_date', 'DESC')->first();

                    $modify_date = DB::table('jocom_zones')
                                        ->select('modify_date')
                                        ->orderBy('modify_date', 'DESC')->first();

                    // $timestamp_insert = $insert_date->insert_date;
                    // $timestamp_modify = $modify_date->modify_date;

                    $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
                    $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

                    if ($modify_date > $insert_date) {
                        $timestamp = $modify_date ;
                    } else {
                        $timestamp = $insert_date ;
                    }

                    $query2 = DB::table('jocom_countries')
                                ->select('*')
                                ->where('id', '=', $row->country_id)->get();

                    $data['timestamp'] = $timestamp;
                    $data['record'] = count($query2);
                    $data['category'] = 'NO';
                    $data['zone_name'] = $row->name;
                    $data['zone_weight'] = $row->weight;
                    $data['item'] = array();

                    $avail_zone_state_id = array();

                    $zone_state_query = DB::table('jocom_zone_states')
                                ->select('*')
                                ->where('zone_id', '=', $row->id)
                                ->get();

                    foreach($zone_state_query as $zone_state_row) {
                        $avail_zone_state_id[] = $zone_state_row->states_id;
                    }

                    foreach($query2 as $row2) {
                        $states = array();

                        $sql = DB::select( DB::raw("SELECT * FROM `jocom_country_states` WHERE `country_id` = " . $row->country_id . " AND `status` = 1 AND `id` IN (" . implode(", ", $avail_zone_state_id) . ")") );

                        foreach($sql as $state_row) {
                            $states[] = array(
                                'state' => array(array(
                                    'id' => $state_row->id,
                                    'name' => $state_row->name,
                                ))
                            );
                        }

                        $data['item'][] = array(
                            'country_id' => $row2->id,
                            'country_name' => $row2->name,
                            'states_records' => sizeof($states),
                            'states' => array($states)
                        );
                    }
                }
            }
        } else {
            $query = DB::table('jocom_zones')
                            ->select('*')
                            ->take($limit)->get();

            // calculate latest date
            $insert_date = DB::table('jocom_zones')
                                ->select('insert_date')
                                ->orderBy('insert_date', 'DESC')->first();

            $modify_date = DB::table('jocom_zones')
                                ->select('modify_date')
                                ->orderBy('modify_date', 'DESC')->first();

            // $timestamp_insert = $insert_date->insert_date;
            // $timestamp_modify = $modify_date->modify_date;

            $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
            $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

            if ($modify_date > $insert_date) {
                $timestamp = $modify_date ;
            } else {
                $timestamp = $insert_date ;
            }

            $data['timestamp'] = $timestamp;
            $data['record'] = count($query);
            $data['zone'] = 'YES';
            $data['item'] = array();

            foreach($query as $row) {

                $data['item'][] = array(
                    'id' => $row->id,
                    'name' => $row->name
                );

            }

        }
        return array('xml_data' => $data);
    }

    /**
     * Display product category feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeProducts_cat_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";
        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $search_product_cat_key = "#None#";
        if( isset($get['code']) && strlen($get['code']) >= 3 ) {
            $search_product_cat_key = $get['code'];
        }

        $query = DB::table('jocom_products_category')
                                ->select('*')
                                ->take($limit)
                                ->where('category_name', 'LIKE', "%".$search_product_cat_key."%")->get();

        $insert_date = DB::table('jocom_products_category')
                            ->select('insert_date')
                            ->orderBy('insert_date', 'DESC')
                            ->where('category_name', 'LIKE', "%".$search_product_cat_key."%")->first();

        $modify_date = DB::table('jocom_products_category')
                            ->select('modify_date')
                            ->orderBy('modify_date', 'DESC')
                            ->where('category_name', 'LIKE', "%".$search_product_cat_key."%")->first();

        // $timestamp_insert = $insert_date->insert_date;
        // $timestamp_modify = $modify_date->modify_date;

        $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
        $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        $data['timestamp'] = $timestamp;
        $data['tot_record'] = count($insert_date);
        $data['record'] = count($query);
        $data['category'] = 'YES';
        $data['item'] = array();

        $args = array();

        foreach($query as $row) {
            $category_parent = DB::table('jocom_products_category')
                                ->select('category_name')
                                ->take($limit)
                                ->where('id', '=', $row->category_parent)->first();

            $args['products_cat'] = $row->id;
            $data['item'][] = array(
                'id' => $row->id,
                'cat_name' => $row->category_name,
                'cat_description' => $row->category_descriptions,
                'cat_parent' => $row->category_parent,
                'cat_parent_name' => ($category_parent != NULL) ? $category_parent->category_name : '',
            );
        }

        return array('xml_data' => $data);
    }

    /**
     * Display hot items feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeHot_feed($query, $param1, $count='', $from='', $get=array())
    {
        
        try{
            
        
        $lang   = 'en';
        $device = 'phone';
        $data   = array();
        $limit  = "";
        $count  = 0;
        $v3App = false;
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'HOT_ITEM';
        $ApiLog->data = json_encode($get);
        $ApiLog->save();

        // App checking version
        if($get['vapp'] != ""){
            $vApp = $get['vapp'];
            if($vApp >= 3.0){
                $v3App = true;
            }
        }

        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $where = "";

        if ($get['lang'] !== "")    $lang = strtolower($get['lang']);
        if ($get['device'] !== "")  $device = strtolower($get['device']);
        if ($get['platform'] != "") {
            $platform = strtoupper($get['platform']);
        }else{
            $platform = Platform::JOCOM_APP_CODE;
        }

       $region_id = "";
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyBPcKtMmHfljZFsJSHy4wuzp5vO7NNwGVo';
            $json = file_get_contents($url);
            $test=json_decode($json);
            //print_r($test);
            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];

            $country_name = $response[1];

            if ($region_name== "Wilayah Persekutuan Kuala Lumpur" OR $region_name== "Wilayah Persekutuan Putrajaya" or  $region_name== "Wilayah Persekutuan Labuan") {
                
                $string = explode (' ', $region_name, 3);
                $name2 = "WP-".$string[2];
            }
            else{
                $name2 = $region_name;
            }
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();
            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
            $region_id = $regions->region_id;

        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
            if($stateid == RegionController::KLANGVALLEYSTATEID){
                $region_id = RegionController::KLANGVALLEYREGIONID;
            }else{
                $stateidInfo = State::find($stateid);
                $region_id = $stateidInfo->region_id;
            }
        }
        
        // FOR NEW APP
        
        if($v3App){
            
            $query = DB::table('jocom_hot_items')->select('jocom_hot_items.id', 'jocom_hot_items.pos', 'jocom_hot_items.url_link', 'jocom_hot_items.qrcode');
                                //->leftjoin('jocom_hot_item_images', 'jocom_hot_item_images.hot_id', '=', 'jocom_hot_items.id')
                                //->where('jocom_hot_item_images.language', '=', 'en')
            // WHEN JUEPIN APP IS READY //
            /*
            switch ($platform) {
                case Platform::JOCOM_APP_CODE:
                    $query->where('jocom_hot_items.is_jocom_app',"=",1);
                    break;
                case Platform::JUEPIN_APP_CODE:
                    $query->where('jocom_hot_items.is_juepin_app',"=",1);
                    break;
                default:
                    $query->where('jocom_hot_items.is_jocom_app',"=",1);
                    break;
            }
            */
            // WHEN JUEPIN APP IS READY //
            if($region_id == 2 || $region_id ==  3){
                $query = $query->whereIn('jocom_hot_items.region_id',[$region_id,0]);
            }else{
                $query = $query->whereIn('jocom_hot_items.region_id',[$region_id,0]);
            }
            

        }else{
            
            $query = DB::table('jocom_hot_items')->select('jocom_hot_items.id', 'jocom_hot_items.pos', 'jocom_hot_items.url_link', 'jocom_hot_items.qrcode');
                //->leftjoin('jocom_hot_item_images', 'jocom_hot_item_images.hot_id', '=', 'jocom_hot_items.id')
                //->where('jocom_hot_item_images.language', '=', 'en')
        }
        
            $query = $query->groupby('jocom_hot_items.id');
            $query = $query->orderBy('jocom_hot_items.pos', 'DESC')->get();
                                

        $insert_date = DB::table('jocom_hot_items')
                            ->select('insert_date')
                            ->orderBy('insert_date', 'DESC')->first();

        $modify_date = DB::table('jocom_hot_items')
                            ->select('modify_date')
                            ->orderBy('modify_date', 'DESC')->first();

        $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
        $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        $data['timestamp']  = $timestamp;

        $data['item']       = array();
        $args               = array();
        $args['req']        = 'hot';
        $image_path         = "";
        $thumb_path         = "";
        $thumb_width        = 320;
        $thumb_height       = 220;

        switch($device) {
            case 'phone' :
                $image_path         = Config::get('constants.HOTITEM_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.HOTITEM_THUMB_FILE_PATH');  //'./public/images/banner/thumbs/';
                break;

            case 'tablet' :
                $image_path         = Config::get('constants.HOTITEM_TAB_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.HOTITEM_TAB_THUMB_FILE_PATH');  //'./public/images/banner/thumbs/';
                break;
        }

        if(count($query) > 0) {
            foreach ($query as $row) {
                $file_name  = "";
                $thumb_name = "";
                $images     = HotItem::getImagesByLanguage($row->id, $device, $lang);

                if(count($images) > 0) {
                    $file_name  = $image_path . $lang . "/" . $images->file_name;
                    $thumb_name = $thumb_path . $lang . "/" . $images->thumb_name;
                }
                else {
                    $default_images = HotItem::getImagesByLanguage($row->id, "phone", "en");

                    if(count($default_images) > 0) {
                        $file_name      = Config::get('constants.HOTITEM_FILE_PATH') . "en/" . $default_images->file_name;
                        $thumb_name     = Config::get('constants.HOTITEM_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                    }
                }

                if ($file_name != "" && file_exists('./' . $file_name)) {
                    if(!file_exists('./' . $thumb_name)) {
                        create_thumbnail($file_name, $thumb_width, $thumb_height, './' . $image_path, './' . $thumb_path);
                    }
                    
                    $cleanqrcode = str_replace(" ","",$row->qrcode);
                    $QRCODE  = explode(",",$cleanqrcode);
                    
                    // $recordProduct = DB::table('jocom_products AS JP')
                    //     ->select('JP.qrcode')
                    //     ->where('JP.status',1)
                    //     ->whereIn('JP.qrcode', $QRCODE)
                    //     ->where('JP.region_id',$region_id)->toSql();
                        
                    // $ApiLog = new ApiLog ;
                    // $ApiLog->api = 'HOT_ITEM_RESPONSE_PRODUCT';
                    // $ApiLog->data = json_encode($QRCODE);
                    // $ApiLog->save();
                        
                    // $ApiLog = new ApiLog ;
                    // $ApiLog->api = 'HOT_ITEM_RESPONSE_PRODUCT_V2';
                    // $ApiLog->data = json_encode($recordProduct);
                    // $ApiLog->save();

                    $data['item'][] = array(
                        'id'        => $row->id,
                        'img'       => Image::link($file_name),
                        'thumbnail' => Image::link($thumb_name),
                        'url'       => $row->url_link,
                        'qrcode'    => $row->qrcode
                    );
                    $count++;
                }
            }

            $data['record']     = $count;


        } else $data['status_msg'] = '#701';
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'HOT_ITEM_RESPONSE';
        $ApiLog->data = json_encode($data);
        $ApiLog->save();
        return array('xml_data' => $data);
        
        }catch(Exception $ex){
            
            echo $ex->getMessage();
            
            $ApiLog = new ApiLog ;
            $ApiLog->api = 'HOT_ITEM_RESPONSE_ERROR';
            $ApiLog->data = $ex->getMessage();
            $ApiLog->save();
            
        }
    }
    
    /**
     * Display hot items feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeComment_feed($query, $param1, $count='', $from='', $get=array()) {
        $data = array();
        $limit = "";
        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        if( isset($get['code']) ) {
            $query = DB::table('jocom_comments')
                                ->leftJoin('jocom_user AS JU', 'JU.id', '=', 'jocom_comments.user_id')
                                ->select('*')
                                ->take($count)
                                ->skip($from)
                                ->groupBy('user_id')
                                ->orderBy('comment_date', 'DESC')
                                ->where('product_id', '=', $get['code'])->get();
        } else {
            $query = DB::table('jocom_comments')
                                ->select('*')
                                ->take($count)
                                ->skip($from)
                                ->orderBy('comment_date', 'DESC')->get();
        }

        $insert_date = DB::table('jocom_comments')
                            ->select('insert_date')
                            ->orderBy('insert_date', 'DESC')->first();

        $modify_date = DB::table('jocom_comments')
                            ->select('modify_date')
                            ->orderBy('modify_date', 'DESC')->first();

        // $timestamp_insert = $insert_date->insert_date;
        // $timestamp_modify = $modify_date->modify_date;

        $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
        $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        $data['timestamp'] = $timestamp;
        $data['record'] = count($query);
        $data['tot_record'] = count($query);
        $data['item'] = array();

        $args = array();
        $inv_buyer = array();
        foreach($query as $row)
            $inv_buyer[] = $row->user_id;
        if(sizeof($inv_buyer) == 0)
            $inv_buyer[] = 0;

        $buyer_data = array();
        $buyer_query = DB::select( DB::raw("SELECT * FROM `jocom_user` WHERE `id` IN (" . implode(", ", $inv_buyer) . ")") );
        //print_r($buyer_query);
        foreach($buyer_query as $row)
            $buyer_data[$row->id] = $row;
        foreach($query as $row) {

            $full_name = isset($buyer_data[$row->user_id]) ? $buyer_data[$row->user_id]->full_name : '';
            $user_name = isset($buyer_data[$row->user_id]) ? $buyer_data[$row->user_id]->username : '';
            $data['item'][] = array(
                'id' => $row->id,
                'full_name' => $full_name,
                'user_name' => $user_name,
                'product_id' => $row->product_id,
                'comment_date' => $row->comment_date,
                'comment' => $row->comment,
                'rating' => $row->rating
            );
        }
        return array('xml_data' => $data);
    }

    public function scopeGet_version($query, $input)
    {
        $data       = array();
        $app        = DB::table('app_version')->first();

        if(isset($input)) {
            switch ($input) {
                case 'iphone':
                    $data['item'][] = array(
                        'version'   => $app->iphone,
                    );
                    break;

                case 'ipad':
                    $data['item'][] = array(
                        'version'   => $app->ipad,
                    );
                    break;

                case 'android':
                    $data['item'][] = array(
                        'version'   => $app->android,
                    );
                    break;

                case 'tablet':
                    $data['item'][] = array(
                        'version'   => $app->tablet,
                    );
                    break;
            }

        } else $data['status_msg'] = '#1001';
        // else $data['error'] = 'Sorry. No data found!';

        // var_dump($data);
        return array('xml_data' => $data);
    }
    
    public function scopeGetnewversionLogistic($query, $input){
        $data  = array();

        $app   =  DB::table('appversion_logistic')
                      ->where('apptype','=',$input)
                      ->where('default','=',1)  
                      ->first();

           if(count($app)>0){
            
            if($app->installer_filename != ''){
                $path_file = Config::get('constants.LOGISTIC_APP_INSTALLER')."/".$app->installer_filename;
                $url = Image::link($path_file);   
            }else{
               $url = '';
            }   
            
            
               
            $data['item'][] = array(
                        'version'   => $app->version,
                        'feature'   => $app->features,
                        'download_url'   => $url,
                    );  

           }
           else {
             $data['status_msg'] = '#1001';
           }           
   
        return array('xml_data' => $data);         

    }
    
    public function scopeGetnewversion($query, $input){
        $data  = array();

        $app   =  DB::table('appversion')
                      ->where('apptype','=',$input)
                      ->where('default','=',1)  
                      ->first();

           if(count($app)>0){
            $data['item'][] = array(
                        'version'   => $app->version,
                        // 'feature'   => $app->features,
                    );  

           }
           else {
             $data['status_msg'] = '#1001';
           }           

        return array('xml_data' => $data);         

    }
    
    function scopeBannertemplatenew_feed($query, $param1, $count='', $from='', $get=array()){

        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
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

            $stateidInfo = State::find($stateid);
            $regions_id = $stateidInfo->region_id;
            
        }

        if ($regions_id !='') {

           $bannermasters = DB::table('jocom_managebanners_new')
                        ->select('id','type','device',DB::raw("CONVERT(seq,UNSIGNED INTEGER) AS num"))
                        ->where('region_id',$regions_id)
                        ->where('active_status','=',1)
                        ->orderBy('num','asc')
                        ->get();

        }else{
            $bannermasters = DB::table('jocom_managebanners_new')
                        ->select('id','type','device',DB::raw("CONVERT(seq,UNSIGNED INTEGER) AS num"))
                        ->where('region_id',1)
                        ->where('active_status','=',1)
                        ->orderBy('num','asc')
                        ->get();
        }    
        if(!empty($bannermasters)){
            foreach ($bannermasters as $bannermaster) {
                
                $banners = DB::table('jocom_managebanners_images_new AS JMI')
                        ->leftjoin('jocom_managebanners_new AS JM', 'JM.id','=', 'JMI.banner_id')
                        ->where('JMI.banner_id','=',$bannermaster->id)
                        ->where('JMI.active_status','=',1)
                        ->select('JMI.file_name','JMI.qrcode','JMI.max_width','JMI.max_height','JMI.banner_seq','JMI.language')
                        //->orderBy('JMI.banner_seq','asc')
                        ->get();

                $array_banners['banner'] ="";     
                $Bcounter = 0;
                foreach ($banners as $banner) {

                    $file_name = Config::get('constants.NEW_BANNER_FILE_PATH').$banner->file_name;
    
                            $array_banners['banner'][] = array(
                                'file_name' => Image::link($file_name),
                                'max_width' => $banner->max_width,
                                'max_height'=> $banner->max_height,
                                'banner_seq'=> $banner->banner_seq,
                                'qrcode'    => $banner->qrcode,
                                
                            );

                        }
                        
                    $data['item'][] = array(
                        'id'        => $bannermaster->id,
                        'type'      => $bannermaster->type,
                        'device'    => $bannermaster->device,
                        'layout'    => array($array_banners),
                     );   
                    
            }
        }else{
            $data['item'][] = array();
            
        }

        return array('xml_data' => $data);
    }
    
    public function scopePopup_feed($query, $param1, $count='', $from='', $get=array()) {
       
        
        $data = array();
        $limit = "";
        
        $finalList = array();
        
        
        $current_date = date("Y-m-d h:i:s");
        
        $from_date = date("Y-m-d") .' 00:00:00';
        
        $region_id = "";
        
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
                }
            
              }
              
            $region_name = $response[0];

            $country_name = $response[1];

            if ($region_name== "Wilayah Persekutuan Kuala Lumpur" OR $region_name== "Wilayah Persekutuan Putrajaya" or  $region_name== "Wilayah Persekutuan Labuan") {
                
                $string = explode (' ', $region_name, 3);
                $name2 = "WP-".$string[2];
            }
            else{
                $name2 = $region_name;
            }
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();
            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
            
            $country_id = $country->id;
            $region_id = $regions->region_id;
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
            if($stateid == RegionController::KLANGVALLEYSTATEID){
                $region_id = RegionController::KLANGVALLEYREGIONID;
                $country_id = 458;
            }else{
                $stateidInfo = State::find($stateid);
                $region_id = $stateidInfo->region_id;
                $country_id = $stateidInfo->country_id;
            }
        }
        
        $popup  = DB::table('jocom_popup_banner')
                        ->where('status', '=', 1)
                        ->where('to_date','<=',$current_date)
                        ->where('country_id','>=',$country_id)
                        ->whereIn('region_id',[0,$region_id])
                        ->where('from_date','>=',$current_date)
                        ->get();
                        
            //  print_r($from_date);           
       
//        
        $directoryPath =  Config::get('constants.BANNER_POPUP');
        
            if(count($popup) > 0 ){
                // echo 'ok';
                
                foreach ($popup as $key => $value) {
                
                $listImage = array();
                
                
                
                for($x=1;$x<=5;$x++){
                  
                    $imageProperty = 'image'."_".$x;
                    if($value->$imageProperty != ''){
                        $listImage['image'][]['url'] = str_replace('', '_', Image::link($directoryPath.$value->$imageProperty));
                        
                        //$listImage['image'][] = 'sasa';
                    }
                }
                
                $subLine = array(
                    "id" => $value->id,
                    "title" => $value->description,
                    "qr_code" => $value->qr_code,
                    "category_id" => $value->category_id,
                    "from_date" => $value->from_date,
                    "to_date" => $value->to_date,
                    "activation" => $value->activation,
                    "images" => $listImage,
                );
                
                $finalList['popup'][] = $subLine;
                
            }

            $data['popup'] = $finalList['popup'];
            
        }else{
            $data['popup'] = [];
        }
        
       
        return array('xml_data' => $data);
        
        
    }
    
    public function scopeJcmpopup_feed($query, $param1, $count='', $from='', $get=array()) {
       
        
        $data = array();
        $limit = "";
        
        $finalList = array();
        
        // echo $from;
        $current_date = date("Y-m-d") .' 23:59:59';;
        
        if($from !=''){
            
          $from_date = $from .' 00:00:00';  
        }
        else 
        {
        $from_date = date("Y-m-d") .' 00:00:00';
        }
        
        $region_id = "";
        
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
                }
            
              }
              
            $region_name = $response[0];

            $country_name = $response[1];

            if ($region_name== "Wilayah Persekutuan Kuala Lumpur" OR $region_name== "Wilayah Persekutuan Putrajaya" or  $region_name== "Wilayah Persekutuan Labuan") {
                
                $string = explode (' ', $region_name, 3);
                $name2 = "WP-".$string[2];
            }
            else{
                $name2 = $region_name;
            }
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();
            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
            
            $country_id = $country->id;
            $region_id = $regions->region_id;
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
            if($stateid == RegionController::KLANGVALLEYSTATEID){
                $region_id = RegionController::KLANGVALLEYREGIONID;
                $country_id = 458;
            }else{
                $stateidInfo = State::find($stateid);
                $region_id = $stateidInfo->region_id;
                $country_id = $stateidInfo->country_id;
            }
        }
        
        $popup  = DB::table('jocom_popup_banner')
                        ->where('status', '=', 1)
                        ->where('country_id','=',$country_id)
                        ->whereIn('region_id',[0,$region_id])
                        ->where('from_date','<=', $from_date)
                        ->where('to_date','>=', $current_date)
                        ->get();
                        
             //print_r($current_date);           
       
//        
        $directoryPath =  Config::get('constants.BANNER_POPUP');
        
            if(count($popup) > 0 ){
                // echo 'ok';
                
                foreach ($popup as $key => $value) {
                
                $listImage = array();
                
                
                
                for($x=1;$x<=5;$x++){
                  
                    $imageProperty = 'image'."_".$x;
                    if($value->$imageProperty != ''){
                        $listImage['image'][]['url'] = str_replace('', '_', Image::link($directoryPath.$value->$imageProperty));
                        
                        //$listImage['image'][] = 'sasa';
                    }
                }
                
                $subLine = array(
                    "id" => $value->id,
                    "title" => $value->description,
                    "qr_code" => $value->qr_code,
                    "category_id" => $value->category_id,
                    "from_date" => $value->from_date,
                    "to_date" => $value->to_date,
                    "activation" => $value->activation,
                    "images" => $listImage,
                );
                
                $finalList['popup'][] = $subLine;
                
            }

            $data['popup'] = $finalList['popup'];
            
        }else{
            $data['popup'] = [];
        }
        
       
        return array('xml_data' => $data);
        
        
    }
    
    public function scopejcmpopupstagging_feed($query, $param1, $count='', $from='', $get=array()) {
       
        
        $data = array();
        $limit = "";
        
        $finalList = array();
        
        // echo $from;
        $current_date = '2021-12-10 23:59:59';//date("Y-m-d") .' 23:59:59';
        
        if($from !=''){
            
          $from_date = $from .' 00:00:00';  
        }
        else 
        {
        $from_date = date("Y-m-d") .' 00:00:00';
        }
        
        $region_id = "";
        
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
                }
            
              }
              
            $region_name = $response[0];

            $country_name = $response[1];

            if ($region_name== "Wilayah Persekutuan Kuala Lumpur" OR $region_name== "Wilayah Persekutuan Putrajaya" or  $region_name== "Wilayah Persekutuan Labuan") {
                
                $string = explode (' ', $region_name, 3);
                $name2 = "WP-".$string[2];
            }
            else{
                $name2 = $region_name;
            }
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();
            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
            
            $country_id = $country->id;
            $region_id = $regions->region_id;
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
            if($stateid == RegionController::KLANGVALLEYSTATEID){
                $region_id = RegionController::KLANGVALLEYREGIONID;
                $country_id = 458;
            }else{
                $stateidInfo = State::find($stateid);
                $region_id = $stateidInfo->region_id;
                $country_id = $stateidInfo->country_id;
            }
        }
        
        $popup  = DB::table('jocom_popup_banner')
                        ->where('status', '=', 1)
                        ->where('to_date','<=',$current_date)
                        ->where('country_id','>=',$country_id)
                        ->whereIn('region_id',[0,$region_id])
                        ->where('from_date','>=',$from_date)
                        ->get();
                        
            //  print_r($from_date);           
       
//        
        $directoryPath =  Config::get('constants.BANNER_POPUP');
        
            if(count($popup) > 0 ){
                // echo 'ok';
                
                foreach ($popup as $key => $value) {
                
                $listImage = array();
                
                
                
                for($x=1;$x<=5;$x++){
                  
                    $imageProperty = 'image'."_".$x;
                    if($value->$imageProperty != ''){
                        $listImage['image'][]['url'] = str_replace('', '_', Image::link($directoryPath.$value->$imageProperty));
                        
                        //$listImage['image'][] = 'sasa';
                    }
                }
                
                $subLine = array(
                    "id" => $value->id,
                    "title" => $value->description,
                    "qr_code" => $value->qr_code,
                    "category_id" => $value->category_id,
                    "from_date" => $value->from_date,
                    "to_date" => $value->to_date,
                    "activation" => $value->activation,
                    "images" => $listImage,
                );
                
                $finalList['popup'][] = $subLine;
                
            }

            $data['popup'] = $finalList['popup'];
            
        }else{
            $data['popup'] = [];
        }
        
       
        return array('xml_data' => $data);
        
        
    }
        function scopeBannertemplatelatest_feed($query, $param1, $count='', $from='', $get=array()){

        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false';
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

            $stateidInfo = State::find($stateid);
            $regions_id = $stateidInfo->region_id;
            
        }

        if ($regions_id !='') {

           $bannermasters = DB::table('jocom_managebanners_new')
                        ->select('id','type','device',DB::raw("CONVERT(seq,UNSIGNED INTEGER) AS num"))
                        ->where('region_id',$regions_id)
                        ->where('active_status','=',1)
                        ->orderBy('num','asc')
                        ->get();

        }else{
            $bannermasters = DB::table('jocom_managebanners_new')
                        ->select('id','type','device',DB::raw("CONVERT(seq,UNSIGNED INTEGER) AS num"))
                        ->where('region_id',1)
                        ->where('active_status','=',1)
                        ->orderBy('num','asc')
                        ->get();
        }    
        if(!empty($bannermasters)){
            foreach ($bannermasters as $bannermaster) {
                
                $banners = DB::table('jocom_managebanners_images_new AS JMI')
                        ->leftjoin('jocom_managebanners_new AS JM', 'JM.id','=', 'JMI.banner_id')
                        ->where('JMI.banner_id','=',$bannermaster->id)
                        ->where('JMI.active_status','=',1)
                        ->select('JMI.file_name','JMI.qrcode','JMI.max_width','JMI.max_height','JMI.banner_seq','JMI.language')
                        //->orderBy('JMI.banner_seq','asc')
                        ->get();

                $array_banners['banner'] ="";     
                $Bcounter = 0;
                foreach ($banners as $banner) {

                    $file_name = Config::get('constants.NEW_BANNER_FILE_PATH').$banner->file_name;
    
                            $array_banners = array(
                                'id'        => $bannermaster->id,
                                'type'      => $bannermaster->type,
                                'device'    => $bannermaster->device,
                                'file_name' => Image::link($file_name),
                                'max_width' => $banner->max_width,
                                'max_height'=> $banner->max_height,
                                'banner_seq'=> $banner->banner_seq,
                                'qrcode'    => $banner->qrcode,
                                
                            );

                        }
                        
                    $data['item'][] =$array_banners;   
                    
            }
        }else{
            $data['item'][] = array();
            
        }

        return array('xml_data' => $data);
    }
    /**
     * Listing for transaction
     * @return [type] [description]
     */
    public function scopeTransaction_feeds($query, $req, $count='', $from='', $get=array())
    {
        
        try{
            
       //DB::enableQueryLog();
        $error = false;
        if( !isset($get['buyer']) )
        {
            $error = true;
        }
        else
        {
            $buyer = Customer::where('username', '=', $get['buyer'])->first();
            if ($buyer == null)
            {
                $error = true;
            }
        }

        if($error === true)
            return array('xml_data' => array('status_msg' => '#401'));
            
        $data = array();
        $limit = "";
        if($count !== false && is_numeric($count)) {
            $limit =$count;
            if($from !== false && is_numeric($from))
                $limit = $from . "," . $count;
        }

        $where = " WHERE status in ('completed', 'refund') ";
        if( isset($get['buyer']) ) {
            $where .= " AND `buyer_username` = " . General::escape($get['buyer']);
        }
        $user_id=DB::table('jocom_user')->select('id')->where('username','=',$get['buyer'])->first();
        $orders=Transaction::select('id')->where('buyer_id','=',$user_id->id)->whereIn('status',['completed','refund'])->get()->toArray();
        $query=Transaction::select('id','transaction_date','total_amount','gst_total','parcel_status','buyer_username','delivery_charges','process_fees','gst_rate','status')
              ->whereIn('status',['completed','refund'])
              ->where('buyer_id','=',$user_id->id)
              ->whereIn('id',$orders)
              ->orderBy('id', 'desc')
              ->take($limit)
              ->get();
             
        //$totalquery = Transaction::where('buyer_id','=',$user_id->id)->whereIn('status',['completed','refund'])->count();
    
        $data['record'] = count($query);
        $data['total_record'] = count($orders);
        $data['item'] = array();


        foreach($query as $row)
        {
            // For the package
            $group_product_price = array();

            $details = array();
            
            $query_2 = DB::table('jocom_transaction_details AS JTD')
                ->select('JTD.product_group','JTD.price','JTD.unit','JTD.delivery_time','JTD.total','JTD.gst_amount','JP.img_1','JP.img_2','JP.img_3','JP.id','JP.name','JP.sku','JP.qrcode')
                ->leftJoin('jocom_products AS JP', 'JP.id', '=', 'JTD.product_id')    
                ->where('JTD.transaction_id','=',$row->id)
                ->get();

            foreach($query_2 as $row_2)
            {
                if($row_2->product_group != '')
                {
                    if(!isset($group_product_price[$row_2->product_group]))
                        $group_product_price[$row_2->product_group] = 0;
                    $group_product_price[$row_2->product_group] += ($row_2->price * $row_2->unit);

                    if(!isset($group_product_gst[$row_2->product_group]))
                        $group_product_gst[$row_2->product_group] = 0;
                    $group_product_gst[$row_2->product_group] += ($row_2->gst_amount);
                    continue;
                }
                
                $image1 = ( ! empty($row_2->img_1)) ? Image::link("images/data/thumbs/{$row_2->img_1}") : '';
                $image2 = ( ! empty($row_2->img_2)) ? Image::link("images/data/thumbs/{$row_2->img_2}") : '';
                $image3 = ( ! empty($row_2->img_3)) ? Image::link("images/data/thumbs/{$row_2->img_3}") : '';
                
                $details[] = array(
                    'id' => $row_2->id,
                    'name'  => $row_2->name,
                    'img_1' => $image1,
                    'img_2' => $image2,
                    'img_3' => $image3,
                    'sku' => $row_2->sku,
                    'qrcode' => $row_2->qrcode,
                    'price' => number_format($row_2->price,2,'.',''),
                    'unit' => $row_2->unit,
                    'gst_amount' => number_format($row_2->gst_amount,2,'.',''),
                    'delivery_time' => $row_2->delivery_time,
                    'total' => number_format($row_2->total,2,'.','')
                );
            }

            // Get The Transaction Product For Package
            $trans_detail_group_query = DB::select('SELECT a.id,a.sku,b.qrcode,a.unit,b.delivery_time, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_products` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ' . $row->id);
            foreach($trans_detail_group_query as $pack_row)
            {
                $details[] = array(
                    'id' => $pack_row->id,
                    'sku' => $pack_row->sku,
                    'qrcode' => $row_2->qrcode,
                    'price' => number_format($group_product_price[$pack_row->sku] / $pack_row->unit,2,'.',''),
                    'unit' => $pack_row->unit,
                    'gst_amount' => number_format($group_product_gst[$pack_row->sku],2,'.',''),
                    //'delivery_fees' => 0,
                    'delivery_time' => $pack_row->delivery_time,
                    'total' => number_format($group_product_price[$pack_row->sku],2,'.','')
                );
            }

            // Get The Coupon Applied
            $coupon_applied = array();
            $coupon_tot_amt = 0;
            $coupon_query = TCoupon::where('transaction_id', '=', $row->id)->get();
            foreach($coupon_query as $coupon_row) {
                $coupon_applied[] = $coupon_row->coupon_code;
                $coupon_tot_amt += $coupon_row->coupon_amount;
            }
                
            //JCashback Start  
            $jcashback = 0;
            $jcashback_applied = array();

            $queryjcashback = DB::table('jocom_jcashback_transactiondetails')
                            ->where("transaction_id",$row->id)
                            ->where("status",'=',1)
                            ->first();

            if(count($queryjcashback) >0){

                $jcashbackrm = $queryjcashback->jcash_point;
                $jcashback = number_format(($queryjcashback->jcash_point/100),2, '.', '');

                $jcashback_applied[] = [
                    'jcashback_points_rm' => $jcashback,
                    'jcashback_points' => $jcashbackrm,
                ];

            }

            //JCashback End 
            
            $redeemedPoints = TPoint::join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')
                ->where('jocom_transaction_point.transaction_id', '=', $row->id)
                ->where('jocom_transaction_point.status', '=', 1)
                ->get();

            $grandTotal = $row->total_amount - ($coupon_tot_amt + $row->gst_total+ $jcashback);
            $redeemed = array();
            $earned = array();

            foreach ($redeemedPoints as $point)
            {
                $redeemed[] = [
                    'point_type_id' => $point->point_type_id,
                    'point_type' => $point->type,
                    'point' => $point->point,
                    'amount' => $point->amount,
                ];

                $grandTotal -= $point->amount;
            }

            $earnedPoints = DB::table('point_transactions')
                ->select('point_users.point_type_id', 'point_types.type', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $row->id)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->groupBy('point_transactions.transaction_id')
                ->get();

            foreach ($earnedPoints as $point)
            {
                $earned[] = [
                    'point_type_id' => $point->point_type_id,
                    'point_type' => $point->type,
                    'point' => $point->point,
                ];
            }
            
            $refund      = "";
            $arr_refunds    = Refund::get_refund_history($row->id); 
            $cash_amount    = 0;
            $point_amount   = "";
            
            foreach($arr_refunds as $refunds) {
                $amount         = $refunds->amount;

                if($refund_id == "" || $refund_id != $refunds->id) { 
                    $refund_id = $refunds->id;
                }
                
                if(is_numeric($amount) && $amount > 0) {
                    if($refunds->refund_type == "Cash") {
                        $cash_amount    =  ($cash_amount == "") ? $amount : $cash_amount + $amount;
                    }
                        
                    if($refunds->refund_type == "JoPoint") {
                        $point          = ($refunds->amount_type == "deduct") ? "" : "+".$amount;
                        $point_amount   = ($point_amount == "") ? $point : $point_amount  + $point;
                    }
                }
            }
            
            if ($cash_amount > 0 || $point_amount > 0) {
                $refund[] = array(
                                //'refund_id'       => $refunds->id,
                                'refund_cash'   => Config::get('constants.CURRENCY') . number_format($cash_amount,2,'.',''),
                                'refund_point'  => ($point_amount > 0) ? $point_amount : "",
                                'refund_date'   => $refunds->created_date,
                            );
            }

            // Get delivery status
            $logisticStatus = LogisticTransaction::select('status')->where('transaction_id', '=',$row->id)->first();
            
            if ($logisticStatus->status) {
                $deliveryStatus = LogisticTransaction::get_status($logisticStatus->status);
            } else {
                $deliveryStatus = '-';
            }
            
            if($logisticStatus->status == 5){
                $delivery_mark = 'true'; 
            }else{
                $delivery_mark = 'false'; 
            }

            if ($row->parcel_status == Parcel::Sending){
                $parcel = Parcel::Received;
            } else {
                $parcel = '';
            }
            
            // Logistic Status //
            
            
                
            $data['item'][] = array(
                'id' => $row->id,
                'transaction_date' => date("d/m/Y", strtotime($row->transaction_date)),
                'buyer' => $row->buyer_username,
                'delivery_charges' => number_format($row->delivery_charges, 2,'.',''),
                'processing_fees' => number_format($row->process_fees, 2,'.',''),
                'coupon_code' => implode(", ", $coupon_applied),
                'jcashback' => $jcashback_applied,
                'coupon_amount' => number_format($coupon_tot_amt, 2,'.',''),
                'point_redeem' => $redeemed,
                'point_earn' => $earned,
                'gst_rate' => number_format($row->gst_rate, 0,'.',''),
                'gst_total' => number_format($row->gst_total, 2,'.',''),
                // amount customer paid
                'grand_total' => number_format($grandTotal, 2, '.', ''),
                'status' => ucwords($row->status),
                'refund' => $refund,
//                'refund_id' => $refund->id,
//                'refund_type' => $refund->refund_type,
//                'refund_amount' => ($refund->refund_type == "Cash") ? number_format($refund->amount,2, '.', '') : $refund->amount,
//                'refund_date' => $refund->created_date,
                'extra' => $details,
                'delivery_status' => $deliveryStatus,
                'delivery_completed' => $delivery_mark,
                'parcel_status' => $row->parcel_status,
                'parcel_status_option' => $parcel
            );

        }

        //return $data['record'];
        //return var_dump($error);
        //return $in_date . " / " . $mo_date . " / " . $timestamp;
        // print"<pre>";print_r(DB::getQueryLog());
        // exit;
        // return DB::getQueryLog();

        // $data['name'] = "eugene lee";

        return array('xml_data' => $data);
        
        }catch(exception $ex){
            echo $ex->getMessage();
        }
    }
    /**
     * Display Brands feed.
     *
     * @param  string  $param1
     * @param  int  $count
     * @param  int  $from
     * @param  array  $get
     * @return Response
     */
    function scopeBrands_feed($query, $param1, $count='', $from='', $get=array())
    {
        
        try{
            
        
        $lang   = 'en';
        $device = 'phone';
        $data   = array();
        $limit  = "";
        $count  = 0;
        $v3App = false;
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'BRAND_ITEM';
        $ApiLog->data = json_encode($get);
        $ApiLog->save();

        // App checking version
        if($get['vapp'] != ""){
            $vApp = $get['vapp'];
            if($vApp >= 3.0){
                $v3App = true;
            }
        }

        if($count !== false && is_numeric($count)) {
            $limit = " LIMIT " . $count;
            if($from !== false && is_numeric($from))
                $limit = " LIMIT " . $from . ", " . $count;
        }

        $where = "";

        if ($get['lang'] !== "")    $lang = strtolower($get['lang']);
        if ($get['device'] !== "")  $device = strtolower($get['device']);
        if ($get['platform'] != "") {
            $platform = strtoupper($get['platform']);
        }else{
            $platform = Platform::JOCOM_APP_CODE;
        }

       $region_id = "";
        
        if(Input::get('latitude')!='' && Input::get('longitude')!=''){

            $latitude      = Input::get('latitude');
            $longitude     = Input::get('longitude');

            $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($latitude).','.trim($longitude).'&sensor=false&key=AIzaSyBPcKtMmHfljZFsJSHy4wuzp5vO7NNwGVo';
            $json = file_get_contents($url);
            $test=json_decode($json);
            //print_r($test);
            if(isset($test->results[0])) {
                $response = array();
                foreach($test->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                   if(in_array('country', $addressComponet->types)) {
                            $response[] = $addressComponet->long_name;

                    }
                }
            
              }
              
            $region_name = $response[0];

            $country_name = $response[1];

            if ($region_name== "Wilayah Persekutuan Kuala Lumpur" OR $region_name== "Wilayah Persekutuan Putrajaya" or  $region_name== "Wilayah Persekutuan Labuan") {
                
                $string = explode (' ', $region_name, 3);
                $name2 = "WP-".$string[2];
            }
            else{
                $name2 = $region_name;
            }
            $country = DB::table('jocom_countries')->where('name', '=',  $country_name)->first();
            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name','=',$name2)->first();
            $region_id = $regions->region_id;

        }
        
        if($get['stateid'] != ""){
            $stateid = strtoupper($get['stateid']);
            
            if($stateid == RegionController::KLANGVALLEYSTATEID){
                $region_id = RegionController::KLANGVALLEYREGIONID;
            }else{
                $stateidInfo = State::find($stateid);
                $region_id = $stateidInfo->region_id;
            }
        }
        
        // FOR NEW APP
        
        if($v3App){
            
            $query = DB::table('jocom_brand_items')->select('jocom_brand_items.id', 'jocom_brand_items.pos', 'jocom_brand_items.url_link', 'jocom_brand_items.qrcode');
                                //->leftjoin('jocom_brand_item_images', 'jocom_brand_item_images.BRAND_id', '=', 'jocom_brand_items.id')
                                //->where('jocom_brand_item_images.language', '=', 'en')
            // WHEN JUEPIN APP IS READY //
            /*
            switch ($platform) {
                case Platform::JOCOM_APP_CODE:
                    $query->where('jocom_brand_items.is_jocom_app',"=",1);
                    break;
                case Platform::JUEPIN_APP_CODE:
                    $query->where('jocom_brand_items.is_juepin_app',"=",1);
                    break;
                default:
                    $query->where('jocom_brand_items.is_jocom_app',"=",1);
                    break;
            }
            */
            // WHEN JUEPIN APP IS READY //
            if($region_id == 2 || $region_id ==  3){
                $query = $query->whereIn('jocom_brand_items.region_id',[$region_id,0]);
            }else{
                $query = $query->whereIn('jocom_brand_items.region_id',[$region_id,0]);
            }
            

        }else{
            
            $query = DB::table('jocom_brand_items')->select('jocom_brand_items.id', 'jocom_brand_items.pos', 'jocom_brand_items.url_link', 'jocom_brand_items.qrcode');
                //->leftjoin('jocom_brand_item_images', 'jocom_brand_item_images.brand_id', '=', 'jocom_brand_items.id')
                //->where('jocom_brand_item_images.language', '=', 'en')
        }
        
            $query = $query->groupby('jocom_brand_items.id');
            $query = $query->orderBy('jocom_brand_items.pos', 'ASC')->get();
                                

        $insert_date = DB::table('jocom_brand_items')
                            ->select('insert_date')
                            ->orderBy('insert_date', 'DESC')->first();

        $modify_date = DB::table('jocom_brand_items')
                            ->select('modify_date')
                            ->orderBy('modify_date', 'DESC')->first();

        $insert_date = (isset($insert_date->insert_date) ? $insert_date->insert_date : '');
        $modify_date = (isset($modify_date->modify_date) ? $modify_date->modify_date : '');

        if ($modify_date > $insert_date) {
            $timestamp = $modify_date ;
        } else {
            $timestamp = $insert_date ;
        }

        $data['timestamp']  = $timestamp;

        $data['item']       = array();
        $args               = array();
        $args['req']        = 'brands';
        $image_path         = "";
        $thumb_path         = "";
        $thumb_width        = 320;
        $thumb_height       = 220;

        switch($device) {
            case 'phone' :
                $image_path         = Config::get('constants.BRANDITEM_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BRANDITEM_THUMB_FILE_PATH');  //'./public/images/banner/thumbs/';
                break;

            case 'tablet' :
                $image_path         = Config::get('constants.BRANDITEM_TAB_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BRANDITEM_TAB_THUMB_FILE_PATH');  //'./public/images/banner/thumbs/';
                break;
        }

        if(count($query) > 0) {
            foreach ($query as $row) {
                $file_name  = "";
                $thumb_name = "";
                $images     = BrandItem::getImagesByLanguage($row->id, $device, $lang);

                if(count($images) > 0) {
                    $file_name  = $image_path . $lang . "/" . $images->file_name;
                    $thumb_name = $thumb_path . $lang . "/" . $images->thumb_name;
                }
                else {
                    $default_images = BrandItem::getImagesByLanguage($row->id, "phone", "en");

                    if(count($default_images) > 0) {
                        $file_name      = Config::get('constants.BRANDITEM_FILE_PATH') . "en/" . $default_images->file_name;
                        $thumb_name     = Config::get('constants.BRANDITEM_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                    }
                }

                if ($file_name != "" && file_exists('./' . $file_name)) {
                    if(!file_exists('./' . $thumb_name)) {
                        create_thumbnail($file_name, $thumb_width, $thumb_height, './' . $image_path, './' . $thumb_path);
                    }
                    
                    $cleanqrcode = str_replace(" ","",$row->qrcode);
                    $QRCODE  = explode(",",$cleanqrcode);
                    
                    // $recordProduct = DB::table('jocom_products AS JP')
                    //     ->select('JP.qrcode')
                    //     ->where('JP.status',1)
                    //     ->whereIn('JP.qrcode', $QRCODE)
                    //     ->where('JP.region_id',$region_id)->toSql();
                        
                    // $ApiLog = new ApiLog ;
                    // $ApiLog->api = 'BRAND_ITEM_RESPONSE_PRODUCT';
                    // $ApiLog->data = json_encode($QRCODE);
                    // $ApiLog->save();
                        
                    // $ApiLog = new ApiLog ;
                    // $ApiLog->api = 'BRAND_ITEM_RESPONSE_PRODUCT_V2';
                    // $ApiLog->data = json_encode($recordProduct);
                    // $ApiLog->save();

                    $data['item'][] = array(
                        'id'        => $row->id,
                        'position'  => $row->pos,
                        'img'       => Image::link($file_name),
                        'thumbnail' => Image::link($thumb_name),
                        'url'       => $row->url_link,
                        'qrcode'    => $row->qrcode
                    );
                    $count++;
                }
            }

            $data['record']     = $count;


        } else $data['status_msg'] = '#701';
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'BRAND_ITEM_RESPONSE';
        $ApiLog->data = json_encode($data);
        $ApiLog->save();
        return array('xml_data' => $data);
        
        }catch(Exception $ex){
            
            echo $ex->getMessage();
            
            $ApiLog = new ApiLog ;
            $ApiLog->api = 'BRAND_ITEM_RESPONSE_ERROR';
            $ApiLog->data = $ex->getMessage();
            $ApiLog->save();
            
        }
    }
    
    
}
?>