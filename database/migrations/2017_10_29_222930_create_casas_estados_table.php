<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCasasEstadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casas_estados', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_broker');
            $table->unsignedInteger('old_association');
            $table->string('nombre', 40);
            $table->string('slug', 25);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->unique(['id_broker','slug']);

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
        Schema::dropIfExists('casas_estados');
    }
}
