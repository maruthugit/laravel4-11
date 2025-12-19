<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBcardVoidsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('bcard_voids', function(Blueprint $table)
		{
			$table->increments('id');
            $table->string('action', 40);
            $table->string('api', 40);
            $table->integer('point')->unsigned()->default(0);
            $table->text('request');
            $table->text('response');
            $table->timestamp('created_at');
			$table->integer('reward_id')->unsigned()->nullable();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('bcard_voids');
	}

}
