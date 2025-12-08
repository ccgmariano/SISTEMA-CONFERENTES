<?php
require_once __DIR__ . '/config.php';
require_login();
require_once __DIR__ . '/app/views/header.php';

// Operação atual (se houver)
$operacao = $_SESSION['operacao'] ?? null;

// Períodos já cadastrados na sessão
$periodos = $_SESSION['periodos'] ?? [];

// Mapa rápido para saber quais períodos já existem
$periodosCriados = [];
foreach ($periodos as $p) {
    $chave = $p['inicio'] . '|' . $p['fim'];
    $periodosCriados[$chave] = true;
}

// Períodos oficiais do porto (horários do conferentes.app)
// Ajuste aqui se quiser mudar no futuro
$periodos_porto = [
    ['inicio' => '07:00', 'fim' => '12:59'],
    ['inicio' => '13:00', 'fim' => '18:59'],
    ['inicio' => '19:00', 'fim' => '23:59'],
    ['inicio' => '00:00', 'fim' => '06:59'],
];
?>

<div class="container mt-4" style="max-width: 900px;">

    <h2>Operação Atual</h2>

    <?php if (!$operacao): ?>

        <p>Nenhuma operação criada ainda.</p>
        <a href="/nova_operacao.php" class="btn btn-primary">+ Nova Operação</a>

    <?php else: ?>

        <ul class="list-group mb-4">
            <li class="list-group-item"><strong>Empresa:</strong>
                <?= htmlspecialchars($operacao['empresa'] ?? '') ?></li>
            <li class="list-group-item"><strong>Tipo de Operação:</strong>
                <?= htmlspecialchars($operacao['tipo_operacao'] ?? ($operacao['tipo'] ?? '')) ?></li>
            <li class="list-group-item"><strong>Navio:</strong>
                <?= htmlspecialchars($operacao['navio'] ?? '') ?></li>
            <li class="list-group-item"><strong>Produto:</strong>
                <?= htmlspecialchars($operacao['produto'] ?? '') ?></li>
            <li class="list-group-item"><strong>Recinto:</strong>
                <?= htmlspecialchars($operacao['recinto'] ?? '') ?></li>
        </ul>

        <!-- PERÍODOS OFICIAIS DO PORTO -->
        <h3>Períodos Oficiais do Porto</h3>
        <p class="text-muted">Escolha um período para criar. Se já existir, o botão aparecerá desabilitado.</p>

        <?php foreach ($periodos_porto as $p): ?>
            <?php
                $chave = $p['inicio'] . '|' . $p['fim'];
                $jaCriado = isset($periodosCriados[$chave]);
            ?>

            <?php if ($jaCriado): ?>
                <button class="btn btn-outline-secondary w-100 mb-2" disabled>
                    Período já criado: <?= $p['inicio'] ?> — <?= $p['fim'] ?>
                </button>
            <?php else: ?>
                <form method="POST" action="/periodo_controller.php" class="mb-2">
                    <input type="hidden" name="acao" value="criar">
                    <input type="hidden" name="inicio" value="<?= $p['inicio'] ?>">
                    <input type="hidden" name="fim" value="<?= $p['fim'] ?>">
                    <button class="btn btn-outline-primary w-100">
                        Criar Período: <?= $p['inicio'] ?> — <?= $p['fim'] ?>
                    </button>
                </form>
            <?php endif; ?>

        <?php endforeach; ?>

        <hr class="my-4">

        <!-- PERÍODOS JÁ CADASTRADOS -->
        <h3>Períodos da Operação</h3>

        <?php if (empty($periodos)): ?>

            <p class="text-muted">Nenhum período cadastrado ainda.</p>

        <?php else: ?>

            <ul class="list-group mb-3">
                <?php foreach ($periodos as $index => $per): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <strong>Início:</strong> <?= htmlspecialchars($per['inicio']) ?>
                            —
                            <strong>Fim:</strong> <?= htmlspecialchars($per['fim']) ?>
                        </span>

                        <span>
                            <!-- Entrar / Capturar -->
                            <form method="POST" action="/periodo_controller.php" class="d-inline">
                                <input type="hidden" name="acao" value="selecionar">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button class="btn btn-sm btn-success">
                                    Capturar
                                </button>
                            </form>

                            <!-- Excluir -->
                            <form method="POST" action="/periodo_controller.php" class="d-inline ms-2"
                                  onsubmit="return confirm('Excluir este período?');">
                                <input type="hidden" name="acao" value="excluir">
                                <input type="hidden" name="index" value="<?= $index ?>">
                                <button class="btn btn-sm btn-danger">
                                    Excluir
                                </button>
                            </form>
                        </span>
                    </li>
                <?php endforeach; ?>
            </ul>

        <?php endif; ?>

        <!-- BLOCO DE PERÍODO SELECIONADO (mantém comportamento antigo) -->
        <h3>Período Selecionado</h3>

        <?php if (!isset($_SESSION['periodo'])): ?>

            <p class="text-muted">Nenhum período selecionado ainda.</p>

        <?php else: ?>
            <?php $pSel = $_SESSION['periodo']; ?>
            <ul class="list-group">
                <li class="list-group-item"><strong>Início:</strong> <?= htmlspecialchars($pSel['inicio']) ?></li>
                <li class="list-group-item"><strong>Fim:</strong> <?= htmlspecialchars($pSel['fim']) ?></li>
            </ul>
            <a href="/captura.php" class="btn btn-primary w-100 mt-3">Ir para Captura</a>
        <?php endif; ?>

    <?php endif; ?>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
