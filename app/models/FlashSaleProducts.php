<?php

class FlashSaleProducts extends Eloquent
{
    protected $table = 'jocom_flashsale_products';
    
    
    public static function getTotalCampaignProduct($campaign_id){
        return DB::table('jocom_flashsale_products')
        ->where('campaign_id', $campaign_id)
        ->count();
    }


    public function scopeStoreFlashProducts($lid, $lbl, $actualprice, $promoprice, $productqty, $limitqty, $productid, $seq, $id) {
        $id2 = DB::table('jocom_flashsale_products')->insertGetId([
            'fid'=>$id,
            'label_id'=>$lid,
            'label'=>$lbl,
            'actual_price'=>$actualprice,
            'promo_price'=>$promoprice,
            'qty'=>0,
            'limit_quantity' => $productqty,
            'max_qty' => $limitqty,
            'product_id'=>$productid,
            'seq'=>$seq,
            'activation'=>1,
            'created_at'  => date('Y-m-d h:i:s'),
            'created_by'  => Session::get('username'),
        ]);

        return DB::table('jocom_flashsale_stock')->insert([
            'stock'=>$productqty, 
            'fpid'=>$id2, 
            'modify_by'=>Session::get('username'),
            'modify_date'=>date('Y-m-d H:i:s')
        ]);
    }

    public function scopeUpdateFlashProducts($sku,$price,$qty,$id) {

        $modify_by = Session::get('username');
        $modify_date = date('Y-m-d H:i:s');

        return DB::table('jocom_flashsale_products')
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
