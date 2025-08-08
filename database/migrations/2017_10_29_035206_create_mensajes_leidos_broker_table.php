<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMensajesLeidosBrokerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mensajes_leidos_broker', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_mensaje');
            $table->unsignedInteger('id_broker');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_mensaje')->references('id')
                ->on('mensajes')->onDelete('restrict');
            $table->foreign('id_broker')->references('id')
                ->on('brokers')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mensajes_leidos_broker');
    }
}
