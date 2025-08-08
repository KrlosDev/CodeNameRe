<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDocumentosClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('documentos_clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('id_cliente');
            $table->unsignedInteger('id_pais')->nullable();
            $table->unsignedInteger('tipo_documento');
            $table->date('fecha_expiracion');
            $table->string('src', 100);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_cliente')->references('id')
                ->on('clientes')->onDelete('restrict');
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
        Schema::dropIfExists('documentos_clientes');
    }
}
