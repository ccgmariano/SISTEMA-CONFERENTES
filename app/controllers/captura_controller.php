<?php
require_once __DIR__ . '/../../config.php';
require_login();

// Recupera operação e período
$operacao = $_SESSION['operacao_atual'] ?? null;
$periodo  = $_SESSION['periodo_atual'] ?? null;

if (!$operacao || !$periodo) {
    die("<div class='alert alert-danger'>Erro: operação ou período inválidos.</div>");
}

// --------------------------
// SIMULAÇÃO DE PESAGENS
// Aqui, no futuro, faremos a consulta automática ao Poseidon
// --------------------------

$simulado = [
    ["hora" => "08:12:44", "peso" => 22340],
    ["hora" => "08:19:02", "peso" => 22110],
    ["hora" => "08:26:33", "peso" => 22490],
    ["hora" => "08:34:10", "peso" => 22540],
];

$_SESSION['pesagens'] = $simulado;

require_once __DIR__ . '/../views/lista_pesagens.php';
