<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Models;

/**
 * Description of Funciones
 *
 * @author FelixAlberto
 */
class Funciones
{
    /**
     * @const int Option to indicates hexadecimal box
     */
    const BOX_HEX = 0;
    /**
     * @const int Option to indicates numeric box
     */
    const BOX_NUMBER = 1;
    /**
     * @const int Option to indicates alphabetic box
     */
    const BOX_ALPHABETIC = 2;
    /**
     *
     * @var array Array with the codes for boxes
     */
    public static $BOXES = [
        self::BOX_HEX => "0123456789abcdef",
        self::BOX_NUMBER => "0123456789",
        self::BOX_ALPHABETIC => "abcdefghijklmnñopqrstuvwxyz",
    ];
    
    public static $cantidad_option = [
        10 => 10,
        15 => 15,
        25 => 25,
        50 => 50,
        100 => 100,
        200 => 200,
        500 => 500,
        1000 => 1000,
        1500 => 1500,
    ];
    
    public static function getUsuario($user)
    {
        return "";
        if ($user->isEstudiante()) {
            return Usuario::where('usuario.id', $user->id)
                    ->leftjoin('estudiante', 'usuario_id', '=', 'usuario.id')
                    ->select('usuario.*', 'estudiante.Nombre', 'estudiante.Cedula', 'estudiante.id as estudiante_id')
                    ->first();
        }
        return Usuario::where('usuario.id', $user->id)
                ->leftjoin('profesor', 'usuario_id', '=', 'usuario.id')
                ->select('usuario.*', 'profesor.Nombre', 'profesor.Cedula', 'profesor.id as profesor_id')
                ->first();
    }
    
    public static function getAlert($tipo, $titulo, $mensaje)
    {
        return ["tipo"=>$tipo,"titulo"=>$titulo,"mensaje"=>$mensaje];
    }
    
    public static function ToRoman($num)
    {
        $n = intval($num);
        $res = '';

        //array of roman numbers
        $romanNumber_Array = [
        'M'  => 1000,
        'CM' => 900,
        'D'  => 500,
        'CD' => 400,
        'C'  => 100,
        'XC' => 90,
        'L'  => 50,
        'XL' => 40,
        'X'  => 10,
        'IX' => 9,
        'V'  => 5,
        'IV' => 4,
        'I'  => 1];

        foreach ($romanNumber_Array as $roman => $number) {
            //divide to get  matches
            $matches = intval($n / $number);

            //assign the roman char * $matches
            $res .= str_repeat($roman, $matches);

            //substract from the number
            $n = $n % $number;
        }

        // return the result
        return $res;
    }

    public static function list2array($list)
    {
        $array = explode(',', $list);
        $return = [];
        foreach ($array as $value) {
            $explode2 = explode('-', $value);
            if (count($explode2) > 1) {
                $range = range($explode2[0], $explode2[1]);
                $return = array_merge($return, $range);
            } else {
                $return[] = (int) $value;
            }
        }
        return $return;
    }

    public static function sanear_string($string)
    {
        $string = trim($string);
         
        $string = str_replace(
                ['á', 'à', 'ä', 'â', 'ª', 'Á', 'À', 'Â', 'Ä'],
                ['a', 'a', 'a', 'a', 'a', 'A', 'A', 'A', 'A'],
                $string
            );
         
        $string = str_replace(
                ['é', 'è', 'ë', 'ê', 'É', 'È', 'Ê', 'Ë'],
                ['e', 'e', 'e', 'e', 'E', 'E', 'E', 'E'],
                $string
            );
         
        $string = str_replace(
                ['í', 'ì', 'ï', 'î', 'Í', 'Ì', 'Ï', 'Î'],
                ['i', 'i', 'i', 'i', 'I', 'I', 'I', 'I'],
                $string
            );
         
        $string = str_replace(
                ['ó', 'ò', 'ö', 'ô', 'Ó', 'Ò', 'Ö', 'Ô'],
                ['o', 'o', 'o', 'o', 'O', 'O', 'O', 'O'],
                $string
            );
         
        $string = str_replace(
                ['ú', 'ù', 'ü', 'û', 'Ú', 'Ù', 'Û', 'Ü'],
                ['u', 'u', 'u', 'u', 'U', 'U', 'U', 'U'],
                $string
            );
         
        $string = str_replace(
                ['ñ', 'Ñ', 'ç', 'Ç'],
                ['n', 'N', 'c', 'C',],
                $string
            );
         
        //Esta parte se encarga de eliminar cualquier caracter extraño
         
        return $string;
    }
    
    /**
     *
     * @param string $date
     * @param string $format [optional]
     * @param \DateTimeZone $datetime_zone [optional]
     * @return \DateTime
     */
    public static function createDateTimeObject($date, $format='d/m/Y', $datetime_zone=null)
    {
        if ($datetime_zone===null) {
            $datetime_zone = new \DateTimeZone(config('app.timezone'));
        }
        try {
            return \DateTime::createFromFormat($format, $date, $datetime_zone);
        } catch (Excetpion $ex) {
            return null;
        }
    }
    
    /**
     * Colorea el monto según sea la cifra
     *
     * @param double $saldo
     * @param boolean $fixed [optional]
     * @param boolean $sign [optional]
     * @return string
     */
    public static function colorearSaldo($saldo, $fixed=true, $sign=false)
    {
        $str = "<span class=':color'>".(($sign && $saldo>=0)?"+":"").":saldo</span>";
        if ($saldo>=0) {
            $str = str_replace(':color', 'color-good', $str);
        } else {
            $str = str_replace(':color', 'color-remove', $str);
        }
        return str_replace(':saldo', (($fixed)?number_format($saldo, 2, ',', '.'):$saldo), $str);
    }
    
    /**
     * Colorea el monto según sea la cifra
     *
     * @param double $saldo
     * @param double $valor
     * @param boolean $fixed [optional]
     * @param boolean $sign [optional]
     * @return string
     */
    public static function colorearSaldoExacto($saldo, $valor, $fixed=true, $sign=false)
    {
        $str = "<span class=':color'>".(($sign && $saldo==$valor)?"+":"").":saldo</span>";
        if ($saldo==$valor) {
            $str = str_replace(':color', 'color-good', $str);
        } else {
            $str = str_replace(':color', 'color-remove', $str);
        }
        return str_replace(':saldo', (($fixed)?number_format($saldo, 2, ',', '.'):$saldo), $str);
    }
    
    /**
     * Generate a code with the parameters provided
     * @param int $key Key for selection
     * @param int $length The length of the string generated
     * @return mixed Returns the generated string or null if the key doesn't exists
     **/
    public static function generateCode($key, $length)
    {
        $code = "";
        for ($i = 0; $i < $length; $i++) {
            $code .= substr(self::$BOXES[$key], rand(0, strlen(self::$BOXES[$key])), 1);
        }
        return $code;
    }
    
    /**
     * Get the extension for the name provided
     * @param string $name The filename to get the extension
     * @return string Return the extension filename.
     **/
    public static function getExtension($name)
    {
        $array = explode(".", $name);
        return end($array);
    }

    /**
     * @return \Illuminate\Http\Response
     */
    public static function return403()
    {
        return response()->view('errors.403', [], 403);
    }
}
