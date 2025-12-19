<?php

use Illuminate\Auth\UserTrait;
use Illuminate\Auth\UserInterface;
use Illuminate\Auth\Reminders\RemindableTrait;
use Illuminate\Auth\Reminders\RemindableInterface;

class Setting extends Eloquent implements UserInterface, RemindableInterface {

	use UserTrait, RemindableTrait;

	public function scopeGetAppVersion() {
		return DB::table('app_version')->first();
	}

	public function scopeUpdateApp($query, array $data) {
		$app 	= DB::table('app_version')
					->update($data);

		return $app;
	}
	
	public function scopeGetAllLang() {
		return DB::table('language')
					->lists('name', 'code');
	}
	
	public function scopeGetAppnewVersion(){

		return DB::table('appversion')
					->where('default','=','1')
					->orderby('apptype','ASC')
					->get();
	}

	public function scopeUpdateAppnew($query, array $data,$apptype) {

		$res = DB::table('appversion')
          		  ->where('apptype','=',$apptype)
            	  ->update(array('default' => 0));

		$app 	= DB::table('appversion')
					->insert($data);

		return $app;
	}

	public function scopeGetIndividualVersion($apptype){

		return DB::table('appversion')
					->where('default','=','1')
					->where('apptype','=',$apptype)
					->first();
	}
}
