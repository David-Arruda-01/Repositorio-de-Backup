<?php $template->title = 'mesas' ?>
<div class="container-fluid">

    <!-- Controle de mesas -->
    <div class="row mb-3 justify-content-center">
        <div class="col-auto">
            <div class="card border-primary rounded shadow-sm text-center p-3">
                <form action="<?= route('alterar_mesas') ?>" method="post" class="d-flex justify-content-center align-items-center">
                    <?= CSRF() ?>
                    <button type="submit" name="acao" value="menos" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-minus"></i>
                    </button>

                    <span class="mx-3 h5 mb-0"><?= count($mesas) ?> Mesas</span>

                    <button type="submit" name="acao" value="mais" class="btn btn-sm btn-outline-primary">
                        <i class="fa fa-plus"></i>
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Mesas -->
    <div class="row">

        <?php foreach ($mesas as $mesaKey => $mesa):

            $ocupada = $mesa['ocupada'];
            $atendimento = $mesa['atendimento'];
            $reservada = $mesa['reservada'] ?? false;

            $color = $ocupada ? ($reservada ? 'warning' : 'danger') : 'success';
            $img = $ocupada
                ? ($reservada ? 'reservada' : 'ocupada')
                : 'livre';

            // 🔥 tempo de atendimento
            $tempo = null;
            if ($ocupada && $atendimento->criacao_data) {
                $inicio = new DateTime($atendimento->criacao_data);
                $agora = new DateTime();
                $tempo = $inicio->diff($agora)->format('%H:%I');
            }

        ?>

            <div class="col-md-4 col-lg-3 mb-3">
                <div class="card border-<?= $color ?> shadow-sm">

                    <!-- HEADER -->
                    <div class="card-header text-center">
                        <div class="card-title h3 text-<?= $color ?>">
                            Mesa <?= $mesaKey ?>
                        </div>
                    </div>

                    <!-- BODY -->
                    <div class="card-body text-center">

                        <img src="<?= assets("/img/mesas/$img.png") ?>"
                            alt="Mesa <?= $mesaKey ?>"
                            class="img-fluid"
                            style="max-height: 100px;">

                        <div class="mt-2">
                            <span class="badge badge-<?= $color ?>">
                                <?= $ocupada ? ($reservada ? 'Reservada' : 'Ocupada') : 'Livre' ?>
                            </span>
                        </div>

                        <?php if ($ocupada): ?>

                            <!-- 💰 TOTAL -->
                            <div class="mt-2">
                                <strong>R$ <?= number_format($atendimento->total, 2, ',', '.') ?></strong>
                            </div>

                            <!-- ⏱️ TEMPO -->
                            <?php if ($tempo): ?>
                                <div class="text-muted small">
                                    ⏱️ <?= $tempo ?>
                                </div>
                            <?php endif; ?>

                        <?php endif; ?>

                    </div>

                    <!-- FOOTER -->
                    <div class="card-footer text-center">

                        <?php if ($ocupada): ?>
                            <div class="btn-group-vertical w-100">

                                <!-- 🔥 CONTINUAR -->
                                <a href="<?= route('atendimentos', ['id' => $atendimento->id]) ?>"
                                    class="btn btn-primary mb-2 w-100">
                                    <i class="fa fa-edit"></i> Atender
                                </a>

                                <!-- 🔥 FINALIZAR -->
                                <?php if (!$reservada): ?>
                                    <form action="<?= route('atendimento.finalizar', ['id' => $atendimento->id]) ?>"
                                        method="post"
                                        onsubmit="return confirm('Deseja realmente finalizar este atendimento?')"
                                        class="mb-2">

                                        <?= CSRF() ?>

                                        <button type="submit" class="btn btn-danger w-100">
                                            <i class="fa fa-check"></i> Finalizar Atendimento
                                        </button>
                                    </form>
                                <?php endif; ?>

                                <!-- 🗑️ DELETAR -->
                                <form action="<?= route('atendimento.delete') ?>"
                                    method="post"
                                    onsubmit="return confirm('Deseja realmente deletar este atendimento? Isso removerá permanentemente todos os dados!')"
                                    class="mb-2">

                                    <?= CSRF() ?>
                                    <input type="hidden" name="id" value="<?= $atendimento->id ?>">

                                    <button type="submit" class="btn btn-dark w-100">
                                        <i class="fa fa-trash"></i> Deletar Atendimento
                                    </button>
                                </form>
                            </div>

                        <?php else: ?>
                            <div class="btn-group-vertical w-100">

                                <!-- 🔥 INICIAR -->
                                <a href="<?= route('mesa.atendimento', ['id' => $mesaKey]) ?>"
                                    class="btn btn-success mb-2 w-100">
                                    <i class="fa fa-play"></i> Iniciar Atendimento
                                </a>

                                <!-- 🟡 RESERVAR -->
                                <form action="<?= route('mesa.reservar', ['id' => $mesaKey]) ?>"
                                    method="post"
                                    class="mb-2">

                                    <?= CSRF() ?>

                                    <button type="submit" class="btn btn-warning w-100">
                                        <i class="fa fa-clock"></i> Reservar
                                    </button>
                                </form>

                            </div>
                        <?php endif; ?>

                    </div>
                </div>
            </div>

        <?php endforeach; ?>

    </div>
</div>