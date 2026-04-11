<?php

use Fmk\Utils\Router;

Router::get('funcionarios', [App\Controllers\FuncionariosController::class, 'index'])
    ->name('funcionario.list');

Router::get('funcionario/novo', [App\Controllers\FuncionariosController::class, 'create'])
    ->name('funcionario.create');

Router::post('funcionario', [App\Controllers\FuncionariosController::class, 'storage'])
    ->name('funcionario.storage');

Router::get('funcionario/{id}/editar', [App\Controllers\FuncionariosController::class, 'edit'])
    ->name('funcionario.edit');

Router::post('funcionario/{id}/atualizar', [App\Controllers\FuncionariosController::class, 'update'])
    ->name('funcionario.update');

Router::post('funcionario/delete', [App\Controllers\FuncionariosController::class, 'delete'])
    ->name('funcionario.delete');
