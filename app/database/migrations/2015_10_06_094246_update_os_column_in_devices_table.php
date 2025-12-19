<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateOsColumnInDevicesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('notification_devices', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE notification_devices CHANGE os os ENUM("Android", "iOS", "iPad") DEFAULT "Android"');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('notification_devices', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE notification_devices CHANGE os os ENUM("Android", "iOS") DEFAULT "Android"');
		});
	}

}
