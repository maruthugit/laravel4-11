<?php


class WarehouseProductLog extends Eloquent {

	
    protected $table = 'jocom_warehouse_product_log';
    
    
    
    public static function saveRecord($warehouse_id,$action_type,$quantity,$reference_id = ''){
        
        try{
            
        
        $new_balance = 0;
        $warehouse = Warehouse::find($warehouse_id);
        
        $product_id = $warehouse->product_id;
        
        switch ($action_type) {
            
            case 'STOCKIN':
                $new_balance =  $warehouse->stockin_hand + $quantity;
                $operator =  '+';
                break;
            case 'STOCKOUT':
                $new_balance =  $warehouse->stockin_hand - $quantity;
                $operator =  '-';
                break;
            case 'STOCKDEDUCT':
                $new_balance =  $warehouse->stockin_hand - $quantity;
                 $operator =  '-';
                break;
            case 'STOCKADJUST':
                $new_balance =  $quantity;
                $operator =  '=';
                break;
            case 'NEWSTOCK':
                $new_balance =  0;
                $quantity =  0;
                $operator =  '=';
                break;
    
        }
        
        
        $WarehouseProductHistory = new WarehouseProductLog;
        $WarehouseProductHistory->product_warehouse_id = $warehouse_id;
        $WarehouseProductHistory->product_id = $product_id;
        $WarehouseProductHistory->action = $action_type;
        $WarehouseProductHistory->operator = $operator;
        $WarehouseProductHistory->previous_balance = $warehouse->stockin_hand;
        $WarehouseProductHistory->previous_balance_reserved = $warehouse->reserved_in_hand;
        $WarehouseProductHistory->action_type = '';
        $WarehouseProductHistory->quantity = $quantity;
        $WarehouseProductHistory->new_balance = $new_balance ;
        $WarehouseProductHistory->reference_id = $reference_id ;
        $WarehouseProductHistory->doer = Session::get('username');
        $WarehouseProductHistory->save();
        
        }catch (exception $ex){
            echo $ex->getMessage();
        }
        
        
        
    }

	
}
