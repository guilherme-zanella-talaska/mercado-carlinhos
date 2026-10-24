<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Encomenda extends Model
{
    protected $table = 'encomendas';

    protected $fillable = [
        'id_cliente',
        'quantidade',
        'data_entrega',
        'horario_entrega',
        'tipo_entrega',
        'endereco',
        'status',
        'observacoes'
    ];

    protected function casts(): array
    {
        return [
            'data_entrega' => 'date',
        ];
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'id_cliente');
    }
}