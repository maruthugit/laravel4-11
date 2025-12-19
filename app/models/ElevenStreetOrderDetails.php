<?php

class ElevenStreetOrderDetails extends Eloquent
{
    
    protected $table = 'jocom_elevenstreet_order_details';
    
    public static function getByOrderID($order_id){
        
        $result = ElevenStreetOrderDetails::where('order_id', '=', $order_id)->get();
        return $result;
        
    }
    
    public static function findElevenProductDetails($order_id,$product_name){
    
        $result = ElevenStreetOrderDetails::where('order_id', '=', $order_id)
                ->where('product_name', '=', $product_name)
                ->first();
        return $result;
        
}
    
    
}
