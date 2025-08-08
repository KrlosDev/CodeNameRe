<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCodeudoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('codeudores', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedInteger('id_pais');
            $table->unsignedInteger('id_distrito');
            $table->string('nombre', 60);
            $table->string('apellido', 60);
            $table->string('identificacion', 20);
            $table->date('fecha_nacimiento');
            $table->tinyInteger('estado_civil');
            $table->string('telefono', 15)->nullable();
            $table->string('direccion', 100);
            $table->tinyInteger('casa_apartamento');
            $table->string('email', 100);
            $table->tinyInteger('tipo_trabajo');
            $table->string('empresa', 100)->nullable();
            $table->string('cargo_empresa', 35)->nullable();
            $table->double('salario');
            $table->float('anios_laborando')->nullable();
            $table->string('direccion_empresa', 100)->nullable();
            $table->string('telefonos_empresa', 100)->nullable();
            $table->string('email_empresa', 100)->nullable();
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();

            $table->foreign('id_pais')->references('id')
                ->on('paises')->onDelete('restrict');
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
        Schema::dropIfExists('codeudores');
    }
}
