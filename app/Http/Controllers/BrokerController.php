<?php

namespace App\Http\Controllers;

use App\Http\Requests\BrokerAsignarPorcentajeForm;
use App\Http\Requests\BrokerForm;
use App\Models\ClienteCasa;
use App\Models\Proyecto;
use Illuminate\Http\Request;
use App\Models\Broker;
use App\Models\ConstructoraBroker;
use App\Models\Funciones;
use App\User;
use App\Models\Casa;
use App\Models\Mensaje;
use App\Models\PorcentajeBroker;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use DB;

class BrokerController extends Controller
{

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     * @param  int  $id
     */
    public function get($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $broker = Broker::with(['user','constructora'])->where("brokers.id", $id)->first();
        if (! $broker || $user->cannot('get', $broker)) {
            return Funciones::return403();
        }
        return json_encode($broker);
    }

    /**
     * Returns all the messages pending to be saw by the logged user
     *
     * @return \Illuminate\Http\Response|string
     */
    public function porLeer()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('porLeer', Mensaje::class)) {
            return Funciones::return403();
        }

        $mensajes = Mensaje::where('leido_broker', '=', 0)
            ->where('id_broker', '=', $user->broker->id)
            ->count();

        return json_encode($mensajes);
    }
    
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
        if ($user->cannot('getAll', Broker::class)) {
            return redirect()->route('brokers')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación errónea, Usuario no autorizado."));
        }
        
        if ($user->isConstructora()) {
            $proyectos = $user->constructora->proyecto;

            $brokers = $user->constructora->brokers()
                ->paginate(10);

            return view('pages.broker.index')->with('brokers', $brokers)->with('proyectos', $proyectos)->with('usuario', $user);
        }

        if ($user->isAdministrador()) {
            $brokers = Broker::paginate(10);
             
            return view('pages.broker.index')->with('brokers', $brokers)->with('proyectos', [])->with('usuario', $user);
        }

        return redirect()->route('/');
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\BrokerForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BrokerForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', Broker::class)) {
            return redirect()->route('brokers')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $date = Carbon::now();
        $constructora = $user->constructora;
        
        $cantidad = DB::table('constructoras_brokers')
            ->where('id_constructora', $constructora->id)
            ->whereNull('deleted_at')
            ->count();

        if ($cantidad >= $constructora->max_brokers) {
            return redirect()->route('brokers')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Limite de broker alcanzado."));
        }
        
        $usuario=new User();
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;
        $usuario->email=$request->email;
        $usuario->id_rol=User::BROKERS;
        $usuario->password=bcrypt($request->password);
        if (! $usuario->save()) {
            return redirect()->route('brokers')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el usuario."));
        }
        $broker = new Broker();
        $broker->id_user = $usuario->id;
        
        $broker->max_ej_ventas = $request->max_ej_ventas;
        $broker->max_ej_bancos = $request->max_ej_bancos;
        $constructora=$user->constructora()->first();
        if (($broker->save()&&$broker->constructora()->attach($constructora->id, ['created_at' => $date->toDateTimeString()]))) {
            return redirect()->route('brokers')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el broker."));
        }
        
        return redirect()->route('brokers')
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\BrokerForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(BrokerForm $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $broker=Broker::find($id);

        if (! $broker) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "El broker ingresado no existe."));
        }
        if ($user->cannot('update', $broker)) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea. Usuario no autorizado."));
        }

        $broker->max_ej_ventas = $request->max_ej_ventas;
        $broker->max_ej_bancos = $request->max_ej_bancos;
        if (! $broker->save()) {
            return redirect()->route('brokers')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el broker."));
        }

        $usuario=User::find($broker->id_user);
        if ($request->password!='') {
            $usuario->password=bcrypt($request->password);
        }
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;
        $usuario->email=$request->email;
        if (! $usuario->save()) {
            return redirect()->route('brokers')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el broker."));
        }

        return redirect()->route('brokers')
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
         * @var Broker $broker
         */
        $broker=Broker::find($id);
        if (! $broker) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El broker ingresado no existe."));
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('delete', $broker)) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }

        /**
         * @var User $usuario
         */
        $usuario = User::find($broker->id_user);
        $asignados = Casa::where('id_broker', '=', $broker->id)->count();

        if ($asignados > 0) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El Broker seleccionado tiene casas asignadas."));
        }

        $consBrokers = ConstructoraBroker::where('id_broker', $broker->id)->get();

        foreach ($consBrokers as $cb) {
            $cb->delete();
        }

        if ($broker->delete() && $usuario->delete()) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
        }

        return redirect()->route('brokers')
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error efectuando la operación."));
    }

    /**
     * Assigns the earnings to brokers for each project
     *
     * @param BrokerAsignarPorcentajeForm $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarPorcentaje(BrokerAsignarPorcentajeForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Broker $broker
         */
        $broker=Broker::find($request->id_broker);
        $porcentajeBroker = PorcentajeBroker::where('id_broker', $broker->id)->where('id_proyecto', $request->id_proyecto)->first();

        if ($user->cannot('update', $broker)) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar porcentaje", "Operación errónea. Usuario no autorizado."));
        }
        
        if ($porcentajeBroker == null) {
            /**
             * @var Proyecto $proyecto
             */
            $proyecto = Proyecto::find($request->id_proyecto);
            if ($proyecto->constructora->id != $user->constructora->id) {
                return redirect()->route('brokers')
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar porcentaje", "Operación errónea. Los datos ingresados son inválidos."));
            }

            $porcentajeB=new PorcentajeBroker();
            $porcentajeB->id_broker=$broker->id;
            $porcentajeB->id_proyecto=$proyecto->id;
            $porcentajeB->porcentaje=$request->porcentaje;

            if (! $porcentajeB->save()) {
                return redirect()->route('brokers')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar porcentaje", "Operación errónea. Error al asignar porcentaje."));
            }
        
            return redirect()->route('brokers')
                    ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
        } else {
            $porcentajeBroker->porcentaje = $request->porcentaje;
           
            if (! $porcentajeBroker->save()) {
                return redirect()->route('brokers')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error al asignar el procentaje."));
            }
        }
        
        return redirect()->route('brokers')
                    ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
     * @param int $id
     * @param int $id_proyecto
     *
     * @return string
     */
    public function obtenerPorcentaje($id, $id_proyecto)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Broker $broker
         */
        $broker = Broker::with(['user','constructora'])->where("brokers.id", $id)->first();

        if (! $broker || $user->cannot('get', $broker)) {
            return Funciones::return403();
        }
        $porcentajeB = PorcentajeBroker::where('porcentaje_broker.id_broker', $id)
            ->where('porcentaje_broker.id_proyecto', $id_proyecto)
            ->first();
        return json_encode(['porcentaje'=>$porcentajeB]);
    }

    /**
     *
     * @param Request $request
     * @param int $id_broker
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function quitarCasa(Request $request, $id_broker)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $broker = Broker::find($id_broker);
        if ($user->cannot('quitarCasa', $broker)) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar casa", "Operación errónea. Usuario no autorizado."));
        }
        /**
         * @var Casa $casa
         */
        $casa = Casa::find($request->get('casaquitar'));
        if (! $casa) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error quitar casa", "La propiedad ingresada no existe."));
        }
        $clienteCasa = ClienteCasa::where('id_casa', $request->casaquitar)->get();
        
        if (count($clienteCasa) != 0) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error quitar casa", "El esta propiedad tiene un cliente asignado."));
        }
        
        $casa->id_broker = null;
        if (! $casa->save()) {
            return redirect()->route('brokers')
                ->with("alert", Funciones::getAlert("danger", "Error quitar casa", "No se pudo completar la acción ."));
        }
           
        return redirect()->route('brokers')
            ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
    }
    
    /**
     * Returns the estados for the broker specified
     *
     * @param int $id
     *
     * @return string
     */
    public function getCasaEstados($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $broker = Broker::find($id);
        if (! $broker || $user->cannot('get', $broker)) {
            return Funciones::return403();
        }

        return json_encode(
            CasaEstado::where('id_broker', $broker->id)
            ->orderBy('nombre', 'asc')
            ->select(['id','nombre'])
            ->get()
        );
    }
}
