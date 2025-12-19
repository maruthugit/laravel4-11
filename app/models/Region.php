<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class Region extends Eloquent  {

	
	protected $table = 'jocom_region';
        
        
        public static function getRegionInfo($region_id){
            //->leftJoin('jocom_countries', 'jocom_countries.id', '=', 'jocom_region.country_id')
            $result = DB::table('jocom_region AS JR')
                ->leftJoin('jocom_countries AS JC', 'JC.id', '=', 'JR.country_id') 
                ->select('JR.*', 'JC.name')        
                ->where('JR.id', $region_id)
                ->first();
            
        
            return $result;
            
        }

	
}
