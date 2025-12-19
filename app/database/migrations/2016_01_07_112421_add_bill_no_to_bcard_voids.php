<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBillNoToBcardVoids extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('bcard_voids', function(Blueprint $table)
		{
			$table->string('bill_no', 40)->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('bcard_voids', function(Blueprint $table)
		{
			$table->dropColumn('bill_no');
		});
	}

}
