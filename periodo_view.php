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
        o.id   AS operacao_id,
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
// 3. LISTAS PARA CONFIGURAÇÕES DE LANÇAMENTO
// ======================================================
$equipamentos = $db->query("
    SELECT id, nome 
    FROM equipamentos 
    WHERE ativo = 1 
    ORDER BY nome
")->fetchAll();

$origens = $db->query("
    SELECT id, nome 
    FROM origem_destino 
    ORDER BY nome
")->fetchAll();

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

    <!-- CONFIGURAÇÕES DE LANÇAMENTO (ESTADO DE TELA) -->
    <h4>Configurações de Lançamento</h4>

    <div class="row mb-3">
        <div class="col">
            <label>Terno</label>
            <select id="cfg_terno" class="form-control">
                <option value="">-- Nº Terno --</option>
                <?php for ($i = 1; $i <= 10; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col">
            <label>Equipamento</label>
            <select id="cfg_equipamento" class="form-control">
                <option value="">-- Equipamento --</option>
                <?php foreach ($equipamentos as $e): ?>
                    <option value="<?= $e['id'] ?>"><?= htmlspecialchars($e['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col">
            <label>Porão</label>
            <select id="cfg_porao" class="form-control">
                <option value="">-- Porão --</option>
                <?php for ($i = 1; $i <= 9; $i++): ?>
                    <option value="<?= $i ?>"><?= $i ?></option>
                <?php endfor; ?>
            </select>
        </div>

        <div class="col">
            <label>Deck</label>
            <select id="cfg_deck" class="form-control">
                <option value="">-- Deck --</option>
                <option value="LH">LH</option>
                <option value="RH">RH</option>
            </select>
        </div>

        <div class="col">
            <label>Origem / Destino</label>
            <select id="cfg_origem" class="form-control">
                <option value="">-- Origem/Destino --</option>
                <?php foreach ($origens as $o): ?>
                    <option value="<?= $o['id'] ?>"><?= htmlspecialchars($o['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <hr>

    <!-- CAPTURA DE PESAGENS -->
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

    <hr>

    <!-- PESAGENS CONFERIDAS -->
    <h4>Pesagens Conferidas</h4>

    <div id="pesagensConferidas">
        <?php
            $_GET['periodo_id'] = $id;
            include $_SERVER['DOCUMENT_ROOT'] . '/app/controllers/pesagens_list.php';
        ?>
    </div>

    <a href="/operacao_view.php?id=<?= (int)$periodo['operacao_id'] ?>"
       class="btn btn-secondary mt-4">
        Voltar à Operação
    </a>

</div>

<!-- ====================================================== -->
<!-- MODAL DA LUPA (BASE – SEM LÓGICA AINDA) -->
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
        width:600px;
        margin:60px auto;
        padding:20px;
        position:relative;
    ">

        <button onclick="fecharModal()"
                style="position:absolute; top:10px; right:10px;">
            ✖
        </button>

        <h3>Conferência da Pesagem</h3>

        <div id="conteudoModal">
            <!-- conteúdo virá no próximo passo -->
            <p><em>Modal aberto com sucesso.</em></p>
            <p>Ticket selecionado: <strong id="ticketSelecionado"></strong></p>
        </div>

    </div>
</div>

<script>
function abrirModalPesagem(ticket) {
    document.getElementById('ticketSelecionado').innerText = ticket;
    document.getElementById('modalPesagem').style.display = 'block';
}

function fecharModal() {
    document.getElementById('modalPesagem').style.display = 'none';
}
</script>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
