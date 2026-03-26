<?php

namespace App\Models;

use Fmk\MVC\Model;
use App\Models\Pedido;

class Atendimento extends Model
{
    protected $visible = [
        'id',
        'cliente_id',
        'mesa',
        'pagamento_data'
    ];

    /**
     * 🔗 Relacionamento com pedidos
     */
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'atendimento_id');
    }

    /**
     * 💰 Total do atendimento (Accessor)
     */
    public function getTotalAttribute()
    {
        // 🔥 Garante que não terá N+1
        $this->loadMissing('pedidos.produto');

        return $this->pedidos->sum(function ($pedido) {
            return ($pedido->valor_un ?? 0) * ($pedido->quantidade ?? 0);
        });
    }

    /**
     * 🎯 Pedidos detalhados
     */
    public function getPedidosDetalhados(): array
    {
        $this->loadMissing('pedidos.produto', 'pedidos.funcionario');

        return $this->pedidos->map(function ($pedido) {
            return [
                'pedido' => [
                    'id_pedido' => $pedido->id,
                    'status_do_pedido' => $pedido->status_do_pedido ?? null,
                    'quantidade' => $pedido->quantidade ?? 0,
                ],
                'produto' => [
                    'id_produto' => $pedido->produto->id ?? null,
                    'nome_produto' => $pedido->produto->nome ?? null,
                    'valor_unitario' => $pedido->valor_un ?? 0,
                ],
                'funcionario' => [
                    'id_funcionario' => $pedido->funcionario->id ?? null,
                    'nome_funcionario' => $pedido->funcionario->nome ?? null,
                ],
            ];
        })->toArray();
    }

    /**
     * 🎯 Dados completos
     */
    public function getDadosCompletos(): array
    {
        return [
            'atendimento' => [
                'id' => $this->id,
                'valor_total' => $this->total, // 🔥 usa accessor
                'data_criacao' => $this->criacao_data ?? null,
                'mesa' => $this->mesa
            ],
            'pedidos' => $this->getPedidosDetalhados()
        ];
    }

    /**
     * 📊 Mesas abertas
     */
    public static function getMesas()
    {
        $nMesas = constant('N_MESAS');

        $mesas = array_fill(1, $nMesas, null);

        $atendimentos = self::whereNull('pagamento_data')->get();

        foreach ($atendimentos as $atendimento) {
            $mesaId = (int) $atendimento->mesa;

            if ($mesaId >= 1 && $mesaId <= $nMesas) {
                $mesas[$mesaId] = $atendimento;
            }
        }

        return $mesas;
    }

    /**
     * 📂 Atendimentos abertos
     */
    public static function getAbertos()
    {
        return self::whereNull('pagamento_data')
            ->whereNotNull('mesa')
            ->get();
    }
}
