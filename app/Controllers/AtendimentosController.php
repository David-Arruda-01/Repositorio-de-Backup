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
use Illuminate\Support\Facades\DB;

class AtendimentosController extends Controller
{
    public function __construct()
    {
        $this->middlewares('auth');
    }

    /**
     * Listagem de um atendimento
     */
    public function index($id)
    {
        $atendimento = Atendimento::with(['pedidos.produto'])->findOrFail($id);

        // 🔥 bloqueia acesso a atendimento já finalizado
        if ($atendimento->pagamento_data !== null) {
            NotifyComponent::error("Atendimento já finalizado!");
            return Router::getRouteByName('home')->redirect();
        }

        return view('atendimentos.list', [
            'atendimento' => $atendimento,
            'pedidos'     => $atendimento->pedidos
        ], 'main');
    }

    /**
     * Tela de criação (listar produtos/pedidos disponíveis)
     */
    public function create()
    {
        try {
            $pedidos = Pedido::all();

            return view('atendimentos.card-list-pedidos', [
                'pedidos' => $pedidos
            ], 'main');

        } catch (\Exception $e) {
            return NotifyComponent::error('Erro ao carregar a página: ' . $e->getMessage());
        }
    }

    /**
     * Adicionar produto ao atendimento
     */
    public function adicionarProduto($id)
    {
        $request = Request::getInstance();

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $atendimento = Atendimento::findOrFail($id);

        // 🔥 bloqueia se já estiver finalizado
        if ($atendimento->pagamento_data !== null) {
            NotifyComponent::error("Atendimento já finalizado!");
            return Router::getRouteByName('home')->redirect();
        }

        DB::transaction(function () use ($request, $id) {

            $produto = Produto::findOrFail($request->produto_id);

            Pedido::create([
                'atendimento_id' => $id,
                'produto_id'     => $produto->id,
                'quantidade'     => $request->quantidade,
                'valor_un'       => $produto->preco,
            ]);

            // 🔥 atualiza total automaticamente
            $atendimento = Atendimento::findOrFail($id);
            $atendimento->update([
                'valor_total' => $atendimento->getTotal()
            ]);
        });

        NotifyComponent::success('Produto adicionado com sucesso!');

        return route('atendimentos', ['id' => $id])->redirect();
    }

    /**
     * Atualizar pedido
     */
    public function update($id)
    {
        $request = Request::getInstance();

        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
            'valor_un'   => 'required|float'
        ]);

        DB::transaction(function () use ($request, $id) {

            $pedido = Pedido::findOrFail($id);

            $pedido->update([
                'produto_id' => $request->produto_id,
                'quantidade' => $request->quantidade,
                'valor_un'   => $request->valor_un,
            ]);

            // 🔥 atualiza total do atendimento
            $atendimento = Atendimento::findOrFail($pedido->atendimento_id);
            $atendimento->update([
                'valor_total' => $atendimento->getTotal()
            ]);
        });

        NotifyComponent::success("Pedido atualizado com sucesso!");

        $pedido = Pedido::findOrFail($id);
        return route('atendimentos', ['id' => $pedido->atendimento_id])->redirect();
    }

    /**
     * Criar pedido (store)
     */
    public function store()
    {
        $request = Request::getInstance();

        $request->validate([
            'atendimento_id' => 'required|exists:atendimentos,id',
            'produto_id'     => 'required|exists:produtos,id',
            'quantidade'     => 'required|integer|min:1',
            'valor_un'       => 'required|float'
        ]);

        $atendimento = Atendimento::findOrFail($request->atendimento_id);

        if ($atendimento->pagamento_data !== null) {
            NotifyComponent::error("Atendimento já finalizado!");
            return Router::getRouteByName('home')->redirect();
        }

        DB::transaction(function () use ($request) {

            Pedido::create([
                'atendimento_id' => $request->atendimento_id,
                'produto_id'     => $request->produto_id,
                'quantidade'     => $request->quantidade,
                'valor_un'       => $request->valor_un,
            ]);

            // 🔥 atualiza total
            $atendimento = Atendimento::findOrFail($request->atendimento_id);
            $atendimento->update([
                'valor_total' => $atendimento->getTotal()
            ]);
        });

        NotifyComponent::success("Pedido cadastrado com sucesso!");

        return route('atendimentos', ['id' => $request->atendimento_id])->redirect();
    }

    /**
     * Finalizar atendimento
     */
    public function finalizarAtendimento($id)
    {
        $atendimento = Atendimento::with('pedidos')->findOrFail($id);

        if ($atendimento->pedidos->isEmpty()) {
            NotifyComponent::error("Não é possível finalizar um atendimento sem pedidos.");
            return route('atendimentos', ['id' => $id])->redirect();
        }

        DB::transaction(function () use ($atendimento) {
            $atendimento->update([
                'pagamento_data' => date('Y-m-d H:i:s'),
                'valor_total'    => $atendimento->getTotal()
            ]);
        });

        NotifyComponent::success("Mesa {$atendimento->mesa} finalizada com sucesso!");

        return Router::getRouteByName('home')->redirect();
    }

    /**
     * Excluir pedido
     */
    public function excluirPedido($id)
    {
        $pedido = Pedido::findOrFail($id);
        $atendimento_id = $pedido->atendimento_id;

        DB::transaction(function () use ($pedido) {
            $pedido->delete();
        });

        // 🔥 atualiza total após exclusão
        $atendimento = Atendimento::findOrFail($atendimento_id);
        $atendimento->update([
            'valor_total' => $atendimento->getTotal()
        ]);

        NotifyComponent::success("Pedido excluído com sucesso!");

        return route('atendimentos', ['id' => $atendimento_id])->redirect();
    }

    /**
     * Registrar pagamento
     */
    public function registrarPagamento($id)
    {
        $request = Request::getInstance();

        $request->validate([
            'valor' => 'required|float',
            'metodo_pagamento' => 'required'
        ]);

        DB::transaction(function () use ($request, $id) {

            Atendimento::findOrFail($id);

            Pagamento::create([
                'atendimento_id'    => $id,
                'valor'             => $request->valor,
                'metodo_pagamento'  => $request->metodo_pagamento,
                'data_pagamento'    => date('Y-m-d H:i:s')
            ]);
        });

        NotifyComponent::success("Pagamento registrado com sucesso!");

        return route('atendimentos', ['id' => $id])->redirect();
    }

    /**
     * Calcular total do atendimento
     */
    public function calcularTotalAtendimento($id)
    {
        $atendimento = Atendimento::findOrFail($id);

        return response()->json([
            'total' => $atendimento->getTotal()
        ]);
    }

    /**
     * Editar pedido
     */
    public function edit($id)
    {
        $pedido = Pedido::findOrFail($id);

        return view('atendimentos.edit', [
            'pedido' => $pedido
        ], 'main');
    }

    /**
     * Deletar pedido (via form)
     */
    public function destroy()
    {
        $request = Request::getInstance();

        $request->validate([
            'id' => 'required|exists:pedidos,id'
        ]);

        $pedido = Pedido::findOrFail($request->id);
        $atendimento_id = $pedido->atendimento_id;

        DB::transaction(function () use ($pedido) {
            $pedido->delete();
        });

        // 🔥 atualiza total
        $atendimento = Atendimento::findOrFail($atendimento_id);
        $atendimento->update([
            'valor_total' => $atendimento->getTotal()
        ]);

        NotifyComponent::success("Pedido deletado com sucesso!");

        return route('atendimentos', ['id' => $atendimento_id])->redirect();
    }
}
