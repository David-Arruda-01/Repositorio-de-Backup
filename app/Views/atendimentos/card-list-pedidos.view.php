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
                <?php foreach ($atendimento->pedidos as $pedido): ?>
                    <tr>
                        <td><?= $pedido->produto->nome ?></td>
                        <td><?= $pedido->quantidade ?></td>
                        <td>R$ <?= number_format($pedido->valor_un, 2, ',', '.') ?></td>
                        <td>R$ <?= number_format($pedido->quantidade * $pedido->valor_un, 2, ',', '.') ?></td>
                        <td>
                            <button class="btn btn-danger btn-sm" onclick="excluirPedido(<?= $pedido->id ?>)">
                                <i class="fa fa-trash"></i>
                            </button>
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