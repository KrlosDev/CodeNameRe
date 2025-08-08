<?php

namespace Tests\Controllers;

use App\Models\Casa;
use App\Models\CasaRequerimientoTramite;
use App\Models\Constructora;
use App\Models\Proyecto;
use App\Models\RequerimientoTramite;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Collection;
use Tests\KitTestCase;

class CasaRequerimientoTramiteControllerTest extends KitTestCase
{
    use DatabaseTransactions;

    public function test_it_constructora_successfully_lists_casas_requerimientos_tramites()
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

        $requerimientoTramite = factory(RequerimientoTramite::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);
        $reqs = factory(RequerimientoTramite::class, 3)->create([
            'id_proyecto' => $proyecto2->id,
        ]);
        $casas = factory(Casa::class, 3)->create([
            'id_proyecto' => $proyecto2->id,
        ]);

        $casaRequerimientoTramite = factory(CasaRequerimientoTramite::class)->create([
            'id_casa' => $casa->id,
            'id_requerimiento_tramite' => $requerimientoTramite->id,
        ]);
        //We create more requerimientos that shouldn't be retrieved for this user
        factory(CasaRequerimientoTramite::class)->create([
            'id_casa' => $casas[0]->id,
            'id_requerimiento_tramite' => $reqs[0]->id,
        ]);

        $response = $this->actingAs($user)
            ->get("casas_requerimientos_tramites/{$casa->id}")
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
        $ids = [$requerimientoTramite->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($data));

        /**
         * @var Collection $dataCollection
         */
        //Get the data to check if is the same data original generated
        $dataCollection = $content->getData()['casasRequerimientosTramites'];

        $data = $dataCollection->all();

        //Get all ids to compare if was the retrieved ids
        $ids = [$casaRequerimientoTramite->id];
        foreach ($data as $row) {
            $this->assertContains($row->id, $ids);
        }

        $this->assertEquals(1, count($data));
    }

    public function test_it_constructora_successfully_toggle_adding_casas_requerimientos_tramites()
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
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $this->actingAs($user)
            ->get("casas_requerimientos_tramites/{$casa->id}/{$requerimientoTramite->id}/toggle")
            ->seeStatusCode(302)
            ->seeInDatabase('casas_requerimientos_tramites', [
                'id_casa' => $casa->id,
                'id_requerimiento_tramite' => $requerimientoTramite->id,
            ]);
    }

    public function test_it_constructora_successfully_toggle_removing_casas_requerimientos_tramites()
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
         * @var Casa $casa
         */
        $casa = factory(Casa::class)->create([
            'id_proyecto' => $proyecto->id,
        ]);

        $casaRequerimientoTramite = factory(CasaRequerimientoTramite::class)->create([
            'id_casa' => $casa->id,
            'id_requerimiento_tramite' => $requerimientoTramite->id,
        ]);

        $this->actingAs($user)
            ->get("casas_requerimientos_tramites/{$casa->id}/{$requerimientoTramite->id}/toggle")
            ->seeStatusCode(302)
            ->notSeeInDatabase('casas_requerimientos_tramites', [
                'id' => $casaRequerimientoTramite->id,
            ]);
    }
}
