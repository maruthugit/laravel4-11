<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class CouponStatic extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
	/**
	 * Table for state.
	 *
	 * @var string
	 */
	protected $table = 'jocom_static_coupon';
    
     public static $rules = array(
        'coupon_code'=>'required|unique:jocom_static_coupon',
        'description'=>'required',
        'valid_from'=>'required',
        'valid_to'=>'required'
    );
      public static $editrule = array(
        'coupon_code'=>'required',
        'description'=>'required',
        'valid_from'=>'required',
        'valid_to'=>'required'
    );
	
}
