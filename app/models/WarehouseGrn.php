<?php

class WarehouseGrn extends Eloquent
{
    protected $table = 'jocom_warehouse_grn';

    /**
     * Validation rules for creating a new grn.
     * @var array
     */
    public static $rules = array(
        'grn_date' => 'required',
        'po_id' => 'required',
        'po_no' => 'required',
        'whloc_id' => 'required',
        'seller_id' => 'required',
        'seller_do_no' => 'required',
        'seller_driver_name' => 'required',
        'deliverby' => 'required',
        'receivedby' => 'required',
        'verifiedby' => 'required',
    );

    public static $updateRules = array(
        'grn_id' => 'required',
        'seller_do_no' => 'required',
        'seller_driver_name' => 'required',
        'deliverby' => 'required',
        'receivedby' => 'required',
        'verifiedby' => 'required',
    );

    public static $message = array(
        'po_id.required' => 'Select PO Number is requried',
        'whloc_id.required' => 'Select Warehouse Location is required',
        'seller_id.required' => 'Select Supplier/Seller Name is required',
    );

    public function lists() {
        return DB::table('jocom_warehouse_grn as grn')
                ->join('jocom_seller as seller', 'grn.seller_id', '=', 'seller.id')
                ->join('jocom_purchase_order as po', 'grn.po_id', '=', 'po.id')
                ->where('grn.status', '!=', 2)
                ->select('grn.id', 'grn.grn_no','po.po_no', DB::raw("DATE_FORMAT(grn.grn_date, '%Y-%m-%d') as grn_date"), 'seller.company_name','grn.status');
    }

    public function get($id) {
        return DB::table('jocom_warehouse_grn as grn')
                ->join('jocom_warehouse_location as warehouse', 'grn.warehouse_loc_id', '=', 'warehouse.id')
                ->join('jocom_seller as seller', 'grn.seller_id', '=', 'seller.id')
                ->join('jocom_purchase_order as po', 'grn.po_id', '=', 'po.id')
                ->where('grn.status', '!=', '2')
                ->where('grn.id', '=', $id)
                ->select('grn.*', 'warehouse.id as warehouse_id', 'warehouse.name as warehouse_name', 'warehouse.address_1', 'warehouse.address_2', 'warehouse.pic_name', 'warehouse.pic_contact', 'seller.company_name', 'po.po_no')
                ->first();
    }

}
