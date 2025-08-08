<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('email', 100);
            $table->string('password', 100);
            $table->string('name', 60);
            $table->string('nombre', 60);
            $table->unsignedInteger('id_rol');
            $table->integer('estado');
            $table->timestamps();
            $table->timestamp('deleted_at')->nullable();
            $table->string('remember_token', 100)->nullable();

            $table->foreign('id_rol')->references('id')
                ->on('roles')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
