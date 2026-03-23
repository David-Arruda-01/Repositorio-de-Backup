<div class="card card-user">
    <?php if (isset($atendimento) && $atendimento): ?>
    <form action="<?= route('atendimento.finalizar', ['id' => $atendimento->id]) ?>" method="POST">
        <div class="card-header">
            <h5 class="card-title">Atendimento da Mesa <?= $atendimento->mesa ?></h5>
        </div>
        
        <div class="card-body px-4">
            <?= CSRF(); ?>
            <div class="row">
                <div class="row pb-5">
                    <div class="col pb-5">
                        <div class="card">
                            <div class="card-body">
                                <div class="container-fluid">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <!-- Card de listagem de pedidos -->
                                            <?php if (file_exists(view_path('atendimentos.card-list-pedidos'))): ?>
                                                <?= view('atendimentos.card-list-pedidos', ['atendimento' => $atendimento]); ?>
                                            <?php else: ?>
                                                <div class="alert alert-warning">
                                                    Componente de pedidos não encontrado (atendimentos.card-list-pedidos)
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="row">
                                                <!-- Card de total do atendimento -->
                                                <div class="col-md-6 d-flex justify-content-center align-items-center">
                                                    <div class="card text-white bg-info mb-3 w-100">
                                                        <div class="card-body">
                                                            <div class="">
                                                                Total:
                                                            </div>
                                                            <h2 class="text-center" id="total_atendimento">
                                                                R$ 
                                                                <?php 
                                                                if (is_array($atendimento) && isset($atendimento['pedidos'])) {
                                                                    $total = array_sum(array_map(function($pedido) {
                                                                        return $pedido['quantidade'] * $pedido['valor_un'];
                                                                    }, $atendimento['pedidos']));
                                                                    echo number_format($total, 2, ',', '.');
                                                                } elseif (is_object($atendimento) && isset($atendimento->pedidos)) {
                                                                    $total = 0;
                                                                    foreach($atendimento->pedidos as $pedido) {
                                                                        $total += $pedido->quantidade * $pedido->valor_un;
                                                                    }
                                                                    echo number_format($total, 2, ',', '.');
                                                                } else {
                                                                    echo '0,00';
                                                                }
                                                                ?>
                                                            </h2>
                                                        </div>
                                                        <div class="card-footer text-dark">
                                                            Cod. Atendimento <?= is_array($atendimento) ? $atendimento['id'] : $atendimento->id ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Botões de ação -->
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="text-center col-12">
                                                            <a href="/home" class="btn btn-warning w-100">
                                                                <i class="fa fa-arrow-left mr-2"></i> <span>Voltar [&#x232b;]</span>
                                                            </a>
                                                        </div>
                                                        <div class="text-center col-12">
                                                            <a href="#" class="btn btn-info w-100">
                                                                <i class="fa fa-user-o mr-2"></i> <span>Add Cliente [C] </span>
                                                            </a>
                                                        </div>
                                                        <div class="text-center col-12">
                                                            <a href="#" class="btn btn-success w-100" data-toggle="modal"
                                                                data-target="#registrar-pagamento">
                                                                <i class="fa fa-money mr-2"></i>
                                                                <span>Registrar Pagamento [p]</span>
                                                            </a>
                                                        </div>
                                                        <div class="text-center col-12">
                                                            <a href="#" class="btn btn-primary w-100" data-toggle="modal"
                                                                data-target="#finalizar-atendimento">
                                                                <i class="fa fa-handshake-o mr-2"></i>
                                                                Finalizar Atendimento [F]
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Card de pagamentos -->
                                                <div class="col-12 order-first order-md-last">
                                                    <div class="row py-3">
                                                        <div class="col">
                                                            <?php if (file_exists(view_path('atendimentos.card-registrar-pagamento'))): ?>
                                                                <!-- O card de registro de pagamento também pode mostrar a lista se implementado -->
                                                            <?php endif; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Modal de registrar pagamento -->
    <?php if (file_exists(view_path('atendimentos.card-registrar-pagamento'))): ?>
        <?= view('atendimentos.card-registrar-pagamento', ['atendimento' => $atendimento]); ?>
    <?php endif; ?>

    <!-- Modal de finalizar atendimento -->
    <?php if (file_exists(view_path('atendimentos.card-finalizar-atendimento'))): ?>
        <?= view('atendimentos.card-finalizar-atendimento', ['atendimento' => $atendimento]); ?>
    <?php endif; ?>
    
    <?php else: ?>
    <div class="card-header">
        <h5 class="card-title">Nenhum atendimento encontrado</h5>
    </div>
    <div class="card-body text-center">
        <p>Não há atendimentos ativos no momento.</p>
        <a href="/home" class="btn btn-primary">
            <i class="fa fa-plus mr-2"></i> Voltar para Mesas
        </a>
    </div>
    <?php endif; ?>
</div>
