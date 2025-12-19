<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Permission extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'permissions';

	public function scopeGetAllFunc()
	{
		return DB::table('modules')
					->select('id', 'group', 'module', 'controller')
					->get();
	}

	public function scopeGetAllow($query, $id)
	{
		return DB::table('permissions')
					->select('user_id', 'mod_id')
					->where('user_id', '=', $id)
					->get();
	}

	public function scopeGetNotAllow() 
	{
		$not_allow 	= array();
		$all_func	= $this->getAllFunc();
		$allow 		= $this->getAllow();
		// $total_func	= count($all_func);

		foreach($all_func as $func) {
			foreach($allow as $a) {
				if($func->id != $a->mod_id) {
					$not_allow = $func->id;
				}
			}
		}

		return $not_allow;
	}

	public function scopeGetFuncID($query, $group)
	{
		return DB::table('modules')
					->select('id', 'group', 'module', 'controller')
					->where('group', '=', $group)
					->get();
	}				

	public static function CheckAllowGroup($group, $id)
	{
		$result	= DB::table('modules as m')
						->select('m.id', 'm.group', 'p.role_id')
						->leftjoin('permissions as p', 'm.id', '=', 'p.mod_id')
						->where('p.role_id', '=', $id)
						->where('m.group', '=', $group)
						->get();

		// echo "<br>[group:".$group."][id:".$id."]<br>";				
		//var_dump($result);

		return count($result) > 0 ? true : false;
		

	}

	public function scopeGetModules()
	{
		return DB::table('modules')
					->select('id','module', 'group')
					->orderby('module')
					->get();
	}

    public function scopeGetSubModules($query, $id, $user_id) // get submodules
	{
		$submodule	= 	DB::table('sub_modules as sm')
							->leftJoin('sub_module_permissions as smp', function($join)  use ($user_id) {
								$join->on('smp.sub_module_id','=','sm.id');
								$join->on('smp.user_id', '=', DB::raw($user_id));
								$join->on('smp.status', '=', DB::raw(1));
							})
							->select('sm.id','sm.sub_module', 'sm.page_link', 'smp.user_id', 'smp.status')
							->where('sm.mod_id', '=', $id)
							->orderby('sm.id')
							->get();
		// Log::info($submodule);

		return count($submodule) > 0 ? $submodule : null;
	}
	
	public function scopeUpdatePermission($query, $id, array $data)
	{
		$permission = DB::table('permissions')
						->where('id', $id)
						->update($data);
		return $permission;
	}

	public function scopeGetPermissions($query, $id)
	{

		/*
		SELECT permissions.mod_id, permission_bit.bit, permission_bit.name
   FROM permissions LEFT JOIN permission_bit ON permissions.bit_level & permission_bit.bit
 WHERE permissions.user_id = 24
		 */
		
		$result	= DB::table('permissions')
					->select('mod_id', 'bit_level')
					->where('role_id', '=', $id)
					->get();

		
		foreach($result as $r) {
			foreach($r as $key => $value) {
				
				if($key == "mod_id") $cur_mod = $value;
				if($key == "bit_level") {
					$bin 		= sprintf( "%04d", decbin($value));
					$arr[$key]	= $bin;
				}
				else $arr[$key] = $value;
			}
			
			$permission[$cur_mod] = $bin;
			
		}
		if (count($result) > 0) return $permission;
		else return 0;
		
	}

	public function scopeGetPermissionBit()
	{
		return DB::table('permission_bit')
					->select('id', 'bit','name')
					->orderby('bit', 'desc')
					->get();
	}

	public function scopeDeletePermissions($query, $id)
	{
		return DB::table('permissions')
					->where('role_id', '=', $id)
					->delete();
	}

	public static function CheckAccessLevel($id, $mod_id, $bit, $operator)
	{		
        $user_id        = Session::get('user_id');
		$page_name		= Request::path();

		$sub_module_access	= DB::table('sub_module_permissions as smp')
							->join('sub_modules as sm', 'sm.id', '=', 'smp.sub_module_id')
							->selectRaw('smp.id, sm.mod_id, sm.sub_module')
							->where('smp.user_id', $user_id)
							->where('smp.status', 1)
							->where('sm.mod_id', $mod_id)
							->get();

		if($sub_module_access) { // if user have submodules access/individual permission

			$allow = true;

		} else {
			
			$result		= -1;
			$allow 		= "";
			$bit_bin	= sprintf( "%04d", decbin($bit));
			$arr_bit_bin= str_split($bit_bin);	// SYSTEM
	
			$permission	= DB::table('modules as m')
							->select('p.bit_level')
							->leftjoin('permissions as p', 'm.id', '=', 'p.mod_id')
							->where('p.role_id', '=', $id)
							->where('m.id', '=', $mod_id)
							->first();
	
			if (count($permission) > 0) {
				$bin 		= sprintf( "%04d", decbin($permission->bit_level));		
				$arr_bin 	= str_split($bin);	// DB
	
			// echo "<br>[MOD:".$mod_id."][".$permission->bit_level."][db:".$bin."][sys:".$bit_bin."]";
			
				for ($i = count($arr_bit_bin) - 1; $i >= 0; $i--) {
	
					if ($operator == 'OR') {
						if ($arr_bin[$i] == 1 && $arr_bin[$i] == $arr_bit_bin[$i]) {
							$allow == "" ? $allow = $arr_bin[$i] : $allow = $allow | $arr_bin[$i];
						// echo "[".$operator."][allow:".$allow."][db:".$arr_bin[$i]."][sys:".$arr_bit_bin[$i]."]";
						}	
					}
				
					if ($operator == 'AND') {
						if ($arr_bit_bin[$i] == 1) {
							$allow == "" ? $allow = $arr_bin[$i] : $allow = $allow & $arr_bin[$i];
						}
					
					// echo "[".$operator."][allow:".$allow."][db:".$arr_bin[$i]."][sys:".$arr_bit_bin[$i]."]";
					}
				}
			} else {
				$allow = 0;
			}
		}

		// echo "[Final allow: ".$allow."]";
		
		// return $allow ? true : false;
		return $allow ? true : false;
	}
	
	// Nadzri - Individual permission (19/07/2022)
	// Check individual permission/user's accesss for submodule
	public static function CheckSubModAccess($role_id, $user_id, $mod_id, $sub_mod_name)
	{	           
		$indvPermission	= DB::table('sub_module_permissions')
			->select('id')
			->where('mod_id', $mod_id)
			->where('user_id', $user_id)
			->where('status', 1)
			->first();

		if (is_array($sub_mod_name)) {

			$multiple_sub_mod	= DB::table('sub_module_permissions as smp')
				->join('sub_modules as sm', 'sm.id', '=', 'smp.sub_module_id')
				->select('smp.id')
				->where('smp.user_id', $user_id)
				->where('smp.status',1)
				->whereIn('sm.page_link', $sub_mod_name)
				->first(); 

			if ($indvPermission) {	// user has sub module access			
				if ($multiple_sub_mod){
					$allow = true;
				} else {
					$allow = 0;
				}
			} else { // if no, then show all sub modules
				$allow = true;
			}

		} else {
			
			$sub_mod_access	= DB::table('sub_module_permissions as smp')
				->join('sub_modules as sm', 'sm.id', '=', 'smp.sub_module_id')
				->select('smp.id')
				->where('smp.user_id', $user_id)
				->where('smp.status',1)
				->where('sm.page_link', $sub_mod_name)
				->first(); 

			// submodule permission
			if ($indvPermission) { // user has sub module access
				if ($sub_mod_access){
					$allow = true;
				} else {
					$allow = 0;
				}
			} else { // if no, then show all sub modules
				$allow = true;
			}
		
		}

		return $allow ? true : false;
	}

}

?>