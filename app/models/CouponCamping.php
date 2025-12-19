<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class CouponCamping extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;
    protected $table = 'jocom_coupon_camping';
    protected $fillable = ['id', 'start_at', 'end_at', 'name', 'coupon_data', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by']; // column name
}