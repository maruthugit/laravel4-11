<?php


class Courier extends Eloquent {

    protected $table = 'jocom_courier';

    
    public static function getCourierByCode($couriercode){
        
        
        $query = Courier::where('courier_code',$couriercode)->first();
        
        return $query;
        
    }
    
    
}
