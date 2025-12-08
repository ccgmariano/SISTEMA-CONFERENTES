<?php
require_once __DIR__ . '/config.php';
require_login();

// Carrega operação atual (se existir)
$operacao = $_SESSION['operacao_atual'] ?? null;

// Carrega períodos já criados
$periodos = $_SESSION['periodos'] ?? [];

// Gera os 4 períodos oficiais do porto (dia atual)
$hoje = date('Y-m-d');
$periodos_padrao = [
    ['inicio' => "$hoje 07:00", 'fim' => "$hoje 12:59"],
    ['inicio' => "$hoje 13:00", 'fim' => "$hoje 18:59"],
    ['inicio' => "$hoje 19:00", 'fim' => "$hoje 23:59"],
    ['inicio' => date('Y-m-d H:i', strtotime("$hoje 00:00")), 'fim' => "$hoje 06:59"],
];

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container" style="max-width:900px; margin-top:30px;">

    <h2>Operação Atual</h2>
    <hr>

    <?php if (!$operacao): ?>
        <p>Nenhuma operação criada ainda.</p>
        <a class="btn btn-primary" href="/nova_operacao.php">Criar Operação</a>

    <?php else: ?>

        <div class="card" style="padding:20px; margin-bottom:30px;">

            <p><strong>Empresa:</strong> <?= htmlspecialchars($operacao['empresa']) ?></p>
            <p><strong>Navio:</strong> <?= htmlspecialchars($operacao['navio']) ?></p>
            <p><strong>Produto:</strong> <?= htmlspecialchars($operacao['produto']) ?></p>
            <p><strong>Recinto:</strong> <?= htmlspecialchars($operacao['recinto']) ?></p>
            <p><strong>Tipo de Operação:</strong> <?= htmlspecialchars($operacao['tipo']) ?></p>
            <p><strong>Criado em:</strong> <?= htmlspecialchars($operacao['criado_em']) ?></p>

        </div>

        <h3>Períodos da Operação</h3>
        <hr>

        <p>Selecione um dos períodos oficiais do porto para cadastrar:</p>

        <div style="display:flex; flex-wrap:wrap; gap:15px; margin:20px 0;">
            <?php foreach ($periodos_padrao as $p): ?>

                <form method="POST" action="/criar_periodo.php" style="display:inline;">
                    <input type="hidden" name="inicio" value="<?= $p['inicio'] ?>">
                    <input type="hidden" name="fim" value="<?= $p['fim'] ?>">

                    <button class="btn btn-secondary">
                        <?= date('H:i', strtotime($p['inicio'])) ?> —
                        <?= date('H:i', strtotime($p['fim'])) ?>
                    </button>
                </form>

            <?php endforeach; ?>
        </div>

        <?php if (!empty($periodos)): ?>
            <h4>Períodos já cadastrados</h4>
            <ul>
                <?php foreach ($periodos as $p): ?>
                    <li>
                        <strong>Início:</strong> <?= htmlspecialchars($p['inicio']) ?>
                        —
                        <strong>Fim:</strong> <?= htmlspecialchars($p['fim']) ?>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>Nenhum período cadastrado ainda.</p>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
