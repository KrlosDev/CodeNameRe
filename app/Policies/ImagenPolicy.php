<?php

namespace App\Policies;

use App\User;
use App\Models\Imagen;
use Illuminate\Auth\Access\HandlesAuthorization;

class ImagenPolicy
{
    use HandlesAuthorization;
    
    /**
     *
     * @param \App\User $user
     * @param \App\Imagen $imagen
     * @return boolean
     */
    public function get(User $user, Imagen $imagen)
    {
        $proyecto = $imagen->proyecto()->first();
        if ($user->isConstructora()) {
            $constructora = $user->constructora()->first();
            return $proyecto->id_constructora == $constructora->id;
        } elseif ($user->isCliente()) {
            $id_proyecto = $proyecto->id;
            $cliente = $user->cliente()->first();
            /*$casas = $cliente->clienteCasa()->get();
            foreach($casas as $casa) {
                if($casa->id_proyecto == $proyecto->id)
                    return true;
            }*/
            $casas = $cliente->whereHas('clienteCasa', function ($query) use ($id_proyecto) {
                $query->where('id_proyecto', '=', $id_proyecto);
            })
                ->count();
            return $casas > 0;
        }
        return false;
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
    
    /**
     *
     * @param \App\User $user
     * @param \App\Imagen $imagen
     * @return boolean
     */
    public function update(User $user, Imagen $imagen)
    {
        if ($user->isConstructora()) {
            $proyecto = $imagen->proyecto()->first();
            $constructora = $user->constructora()->first();
            return $proyecto->id_constructora == $constructora->id;
        }
        return false;
    }
    
    /**
     *
     * @param \App\User $user
     * @param \App\Imagen $imagen
     * @return boolean
     */
    public function delete(User $user, Imagen $imagen)
    {
        if ($user->isConstructora()) {
            $proyecto = $imagen->proyecto()->first();
            $constructora = $user->constructora()->first();
            return $proyecto->id_constructora == $constructora->id;
        }
        return false;
    }
}
