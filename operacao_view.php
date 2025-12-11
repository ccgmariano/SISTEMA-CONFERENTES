<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ID da operação
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Operação inválida.');
}

// Busca operação
$stmt = $db->prepare('SELECT * FROM operacoes WHERE id = ?');
$stmt->execute([$id]);
$op = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$op) {
    die('Operação não encontrada.');
}

// Busca períodos existentes
$stmt = $db->prepare('SELECT * FROM periodos WHERE operacao_id = ? ORDER BY data, inicio');
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

    <h3>Criar novo período</h3>
    <p>Escolha a data e selecione o período desejado.</p>

    <!-- Seleção da data -->
    <label class="form-label fw-bold">Data do Período:</label>
    <input id="dataPeriodo" type="date" class="form-control mb-3" required>

    <label class="form-label fw-bold mt-3">Selecione o período:</label>

    <?php
    // Períodos oficiais do porto
    $periodosPadrao = [
        ['07:00', '12:59'],
        ['13:00', '18:59'],
        ['19:00', '00:59'],
        ['01:00', '06:59'],
    ];
    ?>

    <?php foreach ($periodosPadrao as $p): ?>
        <form method="POST" action="/app/controllers/periodo_controller.php" class="mb-2">

            <input type="hidden" name="operacao_id" value="<?= $op['id'] ?>">

            <!-- Data real do período -->
            <input type="hidden" name="data" class="campoDataPeriodo">

            <!-- Horários -->
            <input type="hidden" name="inicio" value="<?= $p[0] ?>">
            <input type="hidden" name="fim" value="<?= $p[1] ?>">

            <button type="submit" class="btn btn-outline-primary w-100">
                Criar Período: <?= $p[0] ?> → <?= $p[1] ?>
            </button>
        </form>
    <?php endforeach; ?>

    <script>
        // Sempre que a data mudar, atualizar TODOS os formulários
        document.getElementById('dataPeriodo').addEventListener('change', function () {
            document.querySelectorAll('.campoDataPeriodo').forEach(el => {
                el.value = this.value;
            });
        });
    </script>

    <hr class="my-4">

    <h4>Períodos existentes</h4>

    <?php if (empty($periodosExistentes)): ?>

        <p>Nenhum período criado ainda.</p>

    <?php else: ?>

        <ul class="list-group">
            <?php foreach ($periodosExistentes as $per): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        <strong><?= htmlspecialchars($per['data']) ?></strong>
                        — <?= htmlspecialchars($per['inicio']) ?> → <?= htmlspecialchars($per['fim']) ?>
                    </span>

                    <a href="/periodo_view.php?id=<?= (int)$per['id'] ?>"
                       class="btn btn-sm btn-outline-primary">
                        Abrir período
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

    <a href="/dashboard.php" class="btn btn-secondary mt-4">Voltar ao Dashboard</a>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
