<?php
require_once __DIR__ . '/../../config.php';

// Usuário "fake" por enquanto — depois trocamos para consulta no Poseidon ou banco
$valid_user = 'conferente';
$valid_pass = '1234';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /login.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$senha   = trim($_POST['senha'] ?? '');

if ($usuario === $valid_user && $senha === $valid_pass) {
    $_SESSION['user'] = $usuario;
    header('Location: /dashboard.php');
    exit;
}

// Se chegou aqui é porque errou usuário/senha
$_SESSION['login_error'] = 'Usuário ou senha inválidos.';
header('Location: /login.php');
exit;
