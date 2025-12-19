<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePointConversionRatesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('point_conversion_rates', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('type_from')->unsigned();
			$table->foreign('type_from')->references('id')->on('point_types');
			$table->integer('type_to')->unsigned();
			$table->foreign('type_to')->references('id')->on('point_types');
			$table->decimal('rate', 16, 4);
			$table->decimal('charges', 16, 4);
			$table->integer('status')->unsigned();
			$table->integer('minimum')->unsigned();
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
		Schema::drop('point_conversion_rates');
	}

}
