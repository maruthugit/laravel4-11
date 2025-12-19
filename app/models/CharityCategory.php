<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class CharityCategory extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for Charity info for each category.
     *
     * @var string
     */
    protected $table = 'jocom_charity_category';

    public static $rules = array(
        'image2'      =>'mimes:gif,jpeg,jpg,png',
        'image3'      =>'mimes:gif,jpeg,jpg,png',
    );

    public static function getCharityOption()
    {
        $banner = DB::table('jocom_products_category')
                ->where('charity', '=', 1)
                ->lists('id');

        $childs = array();
        $list = array("-- Select Category --" => 0);

        foreach ($banner as $banners)
        {
                $child = ApiProduct::fetchCategoryTree($banners);

                if (count($child) > 0)
                        $childs = array_merge($childs, $child);
        }

        foreach ($childs as $v)
        {
                $temp = Category::where('id', '=', $v['id'])->lists('id', 'category_name');    

                if (count($temp) > 0)
                {
                        $list = array_merge($list, $temp);
                }
        }

        $list = array_flip($list);
        asort($list);

        return $list;
    }    

    public static function getCharityOption2()
    {
        $banner = DB::table('jocom_charity_category')
                ->lists('name', 'id');

        return $banner;
    }    
}
