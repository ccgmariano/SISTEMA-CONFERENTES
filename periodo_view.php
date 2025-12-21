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

// ======================================================
// BUSCA CONFIGURAÇÃO DE LANÇAMENTO DO PERÍODO
// ======================================================
$stmt = $db->prepare("SELECT * FROM periodo_config_lancamentos WHERE periodo_id = ?");
$stmt->execute([$id]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// ======================================================
// LISTAS
// ======================================================
$equipamentos = $db->query("SELECT id, nome FROM equipamentos WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$origens = $db->query("SELECT id, nome FROM origem_destino ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

<h2>Período</h2>

<ul class="list-group mb-4">
    <li class="list-group-item"><strong>Data:</strong> <?= htmlspecialchars($periodo['data']) ?></li>
    <li class="list-group-item"><strong>Horário:</strong> <?= $periodo['inicio'] ?> → <?= $periodo['fim'] ?></li>
    <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($periodo['empresa']) ?></li>
    <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($periodo['navio']) ?></li>
    <li class="list-group-item"><strong>Tipo:</strong> <?= htmlspecialchars($periodo['tipo_operacao']) ?></li>
    <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($periodo['produto']) ?></li>
    <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($periodo['recinto']) ?></li>
</ul>

<hr>

<!-- ====================================================== -->
<!-- CONFIGURAÇÕES DE LANÇAMENTO -->
<!-- ====================================================== -->
<h4>Configurações de Lançamento</h4>

<form id="formConfigLancamento">

<input type="hidden" name="periodo_id" value="<?= $id ?>">

<div style="display:flex; gap:10px; flex-wrap:wrap">

<input type="number" name="terno" placeholder="Terno"
       value="<?= $config['terno'] ?? '' ?>">

<input type="number" name="porao" placeholder="Porão"
       value="<?= $config['porao'] ?? '' ?>">

<input type="text" name="deck" placeholder="Deck"
       value="<?= $config['deck'] ?? '' ?>">

<select name="equipamento_id">
    <option value="">-- Equipamento --</option>
    <?php foreach ($equipamentos as $e): ?>
        <option value="<?= $e['id'] ?>"
            <?= ($config['equipamento_id'] ?? '') == $e['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($e['nome']) ?>
        </option>
    <?php endforeach; ?>
</select>

<select name="origem_destino_id">
    <option value="">-- Origem / Destino --</option>
    <?php foreach ($origens as $o): ?>
        <option value="<?= $o['id'] ?>"
            <?= ($config['origem_destino_id'] ?? '') == $o['id'] ? 'selected' : '' ?>>
            <?= htmlspecialchars($o['nome']) ?>
        </option>
    <?php endforeach; ?>
</select>

</div>

<br>

<button type="button" onclick="salvarConfigLancamento()" class="btn btn-primary">
    Salvar Configurações
</button>

<span id="statusConfig" style="margin-left:10px;"></span>

</form>

<script>
function salvarConfigLancamento() {
    const form = document.getElementById('formConfigLancamento');
    const data = new FormData(form);

    fetch('/app/controllers/salvar_config_lancamento.php', {
        method: 'POST',
        body: data
    })
    .then(r => r.json())
    .then(j => {
        document.getElementById('statusConfig').innerText = j.ok ? '✔ Salvo' : 'Erro';
    });
}
</script>

<hr>

<!-- ====================================================== -->
<!-- CAPTURA -->
<!-- ====================================================== -->
<h4>Captura de Pesagens</h4>

<button id="btnCapturar" class="btn btn-success mb-3">
    Capturar Pesagens do Período
</button>

<div id="resultadoCaptura"></div>

<script>
document.getElementById('btnCapturar').onclick = () => {
    fetch("/app/controllers/captura_controller.php?periodo_id=<?= $id ?>")
        .then(r => r.text())
        .then(html => document.getElementById('resultadoCaptura').innerHTML = html);
};
</script>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
