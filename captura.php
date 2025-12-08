<?php
// captura.php (RAIZ)

require_once __DIR__ . '/config.php';
require_login();

// Header (já puxa APP_NAME, CSS etc.)
require_once __DIR__ . '/app/views/header.php';

// Conferência: precisamos ter operação e período na sessão
if (!isset($_SESSION['operacao']) || !isset($_SESSION['periodo'])) {
    ?>
    <div class="container" style="margin-top:30px; max-width:900px;">
        <div class="alert alert-warning">
            Operação ou período não encontrado na sessão.<br>
            Volte ao <a href="/dashboard.php">dashboard</a>, selecione uma operação e crie/abra um período.
        </div>
    </div>
    <?php
    require_once __DIR__ . '/app/views/footer.php';
    exit;
}

$op  = $_SESSION['operacao'];
$per = $_SESSION['periodo'];
?>

<div class="container" style="margin-top: 30px; max-width: 900px;">

    <h2>Capturar Pesagens</h2>
    <p class="text-muted">
        Esses são os dados que serão enviados para o Poseidon (via teste.php do conferentes.app.br):
    </p>

    <ul class="list-group mt-3">
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($op['navio']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($op['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($op['recinto']) ?></li>
        <li class="list-group-item"><strong>Início:</strong> <?= htmlspecialchars($per['inicio']) ?></li>
        <li class="list-group-item"><strong>Fim:</strong> <?= htmlspecialchars($per['fim']) ?></li>
    </ul>

    <button class="btn btn-primary w-100 mt-4" id="btnCapturar">
        Capturar Pesagens (Poseidon)
    </button>

    <div id="resultado" class="mt-4"></div>

</div>

<script>
document.getElementById("btnCapturar").addEventListener("click", function() {
    const btn = this;
    const divResultado = document.getElementById("resultado");

    btn.disabled = true;
    btn.innerText = "Consultando…";
    divResultado.innerHTML = "<div class='alert alert-info'>Consultando Poseidon via conferentes.app.br...</div>";

    fetch("/app/controllers/captura_controller.php")
        .then(r => r.text())
        .then(html => {
            divResultado.innerHTML = html;
            btn.disabled = false;
            btn.innerText = "Capturar Novamente";
        })
        .catch(err => {
            console.error(err);
            divResultado.innerHTML =
                "<div class='alert alert-danger'>Erro na captura. Tente novamente.</div>";
            btn.disabled = false;
            btn.innerText = "Capturar Pesagens (Poseidon)";
        });
});
</script>

<?php
require_once __DIR__ . '/app/views/footer.php';
?>
