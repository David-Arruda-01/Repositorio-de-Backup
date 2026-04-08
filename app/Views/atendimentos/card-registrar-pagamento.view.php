<div class="modal fade" id="registrar-pagamento" tabindex="-1" role="dialog" aria-labelledby="registrarPagamentoLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="registrarPagamentoLabel">Registrar Pagamento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Fechar">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>

            <form action="<?= route('atendimento.pagamento', ['id' => $atendimento->id]) ?>" method="post">
                <div class="modal-body">
                    <?= CSRF() ?>

                    <div class="form-group">
                        <label for="pagamento_tipo_id">Tipo de Pagamento</label>
                        <select class="form-control" id="pagamento_tipo_id" name="pagamento_tipo_id" required>
                            <option value="">Selecione um tipo de pagamento</option>
                            <option value="1">Dinheiro</option>
                            <option value="2">Cartão de Débito</option>
                            <option value="3">Cartão de Crédito</option>
                            <option value="4">PIX</option>
                            <option value="5">Promoção da Loja</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="valor">Valor do Pagamento</label>
                        <input
                            type="number"
                            class="form-control"
                            id="valor"
                            name="valor"
                            min="0.01"
                            step="0.01"
                            placeholder="0,00"
                            required>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                    <button type="submit" class="btn btn-success">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>