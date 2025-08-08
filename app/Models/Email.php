<?php

namespace App\Models;

use Illuminate\Support\Facades\Mail;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Email
 * @package App\Models
 *
 * @property int $id
 * @property string $asunto
 * @property string $contenido
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 */
class Email extends Model
{
    /**
     * @const int Constante que representa el límite de caracteres que se pueden
     * ingresar en el campo asunto
     */
    const MAX_LENGTH_ASUNTO = 30;
    
    protected $table = 'emails';
 
    protected $fillable = ['asunto','contenido'];
 
    protected $guarded = ['id'];
    
    /**
     * Envía el correo creado a la lista de jugadores provista
     *
     * @param  array $users
     * @return boolean
     */
    public function enviarCorreo($users)
    {
        if (! is_array($users)) {
            $users = [$users];
        }
        return self::enviarCorreoLibre($users, $this->asunto, $this->contenido);
    }
    
    /**
     * Envia un correo con el asunto y contenido al arreglo de jugadores provisto
     *
     * @param array $users
     * @param string $asunto
     * @param string $contenido
     * @return boolean
     */
    public static function enviarCorreoLibre(array $users, $asunto, $contenido)
    {
        return Mail::raw($contenido, function ($m) use ($users,$asunto) {
            $m->from('noreply@mihogar.com.pa.com', 'Mi Hogar');
            
            $m->to($users)->subject($asunto);
        });
    }
    
    /**
     * Enviar email de registro
     *
     * @param  \App\User $user
     * @param  string $password
     * @return boolean
     */
    public static function sendRegistrationNotification($user, $password)
    {
        try {
            return Mail::send('emails.registro', ['user' => $user,'password' => $password], function ($m) use ($user) {
                $m->from('noreply@mihogar.com.pa.com', 'Mi Hogar');
                
                $m->to($user->email, $user->name)->subject('Bienvenido a Mi Hogar');
            });
        } catch (\Exception $ex) {
            return false;
        }
    }
    
    /**
     * Enviar email de registro
     *
     * @param  \App\User $user
     * @return boolean
     */
    public static function sendNotificationRecoverPassword($user)
    {
        return Mail::send('emails.reset-pass', ['user' => $user], function ($m) use ($user) {
            $m->from('noreply@mihogar.com.pa.com', 'Mi Hogar');

            $m->to($user->email, $user->name)->subject('Recuperación de contraseña');
        });
    }
}
