<?php

class Supplier extends Eloquent
{
    protected $table = 'jocom_supplier';

    public function setCreatedAtAttribute($value)
    {
        // Disable Eloquent default `created_at` column in DB
    }

    public function datatables()
    {
        return Supplier::select('id', 'supplier_name','supplier_code');
    }

    public function scopeAlphabeticalOrder($query)
    {
        return $query->orderBy('supplier_name');
    }

   
}
