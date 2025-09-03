<div class="modal fade" id="registrar-pagamento" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Registrar Pagamento</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="formRegistrarPagamento">
                    <div class="form-group">
                        <label for="valor">Valor:</label>
                        <input type="number" class="form-control" id="valor" name="valor" required>
                    </div>
                    <div class="form-group">
                        <label for="metodo_pagamento">Método de Pagamento:</label>
                        <select class="form-control" id="metodo_pagamento" name="metodo_pagamento" required>
                            <option value="dinheiro">Dinheiro</option>
                            <option value="cartao">Cartão</option>
                            <option value="pix">PIX</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" onclick="registrarPagamento(<?= $atendimento->id ?>)">Registrar</button>
            </div>
        </div>
    </div>
</div>

<script>
    function registrarPagamento(atendimento_id) {
        const form = document.getElementById('formRegistrarPagamento');
        const valor = form.valor.value;
        const metodo_pagamento = form.metodo_pagamento.value;

        fetch(`/atendimento/registrar-pagamento/${atendimento_id}`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ valor, metodo_pagamento }),
        })
        .then(response => response.json())
        .then(data => {
            alert('Pagamento registrado com sucesso!');
            window.location.reload();
        });
    }
</script>