<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTelefonosClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('telefonos_clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_cliente');
            $table->text('telefono', 20);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_cliente')->references('id')
                ->on('clientes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('telefonos_clientes');
    }
}
