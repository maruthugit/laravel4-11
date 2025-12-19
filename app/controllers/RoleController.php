<?php
 
class RoleController extends BaseController {
 
    public function __construct()
    {
        $this->beforeFilter('auth');
    }
 
     /**
     * Display a listing of the users on datatable.
     *
     * @return Response
     */
    public function anyRoles() {     
        $roles = Role::select('id', 'role_name', 'active_status', 'created_at');

        return Datatables::of($roles)       
                    ->edit_column('active_status', '
                            @if($active_status == 1)
                                <p class="text-success">Active</p>
                            @else
                                <p class="text-danger">Inactive</p>
                            @endif
                        ')                
                    ->add_column('Action', '
                            <a class="btn btn-primary" title="" data-toggle="tooltip" href="/sysadmin/role/edit/{{$id}}"><i class="fa fa-pencil"></i></a>
                            @if(Permission::CheckAccessLevel(Session::get(\'role_id\'), 10, 9, \'AND\'))
                            <a id="deleteRole" class="btn btn-danger" title="" data-toggle="tooltip" data-value="{{$role_name}}" href="/sysadmin/role/delete/{{$id}}"><i class="fa fa-times"></i></a>
                            @endif
                        ')
                    ->make();
    }
 
    public function anyIndex() 
    {
        $roles          = Role::getRoles();
        
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.role.index', [
            'roles'         => $roles, 
            ]
        );

    }

    public function anyCreate() 
    {
        $modules        = Permission::getModules(); 
        //$permissions    = Permission::getPermissions();
        $permission_bit = Permission::getPermissionBit();

        return View::make(Config::get('constants.SYSTEM_ADMIN').'.role.create', [
                'modules'       => $modules, 
                'permission_bit'=> $permission_bit,
            ]
        );  //, ['roles' => $roles]);

    }

    public function anyStore() 
    {
        $role                   = new Role;
        // $roles              = $role->getRoles();
        //$new_role               = array();
        $arr_permission         = Input::get('permission');
        $role->role_name        = Input::get('role_name');
        $role->created_by       = Session::get('username');
        $role->created_at       = date('Y-m-d H:i:s');
        $role->timestamps       = false;
        
        if($role->save()) {

            foreach ($arr_permission as $key => $value) {
                $bit = 0;

                foreach($value as $v) {
                    $bit = $bit + $v;
                }

                $arr_bit['role_id']     = $role->id;
                $arr_bit['mod_id']      = $key;
                $arr_bit['bit_level']   = $bit;
               
                $permissions[]          = $arr_bit;

                $permission = new Permission;
                $permission->role_id    = $role->id;
                $permission->mod_id     = $key;
                $permission->bit_level  = $bit;
                $permission->created_by = Session::get('username');
                $permission->save();

            }

            return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/role')->with('message', 'Role added successfully!'); 
        
        }
        

        // if(Role::addRole($new_role)) {
        //     return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/role')->with('message', 'Role added successfully!'); 
        // }
    }

    public function anyEdit($id)
    {
        $role           = Role::getRoleByID($id);
        $modules        = Permission::getModules(); 
        $permissions    = Permission::getPermissions($id);
        $permission_bit = Permission::getPermissionBit();
        
        return View::make(Config::get('constants.SYSTEM_ADMIN').'.role.edit')
                    ->with(array(
                            'role' => $role,
                            'modules'       => $modules, 
                            'permission_bit'=> $permission_bit,
                            'permissions'   => $permissions,
                        )
        );
    }

    public function anyUpdate($id)
    {
        $role                = Role::find($id);
        $role->role_name     = Input::get('role_name');
        $role->updated_by    = Session::get('username');
        $role->updated_at    = date('Y-m-d H:i:s');
        $arr_permission      = Input::get('permission');

        if ($role->save()){
            $delete_old_permission = Permission::deletePermissions($id);

            foreach ($arr_permission as $key => $value) {
                $bit = 0;

                foreach($value as $v) {
                    $bit = $bit + $v;
                }

                $arr_bit['role_id']     = $id;
                $arr_bit['mod_id']      = $key;
                $arr_bit['bit_level']   = $bit;
               
                $permissions[]          = $arr_bit;

                $permission = new Permission;
                $permission->role_id    = $id;
                $permission->mod_id     = $key;
                $permission->bit_level  = $bit;
                $permission->created_by = Session::get('username');
                $permission->save();

                // echo "<br>";
                // var_dump($permissions);
            }

            Session::flash('success', 'Setting has been successfully save!');
            return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/role/edit/'.$id);
               

        // if(Role::updateRole($id, $udata)){
        //     return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/role');
        } 
    }

    /**
     * Remove/Inactivate the specified role.
     *
     * @param  int  $id
     * @return Response
     */
    public function anyDelete($id)
    {
        $role = Role::find($id);
        // User::destroy($id);
        $role->active_status = '0';
        $role->timestamps = false;
        $role->save();

        return Redirect::to(Config::get('constants.SYSTEM_ADMIN').'/role');
    }


}

?>