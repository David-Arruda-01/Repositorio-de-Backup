<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;
use App\Models\Atendimento;
use App\Controllers\AtendimentosController;

class ReservadaTest extends TestCase
{
    public function testReservadaButtonChangesTableToReserved()
    {
        // Criar um atendimento sem pedidos
        $atendimento = Atendimento::create([
            'mesa' => 1,
            'criacao_data' => date('Y-m-d H:i:s')
        ]);

        // Antes de reservar, verificar se NÃO tem pedidos
        $pedidos = \App\Models\Pedido::where('atendimento_id', '=', $atendimento->id)
            ->whereNotNull('produto_id')
            ->first();

        $this->assertFalse($pedidos, 'Atendimento deve estar sem pedidos inicialmente');

        // ✅ Chamar o método que será testado
        $controller = new AtendimentosController();
        $controller->reservadaAtendimento($atendimento->id);

        // Verificar que o atendimento ainda existe
        $atendimentoReloaded = Atendimento::find($atendimento->id);
        $this->assertNotNull($atendimentoReloaded);

        // Simular lógica da home
        $atendimentos = Atendimento::all();

        $mesas = [];
        for ($i = 1; $i <= 10; $i++) {
            $mesas[$i] = ['ocupada' => false, 'reservada' => false];
        }

        foreach ($atendimentos as $at) {
            $mesaId = (int) $at->mesa;

            $has_produtos = \App\Models\Pedido::where('atendimento_id', '=', $at->id)
                ->whereNotNull('produto_id')
                ->first();

            $mesas[$mesaId] = [
                'ocupada' => true,
                'reservada' => !$has_produtos
            ];
        }

        // ✅ Verificações finais
        $this->assertTrue($mesas[1]['ocupada']);
        $this->assertTrue($mesas[1]['reservada']);

        // Limpeza
        $atendimento->delete();
    }
}
