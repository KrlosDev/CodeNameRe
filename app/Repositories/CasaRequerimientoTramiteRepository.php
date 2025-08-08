<?php

namespace App\Repositories;

use App\Models\CasaRequerimientoTramite;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CasaRequerimientoTramiteRepository
{
    public function __construct()
    {
    }

    /**
     *
     * @param int $id_casa
     * @param int $quantity
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateCasaRequerimientoTramite($id_casa, $quantity=15)
    {
        /**
         * @var Builder $query
         */
        $query = CasaRequerimientoTramite::orderBy('id', 'desc');
        if ($id_casa) {
            $query->where('id_casa', $id_casa);
        }

        return $query->paginate($quantity);
    }

    /**
     * @param int $id_casa
     * @return \Illuminate\Support\Collection
     */
    public function getCasasRequerimientosTramites($id_casa=null)
    {
        /**
         * @var Builder $query
         */
        $query = CasaRequerimientoTramite::orderBy('id', 'desc');
        if ($id_casa) {
            $query->where('id_casa', $id_casa);
        }

        return $query->get();
    }

    /**
     * @param int $id_casa
     * @param int $id_requerimiento_tramite
     * @return mixed
     */
    public function create($id_casa, $id_requerimiento_tramite)
    {
        return DB::transaction(function () use ($id_casa,$id_requerimiento_tramite) {
            $casaRequerimientoTramiteTransaccion = CasaRequerimientoTramite::create([
                'id_casa' => $id_casa,
                'id_requerimiento_tramite' => $id_requerimiento_tramite,
            ]);
            return $casaRequerimientoTramiteTransaccion;
        });
    }
}
