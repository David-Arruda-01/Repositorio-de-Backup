<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Funcionario;
use Fmk\MVC\Controller;
use Fmk\Utils\Request;
use Fmk\Utils\Router;

class LoginController extends Controller
{
    public function index()
    {
        return view('auth.login', [], 'blank');
    }

    public function logar()
    {
        $request = Request::getInstance();

        $login = $request->validate('login', 'Usuário')->required();
        $senha = $request->validate('senha', 'Senha')->required();

        if (!$request->validation()) {
            NotifyComponent::error('Existem erros de preenchimento no formulário');
            return Router::getRouteByName('login')->redirect();
        }

        // Verifica se o usuário existe
        $userExists = Funcionario::where('login', '=', $login->getValue())->first();

        if (!$userExists) {
            NotifyComponent::error('Usuário não encontrado.');
            return Router::getRouteByName('login')->redirect();
        }

        // Autenticação
        if (Funcionario::Auth($login->getValue(), $senha->getValue())) {
            NotifyComponent::success('Bem-vindo!');
            return Router::getRouteByName('home')->redirect();
        }

        NotifyComponent::warning('Senha incorreta!');
        return Router::getRouteByName('login')->redirect();
    }

    public function logout()
    {
        user()->logout();

        NotifyComponent::info('Você saiu do sistema.');
        return Router::getRouteByName('login')->redirect();
    }
}
