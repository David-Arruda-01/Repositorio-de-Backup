<?php

require_once "../vendor/autoload.php";
use Fmk\Initialize;
use Fmk\Utils\Router;

Initialize::run();
Initialize::createConstants(include "Configs/app.php");
Initialize::createConstants(App\Models\Config::getConfig());
//A algum  proble com o Fmk\Utils\Config, por isso o getConfig não funciona...



Router::defineError404(function ($msg) {
  return view('errors.404',['msg'=>$msg], 'errors');
});
Router::defineError403(function ($msg) {
  return view('errors.403',['msg'=>$msg], 'errors');
});