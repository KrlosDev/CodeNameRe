<?php

use Illuminate\Database\Migrations\Migration;

class AlterTableRolesAddDeletedAt extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::statement('ALTER TABLE roles ADD COLUMN deleted_at TIMESTAMP NULL DEFAULT NULL AFTER updated_at;');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::statement('ALTER TABLE roles DROP COLUMN deleted_at;');
    }
}
