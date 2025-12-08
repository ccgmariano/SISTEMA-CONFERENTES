<?php
require_once 'config.php';
require_login();

// Lista de períodos fixos do porto
$periodos = [
    ["07:00", "12:59"],
    ["13:00", "18:59"],
    ["19:00", "23:59"],
    ["00:00", "06:59"],
];

$dataHoje = date("Y-m-d");

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/header.php';
?>

<div class="container mt-5" style="max-width:600px;">
    <h3 class="mb-4">Selecione o Período</h3>

    <?php foreach ($periodos as $p): ?>
        <form method="POST" action="/app/controllers/periodo_controller.php" class="mb-3">
            <input type="hidden" name="inicio" value="<?= $dataHoje . 'T' . $p[0] ?>">
            <input type="hidden" name="fim" value="<?= $dataHoje . 'T' . $p[1] ?>">

            <button class="btn btn-primary w-100">
                <?= $p[0] ?> — <?= $p[1] ?>
            </button>
        </form>
    <?php endforeach; ?>
</div>

<?php require_once $_SERVER['DOCUMENT_ROOT'] . '/app/views/footer.php'; ?>
