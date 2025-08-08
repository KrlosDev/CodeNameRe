<?php

namespace App\Http\Controllers;

use App\Http\Requests\EjecutivoBancosForm;
use Illuminate\Http\Request;
use App\Models\EjecutivoBancos;
use App\Models\Funciones;
use App\User;
use Illuminate\Support\Facades\Auth;
use App\Models\ClienteCasa;
use DB;

class EjBancosController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        $ejbancos = EjecutivoBancos::with(['broker','user'])->where("ejecutivos_bancos.id", $id)->first();
        if (! $ejbancos || $user->cannot('get', $ejbancos)) {
            return Funciones::return403();
        }
        return json_encode($ejbancos);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function getAll()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', EjecutivoBancos::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        
        $broker = $user->broker()->get()->first();
        $ejbancos = EjecutivoBancos::with(['user','broker'])
            ->where('id_broker', '=', $broker->id)
            ->orderBy("ejecutivos_bancos.id", "desc")
            ->paginate();

        return view('pages.ejbanco.index')->with('ejbancos', $ejbancos);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  EjecutivoBancosForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EjecutivoBancosForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', EjecutivoBancos::class)) {
            return redirect()->route('ejecutivo_bancos')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        
        $broker = $user->broker;
        $constructora = $broker->constructora()->first();
        $total_asignados = EjecutivoBancos::getTotalAsignadosConstructora($constructora);
        if ($constructora->max_ejecutivos_bancos <= $total_asignados) {
            return redirect()->route('ejecutivo_bancos')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. El límite de ejecutivos ha sido alcanzado."))->withInput();
        }
        $cantidad = DB::table('ejecutivos_bancos')
            ->whereNull('deleted_at')
            ->where('id_broker', $broker->id)
            ->count();
    
        if ($cantidad >= $broker->max_ej_ventas) {
            return redirect()->route('ejecutivo_bancos')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Limite de Ejecutivo de Bancos alcanzado."))->withInput();
        }
        
        $usuario=new User();
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;
        $usuario->email=$request->email;
        $usuario->id_rol=User::EJ_BANCOS;
        $usuario->password=bcrypt($request->password);
        if (! $usuario->save()) {
            return redirect()->route('ejecutivo_bancos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el usuario."));
        }
        
        $ejbancos = new EjecutivoBancos();
        $ejbancos->id_user = $usuario->id;
        $id_broker = $user->broker()->get()->first()->id;
        $ejbancos->id_broker = $broker->id;
    
    
        if (! $ejbancos->save()) {
            return redirect()->route('ejecutivo_bancos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el ejecutivo ventas."));
        }

        return redirect()->route('ejecutivo_bancos')
                    ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $ejbancos= EjecutivoBancos::find($id);
        
        if (! $ejbancos) {
            return redirect()->route('ejecutivo_bancos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "El ejecutivo ventas ingresado no existe."));
        }
        if ($user->cannot('update', $ejbancos)) {
            return redirect()->route('ejecutivo_bancos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea. Usuario no autorizado."));
        }

        if (! $ejbancos->save()) {
            return redirect()->route('ejecutivo_bancos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el ejecutivo ventas."));
        }
        /**
         * @var User $usuario
         */
        $usuario=User::find($ejbancos->id_user);
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;
        $usuario->email=$request->email;
        if ($request->password!='') {
            $usuario->password=bcrypt($request->password);
        }
        
        if (! $usuario->save()) {
            return redirect()->route('ejecutivo_bancos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el ejecutivo ventas."));
        }
        
        return redirect()->route('ejecutivo_bancos')
            ->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $ejbancos=EjecutivoBancos::find($id);
        $usuario = User::find($ejbancos->id_user);
      
        if (! $ejbancos) {
            return redirect()->route('ejecutivo_bancos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El ejecutivo de venta ingresado no existe."));
        }
        if ($user->cannot('delete', $ejbancos)) {
            return redirect()->route('ejecutivo_bancos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }
        
        $asignados = ClienteCasa::where('id_ej_bancos', '=', $ejbancos->id)
            ->count();
        
        if ($asignados == 0) {
            if ($ejbancos->delete() &&  $usuario->delete()) {
                return redirect()->route('ejecutivo_bancos')
                    ->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
            }
        } else {
            return redirect()->route('ejecutivo_bancos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El ejecutivo de bancos tiene clientes asignados."));
        }
        return redirect()->route('ejecutivo_bancos')
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error efectuando la operación."));
    }
}
