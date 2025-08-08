<?php

namespace App\Policies;

use App\Models\Proyecto;
use App\User;
use App\Models\Casa;
use Illuminate\Auth\Access\HandlesAuthorization;

class CasaPolicy
{
    use HandlesAuthorization;
    
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * @param User $user
     * @param Casa $casa
     *
     * @return $this|bool
     */
    public function get(User $user, Casa $casa)
    {
        $constructora = $casa->proyecto->constructora;
        if ($user->isConstructora()) {
            return $user->id == $constructora->id_user;
        }
        if ($user->isBroker()) {
            $broker = $user->broker;
            $brokers = $constructora->brokers;
            return in_array($broker->id, array_map(function ($o) {
                return $o->id;
            }, $brokers->all()));
        }
        if ($user->isEjVentas()) {
            return $casa->id_ej_ventas == $user->ejecutivoVentas->id;
        }
        if ($user->isEjBancos()) {
            return $casa->cliente()->where('id_ej_bancos', $user->ejecutivoBancos->id)->first() != null;
        }
        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function getAll(User $user)
    {
        return $user->isConstructora() || $user->isBroker()|| $user->isEjVentas();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isConstructora();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function count(User $user)
    {
        return $user->isConstructora();
    }

    /**
     * @param User $user
     * @param Casa $casa
     *
     * @return bool
     */
    public function update(User $user, Casa $casa)
    {
        if ($user->isConstructora()) {
            return $user->id == $casa->proyecto->constructora->id_user;
        }

        return false;
        //return $user->isConstructora();
    }

    /**
     * @param User $user
     * @param Proyecto $proyecto
     *
     * @return bool
     */
    public function getCasasProyecto(User $user, Proyecto $proyecto)
    {
        if ($user->isConstructora()) {
            return $user->id == $proyecto->constructora->id_user;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Casa $casa
     *
     * @return bool
     */
    public function updateBroker(User $user, Casa $casa)
    {
        if ($user->isBroker()) {
            $constructora = $casa->proyecto->constructora;
            $broker = $user->broker;
            $constructora_broker = $broker->constructora()->first();
            return $constructora->id == $constructora_broker->id;
        }
        return false;
    }

    /**
     * @param User $user
     * @param Casa $casa
     *
     * @return bool
     */
    public function delete(User $user, Casa $casa)
    {
        if ($user->isConstructora()) {
            return $user->id == $casa->proyecto->constructora->id_user;
        }

        return false;
        //return $user->isConstructora();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function asignarEjecutivos(User $user)
    {
        return $user->isBroker()|| $user->isEjVentas();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function asignarEstatus(User $user)
    {
        return $user->isBroker()|| $user->isEjVentas();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function reporteInformeDeVentas(User $user)
    {
        return $user->isBroker() || $user->isConstructora();
    }

    /**
     * @param User $user
     * @param Casa $casa
     *
     * @return bool
     */
    public function informeDeVentas(User $user, Casa $casa)
    {
        $constructora = $casa->constructora;
        if ($user->isBroker()) {
            $broker = $user->broker;
            if (! $broker) {
                return false;
            }
            return in_array($constructora->id, $broker->constructora->map(function ($o) {
                return $o->id;
            })->all());
        }
        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function listadoPropiedades(User $user)
    {
        return $user->isBroker() || $user->isConstructora() || $user->isEjVentas();
    }
}
