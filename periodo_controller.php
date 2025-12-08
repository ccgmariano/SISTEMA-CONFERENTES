<?php
require_once __DIR__ . '/config.php';
require_login();

$inicio = $_POST['inicio'] ?? null;
$fim    = $_POST['fim'] ?? null;

if (!$inicio || !$fim) {
    die("Erro: preencha todos os campos.");
}

$novo_periodo = [
    'inicio' => $inicio,
    'fim'    => $fim,
];

// Se ainda não existir array de períodos
if (!isset($_SESSION['periodos'])) {
    $_SESSION['periodos'] = [];
}

// Adiciona novo período
$_SESSION['periodos'][] = $novo_periodo;

// Volta para dashboard
header("Location: /dashboard.php");
exit;
