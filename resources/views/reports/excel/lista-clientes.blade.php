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
        <table>
            <thead>
                <tr>
                <th>Casas</th>


                <th>Nombre</th>
                <th>Apellido</th>
                <th>Correo</th>
                <!-- <th>Identificación</th>   -->
                 <th>Telefono</th>
                <th>Estado</th>
                @if(!(Auth::user()->isEjVentas() || Auth::user()->isEjBancos()))
                <th title="Ejecutivo de Ventas">Ejecutivo de Ventas</th>
                <!-- <th>Status de Tramite</th> -->
                @endif
                </tr>
            </thead>
            <tbody>
            @if(count($clientes) == 0)
                <tr>
                    <td colspan="7">No se han encontrado resultados...</td>
                </tr>
            @endif
            @foreach($clientes as $cliente)
                <tr @if(count($cliente->clienteCasa)==0) class="danger" @endif>
                    <td>
                    @if(count($cliente->clienteCasa)!=0)
                        @foreach($cliente->clienteCasa as $i=>$house)
                            @if($i!=0)
                                ,
                            @endif
                            {{$house->codigo}}
                        @endforeach
                    @else
                        Ninguna
                    @endif
                    </td>

                    <td>{{$cliente->nombre}}</td>
                    <td>{{$cliente->apellido}}</td>
                    <td>{{$cliente->user->email}}</td>
                    <!-- <td>{{$cliente->identificacion}}</td> -->
                    <td>
                    @foreach($cliente->telefonoCliente as $telefono_cliente)
                        {{$telefono_cliente->telefono}}
                        <br>
                    @endforeach
                    </td>

                    <td>
                    @if(count($cliente->clienteCasa)!=0)
                        @foreach($cliente->clienteCasa as $i=>$house)
                            @if($i!=0)
                                ,
                            @endif
                            {{$house->getEstadoNombre()}}
                        @endforeach
                    @else
                        Ninguno
                    @endif
                    </td>

                    @if(!(Auth::user()->isEjVentas() || Auth::user()->isEjBancos()))
                    <td>
                        @if(count($cliente->clienteCasa)!=0)

                            @foreach($cliente->clienteCasa as $i=>$house)
                                @if($i!=0)
                                ,
                                @endif

                                @if(count($house->ejecutivoVentas) != 0)
                                {{$house->ejecutivoVentas->user->nombre}}
                                @else
                                Ninguno
                                @endif
                            @endforeach

                        @else
                            Ninguno
                        @endif
                    </td>
                    @endif
                </tr>

            @endforeach
            </tbody>
        </table>
    </body>
</html>