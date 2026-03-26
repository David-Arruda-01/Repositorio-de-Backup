<?php

namespace App\Models;

use Fmk\MVC\Model;

class Pedido extends Model
{
    protected $visible = [
        'id',
        'atendimento_id',
        'produto_id',
        'quantidade',
        'valor_un',
        'status_do_pedido'
    ];

    /**
     * 🔗 Pedido pertence a um atendimento
     */
    public function atendimento()
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    /**
     * 🔗 Pedido pertence a um produto
     */
    public function produto()
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    /**
     * 🔗 Pedido pode ter um funcionário (opcional)
     */
    public function funcionario()
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    /**
     * 💰 Subtotal do pedido (Accessor)
     */
    public function getSubtotalAttribute()
    {
        return ($this->quantidade ?? 0) * ($this->valor_un ?? 0);
    }

    /**
     * 📦 Escopo: pedidos de um atendimento
     */
    public function scopeDoAtendimento($query, $atendimentoId)
    {
        return $query->where('atendimento_id', $atendimentoId);
    }

    /**
     * 📦 Escopo: com produto (evita N+1)
     */
    public function scopeComProduto($query)
    {
        return $query->with('produto');
    }
}
