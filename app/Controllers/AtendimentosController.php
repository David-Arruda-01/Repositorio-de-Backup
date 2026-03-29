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
        $atendimento = $this->findOrFail(Atendimento::class, $atendimento_id);

        $atendimento->update([
            'valor_total' => $atendimento->getTotal()
        ]);
    }

    // ===========================
    // 📋 INDEX
    // ===========================

    public function index($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        $this->checkFinalizado($atendimento);

        $pedidos = Pedido::where('atendimento_id', $id)->get();

        return view('atendimentos.list', [
            'atendimento' => $atendimento,
            'pedidos'     => $pedidos
        ], 'main');
    }

    // ===========================
    // ➕ CREATE
    // ===========================

    public function create()
    {
        try {
            $pedidos = Pedido::all();

            return view('atendimentos.card-list-pedidos', [
                'pedidos' => $pedidos
            ], 'main');
        } catch (\Exception $e) {
            NotifyComponent::error('Erro: ' . $e->getMessage());
        }
    }

    // ===========================
    // ➕ ADICIONAR PRODUTO
    // ===========================

    public function adicionarProduto($id)
    {
        $request = Request::getInstance();

        $request->validate([
            'produto_id' => 'required',
            'quantidade' => 'required|integer|min:1',
        ]);

        $atendimento = $this->findOrFail(Atendimento::class, $id);
        $this->checkFinalizado($atendimento);

        DB::transaction(function () use ($request, $id) {

            $produto = $this->findOrFail(Produto::class, $request->produto_id);

            Pedido::create([
                'atendimento_id' => $id,
                'produto_id'     => $produto->id,
                'quantidade'     => $request->quantidade,
                'valor_un'       => $produto->preco,
            ]);

            $this->atualizarTotal($id);
        });

        NotifyComponent::success('Produto adicionado com sucesso!');

        return route('atendimentos', ['id' => $id])->redirect();
    }

    // ===========================
    // ✏️ UPDATE
    // ===========================

    public function update($id)
    {
        $request = Request::getInstance();

        $request->validate([
            'produto_id' => 'required',
            'quantidade' => 'required|integer|min:1',
            'valor_un'   => 'required'
        ]);

        DB::transaction(function () use ($request, $id) {

            $pedido = $this->findOrFail(Pedido::class, $id);

            $pedido->update([
                'produto_id' => $request->produto_id,
                'quantidade' => $request->quantidade,
                'valor_un'   => $request->valor_un,
            ]);

            $this->atualizarTotal($pedido->atendimento_id);
        });

        NotifyComponent::success("Pedido atualizado!");

        $pedido = $this->findOrFail(Pedido::class, $id);

        return route('atendimentos', [
            'id' => $pedido->atendimento_id
        ])->redirect();
    }

    // ===========================
    // 💾 STORE
    // ===========================

    public function store()
    {
        $request = Request::getInstance();

        $request->validate([
            'atendimento_id' => 'required',
            'produto_id'     => 'required',
            'quantidade'     => 'required|integer|min:1',
            'valor_un'       => 'required'
        ]);

        $atendimento = $this->findOrFail(Atendimento::class, $request->atendimento_id);
        $this->checkFinalizado($atendimento);

        DB::transaction(function () use ($request) {

            Pedido::create([
                'atendimento_id' => $request->atendimento_id,
                'produto_id'     => $request->produto_id,
                'quantidade'     => $request->quantidade,
                'valor_un'       => $request->valor_un,
            ]);

            $this->atualizarTotal($request->atendimento_id);
        });

        NotifyComponent::success("Pedido criado!");

        return route('atendimentos', [
            'id' => $request->atendimento_id
        ])->redirect();
    }

    // ===========================
    // ✅ FINALIZAR
    // ===========================

    public function finalizarAtendimento($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        $pedidos = Pedido::where('atendimento_id', $id)->get();

        if (empty($pedidos)) {
            NotifyComponent::error("Sem pedidos!");
            return route('atendimentos', ['id' => $id])->redirect();
        }

        DB::transaction(function () use ($atendimento) {
            $atendimento->update([
                'pagamento_data' => date('Y-m-d H:i:s'),
                'valor_total'    => $atendimento->getTotal()
            ]);
        });

        NotifyComponent::success("Mesa {$atendimento->mesa} finalizada!");

        return Router::getRouteByName('home')->redirect();
    }

    // ===========================
    // ❌ EXCLUIR PEDIDO
    // ===========================

    public function excluirPedido($id)
    {
        $pedido = $this->findOrFail(Pedido::class, $id);
        $atendimento_id = $pedido->atendimento_id;

        DB::transaction(function () use ($pedido) {
            $pedido->delete();
        });

        $this->atualizarTotal($atendimento_id);

        NotifyComponent::success("Pedido excluído!");

        return route('atendimentos', ['id' => $atendimento_id])->redirect();
    }

    // ===========================
    // 💰 PAGAMENTO
    // ===========================

    public function registrarPagamento($id)
    {
        $request = Request::getInstance();

        $request->validate([
            'valor' => 'required',
            'metodo_pagamento' => 'required'
        ]);

        DB::transaction(function () use ($request, $id) {

            $this->findOrFail(Atendimento::class, $id);

            Pagamento::create([
                'atendimento_id'   => $id,
                'valor'            => $request->valor,
                'metodo_pagamento' => $request->metodo_pagamento,
                'data_pagamento'   => date('Y-m-d H:i:s')
            ]);
        });

        NotifyComponent::success("Pagamento registrado!");

        return route('atendimentos', ['id' => $id])->redirect();
    }

    // ===========================
    // 📊 TOTAL (JSON)
    // ===========================

    public function calcularTotalAtendimento($id)
    {
        $atendimento = $this->findOrFail(Atendimento::class, $id);

        echo json_encode([
            'total' => $atendimento->getTotal()
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
    // 🗑️ DESTROY
    // ===========================

    public function destroy()
    {
        $request = Request::getInstance();

        $request->validate([
            'id' => 'required'
        ]);

        $pedido = $this->findOrFail(Pedido::class, $request->id);
        $atendimento_id = $pedido->atendimento_id;

        DB::transaction(function () use ($pedido) {
            $pedido->delete();
        });

        $this->atualizarTotal($atendimento_id);

        NotifyComponent::success("Pedido deletado!");

        return route('atendimentos', ['id' => $atendimento_id])->redirect();
    }
}
