<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_casa');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedInteger('id_forma_pago');
            $table->unsignedInteger('id_tipo_transaccion');
            $table->string('descipcion', 600);
            $table->double('monto');
            $table->date('realizado_at');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_casa')->references('id')
                ->on('casas')->onDelete('restrict');
            $table->foreign('id_cliente')->references('id')
                ->on('clientes')->onDelete('restrict');
            $table->foreign('id_forma_pago')->references('id')
                ->on('formas_pagos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pagos');
    }
}
