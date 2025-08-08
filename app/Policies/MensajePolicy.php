<?php

namespace App\Policies;

use App\Models\Mensaje;
use Illuminate\Auth\Access\HandlesAuthorization;
use App\User;

class MensajePolicy
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
     * @param Mensaje $mensaje
     *
     * @return bool
     */
    public function get(User $user, Mensaje $mensaje)
    {
        if ($user->isBroker()) {
            return $user->broker->id == $mensaje->id_broker;
        }
        if ($user->isEjVentas()) {
            return $user->ejecutivoVentas->id == $mensaje->id_ej_ventas;
        }
        if ($user->isCliente()) {
            return $user->cliente->id == $mensaje->id_cliente;
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
        return $user->isBroker() || $user->isEjVentas();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isCliente();
    }

    /**
     * @param User $user
     * @param Mensaje $mensaje
     *
     * @return bool
     */
    public function update(User $user, Mensaje $mensaje)
    {
        if ($user->isBroker()) {
            return $user->broker->id == $mensaje->id_broker;
        }
        if ($user->isEjVentas()) {
            return $user->ejecutivoVentas->id == $mensaje->id_ej_ventas;
        }

        return false;
    }

    /**
     * @param User $user
     * @param Mensaje $mensaje
     *
     * @return bool
     */
    public function delete(User $user, Mensaje $mensaje)
    {
        if ($user->isBroker()) {
            return $user->broker->id == $mensaje->id_broker;
        }
        if ($user->isEjVentas()) {
            return $user->ejecutivoVentas->id == $mensaje->id_ej_ventas;
        }
        if ($user->isCliente()) {
            return $user->cliente->id == $mensaje->id_cliente;
        }

        return false;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function porLeer(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }
}
