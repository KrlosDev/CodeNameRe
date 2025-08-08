<?php

use Illuminate\Database\Migrations\Migration;

class MigrateOldStatesToCasasEstados extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('
            UPDATE
                casas AS ca
            INNER JOIN casas_estados AS ce
                 ON ca.estatus_tramite_cliente = ce.old_association AND ca.estatus_tramite_cliente != 1
            SET
                ca.id_casa_estado = ce.id;
      ');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
