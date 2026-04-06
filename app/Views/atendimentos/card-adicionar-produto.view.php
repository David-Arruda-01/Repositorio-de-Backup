<div class="modal fade" id="adicionar-produto" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="card-title">Adicionar Produto ao Atendimento</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?= CSRF(); ?>
                <div class="row">
                    <?php
                    $produtos = \App\Models\Produto::all();
                    foreach ($produtos as $produto):
                    ?>
                        <div class="col-md-4 mb-3">
                            <div class="card h-100">
                                <div class="card-body text-center">
                                    <h6 class="card-title"><?= $produto->nome ?></h6>
                                    <p class="card-text">R$ <?= number_format($produto->valor_un ?? $produto->preco ?? 0, 2, ',', '.') ?></p>
                                    <form action="<?= route('atendimento.adicionarProduto', ['id' => $atendimento->id]) ?>" method="post" class="d-inline">
                                        <?= CSRF() ?>
                                        <input type="hidden" name="produto_id" value="<?= $produto->id ?>">
                                        <div class="form-group">
                                            <label for="quantidade_<?= $produto->id ?>">Quantidade:</label>
                                            <input type="number" class="form-control" id="quantidade_<?= $produto->id ?>" name="quantidade" value="1" min="1" required>
                                        </div>
                                        <button type="submit" class="btn btn-primary btn-sm">Adicionar</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>