<?php
require_once __DIR__ . '/../database.php';
$db = Database::connect();

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $db->prepare("
    UPDATE periodo_config_lancamentos
    SET
        terno = :terno,
        equipamento_id = :equipamento_id,
        porao = :porao,
        deck = :deck,
        origem_destino_id = :origem_destino_id,
        atualizado_em = CURRENT_TIMESTAMP
    WHERE periodo_id = :periodo_id
");

$stmt->execute([
    ':terno' => $data['terno'] ?: null,
    ':equipamento_id' => $data['equipamento_id'] ?: null,
    ':porao' => $data['porao'] ?: null,
    ':deck' => $data['deck'] ?: null,
    ':origem_destino_id' => $data['origem_destino_id'] ?: null,
    ':periodo_id' => $data['periodo_id']
]);

echo 'OK';
