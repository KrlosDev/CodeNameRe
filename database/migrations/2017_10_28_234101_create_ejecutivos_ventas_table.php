<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEjecutivosVentasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ejecutivos_ventas', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_broker');
            $table->unsignedBigInteger('id_user');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_broker')->references('id')
                ->on('brokers')->onDelete('restrict');
            $table->foreign('id_user')->references('id')
                ->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ejecutivos_ventas');
    }
}
