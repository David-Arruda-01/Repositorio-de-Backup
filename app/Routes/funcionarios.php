<?php

use Fmk\Utils\Router;

Router::get('funcionarios', [App\Controllers\FuncionariosController::class, 'index'])
    ->name('funcionario.list');

Router::get('funcionario/novo', [App\Controllers\FuncionariosController::class, 'create'])
    ->name('funcionario.create');

Router::post('funcionario', [App\Controllers\FuncionariosController::class, 'storage'])
    ->name('funcionario.storage');

Router::get('funcionario/{id}', [App\Controllers\FuncionariosController::class, 'edit'])
    ->name('funcionario.edit');

Router::post('funcionario/{id}', [App\Controllers\FuncionariosController::class, 'update']);

Router::post('funcionario/delete', [App\Controllers\FuncionariosController::class, 'delete'])
    ->name('funcionario.delete');

// Gorjetas
Router::get('gorjetas/{funcionario_id}/{data}', function ($funcionario_id, $data) {
    echo "Gorjetas do Funcionario $funcionario_id no dia $data";
})->name("funcionario.gorjeta");
