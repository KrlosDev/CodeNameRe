<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use App\Models\ClienteCasa;
use App\Models\Cliente;
use App\Models\Casa;
use App\Models\EjecutivoBancos;
use App\Models\EjecutivoVentas;
use App\Models\Funciones;
use Illuminate\Support\Facades\Auth;

class ClienteCasaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();

        if ($user->cannot('store', ClienteCasa::class)) {
            return redirect()->back()->with('alert', Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Usuario no autorizado."));
        }

        $clientecasa=new ClienteCasa();
        $clientecasa->id_cliente=$request->clientanadir;
        $clientecasa->id_casa= $request->casaid;
        if ($request->id_ej_bancos) {
            $ejBanco = EjecutivoBancos::find($request->id_ej_bancos);
            if ($ejBanco) {
                $clientecasa->id_ej_bancos = $ejBanco->id;
            }
        }
        if (! $clientecasa->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar agregar", "Operación errónea. Erro asignando la casa."));
        }

        /**
         * @var Casa $house
         */
        $house=Casa::find($request->casaid);
        $casasEstados = $house->broker->casasEstados;
        $house->id_casa_estado = $casasEstados->count() > 0 ? $casasEstados->first()->id : null;
        if ($user->isEjVentas()) {
            $house->id_ej_ventas=$user->ejecutivoVentas->id;
        }
        if ($user->isBroker() && $request->id_ej_ventas) {
            $ejVenta = EjecutivoVentas::find($request->id_ej_ventas);
            if ($ejVenta) {
                $house->id_ej_ventas = $ejVenta->id;
            }
        }
        if (! $house->save()) {
            return redirect()->back()->withInput()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar Asignar", "Operación errónea. Error asignando la casa."));
        }

        return redirect()->back()
            ->with("alert", Funciones::getAlert("success", "Casa añadida exitosamente", "Operación exitosa."));
    }

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
        $clientecasa = ClienteCasa::where("clientes_casas.id_cliente", $id)->first();
        
        if (! $clientecasa || $user->cannot('get', $clientecasa)) {
            return json_encode([]);
        }
        return json_encode($clientecasa);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        /**
         * @var User $user
         */
        $user=Auth::user();
        
        $clientecasa=ClienteCasa::where('id_casa', $request->casaquitar)->first();
        $cliente=Cliente::find($clientecasa->id_cliente);
        /**
         * @var Casa $casa
         */
        $casa=Casa::find($clientecasa->id_casa);

        if ($user->cannot('delete', $clientecasa)) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar desasignar", "Operación errónea. Usuario no autorizado."));
        }
      
        if (! $casa) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar desasignar", "La seleccionada casa no existe."));
        }
        
        if (! $clientecasa) {
            return redirect()->back()
                ->with("alert", Funciones::getAlert("danger", "Error al intentar desasignar", "El casa o cliente no encontrado."));
        }

        if ($clientecasa->delete()) {
            $casasEstados = $casa->broker->casasEstados;
            $casa->id_casa_estado = null;
            $casa->save();

            $pagos=$cliente->pago()->get();

            foreach ($pagos as $pago) {
                $pago->delete();
            }

            return redirect()->back()
                    ->with("alert", Funciones::getAlert("success", "Casa desasignada exitosamente", "Operación exitosa."));
        } else {
            return redirect()->back()
            ->with("alert", Funciones::getAlert("danger", "Error al intentar desasignar", "Ha ocurrido un error efectuando la operación."));
        }
    }
    
    public function getCasasCliente($id)
    {
        $cliente = Cliente::find($id);
        $clienteCasas = $cliente->clienteCasa()->get();
            
        $datos = [];
        $i = 0;
        foreach ($clienteCasas as $clienteCasa) {
            if ($clienteCasas[$i]->deleted_at == null) {
                $a=["id_casa"=> $clienteCasas[$i]->id,"codigo_casa"=> $clienteCasas[$i]->codigo];
                array_push($datos, $a);
                $i = $i + 1;
            }
        }
        return json_encode($datos);
    }
}
