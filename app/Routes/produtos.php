<?php

use Fmk\Utils\Router;

Router::get('produtos', [App\Controllers\ProdutosController::class, 'index'])
    ->name('produto.list');

Router::get('produto/novo', [App\Controllers\ProdutosController::class, 'create'])
    ->name('produto.create');

Router::post('produto', [App\Controllers\ProdutosController::class, 'storage'])
    ->name('produto.storage');

Router::get('produto/{id}', [App\Controllers\ProdutosController::class, 'edit'])
    ->name('produto.edit');

Router::post('produto/{id}', [App\Controllers\ProdutosController::class, 'update'])
    ->name('produto.update');

Router::post('produto/delete', [App\Controllers\ProdutosController::class, 'delete'])
    ->name('produto.delete');
