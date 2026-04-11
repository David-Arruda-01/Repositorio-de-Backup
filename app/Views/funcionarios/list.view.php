<?php $template->title = 'Funcionarios' ?>
<div class="container-fluid">

    <div class="row d-flex justify-content-end">
        <div class="col-6 col-md-4 col-sm-2 text-right">
            <a href="<?= route('funcionario.create') ?>" class="btn btn-primary">
                <i class="fa fa-user-plus"></i> Adicionar Funcionário
            </a>
        </div>
    </div>

    <div class="row">
        <?php foreach ($funcionarios as $funcionario): ?>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h3 class="text-center"><?= $funcionario->nome ?></h3>
                    </div>

                    <div class="card-footer d-flex justify-content-center">

                        <!-- EDITAR -->
                        <a href="<?= route('funcionario.edit', ['id' => $funcionario->id]) ?>"
                            class="btn btn-primary mr-3">
                            <i class="fa fa-edit"></i>
                        </a>

                        <!-- DELETAR -->
                        <form action="<?= route('funcionario.delete') ?>"
                            method="POST"
                            onsubmit="return confirm('Tem certeza que deseja excluir este funcionário?');">

                            <?= CSRF() ?>

                            <input type="hidden" name="id" value="<?= $funcionario->id ?>">

                            <button type="submit" class="btn btn-danger">
                                <i class="fa fa-trash"></i>
                            </button>
                        </form>

                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <div class="row d-flex justify-content-end">
        <div class="col-6 col-md-4 col-sm-2 text-right">
            <a href="<?= route('home') ?>" class="btn btn-warning">
                <i class="fa fa-arrow-left mr-2"></i> Voltar
            </a>
        </div>
    </div>

</div>