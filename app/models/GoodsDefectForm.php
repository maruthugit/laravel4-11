<?php

class GoodsDefectForm extends Eloquent
{
    protected $table = 'jocom_goods_defect_form';

    /**
     * Validation rules for creating a new goods defect form.
     * @var array
     */
    public static $rules = array(
        'type' => 'required',
        'warehouse_id' => 'required',
        // 'seller_id' => 'required',
        'reason' => 'required',
        'product_id' => 'required|array',
        'quantity' => 'required|array',
    );

    public static $message = array(
        'product_id.required'=>'Add product is required.',
        'warehouse_id.required'=>'The seller field is required.',
        // 'seller_id.required'=>'The warehouse field is required.',
    );

    public function get($id) {
        return DB::table('jocom_goods_defect_form as gdf')
                ->join('jocom_warehouse_location as warehouse', 'gdf.warehouse_id', '=', 'warehouse.id')
                // ->join('jocom_seller as seller', 'gdf.seller_id', '=', 'seller.id')
                ->where('gdf.id', '=', $id)
                ->select('gdf.*', 'warehouse.name as warehouse_name')
                ->first();
    }
}
