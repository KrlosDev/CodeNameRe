<?php

use Illuminate\Database\Migrations\Migration;

class AlterDescipcionInPagosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE pagos CHANGE COLUMN descipcion descripcion VARCHAR(600) NOT NULL;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE pagos CHANGE COLUMN descripcion descipcion VARCHAR(600) NOT NULL;');
    }
}
