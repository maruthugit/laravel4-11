<?php

class PurchaseOrder extends Eloquent
{
    protected $table = 'jocom_purchase_order';

    /**
     * Validation rules for creating a new purchase order.
     * @var array
     */
    public static $rules = array(
        'po_date' => 'required',
        'payment_terms' => 'required',
        'delivery_date' => 'required',
        'seller_id' => 'required',
        'seller' => 'required',
        'warehouse_id' => 'required',
        'warehouse' => 'required',
        'manager' => 'required',
        'qrcode' => 'required|array',
    );

    public static $message = array(
        'qrcode.required'=>'Add product is required',
    );

    public function lists() {
        return DB::table('jocom_purchase_order as po')
                ->join('jocom_seller as seller', 'po.seller_id', '=', 'seller.id')
                ->leftJoin('jocom_einvoice as einv', 'po.id', '=', 'einv.po_id')
                ->whereIn('po.status',['1','4'])
                ->select('po.id', 'po.po_no', DB::raw("DATE_FORMAT(po.po_date, '%Y-%m-%d') as po_date"), DB::raw("DATE_FORMAT(po.delivery_date, '%Y-%m-%d') as delivery_date"), 'seller.company_name', 'po.grn_id', 'einv.id as einv_id', 'einv.status as einv_status','po.status');
    }
    public function dashboardlists() {
        return DB::table('jocom_purchase_order as po')
                ->join('jocom_seller as seller', 'po.seller_id', '=', 'seller.id')
                ->leftJoin('jocom_einvoice as einv', 'po.id', '=', 'einv.po_id')
                ->whereIn('po.status',['1','4'])
                ->select('po.id', 'po.po_no', DB::raw("DATE_FORMAT(po.po_date, '%Y-%m-%d') as po_date"), DB::raw("DATE_FORMAT(po.delivery_date, '%Y-%m-%d') as delivery_date"), 'seller.company_name', 'po.grn_id', 'einv.id as einv_id', 'einv.status as einv_status','po.status');
    }
    
    public function orderlists() {
        return DB::table('jocom_purchase_order as po')
                ->join('jocom_seller as seller', 'po.seller_id', '=', 'seller.id')
                ->leftJoin('jocom_einvoice as einv', 'po.id', '=', 'einv.po_id')
                ->whereIn('po.status',['1','2','3','4'])
                ->select('po.id', 'po.po_no', DB::raw("DATE_FORMAT(po.po_date, '%Y-%m-%d') as po_date"), DB::raw("DATE_FORMAT(po.delivery_date, '%Y-%m-%d') as delivery_date"), 'seller.company_name', 'po.grn_id', 'einv.id as einv_id', 'einv.status as einv_status','po.status');
    }
    public function editlists($id) {
        return DB::table('jocom_purchase_order as po')
                ->join('jocom_seller as seller', 'po.seller_id', '=', 'seller.id')
                ->leftJoin('jocom_einvoice as einv', 'po.id', '=', 'einv.po_id')
                ->whereIn('po.status',['1','4'])
                ->where('po.id', '=', $id)
                ->select('po.id', 'po.po_no', DB::raw("DATE_FORMAT(po.po_date, '%Y-%m-%d') as po_date"), DB::raw("DATE_FORMAT(po.delivery_date, '%Y-%m-%d') as delivery_date"), 'seller.company_name', 'po.grn_id', 'einv.id as einv_id', 'einv.status as einv_status','po.status')
                ->first();

    }

    public function get($id) {
        return DB::table('jocom_purchase_order as po')
                ->join('jocom_warehouse_location as warehouse', 'po.warehouse_location_id', '=', 'warehouse.id')
                ->join('jocom_seller as seller', 'po.seller_id', '=', 'seller.id')
                ->leftJoin('jocom_einvoice as einv', 'po.id', '=', 'einv.po_id')
                ->whereIn('po.status',['1','4'])
                ->where('po.id', '=', $id)
                ->select('po.*', 'warehouse.name as warehouse_name', 'seller.company_name','einv.id as einv_id', 'einv.status as einv_status')
                ->first();
    }

    public function id_no() {
        return DB::table('jocom_purchase_order')
                ->whereIn('status',['1','4'])
                ->select('id', 'po_no')
                ->get();
    }

    public static function po_update_log($from_data = null, $discount_percent = null, $manager = null, $remark = null,$delivery_date= null,$updated_by = null,$po_id=null,$from_data_old = null, $discount_percent_old = null, $manager_old = null, $remark_old = null,$delivery_date_old= null,$updated_by_old = null,$product_new=null,$product_old=null,$delete_pr,$f_product_add) 
    {
        

        if ($delete_pr!="[]") {
         $product_delete=$delete_pr;
        }
        else{
            $product_delete="";  
        }
        if ($f_product_add!="[]") {
         $product_add=$f_product_add;
        }
        else{
            $product_add="";  
        }
        $details = array();
        $details['from_data'] = $from_data;
        $details['discount_percent'] = $discount_percent;
        $details['manager'] = $manager;
        $details['remark'] = $remark;
        $details['delivery_date'] = $delivery_date;
        $details['updated_by'] = $updated_by;
        $details['po_id'] =$po_id;
        $details['from_data_old'] = $from_data_old;
        $details['discount_percent_old'] = $discount_percent_old;
        $details['manager_old'] = $manager_old;
        $details['remark_old'] = $remark_old;
        $details['delivery_date_old'] = $delivery_date_old;
        $details['updated_by_old'] = $updated_by_old;
        $details['product'] = $product_new;
        $details['product_old'] = $product_old;
        $details['product_delete'] =$product_delete;
        $details['product_add'] =$product_add;
        $details['insert_date'] = date('Y-m-d H:i:s');

        // print"<pre>";    
        // print_r($details);
        // exit;
        $insert_id = DB::table('jocom_po_update_log')->insertGetId($details);
     
        return $insert_id;
        
    }
}
