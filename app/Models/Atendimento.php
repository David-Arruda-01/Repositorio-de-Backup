<?php

namespace App\Models;

use Fmk\MVC\Model;
use App\Models\Pedido;

class Atendimento extends Model
{
    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'atendimento_id');
    }

    public function __get($name)
    {
        if ($name === 'total') {
            return $this->getTotal();
        }
        return parent::__get($name);
    }

    public static function getMesas()
    {
        $nmesas = defined('N_MESAS') ? constant('N_MESAS') : 1;
        $mesas = [];
        for ($x = 1; $x <= $nmesas; $x++) {
            $mesas[$x] = null;
        }
        $atendimentos = self::where('pagamento_data', 'is', null)->get();
        foreach ($atendimentos as $atendimento) {
            $mesas[$atendimento->mesa] = $atendimento;
        }
        return $mesas;
    }

    public function getTotal()
    {
        $total = 0;
        foreach ($this->pedidos() as $pedido) {
            $valorPedido = $pedido->valor_un ?? null;
            if ($valorPedido === null) {
                $produto = $pedido->produto()->first();
                $valorPedido = $produto->valor_un ?? $produto->produto_id ?? 0;
            }
            $total += $pedido->quantidade * $valorPedido;
        }
        return $total;
    }
}
