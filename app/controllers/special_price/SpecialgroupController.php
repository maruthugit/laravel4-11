<?php

class SpecialgroupController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
    }
 

    /**
     * Display a listing of the customers on datatable.
     *
     * @return Response
     */
    public function anyGroups() {     
        $groups = SpecialPrice::select('jocom_sp_group.id', 'jocom_sp_group.name', 'jocom_seller.company_name', 'jocom_sp_group.created_at')
                                ->leftJoin('jocom_seller', 'jocom_seller.id', '=', 'jocom_sp_group.seller_id')
                                ->where('jocom_sp_group.status', '=', '1');
                        
        return Datatables::of($groups)
                ->add_column('Action', '
                        <a class="btn btn-primary" title="" data-toggle="tooltip" href="/special_price/group/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                        @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 8, 9, \'AND\'))
                        <a id="deleteGroup" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$name}}" href="/special_price/group/delete/{{$id}}"><i class="fa fa-times"></i></a>
                        @endif
                    ')
                ->make();
    }

    /**
     * Display a listing of the special price customers.
     *
     * @return Response
     */
    public function anyIndex()
    {
       // $groups  = SpecialPrice::all();
       return View::make('special_price.group.index');  //, ['groups' => $groups]);
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return Response
     */
    public function anyCreate()
    {   
        $sellers    = SpecialPrice::get_sellers();
        
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND'))
            return View::make('special_price.group.create')->with('sellers', $sellers); 
        else
            return View::make('home.denied', array('module' => 'Special Pricing > Add Special Price Customer'));   
    }

    public function anyStore()
    {
        $group      = new SpecialPrice;
        $inputs     = Input::all();

        // var_dump($inputs);
        // exit();
        $validator = Validator::make(Input::all(), SpecialPrice::$rules);

        if ($validator->passes()) {
            $group->name        = Input::get('group_name');
//            $group->seller_id   = Input::get('seller');
            $group->min_purchase= Input::get('min_purchase');
            $group->min_qty_purchase = Input::get('min_qty_purchase');
            $group->is_free_delivery_min_qty = Input::get('is_free_delivery_min_qty');
            $group->created_by  = Session::get('user_id');
            $group->created_at  = date('Y-m-d H:i:s');

            if($group->save()) {
                return Redirect::to('/special_price/group/index');
            }
        }
        else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }
       
    }

    public function anyEdit($id)
    {
        $group      = SpecialPrice::find($id);
//        $sellers    = SpecialPrice::get_sellers();
        $customers  = SpecialPrice::get_group_customer($id);

        return View::make('special_price.group.edit')->with(array(
                            'group'     => $group,
//                            'sellers'   => $sellers,
                            'customers' => $customers
                        ));
    }

    public function anyUpdate($id)
    {
        $rules      = [
            'group_name'    => 'required',
//            'seller'        => 'required',
            'cid'           => 'required',
            'min_purchase'  => 'required|numeric',
        ];

        $messages['group_name.required']  = "The Group Name is required.";
//        $messages['seller.required']  = "The Seller is required.";
        $messages['cid.required']  = "The customer is required.";
        $messages['min_purchase.required']  = "The Minimum Purchase is required.";

        $inputs     = Input::all();
        $group      = array();
        $sellers    = SpecialPrice::get_sellers();
        $customers  = SpecialPrice::get_group_customer($id);
        
        $validator  = Validator::make(Input::all(), $rules, $messages);

        // var_dump($inputs);
        // exit();
        if($validator->passes()) {

            $group['name']      = Input::get('group_name');
//            $group['seller_id'] = Input::get('seller');
            $group['min_purchase'] = Input::get('min_purchase');
            $group['min_qty_purchase'] = Input::get('min_qty_purchase');
            $group['is_free_delivery_min_qty'] = Input::get('is_free_delivery_min_qty');
            
            SpecialPrice::update_group($id, $group);

            if(Input::has('cid')) {
                DB::table('jocom_sp_customer_group')->where('sp_group_id', '=', $id)->delete();

                foreach (Input::get('cid') as $cid) {
                    $sp_id  = 0;
                    $sp     = SpecialPrice::get_cust_sp_id($cid);

                    if (count($sp) > 0) {
                        $sp_id              = $sp->id;
                        $arr_seller = SpecialPrice::get_cust_group($sp_id);

                        foreach ($arr_seller as $s) {
                            if ($s->id == Input::get('seller')) {
                                $group      = SpecialPrice::find($id);
                                $customers  = SpecialPrice::get_group_customer($id);

                                $err_msg    = "The customer has already added with the seller.";
                                return Redirect::back()
                                            ->withInput()
                                            ->withErrors($err_msg)->withInput();
                            }
                        }

                    } 
                    if ($sp_id == 0) {
//                        echo "<br>NEW CUSTOMER! - ".$cid;
                        $new_cust               = array();
                        $new_cust['user_id']    = $cid;
                        // $cust['status']     = Input::get('status');
                        $new_cust['created_by'] = Session::get('user_id');
                        $new_cust['created_at'] = date('Y-m-d H:i:s');

                        $sp_id = DB::table('jocom_sp_customer')->insertGetId($new_cust);
                    }

                    $cust               = array();
                    $cust['sp_cust_id'] = $sp_id;
                    $cust['sp_group_id']= $id;

                    DB::table('jocom_sp_customer_group')->insert($cust);
                    // echo "<br>Got CID: ".$cid;
                }
            }

            $group      = SpecialPrice::find($id);
            $customers  = SpecialPrice::get_group_customer($id);

            Session::flash('message', 'Settings updated successfully.');
            // else {
            //     echo "Failed to update group!";
            // }
            return View::make('special_price.group.edit')->with(array(
                            'group'     => $group,
//                            'sellers'   => $sellers,
                            'customers' => $customers
                        ));
        }
        else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }
    }
}

?>