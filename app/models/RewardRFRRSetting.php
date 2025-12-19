<?php

class RewardRFRRSetting extends Eloquent
{
    
    protected $table = 'jocom_reward_referrer_setting';
    
   	public static function generateCode(){

            //To Pull 6 Unique Random Values Out Of AlphaNumeric
            //Total: keys = 32, elements = 33
            $characters = array(
            "A","B","C","D","E","F","G","H","J","K","L","M",
            "N","P","Q","R","S","T","U","V","W","X","Y","Z",
            "1","2","3","4","5","6","7","8","9");

            $keys = array();

            //first count of $keys is empty so "1", remaining count is 1-7 = total 8 times
            while(count($keys) < 6) {
                $x = mt_rand(0, count($characters)-1);
                if(!in_array($x, $keys)) {
                   $keys[] = $x;
                }
            }

            foreach($keys as $key){
               $random_chars .= $characters[$key];
            }  

            return $random_chars;  
    }


    public static function referrerReward($transid){

    	$referrerpoint = 0; 
    	$response = 0;

    	$trans = Transaction::find($transid);			

    	$userid   = $trans->buyer_id;
    	$username = $trans->buyer_username; 


    	$RewardRFRRSetting = RewardRFRRSetting::where("reward_code_type","RFRR")->first();

    	if(count($RewardRFRRSetting)>0){
    		$referrerpoint = $RewardRFRRSetting->point;

    	}


    	$trackingresult = RewardRFRRTracking::where('referrer_to',$userid)
    						  ->where('referrer_from','<>',$userid)
    						  ->where('is_completed','=',0)
    						  ->first();

    	if(count($trackingresult)>0){

    			$trackingid = $trackingresult->id; 


    			$track = RewardRFRRTracking::find($trackingid);
    			$track->transaction_id = $transid; 
    			$track->is_completed = 1; 
    			$track->save();

    			$pointuser = PointUser::where('user_id',$userid)
    									->where('point_type_id',1)
    									->first(); 
    			$pointuser->point += $referrerpoint;
    			$pointuser->save();

    			$response = 1; 

    	}					  

    	return $response;

    }
    

}

?>