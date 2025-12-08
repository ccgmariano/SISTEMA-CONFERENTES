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
            <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($op['empresa']) ?></li>
            <li class="list-group-item"><strong>Tipo:</strong> <?= htmlspecialchars($op['tipo']) ?></li>
            <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($op['navio']) ?></li>
            <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($op['produto']) ?></li>
            <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($op['recinto']) ?></li>
        </ul>

        <h3>Períodos Oficiais do Porto</h3>
        <p class="text-muted">Escolha um período para criar (sempre do dia atual).</p>

        <?php
        // Mesmos horários do conferentes.app
        $periodos = [
            ['07:00', '12:59'],
            ['13:00', '18:59'],
            ['19:00', '23:59'],
            ['00:00', '06:59'],
        ];
        ?>

        <?php foreach ($periodos as $p): ?>
            <form method="POST" action="/periodo_controller.php" class="mb-2">
                <input type="hidden" name="inicio" value="<?= $p[0] ?>">
                <input type="hidden" name="fim" value="<?= $p[1] ?>">
                <button class="btn btn-outline-primary w-100">
                    Criar Período: <?= $p[0] ?> — <?= $p[1] ?>
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
                <li class="list-group-item"><strong>Início:</strong> <?= htmlspecialchars($per['inicio']) ?></li>
                <li class="list-group-item"><strong>Fim:</strong> <?= htmlspecialchars($per['fim']) ?></li>
            </ul>

            <a href="/captura.php" class="btn btn-success w-100 mt-3">Ir para Captura</a>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
