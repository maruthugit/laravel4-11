<?php

class FeedV2Controller extends BaseController {

    protected $seller;

    public function __construct(Feed $feed) {

        $this->feed = $feed;

    }
    
    // req => request
    // enc => (encoding to format output - if blank, defaults to "UTF-8")
    // count => (number of records to fetch) - numeric - optional - default 50 records
    // from => (offset) - numeric
    // products_cat => products category id
    
    function anyIndex() {
        
        $data   = array();
        $get    = array();

        // Trim input :D
        // Input::merge(array_map('trim', Input::all()));       

        // print_r(Input::all());
        
        if(Input::get('enc')) 
            $data['enc'] = Input::get('enc');
        
        if(Input::get('products_cat'))
            $get['products_cat'] = Input::get('products_cat');
        
        if(Input::get('code'))
            $get['code'] = Input::get('code'); 
        
        if(Input::get('user'))
            $get['user'] = Input::get('user');
        
        if(Input::get('buyer'))
            $get['buyer'] = Input::get('buyer');
        
        if(Input::get('pass'))
            $get['pass'] = Input::get('pass');

        //added by eugene
        if(Input::get('lang'))
            $get['lang'] = Input::get('lang');

        if(Input::get('device'))
            $get['device'] = Input::get('device');
        
        if(Input::get('stateid'))
            $get['stateid'] = Input::get('stateid');

        //added by maryanne
        if(Input::get('parcel_status'))
            $get['parcel_status'] = Input::get('parcel_status');

        if(Input::get('id'))
            $get['id'] = Input::get('id');
        
        if(Input::get('vapp'))
            $get['vapp'] = Input::get('vapp');
        
        // added by Wira
        if(Input::get('platform')){
            switch (Input::get('platform')) {
                case 'JUE':
                    $get['platform'] = Platform::JUEPIN_APP_CODE;
                    break;
                
                case 'JOC':
                    $get['platform'] = Platform::JOCOM_APP_CODE;
                    break;

                default:
                    $get['platform'] = Platform::JOCOM_APP_CODE;
                    break;
            }
        }else{
            $get['platform'] = Platform::JOCOM_APP_CODE;
        }
            
        
        $req    = Input::get('req');
        $count  = Input::get('count');
        $from   = Input::get('from');
        
        if($req == '' && sizeof(Input::get('req')) > 0) {
            if(Input::get('req') !== false)
                $req = "default";
        }

        if($count === false || !is_numeric($count)) {
            $count = 50;
        }
        // var_dump($_POST);
        switch($req) {
            // case "":
            //     $defdata = array(
            //         'timestamp' => date('Y-m-d H:i:s'),
            //         'record' => 2,
            //         'item' => array(
            //                     0 => array(
            //                             'name' => 'PRODUCTS',
            //                             'url' => base_url() . 'feed/?req=products'
            //                             ),
            //                     1 => array(
            //                             'name' => 'TRANSACTION',
            //                             'url' => base_url() . 'feed/?req=trans'
            //                             )
            //         )
            //     );
            //     $data = array_merge($data, array('xml_data' => $defdata));
            //     break;
            case "seller_products":
                // code -------------
                // Accept The Sell Id
                $data = array_merge($data, $this->feed->seller_products_feed($req, $count, $from, $get));
                break;
            case "pro_cat":
                $data = array_merge($data, $this->feed->products_cat_feed($req, $count, $from, $get));
                break;
            case "pro_name":
                $data = array_merge($data, $this->feed->products_name_feed($req, $count, $from, $get));
                break;
            case "pro_name_custom":
                $data = array_merge($data, $this->feed->products_name_custom($req, $count, $from, $get));
                break;
            case "products":
                $data = array_merge($data, $this->feed->products_feed($req, $count, $from, $get));
                break;
            case "products_custom":
                $data = array_merge($data, $this->feed->products_custom($req, $count, $from, $get));
                break;
            case "banner":
                // Banner
                $data = array_merge($data, $this->feed->banner_feed($req, $count, $from, $get));
                break;
            case "news":
                // Latest News
                $data = array_merge($data, $this->feed->news_feed($req, $count, $from, $get));
                break;
            case "hot":
                // Hot Items
                $data = array_merge($data, $this->feed->hot_feed($req, $count, $from, $get));
                break;
            case "brands":
                // Brand Items
                $data = array_merge($data, $this->feed->brands_feed($req, $count, $from, $get));
                break;
            case 'trans':
                
                //$tempdata = Feed::transaction_feed($req, $count, $from, $get);
                $tempdata = Feed::transaction_feeds($req, 250, $from, $get);
                $data = array_merge($data, $tempdata);
                //var_dump($tempdata);
                break;
            case 'trans_update':
                $tempdata = Feed::parcel_update_feed($get);
                $data = array_merge($data, $tempdata);
                //var_dump($data);exit;
                break;
            case 'fees':
                $tempdata = Feed::fees_feed($req, $count, $from, $get);
                $data = array_merge($data, $tempdata);                
                break;
                
            case "zone":
                $data = array_merge($data, $this->feed->zone_feed($req, $count, $from, $get));
                break;
            case "state":
                // echo "<br>feed - state -> ";
                // var_dump($get);
                $data = array_merge($data, $this->feed->state_feed($req, $count, $from, $get));
                break;
            case "country":
                // echo "<br>feed - country";
                $data = array_merge($data, $this->feed->country_feed($req, $count, $from, $get));
                break;

            case "city":
                // echo "<br>feed - city : " . "<br>";
                $data = array_merge($data, $this->feed->city_feed($req, $count, $from, $get));
                break;
            
            case "comments":
                $data = array_merge($data, $this->feed->comment_feed($req, $count, $from, $get));
                break;
            
            case "bannertemplate":
                $data = array_merge($data, $this->feed->bannertemplate_feed($req, $count, $from, $get));
                break;
             
            case "bannertemplatenew":
                $data = array_merge($data, $this->feed->bannertemplatelatest_feed($req, $count, $from, $get));
                break;
                
            case "popup":
                $data = array_merge($data, $this->feed->popup_feed($req, $count, $from, $get));
                break;  
            
            case "jcmpopup":
                $data = array_merge($data, $this->feed->jcmpopup_feed($req, $count, $from, $get));
                break;  
                
            case "jcmpopupstagging":
                $data = array_merge($data, $this->feed->jcmpopupstagging_feed($req, $count, $from, $get));
                break;  
                
            default:
                $tmpdata = array(
                    'status_msg' => '#01'
                    // 'error_message' => 'NO data found.'
                );
                $data = array_merge($data, array('xml_data' => $tmpdata));
                break;
        }

        return json_encode($data);
    }


    function anyUser() {

        $user           = trim(Input::get('user'));
        $pass           = trim(Input::get('pass'));
        $data           = Feed::fetch_user($user, $pass);

        return Response::json($data);

    }
    
    function anyVersion()
    {      
        $get        = array();
        $os         = trim(Input::get('os'));
        // Feed::get_version($os);
        $data       = Feed::get_version($os);

        return Response::json($data);
    }
    
    function anyVersioninglogistic()
    {
        $get        = array();
        $os         = trim(Input::get('os'));
        // Feed::get_version($os);
        $data       = Feed::getnewversionLogistic($os);

        return Response::json($data);
    }
    
    function anyVersioning()
    {
        $get        = array(); 
        $os         = trim(Input::get('os'));
        // Feed::get_version($os);
        $data       = Feed::getnewversion($os);

        return Response::json($data);
    }
}
?>