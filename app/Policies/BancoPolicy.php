<?php

namespace App\Policies;

use App\User;
use App\Models\Banco;
use Illuminate\Auth\Access\HandlesAuthorization;

class BancoPolicy
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
     *
     * @param \App\User $user
     * @return boolean
     */
    public function before(User $user)
    {
        if ($user->isAdministrador()) {
            return true;
        }
    }
    
    /**
     *
     * @param \App\User $user
     * @param \App\Banco $banco
     * @return boolean
     */
    public function get(User $user, Banco $banco)
    {
        return false;
    }
    
    /**
     *
     * @param \App\User $user
     * @return boolean
     */
    public function getAll(User $user)
    {
        return false;
    }
    
    /**
     *
     * @param \App\User $user
     * @return boolean
     */
    public function store(User $user)
    {
        return false;
    }
    
    /**
     *
     * @param \App\User $user
     * @return boolean
     */
    public function count(User $user)
    {
        return false;
    }
    
    /**
     *
     * @param \App\User $user
     * @param \App\Banco $banco
     * @return boolean
     */
    public function update(User $user, Banco $banco)
    {
        return false;
    }
    
    /**
     *
     * @param \App\User $user
     * @param \App\Banco $banco
     * @return boolean
     */
    public function delete(User $user, Banco $banco)
    {
        return false;
    }
}
