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
        // 🔥 evita N+1
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
     * 🎯 Dados completos do atendimento
     */
    public function getDadosCompletos(): array
    {
        return [
            'atendimento' => [
                'id' => $this->id,
                'valor_total' => $this->total, // accessor
                'data_criacao' => $this->criacao_data ?? null,
                'mesa' => $this->mesa
            ],
            'pedidos' => $this->getPedidosDetalhados()
        ];
    }

    /**
     * 📊 Retorna estado das mesas
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
     * 📂 Retorna todos atendimentos abertos
     */
    public static function getAbertos()
    {
        return self::whereNull('pagamento_data')
            ->with('pedidos.produto')
            ->get();
    }

    /**
     * 🔍 Retorna atendimento aberto por mesa
     */
    public static function getAbertoPorMesa($mesaId)
    {
        return self::where('mesa', $mesaId)
            ->whereNull('pagamento_data')
            ->orderBy('id', 'desc') // garante o mais recente
            ->with('pedidos.produto')
            ->first();
    }

    /**
     * 🚀 Busca ou cria atendimento automaticamente
     */
    public static function getOuCriarPorMesa($mesaId)
    {
        $atendimento = self::getAbertoPorMesa($mesaId);

        if (!$atendimento) {
            $atendimento = self::create([
                'mesa' => $mesaId,
                'criacao_data' => date('Y-m-d H:i:s'),
                'valor_total' => 0
            ]);
        }

        return $atendimento;
    }
}
