<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Migrations\Migration;

class AddCasaEstadoColumnToCasas extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement("
            ALTER TABLE casas ADD COLUMN id_casa_estado int unsigned NULL DEFAULT NULL AFTER estatus_tramite_cliente;
        ");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement("
            ALTER TABLE casas DROP COLUMN id_casa_estado;
        ");
    }
}
