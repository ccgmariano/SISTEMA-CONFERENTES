<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

$periodoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($periodoId <= 0) {
    die('Período inválido.');
}

// Período
$stmt = $db->prepare("SELECT * FROM periodos WHERE id = ?");
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die('Período não encontrado.');
}

$operacaoId = (int)$periodo['operacao_id'];

// Funções e associados (sem filtro ativo)
$funcoes = $db->query("SELECT id, nome FROM funcoes ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);
$associados = $db->query("SELECT id, nome FROM associados ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Funções selecionadas no período
$stmt = $db->prepare("SELECT id, funcao_id FROM periodo_funcoes WHERE periodo_id = ?");
$stmt->execute([$periodoId]);
$pfRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$funcoesSelecionadas = [];          // [funcao_id => true]
$periodoFuncaoPorFuncao = [];       // [funcao_id => periodo_funcao_id]
foreach ($pfRows as $r) {
    $funcoesSelecionadas[(int)$r['funcao_id']] = true;
    $periodoFuncaoPorFuncao[(int)$r['funcao_id']] = (int)$r['id'];
}

// Conferentes selecionados por função
$conferentesSelecionados = []; // [funcao_id => [associado_id => true]]
if (!empty($periodoFuncaoPorFuncao)) {
    foreach ($periodoFuncaoPorFuncao as $funcaoId => $periodoFuncaoId) {
        $stmt = $db->prepare("SELECT associado_id FROM periodo_conferentes WHERE periodo_funcao_id = ?");
        $stmt->execute([$periodoFuncaoId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $rr) {
            $conferentesSelecionados[$funcaoId][(int)$rr['associado_id']] = true;
        }
    }
}

// Detectar período_escolhido atual pelo horário (fallback)
$inicioHora = substr($periodo['inicio'], 11, 5);
$fimHora    = substr($periodo['fim'], 11, 5);
$periodoEscolhidoAtual = $inicioHora . '|' . $fimHora;

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container">

    <h2>Editar Período</h2>

    <form method="POST" action="/app/controllers/periodo_update_controller.php">

        <input type="hidden" name="periodo_id" value="<?= (int)$periodoId ?>">
        <input type="hidden" name="operacao_id" value="<?= (int)$operacaoId ?>">

        <p>
            <label><strong>Data:</strong></label><br>
            <input type="date" name="data" value="<?= htmlspecialchars($periodo['data']) ?>" required>
        </p>

        <p>
            <label><strong>Período:</strong></label><br>
            <select name="periodo_escolhido" required>
                <option value="07:00|12:59" <?= ($periodoEscolhidoAtual === '07:00|12:59') ? 'selected' : '' ?>>Período 1 — 07:00 às 12:59</option>
                <option value="13:00|18:59" <?= ($periodoEscolhidoAtual === '13:00|18:59') ? 'selected' : '' ?>>Período 2 — 13:00 às 18:59</option>
                <option value="19:00|00:59" <?= ($periodoEscolhidoAtual === '19:00|00:59') ? 'selected' : '' ?>>Período 3 — 19:00 às 00:59</option>
                <option value="01:00|06:59" <?= ($periodoEscolhidoAtual === '01:00|06:59') ? 'selected' : '' ?>>Período 4 — 01:00 às 06:59</option>
            </select>
        </p>

        <hr>

        <h3>Funções e Conferentes</h3>

        <?php foreach ($funcoes as $funcao): ?>
            <?php
                $fid = (int)$funcao['id'];
                $checked = isset($funcoesSelecionadas[$fid]) ? 'checked' : '';
            ?>
            <fieldset style="margin-bottom:15px; padding:10px; border:1px solid #999;">
                <legend>
                    <label>
                        <input type="checkbox" name="funcoes[]" value="<?= $fid ?>" <?= $checked ?>>
                        <strong><?= htmlspecialchars($funcao['nome']) ?></strong>
                    </label>
                </legend>

                <p>Conferentes desta função:</p>

                <select name="conferentes[<?= $fid ?>][]" multiple size="5">
                    <?php foreach ($associados as $a): ?>
                        <?php
                            $aid = (int)$a['id'];
                            $sel = (isset($conferentesSelecionados[$fid]) && isset($conferentesSelecionados[$fid][$aid])) ? 'selected' : '';
                        ?>
                        <option value="<?= $aid ?>" <?= $sel ?>>
                            <?= htmlspecialchars($a['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </fieldset>
        <?php endforeach; ?>

        <p>
            <button type="submit">Salvar alterações</button>
            &nbsp;|&nbsp;
            <a href="/periodo_view.php?id=<?= (int)$periodoId ?>">Cancelar</a>
        </p>

    </form>

    <p>
        <a href="/operacao_view.php?id=<?= (int)$operacaoId ?>">Voltar à Operação</a>
    </p>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
