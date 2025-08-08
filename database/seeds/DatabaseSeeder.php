<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(RolSeeder::class);
        $this->call(FormaPagoSeeder::class);
        //$this->call(DistritoSeeder::class);
    }
}
