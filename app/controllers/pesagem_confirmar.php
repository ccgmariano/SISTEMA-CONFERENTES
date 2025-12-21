<?php
require_once __DIR__ . '/../database.php';
$db = Database::connect();

$data = json_decode(file_get_contents('php://input'), true);

$stmt = $db->prepare("
    INSERT INTO pesagens_conferidas (
        periodo_id,
        ticket,
        placa,
        peso_liquido,
        terno,
        porao,
        deck,
        criado_em
    ) VALUES (
        :periodo_id,
        :ticket,
        :placa,
        :peso,
        :terno,
        :porao,
        :deck,
        datetime('now')
    )
");

$stmt->execute([
    ':periodo_id' => $data['periodo_id'],
    ':ticket'     => $data['ticket'],
    ':placa'      => $data['placa'],
    ':peso'       => $data['peso'],
    ':terno'      => $data['terno'],
    ':porao'      => $data['porao'],
    ':deck'       => $data['deck']
]);

echo json_encode(['ok' => true]);
