<?php

use App\Models\Distrito;
use App\Models\Provincia;
use Illuminate\Database\Seeder;

class DistritoSeeder extends Seeder
{
    public function run()
    {
        $faker = \Faker\Factory::create();
        if (Distrito::count() == 0) {
            for ($i=0;$i < 5; $i++) {
                $provincia = factory(Provincia::class)->create([
                    'nombre' => $faker->words(2),
                ]);

                factory(Distrito::class, 5)->create([
                    'id_provincia' => $provincia->id,
                    'nombre' => $faker->words(2),
                ]);
            }
        }
    }
}
