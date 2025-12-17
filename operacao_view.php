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

// Busca períodos da operação
$stmt = $db->prepare('SELECT * FROM periodos WHERE operacao_id = ? ORDER BY id');
$stmt->execute([$id]);
$periodosExistentes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca funções
$stmt = $db->query('SELECT id, nome FROM funcoes WHERE ativo = 1 ORDER BY nome');
$funcoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Busca associados
$stmt = $db->query('SELECT id, nome FROM associados WHERE ativo = 1 ORDER BY nome');
$associados = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    <form method="POST" action="/app/controllers/periodo_controller.php">

        <input type="hidden" name="operacao_id" value="<?= $op['id'] ?>">

        <label class="form-label fw-bold">Data</label>
        <input type="date" class="form-control mb-3" name="data" required>

        <label class="form-label fw-bold">Período</label>
        <select name="periodo_escolhido" class="form-control mb-3" required>
            <option value="07:00|12:59">Período 1 (07:00–12:59)</option>
            <option value="13:00|18:59">Período 2 (13:00–18:59)</option>
            <option value="19:00|00:59">Período 3 (19:00–00:59)</option>
            <option value="01:00|06:59">Período 4 (01:00–06:59)</option>
        </select>

        <hr>

        <h4>Funções e Conferentes</h4>

        <?php foreach ($funcoes as $funcao): ?>
            <div style="margin-bottom:15px; padding:10px; border:1px solid #ddd;">
                <label class="fw-bold">
                    <input type="checkbox" name="funcoes[]" value="<?= $funcao['id'] ?>">
                    <?= htmlspecialchars($funcao['nome']) ?>
                </label>

                <br>

                <small>Conferentes:</small>
                <select name="conferentes[<?= $funcao['id'] ?>][]" multiple size="4" class="form-control">
                    <?php foreach ($associados as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
        <?php endforeach; ?>

        <button type="submit" class="btn btn-primary mt-3">
            Criar Período
        </button>

    </form>

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
