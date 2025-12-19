<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointConversionsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('point_conversions', function(Blueprint $table)
		{
            $table->increments('id');
            $table->integer('point_user_id')->unsigned();
            $table->foreign('point_user_id')->references('id')->on('point_users');
            $table->integer('type_from')->unsigned();
            $table->foreign('type_from')->references('id')->on('point_types');
            $table->integer('type_to')->unsigned();
            $table->foreign('type_to')->references('id')->on('point_types');
            $table->integer('point_from')->unsigned();
            $table->integer('point_to')->unsigned();
            $table->decimal('rate', 16, 4);
			$table->decimal('charges', 16, 4);
            $table->integer('status')->unsigned();
            $table->text('remark')->nullable();
            $table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('point_conversions');
	}

}
