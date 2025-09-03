<?php $template->title='Configurações'?>
<div class="container-fluid">
    <div class="row">
        <div class="col">
            <form action="<?=route('configuracoes')?>" method="post">
                <div class="card">
                    <div class="card-body">
                        <?php foreach ($configs as $config): ?>
                            <div class="form-group row">
                                <label for="<?=  $config->name?>" class="col-sm-2"><?= $config->label ?></label>
                                <div class="col-sm-10">
                                    <input type="text" class="form-control <?=has_error($config->name, 'is-invalid')?> " id="<?= $config->name?>"
                                    value="<?= $config->value?>" name="<?= $config->name?>">  
                                    
                                    <div class='invalid-feedback'>
                                        <ul>
                                            <?php foreach (errors($config->name) as $erro): ?>
                                                <li><?= $erro ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>                        
                    </div>
                    <div class="card-footer">
                        <div class="card-footer text-right">
                            <?= CSRF(); ?>
                            <a href="<?=route('home');?>" class="btn btn-warning ">
                                <i class="fa fa-arrow-left mr-2">  voltar</i>
                            </a>
                            <button type="submit" class="btn btn-primary btn-round">
                                    <i>Salvar</i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>