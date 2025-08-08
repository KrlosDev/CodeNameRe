<?php

namespace App\Models;

/**
 * Class Datos
 * @package App\Models
 *
 * @static array $EXTENSIONES_PERMITIDAS
 */
class Datos
{
    public static $EXTENSIONES_PERMITIDAS=[
        'ext_img'       => [ 'jpg', 'jpeg', 'png', 'gif', 'bmp', 'tiff', 'svg' ], //Images
        'ext_file'      => [ 'doc', 'docx', 'rtf', 'pdf', 'xls', 'xlsx', 'txt', 'csv', 'html', 'xhtml', 'psd', 'sql', 'log', 'fla', 'xml', 'ade', 'adp', 'mdb', 'accdb', 'ppt', 'pptx', 'odt', 'ots', 'ott', 'odb', 'odg', 'otp', 'otg', 'odf', 'ods', 'odp', 'css', 'ai', 'kmz' ], //Files
        'ext_video'     => [ 'mov', 'mpeg', 'm4v', 'mp4', 'avi', 'mpg', 'wma', "flv", "webm" ], //Video
        'ext_music'     => [ 'mp3', 'mpga', 'm4a', 'ac3', 'aiff', 'mid', 'ogg', 'wav' ], //Audio
        'ext_misc'      => [ 'zip', 'rar', 'gz', 'tar', 'iso', 'dmg' ], //Archives
    ];
}
