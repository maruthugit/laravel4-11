<?php

class BulkCoupon extends Eloquent {

	protected $table = 'jocom_coupon_bulk';

	public function scopeBulkadd_coupon()
    {
        $username = Input::get('username');
        
        if ($username !='') {
               $ori_username = substr($username, 0, strpos($username, "("));
            }else{
                $ori_username = "";
            }
        
        $coupon = new BulkCoupon;
        $coupon->prefix = Input::get('prefix');
        $coupon->code_length = Input::get('code_length');
        $coupon->generate_quantity = Input::get('gquantity');
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

        $coupon->save();

        return $insertedId = $coupon->id;
    }

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
            
            $coupon_id = Input::get('id');
            $couponb = BulkCoupon::find($coupon_id);
            $couponb->name = Input::get('name');
            $couponb->username = $ori_username;
            $couponb->amount = Input::get('amount');
            $couponb->amount_type = Input::get('amount_type');
            $couponb->status = Input::get('status');
            $couponb->min_purchase = Input::get('min_purchase');
            $couponb->max_purchase = Input::get('max_purchase');
            $couponb->valid_from = Input::get('valid_from');
            $couponb->valid_to = Input::get('valid_to');
            $couponb->type = Input::get('type');
            $couponb->qty = Input::get('qty');
            $couponb->q_limit = Input::get('q_limit');
            $couponb->cqty = Input::get('cqty');
            $couponb->c_limit = Input::get('c_limit');
            $couponb->free_delivery = Input::get('free_delivery');
            $couponb->free_process = Input::get('free_process');
            $couponb->boost_payment = Input::get('boost_payment');


            $couponb->save();

            $ori_type = Input::get('ori_type');
            $new_type = Input::get('type');

            $coupon_ids = [];
            $coupons = Coupon::where('coupon_code', 'LIKE', $couponb->prefix.'%')->select('id')->get();
            foreach ($coupons as $coupon) {
            	array_push($coupon_ids, $coupon->id);
            }


            if ($new_type == 'all' && $ori_type != 'all')
            {
                $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id);     
            
                $donedel = $coupont->delete();                
            }

            if ($new_type == 'seller')
            {
                if ($ori_type != 'seller'){
                    $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id);
                    $donedel = $coupont->delete();

                    $coupontc = CouponType::whereIn('coupon_id' , $coupon_ids);
                    $donedelc = $coupontc->delete();
                }

                $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_com_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_com_id')!="") {
                    $coupontype = new BulkCouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_com_id');
                    $coupontype->save();
                }

                foreach ($coupon_ids as $coupon_idc) {
                	$coupontc = CouponType::where('coupon_id', '=', $coupon_idc)->where('related_id', '=', Input::get('related_com_id'))->first();
                

	                if (count($coupontc) < 1 AND Input::get('related_com_id')!="") {
	                    $coupontype = new CouponType;
	                    $coupontype->coupon_id = $coupon_idc;
	                    $coupontype->related_id = Input::get('related_com_id');
	                    $coupontype->save();
	                }
                }

            }

            if ($new_type == 'item')
            {
                if ($ori_type != 'item'){
                    $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id);
                    $donedel = $coupont->delete();

                    $coupontc = CouponType::whereIn('coupon_id' , $coupon_ids);
                    $donedelc = $coupontc->delete();
                }

                //$getProduct = Products::select('sku')->where('id', '=', Input::get('related_item_id'))->first();

                $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_item_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_item_id')!="") {
                    $coupontype = new BulkCouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_item_id');
                    $coupontype->save();
                }

                foreach ($coupon_ids as $coupon_idc) {
                	$coupontc = CouponType::where('coupon_id', '=', $coupon_idc)->where('related_id', '=', Input::get('related_item_id'))->first();
                

	                if (count($coupontc) < 1 AND Input::get('related_item_id')!="") {
	                    $coupontype = new CouponType;
	                    $coupontype->coupon_id = $coupon_idc;
	                    $coupontype->related_id = Input::get('related_item_id');
	                    $coupontype->save();
	                }
                }

                                
            }

            if ($new_type == 'package')
            {
                if ($ori_type != 'package'){
                    $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id);
                    $donedel = $coupont->delete();

                    $coupontc = CouponType::whereIn('coupon_id' , $coupon_ids);
                    $donedelc = $coupontc->delete();
                }

                //$getProduct = Products::select('sku')->where('id', '=', Input::get('related_item_id'))->first();

                $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_package_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_package_id')!="") {
                    $coupontype = new BulkCouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_package_id');
                    $coupontype->save();
                }

                foreach ($coupon_ids as $coupon_idc) {
                	$coupontc = CouponType::where('coupon_id', '=', $coupon_idc)->where('related_id', '=', Input::get('related_package_id'))->first();
                

	                if (count($coupontc) < 1 AND Input::get('related_package_id')!="") {
	                    $coupontype = new CouponType;
	                    $coupontype->coupon_id = $coupon_idc;
	                    $coupontype->related_id = Input::get('related_package_id');
	                    $coupontype->save();
	                }
                }

                                
            }

            if ($new_type == 'customer')
            {
                if ($ori_type != 'customer'){
                    $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id); 
                    $donedel = $coupont->delete();

                    $coupontc = CouponType::whereIn('coupon_id' , $coupon_ids);
                    $donedelc = $coupontc->delete();
                }

                $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_customer_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_customer_id')!="") {
                    $coupontype = new BulkCouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_customer_id');
                    $coupontype->save();
                }

                foreach ($coupon_ids as $coupon_idc) {
                	$coupontc = CouponType::where('coupon_id', '=', $coupon_idc)->where('related_id', '=', Input::get('related_customer_id'))->first();
                

	                if (count($coupontc) < 1 AND Input::get('related_customer_id')!="") {
	                    $coupontype = new CouponType;
	                    $coupontype->coupon_id = $coupon_idc;
	                    $coupontype->related_id = Input::get('related_customer_id');
	                    $coupontype->save();
	                }
                }
                                
            }

            if ($new_type == 'category')
            {
                if ($ori_type != 'category'){
                    $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id); 
                    $donedel = $coupont->delete();

                    $coupontc = CouponType::whereIn('coupon_id' , $coupon_ids);
                    $donedelc = $coupontc->delete();
                }

                $coupont = BulkCouponType::where('coupon_id', '=', $coupon_id)->where('related_id', '=', Input::get('related_category_id'))->first();
                

                if (count($coupont) < 1 AND Input::get('related_category_id')!="") {
                    $coupontype = new BulkCouponType;
                    $coupontype->coupon_id = $coupon_id;
                    $coupontype->related_id = Input::get('related_category_id');
                    $coupontype->save();
                }

                foreach ($coupon_ids as $coupon_idc) {
                	$coupontc = CouponType::where('coupon_id', '=', $coupon_idc)->where('related_id', '=', Input::get('related_category_id'))->first();
                

	                if (count($coupontc) < 1 AND Input::get('related_category_id')!="") {
	                    $coupontype = new CouponType;
	                    $coupontype->coupon_id = $coupon_idc;
	                    $coupontype->related_id = Input::get('related_category_id');
	                    $coupontype->save();
	                }
                }
                                
            }

            Coupon::where('coupon_code', 'LIKE', $couponb->prefix.'%')
            	->update([
            		'name' => Input::get('name'),
		            'username' => $ori_username,
		            'amount' => Input::get('amount'),
		            'amount_type' => Input::get('amount_type'),
		            'status' => Input::get('status'),
		            'min_purchase' => Input::get('min_purchase'),
		            'max_purchase' => Input::get('max_purchase'),
		            'valid_from' => Input::get('valid_from'),
		            'valid_to' => Input::get('valid_to'),
		            'type' => Input::get('type'),
		            'qty' => Input::get('qty'),
		            'q_limit' => Input::get('q_limit'),
		            'cqty' => Input::get('cqty'),
		            'c_limit' => Input::get('c_limit'),
		            'free_delivery' => Input::get('free_delivery'),
		            'free_process' => Input::get('free_process'),
		            'boost_payment' => Input::get('boost_payment'),
            	]);

            return $coupon;
        }
        else
        {
            return false;
        }            
        
    }
	
}