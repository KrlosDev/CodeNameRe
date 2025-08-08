<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Casa;
use App\Models\Broker;
use App\Models\Funciones;
use App\Models\Proyecto;
use App\Models\Pago;
use App\Models\FormaPago;
use App\Models\EjecutivoVentas;
use App\User;
use Carbon\Carbon;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use PDF;

class PdfController extends Controller
{
    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function invoice($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        $cliente= Cliente::with('clienteCasa.broker.user', 'codeudor', 'referenciasPersonales')->find($id);
        if ($cliente == null) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error", "El cliente buscado no existe."));
        }

        if ($user->cannot('get', $cliente)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error", "Usuario no autorizado."));
        }

        $data = $cliente;
        $titulo="datos_cliente_".$cliente->nombre.".pdf";
        $date = Carbon::now()->format('d-m-Y');
        $invoice = "2222";
        $view =  \View::make('reports.pdf.invoice', compact('data', 'date', 'invoice'))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->download($titulo);
    }

    /**
     * @return array
     */
    public function getData()
    {
        $data =  [
            'quantity'      => '1' ,
            'description'   => 'some ramdom text',
            'price'   => '500',
            'total'     => '500'
        ];
        return $data;
    }
    
    public function reporteGananciaBroker($id_proyecto, $id_broker)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Broker $broker
         */
        $broker = null;
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = null;
        
        if (! ($user->isBroker() || $user->isConstructora())) {
            return redirect()->route('/');
        }
        if ($user->isBroker()) {
            $broker = $user->broker()->first();
            $constructora = $broker->constructora()->first();
            $proyecto = Proyecto::where('proyectos.id', $id_proyecto)->where('proyectos.id_constructora', $constructora->id)->first();
        } elseif ($user->isConstructora()) {
            $broker = Broker::find($id_broker);
            $proyecto = Proyecto::find($id_proyecto);
        }
        $casas_boker = $broker->casa()->where('casas.id_proyecto', '=', $id_proyecto)->get();

        $estados = $broker->casasEstados;
        $cantidad_casasasignadasbroker=count($casas_boker);
        $cantidad_casasproyecto= $proyecto->casas->count();
        
        $cantidad_casasocupadas = Casa::join('clientes_casas', 'casas.id', '=', 'clientes_casas.id_casa')
            ->where('casas.id_broker', '=', $broker->id)
            ->where('casas.id_proyecto', '=', $id_proyecto)
            ->whereNull('clientes_casas.deleted_at')
            ->count();
        $cantidad_casas_libres=$cantidad_casasasignadasbroker- $cantidad_casasocupadas;
        $modelo = $proyecto->casas->first();

        $totales = Broker::generateEmptyArrayWithEstados($estados);
        $total_tramites = $broker->casa()->where('casas.id_proyecto', '=', $id_proyecto)
            ->groupBy('casas.id_casa_estado')
            ->select(['casas.id_casa_estado',DB::raw('COUNT(casas.id) as total')])
            ->get();
        $total_no_asignadas = $broker->casa()->where('casas.id_proyecto', '=', $id_proyecto)
            ->whereNull('id_casa_estado')
            ->groupBy('casas.id_casa_estado')
            ->count();
        foreach ($total_tramites as $tot) {
            $totales[$tot->id_casa_estado] = $tot->total;
        }
        $totales['null'] = $total_no_asignadas;

        $abonoinicial_recibido=$mts2_recibidos=$abonoinicial_total=$mts2_total=0;
        
        $total_reserva=$reserva_recibido=0;
        foreach ($casas_boker as $home) {
            $abonoinicial_total=$abonoinicial_total+$home->monto_abono_inicial;
            $mts2_total=$mts2_total+$home->monto_mts2_adicional*$home->mts2_adicionales;
            $total_reserva=$total_reserva+$home->monto_separacion;
            $abonoinicial_recibido += Pago::getPagosCasa($home->id, Pago::TIPO_PAGO_MONTO_INICIAL);
            $reserva_recibido += Pago::getPagosCasa($home->id, Pago::TIPO_PAGO_MONTO_SEPARACION);
            $mts2_recibidos += Pago::getPagosCasa($home->id, Pago::TIPO_PAGO_MTS_ADICIONALES);
        }
        
        $date = Carbon::now()->format('d-m-Y');
        $titulo="reporte_ganancias_$date.pdf";
        $view =  \View::make('reports.pdf.reporte-broker', compact(
            'proyecto',
            'cantidad_casasproyecto',
            'cantidad_casasasignadasbroker',
            'cantidad_casasocupadas',
            'cantidad_casas_libres',
            'modelo',
            'abonoinicial_recibido',
            'mts2_recibidos',
            'abonoinicial_total',
            'mts2_total',
            'reserva_recibido',
            'total_reserva',
            'totales',
            'estados'
        ))->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->download($titulo);
    }

    /**
     * @return mixed
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
        $pagos = $pagos->get();
        $totales_por_concepto=array_fill_keys(array_keys(Pago::$tipos_pago), 0);
        foreach ($pagos as $pago) {
            if ($agrupar) {
                $totales_por_concepto[$pago->id_tipo_transaccion] += $pago->{'total_'.Pago::$tipos_pago_asociacion[$pago->id_tipo_transaccion]};
            } else {
                $totales_por_concepto[$pago->id_tipo_transaccion] += $pago->monto;
            }
        }
        
        $date = Carbon::now()->format('d-m-Y');
        $titulo="reporte_finanzas_$date.pdf";
        $view =  \View::make('reports.pdf.reporte-finanzas', ['pagos' => $pagos, 'totales_por_concepto' => $totales_por_concepto, 'formas_pago' => FormaPago::all(),
            'tipos_pago' => Pago::$tipos_pago, 'proyectos' => $proyectos, 'busqueda_agrupar' => $agrupar])->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->download($titulo);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
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
        
        $cantidad = 1000;
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
        
        $date = Carbon::now()->format('d-m-Y');
        $titulo="reporte_estado_cuenta_cliente_$date.pdf";
        $view =  \View::make('reports.pdf.reporte-estado-cuenta-cliente', ['pagos' => $pagos->paginate($cantidad), 'pagos_por_pagar' => $pagos_por_pagar,
            'pagos_efectuados' => $pagos_efectuados, 'pagos_pendientes' => $pagos_pendientes, 'formas_pago' => FormaPago::all(),
            'tipos_pago' => Pago::$tipos_pago, 'busqueda_cantidad' => $cantidad, 'busqueda_identificacion' => $identificacion,
            'busqueda_propiedad' => $id_propiedad])->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->download($titulo);
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporteDetallado($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('reporteDetallado', Pago::class)) {
            return redirect()->route('reporte')->with('alert', Funciones::getAlert("danger", "Error", "No se encuentra autorizado."));
        }
        $date = Carbon::now()->format('d-m-Y');
        $titulo="reporte_detallado_$date.pdf";
        /**
         * @var Casa $casa
         */
        $monto_total = $monto_total_pagado = $casa = null;
        $pagos = $brokers = [];
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
                return redirect()->route('reporte')->with('alert', Funciones::getAlert("danger", "Error", "Proyecto no existe."));
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
            $view =  \View::make('reports.pdf.reporte-detallado', ['proyecto' => $proyecto, 'casas' => $casas, 'casas_ocupadas' => $casas_ocupadas,
                'monto_total_pagado' => $monto_total_pagado, 'monto_total' => $monto_total, "casa" => $casa, "pagos" => $pagos,
                'brokers' => $brokers])->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->download($titulo);
        } elseif ($user->id_rol == User::CLIENTE) {
            $id_propiedad = $id;
            $pagos_por_pagar = $pagos_efectuados = $pagos_pendientes = [];
            
            $cliente = $user->cliente()->first();
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
            
            $view =  \View::make('reports.pdf.reporte-detallado', ['casa' => $casa,'pagos' => $pagos->paginate(10), 'pagos_por_pagar' => $pagos_por_pagar,
                'pagos_efectuados' => $pagos_efectuados, 'pagos_pendientes' => $pagos_pendientes, 'formas_pago' => FormaPago::all(),
                'tipos_pago' => Pago::$tipos_pago])->render();
            $pdf = \App::make('dompdf.wrapper');
            $pdf->loadHTML($view);
            return $pdf->download($titulo);
        }

        return redirect()->route('reporte')->with('alert', Funciones::getAlert("danger", "Error", "No se encuentra autorizado."));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
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
        
        $cantidad = 10000;
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
            ->orderBy('bancos_nombre', 'asc')
            ->orderBy('casas.id_proyecto', 'desc')
            ->orderBy('casas.id', 'asc')
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
            if ($id_casa_estado == 'sin_asignar') {
                $casas->whereNull('casas.id_casa_estado');
            } else {
                $casas->where('casas.id_casa_estado', $id_casa_estado);
            }
        }
        if ($asignadas) {
            $casas->has('cliente');
        }
        if ($asignadas_bancos) {
            $casas->has('cliente.banco');
        }

        $pdf = PDF::loadView('reports.pdf.reporte-informe-ventas', ['casas' => $casas->paginate($cantidad),
            'estados' => $estados->pluck('nombre', 'id')->toArray(),
        ]);
        return $pdf->download('reporte-informe-ventas.pdf');
    }

    /**
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
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

        $cantidad = 1000;
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        $estados = $ejecutivoDeVentas->broker->casasEstados->pluck('nombre', 'id')->toArray();
        $estados[null] = 'Sin asignar';

        $broker = $ejecutivoDeVentas->broker()->first();
        $id_broker = $broker->id;
        $casas = $ejecutivoDeVentas->casa()
            ->with(['cliente'])
            ->orderBy('casas.id_proyecto', 'desc')
            ->orderBy('casas.id', 'asc');
        if ($id_proyecto) {
            $casas->where('casas.id_proyecto', $id_proyecto)
                ->whereHas('broker', function ($query) use ($id_broker) {
                    $query->where('brokers.id', $id_broker);
                });
        }
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

        $date = Carbon::now()->format('d-m-Y');
        $titulo="reporte_ejecutivo_de_ventas_$date.pdf";
        $view =  \View::make('reports.pdf.reporte-ejecutivo-venta', ['casas' => $casas->paginate($cantidad), 'casas_estados' => $casas_estados->get(),
            'total_casas' => $total_casas, 'proyectos' => $broker->getProyectos(), 'ejecutivoDeVentas' => $ejecutivoDeVentas,
            'estados' => $estados,
            'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago, 'busqueda_cantidad' => $cantidad, 'busqueda_proyecto' => $id_proyecto])->render();
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadHTML($view);
        return $pdf->download($titulo);
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reportelistaDeClientes()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('listadoClientes', Cliente::class)) {
            return redirect()->route('reporte')
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
        }

        $cantidad = 10000;
        $nombres=filter_input(INPUT_GET, 'nombres', FILTER_SANITIZE_STRING);
        $apellidos=filter_input(INPUT_GET, 'apellidos', FILTER_SANITIZE_STRING);
        $identificacion=filter_input(INPUT_GET, 'identificacion', FILTER_SANITIZE_STRING);
        $con_notas=(filter_input(INPUT_GET, 'con_notas', FILTER_SANITIZE_STRING)=="on")?true:false;
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        $busqueda_numero_casa = filter_input(INPUT_GET, 'numero_casa', FILTER_SANITIZE_STRING);
        $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);

        if ($user->isConstructora() || $user->isBroker() || $user->isEjVentas()) {
            $brokers = [];
            if ($user->isConstructora()) {
                $brokers = $user->constructora->brokers->map(function ($o) {
                    return $o->id;
                })->all();
            } elseif ($user->isEjVentas()) {
                $brokers[] = $user->ejecutivoVentas->broker->id;
            } else {
                $brokers[] = $user->broker->id;
            }

            $cliente=Cliente::leftJoin('clientes_casas', 'clientes.id', '=', 'clientes_casas.id_cliente')
                ->leftJoin('casas', 'clientes_casas.id_casa', '=', 'casas.id')
                ->select('clientes.*', 'casas.codigo', 'casas.id_casa_estado')
                ->whereIn('clientes.id_broker', $brokers)
                ->with('clienteCasa', 'telefonoCliente', 'referenciasPersonales');

            if ($nombres) {
                $cliente=$cliente->where('nombre', 'LIKE', "%$nombres%");
            }
            if ($apellidos) {
                $cliente=$cliente->where('apellido', 'LIKE', "%$apellidos%");
            }
            if ($identificacion) {
                $cliente=$cliente->where('identificacion', 'LIKE', "%$identificacion%");
            }
            if ($busqueda_numero_casa) {
                $cliente=$cliente->where('casas.codigo', '=', $busqueda_numero_casa);
            }
            if ($id_proyecto) {
                $cliente=$cliente->where('casas.id_proyecto', '=', $id_proyecto);
            }
            if ($id_casa_estado) {
                $cliente=$cliente->where('casas.id_casa_estado', '=', $id_casa_estado);
            }
            if ($con_notas) {
                $cliente->where('notas', '<>', "");
            }

            $pdf = PDF::loadView('reports.pdf.listaclientes', ['clientes' => $cliente->paginate($cantidad)]);

            return $pdf->download('reporte-lista-clientes.pdf');
        }
        if ($user->isEjBancos()) {
            $ejBancos = $user->ejecutivoBancos()->first();

            $broker= $user->ejecutivoBancos()->first();
            $broker= $broker->broker;

            $consultaClientes = DB::table('clientes_casas')
                ->where('id_ej_bancos', $ejBancos->id)
                ->select('clientes_casas.id_cliente')
                ->distinct()
                ->get();
            $idarray= [];
            foreach ($consultaClientes as $id) {
                array_push($idarray, $id->id_cliente);
            }

            $cliente=Cliente::leftJoin('clientes_casas', 'clientes.id', '=', 'clientes_casas.id_cliente')
                ->leftJoin('casas', 'clientes_casas.id_casa', '=', 'casas.id')
                ->select('clientes.*', 'casas.codigo', 'casas.id_casa_estado')
                ->where('clientes.id_broker', $broker->id)
                ->whereIn('id', $idarray)
                ->with('clienteCasa', 'telefonoCliente', 'referenciasPersonales');

            if ($nombres) {
                $cliente=$cliente->where('nombre', 'LIKE', "%$nombres%");
            }
            if ($apellidos) {
                $cliente=$cliente->where('apellido', 'LIKE', "%$apellidos%");
            }
            if ($identificacion) {
                $cliente=$cliente->where('identificacion', 'LIKE', "%$identificacion%");
            }
            if ($busqueda_numero_casa) {
                $cliente=$cliente->where('casas.codigo', '=', $busqueda_numero_casa);
            }
            if ($id_proyecto) {
                $cliente=$cliente->where('casas.id_proyecto', '=', $id_proyecto);
            }
            if ($id_casa_estado) {
                $cliente=$cliente->where('casas.id_casa_estado', '=', $id_casa_estado);
            }
            if ($con_notas) {
                $cliente->where('notas', '<>', "");
            }

            $pdf = PDF::loadView('reports.pdf.listaclientes', ['clientes' => $cliente->paginate($cantidad)]);

            return $pdf->download('reporte-lista-clientes.pdf');
        }

        return redirect()->route('reporte')
            ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reportelistaDePropiedades()
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('listadoPropiedades', Casa::class)) {
            return redirect()->route('reporte')
                ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
        }

        $cantidad = 10000;
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        $id_broker = filter_input(INPUT_GET, 'id_broker', FILTER_SANITIZE_NUMBER_INT);
        $recamaras = filter_input(INPUT_GET, 'recamaras', FILTER_SANITIZE_NUMBER_INT);
        $busqueda_numero_casa = filter_input(INPUT_GET, 'numero_casa', FILTER_SANITIZE_STRING);
        $banos = filter_input(INPUT_GET, 'banos', FILTER_SANITIZE_NUMBER_INT);
        $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);

        $estados = $user->getCasasEstados();
        if ($user->isConstructora()) {
            $constructora = $user->constructora()->first();
            $proyects = $constructora->proyecto();

            $proyects = $proyects->get();
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
                if ($id_casa_estado == 'sin_asignar') {
                    $casas->whereNull('casas.id_casa_estado');
                } else {
                    $casas->where('casas.id_casa_estado', $id_casa_estado);
                }
            }
            if ($busqueda_numero_casa) {
                $casas->where('casas.codigo', $busqueda_numero_casa);
            }

            $pdf = PDF::loadView('reports.pdf.listapropiedades', [
                'casas' => $casas->paginate($cantidad),
                'estados' => $estados,
            ]);
            return $pdf->download('reporte-lista-propiedades.pdf');
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

            $casas = Casa::leftJoin('clientes_casas', 'casas.id', '=', 'clientes_casas.id_casa')
                ->select('casas.*', 'clientes_casas.id_cliente', 'clientes_casas.id_ej_bancos')
                ->where('casas.id_broker', $broker->id)
                ->orderBy("casas.id_proyecto", 'asc')
                ->orderBy("casas.lote_apto", 'asc');
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
                $casas->where('casas.id_casa_estado', $id_casa_estado);
            }
            if ($busqueda_numero_casa) {
                $casas->where('casas.codigo', $busqueda_numero_casa);
            }

            $pdf = PDF::loadView('reports.pdf.listapropiedades', [
                'casas' => $casas->paginate($cantidad),
                'estados' => $estados,
            ]);
            return $pdf->download('reporte-lista-propiedades.pdf');
        }

        return redirect()->route('reporte')
            ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
    }
}
