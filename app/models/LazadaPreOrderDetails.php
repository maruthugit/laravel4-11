<?php

class LazadaPreOrderDetails extends Eloquent
{
    
    protected $table = 'jocom_lazada_pre_order_items';
    
    public static function getByOrderID($order_id){
        
        $result = LazadaPreOrderDetails::where('order_id', '=', $order_id)->get();
        return $result;
        
    }
    
    
    
}
