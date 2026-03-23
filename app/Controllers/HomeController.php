<?php
namespace App\Controllers;

use App\Models\Atendimento;
use Fmk\MVC\Controller;
use Fmk\Utils\Router;

class HomeController extends Controller {

    public function __construct() {
        $this->middlewares('auth');
    }

    public function index() {
        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');
        $mesas = array_fill(1, $nMesas, null);

        // Busca apenas atendimentos abertos (sem data de pagamento)
        $atendimentos = Atendimento::where('pagamento_data', 'IS', null)->get();

        foreach ($atendimentos as $atendimento) {
            if (isset($mesas[$atendimento->mesa])) {
                $mesas[$atendimento->mesa] = $atendimento;
            }
        }

        return view('mesas', ['mesas' => $mesas], 'main');
    }

    public function alterarMesas() {
        session_start();

        $acao = $_POST['acao'] ?? null;
        if (!$acao) return Router::getRouteByName('home')->redirect();

        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');

        if ($acao === 'mais') $nMesas++;
        if ($acao === 'menos' && $nMesas > 1) $nMesas--;

        $_SESSION['N_MESAS'] = $nMesas;

        return Router::getRouteByName('home')->redirect();
    }

    public function atendimento($id) {
        // Verifica se já existe um atendimento ativo para a mesa
        $atendimento = Atendimento::where('mesa', '=', $id)
            ->where('pagamento_data', 'IS', null)
            ->first();

        if (!$atendimento) {
            // Cria novo atendimento se não existir
            $atendimento = Atendimento::create([
                'mesa' => $id,
                'criacao_data' => date('Y-m-d H:i:s'),
            ]);
        }

        // Redireciona usando route() em vez de redirect()
        return (route('atendimentos', ['id' => $atendimento->id]))->redirect();
    }
}
