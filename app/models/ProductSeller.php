<?php

class ProductSeller extends Eloquent
{
    protected $table = 'jocom_product_seller';
    
    public static function getProductSeller($productID){
        
        return DB::table('jocom_product_seller AS JPS')
            ->leftJoin('jocom_seller AS JS', 'JS.id', '=', 'JPS.seller_id')
            ->leftJoin('jocom_country_states AS JCS', 'JCS.id', '=', 'JS.state')
            ->select('JPS.*','JS.company_name','JCS.name AS StateName','JCS.region_id')
            ->where("JPS.product_id",$productID)
            ->where("JPS.activation",1)    
            ->get();
        
    }
    

}
