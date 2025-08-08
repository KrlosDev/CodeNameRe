<?php

namespace App\Http\Controllers;

use App\Http\Requests\CasaForm;
use App\Models\Casa;
use App\Models\Cliente;
use App\Models\ClienteCasa;
use App\Models\Constructora;
use App\Models\Proyecto;
use App\Models\EjecutivoVentas;
use App\Models\EjecutivoBancos;
use App\Models\Broker;
use App\Models\Funciones;
use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Auth;

class CasaController extends Controller
{
    public function listModal($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $proyecto=Proyecto::find($id);
        if (! $proyecto) {
            return redirect()->route('casas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar", "El proyecto ingresado no existe."));
        }
        $casas = Casa::with(['proyecto','broker','ejecutivoVentas'])
            ->where("casas.id_proyecto", $proyecto->id)
            ->get();
        if ($user->cannot('update', $proyecto)) {
            return json_encode([]);
        }
        return json_encode($casas);
    }

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
        $casa = Casa::with(['proyecto','broker','ejecutivoVentas'])
            ->where("casas.id", $id)
            ->first();
        if (! $casa || $user->cannot('get', $casa)) {
            return Funciones::return403();
        }
        return json_encode($casa);
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
        if ($user->cannot('getAll', Casa::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        
        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        $id_broker = filter_input(INPUT_GET, 'id_broker', FILTER_SANITIZE_NUMBER_INT);
        $recamaras = filter_input(INPUT_GET, 'recamaras', FILTER_SANITIZE_NUMBER_INT);
        $busqueda_numero_casa = filter_input(INPUT_GET, 'numero_casa', FILTER_SANITIZE_STRING);
        $banos = filter_input(INPUT_GET, 'banos', FILTER_SANITIZE_NUMBER_INT);
        $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);

        $estados = $user->getCasasEstados();
        if ($user->isConstructora()) {
            $proyects = $user->constructora->proyecto;

            $proyectos = [];
            foreach ($proyects as $proyecto) {
                if (! $id_proyecto || $proyecto->id == $id_proyecto) {
                    array_push($proyectos, $proyecto->id);
                }
            }

            /**
             * @var Builder $casas
             */
            $casas = Casa::whereIn('id_proyecto', $proyectos)
                /*->orderBy("casas.id_proyecto", 'asc')
                ->orderBy(\DB::raw('-(casas.id_casa_estado)'), 'asc')
                ->orderBy("casas.lote_apto", 'asc');*/
                ->orderBy("casas.id", 'asc');

            if ($recamaras) {
                $casas->where('casas.recamaras', '>=', $recamaras);
            }
            if ($banos) {
                $casas->where('casas.banos', '>=', $banos);
            }
            if ($id_broker) {
                $casas->where('casas.id_broker', $id_broker);
            }
            if ($id_casa_estado) {
                if ($id_casa_estado == 'sin_asignar') {
                    $casas->whereNull('casas.id_casa_estado');
                } else {
                    $casas->where('casas.id_casa_estado', $id_casa_estado);
                }
            }
            if ($busqueda_numero_casa) {
                $casas->where('casas.codigo', $busqueda_numero_casa);
            }
            $casas = $casas->paginate($cantidad);
            
            $brokers = $user->constructora->brokers()
                ->orderBy('brokers.id', 'desc')
                ->get();
            $proyecto = null;

            return view('pages.casa.index', [
                'casas' => $casas,
                'estados' => $estados,
                'brokers' => $brokers,
                'proyecto' => $proyecto,
                'ejVentas' => [],
                'ejBancos' => [],
                'busqueda_cantidad'=> $cantidad,
                'busqueda_proyecto'=> $id_proyecto,
                'busqueda_broker'=> $id_broker,
                'busqueda_recamaras'=> $recamaras,
                'busqueda_banos'=> $banos,
                'proyectos'=> $proyects,
                'busqueda_numero_casa'=> $busqueda_numero_casa,
                'busqueda_id_casa_estado' => $id_casa_estado
            ]);
        } elseif ($user->isBroker()||$user->isEjVentas()) {
            /**
             * @var Broker $broker
             */
            $broker = null;
            if ($user->isBroker()) {
                $broker = $user->broker;
            } elseif ($user->isEjVentas()) {
                $broker = $user->ejecutivoVentas->broker;
            }
            $proyectos = [];
            $casas = $broker->casa()->with('cliente')->get();
            if (! empty($casas)) {
                $ids = array_map(function ($o) {
                    return $o->id_proyecto;
                }, $casas->all());
                $proyectos = Proyecto::whereIn('proyectos.id', $ids)->get();
            }
            
            $ejVentas = EjecutivoVentas::with(['user','broker'])
                ->where('id_broker', '=', $broker->id)
                ->orderBy("ejecutivos_ventas.id", "desc")
                ->get();
            $ejBancos = EjecutivoBancos::with(['user','broker'])
                ->where('id_broker', '=', $broker->id)
                ->orderBy("ejecutivos_bancos.id", "desc")
                ->get();

            /**
             * @var Builder $casas
             */
            $casas = Casa::leftJoin('clientes_casas', 'casas.id', '=', 'clientes_casas.id_casa')
                ->select('casas.*', 'clientes_casas.id_cliente', 'clientes_casas.id_ej_bancos')
                ->where('casas.id_broker', $broker->id)
                /*->orderBy("casas.id_proyecto", 'asc')
                ->orderBy(\DB::raw('-(casas.id_casa_estado)'), 'asc')
                ->orderBy("casas.lote_apto", 'asc');*/
                ->orderBy("casas.id", 'asc');
            if ($recamaras) {
                $casas->where('casas.recamaras', '>=', $recamaras);
            }
            if ($banos) {
                $casas->where('casas.banos', '>=', $banos);
            }
            if ($id_proyecto) {
                $casas->where('casas.id_proyecto', $id_proyecto);
            }
            if ($id_casa_estado) {
                if ($id_casa_estado == 'sin_asignar') {
                    $casas->whereNull('casas.id_casa_estado');
                } else {
                    $casas->where('casas.id_casa_estado', $id_casa_estado);
                }
            }
            if ($busqueda_numero_casa) {
                $casas->where('casas.codigo', $busqueda_numero_casa);
            }
            
            return view('pages.casa.index')->with('casas', $casas->paginate($cantidad))->with('proyecto', [])
                ->with('brokers', [])
                ->with('estados', $estados)
                ->with('ejVentas', $ejVentas)
                ->with('ejBancos', $ejBancos)
                ->with('proyectos', $proyectos)
                ->with('busqueda_cantidad', $cantidad)
                ->with('busqueda_proyecto', $id_proyecto)
                ->with('busqueda_broker', $id_broker)
                ->with('busqueda_recamaras', $recamaras)
                ->with('busqueda_banos', $banos)
                ->with('busqueda_numero_casa', $busqueda_numero_casa)
                ->with('busqueda_id_casa_estado', $id_casa_estado);
        }

        return Funciones::return403();
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function getCasasProyecto($id)
    {
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = Proyecto::find($id);
        if (! $proyecto) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar listar", "Operación errónea. El proyecto no existe."));
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getCasasProyecto', [Casa::class,$proyecto])) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar listar", "Operación errónea. Usuario no autorizado."));
        }

        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $id_broker = filter_input(INPUT_GET, 'id_broker', FILTER_SANITIZE_NUMBER_INT);
        $recamaras = filter_input(INPUT_GET, 'recamaras', FILTER_SANITIZE_NUMBER_INT);
        $busqueda_numero_casa = filter_input(INPUT_GET, 'numero_casa', FILTER_SANITIZE_STRING);
        $banos = filter_input(INPUT_GET, 'banos', FILTER_SANITIZE_NUMBER_INT);
        $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);

        $constructora = $user->constructora;
        $casas = Casa::with(['constructora'])->where('casas.id_proyecto', $proyecto->id)
            ->orderBy("casas.id_proyecto", 'asc')
            ->orderBy("casas.lote_apto", 'asc');
        if ($recamaras) {
            $casas->where('casas.recamaras', '>=', $recamaras);
        }
        if ($banos) {
            $casas->where('casas.banos', '>=', $banos);
        }
        if ($id_broker) {
            $casas->where('casas.id_broker', $id_broker);
        }
        if ($id_casa_estado) {
            $casas->where('casas.id_casa_estado', $id_casa_estado);
        }
        if ($busqueda_numero_casa) {
            $casas->where('casas.codigo', $busqueda_numero_casa);
        }
        $brokers = $constructora->brokers()
            ->orderBy('brokers.id', 'desc')
            ->get();
        $estados = $user->getCasasEstados();

        return view('pages.casa.index')->with('casas', $casas->paginate($cantidad))->with('brokers', $brokers)->with('proyecto', $proyecto)->with('ejVentas', [])
            ->with('ejBancos', [])
            ->with('proyectos', [$proyecto])
            ->with('estados', $estados)
            ->with('busqueda_cantidad', $cantidad)
            ->with('busqueda_proyecto', $id)
            ->with('busqueda_broker', $id_broker)
            ->with('busqueda_recamaras', $recamaras)
            ->with('busqueda_banos', $banos)
            ->with('busqueda_numero_casa', $busqueda_numero_casa)
            ->with('busqueda_id_casa_estado', $id_casa_estado);
    }

    /**
     * @param int $id
     * @return string
     */
    public function getCasasModelo($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $proyecto=Proyecto::find($id);
        if (! $proyecto) {
            return json_encode([]);
        }

        $casa = Casa::where("casas.id_proyecto", $id)
            ->first();
        if (! $casa || $user->cannot('store', Casa::class)) {
            return Funciones::return403();
        }
        return json_encode($casa);
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\CasaForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CasaForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', Casa::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $cantidad = $request->ccantidad;
        if ($cantidad<=0) {
            return redirect()->route('casas')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Cantidad inválida."));
        }

        /**
         * @var Proyecto $proyecto
         */
        $proyecto=Proyecto::find($request->cid_proyecto);

        if (! $proyecto) {
            return redirect()->route('casas')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Proyecto inválido."));
        }

        $ultima = $proyecto->casas()->orderBy('id', 'desc')->first();
        if (! $ultima) {
            $lote = 0;
        } else {
            $lote = $ultima->lote_apto;
        }
        $casas_correctas = 0;

        for ($i = 1; $i <= $request->ccantidad; $i++) {
            $casa = new Casa();
            $casa->id_proyecto = $request->cid_proyecto;
            //$casa->codigo = $proyecto->codigo."$i";
            $casa->codigo = $proyecto->codigo.($lote+$i);
            $casa->modelo = $request->cmodelo;
            $casa->lote_apto = ($lote+$i);
            $casa->mts2_total = $request->cmts2_total;
            $casa->mts2_construccion = $request->cmts2_construccion;
            $casa->mts2_adicionales = $request->cmts2_adicionales;
            $casa->recamaras = $request->crecamaras;
            $casa->banos = $request->cbanos;
            $casa->monto_separacion = $request->cmonto_separacion;
            $casa->monto_abono_inicial = $request->cmonto_abono_inicial;
            $casa->monto_mts2_adicional = $request->cmonto_mts2_adicional;
            $casa->valor = $request->cvalor;
            if ($casa->save()) {
                $casas_correctas++;
            }
        }

        return redirect()->route('proyectos')
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa, ".$casas_correctas." creadas"));
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function count()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('count', Casa::class)) {
            return Funciones::return403();
        }
        return json_encode(Casa::count());
    }

    /**
     * @param CasaForm $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CasaForm $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Casa $casa
         */
        $casa=Casa::find($id);
        if (! $casa) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "La casas ingresado no existe."));
        }
        if ($user->cannot('update', $casa)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }
          
        $casa->modelo = $request->modelo_u;
        $casa->mts2_total = $request->mts2_total_u;
        $casa->mts2_construccion = $request->mts2_construccion_u;
        $casa->mts2_adicionales = $request->mts2_adicionales_u;
        $casa->recamaras = $request->recamaras_u;
        $casa->banos = $request->banos_u;
        $casa->monto_separacion = $request->monto_separacion_u;
        $casa->monto_abono_inicial = $request->monto_abono_inicial_u;
        $casa->monto_mts2_adicional = $request->monto_mts2_adicional_u;
        $casa->valor = $request->valor_u;

        if (! $casa->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando la casa."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
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

        /**
         * @var Casa $casa
         */
        $casa=Casa::find($id);

        if (! $casa) {
            return redirect()->route('casas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "La casas ingresado no existe."));
        }
        
        if ($user->cannot('delete', $casa)) {
            return redirect()->route('casas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }

        if ($casa->casasAsignada($id)) {
            return redirect()->route('casas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "La casas actualmente esta asignada."));
        }

        if ($casa->delete()) {
            return redirect()->route('casas')
                ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
        }
        return redirect()->route('casas')
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error efectuando la operación."));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarEjecutivos(Request $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Casa $casa
         */
        $casa=Casa::find($request->get('id_casa'));
        
        $ej = $request->get('ej');

        if (! $casa) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar Ejecutivos", "La casa ingresada no existe."));
        }
        
        if ($user->cannot('asignarEjecutivos', $casa)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar ejecutivos", "Operación errónea. Usuario no autorizado."));
        }
       
        $casa->id_ej_ventas = $request->id_ej_ventas;
        if (! $casa->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error al asignar ejecutivos."));
        }
    
        if ($ej == '1') {
            $clientesCasas = ClienteCasa::where('id_casa', $casa->id)->first();
        
            $clientesCasas->id_ej_bancos = $request->id_ej_bancos;
            if (! $clientesCasas->save()) {
                return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error al asignar ejecutivos."));
            }
        }
     
        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Asignación", "Ejecutivos asignados exitosamente."));
    }

    /**
     * @param int $id
     *
     * @return string
     */
    public function getEstatus($id)
    {
        /**
         * @var Casa $casa
         */
        $casa = Casa::select("casas.id_casa_estado")->where("casas.id", $id)->first();
        
        if (! $casa) {
            return json_encode([]);
        }

        return json_encode($casa);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function asignarEstatus(Request $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Casa $casa
         */
        $casa=Casa::where("casas.id", $request->casas)->first();

        if ($user->cannot('asignarEstatus', $casa)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }

        if ($request->status === 'sin_asignar') {
            $casa->id_casa_estado = null;
        } else {
            $casa->id_casa_estado = (int)$request->status;
        }
          
        if (! $casa->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Error actualizando el estatus."));
        }
           
        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function eliminarCasas(Request $request)
    {
        if ($request->cliente) {
            /**
             * @var User $user
             */
            $user=Auth::user();
            $id = $request->cliente;
        
            $casas =Casa::whereIn('id', $id)
                ->get();
        
            if (empty($casas)) {
                return redirect()->route('casas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "La casas ingresado no existe."));
            }

            if ($user->cannot('delete', $casas[0])) {
                return redirect()->route('casas')
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
            }
        
            $actualizaciones_correctas = 0;
            /**
             * @var Casa $casa
             */
            foreach ($casas as $casa) {
                if (! empty($casa->ejecutivoVentas)) {
                    continue;
                }
                //Eval to replace
                /*if(count($casa->broker) == 0 && !$casa->broker) {
                    if ($casa->delete()) {
                        $actualizaciones_correctas++;
                    }
                }*/

                $clienteCasa = ClienteCasa::where('id_casa', $casa->id)->get();
              
                if (count($clienteCasa)!=0) {
                    continue;
                }

                if ((count($casa->broker()->get())) != 0) {
                    continue;
                }

                if ($casa->delete()) {
                    $actualizaciones_correctas++;
                }
            }
    
            return redirect()->route('casas')
            ->with("alert", Funciones::getAlert("success", "Eliminar", "Se han eliminado $actualizaciones_correctas de ".count($casas)." exitosamente."));
        } else {
            return redirect()->route('casas')
            ->with("alert", Funciones::getAlert("danger", "Error al eliminar", "Debe Seleccionar almenos una casilla"));
        }
    }

    /**
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse|string
     */
    public function getAllbyBroker($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Casa::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $broker = Broker::find($id);
        $casas = $broker->casa()->get();
        if (empty($casas)) {
            return json_encode([]);
        }
        return json_encode($casas);
    }
    
    /**
     * Retorna las propiedades del cliente provisto
     *
     * @param int $identificacion
     *
     * @return \Illuminate\Http\Response
     */
    public function getPropiedades($identificacion)
    {
        $cliente = Cliente::where('clientes.identificacion', $identificacion)->first();
        if (! $cliente) {
            return json_encode(["data" => []]);
        }
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('buscarCliente', $cliente)) {
            return json_encode(["data" => []]);
        }
        
        return json_encode(["data" => $cliente->clienteCasa()->get()]);
    }
    
    /**
     * Desasigna la propiedad y la libera
     *
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function desasignar($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Casa $casa
         */
        $casa=Casa::find($id);
        if (! $casa) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "La casas ingresado no existe."));
        }
        if ($user->cannot('update', $casa)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }
        
        $casa->id_broker = null;
        if ($casa->save()) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
        }
        return redirect()->back()->withInput()
            ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando la casa."));
    }

    /**
     * Update method for brokers
     *
     * @param CasaForm $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBroker(CasaForm $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Casa $casa
         */
        $casa=Casa::find($id);
        if (! $casa) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "La casas ingresado no existe."));
        }
        if ($user->cannot('updateBroker', $casa)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }

        if ($request->get('monto_separacion_u')) {
            $casa->monto_separacion = $request->monto_separacion_u;
        }
        if ($request->get('monto_abono_inicial_u')) {
            $casa->monto_abono_inicial = $request->monto_abono_inicial_u;
        }

        if (! $casa->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando la casa."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }
}
