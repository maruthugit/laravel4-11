<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class EInvoiceSchedule extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	
	/**
	 * Table for state.
	 *
	 * @var string
	 */
	protected $table = 'jocom_generate_einvoice_schedule';

	
}
