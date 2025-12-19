<?php

class ApiV2UserController extends BaseController
{
    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyLogin()
    {
        $get         = array();
        $get['lang'] = 'EN';
        $user        = trim(Input::get('user'));
        $pass        = trim(Input::get('pass'));

        if(Input::has('uuid'))
            $uuid = Input::get('uuid');

        $data = ApiUser::GetUserDetails($user, $pass, $uuid);

        // return Response::json($data);
        return json_encode($data);
    }

    public function anyFBlogin()
    {
        $get         = array(); 
        $get['lang'] = 'EN';
        $email       = trim(Input::get('email'));
        $fid         = trim(Input::get('fid'));
        $password    = (Input::has('password')) ? trim(Input::get('password')) : "";

        if(Input::has('uuid'))
            $uuid = Input::get('uuid');

        $data = ApiUser::GetFBDetails($email, $fid, $password, $uuid);
        
        // return Response::json($data);
        return json_encode($data);

    }
    
    public function anyGooglelogin(){

        $get         = array();
        $get['lang'] = 'EN';
        $email       = trim(Input::get('email'));
        $password    = (Input::has('password')) ? trim(Input::get('password')) : "";
        $id          = trim(Input::get('id'));
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'GOOGLE LOGIN';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

        $data = ApiUser::GetGoogleDetails($email, $password, $id);

        // return Response::json($data);
        return json_encode($data);

    }
    
    public function anyApplelogin(){

        $get         = array();
        $get['lang'] = 'EN';
        $id          = trim(Input::get('id'));
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'APPLE LOGIN';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();
        $data =ApiUser::GetAppleDetails($id);

        // return Response::json($data);
        return json_encode($data);
    }

    public function anyPassword()
    {
        $get         = array();
        // $api->MemberForgot(Input::all());
        $data = ApiUser::ResetPassword(Input::all());
        // var_dump($data);

        // return Response::json($data);
        return json_encode($data);

    }

    public function anyProfile()
    {
        $get         = array();
        $data = ApiUser::updateprofile(Input::all());

        // return Response::json($data);
        return json_encode($data);
    }

    public function anyRegister()
    {
        $get         = array();
        $get['lang'] = 'EN';

        $data = ApiUser::RegisterMember(Input::all());
         
        // return Response::json($data);
        return json_encode($data);

    }
    
    public function registerreward($username = ''){

        try{
         
            $RewardUser = RewardUser::Campaign('REG-JPT')->first();
            // Reward
            $User = Customer::where("username",$username)->first();
            $PointUser = PointUser::where("user_id",$User->id)->where("point_type_id",1)->where("status",1)->first();
            
            if($RewardUser){
                if($PointUser ){
                    $PointUser->point = $PointUser->point + $RewardUser->total_reward;
                }else{
                    // Create Point Account
                    $PointUser = new PointUser;
                    $PointUser->user_id = $User->id;
                    $PointUser->point = 0;
                    $PointUser->point_type_id = 1;
                    $PointUser->status = 1;
                    $PointUser->save();
    
                    $PointUser->point = $PointUser->point + $RewardUser->total_reward;
                }
            }

            if($PointUser->save()){
                // Deduct Reward Counter
                $RewardUser->balance = $RewardUser->balance - 1;
                $RewardUser->save();

                // Update Details
                $RewardSchemeDetails = new RewardSchemeDetails;
                $RewardSchemeDetails->user_id = $User->id;
                $RewardSchemeDetails->scheme_id = $RewardUser->id;
                $RewardSchemeDetails->amount = $RewardUser->total_reward;
                $RewardSchemeDetails->save();
            }

            
        } catch(exception $ex){

        }

    }

    public function getPoints()
    {
        $username = Input::get('username', Input::get('user')); // Fallback for old convention
        $points   = PointUser::select('point_types.type AS type', 'point_users.point AS point')
            ->join('point_types', 'point_users.point_type_id', '=', 'point_types.id')
            ->join('jocom_user', 'point_users.user_id', '=', 'jocom_user.id')
            ->where('jocom_user.username', '=', $username)
            ->where('point_users.status', '=', 1)
            ->get();

        return json_encode($points);
    }

    public function anyEditfavaddr()
    {
        $data        = array();
        $get         = array();
        $get['lang'] = 'EN';

        if (Input::has('delete'))
        {
            $data = array_merge($data, ApiUser::DeleteFavAddr(Input::all()));
        }
        else
        {
            if (Input::has('addr_id'))
                $data = array_merge($data, ApiUser::EditFavAddr(Input::all()));
            else
                $data = array_merge($data, ApiUser::AddFavAddr(Input::all()));
        }

        // return Response::json($data);
        return json_encode($data);

    }

    public function anyListfavaddr()
    {
        $get         = array();
        $get['lang'] = 'EN';

        $data =  ApiUser::ListFavAddr(Input::all());  
        // return Response::json($data);
        return json_encode($data);

    }
    
    public function anySecurity()
    {
        
        $data = array();
        $ResponseStatus = 1; 
        $isError = false;

        try{
            // Begin Transaction
            DB::beginTransaction();
            
            $username = Input::get('username');
            $userAccount = Customer::where('username', '=', $username)
                ->where('active_status','<>','2')->first();
            
        
            if(count($userAccount)> 0 ){
                
                // Activate the account
                $userAccount->active_status = 2;
                $userAccount->save();

                $data = "Your account has been deleted. Thank you for your support";
                        
                              
                // End of Free Coupon for newly register member

            }else{
               $ResponseStatus = 0;   
               $data = 'Invalid Username';
            }
           
        } catch (Exception $e) {
           
            $ResponseStatus = 0; 
            $isError = true;
            $data['message'] = $e->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = $data;
        return $response;
        
    }
    
    public function anyActivation()
    {
        
        $data = array();
        $ResponseStatus = 1; 
        $isError = false;

        try{
            // Begin Transaction
            DB::beginTransaction();
            
            $username = Input::get('username');
            $userAccount = Customer::where('username', '=', $username)
                ->where('active_status','=','0')->first();
            
        
            if(count($userAccount)> 0 ){
                
                // Activate the account
                $userAccount->active_status = 1;
                $userAccount->save();

                $userEmail = $userAccount->email;
                $userFirstname = $userAccount->firstname;
                
                // Free Coupon for newly register member
                $campaignDate = date("Y-m-d");
                if ($campaignDate <= "2016-06-30")
                {
                    $new = 1;
                    
                    
                    if ($new == 1)
                    {
                        $coupon  = Coupon::find(1226); // related coupon for the campaign
                        $coupontype = CouponType::where('coupon_id', 1226)->get();
                        $num = 1;

                        $coupon = Coupon::duplicate($coupon, $coupontype);

                        $subject    = "[tmGrocer]: Get RM10 Off For Your First Purchase!";

                        $user = array(
                            'email' => $userEmail,
                            'name'  => $userFirstname,
                            'username'  => $username,
                            
                        );
                        
                        $data = array(
                            'name'      => $userFirstname,
                            'username'  => $username,
                            'coupon'    => $coupon,
                        );

                        Mail::send('emails.coupon', $data, function($message) use ($user,$subject)
                        {
                            $message->from('webmaster@tmgrocer.com', 'tmGrocer');
                            $message->to($user['email'], $user['name'])->subject($subject);
                        });
                    }
                }                
                // End of Free Coupon for newly register member

            }else{
                 
                throw new Exception("Failed to activate!");
            }
           
        } catch (Exception $e) {
           
            $ResponseStatus = 0; 
            $isError = true;
            $data['message'] = $e->getMessage();
            
        }finally{
            if($isError){
                DB::rollback();
            }else{
                DB::commit();
            }
        }
        
        $response = array("responseStatus"=>$ResponseStatus,"data"=>$data);
        return json_encode($response);
        
    }
    
    public function anyResendactivation()
    {
        
        $data = array();
        $ResponseStatus = 1; 
        $isError = false;

        try{
            
            $username = Input::get('username');
            $userAccount = Customer::where('username', '=', $username)
                ->where('active_status','=','0')->first();
            
            
            if(count($userAccount)> 0 ){
                
                // send activation link
                $user = array(
                    "email"=>$userAccount->email,
                    "name"=>$userAccount->firstname,
                );
                
                $data = array(
                    'username'      => $username,
                    'name'      => $firstname,
                    'environment'  => Config::get('constants.ENVIRONMENT')
                );
                
                $ActivationSubject    = "[tmGrocer]: Account Activation!";
                Mail::send('emails.activation', $data, function($message) use ($user,$ActivationSubject)
                {
                    $message->from('webmaster@tmgrocer.com', 'tmGrocer');
                    $message->to($user['email'], $user['name'])->subject($ActivationSubject);
                });

            }else{
                 
                throw new Exception("Failed to send link!");
            }
           
        } catch (Exception $e) {
           
            $ResponseStatus = 0; 
            $isError = true;
            $data['message'] = $e->getMessage();
            
        }
        
        $response = array("responseStatus"=>$ResponseStatus,"data"=>$data);
        
        // return Response::json($response);
        return json_encode($response);
        
        
    }


    public function anyServicedeliverylogin() {


        $data = array();
        $RespStatus = 1; 
        $message = "";
        $messageCode = "";
        $errorCode = "";
        $is_error = false;
        $isInvalidLogin = false;
        $error_line = "";

        try {
            
            $user   = trim(Input::get('user'));
            $pass   = trim(Input::get('pass'));

            $Customer = Customer::where('username',$user)
                    ->where('active_status',1)->first();
            
         

            if(count($Customer) > 0 ){
                  //return $Customer;
                $password = Hash::make($pass);
               
                if(Hash::check($pass,$Customer->password )){
                  
                    $name = $Customer->full_name;
                    $username = $Customer->username;
                    $user_id = $Customer->id;
                    $shipper_id = $Customer->id;
                    $messageCode = 104;
                }else{
                    throw new Exception($message, '103');
                }
                
            }else{
                throw new Exception($message, '103');
            }
        
        } catch (Exception $ex) {
            
            if($ex->getCode() == '103'){
                $isInvalidLogin = true;
            }else{
                $is_error = true;
                $error_line = $ex->getLine();
                $errorCode = $ex->getCode();
            }
            
        } finally {
            if ($isInvalidLogin) {
                $data['loginAuthenticated'] = 0;
                $messageCode = '103';
            } else {
                $data['loginAuthenticated'] = 1;
                $data['profileInfo'] = array(
                    "name"=> $name,
                    "username"=> $username,
                    "user_id"=> $user_id,
                    "shipper_id"=> $shipper_id,
                );
            }
        }


        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message,"messageCode"=>$messageCode, "data" => $data);
        return $response;

    
    }
    
    /**
     * Comment: Cuz it used by revamp JOCOM payment page. 
     *          Purpose to reduce the redundant API call
     *
     * @api TRUE
     * @author YEE HAO
     * @since 2 JUN 2021
     * @param method
     * @version 1.0
     * @method GET, POST, any request method
     * @return XML
     * @used-by revamp.jocom.com.my Payment
     *
     * Last Update: 2 JUN 2021
     */
    public function anyGetaddrbyid()
    {
        $get         = array();
        $get['lang'] = 'EN';

        // GetAddrBYID
        $data =ApiUser::GetAddrBYID(Input::all());
        
        // return Response::json($data);
        return json_encode($data);

    }
    /**
     * Comment: API V2 Profile e-Mail Update
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 08 Nov 2022
     * @param method
     * @version 2.0
     * @method GET, POST, any request method
     * @return JSON
     * @used-by api.jocom.com.my API v2
     *
     * Last Update: 09 NOV 2022
     */
        public function anyUpdateemail()
    {
        $username=Input::get('username');
        $email= Input::get('email');
        $error_exists="0";
        if(filter_var($email,FILTER_VALIDATE_EMAIL)){
           $email_id= $email; 
        }else{
            $data['status'] = '0';
            $data['status_msg'] = 'Please Enter Vaild Mail id'; 
            return json_encode($data); 
        }
        $user_id= Customer::where('username', '=',$username)->first();

        //valid login
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            $checkpoint = DB::table('jocom_user')->select('email')->where('email','=',$email)->first();
            if(!$checkpoint){
                $customer->email=$email_id;
                $error_exists="1";
            }
            $customer->modify_by    = 'api_update';
            $customer->modify_date  = date('Y-m-d H:i:s');
            $customer->timestamps   = false;

            if($customer->save() && $error_exists=="1") {
                // Success message
                 $data['status']     = '1';
                 $data['status_msg'] = '#807';
                 $data['response_data']=array('mobile'=>$customer->mobile_no,'email'=>$customer->email,'address'=>['address1'=>$customer->address1,
            'address2'=>$customer->address2,
            'postcode'=>$customer->postcode,
            'city'=>$customer->city,
            'state'=>$customer->state,
            'country'=>$customer->country]);
            }else if($error_exists=="0"){
                 $data['status'] = '0';
                 $data['status_msg'] = 'Email ID already Exists'; 
            }
            else
            {
                // Invalid request
                 $data['status'] = '0';
                 $data['status_msg'] = '#805';
                // $data['status_msg'] = 'Invalid request. Please try again.';
            }
        }else{
                 $data['status'] = '0';
                 $data['status_msg'] = 'User Not Found';
        }        
        
     return json_encode($data);
    }
    /**
     * Comment: API V2 Profile Mobile Number Update
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 08 Nov 2022
     * @param method
     * @version 2.0
     * @method GET, POST, any request method
     * @return JSON
     * @used-by api.jocom.com.my API v2
     *
     * Last Update: 09 NOV 2022
     */
        public function anyUpdatemobilenumber()
    {
        $username=Input::get('username');
        $mobile= Input::get('mobile_no');
        if($mobile!="" & preg_match('/^[0-9 +]+$/', $mobile)){
           $mobile_no= $mobile; 
        }else{
            $data['status'] = '0';
            $data['status_msg'] = 'Please Enter Vaild Mobile Number'; 
            return json_encode($data); 
        }
        $user_id= Customer::where('username', '=',$username)->first();

        //valid login
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            $customer->mobile_no=$mobile_no;
            $customer->modify_by    = 'api_update';
            $customer->modify_date  = date('Y-m-d H:i:s');
            $customer->timestamps   = false;

            if($customer->save()) {
                // Success message
                 $data['status']     = '1';
                 $data['status_msg'] = '#807';
                 $data['response_data']=array('mobile'=>$customer->mobile_no,'email'=>$customer->email,'address'=>['address1'=>$customer->address1,
            'address2'=>$customer->address2,
            'postcode'=>$customer->postcode,
            'city'=>$customer->city,
            'state'=>$customer->state,
            'country'=>$customer->country]);
            }
            else
            {
                // Invalid request
                 $data['status'] = '0';
                 $data['status_msg'] = '#805';
                // $data['status_msg'] = 'Invalid request. Please try again.';
            }
        }else{
                 $data['status'] = '0';
                 $data['status_msg'] = 'User Not Found';
        }        
        
     return json_encode($data);
    }
    /**
     * Comment: API V2 Profile Address update
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 08 Nov 2022
     * @param method
     * @version 2.0
     * @method GET, POST, any request method
     * @return JSON
     * @used-by APP Profile Address
     *
     * Last Update: 10 NOV 2022
     */
        public function anyUpdateprofileaddress()
    {
        $username=Input::get('username');
        $address_1= Input::get('deliveradd1');
        $address_2= Input::get('deliveradd2');
        $city_name=Input::get('cityName');
        $post_code= Input::get('deliverpostcode');
        $state_name=Input::get('stateName');
        $country_name=Input::get('countryName');
        
        if($address_1==""|| $city_name==""||$state_name==""||$country_name==""){
            $data['status'] = '0';
            $data['status_msg'] = 'Please Fill all required Fields'; 
            return json_encode($data);
        }
        if($post_code!="" & preg_match('/^[0-9]+$/',$post_code)){
           $delivery_postcode= $post_code; 
        }else{
            $data['status'] = '0';
            $data['status_msg'] = 'Please Enter Vaild Postcode'; 
            return json_encode($data); 
        }
        
        $user_id= Customer::where('username', '=',$username)->first();
      
        //valid login
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            $customer->address1=$address_1;
            $customer->address2=$address_2;
            $customer->postcode=$delivery_postcode;
            $customer->city=$city_name;
            $customer->state=$state_name;
            $customer->country=$country_name;
            $customer->modify_by    = 'api_update';
            $customer->modify_date  = date('Y-m-d H:i:s');
            $customer->timestamps   = false;

            if($customer->save()) {
                // Success message
                 $data['status']     = '1';
                 $data['status_msg'] = '#807';
                 $data['response_data']=array('mobile'=>$customer->mobile_no,'email'=>$customer->email,'address'=>['address1'=>$customer->address1,
            'address2'=>$customer->address2,
            'postcode'=>$customer->postcode,
            'city'=>$customer->city,
            'state'=>$customer->state,
            'country'=>$customer->country]);
            }
            else
            {
                // Invalid request
                 $data['status'] = '0';
                 $data['status_msg'] = '#805';
                // $data['status_msg'] = 'Invalid request. Please try again.';
            }
        }else{
                 $data['status'] = '0';
                 $data['status_msg'] = 'User Not Found';
        }        
        
     return json_encode($data);
    }
        /**
     * Comment: API V2 Account Delete
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 20 JAN 2023
     * @param method
     * @version 2.0
     * @method GET, POST, any request method
     * @return JSON
     * @used-by APP Account Delete
     *
     * Last Update: 20 JAN 2023
     */
        public function anyUserdelete()
    {
        $username=Input::get('username');
        $email= Input::get('email');
        $reason= Input::get('reason');
        $description=Input::get('description');
        
        $user_id= Customer::where('username', '=',$username)->first();
      
        //valid login
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            $customer->active_status= '0';
            $customer->modify_by    = 'api_update';
            $customer->modify_date  = date('Y-m-d H:i:s');
            $customer->timestamps   = false;

            if($customer->save()) {
                
                $user_details=DB::table('jocom_user_delete')->insert(['username'=>$username,'email'=>$email,'reason'=>$reason,'description'=>$description,'created_date'=>date('Y-m-d H:i:s')]);
                // Success message
                 $data['status']     = '1';
                 $data['status_msg'] = '#807';
            }
            else
            {
                // Invalid request
                 $data['status'] = '0';
                 $data['status_msg'] = '#805';
            }
        }else{
                 $data['status'] = '2';
                 $data['status_msg'] = '#804';
        }        
        
     return json_encode($data);
    }
     /**
     * Comment: API V2 GET DOB
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 28 MARCH 2023
     * @param method
     * @version 2.0
     * @method GET, POST, any request method
     * @return JSON
     * @used-by APP USER DOB GET
     *
     * Last Update: 29 MARCH 2023
     */
        public function anyUserdob()
    {
        $username=Input::get('username');
        
        if($username!=""){
        $user_id= Customer::where('username', '=',$username)->first();
        $data=array();
        //valid login
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            
                // Invalid request
                 $data['status'] = '1';
                 $data['status_msg'] = '#807';
                 $data['response_data']=array('username'=>$username,'dob'=>$customer->dob);
        }else{
                 $data['status'] = '2';
                 $data['status_msg'] = '#804';
                 $data['error_msg'] = 'User not found';
        }
        }else{
                 $data['status'] = '3';
                 $data['status_msg'] = '#806';
                 $data['error_msg'] = 'Username is required';
        }
        
     return json_encode($data);
    }
    /**
     * Comment: API V2 USER DOB Update
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 29 March 2023
     * @param method
     * @version 2.0
     * @method POST, any request method
     * @return JSON
     * @used-by api.jocom.com.my API v2 user
     *
     * Last Update: 29 MARCH 2023
     */
        public function anyUpdateuserdob()
    {
        $username=Input::get('username');
        $dob= Input::get('dob');
        
    $rules = [
    'username'=>'required',
    'dob'	=> 'date_format:Y-m-d'];


    $validator = Validator::make(Input::all(), $rules);
    if($dob!=""){
     if ($validator->passes()) {
     
        $user_id= Customer::where('username', '=',$username)->first();

        //valid login
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            $customer->dob=$dob;
            $customer->modify_by    = 'api_update';
            $customer->modify_date  = date('Y-m-d H:i:s');
            $customer->timestamps   = false;
 
            if($customer->save()) {
                // Success message
                 $data['status']     = '1';
                 $data['status_msg'] = '#807';
                 $data['response_data']=array('username'=>$username,'dob'=>$customer->dob);
            }else{
                // Invalid request
                 $data['status'] = '0';
                 $data['status_msg'] = '#805';
                 $data['error_msg'] = 'Invalid request. Please try again.';
            }
        }else{
                 $data['status'] = '2';
                 $data['status_msg'] = '#804';
                 $data['error_msg'] = 'User Not Found';
        }
     }else{
          $errors=$validator->messages();
          $data['status'] = '3';
          $data['status_msg'] = '#806';
          if($errors->has('dob')) {
                $data['error_msg'] = 'Date of Birth does not match the format Y-m-d';
            }
          if($errors->has('username')) {
                $data['error_msg'] = 'Username is required';
            }
          
     }
    }else{
           $data['status'] = '3';
          $data['status_msg'] = '#806';
          $data['error_msg'] = 'Date of birth is required';
    }
        
     return json_encode($data);
    }
    /**
     * Comment: API V2 GET GENDER
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 28 MARCH 2023
     * @param method
     * @version 2.0
     * @method GET, POST, any request method
     * @return JSON
     * @used-by APP USER GENDER GET
     *
     * Last Update: 29 MARCH 2023
     */
        public function anyUsergender()
    {
        $username=Input::get('username');
        $user_id= Customer::where('username', '=',$username)->first();
        $data=array();
        //valid login
        if($username!=""){
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            
                // Invalid request
                 $data['status'] = '1';
                 $data['status_msg'] = '#807';
                $data['response_data']=array('username'=>$username,'gender'=>$customer->gender!=null?$customer->gender:"");
        }else{
                 $data['status'] = '2';
                 $data['status_msg'] = '#804';
                 $data['error_msg'] = 'User Not Found';
                 
        }
        }else{
                 $data['status'] = '3';
                 $data['status_msg'] = '#806';
                 $data['error_msg'] = 'Username is required';
        }
        
     return json_encode($data);
    }
   /**
     * Comment: API V2 USER GENDER Update
     *
     * @api TRUE
     * @author Boobalan Thangavel
     * @since 29 March 2023
     * @param method
     * @version 2.0
     * @method POST, any request method
     * @return JSON
     * @used-by api.jocom.com.my API v2 user
     *
     * Last Update: 29 MARCH 2023
     */
        public function anyUpdateusergender()
    {
        $username=Input::get('username');
        $gender= Input::get('gender');
        
    $rules = [
    'username'=>'required',
    'gender'	=> 'required'];


    $validator = Validator::make(Input::all(), $rules);
     if ($validator->passes()) {
     
        $user_id= Customer::where('username', '=',$username)->first();

        //valid login
        if ($user_id != null)
        {
            $customer = Customer::find($user_id->id);
            $customer->gender=$gender;
            $customer->modify_by    = 'api_update';
            $customer->modify_date  = date('Y-m-d H:i:s');
            $customer->timestamps   = false;
 
            if($customer->save()) {
                // Success message
                 $data['status']     = '1';
                 $data['status_msg'] = '#807';
                 $data['response_data']=array('username'=>$username,'gender'=>$customer->gender!=null?$customer->gender:"");
            }else{
                // Invalid request
                 $data['status'] = '0';
                 $data['status_msg'] = '#805';
                 $data['error_msg'] = 'Invalid request. Please try again.';
            }
        }else{
                 $data['status'] = '2';
                 $data['status_msg'] = '#804';
                 $data['error_msg'] = 'User Not Found';
        }
     }else{
          $errors=$validator->messages();
          $data['status'] = '0';
          $data['status_msg'] = '#806';
          if($errors->has('gender')) {
                $data['error_msg'] = 'Gender is required';
            }
          if($errors->has('username')) {
                $data['error_msg'] = 'Username is required';
            }
          
     }
        
     return json_encode($data);
    }
    public function anyPasswordchange()
    {
        $username=trim(Input::get('username'));
        $password=trim(Input::get('new_password'));
        $old_password=trim(Input::get('old_password'));
        
        $rules = [
                  'username'=>'required',
                  'new_password'	=> 'required',
                  'old_password'	=> 'required',];
       $validator = Validator::make(Input::all(), $rules);
     
       if($validator->passes()) {
         
        $user_id= Customer::where('username', '=',$username)->first();
        $valid= 0;
        if($user_id){
        if(Hash::check($old_password,$user_id->password)){
          $valid = 1;   
        }
        }else{
                 $data['status'] = '0';
                 $data['status_msg'] = '#804';
                 $data['error_msg'] = 'User Not Found!';  
                  return json_encode($data);
        }
        
         //valid login
         
        if ($user_id != null && $valid==1)
        {
            $customer = Customer::find($user_id->id);
            $customer->password=Hash::make($password);
            $customer->modify_by    = 'api_update';
            $customer->modify_date  = date('Y-m-d H:i:s');
            $customer->timestamps   = false;
 
            if($customer->save()) {
                // Success message
                 $data['status']     = '1';
                 $data['status_msg'] = '#807';
                 $data['response_data']=array('username'=>$username,'password'=>$password);
            }else{
                // Invalid request
                 $data['status'] = '0';
                 $data['status_msg'] = '#805';
                 $data['error_msg'] = 'Invalid request. Please try again.';
            }
        }else{
                 $data['status'] = '0';
                 $data['status_msg'] = '#801';
                 $data['error_msg'] = 'Old password is incorrect.';   
        }
        
        }else{
          $errors=$validator->messages();
          $data['status'] = '0';
          $data['status_msg'] = '#806';
          if($errors->has('new_password')) {
                $data['error_msg'] = 'New Password is required';
            }
          if($errors->has('username')) {
                $data['error_msg'] = 'Username is required';
            }
          if($errors->has('old_password')) {
                $data['error_msg'] = 'Old Password is required';
            }
          }
        return json_encode($data);
    }
    
    
}
