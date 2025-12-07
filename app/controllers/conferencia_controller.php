<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}

$navio   = $_POST['navio']   ?? null;
$inicio  = $_POST['inicio']  ?? null;
$fim     = $_POST['fim']     ?? null;
$produto = $_POST['produto'] ?? null;
$recinto = $_POST['recinto'] ?? null;

if (!$navio || !$inicio || !$fim || !$produto || !$recinto) {
    die("Erro: todos os campos são obrigatórios.");
}

// Envia dados para a view da tabela
require_once __DIR__ . '/../views/conferencia_tabela.php';

