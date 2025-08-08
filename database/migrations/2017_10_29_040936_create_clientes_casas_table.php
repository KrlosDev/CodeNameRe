<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesCasasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes_casas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_casa');
            $table->unsignedInteger('id_ej_bancos')->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_cliente')->references('id')
                ->on('clientes')->onDelete('restrict');
            $table->foreign('id_casa')->references('id')
                ->on('casas')->onDelete('restrict');
            $table->foreign('id_ej_bancos')->references('id')
                ->on('ejecutivos_bancos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes_casas');
    }
}
