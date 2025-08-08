<?php

namespace App\Policies;

use App\User;
use App\Models\Cliente;
use App\Models\Casa;
use Illuminate\Auth\Access\HandlesAuthorization;

class ClientePolicy
{
    use HandlesAuthorization;

    /**
     * @param User $user
     * @return bool
     */
    public function getAll(User $user)
    {
        return $user->isConstructora() || $user->isBroker()  || $user->isEjVentas() || $user->isEjBancos();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function store(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }

    /**
     * @param User $user
     * @return bool
     */
    public function count(User $user)
    {
        return $user->isBroker()|| $user->isEjVentas();
    }

    /**
     * @param User $user
     * @param Cliente $cliente
     *
     * @return bool
     */
    public function update(User $user, Cliente $cliente)
    {
        if ($user->isBroker()) {
            foreach ($cliente->clienteCasa as $casa) {
                if ($casa->id_broker == $user->broker->id) {
                    return true;
                }
            };
        }
        if ($user->isEjVentas()) {
            foreach ($cliente->clienteCasa as $casa) {
                if ($casa->id_ej_ventas == $user->ejecutivoVentas->id) {
                    return true;
                }
            };
        }
        return false;
        //return $user->isBroker()|| $user->isEjVentas();
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function delete(User $user)
    {
        return $user->isBroker();
    }

    /**
     * @param User $user
     * @param Cliente $cliente
     *
     * @return bool
     */
    public function get(User $user, Cliente $cliente)
    {
        if ($user->isConstructora()) {
            foreach ($cliente->clienteCasa as $casa) {
                if (in_array($casa->id_broker, $user->constructora->brokers->map(function ($o) {
                    return $o->id;
                })->all())) {
                    return true;
                }
            };
        }
        if ($user->isBroker()) {
            if (count($cliente->clienteCasa) == 0) {
                return true;
            }
            foreach ($cliente->clienteCasa as $casa) {
                if ($casa->id_broker == $user->broker->id) {
                    return true;
                }
            };
        }
        if ($user->isEjVentas()) {
            foreach ($cliente->clienteCasa as $casa) {
                if ($casa->id_ej_ventas == $user->ejecutivoVentas->id) {
                    return true;
                }
            };
        }
        if ($user->isEjBancos()) {
            foreach ($cliente->clienteCasa as $casa) {
                if ($casa->pivot->id_ej_bancos == $user->ejecutivoBancos->id) {
                    return true;
                }
            };
        }
        return false;
        //return  $user->isConstructora() || $user->isBroker() || $user->isEjVentas() || $user->isEjBancos();
    }
    
    /**
     * Determine whether the user can estadoCuentaCliente the cliente.
     *
     * @param  User     $user
     * @param  Cliente  $cliente
     * @param  Casa     $casa
     * @return mixed
     */
    public function estadoCuentaCliente(User $user, Cliente $cliente, Casa $casa)
    {
        if ($user->isBroker() || $user->isEjVentas()) {
            $casa_id = $casa->id;
            $casas = Cliente::with('clienteCasa')
                ->where('clientes.id', $cliente->id)
                ->whereHas('clienteCasa', function ($query) use ($casa_id) {
                    $query->where('casas.id', '=', $casa_id);
                })
                ->get();
            return count($casas) > 0;
        }
        return false;
    }
    
    /**
     * Determine whether the user can buscar the cliente.
     *
     * @param  User     $user
     * @return mixed
     */
    public function buscar(User $user)
    {
        return $user->isBroker() || $user->isEjVentas();
    }
    
    /**
     * Determine whether the user can buscarCliente the cliente.
     *
     * @param  User     $user
     * @param  Cliente  $cliente
     * @return mixed
     */
    public function buscarCliente(User $user, Cliente $cliente)
    {
        if ($user->isBroker()) {
            return $cliente->id_broker == $user->broker->id;
        }
        if ($user->isEjVentas()) {
            return $cliente->id_broker == $user->ejecutivoVentas->broker->id;
        }

        return false;
    }

    /**
     * @param User $user
     * @return bool
     */
    public function listadoClientes(User $user)
    {
        return $user->isBroker() || $user->isConstructora() || $user->isEjVentas() || $user->isEjBancos();
    }
}
