<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\Funciones;
use App\User;
use Illuminate\Support\Facades\Auth;

use App\Http\Requests\BancoForm;

class BancoController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Banco::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        
        $cantidad = filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT);
        $nombre = filter_input(INPUT_GET, 'nombre', FILTER_SANITIZE_STRING);
        
        $bancos = Banco::orderBy('bancos.nombre', 'asc');
        if ($nombre) {
            $bancos->where('bancos.nombre', 'LIKE', "%$nombre%");
        }
        
        return view('pages.banco.index')->with('bancos', $bancos->paginate($cantidad));
    }
    
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $banco = Banco::find($id);
        if (! $banco) {
            return json_encode([]);
        }
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('get', $banco)) {
            return Funciones::return403();
        }
        return json_encode($banco);
    }
    
    /**
     * Store the specified resource.
     *
     * @param  \App\Http\Requests\BancoForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BancoForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', Banco::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        
        $banco = new Banco();
        $banco->nombre = $request->nombre;
        if (! $banco->save()) {
            return redirect()->back()->with("alert", Funciones::getAlert("danger", "Error al agregar", "Ha ocurrido un error agregando el banco."));
        }
        return redirect()->back()->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }
    
    /**
     * Count the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('count', Banco::class)) {
            return json_encode([]);
        }
        
        return json_encode(['total' => Banco::count()]);
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BancoForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BancoForm $request, $id)
    {
        $banco = Banco::find($id);
        if (! $banco) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar editar", "El banco no existe."));
        }
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('update', $banco)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea. Usuario no autorizado."));
        }
        
        $banco->nombre = $request->nombre;
        if (! $banco->save()) {
            return redirect()->back()->with("alert", Funciones::getAlert("danger", "Error al editar", "Ha ocurrido un error editando el banco."));
        }
        return redirect()->back()->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
    }
    
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $banco = Banco::find($id);
        if (! $banco) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "El banco no existe."));
        }
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('delete', $banco)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }
        
        if (! $banco->delete()) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error eliminando el banco."));
        }
        return redirect()->back()->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
    }
}
