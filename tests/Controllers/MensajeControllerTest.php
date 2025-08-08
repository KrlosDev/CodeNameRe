<?php

namespace Tests\Controllers;

use App\Models\Broker;
use App\Models\Casa;
use App\Models\Cliente;
use App\Models\Constructora;
use App\Models\EjecutivoVentas;
use App\Models\Mensaje;
use App\Models\Proyecto;
use App\Models\TelefonoCliente;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class MensajeControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_broker_successfully_lists_mensajes()
    {
        $user = $this->getUserBroker();
        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

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
        ]);
        factory(Mensaje::class, 5)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);

        $response = $this->actingAs($user)
            ->get('mensajes')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['mensajes'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $mensajes->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($mensajes->count(), count($ids));
    }

    public function test_it_broker_successfully_shows_mensaje_and_nombre()
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
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = factory(Mensaje::class)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);
        TelefonoCliente::create([
            'id_cliente' => $cliente->id,
            'telefono' => '23123123',
        ]);

        $this->actingAs($user)
            ->get("mensajes/{$mensaje->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $mensaje->id,
                'id_cliente' => $mensaje->id_cliente,
                'id_broker' => $mensaje->id_broker,
                'id_ej_ventas' => $mensaje->id_ej_ventas,
                'nombre' => $cliente->nombre,
            ]);
    }

    public function test_it_broker_successfully_updates_mensaje()
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
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = factory(Mensaje::class)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->broker->id,
            'id_ej_ventas' => $ejVentas->id,
            'leido_broker' => 0,
        ]);

        $this->actingAs($user)
            ->put("mensajes/{$mensaje->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("mensajes", [
                'id' => $mensaje->id,
                'leido_broker' => 1,
            ]);
    }

    public function test_it_broker_successfully_deletes_mensaje()
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
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = factory(Mensaje::class)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);

        $this->actingAs($user)
            ->delete("mensajes/{$mensaje->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("mensajes", [
                'id' => $mensaje->id,
                'broker_deleted_at' => null,
            ]);
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_ej_ventas_successfully_lists_mensajes()
    {
        $user = $this->getUserEjVentas();
        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

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
        /**
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas) = $this->createEjVentas();

        $mensajes = factory(Mensaje::class, 5)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);
        factory(Mensaje::class, 5)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);

        $response = $this->actingAs($user)
            ->get('mensajes')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['mensajes'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $mensajes->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($mensajes->count(), count($ids));
    }

    public function test_it_ej_ventas_successfully_shows_mensaje_and_nombre()
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
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = factory(Mensaje::class)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);
        TelefonoCliente::create([
            'id_cliente' => $cliente->id,
            'telefono' => '23123123',
        ]);

        $this->actingAs($user)
            ->get("mensajes/{$mensaje->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $mensaje->id,
                'id_cliente' => $mensaje->id_cliente,
                'id_broker' => $mensaje->id_broker,
                'id_ej_ventas' => $mensaje->id_ej_ventas,
                'nombre' => $cliente->nombre,
            ]);
    }

    public function test_it_ej_ventas_successfully_updates_mensaje()
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
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = factory(Mensaje::class)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
            'leido_ventas' => 0,
        ]);

        $this->actingAs($user)
            ->put("mensajes/{$mensaje->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("mensajes", [
                'id' => $mensaje->id,
                'leido_ventas' => 1,
            ]);
    }

    public function test_it_ej_ventas_successfully_deletes_mensaje()
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
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = factory(Mensaje::class)->create([
            'id_cliente' => $cliente->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $this->actingAs($user)
            ->delete("mensajes/{$mensaje->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("mensajes", [
                'id' => $mensaje->id,
                'ventas_deleted_at' => null,
            ]);
    }

    /********* CLIENTE *********/

    public function test_it_cliente_successfully_shows_mensaje_and_nombre()
    {
        $user = $this->getUserCliente();
        /**
         * @var Constructora $constructora
         */
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
        ]);
        $casa->cliente()->sync([$user->cliente->id]);
        /**
         * @var Broker $broker
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas, $broker) = $this->createEjVentas();
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = factory(Mensaje::class)->create([
            'id_cliente' => $user->cliente->id,
            'id_broker' => $broker->id,
            'id_ej_ventas' => $ejVentas->id,
        ]);
        TelefonoCliente::create([
            'id_cliente' => $user->cliente->id,
            'telefono' => '23123123',
        ]);

        $this->actingAs($user)
            ->get("mensajes/{$mensaje->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $mensaje->id,
                'id_cliente' => $mensaje->id_cliente,
                'id_broker' => $mensaje->id_broker,
                'id_ej_ventas' => $mensaje->id_ej_ventas,
                'nombre' => $user->cliente->nombre,
            ]);
    }

    public function test_it_cliente_successfully_sends_mensaje()
    {
        $user = $this->getUserCliente();
        /**
         * @var Constructora $constructora
         */
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
        ]);
        $casa->cliente()->sync([$user->cliente->id]);
        /**
         * @var Broker $broker
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas, $broker) = $this->createEjVentas();
        TelefonoCliente::create([
            'id_cliente' => $user->cliente->id,
            'telefono' => '23123123',
        ]);

        $data = [
            'titulo' => 'My test title',
            'descripcion' => 'My test title',
            'para' => $ejVentas->id,
        ];

        $this->actingAs($user)
            ->post("mensajes", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('mensajes', [
                'id_cliente' => $user->cliente->id,
                'titulo' => 'My test title',
                'descripcion' => 'My test title',
                'id_ej_ventas' => $ejVentas->id,
                'leido_broker' => 0,
                'leido_ventas' => 0,
            ]);
    }

    public function test_it_cliente_fails_sending_mensaje_due_to_validations()
    {
        $user = $this->getUserCliente();
        /**
         * @var Constructora $constructora
         */
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
        ]);
        $casa->cliente()->sync([$user->cliente->id]);
        /**
         * @var Broker $broker
         * @var EjecutivoVentas $ejVentas
         */
        list($ejVentas, $broker) = $this->createEjVentas();
        TelefonoCliente::create([
            'id_cliente' => $user->cliente->id,
            'telefono' => '23123123',
        ]);

        $data = [
            'titulo' => 'My test titleqf qowifqwonqwfoqenfoqwnfwqoinfoiq',
            'descripcion' => 'My test title',
            'para' => 'afawf554',
        ];

        $this->actingAs($user)
            ->post("mensajes", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('mensajes', [
                'id_cliente' => $user->cliente->id,
                'titulo' => 'My test title',
            ]);
    }
}
