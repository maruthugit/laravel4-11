<?php

class ShopeeController extends BaseController
{
    const DATE_FORMAT_ymdhhmm = "1";
    
    public function anyIndex(){

        return View::make('shopee.index');
    }

    public function anyOrderslisting() {
        
        // Get Orders
        $orders = DB::table('jocom_shopee_order')->select(
                    array(
                        'jocom_shopee_order.id',
                        'jocom_shopee_order.ordersn',
                        'jocom_shopee_order.name',
                        'jocom_shopee_order.phone',
                        'jocom_shopee_order.transaction_id',
                        'jocom_shopee_order.status'
                        ))
                    ->leftJoin('jocom_shopee_order_details', 'jocom_shopee_order_details.order_id', '=', 'jocom_shopee_order.id')
                    ->where('jocom_shopee_order_details.activation',1)
                    ->groupby('jocom_shopee_order.ordersn');
 
        return Datatables::of($orders)->make(true);
        
    }

    public function anyMigrate(){
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
        $OrderCollection = array();
        $message = "";
    // echo 'In';
        try{
 
            $date = new DateTime();
            $earlier = $date->modify('-15 day');
            $from = $earlier->getTimestamp();
            $earlier1 = $date->modify('-5 day');
            $from1 = $earlier1->getTimestamp();
            
            // $earlier2 = $date->modify('-1 day');
            // $currentdate_1 = $earlier2->getTimestamp();
            // echo $from.$earlier.'ss';
            // die();
            
            $parameters = array(
                "pagination_offset" => 0,
                "pagination_entries_per_page" => 100,
            );

            $date2 = new DateTime();
            $currentdate = $date2->getTimestamp();
            $pagination_entries_per_page = 100;
            $offset = 0;
            // for ($l = 1; $l <= 10; $l++) { 
                //  $offset = $l;
            // for ($x = 1; $x <= 100; $x++) {  //StartNewMg
            // $pagination_entries_per_page = $x;
            // $offset = 50; 
            //  echo $x;
            $status = "READY_TO_SHIP";
            // $status = "COMPLETED";
            $listType = Input::get('list_type');
            // pagination_entries_per_page
            switch ($listType) {
                case 1:
                    $shopid     = Config::get('constants.ShopeeJocomShopid');
                    $partner_id = Config::get('constants.ShopeeJocomPartnerID');
                    $secret     = Config::get('constants.ShopeeJocomSecret');  
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);

                    break;
                case 2:
                    $shopid     = Config::get('constants.ShopeeCocaShopid');
                    $partner_id = Config::get('constants.ShopeeCocaPartnerID');
                    $secret     = Config::get('constants.ShopeeCocaSecret');   
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 3:
                    $shopid     = Config::get('constants.ShopeeYeoShopid');
                    $partner_id = Config::get('constants.ShopeeYeoPartnerID');
                    $secret     = Config::get('constants.ShopeeYeoSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 4:
                           
                    $shopid     = Config::get('constants.ShopeeFNShopid');
                    $partner_id = Config::get('constants.ShopeeFNPartnerID');
                    $secret     = Config::get('constants.ShopeeFNSecret');    
                   $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'","pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'","pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                   
                    break;
                case 5:
                    $shopid     = Config::get('constants.ShopeeOrientalShopid');
                    $partner_id = Config::get('constants.ShopeeOrientalPartnerID');
                    $secret     = Config::get('constants.ShopeeOrientalSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 6:
                    $shopid     = Config::get('constants.ShopeeNikudoShopid');
                    $partner_id = Config::get('constants.ShopeeNikudoPartnerID');
                    $secret     = Config::get('constants.ShopeeNikudoSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 7:
                    $shopid     = Config::get('constants.ShopeeStarbucksShopid');
                    $partner_id = Config::get('constants.ShopeeStarbucksPartnerID');
                    $secret     = Config::get('constants.ShopeeStarbucksSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 8:
                    $shopid     = Config::get('constants.ShopeeKawanShopid');
                    $partner_id = Config::get('constants.ShopeeKawanPartnerID');
                    $secret     = Config::get('constants.ShopeeKawanSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 9:
                    $shopid     = Config::get('constants.ShopeePokkaShopid');
                    $partner_id = Config::get('constants.ShopeePokkaPartnerID');
                    $secret     = Config::get('constants.ShopeePokkaSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 10:
                    $shopid     = Config::get('constants.ShopeeEtikaShopid');
                    $partner_id = Config::get('constants.ShopeeEtikaPartnerID');
                    $secret     = Config::get('constants.ShopeeEtikaSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 11:
                    $shopid     = Config::get('constants.ShopeeEbfrozenShopid');
                    $partner_id = Config::get('constants.ShopeeEbfrozenPartnerID');
                    $secret     = Config::get('constants.ShopeeEbfrozenSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 12:
                    $shopid     = Config::get('constants.ShopeeEverbestShopid');
                    $partner_id = Config::get('constants.ShopeeEverbestPartnerID');
                    $secret     = Config::get('constants.ShopeeEverbestSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                default:
                    $shopid     = Config::get('constants.ShopeeJocomShopid');
                    $partner_id = Config::get('constants.ShopeeJocomPartnerID');
                    $secret     = Config::get('constants.ShopeeJocomSecret');  
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
            }
            print_r($sig);
            echo '<pre>';
            // print_r($x);
            print_r($post);
            echo '</pre>';
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://partner.uat.shopeemobile.com/api/v2/public/get_refresh_token_by_upgrade_code?partner_id='.$partner_id.'&sign='.$sig.'&timestamp='.$currentdate,
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS => '{
                "shop_id_list": [
                   $shopid
                ],
                "upgrade_code": "4663786f5153527667684f4c574e645a68496f464875797457706174416f554d"
            }',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            echo $response;
            die();
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => 'https://partner.shopeemobile.com/api/v2/order/get_order_list?access_token='.$sig.'&cursor=%22%22&order_status=READY_TO_SHIP&page_size=20&partner_id=partner_id&response_optional_fields=order_status&shop_id=shop_id&sign=sign&time_from=1607235072&time_range_field=create_time&time_to=1608271872&timestamp=timestamp',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'GET',
              CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
              ),
            ));
            
            $response = curl_exec($curl);
            
            curl_close($curl);
            echo $response;
            
                die();
                    
            
            $ch = curl_init('https://partner.shopeemobile.com/api/v2/order/get_order_list');

            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

            //execute post
            $result = curl_exec($ch);
            print_r($result);


            $results = json_decode($result, TRUE);
            print_r($results);
            die('In');
            $array = array();

            if (!empty($results)) {
                foreach ($results['orders'] as $key => $value) {

                    $ordernum = $value['ordersn'];

                    array_push($array, $ordernum);
                }   
            }

            $chunked_arr = array_chunk($array,50);
            $total = array();

            foreach ($chunked_arr as $key => $value) {

                $list = json_encode($value);

                $OrderCollection = $this->getOrders($listType,$list,$shopid,$partner_id,$secret);

                array_push($total, count($OrderCollection));

                if(count($OrderCollection) > 0 ){
                    // Save new records
                    $isSaved = $this->saveNewOrder($OrderCollection);
                    
                }
            }
            // }
            // } //} //EndNewMg
            //   echo "<pre>";
            // print_r($OrderCollection);
            // echo "</pre>";
            // die();
            
  
        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getMessage();
        // echo $message;
        }

        finally {
            return array(
                "response" => $isError,
                "totalRecords" => array_sum($total),
                //"LatestRecord" => $OrderCollection,
                "message"=>$message,
                "charges"=>$isSaved
            );
        }

    }

    public function getOrders($listType,$ordernum,$shopid,$partner_id,$secret){
         set_time_limit(0);
        ini_set('memory_limit', '-1');
        
        $orderCollection = [];
        $date = new DateTime();
        $currentdate = $date->getTimestamp();
        $isTrue = 0; 

        if($listType > 4){
            $transorders = Transaction::where('external_ref_number','=', $ordernum)->first();
            if (empty($transorders)){
                $isTrue = 0; 
            } 
            else {
                $isTrue = 1; 
            }
        }
        
        if($isTrue == 0){  
            
            $string = 'https://partner.shopeemobile.com/api/v2/order/detail|{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "ordersn_list":'.$ordernum.'}';
            $sig = hash_hmac('sha256', $string, $secret);
            $post = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "ordersn_list":'.$ordernum.'}';
            $header = array('Content-Type: application/json', 'Authorization: '. $sig);
    
            $ch = curl_init('https://partner.shopeemobile.com/api/v2/order/detail');
                                                                            
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);
    
            //execute post
            $result = curl_exec($ch);
    
            $results = json_decode($result, TRUE);
    
            foreach ($results['orders'] as $value) 
            {
                $OrderRecord  = DB::table('jocom_shopee_order')
                                    ->where('ordersn', $value['ordersn'])
                                    ->where('activation', 1)
                                    ->get();
    
                if (empty($OrderRecord)) 
                {
                    if (!isset($orderCollection[$value['ordersn']]))
                    {
    
                        $orderCollection[$value['ordersn']] = array(
                                'ordersn'=> $value['ordersn'],
                                "name" =>$value['recipient_address']['name'],
                                "zipcode" =>$value['recipient_address']['zipcode'],
                                "full_address" =>$value['recipient_address']['full_address'],
                                "phone" =>$value['recipient_address']['phone'],
                                "message_to_seller" =>$value['recipient_address']['message_to_seller'],
                                "estimated_shipping_fee" =>$value['estimated_shipping_fee'],
                                "from_account" =>$listType,
                                "created_by" => Session::get('username'),
                                "created_at" => date('Y-m-d H:i:s'),
                                "json" => $value,
                                'product' => array()
                            );
                    }
    
                    foreach ($value['items'] as $va) 
                    {
    
                        $orderCollection[$value['ordersn']]['product'][] = array(
                            'item_name'                     => $va['item_name'], 
                            'item_sku'                      => $va['item_sku'], 
                            'variation_sku'                 => $va['variation_sku'], 
                            'variation_name'                => $va['variation_name'], 
                            'variation_quantity_purchased'  => $va['variation_quantity_purchased'], 
                            'variation_discounted_price'    => $va['variation_discounted_price'], 
                            'variation_original_price'      => $va['variation_original_price'], 
                            "message_to_seller"             =>$value['recipient_address']['message_to_seller'],
                            "name"                          =>$value['recipient_address']['name'],
                            "phone"                         =>$value['recipient_address']['phone'],
                            "zipcode"                       =>$value['recipient_address']['zipcode'],
                            "full_address"                  =>$value['recipient_address']['full_address'],
                            "estimated_shipping_fee"        =>$value['estimated_shipping_fee'],
                            "from_account"                  =>$listType,
                            // 'ordersn'=> $value['ordersn'],
                        );
                    }
                }
            
            }
        }
        return $orderCollection;

    }

    public function anyRevert(){
        
        $isError = 0;
        
        try{
            $order_id = Input::get('order_id');
            $ShopeeOrder = ShopeeOrder::find($order_id);
            $ShopeeOrder->status =  1;
            $ShopeeOrder->is_completed =  1;
            $ShopeeOrder->save();
            
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
         set_time_limit(0);
        ini_set('memory_limit', '-1');
        $counter = 0;
        $isSaved = true;
        $manualProcess = false;
        $manualProcessOrder = array();
        $transferedProcessOrder = array();
        $shopeeDeliveryCharges = 0;
        set_time_limit(0);

        try{
            
            $tax_rate = Fees::get_tax_percent();
            
            foreach ($OrderCollection as $key => $value) 
            {
                $ordersn                = $value['ordersn'];
                $name                   = $value['name'];
                $phone                  = $value['phone'];
                $receiver               = $value['name'];
                $receiverTel            = $value['phone'];
                $full_address           = $value['full_address'];
                $zipcode                = $value['zipcode'];
                $estimated_shipping_fee = $value['estimated_shipping_fee'];
                $from_account           = $value['from_account'];
                // Check if the order already in database 
                $orders = ShopeeOrder::where('ordersn','=', $ordersn)->first();
   
                if (empty($orders)) 
                {
                    $Shopee = new ShopeeOrder;
                    $Shopee->ordersn = $ordersn;
                    $Shopee->name = $name;
                    $Shopee->phone = $phone;
                    $Shopee->transaction_id = 0;
                    $Shopee->migrate_from = "Shopee";
                    $Shopee->is_completed = 1;
                    $Shopee->from_account = $from_account;
                    $Shopee->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                    $Shopee->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";

                    $Shopee->save();

                    $OrderID = $Shopee->id;

                     // Save Product Details
                    foreach ($value['product'] as $key => $val) {
                    
                        $ShopeeOrderDetails = new ShopeeOrderDetails;
                        $ShopeeOrderDetails->order_id = $OrderID;
                        $ShopeeOrderDetails->ordersn = $ordersn;
                        $ShopeeOrderDetails->item_name = $val['item_name'];
                        $ShopeeOrderDetails->item_sku = $val['item_sku'];
                        $ShopeeOrderDetails->api_result_return = json_encode($val);
                        $ShopeeOrderDetails->api_result_full = json_encode($value['json']);
                        $ShopeeOrderDetails->created_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $ShopeeOrderDetails->updated_by = Session::get('username') != "" ? Session::get('username') : "api_update";
                        $ShopeeOrderDetails->save();

                    }
                }

                $username_Shopee = 'shopee';
                $password_Shopee = '';
                $city = DB::table('postcode')->where('postcode', $zipcode)->first();
                $city_id = DB::table('jocom_cities')->where('name', $city->post_office)->first();
                $country_id = 458;

                $OrderDataDetails = ShopeeOrderDetails::getByOrderID($OrderID);

                $qrcode = array();
                $priceopt = array();
                $qty = array();
                $shopee_original_price = array();
                $shopee_platform_original_price = array();
                
                $zeroDelivery = 0;
            
                foreach ($OrderDataDetails as $keyDetailsCheck => $valueDetailsCheck) {
                    $APIDataCheck = json_decode($valueDetailsCheck->api_result_return, true);
                    if($APIDataCheck['estimated_shipping_fee'] == 0){
                        $zeroDelivery = 1;
                    }
                }

                $shopeeDeliveryCharges = 0;  
                    
                $delivery = array();
                $is_skip = 0;
                $extra_message = "";
                    
                foreach ($OrderDataDetails as $keyDetails => $valueDetails) 
                {
                    $APIData = json_decode($valueDetails->api_result_return, true);
                        
                    if(count($APIData['item_sku']) > 0 )
                    { 
                        if(count($APIData['message_to_seller']) > 0 ){
                            $extra_message = $APIData['message_to_seller'];
                        }

                        //find by Qrcode
                        $ProductInformation = Product::findProductInfoByQRCODE($APIData['item_sku']);

                            if(count($ProductInformation) >  0)
                            {
                                $qrcode[] = $ProductInformation->qrcode;
                                
                                // //CHECK DEFINE LABEL PRICE CODE OLD//
                                // $optionName = $APIData['variation_sku'];
                                                                
                                // if(count($optionName) > 0 )
                                // {                                  
                                    
                                //     if ((strpos($optionName, '[') !== false) && (strpos($optionName, ']') !== false))
                                //     {
                                //         // TAKE DEFINED LABEL ID //
                                //         $selectedPriceOptionID = substr($optionName,strpos($string,"[") + 1,strpos($optionName,"]") -1);
                                //         $PriceOption = Price::find($selectedPriceOptionID);

                                //         if(count($PriceOption)>0)
                                //         {
                                //             $priceopt[] = $selectedPriceOptionID;       
                                //         }else{
                                //             $priceopt[] = $ProductInformation->ProductPriceID;
                                //         }

                                //     }else{
                                //         $priceopt[] = $ProductInformation->ProductPriceID;
                                //     }
                                    
                                // }else{
                                //     $priceopt[] = $ProductInformation->ProductPriceID;
                                //      // $priceopt[] = $APIData['total'];
                                // }
                                // // CHECK DEFINE LABEL PRICE CODE OLD//
                                
                                //CHECK DEFINE LABEL PRICE CODE //
                                $optionId = $APIData['variation_sku'];
                                                                
                                if(strlen($optionId) > 0 )
                                {                                                                                        
                                        // TAKE DEFINED LABEL ID //
                                        
                                        $PriceOption = Price::find($optionId);

                                        if(count($PriceOption)>0)
                                        {
                                            $priceopt[] = $optionId;       
                                        }else{
                                            $priceopt[] = $ProductInformation->ProductPriceID;
                                        }
                                    
                                }else{
                                    $priceopt[] = $ProductInformation->ProductPriceID;
                                     // $priceopt[] = $APIData['total'];
                                }
                                // CHECK DEFINE LABEL PRICE CODE //
                                
                                $qty[] = $APIData['variation_quantity_purchased'];   
                                $shopee_original_price[] = $APIData['variation_discounted_price']; 
                                $shopee_platform_original_price[] = $APIData['variation_original_price']; 
                                
                            }else{

                                $is_skip = 1;
                                $manualProcess = true;
                                array_push($manualProcessOrder, array(
                                    "order_number"=>$ordersn,
                                    "buyername"=>$name 
                                ));

                            }
                    }else{
                            
                        $is_skip = 1;
                        $manualProcess = true;
                        array_push($manualProcessOrder, array(
                            "order_number"=>$ordersn,
                            "buyername"=>$name 
                        ));
                    }                                          
                }

                $shopeeDeliveryCharges = $shopeeDeliveryCharges + ($estimated_shipping_fee * (100/(100 + $tax_rate)));
                $delivery[] = $shopeeDeliveryCharges;

                if($is_skip == 0)
                {
                    $get = array(
                        'user'                => $username_Shopee,             // Buyer Username
                        'pass'                => $password_Shopee,             // Buyer Password
                        'delivery_name'       => $name,      // delivery name
                        'delivery_contact_no' => $phone, // delivery contact no
                        'special_msg'         => 'Transaction transfer from Shopee ( Order Number : '.$ordersn.' )'. " ".$extra_message,       // special message
                        'delivery_addr_1'     => $full_address,
                        'delivery_addr_2'     => '',
                        'delivery_postcode'   => $zipcode,
                        'delivery_city'       => $city_id->id, // City ID 
                        'delivery_state'      => $city_id->state_id,                          // State ID
                        'delivery_country'    => $country_id,                 // Country ID
                        'shopeeDeliveryCharges'=> $shopeeDeliveryCharges,  
                        'qrcode'              => $qrcode,
                        'price_option'        => $priceopt, // Price Option
                        'qty'                 => $qty,
                        'shopee_original_price' => $shopee_original_price,
                        'shopee_platform_original_price' => $shopee_platform_original_price,
                        'devicetype'          => 'cms',
                        'uuid'                => NULL, // City ID
                        'lang'                => 'EN',
                        'ip_address'          => Request::getClientIp(),
                        'location'            => '',
                        'transaction_date'    => date("Y-m-d H:i:s"),
                        'charity_id'          => '',
                    );

                    $data = MCheckout::checkout_transaction($get);
                    
                    echo "<pre>";
                    print_r($data);
                    echo "</pre>";

                    if($data['status'] == "success")
                    {
                        $transaction_id = $data["transaction_id"];
                            
                            // PUSH TO SUCCESS LIST 
                        array_push($transferedProcessOrder, array(
                             "order_number"=>$ordersn,
                            "buyername"=>$name,
                            "transactionID"=>$transaction_id
                        ));                            
                            
                        // SAVE AS COMPLETED TRANSACTION //
                        $trans = Transaction::find($transaction_id);
                        $trans->status = 'completed';
                        $trans->modify_by = 'API';
                        $trans->modify_date = date("Y-m-d h:i:sa");
                        $trans->save();
                        // SAVE AS COMPLETED TRANSACTION //

                        // Update Status Shopee Order as transfered
                        if($OrderID != 0)
                        {
                            $ShopeeOrder = ShopeeOrder::find($OrderID);
                            $ShopeeOrder->status = 2;
                            $ShopeeOrder->transaction_id = $transaction_id;
                            $ShopeeOrder->save();
                        }

                    }else{

                        $manualProcess = true;
                        array_push($manualProcessOrder, array(
                            "order_number"=>$ordersn,
                            "buyername"=>$name
                        ));
                            // THROW ERROR FAILED TO CREATE TRANSACTION
                    }
                }
            }
            switch ($from_account) {
                case 1:
                    $acc_name = "Shopee Jocom";  
                    break;
                case 2:
                    $acc_name = "Shopee Coca-Cola";  
                    break;
                case 3:
                    $acc_name = "Shopee Yeo Hiap Seng";  
                    break;
                case 4:
                    $acc_name = "Shopee F&N";  
                    break;
                case 5:
                    $acc_name = "Shopee OrientalFoodMY";  
                    break;
                case 6:
                    $acc_name = "Shopee NikudoSeafood";  
                    break;
                case 7:
                    $acc_name = "Shopee Starbucks.OS";  
                    break;
                case 8:
                    $acc_name = "Shopee KawanFood";  
                    break;
                case 9:
                    $acc_name = "Shopee Pokka";  
                    break;
                case 10:
                    $acc_name = "Shopee Etika";  
                    break;
                case 11:
                    $acc_name = "Shopee Ebfrozen";  
                    break;
                case 12:
                    $acc_name = "Shopee Everbest";  
                    break;
                default:
                   $acc_name = "Shopee Jocom";  
                    break;
            }
            // MANUAL PROCESS HANDLING
            // if($manualProcess){
                // 1. SEND EMAIL TO GANES TO INFORM INFORMATION
                $subject = "Migration Notification";
                $recipient = array(
                    "email"=>Config::get('constants.ShopeeManagerEmail'),
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

                Mail::send('emails.shopeemigratereport', $data, function($message) use ($recipient,$subject)
                {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($recipient['email'], $recipient['name'])
                            // ->cc(Config::get('constants.ShopeeManagerEmailCC'))
                            ->cc(['maruthu@jocom.my', 'fooyau@jocom.my'])
                            ->subject($subject);
                });
                       
                $running_number = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'batch_no')->first();
                
                $batchNo = str_pad($running_number->counter + 1,10,"0",STR_PAD_LEFT);
                $NewRunner = Running::find($running_number->id);
                $NewRunner->counter = $running_number->counter + 1;
                $NewRunner->save();
                
                self::transactionDeliverytime24h($batchNo, $transferedProcessOrder);
                
            // }

            
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
         set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
        DB::beginTransaction();
        try{
            
            $batch = ShopeeOrder::getBatch();

            //return $batch;
            if(count($batch) > 0 ){
                
                set_time_limit(0);
                foreach ($batch as $key => $value) {

                    $transaction_id = $value->transaction_id;
                    $Transaction = Transaction::find($transaction_id);
                    if($Transaction->status == 'completed'){
                        $order_id = $value->id;
                        $ShopeeOrder = ShopeeOrder::find($order_id);
                        $TransactionInfo = Transaction::find($transaction_id);

                        if($TransactionInfo->do_no == ""){
                            // CREATE INV
                            $tempInv = MCheckout::generateInv($transaction_id, true);
                            // CREATE PO
                            $tempPO = MCheckout::generatePO($transaction_id, true);
                            // CREATE DO
                            $tempDO = MCheckout::generateDO($transaction_id, true);
                        }

                        $ShopeeOrder->status = "2";
                        $ShopeeOrder->is_completed = "2";
                        $ShopeeOrder->updated_by = "api_system";
                        $ShopeeOrder->save();
                        // AUTOMATED LOG TO LOGISTIC APP
                        $logisticcheck = LogisticTransaction::where("transaction_id",'=', $transaction_id)->get();
                        if(count($logisticcheck)==0){
                           LogisticTransaction::log_transaction($transaction_id); 
                        }
                        

                        $logisticData= LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        
                        // Insert transaction into schedule
                        $ShopeePushStatus = new ShopeePushStatus();
                        $ShopeePushStatus->shopee_order_number = $value->ordersn;
                        $ShopeePushStatus->transaction_id = $value->transaction_id;
                        $ShopeePushStatus->logistic_id = $logisticData->id;
                        $ShopeePushStatus->is_completed = 0;
                        $ShopeePushStatus->current_logistic_status = 0;
                        $ShopeePushStatus->from_account = $value->from_account;
                        $ShopeePushStatus->created_by = Session::get('username') != "" ? Session::get('username') : "SYSTEM";
                        $ShopeePushStatus->save();
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
            }else{
                DB::rollBack();
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }
        
    }
    
    public function anyBatchnewgenerate(){
         set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
        DB::beginTransaction();
        try{
            
            $batch = ShopeeOrder::getBatchlist();
            
            // echo '<pre>';
            // print_r($batch);
            // echo '</pre>';
            die();

            //return $batch;
            if(count($batch) > 0 ){
                
                set_time_limit(0);
                foreach ($batch as $key => $value) {

                    $transaction_id = $value->transaction_id;
                    
                    $trans = Transaction::find($transaction_id);
                    $trans->status = 'completed';
                    $trans->modify_by = 'API';
                    $trans->modify_date = date("Y-m-d h:i:sa");
                    $trans->save();
                    
                    $Transaction = Transaction::find($transaction_id);
                    if($Transaction->status == 'completed'){
                        $order_id = $value->id;
                        $ShopeeOrder = ShopeeOrder::find($order_id);
                        $TransactionInfo = Transaction::find($transaction_id);

                        if($TransactionInfo->do_no == ""){
                            // CREATE INV
                            $tempInv = MCheckout::generateInv($transaction_id, true);
                            // CREATE PO
                            $tempPO = MCheckout::generatePO($transaction_id, true);
                            // CREATE DO
                            $tempDO = MCheckout::generateDO($transaction_id, true);
                        }

                        $ShopeeOrder->status = "2";
                        $ShopeeOrder->is_completed = "2";
                        $ShopeeOrder->updated_by = "api_system";
                        $ShopeeOrder->save();
                        // AUTOMATED LOG TO LOGISTIC APP
                        $logisticcheck = LogisticTransaction::where("transaction_id",'=', $transaction_id)->get();
                        if(count($logisticcheck)==0){
                           LogisticTransaction::log_transaction($transaction_id); 
                        }
                        

                        $logisticData= LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        
                        // Insert transaction into schedule
                        $ShopeePushStatus = new ShopeePushStatus();
                        $ShopeePushStatus->shopee_order_number = $value->ordersn;
                        $ShopeePushStatus->transaction_id = $value->transaction_id;
                        $ShopeePushStatus->logistic_id = $logisticData->id;
                        $ShopeePushStatus->is_completed = 0;
                        $ShopeePushStatus->current_logistic_status = 0;
                        $ShopeePushStatus->from_account = $value->from_account;
                        $ShopeePushStatus->created_by = Session::get('username') != "" ? Session::get('username') : "SYSTEM";
                        $ShopeePushStatus->save();
                    }
                     echo $transaction_id.'<br>';
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
            }else{
                DB::rollBack();
            }
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }
        
    }


    public function anyShopeestatus(){
        
        /*
         * 1. Insert into schedule list when mark sent/ update sent
         * 2. Run crob job to every 0900 , 1300, 1700, 2100
         * 3. Save response into log and update try out
         */
        
        
        /*
         * Get all incomplete order
         */
        die();
        $isError = 0;
        $successResponse = array();
        $pushStatusList = array();
        $pushStatusSuccessList = array();
        $pushStatusFailedList = array();
        $message = "";
        
        
        try{
            
            // Begin Transaction
            DB::beginTransaction();
            
            // Get Incomplete shopee orders
            $incompleteOrders = DB::table('jocom_shopee_push_status AS JSPS ')
                    ->where("JSPS.is_completed","0")
                    ->get();
           
            foreach ($incompleteOrders as $key => $value) {

                $ShopeeScheduleID = $value->id;
                $LogisticTransaction = LogisticTransaction::find($value->logistic_id);
                $transactionID = $LogisticTransaction->transaction_id;

                $StatusLog = DB::table('jocom_shopee_push_status_log AS JSPSL ')
                    ->where("JSPSL.push_order_id",$value->id)
                    ->where("JSPSL.status",1)
                    ->get();

                // Shopee Order Number
                $shopeeOrderNumber = $value->shopee_order_number;
                
                $statusData = UtilitiesController::getLogisticStatusInfo($LogisticTransaction->status);

                switch ($value->from_account) {
                    case 1:
                        $shopid     = Config::get('constants.ShopeeJocomShopid');
                        $partner_id = Config::get('constants.ShopeeJocomPartnerID');
                        $secret     = Config::get('constants.ShopeeJocomSecret');  
                        break;
                    case 2:
                        $shopid     = Config::get('constants.ShopeeCocaShopid');
                        $partner_id = Config::get('constants.ShopeeCocaPartnerID');
                        $secret     = Config::get('constants.ShopeeCocaSecret');  
                        break;
                    case 3:
                        $shopid     = Config::get('constants.ShopeeYeoShopid');
                        $partner_id = Config::get('constants.ShopeeYeoPartnerID');
                        $secret     = Config::get('constants.ShopeeYeoSecret');    
                        break;
                    case 4:
                        $shopid     = Config::get('constants.ShopeeFNShopid');
                        $partner_id = Config::get('constants.ShopeeFNPartnerID');
                        $secret     = Config::get('constants.ShopeeFNSecret');    
                        break;
                    case 5:
                        $shopid     = Config::get('constants.ShopeeOrientalShopid');
                        $partner_id = Config::get('constants.ShopeeOrientalPartnerID');
                        $secret     = Config::get('constants.ShopeeOrientalSecret');    
                        break;
                    case 6:
                        $shopid     = Config::get('constants.ShopeeNikudoShopid');
                        $partner_id = Config::get('constants.ShopeeNikudoPartnerID');
                        $secret     = Config::get('constants.ShopeeNikudoSecret');    
                        break;
                    case 7:
                        $shopid     = Config::get('constants.ShopeeStarbucksShopid');
                        $partner_id = Config::get('constants.ShopeeStarbucksPartnerID');
                        $secret     = Config::get('constants.ShopeeStarbucksSecret');    
                        break;
                    case 8:
                        $shopid     = Config::get('constants.ShopeeKawanShopid');
                        $partner_id = Config::get('constants.ShopeeKawanPartnerID');
                        $secret     = Config::get('constants.ShopeeKawanSecret');    
                        break;  
                    case 9:
                        $shopid     = Config::get('constants.ShopeePokkaShopid');
                        $partner_id = Config::get('constants.ShopeePokkaPartnerID');
                        $secret     = Config::get('constants.ShopeePokkaSecret');    
                        break;  
                    case 10:
                        $shopid     = Config::get('constants.ShopeeEtikaShopid');
                        $partner_id = Config::get('constants.ShopeeEtikaPartnerID');
                        $secret     = Config::get('constants.ShopeeEtikaSecret');    
                        break; 
                    case 11:
                        $shopid     = Config::get('constants.ShopeeEbfrozenShopid');
                        $partner_id = Config::get('constants.ShopeeEbfrozenPartnerID');
                        $secret     = Config::get('constants.ShopeeEbfrozenSecret');    
                        break; 
                    case 12:
                        $shopid     = Config::get('constants.ShopeeEverbestShopid');
                        $partner_id = Config::get('constants.ShopeeEverbestPartnerID');
                        $secret     = Config::get('constants.ShopeeEverbestSecret');    
                        break; 
                    default:
                        $shopid     = Config::get('constants.ShopeeJocomShopid');
                        $partner_id = Config::get('constants.ShopeeJocomPartnerID');
                        $secret     = Config::get('constants.ShopeeJocomSecret');  
                        break;
                }

//                
                if(!$statusData){
                    $logistic_status = "No Status";
                }else{
                    $logistic_status = $statusData['status_description'];
                }
                
                // Have Record
                if(count($StatusLog) > 0 ){
                    
                    if($LogisticTransaction->status == 0){
                        $dateTime = $LogisticTransaction->insert_date; 
                    }else{
                        $dateTime = $LogisticTransaction->modify_date; 
                    }

                    if($value->current_logistic_status != $LogisticTransaction->status){
                        // Insert to push latest status List 
                        $setData = array(
                            "push_order_id" => $value->id,
                            "transaction_ID" => $transactionID,
                            "order_number" => $shopeeOrderNumber,
                            "logistic_status_code" => $LogisticTransaction->status,
                            "logistic_status" => $logistic_status,
                            "datetime" => $dateTime,
                            "remark" => '',
                            "shopid" => $shopid,
                            "partner_id" => $partner_id,
                            "secret" => $secret,
                        );
                        
                        array_push($pushStatusList, $setData);

                    }
                }else{
                    
                    if($LogisticTransaction->status == 0){
                        $dateTime = $LogisticTransaction->insert_date; 
                    }else{
                        $dateTime = $LogisticTransaction->modify_date; 
                    }
                   
                    // Insert to push latest status List 
                    $setData = array(
                            "push_order_id" => $value->id,
                            "transaction_ID" => $transactionID,
                            "order_number" => $shopeeOrderNumber,
                            "logistic_status_code" => $LogisticTransaction->status,
                            "logistic_status" => $logistic_status,
                            "datetime" => $dateTime,
                            "remark" => '',
                            "shopid" => $shopid,
                            "partner_id" => $partner_id,
                            "secret" => $secret,
                        );
                    
                    array_push($pushStatusList, $setData);
                    
                }

            }
       
            // Start push status to shopee API only when logistic status is SENT
            foreach ($pushStatusList as $kPush => $vPush) {

                if ($vPush['logistic_status'] === "Sent") 
                {
                    $setDataPush = array(
                    "ordersn" => $vPush['order_number'],
                    "timestamp" => $vPush['datetime'],
                    "shopid" => $vPush['shopid'],
                    "partner_id" => $vPush['partner_id'],
                    );

                    $shopid      = $vPush['shopid'];
                    $partner_id  = $vPush['partner_id'];
                    $date        = new DateTime();
                    $currentdate = $date->getTimestamp();
                    $ordersn     = $vPush['order_number'];
                    $secret      = $vPush['secret'];
             
                    $string = 'https://partner.shopeemobile.com/api/v2/logistics/offline/set|{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "ordersn":"'.$ordersn.'"}';

                    $sig = hash_hmac('sha256', $string, $secret);
                    $post = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "ordersn":"'.$ordersn.'"}';
                    $header = array('Content-Type: application/json', 'Authorization: '. $sig);

                    $ch = curl_init('https://partner.shopeemobile.com/api/v2/logistics/offline/set');
                                                                                    
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 300);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

                    //execute post
                    $result = curl_exec($ch);

                    $pushDatetime = date("Y-m-d h:i:s");
     
                    if($result === "{}"){
                        
                        array_push($pushStatusSuccessList, array_merge($vPush,array("APIHttpCode" => json_encode($result))));
                  
                        // Insert into LOG
                        $ShopeePushStatusLog = new ShopeePushStatusLog();
                        $ShopeePushStatusLog->push_order_id = $vPush['push_order_id'];
                        $ShopeePushStatusLog->push_data = json_encode($setDataPush);
                        $ShopeePushStatusLog->push_at = $pushDatetime;
                        $ShopeePushStatusLog->response_data = json_encode($result);
                        $ShopeePushStatusLog->response_at = date("Y-m-d h:i:s");
                        $ShopeePushStatusLog->push_status = $vPush['logistic_status'];
                        $ShopeePushStatusLog->status = 1;
                        $ShopeePushStatusLog->save();
                        
                         
                        $ShopeePushStatus = ShopeePushStatus::find($vPush['push_order_id']);
                        $ShopeePushStatus->current_logistic_status = $vPush['logistic_status_code'];
                        $ShopeePushStatus->updated_by = 'SYSTEM';
                        if($vPush['logistic_status_code'] == 5){ // 5 = Sent
                            $ShopeePushStatus->is_completed = 1;
                        }
                        $ShopeePushStatus->save(); 
                        
                    }else{
                        array_push($pushStatusFailedList, array_merge($vPush,array("APIHttpCode" => json_encode($result))));
                        
                        $ShopeePushStatusLog = new ShopeePushStatusLog();
                        $ShopeePushStatusLog->push_order_id = $vPush['push_order_id'];
                        $ShopeePushStatusLog->push_data = json_encode($setDataPush);
                        $ShopeePushStatusLog->push_at = $pushDatetime;
                        $ShopeePushStatusLog->response_data = json_encode($result);
                        $ShopeePushStatusLog->response_at = date("Y-m-d h:i:s");
                        $ShopeePushStatusLog->push_status = $vPush['logistic_status'];
                        $ShopeePushStatusLog->status = 0;
                        $ShopeePushStatusLog->save();
                    }
                }
            }
         
           // Send Email Notification 
            $recipient = array(
                "name" => "",
                "email" => Config::get('constants.ShopeeManagerEmail')
            );
            $subject = "SHOPEE PUSH STATUS REPORT ".date("Y-m-d H:i:s");
            
            $data = array(
                    'execution_datetime'      => date("Y-m-d H:i:s"),
                    'total_records'  => count($pushStatusFailedList) + count($pushStatusSuccessList),
                    'manual_process'  => count($pushStatusFailedList),
                    'failed_list'  => $pushStatusFailedList,
                    'success_list'  => $pushStatusSuccessList
            );
            
            Mail::send('emails.shopeepushstatusreport', $data, function($message) use ($recipient,$subject)
            {
                $message->from('payment@jocom.my', 'JOCOM SHOPEE PUSH STATUS');
                $message->to($recipient['email'], $recipient['name'])->subject($subject);
            });
                
        
        } catch (Exception $ex) {
            
            $message = $ex->getMessage();
            $isError = 1;
            echo $ex->getLine();
            
        }  finally {
            if($isError){
                // LOG ERROR
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "response" => $isError,
                "response_message" => $message
            );
                
        }
        
        
    }
    
    public function anyShopeepriceupdate(){
    
        //  die('Done');

            $lazadaPrice = DB::table('jocom_shopee_price')
                              ->get();

            if(count($lazadaPrice) > 0){

                foreach ($lazadaPrice as $value) {
                    
                        echo $value->id .'==' . $value->transaction_id .'<br>';
                        $trans_id = 0;
                        $product_id = 0;
                        $id = 0;
                        $price = 0; 
                        $id = (int) $value->id;
                        $trans_id = (int) $value->transaction_id;
                        $price = number_format($value->price,2);

                         DB::table('jocom_transaction_details')
                                ->where("id","=",$id)
                                ->update(
                                        ['price' => $price]
                                );

                }
            }

            echo 'Value Updated'.'<br>';

            $lazadatrans = DB::table('jocom_shopee_price')
                              ->select('transaction_id')
                              ->distinct()->get();

            if(count($lazadatrans) > 0){

                foreach ($lazadatrans as $value) {
                    echo $value->transaction_id .'<br>';

                    $t_id = 0;
                    $t_id = $value->transaction_id;

                     $trasdetails = DB::table('jocom_transaction_details')->where('transaction_id','=',$t_id)->get();

                     if(count($trasdetails)>0){
                            foreach ($trasdetails as $value2) {
                                $d_id = 0; 
                                $d_price = 0; 
                                $d_unit = 0; 
                                $d_total = 0;
                                $d_id = $value2->id;
                                $d_price = round($value2->price,2);
                                $d_unit = (int)$value2->unit;

                                $d_total = round(($d_price * $d_unit),2);


                                DB::table('jocom_transaction_details')
                                    ->where("id","=",$d_id)
                                    ->update(
                                            ['total' => $d_total]
                                    );
                                # code...
                            }

                     }

                     $trasdetails = DB::table('jocom_transaction_details')
                                    ->select(DB::raw('SUM(total) as total_amount'))
                                    ->where('transaction_id','=',$t_id)->first();

                     if(count($trasdetails)>0){

                        echo $value->transaction_id .'-'.$trasdetails->total_amount.'<br>';

                        $transactions = Transaction::find($t_id);
                        $transactions->total_amount = $transactions->delivery_charges + $trasdetails->total_amount;
                        $transactions->save();

                     }               



                }

                // 

            }



    }
        public function anyTestmigrate(){
          
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
        $OrderCollection = array();
        $message = "";
    // echo 'In';
        try{
 
            $date = new DateTime();
            $earlier = $date->modify('-15 day');
            $from = $earlier->getTimestamp();
            $earlier1 = $date->modify('-5 day');
            $from1 = $earlier1->getTimestamp();
            
            $parameters = array(
                "pagination_offset" => 0,
                "pagination_entries_per_page" => 100,
            );

            $date2 = new DateTime();
            $currentdate = $date2->getTimestamp();
            $pagination_entries_per_page = 100;
            $status = "READY_TO_SHIP";
            $listType = Input::get('list_type');
            // pagination_entries_per_page
            switch ($listType) {
                case 1:
                    $shopid     = Config::get('constants.ShopeeJocomShopid');
                    $partner_id = (int) Config::get('constants.ShopeeJocomPartnerID');
                    $secret     = (int) Config::get('constants.ShopeeJocomSecret');  
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.',"timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $get_param='access_token='.$sig.'&order_status='.$status.'&page_size='.$pagination_entries_per_page.'&partner_id='.$partner_id.'&shop_id='.$shopid.'&sign='.$sig.'&time_from='.$from.'&time_range_field=create_time&time_to='.$currentdate.'&timestamp='.$currentdate.'';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);

                    break;
                case 2:
                    $shopid     = Config::get('constants.ShopeeCocaShopid');
                    $partner_id = Config::get('constants.ShopeeCocaPartnerID');
                    $secret     = Config::get('constants.ShopeeCocaSecret');   
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 3:
                    $shopid     = Config::get('constants.ShopeeYeoShopid');
                    $partner_id = Config::get('constants.ShopeeYeoPartnerID');
                    $secret     = Config::get('constants.ShopeeYeoSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 4:
                           
                    $shopid     = Config::get('constants.ShopeeFNShopid');
                    $partner_id = Config::get('constants.ShopeeFNPartnerID');
                    $secret     = Config::get('constants.ShopeeFNSecret');    
                   $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'","pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'","pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                   
                    break;
                case 5:
                    $shopid     = Config::get('constants.ShopeeOrientalShopid');
                    $partner_id = Config::get('constants.ShopeeOrientalPartnerID');
                    $secret     = Config::get('constants.ShopeeOrientalSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 6:
                    $shopid     = Config::get('constants.ShopeeNikudoShopid');
                    $partner_id = Config::get('constants.ShopeeNikudoPartnerID');
                    $secret     = Config::get('constants.ShopeeNikudoSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 7:
                    $shopid     = Config::get('constants.ShopeeStarbucksShopid');
                    $partner_id = Config::get('constants.ShopeeStarbucksPartnerID');
                    $secret     = Config::get('constants.ShopeeStarbucksSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 8:
                    $shopid     = Config::get('constants.ShopeeKawanShopid');
                    $partner_id = Config::get('constants.ShopeeKawanPartnerID');
                    $secret     = Config::get('constants.ShopeeKawanSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 9:
                    $shopid     = Config::get('constants.ShopeePokkaShopid');
                    $partner_id = Config::get('constants.ShopeePokkaPartnerID');
                    $secret     = Config::get('constants.ShopeePokkaSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 10:
                    $shopid     = Config::get('constants.ShopeeEtikaShopid');
                    $partner_id = Config::get('constants.ShopeeEtikaPartnerID');
                    $secret     = Config::get('constants.ShopeeEtikaSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 11:
                    $shopid     = Config::get('constants.ShopeeEbfrozenShopid');
                    $partner_id = Config::get('constants.ShopeeEbfrozenPartnerID');
                    $secret     = Config::get('constants.ShopeeEbfrozenSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                case 12:
                    $shopid     = Config::get('constants.ShopeeEverbestShopid');
                    $partner_id = Config::get('constants.ShopeeEverbestPartnerID');
                    $secret     = Config::get('constants.ShopeeEverbestSecret');    
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.', "create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
                default:
                    $shopid     = Config::get('constants.ShopeeJocomShopid');
                    $partner_id = Config::get('constants.ShopeeJocomPartnerID');
                    $secret     = Config::get('constants.ShopeeJocomSecret');  
                    $string     = Config::get('constants.ShopeeUrlGet').'{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $sig        = hash_hmac('sha256', $string, $secret);
                    $post       = '{"shopid": '.$shopid.',"partner_id": '.$partner_id.', "timestamp":'.$currentdate.',"create_time_from": '.$from.', "create_time_to":'.$currentdate.', "order_status":"'.$status.'", "pagination_entries_per_page":'.$pagination_entries_per_page.'}';
                    $header     = array('Content-Type: application/json', 'Authorization: '. $sig);
                    break;
            }
            
              //access token
             $timest = time();
             $partner_id = (int) $partner_id;
             $shopid= (int) $shopid;
             $code="4663786f5153527667684f4c574e645a68496f464875797457706174416f554d";
             $body = array("code" => $code,  "shop_id" => $shopid, "partner_id" => $partner_id);
             $path = "/api/v2/auth/token/get";
             $baseString = sprintf("%s%s%s", $partner_id, $path, $timest);
             $sign = hash_hmac('sha256', $baseString, $secret);
             $url = sprintf("%s%s?partner_id=%s&timestamp=%s&sign=%s",'https://partner.shopeemobile.com', $path, $partner_id, $timest, $sign);
                $c = curl_init($url);
                curl_setopt($c, CURLOPT_POST, 1);
                curl_setopt($c, CURLOPT_POSTFIELDS, json_encode($body));
                curl_setopt($c, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
                curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
                $resp = curl_exec($c);
                
                $ret = json_decode($resp, true);
                $accessToken = $ret["access_token"];
                $newRefreshToken = $ret["refresh_token"];
            print"<pre>";print_r($resp);
             exit;
            // $ch = curl_init('https://partner.shopeemobile.com/api/v2/order/get_order_list');

            // curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
            // curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            // curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            // curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
            // curl_setopt($ch, CURLOPT_TIMEOUT, 300);
            // curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

            // //execute postgetTokenShopLevel
            // $result = curl_exec($ch);
  
$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://partner.shopeemobile.com/api/v2/order/get_order_list?'.$get_param.'',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/json'
  ),
));

$results = curl_exec($curl);

curl_close($curl);


         
            $results = json_decode($result, TRUE);
             
            print_r($results);
            die('In');
            $array = array();

            if (!empty($results)) {
                foreach ($results['orders'] as $key => $value) {

                    $ordernum = $value['ordersn'];

                    array_push($array, $ordernum);
                }   
            }

            $chunked_arr = array_chunk($array,50);
            $total = array();
             
            // foreach ($chunked_arr as $key => $value) {

            //     $list = json_encode($value);

            //     $OrderCollection = $this->getOrders($listType,$list,$shopid,$partner_id,$secret);

            //     array_push($total, count($OrderCollection));

            //     if(count($OrderCollection) > 0 ){
            //         // Save new records
            //         $isSaved = $this->saveNewOrder($OrderCollection);
                    
            //     }
            // }
          
  
        }catch (Exception $ex) {
            $isError = 1;
            $message = $ex->getMessage();
        // echo $message;
        }

        finally {
            return array(
                "response" => $isError,
                "totalRecords" => array_sum($total),
                //"LatestRecord" => $OrderCollection,
                "message"=>$message,
                "charges"=>$isSaved
            );
        }

    }
    

}