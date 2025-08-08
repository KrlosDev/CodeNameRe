<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMensajesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mensajes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedInteger('id_broker');
            $table->unsignedInteger('id_ej_ventas');
            $table->string('titulo', 100);
            $table->string('descripcion', 600);
            $table->tinyInteger('leido_broker');
            $table->tinyInteger('leido_ventas');
            $table->date('ventas_deleted_at')->nullable();
            $table->date('broker_deleted_at')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_cliente')->references('id')
                ->on('clientes')->onDelete('restrict');
            $table->foreign('id_ej_ventas')->references('id')
                ->on('ejecutivos_ventas')->onDelete('restrict');
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
        Schema::dropIfExists('mensajes');
    }
}
