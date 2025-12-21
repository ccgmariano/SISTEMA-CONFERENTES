<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

$periodoId = (int)($_GET['id'] ?? 0);
if ($periodoId <= 0) die('Período inválido');

// PERÍODO
$stmt = $db->prepare("
    SELECT p.*, o.empresa, o.navio, o.tipo_operacao, o.produto, o.recinto
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$periodo) die('Período não encontrado');

// CONFIG DO PERÍODO
$stmt = $db->prepare("SELECT * FROM periodo_config_lancamentos WHERE periodo_id = ?");
$stmt->execute([$periodoId]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$config) {
    $db->prepare("INSERT INTO periodo_config_lancamentos (periodo_id) VALUES (?)")
       ->execute([$periodoId]);
    $stmt->execute([$periodoId]);
    $config = $stmt->fetch(PDO::FETCH_ASSOC);
}

$equipamentos = $db->query("SELECT id, nome FROM equipamentos")->fetchAll(PDO::FETCH_ASSOC);
$origens = $db->query("SELECT id, nome FROM origem_destino")->fetchAll(PDO::FETCH_ASSOC);

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

<div style="display:flex; gap:10px; flex-wrap:wrap" id="configLancamentos">
    <select id="terno">
        <option value="">Terno</option>
        <?php for ($i=1;$i<=10;$i++): ?>
            <option value="<?= $i ?>" <?= $config['terno']==$i?'selected':'' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <select id="equipamento">
        <option value="">Equipamento</option>
        <?php foreach ($equipamentos as $e): ?>
            <option value="<?= $e['id'] ?>" <?= $config['equipamento_id']==$e['id']?'selected':'' ?>>
                <?= htmlspecialchars($e['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>

    <select id="porao">
        <option value="">Porão</option>
        <?php for ($i=1;$i<=10;$i++): ?>
            <option value="<?= $i ?>" <?= $config['porao']==$i?'selected':'' ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>

    <select id="deck">
        <option value="">Deck</option>
        <option value="LH" <?= $config['deck']=='LH'?'selected':'' ?>>LH</option>
        <option value="SH" <?= $config['deck']=='SH'?'selected':'' ?>>SH</option>
    </select>

    <select id="origem_destino">
        <option value="">Origem/Destino</option>
        <?php foreach ($origens as $o): ?>
            <option value="<?= $o['id'] ?>" <?= $config['origem_destino_id']==$o['id']?'selected':'' ?>>
                <?= htmlspecialchars($o['nome']) ?>
            </option>
        <?php endforeach; ?>
    </select>
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
    top:0;left:0;right:0;bottom:0;
    background:rgba(0,0,0,0.5);
    justify-content:center;
    align-items:center;
">
    <div style="background:#fff;padding:20px;width:500px">
        <h4>Conferência de Pesagem</h4>

        <p><strong>Ticket:</strong> <span id="m_ticket"></span></p>
        <p><strong>Placa:</strong> <span id="m_placa"></span></p>
        <p><strong>Peso:</strong> <span id="m_peso"></span></p>

        <hr>

        <p><strong>Terno:</strong> <?= htmlspecialchars($config['terno']) ?></p>
        <p><strong>Porão:</strong> <?= htmlspecialchars($config['porao']) ?></p>
        <p><strong>Deck:</strong> <?= htmlspecialchars($config['deck']) ?></p>

        <button onclick="fecharModal()">Fechar</button>
    </div>
</div>

<script>
function salvarConfig() {
    fetch('/app/controllers/salvar_config_periodo.php', {
        method:'POST',
        headers:{'Content-Type':'application/json'},
        body:JSON.stringify({
            periodo_id:<?= $periodoId ?>,
            terno:terno.value,
            equipamento_id:equipamento.value,
            porao:porao.value,
            deck:deck.value,
            origem_destino_id:origem_destino.value
        })
    });
}
document.querySelectorAll('#configLancamentos select')
    .forEach(e=>e.onchange=salvarConfig);

btnCapturar.onclick=()=>{
    fetch('/app/controllers/captura_controller.php?periodo_id=<?= $periodoId ?>')
        .then(r=>r.text())
        .then(html=>resultadoCaptura.innerHTML=html);
};

function abrirModalPesagem(ticket, placa, peso){
    document.getElementById('m_ticket').innerText = ticket;
    document.getElementById('m_placa').innerText = placa;
    document.getElementById('m_peso').innerText = peso;
    document.getElementById('modalPesagem').style.display='flex';
}

function fecharModal(){
    document.getElementById('modalPesagem').style.display='none';
}
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
