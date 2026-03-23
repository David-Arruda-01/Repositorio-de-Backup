<div class="card">
    <div class="card-header">
        <h5 class="card-title">Pedidos</h5>
    </div>
    <div class="card-body">
        <table class="table">
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
                <?php foreach ($pedidos ?? [] as $pedido): ?>

                    <tr>
                        <td><?= $pedido['produto']['nome_produto'] ?? '' ?></td>
                        <td><?= $pedido['pedido']['quantidade'] ?? 0 ?></td>
                        <td><?= $pedido['produto']['valor_unitario'] ?? 0 ?></td>
                        <td>
                            <?= ($pedido['produto']['valor_unitario'] ?? 0) * ($pedido['pedido']['quantidade'] ?? 0) ?>
                        </td>
                    </tr>

                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
    function excluirPedido(pedido_id) {
        if (confirm('Tem certeza que deseja excluir este pedido?')) {
            fetch(`/atendimento/excluir-pedido/${pedido_id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                })
                .then(response => response.json())
                .then(data => {
                    alert(data.message);
                    window.location.reload();
                });
        }
    }
</script>