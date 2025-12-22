<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) die('Período inválido.');

$stmt = $db->prepare("
    SELECT 
        p.*,
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
if (!$periodo) die('Período não encontrado.');

$equipamentos = $db->query("SELECT nome FROM equipamentos WHERE ativo = 1")->fetchAll(PDO::FETCH_COLUMN);
$origens = $db->query("SELECT nome FROM origem_destino")->fetchAll(PDO::FETCH_COLUMN);

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

<h2>Período</h2>

<ul>
    <li><strong>Data:</strong> <?= $periodo['data'] ?></li>
    <li><strong>Horário:</strong> <?= $periodo['inicio'] ?> → <?= $periodo['fim'] ?></li>
    <li><strong>Navio:</strong> <?= $periodo['navio'] ?></li>
</ul>

<hr>

<h4>Configurações de Lançamento</h4>

<div style="display:flex; gap:10px; flex-wrap:wrap;">
    <input id="cfg_terno" type="number" placeholder="Terno" value="1">
    <input id="cfg_porao" type="number" placeholder="Porão">
    <input id="cfg_deck" type="text" placeholder="Deck">

    <select id="cfg_equipamento">
        <option value="">Equipamento</option>
        <?php foreach ($equipamentos as $e): ?>
            <option value="<?= htmlspecialchars($e) ?>"><?= htmlspecialchars($e) ?></option>
        <?php endforeach; ?>
    </select>

    <select id="cfg_origem">
        <option value="">Origem/Destino</option>
        <?php foreach ($origens as $o): ?>
            <option value="<?= htmlspecialchars($o) ?>"><?= htmlspecialchars($o) ?></option>
        <?php endforeach; ?>
    </select>
</div>

<hr>

<h4>Captura de Pesagens</h4>

<button id="btnCapturar">Capturar Pesagens do Período</button>
<div id="resultadoCaptura"></div>

<script>
document.getElementById('btnCapturar').onclick = () => {
    fetch('/app/controllers/captura_controller.php?periodo_id=<?= $id ?>')
        .then(r => r.text())
        .then(html => document.getElementById('resultadoCaptura').innerHTML = html);
};
</script>

</div>

<!-- MODAL -->
<div id="modalPesagem" style="display:none;position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,.5)">
    <div style="background:#fff;width:420px;margin:8% auto;padding:20px;border-radius:6px">
        <h4>Conferência de Pesagem</h4>
        <div id="conteudoModal"></div>
        <button onclick="confirmarPesagem()">Confirmar Pesagem</button>
        <button onclick="fecharModal()">Fechar</button>
    </div>
</div>

<script>
let pesagemAtual = {};

function abrirModalPesagem(a, b, c) {

    if (typeof a === 'string') {
        pesagemAtual.ticket = a;
        pesagemAtual.placa  = b;
        pesagemAtual.peso   = c;
    } else {
        pesagemAtual.ticket = a.dataset.ticket;
        pesagemAtual.placa  = a.dataset.placa;
        pesagemAtual.peso   = a.dataset.peso;
    }

    pesagemAtual.terno = document.getElementById('cfg_terno').value;
    pesagemAtual.porao = document.getElementById('cfg_porao').value;
    pesagemAtual.deck  = document.getElementById('cfg_deck').value;
    pesagemAtual.equip = document.getElementById('cfg_equipamento').value;
    pesagemAtual.orig  = document.getElementById('cfg_origem').value;

    document.getElementById('conteudoModal').innerHTML = `
        <p><strong>Ticket:</strong> ${pesagemAtual.ticket}</p>
        <p><strong>Placa:</strong> ${pesagemAtual.placa}</p>
        <p><strong>Peso Líquido:</strong> ${pesagemAtual.peso}</p>
    `;

    document.getElementById('modalPesagem').style.display = 'block';
}

function confirmarPesagem() {
    fetch('/app/controllers/confirmar_pesagem.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
            periodo_id: <?= $id ?>,
            ...pesagemAtual
        })
    });

    fecharModal();
}

function fecharModal() {
    document.getElementById('modalPesagem').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
