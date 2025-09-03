<?php $template->title='mesas'?> 
<div class="container-fluid">
    <!-- Controle de mesas -->
    <div class="row mb-3 justify-content-center">
        <div class="col-auto">
            <div class="card border-primary rounded shadow-sm text-center p-3">
                <form action="<?= route('alterar_mesas') ?>" method="post" class="d-flex justify-content-center align-items-center">
                    <?= CSRF() ?>
                    <span class="mx-3 h5 mb-0"><?= count($mesas) ?> Mesas</span>
                </form>
            </div>
        </div>
    </div>

    <!-- Mesas -->
    <div class="row">
      
    <?php foreach($mesas as $mesaKey => $mesa): 
    $ocupada = !empty($mesa);
    $color = $ocupada ? 'success' : 'danger';
    $img = $ocupada ? 'ocupada' : 'livre';
    $texto = $ocupada ? 'Atender' : 'Iniciar Atendimento';
    $icone = $ocupada ? 'fa-edit' : 'fa-plus';
    // Define o ID para a rota de atendimento
    
    $idParaRota = $ocupada ? $mesa->id : $mesaKey;
    //$mesa['id']
    //print_r($mesa->id);
    //print_r($idParaRota);
    ?>
    <div class="col-md-4 col-lg-3 mb-3">
        <div class="card border-<?=$color?>">
            <div class="card-header">
                <div class="card-title h3 text-center text-<?=$color?>">
                    Mesa <?=$mesaKey?>
                </div>
            </div>
            <div class="card-body text-center">
                <img src="<?= assets("/img/mesas/$img.png") ?>" alt="Mesa <?=$mesaKey?>" class="img-fluid">
            </div>
            <div class="card-footer text-center">
                <a href="<?= route('mesa.atendimento', ['id' => $idParaRota]) ?>" class="btn btn-<?=$color?>">
                    <i class="fa <?=$icone?>"></i> <?=$texto?>
                </a>
            </div>
        </div>
    </div>
<?php endforeach; ?>       
</div>
</div>
