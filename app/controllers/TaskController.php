<?php
/*
 * This Task Controller handle task assignment 
 */

use Helper\ImageHelper as Image;

class TaskController extends BaseController {

    private function TASKCOUNT(){
        $totalTodayTask = DB::table('jocom_task')
                ->where('assign_to','=', Session::get("user_id"))
                ->whereDate('created_at','=', date('Y-m-d'))
                ->count();
        
        $totalPendingTask = DB::table('jocom_task')
                ->where('assign_to','=', Session::get("user_id"))
                ->where('status','=', 0)
                ->count();
        
        $TotalResolvedTask = DB::table('jocom_task')
                ->where('status','=', 1)
                ->whereDate('updated_at', '>=', date('Y-m-d', strtotime("-1 week")))
                ->count();
        
        $TotalAssignedTask = DB::table('jocom_task')
                ->where('assign_by','=', Session::get("user_id"))
                ->whereDate('updated_at', '>=', date('Y-m-d', strtotime("-1 week")))
                ->whereIn('status', [0, 1])
                ->count();

        return [
            'totalTodayTask' => $totalTodayTask,
            'totalPendingTask' => $totalPendingTask,
            'TotalResolvedTask' => $TotalResolvedTask,
            'TotalAssignedTask' => $TotalAssignedTask,
        ];
    }

    public function index(){
        $count = $this->TASKCOUNT();
        $totalTodayTask = $count['totalTodayTask'];
        $totalPendingTask = $count['totalPendingTask'];
        $TotalResolvedTask = $count['TotalResolvedTask'];
        $TotalAssignedTask = $count['TotalAssignedTask'];
        $ThirdPatryPlatform = ThirdPartyPlatform::$data;
        
        return View::make('task.index')->with("TotalTodayTask",$totalTodayTask)
                ->with("TotalPendingTask",$totalPendingTask)
                ->with("TotalResolvedTask",$TotalResolvedTask)
                ->with("TotalAssignedTask",$TotalAssignedTask)
                ->with("ThirdPatryPlatform", $ThirdPatryPlatform);
        
    }

    public function create(){
        $count = $this->TASKCOUNT();
        $totalTodayTask = $count['totalTodayTask'];
        $totalPendingTask = $count['totalPendingTask'];
        $TotalResolvedTask = $count['TotalResolvedTask'];
        $TotalAssignedTask = $count['TotalAssignedTask'];
        $ThirdPatryPlatform = ThirdPartyPlatform::$data;
        
        return View::make('task.create')->with("TotalTodayTask",$totalTodayTask)
                ->with("TotalPendingTask",$totalPendingTask)
                ->with("TotalResolvedTask",$TotalResolvedTask)
                ->with("TotalAssignedTask",$TotalAssignedTask)
                ->with("ThirdPatryPlatform", $ThirdPatryPlatform);
        
    }

    public function report(){
        $count = $this->TASKCOUNT();
        $totalTodayTask = $count['totalTodayTask'];
        $totalPendingTask = $count['totalPendingTask'];
        $TotalResolvedTask = $count['TotalResolvedTask'];
        $TotalAssignedTask = $count['TotalAssignedTask'];
        
        return View::make('task.report')->with("TotalTodayTask",$totalTodayTask)
                ->with("TotalPendingTask",$totalPendingTask)
                ->with("TotalResolvedTask",$TotalResolvedTask)
                ->with("TotalAssignedTask",$TotalAssignedTask);
        
    }

    public function details($id){
        $count = $this->TASKCOUNT();
        $totalTodayTask = $count['totalTodayTask'];
        $totalPendingTask = $count['totalPendingTask'];
        $TotalResolvedTask = $count['TotalResolvedTask'];
        $TotalAssignedTask = $count['TotalAssignedTask'];
        $ThirdPatryPlatform = ThirdPartyPlatform::$data;

        $result = DB::table('jocom_task')->where('id','=', $id)->first();
        if($result){
            $save_allow = ((int)$result->assign_to == Session::get("user_id") || (int)$result->assign_by == Session::get("user_id") || in_array(Session::get("role_id"), [1, 2, 11, 23]) ? 1 : 0);
        }else{
            $save_allow = 0;
        }

        $sysAdmin = User::where('active_status', '=', 1)->select('full_name','id')->get()->toArray();

        return View::make('task.details')->with("TotalTodayTask", $totalTodayTask)
                ->with("TotalPendingTask", $totalPendingTask)
                ->with("TotalResolvedTask", $TotalResolvedTask)
                ->with("TotalAssignedTask", $TotalAssignedTask)
                ->with("save_allow", $save_allow)
                ->with("id", $id)
                ->with("ThirdPatryPlatform", $ThirdPatryPlatform)
                ->with("AdminUser", $sysAdmin);
    }

    public function reportprev() {
        // datatable use for the preview report
        $input = array_values((array)Input::json())[0];

        $tasks = DB::table('jocom_task AS JT')->select(['JT.id', 'JT.transaction_id', 'JT.task_id_number','JT.title','JT.description','JT.assign_by','JSA.full_name AS AssignByName','JT.assign_to','JST.full_name AS AssignToName','JT.is_urgent','JT.status','JT.activation','JT.created_at','JTC.label','JT.priority', 'JT.updated_at'])
        ->leftJoin('jocom_task_category AS JTC', 'JTC.id', '=', 'JT.category_id')
        ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JT.assign_by')
        ->leftJoin('jocom_sys_admin AS JST', 'JST.id', '=', 'JT.assign_to');

        if($input['Rcategory']) $tasks->where('JT.title', 'like', '%' . $input['Rcategory'] . '%');
        if($input['start_date']) $tasks->where('JT.due_date', '>=', $input['start_date']);
        if($input['end_date']) $tasks->where('JT.due_date', '<=', $input['end_date']);
        
        $tasks->where('JT.activation', '=', 1)->orderBy('JT.id','DESC');

        return Datatables::of($tasks)->make(true);
    }


    public function reportGenerate(){
        $tasks = DB::table('jocom_task AS JT')->select(['JT.id', 'JT.transaction_id', 'JT.task_id_number','JT.title','JT.description','JT.assign_by','JSA.full_name AS AssignByName','JT.assign_to','JST.full_name AS AssignToName','JT.is_urgent','JT.status','JT.activation','JT.created_at','JTC.label','JT.priority', 'JT.updated_at'])
        ->leftJoin('jocom_task_category AS JTC', 'JTC.id', '=', 'JT.category_id')
        ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JT.assign_by')
        ->leftJoin('jocom_sys_admin AS JST', 'JST.id', '=', 'JT.assign_to');

        if(Input::get('Rcategory')) $tasks->where('JT.title', 'like', '%' . Input::get('Rcategory') . '%');
        if(Input::get('start_date')) $tasks->where('JT.due_date', '>=', Input::get('start_date'));
        if(Input::get('end_date')) $tasks->where('JT.due_date', '<=', Input::get('end_date'));
        
        $tasks->where('JT.activation', '=', 1)->orderBy('JT.id','DESC');
        $result = $tasks->get();

        $filename   = "taskreport_" . uniqid() . "_" . time() . ".csv";
        $file       = Config::get('constants.REPORT_PATH') . $filename;
        $fp         = fopen($file, "w");

        header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
        header('Expires: 0');
        header('Pragma: public');
        header("Content-type: text/csv");
        header("Content-Disposition: attachment; filename=$filename");

        foreach ($result as $key => $value) {
            fputcsv($fp, (array)$value, ",", "\"");
            echo implode(', ', (array)$value);
        }

        fclose($fp);
    }


    public function tasks($type) {
        
        // Get Orders
        $user_id = Session::get("user_id");
        switch ($type) {
            case 1:
                
                $tasks = DB::table('jocom_task AS JT')->select(['JT.id', 'JT.transaction_id', 'JT.task_id_number','JT.title','JT.description','JT.assign_by','JSA.full_name AS AssignByName','JT.assign_to','JST.full_name AS AssignToName','JT.is_urgent','JT.status','JT.activation','JT.created_at','JT.created_by','JTC.label','JT.priority', 'JT.updated_at', 'JT.platform', 'JT.vendor'])
                ->leftJoin('jocom_task_category AS JTC', 'JTC.id', '=', 'JT.category_id')
                ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JT.assign_by')
                ->leftJoin('jocom_sys_admin AS JST', 'JST.id', '=', 'JT.assign_to')
                ->where('JT.assign_to', '=', $user_id)
                ->orderBy('JT.id','DESC');

                if(!Input::get('status')) $tasks->where('JT.status', '=', 0);

                break;
            
            case 2:
                
                $tasks = DB::table('jocom_task AS JT')->select(['JT.id', 'JT.transaction_id', 'JT.task_id_number','JT.title','JT.description','JT.assign_by','JSA.full_name AS AssignByName','JT.assign_to','JST.full_name AS AssignToName','JT.is_urgent','JT.status','JT.activation','JT.created_at','JT.created_by','JTC.label','JT.priority', 'JT.updated_at', 'JT.platform', 'JT.vendor'])
                ->leftJoin('jocom_task_category AS JTC', 'JTC.id', '=', 'JT.category_id')
                ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JT.assign_by')
                ->leftJoin('jocom_sys_admin AS JST', 'JST.id', '=', 'JT.assign_to')
                ->where('JT.created_by', '=', $user_id)
                ->orderBy('JT.id','DESC');

                break;
                
            case 3:
                
                $tasks = DB::table('jocom_task AS JT')->select(['JT.id', 'JT.transaction_id', 'JT.task_id_number','JT.title','JT.description','JT.assign_by','JSA.full_name AS AssignByName','JT.assign_to','JST.full_name AS AssignToName','JT.is_urgent','JT.status','JT.activation','JT.created_at','JT.created_by','JTC.label','JT.priority', 'JT.updated_at', 'JT.platform', 'JT.vendor'])
                ->leftJoin('jocom_task_category AS JTC', 'JTC.id', '=', 'JT.category_id')
                ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JT.assign_by')
                ->leftJoin('jocom_sys_admin AS JST', 'JST.id', '=', 'JT.assign_to')
                ->where('JT.activation', '=', 1)
                ->orderBy('JT.id','DESC');

                break;

            default:
                break;
        }

        if(Input::get('status')){
            $tasks->whereIn('JT.status', Input::get('status'));
        }

        if(Input::get('vendor')){
            $temp_data = explode('.', Input::get('vendor'));
            $platform = $temp_data[0];
            $vendor = $temp_data[1];
            $tasks->where('JT.platform', $platform);
            $tasks->where('JT.vendor', $vendor);
        } else if(Input::get('platform')){
            $tasks->whereIn('JT.platform', Input::get('platform'));
        }

        $platform = ThirdPartyPlatform::$data;
        $userAdmin = DB::table('jocom_sys_admin')->where('active_status', 1)->lists('full_name', 'id');

        return Datatables::of($tasks)
        ->edit_column('vendor', function($row) use ($platform) {
            return $platform[$row->platform][$row->vendor]['name'];
        })
        ->edit_column('created_by', function($row) use ($userAdmin) {
            return $userAdmin[$row->created_by];
        })
        ->make(true);
    }
    
    /*
     * Save new task
     */
    public function saveTask() {
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";
        $last_id = 0;

        try {
            
            DB::beginTransaction();
            
            $title = Input::get('title');
            $assign_to = Input::get('assign_to');
            $category = Input::get('category');
            $due_date = Input::get('due_date');
            $description = Input::get('description');
            $transaction_id = Input::get('transaction_id');
            $is_urgent = Input::get('is_urgent');
            $priority = Input::get('priority');
            
            $running_number = DB::table('jocom_running')->select('*')->where('value_key', '=', 'task')->first();

            $taskNumber = "TS".str_pad($running_number->counter + 1,5,"0",STR_PAD_LEFT);
            $NewRunner = Running::find($running_number->id);
            $NewRunner->counter = $running_number->counter + 1;
            $NewRunner->save();

            $AssignByUser = User::find(Session::get("user_id"));
            $AssignToUser = User::find($assign_to);

            $Task = new Task;
            $Task->task_id_number = $taskNumber;
            $Task->title = $title;
            $Task->description = $description;
            $Task->platform = Input::get('platform');
            $Task->vendor = Input::get('vendor');
            $Task->category_id = $category;
            $Task->due_date = $due_date;
            $Task->assign_to = $assign_to;
            $Task->assign_to_type = 1;
            $Task->assign_by = Session::get("user_id");
            $Task->transaction_id = $transaction_id;
            $Task->is_urgent = $is_urgent;
            $Task->priority = $priority;
            $Task->created_by = Session::get("user_id");
            $Task->updated_by = Session::get("user_id");
           
            if($Task->save()){
                $last_id = $Task->id;
                if($transaction_id != "" ){
                    $Transaction = Transaction::where("id",$transaction_id)->first();
                    if ($Transaction === null) {
                        throw new Exception('No transaction found for order id: '.$transaction_id);
                    }else{
                        $LogisticTransaction = LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        if ($LogisticTransaction === null) {
                            throw new Exception('No Logistic found for order id: '.$transaction_id);
                        }else{
                            if($is_urgent == 1){
                                $LogisticTransaction->is_urgent = $is_urgent;
                                $LogisticTransaction->save();
                            }

                            if(Input::get('logisticTransRemark')){
                                Input::replace(['logTransID' => $LogisticTransaction->id, 'remarks' => Input::get('logisticTransRemark')]);
                                self::updateLogiTrans();
                            }

                            // Logistic Transaction Attachment
                            $files = Input::file('images');
                            if ($files) {
                                foreach($files as $indx => $value) {
                                    $image = $files[$indx];
                                    if (!empty($image)) {
                                        $attach =  date("Ymdhis") . "-" . uniqid();
                                        $images = $attach . '.' . $image->getClientOriginalExtension();
                                        $user = Session::get('username');
                                        $date = date('Y-m-d H:i:s');
                                        $mim = $image->getMimeType();
                                        $path = public_path('logistic/images');  
                                        $image->move($path, $images);
                                        DB::table('logistic_batch_attachment')->insertGetId([
                                            'batch_id'      =>  $LogisticTransaction->id,
                                            'attachment'    =>  $images,
                                            'path_to_file'  =>  $path,
                                            'mime'          =>  $mim,
                                            'created_by'    =>  $user,
                                            'created_at'    =>  $date,
                                        ]);
                                    }
                                }
                            }
                        }
                    }
                }
            }else{
                throw new Exception('Failed to save!');
            }
        } catch (Exception $ex) {
            $is_error = true;
            $message = $ex->getMessage();
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                NotificationController::saveNotification(NotificationController::NOTI_ASSIGN_TASK, $AssignByUser->full_name, Session::get("user_id"), $taskNumber, $Task->id, $assign_to);
                // SEND EMAIL NOTIFICATION //
                $data = array(
                    'emailType'  => 'ASGN',
                    'taskNumber'  => $taskNumber,
                    'assignedBy'  => $AssignByUser->full_name,
                    'assignedTo'  => $AssignToUser->full_name,
                    "description" => $description,
                    'order_id'  => $transaction_id
                );
                $subject = 'New Task :' . $taskNumber;

                Mail::send('emails.tasknotification', $data, function($message) use ($AssignToUser,$subject){
                    $message->from('support@jocom.my', 'JOCOM');
                    $message->to($AssignToUser->email, $AssignToUser->full_name)->subject($subject);
                });
                // SEND EMAIL NOTIFICATION //
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data, 'last_id' => $last_id);
        return $response;
    }
    
    public function updateTask() {
        $data = array();
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            DB::beginTransaction();
            
            $task_id = Input::get('task_id');
            $transaction_id = Input::get('transaction_id');
            $remarks = Input::get('remarks');
            $assign_to = Input::get('assign_to');
            
            $Task = Task::find($task_id);
            $Task->title = Input::get('task_title');
            $Task->description = Input::get('description');
            $Task->due_date = Input::get('due_date');
            $Task->transaction_id = $transaction_id;
            $Task->is_urgent = Input::get('is_urgent');
            $Task->priority = Input::get('priority');
            $Task->platform = Input::get('platform');
            $Task->vendor = Input::get('vendor');
            if($assign_to){
                $Task->assign_to = $assign_to;
                $Task->assign_by = Session::get("user_id");
            }
            if($remarks){
                $Task->resolved_action = $remarks;
            }
            $Task->updated_by = Session::get("user_id");
            
            if($Task->save()){
                if($transaction_id != "" ){
                    $Transaction = Transaction::where("id", $transaction_id)->first();
                    
                    if ($Transaction === null) {
                        throw new Exception('No transaction found for order id: '.$transaction_id);
                    }else{
                        $LogisticTransaction = LogisticTransaction::where("transaction_id",$transaction_id)->first();
                        if ($LogisticTransaction === null) {
                            throw new Exception('No logistic found for order id: '.$transaction_id);
                        }else{
                            if($is_urgent != ""){
                                $LogisticTransaction->is_urgent = $is_urgent;
                                $LogisticTransaction->save();
                            }
                        }
                    }
                }
            }else{
                throw new Exception('Failed to update');
            }
            
            $data['task_id'] = $Task->id;
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
        return array("RespStatus" => 1, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
    }
    
    public function getCategory() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            $category = DB::table('jocom_task_category AS JTC')->select(array(
                'JTC.id','JTC.label','JTC.description','JTC.status'
                ))
            ->where('JTC.status', '=', 1)
            ->orderBy('JTC.id','DESC')->get();
            $data = $category;
        
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
    
    public function getSysadmin() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            $keyword = str_replace(' ', '', Input::get('keyword'));
            
            if(strlen($keyword) > 0 ){
                $sysAdmin = User::where('active_status', '=', 1)
                ->select('full_name','id')
                ->where('full_name', 'LIKE', '%'.$keyword.'%')->get();
                 
                $data = $sysAdmin;
            }else{
                 $data = array();
            }

            $data = $sysAdmin;
        
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
    
    public function updateTaskStatus() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();

            $action_type = Input::get('action_type');
            $remarks = Input::get('remarks');
            $taskNumber = Input::get('taskNumber');
            $status = Input::get('status');

            $Task = Task::where("task_id_number",$taskNumber)->first();
            $Task->resolved_action = $remarks;
            $Task->status = $status;
            $Task->updated_by = Session::get("username");
            $Task->save();

            // SEND NOTIFCATION //
            $AssignByUser = User::find($Task->assign_by);
            $AssignToUser = User::find($Task->assign_to);

            switch ($status) {
                case 1: // RESOLVED
                    // SEND EMAIL NOTIFICATION //
                    $Emaildata = array(
                        'emailType'   => 'RSVD',
                        'taskNumber'  => $Task->task_id_number,
                        'assignTo'    => $AssignToUser->full_name,
                        'assignBy'    => $AssignByUser->full_name,
                        "description" => $Task->description,
                        'order_id'    => $Task->transaction_id,
                        'remarks'     => $Task->resolved_action
                    );
                    $subject = 'Task :'.$taskNumber. " has marked resolved.";
                    $emailTo = $AssignByUser->email;
                    $emailToName = $AssignByUser->full_name;

                    // SEND EMAIL NOTIFICATION //
                    $typeCode = NotificationController::NOTI_COMPLETED_TASK;
                    if(Session::get("user_id") == $Task->assign_to){
                        NotificationController::saveNotification($typeCode, $AssignToUser->full_name,Session::get("user_id"), $Task->task_id_number, $Task->id, $Task->assign_by);
                    }
                    
                    if(Session::get("user_id") == $Task->assign_by){
                        NotificationController::saveNotification($typeCode, $AssignByUser->full_name,Session::get("user_id"), $Task->task_id_number, $Task->id, $Task->assign_to);
                    }

                    break;
                case 2: // CANCELLED
                    
                    if($Task->is_urgent == 1){
                        
                        $CheckExist = Task::where("transaction_id",$Task->transaction_id)
                                ->where("is_urgent",1)
                                ->where("task_id_number","<>",$Task->task_id_number)
                                ->where("status","=",0)
                                ->first();
                        
                        if ($CheckExist === null) {
                            // Remove logistic from urgent list 
                            $LogisticTransaction = LogisticTransaction::where("transaction_id",$Task->transaction_id)->first();
                            $LogisticTransaction->is_urgent = 0;
                            $LogisticTransaction->save();
                        }
                    }
                    // SEND EMAIL NOTIFICATION //
                    $typeCode = NotificationController::NOTI_CANCEL_TASK;
                    if(Session::get("user_id") != $Task->assign_to){
                        NotificationController::saveNotification($typeCode, $AssignByUser->full_name, Session::get("user_id"), $Task->task_id_number, $Task->id, $Task->assign_to);
                    }
                    
                    $Emaildata = array(
                        'emailType'   => 'CCLD',
                        'taskNumber'  => $Task->task_id_number,
                        'assignTo'    => $AssignToUser->full_name,
                        'assignBy'    => $AssignByUser->full_name,
                        "description" => $Task->description,
                        'order_id'    => $Task->transaction_id,
                        'remarks'     => $Task->resolved_action
                    );
                    $subject = 'Task :'.$taskNumber. " has been cancelled.";
                    $emailTo = $AssignToUser->email;
                    $emailToName = $AssignToUser->full_name;

                    break;

                default:
                    break;
            }

            $data['taskNumber'] = $taskNumber;

        } catch (Exception $ex) {
            $is_error = true;
            $message = $ex->getMessage();
        } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                Mail::send('emails.tasknotification', $Emaildata, function($message) use ($emailTo,$subject,$emailToName) {
                    $message->from('support@jocom.my', 'JOCOM');
                    $message->to($emailTo, $emailToName)->subject($subject);
                });
                // SEND EMAIL NOTIFICATION //
                DB::commit();
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    }

    public function getTransactionInfo() {
        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            $taskId = Input::get("task_id");
            $orderItemsInfo = array();
            $BatchItemsInfo = array();
            $taskId = $taskId;
            
            $taskInfo = DB::table('jocom_task AS JT')->select(array(
                DB::raw("CASE WHEN JT.status = 0 THEN 'Pending'
                    WHEN JT.status = 1 THEN 'Resolved'
                    WHEN JT.status = 2 THEN 'Cancelled'
                    WHEN JT.status = 3 THEN 'Rejected'
                    ELSE '-'
                    END AS taskStatus"),
                 'JT.id','JT.task_id_number','JT.title','JT.description','JT.transaction_id', 'JSB.full_name AS CreatedByName', 'JT.assign_by','JSA.full_name AS AssignByName','JT.assign_to','JST.full_name AS AssignToName','JT.is_urgent','JT.status','JT.activation','JT.created_at','JT.due_date','JTC.label','JT.resolved_action','JT.priority', 'JT.platform', 'JT.vendor'
                ))
                ->leftJoin('jocom_task_category AS JTC', 'JTC.id', '=', 'JT.category_id')
                ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JT.assign_by')
                ->leftJoin('jocom_sys_admin AS JSB', 'JSB.id', '=', 'JT.created_by')
                ->leftJoin('jocom_sys_admin AS JST', 'JST.id', '=', 'JT.assign_to')
                ->where( 'JT.id', '=', $taskId)    
                ->orderBy('JT.id','DESC')->first();
            
            $transaction_id = $taskInfo->transaction_id;
            
            $orderInfo = DB::table('jocom_transaction AS JT')
                    ->leftJoin('logistic_transaction AS LT', 'LT.transaction_id', '=', 'JT.id')
                    ->select('JT.*','LT.id AS logisticID', 'LT.status AS LogisticStatus', 'LT.remark')
                    ->where('JT.id', '=', $transaction_id)
                    ->first();
            
            $customerInfo = DB::table('jocom_user')->select('*')->where('username', '=', $orderInfo->buyer_username)->first();
            $orderDetails = DB::table('jocom_transaction_details')->select('*')->where('transaction_id', '=', $transaction_id)->get();
            $couponInfo = DB::table('jocom_transaction_coupon')->select('*')->where('transaction_id', '=', $transaction_id)->first();
            
            $logisticBatchInfo = DB::table('logistic_batch AS LB')
                    ->leftJoin('logistic_driver AS LD', 'LD.id', '=', 'LB.driver_id')
                    ->leftJoin('jocom_courier_orders AS JCO', 'JCO.batch_id', '=', 'LB.id')
                    ->leftJoin('jocom_courier AS JC', 'JC.id', '=', 'JCO.courier_id')
                    ->select('LB.*','JCO.courier_id','JCO.tracking_no','JC.courier_name','LD.name AS DriverName')
                    ->where('LB.logistic_id', '=', $orderInfo->logisticID)
                    ->orderBy('LB.id','asc')
                    ->get();

            $imageBatch = [];
            $batchlist = DB::table('logistic_batch')->select(DB::raw('GROUP_CONCAT(id) AS idlist'))->where('logistic_id', '=', $orderInfo->logisticID)->first();
            if($batchlist->idlist){
                $LogisticBatchImage = LogisticBatchImage::whereIn("batch_id", explode(',', $batchlist->idlist))->where("status", 1)->get();
                if(count($LogisticBatchImage) > 0){
                    foreach ($LogisticBatchImage as $kBI => $vBI) {
                        $imageBatch[] = array(
                            "filename" => Image::link(Config::get('constants.LOGISTIC_DELIVERY_IMG_PATH') . "/" . $vBI->filename),
                            "batch_id" => $vBI->batch_id,
                            "created_at" => $vBI->created_at
                        );
                    }
                }
            }

            $logisticAttachInfo =  DB::table('logistic_batch_attachment')->where('batch_id', $orderInfo->logisticID)->get();
            
            $courierOrder = DB::table('jocom_courier_orders')->select('*')->where('batch_id', '=', $logisticBatchInfo->id)->first();
            
            $logisticInfo = array(
                "id" => $orderInfo->logisticID,
                "status" => LogisticTransaction::get_status($orderInfo->LogisticStatus),
                "DeliveryAddress" => $orderInfo->delivery_addr_1." ".$orderInfo->delivery_addr_2." ".$orderInfo->delivery_city." ".$orderInfo->delivery_postcode." ".$orderInfo->delivery_state." ".$orderInfo->delivery_country,
                "Recipient" => $orderInfo->delivery_name,
                "RecipientPhone" => $orderInfo->delivery_contact_no,
                "specialMessage" => $orderInfo->special_msg,
                "remark" => $orderInfo->remark,
                "attach" => $logisticAttachInfo,
            );
            
            $counterBatch = 1;
            foreach ($logisticBatchInfo as $keyB => $valueB) {
                
                $subBatchLine = array(
                    "numbering" => $counterBatch,
                    "created_at" => $valueB->assign_date,
                    "logisticCourier" => $valueB->courier_name != '' ? $valueB->courier_name : 'Jocom Delivery',
                    "trackingNumber" => $valueB->tracking_no != '' ? $valueB->tracking_no :$transaction_id,
                    "driver" => $valueB->courier_name != '' ? '' : $valueB->DriverName,
                    "status" => LogisticBatch::get_status($valueB->status),
                );
                
                $counterBatch++;
                
                array_push($BatchItemsInfo, $subBatchLine);
            }
            
            $counter = 1;
            foreach ($orderDetails as $key => $value) {
                
                $subOrderItemLine = array(
                    "numbering" => $counter,
                    "ProductName" => $value->product_name,
                    "ProductLabel" => $value->price_label,
                    "ProductSKU" => $value->sku,
                    "qty" => $value->unit,
                    "price" => $value->price,
                    "gst" => $value->gst_amount,
                    "deliveryTime" => $value->delivery_time,
                    "totalAmount" => $value->total + $value->gst_amount,
                );
                
                $counter++;
                
                array_push($orderItemsInfo, $subOrderItemLine);
                
            }

            if(!Session::get('full_name'))
                $full_name = User::where('active_status', '=', 1)->select('full_name')->where('id', Session::get('user_id'))->first()->toArray()['full_name'];
            else
                $full_name = Session::get('full_name');
            $orderInformation = array(
                "login_user" => $full_name,
                "login_role" => Session::get('role_id'),
                "status" => $orderInfo->status,
                "transaction_id" => $orderInfo->id,
                "transaction_date" => $orderInfo->transaction_date,
                "buyer" => $customerInfo->full_name,
                "buyer_email" => $customerInfo->email,
                "phone" => $customerInfo->mobile_no != '' ? $customerInfo->mobile_no:$customerInfo->home_num,
                "invoice_no" => $orderInfo->invoice_no,
                "do_no" => $orderInfo->do_no,
                "couponcode" => $couponInfo->coupon_code != '' ? $couponInfo->coupon_code : '',
                "orderItemsInfo" => $orderItemsInfo,
                "logisticInfo" => $logisticInfo,
                "logisticBatchInfo" => $BatchItemsInfo ,
                "taskInfo" => $taskInfo,
                "imageBatch" => $imageBatch
            );
            
            $data = $orderInformation;
        
        } catch (Exception $ex) {
            $is_error = true;
            echo $ex->getMessage();
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
    
    public function saveMessage() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            $taskID = Input::get("task_id");
            $comment = Input::get("message");
            
            $User = User::find(Session::get("user_id"));
            
            $TaskMessage = new TaskMessage();
            $TaskMessage->task_id = $taskID;
            $TaskMessage->message = $comment;
            $TaskMessage->sender_name = $User->full_name;
            if (Input::hasFile('image')){
                
                $validator = Validator::make(['image' => Input::file('image')], ['image' => 'mimes:gif,jpeg,jpg,png']);
                if ($validator->passes()){
                    $filePath = Config::get('constants.TASK_MSG_IMG');
                    $extension = Input::file('image')->getClientOriginalExtension();
                    $filename = time() . '.' . $extension;

                    // delete existing file
                    if (file_exists($filePath . '/' . $filename))
                        unlink($filePath . '/' . $filename);

                    $upload_file_succ = Input::file('image')->move(Config::get('constants.TASK_MSG_IMG'), $filename);

                    if(isset($upload_file_succ)){
                        $TaskMessage->image = Config::get('constants.TASK_MSG_IMG') . '/' . $filename;
                    }                    
                } else {
                    return Redirect::back()
                                ->withInput()
                                ->with('message', 'File type error!');
                }
            }
            $TaskMessage->sender = Session::get("user_id");
            $TaskMessage->save();
            
            $Task = DB::table('jocom_task AS JT')
                    ->select('JT.task_id_number','JT.title','JT.assign_to','JT.assign_by')
                    ->where('JT.id', '=', $taskID)
                    ->first();
            
            $data['msg'] = array(
                "id" => $TaskMessage->id,
                "date" => date("d-m-Y h:iA"),
                "name" => $TaskMessage->sender_name ,
                "image" => $User->user_photo == '' || $User->user_photo == null ? '/images/asset/icon/people.png' : '/images/userprofile/'.$User->user_photo,
                "comment" => $TaskMessage->message,
                "attach_path" => Config::get('constants.TASK_MSG_IMG') . '/' . $filename,
            );
                
            if(Session::get("user_id") == $Task->assign_by){
                NotificationController::saveNotification(NotificationController::NOTI_COMMENT_TASK,  Session::get("full_name"),Session::get("user_id"), $Task->task_id_number, $taskID, $Task->assign_to, $comment);
            }
            if(Session::get("user_id") == $Task->assign_to){
                NotificationController::saveNotification(NotificationController::NOTI_COMMENT_TASK,  Session::get("full_name"),Session::get("user_id"), $Task->task_id_number, $taskID, $Task->assign_by, $comment);
            }
            if((Session::get("user_id") != $Task->assign_to) && (Session::get("user_id") != $Task->assign_by)){
                NotificationController::saveNotification(NotificationController::NOTI_COMMENT_TASK,  Session::get("full_name"),Session::get("user_id"), $Task->task_id_number, $taskID, $Task->assign_to, $comment);
                NotificationController::saveNotification(NotificationController::NOTI_COMMENT_TASK,  Session::get("full_name"),Session::get("user_id"), $Task->task_id_number, $taskID, $Task->assign_by, $comment);
            }
        
        } catch (Exception $ex) {
            $is_error = true;
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
    
    public function getMessage() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";


        try {
            
            DB::beginTransaction();
            
            $taskID = Input::get("task_id");
            $commentsList = array();
            
            $comments = DB::table('jocom_task_message AS JTM')->select('JTM.*','JSA.user_photo')
                    ->leftJoin('jocom_sys_admin AS JSA', 'JSA.id', '=', 'JTM.sender')
                    ->where('JTM.task_id', '=', $taskID)
                    ->where('JTM.activation', '=', 1)
                    ->orderBy('JTM.id', 'DESC')
                    ->get();
            
            foreach ($comments as $key => $value) {
                
                $subLineComment = array(
                    "id" => $value->id,
                    "date" => date("d-m-Y h:iA", strtotime($value->created_at)),
                    "name" => $value->sender_name,
                    "image" => $value->user_photo == '' || $value->user_photo == null ? '/images/asset/icon/people.png' : '/images/userprofile/'.$value->user_photo,
                    "comment" => $value->message,
                    "attach_path" => ($value->image ? URL::to('/') . '/' . $value->image : ''),
                );
                
                array_push($commentsList, $subLineComment);
            }
            
            $data = array("message"=>$commentsList);
            
        
        } catch (Exception $ex) {
                    
            $is_error = true;
            
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

    public function postAssignTo(){
        if(Input::get('assign_val')){
            $assign_val = explode('/', Input::get('assign_val'));
            $task_id = $assign_val[1];
            $assign_to = $assign_val[0];
            
            $Task = Task::find($task_id);
            $Task->assign_to = $assign_to;
            $Task->assign_by = Session::get("user_id");
            $Task->save();

            return [
                'status' => 'success',
                'message' => 'Assign Successful',
            ];
        }else{
            return false;
        }
    }
    
    public function updateLogiTrans(){
        try{
            $logistic = LogisticTransaction::find(Input::get('logTransID'));
            if(Input::get('remarks')){
                $tempremark = date('Y-m-d H:i:s') . " [CMS]" . Session::get('username') . ": " . trim(Input::get('remarks'));
                $logistic->remark = ($logistic->remark == '' ? $tempremark : $logistic->remark . "\n" . $tempremark);
            }

            // Logistic Transaction Attachment
            $files = Input::file('uploadAttch');
            if ($files) {
                foreach($files as $indx => $value) {
                    $image = $files[$indx];
                    if (!empty($image)) {
                        $attach =  date("Ymdhis") . "-" . uniqid();
                        $images = $attach . '.' . $image->getClientOriginalExtension();
                        $user = Session::get('username');
                        $date = date('Y-m-d H:i:s');
                        $mim = $image->getMimeType();
                        $path = public_path('logistic/images');
                        $image->move($path, $images);

                        DB::table('logistic_batch_attachment')->insertGetId([
                            'batch_id'      =>  $logistic->id,
                            'attachment'    =>  $images,
                            'path_to_file'  =>  $path,
                            'mime'          =>  $mim,
                            'created_by'    =>  $user,
                            'created_at'    =>  $date,
                        ]);
                    }
                }
            }

            if ($files || Input::get('remarks')){
                $logistic->modify_by    = Session::get('username');
                $logistic->modify_date  = date("Y-m-d h:i:sa");
                $logistic->save();
            }

            $status = 'complete';
            $message = '';
        } catch (Exception $ex) {
            $is_error = true;
            $status = 'fail';
            $message = $ex->getMessage();
        } finally {
            if ($is_error)
                DB::rollback();
            else
                DB::commit();
        }
        

        return [
            'status' => $status,
            'message' => $message,
        ];
    }
}
?>