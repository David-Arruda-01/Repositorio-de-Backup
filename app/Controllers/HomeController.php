<?php

namespace App\Controllers;

use App\Models\Atendimento;
use Fmk\MVC\Controller;
use Fmk\Utils\Router;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middlewares('auth');
    }

    public function index()
    {
        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');

        $mesas = [];

        // Inicializa mesas como livres
        for ($i = 1; $i <= $nMesas; $i++) {
            $mesas[$i] = [
                'ocupada' => false,
                'atendimento' => null,
                'reservada' => false
            ];
        }

        // 🔥 SOMENTE atendimentos abertos
        $atendimentos = Atendimento::where('pagamento_data', 'IS', null)->get();

        foreach ($atendimentos as $atendimento) {
            $mesaId = (int) $atendimento->mesa;

            if (isset($mesas[$mesaId])) {

                if (!$mesas[$mesaId]['ocupada']) {
                    $mesas[$mesaId] = [
                        'ocupada' => true,
                        'atendimento' => $atendimento,
                        'reservada' => (bool) $atendimento->reservada
                    ];
                }
            }
        }

        return view('mesas', ['mesas' => $mesas], 'main');
    }

    public function alterarMesas()
    {
        session_start();
        $acao = $_POST['acao'] ?? null;
        if (!$acao) return Router::getRouteByName('home')->redirect();

        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');

        if ($acao === 'mais') $nMesas++;
        if ($acao === 'menos' && $nMesas > 1) $nMesas--;

        $_SESSION['N_MESAS'] = $nMesas;

        return Router::getRouteByName('home')->redirect();
    }

    public function atendimento($mesaId)
    {
        // Remove atendimento finalizado antigo (se existir)
        $atendimentoFinalizado = Atendimento::where('mesa', '=', $mesaId)
            ->orderDesc('id')
            ->first();

        if ($atendimentoFinalizado && $atendimentoFinalizado->pagamento_data !== null) {

            $pedidos = \App\Models\Pedido::where('atendimento_id', '=', $atendimentoFinalizado->id)->get();
            foreach ($pedidos as $pedido) {
                $pedido->delete();
            }

            $pagamentos = \App\Models\Pagamento::where('atendimento_id', '=', $atendimentoFinalizado->id)->get();
            foreach ($pagamentos as $pagamento) {
                $pagamento->delete();
            }

            $atendimentoFinalizado->delete();
        }

        // 🔥 Cria ou atualiza atendimento como NÃO reservado
        $atendimento = $this->salvarAtendimento($mesaId, null);

        return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
    }

    public function reservar($mesaId)
    {
        $this->salvarAtendimento($mesaId, 1); // 1 = reservado

        return route('home')->redirect();
    }
    private function salvarAtendimento($mesaId, $reservada = null)
    {
        $atendimento = Atendimento::where('mesa', '=', $mesaId)
            ->where('pagamento_data', 'IS', null)
            ->orderDesc('id')
            ->first();

        if ($atendimento) {
            $atendimento->reservada = $reservada;
            $atendimento->save();
        } else {
            $atendimento = Atendimento::create([
                'mesa' => $mesaId,
                'criacao_data' => date('Y-m-d H:i:s'),
                'reservada' => $reservada
            ]);
        }

        return $atendimento;
    }
}
