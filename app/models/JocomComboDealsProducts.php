<?php

class JocomComboDealsProducts extends Eloquent
{
    protected $table = 'jocom_combodeals_products';
    
    
    public static function getTotalCampaignProduct($campaign_id){
        
        return DB::table('jocom_combodeals_products')
                   ->where('campaign_id', $campaign_id)
                   ->count();
        
    }

    public function scopeStoreProducts($lid,$lbl,$actualprice,$promoprice,$productqty,$limitqty,$productid,$seq,$id) {

        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d h:i:s');
        // print_r($actualprice);die();
        $id2 = DB::table('jocom_combodeals_products')
                ->insertGetId(array(
                    'fid'=>$id,
                    'label_id'=>$lid,
                    'label'=>$lbl,
                    'actual_price'=>$actualprice,
                    'promo_price'=>$promoprice,
                    'qty'=>$productqty,//added by boobalan
                    'limit_quantity'=>$limitqty, //added by boobalan
                    'product_id'=>$productid,
                    'seq'=>$seq,
                    'activation'=>1,
                    'created_at'  => date('Y-m-d h:i:s'),
                    'created_by'  => Session::get('username'),
                    )
                );  

       return DB::table('jocom_combodeals_stock')->insert(array(
                'stock'=>$productqty, 
                'fpid'=>$id2, 
                'modify_by'=>Session::get('username'),
                'modify_date'=>date('Y-m-d H:i:s')
            ));
       
    }

    public function scopeUpdateFlashProducts($sku,$price,$qty,$id) {

        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocom_combodeals_products')
            ->where('id','=',$id)
                ->update(array(
                    'product_sku'=>$sku,
                    'product_price'=>$price,
                    'product_quantity'=>$qty,
                    'updated_at'  => date('Y-m-d H:i:s'),
                    'updated_by'  => Session::get('username'),
                    )
                );  
       
    }

  
}
