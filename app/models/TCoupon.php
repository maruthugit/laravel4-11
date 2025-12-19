<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class TCoupon extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
	/**
	 * Table for relationship between transaction and coupon.
	 *
	 * @var string
	 */
	protected $table = 'jocom_transaction_coupon';

	
	public function transaction()
    {
        return $this->hasOne('Transaction');
    }

    public function coupon()
    {
        return $this->hasOne('Coupon', 'coupon_code');
    }

    public static function getByTransaction($transaction_id){
        
        $query = TCoupon::where("transaction_id",$transaction_id)->first();
        return $query;
        
    }
	
}
