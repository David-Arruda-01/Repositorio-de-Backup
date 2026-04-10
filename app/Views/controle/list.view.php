<?php $template->title = 'Controle' ?>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    <h3>Em construção</h3>
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

                                    <td><?= $pagamento->pagamento_tipo_id ?></td>

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