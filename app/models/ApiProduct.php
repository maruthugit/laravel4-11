<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

use Helper\ImageHelper as Image;

class ApiProduct extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    //protected $table = 'jocom_transaction';
    
    private static $d_time_range = [
        '24 hours' => [
            'to' => 1,
            'from' => 1,
        ],
        '1-2 business days2-3 business days' => [
            'to' => 1,
            'from' => 2,
        ],
        '2-3 business days' => [
            'to' => 2,
            'from' => 3,
        ],
        '3-7 business days' => [
            'to' => 3,
            'from' => 7,
        ],
        '14 business days' => [
            'to' => 1,
            'from' => 14,
        ]
    ];


    public static function fetch_category($limit = 50, $offset = 0, $params = [])
    {
        // $ApiLog = new ApiLog ;
        // $ApiLog->api = 'API_PRODUCT_CATEGORY';
        // $ApiLog->data = json_encode($params);
        // $ApiLog->save();
        
        $category   = array_get($params, 'product_cat', 0);
        $permission = array_get($params, 'custom', 0);
        $username   = array_get($params, 'username', NULL);
        $charity    = array_get($params, 'charity', 0);
        // $lang       = array_get($params, 'lang', 'en');
        $device     = array_get($params, 'device', 'phone');
        $city       = array_get($params, 'city', 0);   // Added by Maruthu
        $charityCount = 0;
        

        // Private App
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

             $categories = Category::where('category_parent', '=', $category)
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

        $data = [
            'record'        => $categories->get()->count(), 
            'cat_parent'    => $category,
            'total_record'  => $categories->count(),
        ];

        $categories = $categories->orderBy('weight', 'desc')->orderBy('category_name', 'asc')->get();

        foreach ($categories as $category)
        {
            switch (array_get($params, 'lang'))
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

		// Added by Maruthu
           if (isset($city) && ! empty($city))
            {
                $productsCount = DB::table('jocom_product_and_package')
                ->join('jocom_product_delivery', 'jocom_product_delivery.product_id', '=', 'jocom_product_and_package.id')
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
                $productsCount = DB::table('jocom_product_and_package')

                ->where('status', '=', 1)

                ->where(function($categories) use ($regex)
                {
                    $categories->where('category', 'REGEXP', "(^|,)({$regex})(,|$)");
                })
                ->count();
            }

            $categoryImage = ( ! empty($category->category_img)) ?
                Image::link("images/category/{$category->category_img}") : '';
            
            $categoryImagebanner = ( ! empty($category->category_img_banner)) ?
                Image::link("images/category/{$category->category_img_banner}") : '';

            if ($charity == 1)
            {
                $charityInfo = CharityCategory::where('category_id', '=', $category->id)->first();

                if (count($charityInfo) > 0)
                {
                    $charityCount++;

                    $city_name = "";
                    $city_name = City::select('name')->find($charityInfo->city);

                    $state_name = "";
                    $state_name = State::select('name')->find($charityInfo->state);

                    $country_name = "";
                    $country_name = Country::select('name')->find($charityInfo->country);

                    if ($charityInfo->{"img_{$device}"} == '' or $charityInfo->{"img_{$device}"} == NULL)
                        $img = $charityInfo->img_phone;
                    else
                        $img = $charityInfo->{"img_{$device}"};

                    $data['item'][] = [
                        'id'                => $category->id,
                        'charity_id'        => $charityInfo->id,
                        'cat_name'          => $categoryName,
                        'cat_img'           => Image::link(Config::get('constants.CHARITY_FILE_PATH') . $img),
                        'delivername'       => $charityInfo->name,
                        'delivercontactno'  => $charityInfo->contactno,
                        'specialmsg'        => $charityInfo->specialmsg,
                        'deliveradd1'       => $charityInfo->address1,
                        'deliveradd2'       => $charityInfo->address2,
                        'deliverpostcode'   => $charityInfo->postcode,
                        'city'              => $charityInfo->city,
                        'city_name'         => $city_name->name,
                        'state'             => $charityInfo->state,
                        'state_name'        => $state_name->name,
                        'delivercountry'    => $charityInfo->country,
                        'country_name'      => $country_name->name,
                        'banner'            => $bannerlist,
                    ];
                }

                
            }
            else
            {            
                        $data['item'][] = [
                        'id'        => $category->id,
                        'cat_name'  => $categoryName,
                        'cat_icon'  => $categoryImage,
                    'cat_banner'  => $categoryImagebanner,
                        'p_count'   => $productsCount,
                        'sub_cat'   => (count($childCategories)) ? 1 : 0,
                        'charity'   => $category->charity,
                        ];
            }            
        }
        if ($charity == 1)
        {
            $data['record'] = $charityCount;
            $data['total_record'] = $charityCount;
        }

        return ['xml_data' => $data];
    }

    public static function fetchCategoryTree($parent = 0, $user_tree_array = '')
    {
        // $ApiLog = new ApiLog ;
        // $ApiLog->api = 'API_PRODUCT_CATEGORY_TREE';
        // $ApiLog->data = json_encode($parent);
        // $ApiLog->save();
        
        //  $ApiLog = new ApiLog ;
        // $ApiLog->api = 'API_PRODUCT_CATEGORY_TREE';
        // $ApiLog->data = json_encode($user_tree_array);
        // $ApiLog->save();
        
        if ($parent == 0)
        {
            return Category::select('id')
                ->where('status', '=', 1)
                ->get();
        }

        if ( ! is_array($user_tree_array))
        {
            $user_tree_array = array();
        }

        $query = DB::table('jocom_products_category')
            ->select('id')
            ->where('category_parent', '=', $parent)
            ->where('status', '=', 1)
            ->get();

        if (count($query) > 0)
        {
            foreach($query as $row)
            {
                $user_tree_array[] = array('id' => $row->id);
                $user_tree_array = ApiProduct::fetchCategoryTree($row->id, $user_tree_array);
            }
        }

        return $user_tree_array;
    }

    public static function fetch_product($limit = 250, $offset = 0, $params = [])
    {
        
       
        // $ApiLog = new ApiLog ;
        // $ApiLog->api = 'API_PRODUCT';
        // $ApiLog->data = json_encode($params);
        // $ApiLog->save();
        
        //uncomment this code for new app 
        $vApp = Input::get('vapp');
        $v3App = false;

        if($vApp >= 3.0){
            $v3App = true;
        }
        
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
            $regions_id = $regions->region_id;

        }else{
            if(Input::get('id') != ''){

            $id = Input::get('id');
                if($id == RegionController::KLANGVALLEYSTATEID){
                    $regions_id = RegionController::KLANGVALLEYREGIONID;
                }else{
                    $regions = State::find($id);
                    $regions_id = $regions->region_id;
                }

            }else{

        }         
 
        }
 
        $category   = array_get($params, 'product_cat', NULL);
        $codes      = array_get($params, 'code', NULL);
        $code2      = array_get($params, 'code2', NULL);
        $name       = array_get($params, 'name', NULL);
        $permission = array_get($params, 'custom', 0);
        $username   = array_get($params, 'username', NULL);
        $requestBanner = array_get($params, 'banner', 0);
        $city       = array_get($params, 'city', NULL);   // Added by Maruthu
        $charity_id = 0;
        $device     = array_get($params, 'device', 'phone');
       
        switch ($device)
        {
            case 'phone':
                $image_path         = Config::get('constants.BANNER_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BANNER_THUMB_FILE_PATH');
                break;
            
            case 'tablet':
                $image_path         = Config::get('constants.BANNER_TAB_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BANNER_TAB_THUMB_FILE_PATH');
                break;
        }

        // if no app platform code will take JOC as platform code
        $platform    = array_get($params, 'platform', Platform::JOCOM_APP_CODE);        
        
//      echo print_r($params); die();

        if ( ! (is_numeric($limit) && $limit > 0 && is_numeric($offset)))
        {
            $limit  = 20;  // Product reduce 250 - 50
            $offset = 0;
        }

        $data       = [
            'banner'        => '',
            'record'        => 0,
            'total_record'  => 0,
            'item'          => [],            
        ];

        switch (array_get($params, 'lang'))
        {
            case 'CN':
                $language = '_cn';
                $lang = 'cn';
                break;
            case 'MY':
                $language = '_my';
                $lang = 'my';
                break;
            default:
                $language = '';
                $lang = 'en';
                break;
        }

        if (is_numeric($category) && empty($name) && empty($city))
        {
            
            if($v3App){
        
            $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id');
                
                // IF JOHOR REGION ONLY RETURN JOHOR PRODUCT : REQUESTED BY JOHOR MANAGEMENT //
                if($regions_id == 2 || $regions_id == 3){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                
                } else if($regions_id == 5){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id]);
                }else{
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                }
                    
                $now = date_format(Carbon\Carbon::now(), 'Y-m-d H:i:s');
                // Checking on platform
                $products->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1 AND (new_arrival_expire is null OR new_arrival_expire > '${now}')")
                
                // uncomment for new app
                ->orderBy('jocom_product_and_package.region_id', 'desc');
                
                // not able to sort by ID, sql not able to sort properly
                // ->orderBy('jocom_product_and_package.id', 'desc');
                // ->orderBy('name', 'asc');
            // WHEN JUEPIN IS READY //
            /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                }
            */
            // WHEN JUEPIN IS READY //

            }else{
                
            
                $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1");
            }
            
            
            $products      = $products->orderBy('weight', 'desc');
            $products      = $products->orderBy('jocom_product_and_package.sku', 'desc');
            $totalProducts = $products->count();
            $products      = $products->skip($offset)->take($limit)->get();

            // request category banner
            if ($requestBanner == 1)
            {
                $bannerlist = array();

                $charity_cat = CharityCategory::select('id')->where('category_id', $category)->first();

                if(count($charity_cat) > 0)
                    $charity_id = $charity_cat->id;

                $banners = Banner::select('jocom_banners.id', 'jocom_banners.pos', 'jocom_banners.url_link', 'jocom_banners.qrcode')
                        ->where('jocom_banners.category_id', '=', $category)
                        ->groupby('jocom_banners.id')
                        ->orderBy('jocom_banners.pos', 'desc')
                        ->get();

                if(count($banners) > 0)
                {
                    foreach ($banners as $banner)
                    {
                        $image_name = "";
                        $file_name  = "";
                        $thumb_name = "";
                        $bimage     = Banner::getImagesByLanguage($banner->id, $device, $lang);

                        if(count($bimage) > 0)
                        {
                            $image_name = $bimage->file_name;
                            $file_name  = $image_path . $lang . "/" . $bimage->file_name;
                            $thumb_name = $thumb_path . $lang . "/" . $bimage->thumb_name;
                        }
                        else
                        {
                            $default_images = Banner::getImagesByLanguage($banner->id, "phone", "en");

                            if(count($default_images) > 0)
                            {
                                $image_name     = $default_images->file_name;
                                $file_name      = Config::get('constants.BANNER_FILE_PATH') . "en/" . $default_images->file_name;
                                $thumb_name     = Config::get('constants.BANNER_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                            }
                        }

                        if ($file_name != "" && file_exists('./' . $file_name))
                        {
                            if(!file_exists('./' . $thumb_name))
                            {
                                // echo "<br> NOT EXISTS! - ". '/public/' . $thumb_name;
                                create_thumbnail($image_name, $thumb_width, $thumb_height, './' . $image_path . "en", './' . $thumb_path . $lang . "/");
                            }

                            $bannerlist[] = array(
                                'id'        => $banner->id,
                                'img'       => Image::link($file_name),
                                'thumbnail' => Image::link($thumb_name),
                                'url'       => $banner->url_link,
                                'qrcode'    => $banner->qrcode
                            );
                        }
                    }
                }


                $data['banner'] = $bannerlist;
            }
        }
        else if (is_numeric($category) && empty($name) && is_numeric($city))
        {
             // die('New City');
            
            if($v3App){
          
            $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_delivery', 'jocom_categories.product_id', '=', 'jocom_product_delivery.product_id') // Added by Maruthu
                ->leftJoin('jocom_zone_cities', 'jocom_product_delivery.zone_id', '=', 'jocom_zone_cities.zone_id') // Added by Maruthu
                ->leftJoin('jocom_zones', 'jocom_zones.id', '=', 'jocom_zone_cities.zone_id'); // Added by Maruthu
                // uncomment for new app
                
                 if($regions_id == 2 || $regions_id == 3){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                 } else if($regions_id == 5){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id]);
                }else{
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0])->orWhere('jocom_product_and_package.region_id', '=',0) ;
                }
                    
                
                $products->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1")
                // uncomment for new app
                ->orderBy('jocom_product_and_package.region_id', 'desc');
                
                // WHEN JUEPIN APP IS READY //
                // Handle Platform Request 
                /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                }
                */
                // WHEN JUEPIN APP IS READY //

            }else{
                         
                $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_delivery', 'jocom_categories.product_id', '=', 'jocom_product_delivery.product_id') // Added by Maruthu
                ->leftJoin('jocom_zone_cities', 'jocom_product_delivery.zone_id', '=', 'jocom_zone_cities.zone_id') // Added by Maruthu
                // uncomment for new app
//                ->whereIn('jocom_product_and_package.region_id',[$regions_id,0])
//                ->orWhere('jocom_product_and_package.region_id', '=',0)
                ->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1");
                // uncomment for new app
//                ->orderBy('jocom_product_and_package.region_id', 'desc');
                
            }    
            $products->orderBy('weight', 'desc');
            $products->orderBy('jocom_product_and_package.sku', 'desc');
            // not able to sort by ID, sql not able to sort properly
            // ->orderBy('jocom_product_and_package.id', 'desc');
            // ->orderBy('name', 'asc');

            $totalProducts = $products->count();
            $products      = $products->skip($offset)->take($limit)->get();
            
            // request category banner
            if ($requestBanner == 1)
            {
                $bannerlist = array();

                $charity_cat = CharityCategory::select('id')->where('category_id', $category)->first();

                if(count($charity_cat) > 0)
                    $charity_id = $charity_cat->id;

                $banners = Banner::select('jocom_banners.id', 'jocom_banners.pos', 'jocom_banners.url_link', 'jocom_banners.qrcode')
                        ->where('jocom_banners.category_id', '=', $category)
                        ->groupby('jocom_banners.id')
                        ->orderBy('jocom_banners.pos', 'desc')
                        ->get();

                if(count($banners) > 0)
                {
                    foreach ($banners as $banner)
                    {
                        $image_name = "";
                        $file_name  = "";
                        $thumb_name = "";
                        $bimage     = Banner::getImagesByLanguage($banner->id, $device, $lang);

                        if(count($bimage) > 0)
                        {
                            $image_name = $bimage->file_name;
                            $file_name  = $image_path . $lang . "/" . $bimage->file_name;
                            $thumb_name = $thumb_path . $lang . "/" . $bimage->thumb_name;
                        }
                        else
                        {
                            $default_images = Banner::getImagesByLanguage($banner->id, "phone", "en");

                            if(count($default_images) > 0)
                            {
                                $image_name     = $default_images->file_name;
                                $file_name      = Config::get('constants.BANNER_FILE_PATH') . "en/" . $default_images->file_name;
                                $thumb_name     = Config::get('constants.BANNER_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                            }
                        }

                        if ($file_name != "" && file_exists('./' . $file_name))
                        {
                            if(!file_exists('./' . $thumb_name))
                            {
                                // echo "<br> NOT EXISTS! - ". '/public/' . $thumb_name;
                                create_thumbnail($image_name, $thumb_width, $thumb_height, './' . $image_path . "en", './' . $thumb_path . $lang . "/");
                            }

                            $bannerlist[] = array(
                                'id'        => $banner->id,
                                'img'       => Image::link($file_name),
                                'thumbnail' => Image::link($thumb_name),
                                'url'       => $banner->url_link,
                                'qrcode'    => $banner->qrcode
                            );
                        }
                    }
                }


                $data['banner'] = $bannerlist;
            }
        }
        elseif ( ! empty($codes) AND empty($code2))
        {
            //echo "hwee";
            ksort($codes);
 
            $codes = "'".implode("', '", $codes)."'";

            // $products = DB::table('jocom_product_and_package')
            //     ->select('*')
             $products = Product::select('*')
                ->where('status', '=', 1)
                ->whereRaw("qrcode IN ({$codes})");

            // Handle Platform Request  
            
            // WHEN JUEPIN APP IS READY //
            /*
            switch ($platform) {
                case Platform::JOCOM_APP_CODE:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
                case Platform::JUEPIN_APP_CODE:
                    $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                    break;
                default:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
            }
            */
            // WHEN JUEPIN APP IS READY //
            
            $products->orderByRaw("FIELD(qrcode, {$codes})");
            $totalProducts  = $products->count();
            $products       = $products->skip($offset)->take($limit)->get();
        }
        elseif ( ! empty($code2))
        { 
            
            ksort($code2);

            $code2 = "'".implode("', '", $code2)."'";

            // $products = DB::table('jocom_product_and_package')
            $products = Product::select('*')
                // ->select('*')
                ->where('status', '=', 1)
                ->whereRaw("qrcode IN ({$code2})");

            // Handle Platform Request  
            // WHEN JUEPIN APP IS READY //
            /*
            switch ($platform) {
                case Platform::JOCOM_APP_CODE:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
                case Platform::JUEPIN_APP_CODE:
                    $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                    break;
                default:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
            }
            */
            // WHEN JUEPIN APP IS READY //
            
            $products->orderByRaw("FIELD(qrcode, {$code2})");

            $totalProducts  = $products->count();
            $products       = $products->skip($offset)->take($limit)->get();
        }
        elseif ( ! empty($name) && strlen($name) >= 2)
        {
         
            $category       = isset($category) ? $category : 0;
            $categoryList   = [];
            $isPrivateApp   = false;

            // Private App
            if (isset($username) && ! empty($username) && $category == 0)
            {
                $userCategories = DB::table('jocom_user_category')
                    ->select('category_id')
                    ->where('username', '=', $username)
                    ->get();

                if ($userCategories)
                {
                    $isPrivateApp = true;

                    foreach ($userCategories as $userCategory)
                    {
                        $categoryList[] = $userCategory->category_id;
                    }
                }
            }

            if ($isPrivateApp || $category > 0)
            {
                $categories = Category::getByParent($category, $permission, $categoryList);
            }
            else
            {
                $categories = Category::where('category_parent', '=', $category)
                    ->where('permission', '=', $permission)
                    ->where('status', '=', 1)
                    ->where('id', '>', 0)
                    ->get();
            }

            $familyCategories = [];

            if ($categories->count() > 0)
            {
                foreach ($categories as $category)
                {
                    $familyCategories[] = $category->id;
                    $childCategories    = array_pluck(ApiProduct::fetchCategoryTree($category->id), 'id');
                    $familyCategories   = array_merge($familyCategories, $childCategories);
                }
            }

            $familyCategories = implode(', ', array_unique($familyCategories, SORT_NUMERIC));

            // $name = str_replace('\'', '\'\'', $name);

            $name = addslashes($name);
           
    	    if(! empty($city) && is_numeric($city))
            {
                
                
                
                $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_tags', 'jocom_product_and_package.id', '=', 'jocom_product_tags.product_id')
                ->leftJoin('jocom_product_delivery', 'jocom_product_and_package.id', '=', 'jocom_product_delivery.product_id') 
                ->leftJoin('jocom_zone_cities', 'jocom_product_delivery.zone_id', '=', 'jocom_zone_cities.zone_id') 
                ->where('jocom_product_and_package.status', '=', 1)
                ->whereRaw("(tag_name LIKE '%{$name}%' OR name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'TM-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'TMP-%' AND category IN ({$familyCategories}))) AND status = 1 AND jocom_zone_cities.city_id={$city}" 
                );
                // Handle Platform Request  
                // WHEN JUEPIN APP IS READY //
                /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
            }
                */
                // WHEN JUEPIN APP IS READY //
                
                $products->groupBy('sku');
                $products->orderBy('weight', 'desc');
                $products->orderBy('name', 'asc');
            }
            else
            {
                
               // echo $name;die();
                
                if($v3App){
                    
                    $products = DB::table('jocom_product_and_package')
                        ->select('jocom_product_and_package.*')
                        ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                        ->leftJoin('jocom_product_tags', 'jocom_product_and_package.id', '=', 'jocom_product_tags.product_id')
                        ->where('jocom_product_and_package.status', '=', 1);
                        
                if($regions_id == 2 || $regions_id == 3){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                } else if($regions_id == 5){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id]);
                }else{
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]) ;
                }
                        
           
                    //   $products->whereRaw("(tag_name LIKE '%{$name}%' OR name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1"
                    //          // "(name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1 AND permission = {$permission}"
                    //     );
                        
                        
                         $products->whereRaw("(name LIKE '%{$name}%' ) AND ((sku LIKE 'TM-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'TMP-%' AND category IN ({$familyCategories}))) AND status = 1"
                             // "(name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1 AND permission = {$permission}"
                        );

                    
                
                }else{
                    
                    $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_tags', 'jocom_product_and_package.id', '=', 'jocom_product_tags.product_id')
                ->whereRaw("(tag_name LIKE '%{$name}%' OR name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'TM-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'TMP-%' AND category IN ({$familyCategories}))) AND status = 1"
                     // "(name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1 AND permission = {$permission}"
                );
                // Handle Platform Request  
                
                // WHEN JUEPIN APP IS READY //
                /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
            }
                */
                // WHEN JUEPIN APP IS READY //
                    
                    
                        
                }
                
	            
                $products->groupBy('sku');
                $products->orderBy('weight', 'desc');
                $products->orderBy('name', 'asc');
            }
            $totalProducts = count($products->get());

            $products = $products->skip($offset)->take($limit)->get();
            
        }
        else
        {
            return ['xml_data' => array_merge($data, ['status_msg' => '#201'])];
        }

        if ($totalProducts <= 0)
        {
            return ['xml_data' => array_merge($data, ['status_msg' => '#201'])];
        }
        else
        {
            $zoneNames              = Zone::lists('name'); // To be used by package-zone match
            $data['record']         = count($products);
            $data['total_record']   = $totalProducts;

            foreach ($products as $product)
            {
                
               
                for ($i = 1; $i <= 3; $i++)
                {
                    $filename = $product->{"img_{$i}"};

                    $images[$i]     = ( ! empty($filename)) ? Image::link("images/data/{$filename}") : '';
                    $thumbnails[$i] = ( ! empty($filename)) ? Image::link("images/data/thumbs/{$filename}") : '';
                }

                $productName        = ($product->{'name'.$language}) ? $product->{'name'.$language} : $product->name;
                $productDescription = ($product->{'description'.$language}) ? $product->{'description'.$language} : $product->description;
                $filename           = $product->vid_1;
                // $videoPath          = asset("media/video/files/{$filename}");
                $video              = ( $filename != "") ? $filename : '';
                $pricesArray        = [];

                /**
                 * If ID is prefixed with 'P' meaning it is a package
                 * Otherwise it is a product
                 */
                if (substr($product->id, 0, 1) == 'P')
                {
                    $packageId      = substr($product->id, 1);
                    $proOptIds      = [];
                    $proQtyCal      = [];

                    $deliveryZones  = (array) DB::table('jocom_package_delivery')
                        ->where('jocom_package_delivery.package_id', '=', $packageId)
                        ->first();
                    $deliveryZones  = array_unique(explode(',', $deliveryZones['zone']));
                    $zones          = [];

                    foreach ($deliveryZones as $deliveryZone)
                    {
                        // $zoneNames array key starts from 0 and $deliveryZone starts from 1, so minus 1 to get the correct zone name
                        $zones[] = [
                            'zone' => $deliveryZone,
                            'zone_name' => $zoneNames[$deliveryZone - 1]
                        ];
                    }

                    $packageProducts = DB::table('jocom_product_package_product')
                        ->where('package_id', '=', $packageId)
                        ->get();

                    foreach ($packageProducts as $packageProduct)
                    {
                        $proOptIds[$packageProduct->product_opt] = $packageProduct->qty;
                        $proQtyCal[$packageProduct->product_opt] = 0;
                    }

                    $actualPrice    = 0;
                    $promotionPrice = 0;

                    if (empty($proOptIds))
                    {
                        $pricesArray[] = [
                            'id'            => 'Null',
                            'label'         => 'Null',
                            'price'         => 0,
                            'promo_price'   => 0,
                            'qty'           => 0,
                            'stock'         => 0,
                            'stock_unit'    => 0,
                            'default'       => 'TRUE',
                            'p_weight'      => 0,
                        ];
                    }
                    else
                    {
                        $prices = Price::getMultiActivePrices($proOptIds);
                        $maxQty = false;

                        foreach ($prices as $price)
                        {
                            $gst            = Product::getGstValue($price->id);
                            $actualPrice   += $price->price * $proOptIds[$price->id] * $gst;

                            if ($price->price_promo > 0)
                            {
                                $promotionPrice += $price->price_promo * $proOptIds[$price->id] * $gst;
                            }
                            else
                            {
                                $promotionPrice += $price->price * $proOptIds[$price->id] * $gst;
                            }

                            $proQtyCal[$price->id] = $price->qty;
                        }

                        foreach ($proQtyCal as $mQty)
                        {
                            if (($maxQty === false) || ($maxQty > $mQty))
                            {
                                $maxQty = $mQty;
                            }
                        }

                        if ($actualPrice > 0)
                        {
                            $promotionPrice = ($actualPrice == $promotionPrice) ?
                                0 : number_format($promotionPrice, 2, '.', '');
                            $actualPrice    = number_format($actualPrice, 2, '.', '');

                            $pricesArray[] = [
                                'id'            => 'Null',
                                'label'         => 'Null',
                                'price'         => ApiProduct::hidePricing($username, $actualPrice),
                                'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                'qty'           => $maxQty,
                                'stock'         => 0,
                                'stock_unit'    => 0,
                                'default'       => 'TRUE',
                                'p_weight'      => 0,
                            ];
                        }
                        else
                        {
                            $pricesArray[] = [
                                'id'            => 'Null',
                                'label'         => 'Null',
                                'price'         => ApiProduct::hidePricing($username, $actualPrice),
                                'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                'qty'           => $maxQty,
                                'stock'         => 0,
                                'stock_unit'    => 0,
                                'default'       => 'TRUE',
                                'p_weight'      => 0,
                            ];
                        }
                    }
                }
                else
                {
                    unset($prices);
                    $zones          = [];
                    $deliveryZones  = Delivery::getZonesByProduct($product->id);
                    $productId      = (int) next(explode('-', $product->sku));

                    foreach ($deliveryZones as $deliveryZone)
                    {
                        $zones[] = [
                            'zone' => $deliveryZone->zone_id,
                            'zone_name' => $deliveryZone->zone_name,
                        ];
                    }

                    if (isset($username) && ! empty($username))
                    {
                        $spGroup = DB::table('jocom_user AS user')
                            ->select('group.sp_group_id')
                            ->leftJoin('jocom_sp_customer AS customer', 'user.id', '=', 'customer.user_id')
                            ->leftJoin('jocom_sp_customer_group AS group', 'customer.id', '=', 'group.sp_cust_id')
                            ->where('user.username', '=', $username)
                            ->get();
                            
                        // echo "<pre>";
                        // print_r($spGroup);
                        // echo "</pre>";

                        if (count($spGroup) > 0)
                        {
                            $special = true;
                                    
                            foreach ($spGroup as $group)
                            {
                                $spGroupId[] = $group->sp_group_id;
                            }

                            $prices = DB::table('jocom_sp_product_price AS sp')
                                ->select('np.label', 'np.label_cn', 'np.label_my', 'np.seller_sku', 'np.stock', 'np.stock_unit', 'np.p_weight', 'sp.id', 'sp.price', 'sp.price_promo', 'sp.qty', 'sp.p_referral_fees', 'sp.p_referral_fees_type', 'sp.disc_amount', 'sp.disc_type', 'sp.default', 'sp.product_id', 'sp.status')
                                ->leftJoin('jocom_product_price AS np', 'sp.label_id', '=', 'np.id')
                                ->where('sp.status', '=', 1)
                                ->where('sp.product_id', '=', $productId)
                                ->whereIn('sp.sp_group_id', $spGroupId)
                                ->orderBy('sp.default', 'DESC')
                                ->orderBy('sp.price', 'ASC')
                                ->get();
                            
                            // Log::info(DB::getQueryLog());
                        }

                        if (count($prices) == 0)
                        {
                            $special = false;
                            $prices  = Price::getActivePrices($productId);
                        }
                    }
                    else
                    {
                        $special = false;
                        $prices  = Price::getActivePrices($productId);
                    }

                    if (( ! $special && $prices->toArray()) || ($special && $prices))
                    {
                        foreach ($prices as $price)
                        {
                            $priceLabel = ($price->{'label'.$language}) ?
                                $price->{'label'.$language} : $price->label;

                            $gst = Product::getGstValue($price->id, $special);

                            $promotionPrice = ($price->price_promo != 0) ?
                                number_format(($price->price_promo * $gst), 2, '.', '') : 0;

                            if ($special)
                            {
                               
                                $qty        = SpecialPrice::get_default_qty();
                                $sp_price   = $price->price;
                            
                                if ($price->disc_amount > 0 && $price->disc_type != "") {
                                    switch($price->disc_type) {
                                        case '%' :
                                                $discount   = $price->price * ($price->disc_amount / 100);
                                                $sp_price   = number_format($price->price - $discount, 2);
                                            break;

                                        case 'N' :
                                                $sp_price   = number_format($price->price - $price->disc_amount, 2);
                                            break;
                                    }
                                }

                                $pricesArray[] = [
                                    'id'            => $price->id, // 'S'.$price->id, // TEMPORARY FIX FOR SPECIAL PRICE
                                    // 'id'            => 'SPCL'.$price->id,
                                    'label'         => strip_tags($priceLabel),
                                    'price'         => ApiProduct::hidePricing($username, number_format(($sp_price * $gst), 2, '.', '')),
                                    'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                    'qty'           => $qty->default_qty,
                                    'stock'         => $price->stock,
                                    'stock_unit'    => $price->stock_unit,
                                    'default'       => 'TRUE',
                                    // 'default'       => ($price->default == 1) ? 'TRUE' : 'FALSE',
                                    'p_weight'      => $price->p_weight,
                                    'discount_percent' => '',
                                    'jpoint'        => 0,
                                    'bpoint'        => 0,
                                ];
                                
                                // echo "<pre>";
                                // print_r($pricesArray);
                                // echo "</pre>";
                            }
                            else
                            {
                                $qty = $price->qty;

                                if ($charity_id != 0)
                                {
                                    $tempqty = CharityProduct::getCharityQuantity($price->id, $charity_id);

                                    if (count($tempqty) > 0)
                                        $qty = $tempqty->qty;
                                }
                                
                                $orginalPrice = ApiProduct::hidePricing($username, number_format(($price->price * $gst), 2, '.', ''));
                                $discPrice =  ApiProduct::hidePricing($username, $promotionPrice);  

                                if ($discPrice != "0") {
                                    $discpercent = (($orginalPrice - $discPrice)*100) /$orginalPrice ; 
                                    $percent = number_format($discpercent, 0).'%';
                                }else{
                                    $percent = '';
                                }
                                
                                $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
                                $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
                                $multiply = 1;
                                
                                $key = $product->qrcode;
                                
                                $date = date('Y-m-d h:i:s');
                                
                                // SPECIAL CAMPAIGN //
                                
                                $startdate = date('2018-08-06 00:00:00');
                                $enddate = date('2018-09-24 23:59:59');
                                
                                if (strtotime($date) >= strtotime($startdate) && strtotime($date) <= strtotime($enddate)) {
                                
                                    $multiply_extra = array( 
                                        "JC15061" => 2
                                    );
                                             
                                    if (array_key_exists($key,$multiply_extra) ){
                                        $multiplyBP = $multiply_extra[$key];
                                    }else{
                                        $multiplyBP = 1;
                                    } 
                                
                                }else{
                                    $multiplyBP = 1;
                                }
                                // Noob campaign 8 nov - 14 nov 2018
                                if(date("Y-m-d h:i:s") >= '2018-11-08 00:00:00' && date("Y-m-d h:i:s") <= '2018-11-14 23:59:59'){
                                    $multiplyBP = 3;
                                }
                                
                                // SPECIAL CAMPAIGN //
                                
                                // $pointsJpoint = ($price->price) * $Jpoint->earn_rate * $multiply;
                                // $pointsBpoint = ($price->price) * $Bpoint->earn_rate * $multiply;
                                
                                if ($discPrice > 0) {
                                    $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
                                    // $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiplyBP;
                                    $pointsBpoint = 0;
                                }else{
                                    $pointsJpoint = ($price->price) * $Jpoint->earn_rate * $multiply;
                                    // $pointsBpoint = ($price->price) * $Bpoint->earn_rate * $multiplyBP;
                                    $pointsBpoint = 0;
                                }

                                $pricesArray[] = [
                                    'id'            => $price->id,
                                    'label'         => strip_tags($priceLabel),
                                    'price'         => ApiProduct::hidePricing($username, number_format(($price->price * $gst), 2, '.', '')),
                                    'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                    'qty'           => $qty,
                                    'stock'         => $price->stock,
                                    'stock_unit'    => $price->stock_unit,
                                    'default'       => ($price->default == 1) ? 'TRUE' : 'FALSE',
                                    'p_weight'      => $price->p_weight,
                                    'discount_percent' => $percent,
                                    'jpoint'        => floor($pointsJpoint),
                                    'bpoint'        => floor($pointsBpoint),
                                ];
                            }
                        }
                    }
                    else
                    {
                        $data['record']--;
                        $data['total_record']--;

                        if ($data['total_record'] == 0)
                        {
                            $data['status_msg'] = '#201';
                        }
                    }
                }

                // if ($product->freshness_days !='') {
                //   $freshness_tag = 'Guaranteed fresh for '. $product->freshness_days. ' days, inc. delivery day';
                // }else{
                //     $freshness_tag = '';
                // }
                if ($product->freshness_days !='' && $product->freshness_days != 0) {
                   $freshness_tag = $product->freshness_days. ' days freshness.';
                }else{
                    $freshness_tag = '';
                }
                
                // $total_sold = DB::table('jocom_transaction_details AS JTD')
                //         ->leftJoin('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
                //         ->where('JTD.sku','=',$product->sku)
                //         ->where('JT.status','=','completed')
                //         ->count();
                
                $total_sold = DB::table('jocom_transaction_details AS JTD')
                        ->leftJoin('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
                        ->where('JTD.sku','=',$product->sku)
                        ->where('JT.status','=','completed')
                        ->sum('JTD.unit');
                        
                if ($total_sold != "0") {
                    // $total = $total_sold;
                    $total = round($total_sold,2);
                }else{
                    $total = '';
                }
                
                $points  = Comment::scopeCommentsRating($product->id);
                
                $delivery_time = empty($product->delivery_time) ? '24 hours' : $product->delivery_time;
                
                switch ($delivery_time) {
                    case '24 hours':
                        $delivery_from = 1;
                        $delivery_to = 1;

                        break;
                    
                    case '1-2 business days2-3 business days':
                        $delivery_from = 1;
                        $delivery_to = 2;

                        break;
                    
                    case '2-3 business days':
                        $delivery_from = 2;
                        $delivery_to = 3;

                        break;
                    
                    case '3-7 business days':
                        $delivery_from = 3;
                        $delivery_to = 7;

                        break;
                    case '14 business days':
                        $delivery_from = 1;
                        $delivery_to = 14;

                        break;

                    default:
                        break;
                }
                //restriction module added by boobalan on 08/03/2023
                $restriction=DB::table('jocom_products')
                             ->select('is_jpoint','is_voucher_code','is_bank_card_promo')
                             ->where('id','=',$product->id)
                             ->first();

                if (reset($pricesArray) && ! (isset($proOptIds) && empty($proOptIds)))
                {
                    if(version_compare($vApp,'3.2.4','>=')){
                        
                        $data['item'][] = [
                        'sku'               => $product->sku,
                        'name'              => $productName,
                        'description'       => $productDescription,
                        'delivery_time'     => empty($product->delivery_time) ? '24 hours' : $product->delivery_time,
                        'delivery_day_from' => $delivery_from ,
                        'delivery_day_to'   => $delivery_to,
                        'min_qty'           => empty($product->min_qty) ? '' : $product->min_qty,
                        'max_qty'           => empty($product->max_qty) ? '' : $product->max_qty,
                        'total_sold'        => $total,
                        'img_1'             => $images[1],
                        'img_2'             => $images[2],
                        'img_3'             => $images[3],
                        'thumb_1'           => $thumbnails[1],
                        'thumb_2'           => $thumbnails[2],
                        'thumb_3'           => $thumbnails[3],
                        'vid_1'             => $video,
                        'qr_code'           => empty($product->qrcode) ? '' : $product->qrcode,
                        'zone_records'      => count($zones),
                        'delivery_zones'    => [$zones],
                        'price_records'     => count($pricesArray),
                        'price_option'      => ['option' => $pricesArray],
                        'related_products'  => $product->related_product,
                        'freshness'         => $product->freshness,
                        'freshness_days'    => $freshness_tag,
                        'bulk'              => empty($product->bulk) ? '0' : $product->bulk,
                        'halal'             => empty($product->halal) ? '0' : $product->halal,
                        'overall_rating'    => empty($points) ? '' : $points,
                        'jpoint'            => $restriction->is_jpoint,
                        'voucher_code'      => $restriction->is_voucher_code,
                        'bank_card_promo'   => $restriction->is_bank_card_promo,
                    ];
                        
                    }else{
                        
                        $data['item'][] = [
                        'sku'               => $product->sku,
                        'name'              => $productName,
                        'description'       => $productDescription,
                        'delivery_time'     => empty($product->delivery_time) ? '24 hours' : $product->delivery_time,
                       // 'delivery_day_from' => $delivery_from ,
                       // 'delivery_day_to'   => $delivery_to,
                        'min_qty'           => empty($product->min_qty) ? '' : $product->min_qty,
                        'max_qty'           => empty($product->max_qty) ? '' : $product->max_qty,
                        'total_sold'        => $total,
                        'img_1'             => $images[1],
                        'img_2'             => $images[2],
                        'img_3'             => $images[3],
                        'thumb_1'           => $thumbnails[1],
                        'thumb_2'           => $thumbnails[2],
                        'thumb_3'           => $thumbnails[3],
                        'vid_1'             => $video,
                        'qr_code'           => empty($product->qrcode) ? '' : $product->qrcode,
                        'zone_records'      => count($zones),
                        'delivery_zones'    => [$zones],
                        'price_records'     => count($pricesArray),
                        'price_option'      => ['option' => $pricesArray],
                        'related_products'  => $product->related_product,
                        'freshness'         => $product->freshness,
                        'freshness_days'    => $freshness_tag,
                        'bulk'              => empty($product->bulk) ? '0' : $product->bulk,
                        'halal'             => empty($product->halal) ? '0' : $product->halal,
                        'overall_rating'    => empty($points) ? '' : $points,
                        'jpoint'            => $restriction->is_jpoint,
                        'voucher_code'      => $restriction->is_voucher_code,
                        'bank_card_promo'   => $restriction->is_bank_card_promo,
                    ];
                        
                    }
                    
                }
            }
        }

        return ['xml_data' => $data];
        
       
    }
    
    public static function fetch_product_ios($limit = 250, $offset = 0, $params = [])
    {
        
       
        // $ApiLog = new ApiLog ;
        // $ApiLog->api = 'API_PRODUCT_IOS';
        // $ApiLog->data = json_encode($params);
        // $ApiLog->save();
        
        //uncomment this code for new app 
        $vApp = Input::get('vapp');
        $v3App = false;

        if($vApp >= 3.0){
            $v3App = true;
        }
        
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
            $regions_id = $regions->region_id;

        }else{
            if(Input::get('id') != ''){

            $id = Input::get('id');
                if($id == RegionController::KLANGVALLEYSTATEID){
                    $regions_id = RegionController::KLANGVALLEYREGIONID;
                }else{
                    $regions = State::find($id);
                    $regions_id = $regions->region_id;
                }

            }else{

        }         
 
        }
 
        $category   = array_get($params, 'product_cat', NULL);
        $codes      = stripslashes(array_get($params, 'code', NULL));
        $code2      = array_get($params, 'code2', NULL);
        $name       = array_get($params, 'name', NULL);
        $permission = array_get($params, 'custom', 0);
        $username   = array_get($params, 'username', NULL);
        $requestBanner = array_get($params, 'banner', 0);
        $city       = array_get($params, 'city', NULL);   // Added by Maruthu
        $charity_id = 0;
        $device     = array_get($params, 'device', 'phone');
       
        switch ($device)
        {
            case 'phone':
                $image_path         = Config::get('constants.BANNER_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BANNER_THUMB_FILE_PATH');
                break;
            
            case 'tablet':
                $image_path         = Config::get('constants.BANNER_TAB_FILE_PATH');        //'./public/images/banner/';
                $thumb_path         = Config::get('constants.BANNER_TAB_THUMB_FILE_PATH');
                break;
        }

        // if no app platform code will take JOC as platform code
        $platform    = array_get($params, 'platform', Platform::JOCOM_APP_CODE);        
        
//      echo print_r($params); die();

        if ( ! (is_numeric($limit) && $limit > 0 && is_numeric($offset)))
        {
            $limit  = 20;
            $offset = 0;
        }

        $data       = [
            'banner'        => '',
            'record'        => 0,
            'total_record'  => 0,
            'item'          => [],            
        ];

        switch (array_get($params, 'lang'))
        {
            case 'CN':
                $language = '_cn';
                $lang = 'cn';
                break;
            case 'MY':
                $language = '_my';
                $lang = 'my';
                break;
            default:
                $language = '';
                $lang = 'en';
                break;
        }

        if (is_numeric($category) && empty($name) && empty($city))
        {
            
            if($v3App){
        
            $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id');
                
                // IF JOHOR REGION ONLY RETURN JOHOR PRODUCT : REQUESTED BY JOHOR MANAGEMENT //
                if($regions_id == 2 || $regions_id == 3){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                
                } else if($regions_id == 5){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id]);
                }else{
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                }
                    
                $now = date_format(Carbon\Carbon::now(), 'Y-m-d H:i:s');
                // Checking on platform
                $products->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1 AND (new_arrival_expire is null OR new_arrival_expire > '${now}')")
                
                // uncomment for new app
                ->orderBy('jocom_product_and_package.region_id', 'desc');
                
                // not able to sort by ID, sql not able to sort properly
                // ->orderBy('jocom_product_and_package.id', 'desc');
                // ->orderBy('name', 'asc');
            // WHEN JUEPIN IS READY //
            /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                }
            */
            // WHEN JUEPIN IS READY //

            }else{
                
            
                $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1");
            }
            
            
            $products      = $products->orderBy('weight', 'desc');
            $products      = $products->orderBy('jocom_product_and_package.sku', 'desc');
            $totalProducts = $products->count();
            $products      = $products->skip($offset)->take($limit)->get();

            // request category banner
            if ($requestBanner == 1)
            {
                $bannerlist = array();

                $charity_cat = CharityCategory::select('id')->where('category_id', $category)->first();

                if(count($charity_cat) > 0)
                    $charity_id = $charity_cat->id;

                $banners = Banner::select('jocom_banners.id', 'jocom_banners.pos', 'jocom_banners.url_link', 'jocom_banners.qrcode')
                        ->where('jocom_banners.category_id', '=', $category)
                        ->groupby('jocom_banners.id')
                        ->orderBy('jocom_banners.pos', 'desc')
                        ->get();

                if(count($banners) > 0)
                {
                    foreach ($banners as $banner)
                    {
                        $image_name = "";
                        $file_name  = "";
                        $thumb_name = "";
                        $bimage     = Banner::getImagesByLanguage($banner->id, $device, $lang);

                        if(count($bimage) > 0)
                        {
                            $image_name = $bimage->file_name;
                            $file_name  = $image_path . $lang . "/" . $bimage->file_name;
                            $thumb_name = $thumb_path . $lang . "/" . $bimage->thumb_name;
                        }
                        else
                        {
                            $default_images = Banner::getImagesByLanguage($banner->id, "phone", "en");

                            if(count($default_images) > 0)
                            {
                                $image_name     = $default_images->file_name;
                                $file_name      = Config::get('constants.BANNER_FILE_PATH') . "en/" . $default_images->file_name;
                                $thumb_name     = Config::get('constants.BANNER_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                            }
                        }

                        if ($file_name != "" && file_exists('./' . $file_name))
                        {
                            if(!file_exists('./' . $thumb_name))
                            {
                                // echo "<br> NOT EXISTS! - ". '/public/' . $thumb_name;
                                create_thumbnail($image_name, $thumb_width, $thumb_height, './' . $image_path . "en", './' . $thumb_path . $lang . "/");
                            }

                            $bannerlist[] = array(
                                'id'        => $banner->id,
                                'img'       => Image::link($file_name),
                                'thumbnail' => Image::link($thumb_name),
                                'url'       => $banner->url_link,
                                'qrcode'    => $banner->qrcode
                            );
                        }
                    }
                }


                $data['banner'] = $bannerlist;
            }
        }
        else if (is_numeric($category) && empty($name) && is_numeric($city))
        {
             // die('New City');
            
            if($v3App){
          
            $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_delivery', 'jocom_categories.product_id', '=', 'jocom_product_delivery.product_id') // Added by Maruthu
                ->leftJoin('jocom_zone_cities', 'jocom_product_delivery.zone_id', '=', 'jocom_zone_cities.zone_id') // Added by Maruthu
                ->leftJoin('jocom_zones', 'jocom_zones.id', '=', 'jocom_zone_cities.zone_id'); // Added by Maruthu
                // uncomment for new app
                
                 if($regions_id == 2 || $regions_id == 3){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                 } else if($regions_id == 5){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id]);
                }else{
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0])->orWhere('jocom_product_and_package.region_id', '=',0) ;
                }
                    
                
                $products->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1")
                // uncomment for new app
                ->orderBy('jocom_product_and_package.region_id', 'desc');
                
                // WHEN JUEPIN APP IS READY //
                // Handle Platform Request 
                /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                }
                */
                // WHEN JUEPIN APP IS READY //

            }else{
                         
                $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.id as id', 'jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_delivery', 'jocom_categories.product_id', '=', 'jocom_product_delivery.product_id') // Added by Maruthu
                ->leftJoin('jocom_zone_cities', 'jocom_product_delivery.zone_id', '=', 'jocom_zone_cities.zone_id') // Added by Maruthu
                // uncomment for new app
//                ->whereIn('jocom_product_and_package.region_id',[$regions_id,0])
//                ->orWhere('jocom_product_and_package.region_id', '=',0)
                ->whereRaw("((sku LIKE 'TM-%' AND jocom_categories.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1");
                // uncomment for new app
//                ->orderBy('jocom_product_and_package.region_id', 'desc');
                
            }    
            $products->orderBy('weight', 'desc');
            $products->orderBy('jocom_product_and_package.sku', 'desc');
            // not able to sort by ID, sql not able to sort properly
            // ->orderBy('jocom_product_and_package.id', 'desc');
            // ->orderBy('name', 'asc');

            $totalProducts = $products->count();
            $products      = $products->skip($offset)->take($limit)->get();
            
            // request category banner
            if ($requestBanner == 1)
            {
                $bannerlist = array();

                $charity_cat = CharityCategory::select('id')->where('category_id', $category)->first();

                if(count($charity_cat) > 0)
                    $charity_id = $charity_cat->id;

                $banners = Banner::select('jocom_banners.id', 'jocom_banners.pos', 'jocom_banners.url_link', 'jocom_banners.qrcode')
                        ->where('jocom_banners.category_id', '=', $category)
                        ->groupby('jocom_banners.id')
                        ->orderBy('jocom_banners.pos', 'desc')
                        ->get();

                if(count($banners) > 0)
                {
                    foreach ($banners as $banner)
                    {
                        $image_name = "";
                        $file_name  = "";
                        $thumb_name = "";
                        $bimage     = Banner::getImagesByLanguage($banner->id, $device, $lang);

                        if(count($bimage) > 0)
                        {
                            $image_name = $bimage->file_name;
                            $file_name  = $image_path . $lang . "/" . $bimage->file_name;
                            $thumb_name = $thumb_path . $lang . "/" . $bimage->thumb_name;
                        }
                        else
                        {
                            $default_images = Banner::getImagesByLanguage($banner->id, "phone", "en");

                            if(count($default_images) > 0)
                            {
                                $image_name     = $default_images->file_name;
                                $file_name      = Config::get('constants.BANNER_FILE_PATH') . "en/" . $default_images->file_name;
                                $thumb_name     = Config::get('constants.BANNER_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                            }
                        }

                        if ($file_name != "" && file_exists('./' . $file_name))
                        {
                            if(!file_exists('./' . $thumb_name))
                            {
                                // echo "<br> NOT EXISTS! - ". '/public/' . $thumb_name;
                                create_thumbnail($image_name, $thumb_width, $thumb_height, './' . $image_path . "en", './' . $thumb_path . $lang . "/");
                            }

                            $bannerlist[] = array(
                                'id'        => $banner->id,
                                'img'       => Image::link($file_name),
                                'thumbnail' => Image::link($thumb_name),
                                'url'       => $banner->url_link,
                                'qrcode'    => $banner->qrcode
                            );
                        }
                    }
                }


                $data['banner'] = $bannerlist;
            }
        }
        elseif ( ! empty($codes) AND empty($code2))
        {
            //echo "hwee";
            ksort($codes);
 
            $codes = "'".implode("', '", $codes)."'";

            $products = DB::table('jocom_product_and_package')
                ->select('*')
                ->where('status', '=', 1)
                ->whereRaw("qrcode IN ({$codes})");

            // Handle Platform Request  
            
            // WHEN JUEPIN APP IS READY //
            /*
            switch ($platform) {
                case Platform::JOCOM_APP_CODE:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
                case Platform::JUEPIN_APP_CODE:
                    $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                    break;
                default:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
            }
            */
            // WHEN JUEPIN APP IS READY //
            
            $products->orderByRaw("FIELD(qrcode, {$codes})");
            $totalProducts  = $products->count();
            $products       = $products->skip($offset)->take($limit)->get();
        }
        elseif ( ! empty($code2))
        { 
            
            ksort($code2);

            $code2 = "'".implode("', '", $code2)."'";

            $products = DB::table('jocom_product_and_package')
                ->select('*')
                ->where('status', '=', 1)
                ->whereRaw("qrcode IN ({$code2})");

            // Handle Platform Request  
            // WHEN JUEPIN APP IS READY //
            /*
            switch ($platform) {
                case Platform::JOCOM_APP_CODE:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
                case Platform::JUEPIN_APP_CODE:
                    $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                    break;
                default:
                    $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                    break;
            }
            */
            // WHEN JUEPIN APP IS READY //
            
            $products->orderByRaw("FIELD(qrcode, {$code2})");

            $totalProducts  = $products->count();
            $products       = $products->skip($offset)->take($limit)->get();
        }
        elseif ( ! empty($name) && strlen($name) >= 2)
        {
         
            $category       = isset($category) ? $category : 0;
            $categoryList   = [];
            $isPrivateApp   = false;

            // Private App
            if (isset($username) && ! empty($username) && $category == 0)
            {
                $userCategories = DB::table('jocom_user_category')
                    ->select('category_id')
                    ->where('username', '=', $username)
                    ->get();

                if ($userCategories)
                {
                    $isPrivateApp = true;

                    foreach ($userCategories as $userCategory)
                    {
                        $categoryList[] = $userCategory->category_id;
                    }
                }
            }

            if ($isPrivateApp || $category > 0)
            {
                $categories = Category::getByParent($category, $permission, $categoryList);
            }
            else
            {
                $categories = Category::where('category_parent', '=', $category)
                    ->where('permission', '=', $permission)
                    ->where('status', '=', 1)
                    ->where('id', '>', 0)
                    ->get();
            }

            $familyCategories = [];

            if ($categories->count() > 0)
            {
                foreach ($categories as $category)
                {
                    $familyCategories[] = $category->id;
                    $childCategories    = array_pluck(ApiProduct::fetchCategoryTree($category->id), 'id');
                    $familyCategories   = array_merge($familyCategories, $childCategories);
                }
            }

            $familyCategories = implode(', ', array_unique($familyCategories, SORT_NUMERIC));

            // $name = str_replace('\'', '\'\'', $name);

            $name = addslashes($name);
           
    	    if(! empty($city) && is_numeric($city))
            {
                
                
                
                $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_tags', 'jocom_product_and_package.id', '=', 'jocom_product_tags.product_id')
                ->leftJoin('jocom_product_delivery', 'jocom_product_and_package.id', '=', 'jocom_product_delivery.product_id') 
                ->leftJoin('jocom_zone_cities', 'jocom_product_delivery.zone_id', '=', 'jocom_zone_cities.zone_id') 
                ->where('jocom_product_and_package.status', '=', 1)
                ->whereRaw("(tag_name LIKE '%{$name}%' OR name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'TM-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'TMP-%' AND category IN ({$familyCategories}))) AND status = 1 AND jocom_zone_cities.city_id={$city}" 
                );
                // Handle Platform Request  
                // WHEN JUEPIN APP IS READY //
                /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
            }
                */
                // WHEN JUEPIN APP IS READY //
                
                $products->groupBy('sku');
                $products->orderBy('weight', 'desc');
                $products->orderBy('name', 'asc');
            }
            else
            {
                
               // echo $name;die();
                
                if($v3App){
                    
                    $products = DB::table('jocom_product_and_package')
                        ->select('jocom_product_and_package.*')
                        ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                        ->leftJoin('jocom_product_tags', 'jocom_product_and_package.id', '=', 'jocom_product_tags.product_id')
                        ->where('jocom_product_and_package.status', '=', 1);
                        
                if($regions_id == 2 || $regions_id == 3){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]);
                } else if($regions_id == 5){
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id]);
                }else{
                    $products = $products->whereIn('jocom_product_and_package.region_id',[$regions_id,0]) ;
                }
                        
           
                    //   $products->whereRaw("(tag_name LIKE '%{$name}%' OR name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1"
                    //          // "(name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1 AND permission = {$permission}"
                    //     );
                        
                        
                         $products->whereRaw("(name LIKE '%{$name}%' ) AND ((sku LIKE 'TM-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'TMP-%' AND category IN ({$familyCategories}))) AND status = 1"
                             // "(name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1 AND permission = {$permission}"
                        );

                    
                
                }else{
                    
                    $products = DB::table('jocom_product_and_package')
                ->select('jocom_product_and_package.*')
                ->leftJoin('jocom_categories', 'jocom_product_and_package.id', '=', 'jocom_categories.product_id')
                ->leftJoin('jocom_product_tags', 'jocom_product_and_package.id', '=', 'jocom_product_tags.product_id')
                ->whereRaw("(tag_name LIKE '%{$name}%' OR name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'TM-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'TMP-%' AND category IN ({$familyCategories}))) AND status = 1"
                     // "(name LIKE '%{$name}%' OR name_cn LIKE '%{$name}%' OR name_my LIKE '%{$name}%') AND ((sku LIKE 'JC-%' AND jocom_categories.category_id IN ({$familyCategories})) OR (sku LIKE 'JCP-%' AND category IN ({$familyCategories}))) AND status = 1 AND permission = {$permission}"
                );
                // Handle Platform Request  
                
                // WHEN JUEPIN APP IS READY //
                /*
                switch ($platform) {
                    case Platform::JOCOM_APP_CODE:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
                    case Platform::JUEPIN_APP_CODE:
                        $products->where('jocom_product_and_package.juepin_app_available',"=",1);
                        break;
                    default:
                        $products->where('jocom_product_and_package.jocom_app_available',"=",1);
                        break;
            }
                */
                // WHEN JUEPIN APP IS READY //
                    
                    
                        
                }
                
	            
                $products->groupBy('sku');
                $products->orderBy('weight', 'desc');
                $products->orderBy('name', 'asc');
            }
            $totalProducts = count($products->get());

            $products = $products->skip($offset)->take($limit)->get();
            
        }
        else
        {
            return ['xml_data' => array_merge($data, ['status_msg' => '#201'])];
        }

        if ($totalProducts <= 0)
        {
            return ['xml_data' => array_merge($data, ['status_msg' => '#201'])];
        }
        else
        {
            $zoneNames              = Zone::lists('name'); // To be used by package-zone match
            $data['record']         = count($products);
            $data['total_record']   = $totalProducts;

            foreach ($products as $product)
            {
                
               
                for ($i = 1; $i <= 3; $i++)
                {
                    $filename = $product->{"img_{$i}"};

                    $images[$i]     = ( ! empty($filename)) ? Image::link("images/data/{$filename}") : '';
                    $thumbnails[$i] = ( ! empty($filename)) ? Image::link("images/data/thumbs/{$filename}") : '';
                }

                $productName        = ($product->{'name'.$language}) ? $product->{'name'.$language} : $product->name;
                $productDescription = ($product->{'description'.$language}) ? $product->{'description'.$language} : $product->description;
                $filename           = $product->vid_1;
                // $videoPath          = asset("media/video/files/{$filename}");
                $video              = ( $filename != "") ? $filename : '';
                $pricesArray        = [];

                /**
                 * If ID is prefixed with 'P' meaning it is a package
                 * Otherwise it is a product
                 */
                if (substr($product->id, 0, 1) == 'P')
                {
                    $packageId      = substr($product->id, 1);
                    $proOptIds      = [];
                    $proQtyCal      = [];

                    $deliveryZones  = (array) DB::table('jocom_package_delivery')
                        ->where('jocom_package_delivery.package_id', '=', $packageId)
                        ->first();
                    $deliveryZones  = array_unique(explode(',', $deliveryZones['zone']));
                    $zones          = [];

                    foreach ($deliveryZones as $deliveryZone)
                    {
                        // $zoneNames array key starts from 0 and $deliveryZone starts from 1, so minus 1 to get the correct zone name
                        $zones[] = [
                            'zone' => $deliveryZone,
                            'zone_name' => $zoneNames[$deliveryZone - 1]
                        ];
                    }

                    $packageProducts = DB::table('jocom_product_package_product')
                        ->where('package_id', '=', $packageId)
                        ->get();

                    foreach ($packageProducts as $packageProduct)
                    {
                        $proOptIds[$packageProduct->product_opt] = $packageProduct->qty;
                        $proQtyCal[$packageProduct->product_opt] = 0;
                    }

                    $actualPrice    = 0;
                    $promotionPrice = 0;

                    if (empty($proOptIds))
                    {
                        $pricesArray[] = [
                            'id'            => 'Null',
                            'label'         => 'Null',
                            'price'         => 0,
                            'promo_price'   => 0,
                            'qty'           => 0,
                            'stock'         => 0,
                            'stock_unit'    => 0,
                            'default'       => 'TRUE',
                            'p_weight'      => 0,
                        ];
                    }
                    else
                    {
                        $prices = Price::getMultiActivePrices($proOptIds);
                        $maxQty = false;

                        foreach ($prices as $price)
                        {
                            $gst            = Product::getGstValue($price->id);
                            $actualPrice   += $price->price * $proOptIds[$price->id] * $gst;

                            if ($price->price_promo > 0)
                            {
                                $promotionPrice += $price->price_promo * $proOptIds[$price->id] * $gst;
                            }
                            else
                            {
                                $promotionPrice += $price->price * $proOptIds[$price->id] * $gst;
                            }

                            $proQtyCal[$price->id] = $price->qty;
                        }

                        foreach ($proQtyCal as $mQty)
                        {
                            if (($maxQty === false) || ($maxQty > $mQty))
                            {
                                $maxQty = $mQty;
                            }
                        }

                        if ($actualPrice > 0)
                        {
                            $promotionPrice = ($actualPrice == $promotionPrice) ?
                                0 : number_format($promotionPrice, 2, '.', '');
                            $actualPrice    = number_format($actualPrice, 2, '.', '');

                            $pricesArray[] = [
                                'id'            => 'Null',
                                'label'         => 'Null',
                                'price'         => ApiProduct::hidePricing($username, $actualPrice),
                                'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                'qty'           => $maxQty,
                                'stock'         => 0,
                                'stock_unit'    => 0,
                                'default'       => 'TRUE',
                                'p_weight'      => 0,
                            ];
                        }
                        else
                        {
                            $pricesArray[] = [
                                'id'            => 'Null',
                                'label'         => 'Null',
                                'price'         => ApiProduct::hidePricing($username, $actualPrice),
                                'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                'qty'           => $maxQty,
                                'stock'         => 0,
                                'stock_unit'    => 0,
                                'default'       => 'TRUE',
                                'p_weight'      => 0,
                            ];
                        }
                    }
                }
                else
                {
                    unset($prices);
                    $zones          = [];
                    $deliveryZones  = Delivery::getZonesByProduct($product->id);
                    $productId      = (int) next(explode('-', $product->sku));

                    foreach ($deliveryZones as $deliveryZone)
                    {
                        $zones[] = [
                            'zone' => $deliveryZone->zone_id,
                            'zone_name' => $deliveryZone->zone_name,
                        ];
                    }

                    if (isset($username) && ! empty($username))
                    {
                        $spGroup = DB::table('jocom_user AS user')
                            ->select('group.sp_group_id')
                            ->leftJoin('jocom_sp_customer AS customer', 'user.id', '=', 'customer.user_id')
                            ->leftJoin('jocom_sp_customer_group AS group', 'customer.id', '=', 'group.sp_cust_id')
                            ->where('user.username', '=', $username)
                            ->get();
                            
                        // echo "<pre>";
                        // print_r($spGroup);
                        // echo "</pre>";

                        if (count($spGroup) > 0)
                        {
                            $special = true;
                                    
                            foreach ($spGroup as $group)
                            {
                                $spGroupId[] = $group->sp_group_id;
                            }

                            $prices = DB::table('jocom_sp_product_price AS sp')
                                ->select('np.label', 'np.label_cn', 'np.label_my', 'np.seller_sku', 'np.stock', 'np.stock_unit', 'np.p_weight', 'sp.id', 'sp.price', 'sp.price_promo', 'sp.qty', 'sp.p_referral_fees', 'sp.p_referral_fees_type', 'sp.disc_amount', 'sp.disc_type', 'sp.default', 'sp.product_id', 'sp.status')
                                ->leftJoin('jocom_product_price AS np', 'sp.label_id', '=', 'np.id')
                                ->where('sp.status', '=', 1)
                                ->where('sp.product_id', '=', $productId)
                                ->whereIn('sp.sp_group_id', $spGroupId)
                                ->orderBy('sp.default', 'DESC')
                                ->orderBy('sp.price', 'ASC')
                                ->get();
                            
                            // Log::info(DB::getQueryLog());
                        }

                        if (count($prices) == 0)
                        {
                            $special = false;
                            $prices  = Price::getActivePrices($productId);
                        }
                    }
                    else
                    {
                        $special = false;
                        $prices  = Price::getActivePrices($productId);
                    }

                    if (( ! $special && $prices->toArray()) || ($special && $prices))
                    {
                        foreach ($prices as $price)
                        {
                            $priceLabel = ($price->{'label'.$language}) ?
                                $price->{'label'.$language} : $price->label;

                            $gst = Product::getGstValue($price->id, $special);

                            $promotionPrice = ($price->price_promo != 0) ?
                                number_format(($price->price_promo * $gst), 2, '.', '') : 0;

                            if ($special)
                            {
                               
                                $qty        = SpecialPrice::get_default_qty();
                                $sp_price   = $price->price;
                            
                                if ($price->disc_amount > 0 && $price->disc_type != "") {
                                    switch($price->disc_type) {
                                        case '%' :
                                                $discount   = $price->price * ($price->disc_amount / 100);
                                                $sp_price   = number_format($price->price - $discount, 2);
                                            break;

                                        case 'N' :
                                                $sp_price   = number_format($price->price - $price->disc_amount, 2);
                                            break;
                                    }
                                }

                                $pricesArray[] = [
                                    'id'            => $price->id, // 'S'.$price->id, // TEMPORARY FIX FOR SPECIAL PRICE
                                    // 'id'            => 'SPCL'.$price->id,
                                    'label'         => strip_tags($priceLabel),
                                    'price'         => ApiProduct::hidePricing($username, number_format(($sp_price * $gst), 2, '.', '')),
                                    'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                    'qty'           => $qty->default_qty,
                                    'stock'         => $price->stock,
                                    'stock_unit'    => $price->stock_unit,
                                    'default'       => 'TRUE',
                                    // 'default'       => ($price->default == 1) ? 'TRUE' : 'FALSE',
                                    'p_weight'      => $price->p_weight,
                                    'discount_percent' => '',
                                    'jpoint'        => '',
                                    'bpoint'        => '',
                                ];
                                
                                // echo "<pre>";
                                // print_r($pricesArray);
                                // echo "</pre>";
                            }
                            else
                            {
                                $qty = $price->qty;

                                if ($charity_id != 0)
                                {
                                    $tempqty = CharityProduct::getCharityQuantity($price->id, $charity_id);

                                    if (count($tempqty) > 0)
                                        $qty = $tempqty->qty;
                                }
                                
                                $orginalPrice = ApiProduct::hidePricing($username, number_format(($price->price * $gst), 2, '.', ''));
                                $discPrice =  ApiProduct::hidePricing($username, $promotionPrice);  

                                if ($discPrice != "0") {
                                    $discpercent = (($orginalPrice - $discPrice)*100) /$orginalPrice ; 
                                    $percent = number_format($discpercent, 0).'%';
                                }else{
                                    $percent = '';
                                }
                                
                                $Jpoint = PointType::where('type','=', 'Jpoint')->where('status',1)->first();
                                $Bpoint = PointType::where('type','=', 'Bcard')->where('status',1)->first();
                                $multiply = 1;
                                
                                $key = $product->qrcode;
                                
                                $date = date('Y-m-d h:i:s');
                                
                                // SPECIAL CAMPAIGN //
                                
                                $startdate = date('2018-08-06 00:00:00');
                                $enddate = date('2018-09-24 23:59:59');
                                
                                if (strtotime($date) >= strtotime($startdate) && strtotime($date) <= strtotime($enddate)) {
                                
                                    $multiply_extra = array( 
                                        "JC15061" => 2
                                    );
                                             
                                    if (array_key_exists($key,$multiply_extra) ){
                                        $multiplyBP = $multiply_extra[$key];
                                    }else{
                                        $multiplyBP = 1;
                                    } 
                                
                                }else{
                                    $multiplyBP = 1;
                                }
                                // Noob campaign 8 nov - 14 nov 2018
                                if(date("Y-m-d h:i:s") >= '2018-11-08 00:00:00' && date("Y-m-d h:i:s") <= '2018-11-14 23:59:59'){
                                    $multiplyBP = 3;
                                }
                                
                                // SPECIAL CAMPAIGN //
                                
                                // $pointsJpoint = ($price->price) * $Jpoint->earn_rate * $multiply;
                                // $pointsBpoint = ($price->price) * $Bpoint->earn_rate * $multiply;
                                
                                if ($discPrice > 0) {
                                    $pointsJpoint = ($discPrice) * $Jpoint->earn_rate * $multiply;
                                    // $pointsBpoint = ($discPrice) * $Bpoint->earn_rate * $multiplyBP;
                                    $pointsBpoint = 0;
                                }else{
                                    $pointsJpoint = ($price->price) * $Jpoint->earn_rate * $multiply;
                                    // $pointsBpoint = ($price->price) * $Bpoint->earn_rate * $multiplyBP;
                                    $pointsBpoint = 0;
                                }

                                $pricesArray[] = [
                                    'id'            => $price->id,
                                    'label'         => strip_tags($priceLabel),
                                    'price'         => ApiProduct::hidePricing($username, number_format(($price->price * $gst), 2, '.', '')),
                                    'promo_price'   => ApiProduct::hidePricing($username, $promotionPrice),
                                    'qty'           => $qty,
                                    'stock'         => $price->stock,
                                    'stock_unit'    => $price->stock_unit,
                                    'default'       => ($price->default == 1) ? 'TRUE' : 'FALSE',
                                    'p_weight'      => $price->p_weight,
                                    'discount_percent' => $percent,
                                    'jpoint'        => floor($pointsJpoint),
                                    'bpoint'        => floor($pointsBpoint),
                                ];
                            }
                        }
                    }
                    else
                    {
                        $data['record']--;
                        $data['total_record']--;

                        if ($data['total_record'] == 0)
                        {
                            $data['status_msg'] = '#201';
                        }
                    }
                }

                // if ($product->freshness_days !='') {
                //   $freshness_tag = 'Guaranteed fresh for '. $product->freshness_days. ' days, inc. delivery day';
                // }else{
                //     $freshness_tag = '';
                // }
                if ($product->freshness_days !='' && $product->freshness_days != 0) {
                   $freshness_tag = $product->freshness_days. ' days freshness.';
                }else{
                    $freshness_tag = '';
                }
                
                // $total_sold = DB::table('jocom_transaction_details AS JTD')
                //         ->leftJoin('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
                //         ->where('JTD.sku','=',$product->sku)
                //         ->where('JT.status','=','completed')
                //         ->count();
                
                $total_sold = DB::table('jocom_transaction_details AS JTD')
                        ->leftJoin('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
                        ->where('JTD.sku','=',$product->sku)
                        ->where('JT.status','=','completed')
                        ->sum('JTD.unit');
                        
                if ($total_sold != "0") {
                    // $total = $total_sold;
                    $total = round($total_sold,2);
                }else{
                    $total = '';
                }
                
                $points  = Comment::scopeCommentsRating($product->id);
                
                $delivery_time = empty($product->delivery_time) ? '24 hours' : $product->delivery_time;
                
                switch ($delivery_time) {
                    case '24 hours':
                        $delivery_from = 1;
                        $delivery_to = 1;

                        break;
                    
                    case '1-2 business days2-3 business days':
                        $delivery_from = 1;
                        $delivery_to = 2;

                        break;
                    
                    case '2-3 business days':
                        $delivery_from = 2;
                        $delivery_to = 3;

                        break;
                    
                    case '3-7 business days':
                        $delivery_from = 3;
                        $delivery_to = 7;

                        break;
                    case '14 business days':
                        $delivery_from = 1;
                        $delivery_to = 14;

                        break;

                    default:
                        break;
                }

                if (reset($pricesArray) && ! (isset($proOptIds) && empty($proOptIds)))
                {
                    if(version_compare($vApp,'3.2.4','>=')){
                        
                        $data['item'][] = [
                        'sku'               => $product->sku,
                        'name'              => $productName,
                        'description'       => $productDescription,
                        'delivery_time'     => empty($product->delivery_time) ? '24 hours' : $product->delivery_time,
                        'delivery_day_from' => $delivery_from ,
                        'delivery_day_to'   => $delivery_to,
                        'min_qty'           => empty($product->min_qty) ? '' : $product->min_qty,
                        'max_qty'           => empty($product->max_qty) ? '' : $product->max_qty,
                        'total_sold'        => $total,
                        'img_1'             => $images[1],
                        'img_2'             => $images[2],
                        'img_3'             => $images[3],
                        'thumb_1'           => $thumbnails[1],
                        'thumb_2'           => $thumbnails[2],
                        'thumb_3'           => $thumbnails[3],
                        'vid_1'             => $video,
                        'qr_code'           => empty($product->qrcode) ? '' : $product->qrcode,
                        'zone_records'      => count($zones),
                        'delivery_zones'    => [$zones],
                        'price_records'     => count($pricesArray),
                        'price_option'      => ['option' => $pricesArray],
                        'related_products'  => $product->related_product,
                        'freshness'         => $product->freshness,
                        'freshness_days'    => $freshness_tag,
                        'bulk'              => empty($product->bulk) ? '0' : $product->bulk,
                        'halal'             => empty($product->halal) ? '0' : $product->halal,
                        'overall_rating'    => empty($points) ? '' : $points,
                    ];
                        
                    }else{
                        
                        $data['item'][] = [
                        'sku'               => $product->sku,
                        'name'              => $productName,
                        'description'       => $productDescription,
                        'delivery_time'     => empty($product->delivery_time) ? '24 hours' : $product->delivery_time,
                       // 'delivery_day_from' => $delivery_from ,
                       // 'delivery_day_to'   => $delivery_to,
                        'min_qty'           => empty($product->min_qty) ? '' : $product->min_qty,
                        'max_qty'           => empty($product->max_qty) ? '' : $product->max_qty,
                        'total_sold'        => $total,
                        'img_1'             => $images[1],
                        'img_2'             => $images[2],
                        'img_3'             => $images[3],
                        'thumb_1'           => $thumbnails[1],
                        'thumb_2'           => $thumbnails[2],
                        'thumb_3'           => $thumbnails[3],
                        'vid_1'             => $video,
                        'qr_code'           => empty($product->qrcode) ? '' : $product->qrcode,
                        'zone_records'      => count($zones),
                        'delivery_zones'    => [$zones],
                        'price_records'     => count($pricesArray),
                        'price_option'      => ['option' => $pricesArray],
                        'related_products'  => $product->related_product,
                        'freshness'         => $product->freshness,
                        'freshness_days'    => $freshness_tag,
                        'bulk'              => empty($product->bulk) ? '0' : $product->bulk,
                        'halal'             => empty($product->halal) ? '0' : $product->halal,
                        'overall_rating'    => empty($points) ? '' : $points,
                    ];
                        
                    }
                    
                }
            }
        }

        return ['xml_data' => $data];
        
       
    }

    public static function check_minimum($post=array())
    {
        $data = array();


        // $data['status_msg'] = '#302';
        $data['item'] = array();
        $group_total = array();

        // for each product with qrcode
        foreach($post['qrcode'] as $k => $v)
        {

            if(substr($post['priceopt'][$k], 0, 1) == 'S')
            {
                $price_id = substr($post['priceopt'][$k], 1);

                $price_row = DB::table('jocom_sp_product_price AS a')
                                ->select('a.sp_group_id', 'a.price', 'a.price_promo')
                                ->where('a.id', '=', $price_id)
                                ->first();


                if (count($price_row) > 0)
                {
                    $group_total[$price_row->sp_group_id] += (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $post['qty'][$k];

                    $data['item'][] = array(
                        'qrcode' => $post['qrcode'][$k],
                        'priceopt' => $post['priceopt'][$k],
                        'sp_group_id' => $price_row->sp_group_id,
                    );
                }
            }

        } // end of each product with qrcode

        if (count($group_total) > 0)
        {
            $newdata['total_group'] = count($group_total);

            foreach ($group_total as $key => $value)
            {
                $groupmin = DB::table('jocom_sp_group')
                ->select('min_purchase')
                ->where('id', '=', $key)
                ->first();

                if($group_total[$key] < $groupmin->min_purchase)
                {
                    foreach ($data['item'] as $item)
                    {
                        if ($key == $item['sp_group_id'])
                        {
                            $newdata['item'][] = array(
                                'qrcode' => $item['qrcode'],
                                'priceopt' => $item['priceopt'],
                                'sp_group_id' => $key,
                                'minimum' => $groupmin->min_purchase,

                            );
                        }
                    }
                }
            }
        }
        else
            $newdata['total_group'] = 0;


        // var_dump($newdata);
        // exit();

        // tie a product to KKW only
        // $fix_prod = 'JC2995';
        // $prod_name = 'Pancake';
        // if($post['qrcode'][$k] == $fix_prod AND $post['username'] != 'kkwoodypavilion')
        // {
        //     $data['item'][] = array(
        //         'qrcode' => $post['qrcode'][$k],
        //         'priceopt' => $post['priceopt'][$k],
        //         'status_msg' => '#1301',
        //     );
        //     break;
        // }
        // end tie to...

        return array('xml_data' => $newdata);


    }

    public static function hidePricing($username, $price)
    {
        $block = Config::get('constants.HIDE_PRICE');

        if (in_array($username, $block))
        {
            $showPrice = 0;
        }
        else
        {
            $showPrice = $price;
        }

        return $showPrice;
    }
    
    public static function fetch_product_lite($limit = 50, $offset = 0, $params = []) { // YH: Average UAT 1.3s/Production 1.2s, Single Product Query is slower that laravel Single Product Query
        $v3App = (Input::get('vapp') >= 3.0 || Input::get('app_code') == 'B2D' ? true : false);
        unset($totalProducts, $remove_limit);

        if(Input::get('latitude') != '' && Input::get('longitude') != ''){ // Google geolocation
            $data = json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?latlng=' . trim(Input::get('latitude')) . ',' . trim(Input::get('longitude')) . '&sensor=false'));
            
            if(isset($data->results[0])) {
                $response = array();
                foreach($data->results[0]->address_components as $addressComponet) {
                    if(in_array('administrative_area_level_1', $addressComponet->types)) $response[] = $addressComponet->long_name;
                    if(in_array('country', $addressComponet->types)) $response[] = $addressComponet->long_name;
                }
            }

            $country = DB::table('jocom_countries')->where('name', '=', $response[1])->select('id')->first();
            $region_name = (in_array($response[0], ["Wilayah Persekutuan Kuala Lumpur", "Wilayah Persekutuan Putrajaya", "Wilayah Persekutuan Labuan"]) ? "WP-" . explode(' ', $response[0], 3)[2] : $response[0]);

            $regions = DB::table('jocom_country_states')->where('country_id','=', $country->id)->where('name', 'LIKE', $region_name)->first();
            $regions_id = $regions->region_id;
        }else{ // check region ID
            if(Input::get('id') != ''){
                if(Input::get('id') == RegionController::KLANGVALLEYSTATEID){
                    $regions_id = RegionController::KLANGVALLEYREGIONID;
                }else{
                    $regions = State::find(Input::get('id'));
                    $regions_id = $regions->region_id;
                }
            }
        }
        if(isset($regions_id) && !is_numeric($regions_id)) unset($regions_id);

        $category   = array_get($params, 'product_cat', NULL);
        $codes      = array_get($params, 'code', NULL);
        $code2      = array_get($params, 'code2', NULL);
        $name       = array_get($params, 'name', NULL);
        $permission = array_get($params, 'custom', 0);
        $username   = array_get($params, 'username', NULL);
        $platform   = (isset($params['platform']) && in_array($params['platform'], ['jocom', 'wavpay']) ? $params['platform'] : 'jocom');
        $requestBanner = array_get($params, 'banner', 0);
        $city       = array_get($params, 'city', NULL);
        $charity_id = 0;
        $device     = array_get($params, 'device', 'phone');
        $image_path = Config::get('constants.BANNER_' . ($params['device'] === 'tablet' ? 'TAB_' : '') . 'FILE_PATH');
        $thumb_path = Config::get('constants.BANNER_' . ($params['device'] === 'tablet' ? 'TAB_' : '') . 'THUMB_FILE_PATH');

        if (!(is_numeric($limit) && $limit > 0 && is_numeric($offset))) {
            $limit  = 50;
            $offset = 0;
        }

        $data = [
            'banner'        => '',
            'record'        => 0,
            'total_record'  => 0,
            'item'          => [],            
        ];

        // otherwise treat ENG as default
        $language = (isset($params['lang']) && $params['lang'] === 'CN' ? '_cn' : (isset($params['lang']) && $params['lang'] === 'MY' ? '_my' : ''));
        $lang = (isset($params['lang']) && $params['lang'] === 'CN' ? 'cn' : (isset($params['lang']) && $params['lang'] === 'MY' ? 'my' : 'en'));

        $select_sql = "SELECT product_package.id AS id, product_package.sku AS sku, product_package.name AS name, product_package.name_cn AS name_cn, product_package.name_my AS name_my, product_package.description AS description, product_package.description_cn AS description_cn, product_package.description_my AS description_my, product_package.img_1 AS img_1, product_package.img_2 AS img_2, product_package.img_3 AS img_3, product_package.vid_1 AS vid_1, product_package.qrcode AS qrcode, product_package.delivery_time AS delivery_time, product_package.related_product AS related_product, product_package.bulk AS bulk, product_package.halal AS halal, product_package.min_qty AS min_qty, product_package.max_qty AS max_qty, product_package.freshness AS freshness, product_package.freshness_days AS freshness_days ";
        if (is_numeric($category)) {
            $base_rawsql = " FROM jocom_product_and_package AS product_package LEFT JOIN jocom_categories AS cat ON product_package.id = cat.product_id ";
            $region_data = implode(',', (isset($regions_id) ? ($regions_id !== 5 ? [$regions_id, 0] : [$regions_id]) : [0]));
            $v3_rawsql = " WHERE product_package.region_id IN ($region_data) AND ((sku LIKE 'TM-%' AND cat.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1 ";
            if(empty($name) && is_numeric($city)){
                $base_rawsql .= " LEFT JOIN jocom_product_delivery ON cat.product_id = jocom_product_delivery.product_id LEFT JOIN jocom_zone_cities ON jocom_product_delivery.zone_id = jocom_zone_cities.zone_id ";
                if($v3App) $base_rawsql .= $v3_rawsql . " ORDER BY product_package.region_id DESC, ";
            }else{
                if($v3App) $base_rawsql .= $v3_rawsql . " AND (new_arrival_expire is null OR new_arrival_expire > '" . date_format(Carbon\Carbon::now(), 'Y-m-d H:i:s') . "') ORDER BY product_package.region_id DESC, ";
            }
            if(!$v3App) $base_rawsql .= " WHERE ((sku LIKE 'TM-%' AND cat.category_id = {$category}) OR (sku LIKE 'TMP-%' AND category = '{$category}')) AND status = 1 ORDER BY ";
            $base_rawsql .= " product_package.weight DESC, product_package.sku DESC ";

            // request category banner
            if ($requestBanner == 1) $data['banner'] = self::fetch_product_banner_callback($category, $lang, $charity_id);
        } elseif (!empty($codes) || !empty($code2)) {
            $is_codes = (!empty($codes) ? true : false);
            $c = ($is_codes ? $codes : $code2);
            ksort($c);
            $c = "'" . implode("', '", $c) . "'";

            $base_rawsql = " FROM jocom_product_and_package AS product_package WHERE product_package.qrcode IN ({$c}) " . ($is_codes ? "AND product_package.status = 1 " : "") . "ORDER BY FIELD(product_package.qrcode, {$c})";
        } elseif (!empty($name) && strlen($name) >= 2) {
            $valid = false;
            $query = DB::table('jocom_products AS jp')
                ->where('jp.status', '=', 1)
                ->whereIn('jp.region_id', [1, 0])
                ->where('jp.name', 'LIKE', '%' . addslashes($name) . '%')
                ->orderBy('jp.region_id', 'DESC');
            $query->whereRaw('LOCATE(1347, jp.category)' . ($platform === 'jocom' ? ' = 0' : '')); // if is jocom platform exclude wavpay category product
            $totalProducts = $query->count(); // set total product at here

            if($totalProducts > 0){ // Product name got Record
                $c = $query->skip($offset)->take($limit)->lists('qrcode');
                $c = "'" . implode("', '", $c) . "'";
                $base_rawsql = " FROM jocom_product_and_package AS product_package WHERE product_package.qrcode IN ({$c}) AND product_package.status = 1 ORDER BY FIELD(product_package.qrcode, {$c})";
                $remove_limit = true; $valid = true; // remove limit = ignore SQL LIMIT script generate on the raw query
            }

            // these query lead to question why i try mommy keywords will slow as F
            if(!$valid){ // Consider user input category name
                unset($totalProducts);
                $query = DB::table('jocom_products_category')->where('category_name', 'LIKE', '%' . addslashes($name) . '%')->where('status', '=', 1);
                if($platform === 'jocom')
                    $query = $query->where('category_parent', '!=', 1347)->where('id', '=', 1347);
                else
                    $query = $query->where(function($q) {
                        $q->where('category_parent', '=', 1347)->orWhere('id', '=', 1347);
                    });
                $cat_ids = $query->lists('id');
                if(count($cat_ids) > 0){
                    $cat_ids = implode(", ", $cat_ids);
                    $base_rawsql = " FROM (SELECT product_id FROM jocom_categories WHERE category_id IN ($cat_ids) GROUP BY product_id) AS cat LEFT JOIN jocom_product_and_package AS product_package ON product_package.id = cat.product_id WHERE product_package.status = 1 ";
                    $valid = true; $cat_data_type = true;
                } // if no category found start using the tag name
            }
        }

        if(isset($products) || isset($base_rawsql)){
            $products = (isset($base_rawsql) ? DB::select(DB::raw($select_sql . $base_rawsql . (!isset($remove_limit) ? ($limit ? " LIMIT $limit " : "") . ($offset ? " OFFSET $offset " : "") : ''))) : $products->skip($offset)->take($limit)->get());
            if(!isset($totalProducts)){
                if(!isset($count_code)){
                    $totalProducts = (isset($base_rawsql) ? DB::select(DB::raw("SELECT count(product_package.id) AS num_record " . $base_rawsql)) : $products->count());
                    if(!is_int($totalProducts)) $totalProducts = (int)$totalProducts[0]->num_record;
                }else{
                    $totalProducts = count($products[0]);
                }
            }
        }

        // return fail if total product = 0
        if (!isset($totalProducts) || $totalProducts <= 0) return ['xml_data' => array_merge($data, ['status_msg' => '#201'])];


        // YH: WARNING Do not put below code into product item loop, it was massive slown down to API SYSTEM
        $Jpoint = PointType::where('type', '=', 'Jpoint')->where('status', 1)->first();
        $gst_status = Fees::get_gst_status(); // put at here
        $data['record']         = (int)(!isset($count_code) ? count($products) : $totalProducts);
        $data['total_record']   = $totalProducts;

        if($data['record'] == 1){
            // Total Sold
            $total_sold = DB::table('jocom_transaction_details AS JTD')->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JTD.transaction_id')->where('JTD.sku','=', $products[0]->sku)->where('JT.status','=','completed')->count();
            $total_sold = ($total_sold_raw > 0 ? [$products[0]->sku => $total_sold] : []);
            
            $nonP_delivery = [];
            $pack_delivery = [];
            if(substr($products[0]->id, 0, 1) !== 'P'){ // None Package Single Product 
                $nonP_delivery = Delivery::select('jocom_product_delivery.product_id', 'jocom_zones.id as zone', 'jocom_zones.name as zone_name')->join('jocom_zones', 'jocom_product_delivery.zone_id', '=', 'jocom_zones.id')->where('jocom_product_delivery.product_id', '=', $products[0]->id)->get()->toArray();
            }else{ // Package Single Product 
                $pack_delivery = DB::table('jocom_package_delivery')->where('package_id', '=', $products[0]->id)->groupBy('package_id')->lists('zone', 'package_id');
            }

            // Product GST data
            $gst_raw = DB::table('jocom_products')->where('id', '=', explode('-', $products[0]->sku)[1])->select('sku', 'gst', 'gst_value')->first();
            $gst_list = [$gst_raw->sku => [ 'gst' => $gst_raw->gst, 'gst_value' => $gst_raw->gst_value ]];
        }else{
            // Initial Data
            $product_array = json_decode(json_encode($products), true); // Convert Data into Array Format
            $sku_list_raw = array_column($product_array, 'sku'); // Grasp SKU column from array product into single dimentional array
            $sku_list = "('" . implode("', '", $sku_list_raw) . "')";
            $id_list = array_column($product_array, 'id'); // Grasp SKU column from array product into single dimentional array
            $p_idlist = explode(',', preg_replace('/(,?TM-[0]+)/i', ',', implode(',', $sku_list_raw)));

            // Total Sold - Swap Query method when category data is search Category cuz it may affect peformance dunno why.
            $q = (isset($cat_data_type) && $cat_data_type ? "SELECT JTD.sku AS sku, SUM(JTD.unit) AS total FROM (SELECT sku, unit, transaction_id FROM jocom_transaction_details WHERE sku IN $sku_list) AS JTD LEFT JOIN (SELECT id FROM jocom_transaction WHERE status = 'completed') AS JT ON JT.id = JTD.transaction_id GROUP BY JTD.sku" : "SELECT JTD.sku AS sku, SUM(JTD.unit) AS total FROM jocom_transaction_details AS JTD LEFT JOIN jocom_transaction AS JT ON JT.id = JTD.transaction_id WHERE JTD.sku IN $sku_list AND JT.status = 'completed' GROUP BY JTD.sku");
            $total_sold_raw = DB::select(DB::raw($q));
            $total_sold_raw = json_decode(json_encode($total_sold_raw), true);
            $total_sold = array_combine(array_column($total_sold_raw, 'sku'), array_column($total_sold_raw, 'total'));

            // None Package Product
            $nonP_idlist = array_filter($id_list, function($var){ return substr($var, 0, 1) !== 'P'; });
            $nonP_delivery = (count($nonP_idlist) > 0 ? Delivery::select('jocom_product_delivery.product_id', 'jocom_zones.id as zone', 'jocom_zones.name as zone_name')->join('jocom_zones', 'jocom_product_delivery.zone_id', '=', 'jocom_zones.id')->whereIn('jocom_product_delivery.product_id', $id_list)->get()->toArray() : []);
            
            // Package Product
            $pack_idlist = array_diff($id_list, $nonP_idlist);
            $pack_delivery = (count($pack_idlist) > 0 ? DB::table('jocom_package_delivery')->whereIn('package_id', $pack_idlist)->groupBy('package_id')->lists('zone', 'package_id') : []);

            // Product GST data
            $gst_raw = DB::table('jocom_products')->whereIn('id', $p_idlist)->select('sku', 'gst', 'gst_value')->get();
            $gst_raw = json_decode(json_encode($gst_raw), true);
            $gst_list = array_combine(array_column($gst_raw, 'sku'), $gst_raw);
        }
        if(count($pack_delivery) > 0) $zoneNames = Zone::lists('name');

        if(isset($username) && !empty($username)){ // it use when using serach product function
            $spGroupId = DB::table('jocom_user AS user')
                ->select('group.sp_group_id')
                ->leftJoin('jocom_sp_customer AS customer', 'user.id', '=', 'customer.user_id')
                ->leftJoin('jocom_sp_customer_group AS group', 'customer.id', '=', 'group.sp_cust_id')
                ->where('user.username', '=', $username)
                ->lists('sp_group_id');
            if(count($spGroupId) > 0){ // speical price qty, if special price is set
                $special_qty = SpecialPrice::get_default_qty();
                $special_qty = $special_qty->default_qty;
            }
        }
        // ---

        foreach ($products as $product) {
            $pricesArray        = [];

            if (substr($product->id, 0, 1) == 'P') { // if ID prefixed start with 'P' consider as package product
                $packageId      = substr($product->id, 1);
                $deliveryZones  = array_unique(explode(',', $pack_delivery[$packageId]));
                $zones          = [];
                // $zoneNames array key starts from 0 and $deliveryZone starts from 1, so minus 1 to get the correct zone name
                foreach ($deliveryZones as $deliveryZone) $zones[] = [ 'zone' => $deliveryZone, 'zone_name' => $zoneNames[$deliveryZone - 1] ];
                
                $proOptIds = DB::table('jocom_product_package_product')->where('package_id', '=', $packageId)->lists('qty', 'product_opt');
                $proQtyCal = array_fill_keys(array_keys($proOptIds), 0);

                $actualPrice    = 0;
                $promotionPrice = 0;

                $price_tempAssignData = [
                    'id'            => 'Null',
                    'label'         => 'Null',
                    'price'         => 0,
                    'promo_price'   => 0,
                    'qty'           => 0,
                    'stock'         => 0,
                    'stock_unit'    => 0,
                    'default'       => 'TRUE',
                    'p_weight'      => 0,
                ];
                if (!empty($proOptIds)) {
                    $prices = Price::getMultiActivePrices($proOptIds);
                    $maxQty = false;

                    foreach ($prices as $price) {
                        $gst            = Product::getGstValue($price->id);
                        $actualPrice   += $price->price * $proOptIds[$price->id] * $gst;
                        $promotionPrice += ($price->price_promo > 0 ? $price->price_promo : $price->price) * $proOptIds[$price->id] * $gst;
                        $proQtyCal[$price->id] = $price->qty;
                        if (($maxQty === false) || ($maxQty > $price->qty)) $maxQty = $price->qty;
                    }

                    if ($actualPrice > 0){
                        $promotionPrice = ($actualPrice == $promotionPrice) ? 0 : number_format($promotionPrice, 2, '.', '');
                        $actualPrice    = number_format($actualPrice, 2, '.', '');
                    }
                    $price_tempAssignData['price']          = self::hidePricing($username, $actualPrice);
                    $price_tempAssignData['promo_price']    = self::hidePricing($username, $promotionPrice);
                    $price_tempAssignData['qty']            = $maxQty;
                }
                $pricesArray[] = $price_tempAssignData;
            } else { // None Package Product
                unset($prices, $special);
                $productId      = (int)preg_replace('/^TM-[0]+/i', '', $product->sku);
                $zones          = array_values(array_filter($nonP_delivery, function($v) use ($productId){ return (int)$v['product_id'] === $productId; }));

                if (count($spGroupId) > 0) {
                    $special = true;
                    $prices = DB::table('jocom_sp_product_price AS sp')
                        ->select('np.label', 'np.label_cn', 'np.label_my', 'np.stock', 'np.stock_unit', 'np.p_weight', 'sp.id', 'sp.price', 'sp.price_promo', 'sp.qty', 'sp.disc_amount', 'sp.disc_type', 'sp.default')
                        ->leftJoin('jocom_product_price AS np', 'sp.label_id', '=', 'np.id')
                        ->where('sp.status', '=', 1)
                        ->where('sp.product_id', '=', $productId)
                        ->whereIn('sp.sp_group_id', $spGroupId)
                        ->orderBy('sp.default', 'DESC')
                        ->orderBy('sp.price', 'ASC')
                        ->get();
                }
                
                if(!isset($special) || ($special && count($prices) == 0)){
                    $special = false;
                    $prices = DB::select(DB::raw("
                        SELECT p.id, p.label, p.label_cn, p.label_my, p.price_promo, p.price, p.qty, p.stock, p.stock_unit, p.default, p.p_weight
                        FROM jocom_product_price AS p
                        WHERE p.status = 1 AND p.product_id = $productId
                        ORDER BY p.default DESC, p.price DESC
                    "));
                }

                if (count($prices) > 0) {
                    foreach ($prices as $price) {
                        $priceLabel = ($price->{'label'. $language}) ? $price->{'label' . $language} : $price->label;

                        $gst = 1;
                        if ($gst_status == '1' && isset($gst_list[$product->sku]['gst']) && $gst_list[$product->sku]['gst'] == 2) $gst += $gst_list[$product->sku]['gst_value'] / 100;
                        $promotionPrice = ($price->price_promo != 0) ? number_format(($price->price_promo * $gst), 2, '.', '') : 0;
                        $promotionPrice_display = self::hidePricing($username, $promotionPrice);
                        unset($percent, $pointsJpoint);

                        if ($special) {
                            $sp_price = $price->price;
                            if ($price->disc_amount > 0 && $price->disc_type != "") {
                                if($price->disc_type === '%'){
                                    $sp_price   = number_format($price->price - ($price->price * ($price->disc_amount / 100)), 2);
                                }else if($price->disc_type === 'N'){
                                    $sp_price   = number_format($price->price - $price->disc_amount, 2);
                                }
                            }

                            $orginalPrice = self::hidePricing($username, number_format(($sp_price * $gst), 2, '.', ''));
                        } else {
                            $qty = $price->qty;

                            if ($charity_id != 0) {
                                $tempqty = CharityProduct::getCharityQuantity($price->id, $charity_id);
                                if (count($tempqty) > 0) $qty = $tempqty->qty;
                            }
                            
                            $orginalPrice = self::hidePricing($username, number_format(($price->price * $gst), 2, '.', ''));
                            $discPrice = $promotionPrice_display;  

                            $percent = ($discPrice != "0" ? number_format((($orginalPrice - $discPrice) * 100) / $orginalPrice, 0) . '%' : '');
                            $price_forpoint = ($discPrice > 0 ? $discPrice : $price->price);
                            $pointsJpoint = $price_forpoint * $Jpoint->earn_rate * 1;
                        }
                        $pricesArray[] = [
                            'id'            => ($special ? 'S' : '') . $price->id,
                            'label'         => strip_tags($priceLabel),
                            'price'         => $orginalPrice,
                            'promo_price'   => $promotionPrice_display,
                            'qty'           => ($special ? $special_qty : $qty),
                            'stock'         => $price->stock,
                            'stock_unit'    => $price->stock_unit,
                            'default'       => ($price->default == 1 || $special) ? 'TRUE' : 'FALSE',
                            'p_weight'      => $price->p_weight,
                            'discount_percent' => (isset($percent) ? $percent : ''),
                            'jpoint'        => (isset($pointsJpoint) ? floor($pointsJpoint) : ''),
                            'bpoint'        => 0,
                        ];
                    }
                } else {
                    $data['record']--;
                    $data['total_record']--;

                    if ($data['total_record'] == 0) $data['status_msg'] = '#201';
                    continue;
                }
            }

            $delivery_time = (empty($product->delivery_time) ? '24 hours' : $product->delivery_time);
            if (isset($pricesArray) && !(isset($proOptIds) && empty($proOptIds))) {
                $productdata_input = [
                    'sku'               => $product->sku,
                    'name'              => (($product->{'name' . $language}) ? $product->{'name' . $language} : $product->name),
                    'description'       => (($product->{'description' . $language}) ? $product->{'description' . $language} : $product->description),
                    'delivery_time'     => $delivery_time,
                    'min_qty'           => empty($product->min_qty) ? '' : $product->min_qty,
                    'max_qty'           => empty($product->max_qty) ? '' : $product->max_qty,
                    'total_sold'        => (isset($total_sold[$product->sku]) ? round($total_sold[$product->sku], 2) : ''),
                    'img_1'             => (!empty($product->img_1) ? Image::link("images/data/{$product->img_1}") : ''),
                    'img_2'             => (!empty($product->img_2) ? Image::link("images/data/{$product->img_2}") : ''),
                    'img_3'             => (!empty($product->img_3) ? Image::link("images/data/{$product->img_3}") : ''),
                    'thumb_1'           => (!empty($product->img_1) ? Image::link("images/data/thumbs/{$product->img_1}") : ''),
                    'thumb_2'           => (!empty($product->img_2) ? Image::link("images/data/thumbs/{$product->img_2}") : ''),
                    'thumb_3'           => (!empty($product->img_3) ? Image::link("images/data/thumbs/{$product->img_3}") : ''),
                    'vid_1'             => ($product->vid_1 ? $product->vid_1 : ''),
                    'qr_code'           => empty($product->qrcode) ? '' : $product->qrcode,
                    'zone_records'      => count($zones),
                    'delivery_zones'    => $zones,
                    'price_records'     => count($pricesArray),
                    'price_option'      => ['option' => (count($pricesArray) == 1 ? $pricesArray[0] : $pricesArray)],
                    'related_products'  => $product->related_product,
                    'freshness'         => $product->freshness,
                    'freshness_days'    => ($product->freshness_days != '' && $product->freshness_days != 0 ? $product->freshness_days . ' days freshness.' : ''),
                    'bulk'              => empty($product->bulk) ? '0' : $product->bulk,
                    'halal'             => empty($product->halal) ? '0' : $product->halal,
                    'overall_rating'    => Comment::scopeCommentsRating($product->id),
                ];
                if(version_compare(Input::get('vapp'), '3.2.4', '>=')){
                    $productdata_input['delivery_day_from'] = (isset(self::$d_time_range[$delivery_time]['from']) ? self::$d_time_range[$delivery_time]['from'] : false);
                    $productdata_input['delivery_day_to'] = (isset(self::$d_time_range[$delivery_time]['to']) ? self::$d_time_range[$delivery_time]['to'] : false);
                }
                $data['item'][] = $productdata_input;
            }
        }

        return ['xml_data' => $data];
    }
    
    private static function fetch_product_banner_callback($category, $lang, &$charity_id){
        $bannerlist = [];

        $charity_cat = CharityCategory::select('id')->where('category_id', $category)->first();
        if(count($charity_cat) > 0) $charity_id = $charity_cat->id;

        $banners = Banner::select('jocom_banners.id', 'jocom_banners.pos', 'jocom_banners.url_link', 'jocom_banners.qrcode')
            ->where('jocom_banners.category_id', '=', $category)
            ->groupby('jocom_banners.id')
            ->orderBy('jocom_banners.pos', 'desc')
            ->get();

        if(count($banners) > 0) {
            foreach ($banners as $banner) {
                $image_name = "";
                $file_name  = "";
                $thumb_name = "";
                $bimage     = Banner::getImagesByLanguage($banner->id, $device, $lang);

                if(count($bimage) > 0) {
                    $image_name = $bimage->file_name;
                    $file_name  = $image_path . $lang . "/" . $bimage->file_name;
                    $thumb_name = $thumb_path . $lang . "/" . $bimage->thumb_name;
                } else {
                    $default_images = Banner::getImagesByLanguage($banner->id, "phone", "en");

                    if(count($default_images) > 0) {
                        $image_name     = $default_images->file_name;
                        $file_name      = Config::get('constants.BANNER_FILE_PATH') . "en/" . $default_images->file_name;
                        $thumb_name     = Config::get('constants.BANNER_THUMB_FILE_PATH') . "en/" . $default_images->thumb_name;
                    }
                }

                if ($file_name != "" && file_exists('./' . $file_name)) {
                    if(!file_exists('./' . $thumb_name)) create_thumbnail($image_name, $thumb_width, $thumb_height, './' . $image_path . "en", './' . $thumb_path . $lang . "/");

                    $bannerlist[] = [
                        'id'        => $banner->id,
                        'img'       => Image::link($file_name),
                        'thumbnail' => Image::link($thumb_name),
                        'url'       => $banner->url_link,
                        'qrcode'    => $banner->qrcode
                    ];
                }
            }
        }

        return $bannerlist;
    }
    
    public static function fetch_ProductDetails($params = []) { // UAT Server: 1.5 ~ 0.9s
        // Treat ENG as default, if lang not set
        $language   = (isset($params['lang']) && $params['lang'] === 'CN' ? '_cn' : (isset($params['lang']) && $params['lang'] === 'MY' ? '_my' : ''));
        $lang       = (isset($params['lang']) && $params['lang'] === 'CN' ? 'cn' : (isset($params['lang']) && $params['lang'] === 'MY' ? 'my' : 'en'));
        $code       = array_get($params, 'code', NULL);
        if (!is_string($code)) return ['xml_data' => ['status_msg' => '#201']];
        if (Cache::has('prode_' . $code . '_' . $lang)) return ['xml_data' => Cache::get('prode_' . $code . '_' . $lang)];

        $name       = array_get($params, 'name', NULL);
        $username   = array_get($params, 'username', NULL);
        $device     = array_get($params, 'device', 'phone');
        $image_path = Config::get('constants.BANNER_' . ($params['device'] === 'tablet' ? 'TAB_' : '') . 'FILE_PATH');
        $thumb_path = Config::get('constants.BANNER_' . ($params['device'] === 'tablet' ? 'TAB_' : '') . 'THUMB_FILE_PATH');

        $data = [
            'details'   => '',
            'comment'   => [],
            'related'   => [], // product related to these product
        ];

        // Grasp Product
        if(!is_string($code)) return ['xml_data' => ['status_msg' => '#201']];
        $product = DB::table('jocom_product_and_package')->where('status', '=', 1)->where("qrcode", '=', $code)->first();
        if (!$product) return ['xml_data' => ['status_msg' => '#201']];

        $Jpoint = PointType::where('type', '=', 'Jpoint')->where('status', 1)->first();
        $gst_status = Fees::get_gst_status(); // put at here

        // Get total
        $total_sold = DB::table('jocom_transaction_details AS JTD')
            ->join('jocom_transaction AS JT','JT.id','=','JTD.transaction_id')
            ->where('JTD.sku', '=', $product->sku)
            ->where('JT.status','=','completed')
            ->count();

        $productId = (int)preg_replace('/^TM-[0]+/i', '', $product->sku);

        $pricesArray = [];
        if(substr($product->id, 0, 1) !== 'P'){ // None Package Product
            if(isset($username) && !empty($username)){ // Get special user price
                $spGroupId = DB::table('jocom_user AS user')
                    ->join('jocom_sp_customer AS customer', 'user.id', '=', 'customer.user_id')
                    ->join('jocom_sp_customer_group AS group', 'customer.id', '=', 'group.sp_cust_id')
                    ->where('user.username', '=', $username)
                    ->select('group.sp_group_id')
                    ->lists('sp_group_id');
                if(count($spGroupId) > 0){ // speical price qty, if special price is set
                    $special = true;
                    $special_qty = SpecialPrice::get_default_qty();
                    $special_qty = $special_qty->default_qty;

                    $prices = DB::table('jocom_sp_product_price AS sp')
                        ->select('np.label', 'np.label_cn', 'np.label_my', 'np.stock', 'np.stock_unit', 'np.p_weight', 'sp.id', 'sp.price', 'sp.price_promo', 'sp.qty', 'sp.disc_amount', 'sp.disc_type', 'sp.default')
                        ->leftJoin('jocom_product_price AS np', 'sp.label_id', '=', 'np.id')
                        ->where('sp.status', '=', 1)
                        ->where('sp.product_id', '=', $productId)
                        ->whereIn('sp.sp_group_id', $spGroupId)
                        ->orderBy('sp.default', 'DESC')
                        ->orderBy('sp.price', 'ASC')
                        ->get();
                }
            }
            
            if(!isset($special) || ($special && count($prices) == 0)){
                $special = false;
                $prices = DB::select("
                    SELECT p.id, p.label, p.label_cn, p.label_my, p.price_promo, p.price, p.qty, p.stock, p.stock_unit, p.default, p.p_weight
                    FROM jocom_product_price AS p
                    WHERE p.status = 1 AND p.product_id = $productId
                    ORDER BY p.default DESC, p.price DESC
                ");
            }

            if (!count($prices)) return ['xml_data' => ['status_msg' => '#201']];

            // Grasp Zone
            $zones = Delivery::join('jocom_zones', 'jocom_product_delivery.zone_id', '=', 'jocom_zones.id')->where('jocom_product_delivery.product_id', '=', $productId)->select('jocom_product_delivery.product_id', 'jocom_zones.id as zone', 'jocom_zones.name as zone_name')->get()->toArray();

            // Product GST data
            $gst_raw = Product::where('id', '=', $productId)->select('sku', 'gst', 'gst_value')->first();
            $gst_list = [$gst_raw->sku => [ 'gst' => $gst_raw->gst, 'gst_value' => $gst_raw->gst_value ]];

            foreach ($prices as $price) {
                $priceLabel = ($price->{'label'. $language}) ? $price->{'label' . $language} : $price->label;

                $gst = 1;
                if ($gst_status == '1' && isset($gst_list[$product->sku]['gst']) && $gst_list[$product->sku]['gst'] == 2) $gst += $gst_list[$product->sku]['gst_value'] / 100;
                $promotionPrice = ($price->price_promo != 0) ? number_format(($price->price_promo * $gst), 2, '.', '') : 0;
                $promotionPrice_display = self::hidePricing($username, $promotionPrice);

                if ($special) {
                    $sp_price = $price->price;
                    if ($price->disc_amount > 0 && !empty($price->disc_type) && in_array($price->disc_type, ['%', 'N'])) $sp_price = number_format($price->price - ($price->disc_type === '%' ? ($price->price * ($price->disc_amount / 100)) : $price->disc_amount), 2);

                    $orginalPrice = self::hidePricing($username, number_format(($sp_price * $gst), 2, '.', ''));
                } else {
                    $qty = $price->qty;
                    
                    $orginalPrice = self::hidePricing($username, number_format(($price->price * $gst), 2, '.', ''));
                    $discPrice = $promotionPrice_display;  

                    $percent = ($discPrice != "0" ? number_format((($orginalPrice - $discPrice) * 100) / $orginalPrice, 0) . '%' : '');
                    $price_forpoint = ($discPrice > 0 ? $discPrice : $price->price);
                    $pointsJpoint = $price_forpoint * $Jpoint->earn_rate * 1;
                }
                $pricesArray[] = [
                    'id'            => ($special ? 'S' : '') . $price->id,
                    'label'         => strip_tags($priceLabel),
                    'price'         => $orginalPrice,
                    'promo_price'   => $promotionPrice_display,
                    'qty'           => ($special ? $special_qty : $qty),
                    'stock'         => $price->stock,
                    'stock_unit'    => $price->stock_unit,
                    'default'       => ($price->default == 1 || $special) ? 'TRUE' : 'FALSE',
                    'p_weight'      => $price->p_weight,
                    'discount_percent' => (isset($percent) ? $percent : ''),
                    'jpoint'        => (isset($pointsJpoint) ? floor($pointsJpoint) : ''),
                    'bpoint'        => 0,
                ];
            }
        }else{ // Package Product
            $pack_delivery = DB::table('jocom_package_delivery')->where('package_id', '=', $product->id)->groupBy('package_id')->lists('zone', 'package_id');
            $zoneNames = Zone::lists('name');

            $packageId      = substr($product->id, 1);
            $deliveryZones  = array_unique(explode(',', $pack_delivery[$packageId]));
            $zones          = [];
            // $zoneNames array key starts from 0 and $deliveryZone starts from 1, so minus 1 to get the correct zone name
            foreach ($deliveryZones as $deliveryZone) $zones[] = [ 'zone' => $deliveryZone, 'zone_name' => $zoneNames[$deliveryZone - 1] ];

            $proOptIds = DB::table('jocom_product_package_product')->where('package_id', '=', $packageId)->lists('qty', 'product_opt');

            $actualPrice    = 0;
            $promotionPrice = 0;

            $price_tempAssignData = [
                'id'            => 'Null',
                'label'         => 'Null',
                'price'         => 0,
                'promo_price'   => 0,
                'qty'           => 0,
                'stock'         => 0,
                'stock_unit'    => 0,
                'default'       => 'TRUE',
                'p_weight'      => 0,
            ];
            if (!empty($proOptIds)) {
                $prices = Price::getMultiActivePrices($proOptIds);
                $maxQty = false;

                foreach ($prices as $price) {
                    $gst            = Product::getGstValue($price->id);
                    $actualPrice   += $price->price * $proOptIds[$price->id] * $gst;
                    $promotionPrice += ($price->price_promo > 0 ? $price->price_promo : $price->price) * $proOptIds[$price->id] * $gst;
                    if (($maxQty === false) || ($maxQty > $price->qty)) $maxQty = $price->qty;
                }

                if ($actualPrice > 0){
                    $promotionPrice = ($actualPrice == $promotionPrice) ? 0 : number_format($promotionPrice, 2, '.', '');
                    $actualPrice    = number_format($actualPrice, 2, '.', '');
                }
                $price_tempAssignData['price']          = self::hidePricing($username, $actualPrice);
                $price_tempAssignData['promo_price']    = self::hidePricing($username, $promotionPrice);
                $price_tempAssignData['qty']            = $maxQty;
            }
            $pricesArray[] = $price_tempAssignData;
        }

        $delivery_time = (empty($product->delivery_time) ? '24 hours' : $product->delivery_time);
        if (isset($pricesArray) && !(isset($proOptIds) && empty($proOptIds))) {
            $data['details'] = [
                'sku'               => $product->sku,
                'name'              => (($product->{'name' . $language}) ? $product->{'name' . $language} : $product->name),
                'description'       => (($product->{'description' . $language}) ? $product->{'description' . $language} : $product->description),
                'delivery_time'     => $delivery_time,
                'delivery_day_from' => (isset(self::$d_time_range[$delivery_time]['from']) ? self::$d_time_range[$delivery_time]['from'] : false),
                'delivery_day_to'   => (isset(self::$d_time_range[$delivery_time]['to']) ? self::$d_time_range[$delivery_time]['to'] : false),
                'min_qty'           => $product->min_qty ? $product->min_qty : '',
                'max_qty'           => $product->max_qty ? $product->max_qty : '',
                'total_sold'        => ((int)$total_sold ? $total_sold : ''),
                'img_1'             => (!empty($product->img_1) ? Image::link("images/data/{$product->img_1}") : ''),
                'img_2'             => (!empty($product->img_2) ? Image::link("images/data/{$product->img_2}") : ''),
                'img_3'             => (!empty($product->img_3) ? Image::link("images/data/{$product->img_3}") : ''),
                'thumb_1'           => (!empty($product->img_1) ? Image::link("images/data/thumbs/{$product->img_1}") : ''),
                'thumb_2'           => (!empty($product->img_2) ? Image::link("images/data/thumbs/{$product->img_2}") : ''),
                'thumb_3'           => (!empty($product->img_3) ? Image::link("images/data/thumbs/{$product->img_3}") : ''),
                'vid_1'             => ($product->vid_1 ? $product->vid_1 : ''),
                'qr_code'           => empty($product->qrcode) ? '' : $product->qrcode,
                'zone_records'      => count($zones),
                'delivery_zones'    => $zones,
                'price_records'     => count($pricesArray),
                'price_option'      => ['option' => (count($pricesArray) == 1 ? $pricesArray[0] : $pricesArray)],
                'related_products'  => $product->related_product,
                'freshness'         => $product->freshness,
                'freshness_days'    => ($product->freshness_days != '' && $product->freshness_days != 0 ? $product->freshness_days . ' days freshness.' : ''),
                'bulk'              => empty($product->bulk) ? '0' : $product->bulk,
                'halal'             => empty($product->halal) ? '0' : $product->halal,
                'overall_rating'    => Comment::scopeCommentsRating($product->id),
            ];
        }

        if($product){
            $query = DB::select("
                SELECT com.id, user.full_name AS full_name, user.username AS user_name, com.comment_date, com.comment, com.image, com.rating
                FROM jocom_comments AS com
                LEFT JOIN jocom_user AS user ON user.id = com.user_id
                WHERE com.product_id = $productId AND com.status = 1 AND com.lang = '$lang'
                ORDER BY comment_date DESC
            ");

            $data['comment'] = [
                'image_path' => url('/') . '/' . Config::get('constants.COMMENT_IMG_PATH'),
                'timestamp' => (count($query) ? $query[0]->comment_date : date('Y-m-d h:i:s')),
                'record' => count($query),
                'tot_record' => count($query),
                'item' => $query,
            ];
        }

        Cache::add('prode_' . $code . '_' . $lang, $data, Carbon\Carbon::now()->addMinutes(1));

        return ['xml_data' => $data];
    }
    

}