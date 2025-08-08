<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReferenciasPersonalesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('referencias_personales', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_cliente');
            $table->text('nombre', 20)->nullable();
            $table->text('parentesco', 20)->nullable();
            $table->text('telefono', 15)->nullable();
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
        Schema::dropIfExists('referencias_personales');
    }
}
