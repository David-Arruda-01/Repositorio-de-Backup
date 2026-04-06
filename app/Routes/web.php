<?php

use Fmk\Utils\Router;

Router::get('/', [App\Controllers\HomeController::class, 'index'])->name('home')->middlewares('auth');
Router::get('/login', [App\Controllers\LoginController::class, 'index'])->name('login')->middlewares('noAuth');
Router::post('/logar', [App\Controllers\LoginController::class, 'logar'])->name('logar');
Router::get('/logout', [App\Controllers\LoginController::class, 'logout'])->middlewares('auth');

Router::get('/mesa/{id}', function ($id) {
    echo "Abriu a mesa $id";
});
Router::post('mesa/{id}/pedido', function ($id) {
    echo "Adicionou o Pedido";
});
Router::get('mesa/{id}/pedidos', function ($id) {
    echo "Pegar Pedidos da mesa $id";
})->name('mesa.pedidos');

//Rotas de funcionário.
Router::get('funcionarios', [App\Controllers\FuncionariosController::class, 'index'])->name('funcionario.list');
Router::get('funcionario/novo', [App\Controllers\FuncionariosController::class, 'create'])->name('funcionario.create');
Router::post('funcionario/delete', [App\Controllers\FuncionariosController::class, 'delete'])->name('funcionario.delete');
Router::get('funcionario/{id}', [App\Controllers\FuncionariosController::class, 'edit'])->name('funcionario.edit');
Router::post('funcionario/{id}', [App\Controllers\FuncionariosController::class, 'update']);
Router::post('funcionario', [App\Controllers\FuncionariosController::class, 'storage'])->name('funcionario.storage');

//rotas dos atendimentos
// Listar todas as mesas e seus atendimentos (disponível/ocupada)
Router::get('mesas', [App\Controllers\HomeController::class, 'index'])->name('mesas.list');

// Abrir ou recuperar atendimento de uma mesa pelo ID
Router::get('mesa/{id}/atendimento', [App\Controllers\HomeController::class, 'atendimento'])->name('mesa.atendimento');

// Criar atendimento / visualizar pedidos disponíveis
Router::get('atendimentos/create', [App\Controllers\AtendimentosController::class, 'create'])->name('atendimentos.create');

// Criar pedido independente
Router::post('pedidos/create', [App\Controllers\AtendimentosController::class, 'store'])->name('pedidos.create');

// Exibir atendimento (id fornecido)
Router::get('atendimentos/{id}', [App\Controllers\AtendimentosController::class, 'index'])->name('atendimentos');

// Adicionar produto a um atendimento
Router::post('atendimento/{id}/adicionar-produto', [App\Controllers\AtendimentosController::class, 'adicionarProduto'])->name('atendimento.adicionarProduto');

// Excluir pedido
Router::post('pedido/{id}/excluir', [App\Controllers\AtendimentosController::class, 'excluirPedido'])->name('pedido.excluir');

// Excluir produto do pedido (manter usuário no atendimento)
Router::post('pedido/{id}/produto/excluir', [App\Controllers\AtendimentosController::class, 'excluirProduto'])->name('pedido.produto.excluir');

// Finalizar atendimento
Router::post('atendimento/{id}/finalizar', [App\Controllers\AtendimentosController::class, 'finalizarAtendimento'])->name('atendimento.finalizar');

// Reservar atendimento
Router::post('/mesa/{id}/reservar', [App\Controllers\HomeController::class, 'reservar'])->name('mesa.reservar');

// Registrar pagamento
Router::post('atendimento/{id}/pagamento', [App\Controllers\AtendimentosController::class, 'registrarPagamento'])->name('atendimento.pagamento');

// Calcular total de um atendimento
Router::get('atendimento/{id}/total', [App\Controllers\AtendimentosController::class, 'calcularTotalAtendimento'])->name('atendimento.total');

// Atualizar pedido
Router::post('atendimento/{id}/update', [App\Controllers\AtendimentosController::class, 'update'])->name('atendimento.update');

// Editar pedido
Router::get('pedido/{id}/edit', [App\Controllers\AtendimentosController::class, 'edit'])->name('pedido.edit');

// Deletar pedido
Router::post('pedido/delete', [App\Controllers\AtendimentosController::class, 'excluirPedido'])->name('pedido.delete');

// Deletar atendimento
Router::post('atendimento/delete', [App\Controllers\AtendimentosController::class, 'destroy'])->name('atendimento.delete');

// Criar / salvar pedido (storage)
Router::post('pedido/storage', [App\Controllers\AtendimentosController::class, 'storage'])->name('pedido.storage');



//Rotas de produtos.
Router::get('produtos', [App\Controllers\ProdutosController::class, 'index'])->name('produto.list');
Router::get('produto/novo', [App\Controllers\ProdutosController::class, 'create'])->name('produto.create');
Router::post('produto/delete', [App\Controllers\ProdutosController::class, 'delete'])->name('produto.delete');
Router::get('produto/{id}', [App\Controllers\ProdutosController::class, 'edit'])->name('produto.edit');
Router::post('produto/{id}', [App\Controllers\ProdutosController::class, 'update'])->name('produto.update');
Router::post('produto', [App\Controllers\ProdutosController::class, 'storage'])->name('produto.storage');

Router::get('gorjetas/{funcionario_id}/{data}', function ($funcionario_id, $data) {
    echo "Gorjetas do Funcionario $funcionario_id no dia $data";
})->name("funcionario.gorjeta");

Router::get('configuracoes', [App\Controllers\ConfiguracoesController::class, 'index'])->name('configuracoes');
Router::post('/configuracoes', [App\Controllers\ConfiguracoesController::class, 'update']);

Router::post('alterar_mesas', [App\Controllers\HomeController::class, 'alterarMesas'])->name('alterar_mesas');
