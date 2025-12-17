<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ID do período
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Período inválido.');
}

// Buscar período
$stmt = $db->prepare("
    SELECT p.*, o.id AS operacao_id
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die('Período não encontrado.');
}

// Buscar funções do período
$stmt = $db->prepare("
    SELECT
        pf.id AS periodo_funcao_id,
        f.id  AS funcao_id,
        f.nome AS funcao_nome
    FROM periodo_funcoes pf
    JOIN funcoes f ON f.id = pf.funcao_id
    WHERE pf.periodo_id = ?
    ORDER BY f.nome
");
$stmt->execute([$id]);
$funcoesPeriodo = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container">

    <h2>Período</h2>

    <ul>
        <li><strong>Data:</strong> <?= htmlspecialchars($periodo['data']) ?></li>
        <li><strong>Horário:</strong> <?= htmlspecialchars($periodo['inicio']) ?> → <?= htmlspecialchars($periodo['fim']) ?></li>
    </ul>

    <hr>

    <!-- AÇÕES DO PERÍODO -->
    <p>
        <a href="/pesagens_view.php?periodo_id=<?= (int)$id ?>"
           class="btn btn-primary">
           Consultar Pesagens
        </a>
    </p>

    <hr>

    <h3>Funções escaladas</h3>

    <?php if (empty($funcoesPeriodo)): ?>

        <p><em>Nenhuma função foi escalada para este período.</em></p>

    <?php else: ?>

        <?php foreach ($funcoesPeriodo as $f): ?>

            <fieldset style="margin-bottom:15px; padding:10px; border:1px solid #999;">
                <legend><strong><?= htmlspecialchars($f['funcao_nome']) ?></strong></legend>

                <?php
                $stmt = $db->prepare("
                    SELECT a.nome
                    FROM periodo_conferentes pc
                    JOIN associados a ON a.id = pc.associado_id
                    WHERE pc.periodo_funcao_id = ?
                    ORDER BY a.nome
                ");
                $stmt->execute([$f['periodo_funcao_id']]);
                $conferentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (empty($conferentes)): ?>
                    <p>Nenhum conferente atribuído.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($conferentes as $c): ?>
                            <li><?= htmlspecialchars($c['nome']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>

            </fieldset>

        <?php endforeach; ?>

    <?php endif; ?>

    <p>
        <a href="/operacao_view.php?id=<?= (int)$periodo['operacao_id'] ?>">
            Voltar à Operação
        </a>
    </p>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
