<?php require 'header.php'; ?>

<h2><?= isset($assoc) ? 'Editar' : 'Novo' ?> Associado</h2>

<form method="post" action="/app/controllers/associados_controller.php?acao=save">
  <?php if (isset($assoc)): ?>
    <input type="hidden" name="id" value="<?= $assoc['id'] ?>">
  <?php endif; ?>

  <label>Nome</label><br>
  <input name="nome" required value="<?= htmlspecialchars($assoc['nome'] ?? '') ?>"><br><br>

  <label>Código</label><br>
  <input name="codigo" value="<?= htmlspecialchars($assoc['codigo'] ?? '') ?>"><br><br>

  <label>Observações</label><br>
  <textarea name="observacoes"><?= htmlspecialchars($assoc['observacoes'] ?? '') ?></textarea><br><br>

  <button type="submit">Salvar</button>
</form>

<?php require 'footer.php'; ?>
