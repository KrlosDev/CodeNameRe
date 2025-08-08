<?php

namespace Tests;

use App\Models\Broker;
use App\Models\Cliente;
use App\Models\Configuracion;
use App\Models\Constructora;
use App\Models\Distrito;
use App\Models\EjecutivoBancos;
use App\Models\EjecutivoVentas;
use App\Models\Licencia;
use App\Models\Pais;
use App\Models\Provincia;
use App\User;
use Faker\Factory;
use Illuminate\Support\Facades\Artisan;
use Laravel\BrowserKitTesting\TestCase as BaseTestCase;

class KitTestCase extends BaseTestCase
{
    use CreatesApplication;

    const TEST_NAME = "johndoe";
    const TEST_PASSWORD = "123456";
    const TEST_EMAIL = "john@example.com";

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl;
    /**
     * @var User $user
     */
    protected $user;
    protected $headers;
    /**
     * @var Factory|\Faker\Generator $faker
     */
    protected $faker;

    public function setUp()
    {
        parent::setUp();



        //To make hard reset of migrations
        //Artisan::call('migrate:reset');
        Artisan::call('migrate');
        Artisan::call('db:seed');

        $this->baseUrl = env('APP_URL_TEST', 'localhost');
        $this->headers = [];
        $this->faker = Factory::create();
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     * @return \Laravel\BrowserKitTesting\Concerns\MakesHttpRequests
     */
    public function post($uri, array $data = [], array $headers = [])
    {
        return parent::post("{$this->baseUrl}/$uri", $data, array_merge($this->headers, $headers));
    }

    /**
     * @param string $uri
     * @param array $headers
     *
     * @return \Laravel\BrowserKitTesting\Concerns\MakesHttpRequests
     */
    public function get($uri, array $headers = [])
    {
        return parent::get("{$this->baseUrl}/$uri", array_merge($this->headers, $headers));
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Laravel\BrowserKitTesting\Concerns\MakesHttpRequests
     */

    public function put($uri, array $data = [], array $headers = [])
    {
        return parent::put("{$this->baseUrl}/$uri", $data, array_merge($this->headers, $headers));
    }

    /**
     * @param string $uri
     * @param array $data
     * @param array $headers
     *
     * @return \Laravel\BrowserKitTesting\Concerns\MakesHttpRequests
     */
    public function delete($uri, array $data = [], array $headers = [])
    {
        return parent::delete("{$this->baseUrl}/$uri", $data, array_merge($this->headers, $headers));
    }

    /**
     * Returns the user default for tests
     *
     * @param int $id_rol
     * @param string $name
     * @param string $password
     * @param string $email
     *
     * @return User
     */
    public function getUser($id_rol = User::ADMINISTRADOR, $name = self::TEST_NAME, $password = self::TEST_PASSWORD, $email = self::TEST_EMAIL)
    {
        if (! $this->user) {
            $this->user = factory(User::class)->create([
                'email' => $email,
                'password' => bcrypt($password),
                'name' =>  $name,
                'nombre' => 'John Doe',
                'estado' => User::ACTIVO,
                'id_rol' => $id_rol,
            ]);
        }
        return $this->user;
    }

    /**
     * Returns the user default for tests
     *
     * @param string $name
     * @param string $password
     * @param string $email
     *
     * @return User
     */
    public function getUserConstructora($name = null, $password = self::TEST_PASSWORD, $email = null)
    {
        if (! $this->user) {
            list($constructora, $user) = $this->createConstructora($name, $password, $email);

            $this->user = $user;
        }

        return $this->user;
    }

    /**
     * Returns the user default for tests
     *
     * @return User
     */
    public function getUserBroker()
    {
        if (! $this->user) {
            /**
             * @var Broker $broker
             */
            list($broker, $constructora) = $this->createBroker();

            $this->user = $broker->user;
        }

        return $this->user;
    }

    /**
     * Returns the user default for tests
     *
     * @return User
     */
    public function getUserEjVentas()
    {
        if (! $this->user) {
            /**
             * @var EjecutivoVentas $ejVentas
             */
            list($ejVentas, $broker) = $this->createEjVentas();

            $this->user = $ejVentas->user;
        }

        return $this->user;
    }

    /**
     * Returns the user default for tests
     *
     * @return User
     */
    public function getUserEjBancos()
    {
        if (! $this->user) {
            /**
             * @var EjecutivoBancos $ejBancos
             */
            list($ejBancos, $broker) = $this->createEjBancos();

            $this->user = $ejBancos->user;
        }

        return $this->user;
    }

    /**
     * Returns the user default for tests
     *
     * @return User
     */
    public function getUserCliente()
    {
        if (! $this->user) {
            /**
             * @var Cliente $cliente
             */
            $cliente = $this->createCliente();

            $this->user = $cliente->user;
        }

        return $this->user;
    }

    /**
     * Returns new User
     *
     * @param int $id_rol
     *
     * @return User
     */
    public function createUser($id_rol)
    {
        $user = factory(User::class)->create([
            'id_rol' => $id_rol,
        ]);

        return $user;
    }

    /**
     * Returns new Constructora and its User
     *
     * @param string $name
     * @param string $password
     * @param string $email
     *
     * @return array
     */
    public function createConstructora($name = null, $password = self::TEST_PASSWORD, $email = null)
    {
        $user = factory(User::class)->create([
            'email' => $email ? $email : $this->faker->unique()->email,
            'password' => bcrypt($password),
            'name' =>  $name ? $name : $this->faker->text(20),
            'nombre' => $this->faker->text(20),
            'estado' => User::ACTIVO,
            'id_rol' => User::CONSTRUCTORA,
        ]);

        $licencia = factory(Licencia::class)->create();

        $constructora = factory(Constructora::class)->create([
            'id_user' => $user->id,
            'id_licencia' => $licencia->id,
        ]);

        return [$constructora,$user];
    }

    /**
     * Returns new Broker and its Constructora
     *
     * @param string $name
     * @param string $password
     * @param string $email
     *
     * @return array
     */
    public function createBroker($name = null, $password = self::TEST_PASSWORD, $email = null)
    {
        /**
         * @var Constructora $constructora
         */
        list($constructora, $user) = $this->createConstructora();

        $user = factory(User::class)->create([
            'email' => $email ? $email : $this->faker->unique()->email,
            'password' => bcrypt($password),
            'name' =>  $name ? $name : $this->faker->text(20),
            'nombre' => $this->faker->text(20),
            'estado' => User::ACTIVO,
            'id_rol' => User::BROKERS,
        ]);

        /**
         * @var Broker $broker
         */
        $broker = factory(Broker::class)->create([
            'id_user' => $user->id,
        ]);

        $constructora->brokers()->sync([$broker->id]);

        return [$broker,$constructora];
    }

    /**
     * Returns new EjecutivoVentas and its Broker
     *
     * @param string $name
     * @param string $password
     * @param string $email
     *
     * @return array
     */
    public function createEjVentas($name = null, $password = self::TEST_PASSWORD, $email = null)
    {

        /**
         * @var Broker $broker
         */
        list($broker, $constructora) = $this->createBroker();

        /**
         * @var User $user
         */
        $user = factory(User::class)->create([
            'email' => $email ? $email : $this->faker->unique()->email,
            'password' => bcrypt($password),
            'name' =>  $name ? $name : $this->faker->text(20),
            'estado' => User::ACTIVO,
            'id_rol' => User::EJ_VENTAS,
        ]);

        $ejVentas = factory(EjecutivoVentas::class)->create([
            'id_user' => $user->id,
            'id_broker' => $broker->id,
        ]);

        return [$ejVentas, $broker];
    }

    /**
     * Returns new EjecutivoBancos and its Broker
     *
     * @param string $name
     * @param string $password
     * @param string $email
     *
     * @return array
     */
    public function createEjBancos($name = null, $password = self::TEST_PASSWORD, $email = null)
    {

        /**
         * @var Broker $broker
         */
        list($broker, $constructora) = $this->createBroker();

        /**
         * @var User $user
         */
        $user = factory(User::class)->create([
            'email' => $email ? $email : $this->faker->unique()->email,
            'password' => bcrypt($password),
            'name' =>  $name ? $name : $this->faker->text(20),
            'estado' => User::ACTIVO,
            'id_rol' => User::EJ_BANCOS,
        ]);

        $ejBancos = factory(EjecutivoBancos::class)->create([
            'id_user' => $user->id,
            'id_broker' => $broker->id,
        ]);

        return [$ejBancos, $broker];
    }

    /**
     * Returns new EjecutivoBancos and its Broker
     *
     * @param string $name
     * @param string $password
     * @param string $email
     *
     * @return Cliente
     */
    public function createCliente($name = null, $password = self::TEST_PASSWORD, $email = null)
    {
        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();

        /**
         * @var User $user
         */
        $user = factory(User::class)->create([
            'email' => $email ? $email : $this->faker->unique()->email,
            'password' => bcrypt($password),
            'name' =>  $name ? $name : $this->faker->text(20),
            'estado' => User::ACTIVO,
            'id_rol' => User::CLIENTE,
        ]);

        $provincia = factory(Provincia::class)->create();
        $distrito = factory(Distrito::class)->create([
            'id_provincia' => $provincia->id,
        ]);
        $pais = factory(Pais::class)->create();

        $cliente = factory(Cliente::class)->create([
            'id_user' => $user->id,
            'id_distrito' => $distrito->id,
            'id_pais' => $pais->id,
            'id_broker' => $broker->id,
        ]);

        return $cliente;
    }

    /**
     * Add default configurations needed by the system
     */
    public function addDefaultConfigurations()
    {
        factory(Configuracion::class)->create([
            'descripcion' => Configuracion::CONFIGURACION_MONEDA,
            'contenido' => '$',
        ]);
        factory(Configuracion::class)->create([
            'descripcion' => Configuracion::CONFIGURACION_MAX_FOTOS_PROYECTO,
            'contenido' => 10,
        ]);
    }
}
