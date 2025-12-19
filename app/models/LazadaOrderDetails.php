<?php

class LazadaOrderDetails extends Eloquent
{
    
    protected $table = 'jocom_lazada_order_items';
    
    public static function getByOrderID($order_id){
        
        $result = LazadaOrderDetails::where('order_id', '=', $order_id)->get();
        return $result;
        
    }
    
    
    
}
