<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

$periodoId = (int)($_GET['id'] ?? 0);
if ($periodoId <= 0) die('Período inválido');

$stmt = $db->prepare("
    SELECT p.*, o.empresa, o.navio, o.tipo_operacao, o.produto, o.recinto
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$periodo) die('Período não encontrado');

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

<h2>Período</h2>

<ul>
    <li><strong>Data:</strong> <?= htmlspecialchars($periodo['data']) ?></li>
    <li><strong>Horário:</strong> <?= htmlspecialchars($periodo['inicio']) ?> → <?= htmlspecialchars($periodo['fim']) ?></li>
    <li><strong>Navio:</strong> <?= htmlspecialchars($periodo['navio']) ?></li>
</ul>

<hr>

<h4>Captura de Pesagens</h4>
<button id="btnCapturar">Capturar Pesagens do Período</button>
<div id="resultadoCaptura"></div>

</div>

<!-- MODAL -->
<div id="modalPesagem" style="
    display:none;
    position:fixed;
    inset:0;
    background:rgba(0,0,0,.5);
    align-items:center;
    justify-content:center;
">
    <div style="background:#fff;padding:20px;width:420px">
        <h4>Confirmar Pesagem</h4>

        <p><strong>Ticket:</strong> <span id="m_ticket"></span></p>
        <p><strong>Placa:</strong> <span id="m_placa"></span></p>
        <p><strong>Peso:</strong> <span id="m_peso"></span></p>

        <hr>

        <button onclick="confirmarPesagem()">Confirmar</button>
        <button onclick="fecharModal()">Cancelar</button>
    </div>
</div>

<script>
const btnCapturar = document.getElementById('btnCapturar');
const resultadoCaptura = document.getElementById('resultadoCaptura');

let pesagemAtual = {};

btnCapturar.onclick = function () {
    fetch('/app/controllers/captura_controller.php?periodo_id=<?= $periodoId ?>')
        .then(r => r.text())
        .then(html => resultadoCaptura.innerHTML = html)
        .catch(() => alert('Erro ao capturar pesagens'));
};

function abrirModalPesagem(ticket, placa, peso, dataHora) {
    pesagemAtual = { ticket, placa, peso, dataHora };

    document.getElementById('m_ticket').innerText = ticket;
    document.getElementById('m_placa').innerText  = placa;
    document.getElementById('m_peso').innerText   = peso;

    document.getElementById('modalPesagem').style.display = 'flex';
}

function fecharModal() {
    document.getElementById('modalPesagem').style.display = 'none';
}

function confirmarPesagem() {
    fetch('/app/controllers/pesagem_confirmar.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            periodo_id: <?= $periodoId ?>,
            ticket: pesagemAtual.ticket,
            placa: pesagemAtual.placa,
            peso: pesagemAtual.peso,
            data_hora: pesagemAtual.dataHora
        })
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.ok) {
            fecharModal();
            btnCapturar.click();
        } else {
            alert('Erro ao gravar pesagem');
        }
    });
}
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
