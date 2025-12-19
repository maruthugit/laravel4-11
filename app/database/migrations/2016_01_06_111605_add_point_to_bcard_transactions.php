<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPointToBcardTransactions extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bcard_transactions', function ($table) {
			$table->integer('point')->unsigned()->default(0)->after('api');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bcard_transactions', function ($table) {
			$table->dropColumn('point');
		});
	}

}
