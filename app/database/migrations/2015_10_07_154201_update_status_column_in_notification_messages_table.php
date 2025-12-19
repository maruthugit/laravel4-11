<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateStatusColumnInNotificationMessagesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('notification_messages', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE notification_messages CHANGE status status ENUM("Pending", "Processing", "Completed", "Stopped") DEFAULT "Pending"');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('notification_messages', function(Blueprint $table)
		{
			DB::statement('ALTER TABLE notification_messages CHANGE status status ENUM("Pending", "Processing", "Completed") DEFAULT "Pending"');
		});
	}

}
