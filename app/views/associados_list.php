<?php require 'header.php'; ?>

<h2>Associados</h2>

<p>
  <a href="?acao=form">Novo Associado</a>
</p>

<table border="1" cellpadding="6">
  <tr>
    <th>Nome</th>
    <th>Código</th>
    <th>Status</th>
    <th>Ações</th>
  </tr>

  <?php foreach ($associados as $a): ?>
    <tr>
      <td><?= htmlspecialchars($a['nome']) ?></td>
      <td><?= htmlspecialchars($a['codigo']) ?></td>
      <td><?= $a['ativo'] ? 'Ativo' : 'Inativo' ?></td>
      <td>
        <a href="?acao=form&id=<?= $a['id'] ?>">Editar</a> |
        <a href="?acao=toggle&id=<?= $a['id'] ?>">
          <?= $a['ativo'] ? 'Desativar' : 'Ativar' ?>
        </a>
      </td>
    </tr>
  <?php endforeach; ?>
</table>

<?php require 'footer.php'; ?>
