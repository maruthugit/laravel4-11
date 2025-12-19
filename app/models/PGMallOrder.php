<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class PGMallOrder extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $table = 'jocom_pgmall_order';

    public static function getBatch(){
    
        $result = DB::table('jocom_pgmall_order AS JPO')
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JPO.transaction_id')
                ->where("JPO.status","2")
                ->where("JPO.is_completed","1")
                ->where("JPO.transaction_id",">",0)
                ->where("JT.status","=",'completed')
                ->orderBy('JPO.created_at', 'asc')
                ->select('JPO.*')
                ->get();
        
        return $result;
        
	}
    

}
