<?php

class GiftController extends \BaseController {
	
    public function getactivepromo(){
        
        $promo = DB::table('jocom_foc_reward AS JFR')
            ->where("JFR.activation",1)
            ->first();
        
        return $promo;
        
    }
    
    /*
     * 
     */
    public function getreward(){
        
        try{
        
        $promo = DB::table('jocom_foc_reward AS JFR')
            ->select("JFR.*","JP.qrcode","JPP.id AS p_option_id","JPP.label","JPP.price","JPP.price_promo","JPP.p_weight","JPP.p_referral_fees_type","JS.username as seller_username")
            ->leftjoin('jocom_products AS JP', 'JP.id', '=', 'JFR.product_id')
            ->leftjoin('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JFR.product_id')
            ->leftjoin('jocom_seller AS JS', 'JS.id', '=', 'JFR.seller_id')
            ->where("JFR.activation",1)
            ->where("JPP.status",1)
            ->where("JPP.default",1)
            ->where("JFR.type",'FOC')
            ->where("JFR.start_date","<=",DATE("Y-m-d h:i:s"))
            ->where("JFR.end_date",">=",DATE("Y-m-d h:i:s"))
            ->get();
        
        return $promo;

        }catch(exception $ex){

            echo $ex->getMessage();
      
        }
        
    }

    public function validaterules(){

        return true;

    }

}
