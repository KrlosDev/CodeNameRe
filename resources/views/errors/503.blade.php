<?php

/**
 * Description of 503
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
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title"><a class='danger'>¡Vuelve pronto!</a><br>El servidor actualmente no se encuentra en funcionamiento.</div>
            </div>
        </div>
    </body>
</html>
