<div class="card card-user">
    <?php if (isset($atendimento) && $atendimento): ?>
        <div class="card-header">
            <?= constant('ID') ?>
            <h5 class="card-title">Atendimento da Mesa <?= $atendimento->mesa ?? '' ?></h5>
        </div>

        <div class="card-body px-4">
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
                                                <?= view('atendimentos.card-list-pedidos', ['atendimento' => $atendimento, 'pedidos' => $atendimento->pedidos]); ?>
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
                                                                R$ <?= number_format($atendimento->total ?? 0, 2, ',', '.') ?>
                                                            </h2>
                                                        </div>
                                                        <div class="card-footer text-dark">
                                                            Cod. Atendimento <?= is_array($atendimento) ? $atendimento['id'] : $atendimento->mesa ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Botões de ação -->
                                                <div class="col-md-6">
                                                    <div class="row">
                                                        <div class="text-center col-12">
                                                            <a href="/mesas" class="btn btn-warning w-100">
                                                                <i class="fa fa-arrow-left mr-2"></i> <span>Voltar [&#x232b;]</span>
                                                            </a>
                                                        </div>
                                                        <div class="text-center col-12">
                                                            <a href="#" class="btn btn-success w-100" data-toggle="modal"
                                                                data-target="#adicionar-produto">
                                                                <i class="fa fa-plus mr-2"></i>
                                                                <span>Add Produto [P]</span>
                                                            </a>
                                                        </div>
                                                        <div class="text-center col-12">
                                                            <?= constant('NAO') ?>
                                                            <a href="#" class="btn btn-success w-100" data-toggle="modal"
                                                                data-target="#registrar-pagamento">
                                                                <i class="fa fa-money mr-2"></i>
                                                                <span>Registrar Pagamento [p]</span>
                                                            </a>
                                                        </div>
                                                        <?php if (count($pedidos) > 0): ?>
                                                            <div class="text-center col-12">
                                                                <a href="#" class="btn btn-primary w-100" data-toggle="modal"
                                                                    data-target="#finalizar-atendimento">
                                                                    <i class="fa fa-handshake-o mr-2"></i>
                                                                    Finalizar Atendimento [F]
                                                                </a>
                                                            </div>
                                                        <?php else: ?>
                                                            <div class="text-center col-12">
                                                                <form action="/atendimento/<?= $atendimento->id ?>/reservada"
                                                                    method="post"
                                                                    onsubmit="return confirm('Deseja realmente reservar esta mesa?')">

                                                                    <?= CSRF() ?>

                                                                    <button type="submit" class="btn btn-secondary w-100">
                                                                        <i class="fa fa-bookmark mr-2"></i>
                                                                        Reservada [R]
                                                                    </button>
                                                                </form>
                                                            </div>
                                                        <?php endif; ?>
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
</div>

<!-- Modal de registrar pagamento -->
<?php if (file_exists(view_path('atendimentos.card-registrar-pagamento'))): ?>
    <!-- O card de registro de pagamento também pode mostrar a lista se implementado -->
<?php endif; ?>

<!-- Modal de finalizar atendimento -->
<?php if (file_exists(view_path('atendimentos.card-finalizar-atendimento'))): ?>
    <?= view('atendimentos.card-finalizar-atendimento', ['atendimento' => $atendimento]); ?>
<?php endif; ?>

<!-- Modal de adicionar produto -->
<?php if (file_exists(view_path('atendimentos.card-adicionar-produto'))): ?>
    <?= view('atendimentos.card-adicionar-produto', ['atendimento' => $atendimento]); ?>
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

<script>
    // Função para atualizar o total do atendimento via AJAX
    function atualizarTotalAtendimento() {
        const atendimentoId = '<?= $atendimento->id ?? '' ?>';

        if (!atendimentoId) return;

        fetch(`/atendimento/${atendimentoId}/total`)
            .then(response => response.json())
            .then(data => {
                const totalElement = document.getElementById('total_atendimento');
                if (totalElement) {
                    totalElement.textContent = data.total_formatado;
                }
            })
            .catch(error => console.error('Erro ao atualizar total:', error));
    }

    // Atualizar total a cada 2 segundos
    setInterval(atualizarTotalAtendimento, 2000);
</script>