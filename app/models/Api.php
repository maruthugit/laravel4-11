<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Api extends Eloquent implements UserInterface, RemindableInterface {

    use UserTrait, RemindableTrait;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'jocom_user';

    public function RegisterFbMember($input = array())
    {
        $data       = array();
        $err_msg    = array();
        $validator  = Validator::make($input, Customer::$rules);

        $cust       = new Customer;
        
        if ($validator->passes()) {
            $cust->username     = Input::get('email');
            $cust->full_name    = Input::get('firstname') ." ". Input::get('lastname');
            $cust->firstname    = Input::get('firstname');
            $cust->lastname     = Input::get('lastname');
            $cust->email        = Input::get('email');
            if(Input::get('fid') != "") $cust->fid          = Input::get('fid');
            $cust->timestamps   = false;
            $cust->created_by   = 'api_fb_register';
            $cust->created_date = date("Y-m-d H:i:s");
            
            $num_of_chracter    = 6;
            $keys               = "1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $pass               = ""; // old code
            // for($i = 0; $i < $num_of_chracter; $i++) {
            //         $pass .= $keys[rand(0, strlen($keys)-1)];
            // }
            $pass               = Input::get('password'); 
            
            $cust->password     = Hash::make(Input::get('password'));
            if($cust->save()) {
                // Send Out Email to the member's
                // $fullname   = Input::get('full_name');
                $firstname  = Input::get('firstname');
                $username   = Input::get('email');
                $pass       = Input::get('password');
                $email      = Input::get('email');

                $subject    = "[tmGrocer]: Welcome new member!";

                $user = array(
                            'email' => $email,
                            'name'  => $firstname,
                );

                $edata = array(
                            'name'      => $firstname,
                            'username'  => $username,
                            'password'  => $pass,
                            // 'subject'   => $subject,
                );

                Mail::send('emails.welcome', $edata, function($message) use ($user,$subject)
                {
                    $message->from('enquiries@tmgrocer.com', 'tmGrocer');
                    $message->to($user['email'], $user['name'])->subject($subject);
                });

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

            $data['status']     = '0';
            $data['status_msg'] = implode("\n", $err_msg); 
        }

        return array('xml_data' => $data);
    }
    
    public function RegisterGoogleMember($input = array())
    {
        $data       = array();
        $err_msg    = array();
        $validator  = Validator::make($input, Customer::$rules);
        $cust       = new Customer;
        
        if ($validator->passes()) {
            $cust->username     = Input::get('email');
            $cust->full_name    = Input::get('firstname') ." ". Input::get('lastname');
            $cust->firstname    = Input::get('firstname');
            $cust->lastname     = Input::get('lastname');
            $cust->email        = Input::get('email');
            $cust->password     = Hash::make(Input::get('password'));
            $cust->timestamps   = false;
            $cust->created_by   = 'api_google_register';
            $cust->created_date = date("Y-m-d H:i:s");
            
            if($cust->save()) {
                // Send Out Email to the member's
                // $fullname   = Input::get('full_name');
                $firstname  = Input::get('firstname');
                $username   = Input::get('username');
                $pass       = Input::get('password');
                $email      = Input::get('email');

                $subject    = "[tmGrocer]: Welcome new member!";

                $user = array(
                            'email' => $email,
                            'name'  => $firstname,
                );

                $data = array(
                            'name'      => $firstname,
                            'username'  => $username,
                            'password'  => $pass,
                            // 'subject'   => $subject,
                );

                Mail::send('emails.welcome', $data, function($message) use ($user,$subject)
                {
                    $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                    $message->to($user['email'], $user['name'])->subject($subject);
                });

                $data['status'] = '1';
                $data['status_msg'] = '#801';
                // $data['status_msg'] = 'Successfully register member. (Member ID: ' . $cust->id . ')';

                DB::table('google_users')->insert(array('user_id'=>$cust->id, 'google_id'=>Input::get('id'), 'created_at'=>date('Y-m-d H:i:s')));

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
                //echo "<br>[".$key."] ".$value;
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
                    
                    case "mobile_no": $msg = "Mobile number must only contains number.";$code = "#814";
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

            
            
            //echo "msg = ".$msg;
                        
             //implode("\n", $err_msg); 
        }
       
        return array('xml_data' => $data);
    }
    
    public function RegisterAppleMember($input = array())
    {
        $data       = array();
        $err_msg    = array();
        $validator  = Validator::make($input, Customer::$rules);
        $cust       = new Customer;
        $appleid = Input::get('id');
        $flag = 0;
        // echo $appleid;
        $appresult = DB::table('apple_users')->where('apple_id','=',$appleid)->get();
        if(count($appresult) > 0){
            $flag = 1;
        }
        
    
        if ($validator->passes()) {

            if($flag == 0){
                $cust->username     = Input::get('username');
                $cust->full_name    = Input::get('firstname') ." ". Input::get('lastname');
                $cust->firstname    = Input::get('firstname');
                $cust->lastname     = Input::get('lastname');
                $cust->email        = Input::get('email');
                $cust->password     = Hash::make(Input::get('password'));
                $cust->timestamps   = false;
                $cust->created_by   = 'api_apple_register';
                $cust->created_date = date("Y-m-d H:i:s");
                
                if($cust->save()) {
                    // Send Out Email to the member's
                    // $fullname   = Input::get('full_name');
                    $firstname  = Input::get('firstname');
                    $username   = Input::get('username');
                    $pass       = Input::get('password');
                    $email      = Input::get('email');

                    $subject    = "[tmGrocer]: Welcome new member!";

                    $user = array(
                                'email' => $email,
                                'name'  => $firstname,
                    );

                    $data = array(
                                'name'      => $firstname,
                                'username'  => $username,
                                'password'  => $pass,
                                // 'subject'   => $subject,
                    );

                    Mail::send('emails.welcome', $data, function($message) use ($user,$subject)
                    {
                        $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                        $message->to($user['email'], $user['name'])->subject($subject);
                    });

                    $data['status'] = '1';
                    $data['status_msg'] = '#801';
                    // $data['status_msg'] = 'Successfully register member. (Member ID: ' . $cust->id . ')';

                    DB::table('apple_users')->insert(array('user_id'=>$cust->id, 'apple_id'=>Input::get('id'), 'created_at'=>date('Y-m-d H:i:s')));

                } else {
                    $data['status']     = '0';
                    $data['status_msg'] = '#802';
                    // $data['status_msg'] = 'unable to save.';
                }
            }
            else{
                $data['status']     = '0';
                $data['status_msg'] = '#821';
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
                //echo "<br>[".$key."] ".$value;
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
                    
                    case "mobile_no": $msg = "Mobile number must only contains number.";$code = "#814";
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

            
            
            //echo "msg = ".$msg;
                        
             //implode("\n", $err_msg); 
        }
       
        return array('xml_data' => $data);
    }
    
    public function RegisterMember($input = array())
    {
        $data       = array();
        $err_msg    = array();
        $validator  = Validator::make($input, Customer::$rules);
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
            if(Input::get('state') != "")       $cust->state_id     = Input::get('state');
            if(Input::get('city') != "")        $cust->city         = Input::get('city');
            if(Input::get('country') != "")     $cust->country_id   = Input::get('country');
            if(Input::get('dob') != "")         $cust->dob          = Input::get('dob');
            if(Input::get('mobile_no') != "")   $cust->mobile_no    = Input::get('mobile_no');
            if(Input::get('usr_agree') != "")   $cust->usr_agree    = Input::get('usr_agree');
            if(Input::get('pdpa') != "")        $cust->pdpa         = Input::get('pdpa');
            $cust->timestamps   = false;
            $cust->created_by   = 'api_register';
            $cust->created_date = date("Y-m-d H:i:s");
            
            if($cust->save()) {
                // Send Out Email to the member's
                // $fullname   = Input::get('full_name');
                $firstname  = Input::get('firstname');
                $username   = Input::get('username');
                $pass       = Input::get('password');
                $email      = Input::get('email');

                $subject    = "[tmGrocer]: Welcome new member!";

                $user = array(
                            'email' => $email,
                            'name'  => $firstname,
                );

                $data = array(
                            'name'      => $firstname,
                            'username'  => $username,
                            'password'  => $pass,
                            // 'subject'   => $subject,
                );

                Mail::send('emails.welcome', $data, function($message) use ($user,$subject)
                {
                    $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                    $message->to($user['email'], $user['name'])->subject($subject);
                });

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
                //echo "<br>[".$key."] ".$value;
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
                    // case "password" : $msg = "Password must contains Alpha-numeric characters, as well as dashes and underscores.";$code = "#816";
                            break;  
                    
                    default:    $msg = "Unknown error.";$code = "#817";
                            break;  
                }
                
                if($errors->has($key)) {
                	$data['status']     = '0';
           	 		$data['status_msg'] = $code;
                }
            }

			
            
            //echo "msg = ".$msg;
                        
             //implode("\n", $err_msg); 
        }
       
        return array('xml_data' => $data);
    }

    public function MemberForgot($input=array()) 
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

                        Mail::send('emails.forgot', $data, function($message) use ($user)
                        {
                            $message->from('customersupport@tmgrocer.com', 'tmGrocer');
                            $message->to($user['email'], $user['name'])->subject('[tmGrocer]: Your password has been reset!');
                        });

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

    public function array_flatten($array) 
    { 
        if (!is_array($array)) { 
            return FALSE; 
        } 
        $result = array(); 
        foreach ($array as $key => $value) { 
            if (is_array($value)) { 
              $result = array_merge($result, array_flatten($value)); 
            } 
            else { 
              $result[$key] = $value; 
            } 
        } 
        return $result; 
    } 

    function comments() {
        $data = array();
        $xmldata = array();
        $get = array();
        $enc = 'UTF-8';
        
        if($input('enc')) 
            $enc = trim($input['enc']);
        
        // Buyer Username
        $username = trim($input['username']);
        $sku = trim($input['sku']); // Product SKU

        $user = DB::table('jocom_user')->select('id')
                                    ->where('username', '=', $username)
                                    ->first();
        
        $product = DB::table('jocom_products')->select('id')
                                    ->where('sku', '=', $sku)
                                    ->first();

        $data['comment'] = trim($input['comment']); // Comment
        $data['rating'] = trim($input['rating']); // Comment Rating (0-5) default 0
        
        if(!is_numeric($data['rating']) && $data['rating'] < 1 && $data['rating'] > 5)
            $data['rating'] = 0;
        
        if($data['user_id'] === false) {
            $xmldata['status'] = '0';
            $xmldata['status_msg'] = '#301';
            // $xmldata['status_msg'] = 'Invalid username.';
        } else if($data['product_id'] === false) {
            $xmldata['status'] = '0';
            $xmldata['status_msg'] = '#302';
            // $xmldata['status_msg'] = 'Invalid product SKU.';
        } else {
            // $this->api_m->insert_comment($data);
            $id = DB::table('jocom_comments')->insertGetId(array(
                            'comment_date' => date('Y-m-d H:i:s', time()),
                            'user_id' => $user->id,
                            'product_id' => $product->id,
                            'comment' => $data['comment'],
                            'rating' => $data['rating'],
                            'insert_by' => $data["insert_by"],
                            'insert_date' => date('Y-m-d H:i:s'),
                            'modify_date' => date('Y-m-d H:i:s'))
            );
            
            // $sql = "INSERT INTO `jocom_comments` (" . implode(", ", array_keys($tmp_insert)) . ") VALUES (" . implode(", ", array_values($tmp_insert)) . ")";

            $xmldata['status'] = '1';
            $xmldata['status_msg'] = '#303';
            // $xmldata['status_msg'] = 'Comment saved.';
        }
        
        // header('Content-type: text/xml');
        // header('Pragma: public');
        // header('Cache-control: private');
        // header('Expires: -1');
        // $this->load->view('xml_v', array('enc' => $enc, 'xml_data' => $xmldata));
        return array('xml_data' => $xmldata);
    }

     public function updateprofile($input=array()) 
    {
            
            $data  = array();
            
            //check user login - username and password
            $login = Customer::check_login(Input::get('username'), Input::get('password'));

            
            if ($login == 'yes')
            {
                //jocom_user db : get username
                $urow = Customer::where('username', '=', Input::get('username'))->first();

                 

                //valid login
                if ($urow != null)
                {
                    $error = false;

                    $cust               = Customer::find($urow->id); 
                    $cust->password     = Hash::make(Input::get('password_confirmation')); 
                    $cust->full_name    = Input::get('firstname') ." ". Input::get('lastname');
                    $cust->firstname    = Input::get('firstname');
                    $cust->lastname     = Input::get('lastname');
                    $cust->ic_no        = Input::get('ic_no');
                    $cust->home_num     = Input::get('home_num');
                    $cust->address1     = Input::get('address1');
                    $cust->address2     = Input::get('address2');
                    $cust->postcode     = Input::get('postcode');
                    $cust->state_id     = Input::get('state');
                    $cust->city         = Input::get('city');
                    $cust->country_id   = Input::get('country');
                    $cust->dob          = Input::get('dob');
                    $cust->mobile_no    = Input::get('mobile_no');
                    
                    $cust->modify_by    = 'api_update';
                    $cust->modify_date  = date('Y-m-d H:i:s');
                    $cust->timestamps   = false;
                    
                
                    if($cust->save()) {

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
            }
            
            else
            { 
                // Invalid User
                $data['status']     = '0';
                $data['status_msg'] = '#808';
                // $data['status_msg'] = 'Invalid username or wrong password.';
            }
       
            return array('xml_data' => $data);
    
    }
    
    public static function getAPIProduct(){
            
            $products = array(29635,29637,29636);

        return DB::table('jocom_products AS JP')
                    ->select('JP.id AS ProductID','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                            'JPP.*')
                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->whereIn('JP.id',$products)
                    ->where('JP.status', 1)
                    ->where('JPP.default', 1)
                    // ->orderBy('JP.id','DESC')
                    ->get();
        
    }
    
    public static function getAPIProductInfo($productid){
            
        return DB::table('jocom_products AS JP')
                    ->select('JP.id AS ProductID','JP.sku','JP.name','JP.qrcode','JP.img_1','JP.img_2','JP.img_3','JP.gst','JP.gst_value','JP.weight','JP.halal','JP.freshness','JP.description AS ProductDescription','JP.delivery_time',
                            'JPP.*')
                    ->join('jocom_product_price AS JPP', 'JPP.product_id', '=', 'JP.id')
                    ->where('JP.id',$productid)
                    ->where('JP.status', 1)
                    ->where('JPP.default', 1)
                    ->first();
        
    }
    
    
    public function RegisterWavPayMember($input = []){
        die('Invalid Request');
		$enc_ref = json_encode(["userID" => preg_replace('/[^\w- ]+/', '', (Input::get('WavPayUserID') ? Input::get('WavPayUserID') : ''))]);
		$exist = Customer::where('full_name', Input::get('fullname'))->where('email', Input::get('email'))->where('ref_info', $enc_ref)->first();
		if($exist){
			$data = [
				'name'          => $exist->full_name,
				'username'      => $exist->username,
				'status'        => '1',
				'status_msg'    => '#801'
			];
		}else{
			$unique_name            = preg_replace('/[^\w]+/', '', strtolower(Input::get('fullname')));
			$unique_name            = explode('\r\n', chunk_split($unique_name, 6, '\r\n'))[0];
			$unique_name            = 'wavpay_' . date('Ymd') . '_' . $unique_name;
			
			// Cusotmer Data
			$cust                   = new Customer;
			$cust->username         = $unique_name;
			$cust->full_name        = Input::get('fullname');
			$cust->firstname        = Input::get('firstname');
			$cust->lastname         = Input::get('lastname');
			$cust->email            = Input::get('email');
			$cust->ic_no            = Input::get('icno');
			$cust->password         = Hash::make(openssl_random_pseudo_bytes(12));
			$cust->timestamps       = false;
			$cust->created_by       = 'api_wavpay_register';
			$cust->created_date     = date("Y-m-d H:i:s");
			$cust->ref_info         = $enc_ref;
			
			if($cust->save()) {
				$user = [
					'email' => Input::get('email'),
					'name'  => Input::get('fullname'),
				];

				$data = [
					'name'          => Input::get('fullname'),
					'username'      => $unique_name,
					'password'      => 'Generate by hash system byte',
					'status'        => '1',
					'status_msg'    => '#801',
				];

				Mail::send('emails.welcome', $data, function($message) use ($user) {
					$message->from('customersupport@tmgrocer.com', 'tmGrocer');
					$message->to($user['email'], $user['name'])->subject("[tmGrocer]: Welcome new member!");
				});
			} else {
				$data = [
					'status' => '0',
					'status_msg' => '#802'
				];
			}
		}

		return ['xml_data' => $data];
	}

    

}
?>