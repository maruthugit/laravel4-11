<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class PurchaseHistory extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $table = 'jocom_transaction_purchase_history';
    
    public $timestamps = false;
}
