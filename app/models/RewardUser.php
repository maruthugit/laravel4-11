<?php

class RewardUser extends Eloquent  {

	
	protected $table = 'jocom_reward_users';
        
        
    public static function scopeCampaign($query , $schemeCode){
       
        return $query->where('reward_scheme', $schemeCode)
        ->where('start_date','<=',date("Y-m-d H:i:s"))
        ->where('end_date','>=',date("Y-m-d H:i:s"))
        ->where('activation',1)
        ->where('balance','>',0);

    }

	
}
