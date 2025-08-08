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

    <title>Información de Cliente</title>
    <style>
        td{
            padding: 0px 0px 0px 20px!important;
/*            border: solid 1px #000;*/
        }
        td p{
            margin: 0px;
        }
        
    </style>
    
  </head>
  <body>
        <div id="details" class="clearfix">
          <div id="invoice">
              <div class="col-xs-9">
                  <h3 style="padding-top:0;margin-top:0;">
                    @if(count($data->clienteCasa)>0)
                        @foreach($data->clienteCasa as $i=>$casa)
                            @if($i!=0)
                            ,
                            @endif
                            <a>{{ $casa->broker->user->nombre }}</a>
                        @endforeach
                    @else
                    Ninguno
                    @endif
                  </h3>
              </div>
              <div class="col-xs-3">
                  <div class="date">Fecha: {{ $date }}</div>
              </div>
          </div>
        </div>
      
    <div class="col-xs-12" style="margin-top:40px;padding-top:40px;">
    <h3>Datos del Cliente</h3>
    <table class="table table-striped">

        <tbody>

            <tr>
                <td><p><b>Nombres:</b> {{ $data->nombre }}</p></td>
              <td><p><b>Apellidos:</b> {{ $data->apellido }}</p></td>
            </tr>
            <tr>
              <td><p><b>Correo:</b> {{ $data->email }}</p></td>
              <td></td>
            </tr>

            <tr>
              <td><p><b>Identificación:</b> {{ $data->identificacion }}</p></td>            
              <td><p><b>Teléfono Personal:</b> 
                      @foreach($data->telefonoCliente as $telefono) {{ $telefono->telefono }} @endforeach
                  </p>
              </td>
            </tr>


            <tr>
              <td><p><b>Fecha de Nacimiento:</b> {{ $data->fecha_nacimiento }}</p></td>
              <td><p><b>Estado Civil:</b> {{ $data->getNombreCivil($data->estado_civil) }}</p></td>           
            </tr>

            <tr>
              <td><p><b>País :</b> {{ $data->pais->nombre }}</p></td>
              <td><p><b>Distrito:</b> {{ $data->distrito->nombre }}</p></td>
            </tr>

            <tr>
              <td colspan="2"><p><b>Dirección:</b> {{ $data->direccion }}</p></td>

            </tr>

            <tr>          
              <td><p><b>Casa/Apto:</b> {{ $data->getNombreVivienda($data->casa_apartamento) }}</p></td>
              <td></td>
            </tr>


            <tr>
              <td colspan="2"><p style="font-size:18px;"><b>Datos Laborales:</b></p></td>            
            </tr>

            <tr>
              <td><p><b>Tipo de Trabajo:</b> {{ $data->getNombreTrabajo($data->tipo_trabajo) }}</p></td>
              <td><p><b>Salario:</b> {{ $data->salario }}</p></td>
            </tr>

          @if($data->tipo_trabajo != 1))
            <tr>
              <td><p><b>Empresa:</b> {{ $data->empresa }}</p></td>
              <td><p><b>Cargo:</b> {{ $data->cargo_empresa }}</p></td>
            </tr>

            <tr>
              <td><p><b>Años Laborando:</b> {{ $data->anios_laborando }}</p></td>
              <td><p><b>Email:</b> {{ $data->email_empresa }}</p></td>
            </tr>

            <tr>
              <td colspan="2"><p><b>Dirección  de Empresa:</b> {{ $data->direccion_empresa }}</p></td>            
            </tr>

            <tr>
              <td><p><b>Teléfono de Empresa:</b> {{ $data->telefonos_empresa }}</p></td>            
            </tr>

          @endif


        </tbody>
    </table>
    
       <br>
       <h3>Referencias personales</h3>
       <table class="table table-striped">
           <tbody>
                @foreach($data->referenciasPersonales as $referencia)
                    @if($referencia->id)
                    <tr>
                        <td><p><b>Nombre:</b> {{ $referencia->nombre }}</p></td>
                        <td><p><b>Parentesco:</b> {{ $referencia->parentesco }}</p></td>
                    </tr>
                    <tr>
                        <td><p><b>Teléfono:</b> {{ $referencia->telefono }}</p></td>
                        <td><p><b></b></p></td>
                    </tr>
                    <tr>
                     @endif
                @endforeach
           </tbody>
       </table>
    
       <br>
    @if(count($data->codeudor))
       <h3>Datos del Codeudor</h3>
    @foreach($data->codeudor as $codeudor)
       
    <table class="table table-striped">
 
        <tbody>

          <tr>
              <td><p><b>Nombres:</b> {{ $codeudor->nombre }}</p></td>
            <td><p><b>Apellidos:</b> {{ $codeudor->apellido }}</p></td>
          </tr>
          <tr>
            <td><p><b>Correo:</b> {{ $codeudor->email }}</p></td>
            <td></td>
          </tr>

          <tr>
            <td><p><b>Identificación:</b> {{ $codeudor->identificacion }}</p></td>            
            <td></td>
          </tr>


          <tr>
            <td><p><b>Fecha de Nacimiento:</b> {{ $codeudor->fecha_nacimiento }}</p></td>
            <td><p><b>Estado Civil:</b> {{ $codeudor->getNombreCivil($codeudor->estado_civil) }}</p></td>           
          </tr>

          <tr>
            <td><p><b>País :</b> {{ $codeudor->pais->nombre }}</p></td>
            <td><p><b>Distrito:</b> {{ $codeudor->distrito->nombre }}</p></td>
          </tr>

          <tr>
            <td colspan="2"><p><b>Dirección:</b> {{ $codeudor->direccion }}</p></td>
            
          </tr>

          <tr>          
            <td><p><b>Casa/Apto:</b> {{ $codeudor->getNombreVivienda($codeudor->casa_apartamento) }}</p></td>
            <td></td>
          </tr>

          <tr>
            <td colspan="2"><p style="font-size:18px;"><b>Datos Laborales:</b></p></td>            
          </tr>

          <tr>
            <td><p><b>Tipo de Trabajo:</b> {{ $codeudor->getNombreTrabajo($codeudor->tipo_trabajo) }}</p></td>
            <td><p><b>Salario:</b> {{ $codeudor->salario }}</p></td>
          </tr>


          @if($codeudor->tipo_trabajo != 1))
          <tr>
            <td><p><b>Empresa:</b> {{ $codeudor->empresa }}</p></td>
            <td><p><b>Cargo:</b> {{ $codeudor->cargo_empresa }}</p></td>
          </tr>

          <tr>
            <td><p><b>Años Laborando:</b> {{ $codeudor->anios_laborando }}</p></td>
            <td><p><b>Email:</b> {{ $codeudor->email_empresa }}</p></td>
          </tr>

          <tr>
            <td colspan="2"><p><b>Dirección  de Empresa:</b> {{ $codeudor->direccion_empresa }}</p></td>            
          </tr>

          <tr>
            <td><p><b>Teléfono de Empresa:</b> {{ $codeudor->telefonos_empresa }}</p></td>            
          </tr>

        @endif


        </tbody>
      </table>
    @endforeach
       @endif
    
    </div>
  </body>
</html>