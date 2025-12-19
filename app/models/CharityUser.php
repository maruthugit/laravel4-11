<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class CharityUser extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_charity_users';

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = array('password', 'remember_token');
	
	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	// protected $hidden = array('password', 'remember_token');
	
	

	/**
	 * Validation rules for creating a new user.
	 * @var array
	 */
	public static $rules = array(
		//'username'=>'required|email|unique:users'
	    'username'=>'required|min:3|unique:jocom_charity_users,username',
	    'full_name'=>'required|min:5',	
	    'email'=>'required|email',
	    'password'=>'required|alpha_num|between:6,12|confirmed',
	    'password_confirmation'=>'required|alpha_num|between:6,12',
	    'charity_id'=>'required|min:1'
	);

	public function scopeGetUpdateRules($query, array $inputs) {
		$validate_rule = array();

		foreach($inputs as $key => $value) {
            
            if(isset($value)) {
            	switch($key) {
            		case 'username'     :   
                                $validate_rule[$key] = "required|min:3";
                                break;

                    case 'full_name'    :   
                                $validate_rule[$key] = "required|min:5";
                                break;

                    case 'email'        :   
                                $validate_rule[$key] = "required|email";
                                break;

                    case 'password'     :   
                                $validate_rule[$key] = "alpha_num|between:6,12|confirmed";
                                break;

                    case 'password_confirmation' :   
                                $validate_rule[$key] = "alpha_num|between:6,12";
                                break;

                    case 'charity_id' :   
                                $validate_rule[$key] = "required|min:1";
                                break;

                    default:
                                $validate_rule[$key] = "";
                                break;
            	}
            }
        }
        return $validate_rule;
	}

	public function scopeGetUpdateInputs($query, array $inputs) {
		$arr_input = array();

		foreach($inputs as $key => $value) {
			if($value != "") {
				$arr_input[$key] = $value;
			}
		}
		return $arr_input;
	}

	public function scopeGetUpdateDbDetails($query, array $inputs) {
		foreach($inputs as $key => $value) {
            if($value != "" && $key !== "_method" && $key !== "_token" && $key !== "password_confirmation") {
                $arr_udata[$key] = $value;

                if($key == "password") $arr_udata[$key] = Hash::make($value);
            }               
	    }

	    $arr_udata['modify_by']		= Session::get('username');
	    $arr_udata['modify_date']	= date("Y-m-d H:i:s");
	    
	    return $arr_udata;
	}

	public function scopeUpdateUser($query, $id, array $data) {
		$user = DB::table('jocom_charity_users')
					->where('id', $id)
					->update($data);
		return $user;
	}

}
