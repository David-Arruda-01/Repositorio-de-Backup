<?php

require_once "../vendor/autoload.php";
use Fmk\Initialize;
use Fmk\Utils\Router;

Initialize::run();
// Carrega configurações (arquivo + banco de dados) e cria constantes
Initialize::createConstants(App\Models\Config::getConfig());



Router::defineError404(function ($msg) {
  return view('errors.404',['msg'=>$msg], 'errors');
});
Router::defineError403(function ($msg) {
  return view('errors.403',['msg'=>$msg], 'errors');
});