<?php

namespace App\Http\Controllers;

use App\Models\Funciones;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $user=Auth::user();

        if (! $user) {
            return json_encode([]);
        }
        return json_encode($user);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function getAll()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->can('getAll', User::class)) {
            $usuario = User::all();
            if ($usuario==null) {
                return redirect()->route('/')->with("alert", Funciones::getAlert("danger", "Error al intentar buscar", "Ha ocurrido un problema obteniendo los resultados."));
            } else {
                return redirect()->route('/')->with("alert", Funciones::getAlert("danger", "Error al intentar buscar", "Ha ocurrido un problema obteniendo los resultados."));
            }
        }

        return redirect()->route('/')->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "OperaciÃ³n errÃ³nea. Usuario no autorizado."));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        if ($request->get('upassword') != '') {
            $user->password=bcrypt($request->get('upassword'));
        }
        if ($request->get('unombre') != '') {
	    $user->nombre = $request->get('unombre');
        }
        if ($request->get('uname') != '') {
	    $user->name = $request->get('uname');
        }
        if ($request->get('uemail') != '') {
	    $user->email = $request->get('uemail');
        }

        if (! $user->save()) {
            return redirect()->back()->withInput()
                ->with('alert', Funciones::getAlert('danger', 'Error al intentar actualizar', 'Operaci¨®n err¨®nea. Error creando el broker.'));
        }

        return redirect()->route('login')
            ->with('alert', Funciones::getAlert('success', 'Editado exitosamente', 'Operaci¨®n exitosa.'));
    }
}
