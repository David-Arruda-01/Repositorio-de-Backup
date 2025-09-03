<?php $template->title='Produtos'?>
<div class="container-fluid">
    <div class="row d-flex justify-content-end mb-3">
        <div class="col-6 col-md-4 col-sm-2 text-right">
            <a href="<?=route('home')?>" class="btn btn-warning">
                <i class="fa fa-arrow-left mr-2"></i> Voltar
            </a>
            <a href="<?= route('produto.create') ?>" class="btn btn-primary">
                <i class="fa fa-plus-circle"></i> Adicionar Produto
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th>Descrição</th>
                        <th>Unidade</th>
                        <th>Valor</th>
                        <th>Disponível</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($produtos as $produto): ?>
                        <tr>
                            <td><?= $produto->nome ?></td>
                            <td><?= $produto->descricao ?></td>
                            <td><?= $produto->unidade_medida ?></td>
                            <td><?= number_format($produto->valor_un, 2, ',', '.') ?></td>
                            <td><?= $produto->disponivel ? 'Sim' : 'Não' ?></td>
                            <td class="d-flex">
                                <!-- Botão editar vai para o formulário -->
                                <a href="<?= route('produto.edit', ['id' => $produto->id]) ?>" class="btn btn-sm btn-info mr-2">
                                    <i class="fa fa-edit"></i> Editar
                                </a>
                                <!-- Botão excluir envia para produto.delete -->
                                <form action="<?= route('produto.delete') ?>" method="post" onsubmit="return confirm('Tem certeza que deseja excluir este produto?')">
                                    <?= CSRF() ?>
                                    <input type="hidden" name="id" value="<?= $produto->id ?>">
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fa fa-trash"></i> Excluir
                                    </button>
                                </form>
                            </td>
                        </tr> 
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
