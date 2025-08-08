<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ConfiguracionPolicy
{
    use HandlesAuthorization;

    
    public function getAll(User $user)
    {
        return $user->isAdministrador();
    }
    
    public function update(User $user)
    {
        return $user->isAdministrador();
    }
    public function get(User $user)
    {
        return $user->isAdministrador();
    }
}
