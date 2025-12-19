<?php

class Stocks extends Eloquent
{
    protected $table = 'jocom_stocktransfer';

    public function setCreatedAtAttribute($value)
    {
     // Disable Eloquent default `updated_at` column in DB
     }

     public static function get_stk_item() {
        return DB::table('jocom_stocktransfer_details')
                    ->select('product','quantity')
                  
                    ->first();
    }

    

     public static function insert_stock_details(array $inputs) {
        return DB::table('jocom_stocktransfer_details')
                    ->insert($inputs);
    }

    public function scopeBrandCode($query, $code)
    {
        return $query->where('st_no', '=', $code);
    }

     public function scopeAlphabeticalOrder($query)
    {
        return $query->orderBy('st_no');
    }

    public static function insert_stocks_details(array $inputs) {
        return DB::table('jocom_stocktransfer_details')
                    ->insert($inputs);
    }

   
}

