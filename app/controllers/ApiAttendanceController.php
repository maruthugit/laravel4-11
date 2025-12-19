<?php

class ApiAttendanceController extends BaseController
{

    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyAttendancelogin()
    {

        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }


        $data = array_merge($data, self::checkAttendance(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }

    public static function checkAttendance($input=array()){


        $data = array();
        $username=Input::get('username');
        $pass=Input::get('password');
        $devideid=Input::get('device_id');


        if(Input::has('username') AND Input::has('password'))
        {
            $username = trim(Input::get('username'));
            $password = trim(Input::get('password'));

            $driver = LogisticDriver::where('username', '=', $username)->where('status', '=', '1')->first();

            if(count($driver)>0)
            {
                if(Hash::check($password, $driver->password))
                {
                    // $type = LogisticDriver::get_type($driver->type);

                    // $data['name'] = $driver->name;
                    // $data['contact_no'] = $driver->contact_no;
                    // $data['type'] = $type;
                     // echo $driver->id;   

                    $dev_id   = DB::table('logistic_driver_device')
                        ->where('username', '=', $username)
                        ->where('device_id', '=', $devideid)
                        ->first();
                        
                        if(count($dev_id)>0)
                        {
                             $data  = array('username'   => Input::get('username'),
                                   'id'             => $driver->id, 
                                   'status'         => '1',
                                   'status_msg'     => 'Successfully Logged in',
                                ); 

                            // $data['status_msg']='Successfully Logged in';

                             
                        }
                        else
                        {

                             $data  = array('username'   => Input::get('username'),
                                      'id'             => '0', 
                                      'status'         => '0',

                                      'status_msg'     => 'Device ID does not match',
                             );    
                        }


                }
                else
                    $data  = array('username'   => Input::get('username'),
                                   'id'             => '0', 
                                   'status'         => '0',
                                   'status_msg'     => 'Invalid username or password!',
                                ); 
            }
            else
                $data  = array('username'   => Input::get('username'),
                                   'id'             => '0', 
                                   'status'         => '0',
                                   'status_msg'     => 'Invalid username!',
                                ); 
        }
        
        return array('xml_data' => $data);

    }

    public function anySaveattendance()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }


        $data = array_merge($data, self::createAttendance(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }


    public static function createAttendance($input=array()){

        $data = array();
        $username=Input::get('username');
        $deviceid=Input::get('device_id');
        $flag = 0;


        $date = date("Y-m-d H:i:s");


        // echo $date."\n";
        // die(date("Y-m-d H:i:s",time()-86400));

        $devicevalid = Attendance::deviceValidator($username,$deviceid);

        if($devicevalid == 1 || $devicevalid == 3)
        {

            $returndata = Attendance::getAttendancestatus($username);

            $type=Input::get('type');

           // print_r($returndata[0]->type);
          
            if(count($returndata)>0)
            {   
                $returnstatus=Attendance::getAttendancedummy($username,$type);
                if((int)$type==(int)$returnstatus[0]->type){
                    


                    $s_msg='';

                    switch ($returnstatus[0]->type) {
                        case 1:
                            $s_msg = 'Sorry. You have already logged in';
                            break;
                        case 2:
                            $s_msg = 'Sorry. You have already logged Out';
                            break;
                        case 3: 
                            $s_msg = 'Sorry. You have already OT logged in';
                            break;
                        case 4: 
                            $s_msg = 'Sorry. You have already OT logged Out';
                            break;

                        default:
                            $s_msg = 'Unable to proceed. Please contact your Supervisor';
                            break;

                    }

                    // if($returnstatus[0]->type == 1){
                    //     $s_msg = 'Sorry. You have already logged in';
                    // }
                    // elseif ($returnstatus[0]->type == 1 || $returnstatus[0]->type ==3){

                    // elseif($returnstatus[0]->type == 2 || $returnstatus[0]->type ==4)
                    // {
                    //     $s_msg = 'Sorry. You have already logged Out';
                    // }

                    $data  = array('username'   => Input::get('username'),
                               'status_ins'     => '0',
                               'status_msg'     => $s_msg,
                        );  
                        
                }
                else
                {
                    switch ($type) {
                        case 2:
                            $status_msg = 'Successfully Logged Out';
                            break;
                        case 3: 
                            $status_msg = 'Successfully Logged OT In';
                            break;
                        case 4: 
                            $status_msg = 'Successfully Logged OT Out';
                            break;

                        default:
                            break;

                    }
                    if((int)$type==2)
                    {
                        $timesheet=AttendanceSheet::getAttendance($username,$type);
                        if((int)$timesheet ==1)
                        {
                            $flag = 1;
                        }
                        

                    }
                    elseif((int)$type==3)
                    {
                        $timesheet=AttendanceSheet::getAttendance($username,$type);
                        if((int)$timesheet ==1)
                        {
                            $flag = 1;
                        }
                    }
                    elseif((int)$type==4)
                    {
                        $timesheet=AttendanceSheet::getAttendance($username,$type);
                        if((int)$timesheet ==1)
                        {
                            $flag = 1;
                        }
                    }
                    if($flag == 1){
                        // echo "Insert 2";
                        $api1                = new Attendance;
                        $api1->userid        = Input::get('userid');
                        $api1->username      = Input::get('username');
                        $api1->type          = Input::get('type');
                        $api1->status        = Input::get('status');
                        $api1->latitude      = Input::get('latitude');
                        $api1->longitude     = Input::get('longitude');
                        $api1->device_id     = Input::get('device_id');
                        $api1->created_by    = Input::get('username');

                        if($api1->save()){

                            $myreturn = self::saveTimeTracking($username , $type, $date); 
                            $data  = array('username'   => Input::get('username'),
                                       'status_ins'     => '1',
                                       'status_msg'     => $status_msg,
                                ); 
                        }
                    }
                    else 
                     {
                        $data  = array('username'   => Input::get('username'),
                               'status_ins'     => '0',
                               'status_msg'     => 'Unable to proceed. Please contact your Supervisor',
                        ); 
                     }
                        

                } 

                

            }
            elseif((int)$type==1)
            {
                
                // $prvdayflag=0;
                $returnprvdate=Attendance::getPrvdaystatus($username);
                if($returnprvdate == 1){
                    // echo 'Insert';
                    $api                = new Attendance;
                    $api->userid        = Input::get('userid');
                    $api->username      = Input::get('username');
                    $api->type          = Input::get('type');
                    $api->status        = Input::get('status');
                    $api->latitude      = Input::get('latitude');
                    $api->longitude     = Input::get('longitude');
                    $api->device_id     = Input::get('device_id');
                    $api->created_by    = Input::get('username');

                    if($api->save()){
                        
                    $myreturn = self::saveTimeTracking(Input::get('username') , Input::get('type'), $date);

                        $data  = array('username'   => Input::get('username'),
                                       'type'       => Input::get('type'),
                                       'status_ins' => '1', 
                                       'response' => $myreturn,
                                       'status_msg'     => 'Successfully Logged In',

                            );


                    } 
                    else 
                    {
                        $data  = array('username'   => Input::get('username'),
                                       'status_ins'     => '0',
                                       'status_msg'     => 'Unable to insert Attendance',
                                );  
                    } 
                }
                else{
                    $data  = array('username'   => Input::get('username'),
                                   'status_ins' => '0',
                                   'status_msg' => 'Unable to proceed. Please contact your Supervisor',
                            );  
                }

                
            }
            elseif ((int)$type==4) {
                $returndatas = Attendance::getAttendancedummydata($username,$type);
                if(count($returndatas)>0){
                        $prvdaydate = date("Y-m-d H:i:s",time()-86400);
                        $currentdate = date("Y-m-d H:i:s");

                        $datePosted = new DateTime($prvdaydate);
                        $formatDate =  $datePosted->format('Y-m-d');

                        $formatDate1 =  date("Y-m-d 05:00:00");
                        if($currentdate<=$formatDate1)
                        {
                            
                            $api2                = new Attendance;
                            $api2->userid        = Input::get('userid');
                            $api2->username      = Input::get('username');
                            $api2->type          = Input::get('type');
                            $api2->status        = Input::get('status');
                            $api2->latitude      = Input::get('latitude');
                            $api2->longitude     = Input::get('longitude');
                            $api2->created_date  = $formatDate;
                            $api2->device_id     = Input::get('device_id');
                            $api2->created_by    = Input::get('username');

                            if($api2->save()){

                                $myreturn = self::saveTimeTracking_1($username , $type, $formatDate); 
                                $data  = array('username'   => Input::get('username'),
                                           'status_ins'     => '1',
                                           'status_msg'     => 'Successfully OT Logged Out',
                                    ); 
                            }
                        }
                        else
                        {
                            $data  = array('username'   => Input::get('username'),
                                   'status_ins' => '0',
                                   'status_msg' => 'Unable to proceed. Please contact your Supervisor',
                            );  
                        }

                }
                else{

                    $data  = array('username'   => Input::get('username'),
                                   'status_ins' => '0',
                                   'status_msg' => 'Unable to proceed. Please contact your Supervisor',
                            );  

                } 



            }
            else{
                    $data  = array('username'   => Input::get('username'),
                                   'status_ins' => '0',
                                   'status_msg' => 'Unable to proceed. Please contact your Supervisor',
                            );  

            } 

            // die('ret');

            


            
        }
        else if($devicevalid == 2)
        {
            $data  = array('username'   => Input::get('username'),
                                      'status_ins'     => '0',
                                      'status_msg'     => 'Device ID does not match',
                        );  
        }    

        return array('xml_data' => $data);


    }

    public static function saveTimeTracking($driver_username , $type, $date){
        
       
        try{

        $datePosted = new DateTime($date);
        $formatDate =  $datePosted->format('Y-m-d');
        $AttendanceExist = AttendanceSheet::getDate($formatDate,$driver_username);
        
        
        if($AttendanceExist > 0){

            $AttendanceRow = AttendanceSheet::getRecord($formatDate,$driver_username);
            $Attendance = AttendanceSheet::find($AttendanceRow->id);

        }else{
            
            $Attendance = new AttendanceSheet();
            $Attendance->date = $formatDate;
        }
        switch ($type) {
            case 1:
                $Attendance->time_in = $date;
                break;
            case 2:
                $Attendance->time_out = $date;
                break;
            case 3:
                $Attendance->ot_in = $date;
                break;
            case 4:
                $Attendance->ot_out = $date;
                break;

            default:
                break;
        }
        
        $Attendance->driver_username = $driver_username;
        $Attendance->save();
        
          return 1;
      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public static function saveTimeTracking_1($driver_username , $type, $date){
        
       
        try{
        $date_1 = date("Y-m-d H:i:s");    
        $datePosted = new DateTime($date);
        $formatDate =  $datePosted->format('Y-m-d');
        $AttendanceExist = AttendanceSheet::getDate($formatDate,$driver_username);
        
        
        
        $AttendanceRow = AttendanceSheet::getRecord($formatDate,$driver_username);
        $Attendance = AttendanceSheet::find($AttendanceRow->id);

        switch ($type) {
            case 1:
                $Attendance->time_in = $date_1;
                break;
            case 2:
                $Attendance->time_out = $date_1;
                break;
            case 3:
                $Attendance->ot_in = $date_1;
                break;
            case 4:
                $Attendance->ot_out = $date_1;
                break;

            default:
                break;
        }
        
        $Attendance->driver_username = $driver_username;
        $Attendance->save();
        
          return 1;
      }catch(Exception $ex){

          return $ex->getMessage();
      }
        
        
    }

    public function anyAttendancetype($input=array()){
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }


        $data = array_merge($data, self::attendanceClockstatus(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }

    public static function attendanceClockstatus($input=array()){
        $data = array();
        $username=Input::get('username');
        // $deviceid=Input::get('device_id');
        $type = 0;

        
        $sqlq ="select * from jocom_attendance where id in (select max(id) from jocom_attendance where username='".$username."')";

        // $returnstatus  = DB::table('jocom_attendance')
        //          ->whereIn('id', function($query) 
        //             { 
        //                 $query->selectRaw('max(id)')->from('jocom_attendance');
        //             })
        //          ->where('username',$username)//->toSql();
        //          ->first();

        $returnstatus=DB::select($sqlq);    
        
        
         if(count($returnstatus)>0){

            $type = $returnstatus[0]->type;
         }
                          
         // echo  $returnstatus['type'][0];

        if(count($returnstatus)>0){
            
            switch ($type) {
                        case 1:
                            $status_type = '1';
                            $status_msg  = 'Clock in';
                            break;
                        case 2:
                            $status_type = '2';
                            $status_msg = 'Clock Out';
                            break;
                        case 3: 
                            $status_type = '3';
                            $status_msg = 'OT Clock In';
                            break;
                        case 4: 
                            $status_type = '4';
                            $status_msg = 'OT Clock Out';
                            break;

                        default:
                            $status_type = '0';
                            $status_msg = 'Unable to proceed. Please contact your Supervisor';
                            break;

                    }

                    $data  = array('username'   => Input::get('username'),
                                      'current_status'     => $status_type,
                                      'status_msg' => $status_msg,

                        );  

            
        }
        else 
        {
            $data  = array('username'   => Input::get('username'),
                                      'current_status'     => 0,
                                      'status_msg' => 'You have not logged in',

                        );

        }
        

        // $devicevalid = Attendance::deviceValidator($username,$deviceid);

        // if($devicevalid == 1 || $devicevalid == 3)
        // {



        // }
        // else if($devicevalid == 2)
        // {
        //     $data   =  $data  = array('username'   => Input::get('username'),
        //                               'status'     => '0',
        //                               'status_msg'     => 'Device ID does not match',
        //                 );  
        // } 

        return array('xml_data' => $data);


    }

    public function getTimeout(){
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";

        try{
            $curtime = date("Y-m-d H:i:s");
            $date = date('Y-m-d');
            

            $result = DB::table('jocom_attendance')->select('username')
                        ->where('created_date','like','%'.date('Y-m-d').'%')
                        ->groupby('username')
                        ->get(); 
                        // print_r($result);
                foreach ($result as  $value) {

                    $sqlq ="select * from jocom_attendance where id in (select max(id) from jocom_attendance where username='".$value->username."') and created_date like '%".$date."%'";
                    $inner_update = DB::select($sqlq);   


                    if($inner_update[0]->type == 1)
                    {

                        
                            // *** API Logout 
                            DB::table('jocom_attendance')->insert(array(
                                'username'          =>  $value->username,
                                'type'              =>  2,
                                'latitude'          =>  '3.110305',
                                'longitude'         =>  '101.663798',
                                'created_date'      =>  $curtime,
                                'created_by'        =>  'API',
                                    )
                            ); 

                            //*** API OT In
                            DB::table('jocom_attendance')->insert(array(
                                'username'          =>  $value->username,
                                'type'              =>  3,
                                'latitude'          =>  '3.110305',
                                'longitude'         =>  '101.663798',
                                'created_date'      =>  $curtime,
                                'created_by'        =>  'API',
                                    )
                            );   

                            DB::table('jocom_attendance_sheet')
                            ->where('driver_username',$value->username)
                            ->where('created_at','like','%'.date('Y-m-d').'%')
                            ->update(array('time_out'=>$curtime,'ot_in'=>$curtime)); 


                    }

                    // echo $value->type.$value->username;

                }

           
            } catch (Exception $ex) {
            $isError = 1;
            $errorMessage = $ex->getMessage();
        }
        finally{
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }         
                   
    }


    public function getGenerate(){
       
        $isError = 0;
        $respStatus = 1;
        $errorMessage = "";
        $arr  = array();
      
        try{

        $prvdaydate = date("Y-m-d H:i:s",time()-86400);

        $datePosted = new DateTime($prvdaydate);
        $formatDate =  $datePosted->format('Y-m-d');

        $result = DB::table('jocom_attendance_sheet')//->select('driver_username','time_in','time_out','ot_in','ot_out','created_at')
                    ->where('created_at','like','%'.$formatDate.'%')
                    ->get(); 
          // print_r($result);
           if(count($result)>0){

             foreach ($result as  $value) {
                // echo $value->driver_username;
                $array = array('driver_username' => $value->driver_username,
                               'created_at'      => $value->created_at, 
                               'time_in'         => $value->time_in, 
                               'time_out'        => $value->time_out, 
                               'ot_in'           => $value->ot_in, 
                               'ot_out'          => $value->ot_out, 
                             ); 

                        array_push($arr, $array);   

                    } 

           }         
                  
          // die();
            self::createCSV($arr);        

        } catch (Exception $ex) {
            $isError = 1;
            $errorMessage = $ex->getMessage();
        }
        finally{
            
            return array(
                "respStatus"=>$isError,
                "errorMessage"=>$errorMessage
            );
        }

        


    }


    public static function createCSV($record){
   
    
        $fileName = 'driver_attendance_'.date("Ymdhis").".csv";

        $path = Config::get('constants.CSV_FILE_PATH');
        $file = fopen($path.'/'.$fileName, 'w');
    
        fputcsv($file, ['User Name', 'Created Time', 'Time In', 'Time Out','OT In', 'OT Out']);

        foreach ($record as $row)
        {   

            // echo $row['driver_username'];
                fputcsv($file, [
                    $row['driver_username'],
                    $row['created_at'],
                    $row['time_in'],
                    $row['time_out'],
                    $row['ot_in'],
                    $row['ot_out']
                ]);
            //}        
    
        }
        
        fclose($file);
        // print_r(expression)
        $test = Config::get('constants.ENVIRONMENT');

 
        if ($test == 'test')
            $mail = ['maruthu@jocom.my'];
        else
            $mail = ['sri@jocom.my', 'humairah@jocom.my'];
             //$mail = ['maruthu@jocom.my'];
        $subject = "Driver Attendance : " . $fileName;
        $attach = $path . "/" . $fileName;
       // print_r($attach);
        $body = array('title' => 'Driver Attendance');

        Mail::send('emails.attendance', $body, function($message) use ($subject, $mail, $attach)
            {
                $message->from('maruthu@jocom.my', 'Maruthu');
                $message->to($mail, '')->subject($subject);
                $message->attach($attach);
            }
        );
      
      unlink($attach);  
        
    }




}