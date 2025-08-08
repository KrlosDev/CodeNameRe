<?php

namespace App\Http\Controllers;

use App\Http\Requests\CasaRequerimientoTramiteForm;
use App\Models\Casa;
use App\Models\CasaRequerimientoTramite;
use App\Models\Funciones;
use App\Repositories\CasaRequerimientoTramiteRepository;
use App\Repositories\RequerimientoTramiteRepository;
use App\User;
use Illuminate\Support\Facades\Auth;

class CasaRequerimientoTramiteController extends Controller
{
    /**
     * @param int $id
     *
     * @return string
     */
    public function get($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var CasaRequerimientoTramite $casaRequerimientoTramite
         */
        $casaRequerimientoTramite = CasaRequerimientoTramite::find($id);

        if (! $casaRequerimientoTramite || $user->cannot('get', $casaRequerimientoTramite)) {
            return Funciones::return403();
        }

        return json_encode($casaRequerimientoTramite);
    }

    /**
     *
     * @param int $id_casa
     * @param CasaRequerimientoTramiteRepository $repository
     * @param RequerimientoTramiteRepository $requerimientoTramiteRepository
     *
     * @return $this|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function getAll($id_casa, CasaRequerimientoTramiteRepository $repository, RequerimientoTramiteRepository $requerimientoTramiteRepository)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', CasaRequerimientoTramite::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $busqueda_cantidad = filter_input(INPUT_GET, 'cantidad', FILTER_SANITIZE_NUMBER_INT);

        /**
         * @var Casa $casa
         */
        $casa = Casa::find($id_casa);
        $requerimientosTramites = $requerimientoTramiteRepository->paginateRequerimientos($casa->proyecto->id, $busqueda_cantidad);
        $casasRequerimientosTramites = $repository->getCasasRequerimientosTramites($casa->id);

        return view('pages.casa.requerimientos-tramites.index')
            ->with('busqueda_cantidad', $busqueda_cantidad)
            ->with('casa', $casa)
            ->with('requerimientosTramites', $requerimientosTramites)
            ->with('casasRequerimientosTramites', $casasRequerimientosTramites);
    }

    /**
     * @param CasaRequerimientoTramiteForm $request
     * @param CasaRequerimientoTramiteRepository $repository
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CasaRequerimientoTramiteForm $request, CasaRequerimientoTramiteRepository $repository)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        $casa = Casa::find($request->id_casa);
        if ($user->cannot('store', [CasaRequerimientoTramite::class,$casa])) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Usuario no autorizado."));
        }

        $casaRequerimientoTramite = $repository->create($request->id_casa, $request->id_requerimiento_tramite);

        if (! $casaRequerimientoTramite) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Error enviando agregando el requerimiento."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
     * @param int $id_casa
     * @param int $id_requerimiento_tramite
     * @param CasaRequerimientoTramiteRepository $repository
     *
     * @return \Illuminate\Http\RedirectResponse
     *
     * @throws \Exception
     */
    public function toggle($id_casa, $id_requerimiento_tramite, CasaRequerimientoTramiteRepository $repository)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Casa $casa
         */
        $casa = Casa::find($id_casa);
        if (! $casa || $user->cannot('store', [CasaRequerimientoTramite::class,$casa])) {
            return redirect()->route('/')->with('alert', Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Usuario no autorizado."));
        }

        /**
         * @var CasaRequerimientoTramite $casaRequerimientoTramite
         */
        $casaRequerimientoTramite = CasaRequerimientoTramite::where('id_casa', $id_casa)
            ->where('id_requerimiento_tramite', $id_requerimiento_tramite)
            ->first();

        //If exists we delete the requirement
        if ($casaRequerimientoTramite) {
            if (! $casaRequerimientoTramite->delete()) {
                return redirect()->route('casas_requerimientos_tramites', ['id_casa'=>$id_casa])->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea."));
            }
            return redirect()->back()
                ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
        }
        //If it doesn't we create it
        else {
            $casaRequerimientoTramite = $repository->create($id_casa, $id_requerimiento_tramite);

            if (! $casaRequerimientoTramite) {
                return redirect()->back()->withInput()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar enviar", "Operación errónea. Error enviando agregando el requerimiento."));
            }

            return redirect()->back()
                ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
        }
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
         * @var CasaRequerimientoTramite $casaRequerimientoTramite
         */
        $casaRequerimientoTramite= CasaRequerimientoTramite::find($id);
        if ($user->cannot('delete', $casaRequerimientoTramite)) {
            return redirect()->route('casas_requerimientos_tramites', ['id_proyecto'=>$id])->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }

        if (! $casaRequerimientoTramite->delete()) {
            return redirect()->route('casas_requerimientos_tramites', ['id_proyecto'=>$id])->with('alert', Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea."));
        }
        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
    }
}
