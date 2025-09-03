<?php
/*
namespace App\Controllers;

use App\Models\Atendimento;
use Fmk\MVC\Controller;
use Fmk\Utils\Router;

class HomeController extends Controller {

    public function __construct() {
            $this->middlewares('auth');
        }


    public function index() {
        $mesas = array_fill(1,constant('N_MESAS'),null);
        $atendimentos = Atendimento::where('pagamento_data','is',null)->get();
        foreach($atendimentos as $atendimento){
            $mesas[$atendimento->mesa] = $atendimento;
        } 

        return view('mesas', ['mesas' => $mesas], 'main');
        //'mesas' => $mesas, 'nMesas' => $nMesas
    }

    public function alterarMesas() {
        session_start();

        $acao = $_POST['acao'] ?? null;
        if (!$acao) return Router::getRouteByName('home')->redirect();

        // Pega número atual de mesas da sessão ou da constante
        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');

        if ($acao === 'mais') $nMesas++;
        if ($acao === 'menos' && $nMesas > 1) $nMesas--;

        // Salva na sessão
        $_SESSION['N_MESAS'] = $nMesas;

        return Router::getRouteByName('home')->redirect();
    }

    public function atendimento($id){
    // Verifica se já existe um atendimento ativo para a mesa
    $atendimento = Atendimento::where('mesa_id', $id)
                              ->where('status', 'ativo')
                              ->first();

    if (!$atendimento) {
        // Se não existir, cria um novo atendimento
        $atendimento = Atendimento::create([
            'mesa_id' => $id,
            'status' => 'ativo',
            'inicio' => date('Y-m-d H:i:s'),
            // outros campos que você precise inicializar
        ]);
    }

    // Redireciona para a tela de atendimento com o ID do atendimento
    return route('atendimento.show', ['id' => $atendimento->id]);
    }

}

<?php 
*/
namespace App\Controllers;

use App\Models\Atendimento;
use Fmk\MVC\Controller;
use Fmk\Utils\Router;

class HomeController extends Controller {

    public function __construct() {
        $this->middlewares('auth');
    }

    public function index() {
    // cria o array de mesas [1 => null, 2 => null, ..., N_MESAS => null]
    $mesas = array_fill(1, constant('N_MESAS'), null);

    // pega todos os atendimentos da tabela (não só os abertos)
    $atendimentos = Atendimento::all();

    foreach ($atendimentos as $atendimento) {
        $status = 'livre';

        if (is_null($atendimento->pagamento_data)) {
            $status = 'ativo'; // em andamento
        } else {
            $status = 'finalizado'; // já pago/finalizado
        }

        // adiciona no array a mesa com os dados e o status
        $mesas[$atendimento->mesa] = [
            'id'     => $atendimento->id,
            'mesa'   => $atendimento->mesa,
            'status' => $status,
            'dados'  => $atendimento
        ];
    }

    // se a mesa não tiver atendimento, mantém "livre"
    foreach ($mesas as $num => $valor) {
        if ($valor === null) {
            $mesas[$num] = [
                'id'     => null,
                'mesa'   => $num,
                'status' => 'livre',
                'dados'  => null
            ];
        }
    }

    return view('mesas', ['mesas' => $mesas], 'main');
}
        //print_r($mesas);

        // var_dump($mesas);
        //die;
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

    public function atendimento($id){
    // Verifica se já existe um atendimento ativo para a mesa
    $atendimento = Atendimento::where('mesa', $id)
                              ->where('status', 'ativo')
                              ->first();

    if (!$atendimento) {
        // Cria novo atendimento se não existir
        $atendimento = Atendimento::create([
            'mesa' => $id,
            'status' => 'ativo',
            'inicio' => date('Y-m-d H:i:s'),
        ]);
    }

    // Redireciona usando route() em vez de redirect()
    return (route('atendimentos.list', ['id' => $atendimento->id]));
}
}


