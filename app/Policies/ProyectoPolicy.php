<?php

namespace App\Policies;

use App\Models\Constructora;
use App\User;
use App\Models\Proyecto;
use App\Models\Cliente;
use Illuminate\Auth\Access\HandlesAuthorization;

class ProyectoPolicy
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
     * @param Proyecto $proyecto
     *
     * @return bool
     */
    public function get(User $user, Proyecto $proyecto)
    {
        $constructora = $proyecto->constructora;

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

        return false;
        //return $user->isConstructora();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function getAll(User $user)
    {
        return $user->isConstructora() || $user->isBroker();
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
    
    public function count(User $user)
    {
        return $user->isConstructora();
    }

    /**
     * @param User $user
     * @param Proyecto $proyecto
     *
     * @return bool
     */
    public function update(User $user, Proyecto $proyecto)
    {
        if ($user->isConstructora()) {
            return $user->id == $proyecto->constructora->id_user;
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
    public function delete(User $user, Proyecto $proyecto)
    {
        if ($user->isConstructora()) {
            return $user->id == $proyecto->constructora->id_user;
        }

        return false;

        //return $user->isConstructora();
    }

    /**
     *
     * @param User $user
     * @param Proyecto $proyecto
     *
     * @return boolean
     */
    public function verImagenes(User $user, Proyecto $proyecto)
    {
        /**
         * @var Constructora $constructora
         */
        $constructora = $user->getConstructora();
        if ($constructora === true) {
            return true;
        }

        if ($user->isCliente()) {
            $id_proyecto = $proyecto->id;
            $cantidad_casas = Cliente::with(['clienteCasa'])
                ->where('clientes.id', $user->cliente->id)
                ->whereHas('clienteCasa', function ($query) use ($id_proyecto) {
                    $query->where('id_proyecto', '=', $id_proyecto);
                })
                ->count();
            return $cantidad_casas > 0;
        }

        return $constructora && $constructora->id == $proyecto->id_constructora;
    }

    /**
     *
     * @param User $user
     * @param Proyecto $proyecto
     *
     * @return boolean
     */
    public function subirImagenes(User $user, Proyecto $proyecto)
    {
        if ($user->isConstructora()) {
            return $user->constructora->id == $proyecto->id_constructora;
        }
        return false;
    }
}
