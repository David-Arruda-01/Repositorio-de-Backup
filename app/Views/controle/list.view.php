<?php $template->title = 'Controle' ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">📅 Resumo Diário</h4>
                </div>
                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Mesas</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($resumoDiario as $data => $resumo): ?>
                                <tr>
                                    <td><?= date('d/m/Y', strtotime($data)) ?></td>
                                    <td><?= count($resumo['mesas']) ?></td>
                                    <td>R$ <?= number_format($resumo['valor_total'], 2, ',', '.') ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <h2 class="mb-4">💰 Controle de Pagamentos</h2>

                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Mesa</th>
                                <th>Tipo</th>
                                <th>Valor</th>
                                <th>Data</th>
                                <th>Observação</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pagamentos as $pagamento): ?>
                                <tr>
                                    <td><?= $pagamento->id ?></td>

                                    <td>
                                        <?= $pagamento->atendimento->mesa ?? '—' ?>
                                    </td>

                                    <td><?= $pagamento->tipo->descricao ?? 'Não informado' ?></td>

                                    <td>
                                        R$ <?= number_format($pagamento->valor, 2, ',', '.') ?>
                                    </td>

                                    <td>
                                        <?= date('d/m/Y H:i', strtotime($pagamento->criacao_data)) ?>
                                    </td>

                                    <td>
                                        <?= $pagamento->observacao ?? '-' ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>