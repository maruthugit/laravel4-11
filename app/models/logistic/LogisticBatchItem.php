<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class LogisticBatchItem extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
    
	protected $table = 'logistic_batch_item';
	

    // public static function insert_trans($trans)
    // {
    //     $new = array();
    //     $new['transaction_id'] = $trans->id;
    //     $new['transaction_date'] = $trans->transaction_date;
    //     $new['delivery_name'] = $trans->delivery_name;
    //     $new['delivery_contact_no'] = $trans->delivery_contact_no;
    //     $new['buyer_email'] = $trans->buyer_email;
    //     $new['delivery_addr_1'] = $trans->delivery_addr_1;
    //     $new['delivery_addr_2'] = $trans->delivery_addr_2;
    //     // $new['delivery_city'] = '';
    //     $new['delivery_postcode'] = $trans->delivery_postcode;
    //     $new['delivery_state'] = $trans->delivery_state;
    //     $new['delivery_country'] = $trans->delivery_country;
    //     $new['special_msg'] = $trans->special_msg;
    //     $new['do_no'] = $trans->do_no;
    //     // $new['remark'] = '';
    //     $new['status'] = 0;
    //     $new['insert_by'] = Session::get('username');
    //     $new['insert_date'] = date('Y-m-d H:i:s');
    //     $new['modify_by'] = '';
    //     $new['modify_date'] = '';

    //     $insert_id = LogisticTransaction::insertGetId($new);

    //     return $insert_id;        
    // }
	
}
