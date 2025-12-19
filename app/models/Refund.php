<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Refund extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	
	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'jocom_refund';
    
    public $timestamps = false;

	public static $rules = array(
        'trans_id'              => 'required',
        // 'grand_total'			=> 'required',
        // 'type'			        => 'required',
    );

    public static $message = array(
        'trans_id.required'			=> 'Please select one(1) transaction.',
        // 'grand_total.required'		=>'Please select at least one(1) item to refund.',
        // 'type.required'		        =>'Please select one(1) refund type.',
    );

    public static function get_trans_item($id) {
    	return DB::table('jocom_transaction_details')
    				->select('product_id', 'p_option_id', 'price', 'p_referral_fees', 'p_referral_fees_type', 'unit', 'gst_rate_item', 'disc', 'total')
    				->where('id', '=', $id)
    				->first();
    }

    // 14/04/2022 - User for refund's permission
    public static function permission($username, $roles) {
            $allow =  DB::table('jocom_refund_permission')
                        ->select('*')
                        ->where('username', '=', $username)
                        ->whereIn('role', explode(',', $roles))
                        ->where('status', '=', 1)
                        ->first();
            
		return $allow ? true : false;
    }

    // 14/04/2022 - User for refund's permission
    public static function getPermission($id) {
        return  DB::table('jocom_refund_permission')
                    ->select('*')
                    ->where('id', '=', $id)
                    ->first();
    }

    public static function insert_refund_with_tranID($trans_id) {     
        $getBuyerID = Transaction::selectRaw('buyer_id')
                        ->where('id', $trans_id)
                        ->first();

        $refundId = DB::table('jocom_refund')
                ->insertGetId(
                    ['trans_id'         => $trans_id,
                    'buyer_id'          => $getBuyerID]
                );    
    }

    public static function insert_refund($inputs, $csv, array $attachment) {
        $test = json_encode($inputs);
        
        $getBuyerID = Transaction::selectRaw('buyer_id')
                        ->where('id', $inputs['transaction_id'])
                        ->first();

        // Insert refund data and get its ID
        $refundId = DB::table('jocom_refund')
                            ->insertGetId(
                                ['trans_id'         => $inputs['transaction_id'],
                                'buyer_id'          => $getBuyerID->buyer_id,
                                'amount'            => $inputs['amount'],
                                'status'            => "pending",
                                'customer_name'     => $inputs['customer_name'],
                                'ic_no'             => $inputs['ic_no'],
                                'address'           => $inputs['address'],
                                'postcode'          => $inputs['postcode'],
                                'hp_no'             => $inputs['hp_no'],
                                'email'             => $inputs['email'],
                                'bank_name'         => $inputs['bank_name'],
                                'bank_account_no'   => $inputs['bank_account_no'],
                                'order_no'          => $inputs['order_no'],
                                'platform_store'    => $inputs['platform_store'],
                                'remarks'           => "remarks",
                                'created_from'      => "excel upload",
                                'created_by'        => Session::get('username'),
                                'created_date'      => date("Y-m-d H:i:s")]
                            );

        
        $transactionDetails = DB::table('jocom_transaction_details')
                            ->selectRaw('id, product_id, p_option_id as price_id, price, p_referral_fees,
                                p_referral_fees_type, unit, gst_amount as gst_rate, disc, total')
                            ->where('transaction_id', $inputs['transaction_id'])
                            ->get();

        $test2 = json_encode($transactionDetails);

        // Automatically add products based  - v1
        // foreach($transactionDetails as $test2A){ 
        //     $refundDetails =  DB::table('jocom_refund_details')
        //                         ->insert(
        //                             ['refund_id' => $refundId,
        //                             'trans_detail_id' => $test2A->id, //id from jocom_transaction_details
        //                             'product_id' => $test2A->product_id,
        //                             'price_id' => $test2A->price_id, //p_option_id from jocom_transaction_details
        //                             'price' => $test2A->price,
        //                             'p_referral_fees' => $test2A->p_referral_fees,
        //                             'p_referral_fees_type' => $test2A->p_referral_fees_type,
        //                             'unit' => $test2A->unit,
        //                             'gst_rate' => $test2A->gst_rate,
        //                             'disc' => $test2A->disc,
        //                             'total' => $test2A->total]
        //                         );
        // }
        
        // Add supporting docs
        $testAttachment = json_encode($attachment);
        foreach($attachment as $attachmentTest){
            $refundAttachment = DB::table('jocom_refund_supp_docs')
                                ->insert(
                                    ['refund_id' => $refundId,
                                    'supp_docs' => $attachmentTest,
                                    'created_at' => date("Y-m-d H:i:s")]
                                );

        }

        Redirect::to('refund/import/')->with(['refundId' => $refundId]); // Pass back refund id to create pdf
    }

    public static function get_refund_supp_docs($id) {
        return DB::table('jocom_refund_supp_docs')
                    ->select('jocom_refund_supp_docs.supp_docs')
                    ->where('jocom_refund_supp_docs.refund_id', '=', $id)
                    ->get();
    }

    public static function update_refund_details($prod_details) { // Update refund product details 

        $update_refund_details = DB::table('jocom_refund_details')
                ->where('jocom_refund_details.id', '=', $prod_details['id'])
                ->update([
                        // 'unit'      => $prod_details['quantity'],
                        // 'price'     => $prod_details['price'],
                        // 'total'     => $prod_details['quantity'] * $prod_details['price'],
                        'approved'  => 1
                    ]);
    }

    public static function update_refund_others($prod_details) { // Update refund other's details 

        $update_refund_others = DB::table('jocom_refund_details')
                ->where('jocom_refund_details.id', '=', $prod_details['id'])
                ->update([
                        'approved'  => 1
                    ]);
    }

    // public static function update_refund_remark($id, $remark) { // Update refund product details 
    //     $test = DB::table('jocom_refund')
    //                 ->where('jocom_refund.id', '=', $id)
    //                 ->update([
    //                         'remarks'     => $remark
    //                     ]);
    // }

    public static function update_refund($id,$inputs, $actions) { // Update total refund amd remark 
        if ($actions == "Save Edit"){
            return DB::table('jocom_refund')
                        ->where('jocom_refund.id', '=', $id)
                        ->update([
                                'amount'            => $inputs['total'],
                                'customer_name'     => $inputs['customer_name'],
                                'ic_no'             => $inputs['ic_no'],
                                'address'           => $inputs['address'],
                                'postcode'          => $inputs['postcode'],
                                'hp_no'             => $inputs['hp_no'],
                                'email'             => $inputs['email'],
                                'bank_name'         => $inputs['bank_name'],
                                'bank_account_no'   => $inputs['bank_account'],
                                'order_no'          => $inputs['order_no'],
                                'platform_store'    => $inputs['platform_store'],
                                'remarks'           => $inputs['remarks'],
                                'modify_by'         => Session::get('username'),
                                'modify_date'       => date("Y-m-d H:i:s"),
                            ]);
        } else {
            return DB::table('jocom_refund')
                        ->where('jocom_refund.id', '=', $id)
                        ->update([
                                'amount'            => $inputs['total'],
                                'status'            => "approved",
                                'customer_name'     => $inputs['customer_name'],
                                'ic_no'             => $inputs['ic_no'],
                                'address'           => $inputs['address'],
                                'postcode'          => $inputs['postcode'],
                                'hp_no'             => $inputs['hp_no'],
                                'email'             => $inputs['email'],
                                'bank_name'         => $inputs['bank_name'],
                                'bank_account_no'   => $inputs['bank_account'],
                                'order_no'          => $inputs['order_no'],
                                'platform_store'    => $inputs['platform_store'],
                                'remarks'           => $inputs['remarks'],
                                'modify_by'         => Session::get('username'),
                                'modify_date'       => date("Y-m-d H:i:s"),
                            ]);
        }
    }
    public static function insert_refund_details(array $inputs) {
    	return DB::table('jocom_refund_details')
    				->insert($inputs);
    }

    public static function insert_refund_types(array $inputs) {
        return DB::table('jocom_refund_types')
                    ->insert($inputs);
    }

    public static function insert_refund_remark(array $inputs) {
        return DB::table('jocom_refund_remarks')
                    ->insert($inputs);
    }

    public static function get_refund($id) {
        return DB::table('jocom_refund')
                    ->select('jocom_refund.id', 'jocom_refund.status','jocom_refund.created_date', 
                        'jocom_refund.created_by', 'jocom_refund.created_from', 'jocom_refund.amount',
                        'jocom_refund.trans_id', 'jocom_refund.customer_name', 'jocom_refund.ic_no', 
                        'jocom_refund.address', 'jocom_refund.postcode', 'jocom_refund.hp_no', 
                        'jocom_refund.email', 'jocom_refund.bank_name', 'jocom_refund.bank_account_no', 
                        'jocom_refund.order_no', 'jocom_refund.platform_store', 'jocom_refund.remarks', 
                        'jocom_transaction.transaction_date', 'jocom_refund.buyer_id', 'jocom_user.username', 
                        'jocom_transaction.total_amount', 'jocom_transaction.gst_rate'
                    )
                    ->leftjoin('jocom_transaction', 'jocom_transaction.id', '=', 'jocom_refund.trans_id')
                    ->leftjoin('jocom_user', 'jocom_user.id', '=', 'jocom_refund.buyer_id')
                    ->where('jocom_refund.id', '=', $id)
                    ->first();
    }

    // 21/03/2022 -  change query
    public static function get_refund_products($id, $status) {
        $refund_producs = [];

        if ($status == "pending"){
            return DB::table('jocom_refund_details')
                        ->selectRaw('jocom_refund_details.id, jocom_refund_details.product_name, jocom_refund_details.sku, jocom_refund_details.product_id, 
                                jocom_refund_details.item_name as name, jocom_refund_details.label, jocom_refund_details.ori_price as oriPrice,  
                                jocom_refund_details.unit, jocom_refund_details.price, jocom_refund_details.total'    
                        )
                        ->where('jocom_refund_details.refund_id', '=', $id)
                        ->where('jocom_refund_details.product_id', '<>', '0')
                        ->where('jocom_refund_details.approved', '=', 0)
                        ->get();
        } else {
            return DB::table('jocom_refund_details')
            ->selectRaw('jocom_refund_details.id, jocom_refund_details.product_name, jocom_refund_details.sku, jocom_refund_details.product_id, 
                    jocom_refund_details.item_name as name, jocom_refund_details.label, jocom_refund_details.ori_price as oriPrice,  
                    jocom_refund_details.unit, jocom_refund_details.price, jocom_refund_details.total'    
            )
            ->where('jocom_refund_details.refund_id', '=', $id)
            ->where('jocom_refund_details.product_id', '<>', '0')
            ->where('jocom_refund_details.approved', '=', 1)
            ->get();
        }
    }

    public static function get_refund_others($id, $status) {
        if ($status == "pending"){
            return DB::table('jocom_refund_details')
                    ->select('jocom_refund_details.id','jocom_refund_details.product_name','jocom_refund_details.price',
                        'jocom_refund_details.unit','jocom_refund_details.gst_rate','jocom_refund_details.total'
                    )
                    ->where('jocom_refund_details.refund_id', '=', $id)
                    ->where('jocom_refund_details.product_id', '=', '0')
                    ->where('jocom_refund_details.approved', '=', 0)
                    ->get();
        } else {
            return DB::table('jocom_refund_details')
                    ->select('jocom_refund_details.id','jocom_refund_details.product_name','jocom_refund_details.price',
                        'jocom_refund_details.unit','jocom_refund_details.gst_rate','jocom_refund_details.total'
                    )
                    ->where('jocom_refund_details.refund_id', '=', $id)
                    ->where('jocom_refund_details.product_id', '=', '0')
                    ->where('jocom_refund_details.approved', '=', 1)
                    ->get();
        }
    }

    public static function get_refund_types($id) {
        return DB::table('jocom_refund_types')
                    ->where('refund_id', '=', $id)
                    ->get();
    }

    public static function get_refund_remarks($id) {
        return DB::table('jocom_refund_remarks')
                    ->where('refund_id', '=', $id)
                    ->orderBy('created_date')
                    ->get();
    }

    public static function update_refund_status($id, $status) {
        if ($status == "confirmed") {
            return DB::table('jocom_refund')
                        ->where('id', '=', $id)
                        ->update(array('status' => $status,
                            'confirmed_date' => date("Y-m-d H:i:s")    
                        ));

        } else {
            return DB::table('jocom_refund')
                        ->where('id', '=', $id)
                        ->update(array('status' => $status));
        }
    }

    // public static function update_refund_total($id, $total) {
    //     return DB::table('jocom_refund')
    //                 ->where('id', '=', $id)
    //                 ->update(array('amount' => $total));
    // }

    public static function get_cn_no($id) {
        return DB::table('jocom_refund')
                    ->where('id', '=', $id)
                    ->pluck('cn_no');
    }

    public static function get_form_no($id) {
        return DB::table('jocom_refund')
                    ->where('id', '=', $id)
                    ->pluck('form_no');
    }

    public static function get_invoice_no($id) {
        return DB::table('jocom_refund')
                    ->select('jocom_refund.trans_id', 'jocom_transaction.invoice_no')
                    ->leftjoin('jocom_transaction', 'jocom_transaction.id', '=', 'jocom_refund.trans_id')
                    ->where('jocom_refund.id', '=', $id)
                    ->first();
    }

    public static function generate_cn_no() {
        $cur_cn_no  = DB::table('jocom_running')
                            ->where('value_key', '=', 'cn_no')
                            ->pluck('counter');
                            
        return $cur_cn_no;
    }


    public static function get_buyer_details($id) {
        return DB::table('jocom_refund')
                    ->select('jocom_user.id',
                            'jocom_user.full_name',
                            'jocom_user.address1',
                            'jocom_user.address2',
                            'jocom_user.postcode',
                            'jocom_user.city',
                            'jocom_user.state',
                            'jocom_countries.name as country'
                        )
                    ->leftjoin('jocom_user', 'jocom_user.id', '=', 'jocom_refund.buyer_id')
                    ->leftjoin('jocom_countries', 'jocom_countries.id', '=', 'jocom_user.country_id')
                    ->where('jocom_refund.id', '=', $id)
                    ->first();
    }

    public static function update_cn_no($id, $cn_no) {
        return DB::table('jocom_refund')
                    ->where('id', '=', $id)
                    ->update(array('cn_no' => $cn_no));
    }

    public static function update_cn_running($key, $new_num) {
        return DB::table('jocom_running')
                    ->where('value_key', '=', $key)
                    ->update(array('counter' => $new_num));
    }

    public static function insert_cn_details(array $inputs) {
        return DB::table('jocom_cn')
                    ->insert($inputs);
    }

    public static function insert_form_details(array $inputs) {
        return DB::table('jocom_refund_form')
                    ->insert($inputs);
    }

    public static function update_form_details(array $details) {
        return DB::table('jocom_refund_form')
            ->where('jocom_refund_form.refund_id', '=', $details['refund_id'])
            ->where('jocom_refund_form.trans_id', '=', $details['trans_id'])
            ->update([
                    'customer_name'     => $details['customer_name'],
                    'ic_no'             => $details['ic_no'],
                    'hp_no'             => $details['hp_no'],
                    'email'             => $details['email'],
                    'address'           => $details['address'],
                    'postcode'          => $details['postcode'],
                    'bank_name'         => $details['bank_name'],
                    'bank_account'      => $details['bank_account'],
                    'order_no'          => $details['order_no'],
                    'platform_store'    => $details['platform_store'],
                    'total'             => $details['total'],
                    'remarks'           => $details['remarks'],
                    'approve_by'        => $details['approve_by'],
                    'date_approve'      => $details['date_approve'],
                ]);
    }

    public static function get_cn_details($id) {
        return DB::table('jocom_cn')
                ->select('*')
                ->where('refund_id', '=', $id)
                ->first();
    }

    public static function get_form_details($id) {
        return DB::table('jocom_refund_form')
                ->select('*')
                ->where('refund_id', '=', $id)
                ->first();
    }

    public static function add_stock($id, $qty) {
        return DB::table('jocom_product_price')
                    ->where('id', '=', $id)
                    ->update(array('qty' => DB::raw('qty + '.$qty)));
                    
    }

    public static function get_trans_id($id) {
        return DB::table('logistic_transaction')
                    ->where('id', '=', $id)
                    ->pluck('transaction_id');
    }

    public static function get_buyer_id($id) {
        return DB::table('jocom_transaction')
                    ->where('id', '=', $id)
                    ->pluck('buyer_id');
    }

    public static function logistic_get_refund_products($id) {
        return DB::table('logistic_transaction as log_trans')
                    ->select('log_trans_item.product_id', 'log_trans_item.product_price_id', 'log_trans_item.qty_to_send')
                    ->leftjoin('logistic_transaction_item as log_trans_item', 'log_trans_item.logistic_id', '=', 'log_trans.id')
                    ->where('log_trans.id', '=', $id)
                    ->where('log_trans_item.qty_to_send', '>', '0')
                    ->get();
    }

    public static function logistic_get_trans_item($id, $product_id, $price_id) {
        return DB::table('jocom_transaction_details')
                    ->select('id','product_id', 'p_option_id', 'price', 'p_referral_fees', 'p_referral_fees_type', 'unit', 'gst_rate_item', 'disc', 'total')
                    ->where('transaction_id', '=', $id)
                    ->where('product_id', '=', $product_id)
                    ->where('p_option_id', '=', $price_id)
                    ->first();
    }

    public static function get_refund_history($id) {
        return DB::table('jocom_refund as refund')
                    ->select('refund.id', 'type.refund_type', 'type.amount', 'type.amount_type', 'refund.created_date')
                    ->leftJoin('jocom_refund_types as type', 'type.refund_id', '=', 'refund.id')
                    ->where('refund.trans_id', '=', $id)
                    ->whereNotNull('type.refund_type')
                    ->get();
    }
    
    public static function get_all_refunds($id) {
        return DB::table('jocom_refund')
                    ->where('trans_id', '=', $id)
                    ->get();
    }
}

?>