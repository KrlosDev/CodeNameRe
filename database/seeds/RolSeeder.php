<?php

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run()
    {
        if (Rol::count() == 0) {
            factory(Rol::class)->create([
                'nombre' => 'Administrador',
            ]);
            factory(Rol::class)->create([
                'nombre' => 'Constructora',
            ]);
            factory(Rol::class)->create([
                'nombre' => 'Broker',
            ]);
            factory(Rol::class)->create([
                'nombre' => 'Ejecutivo de Ventas',
            ]);
            factory(Rol::class)->create([
                'nombre' => 'Ejecutivo de Banco',
            ]);
            factory(Rol::class)->create([
                'nombre' => 'Cliente',
            ]);
        }
    }
}
