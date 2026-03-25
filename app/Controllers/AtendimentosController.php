<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Atendimento;
use App\Models\Pagamento;
use App\Models\Produto;
use Fmk\MVC\Controller;
use App\Models\Pedido;
use Fmk\Utils\Request;
use Fmk\Utils\Router;

class AtendimentosController extends Controller
{
    public function __construct()
    {
        $this->middlewares('auth');
    }

    public function index($id)
    {
        // 🔥 Busca atendimento
        $atendimento = Atendimento::find($id);

        // if (!$atendimento) {
        //     return redirect()->back();
        // }

        // 🔥 Lazy loading (ou eager se tiver)
        $pedidos = $atendimento->pedidos();

        return view('atendimentos.list', [
            'atendimento' => $atendimento,
            'pedidos' => $pedidos
        ], 'main');
    }

    public function create()
    {
        try {
            $pedidos = Pedido::all();
            return view('atendimentos.card-list-pedidos', [
                'pedidos' => $pedidos
            ], 'main');
        } catch (\Exception $e) {
            return NotifyComponent::error('Erro ao carregar a página de criação de atendimento: ') . $e->getMessage();
        }
    }

    public function adicionarProduto($id)
    {
        $request = Request::getInstance();
        $request->validate([
            'produto_id' => 'required|exists:produtos,id',
            'quantidade' => 'required|integer|min:1',
        ]);

        $produto = Produto::find($request->produto_id);

        Pedido::create([
            'atendimento_id' => $id,
            'produto_id'    => $request->produto_id,
            'quantidade'    => $request->quantidade,
            'valor_un'      => $produto->preco,
        ]);

        NotifyComponent::success('Produto adicionado com sucesso!');

        return (route('atendimentos', ['id' => $id]))->redirect();
    }

    public function update($id)
    {
        $request = Request::getInstance();
        $atendimento_id = $request->validate('atendimento_id')->required()->isInt()->dbExists(Atendimento::class);

        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }

        $produto_id = $request->validate(name: 'produto_id', label: 'Produto')->required();
        $valor_un   = $request->validate(name: 'valor_un', label: 'Valor Unitário')->isFloat()->required();
        $quantidade = $request->validate(name: 'quantidade', label: 'Quantidade')->required();

        $data    = compact('atendimento_id', 'produto_id', 'valor_un', 'quantidade');
        $produto = Pedido::find($id);

        if (!$produto) {
            NotifyComponent::error("Pedido não encontrado.");
            return $request->old()->redirect();
        }

        $produto->update($data);
        NotifyComponent::success("Pedido atualizado com sucesso!");

        return (route('atendimentos', ['id' => $id]))->redirect();
    }

    public function storage()
    {
        $request = Request::getInstance();
        $atendimento_id = $request->validate(name: 'atendimento_id', label: 'Id do atendimento')->required();
        $produto_id     = $request->validate(name: 'produto_id', label: 'Produto')->required();
        $valor_un       = $request->validate(name: 'valor_un', label: 'Valor Unitário')->isFloat()->required();
        $quantidade     = $request->validate(name: 'quantidade', label: 'Quantidade')->required();

        if (!$request->validation()) {
            NotifyComponent::error(msg: "Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }

        $data = compact('atendimento_id', 'valor_un', 'produto_id', 'quantidade');
        Pedido::create($data);

        NotifyComponent::success("Pedido cadastrado com sucesso!");

        return (route('atendimentos', ['id' => $atendimento_id]))->redirect();
    }

    public function atendimentoId($id)
    {
        $nMesas = $_SESSION['N_MESAS'] ?? constant('N_MESAS');

        if ($id < 1 || $id > $nMesas) {
            NotifyComponent::error("Número de mesa inválido.");
            return Router::getRouteByName('home')->redirect();
        }

        // 🔍 Busca atendimento aberto da mesa
        $atendimento = Atendimento::where('mesa', '=', $id)
            ->where('pagamento_data', 'IS', null)
            ->first();

        // 🆕 Cria se não existir
        if (!$atendimento) {
            $atendimento = Atendimento::create([
                'mesa' => $id
            ]);
        }


        // 🔥 Aqui está a correção principal
        $dados = $atendimento->getDadosCompletos();
        // var_dump($atendimento->getDadosCompletos());
        // die;
        return view('atendimentos.list', [
            'atendimento' => $dados['atendimento'],
            'pedidos'     => $dados['pedidos']
        ], 'main');
    }

    public function finalizarAtendimento($id)
    {
        $atendimento = Atendimento::find($id);

        if (!$atendimento) {
            NotifyComponent::error("Atendimento não encontrado.");
            return Router::getRouteByName('home')->redirect();
        }

        $atendimento->update([
            'pagamento_data' => date('Y-m-d H:i:s')
        ]);

        NotifyComponent::success("Atendimento da mesa {$atendimento->mesa} finalizado com sucesso!");
        return Router::getRouteByName('home')->redirect();
    }

    public function excluirPedido($id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            NotifyComponent::error("Pedido não encontrado.");
            return Router::getRouteByName('home')->redirect();
        }

        $atendimento_id = $pedido->atendimento_id;
        $pedido->delete();

        NotifyComponent::success("Pedido excluído com sucesso!");
        return (route('atendimentos', ['id' => $atendimento_id]))->redirect();
    }

    public function registrarPagamento($id)
    {
        $request = Request::getInstance();
        $atendimento = Atendimento::find($id);

        if (!$atendimento) {
            NotifyComponent::error("Atendimento não encontrado.");
            return Router::getRouteByName('home')->redirect();
        }

        $valor = $request->validate(name: 'valor', label: 'Valor')->required()->isFloat();
        $metodo = $request->validate(name: 'metodo_pagamento', label: 'Método de Pagamento')->required();

        if (!$request->validation()) {
            NotifyComponent::error("Existem erros no formulário.");
            return $request->old()->redirect();
        }

        Pagamento::create([
            'atendimento_id' => $id,
            'valor' => $valor,
            'metodo_pagamento' => $metodo,
            'data_pagamento' => date('Y-m-d H:i:s')
        ]);

        NotifyComponent::success("Pagamento registrado com sucesso!");
        return (route('atendimentos', ['id' => $id]))->redirect();
    }

    public function calcularTotalAtendimento($id)
    {
        $atendimento = Atendimento::find($id);

        if (!$atendimento) {
            return json_encode(['erro' => 'Atendimento não encontrado']);
        }

        $total = $atendimento->getTotal();
        return json_encode(['total' => $total]);
    }

    public function edit($id)
    {
        $pedido = Pedido::find($id);

        if (!$pedido) {
            NotifyComponent::error("Pedido não encontrado.");
            return Router::getRouteByName('home')->redirect();
        }

        return view('atendimentos.edit', ['pedido' => $pedido], 'main');
    }

    public function delete()
    {
        $request = Request::getInstance();
        $id = $request->validate(name: 'id', label: 'ID do Pedido')->required();

        if (!$request->validation()) {
            NotifyComponent::error("ID inválido.");
            return $request->old()->redirect();
        }

        $pedido = Pedido::find($id);

        if (!$pedido) {
            NotifyComponent::error("Pedido não encontrado.");
            return Router::getRouteByName('home')->redirect();
        }

        $atendimento_id = $pedido->atendimento_id;
        $pedido->delete();

        NotifyComponent::success("Pedido deletado com sucesso!");
        return (route('atendimentos', ['id' => $atendimento_id]))->redirect();
    }
}
