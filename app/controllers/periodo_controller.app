<?php
session_start();

// Se operação não existir, não pode haver período
if (!isset($_SESSION['operacao'])) {
    header("Location: /nova_operacao.php");
    exit;
}

$inicio = $_POST['inicio'] ?? null;
$fim    = $_POST['fim'] ?? null;

if (!$inicio || !$fim) {
    die("Erro: datas inválidas.");
}

$_SESSION['periodo'] = [
    "inicio" => $inicio,
    "fim"    => $fim
];

header("Location: /app/views/resumo_periodo.php");
exit;
