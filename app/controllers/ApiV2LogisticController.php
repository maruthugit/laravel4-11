<?php 
use Helper\ImageHelper as Image;
class ApiV2LogisticController extends BaseController {

   
    public function anyIndex()
    {
        echo "Under development...";
    }

    public function anyLogin()
    {
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'LOGISTIC_DRIVER_LOGIN';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();
        
        $data = array();

        if(Input::has('username') AND Input::has('password'))
        {
            $username = trim(Input::get('username'));
            $password = trim(Input::get('password'));

            $driver = LogisticDriver::where('username', '=', $username)->where('status', '=', '1')->first();

            if(count($driver)>0)
            {
                if(Hash::check($password, $driver->password))
                {
                    $type = LogisticDriver::get_type($driver->type);
                    
                    $data['driver_id'] = $driver->id;
                    $data['name'] = $driver->name;
                    $data['contact_no'] = $driver->contact_no;
                    $data['type'] = $type;
                    $data['filename'] = $driver->filename;
                    $data['filepath'] = Config::get('constants.DRIVER_PROFILE_FILE_PATH');
                    $data['fileurl'] = Config::get('constants.DRIVER_PROFILE_FILE_URL');
                    $headerResponse = array(
                        "isSuccess" => 'true',
                        "message" => 'Login Succesfully!'
                    );
                    
                    $data = array_merge($headerResponse,$data);
                }
                else
                    $data['status_msg']  = 'Invalid username or password!';
                    $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => 'Invalid username or password!'
                    );
                    
                    $data = array_merge($headerResponse,$data);
            }
            else
                $data['status_msg']  = 'Invalid username!';
                $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => 'Invalid username!'
                    );
                    
                $data = array_merge($headerResponse,$data);
        }
        else
            $data['status_msg']  = 'No username or password!';
            $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => $data['status_msg']
                    );

            $data = array_merge($headerResponse,$data);
        


        return View::make('logistic.logistic_app_view')->with('data', $data); 
    //     return $data;
    }


    public function anyDriver()
    {
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'LOGISTIC_DRIVER';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();
        
        $data = array();

        if(Input::has('username'))
        {
            $username = trim(Input::get('username'));

            $supervisor = LogisticDriver::where('username', '=', $username)->where('type', '=', '1')->where('status', '=', '1')->first();

            // only supervisor may access
            if(count($supervisor)>0)
            {
                $driver = LogisticDriver::where('status', '=', '1')->get();

                if(count($driver)>0)
                {
                    foreach ($driver as $drow)
                    {
                        $type = LogisticDriver::get_type($drow->type);

                        $data['driver'][] = array(
                            'driver_id' => $drow->id,
                            'name' => $drow->name,
                            'contact_no' => $drow->contact_no,
                            'type' => $type,
                        );
                        $headerResponse = array(
                            "isSuccess" => 'true',
                            "message" => 'Success'
                        );
                    
                        $data = array_merge($headerResponse,$data);
                    }
                }
                else
                    $data['status_msg']  = 'No driver.';
                    $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => 'No active driver records'
                    );
                    
                    $data = array_merge($headerResponse,$data);
            }
            else
                $data['status_msg']  = 'Access denied.';
                $headerResponse = array(
                        "isSuccess" => 'false',
                        "message" => 'Your access denied!'
                    );
                $data = array_merge($headerResponse,$data);
        }
        else
            $data['status_msg']  = 'No username.';
            $headerResponse = array(
                "isSuccess" => 'false',
                "message" => 'No username provided!'
            );
            $data = array_merge($headerResponse,$data);


        return View::make('logistic.logistic_app_view')->with('data', $data);
    }
 
    public function anyDriverdashboard(){

        $username = Input::get('username');
        $startDate = Input::get("startDate");
        $endDate = Input::get("endDate");

        if ($username != '') {

            $pending = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)      
                ->where('logistic_batch.status', '=', 0)
                ->where('logistic_batch.modify_date', '>=', date('Y-m-d').' 00:00:00')
                ->count();

            $returned = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)              
                ->where('logistic_batch.status', '=', 2)
                ->where('logistic_batch.modify_date', '>=', date('Y-m-d').' 00:00:00') 
                ->count();

            $undelivered = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)  
                ->where('logistic_batch.status', '=', 3)
                ->where('logistic_batch.modify_date', '>=', date('Y-m-d').' 00:00:00') 
                ->count();

            $sent = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)        
                ->where('logistic_batch.status', '=', 4)
                ->where('logistic_batch.modify_date', '>=', date('Y-m-d').' 00:00:00')
                ->count();

            $total_assigned = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)        
                ->where('logistic_batch.assign_date', '<=', date('Y-m-d').' 23:59:59')
                ->count();

        }

        if ($startDate!='' && $endDate!='') {

            $pending = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)
                ->where('logistic_batch.modify_date', '>=', $startDate." 00:00:00")
                ->where('logistic_batch.modify_date', '<=', $endDate." 23:59:59")    
                ->where('logistic_batch.status', '=', 0)
                ->count();

            $returned = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)        
                ->where('logistic_batch.modify_date', '>=', $startDate." 00:00:00")
                ->where('logistic_batch.modify_date', '<=', $endDate." 23:59:59") 
                ->where('logistic_batch.status', '=', 2)
                ->count();

            $undelivered = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)
                ->where('logistic_batch.modify_date', '>=', $startDate." 00:00:00")
                ->where('logistic_batch.modify_date', '<=', $endDate." 23:59:59")         
                ->where('logistic_batch.status', '=', 3)
                ->count();

            $sent = DB::table('logistic_driver')
                ->join('logistic_batch', 'logistic_batch.driver_id', '=', 'logistic_driver.id')
                ->where('logistic_driver.username', '=', $username)
                ->where('logistic_batch.modify_date', '>=', $startDate." 00:00:00")
                ->where('logistic_batch.modify_date', '<=', $endDate." 23:59:59")      
                ->where('logistic_batch.status', '=', 4)
                ->count();
        }

        $data = array(
            "sent"=>$sent,
            "returned"=>$returned,
            "undelivered"=>$undelivered,
            "pending"=>$pending,
            "total_assigned"=>$total_assigned
            );

        return View::make('logistic.logistic_app_view')->with('data', $data);       

    }

    public function anyTransaction()
    {
        $data   = array();
        $get    = array();

        if(Input::has('username'))
            $get['username'] = trim(Input::get('username'));

        if(Input::has('transaction_date'))
            $get['transaction_date'] = trim(Input::get('transaction_date'));

        if(Input::has('status'))
            $get['status'] = trim(Input::get('status'));

        if(Input::has('transaction_id'))
            $get['transaction_id'] = trim(Input::get('transaction_id'));

        if(Input::has('urgent'))
            $get['urgent'] = trim(Input::get('urgent'));

        if(Input::has('from'))
            $get['from'] = trim(Input::get('from'));

        if(Input::has('count'))
            $get['count'] = trim(Input::get('count'));
        else
            $get['count'] = 20;


        $data = LogisticTransaction::api_transaction_list($get);

        return View::make('logistic.logistic_app_view')->with('data', $data);
    }

    public function anyTransactiondetail()
    {
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'LOGISTIC_TRANSACTION_DETAIL';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();
        
        
        $data   = array();
        $get    = array();
        $get['cms'] = 0;

        if(Input::has('username'))
            $get['username'] = trim(Input::get('username'));

        if(Input::has('logistic_id'))
            $get['logistic_id'] = trim(Input::get('logistic_id'));

        if(Input::has('status'))
            $get['status'] = trim(Input::get('status'));

        if(Input::has('item_id'))
            $get['item_id'] = Input::get('item_id');

        if(Input::has('qty_assign'))
            $get['qty_assign'] = Input::get('qty_assign');

        if(Input::has('driver_id'))
            $get['driver_id'] = trim(Input::get('driver_id'));
            
        if(Input::has('international_courier_id'))
            $get['international_courier_id'] = trim(Input::get('international_courier_id'));
        
        if(Input::has('is_international_logistic'))
            $get['is_international_logistic'] = trim(Input::get('is_international_logistic'));

        if(Input::has('remark'))
            $get['remark'] = trim(Input::get('remark'));

        if(Input::has('cms'))
        {
            $get['cms'] = trim(Input::get('cms'));
            $get['username'] = '[CMS]'.Session::get('username');
        }

        if ($get['cms']==1) {

            $data = LogisticTransaction::api_transaction_detail($get);
            
            return Redirect::back()->with('success', $data['status_msg']);
        }
        else{

            $data = LogisticTransaction::api_transaction_detail($get);
            return View::make('logistic.logistic_app_view')->with('data', $data);
        }

        //$data = LogisticTransaction::api_transaction_detail($get);

        //return View::make('logistic.logistic_app_view')->with('data', $data);
    }

    public function anyBatch()
    {
        $data   = array();
        $get    = array();
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'LOGISTIC_BATCH_LIST';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

        if(Input::has('username'))
            $get['username'] = trim(Input::get('username'));

        if(Input::has('logistic_id'))
            $get['logistic_id'] = trim(Input::get('logistic_id'));

        if(Input::has('batch_date'))
            $get['batch_date'] = trim(Input::get('batch_date'));

        if(Input::has('accept_date'))
            $get['accept_date'] = trim(Input::get('accept_date'));

        if(Input::has('assign_date'))
            $get['assign_date'] = trim(Input::get('assign_date'));

        if(Input::has('status'))
            $get['status'] = trim(Input::get('status'));
            
        if(Input::has('image_status'))
            $get['image_status'] = trim(Input::get('image_status'));

        if(Input::has('from'))
            $get['from'] = trim(Input::get('from'));

        if(Input::has('keyword'))
            $get['keyword'] = trim(Input::get('keyword'));

        if(Input::has('count'))
            $get['count'] = trim(Input::get('count'));
        else
            $get['count'] = 500;


        $data = LogisticBatch::api_batch_list($get);

        return View::make('logistic.logistic_app_view')->with('data', $data);
    }

    public function anyBatchdetail()
    {
        $data   = array();
        $get    = array();
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'LOGISTIC_BATCH';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

	if(Input::has('qrcode'))
        {
            $qrcode = Input::get('qrcode');

            $batch = DB::table('logistic_transaction')->where('do_no', '=', $qrcode)->first();

            $batch_id = DB::table('logistic_batch')
                        ->where('logistic_id', '=', $batch->id)
                        ->orderBy('id', 'desc')
                        ->first();


            $batch_item_id = DB::table('logistic_batch_item')->where('batch_id', '=', $batch_id->id)->lists('id');

            $qty_pickup = DB::table('logistic_batch_item')->where('batch_id', '=', $batch_id->id)->lists('qty_assign');

            $get['username'] = trim(Input::get('username'));
            
            if(isset($batch_id->status) && $batch_id->status == 0){
                $get['batch_id'] = $batch_id->id;
                $get['batch_item_id'] = $batch_item_id;    
                $get['qty_pickup'] = $qty_pickup;
            }
            $get['delivery_img'] = Input::file('delivery_img');

            //print_r($batch);
            //print_r($batch_id);
            //print_r($batch_item_id);
            //print_r($qty_pickup);


        }else{
        if(Input::has('username'))
            $get['username'] = trim(Input::get('username'));

        if(Input::has('batch_id'))
            $get['batch_id'] = trim(Input::get('batch_id'));

        if(Input::has('status'))
            $get['status'] = trim(Input::get('status'));

        if(Input::has('items'))
            $get['items'] = Input::get('items');

        if(Input::has('batch_item_id'))
            $get['batch_item_id'] = Input::get('batch_item_id');

        if(Input::has('qty_sent'))
            $get['qty_sent'] = Input::get('qty_sent');

        if(Input::has('qty_pickup'))
            $get['qty_pickup'] = Input::get('qty_pickup');

        if(Input::has('remark_item'))
            $get['remark_item'] = Input::get('remark_item');

        if(Input::has('latitude'))
            $get['latitude'] = trim(Input::get('latitude'));

        if(Input::has('longitude'))
            $get['longitude'] = trim(Input::get('longitude'));

        if(Input::hasFile('signature'))
            $get['signature'] = Input::file('signature');

        if(Input::hasFile('signature2'))
            $get['signature2'] = Input::file('signature2');

        if(Input::hasFile('delivery_img'))
                $get['delivery_img'] = Input::file('delivery_img');

        if(Input::has('accept_date'))
            $get['accept_date'] = trim(Input::get('accept_date'));

        if(Input::has('sign_name'))
            $get['sign_name'] = trim(Input::get('sign_name'));

        if(Input::has('sign_ic'))
            $get['sign_ic'] = trim(Input::get('sign_ic'));

        if(Input::has('remark'))
            $get['remark'] = trim(Input::get('remark'));
            
	}

        $data = LogisticBatch::api_batch_detail($get);


        // return View::make('logistic.do_view')->with('display_details', $data);
        return View::make('logistic.logistic_app_view')->with('data', $data);
    }

    public function anyImage()
    {


        $all = $_POST;

         $all = '{"username":"admin","logistic_id":7,"status":"Undelivered","remark":"abc","items":[{"item_id":12,"qty_assign":"1","remark_item":""},{"item_id":13,"qty_assign":"1","remark_item":""},{"item_id":14,"qty_assign":"1","remark_item":""}]}';
        // $all2 = json_encode($all);
        $all2 = json_decode($all);
        // echo '<img src="data:image/jpeg;base64,' . base64_encode($all2->signature) . '" width="290" height="290">';

        // var_dump($all2->items);
        // 
        $file_name = "test.txt";
        $path = Config::get('constants.LOGISTIC_SIG_PATH');
        $upload_file_succ = file_put_contents($path ."/". $file_name, ($all2->username));
        exit();
    }

    public function anyTransactionstatus()
    {
        $data = LogisticTransaction::api_transaction_status();

        return View::make('logistic.logistic_app_view')->with('data', $data);
    }
    
    public function anyTrackingjms()
    { 
        
        // if(!empty(Input::get('g-recaptcha-response')))
        //     $captcha=Input::get('g-recaptcha-response');
        // else
        //     $captcha = false;
            
        // if(!$captcha){
        //     return false;
        //     // die;
        // } else{
        //      return $secret = '6LeMZKQUAAAAAGg-Cismh1L3bYqWLE_SxIcp6t5k';
        //      $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=.$secret.&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
        //     if($response.success==false)
        //     { 
        //         return false;
        //         // die;
        //     }

        //     $data = LogisticTransaction::api_trackingNo($get);
            
        //     return $data;
        // }
        
        $data = LogisticTransaction::api_trackingNo($get);
            
        return $data;
        
        //return View::make('logistic.logistic_app_view')->with('data', $data);
    }
    
   public function anyTracking()
    {
        // if(!empty(Input::get('g-recaptcha-response')))
        //     $captcha=Input::get('g-recaptcha-response');
        // else
        //     $captcha = false;
            
        // if(!$captcha){
        //     return false;
        //     die;
        // } else{
            
        //     return $secret = '6LeMZKQUAAAAAGg-Cismh1L3bYqWLE_SxIcp6t5k';
        //     $response=file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=.$secret.&response=".$captcha."&remoteip=".$_SERVER['REMOTE_ADDR']);
            
        //     if($response.success==false)
        //     {
        //         return false;
        //         die;
        //     }
        //     //  die;
        //     $data = LogisticTransaction::api_trackingNo($get);
            
        //     return $data;
        // }
        
        $data = LogisticTransaction::api_trackingNo($get);
            
        return $data;
        
        //return View::make('logistic.logistic_app_view')->with('data', $data);
    }

    public function anyBatchstatus()
    {
        $data = LogisticBatch::api_batch_status();
        $headerResponse = array(
            "isSuccess" => 'true',
            "message" => 'Success'
        );
        $data = array_merge($headerResponse,$data);

        return View::make('logistic.logistic_app_view')->with('data', $data);
    }

    public function anyDeliverytime()
    {
        $data = LogisticTItem::api_delivery_time();
        $headerResponse = array(
            "isSuccess" => 'true',
            "message" => 'Success'
        );
        $data = array_merge($headerResponse,$data);

        return View::make('logistic.logistic_app_view')->with('data', $data);
    }
    
    // REMINDER NOTE : will/Need revamp again this is adhoc task.
    public function anyAssignbyqrcode(){
        
        try{
            
            $do_number = Input::get('do_no');
            $driver_id= Input::get('driver_id');


            $item_ids = [];
            $item_qty_assign = [];
            
            $LogisticTransaction = LogisticTransaction::where("do_no",$do_number)->first();
            $logistic_id = $LogisticTransaction->id;
            $LogisticTItem = LogisticTItem::where("logistic_id",$logistic_id)->get();

            // preparing data //

            foreach ($LogisticTItem as $keyItem => $valueItem) {
                $item_ids[] = $valueItem->id;
                $item_qty_assign[] = $valueItem->qty_order;
            }

            $get    = array();
            $get['cms'] = 0;
            $get['logistic_id'] = $logistic_id;
            $get['item_id'] = $item_ids;
            $get['qty_assign'] = $item_qty_assign;
            $get['driver_id'] = $driver_id;

            if(Input::has('username')){
                $get['username'] = trim(Input::get('username'));
            }else{
                $get['username'] = 'API_update';
            }
            
                $data = LogisticTransaction::api_transaction_detail($get);
            return $data;

        } catch (Exception $ex) {
   
            $headerResponse = array(
                "isSuccess" => 'false',
                "message" => $ex->getMessage()
            );

            return $headerResponse;
    }
    
    
    


}

    public function anyAssignbyqrcodeprescan(){
        
        try{
            
            $do_number = Input::get('do_no');
            $driver_id= Input::get('driver_id');


            $item_ids = [];
            $item_qty_assign = [];
            
            $LogisticTransaction = LogisticTransaction::where("do_no",$do_number)->first();
            $logistic_id = $LogisticTransaction->id;
            $LogisticTItem = LogisticTItem::where("logistic_id",$logistic_id)->get();

            // preparing data //

            foreach ($LogisticTItem as $keyItem => $valueItem) {
                $item_ids[] = $valueItem->id;
                $item_qty_assign[] = $valueItem->qty_order;
            }

            $get    = array();
            $get['cms'] = 0;
            $get['logistic_id'] = $logistic_id;
            $get['item_id'] = $item_ids;
            $get['qty_assign'] = $item_qty_assign;
            $get['driver_id'] = $driver_id;

            if(Input::has('username')){
                $get['username'] = trim(Input::get('username'));
            }else{
                $get['username'] = 'API_update';
            }
            
                $data = LogisticTransaction::api_transaction_detail_prebatch($get);
            return $data;

        } catch (Exception $ex) {
   
            $headerResponse = array(
                "isSuccess" => 'false',
                "message" => $ex->getMessage()
            );

            return $headerResponse;
        }
    
    
    


    }

    public function anyDriverstatistic(){
        $username = Input::get('username');
        $status = Input::get('status');
        //sent = 4, sending = 1, returned = 2, pending = 0
        $startDate = Input::get("startDate")." 08:00:00";
        $toDate = Input::get("toDate")." 23:59:59";

        $currentday = date('l'); 

        if($currentday == 'Monday'){
            $yesterday = Date('Y-m-d',strtotime("-2 days"))." 08:00:00";
            $today   = Date('Y-m-d')." 23:59:59";
        }
        else 
        {
            $yesterday = Date('Y-m-d',strtotime("-1 days"))." 08:00:00";
            $today   = Date('Y-m-d')." 23:59:59";
        }
       
        $collectionDriver = array();
        $totalhr = array();

        if ($username !='') {

            $LogisticDriver = LogisticDriver::where("status",1)
                    ->where("username",$username)->get();

        }else{

            $LogisticDriver = LogisticDriver::where("status",1)
                    ->whereNotIn("username",['admin','joshua'])->get();
        }
            
        foreach ($LogisticDriver as $keyDriver => $valueDriver) {
            $totalSent = 0;
            $totalAssign = 0;
                
            if (Input::get("startDate") !='' && Input::get("toDate")!='') {
                
                $array = array();
                $interval = new DateInterval('P1D');
                $format = 'Y-m-d';
                $realEnd = new DateTime($toDate);
                $realEnd->add($interval);

                $period = new DatePeriod(new DateTime($startDate), $interval, $realEnd);
                foreach($period as $date) { 
                    $array[] = $date->format($format); 
                }

                $total2 = array();
                foreach ($array as $key => $value) {

                    $start = $value." 08:00:00";
                    $end = $value." 23:59:59";
                    //accept date
                    $totalHours = LogisticBatch::getTotalHoursWorked($valueDriver->id,$start,$end);

                    if (isset($totalHours)) {

                        $datetime1 = new DateTime($start);
                        $datetime2 = new DateTime($totalHours->accept_date);
                        $interval = $datetime1->diff($datetime2);

                        $total2[] = $interval->format("%H:%I");
                    }else{

                        $total2[] = 0;
                    }
                }

                foreach ($total2 as $time) {
                    list($hour, $minute) = explode(':', $time);
                    $minutes += $hour * 60;
                    $minutes += $minute;
                }

                $hours = floor($minutes / 60);
                $minutes -= $hours * 60;
                $full = sprintf('%02dhr %02dmin', $hours, $minutes);

                //assign date
                $totalAssign = LogisticBatch::getTotalAssignedDriver(0,$valueDriver->id,$startDate,$toDate);
                //accept date
                $totalSending = LogisticBatch::getTotalStatusByDriver(1,$valueDriver->id,$startDate,$toDate);
                $totalReturned = LogisticBatch::getTotalStatusByDriver(2,$valueDriver->id,$startDate,$toDate);
                $totalUndelivered = LogisticBatch::getTotalStatusByDriver(3,$valueDriver->id,$startDate,$toDate);
                //accept date
                $totalSent = LogisticBatch::getTotalSentByDriver(4,$valueDriver->id,$startDate,$toDate);
                $totalCancelled = LogisticBatch::getTotalStatusByDriver(5,$valueDriver->id,$startDate,$toDate);
                $driver_img = ( ! empty($valueDriver->filename)) ? Image::link("images/driver/{$valueDriver->filename}") : '';
                if ($status !='') {
                    $details = LogisticBatch::getDriverStatusDetails($status,$valueDriver->id,$startDate,$toDate);
                }
                
                $d1 = new DateTime($startDate);
                $r1 = $d1->format('d F Y');
                $d2 = new DateTime($toDate);
                $r2 = $d2->format('d F Y');
                $date_reported = "Report on ". $r1." - ".$r2; 

            }else{

                $array = array();
                $interval = new DateInterval('P1D');
                $format = 'Y-m-d';
                $realEnd = new DateTime($today);
                $realEnd->add($interval);

                $period = new DatePeriod(new DateTime($yesterday), $interval, $realEnd);
                foreach($period as $date) { 
                    $array[] = $date->format($format); 
                }
                $total2 = array();
                foreach ($array as $key => $value) {

                    $start = $value." 08:00:00";
                    $end = $value." 23:59:59";
                    $totalHours = LogisticBatch::getTotalHoursWorked($valueDriver->id,$start,$end);

                    if (isset($totalHours)) {

                        $datetime1 = new DateTime($start);
                        $datetime2 = new DateTime($totalHours->accept_date);
                        $interval = $datetime1->diff($datetime2);

                        $total2[] = $interval->format("%H:%I");
                    }else{

                        $total2[] = 0;
                    }
                }

                foreach ($total2 as $time) {
                    list($hour, $minute) = explode(':', $time);
                    $minutes += $hour * 60;
                    $minutes += $minute;
                }

                $hours = floor($minutes / 60);
                $minutes -= $hours * 60;
                $full = sprintf('%02dhr %02dmin', $hours, $minutes);

                //assign date
                $totalAssign = LogisticBatch::getTotalAssignedDriver(0,$valueDriver->id,$yesterday,$today);
                //accept date
                $totalSending = LogisticBatch::getTotalStatusByDriver(1,$valueDriver->id,$yesterday,$today);
                $totalReturned = LogisticBatch::getTotalStatusByDriver(2,$valueDriver->id,$yesterday,$today);
                $totalUndelivered = LogisticBatch::getTotalStatusByDriver(3,$valueDriver->id,$yesterday,$today);
                //accept date
                $totalSent = LogisticBatch::getTotalSentByDriver(4,$valueDriver->id,$yesterday,$today);
                $totalCancelled = LogisticBatch::getTotalStatusByDriver(5,$valueDriver->id,$yesterday,$today);
                $driver_img = ( ! empty($valueDriver->filename)) ? Image::link("images/driver/{$valueDriver->filename}") : '';
                if ($status !='') {
                    $details = LogisticBatch::getDriverStatusDetails($status,$valueDriver->id,$yesterday,$today);
                }
                
                $d1 = new DateTime($today);
                $r1 = $d1->format('d F Y');
                $date_reported = "Report on ". $r1; 

            }

                if (isset($details)) {
                    $driver_temp = array(
                        "name"=> $valueDriver->name,
                        "username"=> $valueDriver->username,
                        "image"=> $driver_img,
                        "date_reported"=> $date_reported,
                        "assigned" => $totalAssign,
                        "sending" => $totalSending,
                        "returned" => $totalReturned,
                        "undelivered"=>$totalUndelivered,
                        "sent"=>$totalSent,
                        "cancelled"=>$totalCancelled,
                        "total_hours" => $full,
                        "details"=> $details,
                    );
                }else{
                    $driver_temp = array(
                        "name"=> $valueDriver->name,
                        "username"=> $valueDriver->username,
                        "image"=> $driver_img,
                        "date_reported"=> $date_reported,
                        "assigned" => $totalAssign,
                        "sending" => $totalSending,
                        "returned" => $totalReturned,
                        "undelivered"=>$totalUndelivered,
                        "sent"=>$totalSent,
                        "cancelled"=>$totalCancelled,
                        "total_hours" => $full,
                    );
                }
                

            array_push($collectionDriver, $driver_temp);

        }

        $data['batch'] = $collectionDriver;


        return $data;
        
    } 
        /*
     * @Desc : Able to upload images
     */
    public function anyUploadimage(){
        
        $isSuccess = true;
        $message = 'Image Uploaded!';
        
        try{
            
            $batchId = Input::get("batch_id");
            $username = Input::get("username");
            $delivery_img = Input::file('delivery_img');
            
            if(Input::has('latitude')){
                $latitude = Input::get('latitude') != '' ? Input::get('latitude') : '';
            }else{
                $latitude = '';
            }
            
            if(Input::has('longitude')){
                $longitude = Input::get('longitude') != '' ? Input::get('longitude') : '';
            }else{
                $longitude = '';
            }
            
            
            
            // echo "<pre>";
            // print_r($delivery_img);
            // echo "</pre>";
            if(count($delivery_img) > 0 ){
                
                foreach ($delivery_img as $key => $value) {
                    
                    $dateTime = date("YmdHis").uniqid();
                    $file_name = $batchId."_".$dateTime."_".uniqid().".".$value->getClientOriginalExtension(); 
                    $path = Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH');
                    
                    $upload_file_succ = $value->move($path, $file_name);
    
                    $LogisticBatchImage = new LogisticBatchImage;
                    $LogisticBatchImage->batch_id = $batchId;
                    $LogisticBatchImage->filename = $file_name;
                    $LogisticBatchImage->latitude = $latitude;
                    $LogisticBatchImage->longitude = $longitude;
                    $LogisticBatchImage->created_by = $username;
                    $LogisticBatchImage->save();

                }
                
                
                 
            }else{
                $isSuccess = false;
                $message = 'No Image attached!';
            }
              
       
        } catch (Exception $ex) {
            $isSuccess = false;
            $message = 'Uploaded failed!';
            
        }finally{
            
            $Response = array(
                "isSuccess" => $isSuccess,
                "message" => $message
            );
            
            return $Response;
            
        }
        
    }
    
    
        /*
     * @Desc : Able to upload images
     */
    public function anyUpload(){
        
       $isSuccess = true;
        $message = 'Image Uploaded!';
        
        try{
            
            $batchId = Input::get("batch_id");
            $username = Input::get("username");
            $delivery_img = Input::file('delivery_img');
            
            if(Input::has('latitude')){
                $latitude = Input::get('latitude') != '' ? Input::get('latitude') : '';
            }else{
                $latitude = '';
            }
            
            if(Input::has('longitude')){
                $longitude = Input::get('longitude') != '' ? Input::get('longitude') : '';
            }else{
                $longitude = '';
            }
            
            if(count($delivery_img) > 0 ){
                
                $dateTime = date("YmdHis");
                $file_name = $batchId."_".$dateTime."_".uniqid().".".$delivery_img->getClientOriginalExtension(); 
                $path = Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH');
                
                $upload_file_succ = $delivery_img->move($path, $file_name);

                $LogisticBatchImage = new LogisticBatchImage;
                $LogisticBatchImage->batch_id = $batchId;
                $LogisticBatchImage->filename = $file_name;
                $LogisticBatchImage->latitude = $latitude;
                $LogisticBatchImage->longitude = $longitude;
                $LogisticBatchImage->created_by = $username;
                $LogisticBatchImage->save();
                 
            }else{
                $isSuccess = false;
                $message = 'No Image attached!';
            }
              
       
        } catch (Exception $ex) {
            $isSuccess = false;
            $message = 'Uploaded failed!';
            
        }finally{
            
            $Response = array(
                "isSuccess" => $isSuccess,
                "message" => $message
            );
            
            return $Response;
            
        }
        
    }
    
    
    /*
     * Get list of urgent delivery
     */
    public function anyUrgentlogistic(){
        
        
        $masterList = array();
        
        $urgentList = DB::table('logistic_transaction AS LT')
            ->leftJoin('jocom_task as JT','JT.transaction_id','=','LT.transaction_id')
            ->select('JT.*','LT.id AS LogisticID')
            ->where("LT.is_urgent",1)
            ->where("JT.is_urgent",1)
            ->whereIn('LT.status', [0,1,2,3,4])
            ->get();
        
        foreach ($urgentList as $key => $value) {
            
            $subLine = array(
                "transaction_id" => $value->transaction_id,
                "logistic_id" => $value->LogisticID,
                "status" => LogisticTransaction::get_status($value->status) ,
                "message" => $value->description
            );
            
            array_push($masterList, $subLine);

        }
        
        $response = array(
            "status" => '1',
            "urgent" => $masterList
        );
        
        return $response;
    
        
        
    }
    
    /*
     * Generate Manifest
     */
    
    public function anyGeneratemanifest(){
        
        /*
         * ASKBBB20289310
         */
        
        try{
            
        $requestCode = Input::get("code");
        
        $requestCode = 'ASKBBB20289310';
        
        if($requestCode != 'ASKBBB20289310'){
            // dont process
        }else{
            
            // Check How Many Type of Country 
            
            $collectionData = DB::table('jocom_international_logistic AS JIL')->select(array(
                    'JIL.deliver_to_country',  
                ))
                ->where('JIL.status', '=',3)
                ->where('JIL.manifest_id', '=','')
                ->groupBy('JIL.deliver_to_country')
                ->get();
           
            if($collectionData){
                
                foreach ($collectionData as $countryID) {
                    
                    switch ($countryID->deliver_to_country) {
                        case 156: // CHINA
                            $countryCode = 'MFCHN';
                            break;
                        
                        case 458: // MALAYSIA
                            $countryCode = 'MFMAS';
                            break;
                    }
                    
                    $manifestNumber = $countryCode.DATE("Ymdhis");
                    
                 
                    // Register Manifest ID 
                    $IntManifest = new InternationalLogisticManifest();
                    $IntManifest->manifest_id = $manifestNumber;
                    $IntManifest->save();
             
                    $updateWeight = DB::table('jocom_international_logistic')
                            ->where("deliver_to_country",$countryID->deliver_to_country)
                            ->where("manifest_id",'')
                            ->where("weight",'>',0)
                            ->where("status",3)->update(
                            [
                                'manifest_id' => $manifestNumber,
                                'updated_at' => DATE("Y-m-d h:i:s"),
                                'updated_by' => 'SYSTEM'
                            ]); 
                    
                    
                }
                
            }else{
                // Nothing to Generate
            }
            
            
            
        }
     
        } catch (Exception $ex) {
            echo $ex->getMessage();
        } finally {
            
            echo "DONE";
            
        }
        
    }

    public function anyOnlinecampaign(){
       
        //  dd(Input::all());
            // echo Input::get('email');
                    $OnlineCampaign = new OnlineCampaignUsers;
                    $OnlineCampaign->name = Input::get('email');
                    $OnlineCampaign->email = Input::get('name');
                    $OnlineCampaign->contact_no = Input::get('contact_no');
                    $OnlineCampaign->created_by = "API";
                    $OnlineCampaign->save();
        
       
        //   echo 'In';
        
    }
    
    /* Function: anyReturnbatchstatus
        Description : Returned logistic batch status 
        _INPUT_ :
        
         Batch ID : Logistic Batch ID  
         Return status : Status ID to be updated
         Others : Other batch status details 
    */

    public function anyReturnbatchstatus(){

        $ApiLog = new ApiLog ;
        $ApiLog->api = 'LOGISTIC_BATCH_RETURN';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();
        
        $isSuccess = true;
        $batchstatus = 2;

        try{

            $batchId = Input::get("batch_id");
            $driver_id = Input::get("driver_id");
            $returnstatus = Input::get("return_status");
            $others = Input::get("others");

            $LogisticDriver = LogisticDriver::where("status",1)
                    ->where("id",$driver_id)->first();

            $logistcbatch = LogisticBatch::where('id',$batchId)
                                          ->first();

            $LogisticTransaction = LogisticTransaction::where("id",$logistcbatch->logistic_id)->first();        

            


            // Reassign batch details 


                $batch = LogisticBatch::find($batchId);

                if ($batch->status != $batchstatus)
                {

                    $LogisticT = LogisticTransaction::find($batch->logistic_id);
                    $ori_status = $batch->status;
                    $new_status = $batchstatus;
                    $trans_id = $LogisticT->transaction_id;
                    $batch_id = $batch->id;
                    $type = 'Batch';

                    Transaction::statusHistory($ori_status,$new_status, $trans_id, $batch->logistic_id,$batch_id,$type, $LogisticDriver->username);

                }

                $transaction = LogisticTransaction::find($batch->logistic_id);

                switch ($batchstatus) {
                    //Returned
                    case '2':
                        $transaction->status = 3;
                        break;
                    // Undelivered
                    case '3':
                        $transaction->status = 1;
                        break;
                }

                if ($batchstatus!=$transaction->status) {
                    $status = LogisticTransaction::find($batch->logistic_id);
                    $ori_status= $status->status;
                    $new_status = $transaction->status;
                    $trans_id=$transaction->transaction_id;
                    $logistic_id=$batch->logistic_id;
                    $type='batchTrans';

                    Transaction::statusHistory($ori_status,$new_status, $trans_id, $logistic_id,$batch_id,$type,$LogisticDriver->username);

                }
                
                $transaction->modify_by = $LogisticDriver->username;
                $transaction->modify_date = date('Y-m-d H:i:s');
                $transaction->save();

                $batch_item = LogisticBatchItem::where('batch_id', '=', $batchId)->get();

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

                    switch ($batchstatus)
                    {
                        case '2':
                            $new_status = 'Returned [API]';
                            break;
                        case '3':
                            $new_status = 'Undelivered [API]';
                            break;
                        case '5':
                            $new_status = 'Cancelled [API]';
                            break;
                        default:
                            break;
                }

                    //passing var
                    $username = $LogisticDriver->username;
                    $date = date('Y-m-d H:i:s');
                    $HistoryStatus = $new_status;
                    $TransID = $Trans_ID->transaction_id;
                    $qty_assignHistory = $item->qty_assign;
                    $qty_pickupHistory = $item->qty_pickup;
                    $product_name = $productList->name;

                    LogisticBatch::getInventoryHistory($username,$date,$HistoryStatus,$TransID,$qty_assignHistory,$qty_pickupHistory,$product_name);

                }

                $batch->modify_by    = $LogisticDriver->username;
                $batch->status_return = $returnstatus; 
                $batch->status_return_others = $others; 
                $batch->modify_date  = date('Y-m-d H:i:s');
                $batch->status = $batchstatus;    
                $batch->save();
             // Reassign batch details 


            // $LogisticTransactionUpdate = LogisticTransaction::find($logistcbatch->logistic_id);
            // $LogisticTransactionUpdate->status = 3;
            // $LogisticTransactionUpdate->modify_by = $LogisticDriver->name;
            // $LogisticTransactionUpdate->modify_date = date('Y-m-d H:i:s');
            // $LogisticTransactionUpdate->save();

            // $returnbatch = LogisticBatch::find($batchId);
            // $returnbatch->status = 2;
            // $returnbatch->status_return = $returnstatus;
            // $returnbatch->status_return_others = $others;
            // $returnbatch->modify_by = $LogisticDriver->name;
            // $returnbatch->modify_date = date('Y-m-d H:i:s');
            // $returnbatch->save();


            $BatchReturnLogs = new LogisticBatchreturnReasonsLogs;
            $BatchReturnLogs->batch_id = $batchId;
            $BatchReturnLogs->transaction_id = $LogisticTransaction->transaction_id;
            $BatchReturnLogs->driver_id = $driver_id;
            $BatchReturnLogs->return_status = $returnstatus;
            $BatchReturnLogs->return_others = $others;
            $BatchReturnLogs->created_at = date('Y-m-d H:i:s');
            $BatchReturnLogs->created_by = $LogisticDriver->username;
            $BatchReturnLogs->save();

            $message = "Returned batch is Succesfully Updated";


        } catch (Exception $ex) {
            // echo $ex->getMessage();
            $isSuccess = false;
            $message = 'Returned batch is not Updated.';
            
        }finally{
            
            $Response = array(
                "isSuccess" => $isSuccess,
                "message" => $message
            );
            
            return $Response;
            
        }


    }
    
    public function anyLeaderboard(){

        $data = array();
        try{

        $result = DB::table('jocom_transaction AS JT')
                        ->select('JT.buyer_id','JT.buyer_username','JU.email',DB::raw('COUNT(JT.buyer_username) as notopup'),DB::raw('sum(JT.total_amount) as totalvalue'))
                        ->leftJoin('jocom_transaction_details AS JTD', 'JTD.transaction_id','=','JT.id')
                        ->leftJoin('jocom_user AS JU','JU.id','=','JT.buyer_id')
                        ->whereIn('JTD.product_id',[29635,29636,29637,29757,29885,29886])
                        ->where('JT.status','=','completed')
                        ->where('JT.transaction_date','<=','2021-03-31 23:23:59')
                        ->groupBy('JT.buyer_username')
                        ->orderBy('notopup','DESC')
                        //  ->orderBy('totalvalue','DESC')
                        ->get();

        if(count($result)>0){

            foreach ($result as $value) {
                
                $noval = $value->totalvalue;
                $notop = $value->notopup;
                $notrans = self::GetTransaction($value->buyer_username);
                
                if($value->buyer_username == 'maruthujocom'){
                    $noval = number_format(4800,2);
                    $notrans = 26;
                    $notop = 5;
                }

                $tempArray = array('username' => $value->buyer_username, 
                                   'email' => $value->email,   
                                   'no_of_transaction' => $notrans,   
                                   'no_of_topup' => $notop, 
                                   'total_purchased_value' => $noval, 

                                );

                array_push($data, $tempArray);
                
                $keys = array_column($data, 'no_of_topup');
                array_multisort($keys, SORT_DESC, $data);

            }

        }


        $response = array(
                        "data" => $data
                    );
        
        } catch (Exception $ex) {
            echo $ex->getMessage();
        } finally {
            
            return $response;
            
        }
        

    }
    
    public static function GetTransaction($user_id){

        $nooftransaction = 0; 

        $result = DB::table('jocom_transaction AS JT')
                      ->leftJoin('point_transactions AS PT','PT.transaction_id','=','JT.id')
                      ->where('JT.buyer_username','=',$user_id)
                      ->where('JT.status','=','completed')
                      ->where('PT.point','<',0)    
                      ->count();

        return $result;

    }

}
?>