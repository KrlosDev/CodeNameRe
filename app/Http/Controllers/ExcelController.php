<?php

namespace App\Http\Controllers;

use App\Models\Banco;
use App\Models\Broker;
use App\Models\Cliente;
use App\Models\Casa;
use App\Models\Funciones;
use App\Models\Pago;
use App\Models\FormaPago;
use App\User;
use Illuminate\Support\Facades\Auth;
use DB;
use Illuminate\Database\Query\Builder;
use Maatwebsite\Excel\Facades\Excel;

class ExcelController extends Controller
{
    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporteListaDeClientesExcel()
    {
        Excel::create('Reporte lista de clientes', function ($excel) {
            $excel->sheet('Excel sheet', function ($sheet) {
                $sheet->setTitle('Reporte lista de clientes')
                    ->setOrientation('landscape');
                /**
                 * @var User $user
                 */
                $user = Auth::user();
                if ($user->cannot('listadoClientes', Cliente::class)) {
                    return redirect()->route('reporte')
                            ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
                }

                $nombres=filter_input(INPUT_GET, 'nombres', FILTER_SANITIZE_STRING);
                $apellidos=filter_input(INPUT_GET, 'apellidos', FILTER_SANITIZE_STRING);
                $identificacion=filter_input(INPUT_GET, 'identificacion', FILTER_SANITIZE_STRING);
                $con_notas=(filter_input(INPUT_GET, 'con_notas', FILTER_SANITIZE_STRING)=="on")?true:false;
                $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
                $busqueda_numero_casa = filter_input(INPUT_GET, 'numero_casa', FILTER_SANITIZE_STRING);
                $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);

                /**
                 * @var Cliente $cliente
                 */
                $cliente = null;
                /**
                 * @var array $brokers
                 */
                $brokers = [];

                if ($user->isConstructora() || $user->isBroker() || $user->isEjVentas()) {
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
                        if ($id_casa_estado == 'sin_asignar') {
                            $cliente->whereNull('casas.id_casa_estado');
                        } else {
                            $cliente->where('casas.id_casa_estado', $id_casa_estado);
                        }
                    }
                    if ($con_notas) {
                        $cliente->where('notas', '<>', "");
                    }
                } elseif ($user->isEjBancos()) {
                    $ejBancos = $user->ejecutivoBancos()->first();

                    $brokers[] = $user->ejecutivoBancos->broker->id;

                    $consultaClientes = DB::table('clientes_casas')
                        ->where('id_ej_bancos', $ejBancos->id)
                        ->select('clientes_casas.id_cliente')
                        ->distinct()
                        ->get();
                    $idarray=[];
                    foreach ($consultaClientes as $id) {
                        array_push($idarray, $id->id_cliente);
                    }

                    $cliente=Cliente::join('clientes_casas', 'clientes.id', '=', 'clientes_casas.id_cliente')
                        ->join('casas', 'clientes_casas.id_casa', '=', 'casas.id')
                        ->select('clientes.*', 'casas.codigo', 'casas.id_casa_estado')
                        ->whereIn('casas.id_broker', $brokers)
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
                }

                $sheet->loadView('reports.excel.lista-clientes', ['clientes' =>$cliente->get()]);
            });
        })->download('xls');
        return redirect()->back();
    }

    public function reportelistaDePropiedades()
    {
        Excel::create('Reporte lista de propiedades', function ($excel) {
            $excel->sheet('Excel sheet', function ($sheet) {
                $sheet->setTitle('Reporte lista de propiedades')
                    ->setOrientation('landscape');
                /**
                 * @var User $user
                 */
                $user = Auth::user();
                if ($user->cannot('listadoPropiedades', Casa::class)) {
                    return redirect()->route('reporte')
                        ->with("alert", Funciones::getAlert("danger", "Error al mostrar reporte", "Usted no cuenta con los permisos para ver este reporte."));
                }

                $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
                $id_broker = filter_input(INPUT_GET, 'id_broker', FILTER_SANITIZE_NUMBER_INT);
                $recamaras = filter_input(INPUT_GET, 'recamaras', FILTER_SANITIZE_NUMBER_INT);
                $busqueda_numero_casa = filter_input(INPUT_GET, 'numero_casa', FILTER_SANITIZE_STRING);
                $banos = filter_input(INPUT_GET, 'banos', FILTER_SANITIZE_NUMBER_INT);
                $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);

                /**
                 * @var Broker $broker
                 */
                $broker = null;
                /**
                 * @var Builder $casas
                 */
                $casas = null;
                $estados = $user->getCasasEstados();

                if ($user->isConstructora()) {
                    $proyects = $user->constructora->proyecto();

                    $proyects = $proyects->get();
                    $proyectos = [];
                    foreach ($proyects as $proyecto) {
                        if (! $id_proyecto || $proyecto->id == $id_proyecto) {
                            array_push($proyectos, $proyecto->id);
                        }
                    }

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
                        $casas->where('casas.id_casa_estado', $id_casa_estado);
                    }
                    if ($busqueda_numero_casa) {
                        $casas->where('casas.codigo', $busqueda_numero_casa);
                    }

                    $sheet->loadView('excel.listapropiedades', ['casas' =>$casas->get(),'estados'=>$estados]);
                } elseif ($user->isBroker()||$user->isEjVentas()) {
                    if ($user->isBroker()) {
                        $broker = $user->broker()->first();
                    } elseif ($user->isEjVentas()) {
                        $broker = $user->ejecutivoVentas->broker()->first();
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
                }
                $sheet->loadView('reports.excel.lista-propiedades', ['casas' =>$casas->get(),'estados'=>$estados]);
            });
        })->download('xls');
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporteFinanzas()
    {
        Excel::create('Reporte finanzas', function ($excel) {
            $excel->sheet('Excel sheet', function ($sheet) {
                $sheet->setTitle('Reporte finanzas')
                              ->setOrientation('landscape');
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
                    $proyectos = $user->broker()->first()->getProyectos();
                } else {
                    $proyectos = $user->ejecutivoVentas()->first()->broker()->first()->getProyectos();
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
                $sheet->loadView('reports.excel.reporte-finanzas', ['pagos' => $pagos, 'totales_por_concepto' => $totales_por_concepto, 'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago, 'proyectos' => $proyectos, 'busqueda_cantidad' => $cantidad, 'busqueda_tipo_pago' => $id_tipo_transaccion, 'busqueda_fecha_min' => $fecha_min, 'busqueda_fecha_max' => $fecha_max, 'busqueda_proyecto' => $id_proyecto, 'busqueda_agrupar' => $agrupar]);
            });
        })->download('xls');
        return redirect()->back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporteInformeDeVentas()
    {
        Excel::create('Reporte informe de ventas', function ($excel) {
            $excel->sheet('Excel sheet', function ($sheet) {
                $sheet->setTitle('Reporte lista de propiedades')
                    ->setOrientation('landscape');
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
                    $broker = $user->broker()->first();
                    $proyectos = $broker->getProyectos();
                } else {
                    $proyectos = $constructora->proyecto()->get();
                }
                $bancos = Banco::orderBy('bancos.nombre', 'asc')
                    ->select(['bancos.id','bancos.nombre'])
                    ->get();
                $sheet->loadView('reports.excel.reporte-informe-ventas', [
                    'casas' => $casas->get(), 'bancos' => $bancos, 'proyectos' => $proyectos,
                    'estados' => $estados->pluck('nombre', 'id')->toArray(),
                    'formas_pago' => FormaPago::all(),
                    'tipos_pago' => Pago::$tipos_pago, 'busqueda_cantidad' => $cantidad,
                    'busqueda_banco' => $id_banco, 'busqueda_proyecto' => $id_proyecto,
                    'busqueda_id_casa_estado' => $id_casa_estado,
                    'busqueda_asignadas' => $asignadas, 'busqueda_asignadas_bancos' => $asignadas_bancos
                ]);
            });
        })->download('xls');
        return redirect()->back();
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function reporteEstadoCuentaCliente()
    {
        Excel::create('Reporte estado de cuenta cliente', function ($excel) {
            $excel->sheet('Excel sheet', function ($sheet) {
                $sheet->setTitle('Reporte estado de cuenta')
                    ->setOrientation('landscape');
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

                $sheet->loadView('reports.excel.reporte-estado-cuenta-cliente', ['pagos' => $pagos->paginate($cantidad), 'pagos_por_pagar' => $pagos_por_pagar, 'pagos_efectuados' => $pagos_efectuados, 'pagos_pendientes' => $pagos_pendientes, 'formas_pago' => FormaPago::all(), 'tipos_pago' => Pago::$tipos_pago, 'busqueda_cantidad' => $cantidad, 'busqueda_identificacion' => $identificacion, 'busqueda_propiedad' => $id_propiedad]);
            });
        })->download('xls');
        return redirect()->back();
    }
}
