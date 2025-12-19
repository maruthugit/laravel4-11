<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class Customer extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

    public $timestamps = false;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_user';

	/**
	 * Validation rules for creating a new user.
	 * @var array
	 */
	public static $rules = array(
	    'username'=>'required|min:3|unique:jocom_user,username',
	   // 'username'=>'alpha_num_dash|min:3|unique:jocom_user,username',
//	    'full_name'=>'required|alpha_spaces|min:5',	
        'firstname'=>'required|min:1', 
        'lastname'=>'required|min:1', 
        'ic_passport'=>'unique:jocom_user,ic_no',
        // 'email'=>'required|email|unique:jocom_user,email',
//	    'home_num'=>'numeric',
          //  'mobile_no'=>'required|numeric',
	    'postcode'=>'numeric',
	    'dob'	=> 'after:1900-01-01|date_format:Y-m-d',
            'password'=>'required|alpha_num|min:6',
//	    'password_confirmation'=>'required|alpha_num|between:6,12',
	);
	    
	

	public function scopeGetUpdateRules($query, array $inputs, $id) {
		$validate_rule = array();

		foreach($inputs as $key => $value) {
          if($value != NULL) {
                switch($key) {
                    case 'password'     :   
                                $validate_rule[$key] = "between:6,12|confirmed";
                                break;

                     case 'password_confirmation' :   
                                 $validate_rule[$key] = "alpha_num|between:6,12";
                                 break;
				}
            }
            
           	switch($key) {
             	case 'username'     :   
                        	$validate_rule[$key] = "min:3";
                    		break;

               	case 'firstname'    :   
                          	$validate_rule[$key] = "min:2";
                         	break;

              	case 'lastname'    :   
                           	$validate_rule[$key] = "min:2";
                          	break;
                                       
            	case 'ic_passport'        :
                         	$validate_rule[$key] = "unique:jocom_user,ic_no";
                       		break;

               	case 'email'        :   
                          	$validate_rule[$key] = "required|email|unique:jocom_user,email,".$id;
                         	break;

               	case 'mobile_no'    :   
                        	$validate_rule[$key] = "numeric";
                           	break;

            	case 'postcode'        :   
                      		$validate_rule[$key] = "numeric";
                        	break;

               	default:
                       		$validate_rule[$key] = "";
                         	break;
       		}
        }
        return $validate_rule;
	}

	public function scopeGetUpdateInputs($query, array $inputs) {
		$arr_input	= array();
		foreach($inputs as $key => $value)
    {
      if ($key == "password" || $key == "password_confirmation")
      {
        if($value != NULL)
        {
          $arr_input[$key] = $value;
        }
      }
      else if ($key == "bcard")
      {
        // skip bcard as in diff table
      }
      else
      {
        $arr_input[$key] = $value;
      }
    }
		return $arr_input;
	}
	
	public function scopeGetUpdateDbDetails($query, array $inputs) {
        $firstname                = "";
        $lastname                 = "";
		$arr_udata                = array();
        $arr_udata['pdpa']        = '0';
        $arr_udata['usr_agree']   = '0';

		foreach($inputs as $key => $value) {
            if(isset($value) && $key !== "_method" && $key !== "_token" && $key !== "password_confirmation") {
                $arr_udata[$key] = $value;
                
                if($key == "firstname") $firstname  = $value;
                if($key == "lastname")  $lastname   = $value;
                if($key == "password") $arr_udata[$key] = Hash::make($value);

            }               
	    }

        if(isset($firstname) || isset($lastname)) {
            $arr_udata['full_name']     = $firstname ." ". $lastname;
        }

	    $arr_udata['modify_by']		= Session::get('username');
	    $arr_udata['modify_date']	= date("Y-m-d H:i:s");
	    
	    return $arr_udata;
	}

	public function scopeUpdateCustomer($query, $id, array $data) {
		$customer = DB::table('jocom_user')
					->where('id', $id)
					->update($data);
		return $customer;
	}	

    public function scopeGetCountryList() {
        // $role_list = DB::table('roles')->lists('role_name', 'id');
        return DB::table('jocom_countries')
                    ->select('id', 'name')
                    ->orderby('name')
                    ->get();
    }

    public function scopeGetStateList($query, $id) {
        // echo "<br>getStateList ID: ".$id;
        return DB::table('jocom_country_states')
                    ->select('id', 'name', 'country_id')
                    ->where('country_id', '=', $id)
                    ->orderby('name')
                    ->get();
    }

    public function scopeGetCityList($query, $id) {
        return DB::table('jocom_cities')
                    ->select('id', 'name', 'state_id')
                    ->where('state_id', '=', $id)
                    ->orderby('name')
                    ->get();
    }

    public static function totalCustomer() {
        $cust = 0;
        try{
        // $cust       = Customer::all();
        // $total      = count($cust);
            $cust       = Customer::count();
        
        } catch (Exception $ex) {
            
            $exp = $ex->getMessage();
            $expline = $ex->getLine();
           
           
        } 
        
        return $cust;
    }

    public function scopeCheck_login($query, $username, $pass) 
    {
        $data       = array();
        $valid      = 0;
        $crypt_pass = md5($pass);
        
        $cust       = Customer::where('username', '=', $username)->where('password', '=', $crypt_pass)->first();

        if($cust != null) {
            $valid = 1;
            $udata                  = array();
            $udata['password']      = Hash::make($pass);
            $udata['modify_date']   = date("Y-m-d H:i:s");
            $udata['modify_by']     = "admin";

            $customer = Customer::where('username', '=', $username)->update($udata);

        } else {

            $cust   = Customer::where('username', '=', $username)->first();

            if(Hash::check($pass, $cust->password)) $valid = 1;
        
        }
    
        if($valid == 1) 
        {           
            return 'yes';
        } 
        else 
        {
            return 'no';
        }
    }
    
    public function scopeGetCustDetails($query, $username, $pass) {
        $crypt_pass = md5($pass);

        return DB::table('jocom_user')
                ->where('username', '=', $username)
                ->where('password', '=', $crypt_pass)
                ->get();
    }
}
?>