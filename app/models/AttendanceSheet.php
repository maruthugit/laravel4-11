<?php

class AttendanceSheet extends Eloquent
{
    protected $table = 'jocom_attendance_sheet';
    
    public static function getDate($formatDate,$driver_username){
        
        return DB::table('jocom_attendance_sheet')
                    ->where('date', $formatDate)
                    ->where('driver_username', $driver_username)
                    ->count();
        
    }
    
    public static function getRecord($formatDate,$driver_username){
        
        return AttendanceSheet::where('date', $formatDate)
                    ->where('driver_username', $driver_username)
                    ->first();
        
    }

    public static function getAttendance($username,$type){
        $date = date("Y-m-d H:i:s");
        $datePosted = new DateTime($date);
        $formatDate =  $datePosted->format('Y-m-d');
        $flag = 0;

        $result = DB::table('jocom_attendance_sheet')
                 ->where('date',$formatDate)
                 ->where('driver_username',$username)
                 ->first();

            if(isset($result->time_in) && $result->time_in<>'' && (int)$type ==2)
            {

                $flag = 1;
            }
            elseif(isset($result->time_out) && $result->time_out<>'' && (int)$type ==3)
            {
                $flag = 1;
            }
            elseif(isset($result->ot_in) && $result->ot_in<>'' && (int)$type ==4)
            {
                $flag = 1;
            }

            // if($flag == 1)
            // {
            //     DB::table('jocom_attendance')
            //         ->where('username',$username)
            //         ->where('created_date','like','%'.date('Y-m-d').'%')
            //         ->update(array('type' => $type));
            // }

        return $flag;
    }

    public static function getPrvdaystatus($username,$type){
       
        $rflag=1;
        $prvdaydate = date("Y-m-d H:i:s",time()-86400);

        $datePosted = new DateTime($prvdaydate);
        $formatDate =  $datePosted->format('Y-m-d');

        $return = DB::table('jocom_attendance')
                        ->where('created_date','like','%'.$formatDate.'%')
                        ->where('username', $username)
                        ->first();
        if((int)$return->type == 1 && (int)$return->type == 3)
        {
            $rflag=0;
        }

        return $rflag;

    }

   

   
}
