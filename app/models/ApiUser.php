<?php
use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class ApiUser extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    public $timestamps = false;
    /**
     * Table for all transaction.
     *
     * @var string
     */
    protected $table = 'jocom_user';

    public function RegisterMember($input = array())
    {
        $data       = array();
        $err_msg    = array();
        $validator  = Validator::make($input, Customer::$rules);
        $createdby = "api_register";
        $cust       = new Customer;

        if ($validator->passes()) {
            $cust->username     = Input::get('username');
            $cust->full_name    = Input::get('firstname') ." ". Input::get('lastname');
            $cust->firstname    = Input::get('firstname');
            $cust->lastname     = Input::get('lastname');
            $cust->email        = Input::get('email');
            $cust->password     = Hash::make(Input::get('password'));

            if(Input::get('ic_passport') != "") $cust->ic_no        = Input::get('ic_passport');
            if(Input::get('home_no') != "")     $cust->home_num     = Input::get('home_no');
            if(Input::get('address_1') != "")   $cust->address1     = Input::get('address_1');
            if(Input::get('address_2') != "")   $cust->address2     = Input::get('address_2');
            if(Input::get('postcode') != "")    $cust->postcode     = Input::get('postcode');
            if(Input::get('state') != "")       $cust->state        = Input::get('state');
            if(Input::get('city') != "")        $cust->city         = Input::get('city');
            if(Input::get('country') != "")     $cust->country_id   = Input::get('country');
            if(Input::get('dob') != "")         $cust->dob          = Input::get('dob');
            if(Input::get('gender') != "")      $cust->gender       = Input::get('gender');
            if(Input::get('mobile_no') != "")   $cust->mobile_no    = Input::get('mobile_no');
            if(Input::get('usr_agree') != "")   $cust->usr_agree    = Input::get('usr_agree');
            if(Input::get('pdpa') != "")        $cust->pdpa         = Input::get('pdpa');
            if(Input::has('uuid'))              $cust->uuid         = Input::get('uuid');

            if (Input::has('agent')) {
                $cust->agent_id = Agent::agentCode(Input::get('agent'))->pluck('id');
            }
            
            if (Input::has('accountid')) {
                $cust->accountid    = Input::get('accountid');
                
                if(Input::has('accountid') == 2){
                    $createdby = "web_register";
                }
                
                
            }
            

            $cust->timestamps   = false;
            $cust->created_by   = $createdby;
            $cust->created_date = date("Y-m-d H:i:s");

            // Get Email Activation Setup
            $Setup = Fees::find(1);
            if($Setup->email_activation == 1){
                $cust->active_status = 0; 
            }else{
                $cust->active_status = 1; 
            }
            

            if($cust->save())
            {
                // Link FB account
                if (Input::has('fid') AND Input::get('fid') != "")
                {
                    $insert = array();
                    $insert['user_id'] = $cust->id;
                    $insert['fid'] = Input::get('fid');
                    $insert['created_at'] = date('Y-m-d H:i:s');

                    $insert['FID_id'] = FID::insertGetId($insert);
                }
                
                // Send Out Email to the member's
                $firstname  = Input::get('firstname');
                $username   = Input::get('username');
                $pass       = Input::get('password');
                $email      = Input::get('email');

                $subject    = "[tmGrocer]: Welcome new member!";
                $user = array(
                            'email' => $email,
                            'name'  => $firstname,
                            'username'  => $username,
                            
                );

                
                
                $data = array(
                            'name'      => $firstname,
                            'username'  => $username,
                            'password'  => $pass,
                            "email_activation" => $Setup->email_activation,
                            'environment'  => Config::get('constants.ENVIRONMENT')
                            // 'subject'   => $subject,
                );

                Mail::send('emails.welcome', $data, function($message) use ($user,$subject)
                {
                    $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                    $message->to($user['email'], $user['name'])->subject($subject);
                });
                
                
                // Send activation email to new registered customer 
//                $ActivationSubject    = "[JOCOM]: Account Activation!";
//                Mail::send('emails.activation', $data, function($message) use ($user,$ActivationSubject)
//                {
//                    $message->from('payment@tmgrocer.com', 'JOCOM');
//                    $message->to($user['email'], $user['name'])->subject($ActivationSubject);
//                });
                
                // Free Coupon for newly register member
//                $campaignDate = date("Y-m-d");
//                if ($campaignDate <= "2016-06-30")
//                {
//                    $new = 1;
//                    if(Input::has('uuid'))
//                    {
//                        $exist = Customer::where('uuid', Input::get('uuid'));
//                
//                        if (count($exist)>0)
//                            $new = 0;
//                    }
//
//                    if ($new == 1)
//                    {
//                        $coupon  = Coupon::find(1226); // related coupon for the campaign
//                        $coupontype = CouponType::where('coupon_id', 1226)->get();
//                        $num = 1;
//
//                        $coupon = Coupon::duplicate($coupon, $coupontype);
//
//                        $subject    = "[JOCOM]: Get RM10 Off For Your First Purchase!";
//
//                        $data = array(
//                            'name'      => $firstname,
//                            'username'  => $username,
//                            'coupon'    => $coupon,
//                        );
//
//                        Mail::send('emails.coupon', $data, function($message) use ($user,$subject)
//                        {
//                            $message->from('payment@jocom.my', 'JOCOM');
//                            $message->to($user['email'], $user['name'])->subject($subject);
//                        });
//                    }
//                }                
                // End of Free Coupon for newly register member

                $data['status'] = '1';
                $data['status_msg'] = '#801';
                // $data['status_msg'] = 'Successfully register member. (Member ID: ' . $cust->id . ')';
            } else {
                $data['status']     = '0';
                $data['status_msg'] = '#802';
                // $data['status_msg'] = 'unable to save.';
            }

        } else {

            $arr_err    = array_flatten($validator->messages());
            $msg_no     = 0;
            foreach ($arr_err as $k=>$v) {
                if($v !== ':message' && $msg_no == 0) {
                    $err_msg[] = $v;
                    $msg_no++;
                }
            }

            //var_dump(Customer::$rules);
            $errors = $validator->messages();
            $arr_rules = Customer::$rules;
            foreach ($arr_rules as $key => $value) {
                $code = "";

                switch ($key) {
                    case "username" : $msg = "Username has already been taken.";$code = "#809";
                            break;

                    case "email"    : $msg = "Email has already been taken.";$code = "#810";
                            break;

                    case "firstname": $msg = "First name must at least has 2 characters.";$code = "#811";
                            break;

                    case "lastname" : $msg = "Last name must at least has 2 characters.";$code = "#812";
                            break;

                    case "ic_passport": $msg = "IC/Passport no. has already been taken.";$code = "#813";
                            break;

                    case "mobile_no": $msg = "Mobile number must only contains number.";$code = "#814";
                            break;

                    case "dob"      : $msg = "Date of birth must be after 1900-01-01";$code = "#815";
                            break;

                    case "password" : $msg = "Password must contains alphabets and numbers.";$code = "#816";
                    
                            break;

                    default:    $msg = "Unknown error.";$code = "#817";
                            break;
                }

                if($errors->has($key)) {
                    $data['status']     = '0';
                    $data['status_msg'] = $code;
                }
            }
        }

        return array('xml_data' => $data);
    }

    public function GetUserDetails($username, $pass, $uuid = NULL)
    {
        $data       = array();
        $valid      = 0;
        $invalidst  = 2;
        $crypt_pass = md5($pass);

        $cust       = DB::table('jocom_user')
                        ->where('username', '=', $username)
                        ->where('password', '=', $crypt_pass)
                        ->where('active_status', '<>', $invalidst)
                        ->first();

        if($cust) {
            $valid = 1;
            $udata                  = array();
            $udata['password']      = Hash::make($pass);
            $udata['modify_date']   = date("Y-m-d H:i:s");
            $udata['modify_by']     = "admin";

            $customer = DB::table('jocom_user')
                            ->where('username', '=', $username)
                            ->update($udata);

        } else {
            $cust   = DB::table('jocom_user as u')
                        ->select('u.*', 'c.name as country2')   //, 's.name as state2')
                        ->leftjoin('jocom_countries as c', 'c.id', '=', 'u.country_id')
                        ->where('username', '=', $username)
                        ->where('u.active_status', '<>', $invalidst)
                        ->first();

            if($cust) {
                if(Hash::check($pass, $cust->password)) $valid = 1;
            }
        }

        if($valid == 1) {

            $data['username']       = $cust->username;
            $data['full_name']      = $cust->full_name;
            $data['ic_no']          = $cust->ic_no;
            $data['address1']       = str_replace("\n", " ", $cust->address1);
            $data['address2']       = str_replace("\n", " ", $cust->address2);
            $data['postcode']       = $cust->postcode;
            $data['state']          = $cust->state;
            $data['city']           = $cust->city;
            $data['dob']            = $cust->dob;
            $data['gender']         = $cust->gender!=null ?$cust->gender:"";
            $data['country']        = $cust->country;
            $data['email']          = $cust->email;
            $data['mobile_no']      = $cust->mobile_no;
            $data['firstname']      = $cust->firstname;
            $data['lastname']       = $cust->lastname;

            $joPoint = PointType::find(1);

            if ($joPoint->status == 1) {
                $point = PointUser::getPoint($cust->id, PointType::JOPOINT, true);

                if ($point && $point->status == 1) {
                    $point = [
                        'name'  => 'TPoint',
                        'value' => isset($point->point) ? $point->point : 0,
                    ];

                    $data['points'] = ['point' => [0 => $point]];
                }
            }

            $data['bcard'] = "";

            $bcardstatus = PointModule::getStatus('bcard_update');

            if ($bcardstatus == 1)
            {
                $bcard = BcardM::where('username', '=', $username)->first();

                if (count($bcard) > 0)
                {
                    $data['bcard'] = $bcard->bcard;
                }
            }

            $data['modify_date']    = $cust->modify_date;
            $data['created_date']   = $cust->created_date;
            
            if ($uuid != NULL)
            {
                $update['uuid'] = $uuid;

                $temp = DB::table('jocom_user')
                        ->where('username', '=', $username)
                        ->update($update);
            }
        } else {

            $data['status_msg']  = '#806';
            // $data['error_message']  = 'Access denied';
        }

        return array('xml_data' => $data);
    }


    
    
    
    public function updateprofile($input=array())
    {
        $data  = array();

        
        //jocom_user db : get username
        $urow = Customer::where('username', '=', Input::get('username'))->first();

        //valid login
        if ($urow != null)
        {
            $error = false;

            $cust               = Customer::find($urow->id);
            // $cust->password     = Hash::make(Input::get('password_confirmation'));
            if(Input::get('password_confirmation')) $cust->password = Hash::make(Input::get('password_confirmation'));
            $cust->full_name    = Input::get('firstname') ." ". Input::get('lastname');
            $cust->firstname    = Input::get('firstname');
            $cust->lastname     = Input::get('lastname');
            $cust->ic_no        = Input::get('ic_no');
            $cust->home_num     = Input::get('home_num');
            $cust->address1     = Input::get('address1');
            $cust->address2     = Input::get('address2');
            $cust->postcode     = Input::get('postcode');
            $cust->state        = Input::get('state');
            $cust->city         = Input::get('city');
            $cust->country_id   = Input::get('country');
            $cust->dob          = Input::get('dob');
            $cust->gender       = Input::get('gender');
            $cust->mobile_no    = Input::get('mobile_no');
            if(Input::get('email') != ''){
                $cust->email        = Input::get('email');
            }
            

            $cust->modify_by    = 'api_update';
            $cust->modify_date  = date('Y-m-d H:i:s');
            $cust->timestamps   = false;

            if($cust->save()) {

                $bcardstatus = PointModule::getStatus('bcard_update');

                if ($bcardstatus == 1)
                {
                    if (Input::has('bcard'))
                    {
                        if (Input::get('bcard') != "")
                        {
                            $update = BcardM::update_card(Input::get('bcard'), Input::get('username'));
                        }                                
                        else
                        {
                            $update = BcardM::where('username', '=', Input::get('username'))->first();

                            if (count($update) > 0)
                                $update->delete();
                        }
                    }
                }
                
                // Success message
                $data['status']     = '1';
                $data['status_msg'] = '#807';
                // $data['status_msg'] = 'Successfully update profile. (Member ID: ' . $urow->id . ')';
            }
            else
            {
                // Invalid request
                $data['status'] = '0';
                $data['status_msg'] = '#805';
                // $data['status_msg'] = 'Invalid request. Please try again.';

            }
        }        

        return array('xml_data' => $data);
    }

    public function ResetPassword($input=array())
    {
        $data       = array();

        if($input['email'] != "")  {
            $column     = 'email';
            $value      = trim($input['email']);
        }

        if($input['username'] != "") {
            $column     = 'username';
            $value      = trim($input['username']);
        }
        
        $ApiLog = new ApiLog ;
        $ApiLog->api = 'RESET_PASSWORD';
        $ApiLog->data = json_encode($input);
        $ApiLog->save();

        if(isset($input['email']) || isset($input['username'])) {
            $customers = DB::table('jocom_user')
                        ->where($column, '=', $value)
                        ->get();

            if(count($customers) > 0) {
                foreach($customers as $customer) {
                    $fullname   = "";
                    $cust_id    = "";
                    $username   = "";

                    $num_of_chracter    = 6;
                    $keys               = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
                    $pass               = "";

                    for($i = 0; $i < $num_of_chracter; $i++) {
                            $pass .= $keys[rand(0, strlen($keys)-1)];
                    }

                    $cust               = Customer::find($customer->id);
                    $cust->password     = Hash::make($pass);
                    $cust->modify_by    = 'api_register';
                    $cust->modify_date  = date('Y-m-d H:i:s');
                    $cust->timestamps   = false;

                    if($cust->save()) {
                    // Send Out Email to the member's
                        $subject = "[tmGrocer]: Your password has been reset!";
                        $user = array(
                            'email' => $customer->email, //$email,
                            'name'  => $customer->full_name,
                        );


                        $data = array(
                                    'name'      => $customer->full_name,
                                    'username'  => $customer->username,
                                    'password'  => $pass,
                                    // 'subject'   => $subject,
                        );
                        
                        $ApiLog = new ApiLog ;
                        $ApiLog->api = 'RESET_PASSWORD';
                        $ApiLog->data = json_encode($user);
                        $ApiLog->save();

                        Mail::send('emails.forgot', $data, function($message) use ($user)
                        {
                            $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                            $message->to($user['email'], $user['name'])->subject('[tmGrocer]: Your password has been reset!');
                        });
                        
                        if( count(Mail::failures()) > 0 ) {

                            $ApiLog = new ApiLog ;
                            $ApiLog->api = 'RESET_PASSWORD_EMAIL';
                            $ApiLog->data = 'FAILED';
                            $ApiLog->save();
                        
                        } else {
                            $ApiLog = new ApiLog ;
                            $ApiLog->api = 'RESET_PASSWORD_EMAIL';
                            $ApiLog->data = 'SENT';
                            $ApiLog->save();
                        }

                        $data['status']     = 1;
                        $data['status_msg'] = '#803';
                        // $data['status_msg'] = 'Password has been reset successfully';
                    }
                }
            } else {
                $data['status'] = 0;
                $data['status_msg'] = '#804';
                // $data['status_msg'] = 'Invalid email or username entered.';
            }

        } else {
            $data['status']     = '0';
            $data['status_msg'] = "#805";
            // $data['status_msg'] = "Invalid request. Please try again.";
        }

        return array('xml_data' => $data);

    }

    public function AddFavAddr($input=array())
    {
        $data       = array();

        $user = Customer::select('id')->where('username', '=', $input['user'])->first();

        if ($user)
        {
            $count = FavouriteAddress::where('username', '=', $input['user'])->count();

            // Max favourite address is 5 only
            if ($count >= FavouriteAddress::MAXADDR)
            {
                $data['status'] = 0;
                $data['status_msg'] = '#820';
                // "Maximum 5 favourite address!",
            }
            else
            {   
                $data['user_id'] = $user->id;
                $data['username'] = $input['user'];
                $data['delivername'] = $input['delivername'];
                $data['delivercontactno'] = $input['delivercontactno'];
                $data['specialmsg'] = $input['specialmsg'];
                $data['deliveradd1'] = $input['deliveradd1'];
                $data['deliveradd2'] = $input['deliveradd2'];
                $data['deliverpostcode'] = $input['deliverpostcode'];
                $data['city'] = $input['city'];
                $data['state'] = $input['state'];
                $data['delivercountry'] = $input['delivercountry'];
                $data['created_at'] = date('Y-m-d H:i:s');

                // change other to non-default
                if ($count > 0 AND $input['default_list'] == 1)
                {
                    $insert = array();
                    $insert['default_list'] = 0;

                    $address = FavouriteAddress::where('username', '=', $input['user'])->update($insert);
                }

                if ($count < 1)
                    $data['default_list'] = 1;
                else
                    $data['default_list'] = ($input['default_list'] == "" OR $input['default_list'] == NULL) ? 0 : $input['default_list'];

                // $newAdd = new FavouriteAddress;
                // $newAdd->user_id = $data['user_id'];
                // $newAdd->save();

                $data['addr_id'] = FavouriteAddress::insertGetId($data);                

                $data['city_name'] = $input['city_name'];
                $data['state_name'] = $input['state_name'];
                $data['country_name'] = $input['country_name'];

                // Checking Customer Profile Address empty or not
                if(strlen($user->address1) == 0){
                    // Update profile address

                    $cityRecord = City::find($data['city']);
                    $stateRecord = State::find($data['state']);
                    $countryRecord = Country::find($data['delivercountry']);

                    $user->address1 = $data['deliveradd1'];
                    $user->address2 = $data['deliveradd2'];
                    $user->postcode = $data['deliverpostcode'];
                    $user->city = $cityRecord->name;
                    $user->state = $stateRecord->name;
                    $user->country = $countryRecord->name;
                    $user->country_id = $countryRecord->id;
                    $user->modify_by = $data['username'];
                    $user->save();
                }

                $data['status'] = 1;
                $data['status_msg'] = '#818';
                // "#818": "Updated successfully.",
            }
        }
        else
        {
            $data['status'] = 0;
            $data['status_msg'] = '#808';
            // "#808": "Invalid username or wrong password.",
        }

        return array('xml_data' => $data);
    }

    public function EditFavAddr($input=array())
    {
        $data       = array();
        $insert     = array();

        $insert['delivername'] = $input['delivername'];
        $insert['delivercontactno'] = $input['delivercontactno'];
        $insert['specialmsg'] = $input['specialmsg'];
        $insert['deliveradd1'] = $input['deliveradd1'];
        $insert['deliveradd2'] = $input['deliveradd2'];
        $insert['deliverpostcode'] = $input['deliverpostcode'];
        $insert['city'] = $input['city'];
        $insert['state'] = $input['state'];
        $insert['delivercountry'] = $input['delivercountry'];

        // change other to non-default
        if ($input['default_list'] == 1)
        {
            $insert['default_list'] = $input['default_list'];

            $update_default = array();
            $update_default['default_list'] = 0;

            $address = FavouriteAddress::where('username', '=', $input['user'])->update($update_default);
        }        

        $address = FavouriteAddress::where('id', '=', $input['addr_id'])->update($insert);

        if ($address)
        {
            $data['status'] = 1;
            $data['status_msg'] = '#818';
            // "#818": "Updated successfully.",
        }
        else
        {
            $data['status'] = 0;
            $data['status_msg'] = '#802';
            // "#802": "Unable to save.",
        }        

        return array('xml_data' => $data);
    }

    public function DeleteFavAddr($input=array())
    {
        $data       = array();

        $address = FavouriteAddress::where('id', '=', $input['addr_id'])->delete();

        if ($address)
        {
            $data['status'] = 1;
            $data['status_msg'] = '#818';
            // "#818": "Updated successfully.",
        }
        else
        {
            $data['status'] = 0;
            $data['status_msg'] = '#802';
            // "#802": "Unable to save.",
        }        

        return array('xml_data' => $data);
    }

    public function ListFavAddr($input=array())
    {
        $data       = array();

        $address = FavouriteAddress::where('username', '=', $input['user'])->get();

        if (count($address) > 0)
        {
            $data['record'] = count($address);

            foreach ($address as $addr)
            {
                $city_name = "";
                $city_name = City::select('name')->find($addr->city);

                $state_name = "";
                $state_name = State::select('name')->find($addr->state);

                $country_name = "";
                $country_name = Country::select('name')->find($addr->delivercountry);

                $data['address'][] = [
                    'addr_id'           => $addr->id,
                    'delivername'       => $addr->delivername,
                    'delivercontactno'  => $addr->delivercontactno,
                    'specialmsg'        => $addr->specialmsg,
                    'deliveradd1'       => $addr->deliveradd1,
                    'deliveradd2'       => $addr->deliveradd2,
                    'deliverpostcode'   => $addr->deliverpostcode,
                    'city'              => $addr->city,
                    'city_name'         => $city_name->name,
                    'state'             => $addr->state,
                    'state_name'        => $state_name->name,
                    'delivercountry'    => $addr->delivercountry,
                    'country_name'      => $country_name->name,
                    'default_list'      => $addr->default_list,
                ];
            }
        }
        else
        {
            $data['record'] = 0;
            // $data['status_msg'] = '#819';
            // “#819”: "Sorry. No data found!",
        }        

        return array('xml_data' => $data);
    }

    public function GetFBDetails($email, $fid, $password, $uuid = NULL)
    {
        $data       = array();
        $insert       = array();
        $valid      = 0;

        $existFID = ApiUser::FIDExist($fid);        

        if (isset($existFID))
        {
            // if link FB account before
            $valid = 1;
            $user_id = $existFID->user_id;
        }
        else
        {
            $existEmail = ApiUser::EmailExist($email);

            if (isset($existEmail))
            {
                // existing customer, request password for verification
                if ($password != "")
                {
                    $cust   = DB::table('jocom_user as u')
                                ->select('u.*', 'c.name as country2')   //, 's.name as state2')
                                ->leftjoin('jocom_countries as c', 'c.id', '=', 'u.country_id')
                                ->where('email', '=', $email)
                                ->first();

                    if($cust)
                    {
                        if(Hash::check($password, $cust->password))
                        {
                            $valid = 1;

                            $insert['user_id'] = $cust->id;
                            $insert['fid'] = $fid;
                            $insert['created_at'] = date('Y-m-d H:i:s');

                            $insert['FID_id'] = FID::insertGetId($insert);

                            $user_id = $cust->id;

                        }
                        else
                        {
                            $data['status'] = 0;
                            $data['status_msg'] = '#806';
                            // "#806": "Access denied.",
                        }
                    }
                }
                else
                {
                    $data['status'] = 0;
                    $data['status_msg'] = 'password';
                }                
            }
            else
            {
                $data['status'] = 0;
                $data['status_msg'] = 'new';
            }
        }

        if($valid == 1)
        {
            $cust   = DB::table('jocom_user as u')
                    ->select('u.*', 'c.name as country2')   //, 's.name as state2')
                    ->leftjoin('jocom_countries as c', 'c.id', '=', 'u.country_id')
                    ->where('u.id', '=', $user_id)
                    ->first();

            $data['username']       = $cust->username;
            $data['full_name']      = $cust->full_name;
            $data['ic_no']          = $cust->ic_no;
            $data['address1']       = str_replace("\n", " ", $cust->address1);
            $data['address2']       = str_replace("\n", " ", $cust->address2);
            $data['postcode']       = $cust->postcode;
            $data['state']          = $cust->state;
            $data['city']           = $cust->city;
            $data['dob']            = $cust->dob;
            $data['country']        = $cust->country2;
            $data['email']          = $cust->email;
            $data['mobile_no']      = $cust->mobile_no;
            $data['firstname']      = $cust->firstname;
            $data['lastname']       = $cust->lastname;

            $joPoint = PointType::find(1);

            if ($joPoint->status == 1) {
                $point = PointUser::getPoint($cust->id, PointType::JOPOINT, true);

                if ($point && $point->status == 1) {
                    $point = [
                        'name'  => 'TPoint',
                        'value' => isset($point->point) ? $point->point : 0,
                    ];

                    $data['points'] = ['point' => [0 => $point]];
                }
            }

            $data['bcard'] = "";

            $bcardstatus = PointModule::getStatus('bcard_update');

            if ($bcardstatus == 1)
            {
                $bcard = BcardM::where('username', '=', $cust->username)->first();

                if (count($bcard) > 0)
                {
                    $data['bcard'] = $bcard->bcard;
                }
            }

            $data['modify_date']    = $cust->modify_date;
            $data['created_date']   = $cust->created_date;

            if ($uuid != NULL)
            {
                $update['uuid'] = $uuid;

                $temp = DB::table('jocom_user')
                        ->where('username', '=', $cust->username)
                        ->update($update);
            }
        }

        return array('xml_data' => $data);
    }

    public function FIDExist($fid)
    {
        $exist = FID::select('user_id')->where('fid', '=', $fid)->orderBy('id', 'desc')->first();

        return $exist;
    }

    public function EmailExist($email)
    {
        $exist = Customer::select('username')->where('email', '=', $email)->first();

        return $exist;
    }
    
    public function GetGoogleDetails($email, $password, $id)
    {
        $data       = array();
        $insert       = array();
        $valid      = 0;

        $existGID = DB::table('google_users')->where('google_id', '=', $id)->first(); 

        if (isset($existGID))
        {
            $valid = 1;
            $user_id = $existGID->user_id;

        }
        else
        {
            $existEmail = DB::table('jocom_user')->where('email','=', $email)->first();

            if (isset($existEmail))
            {
                // existing customer, request password for verification
                if ($password != "")
                {
                    $cust   = DB::table('jocom_user as u')
                                ->select('u.*', 'c.name as country2')   //, 's.name as state2')
                                ->leftjoin('jocom_countries as c', 'c.id', '=', 'u.country_id')
                                ->where('email', '=', $email)
                                ->first();
      
                    if($cust)
                    {
                        if(Hash::check($password, $cust->password))
                        {
                            $valid = 1;
                          
                            $user_id = $cust->id;
                        }
                        else
                        {
                            $data['status'] = 0;
                            $data['status_msg'] = '#806';
                            // "#806": "Access denied.",
                        }
                    }
                }
                else
                {
                    $data['status'] = 0;
                    $data['status_msg'] = 'password';
                }                
            }
            else
            {
                $data['status'] = 0;
                $data['status_msg'] = 'new';
            }
        }

        if($valid == 1)
        {
            $cust   = DB::table('jocom_user as u')
                    ->select('u.*', 'c.name as country2')   //, 's.name as state2')
                    ->leftjoin('jocom_countries as c', 'c.id', '=', 'u.country_id')
                    ->where('u.id', '=', $user_id)
                    ->first();

            $data['username']       = $cust->username;
            $data['full_name']      = $cust->full_name;
            $data['ic_no']          = $cust->ic_no;
            $data['address1']       = str_replace("\n", " ", $cust->address1);
            $data['address2']       = str_replace("\n", " ", $cust->address2);
            $data['postcode']       = $cust->postcode;
            $data['state']          = $cust->state;
            $data['city']           = $cust->city;
            $data['dob']            = $cust->dob;
            $data['country']        = $cust->country2;
            $data['email']          = $cust->email;
            $data['mobile_no']      = $cust->mobile_no;
            $data['firstname']      = $cust->firstname;
            $data['lastname']       = $cust->lastname;

            $joPoint = PointType::find(1);

            if ($joPoint->status == 1) {
                $point = PointUser::getPoint($cust->id, PointType::JOPOINT, true);

                if ($point && $point->status == 1) {
                    $point = [
                        'name'  => 'TPoint',
                        'value' => isset($point->point) ? $point->point : 0,
                    ];

                    $data['points'] = ['point' => [0 => $point]];
                }
            }

            $data['bcard'] = "";

            $bcardstatus = PointModule::getStatus('bcard_update');

            if ($bcardstatus == 1)
            {
                $bcard = BcardM::where('username', '=', $cust->username)->first();

                if (count($bcard) > 0)
                {
                    $data['bcard'] = $bcard->bcard;
                }
            }

            $data['modify_date']    = $cust->modify_date;
            $data['created_date']   = $cust->created_date;

        }        

        return array('xml_data' => $data);
    }
    
    public function GetAppleDetails($id)
    {
        $data       = array();
        $insert       = array();
        $valid      = 0;

        $existGID = DB::table('apple_users')->where('apple_id', '=', $id)->first(); 

        if (isset($existGID))
        {
            $valid = 1;
            $user_id = $existGID->user_id;

        }
        else
        {
            $data['status'] = 0;
            $data['status_msg'] = '#806';
            // "#806": "Access denied.",
        }
        

        if($valid == 1)
        {
            $cust   = DB::table('jocom_user as u')
                    ->select('u.*', 'c.name as country2')   //, 's.name as state2')
                    ->leftjoin('jocom_countries as c', 'c.id', '=', 'u.country_id')
                    ->where('u.id', '=', $user_id)
                    ->first();

            $data['username']       = $cust->username;
            $data['full_name']      = $cust->full_name;
            $data['ic_no']          = $cust->ic_no;
            $data['address1']       = str_replace("\n", " ", $cust->address1);
            $data['address2']       = str_replace("\n", " ", $cust->address2);
            $data['postcode']       = $cust->postcode;
            $data['state']          = $cust->state;
            $data['city']           = $cust->city;
            $data['dob']            = $cust->dob;
            $data['country']        = $cust->country2;
            $data['email']          = $cust->email;
            $data['mobile_no']      = $cust->mobile_no;
            $data['firstname']      = $cust->firstname;
            $data['lastname']       = $cust->lastname;

            $joPoint = PointType::find(1);

            if ($joPoint->status == 1) {
                $point = PointUser::getPoint($cust->id, PointType::JOPOINT, true);

                if ($point && $point->status == 1) {
                    $point = [
                        'name'  => 'TPoint',
                        'value' => isset($point->point) ? $point->point : 0,
                    ];

                    $data['points'] = ['point' => [0 => $point]];
                }
            }

            $data['bcard'] = "";

            $bcardstatus = PointModule::getStatus('bcard_update');

            if ($bcardstatus == 1)
            {
                $bcard = BcardM::where('username', '=', $cust->username)->first();

                if (count($bcard) > 0)
                {
                    $data['bcard'] = $bcard->bcard;
                }
            }

            $data['modify_date']    = $cust->modify_date;
            $data['created_date']   = $cust->created_date;

        }        

        return array('xml_data' => $data);
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
    public function GetAddrBYID($input=array())
    {
        $data = array();

        $address = FavouriteAddress::where('username', '=', $input['user'])->get();

        $city_name = "";
        $city_name = City::select('name')->find($input['city']);
        $city_name = ($city_name ? $city_name->toArray()['name'] : '');

        $state_name = "";
        $state_name = State::select('name')->find($input['state']);
        $state_name = ($state_name ? $state_name->toArray()['name'] : '');

        $country_name = "";
        $country_name = Country::select('name')->find($input['country']);
        $country_name = ($country_name ? $country_name->toArray()['name'] : '');

        $data['record'] = 1;

        $data['address'] = [
            'city'          => $input['city'],
            'city_name'     => $city_name,
            'state'         => $input['state'],
            'state_name'    => $state_name,
            'country'       => $input['country'],
            'country_name'  => $country_name,
        ];

        // echo '<pre>';
        // print_r($data);
        // echo '</pre>';
        // die();

        return array('xml_data' => $data);
    }
    
    public function GetWavpayDetails() {
        die('Invalid Request');
		$user = Customer::where('ref_info', json_encode(['userID' => Input::get('userId')]))->where('created_by', 'api_wavpay_register')->first();

		if ($user) {
			$data = $user->toArray(); // Convert into array
			$c = Country::where('id', $user['country_id'])->select('name')->first();
			if($c) $data['country'] = $c->name;
			$data['address1']       = str_replace("\n", " ", $data['address1']);
			$data['address2']       = str_replace("\n", " ", $data['address2']);
			// grap all the file that need and throw all unneed field on display
			$data = array_filter($data, function($k) {
				return in_array($k, ['username', 'full_name', 'ic_no', 'address1', 'address2', 'postcode', 'state', 'city', 'dob', 'country', 'email', 'mobile_no', 'firstname', 'lastname', 'modify_date', 'created_date']); 
			}, ARRAY_FILTER_USE_KEY);
		} else {
			$data['status'] = 0;
			$data['status_msg'] = '#806';
		}
		return ['xml_data' => $data];
	}
    
}
?>
