<?php

class FlashSale extends Eloquent
{
    protected $table = 'jocom_flashsale';

  	public static $rules = array(
        'rule_name'   =>'required',
        'valid_from'  =>'required',
        'valid_to'    =>'required',
   		'label_id' =>'required',
    );
}
