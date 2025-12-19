<?php

class JocomComboDeals  extends Eloquent
{
    protected $table = 'jocom_combodeals';

  	public static $rules = array(
  	    'main_title'   =>'required',
        'rule_name'   =>'required',
        'valid_from'  =>'required',
        'valid_to'    =>'required',
   		'label_id' =>'required',
    );
}
