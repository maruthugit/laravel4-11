<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class SubModulePermission extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sub_module_permissions';

	
	public function scopeGetSubModPermission() {
		// return DB::table('sub_module_permissions as smp')
		// 	->join('jocom_sys_admin as jsa', 'smp.user_id', '=', 'jsa.id')
		// 	->join('modules as m', 'smp.mod_id', '=', 'm.id')
		// 	->join('sub_modules as sm', 'smp.sub_module_id', '=', 'sm.id');

		return DB::table('sub_module_permissions as smp')
			->join('jocom_sys_admin as jsa', 'smp.user_id', '=', 'jsa.id')
			->join('modules as m', 'smp.mod_id', '=', 'm.id')
// 			->groupBy('smp.mod_id');
			->groupBy('jsa.username','smp.mod_id');
	}







}

?>