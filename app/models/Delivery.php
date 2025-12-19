<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Delivery extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_product_delivery';

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

    public function getByZone($productId)
    {
        return $this->select('jocom_product_delivery.zone_id', 'jocom_product_delivery.price', 'jocom_zones.name')
            ->leftJoin('jocom_zones', 'jocom_product_delivery.zone_id', '=', 'jocom_zones.id')
            ->where('product_id', '=', $productId)
            ->get();
    }

	public function getZonesByProduct($productId)
	{
		return self::select('jocom_product_delivery.id', 'jocom_product_delivery.price', 'jocom_zones.id as zone_id', 'jocom_zones.name as zone_name', 'jocom_zones.country_id')
			->join('jocom_zones', 'jocom_product_delivery.zone_id', '=', 'jocom_zones.id')
			->where('product_id', '=', $productId)
			->get();
	}

    public function getDeliveryCountries()
    {
        return DB::table('jocom_countries')
                            ->where('status', '=', '1')
                            ->orderBy('name', 'ASC')
                            ->get();
    }

    public function getStateList($country_id)
    {
        return DB::table('jocom_country_states as state')
                        ->select('state.id', 'state.name')
                        ->where('state.country_id', '=', $country_id)
                        ->orderBy('state.name', 'ASC')
                        ->get();
    }

    public function getCityList($state_id) 
    {
        return DB::table('jocom_cities as city')
                        ->select('city.id', 'city.name')
                        ->leftjoin('jocom_country_states as state', 'state.id', '=', 'city.state_id')
                        ->where('city.state_id', '=', $state_id)
                        ->orderBy('city.name', 'ASC')
                        ->get();
    }
}
