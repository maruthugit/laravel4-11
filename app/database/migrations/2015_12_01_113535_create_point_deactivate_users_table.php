<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreatePointDeactivateUsersTable extends Migration
{

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('point_deactivate_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('point_type_id')->unsigned();
            $table->foreign('point_type_id')->references('id')->on('point_types');
            $table->integer('user_id');
            $table->foreign('user_id')->references('id')->on('jocom_user');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('point_deactivate_users');
    }

}
