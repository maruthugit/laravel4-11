<?php

class Stockout extends Eloquent
{
    protected $table = 'jocom_pallet_stockout';

    public function setCreatedAtAttribute($value)
    {
        // Disable Eloquent default `created_at` column in DB
    }

 


   
}
