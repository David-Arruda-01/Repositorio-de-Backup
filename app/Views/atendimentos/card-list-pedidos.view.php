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
                    <!--Subtotal-->
                    <th>Ações</th>
                </tr>
            </thead>

            <tbody>

                <?php $pedidos = $pedidos ?? ($atendimento->pedidos ?? []); ?>

                <?php if (!empty($pedidos)): ?>

                    <?php foreach ($pedidos as $pedido): ?>

                        <?php
                        // 🔥 busca produto (lazy loading)
                        $produto = $pedido->produto()->first();

                        $nome = $pedido->nome_produto ?? $produto->nome ?? 'Produto não encontrado';
                        $descricao = $pedido->descricao_produto ?? $produto->descricao ?? '';
                        $valor = $pedido->valor_un ?? $produto->valor_un ?? $produto->preco ?? 0;
                        $qtd = $pedido->quantidade ?? 0;
                        $subtotal = $valor * $qtd;
                        ?>

                        <tr>
                            <td>
                                <strong><?= $nome ?></strong>
                                <?php if (!empty($descricao)): ?>
                                    <br><small><?= $descricao ?></small>
                                <?php endif; ?>
                            </td>

                            <td><?= $qtd ?></td>

                            <td>
                                R$ <?= number_format($valor, 2, ',', '.') ?>
                            </td>

                            <!-- <th>Subtotal</th> -->

                            <td>
                                <form action="/pedido/<?= $pedido->id ?>/produto/excluir" method="post" style="display:inline; margin:0; padding:0;">
                                    <?= csrf() ?>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Tem certeza que deseja excluir este item do pedido?');">
                                        <i class="fa fa-trash"></i>
                                    </button>
                                </form>
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