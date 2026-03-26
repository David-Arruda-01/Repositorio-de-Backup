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

        return view('atendimentos.list', [
            'atendimento' => $atendimento,
            'pedidos'     => $atendimento->pedidos
        ], 'main');
    }

    /**
     * Tela de criação (listar pedidos disponíveis)
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

        DB::transaction(function () use ($request, $id) {

            $produto = Produto::findOrFail($request->produto_id);

            Pedido::create([
                'atendimento_id' => $id,
                'produto_id'     => $produto->id,
                'quantidade'     => $request->quantidade,
                'valor_un'       => $produto->preco,
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
        });

        NotifyComponent::success("Pedido atualizado com sucesso!");

        return route('atendimentos', ['id' => $id])->redirect();
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

        DB::transaction(function () use ($request) {

            Pedido::create([
                'atendimento_id' => $request->atendimento_id,
                'produto_id'     => $request->produto_id,
                'quantidade'     => $request->quantidade,
                'valor_un'       => $request->valor_un,
            ]);
        });

        NotifyComponent::success("Pedido cadastrado com sucesso!");

        return route('atendimentos', ['id' => $request->atendimento_id])->redirect();
    }

    /**
     * Buscar ou criar atendimento por mesa
     */
    public function atendimentoId($id)
    {
        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');

        if ($id < 1 || $id > $nMesas) {
            NotifyComponent::error("Número de mesa inválido.");
            return Router::getRouteByName('home')->redirect();
        }

        $atendimento = Atendimento::where('mesa', $id)
            ->whereNull('pagamento_data')
            ->first();

        if (!$atendimento) {
            $atendimento = Atendimento::create([
                'mesa' => $id
            ]);
        }

        $atendimento->load('pedidos.produto');

        return view('atendimentos.list', [
            'atendimento' => $atendimento,
            'pedidos'     => $atendimento->pedidos
        ], 'main');
    }

    /**
     * Finalizar atendimento
     */
    public function finalizarAtendimento($id)
    {
        $atendimento = Atendimento::findOrFail($id);

        DB::transaction(function () use ($atendimento) {
            $atendimento->update([
                'pagamento_data' => date('Y-m-d H:i:s')
            ]);
        });

        NotifyComponent::success("Atendimento da mesa {$atendimento->mesa} finalizado com sucesso!");

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
     * Deletar pedido (alternativo via form)
     */
    public function destroy()
    {
        $request = Request::getInstance();

        $request->validate([
            'id' => 'required|exists:pedidos,id'
        ]);

        DB::transaction(function () use ($request) {

            $pedido = Pedido::findOrFail($request->id);
            $atendimento_id = $pedido->atendimento_id;

            $pedido->delete();

            NotifyComponent::success("Pedido deletado com sucesso!");

            return route('atendimentos', ['id' => $atendimento_id])->redirect();
        });
    }
}
