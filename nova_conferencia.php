<?php
session_start();
require_once __DIR__ . '/app/views/header.php';

// Se não existir operação criada, voltar
if (!isset($_SESSION['operacao'])) {
    header("Location: /nova_operacao.php");
    exit;
}
?>

<div class="container" style="max-width: 600px; margin-top: 30px;">
    <h2>Criar Período de Trabalho</h2>
    <p class="text-muted">Informe o intervalo de datas para capturar as pesagens.</p>

    <form method="POST" action="/app/controllers/periodo_controller.php">

        <label class="mt-3">Início do Período</label>
        <input type="datetime-local" name="inicio" class="form-control" required>

        <label class="mt-3">Fim do Período</label>
        <input type="datetime-local" name="fim" class="form-control" required>

        <button class="btn btn-primary w-100 mt-4">Salvar Período</button>

    </form>

    <a href="/nova_operacao.php" class="btn btn-secondary w-100 mt-2">Voltar</a>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
