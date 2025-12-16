<?php require 'header.php'; ?>

<h2><?= $config['titulo'] ?></h2>

<p>
  <a href="?entidade=<?= $entidade ?>&acao=form">Novo</a>
</p>

<table border="1" cellpadding="6">
<tr>
  <?php foreach ($config['campos'] as $campo => $c): ?>
    <th><?= $c['label'] ?></th>
  <?php endforeach; ?>
  <th>Ações</th>
</tr>

<?php foreach ($registros as $r): ?>
<tr>
  <?php foreach ($config['campos'] as $campo => $c): ?>
    <td><?= htmlspecialchars($r[$campo] ?? '') ?></td>
  <?php endforeach; ?>
  <td>
    <a href="?entidade=<?= $entidade ?>&acao=form&id=<?= $r['id'] ?>">Editar</a>
  </td>
</tr>
<?php endforeach; ?>
</table>

<?php require 'footer.php'; ?>
