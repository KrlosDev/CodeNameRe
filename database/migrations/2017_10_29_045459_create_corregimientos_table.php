<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCorregimientosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('corregimientos', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_distrito');
            $table->unsignedInteger('id_circuito');
            $table->string('nombre', 40);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_distrito')->references('id')
                ->on('distritos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('corregimientos');
    }
}
