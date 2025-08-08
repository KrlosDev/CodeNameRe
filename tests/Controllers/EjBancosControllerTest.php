<?php

namespace Tests\Controllers;

use App\Models\EjecutivoBancos;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class EjBancosControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_broker_successfully_lists_ej_bancos()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();
        $ejBancos->id_broker = $user->broker->id;
        $ejBancos->save();
        $this->createEjBancos();
        $this->createEjBancos();

        $response = $this->actingAs($user)
            ->get("ejecutivo_bancos")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['ejbancos'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$ejBancos->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($ids));
    }

    public function test_it_broker_successfully_shows_ej_bancos()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();
        $ejBancos->id_broker = $user->broker->id;
        $ejBancos->save();

        $this->actingAs($user)
            ->get("ejecutivo_bancos/{$ejBancos->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $ejBancos->id,
                'id_broker' => $ejBancos->id_broker,
            ]);
    }

    public function test_it_broker_fails_showing_ej_bancos_due_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();

        $this->actingAs($user)
            ->get("ejecutivo_bancos/{$ejBancos->id}")
            ->seeStatusCode(403);
    }

    public function test_it_broker_successfully_stores_ej_bancos()
    {
        $user = $this->getUserBroker();

        $data = [
            //User
            'name' => 'Name',
            'nombre' => 'SuperNombre',
            'email' => 'myawesom@email.com',
            'password' => '123456',
            'password_confirmation' => '123456',
            //EjBancos
            'id_broker' => $user->broker->id,
        ];

        $this->actingAs($user)
            ->post("ejecutivo_bancos", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("ejecutivos_bancos", array_only($data, ['id_broker']));
    }

    public function test_it_broker_fails_storing_ej_bancos_due_to_validations()
    {
        $user = $this->getUserBroker();

        $data = [
            //User
            'name' => 'Nameoiweeioewowewfewfomwefeoifewomewfomwefewoimfefoimwfweoimfewoifmfewomifewoifmew',
            'nombre' => 'SuperNombre',
            'email' => 'myawesomemail',
            'password' => '123456',
            'password_confirmation' => '123456789',
            //EjBancos
            'id_broker' => $user->broker->id,
        ];

        $this->actingAs($user)
            ->post("ejecutivo_bancos", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("ejecutivos_bancos", array_only($data, ['id_broker']));
    }

    public function test_it_broker_successfully_updates_ej_bancos()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();
        $ejBancos->id_broker = $user->broker->id;
        $ejBancos->save();

        $data = [
            'name' => 'Name',
            'nombre' => 'SuperNombre',
            'email' => 'myawesom@email.com',
        ];

        $this->actingAs($user)
            ->put("ejecutivo_bancos/{$ejBancos->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("users", $data+['id'=>$ejBancos->user->id]);
    }

    public function test_it_broker_fails_updating_ej_bancos_due_to_validations()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();
        $ejBancos->id_broker = $user->broker->id;
        $ejBancos->save();

        $data = [
            'name' => 'Naqwqwqwqwwqqwqwfqwwqfqwfinqwoifnqwfiuqwbnfiuqwnf oiUWBFOIAWEFBAWOEIUFBNWAEIOUFNEIUqme',
            'nombre' => 'SuperNombre',
            'email' => 'myawesomom',
        ];

        $this->actingAs($user)
            ->put("ejecutivo_bancos/{$ejBancos->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("users", $data+['id'=>$ejBancos->user->id]);
    }

    public function test_it_broker_fails_updating_ej_bancos_due_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();

        $data = [
            'name' => 'Name',
            'nombre' => 'SuperNombre',
            'email' => 'myawesom@email.com',
        ];

        $this->actingAs($user)
            ->put("ejecutivo_bancos/{$ejBancos->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("users", $data+['id'=>$ejBancos->user->id]);
    }

    public function test_it_broker_successfully_deletes_ej_bancos()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();
        $ejBancos->id_broker = $user->broker->id;
        $ejBancos->save();

        $this->actingAs($user)
            ->delete("ejecutivo_bancos/{$ejBancos->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("ejecutivos_bancos", [
                'id' => $ejBancos->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_broker_fails_deleting_ej_bancos_due_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var EjecutivoBancos $ejBancos
         */
        list($ejBancos) = $this->createEjBancos();

        $this->actingAs($user)
            ->delete("ejecutivo_bancos/{$ejBancos->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("ejecutivos_bancos", [
                'id' => $ejBancos->id,
                'deleted_at' => null,
            ]);
    }
}
