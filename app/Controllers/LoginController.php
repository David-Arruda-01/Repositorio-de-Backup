<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Funcionario;
use Fmk\MVC\Controller;
use Fmk\Utils\Request;
use Fmk\Utils\Router;
use Fmk\Utils\Session;

class LoginController extends Controller{


    public function index(){
        return view('auth.login',[''],'blank');
    }

    public function logar(){
        $request = Request::getInstance();
        
        $email = $request->validate('email', 'E-mail')->required();
        $login = $request->validate('login', 'Usuário')->required();
        $senha = $request->validate('senha', 'Senha')->required();

        if(!$request->validation()){
            NotifyComponent::error('Existem erros de preenchimento no formulário');
            return Router::getRouteByName('login')->redirect();
        }

        // Verifica se o e-mail existe no banco de dados
        $userExists = Funcionario::where('login', '=', $email->getValue())->first();
        
        if (!$userExists) {
            NotifyComponent::error('O e-mail informado não está cadastrado no sistema.');
            return Router::getRouteByName('login')->redirect();
        }

        if(Funcionario::Auth($login->getValue(), $senha->getValue())){
            NotifyComponent::success('Bem vindo !!!');
            return Router::getRouteByName('home')->redirect();
        }

        NotifyComponent::warning('Credenciais inválidas!');
        return Router::getRouteByName('login')->redirect();
    }
    public function logout(){
        $request =  Request::getInstance();
        user()->logout();
        if($request->validation()){
            NotifyComponent::info('tchau!!!');
            route('login')->redirect();
        }
        NotifyComponent::error('Falha ao tentar sair!!! "falha no logout"...');
        return Router::getRouteByName('logout')->redirect();
        Request::getInstance();
        Session::getInstance()->userUnregister();
        return Router::getRouteByName('login')->redirect();
    }
}