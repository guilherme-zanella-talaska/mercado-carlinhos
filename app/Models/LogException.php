<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LogException extends Model
{
    protected $table = 'logs_exceptions';

    public $timestamps = false;

    protected $fillable = [
        'tipo',
        'mensagem',
        'arquivo',
        'linha',
        'metodo',
        'url',
        'id_usuario',
        'created_at'
    ];
}