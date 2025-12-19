<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class TPayPal extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
	/**
	 * Table for transaction PayPal details.
	 *
	 * @var string
	 */
	protected $table = 'jocom_paypal_transaction';

	
    public function transaction()
    {
        return $this->belongsTo('Transction');
    }

	
}
