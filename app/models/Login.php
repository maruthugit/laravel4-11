<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Login extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'login_attempts';


	public function add_attempt($username, $ip, $date, $status) {
		$attempts 				= new Login;
		$attempts->username 	= $username;
		$attempts->ip_address 	= $ip;
		$attempts->attempt_at 	= $date;
		$attempts->status 		= $status;
		$attempts->timestamps 	= false;

		if($attempts->save()) return true;

		return false;

	}

	public function CheckFailedAttempt($username, $ip) {
		$count			= 0;
		$date 			= new DateTime;
		$date->modify('-30 minutes');
		$formatted_date	= $date->format('Y-m-d H:i:s');

		$attempts  	= DB::table('login_attempts')
						->where('username', '=', $username)
						->where('ip_address', '=', $ip)
						->where('attempt_at' ,'>=', $formatted_date)
						->where('status', '=', '0')
						->get();

		$count = count($attempts);

		return $count;
	}
}