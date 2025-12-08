<?php
session_start();

// Garantir que existe operação ativa
if (!isset($_SESSION['operacao'])) {
    header("Location: /dashboard.php");
    exit;
}

// Validar período recebido
if (!isset($_POST['inicio']) || !isset($_POST['fim'])) {
    die("Erro: período inválido.");
}

$inicio = $_POST['inicio'];
$fim    = $_POST['fim'];

// Salvar período atual
$_SESSION['periodo'] = [
    'inicio' => $inicio,
    'fim'    => $fim
];

// Após salvar o período, ir para captura.php
header("Location: /captura.php");
exit;
?>
