<?php

class Visitor extends Eloquent
{
    protected $table = 'jocom_visitor_details';

  	public static $rules = array(
        'visitor_name'   =>'required',
        'visitor_ic'  =>'required',
        'visitor_datetime'    =>'required',
    );


    
}
