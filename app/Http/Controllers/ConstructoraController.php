<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\User;
use App\Models\Constructora;
use App\Models\Licencia;
use App\Http\Requests\ConstructoraForm;
use App\Models\Funciones;

class ConstructoraController extends Controller
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
        if ($user->cannot('get', Constructora::class)) {
            return Funciones::return403();
        }

        return json_encode(Constructora::with(['licencia','user'])->where("constructoras.id", $id)->first());
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
        if ($user->cannot('getAll', Constructora::class)) {
            return redirect()->route('/')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación errónea, Usuario no autorizado."));
        }

        $constructoras = Constructora::with(['licencia','user'])->paginate(10);

        return view('pages.constructora.index')->with('constructoras', $constructoras);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ConstructoraForm $request
     * @return \Illuminate\Http\Response
     */
    public function store(ConstructoraForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        if ($user->cannot('store', Constructora::class)) {
            return redirect()->route('constructoras')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación errónea, Usuario no autorizado."));
        }

        $usuario=new User();
        $usuario->name=$request->name;
        $usuario->nombre=$request->nombre;
        $usuario->email=$request->email;
        $usuario->id_rol=User::CONSTRUCTORA;
        $usuario->password=bcrypt($request->password);
        if (! $usuario->save()) {
            return redirect()->route('constructoras')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación errónea creando el usuario."));
        }
        
        $licencia=new Licencia();
        $licencia->fecha_vencimiento=$request->fecha_vencimiento;
        $licencia->fecha_suspension=$request->fecha_suspension;

        if (! $licencia->save()) {
            return redirect()->route('constructoras')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación errónea creando la licencia."));
        }
        $constructora=new Constructora();
        $constructora->max_brokers=$request->max_brokers;
        $constructora->max_ejecutivos_ventas=$request->max_ejecutivos_ventas;
        $constructora->max_ejecutivos_bancos=$request->max_ejecutivos_bancos;
        $constructora->id_licencia=$licencia->id;
        $constructora->id_user=$usuario->id;
        if ($constructora->save()) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("success", "Agregado Exitosamente", "Operación exitosa."));
        }
        
        return redirect()->route('constructoras')->withInput()
            ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación errónea al intentar crear la constructora."));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  ConstructoraForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ConstructoraForm $request, $id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        /**
         * @var Constructora $constructora
         */
        $constructora=Constructora::find($id);

        if ($user->cannot('update', $constructora)) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }

        /**
         * @var User $usuario
         */
        $usuario=User::find($constructora->id_user);

        if ($request->get('password')!='') {
            $usuario->password=bcrypt($request->password);
        }

        $usuario->nombre=$request->nombre;
        $usuario->email=$request->email;

        if (! $usuario->save()) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }

        /**
         * @var Licencia $licencia
         */
        $licencia=Licencia::find($constructora->id_licencia);
        $licencia->fecha_vencimiento=$request->fecha_vencimiento;
        $licencia->fecha_suspension=$request->fecha_suspension;

        if (! $licencia->save()) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }

        $licencia->chequearLicencia();
        $constructora->max_brokers=$request->max_brokers;
        $constructora->max_ejecutivos_ventas=$request->max_ejecutivos_ventas;
        $constructora->max_ejecutivos_bancos=$request->max_ejecutivos_bancos;
        if (! $constructora->save()) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea."));
        }

        return redirect()->route('constructoras')
            ->with("alert", Funciones::getAlert("success", "Editado exitosamente", "Operación exitosa."));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\Response
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
         * @var Constructora $constructora
         */
        $constructora = Constructora::find($id);

        if ($user->cannot('delete', $constructora)) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }

        if (! $constructora) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "La constructora ingresada no existe."));
        }
        /**
         * @var User $usuario
         */
        $usuario = User::find($constructora->id_user);
        if (! $usuario->delete()) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un problema eliminando"));
        }
        if (! $constructora->delete()) {
            return redirect()->route('constructoras')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un problema eliminando"));
        }

        return redirect()->route('constructoras')
            ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
    }
}
