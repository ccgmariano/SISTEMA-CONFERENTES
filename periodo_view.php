<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ======================================================
// ID DO PERÍODO
// ======================================================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Período inválido.');
}

// ======================================================
// BUSCA PERÍODO + OPERAÇÃO
// ======================================================
$stmt = $db->prepare("
    SELECT 
        p.*,
        o.id AS operacao_id,
        o.empresa,
        o.navio,
        o.tipo_operacao,
        o.produto,
        o.recinto
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$id]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die('Período não encontrado.');
}

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

    <h2>Período</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Data:</strong> <?= htmlspecialchars($periodo['data']) ?></li>
        <li class="list-group-item">
            <strong>Horário:</strong>
            <?= htmlspecialchars($periodo['inicio']) ?> → <?= htmlspecialchars($periodo['fim']) ?>
        </li>
        <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($periodo['empresa']) ?></li>
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($periodo['navio']) ?></li>
        <li class="list-group-item"><strong>Tipo:</strong> <?= htmlspecialchars($periodo['tipo_operacao']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($periodo['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($periodo['recinto']) ?></li>
    </ul>

    <hr>

    <!-- CAPTURA -->
    <h4>Captura de Pesagens</h4>

    <button id="btnCapturar" class="btn btn-success mb-3">
        Capturar Pesagens do Período
    </button>

    <div id="resultadoCaptura"></div>

    <script>
    document.getElementById('btnCapturar').addEventListener('click', function () {
        fetch("/app/controllers/captura_controller.php?periodo_id=<?= (int)$id ?>")
            .then(r => r.text())
            .then(html => document.getElementById('resultadoCaptura').innerHTML = html);
    });
    </script>

</div>

<!-- ====================================================== -->
<!-- MODAL -->
<!-- ====================================================== -->
<div id="modalPesagem"
     style="display:none;
            position:fixed;
            top:0; left:0;
            width:100%; height:100%;
            background:rgba(0,0,0,0.6);
            z-index:9999;">

    <div style="
        background:#fff;
        width:500px;
        margin:80px auto;
        padding:20px;
        position:relative;
        border-radius:4px;
    ">

        <button onclick="window.fecharModal()"
                style="position:absolute; top:10px; right:10px;">
            ✖
        </button>

        <h3>Modal de Pesagem</h3>

        <p>
            Ticket selecionado:
            <strong id="ticketSelecionado"></strong>
        </p>

        <p><em>Se você está vendo isso, o modal FUNCIONA.</em></p>

    </div>
</div>

<!-- ====================================================== -->
<!-- JS GLOBAL (OBRIGATÓRIO PARA FETCH) -->
<!-- ====================================================== -->
<script>
/* FUNÇÕES GLOBAIS */

window.abrirModalPesagem = function(ticket) {
    console.log('abrirModalPesagem chamado:', ticket);

    const span = document.getElementById('ticketSelecionado');
    span.innerText = ticket;

    document.getElementById('modalPesagem').style.display = 'block';
};

window.fecharModal = function() {
    document.getElementById('modalPesagem').style.display = 'none';
};
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
