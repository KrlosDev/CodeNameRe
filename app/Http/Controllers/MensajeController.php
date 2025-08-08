<?php

namespace App\Http\Controllers;

use App\User;
use App\Http\Requests\MensajeForm;
use App\Models\EjecutivoVentas;
use App\Models\Mensaje;
use App\Models\Funciones;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class MensajeController extends Controller
{
    /**
     * @param int $id
     *
     * @return string
     */
    public function get($id)
    {
        /**
         * @var Mensaje $mensaje
         */
        $mensaje = Mensaje::join('clientes', 'mensajes.id_cliente', '=', 'clientes.id')
            ->join('telefonos_clientes', 'mensajes.id_cliente', '=', 'telefonos_clientes.id_cliente')
            ->select('mensajes.*', 'clientes.nombre', 'telefonos_clientes.telefono')
            ->where('mensajes.id', '=', $id)
            ->first();
        if (! $mensaje) {
            return json_encode([]);
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', $mensaje)) {
            return Funciones::return403();
        }

        $this->update($id);

        return json_encode($mensaje);
    }

    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getAll()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Mensaje::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $mensajes = collect();
        if ($user->isBroker()) {
            $mensajes = Mensaje::with(['cliente'])
                ->where('mensajes.id_broker', '=', $user->broker->id)
                ->whereNull('mensajes.broker_deleted_at')
                ->get();
        } elseif ($user->isEjVentas()) {
            $mensajes = Mensaje::with(['cliente'])
                ->where('mensajes.id_ej_ventas', '=', $user->ejecutivoVentas->id)
                ->whereNull('mensajes.ventas_deleted_at')
                ->get();
        } elseif ($user->isCliente()) {
            $mensajes = Mensaje::with(['cliente'])
                ->where('mensajes.id_cliente', '=', $user->cliente->id)
                ->get();
        }

        return view('pages.mensaje.index')->with('mensajes', $mensajes);
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update($id)
    {
        /**
         * @var Mensaje $mensaje
         */
        $mensaje= Mensaje::find($id);
        if (! $mensaje) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar leer", "Operación errónea. El mensaje ingresado no existe."));
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('update', $mensaje)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar leer", "Operación errónea. Usuario no autorizado."));
        }

        if ($user->isBroker()) {
            $mensaje->leido_broker=1;
            $mensaje->save();
        } elseif ($user->isEjVentas()) {
            $mensaje->leido_ventas=1;
            $mensaje->save();
        }

        return redirect()->back()->with('alert', Funciones::getAlert("success", "Actualización realizada con éxito", "Operación realizada con éxito."));
    }
    
    public function store(MensajeForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', Mensaje::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Usuario no autorizado."));
        }
        
        $ejventas = EjecutivoVentas::find($request->para);

        $mensaje = new Mensaje();
        $mensaje->id_cliente = $user->cliente->id;
        $mensaje->titulo = $request->titulo;
        $mensaje->descripcion = $request->descripcion;
        $mensaje->id_ej_ventas = $request->para;
        $mensaje->leido_broker = 0;
        $mensaje->leido_ventas = 0;
        $mensaje->id_broker = $ejventas->id_broker;
        
        if (! $mensaje->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Error enviando el mensaje."));
        }
        
        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Enviado exitosamente", "Operación exitosa."));
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Mensaje $mensaje
         */
        $mensaje= Mensaje::find($id);
        if (! ($user->isBroker() || $user->isEjVentas())) {
            return redirect()->route('mensajes')->with('alert', Funciones::getAlert("danger", "Error en mensajes", "Operación errónea. Usuario no autorizado."));
        }
      
        if ($user->isBroker()) {
            $mensaje->broker_deleted_at = Carbon::now();
        } elseif ($user->isEjVentas()) {
            $mensaje->ventas_deleted_at = Carbon::now();
        }
        $mensaje->deleted_at = Carbon::now();

        if (! $mensaje->save()) {
            return redirect()->route('mensajes')->with('alert', Funciones::getAlert("danger", "Error en mensajes", "Operación errónea."));
        }
        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Borrado exitosamente", "Operación exitosa."));
    }
}
