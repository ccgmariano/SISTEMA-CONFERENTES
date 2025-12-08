<?php
require_once __DIR__ . '/config.php';
require_login();
require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

    <h2>Operação Atual</h2>

    <?php if (!isset($_SESSION['operacao'])): ?>
        
        <p>Nenhuma operação criada ainda.</p>
        <a href="/nova_operacao.php" class="btn btn-primary">+ Nova Operação</a>

    <?php else: ?>

        <?php $op = $_SESSION['operacao']; ?>

        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Empresa:</strong> <?= $op['empresa'] ?></li>
            <li class="list-group-item"><strong>Tipo:</strong> <?= $op['tipo'] ?></li>
            <li class="list-group-item"><strong>Navio:</strong> <?= $op['navio'] ?></li>
            <li class="list-group-item"><strong>Produto:</strong> <?= $op['produto'] ?></li>
            <li class="list-group-item"><strong>Recinto:</strong> <?= $op['recinto'] ?></li>
        </ul>

        <h3>Períodos Oficiais do Porto</h3>
        <p class="text-muted">Escolha um período para criar.</p>

        <?php
        $periodos = [
            ["08:00", "12:00"],
            ["12:00", "18:00"],
            ["18:00", "00:00"],
            ["00:00", "08:00"],
        ];
        ?>

        <?php foreach ($periodos as $p): ?>
            <form method="POST" action="/periodo_controller.php" class="mb-2">
                <input type="hidden" name="inicio" value="<?= $p[0] ?>">
                <input type="hidden" name="fim" value="<?= $p[1] ?>">
                <button class="btn btn-outline-primary w-100">
                    Criar Período: <?= $p[0] ?> → <?= $p[1] ?>
                </button>
            </form>
        <?php endforeach; ?>

        <hr class="my-4">

        <h3>Período Selecionado</h3>

        <?php if (!isset($_SESSION['periodo'])): ?>
            <p>Nenhum período cadastrado ainda.</p>
        <?php else: ?>
            <?php $per = $_SESSION['periodo']; ?>
            <ul class="list-group">
                <li class="list-group-item"><strong>Início:</strong> <?= $per['inicio'] ?></li>
                <li class="list-group-item"><strong>Fim:</strong> <?= $per['fim'] ?></li>
            </ul>

            <a href="/captura.php" class="btn btn-success w-100 mt-3">Ir para Captura</a>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
