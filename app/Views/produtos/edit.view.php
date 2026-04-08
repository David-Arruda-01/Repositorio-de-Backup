<div class="card card-user">
  <!-- Alterar action para a rota de update e adicionar method PUT -->
  <form action="<?= route('produto.update', ['id' => $id]) ?>" method="POST">

    <div class="card-header">
      <h5 class="card-title">Atualizar Produto</h5>
    </div>

    <div class="card-body px-4">
      <?= CSRF(); ?>
      <input type="hidden" name="id" value="<?= $id ?? '' ?>">

      <div class="row">
        <!-- Nome -->
        <div class="col-md-6">
          <div class="form-group">
            <label for="nome">Nome do produto</label>
            <input type="text" id="nome" name="nome"
              class="form-control <?= has_error('nome', 'is-invalid') ?>"
              placeholder="Nome do produto"
              value="<?= old('nome', $nome ?? '') ?>" required>
            <div class="invalid-feedback">
              <?php foreach (errors('nome') as $erro): ?>
                <div><?= $erro ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Valor -->
        <div class="col-md-3">
          <div class="form-group">
            <label for="valor_un">Valor do produto</label>
            <input type="number" id="valor_un" name="valor_un"
              class="form-control <?= has_error('valor_un', 'is-invalid') ?>"
              placeholder="0,00"
              value="<?= old('valor_un', $valor_un ?? '') ?>"
              step="0.01" min="0" lang="pt-BR" required>
            <div class="invalid-feedback">
              <?php foreach (errors('valor_un') as $erro): ?>
                <div><?= $erro ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <!-- Unidade -->
        <div class="col-md-3">
          <div class="form-group">
            <label for="unidade_medida">Unidade</label>
            <select id="unidade_medida" name="unidade_medida"
              class="form-control <?= has_error('unidade_medida', 'is-invalid') ?>">
              <option value="unidade" <?= old('unidade_medida', $unidade_medida ?? '') === 'unidade' ? 'selected' : '' ?>>Unidade</option>
              <option value="quilo" <?= old('unidade_medida', $unidade_medida ?? '') === 'quilo' ? 'selected' : '' ?>>Quilo</option>
              <option value="grama" <?= old('unidade_medida', $unidade_medida ?? '') === 'grama' ? 'selected' : '' ?>>Grama</option>
            </select>
            <div class="invalid-feedback">
              <?php foreach (errors('unidade_medida') as $erro): ?>
                <div><?= $erro ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Descrição -->
      <div class="row">
        <div class="col-md-12">
          <div class="form-group">
            <label for="descricao">Descrição do Produto</label>
            <textarea id="descricao" name="descricao" rows="3"
              class="form-control <?= has_error('descricao', 'is-invalid') ?>"
              placeholder="Descreva o produto"><?= old('descricao', $descricao ?? '') ?></textarea>
            <div class="invalid-feedback">
              <?php foreach (errors('descricao') as $erro): ?>
                <div><?= $erro ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>

      <!-- Quantidade em estoque -->
      <div class="row">
        <div class="col-md-4">
          <div class="form-group">
            <label for="disponivel">Disponível em estoque</label>
            <select id="disponivel" name="disponivel"
              class="form-control <?= has_error('disponivel', 'is-invalid') ?>">
              <option value="1" <?= old('disponivel', $disponivel ?? '') == '1' ? 'selected' : '' ?>>Sim</option>
              <option value="0" <?= old('disponivel', $disponivel ?? '') == '0' ? 'selected' : '' ?>>Não</option>
            </select>
            <div class="invalid-feedback">
              <?php foreach (errors('disponivel') as $erro): ?>
                <div><?= $erro ?></div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="card-footer text-right">
      <a href="<?= route('produto.list') ?>" class="btn btn-warning">
        <i class="fa fa-arrow-left mr-2"></i> Voltar
      </a>
      <button type="submit" class="btn btn-primary btn-round">
        <i class="fa fa-floppy-o"></i> Salvar
      </button>
    </div>
  </form>
</div>