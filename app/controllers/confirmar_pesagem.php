<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();
require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';

/* ===== PASSO 1 — LOG DE CHEGADA (TEMPORÁRIO) ===== */
file_put_contents(
    $_SERVER['DOCUMENT_ROOT'] . '/tmp/debug_confirmar.log',
    date('Y-m-d H:i:s') . " CHEGOU\n",
    FILE_APPEND
);
/* =============================================== */

$db = Database::connect();

$data = json_decode(file_get_contents('php://input'), true);

/* ===== PASSO 2 — LOG DO PAYLOAD (TEMPORÁRIO) ===== */
file_put_contents(
    $_SERVER['DOCUMENT_ROOT'] . '/tmp/debug_payload.log',
    print_r($data, true),
    FILE_APPEND
);
/* =============================================== */

if (!$data) exit;

$periodoId = (int)$data['periodo_id'];
$ticket    = $data['ticket'];

$stmt = $db->prepare("
    SELECT 1 FROM pesagens 
    WHERE periodo_id = ? AND ticket = ?
");
$stmt->execute([$periodoId, $ticket]);
if ($stmt->fetch()) exit;

$stmt = $db->prepare("
    INSERT INTO pesagens (
        periodo_id,
        ticket,
        placa,
        peso_liquido,
        terno,
        porao,
        deck,
        equipamento,
        origem_destino,
        criado_em
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'))
");

$stmt->execute([
    $periodoId,
    $ticket,
    $data['placa'],
    $data['peso'],
    $data['terno'],
    $data['porao'],
    $data['deck'],
    $data['equip'],
    $data['orig']
]);
