<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class City extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
	/**
	 * Table for state.
	 *
	 * @var string
	 */
	protected $table = 'jocom_cities';

	
}
