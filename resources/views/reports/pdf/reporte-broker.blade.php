<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <!-- Bootstrap core CSS     -->
        <link href="{{url('assets/css/bootstrap.min.css')}}" rel="stylesheet" />
        <!-- Animation library for notifications   -->
        <link href="{{url('assets/css/animate.min.css')}}" rel="stylesheet"/>
        <!--  Light Bootstrap Table core CSS    -->
        <link href="{{url('assets/css/light-bootstrap-dashboard.css')}}" rel="stylesheet"/>
        <!--     Fonts and icons     -->
        <link href="{{url('css/app1.css')}}" rel="stylesheet" />
        <title>Reporte de Ganancias Broker</title>
        <style>
            td {
                padding: 0px 0px 0px 20px!important;
            }
            td p {
                margin: 0px;
            }
            h4 {
                font-size: 18px;
                margin: 2px 0px 2px 0px;
                padding: 2px 0px 2px 0px;
            }
        </style>
    </head>
    <body>
        <div class="row">
            <div class="col-md-12 ">
                <div class="card">
                    <h4>Proyecto: {{$proyecto->nombre}}</h4>
                    <div class="content">
                        <div class="row">
                            <div class="col-lg-12 no-padding">
                                <div class="col-lg-6 no-padding">
                                    <h4>Reporte</h4>
                                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                        <thead>
                                            <tr>
                                                <th>Descripción</th>
                                                <th>Cantidad</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr>
                                                <td>Propiedades del Proyecto</td>
                                                <td>{{$cantidad_casasproyecto}}</td>
                                            </tr>
                                            <tr>
                                                <td>Propiedades Asignadas</td>
                                                <td>{{$cantidad_casasasignadasbroker}}</td>
                                            </tr>
                                             <tr>
                                                <td>Propiedades Disponibles</td>
                                                <td>{{$cantidad_casas_libres}}</td>
                                            </tr>
                                            <tr>
                                                <td>Propiedades Ocupadas</td>
                                                <td>{{$cantidad_casasocupadas}}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <br>
                                    <h4>Propiedad</h4>
                                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                        <thead>
                                            <tr>
                                                <th>Habitaciones</th>
                                                <th>Baños</th>
                                                <th>Mts2</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(true)
                                            <tr>
                                                <td>{{$modelo->recamaras}}</td>
                                                <td>{{$modelo->banos}}</td>
                                                <td>{{$modelo->mts2_total}}</td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td colspan="2">Este proyecto no tiene propiedades actualmente...</td>
                                            </tr>
                                            @endif
                                        </tbody>
                                    </table>
                                    <br>
                                    <h4>Ingresos</h4>
                                    <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                        <thead>
                                            <tr>
                                                <th>Tipo</th>
                                                <th>Total</th>
                                                <th>Recibido </th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if(true)
                                            <tr>
                                                <td>{{'Total Abono Inicial'}}</td>
                                                <td>{{$abonoinicial_total}}</td>
                                                <td>{{$abonoinicial_recibido}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{'Total Metros Adicionales'}}</td>
                                                <td>{{$mts2_total}}</td>
                                                <td>{{$mts2_recibidos}}</td>
                                            </tr>
                                            <tr>
                                                <td>{{'Total Reserva'}}</td>
                                                <td>{{$total_reserva}}</td>
                                                <td>{{$reserva_recibido}}</td>
                                            </tr>
                                            @else
                                            <tr>
                                                <td colspan="2">Este proyecto no tiene propiedades actualmente...</td>
                                            </tr>
                                            @endif

                                        </tbody>
                                    </table>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-lg-6">
                                <h4>Reporte por Trámite</h4>
                                <table class="table table-striped table-basic-striped table-hover table-basic-hover table-center">
                                    <thead>
                                        <tr>
                                            <th>Descripción</th>
                                            <th>Cantidad</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Sin asignar</td>
                                            <td>{{$totales['null']}}</td>
                                        </tr>
                                    @foreach($estados as $estado)
                                        <tr>
                                            <td>{{$estado->nombre}}</td>
                                            <td>{{$totales[$estado->id]}}</td>
                                        </tr>
                                    @endforeach
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