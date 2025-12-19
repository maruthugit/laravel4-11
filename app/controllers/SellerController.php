<?php

class SellerController extends BaseController {

    public function __construct()
    {
        
        $this->beforeFilter('auth');
        // echo "<br>check authentication ";
    }

    /**
     * Display a listing of the banners on datatable.
     *
     * @return Response
     */
    public function anySellers() {     
        // $sellers = Seller::select('id', 'username', 'company_name', 'ic_no', 'email', 'tel_num', 'mobile_no', 'bank_acc_no', 'bank_type')
        $sellers = Seller::select('id', 'created_date', 'username', 'company_name', 'ic_no', 'email','tel_num', 'mobile_no', 'credit_term', 'business_method','active_status')
                        ->where('active_status', '<', '2');
                        
                            // ->get();

        return Datatables::of($sellers)
                        ->add_column('Action', '
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/seller/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 9, 9, \'AND\'))
                                <a id="deleteSeller" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$company_name}}" href="/seller/delete/{{$id}}"><i class="fa fa-remove"></i></a>
                                @endif
                            ')
                        ->edit_column('active_status', '
                                @if($active_status == 1)
                                    <p class="text-success">Active</p>
                                @else
                                    <p class="text-danger">Inactive</p>
                                @endif
                                ')
                        ->edit_column('business_method', '
                                @if($business_method == 1)
                                    Buy Off
                                @elseif($business_method == 2)
                                    Consignment
                                @else
                                    -
                                @endif
                                ')
                        ->make();
    }

    /**
     * Display a listing of the customer.
     *
     * @return Response
     */
    public function anyIndex()
    {
        $seller   = Seller::all();

        return View::make('seller.index', ['sellers' => $seller]);
    }


    /**
     * Show the form for creating a new customer.
     *
     * @return Response
     */
    public function anyCreate()
    {   
        $status         = array("1" => "Active", "0" => "Inactive"); 
        $countries      = Customer::GetCountryList();

        if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
            return View::make('seller.create', ['countries' => $countries, 'status' => $status]);   
        else
            return View::make('home.denied', array('module' => 'Sellers > Add Seller'));
    }

     /**
     * Store a newly created customer in storage.
     *
     * @return Response
     */
    public function anyStore()
    {
        $seller_input   = Input::all();
        
        $validator      = Validator::make(Input::all(), Seller::$rules);

        $seller         = new Seller;

        if (Input::get('non_gst') == null )
            { $non_gst = 0; } 
        else 
            { $non_gst =Input::get('non_gst'); } 
            
        if (Input::get('notification') == null )
            { $notification = 0; } 
        else 
            { $notification =Input::get('notification'); }

        if ($validator->passes()) {
            $seller->username       = Input::get('username');
            $seller->pic_full_name  = Input::get('pic_full_name');
            $seller->company_name   = Input::get('company_name');
            $seller->company_reg_num    = Input::get('company_reg_num');
            $seller->gst_reg_num        = Input::get('gst_reg_num');
            $seller->non_gst        = $non_gst;
            $seller->notification   = $notification;
            $seller->email          = Input::get('email');
            $seller->password       = Hash::make(Input::get('password'));
            $seller->ic_no          = Input::get('ic_no');
            $seller->tel_num        = Input::get('tel_num');
            $seller->address1       = Input::get('address1');
            $seller->address2       = Input::get('address2');
            $seller->postcode       = Input::get('postcode');
            $seller->country        = Input::get('country');
            $seller->state          = Input::get('state');
            $seller->city           = Input::get('city');
//            $seller->city_id        = Input::get('city');
            $seller->mobile_no      = Input::get('mobile_no');
            $seller->bank_acc_no    = Input::get('bank_acc_no');
            $seller->bank_type      = Input::get('bank_type');
            $seller->active_status  = Input::get('status');
            $seller->timestamps     = false;
            $seller->credit_term    = Input::get('credit_term');
            $seller->business_method    = Input::get('business_method');
            $seller->description    = Input::get('description');
            $seller->created_by     = Session::get('username');
            $seller->created_date   = date("Y-m-d H:i:s");
            $seller->email1         = Input::get('email2');
            $seller->email2         = Input::get('email3');
            if(file_exists(Input::file('logo')))
            {
            $file                   = Input::file('logo');
            $extension              = $file->getClientOriginalExtension();
            }
            else {
                $file                   = '';
                $extension              = '';
            }
            if($seller->save())
            {
                $insert_audit = General::audit_trail('SellerController.php', 'Store()', 'Add Seller', Session::get('username'), 'CMS');
                if(file_exists(Input::file('logo')))
                {
                $file_name       = $seller->id . "." .$extension; 
                $dest_path       = Config::get('constants.SELLER_FILE_PATH');
                $upload_success  = Input::file('logo')->move($dest_path, $file_name);

                if ($upload_success) {
                    $seller = Seller::find($seller->id);
                    $seller->file_name = $file_name;
                    $seller->timestamps = false;
                    $seller->save();
                    
                } else {
                    echo "<br>Upload file FAILED!";
                }
              }
              return Redirect::to('/seller/index')->with('success', 'Seller(ID: '.$seller->id.') added successfully.');
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
        $seller         = Seller::find($id);
        // $country_list   = DB::table('jocom_countries')->lists('name', 'id');
        $status         = array("1" => "Active", "0" => "Inactive");  
        $countries      = Customer::getCountryList();
        $states         = Customer::getStateList($seller->country);
        $cities         = Customer::getCityList($seller->state);


        return View::make('seller.edit')->with(array(
                        'seller'    => $seller, 
                        'countries' => $countries, 
                        'states'    => $states,
                        'cities'    => $cities,
                        'status'    => $status
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
        $seller         = new Seller();
        
        if (!Input::has('non_gst'))
            { $non_gst = 0; } 
        else 
            { $non_gst =Input::get('non_gst'); }
            
        if (!Input::has('notification'))
            { $notification = 0; } 
        else 
            { $notification =Input::get('notification'); }

        $arr_input_all            = Input::all();
        $arr_input_all['non_gst'] = $non_gst;
        $arr_input_all['notification'] = $notification;
        $arr_validate   = Seller::getUpdateRules($arr_input_all);
        $arr_input      = Seller::getUpdateInputs($arr_input_all);
        $validator      = Validator::make($arr_input, $arr_validate);
        $dest_path      = Config::get('constants.SELLER_FILE_PATH');
        $file_name      = "";

        // var_dump($arr_input_all);

        if(Input::hasFile('logo')) {
            $file           = Input::file('logo');
            $cur_file_name  = $file->getRealPath() . "/" . $file->getClientOriginalName();
            $old_file_name   = Seller::getOldFilename($id);
            $extension       = $file->getClientOriginalExtension();

            if($old_file_name <> $cur_file_name) {
//              echo "===> [dest_path] ".$dest_path;
//              echo "<br>===> [old_file_name] ".$old_file_name;                
                
                $str = strtolower($extension);
                if($str == 'jpg' || $str  == 'png' || $str  == 'jpeg' || $str  == 'gif')
                {
                    if(file_exists($dest_path. "/" . $old_file_name)) {
                    // echo "<br>File EXISTS! ".$dest_path. "/" . $old_file_name;
                    //    /* Delete old file. */
                       File::delete($dest_path ."/". $old_file_name);
                    }
                    /* Replace with new file. */
                    $file_name       = $id . "." . $extension;   
                    $upload_success  = $file->move($dest_path, $file_name);
                }
                else
                {  return Redirect::back()
                                ->withErrors(['The logo must be a file of type: gif, jpeg, jpg, png.']); 
                }

            }
        }

        if ($validator->passes()) {
           
            $arr_udata              = Seller::getUpdateDbDetails($arr_input);

            if($file_name != "")
            $arr_udata['file_name'] = $file_name;
//              echo "<pre>";
//            var_dump($arr_udata);
//            echo "</pre>";
//            
            if($seller->updateSeller($id, $arr_udata))
            {
                $insert_audit = General::audit_trail('SellerController.php', 'Update()', 'Edit Seller', Session::get('username'), 'CMS');                
                return Redirect::to('seller/index')->with('success', 'Seller(ID: '.$id.') updated successfully.');
            } 
        
        }else {
            return Redirect::back()
                        ->withInput()
                        ->withErrors($validator)->withInput();
        }
    }
 
    /**
     * Remove the specified seller from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $file_name      = Seller::getOldFilename($id);
        $date           = date("Ymd_His");

        if(file_exists(Config::get('constants.SELLER_FILE_PATH')."/".$file_name))
            rename(Config::get('constants.SELLER_FILE_PATH')."/".$file_name, Config::get('constants.ARC_SELLER_FILE_PATH')."/". $date . "_" . $file_name);

        $seller = Seller::find($id);
        $seller->active_status  = '2';
        $seller->timestamps     = false;
        $seller->modify_date    = date("Y-m-d H:i:s");
        $seller->modify_by      = Session::get('username');
        $seller->save();
        
        // Seller::destroy($id);
        // $user->active_status = '0';
        // $user->timestamps = false;
        // $user->save();

        $insert_audit = General::audit_trail('SellerController.php', 'delete()', 'Delete Seller', Session::get('username'), 'CMS');

        return Redirect::to('/seller/index');
    }
    
    /*
    * List GS seller index
    */
    public function anyGsseller(){


        return View::make('seller.gs_seller');


    }

    /*
    * List GS seller index
    */
    public function anyGssellercreate(){

        $status         = array("1" => "Active", "0" => "Inactive"); 
        $countries      = Customer::GetCountryList();

        if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
            return View::make('seller.gs_vendor_create', ['countries' => $countries, 'status' => $status]);   
        else
            return View::make('home.denied', array('module' => 'Sellers > Add Seller'));

    }

    public function anyGsselleredit($id){

        $vendor     = GSVendor::find($id);

        if ( Permission::CheckAccessLevel(Session::get('role_id'), 9, 5, 'AND'))
            return View::make('seller.gs_seller_edit', ['vendor' => $vendor]);   
        else
            return View::make('home.denied', array('module' => 'Sellers > Add Seller'));

    }

    public function anyStoregsseller(){

        $isError = false;

        try{

            $vendor_name      = Input::get('vendor_name');
            $vendor_com_reg      = Input::get('vendor_com_reg');
            $vendor_contact_number      = Input::get('vendor_contact_number');
            $address      = Input::get('address');

            $GSVendor = new GSVendor;
            $GSVendor->seller_name = $vendor_name ;
            $GSVendor->seller_address = $address ;
            $GSVendor->seller_phone_no = $vendor_contact_number;
            $GSVendor->save();

        } catch (Exception $ex) {
            $isError = true;
        }finally{
            if(!$isError){
                return Redirect::to('/seller/gsseller')->with('success', 'Vendor added successfully.');
            }else{
                return Redirect::to('/seller/gsseller')->with('error', 'Failed to add.');
            }
            
        }

    }

    public function anyStoreeditgsseller(){

        $isError = false;

        try{

            $vendor_name      = Input::get('vendor_name');
            $vendor_com_reg      = Input::get('vendor_com_reg');
            $vendor_contact_number      = Input::get('vendor_contact_number');
            $address      = Input::get('address');
            $status     = Input::get('status');
            $id     = Input::get('id');

            $GSVendor = GSVendor::find($id);
            $GSVendor->seller_name = $vendor_name ;
            $GSVendor->seller_address = $address ;
            $GSVendor->seller_phone_no = $vendor_contact_number;
            $GSVendor->activation = $status ;
            $GSVendor->save();

        } catch (Exception $ex) {
            $isError = true;
        }finally{
            if(!$isError){
                return Redirect::to('/seller/gsseller')->with('success', 'Vendor update successfully.');
            }else{
                return Redirect::to('/seller/gsseller')->with('error', 'Failed to update.');
            }
            
        }

    }

    public function anyEditgsseller(){

        $isError = false;

        try{

            $vendor_name      = Input::get('vendor_name');
            $vendor_com_reg      = Input::get('vendor_com_reg');
            $vendor_contact_number      = Input::get('vendor_contact_number');
            $address      = Input::get('address');

            $GSVendor = new GSVendor;
            $GSVendor->seller_name = $vendor_name ;
            $GSVendor->seller_address = $address ;
            $GSVendor->seller_phone_no = $vendor_contact_number;
            $GSVendor->save();

        } catch (Exception $ex) {
            $isError = true;
        }finally{
            if(!$isError){
                return Redirect::to('/seller/gsseller')->with('success', 'Vendor added successfully.');
            }else{
                return Redirect::to('/seller/gsseller')->with('error', 'Failed to add.');
            }
            
        }

    }


    public function anyGSvendors() { 

        $GSsellers = GSVendor::select('id', 'seller_name', 'seller_address', 'seller_phone_no','activation');
                      
        return Datatables::of($GSsellers)
            ->add_column('Action', '
                    <a class="btn btn-primary" title="" data-toggle="tooltip" href="/seller/gsselleredit/{{$id}}"><i class="fa fa-pencil"></i></a>
                ')
            ->edit_column('activation', '
                    @if($activation == 1)
                        <p class="text-success">Active</p>
                    @else
                        <p class="text-danger">Inactive</p>
                    @endif
                    ')
            ->make();
    }
}
?>