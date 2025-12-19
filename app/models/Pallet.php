<?php

class Pallet extends Eloquent
{
    protected $table = 'jocom_pallet';

    public function setCreatedAtAttribute($value)
    {
        // Disable Eloquent default `created_at` column in DB
    }

    public function datatables()
    {
        return Pallet::select('id','pallet_code','pallet_price','pallet_Description');
    }

    public function scopeAlphabeticalOrder($query)
    {
        return $query->orderBy('pallet_code');
    }

   
}
