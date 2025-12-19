<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class LogisticTransaction extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;

    protected $table = 'logistic_transaction';

    public static $rules = array(
        'delivery_name'=>'required',
        'delivery_contact_no'=>'required|numeric',
       
    );

    public static $message = array(
        'delivery_name.required'=>'The delivery name is required',
        'delivery_contact_no.required'=>'The delivery contact number is required',
    
    );

    public static function insert_trans($trans)
    {
        $new = array();
        $new['transaction_id'] = $trans->id;
        $new['transaction_date'] = $trans->transaction_date;
        $new['delivery_name'] = $trans->delivery_name;
        $new['delivery_contact_no'] = $trans->delivery_contact_no;
        $new['buyer_email'] = $trans->buyer_email;
        $new['delivery_addr_1'] = $trans->delivery_addr_1;
        $new['delivery_addr_2'] = $trans->delivery_addr_2;
        $new['delivery_postcode'] = $trans->delivery_postcode;
        $new['delivery_city'] = $trans->delivery_city;
        $new['delivery_city_id'] = $trans->delivery_city_id;
        $new['delivery_state'] = $trans->delivery_state;
        $new['delivery_state_id'] = $trans->delivery_state_id;  //Added new field - 12-01-2018
        $new['delivery_country'] = $trans->delivery_country;
        $new['special_msg'] = $trans->special_msg;
        $new['do_no'] = isset($trans->do_no) ? $trans->do_no : '';
        // $new['remark'] = '';
        $new['status'] = 0;
        $new['insert_by'] = null!==Session::get('username') ? Session::get('username') : 'mobile_app';
        $new['insert_date'] = date('Y-m-d H:i:s');
        $new['modify_by'] = '';
        $new['modify_date'] = '';

        $street = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$trans->delivery_addr_1));
        $route = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$trans->delivery_addr_2));
        $city = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$trans->delivery_city));
        $state = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$trans->delivery_state));
        $country = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$trans->delivery_country));
        $postcode = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$trans->delivery_postcode));

        $apiGoogleMapKey = Config::get('constants.GOOGLE_MAP_API_KEY');
        $URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$street.",+".$route.",+".$city."+".$postcode.",".$country."&key=".$apiGoogleMapKey;
        // print $URL;
        $ch = curl_init();
        curl_setopt($ch,CURLOPT_URL,$URL );
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $output = curl_exec($ch);
        curl_close($ch);
      
        $Mapvalue = json_decode($output);


        if($Mapvalue->status=="ZERO_RESULTS"){

            $URL = "https://maps.googleapis.com/maps/api/geocode/json?address=".$city."+".$postcode.",".$country."&key=".$apiGoogleMapKey;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$URL );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $output = curl_exec($ch);
            curl_close($ch);
        }            

        $ArrayMap = json_decode($output) ;

        $latitude = $ArrayMap->results[0]->geometry->location->lat;
        $longitute = $ArrayMap->results[0]->geometry->location->lng;

        $new['gps_latitude'] = $latitude;
        $new['gps_longitude'] = $longitute;

        $insert_id = LogisticTransaction::insertGetId($new);

        return $insert_id;        
    }

    public static function get_status($status = "")
    {
        $value = "";

        switch ($status)
        {
            case '0':
                $value = 'Pending';
                break;
            case '1':
                $value = 'Undelivered';
                break;
            case '2':
                $value = 'Partial Sent';
                break;
            case '3':
                $value = 'Returned';
                break;
            case '4':
                $value = 'Sending';
                break;
            case '5':
                $value = 'Sent';
                break;
            case '6':
                $value = 'Cancelled';
                break;                        
            default:
                $value = 'Pending';
                break;
        }

        return $value;
    }

    public static function get_status_int($status = "")
    {
        $value = "";

        switch ($status)
        {
            case 'Pending':
                $value = '0';
                break;
            case 'Undelivered':
                $value = '1';
                break;
            case 'Partial Sent':
                $value = '2';
                break;
            case 'Returned':
                $value = '3';
                break;
            case 'Sending':
                $value = '4';
                break;
            case 'Sent':
                $value = '5';
                break;
            case 'Cancelled':
                $value = '6';
                break;                        
            default:
                $value = '0';
                break;
        }

        return $value;
    }

    public static function api_transaction_status()
    {
        $data['status'] = array('Pending', 'Undelivered', 'Partial Sent', 'Returned', 'Sending', 'Sent', 'Cancelled');
        return $data;
    }

    public static function api_trackingNo($get=array(),$transaction_id = null)
    {   
        
        $data   = array();
        $last   = array();
        $final   = array();
        $first   = array();
        $id = 0;
        // die();
        if($transaction_id === null){
           $id = substr(Input::get('trackingNo'),14);        

            $checkString = substr(Input::get('trackingNo'),0,14);

            if(is_numeric($checkString))
            {
                $id = Input::get('trackingNo');
            } 
        }
        
        if($transaction_id != null){
            $id = $transaction_id;
        }

        //get status from logistic transaction

        $tracking = LogisticTransaction::where('transaction_id', '=', $id )->select('id','status','insert_date','modify_date','delivery_name')->first();

        $tempstatus= $tracking->status;

        $status = "";

        switch ($tempstatus)
        {
            case '0':
                $status = 'Pending';
                break;
            case '1':
                $status = 'Undelivered';
                break;
            case '2':
                $status = 'Partial Sent';
                break;
            case '3':
                $status = 'Returned';
                break;
            case '4':
                $status = 'Sending';
                break;
            case '5':
                $status = 'Sent';
                break;
            case '6':
                $status = 'Cancelled';
                break;            
            default:
                $status = 'No Record';
                break;
        }

        $data['status'] = $status;

        $default[] = array(
            'datetime'  => "",
            'remark'    => "No Remark",
            // 'remark'    => "Preparing for delivery",
            'office'    => ""
        );

        $data['data'] = $default;

        if(count($tracking)>0)
        {
            $tempoffice = 'HQ';

            $batch = LogisticBatch::select('remark','batch_date', 'status','modify_date')
                        ->where('logistic_id', '=', $tracking->id)
                        ->orderBy('id', 'desc')
                        ->get();
                        
            // echo "<pre>";
            // print_r($batch);
            // echo "</pre>";

            // Insert 1st remark if is in logistic transaction
            $first[] = array(
                'datetime'  => $tracking->insert_date,
                'remark'    => "Processing your order...",
                'office'    => $tempoffice
            );

            //Get remarks from logistic_batch and arrange into array
            if(count($batch)>0)
            {
                $final = array();
                    
                foreach ($batch as $row)
                {
                    $temp= "";
                    $each = "";

                    // insert 1st remark for each batch
                    $each[] = array(
                        'datetime'  => $row->batch_date,
                        'remark'    => "Preparing for delivery!",
                        'office'    => $tempoffice
                    );

                    if ($row->status == 1)
                    {
                        // insert sending remark for each batch
                        $each[] = array(
                            'datetime'  => $row->batch_date,
                            'remark'    => "Sending...",
                            'office'    => $tempoffice
                        );
                    }
                    
                    if ($row->status == 2)
                    {
                        // insert sending remark for each batch
                        $each[] = array(
                            'datetime'  => $row->modify_date,
                            'remark'    => "Returned:  Arrange for next delivery .",
                            'office'    => $tempoffice
                        );
                    }

                    if ($row->remark != "")
                    {
                        $array = explode("\n", $row->remark);

                        foreach ($array as $remark)
                        {
                            $array = explode(": ", substr($remark, 19));

                            $temp["datetime"]   = substr($remark, 0, 19);                        
                            $temp['remark']     = $array[1];
                            $temp['office']     = $tempoffice;

                            $each[] = $temp;                        
                        }                 
                    }
                    rsort($each);

                    $final = array_merge($final, $each);
                }
            }        

            // insert last remark if transaction status is Sent
            if ($tempstatus== '5')
            {
                $remark_new = LogisticBatch::select('accept_date', 'sign_name')
                                ->where('logistic_id', '=', $tracking->id)
                                ->where('sign_name', '!=', "")
                                ->orderBy('id', 'desc')
                                ->first();
                
                
                if (count($remark_new)>0)
                {    
                    
                    
                    $last[] = array(
                        'datetime'  => $remark_new->accept_date,
                        'remark'    => 'Delivered : Accepted by '.$remark_new->sign_name,
                        'office'    => $tempoffice
                    );
                    
                    $last[] = array(
                            'datetime'  => $row->batch_date,
                            'remark'    => "Sending...",
                            'office'    => $tempoffice
                        );
                }else{
                    $last[] = array(
                            'datetime'  => $tracking->modify_date,
                            'remark'    => 'Delivered : Accepted by '.$tracking->delivery_name,
                            'office'    => $tempoffice
                        );
                }
            }    
                     
            $data['data'] = array_merge($last, $final, $first);
        }
                                             
       return $data;
    }

    public static function api_transaction_list($get = array())
    {
        $data   = array();

        $supervisor = LogisticDriver::where('username', '=', $get['username'])->where('type', '=', '1')->where('status', '=', '1')->first();

        // only supervisor may access
        if(count($supervisor)>0)
        {
            if($get['transaction_date'] != '')
            {
                $request_date= '%' . $get['transaction_date'] .'%';
            }
            else
            {
                $request_date = '%%';
            }

            if($get['status'] != '')
            {
                $tempstatus = LogisticTransaction::get_status_int($get['status']);
                $request_status = array($tempstatus);
            }
            else
            {
                $request_status = array('0', '1', '2', '3', '4');
            }

            if($get['transaction_id'] != '')
            {
                $transaction = LogisticTransaction::whereIn('status', $request_status)
                                                    ->where('transaction_id', '=', $get['transaction_id'])
                                                    ->where('transaction_date', 'like', $request_date)
                                                    ->get();

                $totalcount = LogisticTransaction::whereIn('status', $request_status)
                                                    ->where('transaction_id', '=', $get['transaction_id'])
                                                    ->where('transaction_date', 'like', $request_date)
                                                    ->count();
            }
            elseif($get['urgent'] != '')
            {
                $delivery = LogisticTItem::get_delivery_int($get['urgent']);

                $list = LogisticTItem::select('logistic_id')->where('delivery_time', '=', $delivery)->get();
                
                $logisticlist = array();

                foreach ($list as $templist)
                {
                    $logisticlist[] = $templist->logistic_id;
                }
                if(sizeof($logisticlist) == 0) 
                {
                    $logisticlist[] = 0;
                }

                $transaction = LogisticTransaction::whereIn('status', $request_status)
                                                    ->whereIn('id', $logisticlist)
                                                    ->where('transaction_date', 'like', $request_date)
                                                    ->take($get['count'])
                                                    ->skip($get['from'])
                                                    ->orderBy('status', 'asc')
                                                    ->get();

                $totalcount = LogisticTransaction::whereIn('status', $request_status)
                                                    ->whereIn('id', $logisticlist)
                                                    ->where('transaction_date', 'like', $request_date)
                                                    ->count();
            }
            else
            {
                $transaction = LogisticTransaction::whereIn('status', $request_status)
                                                    ->where('transaction_date', 'like', $request_date)
                                                    ->take($get['count'])
                                                    ->skip($get['from'])
                                                    ->orderBy('status', 'asc')
                                                    ->get();

                $totalcount = LogisticTransaction::whereIn('status', $request_status)
                                                    ->where('transaction_date', 'like', $request_date)
                                                    ->count();
            }

            
            // $transaction = DB::table('logistic_transaction')->whereIn($request_status)->get();

            $data['record'] = count($transaction);
            $data['total_record'] = $totalcount;

            $details = array();

            foreach ($transaction as $row)
            {
                $status = LogisticTransaction::get_status($row->status);

                $details[] = array(
                    'logistic_id' => $row->id,
                    'transaction_id' => $row->transaction_id,
                    'transaction_date' => $row->transaction_date,
                    'delivery_name' => $row->delivery_name,
                    'delivery_city' => $row->delivery_city,
                    'special_msg' => $row->special_msg,
                    'do_no' => $row->do_no,
                    'status' => $status
                );
            }

            $data['transaction'] = $details;
            $headerResponse = array(
                "isSuccess" => 'true',
                "message" => 'Success'
            );
            $data = array_merge($headerResponse,$data);
        }
        else
            $data['status_msg']  = 'Access Denied!';
            $headerResponse = array(
                "isSuccess" => 'false',
                "message" => 'Your Access Denied!'
            );
            $data = array_merge($headerResponse,$data);

        return $data;

    }

    public static function api_transaction_detail($get = array())
    {
        $data   = array();

        $supervisor = LogisticDriver::where('username', '=', $get['username'])->where('type', '=', '1')->where('status', '=', '1')->first();

        // only supervisor may access
        if(count($supervisor)>0 or $get['cms'] == '1')
        {
            $transaction = LogisticTransaction::find($get['logistic_id']);
    
            // valid id
            if(count($transaction) > 0)
            {
                // update remark
                if($get['remark'] != '')
                {
                    $tempremark = date('Y-m-d H:i:s') . " " . $get['username'] . ": " . $get['remark'];
                    if($transaction->remark == '')
                        $transaction->remark = $tempremark;
                    else
                        $transaction->remark = $transaction->remark . "\n" . $tempremark;
                    $transaction->modify_by = $get['username'];
                    $transaction->modify_date = date('Y-m-d H:i:s');
                    $transaction->save();

                    // var_dump(nl2br($transaction->remark)); exit();

                    $data['status_msg']  = 'Success: Remark Updated!';
                    $headerResponse = array(
                        "isSuccess" => 'true',
                        "message" => 'Your changes has been successfully update'
                    );
                    $data = array_merge($headerResponse,$data);
                }

                // update status
                if($get['status'] != '')
                {
                    $tempstatus = LogisticTransaction::get_status_int($get['status']);

                    // cancelled
                    if($tempstatus == '6')
                    {
                        // for status Pending and Sending
                        $check_status = array('0', '1');

                        $batch = LogisticBatch::whereIn('status', $check_status)
                                                    ->where('logistic_id', '=', $get['logistic_id'])
                                                    ->get();

                        // update batch status to Cancelled
                        if(count($batch)>0)
                        {
                            foreach ($batch as $list)
                            {
                                $list->status = '5';
                                $list->save();
                            }
                        }
                        
                        LogisticTransaction::create_refund($get['logistic_id'], $get['username']);
                    }
                    
                    $transaction->status = $tempstatus;
                    $transaction->modify_by = $get['username'];
                    $transaction->modify_date = date('Y-m-d H:i:s');
                    $transaction->save();
                    $data['status_msg']  = 'Success: Status Updated!';
                    $headerResponse = array(
                        "isSuccess" => 'true',
                        "message" => 'Your changes has been successfully update'
                    );
                    $data = array_merge($headerResponse,$data);
                    
                    //Lazada DBS
                    if($tempstatus == '5')
                    {
                        $response = LazadaController::dbsdelivered($get['logistic_id']);
                    }
                    
                }

                // assigning a new batch
                if($get['driver_id'] != '')
                {
                    if($transaction->status == '6')
                    {
                        $data['status_msg']  = 'Failed: Transaction cancelled cannot be assigned!';
                        $headerResponse = array(
                            "isSuccess" => 'false',
                            "message" => 'Cannot assign on cancelled transaction'
                        );
                        $data = array_merge($headerResponse,$data);
                    }
                    else
                    {
                        $totalassign = 0;
                        foreach($get['item_id'] as $k => $v)
                        {
                            $qty_assign = $get['qty_assign'][$k];
                            $totalassign += $qty_assign;
                        }

                        // at least an item is assigned
                        if($totalassign > 0)
                        {
                            $new = array();
                            $new['logistic_id'] = $get['logistic_id'];
                            $new['batch_date'] = date('Y-m-d H:i:s');
                            $new['driver_id'] = $get['driver_id'];
                            $new['status'] = 0;
                            $new['assign_by'] = $get['username'];
                            $new['assign_date'] = date('Y-m-d H:i:s');
                            $new['modify_by'] = $get['username'];
                            $new['modify_date'] = '';
                            
                            DB::beginTransaction();

                            $insert_id = LogisticBatch::insertGetId($new);

                            $tempcheck = 0;
                            
                            // to update warehouse stock
                            $stocks = array();

                            foreach($get['item_id'] as $k => $v)
                            {
                                $item_id = $get['item_id'][$k];
                                $qty_assign = $get['qty_assign'][$k];

                                $tempitem = LogisticTItem::find($item_id);

                                // quantity assigned is not more than pending to assign
                                if($tempitem->qty_to_assign >= $qty_assign AND $qty_assign > 0)
                                {
                                    $tempitem->qty_to_assign -= $qty_assign;
                                    $tempitem->save();

                                    $batch_item = array();
                                    $batch_item['batch_id'] = $insert_id;
                                    $batch_item['transaction_item_id'] = $item_id;
                                    $batch_item['qty_assign'] = $qty_assign;
                                    $batch_item['qty_sent'] = 0;
                                    $batch_item['modify_by'] = $get['username']; //'adrian';
                                    $batch_item['modify_date'] = '';

                                    $insert_id2 = LogisticBatchItem::insertGetId($batch_item);

                                    $tempcheck += 1;
                                    
                                    // if sorted then no need to deduct
                                    $sorted = DB::table('jocom_sort_transaction')
                                                ->where('transaction_id', '=', $transaction->transaction_id)
                                                ->where('generated', '=', 1)
                                                ->count();

                                    if ($sorted > 0) {
                                        if ($transaction->status == 3) {
                                            array_push($stocks, [
                                                'product_id' => $tempitem->product_id, 
                                                'product_price_id' => $tempitem->product_price_id,
                                                'quantity' => $qty_assign
                                            ]);
                                        }
                                    } else {
                                        array_push($stocks, [
                                            'product_id' => $tempitem->product_id, 
                                            'product_price_id' => $tempitem->product_price_id,
                                            'quantity' => $qty_assign
                                        ]);
                                    }
                                }
                                else
                                    continue;
                            }

                            if($tempcheck > 0)
                            {
                                $trans_id = $transaction->transaction_id;
                                $logistic_id = $get['logistic_id'];
                                $ori_status = $transaction->status;
                                $new_status = 'sending';
                                $type='after_assign';

                                

                                // update transaction status to Sending
                                $transaction->status = '4';
                                $transaction->modify_by = $get['username'];
                                $transaction->modify_date = date('Y-m-d H:i:s');
                                $transaction->save();

                                $data['status_msg']  = 'Success: New batch (ID:' . $insert_id . ') created!';
                                $headerResponse = array(
                                    "isSuccess" => 'true',
                                    "message" => 'New batch (ID:' . $insert_id . ') successfully assigned to '.$DriverRecord->username
                                );
                                $data = array_merge($headerResponse,$data);
                                
                                // deduct stock in warehouse
                                foreach($stocks as $stock) {
                                    // Deactivated 22/12/2021
                                    // Warehouse::manageProductstock($stock['product_id'], $stock['product_price_id'], $stock['quantity'], 'decrease', $get['username']);
                                }
                                
                                // OPEN: SEND POPBOX REQUEST ORDER IF ORDER IS FOR POPBOX DELIVERY //
                                
                                $popOrder = PopboxOrder::where("transaction_id",$transaction->transaction_id)->first();
                                if(count($popOrder) > 0){
                                    $PopboxSave = PopboxController::sendPopBoxOrder($transaction->transaction_id);
                                }
                                
                                Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,$get['username']);
                                // CLOSE: SEND POPBOX REQUEST ORDER IF ORDER IS FOR POPBOX DELIVERY //

                            }
                            else
                            {
                                LogisticBatch::destroy($insert_id);
                                
                                $LogisticBatchAssigned = LogisticBatch::where("logistic_id",$get['logistic_id'])
                                        ->whereIn('status', [0, 1])->get();
                                
                                if(count($LogisticBatchAssigned) > 0 ){
                                    $data['status_msg']  = 'Failed: This DO has been assigned before!';
                                    $headerResponse = array(
                                        "isSuccess" => 'false',
                                        "message" => 'This DO has been assigned before!'
                                    );
                                    $data = array_merge($headerResponse,$data);
                                }else{
                                    $data['status_msg']  = 'Failed: No available item to assign!';
                                     $headerResponse = array(
                                        "isSuccess" => 'false',
                                        "message" => 'No available item to assign!'
                                    );
                                    $data = array_merge($headerResponse,$data);
                                }
                            }
                            DB::commit();
                            
                        }
                        else
                        {
                            $data['status_msg']  = 'Failed: No quantity assigned!';
                            $headerResponse = array(
                                "isSuccess" => 'false',
                                "message" => 'No valid quantity of item!'
                            );
                            $data = array_merge($headerResponse,$data);
                        }
                    }
                }
                
                // assigning a new batch international
                if($get['is_international_logistic'] == 1){
                    
                    // Assign to batch 
                    
                    if($transaction->status == '6')
                    {
                        $data['status_msg']  = 'Transaction cancelled cannot be assigned!';
                    }
                    else
                    {
                        $totalassign = 0;
                        foreach($get['item_id'] as $k => $v)
                        {
                            $qty_assign = $get['qty_assign'][$k];
                            $totalassign += $qty_assign;
                        }

                        // at least an item is assigned
                        if($totalassign > 0)
                        {
                            $new = array();
                            $new['logistic_id'] = $get['logistic_id'];
                            $new['batch_date'] = date('Y-m-d H:i:s');
                            $new['driver_id'] = $get['driver_id'];
                            $new['status'] = 0;
                            $new['assign_by'] = $get['username'];
                            $new['assign_date'] = date('Y-m-d H:i:s');
                            $new['modify_by'] = '';
                            $new['modify_date'] = '';
                            
                            $LogisticBatch = new LogisticBatch();
                            $LogisticBatch->logistic_id = $get['logistic_id']; 
                            $LogisticBatch->batch_date = date("Y-m-d h:i:s");
                            $LogisticBatch->driver_id = 0;
                            $LogisticBatch->shipping_method = $get['international_courier_id'];
                            $LogisticBatch->tracking_number = '';
                            $LogisticBatch->do_no = $transaction->do_no;
                            $LogisticBatch->status = 1;
                            $LogisticBatch->assign_by = Session::get('username');
                            $LogisticBatch->assign_date = date("Y-m-d h:i:s");
                            $LogisticBatch->save();
                            
                            $insert_id = $LogisticBatch->id;

                            $tempcheck = 0;
                            
                            $ItemsDetailsCollection = array();

                            foreach($get['item_id'] as $k => $v)
                            {
                                $item_id = $get['item_id'][$k];
                                $qty_assign = $get['qty_assign'][$k];

                                $tempitem = LogisticTItem::find($item_id);

                                // quantity assigned is not more than pending to assign
                                if($tempitem->qty_to_assign >= $qty_assign AND $qty_assign > 0)
                                {
                                    $tempitem->qty_to_assign -= $qty_assign;
                                    $tempitem->save();

                                    $batch_item = array();
                                    $batch_item['batch_id'] = $insert_id;
                                    $batch_item['transaction_item_id'] = $item_id;
                                    $batch_item['qty_assign'] = $qty_assign;
                                    $batch_item['qty_sent'] = 0;
                                    $batch_item['modify_by'] = '';
                                    $batch_item['modify_date'] = '';

                                    $insert_id2 = LogisticBatchItem::insertGetId($batch_item);
                                    $ItemsDetailsCollection[] = $batch_item;
                                    $tempcheck += 1;
                                }
                                else
                                    continue;
                            }

                            if($tempcheck > 0)
                            {
                                $trans_id = $transaction->transaction_id;
                                $logistic_id = $get['logistic_id'];
                                $ori_status = $transaction->status;
                                $new_status = 'sending';
                                $type='after_assign';

                                Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type);

                                // update transaction status to Sending
                                $transaction->status = '4';
                                $transaction->modify_by = $get['username'];
                                $transaction->modify_date = date('Y-m-d H:i:s');
                                $transaction->save();
                        
                                $data['status_msg']  = 'New batch (ID:' . $insert_id . ') created!';
                                
                                $Country = DB::table('jocom_country_states')->where('id','=', $transaction->delivery_state_id)->select('id','country_id')->first();
                                
                                // Create International Order
                                
                                $DeliveryOrder = DB::table('jocom_delivery_order')->where("transaction_id",$transaction->transaction_id)->first();
                                
                                $InternationalLogistic = new InternationalLogistic();
                                $InternationalLogistic->reference_number = '';
                                $InternationalLogistic->transaction_id = $transaction->transaction_id;
                                $InternationalLogistic->manifest_id = '';
                                $InternationalLogistic->deliver_to_country = $Country->country_id;
                                $InternationalLogistic->batch_id = $insert_id;
                                $InternationalLogistic->order_request_id = $DeliveryOrder->id > 0 ? $DeliveryOrder->id: 0;
                                $InternationalLogistic->created_by = Session::get("username");
                                $InternationalLogistic->updated_by = Session::get("username");
                                $InternationalLogistic->status = 1;
                                $InternationalLogistic->activation = 1;
                                $InternationalLogistic->save();
                                
                                $jocom_international_logistic_id = $InternationalLogistic->id;
                                
                                if($DeliveryOrder->id > 0){
                                    
                                    
                                    
                                    $ItemsDetailsCollection = DB::table('jocom_delivery_order_items')->where("service_order_id",$DeliveryOrder->id)->get();
                               
                                    foreach ($ItemsDetailsCollection as $keyLITEM => $valueLItem) {
                                        
                                        $InterLogItem = new InternationalLogisticItem;
                                        
                                        $InterLogItem->jocom_international_logistic_id = $jocom_international_logistic_id;
                                        $InterLogItem->logistic_transaction_item_id = '';
                                        $InterLogItem->product_id = '';
                                        $InterLogItem->product_name =  $valueLItem->item_description;
                                        $InterLogItem->product_label = $valueLItem->item_label;
                                        $InterLogItem->brand = '';
                                        $InterLogItem->quantity = $valueLItem->quantity;
                                        $InterLogItem->value = $valueLItem->amount_value;
                                        $InterLogItem->Model = '';
                                        $InterLogItem->no_of_pcs = $valueLItem->quantity;
                                        $InterLogItem->content_of_pcs = $valueLItem->quantity;
                                        $InterLogItem->weight = $valueLItem->item_total_weight;
                                        $InterLogItem->created_by = Session::get("username");
                                        $InterLogItem->updated_by = Session::get("username");
                                        $InterLogItem->save();
                                        
                                    }
                                    
                                }else{
                                    
                                    // For jocom own order 
                                    
//                                    echo "<pre>";
//                                    print_r($transaction);
//                                    echo "</pre>";
                                    
                                    
                                    foreach ($ItemsDetailsCollection as $keyLITEM => $valueLItem) {
                                        
//                                        echo "<pre>";
//                                        print_r($valueLItem);
//                                        echo "</pre>";
                                        
//                                        $ItemTransactionDetail = DB::table('jocom_transaction_details AS JTD')
//                                                ->leftJoin("logistic_transaction_item AS LTI", 'LTI.id', '=', )
//                                                ->where("id",$valueLItem->transaction_item_id)->first();
                                        
                                        $ItemTransactionDetail = DB::table('logistic_transaction_item AS LTI')
                                                ->leftJoin("jocom_transaction_details AS JTD", 'JTD.id', '=', "LTI.transaction_item_id")
                                                ->select(array(
                                                    'JTD.*',
                                                   ))
                                                ->where("LTI.id",$valueLItem['transaction_item_id'])->first();
                                        
                                        $InterLogItem = new InternationalLogisticItem;
                                        
                                        $InterLogItem->jocom_international_logistic_id = $jocom_international_logistic_id;
                                        $InterLogItem->logistic_transaction_item_id = $valueLItem['transaction_item_id'];
                                        $InterLogItem->product_id = $ItemTransactionDetail->product_id;
                                        $InterLogItem->product_name =  $ItemTransactionDetail->product_name;
                                        $InterLogItem->product_label = $ItemTransactionDetail->price_label;
                                        $InterLogItem->brand = '';
                                        $InterLogItem->quantity = $ItemTransactionDetail->unit;
                                        $InterLogItem->value = $ItemTransactionDetail->total;
                                        $InterLogItem->Model = '';
                                        $InterLogItem->no_of_pcs = $ItemTransactionDetail->unit;
                                        $InterLogItem->content_of_pcs = $ItemTransactionDetail->unit;
                                        $InterLogItem->created_by = Session::get("username");
                                        $InterLogItem->updated_by = Session::get("username");
                                        $InterLogItem->save();
                                        
                                    }
                                    
                                }
                                
                                
                                // Create International Order
                                
                                
                            }
                            else
                            {
                                LogisticBatch::destroy($insert_id);
                                $data['status_msg']  = 'No item assigned!';
                            }
                            
                        }
                        else
                        {
                            $data['status_msg']  = 'No quantity assigned!';
                        }
                    }
                    
                    
                    
                    // Assign to international logistic
                    
                }

                // display listing
                if($get['logistic_id'] != '' && $get['remark'] == '' && $get['status'] == '' && $get['driver_id'] == '')
                {
                    $list = LogisticTItem::where('logistic_id', '=', $get['logistic_id'])->get();

                    $status = LogisticTransaction::get_status($transaction->status);

                    $data['logistic_id'] = $transaction->id;
                    $data['transaction_id'] = $transaction->transaction_id;
                    $data['transaction_date'] = $transaction->transaction_date;
                    $data['delivery_name'] = $transaction->delivery_name;
                    $data['delivery_contact_no'] = $transaction->delivery_contact_no;
                    $data['buyer_email'] = $transaction->buyer_email;
                    $data['delivery_addr_1'] = $transaction->delivery_addr_1;
                    $data['delivery_addr_2'] = $transaction->delivery_addr_2;
                    $data['delivery_city'] = $transaction->delivery_city;
                    $data['delivery_postcode'] = $transaction->delivery_postcode;
                    $data['delivery_state'] = $transaction->delivery_state;
                    $data['delivery_country'] = $transaction->delivery_country;
                    $data['special_msg'] = $transaction->special_msg;
                    $data['do_no'] = $transaction->do_no;
                    $data['remark'] = $transaction->remark;
                    $data['status'] = $status;

                    foreach ($list as $row)
                    {
                        $delivery = LogisticTItem::get_delivery($row->delivery_time);
                        $details[] = array(
                            'item_id' => $row->id,
                            'sku' => $row->sku,
                            'name' => $row->name,
                            'label' => $row->label,
                            'delivery_time' => $delivery,
                            'qty_order' => $row->qty_order,
                            'qty_to_assign' => $row->qty_to_assign,
                            'qty_to_send' => $row->qty_to_send
                        );
                    }
                    $data['item'] = $details;
                }
            }
            else
            {
                $data['status_msg']  = 'Failed: Invalid Logistic ID!';
                $headerResponse = array(
                    "isSuccess" => 'false',
                    "message" => 'Invalid Logistic ID!'
                );
                $data = array_merge($headerResponse,$data);
            }
        }
        else
        {
            $data['status_msg']  = 'Failed: Access Denied!';
            $headerResponse = array(
                    "isSuccess" => 'false',
                    "message" => 'Your access denied!'
                );
            $data = array_merge($headerResponse,$data);
        }
        return $data;
    }
    
    public static function api_transaction_detail_prebatch($get = array())
    {
        $data   = array();

        $supervisor = LogisticDriver::where('username', '=', $get['username'])->where('type', '=', '1')->where('status', '=', '1')->first();

        // only supervisor may access
        if(count($supervisor)>0 or $get['cms'] == '1')
        {
            $transaction = LogisticTransaction::find($get['logistic_id']);
    
            // valid id
            if(count($transaction) > 0)
            {
                // update remark
                if($get['remark'] != '')
                {
                    $tempremark = date('Y-m-d H:i:s') . " " . $get['username'] . ": " . $get['remark'];
                    if($transaction->remark == '')
                        $transaction->remark = $tempremark;
                    else
                        $transaction->remark = $transaction->remark . "\n" . $tempremark;
                    $transaction->modify_by = $get['username'];
                    $transaction->modify_date = date('Y-m-d H:i:s');
                    $transaction->save();

                    // var_dump(nl2br($transaction->remark)); exit();

                    $data['status_msg']  = 'Success: Remark Updated!';
                    $headerResponse = array(
                        "isSuccess" => 'true',
                        "message" => 'Your changes has been successfully update'
                    );
                    $data = array_merge($headerResponse,$data);
                }

                // update status
                if($get['status'] != '')
                {
                    $tempstatus = LogisticTransaction::get_status_int($get['status']);

                    // cancelled
                    if($tempstatus == '61')
                    {
                        // for status Pending and Sending
                        $check_status = array('0', '1');

                        $batch = LogisticBatchPrescan::whereIn('status', $check_status)
                                                    ->where('logistic_id', '=', $get['logistic_id'])
                                                    ->get();

                        // update batch status to Cancelled
                        if(count($batch)>0)
                        {
                            foreach ($batch as $list)
                            {
                                $list->status = '5';
                                $list->save();
                            }
                        }
                        
                        // LogisticTransaction::create_refund($get['logistic_id'], $get['username']);
                    }

                    // $transaction->status = $tempstatus;
                    $transaction->modify_by = $get['username'].'[PRESCAN]';
                    $transaction->modify_date = date('Y-m-d H:i:s');
                    $transaction->save();
                    $data['status_msg']  = 'Success: Status Updated!';
                    $headerResponse = array(
                        "isSuccess" => 'true',
                        "message" => 'Your changes has been successfully update'
                    );
                    $data = array_merge($headerResponse,$data);

                    
                }

                // assigning a new batch
                if($get['driver_id'] != '')
                {
                    if($transaction->status == '6')
                    {
                        $data['status_msg']  = 'Failed: Transaction cancelled cannot be assigned!';
                        $headerResponse = array(
                            "isSuccess" => 'false',
                            "message" => 'Cannot assign on cancelled transaction'
                        );
                        $data = array_merge($headerResponse,$data);
                    }
                    else
                    {
                        $totalassign = 0;
                        foreach($get['item_id'] as $k => $v)
                        {
                            $qty_assign = $get['qty_assign'][$k];
                            $totalassign += $qty_assign;
                        }

                        // at least an item is assigned
                        if($totalassign > 0)
                        {
                            $new = array();
                            $new['logistic_id'] = $get['logistic_id'];
                            $new['batch_date'] = date('Y-m-d H:i:s');
                            $new['driver_id'] = $get['driver_id'];
                            $new['status'] = 0;
                            $new['assign_by'] = $get['username'];
                            $new['assign_date'] = date('Y-m-d H:i:s');
                            $new['modify_by'] = $get['username'];
                            $new['modify_date'] = '';
                            
                            DB::beginTransaction();

                            $insert_id = LogisticBatchPrescan::insertGetId($new);

                            $tempcheck = 0;
                            
                            // to update warehouse stock
                            $stocks = array();

                            foreach($get['item_id'] as $k => $v)
                            {
                                $item_id = $get['item_id'][$k];
                                $qty_assign = $get['qty_assign'][$k];

                                $tempitem = LogisticTItem::find($item_id);

                                // quantity assigned is not more than pending to assign
                                if($tempitem->qty_to_assign >= $qty_assign AND $qty_assign > 0)
                                {
                                    // $tempitem->qty_to_assign -= $qty_assign;
                                    // $tempitem->save();

                                    $batch_item = array();
                                    $batch_item['batch_id'] = $insert_id;
                                    $batch_item['transaction_item_id'] = $item_id;
                                    $batch_item['qty_assign'] = $qty_assign;
                                    $batch_item['qty_sent'] = 0;
                                    $batch_item['modify_by'] = $get['username']; //'adrian';
                                    $batch_item['modify_date'] = '';

                                    $insert_id2 = LogisticBatchItemPrescan::insertGetId($batch_item);

                                    $tempcheck += 1;
                                    
                                    // if sorted then no need to deduct
                                    // $sorted = DB::table('jocom_sort_transaction')
                                    //             ->where('transaction_id', '=', $transaction->transaction_id)
                                    //             ->where('generated', '=', 1)
                                    //             ->count();

                                    // if ($sorted > 0) {
                                    //     if ($transaction->status == 3) {
                                    //         array_push($stocks, [
                                    //             'product_id' => $tempitem->product_id, 
                                    //             'product_price_id' => $tempitem->product_price_id,
                                    //             'quantity' => $qty_assign
                                    //         ]);
                                    //     }
                                    // } else {
                                    //     array_push($stocks, [
                                    //         'product_id' => $tempitem->product_id, 
                                    //         'product_price_id' => $tempitem->product_price_id,
                                    //         'quantity' => $qty_assign
                                    //     ]);
                                    // }
                                }
                                else
                                    continue;
                            }

                            if($tempcheck > 0)
                            {
                                // $trans_id = $transaction->transaction_id;
                                // $logistic_id = $get['logistic_id'];
                                // $ori_status = $transaction->status;
                                // $new_status = 'sending';
                                // $type='after_assign';

                                

                                // // update transaction status to Sending
                                // $transaction->status = '4';
                                // $transaction->modify_by = $get['username'];
                                // $transaction->modify_date = date('Y-m-d H:i:s');
                                // $transaction->save();

                                $data['status_msg']  = 'Success: New batch (ID:' . $insert_id . ') created!';
                                $headerResponse = array(
                                    "isSuccess" => 'true',
                                    "message" => 'New batch (ID:' . $insert_id . ') successfully assigned to PreScan'
                                );
                                $data = array_merge($headerResponse,$data);
                                
                                // deduct stock in warehouse
                                foreach($stocks as $stock) {
                                    // Warehouse::manageProductstock($stock['product_id'], $stock['product_price_id'], $stock['quantity'], 'decrease', $get['username']);
                                }
                                
                                // OPEN: SEND POPBOX REQUEST ORDER IF ORDER IS FOR POPBOX DELIVERY //
                                
                                // $popOrder = PopboxOrder::where("transaction_id",$transaction->transaction_id)->first();
                                // if(count($popOrder) > 0){
                                //     $PopboxSave = PopboxController::sendPopBoxOrder($transaction->transaction_id);
                                // }
                                
                                // Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,$get['username']);
                                // CLOSE: SEND POPBOX REQUEST ORDER IF ORDER IS FOR POPBOX DELIVERY //

                            }
                            else
                            {
                                LogisticBatchPrescan::destroy($insert_id);
                                
                                $LogisticBatchAssigned = LogisticBatchPrescan::where("logistic_id",$get['logistic_id'])
                                        ->whereIn('status', [0, 1])->get();
                                
                                if(count($LogisticBatchAssigned) > 0 ){
                                    $data['status_msg']  = 'Failed: This DO has been assigned before!';
                                    $headerResponse = array(
                                        "isSuccess" => 'false',
                                        "message" => 'This DO has been assigned before!'
                                    );
                                    $data = array_merge($headerResponse,$data);
                                }else{
                                    $data['status_msg']  = 'Failed: No available item to assign!';
                                     $headerResponse = array(
                                        "isSuccess" => 'false',
                                        "message" => 'No available item to assign!'
                                    );
                                    $data = array_merge($headerResponse,$data);
                                }
                            }
                            DB::commit();
                            
                        }
                        else
                        {
                            $data['status_msg']  = 'Failed: No quantity assigned!';
                            $headerResponse = array(
                                "isSuccess" => 'false',
                                "message" => 'No valid quantity of item!'
                            );
                            $data = array_merge($headerResponse,$data);
                        }
                    }
                }
               

                // display listing
                if($get['logistic_id'] != '' && $get['remark'] == '' && $get['status'] == '' && $get['driver_id'] == '')
                {
                    $list = LogisticTItem::where('logistic_id', '=', $get['logistic_id'])->get();

                    $status = LogisticTransaction::get_status($transaction->status);

                    $data['logistic_id'] = $transaction->id;
                    $data['transaction_id'] = $transaction->transaction_id;
                    $data['transaction_date'] = $transaction->transaction_date;
                    $data['delivery_name'] = $transaction->delivery_name;
                    $data['delivery_contact_no'] = $transaction->delivery_contact_no;
                    $data['buyer_email'] = $transaction->buyer_email;
                    $data['delivery_addr_1'] = $transaction->delivery_addr_1;
                    $data['delivery_addr_2'] = $transaction->delivery_addr_2;
                    $data['delivery_city'] = $transaction->delivery_city;
                    $data['delivery_postcode'] = $transaction->delivery_postcode;
                    $data['delivery_state'] = $transaction->delivery_state;
                    $data['delivery_country'] = $transaction->delivery_country;
                    $data['special_msg'] = $transaction->special_msg;
                    $data['do_no'] = $transaction->do_no;
                    $data['remark'] = $transaction->remark;
                    $data['status'] = $status;

                    foreach ($list as $row)
                    {
                        $delivery = LogisticTItem::get_delivery($row->delivery_time);
                        $details[] = array(
                            'item_id' => $row->id,
                            'sku' => $row->sku,
                            'name' => $row->name,
                            'label' => $row->label,
                            'delivery_time' => $delivery,
                            'qty_order' => $row->qty_order,
                            'qty_to_assign' => $row->qty_to_assign,
                            'qty_to_send' => $row->qty_to_send
                        );
                    }
                    $data['item'] = $details;
                }
            }
            else
            {
                $data['status_msg']  = 'Failed: Invalid Logistic ID!';
                $headerResponse = array(
                    "isSuccess" => 'false',
                    "message" => 'Invalid Logistic ID!'
                );
                $data = array_merge($headerResponse,$data);
            }
        }
        else
        {
            $data['status_msg']  = 'Failed: Access Denied!';
            $headerResponse = array(
                    "isSuccess" => 'false',
                    "message" => 'Your access denied!'
                );
            $data = array_merge($headerResponse,$data);
        }
        return $data;
    }

    public static function log_transaction($id = NULL)
    {
        $returndata = array();

        if (isset($id))
        {
            $existID = LogisticTransaction::select('id')->where('transaction_id', '=', $id)->first();
            
            if(count($existID)>0)
            {
                $returndata['type'] = 'message';
                $returndata['message'] = 'Transaction ID exist in Logistic System!';
            }
            else
            {
                $trans = Transaction::where('id', '=', $id)->first();
                if(count($trans)>0)
                {
                    // purchase Jpoint no shipping
                    if ($trans->no_shipping == 1)
                    {
                        $returndata['type'] = 'message';
                        $returndata['message'] = 'No shipping is required!!';
                    }
                    else
                    {
                        $transDetail = TDetails::where('transaction_id', '=', $id)->get();
                        if(count($transDetail)>0)
                        {
                            $transID = LogisticTransaction::insert_trans($trans);
                            if($transID != NULL)
                            {
                                $transDetailID = LogisticTItem::insert_transDetail($transDetail, $transID);
                                $returndata['type'] = 'success';
                                $returndata['message'] = 'This transaction is logged in Logistic System!';
                                
                                // Special Request to auto completed as 'SENT' for this coupon code //
                                $TransactionCoupon = TCoupon::getByTransaction($id);
                                if(count($TransactionCoupon) > 0){
                                    if($TransactionCoupon->coupon_code == 'JOCOM37'){ // OLD CODE JOCOM4U
                                        $LogisticTransaction = LogisticTransaction::find($transID);
                                        $LogisticTransaction->status = 5; //Sent
                                        $LogisticTransaction->save();
                                        
                                        // UPDATE to FIMS QUEUE
                                        $SupplierInvoiceQueue =  new SupplierInvoiceQueue();
                                        $SupplierInvoiceQueue->transaction_id = $id;
                                        $SupplierInvoiceQueue->is_generated = 0;
                                        $SupplierInvoiceQueue->save();
                                        // UPDATE to FIMS QUEUE
                                    }
                                }
                                // Special Request to auto completed as 'SENT' for this coupon code //
                                
                                if($trans->is_self_collect == 1){

                                    $RegionInfo = DB::table('jocom_country_states')->where("id",$trans->delivery_state_id)->first();
                                
                                    $region_id = $RegionInfo->region_id;
                                    $from_id = '';
                                    $to_id = '';
                                    $list_transaction = $trans->id;
                                    $create_separator = false;
                                    $is_include_failed = false;
                                    $failed_do_only = false;

                                    TransactionController::dosorting($region_id,$from_id,$to_id,$list_transaction, $create_separator,$is_include_failed,$failed_do_only);

                                }
                                
                                // Auto Deduct Stock //
                                
                            }                        
                        }
                        else
                        {
                            $returndata['type'] = 'message';
                            $returndata['message'] = 'Transaction details error!';
                        }
                    }
                }
                else
                {
                    $returndata['type'] = 'message';
                    $returndata['message'] = 'Transaction ID error!';
                }                
            }           

        }
        else
        {
            $returndata['type'] = 'message';
            $returndata['message'] = 'No Transaction ID provided!';
        }

        return $returndata;
    }


    /**
     * Listing for logistic transaction
     * @return [type] [description]
     */
    public function scopeLogistic_listing()
    {
        $list = LogisticTransaction::orderBy('id', 'Desc')->get();
        return $list;       
    
    }

   /**
     * Save logistic transaction
     * @return [type] [description]
     */
    public function scopeSave_logistic()
    {
        if (Input::has('id'))
        {
            $logistic_id                    = Input::get('id');
            $logistic                       = LogisticTransaction::find($logistic_id);
            $logistic->status               = Input::get('status');
            $ori_status                     = Input::get('ori_status');
            // $logistic->delivery_name        = trim(Input::get('delivery_name'));
            // $logistic->delivery_contact_no  = trim(Input::get('delivery_contact_no'));
            // $logistic->buyer_email          = trim(Input::get('buyer_email'));
            // $logistic->delivery_addr_1      = trim(Input::get('delivery_addr_1'));
            // $logistic->delivery_addr_2      = trim(Input::get('delivery_addr_2'));
            // $logistic->delivery_city        = trim(Input::get('delivery_city'));
            // $logistic->delivery_postcode    = trim(Input::get('delivery_postcode'));
            // $logistic->delivery_state       = trim(Input::get('delivery_state'));
            // $logistic->delivery_country     = trim(Input::get('delivery_country'));
            // $logistic->special_msg          = trim(Input::get('special_msg'));
            
            if(Input::has('remark'))
            {
                $tempremark = date('Y-m-d H:i:s') . " [CMS]" . Session::get('username') . ": " . trim(Input::get('remark'));

                if($logistic->remark == '')
                    $logistic->remark = $tempremark;
                else
                    $logistic->remark = $logistic->remark . "\n" . $tempremark;
            }

            $logistic->modify_by    = Session::get('username');
            $logistic->modify_date  = date("Y-m-d h:i:sa");           
           
            $logistic->save();
            
            if($logistic->status == 5){
                
                    $response = LazadaController::dbsdelivered($logistic_id);
            }

            if ($ori_status != $logistic->status) {

               $new_status = $logistic->status;
               $trans_id = $logistic->transaction_id;
               $type = 'Logistic';

               Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,Session::get('username'));
            }
        }
            
        
    }
    
    public static function create_refund($id, $username) {
        // echo "<br>Creating refund . . . [log_id: ".$id."]";

        $trans_id               = Refund::get_trans_id($id);
        $buyer_id               = Refund::get_buyer_id($trans_id);
        $amount                 = 0;
        $arr_refund_products    = Refund::logistic_get_refund_products($id);

        // var_dump($arr_refund_products);

        if(count($arr_refund_products) > 0) {
            $refund                 = new Refund;
            $refund->trans_id       = $trans_id;
            $refund->buyer_id       = $buyer_id;
            $refund->status         = "pending";
            $refund->timestamps     = false;
            $refund->created_by     = $username;
            $refund->created_from   = "logistic";
            $refund->created_date   = date("Y-m-d H:i:s");
            
            if ($refund->save()) {
                $refund_id                      = $refund->id;

                foreach ($arr_refund_products as $products) {
                    $arr_trans_item = "";
                    // var_dump($products);

                    $unit_disc      = 0;
                    $product_id     = $products->product_id;
                    $price_id       = $products->product_price_id;
                    $qty_to_send    = $products->qty_to_send;

                    // echo "<br>[CREATE REFUND] [TRANS ID: ".$trans_id."] [Product ID: ".$product_id."] [Price ID: ".$price_id."]";

                    $arr_trans_item = Refund::logistic_get_trans_item($trans_id, $product_id, $price_id);
                    // ->select('id','product_id', 'p_option_id', 'price', 'p_referral_fees', 'p_referral_fees_type', 'unit', 'gst_rate_item', 'disc', 'total')

                    if(count($arr_trans_item) > 0 ) {
                        $arr_item = "";

                        if ($arr_trans_item->disc > 0) {
                            $unit_disc  = $arr_trans_item->disc / $arr_trans_item->unit;
                        }

                        $arr_item['refund_id']          = $refund_id;
                        $arr_item['trans_detail_id']    = $arr_trans_item->id;
                        $arr_item['product_id']         = $products->product_id;
                        $arr_item['price_id']           = $price_id;
                        $arr_item['price']              = $arr_trans_item->price;
                        $arr_item['p_referral_fees']    = $arr_trans_item->p_referral_fees;
                        $arr_item['p_referral_fees_type']= $arr_trans_item->p_referral_fees_type;
                        $arr_item['unit']               = $qty_to_send;
                        $arr_item['gst_rate']           = $arr_trans_item->gst_rate_item;
                        $arr_item['disc']               = $unit_disc * $qty_to_send;
                        $arr_item['total']              = ($arr_trans_item->price - $unit_disc) * $qty_to_send;

                        Refund::insert_refund_details($arr_item);
                        // var_dump($arr_item);

                    }
                }
            }
        }
    }

     public function getTransinfo($TransactionID)
    {
        $transf= DB::table('jocom_transaction')
                ->where('id', '=', $TransactionID);
            // ->first();
                return $transf;
    }
    public function get_transaction($TransactionID) {
         // $transinfo=DB::table('jocom_transaction')
        $transinfo=DB::table('logistic_transaction')
                        ->where('transaction_id','=',$TransactionID)
                        ->first();
                        // ¬
        return $transinfo;
    }
    public function getAlltransactiongroup()
    {
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));

                    foreach ($SysAdminRegion as  $value) {
                        $regionid = $value;
                       
                    }

        if($regionid == 0){
          $regionid = 1;  
        }

        return DB::table('logistic_transaction_group')
                 ->where('region_id','=',$regionid)
                 ->orderBy('logistic_transaction_group.groupname')
                 ->get();
    }
    public function getTransactiongroup($groupname,$groupid)
    {
        return DB::table('logistic_transaction_group')
                 ->where('groupname','=',$groupname)
                 ->where('id','=',$groupid)
                 ->first();
    }
    public function getAlltransactionmap($groupid,$transid)
    {
        return DB::table('logistic_transaction_map')
                 ->where('group_id','=',$groupid)
                 ->where('transaction_id','=',$transid)
                 ->get();
    }
    public function getTransactionmapid($transid)
    {
        return DB::table('logistic_transaction_map')
                 ->where('transaction_id','=',$transid)
                 ->get();
    }
    public function getGrouptransaction($groupname,$groupid)
    {
        $group_name=DB::table('logistic_transaction_group')
                      ->where('groupname','=',$groupname)  
                      ->where('id','=',$groupid)
                      ->first();
         // print $group_name->id;             
        $group=DB::table('logistic_transaction_map')
                        ->select('transaction_id')
                        ->where('group_id','=',$groupid)
                        ->get();
        return $group;
    }
    public function getTransactionmap($transactionid)
    {
        return DB::table('logistic_transaction_map')
                        ->where('transaction_id','=',$transactionid);
                        // ->get();
                        // ->first();
    }
    public function insert_transactionmap(array $transactionid =[])
    {
        return DB::table('logistic_transaction_map')
                        ->wherein('transaction_id','=',$transactionid)
                        ->first();
    }

    
    /*
     * @Desc    : Get total of Logistic transaction base on status,from date , to date
     */
    public static function getTotalRecordByStatus($status ,$startDate,$toDate){
        
        $result = LogisticTransaction::where("status","=",$status)
                ->where("insert_date",">=",$startDate)
                ->where("insert_date","<=",$toDate)
                ->count();
        
        return $result;
    }
    
    public static function getTotalRecordByStatusRegion($status ,$startDate,$toDate){		
        		
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
        $result = LogisticTransaction::where("status","=",$status)		
                ->where("insert_date",">=",$startDate)		
                ->where("insert_date","<=",$toDate)		
                ->whereIn("delivery_state",$states)		
                ->count();		
        		
        return $result;		
    }

    public static function getAllTotalRecordByStatus($status,$date){

         $result = LogisticTransaction::where("status","=",$status)
                ->where("insert_date","<=",$date)
                ->count();
        
        return $result;

    }

    public static function getAllListDelay($status,$period){
        
        if($period == 1){
            
            $result = LogisticTransaction::where("status","=",$status)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",7)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",21)
                ->get();
            return $result;
            
        }
        
        if($period == 2){
            
            $result = LogisticTransaction::where("status","=",$status)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",21)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",31)
                ->get();
            return $result;
            
        }
        
        if($period == 3){
            
            $result = LogisticTransaction::where("status","=",$status)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),'>',31)
                ->get();
            return $result;
            
        }
        
    }
    
    public static function getAllListDelayRegion($status,$period){		
        		
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
        if($period == 1){		
            		
            $result = LogisticTransaction::where("status","=",$status)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",7)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",21)		
                ->whereIn("delivery_state",$states)		
                ->get();		
            return $result;		
            		
        }		
        		
        if($period == 2){		
            		
            $result = LogisticTransaction::where("status","=",$status)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",21)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",31)		
                ->whereIn("delivery_state",$states)		
                ->get();		
            return $result;		
            		
        }		
        		
        if($period == 3){		
            		
            $result = LogisticTransaction::where("status","=",$status)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),'>',31)		
                ->whereIn("delivery_state",$states)		
                ->get();		
            return $result;		
            		
        }		
        		
    }
    
    public static function getAllTotalDelaybyMonth($status,$period){
        
        $day = 7;
        if(in_array($total_month, array(1,2))){
            $operation_start = '>=';
            $operation_end = '<';
        }else{
            $operation_start = '>';
        }
        
        if($period == 1){
            
        $result = LogisticTransaction::where("status","=",$status)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",7)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",21)
                ->count();
            return $result;

        }
        
        if($period == 2){
            
            $result = LogisticTransaction::where("status","=",$status)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",21)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",31)
                ->count();
        return $result;
            
    }

        if($period == 3){
            
            $result = LogisticTransaction::where("status","=",$status)
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),'>',31)
                ->count();
            return $result;
            
        }
        
        
        
    }
    
    public static function getAllTotalDelaybyMonthRegion($status,$period){		
        		
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
        $day = 7;		
        if(in_array($total_month, array(1,2))){		
            $operation_start = '>=';		
            $operation_end = '<';		
        }else{		
            $operation_start = '>';		
        }		
        		
        if($period == 1){		
            		
            $result = LogisticTransaction::where("status","=",$status)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",7)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",21)		
                ->whereIn("delivery_state",$states)		
                ->count();		
            return $result;		
            		
        }		
        		
        if($period == 2){		
            		
            $result = LogisticTransaction::where("status","=",$status)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),">=",21)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),"<",31)		
                ->whereIn("delivery_state",$states)		
                ->count();		
            return $result;		
            		
        }		
        		
        if($period == 3){		
            		
            $result = LogisticTransaction::where("status","=",$status)		
                ->where(DB::raw('TIMESTAMPDIFF(DAY,insert_date,NOW())'),'>',31)		
                ->whereIn("delivery_state",$states)		
                ->count();		                
            return $result;		            
            		            
        }
    }

    public static function getLogisticID($transactionid){
        //print "Hello";
        $result = DB::table('logistic_transaction')
                    ->where('transaction_id','=',$transactionid)->first();
        return $result;

    }

    public static function getGtransaction($groupid){

        $transaction = array();

        //print 'CS'.$groupid.'CS';

        $result = DB::table('logistic_transaction_map')
                    ->where('group_id','=',$groupid)
                    ->get();

                    // print_r($result);

                foreach ($result as  $value) {
                    $array = array('transaction_id' => $value->transaction_id, 
                        );          
                    array_push($transaction, $array);
                 }
                 //print_r($transaction);
        return $transaction;
    }

     public static function getBatchtransaction($driverid,$startDate,$toDate){
        $status = 0;
        $result = LogisticBatch::where("driver_id","=",$driverid)
                // ->where('driver_id','=',$driverid)
                ->where("assign_date",">=",$startDate)
                ->where("assign_date","<=",$toDate)
                ->count();
    
        return $result;

    }

    public static function getBatchtransactionSent($driverid,$startDate,$toDate){
        $status = 4;
        $result = LogisticBatch::where("driver_id","=",$driverid)
                ->where('status','=',$status)
                ->where("modify_date",">=",$startDate)
                ->where("modify_date","<=",$toDate)
                ->count();
        return $result;

    }

    public static function getBatchdelivered($driverid,$startDate,$toDate){
        $status = 4;

        $result = DB::table('logistic_batch')
                ->where("status","=",$status)
                ->where('driver_id','=',$driverid)
                ->where("modify_date",">=",$startDate)
                ->where("modify_date","<=",$toDate)
                ->orderBy("modify_date",'ASC')
                ->get();
        return $result;

    }

    public static function getTotaltranslistingbystatus($status){
        
        $result    = LogisticTransaction::where("status","=",$status)  
                     ->count();
        return $result;    
    }

    public static function getTotalbatchbystatus($status){
    
        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        }
        
        $result    = LogisticBatch::where("status","=",$status)
                     ->where("assign_date",">=",$startdate)
                     ->where("assign_date","<=",$enddate)
                     ->count();
        return $result;                      


    }


    public static function getTotalbatchtoday(){

        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        }

        // $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
        // $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";

        $driverstatus = 0;
        
        $result    = LogisticBatch::where("assign_date",">=",$startdate)
                     ->where("assign_date","<=",$enddate)
                     ->where("driver_id","<>",$driverstatus)
                     ->count();
        return $result;                      

    }

    public static function getTotaldailybatch($driverid){

        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
            // $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
            $enddate   = Date('Y-m-d')." 08:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
            // $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
            $enddate   = Date('Y-m-d')." 08:59:59";
        }

        // $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
        // $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        
        $result    = LogisticBatch::where("assign_date",">=",$startdate)
                     ->where("assign_date","<=",$enddate)
                     ->where("driver_id","=",$driverid)
                     ->count();
        return $result;                      

    }


    public static function getTotaldailybatchsent($driverid){

        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
            // $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
            $enddate   = Date('Y-m-d')." 08:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
            // $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
            $enddate   = Date('Y-m-d')." 08:59:59";
        }

        // $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
        // $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        
        $result    = LogisticBatch::where("assign_date",">=",$startdate)
                     ->where("assign_date","<=",$enddate)
                     ->where("driver_id","=",$driverid)
                     ->where("status",4)
                     ->count();
        return $result;                      

    }

    public static function getTotalbatchPending($option){

        $currentday = date('l'); 

        $driverstatus = 0;
        
        


        if($currentday == 'Monday'){

            if($option == 1)
            {
                $startdate = Date('Y-m-d',strtotime("-3 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
            } 
            elseif ($option == 2) {
                $startdate = Date('Y-m-d',strtotime("-4 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-4 days"))." 23:59:59";
             } 
             elseif ($option == 3) {
                $startdate = Date('Y-m-d',strtotime("-5 days"))." 23:59:59";
             } 
            
        }
        else 
        {
            if($option == 1)
            {
                $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
               
            } 
            elseif ($option == 2) {
                $startdate = Date('Y-m-d',strtotime("-3 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
                
             } 
             elseif ($option == 3) {
                $startdate = Date('Y-m-d',strtotime("-4 days"))." 23:59:59";
             } 
        }

        if($option == 3){
            $result    = LogisticBatch::where("driver_id","<>",$driverstatus)
                      // ->where('status','=',0)
                      // ->orwhere('status','=',1)
                      ->where(function ($query){
                        $query->where('status','=',0)
                              ->orwhere('status','=',1);
                       })
                      ->where("assign_date","<=",$startdate)
                      ->count();
        }
        else
        {
            $result    = LogisticBatch::where("driver_id","<>",$driverstatus)
                      // ->where('status','=',0)
                      // ->orwhere('status','=',1)
                      ->where(function ($query){
                        $query->where('status','=',0)
                              ->orwhere('status','=',1);
                       })
                      ->where("assign_date",">=",$startdate)
                      ->where("assign_date","<=",$enddate)
                      ->count();
        }

        return $result;                      

    }




    public static function getDriverTeam($status){
        $result = DB::table('logistic_driver_team')
                      ->where("status","=",$status)
                      ->orderBy("team_sequence","ASC")
                      ->get();
        return $result;
    }

    public static function getDriverName($driverid){

        // print 'New'.$driverid.'New';
        $result = DB::table('logistic_driver')
                      ->where("id",$driverid)
                      ->first();
        return $result;


    }

    public static function getBatchdetails($driverid){
        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        }

        $result = LogisticBatch::where("driver_id","=",$driverid)
                    ->where("assign_date",">=",$startdate)
                    ->where("assign_date","<=",$enddate)
                    ->get();

        return $result;

    }

    public static function getBatchdetailsPendingDay($driverid){ 
        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }

        $result = LogisticBatch::where("driver_id","=",$driverid)
                    ->select('logistic_id','assign_date'
                        )
                    ->where("assign_date","<=",$startdate)
                    ->where(function ($query){
                        $query->where('status','=',0)
                              ->orwhere('status','=',1);
                    })
                    // ->where('status','=',0)
                    // ->orwhere('status','=',1)
                    ->get();

        return $result;

    }
    
    public static function getBatchdetailsover60Days(){ 
        $currentday = date('l'); 

        $currday  = '%'.Date('Y-m-d').'%';
        $currday1 = Date('Y-m-d').' 00:00:00';
        $currday2 = Date('Y-m-d').' 23:59:59';


        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }
        
        $startdate = Date('Y-m-d')." 23:59:59";
        
        // echo $startdate;
        $result = LogisticBatch::select('logistic_batch.logistic_id', DB::raw('MAX(logistic_batch.assign_date) as assign_date')
                        )
                    ->leftjoin('logistic_transaction','logistic_transaction.id','=','logistic_batch.logistic_id')
                    ->where("logistic_batch.assign_date","<=",$startdate)
                    ->whereIn('logistic_batch.status',array(0,1,2))
                    ->whereIn('logistic_transaction.status',array(0,3,4))
                    // ->where(function ($query){
                    //     $query->where('status','=',0)
                    //           ->orwhere('status','=',1);
                            
                    // })
                    ->groupby('logistic_transaction.transaction_id')
                     ->orderby('logistic_batch.id','DESC')
                    ->get();


        return $result;

    }

    public static function getBatchdetailsTaqbinlineclear(){ 
        $currentday = date('l'); 


        $result = LogisticBatch::select('logistic_batch.logistic_id','logistic_batch.assign_date','jocom_courier_orders.courier_id'
                        )
                   ->join('jocom_courier_orders','jocom_courier_orders.batch_id','=','logistic_batch.id')
                    ->where(function ($query){
                        $query->where('jocom_courier_orders.courier_id','=',1)
                              ->orwhere('jocom_courier_orders.courier_id','=',2);
                            
                    })
                    ->where(function ($query){
                        $query->where('logistic_batch.status','=',0)
                              ->orwhere('logistic_batch.status','=',1);
                             
                              // ->where('modify_date','>=',$currday1)
                              // ->where('modify_date','<=',$currday2);
                    })
                    

                    // ->where('status','=',0)
                    // ->orwhere('status','=',1)
                    ->get();


        return $result;

    }

    public static function getBatchdetailsDeliveryDay($driverid){ 
        $currentday = date('l'); 

        $currday  = '%'.Date('Y-m-d').'%';
        $currday1 = Date('Y-m-d').' 00:00:00';
        $currday2 = Date('Y-m-d').' 23:59:59';

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }

        // echo $startdate;

        $result = LogisticBatch::where("driver_id","=",$driverid)
                    ->select('logistic_id','assign_date'
                        )
                    ->where("modify_date","like",$currday)
                    ->where("batch_date","<=",$startdate)
                    ->where('status','=',4)
                    ->get();
            
       return $result;

    }
    
    public static function getValidTransaction($id){ 

        $count = 0;
        $status = 0;

        $result = LogisticTransaction::where("id","=",$id)
                     ->get();
        if(count($result) > 1){
            $resultcount = LogisticTransaction::where("id","=",$id)
                                ->orderby('insert_date','DESC')
                                ->first();

            $status = $resultcount->status;

            if($status != 3 || $status != 6)
            { 
               $count = 1; 
            }

        }  
        
       return $count;

    }

    public static function getTransactionID($logisticid){
        $result = LogisticTransaction::where("id",'=',$logisticid)
                    ->first();
        return $result;

    }

    public static function getBatchIDStatus($logisticid){
        // $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
        // $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        $result = LogisticBatch::where("logistic_id",'=',$logisticid)
                     // ->where("assign_date",">=",$startdate)
                     // ->where("assign_date","<=",$enddate)  
                     ->orderBy('assign_date','DESC')
                    ->first();
        return $result;

    }
    
    public static function getAllTotalRecordByStatusRegion($status,$date){		
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
         $result = LogisticTransaction::where("status","=",$status)		
                ->whereIn("delivery_state",$states)		
                ->where("insert_date","<=",$date)		
                ->count();		
        		
        return $result;		
    }		
    public static function getStates($username){		
        $user = DB::table('jocom_sys_admin')->where('username','=',$username)->first();		
        $region = DB::table('jocom_sys_admin_region AS JSR')		
                    ->select('JSR.*')        		
                    ->where('JSR.sys_admin_id', $user->id)		
                    ->where('JSR.status', 1)		
                    ->first();		
        $states = DB::table('jocom_country_states')->where('region_id',$region->region_id)->lists('name');		
        		
        return $states;		
    }	
    
     /* Region   */
      
    public static function getDriverRegionName($regionid){

        $result = DB::table('jocom_region')
                      ->where("id",$regionid)
                      ->first();
        return $result;


    }


    public static function getTotaltranslistingbystatusRegion($status,$regionid,$states){
        
        if(isset($regionid) && $regionid==0){
            $result    = LogisticTransaction::where("status","=",$status)  
                        ->count();
        }else{
            $result    = LogisticTransaction::where("status","=",$status)  
                         ->wherein('delivery_state',$states)
                         ->count();
        }


        
        return $result;    
    }


    public static function getTotalbatchPendingRegion($option,$regionid,$states){

        $currentday = date('l'); 

        $driverstatus = 0;

        if($currentday == 'Monday'){

            if($option == 1)
            {
                $startdate = Date('Y-m-d',strtotime("-3 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
            } 
            elseif ($option == 2) {
                $startdate = Date('Y-m-d',strtotime("-4 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-4 days"))." 23:59:59";
             } 
             elseif ($option == 3) {
                $startdate = Date('Y-m-d',strtotime("-5 days"))." 23:59:59";
             } 
            
        }
        else 
        {
            if($option == 1)
            {
                $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
               
            } 
            elseif ($option == 2) {
                $startdate = Date('Y-m-d',strtotime("-3 days"))." 00:00:00";
                $enddate   = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
                
             } 
             elseif ($option == 3) {
                $startdate = Date('Y-m-d',strtotime("-4 days"))." 23:59:59";
             } 
        }

        if($option == 3){
            if(isset($regionid) && $regionid==0){
            $result    = LogisticBatch::where("driver_id","<>",$driverstatus)
                      // ->where('status','=',0)
                      // ->orwhere('status','=',1)
                      ->where(function ($query){
                        $query->where('status','=',0)
                              ->orwhere('status','=',1);
                       })
                      ->where("assign_date","<=",$startdate)
                      ->count();
            }else{
               $result   = DB::table('logistic_batch AS LB')
                            ->leftjoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id') 
                            ->wherein('LT.delivery_state',$states)
                            ->where("LB.driver_id","<>",$driverstatus)
                              ->where(function ($query){
                                $query->where('LB.status','=',0)
                                      ->orwhere('LB.status','=',1);
                               })
                              ->where("LB.assign_date","<=",$startdate)
                              ->count();   
            }
        }
        else
        {
            if(isset($regionid) && $regionid==0){
                $result    = LogisticBatch::where("driver_id","<>",$driverstatus)
                          // ->where('status','=',0)
                          // ->orwhere('status','=',1)
                          ->where(function ($query){
                            $query->where('status','=',0)
                                  ->orwhere('status','=',1);
                           })
                          ->where("assign_date",">=",$startdate)
                          ->where("assign_date","<=",$enddate)
                          ->count();
             }else{
                $result    = DB::table('logistic_batch AS LB')
                            ->leftjoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id') 
                            ->wherein('LT.delivery_state',$states)
                            ->where("LB.driver_id","<>",$driverstatus)
                              ->where(function ($query){
                                $query->where('LB.status','=',0)
                                      ->orwhere('LB.status','=',1);
                               })
                              ->where("LB.assign_date",">=",$startdate)
                              ->where("LB.assign_date","<=",$enddate)
                              ->count();
             }

        }


        return $result;                      

    }

    public static function getTotalbatchtodayRegion($regionid,$stateName){

        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        }

        // $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
        // $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";

        $driverstatus = 0;


        if(isset($regionid) && $regionid==0){
            $result    = LogisticBatch::where("assign_date",">=",$startdate)
                         ->where("assign_date","<=",$enddate)
                         ->where("driver_id","<>",$driverstatus)
                         ->count();
        }else{

            $result    = DB::table('logistic_batch AS LB')
                         ->leftjoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id') 
                         ->wherein('LT.delivery_state',$stateName)
                         ->where("LB.assign_date",">=",$startdate)
                         ->where("LB.assign_date","<=",$enddate)
                         ->where("LB.driver_id","<>",$driverstatus)
                         ->count();

        }                 

        return $result;                      

    }


    public static function getTotalbatchbystatusRegion($status,$regionid,$stateName){
    
        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $startdate = Date('Y-m-d',strtotime("-2 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
        }
        else 
        {
            $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
            $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
        }
        if(isset($regionid) && $regionid==0){
            $result    = LogisticBatch::where("status","=",$status)
                         ->where("assign_date",">=",$startdate)
                         ->where("assign_date","<=",$enddate)
                         ->count();
        }else{
            $result    = DB::table('logistic_batch AS LB')
                        ->leftjoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id') 
                        ->wherein('LT.delivery_state',$stateName)
                        ->where("LB.status","=",$status)
                        ->where("LB.assign_date",">=",$startdate)
                        ->where("LB.assign_date","<=",$enddate)
                        ->count();

        }
        return $result;                      


    }
    
    public static function getDriverRegion($regionid){

        $result = DB::table('logistic_driver')
                      ->select('id')
                      ->where("id",$regionid)
                      ->get();
        return $result;


    }

    public static function getTransactionpending($stateName){

        $result = LogisticTransaction::wherein('delivery_state',$stateName)
                    ->select('transaction_id')
                    ->where('status','=',0)
                    ->get();
        return $result;

    }
    
    public static function getTransactionreturned($username){
        $states = LogisticTransaction::getStates($username);
        $status = 3;
        $result = LogisticTransaction::where("status","=",$status)
                // ->whereIn("delivery_state",$states)
                ->get();
        return $result;

    }
    
    public static function getTransactionpartialsent($username){
        $states = LogisticTransaction::getStates($username);

        $status = 2;
        $result = LogisticTransaction::where("status","=",$status)
                // ->whereIn("delivery_state",$states)
                ->get();
        return $result;

    }
    
    public static function getTransactionpending2weeks(){ 
        $currentday = date('l'); 

        $currday  = '%'.Date('Y-m-d').'%';
        $currday1 = Date('Y-m-d').' 00:00:00';
        $currday2 = Date('Y-m-d').' 23:59:59';

        $startdate = Date('Y-m-d',strtotime("-1 week"))." 23:59:59";


        $LogisticPendingReport =  LogisticTransaction::select(
                                        'logistic_transaction.transaction_id',
                                        'logistic_transaction.transaction_date',
                                        'logistic_transaction.delivery_name',
                                        'logistic_transaction.delivery_contact_no',
                                        'logistic_transaction.buyer_email',
                                        'logistic_transaction.delivery_city',
                                        'logistic_transaction.delivery_state',
                                        'logistic_transaction.delivery_country',
                                        'logistic_transaction_item.sku',
                                        'logistic_transaction_item.name',
                                        'logistic_transaction_item.label',
                                        DB::raw("(CASE WHEN logistic_transaction.status='0' THEN 'Pending' WHEN logistic_transaction.status='4' THEN 'Sending' END ) as status")
                                        )
                                    ->leftjoin('logistic_transaction_item','logistic_transaction_item.logistic_id','=','logistic_transaction.id')
                                    ->where('logistic_transaction.transaction_date','<=',$startdate)
                                    ->whereIn('logistic_transaction.status',array(0,4))
                                    ->orderby('logistic_transaction.transaction_date')
                                    ->get();


            $fileName = 'deliveryorder_pending'.date('Y-m-d H:i:s').".csv";
                  $path = Config::get('constants.CSV_PENDING_DELIVERY_PATH');
                  $file = fopen($path.'/'.$fileName, 'w');

                  fputcsv($file, ['transaction_id', 'transaction_date','delivery_name','delivery_contact_no', 'buyer_email','delivery_city_id','delivery_state','delivery_country','sku','name','label','status']);

                  

                  if(count($LogisticPendingReport)> 0) {
                    foreach ($LogisticPendingReport as $row)
                      {   
                              fputcsv($file, [
                                 $row->transaction_id,
                                  $row->transaction_date,
                                  $row->delivery_name,
                                  $row->delivery_contact_no,
                                  $row->buyer_email,
                                  $row->delivery_city,
                                  $row->delivery_state,
                                  $row->delivery_country,
                                  $row->sku,
                                  $row->name,
                                  $row->label,
                                  $row->status,

                              ]);  
                          
                      }
                    
                  }

                  

                  


                  
                  fclose($file);
                  
                  $test = Config::get('constants.ENVIRONMENT');
                  if ($test == 'test')
                      $mail = ['maruthu@tmgrocer.com'];
                  else
                      $mail = ['quenny.leong@tmgrocer.com','maruthu@tmgrocer.com'];

                  $subject = "Delivery Order Pending Notification[More than 7 Days] : " . $fileName;
                  $attach = $path . "/" . $fileName;
                  $body = array('title' => 'Delivery Order Pending');
                  
                  Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
                      {
                          $message->from('notification@tmgrocer.com', 'tmGrocer');
                          $message->to($mail, '')->subject($subject);
                          $message->attach($attach);
                      }
                  );        



    }

   public static function getBatchlistwithoutimage(){

        $startdate = Date('Y-m-d',strtotime("-1 days"))." 00:00:00";
        $enddate   = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";

        $LogisticBatchReport =  LogisticBatch::select(
                                        
                                        'logistic_batch.id AS batch_id',
                                        'logistic_batch.batch_date',
                                        'logistic_transaction.transaction_id',
                                        'logistic_driver.name',
                                        'logistic_transaction.delivery_city',
                                        'logistic_transaction.delivery_state',
                                        'logistic_transaction.do_no',
                                        'logistic_batch.batch_date AS delivery_date',
                                        DB::raw("(CASE WHEN logistic_batch.status='4' THEN 'Sent' END ) as status")

                                        )
                                    ->leftjoin('logistic_transaction','logistic_transaction.id','=','logistic_batch.logistic_id')
                                    ->leftjoin('logistic_driver','logistic_driver.id','=','logistic_batch.driver_id')
                                    ->where('logistic_batch.modify_date','>=',$startdate)
                                    ->where('logistic_batch.modify_date','<=',$enddate)
                                    ->where('logistic_batch.status','=',4)
                                    ->where('logistic_driver.region_id','=',1)
                                    ->orderby('logistic_batch.batch_date')
                                    ->get();

                 $fileName = 'deliveryorder_withoutimage'.date('Y-m-d H:i:s').".csv";
                  $path = Config::get('constants.CSV_WITHOUT_PHOTO_PATH');
                  $file = fopen($path.'/'.$fileName, 'w');

                  fputcsv($file, ['batch_id', 'batch_date','transaction_id','name', 'delivery_city','delivery_state','do_no','delivery_date','status']);

                if(count($LogisticBatchReport)> 0) {
                    foreach ($LogisticBatchReport as $row)
                      {   
                            $batchimgresponse = 0;
                            $batchimgresponse = self::getBatchImagesavailable($row->batch_id);

                            if($batchimgresponse == 0){
                                fputcsv($file, [
                                  $row->batch_id,
                                  $row->batch_date,
                                  $row->transaction_id,
                                  $row->name,
                                  $row->delivery_city,
                                  $row->delivery_state,
                                  $row->do_no,
                                  $row->delivery_date,
                                  $row->status,

                              ]); 
                            }
                                


                          
                      }
                    
                }



                  fclose($file);
                  
                  $test = Config::get('constants.ENVIRONMENT');
                  if ($test == 'test')
                      $mail = ['maruthu@tmgrocer.com'];
                  else
                      $mail = ['quenny.leong@tmgrocer.com','maruthu@tmgrocer.com'];

                  $subject = "Delivery Order Without Images : " . $fileName;
                  $attach = $path . "/" . $fileName;
                  $body = array('title' => 'Delivery Order Without Images');

                if(count($LogisticBatchReport) == 0) {
                    $body = array('title' => 'No Delivery Order Found');
                    $subject = "No Delivery Order Found : " . $fileName;
                }
                  
                  Mail::send('emails.blank', $body, function($message) use ($subject, $mail, $attach)
                      {
                          $message->from('notification@tmgrocer.com', 'tmGrocer');
                          $message->to($mail, '')->subject($subject);
                          $message->attach($attach);
                      }
                  );  


    }


   public static function getBatchImagesavailable($batchid){
        $response = 0;

       $LogisticBatchImage = LogisticBatchImage::where("batch_id",$batchid)
                            ->where("status",1)->get();
                             
        if(count($LogisticBatchImage)>0){

            $response = 1;
        }

        return  $response;
                    
    } 

   

    

    
}