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

        // 🔥 SOMENTE ATENDIMENTOS ABERTOS (status = 1)
        $atendimentos = Atendimento::where('status', '=', 1)->get();

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

    // ===========================
    // 🍽️ ABRIR ATENDIMENTO
    // ===========================

    public function atendimento($mesaId)
    {
        // 🔥 Verifica se já existe atendimento ABERTO
        $atendimento = Atendimento::where('mesa', '=', $mesaId)
            ->where('status', '=', 1)
            ->first();

        // Se não existir, cria um novo
        if (!$atendimento) {
            $atendimento = Atendimento::create([
                'mesa' => $mesaId,
                'status' => 1, // 🔥 ABERTO
                'total' => 0,
                'reservada' => null,
                'criacao_data' => date('Y-m-d H:i:s')
            ]);
        }

        return route('atendimentos', ['id' => $atendimento->id])->redirect();
    }

    // ===========================
    // 🪑 RESERVAR MESA
    // ===========================

    public function reservar($mesaId)
    {
        // 🔥 Verifica se já existe atendimento aberto
        $atendimento = Atendimento::where('mesa', '=', $mesaId)
            ->where('status', '=', 1)
            ->first();

        if ($atendimento) {
            $atendimento->reservada = 1;
            $atendimento->save();
        } else {
            // cria já como reservado
            Atendimento::create([
                'mesa' => $mesaId,
                'status' => 1,
                'total' => 0,
                'reservada' => 1,
                'criacao_data' => date('Y-m-d H:i:s')
            ]);
        }

        return route('home')->redirect();
    }
}
