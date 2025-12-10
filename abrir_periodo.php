<?php
session_start();

require_once __DIR__ . '/config.php';
require_login();

require_once __DIR__ . '/app/database.php';
$db = Database::connect();

// ID do PERÍODO recebido via GET: /abrir_periodo.php?id=123
$periodoId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($periodoId <= 0) {
    die('Período inválido.');
}

// Busca o período
$stmt = $db->prepare('SELECT * FROM periodos WHERE id = ?');
$stmt->execute([$periodoId]);
$periodo = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$periodo) {
    die('Período não encontrado.');
}

// Busca a operação dona desse período
$stmt = $db->prepare('SELECT * FROM operacoes WHERE id = ?');
$stmt->execute([$periodo['operacao_id']]);
$op = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$op) {
    die('Operação não encontrada.');
}

// Guarda operação e período na sessão
$_SESSION['operacao'] = $op;
$_SESSION['periodo'] = [
    'id'     => $periodo['id'],      // <<< daqui vem o id para a captura
    'inicio' => $periodo['inicio'],
    'fim'    => $periodo['fim'],
];

// Vai para a tela de captura
header('Location: /captura.php');
exit;
