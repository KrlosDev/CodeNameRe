<?php

namespace App\Http\Controllers;

use App\Models\Constructora;
use App\Models\Proyecto;
use App\User;
use App\Models\Casa;
use App\Models\ClienteCasa;
use App\Models\FormaPago;
use App\Models\Pago;
use App\Models\Cliente;
use App\Models\Funciones;
use App\Models\Broker;
use App\Models\Banco;
use App\Models\EjecutivoVentas;
use DB;
use DateTime;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ReporteController extends Controller
{
    /**
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function index()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $hoy = new DateTime("now");
        $fecha = $hoy->format("Y-m-d");
        $estados = $user->getCasasEstados();
        if ($user->id_rol==User::ADMINISTRADOR) {
            $proyectos= Proyecto::paginate(10);

            return view('pages.reporte.index')->with(['proyectos' => $proyectos,
                'estados' => $estados->pluck('nombre', 'id')->toArray(),
                'hoy' => $fecha, 'docucliente' => null, 'banco' => null]);
        }
        if ($user->id_rol==User::CONSTRUCTORA) {
            $proyectos= $user->constructora->proyecto()->paginate(10);

            return view('pages.reporte.index')->with(['proyectos' => $proyectos,
                'estados' => $estados->pluck('nombre', 'id')->toArray(),
                'hoy' => $fecha, 'docucliente' => null, 'banco' => null]);
        }
        if ($user->isBroker()) {
            $broker = $user->broker;
            $casas = $broker->casa()->get();
            $proyectos = [];
            foreach ($casas as $casa) {
                $proyecto = $casa->proyecto;
                if (! in_array($proyecto, $proyectos)) {
                    $proyectos[] = $proyecto;
                }
            }

            return view('pages.reporte.index')->with(['proyectos' => $proyectos,
                'estados' => $estados->pluck('nombre', 'id')->toArray(),
                'hoy' => $fecha, 'docucliente' => null, 'banco' => null]);
        }
        if ($user->id_rol==User::CLIENTE) {
            $cliente= $user->cliente;
            $casascliente=ClienteCasa::where('id_cliente', $cliente->id)->get();
            $idcasas = [];
            foreach ($casascliente as $cas) {
                array_push($idcasas, $cas->id_casa);
            }

            $banco = $cliente->banco;
            $docucliente = $cliente->documentoCliente()->paginate(10);
            $casas= Casa::with(['proyecto','ejecutivoVentas','cliente'])->whereIn('id', $idcasas)->paginate(10);
            return view('pages.reporte.index')->with(['casas' => $casas,
                'estados' => $estados->pluck('nombre', 'id')->toArray(),
                'hoy' => $fecha, 'docucliente' => $docucliente, 'banco' => $banco]);
        }
        if ($user->id_rol==User::EJ_VENTAS) {
            return redirect()->to('reporte/estadoCuentaCliente');
        }
        if ($user->id_rol==User::EJ_BANCOS) {
            return redirect()->route('clientes');
        }

        return view('pages.reporte.index');
    }

    /**
     * @param int $id_proyecto
     * @param int $id_broker
     * @return $this
     */
    public function reporteGananciaBroker($id_proyecto, $id_broker)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        if (! ($user->isBroker() || $user->isConstructora())) {
            return redirect()->route('/');
        }

        /**
         * @var Broker $broker
         */
        $broker = null;
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = null;

        if ($user->isBroker()) {
            $broker = $user->broker;

            /**
             * @var Constructora $constructora
             */
            $constructora = $broker->constructora->first();
            $proyecto = Proyecto::where('proyectos.id', $id_proyecto)->where('proyectos.id_constructora', $constructora->id)->first();
        } elseif ($user->isConstructora()) {
            $broker = Broker::find($id_broker);
            $proyecto = Proyecto::find($id_proyecto);
        }

        $cantidad_casasproyecto= count($proyecto->casas);

        $casas_boker = $broker->casa()
            ->where('casas.id_proyecto', '=', $id_proyecto)
            ->get();

        $casas_broker_asignadas = $broker->casa()
            ->where('casas.id_proyecto', '=', $id_proyecto)
            ->has('cliente')
            ->get();
        $estados = $broker->casasEstados;

        $total = Broker::generateEmptyArrayWithEstados($estados);
        Casa::countEstados($casas_boker, $total);
        $total_asignadas = Broker::generateEmptyArrayWithEstados($estados);
        Casa::countEstados($casas_broker_asignadas, $total_asignadas);

        $cantidad_casasasignadasbroker=count($casas_boker);

        $casasocupadas = Casa::join('clientes_casas', 'casas.id', '=', 'clientes_casas.id_casa')
            ->where('casas.id_broker', '=', $broker->id)
            ->where('casas.id_proyecto', '=', $id_proyecto)
            ->whereNull('clientes_casas.deleted_at')
            ->get();

        $cantidad_casasocupadas = count($casasocupadas);
        $casas_libres=$cantidad_casasasignadasbroker- $cantidad_casasocupadas;
        $otras_propiedades= $cantidad_casasproyecto-$cantidad_casasasignadasbroker;
        $casamodelo = $proyecto->casas->first();

        $abonoinicial_recibido=$mts2_recibidos=0;
        $abonoinicial_total=$mts2_total=0;
        $total_reserva=$reserva_recibido=0;

        foreach ($casas_boker as $home) {
            $pagos= Pago::where('id_casa', '=', $home->id)
                ->get();

            $abonoinicial_total=$abonoinicial_total+$home->monto_abono_inicial;
            $mts2_total=$mts2_total+$home->monto_mts2_adicional*$home->mts2_adicionales;
            $total_reserva=$total_reserva+$home->monto_separacion;

            if (count($pagos)==0) {
                continue;
            } else {
                foreach ($pagos as $pago) {
                    if ($pago->id_tipo_transaccion == 1) {
                        $abonoinicial_recibido=$abonoinicial_recibido+$pago->monto;
                    } elseif ($pago->id_tipo_transaccion == 2) {
                        $reserva_recibido=$reserva_recibido+$pago->monto;
                    } elseif ($pago->id_tipo_transaccion == 3) {
                        $mts2_recibidos=$mts2_recibidos+$pago->monto;
                    }
                }
            }
        }

        return view('pages.reporte.reporte-broker')->with(['proyecto' => $proyecto, 'casasproyecto' => $cantidad_casasproyecto,'asignadas' => $cantidad_casasasignadasbroker, 'ocupadas' => $cantidad_casasocupadas, 'libres' => $casas_libres,
            'modelo' => $casamodelo,
            'total' => $total,
            'estados' => $estados,
            'total_asignadas' => $total_asignadas,
            'abonoinicial_recibido' => $abonoinicial_recibido,
            'mts2_recibidos' => $mts2_recibidos,
            'abonoinicial_total' => $abonoinicial_total,
            'mts2_total' => $mts2_total,
            'reserva_recibido' => $reserva_recibido,
            'total_reserva' => $total_reserva,
            'id_broker' => $id_broker,
            'otras_propiedades' =>$otras_propiedades,
        ]);
    }

    /**
     * @param int $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function reporteDetallado($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        $monto_total = $monto_total_pagado = $casa = null;
        $pagos = $brokers = [];
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = null;

        if (in_array($user->id_rol, [User::ADMINISTRADOR,User::CONSTRUCTORA])) {
            if ($user->id_rol==User::ADMINISTRADOR) {
                $proyecto=Proyecto::find($id);
            } else {
                $proyecto=Proyecto::where('proyectos.id', $id)
                    ->where('proyectos.id_constructora', $user->constructora->id)
                    ->first();
            }

            if ($proyecto==null) {
                return redirect()->route('reporte')->with('alert', Funciones::getAlert("danger", "Erro", "Proyecto no existe."));
            }

            $brokers = $proyecto->casas()
                ->whereNull('casas.deleted_at')
                ->whereNotNull('casas.id_broker')
                ->groupBy('casas.id_broker')
                ->select(['casas.id_broker'])
                ->with('broker')
                ->get();

            $casas = $proyecto->casas()->get();
            $casas_ocupadas = 0;

            foreach ($casas as $casa) {
                if (count($casa->cliente()->get()) != 0) {
                    $casas_ocupadas++;
                }
            }

            return view('pages.reporte.reporte')->with(['proyecto' => $proyecto,'id'=>$id,'casas' => $casas, 'casas_ocupadas' => $casas_ocupadas, 'monto_total_pagado' => $monto_total_pagado, 'monto_total' => $monto_total, "casa" => $casa, "pagos" => $pagos, 'brokers' => $brokers]);
        } elseif ($user->id_rol == User::CLIENTE) {
            $id_propiedad = $id;

            $cliente = $user->cliente;

            $casa = Casa::find($id_propiedad);

            if (! $cliente || ! $casa) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Debe ingresar datos válidos."));
            }

            $pagos_por_pagar=Pago::asignarPagosPorPagar(array_fill_keys(array_keys(Pago::$tipos_pago), 0), $casa);
            $pagos_efectuados=array_fill_keys(array_keys(Pago::$tipos_pago), 0);
            $pagos_pendientes=array_fill_keys(array_keys(Pago::$tipos_pago), 0);

            foreach (Pago::$tipos_pago as $key => $value) {
                $pagos_efectuados[$key] = Pago::where('pagos.id_tipo_transaccion', $key)
                ->where('pagos.id_cliente', $cliente->id)
                ->where('pagos.id_casa', $casa->id)
                ->sum('pagos.monto');
            }
            $pagos_pendientes = Pago::asignarPagosPendientes($pagos_pendientes, $pagos_por_pagar, $pagos_efectuados);
            $pagos = Pago::where('id_cliente', $cliente->id)
                ->where('id_casa', $casa->id);

            return view('pages.reporte.reporte')->with(['casa' => $casa,'id'=>$id,'pagos' => $pagos->paginate(10), 'pagos_por_pagar' => $pagos_por_pagar, 'pagos_efectuados' => $pagos_efectuados, 'pagos_pendientes' => $pagos_pendientes, 'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago]);
        }
    }

    /**
     * @param int $id
     * @return $this
     */
    public function reporteCliente($id)
    {
        /**
         * @var Cliente $cliente
         */
        $cliente = Cliente::find($id);
        $casas = $cliente->clienteCasa;
        $dato= [];

        /**
         * @var Casa $casa
         */
        foreach ($casas as $casa) {
            $pagos = Pago::where('id_casa', $casa->id)
                ->get();
            array_push($dato, $pagos, $casa);
        }

        return view('pages.reporte.reporte-cliente')->with(['datos' => $dato]);
    }

    /**
     * Retorna el reporte de pagos
     *
     * @return \Illuminate\Http\Response
     */
    public function reporteFinanzas()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('reporteFinanzas', Pago::class)) {
            return redirect()->route('casas')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar", "Usted no cuenta con los permisos para ver este reporte."));
        }

        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $id_tipo_transaccion = filter_input(INPUT_GET, 'id_tipo_transaccion', FILTER_SANITIZE_NUMBER_INT);
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        $fecha_min = filter_input(INPUT_GET, 'fecha_min', FILTER_SANITIZE_STRING);
        $fecha_max = filter_input(INPUT_GET, 'fecha_max', FILTER_SANITIZE_STRING);
        $agrupar = filter_input(INPUT_GET, 'agrupar', FILTER_SANITIZE_NUMBER_INT);

        /**
         * @var Collection $proyectos
         */
        $proyectos = null;
        $datetime_min = Funciones::createDateTimeObject($fecha_min);
        $datetime_max = Funciones::createDateTimeObject($fecha_max);

        $pagos = Pago::with(['cliente','casa'])->orderBy('id', 'desc');
        if ($id_tipo_transaccion) {
            $pagos->where('id_tipo_transaccion', $id_tipo_transaccion);
        }
        if ($id_proyecto) {
            $pagos->whereHas('casa', function ($query) use ($id_proyecto) {
                $query->where('id_proyecto', '=', $id_proyecto);
            });
        }
        if ($datetime_min && $datetime_max) {
            $pagos->whereDate('pagos.realizado_at', '>=', $datetime_min->format('Y-m-d'))
                ->whereDate('pagos.realizado_at', '<=', $datetime_max->format('Y-m-d'));
        }
        if ($user->isBroker()) {
            $proyectos = $user->broker->getProyectos();
        } else {
            $proyectos = $user->ejecutivoVentas->broker->getProyectos();
        }
        if ($agrupar) {
            $pagos->groupBy('pagos.id_tipo_transaccion')
                ->groupBy('pagos.id_cliente')
                ->select(['pagos.*',DB::raw('SUM(IF(pagos.id_tipo_transaccion = '.Pago::TIPO_PAGO_MONTO_SEPARACION.',pagos.monto,0)) as total_'.Pago::$tipos_pago_asociacion[Pago::TIPO_PAGO_MONTO_SEPARACION]),
                    DB::raw('SUM(IF(pagos.id_tipo_transaccion = '.Pago::TIPO_PAGO_MONTO_INICIAL.',pagos.monto,0)) as total_'.Pago::$tipos_pago_asociacion[Pago::TIPO_PAGO_MONTO_INICIAL]),
                    DB::raw('SUM(IF(pagos.id_tipo_transaccion = '.Pago::TIPO_PAGO_MTS_ADICIONALES.',pagos.monto,0)) as total_'.Pago::$tipos_pago_asociacion[Pago::TIPO_PAGO_MTS_ADICIONALES])]);
        }
        $pagos = $pagos->paginate($cantidad);
        $totales_por_concepto=array_fill_keys(array_keys(Pago::$tipos_pago), 0);
        foreach ($pagos as $pago) {
            if ($agrupar) {
                $totales_por_concepto[$pago->id_tipo_transaccion] += $pago->{'total_'.Pago::$tipos_pago_asociacion[$pago->id_tipo_transaccion]};
            } else {
                $totales_por_concepto[$pago->id_tipo_transaccion] += $pago->monto;
            }
        }

        return view('pages.reporte.reporte-finanzas')->with(['pagos' => $pagos, 'totales_por_concepto' => $totales_por_concepto, 'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago, 'proyectos' => $proyectos, 'busqueda_cantidad' => $cantidad, 'busqueda_tipo_pago' => $id_tipo_transaccion, 'busqueda_fecha_min' => $fecha_min, 'busqueda_fecha_max' => $fecha_max, 'busqueda_proyecto' => $id_proyecto, 'busqueda_agrupar' => $agrupar]);
    }

    /**
     * Retorna el reporte de estado de cuenta del cliente
     *
     * @return \Illuminate\Http\Response
     */
    public function reporteEstadoCuentaCliente()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('estadoCuentaCliente', Pago::class)) {
            return redirect()->route('reporte')
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
        }

        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $identificacion = filter_input(INPUT_GET, 'identificacion', FILTER_SANITIZE_STRING);
        $id_propiedad = filter_input(INPUT_GET, 'id_propiedad', FILTER_SANITIZE_NUMBER_INT);

        $identificacion_decoded = null;
        $identificacion_array = explode('/', $identificacion);
        if (is_array($identificacion_array) && (count($identificacion_array) == 2 || count($identificacion_array) == 1)) {
            $identificacion_decoded = $identificacion_array[0];
        }

        //No ha ingresado datos
        $pagos_por_pagar = $pagos_efectuados = $pagos_pendientes = [];
        if (! $identificacion_decoded || ! $id_propiedad) {
            return view('pages.reporte.reporte-estado-cuenta-cliente')->with(['pagos' => [], 'pagos_por_pagar' => $pagos_por_pagar, 'pagos_efectuados' => $pagos_efectuados, 'pagos_pendientes' => $pagos_pendientes, 'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago, 'busqueda_cantidad' => $cantidad, 'busqueda_identificacion' => $identificacion, 'busqueda_propiedad' => $id_propiedad]);
        }

        $cliente = Cliente::where('clientes.identificacion', $identificacion_decoded)
            ->first();
        $casa = Casa::find($id_propiedad);
        if (! $cliente || ! $casa) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Debe ingresar datos válidos."));
        }
        if ($user->cannot('estadoCuentaCliente', [$cliente,$casa])) {
            return redirect()->route('reporte')
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
        }

        $pagos_por_pagar=Pago::asignarPagosPorPagar(array_fill_keys(array_keys(Pago::$tipos_pago), 0), $casa);
        $pagos_efectuados=array_fill_keys(array_keys(Pago::$tipos_pago), 0);
        $pagos_pendientes=array_fill_keys(array_keys(Pago::$tipos_pago), 0);

        foreach (Pago::$tipos_pago as $key => $value) {
            $pagos_efectuados[$key] = Pago::where('pagos.id_tipo_transaccion', $key)
                ->where('pagos.id_cliente', $cliente->id)
                ->where('pagos.id_casa', $casa->id)
                ->sum('pagos.monto');
        }
        $pagos_pendientes = Pago::asignarPagosPendientes($pagos_pendientes, $pagos_por_pagar, $pagos_efectuados);
        $pagos = Pago::where('id_cliente', $cliente->id)
            ->where('id_casa', $casa->id);

        return view('pages.reporte.reporte-estado-cuenta-cliente')->with(['pagos' => $pagos->paginate($cantidad), 'pagos_por_pagar' => $pagos_por_pagar, 'pagos_efectuados' => $pagos_efectuados, 'pagos_pendientes' => $pagos_pendientes, 'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago, 'busqueda_cantidad' => $cantidad, 'busqueda_identificacion' => $identificacion, 'busqueda_propiedad' => $id_propiedad]);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function reporteInformeDeVentas()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('reporteInformeDeVentas', Casa::class)) {
            return redirect()->route('reporte')
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
        }

        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $id_banco = filter_input(INPUT_GET, 'id_banco', FILTER_SANITIZE_NUMBER_INT);
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);
        $asignadas = filter_input(INPUT_GET, 'asignadas', FILTER_SANITIZE_STRING);
        $asignadas_bancos = filter_input(INPUT_GET, 'asignadas_bancos', FILTER_SANITIZE_STRING);

        $estados = $user->getCasasEstados();
        $constructora = $user->getConstructora();
        $casas = Casa::with(['cliente','cliente.banco'])
            ->whereHas('proyecto', function ($query) use ($constructora) {
                $query->where('proyectos.id_constructora', $constructora->id);
            })
            ->leftJoin('clientes_casas', 'casas.id', '=', 'clientes_casas.id_casa')
            ->leftJoin('clientes', 'clientes_casas.id_cliente', '=', 'clientes.id')
            ->leftJoin('bancos', 'clientes.id_banco', '=', 'bancos.id')
            ->select(['casas.*',DB::raw('bancos.id as bancos_id'),DB::raw('bancos.nombre as bancos_nombre')]);
        if ($id_banco) {
            $casas->whereHas('cliente', function ($query) use ($id_banco) {
                $query->with(['banco'])
                    ->whereHas('banco', function ($query) use ($id_banco) {
                        $query->where('bancos.id', $id_banco);
                    });
            });
        }
        if ($id_proyecto) {
            $casas->where('casas.id_proyecto', $id_proyecto);
        }
        if ($id_casa_estado) {
            $casas->where('casas.id_casa_estado', $id_casa_estado);
        }
        if ($asignadas) {
            $casas->has('cliente');
        }
        if ($asignadas_bancos) {
            $casas->has('cliente.banco')
                ->orderBy('bancos_nombre', 'asc')
                ->orderBy('casas.id_proyecto', 'desc')
                ->orderBy('casas.id', 'asc');
        } else {
            $casas->orderBy('casas.id_proyecto', 'desc')
                ->orderBy('casas.id', 'asc');
        }
        if ($user->isBroker()) {
            $proyectos = $user->broker->getProyectos();
            $casas->where('casas.id_broker', $user->broker->id);
        } else {
            $proyectos = $constructora->proyecto;
        }
        $bancos = Banco::orderBy('bancos.nombre', 'asc')
            ->select(['bancos.id','bancos.nombre'])
            ->get();
        return view('pages.reporte.reporte-informe-ventas')->with([
            'casas' => $casas->paginate($cantidad),
            'bancos' => $bancos, 'proyectos' => $proyectos,
            'estados' => $estados->pluck('nombre', 'id')->toArray(),
            'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago, 'busqueda_cantidad' => $cantidad,
            'busqueda_banco' => $id_banco, 'busqueda_proyecto' => $id_proyecto,
            'busqueda_id_casa_estado' => $id_casa_estado, 'busqueda_asignadas' => $asignadas,
            'busqueda_asignadas_bancos' => $asignadas_bancos
        ]);
    }

    /**
     * @param int $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function reporteEjecutivoDeVentas($id)
    {
        /**
         * @var EjecutivoVentas $ejecutivoDeVentas
         */
        $ejecutivoDeVentas = EjecutivoVentas::find($id);
        if (! $ejecutivoDeVentas) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "El ejecutivo de ventas no existe."));
        }
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('reporteEjecutivoDeVentas', $ejecutivoDeVentas)) {
            return redirect()->route('reporte')
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
        }

        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);

        $broker = $ejecutivoDeVentas->broker;
        $id_broker = $broker->id;
        $casas_estados = $ejecutivoDeVentas->casa()
            ->with(['cliente'])
            ->select(['casas.id_casa_estado',DB::raw('COUNT(casas.id_casa_estado) as total')])
            ->where('casas.id_broker', $id_broker)
            ->groupBy('casas.id_casa_estado');
        if ($id_proyecto) {
            $casas_estados->where('casas.id_proyecto', $id_proyecto)
                ->whereHas('broker', function ($query) use ($id_broker) {
                    $query->where('brokers.id', $id_broker);
                });
        }
        $total_casas = 0;
        if ($id_proyecto) {
            $total_casas = Proyecto::find($id_proyecto)->casas()->count();
        }
        $estados = $user->getCasasEstados()->pluck('nombre', 'id')->toArray();
        $estados[null] = 'Sin asignar';

        return view('pages.reporte.reporte-ejecutivo-venta')->with(['casas_estados' => $casas_estados->get(),
            'total_casas' => $total_casas, 'proyectos' => $broker->getProyectos(),
            'ejecutivoDeVentas' => $ejecutivoDeVentas,
            'estados' => $estados,
            'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago,
            'busqueda_proyecto' => $id_proyecto
        ]);
    }
}
