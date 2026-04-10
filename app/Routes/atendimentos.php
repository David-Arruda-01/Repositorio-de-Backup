<?php

use Fmk\Utils\Router;

// Criar atendimento
Router::get('atendimentos/create', [App\Controllers\AtendimentosController::class, 'create'])
    ->name('atendimentos.create');

// Criar pedido
Router::post('pedidos/create', [App\Controllers\AtendimentosController::class, 'store'])
    ->name('pedidos.create');

// Exibir atendimento
Router::get('atendimentos/{id}', [App\Controllers\AtendimentosController::class, 'index'])
    ->name('atendimentos');

// Produtos no atendimento
Router::post('atendimento/{id}/adicionar-produto', [App\Controllers\AtendimentosController::class, 'adicionarProduto'])
    ->name('atendimento.adicionarProduto');

// Pedido
Router::get('pedido/{id}/edit', [App\Controllers\AtendimentosController::class, 'edit'])
    ->name('pedido.edit');

Router::post('pedido/{id}/excluir', [App\Controllers\AtendimentosController::class, 'excluirPedido'])
    ->name('pedido.excluir');

Router::post('pedido/{id}/produto/excluir', [App\Controllers\AtendimentosController::class, 'excluirProduto'])
    ->name('pedido.produto.excluir');

Router::post('pedido/delete', [App\Controllers\AtendimentosController::class, 'excluirPedido'])
    ->name('pedido.delete');

Router::post('pedido/storage', [App\Controllers\AtendimentosController::class, 'storage'])
    ->name('pedido.storage');

// Atendimento ações
Router::post('atendimento/{id}/update', [App\Controllers\AtendimentosController::class, 'update'])
    ->name('atendimento.update');

Router::post('atendimento/{id}/finalizar', [App\Controllers\AtendimentosController::class, 'finalizarAtendimento'])
    ->name('atendimento.finalizar');

Router::post('atendimento/{id}/pagamento', [App\Controllers\AtendimentosController::class, 'registrarPagamento'])
    ->name('atendimento.pagamento');

Router::post('atendimento/{id}/reservada', [App\Controllers\AtendimentosController::class, 'reservadaAtendimento'])
    ->name('atendimento.reservada');

Router::get('atendimento/{id}/total', [App\Controllers\AtendimentosController::class, 'calcularTotalAtendimento'])
    ->name('atendimento.total');
Router::get('/atendimento/{id}/total', [App\Controllers\AtendimentosController::class, 'getTotal']);

Router::post('atendimento/delete', [App\Controllers\AtendimentosController::class, 'destroy'])
    ->name('atendimento.delete');
