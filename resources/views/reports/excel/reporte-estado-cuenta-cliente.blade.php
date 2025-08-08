<?php

use App\Models\Configuracion;

if (! isset($user)) {
    $user = \Auth::user();
}

$configuracion_moneda = Configuracion::where('descripcion', Configuracion::CONFIGURACION_MONEDA)->first();
?>

<!-- Button trigger modal -->
<!-- <button type="button" class="btn btn-primary btn-lg" data-toggle="modal" data-target="#myModal" data-backdrop="true">
  Launch demo modal
</button>
 -->

<div class="row">
<div class="col-md-12 ">
    <div class="card">
        <div class="content">
            <div class="row">
                <div class="col-xs-12 margin-top">
                    <h3>Balance</h3>
                    <div class="table-responsive">
                        <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                            <thead>
                                <tr>
                                    <th>Descripción</th>
                                    @foreach($tipos_pago as $key => $value)
                                    <th>{{$value}}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
<!--                                 <tr>
                                    <td>Por pagar</td>
                                    @foreach($pagos_por_pagar as $key => $value)
                                    <td>{{$value}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Pagado</td>
                                    @foreach($pagos_efectuados as $key => $value)
                                    <td>{{$value}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Pendiente</td>
                                    @foreach($pagos_pendientes as $key => $value)
                                    <td>{!!\App\Models\Funciones::colorearSaldoExacto($pagos_por_pagar[$key] - $pagos_efectuados[$key],0)!!}</td>
                                    @endforeach
                                </tr> -->

                                <tr>
                                    <td>Por pagar</td>
                                    
                                    @if(count($pagos_por_pagar) > 0)
                                        <td>{{$pagos_por_pagar[2]}}</td>
                                        <td>{{$pagos_por_pagar[1]}}</td>
                                        <td>{{$pagos_por_pagar[3]}}</td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>Pagado</td>
                                    @foreach($pagos_efectuados as $key => $value)
                                    <td>{{$value}}</td>
                                    @endforeach
                                </tr>
                                <tr>
                                    <td>Pendiente</td>
                                   @if(count($pagos_por_pagar) > 0)
                                        <td>{!!($pagos_por_pagar[2] - $pagos_efectuados[1])!!}</td>
                                        <td>{!!($pagos_por_pagar[1] - $pagos_efectuados[2])!!}</td>
                                        <td>{!!($pagos_por_pagar[3] - $pagos_efectuados[3])!!}</td>
                                    @endif
                                </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="col-xs-12 margin-top">
                    <h3>Pagos</h3>
                    <div class="table-responsive">
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
                            @if(count($pagos) == 0)
                                <tr>
                                    <td colspan="4">No se han registrado pagos...</td>
                                </tr>
                            @else
                                @foreach($pagos as $pago)
                                <?php $datetime = \App\Models\Funciones::createDateTimeObject($pago->realizado_at, 'Y-m-d'); ?>
                                <tr>
                                    <td>{{$pago->cliente->nombre." ".$pago->cliente->apellido}}</td>
                                    <td>{{$tipos_pago[$pago->id_tipo_transaccion]}}</td>
                                    <td>{{$pago->monto.$configuracion_moneda->contenido}}</td>
                                    <td>{{$datetime->format('d/m/Y')}}</td>
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
</div>
