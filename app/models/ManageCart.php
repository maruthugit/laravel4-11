<?php

class ManageCart extends Eloquent
{

	protected $table = 'jocom_managecart_items';
	
	public static function rnd_number(){

        $random_id_length = 10; 
        //generate a random id and store it in $rnd_id 
        //$rnd_id = crypt(uniqid(rand(),1)); 
        $rnd_id = uniqid(rand(),1); 
        //to remove any slashes that might have come 
        $rnd_id = strip_tags(stripslashes($rnd_id)); 
        //Removing any . or / and reversing the string 
        $rnd_id = str_replace(".","",$rnd_id); 
        $rnd_id = strrev(str_replace("/","",$rnd_id)); 
        //finally I take the first 10 characters from the $rnd_id 
        $rnd_id = substr($rnd_id,0,$random_id_length); 
        
        return $rnd_id;

    }
    
    public static function session_id(){
        $rndID              = self::rnd_number();
        $gmtdatestring      = $rndID . gmdate("D, d M Y H:i:s", time() + 3600 * ($timezone + date("I")));
        $cartsession_id     = md5($gmtdatestring);
        return $cartsession_id;
    }

}