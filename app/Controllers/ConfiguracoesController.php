<?php

namespace App\Controllers;

use App\Components\NotifyComponent;
use App\Models\Config;
use Fmk\MVC\Controller;
class ConfiguracoesController extends Controller{
    public function __construct(){
        $this->middlewares('auth');

    }
    public function index(){
        return view('configuracoes.list',['configs' => Config::all()],'main');
    }
    public function update(){
        $configs = Config::all();
        $request = $this->request();
        foreach($configs as $config){
            $config->value = $request->validate($config->name,$config->label)->required();
        }
        if($request->validation()){
            foreach($configs as $config){
                $config->save();
            }
            NotifyComponent::success('Configuraçoes atualizadas',' Sucesso!');
            return route('home')->redirect();
        }
        NotifyComponent::error('Existem erros no preenchimento do formulário',' Falha!');
            return $request->old()->redirect();
    }

}