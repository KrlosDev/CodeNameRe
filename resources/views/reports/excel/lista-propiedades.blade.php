<?php

use App\Models\Configuracion;

if (! isset($user)) {
    $user = \Auth::user();
}

$configuracion_moneda = Configuracion::where('descripcion', Configuracion::CONFIGURACION_MONEDA)->first();
?>
<!DOCTYPE html>
<html lang="en">
    <body>
        <table class="table table-hover table-center">
            <thead>
                <tr>
                @if(Auth::user()->isConstructora())
                    <th> <input type="checkbox" onClick="toggle(this)" /><br/></th>
                @endif
                    <th>Código</th>
                    <!--th>Modelo</th-->
                    <th>Cliente</th>
                    <th>Telefono</th>
                    <th>Valor</th>
                    <th>Monto de Separación</th>
                    <th>Abono Inicial</th>
                    <th>Total de costo mts2 adicionales</th>
                    <th>Pagado</th>
                    <th>Por pagar</th>
                    <th>Status</th>
                @if(!Auth::user()->isBroker())
                    <th>Broker</th>
                @endif
                    </tr>
            </thead>
            <tbody>
                @if(count($casas) == 0)
                    <tr>
                        <td colspan="12">No se han encontrado resultados...</td>
                    </tr>
                @endif
                @foreach($casas as $casa)
                    <tr
                        @if($estados->first() && !($casa->id_casa_estado != $estados->first()->id))
                            class="success"
                        @else
                            class="danger"
                        @endif
                    >
                        @if(Auth::user()->isConstructora())
                        <td><input type="checkbox" name="cliente[]" id="cliente[]" value={{$casa->id}}></td>
                        @endif

                        <td>{{$casa->codigo}}</td>
                        <!--td>{{$casa->modelo}}</td-->
                        @if(count($casa->cliente)>0)
                        <td>{{$casa->cliente[0]->nombre}} {{$casa->cliente[0]->apellido}}</td>
                        <td>
                        @if(count($casa->cliente[0]->telefonoCliente)>0)
                            @foreach($casa->cliente[0]->telefonoCliente as $i=>$telefono)
                                @if($i!=0)
                                ,
                                @endif
                                {{$telefono->telefono}}
                            @endforeach
                        @else
                            Ninguno
                        @endif
                        </td>
                        @else
                        <td>Ninguno</td>
                        <td>Ninguno</td>
                        @endif

                        <td>{{$casa->valor}}</td>
                        <td>{{$casa->monto_separacion}}</td>
                        <td>{{$casa->monto_abono_inicial}}</td>
                        <td>{{$casa->monto_mts2_adicional*$casa->mts2_adicionales}}</td>
                        <td>{{$casa->getMontoPagadoTotalTabla([\App\Models\Pago::TIPO_PAGO_MONTO_INICIAL])}}</td>
                        <td>{{$casa->getMontoPorPagarTotalTabla([\App\Models\Pago::TIPO_PAGO_MONTO_INICIAL])}}</td>
                        <td>{{$casa->getEstadoNombre()}}</td>

                        @if(!Auth::user()->isBroker())

                        <td>
                            @if(!($casa->id_broker == null))
                                {{$casa->broker->user->nombre}}
                            @endif
                        </td>
                        @endif

                    </tr>
                @endforeach
            </tbody>
        </table>
    </body>
</html>