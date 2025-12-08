<?php
session_start();
require_once $_SERVER['DOCUMENT_ROOT'] . '/config.php';
require_login();

$inicio = $_POST['inicio'] ?? null;
$fim    = $_POST['fim']    ?? null;

if (!$inicio || !$fim) {
    die("Período inválido.");
}

if (!isset($_SESSION['periodos'])) {
    $_SESSION['periodos'] = [];
}

$_SESSION['periodos'][] = [
    'inicio' => $inicio,
    'fim' => $fim,
    'created_at' => date("Y-m-d H:i:s")
];

header("Location: /dashboard.php");
exit;
