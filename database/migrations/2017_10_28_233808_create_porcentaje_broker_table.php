<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePorcentajeBrokerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('porcentaje_broker', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_broker');
            $table->unsignedInteger('id_proyecto');
            $table->double('porcentaje');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_broker')->references('id')
                ->on('brokers')->onDelete('restrict');
            $table->foreign('id_proyecto')->references('id')
                ->on('proyectos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('porcentaje_broker');
    }
}
