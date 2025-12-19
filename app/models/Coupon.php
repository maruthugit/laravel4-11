<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;



class Coupon extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for coupon.
     *
     * @var string
     */
    protected $table = 'jocom_coupon';

    public static $rules = array(
        'coupon_code'=>'required',
        'amount'=>'required|numeric',
        'min_purchase'=>'numeric',
        'qty'=>'numeric',
    );
    
    public static $rulesfreeitem = array(
        'coupon_code'=>'required',
        'start_from'=>'required',
        'end_to'=>'required',
    );
    
    public static $message = array(
        'coupon_code.required'=>'Please enter Coupon Code',
        'amount.required'=>'Please enter correct Coupon Amount',
    );
    
    public static $messagefreeitem = array(
        'coupon_code.required'=>'Please enter Coupon Code',
        'start_from.required'=>'Please enter Start Date',
        'end_to.required'=>'Please enter End Date',
    );
    

    /**
     * Listing for coupon
     * @return [type] [description]
     */
    public function scopeCoupon_listing()
    {
        
        //commented as not yet pagination
        //$list = Coupon::orderBy('id', 'Desc')->paginate(25);                
        $list = Coupon::orderBy('id', 'Desc')->get();

       return $list;
    }

    /**
     * Add coupon
     * @return [type] [description]
     */
    public function scopeAdd_coupon()
    {
        $username = Input::get('username');
        
        if ($username !='') {
               $ori_username = substr($username, 0, strpos($username, "("));
            }else{
                $ori_username = "";
            }
            
        if(Input::get('zone_id')!=""){
        foreach (Input::get('zone_id') as $key => $value)
        {
               $region_ids[]=Input::get("zone_id.{$key}");
                
        }
        $region=implode(',',$region_ids);
        }else{
          $region=null;  
        }
        
        $coupon = new Coupon;
        $coupon->coupon_code = Input::get('coupon_code');
        $coupon->name = Input::get('name');
        $coupon->username = $ori_username;
        $coupon->amount = Input::get('amount');
        $coupon->amount_type = Input::get('amount_type');
        $coupon->status = Input::get('status');
        $coupon->min_purchase = Input::get('min_purchase');
        $coupon->max_purchase = Input::get('max_purchase');
        $coupon->valid_from = Input::get('valid_from');
        $coupon->valid_to = Input::get('valid_to');
        $coupon->type = Input::get('type');
        $coupon->qty = Input::get('qty');
        $coupon->q_limit = Input::get('q_limit');
        $coupon->cqty = Input::get('cqty');
        $coupon->c_limit = Input::get('c_limit');
        $coupon->free_delivery = Input::get('free_delivery');
        $coupon->free_process = Input::get('free_process');
        $coupon->boost_payment = Input::get('boost_payment');
        $coupon->razerpay_payment = Input::get('razerpay_payment');
        $coupon->is_jpoint = Input::get('is_jpoint') ? 1:0;
        $coupon->is_preferred_member = Input::get('is_preferred_member') ? 1:0;
        $coupon->region=$region;
        $coupon->insert_by = Session::get('username');
        $coupon->insert_date = date("Y-m-d h:i:sa");        
        $coupon->modify_by = Session::get('username');
        $coupon->modify_date = date("Y-m-d h:i:sa");
        $coupon->save();
        
       

        return $insertedId = $coupon->id;
        
    }
    
    /**
     * Add coupon Free Item
     * @return [type] [description]
     */
    public function scopeAdd_couponfreeitem()
    {
        $username = '';
        
     
        
        $coupon = new Coupon;
        $coupon->coupon_code = Input::get('coupon_code');
        $coupon->name =  Input::get('name');
        $coupon->username = $username;
        $coupon->amount = 0;
        $coupon->amount_type = 'Nett';
        $coupon->status = Input::get('status');
        $coupon->min_purchase = 0;
        $coupon->max_purchase = 0;
        $coupon->valid_from = Input::get('start_from');
        $coupon->valid_to = Input::get('end_to');
        $coupon->type = 'all';
        $coupon->qty = Input::get('qty');
        $coupon->q_limit = Input::get('q_limit');
        $coupon->cqty = 1;
        $coupon->c_limit = 'Yes';
        $coupon->free_delivery = 0;
        $coupon->free_process = 0;
        $coupon->delivery_discount = 0;
        $coupon->is_free_item = 1;
        $coupon->is_seller = Input::get('seller_flg');
        $coupon->seller_id = Input::get('seller');
        $coupon->insert_by = Session::get('username');
        $coupon->insert_date = date("Y-m-d h:i:sa");        
        $coupon->modify_by = Session::get('username');
        $coupon->modify_date = date("Y-m-d h:i:sa");
        $coupon->save();

        return $insertedId = $coupon->id;
        
    }
    
    public function scopeBulkadd_coupon($query, $coupon_code)
    {
        $username = Input::get('username');
        
        if ($username !='') {
               $ori_username = substr($username, 0, strpos($username, "("));
            }else{
                $ori_username = "";
            }
        
        $coupon = new Coupon;
        $coupon->coupon_code = $coupon_code;
        $coupon->name = Input::get('name');
        $coupon->username = $ori_username;
        $coupon->amount = Input::get('amount');
        $coupon->amount_type = Input::get('amount_type');
        $coupon->status = Input::get('status');
        $coupon->min_purchase = Input::get('min_purchase');
        $coupon->max_purchase = Input::get('max_purchase');
        $coupon->valid_from = Input::get('valid_from');
        $coupon->valid_to = Input::get('valid_to');
        $coupon->type = Input::get('type');
        $coupon->qty = Input::get('qty');
        $coupon->q_limit = Input::get('q_limit');
        $coupon->cqty = Input::get('cqty');
        $coupon->c_limit = Input::get('c_limit');
        $coupon->free_delivery = Input::get('free_delivery');
        $coupon->free_process = Input::get('free_process');
        $coupon->boost_payment = Input::get('boost_payment');

        $coupon->insert_by = Session::get('username');
        $coupon->insert_date = date("Y-m-d h:i:sa");        
        $coupon->modify_by = Session::get('username');
        $coupon->modify_date = date("Y-m-d h:i:sa");
        $coupon->save();

        return $insertedId = $coupon->id;
    }

    /**
     * Save coupon
     * @return [type] [description]
     */
    public function scopeSave_freecoupon()
    {
        if (Input::has('id'))
        {
            $username = '';
            
            
            
            $coupon_id = Input::get('id');
            $coupon = Coupon::find($coupon_id);
            $coupon->name = Input::get('name');
            $coupon->username = $username;
            $coupon->amount = 0;
            $coupon->amount_type = 'Nett';
            $coupon->status = Input::get('status');
            $coupon->min_purchase = 0;
            $coupon->max_purchase = 0;
            $coupon->valid_from = Input::get('start_from');
            $coupon->valid_to = Input::get('end_to');
            $coupon->type = 'all';
            $coupon->qty = Input::get('qty');
            $coupon->q_limit = Input::get('q_limit');
            $coupon->cqty = 1;
            $coupon->c_limit = 'Yes';
            $coupon->free_delivery = 0;
            $coupon->free_process = 0;
            $coupon->delivery_discount = 0;
            $coupon->is_free_item = 1;
            $coupon->is_seller = Input::get('seller_flg');
            $coupon->seller_id = Input::get('seller');
            $coupon->modify_by = Session::get('username');
            $coupon->modify_date = date("Y-m-d h:i:sa"); 

            $coupon->save();

            $ori_type = 'all';
            $new_type = 'item';

           

            if ($new_type == 'item')
            {
                // if ($ori_type != 'item'){
                //     $coupont = CouponType::where('coupon_id', '=', $coupon_id);
                //     $donedel = $coupont->delete();
                // }

                //$getProduct = Products::select('sku')->where('id', '=', Input::get('related_item_id'))->first();

                $coupont = CouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_item_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_item_id')!="") {
                    $coupontype = new CouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_item_id');
                    $coupontype->save();
                }

                                
            }

            

            return $coupon;
        }
        else
        {
            return false;
        }            
        
    }
    

    /**
     * Save coupon
     * @return [type] [description]
     */
    public function scopeSave_coupon()
    {
        if (Input::has('id'))
        {
            $username = Input::get('username');
            
            if ($username !='') {
               $ori_username = substr($username, 0, strpos($username, "("));
            }else{
                $ori_username = "";
            }
            if(Input::get('zone_id')!=""){
        foreach (Input::get('zone_id') as $key => $value)
        {
               $region_ids[]=Input::get("zone_id.{$key}");
                
        }
        $region=implode(',',$region_ids);
        }else{
          $region=null;  
        }
            $coupon_id = Input::get('id');
            $coupon = Coupon::find($coupon_id);
            $coupon->name = Input::get('name');
            $coupon->username = $ori_username;
            $coupon->amount = Input::get('amount');
            $coupon->amount_type = Input::get('amount_type');
            $coupon->status = Input::get('status');
            $coupon->min_purchase = Input::get('min_purchase');
            $coupon->max_purchase = Input::get('max_purchase');
            $coupon->valid_from = Input::get('valid_from');
            $coupon->valid_to = Input::get('valid_to');
            $coupon->type = Input::get('type');
            $coupon->qty = Input::get('qty');
            $coupon->q_limit = Input::get('q_limit');
            $coupon->cqty = Input::get('cqty');
            $coupon->c_limit = Input::get('c_limit');
            $coupon->free_delivery = Input::get('free_delivery');
            $coupon->free_process = Input::get('free_process');
            $coupon->boost_payment = Input::get('boost_payment');
            $coupon->razerpay_payment = Input::get('razerpay_payment');
            $coupon->is_jpoint = Input::get('is_jpoint') ? 1:0;
            $coupon->is_preferred_member = Input::get('is_preferred_member') ? 1:0;
            $coupon->region=$region;
            $coupon->modify_by = Session::get('username');
            $coupon->modify_date = date("Y-m-d h:i:sa"); 

            $coupon->save();

            $ori_type = Input::get('ori_type');
            $new_type = Input::get('type');

            if ($new_type == 'all' && $ori_type != 'all')
            {
                $coupont = CouponType::where('coupon_id', '=', $coupon_id);     
            
                $donedel = $coupont->delete();                
            }

            if ($new_type == 'seller')
            {
                if ($ori_type != 'seller'){
                    $coupont = CouponType::where('coupon_id', '=', $coupon_id);
                    $donedel = $coupont->delete();
                }

                $coupont = CouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_com_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_com_id')!="") {
                    $coupontype = new CouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_com_id');
                    $coupontype->save();
                }

                

            }

            if ($new_type == 'item')
            {
                if ($ori_type != 'item'){
                    $coupont = CouponType::where('coupon_id', '=', $coupon_id);
                    $donedel = $coupont->delete();
                }

                //$getProduct = Products::select('sku')->where('id', '=', Input::get('related_item_id'))->first();

                $coupont = CouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_item_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_item_id')!="") {
                    $coupontype = new CouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_item_id');
                    $coupontype->save();
                }

                                
            }

            if ($new_type == 'package')
            {
                if ($ori_type != 'package'){
                    $coupont = CouponType::where('coupon_id', '=', $coupon_id);
                    $donedel = $coupont->delete();
                }

                //$getProduct = Products::select('sku')->where('id', '=', Input::get('related_item_id'))->first();

                $coupont = CouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_package_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_package_id')!="") {
                    $coupontype = new CouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_package_id');
                    $coupontype->save();
                }

                                
            }

            if ($new_type == 'customer')
            {
                if ($ori_type != 'customer'){
                    $coupont = CouponType::where('coupon_id', '=', $coupon_id); 
                    $donedel = $coupont->delete();
                }

                $coupont = CouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_customer_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_customer_id')!="") {
                    $coupontype = new CouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_customer_id');
                    $coupontype->save();
                }
                                
            }

            if ($new_type == 'category')
            {
                if ($ori_type != 'category'){
                    $coupont = CouponType::where('coupon_id', '=', $coupon_id); 
                    $donedel = $coupont->delete();
                }

                $coupont = CouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_category_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_category_id')!="") {
                    $coupontype = new CouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_category_id');
                    $coupontype->save();
                }
                                
            }

            return $coupon;
        }
        else
        {
            return false;
        }            
        
    }

    public static function duplicate($coupon = null, $coupontype = null)
    {
        $num_of_chracter    = 4;
        $keys               = "1234567890ABCDEFGHIJKLMNOPQRSTUVWXYZ";

        $user = Session::get('username');

        if ($user == "")
            $user = "API Register";

        $save = 0;

        $newcoupon                  = new Coupon;

        $newcoupon->name            = $coupon->name;
        $newcoupon->amount          = $coupon->amount;
        $newcoupon->amount_type     = $coupon->amount_type;
        $newcoupon->min_purchase    = $coupon->min_purchase;
        $newcoupon->max_purchase    = $coupon->max_purchase;
        $newcoupon->valid_from      = $coupon->valid_from;
        $newcoupon->valid_to        = $coupon->valid_to;
        $newcoupon->type            = $coupon->type;
        $newcoupon->qty             = $coupon->qty;
        $newcoupon->q_limit         = $coupon->q_limit;
        $newcoupon->cqty            = $coupon->cqty;
        $newcoupon->c_limit         = $coupon->c_limit;
        $newcoupon->free_delivery   = $coupon->free_delivery;
        $newcoupon->free_process    = $coupon->free_process;
        $newcoupon->status          = $coupon->status;
        $newcoupon->insert_by       = $user;
        $newcoupon->insert_date     = date("Y-m-d h:i:sa");

        while(!$save)
        {
            $pass = "";

            for($j = 0; $j < $num_of_chracter; $j++)
            {
                $pass .= $keys[mt_rand(0, strlen($keys)-1)];
            }

            $newcoupon->coupon_code = $coupon->coupon_code . $pass;

            $exist = Coupon::where('coupon_code', $newcoupon->coupon_code)->first();

            if (count($exist)<=0)
            {
                $newcoupon->save();

                $save = 1;
            }
        }

        if (count($coupontype)>0 AND isset($newcoupon->id))
        {
            foreach ($coupontype as $type)
            {
                $newtype               = new CouponType;
                $newtype->coupon_id    = $newcoupon->id;
                $newtype->related_id   = $type->related_id;

                $newtype->save();
            }
        }

        return $newcoupon->coupon_code;
    }

    
    public function transaction()
    {
        return $this->belongsToMany('Transaction');
    }
    
}
