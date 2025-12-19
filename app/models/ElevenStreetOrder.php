<?php

class ElevenStreetOrder extends Eloquent
{
    protected $table = 'jocom_elevenstreet_order';
    
    public static function findLastByType($listType){
        
        $result = DB::table('jocom_elevenstreet_order')
                ->orderBy('id', 'desc')
                ->where('migrate_from',$listType)
                ->first();
        return $result;
        
    }
  
    public static function findByOrderNumber($orderNumber){
    
        $result = DB::table('jocom_elevenstreet_order')
                ->where('order_number', $orderNumber)
                ->where('activation', 1)
                ->first();
        return $result;
    
    }


    public static function specialForce($orderNumber){
        
        $result = DB::table('jocom_elevenstreet_order')
                ->where('order_number', $orderNumber)
                ->where('activation', 1)
                ->first();
        return $result;
        
    }
    
    public static function getState()
    {
        return DB::table('state')
                 ->orderBy('state.state_code')
                 ->get();
    }

    public static function getPostcode()
    {
        return DB::table('postcode')
                 ->orderBy('postcode.state_code')
                 ->get();
    }
  
    
    public static function getBatch(){
    
        $result = DB::table('jocom_elevenstreet_order AS JEO')
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JEO.transaction_id')
                ->where("JEO.status","2")
                ->where("JEO.is_completed","1")
                ->where("JEO.transaction_id",">",0)
                ->where("JT.status","=",'completed')
                ->orderBy('JEO.created_at', 'asc')
                ->select('JEO.*')
                ->take(100)
                ->get();
        
//        $result = ElevenStreetOrder::where("status","2")
//                ->leftJoin('jocom_transaction', 'users.id', '=', 'posts.user_id')
//                ->where("is_completed","1")
//                ->where("transaction_id",">",0)
//                ->orderBy('created_at', 'asc')
//                ->take(3)
//                ->get();
        
        return $result;
        
    }
  
    
    
}