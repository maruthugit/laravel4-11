<?php

class ExchangeRate extends Eloquent
{
    protected $table = 'jocom_exchange_rate';
    
    
    public static function getExchangeRate($formCurrency , $toCurrency){
        
        $Rate = DB::table('jocom_exchange_rate AS JERL')->select(array(
            'JERL.id',
            'JERL.currency_code_from',
            'JERL.amount_from',
            'JERL.currency_code_to',
            'JERL.amount_to',
            'JERL.updated_at',
            'JERL.updated_by'
           ))->where('JERL.currency_code_from', $formCurrency)->where('JERL.currency_code_to', $toCurrency)->first();
           
           return $Rate;
        
    
    }
    
    
    
}

?>