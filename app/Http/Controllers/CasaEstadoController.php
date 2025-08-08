<?php

namespace App\Http\Controllers;

use App\Http\Requests\CasaEstadoForm;
use App\Models\Casa;
use App\Models\CasaEstado;
use App\Models\Funciones;
use App\Repositories\CasaEstadoRepository;
use App\User;

use Illuminate\Support\Facades\Auth;

class CasaEstadoController extends Controller
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
        $casaEstado = CasaEstado::with(['broker'])
            ->where("id", $id)
            ->first();
        if (! $casaEstado || $user->cannot('get', $casaEstado)) {
            return Funciones::return403();
        }
        return json_encode($casaEstado);
    }

    /**
     * Display the specified resource.
     *
     * @param CasaEstadoRepository $repository
     *
     * @return \Illuminate\Http\Response
     */
    public function getAll(CasaEstadoRepository $repository)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', CasaEstado::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $cantidad = ((filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT))?filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT):15);
        $id_broker = filter_input(INPUT_GET, 'id_broker', FILTER_SANITIZE_NUMBER_INT);

        $data = $repository->paginate($user, $cantidad, $id_broker);
        if ($data===null) {
            return Funciones::return403();
        }

        return view('pages.casa-estado.index', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  CasaEstadoForm  $request
     * @param CasaEstadoRepository $repository
     * @return \Illuminate\Http\Response
     */
    public function store(CasaEstadoForm $request, CasaEstadoRepository $repository)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', CasaEstado::class)) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }
        
        $casaEstado = $repository->create($user->broker->id, $request->nombre, $request->slug);

        if (! $casaEstado) {
            return redirect()->route('casas_estados')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar guardar", "Operación errónea."));
        }
        

        return redirect()->route('casas_estados')
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa, se ha guardado el estado correctamente."));
    }

    /**
     * @param CasaEstadoForm $request
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(CasaEstadoForm $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado=CasaEstado::find($id);
        if (! $casaEstado) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "El estado ingresado no existe."));
        }
        if ($user->cannot('update', $casaEstado)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }

        $casaEstado->nombre = $request->nombre;
        //$casaEstado->slug = $request->slug;

        if (! $casaEstado->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el estado."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param int $id
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function delete($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        /**
         * @var CasaEstado $casaEstado
         */
        $casaEstado=CasaEstado::find($id);

        if (! $casaEstado) {
            return redirect()->route('casas_estados')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El estado ingresado no existe."));
        }

        if ($user->cannot('delete', $casaEstado)) {
            return redirect()->route('casas_estados')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }

        if (Casa::where('id_casa_estado', $casaEstado->id)->count() > 0) {
            return redirect()->route('casas_estados')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El estado actualmente se encuentra asignado a una propiedad"));
        }

        if ($casaEstado->delete()) {
            return redirect()->route('casas_estados')
                ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
        }
        return redirect()->route('casas_estados')
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error efectuando la operación."));
    }
}
