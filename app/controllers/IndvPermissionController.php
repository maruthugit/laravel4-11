<?php
 
class IndvPermissionController extends BaseController {
    public function __construct()
    {
        $this->beforeFilter('auth');
    }

    public function anyPermission() {  // permission list
        // $indvPermission     = DB::table('sub_module_permissions as smp')
        //     ->join('jocom_sys_admin as jsa', 'smp.user_id', '=', 'jsa.id')
        //     ->join('modules as m', 'smp.mod_id', '=', 'm.id')
        //     ->join('sub_modules as sm', 'smp.sub_module_id', '=', 'sm.id')
        //     ->selectRaw('smp.id, jsa.username, m.module, sm.sub_module')
        //     ->where('smp.status', 1)
        //     ->orderBy('smp.id', 'asc');

        $indvPermission      = SubModulePermission::getSubModPermission()
            ->selectRaw('smp.id, jsa.username, m.module')
            ->where('smp.status', 1)
            ->orderBy('smp.id', 'asc');

        return Datatables::of($indvPermission)                 
                    ->add_column('Action', '
                            <a class="btn btn-primary" title="" data-toggle="tooltip" href="/sysadmin/indvPermission/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                            @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 10, 9, \'AND\'))
                            <!-- <a id="deleteRole" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$role_name}}" href="/sysadmin/indvPermission/delete/{{$id}}"><i class="fa fa-times"></i></a> -->
                            @endif
                        ')
                    ->make();
    }
     
    public function anyIndex() // permission list (page)
    {
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.indvPermission.index');

    }

    public function anyCreate() 
    {
        $modules        = Permission::getModules();

        return View::make(Config::get('constants.SYSTEM_ADMIN').'.indvPermission.create', [
                'modules'       => $modules, 
            ]
        );  

    }

    public function anyEdit($id)
    {
        $role           = Role::getRoleByID($id);
        $modules        = Permission::getModules(); 

        // $indvPermission     = DB::table('sub_module_permissions as smp')
        //     ->join('jocom_sys_admin as jsa', 'smp.user_id', '=', 'jsa.id')
        //     ->join('modules as m', 'smp.mod_id', '=', 'm.id')
        //     ->join('sub_modules as sm', 'smp.sub_module_id', '=', 'sm.id')
        //     ->selectRaw('smp.user_id, jsa.username, m.module, smp.mod_id')
        //     ->where('smp.id', $id)
        //     ->first();

        $indvPermission     = SubModulePermission::getSubModPermission()
            ->selectRaw('smp.user_id, jsa.username, m.module, smp.mod_id')
            ->where('smp.id', $id)
            ->first();

        $subModules         = Permission::getSubModules($indvPermission->mod_id, $indvPermission->user_id);        
        
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.indvPermission.edit')
            ->with(array(
                    'indvPermission'    => $indvPermission,
                    'subModules'        => $subModules,
                )
        );
    }

    public function anyUpdate($user_id, $mod_id)
    {
        $sub_module_id  =   is_array(Input::get('sub_module')) ? array_values(Input::get('sub_module')) : array();

        $get_sub_mod_list = DB::table('sub_module_permissions') 
            ->select('sub_module_id', 'status')
            ->where('user_id', $user_id)
            ->where('mod_id', $mod_id)
            ->get();     

        $array = array_column(json_decode( json_encode($get_sub_mod_list), true), 'sub_module_id');

        foreach ($get_sub_mod_list as $key => $value){
            if (!in_array($value->sub_module_id ,$sub_module_id)){ // if data exist but unchecked, update status to 0
                
                DB::table('sub_module_permissions')
                    ->where('user_id', $user_id)
                    ->where('sub_module_id', $value->sub_module_id)
                    ->update([
                            'status' => 0,
                            'updated_at'    => date('Y-m-d H:i:s'), 
                            'updated_by'    => Session::get('username')
                        ]);

            } else { // if data exist but checked, update status to 1

                DB::table('sub_module_permissions')
                    ->where('user_id', $user_id)
                    ->where('sub_module_id', $value->sub_module_id)
                    ->update([
                            'status' => 1,
                            'updated_at'    => date('Y-m-d H:i:s'), 
                            'updated_by'    => Session::get('username')
                        ]);

            }
        }
        foreach ($sub_module_id as $key => $value) { // to insert new data
            if (!in_array($value ,$array)){ 

                DB::table('sub_module_permissions')
                    ->insert([
                        'user_id'       => $user_id, 
                        'mod_id'        => $mod_id, 
                        'sub_module_id' => $value, 
                        'status'        => 1, 
                        'created_at'    => date('Y-m-d H:i:s'), 
                        'created_by'    => Session::get('username')
                    ]);

            }
        }

        Session::flash('success', 'Setting has been successfully save!');
        return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/indvPermission');
    }

    public function anyDelete($id)
    {
        $indvPermission = SubModulePermission::find($id);

        $indvPermission->status     = '0';
        $indvPermission->updated_at = date('Y-m-d H:i:s');
        $indvPermission->updated_by =Session::get('username');
        $indvPermission->save();

        return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/indvPermission');
    }
    
    public function anyUserlist() { // open modal for user list
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.indvPermission.user-list');
    }

    public function anyAjaxfetchuser() { // get user 

        $users = DB::table('jocom_sys_admin as jsa')
            ->join('roles as r', 'jsa.role_id', '=', 'r.id')
            ->where('jsa.role_id', '<>', 1)
            ->where('jsa.active_status', 1)
            ->select('jsa.id', 'jsa.username', 'jsa.full_name', 'jsa.email', 'jsa.role_id', 'r.role_name');

        return Datatables::of($users)
            ->add_column('Action', '<a id="selectUser" class="btn btn-primary">Select</a>')
            ->make();
    }

    public function anyModule() { // get module

        $role_id        = Input::get('role_id');
        $module         = DB::table('modules as m')
            ->join('permissions as p', 'm.id', '=', 'p.mod_id')
            ->select('m.id', 'm.module')
            ->where('p.role_id', $role_id)
            ->get();
        
        $data['module'] = $module;

        $response = array("data" => $data);
        return $response;

    
    }

    public function anySubmodule() { // get submodule
        $data       = array();
        $RespStatus = 1; 
        $message    = "";
        $errorCode  = "";
        $is_error   = false;
        $error_line = "";

        try {
            
            $module_id          = Input::get('module_id');
            $user_id            = Input::get('user_id');
            $role_id            = Input::get('role_id');
            $subModule          = Permission::getSubModules($module_id, $user_id);
            $permission_bit     = Permission::getPermissionBit();
            
            $data['subModule']      = $subModule;
            $data['permission_bit'] = $permission_bit;
        
        } catch (Exception $ex) {
            $is_error   = true;
            $message    = $ex->getMessage();
        } 

        /* Return Response */
        $response = array("RespStatus" => $RespStatus, "error" => $is_error, "error_code" => $errorCode, "error_line" => $error_line, "message" => $message, "data" => $data);
        return $response;

    
    }

    public function anyStore() // add/update data
    {   

        $rules = array(
            'user'=>'required',
            'modules'=>'required',	
        );

        $user_id        =   Input::get('user');
        $module_id      =   Input::get('modules');
        $sub_module_id  =   is_array(Input::get('sub_module')) ? array_values(Input::get('sub_module')) : array();

        $validator = Validator::make(Input::all(), $rules);

        // get data that exist from table sub_module_permissions
        $get_sub_mod_list = DB::table('sub_module_permissions as smp') 
            ->leftjoin('sub_modules as sm', 'smp.sub_module_id', '=', 'sm.id')
            ->select('smp.sub_module_id', 'smp.status')
            ->where('smp.user_id', $user_id)
            ->where('sm.mod_id', $module_id)
            ->get();     
        $array = array_column(json_decode( json_encode($get_sub_mod_list), true), 'sub_module_id');

        if ($validator->passes()) {
            foreach ($get_sub_mod_list as $key => $value){ // to update date that exist in db
                if (!in_array($value->sub_module_id ,$sub_module_id)){ // if data exist but unchecked, update status to 0
                    
                    DB::table('sub_module_permissions')
                        ->where('user_id', $user_id)
                        ->where('sub_module_id', $value->sub_module_id)
                        ->update([
                                'status' => 0,
                                'updated_at'    => date('Y-m-d H:i:s'), 
                                'updated_by'    => Session::get('username')
                            ]);

                } else { // if data exist but checked, update status to 1
                    if ($value->status == 0){

                        DB::table('sub_module_permissions')
                            ->where('user_id', $user_id)
                            ->where('sub_module_id', $value->sub_module_id)
                            ->update([
                                    'status' => 1,
                                    'updated_at'    => date('Y-m-d H:i:s'), 
                                    'updated_by'    => Session::get('username')
                                ]);
    
                    }
                }

            }
            foreach ($sub_module_id as $key => $value) { // to insert new data
                if (!in_array($value ,$array)){ // if data from db not check

                    DB::table('sub_module_permissions')
                        ->insert([
                            'user_id'       => $user_id, 
                            'mod_id'        => $module_id, 
                            'sub_module_id' => $value, 
                            'status'        => 1, 
                            'created_at'    => date('Y-m-d H:i:s'), 
                            'created_by'    => Session::get('username')
                        ]);
                }                         
                
            }

            return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/indvPermission')->with('message', 'Individual permission added successfully!'); 
        
        } else {

            return Redirect::back()->withInput()->withErrors($validator)->withInput();

        }
    }
}

?>