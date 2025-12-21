<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

$periodoId = (int)($_GET['id'] ?? 0);
if ($periodoId <= 0) die('Período inválido');

// PERÍODO + OPERAÇÃO
$stmt = $db->prepare("
    SELECT p.*, o.empresa, o.navio, o.tipo_operacao, o.produto, o.recinto
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$periodo) die('Período não encontrado');

// CONFIGURAÇÕES DO PERÍODO
$stmt = $db->prepare("SELECT * FROM periodo_config_lancamentos WHERE periodo_id = ?");
$stmt->execute([$periodoId]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// garante linha
if (!$config) {
    $db->prepare("INSERT INTO periodo_config_lancamentos (periodo_id) VALUES (?)")
       ->execute([$periodoId]);
    $stmt->execute([$periodoId]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
}

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

<h4>Configurações de Lançamento</h4>

<div id="configLancamentos" style="display:flex;gap:10px;flex-wrap:wrap">
    <input type="number" id="terno" placeholder="Terno" value="<?= $config['terno'] ?>">
    <input type="number" id="porao" placeholder="Porão" value="<?= $config['porao'] ?>">
    <input type="text" id="deck" placeholder="Deck" value="<?= $config['deck'] ?>">
</div>

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
let pesagemAtual = {};

btnCapturar.onclick = () => {
    fetch('/app/controllers/captura_controller.php?periodo_id=<?= $periodoId ?>')
        .then(r => r.text())
        .then(html => resultadoCaptura.innerHTML = html);
};

function abrirModalPesagem(ticket, placa, peso) {
    pesagemAtual = { ticket, placa, peso };

    m_ticket.innerText = ticket;
    m_placa.innerText  = placa;
    m_peso.innerText   = peso;

    modalPesagem.style.display = 'flex';
}

function fecharModal() {
    modalPesagem.style.display = 'none';
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
            terno: document.getElementById('terno').value,
            porao: document.getElementById('porao').value,
            deck: document.getElementById('deck').value
        })
    })
    .then(r => r.json())
    .then(resp => {
        if (resp.ok) {
            fecharModal();
            btnCapturar.click(); // recarrega lista
        } else {
            alert('Erro ao gravar pesagem');
        }
    });
}
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
