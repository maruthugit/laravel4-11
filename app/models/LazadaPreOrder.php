<?php

class LazadaPreOrder extends Eloquent
{
    protected $table = 'jocom_lazada_pre_order';
    
    public static function findLastByType($listType){
        
        $result = DB::table('jocom_lazada_pre_order')
                ->orderBy('id', 'desc')
                ->where('migrate_from',$listType)
                ->first();
        return $result;
        
    }
    
    public static function findByOrderNumber($orderNumber){
        
        $result = DB::table('jocom_lazada_pre_order')
                ->where('order_number', $orderNumber)
                ->where('activation', 1)
                ->first();
        return $result;
        
    }
    
    public static function getBatch(){
    
        $result = DB::table('jocom_lazada_pre_order AS JLO')
                ->leftJoin('jocom_transaction AS JT', 'JT.id', '=', 'JLO.transaction_id')
                ->where("JLO.status","2")
                ->where("JLO.is_completed","0")
                ->where("JLO.transaction_id",">",0)
                ->where("JT.status","=",'completed')
                ->orderBy('JLO.created_at', 'asc')
                ->select('JLO.*')
                ->take(100)
                ->get();
                
                return $result;
    }
    
    public static function specialForce($orderNumber){
        
        $result = DB::table('jocom_lazada_pre_order')
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
  
  
}
