<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ======================================================
// 1) ID DO PERÍODO
// ======================================================
$periodoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($periodoId <= 0) {
    die('Período inválido.');
}

// ======================================================
// 2) BUSCAR PERÍODO + OPERAÇÃO (campos completos)
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
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die('Período não encontrado.');
}

// ======================================================
// 3) FUNÇÕES ESCALADAS (opcional, já estava no seu arquivo)
// ======================================================
$stmt = $db->prepare("
    SELECT
        pf.id AS periodo_funcao_id,
        f.nome AS funcao_nome
    FROM periodo_funcoes pf
    JOIN funcoes f ON f.id = pf.funcao_id
    WHERE pf.periodo_id = ?
    ORDER BY f.nome
");
$stmt->execute([$periodoId]);
$funcoesPeriodo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ======================================================
// 4) LISTAS PARA CONFIGURAÇÕES (tabelas EXISTENTES)
// ======================================================
$equipamentos = $db->query("SELECT id, nome FROM equipamentos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$decks        = $db->query("SELECT id, nome FROM decks ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$origens      = $db->query("SELECT id, nome FROM origem_destino ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// ======================================================
// 5) CARREGAR CONFIG ATUAL DO PERÍODO (se existir)
// ======================================================
$stmt = $db->prepare("SELECT * FROM periodo_config_lancamentos WHERE periodo_id = ? LIMIT 1");
$stmt->execute([$periodoId]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// defaults
$cfgTerno = $config['terno'] ?? '';
$cfgPorao = $config['porao'] ?? '';
$cfgDeckId = $config['deck_id'] ?? '';
$cfgEquipId = $config['equipamento_id'] ?? '';
$cfgOrigId = $config['origem_destino_id'] ?? '';

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

    <h2>Período</h2>

    <ul class="list-group mb-4">
        <li class="list-group-item"><strong>Data:</strong> <?= htmlspecialchars($periodo['data']) ?></li>
        <li class="list-group-item"><strong>Horário:</strong> <?= htmlspecialchars($periodo['inicio']) ?> → <?= htmlspecialchars($periodo['fim']) ?></li>
        <li class="list-group-item"><strong>Empresa:</strong> <?= htmlspecialchars($periodo['empresa']) ?></li>
        <li class="list-group-item"><strong>Navio:</strong> <?= htmlspecialchars($periodo['navio']) ?></li>
        <li class="list-group-item"><strong>Tipo:</strong> <?= htmlspecialchars($periodo['tipo_operacao']) ?></li>
        <li class="list-group-item"><strong>Produto:</strong> <?= htmlspecialchars($periodo['produto']) ?></li>
        <li class="list-group-item"><strong>Recinto:</strong> <?= htmlspecialchars($periodo['recinto']) ?></li>
    </ul>

    <hr>

    <!-- ======================================================
         CONFIGURAÇÕES DO LANÇAMENTO (NA MESMA PÁGINA)
         ====================================================== -->
    <h4>Configurações dos Lançamentos</h4>

    <form id="formConfigLanc" method="POST" action="/app/controllers/config_lancamentos_controller.php" class="mb-3">
        <input type="hidden" name="periodo_id" value="<?= (int)$periodoId ?>">

        <div style="display:flex; gap:12px; flex-wrap:wrap; align-items:flex-end;">
            <div>
                <label><strong>Terno</strong></label><br>
                <input type="number" name="terno" min="1" style="width:120px" value="<?= htmlspecialchars($cfgTerno) ?>">
            </div>

            <div>
                <label><strong>Equipamento</strong></label><br>
                <select name="equipamento_id" style="width:220px">
                    <option value="">-- selecione --</option>
                    <?php foreach ($equipamentos as $e): ?>
                        <option value="<?= (int)$e['id'] ?>" <?= ((string)$cfgEquipId === (string)$e['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($e['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label><strong>Porão</strong></label><br>
                <input type="number" name="porao" min="1" style="width:120px" value="<?= htmlspecialchars($cfgPorao) ?>">
            </div>

            <div>
                <label><strong>Deck</strong></label><br>
                <select name="deck_id" style="width:160px">
                    <option value="">-- selecione --</option>
                    <?php foreach ($decks as $d): ?>
                        <option value="<?= (int)$d['id'] ?>" <?= ((string)$cfgDeckId === (string)$d['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <label><strong>Origem/Destino</strong></label><br>
                <select name="origem_destino_id" style="width:220px">
                    <option value="">-- selecione --</option>
                    <?php foreach ($origens as $o): ?>
                        <option value="<?= (int)$o['id'] ?>" <?= ((string)$cfgOrigId === (string)$o['id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($o['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div>
                <button type="submit" class="btn btn-primary">Salvar Configuração</button>
            </div>
        </div>

        <small class="text-muted">
            Observação: por enquanto ainda existe “Salvar Configuração”. No próximo passo, vamos eliminar esse clique e aplicar automaticamente.
        </small>
    </form>

    <div id="msgConfigLanc"></div>

    <script>
    // salva config sem sair da página (não redireciona)
    document.getElementById('formConfigLanc').addEventListener('submit', function(e){
        e.preventDefault();
        const form = this;
        const msg = document.getElementById('msgConfigLanc');
        msg.innerHTML = "";

        fetch(form.action, { method: "POST", body: new FormData(form) })
            .then(r => r.text())
            .then(html => { msg.innerHTML = html; })
            .catch(() => { msg.innerHTML = "<div class='alert alert-danger'>Erro ao salvar configuração.</div>"; });
    });
    </script>

    <hr>

    <!-- ======================================================
         CAPTURA DE PESAGENS
         ====================================================== -->
    <h4>Captura de Pesagens</h4>

    <button id="btnCapturar" class="btn btn-success w-100 mb-3">
        Capturar Pesagens do Período
    </button>

    <div id="resultadoCaptura"></div>

    <script>
    (function(){
        const btn = document.getElementById('btnCapturar');
        const divResultado = document.getElementById('resultadoCaptura');

        if (!btn) return;

        btn.addEventListener('click', function () {
            btn.disabled = true;
            btn.innerText = "Consultando Poseidon...";

            fetch("/app/controllers/captura_controller.php?periodo_id=<?= (int)$periodoId ?>")
                .then(resp => resp.text())
                .then(html => { divResultado.innerHTML = html; })
                .catch(() => {
                    divResultado.innerHTML = "<div class='alert alert-danger'>Erro ao consultar pesagens.</div>";
                })
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = "Capturar Pesagens do Período";
                });
        });
    })();
    </script>

    <hr>

    <!-- ======================================================
         PESAGENS CONFERIDAS (do seu controller existente)
         ====================================================== -->
    <h4>Pesagens Conferidas</h4>

    <div id="pesagensConferidas">
        <?php
            $_GET['periodo_id'] = $periodoId;
            include $_SERVER['DOCUMENT_ROOT'] . '/app/controllers/pesagens_list.php';
        ?>
    </div>

    <hr>

    <!-- ======================================================
         FUNÇÕES E CONFERENTES
         ====================================================== -->
    <h3>Funções escaladas</h3>

    <?php if (empty($funcoesPeriodo)): ?>
        <p><em>Nenhuma função foi escalada para este período.</em></p>
    <?php else: ?>
        <?php foreach ($funcoesPeriodo as $f): ?>
            <fieldset style="margin-bottom:15px; padding:10px; border:1px solid #999;">
                <legend><strong><?= htmlspecialchars($f['funcao_nome']) ?></strong></legend>

                <?php
                $stmt = $db->prepare("
                    SELECT a.nome
                    FROM periodo_conferentes pc
                    JOIN associados a ON a.id = pc.associado_id
                    WHERE pc.periodo_funcao_id = ?
                    ORDER BY a.nome
                ");
                $stmt->execute([$f['periodo_funcao_id']]);
                $conferentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                ?>

                <?php if (empty($conferentes)): ?>
                    <p>Nenhum conferente atribuído.</p>
                <?php else: ?>
                    <ul>
                        <?php foreach ($conferentes as $c): ?>
                            <li><?= htmlspecialchars($c['nome']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </fieldset>
        <?php endforeach; ?>
    <?php endif; ?>

    <a href="/operacao_view.php?id=<?= (int)$periodo['operacao_id'] ?>" class="btn btn-secondary mt-4">
        Voltar à Operação
    </a>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
