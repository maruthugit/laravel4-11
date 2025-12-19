<?php

class WarehouseLocation extends Eloquent
{
    protected $table = 'jocom_warehouse_location';

    /**
     * Validation rules for creating a new location.
     * @var array
     */
    public static $rules = array(
        'name' => 'required',
        'address_1' => 'required',
        'postcode' => 'required|numeric',
        'country' => 'required',
        'state' => 'required',
        'city' => 'required',
        'pic_name' => 'required',
        'pic_contact' => 'required'
    );

    public function activeList() {
        return DB::table('jocom_warehouse_location')
                ->select('id', 'name', 'address_1', 'address_2', 'postcode', 'city', 'city_id', 'state', 'state_id', 'country', 'pic_name', 'pic_contact', 'status')
                ->where('status', '=', 1)
                ->get();
    }

    public function activeDataList() {
        return WarehouseLocation::where('status', '=', 1)->select('id', 'name', 'address_1', 'address_2', 'pic_contact', 'pic_name');
    }
}
