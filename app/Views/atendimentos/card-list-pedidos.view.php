<div class="card">
    <div class="card-header">
        <h5 class="card-title">Pedidos</h5>
    </div>

    <div class="card-body">
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>Produto</th>
                    <th>Quantidade</th>
                    <th>Valor Unitário</th>
                    <th>Subtotal</th>
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                <?php if (!empty($pedidos)): ?>

                    <?php foreach ($pedidos as $pedido): ?>

                        <?php
                        // 🔥 busca produto (lazy loading)
                        $produto = $pedido->produto()->first();

                        $nome = $produto->nome ?? 'Produto não encontrado';
                        $valor = $pedido->valor_unitario ?? 0;
                        $qtd = $pedido->quantidade ?? 0;
                        $subtotal = $valor * $qtd;
                        ?>

                        <tr>
                            <td><?= $nome ?></td>

                            <td><?= $qtd ?></td>

                            <td>
                                R$ <?= number_format($valor, 2, ',', '.') ?>
                            </td>

                            <td>
                                R$ <?= number_format($subtotal, 2, ',', '.') ?>
                            </td>

                            <td>
                                <button class="btn btn-sm btn-danger"
                                    onclick="excluirPedido(<?= $pedido->id ?>)">
                                    <i class="fa fa-trash"></i>
                                </button>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="5" class="text-center">
                            Nenhum pedido registrado
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>
        </table>
    </div>
</div>