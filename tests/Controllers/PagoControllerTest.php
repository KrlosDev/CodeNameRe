<?php

namespace Tests\Controllers;

use App\Models\Casa;
use App\Models\EjecutivoVentas;
use App\Models\Pago;
use App\Models\Proyecto;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\KitTestCase;

class PagoControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_broker_successfully_lists_pagos()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $pagos = factory(Pago::class, 3)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $response = $this->actingAs($user)
            ->get('pagos')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['pagos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $pagos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_broker_successfully_list_only_pagos_assigned()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($broker) = $this->createBroker();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        factory(Pago::class, 3)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $response = $this->actingAs($user)
            ->get('pagos')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['pagos']->items();

        $this->assertEquals(count($data), 0);
    }

    public function test_it_broker_successfully_shows_pago()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var Pago $pago
         */
        $pago = factory(Pago::class)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $this->actingAs($user)
            ->get("pagos/{$pago->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $pago->id,
                'id_casa' => $casa->id,
                'monto' => $pago->monto,
                'id_cliente' => $cliente->id,
            ]);
    }

    public function test_it_broker_does_not_shows_pago_not_assigned()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($broker, $constructora) = $this->createBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        /**
         * @var Pago $pago
         */
        $pago = factory(Pago::class)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $this->actingAs($user)
            ->get("pagos/{$pago->id}")
            ->seeStatusCode(403);
    }

    public function test_it_broker_successfully_lists_pagos_from_cliente()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();
        $cliente2 = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        factory(Pago::class, 3)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente2->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $pagos = factory(Pago::class, 3)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $response = $this->actingAs($user)
            ->get("pagos/cliente/{$cliente->id}")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['pagos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $pagos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_broker_successfully_stores_pago()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'casapago' => $casa->id,
            'cl_id' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ];

        $this->actingAs($user)
            ->post("pagos", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('pagos', [
                'id_casa' => $casa->id,
                'id_cliente' => $cliente->id,
                'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
                'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
                'descripcion' => 'My description',
                'monto' => $casa->monto_abono_inicial,
                'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
            ]);
    }

    public function test_it_broker_fails_storing_pago_due_to_validations()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'casapago' => $casa->id,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ];

        $this->actingAs($user)
            ->post("pagos", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('pagos', [
                'id_casa' => $casa->id,
                'id_cliente' => $cliente->id,
                'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
                'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
                'descripcion' => 'My description',
                'monto' => $casa->monto_abono_inicial,
                'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
            ]);
    }

    public function test_it_broker_successfully_updates_pago()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $data = [
            'monto' => $casa->monto_abono_inicial/2,
            'descripcion' => 'My description 2',
        ];

        $this->actingAs($user)
            ->put("pagos/{$pago->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('pagos', [
                'id' => $pago->id,
                'monto' => $data['monto'],
                'descripcion' => $data['descripcion'],
            ]);
    }

    public function test_it_broker_fails_updating_pago_due_to_validations()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $data = [
            'monto' => $casa->monto_abono_inicial * 2,
            'id_forma_pago' => 'eufnq9f39',
        ];

        $this->actingAs($user)
            ->put("pagos/{$pago->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('pagos', [
                'id' => $pago->id,
                'monto' => $data['monto'],
                'id_forma_pago' => $data['id_forma_pago'],
            ]);
    }

    public function test_it_broker_successfully_deletes_pago()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $this->actingAs($user)
            ->delete("pagos/{$pago->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase('pagos', [
                'id' => $pago->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_broker_fails_deleting_pago_due_to_validations()
    {
        $user = $this->getUserBroker();

        $cliente = $this->createCliente();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $this->actingAs($user)
            ->delete("pagos/13413521231")
            ->seeStatusCode(302)
            ->seeInDatabase('pagos', [
                'id' => $pago->id,
                'deleted_at' => null,
            ]);
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_ej_ventas_successfully_lists_pagos()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        $cliente2 = $this->createCliente();

        list($broker) = $this->createBroker();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Proyecto $proyecto
         */
        $proyecto2 = factory(Proyecto::class)->create([
            'id_constructora' => $broker->constructora->first()->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa2 = factory(Casa::class)->create([
            'id_proyecto' => $proyecto2->id,
            'id_broker' => $broker->id,
        ]);

        $pagos = factory(Pago::class, 3)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        factory(Pago::class, 3)->create([
            'id_casa' => $casa2->id,
            'id_cliente' => $cliente2->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $response = $this->actingAs($user)
            ->get('pagos')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['pagos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $pagos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_ej_ventas_successfully_shows_pago()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        /**
         * @var Pago $pago
         */
        $pago = factory(Pago::class)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $this->actingAs($user)
            ->get("pagos/{$pago->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $pago->id,
                'id_casa' => $casa->id,
                'monto' => $pago->monto,
                'id_cliente' => $cliente->id,
            ]);
    }

    public function test_it_ej_ventas_fails_showing_pago_not_assigned()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();

        $cliente = $this->createCliente();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $ejVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $ejVentas->id,
            'id_broker' => $ejVentas->broker->id,
        ]);

        /**
         * @var Pago $pago
         */
        $pago = factory(Pago::class)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $this->actingAs($user)
            ->get("pagos/{$pago->id}")
            ->seeStatusCode(403);
    }

    public function test_it_ej_ventas_successfully_lists_pagos_from_cliente()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();
        $cliente2 = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        factory(Pago::class, 3)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente2->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $pagos = factory(Pago::class, 3)->create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
        ]);

        $response = $this->actingAs($user)
            ->get("pagos/cliente/{$cliente->id}")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['pagos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $pagos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_ej_ventas_successfully_stores_pago()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $data = [
            'casapago' => $casa->id,
            'cl_id' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ];

        $this->actingAs($user)
            ->post("pagos", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('pagos', [
                'id_casa' => $casa->id,
                'id_cliente' => $cliente->id,
                'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
                'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
                'descripcion' => 'My description',
                'monto' => $casa->monto_abono_inicial,
                'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
            ]);
    }

    public function test_it_ej_ventas_fails_storing_pago_due_to_validations()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $data = [
            'casapago' => $casa->id,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ];

        $this->actingAs($user)
            ->post("pagos", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('pagos', [
                'id_casa' => $casa->id,
                'id_cliente' => $cliente->id,
                'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
                'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
                'descripcion' => 'My description',
                'monto' => $casa->monto_abono_inicial,
                'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
            ]);
    }

    public function test_it_ej_ventas_successfully_updates_pago()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $data = [
            'monto' => $casa->monto_abono_inicial/2,
            'descripcion' => 'My description 2',
        ];

        $this->actingAs($user)
            ->put("pagos/{$pago->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('pagos', [
                'id' => $pago->id,
                'monto' => $data['monto'],
                'descripcion' => $data['descripcion'],
            ]);
    }

    public function test_it_ej_ventas_fails_updating_pago_due_to_validations()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $data = [
            'monto' => $casa->monto_abono_inicial * 2,
            'id_forma_pago' => 'eufnq9f39',
        ];

        $this->actingAs($user)
            ->put("pagos/{$pago->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('pagos', [
                'id' => $pago->id,
                'monto' => $data['monto'],
                'id_forma_pago' => $data['id_forma_pago'],
            ]);
    }

    public function test_it_ej_ventas_successfully_deletes_pago()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $this->actingAs($user)
            ->delete("pagos/{$pago->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase('pagos', [
                'id' => $pago->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_ej_ventas_fails_deleting_pago_due_to_validations()
    {
        $user = $this->getUserEjVentas();

        $cliente = $this->createCliente();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $pago = Pago::create([
            'id_casa' => $casa->id,
            'id_cliente' => $cliente->id,
            'id_forma_pago' => Pago::FORMA_PAGO_EFECTIVO,
            'id_tipo_transaccion' => Pago::TIPO_PAGO_MONTO_INICIAL,
            'descripcion' => 'My description',
            'monto' => $casa->monto_abono_inicial,
            'realizado_at' => (new Carbon('now'))->subDays(13)->format('Y-m-d'),
        ]);

        $this->actingAs($user)
            ->delete("pagos/13413521231")
            ->seeStatusCode(302)
            ->seeInDatabase('pagos', [
                'id' => $pago->id,
                'deleted_at' => null,
            ]);
    }
}
