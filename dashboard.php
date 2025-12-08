<?php
require_once __DIR__ . '/config.php';
require_login();

// Se existir operação atual
$operacao = $_SESSION['operacao_atual'] ?? null;

// Lista de períodos (por enquanto apenas array em sessão)
$periodos = $_SESSION['periodos'] ?? [];
?>

<?php include __DIR__ . '/app/views/header.php'; ?>

<div class="container">

    <?php if ($operacao): ?>
        <div class="card">
            <h3>Operação Atual</h3>

            <p><strong>Empresa:</strong> <?= htmlspecialchars($operacao['empresa']) ?></p>
            <p><strong>Navio:</strong> <?= htmlspecialchars($operacao['navio']) ?></p>
            <p><strong>Produto:</strong> <?= htmlspecialchars($operacao['produto']) ?></p>
            <p><strong>Recinto:</strong> <?= htmlspecialchars($operacao['recinto']) ?></p>
            <p><strong>Tipo de Operação:</strong> <?= htmlspecialchars($operacao['tipo']) ?></p>
            <p><strong>Criado em:</strong> <?= htmlspecialchars($operacao['criado_em']) ?></p>
        </div>

        <hr>

        <h3>Períodos da Operação</h3>

        <!-- BOTÃO CRIAR PERÍODO -->
        <p>
            <a href="/novo_periodo.php" class="btn btn-primary">Criar Período</a>
        </p>

        <!-- LISTA DE PERÍODOS -->
        <?php if (empty($periodos)): ?>
            <p><em>Nenhum período cadastrado até o momento.</em></p>
        <?php else: ?>
            <ul class="list-group">
                <?php foreach ($periodos as $p): ?>
                    <li class="list-group-item">
                        <strong>Início:</strong> <?= htmlspecialchars($p['inicio']) ?>
                        —
                        <strong>Fim:</strong> <?= htmlspecialchars($p['fim']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

    <?php else: ?>
        <p><strong>Nenhuma operação ativa.</strong></p>
        <a href="/nova_operacao.php" class="btn btn-primary">Criar nova operação</a>
    <?php endif; ?>

</div>

<?php include __DIR__ . '/app/views/footer.php'; ?>
