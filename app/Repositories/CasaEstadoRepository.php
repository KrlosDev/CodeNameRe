<?php

namespace App\Repositories;

use App\Models\CasaEstado;
use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class CasaEstadoRepository
{

    /**
     * Paginate the results and filter based on params received
     *
     * @param User $user
     * @param int $quantity
     * @param int $id_broker
     *
     * @return array
     */
    public function paginate($user, $quantity=15, $id_broker=null)
    {
        if ($user->isConstructora()) {
            $brokers = $user->constructora->brokers;

            $casaEstados = CasaEstado::whereIn('id_broker', $brokers->map(function ($o) {
                return $o->id;
            })->all())
                ->orderBy('id', 'desc');

            if ($id_broker) {
                $casaEstados->where('id_broker', $id_broker);
            }

            return [
                'casasEstados' => $casaEstados->paginate($quantity),
                'brokers' => $brokers,
                'busqueda_cantidad'=> $quantity,
                'busqueda_broker'=> $id_broker,
            ];
        } elseif ($user->isBroker()) {
            $casaEstados = CasaEstado::where('id_broker', $user->broker->id)
                ->orderBy('id', 'desc');

            return [
                'casasEstados' => $casaEstados->paginate($quantity),
                'busqueda_cantidad'=> $quantity,
            ];
        } elseif ($user->isEjBancos() || $user->isEjVentas()) {
            $broker = $user->getBroker();
            $casaEstados = CasaEstado::where('id_broker', $broker->id)
                ->orderBy('id', 'desc');

            return [
                'casasEstados' => $casaEstados->paginate($quantity),
                'busqueda_cantidad'=> $quantity,
            ];
        } elseif ($user->isCliente()) {
            $brokers = array_unique($user->cliente->clienteCasa->map(function ($o) {
                return $o->id_broker;
            })->all());

            $casaEstados = CasaEstado::whereIn('id_broker', $brokers)
                ->orderBy('id', 'desc');

            return [
                'casasEstados' => $casaEstados->paginate($quantity),
                'busqueda_cantidad'=> $quantity,
            ];
        }

        return null;
    }

    /**
     * Get the results filtering based on the received params
     *
     * @param int $id_broker
     *
     * @return \Illuminate\Support\Collection
     */
    public function get($id_broker=null)
    {
        /**
         * @var Builder $query
         */
        $query = CasaEstado::orderBy('id', 'desc');
        if ($id_broker) {
            $query->where('id_broker', $id_broker);
        }

        return $query->get();
    }

    /**
     * Creates the model and returns the result
     *
     * @param int $id_broker
     * @param string $nombre
     * @param string $slug
     *
     * @return mixed
     *
     */
    public function create($id_broker, $nombre, $slug)
    {
        return DB::transaction(function () use ($id_broker,$nombre,$slug) {
            $casaEstado = CasaEstado::create([
                'id_broker' => $id_broker,
                'nombre' => $nombre,
                'slug' => $slug,
            ]);
            return $casaEstado;
        });
    }
}
