<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
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
        return $user->isConstructora() ;
    }

    public function getAll(User $user)
    {
        return $user->isConstructora();
    }

    public function store(User $user)
    {
        return $user->isConstructora();
    }

    public function count(User $user)
    {
        return $user->isConstructora();
    }

    public function update(User $user)
    {
        return $user->isConstructora();
    }

    public function delete(User $user)
    {
        return $user->isConstructora();
    }
}
