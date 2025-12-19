<?php

class BoostApiTokenResponse extends Eloquent
{
    protected $table = 'jocom_boost_apitoken_response';




    public static function getBoostAPIResponse($onlinerefno){


    	$BoostResponse = BoostApiTokenResponse::where('onlinerefnum',$onlinerefno)
    						->first();

    	return $BoostResponse;


    }




}





?>