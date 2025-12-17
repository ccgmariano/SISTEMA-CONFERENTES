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

// Busca períodos
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

<div class="container">

    <h2>Operação</h2>

    <ul>
        <li><strong>Empresa:</strong> <?= htmlspecialchars($op['empresa']) ?></li>
        <li><strong>Tipo:</strong> <?= htmlspecialchars($op['tipo_operacao']) ?></li>
        <li><strong>Navio:</strong> <?= htmlspecialchars($op['navio']) ?></li>
        <li><strong>Produto:</strong> <?= htmlspecialchars($op['produto']) ?></li>
        <li><strong>Recinto:</strong> <?= htmlspecialchars($op['recinto']) ?></li>
    </ul>

    <hr>

    <h3>Criar novo período</h3>

    <form method="POST" action="/app/controllers/periodo_controller.php">

        <input type="hidden" name="operacao_id" value="<?= $op['id'] ?>">

        <p>
            <label><strong>Data:</strong></label><br>
            <input type="date" name="data" required>
        </p>

        <p>
            <label><strong>Período:</strong></label><br>
            <select name="periodo_escolhido" required>
                <option value="07:00|12:59">Período 1 — 07:00 às 12:59</option>
                <option value="13:00|18:59">Período 2 — 13:00 às 18:59</option>
                <option value="19:00|00:59">Período 3 — 19:00 às 00:59</option>
                <option value="01:00|06:59">Período 4 — 01:00 às 06:59</option>
            </select>
        </p>

        <hr>

        <h3>Funções e Conferentes</h3>
        <p><em>Marque as funções do período e escolha os conferentes para cada uma.</em></p>

        <?php foreach ($funcoes as $funcao): ?>
            <fieldset style="margin-bottom:15px; padding:10px; border:1px solid #999;">
                <legend>
                    <label>
                        <input type="checkbox" name="funcoes[]" value="<?= $funcao['id'] ?>">
                        <strong><?= htmlspecialchars($funcao['nome']) ?></strong>
                    </label>
                </legend>

                <p>Conferentes desta função:</p>

                <select name="conferentes[<?= $funcao['id'] ?>][]" multiple size="5">
                    <?php foreach ($associados as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>
        <?php endforeach; ?>

        <p>
            <button type="submit">Criar Período</button>
        </p>

    </form>

    <hr>

    <h3>Períodos existentes</h3>

    <?php if (empty($periodosExistentes)): ?>
        <p>Nenhum período criado.</p>
    <?php else: ?>
        <ul>
            <?php foreach ($periodosExistentes as $per): ?>
                <li>
                    <?= htmlspecialchars($per['data']) ?>
                    — <?= htmlspecialchars($per['inicio']) ?> → <?= htmlspecialchars($per['fim']) ?>
                    |
                    <a href="/periodo_view.php?id=<?= (int)$per['id'] ?>">Abrir</a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <p>
        <a href="/dashboard.php">Voltar ao Dashboard</a>
    </p>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
