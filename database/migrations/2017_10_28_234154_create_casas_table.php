<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCasasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('casas', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_proyecto');
            $table->unsignedInteger('id_broker')->nullable();
            $table->unsignedInteger('id_ej_ventas')->nullable();
            $table->string('codigo', 10);
            $table->string('modelo', 15);
            $table->tinyInteger('estatus_tramite_cliente');
            $table->integer('lote_apto');
            $table->float('mts2_total');
            $table->float('mts2_construccion');
            $table->float('mts2_adicionales');
            $table->tinyInteger('recamaras');
            $table->tinyInteger('banos');
            $table->double('valor');
            $table->double('monto_separacion');
            $table->double('monto_abono_inicial');
            $table->double('monto_mts2_adicional');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_proyecto')->references('id')
                ->on('proyectos')->onDelete('restrict');
            $table->foreign('id_broker')->references('id')
                ->on('brokers')->onDelete('restrict');
            $table->foreign('id_ej_ventas')->references('id')
                ->on('ejecutivos_ventas')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('casas');
    }
}
