<?php

namespace Tests\Controllers;

use App\Models\Broker;
use App\Models\Casa;
use App\Models\Cliente;
use App\Models\EjecutivoVentas;
use App\Models\Mensaje;
use App\Models\PorcentajeBroker;
use App\Models\Proyecto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\KitTestCase;

class BrokerControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_constructora_successfully_lists_brokers()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        $response = $this->actingAs($user)
            ->get('brokers')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['brokers']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = [$broker->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_constructora_successfully_lists_brokers_only_assigned()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        //We create 2 more brokers and neither of these should be retrevied
        $this->createBroker();
        $this->createBroker();

        $response = $this->actingAs($user)
            ->get('brokers')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['brokers']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = [$broker->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($data));
    }

    public function test_it_constructora_successfully_shows_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        $this->actingAs($user)
            ->get("brokers/{$broker->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $broker->id,
                'id_user' => $broker->id_user,
                'max_ej_bancos' => $broker->max_ej_bancos,
                'max_ej_ventas' => $broker->max_ej_ventas,
            ]);
    }

    public function test_it_constructora_fails_showing_broker_due_to_validations()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        $this->actingAs($user)
            ->get("brokers/{$broker->id}")
            ->seeStatusCode(403);
    }

    public function test_it_constructora_successfully_stores_broker()
    {
        $user = $this->getUserConstructora();

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Broker
            'max_ej_ventas' => ceil($user->constructora->max_ejecutivos_ventas / 2),
            'max_ej_bancos' => ceil($user->constructora->max_ejecutivos_bancos / 2),
        ];

        $this->actingAs($user)
            ->post("brokers", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("brokers", array_only($data, ['max_ej_ventas','max_ej_bancos']));
    }

    public function test_it_constructora_fails_storing_broker_due_to_validations()
    {
        $user = $this->getUserConstructora();

        $data = [
            //User
            'name' => 'OAINVOEINQW 9WEOIF EWMVOE WNFUIEWNEWJN EWJKF NEWKJFN EWF NEWJKFN EWJFN EWKJNFJKWENFKJEWN weof ewiuf eiuw',
            'nombre' => 'SuperNombre',
            'email' => 'myswadWailcom',
            'password' => '123456',
            'password_confirmation' => '123456789',
            //Broker
            'max_ej_ventas' => ceil($user->constructora->max_ejecutivos_ventas * 2),
            'max_ej_bancos' => ceil($user->constructora->max_ejecutivos_bancos * 2),
        ];

        $this->actingAs($user)
            ->post("brokers", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("brokers", array_only($data, ['max_ej_ventas','max_ej_bancos']));
    }

    public function test_it_constructora_successfully_updates_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            //Broker
            'max_ej_ventas' => ceil($user->constructora->max_ejecutivos_ventas / 2),
            'max_ej_bancos' => ceil($user->constructora->max_ejecutivos_bancos / 2),
        ];

        $this->actingAs($user)
            ->put("brokers/{$broker->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("brokers", array_merge(array_only($data, ['max_ej_ventas','max_ej_bancos']), ['id'=>$broker->id]));
    }

    public function test_it_constructora_fails_updating_broker_due_to_validations()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        $data = [
            //User
            'name' => 'OAINVOEINQW 9WEOIF EWMVOE WNFUIEWNEWJN EWJKF NEWKJFN EWF NEWJKFN EWJFN EWKJNFJKWENFKJEWN weof ewiuf eiuw',
            'nombre' => 'SuperNombre',
            'email' => 'myswadWailcom',
            //Broker
            'max_ej_ventas' => ceil($user->constructora->max_ejecutivos_ventas * 2),
            'max_ej_bancos' => ceil($user->constructora->max_ejecutivos_bancos / 2),
        ];

        $this->actingAs($user)
            ->put("brokers/{$broker->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("brokers", array_merge(array_only($data, ['max_ej_ventas','max_ej_bancos']), ['id'=>$broker->id]));
    }

    public function test_it_constructora_successfully_deletes_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        $this->actingAs($user)
            ->delete("brokers/{$broker->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("brokers", [
                'id' => $broker->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_constructora_fails_deleting_broker_not_assigned()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        $this->actingAs($user)
            ->delete("brokers/{$broker->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("brokers", [
                'id' => $broker->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_constructora_successfully_assigns_porcentaje_to_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);

        $data = [
            'id_broker' => $broker->id,
            'id_proyecto' => $proyecto->id,
            'porcentaje' => 15,
        ];

        $this->actingAs($user)
            ->post("brokers/porcentaje", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("porcentaje_broker", $data);
    }

    public function test_it_constructora_successfully_updates_porcentaje_to_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);

        $porcentajeBroker = factory(PorcentajeBroker::class)->create([
            'id_broker' => $broker->id,
            'id_proyecto' => $proyecto->id,
            'porcentaje' => 15,
        ]);

        $data = [
            'id_broker' => $broker->id,
            'id_proyecto' => $proyecto->id,
            'porcentaje' => 25,
        ];

        $this->actingAs($user)
            ->post("brokers/porcentaje", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("porcentaje_broker", array_merge($data, ['id'=>$porcentajeBroker->id,'id_proyecto'=>$proyecto->id]));
    }

    public function test_it_constructora_fails_assigning_porcentaje_to_broker_not_assigned()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);

        $data = [
            'id_broker' => $broker->id,
            'id_proyecto' => $proyecto->id,
            'porcentaje' => 15,
        ];

        $this->actingAs($user)
            ->post("brokers/porcentaje", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("porcentaje_broker", $data);
    }

    public function test_it_constructora_successfully_shows_porcentaje_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        $this->actingAs($user)
            ->get("brokers/{$broker->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $broker->id,
                'id_user' => $broker->id_user,
                'max_ej_bancos' => $broker->max_ej_bancos,
                'max_ej_ventas' => $broker->max_ej_ventas,
            ]);
    }

    public function test_it_constructora_fails_showing_porcentaje_broker_not_assigned()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        $this->actingAs($user)
            ->get("brokers/{$broker->id}")
            ->seeStatusCode(403);
    }

    public function test_it_constructora_successfully_removes_casas_to_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        $data = [
            'casaquitar' => $casa->id,
        ];

        $this->actingAs($user)
            ->post("brokers/eliminar_casas/{$broker->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("casas", [
                'id' => $casa->id,
                'id_broker' => null,
            ]);
    }

    public function test_it_constructora_fails_removing_casas_to_broker_if_already_assigned()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var Cliente $cliente
         */
        $cliente = $this->createCliente();
        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        $casa->cliente()->sync([$cliente->id]);

        $data = [
            'casaquitar' => $casa->id,
        ];

        $this->actingAs($user)
            ->post("brokers/eliminar_casas/{$broker->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("casas", [
                'id' => $casa->id,
                'id_broker' => $broker->id,
            ]);
    }

    /********* BROKER *********/

    public function test_it_broker_successfully_shows_mensajes_por_leer()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);
        /**
         * @var Cliente $cliente
         */
        $cliente = $this->createCliente();
        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        $casa->cliente()->sync([$cliente->id]);
        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();

        $mensajes = factory(Mensaje::class, 5)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->broker->id,
            'id_ej_ventas' => $ejVentas->id,
            'leido_broker' => 0,
        ]);
        //We create another messages to check only unread messages are counted
        factory(Mensaje::class, 5)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->broker->id,
            'id_ej_ventas' => $ejVentas->id,
            'leido_broker' => 1,
        ]);

        $this->actingAs($user)
            ->get("brokers/mensajes/porLeer")
            ->seeStatusCode(200)
            ->seeJsonContains([
                $mensajes->count(),
            ]);
    }
}
