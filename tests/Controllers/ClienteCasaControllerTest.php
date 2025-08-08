<?php

namespace Tests\Controllers;

use App\Models\Casa;
use App\Models\Cliente;
use App\Models\Distrito;
use App\Models\EjecutivoBancos;
use App\Models\EjecutivoVentas;
use App\Models\Pais;
use App\Models\Provincia;
use App\Models\Proyecto;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\KitTestCase;

class ClienteCasaControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_broker_successfully_assigns_casa_to_cliente()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);
        /**
         * @var User $userCliente
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        /**
         * @var Cliente $cliente
         */
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'clientanadir' => $cliente->id,
            'casaid' => $casa->id,
        ];

        $this->actingAs($user)
            ->post("clientescasa", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes_casas", [
            'id_cliente' => $cliente->id,
            'id_casa' => $casa->id,
        ]);
    }

    public function test_it_broker_successfully_assigns_casa_to_cliente_with_ej_bancos()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);
        /**
         * @var User $userCliente
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        /**
         * @var Cliente $cliente
         */
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var User $userBanco
         */
        $userBanco = $this->createUser(User::EJ_BANCOS);
        $ejBancos = factory(EjecutivoBancos::class)->create([
            'id_user' => $userBanco->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'clientanadir' => $cliente->id,
            'casaid' => $casa->id,
            'id_ej_bancos' => $ejBancos->id,
        ];

        $this->actingAs($user)
            ->post("clientescasa", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes_casas", [
                'id_cliente' => $cliente->id,
                'id_casa' => $casa->id,
                'id_ej_bancos' => $ejBancos->id,
            ]);
    }

    public function test_it_broker_successfully_assigns_casa_to_cliente_with_ej_bancos_and_ej_ventas()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);
        /**
         * @var User $userCliente
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        /**
         * @var Cliente $cliente
         */
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var User $userBanco
         */
        $userBanco = $this->createUser(User::EJ_BANCOS);
        $ejBancos = factory(EjecutivoBancos::class)->create([
            'id_user' => $userBanco->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var User $userVenta
         */
        $userVenta = $this->createUser(User::EJ_VENTAS);
        $ejVentas = factory(EjecutivoVentas::class)->create([
            'id_user' => $userVenta->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'clientanadir' => $cliente->id,
            'casaid' => $casa->id,
            'id_ej_bancos' => $ejBancos->id,
            'id_ej_ventas' => $ejVentas->id,
        ];

        $this->actingAs($user)
            ->post("clientescasa", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes_casas", [
                'id_cliente' => $cliente->id,
                'id_casa' => $casa->id,
                'id_ej_bancos' => $ejBancos->id,
            ])
            ->seeInDatabase("casas", [
                'id' => $casa->id,
                'id_ej_ventas' => $ejVentas->id,
            ]);
    }

    public function test_it_broker_successfully_assigns_casa_to_cliente_with_ej_ventas()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);
        /**
         * @var User $userCliente
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        /**
         * @var Cliente $cliente
         */
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        /**
         * @var User $userVenta
         */
        $userVenta = $this->createUser(User::EJ_VENTAS);
        $ejVentas = factory(EjecutivoVentas::class)->create([
            'id_user' => $userVenta->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            'clientanadir' => $cliente->id,
            'casaid' => $casa->id,
            'id_ej_ventas' => $ejVentas->id,
        ];

        $this->actingAs($user)
            ->post("clientescasa", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes_casas", [
                'id_cliente' => $cliente->id,
                'id_casa' => $casa->id,
            ])
            ->seeInDatabase("casas", [
                'id' => $casa->id,
                'id_ej_ventas' => $ejVentas->id,
            ]);
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_ej_ventas_successfully_assigns_casa_to_cliente()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);
        /**
         * @var User $userCliente
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        /**
         * @var Cliente $cliente
         */
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $data = [
            'clientanadir' => $cliente->id,
            'casaid' => $casa->id,
        ];

        $this->actingAs($user)
            ->post("clientescasa", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes_casas", [
                'id_cliente' => $cliente->id,
                'id_casa' => $casa->id,
            ])
            ->seeInDatabase("casas", [
                'id' => $casa->id,
                'id_ej_ventas' => $user->ejecutivoVentas->id,
            ]);
    }

    public function test_it_ej_ventas_successfully_assigns_casa_to_cliente_with_ej_bancos()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);
        /**
         * @var User $userCliente
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        /**
         * @var Cliente $cliente
         */
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        /**
         * @var User $userBanco
         */
        $userBanco = $this->createUser(User::EJ_BANCOS);
        $ejBancos = factory(EjecutivoBancos::class)->create([
            'id_user' => $userBanco->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        /**
         * @var User $userVenta
         */
        $userVenta = $this->createUser(User::EJ_VENTAS);
        $ejVentas = factory(EjecutivoVentas::class)->create([
            'id_user' => $userVenta->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $data = [
            'clientanadir' => $cliente->id,
            'casaid' => $casa->id,
            'id_ej_bancos' => $ejBancos->id,
            'id_ej_ventas' => $ejVentas->id,
        ];

        $this->actingAs($user)
            ->post("clientescasa", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes_casas", [
                'id_cliente' => $cliente->id,
                'id_casa' => $casa->id,
                'id_ej_bancos' => $ejBancos->id,
            ])
            ->seeInDatabase("casas", [
                'id' => $casa->id,
                //Check for user even if we pass another ejVentas only can be auto assigned
                'id_ej_ventas' => $user->ejecutivoVentas->id,
            ]);
    }
}
