<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Produto;
use Fmk\MVC\Controller;
use Fmk\Utils\Request;

class ProdutosController extends Controller
{
    public function __construct()
    {
        $this->middlewares('auth');
    }

    /**
     * Listar Produtos do Sistema
     * @return string
     */
    public function index()
    {
        $produtos = Produto::all();
        return view('produtos.list', compact('produtos'), 'main');
    }

    /**
     * Exibir o formulário de adição de produto
     * @return string
     */
    public function create()
    {
        return view('produtos.cadastro', [], 'main');
    }

    /**
     * Salva o produto no banco de dados
     * @return void
     */
    public function storage()
    {
        $request = Request::getInstance();
        
        $nome = $request->validate(name: 'nome', label: 'Nome')->required();
        $descricao = $request->validate(name: 'descricao', label: 'Descrição')->required();
        $valor_un = $request->validate(name: 'valor_un', label: 'Valor Unitário')->isFloat()->required();
        $unidade_medida = $request->validate(name: 'unidade_medida', label: 'Unidade de Medida')->required();
        $disponivel = ($request->disponivel) ? 1 : 0;
        
        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }
       
        $data = compact('nome', 'descricao', 'valor_un', 'unidade_medida', 'disponivel');
        Produto::create($data);
        
        NotifyComponent::success("Produto $nome cadastrado com sucesso!");
        return route('produto.list')->redirect();
    }

    /**
     * Atualiza um produto existente
     * @param mixed $id
     * @return void
     */
    public function update($id)
    {
        $request = Request::getInstance();
        
        // Validação do ID
        $request->validate('id')->required()->isInt()->dbExists(Produto::class);
        
        $nome = $request->validate('nome', 'Nome')->required();
        $descricao = $request->validate('descricao', 'Descrição')->required();
        $valor_un = $request->validate('valor_un', 'Valor Unitário')->isFloat()->required();
        $unidade_medida = $request->validate('unidade_medida', 'Unidade de Medida')->required();
        $disponivel = ($request->disponivel) ? 1 : 0;
        
        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }
        
        // Verifica se o ID da URL corresponde ao ID do formulário
        if ($request->id != $id) {
            NotifyComponent::error("ID do produto inválido.");
            return route('produto.list')->redirect();
        }
        
        $data = compact('nome', 'descricao', 'valor_un', 'unidade_medida', 'disponivel');
        $produto = Produto::find($id);
        
        if (!$produto) {
            NotifyComponent::error("Produto não encontrado.");
            return route('produto.list')->redirect();
        }
        
        $produto->save($data);
        NotifyComponent::success("Produto $nome alterado com sucesso!");
        return route('produto.list')->redirect();
    }

    /**
     * Abre a tela de edição de um determinado produto
     * @param mixed $id
     * @return string
     */
    public function edit($id)
    {
        $produto = Produto::find($id);
        
        if (!$produto) {
            NotifyComponent::error("Produto não encontrado.");
            route('produto.list')->redirect();
        }
        
        return view('produtos.edit', $produto->toArray(), 'main');
    }

    /**
     * Apaga um produto da base de dados
     * @return void
     */
    public function delete()
    {
        $request = Request::getInstance();
        
        $request->validate('id')->required()->isInt()->dbExists(Produto::class);
        
        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }

        $produto = Produto::find($request->id);
        
        if ($produto) {
            $produto->delete();
            NotifyComponent::success("Produto excluído com sucesso!");
        } else {
            NotifyComponent::error("Produto não encontrado.");
        }
        
        return route('produto.list')->redirect();
    }
}