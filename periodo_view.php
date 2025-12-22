<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ======================================================
// 1. RECEBE ID DO PERÍODO
// ======================================================
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id <= 0) {
    die('Período inválido.');
}

// ======================================================
// 2. BUSCA PERÍODO + OPERAÇÃO
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
// 3. DADOS PARA CONFIGURAÇÃO DE LANÇAMENTOS
// ======================================================
$equipamentos = $db->query("SELECT id, nome FROM equipamentos WHERE ativo = 1 ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$origens = $db->query("SELECT id, nome FROM origem_destino ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

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

    <!-- CONFIGURAÇÕES DE LANÇAMENTO -->
    <h4>Configurações de Lançamento</h4>

    <div class="row mb-3">
        <div class="col">
            <label>Terno</label>
            <input type="number" id="cfg_terno" class="form-control" value="1">
        </div>

        <div class="col">
            <label>Porão</label>
            <input type="number" id="cfg_porao" class="form-control">
        </div>

        <div class="col">
            <label>Deck</label>
            <input type="text" id="cfg_deck" class="form-control">
        </div>

        <div class="col">
            <label>Equipamento</label>
            <select id="cfg_equipamento" class="form-control">
                <option value="">-- selecione --</option>
                <?php foreach ($equipamentos as $e): ?>
                    <option value="<?= $e['nome'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col">
            <label>Origem / Destino</label>
            <select id="cfg_origem" class="form-control">
                <option value="">-- selecione --</option>
                <?php foreach ($origens as $o): ?>
                    <option value="<?= $o['nome'] ?>"><?= htmlspecialchars($o['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <small class="text-muted">
        Essas configurações serão usadas automaticamente ao clicar na lupa de cada pesagem.
    </small>

    <hr>

    <!-- CAPTURA DE PESAGENS -->
    <h4>Captura de Pesagens</h4>

    <button id="btnCapturar" class="btn btn-success w-100 mb-3">
        Capturar Pesagens do Período
    </button>

    <div id="resultadoCaptura"></div>

    <script>
    document.getElementById('btnCapturar').addEventListener('click', function () {
        const btn = this;
        const divResultado = document.getElementById('resultadoCaptura');

        btn.disabled = true;
        btn.innerText = "Consultando Poseidon...";

        fetch("/app/controllers/captura_controller.php?periodo_id=<?= (int)$id ?>")
            .then(resp => resp.text())
            .then(html => {
                divResultado.innerHTML = html;
            })
            .catch(() => {
                divResultado.innerHTML =
                    "<div class='alert alert-danger'>Erro ao consultar pesagens.</div>";
            })
            .finally(() => {
                btn.disabled = false;
                btn.innerText = "Capturar Pesagens do Período";
            });
    });
    </script>

</div>

<!-- MODAL DE CONFERÊNCIA -->
<div id="modalPesagem" style="
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.5);
    z-index:9999;
">
    <div style="
        background:#fff;
        width:420px;
        margin:8% auto;
        padding:20px;
        border-radius:6px;
    ">
        <h4>Conferência de Pesagem</h4>
        <div id="conteudoModalPesagem"></div>

        <div style="text-align:right; margin-top:15px;">
            <button onclick="fecharModalPesagem()" class="btn btn-secondary">
                Fechar
            </button>
        </div>
    </div>
</div>

<script>
function abrirModalPesagem(dados) {

    const cfg = {
        terno: document.getElementById('cfg_terno').value,
        porao: document.getElementById('cfg_porao').value,
        deck: document.getElementById('cfg_deck').value,
        equipamento: document.getElementById('cfg_equipamento').value,
        origem: document.getElementById('cfg_origem').value
    };

    let html = `
        <p><strong>Ticket:</strong> ${dados.ticket}</p>
        <p><strong>Placa:</strong> ${dados.placa}</p>
        <p><strong>Peso Líquido:</strong> ${dados.peso}</p>
        <hr>
        <p><strong>Terno:</strong> ${cfg.terno}</p>
        <p><strong>Porão:</strong> ${cfg.porao}</p>
        <p><strong>Deck:</strong> ${cfg.deck}</p>
        <p><strong>Equipamento:</strong> ${cfg.equipamento}</p>
        <p><strong>Origem/Destino:</strong> ${cfg.origem}</p>
    `;

    document.getElementById('conteudoModalPesagem').innerHTML = html;
    document.getElementById('modalPesagem').style.display = 'block';
}

function fecharModalPesagem() {
    document.getElementById('modalPesagem').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
