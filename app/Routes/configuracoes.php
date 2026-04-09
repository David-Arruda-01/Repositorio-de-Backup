<?php

use Fmk\Utils\Router;

Router::get('configuracoes', [App\Controllers\ConfiguracoesController::class, 'index'])
    ->name('configuracoes');

Router::post('configuracoes', [App\Controllers\ConfiguracoesController::class, 'update']);
