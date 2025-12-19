<?php

class StockTransfer extends Eloquent
{
    protected $table = 'jocom_stock_transfer';

    /**
     * Validation rules for creating a new stock transfer.
     * @var array
     */
    public static $rules = array(
        'delivery_date' => 'required',
        'seller_id' => 'required',
        'warehouse_id' => 'required',
        'product_id' => 'required|array',
        'quantity' => 'required|array',
    );

    public static $message = array(
        'product_id.required'=>'Add product is required.',
        'seller_id.required'=>'The seller field is required.',
        'warehouse_id.required'=>'The warehouse field is required.',
    );

    public function get($id) {
        $getseller=StockTransfer::find($id);
        if($getseller->seller_id!="0"){
        return DB::table('jocom_stock_transfer as st')
                ->join('jocom_warehouse_location as warehouse', 'st.warehouse_id', '=', 'warehouse.id')
                ->join('jocom_seller as seller', 'st.seller_id', '=', 'seller.id')
                ->where('st.id', '=', $id)
                ->select('st.*', 'warehouse.name as warehouse_name', 'seller.company_name as seller_name')
                ->first();
        }else{
          return DB::table('jocom_stock_transfer as st')
                ->join('jocom_warehouse_location as warehouse', 'st.warehouse_id', '=', 'warehouse.id')
                ->where('st.id', '=', $id)
                ->select('st.*', 'warehouse.name as warehouse_name')
                ->first();  
        }
    }
}
