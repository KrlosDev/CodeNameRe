<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProyectoForm;
use App\Http\Requests\ImagenForm;
use App\Models\Proyecto;
use App\Models\Broker;
use App\Models\Casa;
use App\Models\Configuracion;
use App\Models\Imagen;
use App\Models\Funciones;
use App\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProyectoController extends Controller
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

        $proyecto = Proyecto::find($id);
       
        if (! $proyecto || $user->cannot('get', $proyecto)) {
            return Funciones::return403();
        }

        $home= Casa::where('id_proyecto', $id)->first();
        $cantidad=count($proyecto->casas);
        $data= [];

        array_push($data, $proyecto, $home, $cantidad);

        return json_encode($data);
    }
    
    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll()
    {
        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $id_proyecto = filter_input(INPUT_GET, 'id_proyecto', FILTER_SANITIZE_NUMBER_INT);
        
        $estados = [
            "1" => "Activo",
            "2" => "Suspendido",
            "3" => "Culminado",
        ];

        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Proyecto::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        
        if ($user->isConstructora()) {
            $constructora = $user->constructora()->first();

            $proyectos= $constructora->proyecto();
            if ($id_proyecto) {
                $proyectos->where('proyectos.id', $id_proyecto);
            }

            $brokers = $constructora->brokers()
                ->orderBy('brokers.id', 'desc')
                ->get();

            return view('pages.proyecto.index')->with(
                [
                    'proyectos' => $proyectos->paginate($cantidad),
                    'brokers' => $brokers]
                )->with('estados', $estados)
                ->with('busqueda_cantidad', $cantidad)
                ->with('busqueda_proyecto', $id_proyecto);
        } elseif ($user->isBroker()) {
            $broker = $user->broker()->first();

            $id_proyectos = DB::table('casas')
                ->select('id_proyecto')
                ->distinct()
                ->where('id_broker', $broker->id)
                ->get();
           
            $idarray= [];
            foreach ($id_proyectos as $id) {
                array_push($idarray, $id->id_proyecto);
            }

            $pro= Proyecto::whereIn('id', $idarray);
            if ($id_proyecto) {
                $pro->where('proyectos.id', $id_proyecto);
            }

            return view('pages.proyecto.index')->with(['proyectos' => $pro->paginate($cantidad), 'brokers' => []])
                ->with('estados', $estados)
                ->with('brok', $broker)
                ->with('busqueda_cantidad', $cantidad)
                ->with('busqueda_proyecto', $id_proyecto);
        }

        return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
    }
    
    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\ProyectoForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProyectoForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', Proyecto::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $constructora = $user->constructora()->first();
        $cantidad = $request->cantidad;
        if ($cantidad<=0) {
            return redirect()->route('proyectos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Cantidad inválida."));
        }
        $proyecto = new Proyecto();
        $proyecto->codigo = $request->codigo;
        $proyecto->nombre = $request->nombre;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->id_constructora = $constructora->id;
        $proyecto->estado = $request->estado;
        if (! $proyecto->save()) {
            return redirect()->route('proyectos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el proyecto."));
        }
        $casas_correctas = 0;
        for ($i = 1; $i <= $cantidad; $i++) {
            $casa = new Casa();
            $casa->id_proyecto = $proyecto->id;
            $casa->codigo = $request->codigo."$i";
            $casa->modelo = $request->modelo;
            $casa->lote_apto = $i;
            $casa->mts2_total = $request->mts2_total;
            $casa->mts2_construccion = $request->mts2_construccion;
            $casa->recamaras = $request->recamaras;
            $casa->banos = $request->banos;
            $casa->monto_separacion = $request->monto_separacion;
            $casa->monto_abono_inicial = $request->monto_abono_inicial;
            $casa->monto_mts2_adicional = $request->monto_mts2_adicional;
            $casa->valor = $request->valor;
            if ($casa->save()) {
                $casas_correctas++;
            }
        }
        return redirect()->route('proyectos')
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
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
        if ($user->cannot('count', Proyecto::class)) {
            return json_encode([]);
        }
        return json_encode(Proyecto::count());
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\ProyectoForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProyectoForm $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $proyecto=Proyecto::find($id);
       
        if (! $proyecto) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "El proyecto ingresado no existe."));
        }
        if ($user->cannot('update', $proyecto)) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }
        
        $proyecto->nombre = $request->nombre;
        $proyecto->descripcion = $request->descripcion;
        $proyecto->estado = $request->estado;

        if ($request->casa) {
            $casas=$proyecto->casas;
            if (count($casas) != 0) {
                foreach ($casas as $home) {
                    $home->modelo = $request->modelo;
                    $home->mts2_total = $request->mts2_total;
                    $home->mts2_construccion = $request->mts2_construccion;
                    $home->recamaras = $request->recamaras;
                    $home->banos = $request->banos;
                    $home->monto_separacion = $request->monto_separacion;
                    $home->monto_abono_inicial = $request->monto_abono_inicial;
                    $home->monto_mts2_adicional = $request->monto_mts2_adicional;
                    $home->valor = $request->valor;
                    $home->save();
                }
            }
        }

        if (! $proyecto->save()) {
            return redirect()->route('proyectos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el proyecto."));
        }
    
        return redirect()->route('proyectos')
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
        $proyecto=Proyecto::find($id);
        
        if (! $proyecto) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El proyecto ingresado no existe."));
        }
        if ($user->cannot('delete', $proyecto)) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }
        
        if ($proyecto->getCasasAsignadas() > 0) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El proyecto actualmente tiene casas asignadas a brokers."));
        }
        
        if (Casa::where('casas.id_proyecto', $proyecto->id)->delete() && $proyecto->delete()) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
        }
        return redirect()->route('proyectos')
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error efectuando la operación."));
    }
    
    /**
     * Asigna las casas enviadas por POST a el broker y proyecto asignados
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function asignarCasas(Request $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $id_proyecto = $request->get('id_proyecto');
        $proyecto=Proyecto::find($id_proyecto);
        if (! $proyecto) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar", "El proyecto ingresado no existe."));
        }

        /**
         * @var Broker $broker
         */
        $broker=Broker::find($request->get('id_broker'));
        if (! $broker) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar", "El broker ingresado no existe."));
        }
        if ($user->cannot('update', $proyecto) || $user->cannot('propioBroker', $broker)) {
            return redirect()->route('proyectos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar asignar", "Operación errónea. Usuario no autorizado."));
        }

        $casas = Funciones::list2array($request->get('casas'));
        $actualizaciones_correctas = 0;
        if (! empty($casas)) {
            foreach ($casas as $casa) {
                $casa_ins = Casa::where('casas.lote_apto', $casa)
                    ->where('casas.id_proyecto', $proyecto->id)
                    //Validación de casa ya ocupada
                    ->whereNull('casas.id_broker')
                    ->first();
                if (! $casa_ins) {
                    continue;
                }
                $casa_ins->id_broker = $broker->id;
                if ($casa_ins->save()) {
                    $actualizaciones_correctas++;
                }
            }
        }
        return redirect()->route('proyectos')
            ->with("alert", Funciones::getAlert("success", "Asignación", "Se han asignado $actualizaciones_correctas de ".count($casas)." exitosamente."));
    }

    /**
     * @param int $id
     * @return string
     */
    public function getPropiedadesDisponibles($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = Proyecto::find($id);
        if (! $proyecto || $user->cannot('get', $proyecto)) {
            return Funciones::return403();
        }
        $casas = $proyecto->getPropiedadesDisponibles();
        return json_encode($casas);
    }

    /**
     * @param int $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function getViewPanelImagenes($id)
    {
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = Proyecto::find($id);
        if (! $proyecto) {
            return redirect()->route('proyectos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea, El proyecto ingreasdo no existe."));
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('verImagenes', $proyecto)) {
            return redirect()->route('proyectos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea, Usuario no autorizado."));
        }
        
        $configuracion = Configuracion::where('descripcion', Configuracion::CONFIGURACION_MAX_FOTOS_PROYECTO)->first();
        $imagenes = $proyecto->imagenes;

        return view('pages.proyecto.panel-imagenes')->with(['imagenes' => $imagenes, 'proyecto' => $proyecto, 'limite_imagenes' => $configuracion->contenido]);
    }

    /**
     * @param ImagenForm $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function agregarImagen(ImagenForm $request, $id)
    {
        $proyecto = Proyecto::find($id);
        if (! $proyecto) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea, El proyecto ingreasdo no existe."));
        }
        /**
         * @var User $user
         */
        $user = Auth::user();
        if ($user->cannot('subirImagenes', $proyecto)) {
            return redirect()->route('proyectos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea, Usuario no autorizado."));
        }
        
        $codigo = Imagen::generarNuevoCodigo().".".Funciones::getExtension($request->file('imagen')->getClientOriginalName());
        $path = base_path().Imagen::PATH_PUBLIC.'/';
        $request->file('imagen')->move($path, $codigo);
        
        $imagen=new Imagen();
        $imagen->descripcion=$request->descripcion;
        $imagen->nombre=$request->nombre;
        $imagen->path=$codigo;
        
        if ($imagen->save()) {
            $proyecto->imagenes()->attach($imagen);
            
            return redirect()->to('proyectos/'.$proyecto->id.'/imagenes')
                ->with("alert", Funciones::getAlert("success", "Agregado Exitosamente", "Operación Exitosa."));
        }

        return redirect()->to('proyectos/'.$proyecto->id.'/imagenes')->withInput()
            ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea al intentar subir la imagen."));
    }
}
