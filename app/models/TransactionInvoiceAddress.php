<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;


class TransactionInvoiceAddress extends Eloquent  {

    protected $table = 'jocom_transaction_invoice_address';
    
    public static function saveAddress($transaction_id , $address_type = 1){
       
        if($address_type == 2){

            $transaction = Transaction::find($transaction_id);
            $buyer_username = $transaction->buyer_username;

            $Customer =  Customer::where("username",$buyer_username)->first();
            $State = State::where("name",$Customer->state)->first();
            $City = City::where("name",$Customer->city)->first();

            $address_1 = $Customer->address1;
            $address_2 = $Customer->address2;
            $address_postcode = $Customer->postcode;
            $address_city = $Customer->city;
            $address_city_id = $City->id;
            $address_state= $Customer->state;
            $address_state_id = $State->id;
            $address_country = $Customer->country;

        }else{

            $transaction = Transaction::find($transaction_id);

            $address_1 = $transaction->delivery_addr_1;
            $address_2 = $transaction->delivery_addr_2;
            $address_postcode = $transaction->delivery_postcode;
            $address_city = $transaction->delivery_city;
            $address_city_id = $transaction->delivery_city_id;
            $address_state = $transaction->delivery_state;
            $address_state_id = $transaction->delivery_state_id;
            $address_country = $transaction->delivery_country;

        }

        $trans = new TransactionInvoiceAddress;
        $trans->transaction_id = $transaction_id;
        $trans->invoice_address_1 = $address_1;
        $trans->invoice_address_2 = $address_2;
        $trans->invoice_postcode = $address_postcode;
        $trans->invoice_city = $address_city;
        $trans->invoice_city_id = $address_city_id;
        $trans->invoice_state = $address_state;
        $trans->invoice_state_id = $address_state_id;
        $trans->invoice_country = $address_country;
      
        $trans->save();



    }

	
}
