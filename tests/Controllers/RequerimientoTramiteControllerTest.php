<?php

namespace Tests\Controllers;

use App\Models\Constructora;
use App\Models\Proyecto;
use App\Models\RequerimientoTramite;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class RequerimientoTramiteControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_constructora_successfully_lists_requerimientos_tramites()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var Constructora $constructora
         * @var Proyecto $proyecto2
         */
        list($constructora) = $this->createConstructora();
        $proyecto2 = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);

        $requerimientosTramites = factory(RequerimientoTramite::class, 5)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        //We create more requerimientos that shouldn't be retrieved for this user
        factory(RequerimientoTramite::class, 5)->create([
            'id_proyecto' => $proyecto2->id,
        ]);

        $response = $this->actingAs($user)
            ->get("requerimientos_tramites/{$proyecto->id}")
            ->seeStatusCode(200);

        //Get the original content from response
        $content = $response->response->getOriginalContent();

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['requerimientosTramites'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = $requerimientosTramites->map(function ($o) {
            return $o->id;
        });
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals($requerimientosTramites->count(), count($data));
    }

    public function test_it_constructora_successfully_shows_requerimiento_tramite()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("requerimientos_tramites/{$proyecto->id}/{$requerimientoTramite->id}")
            ->seeStatusCode(200)
            ->seeJsonContains([
                'id' => $requerimientoTramite->id,
                'id_proyecto' => $proyecto->id,
            ]);
    }

    public function test_it_constructora_fails_showing_requerimiento_tramite_if_not_assigned()
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
            'id_constructora' => $constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("requerimientos_tramites/{$proyecto->id}/{$requerimientoTramite->id}")
            ->seeStatusCode(403);
    }

    public function test_it_constructora_successfully_stores_requerimiento_tramite()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $data = [
            'id_proyecto' => $proyecto->id,
            'nombre' => 'MyName',
        ];

        $this->actingAs($user)
            ->post("requerimientos_tramites/{$proyecto->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("requerimientos_tramites", $data);
    }

    public function test_it_constructora_fails_storing_requerimiento_tramite_due_to_permissions()
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
            'id_constructora' => $constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $data = [
            'id_proyecto' => $proyecto->id,
            'nombre' => 'MyName',
        ];

        $this->actingAs($user)
            ->post("requerimientos_tramites/{$proyecto->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("requerimientos_tramites", $data);
    }

    public function test_it_constructora_fails_storing_requerimiento_tramite_due_to_validations()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $data = [
            'id_proyecto' => $proyecto->id,
            'nombre' => 'OIaoiaoiwncioqwncwioqnwoinqwoinwqiodwqiodwnqoidwnqoidwqndoiwqndiowqndiowqndoiqndoiqwdnwqoi',
        ];

        $this->actingAs($user)
            ->post("requerimientos_tramites/{$proyecto->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("requerimientos_tramites", $data);
    }

    public function test_it_constructora_successfully_updates_requerimiento_tramite()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $data = [
            'id_proyecto' => 12312323,
            'nombre' => 'My Name',
        ];

        $this->actingAs($user)
            ->put("requerimientos_tramites/{$requerimientoTramite->id}", $data)
            ->seeStatusCode(302)
            ->seeInDatabase("requerimientos_tramites", [
                'id' => $requerimientoTramite->id,
                'id_proyecto' => $requerimientoTramite->id_proyecto,
                'nombre' => $data['nombre'],
            ]);
    }

    public function test_it_constructora_fails_updating_requerimiento_tramite_due_to_validations()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $data = [
            'nombre' => 'Mye oieoiwe 123123123 12312 123123213 123 12',
        ];

        $this->actingAs($user)
            ->put("requerimientos_tramites/{$requerimientoTramite->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("requerimientos_tramites", [
                'id' => $requerimientoTramite->id,
                'nombre' => $data['nombre'],
            ]);
    }

    public function test_it_constructora_fails_updating_requerimiento_tramite_due_to_permissions()
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
            'id_constructora' => $constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $data = [
            'id_proyecto' => 12312323,
            'nombre' => 'My Name',
        ];

        $this->actingAs($user)
            ->put("requerimientos_tramites/{$requerimientoTramite->id}", $data)
            ->seeStatusCode(302)
            ->notSeeInDatabase("requerimientos_tramites", [
                'id' => $requerimientoTramite->id,
                'id_proyecto' => $requerimientoTramite->id_proyecto,
                'nombre' => $data['nombre'],
            ]);
    }

    public function test_it_constructora_successfully_deletes_requerimiento_tramite()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->delete("requerimientos_tramites/{$requerimientoTramite->id}")
            ->seeStatusCode(302)
            ->notSeeInDatabase("requerimientos_tramites", [
                'id' => $requerimientoTramite->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_constructora_fails_deleting_requerimiento_tramite_due_to_permissions()
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
            'id_constructora' => $constructora->id,
        ]);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->delete("requerimientos_tramites/{$requerimientoTramite->id}")
            ->seeStatusCode(302)
            ->seeInDatabase("requerimientos_tramites", [
                'id' => $requerimientoTramite->id,
                'deleted_at' => null,
            ]);
    }

    public function test_it_constructora_successfully_copy_requerimientos_tramites_to_another_project()
    {
        $user = $this->getUserConstructora();

        /**
         * @var Proyecto $proyecto
         */
        $proyecto = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var Proyecto $proyecto2
         */
        $proyecto2 = factory(Proyecto::class)->create([
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var Collection $requerimientosTramites
         */
        $requerimientosTramites = factory(RequerimientoTramite::class, 3)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $data = [
            'id_proyecto' => $proyecto->id,
        ];
        //If we want to ignore the job, uncomment this line will produce that effect
        //$this->expectsJobs(CopiarRequerimientosDeProyecto::class);

        $response = $this->actingAs($user)
            ->post("requerimientos_tramites/{$proyecto2->id}/copiar", $data)
            ->seeStatusCode(302);

        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        foreach ($requerimientosTramites as $requerimientoTramite) {
            $response->seeInDatabase("requerimientos_tramites", [
                'id_proyecto' => $proyecto2->id,
                'nombre' => $requerimientoTramite->nombre,
            ]);
        }
    }

    public function test_it_constructora_fails_copying_requerimientos_tramites_to_another_project_due_to_permissions()
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
            'id_constructora' => $user->constructora->id,
        ]);
        /**
         * @var Proyecto $proyecto2
         */
        $proyecto2 = factory(Proyecto::class)->create([
            'id_constructora' => $constructora->id,
        ]);
        /**
         * @var Collection $requerimientosTramites
         */
        $requerimientosTramites = factory(RequerimientoTramite::class, 3)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $data = [
            'id_proyecto' => $proyecto->id,
        ];
        //If we want to ignore the job, uncomment this line will produce that effect
        //$this->expectsJobs(CopiarRequerimientosDeProyecto::class);

        $response = $this->actingAs($user)
            ->post("requerimientos_tramites/{$proyecto2->id}/copiar", $data)
            ->seeStatusCode(302);
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        foreach ($requerimientosTramites as $requerimientoTramite) {
            $response->notSeeInDatabase("requerimientos_tramites", [
                'id_proyecto' => $proyecto2->id,
                'nombre' => $requerimientoTramite->nombre,
            ]);
        }
    }
}
