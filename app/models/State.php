<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class State extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public $timestamps = false;
	/**
	 * Table for state.
	 *
	 * @var string
	 */
	protected $table = 'jocom_country_states';

        public static function getCountryStatesRegion($country_id,$platform_code = 'JOC'){
            
            try{
                switch ($platform_code) {
                    case 'JOC':
                        $platform_column = 'JCS.region_id';

                        break;
                    case 'JUE':
                        $platform_column = 'JCS.juepin_region_id';

                        break;

                    default:
                        break;
                }
                
                $regions =  DB::table('jocom_country_states AS JCS')
                   // ->leftJoin('jocom_region AS JR', 'JR.id', '=', $platform_column) // Uncomment for JUEPIN 
                    ->leftJoin('jocom_region AS JR', 'JR.id', '=', 'JCS.region_id')
                    ->select('JCS.*','JR.region','JR.region_code','JR.id AS RegionID')    
                ->where('JCS.country_id',$country_id)
                ->get();
            
                return $regions;
            
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        
        }
        
        
        public static function getStateByRegion($region_id){
            
            return DB::table('jocom_country_states AS JCS')
                ->select('JCS.*')    
                ->where('JCS.region_id',$region_id)
                ->get();
            
        }
        
        public static function getStateByCountry($country_id){
            return DB::table('jocom_country_states AS JCS')
                ->select('JCS.*')    
                ->where('JCS.country_id',$country_id)
                ->get();
        }

	
}
