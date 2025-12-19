<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class ProductPriceSeller extends Eloquent  {

	protected $table = 'jocom_product_price_seller';
        
        public static function gtSellerProductPrice($price_option_id){
            
            $result = DB::table('jocom_product_price_seller AS JPPS')
                ->leftJoin('jocom_seller AS JS', 'JS.id', '=', 'JPPS.seller_id')
                ->where('JPPS.product_price_id', '=', $price_option_id)
                ->where('JPPS.activation', '=', 1)
                ->select('JPPS.*','JS.company_name')
                ->get();

            return $result;
            
        }
        
        public static function getPriceBySeller($price_option_id,$seller_id){
            
            $result = ProductPriceSeller::where("product_price_id",$price_option_id)
                    ->where("seller_id",$seller_id)
                    ->where("activation",1)
                    ->first();
            
            return $result;
            
        }
        
        
        
	
}
