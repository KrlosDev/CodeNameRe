<?php

namespace Tests\Controllers;

use App\Models\Casa;
use App\Models\Cliente;
use App\Models\EjecutivoVentas;
use App\Models\Mensaje;
use App\Models\Proyecto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class EjVentasControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_broker_successfully_lists_ej_ventas()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();
        $ejVentas->id_broker = $user->broker->id;
        $ejVentas->save();
        $this->createEjVentas();
        $this->createEjVentas();

        $response = $this->actingAs($user)
            ->get("ejecutivo_ventas")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['ejventas'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$ejVentas->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($ids));
    }

    public function test_it_broker_successfully_shows_ej_ventas()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();
        $ejVentas->id_broker = $user->broker->id;
        $ejVentas->save();

        $this->actingAs($user)
            ->get("ejecutivo_ventas/{$ejVentas->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $ejVentas->id,
                'id_broker' => $ejVentas->id_broker,
            ]);
    }

    public function test_it_broker_fails_showing_ej_ventas_due_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();

        $this->actingAs($user)
            ->get("ejecutivo_ventas/{$ejVentas->id}")
            ->seeStatusCode(403);
    }

    public function test_it_broker_successfully_stores_ej_ventas()
    {
        $user = $this->getUserBroker();

        $data = [
            //User
            'name' => 'Name',
            'nombre' => 'SuperNombre',
            'email' => 'myawesom@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //EjVentas
            'id_broker' => $user->broker->id,
        ];

        $this->actingAs($user)
            ->post("ejecutivo_ventas", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("ejecutivos_ventas", array_only($data, ['id_broker']));
    }

    public function test_it_broker_fails_storing_ej_ventas_due_to_validations()
    {
        $user = $this->getUserBroker();

        $data = [
            //User
            'name' => 'Nameoiweeioewowewfewfomwefeoifewomewfomwefewoimfefoimwfweoimfewoifmfewomifewoifmew',
            'nombre' => 'SuperNombre',
            'email' => 'myawesomemail',
            'password' => '123456',
            'password_confirmation' => '123456789',
            //EjVentas
            'id_broker' => $user->broker->id,
        ];
        
        $this->actingAs($user)
            ->post("ejecutivo_ventas", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("ejecutivos_ventas", array_only($data, ['id_broker']));
    }

    public function test_it_broker_successfully_updates_ej_ventas()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();
        $ejVentas->id_broker = $user->broker->id;
        $ejVentas->save();

        $data = [
            'name' => 'Name',
            'nombre' => 'SuperNombre',
            'email' => 'myawesom@email.com',
        ];

        $this->actingAs($user)
            ->put("ejecutivo_ventas/{$ejVentas->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("users", $data+['id'=>$ejVentas->user->id]);
    }

    public function test_it_broker_fails_updating_ej_ventas_due_to_validations()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();
        $ejVentas->id_broker = $user->broker->id;
        $ejVentas->save();

        $data = [
            'name' => 'Naqwqwqwqwwqqwqwfqwwqfqwfinqwoifnqwfiuqwbnfiuqwnf oiUWBFOIAWEFBAWOEIUFBNWAEIOUFNEIUqme',
            'nombre' => 'SuperNombre',
            'email' => 'myawesomom',
        ];

        $this->actingAs($user)
            ->put("ejecutivo_ventas/{$ejVentas->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("users", $data+['id'=>$ejVentas->user->id]);
    }

    public function test_it_broker_fails_updating_ej_ventas_due_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();

        $data = [
            'name' => 'Name',
            'nombre' => 'SuperNombre',
            'email' => 'myawesom@email.com',
        ];

        $this->actingAs($user)
            ->put("ejecutivo_ventas/{$ejVentas->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("users", $data+['id'=>$ejVentas->user->id]);
    }

    public function test_it_broker_successfully_deletes_ej_ventas()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();
        $ejVentas->id_broker = $user->broker->id;
        $ejVentas->save();

        $this->actingAs($user)
            ->delete("ejecutivo_ventas/{$ejVentas->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("ejecutivos_ventas", [
                'id' => $ejVentas->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_broker_fails_deleting_ej_ventas_due_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();

        $this->actingAs($user)
            ->delete("ejecutivo_ventas/{$ejVentas->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("ejecutivos_ventas", [
                'id' => $ejVentas->id,
                'deleted_at' => null,
            ]);
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_ej_ventas_successfully_shows_mensajes_por_leer()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
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

        $mensajes = factory(Mensaje::class, 5)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'leido_ventas' => 0,
        ]);
        //We create another messages to check only unread messages are counted
        factory(Mensaje::class, 5)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'leido_ventas' => 1,
        ]);

        $this->actingAs($user)
            ->get("ejecutivo_ventas/mensajes/porLeer")
            ->seeStatusCode(200)
            ->seeJsonContains([
                $mensajes->count(),
            ]);
    }
}
