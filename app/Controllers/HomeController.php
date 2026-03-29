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
                'atendimento' => null
            ];
        }

        // 🔥 pega todos atendimentos abertos
        $atendimentos = Atendimento::all();

        foreach ($atendimentos as $atendimento) {
            $mesaId = (int) $atendimento->mesa;

            if (isset($mesas[$mesaId])) {

                // ⚠️ garante apenas 1 atendimento por mesa
                if (!$mesas[$mesaId]['ocupada']) {
                    $mesas[$mesaId] = [
                        'ocupada' => true,
                        'atendimento' => $atendimento
                    ];
                    var_dump($mesas);
                    die();
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

    public function atendimento($id)
    {
        // 🔥 SEMPRE usa método centralizado
        $atendimento = Atendimento::find($id);

        if (!$atendimento) {
            // 🔥 cria atendimento com padrão único
            $atendimento = Atendimento::create([
                'mesa' => $id,
                'criacao_data' => date('Y-m-d H:i:s'),
                'valor_total' => 0
            ]);
        }

        return route('atendimentos', ['id' => $atendimento->id])->redirect();
    }
}
