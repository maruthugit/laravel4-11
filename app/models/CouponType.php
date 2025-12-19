<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class CouponType extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for relationship between transaction and coupon.
     *
     * @var string
     */
    protected $table = 'jocom_coupon_type';

    public function scopeGet_list($query, $id) 
    {
        $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
        $couponlist = array();

        foreach ($listtype as $templist)
        {
            $couponlist[] = $templist->related_id;
        }
        if(sizeof($couponlist) == 0) 
        {
            $couponlist[] = 0;
        }

        return $couponlist;
    }

    public function scopeGet_list_package($query, $id) 
    {
        $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
        $couponlist = array();

        foreach ($listtype as $templist)
        {
            $couponlist[] = 'JCP-' . str_pad($templist->related_id, 12, '0', STR_PAD_LEFT);
        }
        if(sizeof($couponlist) == 0) 
        {
            $couponlist[] = 0;
        }

        return $couponlist;
    }

    public function scopeGet_list_category($query, $id) 
    {
        $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
        $couponlist2 = array();

        foreach ($listtype as $templist)
        {
            $couponlist2[] = $templist->related_id;
        }
        if(sizeof($couponlist2) == 0) 
        {
            $couponlist2[] = 0;
        }

        $listtype = ProductsCategory::select('product_id')->whereIn('category_id', $couponlist2)->get();
        $couponlist = array();

        foreach ($listtype as $templist)
        {
            $couponlist[] = $templist->product_id;
        }
        if(sizeof($couponlist) == 0) 
        {
            $couponlist[] = 0;
        }

        $couponlist = array_unique($couponlist);

        return $couponlist;
    }
    
}
