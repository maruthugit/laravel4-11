<?php

class SpecialcustomerController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
    }
 
    /**
     * Display a listing of the products resource.
     *
     * @return Response
     */
    public function anySellersajax() {     
        $sellers = DB::table('jocom_seller')
                        ->select(array('id', 'company_name'))
                        ->where('jocom_products.status', '=', '1');
                                    
        return Datatables::of($sellers)
                                    ->add_column('Action', '<a id="selectCust" class="btn btn-primary" title="" href="{{$id}}">Select</a>')
                                    ->make();
    }

    /**
     * Display a listing of the customers on datatable.
     *
     * @return Response
     */
    public function anyCustomers() {     
        $groups = DB::table('jocom_sp_customer')
                        ->select('jocom_sp_customer.id', 'jocom_user.username', 'jocom_user.firstname', 'jocom_user.lastname')
                        ->leftJoin('jocom_user', 'jocom_user.id', '=', 'jocom_sp_customer.user_id');
                        //->groupBy('jocom_user.username')
                        // ->where('jocom_sp_customer.status', '!=', '2');
                
        return Datatables::of($groups)
                ->add_column('Action', '
                        <a class="btn btn-primary" title="" data-toggle="tooltip" href="/special_price/customer/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                        
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
       return View::make('special_price.customer.index', ['sp_customer' => '']);
    }

    /**
     * Show the form for creating a new customer.
     *
     * @return Response
     */
    public function anyCreate()
    {   
        $status     = array('0' => 'Inactive', '1' => 'Active');
        
        if ( Permission::CheckAccessLevel(Session::get('role_id'), 8, 5, 'AND'))
            return View::make('special_price.customer.create')->with(array('status' => $status)); 
        else
            return View::make('home.denied', array('module' => 'Special Pricing > Add Special Price Customer'));   
    }


    public function anyStore()
    {
        $inputs     = Input::all();

        //var_dump($inputs);
        // exit();
        $rules      = [
            'cid'   => 'required',
            'gid'   => 'required',
        ];

        $messages['cid.required']  = "The customer is required.";
        $messages['gid.required']  = "The group is required.";
     

        $validator = Validator::make(Input::all(), $rules, $messages);

        if ($validator->passes()) {
            //echo "Pass!";
            
            if (Input::has('gid')) {
                // var_dump(Input::get('gid'));
                $cust               = array();
                $cust['user_id']    = Input::get('cid');
                $cust['status']     = Input::get('status');
                $cust['created_by'] = Session::get('user_id');
                $cust['created_at'] = date('Y-m-d H:i:s');

                $sp_id = DB::table('jocom_sp_customer')->insertGetId($cust);

                foreach (Input::get('gid') as $gid) {
                    $group               = array();
                    $group['sp_cust_id'] = $sp_id;
                    $group['sp_group_id']= $gid;
                    // var_dump($group);
                    DB::table('jocom_sp_customer_group')->insert($group);
                }

                Session::flash('message', 'Settings saved successfully.');
                return View::make('special_price.customer.index');
            }
            
        } 
        else {
            //$errors = $validator->messages();
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }

    }


    public function anyEdit($id) {
        $status     = array('0' => 'Inactive', '1' => 'Active');
        $customer   = SpecialPrice::get_sp_customer($id);
        $groups     = SpecialPrice::get_sp_customer_group($id);

        return View::make('special_price.customer.edit')->with(array('customer' => $customer, 'status' => $status, 'groups' => $groups, 'sp_id' => $id)); 
    }

    public function anyUpdate($id) { 
        $inputs     = Input::all();
        var_dump($inputs);

        $rules      = [
            'cid'   => 'required',
            'gid'   => 'required',
        ];

        $messages['cid.required']  = "The customer is required.";
        $messages['gid.required']  = "The group is required.";

        $validator  = Validator::make(Input::all(), $rules, $messages);
        $sp_id      = SpecialPrice::get_cust_sp_id($id);

        if ($validator->passes()) {
            
            if (Input::has('gid')) {

                DB::table('jocom_sp_customer_group')->where('sp_cust_id', '=', $id)->delete();

                foreach(Input::get('gid') as $gid) {
                    $group               = array();
                    $group['sp_cust_id'] = $id;
                    $group['sp_group_id']= $gid;

                    DB::table('jocom_sp_customer_group')->insert($group);
                }
                
                Session::flash('message', 'Settings saved successfully.');
            }

            $status     = array('0' => 'Inactive', '1' => 'Active');
            $customer   = SpecialPrice::get_sp_customer($id);
            $groups     = SpecialPrice::get_sp_customer_group($id);

            return View::make('special_price.customer.edit')->with(array('customer' => $customer, 'status' => $status, 'groups' => $groups, 'sp_id' => $id)); 
        } 
        else {
            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
        }

       
    }

}

?>