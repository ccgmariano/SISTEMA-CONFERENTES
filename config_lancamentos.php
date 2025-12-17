<?php
require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ============================
// VALIDAR PERIODO
// ============================
$periodoId = isset($_GET['periodo_id']) ? (int)$_GET['periodo_id'] : 0;
if ($periodoId <= 0) {
    die('Período inválido.');
}

// ============================
// BUSCAR CONFIG ATUAL (SE EXISTIR)
// ============================
$stmt = $db->prepare("SELECT * FROM config_lancamentos WHERE periodo_id = ?");
$stmt->execute([$periodoId]);
$config = $stmt->fetch(PDO::FETCH_ASSOC);

// ============================
// BUSCAR LISTAS (CADASTROS)
// ============================
$equipamentos = $db->query("SELECT id, nome FROM equipamentos WHERE ativo = 1 ORDER BY nome")->fetchAll();
$origens = $db->query("SELECT id, nome FROM origem_destino ORDER BY nome")->fetchAll();

require_once __DIR__ . '/app/views/header.php';
?>

<div class="container mt-4">
    <h3>Configurações de Lançamento do Período</h3>

    <form method="POST" action="/app/controllers/config_lancamentos_salvar.php">

        <input type="hidden" name="periodo_id" value="<?= $periodoId ?>">

        <div class="mb-3">
            <label>Porão padrão</label>
            <input type="number" name="porao" class="form-control"
                   value="<?= $config['porao'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label>Deck padrão</label>
            <input type="text" name="deck" class="form-control"
                   value="<?= $config['deck'] ?? '' ?>">
        </div>

        <div class="mb-3">
            <label>Equipamento padrão</label>
            <select name="equipamento_id" class="form-control">
                <option value="">Selecione</option>
                <?php foreach ($equipamentos as $e): ?>
                    <option value="<?= $e['id'] ?>"
                        <?= (isset($config['equipamento_id']) && $config['equipamento_id'] == $e['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($e['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label>Origem/Destino padrão</label>
            <select name="origem_destino_id" class="form-control">
                <option value="">Selecione</option>
                <?php foreach ($origens as $o): ?>
                    <option value="<?= $o['id'] ?>"
                        <?= (isset($config['origem_destino_id']) && $config['origem_destino_id'] == $o['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($o['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <a href="/periodo_view.php?id=<?= $periodoId ?>" class="btn btn-secondary">
            Voltar
        </a>

        <button type="submit" class="btn btn-primary">
            Salvar Configurações
        </button>

    </form>
</div>

<?php require_once __DIR__ . '/app/views/footer.php'; ?>
