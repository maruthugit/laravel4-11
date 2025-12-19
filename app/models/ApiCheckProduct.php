<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ApiCheckProduct extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public static function get_product_zone($code)
    {
    	return DB::table('jocom_products as product')
    				->select('pdelivery.zone_id')
    				->leftjoin('jocom_product_delivery as pdelivery', 'pdelivery.product_id', '=', 'product.id')
    				->where('product.qrcode', '=', $code)
    				->get();
    }

    public static function get_package_zone($code) 
    {
        return DB::table('jocom_product_package as package')
                    ->select('pdelivery.zone_id')
                    ->leftjoin('jocom_product_package_product as pp', 'pp.package_id', '=', 'package.id')
                    ->leftjoin('jocom_product_price as price', 'price.id', '=', 'pp.product_opt')
                    ->leftjoin('jocom_products as product', 'product.id', '=', 'price.product_id')
                    ->leftjoin('jocom_product_delivery as pdelivery', 'pdelivery.product_id', '=', 'product.id')
                    ->where('package.qrcode', '=', $code)
                    ->get();
    }

    public static function get_zone_country($id) 
    {
    	return DB::table('jocom_zones')
    				->select('country_id')
    				->where('id', '=', $id)
    				->first();
    }

    public static function get_zone_state($id) 
    {
    	return DB::table('jocom_zone_states')
    				->select('states_id')
    				->where('zone_id', '=', $id)
    				->get();
    }

    public static function get_zone_city($id) 
    {
    	$cities = DB::table('jocom_zone_cities')
    				->select('city_id')
    				->where('zone_id', '=', $id)
    				->get();

    	return $cities;
    }

	public static function get_max_weight($id) 
    {
        return DB::table('jocom_zones')
                    ->where('id', '=', $id)
                    ->first();

    }
}
?>