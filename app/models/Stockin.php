<?php

class Stockin extends Eloquent
{
    protected $table = 'jocom_pallet_stockin';

    public function setCreatedAtAttribute($value)
    {
        // Disable Eloquent default `created_at` column in DB
    }

 


   
}
