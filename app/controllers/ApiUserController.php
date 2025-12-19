<?php

class ApiUserController extends BaseController
{
    public function anyIndex()
    {
        echo "Page not found.";
        return 0;
    }

    public function anyLogin()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';
        $user        = trim(Input::get('user'));
        $pass        = trim(Input::get('pass'));

        if(Input::has('uuid'))
            $uuid = Input::get('uuid');

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, ApiUser::GetUserDetails($user, $pass, $uuid));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }

    public function anyFBlogin()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';
        $email       = trim(Input::get('email'));
        $fid         = trim(Input::get('fid'));
        $password    = (Input::has('password')) ? trim(Input::get('password')) : "";

        if(Input::has('uuid'))
            $uuid = Input::get('uuid');

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, ApiUser::GetFBDetails($email, $fid, $password, $uuid));
        

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }
    
    public function anyGooglelogin(){

        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';
        $email       = trim(Input::get('email'));
        $password    = (Input::has('password')) ? trim(Input::get('password')) : "";
        $id          = trim(Input::get('id'));
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'GOOGLE LOGIN';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

        $data = array_merge($data, ApiUser::GetGoogleDetails($email, $password, $id));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }
    
    public function anyApplelogin(){

        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';

        $id          = trim(Input::get('id'));
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'APPLE LOGIN';
        $ApiLog->data = json_encode(Input::all());
        $ApiLog->save();

        $data = array_merge($data, ApiUser::GetAppleDetails($id));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');

    }

    public function anyPassword()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }
        // $api->MemberForgot(Input::all());
        $data = array_merge($data, ApiUser::ResetPassword(Input::all()));
        // var_dump($data);

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }

    public function anyProfile()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, ApiUser::updateprofile(Input::all()));

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }

    public function anyRegister()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, ApiUser::RegisterMember(Input::all()));
        
        // reward user //
        // if($data['xml_data']['status'] == '1'){
        //     $this->registerreward(Input::get('username'));
        // }
        
        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
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
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

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

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }

    public function anyListfavaddr()
    {
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        $data = array_merge($data, ApiUser::ListFavAddr(Input::all()));
        

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
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
                            $message->from('payment@tmgrocer.com', 'tmGrocer');
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
        
        return Response::json($response);
        
        
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
        $data        = array();
        $get         = array();
        $data['enc'] = 'UTF-8';
        $get['lang'] = 'EN';

        if (Input::has('enc')) {
            $data['enc'] = trim(Input::get('enc'));
        }

        // GetAddrBYID
        $data = array_merge($data, ApiUser::GetAddrBYID(Input::all()));
        

        return Response::view('xml_v', $data)
            ->header('Content-Type', 'text/xml')
            ->header('Pragma', 'public')
            ->header('Cache-control', 'private')
            ->header('Expires', '-1');
    }
    
    public function anyWavpaylogin(){
        die('Invalid Request');
		$data = [ 'enc' => 'UTF-8' ];
		$ApiLog = new ApiLog;
		$ApiLog->api = 'WAVPAY APP LOGIN';
		$ApiLog->data = json_encode(Input::all());
		$ApiLog->save();

		$data = array_merge($data, ApiUser::GetWavpayDetails());

		return Response::view('xml_v', $data)
			->header('Content-Type', 'text/xml')
			->header('Pragma', 'public')
			->header('Cache-control', 'private')
			->header('Expires', '-1');
	}
    
}
