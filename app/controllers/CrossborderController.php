<?php
use Helper\ImageHelper as Image;
class CrossborderController extends BaseController
{
    
    
    /*
     * @Desc    : Get campaign's product
     * @Param   : campaign_id
     * @Method  : POST
     * @Return  : JSON
     */
    public function getCampaignProduct(){
        
        
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            $campaign_id = Input::get('campaign_id');
            $Products = CrossborderProducts::getCampaignProduct(1);
            $data['products'] = $Products;
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        return json_encode($data['products']);
        
        
    }
    
    
    public function addCampaignProduct(){
        
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            
            DB::beginTransaction();
            
            $product_id = Input::get('product_id');
            $campaign_id = Input::get('campaign_id');
            
            $totalProduct = CrossborderProducts::getTotalCampaignProduct($campaign_id);
            // Check if the product already added
            
            $productInfo = CrossborderProducts::findProduct($product_id);

            if(count($productInfo) <= 0){
                
                $CrossborderProducts = new CrossborderProducts();
                $CrossborderProducts->product_id = $product_id;
                $CrossborderProducts->campaign_id = $campaign_id;
                $CrossborderProducts->order_position = $totalProduct + 1;
                $CrossborderProducts->created_by = Session::get('username');
                $CrossborderProducts->updated_by = Session::get('username');
                $CrossborderProducts->status = 1;
                $CrossborderProducts->save();
                
            }else{
                throw new Exception('Failed: Product already exist', 10);
            }
            
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        return json_encode($response);
        
    }
    /*
     * @Desc    : Remove product from campaign
     * @Param   : campaign product id
     */
    public function removeCampaignProduct(){
        
        
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            // Start transaction
            DB::beginTransaction();
            
            $product_campaign_id = Input::get('product_campaign_id');
            $Product = CrossborderProducts::find($product_campaign_id);
            
            if(count($Product) > 0){
                
                $Product->updated_by = Session::get('username');
                $Product->status = 0;
                $Product->save();
                
            }else{
                throw new Exception('Failed: Product not found', 10);
            }
            
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        return json_encode($response);
        
    }
    
    
    public function getAPICampaignProduct(){
        
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            // Start transaction
            DB::beginTransaction();
            
            $campaign_id = 1;
            $Product = CrossborderProducts::getAPICampaignProduct($campaign_id);
            
            if(count($Product) > 0){
                
                $ProductCollection = array();
                $PriceOptionCollection = array();
                
                foreach ($Product as $key => $value) {
                    
                    $delivery_zones = array();
                    $PriceOptionCollection = array();
                    
                    $DeliveryOption = Delivery::getZonesByProduct($value->ProductID);
     
                    foreach ($DeliveryOption as $keyZone => $valueZone) {
                        $line_delivery_zones = array(
                            "zone_id" => $valueZone->zone_id,
                            "zone_price" => number_format( $valueZone->price, 2, '.', ''),
                            "zone_name" => $valueZone->zone_name,
                        );
                        
                        array_push($delivery_zones, $line_delivery_zones);
                    }
                        
                    
                    $PriceOption = Price::getActivePrices($value->ProductID);
                    foreach ($PriceOption as $keyPrice => $valuePrice) {
                        
                        if($value->gst == 2){ // 0=Exempted, 1=ZeroRated, 2=Taxable //Inclusive Tax : Exclusive Amount * 106 / 100
                        $final_price = $valuePrice->price * ((100 + $value->gst_value) / 100);
                        $final_price_promo = $valuePrice->price_promo * ((100 + $value->gst_value) / 100);
                        
                        }else{
                            $final_price = $valuePrice->price ;
                            $final_price_promo = $valuePrice->price_promo ;
                        }
                        
                        $line_price_option = array(
                            "id" => $valuePrice->id,
                            "label" => $valuePrice->label, //number_format( $valueZone->price, 2, '.', ''),
                            "price" => number_format($final_price, 2, '.', ''),
                            "promo_price" => number_format($final_price_promo, 2, '.', ''),
                            "qty" => $valuePrice->qty,
                            "stock" => $valuePrice->stock,
                            "stock_unit" => $valuePrice->stock_unit,
                            "default" => $valuePrice->default,
                            "p_weight" => $valuePrice->p_weight,
                            "p_weight_unit" => "g",
                        );
                        
                        array_push($PriceOptionCollection, $line_price_option);
                    }
                    
                    
                    $lineProduct = array(
                        
                        "ProductID" => $value->ProductID,
                        "ProductName" => $value->name,
                        "ProductSKU" => $value->sku,
//                        "ProductActualPrice" => number_format( $value->price, 2, '.', ''),
//                        "ProductPromoPrice" => number_format( $value->price_promo, 2, '.', ''),
//                        "ProductActualPriceFinal" => number_format($final_price, 2, '.', ''),
//                        "ProductPromoPriceFinal" =>number_format( $final_price_promo, 2, '.', ''),
                        "ProductDescription" => $value->ProductDescription,
                        "delivery_time" => $value->delivery_time,
                        "delivery_zone" => $delivery_zones,
                        "ProductIMG1" => ( ! empty($value->img_1)) ? Image::link("images/data/{$value->img_1}") : '',
                        "ProductIMG2" => ( ! empty($value->img_2)) ? Image::link("images/data/{$value->img_2}") : '',
                        "ProductIMG3" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductThumb1" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductThumb2" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductThumb3" => ( ! empty($value->img_3)) ? Image::link("images/data/{$value->img_3}") : '',
                        "ProductQRCODE" => $value->qrcode,
                        "ProductWeight" => $value->weight,
                        "isGST" => $value->gst,
                        "ProductLabel" => $value->label,
                        "PriceOption" => $PriceOptionCollection,
                        "freshness" => $value->freshness,
                        "bulk" => "",
                        "halal" => empty($value->halal) ? '0' : $value->halal
                        
                    );
                    
                    array_push($ProductCollection, $lineProduct);
                    
                }
                
            }else{
                throw new Exception('Failed: Product not found', 10);
            }
            
            $data['dataProduct'] = $ProductCollection;
            
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        return json_encode($response);
        
    }
    
    public function getAPICampaignProductInfo(){
    
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            // Start transaction
            DB::beginTransaction();
            
            $campaign_id = 1;
            $productID = Input::get('product_id');
            $Product = CrossborderProducts::getCampaignProductInfo($campaign_id,$productID);

            if(count($Product) > 0){
                
                $ProductCollection = array(); 
                $PriceOptionCollection = array();
                
                    
                    $delivery_zones = array();
                    $PriceOptionCollection = array();
                    
                    $DeliveryOption = Delivery::getZonesByProduct($Product->ProductID);
     
                    foreach ($DeliveryOption as $keyZone => $valueZone) {
                        $line_delivery_zones = array(
                            "zone_id" => $valueZone->zone_id,
                            "zone_price" => number_format( $valueZone->price, 2, '.', ''),
                            "zone_name" => $valueZone->zone_name,
                        );
                        
                        array_push($delivery_zones, $line_delivery_zones);
                    }
                    
                    
                    $PriceOption = Price::getActivePrices($Product->ProductID);
                    foreach ($PriceOption as $keyPrice => $valuePrice) {
                        
                        if($Product->gst == 2){ // 0=Exempted, 1=ZeroRated, 2=Taxable //Inclusive Tax : Exclusive Amount * 106 / 100
                        $final_price = $valuePrice->price * ((100 + $Product->gst_value) / 100);
                        $final_price_promo = $valuePrice->price_promo * ((100 + $Product->gst_value) / 100);
                        
                        }else{
                            $final_price = $valuePrice->price ;
                            $final_price_promo = $valuePrice->price_promo ;
                        }
                        
                        $line_price_option = array(
                            "id" => $valuePrice->id,
                            "label" => $valuePrice->label, //number_format( $valueZone->price, 2, '.', ''),
                            "price" => number_format($final_price, 2, '.', ''),
                            "promo_price" => number_format($final_price_promo, 2, '.', ''),
                            "qty" => $valuePrice->qty,
                            "stock" => $valuePrice->stock,
                            "stock_unit" => $valuePrice->stock_unit,
                            "default" => $valuePrice->default,
                            "p_weight" => $valuePrice->p_weight,
                            "p_weight_unit" => "g",
                        );
                        
                        array_push($PriceOptionCollection, $line_price_option);
                    }
                  
                    
                    $ProductCollection = array(
                        
                        "ProductID" => $Product->ProductID,
                        "ProductName" => $Product->name,
                        "ProductSKU" => $Product->sku,
                        "ProductDescription" => $Product->ProductDescription,
                        "delivery_time" => $Product->delivery_time,
                        "delivery_zone" => $delivery_zones,
                        "ProductIMG1" => ( ! empty($Product->img_1)) ? Image::link("images/data/{$Product->img_1}") : '',
                        "ProductIMG2" => ( ! empty($Product->img_2)) ? Image::link("images/data/{$Product->img_2}") : '',
                        "ProductIMG3" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb1" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb2" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductThumb3" => ( ! empty($Product->img_3)) ? Image::link("images/data/{$Product->img_3}") : '',
                        "ProductQRCODE" => $Product->qrcode,
                        "ProductWeight" => $Product->weight,
                        "isGST" => $Product->gst,
                        "ProductLabel" => $Product->label,
                        "PriceOption" => $PriceOptionCollection,
                        "freshness" => $Product->freshness,
                        "bulk" => "",
                        "halal" => empty($Product->halal) ? '0' : $Product->halal
                        
                    );
                    
                    
                
            }else{
                throw new Exception('Failed: Product not found', 10);
            }
            
            $data['dataProduct'] = $ProductCollection;
            
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        return json_encode($response);
        
    }
    
    
    public function moveProductOrder(){
        
        $isError = 0;
        $RespStatus = 1;
        $message = "";
        $data = array();
        
        try{
            // Start transaction
            DB::beginTransaction();
            
            $product_campaign_id = Input::get('product_campaign_id');
            $action = Input::get('action'); // 1: move up : 2: move down
            $Product = CrossborderProducts::find($product_campaign_id);
            $ProductPosition = $Product->order_position;
            
            if($action == 1){
                
                $ProductAfter = CrossborderProducts::findProductOrderAfter($ProductPosition,$Product->campaign_id); 
                if(count($ProductAfter) > 0){
                    
                    $Product->updated_by = Session::get('username');
                    $Product->order_position = $ProductAfter->order_position;
                    $Product->save();    

                    $ProductAfter->updated_by = Session::get('username');
                    $ProductAfter->order_position = $ProductPosition;
                    $ProductAfter->save();
                
                }else{
                    //throw new Exception('Failed: Product not found', 10);
                }
            }elseif($action == 2){
                
                $ProductBefore = CrossborderProducts::findProductOrderBefore($ProductPosition,$Product->campaign_id); 
                if(count($ProductBefore) > 0){
                    
                    $Product->updated_by = Session::get('username');
                    $Product->order_position = $ProductBefore->order_position;
                    $Product->save();    

                    $ProductBefore->updated_by = Session::get('username');
                    $ProductBefore->order_position = $ProductPosition;
                    $ProductBefore->save();
                
                }else{
                    //throw new Exception('Failed: Product not found', 10);
                }
            }
            
        } catch (Exception $ex) {
            
            $isError = 1;
            $errorCode = $ex->getCode();
            $message = $ex->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("RespStatus"=> $RespStatus,"error"=> $isError,"errorCode"=> $errorCode,"message" => $message,"data"=> $data);
        return json_encode($response);
        
    }
        

    
    
        

}
