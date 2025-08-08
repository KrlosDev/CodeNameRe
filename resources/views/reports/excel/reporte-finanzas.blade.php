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
                             <div>
                                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                        <thead>
                                            <tr>
                                                <th>Nombre</th>
                                                <th>Concepto</th>
                                                <th>Monto</th>
                                                <th>Fecha de pago</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($pagos) > 0)
                                            @foreach($pagos as $pago)
                                            <?php $datetime = \App\Models\Funciones::createDateTimeObject($pago->realizado_at, 'Y-m-d'); ?>
                                            <tr>
                                                <td>{{$pago->cliente->nombre." ".$pago->cliente->apellido}}</td>
                                                <td>{{$tipos_pago[$pago->id_tipo_transaccion]}}</td>
                                                @if($busqueda_agrupar)
                                                <td>{{$pago->{'total_'.\App\Models\Pago::$tipos_pago_asociacion[$pago->id_tipo_transaccion]}.$configuracion_moneda->contenido}}</td>
                                                @else
                                                <td>{{$pago->monto.$configuracion_moneda->contenido}}</td>
                                                @endif
                                                <td>{{$datetime->format('d/m/Y')}}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="4">No se han registrado pagos...</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                    </table>
                            </div>
                            <div class="col-lg-12 margin-top">
                                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                        <thead>
                                            <tr>
                                                <th>Concepto</th>
                                                <th>Monto</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @if(count($pagos) > 0)
                                            @foreach($totales_por_concepto as $key => $value)
                                            <tr>
                                                <td>{{$tipos_pago[$key]}}</td>
                                                <td>{{$value.$configuracion_moneda->contenido}}</td>
                                            </tr>
                                            @endforeach
                                            <tr>
                                                <td colspan="1"></td>
                                                <th>Total</th>
                                            </tr>
                                            <tr>
                                                <td colspan="1"></td>
                                                <td>{{array_sum(array_values($totales_por_concepto)).$configuracion_moneda->contenido}}</td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td colspan="2">No se han encontrado pagos...</td>
                                            </tr>
                                        @endif
                                        </tbody>
                                </div>
    </body>
</html>