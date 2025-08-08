<?php

use App\User;

$factory->define(App\User::class, function (Faker\Generator $faker) {
    return [
        'name' => $faker->unique()->word,
        'nombre' => $faker->name,
        'email' => $faker->email,
        'password' => bcrypt(str_random(10)),
        'estado' => $faker->randomElement(array_keys(User::$estados)),
        'id_rol' => rand(1, 6),
    ];
});

$factory->define(App\Models\Banco::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->word,
    ];
});

$factory->define(App\Models\Broker::class, function (Faker\Generator $faker) {
    return [
        'max_ej_ventas' => $faker->numberBetween(1, 5),
        'max_ej_bancos' => $faker->numberBetween(1, 5),
    ];
});

$factory->define(App\Models\Casa::class, function (Faker\Generator $faker) {
    return [
        'codigo' => $faker->unique()->word,
        'modelo' => $faker->unique()->word,
        'estatus_tramite_cliente' => $faker->randomElement(array_keys(App\Models\Casa::$estados)),
        'lote_apto' => $faker->numberBetween(1, 100),
        'mts2_total' => $faker->randomFloat(2, 1, 100),
        'mts2_construccion' => $faker->randomFloat(2, 1, 100),
        'mts2_adicionales' => $faker->randomFloat(2, 1, 100),
        'recamaras' => $faker->numberBetween(1, 4),
        'banos' => $faker->numberBetween(1, 4),
        'valor' => $faker->randomFloat(2, 1000, 50000),
        'monto_separacion' => $faker->randomFloat(2, 100, 250),
        'monto_abono_inicial' => $faker->randomFloat(2, 100, 250),
        'monto_mts2_adicional' => $faker->randomFloat(2, 100, 150),
    ];
});

$factory->define(App\Models\CasaEstado::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->words(5, true),
        'slug' => $faker->unique()->word,
    ];
});

$factory->define(App\Models\Cliente::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->word,
        'apellido' => $faker->word,
        'identificacion' => $faker->word,
        'fecha_nacimiento' => $faker->dateTime(),
        'estado_civil' => $faker->randomElement(array_keys(App\Models\Cliente::$estados_civiles)),
        'direccion' => $faker->words(3, true),
        'casa_apartamento' => $faker->randomElement(array_keys(App\Models\Cliente::$estados_tipos_residencia)),
        'email' => $faker->unique()->email,
        'tipo_trabajo' => $faker->randomElement(array_keys(App\Models\Cliente::$estados_trabajo)),
        //'empresa' => $faker->word,
        //'cargo_empresa' => $faker->word,
        'salario' => $faker->randomFloat(0, 100, 100000),
        'anios_laborando' => $faker->randomFloat(1, 0, 50),
        //'direccion_empresa' => $faker->word,
        //'email_empresa' => $faker->unique()->email,
        'notas' => $faker->words(5, true),
    ];
});

$factory->define(App\Models\Codeudor::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->word,
        'apellido' => $faker->word,
        'identificacion' => $faker->word,
        'fecha_nacimiento' => $faker->dateTime(),
        'estado_civil' => $faker->randomElement(array_keys(App\Models\Codeudor::$estados_civiles)),
        'direccion' => $faker->words(3, true),
        'casa_apartamento' => $faker->randomElement(array_keys(App\Models\Codeudor::$estados_tipos_residencia)),
        'email' => $faker->unique()->email,
        'tipo_trabajo' => $faker->randomElement(array_keys(App\Models\Codeudor::$estados_trabajo)),
        //'empresa' => $faker->word,
        //'cargo_empresa' => $faker->word,
        //'direccion_empresa' => $faker->word,
        //'email_empresa' => $faker->unique()->email,
    ];
});

$factory->define(App\Models\Constructora::class, function (Faker\Generator $faker) {
    return [
        'max_brokers' => $faker->numberBetween(1, 10),
        'max_ejecutivos_ventas' => $faker->numberBetween(1, 20),
        'max_ejecutivos_bancos' => $faker->numberBetween(1, 20),
    ];
});

$factory->define(App\Models\Configuracion::class, function (Faker\Generator $faker) {
    return [
        'descripcion' => $faker->text(30),
        'contenido' => $faker->text(50),
    ];
});

$factory->define(App\Models\Corregimiento::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->city,
    ];
});

$factory->define(App\Models\Distrito::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->city,
    ];
});

$factory->define(App\Models\DocumentoCliente::class, function (Faker\Generator $faker) {
    return [
        'fecha_expiracion' => $faker->dateTime,
        'tipo_documento' => $faker->randomElement(array_keys(App\Models\DocumentoCliente::$tipos)),
        'src' => $faker->text(50),
    ];
});

$factory->define(App\Models\DocumentoCodeudor::class, function (Faker\Generator $faker) {
    return [
        'fecha_expiracion' => $faker->dateTime,
        'tipo_documento' => $faker->randomElement(array_keys(App\Models\DocumentoCodeudor::$tipos)),
        'src' => $faker->text(50),
    ];
});

$factory->define(App\Models\EjecutivoVentas::class, function (Faker\Generator $faker) {
    return [

    ];
});

$factory->define(App\Models\EjecutivoBancos::class, function (Faker\Generator $faker) {
    return [

    ];
});

$factory->define(App\Models\FormaPago::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->word,
    ];
});

$factory->define(App\Models\Imagen::class, function (Faker\Generator $faker) {
    return [
        'descripcion' => $faker->text(\App\Models\Imagen::MAX_LENGTH_DESCRIPCION),
        'nombre' => $faker->words(\App\Models\Imagen::MAX_LENGTH_NOMBRE, true),
        'path' => $faker->unique()->word,
    ];
});

$factory->define(App\Models\Licencia::class, function (Faker\Generator $faker) {
    return [
        'fecha_vencimiento' => $faker->dateTimeBetween('next Monday', 'next Monday +7 days'),
        'fecha_suspension' => $faker->dateTimeBetween('next Monday +7 days', 'next Monday +14 days'),
    ];
});

$factory->define(App\Models\Mensaje::class, function (Faker\Generator $faker) {
    return [
        'titulo' => $faker->word,
        'descripcion' => $faker->text(25),
        'leido_broker' => $faker->boolean(50),
        'leido_ventas' => $faker->boolean(50),
    ];
});

$factory->define(App\Models\Pago::class, function (Faker\Generator $faker) {
    return [
        'descripcion' => $faker->text(25),
        'id_tipo_transaccion' => $faker->randomElement(array_keys(App\Models\Pago::$tipos_pago)),
        'monto' => $faker->randomFloat(0, 10, 1500),
        'realizado_at' => $faker->dateTime(),
    ];
});

$factory->define(App\Models\Pais::class, function (Faker\Generator $faker) {
    $nombre = $faker->country;
    return [
        'nombre' => $nombre,
        'name' => $nombre,
        'nom' => $nombre,
        'iso2' => $faker->countryCode,
        'iso3' => $faker->countryISOAlpha3,
        'codigo_telefonico' => $faker->numberBetween(1, 99),
    ];
});

$factory->define(App\Models\PorcentajeBroker::class, function (Faker\Generator $faker) {
    return [
        'porcentaje' => $faker->randomFloat(2, 0, 99),
    ];
});

$factory->define(App\Models\Provincia::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->city,
    ];
});

$factory->define(App\Models\Proyecto::class, function (Faker\Generator $faker) {
    return [
        'codigo' => $faker->unique()->word,
        'nombre' => $faker->name,
        'estado' => $faker->randomElement(array_keys(App\Models\Proyecto::$estados)),
        'descripcion' => $faker->text(100),
    ];
});

$factory->define(App\Models\RequerimientoTramite::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->name,
        'cumplido' => $faker->boolean(50),
    ];
});

$factory->define(App\Models\Rol::class, function (Faker\Generator $faker) {
    return [
        'nombre' => $faker->name,
    ];
});

$factory->define(App\Models\TelefonoCliente::class, function (Faker\Generator $faker) {
    return [
        'telefono' => $faker->numberBetween(54895004, 98745632),
    ];
});
