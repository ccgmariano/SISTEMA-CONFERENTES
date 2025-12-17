<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ============================
// FUNÇÕES AUXILIARES
// ============================
function tableHasColumn(PDO $db, string $table, string $column): bool {
    try {
        $stmt = $db->query("PRAGMA table_info($table)");
        $cols = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) {
            if (($c['name'] ?? '') === $column) return true;
        }
    } catch (Throwable $e) {}
    return false;
}

// ============================
// VALIDAR PERIODO
// ============================
$periodoId = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;
if ($periodoId <= 0) {
    die('Período inválido.');
}

// ============================
// BUSCAR PERÍODO + OPERAÇÃO + NAVIO (NOME)
// ============================
$stmt = $db->prepare("
    SELECT 
        p.id as periodo_id,
        p.operacao_id,
        o.navio
    FROM periodos p
    JOIN operacoes o ON o.id = p.operacao_id
    WHERE p.id = ?
");
$stmt->execute([$periodoId]);
$ctx = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$ctx) {
    die('Período não encontrado.');
}

$navioNome = trim($ctx['navio'] ?? '');

// ============================
// BUSCAR NAVIO (para montar Porão e Deck)
// ============================
$numPoroes = null;
$decksList = [];

if ($navioNome !== '') {
    // tenta achar navio pelo nome (como está hoje na operação)
    $stmt = $db->prepare("SELECT num_poroes, decks FROM navios WHERE nome = ? LIMIT 1");
    $stmt->execute([$navioNome]);
    $nav = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($nav) {
        if (isset($nav['num_poroes']) && $nav['num_poroes'] !== '') {
            $numPoroes = (int)$nav['num_poroes'];
            if ($numPoroes <= 0) $numPoroes = null;
        }
        $decksStr = trim((string)($nav['decks'] ?? ''));
        if ($decksStr !== '') {
            $parts = array_map('trim', explode(',', $decksStr));
            $decksList = array_values(array_filter($parts, fn($x) => $x !== ''));
        }
    }
}

// ============================
// BUSCAR CONFIG ATUAL (SE EXISTIR)
// ============================
$stmt = $db->prepare("SELECT * FROM config_lancamentos WHERE periodo_id = ?");
$stmt->execute([$periodoId]);
$config = $stmt->fetch(PDO::FETCH_ASSOC) ?: [];

// ============================
// BUSCAR LISTAS (CADASTROS) - SEM DEPENDER DE "ativo"
// ============================
$equipSql = "SELECT id, nome FROM equipamentos";
if (tableHasColumn($db, 'equipamentos', 'ativo')) {
    $equipSql .= " WHERE ativo = 1";
}
$equipSql .= " ORDER BY nome";
$equipamentos = $db->query($equipSql)->fetchAll(PDO::FETCH_ASSOC);

$odSql = "SELECT id, nome FROM origem_destino";
if (tableHasColumn($db, 'origem_destino', 'ativo')) {
    $odSql .= " WHERE ativo = 1";
}
$odSql .= " ORDER BY nome";
$origens = $db->query($odSql)->fetchAll(PDO::FETCH_ASSOC);

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">

    <h3>Configurações de Lançamento do Período</h3>

    <?php if (isset($_GET['ok']) && $_GET['ok'] == '1'): ?>
        <div class="alert alert-success">Configurações salvas.</div>
    <?php endif; ?>

    <form method="POST" action="/app/controllers/config_lancamentos_salvar.php">
        <input type="hidden" name="periodo_id" value="<?= (int)$periodoId ?>">

        <hr>

        <h5>Padrões do Período</h5>

        <!-- PORÃO -->
        <div class="mb-3">
            <label class="form-label"><strong>Porão padrão</strong></label>

            <?php if ($numPoroes !== null): ?>
                <select name="porao" class="form-control">
                    <option value="">Selecione</option>
                    <?php for ($i = 1; $i <= $numPoroes; $i++): ?>
                        <option value="<?= $i ?>"
                            <?= (isset($config['porao']) && (int)$config['porao'] === $i) ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor; ?>
                </select>
            <?php else: ?>
                <input type="number" name="porao" class="form-control"
                       value="<?= htmlspecialchars($config['porao'] ?? '') ?>"
                       placeholder="Ex: 1, 2, 3...">
                <small class="text-muted">Obs.: não consegui ler o nº de porões do navio automaticamente.</small>
            <?php endif; ?>
        </div>

        <!-- DECK -->
        <div class="mb-3">
            <label class="form-label"><strong>Deck padrão</strong></label>

            <?php if (!empty($decksList)): ?>
                <select name="deck" class="form-control">
                    <option value="">Selecione</option>
                    <?php foreach ($decksList as $d): ?>
                        <option value="<?= htmlspecialchars($d) ?>"
                            <?= (isset($config['deck']) && (string)$config['deck'] === $d) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($d) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            <?php else: ?>
                <input type="text" name="deck" class="form-control"
                       value="<?= htmlspecialchars($config['deck'] ?? '') ?>"
                       placeholder="Ex: A, B, C...">
                <small class="text-muted">Obs.: não consegui ler a lista de decks do navio automaticamente.</small>
            <?php endif; ?>
        </div>

        <!-- EQUIPAMENTO -->
        <div class="mb-3">
            <label class="form-label"><strong>Equipamento padrão</strong></label>
            <select name="equipamento_id" class="form-control">
                <option value="">Selecione</option>
                <?php foreach ($equipamentos as $e): ?>
                    <option value="<?= (int)$e['id'] ?>"
                        <?= (isset($config['equipamento_id']) && (int)$config['equipamento_id'] === (int)$e['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <!-- ORIGEM/DESTINO -->
        <div class="mb-3">
            <label class="form-label"><strong>Origem/Destino padrão</strong></label>
            <select name="origem_destino_id" class="form-control">
                <option value="">Selecione</option>
                <?php foreach ($origens as $o): ?>
                    <option value="<?= (int)$o['id'] ?>"
                        <?= (isset($config['origem_destino_id']) && (int)$config['origem_destino_id'] === (int)$o['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($o['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <a href="/periodo_view.php?id=<?= (int)$periodoId ?>" class="btn btn-secondary">
            Voltar
        </a>

        <button type="submit" class="btn btn-primary">
            Salvar Configurações
        </button>

    </form>

</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
