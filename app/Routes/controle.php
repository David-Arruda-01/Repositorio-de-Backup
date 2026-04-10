<?php

use Fmk\Utils\Router;

Router::get('controle', [App\Controllers\ControleController::class, 'index'])
    ->name('controle');
