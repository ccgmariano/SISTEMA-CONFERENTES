<?php
require_once __DIR__ . '/../database.php';
$db = Database::connect();

$op_id = $_POST['operacao_id'] ?? null;
$inicio = $_POST['inicio'] ?? null;
$fim = $_POST['fim'] ?? null;

if (!$op_id || !$inicio || !$fim) {
    die("Erro: dados inválidos.");
}

$stmt = $db->prepare("
    INSERT INTO periodos (operacao_id, inicio, fim)
    VALUES (?, ?, ?)
");

$stmt->execute([$op_id, $inicio, $fim]);

header("Location: /operacao_view.php?id=" . $op_id);
exit;
