<?php

use Fmk\Utils\Router;

// Listagem de mesas
Router::get('mesas', [App\Controllers\HomeController::class, 'index'])
    ->name('mesas.list');

// Ações da mesa
Router::get('/mesa/{id}', function ($id) {
    echo "Abriu a mesa $id";
});

Router::post('mesa/{id}/pedido', function ($id) {
    echo "Adicionou o Pedido";
});

Router::get('mesa/{id}/pedidos', function ($id) {
    echo "Pedidos da mesa $id";
})->name('mesa.pedidos');

// Atendimento da mesa
Router::get('mesa/{id}/atendimento', [App\Controllers\HomeController::class, 'atendimento'])
    ->name('mesa.atendimento');

Router::post('/mesa/{id}/reservar', [App\Controllers\HomeController::class, 'reservar'])
    ->name('mesa.reservar');

// Alterar mesas
Router::post('alterar_mesas', [App\Controllers\HomeController::class, 'alterarMesas'])
    ->name('alterar_mesas');
