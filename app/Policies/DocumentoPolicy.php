<?php

namespace App\Policies;

use App\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DocumentoPolicy
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
        return ($user->isConstructora() || $user->isBroker() || $user->isBroker() || $user->isEjVentas() || $user->isEjBancos());
    }
    
    public function getAll(User $user)
    {
        return ($user->isConstructora() || $user->isBroker() || $user->isEjVentas() || $user->isEjBancos());
    }
    
    public function store(User $user)
    {
        return ($user->isConstructora() || $user->isBroker() || $user->isEjVentas());
    }
    
    public function count(User $user)
    {
        return ($user->isConstructora() || $user->isBroker() || $user->isEjVentas());
    }
    
    public function update(User $user)
    {
        return ($user->isConstructora() || $user->isBroker() || $user->isEjVentas());
    }
    

    public function delete(User $user)
    {
        return ($user->isConstructora() || $user->isBroker() || $user->isEjVentas());
    }
    
    public function notificacionesgetAll(User $user)
    {
        return ($user->isConstructora() || $user->isBroker() || $user->isEjVentas());
    }
}
