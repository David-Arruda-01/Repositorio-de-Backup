<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Funcionario;
use Fmk\MVC\Controller;
use Fmk\Utils\Request;

class FuncionariosController extends Controller
{
    public function __construct()
    {
        // Restrição para que somente admin ou root acessem a gestão de funcionários
        $this->middlewares('auth', 'admin');
    }
    /**
     * Listar Funcionários do Sistema
     * @return string
     */
    public function index()
    {
        return view('funcionarios.list', ['funcionarios' => Funcionario::all()], 'main');
    }

    /**
     * Exbir o formulário de adição de funcionário
     * @return string
     */
    public function create()
    {
        return view('funcionarios.cadastro', [], 'main');
    }

    /**
     * Salva o funcionário no banco de dados
     * @var mixed $id
     * @return void
     */
    public function storage()
    {
        $request = Request::getInstance();

        // Validação
        $nome = $request->validate('nome', 'Nome')->required();
        $cpf = $request->validate('cpf', 'CPF')->required();
        $login = $request->validate('login', 'Endereço de E-mail')->required();
        $password = $request->validate('password', 'Senha')->required()->confirme($request->confirmacao);

        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }

        // Pegando os valores CORRETAMENTE
        $data = [
            'nome' => $nome->getValue(),
            'cpf' => str_replace(['.', '-'], '', $cpf->getValue()),
            'login' => $login->getValue(),
            'password' => password_hash($password->getValue(), PASSWORD_DEFAULT),
            'rg' => $request->rg,
            'rg_expedidor' => $request->rg_expedidor,
            'telefone' => $request->telefone
        ];

        $funcionario = new Funcionario();
        $funcionario->save($data);

        NotifyComponent::success("Funcionário cadastrado com sucesso!");
        return route('funcionario.list')->redirect();
    }
    public function update($id)
    {
        $request = Request::getInstance();
        $request->validate('id')->required()->isInt()->dbExists(Funcionario::class)->confirme($id);
        $nome = $request->validate('nome', 'Nome')->required();
        $cpf = str_replace('.', '', str_replace('-', '', $request->validate('cpf', 'CPF')->required()));
        $login = $request->validate('login', 'Endereço de E-mail')->required();
        $rg = $request->rg;
        $rg_expedidor = $request->rg_expedidor;
        $telefone = $request->telefone;
        $data = compact('nome', 'cpf', 'login', 'rg', 'rg_expedidor', 'telefone');
        if (!empty(trim($request->password))) {
            $password = $request->validate('password', 'Senha')->confirme($request->confirmacao);
            $password = password_hash($password, PASSWORD_DEFAULT);
            $data['password'] = $password;
        }
        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }
        $funcionario = Funcionario::find($request->id);
        $funcionario->save($data);
        NotifyComponent::success("Funcionário cadastrado com sucesso!");
        return route('funcionario.list')->redirect();
    }
    /**
     * Abre a tela de edição de um determinado funcionário
     * @param mixed $id
     * @return string
     */
    public function edit($id)
    {
        $funcionario = Funcionario::find($id);
        if ($funcionario === false) {
            NotifyComponent::error("Funcionário não encontrado.");
            return route('funcionario.list')->redirect();
        }
        return view('funcionarios.edit', $funcionario->toArray(), 'main');
    }
    /**
     * Apaga um funcionário da base de dados
     * @return void
     */
    public function delete()
    {
        $request = Request::getInstance();
        $request->validate('id')->required()->isInt()->dbExists(Funcionario::class);
        if (!$request->validation()) {
            NotifyComponent::error("Existem erros de preenchimento no formulário.");
            return $request->old()->redirect();
        }
        if ($request->id == user()->id) {
            NotifyComponent::error("Você não pode excluir a conta do usuário logado!");
            return $request->old()->redirect();
        }
        Funcionario::find($request->id)->delete();
        NotifyComponent::success("Funcionário Excluído com sucesso!");
        return $request->old()->redirect();
    }
}
