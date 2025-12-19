<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class LogisticBatch extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    
    protected $table = 'logistic_batch';

    public static $rules = array(
        'signature'      =>'mimes:gif,jpeg,jpg,png',
    );

    public static function get_status($status = "")
    {
        $value = "";

        switch ($status)
        {
            case '0':
                $value = 'Pending';
                break;
            case '1':
                $value = 'Sending';
                break;
            case '2':
                $value = 'Returned';
                break;
            case '3':
                $value = 'Undelivered';
                break;
            case '4':
                $value = 'Sent';
                break;
            case '5':
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
            case 'Sending':
                $value = '1';
                break;
            case 'Returned':
                $value = '2';
                break;
            case 'Undelivered':
                $value = '3';
                break;
            case 'Sent':
                $value = '4';
                break;
            case 'Cancelled':
                $value = '5';
                break;
            case 'Showall':
                $value = '6';
                break;
            default:
                $value = '0';
                break;
        }

        return $value;
    }

    public static function api_batch_status()
    {
        $data['status'] = array('Pending','Sending', 'Returned', 'Undelivered', 'Sent', 'Cancelled');
        return $data;
    }

    public static function api_batch_list($get = array())
    {
        $data   = array();
        $isSuccess = 'true';
        $respMessage = 'Success';
        
        if($get['batch_date'] != '')
        {
            $request_date= '%' . $get['batch_date'] .'%';
        }
        else
        {
            $request_date = '%%';
        }

        if($get['accept_date'] != '')
        {
            $accept_date= '%' . $get['accept_date'] .'%';
        }
        else
        {
            $accept_date = '%%';
        }

        if($get['assign_date'] != '')
        {
            $assign_date= '%' . $get['assign_date'] .'%';
        }
        else
        {
            $assign_date = '%%';
        }

        if($get['status'] != '')
        {
            $tempstatus = LogisticBatch::get_status_int($get['status']);
            if($tempstatus == '6')
            {
                $request_status = array('0', '1', '2', '3', '4', '5');
            }
            else
            {
                $request_status = array($tempstatus);
            }
        }
        else
        {
            $request_status = array('0', '1');
        }

        if ($get['keyword']!='') {
           $keyword = '%' . $get['keyword'] .'%';
        }
        else
        {
            $keyword = '%%';
        }

        if($get['image_status'] != '' ){
            
            //echo $get['image_status'] ;
            
            // all batch listing
        $batch = DB::table('logistic_batch AS a')
                    ->select('a.*', 'b.transaction_id', 'b.delivery_city', 'b.special_msg')
                    ->leftJoin('logistic_transaction AS b', 'a.logistic_id', '=', 'b.id')
                    ->leftJoin('logistic_driver AS c', 'a.driver_id', '=', 'c.id')
                    ->leftJoin('logistic_batch_image AS LBI', 'LBI.batch_id', '=', 'a.id')
                    ->whereIn('a.status', $request_status)
                    ->where('a.batch_date', 'like', $request_date)
                    ->where('a.accept_date', 'like', $accept_date)
                    ->where('a.assign_date', 'like', $assign_date)
                    ->where('c.username', '=', $get['username'])
                    ->where('b.transaction_id', 'like', $keyword);
                    
        if($get['image_status'] == 0){
            
        $batch =     $batch->whereNull('LBI.id')
                    ->groupBy('a.id')
                    ->take($get['count'])
                    ->skip($get['from'])
                    ->get();
            
        } else{
            
        $batch = $batch->whereNotNull('LBI.id')
                    ->groupBy('a.id')
                    ->take($get['count'])
                    ->skip($get['from'])
                    ->get();
        }           
          
        $totalcount = DB::table('logistic_batch AS a')
                    ->select('a.*', 'b.delivery_city')
                    ->leftJoin('logistic_transaction AS b', 'a.logistic_id', '=', 'b.id')
                    ->leftJoin('logistic_driver AS c', 'a.driver_id', '=', 'c.id')
                    ->leftJoin('logistic_batch_image AS LBI', 'LBI.batch_id', '=', 'a.id')
                    ->whereIn('a.status', $request_status)
                    ->where('a.batch_date', 'like', $request_date)
                    ->where('a.accept_date', 'like', $accept_date)
                    ->where('a.assign_date', 'like', $assign_date)
                    ->where('c.username', '=', $get['username'])
                    ->where('b.transaction_id', 'like', $keyword)
                    ->where('LBI.id', null);
                    
            if($get['image_status'] == 0){
                
                $totalcount = $totalcount->whereNull('LBI.id')->groupBy('a.id')->count();
                
            } else{
                
                $totalcount = $totalcount->whereNotNull('LBI.id')->groupBy('a.id')->count();
            }           
          
            
        }else{
            
            // all batch listing
            $batch = DB::table('logistic_batch AS a')
                        ->select('a.*', 'b.transaction_id', 'b.delivery_city', 'b.special_msg')
                        ->leftJoin('logistic_transaction AS b', 'a.logistic_id', '=', 'b.id')
                        ->leftJoin('logistic_driver AS c', 'a.driver_id', '=', 'c.id')
                        ->whereIn('a.status', $request_status)
                        ->where('a.batch_date', 'like', $request_date)
                        ->where('a.accept_date', 'like', $accept_date)
                        ->where('a.assign_date', 'like', $assign_date)
                        ->where('c.username', '=', $get['username'])
                        ->where('b.transaction_id', 'like', $keyword)
                        ->take($get['count'])
                        ->skip($get['from'])
                        ->get();
            
            $totalcount = DB::table('logistic_batch AS a')
                        ->select('a.*', 'b.delivery_city')
                        ->leftJoin('logistic_transaction AS b', 'a.logistic_id', '=', 'b.id')
                        ->leftJoin('logistic_driver AS c', 'a.driver_id', '=', 'c.id')
                        ->whereIn('a.status', $request_status)
                        ->where('a.batch_date', 'like', $request_date)
                        ->where('a.accept_date', 'like', $accept_date)
                        ->where('a.assign_date', 'like', $assign_date)
                        ->where('c.username', '=', $get['username'])
                        ->where('b.transaction_id', 'like', $keyword)
                        ->count();

            
        }

        // for supervisor to retrieve all batches under a specific transaction
        if($get['logistic_id'] != '')
        {
            $supervisor = LogisticDriver::where('username', '=', $get['username'])->where('type', '=', '1')->where('status', '=', '1')->first();

            // only supervisor may access
            if(count($supervisor)>0)
            {
                $request_status = array('0', '1', '2', '3', '4', '5');
                
                $batch = DB::table('logistic_batch AS a')
                            ->select('a.*', 'b.transaction_id', 'b.delivery_city', 'b.special_msg')
                            ->leftJoin('logistic_transaction AS b', 'a.logistic_id', '=', 'b.id')
                            ->whereIn('a.status', $request_status)
                            ->where('a.logistic_id', '=', $get['logistic_id'])
                            ->where('a.batch_date', 'like', $request_date)
                            ->where('a.accept_date', 'like', $accept_date)
                            ->where('a.assign_date', 'like', $assign_date)
                            ->get();

                $totalcount = count($batch);
            }
            else
                $data['status_msg']  = 'Access Denied!';
                $isSuccess = 'false';
                $respMessage = 'Your access denied!';
        }
        

        $data['record'] = count($batch);
        $data['total_record'] = $totalcount;

        $details = array();

        foreach ($batch as $row)
        {
            $status = LogisticBatch::get_status($row->status);
            
            $TotalImage = LogisticBatchImage::where("batch_id",$row->id)->count();

            $details[] = array(
                'batch_id' => $row->id,
                'batch_date' => $row->batch_date,
                'accept_date' => $row->accept_date,
                'assign_date' => $row->assign_date,
                'transaction_id' => $row->transaction_id,
                'delivery_city' => $row->delivery_city,
                'special_msg' => $row->special_msg,
                'do_no' => $row->do_no,
                'is_image_uploaded' => $TotalImage > 0 ? true:false,
                'status' => $status
            );
        }

        $data['batch'] = $details;        
        $headerResponse = array(
            "isSuccess" => $isSuccess,
            "message" => $respMessage
        );
        $data = array_merge($headerResponse,$data);

        return $data;
    }

    
    public static function api_batch_detail($get = array())
    {
        $data   = array();

        $batch = LogisticBatch::where('id', '=', $get['batch_id'])->first();

        // $batch = DB::table('logistic_batch AS a')
        //             ->select('a.*', 'b.username')
        //             ->leftJoin('logistic_driver AS b', 'a.driver_id', '=', 'b.id')
        //             ->where('a.id', '=', $get['batch_id'])
        //             ->first();

        if(count($batch)>0)
        {
            $diplay_listing = 0;
            $driver = LogisticDriver::find($batch->driver_id);
            $flagPickup = false;
            
            // supervisor requesting details
            if(strtolower($get['username']) != strtolower($driver->username))
            {
                $supervisor = LogisticDriver::where('username', '=', $get['username'])->where('type', '=', '1')->where('status', '=', '1')->first();

                if(count($supervisor)>0)
                {
                    $diplay_listing = 1;
                    $data['status_msg']  = 'Not allow to update!';
                    $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => 'Your are not allow to update this batch!'
                    );
                    $data = array_merge($headerResponse,$data);
                }
                else 
                {
                    $data['status_msg']  = 'Access Denied!';
                    $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => 'Your access denied!'
                    );
                    $data = array_merge($headerResponse,$data);
                }
            }
            else
            {
                $diplay_listing = 2;
            }


            if($diplay_listing > 1)
            {
                // Returned, Undelivered, Sent, Cancelled batch are not allow to update
                if($batch->status > 1)
                {
                    $data['status_msg']  = 'Not allow to update!';
                    $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => 'Your are not allow to update this batch!'
                    );
                    $data = array_merge($headerResponse,$data);
                }
                else
                {
                    // update remark
                    if($get['remark'] != '')
                    {
                        $tempremark = date('Y-m-d H:i:s') . " " . $get['username'] . ": " . $get['remark'];
                        if($batch->remark == '')
                            $batch->remark = $tempremark;
                        else
                            $batch->remark = $batch->remark . "\n" . $tempremark;
                        $batch->modify_by = $get['username'];
                        $batch->modify_date = date('Y-m-d H:i:s');
                        $batch->save();

                        $data['status_msg']  = 'Remark Updated!';
                        $headerResponse = array(
                            "isSuccess" => 'true',
                            "message" => 'Your changes have been successfully updated!'
                        );
                        $data = array_merge($headerResponse,$data);
                    }

                    // update qty
                    if($get['batch_item_id'] != '')
                    {
                        foreach ($get['batch_item_id'] as $k => $v)
                        {
                            $batch_item = LogisticBatchItem::find($get['batch_item_id'][$k]);

                            if(count($batch_item)>0)
                            {
                                if($get['remark_item'][$k] != '')
                                {
                                    $tempremark = date('Y-m-d H:i:s') . " " . $get['username'] . ": " . $get['remark_item'][$k];
                                    if($batch_item->remark == '')
                                        $batch_item->remark = $tempremark;
                                    else
                                        $batch_item->remark = $batch_item->remark . "\n" . $tempremark;
                                }

                                if($get['qty_sent'][$k] != '')
                                {
                                    if($get['qty_sent'][$k] > $batch_item->qty_assign)
                                        $qtysent = $batch_item->qty_assign;
                                    else
                                        $qtysent = $get['qty_sent'][$k];

                                    $batch_item->qty_sent = $qtysent;
                                }

                                if($get['qty_pickup'][$k] != '')
                                {
                                    if($get['qty_pickup'][$k] > $batch_item->qty_assign)
                                        $qtypickup = $batch_item->qty_assign;
                                    else
                                        $qtypickup = $get['qty_pickup'][$k];

                                    $batch_item->qty_pickup = $qtypickup;

                                    $flagPickup = true;
                                }
                                
                                $batch_item->modify_by = $get['username'];
                                $batch_item->modify_date = date('Y-m-d H:i:s');
                                $batch_item->save();

                                //inventory history update pickup qty
                                $Trans_ID = LogisticTransaction::find($batch->logistic_id);
                                $productList = LogisticTItem::find($batch_item->transaction_item_id);

                                 //passing var
                                $username = $get['username'];
                                $date = date('Y-m-d H:i:s');
                                $HistoryStatus = 'Pickup';
                                $TransID = $Trans_ID->transaction_id;
                                $qty_assignHistory = $batch_item->qty_assign;
                                $qty_pickupHistory = $batch_item->qty_pickup;
                                $product_name = $productList->name;

                                LogisticBatch::getInventoryHistory($username,$date,$HistoryStatus,$TransID,$qty_assignHistory,$qty_pickupHistory,$product_name);

                            }
                        }
                        $batch->status = '1';
                        $batch->modify_by = $get['username'];
                        $batch->modify_date = date('Y-m-d H:i:s');

                        $batch->save();

                        $data['status_msg']  = 'Quantity Updated!';
                        $headerResponse = array(
                            "isSuccess" => 'true',
                            "message" => 'Your changes have been successfully updated!'
                        );
                        $data = array_merge($headerResponse,$data);
                    }

                    // update status
                    if($get['status'] != '')
                    {
                        $update = 1;
                        $status = LogisticBatch::get_status_int($get['status']);

                        // not allow to change to Sent or Cancelled
                        if($status == '4' OR $status == '5')
                        {
                            $update = 0;
                            $data['status_msg']  = 'Status not updated!';
                            $headerResponse = array(
                                "isSuccess" => 'false',
                                "message" => 'Your are not allow to update status to Sent/Cancelled!'
                            );
                            $data = array_merge($headerResponse,$data);
                        }
                        // Returned and Undelivered  parcel
                        else if($status == '2' OR $status == '3')
                        {                        
                            $transaction = LogisticTransaction::find($batch->logistic_id);
                            switch ($status) {
                                //Returned
                                case '2':
                                    $transaction->status = 3;
                                    break;
                                // Undelivered
                                case '3':
                                    $transaction->status = 1;
                                    break;
                            }
                            $transaction->modify_date = date('Y-m-d H:i:s');
                            $transaction->save();

                            $batch_item = LogisticBatchItem::where('batch_id', '=', $get['batch_id'])->get();

                            foreach ($batch_item as $item)
                            {
                                //qty_order qty_to_assign   qty_to_send qty_assign   qty_sent
                                //10        10              10          0            0
                                //10        4               10          6            0
                                //10        4               10          6            4
                                //new batch
                                //10        10              10          0            0
                                //10        6               10          4            0
                                $itemlist = LogisticTItem::find($item->transaction_item_id);
                                $itemlist->qty_to_assign += $item->qty_assign;
                                $itemlist->save();

                                //inventory history update returned, undelivered from logistic app
                                //passing var
                                $username = $get['username'];
                                $date = date('Y-m-d H:i:s');
                                $HistoryStatus = $get['status'];
                                $TransID = $transaction->transaction_id;
                                $qty_assignHistory = $item->qty_assign;
                                $qty_pickupHistory = $item->qty_assign;
                                $product_name = $itemlist->name;

                                LogisticBatch::getInventoryHistory($username,$date,$HistoryStatus,$TransID,$qty_assignHistory,$qty_pickupHistory,$product_name);
                            }

                            $data['status_msg']  = 'Transaction Status Updated!';
                            $headerResponse = array(
                                "isSuccess" => 'true',
                                "message" => 'Your changes have been successfully updated!'
                            );
                            $data = array_merge($headerResponse,$data);
                                
                        }

                        if($update == 1)
                        {
                            $batch->status = $status;
                            $batch->modify_by = $get['username'];
                            $batch->modify_date = date('Y-m-d H:i:s');
                            $batch->save();

                            $data['status_msg']  = 'Status Updated!';
                            $headerResponse = array(
                                "isSuccess" => 'true',
                                "message" => 'Your changes have been successfully updated!'
                            );
                            $data = array_merge($headerResponse,$data);

                        }
                        
                    }

                    if ($get['delivery_img']!='') {

                        // $file_name = $batch->id.".".$get['delivery_img']->getClientOriginalExtension(); 
                        // $path = Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH');
                        // $upload_file_succ = $get['delivery_img']->move($path, $file_name);
                        
                        // $batch->delivery_img = $file_name;

                        // $batch->save();
                        
                        
                        $dateTime = date("YmdHis");
                        $file_name = $batch->id."_".$dateTime.".".$get['delivery_img']->getClientOriginalExtension(); 
                        $path = Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH');
                        $upload_file_succ = $get['delivery_img']->move($path, $file_name);
                        
                        $LogisticBatchImage = new LogisticBatchImage;
                        $LogisticBatchImage->batch_id = $batch->id;
                        $LogisticBatchImage->filename = $file_name;
                        $LogisticBatchImage->created_by = '';
                        $LogisticBatchImage->save();

                    }

                    if($get['signature'] != '')
                    {
                        $batch->accept_date = isset($get['accept_date']) ? $get['accept_date'] : date('Y-m-d H:i:s');
                        $batch->latitude = isset($get['latitude']) ? $get['latitude'] : '';
                        $batch->longitude = isset($get['longitude']) ? $get['longitude'] : '';
                        $batch->sign_name = isset($get['sign_name']) ? $get['sign_name'] : '';
                        $batch->sign_ic = isset($get['sign_ic']) ? $get['sign_ic'] : '';

                        $fileOK = false;
                        $validator = Validator::make($get, LogisticBatch::$rules);

                        if ($validator->passes())
                        {
                            $fileOK = true;
                        }
                        else
                            $fileOK = false;

                        // save valid file
                        if($fileOK == true)
                        {
                            $haveFile = true;
                            $file_name = "";
                            $add = "";
                            $count = 1;

                            while ($haveFile === true)
                            {
                                $file_name = "SIG-" . $batch->id . $add . "." . $get['signature']->getClientOriginalExtension(); 
                                $path = Config::get('constants.LOGISTIC_SIG_PATH');
                               
                                if(LogisticBatch::check_file($file_name, $path))
                                {
                                    //file exist
                                    $haveFile = true;
                                    $add = "-" . $count;
                                    $count++;
                                }
                                else
                                    //no file
                                    $haveFile = false;
                            }

                            $upload_file_succ = $get['signature']->move($path, $file_name);

                            if(isset($upload_file_succ))
                                $batch->signature_file = $file_name;
                        }


                        $DOnum = LogisticGeneral::generate_batch_DO($batch);

                        $batch->do_no = $DOnum;
                        $batch->status = 4;
                        $batch->save();

                        $batch_item = LogisticBatchItem::where('batch_id', '=', $batch->id)->get();

                        foreach ($batch_item as $item)
                        {
                            //qty_order qty_to_assign   qty_to_send qty_assign   qty_sent
                            //10        10              10          0            0
                            //10        4               10          6            0
                            //10        4               10          6            4
                            //new batch
                            //10        6               6           0            0
                            //10        4               6           2            0
                            //10        4               4           2            2
                            //new batch
                            //10        0               4           4            0
                            //10        0               0           0            0
                            $itemlist = LogisticTItem::find($item->transaction_item_id);
                            $itemlist->qty_to_assign += $item->qty_assign - $item->qty_sent;
                            $itemlist->qty_to_send -= $item->qty_sent;
                            $itemlist->save();
                        }

                        $transaction = LogisticTransaction::find($batch->logistic_id);

                        $total_to_send = 0;

                        $listall = LogisticTItem::select('qty_to_send')->where('logistic_id', '=', $batch->logistic_id)->get();

                        foreach ($listall as $item2)
                        {
                            $total_to_send += $item2->qty_to_send;
                        }
                        if($total_to_send == 0){
                            $transaction->status = 5;
                            $transaction->modify_date = date('Y-m-d H:i:s');
                            
                        }else{
                            $transaction->status = 2;
                            $transaction->modify_date = date('Y-m-d H:i:s');
                        }
                        $transaction->save();
                        
                        if($total_to_send == 0){
                            $response = LazadaController::dbsdelivered($batch->logistic_id);
                        }

                        if ($DOnum != NULL)
                        {
                            $email = LogisticGeneral::do_mailout($transaction, $DOnum, $batch->id);
                        }

                        $data['status_msg']  = 'DO generated!';
                        $headerResponse = array(
                                "isSuccess" => 'true',
                                "message" => 'Your changes have been successfully updated!'
                            );
                        $data = array_merge($headerResponse,$data);
                    }
                }// end update
            }

            if($diplay_listing > 0)
            {
                $transaction = LogisticTransaction::find($batch->logistic_id);
                // $transaction = LogisticTransaction::where('id', '=', $batch->logistic_id)->first();

                // $list = LogisticBatchItem::where('batch_id', '=', $get['batch_id'])->get();

                $list = DB::table('logistic_batch_item AS a')
                            ->select('a.*', 'b.sku', 'b.name', 'b.label')
                            ->leftJoin('logistic_transaction_item AS b', 'a.transaction_item_id', '=', 'b.id')
                            ->where('a.batch_id', '=', $get['batch_id'])
                            ->get();

                $status = LogisticBatch::get_status($batch->status);

                if (isset($batch->do_no) AND $batch->do_no != NULL AND $batch->do_no != "")
                    $file = asset('/') . Config::get('constants.LOGISTIC_DO_PATH') . '/' . $batch->do_no . '.pdf';
                else
                    $file = "";
                    
                // if ($batch->delivery_img != "") {
                //     $delivery_img_url = asset('/').Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH').'/'.$batch->delivery_img;
                // }else{
                //     $delivery_img_url = "";
                // }
                
                $LogisticBatchImage = LogisticBatchImage::where("batch_id",$batch->id)->get();
                
                $batchImage = array();
                foreach ($LogisticBatchImage as $key => $value) {
                    $batchImage[] = asset('/').Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH').'/'.$value->filename;
                }

                $data['batch_id'] = $batch->id;
                $data['batch_date'] = $batch->batch_date;
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
                $data['do_no'] = $batch->do_no;
                $data['do_url'] = $file;
                $data['delivery_img_url'] = $batchImage;
                $data['accept_date'] = $batch->accept_date;
                $data['sign_name'] = $batch->sign_name;
                $data['sign_ic'] = $batch->sign_ic;
                $data['remark'] = $batch->remark;
                $data['status'] = $status;

                /* CHECK DO FOR DELIVERY SERVICE */
                $DeliveryOrder = DeliveryOrder::where("transaction_id",$transaction->transaction_id)->first();
                if($DeliveryOrder->id > 0){
                    $deliveryservice = true;
                }else{
                    $deliveryservice = false;
                }
                
                

                foreach ($list as $row)
                {
                    $details[] = array(
                        'item_id' => $row->id,
                        'sku' => $row->sku,
                        'name' => $row->name,
                        'label' => $row->label,
                        'qty_assign' => $row->qty_assign,
                        'qty_pickup' => $row->qty_pickup,
                        'qty_sent' => $row->qty_sent,
                        'remark' => $row->remark
                    );

                    if ($flagPickup == true && $row->qty_pickup > 0) {
                        $pickupDetails[] = [
                            'sku'  => $row->sku,
                            'name' => $row->name,
                            'qty'  => $row->qty_pickup
                        ];
                    }
                }

                if($deliveryservice){
                    $pickupDetails = array();
                    $DeliveryOrder = DeliveryOrder::where("transaction_id",$transaction->transaction_id)->first();
                    $DeliveryOrderItems = DeliveryOrderItems::where("service_order_id",$DeliveryOrder->id)->get();
                    
                    foreach ($DeliveryOrderItems as $keyD => $valueD) {
                        $pickupDetails[] = [
                            'sku'  => $valueD->item_sku,
                            'name' => $valueD->item_description,
                            'qty'  => $valueD->quantity
                        ];
                    }
                    
                   
                }

                $data['item'] = $details;

                if ($flagPickup == true && isset($pickupDetails) && ! empty($pickupDetails)) {
                    $buyer_email       = $transaction->buyer_email;
                    $delivery_name     = $transaction->delivery_name;
                    $pickupMailSubject = 'We\'re on our way to collect your goods!';
                    $pickupMailData    = [
                        'delivery_name'       => $transaction->delivery_name,
                        'delivery_contact_no' => $transaction->delivery_contact_no,
                        'delivery_addr_1'     => $transaction->delivery_addr_1,
                        'delivery_addr_2'     => $transaction->delivery_addr_2,
                        'delivery_city'       => $transaction->delivery_city,
                        'delivery_postcode'   => $transaction->delivery_postcode,
                        'delivery_state'      => $transaction->delivery_state,
                        'delivery_country'    => $transaction->delivery_country,
                        'special_msg'         => $transaction->special_msg,
                        'pickup_details'      => $pickupDetails,
                        '$deliveryservice'    => $deliveryservice    
                    ];

                    Mail::send('emails.progress', $pickupMailData, function ($message) use ($buyer_email, $delivery_name, $pickupMailSubject) {
                        $message->from('payment@tmgrocer.com', 'tmGrocer');
                        $message->to($buyer_email, $delivery_name)->subject($pickupMailSubject);
                    });
                }
            }
        }
        else
        {
            $data['status_msg']  = 'Invalid Batch ID!';
            $headerResponse = array(
                "isSuccess" => 'false',
                "message" => 'Invalid batch ID!'
            );
            $data = array_merge($headerResponse,$data);
        }

        return $data;
        
    }

    public static function check_file($file_name = "", $path = "")
    {
        if(!file_exists($path . "/" . $file_name))
            $hasFile = 0;
        else
            $hasFile = 1;

        return $hasFile;
    }


    public function scopeUpdateBatch($query, $id, $data)
    {
        
        $batch = LogisticBatch::find($id);
        
        
        if (isset($data['remark']) AND trim($data['remark']) != '')
        {
            $tempremark = date('Y-m-d H:i:s') . " [CMS]" . Session::get('username') . ": " . trim($data['remark']);

            if($batch->remark == '')
                $batch->remark = $tempremark;
            else
                $batch->remark = $batch->remark . "\n" . $tempremark;

        }

        if ($batch->status != $data['status'])
        {

            $LogisticT = LogisticTransaction::find($batch->logistic_id);
            $ori_status = $batch->status;
            $new_status = $data['status'];
            $trans_id = $LogisticT->transaction_id;
            $batch_id = $batch->id;
            $type = 'Batch';

            Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type, Session::get('username'));

        }

        if (isset($data['status']) AND $data['status'] != '')
        {
            if($data['status'] == '2' OR $data['status'] == '3' OR $data['status'] == '5')
            {                        
                $transaction = LogisticTransaction::find($batch->logistic_id);

                switch ($data['status']) {
                    //Returned
                    case '2':
                        $transaction->status = 3;
                        break;
                    // Undelivered
                    case '3':
                        $transaction->status = 1;
                        break;
                }

                if ($data['status']!=$transaction->status) {
                    $status = LogisticTransaction::find($batch->logistic_id);
                    $ori_status= $status->status;
                    $new_status = $transaction->status;
                    $trans_id=$transaction->transaction_id;
                    $logistic_id=$batch->logistic_id;
                    $type='batchTrans';

                    Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,Session::get('username'));

                }
                
                $transaction->modify_date = date('Y-m-d H:i:s');
                $transaction->save();

                $batch_item = LogisticBatchItem::where('batch_id', '=', $id)->get();

                foreach ($batch_item as $item)
                {
                    //qty_order qty_to_assign   qty_to_send qty_assign   qty_sent
                    //10        10              10          0            0
                    //10        4               10          6            0
                    //10        4               10          6            4
                    //new batch
                    //10        10              10          0            0
                    //10        6               10          4            0
                    $itemlist = LogisticTItem::find($item->transaction_item_id);
                    
                    $itemlist->qty_to_assign += $item->qty_assign;
                    $itemlist->save();

                    //inventory history update returned,undelivered,cancelled
                    $Trans_ID = LogisticTransaction::find($batch->logistic_id);
                    $productList = LogisticTItem::find($item->transaction_item_id);

                    switch ($data['status'])
                    {
                        case '2':
                            $new_status = 'Returned [CMS]';
                            break;
                        case '3':
                            $new_status = 'Undelivered [CMS]';
                            break;
                        case '5':
                            $new_status = 'Cancelled [CMS]';
                            break;
                        default:
                            break;
                }
                
                    // stock in the products to warehouse
                    // Warehouse::manageProductstock($itemlist->product_id, $itemlist->product_price_id, $item->qty_assign, 'increase');

                    //passing var
                    $username = Session::get('username');
                    $date = date('Y-m-d H:i:s');
                    $HistoryStatus = $new_status;
                    $TransID = $Trans_ID->transaction_id;
                    $qty_assignHistory = $item->qty_assign;
                    $qty_pickupHistory = $item->qty_pickup;
                    $product_name = $productList->name;

                    LogisticBatch::getInventoryHistory($username,$date,$HistoryStatus,$TransID,$qty_assignHistory,$qty_pickupHistory,$product_name);

                }

                $batch->modify_by    = Session::get('username');
                $batch->modify_date  = date('Y-m-d H:i:s');
                $batch->status = $data['status'];    
            }

            if($data['status'] == '4')
            {                
                $batch->save();

                $batch_item = LogisticBatchItem::where('batch_id', '=', $id)->get();

                foreach ($batch_item as $item)
                {
                    //qty_order qty_to_assign   qty_to_send qty_assign   qty_sent
                    //10        10              10          0            0
                    //10        4               10          6            0
                    //10        4               10          6            4
                    //new batch
                    //10        6               6           0            0
                    //10        4               6           2            0
                    //10        4               4           2            2
                    //new batch
                    //10        0               4           4            0
                    //10        0               0           0            0
                    $itemlist = LogisticTItem::find($item->transaction_item_id);
                    // $itemlist->qty_to_assign += $item->qty_assign - $item->qty_sent;
                    $itemlist->qty_to_send -= $item->qty_assign;
                    $itemlist->save();
                    
                    // UPDATE Qty Pickup / Sent
                    $LogisticBatchItem = LogisticBatchItem::find($item->id);
                    $LogisticBatchItem->qty_pickup = $item->qty_assign;
                    $LogisticBatchItem->qty_sent = $item->qty_assign;
                    $LogisticBatchItem->modify_date = date("Y-m-d h:i:s");
                    $LogisticBatchItem->modify_by = Session::get('username');
                    $LogisticBatchItem->save();

                    //inventory history cms update SENT
                    $Trans_ID = LogisticTransaction::find($batch->logistic_id);
                    $productList = LogisticTItem::find($item->transaction_item_id);

                    //passing var
                    $username = Session::get('username');
                    $date = date('Y-m-d H:i:s');
                    $HistoryStatus = 'Sent [CMS]';
                    $TransID = $Trans_ID->transaction_id;
                    $qty_assignHistory = $item->qty_assign;
                    $qty_pickupHistory = $item->qty_assign;
                    $product_name = $productList->name;

                    LogisticBatch::getInventoryHistory($username,$date,$HistoryStatus,$TransID,$qty_assignHistory,$qty_pickupHistory,$product_name);

                }

                $transaction = LogisticTransaction::find($batch->logistic_id);

                $ori_status = $transaction->status;

                $total_to_send = 0;

                $listall = LogisticTItem::select('qty_to_send')->where('logistic_id', '=', $batch->logistic_id)->get();

                foreach ($listall as $item2)
                {
                    $total_to_send += $item2->qty_to_send;
                }
                //if($total_to_send == 0)
                   // $transaction->status = 5;
              //  else
                   // $transaction->status = 2;

                if($total_to_send == 0){
                    $transaction->status = 5;
                    $transaction->modify_date = date('Y-m-d H:i:s');
                }else{
                    $transaction->status = 2;
                }
                $transaction->save();

                $batch->modify_by    = Session::get('username');
                $batch->modify_date  = date("Y-m-d h:i:sa");
                $batch->status = $data['status'];

                if ($data['status']!=$transaction->status) {
                    
                    $new_status = $transaction->status;
                    $trans_id=$transaction->transaction_id;
                    $logistic_id=$batch->logistic_id;
                    $type='batchTrans';

                    Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,Session::get('username'));

                }
            }
        }

        $batch->save();

        
    }  

    public function scopeUpdateBatchLineClear($query, $id, $data)
    {
        
        $batch = LogisticBatch::find($id);
        $username = '[API]LineClear';
        

        if ($batch->status != $data['status'])
        {

            $LogisticT = LogisticTransaction::find($batch->logistic_id);
            $ori_status = $batch->status;
            $new_status = $data['status'];
            $trans_id = $LogisticT->transaction_id;
            $batch_id = $batch->id;
            $type = 'Batch';

            Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type, $username);

        }

        if (isset($data['status']) AND $data['status'] != '')
        {

            if($data['status'] == '4')
            {                
                $batch->save();

                $batch_item = LogisticBatchItem::where('batch_id', '=', $id)->get();

                foreach ($batch_item as $item)
                {
                    //qty_order qty_to_assign   qty_to_send qty_assign   qty_sent
                    //10        10              10          0            0
                    //10        4               10          6            0
                    //10        4               10          6            4
                    //new batch
                    //10        6               6           0            0
                    //10        4               6           2            0
                    //10        4               4           2            2
                    //new batch
                    //10        0               4           4            0
                    //10        0               0           0            0
                    $itemlist = LogisticTItem::find($item->transaction_item_id);
                    // $itemlist->qty_to_assign += $item->qty_assign - $item->qty_sent;
                    $itemlist->qty_to_send -= $item->qty_assign;
                    $itemlist->save();
                    
                    // UPDATE Qty Pickup / Sent
                    $LogisticBatchItem = LogisticBatchItem::find($item->id);
                    $LogisticBatchItem->qty_pickup = $item->qty_assign;
                    $LogisticBatchItem->qty_sent = $item->qty_assign;
                    $LogisticBatchItem->modify_date = date("Y-m-d h:i:s");
                    $LogisticBatchItem->modify_by = $username;
                    $LogisticBatchItem->save();

                    //inventory history cms update SENT
                    $Trans_ID = LogisticTransaction::find($batch->logistic_id);
                    $productList = LogisticTItem::find($item->transaction_item_id);

                    //passing var
                    $date = date('Y-m-d H:i:s');
                    $HistoryStatus = 'Sent [CMS]';
                    $TransID = $Trans_ID->transaction_id;
                    $qty_assignHistory = $item->qty_assign;
                    $qty_pickupHistory = $item->qty_assign;
                    $product_name = $productList->name;

                    LogisticBatch::getInventoryHistory($username,$date,$HistoryStatus,$TransID,$qty_assignHistory,$qty_pickupHistory,$product_name);

                }

                $transaction = LogisticTransaction::find($batch->logistic_id);

                $ori_status = $transaction->status;

                $total_to_send = 0;

                $listall = LogisticTItem::select('qty_to_send')->where('logistic_id', '=', $batch->logistic_id)->get();

                foreach ($listall as $item2)
                {
                    $total_to_send += $item2->qty_to_send;
                }
                //if($total_to_send == 0)
                   // $transaction->status = 5;
              //  else
                   // $transaction->status = 2;

                if($total_to_send == 0){
                    $transaction->status = 5;
                    $transaction->modify_date = date('Y-m-d H:i:s');
                }else{
                    $transaction->status = 2;
                }
                $transaction->save();

                $batch->modify_by    = $username;
                $batch->modify_date  = date("Y-m-d h:i:sa");
                $batch->status = $data['status'];

                if ($data['status']!=$transaction->status) {
                    
                    $new_status = $transaction->status;
                    $trans_id=$transaction->transaction_id;
                    $logistic_id=$batch->logistic_id;
                    $type='batchTrans';

                    Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,$username);

                }
            }
        }

        $batch->save();

        
    }  
    
    public static function getTotalRecordByStatus($status ,$startDate,$toDate){
        
        $result = LogisticBatch::where("status","=",$status)
                ->where("assign_date",">=",$startDate)
                ->where("assign_date","<=",$toDate)
                ->count();
        
        return $result;
    }  
    
    public static function getTotalRecordByStatusRegion($status ,$startDate,$toDate){		
        		
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
        $result = DB::table('logistic_batch AS LB')		
                ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')		
                ->where("LB.status","=",$status)		
                ->where("LB.assign_date",">=",$startDate)		
                ->where("LB.assign_date","<=",$toDate)		
                ->whereIn("LT.delivery_state", $states)		
                ->count();		
        		
        return $result;		
    }
    
    public static function getTotalRecordBatchCompleted($status ,$startDate,$toDate){
        
        $result = LogisticBatch::where("status","=",$status)
                ->where("modify_date",">=",$startDate)
                ->where("modify_date","<=",$toDate)
                ->count();
        
        return $result;
    }

    public static function getTotalRecordDriverSent($status = 4 ,$startDate,$toDate){
                        
        $result = DB::table('logistic_batch') //assign_date
                ->select('modify_by', DB::raw('count(id) as total'))
                ->where("modify_date",">=",$startDate)
                ->where("modify_date","<=",$toDate)
//                ->where("assign_date",">=",$startDate)
//                ->where("assign_date","<=",$toDate)
                ->where("status","=",$status)
                ->groupBy('modify_by')
                ->get();
        
        return $result;
    }
    
    public static function getTotalRecordDriverSentRegion($status = 4 ,$startDate,$toDate){		
        		
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
        $result = DB::table('logistic_batch AS LB')		
                ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id') //assign_date		
                ->select('LB.modify_by', DB::raw('count(LB.id) as total'))		
                ->where("LB.modify_date",">=",$startDate)		
                ->where("LB.modify_date","<=",$toDate)		
//                ->where("assign_date",">=",$startDate)		
//                ->where("assign_date","<=",$toDate)		
                ->where("LB.status","=",$status)		
                ->whereIn("LT.delivery_state", $states)		
                ->groupBy('LB.modify_by')		
                ->get();		
        		
        return $result;		
    }
    
    public static function getTotalStatusByDriver($status,$driver_id,$startDate,$toDate){
    
        $result = DB::table('logistic_batch')
                ->where("modify_date",">=",$startDate)
                ->where("modify_date","<=",$toDate)
                ->where("driver_id","=",$driver_id)
                ->where("status","=",$status)
                ->count();
    
        return $result;
    }
    
    public static function getTotalStatusByDriverRegion($status,$driver_id,$startDate,$toDate){		
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
        $result = DB::table('logistic_batch AS LB')		
                ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')		
                ->where("LB.modify_date",">=",$startDate)		
                ->where("LB.modify_date","<=",$toDate)		
                ->where("LB.driver_id","=",$driver_id)		
                ->where("LB.status","=",$status)		
                ->whereIn("LT.delivery_state", $states)		
                ->count();		
        		
        return $result;
    }
    
    public static function getTotalDriverAssigned($driver_id,$startDate,$toDate){
        
        $result = DB::table('logistic_batch')
                ->where("assign_date",">=",$startDate)
                ->where("assign_date","<=",$toDate)
                ->where("driver_id","=",$driver_id)
                ->count();
        
        return $result;
}

    public static function getTotalDriverAssignedRegion($driver_id,$startDate,$toDate){
        $username = Session::get('username'); 		
        $states = LogisticTransaction::getStates($username);		
        $result = DB::table('logistic_batch AS LB')		
                ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')		
                ->where("LB.assign_date",">=",$startDate)		
                ->where("LB.assign_date","<=",$toDate)		
                ->where("LB.driver_id","=",$driver_id)		
                ->whereIn("LT.delivery_state", $states)		
                ->count();		
        		
        return $result;		
    }
  
   public static function getBatchValid($batchid){
        $status = 0;
        $result = DB::table('jocom_courier_orders')
                  ->where('batch_id','=',$batchid)
                  ->orderby('created_at','DESC')
                  ->first();
        if(count($result)>0){
            $status = $result->courier_id;
        }

        return $status;          
    }
                        
    public static function getInventoryHistory($username,$date,$HistoryStatus,$TransID,$qty_assignHistory,$qty_pickupHistory,$product_name){
        
        if($username != ''){
            $created_by = $username;
        }else{
            $created_by = 'SYSTEM';
        }
        
        DB::table('inventory_history')->insert(
                                    array(
                                        'created_by' => $created_by, 
                                        'created_at' => $date, 
                                        'status' => $HistoryStatus, 
                                        'transaction_id' => $TransID, 
                                        'qty_assign' => $qty_assignHistory, 
                                        'qty_pickup' => $qty_pickupHistory,
                                        'name' => $product_name
                                        )
                                    );
    
    }
    
    public static function getTotalHoursWorked($driver_id,$startDate,$toDate){
        
        $result = DB::table('logistic_batch')
                ->where('driver_id',$driver_id)
                ->where('accept_date','>=',$startDate)
                ->where('accept_date','<=',$toDate)
                ->where('status','4')
                ->orderby('accept_date', 'Desc')
                ->first();

        return $result;
    }
    
    public static function getTotalSentByDriver($status,$driver_id,$startDate,$toDate){

        $result = DB::table('logistic_batch')
                ->where('accept_date','>=',$startDate)
                ->where('accept_date','<=',$toDate)
                ->where('driver_id','=',$driver_id)
                ->where('status','=',$status)
                ->count();
        
        return $result;
    }
    
    public static function getTotalAssignedDriver($status,$driver_id,$startDate,$toDate){
        
        $result = DB::table('logistic_batch')
                ->where('assign_date','>=',$startDate)
                ->where('assign_date','<=',$toDate)
                ->where('driver_id','=',$driver_id)
                ->where('status','=',$status)
                ->count();
        
        return $result;
    }
    public static function getDriverStatusDetails($status, $driver_id,$startDate,$toDate){
        
        switch ($status) {
                case '0':
                    $result = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                    ->where('LB.assign_date','>=',$startDate)
                    ->where('LB.assign_date','<=',$toDate)
                    ->where('LB.driver_id','=',$driver_id)
                    ->where('LB.status',$status)
                    ->select('LT.transaction_id','LB.id','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                    ->get();
                    break;
                case '1':
                    $result = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                    ->where('LB.modify_date','>=',$startDate)
                    ->where('LB.modify_date','<=',$toDate)
                    ->where('LB.driver_id','=',$driver_id)
                    ->where('LB.status',$status)
                    ->select('LT.transaction_id','LB.id AS batch_id','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                    ->get();
                    break;
                case '2':
                    $result = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                    ->where('LB.modify_date','>=',$startDate)
                    ->where('LB.modify_date','<=',$toDate)
                    ->where('LB.driver_id','=',$driver_id)
                    ->where('LB.status',$status)
                    ->select('LT.transaction_id','LB.id AS batch_id','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                    ->get();
                    break;
                case '3':
                    $result = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                    ->where('LB.modify_date','>=',$startDate)
                    ->where('LB.modify_date','<=',$toDate)
                    ->where('LB.driver_id','=',$driver_id)
                    ->where('LB.status',$status)
                    ->select('LT.transaction_id','LB.id AS batch_id','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                    ->get();
                    break;
                case '4':
                    $result = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                    ->where('LB.accept_date','>=',$startDate)
                    ->where('LB.accept_date','<=',$toDate)
                    ->where('LB.driver_id','=',$driver_id)
                    ->where('LB.status',$status)
                    ->select('LT.transaction_id','LB.id AS batch_id','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                    ->get();
                    break;
                case '5':
                    $result = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                    ->where('LB.modify_date','>=',$startDate)
                    ->where('LB.modify_date','<=',$toDate)
                    ->where('LB.driver_id','=',$driver_id)
                    ->where('LB.status',$status)
                    ->select('LT.transaction_id','LB.id AS batch_id','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                    ->get();
                    break;
            }
        
        return $result;
    }
    
    public static function getDriverSent($driver_id,$startDate,$toDate){
        
        $result = DB::table('logistic_batch AS LB')
                ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                ->where('LB.accept_date','>=',$startDate)
                ->where('LB.accept_date','<=',$toDate)
                ->where('LB.driver_id','=',$driver_id)
                ->where('LB.status',4)
                ->select('LT.do_no','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                ->get();
        
        return $result;
    }
    public static function getDriverReturned($driver_id,$startDate,$toDate){
        
        $result = DB::table('logistic_batch AS LB')
                ->leftJoin('logistic_transaction AS LT','LT.id','=','LB.logistic_id')
                ->where('LB.modify_date','>=',$startDate)
                ->where('LB.modify_date','<=',$toDate)
                ->where('LB.driver_id','=',$driver_id)
                ->where('LB.status',2)
                ->select('LT.do_no','LB.status','LT.delivery_name','LT.delivery_addr_1','LT.delivery_addr_2','LT.delivery_city','LT.delivery_contact_no')
                ->get();
        
        return $result;
    }

    public static function getTotalBatchStatistic($status,$startDate,$toDate){
        
        $result = LogisticBatch::where('modify_date', '>=', $startDate)
                    ->where('modify_date', '<=', $toDate)->where('status', '=', $status)->count();
        
        return $result;
    }

    public static function getTotalBatchStatisticAssigned($status,$startDate,$toDate){
        
        $result = LogisticBatch::where('assign_date', '>=', $startDate)
                    ->where('assign_date', '<=', $toDate)->where('status', '=', $status)->count();
        
        return $result;
    }
    
    
    
    
}

                        