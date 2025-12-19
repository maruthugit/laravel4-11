<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class QootenOrder extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $table = 'jocom_qoo10_order';
    

    public static function getBatch(){
    
        $result = DB::table('jocom_qoo10_order AS JQO')
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JQO.transaction_id')
                ->where("JQO.status","2")
                ->where("JQO.is_completed","1")
                ->where("JQO.transaction_id",">",0)
                ->where("JT.status","=",'completed')
                ->orderBy('JQO.created_at', 'asc')
                ->select('JQO.*')
                ->get();
        
        return $result;
        
	}

}
