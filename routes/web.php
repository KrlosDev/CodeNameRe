<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/

//$router = app('web.router');

Route::get('/', function () {
    //return view('welcome');
    return redirect()->route("login");
})->name('/');

//Auth::routes();

Route::get('login', 'LoginController@getViewLogin')->name('login');
Route::post('login', 'LoginController@login');
Route::get('logout', 'LoginController@logout')->name('logout');
Route::post('logout', 'LoginController@logout');

Route::get('/home', 'HomeController@index');


Route::group(['middleware' => 'auth'], function () {
    Route::group(['prefix' => 'casas'], function () {
        Route::get('/{id}/desasignar', 'CasaController@desasignar');
        Route::get('/deCliente/{identificacion}', 'CasaController@getPropiedades');
        Route::get('/listar/{id}', 'CasaController@listModal');
        Route::get('/', 'CasaController@getAll')->name('casas');
        Route::get('/count', 'CasaController@count');
        Route::get('/{id}', 'CasaController@get');
        Route::get('/{id}/broker', 'CasaController@get');
        Route::post('/', 'CasaController@store');
        Route::get('/modelo_p/{id}', 'CasaController@getCasasModelo');

        Route::put('/{id}/broker', 'CasaController@updateBroker');
        Route::put('/{id}', 'CasaController@update');
        Route::delete('/{id}', 'CasaController@delete');
        Route::get('/proyecto/{id}', 'CasaController@getCasasProyecto');
        Route::post('/asignarEjecutivos', 'CasaController@asignarEjecutivos');
        Route::post('/asignarEstatus', 'CasaController@asignarEstatus');
        Route::get('/getEstatus/{id}', 'CasaController@getEstatus');
        Route::post('/eliminar', 'CasaController@eliminarCasas');

        Route::get('/broker_casas/{id}', 'CasaController@getAllbyBroker');
    });

    Route::group(['prefix' => 'proyectos'], function () {
        Route::get('/{id}/imagenes', 'ProyectoController@getViewPanelImagenes');
        Route::post('/{id}/subirImagen', 'ProyectoController@agregarImagen');
        Route::get('/{id}/propiedadesDisponibles', 'ProyectoController@getPropiedadesDisponibles');
        Route::post('/asignar', 'ProyectoController@asignarCasas');
        Route::get('/', 'ProyectoController@getAll')->name('proyectos');
        Route::get('/count', 'ProyectoController@count');
        Route::get('/{id}', 'ProyectoController@get');
        Route::post('/', 'ProyectoController@store');
        Route::put('/{id}', 'ProyectoController@update');
        Route::delete('/{id}', 'ProyectoController@delete');
        Route::post('/delete', 'ProyectoController@deleteGroup');
    });

    /*Route::group(['prefix' => 'pago'], function() {

            Route::get('/', 'PagoController@getAll')->name("pago");
            Route::get('/count', 'PagoController@count');
            Route::get('/{id}', 'PagoController@get');
            Route::post('/', 'PagoController@store');
            Route::put('/{id}', 'PagoController@update');
            Route::delete('/{id}', 'PagoController@delete');

    });*/

    /*Route::group(['prefix' => 'codeudor'], function () {
        Route::get('/', 'CodeudorController@getAll')->name("codeudor");
        Route::get('/count', 'CodeudorController@count');
        Route::get('/{id}', 'CodeudorController@get');
        Route::post('/', 'CodeudorController@store');
        Route::put('/{id}', 'CodeudorController@update');
        Route::delete('/{id}', 'CodeudorController@delete');
    });*/


    Route::group(['prefix' => 'constructoras'], function () {
        Route::get('/', 'ConstructoraController@getAll')->name("constructoras");
        Route::get('/count', 'ConstructoraController@count');
        Route::get('/{id}', 'ConstructoraController@get');
        Route::post('/', 'ConstructoraController@store');
        Route::put('/{id}', 'ConstructoraController@update');
        Route::delete('/{id}', 'ConstructoraController@delete');
    });


    Route::group(['prefix' => 'ejecutivo_ventas'], function () {
        Route::get('/', 'EjVentasController@getAll')->name("ejecutivo_ventas");
        Route::get('/{id}', 'EjVentasController@get');
        Route::post('/', 'EjVentasController@store');
        Route::put('/{id}', 'EjVentasController@update');
        Route::delete('/{id}', 'EjVentasController@delete');
        Route::get('/mensajes/porLeer', 'EjVentasController@porLeer');
    });


    Route::group(['prefix' => 'ejecutivo_bancos'], function () {
        Route::get('/', 'EjBancosController@getAll')->name("ejecutivo_bancos");
        Route::get('/{id}', 'EjBancosController@get');
        Route::post('/', 'EjBancosController@store');
        Route::put('/{id}', 'EjBancosController@update');
        Route::delete('/{id}', 'EjBancosController@delete');
    });

    Route::group(['prefix' => 'brokers'], function () {
        Route::get('/{id}/obtenerPorcentaje/{id_proyecto}', 'BrokerController@obtenerPorcentaje');
        Route::get('/', 'BrokerController@getAll')->name('brokers');
        Route::post('/porcentaje', 'BrokerController@asignarPorcentaje');
        Route::post('/eliminar_casas/{id_broker}', 'BrokerController@quitarCasa');

        Route::get('/{id}/casaEstados', 'BrokerController@getCasaEstados');
        Route::get('/{id}', 'BrokerController@get');
        Route::post('/', 'BrokerController@store');
        Route::put('/{id}', 'BrokerController@update');
        Route::delete('/{id}', 'BrokerController@delete');
        Route::get('/mensajes/porLeer', 'BrokerController@porLeer');
    });

    Route::group(['prefix' => 'clientes'], function () {
        Route::get('/buscar', 'ClienteController@buscar');
        Route::get('/ejecutivos', 'ClienteController@ejecutivos');
        Route::get('/', 'ClienteController@getAll')->name('clientes');
        Route::get('/count', 'ClienteController@count');
        Route::get('/{id}', 'ClienteController@get');
        Route::post('/', 'ClienteController@store');
        Route::put('/{id}', 'ClienteController@update');
        Route::delete('/{id}', 'ClienteController@delete');
        Route::get('/ventas/{id}', 'ClienteController@getEjecutivos');
    });

    Route::group(['prefix' => 'clientescasa'], function () {
        Route::get('/', 'ClienteCasaController@getAll')->name('clientescasa');
        Route::get('/count', 'ClienteCasaController@count');
        Route::get('/{id}', 'ClienteCasaController@get');

        Route::post('/', 'ClienteCasaController@store');
        Route::get('getCasasCliente/{id}', 'ClienteCasaController@getCasasCliente');

        Route::put('/{id}', 'ClienteCasaController@update');
        Route::delete('/{id}', 'ClienteCasaController@delete');
    });


    Route::group(['prefix' => 'configuraciones'], function () {
        Route::get('/', 'ConfiguracionController@getAll')->name('configuraciones');
        Route::put('/{id}', 'ConfiguracionController@update');
        Route::get('/{id}', 'ConfiguracionController@get');
    });


    Route::group(['prefix' => 'documentos'], function () {
        Route::get('/data/cliente/{id}', 'DocumentoController@getById');
        Route::put('/data/cliente/{id}', 'DocumentoController@updateById');
        Route::get('/data/codeudor/{id}', 'DocumentoController@getByIdCodeudor');
        Route::put('/data/codeudor/{id}', 'DocumentoController@updateByIdCodeudor');
        Route::get('/', 'DocumentoController@getAll')->name('documentos');
        Route::get('/{id}', 'DocumentoController@get');
        Route::get('cliente/{id}', 'DocumentoController@listDocsCliente');
        Route::post('/', 'DocumentoController@storeClienteDoc');
        Route::put('/{id}', 'DocumentoController@update');
        Route::delete('/cliente/{id}', 'DocumentoController@deleteDocCliente');
        Route::delete('/codeudor/{id}', 'DocumentoController@deleteDocCodeudor');
    });


    Route::group(['prefix' => 'mensajes'], function () {
        Route::get('/', 'MensajeController@getAll')->name('mensajes');
        Route::get('/{id}', 'MensajeController@get');
        Route::post('/', 'MensajeController@store');
        Route::put('/{id}', 'MensajeController@update');
        Route::delete('/{id}', 'MensajeController@delete');
        Route::delete('/enviar', 'MensajeController@enviarMsj');
    });

    Route::group(['prefix' => 'pagos'], function () {
        Route::get('/', 'PagoController@getAll')->name("pagos");
        Route::get('/{id}', 'PagoController@get');
        Route::get('/cliente/{id}', 'PagoController@getPagosCliente');
        Route::post('/', 'PagoController@store');
        Route::put('/{id}', 'PagoController@update');
        Route::delete('/{id}', 'PagoController@delete');
    });


    Route::group(['prefix' => 'reporte'], function () {
        Route::get('/ejecutivoDeVentas/{id}', 'ReporteController@reporteEjecutivoDeVentas');
        Route::get('/informeDeVentas', 'ReporteController@reporteInformeDeVentas');
        Route::get('/finanzas', 'ReporteController@reporteFinanzas');
        Route::get('/estadoCuentaCliente', 'ReporteController@reporteEstadoCuentaCliente');
        Route::get('/ganancias/{id}/{id2}', 'ReporteController@reporteGananciaBroker');
        Route::get('/pagos', 'ReporteController@reportePagos');
        Route::get('/', 'ReporteController@index')->name("reporte");
        Route::get('/{id}', 'ReporteController@reporteDetallado');
        Route::get('/cliente/{id}', 'ReporteController@reporteCliente');
    });

    Route::group(['prefix' => 'user'], function () {
        Route::get('/', 'UserController@getAll')->name('user');
        Route::get('/count', 'UserController@count');
        Route::get('/{id}', 'UserController@get');
        Route::put('/{id}', 'UserController@update');
    });


    Route::get('pdf/{id}', 'PdfController@invoice');
    Route::get('pdf/reporte/ganancias/{id}/{id2}', 'PdfController@reporteGananciaBroker');
    Route::get('pdf/reporte/finanzas', 'PdfController@reporteFinanzas');
    Route::get('pdf/reporte/estadoCuentaCliente', 'PdfController@reporteEstadoCuentaCliente');
    Route::get('pdf/reporte/detallado/{id}', 'PdfController@reporteDetallado');
    Route::get('pdf/reporte/informeDeVentas', 'PdfController@reporteInformeDeVentas');
    Route::get('pdf/reporte/ejecutivoDeVentas/{id}', 'PdfController@reporteEjecutivoDeVentas');
    Route::get('pdf/reporte/listaDeClientes', 'PdfController@reportelistaDeClientes');
    Route::get('pdf/reporte/listaDePropiedades', 'PdfController@reportelistaDePropiedades');

    Route::get('excel/reporte/clientes', 'ExcelController@reporteListaDeClientesExcel');
    Route::get('excel/reporte/propiedades', 'ExcelController@reportelistaDePropiedades');
    Route::get('excel/reporte/finanzas', 'ExcelController@reporteFinanzas');
    Route::get('excel/reporte/informeDeVentas', 'ExcelController@reporteInformeDeVentas');
    Route::get('excel/reporte/estado-cuenta-cliente', 'ExcelController@reporteEstadoCuentaCliente');

    Route::group(['prefix' => 'notificaciones'], function () {
        Route::get('/', 'DocumentoController@getAll')->name('notificaciones');
        //Route::get('/count', 'MensajeController@count');
        //Route::get('/{id}', 'MensajeController@get');
        //Route::post('/', 'MensajeController@store');
        //Route::put('/{id}', 'MensajeController@update');
        //Route::delete('/{id}', 'MensajeController@delete');
        //Route::delete('/enviar', 'MensajeController@enviarMsj');
    });

    Route::group(['prefix' => 'imagenes'], function () {
        Route::get('/', 'ImagenController@getAll')->name('imagenes');
        Route::get('/count', 'ImagenController@count');
        Route::get('/{id}', 'ImagenController@get');
        Route::post('/', 'ImagenController@store');
        Route::put('/{id}', 'ImagenController@update');
        Route::delete('/{id}', 'ImagenController@delete');
    });

    Route::group(['prefix' => 'bancos', 'middleware' => 'userbanco'], function () {
        Route::get('/', 'BancoController@getAll')->name('bancos');
        Route::get('/count', 'BancoController@count');
        Route::get('/{id}', 'BancoController@get');
        Route::post('/', 'BancoController@store');
        Route::put('/{id}', 'BancoController@update');
        Route::delete('/{id}', 'BancoController@delete');
    });

    Route::group(['prefix' => 'requerimientos_tramites'], function () {
        Route::get('/{id_proyecto}', 'RequerimientoTramiteController@getAll')->name("requerimientos_tramites");
        Route::get('/{id_proyecto}/{id}', 'RequerimientoTramiteController@get');
        Route::post('/{id_proyecto}/copiar', 'RequerimientoTramiteController@copiar');
        Route::post('/{id_proyecto}', 'RequerimientoTramiteController@store');
        Route::put('/{id}', 'RequerimientoTramiteController@update');
        Route::delete('/{id}', 'RequerimientoTramiteController@delete');
    });

    Route::group(['prefix' => 'casas_estados'], function () {
        Route::get('', 'CasaEstadoController@getAll')->name("casas_estados");
        Route::get('/{id}', 'CasaEstadoController@get');
        Route::post('', 'CasaEstadoController@store');
        Route::put('/{id}', 'CasaEstadoController@update');
        Route::delete('/{id}', 'CasaEstadoController@delete');
    });

    Route::group(['prefix' => 'casas_requerimientos_tramites'], function () {
        Route::get('/{id_casa}', 'CasaRequerimientoTramiteController@getAll')->name("casas_requerimientos_tramites");
        Route::get('/{id}', 'CasaRequerimientoTramiteController@get');
        Route::get('/{id_casa}/{id_requerimiento_tramite}/toggle', 'CasaRequerimientoTramiteController@toggle');
    });
});
