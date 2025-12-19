<?php

class DriverController extends BaseController {

    public function __construct()
    {
        $this->beforeFilter('auth');
    }
 
    /**
     * Display a listing of the driver.
     *
     * @return Response
     */
    public function anyIndex()
    {
         return View::make('logistic/driver.index', ['driver' => $driver]);
    }

  
    /**
     * Show the form for creating a new driver.
     *
     * @return Response
     */
    public function anyCreate()
    {   
        // echo "aiheoiauf";
        // $driver  = new LogisticDriver;
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
        
        $country_id = $product->region_country_id;
        if(count($regions) <= 0){
            $regions      = Region::where("country_id",$country_id)->get();
        }

        if($SysAdminRegion->region_id != 0){
            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
        }else{
            $regions      =  Region::where("activation",1)->get();
        }
        
        
        $type    = array("1" => "Supervisor", "0" => "Driver"); 
        $status  = array("1" => "Active", "0" => "Inactive"); 

        return View::make('logistic/driver.create', ['type' => $type, 'status' => $status, 'regions' => $regions]);   
    }



    /**
     * Display a listing of the drivers on datatable.
     *
     * @return Response
     */
    public function anyDriver() {     
        
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                ->where("status",1)->first();
        if($SysAdminRegion->region_id == 0){
            $driver = LogisticDriver::select('id', 'name', 'contact_no', 'type', 'status')
                                        ->where('status','=',1);
                                       

        }else{
            $driver = LogisticDriver::select('id', 'name', 'contact_no', 'type', 'status')
                ->where("region_id",$SysAdminRegion->region_id)
                ->where('status','=',1);
              
        }
        
//        $region_access = Session::get('region_access');
//        if($region_access == 0){
//            $driver = LogisticDriver::select('id', 'name', 'contact_no', 'type', 'status')
//                            ->where('status', '<', '2');
//        }else{
//            $driver = LogisticDriver::select('id', 'name', 'contact_no', 'type', 'status')
//                            ->where('region_id', $region_access)
//                            ->where('status', '<', '2');
//        }
                        
        return Datatables::of($driver)
                ->edit_column('type', '
                                 @if($type == 1)
                                    <p>Supervisor</p>
                                @else
                                  <p>Driver</p>
                                @endif
                                  ')
                  ->edit_column('status', '
                                @if($status == 1)
                                    <p class="text-success">Active</p>
                                @else
                                    <p class="text-danger">Inactive</p>
                                @endif
                                ')
                ->add_column('Action', '
                        <a class="btn btn-primary" title="" data-toggle="tooltip" href="/driver/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                        @if ( Permission::CheckAccessLevel(Session::get(\'role_id\'), 8, 9, \'AND\'))
                        <a id="deleteDriver" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$username}}" href="/driver/delete/{{$id}}"><i class="fa fa-remove"></i></a>
                        @endif
                    ')
                ->make();
    }

 /**
     * Store a newly created driver in storage.
     *
     * @return Response
     */
    public function anyStore()
    {
        $driver       = new LogisticDriver;
        $driver_input = Input::all();
        $validator    = Validator::make(Input::all(), LogisticDriver::$rules);
    
          
        if ($validator->passes()) {
            $driver->username       = Input::get('username');
            $driver->password       = Hash::make(Input::get('password'));
            $driver->name           = Input::get('name');
            $driver->contact_no     = Input::get('contact_no');
            $driver->device_id      = Input::get('device_id');
            $driver->type           = Input::get('type');
            $driver->status         = Input::get('status');
            $driver->region_id      = Input::get('region_access');
            $driver->is_logistic_dashboard      = Input::get('logistic_dashboard');
            
            //$driver->save();
        
            if($driver->save()) 
            {
                $insert_audit = General::audit_trail('DriverController.php', 'Store()', 'Add New Driver', Session::get('username'), 'CMS');
                //return Redirect::to('driver/index/'.$id)->with('success', 'Driver(ID: '.$driver->id.') added successfully.');
                if(Input::get('device_id')){

                  $row = array('username'            => Input::get('username'),
                               'device_id'           => Input::get('device_id'),
                    );

                  $insert = LogisticDriver::InsertDriver($row); 

                }

                 

                if(Input::file('profileimg')){

                  $file                   = Input::file('profileimg');
                  $extension              = $file->getClientOriginalExtension();
                  $timevalue       = time();
                  $file_name       = $timevalue . "." .$extension; 
                  $dest_path       = Config::get('constants.DRIVER_PROFILE_FILE_PATH');
                  $upload_success  = Input::file('profileimg')->move($dest_path, $file_name);

                  if ($upload_success) {
                      $driver = LogisticDriver::find($driver->id);
                      $driver->filename = $file_name;
                      $driver->save();

                      return Redirect::to('driver/index/'.$id)->with('success', 'Driver(ID: '.$driver->id.') added successfully.');
                  } else {
                      echo "<br>Upload file FAILED!";
                  }
                }
                else
                {
                  return Redirect::to('driver/index/'.$id)->with('success', 'Driver(ID: '.$driver->id.') added successfully.');
                } 
                
            }
        } 
        else 
        {
            return Redirect::back()
                            ->withInput()
                            ->withErrors($validator)->withInput();
        }
        
    }


    /**
     * Show the form for editing the specified driver.
     *
     * @param  int  $id
     * @return Response
     */
    
    public function anyEdit($id)
    {
        $driver       = LogisticDriver::find($id);
        $regions      = Region::where("activation",1)->get();
        $type         = array("1" => "Supervisor", "0" => "Driver");
        $status       = array("1" => "Active", "0" => "Inactive");  
       
        $sysAdminInfo = User::where("username",Session::get('username'))->first();
        $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$sysAdminInfo->id)
                    ->where("status",1)->first();
        
        if($SysAdminRegion->region_id != 0){
            $regions = DB::table('jocom_region')->where("id",$SysAdminRegion->region_id)->get();
        }else{
            $regions      =  Region::where("activation",1)->get();
        }
        
        $devid = LogisticDriver::getdevice_id($driver->id);   
        
        return View::make('logistic/driver.edit')->with(array(
                        'driver'  => $driver, 
                        'type'    => $type,
                        'status'  => $status,
                        'user_region'  => $driver->region_id,
                        'regions'  => $regions,
                        'deviceid' => $devid,
            
        ));
    }

    /**
     * Update the specified driver in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id)
    {
        $arr_input_all  = Input::all();
        $arr_validate   = array();
        $arr_input      = array();
        $filename      = "";

        if(Input::file('profileimg')){

          $file       = Input::file('profileimg');
          $extension  = $file->getClientOriginalExtension();
          $timevalue  = time();
          $filename   = $timevalue.'.'.$extension;
        }
        $arr_input_all['filename']=$filename;  
        
        $arr_validate   = LogisticDriver::GetUpdateRules($arr_input_all);
        $arr_input      = LogisticDriver::GetUpdateInputs($arr_input_all);

        $validator      = Validator::make($arr_input, $arr_validate);

        
        $arr_udata  = LogisticDriver::GetUpdateDbDetails($arr_input);
        
        if(Input::get('device_id')){  
             $devid = Input::get('device_id');
             $array = array('device_id' => $devid, );
             
             $updateresult = LogisticDriver::UpdateDeviceid($id,$array);
        }
        
        if ($validator->passes())
        {
            
            if(LogisticDriver::UpdateDriver($id, $arr_udata))
            {
                $insert_audit = General::audit_trail('DriverController.php', 'Update()', 'Edit Driver', Session::get('username'), 'CMS');
                
                if(Input::file('profileimg')){

                     $dest_path       = Config::get('constants.DRIVER_PROFILE_FILE_PATH');
                     $upload_success  = Input::file('profileimg')->move($dest_path, $filename);

                     if ($upload_success) {
                      $driver = LogisticDriver::find($id);
                      $driver->filename = $filename;
                      $driver->save();

                    }

                 }
                
                return Redirect::to('driver/edit/'.$id)->with('success', 'Driver(ID: '.$id.') updated successfully.');
            }

        } else {
            return Redirect::back()
                        ->withInput()
                        ->withErrors($validator)->withInput();
        }
    }

     /**
     * Remove the specified driver from storage.
     *
     * @param  int  $id
     * @return Response
     * 
     */
    public function anyDelete($id)
    {
        $driver = LogisticDriver::find($id);
        $driver->status  = '2';
        $driver->save();

        $insert_audit = General::audit_trail('DriverController.php', 'Delete()', 'Delete Driver', Session::get('username'), 'CMS');
       
        return Redirect::to('driver/index');
    }

   
   
}
?>