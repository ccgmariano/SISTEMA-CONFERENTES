<?php require 'header.php'; ?>

<h2><?= $config['titulo'] ?></h2>

<form method="post" action="/app/controllers/crud_controller.php?entidade=<?= $entidade ?>&acao=save">
  <?php if ($registro): ?>
    <input type="hidden" name="id" value="<?= $registro['id'] ?>">
  <?php endif; ?>

  <?php foreach ($config['campos'] as $campo => $c): ?>
    <label><?= $c['label'] ?></label><br>
    <input
      name="<?= $campo ?>"
      value="<?= htmlspecialchars($registro[$campo] ?? '') ?>"
      <?= !empty($c['required']) ? 'required' : '' ?>
    ><br><br>
  <?php endforeach; ?>

  <button type="submit">Salvar</button>
</form>

<?php require 'footer.php'; ?>
