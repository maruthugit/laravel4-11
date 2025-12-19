<?php

class TAQBINReservedNumber extends Eloquent
{
    
    protected $table = 'jocom_taqbin_reserved_number';
    
    
    
    public static function getInfoByNumber($reserved_number){
        
        $result = TAQBINReservedNumber::where('reserved_number',$reserved_number);
        return $result;
        
    }
    
    
    public static function getNextNumber(){
        
        $result = TAQBINReservedNumber::where("is_use",0)
                ->where("transaction_item_logistic_id",0)
                ->where("activation",1)
                ->orderBy('id', 'asc')
                ->first();
        
        return $result;
        
    }
    
    
}
