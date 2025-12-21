<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ======================================================
// 1. ID DO PERÍODO
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
// 3. CONFIGURAÇÃO DE LANÇAMENTO DO PERÍODO (SE EXISTIR)
// ======================================================
$stmt = $db->prepare("
    SELECT * 
    FROM periodo_config_lancamentos
    WHERE periodo_id = ?
    LIMIT 1
");
$stmt->execute([$id]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// ======================================================
// 4. LISTAS DE APOIO
// ======================================================
$equipamentos = $db->query("
    SELECT id, nome 
    FROM equipamentos 
    WHERE ativo = 1 
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

$origens = $db->query("
    SELECT id, nome 
    FROM origem_destino 
    ORDER BY nome
")->fetchAll(PDO::FETCH_ASSOC);

// ======================================================
// 5. FUNÇÕES DO PERÍODO
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
$stmt->execute([$id]);
$funcoesPeriodo = $stmt->fetchAll(PDO::FETCH_ASSOC);

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

    <!-- CONFIGURAÇÕES DE LANÇAMENTO -->
    <h4>Configurações de Lançamento do Período</h4>

    <form method="POST" action="/app/controllers/config_lancamentos_salvar.php" class="row g-3 mb-4">

        <input type="hidden" name="periodo_id" value="<?= (int)$id ?>">

        <div class="col-md-2">
            <label class="form-label">Terno</label>
            <input type="number"
                   name="terno"
                   class="form-control"
                   value="<?= htmlspecialchars($config['terno'] ?? 1) ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Equipamento</label>
            <select name="equipamento_id" class="form-select">
                <option value="">-- selecionar --</option>
                <?php foreach ($equipamentos as $e): ?>
                    <option value="<?= $e['id'] ?>"
                        <?= (!empty($config['equipamento_id']) && $config['equipamento_id'] == $e['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-md-2">
            <label class="form-label">Porão</label>
            <input type="number"
                   name="porao"
                   class="form-control"
                   value="<?= htmlspecialchars($config['porao'] ?? '') ?>">
        </div>

        <div class="col-md-2">
            <label class="form-label">Deck</label>
            <input type="text"
                   name="deck"
                   class="form-control"
                   value="<?= htmlspecialchars($config['deck'] ?? '') ?>">
        </div>

        <div class="col-md-3">
            <label class="form-label">Origem / Destino</label>
            <select name="origem_destino_id" class="form-select">
                <option value="">-- selecionar --</option>
                <?php foreach ($origens as $o): ?>
                    <option value="<?= $o['id'] ?>"
                        <?= (!empty($config['origem_destino_id']) && $config['origem_destino_id'] == $o['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($o['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="col-12">
            <button class="btn btn-primary">
                Salvar Configurações do Período
            </button>
        </div>
    </form>

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
                .then(html => divResultado.innerHTML = html)
                .finally(() => {
                    btn.disabled = false;
                    btn.innerText = "Capturar Pesagens do Período";
                });
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

    <hr>

    <!-- FUNÇÕES E CONFERENTES -->
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

    <a href="/operacao_view.php?id=<?= (int)$periodo['operacao_id'] ?>"
       class="btn btn-secondary mt-4">
        Voltar à Operação
    </a>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
