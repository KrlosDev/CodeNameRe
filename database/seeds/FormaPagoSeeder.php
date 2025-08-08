<?php

use App\Models\FormaPago;
use App\Models\Pago;
use Illuminate\Database\Seeder;

class FormaPagoSeeder extends Seeder
{
    public function run()
    {
        if (FormaPago::count() == 0) {
            factory(FormaPago::class)->create([
                'nombre' => Pago::$formas_pago[Pago::FORMA_PAGO_TRANSFERENCIA],
            ]);
            factory(FormaPago::class)->create([
                'nombre' => Pago::$formas_pago[Pago::FORMA_PAGO_EFECTIVO],
            ]);
            factory(FormaPago::class)->create([
                'nombre' => Pago::$formas_pago[Pago::FORMA_PAGO_DEPOSITO],
            ]);
            factory(FormaPago::class)->create([
                'nombre' => Pago::$formas_pago[Pago::FORMA_PAGO_TARJETA_CREDITO],
            ]);
            factory(FormaPago::class)->create([
                'nombre' => Pago::$formas_pago[Pago::FORMA_PAGO_OTRO],
            ]);
        }
    }
}
