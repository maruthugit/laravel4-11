<?php 

class CouponController extends BaseController {
    
    
    public function __construct(Coupon $coupon) {

        $this->coupon = $coupon;

       
    }
    
    private static function ActionBAR($href, $html = false, $extend = false){
		$actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="' . $href . '">' . ($html ? $html : '<i class="fa fa-pencil"></i>') . '</a>';
		if (Permission::CheckAccessLevel(Session::get('role_id'), 4, 9, 'AND') && $extend) $actionBar .= ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id}});"><i class="fa fa-remove"></i></a>';
		return $actionBar;
	}
	
	private static function editCoupon_data_callback($type, $list, &$e){
		$e['Seller'] = ($type === 'seller' ? Seller::select('id','username','company_name')->whereIn('id', $list)->get() : []);
		$e['Item'] = ($type === 'item' ? Product::select('id', 'sku','name')->whereIn('id', $list)->get() : []);
		$e['PItem'] = ($type === 'package' ? Package::select('id', 'sku','name')->whereIn('id', $list)->get() : []);
		$e['Customer'] = ($type === 'customer' ? Customer::select('id','username','full_name')->whereIn('id', $list)->get() : []);
		$e['Category'] = ($type === 'category' ? Category::select('id','category_name')->whereIn('id', $list)->get() : []);
	}

	private static function editCoupon_data($id, $editCoupon = false){
		$e = [];
		if(in_array($editCoupon->type, ['seller', 'item', 'package', 'customer', 'category'])) $e['couponlist'] = CouponType::get_list($id);
		$e['Coupon'] = $editCoupon;
		self::editCoupon_data_callback($editCoupon->type, $e['couponlist'], $e);
		$e['cus_data'] = (self::$load_cus ? self::$load_cus : DB::table('jocom_user')->where('active_status', '!=', '2')->select('username','full_name')->get());
		return $e;
	}

    /**
     * Default listing for all coupon.
     * @return [type] [description]
     */
    public function anyIndex()
    {
        
        //commented as using join table
        //$listing = Transaction::orderBy('id', 'Desc')->paginate(25);
        
        //reserve for search function
        // $tempColumn = 'a.buyer_username';
        // $tempOperator = 'LIKE';
        // $tempSearch = '%%';
                
        // $listing = DB::table('jocom_transaction AS a')
        // ->select('a.*', 'b.coupon_code', 'b.coupon_amount')
        // ->leftJoin('jocom_transaction_coupon AS b','a.id', '=', 'b.transaction_id')
        // ->orderBy('a.id', 'Desc', 'b.transaction_date', 'ASC')
        // ->where($tempColumn, $tempOperator, $tempSearch)->get();

        //$this->layout->content = View::make('admin.transaction_listing')->with('display_listing', $listing);        
        
        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_listing');
    }

    public function anyListing()
    {
        $coupon = Coupon::select(array(
                'id', 
                'coupon_code',
                'name',
                'amount',
                'status',
                'amount_type'
                ))
                ->where('status', '!=', 2)
                ->where('is_free_item','=',0);

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="coupon/edit/{{$id}}"><i class="fa fa-pencil"></i></a>';
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 9, 'AND'))
        {     
            $actionBar = $actionBar . ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id;}});"><i class="fa fa-remove"></i></a>';
        }
       

        return Datatables::of($coupon)
                ->edit_column('amount', '<?php if ($amount_type=="Nett") echo Config::get("constants.CURRENCY").number_format($amount,2); else if ($amount_type=="%") echo number_format($amount,0)."%"; ?>')
                ->edit_column('status', '<?php if ($status==1) echo "Active"; elseif ($status==0) echo "Inactive"; ?>')
                // ->edit_column('status', '{{ucwords($status);}}')
                ->add_column('Action', $actionBar)
                ->make(true);

        // return Datatables::of($coupon)
        //         ->edit_column('amount', '{{number_format($amount,2);}}')
        //         ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="coupon/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
        //         <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id;}});"><i class="fa fa-remove"></i></a>')
        //         ->make(true);
       
    }

    public function anyAdd()
    {
        
        if (Input::has('add_check'))
        {
            $tempCheck = Coupon::where('coupon_code', '=', Input::get('coupon_code'))->first();

            if ($tempCheck != null)
            {
                return Redirect::to('coupon/add')->with('message', Input::get('coupon_code').' is already exists!');
            }

            $validator = Validator::make(Input::all(), Coupon::$rules, Coupon::$message);

            Input::flash();
            
            // die('In');

            if ($validator->passes()) 
            {
                $rs = Coupon::add_coupon();
                
                Input::flush();

                if ($rs == true)
                {
                    $insert_audit = General::audit_trail('CouponController.php', 'Add()', 'Add Coupon', Session::get('username'), 'CMS');
                    return Redirect::to('coupon/edit/'.$rs)->with('success', 'Coupon(ID: '.$rs.') added successfully');
                }
                else
                {
                    return Redirect::to('coupon')->with('message', 'Error adding new coupon.');
                }   
            }
            else
            {
                return Redirect::to('coupon/add')->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
            }
            
        }
        else
        {
            $customers = DB::table('jocom_user')->where('active_status', '!=', '2')->select('username','full_name')->get();
            $zoneOptions = Zone::all();
            return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_add')->with('customers',$customers)->with('zoneOptions',$zoneOptions);

        }
    }

    public function anyEdit($id = null)
    {
        if (isset($id))
        {
            if (Input::has('id'))
            {
                 $validator = Validator::make(Input::all(), Coupon::$rules, Coupon::$message);

                 Input::flash();           

                if ($validator->passes()) {

                    $rs = Coupon::save_coupon();

                    Input::flush();

                    if ($rs == true)
                    {
                        $insert_audit = General::audit_trail('CouponController.php', 'Edit()', 'Edit Coupon', Session::get('username'), 'CMS');
                        return Redirect::to('coupon/edit/'.$id)->with('success', 'Coupon(ID: '.$id.') updated successfully.');
                    }
                    else{
                        return Redirect::to('coupon/edit/'.$id)->with('message', 'Coupon(ID: '.$id.') update failed.');
                    } 

                } else 
                {
                    return Redirect::to('coupon/edit/'.$id)->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
                }


                
            }
            else
            {
                
                $editCoupon = Coupon::find($id);

                if ($editCoupon->type == 'seller')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }
                    $couponlist = CouponType::get_list($id);
                    $editSeller = Seller::select('id','username','company_name')->whereIn('id', $couponlist)->get();
                }
                else
                {
                    $editSeller = array();
                }

                if ($editCoupon->type == 'item')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }

                    $couponlist = CouponType::get_list($id);
                    $editItem = Product::select('id', 'sku','name')->whereIn('id', $couponlist)->get();
                    // $editPItem = Package::select('id', 'sku','name')->whereIn('id', $couponlist)->get();

                    // $editItem = Product::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                    // $editPItem = Package::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                }
                else
                {
                    $editItem = array();
                    // $editPItem = array();
                }

                if ($editCoupon->type == 'package')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }

                    $couponlist = CouponType::get_list($id);
                    // $editItem = Product::select('id', 'sku','name')->whereIn('id', $couponlist)->get();
                    $editPItem = Package::select('id', 'sku','name')->whereIn('id', $couponlist)->get();

                    // $editItem = Product::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                    // $editPItem = Package::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                }
                else
                {
                    // $editItem = array();
                    $editPItem = array();
                }


                if ($editCoupon->type == 'customer')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }

                    $couponlist = CouponType::get_list($id);
                    $editCustomer = Customer::select('id','username','full_name')->whereIn('id', $couponlist)->get();
                }
                else
                {
                    $editCustomer = array();
                }

                if ($editCoupon->type == 'category')
                {
                    $couponlist = CouponType::get_list($id);
                    $editCategory = Category::select('id','category_name')->whereIn('id', $couponlist)->get();
                }
                else
                {
                    $editCategory = array();
                }
                
                // $editSeller = Seller::all()->lists('id','company_name');
                //$editItem = Product::all()->lists('sku','name');
                //$editPItem = Package::all()->lists('sku','name');
                 //region
                $selectedzone=$editCoupon->region;
                $zonesplit=explode(',',$selectedzone);
                foreach($zonesplit as $value){
                    $selectedregion[]=$value;
                }
                $zoneOptions = Zone::all();
                $delivery_zones=Zone::select('id as zone_id','name')->whereIn('id',$selectedregion)->get();
                
                //endregion
                $customers = DB::table('jocom_user')->where('active_status', '!=', '2')->select('username','full_name')->get();
                return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_edit')->with('display_coupon', $editCoupon)->with('display_seller', $editSeller)->with('display_item', $editItem)->with('display_package', $editPItem)->with('display_customer', $editCustomer)->with('display_category', $editCategory)->with('customers', $customers)->with('zoneOptions',$zoneOptions)->with('deliveryZone',$selectedregion)->with('deliveryFees',$delivery_zones);
            }       
        }
        else
        {
            return Redirect::to('coupon')->with('message', 'No transaction is selected for edit.');
        }
        
    }

    public function anyRemove() 
    {
        if (Input::has('remove_coupon_id'))
        {
            $coupon_id = Input::get('remove_coupon_id');
            $coupon = Coupon::find($coupon_id);    
            $coupon->status = 2;

            if ($coupon->save())
            {
                $insert_audit = General::audit_trail('CouponController.php', 'Remove()', 'Delete Coupon', Session::get('username'), 'CMS');
                return Redirect::to('coupon')->with('success', 'Coupon(ID: '.$coupon_id.') has been deleted.');
            }
            else
            {
                return Redirect::to('coupon')->with('message', 'Delete failed. Data has not changed');
            }

        
            // if ($coupon->delete())
            // {
                
            //     return Redirect::to('coupon')->with('success', 'Coupon(ID: '.$coupon_id.') has been deleted.');
            // }
            // else
            // {
            //     return Redirect::to('coupon')->with('message', 'Delete failed. Data has not changed');
            // }
        }
        elseif (Input::has('remove_type_id'))
        {
            $details_id = Input::get('remove_type_id');
            $coupon_id = Input::get('couponID');
            $details = CouponType::where('related_id', '=', $details_id)->where('coupon_id', '=', $coupon_id)->first();       
        
            if ($details->delete())
            {
                $insert_audit = General::audit_trail('CouponController.php', 'Remove()', 'Delete Coupon Type', Session::get('username'), 'CMS');
                return Redirect::to('coupon/edit/'.$coupon_id)->with('success', 'Deleted.');
            }
            else
            {
                return Redirect::to('coupon/edit/'.$coupon_id)->with('message', 'Delete failed. Data has not changed');
            }
        }              
        
    }

    public function anyDuplicate($id = null)
    {
        if (isset($id))
        {
            if (Input::has('duplicate') AND Input::get('duplicate') > 0 AND Input::get('duplicate') <= 100)
            {
                $coupon  = Coupon::find($id);
                $coupontype = CouponType::where('coupon_id', $id)->get();
                $num = Input::get('duplicate');

                if (count($coupon)>0)
                {
                    for($i = 0; $i < $num; $i++)
                        $tem_msg = Coupon::duplicate($coupon, $coupontype);
                    
                    $success = 'Coupon(ID: '.$id.') has been duplicated successfully.';
                }                
            }
            else
                $message = 'Invalid duplicate quantity!';

            return Redirect::to('coupon/edit/'.$id)->with('message', $message)->with('success', $success);
        }
        else
        {
            return Redirect::to('coupon')->with('message', 'No coupon is selected for edit.');
        }
        
    }

    public function anySelectseller()
    {
        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_seller');
    }

    public function anyListingseller()
    {
        $seller = Seller::select(array(
                'id', 
                'company_name', 
                'username'
                ));

              
        // $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}}, {{$company_name;}});return false;"><i class="fa fa-hand-o-left "></i></a>';

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}});return false;"><i class="fa fa-hand-o-left "></i></a>';
       

        return Datatables::of($seller)
                ->edit_column('company_name', '{{ucwords($company_name);}}')
                ->add_column('Action', $actionBar)
                ->make(true);       
       
    }



    public function anySelectitem()
    {
        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_item');
    }

    public function anyListingitem()
    {
        $product = Product::select(array(
                'id', 
                'sku', 
                'name'
                ));

              
        // $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}}, {{$company_name;}});return false;"><i class="fa fa-hand-o-left "></i></a>';

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent2({{$id;}});return false;"><i class="fa fa-hand-o-left "></i></a>';
       

        return Datatables::of($product)
                ->edit_column('name', '{{ucwords($name);}}')
                ->add_column('Action', $actionBar)
                ->make(true);
      
       
    }

    public function anySelectpackage()
    {
        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_package');
    }

    public function anyListingpackage()
    {
        $package = Package::select(array(
                'id', 
                'sku', 
                'name'
                ));

              
        // $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}}, {{$company_name;}});return false;"><i class="fa fa-hand-o-left "></i></a>';

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent4({{$id;}});return false;"><i class="fa fa-hand-o-left "></i></a>';
       

        return Datatables::of($package)
                ->edit_column('name', '{{ucwords($name);}}')
                ->add_column('Action', $actionBar)
                ->make(true);
    }

    public function anySelectcustomer()
    {
        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_customer');
    }

    public function anyListingcustomer()
    {
        $customer = Customer::select(array(
                'id', 
                'username', 
                'full_name'
                ));

              
        // $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}}, {{$company_name;}});return false;"><i class="fa fa-hand-o-left "></i></a>';

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}});return false;"><i class="fa fa-hand-o-left "></i></a>';
       

        return Datatables::of($customer)
                ->edit_column('full_name', '{{ucwords($full_name);}}')
                ->add_column('Action', $actionBar)
                ->make(true);        
       
    }

    public function anySelectcategory()
    {
        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_category');
    }

    public function anyListingcategory()
    {
        $category = Category::select(array(
                'id', 
                'category_name'
                ));

              
        // $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}}, {{$company_name;}});return false;"><i class="fa fa-hand-o-left "></i></a>';

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}});return false;"><i class="fa fa-hand-o-left "></i></a>';
       

        return Datatables::of($category)
                ->edit_column('category_name', '{{ucwords($category_name);}}')
                ->add_column('Action', $actionBar)
                ->make(true);        
       
    }
    
    public static function rewardCoupon($reward_type , $voucher_type , $amount , $user_id){
        
        
        try{
            
            // Generate Coupon Code
            
            $Customer = Customer::find($user_id);
            $data = array();
            
            switch ($reward_type) {
                
                case 'BMGM_1':
                    
                    $couponCode = 'BMGM'.date("h").strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3)).date("s");
                    
                    // 1 year 
                    $valid_from = date('Y-m-d');
                    $valid_to = date('Y-m-d', strtotime('+3 month'));
                    
                    $Coupon = new Coupon;
                    $Coupon->coupon_code = $couponCode;
                    $Coupon->name = 'REWARD VOUCHER';
                    $Coupon->username = $Customer->username;
                    $Coupon->amount = $amount;
                    $Coupon->amount_type = $voucher_type;
                    $Coupon->min_purchase = 0;
                    $Coupon->valid_from = $valid_from;
                    $Coupon->valid_to = $valid_to;
                    $Coupon->type = 'all';
                    $Coupon->qty = 1;
                    $Coupon->q_limit = 'Yes';
                    $Coupon->cqty = 1;
                    $Coupon->c_limit = 'Yes';
                    $Coupon->free_delivery = 0;
                    $Coupon->free_process = 0;
                    $Coupon->delivery_discount = 0;
                    $Coupon->status = 1;
                    $Coupon->insert_by = 'CMS';
                    $Coupon->save();
                    
                    $CouponID = $Coupon->id ;
                    
                    $data['coupon_code'] = $couponCode;
                    $data['wording_text'] = 'You have rewarded with voucher RM'.$amount.' ! Please redeem the code on any purchase in tmGrocer APP before '.$valid_to." .";
                    
                    $subject = 'Enjoy your reward from tmGrocer';
                    Mail::send('emails.couponreward-v2', $data, function($message) use ($Customer,$subject)
                    {
                        $message->from('payment@tmgrocer.com', 'tmGrocer');
                        $message->to($Customer->email, $Customer->firstname)->subject($subject);
                    });
                    

                    break;
                
                case 'BRTH':
                    
                    $couponCode = 'BRTH'.date("h").strtoupper(substr(str_shuffle(str_repeat("abcdefghijklmnopqrstuvwxyz", 3)), 0, 3)).date("s");
                    // 1 year 
                    $today = date('m-d');
                    $date = new DateTime($Customer->dob);
                    $valid_from = $date->format('m-d');
                    $valid_from2 = $date->format('Y-m-d');
                    $valid_to = date('Y-m-d', strtotime('+1 month'));

                    if ($today === $valid_from) {

                        $Coupon = new Coupon;
                        $Coupon->coupon_code = $couponCode;
                        $Coupon->name = 'REWARD VOUCHER';
                        $Coupon->username = $Customer->username;
                        $Coupon->amount = $amount;
                        $Coupon->amount_type = $voucher_type;
                        $Coupon->min_purchase = 0;
                        $Coupon->valid_from = $valid_from2;
                        $Coupon->valid_to = $valid_to;
                        $Coupon->type = 'all';
                        $Coupon->qty = 1;
                        $Coupon->q_limit = 'Yes';
                        $Coupon->cqty = 1;
                        $Coupon->c_limit = 'Yes';
                        $Coupon->free_delivery = 0;
                        $Coupon->free_process = 0;
                        $Coupon->delivery_discount = 0;
                        $Coupon->status = 1;
                        $Coupon->insert_by = 'CMS';
                        $Coupon->save();
                        
                        $CouponID = $Coupon->id ;
                        
                        $data['coupon_code'] = $couponCode;
                        $data['wording_text'] = 'Happy Birthday '.$Customer->full_name .'.You have rewarded with voucher RM'.$amount.' ! Please redeem the code on any purchase in tmGrocer APP before '.$valid_to." .";
                        
                        $subject = 'Enjoy your reward from tmGrocer';
                        Mail::send('emails.couponreward-v2', $data, function($message) use ($Customer,$subject)
                        {
                            $message->from('payment@tmgrocer.com', 'tmGrocer');
                            $message->to($Customer->email, $Customer->firstname)->subject($subject);
                        });
                    
                    }

                    break;
                
                default:
                    break;
            }
        
            
            
            
        } catch (Exception $ex) {
            echo $ex;
        }
        
        return $CouponID;
        
        
        
    }
    
    // Free Coupon Item Start
    
    public function anyFreecoupon()
    {
        $sellers = Seller::orderBy('company_name', 'asc')->get();

        foreach ($sellers as $seller) {
            switch ($seller->active_status) {
                case 0:
                    $status = ' **[Inactive]';
                    break;
                case 2:
                    $status = ' **[Deleted]';
                    break;
                default:
                    $status = '';
                    break;
            }

            $sellersOptions[$seller->id] = $seller->company_name.$status;
        }
       return View::make('coupon.addcoupon')
                    ->with('sellersOptions', $sellersOptions);
    }

    /* Function: anyAddFreecoupon
        Description : View to register new coupon page .
    */
    public function anyAddfreeitem()
    {
        if (Input::has('add_check'))
        {
            $tempCheck = Coupon::where('coupon_code', '=', Input::get('coupon_code'))->first();

            if ($tempCheck != null)
            {
                return Redirect::to('coupon/freecoupon')->with('message', Input::get('coupon_code').' is already exists!');
            }

            $validator = Validator::make(Input::all(), Coupon::$rulesfreeitem, Coupon::$messagefreeitem);

            Input::flash();

            if ($validator->passes()) 
            {
                $rs = Coupon::add_couponfreeitem();
                
                Input::flush();

                if ($rs == true)
                {
                    $insert_audit = General::audit_trail('CouponController.php', 'Addcoupon()', 'Add Coupon Free Item', Session::get('username'), 'CMS');
                    return Redirect::to('coupon/editfreecoupon/'.$rs)->with('success', 'Coupon(ID: '.$rs.') added successfully');
                }
                else
                {
                    return Redirect::to('coupon/freecoupon')->with('message', 'Error adding new coupon.');
                }   
            }
            else
            {
                return Redirect::to('coupon/freecoupon')->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
            }
            
        }
        
    }

    /* Function: anyEditfreecoupon
        VIEW: View to edit existing registered coupons .

        _INPUT_ :

        1. id (Coupon id)
    */
    public function anyEditfreecoupon($id = null)
    {
        // print_r(Input::all());
        if (isset($id))
        {
            if (Input::has('id'))
            {
                 $validator = Validator::make(Input::all(), Coupon::$rulesfreeitem, Coupon::$messagefreeitem);

                 Input::flash();           

                if ($validator->passes()) {

                    $rs = Coupon::save_freecoupon();

                    Input::flush();

                    if ($rs == true)
                    {
                        $insert_audit = General::audit_trail('CouponController.php', 'Edit()', 'Edit Coupon', Session::get('username'), 'CMS');
                        return Redirect::to('coupon/editfreecoupon/'.$id)->with('success', 'Coupon(ID: '.$id.') updated successfully.');
                    }
                    else{
                        return Redirect::to('coupon/editfreecoupon/'.$id)->with('message', 'Coupon(ID: '.$id.') update failed.');
                    } 

                } else 
                {
                    return Redirect::to('coupon/editfreecoupon/'.$id)->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
                }


                
            }
            else
            {
                
                $editCoupon = Coupon::find($id);

                $editItem = array();
                $sellers = Seller::orderBy('company_name', 'asc')->get();

                foreach ($sellers as $seller) {
                    switch ($seller->active_status) {
                        case 0:
                            $status = ' **[Inactive]';
                            break;
                        case 2:
                            $status = ' **[Deleted]';
                            break;
                        default:
                            $status = '';
                            break;
                    }

                    $sellersOptions[$seller->id] = $seller->company_name.$status;
                }
                

                    $couponlist = CouponType::get_list($id);
                    $editItem = Product::select('id', 'sku','name')->whereIn('id', $couponlist)->get();
                

                return View::make('coupon/editcoupon')->with('display_coupon', $editCoupon)->with('display_item', $editItem)->with('sellersOptions', $sellersOptions);
            }       
        }
        else
        {
            return Redirect::to('coupon/freelisting')->with('message', 'No transaction is selected for edit.');
        }
        
    }

    public function anySelectfreecategory()
    {
        return View::make('coupon.coupon_category');
    }

    public function anyFreelisting()
    {
        return View::make('coupon.index');
    }



    public function anySelectfreeitem()
    {
        return View::make('coupon.coupon_item');
    }

    public function anyListingfreeitem()
    {
        $product = Product::select(array(
                'id', 
                'sku', 
                'name'
                ));

              
        // $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent({{$id;}}, {{$company_name;}});return false;"><i class="fa fa-hand-o-left "></i></a>';

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="#" onclick="sendUserToParent2({{$id;}});return false;"><i class="fa fa-hand-o-left "></i></a>';
       

        return Datatables::of($product)
                ->edit_column('name', '{{ucwords($name);}}')
                ->add_column('Action', $actionBar)
                ->make(true);
      
       
    }

    /* Function: anyRemove
        Description : To deleted selected coupon .

        _INPUT_ :

        1. remove_coupon_id
    */
    public function anyRemovefreeitem() 
    {
        if (Input::has('remove_coupon_id'))
        {
            $coupon_id = Input::get('remove_coupon_id');
            $coupon = Coupon::find($coupon_id);    
            $coupon->status = 2;

            if ($coupon->save())
            {
                $insert_audit = General::audit_trail('CouponController.php', 'Remove()', 'Delete Coupon', Session::get('username'), 'CMS');
                return Redirect::to('coupon')->with('success', 'Coupon(ID: '.$coupon_id.') has been deleted.');
            }
            else
            {
                return Redirect::to('coupon')->with('message', 'Delete failed. Data has not changed');
            }

        
            // if ($coupon->delete())
            // {
                
            //     return Redirect::to('coupon')->with('success', 'Coupon(ID: '.$coupon_id.') has been deleted.');
            // }
            // else
            // {
            //     return Redirect::to('coupon')->with('message', 'Delete failed. Data has not changed');
            // }
        }
        elseif (Input::has('remove_type_id'))
        {
            $details_id = Input::get('remove_type_id');
            $coupon_id = Input::get('couponID');
            $details = CouponType::where('related_id', '=', $details_id)->where('coupon_id', '=', $coupon_id)->first();       
        
            if ($details->delete())
            {
                $insert_audit = General::audit_trail('CouponController.php', 'Removefreeitem()', 'Delete Coupon Type', Session::get('username'), 'CMS');
                return Redirect::to('coupon/editfreecoupon/'.$coupon_id)->with('success', 'Deleted.');
            }
            else
            {
                return Redirect::to('coupon/editfreecoupon/'.$coupon_id)->with('message', 'Delete failed. Data has not changed');
            }
        }              
        
    }


    /* Function: anyListing
        Description : To listing all free coupon .

        
    */

    public function anyFreelistingitem()
    {
        $coupon = Coupon::select(array(
                'id', 
                'coupon_code',
                'name',
                'status',
                ))
                ->where('status', '!=', 2)
                ->where('is_free_item', '=', 1)
                ;

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="editfreecoupon/{{$id}}"><i class="fa fa-pencil"></i></a>';
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 9, 'AND'))
        {     
            $actionBar = $actionBar . ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id;}});"><i class="fa fa-remove"></i></a>';
        }
       

        return Datatables::of($coupon)
                
                ->edit_column('status', '<?php if ($status==1) echo "Active"; elseif ($status==0) echo "Inactive"; ?>')
                // ->edit_column('status', '{{ucwords($status);}}')
                ->add_column('Action', $actionBar)
                ->make(true);

        // return Datatables::of($coupon)
        //         ->edit_column('amount', '{{number_format($amount,2);}}')
        //         ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="coupon/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
        //         <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id;}});"><i class="fa fa-remove"></i></a>')
        //         ->make(true);
       
    }
    
    
    // Free Coupon Item End

    public function anyBulk()
    {
        return View::make(Config::get('constants.ADMIN_FOLDER').'.bulkcoupon_listing');
    }

    public function anyBulklisting()
    {
        $coupon = BulkCoupon::select(array(
                'id', 
                'prefix',
                'name',
                'amount',
                'status',
                'amount_type'
                ))
                ->where('status', '!=', 2);

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/coupon/bulkedit/{{$id}}"><i class="fa fa-pencil"></i></a>';
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 9, 'AND'))
        {     
            $actionBar = $actionBar . ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id;}});"><i class="fa fa-remove"></i></a>';
        }
       

        return Datatables::of($coupon)
                ->edit_column('amount', '<?php if ($amount_type=="Nett") echo Config::get("constants.CURRENCY").number_format($amount,2); else if ($amount_type=="%") echo number_format($amount,0)."%"; ?>')
                ->edit_column('status', '<?php if ($status==1) echo "Active"; elseif ($status==0) echo "Inactive"; ?>')
                // ->edit_column('status', '{{ucwords($status);}}')
                ->add_column('Action', $actionBar)
                ->make(true);

        // return Datatables::of($coupon)
        //         ->edit_column('amount', '{{number_format($amount,2);}}')
        //         ->add_column('Action', '<a class="btn btn-primary" title="" data-toggle="tooltip" href="coupon/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
        //         <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id;}});"><i class="fa fa-remove"></i></a>')
        //         ->make(true);
       
    }

    public function anyBulkadd()
    {
        // Bulk generate the unique code
        // 

        
        if (Input::has('add_check'))
        {
            

            $validator = Validator::make(
                Input::all(), 
                array(
                    'prefix'=>'required',
                    'code_length'=>'required|numeric',
                    'gquantity'=>'required|numeric', 
                    'amount'=>'required|numeric',
                    'min_purchase'=>'numeric',
                    'qty'=>'numeric',
                ), 
                array(
                    'prefix.required'=>'Please enter Prefix',
                    'code_length.required'=>'Please enter correct code length',
                    'gquantity.required'=>'Please enter Generate Quantity',
                    'amount.required'=>'Please enter correct Coupon Amount',
                )
            );

            Input::flash();

            if ($validator->passes()) 
            {
                $prefix = Input::get('prefix');
                $tempCheck = BulkCoupon::where('prefix', '=', $prefix)->first();

                if ($tempCheck != null)
                {
                    return Redirect::to('coupon/bulkadd')->with('message', 'Prefix ('.$prefix.') is already exists!');
                }

                $code_length = Input::get('code_length');
                $gquantity = Input::get('gquantity');

                $randomStrings = [];
                for ($i = 0; $i < $gquantity; $i++) {
                    do {
                        $randomString = $this->generateRandomString($code_length);
                    } while (in_array($randomString, $randomStrings));

                    array_push($randomStrings, $randomString);
                }

                DB::beginTransaction();
                $rs = BulkCoupon::bulkadd_coupon();

                foreach ($randomStrings as $randomString) {
                    Coupon::bulkadd_coupon($prefix.$randomString);
                }

                DB::commit();
                
                Input::flush();

                if ($rs == true)
                {
                    $insert_audit = General::audit_trail('CouponController.php', 'Add()', 'Add Coupon', Session::get('username'), 'CMS');
                    return Redirect::to('coupon/bulkedit/'.$rs)->with('success', 'Bulk Coupon(ID: '.$rs.') added successfully');
                }
                else
                {
                    return Redirect::to('coupon')->with('message', 'Error adding new coupon.');
                }   
            }
            else
            {
                return Redirect::to('coupon/bulkadd')->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
            }
            
        }
        else
        {
            $customers = DB::table('jocom_user')->where('active_status', '!=', '2')->select('username','full_name')->get();
            return View::make(Config::get('constants.ADMIN_FOLDER').'.bulkcoupon_add')->with('customers',$customers);

        }
    }

    function generateRandomString($length = 5) {
        $characters = '123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }

    public function anyBulkedit($id = null)
    {
        if (isset($id))
        {
            if (Input::has('id'))
            {
                 $validator = Validator::make(
                    Input::all(), 
                    array(
                        'amount'=>'required|numeric',
                        'min_purchase'=>'numeric',
                        'qty'=>'numeric',
                    ), 
                    array(
                        'amount.required'=>'Please enter correct Coupon Amount',
                    )
                );

                 Input::flash();           

                if ($validator->passes()) {

                    $rs = BulkCoupon::save_coupon();

                    Input::flush();

                    if ($rs == true)
                    {
                        $insert_audit = General::audit_trail('CouponController.php', 'Edit()', 'Edit Coupon', Session::get('username'), 'CMS');
                        return Redirect::to('coupon/bulkedit/'.$id)->with('success', 'Bulk Coupon(ID: '.$id.') updated successfully.');
                    }
                    else{
                        return Redirect::to('coupon/bulkedit/'.$id)->with('message', 'Bulk Coupon(ID: '.$id.') update failed.');
                    } 

                } else 
                {
                    return Redirect::to('coupon/bulkedit/'.$id)->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
                }


                
            }
            else
            {
                
                $editCoupon = BulkCoupon::find($id);

                if ($editCoupon->type == 'seller')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }
                    $couponlist = BulkCouponType::get_list($id);
                    $editSeller = Seller::select('id','username','company_name')->whereIn('id', $couponlist)->get();
                }
                else
                {
                    $editSeller = array();
                }

                if ($editCoupon->type == 'item')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }

                    $couponlist = BulkCouponType::get_list($id);
                    $editItem = Product::select('id', 'sku','name')->whereIn('id', $couponlist)->get();
                    // $editPItem = Package::select('id', 'sku','name')->whereIn('id', $couponlist)->get();

                    // $editItem = Product::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                    // $editPItem = Package::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                }
                else
                {
                    $editItem = array();
                    // $editPItem = array();
                }

                if ($editCoupon->type == 'package')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }

                    $couponlist = BulkCouponType::get_list($id);
                    // $editItem = Product::select('id', 'sku','name')->whereIn('id', $couponlist)->get();
                    $editPItem = Package::select('id', 'sku','name')->whereIn('id', $couponlist)->get();

                    // $editItem = Product::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                    // $editPItem = Package::whereIn('sku', $couponlist)->lists('id', 'sku','name');
                }
                else
                {
                    // $editItem = array();
                    $editPItem = array();
                }


                if ($editCoupon->type == 'customer')
                {                    
                    // $listtype = CouponType::select('related_id')->where('coupon_id', '=', $id)->get();
                    // $couponlist = array();

                    // foreach ($listtype as $templist)
                    // {
                    //     $couponlist[] = $templist->related_id;
                    // }
                    // if(sizeof($couponlist) == 0) 
                    // {
                    //     $couponlist[] = 0;
                    // }

                    $couponlist = BulkCouponType::get_list($id);
                    $editCustomer = Customer::select('id','username','full_name')->whereIn('id', $couponlist)->get();
                }
                else
                {
                    $editCustomer = array();
                }

                if ($editCoupon->type == 'category')
                {
                    $couponlist = BulkCouponType::get_list($id);
                    $editCategory = Category::select('id','category_name')->whereIn('id', $couponlist)->get();
                }
                else
                {
                    $editCategory = array();
                }
                
                // $editSeller = Seller::all()->lists('id','company_name');
                //$editItem = Product::all()->lists('sku','name');
                //$editPItem = Package::all()->lists('sku','name');
                $customers = DB::table('jocom_user')->where('active_status', '!=', '2')->select('username','full_name')->get();
                return View::make(Config::get('constants.ADMIN_FOLDER').'.bulkcoupon_edit')->with('display_coupon', $editCoupon)->with('display_seller', $editSeller)->with('display_item', $editItem)->with('display_package', $editPItem)->with('display_customer', $editCustomer)->with('display_category', $editCategory)->with('customers', $customers);
            }       
        }
        else
        {
            return Redirect::to('coupon')->with('message', 'No transaction is selected for edit.');
        }
        
    }

    public function anyBulkremove() 
    {
        if (Input::has('remove_coupon_id'))
        {
            $coupon_id = Input::get('remove_coupon_id');
            $coupon = BulkCoupon::find($coupon_id);    
            $coupon->status = 2;

            Coupon::where('coupon_code', 'LIKE', $coupon->prefix.'%')
                ->update(['status' => 2]);

            if ($coupon->save())
            {
                $insert_audit = General::audit_trail('CouponController.php', 'Remove()', 'Delete Coupon', Session::get('username'), 'CMS');
                return Redirect::to('coupon/bulk')->with('success', 'Coupon(ID: '.$coupon_id.') has been deleted.');
            }
            else
            {
                return Redirect::to('coupon/bulk')->with('message', 'Delete failed. Data has not changed');
            }

        
            // if ($coupon->delete())
            // {
                
            //     return Redirect::to('coupon')->with('success', 'Coupon(ID: '.$coupon_id.') has been deleted.');
            // }
            // else
            // {
            //     return Redirect::to('coupon')->with('message', 'Delete failed. Data has not changed');
            // }
        }
        elseif (Input::has('remove_type_id'))
        {
            $details_id = Input::get('remove_type_id');
            $coupon_id = Input::get('couponID');
            $details = BulkCouponType::where('related_id', '=', $details_id)->where('coupon_id', '=', $coupon_id)->first();
            $coupons = Coupon::where('coupon_code', 'LIKE', $details->prefix.'%')->select('id')->get();
            $coupon_ids = [];
            foreach ($coupons as $coupon) {
                array_push($coupon_ids, $coupon->id);
            }
            CouponType::whereIn('coupon_id', $coupon_ids)->where('related_id', '=', $details_id)->delete();

        
            if ($details->delete())
            {
                $insert_audit = General::audit_trail('CouponController.php', 'Remove()', 'Delete Coupon Type', Session::get('username'), 'CMS');
                return Redirect::to('coupon/bulkedit/'.$coupon_id)->with('success', 'Deleted.');
            }
            else
            {
                return Redirect::to('coupon/bulkedit/'.$coupon_id)->with('message', 'Delete failed. Data has not changed');
            }
        }              
        
    }
    public function anyAddcoupon(){
        $coupon = Coupon::select(array(
                'id', 
                'coupon_code',
                'name',
                'amount',
                'status',
                'amount_type'
                ))
                ->where('status', '=', 1)
                ->get();

        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_static_add')->with('coupon',$coupon);
    }

public function anyAddstaticcoupon(){

        $validator = Validator::make(Input::all(), CouponStatic::$rules);

             Input::flash();
            if($validator->passes()){

                $coupon=new CouponStatic();
                $coupon->coupon_code=Input::get("coupon_code");
                $coupon->coupon_amount=Input::get("amount")." OFF";
                $coupon->description=Input::get("description");
                $coupon->from_date=Input::get("valid_from");
                $coupon->to_date=Input::get("valid_to");
                $coupon->coupon_amount_type=Input::get("amount_type");
                $coupon->coupon_id=Input::get("coupon_id");
                $coupon->status=Input::get("status");
                $coupon->created_by=Session::get("username");
            
                if($coupon->save()){
                   return Redirect::to('coupon/couponstatics/')->with('success', 'Coupon Created successfully');
                }
                else{
                  return Redirect::to('coupon/addcoupon')->with('message', 'Something went wrong! Please Try Again');  
                }

            }else
            {
                return Redirect::to('coupon/addcoupon')->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
            }
    }

    public function anyCouponstatics(){
    
        return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_static_listing');

    }
    public function anyListingstatic()
    {
        $coupon = CouponStatic::select(array(
                'id', 
                'coupon_code',
                'from_date',
                'to_date',
                'coupon_amount',
                'coupon_amount_type',
                'status',
                ))
                ->where('status', '!=',2);

        $actionBar = '<a class="btn btn-primary" title="" data-toggle="tooltip" href="edits/{{$id}}"><i class="fa fa-pencil"></i></a>';
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 4, 9, 'AND'))
        {     
            $actionBar = $actionBar . ' <a class="btn btn-danger" title="" data-toggle="tooltip" href="#" onclick="delete_coupon({{$id;}});"><i class="fa fa-remove"></i></a>';
        }
       

        return Datatables::of($coupon)
        
        
                ->edit_column('amount', '<?php echo $coupon_amount; ?>')
                ->edit_column('status',function ($coupon) {
                if ($coupon->status==1){
                    return '<button type="button" class="btn btn-success btn-sm">Active</button>';
                }
                elseif ($coupon->status==0){
                return '<button type="button" class="btn btn-danger btn-sm">Inactive</button>';
                }
                })
                // ->edit_column('status', '{{ucwords($status);}}')
                ->add_column('Action', $actionBar)
                ->make(true);
    
       
    }

    public function anyStaticremove() 
    {
        if (Input::has('remove_coupon_id'))
        {
            $coupon_id = Input::get('remove_coupon_id');
            $coupon = CouponStatic::find($coupon_id);    
            $coupon->status = 2;

            if ($coupon->save())
            {
                $insert_audit = General::audit_trail('CouponController.php', 'Remove()', 'Delete Static Coupon', Session::get('username'), 'CMS');
                return Redirect::to('coupon/couponstatics')->with('success', 'Coupon(ID: '.$coupon_id.') has been deleted.');
            }
            else
            {
                return Redirect::to('coupon')->with('message', 'Delete failed. Data has not changed');
            }
        }
             
        
    }

    public function anyEdits($id = null)
    {
        if (isset($id))
        {
            if (Input::has('id'))
            {

                 $validator = Validator::make(Input::all(), CouponStatic::$editrule);

                 Input::flash();           

                if ($validator->passes()) {
                
                $coupon = CouponStatic::find($id);
                $coupon->coupon_code=Input::get("coupon_code");
                if($coupon->coupon_amount==Input::get("amount")){
                $coupon->coupon_amount=Input::get("amount");
                }else{
                $coupon->coupon_amount=Input::get("amount")." OFF";
                }
                $coupon->from_date=Input::get("valid_from");
                $coupon->to_date=Input::get("valid_to");
                $coupon->description=Input::get("description");
                $coupon->coupon_amount_type=Input::get("amount_type");
                $coupon->coupon_id=Input::get("coupon_id");
                $coupon->status=Input::get("status");
                $coupon->updated_by=Session::get("username");

                    if ($coupon->save())
                    {
                        $insert_audit = General::audit_trail('CouponController.php', 'Edits()', 'Edit Static Coupon', Session::get('username'), 'CMS');
                        return Redirect::to('coupon/couponstatics')->with('success', 'Coupon(ID: '.$id.') updated successfully.');
                    }
                    else{
                        return Redirect::to('coupon/edits/'.$id)->with('message', 'Coupon(ID: '.$id.') update failed.');
                    } 

                } else 
                {
                    return Redirect::to('coupon/edits/'.$id)->with('message', 'The highlighted field is required')->withErrors($validator)->withInput();
                }


                
            }
            else
            {
                $editCoupon = CouponStatic::find($id);
              
                return View::make(Config::get('constants.ADMIN_FOLDER').'.coupon_static_edit')->with('display_coupon', $editCoupon);
            }       
        }
        else
        {
            return Redirect::to('coupon')->with('message', 'No Coupon is selected for edit.');
        }
        
    }

    public function anyAjaxcoupon()
    {
        return View::make('admin.ajaxcoupon');
    }


    public function anyCouponsajax()
    {
        $coupon = DB::table('jocom_coupon')
            ->select('id', 'coupon_code', 'name', 'amount','amount_type','status');

        return Datatables::of($coupon)
          ->edit_column('amount', '<?php if ($amount_type=="Nett") echo Config::get("constants.CURRENCY").number_format($amount,2); else if ($amount_type=="%") echo number_format($amount,0)."%"; ?>')
         ->edit_column('status', '<?php if ($status==1) echo "Active"; elseif ($status==0) echo "Inactive"; ?>')
        ->add_column('Action', '<a id="selectCoup" class="btn btn-primary" title="" >Select</a>')
            ->make();
    }
    
    
    public function anyCamping(){
		return View::make(Config::get('constants.ADMIN_FOLDER') . '.coupon_camping_list');
	}

	public function anyCampinglist(){
		return Datatables::of(CouponCamping::select('id', 'start_at', 'end_at', 'name', 'coupon_data', 'status', 'created_at', 'created_by', 'updated_at', 'updated_by')->where('status', '!=', 2))
			->edit_column('coupon_data', function($row){
				return implode('<br>', array_column(json_decode($row->coupon_data, true), 'shortname'));
			})
			->edit_column('status', function($row){
				return ((int)$row->status == 1 ? 'Active' : 'Inactive');
			})
			->add_column('duration', function($row){
				return '<p>Start: ' . $row->start_at . '</p><p>End: ' . $row->end_at . '</p>';
			})
			->add_column('last', function($row){
				return '<p>Start: ' . $row->created_at . '<br>' . $row->created_by . '</p><p>End: ' . $row->updated_at . '<br>' . $row->updated_by . '</p>';
			})
			->add_column('action', self::ActionBAR('/coupon/campingedit/{{$id}}', false, true))
			->remove_column('start_at')->remove_column('end_at')
			->remove_column('created_at')->remove_column('created_by')->remove_column('updated_at')->remove_column('updated_by')
			->make();
	}

	public function anyCampingedit($id){
		$camp = [];
		if((int)$id !== 0){
			$camp = CouponCamping::find($id)->toArray();
			if(!$camp) return Redirect::to('/coupon/camping')->with('message', 'No Coupon Camping found.');
			if($camp['coupon_data']){
				$camp['coupon_data'] = json_decode($camp['coupon_data'], true);
				foreach ($camp['coupon_data'] as $k => $v) {
					$e = [];
					self::editCoupon_data_callback($v['type'], $v['related_id'], $e);
					$camp['coupon_data'][$k]['coupon_type_data'] = $e;
				}
			}
		}
		return View::make(Config::get('constants.ADMIN_FOLDER') . '.coupon_camping_edit')->with('camping', $camp);
	}

	public function anyCampingupdate($id){
		if((int)$id !== (int)Input::get('id')) return Redirect::to('coupon/campingedit/'. $id)->with('message', 'Invalid update');
		if($id === "0"){
			$cc = CouponCamping::create(['name' => Input::get('name'), 'format' => Input::get('format'), 'start_at' => Input::get('start_at'), 'end_at' => Input::get('end_at'), 'status' => Input::get('status'), 'coupon_data' => '[]', 'created_by' => Session::get('username'), 'updated_by' => Session::get('username')]);
			$id = $cc->id;
		}else{
			$cc = CouponCamping::where('id', '=', $id);
			$cc->update(['name' => Input::get('name'), 'format' => Input::get('format'), 'start_at' => Input::get('start_at'), 'end_at' => Input::get('end_at'), 'status' => Input::get('status'), 'updated_by' => Session::get('username')]);
		}

		$coupon_data = [];
		$data_format = [
			"shortname"			=> "",
			"dis_name"			=> "",
			"dis_desc"			=> "",
			"dis_color"			=> "",
			"dis_bg"			=> "",
			"name"				=> "",
			"amount"			=> 0, 
			"amount_type"		=> "Nett", 
			"min_purchase"		=> null, 
			"max_purchase"		=> null, 
			"valid_from"		=> date('Y-m-d h:i:s'), 
			"valid_to"			=> date('Y-m-d h:i:s'), 
			"type"				=> "all", 
			"qty"				=> 0, 
			"q_limit"			=> "No", 
			"cqty"				=> 0, 
			"c_limit"			=> "No", 
			"free_delivery"		=> 0, 
			"free_process"		=> 0, 
			"delivery_discount"	=> 0, 
			"boost_payment"		=> 0, 
			"razerpay_payment"	=> 0,
			"tng_payment"		=> 0, 
			"is_free_item"		=> 0, 
			"is_bank_code"		=> 0, 
			"is_seller"			=> 0, 
			"is_vvip"			=> 0,
			"is_jpoint"			=> 1,
			"status"			=> 1, 
			"related_id"		=> []
		];

		$namelist = [];
		foreach(Input::get('cp_shortname') as $idx => $v){
			if(in_array($v, $namelist)){
				return Redirect::to('coupon/campingedit/'. $id)->with('message', 'Short name must be unique.');
				break;
			}

			if(!in_array(Input::get('cp_amount_type')[$idx], ['%', 'Nett'])){
				return Redirect::to('coupon/campingedit/'. $id)->with('message', 'Invalid amount type.');
				break;
			}

			$temp = $data_format;
			$namelist[] = $v;

			$temp["shortname"] = $v;
			if(Input::get('cp_name')[$idx])					$temp["name"]				= Input::get('cp_name')[$idx];
			if(Input::get('cp_dis_name')[$idx])				$temp["dis_name"]			= Input::get('cp_dis_name')[$idx];
			if(Input::get('cp_dis_desc')[$idx])				$temp["dis_desc"]			= Input::get('cp_dis_desc')[$idx];
			if(Input::get('cp_dis_color')[$idx])			$temp["dis_color"]			= Input::get('cp_dis_color')[$idx];
			if(Input::get('cp_dis_bg')[$idx])				$temp["dis_bg"]				= Input::get('cp_dis_bg')[$idx];
			if(Input::get('cp_amount')[$idx])				$temp["amount"]				= Input::get('cp_amount')[$idx];
			if(Input::get('cp_amount_type')[$idx])			$temp["amount_type"]		= Input::get('cp_amount_type')[$idx];
			if(Input::get('cp_min_purchase')[$idx])			$temp["min_purchase"]		= Input::get('cp_min_purchase')[$idx];
			if(Input::get('cp_max_purchase')[$idx])			$temp["max_purchase"]		= Input::get('cp_max_purchase')[$idx];
			if(Input::get('cp_valid_from')[$idx])			$temp["valid_from"]			= Input::get('cp_valid_from')[$idx];
			if(Input::get('cp_valid_to')[$idx])				$temp["valid_to"]			= Input::get('cp_valid_to')[$idx];
			if(Input::get('cp_type')[$idx])					$temp["type"]				= Input::get('cp_type')[$idx];
			if(Input::get('cp_qty')[$idx])					$temp["qty"]				= Input::get('cp_qty')[$idx];
			if(Input::get('cp_q_limit')[$idx])				$temp["q_limit"]			= Input::get('cp_q_limit')[$idx];
			if(Input::get('cp_cqty')[$idx])					$temp["cqty"]				= Input::get('cp_cqty')[$idx];
			if(Input::get('cp_c_limit')[$idx])				$temp["c_limit"]			= Input::get('cp_c_limit')[$idx];
			if(Input::get('cp_free_delivery')[$idx])		$temp["free_delivery"]		= Input::get('cp_free_delivery')[$idx];
			if(Input::get('cp_free_process')[$idx])			$temp["free_process"]		= Input::get('cp_free_process')[$idx];
			if(Input::get('cp_delivery_discount')[$idx])	$temp["delivery_discount"]	= Input::get('cp_delivery_discount')[$idx];
			if(Input::get('cp_boost_payment')[$idx])		$temp["boost_payment"]		= Input::get('cp_boost_payment')[$idx];
			if(Input::get('cp_razerpay_payment')[$idx])		$temp["razerpay_payment"]	= Input::get('cp_razerpay_payment')[$idx];
			if(Input::get('cp_tng_payment')[$idx])			$temp["tng_payment"]		= Input::get('cp_tng_payment')[$idx];
			if(Input::get('cp_is_jpoint')[$idx])			$temp["is_jpoint"]			= Input::get('cp_is_jpoint')[$idx];
			if(Input::get('cp_status')[$idx])				$temp["status"]				= Input::get('cp_status')[$idx];

			if(Input::get('cp_type')[$idx] !== 'all'){
				$r_id = [];
				if(Input::get('type_related')[$idx]){
					$t = explode('|', Input::get('type_related')[$idx])[0];
					if(Input::get('cp_type')[$idx] === $t){
						$r_id = explode(',', explode('|', Input::get('type_related')[$idx])[1]);
						$r_id[] = Input::get('related_' . Input::get('cp_type')[$idx])[$idx];
					}
				}
				$r_id[] = Input::get('related_' . Input::get('cp_type')[$idx])[$idx];
				$temp["related_id"] = array_filter(array_unique($r_id), function($r){ return $r; });
			}
			$coupon_data[] = $temp;
		}
		$cc->update(['coupon_data' => json_encode($coupon_data)]);
		return Redirect::to('coupon/camping')->with('success', 'Update successfully.');
	}

	public function anyCampingdelete(){
		if (Input::has('remove_coupon_id')) {
			$camping_id = Input::get('remove_coupon_id');
			$camping = CouponCamping::find($camping_id);    
			$camping->status = 2;

			if ($camping->save()) {
				$insert_audit = General::audit_trail('CouponController.php', 'Remove()', 'Delete Coupon Camping', Session::get('username'), 'CMS');
				return Redirect::to('coupon/camping')->with('success', 'Coupon Camping(ID: ' . $camping_id . ') has been deleted.');
			} else {
				return Redirect::to('coupon/camping')->with('message', 'Delete failed. Data has not changed');
			}
		}
	}

    
    
    
}
?>