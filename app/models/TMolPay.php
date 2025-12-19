<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class TMolPay extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
	/**
	 * Table for transaction MolPay details.
	 *
	 * @var string
	 */
	protected $table = 'jocom_molpay_transaction';

	
    public function transaction()
    {
        return $this->belongsTo('Transction');
    }
  
	
}
