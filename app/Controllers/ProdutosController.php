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
     * Listar Produtos do Sistema (apenas não excluídos)
     * @return string
     */
    public function index()
    {
        // Busca apenas produtos que não foram excluídos (soft delete)
        $produtos = Produto::where('exclusao_data', '=', null)->get();
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
        $unidade_medida = $this->normalizeUnidadeMedida($unidade_medida);

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
        $unidade_medida = $this->normalizeUnidadeMedida($unidade_medida);

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

        $produto->nome = $nome;
        $produto->descricao = $descricao;
        $produto->valor_un = $valor_un;
        $produto->unidade_medida = $unidade_medida;
        $produto->disponivel = $disponivel;
        $produto->save();
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
            return route('produto.list')->redirect();
        }

        return view('produtos.edit', $produto->toArray(), 'main');
    }

    private function normalizeUnidadeMedida($value)
    {
        $value = mb_strtolower(trim($value));

        if ($value === 'unidade') {
            return 'Unidade';
        }

        if ($value === 'quilo') {
            return 'Quilo';
        }

        return 'Grama';
    }

    /**
     * Apaga um produto da base de dados (soft delete)
     * @return void
     */
    public function delete()
    {
        try {
            $request = Request::getInstance();

            // Validação simples do ID
            $id = $request->id ?? null;

            if (!$id || !is_numeric($id)) {
                NotifyComponent::error("ID do produto inválido.");
                return route('produto.list')->redirect();
            }

            // Busca o produto
            $produto = Produto::find($id);

            if (!$produto) {
                NotifyComponent::error("Produto não encontrado.");
                return route('produto.list')->redirect();
            }

            // Verifica se já foi excluído
            if ($produto->exclusao_data !== null) {
                NotifyComponent::error("Este produto já foi excluído.");
                return route('produto.list')->redirect();
            }

            $nomeProduto = $produto->nome;

            // Soft delete - marca como excluído sem remover do banco
            $produto->exclusao_data = date('Y-m-d H:i:s');
            $saved = $produto->save();

            if ($saved) {
                NotifyComponent::success("Produto '$nomeProduto' excluído com sucesso!");
            } else {
                NotifyComponent::error("Falha ao atualizar a data de exclusão do produto.");
            }
        } catch (\Exception $e) {
            NotifyComponent::error("Erro ao excluir produto: " . $e->getMessage());
        }

        return route('produto.list')->redirect();
    }
}
