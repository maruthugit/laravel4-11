<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class ApiHelpCenter extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for HelpCenter
     *
     * @var string
     */
    protected $table = 'jocom_helpcenter';

    public static $rules = array(
        'username'=>'required',
        'order_id'=>'required',
        'email'=>'required',
        'description'=>'required',
        'contact_number'=>'required',
    );


    public static $message = array(
        'username.required'=>'Username is required',
        'order_id.required'=>'Order ID is required',
        'email.required'=>'Email ID is required',
        'description.required'=>'Description is required',
        'contact_number.required'=>'Contact ID is required',
    );
    
    public static $apirule=array(
         'username'=>'required',
         'query_topic'=>'required',
         'email'=>'required',
         'contact_number'=>'required',
    );
    public static $apimessage = array(
        'username.required'=>'Username is required',
        'query_topic.required'=>'Topic is required',
        'email.required'=>'Email ID is required',
        'contact_number.required'=>'Contact Number is required',
    );



}