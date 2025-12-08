<?php
require_once __DIR__ . '/config.php';
require_login();

// Verificar operação e período ativos
$operacao = $_SESSION['operacao_atual'] ?? null;
$periodo  = $_SESSION['periodo_atual'] ?? null;

if (!$operacao || !$periodo) {
    die("Operação ou período não encontrado.");
}

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container" style="max-width:900px; margin-top:30px;">

    <h2>Capturar Pesagens</h2>
    <p class="text-muted">Use os dados abaixo para buscar as pesagens no Poseidon.</p>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($operacao['navio']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($operacao['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($operacao['recinto']) ?></li>
        <li class="list-group-item"><strong>Início:</strong> <?= $periodo['inicio'] ?></li>
        <li class="list-group-item"><strong>Fim:</strong> <?= $periodo['fim'] ?></li>
    </ul>

    <button id="btnCapturar" class="btn btn-primary w-100">
        🔄 Capturar Pesagens
    </button>

    <div id="resultado" class="mt-4"></div>

</div>

<script>
document.getElementById("btnCapturar").addEventListener("click", function () {

    const btn = this;
    btn.disabled = true;
    btn.innerText = "Consultando…";

    fetch("/app/controllers/captura_controller.php")
        .then(r => r.text())
        .then(resp => {
            document.getElementById("resultado").innerHTML = resp;
            btn.disabled = false;
            btn.innerText = "Capturar Novamente";
        })
        .catch(() => {
            document.getElementById("resultado").innerHTML =
                "<div class='alert alert-danger'>Erro ao consultar.</div>";
            btn.disabled = false;
            btn.innerText = "Capturar Pesagens";
        });

});
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
