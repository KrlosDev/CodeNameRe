<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProyectosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_constructora');
            $table->string('codigo', 10);
            $table->string('nombre', 100);
            $table->string('estado', 100);
            $table->string('descripcion', 100);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

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
        Schema::dropIfExists('proyectos');
    }
}
