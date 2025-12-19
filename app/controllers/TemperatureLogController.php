<?php

class TemperatureLogController extends BaseController {

	public function index() {
		return View::make('temperature_log');
	}

	public function store() {
		$name = Input::get('name');
		$phone = Input::get('phone');
		$temperature = Input::get('temperature');
		$logged_at = date('Y-m-d H:i:s');
		DB::table('temperature_log')->insert([
			'name' => $name,
			'phone' => $phone,
			'temperature' => $temperature,
			'type' => Input::get('type'),
			'logged_at' => $logged_at,
		]);

		$safe = false;
		if ($temperature <= 37.4) {
			$safe = true;
		}
		
		$message = '';
		if ($safe) {
		    $message = 'Your temperature is normal. You may now proceed.';
		} else {
		    $message = 'Your temperature is abnormal. Please seek medical advice.';
		}

		return Response::json(['safe' => $safe, 'name' => $name, 'phone' => $phone, 
							  'temperature' => $temperature, 'message' => $message,
							  'logged_at' => date_format(date_create($logged_at), 'g:i A')]);
	}

}