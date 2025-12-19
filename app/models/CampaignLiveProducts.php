<?php

class CampaignLiveProducts extends Eloquent
{
    protected $table = 'jocom_livestreaming_products';
    
    
    public static function getTotalCampaignProduct($campaign_id){
        
        return DB::table('jocom_livestreaming_products')
                   ->where('campaign_id', $campaign_id)
                   ->count();
        
    }
    
//    public static function getCampaignProduct($campaign_id){
//        
//        return DB::table('jocom_campaign_products AS JCP')
//                    ->select('JCP.*', 'JP.sku','JP.name','JP.id AS ProductID')
//                    ->join('jocom_products AS JP', 'JP.id', '=', 'JCP.product_id')
//                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
//                    ->where('JCP.campaign_id', $campaign_id)
//                    ->where('JCP.status', 1)
//                    ->orderBy('JCP.order_position', 'DESC')
//                    ->get();
//        
//    }
    
    
    public static function getCampaignProduct($campaign_id){
        
        return DB::table('jocom_livestreaming_products AS JCP')
                    ->select('JCP.*','JCP.id AS productCampaignID', 
                            'JP.id AS ProductID','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value',
                            'JPP.*')
                    ->join('jocom_products AS JP', 'JP.id', '=', 'JCP.product_id')
                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->where('JCP.campaign_id', $campaign_id)
                    ->where('JP.status', 1)
                    ->where('JCP.status', 1)
                    ->where('JPP.default', 1)
                    ->orderBy('JCP.order_position')
                    ->get();
        
    }
    
    public static function getAPICampaignProduct($campaign_id){
        
        return DB::table('jocom_livestreaming_products AS JCP')
                    ->select('JCP.*','JCP.id AS productCampaignID', 
                            'JP.id AS ProductID','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                            'JPP.*')
                    ->join('jocom_products AS JP', 'JP.id', '=', 'JCP.product_id')
                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->where('JCP.campaign_id', $campaign_id)
                    ->where('JCP.status', 1)
                    ->where('JP.status', 1)
                    ->where('JPP.default', 1)
                    ->orderBy('JCP.order_position')
                    ->get();
        
    }
    
    public static function getCampaignProductInfo($campaign_id,$productID){
    
        return DB::table('jocom_livestreaming_products AS JCP')
                    ->select('JCP.*','JCP.id AS productCampaignID', 
                            'JP.id AS ProductID','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                            'JPP.*')
                    ->join('jocom_products AS JP', 'JP.id', '=', 'JCP.product_id')
                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->where('JCP.campaign_id', $campaign_id)
                    ->where('JCP.status', 1)
                    ->where('JP.status', 1)
                    ->where('JP.id', $productID)
                    ->where('JPP.default', 1)
                    ->orderBy('JCP.order_position', 'DESC')
                    ->first();
        
    }
    
    
    public static function findProduct($product_id,$status = 1){
        
        return DB::table('jocom_livestreaming_products')
                    ->where('product_id', $product_id)
                    ->where('status', $status)
                    ->first();
        
    }
    
    public static function findProductOrderAfter($position_product_id,$campaign_id,$status = 1){
        
        return CampaignProducts::where('order_position',">", $position_product_id)
                    ->where('status', $status)
                    ->where('campaign_id', $campaign_id)
                    ->orderBy('order_position', 'asc')
                    ->first();
        
    }
    
    public static function findProductOrderBefore($position_product_id,$campaign_id,$status = 1){
        
        return CampaignProducts::where('order_position',"<", $position_product_id)
                    ->where('status', $status)
                    ->where('campaign_id', $campaign_id)
                    ->orderBy('order_position', 'desc')
                    ->first();
        
    }
    
    public static function swapOrderPosition($id, $position)
    {
        $product = self::find($id);
        $another_product = self::where('order_position', $position)->first();

        $another_product->updated_by = Session::get('username');
        $another_product->order_position = $product->order_position;
        $another_product->save();

        $product->updated_by = Session::get('username');
        $product->order_position = $position;
        $product->save();
        
    }
    

  
}
