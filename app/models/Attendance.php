<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class Attendance extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    protected $table = 'jocom_attendance';


    public static function getAttendancestatus($username){

                $date = date('Y-m-d');
                $sqlq ="select * from jocom_attendance where id in (select max(id) from jocom_attendance where username='".$username."') and created_date like '%".$date."%'";
                // $sql="select  max(id),username, type from jocom_attendance where created_date like '%".$date."%' and username='".$username."'";
               return DB::select($sqlq);   


         // return  DB::table('jocom_attendance')->select('id')
         //         ->wherein('id', function($query) 
         //            { 
         //                $query->selectRaw('max(id)')->from('jocom_attendance')
         //                     ->where('created_date','like','%'.date('Y-m-d').'%');

         //            })
         //         ->where('username',$username)
         //         ->first();

    }

    public static function getAttendancedummy($username,$type)
    {

                $date = date('Y-m-d');
                $sqlq ="select * from jocom_attendance where id in (select max(id) from jocom_attendance where username='".$username."' and type=".$type.") and created_date like '%".$date."%'";
                // $sql="select  max(id),username, type from jocom_attendance where created_date like '%".$date."%' and username='".$username."'";
               return DB::select($sqlq);   
    }

    public static function getAttendancedummydata($username,$type)
    {


                $date = date('Y-m-d');
                $prvdaydate = date("Y-m-d H:i:s",time()-86400);

                $datePosted = new DateTime($prvdaydate);
                $formatDate =  $datePosted->format('Y-m-d');


                $sqlq ="select * from jocom_attendance where id in (select max(id) from jocom_attendance where username='".$username."' and type=".$type.") and created_date like '%".$formatDate."%'";
                // $sql="select  max(id),username, type from jocom_attendance where created_date like '%".$date."%' and username='".$username."'";
                $result = DB::select($sqlq);
                
                if(count($result)==0){
                    $sqlq1 ="select * from jocom_attendance where id in (select max(id) from jocom_attendance where username='".$username."' ) and created_date like '%".$formatDate."%'";

                    return DB::select($sqlq1);
                }

                

               

    }

    public static function deviceValidator($username,$deviceid){

        $valid  = 0;

        $chkExists=DB::table('logistic_driver_device')
                ->where('username','=',$username)
                ->first();

        if(count($chkExists)>0)
        {
            if((string)$chkExists->device_id == (string)$deviceid)
            {
                $valid = 1; 
            }
            else 
            {
                $valid = 2;
            }

        }
        else 
        {
                DB::table('logistic_driver_device')->insert(array(
                    'username'  => $username,
                    'device_id' => $deviceid
                    )
                );
                $valid = 3;

        }

        return $valid;

    }

    public static function getPrvdaystatus($username){
       
        $rflag=1;
        $prvdaydate = date("Y-m-d H:i:s",time()-86400);

        $datePosted = new DateTime($prvdaydate);
        $formatDate =  $datePosted->format('Y-m-d');
        // echo $formatDate;
        // $returndata = DB::table('jocom_attendance')
        //                 ->where('created_date','like','%'.$formatDate.'%')
        //                 ->where('username', $username)
        //                 ->first();
        
        $sqlq ="select * from jocom_attendance where id in (select max(id) from jocom_attendance where username='".$username."')";
        $returndata=DB::select($sqlq);    

        // $returndata = DB::table('jocom_attendance')
        //              ->whereIn('id', function($query) 
        //                 { 
        //                     $query->selectRaw('max(id)')->from('jocom_attendance');
        //                 })
        //              ->where('username',$username)
        //              ->get();

        // echo $returndata->type.'k'.count($returndata)."s";

        if((int)$returndata[0]->type == 1 || (int)$returndata[0]->type == 3)
        {
            $rflag=0;
        }

        return $rflag;

    }

    public static function getClockstatus($username){

        $currentdate = date("Y-m-d");
        $prvdaydate  = date("Y-m-d H:i:s",time()-86400);

        $returnstatus = DB::table('jocom_attendance')
                        ->where('username', $username)
                        ->where('created_date','like','%'.$currentdate.'%')
                        ->first();

        if(count($returnstatus)>0){
            if($returnstatus->type ==1)
            {

                
            }
        }


    }





}
?>