<?php

namespace App\Policies;

use App\User;
use App\Models\EjecutivoVentas;
use Illuminate\Auth\Access\HandlesAuthorization;

class EjecutivoVentasPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * @param User $user
     * @param EjecutivoVentas $ejVentas
     *
     * @return bool
     */
    public function get(User $user, EjecutivoVentas $ejVentas)
    {
        return $user->isBroker() && $user->broker->id == $ejVentas->broker->id;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function getAll(User $user)
    {
        return $user->isBroker();
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
     * @param EjecutivoVentas $ejVentas
     *
     * @return bool
     */
    public function update(User $user, EjecutivoVentas $ejVentas)
    {
        return $user->isBroker() && $user->broker->id == $ejVentas->broker->id;
    }

    /**
     * @param User $user
     * @param EjecutivoVentas $ejVentas
     *
     * @return bool
     */
    public function delete(User $user, EjecutivoVentas $ejVentas)
    {
        return $user->isBroker() && $user->broker->id == $ejVentas->broker->id;
    }
    
    /**
     *
     * @param User $user
     * @param EjecutivoVentas $ejecutivoVentas
     * @return boolean
     */
    public function reporteEjecutivoDeVentas(User $user, EjecutivoVentas $ejecutivoVentas)
    {
        if ($user->isBroker()) {
            return $user->broker->id == $ejecutivoVentas->broker->id;
        } elseif ($user->isEjVentas()) {
            return $user->ejecutivoVentas->id == $ejecutivoVentas->id;
        }
        return false;
    }
    
    /**
     *
     * @param User $user
     *
     * @return boolean
     */
    public function reporteEjecutivoDeVentasPropio(User $user)
    {
        return $user->isEjVentas();
    }
}
