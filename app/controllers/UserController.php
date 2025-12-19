<?php
 
class UserController extends BaseController {
 
    public function __construct()
    {
        $this->beforeFilter('auth');
    }
 
     /**
     * Display a listing of the users on datatable.
     *
     * @return Response
     */
    public function anyUsers() {     
        $users = User::select(array(
                                'jocom_sys_admin.id', 
                                'jocom_sys_admin.username', 
                                'jocom_sys_admin.full_name', 
                                'jocom_sys_admin.email', 
                                'roles.role_name',
                                'jocom_sys_admin.modify_date',
								'jocom_sys_admin.active_status',
                            ))
                            ->leftjoin('roles', 'jocom_sys_admin.role_id', '=', 'roles.id');
                          
        return Datatables::of($users)
        				->edit_column('active_status', '
                                    @if($active_status == 1)
                                        <p class="text-success">Active</p>
                                    @else
                                        <p class="text-danger">Inactive</p>
                                    @endif
                            ')
                        ->add_column('Action', '
                                <a class="btn btn-primary" title="" data-toggle="tooltip" href="/sysadmin/user/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                                @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 10, 9, \'AND\'))
                                <a id="deleteUser" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$username}}" href="/sysadmin/user/delete/{{$id}}"><i class="fa fa-times"></i></a>
                                @endif
                            ')
                        ->make();
    }

     /**
     * Display a listing of the user.
     *
     * @return Response
     */
    public function anyIndex()
    {
        $users  = User::getAllUserRoles();
    
        $rolesUser = User::with(array('role'=>function($query){
                            $query->select('id', 'role_name');
                        }))->get();
        
        if (Permission::CheckAccessLevel(Session::get('role_id'), 10, 1, 'OR'))
            return View::make(Config::get('constants.SYSTEM_ADMIN').'.user.index', ['users' => $users]);
        else
            return View::make('home.denied', array('module' => 'System Administration > User'));
        // return View::make('user.index', ['users' => $users]);
    }
 

 
    /**
     * Show the form for creating a new user.
     *
     * @return Response
     */
    public function anyCreate()
    {   
        $role_list = DB::table('roles')->lists('role_name', 'id');
        $regions      = Region::where("activation",1)->get();
        
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.user.create', ['roles' => $role_list,'regions' => $regions]);
        // return View::make('user.create', ['roles' => $role_list]);
    }
 
    /**
     * Store a newly created user in storage.
     *
     * @return Response
     */
    public function anyStore()
    {
        $user = new User;
 
        $validator = Validator::make(Input::all(), User::$rules);

        if ($validator->passes()) {
            
            if(Input::file('user_photo')){

                $dest_path       = Config::get('constants.CMS_USER_FILE_PATH');
                $file = Input::file('user_photo');
                $file_name = $id.time().".".$file->getClientOriginalExtension(); // prepend the time (integer) to the original file name
                $file->move($dest_path, $file_name); // move it to the 'uploads' directory (public/uploads)
                // create instance of Intervention Image
                $img = Image::make(Config::get('constants.CMS_USER_FILE_PATH')."/".$file_name)
                    ->resize(100, 100)
                    ->save(Config::get('constants.CMS_USER_FILE_PATH')."/".$file_name);

            }
            $user->username     = Input::get('username');
            $user->full_name    = Input::get('full_name');
            $user->email        = Input::get('email');
            $user->password     = Hash::make(Input::get('password'));
            $user->role_id      = Input::get('role');
            $user->timestamps   = false;
            $user->created_by   = Session::get('username');
            $user->created_date = date("Y-m-d H:i:s");
            $user->user_photo   = $file_name;
            $region_access       = Input::get('region_access');
            // $user->modify_date = date('Y-m-d H:i:s', strtotime($date));
            
            if($user->save()) {
                
                $SysAdminRegionInfo = new SysAdminRegion;
                $SysAdminRegionInfo->sys_admin_id = $user->id;
                $SysAdminRegionInfo->region_id = $region_access;
                $SysAdminRegionInfo->status = 1;
                $SysAdminRegionInfo->save();
                    
                return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/user/index')->with('message', 'User added successfully!');       
            } else {
                echo "<br> Failed to add user!";
            }
                
        } else {

            return Redirect::back()
                                ->withInput()
                                ->withErrors($validator)->withInput();
             // return Redirect::to('users/create')->with('message', 'The following errors occurred')->withErrors($validator)->withInput();
        }
        
    }
 
    /**
     * Show the form for editing the specified user.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyEdit($id)
    {
        $user           = User::find($id);
        $role_list      = DB::table('roles')->lists('role_name', 'id');
        $regions      = Region::where("activation",1)->get();

        $user_regions      = SysAdminRegion::where("sys_admin_id",$id)
                ->where("status",1)->first();

        return View::make(Config::get('constants.SYSTEM_ADMIN').'.user.edit')->with(array(
                'user'          => $user, 
                'roles'         => $role_list,
                'regions'       => $regions,
                'user_region'   => $user_regions->region_id
            )
        );
    }
 
    /**
     * Update the specified user in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyUpdate($id)
    {
        try{
            
        
        $arr_validate   = array();
        $arr_input      = array();
        
        $arr_input_user['username']             = Input::get('username');
        $arr_input_user['password']             = Input::get('password');
        $arr_input_user['password_confirmation']= Input::get('password_confirmation');
        $arr_input_user['full_name']            = Input::get('full_name');
        $arr_input_user['email']                = Input::get('email');
        $arr_input_user['role_id']              = Input::get('role');
        $region_access       = Input::get('region_access');
        
        if(Input::file('user_photo')){

            $dest_path       = Config::get('constants.CMS_USER_FILE_PATH');
            $file = Input::file('user_photo');
            $file_name = $id.time().".".$file->getClientOriginalExtension(); // prepend the time (integer) to the original file name
            $file->move($dest_path, $file_name); // move it to the 'uploads' directory (public/uploads)
            // create instance of Intervention Image
            $img = Image::make(Config::get('constants.CMS_USER_FILE_PATH')."/".$file_name)
                ->resize(100, 100)
                ->save(Config::get('constants.CMS_USER_FILE_PATH')."/".$file_name);

        }
        $arr_input_user['user_photo']             = $file_name ;

        $arr_validate   = User::getUpdateRules($arr_input_user);
        $arr_input      = User::getUpdateInputs($arr_input_user);
        $arr_udata      = User::getUpdateDbDetails($arr_input);
        $validator      = Validator::make($arr_input, $arr_validate);

        if ($validator->passes()) {

            if(User::updateUser($id, $arr_udata)){
                
                
                $SysAdminRegion = SysAdminRegion::where("sys_admin_id",$id)
                        ->where("status",1)->first();
                
                if(count($SysAdminRegion) > 0 ){
                    
                    if($SysAdminRegion->region_id != $region_access ){
                        $SysAdminRegion->status = 0;
                        $SysAdminRegion->save();

                        $SysAdminRegionInfo = new SysAdminRegion;
                        $SysAdminRegionInfo->sys_admin_id = $id;
                        $SysAdminRegionInfo->region_id = $region_access;
                        $SysAdminRegionInfo->status = 1;
                        $SysAdminRegionInfo->save();
                    }
                    
                }else{
                    $SysAdminRegionInfo = new SysAdminRegion;
                    $SysAdminRegionInfo->sys_admin_id = $id;
                    $SysAdminRegionInfo->region_id = $region_access;
                    $SysAdminRegionInfo->status = 1;
                    $SysAdminRegionInfo->save();
                }
                
                
                return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/user/index');
            } 
        
        }else {
            
            return Redirect::back()
                        ->withInput()
                        ->withErrors($validator)->withInput();
        }
        
        } catch (Exception $ex) {
echo $ex->getMessage();
    }
    }
 
    /**
     * Remove the specified user from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $user = User::find($id);
        // User::destroy($id);
        $user->active_status = '0';
        $user->timestamps = false;
        $user->save();

        return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/user');
    }

    /**
     * Display a listing of the users resource.
     *
     * @return Response
     */
    public function anyUserajax() {     
        $users = DB::table('jocom_user')->select(array(
                                        'jocom_user.id', 
                                        'jocom_user.username', 
                                        'jocom_user.full_name'
                                    ));
        return Datatables::of($users)
                                    ->add_column('Action', '<a id="selectItem" class="btn btn-primary" href="{{$id}}">Select</a>')
                                    ->make();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $asd
     * @return Response
     */
    public function anyAjaxuser() {
        return View::make('user.ajaxuser');
    }

    
    public static function getSysRegionList($username){
        
        $region_list = array();
        $User = User::where("username",$username)->first();
        $SysAdminRegion = SysAdminRegion::getSysAdminRegion($User->id);
        
        foreach ($SysAdminRegion as $key => $value) {
            $region_list[] = $value->region_id;
}

        return $region_list;
        
    }

}

?>