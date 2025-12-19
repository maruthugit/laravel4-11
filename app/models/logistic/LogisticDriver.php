<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class LogisticDriver extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    
    protected $table = 'logistic_driver';

    /**
     * Validation rules for creating a new driver.
     * @var array
     */
    public static $rules = array(
        'username'              =>'required|min:3|unique:logistic_driver,username',
        'contact_no'            =>'required|numeric',
        'device_id'             =>'required',
        'password'              =>'required|alpha_num|between:6,12|confirmed',
        'password_confirmation' =>'required|alpha_num|between:6,12',
    );

    public function scopeGetUpdateRules($query, array $inputs) {
        $validate_rule = array();

        foreach($inputs as $key => $value) {
            
            if(!empty($value)) {

                // $arr_input[$key] = $value;
             
                switch($key) {
                    case 'username'     :   
                                $validate_rule[$key] = "required|min:3";
                                break;

                    case 'contact_no'    :   
                                $validate_rule[$key] = "required|numeric";
                                break;
                    case 'device_id'    :   
                                $validate_rule[$key] = "required";
                                break;
                    
                    case 'password'     :   
                                $validate_rule[$key] = "required|alpha_num|between:6,12|confirmed";
                                break;

                    case 'password_confirmation' :   
                                $validate_rule[$key] = "required|alpha_num|between:6,12";
                                break;                   

                }
               
            }
            
        }
        return $validate_rule;
    }


    public function scopeGetUpdateInputs($query, array $inputs) {
        foreach($inputs as $key => $value) {
            if($value != NULL) {
                $arr_input[$key] = $value;
            }
        }
        return $arr_input;
    }
    

    public function scopeGetUpdateDbDetails($query, array $inputs) {
        $arr_udata = array();

        foreach($inputs as $key => $value) {
            switch ($key) {
                case 'username':
                    $arr_udata['username'] = $value;
                    break;

                case 'password':
                    $arr_udata['password'] = Hash::make($value);
                    break;

                case 'name':
                    $arr_udata['name'] = $value;
                    break;

                case 'contact_no':
                    $arr_udata['contact_no'] = $value;
                    break;
           
                case 'device_id':
                    $arr_udata['device_id'] = $value;
                    break;

                case 'type':
                    $arr_udata['type'] = $value;
                    break;

                case 'status':
                    $arr_udata['status'] = $value;
                    
                case 'region_access':
                    $arr_udata['region_id'] = $value;
                    break;
                    
                case 'profileimg':
                    $arr_udata['filename'] = $value;
                    break;
                
                case 'logistic_dashboard':
                    $arr_udata['is_logistic_dashboard'] = $value;
                    break;
                
            }
        }

        return $arr_udata;
    }

    public function scopeUpdateDriver($query, $id, array $data) {
        $driver = DB::table('logistic_driver')
                    ->where('id', $id)
                    ->update($data);
        return $driver;
    }

    public static function get_type($type = "")
    {
        $value = "";

        switch ($type)
        {
            case '0':
                $value = 'Driver';
                break;
            case '1':
                $value = 'Supervisor';
                break;
            default:
                $value = 'Driver';
                break;
        }

        return $value;
    }

    public static function get_type_int($type = "")
    {
        $value = "";

        switch ($type)
        {
            case 'Driver':
                $value = '0';
                break;
            case 'Supervisor':
                $value = '1';
                break;
            default:
                $value = '0';
                break;
        }

        return $value;
    }
    
    public static function getdevice_id($id){

        $deviceid = "";
        $result = DB::table('logistic_driver')
                  ->select('username')
                  ->where('id','=',$id)
                  ->first();

        if(count($result)>0){
            $user = $result->username; 

            $resultdevice = DB::table('logistic_driver_device')
                                ->select('device_id')
                                ->where('username','=',$user)
                                ->first();

            if(count($resultdevice)>0){
                $deviceid = $resultdevice->device_id;

            }

        }

        return $deviceid;
    }

    public function scopeUpdateDeviceid($query, $id, array $data) {
       

        $result = DB::table('logistic_driver')
                  ->select('username')
                  ->where('id','=',$id)
                  ->first();

        if(count($result)>0){

            $driver = DB::table('logistic_driver_device')
                    ->where('username', $result->username)
                    ->update($data);
        }          

        return $driver;
    }

    public function scopeInsertDriver($query, array $data){

                $driver = DB::table('logistic_driver_device')
                            ->insert($data);
            return $driver;                
    }
    
}