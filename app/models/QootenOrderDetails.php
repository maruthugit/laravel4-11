<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class QootenOrderDetails extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $table = 'jocom_qoo10_order_details';

    public static function getByOrderID($order_id){

        $result = QootenOrderDetails::where('order_id', '=', $order_id)->get();

        return $result;
        
    }
    
}
