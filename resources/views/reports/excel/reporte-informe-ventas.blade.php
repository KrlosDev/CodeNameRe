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
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <div class="content">
                        <div class="row">
                            <div class="col-xs-12 margin-top">
                                <h4>Balance</h4>
                                <table>
                                    <thead>
                                        <tr>
                                            <th>Banco</th>
                                            <th>Casa</th>
                                            <th>Mts2 Totales</th>
                                            <th>Nombre</th>
                                            <th>Cédula</th>
                                            <th>Recámaras</th>
                                            <th>Estatus</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if(count($casas) == 0)
                                    <tr>
                                        <td colspan='7'>No se han encontrado resultados...</td>
                                    </tr>
                                    @else
                                    @foreach($casas as $casa)
                                    <tr>
                                        @if($casa->cliente()->first() && $casa->cliente()->first()->banco)
                                        <td>{{$casa->cliente()->first()->banco()->first()->nombre}}</td>
                                        @else
                                        <td>---</td>
                                        @endif
                                        <td>{{$casa->codigo}}</td>
                                        <td>{{$casa->mts2_total}}</td>
                                        @if($casa->cliente()->first())
                                        <td>{{$casa->cliente()->first()->nombre." ".$casa->cliente()->first()->apellido}}</td>
                                        <td>{{$casa->cliente()->first()->identificacion}}</td>
                                        @else
                                        <td>---</td>
                                        <td>---</td>
                                        @endif
                                        <td>{{$casa->recamaras}}</td>
                                        @if(array_key_exists($casa->id_casa_estado,$estados))
                                            <td>{{$estados[$casa->id_casa_estado]}}</td>
                                        @else
                                            <td>Sin asignar</td>
                                        @endif
                                    </tr>
                                    @endforeach
                                    @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>