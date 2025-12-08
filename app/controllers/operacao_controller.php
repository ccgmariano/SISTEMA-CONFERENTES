<?php
require_once __DIR__ . '/../database.php';
$db = Database::connect();

$empresa      = $_POST['empresa'] ?? null;
$navio        = $_POST['navio'] ?? null;
$produto      = $_POST['produto'] ?? null;
$recinto      = $_POST['recinto'] ?? null;
$tipoOperacao = $_POST['tipo_operacao'] ?? null;

if (!$empresa || !$navio || !$produto || !$recinto || !$tipoOperacao) {
    die("Erro: todos os campos são obrigatórios.");
}

$stmt = $db->prepare("
    INSERT INTO operacoes (empresa, navio, produto, recinto, tipo_operacao, criado_em)
    VALUES (?, ?, ?, ?, ?, datetime('now'))
");

$stmt->execute([$empresa, $navio, $produto, $recinto, $tipoOperacao]);

header("Location: /dashboard.php");
exit;
