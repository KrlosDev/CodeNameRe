<?php

namespace Tests\Controllers;

use App\Models\Broker;
use App\Models\Casa;
use App\Models\Constructora;
use App\Models\EjecutivoVentas;
use App\Models\Proyecto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class CasaControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    /********* CONSTRUCTORA *********/

    public function test_it_a_constructora_displays_casas_index()
    {
        $user = $this->getUserConstructora();

        $this->actingAs($user)
            ->get('casas')
            ->seeStatusCode(200);
    }

    public function test_it_a_constructora_lists_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get('casas')
            ->seeStatusCode(200);
    }

    public function test_it_a_constructora_shows_a_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("casas/{$casa->id}")
            ->seeStatusCode(200);
    }

    public function test_it_a_constructora_fails_when_tries_to_show_wrong_casa()
    {
        $this->getUserConstructora();

        /**
         * @var Constructora $constructora
         */
        list($constructora, $user) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($this->user)
            ->get("casas/{$casa->id}")
            ->seeStatusCode(403);
    }

    public function test_it_a_constructora_lists_casas_from_proyecto()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("casas/proyecto/{$proyecto->id}")
            ->seeStatusCode(200);
    }

    public function test_it_a_constructora_succesfully_stores_a_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        $data = [
            'cmodelo' => '1',
            'cmts2_total' => 50,
            'cmts2_construccion' => 20,
            'cmts2_adicionales' => 0,
            'crecamaras' => 2,
            'cbanos' => 1,
            'cvalor' => 32000,
            'cmonto_separacion' => 250,
            'cmonto_abono_inicial' => 1000,
            'cmonto_mts2_adicional' => 50,
            'cid_proyecto' => $proyecto->id,
            'ccantidad' => 50,
        ];

        $response = $this->actingAs($this->user)
            ->post("casas", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('casas', [
                'recamaras' => 2,
                'banos' => 1,
                'valor' => 32000,
                'monto_separacion' => 250,
                'monto_abono_inicial' => 1000,
                'monto_mts2_adicional' => 50,
                'id_proyecto' => $proyecto->id,
            ]);

        //dd($response->response);
    }

    public function test_it_a_constructora_fails_trying_to_store_a_casa_due_to_validations()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        $data = [
            'cmodelo' => '1',
            'cmts2_total' => 50,
            'cmts2_construccion' => 20,
            'cmts2_adicionales' => 0,
            'crecamaras' => 2,
            'banos' => 1,
            'valor' => 32000,
            'cmonto_separacion' => 250,
            'cmonto_abono_inicial' => 1000,
            'cmonto_mts2_adicional' => 50,
            'ccantidad' => 50,
            'id_proyecto' => $proyecto->id,
        ];

        $this->actingAs($this->user)
            ->post("casas", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas', [
                'recamaras' => 2,
                'banos' => 1,
                'valor' => 32000,
                'monto_separacion' => 250,
                'monto_abono_inicial' => 1000,
                'monto_mts2_adicional' => 50,
                'id_proyecto' => $proyecto->id,
            ]);
    }

    public function test_it_a_constructora_succesfully_updates_a_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $data = $casa->toArray();

        $data['valor_u'] = 20602;

        $this->actingAs($this->user)
            ->put("casas/{$casa->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('casas', [
                'id' => $casa->id,
                'valor' => 20602,
            ]);
    }

    public function test_it_a_constructora_fails_trying_to_update_a_casa_due_to_permissions()
    {
        $user = $this->getUserConstructora();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $data = $casa->toArray();

        $data['valor_u'] = 20602;

        $this->actingAs($user)
            ->put("casas/{$casa->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas', [
                'id' => $casa->id,
                'valor' => 20602,
            ]);
    }

    public function test_it_a_constructora_successfully_deletes_a_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($this->user)
            ->delete("casas/{$casa->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas', [
                'id' => $casa->id,
                'deleted_at' => $casa->deleted_at,
            ]);
    }

    public function test_it_a_constructora_fails_trying_to_delete_a_casa_if_assigned_to_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        list($broker) = $this->createBroker();

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($this->user)
            ->delete("casas/{$casa->id}")
            ->seeStatusCode(302)
            ->seeInDatabase('casas', [
                'id' => $casa->id,
                'deleted_at' => $casa->deleted_at,
            ]);
    }

    public function test_it_a_constructora_cannot_assign_ej_ventas_to_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        list($ejVentas, $broker) = $this->createEjVentas();

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);

        $data = [
            'id_ej_ventas' => $ejVentas->id,
        ];

        $this->actingAs($this->user)
            ->post("casas/asignarEjecutivos")
            ->seeStatusCode(302)
            ->seeInDatabase('casas', [
                'id' => $casa->id,
                'deleted_at' => $casa->deleted_at,
            ]);
    }

    public function test_it_a_constructora_cannot_assign_estatus_to_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        list($ejVentas, $broker) = $this->createEjVentas();

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);

        $data = [
            'casas' => $casa->id,
            'status' => Casa::ESTADO_POR_ASIGNAR,
        ];

        $this->actingAs($this->user)
            ->post("casas/asignarEstatus", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('casas', [
                'id' => $casa->id,
                'id_casa_estado' => $casa->id_casa_estado,
            ]);
    }

    public function test_it_a_constructora_successfully_deletes_several_casas()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        list($ejVentas, $broker) = $this->createEjVentas();

        /**
         * @var Collection getPropiedades$casas1
         */
        $casas1 = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);

        /**
         * @var Collection $casas2
         */
        $casas2 = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $data = [
            'cliente' => $casas2->map(function ($o) {
                return $o->id;
            })->all(),
        ];

        $this->actingAs($this->user)
            ->post("casas/eliminar", $data)
            ->seeStatusCode(302);

        $this->assertEquals(Casa::whereNull('deleted_at')->count(), $casas1->count());
    }

    public function test_it_a_constructora_fails_trying_to_delete_several_casas_if_assigned()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        list($ejVentas, $broker) = $this->createEjVentas();

        /**
         * @var Collection $casas1
         */
        $casas1 = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);

        /**
         * @var Collection $casas2
         */
        $casas2 = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $data = [
            'cliente' => $casas1->map(function ($o) {
                return $o->id;
            })->all(),
        ];

        $this->actingAs($this->user)
            ->post("casas/eliminar", $data)
            ->seeStatusCode(302);

        $this->assertEquals(Casa::whereNull('deleted_at')->count(), $casas2->count()+$casas1->count());
    }

    public function test_it_a_constructora_succesfully_deasign_a_casa()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);
        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($this->user)
            ->get("casas/deCliente/{$casa->id}")
            ->seeStatusCode(200)
            ->seeInDatabase('casas', [
                'id' => $casa->id,
                'id_broker' => $casa->id_broker,
            ]);
    }

    /********* BROKER *********/

    public function test_it_a_broker_displays_casas_index()
    {
        $user = $this->getUserBroker();

        $this->actingAs($user)
            ->get('casas')
            ->seeStatusCode(200);
    }

    public function test_it_a_broker_shows_a_casa()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora()->first()->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas/{$casa->id}")
            ->seeStatusCode(200);
    }

    public function test_it_a_broker_fails_when_tries_to_show_wrong_casa()
    {
        $this->getUserBroker();

        /**
         * @var Constructora $constructora
         */
        list($broker, $constructora) = $this->createBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($this->user)
            ->get("casas/{$casa->id}")
            ->seeStatusCode(403);
    }

    public function test_it_a_broker_fails_trying_to_store_a_casa_due_to_permissions()
    {
        $this->getUserBroker();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        $data = [
            'cmodelo' => '1',
            'cmts2_total' => 50,
            'cmts2_construccion' => 20,
            'cmts2_adicionales' => 0,
            'crecamaras' => 2,
            'banos' => 1,
            'valor' => 32000,
            'cmonto_separacion' => 250,
            'cmonto_abono_inicial' => 1000,
            'cmonto_mts2_adicional' => 50,
            'ccantidad' => 50,
            'id_proyecto' => $proyecto->id,
        ];

        $this->actingAs($this->user)
            ->post("casas", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas', [
                'recamaras' => 2,
                'banos' => 1,
                'valor' => 32000,
                'monto_separacion' => 250,
                'monto_abono_inicial' => 1000,
                'monto_mts2_adicional' => 50,
                'id_proyecto' => $proyecto->id,
            ]);
    }

    public function test_it_a_broker_succesfully_updates_a_casa()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = $casa->toArray();

        $data['monto_separacion_u'] = 1000;
        $data['monto_abono_inicial_u'] = 2000;

        $this->actingAs($this->user)
            ->put("casas/{$casa->id}/broker", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('casas', [
                'id' => $casa->id,
                'monto_separacion' => 1000,
                'monto_abono_inicial' => 2000,
            ]);
    }

    public function test_it_a_broker_fails_trying_to_update_a_casa_if_not_assigned()
    {
        $user = $this->getUserBroker();

        /**
         * @var Constructora $constructora
         */
        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = $casa->toArray();

        $data['monto_separacion_u'] = 1000;
        $data['monto_abono_inicial_u'] = 2000;

        $this->actingAs($this->user)
            ->put("casas/{$casa->id}/broker", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas', [
                'id' => $casa->id,
                'monto_separacion' => 1000,
                'monto_abono_inicial' => 2000,
            ]);
    }

    public function test_it_a_broker_successfully_assign_ej_ventas_to_casa()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id
        ]);

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();
        $ejVentas->id_broker = $user->broker->id;
        $ejVentas->save();

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'id_casa' => $casa->id,
            'id_ej_ventas' => $ejVentas->id,
        ];

        $this->actingAs($this->user)
            ->post("casas/asignarEjecutivos", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('casas', [
                'id' => $casa->id,
                'id_ej_ventas' => $ejVentas->id,
            ]);
    }

    public function test_it_a_broker_fails_trying_assign_ej_ventas_to_casa_if_not_assigned()
    {
        $user = $this->getUserBroker();

        /**
         * @var Constructora $constructora
         */
        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();
        $ejVentas->id_broker = $user->broker->id;
        $ejVentas->save();

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'id_ej_ventas' => $ejVentas->id,
        ];

        $this->actingAs($this->user)
            ->post("casas/asignarEjecutivos", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas', [
                'id' => $casa->id,
                'id_ej_ventas' => $ejVentas->id,
            ]);
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_a_ej_ventas_lists_casa()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora()->first()->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $this->actingAs($user)
            ->get('casas')
            ->seeStatusCode(200);
    }

    public function test_it_a_ej_ventas_shows_a_casa()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora()->first()->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $this->actingAs($user)
            ->get("casas/{$casa->id}")
            ->seeStatusCode(200);
    }

    public function test_it_a_ej_ventas_does_not_show_a_casa_if_not_asigned()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora()->first()->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("casas/{$casa->id}")
            ->seeStatusCode(403);
    }

    public function test_it_a_ej_ventas_fails_when_tries_to_show_wrong_casa()
    {
        $this->getUserConstructora();

        /**
         * @var Constructora $constructora
         */
        list($constructora, $user) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($this->user)
            ->get("casas/{$casa->id}")
            ->seeStatusCode(403);
    }
}
