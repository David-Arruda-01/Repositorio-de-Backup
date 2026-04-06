<?php $template->title = 'Criar Novo Pedido' ?>
<div class="container-fluid">

    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fa fa-plus"></i> Criar Novo Pedido
                    </h4>
                </div>

                <div class="card-body">
                    <form action="<?= route('pedidos.create') ?>" method="POST">
                        <?= CSRF() ?>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-3">
                                    <label for="produto_id" class="form-label">Produto</label>
                                    <select id="produto_id" name="produto_id"
                                            class="form-control <?= has_error('produto_id', 'is-invalid') ?>"
                                            required>
                                        <option value="">Selecione um produto</option>
                                        <?php
                                        $produtos = \App\Models\Produto::where('disponivel', '=', 1)->get();
                                        foreach ($produtos as $produto):
                                        ?>
                                            <option value="<?= $produto->id ?>"
                                                    <?= old('produto_id') == $produto->id ? 'selected' : '' ?>>
                                                <?= $produto->nome ?> - R$ <?= number_format($produto->valor_un ?? $produto->preco ?? 0, 2, ',', '.') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback">
                                        <ul>
                                            <?php foreach (errors('produto_id') as $erro): ?>
                                                <li><?= $erro ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="quantidade" class="form-label">Quantidade</label>
                                    <input type="number" id="quantidade" name="quantidade"
                                           class="form-control <?= has_error('quantidade', 'is-invalid') ?>"
                                           placeholder="Quantidade"
                                           value="<?= old('quantidade', '') ?>"
                                           min="1" required>
                                    <div class="invalid-feedback">
                                        <ul>
                                            <?php foreach (errors('quantidade') as $erro): ?>
                                                <li><?= $erro ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group mb-3">
                                    <label for="valor_un" class="form-label">Valor Unitário</label>
                                    <input type="number" id="valor_un" name="valor_un"
                                           class="form-control <?= has_error('valor_un', 'is-invalid') ?>"
                                           placeholder="Valor"
                                           value="<?= old('valor_un', '') ?>"
                                           step="0.01" min="0" required>
                                    <div class="invalid-feedback">
                                        <ul>
                                            <?php foreach (errors('valor_un') as $erro): ?>
                                                <li><?= $erro ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <a href="<?= route('atendimentos.create') ?>" class="btn btn-secondary">
                                        <i class="fa fa-arrow-left"></i> Voltar
                                    </a>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fa fa-save"></i> Criar Pedido
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

        </div>
    </div>

</div>