<?php

namespace App\Policies;

use App\User;
use App\Models\CasaEstado;
use Illuminate\Auth\Access\HandlesAuthorization;

class CasaEstadoPolicy
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
     * @param CasaEstado $casaEstado
     *
     * @return $this|bool
     */
    public function get(User $user, CasaEstado $casaEstado)
    {
        if ($user->isConstructora()) {
            return in_array($casaEstado->broker->id, $user->constructora->brokers->map(function ($o) {
                return $o->id;
            })->all());
        }
        if ($user->isBroker()) {
            return $user->broker->id == $casaEstado->broker->id;
        }
        if ($user->isEjVentas()) {
            return $casaEstado->broker->id == $user->ejecutivoVentas->broker->id;
        }
        if ($user->isEjBancos()) {
            return $casaEstado->broker->id == $user->ejecutivoBancos->broker->id;
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
        return true;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isBroker();
    }

    /**
     * @param User $user
     * @param CasaEstado $casaEstado
     *
     * @return bool
     */
    public function update(User $user, CasaEstado $casaEstado)
    {
        if ($user->isBroker()) {
            return $user->broker->id == $casaEstado->broker->id;
        }

        return false;
    }

    /**
     * @param User $user
     * @param CasaEstado $casaEstado
     *
     * @return bool
     */
    public function delete(User $user, CasaEstado $casaEstado)
    {
        if ($user->isBroker()) {
            return $user->broker->id == $casaEstado->broker->id;
        }

        return false;
    }
}
