<?php
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

require_once $_SERVER['DOCUMENT_ROOT'] . '/app/database.php';
$db = Database::connect();

$ids = $_POST['pesagem_ids'] ?? [];

if (!is_array($ids) || empty($ids)) {
    http_response_code(400);
    echo "Nenhuma pesagem selecionada.";
    exit;
}

// Sanitiza IDs
$ids = array_map('intval', $ids);

// Monta placeholders (?, ?, ?)
$placeholders = implode(',', array_fill(0, count($ids), '?'));

$stmt = $db->prepare("
    DELETE FROM pesagens
    WHERE id IN ($placeholders)
");

$stmt->execute($ids);

echo "OK";
