<?php
 
class AppnewController extends BaseController {
 
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function anyIndex()
    {
        $app    = Setting::getAppnewVersion();
       
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.setting.appversion')->with(array('appnew' => $app));
    }

    public function anyUpdate()
    {   
        $flag = 0;
        $arrayandroid = array();
        $arrayiphone  = array();
        $arrayipad    = array();
        $arraytablet  = array();

        // echo '<pre>';
        // print_r(Input::All());
        // echo '</pre>';

      // die();


        $android        = Input::get('android');
        $andtype        = Input::get('app_android');
        $andfeatures    = Input::get('androidfeatures');
        $iphone         = Input::get('iphone');
        $iphtype        = Input::get('app_iphone');
        $iphfeatures    = Input::get('iphonefeatures');
        $ipad           = Input::get('ipad');
        $ipadtype       = Input::get('app_ipad');
        $ipadfeatures   = Input::get('ipadfeatures');
        $tablet         = Input::get('tablet');
        $tablettype     = Input::get('app_tablet');
        $tabtfeatures   = Input::get('tabletfeatures');
        $updatedby      = Session::get('username');
        $updatedat      = date('Y-m-d H:i:s');


        if(Input::get('android')){
            if(isset($android) && $android != '')
            {
                $flag = 1;
                $arrayandroid = array('apptype'      => $andtype,
                                     'version'      => $android,
                                     'features'     => $andfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat   

                                     );

                $result = Setting::updateAppnew($arrayandroid,$andtype);


            }
            
        }
        
        if(Input::get('iphone')){
            if(isset($iphone) && $iphone != '')
            {

                $flag = 1;
                $arrayiphone = array('apptype'      => $iphtype,
                                     'version'      => $iphone,
                                     'features'     => $iphfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat   

                                     );
                $result = Setting::updateAppnew($arrayiphone,$iphtype);
            }
        }
        
        if(Input::get('ipad')){
            if(isset($ipad) && $ipad != '')
            {
                $flag = 1;
                $arrayipad   = array('apptype'      => $ipadtype,
                                     'version'      => $ipad,
                                     'features'     => $ipadfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat   

                                     );
                $result = Setting::updateAppnew($arrayipad,$ipadtype);
            }
        }
        
        if(Input::get('tablet')){
            if(isset($tablet) && $tablet != '')
            {
                $flag = 1;
                $arraytablet = array('apptype'      => $tablettype,
                                     'version'      => $tablet,
                                     'features'     => $tabtfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat   

                                     );
                $result = Setting::updateAppnew($arraytablet,$tablettype);
            }
        }


        if ($flag == 1)
        {
            $insert_audit = General::audit_trail('AppnewController.php', 'Update()', 'Update Version', Session::get('username'), 'CMS');
            $app    = Setting::getAppnewVersion();
            Session::flash('success', 'Setting has been successfully save!');
            
            return View::make(Config::get('constants.SYSTEM_ADMIN').'.setting.appversion')->with(array(
                    'appnew'       => $app,
                    // 'success'   => 'Setting has been successfully save!',
            ));    
        }
        else { 
            return Redirect::back()
                        ->withInput()
                        ->withErrors('Sorry, failed to save settings.');
        }
    }


    public function anyApphistory(){
    
        try{
                $result = DB::table('appversion')
                            ->select('id','apptype','version','features','updated_by','updated_at','default')
                            ->orderby('id','DESC');

                    return Datatables::of($result)
                         ->edit_column('default', '
                            @if($default == 0)
                                <p class="text-danger font-weight-bold">In Active</p>
                            @elseif ($default == 1)  
                                <p class="text-success font-weight-bold">Active</p>
                            @endif
                            ')
                        
                        ->make();   



            } catch (Exception $ex) {
                echo $ex->getMessage();
            }

    }



}

?>