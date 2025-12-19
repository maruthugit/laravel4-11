<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class LogisticBatchPrescan extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    
    protected $table = 'logistic_batch_prescan';

    public static $rules = array(
        'signature'      =>'mimes:gif,jpeg,jpg,png',
    );


}