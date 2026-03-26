<?php

namespace App\Models;

use Fmk\MVC\Model;

class Produto extends Model
{
    protected $visible = [
        'id',
        'nome',
        'preco',
        'descricao',
        'categoria_id',
        'ativo'
    ];

    /**
     * 🔗 Produto tem vários pedidos
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'produto_id');
    }

    /**
     * 💰 Total vendido desse produto
     */
    public function getTotalVendidoAttribute()
    {
        $this->loadMissing('pedidos');

        return $this->pedidos->sum(function ($pedido) {
            return ($pedido->quantidade ?? 0) * ($pedido->valor_un ?? 0);
        });
    }

    /**
     * 📦 Quantidade total vendida
     */
    public function getQuantidadeVendidaAttribute()
    {
        $this->loadMissing('pedidos');

        return $this->pedidos->sum('quantidade');
    }

    /**
     * 📊 Escopo: produtos ativos
     */
    public function scopeAtivos($query)
    {
        return $query->where('ativo', true);
    }

    /**
     * 📊 Escopo: ordenar por nome
     */
    public function scopeOrdenado($query)
    {
        return $query->orderBy('nome');
    }

    /**
     * 🔎 Escopo: busca por nome
     */
    public function scopeBuscar($query, $termo)
    {
        return $query->where('nome', 'like', "%{$termo}%");
    }
}
