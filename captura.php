<?php
session_start();
require_once __DIR__ . '/app/views/header.php';

// Precisa existir operação e período
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    echo "<p>Erro: operação ou período não encontrado.</p>";
    require_once __DIR__ . '/app/views/footer.php';
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];
?>

<div class="container" style="margin-top: 30px; max-width: 900px;">

    <h2>Capturar Pesagens</h2>
    <p class="text-muted">Revise os dados abaixo antes de capturar.</p>

    <ul class="list-group mt-3">
        <li class="list-group-item"><strong>Navio:</strong> <?= $op['navio'] ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= $op['produto'] ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= $op['recinto'] ?></li>
        <li class="list-group-item"><strong>Início:</strong> <?= $per['inicio'] ?></li>
        <li class="list-group-item"><strong>Fim:</strong> <?= $per['fim'] ?></li>
    </ul>

    <button class="btn btn-primary w-100 mt-4" id="btnCapturar">
        Capturar Pesagens Automáticas
    </button>

    <div id="resultado" class="mt-4"></div>

</div>

<script>
document.getElementById("btnCapturar").addEventListener("click", function() {
    this.disabled = true;
    this.innerText = "Consultando…";

    fetch("/app/controllers/captura_controller.php")
        .then(r => r.text())
        .then(html => {
            document.getElementById("resultado").innerHTML = html;
            this.disabled = false;
            this.innerText = "Capturar Novamente";
        })
        .catch(err => {
            document.getElementById("resultado").innerHTML = 
                "<div class='alert alert-danger'>Erro na captura</div>";
            this.disabled = false;
            this.innerText = "Capturar Pesagens";
        });
});
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
