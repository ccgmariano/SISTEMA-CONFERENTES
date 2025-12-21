<?php
require_once __DIR__ . '/../database.php';

$db = Database::connect();
$data = json_decode(file_get_contents('php://input'), true);

// --------------------------------------------------
// VALIDAÇÃO BÁSICA
// --------------------------------------------------
if (
    empty($data['periodo_id']) ||
    empty($data['ticket'])
) {
    echo json_encode(['ok' => false]);
    exit;
}

// --------------------------------------------------
// EVITAR DUPLICAÇÃO
// --------------------------------------------------
$stmt = $db->prepare("
    SELECT id FROM pesagens
    WHERE periodo_id = ? AND ticket = ?
");
$stmt->execute([$data['periodo_id'], $data['ticket']]);

if ($stmt->fetch()) {
    echo json_encode(['ok' => true]);
    exit;
}

// --------------------------------------------------
// INSERT
// --------------------------------------------------
$stmt = $db->prepare("
    INSERT INTO pesagens (
        periodo_id,
        ticket,
        placa,
        data_hora,
        peso_liquido,
        terno,
        equipamento,
        porao,
        deck,
        origem_destino,
        criado_em
    ) VALUES (
        :periodo_id,
        :ticket,
        :placa,
        :data_hora,
        :peso,
        :terno,
        :equipamento,
        :porao,
        :deck,
        :origem,
        datetime('now')
    )
");

$stmt->execute([
    ':periodo_id' => $data['periodo_id'],
    ':ticket'     => $data['ticket'],
    ':placa'      => $data['placa'] ?? null,
    ':data_hora'  => $data['data_hora'] ?? null,
    ':peso'       => $data['peso'] ?? null,
    ':terno'      => $data['terno'] ?? null,
    ':equipamento'=> $data['equipamento'] ?? null,
    ':porao'      => $data['porao'] ?? null,
    ':deck'       => $data['deck'] ?? null,
    ':origem'     => $data['origem_destino'] ?? null
]);

echo json_encode(['ok' => true]);
