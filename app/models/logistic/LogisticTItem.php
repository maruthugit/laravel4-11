<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class LogisticTItem extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;

    protected $table = 'logistic_transaction_item';

    

    public static function insert_transDetail($transDetail, $transID)
    {
        $package = array();
        $new = array();

        foreach ($transDetail as $transD)
        {            
            $new['logistic_id'] = $transID;

            if($transD->product_group == '')
            {
                // normal product
                $product = Product::find($transD->product_id);

                if(isset($transD->delivery_time))
                {
                    $delivery = LogisticTItem::get_delivery_int($transD->delivery_time);
                }
                else
                    $delivery = 0;

                $new['product_id'] = $transD->product_id;
                $new['product_price_id'] = $transD->p_option_id;     
                $new['transaction_item_id'] = (isset($transD->id)?$transD->id:'');
                $new['sku'] = $transD->sku;
                $new['name'] = (isset($product->name)?$product->name:'');
                $new['label'] = $transD->price_label;
                $new['delivery_time'] = $delivery;
                $new['qty_order'] = $transD->unit;
                $new['qty_to_assign'] = $transD->unit;
                $new['qty_to_send'] = $transD->unit;
                $new['sp_group_id'] = $transD->sp_group_id;
            }
            else
            {
                // package
                if (!in_array($transD->product_group, $package))                
                {
                    $product = Package::where('sku', '=', $transD->product_group)->first();
                    $unit = TDetailsGroup::select('unit')->where('transaction_id', '=', $transD->transaction_id)->where('sku', '=', $transD->product_group)->first();
                    
                    if(isset($product->delivery_time))
                    {
                        $delivery = LogisticTItem::get_delivery_int($product->delivery_time);
                    }
                    else
                        $delivery = 0;

                    $new['product_id'] = (isset($product->id)?$product->id:'');
                    $new['sku'] = $transD->product_group;
                    $new['name'] = (isset($product->name)?$product->name:'');
                    $new['delivery_time'] = $delivery;
                    $new['qty_order'] = $unit->unit;
                    $new['qty_to_assign'] = $unit->unit;
                    $new['qty_to_send'] = $unit->unit;

                    $package[] = $transD->product_group;
                }
                else
                {
                    continue;
                }               
            }    

            $insert_id = LogisticTItem::insertGetId($new); 
            
            // deduct actual stock
            // $base_items = DB::table('jocom_product_base_item AS bi')
            //                 ->where("bi.product_id", $transD->product_id)
            //                 ->where("bi.price_option_id", $transD->p_option_id)
            //                 ->where("bi.status", 1)
            //                 ->select('bi.product_base_id', 'bi.quantity')
            //                 ->get();

            // if (count($base_items) > 0) {
            //     foreach ($base_items as $base_item) {
            //         ProductController::manageActualstock($base_item->product_base_id, $transD->unit * $base_item->quantity, 'decrease');
            //     }
            // } else {
            //     ProductController::manageActualstock($transD->product_id, $transD->unit, 'decrease');
            // }
            
            // ProductController::log_actualstockout($transD->product_id, $transD->p_option_id, $transD->unit);
        }        

        return $insert_id;        
    }

    public static function get_delivery_int($date = "")
    {
        $value = "";

        switch ($date)
        {
            case '24 hours':
                $value = 0;
                break;
            case '1-2 business days':
                $value = 1;
                break;
            case '2-3 business days':
                $value = 2;
                break;
            case '3-7 business days':
                $value = 3;
                break;
            case '14 business days':
                $value = 4;
                break;
            default:
                $value = 0;
                break;
        }

        return $value;
    }

    public static function get_delivery($urgent = "")
    {
        $value = "";

        switch ($urgent)
        {
            case '0':
                $value = '24 hours';
                break;
            case '1':
                $value = '1-2 business days';
                break;
            case '2':
                $value = '2-3 business days';
                break;
            case '3':
                $value = '3-7 business days';
                break;
            case '4':
                $value = '14 business days';
                break;
            default:
                $value = '24 hours';
                break;
        }

        return $value;
    }

    public static function api_delivery_time()
    {
        $data['delivery_time'] = array('24 hours', '1-2 business days', '2-3 business days', '3-7 business days', '14 business days');
        return $data;
    }




    
    
}
