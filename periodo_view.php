<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ======================================================
// 1. RECEBE O ID DO PERÍODO
// ======================================================
$periodoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($periodoId <= 0) {
    die("Período inválido.");
}

// ======================================================
// 2. BUSCA O PERÍODO
// ======================================================
$stmt = $db->prepare("SELECT * FROM periodos WHERE id = ?");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die("Período não encontrado.");
}

// ======================================================
// 3. BUSCA A OPERAÇÃO
// ======================================================
$stmt = $db->prepare("SELECT * FROM operacoes WHERE id = ?");
$stmt->execute([$periodo['operacao_id']]);
$operacao = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$operacao) {
    die("Operação vinculada ao período não encontrada.");
}

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

    <h2>Período da Operação</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Data:</strong> <?= htmlspecialchars($periodo['data']) ?></li>
        <li class="list-group-item"><strong>Início:</strong> <?= htmlspecialchars($periodo['inicio']) ?></li>
        <li class="list-group-item"><strong>Fim:</strong> <?= htmlspecialchars($periodo['fim']) ?></li>
        <li class="list-group-item"><strong>Criado em:</strong> <?= htmlspecialchars($periodo['criado_em']) ?></li>
    </ul>

    <h4>Dados da Operação</h4>
    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($operacao['empresa']) ?></li>
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($operacao['navio']) ?></li>
        <li class="list-group-item"><strong>Tipo:</strong> <?= htmlspecialchars($operacao['tipo_operacao']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($operacao['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($operacao['recinto']) ?></li>
    </ul>

    <hr>

    <h4>Captura de Pesagens</h4>

    <button id="btnCapturar" class="btn btn-success w-100 mb-3">
        Capturar Pesagens do Período
    </button>

    <div id="resultadoCaptura" class="mt-3"></div>

    <script>
        document.getElementById('btnCapturar').addEventListener('click', function () {
            const btn = this;
            const divResultado = document.getElementById('resultadoCaptura');

            btn.disabled = true;
            btn.innerText = "Consultando Poseidon...";

            fetch("/app/controllers/captura_controller.php?periodo_id=<?= $periodoId ?>")
                .then(r => r.text())
                .then(html => {
                    divResultado.innerHTML = html;
                })
                .catch(err => {
                    divResultado.innerHTML = "<div class='alert alert-danger'>Erro na captura.</div>";
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = "Capturar Pesagens do Período";
                });
        });
    </script>

    <a href="/operacao_view.php?id=<?= $operacao['id'] ?>" class="btn btn-secondary mt-4">
        Voltar para Operação
    </a>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
