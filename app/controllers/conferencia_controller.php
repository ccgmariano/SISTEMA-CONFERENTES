<?php
session_start();

// Segurança: impedir acesso sem login
if (!isset($_SESSION['user'])) {
    header("Location: /login.php");
    exit;
}

$navio   = $_POST['navio']   ?? null;
$inicio  = $_POST['inicio']  ?? null;
$fim     = $_POST['fim']     ?? null;
$produto = $_POST['produto'] ?? null;
$recinto = $_POST['recinto'] ?? null;

// Validação básica
if (!$navio || !$inicio || !$fim || !$produto || !$recinto) {
    die("Erro: todos os campos são obrigatórios.");
}

// No futuro: aqui chamaremos a coleta automática do Poseidon
// Por enquanto vamos apenas exibir os dados e confirmar o recebimento.

require_once __DIR__ . '/../views/confirmar_conferencia.php';
