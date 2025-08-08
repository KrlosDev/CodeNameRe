<?php
/**
 * Created by PhpStorm.
 * User: luis-hp
 * Date: 04/11/17
 * Time: 06:36 PM
 */

namespace Tests\Controllers;

use App\Models\Broker;
use App\Models\Casa;
use App\Models\Constructora;
use App\Models\Imagen;
use App\Models\Proyecto;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class ProyectoControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_constructora_successfully_lists_proyectos()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyectos = factory(Proyecto::class, 3)->create([
            'id_constructora' => $user->constructora->id
        ]);

        $response = $this->actingAs($user)
            ->get('proyectos')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['proyectos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $proyectos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_constructora_successfully_lists_only_proyectos_assigned()
    {
        $user = $this->getUserConstructora();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyectos = factory(Proyecto::class, 3)->create([
            'id_constructora' => $user->constructora->id
        ]);

        factory(Proyecto::class, 2)->create([
            'id_constructora' => $constructora->id
        ]);

        $response = $this->actingAs($user)
            ->get('proyectos')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['proyectos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $proyectos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_constructora_successfully_shows_proyecto()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $proyecto->id,
                'id_constructora' => $proyecto->id_constructora,
                'nombre' => $proyecto->nombre,
                'descripcion' => $proyecto->descripcion,
            ]);
    }

    public function test_it_constructora_fails_showing_proyecto_due_to_permissions()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Constructora $constructora
         */
        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}")
            ->seeStatusCode(403);
    }

    public function test_it_constructora_successfully_stores_proyecto()
    {
        $user = $this->getUserConstructora();

        $data = [
            'id_constructora' => $user->constructora->id,
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'codigo' => 651,
            'cantidad' => 5,
            'estado' => Proyecto::ESTADO_ACTIVO,
            'modelo' => 'abc',
            'mts2_total' => 50,
            'mts2_construccion' => 70,
            'recamaras' => 3,
            'banos' => 2,
            'monto_separacion' => 250,
            'monto_abono_inicial' => 350,
            'monto_mts2_adicional' => 80,
            'valor' => 50000,
        ];

        $this->actingAs($user)
            ->post("proyectos", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('proyectos', array_only($data, ['id_constructora','nombre','descripcion','estado']))
            ->seeInDatabase('casas', array_only($data, ['mts_total','mts_construccion','recamaras','valor']));
    }

    public function test_it_constructora_fails_storing_proyecto_do_to_validations()
    {
        $user = $this->getUserConstructora();

        $data = [
            'id_constructora' => $user->constructora->id,
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'codigo' => 651,
            'estado' => Proyecto::ESTADO_ACTIVO,
            'modelo' => 'abc',
            'mts2_total' => 50,
            'mts2_construccion' => 70,
            'recamaras' => 3,
            'banos' => 2,
            'monto_separacion' => 250,
            'monto_abono_inicial' => 350,
            'monto_mts2_adicional' => 80,
            'valor' => 50000,
        ];

        $this->actingAs($user)
            ->post("proyectos", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('proyectos', array_only($data, ['id_constructora','nombre','descripcion','estado']));
    }

    public function test_it_constructora_successfully_updates_proyecto()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
            'codigo' => 'oinoi',
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        $data = [
            'nombre' => 'My name 2',
            'descripcion' => 'Weird adqwd',
            'estado' => Proyecto::ESTADO_ACTIVO,
        ];

        $this->actingAs($user)
            ->put("proyectos/{$proyecto->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase('proyectos', array_merge($data, ['id'=>$proyecto->id]));
    }

    public function test_it_constructora_fails_updating_due_to_validations()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
            'codigo' => 'oinoi',
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        $data = [
            'nombre' => 'My name 2',
        ];

        $this->actingAs($user)
            ->put("proyectos/{$proyecto->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('proyectos', array_merge($data, ['id'=>$proyecto->id]));
    }

    public function test_it_constructora_fails_updating_due_to_permissions()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
            'codigo' => 'oinoi',
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        $data = [
            'nombre' => 'My name 2',
        ];

        $this->actingAs($user)
            ->put("proyectos/{$proyecto->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('proyectos', array_merge($data, ['id'=>$proyecto->id]));
    }

    public function test_it_constructora_successfully_deletes_proyecto()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->delete("proyectos/{$proyecto->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase('proyectos', [
                'id'=>$proyecto->id,
                'deleted_at'=>null,
            ])
            ->notSeeInDatabase('casas', [
                'id'=>$casa->id,
                'deleted_at'=>null,
            ]);
    }

    public function test_it_constructora_fails_deleting_proyecto_due_to_vadidations()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->delete("proyectos/9877967557")
            ->seeStatusCode(302)
            ->seeInDatabase('proyectos', [
                'id'=>$proyecto->id,
                'deleted_at'=>null,
            ])
            ->seeInDatabase('casas', [
                'id'=>$casa->id,
                'deleted_at'=>null,
            ]);
    }

    public function test_it_constructora_fails_deleting_proyecto_due_to_permissions()
    {
        $user = $this->getUserConstructora();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        /**
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->delete("proyectos/{$proyecto->id}")
            ->seeStatusCode(302)
            ->seeInDatabase('proyectos', [
                'id'=>$proyecto->id,
                'deleted_at'=>null,
            ])
            ->seeInDatabase('casas', [
                'id'=>$casa->id,
                'deleted_at'=>null,
            ]);
    }

    public function test_it_constructora_successfully_assign_casas_to_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        /**
         * @var Broker $broker2
         */
        list($broker2) = $this->createBroker();
        $broker2->constructora()->sync([$user->constructora->id]);

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        /**;
         * @var Collection $casas
         */
        $casas = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => null,
        ]);
        //We assign this to another broker to lock the house and can't be assigned in the request
        $casas[0]->id_broker = $broker->id;
        $casas[0]->save();

        $data = [
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker2->id,
            'casas' => implode(',', $casas->map(function ($o) {
                return $o->lote_apto;
            })->all()),
        ];

        $response = $this->actingAs($user)
            ->post("proyectos/asignar", $data)
            ->seeStatusCode(302);

        /**
         * @var Casa $casa
         */
        foreach ($casas->slice(1, 5) as $casa) {
            $response->seeInDatabase('casas', [
                'id' => $casa->id,
                'id_broker' => $broker2->id,
            ]);
        }

        foreach ($casas->slice(0, 1) as $casa) {
            $response->seeInDatabase('casas', [
                'id' => $casa->id,
                'id_broker' => $broker->id,
            ]);
        }
    }

    public function test_it_constructora_fails_assigning_casas_not_assigned_to_broker()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Broker $broker
         */
        list($broker) = $this->createBroker();
        $broker->constructora()->sync([$user->constructora->id]);

        /**
         * @var Broker $broker2
         */
        list($broker2) = $this->createBroker();
        $broker2->constructora()->sync([$user->constructora->id]);

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        /**
         * @var Collection $casas
         */
        $casas = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker->id,
        ]);

        $data = [
            'id_proyecto' => $proyecto->id,
            'id_broker' => $broker2->id,
            'casas' => implode(',', $casas->map(function ($o) {
                return $o->id;
            })->all()),
        ];

        $response = $this->actingAs($user)
            ->post("proyectos/asignar", $data)
            ->seeStatusCode(302);

        foreach ($casas as $casa) {
            $response->notSeeInDatabase('casas', [
                'id' => $casa->id,
                'id_broker' => $broker2->id,
            ]);
        }
    }

    public function test_it_constructora_successfully_get_propiedades_disponibles_from_proyecto()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id
        ]);

        /**
         * @var Collection $casas
         */
        $casas = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $response = $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/propiedadesDisponibles")
            ->seeStatusCode(200);

        foreach ($casas as $casa) {
            $response->seeJsonContains([
                'id' => $casa->id,
                'id_proyecto' => $casa->id_proyecto,
            ]);
        }
    }

    public function test_it_constructora_fails_getting_propiedades_disponibles_from_proyecto_due_to_permissions()
    {
        $user = $this->getUserConstructora();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);

        /**
         * @var Collection $casas
         */
        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/propiedadesDisponibles")
            ->seeStatusCode(403);
    }

    public function test_it_constructora_successfully_see_panel_imagenes()
    {
        $user = $this->getUserConstructora();
        $this->addDefaultConfigurations();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        $response = $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['imagenes'];

        //Retrieve data from collection
        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$imagen->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_constructora_fails_seeing_panel_imagenes_due_to_permissions()
    {
        $user = $this->getUserConstructora();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        //Check user was redirected because doesn't have permissions
        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(302)
            ->see('Redirecting to');
    }

    /********* BROKER *********/

    public function test_it_broker_successfully_lists_proyectos()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyectos = factory(Proyecto::class, 3)->create([
            'id_constructora' => $user->broker->constructora()->first()->id
        ]);

        $response = $this->actingAs($user)
            ->get('proyectos')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['proyectos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $proyectos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_broker_successfully_only_lists_proyectos_assigned()
    {
        $user = $this->getUserBroker();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyectos = factory(Proyecto::class, 3)->create([
            'id_constructora' => $user->broker->constructora()->first()->id
        ]);

        factory(Proyecto::class, 3)->create([
            'id_constructora' => $constructora->id,
        ]);

        $response = $this->actingAs($user)
            ->get('proyectos')
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        //Get the data to check if is the same data original generated
        $data = $content->getData()['proyectos']->items();

        //Get all ids to compare if was the retrieved ids
        $ids = $proyectos->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_broker_successfully_shows_proyecto()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id
        ]);

        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $proyecto->id,
                'id_constructora' => $proyecto->id_constructora,
                'nombre' => $proyecto->nombre,
                'descripcion' => $proyecto->descripcion,
            ]);
    }

    public function test_it_broker_fails_showing_proyecto_not_assigned()
    {
        $user = $this->getUserBroker();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}")
            ->seeStatusCode(403);
    }

    public function test_it_broker_fails_storing_proyecto_do_to_permissions()
    {
        $user = $this->getUserBroker();

        $data = [
            'id_constructora' => $user->broker->constructora->first()->id,
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'codigo' => 651,
            'estado' => Proyecto::ESTADO_ACTIVO,
            'modelo' => 'abc',
            'mts2_total' => 50,
            'mts2_construccion' => 70,
            'recamaras' => 3,
            'banos' => 2,
            'monto_separacion' => 250,
            'monto_abono_inicial' => 350,
            'monto_mts2_adicional' => 80,
            'valor' => 50000,
        ];

        $this->actingAs($user)
            ->post("proyectos", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('proyectos', array_only($data, ['id_constructora','nombre','descripcion','estado']));
    }

    public function test_it_broker_fails_updating_proyecto_do_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
            'codigo' => 'oinoi',
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        $data = [
            'nombre' => 'My name 2',
        ];

        $this->actingAs($user)
            ->put("proyectos/{$proyecto->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase('proyectos', array_merge($data, ['id'=>$proyecto->id]));
    }

    public function test_it_broker_fails_deleting_proyecto_do_to_permissions()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
            'codigo' => 'oinoi',
            'nombre' => 'My name',
            'descripcion' => 'Weird description',
            'estado' => Proyecto::ESTADO_SUSPENDIDO,
        ]);

        $this->actingAs($user)
            ->delete("proyectos/{$proyecto->id}")
            ->seeStatusCode(302)
            ->seeInDatabase('proyectos', ['id' => $proyecto->id]);
    }

    public function test_it_broker_successfully_get_propiedades_disponibles_from_proyecto()
    {
        $user = $this->getUserBroker();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id
        ]);

        /**
         * @var Collection $casas
         */
        $casas = factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $response = $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/propiedadesDisponibles")
            ->seeStatusCode(200);

        foreach ($casas as $casa) {
            $response->seeJsonContains([
                'id' => $casa->id,
                'id_proyecto' => $casa->id_proyecto,
            ]);
        }
    }

    public function test_it_broker_fails_getting_propiedades_disponibles_from_proyecto_not_assigned()
    {
        $user = $this->getUserBroker();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id
        ]);

        /**
         * @var Collection $casas
         */
        factory(Casa::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/propiedadesDisponibles")
            ->seeStatusCode(403);
    }

    public function test_it_broker_successfully_see_panel_imagenes()
    {
        $user = $this->getUserBroker();
        $this->addDefaultConfigurations();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->broker->constructora->first()->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        $response = $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['imagenes'];

        //Retrieve data from collection
        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$imagen->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_broker_fails_seeing_panel_imagenes_due_to_permissions()
    {
        $user = $this->getUserBroker();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        //Check user was redirected because doesn't have permissions
        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(302)
            ->see('Redirecting to');
    }

    /********* EJECUTIVO VENTAS *********/

    public function test_it_ej_ventas_fails_listing_proyectos_due_to_permissions()
    {
        $user = $this->getUserEjVentas();

        /**
         * @var Proyecto $proyecto
         */
        factory(Proyecto::class, 3)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora()->first()->id
        ]);

        $this->actingAs($user)
            ->get('proyectos')
            ->seeStatusCode(302)
            ->see('Redirecting to');
    }

    public function test_it_ej_ventas_successfully_see_panel_imagenes()
    {
        $user = $this->getUserEjVentas();
        $this->addDefaultConfigurations();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoVentas->broker->constructora->first()->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        $response = $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['imagenes'];

        //Retrieve data from collection
        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$imagen->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_ej_ventas_fails_seeing_panel_imagenes_due_to_permissions()
    {
        $user = $this->getUserEjVentas();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        //Check user was redirected because doesn't have permissions
        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(302)
            ->see('Redirecting to');
    }

    /********* EJECUTIVO BANCOS *********/

    public function test_it_ej_bancos_fails_listing_proyectos_due_to_permissions()
    {
        $user = $this->getUserEjBancos();

        /**
         * @var Proyecto $proyecto
         */
        factory(Proyecto::class, 3)->create([
            'id_constructora' => $user->ejecutivoBancos->broker->constructora()->first()->id
        ]);

        $this->actingAs($user)
            ->get('proyectos')
            ->seeStatusCode(302)
            ->see('Redirecting to');
    }

    public function test_it_ej_bancos_successfully_see_panel_imagenes()
    {
        $user = $this->getUserEjBancos();
        $this->addDefaultConfigurations();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->ejecutivoBancos->broker->constructora->first()->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        $response = $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['imagenes'];

        //Retrieve data from collection
        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$imagen->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }
    }

    public function test_it_ej_bancos_fails_seeing_panel_imagenes_due_to_permissions()
    {
        $user = $this->getUserEjBancos();

        list($constructora) = $this->createConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);

        /**
         * @var Imagen $imagen
         */
        $imagen = factory(Imagen::class)->create();

        $proyecto->imagenes()->sync([$imagen->id]);

        //Check user was redirected because doesn't have permissions
        $this->actingAs($user)
            ->get("proyectos/{$proyecto->id}/imagenes")
            ->seeStatusCode(302)
            ->see('Redirecting to');
    }
}
