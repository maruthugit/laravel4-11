<?php
 
class PermissionController extends BaseController {
 
    public function __construct()
    {
        $this->beforeFilter('auth');
    }
 
     /**
     * Display a listing of the user.
     *
     * @return Response
     */
    public function anyIndex()
    {
        $role_list  = DB::table('roles')->lists('role_name', 'id');
        $modules    = Permission::getModules(); 

        return View::make(Config::get('constants.SYSTEM_ADMIN').'.permission.index', ['roles' => $role_list, 'modules' => $modules]);
        
    }
 

    public function anyShow()
    {
        
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

}

?>