<?php
require_once __DIR__ . '/config.php';
require_login();
require_once __DIR__ . '/app/views/header.php';
require_once __DIR__ . '/app/database.php';

// -------------------------------------------
// 1) Carregar operações do banco
// -------------------------------------------
$stmt = $db->query("SELECT * FROM operacoes ORDER BY criado_em DESC");
$operacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// -------------------------------------------
// 2) Se existe operação selecionada, carrega períodos dela
// -------------------------------------------
$periodos = [];
if (isset($_GET['op'])) {
    $opId = intval($_GET['op']);
    $stmt = $db->prepare("SELECT * FROM periodos WHERE operacao_id = ? ORDER BY inicio ASC");
    $stmt->execute([$opId]);
    $periodos = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="container mt-4">

    <h2>Operações</h2>

    <a href="/nova_operacao.php" class="btn btn-primary mb-3">+ Nova Operação</a>

    <?php if (count($operacoes) === 0): ?>
        <p>Nenhuma operação cadastrada ainda.</p>
    <?php else: ?>

        <ul class="list-group mb-4">
            <?php foreach ($operacoes as $op): ?>
                <a class="list-group-item list-group-item-action <?= (isset($_GET['op']) && $_GET['op'] == $op['id']) ? 'active' : '' ?>"
                   href="/dashboard.php?op=<?= $op['id'] ?>">
                   
                    <strong><?= htmlspecialchars($op['navio']) ?></strong>
                    — <?= htmlspecialchars($op['produto']) ?>
                    <br>
                    <small><?= htmlspecialchars($op['empresa']) ?> • <?= htmlspecialchars($op['tipo_operacao']) ?></small>
                </a>
            <?php endforeach; ?>
        </ul>

    <?php endif; ?>


    <?php if (isset($opId)): ?>
        <hr>
        <h3>Períodos da Operação</h3>

        <!-- Botão para criar período -->
        <a href="/criar_periodo.php?op=<?= $opId ?>" class="btn btn-success mb-3">
            + Criar Período
        </a>

        <?php if (count($periodos) === 0): ?>
            <p>Nenhum período cadastrado ainda.</p>
        <?php else: ?>

            <ul class="list-group">
                <?php foreach ($periodos as $p): ?>
                    <li class="list-group-item">
                        <strong><?= $p['inicio'] ?> → <?= $p['fim'] ?></strong>
                        <br>
                        Criado em: <?= $p['criado_em'] ?>

                        <div class="mt-2">
                            <a class="btn btn-primary btn-sm"
                               href="/captura.php?periodo=<?= $p['id'] ?>&op=<?= $opId ?>">
                                Abrir Período
                            </a>

                            <a class="btn btn-danger btn-sm"
                               href="/excluir_periodo.php?id=<?= $p['id'] ?>&op=<?= $opId ?>"
                               onclick="return confirm('Excluir este período?')">
                                Excluir
                            </a>
                        </div>
                    </li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
