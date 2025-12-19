<?php

class CustomerController extends BaseController {

    public function __construct()
    {

        $this->beforeFilter('auth');
//        $this->afterFilter('no-cache');
        // echo "<br>check authentication ";
    }

    /**
     * Display a listing of the customer.
     *
     * @return Response
     */
    public function anyIndex()
    {
        // $cust   = Customer::all();
        $limit = ini_get('memory_limit');

        // die($limit);
        return View::make('customer.index', ['customers' => $cust]);
    }

    /**
    * Display a listing of the customers on datatable.
    *
    * @return Response
    */
    public function anyCustomers()
    {
    
        
        $customers = Customer::select('id', 'username', 'full_name', 'ic_no', 'email', 'mobile_no', 'active_status',DB::raw('(
                CASE WHEN created_by = "api_register" THEN "Jocom App" 
                WHEN created_by = "ecom_register" THEN "eCommunity App" 
                WHEN created_by = "api_fb_register" THEN "Facebook" 
                WHEN created_by = "api_google_register" THEN "Google" 
                ELSE "CMS" END) as created_by'))
            ->where('active_status', '!=', '2');
            
        

        $username   = Input::get('username');
        $fullName   = Input::get('full_name');
        $icPassport = Input::get('ic_passport');
        $mobileNo   = Input::get('mobile_no');
        $status     = Input::get('status');

        if (isset($username) && ! empty($username)) {
            $customers = $customers->where('username', 'like', "%{$username}%");
        }

        if (isset($fullName) && ! empty($fullName)) {
            $customers = $customers->where('full_name', 'like', "%{$fullName}%");
        }

        if (isset($icPassport) && ! empty($icPassport)) {
            $customers = $customers->where('ic_no', 'like', "%{$icPassport}%");
        }

        if (isset($mobileNo) && ! empty($mobileNo)) {
            $customers = $customers->where('mobile_no', 'like', "%{$mobileNo}%");
        }

        if (isset($status) && ! empty($status) && $status != 'any') {
            switch ($status) {
                case 'active':
                    $customers = $customers->where('JU.active_status', '=', 1);
                    break;
                case 'inactive':
                    $customers = $customers->where('JU.active_status', '=', 0);
                    break;
            }
        }
       
        return Datatables::of($customers)
            ->edit_column('active_status',
            '@if($active_status == 1)
                    <p class="text-success">Active</p>
                @else
                    <p class="text-danger">Inactive</p>
                @endif
            ')
            ->add_column('Action', 
            '<a class="btn btn-primary" title="" data-toggle="tooltip" href="/customer/editcustcategory/{{$id}}"><i class="fa fa-flag"></i></a>
                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/customer/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 8, 9, \'AND\'))
                    <a id="deleteCust" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$username}}" href="/customer/delete/{{$id}}"><i class="fa fa-times"></i></a>
                @endif
            ')
            ->make();
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return Response
     */
    public function anyCreate($type = "",$order_id = "")
    {
        $countries    = Customer::GetCountryList();
        $types        = array('public', 'corporate');
        $agents       = Agent::where('active_status', '=', 1)->get();
        $agentsSelect = [];
        $customerdata = array();

        foreach ($agents as $agent) {
            $agentsSelect[$agent->id] = $agent->agent_code;
        }

        if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND')) {
            
            if(($type == 1) && ($order_id != "")) {
               // Get customer information from 11Street Order
               $ElevenStreetOrder = ElevenStreetOrder::find($order_id);
               $ElevenStreetOrderDetails = ElevenStreetOrderDetails::getByOrderID($order_id)->first();
            
            
               if(count($ElevenStreetOrderDetails) > 0 ){
                   $collection = json_decode($ElevenStreetOrderDetails->api_result_return);
                
                   $customerdata = array(
                       "first_name" => $collection->ordNm,
                       "last_name" => "",
                       "mobile_no" => $collection->ordPrtblTel,
                       "email" => $collection->ordId,
                       "street_address_1" => $collection->rcvrDtlsAddr,
                       "street_address_2" => $collection->rcvrBaseAddr,
                       "postcode" => $collection->rcvrMailNo,
                   );
                   
               }
            }
            
            if(($type == 3) && ($order_id != "")) {
               // Get customer information from 11Street Order
               $Qoo10Order = QootenOrder::find($order_id);
               $Qoo10OrderDetails = QootenOrderDetails::getByOrderID($order_id)->first();
            
            
               if(count($Qoo10OrderDetails) > 0 ){
                   $collection = json_decode($Qoo10OrderDetails->api_result_return);
                
                   $customerdata = array(
                       "first_name" => $collection->buyer,
                       "last_name" => "",
                       "mobile_no" => $collection->buyerMobile,
                       "email" => $collection->buyerEmail,
                       "street_address_1" => $collection->Addr1,
                       "street_address_2" => $collection->Addr2,
                       "postcode" => $collection->zipCode,
                   );
                   
               }
            }
            
            if(($type == 4) && ($order_id != "")) {
               // Get customer information from Shopee Order
               $ShopeeOrder = ShopeeOrder::find($order_id);
               $ShopeeOrderDetails = ShopeeOrderDetails::getByOrderID($order_id)->first();
            
            
               if(count($ShopeeOrderDetails) > 0 ){
                   $collection = json_decode($ShopeeOrderDetails->api_result_return);
                
                   $customerdata = array(
                       "first_name" => $collection->name,
                       "last_name" => "",
                       "mobile_no" => $collection->phone,
                       "email" => "",
                       "street_address_1" => $collection->full_address,
                       "street_address_2" => "",
                       "postcode" => $collection->zipcode,
                   );
                   
               }
            }
            
            if(($type == 5) && ($order_id != "")) {
                // Get customer information from PGMall Order
                $PGMallOrder = PGMallOrder::find($order_id);
                $PGMallOrderDetails = PGMallOrderDetails::getByOrderID($order_id)->first();
             
             
                if(count($PGMallOrderDetails) > 0 ){
                    $collection = json_decode($PGMallOrderDetails->api_result_return);
                 
                    $customerdata = array(
                        "first_name" => $collection->name,
                        "last_name" => "",
                        "mobile_no" => $collection->phone,
                        "email" => $collection->email,
                        "street_address_1" => $collection->full_address,
                        "street_address_2" => "",
                        "postcode" => $collection->shipping_postcode,
                    );
                }
            }
            
            return View::make('customer.create')
                    ->with('countries', $countries)
                    ->with('types', $types)
                    ->with('agents', $agentsSelect)
                    ->with('customerInfo',$customerdata);
        }
        else
            return View::make('home.denied', array('module' => 'Customers > Add Customer'));
    }

     /**
     * Store a newly created customer in storage.
     *
     * @return Response
     */
    public function anyStore()
    {
        
        
        $cust_input = Input::all();
        
        $cust = new Customer;
        
        $validator = Validator::make(Input::all(), Customer::$rules);

        if ($validator->passes()) {
            
            // Begin Transaction 
            DB::beginTransaction();
            
            if (Input::get('username') != "") $cust->username     = Input::get('username');
            // $cust->full_name    = Input::get('full_name');
            if (Input::get('firstname') != "")  $cust->firstname    = Input::get('firstname');
            if (Input::get('lastname') != "")   $cust->lastname     = Input::get('lastname');
            if (Input::get('firstname') != "" || Input::get('lastname') != "")
                                                $cust->full_name    = Input::get('firstname') ." ". Input::get('lastname');
            if (Input::get('email') != "")      $cust->email        = Input::get('email');
            if (Input::get('password') != "")   $cust->password     = Hash::make(Input::get('password'));
            if (Input::get('ic_passport') != "")$cust->ic_no        = Input::get('ic_passport');
//            $cust->home_num     = Input::get('home_no');
            if (Input::get('address_1') != "")  $cust->address1     = Input::get('address_1');
            if (Input::get('address_2') != "")  $cust->address2     = Input::get('address_2');
            if (Input::get('postcode') != "")   $cust->postcode     = Input::get('postcode');
            if (Input::get('state_id') != "")   $cust->state_id     = Input::get('state_id');
            if (Input::get('state') != "")      $cust->state        = Input::get('state');
            if (Input::get('city') != "")       $cust->city         = Input::get('city');
            if (Input::get('dob') != "")        $cust->dob          = Input::get('dob');
            if (Input::get('mobile_no') != "")  $cust->mobile_no    = Input::get('mobile_no');
            if (Input::get('type') != "")       $cust->type         = Input::get('type');


            if (Input::get('country_id') != ""){
                
                // Find country name 
                $Country = Country::find(Input::get('country_id'));
                $cust->country   = $Country->name;
                $cust->country_id   = Input::get('country_id');
                
            }

            if (Input::has('agent_id')) {
                $cust->agent_id = Input::get('agent_id');
            }

            $cust->timestamps   = false;
            $cust->created_by   = Session::get('username');
            $cust->created_date = date("Y-m-d H:i:s");
            // Set user account status as active account
            $cust->active_status = 1 ; // Active
            
            $SaveResponse = $cust->save();

            if($SaveResponse)
            {
                // Commit Transaction
                DB::commit();
                
                $insert_audit = General::audit_trail('CustomerController.php', 'Store()', 'Add Customer', Session::get('username'), 'CMS');
                if (Input::has('bcard'))  $update = BcardM::update_card(Input::get('bcard'), trim(Input::get('username')));
                return Redirect::to('/customer/index');
            }else{

                // Rollback Transaction 
                DB::rollback();
            }



        } else {

            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }

    }

    /**
     * Show the form for editing the specified customer.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id)
    {
        $customer   = Customer::find($id);
        $card       = BcardM::where('username', '=', $customer->username)->first();
        $countries  = Customer::getCountryList();
        $states     = Customer::getStateList($customer->country_id);
        $cities     = Customer::getCityList($customer->state_id);
        $types      = array('public', 'corporate');
        $add        = DB::table('jocom_fav_address as a')
                        ->select('a.*', 'b.name as country_name', 'c.name as state_name', 'd.name as city_name')
                        ->leftJoin('jocom_countries as b', 'b.id', '=', 'a.delivercountry')
                        ->leftJoin('jocom_country_states as c', 'c.id', '=', 'a.state')
                        ->leftJoin('jocom_cities as d', 'd.id', '=', 'a.city')
                        ->where('a.username', '=', $customer->username)
                        ->get();

        $agents       = Agent::where('active_status', '=', 1)->get();
        $agentsSelect = [];

        foreach ($agents as $agent) {
            $agentsSelect[$agent->id] = $agent->agent_code;
        }

        // $queries = DB::getQueryLog();
        // $last_query         = end($queries);
        // $tempquery          = str_replace(['%', '?'], ['%%', '%s'], $last_query['query']);
        // $query['statement'] = vsprintf($tempquery, $last_query['bindings']);
        // var_dump($query['statement']);exit;

        
        $add2       = FavouriteAddress::where('username', '=', $customer->username)->get();
        $favaddr    = $add2->lists('delivername','id');

        // $favaddr    = FavouriteAddress::where('username', '=', $customer->username)->lists('delivername','id');

        return View::make('customer.edit')->with(array(
                                                'cust'      => $customer,
                                                'card'      => $card,
                                                'countries' => $countries,
                                                'states'    => $states,
                                                'cities'    => $cities,
                                                'types'     => $types,
                                                'add'       => $add,
                                                'favaddr'   => $favaddr,
                                                'agents'    => $agentsSelect,
        ));
    }

    /**
     * Show the form for editing the specified customer category.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEditcustcategory($id)
    {
        $customer   = Customer::find($id);
        $category   = CustomerCategory::where('username', '=', $customer->username)->get();

        return View::make('customer.editcustcategory')
                ->with(array('cust'=> $customer, 'cat'=> $category
                ));
    }

    /**
     * Update the specified customer in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id)
    {
        $arr_input_all  = Input::all();
        // $cust           = new Customer;
        $arr_validate   = array();
        $arr_input      = array();
        $arr_validate   = Customer::GetUpdateRules($arr_input_all, $id);
        $arr_input      = Customer::GetUpdateInputs($arr_input_all);

        $validator      = Validator::make($arr_input, $arr_validate);

//      echo "<br>- - - - - - [arr_input] <br>";
//        var_dump($arr_input);
//
//        echo "<br>- - - - - - [arr_validate] <br>";
//        var_dump($arr_validate);

        $arr_udata  = Customer::GetUpdateDbDetails($arr_input);

//      echo "<br><br>- - - - - [arr_udata]<br>";
//        var_dump($arr_udata);

        if ($validator->passes()) {

            if(Customer::UpdateCustomer($id, $arr_udata))
            {
                $insert_audit = General::audit_trail('CustomerController.php', 'update()', 'Edit Customer', Session::get('username'), 'CMS');

                if (Input::has('bcard') OR Input::get('bcard') == "")  $update = BcardM::update_card(Input::get('bcard'), trim(Input::get('username')));

                return Redirect::to('customer/index');
            }
            else
            {
                echo "<br>Failed to update customer details!";
            }

        }else {
            // echo "<br> ERROR records updated! ";
            return Redirect::back()
                        ->withInput()
                        ->withErrors($validator)->withInput();
        }
    }

    /**
     * Update the specified customer catgeory in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdatecustcategory($id)
    {
        $customer   = Customer::find($id);
        $category = CustomerCategory::where('username', '=', $customer->username);

        $donedel = $category->delete();

        if (Input::has('product_category'))
        {
            $cat_list = Input::get('product_category');

            foreach ($cat_list as $key => $value)
            {
                $newcat = new CustomerCategory;
                $newcat->username = $customer->username;
                $newcat->category_id = $cat_list[$key];
                $newcat->save();

                $insert_audit = General::audit_trail('CustomerController.php', 'updatecustcategory()', 'Edit Customer Category', Session::get('username'), 'CMS');
            }
        }
        return Redirect::to('customer/editcustcategory/'.$id)->with('success', 'Customer(ID: '.$id.') updated successfully.');

    }

    /**
     * Remove the specified customer from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $user = Customer::find($id);
        // Customer::destroy($id);
        $user->active_status = '0';
        $user->timestamps = false;
        $user->save();

        $insert_audit = General::audit_trail('CustomerController.php', 'delete()', 'Delete Customer', Session::get('username'), 'CMS');

        return Redirect::to('/customer/index');
    }

    public function anyStates($id)
    {
        // Log::info(Input::all());
        $country_id = Input::get('country_id');
        $cities     = Customer::getStateList($id);
        return Response::json($states);
    }


}
?>
