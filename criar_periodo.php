<?php
require_once __DIR__ . '/app/views/header.php';

$periodos = [
    ['07:00', '12:59'],
    ['13:00', '18:59'],
    ['19:00', '23:59'],
    ['00:00', '06:59'],
];

$hoje = date('Y-m-d');
?>

<div class="container" style="max-width:700px; margin-top:30px;">
    <h2>Selecione o Período</h2>

    <?php foreach ($periodos as $p): ?>
        <form method="POST" action="/periodo_controller.php">
            <input type="hidden" name="inicio" value="<?= $hoje . 'T' . $p[0] ?>">
            <input type="hidden" name="fim"    value="<?= $hoje . 'T' . $p[1] ?>">

            <button class="btn btn-primary w-100 mt-3">
                <?= $p[0] ?> — <?= $p[1] ?>
            </button>
        </form>
    <?php endforeach; ?>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
