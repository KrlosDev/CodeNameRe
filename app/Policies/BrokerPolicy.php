<?php

namespace App\Policies;

use App\User;
use App\Models\Broker;
use Illuminate\Auth\Access\HandlesAuthorization;

class BrokerPolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     *
     * @return bool
     */
    public function before(User $user)
    {
        if ($user->isAdministrador()) {
            return true;
        }
    }

    /**
     * Create a new policy instance.
     *
     */
    public function __construct()
    {
    }

    /**
     * @param User $user
     * @param Broker $broker
     *
     * @return bool
     */
    public function get(User $user, Broker $broker)
    {
        //return $user->isConstructora();
        if ($user->isConstructora()) {
            return in_array($user->constructora->id, array_map(function ($o) {
                return $o->id;
            }, $broker->constructora->all()));
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
        return ($user->isConstructora() || $user->isAdministrador());
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isConstructora();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function count(User $user)
    {
        return $user->isConstructora();
    }

    /**
     * @param User $user
     * @param Broker $broker
     *
     * @return bool
     */
    public function update(User $user, Broker $broker)
    {
        //return $user->isConstructora();
        if ($user->isConstructora()) {
            return in_array($user->constructora->id, array_map(function ($o) {
                return $o->id;
            }, $broker->constructora->all()));
        }
        return false;
    }

    /**
     * @param User $user
     * @param Broker $broker
     *
     * @return bool
     */
    public function delete(User $user, Broker $broker)
    {
        if ($user->isConstructora()) {
            return in_array($user->constructora->id, array_map(function ($o) {
                return $o->id;
            }, $broker->constructora->all()));
        }
        return false;
    }


    /**
     * @param User $user
     * @param Broker $broker
     *
     * @return bool
     */
    public function quitarCasa(User $user, Broker $broker)
    {
        return $user->isConstructora();
    }

    /**
     * @param User $user
     * @param Broker $broker
     *
     * @return bool
     */
    public function propioBroker(User $user, Broker $broker)
    {
        if ($user->isConstructora()) {
            $brokers = $user->constructora->brokers;
            return in_array($broker->id, array_map(function ($o) {
                return $o->id;
            }, $brokers->all()));
        }
        return false;
    }
}
