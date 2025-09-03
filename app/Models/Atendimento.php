<?php

namespace App\Models;

use Fmk\MVC\Model;

class Atendimento extends Model {

    public function pedidos() {
        return $this->hasMany(Pedido::class, foreign_key: 'atendimento_id');
    }

    public function getTotal() {
        $total = 0;
        foreach ($this->pedidos as $pedido) {
            $total += $pedido->valor_un * $pedido->quantidade;
        }
        return $total;
    }

    // Retorna todas as mesas com atendimentos, sem acessar o banco para N_MESAS
    public static function getMesas() {
        $nMesas = constant('N_MESAS'); // valor fixo da config

        // Inicializa o array de mesas
        $mesas = array_fill(1, $nMesas, null);

        // Busca atendimentos ainda não pagos
        $atendimentos = self::where('pagamento_data', 'is', null)->get();

        foreach ($atendimentos as $atendimento) {
            $mesas[$atendimento->mesa] = $atendimento;
        }

        return $mesas;
    }
}
