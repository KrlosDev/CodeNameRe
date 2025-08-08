<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesCodeudoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes_codeudores', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedBigInteger('id_codeudor');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_cliente')->references('id')
                ->on('clientes')->onDelete('restrict');
            $table->foreign('id_codeudor')->references('id')
                ->on('codeudores')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes_codeudores');
    }
}
