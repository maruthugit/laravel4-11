<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ShopeeOrder extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    protected $table = 'jocom_shopee_order';

    public static function getBatch(){
    
        $result = DB::table('jocom_shopee_order AS JSO')
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JSO.transaction_id')
                ->where("JSO.status","2")
                ->where("JSO.is_completed","1")
                ->where("JSO.transaction_id",">",0)
                ->where("JT.status","=",'completed')
                ->orderBy('JSO.created_at', 'asc')
                ->select('JSO.*')
                ->get();
        
        return $result;
        
	}
	
	public static function getBatchlist(){
    
        $TIDCollection = array(539383,
539384,
539385,
539386,
539387,
539388,
539389,
539390,
539391,
539392,
539393,
539394,
539395,
539396,
539397,
539398,
539399,
539400,
539401,
539402,
539403,
539404,
539405,
539406,
539407,
539408,
539409,
539410,
539411,
539412,
539413,
539414,
539415,
539416,
539417,
539418,
539419,
539420,
539421,
539422,
539423,
539424,
539425,
539426,
539427,
539428,
539429,
539430,
539431,
539432,
539433,
539434,
539435,
539436,
539437,
539438,
539439,
539440,
539441,
539442,
539443,
539444,
539445,
539446,
539447,
539448,
539449,
539450,
539451,
539452,
539453




            );
        
        $result = DB::table('jocom_shopee_order AS JSO')
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JSO.transaction_id')
                // ->where("JSO.status","2")
                // ->where("JSO.is_completed","1")
                ->whereIn('JSO.transaction_id', $TIDCollection)
                ->where("JSO.transaction_id",">",0)
                ->where("JT.status","=",'pending')
                ->orderBy('JSO.created_at', 'asc')
                ->select('JSO.*')
                ->get();
        
        return $result;
        
	}
    

}
