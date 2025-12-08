<?php
session_start();

if (!isset($_SESSION['operacao'])) {
    header("Location: /dashboard.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Erro: método inválido.");
}

$inicio = $_POST['inicio'] ?? null;
$fim    = $_POST['fim'] ?? null;

if (!$inicio || !$fim) {
    die("Erro: período inválido.");
}

$_SESSION['periodo'] = [
    'inicio' => $inicio,
    'fim'    => $fim
];

header("Location: /dashboard.php");
exit;
