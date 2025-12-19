<?php

class DynamicSale  extends Eloquent
{
    protected $table = 'jocom_dynamic_sale';

  	public static $rules = array(
        'rule_name'   =>'required',
        'valid_from'  =>'required',
        'valid_to'    =>'required',
   		'label_id' =>'required',
   		'main_title' =>'required',
   		'banner_filename' =>'required',
    );

  	public static $rulesedit = array(
        'rule_name'   =>'required',
        'valid_from'  =>'required',
        'valid_to'    =>'required',
        'main_title' =>'required',
   		'label_id' =>'required',
    );


}
