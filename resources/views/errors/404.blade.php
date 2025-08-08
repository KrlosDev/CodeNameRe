<?php

/**
 * Description of 404
 *
 * @author Luis Valencia <luisvalencia@vccodes.com.ve>
 * @company VC Codes <contacto@vccodes.com.ve>
 */
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Mi Hogar</title>

        <link href="https://fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                color: #616161;
                display: table;
                font-weight: 500;
                font-family: 'Lato', sans-serif;
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 48px;
                margin-bottom: 40px;
            }
            .danger{
                color:firebrick;
                font-weight: bolder;
            }
            .volver{
                font-style: none;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title"><a class='danger'>¡Ups!</a><br>Parece que la dirección que has ingresado es inválida.</div>
                <div clasS="title"><a href="{{url('/')}}" class="volver">Volver</a></div>
            </div>
        </div>
    </body>
</html>
