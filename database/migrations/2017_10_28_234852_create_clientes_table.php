<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_pais');
            $table->unsignedInteger('id_distrito');
            $table->unsignedBigInteger('id_user')->nullable();
            $table->unsignedInteger('id_banco')->nullable();
            $table->unsignedInteger('id_broker');
            $table->string('nombre', 60);
            $table->string('apellido', 60);
            $table->string('identificacion', 20);
            $table->date('fecha_nacimiento');
            $table->tinyInteger('estado_civil');
            $table->string('direccion', 100);
            $table->tinyInteger('casa_apartamento');
            $table->string('email', 100);
            $table->tinyInteger('tipo_trabajo');
            $table->string('empresa', 100);
            $table->string('cargo_empresa', 35);
            $table->double('salario');
            $table->float('anios_laborando');
            $table->string('direccion_empresa', 100);
            $table->string('telefonos_empresa', 100);
            $table->string('email_empresa', 100);
            $table->string('notas', 500);
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_pais')->references('id')
                ->on('paises')->onDelete('restrict');
            $table->foreign('id_distrito')->references('id')
                ->on('distritos')->onDelete('restrict');
            $table->foreign('id_user')->references('id')
                ->on('users')->onDelete('restrict');
            $table->foreign('id_banco')->references('id')
                ->on('bancos')->onDelete('restrict');
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
        Schema::dropIfExists('clientes');
    }
}
