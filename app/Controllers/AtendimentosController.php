<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Atendimento;
use App\Models\Pagamento;
use App\Models\PagamentoTipo;
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

    /**
     * Busca o atendimento ativo de uma mesa ou lança erro
     */
    private function findAtendimentoByMesa($mesa)
    {
        $atendimento = Atendimento::where('mesa', '=', $mesa)
            ->where('pagamento_data', 'IS', null)
            ->first();

        if (!$atendimento) {
            throw new \Exception("Atendimento ativo não encontrado para a mesa $mesa");
        }

        return $atendimento;
    }

    private function checkFinalizado($atendimento)
    {
        if ($atendimento->pagamento_data !== null) {
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

    public function index($mesa)
    {
        try {
            $atendimento = $this->findAtendimentoByMesa($mesa);
            $this->checkFinalizado($atendimento);

            $pedidos = Pedido::where('atendimento_id', '=', $atendimento->id)->get();

            return view('atendimentos.list', [
                'atendimento' => $atendimento,
                'pedidos'     => $pedidos
            ], 'main');
        } catch (\Exception $e) {
            NotifyComponent::error($e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }
    }

    // ===========================
    // ➕ CREATE (SELECIONAR MESA PARA NOVO ATENDIMENTO)
    // ===========================

    public function create()
    {
        try {
            $nMesas = constant('N_MESAS');

            $mesas = [];

            // Verifica quais mesas estão ocupadas
            $atendimentos = Atendimento::where('pagamento_data', 'IS', null)->get();
            $mesasOcupadas = [];
            foreach ($atendimentos as $atendimento) {
                $mesasOcupadas[] = (int) $atendimento->mesa;
            }

            // Cria lista de mesas disponíveis
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
    // 💾 STORE (CRIAR PEDIDO INDEPENDENTE)
    // ===========================

    public function store()
    {
        $request = Request::getInstance();

        $produto_id = $request->validate('produto_id', 'Produto')->required();
        $quantidade = $request->validate('quantidade', 'Quantidade')->required()->isInt()->min(1);
        $valor_un = $request->validate('valor_un', 'Valor Unitário')->required()->isFloat();

        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }

        try {
            $produto = $this->findOrFail(Produto::class, $produto_id);

            Pedido::create([
                'produto_id'       => $produto->id,
                'nome_produto'     => $produto->nome ?? null,
                'descricao_produto' => $produto->descricao ?? null,
                'quantidade'       => $quantidade,
                'valor_un'         => $valor_un,
                'situacao'         => 'Pedido',
            ]);

            NotifyComponent::success("Pedido criado com sucesso!");
            return route('atendimentos.create')->redirect();
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao criar pedido: ' . $e->getMessage());
            return $request->old()->redirect();
        }
    }

    // ===========================
    // ➕ ADICIONAR PRODUTO (POR MESA)
    // ===========================

    public function adicionarProduto($mesa)
    {
        $request = Request::getInstance();

        $produto_id = $request->validate('produto_id', 'Produto')->required();
        $quantidade = $request->validate('quantidade', 'Quantidade')->required()->isInt()->min(1);

        if (!$request->validation()) {
            NotifyComponent::error("Erros de validação no formulário.");
            $atendimento = Atendimento::find($id);
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
        }

        $atendimento = $this->findOrFail(Atendimento::class, $id);
        $this->checkFinalizado($atendimento);

        try {
            $atendimento = $this->findAtendimentoByMesa($mesa);
            $this->checkFinalizado($atendimento);

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
            NotifyComponent::error('Erro ao adicionar produto: ' . $e->getMessage());
            $atendimento = Atendimento::find($id);
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
        }

        NotifyComponent::success('Produto adicionado com sucesso!');

        $atendimento = Atendimento::find($id);
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
    }

    // ===========================
    // ✏️ UPDATE PEDIDO
    // ===========================

    public function update($id)
    {
        $request = Request::getInstance();

        $produto_id = $request->validate('produto_id', 'Produto')->required();
        $quantidade = $request->validate('quantidade', 'Quantidade')->required()->isInt()->min(1);
        $valor_un = $request->validate('valor_un', 'Valor Unitário')->required();

        try {
            $pedido = $this->findOrFail(Pedido::class, $id);
            $atendimento = $this->findOrFail(Atendimento::class, $pedido->atendimento_id);

            if (!$request->validation()) {
                NotifyComponent::error("Erros de validação no formulário.");
                return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
            }

            $pedido->produto_id = $produto_id;
            $pedido->quantidade = $quantidade;
            $pedido->valor_un = $valor_un;
            $pedido->save();

            $this->atualizarTotal($atendimento->id);
            NotifyComponent::success("Pedido atualizado!");
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao atualizar pedido: ' . $e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }
    }

    // ===========================
    // ✅ FINALIZAR ATENDIMENTO (POR MESA)
    // ===========================

    public function finalizarAtendimento($mesa)
    {
        try {
            $atendimento = $this->findAtendimentoByMesa($mesa);
            $id = $atendimento->id;

            $pedidos = Pedido::where('atendimento_id', '=', $id)->get();

            if (empty($pedidos)) {
                NotifyComponent::error("Sem pedidos!");
                return route('atendimentos', ['id' => $mesa])->redirect();
            }

            // Deletar todos os pedidos relacionados
            foreach ($pedidos as $pedido) {
                $pedido->delete();
            }

            // Deletar pagamentos relacionados
            $pagamentos = Pagamento::where('atendimento_id', '=', $id)->get();
            foreach ($pagamentos as $pagamento) {
                $pagamento->delete();
            }

            // Deletar o atendimento
            $atendimento->delete();

            NotifyComponent::success("Atendimento da mesa {$mesa} finalizado e removido!");
            return Router::getRouteByName('home')->redirect();
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao finalizar atendimento: ' . $e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }
    }

    // ===========================
    // ✅ RESERVADA (POR MESA)
    // ===========================

    public function reservadaAtendimento($mesa)
    {
        try {
            $atendimento = $this->findAtendimentoByMesa($mesa);
            
            // Deletar pagamentos relacionados se houver
            $pagamentos = Pagamento::where('atendimento_id', '=', $atendimento->id)->get();
            foreach ($pagamentos as $pagamento) {
                $pagamento->delete();
            }

            NotifyComponent::success("Mesa {$mesa} reservada!");
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao reservar mesa: ' . $e->getMessage());
        }
        
        return Router::getRouteByName('home')->redirect();
    }

    // ===========================
    // ❌ EXCLUIR PEDIDO
    // ===========================

    public function excluirPedido($id)
    {
        try {
            $pedido = $this->findOrFail(Pedido::class, $id);
            $atendimento = $this->findOrFail(Atendimento::class, $pedido->atendimento_id);
            
            $pedido->delete();
            $this->atualizarTotal($atendimento_id);
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao excluir pedido: ' . $e->getMessage());
            $atendimento = Atendimento::find($atendimento_id);
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
        }

        NotifyComponent::success("Pedido excluído!");

        $atendimento = Atendimento::find($atendimento_id);
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
    }

    // =======================================================================
    // ❌ EXCLUIR PRODUTO (DO PEDIDO CASO TENHA SIDO ADICIONADO MANUALMENTE)
    // =======================================================================
    public function excluirProduto($id)
    {
        $pedido = $this->findOrFail(Pedido::class, $id);
        $atendimento_id = $pedido->atendimento_id;

        try {
            $pedido->delete();
            $this->atualizarTotal($atendimento_id);
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao excluir pedido: ' . $e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }

        NotifyComponent::success("Produto do pedido excluído!");

        $atendimento = Atendimento::find($atendimento_id);
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
    }

    // ===========================
    // ❌ EXCLUIR PRODUTO DO PEDIDO
    // ===========================

    public function excluirProduto($id)
    {
        try {
            $pedido = $this->findOrFail(Pedido::class, $id);
            $atendimento = $this->findOrFail(Atendimento::class, $pedido->atendimento_id);
            
            $pedido->delete();
            $this->atualizarTotal($atendimento->id);
            
            NotifyComponent::success("Produto do pedido excluído!");
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao excluir produto do pedido: ' . $e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }
    }

    // ===========================
    // 💰 REGISTRAR PAGAMENTO (POR MESA)
    // ===========================

    public function registrarPagamento($mesa)
    {
        $request = Request::getInstance();

        $pagamento_tipo_id = $request->validate('pagamento_tipo_id', 'Tipo de Pagamento')->required()->isInt();
        $valor = $request->validate('valor', 'Valor')->required()->isFloat();

        $atendimento = Atendimento::find($id);

        if (!$request->validation()) {
            NotifyComponent::error("Erros de validação no formulário.");
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
        }

        if ((float) $valor <= 0) {
            NotifyComponent::error('O valor do pagamento deve ser maior que zero.');
            return route('atendimentos', ['id' => $atendimento->mesa])->redirect();
        }

            $this->checkFinalizado($atendimento);

            Pagamento::create([
                'atendimento_id'   => $atendimento->id,
                'pagamento_tipo_id' => $pagamento_tipo_id,
                'valor'            => (float) $valor,
            ]);

            NotifyComponent::success("Pagamento registrado com sucesso!");
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao registrar pagamento: ' . $e->getMessage());
        }

        return route('atendimentos', ['id' => $mesa])->redirect();
    }

    // ===========================
    // 📊 TOTAL (JSON) POR MESA
    // ===========================

    public function calcularTotalAtendimento($mesa)
    {
        try {
            $atendimento = $this->findAtendimentoByMesa($mesa);
            $total = $atendimento->getTotal();

            echo json_encode([
                'total' => $total,
                'total_formatado' => 'R$ ' . number_format($total, 2, ',', '.')
            ]);
        } catch (\Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
        exit;
    }

    // ===========================
    // ✏️ EDIT PEDIDO
    // ===========================

    public function edit($id)
    {
        try {
            $pedido = $this->findOrFail(Pedido::class, $id);
            return view('atendimentos.edit', ['pedido' => $pedido], 'main');
        } catch (\Exception $e) {
            NotifyComponent::error($e->getMessage());
            return Router::getRouteByName('home')->redirect();
        }
    }

    // ===========================
    // 🗑️ DESTROY ATENDIMENTO (POR MESA)
    // ===========================

    public function destroy()
    {
        $request = Request::getInstance();
        $mesa = $request->validate('mesa')->required();

        try {
            $atendimento = $this->findAtendimentoByMesa($mesa);
            $id = $atendimento->id;

            // Deletar todos os pedidos relacionados
            $pedidos = Pedido::where('atendimento_id', '=', $id)->get();
            foreach ($pedidos as $pedido) {
                $pedido->delete();
            }

            // Deletar pagamentos relacionados
            $pagamentos = Pagamento::where('atendimento_id', '=', $id)->get();
            foreach ($pagamentos as $pagamento) {
                $pagamento->delete();
            }

            // Deletar o atendimento
            $atendimento->delete();

            NotifyComponent::success("Atendimento da mesa {$mesa} excluído com sucesso!");
        } catch (\Exception $e) {
            NotifyComponent::error("Erro ao excluir atendimento: " . $e->getMessage());
        }

        return Router::getRouteByName('home')->redirect();
    }
}
