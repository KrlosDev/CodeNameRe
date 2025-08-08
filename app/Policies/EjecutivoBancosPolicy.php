<?php

namespace App\Policies;

use App\Models\EjecutivoBancos;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class EjecutivoBancosPolicy
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
     * @param EjecutivoBancos $ejBancos
     *
     * @return bool
     */
    public function get(User $user, EjecutivoBancos $ejBancos)
    {
        return $user->isBroker() && $user->broker->id == $ejBancos->broker->id;
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
     * @param EjecutivoBancos $ejBancos
     *
     * @return bool
     */
    public function update(User $user, EjecutivoBancos $ejBancos)
    {
        return $user->isBroker() && $user->broker->id == $ejBancos->broker->id;
    }

    /**
     * @param User $user
     * @param EjecutivoBancos $ejBancos
     *
     * @return bool
     */
    public function delete(User $user, EjecutivoBancos $ejBancos)
    {
        return $user->isBroker() && $user->broker->id == $ejBancos->broker->id;
    }
}
