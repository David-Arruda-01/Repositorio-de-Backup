<?php

namespace App\Models;

use Fmk\MVC\Model;
use App\Models\Pedido;

class Atendimento extends Model
{
    // 🔗 Relacionamento com pedidos
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'atendimento_id');
    }

    // 💰 Total do atendimento
    public function getTotal()
    {
        $total = 0;

        $pedidos = $this->pedidos ?? [];

        foreach ($pedidos as $pedido) {
            $valor = $pedido->produto->valor_un ?? 0;
            $quantidade = $pedido->quantidade ?? 0;

            $total += $valor * $quantidade;
        }

        return $total;
    }

    // 🎯 🔥 NOVO: Retorna pedidos organizados
    public function getPedidosDetalhados(): array
    {
        $resultado = [];

        $pedidos = $this->pedidos ?? [];

        foreach ($pedidos as $pedido) {

            $resultado[] = [
                'pedido' => [
                    'id_pedido' => $pedido->id,
                    'status_do_pedido' => $pedido->status_do_pedido ?? null,
                    'quantidade' => $pedido->quantidade ?? null,
                ],

                'produto' => [
                    'id_produto' => $pedido->produto->id ?? null,
                    'nome_produto' => $pedido->produto->nome ?? null,
                    'valor_unitario' => $pedido->produto->valor_un ?? null,
                ],

                'funcionario' => [
                    'id_funcionario' => $pedido->funcionario->id ?? null,
                    'nome_funcionario' => $pedido->funcionario->nome ?? null,
                ],
            ];
        }

        return $resultado;
    }

    // 🎯 🔥 NOVO: Retorna tudo do atendimento + pedidos
    public function getDadosCompletos(): array
    {
        return [
            'atendimento' => [
                'id' => $this->id,
                'valor_total' => $this->getTotal(),
                'data_criacao' => $this->criacao_data ?? null,
            ],

            'pedidos' => $this->getPedidosDetalhados()
        ];
    }

    // 📊 Mesas abertas
    public static function getMesas()
    {
        $nMesas = constant('N_MESAS');

        $mesas = array_fill(1, $nMesas, null);

        $atendimentos = self::where('pagamento_data', 'is', null)->get();

        foreach ($atendimentos as $atendimento) {
            $mesas[$atendimento->mesa] = $atendimento;
        }

        return $mesas;
    }
}
