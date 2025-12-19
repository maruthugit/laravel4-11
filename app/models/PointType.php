<?php

class PointType extends Eloquent
{

    const JOPOINT = 1;
    const BCARD   = 2;
    const CASH    = 3;

    public $timestamps = false;

    public function rules()
    {
        return [
            'type'        => 'required',
            'earn_rate'   => 'required|numeric',
            'redeem_rate' => 'required|numeric',
            'status'      => 'required|digits_between:0,2',
        ];
    }

    public function scopeActive($query)
    {
        return $query->where('status', '=', 1);
    }

    public function scopeGetActive($query)
    {
        return $query->where('status', '=', 1)->get();
    }

    public function getDatatables()
    {
        return self::select('id', 'type', 'earn_rate', 'redeem_rate', 'status')
            ->where('status', '!=', 2);
    }

}
