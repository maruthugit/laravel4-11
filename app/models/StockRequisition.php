<?php

class StockRequisition extends Eloquent
{
    protected $table = 'jocom_stock_requisition';

    /**
     * Validation rules for creating a new stock transfer.
     * @var array
     */
    public static $rules = array(
        'delivery_date' => 'required',
        'campaign_end' => 'required',
        'warehouse_id' => 'required',
        'product_id' => 'required|array',
        'quantity' => 'required|array',
    );

    public static $message = array(
        'product_id.required'=>'Add product is required.',
        'warehouse_id.required'=>'The warehouse field is required.',
        'delivery_date.required'=>'The Campaign From Date field is required.',
        'campaign_end.required'=>'The Campaign End Date field is required.',
    );

    public function get($id) {
        return DB::table('jocom_stock_requisition as st')
                 ->join('jocom_warehouse_location as warehouse', 'st.warehouse_id', '=', 'warehouse.id')
                ->where('st.id', '=', $id)
                ->select('st.*','warehouse.name as warehouse_name')
                ->first();
    }
}
