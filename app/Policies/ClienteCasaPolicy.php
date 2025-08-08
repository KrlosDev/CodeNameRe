<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClienteCasaPolicy
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

    public function get(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }

    public function update(User $user)
    {
        return $user->isBroker() || $user->isEjVentas() || $user->isEjBancos();
    }

    public function store(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }

    public function delete(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }
}
