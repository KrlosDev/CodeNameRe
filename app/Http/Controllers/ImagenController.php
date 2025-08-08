<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use App\Models\Imagen;
use App\Http\Requests\ImagenForm;
use App\Models\Funciones;
use Illuminate\Database\Query\Builder;

class ImagenController extends Controller
{
    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get($id)
    {
        $imagen = Imagen::find($id);
        if (! $imagen) {
            return json_encode([]);
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', $imagen)) {
            return json_encode([]);
        }
        
        return json_encode($imagen);
    }

    /**
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function getAll()
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Imagen::class)) {
            return redirect()->route('/')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea, Usuario no autorizado."));
        }
        
        $cantidad = filter_input(INPUT_GET, 'imagenes', FILTER_SANITIZE_NUMBER_INT);
        /**
         * @var Builder $imagenes
         */
        $imagenes = Imagen::orderBy('imagenes.id', 'desc');
        
        return view('pages.imagen.index')->with('imagenes', $imagenes->paginate($cantidad));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  ImagenForm  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ImagenForm $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('store', Imagen::class)) {
            return redirect()->route('imagenes')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea, Usuario no autorizado."));
        }
        
        $codigo = Imagen::generarNuevoCodigo().".".Funciones::getExtension($request->file('archivo')->getClientOriginalName());
        
        $path = base_path().Imagen::PATH_PUBLIC.'/';
        $request->file('archivo')->move($path, $codigo);
        
        $imagen=new Imagen();
        $imagen->descripcion=$request->descripcion;
        $imagen->nombre=$request->nombre;
        $imagen->path=$codigo;
        if ($imagen->save()) {
            return redirect()->route('imagenes')
                ->with("alert", Funciones::getAlert("success", "Agregado Exitosamente", "Operación Exitosa."));
        }
        
        return redirect()->route('imagenes')->withInput()
            ->with("alert", Funciones::getAlert("danger", "Error al Intentar Agregar", "Operación Errónea al intentar crear la imagen."));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  ImagenForm  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ImagenForm $request, $id)
    {
        $imagen = Imagen::find($id);
        if (! $imagen) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Actualizar", "Operación Errónea, La imagen no existe."));
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', $imagen)) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Actualizar", "Operación Errónea, Usuario no autorizado."));
        }
        
        $imagen->descripcion=$request->descripcion;
        $imagen->nombre=$request->nombre;
        if ($imagen->save()) {
            return redirect()->back()
                    ->with("alert", Funciones::getAlert("success", "Actualizado Exitosamente", "Operación Exitosa."));
        }
        return redirect()->back()->withInput()
            ->with("alert", Funciones::getAlert("danger", "Error al Intentar Actualizar", "Operación Errónea al intentar actualizar la imagen."));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete($id)
    {
        $imagen = Imagen::find($id);
        if (! $imagen) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Actualizar", "Operación Errónea, La imagen no existe."));
        }
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('get', $imagen)) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al Intentar Actualizar", "Operación Errónea, Usuario no autorizado."));
        }
        
        unlink(base_path().Imagen::PATH_PUBLIC.'/'.$imagen->path);
        $imagen->proyecto()->dettach($imagen);
        if ($imagen->delete()) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación Exitosa."));
        }
        return redirect()->back()->withInput()
            ->with("alert", Funciones::getAlert("danger", "Error al Intentar Eliminar", "Operación Errónea al intentar actualizar la imagen."));
    }
}
