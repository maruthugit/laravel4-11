<?php

class CourierOrder extends Eloquent
{
    protected $table = 'jocom_courier_orders';
    
    public static function getOrderInfo($courier_order_id){
        
        $result = DB::table('jocom_courier_orders AS JCO')
                ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JCO.transaction_item_logistic_id')
                ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LTI.logistic_id')
                ->where('JCO.id', $courier_order_id)
                ->first();
        
        return $result;
        
    }

    public static function getCourierOrders($courier_id){
        
        $result = DB::table('jocom_courier_orders AS JCO')
                ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JCO.transaction_item_logistic_id')
                ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LTI.logistic_id')
                ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JCO.batch_id')
                ->where('JCO.courier_id', $courier_id)
                ->whereNotIn('LB.status', [5,4])
                ->whereIn('LT.status', [4, 2, 1,0])
                //->where('LT.id', 34790)
                ->get();
        
        return $result;
        
    }
    
    // public static function getCourierOrders($courier_id){
        
    //     $result = DB::table('jocom_courier_orders AS JCO')
    //             ->leftJoin('logistic_transaction_item AS LTI', 'LTI.id', '=', 'JCO.transaction_item_logistic_id')
    //             ->leftJoin('logistic_transaction AS LT', 'LT.id', '=', 'LTI.logistic_id')
    //             ->leftJoin('logistic_batch AS LB', 'LB.id', '=', 'JCO.batch_id')
    //             ->where('JCO.courier_id', $courier_id)
    //             ->whereNotIn('LB.status', [5,4])
    //             ->whereIn('LT.status', [4, 2, 1,0])
    //             ->where('LT.transaction_id', 58927)
    //             //->where('LT.id', 34790)
    //             ->get();
        
    //     return $result;
    //     //58919, 58927
    // }
    
    
    public static function findByTrackingNo($trackingNo){
        
        $result = DB::table('jocom_courier_orders AS JCO')
                ->where('JCO.tracking_no', $trackingNo)
                ->first();
        
        return $result;
        
    }

  
}
