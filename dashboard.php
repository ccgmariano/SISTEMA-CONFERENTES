<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// Buscar todas as operações cadastradas
$stmt = $db->query("SELECT * FROM operacoes ORDER BY id DESC");
$operacoes = $stmt->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

    <h2>Operações</h2>

    <a href="/nova_operacao.php" class="btn btn-primary mb-3">+ Nova Operação</a>

    <?php if (empty($operacoes)): ?>

        <p>Nenhuma operação criada ainda.</p>

    <?php else: ?>

        <ul class="list-group">

            <?php foreach ($operacoes as $op): ?>
                <li class="list-group-item">

                    <strong><?= htmlspecialchars($op['empresa']) ?></strong>
                    — <?= htmlspecialchars($op['produto']) ?><br>
                    <small>
                        <?= htmlspecialchars($op['navio']) ?> •  
                        <?= htmlspecialchars($op['tipo_operacao']) ?> •  
                        <?= htmlspecialchars($op['recinto']) ?>
                    </small>

                    <div class="mt-2">
                        <a href="/operacao_view.php?id=<?= $op['id'] ?>" class="btn btn-sm btn-outline-primary">
                            Abrir
                        </a>
                    </div>

                </li>
            <?php endforeach; ?>

        </ul>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
