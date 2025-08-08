<?php

namespace App\Repositories;

use App\Models\RequerimientoTramite;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class RequerimientoTramiteRepository
{
    public function __construct()
    {
    }

    /**
     * @param int $id_proyecto
     * @param int $quantity
     * @param null $id_user
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function paginateRequerimientos($id_proyecto, $quantity=15, $id_user=null)
    {
        /**
         * @var Builder $query
         */
        $query = RequerimientoTramite::where('id_proyecto', $id_proyecto);
        if ($id_user) {
            $query->where('id_user', $id_user);
        }

        return $query->paginate($quantity);
    }

    /**
     * @param int $id_proyecto
     * @param null $id_user
     * @return \Illuminate\Support\Collection
     */
    public function getRequerimientos($id_proyecto, $id_user=null)
    {
        /**
         * @var Builder $query
         */
        $query = RequerimientoTramite::where('id_proyecto', $id_proyecto);
        if ($id_user) {
            $query->where('id_user', $id_user);
        }

        return $query->get();
    }

    /**
     * @param int $id_proyecto
     * @param string $nombre
     * @return mixed
     */
    public function create($id_proyecto, $nombre)
    {
        return DB::transaction(function () use ($id_proyecto,$nombre) {
            $requerimientoTransaccion = RequerimientoTramite::create([
                'id_proyecto' => $id_proyecto,
                'nombre' => $nombre,
            ]);
            return $requerimientoTransaccion;
        });
    }
}
