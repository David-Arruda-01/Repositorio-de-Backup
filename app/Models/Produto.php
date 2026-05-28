<?php

namespace App\Models;

use Fmk\MVC\Model;

class Produto extends Model
{
    protected $fillable = [
        'nome',
        'descricao',
        'valor_un',
        'unidade_medida',
        'disponivel',
        'exclusao_data'
    ];

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, "produto_id");
    }
}
