<?php
require_once __DIR__ . '/config.php';
require_login();
?>

<?php include __DIR__ . '/app/views/header.php'; ?>

<div class="container" style="max-width:600px;">
    <h2>Novo Período</h2>

    <form action="/periodo_controller.php" method="POST">

        <label>Início</label>
        <input type="datetime-local" name="inicio" required class="form-control">

        <label>Fim</label>
        <input type="datetime-local" name="fim" required class="form-control">

        <br>
        <button class="btn btn-primary w-100">Salvar Período</button>
    </form>
</div>

<?php include __DIR__ . '/app/views/footer.php'; ?>
