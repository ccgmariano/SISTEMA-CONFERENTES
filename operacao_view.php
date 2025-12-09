<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ID da operação que veio pela URL
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Operação inválida.');
}

// Busca a operação
$stmt = $db->prepare('SELECT * FROM operacoes WHERE id = ?');
$stmt->execute([$id]);
$op = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$op) {
    die('Operação não encontrada.');
}

// Busca períodos já cadastrados para essa operação
$stmt = $db->prepare('SELECT * FROM periodos WHERE operacao_id = ? ORDER BY inicio');
$stmt->execute([$id]);
$periodosExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

    <h2>Operação Criada</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($op['empresa']) ?></li>
        <li class="list-group-item"><strong>Tipo de Operação:</strong> <?= htmlspecialchars($op['tipo_operacao']) ?></li>
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($op['navio']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($op['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($op['recinto']) ?></li>
    </ul>

    <h3>Períodos da Operação</h3>

    <p>Selecione um período para criar:</p>

     // HORÁRIOS OFICIAIS (como tínhamos deixado certo antes)
        <?php
$periodosPadrao = [
    ['07:00', '12:59'],
    ['13:00', '18:59'],
    ['19:00', '00:59'],
    ['01:00', '06:59'],
];
?>

    ];
    ?>

    <?php foreach ($periodosPadrao as $p): ?>
        <form method="POST" action="/app/controllers/periodo_controller.php" class="mb-2">
            <input type="hidden" name="operacao_id" value="<?= $op['id'] ?>">
            <input type="hidden" name="inicio" value="<?= $p[0] ?>">
            <input type="hidden" name="fim" value="<?= $p[1] ?>">

            <button class="btn btn-outline-primary w-100" type="submit">
                Criar Período: <?= $p[0] ?> → <?= $p[1] ?>
            </button>
        </form>
    <?php endforeach; ?>

    <hr class="my-4">

    <h4>Períodos existentes</h4>

    <?php if (empty($periodosExistentes)): ?>

        <p>Nenhum período criado ainda.</p>

    <?php else: ?>

        <ul class="list-group">
            <?php foreach ($periodosExistentes as $per): ?>
                <li class="list-group-item">
                    Início: <?= htmlspecialchars($per['inicio']) ?>
                    — Fim: <?= htmlspecialchars($per['fim']) ?>
                    <!-- Depois podemos pôr botões de abrir/editar/excluir aqui -->
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

    <a href="/dashboard.php" class="btn btn-secondary mt-4">Voltar ao Dashboard</a>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
