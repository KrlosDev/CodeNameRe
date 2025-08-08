<?php
namespace Tests\Controllers;

use App\Models\Constructora;
use App\Models\Licencia;
use App\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class ConstructoraControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_administrador_successfully_lists_constructoras()
    {
        $userAdministrador = $this->getUser();

        $licencia = factory(Licencia::class)->create();
        $user = factory(User::class)->create();

        /**
         * @var Collection $constructoras
         */
        $constructoras = factory(Constructora::class, 4)->create([
            'id_user' => $user->id,
            'id_licencia' => $licencia->id,
        ]);

        $response = $this->actingAs($userAdministrador)
            ->get("constructoras")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['constructoras'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $constructoras->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($constructoras->count(), count($data));
    }

    public function test_it_administrador_successfully_stores_constructora()
    {
        $user = $this->getUser();

        $data = [
            //User
            'name' => 'Awesomename',
            'nombre' => 'SuperNombre',
            'email' => 'myawesomemail@gmail.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //Licencia
            'fecha_vencimiento' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'fecha_suspension' => Carbon::now()->addDays(22)->format('Y-m-d'),
            //Constructora
            'max_brokers' => 5,
            'max_ejecutivos_ventas' => 10,
            'max_ejecutivos_bancos' => 10,
        ];

        $this->actingAs($user)
            ->post("constructoras", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("constructoras", array_only($data, ['max_brokers','max_ejecutivos_ventas','max_ejecutivos_bancos']));
    }

    public function test_it_administrador_successfully_shows_constructora()
    {
        $userAdministrador = $this->getUser();

        $licencia = factory(Licencia::class)->create();
        $user = factory(User::class)->create();
        /**
         * @var Constructora $constructora
         */
        $constructora = factory(Constructora::class)->create([
            'id_user' => $user->id,
            'id_licencia' => $licencia->id,
        ]);

        $this->actingAs($userAdministrador)
            ->get("constructoras/{$constructora->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id_user' => $user->id,
                'id_licencia' => $licencia->id,
            ]);
    }

    public function test_it_administrador_fails_storing_constructora_due_to_validations()
    {
        $user = $this->getUser();

        $data = [
            //User
            'name' => 'ASDNQW QWINDQOWIND WOIDNQ OIWND OIQWND QOWIDN QWOIND IOWQNDIOWQND IOQNDIOWNDIOQWNDIOQWNDIOQWNDOI',
            'nombre' => 'SuperNombre',
            'email' => 'myawesomemail',
            'password' => '123456',
            'password_confirmation' => '123456789',
            //Licencia
            'fecha_vencimiento' => Carbon::now()->addDays(15)->format('Y-m-d'),
            'fecha_suspension' => Carbon::now()->addDays(22)->format('Y-m-d'),
            //Constructora
            'max_brokers' => 5,
            'max_ejecutivos_ventas' => 10,
            'max_ejecutivos_bancos' => 10,
        ];

        $this->actingAs($user)
            ->post("constructoras", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("constructoras", array_only($data, ['max_brokers','max_ejecutivos_ventas','max_ejecutivos_bancos']));
    }

    public function test_it_administrador_successfully_updates_constructora()
    {
        $userAdministrador = $this->getUser();

        /**
         * @var Licencia $licencia
         */
        $licencia = factory(Licencia::class)->create();
        /**
         * @var User $user
         */
        $user = factory(User::class)->create();
        /**
         * @var Constructora $constructora
         */
        $constructora = factory(Constructora::class)->create([
            'id_user' => $user->id,
            'id_licencia' => $licencia->id,
        ]);

        $data = [
            //User
            'name' => 'AWESOME',
            'nombre' => 'SUPERNAME',
            'email' => $user->email,
            //Licencia
            'fecha_vencimiento' => $licencia->fecha_vencimiento,
            'fecha_suspension' => $licencia->fecha_suspension,
            //Constructora
            'max_brokers' => 7,
            'max_ejecutivos_ventas' => 15,
            'max_ejecutivos_bancos' => 12,
        ];

        $this->actingAs($userAdministrador)
            ->put("constructoras/{$constructora->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("constructoras", [
                'id' => $constructora->id,
                'max_brokers' => $data['max_brokers'],
                'max_ejecutivos_ventas' => $data['max_ejecutivos_ventas'],
                'max_ejecutivos_bancos' => $data['max_ejecutivos_bancos'],
            ]);
    }

    public function test_it_administrador_fails_updating_constructora_due_to_validations()
    {
        $userAdministrador = $this->getUser();

        /**
         * @var Licencia $licencia
         */
        $licencia = factory(Licencia::class)->create();
        /**
         * @var User $user
         */
        $user = factory(User::class)->create();
        /**
         * @var Constructora $constructora
         */
        $constructora = factory(Constructora::class)->create([
            'id_user' => $user->id,
            'id_licencia' => $licencia->id,
        ]);

        $data = [
            //Constructora
            'max_brokers' => 7,
            'max_ejecutivos_ventas' => 15,
            'max_ejecutivos_bancos' => 12,
        ];

        $this->actingAs($userAdministrador)
            ->put("constructoras/{$constructora->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("constructoras", [
                'id' => $constructora->id,
                'max_brokers' => $data['max_brokers'],
                'max_ejecutivos_ventas' => $data['max_ejecutivos_ventas'],
                'max_ejecutivos_bancos' => $data['max_ejecutivos_bancos'],
            ]);
    }

    public function test_it_administrador_successfully_deletes_constructora()
    {
        $userAdministrador = $this->getUser();

        $licencia = factory(Licencia::class)->create();
        $user = factory(User::class)->create();
        /**
         * @var Constructora $constructora
         */
        $constructora = factory(Constructora::class)->create([
            'id_user' => $user->id,
            'id_licencia' => $licencia->id,
        ]);

        $this->actingAs($userAdministrador)
            ->delete("constructoras/{$constructora->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("constructoras", [
                'id' => $constructora->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_administrador_fails_deleting_constructora_due_to_validations()
    {
        $userAdministrador = $this->getUser();

        $licencia = factory(Licencia::class)->create();
        $user = factory(User::class)->create();
        /**
         * @var Constructora $constructora
         */
        $constructora = factory(Constructora::class)->create([
            'id_user' => $user->id,
            'id_licencia' => $licencia->id,
        ]);

        $this->actingAs($userAdministrador)
            ->delete("constructoras/123112312312323")
            ->seeStatusCode(302)
            ->seeInDatabase("constructoras", [
                'id' => $constructora->id,
                'deleted_at' => null,
            ]);
    }
}
