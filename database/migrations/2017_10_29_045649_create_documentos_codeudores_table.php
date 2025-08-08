<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentosCodeudoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_codeudores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('id_co_deudores');
            $table->unsignedInteger('id_pais');
            $table->string('src', 100);
            $table->integer('tipo_documento');
            $table->date('fecha_expiracion');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_co_deudores')->references('id')
                ->on('codeudores')->onDelete('restrict');
            $table->foreign('id_pais')->references('id')
                ->on('paises')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documentos_codeudores');
    }
}
