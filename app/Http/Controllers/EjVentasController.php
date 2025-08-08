<?php

namespace App\Http\Controllers;

use App\Models\Funciones;
use App\Models\EjecutivoVentas;
use App\User;
use App\Models\Casa;
use App\Models\Mensaje;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Http\Request;
use App\Http\Requests\EjecutivoVentasForm;

class EjVentasController extends Controller
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
        $user=Auth::user();
        $ejventas = EjecutivoVentas::with(['broker','user'])
            ->where("ejecutivos_ventas.id", $id)
            ->first();
        if (! $ejventas || $user->cannot('get', $ejventas)) {
            return Funciones::return403();
        }

        return json_encode($ejventas);
    }

    /**
     * @return string
     */
    public function porLeer()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Mensaje::class)) {
            return Funciones::return403();
        }

        $mensajes = Mensaje::where('leido_ventas', '=', 0)
           ->where('id_ej_ventas', '=', $user->ejecutivoVentas->id)
           ->count();

        return json_encode($mensajes);
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
        if ($user->cannot('getAll', EjecutivoVentas::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $ejventas = EjecutivoVentas::with(['user','broker'])
            ->where('id_broker', '=', $user->broker->id)
            ->orderBy("ejecutivos_ventas.id", "desc")
            ->paginate(10);
       
        return view('pages.ejventas.index')->with('ejventas', $ejventas);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  EjecutivoVentasForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(EjecutivoVentasForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', EjecutivoVentas::class)) {
            return redirect()->route('ejecutivo_ventas')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $constructora = $user->broker->constructora()->first();
        $total_asignados = EjecutivoVentas::getTotalAsignadosConstructora($constructora);
        if ($constructora->max_ejecutivos_ventas <= $total_asignados) {
            return redirect()->route('ejecutivo_ventas')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. El límite de ejecutivos ha sido alcanzado."))->withInput();
        }
        $cantidad = DB::table('ejecutivos_ventas')
            ->whereNull('deleted_at')
            ->where('id_broker', $user->broker->id)
            ->count();
    
        if ($cantidad >= $user->broker->max_ej_ventas) {
            return redirect()->route('ejecutivo_ventas')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Limite de Ejecutivo de Ventas alcanzado."))->withInput();
        }
        
        $usuario=new User();
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;
        $usuario->email=$request->email;
        $usuario->id_rol=User::EJ_VENTAS;
        $usuario->password=bcrypt($request->password);

        if (! $usuario->save()) {
            return redirect()->route('ejecutivo_ventas')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el usuario."));
        }
        $ejventas = new EjecutivoVentas();
        $ejventas->id_user = $usuario->id;
        $ejventas->id_broker = $user->broker->id;

        if (! $ejventas->save()) {
            return redirect()->route('ejecutivo_ventas')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el ejecutivo ventas."));
        }
        return redirect()->route('ejecutivo_ventas')
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
        $ejventas= EjecutivoVentas::find($id);
        
        if (! $ejventas) {
            return redirect()->route('ejecutivo_ventas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "El ejecutivo ventas ingresado no existe."));
        }
        if ($user->cannot('update', $ejventas)) {
            return redirect()->route('ejecutivo_ventas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea. Usuario no autorizado."));
        }
        //$ejventas->nombre = $request->nombre;
        
        if (! $ejventas->save()) {
            return redirect()->route('ejecutivo_ventas')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el ejecutivo ventas."));
        }

        /**
         * @var User $usuario
         */
        $usuario=User::find($ejventas->id_user);
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;
        $usuario->email=$request->email;
        if ($request->password!='') {
            $usuario->password=bcrypt($request->password);
        }
        if (! $usuario->save()) {
            return redirect()->route('ejecutivo_ventas')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el ejecutivo ventas."));
        }
    
        
        return redirect()->route('ejecutivo_ventas')
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
        $ejventas=EjecutivoVentas::find($id);
        /**
         * @var User $usuario
         */
        $usuario = User::find($ejventas->id_user);
        if (! $ejventas) {
            return redirect()->route('ejecutivo_ventas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El ejecutivo de venta ingresado no existe."));
        }
        if ($user->cannot('delete', $ejventas)) {
            return redirect()->route('ejecutivo_ventas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }
        
        $asignados = Casa::where('id_ej_ventas', '=', $ejventas->id)
            ->count();

        if ($asignados == 0) {
            if ($ejventas->delete() && $usuario->delete()) {
                return redirect()->route('ejecutivo_ventas')
                    ->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
            }
        }

        return redirect()->route('ejecutivo_ventas')
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El ejecutivo de ventas tiene casas asignadas."));
    }
}
