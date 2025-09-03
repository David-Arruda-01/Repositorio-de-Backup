<div class="card card-user">
  <form action="<?= route('produto.storage') ?>" method="POST">
    <div class="card-header">
      <h5 class="card-title">Cadastro de um novo produto</h5>
    </div>
    <div class="card-body px-4">
      <?= CSRF(); ?>
      <div class="row">
        <div class="col-md-3 pl-1">
          <div class="form-group">
            <label for="nome">Nome do produto</label>
            <input type="text" id="nome" name="nome" class="form-control <?= has_error('nome', 'is-invalid') ?>"
              placeholder="Nome" value="<?= old('nome', $nome ?? '') ?>" required>
            <div class="invalid-feedback">
              <ul>
                <?php foreach (errors('nome') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
 
        <div class="col-md-4 pl-1"> 
          <div class="form-group">
            <label for="valor_un">Insira o valor do produto</label>
            <input type="number" id="valor_un" name="valor_un" 
                  class="form-control <?= has_error('valor_un', 'is-invalid') ?>" 
                  placeholder="Insira o valor" 
                  value="<?= old('valor_un', $valor_un ?? '') ?>" 
                  step="0.01" min="0" lang="pt-BR" required>
            <div class='invalid-feedback'>
              <ul>
                <?php foreach (errors('valor_un') as $erro): ?>
                  <li><?= $erro ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        </div>
        <div class="col-md-2 pl-1">
          <div class="form-group">
            <label id="disponivel">Quantidade em estoque</label>
            <select id="disponivel" name="disponivel" class="form-control mt-2 
                <?=has_error('disponivel','is-invalid')?>">
                <option value="1" <?= old('disponivel', $disponivel ?? '') === '1' ? 'selected' : ''; ?>>Sim</option>
                <option value="0" <?= old('disponivel', $disponivel ?? '') === '0' ? 'selected' : ''; ?>>Não</option>
              </select>
              <div class='invalid-feedback'>
                <ul>
                  <?php foreach (errors('disponivel') as $erro): ?>
                    <li><?= $erro ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
          </div>
        </div>
        <div class="col-md-2 pl-1">
          <div class="form-group">
            <label id="unidade_medida">Unidade</label>
              <select id="unidade_medida" name="unidade_medida" class="form-control mt-2 
                <?=has_error('unidade_medida','is-invalid')?>">
                <option value="unidade" <?= old('unidade_medida', $unidade_medida ?? '') === 'unidade' ? 'selected' : ''; ?>>Unidade</option>
                <option value="quilo" <?= old('unidade_medida', $unidade_medida ?? '') === 'quilo' ? 'selected' : ''; ?>>Quilo</option>
                <option value="gramas" <?= old('unidade_medida', $unidade_medida ?? '') === 'gramas' ? 'selected' : ''; ?>>Gramas</option>
              </select>
              <div class='invalid-feedback'>
                <ul>
                  <?php foreach (errors('unidade_medida') as $erro): ?>
                  <li><?= $erro ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
          </div>
        </div>
      </div>
      
      <div class="row">
        <div class="col-md-7 pl-1">
          <div class="form-group">
            <label for="descricao">Descrição do Produto</label>
            <input type="text" id="Descricao" name="descricao"
              class="form-control  <?= has_error('descricao', 'is-invalid') ?>" id="Descricao" placeholder="Descrição"
              value="<?= old('descricao', $descricao ?? ''); ?>">
              <div class='invalid-feedback'>
                <ul>
                  <?php foreach (errors('Descricao') as $erro): ?>
                    <li><?= $erro ?></li>
                  <?php endforeach; ?>
                </ul>
              </div>
          </div>
        </div>
        
      </div>
    </div>
    <div class="card-footer text-right">
      <a href="<?=route('produto.list')?>" class="btn btn-warning">
        <i class="fa fa-arrow-left mr-2">voltar</i>
      </a>
      <button type="submit" class="btn btn-primary btn-round"><i class="fa fa-floppy-o"></i> Salvar</button>
    </div>
  </form>
</div>