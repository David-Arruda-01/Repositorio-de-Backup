<?php

namespace App\Models;

use Fmk\MVC\Model;


class Pedido extends Model
{
    protected $fillable = [
        'atendimento_id',
        'produto_id',
        'nome_produto',
        'descricao_produto',
        'quantidade',
        'valor_un',
        'situacao',
        'saida_data',
        'entrega_data',
    ];

    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }
}
