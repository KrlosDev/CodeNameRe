<?php

namespace App\Policies;

use App\Models\Proyecto;
use App\Models\RequerimientoTramite;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RequerimientoTramitePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param RequerimientoTramite $requerimientoTramite
     * @return bool
     */
    public function get(User $user, RequerimientoTramite $requerimientoTramite)
    {
        if ($user->isConstructora()) {
            return $requerimientoTramite->proyecto->id_constructora == $user->constructora->id;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function getAll(User $user)
    {
        return $user->isConstructora();
    }

    /**
     * @param User $user
     * @param int $id_proyecto
     *
     * @return bool
     */
    public function store(User $user, $id_proyecto)
    {
        if ($user->isConstructora()) {
            /**
             * @var Proyecto $proyecto
             */
            $proyecto = Proyecto::find($id_proyecto);

            return $proyecto &&  $proyecto->id_constructora == $user->constructora->id;
        }

        return false;
    }

    /**
     * @param User $user
     * @param RequerimientoTramite $requerimientoTramite
     * @return bool
     */
    public function update(User $user, RequerimientoTramite $requerimientoTramite)
    {
        if ($user->isConstructora()) {
            return $requerimientoTramite->proyecto->id_constructora == $user->constructora->id;
        }

        return false;
    }

    /**
     * @param User $user
     * @param RequerimientoTramite $requerimientoTramite
     * @return bool
     */
    public function delete(User $user, RequerimientoTramite $requerimientoTramite)
    {
        if ($user->isConstructora()) {
            return $requerimientoTramite->proyecto->id_constructora == $user->constructora->id;
        }

        return false;
    }

    /**
     * Method to determine whether the user can copy requerimientos from proyecto_orig to proyecto_dest
     *
     * @param User $user
     * @param Proyecto $proyecto_orig
     * @param Proyecto $proyecto_dest
     *
     * @return bool
     */
    public function copiar(User $user, Proyecto $proyecto_orig, Proyecto $proyecto_dest)
    {
        if ($user->isConstructora()) {
            return $proyecto_orig->id_constructora == $user->constructora->id
                && $proyecto_dest->id_constructora == $user->constructora->id;
        }

        return false;
    }
}
