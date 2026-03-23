<?php

namespace App\Models;

use Fmk\Database\Query;
use Fmk\MVC\Model;

class Pedido extends Model
{
    // 🔗 Pedido pertence a um Atendimento
    public function atendimento(): Query
    {
        return $this->belongsTo(Atendimento::class, 'atendimento_id');
    }

    // 🔗 Pedido pertence a um Produto
    public function produto(): Query
    {
        return $this->belongsTo(Produto::class, 'produto_id');
    }

    // 🔗 Pedido pertence a um Funcionário
    public function funcionario(): Query
    {
        return $this->belongsTo(Funcionario::class, 'funcionario_id');
    }

    // 🎯 Retorna todos os dados organizados
    public function getDadosCompletos(): array
    {
        return [
            'pedido' => [
                'id_pedido' => $this->id,
                'status_do_pedido' => $this->status_do_pedido ?? null,
                'quantidade' => $this->quantidade ?? null,
            ],

            // 🔥 Pagamento vem do ATENDIMENTO
            'pagamento' => [
                'id_pagamento' => $this->atendimento->id ?? null,
                'valor_do_pagamento' => $this->atendimento->valor ?? null,
                'tipo_pagamento_id' => $this->atendimento->pagamento_tipo_id ?? null,
                'data_pagamento' => $this->atendimento->criacao_data ?? null,
            ],

            'funcionario' => [
                'id_funcionario' => $this->funcionario->id ?? null,
                'nome_do_funcionario' => $this->funcionario->nome ?? null,
                'telefone' => $this->funcionario->telefone ?? null,
            ],

            'produto' => [
                'id_produto' => $this->produto->id ?? null,
                'nome_produto' => $this->produto->nome ?? null,
                'valor_unitario' => $this->produto->valor_un ?? null,
                'descricao' => $this->produto->descricao ?? null,
            ]
        ];
    }
}
