<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Atendimento;
use App\Models\Pagamento;
use App\Models\Produto;
use App\Models\Pedido;
use Fmk\MVC\Controller;
use Fmk\Utils\Request;
use Fmk\Utils\Router;

class AtendimentosController extends Controller
{
    public function __construct()
    {
        $this->middlewares('auth');
    }

    // ===========================
    // 🔧 HELPERS
    // ===========================

    private function findOrFail($model, $id)
    {
        $item = $model::find($id);

        if (!$item) {
            throw new \Exception("Registro não encontrado");
        }

        return $item;
    }

    private function checkFinalizado($atendimento)
    {
        if ($atendimento->status == 0) {
            NotifyComponent::error("Atendimento já finalizado!");
            Router::getRouteByName('home')->redirect();
            exit;
        }
    }

    private function atualizarTotal($atendimento_id)
    {
        $pedidos = Pedido::where('atendimento_id', '=', $atendimento_id)->get();

        $total = 0;

        foreach ($pedidos as $pedido) {
            $total += $pedido->quantidade * $pedido->valor_un;
        }

        $atendimento = Atendimento::find($atendimento_id);

        if ($atendimento) {
            $atendimento->total = $total;
            $atendimento->save();
        }
    }

    // ===========================
    // 📋 INDEX
    // ===========================

    public function index($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        $this->checkFinalizado($atendimento);

        $pedidos = Pedido::where('atendimento_id', '=', $id)->get();

        return view('atendimentos.list', [
            'atendimento' => $atendimento,
            'pedidos'     => $pedidos
        ], 'main');
    }

    // ===========================
    // ➕ CREATE (MESAS DISPONÍVEIS)
    // ===========================

    public function create()
    {
        try {
            $nMesas = constant('N_MESAS');

            $mesas = [];

            // 🔥 APENAS ATENDIMENTOS ABERTOS
            $atendimentos = Atendimento::where('status', '=', 1)->get();

            $mesasOcupadas = [];
            foreach ($atendimentos as $atendimento) {
                $mesasOcupadas[] = (int) $atendimento->mesa;
            }

            for ($i = 1; $i <= $nMesas; $i++) {
                if (!in_array($i, $mesasOcupadas)) {
                    $mesas[] = $i;
                }
            }

            return view('atendimentos.create', [
                'mesas' => $mesas
            ], 'main');
        } catch (\Exception $e) {
            NotifyComponent::error('Erro: ' . $e->getMessage());
        }
    }

    // ===========================
    // ➕ ABRIR ATENDIMENTO
    // ===========================

    public function abrirAtendimento()
    {
        $request = Request::getInstance();

        $mesa = $request->validate('mesa', 'Mesa')->required()->isInt();

        if (!$request->validation()) {
            NotifyComponent::error("Mesa inválida.");
            return $request->old()->redirect();
        }

        // 🔥 EVITA DUPLICIDADE
        $existe = Atendimento::where('mesa', '=', $mesa)
            ->where('status', '=', 1)
            ->first();

        if ($existe) {
            NotifyComponent::error("Já existe atendimento aberto nessa mesa!");
            return $request->old()->redirect();
        }

        try {
            $atendimento = new Atendimento();

            $atendimento->mesa = $mesa;
            $atendimento->status = 1; // ABERTO
            $atendimento->total = 0;
            $atendimento->criacao_data = date('Y-m-d H:i:s');

            $atendimento->save();

            NotifyComponent::success("Atendimento aberto na mesa {$mesa}!");

            return route('atendimentos', [
                'id' => $atendimento->id
            ])->redirect();
        } catch (\Exception $e) {
            NotifyComponent::error("Erro ao abrir atendimento: " . $e->getMessage());
            return $request->old()->redirect();
        }
    }

    // ===========================
    // ➕ ADICIONAR PRODUTO
    // ===========================

    public function adicionarProduto($id)
    {
        $request = Request::getInstance();

        $produto_id = $request->validate('produto_id', 'Produto')->required();
        $quantidade = $request->validate('quantidade', 'Quantidade')->required()->isInt()->min(1);

        if (!$request->validation()) {
            NotifyComponent::error("Erros de validação.");
            return route('atendimentos', ['id' => $id])->redirect();
        }

        $atendimento = $this->findOrFail(Atendimento::class, $id);
        $this->checkFinalizado($atendimento);

        try {
            $produto = $this->findOrFail(Produto::class, $produto_id);

            Pedido::create([
                'atendimento_id'   => $id,
                'produto_id'       => $produto->id,
                'nome_produto'     => $produto->nome ?? null,
                'descricao_produto' => $produto->descricao ?? null,
                'quantidade'       => $quantidade,
                'valor_un'         => $produto->valor_un ?? $produto->preco ?? 0,
                'situacao'         => 'Pedido',
            ]);

            if ($atendimento->reservada !== null) {
                $atendimento->reservada = null;
                $atendimento->save();
            }

            $this->atualizarTotal($id);
        } catch (\Exception $e) {
            NotifyComponent::error($e->getMessage());
        }

        NotifyComponent::success('Produto adicionado!');
        return route('atendimentos', ['id' => $id])->redirect();
    }

    // ===========================
    // ✅ FINALIZAR
    // ===========================

    public function finalizarAtendimento($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        try {
            $atendimento->status = 0; // 🔥 FINALIZADO
            $atendimento->pagamento_data = date('Y-m-d H:i:s');
            $atendimento->save();
        } catch (\Exception $e) {
            NotifyComponent::error($e->getMessage());
        }

        NotifyComponent::success("Mesa {$atendimento->mesa} finalizada!");
        return Router::getRouteByName('home')->redirect();
    }

    // ===========================
    // 💰 PAGAMENTO
    // ===========================

    public function registrarPagamento($atendimentoId)
    {
        $request = Request::getInstance();

        $atendimento = $this->findOrFail(Atendimento::class, $atendimentoId);
        $this->checkFinalizado($atendimento);

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {

            $pagamento_tipo_id = $request->validate('pagamento_tipo_id')->required();
            $valor = $request->validate('valor')->required()->isFloat();

            if (!$request->validation()) {
                NotifyComponent::error("Erro no pagamento.");
                return route('atendimentos', ['id' => $atendimentoId])->redirect();
            }

            try {
                // 💰 REGISTRA PAGAMENTO
                $pagamento = new Pagamento();

                $pagamento->atendimento_id = $atendimento->id;
                $pagamento->pagamento_tipo_id = $pagamento_tipo_id;
                $pagamento->valor = $valor;
                $pagamento->criacao_data = date('Y-m-d H:i:s');

                $pagamento->cadastrar();

                // 🔥 FINALIZA ATENDIMENTO
                $atendimento->status = 0;
                $atendimento->pagamento_data = date('Y-m-d H:i:s');
                $atendimento->save();

                // ✅ ALERTA DE SUCESSO
                NotifyComponent::success("Pagamento realizado e atendimento finalizado com sucesso!");

                // 🔥 REDIRECIONA PARA HOME
                return Router::getRouteByName('home')->redirect();
            } catch (\Exception $e) {
                NotifyComponent::error("Erro ao registrar pagamento: " . $e->getMessage());
                return route('atendimentos', ['id' => $atendimento->id])->redirect();
            }
        }

        return route('atendimentos', ['id' => $atendimento->id])->redirect();
    }

    // ===========================
    // 🔖 RESERVAR
    // ===========================

    // public function reservadaAtendimento($id)
    // {
    //     try {
    //         $atendimento = $this->findOrFail(Atendimento::class, $id);
    //         $this->checkFinalizado($atendimento);

    //         $atendimento->reservada = date('Y-m-d H:i:s');
    //         $atendimento->save();

    //         NotifyComponent::success("Mesa {$atendimento->mesa} marcada como reservada!");
    //         return Router::getRouteByName('home')->redirect();
    //     } catch (\Exception $e) {
    //         NotifyComponent::error("Erro ao reservar mesa: " . $e->getMessage());
    //         return route('atendimentos', ['id' => $id])->redirect();
    //     }
    // }
}
