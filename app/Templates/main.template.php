<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="utf-8" />
  <link rel="apple-touch-icon" sizes="76x76" href="<?= assets('/img/apple-icon.png') ?>">
  <link rel="icon" type="image/png" href="<?= assets('/img/favicon.png') ?>">
  <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
  <title>
    <?= constant('APPLICATION_NAME') ?>
  </title>
  <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
  <!--     Fonts and icons     -->
  <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
  <link href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" rel="stylesheet">
  <!-- CSS Files -->
  <?= styles('bootstrap', 'paper-bootstrap') ?>
</head>

<body class="">
  <div class="wrapper ">
    <div class="sidebar" data-color="white" data-active-color="danger">
      <div class="logo">
        <a href="<?= route('home') ?>" class="simple-text logo-mini">
          <div class="logo-image-small">

          </div>
        </a>
        <a href="" class="simple-text logo-normal">
          <?= constant('APPLICATION_NAME') ?><!--restaurante Criar uma rota para o .app do config. -->
        </a>
      </div>
      <div class="sidebar-wrapper">
        <ul class="nav">
          <?= component('menu', ['route' => 'home', 'label' => 'Mesas', 'icon' => 'fa fa-table']); ?>
          <?= component('menu', ['route' => 'funcionario.list', 'label' => 'Funcionários', 'icon' => 'fa fa-users']); ?>
          <?= component('menu', ['route' => 'produto.list', 'label' => 'Produtos', 'icon' => 'fa fa-coffee']); ?>

        </ul>
      </div>
    </div>
    <div class="main-panel">
      <!-- Navbar -->
      <nav class="navbar navbar-expand-lg navbar-absolute fixed-top navbar-transparent">
        <div class="container-fluid">
          <div class="navbar-wrapper">
            <div class="navbar-toggle">
              <button type="button" class="navbar-toggler">
                <span class="navbar-toggler-bar bar1"></span>
                <span class="navbar-toggler-bar bar2"></span>
                <span class="navbar-toggler-bar bar3"></span>
              </button>
            </div>
            <a class="navbar-brand" href="javascript:;"><?= $template->title ?? constant('APPLICATION_NAME') ?></a>
          </div>
          <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navigation" aria-controls="navigation-index" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
            <span class="navbar-toggler-bar navbar-kebab"></span>
          </button>
          <div class="collapse navbar-collapse justify-content-end" id="navigation">
            <form id="atendimento">
              <div class="input-group no-border">
                <input type="number" value="" class="form-control" placeholder="Nº mesa">
                <div class="input-group-append">
                  <div class="input-group-text">
                    <i class="nc-icon nc-zoom-split"></i>
                  </div>
                </div>
              </div>
            </form>
            <ul class="navbar-nav">
              <li class="nav-item">
                <a class="nav-link btn-rotate" href="<?= route('configuracoes'); ?>">
                  <i class="nc-icon nc-settings-gear-65"></i>
                  <p>
                    <span class="d-lg-none d-md-block">Configurações</span>
                  </p>
                </a>
              </li>
              <li class="nav-item">
                <a class="nav-link btn-rotate" href="<?= route('logout'); ?>">
                  <i class="fa fa-arrow-left mr-2">sair</i>
                  <p>
                    <span class="d-lg-none d-md-block">Logout</span>
                  </p>
                </a>
              </li>
            </ul>
          </div>
        </div>
      </nav>
      <!-- End Navbar -->
      <div class="content">
        {{$VIEW}}
      </div>
      <!--O component atendimento, pode ser testado aqui-->
      <?= component('footer') ?>
    </div>
  </div>
  <?= scripts('jquery', 'popper', 'bootstrap', 'perfect-scrollbar', 'bootstrap-notify', 'dashboard') ?>
  <?= component('notify'); ?>
</body>

</html>