<?php

class EInvoice extends Eloquent
{
    protected $table = 'jocom_einvoice';

    /**
     * Validation rules for creating a new purchase order.
     * @var array
     */
    public static $rules = array(
        'einv_date' => 'required',
    );

    public static $message = array(
        'einv_date.required'=>'eInvoice Date is required',
    );

    public function lists() {
        return DB::table('jocom_einvoice as einv')
                ->join('jocom_seller as seller', 'einv.seller_id', '=', 'seller.id')
                ->where('einv.status', '=', 1)
                ->select('einv.id', 'einv.einv_no', DB::raw("DATE_FORMAT(einv.einv_date, '%Y-%m-%d') as einv_date"), 'seller.company_name');
    }

}
