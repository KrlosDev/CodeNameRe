<?php

namespace App\Policies;

use App\User;
use App\Models\Pago;
use Illuminate\Auth\Access\HandlesAuthorization;

class PagoPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view the pago.
     *
     * @param  User  $user
     * @return mixed
     */
    public function getAll(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }

    /**
     * @param User $user
     * @param Pago $pago
     *
     * @return bool
     */
    public function get(User $user, Pago $pago)
    {
        if ($user->isBroker()) {
            if (! $pago->casa->broker) {
                return false;
            }
            return $pago->casa->broker->id == $user->broker->id;
        }
        if ($user->isEjVentas()) {
            return $pago->casa->ejecutivoVentas->id == $user->ejecutivoVentas->id;
        }

        return false;
    }

    /**
     * Determine whether the user can create pagos.
     *
     * @param  User  $user
     * @return mixed
     */
    public function store(User $user)
    {
        //
        return $user->isBroker() || $user->isEjVentas();
    }

    /**
     * Determine whether the user can update the pago.
     *
     * @param  User  $user
     * @param  Pago  $pago
     * @return mixed
     */
    public function update(User $user, Pago $pago)
    {
        if ($user->isBroker()) {
            if (! $pago->casa->broker) {
                return false;
            }
            return $pago->casa->broker->id == $user->broker->id;
        }
        if ($user->isEjVentas()) {
            return $pago->casa->ejecutivoVentas && $pago->casa->ejecutivoVentas->id == $user->ejecutivoVentas->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the pago.
     *
     * @param  User  $user
     * @param  Pago  $pago
     *
     * @return mixed
     */
    public function delete(User $user, Pago $pago)
    {
        if ($user->isBroker()) {
            return $pago->casa->broker->id == $user->broker->id;
        }
        if ($user->isEjVentas()) {
            return $pago->casa->ejecutivoVentas && $pago->casa->ejecutivoVentas->id == $user->ejecutivoVentas->id;
        }

        return false;
    }
    
    /**
     * Determine whether the user can reporteFinanzas the pago.
     *
     * @param  User  $user
     * @return mixed
     */
    public function reporteFinanzas(User $user)
    {
        return $user->isBroker();
    }
    
    /**
     * Determine whether the user can estadoCuentaCliente the pago.
     *
     * @param  User  $user
     * @return mixed
     */
    public function estadoCuentaCliente(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }

    /**
     * Determine whether the user can reporteDetallado the pago.
     *
     * @param  User  $user
     * @return mixed
     */
    public function reporteDetallado(User $user)
    {
        return $user->isAdministrador() || $user->isConstructora() || $user->isCliente();
    }
}
