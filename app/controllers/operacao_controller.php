<?php
require_once __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../app/database.php';
require_login();

$empresa       = $_POST['empresa']       ?? null;
$navio         = $_POST['navio']         ?? null;
$produto       = $_POST['produto']       ?? null;
$recinto       = $_POST['recinto']       ?? null;
$tipoOperacao  = $_POST['tipo_operacao'] ?? null;

if (!$empresa || !$navio || !$produto || !$recinto || !$tipoOperacao) {
    die("Erro: todos os campos são obrigatórios.");
}

$db = Database::connect();

$stmt = $db->prepare("
    INSERT INTO operacoes (empresa, navio, produto, recinto, tipo, criado_em)
    VALUES (?, ?, ?, ?, ?, ?)
");

$stmt->execute([
    $empresa,
    $navio,
    $produto,
    $recinto,
    $tipoOperacao,
    date('Y-m-d H:i:s')
]);

header("Location: /dashboard.php");
exit;
