<div class="card card-user">
  <form action="<?= route('funcionario.update', ['id' => $id]); ?>" method="POST">
    <div class="card-header">
      <h5 class="card-title">Editar Funcionário</h5>
    </div>

    <div class="card-body px-4">
      <?= CSRF(); ?>
      <input type="hidden" name="id" value="<?= $id ?? '' ?>">

      <div class="row">
        <!-- NOME -->
        <div class="col-md-4">
          <div class="form-group">
            <label for="nome">Nome</label>
            <input type="text" id="nome" name="nome"
              class="form-control <?= has_error('nome', 'is-invalid') ?>"
              placeholder="Nome"
              value="<?= old('nome', $nome ?? '') ?>" required>

            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('nome') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- EMAIL -->
        <div class="col-md-4 pl-1">
          <div class="form-group">
            <label for="email">Endereço de E-mail</label>
            <input type="email" id="email" name="login"
              class="form-control <?= has_error('login', 'is-invalid') ?>"
              placeholder="Email"
              value="<?= old('login', $login ?? '') ?>" required>

            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('login') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- TELEFONE -->
        <div class="col-md-4 pl-1">
          <div class="form-group">
            <label for="telefone">Telefone</label>
            <input type="text" id="telefone" name="telefone"
              class="form-control <?= has_error('telefone', 'is-invalid') ?>"
              placeholder="Telefone"
              value="<?= old('telefone', $telefone ?? '') ?>">

            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('telefone') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- CPF -->
        <div class="col-md-4">
          <div class="form-group">
            <label>CPF</label>
            <input type="text" name="cpf"
              class="form-control <?= has_error('cpf', 'is-invalid') ?>"
              placeholder="CPF"
              value="<?= old('cpf', $cpf ?? '') ?>">

            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('cpf') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- RG -->
        <div class="col-md-4">
          <div class="form-group">
            <label>RG</label>
            <input type="text" name="rg"
              class="form-control <?= has_error('rg', 'is-invalid') ?>"
              placeholder="RG"
              value="<?= old('rg', $rg ?? '') ?>">

            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('rg') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- RG EXPEDIDOR -->
        <div class="col-md-4">
          <div class="form-group">
            <label>RG Órgão Expedidor</label>
            <input type="text" name="rg_expedidor"
              class="form-control <?= has_error('rg_expedidor', 'is-invalid') ?>"
              placeholder="SSP/TO"
              value="<?= old('rg_expedidor', $rg_expedidor ?? '') ?>">

            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('rg_expedidor') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
      </div>

      <div class="row">
        <!-- SENHA -->
        <div class="col-md-6">
          <div class="form-group">
            <label>Senha (opcional)</label>
            <input type="password" name="password"
              class="form-control <?= has_error('password', 'is-invalid') ?>"
              placeholder="Digite uma nova senha">

            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('password') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>

        <!-- CONFIRMAÇÃO -->
        <div class="col-md-6">
          <div class="form-group">
            <label>Confirmação</label>
            <input type="password" name="confirmacao"
              class="form-control"
              placeholder="Confirme a nova senha">
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer text-right">
      <a href="<?= route('funcionario.list'); ?>" class="btn btn-warning">
        Voltar
      </a>

      <button type="submit" class="btn btn-primary">
        Salvar
      </button>
    </div>
  </form>
</div>