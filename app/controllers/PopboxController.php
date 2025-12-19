<?php 

class PopboxController extends BaseController {

    
    public function getLockerLocation() {

        $data = array();
        $RespStatus = 1; 
        $message = "";
        $errorCode = "";
        $is_error = false;
        $error_line = "";

        try {
            
            $country = 'Malaysia';
            $province = 'Malaysia';
            $city = 'Malaysia';
            $zip_code = Input::get('');
            
            $endPoint = 'locker/location';
            $param = array(
               // "token"=> '76ae16139e8b3f0dc4b3d6409f3b2b3967b450ce',
                "country"=> 'Malaysia'
              //  "province"=> 'Selangor',
               // "city"=> 'Subang Jaya',
              //  "zip_code"=> '47500',
            );
            $result = self::PopBoxApiCaller($endPoint,$param);
            $data = json_decode($result);
            
        } catch (Exception $ex) {
                    } finally {
            if ($is_error) {
                DB::rollback();
            } else {
                DB::commit();
            }
        }

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }
    
    
    public static function savePopBox($transaction_id,$popboxlocker,$popbox_address){
        
        $popboxSave = 1;
        
        try{
            
        
        $PopboxOrder = new PopboxOrder();
        $PopboxOrder->popbox_locker = $popboxlocker;
        $PopboxOrder->transaction_id = $transaction_id;
        $PopboxOrder->popbox_address = $popbox_address;
        $PopboxOrder->save();

        } catch (Exception $ex) {
            $popboxSave = 1;
        }  finally {
            return $popboxSave;
        }

        
    }
    
    
    public static function sendPopBoxOrder($transaction_id){
        
        $Transaction = Transaction::find($transaction_id);

        $PopboxOrder = PopboxOrder::where("transaction_id",$transaction_id)->first();
        
        $endPoint = 'merchant/pickup';

        $param = array(
               // "token"=> '76ae16139e8b3f0dc4b3d6409f3b2b3967b450ce',
                "order_no"=> "JCM".$Transaction->id,
                "phone"=> $Transaction->delivery_contact_no,
                "popbox_location"=> $PopboxOrder->popbox_locker,
                "pickup_location"=> 'Jocom Mshopping Sdn Bhd',
                "customer_email"=> $Transaction->buyer_email,
                "detail_items"=> '',
                "price"=> '',
            );
        $result = self::PopBoxApiCaller($endPoint,$param);
        $data = json_decode($result, true);      
        $PopboxOrderData = PopboxOrder::where("transaction_id",$transaction_id)->first();
        if($data['response']['code']=='200'){
            $PopboxOrderData->is_submited = 1;
            $PopboxOrderData->popbox_tracking_number = "JCM".$Transaction->id;
            $PopboxOrderData->data_sent = json_encode($param);
            $PopboxOrderData->api_return = json_encode($data);
            $PopboxOrderData->popbox_order_id = $data['data'];
            $PopboxOrderData->save();
        }
        
        return $data;
        
        
    }
    
    public static function PopBoxApiCaller($endPoint,$param){
        
        
        if (Config::get('constants.ENVIRONMENT') == 'live') {
            $popbox_env = Config::get('constants.POPBOX_PRODUCTION_ENV');
            $popbox_api_key = Config::get('constants.POPBOX_PRODUCTION_API_KEY');
        }else{
            $popbox_env = Config::get('constants.POPBOX_DEVELOPMENT_ENV');
            $popbox_api_key = Config::get('constants.POPBOX_DEVELOPMENT_API_KEY');
        }
        
        $param['token'] = $popbox_api_key;

        $data_string = json_encode($param);
        $ch = curl_init($popbox_env.$endPoint);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 300);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 300);

        //execute post
        $resultPopBox = curl_exec($ch);
        
        return $resultPopBox;
       
    }

}
?>