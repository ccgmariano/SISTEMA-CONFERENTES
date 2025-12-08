<?php
require_once __DIR__ . '/../database.php';

$db = Database::connect();

$operacaoId = isset($_POST['operacao_id']) ? (int)$_POST['operacao_id'] : 0;
$inicio     = $_POST['inicio'] ?? null;
$fim        = $_POST['fim'] ?? null;

if ($operacaoId <= 0 || !$inicio || !$fim) {
    die('Erro: dados de período inválidos.');
}

// (Opcional) evitar duplicar exatamente o mesmo período para a mesma operação
$stmt = $db->prepare('
    SELECT COUNT(*) AS total 
    FROM periodos 
    WHERE operacao_id = ? AND inicio = ? AND fim = ?
');
$stmt->execute([$operacaoId, $inicio, $fim]);
if ($stmt->fetchColumn() > 0) {
    // Já existe esse período, só volta pra tela
    header('Location: /operacao_view.php?id=' . urlencode($operacaoId));
    exit;
}

// Insere o período
$stmt = $db->prepare('
    INSERT INTO periodos (operacao_id, inicio, fim, criado_em)
    VALUES (?, ?, ?, datetime("now"))
');
$stmt->execute([$operacaoId, $inicio, $fim]);

// Volta para a tela da operação
header('Location: /operacao_view.php?id=' . urlencode($operacaoId));
exit;
