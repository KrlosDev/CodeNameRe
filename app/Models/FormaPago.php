<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class FormaPago
 * @package App\Models
 *
 * @property int $id
 * @property string $nombre
 * @property string $created_at
 * @property string $updated_at
 * @property string $deleted_at
 *
 */
class FormaPago extends Model
{
    use SoftDeletes;

    protected $table = 'formas_pagos';

    protected $fillable = ['nombre'];

    protected $dates = ['deleted_at'];

    public function pago()
    {
        return $this->belongsTo(Pago::class);
    }
}
