<?php
/**
 * Created by PhpStorm.
 * User: luis-hp
 * Date: 09/12/17
 * Time: 07:09 PM
 */

namespace Tests\Controllers;

use App\Models\Broker;
use App\Models\CasaEstado;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class CasaEstadoControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_constructora_successfully_lists_casas_estados()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $user->constructora->brokers()->sync([$broker->id]);

        /**
         * @var Broker $broker
         */
        list($broker2) = $this->createBroker();

        /**
         * @var Collection $casasEstados
         */
        $casasEstados = factory(CasaEstado::class, 5)->create([
            'id_broker' => $broker->id,
        ]);
        factory(CasaEstado::class, 5)->create([
            'id_broker' => $broker2->id,
        ]);

        $response = $this->actingAs($user)
            ->get("casas_estados")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['casasEstados'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $casasEstados->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($casasEstados->count(), count($data));
    }

    public function test_it_constructora_successfully_shows_casa_estado()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $user->constructora->brokers()->sync([$broker->id]);

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $casaEstado->id,
                'id_broker' => $casaEstado->id_broker,
                'nombre' => $casaEstado->nombre,
                'slug' => $casaEstado->slug,
            ]);
    }

    public function test_it_constructora_successfully_fails_showing_casa_estado_if_not_assigned()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(403);
    }

    /********* BROKER *********/

    public function test_it_broker_successfully_lists_casas_estados()
    {
        $user = $this->getUserBroker();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var Collection $casasEstados
         */
        $casasEstados = factory(CasaEstado::class, 5)->create([
            'id_broker' => $user->broker->id,
        ]);
        factory(CasaEstado::class, 5)->create([
            'id_broker' => $broker->id,
        ]);

        $response = $this->actingAs($user)
            ->get("casas_estados")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['casasEstados'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $casasEstados->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($casasEstados->count(), count($data));
    }

    public function test_it_broker_successfully_shows_casa_estado()
    {
        $user = $this->getUserBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $user->broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $casaEstado->id,
                'id_broker' => $casaEstado->id_broker,
                'nombre' => $casaEstado->nombre,
                'slug' => $casaEstado->slug,
            ]);
    }

    public function test_it_broker_successfully_fails_showing_casa_estado_if_not_assigned()
    {
        $user = $this->getUserBroker();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(403);
    }

    public function test_it_broker_successfully_stores_casa_estado()
    {
        $user = $this->getUserBroker();

        $data = [
            'nombre' => 'My weird name',
            'slug' => 'my_weird_name',
        ];

        $this->actingAs($user)
            ->post("casas_estados", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('casas_estados', $data);
    }

    public function test_it_broker_fails_storing_casa_estado_due_to_validations()
    {
        $user = $this->getUserBroker();

        $data = [
            'nombre' => 'My weird name is too too too long to be accepted in this request',
        ];

        $this->actingAs($user)
            ->post("casas_estados", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas_estados', $data);
    }

    public function test_it_broker_successfully_updates_casa_estado()
    {
        $user = $this->getUserBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'nombre' => 'This is my new name',
        ];

        $this->actingAs($user)
            ->put("casas_estados/{$casaEstado->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('casas_estados', [
                'id' => $casaEstado->id,
                'id_broker' => $casaEstado->id_broker,
                'nombre' => $data['nombre'],
                'slug' => $casaEstado->slug,
            ]);
    }

    public function test_it_broker_fails_updating_casa_estado_due_to_validations()
    {
        $user = $this->getUserBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'nombre' => 'My weird name is too too too long to be accepted in this request',
        ];

        $this->actingAs($user)
            ->put("casas_estados/{$casaEstado->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas_estados', [
                'id' => $casaEstado->id,
                'id_broker' => $casaEstado->id_broker,
                'nombre' => $data['nombre'],
                'slug' => $casaEstado->slug,
            ]);
    }

    public function test_it_broker_fails_updating_casa_estado_due_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $broker->id,
        ]);

        $data = [
            'nombre' => 'This is my new name',
        ];

        $this->actingAs($user)
            ->put("casas_estados/{$casaEstado->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas_estados', [
                'id' => $casaEstado->id,
                'id_broker' => $casaEstado->id_broker,
                'nombre' => $data['nombre'],
                'slug' => $casaEstado->slug,
            ]);
    }

    public function test_it_broker_successfully_deletes_casa_estado()
    {
        $user = $this->getUserBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $user->broker->id,
        ]);

        $this->actingAs($user)
            ->delete("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas_estados', [
                'id' => $casaEstado->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_broker_fails_deleting_casa_estado_due_to_validations()
    {
        $user = $this->getUserBroker();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($user)
            ->delete("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(302)
            ->seeInDatabase('casas_estados', [
                'id' => $casaEstado->id,
                'deleted_at' => null,
            ]);
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_ej_ventas_successfully_lists_casas_estados()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var Collection $casasEstados
         */
        $casasEstados = factory(CasaEstado::class, 5)->create([
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);
        factory(CasaEstado::class, 5)->create([
            'id_broker' => $broker->id,
        ]);

        $response = $this->actingAs($user)
            ->get("casas_estados")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['casasEstados'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $casasEstados->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($casasEstados->count(), count($data));
    }

    public function test_it_ej_ventas_successfully_shows_casa_estado()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $casaEstado->id,
                'id_broker' => $casaEstado->id_broker,
                'nombre' => $casaEstado->nombre,
                'slug' => $casaEstado->slug,
            ]);
    }

    public function test_it_ej_ventas_fails_showing_casa_estado_if_not_assigned()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(403);
    }

    public function test_it_ej_ventas_fails_storing_casa_estado_due_to_permissions()
    {
        $user = $this->getUserEjVentas();

        $data = [
            'nombre' => 'My weird name',
            'slug' => 'my_weird_name',
        ];

        $this->actingAs($user)
            ->post("casas_estados", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas_estados', $data);
    }

    /********* EJECUTIVO BANCO *********/

    public function test_it_ej_bancos_successfully_lists_casas_estados()
    {
        $user = $this->getUserEjBancos();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var Collection $casasEstados
         */
        $casasEstados = factory(CasaEstado::class, 5)->create([
            'id_broker' => $user->ejecutivoBancos->broker->id,
        ]);
        factory(CasaEstado::class, 5)->create([
            'id_broker' => $broker->id,
        ]);

        $response = $this->actingAs($user)
            ->get("casas_estados")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['casasEstados'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $casasEstados->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($casasEstados->count(), count($data));
    }

    public function test_it_ej_bancos_successfully_shows_casa_estado()
    {
        $user = $this->getUserEjBancos();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $user->ejecutivoBancos->broker->id,
            'nombre' => 'Test nombre',
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $casaEstado->id,
                'id_broker' => $casaEstado->id_broker,
                'nombre' => $casaEstado->nombre,
                'slug' => $casaEstado->slug,
            ]);
    }

    public function test_it_ej_bancos_fails_showing_casa_estado_if_not_assigned()
    {
        $user = $this->getUserEjBancos();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado = factory(CasaEstado::class)->create([
            'id_broker' => $broker->id,
        ]);

        $this->actingAs($user)
            ->get("casas_estados/{$casaEstado->id}")
            ->seeStatusCode(403);
    }

    public function test_it_ej_ventas_bancos_fails_storing_casa_estado_due_to_permissions()
    {
        $user = $this->getUserEjBancos();

        $data = [
            'nombre' => 'My weird name',
            'slug' => 'my_weird_name',
        ];

        $this->actingAs($user)
            ->post("casas_estados", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas_estados', $data);
    }
}
