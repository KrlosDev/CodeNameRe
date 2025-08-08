<?php

namespace App\Models;

/**
 * Class Anuncios
 * @package App\Models
 *
 * @static string $insertado_exitosamente
 * @static string $no_insertado
 * @static string $actualizado_exitosamente
 * @static string $no_actualizado
 * @static string $eliminado_exitosamente
 * @static string $no_eliminado
 * @static string $bloqueado_exitosamente
 * @static string $no_bloqueado
 * @static string $usuario_bloqueado
 * @static string $activado_exitosamente
 * @static string $no_activado
 * @static string $desactivado_exitosamente
 * @static string $no_desactivado
 */
class Anuncios
{
    public static $insertado_exitosamente='Agregado Exitosamente.';
    public static $no_insertado='No se pudo insertar, intente nuevamente.';

    public static $actualizado_exitosamente='Actualizado Exitosamente.';
    public static $no_actualizado='No se pudo actualizar, intente nuevamente.';

    public static $eliminado_exitosamente='Eliminado satisfactoriamente.';
    public static $no_eliminado='No se pudo eliminaar, intente nuevamente.';
    
    public static $desbloqueado_exitosamente='Desbloqueado exitosamente.';
    public static $no_desbloqueado='No se pudo desbloquear, intente nuevamente.';
    
    public static $bloqueado_exitosamente='Bloqueado exitosamente.';
    public static $no_bloqueado='No se pudo bloquear, intente nuevamente.';
    
    public static $usuario_bloqueado='Su usuario se encuentra bloquedo, por favor comuniquese al administrador.';

    public static $activado_exitosamente='Activado exitosamente.';
    public static $no_activado='No se pudo activar.';
    
    public static $desactivado_exitosamente='Desctivado exitosamente.';
    public static $no_desactivado='No se pudo desactivar.';
}
