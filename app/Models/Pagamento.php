<?php

namespace App\Models;

use Fmk\MVC\Model;

class Pagamento extends Model
{
    protected $visible = [
        'id',
        'atendimento_id',
        'valor',
        'metodo_pagamento',
        'data_pagamento'
    ];

    /**
     * 🔗 Pagamento pertence a um atendimento
     */
    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    /**
     * 📊 Escopo: pagamentos de um atendimento
     */
    public function scopeDoAtendimento($query, $atendimentoId)
    {
        return $query->where('atendimento_id', $atendimentoId);
    }

    /**
     * 📊 Escopo: ordenar por data
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('data_pagamento', 'desc');
    }

    /**
     * 💰 Formatar valor (Accessor)
     */
    public function getValorFormatadoAttribute()
    {
        return number_format($this->valor ?? 0, 2, ',', '.');
    }

    /**
     * 💳 Método formatado
     */
    public function getMetodoFormatadoAttribute()
    {
        return ucfirst($this->metodo_pagamento);
    }
}
