<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class User extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_sys_admin';

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
	
	public function role() 
	{
		return $this->belongsTo('Role');
	}


	public function modules() {
		return $this->belongsToMany('Module', 'permissions', 'user_id', 'mod_id');
	}

	public function hasRole($key) 
	{
		foreach($this->roles as $role) {
			if($role->role_name == $key) {
				return true;
			}
		}
		return false;
	}



	/**
	 * Validation rules for creating a new user.
	 * @var array
	 */
	public static $rules = array(
		//'username'=>'required|email|unique:users'
	    'username'=>'required|min:3|unique:jocom_sys_admin,username',
	    'full_name'=>'required|min:5',	
	    'email'=>'required|email|unique:jocom_sys_admin',
	    'password'=>'required|alpha_num|between:6,12|confirmed',
	    'password_confirmation'=>'required|alpha_num|between:6,12'
	);

	public function scopeGetAllUserRoles() {
		$users = DB::table('jocom_sys_admin')
					->leftjoin('roles', 'jocom_sys_admin.role_id', '=', 'roles.id')
					->select('jocom_sys_admin.*', 'roles.role_name')
					->get();
		return $users;
	}

	public function scopeGetUserRole($query, $id) {
		$user = DB::table('jocom_sys_admin')
					->leftjoin('roles', 'jocom_sys_admin.role_id', '=', 'roles.id')
					->select('jocom_sys_admin.*', 'roles.role_name')
					->where('jocom_sys_admin.id', '=', $id)
					->first();
		// var_dump($user);
		return $user;

	}

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
		$user = DB::table('jocom_sys_admin')
					->where('id', $id)
					->update($data);
		return $user;
	}

	public static function verify_login($get)
	{
		$username   = $get['username'];
        $password   = $get['password'];
        $ip         = $get['ip'];
        $date       = $get['date'];

		$login      = new Login;
        $count      = $login->CheckFailedAttempt($username, $ip);

        if($count < 5 ) {
            if (Auth::attempt(['username' => $username, 'password' => $password, 'active_status' => 1]))
            {
                $login->add_attempt($username, $ip, $date, 1);
                $username   = Auth::user()->username;
                $role_id    = Auth::user()->role_id;

                // Session::put('role_id', $role_id);
                // Session::put('username', $username);
                // Session::put('user_id', Auth::id());

                $user = User::find(Auth::id());
                $user->last_login = date('Y-m-d H:i:s');
                $user->timestamps = false;
                $user->save();

                $tempdata['username'] = $username;

            } else {

                $user = User::where('username', $username)->first();

                if( $user && $user->password == md5($password)) {
                    $login->add_attempt($username, $ip, $date, 1);

                    $user->password     = Hash::make($password);
                    $user->timestamps   = false;
                    $user->modify_by    = 'Admin';
                    $user->modify_date  = date('Y-m-d H:i:s');

                    if($user->save()) {
                        $user2 = User::where('username', $username)->first();

                        Auth::login($user2, true);
                        // Session::put('role_id', $user2->role_id);
                        // Session::put('username', $user2->username);
                        // Session::put('user_id', Auth::id());

                        $tempdata['username'] = $username;
                    }

                } else {
                    $login->add_attempt($username, $ip, $date, 0);

                    $tempdata['status_msg']  = '#806';
                }
            }
        } else {
            $tempdata['status_msg']  = '#806';
        }

		return $tempdata;
	}

}
