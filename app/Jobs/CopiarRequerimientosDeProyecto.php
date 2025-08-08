<?php

namespace App\Jobs;

use App\Models\Proyecto;
use App\Models\RequerimientoTramite;
use App\Repositories\RequerimientoTramiteRepository;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

/**
 * Class CopiarRequerimientosDeProyecto
 * @package App\Jobs
 *
 * Copy whole requerimientos from one project to another
 */
class CopiarRequerimientosDeProyecto implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var Proyecto $proyecto_orig
     */
    private $proyecto_orig;
    /**
     * @var Proyecto $proyecto_dest
     */
    private $proyecto_dest;

    /**
     * Create a new job instance.
     *
     * @param Proyecto $proyecto_orig
     * @param Proyecto $proyecto_dest
     *
     */
    public function __construct(Proyecto $proyecto_orig, Proyecto $proyecto_dest)
    {
        $this->proyecto_orig = $proyecto_orig;
        $this->proyecto_dest = $proyecto_dest;
    }

    /**
     * Execute the job.
     *
     * @param RequerimientoTramiteRepository $repository
     *
     * @return bool
     */
    public function handle(RequerimientoTramiteRepository $repository)
    {
        $requerimientos = $repository->getRequerimientos($this->proyecto_orig->id);
        $added = 0;
        /**
         * @var RequerimientoTramite $requerimiento
         */
        foreach ($requerimientos as $requerimiento) {
            if ($repository->create($this->proyecto_dest->id, $requerimiento->nombre)) {
                $added++;
            }
        }

        return $added == count($requerimientos);
    }
}
