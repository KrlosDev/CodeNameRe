<?php

namespace Tests\Controllers;

use App\Jobs\SendRegisterNotification;
use App\Models\Casa;
use App\Models\Cliente;
use App\Models\ClienteCasa;
use App\Models\Distrito;
use App\Models\Pais;
use App\Models\Provincia;
use App\Models\Proyecto;
use App\Models\TelefonoCliente;
use App\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class ClienteControllerTest extends KitTestCase
{
    use DatabaseTransactions;
    
    public function test_it_constructora_successfully_lists_clientes()
    {
        $user = $this->getUserConstructora();

        factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);

        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);
        
        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker->id,
        ]);
        //$cliente2 = $this->createCliente();

        $response = $this->actingAs($user)
            ->get("clientes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['clientes'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$cliente->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($data));
    }
    
    public function test_it_constructora_successfully_shows_cliente()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);

        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);
        
        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);

        $response = $this->actingAs($user)
            ->get("clientes/{$cliente->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $cliente->id,
                'id_user' => $cliente->id_user,
                'id_distrito' => $cliente->id_distrito,
                'id_pais' => $cliente->id_pais,
                'id_broker' => $cliente->id_broker,
            ]);
    }
    
    public function test_it_constructora_fails_showing_cliente_due_to_permissions()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);

        list($broker) = $this->createBroker();
        
        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);

        $response = $this->actingAs($user)
            ->get("clientes/{$cliente->id}")
            ->seeStatusCode(403);
    }
    
    /********* BROKER *********/
    
    public function test_it_broker_successfully_lists_clientes()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);
        //$cliente2 = $this->createCliente();

        $response = $this->actingAs($user)
            ->get("clientes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['clientes'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$cliente->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($data));
    }
    
    public function test_it_broker_successfully_shows_cliente()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);
        
        $response = $this->actingAs($user)
            ->get("clientes/{$cliente->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $cliente->id,
                'id_user' => $cliente->id_user,
                'id_distrito' => $cliente->id_distrito,
                'id_pais' => $cliente->id_pais,
                'id_broker' => $cliente->id_broker,
            ]);
    }
    
    public function test_it_broker_fails_showing_cliente_due_to_validation()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);
        
        $response = $this->actingAs($user)
            ->get("clientes/{$cliente->id}")
            ->seeStatusCode(403);
    }
    
    public function test_it_broker_successfully_stores_independendiente_cliente()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            'direccion' => 'I live few streets above',
            'notas' => 'These are my notes as client',
            'casa_apartamento' => Cliente::CASA,
            'tipo_trabajo' => Cliente::INDEPENDIENTE,
            'salario' => 1500,
            //Teléfono
            'telefono' => '15975388',
            //Casa
            'id_casa' => $casa->id,
        ];

        $this->expectsJobs([SendRegisterNotification::class]);
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", array_only(
                $data,
                [
                    'nombre','apellido','estado_civil','id_pais','id_distrito',
                    'direccion','notas','casa_apartamento','tipo_trabajo','salario'
                ]
            ))
            ->seeInDatabase("users", array_only(
                $data,
                [
                    'nombre','name','email'
                ]
            ))
            ->seeInDatabase("clientes_casas", [
                'id_casa' => $casa->id,
            ])
            ->seeInDatabase("telefonos_clientes", [
                'telefono' => $data['telefono'],
            ]);
    }
    
    public function test_it_broker_successfully_stores_empleado_cliente()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            'direccion' => 'I live few streets above',
            'notas' => 'These are my notes as client',
            'casa_apartamento' => Cliente::CASA,
            'tipo_trabajo' => Cliente::EMPLEADO,
            'empresa' => 'Super Enterprise',
            'cargo_empresa' => 'Dealer',
            'años_laborando' => 20,
            'direccion_empresa' => 'Above the stars',
            'telefonos_empresa' => '10384729',
            'email_empresa' => 'starts@superenterprise.com',
            'salario' => 1500,
            //Teléfono
            'telefono' => '15975388',
            //Casa
            'id_casa' => $casa->id,
        ];

        $this->expectsJobs([SendRegisterNotification::class]);
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", array_only(
                $data,
                [
                    'nombre','apellido','estado_civil','id_pais','id_distrito',
                    'direccion','notas','casa_apartamento','tipo_trabajo','empresa',
                    'cargo_empresa','anios_laborando','direccion_empresa','telefonos_empresa',
                    'email_empresa','salario'
                ]
            ))
            ->seeInDatabase("users", array_only(
                $data,
                [
                    'nombre','name','email'
                ]
            ))
            ->seeInDatabase("clientes_casas", [
                'id_casa' => $casa->id,
            ])
            ->seeInDatabase("telefonos_clientes", [
                'telefono' => $data['telefono'],
            ]);
    }
    
    public function test_it_broker_fails_storing_independendiente_cliente_due_to_validations()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            //Teléfono
            'telefono' => '15975388',
            //Casa
            'id_casa' => $casa->id,
        ];
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("clientes", array_only(
                $data,
                [
                    'nombre','apellido','estado_civil','id_pais','id_distrito',
                    'direccion','notas','casa_apartamento','tipo_trabajo','salario'
                ]
            ))
            ->notSeeInDatabase("users", array_only(
                $data,
                [
                    'nombre','name','email'
                ]
            ))
            ->notSeeInDatabase("clientes_casas", [
                'id_casa' => $casa->id,
            ])
            ->notSeeInDatabase("telefonos_clientes", [
                'telefono' => $data['telefono'],
            ]);
    }
    
    public function test_it_broker_fails_storing_empleado_cliente_due_to_validations()
    {
        $user = $this->getUserBroker();
        
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);

        $data = [
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::CASADO,
            'salario' => 2025,
        ];
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("clientes", array_merge(array_only(
                $data,
                [
                    'apellido','fecha_nacimiento','estado_civil','salario'
                ]
            ), ['id' => $cliente->id]));
    }
    
    public function test_it_broker_successfully_updates_cliente()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        $telefono = factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente->id,
        ]);
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);

        $data = [
            //User
            'nombre' => 'New Lastname',
            'apellido' => 'New Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            'direccion' => 'New direction',
            'tipo_trabajo' => Cliente::INDEPENDIENTE,
            'notas' => 'These are my notes as client',
            'salario' => 1500,
            //Teléfono
            'telefono' => '15975388',
        ];

        $this->actingAs($user)
            ->put("clientes/{$cliente->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", array_merge(array_only(
                $data,
            [
                'apellido','fecha_nacimiento','id_pais','id_distrito','direccion','notas','salario'
            ]
        ), ['id'=>$cliente->id]));
    }
    
    public function test_it_broker_fails_updating_cliente_due_to_validations()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        $telefono = factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente->id,
        ]);
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $data = [
            //User
            'nombre' => 'New Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
        ];

        $this->actingAs($user)
            ->put("clientes/{$cliente->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("clientes", array_merge(array_only(
                $data,
            [
                'apellido','fecha_nacimiento','id_pais','id_distrito','direccion','notas','salario'
            ]
        ), ['id'=>$cliente->id]));
    }
    
    public function test_it_broker_successfully_deletes_cliente()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);
        
        $response = $this->actingAs($user)
            ->delete("clientes/{$cliente->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("clientes", [
                'id' => $cliente->id,
                'deleted_at' => null,
            ]);
    }
    
    public function test_it_broker_fails_deleting_cliente_if_has_casa_assigned()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);
        
        $response = $this->actingAs($user)
            ->delete("clientes/{$cliente->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", [
                'id' => $cliente->id,
                'deleted_at' => null,
            ]);
    }
    
    public function test_it_broker_successfully_buscar_clientes()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->broker->id,
            'identificacion' => '15975322',
        ]);
        $cliente2 = $this->createCliente();
        
        $response = $this->actingAs($user)
            ->get("clientes/buscar?term=159753")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'label' => $cliente->identificacion.'/'.$cliente->nombre,
            ]);
    }
    
    /********* EJECUTIVO VENTAS *********/
    
    public function test_it_ej_ventas_successfully_lists_clientes()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);
        //$cliente2 = $this->createCliente();

        $response = $this->actingAs($user)
            ->get("clientes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['clientes'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$cliente->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($data));
    }
    
    public function test_it_ej_ventas_successfully_shows_cliente()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);
        
        $response = $this->actingAs($user)
            ->get("clientes/{$cliente->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $cliente->id,
                'id_user' => $cliente->id_user,
                'id_distrito' => $cliente->id_distrito,
                'id_pais' => $cliente->id_pais,
                'id_broker' => $cliente->id_broker,
            ]);
    }
    
    public function test_it_ej_ventas_fails_showing_cliente_due_to_permissions()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);
        
        $response = $this->actingAs($user)
            ->get("clientes/{$cliente->id}")
            ->seeStatusCode(403);
    }
    
    public function test_it_ej_ventas_successfully_stores_independendiente_cliente()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            'direccion' => 'I live few streets above',
            'notas' => 'These are my notes as client',
            'casa_apartamento' => Cliente::CASA,
            'tipo_trabajo' => Cliente::INDEPENDIENTE,
            'salario' => 1500,
            //Teléfono
            'telefono' => '15975388',
            //Casa
            'id_casa' => $casa->id,
        ];

        $this->expectsJobs([SendRegisterNotification::class]);
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", array_only(
                $data,
                [
                    'nombre','apellido','estado_civil','id_pais','id_distrito',
                    'direccion','notas','casa_apartamento','tipo_trabajo','salario'
                ]
            ))
            ->seeInDatabase("users", array_only(
                $data,
                [
                    'nombre','name','email'
                ]
            ))
            ->seeInDatabase("clientes_casas", [
                'id_casa' => $casa->id,
            ])
            ->seeInDatabase("telefonos_clientes", [
                'telefono' => $data['telefono'],
            ]);
    }
    
    public function test_it_ej_ventas_successfully_stores_empleado_cliente()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            'direccion' => 'I live few streets above',
            'notas' => 'These are my notes as client',
            'casa_apartamento' => Cliente::CASA,
            'tipo_trabajo' => Cliente::EMPLEADO,
            'empresa' => 'Super Enterprise',
            'cargo_empresa' => 'Dealer',
            'años_laborando' => 20,
            'direccion_empresa' => 'Above the stars',
            'telefonos_empresa' => '10384729',
            'email_empresa' => 'starts@superenterprise.com',
            'salario' => 1500,
            //Teléfono
            'telefono' => '15975388',
            //Casa
            'id_casa' => $casa->id,
        ];

        $this->expectsJobs([SendRegisterNotification::class]);
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", array_only(
                $data,
                [
                    'nombre','apellido','estado_civil','id_pais','id_distrito',
                    'direccion','notas','casa_apartamento','tipo_trabajo','empresa',
                    'cargo_empresa','anios_laborando','direccion_empresa','telefonos_empresa',
                    'email_empresa','salario'
                ]
            ))
            ->seeInDatabase("users", array_only(
                $data,
                [
                    'nombre','name','email'
                ]
            ))
            ->seeInDatabase("clientes_casas", [
                'id_casa' => $casa->id,
            ])
            ->seeInDatabase("telefonos_clientes", [
                'telefono' => $data['telefono'],
            ]);
    }
    
    public function test_it_ej_ventas_fails_storing_independendiente_cliente_due_to_validations()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $data = [
            //User
            'name' => 'My Name',
            'nombre' => 'SuperNombre',
            'email' => 'myswadW@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            //Teléfono
            'telefono' => '15975388',
            //Casa
            'id_casa' => $casa->id,
        ];
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("clientes", array_only(
                $data,
                [
                    'nombre','apellido','estado_civil','id_pais','id_distrito',
                    'direccion','notas','casa_apartamento','tipo_trabajo','salario'
                ]
            ))
            ->notSeeInDatabase("users", array_only(
                $data,
                [
                    'nombre','name','email'
                ]
            ))
            ->notSeeInDatabase("clientes_casas", [
                'id_casa' => $casa->id,
            ])
            ->notSeeInDatabase("telefonos_clientes", [
                'telefono' => $data['telefono'],
            ]);
    }
    
    public function test_it_ej_ventas_fails_storing_empleado_cliente_due_to_validations()
    {
        $user = $this->getUserEjVentas();
        
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);

        $data = [
            //Cliente
            'apellido' => 'Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::CASADO,
            'salario' => 2025,
        ];
        
        $this->actingAs($user)
            ->post("clientes", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("clientes", array_merge(array_only(
                $data,
                [
                    'apellido','fecha_nacimiento','estado_civil','salario'
                ]
            ), ['id' => $cliente->id]));
    }
    
    public function test_it_ej_ventas_successfully_updates_cliente()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $telefono = factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente->id,
        ]);
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'id_ej_ventas' => $user->ejecutivoVentas->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);

        $data = [
            //User
            'nombre' => 'New Lastname',
            'apellido' => 'New Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
            'direccion' => 'New direction',
            'tipo_trabajo' => Cliente::INDEPENDIENTE,
            'notas' => 'These are my notes as client',
            'salario' => 1500,
            //Teléfono
            'telefono' => '15975388',
        ];

        $this->actingAs($user)
            ->put("clientes/{$cliente->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", array_merge(array_only(
                $data,
            [
                'apellido','fecha_nacimiento','id_pais','id_distrito','direccion','notas','salario'
            ]
        ), ['id'=>$cliente->id]));
    }
    
    public function test_it_ej_ventas_fails_updating_cliente_due_to_validations()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();
        
        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $telefono = factory(TelefonoCliente::class)->create([
            'id_cliente' => $cliente->id,
        ]);
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $data = [
            //User
            'nombre' => 'New Lastname',
            'identificacionc' => '123456789',
            'fecha_nacimiento' => '1991-06-14',
            'estado_civil' => Cliente::SOLTERO,
            'id_pais' => $pais->id,
            'id_distrito' => $distrito->id,
        ];

        $this->actingAs($user)
            ->put("clientes/{$cliente->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("clientes", array_merge(array_only(
                $data,
            [
                'apellido','fecha_nacimiento','id_pais','id_distrito','direccion','notas','salario'
            ]
        ), ['id'=>$cliente->id]));
    }
    
    public function test_it_ej_ventas_fails_deleting_cliente_due_to_permissions()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);

        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
        ]);
        
        $response = $this->actingAs($user)
            ->delete("clientes/{$cliente->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("clientes", [
                'id' => $cliente->id,
                'deleted_at' => null,
            ]);
    }
    
    public function test_it_ej_ventas_successfully_buscar_clientes()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoVentas->broker->id,
            'identificacion' => '15975322',
        ]);
        $cliente2 = $this->createCliente();
        
        $response = $this->actingAs($user)
            ->get("clientes/buscar?term=159753")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'label' => $cliente->identificacion.'/'.$cliente->nombre,
            ]);
    }
    
    /********* EJECUTIVO BANCOS *********/
    
    public function test_it_ej_bancos_successfully_lists_clientes()
    {
        $user = $this->getUserEjBancos();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoBancos->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoBancos->broker->id,
        ]);
        $cliente2 = $this->createCliente();
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoBancos->broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ], ['id_ej_bancos'=>$user->ejecutivoBancos->id]);

        $clienteCasa = ClienteCasa::where('id_casa', $casa->id)
            ->where('id_cliente', $cliente->id)
            ->first();

        $clienteCasa->id_ej_bancos = $user->ejecutivoBancos->id;
        $clienteCasa->save();

        $response = $this->actingAs($user)
            ->get("clientes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['clientes'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$cliente->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($data));
    }
    
    public function test_it_ej_bancos_successfully_shows_cliente()
    {
        $user = $this->getUserEjBancos();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoBancos->broker->constructora->first()->id,
        ]);

        /**
         * @var User $user
         */
        $userCliente = $this->createUser(User::CLIENTE);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $userCliente->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $user->ejecutivoBancos->broker->id,
        ]);
        
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $user->ejecutivoBancos->broker->id,
        ]);

        $cliente->clienteCasa()->sync([
            $casa->id,
        ]);

        $clienteCasa = ClienteCasa::where('id_casa', $casa->id)
            ->where('id_cliente', $cliente->id)
            ->first();

        $clienteCasa->id_ej_bancos = $user->ejecutivoBancos->id;
        $clienteCasa->save();

        $response = $this->actingAs($user)
            ->get("clientes/{$cliente->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $cliente->id,
                'id_user' => $userCliente->id,
                'id_distrito' => $distrito->id,
                'id_pais' => $pais->id,
                'id_broker' => $user->ejecutivoBancos->broker->id,
            ]);
    }
    
    public function test_it_ej_bancos_fails_showing_cliente_due_to_permissions()
    {
        $user = $this->getUserEjBancos();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoBancos->broker->constructora->first()->id,
        ]);

        $cliente2 = $this->createCliente();
        
        $response = $this->actingAs($user)
            ->get("clientes/{$cliente2->id}")
            ->seeStatusCode(403);
    }
}
