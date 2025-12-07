<?php
session_start();

// LOGIN SIMPLES (depois ligamos ao banco)
$usuario = $_POST['usuario'] ?? "";
$senha = $_POST['senha'] ?? "";

if ($usuario == "admin" && $senha == "1234") {
    $_SESSION['logado'] = true;
    header("Location: /dashboard.php");
    exit;
}

echo "Usuário ou senha incorretos.";
