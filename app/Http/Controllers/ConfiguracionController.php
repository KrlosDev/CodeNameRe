<?php

namespace App\Http\Controllers;

use App\User;
use App\Models\Configuracion;
use App\Models\Funciones;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function getAll()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Configuracion::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error", "Operación errónea. Usuario no autorizado."));
        }

        $config = Configuracion::all();

        return view('pages.configuraciones.index')->with('configuraciones', $config);
    }

    /**
     * @param $id
     * @return string
     */
    public function get($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        $config = Configuracion::where("configuraciones.id", $id)->first();
        if (! $config || $user->cannot('get', $config)) {
            return json_encode([]);
        }

        return json_encode($config);
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $config=Configuracion::find($id);

        if (! $config) {
            return redirect()->route('configuraciones')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "La configuración seleccionada no existe."));
        }
    
        if ($user->cannot('update', $config)) {
            return redirect()->route('configuraciones')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea. Usuario no autorizado."));
        }

        $config->contenido=$request->contenido;

        if (! $config->save()) {
            return redirect()->route('configuraciones')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error editando configuración."));
        }

        return redirect()->back();
    }
}
