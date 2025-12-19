<?php

use Illuminate\Auth\Reminders\RemindableInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\UserTrait;

class MCheckout extends Eloquent implements UserInterface, RemindableInterface
{
    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    //protected $table = 'jocom_transaction';

     public function scopeCheckout_transaction($query, $post = [])
    {
        $returnData = [
            'status'  => 'error',
            'message' => '101',
        ];
        
        // to define total or unit items bought
        $total_unit_items = 0;
        
        $tax_rate    = Fees::get_tax_percent();
        $fl_sale = 0;
        $off_sale = 0;
        
        // This is define the tax calculation is based on per item of per total amount.!                
                    
        if($post['transaction_date'] >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){                
            /* INCLUSIVE TEMPLATE */                
             $gstTotalComputeTax = true;                
        }  else{                
            /* EXCLUSIVE TEMPLATE */                
             $gstTotalComputeTax = false;               
        }          
        
        $gstTotalComputeTax = true;

        if (isset($post['qrcode']) && is_array($post['qrcode'])) {

            $urow = Customer::where('username', '=', $post['user'])->first();
            // print_r($urow);
            //valid login
            if ($urow != null) {
                $error = false;
                $accountStatus = $urow->active_status;
                $returnData['userinfo'] = array(
                    "userEmail"=>$urow->email,
                    "username"=>$urow->username,
                );

                // Get Country
                $country_row =Country::select('id','name')
                    ->where('id', '=', $post['delivery_country'])->first();

                // Get State
                $state_row = CountryState::select('*')
                            ->where('id', '=', $post['delivery_state'])
                             ->where('country_id', '=', $post['delivery_country'])->first();
                // Get Zone
                $check_zone = [];
                $city_name  = '';
                $city_id    = 0;
                $delivery_countryID = $post['delivery_country'];
                 
                 //FREESHIPPING 
                $del_state=0;
                $delv_flag=0;
                $free_del_eastwest =0;
                $free_item =0;
                
                
                $del_state = $post['delivery_state'];   
                
                if ($del_state == '458004') {
                    $free_del_eastwest=1;
                    $free_item =1;
                }
                else if ($del_state == '458013') {
                    $free_del_eastwest=1;
                    $free_item =1;
                }
                else if ($del_state == '458015') {
                    $free_del_eastwest=1;
                    $free_item =1;
                }
                else{
                    $free_del_eastwest=2;
                    $free_item =0;
                }
                
                $currentdate = date('Y-m-d');
                if($currentdate == '2023-10-10'){
                    
                    
                    if ($del_state == '458004') {
                        $delv_flag=1;
                    }
                    if ($del_state == '458013') {
                        $delv_flag=1;
                    }
                    if ($del_state == '458015') {
                        $delv_flag=1;
                    }
                  
                }
                if ($post['delivery_city'] != null or $post['delivery_city'] != '') {
                    // Get Cities
                    $city_row = City::select('id','name')
                        ->where('id', '=', $post['delivery_city'])
                        ->where('state_id', '=', $post['delivery_state'])->first();

                    if (count($city_row) > 0) {
                        $city_name = $city_row->name;
                        $city_id   = $city_row->id;
                    }

                    // Get Zone
                    $zone = DB::table('jocom_zone_cities')
                        ->select('id','zone_id')
                        ->where('city_id', '=', $post['delivery_city'])->get();
                } else {
                    // Get Cities
                    $city_row = '1';

                    // Get Zone
                    $zone = DB::table('jocom_zone_states')
                        ->select('id','zone_id')
                        ->where('states_id', '=', $post['delivery_state'])->get();
                }

                if ($zone == null) {
                    $error = true;
                    $zone_query == null;
                    $returnData['message'] = '105';
                    // $code['105'] = 'Invalid location selected.';
                } else {
                     foreach ($zone as $zone_row) {
                        $check_zone[] = $zone_row->zone_id;
                    }
                    if (sizeof($check_zone) == 0) {
                        $check_zone[] = 0;
                    }

                    $zone_query = DB::table('jocom_zones')
                        ->select('id')
                        ->where('country_id', '=', $post['delivery_country'])
                        ->whereIn('id', $check_zone)
                        ->get();
                }

                if ($country_row == null) {
                    $error                 = true;
                    $returnData['message'] = '103';
                    // $code['103'] = 'Invalid country.';
                } elseif ($state_row == null) {
                    $error                 = true;
                    $returnData['message'] = '104';
                    // $code['104'] = 'Invalid state.';
                } elseif ($city_row == null) {
                    $error                 = true;
                    $returnData['message'] = '112';
                    // $code['104'] = 'Invalid city.';
                } elseif ($zone_query == null) {
                    $error                 = true;
                    $returnData['message'] = '105';
                    // $code['105'] = 'Invalid location selected.';
                }
                if ($accountStatus != 1) { // Return error for account not activate yet
                    $error                 = true;
                    $returnData['message'] = '114';
                    // $code['114'] = 'Account not activate.';
                } 
                
                // no error on location
                if ($error === false) {
                    $buyer_zone = [];
                    foreach ($zone_query as $zone_row) {
                        $buyer_zone[] = $zone_row->id;
                    }

                    $transaction_date = ($post['transaction_date'] == "") ? date('Y-m-d H:i:s') : $post['transaction_date'];
                                        // Allow to add transaction
                    $transac_data = [
                        "transaction_date"    => $transaction_date,
                        "status"              => "pending",
                        "buyer_id"            => $urow->id,
                        "buyer_username"      => $urow->username,
                        "delivery_name"       => $post['delivery_name'],
                        "delivery_contact_no" => $post['delivery_contact_no'],
                        "special_msg"         => $post['special_msg'],
                        "third_party"         => isset($post['elevenstreetDeliveryCharges']) ? 1:0 ,
                        "third_party_lazada"  => isset($post['lazadaDeliveryCharges']) ? 1:0 ,
                        "third_party_qoo10"  => isset($post['qoo10DeliveryCharges']) ? 1:0 ,
                        "third_party_shopee"  => isset($post['shopeeDeliveryCharges']) ? 1:0 ,
                        "third_party_astrogo"  => isset($post['astrogoDeliveryCharges']) ? 1:0 ,
                        "third_party_pgmall"  => isset($post['pgmallDeliveryCharges']) ? 1:0 ,
                        "buyer_email"         => $urow->email,
                        "delivery_addr_1"     => $post['delivery_addr_1'],
                        "delivery_addr_2"     => $post['delivery_addr_2'],
                        "delivery_postcode"   => $post['delivery_postcode'],
                        "delivery_city"       => $city_name,
                        "delivery_city_id"    => $city_id,
                        "delivery_state"      => $state_row->name,
                        "delivery_state_id"   => $state_row->id,    //Added new field - 12-01-2018
                        "delivery_country"    => $country_row->name,
                        "invoice_to_address" => $post['invoice_to_address'],
                        // "delivery_charges"    => $delivery_charges,
                        "device_platform"          => $post['devicetype'],
                        "delivery_condition"  => '',
                        // "process_fees"        => $process_fees,
                        "total_amount"        => 0,
                        // "gst_rate"            => $temp_gst_rate,
                        // "gst_process"         => $temp_gst_process,
                        // "gst_delivery"        => $temp_gst_delivery,
                        // "gst_total"           => $temp_gst_process + $temp_gst_delivery,
                        "insert_by"           => $urow->username,
                        "insert_date"         => date('Y-m-d H:i:s'),
                        "modify_by"           => $urow->username,
                        "modify_date"         => date('Y-m-d H:i:s'),
                        "lang"                => $post['lang'],
                        "ip_address"          => $post['ip_address'],
                        "location"            => $post['location'],
                        'agent_id'            => object_get($urow, 'agent_id'),
                        "charity_id"          => $post['charity_id'],
                        "external_ref_number"  => $post['external_ref_number'],
                        "selected_invoice_date"  => $post['selected_invoice_date'],
                        "is_self_collect"  => $post['is_self_collect'],
                        "create_by_user"  => $post['create_by_user'],
                        
                        "delivery_identity_number"   => isset($post['delivery_identity_number']) ? $post['delivery_identity_number']:'' ,
                        'invoice_bussines_currency' => isset($post['invoice_bussines_currency']) ? $post['invoice_bussines_currency']:'' ,
                        'invoice_bussines_currency_rate' => isset($post['invoice_bussines_currency_rate']) ? $post['invoice_bussines_currency_rate']:0, 
                        'standard_currency' => isset($post['standard_currency']) ? $post['standard_currency']:'' ,
                        'standard_currency_rate' => isset($post['standard_currency_rate']) ? $post['standard_currency_rate']:0, 
                        'base_currency' => isset($post['base_currency']) ? $post['base_currency']:'' ,
                        'base_currency_rate' => isset($post['base_currency_rate']) ? $post['base_currency_rate']:0, 
                        'foreign_country_currency' => isset($post['foreign_country_currency']) ? $post['foreign_country_currency']:'' ,
                        'foreign_country_currency_rate' => isset($post['foreign_country_currency_rate']) ? $post['foreign_country_currency_rate']:0, 
                        'flash_sale_product' => array()

                    ];

                    $transac_data_detail = [];
                    $transac_data_group  = [];
                    $c_seller            = false;
                    $cback               = 0;
                    $cbacktext           = "";
                    $durian               = 0;
                    $i_eleven            = 0;
                    $is_add             = 0;
                    $is_restrict        = 0;
                    $is_minspend        = 0;
                    $is_minspend_shfee  = 0;
                    $is_minspendtotal   = 0;
                    $is_minspendtotalval   = 0;
                    $user_idres = $urow->id;
                    $CheckoutProductQRCODE = array();

foreach ($post['qrcode'] as $k => $v) {
                                               // tie a product to KKW only
                        $fix_prod  = 'JC2995'; //QR code
                        $prod_name = 'Pancake';
                        
                        array_push($CheckoutProductQRCODE, $post['qrcode'][$k]);
                        
                        if ($post['qrcode'][$k] == $fix_prod and $urow->username != 'kkwoodypavilion') {
                            $error      = true;
                            $returnData = [
                                'status'  => 'error',
                                'message' => '110',
                                'kkwprod' => $prod_name,
                            ];
                            break;
                        }
                        
                        if ($urow->delivery_contact_no == '0174378393') {
                            $error      = true;
                            $returnData = [
                                'status'  => 'error',
                                'message' => '101',
                            ];
                            break;
                        }
                        
                        if ($urow->delivery_contact_no == '60174378393') {
                            $error      = true;
                            $returnData = [
                                'status'  => 'error',
                                'message' => '101',
                            ];
                            break;
                        }
                        
                        if ($post['qrcode'][$k]  == 'JC34288')
                        {
                            $cback = 1; 
                            $cbacktext = "JC34288";                         


                        }
                        
                        if ($post['qrcode'][$k]  == 'JC37588')
                        {
                            $durian = 1; 
                           

                        }
                        
                        if ($post['qrcode'][$k]  == 'JC37836')
                        {
                            $durian = 1; 
                        }
                        
                        
                        //Amended on Nov 08 2019
                        if ($post['qrcode'][$k]  == 'JC50347' || $post['qrcode'][$k]  == 'JC50348' || $post['qrcode'][$k]  == 'JC50349' || $post['qrcode'][$k]  == 'JC50934' || $post['qrcode'][$k]  == 'JC51556' || $post['qrcode'][$k]  == 'JC51697' || $post['qrcode'][$k]  == 'JC51698' || $post['qrcode'][$k]  == 'JC51699' || $post['qrcode'][$k]  == 'JC51700' || $post['qrcode'][$k]  == 'JC51701' || $post['qrcode'][$k]  == 'JC51702') { 
                        // if ( $post['qrcode'][$k]  == 'JC48539111') {
                        // if ( $post['qrcode'][$k]  == 'JC51556') { 
                            
                            if($post['qty'][$k] > 1){
                                
                                $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                                $returnData = [
                                    'status'  => 'error',
                                    'message' => '115',
                                    'kkwprod' => $Product->name,
                                ];
                                $error           = true;
                                
                                break;
                            }
                            
                            // $lscheck = Transaction::Checkuserpurchase($user_idres,'JC4853911');
                            //  if($lscheck == 1){
                            //      $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                            //         $returnData = [
                            //             'status'  => 'error',
                            //             'message' => '121',
                            //             'kkwprod' => $Product->name,
                            //         ];
                            //         $error           = true;
                                    
                            //         break;
                            //  }
                            
                        }
                        
                        if ( $post['qrcode'][$k]  == 'JC47165' || $post['qrcode'][$k]  == 'JC47165' || $post['qrcode'][$k]  == 'JC48800' || $post['qrcode'][$k]  == 'JC48801' || $post['qrcode'][$k]  == 'JC48799' || $post['qrcode'][$k]  == 'JC48798' || $post['qrcode'][$k]  == 'JC47165') { 
                        // if ($post['qrcode'][$k]  == 'JC46500' || $post['qrcode'][$k]  == 'JC47165' || $post['qrcode'][$k]  == 'JC38917' || $post['qrcode'][$k]  == 'JC46417' || $post['qrcode'][$k]  == 'JC48136' || $post['qrcode'][$k]  == 'JC40150' || $post['qrcode'][$k]  == 'JC46974' || $post['qrcode'][$k]  == 'JC46072' || $post['qrcode'][$k]  == 'JC51008') { 
                            
                            // $lscheck = Transaction::Checkuserpurchase($user_idres,$post['qrcode'][$k]);
                            //  if($lscheck == 1){
                            //      $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                            //         $returnData = [
                            //             'status'  => 'error',
                            //             'message' => '121',
                            //             'kkwprod' => $Product->name,
                            //         ];
                            //         $error           = true;
                                    
                            //         break;
                            //  }
                             
                             
                            if($post['qty'][$k] > 2){
                                
                                $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                                $returnData = [
                                    'status'  => 'error',
                                    'message' => '119',
                                    'kkwprod' => $Product->name,
                                ];
                                $error           = true;
                                
                                break;
                            }
                            
                             
                            
                            
                        }
                        
                        // if ($post['qrcode'][$k]  == 'JC47973' || $post['qrcode'][$k]  == 'JC47977' || $post['qrcode'][$k]  == 'JC48133' || $post['qrcode'][$k]  == 'JC48134' || $post['qrcode'][$k]  == 'JC47792' || $post['qrcode'][$k]  == 'JC48139' || $post['qrcode'][$k]  == 'JC45612') { 
                        if ( $post['qrcode'][$k]  == 'JC48700' || $post['qrcode'][$k]  == 'JC43991') { 
                            
                            if($post['qty'][$k] > 3){
                                
                                $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                                $returnData = [
                                    'status'  => 'error',
                                    'message' => '120',
                                    'kkwprod' => $Product->name,
                                ];
                                $error           = true;
                                
                                break;
                            }
                            
                            $lscheck = Transaction::Checkuserpurchase($user_idres,$post['qrcode'][$k]);
                             if($lscheck == 1){
                                 $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                                    $returnData = [
                                        'status'  => 'error',
                                        'message' => '121',
                                        'kkwprod' => $Product->name,
                                    ];
                                    $error           = true;
                                    
                                    break;
                             }
                        }
                        
                        // if ( $post['qrcode'][$k]  == 'JC47556' || $post['qrcode'][$k]  == 'JC47554' || $post['qrcode'][$k]  == 'JC47555' || $post['qrcode'][$k]  == 'JC46062' || $post['qrcode'][$k]  == 'JC46499' || $post['qrcode'][$k]  == 'JC47561' || $post['qrcode'][$k]  == 'JC46072' || $post['qrcode'][$k]  == 'JC46398' || $post['qrcode'][$k]  == 'JC46417') {
                        //     $delv_flag = 0;
                        //     $is_add = 1;
                        // }
                        
                        // if($is_add == 1){
                            
                        //      $is_restrict =  $is_restrict + 1;
                        // }
                        
                        // if($is_restrict > 1) {
                        //     $delv_flag = 1;
                        // }
                        
                        // if ($post['qrcode'][$k]  == 'JC38933' || $post['qrcode'][$k]  == 'JC38932' || $post['qrcode'][$k]  == 'JC38931' || $post['qrcode'][$k]  == 'JC38930' || $post['qrcode'][$k]  == 'JC38929' || $post['qrcode'][$k]  == 'JC38928' || $post['qrcode'][$k]  == 'JC38927' || $post['qrcode'][$k]  == 'JC38926' || $post['qrcode'][$k]  == 'JC38925' || $post['qrcode'][$k]  == 'JC38924' || $post['qrcode'][$k]  == 'JC38923' || $post['qrcode'][$k]  == 'JC38922' || $post['qrcode'][$k]  == 'JC38921' || $post['qrcode'][$k]  == 'JC38920' || $post['qrcode'][$k]  == 'JC38919' || $post['qrcode'][$k]  == 'JC38918' || $post['qrcode'][$k]  == 'JC38917' || $post['qrcode'][$k]  == 'JC38916' || $post['qrcode'][$k]  == 'JC38915' || $post['qrcode'][$k]  == 'JC38914' || $post['qrcode'][$k]  == 'JC38912' || $post['qrcode'][$k]  == 'JC38911' || $post['qrcode'][$k]  == 'JC38910' || $post['qrcode'][$k]  == 'JC38909' || $post['qrcode'][$k]  == 'JC38908' || $post['qrcode'][$k]  == 'JC38907' || $post['qrcode'][$k]  == 'JC38906' || $post['qrcode'][$k]  == 'JC38905') {
                        //     $firsttimer = 0;
                        // }
                        
                        
                        if ( $post['qrcode'][$k]  == 'JC32982' ) {
                            $off_sale = 1;
                        }
                        
                        
                        if ($post['qrcode'][$k]  == 'JC48580' || $post['qrcode'][$k]  == 'JC41097' || $post['qrcode'][$k]  == 'JC41389' || $post['qrcode'][$k]  == 'JC41821' || $post['qrcode'][$k]  == 'JC41822' || $post['qrcode'][$k]  == 'JC41855' || $post['qrcode'][$k]  == 'JC41874' || $post['qrcode'][$k]  == 'JC41855' || $post['qrcode'][$k]  == 'JC41903' || $post['qrcode'][$k]  == 'JC41932' || $post['qrcode'][$k]  == 'JC41944' || $post['qrcode'][$k]  == 'JC43306' || $post['qrcode'][$k]  == 'JC43306' || $post['qrcode'][$k]  == 'JC27858' || $post['qrcode'][$k]  == 'JC27857' || $post['qrcode'][$k]  == 'JC49717' || $post['qrcode'][$k]  == 'JC37391' || $post['qrcode'][$k]  == 'JC48584' || $post['qrcode'][$k]  == 'JC48580' || $post['qrcode'][$k]  == 'JC48576' || $post['qrcode'][$k]  == 'JC43549' || $post['qrcode'][$k]  == 'JC41152' || $post['qrcode'][$k]  == 'JC39079' || $post['qrcode'][$k]  == 'JC39077' || $post['qrcode'][$k]  == 'JC39074' || $post['qrcode'][$k]  == 'JC39073' || $post['qrcode'][$k]  == 'JC39072' || $post['qrcode'][$k]  == 'JC29887' || $post['qrcode'][$k]  == 'JC29595' || $post['qrcode'][$k]  == 'JC41026' || $post['qrcode'][$k]  == 'JC43356' || $post['qrcode'][$k]  == 'JC42330' || $post['qrcode'][$k]  == 'JC41043' || $post['qrcode'][$k]  == 'JC13819') { 
                        // if ( $post['qrcode'][$k]  == 'JC48580' ) {
                           if($post['qty'][$k] > 5){
                                
                                $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                                $returnData = [
                                    'status'  => 'error',
                                    'message' => '117',
                                    'kkwprod' => $Product->name,
                                ];
                                $error           = true;
                                
                                break;
                            } 
                            
                            // $lscheck = Transaction::Checkuserpurchase($user_idres,$post['qrcode'][$k]);
                            //  if($lscheck == 1){
                            //      $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                            //         $returnData = [
                            //             'status'  => 'error',
                            //             'message' => '121',
                            //             'kkwprod' => $Product->name,
                            //         ];
                            //         $error           = true;
                                    
                            //         break;
                            //  }
                        }
                        
                        $minspendcheck = Transaction::Checkminspendvalue($post['qrcode'][$k]);
                         if($minspendcheck > 0) {
                             
                             $is_minspend = 1;
                             
                             $is_minspendtotal = Transaction::Checkminspendvaluetotal($post['qrcode'][$k]);
                             if($is_minspendtotal > 0) {
                                 
                                 $is_minspendtotalval = $is_minspendtotal;
                             } 
                             
                         }
                        
                         $qtycheck = Transaction::Checkproductrestrict($post['qrcode'][$k]);
                         if($qtycheck > 0) {
                             if($qtycheck == 5){
                                if($post['qty'][$k] > 5){
                                     $returnData = [
                                        'status'  => 'error',
                                        'message' => '117',
                                        'kkwprod' => $Product->name,
                                    ];
                                    $error           = true;
                                    
                                    break;
                                }
                             }
                             if($qtycheck == 12){
                                if($post['qty'][$k] > 12){
                                     $returnData = [
                                        'status'  => 'error',
                                        'message' => '122',
                                        'kkwprod' => $Product->name,
                                    ];
                                    $error           = true;
                                    
                                    break;
                                }
                             }
                             if($qtycheck == 3){
                                if($post['qty'][$k] > 3){
                                     $returnData = [
                                        'status'  => 'error',
                                        'message' => '120',
                                        'kkwprod' => $Product->name,
                                    ];
                                    $error           = true;
                                    
                                    break;
                                }
                             }
                             if($qtycheck == 1){
                                if($post['qty'][$k] > 1){
                                     $returnData = [
                                        'status'  => 'error',
                                        'message' => '116',
                                        'kkwprod' => $Product->name,
                                    ];
                                    $error           = true;
                                    
                                    break;
                                }
                             }
                             
                         }
                        
                        if ( $post['qrcode'][$k]  == 'JC48650' || $post['qrcode'][$k]  == 'JC48652' || $post['qrcode'][$k]  == 'JC41234' || $post['qrcode'][$k]  == 'JC48655' || $post['qrcode'][$k]  == 'JC48656' || $post['qrcode'][$k]  == 'JC48657' || $post['qrcode'][$k]  == 'JC48658' || $post['qrcode'][$k]  == 'JC48659' || $post['qrcode'][$k]  == 'JC48660' || $post['qrcode'][$k]  == 'JC48662') {
                           if($post['qty'][$k] > 4){
                                
                                $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                                $returnData = [
                                    'status'  => 'error',
                                    'message' => '118',
                                    'kkwprod' => $Product->name,
                                ];
                                $error           = true;
                                
                                break;
                            } 
                        }
                        
                        
                        if ( $post['qrcode'][$k]  == 'JC2454400') { 
                            
                            if($post['qty'][$k] >= 1){
                                
                                $Product = Product::where("qrcode",$post['qrcode'][$k])->first();
                                $returnData = [
                                    'status'  => 'error',
                                    'message' => '116',
                                    'kkwprod' => $Product->name,
                                ];
                                $error           = true;
                                
                                break;
                            } 
                            
                        }
                        
                        //11.11 Freeshipping Start....

                         $totalSKU = Product::where("status",1)
                                            ->where("qrcode",$post['qrcode'][$k])
                                            ->where('category', 'LIKE', '%1060%')->count();

                        if($totalSKU > 0){

                            $i_eleven = 1;
                        }

                        //11.11 Freeshipping ....End 
  
                        // end tie to...

                        // temporary allow a user to buy once only
                        // $cybersales      = ['JC5211']; //QR code
                        // $cybersales_name = '100 Plus Minuman Isotonik (BUY 1 FREE 1) more than one(1)';
                        // if (in_array($post['qrcode'][$k], $cybersales)) {
                        //     $buy_again = DB::table('jocom_transaction AS a')
                        //         ->select('a.id')
                        //         ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
                        //         ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
                        //         ->where('a.buyer_username', '=', $urow->username)
                        //         ->where('a.status', '=', 'completed')
                        //         ->where('c.qrcode', '=', $post['qrcode'][$k])
                        //         ->first();

                        //     if ($post['uuid'] != '')
                        //     {
                        //         $buy_again_uuid = DB::table('jocom_transaction AS a')
                        //             ->select('a.id')
                        //             ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
                        //             ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
                        //             ->leftJoin('jocom_user AS d', 'a.buyer_id', '=', 'd.id')
                        //             ->where('d.uuid', '=', $post['uuid'])
                        //             ->where('a.status', '=', 'completed')
                        //             ->where('c.qrcode', '=', $post['qrcode'][$k])
                        //             ->first();
                        //     }                            

                        //     if (count($buy_again) > 0 or count($buy_again_uuid) > 0 or $post['qty'][$k] > 1) {
                        //         $temp_cyber_name = DB::table('jocom_products')->select('name')->where('qrcode', '=', $post['qrcode'][$k])->first();
                        //         $cybersales_name = $temp_cyber_name->name.' more than one(1)';
                        //         $error           = true;
                        //         $returnData      = [
                        //             'status'  => 'error',
                        //             'message' => '110',
                        //             'kkwprod' => $cybersales_name,
                        //         ];
                        //         break;
                        //     }
                        // }
                        // end temporary allow...

                        if ($error === true) {
                            continue;
                        }
                       $platform_original_price = 0;
                        $shopee_poriginal_price = 0;
                        $lazada_poriginal_price = 0;
                        
                        $platform_price = 0;
                        $lazadapirce = 0;
                        $shopeepirce = 0;
                        $pgmallpirce = 0;
                        $qrcode       = $post['qrcode'][$k];
                        $qty          = $post['qty'][$k];
                        $price_option = $post['price_option'][$k];
                        $lazadapirce  = $post['lazadaoriginalpirce'][$k] ? $post['lazadaoriginalpirce'][$k] : 0;
                        $shopeepirce  = $post['shopee_original_price'][$k] ? $post['shopee_original_price'][$k] : 0;
                        $pgmallpirce  = $post['pgmall_original_price'][$k] ? $post['pgmall_original_price'][$k] : 0;
                        
                        $shopee_poriginal_price  = $post['shopee_platform_original_price'][$k] ? $post['shopee_platform_original_price'][$k] : 0;
                        $lazada_poriginal_price  = $post['lazada_platform_originalpirce'][$k] ? $post['lazada_platform_originalpirce'][$k] : 0;
                        
                        
                         if(is_numeric($lazadapirce) && $lazadapirce > 0) {
                            $platform_price = $lazadapirce;
                        }

                        if(is_numeric($shopeepirce) && $shopeepirce > 0) {
                            $platform_price = $shopeepirce;
                        }
                        
                        if(is_numeric($pgmallpirce) && $pgmallpirce > 0) {
                            $platform_price = $pgmallpirce;
                        }
                        
                        if(is_numeric($shopee_poriginal_price) && $shopee_poriginal_price > 0) {
                            $platform_original_price = $shopee_poriginal_price;
                        }
                        
                        if(is_numeric($lazada_poriginal_price) && $lazada_poriginal_price > 0) {
                            $platform_original_price = $lazada_poriginal_price;
                        }     
                        // valid qrcode and qty in numeric
                        if ($qrcode != '' && is_numeric($qty) && $qty > 0) {
                            $query_row =Product::select('id','name','sku','delivery_time','sell_id')
                                ->where('qrcode', '=', $qrcode)->first();

                            if ($query_row != null) {
                                // not package
                                if (substr($query_row->id, 0, 1) != 'P') {
                                     $prow            = $query_row;
                                    $tmp_return_data = MCheckout::add_transaction_detail($prow, $platform_price,$platform_original_price, $price_option, $qty, $buyer_zone, $returnData, $transac_data, $transac_data_detail, $c_seller, $error, "", $urow->username,$gstTotalComputeTax);
                                    
                                    // print_r($tmp_return_data);
                                    
                                    // echo '<pre>';
                                    // print_r($tmp_return_data["transac_data_detail"]);
                                    // echo '</pre>';
                                    
                                    $returnData          = $tmp_return_data["returnData"];
                                    $transac_data        = $tmp_return_data["transac_data"];
                                    $transac_data_detail = $tmp_return_data["transac_data_detail"];
                                    $error               = $tmp_return_data["error"];
                                    $c_seller            = $tmp_return_data["c_seller"];
                                    
                                    // Sum up total items
                                    $total_unit_items =  $total_unit_items + $returnData['item_quantity'];
                                    
                                } else {
                                    // Get Package Products
                                    $get_pro_query = DB::table('jocom_product_package_product')
                                        ->select('product_opt','qty')
                                        ->where('package_id', '=', substr($query_row->id, 1))->get();

                                    // for each package
                                    foreach ($get_pro_query as $get_pro_row) {
                                        $price_option = $get_pro_row->product_opt;
                                        $pro_qty      = $get_pro_row->qty * $qty;

                                        $popt_row = DB::table('jocom_product_price')->select('product_id')->find($get_pro_row->product_opt);

                                        // with price
                                        if ($popt_row != null) {
                                            $prow = Product::select('id','name','sku','delivery_time','sell_id')->find($popt_row->product_id);

                                            if ($prow != null) {
                                                $tmp_return_data = MCheckout::add_transaction_detail($prow, $platform_price,$platform_original_price, $price_option, $pro_qty, $buyer_zone, $returnData, $transac_data, $transac_data_detail, $c_seller, $error, $query_row->sku, "",$gstTotalComputeTax);
                                                
                                                $returnData          = $tmp_return_data["returnData"];
                                                $transac_data        = $tmp_return_data["transac_data"];
                                                $transac_data_detail = $tmp_return_data["transac_data_detail"];
                                                $error               = $tmp_return_data["error"];
                                                $c_seller            = $tmp_return_data["c_seller"];
                                                // Sum up total items
                                                $total_unit_items =  $total_unit_items + $returnData['item_quantity'];
                                    
                                            } else {
                                                $error = true;
                                                // Error on product transaction
                                                $returnData['message'] = '106';
                                            }
                                        } else {
                                            $error = true;
                                            // Error on product transaction
                                            $returnData['message'] = '106';
                                        } //end for with price

                                    } // end of for each package

                                    if (isset($transac_data_group[$query_row->sku])) {
                                        $transac_data_group[$query_row->sku]["unit"] += $qty;
                                    } else {
                                        $transac_data_group[$query_row->sku] = [
                                            "sku"  => $query_row->sku,
                                            "unit" => $qty,
                                        ];
                                    }
                                } // end not package
                            } else {
                                $error = true;
                                // Error on product transaction
                                $returnData['message'] = '106';
                            }
                        } // end of valid qrcode and qty in numeric

                    } // end of each product with qrcode
               
                    // to check special pricing meet minimum purchase requirement.
                    $group_total = [];
                    $total_check = 0;
                    foreach ($transac_data_detail as $key => $trow) {
                        
                        //echo "<pre>";
                        //print_r($trow);
                        //echo "</pre>";
                        $total_check = $total_check + $trow['total'];
                        if ($trow['sp_group_id'] != 0) {
                            if ( ! isset($group_total[$trow['sp_group_id']])) {
                                $group_total[$trow['sp_group_id']] = 0;
                            }
                            $group_total[$trow['sp_group_id']] += $trow['total'];
                        }
                    }

                    foreach ($group_total as $key => $value) {
                        $groupmin = DB::table('jocom_sp_group')
                            ->select('min_purchase')
                            ->where('id', '=', $key)
                            ->first();
                            
                        //print_r($groupmin);
                        //print_r($group_total);

                        if ($group_total[$key] < $groupmin->min_purchase) {
                            $error                 = true;
                            $returnData['message'] = '111';
                            // $code['111'] = 'Oops, you do not meet the minimum purchase requirement for special pricing.';
                        }
                    }
                    
                    if ($is_minspend == 1) {
                        //  if($delv_flag == 1){
                            
                            if($total_check < $is_minspendtotalval)
                            {
                                $is_minspend_shfee = 2;
                                $delv_flag = 0;
                            }
                            else {
                                $is_minspend_shfee = 1;
                            }
                        //  } 
                    }

                } // end of no error on location
                // echo $error .'In';
                
                // no error on product, proceed to checkout
                if ($error === false) {
                    // $transac_data['total_amount'] = $transac_data['total_amount'] + $transac_data['process_fees'];
                    $involved_seller = [];
                    foreach ($transac_data_detail as $ddatarow) {
                        $involved_seller[] = $ddatarow["seller_username"];
                    }

                    $sellerData = [];
                    $seller     = DB::table('jocom_seller')
                        ->select('username')
                        ->whereIn('username', $involved_seller)
                        ->get();
                    foreach ($seller as $jseller_row) {
                        $sellerData[$jseller_row->username] = $jseller_row;
                    }


                    // Calculate delivery fees CMS 2.9.0
                    $temp_weight = array();
                    $temp_zone = [];

                    $delivery_charges = Fees::GetTotalDelivery($transac_data_detail);

                    $process_fees     = Fees::get_process_fees();

                    $temp_gst_process  = 0;
                    $temp_gst_delivery = 0;
                    $temp_gst_rate     = 0;

                    $gst_status = Fees::get_gst_status();
                    if ($gst_status == '1') {
                        $temp_gst_rate     = Fees::get_gst();
                        $temp_gst_process  = round(($process_fees * $temp_gst_rate / 100), 2);
                        $temp_gst_delivery = round(($delivery_charges * $temp_gst_rate / 100), 2);
                    }

                    if($transac_data['invoice_bussines_currency'] == 'USD'){
                        
                        $LocalExchangeRate = ExchangeRate::getExchangeRate('USD', 'MYR');
                        $local_delivery_charges = $delivery_charges * $LocalExchangeRate->amount_to;
                        $transac_data['delivery_charges']   = $local_delivery_charges;
                        $transac_data['foreign_delivery_charges']   = $delivery_charges;
                    }else{
                        $transac_data['delivery_charges']   = $delivery_charges;
                    }
                    
                    $transac_data['process_fees']       = $process_fees;
                    $transac_data['gst_rate']           = $temp_gst_rate;
                    $transac_data['gst_process']        = $temp_gst_process;
                    $transac_data['gst_delivery']       = $temp_gst_delivery;
                    $transac_data['gst_total']          += $temp_gst_process + $temp_gst_delivery;
                  
                    $transac_data['delivery_condition'] = "Delivery fees is set in the item";

                    // End of Calculate delivery fees CMS 2.9.0
                    
                    
                    

                    // $transac_data['delivery_condition'] = "";
                    // /*
                    // * Calculation for the delivery fees (for multiple items)
                    // * If all items delivery fees is 0 then the delivery fees will be 0
                    // * If all of the items delivery fees is same then it will be charges it same charges
                    // * If one of the items delivery fees is not 0 then it will be charges standard charges "RM 10"
                    // */

                    // // for 0 delivery fees
                    // $total_delivery                     = 0;
                    // $special_cust                       = 0;
                    // $transac_data['delivery_condition'] = "Delivery fees is set 0";
                    // foreach ($transac_data_detail as $k => $drow) {
                    //     if ($drow["delivery_fees"] != 0) {
                    //         $total_delivery += $drow["delivery_fees"];
                    //         $transac_data['delivery_condition'] = "Delivery fees is set in the item";
                    //     }

                    //     // free delivery for special pricing, temporary disable special cust waiver
                    //     // if ($drow["sp_group_id"] != 0) {
                    //     //     $special_cust++;
                    //     // }

                    // }

                    $zeroDelivery = 0;
                    $zone_valid = 0;
                    $firsttimer = 0;

                    // if ($total_delivery == 0 or $special_cust > 0) {
                    // if ($special_cust > 0) {
                    //     $delivery_charges             = 0;
                    //     $transac_data['gst_delivery'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_delivery;

                    //     $process_fees                = 0;
                    //     $transac_data['gst_process'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_process;

                    //     $zeroDelivery = 1;
                    // }
                    $zone_valid = DB::table('jocom_zone_states')    
                        ->select('*')   
                        ->where('states_id', '=', $post['delivery_state'])  
                        ->where('zone_id', '=', 9)->get();  
                    if(count($zone_valid) > 0){ 
                        $zone_valid = 1;    
                    }  


                    //  Delivery Fees RM5 until 31/12/2016
                    // first time buyer free shipping fees with minimum purchase of RM30 until 31/12/2016

                    $buybefore = Transaction::select('id')->where('buyer_username', '=', $urow->username)
                        ->where(function ($query) { 
                            $query->where('status', '=', 'completed');  
                                // ->orWhere('status', '=', 'refund');  
                        })
                         ->first();
                        
                        
                    // Get special customer info 
                    $sp_customer = DB::table('jocom_sp_customer AS JSC')
                        ->select('is_free_delivery_min_qty','min_qty_purchase')
                        ->leftJoin('jocom_sp_customer_group AS JSCG', 'JSCG.sp_cust_id', '=', 'JSC.id')
                        ->leftJoin('jocom_sp_group AS JSG', 'JSG.id', '=', 'JSCG.sp_group_id')
                        ->where('JSC.user_id', '=', $urow->id)
                        ->first();
                    
                    // valid special customer will get free delivery charges when item purchased more than min qty.
                  
                    if( (count($sp_customer) > 0) && ($sp_customer->is_free_delivery_min_qty = 1) && ($total_unit_items >= $sp_customer->min_qty_purchase )){
                        $sp_valid_free_delivery = true;
                    }else{
                        $sp_valid_free_delivery = false;
                    }
                    
                    
                    // Free Delivery for new customer and purchase more than RM80
                    
                    if ((count($buybefore) <= 0 && $transac_data['total_amount'] >= 120 && $zone_valid == 0 and $zeroDelivery == 0) || ($sp_valid_free_delivery) )
                    {
                        // $delivery_charges             = 6.50;
                        // $transac_data['gst_delivery'] = 0;
                        // $transac_data['gst_total'] -= $temp_gst_delivery;

                        $process_fees                = 0;
                        $transac_data['gst_process'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_process;

                        $zeroDelivery = 1;
                    }
                    if (count($buybefore) <= 0 && $transac_data['total_amount'] >= 20)
                    {
                        $firsttimer = 0;
                    }
                    
                    if ($transac_data['total_amount'] >= 80000)
                    {
                        $zeroDelivery = 0;
                    }
                   
                    // end of first time buyer no processing and shipping fees
                    
                     //  Delivery Fees Waiver if customer order items above RM 80 
                    if ($total_delivery == 0 and $transac_data['total_amount'] >= 8000 and $zeroDelivery == 0 and $zone_valid != 1)
                    {
                        $delivery_charges             = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;

                        // $process_fees                = 0;
                        // $transac_data['gst_process'] = 0;
                        // $transac_data['gst_total'] -= $temp_gst_process;

                        $zeroDelivery = 1;
                    }
                    
                    //  Delivery Fees Waiver if Outstation customer order items above RM 150 
                    //  if ($total_delivery == 0 and $transac_data['total_amount'] >= 88 and $zeroDelivery == 0 and $free_del_eastwest == 1)
                    if ($transac_data['total_amount'] >= 120 and $free_del_eastwest == 1)
                    {
                        $delivery_charges             = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;

                        // $process_fees                = 0;
                        // $transac_data['gst_process'] = 0;
                        // $transac_data['gst_total'] -= $temp_gst_process;

                        // $zeroDelivery = 1;
                        $free_del_eastwest = 3;
                    }
                    
                    // //  Delivery Fees Waiver until 31/10/2016
                    if ($total_delivery == 0 and $transac_data['total_amount'] >= 200 and $zeroDelivery == 0 and $free_del_eastwest == 2)
                    {
                        $delivery_charges             = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;

                        // $process_fees                = 0;
                        // $transac_data['gst_process'] = 0;
                        // $transac_data['gst_total'] -= $temp_gst_process;

                        // $zeroDelivery = 1;
                        $free_del_eastwest = 3;
                    }

                    // first time buyer no processing and shipping fees with minimum purchase of RM30 - temporary set to RM100 until 10/04/2016
                    // $buybefore = Transaction::where('buyer_username', '=', $urow->username)
                    //     ->where(function ($query) {
                    //         $query->where('status', '=', 'completed')
                    //             ->orWhere('status', '=', 'refund');
                    //     })
                    //     ->first();

                    // if (count($buybefore) <= 0 and $transac_data['total_amount'] >= 1000000 and $zeroDelivery == 0)
                    // {
                    //     $delivery_charges             = 0;
                    //     $transac_data['gst_delivery'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_delivery;

                    //     $process_fees                = 0;
                    //     $transac_data['gst_process'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_process;

                    //     $zeroDelivery = 1;
                    // }
                    // end of first time buyer no processing and shipping fees

                    // if(sizeof($transac_data_detail) > 1) {
                    //     $delivery_fees_check = 0;
                    //     $transac_data['delivery_condition'] = "Delivery fees is set 0";
                    //     foreach($transac_data_detail as $k => $drow) {
                    //         if($drow["delivery_fees"] != 0) {
                    //             if($delivery_fees_check == 0) {
                    //                 // If all of the items delivery fees is same then it will be charges it same charges
                    //                 $delivery_fees_check = $drow["delivery_fees"];
                    //                 $transac_data['delivery_condition'] = "All items delivery fees is same";
                    //             } else if($drow["delivery_fees"] != $delivery_fees_check) {
                    //                 // If one of the items delivery fees is not 0 then it will be charges standard charges "RM 10"
                    //                 $delivery_fees_check = $delivery_charges;
                    //                 $transac_data['delivery_condition'] = "One or more items delivery fees is not same";
                    //             }
                    //         }
                    //     }
                    // } else {
                    //     $delivery_fees_check = $transac_data_detail[0]["delivery_fees"];
                    //     $transac_data['delivery_condition'] = "Delivery fees is set in the item";
                    // }
                    /** FREE DELIVERY AND PROCESSING FOR MYCYBERSALE2016 WEB CHECKOUT **/
                 
                if($post['isDelivery'] == "1"){

                        $delivery_charges =10;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
               
                }
                if($fl_sale == 1){
                     $delivery_charges = 8.50;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;     
                    
                }
                
                if($off_sale == 1){
                     $delivery_charges = 10;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;     
                    
                }
                
                if($firsttimer == 1){
                     $delivery_charges = 0;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;     
                    
                }
                
                if($durian == 1){
                        $delivery_charges = 0;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;     
                    
                }
                
                if($free_del_eastwest == 3){
                        $delivery_charges = 0;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;  
                        
                }
                
                if($i_eleven == 1){

                        $delivery_charges = 0;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                }
                if($is_minspend_shfee == 1){

                        $delivery_charges = 0;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                }
                
                if($is_minspend_shfee == 2){

                        $delivery_charges = 10;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                }
                
                // echo $transac_data['total_amount'];
                   
                // Freeshipping $delv_flag
                
                    // if($total_check < 50){
                    //     $delv_flag = 0;
                    // }
                    
                    if($delv_flag == 1){
                            $delivery_charges = 0;
                            $process_fees = 0;
                            $transac_data['gst_delivery'] = 0;
                            $transac_data['gst_total'] -= $temp_gst_delivery;
                            $transac_data['delivery_charges'] = $delivery_charges;
                    }
                
                /** FREE DELIVERY AND PROCESSINF FOR MYCYBERSALE2016 WEB CHECKOUT **/
                
           
                if(isset($post['elevenstreetDeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['elevenstreetDeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['elevenstreetDeliveryCharges'] * $tax_rate ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                        
                    }
                    
                    // Lazada Delivery Charges
                    if(isset($post['lazadaDeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['lazadaDeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['lazadaDeliveryCharges'] * $tax_rate ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                        
                    }
                    
                    //Qoo10 Delivery Charges

                    if(isset($post['qoo10DeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['qoo10DeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['qoo10DeliveryCharges'] * $tax_rate ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                        
                    }
                    
                    //Shopee Delivery Charges
                    
                    if(isset($post['shopeeDeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['shopeeDeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['shopeeDeliveryCharges'] * $tax_rate ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                        
                    }

                    // PGMall Delivery Charges
                    // Nadzri - Add PGMall (23/03/2022)
                    if(isset($post['pgmallDeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['pgmallDeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['pgmallDeliveryCharges'] * $tax_rate ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                        
                    }
                    
                    if (!empty($post['delivery_charges'])) {
        
                        $delivery_charges = number_format($post['delivery_charges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['delivery_charges'] * $tax_rate ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        $transac_data['delivery_charges'] = $delivery_charges;
                    }
                    
                    $transac_data['delivery_charges'] = $delivery_charges;

                    $transac_data['process_fees']     = $process_fees;
                    $transac_data['total_amount'] += $transac_data['delivery_charges'] + $transac_data['process_fees'];
                    
                    if($transac_data['invoice_bussines_currency'] == 'USD'){
                        $transac_data['foreign_total_amount'] += $transac_data['foreign_delivery_charges'] ;
                    }
                    
                   

                    // $insert_data = array();
                    // foreach($transac_data as $key => $value) {
                    //     $insert_data['`' . $key . '`'] = $value;
                    // }
                    // Remove Flag 11Street and Lazada
                    
                    unset ($transac_data['third_party']);
                    unset ($transac_data['third_party_lazada']);
                    unset ($transac_data['third_party_qoo10']);
                    unset ($transac_data['third_party_shopee']);
                    unset ($transac_data['third_party_astrogo']);
                    unset ($transac_data['third_party_pgmall']); // Nadzri - Add PGMall (23/03/2022)
                    
                    
                    if($delivery_countryID == 156){
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] = 0;
                        //$transac_data['delivery_charges'] = 35.00;
                        
                    }
                    
                    $invoice_to_address = $transac_data['invoice_to_address'];
                    $flash_sale_products = $transac_data['flash_sale_product'];
                    unset($transac_data['invoice_to_address']);
                    unset($transac_data['flash_sale_product']);
                    
                    $insert_id = DB::table('jocom_transaction')->insertGetId($transac_data);
                    
                    TransactionInvoiceAddress::saveAddress($insert_id , $invoice_to_address);
                    
                    $collection = DB::table('jocom_keywords')->where('type', '=', 'office')->get(['title']);
                    
                    foreach ($collection as $keyword) {
                        
                        $DA1_Where_Like[] = "delivery_addr_1 LIKE '%$keyword->title%'";
                        
                        $DA2_Where_Like[] = "delivery_addr_2 LIKE '%$keyword->title%'";

                    }
                    $DA1WhereLike = implode(" OR ", $DA1_Where_Like);
                    
                    $DA2WhereLike = implode(" OR ", $DA2_Where_Like);
                    
                    // Debugging / Enable query log
                    // \DB::connection()->enableQueryLog();
                    // $query = DB::select("SELECT delivery_addr_1 as a1,  delivery_addr_2 as a2, IF(($DA1WhereLike OR $DA2WhereLike), 'office', 'house') as delivery_area_type FROM jocom_transaction WHERE id = 5393");
                    // $queries = \DB::getQueryLog();
                    
                    $query = DB::update("UPDATE jocom_transaction SET delivery_area_type = IF(($DA1WhereLike OR $DA2WhereLike), 'office', 'house') WHERE id=".$insert_id);
                    
                    // Area Type Ends ----------------
                    
                    if($post['devicetype'] === 'wavpay'){
                        $result = WavpayController::anyInitial([
                            'user' => $urow,
                            'TID' => $insert_id,
                            'amount' => $transac_data['total_amount'],
                            'currency' => $transac_data['base_currency'],
                            'Wavpay_SID' => Input::get('WavPaySID'), // Session ID
                            'Wavpay_UID' => Input::get('WavPayUID'), // User ID
                        ]);
                        $ext_ref = $result['data']['gatewayReference'];
                        $query = DB::update("UPDATE jocom_transaction SET external_ref_number = '$ext_ref' WHERE id = $insert_id");
                    }
                    // print_r($transac_data_detail);
                    foreach ($transac_data_detail as $drow) {
                        $drow["transaction_id"] = $insert_id;

                        $insert_id_details = DB::table('jocom_transaction_details')->insertGetId($drow);
                    }
                    // echo '<pre>';
                    // print_r($insert_id_details);
                    // echo '</pre>';
                    // die('L1');

                    foreach ($transac_data_group as $tdgrow) {
                        $tdgrow["transaction_id"] = $insert_id;

                        $insert_id_d_g = DB::table('jocom_transaction_details_group')->insertGetId($tdgrow);
                    }

                    foreach($flash_sale_products as $product){
                        $item = array(
                            "transaction_id" => $insert_id,
                            "option_id" => $product["label_id"],
                            "flash_sales_id" => $product["flash_sales_id"],
                            "quantity" => $product["quantity"],
                            "created_at" => date("Y-m-d h:i:s")
                        );
                        
                        $insert_id_d_fs = DB::table('jocom_flashsale_transaction_product')->insertGetId($item);
                        
                    }
                    // Black Friday 24/11/2023
                    $currentdate_1 = date('Y-m-d');
                    if($currentdate_1 == '2023-11-30'){
                    if(!in_array(strtolower($urow->username),array("lazada","shopee","pgmall","tiktokshop","lamboplace","srewardscentre"))){
                            $sum_price    = TDetails::where('transaction_id', '=', $insert_id)->sum('total');
                            $sum_purchase = number_format($sum_price, 2);
                            
                            if($sum_purchase>=70 && $sum_purchase<=149){
                                 $lscheck = MCheckout::Couponcodetemp($insert_id,'BLACKFRIDAY5');
                            }else if($sum_purchase>=150){
                                 $lscheck = MCheckout::Couponcodetemp($insert_id,'BLACKFRIDAY10');
                            }
                        }
                    }
                     // Black Friday 30/11/2023   
                    if($urow->username =='maruthu' || $urow->username =='wenyi121500'){
                        
                        
                        
                    $currentdate = date('Y-m-d');
                       if($currentdate == '2023-10-13'){
                        if(!in_array(strtolower($urow->username),array("lazada","shopee","pgmall","tiktokshop","lamboplace","srewardscentre"))){
                            if($free_item == 1){
                                 $lscheck = MCheckout::FreeItemFoc($insert_id);
                            }
                           
                        }
                       }
                    }
                    
                    // TEMP code for coupon insertion - 14 June 2019  - End
                    
                    //Start JCashback
                    $cashbackflag = 0;
                    if($cback == 1) {

                        $cashrev = JCashBack::where('user_id',$urow->id)
                                            ->where('qrcode',$cbacktext)
                                            ->where('status',1)
                                            ->count();

                        if($cashrev < 5)
                        {
                            $temp_cashqry = DB::table('jocom_products')->where('qrcode','=',$cbacktext)->first(); 
                            if(count($temp_cashqry) > 0){

                                $cashdata = array(
                                    "transaction_id"    => $insert_id, 
                                    "user_id"           => $urow->id, 
                                    "qrcode"            => $cbacktext, 
                                    "sku"               => $temp_cashqry->sku, 
                                    "product_name"      => $temp_cashqry->name, 
                                    "jcash_point"       => 800,
                                    "jcash_point_used"  => 0,
                                    "created_by"        => $urow->username ? $urow->username:'API_UPDATE', 
                                    "created_at"        => date("Y-m-d h:i:sa"), 
                                    "updated_by"        => $urow->username ? $urow->username:'API_UPDATE', 
                                    "updated_at"        => date("Y-m-d h:i:sa") 
                                ); 
                                $insert_id_cash = DB::table('jocom_transaction_jcashback')->insertGetId($cashdata);

                            }

                            

                            $cashbackflag = 1;

                        }
                    }

                    //End JCashback 
                    if($post['devicetype'] === 'wavpay') {
                        $returnData['Wavpay'] = WavpayController::$result;
                    }

                    $returnData['transaction_id'] = $insert_id;
                    $returnData['status']         = 'success';
                    $returnData['message']        = 'valid';
                    $returnData['devicetype']     = $post['devicetype'];
                    $returnData['lang']           = $post['lang'];
                    $returnData['cashbackflag']   = $cashbackflag;
                    $returnData['cashbacktext']   = $cbacktext;

                } // end of no error on product, proceed to checkout

            } else {
                // Invalid Buyer
                $returnData['message'] = '102';
            } //end valid login 
        } //end if with qrcode

        return $returnData;
    }
    /**
     * Add details to transaction
     * @param  [type]  $query               [description]
     * @param  [type]  $prow                [description]
     * @param  [type]  $price_option        [description]
     * @param  [type]  $qty                 [description]
     * @param  [type]  $buyer_zone          [description]
     * @param  array   $returnData          [description]
     * @param  array   $transac_data        [description]
     * @param  array   $transac_data_detail [description]
     * @param  boolean $c_seller            [description]
     * @param  boolean $error               [description]
     * @param  string  $type                [description]
     * @return [type]                       [description]
     */
    public function scopeAdd_transaction_detail($query, $prow, $platform_price, $platform_original_price, $price_option, $qty, $buyer_zone, $returnData = [], $transac_data = [], $transac_data_detail = [], $c_seller = false, $error = false, $type = "", $buyer = "",$gstTotalComputeTax= false)
    {
       
        
        $sp_ind     = 0;
        $sp_price   = 0;
        $sp_cus_grp = 0;

        $zone_id        = 0;
        $p_weight       = 0;
        $total_weight   = 0;
        $flash_sale = true;
        $execorner_sale = true;
        
        // TEMP Added for SP Customer 
        $spGroup = DB::table('jocom_user AS user')
                    ->select('group.sp_group_id')
                    ->leftJoin('jocom_sp_customer AS customer', 'user.id', '=', 'customer.user_id')
                    ->leftJoin('jocom_sp_customer_group AS group', 'customer.id', '=', 'group.sp_cust_id')
                    ->where('user.username', '=', $buyer)
                    ->first();
        $sp_cus_grp = $spGroup->sp_group_id;
         // TEMP Added for SP Customer 
        //print_r($prow);
        if (strpos($price_option, 'FS') == "FS" || strpos($price_option, 'EC') == "EC" || strpos($price_option, 'CD') == "CD" || strpos($price_option, 'DY') == "DY") {
             
            if(strpos($price_option, 'FS') == "FS"){
            $optionName = substr($price_option, ($pos = strpos($price_option, 'FS')) !== false ? $pos + 2 : 0);
            }
            if(strpos($price_option, 'EC') == "EC"){
            $optionName = substr($price_option, ($pos = strpos($price_option, 'EC')) !== false ? $pos + 2 : 0);
            }
            if(strpos($price_option, 'CD') == "CD"){
            $optionName = substr($price_option, ($pos = strpos($price_option, 'CD')) !== false ? $pos + 2 : 0);
            }
            if(strpos($price_option, 'DY') == "DY"){
            $optionName = substr($price_option, ($pos = strpos($price_option, 'DY')) !== false ? $pos + 2 : 0);
            }
            
            $arr = explode("[", $optionName, 2);
            $fid = $arr[0];
            if(strpos($price_option, 'FS') == "FS"){
                $optionName2 = substr($price_option, ($pos = strpos($price_option, 'FS')) !== false ? $pos + 3 : 0);
            }
            if(strpos($price_option, 'EC') == "EC"){
                $optionName2 = substr($price_option, ($pos = strpos($price_option, 'EC')) !== false ? $pos + 3 : 0);
            }
            if(strpos($price_option, 'CD') == "CD"){
                $optionName2 = substr($price_option, ($pos = strpos($price_option, 'CD')) !== false ? $pos + 3 : 0);
            }
            if(strpos($price_option, 'DY') == "DY"){
                $optionName2 = substr($price_option, ($pos = strpos($price_option, 'DY')) !== false ? $pos + 3 : 0);
            }
            $option_id = explode("]", $arr[1]);
            $option_id = $option_id[0];

            if(strpos($price_option, 'FS') == "FS"){
            $products = DB::table('jocom_flashsale_products')->where('id','=',$fid)->first();
            }
            if(strpos($price_option, 'EC') == "EC"){
            $products = DB::table('jocom_jocomexcorner_products')->where('id','=',$fid)->first();
            }
            if(strpos($price_option, 'CD') == "CD"){
            $products = DB::table('jocom_combodeals_products')->where('id','=',$fid)->first();
            }
            if(strpos($price_option, 'DY') == "DY"){
            $products = DB::table('jocom_dynamicsale_products')->where('id','=',$fid)->first();
            }
            $flash_sales_id = $products->fid;
            
            $price_row = DB::table('jocom_product_price AS a')
                ->select('a.*', 'b.gst', 'b.gst_value')
                ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                ->where('b.status', '=', 1)
                ->where('a.id', '=', $option_id)
                ->where('a.product_id', '=', $products->product_id)
                ->first();

            $fs_price = $products->promo_price;

            $price_row->price_promo = $fs_price;
            if(strpos($price_option, 'FS') == "FS"){
                $f_stock =FlashSaleController::check_flashsales_stock($flash_sales_id, $option_id, $qty);
            }
            if(strpos($price_option, 'EC') == "EC"){
            $f_stock =JocomExcCornerController::check_flashsales_stock($flash_sales_id, $option_id, $qty);
            }
            if(strpos($price_option, 'CD') == "CD"){
                $f_stock = JocomComboDealsController::check_flashsales_stock($flash_sales_id, $option_id, $qty);
            }
            if(strpos($price_option, 'DY') == "DY"){
                $f_stock = DynamicSaleController::check_flashsales_stock($flash_sales_id, $option_id, $qty);
            }
            
            if(!$f_stock){              
                $returnData['message'] = '111';
                $flash_sale = false;
            }else{
               
                $transac_data['flash_sale_product'][] = array(
                    "flash_sales_id" => $fid,
                    "label_id" => $option_id,
                    "quantity" => $qty
                );
            };
            
            // if(!$JocomExcCornerController->check_flashsales_stock($flash_sales_id,$option_id,$qty)){
              
            //     $returnData['message'] = '111';
            //     $execorner_sale = false;
            // }else{
               
            //     $transac_data['execorner_sale_product'][] = array(
            //         "execorner_sales_id" => $fid,
            //         "label_id" => $option_id,
            //         "quantity" => $qty
            //     );
            // };
        
        }elseif (substr($price_option, 0, 4) == 'SPCL' || $sp_cus_grp > 0) {
            // Get Special Product Price
            // $price_id = substr($price_option,  4);
            $price_id = $price_option;
    
            $price_row = DB::table('jocom_sp_product_price AS a')
                ->select('a.label_id AS id', 'a.sp_group_id', 'b.label', 'b.label_cn', 'b.label_my', 'b.seller_sku', 'b.p_weight', 'a.price', 'a.price_promo', 'a.qty', 'a.p_referral_fees', 'a.p_referral_fees_type','a.disc_amount', 'a.disc_type', 'a.default', 'a.product_id', 'a.status', 'c.gst', 'c.gst_value')
                ->leftjoin('jocom_product_price AS b', 'b.id', '=', 'a.label_id')
                ->leftJoin('jocom_products AS c', 'a.product_id', '=', 'c.id')
                ->where('c.status', '=', 1)
                ->where('a.id', '=', $price_id)
                ->where('a.product_id', '=', $prow->id)
                ->first();
                
                //   echo "<pre>";
                //print_r($price_row);
               // echo "</pre>";
           
            if (count($price_row) <= 0) {
                // Get Public Product Price
                $price_row = DB::table('jocom_product_price AS a')
                    ->select('a.*', 'b.gst', 'b.gst_value')
                    ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                    ->where('b.status', '=', 1)
                    ->where('a.id', '=', $price_id)
                    ->where('a.product_id', '=', $prow->id)
                    ->first();
            }
            else
            {
                $sp_ind = $price_row->sp_group_id;
                $price_row->qty = $qty+1;
                
                switch ($price_row->disc_type) {
                    case '%' : 
                            $discount = 1 - ($price_row->disc_amount/100);
                            $sp_price = number_format($price_row->price * $discount, 2);    
                        break;

                    case 'N' :
                            $sp_price = number_format($price_row->price - $price_row->disc_amount, 2);
                        break;
                    default :
                        $sp_price = $price_row->price;
                        break;
                }
                
                $price_row->price = $sp_price;
            }

        }else {
            
            if( in_array($transac_data['device_platform'], array("ios","android"))){
                // Get Public Product Price
                $price_row = DB::table('jocom_product_price AS a')
                ->select('a.*', 'b.gst', 'b.gst_value')
                ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                ->where('b.status', '=',1)
                ->where('a.id', '=', $price_option)
                ->where('a.product_id', '=', $prow->id)
                ->first();
            }else{
                // Get Public Product Price
                $price_row = DB::table('jocom_product_price AS a')
                ->select('a.*', 'b.gst', 'b.gst_value')
                ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                ->where('a.id', '=', $price_option)
                ->where('a.product_id', '=', $prow->id)
                ->first();
                
            }
          
                
        }

        /*
        * Open : Overide Price If Business in Foreign Currecy
        */
        if($transac_data['invoice_bussines_currency'] == 'USD' ){
            
            $price_row->price = CurrencyController::getRate($transac_data['invoice_bussines_currency'], 'MYR',$price_row->foreign_price);
            $price_row->price_promo = CurrencyController::getRate($transac_data['invoice_bussines_currency'], 'MYR',$price_row->foreign_price_promo);
            $foreign_currency = $transac_data['invoice_bussines_currency'];
        }  else{
            $foreign_currency = '';
        }  
        /*
         * Close : Overide Price If Business in Foreign Currency
         */
         
        $cat1 = DB::table('jocom_categories')
            ->select('jocom_categories.category_id AS category_1')
            ->where('product_id', '=', $prow->id)
            ->where('jocom_categories.main', '=', '1')
           
            ->first();
            // dd($cat1);


        $cat2 = DB::table('jocom_categories')
            ->select('jocom_categories.category_id AS category_2'  )
            ->where('product_id', '=', $prow->id)
            ->where('jocom_categories.main', '=', '0')
           
            ->first();
                 

        $cat3 = DB::table('jocom_categories')
            ->select(DB::raw("group_concat(distinct jocom_categories.category_id separator ', ') AS category_3") )
            ->where('product_id', '=', $prow->id)
             ->first();
                        
          

        // Get Product Delivery Fees
        $dl_row = DB::table('jocom_product_delivery')
            ->select('price','zone_id')
            ->where('product_id', '=', $prow->id)
            ->whereIn('zone_id', $buyer_zone)
            ->first();

        // with product price and delivery fees
        if ($dl_row != null && $price_row != null && $flash_sale) {
            $dl_fees = $dl_row->price;

            $zone_id        = $dl_row->zone_id;

            $p_weight = $price_row->p_weight;
            $total_weight   = $p_weight * $qty;

            
            
            // DEFINE SELLER //
            
            $delivery_city_id = $transac_data['delivery_city_id'];
            
            $City = City::select('state_id')->find($delivery_city_id);
            $StateID = $City->state_id;
            
            // Delivery Region ID 
            $DeliveryState = State::select('region_id')->find($StateID);
            $DeliveryRegionID = $DeliveryState->region_id;

            
            $ProductSellerDefault = ProductSeller::select('seller_id')->where("product_id",$prow->id)->where("activation",1)->first();
            $ProductSeller = ProductSeller::getProductSeller($prow->id);

            $sellerId = $ProductSellerDefault->seller_id;
                    
            foreach($ProductSeller as $key => $value) {
                // $Seller = Seller::find($value->seller_id);
                // if($Seller->state == $StateID){
                //     $sellerId = $Seller->id;
                // }
                if($value->region_id == $DeliveryRegionID){
                    $sellerId = $value->seller_id;
                }
            }
            
            $srow = DB::table('jocom_seller')
                ->select('id','username','gst_reg_num','parent_seller')
                ->where('id', '=', $sellerId)
                ->first();

            // DEFINE SELLER //

            $seller = isset($srow->username) ? $srow->username : $sellerId;
             if($platform_price!=0){
            $price_row->price_promo = (float)$platform_price;
            }
           
            $tempitem  = (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price);
            $temptotal = ((isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $qty);
            
            $original_price = (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price : 0);

            $temp_gst_rate   = 0;
            $temp_gst_amount = 0;

            $parent_seller     = 0;
            $parent_gst_amount = 0;
            
            $item_selling_price = $tempitem;
            
            $gst_status = Fees::get_gst_status();
        
            if ($gst_status == '1') {
                
                // Calculate tax on total amount before tax
                if($gstTotalComputeTax){
                    
                    $item_selling_price = round((isset($price_row->gst) && $price_row->gst == 2 ? $tempitem *  (($price_row->gst_value + 100) / 100): $tempitem), 2);
//                   
                    // WITH GST CALCULATION
                    $temp_gst_rate   = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                    // Price before GST (Exclusive) = Price After GST  / 1.06 
                    
                    if(isset($price_row->gst) && $price_row->gst == 2){
                        $temp_before_gst_amount = round(($item_selling_price * $qty) / (($price_row->gst_value + 100) / 100), 2);
                    }else{
                        $temp_before_gst_amount = round(($tempitem * $qty), 2) ;
                    }
                    
//                    $temp_before_gst_amount = round((isset($price_row->gst) && $price_row->gst == 2 ? ($item_selling_price * $qty) / (($price_row->gst_value + 100) / 100)  : 0), 2);;
                    $temp_gst_amount = (isset($price_row->gst) && $price_row->gst == 2 ? ($item_selling_price * $qty) - $temp_before_gst_amount : 0);
                    $temp_gst_amount = round($temp_gst_amount, 2);
                    
                    $transac_data['total_amount'] += $temp_before_gst_amount;
                    
                    if($price_row->gst == 2){
                        
                        $temptotal = $temp_before_gst_amount;
                    }
                
                    $NotPromoPrice = round((isset($price_row->gst) && $price_row->gst == 2 ? $price_row->price *  (($price_row->gst_value + 100) / 100): 0), 2);
                    $PromoPrice = round((isset($price_row->gst) && $price_row->gst == 2 ? $price_row->price_promo *  (($price_row->gst_value + 100) / 100): 0), 2);
                    
                    $original_price = (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $NotPromoPrice : $PromoPrice);
                    
                    
                    // WITH GST CALCULATION
                    
                    // WITHOUT GST CALCULATION
//                    $temp_gst_rate   = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
//                    $temp_gst_amount = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value / 100 * $temptotal : 0);
//                    $temp_gst_amount = round($temp_gst_amount, 2);
                    // WITHOUT GST CALCULATION
                    
                    

                    $transac_data['gst_total'] += $temp_gst_amount;
                    
                }else{
                    
                    $transac_data['total_amount'] += ((isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $qty);
                    
                    $temp_gst_rate   = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                    $temp_gst_amount = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value / 100 * $tempitem : 0);
                    $temp_gst_amount = round($temp_gst_amount, 2) * $qty;

                    $transac_data['gst_total'] += $temp_gst_amount;
                    
                }
                
            }
            
                
            $tempprice = isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price;

            if (trim($srow->gst_reg_num) != "") {
                // Calculate tax on total amount before tax
                if($gstTotalComputeTax){
                    
                    $tempgstseller = $temptotal - ((isset($price_row->p_referral_fees) ? (isset($price_row->p_referral_fees_type) && $price_row->p_referral_fees_type == 'N' ? $price_row->p_referral_fees : ($price_row->p_referral_fees * ($tempprice) / 100)) : 0)) * $qty;

                    // calculate seller gst/input tax regardless gst is inactive
                    $temp_gst_sell = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                    $tempgstseller = $tempgstseller * $temp_gst_sell / 100;
                    $tempgstseller = round($tempgstseller, 2);
                    
                }else{
                    
                    $tempgstseller = $tempprice - (isset($price_row->p_referral_fees) ? (isset($price_row->p_referral_fees_type) && $price_row->p_referral_fees_type == 'N' ? $price_row->p_referral_fees : ($price_row->p_referral_fees * ($tempprice) / 100)) : 0);

                    // calculate seller gst/input tax regardless gst is inactive
                    $temp_gst_sell = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                    $tempgstseller = $tempgstseller * $temp_gst_sell / 100;
                    $tempgstseller = round($tempgstseller, 2) * $qty;

                    // $tempgstseller = $tempgstseller * $temp_gst_sell / 100 * $qty;
                    // $tempgstseller = $tempgstseller * $temp_gst_rate/100 * $qty;
                    // $tempgstseller = round($tempgstseller, 2);
                    
                }
            } else {
                $tempgstseller = 0;
            }

            // calculate gst for seller parent
            if ($srow->parent_seller != 0) {
                $parent_seller = $srow->parent_seller;

                $parent_row = DB::table('jocom_seller')
                    ->select('gst_reg_num')
                    ->where('id', '=', $srow->parent_seller)
                    ->first();

                if (trim($parent_row->gst_reg_num) != "") {
                    
                    // Calculate tax on total amount before tax
                    if($gstTotalComputeTax){
                        
                        $parent_gst_rate   = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                        $parent_gst_amount = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value / 100 * $temptotal : 0);
                        $parent_gst_amount = round($parent_gst_amount, 2);

                    }else{
                        $parent_gst_rate   = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                        $parent_gst_amount = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value / 100 * $tempitem : 0);
                        $parent_gst_amount = round($parent_gst_amount, 2) * $qty;
                    }
                }
            }
            // end of calculate gst for seller parent

                                // tie a product to KKW only
            $fix_prod = '7750'; //label_id
            if ($price_row->id == $fix_prod and $buyer == 'kkwoodypavilion') {
                //echo "it is here";
                $transac_data_detail[] = [
                    "product_id"           => $prow->id,
                    "product_name"         => $prow->name, //Added new field - 16-01-2018
                    "sku"                  => $prow->sku,
                   "category_1"            => $cat1->category_1,
                   "category_2"            => $cat2->category_2,
                    "category_3"           =>$cat3->category_3,
                    "price_label"          => $price_row->label,
                    "seller_sku"           => $price_row->seller_sku,
                    "price"                => 0,
                    "unit"                 => $qty,
                    "p_referral_fees"      => $price_row->p_referral_fees,
                    "p_referral_fees_type" => $price_row->p_referral_fees_type,
                    "delivery_time"        => ($prow->delivery_time == '' ? '24 hours' : $prow->delivery_time),
                    "delivery_fees"        => $dl_fees,
                    // "delivery_fees" => 0,
                    "seller_id"            => $srow->id,
                    "seller_username"      => $seller,
                    "gst_rate_item"        => 0,
                    "gst_amount"           => 0,
                    "gst_seller"           => 0,
                    "total"                => 0,
                    "transaction_id"       => "",
                    "p_option_id"          => $price_row->id,
                    "product_group"        => $type,
                    "sp_group_id"          => $sp_ind,
                    "parent_seller"        => $parent_seller,
                    "parent_gst_amount"    => $parent_gst_amount,
                    "zone_id"              => $zone_id,
                    "total_weight"         => $total_weight,
                ];

                $transac_data['total_amount'] -= ((isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $qty);
                $transac_data['gst_total'] -= $temp_gst_amount;
            } else {
                if ($price_row->gst == 2) {
                    $taxRate = (isset($price_row->gst) && $price_row->gst == 2 ? ($price_row->gst_value + 100) / 100  : 0);
                    $after_gst = $price_row->price * ($taxRate) ;
                    $gst_ori = (double)$after_gst - (double)$price_row->price;
                    $ori_price = $price_row->price;
                }else{        
                    $gst_ori = 0;
                    $ori_price = $price_row->price;
                }
                
                
                /* Foreign Math */
                if($price_row->foreign_price_promo != 0){
                    $foreign_unit_price = $price_row->foreign_price_promo;
                    $foreign_total_amount = $foreign_unit_price * $qty;
                    $foreign_actual_price = $price_row->foreign_price;
                    $transac_data['foreign_total_amount'] += $foreign_total_amount;
                }else{
                    $foreign_unit_price = $price_row->foreign_price;
                    $foreign_total_amount = $foreign_unit_price * $qty;
                    $foreign_actual_price = $price_row->foreign_price;
                    $transac_data['foreign_total_amount'] += $foreign_total_amount;
                }
                
                        
                /* Foreign Math */
                
                
                /* Add Cost per Item */

                $cost_price = DB::table('jocom_product_price_seller AS JPPS')
                        ->select('JPPS.cost_price')
                        ->where('JPPS.seller_id', '=', $srow->id)
                        ->where('JPPS.activation', '=', 1)
                        ->where('JPPS.product_price_id', '=', $price_row->id)->first();
                        
                //   echo "<pre>";
                // print_r($cost_price);
                // echo "</pre>";

                if($cost_price){
                    $unit_cost = $cost_price->cost_price;
                }else{
                    $unit_cost = 0.00;
                }
                $item_price = $item_selling_price;
                if($platform_price > 0){
                    $item_price = (float)$platform_price;
                }
                
                if($platform_original_price > 0){
                     $plat_ori_price = (float)$platform_original_price;
                }else{
                    $plat_ori_price = 0.00;
                }

                /* Add Cost per Item */
                
                $transac_data_detail[] = [
                    "product_id"           => $prow->id,
                    "product_name"         => $prow->name, //Added new field - 16-01-2018
                    "sku"                  => $prow->sku,
                   "category_1"            => $cat1->category_1,
                   "category_2"            => $cat2->category_2,
                    "category_3"           =>$cat3->category_3,    
                    "price_label"          => $price_row->label,
                    "seller_sku"           => $price_row->seller_sku,
                    // "price"                => $item_selling_price,
                    "price"                => $item_price,
                    "unit"                 => $qty,
                    "p_referral_fees"      => $price_row->p_referral_fees,
                    "p_referral_fees_type" => $price_row->p_referral_fees_type,
                    "delivery_time"        => ($prow->delivery_time == '' ? '24 hours' : $prow->delivery_time),
                    "delivery_fees"        => $dl_fees,
                    "seller_id"            => $srow->id,
                    "seller_username"      => $seller,
                    "gst_rate_item"        => $temp_gst_rate,
                    "gst_amount"           => $temp_gst_amount, //$GstAmount, //$temp_gst_amount,,
                    "gst_seller"           => $tempgstseller,
                    // "total"                => $temptotal,
                    "total"                => $platform_price ? $item_price * $qty : $temptotal,
                    "transaction_id"       => "",
                    "p_option_id"          => $price_row->id,
                    "product_group"        => $type,
                    "sp_group_id"          => $sp_ind,
                    "parent_seller"        => $parent_seller,
                    "parent_gst_amount"    => $parent_gst_amount,
                    "zone_id"              => $zone_id,
                    "total_weight"         => $total_weight,
                    "original_price"       => (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price : 0),//$original_price,
                    "ori_price"            => round($ori_price, 2),
                    "gst_ori"              => round($gst_ori, 2),
                    "actual_price"         => round($ori_price, 2) ,
                    "actual_total_amount"  => round($ori_price, 2) * $qty,
                    "actual_price_gst_amount"  => round($gst_ori, 2),
                    "foreign_price"        => $foreign_unit_price,
                    "foreign_total"        => $foreign_total_amount,
                    "foreign_actual_price" => $foreign_actual_price,
                    "foreign_currency"     => $foreign_currency,
                    "cost_unit_amount"     => $unit_cost,
                    "cost_amount"          => $unit_cost * $qty,
                    // "disc_per_unit"        => number_format((float)($ori_price - $item_selling_price), 2, '.', ''),
                    "disc_per_unit"        => $platform_price ?number_format((float)($ori_price - $item_price), 2, '.', '') :  number_format((float)($ori_price - $item_selling_price), 2, '.', ''), 
                    "disc"                 => 0, //number_format((float)($ori_price - $item_selling_price), 2, '.', '') * $qty, // 
                    "platform_original_price"  => $plat_ori_price,
                ];
                
            }
            
            // Total item quantity
            $returnData['item_quantity'] = $qty;
            
            // allow to checkout even no quantity for 11Street and Lazada and Qoo10
            if($transac_data['third_party'] != 1 && $transac_data['third_party_lazada'] != 1 && $transac_data['third_party_qoo10'] != 1 && $transac_data['third_party_shopee'] != 1  && $transac_data['third_party_astrogo'] != 1 && $transac_data['third_party_pgmall'] != 1){
                if ((int)$price_row->qty < (int)$qty) {
                    $error                 = true;
                    $returnData['message'] = '107';
                    $returnData['outStockList'][] = array(
                        "productSkU" => $prow->sku,
                        "productLabel" => $price_row->label,
                        "productName" => $prow->name,
                        "productID" => $prow->id,
                    );
                   
                }
            }

            // if ($c_seller === false) {
            //     $c_seller = $seller;
            // } else if ((int)$price_row->qty < (int)$qty) {
            //     $error                 = true;
            //     $returnData['message'] = '107';
               
            // }

        } else {
            $error = true;
            if ($price_row != null) {
        
                if($returnData['message'] == '111'){
                    $returnData['message'] = '109';
                }else{
                    $returnData['message'] = '108';
                }
            } else {
                if($returnData['message'] == '111'){
                    $returnData['message'] = '109';
                }else{
                    $returnData['message'] = '109';
                }
                
            }

        } // end of with product price and delivery fees

        return ["returnData" => $returnData, "transac_data" => $transac_data, "transac_data_detail" => $transac_data_detail, "c_seller" => $c_seller, "error" => $error];
    }
    public function scopeGet_checkout_info($query, $trans_id = '')
    {
        $returnData = [];

        $trans_query = DB::table('jocom_transaction AS a')
            ->select('a.*', 'b.full_name AS name')
            ->leftJoin('jocom_user AS b', 'a.buyer_username', '=', 'b.username')
            ->where('a.id', '=', $trans_id)->first();

        $returnData['trans_query'] = $trans_query;
        $returnData['total_all_weight'] = $trans_query;

        // $results = DB::select('SELECT a.*, (CASE WHEN b.name_cn IS NULL THEN b.name_cn ELSE b.name END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ?', $trans->id);
        // $price_query = DB::select( DB::raw("SELECT * FROM `jocom_product_price` WHERE `id` IN (" . implode(", ", array_keys($pro_opt_id)) . ")") );
        // $trans_detail_group_query = DB::select('SELECT a.*, b.delivery_time, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_and_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ' . $row->id);

        switch ($trans_query->lang) {
            case 'EN':
                $trans_detail_query = DB::table('jocom_transaction_details AS a')
                                ->select('a.*', 'b.name AS product_name', 'b.qrcode AS qrcode', 'b.is_popbox_available','b.gst AS is_taxable')
                    ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                    ->where('a.transaction_id', '=', $trans_id)->get();

                $trans_detail_group_query = DB::table('jocom_transaction_details_group AS a')
                    ->select('a.*', 'b.name AS product_name', 'b.qrcode AS qrcode')
                    ->leftJoin('jocom_product_and_package AS b', 'a.sku', '=', 'b.sku')
                    ->where('a.transaction_id', '=', $trans_id)->get();

                break;

            case 'CN':
                // $trans_detail_query = DB::table('jocom_transaction_details AS a')
                //     ->select('a.*', '(CASE WHEN b.name_cn IS NULL THEN b.name_cn ELSE b.name END) AS product_name', 'b.qrcode AS qrcode')
                //     ->leftJoin('jocom_products AS b','a.sku', '=', 'b.sku')
                //     ->where('a.transaction_id', '=', $trans_id)->get();

                $trans_detail_query = DB::select('SELECT a.*, b.qrcode as qrcode, (CASE WHEN b.name_cn IS NULL or b.name_cn = "" THEN b.name ELSE b.name_cn END) as product_name,b.is_popbox_available,b.gst AS is_taxable FROM `jocom_transaction_details` a LEFT JOIN `jocom_products` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ' . $trans_id);

                $trans_detail_group_query = DB::select('SELECT a.*, b.qrcode as qrcode, (CASE WHEN b.name_cn IS NULL or b.name_cn = "" THEN b.name ELSE b.name_cn END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = '.$trans_id);

                // $trans_detail_group_query = DB::table('jocom_transaction_details_group AS a')
                //     ->select('a.*', 'b.name_cn AS product_name', 'b.qrcode AS qrcode')
                //     ->leftJoin('jocom_product_and_package AS b','a.sku', '=', 'b.sku')
                //     ->where('a.transaction_id', '=', $trans_id)->get();
                break;

            case 'MY':
                // $trans_detail_query = DB::table('jocom_transaction_details AS a')
                //     ->select('a.*', 'b.name_my AS product_name', 'b.qrcode AS qrcode')
                //     ->leftJoin('jocom_products AS b','a.sku', '=', 'b.sku')
                //     ->where('a.transaction_id', '=', $trans_id)->get();
                //

                $trans_detail_query = DB::select('SELECT a.*, b.qrcode as qrcode, (CASE WHEN b.name_my IS NULL or b.name_my = "" THEN b.name ELSE b.name_my END),b.is_popbox_available as product_name,b.gst AS is_taxable FROM `jocom_transaction_details` a LEFT JOIN `jocom_products` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ' . $trans_id);

                $trans_detail_group_query = DB::select('SELECT a.*, b.qrcode as qrcode, (CASE WHEN b.name_my IS NULL or b.name_my = "" THEN b.name ELSE b.name_my END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = '.$trans_id);

                // $trans_detail_group_query = DB::table('jocom_transaction_details_group AS a')
                //     ->select('a.*', 'b.name_my AS product_name', 'b.qrcode AS qrcode')
                //     ->leftJoin('jocom_product_and_package AS b','a.sku', '=', 'b.sku')
                //     ->where('a.transaction_id', '=', $trans_id)->get();
                break;

            default:
                $trans_detail_query = DB::table('jocom_transaction_details AS a')
                                ->select('a.*', 'b.name AS product_name', 'b.qrcode AS qrcode','b.is_popbox_available','b.gst AS is_taxable')
                    ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                    ->where('a.transaction_id', '=', $trans_id)->get();

                $trans_detail_group_query = DB::table('jocom_transaction_details_group AS a')
                    ->select('a.*', 'b.name AS product_name', 'b.qrcode AS qrcode')
                    ->leftJoin('jocom_product_and_package AS b', 'a.sku', '=', 'b.sku')
                    ->where('a.transaction_id', '=', $trans_id)->get();
                break;
        }

        // $trans_detail_query = DB::table('jocom_transaction_details AS a')
        //         ->select('a.*', 'b.name AS product_name', 'b.qrcode AS qrcode')
        //         ->leftJoin('jocom_products AS b','a.sku', '=', 'b.sku')
        //         ->where('a.transaction_id', '=', $trans_id)->get();

        $returnData['trans_detail_query'] = $trans_detail_query;

        $total_all_weight = 0;
        foreach ($trans_detail_query as $key => $value) {
            $total_all_weight = $total_all_weight + $value->total_weight;
        }
        
        $returnData['total_all_weight'] = $total_all_weight;

        $returnData['trans_detail_group_query'] = $trans_detail_group_query;

        $trans_coupon = DB::table('jocom_transaction_coupon')
            ->select('*')
            ->where('transaction_id', '=', $trans_id)->first();

        $returnData['trans_coupon'] = $trans_coupon;
       

        $trans_points = DB::table('jocom_transaction_point')
            ->join('point_types', 'jocom_transaction_point.point_type_id', '=', 'point_types.id')
            ->where('jocom_transaction_point.transaction_id', '=', $trans_id)
            ->get();

        $returnData['trans_points'] = $trans_points;

        foreach ($trans_points as $point) {
            $returnData['total_trans_points'] += $point->amount;
        }

        // $trans_detail_group_query = DB::table('jocom_transaction_details_group AS a')
        //         ->select('a.*', 'b.name AS product_name', 'b.qrcode AS qrcode')
        //         ->leftJoin('jocom_product_and_package AS b','a.sku', '=', 'b.sku')
        //         ->where('a.transaction_id', '=', $trans_id)->get();

        $pointTypes = PointType::getActive();
        $userId     = Transaction::findOrFail($trans_id)->getUserId();

        $earn = [];

        foreach ($pointTypes as $pointType) {
            $pointUser                            = PointUser::getOrCreate($userId, $pointType->id, true);
            $pointTransaction                     = new PointTransaction($pointUser);
            $earn['pointsEarn'][$pointType->type] = $pointTransaction->getTransactionPoint($trans_id);
        }
    
        // for MOLPay
        $tempMOL    = MCheckout::molpay_conf();
        $tempMPAY   = MCheckout::mpay_conf();
        $tempBoost  = MCheckout::Boost_conf();
        $tempRevpay  = MCheckout::Revpay_conf();
        $returnData = array_merge($returnData, $tempMOL, $tempMPAY, $tempBoost, $tempRevpay, $earn);

        // POPBOX //
        
        $PopboxOrder = PopboxOrder::where("transaction_id",$trans_id)->where("status",1)->first();
        if(count($PopboxOrder) > 0 ){
            $returnData['is_popbox'] = 1;
            $returnData['popbox_locker'] = $PopboxOrder->popbox_locker;
            $returnData['popbox_address'] = $PopboxOrder->popbox_address;
        }else{
            $returnData['is_popbox'] = 0;
            $returnData['popbox_locker'] = "";
            $returnData['popbox_address'] = "";
        }
        // POPBOX //

        return $returnData;
    }

    public function scopeMolpay_conf($query, $trans_id = '')
    {
        $returnData  = [];
        //$merchant_id = "webtng_Dev";

        if (Config::get('constants.ENVIRONMENT') == 'test') {
            $merchant_id = Config::get('constants.MERCHANT_ID_TEST');
        }else{
            $merchant_id = Config::get('constants.MERCHANT_ID_LIVE');
        }

        //$merchant_id = "twenty37";

        $returnData['molpay_url']         = "https://pay.fiuu.com/RMS/pay/".$merchant_id."/FPX_MB2U.php";
        $returnData['molpay_url2']         = "https://www.onlinepayment.com.my/MOLPay/pay/".$merchant_id."/Point-BCard.php";
        $returnData['molpay_url3']         = "https://pay.fiuu.com/RMS/pay/".$merchant_id."/TNG-EWALLET.php";
        // $returnData['molpay_url4']         = "https://www.onlinepayment.com.my/MOLPay/pay/".$merchant_id."/razerpay.php";
        $returnData['molpay_url4']         = "https://pay.fiuu.com/RMS/pay/".$merchant_id."/GrabPay.php";
        $returnData['molpay_url5']         = "https://pay.fiuu.com/RMS/pay/".$merchant_id."/BOOST.php";
        $returnData['molpay_url6']         = "https://pay.fiuu.com/RMS/pay/".$merchant_id."/index.php";
        $returnData['molpay_url7']         = "https://pay.fiuu.com/RMS/pay/".$merchant_id."/ShopeePay.php";
        $returnData['molpay_url8']         = "https://pay.fiuu.com/RMS/pay/".$merchant_id."/Atome.php";
        $returnData['molpay_merchant_id'] = $merchant_id;
        
        $returnData['razer_atome']         = "https://www.onlinepayment.com.my/MOLPay/pay/".$merchant_id."/Atome.php";
        
        //Razer Credit
        $returnData['razer_credit']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/index.php";
        //Razer FPX
        $returnData['razer_mb2u']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/MB2U.php";
        $returnData['razer_cimbclicks']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/CIMBCLICKS.php";
        $returnData['razer_pbb']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/PBB.php";
        $returnData['razer_hlbconnect']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/HLBConnect.php";
        $returnData['razer_rhbnow']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/RHBNow.php";
        $returnData['razer_bimb']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/BIMB.php";
        $returnData['razer_bankrakyat']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/bankrakyat.php";
        $returnData['razer_bankmuamalat']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/bankmuamalat.php";
        $returnData['razer_fpx_bsn']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_BSN.php";
        $returnData['razer_fpx_abb']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_ABB.php";
        $returnData['razer_fpx_abmb']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_ABMB.php";
        $returnData['razer_amonline']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/AMOnline.php";
        $returnData['razer_fpx_hsbc']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_HSBC.php";
        $returnData['razer_fpx_kfh']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_KFH.php";
        $returnData['razer_fpx_ocbc']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_OCBC.php";
        $returnData['razer_fpx_scb']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_SCB.php";
        $returnData['razer_fpx_uob']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_UOB.php";
        $returnData['razer_fpx_agrobank']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/FPX_AGROBANK.php";
        
        //e-Wallet
        $returnData['razer_tng_ewallet']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/TNG-EWALLET.php";
        $returnData['razer_grabpay']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/GrabPay.php";
        $returnData['razer_shopeepay']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/ShopeePay.php";
        $returnData['razer_mb2u_qrpay_push']         = "https://pay.merchant.razer.com/RMS/pay/".$merchant_id."/MB2U_QRPay-Push.php";

        // $returnData['molpay_verifykey'] = "92851471a0ffb3a722f47d8bff25b02c";
        /* LIVE MOL PAY KEY */
        //$returnData['molpay_verifykey'] = "3bf9b345ea0c068b980322c8302796b9";
        
        /* TEST MOL PAY TNG KEY */
        //$returnData['molpay_verifykey'] = "c181b6dde003c0a99217ba98a4af861d";

        if (Config::get('constants.ENVIRONMENT') == 'test') {
            $returnData['molpay_verifykey'] = Config::get('constants.MOLPAY_VERIFYKEY_TEST');
        }else{
            $returnData['molpay_verifykey'] = Config::get('constants.MOLPAY_VERIFYKEY_LIVE');
        }
        
        $returnData['molpay_returnurl'] = asset('/')."checkout/molpayrtn";

        return $returnData;
    }
    
    public function scopeRevpay_conf($query, $trans_id = '')
    {
        $returnData  = [];
        //$merchant_id = "webtng_Dev";

        if (Config::get('constants.ENVIRONMENT') == 'test') {
            $merchant_id = Config::get('constants.MERCHANT_REVPAY_ID_DEV');
        }else{
            $merchant_id = Config::get('constants.MERCHANT_REVPAY_ID_LIVE');
        }

        //$merchant_id = "twenty37";

        $returnData['revpay_url']         = "https://gateway.revpay.com.my/payment";
        $returnData['revpay_url2']         = "https://gateway.revpay.com.my/requery";
        $returnData['revpay_merchant_id'] = $merchant_id;
     
        if (Config::get('constants.ENVIRONMENT') == 'test') {
            $returnData['revpay_verifykey'] = Config::get('constants.MERCHANT_REVPAY_KEY_DEV');
        }else{
            $returnData['revpay_verifykey'] = Config::get('constants.MERCHANT_REVPAY_KEY_LIVE');
        }

        $returnData['revpay_returnurl'] = asset('/')."checkout/revpayrtn";

        return $returnData;
    }
    
    public function scopeMpay_conf($query, $trans_id = '')
    {
        $returnData = [];

        $test = Config::get('constants.ENVIRONMENT');

        if ($test == 'test') {
            $mid            = '0000004713';
            $mpay_returnurl = '';

            $returnData['mpay_url']       = "https://uatmdex.mpay.my/mdex/payment/twenty37";
            $returnData['mid']            = $mid;
            $returnData['mpay_returnurl'] = asset('/')."checkout/mpayrtn";

            // Test Card No:4005550000000001
            // Expiry Date:05/17
            // CVV:100
            // Card Type:Visa

            // testing
            // https://www.mdex.my/mdex/payment/twenty37?MID=0000006120&invNo=REF00000009&amt=000000000100&desc=Testing&postURL=https://www.mdex.my/mdex/MPAY/testtransac24response.jsp&phone=0123456789&email=sywong@mpsb.net
            //$returnData['mpay_url'] = "https://twenty37app.mpay.my/mdex/payment/twenty37";
        } else {
            $mid            = '0000006120';
            $mpay_returnurl = '';

            $returnData['mpay_url']       = "https://www.mdex.my/mdex/payment/twenty37";
            $returnData['mid']            = $mid;
            $returnData['mpay_returnurl'] = asset('/')."checkout/mpayrtn";
        }

        return $returnData;
    }
    
    public function scopeBoost_conf($query, $trans_id = ''){
        $returnData  = [];

        $test = Config::get('constants.ENVIRONMENT');

        if ($test == 'test') {
             $returnData['boost_merchant_id']    = 'MCM0010040';
        } else {
             $returnData['boost_merchant_id']          = 'MCM0034395';
        }


        $returnData['boost_url'] = asset('/')."boost/validatepayment";

        return $returnData;
    }

    // For Paypal
    public function scopeTransaction_complete($query, $post_data = [])
    {
        $tran_data      = MCheckout::getTranData($post_data);
        $txn_id         = trim(array_get($post_data, 'txn_id'));
        $transaction_id = trim(array_get($post_data, 'invoice'));
        $payment_status = trim(array_get($post_data, 'payment_status'));
        $transaction    = Transaction::incomplete($transaction_id)->first();

        MCheckout::logTransactionData('jocom_paypal_transaction', $txn_id, $transaction_id, $payment_status, $tran_data);

        if ($payment_status == 'Completed' && $transaction != null) {
            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);
            

            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }


            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }

            // LogisticTransaction::log_transaction($transaction_id);
            MCheckout::trans_complete_mailout($transaction);
        }

        if ( ! $cashBuyPointFlag) {
            return 'regular';
        } else {
            return 'point';
        }
    }

    // For Paypal if only return ID in url
    public function scopeTransaction_complete_android($query, $transaction_id)
    {
        $transaction = Transaction::incomplete($transaction_id)->first();

        if ($transaction != null) {
            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }

            // LogisticTransaction::log_transaction($transaction_id);
            MCheckout::trans_complete_mailout($transaction);
        }

        if ( ! $cashBuyPointFlag) {
            return 'regular';
        } else {
            return 'point';
        }
    }

    // For JPoint
    public function point_transaction_complete($transaction_id)
    {
        $transaction = Transaction::incomplete($transaction_id)->first();

        if ($transaction != null) {
            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);
                
            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }
            

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }

            // LogisticTransaction::log_transaction($transaction_id);
            MCheckout::trans_complete_mailout($transaction);
        }

        if ( ! $cashBuyPointFlag) {
            return 'regular';
        } else {
            return 'point';
        }
    }

    // For Manage Pay
    public function scopeMpay_transaction_complete($query, $post_data = [])
    {
        $tran_data      = MCheckout::getTranData($post_data);
        $txn_id         = trim(array_get($post_data, 'authCode'));
        $transaction_id = trim(array_get($post_data, 'invno'));
        $payment_status = trim(array_get($post_data, 'resp'));
        $transaction    = Transaction::incomplete($transaction_id)->first();

        MCheckout::logTransactionData('jocom_mpay_transaction', $txn_id, $transaction_id, $payment_status, $tran_data);

        if ($payment_status == '0' && $transaction != null) {
            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);
            

            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }
            
            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }

            // LogisticTransaction::log_transaction($transaction_id);
            MCheckout::trans_complete_mailout($transaction);
        }

        if ( ! $cashBuyPointFlag) {
            return 'regular';
        } else {
            return 'point';
        }
    }
    
    // For Rev Pay
    public function scopeRev_transaction_complete($query, $post_data = [])
    {
        
        $transaction_id       = trim(substr(trim(array_get($post_data, 'Reference_Number')),'2','20'));
        $post_data['Reference_Number'] = $transaction_id;
        $tran_data            = MCheckout::getTranData($post_data);
        $txn_id               = trim(array_get($post_data, 'Transaction_ID'));
        $payment_id           = trim(array_get($post_data, 'Payment_ID'));
        $payment_status       = trim(array_get($post_data, 'Response_Code'));
        $transaction          = Transaction::incomplete($transaction_id)->first();

        MCheckout::logrevTransactionData('jocom_revpay_transaction', $txn_id, $payment_id, $transaction_id, $payment_status, $tran_data);
        if ($payment_status == '00' && $transaction != null) {
        //if ($payment_status == '00' ) {

            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();
            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);
            MCheckout::jCashbackPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }

            // LogisticTransaction::log_transaction($transaction_id);
         
            MCheckout::trans_complete_mailout($transaction);
        }
        if ( ! $cashBuyPointFlag) {
            return 'regular';
        } else {
            return 'point';
        }
    }
    
    // For MOL Pay
    public function scopeMol_transaction_complete($query, $post_data = [])
    {
        
       
        $transaction_id       = str_replace('"', '', trim(array_get($post_data, 'orderid')));
        $post_data['orderid'] = $transaction_id;
        $tran_data            = MCheckout::getTranData($post_data);
        $txn_id               = trim(array_get($post_data, 'tranID'));
        $payment_status       = trim(array_get($post_data, 'status'));
        $transaction          = Transaction::incomplete($transaction_id)->first();

        MCheckout::logTransactionData('jocom_molpay_transaction', $txn_id, $transaction_id, $payment_status, $tran_data);
        if ($payment_status == '00' && $transaction != null) {
        //if ($payment_status == '00' ) {

            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();
            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);
            

            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);
            // MCheckout::jCashbackPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }

            // LogisticTransaction::log_transaction($transaction_id);
         
            MCheckout::trans_complete_mailout($transaction);
        }
        if ( ! $cashBuyPointFlag) {
            return 'regular';
        } else {
            return 'point';
        }
    }

    // For MOL Pay XDK for Titanium
    public function scopeMol_transaction_complete2($query, $post_data = [])
    {
        $transaction_id       = str_replace('"', '', trim(array_get($post_data, 'order_id')));
        $post_data['order_id'] = $transaction_id;
        $tran_data            = MCheckout::getTranData($post_data);
        $txn_id               = trim(array_get($post_data, 'txn_ID'));
        $payment_status       = trim(array_get($post_data, 'status_code'));
        $transaction          = Transaction::incomplete($transaction_id)->first();

        MCheckout::logTransactionData('jocom_molpay_transaction', $txn_id, $transaction_id, $payment_status, $tran_data);

        if ($payment_status == '00' && $transaction != null) {
            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);
            
            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }
  

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }

            // LogisticTransaction::log_transaction($transaction_id);
            MCheckout::trans_complete_mailout($transaction);
        }

        if ( ! $cashBuyPointFlag) {
            return 'regular';
        } else {
            return 'point';
        }
    }
    
    //For Boost Wallet 

    
    public function scopeBoost_transaction_complete($query, $post_data = []){

        $transaction_id       = str_replace('"', '', trim(array_get($post_data, 'transid')));
        $payment_status       = trim(array_get($post_data, 'transactionStatus'));

        $transaction          = Transaction::incomplete($transaction_id)->first();

         if ($payment_status == 'completed' && $transaction != null) {

            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);
            
            
            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);
            MCheckout::jCashbackPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }
         
            MCheckout::trans_complete_mailout($transaction);


         }

         if ( ! $cashBuyPointFlag) {
                return 'regular';
            } else {
                return 'point';
            }


    }
    
    //For Grab Wallet/Installment/PayLater  
    
    public function scopeGrab_transaction_complete($query, $post_data = []){

        $transaction_id       = array_get($post_data, 'transaction_id');
        $payment_status       = trim(array_get($post_data, 'paymentstatus'));

        $transaction          = Transaction::incomplete($transaction_id)->first();

         if ($payment_status == 'success' && $transaction != null) {

            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);
            
            
            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);
            // MCheckout::jCashbackPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }
         
            MCheckout::trans_complete_mailout($transaction);


         }

         if ( ! $cashBuyPointFlag) {
                return 'regular';
            } else {
                return 'point';
            }


    }
    
    //For FavePay  
    
    public function scopeFavepay_transaction_complete($query, $post_data = []){

        $transaction_id       = trim(array_get($post_data, 'transid'));
        $payment_status       = trim(array_get($post_data, 'transactionStatus'));

        $transaction          = Transaction::incomplete($transaction_id)->first();

         if ($payment_status == 'successful' && $transaction != null) {

            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }
         
            MCheckout::trans_complete_mailout($transaction);


         }

         if ( ! $cashBuyPointFlag) {
                return 'regular';
            } else {
                return 'point';
            }


    }
    
     //For PacePay  
    
    public function scopePacepay_transaction_complete($query, $post_data = []){

        $transaction_id       = trim(array_get($post_data, 'transid'));
        $payment_status       = trim(array_get($post_data, 'transactionStatus'));

        $transaction          = Transaction::incomplete($transaction_id)->first();

         if ($payment_status == 'successful' && $transaction != null) {

            $transaction->status = 'completed';
            $transaction->invoice_date = date('Y-m-d');
            $transaction->save();

            $cashBuyPointFlag = MCheckout::cashBuyPoint($transaction_id);

            if ( ! $cashBuyPointFlag) {
                $userId = $transaction->getUserId();
                $activePointTypes = PointType::getActive();

                foreach ($activePointTypes as $pointType) {
                    $pointUser        = PointUser::getOrCreate($userId, $pointType->id, true);
                    $pointTransaction = new PointTransaction($pointUser);
                    $pointTransaction->purchase($transaction_id);
                }
            }

            MCheckout::afterTransactionUpdate($transaction_id);
            MCheckout::generateInv($transaction_id);

            if ( ! $cashBuyPointFlag) {
                MCheckout::generatePO($transaction_id);
                MCheckout::generateDO($transaction_id);
            }
         
            MCheckout::trans_complete_mailout($transaction);


         }

    
    }
    
    public function scopeCancelled_transaction($query, $tran_id)
    {
        if (isset($tran_id) && $tran_id != '') {
            $sql = DB::table('jocom_transaction')
                ->where('id', $tran_id)
                ->update(['status' => 'cancelled']);

        }
    }

    public function scopeInsert_coupon_code($query, $trans_id = "", $coupon_code = "")
    {
        $code = [];
        switch (Session::get('lang')) {
            case 'CN':
                $code['001'] = $coupon_code . ' 已经到使用极限.';
                $code['002'] = $coupon_code . ' 还未能被使用.';
                $code['003'] = $coupon_code . ' 已经过期了.';
                $code['004'] = $coupon_code . ' 没有该项产品的折扣.';
                $code['005'] = '不能使用优惠劵: ' . $coupon_code;
                $code['006'] = $coupon_code . ' 是无效的.';
                $code['007'] = $coupon_code . ' 需达指定最低消费才可使用.';
                $code['008'] = $coupon_code . ' 已经到使用极限.';
                $code['009'] = 'Coupon code: '. $coupon_code . ' is only applicable with PopBox delivery. Select your PopBox Location.';
                $code['010'] = 'Coupon code: '. $coupon_code . ' is not applicable. Wrong username.';
                $code['011'] = 'Invalid coupon code: ' . $coupon_code. ' your purchase amount is over than coupon available amount';
                break;

            case 'MY':
                $code['001'] = 'Had dah habis untuk kupon: ' . $coupon_code;
                $code['002'] = 'Terlalu awal untuk guna kupon: ' . $coupon_code;
                $code['003'] = 'Tamat tempoh untuk kupon: ' . $coupon_code;
                $code['004'] = 'Barang tiada diskaun guna kupon: ' . $coupon_code;
                $code['005'] = 'Tidak berhak untuk guna kupon: ' . $coupon_code;
                $code['006'] = 'Tidak sah untuk kupon: ' . $coupon_code;
                $code['007'] = 'Pembelian minimum tidak dicapai untuk kupon: ' . $coupon_code;
                $code['008'] = 'Had dah habis untuk kupon: ' . $coupon_code;
                $code['009'] = 'Coupon code: '. $coupon_code . ' is only applicable with PopBox delivery. Select your PopBox Location.';
                $code['010'] = 'Coupon code: '. $coupon_code . ' is not applicable. Wrong username.';
                $code['011'] = 'Invalid coupon code: ' . $coupon_code. ' your purchase amount is over than coupon available amount';
                break;

            case 'EN':
            default:
                $code['001'] = 'Limit finished for coupon code: ' . $coupon_code;
                $code['002'] = 'Too early to use coupon code: ' . $coupon_code;
                $code['003'] = 'Expired for coupon code: ' . $coupon_code;
                $code['004'] = 'Item no discount for coupon code: ' . $coupon_code;
                $code['005'] = 'Not entitled for coupon code: ' . $coupon_code;
                $code['006'] = 'Invalid coupon code: ' . $coupon_code;
                $code['007'] = 'Minimum purchase not reached for coupon code: ' . $coupon_code;
                $code['008'] = 'Limit finished for coupon code: ' . $coupon_code;
                $code['009'] = 'Coupon code: '. $coupon_code . ' is only applicable with PopBox delivery. Select your PopBox Location.';
                $code['010'] = 'Coupon code: '. $coupon_code . ' is not applicable. Wrong username.';
                $code['011'] = 'Invalid coupon code: ' . $coupon_code. ' your purchase amount is over than coupon available amount';
                break;
        }

        if ($coupon_code != "" && $trans_id != "") {
            $coupon_row = DB::table('jocom_coupon')
                ->select('*')
                ->where('coupon_code', '=', $coupon_code)
                ->where('status', '=', 1)
                ->where('is_bank_code', '=', 0)
                ->first();

            // valid and active coupon
            if ($coupon_row != null) {
                $tran_row = Transaction::find($trans_id);

                //valid transaction
                if ($tran_row != null) {
                    $invalid = false;

                    //check min purchase requirement
                    $sum_price    = TDetails::where('transaction_id', '=', $trans_id)->sum('total');
                    $sum_gst      = TDetails::where('transaction_id', '=', $trans_id)->sum('gst_amount');
                    $sum_purchase = number_format($sum_price + $sum_gst, 2);

                    if ($coupon_row->min_purchase > $sum_price) {
                        $invalid = true;
                        Session::put('coupon_msg', $code['007']);
                    }
                    
                    // Max total amount
                    if ($sum_purchase > $coupon_row->max_purchase && $coupon_row->max_purchase > 0) {
                        $invalid = true;
                        Session::put('coupon_msg', $code['011']." [RM $coupon_row->max_purchase]");
                    }

                    // check qty
                    if ($invalid != true && $coupon_row->q_limit == 'Yes') {
                        if ($coupon_row->qty <= 0) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['001']);
                        }
                    }

                    // check start date
                    if ($invalid != true && $coupon_row->valid_from != '') {
                        if ($coupon_row->valid_from > date("Y-m-d")) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['002']);
                        }
                    }

                    // check end date
                    if ($invalid != true && $coupon_row->valid_to != '') {
                        if ($coupon_row->valid_to < date("Y-m-d")) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['003']);
                        }
                    }
                    
                    // check username
                    if ($invalid != true && $coupon_row->username != '') {
                        if ($coupon_row->username != $tran_row->buyer_username) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['010']);
                        }
                    }

                    // check limit per customer
                    if ($invalid != true && $coupon_row->c_limit == 'Yes') {
                        $username = Transaction::select('buyer_username')->find($trans_id);

                        // $usebefore = DB::table('jocom_transaction AS a')
                        //     ->join('jocom_transaction_coupon AS b', 'a.id', '=', 'b.transaction_id')
                        //     ->where('a.buyer_username', '=', $username->buyer_username)
                        //     ->where('b.coupon_code', '=', $coupon_code)
                        //     ->where(function ($query) {
                        //         $query->where('a.status', '=', 'completed')
                        //             ->orWhere('a.status', '=', 'refund');
                        //     })
                        //     ->get()
                        // ;

                        // if (count($usebefore) >= $coupon_row->cqty) {
                        //     $invalid = true;
                        //     Session::put('coupon_msg', $code['008']);
                        // }
                        
                        
                        
                        $usebefore = DB::table('jocom_transaction AS a')
                            ->join('jocom_transaction_coupon AS b', 'a.id', '=', 'b.transaction_id')
                            ->where('b.coupon_code', '=', $coupon_code)
                            ->where(function ($query) {
                                $query->where('a.status', '=', 'completed')
                                    ->orWhere('a.status', '=', 'refund');
                            })
                            ->where(function ($query) use ($username){
                                $query->where('a.buyer_username', '=', $username->buyer_username)
                                    ->orWhere('a.buyer_email', '=', $username->buyer_email)
                                    ->orWhere('a.delivery_contact_no', '=', $username->delivery_contact_no);
                            })->count();

                        if ($usebefore >= $coupon_row->cqty) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['008']);
                        }
                    }

                    // check charity limit to use category type coupon only
                    if ($invalid != true && $tran_row->charity_id > 0)
                    {
                        if ($coupon_row->type != 'category')
                        {
                            $invalid = true;
                            Session::put('coupon_msg', $code['004']);
                        }
                        else
                        {
                            $charity = CharityCategory::find($tran_row->charity_id);

                            if (count($charity)>0)
                            {
                                $matchCat = CouponType::where('coupon_id', $coupon_row->id)->where('related_id', $charity->category_id)->first();

                                if (count($matchCat) <= 0)
                                {
                                    $invalid = true;
                                    Session::put('coupon_msg', $code['004']);
                                }
                            }
                            else
                            {
                                $invalid = true;
                                Session::put('coupon_msg', $code['004']);
                            }
                        }
                    }
                    // CHECKING ON POPBOX VOUCHER ONLY FOR POPBOX SERVICE
                    if (strpos($coupon_code, 'POP') !== false) {
                        $PopboxOrder = PopboxOrder::where("transaction_id", $trans_id)->where("status", 1)->first();
                        if (count($PopboxOrder) > 0) {

                        } else {
                            $invalid = true;
                            Session::put('coupon_msg', $code['009']);
                        }
                    }
                    // CHECKING ON POPBOX VOUCHER ONLY FOR POPBOX SERVICE
                    
                    // CHECKING ON STARBUCK VOUCHER ONLY FOR POPBOX SERVICE
                    if (strpos($coupon_code, 'HWSB') !== false) {
                        
                        if(($tran_row->total_amount - $tran_row->delivery_charges) > 79){
                            $tran_row->total_amount = $tran_row->total_amount - $tran_row->delivery_charges;
                            $tran_row->delivery_charges = 0;
                        }else{
                            $tran_row->total_amount = $tran_row->total_amount - $tran_row->delivery_charges;
                            $tran_row->delivery_charges = 5.00;
                            $tran_row->total_amount = $tran_row->total_amount + $tran_row->delivery_charges;
                        }
                        
                    }
                    // CHECKING ON STARBUCK VOUCHER ONLY FOR POPBOX SERVICE
                    
                    // check type
                    if ($invalid != true) {
                       
                        $total = $tran_row->total_amount - $tran_row->delivery_charges - $tran_row->process_fees;
                        switch ($coupon_row->type) {
                            case 'all':
                                
                                $tran_details = TDetails::where('transaction_id', '=', $trans_id)->get();
                               
                                if ($coupon_row->delivery_discount == 1){
                                    
                                    $coupon_amount = $coupon_row->amount;
                                    
                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process,$coupon_row);
                                    //$tran_row->gst_total = round($tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();
                                    
                                }else{
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type);
                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);
                                    $coupon_amount = $temptotal['coupon'];
                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();
                                }
                          
                                // if ($coupon_row->free_delivery == 1)
                                // {
                                //     $tran_row->total_amount -= $tran_row->delivery_charges;
                                //     $tran_row->delivery_charges = 0;
                                //     $tran_row->gst_delivery = 0;
                                // }

                                // if ($coupon_row->free_process == 1)
                                // {
                                //     $tran_row->total_amount -= $tran_row->process_fees;
                                //     $tran_row->process_fees = 0;
                                //     $tran_row->gst_process = 0;
                                // }

                                

                                $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $coupon_amount);

                                break;

                            case 'seller':

                                $couponlist    = CouponType::get_list($coupon_row->id);
                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->whereIn('seller_id', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->whereNotIn('seller_id', $couponlist)->get();

                                if (count($tran_details) > 0) {
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['004']);
                                }

                                break;

                            case 'customer':

                                $couponlist = CouponType::get_list($coupon_row->id);

                                if (in_array($tran_row->buyer_id, $couponlist)) {
                                    $tran_details = TDetails::where('transaction_id', '=', $trans_id)->get();

                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['005']);
                                }

                                break;

                            case 'item':
                                $couponlist = CouponType::get_list($coupon_row->id);

                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->where('product_group', '=', '')->whereIn('product_id', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->where('product_group', '=', '')->whereNotIn('product_id', $couponlist)->get();
                                
                                // Fraud Avoid!
                                if(strpos($coupon_row->coupon_code, 'HWSB') !== false){
                                 
                                    // if already exist
                                    $buyer_username = $tran_row->buyer_username ;
                                    $totalRedeem = DB::table('jocom_transaction_coupon AS JTC')
                                            ->leftjoin('jocom_transaction AS JT','JT.id','=','JTC.transaction_id') 
                                            ->where("JT.buyer_username",$buyer_username)
                                            ->where("JT.status",'completed')
                                            ->where('JTC.coupon_code', 'LIKE', '%HWSB%')->count();
                                    
                                    if($totalRedeem > 0){
                                        Session::put('coupon_msg', $code['004']);
                                    }else{
                                        
                                        if (count($tran_details) > 0) {
                                            $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                            $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                            $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                            $tran_row->save();

                                            $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                        } else {
                                            Session::put('coupon_msg', $code['004']);
                                        }
                                    }
                                    
                                    
                                }else{
                                
                                    if (count($tran_details) > 0) {
                                        $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);
    
                                        $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);
    
                                        $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                        $tran_row->save();
    
                                        $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                    } else {
                                        Session::put('coupon_msg', $code['004']);
                                    }
                                
                                }

                                break;

                            case 'package':
                                $couponlist = CouponType::get_list_package($coupon_row->id);

                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->whereIn('product_group', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->whereNotIn('product_group', $couponlist)->get();

                                if (count($tran_details) > 0) {
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['004']);
                                }
                                break;

                            case 'category':
                                $couponlist = CouponType::get_list_category($coupon_row->id);

                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->whereIn('product_id', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->whereNotIn('product_id', $couponlist)->get();

                                if (count($tran_details) > 0) {
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['004']);
                                }

                                break;

                            default:
                                Session::put('coupon_msg', $code['006']);
                                break;
                        }
                    } else {
                        if (Session::get('coupon_msg')) {

                        } else {
                            Session::put('coupon_msg', $code['006']);
                        }

                    }

                    // $row = DB::table('jocom_transaction_coupon')
                    //             ->select('*')
                    //             ->where('transaction_id', '=', $trans_id)
                    //             ->first();

                      // if ($row != null)
                      // {
                      //     $sql = DB::table('jocom_transaction_coupon')
                      //             ->where('id', $row->id)
                      //             ->update(array('transaction_id' => $trans_id, 'coupon_code' => $coupon_row->coupon_code, 'coupon_amount' => $coupon_row->amount));
                      // }
                      // else
                      // {
                      //     $sql = DB::table('jocom_transaction_coupon')->insert(array(
                      //             array('transaction_id' => $trans_id, 'coupon_code' => $coupon_row->coupon_code, 'coupon_amount' => $coupon_row->amount),
                      //         ));
                      // }
                } // end valid transaction
            } else {
                Session::put('coupon_msg', $code['006']);
            } // end valid and active coupon

        }

    }
    
    public function scopeInsert_couponpublic_code($query, $trans_id = "", $coupon_code = "", $coupon_type = 0)
    {
        $code = [];
        switch (Session::get('lang')) {
            case 'CN':
                $code['001'] = $coupon_code . ' 已经到使用极限.';
                $code['002'] = $coupon_code . ' 还未能被使用.';
                $code['003'] = $coupon_code . ' 已经过期了.';
                $code['004'] = $coupon_code . ' 没有该项产品的折扣.';
                $code['005'] = '不能使用优惠劵: ' . $coupon_code;
                $code['006'] = $coupon_code . ' 是无效的.';
                $code['007'] = $coupon_code . ' 需达指定最低消费才可使用.';
                $code['008'] = $coupon_code . ' 已经到使用极限.';
                $code['009'] = 'Coupon code: '. $coupon_code . ' is only applicable with PopBox delivery. Select your PopBox Location.';
                $code['010'] = 'Coupon code: '. $coupon_code . ' is not applicable. Wrong username.';
                $code['011'] = 'Invalid coupon code: ' . $coupon_code. ' your purchase amount is over than coupon available amount';
                break;

            case 'MY':
                $code['001'] = 'Had dah habis untuk kupon: ' . $coupon_code;
                $code['002'] = 'Terlalu awal untuk guna kupon: ' . $coupon_code;
                $code['003'] = 'Tamat tempoh untuk kupon: ' . $coupon_code;
                $code['004'] = 'Barang tiada diskaun guna kupon: ' . $coupon_code;
                $code['005'] = 'Tidak berhak untuk guna kupon: ' . $coupon_code;
                $code['006'] = 'Tidak sah untuk kupon: ' . $coupon_code;
                $code['007'] = 'Pembelian minimum tidak dicapai untuk kupon: ' . $coupon_code;
                $code['008'] = 'Had dah habis untuk kupon: ' . $coupon_code;
                $code['009'] = 'Coupon code: '. $coupon_code . ' is only applicable with PopBox delivery. Select your PopBox Location.';
                $code['010'] = 'Coupon code: '. $coupon_code . ' is not applicable. Wrong username.';
                $code['011'] = 'Invalid coupon code: ' . $coupon_code. ' your purchase amount is over than coupon available amount';
                $code['012'] = 'We are regret to inform you that you are not eligible to use : ' . $coupon_code;
                break;

            case 'EN':
            default:
                $code['001'] = 'Limit finished for coupon code: ' . $coupon_code;
                $code['002'] = 'Too early to use coupon code: ' . $coupon_code;
                $code['003'] = 'Expired for coupon code: ' . $coupon_code;
                $code['004'] = 'Item no discount for coupon code: ' . $coupon_code;
                $code['005'] = 'Not entitled for coupon code: ' . $coupon_code;
                $code['006'] = 'Invalid coupon code: ' . $coupon_code;
                $code['007'] = 'Minimum purchase not reached for coupon code: ' . $coupon_code;
                $code['008'] = 'Limit finished for coupon code: ' . $coupon_code;
                $code['009'] = 'Coupon code: '. $coupon_code . ' is only applicable with PopBox delivery. Select your PopBox Location.';
                $code['010'] = 'Coupon code: '. $coupon_code . ' is not applicable. Wrong username.';
                $code['011'] = 'Invalid coupon code: ' . $coupon_code. ' your purchase amount is over than coupon available amount';
                $code['012'] = 'We are regret to inform you that you are not eligible to use : ' . $coupon_code;
                break;
        }

        if ($coupon_code != "" && $trans_id != "") {
            $coupon_row = DB::table('jocom_coupon')
                ->select('*')
                ->where('coupon_code', '=', $coupon_code)
                ->where('status', '=', 1)
                ->where('is_bank_code', '=', $coupon_type)
                ->first();

            // valid and active coupon
            if ($coupon_row != null) {
                $tran_row = Transaction::find($trans_id);

                //valid transaction
                if ($tran_row != null) {
                    $invalid = false;

                    //check min purchase requirement
                    $sum_price    = TDetails::where('transaction_id', '=', $trans_id)->sum('total');
                    $sum_gst      = TDetails::where('transaction_id', '=', $trans_id)->sum('gst_amount');
                    // $sum_purchase = number_format($sum_price + $sum_gst, 2);
                    $sum_purchase = number_format(round($sum_price,2), 2);

                    if ($coupon_row->min_purchase > $sum_purchase) {
                        $invalid = true;
                        Session::put('coupon_msg', $code['007'].$sum_purchase);
                    }
                    
                    // Max total amount
                    if ($sum_purchase > $coupon_row->max_purchase && $coupon_row->max_purchase > 0) {
                        $invalid = true;
                        Session::put('coupon_msg', $code['011']." [RM $coupon_row->max_purchase]");
                    }

                    // check qty
                    if ($invalid != true && $coupon_row->q_limit == 'Yes') {
                        if ($coupon_row->qty <= 0) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['001']);
                        }
                    }

                    // check start date
                    if ($invalid != true && $coupon_row->valid_from != '') {
                        if ($coupon_row->valid_from > date("Y-m-d")) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['002']);
                        }
                    }

                    // check end date
                    if ($invalid != true && $coupon_row->valid_to != '') {
                        if ($coupon_row->valid_to < date("Y-m-d")) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['003']);
                        }
                    }
                    
                    // check username
                    if ($invalid != true && $coupon_row->username != '') {
                        if ($coupon_row->username != $tran_row->buyer_username) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['010']);
                        }
                    }

                    // check limit per customer
                    if ($invalid != true && $coupon_row->c_limit == 'Yes') {
                        $username = Transaction::select('buyer_username')->find($trans_id);

                        // $usebefore = DB::table('jocom_transaction AS a')
                        //     ->join('jocom_transaction_coupon AS b', 'a.id', '=', 'b.transaction_id')
                        //     ->where('a.buyer_username', '=', $username->buyer_username)
                        //     ->where('b.coupon_code', '=', $coupon_code)
                        //     ->where(function ($query) {
                        //         $query->where('a.status', '=', 'completed')
                        //             ->orWhere('a.status', '=', 'refund');
                        //     })
                        //     ->get()
                        // ;

                        // if (count($usebefore) >= $coupon_row->cqty) {
                        //     $invalid = true;
                        //     Session::put('coupon_msg', $code['008']);
                        // }
                        
                        
                        
                        $usebefore = DB::table('jocom_transaction AS a')
                            ->join('jocom_transaction_coupon AS b', 'a.id', '=', 'b.transaction_id')
                            ->where('b.coupon_code', '=', $coupon_code)
                            ->where(function ($query) {
                                $query->where('a.status', '=', 'completed')
                                    ->orWhere('a.status', '=', 'refund');
                            })
                            ->where(function ($query) use ($username){
                                $query->where('a.buyer_username', '=', $username->buyer_username)
                                    ->orWhere('a.buyer_email', '=', $username->buyer_email)
                                    ->orWhere('a.delivery_contact_no', '=', $username->delivery_contact_no);
                            })->count();

                        if ($usebefore >= $coupon_row->cqty) {
                            $invalid = true;
                            Session::put('coupon_msg', $code['008']);
                        }
                    }

                    // check charity limit to use category type coupon only
                    if ($invalid != true && $tran_row->charity_id > 0)
                    {
                        if ($coupon_row->type != 'category')
                        {
                            $invalid = true;
                            Session::put('coupon_msg', $code['004']);
                        }
                        else
                        {
                            $charity = CharityCategory::find($tran_row->charity_id);

                            if (count($charity)>0)
                            {
                                $matchCat = CouponType::where('coupon_id', $coupon_row->id)->where('related_id', $charity->category_id)->first();

                                if (count($matchCat) <= 0)
                                {
                                    $invalid = true;
                                    Session::put('coupon_msg', $code['004']);
                                }
                            }
                            else
                            {
                                $invalid = true;
                                Session::put('coupon_msg', $code['004']);
                            }
                        }
                    }
                    // CHECKING ON POPBOX VOUCHER ONLY FOR POPBOX SERVICE
                    if (strpos($coupon_code, 'POP') !== false) {
                        $PopboxOrder = PopboxOrder::where("transaction_id", $trans_id)->where("status", 1)->first();
                        if (count($PopboxOrder) > 0) {

                        } else {
                            $invalid = true;
                            Session::put('coupon_msg', $code['009']);
                        }
                    }
                    // CHECKING ON POPBOX VOUCHER ONLY FOR POPBOX SERVICE
                    
                    // CHECKING ON STARBUCK VOUCHER ONLY FOR POPBOX SERVICE
                    if (strpos($coupon_code, 'HWSB') !== false) {
                        
                        if(($tran_row->total_amount - $tran_row->delivery_charges) > 79){
                            $tran_row->total_amount = $tran_row->total_amount - $tran_row->delivery_charges;
                            $tran_row->delivery_charges = 0;
                        }else{
                            $tran_row->total_amount = $tran_row->total_amount - $tran_row->delivery_charges;
                            $tran_row->delivery_charges = 5.00;
                            $tran_row->total_amount = $tran_row->total_amount + $tran_row->delivery_charges;
                        }
                        
                    }
                    // CHECKING ON STARBUCK VOUCHER ONLY FOR POPBOX SERVICE
                    
                    
                     
                    
                    // check type
                    if ($invalid != true) {
                       
                        $total = $tran_row->total_amount - $tran_row->delivery_charges - $tran_row->process_fees;
                        switch ($coupon_row->type) {
                            case 'all':
                                
                                $tran_details = TDetails::where('transaction_id', '=', $trans_id)->get();
                               
                                if ($coupon_row->delivery_discount == 1){
                                    
                                    $coupon_amount = $coupon_row->amount;
                                    
                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process,$coupon_row);
                                    //$tran_row->gst_total = round($tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();
                                    
                                }else{
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type);
                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);
                                    $coupon_amount = $temptotal['coupon'];
                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();
                                }
                          
                                // if ($coupon_row->free_delivery == 1)
                                // {
                                //     $tran_row->total_amount -= $tran_row->delivery_charges;
                                //     $tran_row->delivery_charges = 0;
                                //     $tran_row->gst_delivery = 0;
                                // }

                                // if ($coupon_row->free_process == 1)
                                // {
                                //     $tran_row->total_amount -= $tran_row->process_fees;
                                //     $tran_row->process_fees = 0;
                                //     $tran_row->gst_process = 0;
                                // }

                                

                                $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $coupon_amount);

                                break;

                            case 'seller':

                                $couponlist    = CouponType::get_list($coupon_row->id);
                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->whereIn('seller_id', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->whereNotIn('seller_id', $couponlist)->get();

                                if (count($tran_details) > 0) {
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['004']);
                                }

                                break;

                            case 'customer':

                                $couponlist = CouponType::get_list($coupon_row->id);

                                if (in_array($tran_row->buyer_id, $couponlist)) {
                                    $tran_details = TDetails::where('transaction_id', '=', $trans_id)->get();

                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['005']);
                                }

                                break;

                            case 'item':
                                $couponlist = CouponType::get_list($coupon_row->id);

                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->where('product_group', '=', '')->whereIn('product_id', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->where('product_group', '=', '')->whereNotIn('product_id', $couponlist)->get();
                                
                                // Fraud Avoid!
                                if(strpos($coupon_row->coupon_code, 'HWSB') !== false){
                                 
                                    // if already exist
                                    $buyer_username = $tran_row->buyer_username ;
                                    $totalRedeem = DB::table('jocom_transaction_coupon AS JTC')
                                            ->leftjoin('jocom_transaction AS JT','JT.id','=','JTC.transaction_id') 
                                            ->where("JT.buyer_username",$buyer_username)
                                            ->where("JT.status",'completed')
                                            ->where('JTC.coupon_code', 'LIKE', '%HWSB%')->count();
                                    
                                    if($totalRedeem > 0){
                                        Session::put('coupon_msg', $code['004']);
                                    }else{
                                        
                                        if (count($tran_details) > 0) {
                                            $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                            $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                            $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                            $tran_row->save();

                                            $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                        } else {
                                            Session::put('coupon_msg', $code['004']);
                                        }
                                    }
                                    
                                    
                                }else{
                                
                                    if (count($tran_details) > 0) {
                                        $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);
    
                                        $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);
    
                                        $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                        $tran_row->save();
    
                                        $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                    } else {
                                        Session::put('coupon_msg', $code['004']);
                                    }
                                
                                }

                                break;

                            case 'package':
                                $couponlist = CouponType::get_list_package($coupon_row->id);

                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->whereIn('product_group', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->whereNotIn('product_group', $couponlist)->get();

                                if (count($tran_details) > 0) {
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['004']);
                                }
                                break;

                            case 'category':
                                $couponlist = CouponType::get_list_category($coupon_row->id);

                                $tran_details  = TDetails::where('transaction_id', '=', $trans_id)->whereIn('product_id', $couponlist)->get();
                                $tran_details2 = TDetails::where('transaction_id', '=', $trans_id)->whereNotIn('product_id', $couponlist)->get();

                                if (count($tran_details) > 0) {
                                    $temptotal = MCheckout::coupon_amount($trans_id, $tran_details, $total, $coupon_row->amount, $coupon_row->amount_type, $tran_details2);

                                    $tran_row = MCheckout::free_fees($tran_row, $coupon_row->free_delivery, $coupon_row->free_process);

                                    $tran_row->gst_total = round($temptotal['gst'] + $tran_row->gst_process + $tran_row->gst_delivery, 2);
                                    $tran_row->save();

                                    $done = MCheckout::update_coupon($trans_id, $coupon_row->coupon_code, $temptotal['coupon']);
                                } else {
                                    Session::put('coupon_msg', $code['004']);
                                }

                                break;

                            default:
                                Session::put('coupon_msg', $code['006']);
                                break;
                        }
                    } else {
                        if (Session::get('coupon_msg')) {

                        } else {
                            Session::put('coupon_msg', $code['006']);
                        }

                    }

                    // $row = DB::table('jocom_transaction_coupon')
                    //             ->select('*')
                    //             ->where('transaction_id', '=', $trans_id)
                    //             ->first();

                      // if ($row != null)
                      // {
                      //     $sql = DB::table('jocom_transaction_coupon')
                      //             ->where('id', $row->id)
                      //             ->update(array('transaction_id' => $trans_id, 'coupon_code' => $coupon_row->coupon_code, 'coupon_amount' => $coupon_row->amount));
                      // }
                      // else
                      // {
                      //     $sql = DB::table('jocom_transaction_coupon')->insert(array(
                      //             array('transaction_id' => $trans_id, 'coupon_code' => $coupon_row->coupon_code, 'coupon_amount' => $coupon_row->amount),
                      //         ));
                      // }
                } // end valid transaction
            } else {
                Session::put('coupon_msg', $code['006']);
            } // end valid and active coupon

        }

    }
    
    public function scopeUpdate_coupon($query, $id, $coupon_code, $amount)
    {
        $code = [];
        switch (Session::get('lang')) {
            case 'EN':
                $code['001'] = $coupon_code.' applied. You may proceed to checkout';
                break;

            case 'CN':
                $code['001'] = $coupon_code.' 使用成功. 您可以继续结账.';
                break;

            case 'MY':
                $code['001'] = $coupon_code.' berjaya digunakan. Anda boleh terus Checkout.';
                break;

            default:
                $code['001'] = $coupon_code.' applied. You may proceed to checkout';
                break;
        }
        // CHECKING ON CIMB COUPON CODE ONLY

        if(isset($coupon_code) && $coupon_code == 'CIMB10'){
            if($amount > 25){
                $amount=25;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == '10CIMB'){
            if($amount > 18){
                $amount=18;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'AFFINJOM10'){
            if($amount > 15){
                $amount=15;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'AMBANK10OFF'){
            if($amount > 20){
                $amount=20;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'NEW50'){
            if($amount > 85){
                $amount=85;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'NEW20'){
            if($amount > 52){
                $amount=52;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'NEW15'){
            if($amount > 48){
                $amount=48;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'UMOBILE40'){
            if($amount > 40){
                $amount=40;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'NEW50APR'){
            if($amount > 45){
                $amount=45;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'EU30FEB'){
            if($amount > 98){
                $amount=98;
            }

        }
        
        if(isset($coupon_code) && $coupon_code == 'NEW40MAY'){
            if($amount > 31){
                $amount=31;
            }

        }
        
        // CHECKING ON CIMB COUPON CODE ONLY
        
        $row = DB::table('jocom_transaction_coupon')
            ->select('*')
            ->where('transaction_id', '=', $id)
            ->first();

        if (count($row) > 0) {
            $sql = DB::table('jocom_transaction_coupon')
                ->where('id', $row->id)
                ->update(['transaction_id' => $id, 'coupon_code' => $coupon_code, 'coupon_amount' => $amount]);
        } else {
            $sql = DB::table('jocom_transaction_coupon')->insert([
                ['transaction_id' => $id, 'coupon_code' => $coupon_code, 'coupon_amount' => $amount],
            ]);
        }

        Session::put('coupon_msg', $code['001']);
    }

   public function scopeCoupon_amount($query, $id, $tran_details, $total, $coupon_amount, $type, $tran_details2 = null)
    {
        // $tran_details = TDetails::where('transaction_id', '=', $id)->get();

        $temp           = [];
        $temp['gst']    = 0;
        $temp['coupon'] = 0;
        $inv_newdate = Config::get('constants.NEW_INVOICE_START_DATE');
        $currentdate = DATE("Y-m-d")." 00:00:00";
        // $total_gst = 0;
        // $coupon_total = 0;
        foreach ($tran_details as $details) {
            switch ($type) {
                case 'Nett':
                    //if ($total < $coupon_amount) {
                    //    $coupon_amount = $total;
                    //}
                    
                    

                    $tempdisc = round($details->price / $total * $coupon_amount, 2);

                    // assuming only can input coupon once
                    // $tempdisc = $details->disc + $tempdisc;

                    // $tempdisc = round($details->total / $total * $coupon_amount, 2);

                    // // assuming only can input coupon once
                    // // $tempdisc = $details->disc + $tempdisc;

                    // $details->disc = $tempdisc;
                    // $temptotal = $details->total - $tempdisc;
                    // $details->gst_amount = round($temptotal * $details->gst_rate_item / 100, 2);
                    // $temp['gst'] += $details->gst_amount;
                    // $temp['coupon'] += $details->disc;
                    // // $temp['coupon'] += $tempdisc;

                    // $details->save();
                    break;

                case '%':
                    $tempdisc = round($details->price * $coupon_amount / 100, 2);

                    // $tempdisc = round($details->total * $coupon_amount / 100, 2);

                    // // assuming only can input coupon once
                    // // $tempdisc = $details->disc + $tempdisc;

                    // $details->disc = $tempdisc;
                    // $temptotal = $details->total - $tempdisc;
                    // $details->gst_amount = round($temptotal * $details->gst_rate_item / 100, 2);
                    // $temp['gst'] += $details->gst_amount;
                    // $temp['coupon'] += $details->disc;

                    // $details->save();
                    break;

                default:
                    if ($total < $coupon_amount) {
                        $coupon_amount = $total;
                    }
                    $tempdisc = round($details->price / $total * $coupon_amount, 2);
                    break;
            }


            // New Invocie Start  
            if($currentdate<$inv_newdate){

                $details->disc       = $tempdisc * $details->unit;
                $temptotal           = $details->price - $tempdisc;
                $tempgst             = round($temptotal * $details->gst_rate_item / 100, 2);
            }
            else{
                $tempdisc = 0;
                $details->disc       = $tempdisc * $details->unit;
                $temptotal           = $details->price - $tempdisc;
                $tempgst             = round($temptotal * $details->gst_rate_item / 100, 2);

            }
             // New Invocie End 

            // coupon product disc carry to e37, hence parent gst should be after disc
            
            // NO NEED TO RECALCULATE GST SO COMMENT THIS LINE //
            // if ($details->gst_amount == $details->parent_gst_amount)
            //    $details->parent_gst_amount = $tempgst * $details->unit;
          //  $details->gst_amount = $tempgst * $details->unit;
            
            // NO NEED TO RECALCULATE GST SO COMMENT THIS LINE //
          //echo  "IN1:".$tempgst;
           // $details->gst_amount = $tempgst * $details->unit;
            //echo  "IN2:".$temp['gst'];
           // echo  "INGSTAMOUNT:". $details->gst_amount;
            $temp['gst'] += $details->gst_amount;
          //  echo  "IN3:".$temp['gst'];
            //$temp['coupon'] += $details->disc;
            // $temp['coupon'] += $tempdisc;

           // $details->save();
        }

        if ($temp['coupon'] != $coupon_amount and $type != '%') {
            
            // New Invocie Start 
            if($currentdate<$inv_newdate){
                $adjust         = $coupon_amount - $temp['coupon'];
            }
            else{
                 $adjust = 0;
            }
        
            $temp['coupon'] = $coupon_amount;
       

            foreach ($tran_details as $details) {
                $details->disc = round($details->disc + $adjust, 2);
               // $details->save();
                break;
            }

        }
           
        if ($temp['coupon'] != $coupon_amount and $type == '%') {
    
             $temp['coupon'] = ($total+$temp['gst']) * $coupon_amount/100;

        }

        if ($tran_details2 != null) {
            foreach ($tran_details2 as $details2) {
                // assuming only can input coupon once
                // $temp['gst'] += $details2->gst_amount;
                $details2->disc       = 0;
                //$details2->gst_amount = round($details2->price * $details2->gst_rate_item / 100, 2) * $details2->unit;
               // $temp['gst'] += $details2->gst_amount;

                //$details2->save();
            }
        }
// echo "<pre>";
// print_r($temp);
// echo "</pre>";
// die();
        return $temp;
    }

    public function scopeFree_fees($query, $row, $free_delivery, $free_process,$coupon_row = null)
    {
        if ($free_delivery == 1) {
            $row->total_amount -= $row->delivery_charges;
            $row->delivery_charges = 0;
            $row->gst_delivery     = 0;
        }

        if ($free_process == 1) {
            $row->total_amount -= $row->process_fees;
            $row->process_fees = 0;
            $row->gst_process  = 0;
        }

        if ($coupon_row->delivery_discount == 1){
            
            //$transaction_id = $row->id;
            //$row->delivery_charges -= $coupon_row->amount;
            //$row->total_amount -= $coupon_row->amount;
           //$row->gst_delivery  = (($row->delivery_charges / 100) * 6 ) ;
            
        }

        return $row;
    }

    public function scopeTrans_complete_mailout($query, $row, $gateway = null)
    {
        
        // Implement on : 09 Nov 2016
        LogisticTransaction::log_transaction($row->id);
        // Implement on : 09 Nov 2016
        // MCheckout::preferredwelcomemail($row->id);
        // MCheckout::preferredcoupon($row->id);
        // Implement on : 14 Nov 2023
        // MCheckout::smartrentalemail($row->id);
       
        $subject = "tmGrocer Checkout Notification [ Transaction ID : {$row->id} ]";
        $body    = "
            Delivery To:<br>
            Name: {$row->delivery_name}<br>
            Contact No: {$row->delivery_contact_no}<br>
            Address 1: {$row->delivery_addr_1}<br>
            Address 2: {$row->delivery_addr_2}<br>
            State: {$row->delivery_state}<br>
            Postcode: {$row->delivery_postcode}<br>
            Country: {$row->delivery_country}<br><br>
            Special Message: {$row->special_msg}<br><br>
            Buyer Email: {$row->buyer_email}<br><br>
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
        $invoice_no      = $row->invoice_no;
        $dquery          = TDetails::where('transaction_id', '=', $row->id)->get();

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
        $querycoupon = DB::table('jocom_transaction_coupon')
            ->where('transaction_id', $row->id)
            ->get();

        foreach ($querycoupon as $crow) {
            $couponcode=number_format($crow->coupon_amount, 2, '.', '');
            $coupontext=$crow->coupon_code;

        }
        
        //JCashback Start  
        $jcashback = 0;
        $queryjcashback = DB::table('jocom_jcashback_transactiondetails')
                        ->where("transaction_id",$row->id)
                        ->where("status",'=',1)
                        ->first();

        if(count($queryjcashback) >0){

            $jcashback = number_format(($queryjcashback->jcash_point/100),2, '.', '');
        }

        //JCashback End 
        
        $totalamount=number_format(($row->gst_total+$row->total_amount)-($couponcode+$jcashback), 2, '.', '');        
        // $amountintext=MCheckout::convert_number_to_words($totalamount);
        $total_amount_text="Total Amount  : RM {$totalamount}<br>";
            
            if(isset($couponcode) && ! empty($couponcode))
            {
                $couponcodetext="Coupon Code : ({$coupontext}) {$couponcode}<br><br>";
            }
            else 
            {
                $couponcodetext="Coupon Code : 0<br><br>";
            }
            echo '<br>'.$couponcode.'<br>';
            
            if(isset($jcashback) && $jcashback > 0)
            {
                $couponcodetext="JCashback ({$queryjcashback->jcash_point}) Points : {$jcashback}<br><br>";
            }
            
            echo '<br>'.$couponcode.'<br>';
        // End  

        $squery = DB::table('jocom_seller')
            ->whereIn('id', $sell_id)
            ->get();

        $seller_name  = [];
        $seller_email = [];

        foreach ($squery as $srow) {
            $seller_name[$srow->id]  = $srow->company_name;
            $seller_email[$srow->id] = $srow->email;
        }

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

        if ($row->no_shipping == 1) {
            $seller_mail_body2 = "
                </table>
                <br><br>
                The transaction ID will be {$row->id} for references.<br>
                <br>";
        } else {
            $seller_mail_body2 = "
                </table>
                <br><br>
                {$total_amount_text}
                {$couponcodetext}
                <br><br>
                Purchase Order will be generated from tmGrocer within 24-48 hrs. The transaction ID will be {$row->id} for references.<br>
                <br>";
        }

        $notify_body = $seller_mail_body1.implode(' ', $pro_body).$seller_mail_body2;
        $data        = ['notify_body' => $notify_body];
        $test        = Config::get('constants.ENVIRONMENT');
        $testmail    = Config::get('constants.TEST_MAIL');

        if ($test == 'test') {
            $mail  = $testmail;
            $mail2 = $testmail;
        } else {
            $mail  = 'notification@tmgrocer.com';
            $mail2 = 'managepay@jocom.my';
        }

        Mail::send('emails.notification', $data, function ($message) use ($subject, $mail) {
            $message->from('payment@tmgrocer.com', 'tmGrocer');
            $message->to($mail, '')->subject($subject);
        });

        if ($gateway == 'mpay') {
            Mail::send('emails.notification', $data, function ($message) use ($subject, $mail2) {
                $message->from('payment@tmgrocer.com', 'tmGrocer');
                $message->to($mail2, '')->subject($subject);
            });
        }

        if ($row->no_shipping == 0) {
            // PO
            foreach ($seller_email as $s_sell_id => $mail) {
                $seller_notify_email = $seller_mail_body1.$pro_body[$s_sell_id];
                $seller_notify_email .= "
                    </table>
                    <br><br>
                    Purchase Order will be generated from tmGrocer within 24-48 hrs. The transaction ID will be {$row->id} for references.<br>
                    <br>";

                $file_name = urlencode($seller_po[$s_sell_id]).'.pdf';
                $attach    = $seller_popath[$s_sell_id].$file_name;
                //$attach    = $popath.'/'.$file_name;
                $data      = ['notify_body' => $seller_notify_email];

                if ($test == 'test') {
                    $mail = $testmail;
                } else {
                    /* This mail has been commented to allow new notification template to take place
                    
                    Mail::send('emails.notification', $data, function ($message) use ($subject, $attach, $mail) {
                        $message->from('payment@jocom.my', 'JOCOM');
                        $message->to('po.accounts@jocom.my', '')->subject($subject);

//                        if (strpos($attach, '/.') === false) {
//                            $message->attach($attach);
//                        }
                    });
                    
                    */
                }

                $subject2 = $subject." [{$seller_po[$s_sell_id]}]";
                
                /* This mail has been commented to allow new notification template to take place
                
                Mail::send('emails.notification', $data, function ($message) use ($subject2, $attach, $mail) {
                    $message->from('payment@jocom.my', 'JOCOM');
                    $message->to($mail, '')->subject($subject2);

//                    if (strpos($attach, '/.') === false) {
//                        $message->attach($attach);
//                    }
                });
                
                */
            }
        }

        $subject = "Confirmed Order [{$row->id}] and thank you for buying at tmGrocer!";
        $brow    = DB::table('jocom_user')
            ->where('username', '=', $row->buyer_username)
            ->first();

        $browName = ( ! empty($brow->full_name)) ? $brow->full_name : $row->buyer_username;
        $body2    = "
            Dear {$browName},<br><br>
            Thank you for purchasing with tmGrocer and here is our receipt of your successfully purchase of your items below:-<br><br>";

        $body2 .= $seller_mail_body1.implode(' ', $pro_body);
        $body2 .= "
            </table>
            <br><br>
            {$total_amount_text}
            {$couponcodetext}
            Your order is confirmed and your transaction ID : {$row->id}.<br><br>
            Please call us at 03-67348744 or email us at enquiries@tmgrocer.com with your username and Transaction ID if you require any assistance.<br><br>
            Thank you and have a nice day!<br>
            <br>";

        $file_name = urlencode($invoice_no).'.pdf';
        $attach    = Config::get('constants.INVOICE_PDF_FILE_PATH').'/'.$file_name;
        $data      = ['notify_body' => $body2];
        $mail      = $row->buyer_email;

        if ($test == 'test') {
            $mail    = $testmail;
            $do_mail = $testmail;
        } else {
            Mail::send('emails.notification', $data, function ($message) use ($subject, $attach, $mail) {
                $message->from('payment@tmgrocer.com', 'tmGrocer');
                $message->to('inv.accounts@tmgrocer.com', '')->subject($subject);

//                if (strpos($attach, '/.') === false) {
//                    $message->attach($attach);
//                }
            });

            $do_mail = 'orders@tmgrocer.com';
        }

        $buyer_name = ( ! empty($brow->full_name)) ? $brow->full_name : $row->buyer_username;
         if($brow->type=='corporate')
            {
        Mail::send('emails.notification', $data, function ($message) use ($subject, $attach, $mail, $buyer_name) {
            $message->from('payment@tmgrocer.com', 'tmGrocer');
            $message->to($mail, $buyer_name)->subject($subject);

//            if (strpos($attach, '/.') === false) {
//                $message->attach($attach);
//            }
        });
            }
            else
            {
               
            Mail::send('emails.notification', $data, function ($message) use ($subject, $attach, $mail, $buyer_name) {
                $message->from('payment@tmgrocer.com', 'tmGrocer');
                $message->to($mail, $buyer_name)->subject($subject);
            });        

            }
        if ($row->no_shipping == 0) {
            // DO
            $checksour  = $row->checkout_source;
            $sourmsg    = "";
            if($checksour == 2)
            {
                $sourmsg    = "Web";
            }
            else{
                $sourmsg    = "App";
            }
            
            if($checksour == 7)
            {
                $sourmsg    = "Wavpay";
            }
            
            if($checksour == 8)
            {
                $do_mail = "efstore@tmgrocer.com";
            }
            
            $do_no      = $row->do_no;
            $file_name  = urlencode($do_no).'.pdf';
            $do_attach  = Config::get('constants.DO_PDF_FILE_PATH').'/'.$file_name;
            $do_subject = $sourmsg." New Order [Transaction ID: {$row->id}] [{$do_no}]";
            

            Mail::send('emails.notification', $data, function ($message) use ($do_subject, $do_attach, $do_mail) {
                $message->from('payment@tmgrocer.com', 'tmGrocer');
                $message->to($do_mail, 'tmGrocer')->subject($do_subject);

//                if (strpos($do_attach, '/.') === false) {
//                    $message->attach($do_attach);
//                }
            });
        }
        CheckoutController::getFlashcountupdate();
        
        // Send progress notification to customer
        // * We're on our way to collect your goods email
        // $progressSubject = 'We\'re on our way to collect your goods!';

        // Mail::send('emails.progress', array(), function ($message) use ($progressSubject, $mail, $buyer_name) {
        //     $message->from('customer.service@jocom.my', 'JOCOM');
        //     $message->to($mail, $buyer_name)->subject($progressSubject);
        // });
    }
    
    public static function preferredwelcomemail($transaction_id){
        
        if($transaction_id!=""){
           $ProductCheck=TDetails::where('transaction_id', '=',$transaction_id)->where('product_id','=','54361')->first();
           if($ProductCheck){
        
                    $transaction = DB::table('jocom_transaction')
                                   ->select('buyer_username')
                                   ->where('id', '=',$transaction_id)
                                   ->first();
                    $user=DB::table('jocom_user')
                                   ->select('id','email','full_name','preferred_member')
                                   ->where('username', '=',$transaction->buyer_username)
                                   ->first();
                    $users = array(
                            'email' =>$user->email, //$email,
                            'name'  => $user->full_name,
                        );
                    $body_data=array(
                    'username' => $user->full_name
                    );
                if($user->preferred_member==0){
                    $user_update=DB::table('jocom_user')
                    ->where('username',$transaction->buyer_username)
                    ->update(['preferred_member'=>1,'membership_delivery' =>20,'member_disc_1' =>20,'member_disc_2' =>10]);
                       if($user_update){
                          Mail::send('emails.preferred_member',$body_data, function($message) use ($users)
                        {
                            $message->from('payment@tmgrocer.com', 'tmGrocer');
                            $message->to($users['email'],$users['name'])->subject('[tmGrocer]:Congratulations! You are the Preferred Member of tmGrocer');
                        });
                       }
                    }
                          
                }
            }
    }
    
    public static function smartrentalemail($transaction_id){
        
        if($transaction_id!=""){
           $transaction = DB::table('jocom_transaction')
                                   ->select('buyer_username','delivery_charges','total_amount','buyer_email','delivery_name')
                                   ->where('id', '=',$transaction_id)
                                   ->whereNotIn('buyer_username',['prestomall','shopee','lazada','Astro Go Shop','pgmall','tiktokshop','Lamboplace'])
                                   ->first();
                                   
           if($transaction){
                    $totalamount = 0;
                    $users = array(
                            'email' => $transaction->buyer_email, //$email,
                            'name'  => $transaction->delivery_name,
                        );
                    $body_data=array(
                    'buyer_username' => $transaction->buyer_username,
                    'name'  => $transaction->delivery_name,
                    );
                    $totalamount = $transaction->total_amount - $transaction->delivery_charges;
                    
                if($totalamount >= 150){
                   
                         Mail::send('emails.smartrnt_coupon',$body_data, function($message) use ($users)
                        {
                            $message->from('payment@tmgrocer.com', 'tmGrocer');
                            $message->to($users['email'],$users['name'])->subject('Claim Your 1 Month FREE Laptop Rental');
                        });
                       
                    }
                          
                }
            }
    }
    
    
    public static function preferredcoupon($transaction_id)
    {
      $transaction=Transaction::find($transaction_id);
      $CustomerInfo = Customer::where('username',$transaction->buyer_username)->first();
       if($CustomerInfo->preferred_member==1){
           
          if($CustomerInfo->membership_delivery>0 || $CustomerInfo->member_disc_1>0 || $CustomerInfo->member_disc_2>0){
            $coupon = DB::table('jocom_transaction_coupon')
            ->select('*')
            ->where('transaction_id', '=',$transaction_id)
            ->first();
          if($coupon){
              $coupon_code=$coupon->coupon_code;
              $available_count=0;
              if($coupon_code=='PMPFD10'){
                 $coupon_type=1;
                 $count_name='membership_delivery';
                 $claim_count=$CustomerInfo->membership_delivery-1;
                 $available_count=$CustomerInfo->membership_delivery;
             }else if($coupon_code=='PMP18'){
                 $coupon_type=2;
                 $count_name='member_disc_1';
                 $claim_count=$CustomerInfo->member_disc_1-1;
                 $available_count=$CustomerInfo->member_disc_1;
             }else if($coupon_code=='PMP25'){
                  $coupon_type=3;
                  $count_name='member_disc_2';
                  $claim_count=$CustomerInfo->member_disc_2-1;
                  $available_count=$CustomerInfo->member_disc_2;
             }
             
        if($coupon_code=='PMPFD10' ||$coupon_code=='PMP18'||$coupon_code=='PMP25' && $available_count>0){
             
             $member_transaction=DB::table('jocom_preferred_transaction_coupon')->insert([
            'transaction_id' => $transaction_id,
            'coupon_code' =>$coupon->coupon_code,
            'coupon_amount' =>$coupon->coupon_amount,
            'username' => $transaction->buyer_username,
            'coupon_type' =>$coupon_type,
            'status' =>1,
            'claim_remaining' =>$claim_count]);
            
            if($member_transaction){
                     $user_update=DB::table('jocom_user')
                    ->where('username',$transaction->buyer_username)
                    ->update([$count_name => $claim_count]);
                      }
                 }
            
               }
            }
       }
      
    }
    
    
    public static function redeemmail($transactionID){
        
        $redeem_url="https://tmgrocer.com/redeemclaim.php?";
         
        if(isset($transactionID)){
                
                    $transaction = DB::table('jocom_transaction')
                                   ->select('buyer_username','total_amount')
                                   ->where('id', '=',$transactionID)
                                   ->first();
                    $user=DB::table('jocom_user')
                                   ->select('id','email','full_name')
                                   ->where('username', '=',$transaction->buyer_username)
                                   ->first();
                    $redeemCode = 'tmGrocer'.date("h").strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3)).date("s");
                    $users = array(
                            'email' =>$user->email, //$email,
                            'name'  => $user->full_name,
                        );
                          
                     if(($transaction->total_amount) >='200' && ($transaction->total_amount) < '300'){
                         
                         $user_id_parm=urlencode(base64_encode($user->id));
                         $transaction_id_parm=urlencode(base64_encode($transactionID));
                         $redeem_code=urlencode(base64_encode($redeemCode));
                         $amount=urlencode(base64_encode('200'));
                         $password=urlencode(base64_encode('169169'));
                         
                        $redeem_list=DB::table('jocom_redeem_list')
                                   ->select('id','name','amount','status')
                                   ->where('status', '=',1)
                                   ->where('amount','=','200')
                                   ->get();
                                   
                        if($redeem_list!=""){
                         $sql = DB::table('jocom_redeem')->insert([
                         'redeem_code'=>$redeemCode,'transaction_id' => $transactionID, 'user_id' =>$user->id,'spending_amout'=>$transaction->total_amount, 'date_created' => date('Y-m-d H:i:s')]);
                $attach_images='<img alt="tmGrocer" src="https://tmgrocer.com/gearup/mask.png" title="tmGrocer" width="200" height="140">';

                    $body_data=array(
                    'username' => $transaction->buyer_username,
                    'redeem_details'=>$redeem_list,
                    'images'=>$attach_images,
                    'url'=>$redeem_url."uid=$user_id_parm&ord=".$transaction_id_parm
                    );
                  
                     }
                        
                        
                    }else if(($transaction->total_amount) >='300' && ($transaction->total_amount) < '500'){
                        
                        $user_id_parm=urlencode(base64_encode($user->id));
                        $transaction_id_parm=urlencode(base64_encode($transactionID));
                        $redeem_code=urlencode(base64_encode($redeemCode));
                        $amount=urlencode(base64_encode('300'));
                        $password=urlencode(base64_encode('169169'));
                        
                        $redeem_list=DB::table('jocom_redeem_list')
                                   ->select('id','name','amount','status')
                                   ->where('status', '=',1)
                                   ->where('amount','=','300')
                                   ->get();
                                   
                        if($redeem_list!=""){
                $sql = DB::table('jocom_redeem')->insert(
                         ['redeem_code'=>$redeemCode,'transaction_id' => $transactionID, 'user_id' =>$user->id,'spending_amout'=>$transaction->total_amount, 'date_created' => date('Y-m-d H:i:s')]);
                $users = array(
                            'email' =>$user->email, //$email,
                            'name'  => $user->full_name,
                        );
                 $attach_images='<img alt="tmGrocer" src="https://tmgrocer.com/gearup/bag.png" title="tmGrocer" width="100" height="140"><img alt="tmGrocer" src="https://tmgrocer.com/gearup/cap.png" title="tmGrocer" width="150" height="130"><img alt="tmGrocer" src="https://tmgrocer.com/gearup/cap1.png" title="tmGrocer" width="140" height="100">';

                $body_data=array(
                    'username' => $transaction->buyer_username,
                    'redeem_details'=>$redeem_list,
                    'images'=>$attach_images,
                    'url'=>$redeem_url."uid=$user_id_parm&ord=".$transaction_id_parm
                    );
                 
                     }
                    }
                    else if(($transaction->total_amount) >='500'){
                       
                        $user_id_parm=urlencode(base64_encode($user->id));
                        $transaction_id_parm=urlencode(base64_encode($transactionID));
                        $redeem_code=urlencode(base64_encode($redeemCode));
                        $amount=urlencode(base64_encode('500'));
                        $password=urlencode(base64_encode('JCM22GU'));
                        
                        $redeem_list=DB::table('jocom_redeem_list')
                                   ->select('id','name','amount','status')
                                   ->where('status', '=',1)
                                   ->where('amount','=','500')
                                   ->get();
                                   
                        if($redeem_list!=""){
                $sql = DB::table('jocom_redeem')->insert(
                         ['redeem_code'=>$redeemCode,'transaction_id' => $transactionID, 'user_id' =>$user->id,'spending_amout'=>$transaction->total_amount, 'date_created' => date('Y-m-d H:i:s')]);
                $users = array(
                            'email' =>$user->email, //$email,
                            'name'  => $user->full_name,
                        );
                $attach_images='<img alt="tmGrocer" src="https://tmgrocer.com/gearup/tanktop.png" title="tmGrocer" width="100" height="140"><img alt="tmGrocer" src="https://tmgrocer.com/gearup/tanktop3.png" title="tmGrocer" width="100" height="140"><img alt="tmGrocer" src="https://tmgrocer.com/gearup/tanktop1.png" title="tmGrocer" width="100" height="140"><img alt="tmGrocer" src="https://tmgrocer.com/gearup/tanktop2.png" title="tmgrocer" width="100" height="140">';

                $body_data=array(
                    'username' => $transaction->buyer_username,
                    'redeem_details'=>$redeem_list,
                    'images'=>$attach_images,
                    'url'=>$redeem_url."uid=$user_id_parm&ord=".$transaction_id_parm
                    );
                   
                     }
                    }
            if(isset($body_data)){
                // Mail::send('emails.redeem',$body_data, function($message) use ($users)
                //         {
                //             $message->from('payment@jocom.my', 'JOCOM');
                //             $message->to($users['email'], $users['name'])->subject('[JOCOM]:Congratulations! Received Gear UP Redeem Voucher');
                //         });
                
                Mail::send('emails.redeem',$body_data, function($message) use ($users)
                        {
                            $message->from('payment@tmgrocer.com', 'tmGrocer');
                            $message->to('maruthu@tmgrocer.com', $users['name'])->subject('[tmGrocer]:Congratulations! Received Gear UP Redeem Voucher');
                        });
            }
        
        }
    }

    public function scopeCheckout_view_xml($query, $view_data = [])
    {
        $data = [];

        $delivery_charges = Fees::get_delivery_charges();
        $process_fees     = Fees::get_process_fees();

        // $feesrow = DB::table('jocom_fees')
        // ->select('*')
        // ->find(1);

        // $delivery_charges = $feesrow->delivery_charges;
        // $process_fees = $feesrow->process_fees;

        $coupon_code        = "";
        $coupon_code_amount = 0;

        if (isset($view_data['trans_coupon'])) {
            $trans_coupon_row   = $view_data['trans_coupon'];
            $coupon_code        = $trans_coupon_row->coupon_code;
            $coupon_code_amount = $trans_coupon_row->coupon_amount;
        }

        $trans_row = $view_data['trans_query'];

        $data['transaction_id']      = $trans_row->id;
        $data['delivery_name']       = $trans_row->delivery_name;
        $data['delivery_contact_no'] = $trans_row->delivery_contact_no;
        $data['delivery_addr_1']     = $trans_row->delivery_addr_1;
        $data['delivery_addr_2']     = $trans_row->delivery_addr_2;
        $data['delivery_state']      = $trans_row->delivery_state;
        $data['delivery_postcode']   = $trans_row->delivery_postcode;
        $data['delivery_country']    = $trans_row->delivery_country;

        $data['special_msg']     = $trans_row->special_msg;
        $data['processing_fees'] = number_format($process_fees, 2, ".", "");

        $data['coupon_msg'] = '';
        if (Session::get('coupon_msg')) {
            $data['coupon_msg'] = str_replace(['<font color="red">', '</font>'], ['', ''], Session::get('coupon_msg'));
            Session::forget('coupon_msg');
        }

        $delivery_fees = $trans_row->delivery_charges;
        $total_fees    = $trans_row->delivery_charges;

        $items               = [];
        $key                 = 0;
        $group_product_price = [];

        foreach ($view_data['trans_detail_query'] as $row) {
            $total_fees += ($row->price * $row->unit);

            if ($row->product_group != '') {
                if ( ! isset($group_product_price[$row->product_group])) {
                    $group_product_price[$row->product_group] = 0;
                }

                $group_product_price[$row->product_group] += ($row->price * $row->unit);
                continue;
            }

            $items[] = [
                'product_name'  => $row->product_name,
                'sku'           => $row->sku,
                'price'         => $row->price,
                'unit'          => $row->unit,
                'delivery_time' => ($row->delivery_time == '' ? '7 hours' : $row->delivery_time),
                'tot_price'     => number_format($row->price * $row->unit, 2, ".", ""),
            ];
        }

        foreach ($view_data['trans_detail_group_query'] as $row) {
            $items[] = [
                'product_name'  => $row->product_name,
                'sku'           => $row->sku,
                'price'         => number_format($group_product_price[$row->sku] / $row->unit, 2, ".", ""),
                'unit'          => $row->unit,
                'delivery_time' => "",
                'tot_price'     => number_format($group_product_price[$row->sku], 2, ".", ""),
            ];

        }

        $data['coupon_code']        = ($coupon_code != '') ? $coupon_code : '';
        $data['coupon_code_amount'] = ($coupon_code != '') ? number_format($coupon_code_amount, 2, ".", "") : '0.00';

        $data['subtotal']          = number_format($total_fees + $process_fees - $delivery_fees - ($coupon_code != '' ? $coupon_code_amount : 0), 2, ".", "");
        $data['tot_delivery_fees'] = number_format($delivery_fees, 2, ".", "");
        $data['grandtotal']        = number_format($total_fees + $process_fees - ($coupon_code != '' ? $coupon_code_amount : 0), 2, ".", "");

        $data['item'] = $items;

        return ['xml_data' => $data];

    }

    public function scopeC_xml_view($query, $xml_data = [])
    {
        $data        = [];
        $data['enc'] = 'UTF-8';
        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, $xml_data);

        return Response::view('xml_v', $data)->header('Content-Type', 'text/xml')->header('Pragma', 'public')->header('Cache-control', 'private')->header('Expires', '-1');
    }

    public function scopeCheckfile($query, $id, $filetype = null)
    {
        if ($filetype == "INV") {
            $trans = Transaction::where('id', '=', $id)->where('invoice_no', '!=', '')->first();
            if ($trans != null) {
                return 'yes';
            } else {
                return 'no';
            }

            $trans2 = DB::table('jocom_transaction_parent_invoice')->where('transaction_id', '=', $id)->first();
            if ($trans2 != null) {
                return 'yes';
            }
        } else if ($filetype == "DO") {
            $trans = Transaction::where('id', '=', $id)->where('do_no', '!=', '')->first();
            if ($trans != null) {
                return 'yes';
            } else {
                return 'no';
            }
        } else if ($filetype == "PO") {
            $trans = TDetails::where('transaction_id', '=', $id)
                ->where(function ($query) {
                    $query->where('po_no', '!=', '')
                        ->orWhere('parent_po', '!=', '');

                })
                ->first();
            if ($trans != null) {
                return 'yes';
            } else {
                return 'no';
            }
        } else {
            return 'no';
        }

    }

    /**
     * Manually generate PO
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeGeneratePO($query, $id, $manual = null)
    {
        //$gotPO = MCheckout::checkfile($id, "PO");
        $gotPO = MCheckout::checkfile($id, "PO");
        //$gotDO = MCheckout::checkfile($id, "DO");
        //var_dump($gotPO);

        if ($gotPO == 'yes' && $manual != true) {
            return 'no';
            $id = null;
        }

        if ($id != null) {

            $trans = Transaction::find($id);
            //valid transaction
            if ($trans != null) {
                // no PO for point purchase
                if ($trans->no_shipping == 1) {
                    return 'no';
                }

                $coupon = TCoupon::where('transaction_id', '=', $id)->first();

                $general = [
                    "po_no"               => "",
                    "po_date"             => date('d/m/Y'),
                    // "po_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                    "payment_terms"       => "Cash/Credit Card",
                    "transaction_id"      => $trans->id,
                    "delivery_name"       => ($trans->delivery_name != '' ? $trans->delivery_name."" : ''),
                    "delivery_contact_no" => ($trans->delivery_contact_no != '' ? $trans->delivery_contact_no."" : ''),
                    "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                    "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                    "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                    "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                    "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                ];

                $involved_seller = DB::table('jocom_transaction_details')
                    ->select('seller_username', 'parent_seller')
                    ->where('transaction_id', '=', $trans->id)
                    ->groupBy('seller_username')
                    ->get();

                // PO to End Seller
                $sellerPO = MCheckout::processPO($trans, $general, $coupon, $involved_seller, 1);

                $involved_parent = DB::table('jocom_transaction_details')
                    ->select('seller_username', 'parent_seller')
                    ->where('transaction_id', '=', $trans->id)
                    ->where('parent_seller', '!=', 0)
                    ->groupBy('parent_seller')
                    ->get();

                // PO to Parent Seller
                $sellerPO = MCheckout::processPO($trans, $general, $coupon, $involved_parent, 0);
                
                // Send email notification
                $product2 = DB::table('jocom_transaction AS JT')
                            ->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id', '=', 'JT.id')
                            ->leftJoin('jocom_seller AS JS', 'JS.id','=', 'JTD.seller_id')
                            ->leftJoin('jocom_products AS JP', 'JP.id','=', 'JTD.product_id')
                            ->where('JTD.transaction_id', '=', $trans->id)
                            ->where('JS.notification', '=', '1')
                            ->select('JTD.transaction_id', 'JTD.price_label', 'JTD.unit','JS.email','JS.company_name', 'JP.name','JP.sku', 'JT.transaction_date','JTD.parent_seller')
                            ->get();
       
                $newArray = array();
                foreach ($product2 as $val) 
                {
                    if ( !isset($newArray[$val->email]) ) 
                    {
                        $newArray[$val->email] = array(
                                'transaction_date' => $val->transaction_date,
                                'transaction_id' => $val->transaction_id,
                                'parent_seller' => $val->parent_seller,
                                'company_name' =>$val->company_name,
                                'email' =>$val->email,
                                'product' => array()
                            );
                    }

                    $newArray[$val->email]['product'][] = array(
                            'sku'  => $val->sku,
                            'name'  => $val->name,
                            'price_label'  => $val->price_label,
                            'unit'  => $val->unit,    
                        );
                }

                foreach ($newArray as $key => $value) 
                {
                    $test        = Config::get('constants.ENVIRONMENT');

                    if ($test == 'test') {
                        $email  = Config::get('constants.TEST_MAIL');
                    } else {
                        $email  = $key;
                    }

                    $transaction_date = $value['transaction_date'];
                    $transaction_id = $value['transaction_id'];
                    $company_name = $value['company_name'];

                    $data = array(
                        'transaction_date' => $transaction_date,
                        'transaction_id' => $transaction_id,
                        'company_name' => $company_name,
                        'product'  => $value['product'],
                    );

                    $subject = "Product Notification [Transaction ID: {$trans->id}]";
                    //Deactivaed :: 23/06/2020
                    // Mail::send('emails.notificationnew', $data, function($message) use ($email,$subject)
                    // {
                    //     $message->from('payment@jocom.my', 'JOCOM');
                    //     $message->to($email)
                    //             // ->cc('accounts@jocom.my')
                    //             ->subject($subject);
                    // });
                }
                // END Send email notification

                return 'yes';
            } // end valid transaction
        } else {
            return 'no';
        }
    }

    public function processPO($trans, $general, $coupon, $involved_seller, $endSeller)
    {
        //each transaction details
        foreach ($involved_seller as $sellerrow) {
            $po_running = "po_no";
            $file_path  = Config::get('constants.PO_PDF_FILE_PATH')."/";
            $po_prefix  = "PO-";

            $issuer = [
                "issuer_name"      => "Tien Ming Distribution Sdn Bhd (1537285-T)",
                "issuer_address_1" => "10, Jalan Str 1,",
                "issuer_address_2" => "Saujana Teknologi Park,",
                "issuer_address_3" => "Rawang,",
                "issuer_address_4" => "48000 Rawang, Selangor, Malaysia",
                "issuer_tel"       => "Tel: 603-6734 8744",
                "issuer_gst"       => "",
            ];

            if ($endSeller == 1) {

                $coupon       = new TCoupon;

                $select_seller = 'seller_username';
                $select_value  = $sellerrow->seller_username;

                // to end seller
                $sellerTable = DB::table('jocom_seller')
                    ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                    ->where('username', '=', $sellerrow->seller_username)
                    ->first();

                if ($sellerrow->parent_seller != '0') {
                    $po_running = "parent_po";
                    $file_path  = Config::get('constants.PO_PARENT_PDF_FILE_PATH')."/";
                    $po_prefix  = "ePO-";

                    $parentTable = DB::table('jocom_seller')
                        ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num','company_reg_num')
                        ->where('id', '=', $sellerrow->parent_seller)
                        ->first();

                    $parentcountry = "";
                    $parentstate   = "";
                    $parentcity    = "";

                    $parentCountry = Country::find($parentTable->country);
                    if ($parentCountry != null) {
                        $parentcountry = $parentCountry->name;
                    }

                    $parentState = State::find($parentTable->state);
                    if ($parentState != null) {
                        $parentstate = $parentState->name.", ";
                    }

                    if (is_numeric($parentTable->city)) {
                        $city_row = City::find($parentTable->city);

                        if (count($city_row) > 0) {
                            $parentcity = $city_row->name;
                        }

                    } else {
                        $parentcity = $parentTable->city;
                    }

                    $parentcmpReg="";

                    if(isset($parentTable->company_reg_num) && $parentTable->company_reg_num !=''){
                        $parentcmpReg="(".$parentTable->company_reg_num .")";
                    }
                    

                    $issuer = [
                        "issuer_name"      => $parentTable->company_name.$parentcmpReg,
                        "issuer_address_1" => ($parentTable->address1 != '' ? $parentTable->address1."," : ''),
                        "issuer_address_2" => ($parentTable->address2 != '' ? $parentTable->address2."," : ''),
                        "issuer_address_3" => ($parentTable->postcode != '' ? $parentTable->postcode." " : '').($parentcity != '' ? $parentcity.", " : ''),
                        "issuer_address_4" => $parentstate.$parentcountry.".",
                        "issuer_tel"       => $parentTable->tel_num.($parentTable->tel_num != "" && $parentTable->mobile_no != "" ? "/" : '').$parentTable->mobile_no,
                        "issuer_gst"       => $parentTable->gst_reg_num,
                    ];
                }
            } else {
                $select_seller = 'parent_seller';
                $select_value  = $sellerrow->parent_seller;

                // to parent seller
                $sellerTable = DB::table('jocom_seller')
                    ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num')
                    ->where('id', '=', $sellerrow->parent_seller)
                    ->first();
            }

            $tempcountry = "";
            $tempstate   = "";
            $tempcity    = "";

            $sellerCountry = Country::find($sellerTable->country);
            if ($sellerCountry != null) {
                $tempcountry = $sellerCountry->name;
            }

            $sellerState = State::find($sellerTable->state);
            if ($sellerState != null) {
                $tempstate = $sellerState->name.", ";
            }

            if (is_numeric($sellerTable->city)) {
                $city_row = City::find($sellerTable->city);

                if (count($city_row) > 0) {
                    $tempcity = $city_row->name;
                }

            } else {
                $tempcity = $sellerTable->city;
            }

            $seller = [
                "seller_name"      => $sellerTable->company_name,
                "seller_address_1" => ($sellerTable->address1 != '' ? $sellerTable->address1."," : ''),
                "seller_address_2" => ($sellerTable->address2 != '' ? $sellerTable->address2."," : ''),
                "seller_address_3" => ($sellerTable->postcode != '' ? $sellerTable->postcode." " : '').($tempcity != '' ? $tempcity.", " : ''),
                "seller_address_4" => $tempstate.$tempcountry.".",
                "seller_email"     => $sellerTable->email,
                "attn_name"        => $sellerTable->company_name,
                "contact_no"       => $sellerTable->tel_num.($sellerTable->tel_num != "" && $sellerTable->mobile_no != "" ? "/" : '').$sellerTable->mobile_no,
                "seller_gst"       => $sellerTable->gst_reg_num,
            ];

            //with package product
            $product = DB::table('jocom_transaction_details AS a')
                ->select('a.*', 'b.name', 'c.name AS pname')
                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_product_package AS c', 'a.product_group', '=', 'c.sku')
                ->leftJoin('jocom_categories AS d', 'b.id', '=', 'd.product_id')
                ->where('a.transaction_id', '=', $trans->id)
                ->where("a.{$select_seller}", '=', $select_value)
                ->where('d.main', '=', '1')
                ->orderBy('d.category_id')
                ->orderBy('b.name')
                ->get();

            //loop until next empty PO number, to prevent error in jocom_running table
            $haveFile = true;
            while ($haveFile === true) {
                $po_counter = 0;

                $running = DB::table('jocom_running')
                    ->select('*')
                    ->where('value_key', '=', $po_running)->first();

                if ($running != null) {
                    $po_counter = $running->counter + 1;
                    $sql        = DB::table('jocom_running')
                        ->where('value_key', $po_running)
                        ->update(['counter' => $po_counter]);
                } else {
                    $po_counter = 1;
                    $sql        = DB::table('jocom_running')->insert([
                        ['value_key' => $po_running, 'counter' => $po_counter],
                    ]);
                }

                $numPO = $po_prefix.str_pad($po_counter, 5, "0", STR_PAD_LEFT);

                $general['po_no'] = $numPO;

                $file_name = urlencode($general['po_no']).".pdf";

                if ( ! file_exists($file_path.$file_name)) {
                    $haveFile = false;
                }
            }

            // update PO number to transaction details table
            $sql = DB::table('jocom_transaction_details')
                ->where("{$select_seller}", $select_value)
                ->where('transaction_id', $trans->id)
                ->update([$po_running => $numPO]);

            $temp_doc = array_merge($general, $seller, $issuer);

            $doc_info = json_encode($temp_doc);

            $sql = DB::table('jocom_document_data')->insert([
                ['doc_type' => $po_running, 'doc_no' => $general['po_no'], 'doc_info' => $doc_info],
            ]);

            if ( ! file_exists($file_path.$file_name)) {
                include app_path('library/html2pdf/html2pdf.class.php');

                if ($endSeller == 1) 
                {
                    $response = View::make('checkout.po_view')
                        ->with('display_details', $general)
                        ->with('display_trans', $trans)
                        ->with('display_seller', $seller)
                        ->with('display_issuer', $issuer)
                        ->with('display_product', $product)
                        ->with('endSeller', $endSeller);
                }
                else
                {
                    $response = View::make('checkout.po_view_parent')
                        ->with('display_details', $general)
                        ->with('display_trans', $trans)
                        ->with('display_seller', $seller)
                        ->with('display_issuer', $issuer)
                        ->with('display_coupon', $coupon)
                        ->with('display_product', $product)
                        ->with('endSeller', $endSeller);
                }

                // $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                // $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8');
                // $html2pdf->setDefaultFont('arialunicid0');
                // // $html2pdf->pdf->SetDisplayMode('fullpage');
                // // $html2pdf = new HTML2PDF('P','A4');
                // $html2pdf->WriteHTML($response);
                // //$html2pdf->Output("example.pdf");
                // $html2pdf->Output("./".$file_path.$file_name, 'F');

            }
        } //end each transaction details
    }

    /**
     * Manually generate Invoice
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeGenerateInv($query, $id, $manual = null,$deliveryservice = false)
    {
        //$gotPO = MCheckout::checkfile($id, "PO");
        $gotINV = MCheckout::checkfile($id, "INV");
        //$gotDO = MCheckout::checkfile($id, "DO");
        // var_dump($gotINV);

        if ($gotINV == 'yes' && $manual != true) {
            return 'no';
            $id = null;
        }

        if ($id != null) {

            $trans = Transaction::find($id);

            // valid transaction
            if ($trans != null) {
                $buyer = Customer::where('username', '=', $trans->buyer_username)->first();

                $paypal = TPayPal::where('transaction_id', '=', $id)->first();

                $coupon = TCoupon::where('transaction_id', '=', $id)->first();

                $payment_id   = 0;
                $parentSeller = "";

                if (count($paypal) > 0) {
                    $payment_id = $paypal->txn_id;
                }

                $general = [
                    "invoice_no"          => "",
                    //"invoice_date"        => date('d/m/Y'),
                    "invoice_date" => $trans->selected_invoice_date != null ? $trans->selected_invoice_date : date('Y-m-d'),
                    "payment_terms"       => "cash/cc",
                    "transaction_id"      => $trans->id,
                    "payment_id"          => $payment_id,
                    "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

                    "buyer_name"          => isset($buyer->full_name) ? $buyer->full_name : "",
                    "buyer_email"         => isset($buyer->email) ? $buyer->email : "",
                    "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                    "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                    "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                    "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                    "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                    "delivery_contact_no" => $trans->delivery_contact_no,
                ];

                // INV to Customer
                $sellerPO = MCheckout::processINV($trans, $general, $paypal, $coupon, $parentSeller, 1);
                

                // no INV from e37 for point purchase
                if ($trans->no_shipping == 1) {
                    return 'yes';
                }
                if($deliveryservice){
                    // no INV from e37 for delivery services
                    return 'yes';
                }else{
                    // INV from e37
                    $involved_parent = DB::table('jocom_transaction_details')
                        ->where('transaction_id', '=', $trans->id)
                        ->where('parent_seller', '!=', 0)
                        ->groupBy('parent_seller')
                        ->lists('parent_seller');

                    foreach ($involved_parent as $parentSeller) {
                        $sellerPO = MCheckout::processINV($trans, $general, $paypal, $coupon, $parentSeller, 0);
                    }
                    
                    
                /*
                 * Reward Gift Scheme
                 * Only for Platform 11Street , Lazada, Qoo10 , Shopee , Astro Go Shop, Jocom APP
                 */

                $buyer_username = $trans->buyer_username;
                $device_platform = isset($trans->device_platform) ? $trans->device_platform : "";

                $listSellerCollection =  TDetails::where("transaction_id",$id)->select("seller_username","total")->get();
                $listSeller = [];
                $listSellerTotal = [];
                foreach ($listSellerCollection  as $keyS => $valueS) {
                    if (!in_array($valueS->seller_username, $listSeller)) {
                        $listSeller[] = $valueS->seller_username;
                    }
                    if (in_array($valueS->seller_username, $listSeller)) {
                        $listSellerTotal[$valueS->seller_username] +=  $valueS->total;
                    }


                }
                
                
                // BUY MORE GET MORE REWARD //
                
                    $BMGMReward = DB::table('jocom_reward_module')
                        ->where('reward_type_code', '=', 'BMGM')
                        ->first();
                    
                    if($BMGMReward->activation == 1 && $buyer->type == 'public'){
                        
                        RewardController::rewardBMGM($buyer->id, $trans->id);
                        
                    }
                
                // BUY MORE GET MORE REWARD //
                
                $totalamount_tmt = 0; 
                $totalamount_tmt = $trans->total_amount - $trans->delivery_charges;
                 
                // if(in_array(strtolower($buyer_username),array("11street","prestomall","lazada","qoo10","shopee","astro go shop","wiraizkandar")) || in_array($device_platform,array("ios","android"))){
              
                //if(in_array(strtolower($buyer_username),array("wiraizkandar")) || in_array($device_platform,array("ios","android"))){

                    // To do : Update condition for jocom app too 

                    $GiftController = new GiftController();
                    $Gift = $GiftController->getreward();

                    foreach ($Gift as $key => $valueGift) {
                        # code...
                        switch ($valueGift->rule) {
                            
                            
                            case 'PDO':
                                
                                $addFoc = false;
                                
                                $states = DB::table('jocom_country_states')->where("id",$trans->delivery_state_id)->first();
                                $listArea = explode(",",$valueGift->region);
                                if( in_array($trans->delivery_state_id, $listArea) ){
                                    
                                    $totalProduct =  TDetails::where("transaction_id",$id)->where("product_id",$valueGift->target_product_id)->count();
                                    $totalFOCProduct =  TDetails::where("transaction_id",$id)->where("product_id",$valueGift->product_id)->count();
                                    $TransactionCol =  Transaction::where("id",$id)->first();
                                    //   echo "TOTAL PRODUCT:".$totalProduct;
                                    // echo "TOTAL totalFOCProduct:".$totalFOCProduct;
                                    // echo "TOTAL TransactionCol:".$TransactionCol;
                                    if($TransactionCol->total_amount >= $valueGift->base_reference  && $totalProduct > 0 && $totalFOCProduct == 0){
                                        // Apply Meet Requirement
                                        $addFoc = true;
                                        
                                        
                                    }else{
                                        $addFoc = false;
                                    }
                                    
                                    $ApiLog = new ApiLog ;
                                    $ApiLog->api = 'FOC_PDO';
                                    $ApiLog->data = json_encode(array("addFoc" =>  $addFoc,"totalProduct" =>  $totalProduct,"totalFOCProduct" => $totalFOCProduct,"totalProductAmount" => $TransactionCol->total_amount));
                                    $ApiLog->save();
                                    
                                    // var_dump($addFoc);
                                    
                                    
                                    if($addFoc){
                                        
                                        $query_row = DB::table('jocom_product_and_package')->select('*')->where('qrcode', '=', "JC".$valueGift->product_id)->first();
                                        // not package
                                        if (substr($query_row->id, 0, 1) != 'P') {
        
                                            $prow = $query_row;
                                            $price_option = $valueGift->p_option_id;
                                            $qty = $valueGift->reward_quantity;
        
                                            $zoneInfo = DB::table('jocom_product_delivery')->where("product_id",$prow->id)->first();
                                            $sellerInfo = DB::table('jocom_seller')->where("id",$prow->sell_id)->first();
                                            
                                            // Append FOC item in the list
                                            $TDetails = new TDetails;
                                            $TDetails->product_id = $valueGift->product_id;
                                            $TDetails->product_name = $prow->name;
                                            $TDetails->sku = $prow->sku;
                                            $TDetails->price_label = $valueGift->label;
                                            $TDetails->price = $valueGift->price;
                                            $TDetails->foreign_price = 0.00;
                                            $TDetails->p_referral_fees = 0.00;
                                            $TDetails->p_referral_fees_type = $valueGift->p_referral_fees_type;
                                            $TDetails->unit = $qty;
                                            $TDetails->delivery_fees = 0.00;
                                            $TDetails->delivery_time = $prow->delivery_time;
                                            $TDetails->seller_id = $sellerInfo->id;
                                            $TDetails->seller_username = $sellerInfo->username;
                                            $TDetails->disc = 0.00;
                                            $TDetails->gst_rate_item = 0.00;
                                            $TDetails->gst_amount = 0.00;
                                            $TDetails->original_price = 0.00;
                                            $TDetails->ori_price = 0.00;
                                            $TDetails->gst_ori = 0.00;
                                            $TDetails->actual_price = 0.00;
                                            $TDetails->actual_price_gst_amount = 0.00;
                                            $TDetails->transaction_id = $id;
                                            $TDetails->p_option_id = $price_option;
                                            $TDetails->parent_seller = 69;
                                            $TDetails->zone_id = $zoneInfo->zone_id;
                                            $TDetails->total_weight = $qty * $valueGift->p_weight;
                                            $TDetails->original_price = 0.00;
                                            $TDetails->action_type = 'FOC';
                                            
                                            $FocReward =  FocReward::find($valueGift->id);
                                            
                                          
                                            if($valueGift->balance_quantity >= $valueGift->reward_quantity){
                                                $TDetails->save();
                                                // Append FOC item in the list
                                                $FocReward =  FocReward::find($valueGift->id);
                                                $FocReward->balance_quantity = $valueGift->balance_quantity - $valueGift->reward_quantity;
                                                $FocReward->save();
                                                
                                                $FocRewardTransaction = DB::table('jocom_foc_reward_transaction')->insertGetId(
                                                    array(
                                                        "reward_id"=>$FocReward->id,
                                                        "flow_type"=>'OUT',
                                                        "quantity"=>$qty,
                                                        "transaction_id"=>$id,
                                                        "created_at"=>DATE("Y-m-d h:i:s"),
                                                    )
                                                );
                                            }
                                           
                                            
        
                                        }
                                        
                                    }
                                    
                                }
                                
                                
                                break;
                            
                            case 'ALL':
                                //echo "OUT";
                                $states = DB::table('jocom_country_states')->where("id",$trans->delivery_state_id)->first();
                                $listArea = explode(",",$valueGift->region);
                                
                                if( in_array($trans->delivery_state_id, $listArea) ){
                                        
                                    
                                    $query_row = DB::table('jocom_product_and_package')->select('*')->where('qrcode', '=', "JC".$valueGift->product_id)->first();
                                    // not package
                                    if (substr($query_row->id, 0, 1) != 'P') {
    
                                        $prow = $query_row;
                                        $price_option = $valueGift->p_option_id;
                                        $qty = $valueGift->reward_quantity;
    
                                        $zoneInfo = DB::table('jocom_product_delivery')->where("product_id",$prow->id)->first();
                                        $sellerInfo = DB::table('jocom_seller')->where("id",$prow->sell_id)->first();
                                        
                                        // Append FOC item in the list
                                        $TDetails = new TDetails;
                                        $TDetails->product_id = $valueGift->product_id;
                                        $TDetails->product_name = $prow->name;
                                        $TDetails->sku = $prow->sku;
                                        $TDetails->price_label = $valueGift->label;
                                        $TDetails->price = $valueGift->price;
                                        $TDetails->foreign_price = 0.00;
                                        $TDetails->p_referral_fees = 0.00;
                                        $TDetails->p_referral_fees_type = $valueGift->p_referral_fees_type;
                                        $TDetails->unit = $qty;
                                        $TDetails->delivery_fees = 0.00;
                                        $TDetails->delivery_time = $prow->delivery_time;
                                        $TDetails->seller_id = $sellerInfo->id;
                                        $TDetails->seller_username = $sellerInfo->username;
                                        $TDetails->disc = 0.00;
                                        $TDetails->gst_rate_item = 0.00;
                                        $TDetails->gst_amount = 0.00;
                                        $TDetails->original_price = 0.00;
                                        $TDetails->ori_price = 0.00;
                                        $TDetails->gst_ori = 0.00;
                                        $TDetails->actual_price = 0.00;
                                        $TDetails->actual_price_gst_amount = 0.00;
                                        $TDetails->transaction_id = $id;
                                        $TDetails->p_option_id = $price_option;
                                        $TDetails->parent_seller = 69;
                                        $TDetails->zone_id = $zoneInfo->zone_id;
                                        $TDetails->total_weight = $qty * $valueGift->p_weight;
                                        $TDetails->original_price = 0.00;
                                        $TDetails->action_type = 'FOC';
                                        
                                        $FocReward =  FocReward::find($valueGift->id);
                                        
                                        if($valueGift->balance_quantity >= $qty){
                                            $TDetails->save();
                                            // Append FOC item in the list
                                            $FocReward =  FocReward::find($valueGift->id);
                                            $FocReward->balance_quantity = $valueGift->balance_quantity - $valueGift->reward_quantity;
                                            $FocReward->save();
                                            
                                            
                                            $FocRewardTransaction = DB::table('jocom_foc_reward_transaction')->insertGetId(
                                                array(
                                                    "reward_id"=>$FocReward->id,
                                                    "flow_type"=>'OUT',
                                                    "quantity"=>$qty,
                                                    "transaction_id"=>$id,
                                                    "created_at"=>DATE("Y-m-d h:i:s"),
                                                )
                                            );
                                        }
                                       
                                        
    
                                    }
                                }
                                break;
                            

                            case 'TMT':
                                //echo "OUT";
                                $states = DB::table('jocom_country_states')->where("id",$trans->delivery_state_id)->first();
                                $listArea = explode(",",$valueGift->region);
                                if( 
                                    ($listSellerTotal[$valueGift->seller_username] >= $valueGift->base_reference) 
                                    && (in_array($trans->delivery_state_id, $listArea)) 
                                    && (in_array($valueGift->seller_username, $listSeller)) 
                                    ){
                                // if( ($totalamount_tmt >= $valueGift->base_reference) &&  in_array($trans->delivery_state_id, $listArea) ){                
                                        //echo "IN";

                                    $query_row = DB::table('jocom_product_and_package')->select('*')->where('qrcode', '=', "JC".$valueGift->product_id)->first();
                                    // not package
                                    if (substr($query_row->id, 0, 1) != 'P') {
    
                                        $prow = $query_row;
                                        $price_option = $valueGift->p_option_id;
                                        $qty = $valueGift->reward_quantity;
    
                                        $zoneInfo = DB::table('jocom_product_delivery')->where("product_id",$prow->id)->first();
                                        $sellerInfo = DB::table('jocom_seller')->where("id",$prow->sell_id)->first();
                                        
                                        // Append FOC item in the list
                                        $TDetails = new TDetails;
                                        $TDetails->product_id = $valueGift->product_id;
                                        $TDetails->product_name = $prow->name;
                                        $TDetails->sku = $prow->sku;
                                        $TDetails->price_label = $valueGift->label;
                                        $TDetails->price = $valueGift->price;
                                        $TDetails->foreign_price = 0.00;
                                        $TDetails->p_referral_fees = 0.00;
                                        $TDetails->p_referral_fees_type = $valueGift->p_referral_fees_type;
                                        $TDetails->unit = $qty;
                                        $TDetails->delivery_fees = 0.00;
                                        $TDetails->delivery_time = $prow->delivery_time;
                                        $TDetails->seller_id = $sellerInfo->id;
                                        $TDetails->seller_username = $sellerInfo->username;
                                        $TDetails->disc = 0.00;
                                        $TDetails->gst_rate_item = 0.00;
                                        $TDetails->gst_amount = 0.00;
                                        $TDetails->original_price = 0.00;
                                        $TDetails->ori_price = 0.00;
                                        $TDetails->gst_ori = 0.00;
                                        $TDetails->actual_price = 0.00;
                                        $TDetails->actual_price_gst_amount = 0.00;
                                        $TDetails->transaction_id = $id;
                                        $TDetails->p_option_id = $price_option;
                                        $TDetails->parent_seller = 69;
                                        $TDetails->zone_id = $zoneInfo->zone_id;
                                        $TDetails->total_weight = $qty * $valueGift->p_weight;
                                        $TDetails->original_price = 0.00;
                                        $TDetails->action_type = 'FOC';
                                        
                                        $FocReward =  FocReward::find($valueGift->id);
                                        
                                        $is_rewarded =  TDetails::where("transaction_id",$id)->where("product_id",$valueGift->product_id)->count();
                                        
                                        if($valueGift->balance_quantity >= $qty && $is_rewarded == 0 ){
                                            $TDetails->save();
                                            // Append FOC item in the list
                                            $FocReward =  FocReward::find($valueGift->id);
                                            $FocReward->balance_quantity = $valueGift->balance_quantity - $valueGift->reward_quantity;
                                            $FocReward->save();
                                            
                                            
                                            $FocRewardTransaction = DB::table('jocom_foc_reward_transaction')->insertGetId(
                                                array(
                                                    "reward_id"=>$FocReward->id,
                                                    "flow_type"=>'OUT',
                                                    "quantity"=>$qty,
                                                    "transaction_id"=>$id,
                                                    "created_at"=>DATE("Y-m-d h:i:s"),
                                                )
                                            );
                                        }
                                       
                                        
    
                                    }
                                }
                                break;
    
                            default:
                                break;
                        }
                    }

                    
                // }   

                /*
                * Reward Gift Scheme
                */

                
                
                
                    return 'yes';
                }
                
            } // end valid transaction
        } else {
            return 'no';
        }
    }

    public function processINV($trans, $general, $paypal, $coupon, $parentSeller, $toCustomer)
    {
        $inv_running = "invoice_no";
        $cpo_running = "china_po";
        $binv_running = "buyday_invoice";
        
        $file_path   = Config::get('constants.INVOICE_PDF_FILE_PATH')."/";
        $inv_prefix  = Config::get('constants.INVOICE_PREFIX');
        $cpo_prefix  = "CPO-";
        $buyday_prefix  = "BINV-";

        $issuer = [
            "issuer_name"      => "Tien Ming Distribution Sdn Bhd (1537285-T)",
            "issuer_address_1" => "10,Jalan Str 1,",
            "issuer_address_2" => "Saujana Teknologi Park,",
            "issuer_address_3" => "Rawang,",
            "issuer_address_4" => "48000 Rawang, Selangor, Malaysia.",
            "issuer_tel"       => "Tel: +603 6734 8744",
            "issuer_gst"       => "",
        ];

        if ($toCustomer == 1) {
            $product = DB::table('jocom_transaction_details AS a')
                ->select('a.*', 'b.name')
                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                ->where('a.transaction_id', '=', $trans->id)
                ->where('c.main', '=', '1')
                ->orderBy('c.category_id')
                ->orderBy('b.name')
                ->get();

            $group = DB::table('jocom_transaction_details_group AS a')
                ->select('a.*', 'b.name')
                ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                ->where('a.transaction_id', '=', $trans->id)
                ->orderBy('b.category')
                ->orderBy('b.name')
                ->get();

            $points = TPoint::transaction($trans->id)->get();

            // Earned points
            $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->get();

            $earnedId = [];

            foreach ($earnedPoints as $earnedPoint) {
                $earnedId[] = $earnedPoint->id;
            }

            $reversalPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
                ->get();

            $reversedId = [];

            foreach ($reversalPoints as $reversalPoint) {
                $reversedId[] = $reversalPoint->reversal;
            }

            $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->whereIn('point_transactions.id', array_diff($earnedId, $reversedId))
                ->get();

        } else {
            // $coupon       = new TCoupon;
            $points       = new TPoint;
            $earnedPoints = new TPoint;

            $inv_running = "parent_inv";
            $file_path   = Config::get('constants.INVOICE_PARENT_PDF_FILE_PATH')."/";
            $inv_prefix  = "eINV-";

            $product = DB::table('jocom_transaction_details AS a')
                ->select('a.*', 'b.name')
                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                ->where('a.transaction_id', '=', $trans->id)
                ->where('a.parent_seller', '=', $parentSeller)
                ->where('c.main', '=', '1')
                ->orderBy('c.category_id')
                ->orderBy('b.name')
                ->get();

            $group = DB::table('jocom_transaction_details_group AS a')
                ->select('a.*', 'b.name')
                ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_transaction_details AS c', 'a.sku', '=', 'c.product_group')
                ->where('a.transaction_id', '=', $trans->id)
                ->where('c.parent_seller', '=', $parentSeller)
                ->groupBy('c.parent_seller')
                ->orderBy('b.category')
                ->orderBy('b.name')
                ->get();

            $sellerTable = DB::table('jocom_seller')
                ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num','company_reg_num')
                ->where('id', '=', $parentSeller)
                ->first();

            $tempcountry = "";
            $tempstate   = "";
            $tempcity    = "";

            $sellerCountry = Country::find($sellerTable->country);
            if ($sellerCountry != null) {
                $tempcountry = $sellerCountry->name;
            }

            $sellerState = State::find($sellerTable->state);
            if ($sellerState != null) {
                $tempstate = $sellerState->name.", ";
            }

            if (is_numeric($sellerTable->city)) {
                $city_row = City::find($sellerTable->city);

                if (count($city_row) > 0) {
                    $tempcity = $city_row->name;
                }

            } else {
                $tempcity = $sellerTable->city;
            }

            $parentcmpReg="";

            if(isset($sellerTable->company_reg_num) && $sellerTable->company_reg_num !=''){
                $parentcmpReg="(".$sellerTable->company_reg_num .")";
            }

            $issuer = [
                "issuer_name"      => $sellerTable->company_name.$parentcmpReg,
                "issuer_address_1" => ($sellerTable->address1 != '' ? $sellerTable->address1."," : ''),
                "issuer_address_2" => ($sellerTable->address2 != '' ? $sellerTable->address2."," : ''),
                "issuer_address_3" => ($sellerTable->postcode != '' ? $sellerTable->postcode." " : '').($tempcity != '' ? $tempcity.", " : ''),
                "issuer_address_4" => $tempstate.$tempcountry.".",
                "issuer_tel"       => $sellerTable->tel_num.($sellerTable->tel_num != "" && $sellerTable->mobile_no != "" ? "/" : '').$sellerTable->mobile_no,
                "issuer_gst"       => $sellerTable->gst_reg_num,
            ];

            $general['buyer_name']          = "Tien Ming Distribution Sdn Bhd (1537285-T)";
            $general['buyer_email']         = "";
            $general['delivery_address_1']  = "10, Jalan Str 1,";
            $general['delivery_address_2']  = "Saujana Teknologi Park,";
            $general['delivery_address_3']  = "Rawang,";
            $general['delivery_address_4']  = "48000 Rawang, Selangor, Malaysia.";
            $general['special_instruction'] = "";
            $general['delivery_contact_no'] = "+603 6734 8744";

        }

        $haveFile = true;
        while ($haveFile === true) {
            $inv_counter = 0;

            $running = DB::table('jocom_running')
                ->select('*')
                ->where('value_key', '=', $inv_running)->first();
                
            $runningCPO = DB::table('jocom_running')
                ->select('*')
                ->where('value_key', '=', $cpo_running)->first();
            
            $runningBINV = DB::table('jocom_running')
                ->select('*')
                ->where('value_key', '=', $binv_running)->first();

            if ($running != null) {
                $inv_counter = $running->counter + 1;
                $sql         = DB::table('jocom_running')
                    ->where('value_key', $inv_running)
                    ->update(['counter' => $inv_counter]);
            } else {
                $inv_counter = 1;
                $sql         = DB::table('jocom_running')->insert([
                    ['value_key' => $inv_running, 'counter' => $inv_counter],
                ]);
            }
            
            /* PO TO BUYDAY FOR CHINA ORDER */
            if(strtolower($trans->buyer_username) == 'buyday'){
                
                if ($runningCPO->counter  != '') {
                    
                    $cpo_counter = $runningCPO->counter + 1;
                    $sql         = DB::table('jocom_running')
                        ->where('value_key', $cpo_running)
                        ->update(['counter' => $cpo_counter]);
                    
                    $binv_counter = $runningBINV->counter + 1;
                    $sql         = DB::table('jocom_running')
                        ->where('value_key', $binv_running)
                        ->update(['counter' => $binv_counter]);
                    
                    $numBINV = $buyday_prefix.str_pad($binv_counter, 5, "0", STR_PAD_LEFT);
                    
                } else {
                    $cpo_counter = 1;
                    $sql         = DB::table('jocom_running')->insert([
                        ['value_key' => $cpo_running, 'counter' => $cpo_counter],
                    ]);
                }
                
                $numCPO = $cpo_prefix.str_pad($cpo_counter, 5, "0", STR_PAD_LEFT);
                
            }else{
                $numBINV = ''; // Not BuyDay Sales
                $numCPO = ''; // Not BuyDay Sales
            }
            /* PO TO BUYDAY FOR CHINA ORDER */

            $numINV = $inv_prefix.str_pad($inv_counter, 5, "0", STR_PAD_LEFT);
            
            $general['customer_po'] = $numCPO;
            $general['invoice_no'] = $numINV;
            $general['foreign_invoice_no'] = $numBINV;

            $file_name = urlencode($general['invoice_no']).".pdf";

            if ( ! file_exists($file_path."/".$file_name)) {
                $haveFile = false;
            }
        }

        if ($toCustomer == 1) {
            // update Invoice number to transaction table
            $invoice_date = $general['invoice_date'];
            
            $sql = DB::table('jocom_transaction')
                ->where('id', $trans->id)
                ->update(['status' => 'completed','invoice_no' => $numINV, 'invoice_date' => $invoice_date,'customer_po' => $numCPO,'foreign_invoice_no' => $numBINV]);
        } else {
            $sql = DB::table('jocom_transaction_parent_invoice')->insert([
                ['transaction_id' => $trans->id, 'parent_inv' => $numINV, 'parent_seller' => $parentSeller],
            ]);
        }

        $temp_doc = array_merge($general, $issuer);

        $doc_info = json_encode($temp_doc);

        $sql = DB::table('jocom_document_data')->insert([
            ['doc_type' => $inv_running, 'doc_no' => $general['invoice_no'], 'doc_info' => $doc_info],
        ]);

        if ( ! file_exists($file_path."/".$file_name)) {
            include app_path('library/html2pdf/html2pdf.class.php');
            //New Invoice Start
            $invoiceview = "";
            $inv_newdate = Config::get('constants.NEW_INVOICE_START_DATE');

            $currentdate = $trans->transaction_date;
            if($currentdate<$inv_newdate){
                $invoiceview = 'checkout.invoice_view';
            }
            else 
            {
                 $invoiceview = 'checkout.invoice_view_new';
            }
            //New Invoice End
            
            // REFERRER REWARD MODULE  //

            $rewardresponse = RewardRFRRSetting::referrerReward($trans->id);
            
            $response = View::make($invoiceview)
                ->with('display_details', $general)
                ->with('display_trans', $trans)
                ->with('display_issuer', $issuer)
                ->with('display_seller', $paypal)
                ->with('display_coupon', $coupon)
                ->with('display_product', $product)
                ->with('display_group', $group)
                ->with('display_points', $points)
                ->with('display_earns', $earnedPoints)
                ->with('toCustomer', $toCustomer);

            // $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            // // $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8');
            // $html2pdf->setDefaultFont('arialunicid0');
            // // $html2pdf = new HTML2PDF('P','A4');
            // $html2pdf->WriteHTML($response);
            // //$html2pdf->Output("example.pdf");
            // $html2pdf->Output("./".$file_path."/".$file_name, 'F');

        }
    }

    /**
     * Manually generate DO
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeGenerateDO($query, $id, $manual = null,$deliveryservice = false)
    {
        //$gotPO = MCheckout::checkfile($id, "PO");
        $gotDO = MCheckout::checkfile($id, "DO");
        //$gotDO = MCheckout::checkfile($id, "DO");
        //

        if ($gotDO == 'yes' && $manual != true) {
            return 'no';
            $id = null;
        }

        if ($id != null) {

            $trans = Transaction::find($id);

            // valid transaction
            if ($trans != null) {
                // no DO for point purchase
                if ($trans->no_shipping == 1) {
                    return 'no';
                }

                $buyer = Customer::where('username', '=', $trans->buyer_username)->first();

                $paypal = TPayPal::where('transaction_id', '=', $id)->first();

                $payment_id = 0;

                if ($paypal != null) {
                    $payment_id = $paypal->txn_id;
                }

                $general = [
                    "do_no"               => "",
                    "do_date"             => date('Y-m-d H:i:s'),
                    // "do_date" => date('d/m/Y'),
                    // "do_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                    "payment_terms"       => "cash/cc",
                    "transaction_id"      => $trans->id,
                    "delivery_contact_no" => $trans->delivery_contact_no,
                    "payment_id"          => $payment_id,
                    "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

                    "delivery_name"       => isset($trans->delivery_name) ? $trans->delivery_name : "",
                    // "buyer_name" => isset($buyer->full_name) ? $buyer->full_name : "",
                    // "buyer_email" => isset($buyer->email) ? $buyer->email : "",
                    "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                    "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                    "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                    "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                    "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                    "delivery_contact_no" => $trans->delivery_contact_no,
                ];

                $product = DB::table('jocom_transaction_details AS a')
                    ->select('a.*', 'b.name')
                    ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                    ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                    ->where('a.transaction_id', '=', $trans->id)
                    ->where('c.main', '=', '1')
                    ->orderBy('c.category_id')
                    ->orderBy('b.name')
                //->where('a.product_group', '!=', '')
                    ->get();

                foreach ($product as $prow) {
                    $items[] = [
                        "sku"         => $prow->sku,
                        "price_label" => $prow->price_label,
                        "description" => (isset($prow->name) ? $prow->name : ""),
                        "qty"         => $prow->unit,
                        "u_price"     => number_format($prow->price, 2, ".", ""),
                        "value"       => number_format(($prow->price * $prow->unit), 2, ".", ""),
                    ];
                }

                // Special DO for logistic
                // $product2 = DB::table('jocom_transaction_details AS a')
                //     ->select('a.*', 'b.name', 'b.do_cat')
                //     ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                //     ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                //     ->where('a.transaction_id', '=', $trans->id)
                //     ->where('c.main', '=', '1')
                //     ->orderBy('b.do_cat', 'desc')
                //     ->orderBy('c.category_id')
                //     ->orderBy('b.name')
                // //->where('a.product_group', '!=', '')
                //     ->get();

                $group = DB::table('jocom_transaction_details_group AS a')
                    ->select('a.*', 'b.name')
                    ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                    ->where('a.transaction_id', '=', $trans->id)
                    ->orderBy('b.category')
                    ->orderBy('b.name')
                    ->get();

                foreach ($group as $grow) {
                    $items[] = [
                        "sku"         => $grow->sku,
                        "price_label" => "-",
                        "description" => (isset($grow->product_name) ? $grow->product_name : ""),
                        "qty"         => $grow->unit,
                        "u_price"     => 0,
                        "value"       => 0,
                    ];
                }

                 // DELIVERY SERVICE //
                $DeliveryOrderItems = array();
                if($deliveryservice){
                    
                    $DeliveryOrder = DeliveryOrder::where("transaction_id",$trans->id)->first();
                    $DeliveryOrderItems = DeliveryOrderItems::where("service_order_id",$DeliveryOrder->id)->get();
                    
                }
                // DELIVERY SERVICE //

                $general = array_merge($general, ['items' => $items]);

                // $results = DB::select('SELECT a.*, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ?', $trans->id);

                //var_dump( DB::getQueryLog() );

                $haveFile = true;
                while ($haveFile === true) {
                    $do_counter = 0;

                    $running = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'do_no')->first();

                    if ($running != null) {
                        $do_counter = $running->counter + 1;
                        $sql        = DB::table('jocom_running')
                            ->where('value_key', 'do_no')
                            ->update(['counter' => $do_counter]);
                    } else {
                        $do_counter = 1;
                        $sql        = DB::table('jocom_running')->insert([
                            ['value_key' => 'do_no', 'counter' => $do_counter],
                        ]);
                    }

                    $numDO = "DO-".str_pad($do_counter, 5, "0", STR_PAD_LEFT);

                    $general['do_no'] = $numDO;

                    $file_name = urlencode($general['do_no']).".pdf";

                    // Special DO for logistic
                    // $file_name2 = urlencode($general['do_no'])."_logistic.pdf";

                    if ( ! file_exists(Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name)) {
                        $haveFile = false;
                    }
                }
                
                include_once(app_path('library/phpqrcode/qrlib.php'));
                $qrCode     = $general['do_no'];
                $qrCodeFile = $general['do_no'].'.png';
                // $path = 'images/qrcode/';

                QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);

                // update Invoice number to transaction table
                $sql = DB::table('jocom_transaction')
                    ->where('id', $trans->id)
                    ->update(['do_no' => $numDO, 'qr_code'=>$qrCodeFile]);

                $doc_info = json_encode($general);

                $sql = DB::table('jocom_document_data')->insert([
                    ['doc_type' => 'buyer_do', 'doc_no' => $general['do_no'], 'doc_info' => $doc_info],
                ]);

                if ( ! file_exists(Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name)) {
                    include app_path('library/html2pdf/html2pdf.class.php');

                    $response = View::make('checkout.do_view')
                        ->with('display_details', $general)
                        ->with('display_trans', $trans)
                        ->with('display_seller', $paypal)
                        ->with('display_product', $product)
                        ->with('display_group', $group)
                        ->with('deliveryservice', $deliveryservice)
                        ->with("display_delivery_service_items",$DeliveryOrderItems);

                    // $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                    // $html2pdf->setDefaultFont('arialunicid0');
                    // // $html2pdf = new HTML2PDF('P','A4');
                    // $html2pdf->WriteHTML($response);
                    // //$html2pdf->Output("example.pdf");
                    // $html2pdf->Output("./".Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name, 'F');

                }

                // Special DO for logistic
                // if ( ! file_exists(Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name2)) {
                //     include app_path('library/html2pdf/html2pdf.class.php');

                //     $response = View::make('checkout.do_view2')
                //         ->with('display_details', $general)
                //         ->with('display_trans', $trans)
                //         ->with('display_seller', $paypal)
                //         ->with('display_product', $product2)
                //         ->with('display_group', $group);

                //     $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                //     $html2pdf->setDefaultFont('arialunicid0');
                //     // $html2pdf = new HTML2PDF('P','A4');
                //     $html2pdf->WriteHTML($response);
                //     //$html2pdf->Output("example.pdf");
                //     $html2pdf->Output("./".Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name2, 'F');

                // }
                return 'yes';
            } // end valid transaction

        } else {
            return 'no';
        }
    }
    
    /**
     * Manually generate DO
     * @param  [type] $query [description]
     * @param  [type] $id    [description]
     * @return [type]        [description]
     */
    public function scopeGeneratenewDO($query, $id, $manual = true,$deliveryservice = false)
    {

       
        //$gotPO = MCheckout::checkfile($id, "PO");
        $gotDO = MCheckout::checkfile($id, "DO");
        //$gotDO = MCheckout::checkfile($id, "DO");
        //

        if ($gotDO == 'yes' && $manual != true) {
            return 'no';
            $id = null;
        }

        if ($id != null) {

            $trans = Transaction::find($id);

            // valid transaction
            if ($trans != null) {
                // no DO for point purchase
                if ($trans->no_shipping == 1) {
                    return 'no';
                }

                $buyer = Customer::where('username', '=', $trans->buyer_username)->first();

                $paypal = TPayPal::where('transaction_id', '=', $id)->first();

                $payment_id = 0;

                if ($paypal != null) {
                    $payment_id = $paypal->txn_id;
                }

                $general = [
                    "do_no"               => "",
                    "do_date"             => date('Y-m-d H:i:s'),
                    // "do_date" => date('d/m/Y'),
                    // "do_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                    "payment_terms"       => "cash/cc",
                    "transaction_id"      => $trans->id,
                    "delivery_contact_no" => $trans->delivery_contact_no,
                    "payment_id"          => $payment_id,
                    "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

                    "delivery_name"       => isset($trans->delivery_name) ? $trans->delivery_name : "",
                    // "buyer_name" => isset($buyer->full_name) ? $buyer->full_name : "",
                    // "buyer_email" => isset($buyer->email) ? $buyer->email : "",
                    "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                    "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                    "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                    "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                    "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                    "delivery_contact_no" => $trans->delivery_contact_no,
                ];

                $product = DB::table('jocom_transaction_details AS a')
                    ->select('a.*', 'b.name')
                    ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                    ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                    ->where('a.transaction_id', '=', $trans->id)
                    ->where('c.main', '=', '1')
                    ->orderBy('c.category_id')
                    ->orderBy('b.name')
                //->where('a.product_group', '!=', '')
                    ->get();

                foreach ($product as $prow) {
                    $items[] = [
                        "sku"         => $prow->sku,
                        "price_label" => $prow->price_label,
                        "description" => (isset($prow->name) ? $prow->name : ""),
                        "qty"         => $prow->unit,
                        "u_price"     => number_format($prow->price, 2, ".", ""),
                        "value"       => number_format(($prow->price * $prow->unit), 2, ".", ""),
                    ];
                }

                // Special DO for logistic
                // $product2 = DB::table('jocom_transaction_details AS a')
                //     ->select('a.*', 'b.name', 'b.do_cat')
                //     ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                //     ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                //     ->where('a.transaction_id', '=', $trans->id)
                //     ->where('c.main', '=', '1')
                //     ->orderBy('b.do_cat', 'desc')
                //     ->orderBy('c.category_id')
                //     ->orderBy('b.name')
                // //->where('a.product_group', '!=', '')
                //     ->get();

                $group = DB::table('jocom_transaction_details_group AS a')
                    ->select('a.*', 'b.name')
                    ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                    ->where('a.transaction_id', '=', $trans->id)
                    ->orderBy('b.category')
                    ->orderBy('b.name')
                    ->get();

                foreach ($group as $grow) {
                    $items[] = [
                        "sku"         => $grow->sku,
                        "price_label" => "-",
                        "description" => (isset($grow->product_name) ? $grow->product_name : ""),
                        "qty"         => $grow->unit,
                        "u_price"     => 0,
                        "value"       => 0,
                    ];
                }

                 // DELIVERY SERVICE //
                $DeliveryOrderItems = array();
                if($deliveryservice){
                    
                    $DeliveryOrder = DeliveryOrder::where("transaction_id",$trans->id)->first();
                    $DeliveryOrderItems = DeliveryOrderItems::where("service_order_id",$DeliveryOrder->id)->get();
                    
                }
                // DELIVERY SERVICE //

                $general = array_merge($general, ['items' => $items]);

                // $results = DB::select('SELECT a.*, (CASE WHEN b.`name` IS NULL THEN a.`sku` ELSE b.`name` END) as product_name FROM `jocom_transaction_details_group` a LEFT JOIN `jocom_product_package` b ON a.`sku` = b.`sku` WHERE a.`transaction_id` = ?', $trans->id);

                //var_dump( DB::getQueryLog() );

                $haveFile = true;
                while ($haveFile === true) {
                    $do_counter = 0;

                    $running = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'do_no')->first();

                    if ($running != null) {
                        $do_counter = $running->counter + 1;
                        $sql        = DB::table('jocom_running')
                            ->where('value_key', 'do_no')
                            ->update(['counter' => $do_counter]);
                    } else {
                        $do_counter = 1;
                        $sql        = DB::table('jocom_running')->insert([
                            ['value_key' => 'do_no', 'counter' => $do_counter],
                        ]);
                    }

                    $numDO = "DO-".str_pad($do_counter, 5, "0", STR_PAD_LEFT);

                    $general['do_no'] = $numDO;

                    $file_name = urlencode($general['do_no']).".pdf";

                    // Special DO for logistic
                    // $file_name2 = urlencode($general['do_no'])."_logistic.pdf";

                    if ( ! file_exists(Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name)) {
                        $haveFile = false;
                    }
                }
                
                include_once(app_path('library/phpqrcode/qrlib.php'));
                $qrCode     = $general['do_no'];
                $qrCodeFile = $general['do_no'].'.png';
                // $path = 'images/qrcode/';

                QRcode::png($qrCode, "images/qrcode/".$qrCodeFile);

                // update Invoice number to transaction table
                $sql = DB::table('jocom_transaction')
                    ->where('id', $trans->id)
                    ->update(['do_no' => $numDO, 'qr_code'=>$qrCodeFile]);

                $sql2 = DB::table('logistic_transaction')
                    ->where('transaction_id', $trans->id)
                    ->update(['do_no' => $numDO]);

                $doc_info = json_encode($general);

                echo $numDO.'<BR>';

                $sql = DB::table('jocom_document_data')->insert([
                    ['doc_type' => 'buyer_do', 'doc_no' => $general['do_no'], 'doc_info' => $doc_info],
                ]);

                if ( ! file_exists(Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name)) {
                    include app_path('library/html2pdf/html2pdf.class.php');

                    $response = View::make('checkout.do_view')
                        ->with('display_details', $general)
                        ->with('display_trans', $trans)
                        ->with('display_seller', $paypal)
                        ->with('display_product', $product)
                        ->with('display_group', $group)
                        ->with('deliveryservice', $deliveryservice)
                        ->with("display_delivery_service_items",$DeliveryOrderItems);

                    // $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                    // $html2pdf->setDefaultFont('arialunicid0');
                    // // $html2pdf = new HTML2PDF('P','A4');
                    // $html2pdf->WriteHTML($response);
                    // //$html2pdf->Output("example.pdf");
                    // $html2pdf->Output("./".Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name, 'F');

                }

                // Special DO for logistic
                // if ( ! file_exists(Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name2)) {
                //     include app_path('library/html2pdf/html2pdf.class.php');

                //     $response = View::make('checkout.do_view2')
                //         ->with('display_details', $general)
                //         ->with('display_trans', $trans)
                //         ->with('display_seller', $paypal)
                //         ->with('display_product', $product2)
                //         ->with('display_group', $group);

                //     $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
                //     $html2pdf->setDefaultFont('arialunicid0');
                //     // $html2pdf = new HTML2PDF('P','A4');
                //     $html2pdf->WriteHTML($response);
                //     //$html2pdf->Output("example.pdf");
                //     $html2pdf->Output("./".Config::get('constants.DO_PDF_FILE_PATH')."/".$file_name2, 'F');

                // }
                return 'yes';
            } // end valid transaction

        } else {
            return 'no';
        }
    }


    private function afterTransactionUpdate($transactionId)
    {
        $trans = Transaction::find($transactionId);
        $details = TDetails::where('transaction_id', '=', $transactionId)->get();

        foreach ($details as $detail)
        {
            // purchases for charity
            if ($trans->charity_id > 0)
            {
                $product = CharityProduct::where('charity_id', $trans->charity_id)->where('product_price_id', $detail->p_option_id)->first();

                if (isset($product->id))
                {
                    $product->qty -= $detail->unit;
                    // $product->stock -= $detail->unit;
                    $product->save();
                }
            }
            else
            {
                // normal order, not special pricing
                if ($detail->sp_group_id == 0)
                {
                    $product = Price::find($detail->p_option_id);

                    if (isset($product->id))
                    {
                        $product->qty -= $detail->unit;
                        // $product->stock -= $detail->unit;
                        $product->save();
                    }
                }
            }            
        }

        $coupon = TCoupon::where('transaction_id', '=', $transactionId)->first();

        if (isset($coupon->coupon_code)) {
            $limit = Coupon::where('coupon_code', '=', $coupon->coupon_code)->first();

            if (isset($limit->q_limit) && $limit->q_limit == 'Yes') {
                $limit->qty -= 1;
                $limit->save();
            }
        }
    }

    public static function getTranData($post_data)
    {
        $tran_data = '';

        foreach ($post_data as $key => $value) {
            $tran_data .= $key.' = '.$value."\n";
        }

        return $tran_data;
    }
    
    public static function jCashbackPoint($transaction_id) {

        $row = DB::table('jocom_transaction_jcashback')
                    ->where('transaction_id','=',$transaction_id)
                    ->first();
        if(count($row)>0){
            DB::table('jocom_transaction_jcashback')
                    ->where('transaction_id', '=', $transaction_id)
                    ->update([
                        'status'      => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
        }

        $rowdet = DB::table('jocom_jcashback_transactiondetails')
                    ->where('transaction_id','=',$transaction_id)
                    ->first();

        if(count($rowdet)>0){
            DB::table('jocom_transaction_jcashback')
                    ->where('id', '=', $rowdet->jcashback_id)
                    ->update([
                        'jcash_point_used'   => $rowdet->jcash_point,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);

            DB::table('jocom_jcashback_transactiondetails')
                    ->where('transaction_id', '=', $transaction_id)
                    ->update([
                        'status'   => 1,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
        }

    }
    
    public static function logrevTransactionData($table, $txn_id, $payment_id, $transaction_id, $payment_status, $tran_data)
    {
        $row = DB::table($table)
            ->where('txn_id', '=', $txn_id)
            ->where('transaction_id', '=', $transaction_id)
            ->first();

        if ($row) {
            if (base64_encode($row->tran_data) != base64_encode($tran_data)) {
                DB::table($table)
                    ->where('id', '=', $row->id)
                    ->update([
                        'tran_data'      => $tran_data,
                        'payment_status' => $payment_status,
                        'insert_date' => date('Y-m-d H:i:s'),
                    ]);
            }
        } else {
            $data                   = [];
            $data['txn_id']         = $txn_id;
            $data['payment_id']     = $payment_id;
            $data['insert_date']    = date('Y-m-d H:i:s');
            $data['tran_data']      = $tran_data;
            $data['transaction_id'] = $transaction_id;
            $data['payment_status'] = $payment_status;

            DB::table($table)->insert($data);
        }
    }

    public static function logTransactionData($table, $txn_id, $transaction_id, $payment_status, $tran_data)
    {
        $row = DB::table($table)
            ->where('txn_id', '=', $txn_id)
            ->where('transaction_id', '=', $transaction_id)
            ->first();

        if ($row) {
            if (base64_encode($row->tran_data) != base64_encode($tran_data)) {
                DB::table($table)
                    ->where('id', '=', $row->id)
                    ->update([
                        'tran_data'      => $tran_data,
                        'payment_status' => $payment_status,
                        'insert_date' => date('Y-m-d H:i:s'),
                    ]);
            }
        } else {
            $data                   = [];
            $data['txn_id']         = $txn_id;
            $data['insert_date']    = date('Y-m-d H:i:s');
            $data['tran_data']      = $tran_data;
            $data['transaction_id'] = $transaction_id;
            $data['payment_status'] = $payment_status;

            DB::table($table)->insert($data);
        }
    }

    public static function cashBuyPoint($transaction_id)
    {
        $cashBuyPoint       = false;
        $jcash = 0;
        $transaction        = Transaction::find($transaction_id);
        $transactionDetails = DB::table('jocom_transaction_details')
            ->where('transaction_id', '=', $transaction_id)
            ->get();

        foreach ($transactionDetails as $transactionDetail) {
            $pointTypeId = array_get(Config::get('constants.POINTS'), $transactionDetail->product_id);

            if ($pointTypeId) {
                $cashBuyPoint      = true;
                $pointType         = PointType::find($pointTypeId);
                $pointUser         = PointUser::getOrCreate($transaction->buyer_id, $pointTypeId, true);
                $pointTransaction  = PointTransaction::where('point_user_id', '=', $pointUser->id)
                    ->where('transaction_id', '=', $transaction->id)
                    ->first();
                if($transactionDetail->sku == 'JC-0000000034288'){
                     $jcash = 1;
                } 
                
                // if($transaction->buyer_username == 'maruthujocom'){
                //      $jcash = 1;
                // }
                
                if($jcash == 0){
                if ( ! $pointTransaction) {
                    $rate              = 1 / $pointType->redeem_rate;
                    $point             = $transactionDetail->unit;
                    $pointUser->point += $point;
                    $pointUser->save();

                    PointTransaction::updateOrCreate([
                        'point_user_id'   => $pointUser->id,
                        'transaction_id'  => $transaction_id,
                    ], [
                        'point_user_id'   => $pointUser->id,
                        'point'           => $point,
                        'rate'            => $pointType->redeem_rate,
                        'balance'         => $pointUser->point,
                        'point_action_id' => PointAction::CASH_BUY,
                        'code'            => hash('sha256', "{$pointUser->id}|".date('Y-m-d H:i:s')."|{$point}|{$pointUser->point}"),
                        'transaction_id'  => $transaction_id,
                        'created_at'      => date('Y-m-d H:i:s'),
                    ]);
                }}
            } else {
                break; // Purchase item and point shall be separated
            }
        }

        return $cashBuyPoint;
    }
    
    public function scopeCheckout_transaction_qoo10($query, $post = [])
    {
        $returnData = [
            'status'  => 'error',
            'message' => '101',
        ];

        // remove for CMS 2.9.0
        // $delivery_charges = Fees::get_delivery_charges();
        // $process_fees     = Fees::get_process_fees();

        // $temp_gst_process  = 0;
        // $temp_gst_delivery = 0;
        // $temp_gst_rate     = 0;

        // $gst_status = Fees::get_gst_status();
        // if ($gst_status == '1') {
        //     $temp_gst_rate     = Fees::get_gst();
        //     $temp_gst_process  = round(($process_fees * $temp_gst_rate / 100), 2);
        //     $temp_gst_delivery = round(($delivery_charges * $temp_gst_rate / 100), 2);
        // }
        // End of remove for CMS 2.9.0

        // $feesrow = DB::table('jocom_fees')
        // ->select('*')
        // ->find(1);

        // $delivery_charges = $feesrow->delivery_charges;
        // $process_fees = $feesrow->process_fees;

        // if with qrcode
        if (isset($post['qrcode']) && is_array($post['qrcode'])) {
            // $login = ($post['devicetype'] == "manual") ? 'yes' : Customer::check_login($post['user'], $post['pass']);

            // if ($login == 'yes')
            // {
            //     $urow = Customer::where('username', '=', $post['user'])->first();
            // }
            // else
            // {
            //     // Invalid Buyer
            //     $returnData['message'] = '102';
            //     return $returnData;
            // }

            //to amend md5 for password check later
            // $urow = Customer::where('username', '=', $post['user'])->where('password', '=', md5($post['pass']))->first();            

            $urow = Customer::where('username', '=', $post['user'])->first();
            
            //valid login
            if ($urow != null) {
                $error = false;
                $accountStatus = $urow->active_status;
                $returnData['userinfo'] = array(
                    "userEmail"=>$urow->email,
                    "username"=>$urow->username,
                    );

                // first time buyer no processing and shipping fees
                // $buybefore = Transaction::where('buyer_username', '=', $urow->username)
                //                         ->where(function($query)
                //                         {
                //                             $query->where('status', '=', 'completed')
                //                                   ->orWhere('status', '=', 'refund');
                //                         })
                //                         ->first();

                // if (count($buybefore) <= 0)
                // {
                //     $delivery_charges = 0;
                //     $process_fees = 0;

                //     $temp_gst_process = 0;
                //     $temp_gst_delivery = 0;
                // }
                // end of first time buyer no processing and shipping fees
                
                

                // Get Country
                $country_row = DB::table('jocom_countries')
                    ->select('*')
                    ->where('id', '=', $post['delivery_country'])->first();

                // Get State
                $state_row = DB::table('jocom_country_states')
                    ->select('*')
                    ->where('id', '=', $post['delivery_state'])
                    ->where('country_id', '=', $post['delivery_country'])
                    ->first();

                // Get Zone
                $check_zone = [];
                $city_name  = '';
                $city_id    = 0;

                if ($post['delivery_city'] != null or $post['delivery_city'] != '') {
                    // Get Cities
                    $city_row = DB::table('jocom_cities')
                        ->select('*')
                        ->where('id', '=', $post['delivery_city'])
                        ->where('state_id', '=', $post['delivery_state'])
                        ->first();

                    if (count($city_row) > 0) {
                        $city_name = $city_row->name;
                        $city_id   = $city_row->id;
                    }

                    // Get Zone
                    $zone = DB::table('jocom_zone_cities')
                        ->select('*')
                        ->where('city_id', '=', $post['delivery_city'])->get();
                } else {
                    // Get Cities
                    $city_row = '1';

                    // Get Zone
                    $zone = DB::table('jocom_zone_states')
                        ->select('*')
                        ->where('states_id', '=', $post['delivery_state'])->get();
                }

                if ($zone == null) {
                    $error = true;
                    $zone_query == null;
                    $returnData['message'] = '105';
                    // $code['105'] = 'Invalid location selected.';
                } else {
                    foreach ($zone as $zone_row) {
                        $check_zone[] = $zone_row->zone_id;
                    }
                    if (sizeof($check_zone) == 0) {
                        $check_zone[] = 0;
                    }

                    $zone_query = DB::table('jocom_zones')
                        ->select('*')
                        ->where('country_id', '=', $post['delivery_country'])
                        ->whereIn('id', $check_zone)
                        ->get();
                }
                
                
                
                

                if ($country_row == null) {
                    $error                 = true;
                    $returnData['message'] = '103';
                    // $code['103'] = 'Invalid country.';
                } elseif ($state_row == null) {
                    $error                 = true;
                    $returnData['message'] = '104';
                    // $code['104'] = 'Invalid state.';
                } elseif ($city_row == null) {
                    $error                 = true;
                    $returnData['message'] = '112';
                    // $code['104'] = 'Invalid city.';
                } elseif ($zone_query == null) {
                    $error                 = true;
                    $returnData['message'] = '105';
                    // $code['105'] = 'Invalid location selected.';
                }
                if ($accountStatus != 1) { // Return error for account not activate yet
                    $error                 = true;
                    $returnData['message'] = '114';
                    // $code['114'] = 'Account not activate.';
                } 

                // no error on location
                if ($error === false) {
                    $buyer_zone = [];
                    foreach ($zone_query as $zone_row) {
                        $buyer_zone[] = $zone_row->id;
                    }

                    $transaction_date = ($post['transaction_date'] == "") ? date('Y-m-d H:i:s') : $post['transaction_date'];

                    // Allow to add transaction
                    $transac_data = [
                        "transaction_date"    => $transaction_date,
                        "status"              => "pending",
                        "buyer_id"            => $urow->id,
                        "buyer_username"      => $urow->username,
                        "delivery_name"       => $post['delivery_name'],
                        "delivery_contact_no" => $post['delivery_contact_no'],
                        "special_msg"         => $post['special_msg'],
                        "third_party"         => isset($post['elevenstreetDeliveryCharges']) ? 1:0 ,
                        "third_party_lazada"  => isset($post['lazadaDeliveryCharges']) ? 1:0 ,
                        "third_party_qoo10"   => isset($post['qoo10DeliveryCharges']) ? 1:0 ,
                        "third_party_shopee"  => isset($post['shopeeDeliveryCharges']) ? 1:0 ,
                        "buyer_email"         => $urow->email,
                        "delivery_addr_1"     => $post['delivery_addr_1'],
                        "delivery_addr_2"     => $post['delivery_addr_2'],
                        "delivery_postcode"   => $post['delivery_postcode'],
                        "delivery_city"       => $city_name,
                        "delivery_city_id"    => $city_id,
                        "delivery_state"      => $state_row->name,
                        "delivery_country"    => $country_row->name,
                        // "delivery_charges"    => $delivery_charges,
                        "delivery_condition"  => '',
                        // "process_fees"        => $process_fees,
                        "total_amount"        => 0,
                        // "gst_rate"            => $temp_gst_rate,
                        // "gst_process"         => $temp_gst_process,
                        // "gst_delivery"        => $temp_gst_delivery,
                        // "gst_total"           => $temp_gst_process + $temp_gst_delivery,
                        "insert_by"           => $urow->username,
                        "insert_date"         => date('Y-m-d H:i:s'),
                        "modify_by"           => $urow->username,
                        "modify_date"         => date('Y-m-d H:i:s'),
                        "lang"                => $post['lang'],
                        "ip_address"          => $post['ip_address'],
                        "location"            => $post['location'],
                        'agent_id'            => object_get($urow, 'agent_id'),
                        "charity_id"          => $post['charity_id'],
                    ];

                    $transac_data_detail = [];
                    $transac_data_group  = [];
                    $c_seller            = false;

                    // for each product with qrcode
                    foreach ($post['qrcode'] as $k => $v) {
                                               // tie a product to KKW only
                        $fix_prod  = 'JC2995'; //QR code
                        $prod_name = 'Pancake';
                        if ($post['qrcode'][$k] == $fix_prod and $urow->username != 'kkwoodypavilion') {
                            $error      = true;
                            $returnData = [
                                'status'  => 'error',
                                'message' => '110',
                                'kkwprod' => $prod_name,
                            ];
                            break;
                        }
                        // end tie to...

                        // temporary allow a user to buy once only
                        // $cybersales      = ['JC5211']; //QR code
                        // $cybersales_name = '100 Plus Minuman Isotonik (BUY 1 FREE 1) more than one(1)';
                        // if (in_array($post['qrcode'][$k], $cybersales)) {
                        //     $buy_again = DB::table('jocom_transaction AS a')
                        //         ->select('a.id')
                        //         ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
                        //         ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
                        //         ->where('a.buyer_username', '=', $urow->username)
                        //         ->where('a.status', '=', 'completed')
                        //         ->where('c.qrcode', '=', $post['qrcode'][$k])
                        //         ->first();

                        //     if ($post['uuid'] != '')
                        //     {
                        //         $buy_again_uuid = DB::table('jocom_transaction AS a')
                        //             ->select('a.id')
                        //             ->leftJoin('jocom_transaction_details AS b', 'a.id', '=', 'b.transaction_id')
                        //             ->leftJoin('jocom_products AS c', 'b.product_id', '=', 'c.id')
                        //             ->leftJoin('jocom_user AS d', 'a.buyer_id', '=', 'd.id')
                        //             ->where('d.uuid', '=', $post['uuid'])
                        //             ->where('a.status', '=', 'completed')
                        //             ->where('c.qrcode', '=', $post['qrcode'][$k])
                        //             ->first();
                        //     }                            

                        //     if (count($buy_again) > 0 or count($buy_again_uuid) > 0 or $post['qty'][$k] > 1) {
                        //         $temp_cyber_name = DB::table('jocom_products')->select('name')->where('qrcode', '=', $post['qrcode'][$k])->first();
                        //         $cybersales_name = $temp_cyber_name->name.' more than one(1)';
                        //         $error           = true;
                        //         $returnData      = [
                        //             'status'  => 'error',
                        //             'message' => '110',
                        //             'kkwprod' => $cybersales_name,
                        //         ];
                        //         break;
                        //     }
                        // }
                        // end temporary allow...

                        if ($error === true) {
                            continue;
                        }
                        
                        $platform_price = 0;
                        $qrcode       = $post['qrcode'][$k];

                        $qty          = $post['qty'][$k];
                        $price_option = $post['price_option'][$k];
                        $qprice = $post['qprice'][$k];
                        
                        $shopeepirce  = $post['shopee_original_price'][$k];
                        $pgmallpirce  = $post['pgmall_original_price'][$k];

                        // valid qrcode and qty in numeric
                        if ($qrcode != '' && is_numeric($qty) && $qty > 0) {
                            $query_row = DB::table('jocom_product_and_package')
                                ->select('*')
                                ->where('qrcode', '=', $qrcode)->first();

                            if ($query_row != null) {
                                // not package
                                if (substr($query_row->id, 0, 1) != 'P') {
                                    $prow            = $query_row;
                                    $tmp_return_data = MCheckout::add_transaction_detail_qoo10($prow, $price_option, $qty,$qprice, $buyer_zone, $returnData, $transac_data, $transac_data_detail, $c_seller, $error, "", $urow->username);

                                    $returnData          = $tmp_return_data["returnData"];
                                    $transac_data        = $tmp_return_data["transac_data"];
                                    $transac_data_detail = $tmp_return_data["transac_data_detail"];
                                    $error               = $tmp_return_data["error"];
                                    $c_seller            = $tmp_return_data["c_seller"];
                                } else {
                                    // Get Package Products
                                    $get_pro_query = DB::table('jocom_product_package_product')
                                        ->select('*')
                                        ->where('package_id', '=', substr($query_row->id, 1))->get();

                                    // for each package
                                    foreach ($get_pro_query as $get_pro_row) {
                                        $price_option = $get_pro_row->product_opt;
                                        $pro_qty      = $get_pro_row->qty * $qty;

                                        $popt_row = DB::table('jocom_product_price')->find($get_pro_row->product_opt);

                                        // with price
                                        if ($popt_row != null) {
                                            $prow = DB::table('jocom_products')->find($popt_row->product_id);

                                            if ($prow != null) {
                                                $tmp_return_data = MCheckout::add_transaction_detail_qoo10($prow, $price_option, $pro_qty,$qprice, $buyer_zone, $returnData, $transac_data, $transac_data_detail, $c_seller, $error, $query_row->sku, "");
                                                
                                                $returnData          = $tmp_return_data["returnData"];
                                                $transac_data        = $tmp_return_data["transac_data"];
                                                $transac_data_detail = $tmp_return_data["transac_data_detail"];
                                                $error               = $tmp_return_data["error"];
                                                $c_seller            = $tmp_return_data["c_seller"];
                                            } else {
                                                $error = true;
                                                // Error on product transaction
                                                $returnData['message'] = '106';
                                            }
                                        } else {
                                            $error = true;
                                            // Error on product transaction
                                            $returnData['message'] = '106';
                                        } //end for with price

                                    } // end of for each package

                                    if (isset($transac_data_group[$query_row->sku])) {
                                        $transac_data_group[$query_row->sku]["unit"] += $qty;
                                    } else {
                                        $transac_data_group[$query_row->sku] = [
                                            "sku"  => $query_row->sku,
                                            "unit" => $qty,
                                        ];
                                    }
                                } // end not package
                            } else {
                                $error = true;
                                // Error on product transaction
                                $returnData['message'] = '106';
                            }
                        } // end of valid qrcode and qty in numeric

                    } // end of each product with qrcode

                    // to check special pricing meet minimum purchase requirement.
                    $group_total = [];
                    foreach ($transac_data_detail as $key => $trow) {
                        if ($trow['sp_group_id'] != 0) {
                            if ( ! isset($group_total[$trow['sp_group_id']])) {
                                $group_total[$trow['sp_group_id']] = 0;
                            }
                            $group_total[$trow['sp_group_id']] += $trow['total'];
                        }
                    }

                    foreach ($group_total as $key => $value) {
                        $groupmin = DB::table('jocom_sp_group')
                            ->select('min_purchase')
                            ->where('id', '=', $key)
                            ->first();

                        if ($group_total[$key] < $groupmin->min_purchase) {
                            $error                 = true;
                            $returnData['message'] = '111';
                            // $code['111'] = 'Oops, you do not meet the minimum purchase requirement for special pricing.';
                        }
                    }

                } // end of no error on location

                // no error on product, proceed to checkout
                if ($error === false) {
                    // $transac_data['total_amount'] = $transac_data['total_amount'] + $transac_data['process_fees'];
                    $involved_seller = [];
                    foreach ($transac_data_detail as $ddatarow) {
                        $involved_seller[] = $ddatarow["seller_username"];
                    }

                    $sellerData = [];
                    $seller     = DB::table('jocom_seller')
                        ->select('*')
                        ->whereIn('username', $involved_seller)
                        ->get();
                    foreach ($seller as $jseller_row) {
                        $sellerData[$jseller_row->username] = $jseller_row;
                    }


                    // Calculate delivery fees CMS 2.9.0
                    $temp_weight = array();
                    $temp_zone = [];

                    $delivery_charges = Fees::GetTotalDelivery($transac_data_detail);
                    
                    $process_fees     = Fees::get_process_fees();

                    $temp_gst_process  = 0;
                    $temp_gst_delivery = 0;
                    $temp_gst_rate     = 0;

                    $gst_status = Fees::get_gst_status();
                    if ($gst_status == '1') {
                        $temp_gst_rate     = Fees::get_gst();
                        $temp_gst_process  = round(($process_fees * $temp_gst_rate / 100), 2);
                        $temp_gst_delivery = round(($delivery_charges * $temp_gst_rate / 100), 2);
                    }
                        
                    $transac_data['delivery_charges']   = $delivery_charges;
                    $transac_data['process_fees']       = 0;
                    $transac_data['gst_rate']           = 0;
                    $transac_data['gst_process']        = 0;
                    $transac_data['gst_delivery']       = 0;
                    $transac_data['gst_total']          = 0;

                    $transac_data['delivery_condition'] = "Delivery fees is set in the item";

                    // End of Calculate delivery fees CMS 2.9.0
                    
                    
                    

                    // $transac_data['delivery_condition'] = "";
                    // /*
                    // * Calculation for the delivery fees (for multiple items)
                    // * If all items delivery fees is 0 then the delivery fees will be 0
                    // * If all of the items delivery fees is same then it will be charges it same charges
                    // * If one of the items delivery fees is not 0 then it will be charges standard charges "RM 10"
                    // */

                    // // for 0 delivery fees
                    // $total_delivery                     = 0;
                    // $special_cust                       = 0;
                    // $transac_data['delivery_condition'] = "Delivery fees is set 0";
                    // foreach ($transac_data_detail as $k => $drow) {
                    //     if ($drow["delivery_fees"] != 0) {
                    //         $total_delivery += $drow["delivery_fees"];
                    //         $transac_data['delivery_condition'] = "Delivery fees is set in the item";
                    //     }

                    //     // free delivery for special pricing, temporary disable special cust waiver
                    //     // if ($drow["sp_group_id"] != 0) {
                    //     //     $special_cust++;
                    //     // }

                    // }

                    $zeroDelivery = 0;

                    // if ($total_delivery == 0 or $special_cust > 0) {
                    // if ($special_cust > 0) {
                    //     $delivery_charges             = 0;
                    //     $transac_data['gst_delivery'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_delivery;

                    //     $process_fees                = 0;
                    //     $transac_data['gst_process'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_process;

                    //     $zeroDelivery = 1;
                    // }

                    //  Delivery Fees RM5 until 31/12/2016
                    // first time buyer free shipping fees with minimum purchase of RM30 until 31/12/2016
                    $buybefore = Transaction::where('buyer_username', '=', $urow->username)
                        ->where(function ($query) {
                            $query->where('status', '=', 'completed')
                                ->orWhere('status', '=', 'refund');
                        })
                        ->first();

                    if (count($buybefore) <= 0 and $transac_data['total_amount'] >= 30 and $zeroDelivery == 0)
                    {
                        $delivery_charges             = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;

                        // $process_fees                = 0;
                        // $transac_data['gst_process'] = 0;
                        // $transac_data['gst_total'] -= $temp_gst_process;

                        $zeroDelivery = 1;
                    }
                    // end of first time buyer no processing and shipping fees

                    // //  Delivery Fees Waiver until 31/10/2016
                    // if ($total_delivery == 0 and $transac_data['total_amount'] >= 50 and $zeroDelivery == 0)
                    // {
                    //     $delivery_charges             = 0;
                    //     $transac_data['gst_delivery'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_delivery;

                    //     // $process_fees                = 0;
                    //     // $transac_data['gst_process'] = 0;
                    //     // $transac_data['gst_total'] -= $temp_gst_process;

                    //     $zeroDelivery = 1;
                    // }

                    // first time buyer no processing and shipping fees with minimum purchase of RM30 - temporary set to RM100 until 10/04/2016
                    // $buybefore = Transaction::where('buyer_username', '=', $urow->username)
                    //     ->where(function ($query) {
                    //         $query->where('status', '=', 'completed')
                    //             ->orWhere('status', '=', 'refund');
                    //     })
                    //     ->first();

                    // if (count($buybefore) <= 0 and $transac_data['total_amount'] >= 1000000 and $zeroDelivery == 0)
                    // {
                    //     $delivery_charges             = 0;
                    //     $transac_data['gst_delivery'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_delivery;

                    //     $process_fees                = 0;
                    //     $transac_data['gst_process'] = 0;
                    //     $transac_data['gst_total'] -= $temp_gst_process;

                    //     $zeroDelivery = 1;
                    // }
                    // end of first time buyer no processing and shipping fees

                    // if(sizeof($transac_data_detail) > 1) {
                    //     $delivery_fees_check = 0;
                    //     $transac_data['delivery_condition'] = "Delivery fees is set 0";
                    //     foreach($transac_data_detail as $k => $drow) {
                    //         if($drow["delivery_fees"] != 0) {
                    //             if($delivery_fees_check == 0) {
                    //                 // If all of the items delivery fees is same then it will be charges it same charges
                    //                 $delivery_fees_check = $drow["delivery_fees"];
                    //                 $transac_data['delivery_condition'] = "All items delivery fees is same";
                    //             } else if($drow["delivery_fees"] != $delivery_fees_check) {
                    //                 // If one of the items delivery fees is not 0 then it will be charges standard charges "RM 10"
                    //                 $delivery_fees_check = $delivery_charges;
                    //                 $transac_data['delivery_condition'] = "One or more items delivery fees is not same";
                    //             }
                    //         }
                    //     }
                    // } else {
                    //     $delivery_fees_check = $transac_data_detail[0]["delivery_fees"];
                    //     $transac_data['delivery_condition'] = "Delivery fees is set in the item";
                    // }
                /** FREE DELIVERY AND PROCESSING FOR MYCYBERSALE2016 WEB CHECKOUT **/
                if($post['isDelivery'] == "1"){
                    
                        $delivery_charges = 7.50;
                        $process_fees = 0;
                        $transac_data['gst_delivery'] = 0;
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                    
                }
                /** FREE DELIVERY AND PROCESSINF FOR MYCYBERSALE2016 WEB CHECKOUT **/
                    
                    if(isset($post['elevenstreetDeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['elevenstreetDeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['elevenstreetDeliveryCharges'] * 6 ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        
                    }
                    
                    // Lazada Delivery Charges
                    if(isset($post['lazadaDeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['lazadaDeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['lazadaDeliveryCharges'] * 6 ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        
                    }

                    //Qoo10 Delivery Charges

                    if(isset($post['qoo10DeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['qoo10DeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        // $gst_delivery = number_format(($post['qoo10DeliveryCharges'] * 6 ) / 100, 2, '.', '');
                        // $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        // $transac_data['gst_total'] -= $temp_gst_delivery;
                        // $transac_data['gst_total'] += $gst_delivery;
                        
                    }

                    //Shopee Delivery Charges
                    
                    if(isset($post['shopeeDeliveryCharges'])){
                    
                        $delivery_charges = number_format($post['shopeeDeliveryCharges'], 2, '.', '');
                        $process_fees = 0;

                        $gst_delivery = number_format(($post['shopeeDeliveryCharges'] * 6 ) / 100, 2, '.', '');
                        $transac_data['gst_delivery'] = number_format($gst_delivery, 2, '.', '');
                        $transac_data['gst_total'] -= $temp_gst_delivery;
                        $transac_data['gst_total'] += $gst_delivery;
                        
                    }

                    unset ($transac_data['third_party']);
                    unset ($transac_data['third_party_lazada']);
                    unset ($transac_data['third_party_qoo10']);
                    unset ($transac_data['third_party_shopee']);
                    
                    $transac_data['delivery_charges'] = $delivery_charges;
                    
                    $transac_data['process_fees']     = $process_fees;
                    $transac_data['total_amount'] += $transac_data['delivery_charges'] + $transac_data['process_fees'];
                    $transac_data['id'] = $post['transaction_id'];
                    // $insert_data = array();
                    // foreach($transac_data as $key => $value) {
                    //     $insert_data['`' . $key . '`'] = $value;
                    // }

                    $insert_id = DB::table('jocom_transaction_qoo10')->insertGetId($transac_data);
                    //$insert_audit = General::audit_trail('Insert into jocom_transaction', 'MCheckout.php', 'checkout_transaction()', 'Add Transaction', $transac_data['insert_by']);

                    foreach ($transac_data_detail as $drow) {
                        $drow["transaction_id"] = $post['transaction_id'];

                        $insert_id_details = DB::table('jocom_transaction_details_qoo10')->insertGetId($drow);
                    }

                    foreach ($transac_data_group as $tdgrow) {
                        $tdgrow["transaction_id"] = $post['transaction_id'];

                        $insert_id_d_g = DB::table('jocom_transaction_details_group')->insertGetId($tdgrow);
                    }

                    $returnData['transaction_id'] = $post['transaction_id'];
                    $returnData['status']         = 'success';
                    $returnData['message']        = 'valid';
                    $returnData['devicetype']     = $post['devicetype'];
                    $returnData['lang']           = $post['lang'];

                } // end of no error on product, proceed to checkout

            } else {
                // Invalid Buyer
                $returnData['message'] = '102';
            } //end valid login
        } //end if with qrcode

        return $returnData;
    }

    /**
     * Add details to transaction
     * @param  [type]  $query               [description]
     * @param  [type]  $prow                [description]
     * @param  [type]  $price_option        [description]
     * @param  [type]  $qty                 [description]
     * @param  [type]  $buyer_zone          [description]
     * @param  array   $returnData          [description]
     * @param  array   $transac_data        [description]
     * @param  array   $transac_data_detail [description]
     * @param  boolean $c_seller            [description]
     * @param  boolean $error               [description]
     * @param  string  $type                [description]
     * @return [type]                       [description]
     */
    public function scopeAdd_transaction_detail_qoo10($query, $prow, $price_option, $qty,$qprice, $buyer_zone, $returnData = [], $transac_data = [], $transac_data_detail = [], $c_seller = false, $error = false, $type = "", $buyer = "")
    {
        $sp_ind     = 0;
        $sp_price   = 0;

        $zone_id        = 0;
        $p_weight       = 0;
        $total_weight   = 0;
        $listOutStock = array();


        if (substr($price_option, 0, 1) == 'S') {
            // Get Special Product Price
            $price_id = substr($price_option, 1);

            $price_row = DB::table('jocom_sp_product_price AS a')
                ->select('a.label_id AS id', 'a.sp_group_id', 'b.label', 'b.label_cn', 'b.label_my', 'b.seller_sku', 'b.p_weight', 'a.price', 'a.price_promo', 'a.qty', 'a.p_referral_fees', 'a.p_referral_fees_type','a.disc_amount', 'a.disc_type', 'a.default', 'a.product_id', 'a.status', 'c.gst', 'c.gst_value')
                ->leftjoin('jocom_product_price AS b', 'b.id', '=', 'a.label_id')
                ->leftJoin('jocom_products AS c', 'a.product_id', '=', 'c.id')
                ->where('a.id', '=', $price_id)
                ->where('a.product_id', '=', $prow->id)
                ->first();
           
            if (count($price_row) <= 0) {
                // Get Public Product Price
                $price_row = DB::table('jocom_product_price AS a')
                    ->select('a.*', 'b.gst', 'b.gst_value')
                    ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                    ->where('a.id', '=', $price_id)
                    ->where('a.product_id', '=', $prow->id)
                    ->first();
            }
            else
            {
                $sp_ind = $price_row->sp_group_id;
                $price_row->qty = $qty+1;
                
                switch ($price_row->disc_type) {
                    case '%' : 
                            $discount = 1 - ($price_row->disc_amount/100);
                            $sp_price = number_format($price_row->price * $discount, 2);    
                        break;

                    case 'N' :
                            $sp_price = number_format($price_row->price - $price_row->disc_amount, 2);
                        break;
                    default :
                        $sp_price = $price_row->price;
                        break;
                }
                
                $price_row->price = $sp_price;
            }

        } else {
            // Get Public Product Price
            $price_row = DB::table('jocom_product_price AS a')
                ->select('a.*', 'b.gst', 'b.gst_value')
                ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                ->where('a.id', '=', $price_option)
                ->where('a.product_id', '=', $prow->id)
                ->first();
        }

        // Get Product Delivery Fees
        $dl_row = DB::table('jocom_product_delivery')
            ->select('*')
            ->where('product_id', '=', $prow->id)
            ->whereIn('zone_id', $buyer_zone)
            ->first();

        // with product price and delivery fees
        if ($dl_row != null && $price_row != null) {
            $dl_fees = $dl_row->price;

            $zone_id        = $dl_row->zone_id;

            $p_weight = $price_row->p_weight;
            $total_weight   = $p_weight * $qty;
            
            
            
            
            // DEFINE SELLER //
            
            $delivery_city_id = $transac_data['delivery_city_id'];
            
            $City = City::find($delivery_city_id);
            $StateID = $City->state_id;
            
            
            $ProductSellerDefault = ProductSeller::where("product_id",$prow->id)->first();
            $ProductSeller = ProductSeller::getProductSeller($prow->id);
            
            $sellerId = $ProductSellerDefault->seller_id;
                    
            foreach($ProductSeller as $key => $value) {
                $Seller = Seller::find($value->seller_id);
                if($Seller->state == $StateID){
                    $sellerId = $Seller->id;
                }
            }
            
            $srow = DB::table('jocom_seller')
                ->select('*')
                ->where('id', '=', $sellerId)
                ->first();
            
            // DEFINE SELLER //
            
            $seller = isset($srow->username) ? $srow->username : $sellerId;

            // $transac_data['total_amount'] += ($qprice * $qty);
            $before_gst3 = $qprice / 1.00;
            $transac_data['total_amount'] += round($before_gst3,2);
            // $tempitem  = (isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price);
            // $temptotal = ((isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price) * $qty);
            $before_gst2 = $qprice / 1.00;
            $tempitem  = $before_gst2;
            $temptotal = $before_gst2;
            
            $temp_gst_rate   = 0;
            $temp_gst_amount = 0;

            $parent_seller     = 0;
            $parent_gst_amount = 0;

            $gst_status = Fees::get_gst_status();
            if ($gst_status == '1') {
                $temp_gst_rate   = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                $temp_gst_amount = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value / 100 * $tempitem : 0);
                // $temp_gst_amount = round($temp_gst_amount, 2) * $qty;
                $temp_gst_amount = round($temp_gst_amount, 2);
                //$temp_gst_amount = $temp_gst_amount * $qty;
                $transac_data['gst_total'] += $temp_gst_amount;
            }

            $tempprice = isset($price_row->price_promo) && $price_row->price_promo > 0 ? $price_row->price_promo : $price_row->price;

            if (trim($srow->gst_reg_num) != "") {
                $tempgstseller = $tempprice - (isset($price_row->p_referral_fees) ? (isset($price_row->p_referral_fees_type) && $price_row->p_referral_fees_type == 'N' ? $price_row->p_referral_fees : ($price_row->p_referral_fees * ($tempprice) / 100)) : 0);

                // calculate seller gst/input tax regardless gst is inactive
                $temp_gst_sell = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                $tempgstseller = $tempgstseller * $temp_gst_sell / 100;
                $tempgstseller = round($tempgstseller, 2) * $qty;

                // $tempgstseller = $tempgstseller * $temp_gst_sell / 100 * $qty;
                // $tempgstseller = $tempgstseller * $temp_gst_rate/100 * $qty;
                // $tempgstseller = round($tempgstseller, 2);
            } else {
                $tempgstseller = 0;
            }

            // calculate gst for seller parent
            if ($srow->parent_seller != 0) {
                $parent_seller = $srow->parent_seller;

                $parent_row = DB::table('jocom_seller')
                    ->select('*')
                    ->where('id', '=', $srow->parent_seller)
                    ->first();

                if (trim($parent_row->gst_reg_num) != "") {
                    $parent_gst_rate   = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value : 0);
                    $parent_gst_amount = (isset($price_row->gst) && $price_row->gst == 2 ? $price_row->gst_value / 100 * $tempitem : 0);
                    $parent_gst_amount = round($parent_gst_amount, 2) * $qty;
                }
            }
            // end of calculate gst for seller parent

                                // tie a product to KKW only
            $fix_prod = '7750'; //label_id
            if ($price_row->id == $fix_prod and $buyer == 'kkwoodypavilion') {
                $transac_data_detail[] = [
                    "product_id"           => $prow->id,
                    "sku"                  => $prow->sku,
                    "price_label"          => $price_row->label,
                    "seller_sku"           => $price_row->seller_sku,
                    "price"                => 0,
                    "unit"                 => $qty,
                    "p_referral_fees"      => $price_row->p_referral_fees,
                    "p_referral_fees_type" => $price_row->p_referral_fees_type,
                    "delivery_time"        => ($prow->delivery_time == '' ? '24 hours' : $prow->delivery_time),
                    "delivery_fees"        => $dl_fees,
                    // "delivery_fees" => 0,
                    "seller_id"            => $srow->id,
                    "seller_username"      => $seller,
                    "gst_rate_item"        => 0,
                    "gst_amount"           => 0,
                    "gst_seller"           => 0,
                    "total"                => 0,
                    "transaction_id"       => "",
                    "p_option_id"          => $price_row->id,
                    "product_group"        => $type,
                    "sp_group_id"          => $sp_ind,
                    "parent_seller"        => $parent_seller,
                    "parent_gst_amount"    => $parent_gst_amount,
                    "zone_id"              => $zone_id,
                    "total_weight"         => $total_weight,
                ];

                // $transac_data['total_amount'] -= ($qprice * $qty);
                $transac_data['total_amount'] -= round($before_gst3,2);
                $transac_data['gst_total'] -= $temp_gst_amount;
            } // end tie to...
            else {
                
                if($price_row->gst == 2){
                    $before_gst = $qprice / 1.00;
                    $gst_amount = $qprice - $before_gst;
                    $gst_seller = $qprice - $before_gst;
                    $parent_gst_amount = $qprice - $before_gst; 
                }else{
                    $before_gst = $qprice;
                    $gst_amount = 0;
                    $gst_seller = 0;
                    $parent_gst_amount = 0;
                }
                
                $transac_data_detail[] = [
                    "product_id"           => $prow->id,
                    "sku"                  => $prow->sku,
                    "price_label"          => $price_row->label,
                    "seller_sku"           => $price_row->seller_sku,
                    "price"                => $qprice / $qty,
                    "unit"                 => $qty,
                    "p_referral_fees"      => $price_row->p_referral_fees,
                    "p_referral_fees_type" => $price_row->p_referral_fees_type,
                    "delivery_time"        => ($prow->delivery_time == '' ? '24 hours' : $prow->delivery_time),
                    "delivery_fees"        => $dl_fees,
                    "seller_id"            => $srow->id,
                    "seller_username"      => $seller,
                    "gst_rate_item"        => 0, //$temp_gst_rate,
                    "gst_amount"           => 0, //round($gst_amount, 2),
                    "gst_seller"           => round($gst_seller, 2),
                    "total"                => round($before_gst, 2),
                    "transaction_id"       => "",
                    "p_option_id"          => $price_row->id,
                    "product_group"        => $type,
                    "sp_group_id"          => $sp_ind,
                    "parent_seller"        => $parent_seller,
                    "parent_gst_amount"    => round($parent_gst_amount, 2),
                    "zone_id"              => $zone_id,
                    "total_weight"         => $total_weight,
                    "original_price"       => 0,
                ];
                
            }

            // allow to checkout even no quantity for 11Street, Lazada and Qoo10
            if($transac_data['third_party'] != 1 && $transac_data['third_party_lazada'] != 1 && $transac_data['third_party_qoo10'] != 1 && $transac_data['third_party_shopee'] != 1){

                if ((int)$price_row->qty < (int)$qty) {
                    $error                 = true;
                    $returnData['message'] = '107';
                    $returnData['outStockList'][] = array(
                        "productSkU" => $prow->sku,
                        "productLabel" => $price_row->label,
                        "productName" => $prow->name,
                        "productID" => $prow->id,
                    );
                   
                }
            }

            // if ($c_seller === false) {
            //     $c_seller = $seller;
            // } else if ((int)$price_row->qty < (int)$qty) {
            //     $error                 = true;
            //     $returnData['message'] = '107';
               
            // }

        } else {
            $error = true;
            if ($price_row != null) {
                $returnData['message'] = '108';
            } else {
                $returnData['message'] = '109';
            }

        } // end of with product price and delivery fees

        return ["returnData" => $returnData, "transac_data" => $transac_data, "transac_data_detail" => $transac_data_detail, "c_seller" => $c_seller, "error" => $error];
    }

    public function scopeGenerateQoo10Inv($query, $id, $manual = null,$deliveryservice = false)
    {
        //$gotPO = MCheckout::checkfile($id, "PO");
        $gotINV = MCheckout::checkfile($id, "INV");
        //$gotDO = MCheckout::checkfile($id, "DO");
        // var_dump($gotINV);

        if ($gotINV == 'yes' && $manual != true) {
            return 'no';
            $id = null;
        }

        if ($id != null) {

            $trans = TransactionQoo10::find($id);

            // valid transaction
            if ($trans != null) {
                $buyer = Customer::where('username', '=', $trans->buyer_username)->first();

                $paypal = TPayPal::where('transaction_id', '=', $id)->first();

                $coupon = TCoupon::where('transaction_id', '=', $id)->first();

                $payment_id   = 0;
                $parentSeller = "";

                if (count($paypal) > 0) {
                    $payment_id = $paypal->txn_id;
                }

                $general = [
                    "invoice_no"          => "",
                    "invoice_date"        => date('d/m/Y'),
                    // "invoice_date" => isset($trans->invoice_date) ? date("d-m-Y", strtotime($trans->invoice_date)) : date('d/m/Y'),
                    "payment_terms"       => "cash/cc",
                    "transaction_id"      => $trans->id,
                    "payment_id"          => $payment_id,
                    "transaction_date"    => date("d-m-Y", strtotime($trans->transaction_date)),

                    "buyer_name"          => isset($buyer->full_name) ? $buyer->full_name : "",
                    "buyer_email"         => isset($buyer->email) ? $buyer->email : "",
                    "delivery_address_1"  => ($trans->delivery_addr_1 != '' ? $trans->delivery_addr_1."," : ''),
                    "delivery_address_2"  => ($trans->delivery_addr_2 != '' ? $trans->delivery_addr_2."," : ''),
                    "delivery_address_3"  => ($trans->delivery_postcode != '' ? $trans->delivery_postcode." " : '').($trans->delivery_city != '' ? $trans->delivery_city."," : ''),
                    "delivery_address_4"  => ($trans->delivery_state != '' ? $trans->delivery_state.", " : '').$trans->delivery_country.".",
                    "special_instruction" => ($trans->special_msg != "" ? $trans->special_msg : "None"),
                    "delivery_contact_no" => $trans->delivery_contact_no,
                ];

                // INV to Customer
                $sellerPO = MCheckout::processQoo10INV($trans, $general, $paypal, $coupon, $parentSeller, 1);
                

                // no INV from e37 for point purchase
                if ($trans->no_shipping == 1) {
                    return 'yes';
                }
                if($deliveryservice){
                    // no INV from e37 for delivery services
                    return 'yes';
                }else{
                // INV from e37
                $involved_parent = DB::table('jocom_transaction_details_qoo10')
                    ->where('transaction_id', '=', $trans->id)
                    ->where('parent_seller', '!=', 0)
                    ->groupBy('parent_seller')
                    ->lists('parent_seller');

                foreach ($involved_parent as $parentSeller) {
                    //$sellerPO = MCheckout::processQoo10INV($trans, $general, $paypal, $coupon, $parentSeller, 0);
                }
                
                
                $trans->status = "completed";
                $trans->save();

                return 'yes';

                }
                
            } // end valid transaction
        } else {
            return 'no';
        }
    }

    public function processQoo10INV($trans, $general, $paypal, $coupon, $parentSeller, $toCustomer)
    {
        $inv_running = "q10_invoice";
        $file_path   = Config::get('constants.INVOICE_PDF_FILE_PATH')."/";
        $inv_prefix  = "QINV-";

        $issuer = [
            "issuer_name"      => "Tien Ming Distribution Sdn Bhd (1537285-T)",
            "issuer_address_1" => "10,Jalan Str 1,",
            "issuer_address_2" => "Saujana Teknologi Park,",
            "issuer_address_3" => "Rawang,",
            "issuer_address_4" => "48000 Rawang, Selangor, Malaysia.",
            "issuer_tel"       => "Tel: +603 6734 8744",
            "issuer_gst"       => "",
        ];

        if ($toCustomer == 1) {
            $product = DB::table('jocom_transaction_details_qoo10 AS a')
                ->select('a.*', 'd.name')
                ->leftJoin('logistic_transaction_item AS d', 'd.product_id', '=', 'a.product_id')
                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                ->where('a.transaction_id', '=', $trans->id)
                ->where('c.main', '=', '1')
                ->orderBy('c.category_id')
                ->orderBy('b.name')
                ->groupBy('a.p_option_id')
                ->get();

            $group = DB::table('jocom_transaction_details_group AS a')
                ->select('a.*', 'b.name')
                ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                ->where('a.transaction_id', '=', $trans->id)
                ->orderBy('b.category')
                ->orderBy('b.name')
                ->get();

            $points = TPoint::transaction($trans->id)->get();

            // Earned points
            $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_transactions.point_action_id', '=', PointAction::EARN)
                ->get();

            $earnedId = [];

            foreach ($earnedPoints as $earnedPoint) {
                $earnedId[] = $earnedPoint->id;
            }

            $reversalPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->where('point_transactions.transaction_id', '=', $trans->id)
                ->where('point_transactions.point_action_id', '=', PointAction::REVERSAL)
                ->get();

            $reversedId = [];

            foreach ($reversalPoints as $reversalPoint) {
                $reversedId[] = $reversalPoint->reversal;
            }

            $earnedPoints = DB::table('point_transactions')
                ->select('point_types.*', 'point_transactions.*')
                ->join('point_users', 'point_transactions.point_user_id', '=', 'point_users.id')
                ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
                ->whereIn('point_transactions.id', array_diff($earnedId, $reversedId))
                ->get();

        } else {
            // $coupon       = new TCoupon;
            $points       = new TPoint;
            $earnedPoints = new TPoint;

            $inv_running = "parent_inv";
            $file_path   = Config::get('constants.INVOICE_PARENT_PDF_FILE_PATH')."/";
            $inv_prefix  = "QeINV-";

            $product = DB::table('jocom_transaction_details_qoo10 AS a')
                ->select('a.*', 'd.name')
                ->leftJoin('logistic_transaction_item AS d', 'd.product_id', '=', 'a.product_id')
                ->leftJoin('jocom_products AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_categories AS c', 'b.id', '=', 'c.product_id')
                ->where('a.transaction_id', '=', $trans->id)
                ->where('a.parent_seller', '=', $parentSeller)
                ->where('c.main', '=', '1')
                ->orderBy('c.category_id')
                ->orderBy('b.name')
                ->groupBy('a.p_option_id')
                ->get();

            $group = DB::table('jocom_transaction_details_group AS a')
                ->select('a.*', 'b.name')
                ->leftJoin('jocom_product_package AS b', 'a.sku', '=', 'b.sku')
                ->leftJoin('jocom_transaction_details AS c', 'a.sku', '=', 'c.product_group')
                ->where('a.transaction_id', '=', $trans->id)
                ->where('c.parent_seller', '=', $parentSeller)
                ->groupBy('c.parent_seller')
                ->orderBy('b.category')
                ->orderBy('b.name')
                ->get();

            $sellerTable = DB::table('jocom_seller')
                ->select('country', 'state', 'city', 'company_name', 'address1', 'address2', 'postcode', 'email', 'tel_num', 'mobile_no', 'username', 'gst_reg_num','company_reg_num')
                ->where('id', '=', $parentSeller)
                ->first();

            $tempcountry = "";
            $tempstate   = "";
            $tempcity    = "";

            $sellerCountry = Country::find($sellerTable->country);
            if ($sellerCountry != null) {
                $tempcountry = $sellerCountry->name;
            }

            $sellerState = State::find($sellerTable->state);
            if ($sellerState != null) {
                $tempstate = $sellerState->name.", ";
            }

            if (is_numeric($sellerTable->city)) {
                $city_row = City::find($sellerTable->city);

                if (count($city_row) > 0) {
                    $tempcity = $city_row->name;
                }

            } else {
                $tempcity = $sellerTable->city;
            }

            $parentcmpReg="";

            if(isset($sellerTable->company_reg_num) && $sellerTable->company_reg_num !=''){
                $parentcmpReg="(".$sellerTable->company_reg_num .")";
            }

            $issuer = [
                "issuer_name"      => $sellerTable->company_name.$parentcmpReg,
                "issuer_address_1" => ($sellerTable->address1 != '' ? $sellerTable->address1."," : ''),
                "issuer_address_2" => ($sellerTable->address2 != '' ? $sellerTable->address2."," : ''),
                "issuer_address_3" => ($sellerTable->postcode != '' ? $sellerTable->postcode." " : '').($tempcity != '' ? $tempcity.", " : ''),
                "issuer_address_4" => $tempstate.$tempcountry.".",
                "issuer_tel"       => $sellerTable->tel_num.($sellerTable->tel_num != "" && $sellerTable->mobile_no != "" ? "/" : '').$sellerTable->mobile_no,
                "issuer_gst"       => $sellerTable->gst_reg_num,
            ];

            $general['buyer_name']          = "Tien Ming Distribution Sdn Bhd (1537285-T)";
            $general['buyer_email']         = "";
            $general['delivery_address_1']  = "10,Jalan Str 1,";
            $general['delivery_address_2']  = "Saujana Teknologi Park,";
            $general['delivery_address_3']  = "Rawang,";
            $general['delivery_address_4']  = "48000 Rawang, Selangor, Malaysia.";
            $general['special_instruction'] = "";
            $general['delivery_contact_no'] = "+603 6734 8744";

        }

        $haveFile = true;
        while ($haveFile === true) {
            $inv_counter = 0;

            $running = DB::table('jocom_running')
                ->select('*')
                ->where('value_key', '=', $inv_running)->first();

            if ($running != null) {
                $inv_counter = $running->counter + 1;
                $sql         = DB::table('jocom_running')
                    ->where('value_key', $inv_running)
                    ->update(['counter' => $inv_counter]);
            } else {
                $inv_counter = 1;
                $sql         = DB::table('jocom_running')->insert([
                    ['value_key' => $inv_running, 'counter' => $inv_counter],
                ]);
            }

            $numINV = $inv_prefix.str_pad($inv_counter, 5, "0", STR_PAD_LEFT);

            $general['invoice_no'] = $numINV;

            $file_name = urlencode($general['invoice_no']).".pdf";

            if ( ! file_exists($file_path."/".$file_name)) {
                $haveFile = false;
            }
        }

        if ($toCustomer == 1) {
            // update Invoice number to transaction table
            $sql = DB::table('jocom_transaction_qoo10')
                ->where('id', $trans->id)
                ->update(['invoice_no' => $numINV, 'invoice_date' => date('Y-m-d H:i:s')]);
        } else {
            $sql = DB::table('jocom_transaction_parent_invoice')->insert([
                ['transaction_id' => $trans->id, 'parent_inv' => $numINV, 'parent_seller' => $parentSeller],
            ]);
        }

        $temp_doc = array_merge($general, $issuer);

        $doc_info = json_encode($temp_doc);

        $sql = DB::table('jocom_document_data')->insert([
            ['doc_type' => $inv_running, 'doc_no' => $general['invoice_no'], 'doc_info' => $doc_info],
        ]);

        if ( ! file_exists($file_path."/".$file_name)) {
            include app_path('library/html2pdf/html2pdf.class.php');
            //New Invoice Start
            $invoiceview = "";
            $inv_newdate = Config::get('constants.NEW_INVOICE_START_DATE');

            $currentdate = $trans->transaction_date;
            if($currentdate<$inv_newdate){
                $invoiceview = 'checkout.inv_qooten_new_v2';
            }
            else 
            {
                $invoiceview = 'checkout.inv_qooten_new_v2';
            }
            //New Invoice End

            $response = View::make($invoiceview)
                ->with('display_details', $general)
                ->with('display_trans', $trans)
                ->with('display_issuer', $issuer)
                ->with('display_seller', $paypal)
                ->with('display_coupon', $coupon)
                ->with('display_product', $product)
                ->with('display_group', $group)
                ->with('display_points', $points)
                ->with('display_earns', $earnedPoints)
                ->with('toCustomer', $toCustomer);

            // $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');
            // // $html2pdf = new HTML2PDF('L', 'A4', 'en', true, 'UTF-8');
            // $html2pdf->setDefaultFont('arialunicid0');
            // // $html2pdf = new HTML2PDF('P','A4');
            // $html2pdf->WriteHTML($response);
            // //$html2pdf->Output("example.pdf");
            // $html2pdf->Output("./".$file_path."/".$file_name, 'F');

        }
    }
    
    public function registerreward($username = ''){

        try{
         
            $RewardUser = RewardUser::Campaign('REG-JPT')->first();
            // Reward
            $User = Customer::where("username",$username)->first();
            $PointUser = PointUser::where("user_id",$User->id)->where("point_type_id",1)->where("status",1)->first();
            
            if($RewardUser){
                if($PointUser ){
                    $PointUser->point = $PointUser->point + $RewardUser->total_reward;
                }else{
                    // Create Point Account
                    $PointUser = new PointUser;
                    $PointUser->user_id = $User->id;
                    $PointUser->point = 0;
                    $PointUser->point_type_id = 1;
                    $PointUser->status = 1;
                    $PointUser->save();
    
                    $PointUser->point = $PointUser->point + $RewardUser->total_reward;
                }
            }

            if($PointUser->save()){
                // Deduct Reward Counter
                $RewardUser->balance = $RewardUser->balance - 1;
                $RewardUser->save();

                // Update Details
                $RewardSchemeDetails = new RewardSchemeDetails;
                $RewardSchemeDetails->user_id = $User->id;
                $RewardSchemeDetails->scheme_id = $RewardUser->id;
                $RewardSchemeDetails->amount = $RewardUser->total_reward;
                $RewardSchemeDetails->save();
            }

            
        } catch(exception $ex){

        }

    }
    
    //TEMP Coupon Code insertion  - 14-06-2019  Start 
    private function Couponcodetemp($transid,$coupon)
    {
        
    try{
       // $boost = 0;   
        if (isset($transid)) {
            $transactionID = $transid;
                                
            $Transaction = Transaction::find($transactionID);       
            $transactionDate = $Transaction->transaction_date;      
                
            if($transactionDate >= Config::get('constants.NEW_INVOICE_V2_START_DATE')){     
            /* INCLUSIVE TEMPLATE */        
                $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout_v2' : '.checkout_view_v2';       
            }  else{        
                /* EXCLUSIVE TEMPLATE */        
                $checkout_view    = ($_POST['devicetype'] == "manual") ? '.manual_checkout' : '.checkout_view';     
            }       
            
            // Session::put('lang', $_POST['lang']);
            // Session::put('devicetype', $_POST['devicetype']);
            if (isset($coupon)) {
                $temp_xml = MCheckout::insert_coupon_code($transid, $coupon);
                $this->pointBalance($transid);
                
                $coupondata = Coupon::where("coupon_code",$coupon)->first();
                
                  
            }
            
            //to continue
            $data                   = MCheckout::get_checkout_info($transid);
            $data['transaction_id'] = $transid;
            $data['coupon_msg'] = '';
           
            
            if (isset($data['trans_query'])) {
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                // Valid Page
                
                // if($_POST['devicetype'] == "web"){
                //      return $data;
                // }else{
                //     return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                // }
                
                return true;
            } else {
                $data['message'] = '101';
                // $data['message'] = 'Invalid request. Please try again.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                return false;
            }
        } else {
            //header('location:{{asset('/')}}checkout');
            //header('location:https://www.paypal.com/cgi-bin/webscr' . $fieldStr);
            //exit();

            // return Redirect::to('checkout');
            //return Response::view('checkout/checkout_view', $data);
            return false;
        }
        
            
        }catch(Exception $ex){
              //echo $ex->getTraceAsString();
              
        }
    }
    
    private function Couponcodetempweb($transid,$coupon)
    {
        
    try{
       // $boost = 0;   
        if (isset($transid)) {
            $transactionID = $transid;
                                
            $Transaction = Transaction::find($transactionID);       
            $transactionDate = $Transaction->transaction_date;      
                
            
            // Session::put('lang', $_POST['lang']);
            // Session::put('devicetype', $_POST['devicetype']);
            if (isset($coupon)) {
                $temp_xml = MCheckout::insert_coupon_code($transid, $coupon);
                $this->pointBalance($transid);
                
                $coupondata = Coupon::where("coupon_code",$coupon)->first();
                
                  
            }
            
            // //to continue
            $data                   = MCheckout::get_checkout_info($transid);
            $data['transaction_id'] = $transid;
            // $data['coupon_msg'] = '';
           
            
            if (isset($data['trans_query'])) {
                $buyerId        = $data['trans_query']->buyer_id;
                $points         = PointUser::getPoints($buyerId, PointUser::ACTIVE_ONLY);
                $data['points'] = $points;
                // Valid Page
                
                // if($_POST['devicetype'] == "web"){
                //      return $data;
                // }else{
                //     return Response::view(Config::get('constants.CHECKOUT_FOLDER').$checkout_view, $data);
                // }
                
                return $data;
            } else {
                // $data['message'] = '101';
                // $data['message'] = 'Invalid request. Please try again.';
                // return View::make(Config::get('constants.CHECKOUT_FOLDER').'.checkout_msg_view')->with('message', $data['message']);
                // return false;
            }
        } else {
            //header('location:{{asset('/')}}checkout');
            //header('location:https://www.paypal.com/cgi-bin/webscr' . $fieldStr);
            //exit();

            // return Redirect::to('checkout');
            //return Response::view('checkout/checkout_view', $data);
            // return false;
        }
        
            
        }catch(Exception $ex){
              //echo $ex->getTraceAsString();
            //   return false; 
        }
    }
     //TEMP Coupon Code insertion  - 14-06-2019  End 
     
     public function flashsaleupdate($flid,$lbl_id,$qty){

        $fcount = 0; 

        $result = DB::table('jocom_flashsale_products')
                        ->select('*')
                        ->where('id','=',$flid)
                        ->where('label_id','=',$lbl_id)
                        ->first();

        if(count($result)>0){

            $fcount = $result->qty + $qty;
            $sql = DB::table('jocom_flashsale_products')
                        ->where('id','=',$flid)
                        ->where('label_id','=',$lbl_id)
                        ->update(array('qty' => $fcount));

        }

        return $fcount;
    }
    
    public function FreecouponItemFoc($trans_id,$couponid){


        // Append FOC item in the list
        $product_id = 0;
        $coupon_id = 0;
        $seller_flg = 0; 
        $seller_id = 0;
        $sellerstatus = 0; 
        
        // echo $trans_id.'-'.$couponid;
        
        try{
        
        if (isset($couponid)) {
        
            $rowcoupon = DB::table('jocom_coupon')
                                ->select('*')
                                ->where('coupon_code','=',$couponid)
                                ->where('is_free_item','=',1)
                               ->first();
            
            if(count($rowcoupon)>0){
                $coupon_id = $rowcoupon->id;
                $seller_flg = $rowcoupon->is_seller;
                $seller_id = $rowcoupon->seller_id;
                $coupontype = DB::table('jocom_coupon_type')
                                ->select('*')
                                ->where('coupon_id','=',$coupon_id)
                                ->first();
                    // print_r($coupontype);
                if(count($coupontype)>0){
                //  echo 'In';
                    $product_id = $coupontype->related_id;

                    if($seller_flg != 0 && $seller_id != 0){

                        $transdetails = DB::table('jocom_transaction_details')  
                                                    ->where('transaction_id','=',$trans_id)
                                                    ->where('seller_id','=',$seller_id)
                                                    ->first();
                        if(count($transdetails) == 0) {
                            $sellerstatus = 1; 
                        }                            
                    
                    }
                    // echo $sellerstatus.'-'.$trans_id.'-'.$seller_id;
                    if($sellerstatus  == 0) {
                        $price_row = DB::table('jocom_product_price AS a')
                            ->select('a.*', 'b.name', 'b.sku','b.sell_id','delivery_time')
                            ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                            ->where('a.status', '=', 1)
                            ->where('a.default','=',1)
                            ->where('a.product_id', '=', $product_id)
                            ->first();

                         if(count($price_row)>0) {

                            $checkduplicate = TDetails::where('transaction_id', '=', $trans_id)
                                                        ->where('product_id','=',$product_id)
                                                        ->first();
                            if(count($checkduplicate) == 0){
                                $zoneInfo = DB::table('jocom_product_delivery')->where("product_id",$product_id)->first();
                                $sellerInfo = DB::table('jocom_seller')->where("id",$price_row->sell_id)->first();

                                $TDetails = new TDetails;
                                $TDetails->product_id = $product_id;
                                $TDetails->product_name = $price_row->name;
                                $TDetails->sku = $price_row->sku;
                                $TDetails->price_label = $price_row->label;
                                $TDetails->price = 0;
                                $TDetails->foreign_price = 0.00;
                                $TDetails->p_referral_fees = 0.00;
                                $TDetails->p_referral_fees_type = $price_row->p_referral_fees_type;
                                $TDetails->unit = 1;
                                $TDetails->delivery_fees = 0.00;
                                $TDetails->delivery_time = $price_row->delivery_time;
                                $TDetails->seller_id = $sellerInfo->id;
                                $TDetails->seller_username = $sellerInfo->username;
                                $TDetails->disc = 0.00;
                                $TDetails->gst_rate_item = 0.00;
                                $TDetails->gst_amount = 0.00;
                                $TDetails->original_price = 0.00;
                                $TDetails->ori_price = 0.00;
                                $TDetails->gst_ori = 0.00;
                                $TDetails->actual_price = 0.00;
                                $TDetails->actual_price_gst_amount = 0.00;
                                $TDetails->transaction_id = $trans_id;
                                $TDetails->p_option_id =$price_row->id;
                                $TDetails->parent_seller = 69;
                                $TDetails->zone_id = $zoneInfo->zone_id;
                                $TDetails->total_weight = $price_row->p_weight;
                                $TDetails->original_price = 0.00;
                                $TDetails->action_type = 'FOC';
                                
                                $TDetails->save();  
                            }

                         } 
                    }

                }
            }
        }
        } catch (Exception $ex) {
            // echo $ex->getMessage();
        }
        
        
    }
    
    public function FreeItemFoc($trans_id){


        // Append FOC item in the list
        $product_id = 56177;
        $product_id_1 = 56176;
        $sum_purchase = 0;
      
        
        // echo $trans_id.'-'.$couponid;
        
        try{
        
        if (isset($trans_id)) {

            $sum_price    = TDetails::where('transaction_id', '=', $trans_id)->sum('total');
            $sum_purchase = number_format($sum_price, 2);

            if(count($sum_price)>0)
             {
            if($sum_purchase>=50)
              {
               
                    // print_r($coupontype);
        
                 
                        $price_row = DB::table('jocom_product_price AS a')
                            ->select('a.*', 'b.name', 'b.sku','b.sell_id','delivery_time')
                            ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                            ->where('a.status', '=', 1)
                            ->where('a.default','=',1)
                            ->where('a.product_id', '=', $product_id)
                            ->first();

                         if(count($price_row)>0) {

                            $checkduplicate = TDetails::where('transaction_id', '=', $trans_id)
                                                        ->where('product_id','=',$product_id)
                                                        ->first();
                            if(count($checkduplicate) == 0){
                                $zoneInfo = DB::table('jocom_product_delivery')->where("product_id",$product_id)->first();
                                $sellerInfo = DB::table('jocom_seller')->where("id",$price_row->sell_id)->first();

                                $TDetails = new TDetails;
                                $TDetails->product_id = $product_id;
                                $TDetails->product_name = $price_row->name;
                                $TDetails->sku = $price_row->sku;
                                $TDetails->price_label = $price_row->label;
                                $TDetails->price = 0;
                                $TDetails->foreign_price = 0.00;
                                $TDetails->p_referral_fees = 0.00;
                                $TDetails->p_referral_fees_type = $price_row->p_referral_fees_type;
                                $TDetails->unit = 1;
                                $TDetails->delivery_fees = 0.00;
                                $TDetails->delivery_time = $price_row->delivery_time;
                                $TDetails->seller_id = $sellerInfo->id;
                                $TDetails->seller_username = $sellerInfo->username;
                                $TDetails->disc = 0.00;
                                $TDetails->gst_rate_item = 0.00;
                                $TDetails->gst_amount = 0.00;
                                $TDetails->original_price = 0.00;
                                $TDetails->ori_price = 0.00;
                                $TDetails->gst_ori = 0.00;
                                $TDetails->actual_price = 0.00;
                                $TDetails->actual_price_gst_amount = 0.00;
                                $TDetails->transaction_id = $trans_id;
                                $TDetails->p_option_id =$price_row->id;
                                $TDetails->parent_seller = 69;
                                $TDetails->zone_id = $zoneInfo->zone_id;
                                $TDetails->total_weight = $price_row->p_weight;
                                $TDetails->original_price = 0.00;
                                $TDetails->action_type = 'FOC';
                                
                                $TDetails->save();  
                            }

                         } 
                }else if($sum_purchase>=10 && $sum_purchase<=49){
                        $price_row = DB::table('jocom_product_price AS a')
                            ->select('a.*', 'b.name', 'b.sku','b.sell_id','delivery_time')
                            ->leftJoin('jocom_products AS b', 'a.product_id', '=', 'b.id')
                            ->where('a.status', '=', 1)
                            ->where('a.default','=',1)
                            ->where('a.product_id', '=', $product_id_1)
                            ->first();

                         if(count($price_row)>0) {

                            $checkduplicate = TDetails::where('transaction_id', '=', $trans_id)
                                                        ->where('product_id','=',$product_id_1)
                                                        ->first();
                            if(count($checkduplicate) == 0){
                                $zoneInfo = DB::table('jocom_product_delivery')->where("product_id",$product_id_1)->first();
                                $sellerInfo = DB::table('jocom_seller')->where("id",$price_row->sell_id)->first();

                                $TDetails = new TDetails;
                                $TDetails->product_id = $product_id_1;
                                $TDetails->product_name = $price_row->name;
                                $TDetails->sku = $price_row->sku;
                                $TDetails->price_label = $price_row->label;
                                $TDetails->price = 0;
                                $TDetails->foreign_price = 0.00;
                                $TDetails->p_referral_fees = 0.00;
                                $TDetails->p_referral_fees_type = $price_row->p_referral_fees_type;
                                $TDetails->unit = 1;
                                $TDetails->delivery_fees = 0.00;
                                $TDetails->delivery_time = $price_row->delivery_time;
                                $TDetails->seller_id = $sellerInfo->id;
                                $TDetails->seller_username = $sellerInfo->username;
                                $TDetails->disc = 0.00;
                                $TDetails->gst_rate_item = 0.00;
                                $TDetails->gst_amount = 0.00;
                                $TDetails->original_price = 0.00;
                                $TDetails->ori_price = 0.00;
                                $TDetails->gst_ori = 0.00;
                                $TDetails->actual_price = 0.00;
                                $TDetails->actual_price_gst_amount = 0.00;
                                $TDetails->transaction_id = $trans_id;
                                $TDetails->p_option_id =$price_row->id;
                                $TDetails->parent_seller = 69;
                                $TDetails->zone_id = $zoneInfo->zone_id;
                                $TDetails->total_weight = $price_row->p_weight;
                                $TDetails->original_price = 0.00;
                                $TDetails->action_type = 'FOC';
                                
                                $TDetails->save();  
                            }

                         }
                }
              }                    
        }

        } catch (Exception $ex) {
            // echo $ex->getMessage();
        }
        
        
    }
    
}