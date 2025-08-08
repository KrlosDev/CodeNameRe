<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConstructorasBrokersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('constructoras_brokers', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_broker');
            $table->unsignedInteger('id_constructora');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_broker')->references('id')
                ->on('brokers')->onDelete('restrict');
            $table->foreign('id_constructora')->references('id')
                ->on('constructoras')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('constructoras_brokers');
    }
}
