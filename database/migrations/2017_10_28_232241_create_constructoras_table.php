<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConstructorasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('constructoras', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('id_licencia');
            $table->unsignedBigInteger('id_user');
            $table->smallInteger('max_brokers');
            $table->smallInteger('max_ejecutivos_ventas');
            $table->smallInteger('max_ejecutivos_bancos');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_licencia')->references('id')
                ->on('licencias')->onDelete('restrict');
            $table->foreign('id_user')->references('id')
                ->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('constructoras');
    }
}
