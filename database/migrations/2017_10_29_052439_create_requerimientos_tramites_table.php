<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequerimientosTramitesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requerimientos_tramites', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_proyecto');
            $table->tinyInteger('cumplido');
            $table->string('nombre', 40);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_proyecto')->references('id')
                ->on('proyectos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requerimientos_tramites');
    }
}
