<?php

namespace App\Http\Controllers;

use App\Jobs\SendRegisterNotification;
use App\Models\Broker;
use App\Repositories\ClienteRepository;
use Illuminate\Http\Request;
use App\Http\Requests\ClienteForm;
use App\Models\Cliente;
use App\Models\Funciones;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Codeudor;
use App\Models\Pais;
use App\Models\Casa;
use App\Models\ClienteCasa;
use App\Models\DocumentoCliente;
use App\Models\TelefonoCliente;
use App\Models\Corregimiento;
use App\Models\Distrito;
use App\Models\ClienteCodeudor;
use App\Models\EjecutivoVentas;
use App\Models\EjecutivoBancos;
use App\Models\Email;
use App\Models\Banco;
use App\Models\ReferenciaPersonal;
use App\Models\Proyecto;

use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
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
        $cliente = Cliente::with(['user','codeudor','telefonocliente','referenciasPersonales'])->where("clientes.id", $id)->first();

        $referencia = null;
        $i = 0;
        foreach ($cliente->referenciasPersonales as $referencia) {
            $referencia->numero = $i++;
        }
        for (;$i < count($cliente->referenciasPersonales); $i++) {
            $referencia->numero = $i+1;
        }
         
        if (! $cliente || $user->cannot('get', $cliente)) {
            return Funciones::return403();
        }
        return json_encode($cliente);
    }


    public function getEjecutivos($id)
    {
        /**
         * @var Cliente $cliente
         */
        $cliente = Cliente::find($id);
        $casas = $cliente->clienteCasa;

        if (count($casas)==0) {
            return json_encode([]);
        }

        $datos = [];
        $i = 0;
        foreach ($casas as $casa) {
            if ($casa->id_ej_ventas != null) {
                /**
                 * @var EjecutivoVentas $ejecutivo
                 */
                $ejecutivo=EjecutivoVentas::find($casa->id_ej_ventas);
                /**
                 * @var User $user
                 */
                $user=User::find($ejecutivo->id_user);

                $a=["id_ej"=> $ejecutivo->id,"nombre"=> $user->nombre];
                array_push($datos, $a);
                $i = $i + 1;
            }
        }
          
        return json_encode($datos);
    }

    /**
     * Display the specified resource.
     *
     * @param  Request $request
     * @param  ClienteRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function getAll(Request $request, ClienteRepository $repository)
    { 
        /**
         * @var User $user
         */
        $user=Auth::user();

        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $nombres=filter_input(INPUT_GET, 'nombres', FILTER_SANITIZE_STRING);
        $apellidos=filter_input(INPUT_GET, 'apellidos', FILTER_SANITIZE_STRING);
        $identificacion=filter_input(INPUT_GET, 'identificacion', FILTER_SANITIZE_STRING);
        $con_notas=(filter_input(INPUT_GET, 'con_notas', FILTER_SANITIZE_STRING)=="on")?true:false;
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        $busqueda_numero_casa = filter_input(INPUT_GET, 'numero_casa', FILTER_SANITIZE_STRING);
        $id_casa_estado = filter_input(INPUT_GET, 'id_casa_estado', FILTER_SANITIZE_STRING);


        if ($user->isConstructora() || $user->isBroker() || $user->isEjVentas()) {
            $brokers = $user->getBrokersIds();
            $estados = $user->getCasasEstados();
            
            $ejVentas = EjecutivoVentas::with(['user','broker'])
                ->whereIn('id_broker', $brokers)
                ->orderBy("ejecutivos_ventas.id", "desc")
                ->get();
            $ejBancos = EjecutivoBancos::with(['user','broker'])
                ->whereIn('id_broker', $brokers)
                ->orderBy("ejecutivos_bancos.id", "desc")
                ->get();

            $clientesSinCasa= $repository->getClientesSinCasa();
            $paises= Pais::orderBy('nombre')->pluck('nombre', 'id')->toArray();
            $corregimiento = Corregimiento::orderBy('nombre')->pluck('nombre', 'id')->toArray();

            $casas = Casa::whereIn('id_broker', $brokers)
                ->leftJoin('clientes_casas', 'casas.id', '=', 'clientes_casas.id_casa')
                ->select('casas.*', 'clientes_casas.id_cliente as idcliente')
                ->whereNull('id_cliente')
                ->get();
            
            $bancos = Banco::all()->pluck('nombre', 'id')->toArray();
            $bancos[0] = 'Ninguno';
            ksort($bancos);

            $cliente=Cliente::with('clienteCasa', 'telefonoCliente', 'referenciasPersonales')
                ->select(['clientes.*',\DB::raw('(SELECT COUNT(*) from clientes_casas cc WHERE cc.id_cliente = clientes.id) as cant_casas'),'casas.codigo','casas.id_casa_estado','casas.id_proyecto'])
                ->leftJoin('clientes_casas', 'clientes.id', '=', 'clientes_casas.id_cliente')
                ->leftJoin('casas', 'clientes_casas.id_casa', '=', 'casas.id')
                ->orderBy('cant_casas', 'desc')
                ->orderBy('casas.lote_apto', 'asc');

            //We check by roles first and if they have assigned or assigment is null
            //This is because user needs to be seen for assigment
            if (($user->isConstructora() || $user->isBroker())) {
                $cliente->where(function ($q) use ($brokers) {
                    $q->whereIn('clientes.id_broker', $brokers)
                        ->orWhere('clientes.id_broker', "IS", \DB::raw("NULL"));
                });
            } else {
                $cliente->whereIn('clientes.id_broker', $brokers);
            }
            //->orderBy('casas.id_proyecto', 'asc')
            //->orderBy('casas.lote_apto', 'asc');

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
                $con_notas='on';
            }

            $cliente=$cliente->paginate($cantidad);
            $referencias = [
                ReferenciaPersonal::generarReferencia(1),
                ReferenciaPersonal::generarReferencia(2),
                ReferenciaPersonal::generarReferencia(3),
            ];
            $distritos = Distrito::orderBy('distritos.nombre')->pluck('distritos.nombre', 'distritos.id')->toArray();

            $proyectos = [];

            $casas1 = Casa::whereIn('id_broker', $brokers)->with('cliente')->get();
            if (! empty($casas1)) {
                $ids = array_map(function ($o) {
                    return $o->id_proyecto;
                }, $casas1->all());
                $proyectos = Proyecto::whereIn('proyectos.id', $ids)->get();
            }

            return view('pages.cliente.index')->with('clientes', $cliente)
                ->with('clienteSinCasa', $clientesSinCasa)
                ->with('paises', $paises)
                ->with('estados_civiles', Cliente::getEstadosCiviles())
                ->with('corregimientos', $corregimiento)
                ->with('distritos', $distritos)
                ->with('tipoTrabajo', Cliente::getTipoTrabajos())
                ->with('tipo_documentos', DocumentoCliente::$tipos)
                ->with('casapto', Cliente::getTipoVivienda())
                //->with('estatus_cliente', Casa::$estados)
                ->with('estados', $estados)
                ->with('casas', $casas)
                ->with('referencias', $referencias)
                ->with('ejVentas', $ejVentas)
                ->with('ejBancos', $ejBancos)
                //->with('estados_casas', Casa::$estados)
                ->with('bancos', $bancos)
                ->with('proyectos', $proyectos)
                ->with('busqueda_proyecto', $id_proyecto)
                ->with('busqueda_numero_casa', $busqueda_numero_casa)
                ->with('busqueda_id_casa_estado', $id_casa_estado)
                //->with('estados', Casa::$estados)
                ->with('nombres', $nombres)
                ->with('apellidos', $apellidos)
                ->with('identificacion', $identificacion)
                ->with('con_notas', $con_notas);
        } elseif ($user->isEjBancos()) {
            $broker= $user->ejecutivoBancos->broker;
            $estados = $user->getCasasEstados();

            $consultaClientes = DB::table('clientes_casas')
            ->where('id_ej_bancos', $user->ejecutivoBancos->id)
            ->select('clientes_casas.id_cliente')
            ->distinct()
            ->get();
            $idarray= [];
            foreach ($consultaClientes as $id) {
                array_push($idarray, $id->id_cliente);
            }

            $cliente=Cliente::with('clienteCasa', 'telefonoCliente', 'referenciasPersonales')
                ->leftJoin('clientes_casas', 'clientes.id', '=', 'clientes_casas.id_cliente')
                ->leftJoin('casas', 'clientes_casas.id_casa', '=', 'casas.id')
                ->select('clientes.*', 'casas.codigo', 'casas.id_casa_estado', \DB::raw('(SELECT COUNT(*) from clientes_casas cc WHERE cc.id_cliente = clientes.id) as cant_casas'))
                ->where('clientes.id_broker', $broker->id)
                ->whereIn('clientes.id', $idarray)
                ->orderBy('cant_casas', 'desc')
                ->orderBy('clientes.id', 'desc');

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
                $con_notas='on';
            }
            $cliente=$cliente->paginate($cantidad);

            $paises= Pais::orderBy('nombre')->pluck('nombre', 'id');
            $paises=$paises->toArray();

            $corregimiento = Corregimiento::orderBy('nombre')->pluck('nombre', 'id');
            $corregimiento = $corregimiento->toArray();
        
            $estados_civiles = Cliente::getEstadosCiviles();
        
            $tipoTrabajo= Cliente::getTipoTrabajos();
        
            $tipo_documentos = DocumentoCliente::$tipos;
        
            $tipoCasa = Cliente::getTipoVivienda();

            $bancos = Banco::all()->pluck('nombre', 'id')->toArray();
            $bancos[0] = 'Ninguno';
            ksort($bancos);

            $referencias = [
                 ReferenciaPersonal::generarReferencia(1),
                 ReferenciaPersonal::generarReferencia(2),
                 ReferenciaPersonal::generarReferencia(3),
            ];
            $distritos = Distrito::orderBy('distritos.nombre')->pluck('distritos.nombre', 'distritos.id')->toArray();

            $proyectos = [];
            $casas1 = $broker->casa()->with('cliente')->get();
            if (! empty($casas1)) {
                $ids = array_map(function ($o) {
                    return $o->id_proyecto;
                }, $casas1->all());
                $proyectos = Proyecto::whereIn('proyectos.id', $ids)->get();
            }

            return view('pages.cliente.index')->with('clientes', $cliente)
                ->with('paises', $paises)
                ->with('estados_civiles', $estados_civiles)
                ->with('corregimientos', $corregimiento)
                ->with('distritos', $distritos)
                ->with('tipoTrabajo', $tipoTrabajo)
                ->with('tipo_documentos', $tipo_documentos)
                ->with('casapto', $tipoCasa)
                //->with('estatus_cliente', Casa::$estados)
                ->with('estados', $estados)
                ->with('referencias', $referencias)
                ->with('ejVentas', [])
                ->with('ejBancos', [])
                //->with('estados_casas', Casa::$estados)
                ->with('bancos', $bancos)
                ->with('casas', [])
                ->with('proyectos', $proyectos)
                ->with('busqueda_proyecto', $id_proyecto)
                ->with('busqueda_numero_casa', $busqueda_numero_casa)
                ->with('busqueda_id_casa_estado', $id_casa_estado)
                //->with('estados', Casa::$estados)
                ->with('nombres', $nombres)
                ->with('apellidos', $apellidos)
                ->with('identificacion', $identificacion)
                ->with('con_notas', $con_notas);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ClienteForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ClienteForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Broker $broker
         */
        $broker = null;
        if ($user->isEjVentas()) {
            $ejecutivoVenta = $user->ejecutivoVentas()->first();
            $broker=$ejecutivoVenta->broker()->first();
        } elseif ($user->isBroker()) {
            $broker = $user->broker()->first();
        }

        if ($user->cannot('store', Cliente::class)) {
            return redirect()->route('clientes')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        $usuario=new User();
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;

        if ($request->get('email')) {
            $usuario->email=$request->email;
        }
        $usuario->id_rol=User::CLIENTE;
        $usuario->password=bcrypt($request->password);
        if (! $usuario->save()) {
            return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el usuario."));
        }

        $cliente = new Cliente();
        $cliente->nombre = $request->nombre;
        $cliente->apellido = $request->apellido;
        $cliente->id_broker = $broker->id;
        $cliente->identificacion = $request->identificacionc;
        $cliente->fecha_nacimiento = $request->fecha_nacimiento;
        $cliente->estado_civil = $request->estado_civil;
        $cliente->id_pais = $request->id_pais;
        $cliente->id_distrito = $request->id_distrito;
        $cliente->direccion = $request->direccion;
        $cliente->notas = $request->notas;
        $cliente->casa_apartamento = $request->casa_apartamento;
        if ($request->get('email')) {
            $cliente->email = $request->get('email');
        }
        $cliente->id_user = $usuario->id;
        $cliente->tipo_trabajo = $request->tipo_trabajo;
        $cliente->salario = $request->salario;
        if ($request->id_banco) {
            $cliente->id_banco = $request->id_banco;
        }

        if ($cliente->tipo_trabajo == 2) {
            $cliente->empresa = $request->empresa;
            $cliente->cargo_empresa = $request->cargo_empresa;
            $cliente->anios_laborando = $request->años_laborando;
            $cliente->direccion_empresa = $request->direccion_empresa;
            $cliente->telefonos_empresa = $request->telefonos_empresa;
            if ($request->get('email_empresa')) {
                $cliente->email_empresa = $request->email_empresa;
            }
        }
        
        if (! $cliente->save()) {
            return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el cliente."));
        }

        $telefono=new TelefonoCliente();
        $telefono->telefono= $request->telefono;
        $telefono->id_cliente= $cliente->id;

        if ($request->id_casa != "") {
            $clientecasa=new ClienteCasa();
            $clientecasa->id_cliente=$cliente->id;
            $clientecasa->id_casa= $request->id_casa;

            if (! $clientecasa->save()) {
                return redirect()->route('clientes')->withInput()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error asignando la casa."));
            }
            
            $house=Casa::find($request->id_casa);

            /*if ($request->estado_casa && in_array($request->estado_casa, array_keys(Casa::$estados))) {
                $house->id_casa_estado=$request->estado_casa;

            }*/
            if ($request->estado_casa) {
                $house->id_casa_estado = $request->estado_casa;
            }
            
            if ($user->isEjVentas()) {
                $house->id_ej_ventas=$user->ejecutivoVentas->id;
            }
          
            if (! $house->save()) {
                return redirect()->route('clientes')->withInput()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar Asignar", "Operación errónea. Error asignando la casa."));
            }
        }

        if ($request->codeudor) {
            $codeudor = new Codeudor();
            $codeudor->nombre = $request->co_nombre;
            $codeudor->apellido = $request->co_apellido;
            $codeudor->identificacion = $request->co_identificacion;
            $codeudor->fecha_nacimiento = $request->co_fecha_nacimiento;
            $codeudor->estado_civil = $request->co_estado_civil;
            $codeudor->id_pais = $request->co_id_pais;
            $codeudor->id_distrito = $request->co_id_distrito;
            $codeudor->direccion = $request->co_direccion;
            $codeudor->casa_apartamento = $request->co_casa_apartamento;
            $codeudor->email = $request->co_email;
            $codeudor->tipo_trabajo = $request->co_tipo_trabajo;
            $codeudor->salario = $request->co_salario;
            $codeudor->email = $request->co_email;

            if ($codeudor->tipo_trabajo == 2) {
                $codeudor->empresa = $request->co_empresa;
                $codeudor->cargo_empresa = $request->co_cargo_empresa;
                $codeudor->anios_laborando = $request->co_años_laborando;
                $codeudor->direccion_empresa = $request->co_direccion_empresa;
                $codeudor->telefonos_empresa = $request->co_telefonos_empresa;
                $codeudor->email_empresa = $request->co_email_empresa;
            }

            if (! $codeudor->save()) {
                return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el cliente."));
            }

            $clienteCodeudor = new ClienteCodeudor();
            $clienteCodeudor->id_cliente = $cliente->id;
            $clienteCodeudor->id_codeudor = $codeudor->id;

            if (! $clienteCodeudor->save()) {
                return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el cliente."));
            }
        }
        
        $telefono->telefono= $request->telefono;
        $telefono->id_cliente= $cliente->id;
        
        if (! $telefono->save()) {
            return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el cliente."));
        }
        
        for ($i = 0; $i < ReferenciaPersonal::MAX_REFERENCIAS; $i++) {
            $nombre = filter_input(INPUT_GET, "ref_nombre_$i", FILTER_SANITIZE_STRING);
            $parentesco = filter_input(INPUT_GET, "ref_parentesco_$i", FILTER_SANITIZE_STRING);
            $telefono = filter_input(INPUT_GET, "ref_telefono_$i", FILTER_SANITIZE_STRING);
            
            if (! $nombre || ! $parentesco || ! $telefono) {
                continue;
            }
            
            $referenciaPersonal = new ReferenciaPersonal();
            $referenciaPersonal->nombre = $nombre;
            $referenciaPersonal->parentesco = $parentesco;
            $referenciaPersonal->telefono = $telefono;
            $referenciaPersonal->save();
        }
        
        try {
            if ($usuario->email) {
                dispatch(new SendRegisterNotification($usuario, $request->password));
            }
        } catch (\Exception $ex) {
            \Log::error($ex);
            return redirect()->route('clientes')
                ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa, pero ha ocurrido un error enviando el correo electrónico."));
        }
        return redirect()->route('clientes')
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ClienteForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ClienteForm $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Cliente $cliente
         */
        $cliente=Cliente::find($id);
        $usuario=User::find($cliente->id_user);
        /**
         * @var Codeudor $codeudor
         */
        $codeudor = null;
        /**
         * @var ClienteCodeudor $clienteCodeudor
         */
        $clienteCodeudor=ClienteCodeudor::where('id_cliente', '=', $cliente->id)->first();
        $telefono = TelefonoCliente::where('id_cliente', '=', $cliente->id)->first();
        if ($clienteCodeudor != null) {
            $codeudor=Codeudor::find($clienteCodeudor->id_codeudor);
        }
        if ($request->password!='') {
            $usuario->password=bcrypt($request->password);
        }
        
        if (! $cliente) {
            return redirect()->route('clientes')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "El broker ingresado no existe."));
        }
        if ($user->cannot('update', $cliente)) {
            return redirect()->route('clientes')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea. Usuario no autorizado."));
        }
        
        $usuario->name=$request->name;
        $usuario->nombre = $request->nombre;
        if ($request->get('email')) {
            $usuario->email=$request->email;
        }
        
        if (! $usuario->save()) {
            return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Error actualizando el cliente."));
        }
       
        $cliente->nombre = $request->nombre;
        $cliente->apellido = $request->apellido;
        $cliente->identificacion = $request->identificacionc;
        $cliente->fecha_nacimiento = $request->fecha_nacimiento;
        $cliente->estado_civil = $request->estado_civil;
        $cliente->id_pais = $request->id_pais;
        $cliente->id_distrito = $request->id_distrito;
        $cliente->direccion = $request->direccion;
        if ($request->get('notas')) {
            $cliente->notas = $request->notas;
        } else {
            $cliente->notas = null;
        }
        $cliente->casa_apartamento = $request->casa_apartamento;
        if ($request->get('email')) {
            $cliente->email = $request->email;
        }
        $cliente->id_user = $usuario->id;
        $cliente->tipo_trabajo = $request->tipo_trabajo;
        $cliente->salario = $request->salario;

        if ($request->get('id_banco')) {
            $cliente->id_banco = $request->id_banco;
        }
        if ($cliente->tipo_trabajo == 2) {
            $cliente->empresa = $request->empresa;
            $cliente->cargo_empresa = $request->cargo_empresa;
            $cliente->anios_laborando = $request->años_laborando;
            $cliente->direccion_empresa = $request->direccion_empresa;
            $cliente->telefonos_empresa = $request->telefonos_empresa;
            if ($request->get('email_empresa')) {
                $cliente->email_empresa = $request->email_empresa;
            }
        }
        
        if (! $cliente->save()) {
            return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Error actualizando el cliente."));
        }

        if ($request->codeudor) {
            if ($clienteCodeudor == null) {
                $codeudor = new Codeudor();
                $codeudor->nombre = $request->co_nombre;
                $codeudor->apellido = $request->co_apellido;
                $codeudor->identificacion = $request->co_identificacion;
                $codeudor->fecha_nacimiento = $request->co_fecha_nacimiento;
                $codeudor->estado_civil = $request->co_estado_civil;
                $codeudor->id_pais = $request->co_id_pais;
                $codeudor->id_distrito = $request->co_id_distrito;
                $codeudor->telefono = $request->co_telefono;
                $codeudor->direccion = $request->co_direccion;
                $codeudor->casa_apartamento = $request->co_casa_apartamento;
                $codeudor->email = $request->co_email;
                $codeudor->tipo_trabajo = $request->co_tipo_trabajo;
                $codeudor->salario = $request->co_salario;
            
                $codeudor->email = $request->co_email;
            
                if ($codeudor->tipo_trabajo == 2) {
                    $codeudor->empresa = $request->co_empresa;
                    $codeudor->cargo_empresa = $request->co_cargo_empresa;
                    $codeudor->anios_laborando = $request->co_años_laborando;
                    $codeudor->direccion_empresa = $request->co_direccion_empresa;
                    $codeudor->telefonos_empresa = $request->co_telefonos_empresa;
                    $codeudor->email_empresa = $request->co_email_empresa;
                }

                if (! $codeudor->save()) {
                    return redirect()->route('clientes')->withInput()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el cliente."));
                }

                $clienteCodeudor = new ClienteCodeudor();
                $clienteCodeudor->id_cliente = $cliente->id;
                $clienteCodeudor->id_codeudor = $codeudor->id;

                if (! $clienteCodeudor->save()) {
                    return redirect()->route('clientes')->withInput()
                        ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el cliente."));
                }
            } else {
                $codeudor->nombre = $request->co_nombre;
                $codeudor->apellido = $request->co_apellido;
                $codeudor->identificacion = $request->co_identificacion;
                $codeudor->fecha_nacimiento = $request->co_fecha_nacimiento;
                $codeudor->estado_civil = $request->co_estado_civil;
                $codeudor->id_pais = $request->co_id_pais;
                $codeudor->id_distrito = $request->co_id_distrito;
                $codeudor->telefono = $request->co_telefono;
                $codeudor->direccion = $request->co_direccion;
                $codeudor->casa_apartamento = $request->co_casa_apartamento;
                $codeudor->email = $request->co_email;
                $codeudor->tipo_trabajo = $request->co_tipo_trabajo;
                $codeudor->salario = $request->co_salario;
                $codeudor->email = $request->co_email;

                if ($codeudor->tipo_trabajo == 2) {
                    $codeudor->empresa = $request->co_empresa;
                    $codeudor->cargo_empresa = $request->co_cargo_empresa;
                    $codeudor->anios_laborando = $request->co_años_laborando;
                    $codeudor->direccion_empresa = $request->co_direccion_empresa;
                    $codeudor->telefonos_empresa = $request->co_telefonos_empresa;
                    $codeudor->email_empresa = $request->co_email_empresa;
                }
                if (! $codeudor->save()) {
                    return redirect()->route('clientes')->withInput()
                        ->with("alert", Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Error actualizando el cliente."));
                }
            }
        }
        $telefono->telefono= $request->telefono;

        if (! $telefono->save()) {
            return redirect()->route('clientes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Error actualizando el cliente."));
        }
        
        for ($i = 1; $i <= ReferenciaPersonal::MAX_REFERENCIAS; $i++) {
            $id_ref = filter_input(INPUT_POST, "ref_id_$i", FILTER_SANITIZE_NUMBER_INT);
            $nombre = filter_input(INPUT_POST, "ref_nombre_$i", FILTER_SANITIZE_STRING);
            $parentesco = filter_input(INPUT_POST, "ref_parentesco_$i", FILTER_SANITIZE_STRING);
            $telefono = filter_input(INPUT_POST, "ref_telefono_$i", FILTER_SANITIZE_STRING);

            if (! $nombre || ! $parentesco || ! $telefono) {
                continue;
            }

            if ($id_ref) {
                if (! $referenciaPersonal = ReferenciaPersonal::find($id_ref)) {
                    continue;
                }
            } else {
                $referenciaPersonal = new ReferenciaPersonal();
            }
            $referenciaPersonal->id_cliente = $cliente->id;
            $referenciaPersonal->nombre = $nombre;
            $referenciaPersonal->parentesco = $parentesco;
            $referenciaPersonal->telefono = $telefono;
            $referenciaPersonal->save();
        }
        
        return redirect()->back()
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
        
        $cliente=Cliente::find($id);
        $usuario = User::find($cliente->id_user);

        if (! $cliente) {
            return redirect()->route('clientes')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El cliente ingresado no existe."));
        }
        if ($user->cannot('delete', $cliente)) {
            return redirect()->route('clientes')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }
        
        $asignados = ClienteCasa::where('id_cliente', '=', $cliente->id)
            ->count();

        if ($asignados == 0) {
            if ($cliente->delete() && $usuario->delete()) {
                return redirect()->route('clientes')
                    ->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
            }
        } else {
            return redirect()->route('clientes')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El Cliente seleccionado tiene casas asignadas."));
        }
        return redirect()->route('clientes')
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error efectuando la operación."));
    }
    
    /**
     * Retorna los clientes que coincidan o contengan la identidad provista
     *
     * @return \Illuminate\Http\Response
     */
    public function buscar(Request $request)
    {
        //$identidad = filter_input(INPUT_GET, 'term', FILTER_SANITIZE_STRING);
        $identidad = $request->get('term');
        
        if (! $identidad || strlen($identidad) < 3) {
            return json_encode([]);
        }
        
        /**
         * @var User $user
         */
        $user = Auth::user();
        $broker = $user->getBroker();
        if ($user->cannot('buscar', Cliente::class) || ! $broker) {
            return Funciones::return403();
        }
        
        $clientes = Cliente::where('clientes.identificacion', 'LIKE', "%$identidad%")
            ->where('clientes.id_broker', $broker->id)
            //->select(['clientes.identificacion+ as label'])
            ->select(\DB::raw("CONCAT(clientes.identificacion,'/',clientes.nombre) as label"))
            ->limit(10)
            ->get();
        
        return json_encode($clientes);
    }

    /**
     * Returns the executives for the logged client
     *
     * @return \Illuminate\Http\Response
     */
    public function ejecutivos()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if (! $user->isCliente()) {
            return Funciones::return403();
        }

        $ejVentas = [];
        $casas = $user->cliente->clienteCasa;
        /**
         * @var Casa $casa
         */
        foreach ($casas as $casa) {
            if ($casa->ejecutivoVentas) {
                $ejVentas[] = $casa->ejecutivoVentas()
                    ->with('user')
                    ->first();
            }
        }

        return json_encode($ejVentas);
    }
}
