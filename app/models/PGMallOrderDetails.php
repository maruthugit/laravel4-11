<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class PGMallOrderDetails extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $table = 'jocom_pgmall_order_details';

    public static function getByOrderID($order_id){
        
        $result = PGMallOrderDetails::where('order_id', '=', $order_id)->get();
        return $result;
        
    }

    
}
