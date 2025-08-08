<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Casa;
use App\Models\FormaPago;
use App\Models\Funciones;
use App\Models\Pago;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

class PagoController extends Controller
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
        $pago = Pago::find($id);
        if (! $pago || $user->cannot('get', $pago)) {
            return Funciones::return403();
        }

        $casa = Casa::find($pago->id_casa);
        $data = [];
        array_push($data, $pago, $casa->codigo);

        return json_encode($data);
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
        if ($user->cannot('getAll', Pago::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar obtener", "Operación errónea. Usuario no autorizado."));
        }

        $broker = $user->getBroker();
        $pagos = Pago::whereHas('casa', function ($query) use ($broker) {
            $query->where('id_broker', $broker->id);
        })->paginate(10);

        return view('pages.pago.index')->with('pagos', $pagos);
    }

    /**
     * @param $id
     * @return $this|\Illuminate\Http\RedirectResponse
     */
    public function getPagosCliente($id)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        if ($user->cannot('getAll', Pago::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar obtener", "Operación errónea. Usuario no autorizado."));
        }

        $broker = $user->getBroker();
        $pagos = Pago::with('cliente')->where('id_cliente', '=', $id)
            ->whereHas('casa', function ($query) use ($broker) {
                $query->where('id_broker', $broker->id);
            })
            ->latest()
            ->paginate(10);

        $client=Cliente::find($id);

        return view('pages.pago.index')->with('pagos', $pagos)->with('client', $client);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user=Auth::user();
        if ($user->cannot('store', Pago::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $casa = Casa::find($request->casapago);
        $montoTotal = 0;

        $cliente = Cliente::find($request->get('id_cliente'));

        if (! $casa || $cliente || ! array_key_exists($request->get('id_tipo_transaccion'), Pago::$formas_pago)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Los datos ingresados son inválidos."));
        }


        if ($request->id_tipo_transaccion == Pago::$MONTO_INICAL) {
            /*$pagos = Pago::where('id_tipo_transaccion', Pago::$MONTO_INICAL)
                ->where('id_casa', $casa->id)
                ->get();

            foreach ($pagos as $pag) {
                $montoTotal = $montoTotal + $pag->monto;
            }*/

            $montoTotal = Pago::where('id_tipo_transaccion', Pago::$MONTO_INICAL)
                ->where('id_casa', $casa->id)
                ->sum('monto');

            if ($montoTotal >= $casa->monto_abono_inicial) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Monto Inicial cancelado en su totalidad."));
            }

            if ($montoTotal+$request->monto > $casa->monto_abono_inicial) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Pago ingresado: ".$request->monto." Por pagar: ".$casa->abonoInicialFaltante()));
            }
        } elseif ($request->id_tipo_transaccion == Pago::$MONTO_SEPARACION) {
            /*$pagos = Pago::where('id_tipo_transaccion', Pago::$MONTO_SEPARACION)
                ->where('id_casa', $casa->id)
                ->get();

            foreach ($pagos as $pag) {
                $montoTotal = $montoTotal + $pag->monto;
            }*/

            $montoTotal = Pago::where('id_tipo_transaccion', Pago::$MONTO_SEPARACION)
                ->where('id_casa', $casa->id)
                ->sum('monto');
                
            if ($montoTotal >= $casa->monto_separacion) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Monto de Separación cancelado en su totalidad."));
            }
                
            if ($montoTotal+$request->monto > $casa->monto_separacion) {
                return redirect()->back()
                                 ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Pago ingresado:".$request->monto." Por pagar: ".$casa->separacionFaltante()));
            }
        } elseif ($request->id_tipo_transaccion == Pago::$MTS_ADICIONALES) {
            if ($casa->mts2_adicionales != 0) {
                /*$pagos = Pago::where('id_tipo_transaccion', Pago::$MTS_ADICIONALES)
                    ->where('id_casa', $casa->id)
                    ->get();

                foreach ($pagos as $pag) {
                    $montoTotal = $montoTotal + $pag->monto;
                }*/

                $montoTotal = Pago::where('id_tipo_transaccion', Pago::$MTS_ADICIONALES)
                    ->where('id_casa', $casa->id)
                    ->sum('monto');
                
                if ($montoTotal >= $casa->monto_mts2_adicional*$casa->mts2_construccion) {
                    return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Monto de Metros adicionales cancelado en su totalidad."));
                }
              
                if ($montoTotal+$request->monto > ($casa->monto_mts2_adicional*$casa->mts2_construccion)) {
                    return redirect()->back()
                                 ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Pago ingresado:".$request->monto." Por pagar: ".$casa->mtsFaltante()));
                }
            } else {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Esta propiedad no posee Metros Cuadrados Adicionales"));
            }
        }

        $pago = new Pago();
        $pago->id_cliente = $request->cl_id;
        $pago->id_casa = $request->casapago;
        $pago->id_forma_pago = $request->id_forma_pago;
        $pago->descripcion = $request->descripcion;
        $pago->id_tipo_transaccion = $request->id_tipo_transaccion;
        $pago->monto = $request->monto;
        $pago->realizado_at = $request->realizado_at;

        if (! $pago->save()) {
            return redirect()->route('pagos')->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error creando el usuario."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Agregado exitosamente", "Operación exitosa."));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        /**
         * @var User $user
         */
        $user = Auth::user();
        /**
         * @var Pago $pago
         */
        $pago = Pago::find($id);

        if (! $pago) {
            return redirect()->route('pagos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "El Pago ingresado no existe."));
        }
        if ($user->cannot('update', $pago)) {
            return redirect()->route('pagos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Operación errónea. Usuario no autorizado."));
        }
        if ($request->get('id_tipo_transaccion') && ! array_key_exists($request->get('id_tipo_transaccion'), Pago::$tipos_pago)
            || $request->get('id_forma_pago') && ! FormaPago::find($request->get('id_forma_pago'))) {
            return redirect()->route('pagos')
                ->with("alert", Funciones::getAlert("danger", "Error al intentar editar", "Los datos ingresados son inválidos."));
        }

        $casa = Casa::find($pago->id_casa);
        $montoTotal = 0;
         
        if ($request->id_tipo_transaccion == Pago::$MONTO_INICAL) {
            $pags = Pago::where('id_tipo_transaccion', Pago::$MONTO_INICAL)
                ->where('id_casa', $casa->id)
                ->where('id', '!=', $pago->id)
                ->get();

            foreach ($pags as $pag) {
                $montoTotal = $montoTotal + $pag->monto;
            }

            if ($montoTotal >= $casa->monto_abono_inicial) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Monto Inicial cancelado en su totalidad."));
            }

            if ($montoTotal+$request->monto > $casa->monto_abono_inicial) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Pago ingresado: ".$request->monto." Por pagar: ".$casa->abonoInicialFaltante()));
            }
        } elseif ($request->id_tipo_transaccion == Pago::$MONTO_SEPARACION) {
            $pags = Pago::where('id_tipo_transaccion', Pago::$MONTO_SEPARACION)
                ->where('id_casa', $casa->id)
                ->where('id', '!=', $pago->id)
                ->get();
              
            foreach ($pags as $pag) {
                $montoTotal = $montoTotal + $pag->monto;
            }
                
            if ($montoTotal >= $casa->monto_separacion) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Monto de Separación cancelado en su totalidad."));
            }
                
            if ($montoTotal+$request->monto > $casa->monto_separacion) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Pago ingresado:".$request->monto." Por pagar: ".$casa->separacionFaltante()));
            }
        } elseif ($request->id_tipo_transaccion == Pago::$MTS_ADICIONALES) {
            $pags = Pago::where('id_tipo_transaccion', Pago::$MTS_ADICIONALES)
                ->where('id_casa', $casa->id)
                ->where('id', '!=', $pago->id)
                ->get();

            foreach ($pags as $pag) {
                $montoTotal = $montoTotal + $pag->monto;
            }
                
            if ($montoTotal >= $casa->monto_separacion) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Monto de Metros adicionales cancelado en su totalidad."));
            }
                
            if ($montoTotal+$request->monto > $casa->monto_mts2_adicional) {
                return redirect()->back()
                    ->with("alert", Funciones::getAlert("danger", "Error al intentar cargar el pago", "Pago ingresado:".$request->monto." Por pagar: ".$casa->mtsFaltante()));
            }
        }

        if ($request->get('id_forma_pago')) {
            $pago->id_forma_pago = $request->id_forma_pago;
        }
        if ($request->get('descripcion')) {
            $pago->descripcion = $request->descripcion;
        }
        if ($request->get('id_tipo_transaccion')) {
            $pago->id_tipo_transaccion = $request->id_tipo_transaccion;
        }
        if ($request->get('monto')) {
            $pago->monto = $request->monto;
        }
        if ($request->get('realizado_at')) {
            $pago->realizado_at = $request->realizado_at;
        }

        if (! $pago->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Error actualizando el pago."));
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
        $pago = Pago::find($id);
        if (! $pago) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "El pago seleccionado no existe."));
        }
       
        if ($user->cannot('delete', $pago)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Operación errónea. Usuario no autorizado."));
        }
       
        if ($pago->delete()) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("success", "Eliminado exitosamente", "Operación exitosa."));
        }
       
        return redirect()->back()
            ->with("alert", Funciones::getAlert("danger", "Error al intentar eliminar", "Ha ocurrido un error efectuando la operación."));
    }
}
