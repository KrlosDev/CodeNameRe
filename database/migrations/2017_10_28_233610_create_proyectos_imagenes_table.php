<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateProyectosImagenesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proyectos_imagenes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_proyecto');
            $table->unsignedInteger('id_imagen');
            $table->timestamps();

            $table->foreign('id_proyecto')->references('id')
                ->on('proyectos')->onDelete('restrict');
            $table->foreign('id_imagen')->references('id')
                ->on('imagenes')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proyectos_imagenes');
    }
}
