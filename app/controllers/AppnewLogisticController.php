<?php

use Helper\ImageHelper as Image;
 
class AppnewLogisticController extends BaseController {
 
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function anyIndex()
    {
        $app    = SettingLogistic::getAppnewVersion();
       
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.setting.appversion_logistic')->with(array('appnew' => $app));
    }

    public function anyUpdate()
    {   
        
        
        try{
            
        
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
        $installer_android      = Input::file('installer_android');
        
        $iphone         = Input::get('iphone');
        $iphtype        = Input::get('app_iphone');
        $iphfeatures    = Input::get('iphonefeatures');
        $installer_iphone      = Input::file('installer_iphone');
        
        $ipad           = Input::get('ipad');
        $ipadtype       = Input::get('app_ipad');
        $ipadfeatures   = Input::get('ipadfeatures');
        $installer_ipad      = Input::file('installer_ipad');
        
        $tablet         = Input::get('tablet');
        $tablettype     = Input::get('app_tablet');
        $tabtfeatures   = Input::get('tabletfeatures');
        $installer_tablet      = Input::file('installer_tablet');
        
        $updatedby      = Session::get('username');
        $updatedat      = date('Y-m-d H:i:s');
        
       


        if(Input::get('android')){
            if(isset($android) && $android != '')
            {
                $flag = 1;
                
                $installer = Input::file('installer_android');

                if(Input::hasFile('installer_android')){
   
                    $file_name = $installer_android->getClientOriginalName(); 
                    $path = Config::get('constants.LOGISTIC_APP_INSTALLER');
                    $hasFile = true;
                    
                }else{
                    
                    $file_name = '';
                    $hasFile = false;
                }
            
                $arrayandroid = array('apptype'      => $andtype,
                                     'version'      => $android,
                                     'features'     => $andfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat ,  
                                     'installer_filename' => $file_name
                                     );

                $result = SettingLogistic::updateAppnew($arrayandroid,$andtype);


            }
            
            
        }
        
        if(Input::get('iphone')){
            if(isset($iphone) && $iphone != '')
            {   
                
                $installer = Input::file('installer_iphone');
                $file_name = $installer_iphone->getClientOriginalName(); 
                $path = Config::get('constants.LOGISTIC_APP_INSTALLER');

                $flag = 1;
                $arrayiphone = array('apptype'      => $iphtype,
                                     'version'      => $iphone,
                                     'features'     => $iphfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat  ,
                                     'installer_filename' => $file_name

                                     );
                
                $result = SettingLogistic::updateAppnew($arrayiphone,$iphtype);
            }
        }
        
        if(Input::get('ipad')){
            if(isset($ipad) && $ipad != '')
            {
               
                $installer = Input::file('installer_ipad');
                $file_name = $installer_ipad->getClientOriginalName(); 
                $path = Config::get('constants.LOGISTIC_APP_INSTALLER');
                
                $flag = 1;
                $arrayipad   = array('apptype'      => $ipadtype,
                                     'version'      => $ipad,
                                     'features'     => $ipadfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat ,
                                     'installer_filename' => $file_name

                                     );
                                     
                $result = SettingLogistic::updateAppnew($arrayipad,$ipadtype);
                
            }
        }
        
        if(Input::get('tablet')){
            if(isset($tablet) && $tablet != '')
            {
                $installer = Input::file('installer_tablet');
                
                $file_name = $installer_tablet->getClientOriginalName(); 
                $path = Config::get('constants.LOGISTIC_APP_INSTALLER');
                
                $flag = 1;
                $arraytablet = array('apptype'      => $tablettype,
                                     'version'      => $tablet,
                                     'features'     => $tabtfeatures,
                                     'default'      => 1, 
                                     'updated_by'   => $updatedby,   
                                     'updated_at'   => $updatedat ,
                                     'installer_filename' => $file_name

                                     );
                $result = SettingLogistic::updateAppnew($arraytablet,$tablettype);
                
            }
        }
      
        if($result){
            
            if($hasFile){
                $path = Config::get('constants.LOGISTIC_APP_INSTALLER');
                $upload_file_succ = $installer->move($path, $file_name);   
            }
              
        }


        // $udata['android']           = Input::get('android');
        // $udata['androidfeatures']   = Input::get('androidfeatures');
        // $udata['iphone']            = Input::get('iphone');
        // $udata['iphonefeatures']    = Input::get('iphonefeatures');
        // $udata['ipad']              = Input::get('ipad');
        // $udata['ipadfeatures']      = Input::get('ipadfeatures');
        // $udata['tablet']            = Input::get('tablet');
        // $udata['tabletfeatures']    = Input::get('tabletfeatures');
        // $udata['updated_by']    = Session::get('username');
        // $udata['updated_at']    = date('Y-m-d H:i:s');

        if ($flag == 1)
        {
            $insert_audit = General::audit_trail('AppnewController.php', 'Update()', 'Update Version', Session::get('username'), 'CMS');
            $app    = SettingLogistic::getAppnewVersion();
            Session::flash('success', 'Setting has been successfully save!');
            
            return View::make(Config::get('constants.SYSTEM_ADMIN').'.setting.appversion_logistic')->with(array(
                    'appnew'       => $app,
                    //'success'   => 'Setting has been successfully save!',
            ));    
        }
        else { 
            return Redirect::back()
                        ->withInput()
                        ->withErrors('Sorry, failed to save settings.');
        }
        
        }catch(exception $ex){
            echo $ex->getMessage();
        }
    }


    public function anyApphistory(){

        try{
                $result = DB::table('appversion_logistic')
                            ->select('id','apptype','version','installer_filename','features','updated_by','updated_at','default')
                            ->orderby('id','DESC');

                    return Datatables::of($result)
                     ->edit_column('default', '
                            @if($default == 0)
                                <p class="text-danger font-weight-bold">In Active</p>
                            @elseif ($default == 1)  
                                <p class="text-success font-weight-bold">Active</p>
                            @endif
                            ')
                        ->add_column('download_link', function($result){
                            
                            if($result->installer_filename != ''){
                                $path_file = Config::get('constants.LOGISTIC_APP_INSTALLER')."/".$result->installer_filename;
                                $url = Image::link($path_file); 
                                
                                return '<a class="btn btn-default btn-sm" target="_blank" href="'.$url.'">Download <i class="fa fa-file-o" ></i></a>';
                            }
                        })
                        
                        ->make();   



            } catch (Exception $ex) {
                echo $ex->getMessage();
            }

    }



}

?>