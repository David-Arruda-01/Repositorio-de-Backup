<?php

namespace App\Models;

use Fmk\MVC\Model;
use App\Models\Pedido;
use App\Models\Pagamento;

class Atendimento extends Model
{
    // ===========================
    // 🔗 RELACIONAMENTOS
    // ===========================

    public function pedidos()
    {
        return $this->hasMany(Pedido::class, 'atendimento_id');
    }

    public function pagamentos()
    {
        return $this->hasMany(Pagamento::class, 'atendimento_id');
    }

    // ===========================
    // 🔍 SCOPES (FILTROS)
    // ===========================

    public static function abertos()
    {
        return self::where('status', '=', 1)->get();
    }

    public static function finalizados()
    {
        return self::where('status', '=', 0)->get();
    }

    public static function porMesaAberto($mesa)
    {
        return self::where('mesa', '=', $mesa)
            ->where('status', '=', 1)
            ->first();
    }

    // ===========================
    // 🪑 MESAS
    // ===========================

    public static function getMesas()
    {
        $nmesas = defined('N_MESAS') ? constant('N_MESAS') : 1;

        $mesas = [];

        for ($x = 1; $x <= $nmesas; $x++) {
            $mesas[$x] = null;
        }

        // 🔥 SOMENTE ABERTOS
        $atendimentos = self::abertos();

        foreach ($atendimentos as $atendimento) {
            $mesas[$atendimento->mesa] = $atendimento;
        }

        return $mesas;
    }

    // ===========================
    // ➕ ABRIR ATENDIMENTO
    // ===========================

    public static function abrir($mesa, $reservada = null)
    {
        // verifica se já existe aberto
        $existe = self::porMesaAberto($mesa);

        if ($existe) {
            return $existe;
        }

        return self::create([
            'mesa' => $mesa,
            'status' => 1,
            'total' => 0,
            'reservada' => $reservada,
            'criacao_data' => date('Y-m-d H:i:s')
        ]);
    }

    // ===========================
    // ✅ FINALIZAR
    // ===========================

    public function finalizar()
    {
        $this->status = 0;
        $this->pagamento_data = date('Y-m-d H:i:s');
        $this->save();
    }

    // ===========================
    // 💰 REGISTRAR PAGAMENTO
    // ===========================

    public function registrarPagamento($tipo, $valor, $observacao = null)
    {
        $pagamento = new Pagamento();

        $pagamento->atendimento_id = $this->id;
        $pagamento->pagamento_tipo_id = $tipo;
        $pagamento->valor = $valor;
        $pagamento->observacao = $observacao;

        $pagamento->criacao_data = date('Y-m-d H:i:s');
        $pagamento->alteracao_data = date('Y-m-d H:i:s');
        $pagamento->exclusao_data = null;

        $pagamento->cadastrar();

        return $pagamento;
    }

    // ===========================
    // 💵 TOTAL
    // ===========================

    public function __get($name)
    {
        if ($name === 'total') {
            return $this->getTotal();
        }
        return parent::__get($name);
    }

    public function getTotal()
    {
        $total = 0;

        foreach ($this->pedidos as $pedido) {

            $valor = $pedido->valor_un ?? 0;

            if (!$valor && isset($pedido->produto)) {
                $valor = $pedido->produto->valor_un ?? 0;
            }

            $total += $pedido->quantidade * $valor;
        }

        return $total;
    }
}
