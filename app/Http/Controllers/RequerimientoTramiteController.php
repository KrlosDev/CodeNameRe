<?php

namespace App\Http\Controllers;

use App\Http\Requests\RequerimientoTramiteForm;
use App\Jobs\CopiarRequerimientosDeProyecto;
use App\Models\Proyecto;
use App\Models\RequerimientoTramite;
use App\Models\Funciones;
use App\Repositories\RequerimientoTramiteRepository;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RequerimientoTramiteController extends Controller
{
    /**
     * @param int $id_proyecto
     * @param int $id
     *
     * @return string
     */
    public function get($id_proyecto, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = RequerimientoTramite::find($id);
        /**
         * @var Proyecto $proyecto
         */
        $proyecto = Proyecto::find($id);

        if (! $requerimientoTramite || $proyecto || $user->cannot('get', $requerimientoTramite)) {
            return Funciones::return403();
        }

        return json_encode($requerimientoTramite);
    }

    /**
     * @var int $id_proyecto
     * @var RequerimientoTramiteRepository $repository
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getAll($id_proyecto, RequerimientoTramiteRepository $repository)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', RequerimientoTramite::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $busqueda_cantidad = filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT);

        $requerimientosTramites = $repository->paginateRequerimientos($id_proyecto, $busqueda_cantidad);
        $proyectos = Proyecto::where('id_constructora', $user->constructora->id)->get();
        $proyecto = Proyecto::find($id_proyecto);

        return view('pages.proyecto.requerimientos-tramites.index')
            ->with('busqueda_cantidad', $busqueda_cantidad)
            ->with('proyecto', $proyecto)
            ->with('proyectos', $proyectos)
            ->with('requerimientosTramites', $requerimientosTramites);
    }

    /**
     * @param RequerimientoTramiteForm $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(RequerimientoTramiteForm $request, $id)
    {
        /**
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite = RequerimientoTramite::find($id);
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('update', $requerimientoTramite)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Usuario no autorizado."));
        }

        $data = $request->only(['nombre']);

        $requerimientoTramite->fill($data);

        if (! $requerimientoTramite->save()) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar actualizar", "Operación errónea. Ha ocurrido un error actualizando el requerimiento."));
        }

        return redirect()->back()->with('alert', Funciones::getAlert("success", "Actualización realizada con éxito", "Operación realizada con éxito."));
    }

    /**
     * @param int $id_proyecto
     * @param RequerimientoTramiteForm $request
     * @param RequerimientoTramiteRepository $repository
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store($id_proyecto, RequerimientoTramiteForm $request, RequerimientoTramiteRepository $repository)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', [RequerimientoTramite::class,$id_proyecto])) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Usuario no autorizado."));
        }

        $requerimientoTramite = $repository->create($id_proyecto, $request->nombre);

        if (! $requerimientoTramite) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Error enviando agregando el requerimiento."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
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
         * @var RequerimientoTramite $requerimientoTramite
         */
        $requerimientoTramite= RequerimientoTramite::find($id);
        if ($user->cannot('delete', $requerimientoTramite)) {
            return redirect()->route('requerimientos_tramites', ['id_proyecto'=>$id])->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }

        if (! $requerimientoTramite->delete()) {
            return redirect()->route('requerimientos_tramites', ['id_proyecto'=>$id])->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea."));
        }
        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
    }

    /**
     * Copy whole requerimientos from one project to another
     *
     * @param int $id_proyecto
     * @param Request $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copiar($id_proyecto, Request $request)
    {
        /**
         * @var Proyecto $proyecto_orig
         */
        $proyecto_orig = Proyecto::find($request->id_proyecto);
        /**
         * @var Proyecto $proyecto_dest
         */
        $proyecto_dest = Proyecto::find($id_proyecto);
        /**
         * @var User $user
         */
        $user=Auth::user();
        if (! $proyecto_orig || ! $proyecto_dest || ! $user->can('copiar', [RequerimientoTramite::class,$proyecto_orig,$proyecto_dest])) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar copiar", "Operación errónea. Usuario no autorizado."));
        }
        
        if (! dispatch(new CopiarRequerimientosDeProyecto($proyecto_orig, $proyecto_dest))) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar copiar", "Operación errónea."));
        }

        return redirect()->back()->with('alert', Funciones::getAlert("success", "Copia realizada con éxito", "Operación realizada con éxito."));
    }
}
