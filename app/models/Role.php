<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Role extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'roles';

	/**
	 * Get the roles that a user has
	 */
	public function scopeGetRoles() {
		return DB::table('roles')->get();
	}

	public function scopeGetRoleByID($query, $id) {
		$role = DB::table('roles')
					->where('id', '=', $id)
					->first();
		return $role;
	}

	public function scopeUpdateRole($query, $id, array $data) {
		$role = DB::table('roles')
					->where('id', $id)
					->update($data);
		return $role;
	}	

	public function scopeAddRole($query, array $data) {
		$role = DB::table('roles')->insert($data);
		return $role;
	}
	

}

?>