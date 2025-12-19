<?php 
require_once app_path('library/barcodemaster/src/Milon/Barcode/DNS1D.php');
use \Milon\Barcode\DNS1D;
use Helper\ImageHelper as Image;
class LogisticController extends BaseController {
  
    
    public function anyIndex()
    {
        // echo "Under development...";
        //$logistic   = LogisticTransaction::all();
        return View::make('logistic.logistic_listing', ['logistic'=> $logistic]);
    }

    public function anyListing()
    {
        // echo "test";
        // $logistic = LogisticTransaction::select(array('id', 'transaction_id', 'transaction_date', 'delivery_name', 'delivery_city', 'special_msg', 'do_no', 'status'));

        try{
            
        
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }
        $logistic = LogisticTransaction::select(
                    array('logistic_transaction.id', 'logistic_transaction.transaction_id', 'logistic_transaction.transaction_date', 
                        'logistic_transaction.do_no', 
                        'logistic_transaction.special_msg', 
                        'logistic_transaction.delivery_name', 
                        'logistic_transaction.delivery_state',
                        'logistic_transaction.delivery_addr_1',
                        'logistic_transaction.delivery_addr_2',
                        'logistic_transaction.delivery_postcode', 
                        'logistic_transaction.delivery_city', 'logistic_transaction.status'))
                    ->join('logistic_transaction_item', 'logistic_transaction_item.logistic_id', '=', 'logistic_transaction.id')->groupby('logistic_transaction.id');
        
        $from_date   = Input::get('from_date')." 00:00:00";
        $to_date     = Input::get('to_date')." 23:59:59";
        $sku         = Input::get('sku');
        $status      = Input::get('status');

        if (Input::get('from_date') != NULL) {
            $logistic= $logistic->where('logistic_transaction.insert_date', '>=', $from_date);
        }

        if (Input::get('to_date') != NULL) {
            $logistic= $logistic->where('logistic_transaction.insert_date', '<=', $to_date);
        }
    
        if(count($stateName) > 0){
            $logistic = $logistic->whereIn('logistic_transaction.delivery_state', $stateName);
        }
    
        if (Input::get('sku') != NULL) {
            
            $skus = implode('|', array_map('trim', explode(',', $sku)));
            //$skus = explode(",", $sku);
            //print_r($skus);
            $logistic       = $logistic->join('logistic_transaction_item AS LTI ', 'logistic_transaction.id', '=', 'LTI.logistic_id');
            $logistic       = $logistic->where('LTI.sku', 'REGEXP', $skus)->select([
                            'logistic_transaction.id', 
                            'logistic_transaction.transaction_id', 
                            'logistic_transaction.transaction_date', 
                            'logistic_transaction.do_no', 
                            'logistic_transaction.special_msg', 
                            'logistic_transaction.delivery_name', 
                            'logistic_transaction.delivery_city', 
                            'logistic_transaction.status',
                            'LTI.sku'
                            ]);
        }

        if (Input::get('status') != "") {
            $logistic= $logistic->whereIn('logistic_transaction.status', $status);
        }

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="jlogistic/edit/{{$id}}"><i class="fa fa-pencil"></i></a>';
        /* DISABLED BY WIRA */
        //$actionBar = $actionBar . ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_logistic({{$id;}});"><i class="fa fa-remove"></i></a>';
        /* DISABLED BY WIRA */
        

        return Datatables::of($logistic)
                ->edit_column('status', '
                    @if($status == 0)
                        <p class="text-danger">Pending</p>
                    @elseif ($status == 1)  
                        <p class="text-danger">Undelivered</p>
                    @elseif ($status == 2)
                        <p class="text-danger">Partial Send</p>
                    @elseif ($status == 3)
                        <p class="text-danger">Returned</p>
                    @elseif ($status == 4)
                        <p class="text-danger">Sending</p>
                    @elseif ($status == 5)
                        <p class="text-success">Sent</p>
                    @else
                        <p class="text-danger">Cancelled</p>
                    @endif
                    ')
                ->add_column('Action', $actionBar)
                ->add_column('name', function($row){
  
                    $name = DB::table('logistic_transaction_item')->where('logistic_id', '=', $row->id)->lists('name');
                    
                    return $name;
                   
                })
                ->add_column('address', function($row){
  
                    $address= $row->delivery_addr_1." ".$row->delivery_addr_2." ".$row->delivery_postcode." ".$row->delivery_city." ".$row->delivery_state;
                    
                    return $address;
                   
                })
                ->make(true);
       
                } catch (Exception $ex) {
                    echo $ex->getMessage();
    }

    }
   
    public function anyExport(){

        $courier = DB::table('jocom_courier')->select("*")->get();
        $statusList = array(0 => 'Pending', 1 => 'Undelivered', 2 => 'Partial Sent', 3 => 'Returned', 4 => 'Sending', 5 => 'Sent', 6 => 'Cancelled');

        $sysAdminInfo = User::where("username",Session::get('username'))->first();

        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();

        if ($SysAdminRegion->region_id==0) {

            $states = DB::table('jocom_country_states')->where('country_id','=', 458)->select('id','name')->get();

        }else{
            
            $states = DB::table('jocom_country_states')->where('country_id','=', 458)->where('region_id','=',$SysAdminRegion->region_id)->select('id','name')->get();
        }

        return View::make('logistic.export_transaction')
                    ->with('courier',$courier)
                    ->with('statusList',$statusList)
                    ->with('states',$states);
    }

    public function anyExportdetails(){        

        $transID = $_GET['transID'];
        $from_date = $_GET['from_date'];
        $to_date = $_GET['to_date'];
        $product_sku = $_GET['product_sku'];
        $courier = $_GET['courier'];
        $status = $_GET['status'];
        $invoice_no = $_GET['invoice_no'];
        $do_no = $_GET['do_no'];
        $state = $_GET['state'];
    
        $id = implode('|', array_map('trim', explode(',', $transID)));
        $sku = implode('|', array_map('trim', explode(',', $product_sku)));
        $do = implode('|', array_map('trim', explode(',', $do_no)));
        $invoice = implode('|', array_map('trim', explode(',', $invoice_no)));
        
        $state = implode(',', array_map('trim', explode(',', $state)));
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();

        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                        ->where("status",1)->first();

        $states = DB::table('jocom_country_states')->where('region_id','=',$SysAdminRegion->region_id)->lists('name');
        
        if ($SysAdminRegion->region_id == 0) {

            $result = DB::table('logistic_transaction AS LT')
                ->select('LT.id', 'LT.transaction_id', 'LT.transaction_date', 'LT.delivery_name', 'LT.delivery_contact_no', 'LT.buyer_email', 'LT.delivery_addr_1', 'LT.delivery_addr_2', 'LT.delivery_city', 'LT.delivery_postcode', 'LT.delivery_country', 'LT.special_msg', 'LT.do_no', 'LT.insert_date', 'LTI.sku', 'LTI.name', 'LTI.label' , 'LTI.qty_order',
                    DB::raw("(CASE WHEN LT.status='0' THEN 'Pending' WHEN LT.status='1' THEN 'Undelivered' WHEN LT.status='2' THEN 'Partial Send' WHEN LT.status='3' THEN 'Returned' WHEN LT.status='4' THEN 'Sending' WHEN LT.status='5' THEN 'Sent' WHEN LT.status='6' THEN 'Cancelled' ELSE 'LT.status' END ) as status"),'LT.modify_date as Last_Update_date')
                ->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id');
        }else{

            $result = DB::table('logistic_transaction AS LT')
                ->select('LT.id', 'LT.transaction_id', 'LT.transaction_date', 'LT.delivery_name', 'LT.delivery_contact_no', 'LT.buyer_email', 'LT.delivery_addr_1', 'LT.delivery_addr_2', 'LT.delivery_city', 'LT.delivery_postcode', 'LT.delivery_country', 'LT.special_msg', 'LT.do_no', 'LT.insert_date', 'LTI.sku', 'LTI.name', 'LTI.label', 'LTI.qty_order',
                    DB::raw("(CASE WHEN LT.status='0' THEN 'Pending' WHEN LT.status='1' THEN 'Undelivered' WHEN LT.status='2' THEN 'Partial Send' WHEN LT.status='3' THEN 'Returned' WHEN LT.status='4' THEN 'Sending' WHEN LT.status='5' THEN 'Sent' WHEN LT.status='6' THEN 'Cancelled' ELSE 'LT.status' END ) as status"),'LT.modify_date as Last_Update_date')
                ->leftJoin('logistic_transaction_item AS LTI', 'LTI.logistic_id', '=', 'LT.id')->whereIn('LT.delivery_state',$states);
        }
        
        

        if ($id!='') {

            $result = $result->where('LT.transaction_id', 'REGEXP', $id);
        }

        if (isset($from_date) &&  ! empty($from_date)) {
            $result = $result->where('LT.transaction_date', '>=', $from_date);
        }

        if (isset($to_date) &&  ! empty($to_date)) {
            $result = $result->where('LT.transaction_date', '<=', $to_date);
        }

        if ($sku!='') {

            $result = $result->select('LT.id', 'LT.transaction_id', 'LT.transaction_date', 'LT.delivery_name','LTI.name', 'LT.delivery_contact_no', 'LT.buyer_email', 'LT.delivery_addr_1', 'LT.delivery_addr_2', 'LT.delivery_city', 'LT.delivery_postcode', 'LT.delivery_country', 'LT.special_msg', 'LT.do_no', 'LT.insert_date', 'LTI.sku', 'LTI.name', 'LTI.label', 
                'LTI.qty_order',
                    DB::raw("(CASE WHEN LT.status='0' THEN 'Pending' WHEN LT.status='1' THEN 'Undelivered' WHEN LT.status='2' THEN 'Partial Send' WHEN LT.status='3' THEN 'Returned' WHEN LT.status='4' THEN 'Sending' WHEN LT.status='5' THEN 'Sent' WHEN LT.status='6' THEN 'Cancelled' ELSE 'LT.status' END ) as status",'LT.modify_date as Last_Update_date')
                    )
                    ->where('LTI.sku','REGEXP',$sku);
        }

        if ($courier!='') {

            if ($courier=='0') {

                $result = $result->leftJoin('logistic_batch AS LB', 'LB.logistic_id', '=', 'LT.id')
                                ->where('LB.driver_id','!=',0);

            }else{

                $result = $result->select('LT.id', 'LT.transaction_id', 'LT.transaction_date', 'LT.delivery_name', 'JC.courier_name','LT.delivery_contact_no', 'LT.buyer_email', 'LT.delivery_addr_1', 'LT.delivery_addr_2', 'LT.delivery_city', 'LT.delivery_postcode', 'LT.delivery_country', 'LT.special_msg', 'LT.do_no','LB.tracking_number', 'LT.insert_date', 'LTI.sku', 'LTI.name', 'LTI.label', 'LTI.qty_order', 
                    DB::raw("(CASE WHEN LT.status='0' THEN 'Pending' WHEN LT.status='1' THEN 'Undelivered' WHEN LT.status='2' THEN 'Partial Send' WHEN LT.status='3' THEN 'Returned' WHEN LT.status='4' THEN 'Sending' WHEN LT.status='5' THEN 'Sent' WHEN LT.status='6' THEN 'Cancelled' ELSE 'LT.status' END ) as status"),'LT.modify_date as Last_Update_date')
                ->leftJoin('logistic_batch AS LB', 'LB.logistic_id', '=', 'LT.id')
                ->leftJoin('jocom_courier AS JC', 'JC.id','=','LB.shipping_method')
                ->where('LB.shipping_method','=',$courier);
            }
            
        }
        
       
        $status = explode(',', $status);  
        if ($status!='') {

        //   $result = $result->where('LT.status', '=', $status);
             $result = $result->whereIn('LT.status', $status);
        }

        if ($invoice!='') {

            $result = $result->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'LT.transaction_id')->where('JT.invoice_no','REGEXP',$invoice);
        }

        if ($do!='') {

            $result = $result->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'LT.transaction_id')->where('JT.do_no','REGEXP',$do);
        }
        
        $state = explode(',', $state);    
        if ($state!='') {

            // $state_name = DB::table('jocom_country_states')->where('id','=',$state)->first();
            $state_name = DB::table('jocom_country_states')->whereIn('id',$state)->lists('name');
            // $result = $result->whereIn('LT.delivery_state', '=', $state_name->name);
            $result = $result->whereIn('LT.delivery_state',$state_name);
        }
     
        $result = $result->orderBy('LT.transaction_id', 'ASC')->get();
        
        
       

        $data = json_decode(json_encode($result), true);

            switch ($status)
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
            }
           
           
          $dataStrip = self::stripTagsInArrayElements($data);
          
        //   echo "<pre>";
        // // print_r($result);
        // // echo "</pre>";
        // die();

            $date = date('Y-m-d H:i:s');

            $path = Config::get('constants.CSV_FILE_PATH');

            Excel::create($date, function($excel) use($dataStrip) {

                $excel->sheet('Sheet 1', function($sheet) use($dataStrip) {

                    $sheet->fromArray($dataStrip);

                });

            })->download('xls');
            
     
        
    }
    
     public static function stripTagsInArrayElements(array $input, $easy = false, $throwByFoundObject = true)
    {
        if ($easy) {
            $output = array_map(function($v){
                return trim(strip_tags($v));
            }, $input);
        } else {
            $output = $input;
            foreach ($output as $key => $value) {
                if (is_string($value)) {
                    $output[$key] = trim(strip_tags($value));
                } elseif (is_array($value)) {
                    $output[$key] = self::stripTagsInArrayElements($value);
                } elseif (is_object($value) && $throwByFoundObject) {
                    throw new Exception('Object found in Array by key ' . $key);
                }
            }
        }
        return $output;
    }

    // manual log to logistic module
    public function anyLog($id = null)
    {
        try{
            
            $data = array();

        $data = LogisticTransaction::log_transaction($id);

        return Redirect::to('transaction/edit/'.$id)->with($data['type'], $data['message']);     
            
        }catch(exception $ex){
            echo $ex->getMessage();
        }
           
    }

    // for cron jobs to log to logistic module
    public function anyDailylog()
    {
        $today = date('Y-m-d');
        die();
        $yesterday = date_create($today);
        date_sub($yesterday,date_interval_create_from_date_string("1 days"));
        $yesterday = date_format($yesterday,"Y-m-d");
        
        // $transaction = Transaction::where('transaction_date', 'LIKE', '%'.$today.'%')->orWhere('transaction_date', 'LIKE', '%'.$yesterday.'%')->get();

        $transaction = DB::table('jocom_transaction')
                    ->select('id')
                    ->orderBy('transaction_date', 'ASC')
                    ->where('status', '=', 'completed')                                
                    ->where(function($query) use ($today, $yesterday)
                    {
                        $query->where('transaction_date', 'LIKE', '%'.$today.'%')
                              ->orWhere('transaction_date', 'LIKE', '%'.$yesterday.'%');
                    })
                    ->get();

        if(count($transaction)>0)
        {
            foreach ($transaction as $row)
            {
                $data = LogisticTransaction::log_transaction($row->id);
            }
        }        
    }


   /**
     * Edit logistic transaction details
     * @param  [type] $id [Logistic ID]
     * @return [type]     [description]
     */
     public function anyEdit($id = null)
    {
        if (isset($id))
        {
            $editLogistic = LogisticTransaction::find($id);
            
          
             
                $batchid = $editLogistic->id;

             if (Input::file('attachment')) {
               
                 
                 $files = Input::file('attachment');
                      
            foreach($files as $indx => $value) {
                $image = $files[$indx];
                if (!empty($image)) {
                    $attach =  date("Ymdhis")."-".uniqid();
                    $images = $attach . ".".$image->getClientOriginalName(); 
                    
                    $user   = Session::get('username');
                     $date  = date('Y-m-d H:i:s');
                    
                    $mim=$image->getMimeType();
                   
                      $path=public_path('logistic/images');  
                    $image->move($path, $images);
                    DB::table('logistic_batch_attachment')->insertGetId(array( 

                              'batch_id'    =>   $editLogistic->id,
                                'attachment'     =>   $images,
                                 'path_to_file'     =>  $path,
                                 'mime'     =>  $mim,
                                  'created_by'     =>  $user,
                                   'created_at'     =>  $date,
                              ));
                }                   
            } 
        
             }
            
            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION 
            $Transaction = LogisticTransaction::find($id);

            $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
            $stateName = array();
            foreach ($SysAdminRegion as  $value) {
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }
            if(!in_array($Transaction->delivery_state, $stateName)){
                $access = false;
                $SysAdmin = User::where("username",Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::getSysAdminRegion($SysAdmin->id);
                foreach ($SysAdminRegion as $key => $value) {
                    if($value->region_id == 0 ){
                        $access = true;
                    }
                }
                if(!$access){
                    return Redirect::to('jlogistic')->with('message', "You are don't have access right for that Transaction ID")->withErrors($validator)->withInput();
                }
            }
            // VALIDATE ACCESS TRANSACTION BASE ON REGION REGION 
            
            
            if (Input::has('id'))
            {
                $rs = LogisticTransaction::save_logistic();

                if ($rs == true)
                {
                    $insert_audit = General::audit_trail('LogisticController.php', 'Edit()', 'Edit Logistic', Session::get('username'), 'CMS');
                    return Redirect::to('jlogistic/edit/'.$id)->with('success', 'Logistic(ID: '.$id.') updated successfully.');
                }
                else{
                    return Redirect::to('jlogistic')->with('message', 'Logistic(ID: '.$id.') update failed.');
                }
            }
            else
            {
                $editLogistic = LogisticTransaction::find($id);
                
                $InternationalCourier = DB::table('jocom_courier AS JC')
                    ->select('JC.id', 'JC.courier_code', 'JC.courier_name')
                    ->where('JC.status', '=', 1)
                    ->where('JC.is_international', '=', 1)
                    ->get();
                
                // SYS ADMIN 
                $sysAdminInfo = User::where("username",Session::get('username'))->first();
                $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->first();
                if($SysAdminRegion->region_id == 0){
                    $addDriver = LogisticDriver::select('id','username')
                                                ->where('status','=',1)
                                                ->get();

                }else{
                $addDriver = LogisticDriver::select('id','username')
                         ->where("region_id",$SysAdminRegion->region_id)
                         ->where('status','=',1)
                         ->get();
                }
                
                
             
                // $editLogisticBatch  = LogisticBatch::where('logistic_id','=', $id)->get();
                // $editLogisticBatchDetails = LBatchDetails::where('logistic_id','=', $id)->get();
                $list = LogisticTItem::where('logistic_id', '=', $id)->get();
                
                foreach ($list as $row)
                {
                    $delivery = LogisticTItem::get_delivery($row->delivery_time);
                    $detail[] = array(
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
                $data['item'] = $detail;
                
                $batchlist = DB::table('logistic_batch AS a')
                    ->select('a.id', 'a.batch_date', 'a.do_no','a.shipping_method','a.tracking_number', 'a.status', 'a.remark','b.transaction_id', 'b.special_msg','c.username','JC.courier_name','JCO.tracking_no')
                    ->leftJoin('logistic_transaction AS b', 'a.logistic_id', '=', 'b.id')
                    ->leftJoin('logistic_driver AS c', 'a.driver_id', '=', 'c.id')
                    ->leftJoin('jocom_courier AS JC', 'a.shipping_method', '=', 'JC.id')  
                    ->leftJoin('jocom_courier_orders AS JCO', 'JCO.batch_id', '=', 'a.id') 
                    ->where('a.logistic_id', '=', $id)
                    ->get();
                    
                $imageBatch = array();

                foreach ($batchlist as $row)
                {
                    $details[] = array(
                        'id'             => $row->id,
                        'batch_date'     => $row->batch_date,
                        'transaction_id' => $row->transaction_id,
                        'shipping_method' => $row->shipping_method,
                        'shipping_courier' => $row->courier_name,
                        'tracking_number' => $row->tracking_number != "" ? $row->tracking_number : $row->tracking_no,
                        'remark'         => nl2br($row->remark),
                        'qty_assign'     => $row->qty_assign,
                        'do_no'          => $row->do_no,
                        'status'         => LogisticBatch::get_status($row->status),
                        'username'       => $row->username,
                        'delivery_img'   => $row->delivery_img
                    );
                    
                    $LogisticBatchImage = LogisticBatchImage::where("batch_id",$row->id)
                            ->where("status",1)->get();
                    
                    if(count($LogisticBatchImage) > 0 ){
                        foreach ($LogisticBatchImage as $kBI => $vBI) {
                            $imageBatch[] = array(
                                "filename" => Image::link(Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH')."/".$vBI->filename),
                                "batch_id" => $vBI->batch_id,
                                "created_at" => $vBI->created_at
                            );
                        }
                    }

                }
                $data2['batch'] = $details;
                
                $shipper = Courier::where("status",1)->get();

                $batchChecking = DB::table('logistic_batch')->select('*')->where('logistic_id','=',$id)->get();
                if (empty($batchChecking)) {
                    $statusList = array(0 => 'Pending', 1=> 'Undelivered', 2 => 'Partial Send', 3 => 'Returned', 5 => 'Sent', 6 =>'Cancelled');
                }else{
                    $statusList = array(0 => 'Pending', 1=> 'Undelivered', 2 => 'Partial Send', 3 => 'Returned', 4 => 'Sending', 5 => 'Sent', 6 =>'Cancelled');
                }
                
                // Define Logistic Section //
                
                $jocom_country_states = DB::table('jocom_country_states AS JCS')
                    ->select('JCS.country_id')
                    ->where('JCS.id', '=', $editLogistic->delivery_state_id)
                    ->first();
            
                
                if($editLogistic->delivery_country == 'China'){
                   $isInternationalLogistic = true;
                }else{
                    $isInternationalLogistic = false;
                }
                
                  $log = DB::table('logistic_batch_attachment')
                                ->select('*')
                                 ->where('logistic_batch_attachment.batch_id', '=', $id)
                                 ->get();
                
                  $returnedBatchDetails = DB::table('logistic_batchreturn_reasons_logs AS lbrl')
                                            ->select('lbrl.batch_id','lbrl.transaction_id','ld.name','lbr.reason','lbrl.return_others','lbrl.created_at')
                                            ->leftJoin('logistic_batchreturn_reasons AS lbr', 'lbr.id', '=', 'lbrl.return_status')
                                            ->leftJoin('logistic_driver AS ld', 'lbrl.driver_id', '=', 'ld.id')
                                            ->where('lbrl.transaction_id', '=', $editLogistic->transaction_id)
                                            ->get(); 
                                 
                // Define Logistic Section //

                return View::make('logistic.logistic_edit')
                                            ->with('logistic', $editLogistic)
                                            ->with('display_logistic_item', $data)
                                            ->with('display_driver', $addDriver)
                                            ->with('display_batch', $data2)
                                            ->with('shipper', $shipper)
                                            ->with('logistic_image', $imageBatch)
                                            ->with('statusList', $statusList)
                                            ->with('isInternationalLogistic', $isInternationalLogistic)
                                            ->with('InternationalCourier',$InternationalCourier)
                                            ->with('attachment_batch',$log)
                                            ->with('returned_batch',$returnedBatchDetails)
                                            ; 
            }       
        }
        else
        {
            return Redirect::to('jlogistic')->with('message', 'No transaction is selected for edit.');
        }
        
    }

  

    /*
     * @Desc    : View page
     */
    public function anyLocation(){
        
        $MalaysiaCountryID = 458;
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        $stateID = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
                $stateID[] = $valueS->id;
            }
        }
        
        $States = State::where('status', '=', 1)
                ->where('country_id', '=', $MalaysiaCountryID)
                ->whereIn('id', $stateID)->get(); // Malaysia Country ID
        
        return View::make('logistic.logistic_location_listing')
                ->with("States",$States);
        
    }
    /*
     * @Desc    : View page
     */
    public function anyLocationlist(){
        
        $MalaysiaCountryID = 458;
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        $stateID = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
                $stateID[] = $valueS->id;
            }
        }
        
        if(count($stateID) > 0){
            $States = State::where('status', '=', 1)
                ->where('country_id', '=', $MalaysiaCountryID)
                ->whereIn('id', $stateID)->get(); // Malaysia Country ID
        }else{
             $States = State::where('status', '=', 1)->where('country_id', '=', $MalaysiaCountryID)->get();
        }
        
        return View::make('logistic.log_location_listing')
                ->with("States",$States);
        
    }

    public function anyRouteplanner(){
        $transactions = Input::get('groubtrans');

        // $transactions = Input::get('transactionids');     
         return View::make('logistic.routeplanner')
                        ->with('gtransactions',$transactions);

    }

    public function anyManageroute(){
        $addr = "";
        $address = array();
        $translist = explode(",",Input::get("translist"));

        foreach ($translist as $value) {

            $TransactionInfo = LogisticTransaction::get_transaction($value);

            $ltid=$TransactionInfo->transaction_id;
            $street = preg_replace('/\&\s+/', ' ',$TransactionInfo->delivery_addr_1);
            $route = preg_replace('/\&\s+/', ' ',$TransactionInfo->delivery_addr_2);
            $city = preg_replace('/\s+/', ' ',$TransactionInfo->delivery_city);
            $state = preg_replace('/\s+/', ' ',$TransactionInfo->delivery_state);
            $country = preg_replace('/\s+/', ' ',$TransactionInfo->delivery_country);

            if($street!='')
            {
                $addr = $street;
            }
            if($route!='')
            {
                $addr = $addr.', '.$route;
            }
            if($city!='')
            {
                $addr = $addr.', '.$city;
            }
            if($state!='')
            {
                $addr = $addr.', '.$state;
            }
            if($country!='')
            {
                $addr = $addr.', '.$country;
            }

            $temparray = array('address'       => $addr,
                               'transactionid' => $ltid, 
                         );
            array_push($address, $temparray);

            $addr="";
        }

        return $address;
    }

    public function anyTrackingdrivers(){
        
        return View::make('logistic.location_map');
        
    }

    public function anyTrackingvalues(){
        $arr = array(); 
        $address = ""; 
        
        $MalaysiaCountryID = 458;
        $regionid = 0;
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
        }
        
        
                        // ->where('created_date','LIKE','%'.DATE("Y-m-d").'%');
       try
       {
           if (isset($regionid) && $regionid ==0){
            $sql = "Select * from jocom_trackingsignal where id in (
    select max(id) from jocom_trackingsignal where DATE_FORMAT(created_date,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') group by driverid order by created_date ASC
    )";    
            }
            else
            {
                $sql ="Select * from jocom_trackingsignal where id in(select max(id) from jocom_trackingsignal where driverid in (select id from logistic_driver where region_id=".$regionid.") and DATE_FORMAT(created_date,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') group by driverid order by created_date ASC)";
    
            }
   // echo $sql;
//         $sql = "Select * from jocom_trackingsignal where id in (
// select max(id) from jocom_trackingsignal where DATE_FORMAT(created_date,'%Y-%m-%d') = DATE_FORMAT(NOW(),'%Y-%m-%d') group by driverid order by created_date ASC
// )";
            $tracking = DB::select($sql);
                

            foreach ($tracking as $key => $value) {
                          # code...
                       $address = "";
                       $dresult=DB::table('logistic_driver')
                                  ->where('id',$value->driverid)
                                  ->first();
                       //$url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($value->latitude).','.trim($value->longitude).'&sensor=false';
                      //  $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($value->latitude).','.trim($value->longitude).'&sensor=false&key=AIzaSyBUxtVK-pTKvTDxnmug5DenecCZna15EPE';
                      $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($value->latitude).','.trim($value->longitude).'&sensor=false&key=AIzaSyDUFmbwJMBHU_paeMfVO7oqPC1IJEtbJUU';
                    //   $url = 'https://maps.googleapis.com/maps/api/geocode/json?latlng='.trim($value->latitude).','.trim($value->longitude).'&sensor=false&key=AIzaSyAz0Nd2J_FpavXHGmOGd8EaYX1rRDVH5Pc';
                        $json = @file_get_contents($url);
                        $data=json_decode($json);
                        $status = $data->status;
                        if($status=="OK"){
                             $address = $data->results[0]->formatted_address;
                        }

                       $temp =  array('driverid'     => $value->driverid,
                         'latitude'     => $value->latitude,
                         'longitude'    => $value->longitude,
                         'created_date' => $value->created_date,
                         'driver_name'  => $dresult->name,
                         'address'      => $address,
                         );
                       array_push($arr, $temp);
                      }          

                     // echo $tracking;                        
        return $arr;

       }
       catch(Exception $ex){
        return $ex->getMessage();
       }
    }



    /*
     * @desc    : Datatables location listing
     * @Return  : Datatables type collection
     */
    public function anyLocationlisting(){
        
        
        /* Get Posted value */
        $state_id = Input::get('state');
        $city_id = Input::get('city');
        $postcode = Input::get('postcode');
        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');

        $pending        = Input::get('pending');
        $undelivered    = Input::get('undelivered');
        $returned       = Input::get('returned');
        $partialSent    = Input::get('partialSent');
        $sending        = Input::get('sending');
        $sent           = Input::get('sent');
        $cancelled      = Input::get('cancelled');
        // print $sent.$cancelled;
        // print $returned.'news';
        // die('ok');
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();
        foreach ($SysAdminRegion as  $value) {
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }

        $orders = DB::table('logistic_transaction')->select(array(
                        'transaction_id','transaction_date','delivery_name','delivery_state','delivery_city','delivery_postcode','status'
                        ))
                ->whereIn('status',array($pending,$undelivered,$returned,$partialSent,$sending,$sent,$cancelled));

        
        if(!empty($state_id) && $state_id != "-"){
             //$State = State::find($state_id);
             //$orders = $orders->where("delivery_state",$State->name);
            $State =  DB::table('jocom_country_states')->whereIn('id',$state_id)->lists('name');
         
            $orders = $orders->whereIn("delivery_state",$State);
        }
        
        if(!empty($city_id) && $city_id != "-"){
            $orders = $orders->where("delivery_city_id",$city_id);
        }
        
        if(!empty($postcode)){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_postcode",$postcode);
        }
        
        if(!empty($transaction_from)){
            $orders = $orders->where('transaction_date', '>=', $transaction_from." 00:00:00");
        }else{
            $orders = $orders->where('transaction_date', '>=', DATE("Y-m-d")." 00:00:00");
        }

        if(!empty($transaction_to)){
            $orders = $orders->where('transaction_date', '<=', $transaction_to." 23:59:59");
        }else{
            $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        }
        
        if(count($stateName) > 0){
            $orders = $orders->whereIn('delivery_state', $stateName);
        }
        
        // if(!empty($transaction_to)){
        //     $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        // }
        
        return Datatables::of($orders)
                ->edit_column('status', '
                    @if($status == 0)
                        <p class="text-danger">Pending</p>
                    @elseif ($status == 1)  
                        <p class="text-danger">Undelivered</p>
                    @elseif ($status == 2)
                        <p class="text-danger">Partial Send</p>
                    @elseif ($status == 3)
                        <p class="text-danger">Returned</p>
                    @elseif ($status == 4)
                        <p class="text-danger">Sending</p>
                    @elseif ($status == 5)
                        <p class="text-success">Sent</p>
                    @else
                        <p class="text-danger">Cancelled</p>
                    @endif
                    ')
                ->make(true);
       
        
    }
    
    public function anyMaplocations(){
        // die('New');
        $allLocation = array();
        
        /* Get Posted value */
        $state_id = Input::get('state');
        $city_id = Input::get('city');
        $postcode = Input::get('postcode');
        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');
        $showall= Input::get('showall');

        $pending        = Input::get('pending');
        $undelivered    = Input::get('undelivered');
        $returned       = Input::get('returned');
        $partialSent    = Input::get('partialSent');
        $sending        = Input::get('sending');
        $sent           = Input::get('sent');
        $cancelled      = Input::get('cancelled');
        
        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
            if (isset($regionid) && $regionid ==0){
                $State = State::getStateByCountry($MalaysiaCountryID);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }else{
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }
            
        }
        
        // print $sent.$cancelled;
        // $orders = DB::table('logistic_transaction')->select(array(
        //                 'transaction_id','transaction_date','delivery_name','delivery_state','delivery_city','delivery_postcode'
        //                 ))
        //         ->where('status','=',0);

        $orders = DB::table('logistic_transaction')->select(array(
                        'transaction_id','transaction_date','delivery_name','delivery_state','delivery_city','delivery_postcode','status'
                        ))
                // ->where('status','=',0);
                ->whereIn('status',array($pending,$undelivered,$returned,$partialSent,$sending,$sent,$cancelled))
                ->whereIn('delivery_state',$stateName);
        
        if(!empty($state_id) && $state_id != "-"){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_state",$State->name);
        }
        
        if(!empty($city_id) && $city_id != "-"){
            $orders = $orders->where("delivery_city_id",$city_id);
        }
        
        if(!empty($postcode)){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_postcode",$postcode);
        }
        
        if(!empty($transaction_from)){
            $orders = $orders->where('transaction_date', '>=', $transaction_from." 00:00:00");
        }else{
            $orders = $orders->where('transaction_date', '>=', DATE("Y-m-d")." 00:00:00");  
        }
        
        if(!empty($transaction_to)){
            $orders = $orders->where('transaction_date', '<=', $transaction_to." 23:59:59");
        }else{
            $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        }
        
        // if(!empty($transaction_to)){
        //     $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        // }
        
        $orders = $orders->get();

        $transaction_ids = array();
        $ids = array();
        foreach ($orders as $order) {
            array_push($transaction_ids, $order->transaction_id);
        }

        if ($showall =='showall') {
            $ids = $transaction_ids;
        } else {

            $transaction_infos = DB::table('logistic_transaction_map')
                                    ->select('transaction_id')
                                    ->whereIn('transaction_id', $transaction_ids)
                                    ->get();

            $map = array();
            foreach ($transaction_infos as $info) {
                array_push($map, $info->transaction_id);
            }

            $ids = array_diff($transaction_ids, $map);
        }

        $LocationInfo = $this->getBulkLocationDetails($ids);
        
        return $LocationInfo;
        
    }

    public function getBulkLocationDetails($ids){
        
        $orders = DB::table('logistic_transaction')->whereIn('transaction_id', $ids)->get();

        $locations = array();

        DB::beginTransaction();
        foreach ($orders as $order) {
            $street = str_replace("".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$order->delivery_addr_1)), '#', '');
            $route = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$order->delivery_addr_2));
            $city = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$order->delivery_city));
            $state = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$order->delivery_state));
            $country = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$order->delivery_country));
            $postcode = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$order->delivery_postcode));

            if($order->gps_latitude == "" || $order->gps_latitude == null){
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
                
                if($order->transaction_id>0){
                    $TransactionData = LogisticTransaction::where('transaction_id','=',$order->transaction_id)->first();
                    $TransactionData->gps_latitude = $latitude;
                    $TransactionData->gps_longitude = $longitute;
                    $TransactionData->save();
                } 
                
            }else{
                $latitude = $order->gps_latitude;
                $longitute = $order->gps_longitude;
            }

            array_push($locations, array(
                "transaction_id" => $order->transaction_id,
                "latitude" => $latitude,
                "longitude" => $longitute,
                "address" =>array(
                    "street_1" => $order->delivery_addr_1,
                    "street_2" => $order->delivery_addr_2,
                    "city" => $order->delivery_city,
                    "state" => $order->delivery_state,
                    "postcode" => $order->delivery_postcode,
                    "country" => $order->delivery_country,
                )
            ));
        }
        DB::commit();
        
        return $locations;
    }
    

    public function anyMaplocationslist(){
        // die('New');
        $allLocation = array();
        // $inputparams = array();

        
        /* Get Posted value */
        $state_id = Input::get('hidstate');
        $city_id = Input::get('hidcity');
        $postcode = Input::get('hidpostcode');
        $transaction_from = Input::get('hidtransaction_from');
        $transaction_to = Input::get('hidtransaction_to');
        $showall= Input::get('showall');

        $pending        = Input::get('hidpending');
        $undelivered    = Input::get('hidundelivered');
        $returned       = Input::get('hidreturned');
        $partialSent    = Input::get('hidpartialsent');
        $sending        = Input::get('hidsending');
        $sent           = Input::get('hidsent');
        $cancelled      = Input::get('hidcancelled');
        
        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
            if (isset($regionid) && $regionid ==0){
                $State = State::getStateByCountry($MalaysiaCountryID);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }else{
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }
            
        }
        // print $transaction_from.$transaction_to;
        // console.log($transaction_from.$transaction_to."ok");
        // $orders = DB::table('logistic_transaction')->select(array(
        //                 'transaction_id','transaction_date','delivery_name','delivery_state','delivery_city','delivery_postcode'
        //                 ))
        //         ->where('status','=',0);

        
        $inputparams  = array('hidstate' => $state_id,
                              'hidcity'  => $city_id,
                              'hidpostcode' => $postcode,
                              'hidtransaction_from' => $transaction_from,
                              'hidtransaction_to' => $transaction_to,
                              'showall' => $showall,
                              'hidpending' => $pending,
                              'hidundelivered' => $undelivered,
                              'hidreturned' => $returned,
                              'hidpartialsent' => $partialSent,
                              'hidsending' => $sending,
                              'hidsent' => $sent,
                              'hidcancelled' => $cancelled,

         );   

        // array_push($inputparams, $inputparamval);


        $orders = DB::table('logistic_transaction')->select(array(
                        'transaction_id','transaction_date','delivery_name','delivery_state','delivery_city','delivery_postcode','status'
                        ))
                // ->where('status','=',0);
                ->whereIn('status',array($pending,$undelivered,$returned,$partialSent,$sending,$sent,$cancelled))
                ->whereIn('delivery_state',$stateName);
        
        if(!empty($state_id) && $state_id != "-"){
            // $State = State::find($state_id);
            // $orders = $orders->where("delivery_state",$State->name);

            $State =  DB::table('jocom_country_states')->whereIn('id',$state_id)->lists('name');
            $orders = $orders->whereIn("delivery_state",$State);
        }
        
        if(!empty($city_id) && $city_id != "-"){
            $orders = $orders->where("delivery_city_id",$city_id);
        }
        
        if(!empty($postcode)){
            $State = State::find($state_id);
            $orders = $orders->where("delivery_postcode",$postcode);
        }
        
        if(!empty($transaction_from)){
            $orders = $orders->where('transaction_date', '>=', $transaction_from." 00:00:00");
        }else{
            $orders = $orders->where('transaction_date', '>=', DATE("Y-m-d")." 00:00:00");  
        }
        
        if(!empty($transaction_to)){
            $orders = $orders->where('transaction_date', '<=', $transaction_to." 23:59:59");
        }else{
            $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        }
        
        // if(!empty($transaction_to)){
        //     $orders = $orders->where('transaction_date', '<=', DATE("Y-m-d")." 23:59:59");
        // }
        
        $orders = $orders->get();


        foreach ($orders as $key => $value) {
            $transactionID = $value->transaction_id;
            $TransactionInfo = LogisticTransaction::getTransactionmapid($transactionID);
            if(count($TransactionInfo)==0 || $showall=='showall')
            {
                $LocationInfo = $this->getLocationDetails($transactionID);
                array_push($allLocation, $LocationInfo);
            }
            
           
        }

        return View::make('logistic.log_location_map')
                        ->with('inputparams',$inputparams)
                        ->with('locations', json_encode($allLocation));

                                            
        
        // return $allLocation;
        
    }
    
    public function anyMapdriverlocations(){
        // die('New');
        $allLocation = array();
        
        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');
        $driver_id = Input::get('driver_id');
        
        $orders = DB::table('logistic_batch as batch')
                    ->join('logistic_transaction as transaction', 'batch.logistic_id', '=', 'transaction.id')
                    ->select('transaction.transaction_id','transaction.transaction_date','transaction.delivery_name','transaction.delivery_state','transaction.delivery_city','transaction.delivery_postcode','transaction.status')
                    ->where('batch.driver_id', '=', $driver_id)
                    ->whereIn('transaction.status',array(0,1,3,4));
        
        
        if(!empty($transaction_from)){
            $orders = $orders->where('transaction.transaction_date', '>=', $transaction_from." 00:00:00");
        }
        
        if(!empty($transaction_to)){
            $orders = $orders->where('transaction.transaction_date', '<=', $transaction_to." 23:59:59");
        }
        
        $orders = $orders->get();

        $transaction_ids = array();

        foreach ($orders as $order) {
            array_push($transaction_ids, $order->transaction_id);
        }

        $LocationInfo = $this->getBulkLocationDetails($transaction_ids);
        $LocationInfoWithStatus = array();

        foreach ($LocationInfo as $info) {
            foreach ($orders as $order) {
                if ($order->transaction_id == $info['transaction_id']) {
                    $info['status'] = $order->status;
                    array_push($LocationInfoWithStatus, $info);
                }
            }
        }
        
        return $LocationInfoWithStatus;
    }

    public function anyMaplocationsdriverlist(){

        $driver_list = LogisticDriver::where('status', '=', 1)
                                    ->where('is_logistic_dashboard', '=', 1)
                                    ->get();

        return View::make('logistic.log_location_map_driver')
                        ->with('driver_list', $driver_list);
    }
    
    /*
     * @Desc    : Collection transaction location information 
     * @Return  : Array
     */
    public function getLocationDetails($TransactionID){
        
        // echo $TransactionID;
        // die($TransactionID);
        // $TransactionInfo = Transaction::find($TransactionID);

        // $TransactionInfo=DB::table('logistic_transaction')
        //                 ->where('transaction_id','=',$TransactionID)
        //                 ->get();
                        // print_r($TransactionInfo);
        $TransactionInfo = LogisticTransaction::get_transaction($TransactionID);
        
        $ltid=$TransactionInfo->id;
        $street = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$TransactionInfo->delivery_addr_1));
        $route = "".str_replace(" ","+",preg_replace('/\&\s+/', ' ',$TransactionInfo->delivery_addr_2));
        $city = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_city));
        $state = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_state));
        $country = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_country));
        $postcode = "".str_replace(" ","+",preg_replace('/\s+/', ' ',$TransactionInfo->delivery_postcode));
        // print $street;

        if($TransactionInfo->gps_latitude == "" || $TransactionInfo->gps_latitude == null){
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
            
            if($TransactionID>0){
                $TransactionData = LogisticTransaction::where('transaction_id','=',$TransactionID)->first();
                $TransactionData->gps_latitude = $latitude;
                $TransactionData->gps_longitude = $longitute;
                //$TransactionData->modify_by = Session::get('username');
                //$TransactionData->modify_date = DATE("Y-m-d H:i:s");
                $TransactionData->save();
            } 
            
        }else{
            
            $latitude = $TransactionInfo->gps_latitude;
            $longitute = $TransactionInfo->gps_longitude;
            
        }
        
        return array(
            "transaction_id" => $TransactionID,
            "latitude" => $latitude,
            "longitude" => $longitute,
            // "ltid" => $ltid,
            "address" =>array(
                "street_1" => $TransactionInfo->delivery_addr_1,
                "street_2" => $TransactionInfo->delivery_addr_2,
                "city" => $TransactionInfo->delivery_city,
                "state" => $TransactionInfo->delivery_state,
                "postcode" => $TransactionInfo->delivery_postcode,
                "country" => $TransactionInfo->delivery_country,
            )
        );
        
        
    }
    
    /*
     * @Desc    : Get location details base on transaction id
     */
    public function anyGetlocation(){
        
        // Get address information based on transaction id
        
        $TransactionID = Input::get('transactionID');
        // echo $TransactionID;
        $TransactionInfo = LogisticTransaction::get_transaction($TransactionID);
        // $TransactionInfo = Transaction::find($TransactionID);
        // die($TransactionInfo);
        $locationDetails = $this->getLocationDetails($TransactionID);
        return $locationDetails;

    }
    
    /*
     * @Desc    : Get delivery location altitude and longititude
     */
    public function anyUpdatelatlong(){
        
        $isError = 0;
        $response = 1;
        
        try{
            $transactionID = Input::get("transactionID");
            $latlong = explode(",",Input::get("latlong"));
            $latitude = $latlong[0];
            $longitude = $latlong[1];

            // $TransactionData=DB::table('logistic_transaction')
            //             ->where('transaction_id','=',$transactionID);
            // console.log($transactionID);
            // console.log('new'); 
            $TransactionData = LogisticTransaction::where('transaction_id','=',$transactionID)->first();
            // print_r $TransactionData;
            $TransactionData->gps_latitude = $latitude;
            $TransactionData->gps_longitude = $longitude;
            $TransactionData->modify_by = Session::get('username');
            $TransactionData->modify_date = DATE("Y-m-d H:i:s");
            $TransactionData->save();
        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }finally {
            return array(
                "response"=>$response,
                "latitude"=>$latitude,
                "longitude"=>$longitude,
            );
            
        }
    }   

    public function anySelectgroup(){
        
        $locationDetails =array();
        $groupname=Input::get('groupname');
        $groupid=Input::get('groupid');
        $grouptransaction=LogisticTransaction::getGrouptransaction($groupname,$groupid);

        if(count($grouptransaction)>0){
            
            foreach ($grouptransaction as $value){
                $locationDetails[] = $this->getLocationDetails($value->transaction_id);
            }
        }
       
         return $locationDetails;

    }


    public function anyGetgrouptrans(){
        $group  =array();
        $groupname=Input::get("groupname");
        $groupid=Input::get('groupid');
        $grouptransaction=LogisticTransaction::getGrouptransaction($groupname,$groupid);
        if(count($grouptransaction)>0){
            foreach ($grouptransaction as $key => $value) {
                $group[] = $value;
            }

        }
        
        return array('grouptransaction'=>$group);

    }


    public function anyMakegroup(){
        $groupname = array();
        // $translist = array();
        $count      = 0;
        $isData     = 0;
        $recordtype = 0;
        $isExists   = 0;
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));

                    foreach ($SysAdminRegion as  $value) {
                        $regionid = $value;
                    }

        if($regionid == 0){
          $regionid = 1;  
        }

        $transgroupID = explode(",",Input::get("transactionIDs"));
        $id=Input::get("transactionIDs");

        $transactionall = LogisticTransaction::getAlltransactiongroup();
        if(count($transactionall)>0)
        {
            $count = count($transactionall);

            foreach ($transactionall as $key=>$alldata){
                    
                    $gname      = $alldata->groupname;
                    $groupID    = $alldata->id;
                    $groupname[] = $alldata->groupname;

                    foreach ($transgroupID as $tkey => $transvalue) {
                        $grouptransactionall = LogisticTransaction::getAlltransactionmap($groupID,$transvalue);
                        if(count($grouptransactionall)>0){
                            $isExists  = 1;                                  
                        }
                    }
                }   

            if(isset($isExists) && $isExists==0){
                 // print($gname."@");
                $sub=(int)trim(substr($gname,5,10));
                // print('#'.$sub.'#');

                if($sub==$count)
                {
                    $count = $count + 1; 
                }
                else{
                   $count = $sub + 1;   
                }
                


                $gid = DB::table('logistic_transaction_group')->insertGetId(array(
                            'groupname'      => 'Group'.$count,
                            'region_id'      => $regionid 
                            )
                );  

                if(count($transgroupID)==1){

                
                    DB::table('logistic_transaction_map')->insert(array(
                            'group_id'          =>  $gid,
                            'transaction_id'    =>  $id,
                            'region_id'      => $regionid 
                            )
                    );
                }
                else
                {
                    
                    foreach ($transgroupID as $key => $value) {
                        DB::table('logistic_transaction_map')->insert(array(
                                'group_id'          =>  $gid,
                                'transaction_id'    =>  $value,
                                'region_id'      => $regionid 
                                )
                        );

                        }     
                }
                $groupname[]  = 'Group'.$count;
                $recordtype = 1;    
            }
        }
        else 
        {   
            if(count($transgroupID)==1){

                $gid = DB::table('logistic_transaction_group')->insertGetId(array(
                            'groupname'      => 'Group1',
                            'region_id'      => $regionid  
                            )
                );
                DB::table('logistic_transaction_map')->insert(array(
                        'group_id'          =>  $gid,
                        'transaction_id'    =>  $id,
                        'region_id'      => $regionid 
                        )
                );
             $count = 1;
             $groupname = 'Group1';
             $recordtype = 1;
            }
            else
            {
                $gid = DB::table('logistic_transaction_group')->insertGetId(array(
                            'groupname'      => 'Group1',
                            'region_id'      => $regionid  
                            )
                );
                foreach ($transgroupID as $key => $value) {
                    DB::table('logistic_transaction_map')->insert(array(
                            'group_id'          =>  $gid,
                            'transaction_id'    =>  $value,
                            'region_id'      => $regionid 
                            )
                    );
                    }     
                $count = 1;
                $groupname = 'Group1';
                $recordtype = 1;
            }
        }
         return array(
                'groupname'     => $groupname,
                // 'translist'    => $translist,
                'count'         => $count,
                'recordtype'    => $recordtype,
             );

    }

    public function anyShowgroup()
    {
        $groupname = array();
        $groupid = array();
        $count = 0;

        $transactionall = LogisticTransaction::getAlltransactiongroup();
        if(count($transactionall)>0)
        {
            $count = count($transactionall);

            foreach ($transactionall as $key=>$alldata) {
                    $groupname[] = $alldata->groupname;
                    $groupid[] = $alldata->id;
                } 
        }
        return array(
                'groupname'     => $groupname,
                'groupid'       => $groupid,
                'count'         => $count,
             );

    }

    public function anyResetgroup(){
        $isError = 0;
        $response = 1;
        // die('ss');

        $MalaysiaCountryID = 458;
        $regionid = 0;
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
        }

        if($regionid == 0){
            $regionid = 1;
        }


        try
        { 
            DB::table('logistic_transaction_group')->where('region_id', '=', $regionid)->delete();
            DB::table('logistic_transaction_map')->where('region_id', '=', $regionid)->delete();

           // DB::table('logistic_transaction_group')->truncate();
           // DB::table('logistic_transaction_map')->truncate();

        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }
        finally{
           return $response = 1;
        }


    }

    public function anyUpdategroup(){
        $isError        = 0;
        $response       = 1;

        $groupname      = Input::get("groupname");
        $id             = Input::get("transactions");
        $hidgroupid     = Input::get('hidgroupid');
        $transgroupID   = explode(",",Input::get("transactions"));

        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));

                    foreach ($SysAdminRegion as  $value) {
                        $regionid = $value;
                       
                    }

        if($regionid == 0){
          $regionid = 1;  
        }

        try
        { 

        $group  =DB::table('logistic_transaction_group')  
                    ->where('groupname','=',$groupname)
                    ->where('id','=',$hidgroupid)
                    ->first();

                DB::table('logistic_transaction_map')
                    ->where('group_id','=',$group->id)
                    ->delete();     

            $gid = $group->id;        


            if(count($transgroupID)==1){

            
                DB::table('logistic_transaction_map')->insert(array(
                        'group_id'          =>  $gid,
                        'transaction_id'    =>  $id,
                        'region_id'    =>  $regionid,
                        )
                );
            }
            else
            {
                
                foreach ($transgroupID as $key => $value) {
                    DB::table('logistic_transaction_map')->insert(array(
                            'group_id'          =>  $gid,
                            'transaction_id'    =>  $value,
                            'region_id'    =>  $regionid,
                            )
                    );

                    }     
            }  
           

        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }
        finally{
           return array('response' => $response, );
        }    

    }

    public function anyDeletegroup(){
        $response = 1;
        
        $transgroupID   = Input::get("transactionid");
        $hidgroupname   = Input::get('hidgroup');
        $hidgroupid     = Input::get('hidgroupid');

        $group  =DB::table('logistic_transaction_group')  
                    ->where('groupname','=',$hidgroupname)
                    ->where('id','=',$hidgroupid)
                    ->first();

                DB::table('logistic_transaction_group')
                    ->where('groupname','=',$hidgroupname)
                    ->where('id','=',$hidgroupid)
                    ->delete();

                DB::table('logistic_transaction_map')
                    ->where('group_id','=',$group->id)
                    ->delete();

        return array('response' => $response, );
    }

    public function anyGrouplist(){

        $group          = array();
        $grouppending   = array();
        $driver   = array();
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));

                    foreach ($SysAdminRegion as  $value) {
                        $regionid = $value;
                       
                    }
        if($regionid ==0){
          $regionid = 1;  
        }
        
        $grouptrans = DB::table('logistic_transaction_group AS LTG')
                         // ->select('LTG.id,LTG.groupname,LTG.driver_id,LD.name')
                         ->leftJoin('logistic_driver AS LD','LTG.driver_id','=','LD.id')
                         ->where('LTG.driver_id','<>','NULL')
                         ->where('LTG.region_id','=',$regionid)
                         ->get();

               foreach ($grouptrans as $value) {

                            $arrylist =  array('id'             => $value->id,
                                                'groupname'     => $value->groupname,
                                                'driver_id'     => $value->driver_id,
                                                'drivername'    => $value->name,

                                                 );
                            array_push($group, $arrylist);
                         } 

                $groupNametrans = DB::table('logistic_transaction_group')
                         ->wherenull('driver_id')
                         ->where('region_id','=',$regionid)
                         // ->where('driver_id','IS','NULL')
                         ->get();

                foreach ($groupNametrans as  $val) {
                    
                        $arrayname = array('id'         => $val->id,
                                           'groupname'  => $val->groupname,
                         );

                        array_push($grouppending, $arrayname);

                    } 


                $logdriver = DB::table('logistic_driver')
                         ->where('status','=','1')
                         ->where('is_logistic_dashboard', '=', '1')
                         ->where('region_id','=',$regionid)
                         ->get();

                foreach ($logdriver as  $val1) {
                    
                        $arraydriver = array('id'         => $val1->id,
                                           'drivername'  => $val1->name,
                         );

                        array_push($driver, $arraydriver);

                    } 

                return array('group'        => $group,
                             'pendinggroup' => $grouppending,
                             'driver'       => $driver,
                            );      
    }

    public function anyDriver(){

         $driverid        = Input::get('driverid');
         $logdriver = DB::table('logistic_driver')
                         ->where('id',$driverid)
                         ->first();
             // print 'oknew';             
         return $logdriver->name;               

    }

    public function anyAssigndriver(){

        $isError    = 0;
        $response   = 1;
        $data       = array();
        $groupid    = array();
        $get        = array();
        $gupdateid  = 0;

        $groupid    = explode(",",implode(",",Input::get('groupids')));
        //$groupid = Input::get('groupids');
        $driverid   = Input::get('driverid');

        

        try
        {   
            foreach ($groupid as  $value) {

                 
              //  echo 'K'.$value[$key].'K<br>';
              //  $param_transaction=0;
              //  $param_logisticid =0;
                
                // echo $value;
                $gupdateid = $value;
                
               $grouptransactions = LogisticTransaction::getGtransaction($value);

                // print_r($grouptransactions);

                    foreach ($grouptransactions as $valuetrans) {                        
                        // echo $valuetrans['transaction_id'];
                        $logisticsTrans = LogisticTransaction::getLogisticID($valuetrans['transaction_id']);
                        
                        //$param_transaction = $valuetrans['transaction_id'];
                       // print $valuetrans['transaction_id'].'Hte';
                        //print_r($logisticsTrans->id);
                         // $get['transaction_id']    = $valuetrans['transaction_id'];
                         $get['cms'] = 1;
                         $get['logistic_id']    = $logisticsTrans->id;                         
                         
                         $list = LogisticTItem::where('logistic_id', '=', $logisticsTrans->id)->get();
                            
                            unset($detail_1);
                            unset($detail_2);
                            
                            foreach ($list as $row)
                            {                            
                                $detail_1[] = $row->id;
                                $detail_2[] = $row->qty_to_assign;
                               
                            }

                        $get['item_id']     = $detail_1;
                        $get['qty_assign']  = $detail_2;
                        $get['driver_id']   = $driverid;
                        
                        $get['username'] = '[CMS]'.Session::get('username');

                        $data = LogisticTransaction::api_transaction_detail($get);


                        if($gupdateid != 0)
                        {

                            DB::table('logistic_transaction_group')
                                ->where('id',$gupdateid)
                                ->update(array('driver_id' => $driverid));
                                 
                        }
                        
                    }

            }

        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }
        finally{
            return $response;
           
        }

    }

    public function anyGetgrouptransactions(){

        $array = array();

        $isError = 0;
        $response = 1;
        

          $groupname = Input::get('groupname');

        try
        {
             $transgroupID = explode(",",Input::get("groubtrans"));
            // print_r($transgroupID);
              
              $Transactionlist  = LogisticTransaction::getGrouptransaction($groupname);

              foreach ($Transactionlist as $tvalue) {

                $array[] = $tvalue->transaction_id;
                        
              }
               

        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }
        finally{
            return $array;
            
        }
              


    }
               
    public function anyBatchlists(){

        $isError = 0;
        $response = 1;

        $array = array();
        



        try
         {
            $startDate = Input::get("startDate");
            $toDate = Input::get("toDate");
               
            $startDate = date('Y-m-d', strtotime($startDate))." 00:00:00";;
            $toDate = date('Y-m-d', strtotime($toDate))." 23:23:59";
                
                $driverstatus   = 1;
                
                 $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));

                    foreach ($SysAdminRegion as  $value) {
                        $regionid = $value;
                       
                    }
                
                if($regionid == 0){
                    $regionid = 1;
                }

                $resultdriver=DB::table('logistic_driver')
                                  ->where('status','=',$driverstatus)
                                  ->where('region_id','=',$regionid)
                                  ->get();

                foreach ($resultdriver as  $value){
                    // print 'P'.$value->id.'P';
                    $result = LogisticTransaction::getBatchtransaction($value->id,$startDate,$toDate);


                    $array[]= array('id'     => $value->id,
                                    'driver' => $value->name,
                                    'total'  => $result, );

                    // print_r($result);

                        
            }

            
         }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }
        finally{
            return $array;

        }
    }
        
            

    public function anyDeletgroup(){
        $isError    = 0;
        $response   = 1;
        
        $groupname = Input::get('groupname');   
        $groupid   = Input::get('groupid');
        $lbatch    = 0;
        $status    = 2;
        $data      = array();



        try
        {
            $data = array('status' => $status); 
            $Transactionlist  = LogisticTransaction::getGrouptransaction($groupname,$groupid);

            foreach ($Transactionlist as $tvalue) {

                $logisticsTrans = LogisticTransaction::getLogisticID($tvalue->transaction_id);


                $batchtrans = DB::table('logistic_batch')
                                ->where('status','=',$lbatch)
                                ->where('logistic_id','=',$logisticsTrans->id)
                                ->first();

                 // print_r($data);

                 $dataret=LogisticBatch::UpdateBatch($batchtrans->id,$data);              

                 // print  $batchtrans->id.'L';              
           
            }


            if($groupname != '')
            {

                $transid = LogisticTransaction::getTransactiongroup($groupname,$groupid);

                DB::table('logistic_transaction_group')
                    ->where('id',$transid->id)
                    ->update(array('driver_id' => null));
                     
            }
           

            // $grouptransactions = LogisticTransaction::getGtransaction($groupid);


               
         //ßß   $groupname  =2;

        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }
        finally{
            return $response;
           
        }
    }

    public function anyPrintdo(){

        $arr    =[];

        $isError = 0;
        $response = 1;
        

        // die('ss');
        try
        {
             $transgroupID = explode(",",Input::get("groubtrans"));
            // print_r($transgroupID);
              

            foreach ($transgroupID as $key) {
                $filenew ="";
                $encrypted="";
                $TransactionData = LogisticTransaction::where('transaction_id','=',$key)->first();
                // $file = Config::get('constants.DO_PDF_FILE_PATH') . '/' . urlencode($TransactionData->do_no) . '.pdf';
                // $filename=urlencode($TransactionData->do_no) . '.pdf';

                $filenew = ($TransactionData->transaction_id)."#".($TransactionData->do_no);
                $encrypted = Crypt::encrypt($filenew);
                $encrypted = urlencode(base64_encode($encrypted)); 
                // print $filenew.'J';
                $array[]=array('do_no' => $TransactionData->do_no, 
                               'transaction_id' => $TransactionData->transaction_id,
                               'id' => $TransactionData->id,
                               'filenew' => $encrypted,
                               'filename' => $filenew,

                      // file_get_contents($file)   
                        
                    );

            }

        
        }catch (Exception $ex) {
            $isError = 1;
            $response = 0;
        }
        finally{
            return $array;
           
        }
       


        // echo($transgroupID);

    }

    public function anySearch(){

        return View::make('logistic.search');
   
    }

    public function anySearchlisting(){

        $search = DB::table('logistic_transaction_item')
                ->select([
                    'logistic_transaction_item.id',
                    'logistic_transaction_item.logistic_id', 
                    'logistic_transaction_item.sku', 
                    'logistic_transaction_item.name', 
                    'logistic_transaction_item.qty_order', 
                    'logistic_transaction_item.qty_to_assign', 
                    'logistic_transaction_item.qty_to_send', 
                    'logistic_batch.status', 
                    'logistic_batch.do_no', 
                    'logistic_driver.username'
                ])
                ->join('logistic_batch', 'logistic_transaction_item.logistic_id', '=', 'logistic_batch.logistic_id')
                ->join('logistic_driver', 'logistic_driver.id', '=', 'logistic_batch.driver_id')
                ->groupBy('logistic_transaction_item.id', 'logistic_batch.status', 'logistic_batch.do_no');

        $from_date      = Input::get('from_date');
        $to_date        = Input::get('to_date');
        $batch_date     = Input::get('batch_date');
        $accept_date    = Input::get('accept_date');
        $status         = Input::get('status');
        $drivers        = Input::get('drivers');

        if (Input::get('date_option') == 'batch_date') {

            if (Input::get('from_date') != NULL) {
                $search= $search->where('logistic_batch.batch_date', '>=', $from_date);
            }

            if (Input::get('to_date') != NULL) {
                $search= $search->where('logistic_batch.batch_date', '<=', $to_date);
            }
        }

        if (Input::get('date_option') == 'accept_date') {

            if (Input::get('from_date') != NULL) {
                $search= $search->where('logistic_batch.accept_date', '>=', $from_date);
            }

            if (Input::get('to_date') != NULL) {
                $search= $search->where('logistic_batch.accept_date', '<=', $to_date);
            }
        }

        if (Input::get('status') != NULL) {
            $search= $search->whereIn('logistic_batch.status', $status);
        }

        if (Input::get('drivers') != NULL) {
  
            $search= $search->where('logistic_batch.driver_id', '=', $drivers);

        }

        return Datatables::of($search)
        ->add_column('status', '
                    @if($status == 0)
                        <p>Pending</p>
                    @elseif ($status == 1)
                        <p>Sending</p>
                    @elseif ($status == 2)
                        <p>Returned</p>
                    @elseif ($status == 3)
                        <p>Undelivered</p>
                    @elseif ($status == 4)
                        <p class="text-success">Sent</p>
                    @elseif ($status == 5)
                        <p class="text-danger">Cancelled</p>
                    @endif
                    ')
        ->add_column('logistic_batch.do_no', function($trans)
            {
                $file = asset('/') . Config::get('constants.LOGISTIC_DO_PATH') . '/' . $trans->do_no . '.pdf';
                return '<a href ='.$file.' target=_blank >'.$trans->do_no.'</a>';
                            
            })
        ->make();

    }

    public function anyDohistory(){

        return View::make('logistic.dohistory');
   
    }

    public function anyDohistorylisting(){

        $result = DB::table('jocom_doprinting_history')
                ->select([
                    'jocom_doprinting_history.id',
                    'jocom_doprinting_history.username', 
                    'jocom_doprinting_history.transaction_id', 
                    'jocom_doprinting_history.do_no', 
                    'jocom_doprinting_history.type', 
                    'jocom_doprinting_history.total_doprint',
                    'jocom_doprinting_history.created_at'
                    
                ])
                //->orderBy('jocom_doprinting_history.username', 'jocom_doprinting_history.total_doprint')
                ->orderby('id','DESC');



                $from_date      = Input::get('from_date');
                $to_date        = Input::get('to_date');
                $username       = Input::get('user');

                if (Input::get('from_date') != NULL) {
                    $result= $result->where('jocom_doprinting_history.created_at', '>=', $from_date." 00:00:00");
                }

                if (Input::get('to_date') != NULL) {
                    $result= $result->where('jocom_doprinting_history.created_at', '<=', $to_date." 23:59:59");
                }

                if (Input::get('user') != NULL) {
          
                    $result= $result->where('jocom_doprinting_history.username', '=', $username);

                }


         return Datatables::of($result)
                ->make();


    }

    public function anyDashboard()
    {
        $admin_username = Session::get('username');            
        $user = DB::table('jocom_sys_admin')->where('username','=',$admin_username)->first();       
        $region = DB::table('jocom_sys_admin_region AS JSR')        
                ->select('JSR.*')               
                ->where('JSR.sys_admin_id', $user->id)      
                ->where('JSR.status', 1)        
                ->first();      
        $regionName = DB::table('jocom_region')->where('id','=',$region->region_id)->first();       
            
        return View::make('logistic.logistic_dashboard', ['region_id'=> $region->region_id, 'region_name'=>$regionName->region]);
    }
    
    public function anyDashboardwh()
    {
        $admin_username = Session::get('username');            
        $user = DB::table('jocom_sys_admin')->where('username','=',$admin_username)->first();       
        $region = DB::table('jocom_sys_admin_region AS JSR')        
                ->select('JSR.*')               
                ->where('JSR.sys_admin_id', $user->id)      
                ->where('JSR.status', 1)        
                ->first();      
        $regionName = DB::table('jocom_region')->where('id','=',$region->region_id)->first();       
            
        return View::make('logistic.logistic_dashboardwh', ['region_id'=> $region->region_id, 'region_name'=>$regionName->region]);
    }

    public function anyDashboardlist()
    {
        $status = Input::get("status");
        
        $result = DB::table('logistic_transaction')
                ->where("status", $status)
                ->get();

        return $result;

    }

    public function anyDashboardlistregion()
    {
        $status = Input::get("status");

        $username = Session::get('username'); 

        $states = LogisticTransaction::getStates($username);
        
        $result = DB::table('logistic_transaction')
                ->where("status", $status)
                ->whereIn('delivery_state', $states)
                ->orderby('transaction_id')
                ->get();

        return $result;

    }

    public function anyDashboardcsv()
    {
        $status = $_GET['status'];
        $startDate = $_GET['startDate'];
        $endDate = $_GET['endDate'];
        $period = $_GET['month'];

         //status, period
        if ($period!='' && $status!='') {

            $list = LogisticTransaction::getAllListDelay($status, $period);

            $data = json_decode(json_encode($list), true);

            switch ($status)
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
            }

            $ldate = date('Y-m-d')."-".$status.'-'.$period;

            $path = Config::get('constants.CSV_FILE_PATH');

            Excel::create($ldate, function($excel) use($data) {

                $excel->sheet('Sheet 1', function($sheet) use($data) {

                    $sheet->fromArray($data);

                });

            })->download('xls');
        }

        

        //status, startdate, enddate
       if($status!='' && $startDate!='' && $endDate!='') {

            $result = DB::table('logistic_transaction')
                ->where("status", $status)
                ->whereBetween('insert_date', array($startDate, $endDate))
                ->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')->get();

            $data = json_decode(json_encode($result), true);

            switch ($status)
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
            }

            $start = explode(" ", $startDate);

            $startDate= $start[0]; 

            $end = explode(" ", $endDate);

            $endDate= $end[0]; 

            $ldate = $status.'('.$startDate.' '.$endDate.')';

            $path = Config::get('constants.CSV_FILE_PATH');
      
            Excel::create($ldate, function($excel) use($data) {

                $excel->sheet('Sheet 1', function($sheet) use($data) {

                    $sheet->fromArray($data);

                });

            })->download('csv');

        }

        //status
        if ($status!='') {

            $result = DB::table('logistic_transaction')
                ->where("status", $status)
                ->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')->get();

            $data = json_decode(json_encode($result), true);

            switch ($status)
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
            }

            $ldate = date('Y-m-d')."-".$status;

            $path = Config::get('constants.CSV_FILE_PATH');
      
            Excel::create($ldate, function($excel) use($data) {

                $excel->sheet('Sheet 1', function($sheet) use($data) {

                    $sheet->fromArray($data);

                });

            })->download('csv');

        }

       

    }

     public function anyDashboardcsvregion()
    {
        $status = $_GET['status'];
        $startDate = $_GET['startDate'];
        $endDate = $_GET['endDate'];
        $period = $_GET['month'];


         //status, period
        if ($period!='' && $status!='') {

            $list = LogisticTransaction::getAllListDelayRegion($status, $period);

            $data = json_decode(json_encode($list), true);

            switch ($status)
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
            }

            $ldate = date('Y-m-d')."-".$status.'-'.$period;

            $path = Config::get('constants.CSV_FILE_PATH');

            Excel::create($ldate, function($excel) use($data) {

                $excel->sheet('Sheet 1', function($sheet) use($data) {

                    $sheet->fromArray($data);

                });

            })->download('csv');
        }

        

        //status, startdate, enddate
       if($status!='' && $startDate!='' && $endDate!='') {
        
            $username = Session::get('username'); 

            $states = LogisticTransaction::getStates($username);

            $result = DB::table('logistic_transaction')
                    ->where("status", $status)
                    ->whereBetween('insert_date', array($startDate, $endDate))
                    ->whereIn("delivery_state", $states)
                    ->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')
                    ->orderby('transaction_id')
                    ->get();

            $data = json_decode(json_encode($result), true);

            switch ($status)
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
            }

            $start = explode(" ", $startDate);

            $startDate= $start[0]; 

            $end = explode(" ", $endDate);

            $endDate= $end[0]; 

            $ldate = $status.'('.$startDate.' '.$endDate.')';

            $path = Config::get('constants.CSV_FILE_PATH');
      
            Excel::create($ldate, function($excel) use($data) {

                $excel->sheet('Sheet 1', function($sheet) use($data) {

                    $sheet->fromArray($data);

                });

            })->download('csv');

        }

        //status
        if ($status!='') {

            $username = Session::get('username'); 

            $states = LogisticTransaction::getStates($username);

            $result = DB::table('logistic_transaction')
                    ->where("status", $status)
                    ->whereIn("delivery_state", $states)
                    ->select('transaction_id', 'insert_date', 'delivery_name', 'delivery_state', 'delivery_city')
                    ->orderby('transaction_id')
                    ->get();

            $data = json_decode(json_encode($result), true);

            switch ($status)
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
            }

            $ldate = date('Y-m-d')."-".$status;

            $path = Config::get('constants.CSV_FILE_PATH');
      
            Excel::create($ldate, function($excel) use($data) {

                $excel->sheet('Sheet 1', function($sheet) use($data) {

                    $sheet->fromArray($data);

                });

            })->download('xls');

        }

       

    }
    
    public function anyDashboardlistdelay()
    {

        $status = Input::get("status");
        $period = Input::get("month");
        $list = LogisticTransaction::getAllListDelay($status, $period);
        
        return $list;
        
    }

    public function anyDashboardlistdelayregion()       
    {       
        $status = Input::get("status");     
        $period = Input::get("month");      
        $list = LogisticTransaction::getAllListDelayRegion($status, $period);       
        return $list;       
    }
    
    public function anyDashboardlistdate()
    {

        $status = Input::get("status");
        $startDate = Input::get("startDate");
        $endDate = Input::get("endDate");

        $result = DB::table('logistic_transaction')
                ->where("status", $status)
                ->whereBetween('insert_date', array($startDate, $endDate))
                ->get();

        return $result;

    }

    public function anyDashboardlistdateregion()        
    {       
        $status = Input::get("status");     
        $startDate = Input::get("startDate");       
        $endDate = Input::get("endDate");       
        $username = Session::get('username');       
        $states = LogisticTransaction::getStates($username);        
        $result = DB::table('logistic_transaction')     
                ->where("status", $status)      
                ->whereBetween('insert_date', array($startDate, $endDate))      
                ->whereIn('delivery_state',$states)     
                ->orderby('transaction_id')     
                ->get();        
        return $result;     
    }

    public function anyDashboardstatistic(){
        
        $isError = 0;
        $respStatus = 1;
        $data = array();
        
        try{
            
            $rangeType = Input::get("rangeType");
            $startDate = Input::get("startDate");
            $toDate = Input::get("toDate");
            $navigate = Input::get("navigation");
     
            switch ($rangeType) {
                case 1: // Daily
                    if($navigate == 1){ // LEFT
                        $startDate = date('Y-m-d', strtotime(date($startDate).' -1 days', time()))." 00:00:00";
                        $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                    }else if($navigate == 2){ // RIGHT
                        $startDate = date('Y-m-d', strtotime(date($startDate).' +1 days', time()))." 00:00:00";;
                        $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
                    }else{
                        $startDate = date('Y-m-d', strtotime(date($startDate), time()))." 00:00:00";;
                        $toDate = date('Y-m-d', strtotime(date($startDate), time()))." 23:23:59";
}


                    break;
                case 2: // Weekly
                    
                    $day = 7;
                    if($navigate == 1){ // LEFT
                        $startDate = date('Y-m-d', strtotime(date($startDate).' -'.$day.' days', time()))." 00:00:00";
                        $toDate = date('Y-m-d', strtotime(date($toDate).' -'.($day).' days', time()))." 23:23:59";
                    }else if($navigate == 2){ // RIGHT
                        $startDate = date('Y-m-d', strtotime(date($startDate).' +'.$day.' days', time()))." 00:00:00";;
                        $toDate = date('Y-m-d', strtotime(date($toDate).' +'.$day.' days', time()))." 23:23:59";
                    }

                    break;
                case 3: // Monthly
            
                    if($navigate == 1){ // LEFT
                        $startDate = date('Y-m-d', strtotime(date($startDate).' first day of last month', time()))." 00:00:00";
                        $toDate = date('Y-m-d', strtotime(date($toDate).' last day of last month', time()))." 23:23:59";
                    }else if($navigate == 2){ // RIGHT
                        $startDate = date('Y-m-d', strtotime(date($startDate).' first day of next month', time()))." 00:00:00";
                        $toDate = date('Y-m-d', strtotime(date($toDate).' last day of next month', time()))." 23:23:59";
                    }

                    break;

                default:
                    break;
            }
            
            $displayStartDate = date("d M Y", strtotime($startDate));
            $displayEndDate = date("d M Y", strtotime($toDate));
            
            $currentdate = date("Y-m-d")." 23:59:59";
            
            $data['DateSelection'] = array(
                "startDate" => $startDate,
                "toDate" => $toDate,
                "displayStartDate" => $displayStartDate,
                "displayEndDate" => $displayEndDate,
                "today" => date("Y-m-d"),
                "WeeklyStartDate" =>  date("Y-m-d", strtotime('monday this week')), 
                "WeeklyEndDate" =>  date("Y-m-d",  strtotime('sunday this week')),
                "MonthStartDate" => date('Y-m-1'),
                "MonthEndDate" => date('Y-m-t')
            );
            
            $TotalPending = LogisticTransaction::getTotalRecordByStatus(0,$startDate,$toDate);
            $TotalUndelivered = LogisticTransaction::getTotalRecordByStatus(1,$startDate,$toDate);
            $TotalPartial = LogisticTransaction::getTotalRecordByStatus(2,$startDate,$toDate);
            $TotalReturned = LogisticTransaction::getTotalRecordByStatus(3,$startDate,$toDate);
            $TotalSending = LogisticTransaction::getTotalRecordByStatus(4,$startDate,$toDate);
            $TotalSent = LogisticTransaction::getTotalRecordByStatus(5,$startDate,$toDate);
            $TotalCancelled = LogisticTransaction::getTotalRecordByStatus(6,$startDate,$toDate);
            
            $TotalPendingAll = LogisticTransaction::getAllTotalRecordByStatus(0,$currentdate);
            $TotalUndeliveredAll = LogisticTransaction::getAllTotalRecordByStatus(1,$currentdate);
            $TotalPartialAll = LogisticTransaction::getAllTotalRecordByStatus(2,$currentdate);
            $TotalReturnedAll = LogisticTransaction::getAllTotalRecordByStatus(3,$currentdate);
            $TotalSendingAll = LogisticTransaction::getAllTotalRecordByStatus(4,$currentdate);
            $TotalSentAll = LogisticTransaction::getAllTotalRecordByStatus(5,$currentdate);
            $TotalCancelledAll = LogisticTransaction::getAllTotalRecordByStatus(6,$currentdate);

            $totalPendingDelay1Month = LogisticTransaction::getAllTotalDelaybyMonth(0,1);
            $totalPendingDelay2Month = LogisticTransaction::getAllTotalDelaybyMonth(0,2);
            $totalPendingDelay3Month = LogisticTransaction::getAllTotalDelaybyMonth(0,3);

            $totalReturnedDelay1Month = LogisticTransaction::getAllTotalDelaybyMonth(3,1);
            $totalReturnedDelay2Month = LogisticTransaction::getAllTotalDelaybyMonth(3,2);
            $totalReturnedDelay3Month = LogisticTransaction::getAllTotalDelaybyMonth(3,3);
            
            $totalSendingDelay1Month = LogisticTransaction::getAllTotalDelaybyMonth(4,1);
            $totalSendingDelay2Month = LogisticTransaction::getAllTotalDelaybyMonth(4,2);
            $totalSendingDelay3Month = LogisticTransaction::getAllTotalDelaybyMonth(4,3);
            
            $TotalBatchPending = LogisticBatch::getTotalRecordByStatus(0,$startDate,$toDate);
            $TotalBatchSending = LogisticBatch::getTotalRecordByStatus(1,$startDate,$toDate);
            $TotalBatchReturned = LogisticBatch::getTotalRecordByStatus(2,$startDate,$toDate);
            $TotalBatchUndelivered = LogisticBatch::getTotalRecordByStatus(3,$startDate,$toDate);
            $TotalBatchSent = LogisticBatch::getTotalRecordByStatus(4,$startDate,$toDate);
            $TotalBatchCancelled = LogisticBatch::getTotalRecordByStatus(5,$startDate,$toDate);
            
            // REGION UPDATE
             //Region       
            $TotalPendingRegion = LogisticTransaction::getTotalRecordByStatusRegion(0,$startDate,$toDate);      
            $TotalUndeliveredRegion = LogisticTransaction::getTotalRecordByStatusRegion(1,$startDate,$toDate);      
            $TotalPartialRegion = LogisticTransaction::getTotalRecordByStatusRegion(2,$startDate,$toDate);      
            $TotalReturnedRegion = LogisticTransaction::getTotalRecordByStatusRegion(3,$startDate,$toDate);     
            $TotalSendingRegion = LogisticTransaction::getTotalRecordByStatusRegion(4,$startDate,$toDate);      
            $TotalSentRegion = LogisticTransaction::getTotalRecordByStatusRegion(5,$startDate,$toDate);     
            $TotalCancelledRegion = LogisticTransaction::getTotalRecordByStatusRegion(6,$startDate,$toDate);        
            $totalPendingDelay1MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(0,1);       
            $totalPendingDelay2MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(0,2);       
            $totalPendingDelay3MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(0,3);       
                    
            $totalReturnedDelay1MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(3,1);      
            $totalReturnedDelay2MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(3,2);      
            $totalReturnedDelay3MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(3,3);      
                    
            $totalSendingDelay1MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(4,1);       
            $totalSendingDelay2MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(4,2);       
            $totalSendingDelay3MonthRegion = LogisticTransaction::getAllTotalDelaybyMonthRegion(4,3);       
            $TotalPendingAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(0,$currentdate);      
            $TotalUndeliveredAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(1,$currentdate);      
            $TotalPartialAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(2,$currentdate);      
            $TotalReturnedAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(3,$currentdate);     
            $TotalSendingAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(4,$currentdate);      
            $TotalSentAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(5,$currentdate);     
            $TotalCancelledAllRegion = LogisticTransaction::getAllTotalRecordByStatusRegion(6,$currentdate);        
            $TotalBatchPendingRegion = LogisticBatch::getTotalRecordByStatusRegion(0,$startDate,$toDate);       
            $TotalBatchSendingRegion = LogisticBatch::getTotalRecordByStatusRegion(1,$startDate,$toDate);       
            $TotalBatchReturnedRegion = LogisticBatch::getTotalRecordByStatusRegion(2,$startDate,$toDate);      
            $TotalBatchUndeliveredRegion = LogisticBatch::getTotalRecordByStatusRegion(3,$startDate,$toDate);       
            $TotalBatchSentRegion = LogisticBatch::getTotalRecordByStatusRegion(4,$startDate,$toDate);      
            $TotalBatchCancelledRegion = LogisticBatch::getTotalRecordByStatusRegion(5,$startDate,$toDate);     
            $username = Session::get('username');       
            $states = LogisticTransaction::getStates($username);
            // REGION UPDATE


            $TotalPendingAllplace = LogisticTransaction::select(
                DB::raw("count(id) as total_order"), 
                DB::raw("delivery_city"), 
                DB::raw("delivery_state"), 
                DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location"))
                    ->where('status',0)
                ->groupBy('location')
                ->get();
            
            $TotalSendingAllplace = LogisticTransaction::select(
                DB::raw("count(id) as total_order"), 
                DB::raw("delivery_city"), 
                DB::raw("delivery_state"), 
                DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location"))
                    ->where('status',4)
                ->groupBy('location')
                ->get();


            $TotalPendingAllplaceRegion = LogisticTransaction::select(                  
                DB::raw("count(id) as total_order"),        
                DB::raw("delivery_city"),       
                DB::raw("delivery_state"),      
                DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location"))       
                    ->where('status',0)     
                    ->whereIn('delivery_state',$states)     
                ->groupBy('location')       
                ->get();        
            $TotalSendingAllplaceRegion = LogisticTransaction::select(      
                DB::raw("count(id) as total_order"),        
                DB::raw("delivery_city"),       
                DB::raw("delivery_state"),      
                DB::raw("LCASE(CONCAT(delivery_addr_1,' ',delivery_addr_2,' ',delivery_city,' ',delivery_postcode,' ',delivery_state)) AS location"))       
                    ->where('status',4)     
                    ->whereIn('delivery_state',$states)     
                ->groupBy('location')       
                ->get();
            
            
            
//            $sql = " SELECT CONCAT(LT.delivery_addr_1,' ',LT.delivery_addr_2,' ',LT.delivery_city,            ' ',            LT.delivery_postcode,            ' ',            LT.delivery_state) AS Location,            count(id) as TotalOrder ,    LT.*FROM    logistic_transaction AS LTWHERE    status = 0 GROUP BY Location ";
//$TotalPendingAllplace = DB::statement($sql);
//            $TotalSendingAllplace = LogisticTransaction::where("status","=",$status)
//                ->where("insert_date","<=",$date)
//                ->count();
            
            
            
            
            $TotalBatchSentGrouping = LogisticBatch::getTotalRecordDriverSent(4,$startDate,$toDate);
            $collectionDriver = array();
            $LogisticDriver = LogisticDriver::where("is_logistic_dashboard",1)
                    ->whereNotIn("username",['admin','joshua'])->get();
                
            foreach ($LogisticDriver as $keyDriver => $valueDriver) {
                $totalSent = 0;
                $totalAssign = 0;
                $totalReturn = 0;
                
                $totalSent = LogisticBatch::getTotalStatusByDriver(4,$valueDriver->id,$startDate,$toDate);
                $totalReturn = LogisticBatch::getTotalStatusByDriver(2,$valueDriver->id,$startDate,$toDate);
                $totalAssign = LogisticBatch::getTotalDriverAssigned($valueDriver->id,$startDate,$toDate);
                
                $driver_temp = array(
                    "driver"=> $valueDriver->name,
                    "assign"=> $totalAssign,
                    "sent"=> $totalSent,
                    "return"=> $totalReturn,
                );
                array_push($collectionDriver, $driver_temp);
            }

            // UPDATE REGION
              $admin_username = Session::get('username');       
            $user = DB::table('jocom_sys_admin')->where('username','=',$admin_username)->first();       
            $region = DB::table('jocom_sys_admin_region AS JSR')        
                ->select('JSR.*')               
                ->where('JSR.sys_admin_id', $user->id)      
                ->where('JSR.status', 1)        
                ->first();      
            //Region        
            $TotalBatchSentGroupingRegion = LogisticBatch::getTotalRecordDriverSentRegion(4,$startDate,$toDate);        
            $collectionDriverRegion = array();      
            $LogisticDriverRegion = LogisticDriver::where("status",1)       
                    ->whereNotIn("username",['admin','joshua'])->where('region_id','=',$region->region_id)->get();      
            foreach ($LogisticDriverRegion as $keyDriverRegion => $valueDriverRegion) {     
                $totalSent = 0;     
                $totalAssign = 0;       
                $totalReturn = 0;       
                $totalSent = LogisticBatch::getTotalStatusByDriverRegion(4,$valueDriverRegion->id,$startDate,$toDate);      
                $totalReturn = LogisticBatch::getTotalStatusByDriverRegion(2,$valueDriverRegion->id,$startDate,$toDate);        
                $totalAssign = LogisticBatch::getTotalDriverAssignedRegion($valueDriverRegion->id,$startDate,$toDate);      
                        
                $driver_tempRegion = array(     
                    "driver"=> $valueDriverRegion->name,        
                    "assign"=> $totalAssign,        
                    "sent"=> $totalSent,        
                    "return"=> $totalReturn,        
                );      
                array_push($collectionDriverRegion, $driver_tempRegion);        
            }
            // UPDATE REGION
            
//            foreach ($TotalBatchSentGrouping as $keyDriver => $valueDriver) {
//                $driver_temp = array(
//                    "driver"=> $valueDriver->modify_by,
//                    "assign"=> $valueDriver->modify_by,
//                    "sent"=> $valueDriver->total,
//                );
//                array_push($collectionDriver, $driver_temp);
//            }
            
            
            $returnCollection = array(
                "TransactionLogistic"=>array(
                    "pending"=>$TotalPending,
                    "Undelivered"=>$TotalUndelivered,
                    "Partial"=>$TotalPartial,
                    "Returned"=>$TotalReturned,
                    "sending"=>$TotalSending,
                    "Sent"=>$TotalSent,
                    "Cancelled"=>$TotalCancelled,
                ),
                "TransactionLogisticRegion"=>array(     
                    "pending"=>$TotalPendingRegion,     
                    "Undelivered"=>$TotalUndeliveredRegion,     
                    "Partial"=>$TotalPartialRegion,     
                    "Returned"=>$TotalReturnedRegion,       
                    "sending"=>$TotalSendingRegion,     
                    "Sent"=>$TotalSentRegion,       
                    "Cancelled"=>$TotalCancelledRegion,     
                ),
                "TransactionLogisticAll"=>array(
                    "pendingPlace"=>$TotalPendingAllplace,
                    "sendingPlace"=>$TotalSendingAllplace,
                    "pending"=>$TotalPendingAll,
                    "Undelivered"=>$TotalUndeliveredAll,
                    "Partial"=>$TotalPartialAll,
                    "Returned"=>$TotalReturnedAll,
                    "sending"=>$TotalSendingAll,
                    "Sent"=>$TotalSentAll,
                    "Cancelled"=>$TotalCancelledAll,
                    "Pending1Month" => $totalPendingDelay1Month,
                    "Pending2Month" => $totalPendingDelay2Month,
                    "Pending3Month" => $totalPendingDelay3Month,
                    "Returned1Month" => $totalReturnedDelay1Month,
                    "Returned2Month" => $totalReturnedDelay2Month,
                    "Returned3Month" => $totalReturnedDelay3Month,
                    "Sending1Month" => $totalSendingDelay1Month,
                    "Sending2Month" => $totalSendingDelay2Month,
                    "Sending3Month" => $totalSendingDelay3Month
                ),
                "TransactionLogisticAllRegion"=>array(      
                    "pendingPlaceRegion"=>$TotalPendingAllplaceRegion,      
                    "sendingPlaceRegion"=>$TotalSendingAllplaceRegion,      
                    "pending"=>$TotalPendingAllRegion,      
                    "Undelivered"=>$TotalUndeliveredAllRegion,      
                    "Partial"=>$TotalPartialAllRegion,      
                    "Returned"=>$TotalReturnedAllRegion,        
                    "sending"=>$TotalSendingAllRegion,      
                    "Sent"=>$TotalSentAllRegion,        
                    "Cancelled"=>$TotalCancelledAllRegion,      
                    "Pending1Month" => $totalPendingDelay1MonthRegion,      
                    "Pending2Month" => $totalPendingDelay2MonthRegion,      
                    "Pending3Month" => $totalPendingDelay3MonthRegion,      
                    "Returned1Month" => $totalReturnedDelay1MonthRegion,        
                    "Returned2Month" => $totalReturnedDelay2MonthRegion,        
                    "Returned3Month" => $totalReturnedDelay3MonthRegion,        
                    "Sending1Month" => $totalSendingDelay1MonthRegion,      
                    "Sending2Month" => $totalSendingDelay2MonthRegion,      
                    "Sending3Month" => $totalSendingDelay3MonthRegion,      
                ),
                "batchLogistic" => array(
                    "sending"=>$TotalBatchPending,
                    "processing"=>$TotalBatchSending + $TotalBatchReturned + $TotalBatchUndelivered,
                    "completed"=>$TotalBatchSent,
                ),
                "batchLogisticRegion" => array(     
                    "sending"=>$TotalBatchPendingRegion,        
                    "processing"=>$TotalBatchSendingRegion + $TotalBatchReturnedRegion + $TotalBatchUndeliveredRegion,      
                    "completed"=>$TotalBatchSentRegion,     
                ),
                "driverBatch" => array(
                    "TotalBatchSentGrouping"=>$collectionDriver,
                ),"driverBatchRegion" => array(      
                    "TotalBatchSentGroupingRegion"=>$collectionDriverRegion,        
                ),
            );
            
            $data['TotalStatistic'] = $returnCollection;
            
            
        }catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally {
            return array(
                "respStatus" => $respStatus,
                "isError" => $isError,
                "message" => $message,
                "data" => $data
            );
        }
        
        
    }


   public function anyDashboarddayview(){

        return View::make('logistic.logistic_dashboard_dayview');
   
    }

    public function anyDashboardstatisticdayview(){
        $isError = 0;
        $respStatus = 1;
        $data = array(); 
        $status = 1;


        try{

            $TotalPending = LogisticTransaction::getTotaltranslistingbystatus(0);
            $TotalSending = LogisticTransaction::getTotaltranslistingbystatus(4);

            $TotalBatchPending1day = LogisticTransaction::getTotalbatchPending(1);
            $TotalBatchPending2day = LogisticTransaction::getTotalbatchPending(2);
            $TotalBatchPending3day = LogisticTransaction::getTotalbatchPending(3);

            
            // echo $TotalPending.'TOt';
            $TotalBatchtoday    = LogisticTransaction::getTotalbatchtoday();
            $TotalBatchPending  = LogisticTransaction::getTotalbatchbystatus(0);
            $TotalBatchSent     = LogisticTransaction::getTotalbatchbystatus(4);
            $TotalUndelivered   = $TotalBatchtoday -  $TotalBatchSent;

            $TotalBatchPending  = $TotalBatchtoday;

            // $driverresult = LogisticTransaction::getDriverTeam($status);

             $driverresult = DB::select('select LDT.driverid,LDT.team_sequence,LDT.team_bg_colorcode,LDT.seq_bg_colorcode,LD.name from logistic_driver_team LDT LEFT JOIN logistic_driver LD ON LD.id = LDT.driverid where LDT.status=1 order by LDT.team_sequence ASC');
                     
            $callarray = array();
            $calltransactions = array();

            $currentday = date('l'); 

            if($currentday == 'Monday'){
                $startdate = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
            }
            else 
            {
                $startdate = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
            }

            // print date('l',date( "Y-m-d"));
             
            foreach ($driverresult  as  $value) {

                $colorcode = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

                
                $temparray = array('driverid'   => $value->driverid,
                                   'teamseque'  => $value->team_sequence,
                                   'drivername' => $value->name,
                                   'colorlight' => $value->team_bg_colorcode,
                                   'colordark'  => $value->seq_bg_colorcode,
                             );

                array_push($callarray, $temparray);
                
                $driverid = $value->driverid;

                $result = LogisticTransaction::getBatchdetails($value->driverid);

                foreach ($result as  $row) {
                    $tstatus = 0;
                    $Transactionresult = LogisticTransaction::getTransactionID($row->logistic_id);

                    $result_driver = LogisticTransaction::getBatchIDStatus($row->logistic_id);
                    
                    $tstatus = $result_driver->status;

                    $transcolor   = '#000000';  

                    if($tstatus == 0){
                        $transcolor   = '#8A2BE2';    
                    }
                    else if($tstatus == 1){
                        $transcolor = '#000000'; 
                    }
                    else if($tstatus == 2){
                        $transcolor = '#ED2536';
                    }
                    else if($tstatus == 4){
                        $transcolor = '#458224';
                    }
                    else{
                        $transcolor = '#ED2536';
                    }

                    $tempsubarray = array('driver_id'       => $driverid,
                                          'transactionid'   => $Transactionresult->transaction_id,
                                          'transcolor'       => $transcolor,
                                          'status'       => $tstatus,
                                     );
                    array_push($calltransactions, $tempsubarray);
                }
                 $resultpending = "";
                 $resultpending = LogisticTransaction::getBatchdetailsPendingDay($value->driverid);

                 $icount = 0; 

                 foreach ($resultpending as  $rowpending) {
                    $diff = 0;
                    $transID = 0;
                    $tstatus_01 = 0;
                    $assigndate = "";

                    $Transactionresult_01 = LogisticTransaction::getTransactionID($rowpending->logistic_id);

                    $assigndate = $rowpending->assign_date;

                    $transID = $Transactionresult_01->transaction_id;
                    

                    $result_driver_01 = LogisticTransaction::getBatchIDStatus($rowpending->logistic_id);
                    
                    $tstatus_01 = $result_driver_01->status;


                    $first_date = strtotime($startdate);
                    $second_date = strtotime($assigndate);
                    $offset = $second_date-$first_date; 
                    $diff = abs(floor($offset/60/60/24));

                    if($diff ==0){
                       $diff = 1; 
                    }

                    
                    $tagend = "</span>";

                    if($diff == 1){
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' *'.$tagend;

                    } elseif ($diff == 2) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' **'.$tagend;

                    } elseif ($diff ==3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ***'.$tagend;
                    }elseif ($diff >3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ['.$diff.']'.$tagend;
                    }



                    $transcolor1   = '#000000';  

                    if($tstatus_01 == 0){
                        $transcolor1   = '#8A2BE2';    
                    }
                    else if($tstatus_01 == 1){
                        $transcolor1 = '#000000'; 
                    }
                    else if($tstatus_01 == 2){
                        $transcolor1 = '#ED2536';
                    }
                    else if($tstatus_01 == 4){
                        $transcolor1 = '#458224';
                    }
                    else{
                        $transcolor1 = '#ED2536';
                    }    


                    $tempsubarray_01 = array('driver_id'     => $driverid,
                                          'transactionid'    => $transID,
                                          'transcolor'       => $transcolor1,
                                          'status'           => $tstatus_01,
                                       );

                    array_push($calltransactions, $tempsubarray_01);

                 }


            }

             $data['TransactionLogistic'] = array(
                    "TotalBatchPending1day" => $TotalBatchPending1day,
                    "TotalBatchPending2day" => $TotalBatchPending2day,
                    "TotalBatchPending3day" => $TotalBatchPending3day,
                    "TotalPending" => $TotalPending,
                    "TotalSending" => $TotalSending,
                    "TotalBatchPending" =>  $TotalBatchPending,
                    "TotalBatchSent" => $TotalBatchSent,
                    "TotalUndelivered" => abs($TotalUndelivered),
                    "DriverDetails" => $callarray,
                    "BatchDetails"  => $calltransactions
                  
            );





        }catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally {
            return array(
                "respStatus" => $respStatus,
                "isError" => $isError,
                "message" => $message,
                "data" => $data
            );
        }


    }
    
    public function anyDashboardregion(){

        return View::make('logistic.logistic_dashboard_region');

    }

    public function anyDashboardstatisticregion(){
        $isError = 0;
        $respStatus = 1;
        $data = array(); 
        $status = 1;


        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }


        if (isset($regionid) && $regionid ==0){
            // $region = "All Region";
            $region = "HQ Region";
        }
        else{
            $resultregion = LogisticTransaction::getDriverRegionName($regionid);
            $region=$resultregion->region;
        }
       

    //  die();

        try{

            $TotalPending = LogisticTransaction::getTotaltranslistingbystatusRegion(0,$regionid,$stateName);
            $TotalSending = LogisticTransaction::getTotaltranslistingbystatusRegion(4,$regionid,$stateName);

            $TotalBatchPending1day = LogisticTransaction::getTotalbatchPendingRegion(1,$regionid,$stateName);
            $TotalBatchPending2day = LogisticTransaction::getTotalbatchPendingRegion(2,$regionid,$stateName);
            $TotalBatchPending3day = LogisticTransaction::getTotalbatchPendingRegion(3,$regionid,$stateName);


            
            // echo $TotalPending.'TOt';
            $TotalBatchtoday    = LogisticTransaction::getTotalbatchtodayRegion($regionid,$stateName);
            $TotalBatchPending  = LogisticTransaction::getTotalbatchbystatusRegion(0,$regionid,$stateName);
            $TotalBatchSent     = LogisticTransaction::getTotalbatchbystatusRegion(4,$regionid,$stateName);
            $TotalUndelivered   = $TotalBatchtoday -  $TotalBatchSent;

            $TotalBatchPending  = $TotalBatchtoday;

            // $driverresult = LogisticTransaction::getDriverTeam($status);
            if (isset($regionid) && $regionid ==0){
                $driverresult = DB::select('select LDT.driverid,LDT.team_sequence,LDT.team_bg_colorcode,LDT.seq_bg_colorcode,LD.name from logistic_driver_team LDT LEFT JOIN logistic_driver LD ON LD.id = LDT.driverid where LDT.status=1 and LD.region_id=1  order by LDT.region_id,LDT.team_sequence,LD.id ASC');
            }
            else{
             $driverresult = DB::select('select LDT.driverid,LDT.team_sequence,LDT.team_bg_colorcode,LDT.seq_bg_colorcode,LD.name from logistic_driver_team LDT LEFT JOIN logistic_driver LD ON LD.id = LDT.driverid where LDT.status=1 and LD.region_id='.$regionid.' order by LDT.region_id,LDT.team_sequence,LD.id ASC');
            }
                     
            $callarray = array();
            $calltransactions = array();

            $currentday = date('l'); 

            if($currentday == 'Monday'){
                $startdate = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
            }
            else 
            {
                $startdate = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
            }
            // $startdate = Date('Y-m-d')." 23:59:59";
            // print date('l',date( "Y-m-d"));
             
            foreach ($driverresult  as  $value) {

                $colorcode = '#' . substr(str_shuffle('ABCDEF0123456789'), 0, 6);

                
                $temparray = array('driverid'   => $value->driverid,
                                   'teamseque'  => $value->team_sequence,
                                   'drivername' => $value->name,
                                   'regionname' => $region,
                                   'colorlight' => $value->team_bg_colorcode,
                                   'colordark'  => $value->seq_bg_colorcode,
                             );

                array_push($callarray, $temparray);
                
                $driverid = $value->driverid;

                $result = LogisticTransaction::getBatchdetails($value->driverid);

                foreach ($result as  $row) {
                    $tstatus = 0;
                    $Transactionresult = LogisticTransaction::getTransactionID($row->logistic_id);

                    $result_driver = LogisticTransaction::getBatchIDStatus($row->logistic_id);
                    
                    $tstatus = $result_driver->status;

                    $transcolor   = '#000000';  

                    if($tstatus == 0){
                        $transcolor   = '#8A2BE2';    
                    }
                    else if($tstatus == 1){
                        $transcolor = '#000000'; 
                    }
                    else if($tstatus == 2){
                        $transcolor = '#ED2536';
                    }
                    else if($tstatus == 4){
                        $transcolor = '#458224';
                    }
                    else{
                        $transcolor = '#ED2536';
                    }

                    $tempsubarray = array('driver_id'       => $driverid,
                                          'transactionid'   => $Transactionresult->transaction_id,
                                          'transvalid'      => $Transactionresult->transaction_id,
                                          'transcolor'       => $transcolor,
                                          'status'       => $tstatus,
                                     );
                    array_push($calltransactions, $tempsubarray);
                }
                
                //Current date delivery


                 $resultCurrent = "";
                 $resultCurrent = LogisticTransaction::getBatchdetailsDeliveryDay($value->driverid);

                 $icount_1 = 0; 

                 foreach ($resultCurrent as  $rowCurrent) {
                    $diff_01 = 0;
                    $transID_1 = 0;
                    $tstatus_02 = 0;
                    $assigndate_01 = "";

                    $Transactionresult_02 = LogisticTransaction::getTransactionID($rowCurrent->logistic_id);

                    $assigndate_01 = $rowCurrent->assign_date;

                    $transID_1 = $Transactionresult_02->transaction_id;
                    $transID_1_01 = $Transactionresult_02->transaction_id;

                    $result_driver_02 = LogisticTransaction::getBatchIDStatus($rowCurrent->logistic_id);
                    
                    $tstatus_02 = $result_driver_02->status;


                    $first_date_01 = strtotime($startdate);
                    $second_date_01 = strtotime($assigndate_01);
                    $offset_01 = $second_date_01-$first_date_01; 
                    $diff_01 = abs(floor($offset_01/60/60/24));

                    if($diff_01 ==0){
                       $diff_01 = 1; 
                    }
                    
                    // echo $first_date_01.'-'.$second_date_01.'rowend';

                    
                    $tagend_01 = "</span>";

                    if($diff_01 == 1){
                        $tagstart_01 = "<span style=color:#ED2536>";
                        $transID_1 = $transID_1 .$tagstart_01.' *'.$tagend_01;

                    } elseif ($diff_01 == 2) {
                        $tagstart_01 = "<span style=color:#ED2536>";
                        $transID_1 = $transID_1 .$tagstart_01.' **'.$tagend_01;

                    } elseif ($diff_01 ==3) {
                        $tagstart_01 = "<span style=color:#ED2536>";
                        $transID_1 = $transID_1 .$tagstart_01.' ***'.$tagend_01;
                    }elseif ($diff_01 >3) {
                        $tagstart_01 = "<span style=color:#ED2536>";
                        $transID_1 = $transID_1 .$tagstart_01.' ['.$diff_01.']'.$tagend_01;
                    }



                    $transcolor1_01   = '#000000';  

                    if($tstatus_02 == 0){
                        $transcolor1_01   = '#8A2BE2';    
                    }
                    else if($tstatus_02 == 1){
                        $transcolor1_01 = '#000000'; 
                    }
                    else if($tstatus_02 == 2){
                        $transcolor1_01 = '#ED2536';
                    }
                    else if($tstatus_02 == 4){
                        $transcolor1_01 = '#458224';
                    }
                    else{
                        $transcolor1_01 = '#ED2536';
                    }    


                    $tempsubarray_02 = array('driver_id'     => $driverid,
                                          'transactionid'    => $transID_1,
                                          'transvalid'       => $transID_1_01,
                                          'transcolor'       => $transcolor1_01,
                                          'status'           => $tstatus_02,
                                       );

                    

                    $seek = self::seek($calltransactions,'transvalid',$transID_1_01); 


                     
                        if(!is_array($seek)) 
                        {  
                             array_push($calltransactions, $tempsubarray_02);
                        } 

                 }
                 
                 //Returned Bactch but Pending


                 $resultReturned = "";

                 $resultReturned = DB::table('logistic_transaction AS LT')
                                       ->select('LT.id','LT.transaction_id','LB.assign_date')
                                       ->leftJoin('logistic_batch AS LB','LB.logistic_id','=','LT.id')
                                       ->where('LB.driver_id','=',$value->driverid)
                                       ->where('LT.status','=',3)
                                       ->where('LB.status','=',2)
                                       ->orderby('LT.insert_date','DESC')
                                       ->get();

                 $icount_3 = 0; 


                 foreach ($resultReturned as  $rowReturned) {
                    $diff_03 = 0;
                    $transID_3 = 0;
                    $tstatus_03_01 = 0;
                    $assigndate_02 = "";

                    $validResult = LogisticTransaction::getValidTransaction($rowReturned->transaction_id);

                    if($validResult == 0){

                        // echo 'In';
                        

                        $transID_3 = $rowReturned->transaction_id;
                        $transID_1_03 = $rowReturned->transaction_id;

                        $result_driver_03 = LogisticTransaction::getBatchIDStatus($rowReturned->id);
                        
                        $assigndate_03 = $result_driver_03->assign_date;
                        
                        $tstatus_03_01 = $result_driver_03->status;

                        $startdate1 = Date('Y-m-d')." 23:59:59";
                        $first_date_03 = strtotime($startdate1);
                        $second_date_03 = strtotime($assigndate_03);
                        $offset_03 = $second_date_03-$first_date_03; 
                        $diff_03 = abs(floor($offset_03/60/60/24));

                        if($diff_03 ==0){
                           $diff_03 = 1; 
                        }

                        
                        $tagend_03 = "</span>";

                        if($diff_03 == 1){
                            $tagstart_03 = "<span style=color:#ED2536>";
                            $transID_3 = $transID_3 .$tagstart_03.' *'.$tagend_03;

                        } elseif ($diff_03 == 2) {
                            $tagstart_03 = "<span style=color:#ED2536>";
                            $transID_3 = $transID_3 .$tagstart_03.' **'.$tagend_03;

                        } elseif ($diff_03 ==3) {
                            $tagstart_03 = "<span style=color:#ED2536>";
                            $transID_3 = $transID_3 .$tagstart_03.' ***'.$tagend_03;
                        }elseif ($diff_03 >3) {
                            $tagstart_03 = "<span style=color:#ED2536>";
                            $transID_3 = $transID_3 .$tagstart_03.' ['.$diff_03.']'.$tagend_03;
                        }



                        $transcolor1_03   = '#000000';  

                        if($tstatus_03_01 == 0){
                            $transcolor1_03   = '#8A2BE2';    
                        }
                        else if($tstatus_03_01 == 1){
                            $transcolor1_03 = '#000000'; 
                        }
                        else if($tstatus_03_01 == 2){
                            $transcolor1_03 = '#ED2536';
                        }
                        else if($tstatus_03_01 == 4){
                            $transcolor1_03 = '#458224';
                        }
                        else{
                            $transcolor1_03 = '#ED2536';
                        }    


                        $tempsubarray_03 = array('driver_id'     => $driverid,
                                              'transactionid'    => $transID_3,
                                              'transvalid'       => $transID_1_03,
                                              'transcolor'       => $transcolor1_03,
                                              'status'           => $tstatus_03_01,
                                           );

                        

                        $seek_1 = self::seek($calltransactions,'transvalid',$transID_1_03); 


                         
                            if(!is_array($seek_1)) 
                            {  
                                 array_push($calltransactions, $tempsubarray_03);
                            } 
                    }

                    

                 }

                //Pending Order..... 
                
                 $resultpending = "";
                 $resultpending = LogisticTransaction::getBatchdetailsPendingDay($value->driverid);

                 $icount = 0; 

                 foreach ($resultpending as  $rowpending) {
                    $diff = 0;
                    $transID = 0;
                    $tstatus_01 = 0;
                    $assigndate = "";

                    $Transactionresult_01 = LogisticTransaction::getTransactionID($rowpending->logistic_id);

                    $assigndate = $rowpending->assign_date;

                    $transID = $Transactionresult_01->transaction_id;
                    $transIDpending = $Transactionresult_01->transaction_id;
                    

                    $result_driver_01 = LogisticTransaction::getBatchIDStatus($rowpending->logistic_id);
                    
                    $tstatus_01 = $result_driver_01->status;


                    $first_date = strtotime($startdate);
                    $second_date = strtotime($assigndate);
                    $offset = $second_date-$first_date; 
                    $diff = abs(floor($offset/60/60/24));

                    if($diff ==0){
                       $diff = 1; 
                    }

                    
                    $tagend = "</span>";

                    if($diff == 1){
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' *'.$tagend;

                    } elseif ($diff == 2) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' **'.$tagend;

                    } elseif ($diff ==3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ***'.$tagend;
                    }elseif ($diff >3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ['.$diff.']'.$tagend;
                    }



                    $transcolor1   = '#000000';  

                    if($tstatus_01 == 0){
                        $transcolor1   = '#8A2BE2';    
                    }
                    else if($tstatus_01 == 1){
                        $transcolor1 = '#000000'; 
                    }
                    else if($tstatus_01 == 2){
                        $transcolor1 = '#ED2536';
                    }
                    else if($tstatus_01 == 4){
                        $transcolor1 = '#458224';
                    }
                    else{
                        $transcolor1 = '#ED2536';
                    }    


                    $tempsubarray_01 = array('driver_id'     => $driverid,
                                          'transactionid'    => $transID,
                                          'transvalid'       => $transIDpending,
                                          'transcolor'       => $transcolor1,
                                          'status'           => $tstatus_01,
                                       );

                    array_push($calltransactions, $tempsubarray_01);

                 }


            }

             $data['TransactionLogistic'] = array( 
                    "TotalBatchPending1day" => $TotalBatchPending1day,
                    "TotalBatchPending2day" => $TotalBatchPending2day,
                    "TotalBatchPending3day" => $TotalBatchPending3day,
                    "TotalPending" => $TotalPending,
                    "TotalSending" => $TotalSending,
                    "TotalBatchPending" =>  $TotalBatchPending,
                    "TotalBatchSent" => $TotalBatchSent,
                    "TotalUndelivered" => abs($TotalUndelivered),
                    "DriverDetails" => $callarray,
                    "BatchDetails"  => $calltransactions
                  
            );





        }catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally {
            return array(
                "respStatus" => $respStatus,
                "isError" => $isError,
                "message" => $message,
                "data" => $data
            );
        }


    }  
    
    public static function seek($array,$key,$needle){ 
        $seek= 0; 
        foreach($array as $k => $v){ 
            if(array_key_exists($key, $v)){ 
                if(in_array($needle,$v)){ 
                       $seek= $array[$k]; 
                } 
               } 
        } 
        return $seek; 
    } 
    
    
    
    public function anyCouriertransaction(){

        $isError = 0;
        $respStatus = 1;
        $data = array(); 
        $calltransactions = array(); 
        $status = 1;



        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }


        if (isset($regionid) && $regionid ==0){
            $region = 1;
        }

            $currentday = date('l'); 
            if($currentday == 'Monday'){
                $startdate = Date('Y-m-d',strtotime("-3 days"))." 23:59:59";
            }
            else 
            {
                $startdate = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
            }

        // Ta Q BIN & LineClear Start 

            try{
                  $countpending = 0;
                 $resultLineclear = "";
                 $resultLineclear = LogisticTransaction::getBatchdetailsTaqbinlineclear();

                 $countpending = count($resultLineclear);

                 $icount = 0; 

                 foreach ($resultLineclear as  $rowLineclear) {
                    $diff = 0;
                    $transID = 0;
                    $tstatus_01 = 0;
                    $assigndate = "";
                    $courier = "";

                    $Transactionresult_01 = LogisticTransaction::getTransactionID($rowLineclear->logistic_id);

                    $assigndate = $rowLineclear->assign_date;
                    $courier = $rowLineclear->courier_id;

                    $transID = $Transactionresult_01->transaction_id;
                    $transIDpending = $Transactionresult_01->transaction_id;
                    

                    $result_driver_01 = LogisticTransaction::getBatchIDStatus($rowLineclear->logistic_id);
                    
                    $tstatus_01 = $result_driver_01->status;


                    $first_date = strtotime($startdate);
                    $second_date = strtotime($assigndate);
                    $offset = $second_date-$first_date; 
                    $diff = abs(floor($offset/60/60/24));

                    if($diff ==0){
                       $diff = 1; 
                    }

                    
                    $tagend = "</span>";

                    if($diff == 1){
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' *'.$tagend;

                    } elseif ($diff == 2) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' **'.$tagend;

                    } elseif ($diff ==3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ***'.$tagend;
                    }elseif ($diff >3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ['.$diff.']'.$tagend;
                    }



                    $transcolor1   = '#000000';  

                    if($tstatus_01 == 0){
                        $transcolor1   = '#8A2BE2';    
                    }
                    else if($tstatus_01 == 1){
                        $transcolor1 = '#000000'; 
                    }
                    else if($tstatus_01 == 2){
                        $transcolor1 = '#ED2536';
                    }
                    else if($tstatus_01 == 4){
                        $transcolor1 = '#458224';
                    }
                    else{
                        $transcolor1 = '#ED2536';
                    }    


                    $tempsubarray_01 = array('courier'     => $courier,
                                          'transactionid'    => $transID,
                                          'transvalid'       => $transIDpending,
                                          'transcolor'       => $transcolor1,
                                          'status'           => $tstatus_01,
                                       );

                  //  print_r($tempsubarray_01);
                  
                    array_push($calltransactions, $tempsubarray_01);

                 }
               
                
                
                 $data['TransactionLineclear'] = array( 
                    
                    "TalineDetails" => self::arr_unique($calltransactions),
                    "cpending"  =>  $countpending,
                    "regionid"  => $region
                  
            );
              //   print_r($data);

             }catch (Exception $ex) {
                $message = $ex->getMessage();
                $isError = 1;
            }finally {
                return array(
                    "respStatus" => $respStatus,
                    "isError" => $isError,
                    "message" => $message,
                    "data" => $data
                );
            }

                 // Ta Q BIN & LineClear End 

    }
    
    public static function arr_unique($arr) {
      sort($arr);
      $curr = $arr[0];
      $uni_arr[] = $arr[0];
      for($i=0; $i<count($arr);$i++){
          if($curr != $arr[$i]) {
            $uni_arr[] = $arr[$i];
            $curr = $arr[$i];
          }
      }
      return $uni_arr;
    }
    
     // Over 60 days  
    public function anyOver60daystransaction(){

        $isError = 0;
        $respStatus = 1;
        $data = array(); 
        $calltransactions = array(); 
        $status = 1;



        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }


        if (isset($regionid) && $regionid ==0){
            $region = 1;
        }

            $currentday = date('l'); 
            if($currentday == 'Monday'){
                $startdate = Date('Y-m-d',strtotime("-2 days"))." 23:59:59";
            }
            else 
            {
                $startdate = Date('Y-m-d',strtotime("-1 days"))." 23:59:59";
            }
            
            $startdate = Date('Y-m-d')." 23:59:59";

        // Over 60 days Start 

            try{
                  $countpending = 0;
                 $resultover60Days = "";
                 $resultover60Days = LogisticTransaction::getBatchdetailsover60Days();

                 $countpending = count($resultover60Days);
                //  echo '<pre>';print_r($resultover60Days); echo '</pre>';
                 $icount = 0; 

                 foreach ($resultover60Days as  $rowover60Days) {
                    $diff = 0;
                    $transID = 0;
                    $tstatus_01 = 0;
                    $assigndate = "";
                    $courier = "";

                    $Transactionresult_01 = LogisticTransaction::getTransactionID($rowover60Days->logistic_id);

                    $assigndate = $rowover60Days->assign_date;
                   
                    $transID = $Transactionresult_01->transaction_id;
                    $transIDpending = $Transactionresult_01->transaction_id;
                    

                    $result_driver_01 = LogisticTransaction::getBatchIDStatus($rowover60Days->logistic_id);
                    
                    $tstatus_01 = $result_driver_01->status;


                    $first_date = strtotime($startdate);
                    $second_date = strtotime($assigndate);
                    $offset = $second_date-$first_date; 
                    $diff = abs(floor($offset/60/60/24));

                    if($diff ==0){
                       $diff = 1; 
                    }

                    
                    $tagend = "</span>";

                    if($diff == 1){
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' *'.$tagend;

                    } elseif ($diff == 2) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' **'.$tagend;

                    } elseif ($diff ==3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ***'.$tagend;
                    }elseif ($diff >=7) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ['.$diff.']'.$tagend;
                    }



                    $transcolor1   = '#000000';  

                    if($tstatus_01 == 0){
                        $transcolor1   = '#8A2BE2';    
                    }
                    else if($tstatus_01 == 1){
                        $transcolor1 = '#000000'; 
                    }
                    else if($tstatus_01 == 2){
                        $transcolor1 = '#ED2536';
                    }
                    else if($tstatus_01 == 4){
                        $transcolor1 = '#458224';
                    }
                    else{
                        $transcolor1 = '#ED2536';
                    }    

                    if ($diff >=7) {
                    $tempsubarray_01 = array('courier'     => $courier,
                                          'transactionid'    => $transID,
                                          'transvalid'       => $transIDpending,
                                          'transcolor'       => $transcolor1,
                                          'status'           => $tstatus_01,
                                       );

                  //  print_r($tempsubarray_01);

                    array_push($calltransactions, $tempsubarray_01);
                    }

                 }

                 $data['TransactionLineclear'] = array( 
                    
                    "Over60daysDetails" => self::arr_unique($calltransactions),
                    "cpending60"  =>  $countpending,
                    "regionid"  => $region
                  
            );
              //   print_r($data);

             }catch (Exception $ex) {
                $message = $ex->getMessage();
                $isError = 1;
            }finally {
                return array(
                    "respStatus" => $respStatus,
                    "isError" => $isError,
                    "message" => $message,
                    "data" => $data
                );
            }

                 // Over 60 days End 

    }
    
    public function anyReturnedtransaction(){
        $isError = 0;
        $respStatus = 1;
        $data = array(); 
        $calltransactions = array(); 
        $status = 1;



        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }


        if (isset($regionid) && $regionid ==0){
            $region = 1;
        }

            
             $startdate = Date('Y-m-d')." 23:59:59";
             
        // Over 60 days Start 

            try{
                 $countreturned = 0;
                 $resultoReturned = "";
                 $resultoReturned = LogisticTransaction::getTransactionreturned(Session::get('username'));

                 $countreturned = count($resultoReturned);

                 $icount = 0; 

                 foreach ($resultoReturned as  $rowreturned) {
                    $diff = 0;
                    $transID = 0;
                    $tstatus_01 = 0;
                    $assigndate = "";

                    $Transactionresult_01 = LogisticTransaction::getTransactionID($rowreturned->logistic_id);
                    $batchresult_01 = LogisticTransaction::getBatchIDStatus($rowreturned->id);
                     
                    $assigndate = $batchresult_01->assign_date;
                   
                    $transID = $rowreturned->transaction_id;
                    

                    $first_date = strtotime($startdate);
                    $second_date = strtotime($assigndate);
                    $offset = $second_date-$first_date; 
                    // echo $assigndate.'-'.abs(floor($offset/60/60/24)).'<br>';
                    $diff = abs(floor($offset/60/60/24));

                    if($diff ==0){
                       $diff = 1; 
                    }
                     
                    $tagend = "</span>";

                    if($diff == 1){
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' *'.$tagend;

                    } elseif ($diff == 2) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' **'.$tagend;

                    } elseif ($diff ==3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ***'.$tagend;
                    }elseif ($diff >3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ['.$diff.']'.$tagend;
                    }



                    $transcolor1   = '#FF0000';  
   

                    
                    $tempsubarray_01 = array(
                                          'transactionid'    => $transID,
                                          'transcolor'       => $transcolor1,
                                       );

                  //  print_r($tempsubarray_01);

                    array_push($calltransactions, $tempsubarray_01);
                    

                 }

                 $data['TransactionReturned'] = array( 
                    
                    "ReturnedDetails" => self::arr_unique($calltransactions),
                    "creturned"  =>  $countreturned,
                    "regionid"  => $region
                  
            );
              //   print_r($data);

             }catch (Exception $ex) {
                $message = $ex->getMessage();
                $isError = 1;
            }finally {
                return array(
                    "respStatus" => $respStatus,
                    "isError" => $isError,
                    "message" => $message,
                    "data" => $data
                );
            }

    }
    
    public function anyPartialsenttransaction(){
        $isError = 0;
        $respStatus = 1;
        $data = array(); 
        $calltransactions = array(); 
        $status = 1;



        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";
        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;
            $State = State::getStateByRegion($value);
            foreach ($State as $keyS => $valueS) {
                $stateName[] = $valueS->name;
            }
        }


        if (isset($regionid) && $regionid ==0){
            $region = 1;
        }

            
             $startdate = Date('Y-m-d')." 23:59:59";
             
        // Over 60 days Start 

            try{
                 $countpartialsent = 0;
                 $resultoPartialSent = "";
                 $resultoPartialSent = LogisticTransaction::getTransactionpartialsent(Session::get('username'));

                 $countpartialsent = count($resultoPartialSent);

                 $icount = 0; 

                 foreach ($resultoPartialSent as  $rowpartialsent) {
                    $diff = 0;
                    $transID = 0;
                    $tstatus_01 = 0;
                    $assigndate = "";

                    $Transactionresult_01 = LogisticTransaction::getTransactionID($rowpartialsent->logistic_id);

                    $assigndate = $rowpartialsent->insert_date;
                   
                    $transID = $rowpartialsent->transaction_id;
                    

                    $first_date = strtotime($startdate);
                    $second_date = strtotime($assigndate);
                    $offset = $second_date-$first_date; 
                    $diff = abs(floor($offset/60/60/24));

                    if($diff ==0){
                       $diff = 1; 
                    }
                     
                    $tagend = "</span>";

                    if($diff == 1){
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' *'.$tagend;

                    } elseif ($diff == 2) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' **'.$tagend;

                    } elseif ($diff ==3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ***'.$tagend;
                    }elseif ($diff >3) {
                        $tagstart = "<span style=color:#ED2536>";
                        $transID = $transID .$tagstart.' ['.$diff.']'.$tagend;
                    }



                    $transcolor1   = '#FF0000';  
   

                    
                    $tempsubarray_01 = array(
                                          'transactionid'    => $transID,
                                          'transcolor'       => $transcolor1,
                                       );

                  //  print_r($tempsubarray_01);

                    array_push($calltransactions, $tempsubarray_01);
                    

                 }

                 $data['TransactionPartialSent'] = array( 
                    
                    "PartialSentDetails" => self::arr_unique($calltransactions),
                    "cpartialsent"  =>  $countpartialsent,
                    "regionid"  => $region
                  
            );
              //   print_r($data);

             }catch (Exception $ex) {
                $message = $ex->getMessage();
                $isError = 1;
            }finally {
                return array(
                    "respStatus" => $respStatus,
                    "isError" => $isError,
                    "message" => $message,
                    "data" => $data
                );
            }

    }
    
    public function anyUnassignedtransaction(){
        $isError = 0;
        $respStatus = 1;
        $transdata = array(); 
        $rdata     = array();  
        $tdata     = array();

        $status = 1;

        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";

        
        $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
        $stateName = array();

        foreach ($SysAdminRegion as  $value) {
            $regionid = $value;

            if (isset($regionid) && $regionid ==0){
                $State = State::getStateByCountry($MalaysiaCountryID);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }else{
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }

        }


        try{
                switch ($regionid) {
                     case '1':
                         $resultregion = LogisticTransaction::getDriverRegionName(1);
                         $region=$resultregion->region;
                         
                         $countpending = 0;
                         $resultpending = LogisticTransaction::getTransactionpending($stateName);
                         $countpending = count($resultpending);
                                foreach ($resultpending as $tvalue) {

                                    $temparray =  array('transactionid' => $tvalue->transaction_id, 
                                                        'regionid'      => $resultregion->id,
                                                        );
                                    array_push($transdata, $temparray);
                                }

                         $arrayName = array('regionname' => $region, 
                                            'countpending'  =>  $countpending,
                                            'regionid' => $resultregion->id,); 
                         array_push($rdata, $arrayName);  


                         break;

                     case '2':
                         $resultregion = LogisticTransaction::getDriverRegionName(2);
                         $region=$resultregion->region;

                         $countpending = 0;
                         $resultpending = LogisticTransaction::getTransactionpending($stateName);
                         $countpending = count($resultpending);
                                foreach ($resultpending as $tvalue) {

                                    $temparray =  array('transactionid' => $tvalue->transaction_id, 
                                                        'regionid'      => $resultregion->id,
                                                        );
                                    array_push($transdata, $temparray);
                                }   

                         $arrayName = array('regionname' => $region,
                                            'countpending'  =>  $countpending,
                                            'regionid' => $resultregion->id, ); 
                         array_push($rdata, $arrayName); 

                         break;

                     case '3':
                         $resultregion = LogisticTransaction::getDriverRegionName(3);
                         $region=$resultregion->region;
                          
                            $countpending = 0;
                            $resultpending = LogisticTransaction::getTransactionpending($stateName);
                            $countpending = count($resultpending);
                                foreach ($resultpending as $tvalue) {

                                    $temparray =  array('transactionid' => $tvalue->transaction_id, 
                                                        'regionid'      => $resultregion->id,
                                                        );
                                    array_push($transdata, $temparray);
                                }  
                        $arrayName = array('regionname' => $region,
                                            'countpending'  =>  $countpending,
                                            'regionid' => $resultregion->id, ); 
                         array_push($rdata, $arrayName); 

                         break;
                     
                     default:
                         $rowarray = array();

                         $result = DB::table('jocom_region')
                                    ->orderby('id','ASC')
                                    ->get();  

                         foreach ($result as $rvalue) {
                            $region = $rvalue->region;
                            $rid    = $rvalue->id;      
                            

                            unset($stateName);
                            $State = State::getStateByRegion($rid);
                            foreach ($State as $keyS => $valueS) {
                                $stateName[] = $valueS->name;
                            }
                            $countpending = 0;
                            unset($temparray);
                            $resultpending = LogisticTransaction::getTransactionpending($stateName);
                            $countpending = count($resultpending);
                                foreach ($resultpending as $tvalue) {

                                    $temparray =  array('transactionid' => $tvalue->transaction_id, 
                                                        'regionid'      => $rid, 
                                                        );
                                    array_push($transdata, $temparray);
                                }  

                         $arrayName = array('regionname' => $region,
                                             'countpending'  =>  $countpending,
                                                'regionid' => $rid, ); 
                            array_push($rdata, $arrayName); 
                               
                               // die();

                          } 

                          // print_r($transdata);

                         # code...
                         break;
                 } 
                 // die();

            $tdata["Transactionpending"] = array('Regionlist'       => $rdata,
                                                 'Transactinlist'   => $transdata
                                             );

            // echo '<pre>';
            // print_r($tdata);
            // echo '</pre>';

        }catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally {
            return array(
                "respStatus" => $respStatus,
                "isError" => $isError,
                "message" => $message,
                "tdata" => $tdata
            );
        }    


    }

    public function anyDashboarddriverbatch(){
        $isError = 0;
        $respStatus = 1;
        $driverdata = array(); 
        $status = 1;


        try{
            $driverid = Input::get($driverid);
            $calltransactions  = array();

            $result = LogisticTransaction::getBatchdetails($driverid);

                foreach ($result as  $row) {
                    $tstatus = 0;
                    $Transactionresult = LogisticTransaction::getTransactionID($row->logistic_id);
                    
                    $tstatus = $row->status;
                    // print 'TransID-'.$Transactionresult->transaction_id.'-'.$tstatus;
                    $transcolor   = '#000000';  

                    if($tstatus == 0){
                        $transcolor   = '#000000';    
                    }
                    else if($tstatus == 1){
                        $transcolor = '#434a54';
                    }
                    else if($tstatus == 4){
                        $transcolor = '#458224';
                    }
                    else{
                        $transcolor = '#ED2536';
                    }

                    $tempsubarray = array('driver_id'       => $row->driver_id,
                                          'transactionid'   => $Transactionresult->transaction_id,
                                          'transcolor'       => $transcolor,
                                     );
                    array_push($calltransactions, $tempsubarray);
                }

                $driverdata['TransactionLogisticdriver'] = array(
                    "BatchDetailsNew"  => $calltransactions
                  
            );



            }catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally {
            return array(
                "respStatus" => $respStatus,
                "isError" => $isError,
                "message" => $message,
                "driverdata" => $driverdata
            );
        }

    }
    
    /* GENERATE ASSIGN REPORT : OPEN */
    
      public function anyAssigned(){
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->where("status",1)->first();
        
        if($SysAdminRegion->region_id == 0){
            $driver = DB::table('logistic_driver')->where('status',1)->select('id','name')->get();
        }else{
         $driver = DB::table('logistic_driver')->where('region_id', $SysAdminRegion->region_id)->where('status',1)->select('id','name')->get();   
        }

        return View::make('report.assigned_report')->with('driver', $driver);
    }
    
    
    public function anyAssignedprescan(){
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->where("status",1)->first();
        
        if($SysAdminRegion->region_id == 0 || $SysAdminRegion->region_id == 1){
            $driver = DB::table('logistic_driver')->where('status',1)->select('id','name')->get();
        }else{
         $driver = DB::table('logistic_driver')->where('region_id', $SysAdminRegion->region_id)->where('status',1)->where('username','prescan')->select('id','name')->get();   
        }

        return View::make('report.assignedprescan_report')->with('driver', $driver);
    }
    
    

    // public function anyAssignedreport(){

    //     $transaction_from = Input::get('transaction_from');
    //     $transaction_to = Input::get('transaction_to');
    //     $driver = Input::get('driver');

    //     $driver_details = LogisticDriver::find($driver);

    //         $logistic = DB::table('logistic_batch AS LB')
    //                 ->leftJoin('logistic_batch_item AS LBI','LB.id','=','LBI.batch_id')
    //                 ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
    //                 ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
    //                 ->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
    //                 ->where('LB.driver_id','=',$driver)
    //                 ->where('LB.assign_date', '>=',$transaction_from)
    //                 ->where('LB.assign_date','<=',$transaction_to)
    //                 ->whereIn('LB.status',[0,1])
    //                 ->select(DB::raw("LTI.name,LTI.label, SUM(LBI.qty_assign) as 'qty_assign',Count(LT.transaction_id) as 'id_count'"))
    //                 ->groupBy('JP.sku')
    //                 ->orderBy('id_count','Desc')
    //                 ->get();

    //         $logistic2 = DB::table('logistic_batch AS LB')
    //                 ->leftJoin('logistic_batch_item AS LBI','LB.id','=','LBI.batch_id')
    //                 ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
    //                 ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
    //                 ->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
    //                 ->where('LB.driver_id','=',$driver)
    //                 ->where('LB.assign_date', '>=',$transaction_from)
    //                 ->where('LB.assign_date','<=',$transaction_to)
    //                 ->whereIn('LB.status',[0,1])
    //                 ->select(DB::raw("LTI.name,LTI.label, GROUP_CONCAT(LBI.qty_assign SEPARATOR ',') as 'qty_assign',GROUP_CONCAT(LT.transaction_id SEPARATOR ',') as 'transaction_id',SUM(LBI.qty_assign) as 'total', Count(LT.transaction_id) as 'id_count'"))
    //                 ->groupBy('JP.sku')
    //                 ->orderBy('id_count','Desc')
    //                 ->get();
  
    //         $data = array('logistic' =>$logistic,
    //                     'logistic2' =>$logistic2,
    //                     'transaction_from'=>$transaction_from,
    //                     'transaction_to'=>$transaction_to,
    //                     'driver_name' => $driver_details->name,
    //                     );

    //         if (!empty($logistic)) {

    //             return Excel::create('assigned('.$transaction_from.'- '.$transaction_to.')', function($excel) use ($data) {
    //                 $excel->sheet('Assigned Batch', function($sheet) use ($data)
    //                 {
    //                     $sheet->loadView('report.assignedtable', array('data'=>$data));
    //                 });

    //                 $excel->sheet('Batch Item', function($sheet) use ($data)
    //                 {   
    //                     $sheet->loadView('report.assignedtable2', array('data'=>$data));
    //                     $sheet->setOrientation('landscape');

    //                 });

    //             })->download('xls');

    //         }else{
    //             return Redirect::to('jlogistic/assigned')->with('message', 'Sorry. No data found!');
    //         }

    // }
    
    public function anyAssignedreport(){
        
        try{
            
        // echo "MODULE IN UNDER MAINTENANCE";

        $transaction_from = Input::get('transaction_from') .' 00:00:00';
        $transaction_to = Input::get('transaction_to') .' 23:59:59';
        $driver = Input::get('driver');

        $driver_details = LogisticDriver::find($driver);
        
        // print_r(Input::all());
        // die();
        
        $collectionData = array();

        $logistic2 = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_batch_item AS LBI','LB.id','=','LBI.batch_id')
                    ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
                    ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
                    ->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'LTI.product_price_id')
                    ->where('LB.driver_id','=',$driver)
                    ->where('LB.assign_date', '>=',$transaction_from)
                    ->where('LB.assign_date','<=',$transaction_to)
                    ->whereIn('LB.status',[0,1,2,4])
                    ->select("JP.sku",DB::raw("JP.shortname,JP.name,JP.id,LTI.label,LTI.product_price_id, GROUP_CONCAT(LBI.qty_assign SEPARATOR ',') as 'qty_assign',GROUP_CONCAT(LT.transaction_id SEPARATOR ',') as 'transaction_id',SUM(LBI.qty_assign) as 'total', Count(LT.transaction_id) as 'id_count'"))
                    ->groupBy('LTI.product_price_id')
                    ->orderBy('id_count','Desc') //do not modify this
                    ->get();
                    
                //  echo "<pre>";
                //     print_r($logistic2);
                //     echo "</pre>";
                        

            foreach ($logistic2 as $key => $value) {
                
                //  echo "<pre>";
                //     print_r($value);
                //     echo "</pre>";
                    
               // echo "TRANSACTION_ID".$value->transaction_id;

                $base = DB::table('jocom_product_base_item')
                    ->where("product_id",$value->id)
                    ->where("price_option_id",$value->product_price_id) // ADDED: WIRA
                    ->where("status",1);
                    
                
                    
                $baseList = $base;
                $baseListData = $base->get();
                
              
                
                $baseListId = $baseList->lists('product_base_id');
               
                // echo "<pre>";
                // print_r($baseListId);
                // echo "</pre>";
              
                $product = DB::table('jocom_products')->whereIn('id', $baseListId)->select('shortname', 'name', 'id', 'sku')->get(); // amended by wira add quantity
                
                // echo "TRANSACTION_ID :".$value->transaction_id;
                //if((string)$value->transaction_id = 150551){
                 
                    // echo "<pre>";
                    // print_r($baseListData);
                    // echo "</pre>";
                    // echo "<pre>";
                    // print_r($baseListId);
                    // echo "</pre>";
                    
                    // echo "<pre>";
                    // print_r($product);
                    // echo "</pre></br>";
                    
               // }
                
                
                
                if (count($product)>0) {
                    
                    $baseCounter = 0;
                    foreach ($product as $value3) {
                        
                        // $baseTotalQty = $baseListData[$baseCounter]->quantity;
                        
                        //$baseTotalQty = $baseListData[$baseCounter]->quantity;
                        
                        foreach ($baseListData as $kbd => $vbd) {
                            if($vbd->product_base_id == $value3->id){
                                $baseTotalQty = $vbd->quantity;
                                //echo "BASE QTY:".$vbd->quantity; 
                            }
                        }
                            
                       
                        $productPrice = DB::table('jocom_product_price')
                            ->where('product_id', $value3->id)
                            ->where('default', 1)
                            ->first(); // amended by wira add quantity
                            
                   
                        if ($value3->shortname!='') {
                           $name =  $value3->shortname;
                        }else{
                            $name = $value3->name;
                        }
                        
                        if ($productPrice->alternative_label_name !='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                        }
                         
                        // rearrange qty
                        //  array_push($collectionData, (object)array(
                        // array_push($collectionData[$value3->sku], array(
                        //     "product_sku" => $value3->sku ,
                        //     "qty_assign" => $value->qty_assign ,
                        //     "label_name" => $label_name ,
                        //     "transaction_id" =>$value->transaction_id,
                        //     "total" => $value->total,
                        //     "id_count" => $value->id_count,
                        //     "base_product" => $name,
                        //     "base_quantity" => $baseTotalQty,
                        //     ));
                    
                        // FIXING PART //  
                        
                        $assignCollection = explode(",",$value->qty_assign);
                        $assignQTY = '';
                        
                        foreach ($assignCollection as $assign){
                            $assignQTY[] = $assign * $baseTotalQty;
                        }
                        
                        $assignQTY =  implode(",",$assignQTY);
                //         echo "<pre>";
                // print_r($value3->sku);
                // echo "</pre>";
                        
                        if (array_key_exists($value3->sku, $collectionData)) {
                            // echo "HERE";
                            $collectionData[$value3->sku]['qty_assign'] = $collectionData[$value3->sku]['qty_assign'].",".$assignQTY;
                            $collectionData[$value3->sku]['qty'] = $collectionData[$value3->sku]['qty'].",".$value->qty_assign;
                            $collectionData[$value3->sku]['transaction_id'] = $collectionData[$value3->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value3->sku]['total'] = $collectionData[$value3->sku]['total'] + $value->total;
                            $collectionData[$value3->sku]['id_count'] = $collectionData[$value3->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            
                        }else{
                //           echo "HERE99";
                //                   echo "<pre>";
                //                   print_r($value3->sku);
                // print_r($value->transaction_id);
                // echo "</pre>";
                            $setCol = array(
                                "product_sku" => $value3->sku ,
                                "qty" => $value->qty_assign ,
                                "qty_assign" => $assignQTY, //$value->qty_assign ,
                                "label_name" => $label_name ,
                                "transaction_id" =>$value->transaction_id,
                                "total" => $value->total,
                                "id_count" => $value->id_count,
                                "base_product" => $name,
                                "base_quantity" => $baseTotalQty,
                            );
                        
                            
                            
                            $collectionData[$value3->sku] = $setCol;
                            
                            //       echo "<pre>";
                            //  echo "HERE2";
                            // print_r($value3->sku);
                            //     print_r($collectionData[$value3->sku]);
                            // echo "HERE2".$value->transaction_id;
                            //     echo "</pre>";
                            
                        }
                        
                        
                        
                            
                        // FIXING PART //       
                            
                        $baseCounter++;
                          
                    }  
                    
          
                }else{
                     
                    $productPrice = DB::table('jocom_product_price')
                            ->where("id",$value->product_price_id)
                            ->first(); // amended by wira add quantity

                    if ($value->shortname!='') {
                           $name =  $value->shortname;
                    }else{
                            $name = $value->name;
                    }
                    
                    if ($productPrice->alternative_label_name!='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                    }
                    
                    if (array_key_exists($value->sku, $collectionData)) {
                      
                        
                            // echo "THIS IS ID ". $collectionData[$value->sku]['transaction_id'];
                            // echo $value->sku;
                            // echo $value->transaction_id;
                            // echo "HERE3";
                      
                            
                            $collectionData[$value->sku]['qty_assign'] = $collectionData[$value->sku]['qty_assign'].",".$value->qty_assign;
                            $collectionData[$value->sku]['transaction_id'] = $collectionData[$value->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value->sku]['total'] = $collectionData[$value->sku]['total'] + $value->total;
                            $collectionData[$value->sku]['id_count'] = $collectionData[$value->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            //  echo "<pre>";
                            //     print_r($collectionData[$value->sku]);
                            //     echo "</pre>";
                            // echo "<pre>";
                            // print_r( $collectionData[$value3->sku]);
                            // echo "</pre>";
                            
                    }else{
                        // echo "HERE4";
                        // echo "<pre>";
                        // print_r($value->sku);
                        // print_r($value->transaction_id);
                        // echo "</pre>";
                        $collectionData[$value->sku] = array(
                            "is_not_base" => true,
                            "shortname" => $name,
                            "label_name" => $label_name,
                            "qty_assign" => $value->qty_assign,
                            "transaction_id" =>$value->transaction_id,
                            "total" => $value->total,
                            "id_count" => $value->id_count,
                        );
                        
                    }
                        
                    // array_push($collectionData, (object)array(
                    //     "shortname" => $name,
                    //     "label_name" => $label_name,
                    //     "qty_assign" => $value->qty_assign,
                    //     "transaction_id" =>$value->transaction_id,
                    //     "total" => $value->total,
                    //     "id_count" => $value->id_count,
                    //     ));

                }
                
            }

            $data = array(
                    'logistic2' =>$collectionData,
                    'transaction_from'=>$transaction_from,
                    'transaction_to'=>$transaction_to,
                    'driver_name' => $driver_details->name,
                    );
                    
            // echo "<pre>";
            // print_r($data['logistic2']);
            // echo "</pre>";
            // die();
        
            // echo "<pre>";
            // print_r($data['logistic2']);
            // echo "</pre>";
            
            // $numbers = array();
            //     foreach ($data['logistic2'] as $key => $value) { 

            //             $numbers[] = $value['id_count'];
            //             $trans = $value['transaction_id'];
            //             $translist = explode(",", $trans);
                        
            //             $qty = $value['qty_assign'];
            //             $qtylist = explode(",", $qty);
                    
            //         }
            //         print_r($numbers);
            //         $max = max($numbers);
            //         echo $max;
                    
                    
            //         $min = $max - $value['id_count']; 
                    
            //         foreach ($translist as $valuetrans) { 
            //             echo $valuetrans;
            //         }
                    
            //         for ($i=0; $i<$min  ; $i++) {
            //           echo "EMPTY";
            //         }
            //         echo "IN TESTING";
            // die();

            if (!empty($logistic2)) {

                return Excel::create('assigned('.$transaction_from.'- '.$transaction_to.')', function($excel) use ($data) {
   
                    $excel->sheet('Batch Item', function($sheet) use ($data)
                    {   
                        $sheet->loadView('report.assignedtable3', array('data'=>$data));
                        $sheet->setOrientation('landscape');

                    });

                })->download('xls');

            }else{
                return Redirect::to('jlogistic/assigned')->with('message', 'Sorry. No data found!');
            }
            
        }catch(Exception $ex){
            
            echo $ex->getMessage();
        }

    }
    
    public function anyAssignedprescanreport(){
        
        try{
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        // echo "MODULE IN UNDER MAINTENANCE";

        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');
        $driver = Input::get('driver');

        $driver_details = LogisticDriver::find($driver);

        $collectionData = array();

        $logistic2 = DB::table('logistic_batch_prescan AS LB')
                    ->leftJoin('logistic_batch_item_prescan AS LBI','LB.id','=','LBI.batch_id')
                    ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
                    ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
                    ->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'LTI.product_price_id')
                    ->where('LB.driver_id','=',$driver)
                    ->where('LB.assign_date', '>=',$transaction_from)
                    ->where('LB.assign_date','<=',$transaction_to)
                    ->whereIn('LB.status',[0,1])
                    ->select("JP.sku",DB::raw("JP.shortname,JP.name,JP.id,LTI.label,LTI.product_price_id, GROUP_CONCAT(LBI.qty_assign SEPARATOR ',') as 'qty_assign',GROUP_CONCAT(LT.transaction_id SEPARATOR ',') as 'transaction_id',SUM(LBI.qty_assign) as 'total', Count(LT.transaction_id) as 'id_count'"))
                    ->groupBy('LTI.product_price_id')
                    ->orderBy('id_count','Desc') //do not modify this
                    ->get();
                    
            // echo $transaction_from . $transaction_to.$driver "<pre>";
            //         print_r($logistic2);
            //         echo "</pre>";       
            // die();
            foreach ($logistic2 as $key => $value) {
                
                //  echo "<pre>";
                //     print_r($value);
                //     echo "</pre>";
                    
               // echo "TRANSACTION_ID".$value->transaction_id;

                $base = DB::table('jocom_product_base_item')
                    ->where("product_id",$value->id)
                    ->where("price_option_id",$value->product_price_id) // ADDED: WIRA
                    ->where("status",1);
                    
                
                    
                $baseList = $base;
                $baseListData = $base->get();
                
              
                
                $baseListId = $baseList->lists('product_base_id');
               
                // echo "<pre>";
                // print_r($baseListId);
                // echo "</pre>";
              
                $product = DB::table('jocom_products')->whereIn('id', $baseListId)->select('shortname', 'name', 'id', 'sku')->get(); // amended by wira add quantity
                
                // echo "TRANSACTION_ID :".$value->transaction_id;
                //if((string)$value->transaction_id = 150551){
                 
                    // echo "<pre>";
                    // print_r($baseListData);
                    // echo "</pre>";
                    // echo "<pre>";
                    // print_r($baseListId);
                    // echo "</pre>";
                    
                    // echo "<pre>";
                    // print_r($product);
                    // echo "</pre></br>";
                    
               // }
                
                
                
                if (count($product)>0) {
                    
                    $baseCounter = 0;
                    foreach ($product as $value3) {
                        
                        // $baseTotalQty = $baseListData[$baseCounter]->quantity;
                        
                        //$baseTotalQty = $baseListData[$baseCounter]->quantity;
                        
                        foreach ($baseListData as $kbd => $vbd) {
                            if($vbd->product_base_id == $value3->id){
                                $baseTotalQty = $vbd->quantity;
                                //echo "BASE QTY:".$vbd->quantity; 
                            }
                        }
                            
                       
                        $productPrice = DB::table('jocom_product_price')
                            ->where('product_id', $value3->id)
                            ->where('default', 1)
                            ->first(); // amended by wira add quantity
                            
                   
                        if ($value3->shortname!='') {
                           $name =  $value3->shortname;
                        }else{
                            $name = $value3->name;
                        }
                        
                        if ($productPrice->alternative_label_name !='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                        }
                         
                        // rearrange qty
                        //  array_push($collectionData, (object)array(
                        // array_push($collectionData[$value3->sku], array(
                        //     "product_sku" => $value3->sku ,
                        //     "qty_assign" => $value->qty_assign ,
                        //     "label_name" => $label_name ,
                        //     "transaction_id" =>$value->transaction_id,
                        //     "total" => $value->total,
                        //     "id_count" => $value->id_count,
                        //     "base_product" => $name,
                        //     "base_quantity" => $baseTotalQty,
                        //     ));
                    
                        // FIXING PART //  
                        
                        $assignCollection = explode(",",$value->qty_assign);
                        $assignQTY = '';
                        
                        foreach ($assignCollection as $assign){
                            $assignQTY[] = $assign * $baseTotalQty;
                        }
                        
                        $assignQTY =  implode(",",$assignQTY);
                //         echo "<pre>";
                // print_r($value3->sku);
                // echo "</pre>";
                        
                        if (array_key_exists($value3->sku, $collectionData)) {
                            // echo "HERE";
                            $collectionData[$value3->sku]['qty_assign'] = $collectionData[$value3->sku]['qty_assign'].",".$assignQTY;
                            $collectionData[$value3->sku]['qty'] = $collectionData[$value3->sku]['qty'].",".$value->qty_assign;
                            $collectionData[$value3->sku]['transaction_id'] = $collectionData[$value3->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value3->sku]['total'] = $collectionData[$value3->sku]['total'] + $value->total;
                            $collectionData[$value3->sku]['id_count'] = $collectionData[$value3->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            
                        }else{
                //           echo "HERE99";
                //                   echo "<pre>";
                //                   print_r($value3->sku);
                // print_r($value->transaction_id);
                // echo "</pre>";
                            $setCol = array(
                                "product_sku" => $value3->sku ,
                                "qty" => $value->qty_assign ,
                                "qty_assign" => $assignQTY, //$value->qty_assign ,
                                "label_name" => $label_name ,
                                "transaction_id" =>$value->transaction_id,
                                "total" => $value->total,
                                "id_count" => $value->id_count,
                                "base_product" => $name,
                                "base_quantity" => $baseTotalQty,
                            );
                        
                            
                            
                            $collectionData[$value3->sku] = $setCol;
                            
                            //       echo "<pre>";
                            //  echo "HERE2";
                            // print_r($value3->sku);
                            //     print_r($collectionData[$value3->sku]);
                            // echo "HERE2".$value->transaction_id;
                            //     echo "</pre>";
                            
                        }
                        
                        
                        
                            
                        // FIXING PART //       
                            
                        $baseCounter++;
                          
                    }  
                    
          
                }else{
                     
                    $productPrice = DB::table('jocom_product_price')
                            ->where("id",$value->product_price_id)
                            ->first(); // amended by wira add quantity

                    if ($value->shortname!='') {
                           $name =  $value->shortname;
                    }else{
                            $name = $value->name;
                    }
                    
                    if ($productPrice->alternative_label_name!='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                    }
                    
                    if (array_key_exists($value->sku, $collectionData)) {
                      
                        
                            // echo "THIS IS ID ". $collectionData[$value->sku]['transaction_id'];
                            // echo $value->sku;
                            // echo $value->transaction_id;
                            // echo "HERE3";
                      
                            
                            $collectionData[$value->sku]['qty_assign'] = $collectionData[$value->sku]['qty_assign'].",".$value->qty_assign;
                            $collectionData[$value->sku]['transaction_id'] = $collectionData[$value->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value->sku]['total'] = $collectionData[$value->sku]['total'] + $value->total;
                            $collectionData[$value->sku]['id_count'] = $collectionData[$value->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            //  echo "<pre>";
                            //     print_r($collectionData[$value->sku]);
                            //     echo "</pre>";
                            // echo "<pre>";
                            // print_r( $collectionData[$value3->sku]);
                            // echo "</pre>";
                            
                    }else{
                        // echo "HERE4";
                        // echo "<pre>";
                        // print_r($value->sku);
                        // print_r($value->transaction_id);
                        // echo "</pre>";
                        $collectionData[$value->sku] = array(
                            "is_not_base" => true,
                            "shortname" => $name,
                            "label_name" => $label_name,
                            "qty_assign" => $value->qty_assign,
                            "transaction_id" =>$value->transaction_id,
                            "total" => $value->total,
                            "id_count" => $value->id_count,
                        );
                        
                    }
                        
                    // array_push($collectionData, (object)array(
                    //     "shortname" => $name,
                    //     "label_name" => $label_name,
                    //     "qty_assign" => $value->qty_assign,
                    //     "transaction_id" =>$value->transaction_id,
                    //     "total" => $value->total,
                    //     "id_count" => $value->id_count,
                    //     ));

                }
                
            }

            $data = array(
                    'logistic2' =>$collectionData,
                    'transaction_from'=>$transaction_from,
                    'transaction_to'=>$transaction_to,
                    'driver_name' => $driver_details->name,
                    );
                    
            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            // die();
        
            // echo "<pre>";
            // print_r($data['logistic2']);
            // echo "</pre>";
            
            // $numbers = array();
            //     foreach ($data['logistic2'] as $key => $value) { 

            //             $numbers[] = $value['id_count'];
            //             $trans = $value['transaction_id'];
            //             $translist = explode(",", $trans);
                        
            //             $qty = $value['qty_assign'];
            //             $qtylist = explode(",", $qty);
                    
            //         }
            //         print_r($numbers);
            //         $max = max($numbers);
            //         echo $max;
                    
                    
            //         $min = $max - $value['id_count']; 
                    
            //         foreach ($translist as $valuetrans) { 
            //             echo $valuetrans;
            //         }
                    
            //         for ($i=0; $i<$min  ; $i++) {
            //           echo "EMPTY";
            //         }
            //         echo "IN TESTING";
            // die();

            if (!empty($logistic2)) {

                return Excel::create('assigned('.$transaction_from.'- '.$transaction_to.')', function($excel) use ($data) {
   
                    $excel->sheet('Batch Item', function($sheet) use ($data)
                    {   
                        $sheet->loadView('report.assignedtable3', array('data'=>$data));
                        $sheet->setOrientation('landscape');
                        $sheet->getPageSetup()->setFitToWidth(1);
                        $sheet->getPageSetup()->setFitToHeight(2);

                    });

                })->download('xls');

            }else{
                return Redirect::to('jlogistic/assigned')->with('message', 'Sorry. No data found!');
            }
            
        }catch(Exception $ex){
            
            echo $ex->getMessage();
        }

    }
    
    public function anyAssignedreport2(){
        
        try{
            
        // echo "MODULE IN UNDER MAINTENANCE";

        // $transaction_from = Input::get('transaction_from');
        // $transaction_to = Input::get('transaction_to');
        // $driver = Input::get('driver');
        
        
        $transaction_from = '2019-05-11 00:00:00';
        $transaction_to = '2019-05-11 23:59:59';
        $driver = 8;

        $driver_details = LogisticDriver::find($driver);

        $collectionData = array();

        $logistic2 = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_batch_item AS LBI','LB.id','=','LBI.batch_id')
                    ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
                    ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
                    ->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'LTI.product_price_id')
                    ->where('LB.driver_id','=',$driver)
                    ->where('LB.assign_date', '>=',$transaction_from)
                    ->where('LB.assign_date','<=',$transaction_to)
                    //->whereIn('LB.status',[0,1])
                    ->select("JP.sku",DB::raw("JP.shortname,JP.name,JP.id,LTI.label,LTI.product_price_id, GROUP_CONCAT(LBI.qty_assign SEPARATOR ',') as 'qty_assign',GROUP_CONCAT(LT.transaction_id SEPARATOR ',') as 'transaction_id',SUM(LBI.qty_assign) as 'total', Count(LT.transaction_id) as 'id_count'"))
                    ->groupBy('LTI.product_price_id')
                    ->orderBy('id_count','Desc') //do not modify this
                    ->get();
                    
            // echo "<pre>";
            // print_r($logistic2);
            // echo "</pre>";
            // die();

            foreach ($logistic2 as $key => $value) {
                
                //  echo "<pre>";
                //     print_r($value);
                //     echo "</pre>";
                    
               // echo "TRANSACTION_ID".$value->transaction_id;

                $base = DB::table('jocom_product_base_item')
                    ->where("product_id",$value->id)
                    ->where("price_option_id",$value->product_price_id) // ADDED: WIRA
                    ->where("status",1);
                    
                
                    
                $baseList = $base;
                $baseListData = $base->get();
                
              
                
                $baseListId = $baseList->lists('product_base_id');
               
                // echo "<pre>";
                // print_r($baseListId);
                // echo "</pre>";
              
                $product = DB::table('jocom_products')->whereIn('id', $baseListId)->select('shortname', 'name', 'id', 'sku')->get(); // amended by wira add quantity
                
                // echo "TRANSACTION_ID :".$value->transaction_id;
                //if((string)$value->transaction_id = 150551){
                 
                    // echo "<pre>";
                    // print_r($baseListData);
                    // echo "</pre>";
                    // echo "<pre>";
                    // print_r($baseListId);
                    // echo "</pre>";
                    
                    // echo "<pre>";
                    // print_r($product);
                    // echo "</pre></br>";
                    
               // }
                
                
                
                if (count($product)>0) {
                    
                    $baseCounter = 0;
                    foreach ($product as $value3) {
                        
                        // $baseTotalQty = $baseListData[$baseCounter]->quantity;
                        
                        //$baseTotalQty = $baseListData[$baseCounter]->quantity;
                        
                        foreach ($baseListData as $kbd => $vbd) {
                            if($vbd->product_base_id == $value3->id){
                                $baseTotalQty = $vbd->quantity;
                                //echo "BASE QTY:".$vbd->quantity; 
                            }
                        }
                            
                       
                        $productPrice = DB::table('jocom_product_price')
                            ->where('product_id', $value3->id)
                            ->where('default', 1)
                            ->first(); // amended by wira add quantity
                            
                   
                        if ($value3->shortname!='') {
                           $name =  $value3->shortname;
                        }else{
                            $name = $value3->name;
                        }
                        
                        if ($productPrice->alternative_label_name !='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                        }
                         
                        // rearrange qty
                        //  array_push($collectionData, (object)array(
                        // array_push($collectionData[$value3->sku], array(
                        //     "product_sku" => $value3->sku ,
                        //     "qty_assign" => $value->qty_assign ,
                        //     "label_name" => $label_name ,
                        //     "transaction_id" =>$value->transaction_id,
                        //     "total" => $value->total,
                        //     "id_count" => $value->id_count,
                        //     "base_product" => $name,
                        //     "base_quantity" => $baseTotalQty,
                        //     ));
                    
                        // FIXING PART //  
                        
                        $assignCollection = explode(",",$value->qty_assign);
                        $assignQTY = '';
                        
                        foreach ($assignCollection as $assign){
                            $assignQTY[] = $assign * $baseTotalQty;
                        }
                        
                        $assignQTY =  implode(",",$assignQTY);
                //         echo "<pre>";
                // print_r($value3->sku);
                // echo "</pre>";
                        
                        if (array_key_exists($value3->sku, $collectionData)) {
                            // echo "HERE";
                            $collectionData[$value3->sku]['qty_assign'] = $collectionData[$value3->sku]['qty_assign'].",".$assignQTY;
                            $collectionData[$value3->sku]['qty'] = $collectionData[$value3->sku]['qty'].",".$value->qty_assign;
                            $collectionData[$value3->sku]['transaction_id'] = $collectionData[$value3->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value3->sku]['total'] = $collectionData[$value3->sku]['total'] + $value->total;
                            $collectionData[$value3->sku]['id_count'] = $collectionData[$value3->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            
                        }else{
                //           echo "HERE99";
                //                   echo "<pre>";
                //                   print_r($value3->sku);
                // print_r($value->transaction_id);
                // echo "</pre>";
                            $setCol = array(
                                "product_sku" => $value3->sku ,
                                "qty" => $value->qty_assign ,
                                "qty_assign" => $assignQTY, //$value->qty_assign ,
                                "label_name" => $label_name ,
                                "transaction_id" =>$value->transaction_id,
                                "total" => $value->total,
                                "id_count" => $value->id_count,
                                "base_product" => $name,
                                "base_quantity" => $baseTotalQty,
                            );
                        
                            
                            
                            $collectionData[$value3->sku] = $setCol;
                            
                            //       echo "<pre>";
                            //  echo "HERE2";
                            // print_r($value3->sku);
                            //     print_r($collectionData[$value3->sku]);
                            // echo "HERE2".$value->transaction_id;
                            //     echo "</pre>";
                            
                        }
                        
                        
                        
                            
                        // FIXING PART //       
                            
                        $baseCounter++;
                          
                    }  
                    
          
                }else{
                     
                    $productPrice = DB::table('jocom_product_price')
                            ->where("id",$value->product_price_id)
                            ->first(); // amended by wira add quantity

                    if ($value->shortname!='') {
                           $name =  $value->shortname;
                    }else{
                            $name = $value->name;
                    }
                    
                    if ($productPrice->alternative_label_name!='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                    }
                    
                    if (array_key_exists($value->sku, $collectionData)) {
                      
                        
                            // echo "THIS IS ID ". $collectionData[$value->sku]['transaction_id'];
                            // echo $value->sku;
                            // echo $value->transaction_id;
                            // echo "HERE3";
                      
                            
                            $collectionData[$value->sku]['qty_assign'] = $collectionData[$value->sku]['qty_assign'].",".$value->qty_assign;
                            $collectionData[$value->sku]['transaction_id'] = $collectionData[$value->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value->sku]['total'] = $collectionData[$value->sku]['total'] + $value->total;
                            $collectionData[$value->sku]['id_count'] = $collectionData[$value->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            //  echo "<pre>";
                            //     print_r($collectionData[$value->sku]);
                            //     echo "</pre>";
                            // echo "<pre>";
                            // print_r( $collectionData[$value3->sku]);
                            // echo "</pre>";
                            
                    }else{
                        // echo "HERE4";
                        // echo "<pre>";
                        // print_r($value->sku);
                        // print_r($value->transaction_id);
                        // echo "</pre>";
                        $collectionData[$value->sku] = array(
                            "is_not_base" => true,
                            "shortname" => $name,
                            "label_name" => $label_name,
                            "qty_assign" => $value->qty_assign,
                            "transaction_id" =>$value->transaction_id,
                            "total" => $value->total,
                            "id_count" => $value->id_count,
                        );
                        
                    }
                        
                    // array_push($collectionData, (object)array(
                    //     "shortname" => $name,
                    //     "label_name" => $label_name,
                    //     "qty_assign" => $value->qty_assign,
                    //     "transaction_id" =>$value->transaction_id,
                    //     "total" => $value->total,
                    //     "id_count" => $value->id_count,
                    //     ));

                }
                
            }

            $data = array(
                    'logistic2' =>$collectionData,
                    'transaction_from'=>$transaction_from,
                    'transaction_to'=>$transaction_to,
                    'driver_name' => $driver_details->name,
                    );
                    
            // echo "<pre>";
            // print_r($data);
            // echo "</pre>";
            // die();
        
            // echo "<pre>";
            // print_r($data['logistic2']);
            // echo "</pre>";
            
            // $numbers = array();
            //     foreach ($data['logistic2'] as $key => $value) { 

            //             $numbers[] = $value['id_count'];
            //             $trans = $value['transaction_id'];
            //             $translist = explode(",", $trans);
                        
            //             $qty = $value['qty_assign'];
            //             $qtylist = explode(",", $qty);
                    
            //         }
            //         print_r($numbers);
            //         $max = max($numbers);
            //         echo $max;
                    
                    
            //         $min = $max - $value['id_count']; 
                    
            //         foreach ($translist as $valuetrans) { 
            //             echo $valuetrans;
            //         }
                    
            //         for ($i=0; $i<$min  ; $i++) {
            //           echo "EMPTY";
            //         }
            //         echo "IN TESTING";
            // die();

            if (!empty($logistic2)) {

                return Excel::create('assigned('.$transaction_from.'- '.$transaction_to.')', function($excel) use ($data) {
   
                    $excel->sheet('Batch Item', function($sheet) use ($data)
                    {   
                        $sheet->loadView('report.assignedtable3', array('data'=>$data));
                        $sheet->setOrientation('landscape');

                    });

                })->download('xls');

            }else{
                //return Redirect::to('jlogistic/assigned')->with('message', 'Sorry. No data found!');
            }
            
        }catch(Exception $ex){
            
            echo $ex->getMessage();
        }

    }

    public function anyAssignedtable(){

        return View::make('report.assignedtable');
    }

    public function anyAssignedtable2(){

        return View::make('report.assignedtable2');
    }
    
        /* GENERATE ASSIGN REPORT : CLOSE */

    public function anyTracking(){
        
        return View::make('logistic.logistic_tracking_dashboard');
        
    }

    public function anyActivedrivers(){
        $isError = 0;
        $respStatus = 1;

        $data = array();
        $calldata = array();

        $MalaysiaCountryID = 458;
        $regionid = 0;
        $region = "";

        try{
        
            $SysAdminRegion = UserController::getSysRegionList(Session::get('username'));
            $stateName = array();

            foreach ($SysAdminRegion as  $value) {
                $regionid = $value;
                $State = State::getStateByRegion($value);
                foreach ($State as $keyS => $valueS) {
                    $stateName[] = $valueS->name;
                }
            }


            if (isset($regionid) && $regionid ==0){
                $DriversResult = LogisticDriver::where('is_logistic_dashboard','=',1)
                                           ->where('status','=',1)
                                           ->orderBy('username','ASC')
                                           ->get();

            }
            else{
                $DriversResult = LogisticDriver::where('is_logistic_dashboard','=',1)
                                           ->where('status','=',1)
                                           ->where('region_id','=', $regionid)
                                           ->orderBy('username','ASC')
                                           ->get();

            }


            foreach ($DriversResult  as $key => $value) {
                $totalbatch = 0;
                $totalsent = 0;
                $totalpending = 0; 

                $totalbatch = LogisticTransaction::getTotaldailybatch($value->id);
                $totalsent  = LogisticTransaction::getTotaldailybatchsent($value->id);

                if($totalbatch != 0){

                    $totalpending = $totalbatch - $totalsent;

                    $temparray = array('driver_id'  => $value->id, 
                                       'name'       => $value->name, 
                                       'totalbatch' => $totalbatch,
                                       'totalsent'  => $totalsent,
                                       'totalpending'  => $totalpending,
                                     );
                    array_push($calldata, $temparray);
                    
                }

                

                
            }

           $data['driverdata'] =  array('driverdetails' => $calldata, ); 

        
        }catch (Exception $ex) {
            $message = $ex->getMessage();
            $isError = 1;
        }finally {
            return array(
                "respStatus" => $respStatus,
                "isError" => $isError,
                "message" => $message,
                "data" => $data
            );
        }


    }
    
    
    
    /*
     * OPEN
     * Desc : International Logistic Listing
     */
    
    public function anyInternationallogistic(){
        
        
        try{
            
            return View::make('logistic.logistic_international_listing');
            
        } catch (Exception $ex) {

        } 
            
    }
    
    public function anyManifests() {
        
        // Get Orders
                
       $tasks = DB::table('jocom_international_logistic_manifest AS JILM')->select(array(
           'JILM.id','JILM.created_at','JILM.manifest_id','JILM.country_id','JC.name'
       ))
       ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JILM.country_id')
       ->where('JILM.activation', '=',1)
       ->orderBy('JILM.id','ASC');

       return Datatables::of($tasks)->make(true);
        
    }
    
    public function anyInternationallogisticlist($type) {
        
        // Get Orders
        $user_id = Session::get("user_id");
        
        switch ($type) {
            case 1:
                
                 $tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.id',
                    'JIL.reference_number',
                    'JIL.manifest_id',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JIL.transaction_id',
                    'JT.delivery_name',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
                    'JIL.status',
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->where('JIL.status', '=',1)
                ->orderBy('JIL.id','ASC');

                break;
            
            case 2:
                
                 $tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.id',
                    'JIL.reference_number',
                    'JIL.manifest_id',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JIL.transaction_id',
                    'JT.delivery_name',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                    
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
                    'JIL.status',
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->where('JIL.status', '=',2)
                ->orderBy('JIL.id','ASC');

                break;
            
            case 3:
                
                $tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.id',
                    'JIL.reference_number',
                    'JIL.manifest_id',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JIL.transaction_id',
                    'JT.delivery_name',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
                    'JIL.weight',
                    'JIL.status',
                     
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->where('JIL.status', '=',3)
                ->orderBy('JIL.id','ASC');

                break;
            
            case 4:
                
                $tasks = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.id',
                    'JIL.reference_number',
                    'JIL.manifest_id',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JIL.transaction_id',
                    'JT.delivery_name',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
                    'JIL.weight',
                    'JIL.status',
                     
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->whereIn('JIL.status', [4,5])
                ->orderBy('JIL.id','ASC');

                break;
      
            default:
                break;
        }
        
       

        return Datatables::of($tasks)->make(true);
        
    }
    
    /*
     * Desc : To verify/confirmed delivery for new international delivery
     */
    
    public function anyVerifiedlogistic() {


        $data = array();
        $RespStatus = 1; 
        $message = "Updated!";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            $selectedIDs = Input::get("items");
            $action = Input::get("action");
            
//            echo "<pre>";
//            print_r($selectedIDs);
//            echo "</pre>";
            
            switch ($action) {
                case 2: // Verify New Order

                    $updateStatus = self::verifyInternationDelivery($selectedIDs,$action);
                    break;
                
                case 4: // Set as shipped

                    $updateStatus = self::verifyInternationDelivery($selectedIDs,$action);
                    break;
                
                case 5: // Set as delivered

                    $updateStatus = self::verifyInternationDelivery($selectedIDs,$action);
                    break;
                
                default:
                    break;
            }
            
            if(!$updateStatus){
                throw new exception("Ape lancau xupdate doh!");
            }
            
        
        } catch (Exception $ex) {
            
            $is_error = true;
            $message = $ex->getMessage();
        
            
        } finally {
            
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    private static function verifyInternationDelivery($selectedIDs,$action){
        
        
        if(count($selectedIDs) > 0 ){
            
            if($action == 5){
                
                foreach($selectedIDs as $value){
                    
                   
                    $InternationalLogistic = InternationalLogistic::find($value);
                    $InternationalLogistic->status = $action;
                    $InternationalLogistic->updated_by = Session::get("username");
                    $InternationalLogistic->save();
                  
                    
                    LogisticBatch::UpdateBatch($InternationalLogistic->batch_id, array("status"=>4));
                    
                }
                
                
                // DB::table('jocom_international_logistic')
                //     ->whereIn("id",$selectedIDs)
                //     ->update(
                //             ['status' => $action,
                //             'updated_at' => DATE("Y-m-d h:i:s"),
                //             'updated_by' => Session::get("username")]
                //     );
                
            }else{
                
                DB::table('jocom_international_logistic')
                    ->whereIn("id",$selectedIDs)
                    ->update(
                            ['status' => $action,
                            'updated_at' => DATE("Y-m-d h:i:s"),
                            'updated_by' => Session::get("username")]
                    );
                
            }
            
            
            return true;
            
        } else{
            return false ;
        }
        
    }
    
    
    public function anyConfirmedweight() {


        $data = array();
        $RespStatus = 1; 
        $message = "Weight Updated!";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            $itemID = Input::get("item");
            $total = Input::get("total");
            
            $running_number = DB::table('jocom_running')
                        ->select('*')
                        ->where('value_key', '=', 'inter_label')->first();
            
            $ReferenceNo = "JCM".str_pad($running_number->counter + 1,9,"0",STR_PAD_LEFT);
            $NewRunner = Running::find($running_number->id);
            $NewRunner->counter = $running_number->counter + 1;
            $NewRunner->save();
            
            $updateWeight = DB::table('jocom_international_logistic')->where("id",$itemID)->update(
                    ['weight' => $total,
                        'reference_number' => $ReferenceNo,
                        'status' => 3,
                        'updated_at' => DATE("Y-m-d h:i:s"),
                        'updated_by' => Session::get("username")
                    ]);
            
            
        } catch (Exception $ex) {
            
            $is_error = true;
            $message = $ex->getMessage();
            
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    
    /*
     * Desc : View label from
     */
    public function anyViewlabel($deliveryID){
        
        
        
        $isError = 0;
        $message = "";
        $code = $code;
        $loopEmpty = 7;
        
        
        
        
        $d = new DNS1D();
        $d->setStorPath(__DIR__."/cache/");
       
        $collectionData = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.*',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JT.delivery_name',
                    'JT.delivery_contact_no',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
                     
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->where('JIL.id', '=',$deliveryID)->first();
        
//           echo "<pre>";
//        print_r($collectionData);
//        echo "</pre>";
        
        $collectionTotalData = DB::table('jocom_international_logistic_item')->where("jocom_international_logistic_id",$collectionData->id)->count();
        $collectionItems = DB::table('jocom_international_logistic_item')->where("jocom_international_logistic_id",$collectionData->id)->get();
        
        try{
            $general = [
                "barcode"=> '<img src="data:image/png;base64,' . $d->getBarcodePNG(strtoupper($collectionData->reference_number), "C39E",1.8,70) . '" alt="barcode"   />', 
                "data_header"=> $collectionData, 
                "data_items"=> $collectionItems, 
            ];
            

//        
        $loopEmpty = $loopEmpty - $collectionTotalData;

        } catch (Exception $ex) {
            
            $isError = 1;
            
        }finally{

            $data = array(
                    "error" => $isError,
                    "data" => $general,
                    "message" => $message,
                    "totalLoopEmpty" => $loopEmpty
            );

            $pdf = PDF::loadView('emails.international_logistic_lable', $data);
            return $pdf->stream('invoice.pdf');
        }
             
    }
    
    
    public function anyDownloadmanifestbyid($manifestNumber){
        
        
        $collectionData = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.*',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JT.delivery_name',
                    'JT.delivery_contact_no',
                    'JT.delivery_identity_number',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' ),
                    'JILI.*' 
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->leftJoin('jocom_international_logistic_item AS JILI', 'JILI.jocom_international_logistic_id', '=', 'JIL.id')
                ->where('JIL.manifest_id', '=',$manifestNumber)->get();
//        
//        echo "<pre>";
//        print_r($collectionData);
//        echo "</pre>";
//      
        $currentRefNumber = 0;
        $indexCounter = 0;
        
        $DataList = array();
        
        foreach ($collectionData as $key => $value) {
        
            // New Item 
            if($value->reference_number !== $currentRefNumber){
            
                $currentRefNumber = $value->reference_number;
                $indexCounter++;
                
                $sublineArray = array(
                    "index" => $indexCounter,
                    "reference_number" => $value->reference_number,
                    "description" => $value->product_name,
                    "brand" => '',
                    "specification" => $value->product_label,
                    "model" => $value->Model,
                    "no_of_pcs" => $value->no_of_pcs,
                    "content_pcs" => $value->content_of_pcs,
                    "unit" => $value->quantity,
                    "weight" => $value->weight,
                    "value" => $value->value,
                    "recipient_name" => $value->delivery_name,
                    "recipient_id_number" => $value->delivery_identity_number,
                    "delivery_address" => $value->FullAddress,
                    "contact_number" => $value->delivery_contact_no
                );
                
            }else{
                
                $sublineArray = array(
                    "index" => '',
                    "reference_number" => '',
                    "description" => $value->product_name,
                    "brand" => '',
                    "specification" => $value->product_label,
                    "model" => $value->Model,
                    "no_of_pcs" => $value->no_of_pcs,
                    "content_pcs" => $value->content_of_pcs,
                    "unit" => $value->quantity,
                    "weight" => $value->weight,
                    "value" => $value->value,
                    "recipient_name" => '',
                    "recipient_id_number" => '',
                    "delivery_address" => '',
                    "contact_number" => '',
                );
                
            }
            
            array_push($DataList, $sublineArray);
            
        }

        
        return Excel::create($manifestNumber, function($excel) use ($DataList) {
                    
            $excel->sheet('MANIFEST', function($sheet) use ($DataList)
            {
                $sheet->loadView('manifest.CHN', array('data' =>$DataList));
            });
            
        })->download('xls');
        

        
    }
    
    
    public function anyViewdetailslogistic($id){
        
        try{
            
        
        
        $collectionData = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.*',
                    'JT.buyer_username',
                    'JT.transaction_date',
                    'JT.delivery_name',
                    'JT.delivery_contact_no',
                    'JT.delivery_addr_1',
                    'JT.delivery_addr_2',
                    'JT.delivery_city',
                    'JT.delivery_state',
                    'JT.delivery_postcode',
                    'JT.delivery_country',
                     DB::raw('CONCAT(JT.delivery_addr_1," ",JT.delivery_addr_1," ",JT.delivery_city," ",JT.delivery_postcode," ",JT.delivery_state," ",JT.delivery_country) AS FullAddress' )
                ))
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JIL.transaction_id')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JIL.deliver_to_country')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JIL.batch_id')
                ->leftJoin('jocom_delivery_order AS JDO', 'JDO.id', '=', 'JIL.order_request_id')
                ->where('JIL.id', '=',$id)->first();
                
                
            $collectionItems = DB::table('jocom_international_logistic_item')->where("jocom_international_logistic_id",$collectionData->id)->get();
            
            $data=  array(
                "headerInfo" => $collectionData,
                "itemsInfo" => $collectionItems
                );
                
            echo "<pre>";
            print_r($data);
            echo "</pre>";
                
        }catch(Exception $e) {
          echo 'Message: ' .$e->getMessage();
        }
        
        
    }
    
    
    /*
     * CLOSE
     * Desc : International Logistic Listing
     */
     
    public function anyMacrolinkdomesticlabel($id){
        

        $isError = 0;
        $message = "";
        $code = $code;
        $loopEmpty = 5;
        
        
        $d = new DNS1D();
        $d->setStorPath(__DIR__."/cache/");
        
        $collectionData = array();
        $collectionItems = array();
        
        $courierOrderData = DB::table('jocom_courier_orders AS JCO')
                ->where("JCO.id",$id)
                ->first();
        
        $batchInfo = DB::table('logistic_batch AS LB')
                ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
                ->where("LB.id",$courierOrderData->batch_id)
                ->first();
        
//        echo "<pre>";
//        print_r($courierOrderData);
//        echo "</pre>";
//        


        $collectionData = DB::table('jocom_transaction AS JT')
                ->where("JT.id",$batchInfo->transaction_id)
                ->first();
        
        $collectionItems = DB::table('logistic_transaction_item AS LTI')
                ->select("JTD.*")
                ->leftJoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'LTI.transaction_item_id')
                ->where("LTI.id",$courierOrderData->transaction_item_logistic_id)
                ->get();

        $collectionTotalData = DB::table('logistic_transaction_item AS LTI')
                ->leftJoin('jocom_transaction_details AS JTD', 'JTD.id', '=', 'LTI.transaction_item_id')
                ->where("LTI.id",$courierOrderData->transaction_item_logistic_id)
                ->count();
  
        $reference_number = "JDM".str_pad($courierOrderData->transaction_item_logistic_id,9,"0",STR_PAD_LEFT);
       
        try{
            $general = [
                "barcode"=> '<img src="data:image/png;base64,' . $d->getBarcodePNG(strtoupper($reference_number), "C39E",1.8,70) . '" alt="barcode"   />', 
                "data_header"=> $collectionData, 
                "data_items"=> $collectionItems, 
                "reference_number"=> $reference_number, 
            ];
            
       
        $loopEmpty = $loopEmpty - $collectionTotalData;

        } catch (Exception $ex) {
            
            $isError = 1;
            
        }finally{

            $data = array(
                    "error" => $isError,
                    "data" => $general,
                    "message" => $message,
                    "totalLoopEmpty" => $loopEmpty
            );

            $pdf = PDF::loadView('emails.domestic_logistic_label', $data);
            return $pdf->stream('invoice.pdf');
        }
             
    }
     public function getDownload($attachment)
    {
        
                                
     $pathToFile = public_path('logistic/images/'.$attachment);
     
    
     
     if(is_file($pathToFile)) {
            return Response::download($pathToFile);
        }
        else {
            echo "<br>File not exists!";
        }
                             
           
     
        // view("files.download", compact('$download'));
    }

    public function anyDrivertimesheetlist() {
        return View::make('logistic.driver.timesheet', ['logistic'=> $logistic]); 
    }

    public function anyDrivertimesheetajax() {
        $list = DB::table('logistic_driver_time_sheet')->select('id', 'created_at');

        return Datatables::of($list)
                ->edit_column('created_at', function($a) {
                    return date_format(date_create($a->created_at), 'Y-m-d');
                })
                ->add_column('file_name', function($a) {
                    $file_name = 'timesheet_'.strtotime($a->created_at).'.pdf';
                    return $file_name;
                })
                ->add_column('Action', function ($a) {
                            $file = $a->id;
                            $encrypted = Crypt::encrypt($file);
                            $encrypted = urlencode(base64_encode($encrypted));

                            return '<a class="btn btn-success" title="" data-toggle="tooltip" href="/jlogistic/drivertimesheetdownload/'.$encrypted.'" target="_blank"><i class="fa fa-download"></i></a>';
                        })
                ->make(true);
    }

    public function anyDrivertimesheetdownload($loc) {
        $loc = base64_decode(urldecode($loc));
        $id = Crypt::decrypt($loc);
        
        $file_name = explode("/", $file_path);
        $file_name = $file_name[3];

        $timesheet = DB::table('logistic_driver_time_sheet')->where('id', '=', $id)->first();
        $data = json_decode($timesheet->data);

        $file_path = Config::get('constants.DRIVER_TIME_SHEET');
        $file_name = 'timesheet_'.strtotime($timesheet->created_at).'.pdf';

        include app_path('library/html2pdf/html2pdf.class.php');

        $date = date_format(date_create($timesheet->created_at), 'd/m/Y');
        $response =  View::make('logistic.driver.timesheet_view')
                        ->with('data', $data)
                        ->with('date', $date);

        $html2pdf = new HTML2PDF('P', 'A4', 'en', true, 'UTF-8');      
        $html2pdf->WriteHTML($response);
        $html2pdf->Output($file_path .'/'. $file_name, 'F');

        $headers = array(
            'Content-Type' => 'application/pdf',
        );

        return Response::download($file_path .'/'. $file_name, $file_name, $headers);
    }

    public function anyDeleteattachment($id)
    {
        $attachment = DB::table('logistic_batch_attachment')
                        ->where('id', $id)
                        ->first();

        if (DB::table('logistic_batch_attachment')->where('id', $id)->delete()) {
            unlink($attachment->path_to_file . '/' . $attachment->attachment);
            return Response::json(['status' => 1]);
        }
        
        return Response::json(['status' => 0]);
    }
    
    public function anyManuallazada(){
        
        // $dateyday= date("Y-m-d", strtotime("yesterday")); 
        // die();
            
        $SuccesTransList = array(

4449











 





                );
                
            
        foreach ($SuccesTransList as $value) {        
    
        $logisticid = $value; 
        $logistcstatus = 5;
        
        $result = DB::table('logistic_transaction')->selectRaw('id')
                        ->where('transaction_id','=',$value)->first();
        
        $response = LazadaController::sofdelivered($result->id);
        
        echo $value.'<br>';
        $lazada_arr = array($logisticid, $logistcstatus);
        }
        // print_r($response);
        
        echo 'Done'; 
        

    }
    
    public function anyStatuslazada(){
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 27200);
        // die();
        $SuccesTransList = DB::table('logistic_transaction')->selectRaw('id')
                                ->where('buyer_email','=','lazada@tmgrocer.com')
                                ->where('status','=', 5)
                                ->where('modify_date','>=','2025-10-02 00:00:00')
                                ->where('modify_date','<=','2025-10-02 23:59:59')
                                // ->where('modify_date','LIKE','%2022-09-23%')
                                // ->whereIn('modify_by',['whadmin','supervisor','joel','asif','xylee','cocoyeo','joseph','ylng','whlee','kana','ejane','[CMS]amyng','alisupervisor','jiasin','allan','[CMS]xylee','[CMS]joseph','[CMS]asif'])
                                // ->whereIn('modify_by',['[CMS]amyng'])
                                ->get();
        //  echo '<pre>';       
        //  print_r($SuccesTransList);  
        //  echo '</pre>';
        //  die();
        foreach ($SuccesTransList as $key => $value) {        
    
        $logisticid = $value->id; 
        $logistcstatus = 5;
        echo $value;
        $response = LazadaController::sofdelivered($value->id);
        // $response = self::sofdelivered($logisticid);
        echo $logisticid .'<br>';
        $lazada_arr = array($logisticid, $logistcstatus);
        }
        // print_r($lazada_arr);
        
        

        echo 'Done'; 
        

    }
    
    public function sofdelivered($logistic_id){
        
        $data = array();
        $order_item_id = array(); 
        
        $isError = false;

        try{
            
        DB::beginTransaction();

        if($logistic_id > 0){
            
            $logisticdata = DB::table('logistic_transaction')->selectRaw('status,transaction_id')
                                ->where('buyer_email','=','lazada@tmgrocer.com')
                                ->where('status','=',5)
                                ->where('id','=',$logistic_id)
                                ->first();
            
                                
            if(count($logisticdata)>0){
                
                
                $orderdata = DB::table('jocom_lazada_order')->selectRaw('order_number,from_account')->where('transaction_id','=',$logisticdata->transaction_id)->first();
                
                if(count($orderdata)>0){
                    
                    $ordernumber = $orderdata->order_number;
                    $accountType = $orderdata->from_account;
                    // LAZADA DBS Start 
                        $lazadaapires = DB::table('jocom_lazada_order_items AS LOI')
                            ->select('LOI.order_items_details')
                            ->leftjoin('jocom_lazada_order as LO','LO.id','=','LOI.order_id')
                            ->where('LO.order_number','=',$ordernumber)
                            ->get();
        
        
                        foreach($lazadaapires as $value){
                            //$data[] = array('order_items_details' => $row['order_items_details']);
                            $data= json_decode($value->order_items_details);
                           
                            array_push($order_item_id,$data->order_item_id);
                        }
                        
                        //  print_r($order_item_id);
                        //  echo json_encode($order_item_id);
                        //   die("ok");
                        // $accountType = 2;
                        
                        // Define Credential key for lazada API
                            switch ($accountType) {
                                case 1:
                                    $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                case 2:
                                    $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                case 3:
                                    $app_key = Config::get('constants.LAZADA_ETIKA_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_ETIKA_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                case 4:
                                    $app_key = Config::get('constants.LAZADA_YEOS_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_YEOS_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                case 5:
                                    $app_key = Config::get('constants.LAZADA_STARBUCKS_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_STARBUCKS_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                case 6:
                                    $app_key = Config::get('constants.LAZADA_POKKA_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_POKKA_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                case 7:
                                    $app_key = Config::get('constants.LAZADA_EBFROZEN_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_EBFROZEN_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                case 8:
                                    $app_key = Config::get('constants.LAZADA_EVERBEST_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_EVERBEST_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                    break;
                                // case 9:
                                //     $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                                //     $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                                //     $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                
                                //     break;
                                    
                                default:
                
                                    $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                                    $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                                    break;
                            }
                        
                        
                         // Get LAZADA Authentication Token
                            if(strtolower(Config::get('constants.ENVIRONMENT')) == "live"){
                                $url = 'https://api.jocom.com.my/lazada/getauthtoken';
                            }else{
                                $url = 'https://uat.all.jocom.com.my/lazada/getauthtoken';
                            }
                            
                            // echo 'In_1';
                            // print_r($fields_string);
                            // die();
                            $ch = curl_init();
                            curl_setopt($ch, CURLOPT_URL, $url);
                            // Save response to the variable $data
                            $fields_string = array("account"=>$accountType);
                           
                            curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
                            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
                            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
                
                            $data = curl_exec($ch);
                   
                            // Check the return value of curl_exec(), too
                            if ($data === false) {dd(curl_error($ch));
                                throw new Exception(curl_error($ch), curl_errno($ch));
                            }
                           
                            // print_r($data);
                            // print_r($fields_string);
                            // die();
                            $responseAccessTokenArray = json_decode($data,true);
                       
                       
                            // Get LAZADA Authentication Token
                            // $dataReturn["orders"] = $masterList;
                         
                        
                            if( isset($responseAccessTokenArray["responseCode"]) && $responseAccessTokenArray["responseCode"] == '1'){
                                
                                 $LazadaAPIClient = new LazopClient($urlClient,$app_key,$app_secret_id);
                                $request = new LazopRequest('/order/items/get','GET');
                                $request->addApiParam('order_id',$ordernumber);
                                $requestResponseDetails = $LazadaAPIClient->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
                
                                $requestResponseDetailsArray = json_decode($requestResponseDetails,true);
                                
                                // echo '<pre>';
                                // print_r($requestResponseDetailsArray);
                                // echo '</pre>';
                                
                                echo $ordernumber.'---'.$requestResponseDetailsArray["data"][0]["package_id"].'Ins<br>';
                                
                                
                                
                                $package_id = 0;
                                $package_id = $requestResponseDetailsArray["data"][0]["package_id"];
                                
                                $package = array('packages' => [array('package_id' => $package_id)]);
                                
                                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                                $request = new LazopRequest('/order/package/sof/delivered');
                                $request->addApiParam('dbsDeliveryReq',json_encode($package));
                                $c->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
                                // var_dump($c->execute($request, $responseAccessTokenArray["data"]["valid_token"]));
                                /*
                                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                                $request = new LazopRequest('/order/sof/delivered');
                                $request->addApiParam('order_item_ids',json_encode($order_item_id));
                                $c->execute($request, $responseAccessTokenArray["data"]["valid_token"]); 
                                */
                                
                                // var_dump($c->execute($request, $responseAccessTokenArray["data"]["valid_token"]));
                            }
                            
                            
                  
                    
                    // LAZADA DBS End 
                    
                }
                
                
            }
            
            
            
        }
        
        }catch(exception $ex){

            $isError = true;

        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
            
            return array(
                "isError" => $isError
            );
        }
        // $ordernumber = '317663517587665';
        
        
    }
    
    public function anyLazadadbs(){
        
        set_time_limit(0);
        ini_set('memory_limit', '-1');
        $isError = 0;
            echo 'In';
            // die();
        try{
            
            set_time_limit(0);
            $masterList = array();
            $data = array();
            $dataReturn = array();
            
            $accountType = 9;
            $datetime = new DateTime('2017-04-01 00:00:00');
            // $datetime = new DateTime('2021-10-04 00:00:00');
                $createAfter =  $datetime->format(DateTime::ATOM);

            // Define Credential key for lazada API
            switch ($accountType) {
                case 1:
                    $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 2:
                    $app_key = Config::get('constants.LAZADA_JOCOM_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_JOCOM_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 3:
                    $app_key = Config::get('constants.LAZADA_ETIKA_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_ETIKA_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 4:
                    $app_key = Config::get('constants.LAZADA_YEOS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_YEOS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 5:
                    $app_key = Config::get('constants.LAZADA_STARBUCKS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_STARBUCKS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 6:
                    $app_key = Config::get('constants.LAZADA_POKKA_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_POKKA_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 7:
                    $app_key = Config::get('constants.LAZADA_EBFROZEN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_EBFROZEN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 8:
                    $app_key = Config::get('constants.LAZADA_EVERBEST_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_EVERBEST_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                case 9:
                    $app_key = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_JCMEXPRESS_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');

                    break;
                    
                default:

                    $app_key = Config::get('constants.LAZADA_FNN_V2_APP_KEY');
                    $app_secret_id = Config::get('constants.LAZADA_FNN_V2_APP_SECRET');
                    $urlClient = Config::get('constants.LAZADA_V2_URL_PATH');
                    break;
            }

            // Get LAZADA Authentication Token
            if(strtolower(Config::get('constants.ENVIRONMENT')) == "live"){
                $url = 'https://api.jocom.com.my/lazada/getauthtoken';
            }else{
                $url = 'https://uat.all.jocom.com.my/lazada/getauthtoken';
            }
            
            // echo 'In_1';
            // print_r($fields_string);
            // die();
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            // Save response to the variable $data
            $fields_string = array("account"=>$accountType);
           
            curl_setopt($ch, CURLOPT_FORBID_REUSE, false);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);

            $data = curl_exec($ch);
   
            // Check the return value of curl_exec(), too
            if ($data === false) {dd(curl_error($ch));
                throw new Exception(curl_error($ch), curl_errno($ch));
            }
           
            // print_r($data);
            // dd(curl_errno($ch));
            // print_r($fields_string);
            // die();
            $responseAccessTokenArray = json_decode($data,true);
            
            //  echo '<pre>';
            // print_r($responseAccessTokenArray);
            //  echo '</pre>';
        //   die();
       
            // Get LAZADA Authentication Token
            $dataReturn["orders"] = $masterList;
            
            //  $orderitem_id='386515526522832';    
            $orderitem_id = array("388506195105290", "386304624212644", "388498519253359"); 
            $packageid = array(); 
            foreach($orderitem_id as $key => $value){
             
             $LazadaAPIClient = new LazopClient($urlClient,$app_key,$app_secret_id);
                $request = new LazopRequest('/order/items/get','GET');
                $request->addApiParam('order_id',$value);
                $requestResponseDetails = $LazadaAPIClient->execute($request, $responseAccessTokenArray["data"]["valid_token"]);

                $requestResponseDetailsArray = json_decode($requestResponseDetails,true);
                
                echo '<pre>';
                print_r($requestResponseDetailsArray);
                echo '</pre>';
                
                echo $requestResponseDetailsArray["data"][0]["package_id"].'Ins<br>';
                
                
                
                $package_idd = 0;
                $package_idd = $requestResponseDetailsArray["data"][0]["package_id"];
                
                foreach($requestResponseDetailsArray["data"] as $key => $value){
                    
                    echo $value["package_id"].'Pack ID<br>';
                }
                
                $temppackageid = array('package_id' => $package_idd);
                array_push($packageid,$temppackageid);
                
            }    
                // $package = array('packages' => [array('package_id' => $package_id)]);
                $package = array('packages' => $packageid);
                
                echo '<pre>';
                print_r($package);
                echo '</pre>';
                print_r(json_encode($package));
                // die();
                echo  $responseAccessTokenArray["data"]["valid_token"].'Req ID';
                
                $c = new LazopClient($urlClient,$app_key,$app_secret_id);
                $request = new LazopRequest('/order/package/sof/delivered');
                $request->addApiParam('dbsDeliveryReq',json_encode($package));
                var_dump($c->execute($request, $responseAccessTokenArray["data"]["valid_token"]));
                
                
                
           die();
            if(count($orders) > 0 ){
                
                // foreach ($orders as $k => $val){
                    
                    
                    // foreach ($orders as $keyOrder => $valueOrder) {

                    //     $order_number = $valueOrder['order_number'];
                        
                    //     $order = LazadaPreOrder::where("order_number","=",$order_number)->where("from_account","=",$accountType)->get();
                        
                    //     // Order number already exist
                    //     // if($order > 0 ){
                    //     if(count($order) > 0 ){
                    //         // remove from list
                    //         unset($orders[$keyOrder]);
                    //         //echo "OUT";
                    //     }else{
                    //         //echo "IN";
                    //         $LazadaAPIClient = new LazopClient($urlClient,$app_key,$app_secret_id);
                    //         $request = new LazopRequest('/order/items/get','GET');
                    //         $request->addApiParam('order_id',$valueOrder['order_id']);
                    //         $requestResponseDetails = $LazadaAPIClient->execute($request, $responseAccessTokenArray["data"]["valid_token"]);
    
                    //         $requestResponseDetailsArray = json_decode($requestResponseDetails,true);
    
                    //         $valueOrder['orderItems'] = $requestResponseDetailsArray['data'];
                    //         array_push($masterList, $valueOrder);
                    //     }
                    //     sleep(1);
    
                    // }
                    
                    
                // }

                
                
                $dataReturn["orders"] = $masterList;
            }
            
            
        
        } catch (Exception $ex) {
            $messsage = $ex->getMessage();
            $isError = 1;

        } finally {
            return array(
                "response" => $isError,
                "message" => $messsage,
                "data" => $dataReturn
            );
        }
        
        
    }
    
    
    public function anyPriceupdate(){
    
        die('Done');

            $lazadaPrice = DB::table('jocom_price_upate')
                              ->groupBy('transaction_id')    
                              ->get();
            
           

            if(count($lazadaPrice) > 0){

                foreach ($lazadaPrice as $value) {
                    
                        // echo $value->transaction_id .'==' . $value->price_id .'==' . $value->qty .'<br>';
                        $trans_id = 0;
                        $product_id = 0;
                        $price = 0; 
                        $total = 0;
                        $trans_id = (int) $value->transaction_id;
                        $product_id = (int) $value->price_id;
                        $price = number_format($value->price,2);
                        
                        
                        
                        $pricelist = DB::table('jocom_price_upate')
                                        ->where("transaction_id","=",$trans_id)
                                        ->get();
                        
                        foreach ($pricelist as $value2) {
                            
                            
                                $transdetails = DB::table('jocom_transaction_details')
                                                 ->where("transaction_id","=",$value2->transaction_id) 
                                                 ->where("product_id","=",$value2->price_id)  
                                                 ->where("unit","=",$value2->qty)  
                                                  ->first();
                            
                             echo $value2->transaction_id .'==' . $transdetails->transaction_id .'==' . $transdetails->product_id .'==' . $value2->price_id .'==' . $value2->price .'==' . $value2->qty .'==' . $transdetails->price .'==' . $transdetails->id .'<br>'; 
                           
                             
                              DB::table('jocom_transaction_details')
                                 ->where("transaction_id","=",$value2->transaction_id) 
                                 ->where("product_id","=",$value2->price_id)  
                                 ->where("unit","=",$value2->qty) 
                                ->update(
                                        ['price' => $value2->price]
                                );
                            
                            // die();
                            
                        }
                                        
                        
                        //  DB::table('jocom_transaction_details')
                        //         ->where("transaction_id","=",$trans_id)
                        //         ->where("product_id","=",$product_id)
                        //         ->update(
                        //                 ['price' => $price]
                        //         );

                }
            }

            echo 'Value Updated'.'<br>';
            
            

            $lazadatrans = DB::table('jocom_price_upate')
                              ->select('transaction_id')
                              ->distinct()->get();
            
            echo '<pre>';
            print_r($lazadatrans);
            echo '</pre>';
            
            // die();
            

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
    
    public function anyChecklist(){
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)->where("status",1)->first();
        
        if($SysAdminRegion->region_id == 0){
            $driver = DB::table('logistic_driver')->where('status',1)->where('type',0)->select('id','name')->get();
        }else{
         $driver = DB::table('logistic_driver')->where('region_id', $SysAdminRegion->region_id)->where('status',1)->where('type',0)->select('id','name')->get();   
        }

        return View::make('report.warehouse_checklist')->with('driver', $driver);
    }

    

    public function anyChecklistreport(){
         
        $generatedfrom = Input::get('generatedfrom');
        $driver_count = Input::get('driver_count');
        if($generatedfrom=="assigned"){
        
        try{    
            
        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');
        $driver = Input::get('driver');
        $drive_count=count($driver);
        if($driver_count==$drive_count){
            $drivers_counts="all";
        }
        if($driver_count!=$drive_count && $drive_count!=1){
          $drivers_counts="multiple";  
        }
        if($drive_count==1){
            $drivers_counts="one";
        }
       if(!$driver){
        return Redirect::to('jlogistic/checklist')->with('message', 'Please Select Driver');
       }
       if(!$transaction_from || !$transaction_to){
        return Redirect::to('jlogistic/checklist')->with('message', 'Please Select Range Date');
       }

        $driver_details = LogisticDriver::find($driver);


        $collectionData = array();
 
        $logistic2 = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_batch_item AS LBI','LB.id','=','LBI.batch_id')
                    ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
                    ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
                    ->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'LTI.product_price_id')
                    ->leftJoin('jocom_products_category AS CD', 'JP.category', '=', 'CD.id')
                    ->leftJoin('jocom_products_category AS CDP', 'CD.category_parent', '=', 'CDP.id')
                    ->leftJoin('jocom_products_category AS CDPP', 'CDP.category_parent', '=', 'CDPP.id')
                    ->whereIn('LB.driver_id',$driver)
                    ->where('LB.assign_date', '>=',$transaction_from)
                    ->where('LB.assign_date','<=',$transaction_to)
                    ->whereIn('LB.status',[0,1,2,4])
                    ->select("JP.sku",DB::raw("JP.shortname,JP.name,JP.id,LTI.label,LTI.product_price_id,CD.category_name,CDP.category_name as parent_cat,CDPP.category_name as Gparent_cat,GROUP_CONCAT(LBI.qty_assign SEPARATOR ',') as 'qty_assign',GROUP_CONCAT(LT.transaction_id SEPARATOR ',') as 'transaction_id',SUM(LBI.qty_assign) as 'total', Count(LT.transaction_id) as 'id_count'"))
                    ->groupBy('LTI.product_price_id')
                    ->orderBy('id_count','Desc') //do not modify this
                    ->get();
                    
               

            foreach ($logistic2 as $key => $value) {
                
                $base = DB::table('jocom_product_base_item')
                    ->where("product_id",$value->id)
                    ->where("price_option_id",$value->product_price_id) // ADDED: WIRA
                    ->where("status",1);
                    
                $baseList = $base;
                $baseListData = $base->get();
                
                $baseListId = $baseList->lists('product_base_id');
                         
                $product = DB::table('jocom_products')->whereIn('id', $baseListId)->select('shortname', 'name', 'id', 'sku')->get(); // amended by 
                              
                if (count($product)>0) {
                    
                    $baseCounter = 0;
                    foreach ($product as $value3) {
                        
                        foreach ($baseListData as $kbd => $vbd) {
                            if($vbd->product_base_id == $value3->id){
                                $baseTotalQty = $vbd->quantity;
                            }
                        }
                            
                       
                        $productPrice = DB::table('jocom_product_price')
                            ->where('product_id', $value3->id)
                            ->where('default', 1)
                            ->first(); // amended by wira add quantity
                            
                   
                        if ($value3->shortname!='') {
                           $name =  $value3->shortname;
                        }else{
                            $name = $value3->name;
                        }
                        
                        if ($productPrice->alternative_label_name !='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                        }
                         
                        
                        $assignCollection = explode(",",$value->qty_assign);
                        $assignQTY = '';
                        
                        foreach ($assignCollection as $assign){
                            $assignQTY[] = $assign * $baseTotalQty;
                        }
                        
                        $assignQTY =  implode(",",$assignQTY);
               
                        if (array_key_exists($value3->sku, $collectionData)) {
                            // echo "HERE";
                            $collectionData[$value3->sku]['qty_assign'] = $collectionData[$value3->sku]['qty_assign'].",".$assignQTY;
                            $collectionData[$value3->sku]['qty'] = $collectionData[$value3->sku]['qty'].",".$value->qty_assign;
                            $collectionData[$value3->sku]['transaction_id'] = $collectionData[$value3->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value3->sku]['total'] = $collectionData[$value3->sku]['total'] + $value->total;
                            $collectionData[$value3->sku]['id_count'] = $collectionData[$value3->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            
                        }else{
               
                   $local=DB::table('jocom_categories as jc')
                        ->join('jocom_products_category as jp','jc.category_id','=','jp.id')
                        ->where('jc.product_id','=',$value->id)
                        ->select('jp.category_name')
                        ->get();
                    
                    $checklist_final=0;
                    foreach ( $local as $multplevalue) {        
                    if(str_contains($multplevalue->category_name, 'Frozen')){
                    $checklist_final=1;
                       }
                    }

                if(str_contains($value->category_name, 'Frozen')||str_contains($value->parent_cat, 'Frozen')||str_contains($value->Gparent_cat, 'Frozen') || $checklist_final=='1') {

                $cat_type="Frozen";

                }else{
                $cat_type="Dry";
                }
                            $setCol = array(
                                "product_sku" => $value3->sku ,
                                "qty" => $value->qty_assign ,
                                "qty_assign" => $assignQTY, //$value->qty_assign ,
                                "label_name" => $label_name ,
                                "transaction_id" =>$value->transaction_id,
                                "total" => $value->total,
                                "cat_type"=>$cat_type,
                                "sku"=>$value->sku,
                                "id_count" => $value->id_count,
                                "base_product" => $name,
                                "base_quantity" => $baseTotalQty,
                            );
                        
                            
                            
                            $collectionData[$value3->sku] = $setCol;
                            
                        }
                        
                        $baseCounter++;
                          
                    }  
                    
          
                }else{
                     
                    $productPrice = DB::table('jocom_product_price')
                            ->where("id",$value->product_price_id)
                            ->first(); // amended by wira add quantity

                    if ($value->shortname!='') {
                           $name =  $value->shortname;
                    }else{
                            $name = $value->name;
                    }
                    
                    if ($productPrice->alternative_label_name!='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                    }
                    
                    if (array_key_exists($value->sku, $collectionData)) {
                      
                                                         
                            $collectionData[$value->sku]['qty_assign'] = $collectionData[$value->sku]['qty_assign'].",".$value->qty_assign;
                            $collectionData[$value->sku]['transaction_id'] = $collectionData[$value->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value->sku]['total'] = $collectionData[$value->sku]['total'] + $value->total;
                            $collectionData[$value->sku]['id_count'] = $collectionData[$value->sku]['id_count'] + count(explode(",",$value->transaction_id));
                          
                            
                    }else{
                    
                   $local=DB::table('jocom_categories as jc')
                        ->join('jocom_products_category as jp','jc.category_id','=','jp.id')
                        ->where('jc.product_id','=',$value->id)
                        ->select('jp.category_name')
                        ->get();
                    
                    $checklist_final=0;
                    foreach ( $local as $multplevalue) {        
                    if(str_contains($multplevalue->category_name, 'Frozen')){
                    $checklist_final=1;
                       }
                    }

                if(str_contains($value->category_name, 'Frozen')||str_contains($value->parent_cat, 'Frozen')||str_contains($value->Gparent_cat, 'Frozen') || $checklist_final=='1') {
                $cat_type="Frozen";

                }else{
                $cat_type="Dry";
                }
                
                        $collectionData[$value->sku] = array(
                            "is_not_base" => true,
                            "shortname" => $name,
                            "label_name" => $label_name,
                            "qty_assign" => $value->qty_assign,
                            "cat_type"=>$cat_type,
                            "sku"=>$value->sku,
                            "transaction_id" =>$value->transaction_id,
                            "total" => $value->total,
                            "id_count" => $value->id_count,
                        );
                        
                    }
                        
                
                }
                
            }
         
            $data = array(
                    'logistic2' =>$collectionData,
                    'transaction_from'=>$transaction_from,
                    'transaction_to'=>$transaction_to,
                    'driver_name' => $driver_details,
                    'generatedfrom'=>$generatedfrom,
                    'drivers_counts'=>$drivers_counts,
                    );
                                     
                    
            if (!empty($logistic2)) {

                return Excel::create('checklist-assigned('.$transaction_from.'- '.$transaction_to.')', function($excel) use ($data) {
   
                    $excel->sheet('Batch Item', function($sheet) use ($data)
                    {   

                        $sheet->loadView('report.assignedtable4', array('data'=>$data));
                        $sheet->setOrientation('landscape');

                    });

                })->download('xls');

            }else{
                return Redirect::to('jlogistic/checklist')->with('message', 'Sorry. No data found!');
            }
            
        }catch(Exception $ex){
            
            echo $ex->getMessage();
        }
    }else{
       try{
             
        $transaction_from = Input::get('transaction_from');
        $transaction_to = Input::get('transaction_to');
        $driver = Input::get('driver');
        if($driver_count==$drive_count){
            $drivers_counts="all";
        }
        if($driver_count!=$drive_count && $drive_count!=1){
          $drivers_counts="multiple";  
        }
        if($drive_count==1){
            $drivers_counts="one";
        }
         if(!$driver){
        return Redirect::to('jlogistic/checklist')->with('message', 'Please Select Driver');
       }
       if(!$transaction_from || !$transaction_to){
        return Redirect::to('jlogistic/checklist')->with('message', 'Please Select Range Date');
       }
        $driver_details = LogisticDriver::find($driver);

        $collectionData = array();

        $logistic2 = DB::table('logistic_batch_prescan AS LB')
                    ->leftJoin('logistic_batch_item_prescan AS LBI','LB.id','=','LBI.batch_id')
                    ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'LBI.transaction_item_id')
                    ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LB.logistic_id')
                    ->leftJoin('jocom_products AS JP', 'JP.sku', '=', 'LTI.sku')
                    ->leftJoin('jocom_product_price AS JPP', 'JPP.id', '=', 'LTI.product_price_id')
                    ->leftJoin('jocom_products_category AS CD', 'JP.category', '=', 'CD.id')
                    ->leftJoin('jocom_products_category AS CDP', 'CD.category_parent', '=', 'CDP.id')
                    ->leftJoin('jocom_products_category AS CDPP', 'CDP.category_parent', '=', 'CDPP.id')
                    ->whereIn('LB.driver_id',$driver)
                    ->where('LB.assign_date', '>=',$transaction_from)
                    ->where('LB.assign_date','<=',$transaction_to)
                    ->whereIn('LB.status',[0,1])
                    ->select("JP.sku",DB::raw("JP.shortname,JP.name,JP.id,LTI.label,LTI.product_price_id,CD.category_name,CDP.category_name as parent_cat,CDPP.category_name as Gparent_cat,GROUP_CONCAT(LBI.qty_assign SEPARATOR ',') as 'qty_assign',GROUP_CONCAT(LT.transaction_id SEPARATOR ',') as 'transaction_id',SUM(LBI.qty_assign) as 'total', Count(LT.transaction_id) as 'id_count'"))
                    ->groupBy('LTI.product_price_id')
                    ->orderBy('id_count','Desc') //do not modify this
                    ->get();
                    
                   

            foreach ($logistic2 as $key => $value) {
                
                $base = DB::table('jocom_product_base_item')
                    ->where("product_id",$value->id)
                    ->where("price_option_id",$value->product_price_id) // ADDED: WIRA
                    ->where("status",1);
                              
                $baseList = $base;
                $baseListData = $base->get(); 
                $baseListId = $baseList->lists('product_base_id');
              
                $product = DB::table('jocom_products')->whereIn('id', $baseListId)->select('shortname', 'name', 'id', 'sku')->get(); // amended by wira add quantity

                if (count($product)>0) {
                    
                    $baseCounter = 0;
                    foreach ($product as $value3) {
                        
                        foreach ($baseListData as $kbd => $vbd) {
                            if($vbd->product_base_id == $value3->id){
                                $baseTotalQty = $vbd->quantity;
                                //echo "BASE QTY:".$vbd->quantity; 
                            }
                        }
                            
                       
                        $productPrice = DB::table('jocom_product_price')
                            ->where('product_id', $value3->id)
                            ->where('default', 1)
                            ->first(); // amended by wira add quantity
                            
                   
                        if ($value3->shortname!='') {
                           $name =  $value3->shortname;
                        }else{
                            $name = $value3->name;
                        }
                        
                        if ($productPrice->alternative_label_name !='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                        }
                        
                        $assignCollection = explode(",",$value->qty_assign);
                        $assignQTY = '';
                        
                        foreach ($assignCollection as $assign){
                            $assignQTY[] = $assign * $baseTotalQty;
                        }
                        
                        $assignQTY =  implode(",",$assignQTY);
                        
                        if (array_key_exists($value3->sku, $collectionData)) {
                            // echo "HERE";
                            $collectionData[$value3->sku]['qty_assign'] = $collectionData[$value3->sku]['qty_assign'].",".$assignQTY;
                            $collectionData[$value3->sku]['qty'] = $collectionData[$value3->sku]['qty'].",".$value->qty_assign;
                            $collectionData[$value3->sku]['transaction_id'] = $collectionData[$value3->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value3->sku]['total'] = $collectionData[$value3->sku]['total'] + $value->total;
                            $collectionData[$value3->sku]['id_count'] = $collectionData[$value3->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            
                        }else{
                    
                    $local=DB::table('jocom_categories as jc')
                        ->join('jocom_products_category as jp','jc.category_id','=','jp.id')
                        ->where('jc.product_id','=',$value->id)
                        ->select('jp.category_name')
                        ->get();
                    
                    $checklist_final=0;
                    foreach ( $local as $multplevalue) {        
                    if(str_contains($multplevalue->category_name, 'Frozen')){
                    $checklist_final=1;
                       }
                    }

                if(str_contains($value->category_name, 'Frozen')||str_contains($value->parent_cat, 'Frozen')||str_contains($value->Gparent_cat, 'Frozen') || $checklist_final=='1') {
                            $cat_type="Frozen";

                            }else{
                            $cat_type="Dry";
                            }
                            $setCol = array(
                                "product_sku" => $value3->sku ,
                                "qty" => $value->qty_assign ,
                                "qty_assign" => $assignQTY, //$value->qty_assign ,
                                "label_name" => $label_name ,
                                "transaction_id" =>$value->transaction_id,
                                "cat_type"=>$cat_type,
                                "sku"=>$value->sku,
                                "total" => $value->total,
                                "id_count" => $value->id_count,
                                "base_product" => $name,
                                "base_quantity" => $baseTotalQty,
                            );
                        
                            
                            
                            $collectionData[$value3->sku] = $setCol;
                            
                            
                        }  
                            
                        $baseCounter++;
                          
                    }  
                    
          
                }else{
                     
                    $productPrice = DB::table('jocom_product_price')
                            ->where("id",$value->product_price_id)
                            ->first(); // amended by wira add quantity

                    if ($value->shortname!='') {
                           $name =  $value->shortname;
                    }else{
                            $name = $value->name;
                    }
                    
                    if ($productPrice->alternative_label_name!='') {
                            $label_name =  $productPrice->alternative_label_name;
                        }else{
                            $label_name = $productPrice->label;
                    }
                    
                    if (array_key_exists($value->sku, $collectionData)) {
                      
                            
                            $collectionData[$value->sku]['qty_assign'] = $collectionData[$value->sku]['qty_assign'].",".$value->qty_assign;
                            $collectionData[$value->sku]['transaction_id'] = $collectionData[$value->sku]['transaction_id'].",".$value->transaction_id;
                            $collectionData[$value->sku]['total'] = $collectionData[$value->sku]['total'] + $value->total;
                            $collectionData[$value->sku]['id_count'] = $collectionData[$value->sku]['id_count'] + count(explode(",",$value->transaction_id));
                            
                    }else{
                        
                    $local=DB::table('jocom_categories as jc')
                        ->join('jocom_products_category as jp','jc.category_id','=','jp.id')
                        ->where('jc.product_id','=',$value->id)
                        ->select('jp.category_name')
                        ->get();
                    
                    $checklist_final=0;
                    foreach ( $local as $multplevalue) {        
                    if(str_contains($multplevalue->category_name, 'Frozen')){
                    $checklist_final=1;
                       }
                    }

                if(str_contains($value->category_name, 'Frozen')||str_contains($value->parent_cat, 'Frozen')||str_contains($value->Gparent_cat, 'Frozen') || $checklist_final=='1') {

                        $cat_type="Frozen";

                        }else{
                        $cat_type="Dry";
                        }
                        $collectionData[$value->sku] = array(
                            "is_not_base" => true,
                            "shortname" => $name,
                            "label_name" => $label_name,
                            "qty_assign" => $value->qty_assign,
                            "transaction_id" =>$value->transaction_id,
                            "cat_type"=>$cat_type,
                            "sku"=>$value->sku,
                            "total" => $value->total,
                            "id_count" => $value->id_count,
                        );
                        
                    }

                }
                
            }

            $data = array(
                    'logistic2' =>$collectionData,
                    'transaction_from'=>$transaction_from,
                    'transaction_to'=>$transaction_to,
                    'driver_name' => $driver_details,
                    'generatedfrom'=>$generatedfrom,
                    'drivers_counts'=>$drivers_counts,
                    );

            if (!empty($logistic2)) {

                return Excel::create('checklist-prescan('.$transaction_from.'- '.$transaction_to.')', function($excel) use ($data) {
   
                    $excel->sheet('Batch Item', function($sheet) use ($data)
                    {   
                        $sheet->loadView('report.assignedtable4', array('data'=>$data));
                        $sheet->setOrientation('landscape');

                    });

                })->download('xls');

            }else{
                return Redirect::to('jlogistic/checklist')->with('message', 'Sorry. No data found!');
            }
            
        }catch(Exception $ex){
            
            echo $ex->getMessage();
        }
  
    }

    }

    public function anyMobile(){

        $lscheck = Transaction::Checkminspendvalue('JC55536');
            echo $lscheck.'Req';
    }
    
    public function anySmartcheck(){

        $lscheck = MCheckout::smartrentalemail(669154);
            echo $lscheck.'Req';
    }
    
    public function anyCronMissingTransaction()
    {   die('Disabled Temporary');
        // $yesterdayStartDay  = '2023-01-10 00:00:00';
        $yesterdayStartDay  = Carbon\Carbon::parse('yesterday')->startOfDay()->toDateTimeString();
        $yesterdayEndDay    = Carbon\Carbon::parse('yesterday')->endOfDay()->toDateTimeString();

        $trans = DB::table('jocom_transaction')
            ->selectRaw('id, transaction_date, status, delivery_city, delivery_state')
            ->where('invoice_no', '')
            ->where('status', 'completed')
            ->where('transaction_date', '>=', $yesterdayStartDay)
            ->where('transaction_date', '<=', $yesterdayEndDay)
            ->whereNotIn('buyer_username', ['11Street','lazada','Qoo10','shopee','Astro Go Shop','vettons','pgmall','tiktokshop','Lamboplace','SRewardsCentre'])
            ->whereNotIn('id',function($query){
                $query->select('transaction_id')->from('logistic_transaction');
            })
            ->get();

        if($trans){
            $fileName = 'missing_transaction_'.date("Ymdhis").".csv";
    
            $path = Config::get('constants.CSV_FILE_PATH');
            $file = fopen($path.'/'.$fileName, 'w');
    
            fputcsv($file, ['Transaction ID', 'Transaction Date', 'Status', 'Delivery City','Delivery State', 'Logistic']);
    
            foreach ($trans as $row)
            {   
                // echo $row['driver_username'];
                    fputcsv($file, [
                        $row->id,
                        $row->transaction_date,
                        $row->status,
                        $row->delivery_city,
                        $row->delivery_state,
                        'Not In'
                    ]);
                //}            
            }
            
            fclose($file);
            $test = Config::get('constants.ENVIRONMENT');
    
            if ($test == 'test')
                $mail = ['maruthu@tmgrocer.com'];
                // $mail = ['maruthu@jocom.my'];
            else
                // $mail = ['maruthu@jocom.my'];
                $mail = ['maruthu@tmgrocer.com'];
    
            $subject = "Missing Transaction : " . $fileName;
            $attach = $path . "/" . $fileName;
    
            $body = array('title' => 'Missing Transaction');
    
            Mail::send('emails.missingtransaction', $body, function($message) use ($subject, $mail, $attach)
                {
                    $message->from('maruthu@tmgrocer.com', 'Maruthu');
                    $message->to($mail, '')->subject($subject);
                    $message->attach($attach);
                }
            );
          
            unlink($attach);  
        }

    }
    
    public function anyInvissue(){
        die('Done');
         $trans = DB::table('jocom_invissue')
                              ->select('*')
                              ->get();
                              
        if(count($trans) > 0){

                foreach ($trans as $value) {
                    

                    $t_id = 0;
                    $t_id = trim($value->transaction_id);
                    $t_inv = trim($value->invoice_no);
                    $Existstrn = DB::table('jocom_transaction')
                        ->where('id', '=',$t_id)
                        ->where('invoice_no', '=',$t_inv)
                        ->where('status','=','completed')
                        ->get();
                    if(count($Existstrn) === 0){
                        echo count($Existstrn).'-'.$value->transaction_id.'-'.$value->invoice_no.' <br>';
                        $sql = DB::table('jocom_transaction')
                                ->where('id', $value->transaction_id)
                                ->update(array('invoice_no' => $value->invoice_no));
                        // die();
                    }
                
                    
                }
        }
        
    }
    
    public function anyPendingOrders()
    {   // die('Disabled Temporary');
        $date = Carbon\Carbon::parse('today')->subDays(7);

        $trans = DB::table('logistic_transaction')
            ->selectRaw('transaction_date, transaction_id, status, DATEDIFF(now(),date(transaction_date)) as total_pending_days, delivery_city, delivery_state')
            ->where('status', 0)
            ->orderBy('transaction_date', 'asc')
            ->orderBy('transaction_id', 'asc')
            ->orderBy('delivery_state', 'asc')
            ->get();
            
            // echo "<pre>";
            //     Print_r("MASUK 1");
            //     echo "<br>trans = ";
            //     Print_r($trans);
            // echo "</pre>";
            // die(); 

        if($trans){
            $fileName = 'pending_transaction_weekly_'.date("Ymdhis").".csv";
    
            $path = Config::get('constants.CSV_FILE_PATH');
            $file = fopen($path.'/'.$fileName, 'w');
    
            fputcsv($file, ['Transaction Date', 'Transaction ID', 'Status', 'Total Pending Days','Delivery City','Delivery State']);
    
            foreach ($trans as $row)
            {   
                // echo $row['driver_username'];
                    fputcsv($file, [
                        $row->transaction_date,
                        $row->transaction_id,
                        'pending',
                        $row->total_pending_days,
                        $row->delivery_city,
                        $row->delivery_state,
                    ]);
                //}            
            }
            
            fclose($file);
            $test = Config::get('constants.ENVIRONMENT');
    
            if ($test == 'test')
                $mail = ['maruthu@tmgrocer.com'];
                // $mail = ['maruthu@jocom.my'];
            else
                // $mail = ['nadzri@jocom.my', 'nadzri.work@gmail.com'];
                // $mail = ['maruthu@jocom.my', 'nadzri@jocom.my'];
                $mail = ['quenny.leong@tmgrocer.com'];
    
            $subject = "Pending Transaction Weekly: " . $fileName;
            $attach = $path . "/" . $fileName;
    
            $body = array('title' => 'Missing Transaction');
    
            Mail::send('emails.missingtransaction', $body, function($message) use ($subject, $mail, $attach)
                {
                    $message->from('maruthu@tmgrocer.com', 'Maruthu');
                    $message->to($mail, '')->subject($subject);
                    $message->attach($attach);
                }
            );
          
            unlink($attach);  
        }
        echo 'Done';

    }
    
}
?>