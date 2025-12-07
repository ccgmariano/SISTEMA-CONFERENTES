<?php
require_once __DIR__ . '/../../config.php';

// LOGIN SIMPLES – depois conectaremos o Poseidon
$usuariosValidos = [
    'admin' => '1234',
    'cristiano' => '1234'
];

$user = $_POST['usuario'] ?? '';
$pass = $_POST['senha'] ?? '';

if (isset($usuariosValidos[$user]) && $usuariosValidos[$user] === $pass) {
    
    $_SESSION['user'] = $user;

    header("Location: /dashboard.php");
    exit;

} else {
    header("Location: /login.php?erro=1");
    exit;
}
