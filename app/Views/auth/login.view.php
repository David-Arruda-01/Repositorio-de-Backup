<div class="container">
  <div class="row">
    <div class="col-xl-4 col-lg-5 col-md-7 d-flex flex-column mx-lg-0 mx-auto">
      <div class="card card-plain">
        <div class="card-header pb-0 text-start">
          <h4 class="font-weight-bolder">Sign In</h4>
          <?= constant('USE') ?>
          <p class="mb-0">Digite seu usuário e senha</p>
        </div>

        <div class="card-body">
          <?php if (isset(session()->msg)): ?>
            <div class="alert alert-warning">
              <?= session()->flush('msg'); ?>
            </div>
          <?php endif ?>

          <form role="form" action="\logar" method="POST">
            <?= CSRF() ?>

            <!-- LOGIN -->
            <div class="form-group mb-3">
              <input type="text"
                class="form-control form-control-lg <?= has_error('login', 'is-invalid') ?>"
                placeholder="Gmail"
                name="login"
                value="<?= old('login') ?>"
                required>

              <ul class="invalid-feedback">
                <?php foreach (errors('login') as $error): ?>
                  <li><?= $error ?></li>
                <?php endforeach ?>
              </ul>
            </div>

            <!-- SENHA -->
            <div class="mb-3 form-group">
              <input type="password"
                class="form-control form-control-lg <?= has_error('senha', 'is-invalid') ?>"
                placeholder="Senha"
                name="senha"
                required>

              <ul class="invalid-feedback">
                <?php foreach (errors('senha') as $error): ?>
                  <li><?= $error ?></li>
                <?php endforeach ?>
              </ul>
            </div>

            <!-- LEMBRAR -->
            <div class="form-check form-switch">
              <input class="form-check-input border" type="checkbox" id="rememberMe" name="remember">
              <label class="form-check-label" for="rememberMe">Lembre-me</label>
            </div>

            <div class="text-center">
              <button type="submit" class="btn btn-lg btn-primary w-100 mt-4 mb-0">
                Entrar
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </div>
</div>