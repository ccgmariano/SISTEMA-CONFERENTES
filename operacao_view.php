<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// Validar ID
if (!isset($_GET['id'])) {
    die("ID inválido.");
}

$id = (int) $_GET['id'];

// Buscar operação
$stmt = $db->prepare("SELECT * FROM operacoes WHERE id = ?");
$stmt->execute([$id]);
$op = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$op) {
    die("Operação não encontrada.");
}

// Buscar períodos dessa operação
$stmt = $db->prepare("SELECT * FROM periodos WHERE operacao_id = ? ORDER BY id ASC");
$stmt->execute([$id]);
$periodos = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4" style="max-width:900px;">

    <h2>Operação Criada</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($op['empresa']) ?></li>
        <li class="list-group-item"><strong>Tipo de Operação:</strong> <?= htmlspecialchars($op['tipo_operacao']) ?></li>
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($op['navio']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($op['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($op['recinto']) ?></li>
    </ul>

    <hr>

    <h3>Períodos da Operação</h3>

    <!-- períodos oficiais -->
    <p>Selecione um período para criar:</p>

    <?php
    $periodos_porto = [
        ["08:00", "12:00"],
        ["12:00", "18:00"],
        ["18:00", "00:00"],
        ["00:00", "08:00"],
    ];
    ?>

    <?php foreach ($periodos_porto as $p): ?>
        <form method="POST" action="/app/controllers/periodo_controller.php" class="mb-2">
            <input type="hidden" name="operacao_id" value="<?= $op['id'] ?>">
            <input type="hidden" name="inicio" value="<?= $p[0] ?>">
            <input type="hidden" name="fim" value="<?= $p[1] ?>">

            <button class="btn btn-outline-primary w-100">
                Criar Período: <?= $p[0] ?> → <?= $p[1] ?>
            </button>
        </form>
    <?php endforeach; ?>

    <hr>

    <h4>Períodos existentes</h4>

    <?php if (empty($periodos)): ?>

        <p>Nenhum período criado ainda.</p>

    <?php else: ?>

        <ul class="list-group">
            <?php foreach ($periodos as $per): ?>
                <li class="list-group-item">
                    <strong><?= $per['inicio'] ?> → <?= $per['fim'] ?></strong>

                    <div class="mt-2">
                        <a href="/captura.php?periodo=<?= $per['id'] ?>" class="btn btn-sm btn-success">
                            Capturar Pesagens
                        </a>

                        <a href="/app/controllers/excluir_periodo.php?id=<?= $per['id'] ?>&op=<?= $op['id'] ?>"
                           class="btn btn-sm btn-danger"
                           onclick="return confirm('Excluir período?');">
                            Excluir
                        </a>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

    <a href="/dashboard.php" class="btn btn-secondary mt-4">Voltar ao Dashboard</a>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
