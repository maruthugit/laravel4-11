<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class TDetails extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
	/**
	 * Table for transaction details.
	 *
	 * @var string
	 */
	protected $table = 'jocom_transaction_details';

	
    public function transaction()
    {
        return $this->belongsTo('Transction');
    }

   

	// public function scopeGetTrans($query)
	// {
	// 	return $query->where('id', '=', 320);        
		
	// }

	
}
