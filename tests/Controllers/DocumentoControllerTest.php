<?php

namespace Tests\Controllers;

use App\Models\Broker;
use App\Models\Cliente;
use App\Models\Distrito;
use App\Models\DocumentoCliente;
use App\Models\Pais;
use App\Models\Provincia;
use App\Models\TelefonoCliente;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class DocumentoControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_constructora_successfully_lists_documentos()
    {
        /**
         * @var User $user
         */
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $userCliente = $this->createUser(User::CLIENTE);
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker->id,
        ]);
        factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente->id,
        ]);

        /**
         * @var Broker $broker2
         */
        list($broker2) = $this->createBroker();
        $userCliente2 = $this->createUser(User::CLIENTE);
        $cliente2 = factory(Cliente::class)->create([
            'id_user' => $userCliente2->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker2->id,
        ]);
        factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente2->id,
        ]);

        $documentos = factory(DocumentoCliente::class, 3)->create([
            'id_cliente' => $cliente->id,
            'id_pais' => $pais->id,
        ]);
        factory(DocumentoCliente::class, 2)->create([
            'id_cliente' => $cliente2->id,
            'id_pais' => $pais->id,
        ]);

        $response = $this->actingAs($user)
            ->get("documentos")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['docucliente'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $documentos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($documentos->count(), count($data));
    }

    /********* BROKER *********/

    public function test_it_broker_successfully_lists_documentos()
    {
        /**
         * @var User $user
         */
        $user = $this->getUserBroker();

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $userCliente = $this->createUser(User::CLIENTE);
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);
        factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente->id,
        ]);

        /**
         * @var Broker $broker2
         */
        list($broker2) = $this->createBroker();
        $userCliente2 = $this->createUser(User::CLIENTE);
        $cliente2 = factory(Cliente::class)->create([
            'id_user' => $userCliente2->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker2->id,
        ]);
        factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente2->id,
        ]);

        $documentos = factory(DocumentoCliente::class, 3)->create([
            'id_cliente' => $cliente->id,
            'id_pais' => $pais->id,
        ]);
        factory(DocumentoCliente::class, 2)->create([
            'id_cliente' => $cliente2->id,
            'id_pais' => $pais->id,
        ]);

        $response = $this->actingAs($user)
            ->get("documentos")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['docucliente'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $documentos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($documentos->count(), count($data));
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_ej_ventas_successfully_lists_documentos()
    {
        /**
         * @var User $user
         */
        $user = $this->getUserEjVentas();

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $userCliente = $this->createUser(User::CLIENTE);
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);
        factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente->id,
        ]);

        /**
         * @var Broker $broker2
         */
        list($broker2) = $this->createBroker();
        $userCliente2 = $this->createUser(User::CLIENTE);
        $cliente2 = factory(Cliente::class)->create([
            'id_user' => $userCliente2->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker2->id,
        ]);
        factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente2->id,
        ]);

        $documentos = factory(DocumentoCliente::class, 3)->create([
            'id_cliente' => $cliente->id,
            'id_pais' => $pais->id,
        ]);
        factory(DocumentoCliente::class, 2)->create([
            'id_cliente' => $cliente2->id,
            'id_pais' => $pais->id,
        ]);

        $response = $this->actingAs($user)
            ->get("documentos")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['docucliente'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $documentos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($documentos->count(), count($data));
    }
}
