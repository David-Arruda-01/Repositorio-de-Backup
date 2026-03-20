<?php $template->title='mesas'?> 
<div class="container-fluid">
    <!-- Controle de mesas -->
    <div class="row mb-3 justify-content-center">
        <div class="col-auto">
            <div class="card border-primary rounded shadow-sm text-center p-3">
                <form action="<?= route('alterar_mesas') ?>" method="post" class="d-flex justify-content-center align-items-center">
                    <?= CSRF() ?>
                    <button type="submit" name="acao" value="menos" class="btn btn-sm btn-outline-primary"><i class="fa fa-minus"></i></button>
                    <span class="mx-3 h5 mb-0"><?= count($mesas) ?> Mesas</span>
                    <button type="submit" name="acao" value="mais" class="btn btn-sm btn-outline-primary"><i class="fa fa-plus"></i></button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mesas -->
    <div class="row">
      
    <?php foreach($mesas as $mesaKey => $atendimento): 
        $ocupada = !empty($atendimento);
        $color = $ocupada ? 'danger' : 'success';
        $img = $ocupada ? 'ocupada' : 'livre';
        
        // Se ocupada, o ID para a rota é o ID do atendimento. Se livre, usamos o número da mesa.
        $idParaRota = $ocupada ? $atendimento->id : $mesaKey;
    ?>
    <div class="col-md-4 col-lg-3 mb-3">
        <div class="card border-<?=$color?>">
            <div class="card-header">
                <div class="card-title h3 text-center text-<?=$color?>">
                    Mesa <?=$mesaKey?>
                </div>
            </div>
            <div class="card-body text-center">
                <img src="<?= assets("/img/mesas/$img.png") ?>" alt="Mesa <?=$mesaKey?>" class="img-fluid" style="max-height: 100px;">
                <?php if($ocupada): ?>
                    <div class="mt-2">
                        <span class="badge badge-danger">Ocupada</span>
                    </div>
                <?php else: ?>
                    <div class="mt-2">
                        <span class="badge badge-success">Livre</span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="card-footer text-center">
                <?php if($ocupada): ?>
                    <div class="btn-group-vertical w-100">
                        <a href="<?= route('mesa.atendimento', ['id' => $mesaKey]) ?>" class="btn btn-primary mb-2">
                            <i class="fa fa-edit"></i> Atender
                        </a>
                        <form action="<?= route('atendimento.finalizar', ['id' => $atendimento->id]) ?>" method="post" onsubmit="return confirm('Deseja realmente finalizar este atendimento?')">
                            <?= CSRF() ?>
                            <button type="submit" class="btn btn-danger w-100">
                                <i class="fa fa-check"></i> Finalizar Atendimento
                            </button>
                        </form>
                    </div>
                <?php else: ?>
                    <a href="<?= route('mesa.atendimento', ['id' => $mesaKey]) ?>" class="btn btn-success w-100">
                        <i class="fa fa-play"></i> Iniciar Atendimento
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>       
</div>
</div>
