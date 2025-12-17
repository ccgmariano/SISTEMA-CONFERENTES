<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

$periodoId = isset($_POST['periodo_id']) ? (int)$_POST['periodo_id'] : 0;
if ($periodoId <= 0) {
    die('Período inválido.');
}

$porao = trim((string)($_POST['porao'] ?? ''));
$deck  = trim((string)($_POST['deck'] ?? ''));

$equipamentoId = trim((string)($_POST['equipamento_id'] ?? ''));
$origemId      = trim((string)($_POST['origem_destino_id'] ?? ''));

$poraoVal = ($porao === '') ? null : (int)$porao;
$deckVal  = ($deck === '') ? null : $deck;

$equipVal = ($equipamentoId === '') ? null : (int)$equipamentoId;
$origVal  = ($origemId === '') ? null : (int)$origemId;

// UPSERT (1 config por período)
$stmt = $db->prepare("
    INSERT INTO config_lancamentos (
        periodo_id, porao, deck, equipamento_id, origem_destino_id
    ) VALUES (
        :periodo_id, :porao, :deck, :equip, :orig
    )
    ON CONFLICT(periodo_id) DO UPDATE SET
        porao = excluded.porao,
        deck = excluded.deck,
        equipamento_id = excluded.equipamento_id,
        origem_destino_id = excluded.origem_destino_id
");
$stmt->execute([
    ':periodo_id' => $periodoId,
    ':porao'      => $poraoVal,
    ':deck'       => $deckVal,
    ':equip'      => $equipVal,
    ':orig'       => $origVal
]);

header('Location: /config_lancamentos.php?periodo_id=' . urlencode($periodoId) . '&ok=1');
exit;
