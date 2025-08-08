<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

/**
 * Class Imagen
 * @package App\Models
 *
 * @const int MAX_LENGTH_DESCRIPCION
 * @const int MAX_LENGTH_NOMBRE
 * @const int MAX_LENGTH_PATH
 * @const int MAX_PHOTO_SIZE
 *
 * @property int $id
 * @property string $descripcion
 * @property string $nombre
 * @property string $path
 * @property string $created_at
 * @property string $updated_at
 * @property Collection $proyecto
 *
 */
class Imagen extends Model
{
    /**
     * @const string Constante que representa el máximo de caracteres que puede
     * contener la descripción
     */
    const MAX_LENGTH_DESCRIPCION = 150;
    /**
     * @const string Constante que representa el máximo de caracteres que puede
     * contener el nombre
     */
    const MAX_LENGTH_NOMBRE = 30;
    /**
     * @const string Constante que representa el máximo de caracteres que puede
     * contener el path
     */
    const MAX_LENGTH_PATH = 30;
    /**
     * @const string Constante que representa el máximo de espacio que puede
     * tener una imagen
     */
    const MAX_PHOTO_SIZE = 5242880;
    /**
     *
     * @var array Arreglo que coniene los formatos permitidos para las imágenes
     */
    public static $formatos_permitidos = [
        "jpeg","jpg","gif","png"
    ];
    /**
     * @const string Path donde se guardan las imágenes
     */
    const PATH = "/uploads/imagenes";
    /**
     * @const string Path público donde se guardan las imágenes
     */
    const PATH_PUBLIC = "/public/uploads/imagenes";

    protected $table = 'imagenes';

    protected $fillable = ['descripcion', 'nombre', 'path'];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function proyecto()
    {
        return $this->belongsToMany(Proyecto::class, 'proyectos_imagenes', 'id_imagen', 'id_proyecto');
    }
    
    /**
     * Genera un nuevo código disponible
     *
     * @return string
     */
    public static function generarNuevoCodigo()
    {
        do {
            $codigo = Funciones::generateCode(Funciones::BOX_HEX, self::MAX_LENGTH_PATH-5);
            $existe = Imagen::where('path', 'LIKE', "%$codigo%")->first();
        } while ($existe);
        return $codigo;
    }
}
