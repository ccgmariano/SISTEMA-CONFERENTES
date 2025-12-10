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

    <p>Selecione a data base dos períodos:</p>

    <form method="GET" action="">
        <input type="hidden" name="id" value="<?= $op['id'] ?>">

        <div class="mb-3" style="max-width: 250px;">
            <input type="date" name="data_base"
                   value="<?= isset($_GET['data_base']) ? $_GET['data_base'] : date('d-m-yy') ?>"
                   class="form-control" required>
        </div>

        <button class="btn btn-primary mb-4" type="submit">
            Atualizar Períodos
        </button>
    </form>

    <?php
    // DATA BASE PARA MONTAR OS PERÍODOS
    $dataBase = isset($_GET['data_base']) ? $_GET['data_base'] : date('d-m-yy');

    // Converter para timestamp
    $tsBase = strtotime($dataBase);

    // Calcular datas de virada para períodos 3 e 4
    $dataBaseMais1 = date('d-m-yy', $tsBase + 86400);

    // Períodos calculados com data + hora
    $periodosCalculados = [
        [
            'inicio' => $dataBase . ' 07:00',
            'fim'    => $dataBase . ' 12:59'
        ],
        [
            'inicio' => $dataBase . ' 13:00',
            'fim'    => $dataBase . ' 18:59'
        ],
        [
            'inicio' => $dataBase . ' 19:00',
            'fim'    => $dataBaseMais1 . ' 00:59'
        ],
        [
            'inicio' => $dataBaseMais1 . ' 01:00',
            'fim'    => $dataBaseMais1 . ' 06:59'
        ],
    ];
    ?>

    <h4>Selecionar Período para Criar</h4>

    <?php foreach ($periodosCalculados as $p): ?>
        <form method="POST" action="/app/controllers/periodo_controller.php" class="mb-2">

            <input type="hidden" name="operacao_id" value="<?= $op['id'] ?>">
            <input type="hidden" name="inicio" value="<?= $p['inicio'] ?>">
            <input type="hidden" name="fim" value="<?= $p['fim'] ?>">

            <button class="btn btn-outline-primary w-100" type="submit">
                Criar Período: <?= $p['inicio'] ?> → <?= $p['fim'] ?>
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
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <span>
                        Início: <?= htmlspecialchars($per['inicio']) ?>
                        — Fim: <?= htmlspecialchars($per['fim']) ?>
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
