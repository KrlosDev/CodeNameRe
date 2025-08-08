<?php

namespace App\Policies;

use App\Models\Casa;
use App\Models\CasaRequerimientoTramite;
use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CasaRequerimientoTramitePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @param CasaRequerimientoTramite $casaRequerimientoTramite
     * @return bool
     */
    public function get(User $user, CasaRequerimientoTramite $casaRequerimientoTramite)
    {
        if ($user->isConstructora()) {
            return $user->constructora->id == $casaRequerimientoTramite->casa->proyecto->id_constructora;
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
     * @param Casa $casa
     *
     * @return bool
     */
    public function store(User $user, Casa $casa)
    {
        if ($user->isConstructora()) {
            return $user->constructora->id == $casa->proyecto->id_constructora;
        }

        return false;
    }

    /**
     * @param User $user
     * @param CasaRequerimientoTramite $casaRequerimientoTramite
     * @return bool
     */
    public function delete(User $user, CasaRequerimientoTramite $casaRequerimientoTramite)
    {
        if ($user->isConstructora()) {
            return $user->constructora->id == $casaRequerimientoTramite->casa->proyecto->id_constructora;
        }

        return false;
    }
}
