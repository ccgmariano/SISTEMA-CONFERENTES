<?php
require_once __DIR__ . '/config.php';
require_login();
require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-5" style="max-width: 700px;">
    <h3>Operação Criada</h3>

    <p><strong>Empresa:</strong> <?= htmlspecialchars($_GET['empresa'] ?? '') ?></p>
    <p><strong>Tipo de Operação:</strong> <?= htmlspecialchars($_GET['tipo'] ?? '') ?></p>
    <p><strong>Navio:</strong> <?= htmlspecialchars($_GET['navio'] ?? '') ?></p>
    <p><strong>Produto:</strong> <?= htmlspecialchars($_GET['produto'] ?? '') ?></p>

    <a class="btn btn-primary w-100 mt-4" href="/dashboard.php">Voltar ao Dashboard</a>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
