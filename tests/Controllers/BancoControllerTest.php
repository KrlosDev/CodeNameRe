<?php

namespace Tests\Controllers;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;
use App\Models\Banco;

class BancoControllerTest extends KitTestCase
{
    use DatabaseTransactions;
    
    public function test_it_administrador_successfully_lists_bancos()
    {
        $user = $this->getUser();

        $bancos = factory(Banco::class, 5)->create();

        $response = $this->actingAs($user)
            ->get("bancos")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['bancos'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $bancos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(5, count($data));
    }
    
    public function test_it_administrador_successfully_shows_banco()
    {
        $user = $this->getUser();

        $banco = factory(Banco::class)->create();

        $response = $this->actingAs($user)
            ->get("bancos/{$banco->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $banco->id,
                'nombre' => $banco->nombre,
            ]);
    }
    
    public function test_it_administrador_successfully_store_banco()
    {
        $user = $this->getUser();

        $data = [
            'nombre' => 'Test this name'
        ];

        $response = $this->actingAs($user)
            ->post("bancos", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("bancos", $data);
    }
    
    public function test_it_administrador_fails_storing_banco_due_to_validations()
    {
        $user = $this->getUser();

        $data = [
            'nombre' => 'Test this name is too long and not have to be stored'
        ];

        $response = $this->actingAs($user)
            ->post("bancos", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("bancos", $data);
    }
    
    public function test_it_administrador_successfully_updates_banco()
    {
        $user = $this->getUser();

        $banco = factory(Banco::class)->create();

        $data = [
            'nombre' => 'MyNameOMG'
        ];

        $response = $this->actingAs($user)
            ->put("bancos/{$banco->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("bancos", [
                'id' => $banco->id,
                'nombre' => $data['nombre'],
            ]);
    }
    
    public function test_it_administrador_fails_updating_banco_due_to_validations()
    {
        $user = $this->getUser();

        $banco = factory(Banco::class)->create();

        $data = [
            'nombre' => 'Test this name is too long and not have to be stored'
        ];

        $response = $this->actingAs($user)
            ->put("bancos/{$banco->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("bancos", [
                'id' => $banco->id,
                'nombre' => $data['nombre'],
            ]);
    }
    
    public function test_it_administrador_successfully_deletes_banco()
    {
        $user = $this->getUser();

        $banco = factory(Banco::class)->create();

        $response = $this->actingAs($user)
            ->delete("bancos/{$banco->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("bancos", [
                'id' => $banco->id,
                'deleted_at' => null,
            ]);
    }
}
