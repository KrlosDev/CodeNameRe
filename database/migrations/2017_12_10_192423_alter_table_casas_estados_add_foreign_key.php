<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableCasasEstadosAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE `casas` ADD CONSTRAINT `fk_casas_casas_estados` FOREIGN KEY (`id_casa_estado`) REFERENCES `casas_estados`(`id`);');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE `casas` DROP FOREIGN KEY `fk_casas_casas_estados`;');
    }
}
