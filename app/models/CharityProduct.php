<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class CharityProduct extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	protected $fillable = ['product_price_id', 'qty', 'quota', 'charity_id'];

	public $timestamps = false;
	/**
	 * Table for Charity product qty.
	 *
	 * @var string
	 */
	protected $table = 'jocom_charity_product';

	public function getCharityQuantity($price_id, $charity_id)
	{
		return self::select('qty')
			->where('charity_id', '=', $charity_id)
			->where('product_price_id', '=', $price_id)
			->first();
	}
	
}
