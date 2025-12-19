<?php

class LoginController extends BaseController {

    public function getIndex()
    {
        if (Auth::check())
        {
            // Redirect user to dashboard if user already logged in
            return Redirect::intended('home');
        }

        return View::make('login.index');
    }

    public function postIndex()
    {
        $username   = Input::get('username');
        $password   = Input::get('password');
        $ip         = Request::getClientIp();
        $date       = date('Y-m-d H:i:s');
        $login      = new Login;
        $count      = $login->CheckFailedAttempt($username, $ip);

        if($count < 5 ) {
            if (Auth::attempt(['username' => $username, 'password' => $password, 'active_status' => 1]))
            {
                $login->add_attempt($username, $ip, $date, 1);
                $username   = Auth::user()->username;
                $role_id    = Auth::user()->role_id;
                $sys_admin_id = Auth::user()->id;
   
                $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sys_admin_id)
                        ->where("status",1)->first();
                $Region = Region::find($SysAdminRegion->region_id);

                // Set region code 
                if(($SysAdminRegion->region_id == 0) || ($Region->region_code == 'HQ')){
                    $region_code = 'ALL'; 
                    $branch_access = 0; 
                }else{
                    $region_code = $Region->region_code; 
                    $branch_access = '1'; 
                }
                
                $user = User::find(Auth::id());
                $user->last_login = date('Y-m-d H:i:s');
                $user->timestamps = false;
                $user->save();

                Session::put('role_id', $role_id);
                Session::put('username', $username);
                Session::put('user_id', Auth::id());
                Session::put('region_access', $SysAdminRegion->region_id);
                Session::put('region_code', $region_code);
                Session::put('branch_access', $branch_access);
                Session::put('user_photo', $user->user_photo == '' || $user->user_photo == null ? '/images/asset/icon/people.png' : '/images/userprofile/'.$user->user_photo);
                Session::put('full_name', $user->full_name );

                

                return Redirect::intended('/home')->with('success', 'You have logged in successfully.');

            } else {

                $user = User::where('username', $username)->first();

                if( $user && $user->password == md5($password)) {
                    $login->add_attempt($username, $ip, $date, 1);

                    $user->password     = Hash::make($password);
                    $user->timestamps   = false;
                    $user->modify_by    = 'Admin';
                    $user->modify_date  = date('Y-m-d H:i:s');

                    if($user->save()) {
                        $user2 = User::where('username', $username)->first();

                        Auth::login($user2, true);
                        Session::put('role_id', $user2->role_id);
                        Session::put('username', $user2->username);
                        Session::put('user_id', Auth::id());

                        return Redirect::intended('/home')->with('success', 'You have logged in successfully.');
                    }

                } else {
                    $login->add_attempt($username, $ip, $date, 0);

                    return Redirect::back()
                        ->withInput()
                        ->withErrors('Login attempt '. $count .': The username/password combo does not exist.');
                }
            }
        } else {
            return Redirect::back()
                        ->withInput()
                        ->withErrors('You have login for more than 5 times. Please try again after 30 minutes.');
        }




    }

    public function anyLogin()
    {
//        echo "<br>Logging in...";
        return Redirect::to('/');
    }

    public function anyLogout()
    {
    	Auth::logout();

    	Session::flash('success', 'You had logged out!');
        return Redirect::to('/')->with('success', 'You are logged out!')
                    ->header('Cache-Control', 'no-store, no-cache, must-revalidate')
                    ->header('Pragma', 'no-cache')
                    ->header('Expires', 'Tue, 1 Jan 1980 00:00:00 GMT');

//        echo "<br>Logging out...";
//        Session::flush();
//        return Redirect::to('/')->with('success', 'You are logged out!'); // redirect back to login page

    }

    public function missingMethod($parameters = array())
    {
        echo "Invalid URL!";
    }

}
?>
