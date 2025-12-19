<?php

use Helper\ImageHelper as Image;

class ApiCheckProductController extends BaseController {
    public function __construct(){


    }

    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyMinimumpurchase()
    {

        $data   = array();
        $get    = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';

        $get = array(
                'username' => Input::get('username'),
                'qrcode' => Input::get('qrcode'),
                'priceopt' => Input::get('priceopt'),
                'qty' => Input::get('qty'),
            );

        if(Input::has('lang'))
            $get['lang'] = Input::get('lang');

        // $data = ApiProduct::check_minimum($get);
        $data = array_merge($data, ApiProduct::check_minimum($get));

        return Response::view('xml_v', $data)   
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');

        
    }

    // function to be removed for CMS 2.9.0
    public function anyZone()
    {
        $zone               = "";
        $weight             = "1000000";        
       
        $tmpdata['item'][] = array(
                    //'qrcode'    => '',
                    'zone'      => $zone,
                    'max_weight'=> $weight,
                );      


        $data['enc']     = 'UTF-8';
        $data['record']  = 0;
        //$arr_xml = array('xml_data' => $data);

        $data = array_merge($data, array('xml_data' => $tmpdata));

        return Response::view('xml_v', $data)
                    ->header('Content-Type', 'text/xml')
                    ->header('Pragma', 'public')
                    ->header('Cache-control', 'private')
                    ->header('Expires', '-1');

        // $inputs             = Input::all();
        // $delivery_city      = Input::get('delivercity');    // City ID
        // $delivery_state     = Input::get('deliverstate');   // State ID
        // $delivery_country   = Input::get('delivercountry'); // Country ID
        // $arr_invalid_item   = array();
        
        // if(Input::has('qrcode')) {
        //     $products           = Input::get('qrcode');
        //     $product_zone       = array();
        //     $package_zone       = array();
        //     $arr_product_zone   = array();
        //     $arr_product_price  = array();
        //     $arr_package_zone   = array();
        //     $arr_valid_zone     = array();
        //     $i = 0;
           
        //     foreach ($products as $product) {
        //         // echo "<br><br>Product: ".$product;
        //         $is_package     = false;
        //         $is_product     = false;

        //         switch($product) {
        //             case (preg_match('/jcp+[0-9]+$/i', $product) ? true : false) :
        //                 $is_package     = true;
        //                 $package_zone   = ApiCheckProduct::get_package_zone($product);
        //             break;

        //             default:
        //                 $is_product     = true;
        //                 $product_zone   = ApiCheckProduct::get_product_zone($product);
        //                 break;
        //         }

        //         $arr_zone       = array();
        //         $arr_pkg_zone   = array();

        //         if ($is_product == true) {
        //             foreach ($product_zone as $pzone) {
        //                 $arr_zone[] = $pzone->zone_id;
        //             }

        //             $arr_product_zone[$product] = $arr_zone;             
        //         }

        //         if ($is_package == true) {
        //             foreach ($package_zone as $pkgzone) {
        //                 $arr_pkg_zone[] = $pkgzone->zone_id;
        //             }

        //             $arr_package_zone[$product] = $arr_pkg_zone;
        //         }
                
        //     }

        //     // Product Zones
        //     foreach($arr_product_zone as $pk => $pv) {
        //         $valid_country  = false;
        //         $valid_state    = false;
        //         $valid_city     = false;

        //         foreach($pv as $key => $value) {
        //             // echo "<br>[A][zone_id: $value] -- country: $valid_country -- state: $valid_state -- city: $valid_city";

        //             $zone           = ApiCheckProduct::get_zone_country($value); 
        //             $zone_country   = $zone->country_id;

        //             if($delivery_country == $zone_country) {
        //                 $valid_country = true;
        //             }

        //             $zstates        = ApiCheckProduct::get_zone_state($value);
        //             foreach ($zstates as $zs) {
        //                 foreach ($zs as $k => $v) {
        //                     if($delivery_state == $v) {
        //                         $valid_state = true;
        //                     }
        //                 }
        //             }

        //             $zcities        = ApiCheckProduct::get_zone_city($value);
        //             foreach ($zcities as $zc) {
        //                 if($delivery_city == $zc->city_id) {
        //                     $valid_city             = true;
        //                     $zone_details           = ApiCheckProduct::get_max_weight($value);
        //                     $arr_details[$value]    = $zone_details->weight;
        //                     $arr_valid_zone[]       = $arr_details;
        //                 }
        //             }
        //         }
                
        //         if ($valid_city == false) {
        //             $arr_invalid_item[] = $pk;
        //         }

        //     }

        //     // Package Zones
        //     foreach($arr_package_zone as $pk => $pv) {
        //         foreach($pv as $key => $value) {
        //             $valid_country  = false;
        //             $valid_state    = false;
        //             $valid_city     = false;

        //             // echo "<br>[A][zone_id: $value] - country: $valid_country - state: $valid_state - city: $valid_city";

        //             $zone           = ApiCheckProduct::get_zone_country($value); 
        //             $zone_country   = $zone->country_id;

        //             if($delivery_country == $zone_country) {
        //                 $valid_country = true;
        //             }

        //             $zstates        = ApiCheckProduct::get_zone_state($value);
        //             foreach ($zstates as $zs) {
        //                 foreach ($zs as $k => $v) {
        //                     if($delivery_state == $v) {
        //                         $valid_state = true;
        //                     }
        //                 }
        //             }

        //             $zcities        = ApiCheckProduct::get_zone_city($value);
        //             foreach ($zcities as $zc) {
        //                 if($delivery_city == $zc->city_id) {
        //                     $valid_city             = true;
        //                     $zone_details           = ApiCheckProduct::get_max_weight($value);
        //                     $arr_details[$value]    = $zone_details->weight;
        //                     $arr_valid_zone[]       = $arr_details;
        //                 }                    }
        //         }
                
        //         if ($valid_city == false) {
        //             $arr_invalid_item[] = $pk;
        //         }

        //     }

        //     // var_dump($arr_invalid_item);

        // }

        // $tmpdata['item']       = array();
        // $qrcode             = "";
        // $zone               = "";
        // $weight             = "";
        
        // if(count($arr_valid_zone) > 0) {
        //     $zz = $arr_valid_zone[0];

        //     foreach($zz as $key => $value) {
        //         $zone   = $key;
        //         $weight = $value;
        //     }
        // }

        // if(count($arr_invalid_item) > 0) {
        //     $qrcode = "";
        //     foreach($arr_invalid_item as $item) {
        //         if($qrcode == "")
        //             $qrcode = $item;
        //         else 
        //             $qrcode .= ",".$item;
                
        //     }
        //     $tmpdata['item'][] = array(
        //                 'qrcode'    => $qrcode,
        //                 'zone'      => $zone,
        //                 'max_weight'=> $weight,
        //             );

        // }
        // else {
        //     $tmpdata['item'][] = array(
        //                 //'qrcode'    => '',
        //                 'zone'      => $zone,
        //                 'max_weight'=> $weight,
        //             );
        // }


        // $data['enc']     = 'UTF-8';
        // $data['record']  = count($arr_invalid_item);
        // //$arr_xml = array('xml_data' => $data);

        // $data = array_merge($data, array('xml_data' => $tmpdata));

        // return Response::view('xml_v', $data)
        //             ->header('Content-Type', 'text/xml')
        //             ->header('Pragma', 'public')
        //             ->header('Cache-control', 'private')
        //             ->header('Expires', '-1');
    }
    

    public function anyLastpurchaseitems() {
        
        
        $data        = array();
        $collection  = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'en';
        
        if(Input::has('lang')) {
            $get['lang'] = strtolower(trim(Input::get('lang'))) ;            
        }
        
        $username = Input::get('username');
        
        if($username){
           
            // Get last 10 product purchased by username 
            $PreviousPurchased = Transaction::getPreviousPurchasedItem($username,10);

            foreach ($PreviousPurchased as $key => $value) {
             
                // Separate data product collection to increase performance in return data
                $ProductInfo = Product::getBySKU($value->sku);
                
                switch ($get['lang']) {
                case "cn":
                    $ProductName = $ProductInfo->name_cn;
                    break;
                case "my":
                    $ProductName = $ProductInfo->name_my;
                    break;
                default:
                    $ProductName = $ProductInfo->name;
                    break;
            }
                $subCol = array(
                    "id" => $ProductInfo->id,
                    "sku" => $ProductInfo->sku,
                    "name" => $ProductInfo->name,
                    "thumbnail" => Image::link(Config::get('constants.PRODUCT_IMAGE_THUMB_FILE_PATH').$ProductInfo->img_1), // Image Link
                    "qrcode" => $ProductInfo->qrcode,
                );
                array_push($collection, $subCol);
            }
            
        }else{
            $collection = [];
        }
        
        $data['xml_data']['title'] = "Recent purchased items";
        $data['xml_data']['item'] = $collection;

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
        
    }
    
    public function anyLastmonthtoppurchase() {
        
        
        $data        = array();
        $collection  = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'en';
        
        if(Input::has('lang')) {
            $get['lang'] = strtolower(trim(Input::get('lang'))) ;    
        }
        
        $TopPurchased = Transaction::getLastMonthTopPurchase();
        
        foreach ($TopPurchased as $key => $value) {
            
            switch ($get['lang']) {
                case "cn":
                    $ProductName = $value->name_cn;
                    break;
                case "my":
                    $ProductName = $value->name_my;
                    break;
                default:
                    $ProductName = $value->name;
                    break;
            }
            
            $subCol = array(
                "id" => $value->ProductID,
                "totalPurchased" => $value->TotalPurchased,
                "sku" => $value->sku,
                "name" => $ProductName,
                "thumbnail" => Image::link(Config::get('constants.PRODUCT_IMAGE_THUMB_FILE_PATH').$value->img_1), // Image Link
                "qrcode" => $value->qrcode,
            );

            array_push($collection, $subCol);

        }
       
        $data['xml_data']['title'] = "Last Month Top Purchased Items";
        $data['xml_data']['item'] = $collection;

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
        
    }
    
    
    

}
?>