<?php
session_start();
require_once __DIR__ . '/header.php';

if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<p>Erro: operação ou período não encontrado.</p>";
    require_once __DIR__ . '/footer.php';
    exit;
}

$op = $_SESSION['operacao'];
$per = $_SESSION['periodo'];
?>

<div class="container" style="max-width: 700px; margin-top: 40px;">
    <h2>Resumo do Período</h2>

    <ul class="list-group mt-3">
        <li class="list-group-item"><strong>Navio:</strong> <?= $op['navio'] ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= $op['produto'] ?></li>
        <li class="list-group-item"><strong>Empresa:</strong> <?= $op['empresa'] ?></li>
        <li class="list-group-item"><strong>Tipo de Operação:</strong> <?= $op['tipo_operacao'] ?></li>
        <li class="list-group-item"><strong>Porões:</strong> <?= implode(", ", $op['poreos']) ?></li>
        <li class="list-group-item"><strong>Decks:</strong> <?= implode(", ", $op['decks']) ?></li>
        <li class="list-group-item"><strong>Início do Período:</strong> <?= $per['inicio'] ?></li>
        <li class="list-group-item"><strong>Fim do Período:</strong> <?= $per['fim'] ?></li>
    </ul>

    <a href="/nova_conferencia.php" class="btn btn-secondary w-100 mt-4">Editar Período</a>
    <a href="/captura.php" class="btn btn-primary w-100 mt-2">Capturar Pesagens</a>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
