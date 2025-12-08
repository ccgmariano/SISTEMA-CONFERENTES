<?php
require_once __DIR__ . '/config.php';
require_login();

// Recupera ID do período selecionado
$index = $_GET['p'] ?? null;

if ($index === null || !isset($_SESSION['periodos'][$index])) {
    die("Período inválido.");
}

$periodo = $_SESSION['periodos'][$index];
$operacao = $_SESSION['operacao_atual'] ?? null;

// Lista de pesagens (simulação agora — real via Poseidon depois)
$pesagens = $_SESSION['pesagens'][$index] ?? [];

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container" style="max-width: 900px; margin-top: 30px;">
    <h2>Período de Trabalho</h2>

    <p><strong>Início:</strong> <?= htmlspecialchars($periodo['inicio']) ?></p>
    <p><strong>Fim:</strong> <?= htmlspecialchars($periodo['fim']) ?></p>

    <hr>

    <h3>Captura de Pesagens</h3>

    <a class="btn btn-primary" href="/captura.php?p=<?= $index ?>" style="margin-bottom:15px;">
        🔄 Atualizar Pesagens
    </a>

    <?php if (empty($pesagens)): ?>
        <p>Nenhuma pesagem capturada ainda.</p>
    <?php else: ?>
        <table class="tabela">
            <thead>
                <tr>
                    <th>Ticket</th>
                    <th>Data/Hora</th>
                    <th>Placa</th>
                    <th>Peso (kg)</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pesagens as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['ticket']) ?></td>
                    <td><?= htmlspecialchars($p['data']) ?></td>
                    <td><?= htmlspecialchars($p['placa']) ?></td>
                    <td><?= number_format($p['peso'], 0, ',', '.') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
