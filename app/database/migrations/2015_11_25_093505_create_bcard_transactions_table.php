<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;

class CreateBcardTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bcard_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('action', 40);
            $table->string('api', 40);
            $table->text('request');
            $table->text('response');
            $table->timestamp('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bcard_transactions');
    }
}
