<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConstructoraPolicy
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
        return $user->isAdministrador();
    }

    public function getAll(User $user)
    {
        return $user->isAdministrador();
    }

    public function store(User $user)
    {
        return $user->isAdministrador();
    }

    public function count(User $user)
    {
        return $user->isAdministrador();
    }

    public function update(User $user)
    {
        return $user->isAdministrador();
    }

    public function delete(User $user)
    {
        return $user->isAdministrador();
    }
}
