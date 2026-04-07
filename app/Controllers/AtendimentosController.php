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

    public function index($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        $this->checkFinalizado($atendimento);

        $pedidos = Pedido::where('atendimento_id', '=', $id)->get();

        return view('atendimentos.list', [
            'atendimento' => $atendimento,
            'pedidos'     => $pedidos
        ], 'main');
        // var_dump($atendimento);
        // die();
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
            $atendimentos = Atendimento::all();
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
    // ➕ ADICIONAR PRODUTO
    // ===========================

    public function adicionarProduto($id)
    {
        $request = Request::getInstance();

        $produto_id = $request->validate('produto_id', 'Produto')->required();
        $quantidade = $request->validate('quantidade', 'Quantidade')->required()->isInt()->min(1);

        if (!$request->validation()) {
            NotifyComponent::error("Erros de validação no formulário.");
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

            // 🔥 REGRA DE NEGÓCIO
            if ($atendimento->reservada !== null) {
                $atendimento->reservada = null;
                $atendimento->save();
            }

            $this->atualizarTotal($id);
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao adicionar produto: ' . $e->getMessage());
            return route('atendimentos', ['id' => $id])->redirect();
        }

        NotifyComponent::success('Produto adicionado com sucesso!');

        return route('atendimentos', ['id' => $id])->redirect();
    }

    // ===========================
    // ✏️ UPDATE
    // ===========================

    public function update($id)
    {
        $request = Request::getInstance();

        $produto_id = $request->validate('produto_id', 'Produto')->required();
        $quantidade = $request->validate('quantidade', 'Quantidade')->required()->isInt()->min(1);
        $valor_un = $request->validate('valor_un', 'Valor Unitário')->required();

        if (!$request->validation()) {
            NotifyComponent::error("Erros de validação no formulário.");
            return route('atendimentos', ['id' => $id])->redirect();
        }

        try {
            $pedido = $this->findOrFail(Pedido::class, $id);

            $pedido->produto_id = $produto_id;
            $pedido->quantidade = $quantidade;
            $pedido->valor_un = $valor_un;
            $pedido->save();

            $this->atualizarTotal($pedido->atendimento_id);
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao atualizar pedido: ' . $e->getMessage());
            return route('atendimentos', ['id' => $id])->redirect();
        }

        NotifyComponent::success("Pedido atualizado!");

        $pedido = $this->findOrFail(Pedido::class, $id);

        return route('atendimentos', [
            'id' => $pedido->atendimento_id
        ])->redirect();
    }



    // ===========================
    // 💾 ADICIONAR PEDIDO (A ATENDIMENTO EXISTENTE)
    // ===========================

    public function adicionarPedido($id)
    {
        $request = Request::getInstance();

        $atendimento_id = $request->validate('atendimento_id', 'Atendimento')->required()->isInt();
        $produto_id = $request->validate('produto_id', 'Produto')->required()->isInt();
        $quantidade = $request->validate('quantidade', 'Quantidade')->required()->isInt()->min(1);
        $valor_un = $request->validate('valor_un', 'Valor Unitário')->required();

        if (!$request->validation()) {
            NotifyComponent::error("Erros de validação no formulário.");
            return route('atendimentos', ['id' => $id])->redirect();
        }

        $atendimento = $this->findOrFail(Atendimento::class, $atendimento_id);
        $this->checkFinalizado($atendimento);

        try {
            $produto = $this->findOrFail(Produto::class, $produto_id);

            Pedido::create([
                'atendimento_id'   => $atendimento_id,
                'produto_id'       => $produto->id,
                'nome_produto'     => $produto->nome ?? null,
                'descricao_produto' => $produto->descricao ?? null,
                'quantidade'       => $quantidade,
                'valor_un'         => $valor_un,
                'situacao'         => 'Pedido',
            ]);

            if ($atendimento->reservada !== null) {
                $atendimento->reservada = null;
                $atendimento->save();
            }

            $this->atualizarTotal($request->atendimento_id);
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao criar pedido: ' . $e->getMessage());
            return route('atendimentos', ['id' => $request->atendimento_id])->redirect();
        }

        NotifyComponent::success("Pedido criado!");

        return route('atendimentos', [
            'id' => $request->atendimento_id
        ])->redirect();
    }

    // ===========================
    // ✅ FINALIZAR (DELETAR)
    // ===========================

    public function finalizarAtendimento($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        $pedidos = Pedido::where('atendimento_id', '=', $id)->get();

        if (empty($pedidos)) {
            NotifyComponent::error("Sem pedidos!");
            return route('atendimentos', ['id' => $id])->redirect();
        }

        try {
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
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao finalizar atendimento: ' . $e->getMessage());
            return route('atendimentos', ['id' => $id])->redirect();
        }

        NotifyComponent::success("Atendimento da mesa {$atendimento->mesa} finalizado e removido!");
        return Router::getRouteByName('home')->redirect();
    }

    // ===========================
    // ✅ RESERVADA (DELETAR)
    // ===========================

    public function reservadaAtendimento($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        // Não deletar o atendimento, apenas confirmar reserva
        // Deletar pagamentos relacionados se houver
        $pagamentos = Pagamento::where('atendimento_id', '=', $id)->get();
        foreach ($pagamentos as $pagamento) {
            $pagamento->delete();
        }

        NotifyComponent::success("Mesa {$atendimento->mesa} reservada!");
        return Router::getRouteByName('home')->redirect();
    }

    // ===========================
    // ❌ EXCLUIR PEDIDO
    // ===========================

    public function excluirPedido($id)
    {
        $pedido = $this->findOrFail(Pedido::class, $id);
        $atendimento_id = $pedido->atendimento_id;

        try {
            $pedido->delete();
            $this->atualizarTotal($atendimento_id);
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao excluir pedido: ' . $e->getMessage());
            return route('atendimentos', ['id' => $atendimento_id])->redirect();
        }

        NotifyComponent::success("Pedido excluído!");

        return route('atendimentos', ['id' => $atendimento_id])->redirect();
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
            NotifyComponent::error('Erro ao excluir produto do pedido: ' . $e->getMessage());
            return route('atendimentos', ['id' => $atendimento_id])->redirect();
        }

        NotifyComponent::success("Produto do pedido excluído!");

        return route('atendimentos', ['id' => $atendimento_id])->redirect();
    }

    // ===========================
    // 💰 PAGAMENTO
    // ===========================

    public function registrarPagamento($id)
    {
        $request = Request::getInstance();

        $valor = $request->validate('valor', 'Valor')->required();
        $metodo_pagamento = $request->validate('metodo_pagamento', 'Método de Pagamento')->required();

        if (!$request->validation()) {
            NotifyComponent::error("Erros de validação no formulário.");
            return route('atendimentos', ['id' => $id])->redirect();
        }

        try {
            $this->findOrFail(Atendimento::class, $id);

            Pagamento::create([
                'atendimento_id'   => $id,
                'valor'            => $valor,
                'metodo_pagamento' => $metodo_pagamento,
                'data_pagamento'   => date('Y-m-d H:i:s')
            ]);
        } catch (\Exception $e) {
            NotifyComponent::error('Erro ao registrar pagamento: ' . $e->getMessage());
            return route('atendimentos', ['id' => $id])->redirect();
        }

        NotifyComponent::success("Pagamento registrado!");

        return route('atendimentos', ['id' => $id])->redirect();
    }

    // ===========================
    // 📊 TOTAL (JSON)
    // ===========================

    public function calcularTotalAtendimento($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);
        $total = $atendimento->getTotal();

        echo json_encode([
            'total' => $total,
            'total_formatado' => 'R$ ' . number_format($total, 2, ',', '.')
        ]);
        exit;
    }

    // ===========================
    // ✏️ EDIT
    // ===========================

    public function edit($id)
    {
        $pedido = $this->findOrFail(Pedido::class, $id);

        return view('atendimentos.edit', [
            'pedido' => $pedido
        ], 'main');
    }

    // ===========================
    // 🗑️ DESTROY (DELETAR ATENDIMENTO COMPLETO)
    // ===========================

    public function destroy()
    {
        $request = Request::getInstance();

        $request->validate('id')->required()->isInt();

        if (!$request->validation()) {
            NotifyComponent::error("ID do atendimento inválido.");
            return $request->old()->redirect();
        }

        $atendimento = $this->findOrFail(Atendimento::class, $request->id);

        try {
            // Deletar todos os pedidos relacionados
            $pedidos = Pedido::where('atendimento_id', '=', $atendimento->id)->get();
            foreach ($pedidos as $pedido) {
                $pedido->delete();
            }

            // Deletar pagamentos relacionados
            $pagamentos = Pagamento::where('atendimento_id', '=', $atendimento->id)->get();
            foreach ($pagamentos as $pagamento) {
                $pagamento->delete();
            }

            // Deletar o atendimento
            $atendimento->delete();

            NotifyComponent::success("Atendimento da mesa {$atendimento->mesa} excluído com sucesso!");
        } catch (\Exception $e) {
            NotifyComponent::error("Erro ao excluir atendimento: " . $e->getMessage());
        }

        return Router::getRouteByName('home')->redirect();
    }
}
