<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Price extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_product_price';

	/**
     * The name of the "created at" column.
     *
     * @var string
     */
    const CREATED_AT = null;

    /**
     * The name of the "updated at" column.
     *
     * @var string
     */
    const UPDATED_AT = null;

	/**
     * Display price for the specified resource.
     *
     * @param  int  $product_id
     * @return Response
     */
	public function getPrice($id) {

		$price = $this->whereProduct_id($id)->first();
		return $price;

	}

	/**
	* returns money as a formatted string
	* @param integer $amount
	* @param string $symbol currency symbol
	* @return string
	*/
	function money($amount, $symbol = '$') {
		return $symbol . money_format('%i', $amount);
	}

	public function getPrices($productId)
	{
		return $this->where('status', '<>', 2)
			->where('product_id', '=', $productId)
			->get();
	}

	public function getActivePrices($productId)
	{
		return self::where('status', '=', 1)
			->where('product_id', '=', $productId)
			->orderBy('default', 'desc')
			->orderBy('price', 'asc')
			->get();
	}

	public function getMultiActivePrices(array $productIds)
	{
		return self::where('status', '=', 1)
			->whereIn('id', array_keys($productIds))
			->get();
	}

}
