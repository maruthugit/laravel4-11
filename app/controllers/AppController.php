<?php
 
class AppController extends BaseController {
 
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function anyIndex()
    {
        $app    = Setting::getAppVersion();
       
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.setting.app_version')->with(array('app' => $app));
    }

    public function anyUpdate()
    {
        $udata['iphone']        = Input::get('iphone');
        $udata['ipad']          = Input::get('ipad');
        $udata['android']       = Input::get('android');
        $udata['tablet']        = Input::get('tablet');
        $udata['updated_by']    = Session::get('username');
        $udata['updated_at']    = date('Y-m-d H:i:s');

        if (Setting::updateApp($udata))
        {
            $insert_audit = General::audit_trail('AppController.php', 'Update()', 'Edit Version', Session::get('username'), 'CMS');
            $app    = Setting::getAppVersion();
            Session::flash('success', 'Setting has been successfully save!');
            
            return View::make(Config::get('constants.SYSTEM_ADMIN').'.setting.app_version')->with(array(
                    'app'       => $app,
                    // 'success'   => 'Setting has been successfully save!',
            ));    
        }
        else { 
            return Redirect::back()
                        ->withInput()
                        ->withErrors('Sorry, failed to save settings.');
        }
    }

}

?>